<?php

/**
 * Agent replies written in Znuny ("OTRS") that never reached the customer.
 *
 * Znuny does not keep mail a server refuses: the reply is deleted from its
 * mail queue and only marked as failed on the ticket, and an agent has to
 * press "Resend" on that article. This class builds the to-do list for that.
 * It only reads (SELECT) from the Znuny database, never writes.
 *
 * Reference implementation: C:\backupC\dev\znuny\undelivered_replies.py.
 *
 * Clock: every timestamp in the Znuny database is Swedish local time while
 * the database session clock is UTC, so "now" and the window start are
 * computed in PHP and passed in. Never NOW() or CURDATE() in these queries.
 */
class COtrsUndelivered
{
	const TICKET_URL = 'https://otrs.cyberphoto.se/znuny/index.pl?Action=AgentTicketZoom;TicketID=%d;ArticleID=%d';

	/** @var string|null set when a query failed */
	public $error = null;

	/** @var mysqli */
	private $db;

	function __construct()
	{
		$this->db = Db::getConnectionOTRS();
	}

	private function rows($sql, array $params = array())
	{
		try {
			$res = $this->db->execute_query($sql, $params);
			if ($res === false) {
				$this->error = $this->db->error;
				return array();
			}
			return $res->fetch_all(MYSQLI_ASSOC);
		} catch (Throwable $e) {
			$this->error = $e->getMessage();
			return array();
		}
	}

	/** Lowercase e-mail addresses found in a To header (may hold several, with display names). */
	public static function addresses($text)
	{
		preg_match_all('/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/u', (string)$text, $m);
		return array_values(array_unique(array_map('strtolower', $m[0])));
	}

	/**
	 * Short Swedish reason from article_data_mime_send_error.log_message.
	 * @return array [label, pointless] – pointless = resending to the same address will not help
	 */
	public static function reason($logMessage)
	{
		$msg = trim(preg_replace('/\s+/', ' ', (string)$logMessage));
		if ($msg === '') return array('Znuny gav upp efter tillfälliga fel', false);
		if (strpos($msg, '5.7.515') !== false) return array('Hotmail/Outlook tog inte emot mejlet (DKIM)', false);
		if (strpos($msg, 'S3150') !== false || strpos($msg, 'block list') !== false) return array('Hotmail/Outlook tog inte emot mejlet (vår server är spärrlistad)', false);
		if (strpos($msg, "Command unknown: 'AUTH'") !== false) return array('Inloggningen mot Microsoft 365 misslyckades', false);
		if (preg_match('/5\.1\.1|User unknown|Address not found|address couldn/', $msg)) return array('Adressen finns inte', true);
		$hit = preg_match('/(\d{3}), (\d\.\d+\.\d+)/', $msg, $m);
		if ($hit && $m[1][0] === '4') return array('Znuny gav upp efter tillfälliga fel', false);
		return array('Mottagarens server nekade: ' . ($hit ? $m[0] : mb_substr($msg, 0, 60)), false);
	}

	/** Who created the article. tradein-api is the trade-in system sending by itself. */
	public static function sender(array $row)
	{
		if ($row['login'] === 'tradein-api') return 'Inbytessystemet (automatiskt)';
		return trim($row['first_name'] . ' ' . $row['last_name']);
	}

	public static function ticketUrl(array $row)
	{
		return sprintf(self::TICKET_URL, $row['ticket_id'], $row['article_id']);
	}

	/** Queue runs before Znuny deletes a mail (setting MailQueue, ItemMaxAttempts; shipped default 3). */
	public function maxAttempts()
	{
		$rows = $this->rows("SELECT effective_value FROM sysconfig_modified WHERE name = 'MailQueue' AND user_id IS NULL");
		if ($rows && preg_match("/ItemMaxAttempts:\s*'?(\d+)/", (string)$rows[0]['effective_value'], $m)) return (int)$m[1];
		return 3;
	}

