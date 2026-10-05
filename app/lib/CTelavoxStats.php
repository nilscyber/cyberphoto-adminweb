<?php
/**
 * CTelavoxStats
 *
 * Statistikfrågor mot telavox_calls. Vilka outcome-koder som räknas som
 * besvarade/övergivna osv. styrs helt av CTelavoxOutcome.
 *
 * Filter (alla metoder): array('from' => 'YYYY-MM-DD', 'to' => 'YYYY-MM-DD',
 *                              'queue_id' => int (0 = alla), 'agent_id' => int (0 = alla))
 */
class CTelavoxStats
{
    /** Nyckeltal för hela urvalet. */
    public static function summary(array $filter)
    {
        $types = ''; $params = array();
        $sql = "SELECT " . self::aggregates($types, $params) . "
                FROM telavox_calls c
                WHERE " . self::where($filter, $types, $params);
        return self::finish(CTelavoxRepository::selectOne($sql, $types, $params));
    }

    /** Nyckeltal per kö. */
    public static function perQueue(array $filter)
    {
        $types = ''; $params = array();
        $sql = "SELECT q.id AS queue_id, q.name AS queue_name, " . self::aggregates($types, $params) . "
                FROM telavox_calls c
                JOIN telavox_queues q ON q.id = c.queue_id
                WHERE " . self::where($filter, $types, $params) . "
                GROUP BY q.id, q.name
                ORDER BY q.name";
        return array_map(array(__CLASS__, 'finish'), CTelavoxRepository::select($sql, $types, $params));
    }

    /** Nyckeltal per månad (YYYY-MM), för tidsserierna. */
    public static function perMonth(array $filter)
    {
        $types = ''; $params = array();
        $sql = "SELECT DATE_FORMAT(c.call_date, '%Y-%m') AS ym, " . self::aggregates($types, $params) . "
                FROM telavox_calls c
                WHERE " . self::where($filter, $types, $params) . "
                GROUP BY ym
                ORDER BY ym";
        return array_map(array(__CLASS__, 'finish'), CTelavoxRepository::select($sql, $types, $params));
    }

    /**
     * Antal inkommande samtal per år och månad, för årsjämförelsen.
     * Använder filtrets kö, agent och outcome_class men inte datumintervallet.
     *
     * @return array [år => [månad 1-12 => antal]]
     */
    public static function totalsByYearMonth(array $filter)
    {
        $filter['from'] = '1000-01-01';
        $filter['to']   = '9999-12-31';
        $types = ''; $params = array();
        $sql = "SELECT YEAR(c.call_date) AS y, MONTH(c.call_date) AS m, COUNT(*) AS total
                FROM telavox_calls c
                WHERE " . self::where($filter, $types, $params) . "
                GROUP BY y, m
                ORDER BY y, m";
        $out = array();
        foreach (CTelavoxRepository::select($sql, $types, $params) as $row) {
            $out[(int)$row['y']][(int)$row['m']] = (int)$row['total'];
        }
        return $out;
    }

    /**
     * Nyckeltal per agent. Samtal vars anknytning saknar koppling grupperas
     * per anknytning (agent_id NULL). Samtal utan anknytning (t.ex. övergivna)
     * ingår inte.
     */
    public static function perAgent(array $filter)
    {
        $types = ''; $params = array();
        $sql = "SELECT c.agent_id, a.name AS agent_name,
                       IF(c.agent_id IS NULL, c.agent_extension, NULL) AS unknown_extension,
                       " . self::aggregates($types, $params) . "
                FROM telavox_calls c
                LEFT JOIN telavox_agents a ON a.id = c.agent_id
                WHERE c.agent_extension IS NOT NULL
                  AND " . self::where($filter, $types, $params) . "
                GROUP BY c.agent_id, a.name, unknown_extension
                ORDER BY answered DESC, total DESC";
        return array_map(array(__CLASS__, 'finish'), CTelavoxRepository::select($sql, $types, $params));
    }

