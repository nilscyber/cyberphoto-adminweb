<?php

require_once("Db.php");
require_once("CCheckIpNumber.php");

class CMonitorArticles {

    /** @var resource (mysql link) */
    private $db_r;

    /** @var resource (mysql link) */
    private $db_w;

    /** @var resource (pg link) */
    private $ad_r;

    private $warehouseId = 1000000;

    function __construct() {
        // MariaDB (mysql resource i ert projekt)
        $this->db_r = Db::getConnection(false);
        $this->db_w = Db::getConnection(true);

        // ADempiere / PostgreSQL
        $this->ad_r = Db::getConnectionAD(false);

        if ($this->ad_r) { @pg_set_client_encoding($this->ad_r, "UTF8"); }
    }

    /* =========================
       Helpers
    ========================= */

    private function esc($s) {
        return mysqli_real_escape_string($this->db_r, (string)$s);
    }

    private function adFetchQtyAvailableBatch($artnrs) {
        // Return: [artnr => qtyavailable]
        $out = array();
        if (empty($artnrs)) return $out;

        $uniq = array();
        foreach ($artnrs as $a) {
            $a = trim((string)$a);
            if ($a !== '') $uniq[$a] = true;
        }
        $artnrs = array_keys($uniq);
        if (empty($artnrs)) return $out;

        // Params: $1 = warehouse, $2..$N = artnr
        $params = array();
        $params[] = (int)$this->warehouseId;

        $ph = array();
        $i = 2;
        foreach ($artnrs as $a) {
            $ph[] = '$' . $i;
            $params[] = $a;
            $i++;
        }

        $sql = "
            SELECT
                p.value AS artnr,
                COALESCE(pc.qtyavailable, 0) AS qtyavailable
            FROM m_product p
            LEFT JOIN m_product_cache pc
                   ON pc.m_product_id = p.m_product_id
                  AND pc.m_warehouse_id = $1
            WHERE p.value IN (" . implode(',', $ph) . ")
        ";

        $res = ($this->ad_r) ? @pg_query_params($this->ad_r, $sql, $params) : false;
        if (!$res) return $out;

        while ($res && $row = pg_fetch_assoc($res)) {
            $out[$row['artnr']] = (int)$row['qtyavailable'];
        }

        return $out;
    }

    private function adFetchProductNameBatch($artnrs) {
        // Return: [artnr => name]
        $out = array();
        if (empty($artnrs)) return $out;

        $uniq = array();
        foreach ($artnrs as $a) {
            $a = trim((string)$a);
            if ($a !== '') $uniq[$a] = true;
        }
        $artnrs = array_keys($uniq);
        if (empty($artnrs)) return $out;

        $ph = array();
        $params = array();
        $i = 1;
        foreach ($artnrs as $a) {
            $ph[] = '$' . $i;
            $params[] = $a;
            $i++;
        }

        $sql = "SELECT value AS artnr, name FROM m_product WHERE value IN (" . implode(',', $ph) . ")";
        $res = ($this->ad_r) ? @pg_query_params($this->ad_r, $sql, $params) : false;
        if (!$res) return $out;

        while ($res && $row = pg_fetch_assoc($res)) {
            $out[$row['artnr']] = $row['name'];
        }

        return $out;
    }

    private function adProductExists($artnr) {
        $sql = "SELECT 1 FROM m_product WHERE value = $1 LIMIT 1";
        $res = ($this->ad_r) ? @pg_query_params($this->ad_r, $sql, array($artnr)) : false;
        if (!$res) return false;
        return ($res && pg_num_rows($res) > 0);
    }

	private function adIsOrderAllocated($ordernr, $artnr) {

		$sql = "
			SELECT 1
			FROM c_order o
			JOIN c_orderline ol ON ol.c_order_id = o.c_order_id
			JOIN m_product p ON p.m_product_id = ol.m_product_id
			WHERE o.documentno = $1
			  AND p.value = $2
			  AND COALESCE(ol.qtyallocated, 0) > 0
			LIMIT 1
		";

		$res = ($this->ad_r) ? @pg_query_params($this->ad_r, $sql, array($ordernr, $artnr)) : false;
		if (!$res) return false;

		return ($res && pg_num_rows($res) > 0);
	}

