<?php
/**
 * CTelavoxRepository
 *
 * All databasåtkomst för Telavox-statistikens grunddata: köer, agenter,
 * anknytningar och importhistorik (MariaDB, databasen "cyberadmin").
 * Alla frågor är parameteriserade. Fel kastas som mysqli_sql_exception.
 */
class CTelavoxRepository
{
    public static function conn()
    {
        return Db::getConnectionDb('cyberadmin');
    }

    /** Kör en SELECT och returnera alla rader. */
    public static function select($sql, $types = '', array $params = array())
    {
        $stmt = self::conn()->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public static function selectOne($sql, $types = '', array $params = array())
    {
        $rows = self::select($sql, $types, $params);
        return $rows ? $rows[0] : null;
    }

    /** Kör INSERT/UPDATE/DELETE. Returnerar insert-id för INSERT, annars antal påverkade rader. */
    public static function exec($sql, $types = '', array $params = array())
    {
        $conn = self::conn();
        $stmt = $conn->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->insert_id ? $stmt->insert_id : $stmt->affected_rows;
        $stmt->close();
        return $result;
    }

    /** True om felet är ett brott mot unikt index (dubblett). */
    public static function isDuplicateError(\Throwable $e)
    {
        return $e instanceof mysqli_sql_exception && $e->getCode() === 1062;
    }

    // ------------------------------------------------------------------ Köer

    public static function getQueues($onlyActive = false)
    {
        $sql = "SELECT q.*,
                       (SELECT COUNT(*) FROM telavox_calls c WHERE c.queue_id = q.id) AS call_count,
                       (SELECT COUNT(*) FROM telavox_imports i WHERE i.queue_id = q.id) AS import_count
                FROM telavox_queues q "
             . ($onlyActive ? "WHERE q.active = 1 " : "")
             . "ORDER BY q.name";
        return self::select($sql);
    }

    public static function getQueue($id)
    {
        return self::selectOne("SELECT * FROM telavox_queues WHERE id = ?", 'i', array($id));
    }

    public static function addQueue($name, $telavoxQueueId)
    {
        return self::exec(
            "INSERT INTO telavox_queues (name, telavox_queue_id) VALUES (?, ?)",
            'ss', array($name, $telavoxQueueId)
        );
    }

    public static function updateQueue($id, $name, $telavoxQueueId, $active)
    {
        self::exec(
            "UPDATE telavox_queues SET name = ?, telavox_queue_id = ?, active = ? WHERE id = ?",
            'ssii', array($name, $telavoxQueueId, $active ? 1 : 0, $id)
        );
    }

    /** Tar bara bort köer utan importer. Returnerar false om kön har data. */
    public static function deleteQueue($id)
    {
        $row = self::selectOne("SELECT COUNT(*) AS n FROM telavox_imports WHERE queue_id = ?", 'i', array($id));
        if ((int)$row['n'] > 0) {
            return false;
        }
        self::exec("DELETE FROM telavox_queues WHERE id = ?", 'i', array($id));
        return true;
    }

    // --------------------------------------------------------------- Agenter

    public static function getAgents($onlyActive = false)
    {
        $sql = "SELECT a.*,
                       (SELECT COUNT(*) FROM telavox_calls c WHERE c.agent_id = a.id) AS call_count
                FROM telavox_agents a "
             . ($onlyActive ? "WHERE a.active = 1 " : "")
             . "ORDER BY a.name";
        return self::select($sql);
    }

    public static function getAgent($id)
    {
        return self::selectOne("SELECT * FROM telavox_agents WHERE id = ?", 'i', array($id));
    }

    public static function addAgent($name)
    {
        return self::exec("INSERT INTO telavox_agents (name) VALUES (?)", 's', array($name));
    }

    public static function updateAgent($id, $name, $active)
    {
        self::exec(
            "UPDATE telavox_agents SET name = ?, active = ? WHERE id = ?",
            'sii', array($name, $active ? 1 : 0, $id)
        );
    }

    /** Tar bara bort agenter utan samtal. Returnerar false om agenten har samtal. */
    public static function deleteAgent($id)
    {
        $row = self::selectOne("SELECT COUNT(*) AS n FROM telavox_calls WHERE agent_id = ?", 'i', array($id));
        if ((int)$row['n'] > 0) {
            return false;
        }
        self::exec("DELETE FROM telavox_agents WHERE id = ?", 'i', array($id));
        return true;
    }

    /** queue_id-lista för en agent (administrativ kötillhörighet). */
    public static function getAgentQueueIds($agentId)
    {
        $rows = self::select("SELECT queue_id FROM telavox_queue_agents WHERE agent_id = ?", 'i', array($agentId));
        return array_map('intval', array_column($rows, 'queue_id'));
    }

    public static function setAgentQueues($agentId, array $queueIds)
    {
        $conn = self::conn();
        $conn->begin_transaction();
        try {
            self::exec("DELETE FROM telavox_queue_agents WHERE agent_id = ?", 'i', array($agentId));
            foreach (array_unique(array_map('intval', $queueIds)) as $queueId) {
                self::exec(
                    "INSERT INTO telavox_queue_agents (queue_id, agent_id) VALUES (?, ?)",
                    'ii', array($queueId, $agentId)
                );
            }
            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    /** agent_id => "Kö A, Kö B" för agentlistan. */
    public static function getQueueNamesByAgent()
    {
        $rows = self::select(
            "SELECT qa.agent_id, GROUP_CONCAT(q.name ORDER BY q.name SEPARATOR ', ') AS queues
             FROM telavox_queue_agents qa
             JOIN telavox_queues q ON q.id = qa.queue_id
             GROUP BY qa.agent_id"
        );
        return array_column($rows, 'queues', 'agent_id');
    }

    // ----------------------------------------------------------- Anknytningar

    public static function getExtensions($agentId = null)
    {
        $sql = "SELECT e.*, a.name AS agent_name
                FROM telavox_agent_extensions e
                JOIN telavox_agents a ON a.id = e.agent_id ";
        if ($agentId !== null) {
            return self::select($sql . "WHERE e.agent_id = ? ORDER BY e.extension, e.valid_from", 'i', array($agentId));
        }
        return self::select($sql . "ORDER BY e.extension, e.valid_from");
    }

    public static function getExtension($id)
    {
        return self::selectOne("SELECT * FROM telavox_agent_extensions WHERE id = ?", 'i', array($id));
    }

    /**
     * True om perioden krockar med en befintlig koppling för samma anknytning.
     * En anknytning får bara höra till en agent åt gången.
     */
    public static function extensionOverlaps($extension, $validFrom, $validTo, $excludeId = 0)
    {
        $row = self::selectOne(
            "SELECT COUNT(*) AS n
             FROM telavox_agent_extensions
             WHERE extension = ?
               AND id <> ?
               AND valid_from <= COALESCE(?, '9999-12-31')
               AND COALESCE(valid_to, '9999-12-31') >= ?",
            'siss', array($extension, $excludeId, $validTo, $validFrom)
        );
        return (int)$row['n'] > 0;
    }

    public static function addExtension($agentId, $extension, $validFrom, $validTo)
    {
        $id = self::exec(
            "INSERT INTO telavox_agent_extensions (agent_id, extension, valid_from, valid_to) VALUES (?, ?, ?, ?)",
            'isss', array($agentId, $extension, $validFrom, $validTo)
        );
        self::relinkCalls($extension);
        return $id;
    }

    public static function updateExtension($id, $validFrom, $validTo)
    {
        $ext = self::getExtension($id);
        if (!$ext) {
            return;
        }
        self::exec(
            "UPDATE telavox_agent_extensions SET valid_from = ?, valid_to = ? WHERE id = ?",
            'ssi', array($validFrom, $validTo, $id)
        );
        self::relinkCalls($ext['extension']);
    }

    public static function deleteExtension($id)
    {
        $ext = self::getExtension($id);
        if (!$ext) {
            return;
        }
        self::exec("DELETE FROM telavox_agent_extensions WHERE id = ?", 'i', array($id));
        self::relinkCalls($ext['extension']);
    }

    /**
     * Koppla om redan importerade samtal för en anknytning utifrån gällande
     * perioder. Samtal utanför alla perioder får agent_id NULL.
     */
    public static function relinkCalls($extension)
    {
        self::exec(
            "UPDATE telavox_calls c
             LEFT JOIN telavox_agent_extensions e
                    ON e.extension = c.agent_extension
                   AND c.call_date >= e.valid_from
                   AND (e.valid_to IS NULL OR c.call_date <= e.valid_to)
             SET c.agent_id = e.agent_id
             WHERE c.agent_extension = ?",
            's', array($extension)
        );
    }

    /**
     * Förslag på från-datum när en återanvänd anknytning kopplas till en ny agent:
     * datum för första samtalet utan agent. Null om anknytningen aldrig varit
     * kopplad (då gäller kopplingen "från början").
     */
    public static function suggestValidFrom($extension)
    {
        $row = self::selectOne(
            "SELECT (SELECT COUNT(*) FROM telavox_agent_extensions WHERE extension = ?) AS mappings,
                    (SELECT MIN(call_date) FROM telavox_calls WHERE agent_extension = ? AND agent_id IS NULL) AS first_unknown",
            'ss', array($extension, $extension)
        );
        return ((int)$row['mappings'] > 0) ? $row['first_unknown'] : null;
    }

    /** Anknytningar som förekommer i samtal men saknar agent: [extension, call_count, first_date, last_date]. */
    public static function getUnknownExtensions()
    {
        return self::select(
            "SELECT agent_extension AS extension, COUNT(*) AS call_count,
                    MIN(call_date) AS first_date, MAX(call_date) AS last_date
             FROM telavox_calls
             WHERE agent_id IS NULL AND agent_extension IS NOT NULL
             GROUP BY agent_extension
             ORDER BY call_count DESC"
        );
    }

    // --------------------------------------------------------------- Importer

    public static function getImports($limit = 200)
    {
        return self::select(
            "SELECT i.*, q.name AS queue_name
             FROM telavox_imports i
             JOIN telavox_queues q ON q.id = i.queue_id
             ORDER BY i.imported_at DESC, i.id DESC
             LIMIT ?",
            'i', array($limit)
        );
    }

    public static function getImportByChecksum($checksum)
    {
        return self::selectOne(
            "SELECT i.*, q.name AS queue_name
             FROM telavox_imports i
             JOIN telavox_queues q ON q.id = i.queue_id
             WHERE i.checksum = ?",
            's', array($checksum)
        );
    }

    /** Tar bort en import och (via ON DELETE CASCADE) alla dess samtal. */
    public static function deleteImport($id)
    {
        return self::exec("DELETE FROM telavox_imports WHERE id = ?", 'i', array($id));
    }
}
