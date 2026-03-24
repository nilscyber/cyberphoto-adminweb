<?php

require_once("CCheckIpNumber.php");
include("connections.php");

Class CAvbetalningar {

    function __construct() {

        $this->conn_my = Db::getConnection();
        $this->conn_ms = @mssql_pconnect("81.8.240.66", "apache", "aKatöms#1");
        @mssql_select_db("cyberphoto", $this->conn_ms);
        $this->conn_fi = $this->conn_ms;
    }

    function getPayment($country) {

        $rowcolor = true;

        $select = "SELECT dscrptn, currency, count(ordernr) AS Antal, avg(totalSum) AS Snitt ";
        $select .= "FROM kreditor_reference ";
        $select .= "LEFT JOIN kreditor_pclasses ON kreditor_reference.pclass = kreditor_pclasses.pclass ";
        $select .= "WHERE status = -1  AND NOT (currency IS NUll) AND NOT (dscrptn IS NUll) ";
        $select .= "AND currency = '" . $country . "' ";
        $select .= "GROUP BY dscrptn, currency ";
        $select .= "ORDER BY currency, Snitt ASC ";

        $res = mssql_query($select, $this->conn_ms);

        if (mssql_num_rows($res) > 0) {

            echo "<table>";
            echo "<tr>";
            if ($country == "EUR") {
                echo "<td width=\"250\"><font face=\"Verdana\" size=\"1\"><img border=\"0\" src=\"fi_mini.jpg\"></td>";
            } else {
                echo "<td width=\"250\"><font face=\"Verdana\" size=\"1\"><img border=\"0\" src=\"sv_mini.jpg\"></td>";
            }
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Antal</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Snitt</b></td>";
            echo "<td width=\"50\" align=\"center\"><font face=\"Verdana\" size=\"1\"><b>Valuta</b></td>";
            echo "</tr>";

            while ($row = mssql_fetch_array($res)):

                extract($row);

                if ($rowcolor == true) {
                    $backcolor = "#E8E8E8";
                } else {
                    $backcolor = "#FFCF9F";
                }

                echo "<tr>";
                echo "<td bgcolor=\"$backcolor\"><font face=\"Verdana\" size=\"1\">$dscrptn</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$Antal</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">" . round($Snitt) . "</td>";
                echo "<td bgcolor=\"$backcolor\" align=\"center\"><font face=\"Verdana\" size=\"1\">$currency</td>";
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

}

?>
