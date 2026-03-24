<?php
// currently not used...


require_once("CCheckIpNumber.php");
include("connections.php");

Class CPromotionCode {

    function __construct() {

        $this->conn_my = Db::getConnection();
//        $this->conn_ms = @mssql_pconnect("81.8.240.66", "apache", "aKatöms#1");
 //       @mssql_select_db("cyberphoto", $this->conn_ms);
        $this->conn_fi = $this->conn_ms;
    }

    function getLatestCodes() {

        $rowcolor = true;

        $select = "SELECT DATENAME(YEAR, inkommet) AS År, DATENAME(MONTH, inkommet) AS Månad, COUNT(ordernr) AS Antal, MONTH (inkommet) AS NRManad ";
        $select .= "FROM ordertabell_alla ";
        $select .= "WHERE NOT (promotioncode IS NULL) ";
        $select .= "GROUP BY DATENAME(YEAR, inkommet), DATENAME(MONTH, inkommet), MONTH (inkommet) ";
        $select .= "ORDER BY DATENAME(YEAR, inkommet) DESC, MONTH (inkommet) DESC ";

// echo $select;

        $res = mssql_query($select, $this->conn_ms);

        if (mssql_num_rows($res) > 0) {

            echo "<table>";
            echo "<tr>";
            echo "<td width=\"50\"><font face=\"Verdana\" size=\"1\"><b>År</b></td>";
            echo "<td width=\"65\"><font face=\"Verdana\" size=\"1\"><b>Månad</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Antal</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Totalt</b></td>";
            echo "<td width=\"50\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
            echo "</tr>";

            while ($row = mssql_fetch_array($res)):

                extract($row);

                if ($rowcolor == true) {
                    $backcolor = "#CCFFCC";
                } else {
                    $backcolor = "#80FF80";
                }

                echo "<tr>";
                echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">$År</td>";
                echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">$Månad</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$Antal</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . $this->getCodesTotal($År, $NRManad) . "</td>";
                echo "<td><font face=\"Verdana\" size=\"1\"><a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&year=" . $År . "&month=" . $NRManad . "\">Detaljer</td>";
                echo "</tr>";

                if ($rowcolor == true) {
                    $row = true;
                    $rowcolor = false;
                } else {
                    $row = false;
                    $rowcolor = true;
                }

            endwhile;
        } else {

            echo "<tr>";
            echo "<td colspan=\"5\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Tomt</b></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function getCodesMonth($year, $month) {

        $rowcolor = true;

        $select = "SELECT promotioncode, COUNT(ordernr) AS Antal ";
        $select .= "FROM ordertabell_alla ";
        $select .= "WHERE YEAR(inkommet) = $year AND MONTH (inkommet) = $month AND NOT (promotioncode IS NULL) ";
        $select .= "GROUP BY promotioncode ";
        $select .= "ORDER BY promotioncode ASC";

        $res = mssql_query($select, $this->conn_ms);

        if (mssql_num_rows($res) > 0) {

            echo "<table>";
            echo "<tr>";
            echo "<td width=\"115\"><font face=\"Verdana\" size=\"1\"><b>KOD</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Antal</b></td>";
            echo "<td width=\"55\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Andel %</b></td>";
            echo "<td width=\"50\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
            echo "</tr>";

            while ($row = mssql_fetch_array($res)):

                extract($row);

                $procent = round(($Antal / $this->getCodesTotal($year, $month)) * 100, 2);

                if ($rowcolor == true) {
                    $backcolor = "#FFCCCC";
                } else {
                    $backcolor = "#FF9B9B";
                }

                echo "<tr>";
                echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">$promotioncode</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$Antal</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$procent</td>";
                echo "<td><font face=\"Verdana\" size=\"1\"><a href=\"" . $_SERVER['PHP_SELF'] . "?show=yes&year=" . $year . "&month=" . $month . "&promcode=" . $promotioncode . "\">Detaljer</td>";
                echo "</tr>";

                if ($rowcolor == true) {
                    $row = true;
                    $rowcolor = false;
                } else {
                    $row = false;
                    $rowcolor = true;
                }

            endwhile;

            echo "<tr>";
            echo "<td><font face=\"Verdana\" size=\"1\"><b>Antalet webbordrar</b></td>";
            echo "<td align=\"center\"><font face=\"Verdana\" size=\"1\"><b>" . $this->getCodesTotal($year, $month) . "</b></td>";
            echo "<td><font face=\"Verdana\" size=\"1\"></td>";
            echo "<td><font face=\"Verdana\" size=\"1\"></td>";
            echo "</tr>";
        } else {

            echo "<tr>";
            echo "<td colspan=\"4\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Tomt</b></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function getCodesDetail($year, $month, $promcode) {

        $rowcolor = true;

        $select = "SELECT inkommet, ordernr, land_id ";
        $select .= "FROM ordertabell_alla ";
        $select .= "WHERE YEAR(inkommet) = $year AND MONTH (inkommet) = $month AND promotioncode = '" . $promcode . "' ";
        $select .= "ORDER BY inkommet DESC";

// echo $select;

        $res = mssql_query($select, $this->conn_ms);

        if (mssql_num_rows($res) > 0) {

            echo "<table>";
            echo "<tr>";
            echo "<td width=\"20\"><font face=\"Verdana\" size=\"1\">&nbsp;</td>";
            echo "<td width=\"115\"><font face=\"Verdana\" size=\"1\"><b>Tidpunkt</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Order nr</b></td>";
            echo "</tr>";

            while ($row = mssql_fetch_array($res)):

                extract($row);

                // $procent = round(($Antal / $this->getCodesTotal($year,$month)) * 100, 2);

                $inkommet = preg_replace('/:[0-9][0-9][0-9]/', '', $inkommet);

                if ($rowcolor == true) {
                    $backcolor = "#E5E5E5";
                } else {
                    $backcolor = "#C0C0C0";
                }

                echo "<tr>";
                if ($land_id == 358) {
                    echo "<td><font face=\"Verdana\" size=\"1\"><img border=\"0\" src=\"fi_mini.jpg\"></td>";
                } else {
                    echo "<td><font face=\"Verdana\" size=\"1\"><img border=\"0\" src=\"sv_mini.jpg\"></td>";
                }
                echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">" . date("j M Y H:i", strtotime($inkommet)) . "</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$ordernr</td>";
                echo "</tr>";

                if ($rowcolor == true) {
                    $row = true;
                    $rowcolor = false;
                } else {
                    $row = false;
                    $rowcolor = true;
                }

            endwhile;
        } else {

            echo "<tr>";
            echo "<td colspan=\"2\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\"><b>Tomt</b></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function getCodesTotal($year, $month) {

        $rowcolor = true;

        $select = "SELECT COUNT(ordernr) AS Antal ";
        $select .= "FROM ordertabell_alla ";
        $select .= "WHERE YEAR(inkommet) = $year AND MONTH (inkommet) = $month AND inlagd_av = 'WO' ";

        $res = mssql_query($select, $this->conn_ms);

        extract(mssql_fetch_array($res));

        return $Antal;
    }

}

?>