    /** Antal samtal per outcome-kod - visar även koder som ännu inte klassificerats. */
    public static function perOutcome(array $filter)
    {
        $types = ''; $params = array();
        $sql = "SELECT c.outcome, COUNT(*) AS total
                FROM telavox_calls c
                WHERE " . self::where($filter, $types, $params) . "
                GROUP BY c.outcome
                ORDER BY total DESC";
        return CTelavoxRepository::select($sql, $types, $params);
    }

    /** Första och sista samtalsdatum i databasen, eller null om den är tom. */
    public static function dateRange()
    {
        $row = CTelavoxRepository::selectOne("SELECT MIN(call_date) AS first_date, MAX(call_date) AS last_date FROM telavox_calls");
        return ($row && $row['first_date'] !== null) ? $row : null;
    }

    /** Gemensamma aggregat. Lägger sina parametrar först i $types/$params. */
    private static function aggregates(&$types, array &$params)
    {
        $answered  = self::inList(CTelavoxOutcome::codesIn(CTelavoxOutcome::ANSWERED), $types, $params);
        $abandoned = self::inList(CTelavoxOutcome::codesIn(CTelavoxOutcome::ABANDONED), $types, $params);
        $missed    = self::inList(CTelavoxOutcome::codesIn(CTelavoxOutcome::MISSED), $types, $params);
        $queueTime = self::inList(CTelavoxOutcome::queueTimeCodes(), $types, $params);
        $handled   = self::inList(CTelavoxOutcome::codesIn(CTelavoxOutcome::ANSWERED), $types, $params);

        return "COUNT(*) AS total,
                COALESCE(SUM(c.outcome IN ($answered)), 0)  AS answered,
                COALESCE(SUM(c.outcome IN ($abandoned)), 0) AS abandoned,
                COALESCE(SUM(c.outcome IN ($missed)), 0)    AS missed,
                AVG(CASE WHEN c.outcome IN ($queueTime) THEN c.queue_time_seconds END) AS avg_queue_time,
                AVG(CASE WHEN c.outcome IN ($handled) THEN c.handle_time_seconds END)  AS avg_handle_time";
    }

    /** Platshållare för en IN-lista; koderna binds som parametrar. */
    private static function inList(array $codes, &$types, array &$params)
    {
        if (!$codes) {
            return "''";
        }
        foreach ($codes as $code) {
            $types .= 's';
            $params[] = $code;
        }
        return implode(',', array_fill(0, count($codes), '?'));
    }

    private static function where(array $filter, &$types, array &$params)
    {
        $sql = "c.call_date BETWEEN ? AND ?";
        $types .= 'ss';
        $params[] = $filter['from'];
        $params[] = $filter['to'];

        if (!empty($filter['queue_id'])) {
            $sql .= " AND c.queue_id = ?";
            $types .= 'i';
            $params[] = (int)$filter['queue_id'];
        }
        if (!empty($filter['agent_id'])) {
            $sql .= " AND c.agent_id = ?";
            $types .= 'i';
            $params[] = (int)$filter['agent_id'];
        }
        // Valfri begränsning till en outcome-klass, t.ex. CTelavoxOutcome::ANSWERED
        if (!empty($filter['outcome_class'])) {
            $sql .= " AND c.outcome IN (" . self::inList(CTelavoxOutcome::codesIn($filter['outcome_class']), $types, $params) . ")";
        }
        return $sql;
    }

    /** Typa om siffrorna och räkna ut övriga samtal och svarsfrekvens. */
    private static function finish($row)
    {
        foreach (array('total', 'answered', 'abandoned', 'missed') as $key) {
            $row[$key] = (int)$row[$key];
        }
        $row['other']           = $row['total'] - $row['answered'] - $row['abandoned'] - $row['missed'];
        $row['answer_rate']     = $row['total'] > 0 ? $row['answered'] / $row['total'] : null;
        $row['avg_queue_time']  = $row['avg_queue_time'] === null ? null : (float)$row['avg_queue_time'];
        $row['avg_handle_time'] = $row['avg_handle_time'] === null ? null : (float)$row['avg_handle_time'];
        return $row;
    }
}
