<?php

class COtrs
{
    var $conn_otrs;
    var $db_otrs = 'otrs';
    var $debug = false;

    function __construct()
    {
        $this->conn_otrs = Db::getConnectionOTRS();
        if (!$this->conn_otrs) {
            error_log('OTRS CONNECT ERROR');
            return;
        }
    }

    function esc($s)
    {
        return mysqli_real_escape_string($this->conn_otrs, (string)$s);
    }

    function runQuery($sql)
    {
        if (!$this->conn_otrs) {
            error_log('OTRS SQL ERROR: No connection | SQL: ' . $sql);
            return false;
        }

        // pconnect-safe
        //$this->selectDb();

        if ($this->debug) error_log('OTRS SQL: ' . $sql);

        $res = mysqli_query($this->conn_otrs, $sql);
        if ($res === false) {
            error_log('OTRS SQL ERROR: ' . mysqli_error($this->conn_otrs) . ' | SQL: ' . $sql);
        }
        return $res;
    }

    function fetchAllObjects($res)
    {
        $rows = array();
        if ($res === false) return $rows;
        while ($row = mysqli_fetch_object($res)) $rows[] = $row;
        return $rows;
    }

    // ======= TOPP-KÖER (din punkt A1) =======
    function getTopQueues()
    {
        $sql = "SELECT DISTINCT
                  CASE
                    WHEN LOCATE('::', name) > 0 THEN SUBSTRING_INDEX(name, '::', 1)
                    ELSE name
                  END AS top_name
                FROM queue
                WHERE valid_id = 1
                ORDER BY top_name";
        $res = $this->runQuery($sql);
        if ($res === false) return array();

        $out = array();
        while ($r = mysqli_fetch_assoc($res)) {
            $out[] = $r['top_name'];
        }
        return $out;
    }

    // ======= STATISTIK (filtrera på toppnamn) =======

    function getSentEmails2($date, $queueTopName)
    {
        $date = trim((string)$date);
        $top  = trim((string)$queueTopName);

        $sql = "SELECT
                    COUNT(th.id) AS noOfTickets,
                    u.login AS usr,
                    DATE(th.change_time) AS date
                FROM ticket_history th
                JOIN users u ON th.create_by = u.id
                JOIN queue q ON th.queue_id = q.id
                WHERE th.history_type_id = 8
                  AND th.change_time BETWEEN '".$this->esc($date)." 00:00:00' AND '".$this->esc($date)." 23:59:59'";

        if ($top !== '') {
            $sql .= " AND q.name LIKE '".$this->esc($top)."%'";
        }

        $sql .= " GROUP BY 3,2
                  ORDER BY 3 DESC, 1 DESC";

        $res = $this->runQuery($sql);
        if ($res === false || mysqli_num_rows($res) == 0) return array();
        return $this->fetchAllObjects($res);
    }

    function getSentEmailsPeriod2($datefrom, $dateto, $queueTopName)
    {
        $datefrom = trim((string)$datefrom);
        $dateto   = trim((string)$dateto);
        $top      = trim((string)$queueTopName);

        $sql = "SELECT
                    COUNT(th.id) AS noOfTickets,
                    u.login AS usr,
                    DATE(th.change_time) AS date
                FROM ticket_history th
                JOIN users u ON th.create_by = u.id
                JOIN queue q ON th.queue_id = q.id
                WHERE th.history_type_id = 8
                  AND th.change_time BETWEEN '".$this->esc($datefrom)." 00:00:00' AND '".$this->esc($dateto)." 23:59:59'";

        if ($top !== '') {
            $sql .= " AND q.name LIKE '".$this->esc($top)."%'";
        }

        $sql .= " GROUP BY 2
                  ORDER BY 1 DESC";

        $res = $this->runQuery($sql);
        if ($res === false || mysqli_num_rows($res) == 0) return array();
        return $this->fetchAllObjects($res);
    }

    function getClosedTickets2($date, $queueTopName)
    {
        $date = trim((string)$date);
        $top  = trim((string)$queueTopName);

        $sql = "SELECT
                    COUNT(DISTINCT t.id) AS noOfTickets,
                    u.login AS usr,
                    DATE(th.change_time) AS date
                FROM ticket_history th
                JOIN users u ON th.create_by = u.id
                JOIN ticket t ON t.id = th.ticket_id
                JOIN queue q ON th.queue_id = q.id
                WHERE t.ticket_state_id = 2
                  AND th.history_type_id = 27
                  AND th.state_id = 2
                  AND th.change_time BETWEEN '".$this->esc($date)." 00:00:00' AND '".$this->esc($date)." 23:59:59'";

        if ($top !== '') {
            $sql .= " AND q.name LIKE '".$this->esc($top)."%'";
        }

        $sql .= " GROUP BY 3,2
                  ORDER BY 3 DESC, 1 DESC";

        $res = $this->runQuery($sql);
        if ($res === false || mysqli_num_rows($res) == 0) return array();
        return $this->fetchAllObjects($res);
    }

    function getClosedTicketsPerdiod2($datefrom, $dateto, $queueTopName)
    {
        $datefrom = trim((string)$datefrom);
        $dateto   = trim((string)$dateto);
        $top      = trim((string)$queueTopName);

        $sql = "SELECT
                    COUNT(DISTINCT t.id) AS noOfTickets,
                    u.login AS usr,
                    DATE(th.change_time) AS date
                FROM ticket_history th
                JOIN users u ON th.create_by = u.id
                JOIN ticket t ON t.id = th.ticket_id
                JOIN queue q ON th.queue_id = q.id
                WHERE t.ticket_state_id = 2
                  AND th.history_type_id = 27
                  AND th.state_id = 2
                  AND th.change_time BETWEEN '".$this->esc($datefrom)." 00:00:00' AND '".$this->esc($dateto)." 23:59:59'";

        if ($top !== '') {
            $sql .= " AND q.name LIKE '".$this->esc($top)."%'";
        }

        $sql .= " GROUP BY 2
                  ORDER BY 1 DESC";

        $res = $this->runQuery($sql);
        if ($res === false || mysqli_num_rows($res) == 0) return array();
        return $this->fetchAllObjects($res);
    }
}

?>
