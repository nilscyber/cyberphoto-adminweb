<?php

class Db {
    private static ?mysqli $conn_master = null;
    private static ?mysqli $conn_standard = null;

    private static $conn_ad_standard = null;
    private static $conn_ad_master = null;

    private static array $conn_db = [];
    private static ?mysqli $conn_otrs = null;

    /**
     * Get db connection (MySQL/MariaDB)
     * @param bool $master - set to true if replication master is to be used. I.e. for global CRUD-calls
     * @return mysqli
     */
    public static function getConnection(bool $master = false): mysqli {
        if ($master) {
            if (!self::$conn_master || !self::$conn_master->ping()) {
                self::$conn_master = new mysqli(
                    getenv('DB_HOST_MASTER') ?: 'db',
                    getenv('DB_USER_MASTER') ?: 'appuser',
                    getenv('DB_PASS_MASTER') ?: 'apppass',
                    getenv('DB_NAME') ?: 'cyberphoto'
                );
                self::$conn_master->set_charset('utf8mb4');
            }
            return self::$conn_master;
        } else {
            if (!self::$conn_standard || !self::$conn_standard->ping()) {
                self::$conn_standard = new mysqli(
                    getenv('DB_HOST') ?: 'db',
                    getenv('DB_USER') ?: 'appuser',
                    getenv('DB_PASS') ?: 'apppass',
                    getenv('DB_NAME') ?: 'cyberphoto'
                );
                self::$conn_standard->set_charset('utf8mb4');
            }
            return self::$conn_standard;
        }
    }

    /**
     * Get db connection to a specific database (MySQL/MariaDB)
     * Uses the master connection. For: cyberadmin, cyberborsen, cyberorder, cyber2, pacsoft, etc.
     * @param string $dbname - the database name
     * @return mysqli
     */
    public static function getConnectionDb(string $dbname): mysqli {
        $key = $dbname;
        if (!isset(self::$conn_db[$key]) || !self::$conn_db[$key]->ping()) {
            self::$conn_db[$key] = new mysqli(
                getenv('DB_HOST_MASTER') ?: 'db',
                getenv('DB_USER_MASTER') ?: 'appuser',
                getenv('DB_PASS_MASTER') ?: 'apppass',
                $dbname
            );
            self::$conn_db[$key]->set_charset('utf8mb4');
        }
        return self::$conn_db[$key];
    }

    /**
     * Get db connection to OTRS (MySQL/MariaDB, separate server)
     * @return mysqli
     */
    public static function getConnectionOTRS(): mysqli {
        if (!self::$conn_otrs || !self::$conn_otrs->ping()) {
            self::$conn_otrs = new mysqli(
                getenv('OTRS_HOST') ?: 'localhost',
                getenv('OTRS_USER') ?: 'otrs',
                getenv('OTRS_PASS') ?: '',
                getenv('OTRS_DBNAME') ?: 'otrs'
            );
            self::$conn_otrs->set_charset('utf8mb4');
        }
        return self::$conn_otrs;
    }

    /**
     * Get db connection to ADempiere (PostgreSQL)
     * @param bool $master - set to true if replication master is to be used
     * @return resource|PgSql\Connection
     */
    public static function getConnectionAD(bool $master = false) {
        if ($master) {
            if (!self::$conn_ad_master || !pg_ping(self::$conn_ad_master)) {
                $conn_string = sprintf(
                    "host=%s port=%s dbname=%s user=%s password=%s",
                    getenv('AD_HOST_MASTER') ?: 'cyber-erp.cyberphoto.se',
                    getenv('AD_PORT') ?: '5432',
                    getenv('AD_DBNAME') ?: 'adempiere',
                    getenv('AD_USER') ?: 'adempiere',
                    getenv('AD_PASS') ?: ''
                );
                self::$conn_ad_master = @pg_pconnect($conn_string);
            }
            if (self::$conn_ad_master) { pg_set_client_encoding(self::$conn_ad_master, "UTF-8"); }
            return self::$conn_ad_master;
        } else {
            if (!self::$conn_ad_standard || !pg_ping(self::$conn_ad_standard)) {
                $conn_string = sprintf(
                    "host=%s port=%s dbname=%s user=%s password=%s",
                    getenv('AD_HOST') ?: 'localhost',
                    getenv('AD_PORT') ?: '5432',
                    getenv('AD_DBNAME') ?: 'adempiere',
                    getenv('AD_USER') ?: 'adempiere',
                    getenv('AD_PASS') ?: ''
                );
                self::$conn_ad_standard = @pg_pconnect($conn_string);
            }
            if (self::$conn_ad_standard) { pg_set_client_encoding(self::$conn_ad_standard, "UTF-8"); }
            return self::$conn_ad_standard;
        }
    }
}
