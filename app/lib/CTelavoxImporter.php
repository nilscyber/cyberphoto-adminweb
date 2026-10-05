<?php
/**
 * CTelavoxImporter
 *
 * Importlogik för Telavox köexport. Tar emot en redan tolkad fil från
 * CTelavoxCsvParser och den kö användaren valt. Kön kommer alltid från
 * importen - aldrig från agenten - eftersom samma agent kan arbeta i flera köer.
 *
 *   analyze() - förhandsgranskning, skriver inget
 *   import()  - definitiv import i en transaktion
 */
class CTelavoxImporter
{
    /** valid_from för en anknytning som kopplas för första gången */
    const DEFAULT_VALID_FROM = '2000-01-01';

    /**
     * Förhandsgranska en tolkad fil mot databasen.
     *
     * @return array {
     *   duplicate: ?array (tidigare import av samma fil),
     *   new_count, existing_count,
     *   extensions: [ext => [calls, unknown_calls, agent_name]],
     *   unknown_extensions: string[],
     *   outcomes: [code => antal]
     * }
     */
    public static function analyze($queueId, array $parsed)
    {
        $existing = self::existingHashes($queueId, $parsed);
        $resolver = self::loadResolver($parsed['calls']);

        $extensions = array();
        $outcomes   = array();
        $newCount   = 0;

        foreach ($parsed['calls'] as $call) {
            if (isset($existing[$call['row_hash']])) {
                continue;
            }
            $newCount++;
            $outcomes[$call['outcome']] = isset($outcomes[$call['outcome']]) ? $outcomes[$call['outcome']] + 1 : 1;

            $ext = $call['agent_extension'];
            if ($ext === null) {
                continue;
            }
            if (!isset($extensions[$ext])) {
                $extensions[$ext] = array('calls' => 0, 'unknown_calls' => 0, 'agent_name' => null);
            }
            $extensions[$ext]['calls']++;
            $match = self::resolve($resolver, $ext, $call['call_date']);
            if ($match === null) {
                $extensions[$ext]['unknown_calls']++;
            } else {
                $extensions[$ext]['agent_name'] = $match['agent_name'];
            }
        }
        ksort($extensions);
        arsort($outcomes);

        $unknown = array();
        foreach ($extensions as $ext => $info) {
            if ($info['unknown_calls'] > 0) {
                $unknown[] = (string)$ext;
            }
        }

        return array(
            'duplicate'          => CTelavoxRepository::getImportByChecksum($parsed['checksum']),
            'new_count'          => $newCount,
            'existing_count'     => count($parsed['calls']) - $newCount,
            'extensions'         => $extensions,
            'unknown_extensions' => $unknown,
            'outcomes'           => $outcomes,
        );
    }

