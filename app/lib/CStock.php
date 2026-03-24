<?php
// not used

include("connections.php");

Class CStock {

    var $conn_ms;
    var $conn_my;
    var $conn_fi;

    function __construct() {

        $this->conn_my = Db::getConnection();
//        $this->conn_ms = @mssql_pconnect("81.8.240.66", "apache", "aKatöms#1");
 //       @mssql_select_db("cyberphoto", $this->conn_ms);
        $this->conn_fi = $this->conn_ms;
    }

    function getArticlesInStock() {

        $rowcolor = true;

// $select  = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.lagersaldo, Tillverkare.tillverkare, Artiklar.art_id ";
        $select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.lagersaldo, Tillverkare.tillverkare, Artiklar.art_id, (Artiklar.art_id * Artiklar.lagersaldo) AS SummaLager ";
        $select .= "FROM Artiklar ";
        $select .= "JOIN Tillverkare ON Tillverkare.tillverkar_id = Artiklar.tillverkar_id ";
        $select .= "WHERE lagersaldo > 0 AND art_id > 1 ";
// $select .= "WHERE lagersaldo > 0 AND artnr = '232079' ";
        $select .= "AND NOT kategori_id IN(314) ";
// $select .= "ORDER BY lagersaldo DESC ";
        $select .= "ORDER BY SummaLager DESC ";

// echo $select;
// exit;

        $res = mssql_query($select, $this->conn_ms);

        if (mssql_num_rows($res) > 0) {

            echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"1050\">";
            echo "<tr>";
            echo "<td width=\"600\"><b>Produkt</b></td>";
            echo "<td width=\"50\" align=\"center\"><b>Lager</b></td>";
            echo "<td width=\"200\"><b>Artikelnummer</b></td>";
            echo "<td width=\"100\" align=\"center\"><b>Dagar i lager</b></td>";
            echo "<td width=\"100\" align=\"center\"><b>Inpris</b></td>";
            echo "<td width=\"100\" align=\"center\"><b>Lagervärde</b></td>";
            echo "</tr>";

            while ($row = mssql_fetch_array($res)):

                extract($row);


                if ($this->check_days_instock($artnr)) {

                    // $ordervarde = ($art_id * $lagersaldo);
                    $art_id = number_format($art_id, 0, ',', ' ');
                    $daysinstockshow = $this->display_days_instock($artnr);
                    $SummaLager = number_format($SummaLager, 0, ',', ' ');


                    if ($rowcolor == true) {
                        $backcolor = "#E8E8E8";
                    } else {
                        $backcolor = "#FFCF9F";
                    }

                    echo "<tr>";
                    echo "<td bgcolor=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$tillverkare $beskrivning</a></td>";
                    echo "<td bgcolor=\"$backcolor\" align=\"center\">$lagersaldo</td>";
                    echo "<td bgcolor=\"$backcolor\"><a target=\"_blank\" href=\"/?info.php?article=$artnr\">$artnr</a></td>";
                    echo "<td bgcolor=\"$backcolor\" align=\"center\">$daysinstockshow</td>";
                    echo "<td bgcolor=\"$backcolor\" align=\"right\">$art_id kr</td>";
                    echo "<td bgcolor=\"$backcolor\" align=\"right\">$SummaLager kr</td>";
                    echo "</tr>";


                    if ($rowcolor == true) {
                        $row = true;
                        $rowcolor = false;
                    } else {
                        $row = false;
                        $rowcolor = true;
                    }
                }

            endwhile;
        } else {

            echo "<tr>";
            echo "<td colspan=\"3\"><font color=\"#FFFFFF\"><b>Tomt</b></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function check_days_instock($artnr) {

        $select = "SELECT DATEDIFF(dd, leveransTid, getdate()) AS DagarInStock FROM InkomnaArtiklar WHERE artnr = '" . $artnr . "' ORDER BY leveransTid DESC ";

        $res = mssql_query($select, $this->conn_ms);

        $row = mssql_fetch_object($res);

        if (mssql_num_rows($res) > 0) {

            if ($row->DagarInStock > 180) {

                return true;
            } else {

                return false;
            }
        } else {

            return false;
        }
    }

    function display_days_instock($artnr) {

        $select = "SELECT DATEDIFF(dd, leveransTid, getdate()) AS DagarInStock FROM InkomnaArtiklar WHERE artnr = '" . $artnr . "' ORDER BY leveransTid DESC ";

        $res = mssql_query($select, $this->conn_ms);

        $row = mssql_fetch_object($res);

        if (mssql_num_rows($res) > 0) {

            return $row->DagarInStock;
        } else {

            echo "&nbsp;&nbsp;";
        }
    }

}

?>
