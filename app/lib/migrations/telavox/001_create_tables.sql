-- Telavox samtalsstatistik - schema
-- Körs manuellt mot databasen "cyberadmin" (MariaDB).
-- Tabellerna skapas i beroendeordning; kör hela filen i ett svep.

CREATE TABLE telavox_queues (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name             VARCHAR(100) NOT NULL,
  telavox_queue_id VARCHAR(20)  NULL COMMENT 'Telavox kö-id, t.ex. 2023402 från filnamnet queue_2023402_...',
  active           TINYINT(1)   NOT NULL DEFAULT 1,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_telavox_queues_name (name),
  UNIQUE KEY uq_telavox_queues_tqid (telavox_queue_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

CREATE TABLE telavox_agents (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  active     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_telavox_agents_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- En anknytning tillhör en agent under en period. valid_to NULL = gäller tills vidare.
-- Samma anknytning kan återanvändas av en annan agent i en senare period.
CREATE TABLE telavox_agent_extensions (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  agent_id   INT UNSIGNED NOT NULL,
  extension  VARCHAR(20)  NOT NULL,
  valid_from DATE         NOT NULL DEFAULT '2000-01-01',
  valid_to   DATE         NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_telavox_ext_lookup (extension, valid_from, valid_to),
  KEY idx_telavox_ext_agent (agent_id),
  CONSTRAINT fk_telavox_ext_agent FOREIGN KEY (agent_id) REFERENCES telavox_agents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- Administrativ koppling: vilka agenter som normalt tillhör vilka köer.
-- Samtalens kö kommer alltid från importen, aldrig härifrån.
CREATE TABLE telavox_queue_agents (
  queue_id INT UNSIGNED NOT NULL,
  agent_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (queue_id, agent_id),
  KEY idx_telavox_qa_agent (agent_id),
  CONSTRAINT fk_telavox_qa_queue FOREIGN KEY (queue_id) REFERENCES telavox_queues (id) ON DELETE CASCADE,
  CONSTRAINT fk_telavox_qa_agent FOREIGN KEY (agent_id) REFERENCES telavox_agents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- checksum är SHA-256 av filen. Misslyckade importer loggas med checksum NULL
-- så att samma fil kan försökas igen.
CREATE TABLE telavox_imports (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  queue_id      INT UNSIGNED NOT NULL,
  filename      VARCHAR(255) NOT NULL,
  period_from   DATE         NULL,
  period_to     DATE         NULL,
  imported_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  imported_by   VARCHAR(255) NOT NULL DEFAULT '',
  row_count     INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Datarader i filen (exkl. kommentarer)',
  call_count    INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Importerade samtal',
  skipped_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Samtal som redan fanns (överlappande period)',
  status        VARCHAR(20)  NOT NULL DEFAULT 'completed' COMMENT 'completed | failed',
  checksum      CHAR(64)     NULL,
  error_message TEXT         NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_telavox_imports_checksum (checksum),
  KEY idx_telavox_imports_queue (queue_id, imported_at),
  CONSTRAINT fk_telavox_imports_queue FOREIGN KEY (queue_id) REFERENCES telavox_queues (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- row_hash = SHA-1 av den ursprungliga raden + löpnummer bland identiska rader i filen.
-- Unikt per kö, vilket stoppar dubbletter även när två filer har överlappande perioder.
CREATE TABLE telavox_calls (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  import_id           INT UNSIGNED NOT NULL,
  queue_id            INT UNSIGNED NOT NULL,
  agent_id            INT UNSIGNED NULL,
  agent_extension     VARCHAR(20)  NULL,
  call_date           DATE         NOT NULL,
  call_time           TIME         NOT NULL,
  caller_id           VARCHAR(32)  NOT NULL DEFAULT '',
  outcome             VARCHAR(16)  NOT NULL,
  queue_time_seconds  INT UNSIGNED NOT NULL DEFAULT 0,
  handle_time_seconds INT UNSIGNED NOT NULL DEFAULT 0,
  forwarded_extension VARCHAR(20)  NULL,
  raw_data            VARCHAR(255) NULL,
  row_hash            CHAR(40)     NOT NULL,
  created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_telavox_calls_row (queue_id, row_hash),
  KEY idx_telavox_calls_queue_date (queue_id, call_date),
  KEY idx_telavox_calls_agent_date (agent_id, call_date),
  KEY idx_telavox_calls_date (call_date),
  KEY idx_telavox_calls_outcome (outcome),
  KEY idx_telavox_calls_import (import_id),
  KEY idx_telavox_calls_ext (agent_extension),
  CONSTRAINT fk_telavox_calls_import FOREIGN KEY (import_id) REFERENCES telavox_imports (id) ON DELETE CASCADE,
  CONSTRAINT fk_telavox_calls_queue  FOREIGN KEY (queue_id)  REFERENCES telavox_queues (id),
  CONSTRAINT fk_telavox_calls_agent  FOREIGN KEY (agent_id)  REFERENCES telavox_agents (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
