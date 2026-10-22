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
`php -v` in the container and `healthz.php`. Outcome recorded below.