    /* =========================
       UI: Aktiva bevakningar
    ========================= */

    function getActualMonitors($type = null) {
        $rowcolor = true;
        $startcount = 0;

        echo "<table>\n";
        echo "<tr>\n";
        echo "<td width=\"150\"><b>Artikel nr</b></td>\n";
        echo "<td width=\"400\"><b>Benämning</b></td>\n";
        echo "<td width=\"90\" align=\"center\"><b>Typ</b></td>\n";
        echo "<td width=\"110\" align=\"center\"><b>Värde</b></td>\n";
        echo "<td width=\"75\" align=\"center\"><b>Just nu</b></td>\n";
        echo "<td width=\"110\" align=\"center\"><b>Bevakas av</b></td>\n";
        if (!empty($_COOKIE['login_mail']) && $_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
            echo "<td width=\"110\" align=\"center\"><b>Från IP-adress</b></td>\n";
            echo "<td width=\"80\" align=\"center\"><b>Antal dagar</b></td>\n";
            echo "<td width=\"40\" align=\"center\"><b>&nbsp;</b></td>\n";
        }
        echo "<td width=\"65\" align=\"center\"><b>&nbsp;</b></td>\n";
        echo "</tr>\n";

        $sql = "
            SELECT monID, monUser, monArtnr, monMoreLess, monStoreValue, monIP, monTime, monComment, monType, monCount
            FROM cyberphoto.MonitorArticles
            WHERE monActive = 1
            ORDER BY monUser ASC, monArtnr ASC
        ";
        $res = mysqli_query($this->db_r, $sql);

        if ($res && mysqli_num_rows($res) > 0) {

            $rows = array();
            $artnrs = array();

            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
                $artnrs[] = $row['monArtnr'];
            }

            $names = $this->adFetchProductNameBatch($artnrs);
            $qtys  = $this->adFetchQtyAvailableBatch($artnrs);

            foreach ($rows as $row) {
                $monID = (int)$row['monID'];
                $monUser = $row['monUser'];
                $monArtnr = $row['monArtnr'];
                $monMoreLess = (int)$row['monMoreLess'];
                $monStoreValue = $row['monStoreValue'];
                $monIP = $row['monIP'];
                $monTime = $row['monTime'];
                $monComment = $row['monComment'];
                $monType = (int)$row['monType'];
                $monCount = (int)$row['monCount'];

                $backcolor = ($rowcolor ? "firstrow" : "secondrow");
                $rowcolor = !$rowcolor;

                $name = !empty($names[$monArtnr]) ? $names[$monArtnr] : "(saknas i AD)";
                if (strlen($name) > 60) {
                    $name = substr($name, 0, 60) . "....";
                }

                $qtyNow = isset($qtys[$monArtnr]) ? (int)$qtys[$monArtnr] : 0;
                $monDays = 0;
                if (!empty($monTime)) {
                    $monDays = round((time() - strtotime($monTime)) / 3600 / 24);
                }

                echo "<tr>\n";
                echo "<td class=\"$backcolor\">$monArtnr</td>\n";
                echo "<td class=\"$backcolor\"><a target=\"_blank\" href=\"https://www2.cyberphoto.se/info.php?article=" . $monArtnr . "\">" . $name . "</a></td>\n";

                if ($monType == 3) {
                    echo "<td class=\"$backcolor\" align=\"center\">Order nr</td>\n";
                } else {
                    echo "<td class=\"$backcolor\" align=\"center\">Lagersaldo</td>\n";
                }

                if ($monType == 3) {
                    echo "<td class=\"$backcolor\" align=\"center\"><a href=\"javascript:winPopupCenter(500, 1000, '/order_info.php?order=$monStoreValue');\">$monStoreValue</a></td>\n";
                    echo "<td class=\"$backcolor\" align=\"center\">$qtyNow</td>\n";
                } else {
                    if ($monMoreLess == 0) {
                        echo "<td class=\"$backcolor\" align=\"center\">Mindre än $monStoreValue</td>\n";
                    } elseif ($monMoreLess == 1) {
                        echo "<td class=\"$backcolor\" align=\"center\">Mer än $monStoreValue</td>\n";
                    } else {
                        echo "<td class=\"$backcolor\" align=\"center\">Alla ändringar ($monCount)</td>\n";
                    }
                    echo "<td class=\"$backcolor\" align=\"center\">$qtyNow</td>\n";
                }

                echo "<td class=\"$backcolor\" align=\"center\">$monUser</td>\n";

                if (!empty($_COOKIE['login_mail']) && $_COOKIE['login_mail'] == 'stefan@cyberphoto.se') {
                    echo "<td class=\"$backcolor\" align=\"center\">$monIP</td>\n";
                    if ($monDays == 0) {
                        echo "<td class=\"$backcolor\" align=\"right\"><b>$monDays dagar</b>&nbsp;&nbsp;</td>\n";
                    } else {
                        echo "<td class=\"$backcolor\" align=\"right\">$monDays dagar&nbsp;&nbsp;</td>\n";
                    }

                    if ($monComment != "" && $monID > 349) {
                        echo "<td align=\"center\"><img title=\"" . htmlspecialchars($monComment) . "\" border=\"0\" src=\"eye.png\"></td>\n";
                    } else {
                        echo "<td>&nbsp;</td>\n";
                    }
                }

                echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $monID . "\">Ändra</a></td>\n";
                echo "</tr>\n";

                $startcount++;
            }

        } else {
            echo "<tr>\n";
            echo "<td colspan=\"5\"><font color=\"#000000\"><b>Inga artiklar kommer bevakas</b></td>\n";
            echo "</tr>\n";
        }

