<?php
/**
 * CTelavoxCsvParser
 *
 * Läser Telavox köexport (CSV) och returnerar validerade samtalsrader.
 * Gör ingen databasåtkomst - se CTelavoxImporter för själva importen.
 *
 * Filformat (ur filhuvudet):
 *   YYYY-MM-DD,HH:mm:ss,callerid,outcome,queuetime(s),handletime(s),agent[,vidarekopplad till]
 * Rader som börjar med % är kommentarer. In-/utloggningsrader för agenter
 * (AGENTLOGIN/AGENTLOGOUT) räknas men importeras inte.
 */
class CTelavoxCsvParser
{
    const MAX_ERRORS = 50;

    /**
     * @param string $content  filens innehåll
     * @param string $filename ursprungligt filnamn (för Telavox kö-id)
     * @return array {
     *   checksum, queue_name, telavox_queue_id, period_from, period_to,
     *   row_count, event_count, calls: array[], errors: string[], fatal: ?string
     * }
     */
    public static function parse($content, $filename = '')
    {
        $result = array(
            'checksum'         => hash('sha256', $content),
            'queue_name'       => null,
            'telavox_queue_id' => null,
            'period_from'      => null,
            'period_to'        => null,
            'row_count'        => 0,
            'event_count'      => 0,
            'calls'            => array(),
            'errors'           => array(),
            'fatal'            => null,
        );

        if (preg_match('/^queue_(\d+)_/i', basename($filename), $m)) {
            $result['telavox_queue_id'] = $m[1];
        }

        if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
            $content = substr($content, 3);
        }
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        }
        if (strpos($content, "\0") !== false || strncmp($content, 'PK', 2) === 0) {
            $result['fatal'] = 'Filen är inte en textfil. Exportera som CSV från Telavox (inte Excel).';
            return $result;
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);

        $header = '';
        foreach ($lines as $line) {
            if (isset($line[0]) && $line[0] === '%') {
                $header .= ' ' . trim(substr($line, 1));
            }
        }
        if (preg_match('/"([^"]+)"\s+gjorda mellan (\d{4}-\d\d-\d\d) \d\d:\d\d:\d\d och (\d{4}-\d\d-\d\d)/u', $header, $m)) {
            $result['queue_name']  = $m[1];
            $result['period_from'] = $m[2];
            $result['period_to']   = $m[3];
        }

        $seen    = array();
        $minDate = null;
        $maxDate = null;
        $errorCount = 0;

        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '%') {
                continue;
            }
            $result['row_count']++;
            $lineNo = $i + 1;

            $f = str_getcsv($line, ',', '"', '');
            $error = null;

            if (count($f) < 7 || count($f) > 8) {
                $error = 'fel antal fält (' . count($f) . ', förväntat 7-8)';
            } else {
                $f = array_map('trim', array_pad($f, 8, ''));
                list($date, $time, $caller, $outcome, $queueTime, $handleTime, $agent, $forwarded) = $f;

                if (!preg_match('/^(\d{4})-(\d\d)-(\d\d)$/', $date, $d) || !checkdate((int)$d[2], (int)$d[3], (int)$d[1])) {
                    $error = 'ogiltigt datum "' . $date . '"';
                } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $time)) {
                    $error = 'ogiltig tid "' . $time . '"';
                } elseif (!preg_match('/^[A-Z]{1,16}$/', $outcome)) {
                    $error = 'ogiltig avslutsstatus "' . $outcome . '"';
                } elseif (CTelavoxOutcome::isAgentEvent($outcome)) {
                    $result['event_count']++;
                    continue;
                } elseif (($queueTime !== '' && !ctype_digit($queueTime)) || ($handleTime !== '' && !ctype_digit($handleTime))) {
                    $error = 'kötid/handläggningstid är inte ett heltal';
                } elseif (strlen($caller) > 32) {
                    $error = 'för långt inringande nummer';
                }
            }

            if ($error !== null) {
                $errorCount++;
                if ($errorCount <= self::MAX_ERRORS) {
                    $result['errors'][] = 'Rad ' . $lineNo . ': ' . $error;
                }
                continue;
            }

            // Löpnummer skiljer äkta identiska rader (samma sekund, samma data) åt
            $seen[$line] = isset($seen[$line]) ? $seen[$line] + 1 : 1;

            $result['calls'][] = array(
                'line'                => $lineNo,
                'call_date'           => $date,
                'call_time'           => $time,
                'caller_id'           => $caller,
                'outcome'             => $outcome,
                'queue_time_seconds'  => (int)$queueTime,
                'handle_time_seconds' => (int)$handleTime,
                'agent_extension'     => self::normalizeExtension($agent),
                'forwarded_extension' => self::normalizeExtension($forwarded),
                'raw_data'            => mb_substr($line, 0, 255),
                'row_hash'            => sha1($line . '#' . $seen[$line]),
            );

            if ($minDate === null || $date < $minDate) { $minDate = $date; }
            if ($maxDate === null || $date > $maxDate) { $maxDate = $date; }
        }

        if ($errorCount > self::MAX_ERRORS) {
            $result['errors'][] = '... och ' . ($errorCount - self::MAX_ERRORS) . ' rader till med fel.';
        }

        if ($result['period_from'] === null) {
            $result['period_from'] = $minDate;
            $result['period_to']   = $maxDate;
        }

        if ($result['row_count'] === 0) {
            $result['fatal'] = 'Filen innehåller inga datarader.';
        } elseif ($errorCount === $result['row_count']) {
            $result['fatal'] = 'Ingen rad kunde tolkas. Filen ser inte ut att vara en Telavox köexport.';
        }

        return $result;
    }

    /**
     * Anknytningar förekommer både som 0902007094 och 0046902007094.
     * Normaliseras till nationellt format. Tomt och "?" (okänd hos Telavox) ger null.
     */
    public static function normalizeExtension($ext)
    {
        $ext = preg_replace('/[^0-9+]/', '', (string)$ext);
        if ($ext === '' || $ext === '+') {
            return null;
        }
        if (strncmp($ext, '0046', 4) === 0) {
            $ext = '0' . substr($ext, 4);
        } elseif (strncmp($ext, '+46', 3) === 0) {
            $ext = '0' . substr($ext, 3);
        }
        $ext = str_replace('+', '', $ext);
        return $ext === '' ? null : substr($ext, 0, 20);
    }
}