    /**
     * Definitiv import. Allt sker i en transaktion och rullas tillbaka vid fel.
     *
     * @param array $mappings  [anknytning => ['agent_id' => int] | ['new_name' => string]]
     *                         för okända anknytningar. Anknytningar som saknas här
     *                         importeras med agent_id NULL.
     * @return array {ok: bool, message: string, import_id: ?int, call_count, skipped_count}
     */
    public static function import($queueId, array $parsed, $filename, array $mappings, $importedBy)
    {
        $conn = CTelavoxRepository::conn();
        $filename = mb_substr(basename((string)$filename), 0, 255);

        try {
            $queue = CTelavoxRepository::getQueue($queueId);
            if (!$queue) {
                throw new RuntimeException('Vald kö finns inte.');
            }
            if ($parsed['fatal'] !== null || !empty($parsed['errors'])) {
                throw new RuntimeException('Filen innehåller fel och kan inte importeras.');
            }
            $dup = CTelavoxRepository::getImportByChecksum($parsed['checksum']);
            if ($dup) {
                throw new RuntimeException(self::duplicateMessage($dup));
            }

            $conn->begin_transaction();

            $importId = CTelavoxRepository::exec(
                "INSERT INTO telavox_imports (queue_id, filename, period_from, period_to, imported_by, row_count, status, checksum)
                 VALUES (?, ?, ?, ?, ?, ?, 'completed', ?)",
                'issssis',
                array($queueId, $filename, $parsed['period_from'], $parsed['period_to'], $importedBy, $parsed['row_count'], $parsed['checksum'])
            );

            $existing = self::existingHashes($queueId, $parsed);
            $mapped   = self::applyMappings($mappings, $parsed['calls'], $existing);
            $resolver = self::loadResolver($parsed['calls']);

            $stmt = $conn->prepare(
                "INSERT INTO telavox_calls
                    (import_id, queue_id, agent_id, agent_extension, call_date, call_time, caller_id, outcome,
                     queue_time_seconds, handle_time_seconds, forwarded_extension, raw_data, row_hash)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $callCount = 0;
            $skipped   = 0;
            foreach ($parsed['calls'] as $call) {
                if (isset($existing[$call['row_hash']])) {
                    $skipped++;
                    continue;
                }
                $match   = self::resolve($resolver, $call['agent_extension'], $call['call_date']);
                $agentId = $match === null ? null : (int)$match['agent_id'];
                $stmt->bind_param(
                    'iiisssssiisss',
                    $importId, $queueId, $agentId, $call['agent_extension'], $call['call_date'], $call['call_time'],
                    $call['caller_id'], $call['outcome'], $call['queue_time_seconds'], $call['handle_time_seconds'],
                    $call['forwarded_extension'], $call['raw_data'], $call['row_hash']
                );
                $stmt->execute();
                $callCount++;
            }
            $stmt->close();

            if ($callCount === 0) {
                throw new RuntimeException('Alla samtal i filen finns redan importerade för kön. Inget importerades.');
            }

            CTelavoxRepository::exec(
                "UPDATE telavox_imports SET call_count = ?, skipped_count = ? WHERE id = ?",
                'iii', array($callCount, $skipped, $importId)
            );

            // Spara Telavox kö-id från filnamnet första gången, så att kön kan förväljas nästa gång
            if ($parsed['telavox_queue_id'] !== null && (string)$queue['telavox_queue_id'] === '') {
                $taken = CTelavoxRepository::selectOne(
                    "SELECT id FROM telavox_queues WHERE telavox_queue_id = ?", 's', array($parsed['telavox_queue_id'])
                );
                if (!$taken) {
                    CTelavoxRepository::exec(
                        "UPDATE telavox_queues SET telavox_queue_id = ? WHERE id = ?",
                        'si', array($parsed['telavox_queue_id'], $queueId)
                    );
                }
            }

            // Nya kopplingar gäller även samtal med samma anknytning från tidigare importer
            foreach ($mapped as $ext) {
                CTelavoxRepository::relinkCalls($ext);
            }

            $conn->commit();

            return array(
                'ok'            => true,
                'message'       => $callCount . ' samtal importerade till "' . $queue['name'] . '"'
                                 . ($skipped > 0 ? ' (' . $skipped . ' fanns redan och hoppades över)' : '') . '.',
                'import_id'     => $importId,
                'call_count'    => $callCount,
                'skipped_count' => $skipped,
            );
        } catch (\Throwable $e) {
            try { $conn->rollback(); } catch (\Throwable $ignored) {}

            $message = ($e instanceof RuntimeException)
                ? $e->getMessage()
                : 'Importen avbröts av ett databasfel och har rullats tillbaka.';
            error_log('Telavox import failed (' . $filename . ', queue ' . (int)$queueId . '): ' . $e->getMessage());
            self::logFailure($queueId, $filename, $parsed, $importedBy, $e->getMessage());

            return array('ok' => false, 'message' => $message, 'import_id' => null, 'call_count' => 0, 'skipped_count' => 0);
        }
    }

    public static function duplicateMessage(array $import)
    {
        return 'Filen är redan importerad (' . $import['filename'] . ', kö "' . $import['queue_name'] . '", '
             . substr($import['imported_at'], 0, 16) . '). Inget importerades.';
    }

    /** row_hash => true för samtal som redan finns för kön inom filens datumintervall. */
    private static function existingHashes($queueId, array $parsed)
    {
        if (empty($parsed['calls'])) {
            return array();
        }
        $dates = array_column($parsed['calls'], 'call_date');
        $rows = CTelavoxRepository::select(
            "SELECT row_hash FROM telavox_calls WHERE queue_id = ? AND call_date BETWEEN ? AND ?",
            'iss', array($queueId, min($dates), max($dates))
        );
        return array_fill_keys(array_column($rows, 'row_hash'), true);
    }

    /** Hämta alla kopplingar för de anknytningar som förekommer i filen: [ext => rader]. */
    private static function loadResolver(array $calls)
    {
        $exts = array();
        foreach ($calls as $call) {
            if ($call['agent_extension'] !== null) {
                $exts[$call['agent_extension']] = true;
            }
        }
        $exts = array_map('strval', array_keys($exts));
        if (!$exts) {
            return array();
        }
        $rows = CTelavoxRepository::select(
            "SELECT e.extension, e.agent_id, e.valid_from, e.valid_to, a.name AS agent_name
             FROM telavox_agent_extensions e
             JOIN telavox_agents a ON a.id = e.agent_id
             WHERE e.extension IN (" . implode(',', array_fill(0, count($exts), '?')) . ")",
            str_repeat('s', count($exts)), $exts
        );
        $resolver = array();
        foreach ($rows as $row) {
            $resolver[$row['extension']][] = $row;
        }
        return $resolver;
    }

    /** Agenten som hade anknytningen det aktuella datumet, eller null. */
    private static function resolve(array $resolver, $extension, $date)
    {
        if ($extension === null || !isset($resolver[$extension])) {
            return null;
        }
        foreach ($resolver[$extension] as $row) {
            if ($date >= $row['valid_from'] && ($row['valid_to'] === null || $date <= $row['valid_to'])) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Spara användarens kopplingar av okända anknytningar (befintlig eller ny
     * agent). Returnerar de anknytningar som kopplats.
     */
    private static function applyMappings(array $mappings, array $calls, array $existing)
    {
        $mapped = array();
        if (!$mappings) {
            return $mapped;
        }
        $resolver = self::loadResolver($calls);

        // Tidigaste samtalsdatum utan koppling, per anknytning
        $firstUnknown = array();
        foreach ($calls as $call) {
            $ext = $call['agent_extension'];
            if ($ext === null || isset($existing[$call['row_hash']]) || self::resolve($resolver, $ext, $call['call_date']) !== null) {
                continue;
            }
            if (!isset($firstUnknown[$ext]) || $call['call_date'] < $firstUnknown[$ext]) {
                $firstUnknown[$ext] = $call['call_date'];
            }
        }

        foreach ($mappings as $ext => $map) {
            $ext = (string)$ext;
            if (!isset($firstUnknown[$ext])) {
                continue;
            }
            if (!empty($map['new_name'])) {
                $name = trim((string)$map['new_name']);
                $agent = CTelavoxRepository::selectOne("SELECT id FROM telavox_agents WHERE name = ?", 's', array($name));
                $agentId = $agent ? (int)$agent['id'] : (int)CTelavoxRepository::addAgent($name);
            } elseif (!empty($map['agent_id'])) {
                $agentId = (int)$map['agent_id'];
                if (!CTelavoxRepository::getAgent($agentId)) {
                    throw new RuntimeException('Vald användare för anknytning ' . $ext . ' finns inte.');
                }
            } else {
                continue;
            }

            // Har anknytningen redan använts av någon under en annan period börjar
            // den nya kopplingen vid filens första okända samtal, annars "från början".
            $validFrom = isset($resolver[$ext]) ? $firstUnknown[$ext] : self::DEFAULT_VALID_FROM;
            if (CTelavoxRepository::extensionOverlaps($ext, $validFrom, null)) {
                throw new RuntimeException(
                    'Anknytning ' . $ext . ' är redan kopplad till en annan användare under en överlappande period. '
                    . 'Justera perioderna under Användare och importera sedan igen.'
                );
            }
            CTelavoxRepository::exec(
                "INSERT INTO telavox_agent_extensions (agent_id, extension, valid_from, valid_to) VALUES (?, ?, ?, NULL)",
                'iss', array($agentId, $ext, $validFrom)
            );
            $mapped[] = $ext;
        }
        return $mapped;
    }

    /** Logga misslyckad import i historiken (checksum NULL så att filen kan försökas igen). */
    private static function logFailure($queueId, $filename, array $parsed, $importedBy, $error)
    {
        try {
            if (!CTelavoxRepository::getQueue($queueId)) {
                return;
            }
            CTelavoxRepository::exec(
                "INSERT INTO telavox_imports (queue_id, filename, period_from, period_to, imported_by, row_count, status, checksum, error_message)
                 VALUES (?, ?, ?, ?, ?, ?, 'failed', NULL, ?)",
                'issssis',
                array($queueId, $filename, $parsed['period_from'], $parsed['period_to'], $importedBy, $parsed['row_count'], mb_substr($error, 0, 2000))
            );
        } catch (\Throwable $e) {
            error_log('Telavox import: kunde inte logga misslyckad import: ' . $e->getMessage());
        }
    }
}