        echo "<tr>\n";
        echo "<td colspan=\"5\"><b>Just nu bevakas $startcount artiklar med olika kriterier</b></td>\n";
        echo "</tr>\n";
        echo "</table>\n";
    }

    function getNotActualMonitors() {
        echo "<table>\n";
        echo "<tr>\n";
        echo "<td width=\"150\"><b>Artikel nr</b></td>\n";
        echo "<td width=\"400\"><b>Benämning</b></td>\n";
        echo "<td width=\"90\" align=\"center\"><b>Typ</b></td>\n";
        echo "<td width=\"110\" align=\"center\"><b>Värde</b></td>\n";
        echo "<td width=\"110\" align=\"center\"><b>Bevakas av</b></td>\n";
        echo "<td width=\"160\" align=\"center\"><b>Avslutad</b></td>\n";
        echo "</tr>\n";

        $sql = "
            SELECT monID, monUser, monArtnr, monMoreLess, monStoreValue, monEnd, monType
            FROM cyberphoto.MonitorArticles
            WHERE monActive = 0
            ORDER BY monEnd DESC
            LIMIT 100
        ";
        $res = mysqli_query($this->db_r, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            $artnrs = array();
            $rows = array();
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
                $artnrs[] = $row['monArtnr'];
            }
            $names = $this->adFetchProductNameBatch($artnrs);

            $rowcolor = true;
            foreach ($rows as $row) {
                $backcolor = ($rowcolor ? "firstrow" : "secondrow");
                $rowcolor = !$rowcolor;

                $artnr = $row['monArtnr'];
                $name  = !empty($names[$artnr]) ? $names[$artnr] : "(saknas i AD)";
                $type  = ((int)$row['monType'] == 3) ? "Order nr" : "Lagersaldo";

                echo "<tr>\n";
                echo "<td class=\"$backcolor\">" . htmlspecialchars($artnr) . "</td>\n";
                echo "<td class=\"$backcolor\">" . htmlspecialchars($name) . "</td>\n";
                echo "<td class=\"$backcolor\" align=\"center\">$type</td>\n";
                echo "<td class=\"$backcolor\" align=\"center\">" . htmlspecialchars($row['monStoreValue']) . "</td>\n";
                echo "<td class=\"$backcolor\" align=\"center\">" . htmlspecialchars($row['monUser']) . "</td>\n";
                echo "<td class=\"$backcolor\" align=\"center\">" . htmlspecialchars($row['monEnd']) . "</td>\n";
                echo "</tr>\n";
            }
        } else {
            echo "<tr><td colspan=\"6\">Inga avslutade bevakningar hittades.</td></tr>\n";
        }

        echo "</table>\n";
    }

    function getActualMonitorsArticle($addArtnr) {
        $addArtnr = trim((string)$addArtnr);
        if ($addArtnr === '') return;

        echo "<table>\n";
        echo "<tr>\n";
        echo "<td width=\"150\"><b>Artikel nr</b></td>\n";
        echo "<td width=\"400\"><b>Benämning</b></td>\n";
        echo "<td width=\"90\" align=\"center\"><b>Typ</b></td>\n";
        echo "<td width=\"110\" align=\"center\"><b>Värde</b></td>\n";
        echo "<td width=\"75\" align=\"center\"><b>Just nu</b></td>\n";
        echo "<td width=\"135\" align=\"center\"><b>Bevakas av</b></td>\n";
        echo "<td width=\"75\" align=\"center\"><b>&nbsp;</b></td>\n";
        echo "</tr>\n";

        $artnrEsc = $this->esc($addArtnr);

        $sql = "
            SELECT monID, monUser, monArtnr, monMoreLess, monStoreValue, monType, monCount
            FROM cyberphoto.MonitorArticles
            WHERE monActive = 1 AND monArtnr = '$artnrEsc'
            ORDER BY monUser ASC
        ";
        $res = mysqli_query($this->db_r, $sql);

        $names = $this->adFetchProductNameBatch(array($addArtnr));
        $qtys  = $this->adFetchQtyAvailableBatch(array($addArtnr));
        $name  = !empty($names[$addArtnr]) ? $names[$addArtnr] : "(saknas i AD)";
        $qtyNow = isset($qtys[$addArtnr]) ? (int)$qtys[$addArtnr] : 0;

        if ($res && mysqli_num_rows($res) > 0) {
            $rowcolor = true;
            while ($row = mysqli_fetch_assoc($res)) {
                $backcolor = ($rowcolor ? "firstrow" : "secondrow");
                $rowcolor = !$rowcolor;

                $monID = (int)$row['monID'];
                $monUser = $row['monUser'];
                $monMoreLess = (int)$row['monMoreLess'];
                $monStoreValue = $row['monStoreValue'];
                $monType = (int)$row['monType'];
                $monCount = (int)$row['monCount'];

                echo "<tr>\n";
                echo "<td class=\"$backcolor\">" . htmlspecialchars($addArtnr) . "</td>\n";
                echo "<td class=\"$backcolor\">" . htmlspecialchars($name) . "</td>\n";
                echo "<td class=\"$backcolor\" align=\"center\">" . (($monType == 3) ? "Order nr" : "Lagersaldo") . "</td>\n";

                if ($monType == 3) {
                    echo "<td class=\"$backcolor\" align=\"center\">$monStoreValue</td>\n";
                    echo "<td class=\"$backcolor\" align=\"center\">$qtyNow</td>\n";
                } else {
                    if ($monMoreLess == 0) {
                        echo "<td class=\"$backcolor\" align=\"center\">Mindre än $monStoreValue</td>\n";
                    } elseif ($monMoreLess == 1) {
                        echo "<td class=\"$backcolor\" align=\"center\">Mer än $monStoreValue</td>\n";
                    } else {
                        echo "<td class=\"$backcolor\" align=\"center\">Alla ändringar ($monCount)</td>\n";
                    }
                    echo "<td class=\"$backcolor\" align=\"center\">$qtyNow</td>\n";
                }

                echo "<td class=\"$backcolor\" align=\"center\">" . htmlspecialchars($monUser) . "</td>\n";
                echo "<td align=\"center\"><a href=\"" . $_SERVER['PHP_SELF'] . "?change=" . $monID . "\">ändra</a></td>\n";
                echo "</tr>\n";
            }
        } else {
            echo "<tr><td colspan=\"7\">Inga aktiva bevakningar på denna artikel.</td></tr>\n";
        }

        echo "</table>\n";
    }

    /* =========================
       Validering för add-form
    ========================= */

    function check_artikel_status($addArtnr) {
        $addArtnr = trim((string)$addArtnr);
        if ($addArtnr === '') return false;
        return $this->adProductExists($addArtnr) ? $addArtnr : false;
    }

    function check_artikel_on_order($ordernr, $addArtnr) {
        $ordernr = trim((string)$ordernr);
        $addArtnr = trim((string)$addArtnr);
        if ($ordernr === '' || $addArtnr === '') return false;

        $sql = "
            SELECT 1
            FROM c_order o
            JOIN c_orderline ol ON ol.c_order_id = o.c_order_id
            JOIN m_product p ON p.m_product_id = ol.m_product_id
            WHERE o.documentno = $1
              AND p.value = $2
            LIMIT 1
        ";
        $res = ($this->ad_r) ? @pg_query_params($this->ad_r, $sql, array($ordernr, $addArtnr)) : false;
        if (!$res) return false;

        return ($res && pg_num_rows($res) > 0);
    }

    function getStoreValue($artnr) {
        $artnr = trim((string)$artnr);
        if ($artnr === '') return 0;

        $qtys = $this->adFetchQtyAvailableBatch(array($artnr));
        return isset($qtys[$artnr]) ? (int)$qtys[$artnr] : 0;
    }

    /* =========================
       CRUD (samma signaturer som v3)
    ========================= */

    function doMonitorAdd_v3($addArtnr, $addRecipient, $addMoreLess, $addStoreValue, $addComment, $addType) {
        $monIP = $_SERVER['REMOTE_ADDR'];
        $addBy = !empty($_SESSION['admin_info']['email']) ? $_SESSION['admin_info']['email'] : 'noreply';

        $artnr = $this->esc($addArtnr);
        $recipient = $this->esc($addRecipient);
        $moreLess = (int)$addMoreLess;
        $storeValue = (int)$addStoreValue;
        $comment = $this->esc($addComment);
        $type = (int)$addType;
        $ip = $this->esc($monIP);
        $by = $this->esc($addBy);

        $sql = "
            INSERT INTO cyberphoto.MonitorArticles
                (monArtnr, monUser, monMoreLess, monStoreValue, monComment, monIP, monType, monAddBy)
            VALUES
                ('$artnr', '$recipient', $moreLess, $storeValue, '$comment', '$ip', $type, '$by')
        ";
        mysqli_query($this->db_w, $sql);

        header("Location: monitor_articles.php");
        exit;
    }

    function doMonitorChange_v3($addID, $addArtnr, $addRecipient, $addMoreLess, $addStoreValue, $addActive, $addComment, $addType) {
        $aktuelltdatum = date("Y-m-d H:i:s");

        $id = (int)$addID;
        $active = (int)$addActive;

        $artnr = $this->esc($addArtnr);
        $recipient = $this->esc($addRecipient);
        $moreLess = (int)$addMoreLess;
        $storeValue = (int)$addStoreValue;
        $comment = $this->esc($addComment);
        $type = (int)$addType;

        if ($active == 0) {
            $sql = "
                UPDATE cyberphoto.MonitorArticles
                SET monArtnr = '$artnr',
                    monUser = '$recipient',
                    monMoreLess = $moreLess,
                    monStoreValue = $storeValue,
                    monActive = 0,
                    monComment = '$comment',
                    monType = $type,
                    monEnd = '$aktuelltdatum'
                WHERE monID = $id
            ";
        } else {
            $sql = "
                UPDATE cyberphoto.MonitorArticles
                SET monArtnr = '$artnr',
                    monUser = '$recipient',
                    monMoreLess = $moreLess,
                    monStoreValue = $storeValue,
                    monActive = 1,
                    monComment = '$comment',
                    monType = $type
                WHERE monID = $id
            ";
        }

        mysqli_query($this->db_w, $sql);

        header("Location: monitor_articles.php");
        exit;
    }

    function doMonitorDeactivate($addID) {
        $aktuelltdatum = date("Y-m-d H:i:s");
        $id = (int)$addID;

        $sql = "UPDATE cyberphoto.MonitorArticles SET monActive = 0, monEnd = '$aktuelltdatum' WHERE monID = $id";
        mysqli_query($this->db_w, $sql);
    }

    function doCorrectMonitor($newValue, $monID) {
        $id = (int)$monID;
        $val = (int)$newValue;

        $sql = "UPDATE cyberphoto.MonitorArticles SET monStoreValue = $val, monCount = monCount + 1 WHERE monID = $id";
        mysqli_query($this->db_w, $sql);
    }

    /* =========================
       Cron: checkArticlesLevel(0) & (3)
    ========================= */

    function checkArticlesLevel($monitorType = null) {
        $monitorType = (int)$monitorType;

        if ($monitorType == 1 || $monitorType == 2) {
            return;
        }

        $sql = "
            SELECT monID, monUser, monArtnr, monStoreValue, monComment, monMoreLess, monType
            FROM cyberphoto.MonitorArticles
            WHERE monActive = 1 AND monType = $monitorType
        ";
        $res = mysqli_query($this->db_r, $sql);
        if (!$res || mysqli_num_rows($res) == 0) return;

        $rows = array();
        $artnrs = array();

        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
            if ($monitorType == 0) {
                $artnrs[] = $row['monArtnr'];
            }
        }

        $qtys = array();
        if ($monitorType == 0) {
            $qtys = $this->adFetchQtyAvailableBatch($artnrs);
        }

        foreach ($rows as $row) {
            $monID = (int)$row['monID'];
            $monUser = $row['monUser'];
            $monArtnr = $row['monArtnr'];
            $monStoreValue = (int)$row['monStoreValue'];
            $monComment = $row['monComment'];
            $monMoreLess = (int)$row['monMoreLess'];

            $trigger = false;
            $valueNow = 0;

            if ($monitorType == 3) {
                $ordernr = (string)$monStoreValue;
                if ($this->adIsOrderAllocated($ordernr, $monArtnr)) {
                    $trigger = true;
                    $valueNow = $monStoreValue;
                }
            } else {
                $qtyNow = isset($qtys[$monArtnr]) ? (int)$qtys[$monArtnr] : 0;
                $valueNow = $qtyNow;

                if ($monMoreLess == 0) {
                    $trigger = ($qtyNow < $monStoreValue);
                } elseif ($monMoreLess == 1) {
                    $trigger = ($qtyNow > $monStoreValue);
                } else {
                    $trigger = ($qtyNow != $monStoreValue);
                }
            }

            if ($trigger) {
                $this->sendMonitorMess_v1($monUser, $monArtnr, $valueNow, $monComment, $monitorType);

                if ($monMoreLess == 2 && $monitorType == 0) {
                    $this->doCorrectMonitor($valueNow, $monID);
                } else {
                    $this->doMonitorDeactivate($monID);
                }
            }
        }
    }

    function sendMonitorMess_v1($monUser, $monArtnr, $monStoreValue, $monComment, $monType) {
        $bevdatum = date("Y-m-d H:i:s", time());

        $addcreatedby = "no-reply";
        $recipient = " " . $monUser;
        $subj = $bevdatum . " Bevakning av artikel " . $monArtnr;
        $extra = "From: " . $addcreatedby;

        $text1 = "Artikel med bevakning har nått upsatt nivå.\n\n";
        $text1 .= "Artikel nr: " . $monArtnr . "\n\n";

        if ($monType == 3) {
            $text1 .= "Order nr: " . $monStoreValue . "\n\n";
        } else {
            $text1 .= "Lagersaldo just nu: " . $monStoreValue . "\n\n";
        }

        if ($monComment != "") {
            $text1 .= "Din egen notis: " . $monComment . "\n\n";
        }

        $text1 .= "Vänligen vidta lämplig åtgärd!\n\n";
        // $text1 .= "http://www2.cyberphoto.se/info.php?article=" . $monArtnr . "\n";
        $text1 .= "https://admin.cyberphoto.se/search_dispatch.php?mode=product&q=" . $monArtnr . "\n";

        @mail($recipient, $subj, $text1, $extra);
    }

    function isValidDateTime($dateTime) {
        if (preg_match("/^(\\d{4})-(\\d{2})-(\\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        return false;
    }

	function getMonAlerts($ID) {

		$id = (int)$ID; // monID är numeriskt, så vi castar hårt

		$sql = "SELECT * FROM cyberphoto.MonitorArticles WHERE monID = " . $id . " LIMIT 1";

		$res = mysqli_query($this->db_r, $sql);
		if (!$res) {
			return false;
		}

		$row = mysqli_fetch_object($res);
		if (!$row) {
			return false;
		}

		return $row;
	}

}
?>