	/**
	 * Failed agent replies since $since ('Y-m-d H:i:s', local time), classified.
	 * @return array ['todo' => rows, 'resent' => rows, 'waiting' => int, 'older' => int]
	 *   todo rows carry reason/pointless/tries, resent rows carry resent_at.
	 */
	public function failed($since)
	{
		$out = array('todo' => array(), 'resent' => array(), 'waiting' => 0, 'older' => 0);

		$failed = $this->rows(<<<'SQL'
SELECT e.article_id, MAX(e.create_time) AS failed_at, MAX(e.log_message) AS reason_text,
       a.ticket_id, t.tn, q.name AS queue, a.create_time AS written_at, m.a_to,
       u.login, u.first_name, u.last_name
FROM article_data_mime_send_error e
JOIN article a              ON a.id = e.article_id
JOIN article_sender_type st ON st.id = a.article_sender_type_id
JOIN article_data_mime m    ON m.article_id = a.id
JOIN ticket t               ON t.id = a.ticket_id
JOIN queue q                ON q.id = t.queue_id
JOIN users u                ON u.id = a.create_by
WHERE e.create_time >= ? AND st.name = 'agent'
GROUP BY e.article_id, a.ticket_id, t.tn, q.name, a.create_time, m.a_to,
         u.login, u.first_name, u.last_name
ORDER BY a.create_time
SQL, array($since));
		if (!$failed) return $out;

		// Later agent e-mails on the same tickets: a resend is a new article.
		$ids = array_values(array_unique(array_map(function ($f) { return (int)$f['ticket_id']; }, $failed)));
		$later = array();
		$sql = "SELECT a.id AS article_id, a.ticket_id, a.create_time, m.a_to,
		               (SELECT COUNT(*) FROM article_data_mime_send_error e WHERE e.article_id = a.id) AS errors,
		               (SELECT COUNT(*) FROM mail_queue mq WHERE mq.article_id = a.id) AS queued
		        FROM article a
		        JOIN article_sender_type st ON st.id = a.article_sender_type_id
		        JOIN article_data_mime m    ON m.article_id = a.id
		        WHERE a.ticket_id IN (" . implode(',', $ids) . ") AND a.create_time >= ? AND st.name = 'agent'
		          AND a.communication_channel_id = (SELECT id FROM communication_channel WHERE name = 'Email')
		        ORDER BY a.id";
		foreach ($this->rows($sql, array($since)) as $r) {
			$r['addr'] = self::addresses($r['a_to']);
			$later[(int)$r['ticket_id']][] = $r;
		}
		if ($this->error) return $out;

		$older = array(); // earlier failed tries, counted onto the newest try of the same ticket + recipient
		foreach ($failed as $f) {
			$f['addr'] = self::addresses($f['a_to']);
			$delivered = null; $waiting = false; $any = false;
			foreach (isset($later[(int)$f['ticket_id']]) ? $later[(int)$f['ticket_id']] : array() as $x) {
				if ((int)$x['article_id'] <= (int)$f['article_id'] || !array_intersect($x['addr'], $f['addr'])) continue;
				$any = true;
				if ((int)$x['queued'] > 0) $waiting = true;
				elseif ((int)$x['errors'] == 0) $delivered = $x;
			}
			if ($delivered) {
				$f['resent_at'] = $delivered['create_time'];
				$out['resent'][] = $f;
			} elseif ($waiting) {
				$out['waiting']++;
			} elseif ($any) {
				$out['older']++;
				$older[] = $f;
			} else {
				list($f['reason'], $f['pointless']) = self::reason($f['reason_text']);
				$f['tries'] = 1;
				$out['todo'][] = $f;
			}
		}
		foreach ($older as $o) {
			foreach ($out['todo'] as &$t) {
				if ($t['ticket_id'] == $o['ticket_id'] && array_intersect($t['addr'], $o['addr'])) { $t['tries']++; break; }
			}
			unset($t);
		}
		return $out;
	}

	/**
	 * Agent replies still in Znuny's mail queue, with what happens next.
	 * Znuny adds 1 to attempts at every queue run (about once a minute, also
	 * while a mail only waits for due_time) and deletes the mail above $limit,
	 * so the give-up time is the earliest possible one.
	 * @param int $now unix time
	 */
	public function queued($limit, $now)
	{
		$rows = $this->rows(<<<'SQL'
SELECT mq.attempts, mq.create_time AS queued_at, mq.due_time, mq.last_smtp_code,
       a.id AS article_id, a.ticket_id, t.tn, q.name AS queue, a.create_time AS written_at, m.a_to,
       u.login, u.first_name, u.last_name
FROM mail_queue mq
JOIN article a              ON a.id = mq.article_id
JOIN article_sender_type st ON st.id = a.article_sender_type_id
JOIN article_data_mime m    ON m.article_id = a.id
JOIN ticket t               ON t.id = a.ticket_id
JOIN queue q                ON q.id = t.queue_id
JOIN users u                ON u.id = a.create_by
WHERE st.name = 'agent'
ORDER BY mq.id
SQL);
		foreach ($rows as &$r) {
			$r['addr'] = self::addresses($r['a_to']);
			$r['gives_up'] = $now + 60 * max($limit - (int)$r['attempts'] + 1, 0);
			$due = $r['due_time'] ? strtotime($r['due_time']) : null;
			$r['next_try'] = $due;
			// due later than the give-up time: deleted without another try
			$r['will_drop'] = $due !== null && $due > $r['gives_up'];
		}
		unset($r);
		return $rows;
	}
}
