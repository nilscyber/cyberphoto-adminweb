<?php
/**
 * CCronMailRecipients
 *
 * Central hantering av mottagare (To/Cc/Bcc) för cronjobbens mailutskick.
 * Mottagarna hämtas från tabellen cron_mail_recipients (MariaDB, databasen
 * "cyberadmin"), så att de kan
 * ändras via admin-sidan cron_mail_settings.php utan att röra kod/deploy.
 *
 * Om tabellen saknar en rad för ett jobb (eller inte går att nå) faller
 * klassen tillbaka på de hårdkodade standardvärden som stod i respektive
 * cronjob-fil innan flytten till databasen - så ett utskick aldrig blir tomt.
 *
 * Tabell (skapas manuellt):
 *   CREATE TABLE cron_mail_recipients (
 *     job_key    VARCHAR(64) PRIMARY KEY,
 *     job_label  VARCHAR(255),
 *     to_emails  TEXT,
 *     cc_emails  TEXT,
 *     bcc_emails TEXT,
 *     updated_at DATETIME,
 *     updated_by VARCHAR(255)
 *   );
 */
class CCronMailRecipients
{
    /**
     * Kända cronjobb: job_key => [label, file, default to/cc/bcc]
     * "file" är bara för visning på admin-sidan.
     */
    private static $jobs = array(
        'new_products' => array(
            'label' => 'Nya produkter att beställa (lansering)',
            'file'  => 'cron_mail_new_products.php',
            'to'    => array('grupp_inkop@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array('stefan@cyberphoto.se'),
        ),
        'standard_locator' => array(
            'label' => 'Begagnade produkter - lagerplats Standard',
            'file'  => 'cron_mail_standard_locator.php',
            'to'    => array('stefan@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array('borje@cyberphoto.se', 'albin@cyberphoto.se', 'albin.soderlind@cyberphoto.se'),
        ),
        'used_products' => array(
            'label' => 'Begagnade produkter äldre än 90 dagar',
            'file'  => 'cron_mail_used_products.php',
            'to'    => array('stefan@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array('borje@cyberphoto.se', 'albin@cyberphoto.se', 'albin.soderlind@cyberphoto.se'),
        ),
        'demo_products' => array(
            'label' => 'Fyndprodukter äldre än 90 dagar',
            'file'  => 'cron_mail_demo_products.php',
            'to'    => array('stefan@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array('karoline.juliusson@cyberphoto.se', 'service@cyberphoto.se'),
        ),
        'missing_supplier_demo' => array(
            'label' => 'Demo-produkter utan aktuell leverantör',
            'file'  => 'cron_mail_missing_supplier_demo.php',
            'to'    => array('service@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array('stefan@cyberphoto.se', 'karoline.juliusson@cyberphoto.se'),
        ),
        'focus_products' => array(
            'label' => 'Fokusprodukter (veckoutskick)',
            'file'  => 'cron_mail_focus_products.php',
            'to'    => array(
                'stefan@cyberphoto.se',
                'emil.lindberg@cyberphoto.se',
                'jonas@cyberphoto.se',
                'victoria@cyberphoto.se',
                'boyd@cyberphoto.se',
                'thomas@cyberphoto.se',
            ),
            'cc'    => array(),
            'bcc'   => array(),
        ),
        'dropshipment_products' => array(
            'label' => 'Topp 10 - Levererade dropshipment-produkter',
            'file'  => 'cron_dropshipment_products.php',
            'to'    => array('stefan@cyberphoto.se', 'emil.lindberg@cyberphoto.se'),
            'cc'    => array(),
            'bcc'   => array(),
        ),
    );

    /**
     * Hämta To/Cc/Bcc för ett jobb, som färdiga kommaseparerade strängar.
     * Läser från databasen, faller tillbaka på hårdkodade standardvärden om
     * raden saknas eller databasen inte kan nås.
     *
     * @return array{to:string,cc:string,bcc:string,label:string}
     */
    public static function get($jobKey)
    {
        $defaults = self::defaultsFor($jobKey);

        $row = self::fetchRow($jobKey);
        if ($row === null) {
            return $defaults;
        }

        // Raden finns (jobbet har sparats minst en gång) - använd dess värden
        // som de är, även om ett fält har tömts avsiktligt. Standardvärdena
        // ska bara användas innan jobbet någonsin sparats (se ovan).
        return array(
            'label' => ($row['job_label'] !== null && $row['job_label'] !== '') ? $row['job_label'] : $defaults['label'],
            'to'    => (string)$row['to_emails'],
            'cc'    => (string)$row['cc_emails'],
            'bcc'   => (string)$row['bcc_emails'],
        );
    }

    /**
     * Lista alla kända jobb, med sparade värden från databasen där de finns
     * (annars standardvärden). Används av admin-sidan.
     */
    public static function getAllJobs()
    {
        $out = array();
        foreach (self::$jobs as $jobKey => $def) {
            $r = self::get($jobKey);
            $out[] = array(
                'job_key' => $jobKey,
                'file'    => $def['file'],
                'label'   => $r['label'],
                'to'      => $r['to'],
                'cc'      => $r['cc'],
                'bcc'     => $r['bcc'],
            );
        }
        return $out;
    }

    /**
     * Spara mottagare för ett jobb (upsert).
     */
    public static function save($jobKey, $to, $cc, $bcc, $updatedBy = '')
    {
        if (!isset(self::$jobs[$jobKey])) {
            return false;
        }

        $label = self::$jobs[$jobKey]['label'];
        $conn  = Db::getConnectionDb('cyberadmin');

        $sql = "INSERT INTO cron_mail_recipients (job_key, job_label, to_emails, cc_emails, bcc_emails, updated_at, updated_by)
                VALUES (?, ?, ?, ?, ?, NOW(), ?)
                ON DUPLICATE KEY UPDATE
                    to_emails  = VALUES(to_emails),
                    cc_emails  = VALUES(cc_emails),
                    bcc_emails = VALUES(bcc_emails),
                    updated_at = NOW(),
                    updated_by = VALUES(updated_by)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('ssssss', $jobKey, $label, $to, $cc, $bcc, $updatedBy);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    private static function defaultsFor($jobKey)
    {
        if (!isset(self::$jobs[$jobKey])) {
            return array('label' => $jobKey, 'to' => '', 'cc' => '', 'bcc' => '');
        }
        $def = self::$jobs[$jobKey];
        return array(
            'label' => $def['label'],
            'to'    => implode(', ', $def['to']),
            'cc'    => implode(', ', $def['cc']),
            'bcc'   => implode(', ', $def['bcc']),
        );
    }

    /** Rå rad från databasen, eller null om saknas/DB otillgänglig. */
    private static function fetchRow($jobKey)
    {
        try {
            $conn = Db::getConnectionDb('cyberadmin');
            $stmt = $conn->prepare("SELECT job_label, to_emails, cc_emails, bcc_emails FROM cron_mail_recipients WHERE job_key = ? LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param('s', $jobKey);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

}
