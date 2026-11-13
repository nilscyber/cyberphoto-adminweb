# Requests log

Every instruction from Nils, as written, and what was done. Append only.

## 2026-10-22

**Nils:** "This project too, C:\backupC\dev\adminweb . It's not exposed to the internet
but I guess we should fix it anyway. I can see that the Dockerfile only says FROM
php:8.3-apache so maybe a rebuild would be enough?"

Context: PHP 8.3.36 (2026-10-21) fixes CVE-2026-109618 and CVE-2026-109619
("PHP2Shell", unauthenticated RCE, mod_php most exposed). The container on docker-100
ran PHP 8.3.31 from an image built 2026-05-13. admin.cyberphoto.se has no public DNS
record and the FortiGate has no inbound policy to 192.168.1.0/24, so LAN/VPN only.

**Done:** A rebuild is enough, but the Dockerfile was pinned to `php:8.3.36-apache` so
the version is explicit and verifiable (same as visit). Committed with an explicit
pathspec (local WIP in `app/lib/CSalesExport.php` left out), pushed, pulled on
docker-100, `docker compose build --pull` and `docker compose up -d`, then verified
`php -v` in the container and `healthz.php`. Outcome: commit `c7cf009` pushed and pulled
on docker-100 2026-10-22, image rebuilt 08:42 local time, container recreated, now
reports PHP 8.3.36; `healthz.php` 200, `/` 200 inside the container and through the
proxy at admin.cyberphoto.se. Downtime was the container swap, a few seconds. The
untracked `app/public/scan-test.html` on the server was left as it was.

## 2026-11-13

**Nils:** "Please have a look at brief-znuny-undelivered-replies.md and see what you can
do. I haven't actually read the brief. But what I want is a possibility to find
otrs/znuny tickets that needs to be managed manually. I.e. get a heads up "these tickets
needs to be managed, the answer couldn't be sent". Right now it's because office 365 is
having global problems. The report doesn't really fit under any of the other headlines
perfectly, please create a new one called "Kundtjänst" and put it under that one. It can
be between "Försäljning" and "Statistik"."

**Done:** New page `app/public/otrs_undelivered.php` ("Ej levererade svar") with
`app/lib/COtrsUndelivered.php` and `otrs_undelivered.css`, built from
`brief-znuny-undelivered-replies.md`: agent replies that need resending, replies still
in Znuny's mail queue with what happens next, and already resent ones (collapsed).
Read-only against the Znuny database, reloads every minute, start time selectable
(default yesterday 00:00). New menu category "Kundtjänst" between "Försäljning" and
"Statistik" in `menu.php`; browser title added in `CCyberAdmin::displayPageTitle()`.
Checked locally (live data) with start 2026-11-12 12:00 against the reference script
`C:\backupC\dev\znuny\undelivered_replies.py`: the same ticket numbers in all three
sections (7 to resend, 8 in the queue, 5 resent) and the same queue times. Not
committed, not deployed.

**Nils:** "Nice! Just one detail, change "Ej levererade svar" to "Otrs - ärenden som
behöver hanteras""

**Done:** Renamed in the menu, the page heading, the browser title and the CSS comment.
Checked locally with curl. Not committed, not deployed.

**Nils:** "After that please deploy" / "and commit of course."

**Done:** Committed the page, class, CSS, menu and title changes together with the brief
and this log, pushed to origin/main and pulled on docker-100 (see the commit after this
entry for the outcome of the check on the box).
