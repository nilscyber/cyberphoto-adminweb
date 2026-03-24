# AdminWeb

CyberPhoto admin panel — containerized PHP 8.3 / Apache application.

## Project structure

```
adminweb/
├── app/
│   ├── lib/             # PHP classes (outside DocumentRoot)
│   └── public/          # Apache DocumentRoot
├── docker/
│   ├── apache.conf      # Apache vhost config (HTTP + HTTPS)
│   └── cert/            # SSL certificates (gitignored)
├── Dockerfile           # PHP 8.3 + Apache image
├── docker-compose.yml   # Local development setup
├── .env                 # Environment variables (gitignored)
└── .env.example         # Template for .env
```

## Quick start (local development)

1. Copy and fill in environment variables:
   ```bash
   cp .env.example .env
   # Edit .env with actual credentials
   ```

2. Add SSL certificates to `docker/cert/`:
   - `cert.crt` — domain certificate
   - `cert.key` — private key
   - `ca-bundle.crt` — CA chain

3. Build and run:
   ```bash
   docker compose build && docker compose up -d
   ```

4. Access at https://localhost:8443

## Rebuilding after changes

Config changes (Dockerfile, apache.conf) require a rebuild:
```bash
docker compose down && docker compose build && docker compose up -d
```

PHP/code changes in `app/` are reflected immediately (volume mount).

## Environment variables

See `.env.example` for the full list. Summary:

| Variable | Description |
|---|---|
| `DB_HOST` / `DB_USER` / `DB_PASS` / `DB_NAME` | MySQL read connection (cyberphoto) |
| `DB_HOST_MASTER` / `DB_USER_MASTER` / `DB_PASS_MASTER` | MySQL write connection |
| `AD_HOST` / `AD_HOST_MASTER` / `AD_PORT` / `AD_DBNAME` / `AD_USER` / `AD_PASS` | ADempiere PostgreSQL |
| `OTRS_HOST` / `OTRS_USER` / `OTRS_PASS` / `OTRS_DBNAME` | OTRS (separate server) |

## Database connections

All DB connections go through `app/lib/Db.php`:

- `Db::getConnection()` — MySQL read (cyberphoto)
- `Db::getConnection(true)` — MySQL write (cyberphoto)
- `Db::getConnectionDb('dbname')` — MySQL to other databases (cyberadmin, cyberborsen, etc.)
- `Db::getConnectionOTRS()` — OTRS (separate server)
- `Db::getConnectionAD()` — ADempiere PostgreSQL

## Health check

`GET /healthz.php` returns `200 ok` — used by Docker HEALTHCHECK and K8s probes.

## Deployment (K8s)

The same Docker image is used in production. In K8s:
- DB credentials come from Secrets/ConfigMaps (same env var names)
- SSL is typically handled by the ingress controller
- Health/readiness probes: `GET /healthz.php`
