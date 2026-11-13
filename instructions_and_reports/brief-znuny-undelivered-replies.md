# Brief: live report of helpdesk replies that were not delivered

From the Znuny session (`C:\backupC\dev\znuny`), 2026-11-13, for the session
that builds this in adminweb. Nils asked for it to be built here.

## What Nils wants

A page in adminweb that shows, live, which replies written in the helpdesk
(Znuny, "OTRS") never reached the customer and still have to be sent again,
and who wrote each one. On 2026-11-13 Microsoft 365 had a long disturbance
and Znuny dropped replies; Nils asked the Znuny session three times for this
list and wants to be able to open a page instead.

Znuny does not keep mail a server refuses. A failed reply is deleted from
its mail queue and only marked as failed on the ticket. An agent then has to
open the ticket and press "Resend" on that article. The page is the to-do
list for that: it changes nothing itself.

## What the page shows

Computed on every load, with a visible "updated at" time and a reload every
minute or so. A start time for the window (default: yesterday 00:00).
Three sections, UI in Swedish:

1. **Needs resending.** Agent replies whose sending failed in the window
   and that have not been resent since. Columns: ticket number (link), queue,
   sent by, written at, recipient address, why it failed.
2. **Waiting in Znuny's mail queue.** Agent replies still being retried,
   with what happens next (see "The mail queue" below).
3. **Already resent.** Same columns plus when the resend went out. Less
   important; collapsed or at the bottom.

Auto replies are out of scope (Nils: "Auto replies is not interesting").

Ticket link: `https://otrs.cyberphoto.se/znuny/index.pl?Action=AgentTicketZoom;TicketID=<ticket_id>;ArticleID=<article_id>`

## Where the data is

Everything is plain `SELECT` against the Znuny database, the one
`app/lib/COtrs.php` already reads through `Db::getConnectionOTRS()`. No API
and nothing new on the Znuny side. The three queries below are the ones the
reference script runs; they take well under a second.

**Reference implementation, tested on live data today:**
`C:\backupC\dev\znuny\undelivered_replies.py` (run it in that folder:
`python undelivered_replies.py --since "2026-11-12 12:00"`). It produced the
lists Nils has seen. Your page should give the same rows for the same start
time; compare against it before you report.

Failed agent replies in the window:

```sql
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
WHERE e.create_time >= :since AND st.name = 'agent'
GROUP BY e.article_id, a.ticket_id, t.tn, q.name, a.create_time, m.a_to,
         u.login, u.first_name, u.last_name
ORDER BY a.create_time
```

Later agent e-mails on those tickets (to find resends):

```sql
SELECT a.id AS article_id, a.ticket_id, a.create_time, m.a_to,
       (SELECT COUNT(*) FROM article_data_mime_send_error e WHERE e.article_id = a.id) AS errors,
       (SELECT COUNT(*) FROM mail_queue mq WHERE mq.article_id = a.id) AS queued
FROM article a
JOIN article_sender_type st ON st.id = a.article_sender_type_id
JOIN article_data_mime m    ON m.article_id = a.id
WHERE a.ticket_id IN (:ticket_ids) AND a.create_time >= :since AND st.name = 'agent'
  AND a.communication_channel_id = (SELECT id FROM communication_channel WHERE name = 'Email')
ORDER BY a.id
```

Agent replies in the mail queue:

```sql
SELECT mq.attempts, mq.create_time AS queued_at, mq.due_time, mq.last_smtp_code,
       a.id AS article_id, a.ticket_id, t.tn, q.name AS queue, m.a_to,
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
```

## The logic that is not in the SQL

**Has a failed reply been resent?** A resend creates a new agent article on
the same ticket. For a failed article F, take the later articles (second
query, same ticket, higher `article_id`) that share at least one recipient
address with F (`a_to` can hold several addresses and display names; pull
the addresses out and compare lowercase). Then:

- one of them has `errors = 0` and `queued = 0`: already resent (section 3);
- otherwise one of them has `queued > 0`: a resend is waiting (show the
  ticket in section 2, not in 1);
- otherwise there are later ones and all failed: F is an older try; show
  only the newest failed try for that ticket and recipient in section 1;
- no later article: needs resending (section 1).

A reply resent to a different address will still show as not resent. That is
accepted.

**Why it failed** comes from `reason_text`. Show a short Swedish label and
keep the raw text as a tooltip. Test in this order:

| `reason_text` | Meaning |
|---|---|
| empty or NULL | Znuny gave up after temporary errors |
| contains `5.7.515` | Hotmail/Outlook refused the mail: DKIM |
| contains `S3150` or `block list` | Hotmail/Outlook refused: our sending server is on their block list |
| contains `Command unknown: 'AUTH'` | Microsoft 365 SMTP login failed |
| matches `5\.1\.1`, `User unknown`, `Address not found`, `address couldn` | the address does not exist; resending to it is pointless, say so |
| SMTP code (the three digits before `, d.d.d`) starts with 4 | gave up after temporary errors |
| anything else | "refused" plus the code |

**Sent by** is the user who created the article: `first_name last_name`.
The login `tradein-api` is the trade-in system sending by itself, not a
person; label it as such (most rows in queue Inbyte are of this kind).

## The mail queue: "what happens next"

Znuny adds 1 to `mail_queue.attempts` at every queue run, about once a
minute and slower when it is busy, also while a mail is only waiting. Above
the limit the mail is deleted and marked failed. A real send attempt happens
only once `due_time` has passed. The limit is the setting `MailQueue`:

```sql
SELECT effective_value FROM sysconfig_modified WHERE name = 'MailQueue' AND user_id IS NULL
```

The value is a small YAML text containing `ItemMaxAttempts: '85'`; read the
number with a regular expression. No row means the shipped default, 3. Nils
changes it now and then, so do not hard-code it.

For each queued mail: earliest give-up time = now + (limit - attempts + 1)
minutes. If `due_time` is NULL the first try is about to happen. If
`due_time` is later than the give-up time, the mail will be dropped without
another try; make that stand out, because that reply will need resending.
Otherwise show the next try and the give-up time. Say on the page that these
are earliest times.

## Things that will bite

- **Clock.** Every timestamp in the Znuny database is Swedish local time,
  but the database session clock is UTC. Compute the window start and "now"
  in PHP (Europe/Stockholm) and pass them in. Never `NOW()` or `CURDATE()`
  in these queries.
- **Read-only.** `SELECT` only against the Znuny database, ever. Znuny keeps
  its own caches and history and does not see writes made behind its back.
- **Big tables.** `article` and `article_data_mime` hold fifteen years of
  mail. Only reach them through primary keys or the ticket ids from the
  first query, as above. `article_data_mime_send_error` is small (about
  1,600 rows in all) and has no index on `create_time`; filtering it is fine.
- **Never `SELECT *` from `mail_queue`** (`raw_message` holds whole mails)
  **or from `users`** (`pw` holds password hashes). Take the named columns.
- **Bounces are in the same table.** `article_data_mime_send_error` also
  records bounce mails matched to a reply ("Address not found"), which is
  why the address rule exists.
- **Grants.** Check first that adminweb's Znuny user may read
  `article_data_mime_send_error`, `mail_queue`, `users` and
  `sysconfig_modified`. If not, tell Nils; do not work around it.

## What to report back

The page name and where it sits in the menu; what you compared against the
reference script and whether the rows matched; anything you could not
verify. Build and check it locally first (localhost shows live data).
Deploying to docker-100 is Nils's call.

Background, if you need it: `C:\backupC\dev\znuny\docs\znuny-knowledge.md`,
section 2 ("Outgoing mail" and what follows), and
`C:\backupC\dev\znuny\docs\switch-outgoing-smtp.md`.
