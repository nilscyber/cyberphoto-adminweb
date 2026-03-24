<?php

function session_clear() {

    // if session exists, unregister all variables that exist and destroy session 
    $exists = "no";
    $session_array = explode(";", session_encode());
    for ($x = 0; $x < count($session_array); $x++) {
        $name = substr($session_array[$x], 0, strpos($session_array[$x], "|"));
        if (session_is_registered($name)) {
            session_unregister('$name');
            $exists = "yes";
        }
    }

    if ($exists != "no") {
        session_destroy();
    }
}

function debiCrypt($strUrl) {
    return `/web/phplib/veencrypt '$strUrl'`;
}

function getCountry($land_id) {
    global $conn_my;
    $select = "SELECT land_kod FROM Land WHERE land_id like '$land_id'";
    $res = mysqli_query($conn_my, $select);
    $row = mysqli_fetch_object($res);

    return $row->land_kod;
    include ("CConnect_ms.php");
}

function cryptoData($orderrow) {
    global $REMOTE_ADDR, $fi;

    //$ordersumma = number_format($orderrow->netto + $orderrow->moms, 0, "", "");
    $ordersumma = $orderrow->totalsumma;
    $ordersumma = $ordersumma * 100;

    $datat = 'data=';
    $datat .= rawurlencode('1:vara:1:' . $ordersumma . ':');
    /**
      if ($fi)
      $datat .= '&currency=EUR';
      else
      $datat .= '&currency=SEK';
     */
    $datat .= "&currency=" . $orderrow->currency;
    $datat .= '&shipment=0';
    $datat .= "&kundnr=" . $orderrow->kundnr;
    $datat .= "&ordernr=" . $orderrow->ordernr;
    if (strlen($orderrow->email < 7)) {
        $datat .= "&eMail=" . rawurlencode(trim($orderrow->email));
    } else {
        $datat .= "&eMail=" . rawurlencode('null@cyberphoto.se');
    }
    $datat .= "&transID=" . $orderrow->ordernr;
    $datat .= "&namn=" . rawurlencode($orderrow->namn);
    if (strlen($orderrow->co) == 0) {
        $datat .= "&billingAddress=" . rawurlencode($orderrow->ladress);
    } else {
        $datat .= "&billingAddress=" . rawurlencode($orderrow->co);
    }
    $datat .= "&billingCity=" . rawurlencode($orderrow->postadress);
    $datat .= "&billingCountry=" . rawurlencode(getCountry($orderrow->fland_id));
    $datat .= "&billingZipCode=" . rawurlencode($orderrow->postnr);
    $datat .= "&billingFirstName=" . rawurlencode($orderrow->namn);
    $datat .= "&billingLastName=" . rawurlencode($orderrow->namn);
    $datat .= "&ip=" . rawurlencode($REMOTE_ADDR);
    //if (!$fi)
    $datat .= "&uses3dsecure=true";
    $datat .= "&resetSession=true";
    $datat .= "&referenceNo=" . $orderrow->ordernr;
    $datat .= "&metod=order";

    return rtrim(debiCrypt($datat));
}

function show_summary() {

    global $orderrow, $messageText, $messageHtml, $fi, $sv;


    if ($fi)
        $val = " EUR";
    else
        $val = " SEK";
    $summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " " . $val;

    if ($orderrow->land_id == 47 || $orderrow->land_id == 999) {
        $moms = sprintf("%10.0f SEK", 0);
        $summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
    } else {
        $moms = number_format($orderrow->moms, 0, ',', ' ') . " " . $val;
        $summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
    }
    if ($fi && !$sv) {
        ?>
        </center></div>
        <div align="center"><center><table border=0 cellspacing=1 cellpadding=2>
                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Alv 0%:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->netto, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Alv:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->moms, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">Sis Alv:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                </table></center></div>
        <?php
    } else {
        ?>
        </center></div>
        <div align="center"><center><table border=0 cellspacing=1 cellpadding=2>
                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">exkl moms:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->netto, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">moms:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->moms, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                    <tr><td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial">total summa:</font></small></small></td>
                        <td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial">
                                    <?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val; ?>
                                    </font></small></small></td></tr>

                </table></center></div>
        <?php
    }

    if ($fi && !$sv) {
        $messageText .= <<<EOF

  Alv 0%:  $summaexmoms
  Alv:     $moms
  Sis Alv: $summaMedMoms

EOF;
        $messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv 0%:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Sis Alv:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;
    } else {
        $messageText .= <<<EOF

  exkl. moms:       $summaexmoms
  Moms:             $moms
  Totalt:           $summaMedMoms

EOF;
        $messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">exkl moms:</font></p>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="3" height="15">
    <p align="right"><font face="Verdana, Arial" size="1"> inkl moms:</font>
  </td><td bgcolor="#ECECE6" height="15"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;
    }
}

function write_creditcard() {
    global $summaMedMoms;
    ?>
    <p><font face="Verdana" size="1">Ni har valt att betala med kontokort. 
        Betalningen är preliminärt godkänd. Betalningen kommer att belastas ert kort 
        när varorna skickas från CyberPhoto.</font></p>
    <?php
}

function netpay() {
    global $orderrow, $ordernrladdaom;

    # Lägg på moms
    #$totalsumma = $orderrow->netto*1.25;
    $totalsumma = $orderrow->totalsumma;
    # Avrunda beloppet
    $totalsumma = number_format($totalsumma, 0, "", "");

    // $output = `/usr/java/jdk1.3.1_07/bin/java -cp /usr/java/lib/HFAffar.jar se.hbfinans.netpay.store.HFStoreModule 900080 $ordernrladdaom $totalsumma SEK "" SV http://www.cyberphoto.se/?ordernr=$ordernrladdaom&` ;
    // nedanstående när vi byter server
    $output = `/usr/bin/java -cp /usr/lib/java/HFAffar.jar se.hbfinans.netpay.store.HFStoreModule 900080 $ordernrladdaom $totalsumma SEK "" SV http://www.cyberphoto.se/?ordernr=$ordernrladdaom&`;
    ?>
    <form action="https://www.netpay.saljfinans.com/reservation" target="_parent" method="get">
        <input type="hidden" name="reservation" value="<?php echo $output; ?>">
        <input type="image" src="vidare.gif" border="0" value="Klicka här för att gå vidare till Netpay">
    </form>
    <script language="JavaScript">
        document.forms[0].submit();
    </script>

    <?php
}

function write_toOrderposter($artnr, $newordernr, $count, $includedArticle = false, $arrayOnly = false) {
    global $goodsvalue, $moms1, $moms2, $moms3, $moms4, $fi, $sv, $no, $bask, $inkommet, $frakt, $bask, $conn_standard, $extra_frakt, $butiksfrakt,
    $discountCode, $discountCodeStatus, $rowDiscount, $freight, $pay, $conn_my, $conn_master;
    
    $noTax = false;
    
    if ($_SESSION['old_land_id'] == 999)
        $noTax = true;
    
    // Nollställ variabler
    $totalpacutpris = "";
    $rabatt = "";
    $check = "";
    $inserted = "";
    $bokad = "";
    $levDatum = "";
    $beskrivning_alt = "";
    $isHidden = 0;
    $visualPrice = 0;
    $pacKey = NULL;

    $arrRows = array();
    
    $select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.momskod, utpris, utpris_fi, utpris_no, frakt, lagersaldo, till_kund, ";
    $select .= "Moms.momssats, Moms.momssats_fi, Moms.momssats_no, Artiklar.tillverkar_id, Artiklar.kategori_id, Artiklar.IsSalesBundle ";
    $select .= "FROM Artiklar ";
    $select .= "JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
    $select .= "JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
    $select .= "AND artnr = '" . $artnr . "'";

    if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
        //echo $select . "<br>\n\n";
        //exit;
    }
    // Get the data from the DB
    $res = mysqli_query($conn_my, $select);
    $row = mysqli_fetch_object($res);

    $frakt = $row->frakt;

    if ($fi) {
        // $outprice = $row->utpris_fi;
        $outprice = round($row->utpris_fi, 2);
        // $momssats = $row->momssats_fi;
        if ($noTax)
            $momssats = 0;
        else
            $momssats = round($row->momssats_fi, 2);
        // $momssats = 0.23;
        if ($sv || $row->beskrivning_fi == "")
            $beskrivning = $row->beskrivning;
        else
            $beskrivning = $row->beskrivning_fi;
    } else if ($no) {        
        // $momssats = $row->momssats;
        if ($noTax)
            $momssats = 0;
        else
            $momssats = round($row->momssats_no, 2);
        // $outprice = $row->utpris;
        $outprice = round($row->utpris_no, 2);
        $beskrivning = $row->beskrivning;        
    } else {
        // $momssats = $row->momssats;
        if ($noTax)
            $momssats = 0;
        else
            $momssats = round($row->momssats, 2);
        // $outprice = $row->utpris;
        $outprice = round($row->utpris, 2);

        $beskrivning = $row->beskrivning;
    }
    if ($row->kategori_id == 595 || $row->kategori_id == 509 || $row->kategori_id == 630) {
        $isHidden = -1;
        $pacKey = "abbpkt";
        // $bokad = 1;
    }
    if ($row->kategori_id == 595 || $row->kategori_id == 509 || $row->kategori_id == 629 || $row->kategori_id == 630) {
        // $isHidden = -1;
        $bokad = 1;
    }

    if ($row->kategori_id == 630 && $_SESSION['MOB_DELBETALA'] > 0) {
        $outprice = $_SESSION['MOB_DIFF_COUNT'];
	}
    if (($row->kategori_id == 595 || $row->kategori_id == 509) && $_SESSION['MOB_PRICE'] == 1) {
        $outprice = $_SESSION['MOB_DIFF_COUNT'];
        // $outprice = 9001;
    }
    
    if ($_SESSION['MOB_CONFIRMED'] && $_SESSION['MOB_PHONE'] == $artnr) {
        $beskrivning_alt = $_SESSION['MOB_BESKRIVNING'];
        $visualPrice = ($_SESSION['MOB_PRICE'] * 1 / ( 1 + $momssats ));
        $pacKey = "abbpkt";
        if ($arrayOnly) { 
            $arrRows[] = array(
                "ArticleNumber" => $pacKey,
                "Description" => $beskrivning_alt,
                "PricePerUnit" => $visualPrice,
                "NumberOfUnits" => $count,
                "Unit" => "st",
                "VatPercent" => $momssats * 100,
                "DiscountPercent" => 0
            );   
            // lägger till dessa här annars blir det inte gjort då vi inte går längre i koden än så här när det är mobilabonnemang
            if ($row->momskod == 1)
                $moms1 += ($momssats * $visualPrice * $count);
            elseif ($row->momskod == 2)
                $moms2 += ($momssats * $visualPrice * $count);
            elseif ($row->momskod == 3)
                $moms3 += ($momssats * $visualPrice * $count);
            elseif ($row->momskod == 4)
                $moms4 += ($momssats * $visualPrice * $count);     
            
            $goodsvalue+=($visualPrice * $count);
            
            return $arrRows;
        }
    } else {
        $visualPrice = 0;
    }
	
	if ($includedArticle) {
		$outprice = 0;
	}


    // ja, jag vet, den behövs inte, men jag orkar inte leta igenom alla rader där det blir fel annars. 
    $utpris = $outprice;
    if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98xx") {
        echo "<br>Row: <br>";
        Tools::print_rw($row);
        exit;
    }
    $discount = false;
    $utprisNormal = $outprice;
    $newUtpris = null;
    $extraBeskrivningText = "";

    if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
        echo "här: \n<br>";
        print_r($rowDiscount);
        echo "\n<status: " . $discountCodeStatus;
    }
    

    // if ($discountCode != "" && $discountCodeStatus == 1 && $rowDiscount->totalSum == "" && !$fi) {							
    if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" && $row->lagersaldo > 0 && $discountCode == "CANON20XX" && $discountCodeStatus == 1 && ($row->lagersaldo >= $count)) {

        $newUtpris = $bask->getDiscountPrice($rowDiscount, $row->artnr, $row->kategori_id, $row->tillverkar_id, $utpris);
        if ($newUtpris != $utpris) {
            $extraBeskrivningText = $bask->getDiscountText($rowDiscount, $momssats, $utpris, true);
            $discount = true;
            $utpris = $newUtpris;
            $outprice = $newUtpris;
        }
    } elseif ($discountCode != "" && $discountCodeStatus == 1 && $rowDiscount->totalSum == "" && $discountCode != "CANON20XX") {
        if ($rowDiscount->allowMultiple != -1)
            $count = 1;
        $newUtpris = $bask->getDiscountPrice($rowDiscount, $row->artnr, $row->kategori_id, $row->tillverkar_id, $utpris);
        if ($newUtpris != $utpris) {
            $extraBeskrivningText = $bask->getDiscountText($rowDiscount, $momssats, $utpris, false);
            $discount = true;
            $utpris = $newUtpris;
            $outprice = $newUtpris;
        }
    }
    if (!eregi("presentkort", $artnr) && !$includedArticle)
        $goodsvalue+=($outprice * $count);

    $curr = Locs::getCurrency();

    if ($row->IsSalesBundle == -1) {
        $pacKey = $row->artnr;
        $art = $row->artnr;

        if ($arrayOnly) {
            // First create row with package product
            $s = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.momskod, utpris, utpris_fi, utpris_no, frakt, lagersaldo, till_kund, ";
            $s .= "Moms.momssats, Moms.momssats_fi, Moms.momssats_no, Artiklar.tillverkar_id, Artiklar.kategori_id, Artiklar.IsSalesBundle ";
            $s .= "FROM Artiklar ";
            $s .= "JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
            $s .= "JOIN Moms ON Artiklar.momskod = Moms.moms_id ";
            $s .= "AND artnr = '" . $pacKey . "'";

            // Get the data from the DB
            $resArr = mysqli_query($conn_my, $s);
            $rowArr = mysqli_fetch_object($resArr);

            if ($sv || $rowArr->beskrivning_fi == "")
                $descrArr = $rowArr->beskrivning;
            else
                $descrArr = $rowArr->beskrivning_fi;

            if ($fi) {
                if ($noTax)
                    $momssatsA = 0;
                else
                    $momssatsA = round($rowArr->momssats_fi, 2);
                
                if ($sv || $rowArr->beskrivning_fi == "")
                    $descrArr = $rowArr->beskrivning;
                else
                    $descrArr = $rowArr->beskrivning_fi;
            } else if ($no) {
                if ($noTax)
                    $momssatsA = 0;
                else
                    $momssatsA = round($rowArr->momssats_no, 2);
                $descrArr = $rowArr->beskrivning;
           
            } else {
                if ($noTax)
                    $momssatsA = 0;
                else
                    $momssatsA = round($rowArr->momssats, 2);
                $descrArr = $rowArr->beskrivning;
            }
            $arrRows[] = array(
                "ArticleNumber" => $rowArr->artnr,
                "Description" => $descrArr,
                "PricePerUnit" => 0,
                "NumberOfUnits" => $count,
                "Unit" => "st",
                "VatPercent" => $momssatsA * 100,
                "DiscountPercent" => 0
            );
        }

        $select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, Artiklar.utpris_no, tillverkare, ";
        $select .= "frakt, Artiklar.utpris, ";
        $select .= " Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, Artiklar.momskod, ";
        $select .= "Moms.momssats_fi, Moms.momssats, Moms.momssats_no, Paketpriser.antal, art_id, art_id_fi ";
        $select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms, Paketpriser ";
        $select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
        $select .= "AND Paketpriser.artnr_del=Artiklar.artnr ";
        $select .= " AND Paketpriser.artnr_paket = '$art'";

        $res = mysqli_query($conn_my, $select);


        $marg = 0;
        $paketRabatt = 0;
        $pris = 0;
        $marg2 = 0;

        while ($row = mysqli_fetch_object($res)):
            $antal = $row->antal;
            if ($row->artnr == "63608367" && $fi && !$sv) {
                $select2 = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, ";
                $select2 .= "frakt, Artiklar.utpris, Artiklar.utpris_no, Artiklar.till_kund, Artiklar_fi.till_kund_fi, ";
                $select2 .= "Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, Artiklar.momskod, ";
                $select2 .= "Moms.momssats_fi, Moms.momssats_no, Moms.momssats, Artiklar.link, link2_fi, art_id, art_id_fi ";
                $select2 .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms ";
                $select2 .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
                $select2 .= " AND Artiklar.artnr = '63608367_fi'";
                $res3 = mysqli_query($conn_my, $select2);
                $row = mysqli_fetch_object($res3);
            }
            $frakt = $row->frakt;

            if ($frakt > 0 && !($butiksfrakt) && !$bask->freeFreight) { // ingen extra frakt om fri frakt
                // lägg bara på extra frakt om ingen annan produkt har större
                if ($frakt > $extra_frakt) {
                    $extra_frakt = $frakt;
                }
            }


            if ($fi)
                $del_utpris = $row->utpris_fi;
            else if ($no)
                $del_utpris = $row->utpris_no;
            else
                $del_utpris = $row->utpris;

            if ($fi) {
                $utpris = $row->utpris_fi;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats_fi, 2);
                
                $art_id = $row->art_id_fi;
                $till_kund = $row->till_kund_fi;
                if ($row->beskrivning_fi != "" && !$sv)
                    $beskrivning = $row->beskrivning_fi;
                else
                    $beskrivning = $row->beskrivning;
            } else if ($no) {
                $art_id = $row->art_id_no;
                $utpris = $row->utpris_no;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats_no, 2);
                
                $till_kund = $row->till_kund;
                $beskrivning = $row->beskrivning;
                          
            } else {
                $art_id = $row->art_id;
                $utpris = $row->utpris;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats, 2);
                
                $till_kund = $row->till_kund;
                $beskrivning = $row->beskrivning;
            }

            // används för att räkna ut rabatten för varje enskild artikel
            $marg += $utpris - $art_id;
            $pris = $pris + $utpris * $antal * $count;

            $utpris = round($utpris, 2);

            // Plussa på totala paketpriset
            $totalpacutpris += $del_utpris * $antal;

            // Titta hur många av paketdelen vi behöver
            $newCount = $antal * $count;

            if ($bask->prec == "")
                $bask->prec = 0;
            if (!$arrayOnly) {
                $insert = "insert into cyberorder.Orderposter (ordernr, artnr, antal, utpris, beskrivning, momssats, momskod, paketArtnr, currency, pacKey) ";
                $insert .= "values ($newordernr, '$row->artnr', $newCount, $utpris, '$beskrivning', $momssats, $row->momskod, '$art', '$curr', '$pacKey') ";
                mysqli_query($conn_master, $insert);
            }

            $arrRows[] = array(
                "ArticleNumber" => $row->artnr,
                "Description" => "- " . $beskrivning,
                "PricePerUnit" => 0,
                "NumberOfUnits" => $newCount,
                "Unit" => "st",
                "VatPercent" => 0,
                "DiscountPercent" => 0
            );


        endwhile;
        $rabattDel = ($pris - $outprice * $count); //delrabatt för de olika paketen

        $res = mysqli_query($conn_my, $select);
        $antalRader = mysqli_num_rows($res);
        $i = 0;
        $Totalt = $rabattDel; //används för att räkna ut restvärdet av rabatten till den sista artikeln
        $rabattProcent = 0;
        $rabattProcent = ($pris - ($outprice * $count)) / $pris;

        while ($row = mysqli_fetch_object($res)):
            //$marg2 = rsPaketDelInfo!utpris - rsPaketDelInfo!art_id
            $antal = $row->antal;
            if ($row->artnr == "63608367" && $fi && !$sv) {
                $select = "SELECT Artiklar.artnr, Artiklar.beskrivning, Artiklar.bestallt, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, ";
                $select .= "frakt, Artiklar.utpris, Artiklar.utpris_no, Artiklar.till_kund, Artiklar_fi.till_kund_fi, ";
                $select .= "Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, Artiklar.momskod, ";
                $select .= "Moms.momssats_fi, Moms.momssats, Moms.momssats_no, art_id, art_id_fi ";
                $select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms ";
                $select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
                $select .= " AND Artiklar.artnr = '63608367_fi'";

                $select = "SELECT Artiklar.artnr, Artiklar.lagersaldo, Artiklar.beskrivning, beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, tillverkare, ";
                $select .= "frakt, Artiklar.utpris, Artiklar.utpris_no, Artiklar.till_kund, Artiklar_fi.till_kund_fi, ";
                $select .= "Artiklar.ej_brev as ej_brev, Kategori.ej_brev as kat_ej_brev, Artiklar.momskod, ";
                $select .= "Moms.momssats_fi, Moms.momssats, Moms.momssats_no, art_id, art_id_fi ";
                $select .= " FROM Artiklar, Artiklar_fi, Tillverkare, Kategori, Moms ";
                $select .= "WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.kategori_id = Kategori.kategori_id AND Artiklar.momskod = Moms.moms_id ";
                $select .= " AND Artiklar.artnr = '63608367_fi'";
                $res3 = mysqli_query($conn_my, $select);
                $row = mysqli_fetch_object($res3);
            }

            $i += 1;

            if ($fi) {
                $marg2 = $row->utpris_fi - $row->art_id_fi;
                //echo "<p>marg2: " . $marg2 . ",utpris_fi," . $row->utpris_fi . ",art_id_fi," . $row->art_id_fi."<p>";
                // $momssats = $row->momssats_fi;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats_fi, 2);
                //echo $momssats;
                $utpris = $row->utpris_fi;
            } else if ($no) {

                $marg2 = $row->utpris_no - $row->art_id_no;
                // $momssats = $row->momssats;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats_no, 2);
                $utpris = $row->utpris_no;
                         
            } else {

                $marg2 = $row->utpris - $row->art_id;
                // $momssats = $row->momssats;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats, 2);
                $utpris = $row->utpris;
            }

            $andel = $marg2 / $marg;
            //$andelKr = $andel * $rabattDel;
            //$andelKrTot = andelKrTot + $andelKr;

            $andelKr = $rabattProcent * $utpris * $count;
            $andelKrTot = $andelKrTot + $andelKr;

            // resten hamnar på sista posten så att det inte blir något avrundningsproblem
            if ($i == $antalRader) {

                $s = "UPDATE cyberorder.Orderposter set paketRabatt = " . $Totalt . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "' AND paketArtnr='" . $pacKey . "'";
                //echo "här: " . $utpris . "," . $row->antal . "," . $Totalt . "," . $momssats;
                if ($row->momskod == 1)
                    $moms1 += round((($utpris * $antal * $count - $Totalt) * $momssats), 4);
                if ($row->momskod == 2)
                    $moms2 += round((($utpris * $antal * $count - $Totalt) * $momssats), 4);
                if ($row->momskod == 3)
                    $moms3 += round((($utpris * $antal * $count - $Totalt) * $momssats), 4);
                if ($row->momskod == 4)
                    $moms4 += round((($utpris * $antal * $count - $Totalt) * $momssats), 4);
            } else {

                $s = "UPDATE cyberorder.Orderposter set paketRabatt = " . $andelKr . " + paketRabatt WHERE ordernr = " . $newordernr . " AND artnr = '" . $row->artnr . "' AND paketArtnr='" . $pacKey . "'";
                if ($row->momskod == 1)
                    $moms1 += round((($utpris * $antal * $count - $andelKr) * $momssats), 4);
                if ($row->momskod == 2)
                    $moms2 += round((($utpris * $antal * $count - $andelKr) * $momssats), 4);
                if ($row->momskod == 3)
                    $moms3 += round((($utpris * $antal * $count - $andelKr) * $momssats), 4);
                if ($row->momskod == 4)
                    $moms4 += round((($utpris * $antal * $count - $andelKr) * $momssats), 4);
            }

            if (!$arrayOnly) {
                mysqli_query($conn_master, $s);
            }
            $Totalt = $Totalt - $andelKr;
        endwhile;

        // Titta efter hur mycket rabatten blir i paketet mot enskilda delar samt
        // gångra med antal paket
        //echo "här: " . $totalpacutpris . "," . $outprice . "," . $count;

        $rabpp = $totalpacutpris - $outprice;
        $rabppp = round($rabpp / $totalpacutpris * 100, 2);

        $arrRows[0]["PricePerUnit"] = $outprice;
        $arrRows[0]["DiscountPercent"] = 0;

        // below if show discount
        //$arrRows[0]["PricePerUnit"] = $totalpacutpris;
        //$arrRows[0]["DiscountPercent"] = $rabppp;

        $ny_rabatt = ($totalpacutpris - $outprice) * $count;
        $rabattkoll = "SELECT rabatt, kommentar FROM cyberorder.Ordertabell WHERE ordernr = $newordernr";
        $res = mysqli_query($conn_master, $rabattkoll);
        $row3 = mysqli_fetch_object($res);
        $old_rabatt = $row3->rabatt;
        $oldkommentar = $row3->kommentar;

        if ($discount) {
            if ($oldkommentar != "")
                $newkommentar = $oldkommentar . "\r\n" . $extraBeskrivningText;
            else
                $newkommentar = $extraBeskrivningText;
        }
        $discount = false;


        // Lägg ihop existerande rabatt med nya paketrabatten och uppdatera tabellen.
        $tot_rabatt = $old_rabatt + $ny_rabatt;
        if ($newkommentar != "")
            $insert_rabatt = "UPDATE cyberorder.Ordertabell SET paketRabatt = paketRabatt + " . $ny_rabatt . ", kommentar = '" . $newkommentar . "'" . " WHERE ordernr = $newordernr";
        else
            $insert_rabatt = "UPDATE cyberorder.Ordertabell SET paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = $newordernr";

        if (!$arrayOnly) {
            mysqli_query($conn_master, $insert_rabatt);
        }
    }
    // -----------------------------------------------------------
    // end pac
    else { // om inte paket

        if ($frakt > 0 && !($butiksfrakt) && !$bask->freeFreight) {
            // lägg bara på extra frakt om ingen annan produkt har större
            if ($frakt > $extra_frakt) {
                $extra_frakt = $frakt;
            }
        }

        // skapa presentort om det är ett sådant
        if (eregi("presentkort", $artnr)) {
            $date = date("Y-m-d H:i:s");
            $receiver = "";
            ereg("(presentkort)([0-9]+)", $artnr, $matchess);
            $i = $matchess[2];
            //echo "här: " . $i;
            $outprice = $_SESSION['giftCard'][$i];
            $utpris = $outprice;
            $receiver = $_SESSION['giftCardReceiver'][$i];
            newGiftCard($newordernr, $outprice, $date, $artnr, $receiver);
            $goodsvalue+=($outprice * $count);
            //$i += 1;	
        }



        // Om produkten är någon form av frakt så bokas den automatiskt upp direkt.
        if (eregi("frakt", $artnr) || (eregi("presentkort", $artnr)) || (eregi("invoicefee", $artnr)) || (eregi("forsakring", $artnr)) || $row->kategori_id == 595 || $row->kategori_id == 629 || $row->kategori_id == 630) {
            $bokad = 1;
        } else {
            
        }


        if ($bask->prec == "")
            $bask->prec = 0;


        $utpris = round($utpris, 2);
        // Lägg till artikeln till ordern

        if ($discount) {
            $insert = "insert into cyberorder.Orderposter (ordernr, artnr, antal, utpris, beskrivning, momssats, momskod, discountCode, utprisNormal, currency, pacKey) ";
            // $insert .= "values ($newordernr, '$artnr', $count, $utpris, '$beskrivning $extraBeskrivningText', $row->momssats, $row->momskod, '$discountCode', $utprisNormal, '$curr', '$pacKey') ";		  			  			
            $insert .= "values ($newordernr, '$artnr', $count, $utpris, '$beskrivning $extraBeskrivningText', $momssats, $row->momskod, '$discountCode', $utprisNormal, '$curr', '$pacKey') ";
        } else {
            $insert = "insert into cyberorder.Orderposter (ordernr, artnr, antal, utpris, beskrivning, momssats, momskod, utprisNormal, currency, beskrivning_alt, isHidden, visualPrice, pacKey) ";
            // $insert .= "values ($newordernr, '$artnr', $count, $utpris,'$beskrivning $extraBeskrivningText', $row->momssats, $row->momskod, $utprisNormal, '$curr', '$beskrivning_alt', '$isHidden', '$visualPrice', '$pacKey') ";
            $insert .= "values ($newordernr, '$artnr', $count, $utpris,'$beskrivning $extraBeskrivningText', $momssats, $row->momskod, $utprisNormal, '$curr', '$beskrivning_alt', '$isHidden', '$visualPrice', '$pacKey') ";
        }
        
        $arrRows[] = Array(
            "ArticleNumber" => $row->artnr,
            "Description" => $beskrivning . " " . $extraBeskrivningText,
            "PricePerUnit" => $utpris,
            "NumberOfUnits" => $count,
            "Unit" => "st",
            "VatPercent" => $momssats * 100,
            "DiscountPercent" => 0
        );
        if ($_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
            //echo $insert . "<br>";
        }
        //echo "här: " . $conn_master;
        //exit;
        if ($artnr != "" && !$arrayOnly) {
            //echo "yes, including " . $insert . " : " . $conn_master;
            mysqli_query($conn_master, $insert);
        }


        // om det var en inkluderad artikel så skall det rabatteras lika mycket som posten kostade samt en kommentar om det. 
        if ($includedArticle && $sjabo == "ejmer") {
            $rabattkoll = "SELECT rabatt, kommentar FROM cyberorder.Ordertabell WHERE ordernr = $newordernr";
            $res = mysqli_query($conn_master, $rabattkoll);
            $row3 = mysqli_fetch_object($res);
            $old_rabatt = $row3->rabatt;
            $oldkommentar = $row3->kommentar;

            $ny_rabatt = $outprice * $count;
            $name = $beskrivning;
            if ($extraBeskrivningText != "")
                ;
            $name .= " " . $extraBeskrivningText;

            if ($fi) {
                // $momssats = $row->momssats_fi;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats_fi, 2);
            } else {

                // $momssats = $row->momssats;
                if ($noTax)
                    $momssats = 0;
                else
                    $momssats = round($row->momssats, 2);
            }

            $rabattTxt = number_format($ny_rabatt, 0, ',', ' ') . " " . $curr;

            // Titta om "rabatt ord" redan är inlagda, och lägg till text utefter det.
            if ($fi) {
                if ($sv) {
                    if (eregi("Rabatten avser att", $oldkommentar)) {
                        $newkommentar = $oldkommentar . ", samt $name: " . $rabattTxt;
                    } else {
                        $newkommentar = "" . $oldkommentar . "Rabatten avser att $name är inkluderad på köpet: " . $rabattTxt;
                    }
                } else {
                    // TODO: översättning till finska här
                    if (eregi("Alennuksessa on huomioitu että", $oldkommentar)) {
                        $newkommentar = $oldkommentar . ", sekä $name: " . $rabattTxt;
                    } else {
                        $newkommentar = "" . $oldkommentar . "Alennuksessa on huomioitu että $name tulee kaupan päälle: " . $rabattTxt;
                    }
                }
            } else {
                if (eregi("Rabatten avser att", $oldkommentar)) {
                    $newkommentar = $oldkommentar . ", samt $name: " . $rabattTxt;
                } else {
                    $newkommentar = "" . $oldkommentar . "Rabatten avser att $name är inkluderad på köpet: " . $rabattTxt;
                }
            }

            /**
              $extraBeskrivningText = "Rabatten avser "; ?
              if ($oldkommentar != "")
              $newkommentar = $oldkommentar . "\r\n" . $extraBeskrivningText;
              else
              $newkommentar = $extraBeskrivningText;

             */
            // Lägg ihop existerande rabatt med nya paketrabatten och uppdatera tabellen.
            //$tot_rabatt = $old_rabatt + $ny_rabatt;
            //$insert_rabatt = "UPDATE Ordertabell SET kommentar = '$newkommentar', paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = '$newordernr'";
            //if ($fi) {
            //$insert_rabatt = "UPDATE Ordertabell_fi SET paketRabatt = paketRabatt + " . $ny_rabatt . " WHERE ordernr = $newordernr";			
            //} else {
            if ($newkommentar != "")
                $insert_rabatt = "UPDATE cyberorder.Ordertabell SET rabatt = rabatt + " . $ny_rabatt . ", kommentar = '" . $newkommentar . "'" . " WHERE ordernr = $newordernr";
            else
                $insert_rabatt = "UPDATE cyberorder.Ordertabell SET rabatt = rabatt + " . $ny_rabatt . " WHERE ordernr = $newordernr";
            //}
            if (!$arrayOnly) {
                mysqli_query($conn_master, $insert_rabatt);
            }
            // ovanstående påverkar inte momsen alls då det tar ut varandra, därav läggs inget in i momsen nedan. 
        } else {

            if ($row->momskod == 1)
                $moms1 += ($momssats * $utpris * $count);
            elseif ($row->momskod == 2)
                $moms2 += ($momssats * $utpris * $count);
            elseif ($row->momskod == 3)
                $moms3 += ($momssats * $utpris * $count);
            elseif ($row->momskod == 4)
                $moms4 += ($momssats * $utpris * $count);
        }
    }
    return $arrRows;
}

function verifyArtnr($artnr) {

    $select = "SELECT checkArtnr FROM OrderCheck WHERE checkArtnr = '" . $artnr . "' AND checkFrom < NOW() AND CheckTo > NOW() ";

    $res = mysqli_query($select);

    if (mysqli_num_rows($res) > 0) {

        return true;
    } else {

        return false;
    }
}

function sendMailToRecipient($artnr, $ordernr) {

    $orderdatum = date("j/n-Y H:i", time());

    $addcreatedby = "webmaster";

    $select = "SELECT checkRecipient FROM OrderCheck WHERE checkArtnr = '" . $artnr . "' AND checkFrom < NOW() AND CheckTo > NOW() ";

    $res = mysqli_query($select);

    while ($row = mysqli_fetch_array($res)):

        extract($row);

        $recipient .= " " . $checkRecipient;

    endwhile;


    $subj = $orderdatum . " Bevakning av artikel " . $artnr;

    $extra = "From: " . $addcreatedby;

    $text1 .= "Registrerad på order nr: " . $ordernr . "\n\n";
    $text1 .= "Vänligen vidta de åtgärder som du önskar göra i affärssystemet.\n\n";

    mail($recipient, $subj, $text1, $extra);
}

function mailaSlutet() {

    global $messageHtml, $messageText, $message, $headers, $subject, $to, $orderrow, $orderrecipient;
// Avslut i mail meddelandet
    $messageHtml .= <<<EOF

</center>
<tr>
  <td height="15" colspan="5"><font face="Verdana, Arial" size="1">Övrig information<p>Som 
  privatperson har du 14 dagar på dig att ångra ditt köp efter du mottagit 
  varan. Varan ska vara i oförändrat skick och förpackningen ska vara oskadad 
  och obruten.</p>
  <p>Vid eventuell retur, så måste du kontakta oss först för att få ett 
  returnummer. Det är ditt ansvar att varan och dess originalförpackning kommer 
  tillbaka till oss i oskadat skick. Se därför till att varan emballeras väl. Om 
  något skulle hända med varan trots att den emballerats på ett 
  tillfredställande sätt, så är det posten som är ersättningsskyldig. Returen är 
  godkänd först när vi kontrollerat varan. Vid godkänd retur så återbetalas 
  summan inom kort. Vid ej godkänd retur så returneras varan och frakt 
  debiteras.</p>
  <p>Vill du åberopa garantireparation gäller följande:<br>
  Kontakta oss så kan vi hänvisa till lokal auktoriserad verkstad och om det 
  inte finns, så sänder vi en paketavi så att ni kostnadsfritt kan sända kameran 
  till auktoriserad verkstad. Kostnadsfri paketavi gäller inom Sverige. Om du 
  måste sända din kamera så tänk på att kameran har skyddspåse eller liknande i 
  sin kartong så att kameran inte repas och att du har en ytterkartong runt 
  kamerans egen kartong.</font></td>
</tr>
<tr>
  <td height="15" colspan="5">
  <p></p>
  <p></p>
  <p><br>
  <a href="http://www.cyberphoto.se">
  <img border="0" src="cid:logo.gif" width="450" height="95"></a></td>
</tr>
</table>

</td>
      </tr>
      <tr>
        <td></td>
        <td align="right"></td>
      </tr>
      <tr>
        <td></td>
        <td align="right"></td>
      </tr>
    </table>
    </td>
  </tr>
</table>
</div>

</body>
</html>

EOF;

    $messageText .= <<<EOF


---------------------------------------------------------------
Information

Som privatperson har du 14 dagar på dig att ångra ditt köp efter du
mottagit varan. Varan ska vara i oförändrat skick och förpackningen
ska vara oskadad och obruten.

Vid eventuell retur, så måste du kontakta oss först för att få ett
returnummer. Det är ditt ansvar att varan och dess originalförpackning
kommer tillbaka till oss i oskadat skick. Se därför till att varan
emballeras väl. Om något skulle hända med varan trots att den emballerats
på ett tillfredställande sätt, så är det posten som är ersättningsskyldig.
Returen är godkänd först när vi kontrollerat varan. Vid godkänd retur så
återbetalas summan inom kort. Vid ej godkänd retur så returneras varan
och frakt debiteras.

Vill du åberopa garantireparation gäller följande:
Kontakta oss så kan vi hänvisa till lokal auktoriserad verkstad och om
det inte finns, så sänder vi en paketavi så att ni kostnadsfritt kan
sända kameran till auktoriserad verkstad. Kostnadsfri paketavi gäller
inom Sverige. Om du måste sända din kamera så tänk på att kameran har
skyddspåse eller liknande i sin kartong så att kameran inte repas och
att du har en ytterkartong runt kamerans egen kartong.

EOF;


    $message .= "------=MIME_BOUNDRY_main_message\n";
    $message .= "Content-Type: text/plain;\n\tcharset=\"iso-8859-1\"\n";
    $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
    $message .= $messageText . "\n\n";

    $message .= "------=MIME_BOUNDRY_main_message\n";
    //$message .= "Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\n"; 
    //$message .= "Content-Transfer-Encoding: quoted-printable\n\n"; 
    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";

    $message .= $messageHtml;


//echo $messageText;
//$message  .= $messageText . "\n\n" . $messageHtml . "";
    /**
      $message .= "\n------=MIME_BOUNDRY_main_message\n";
      $message .= "Content-Type: image/gif; \n name=\"logo.gif\"\n";
      $message .= "Content-Transfer-Encoding: base64\n";
      //$message .= "Content-Disposition: inline; filename=\"logo.gif\"\n";
      $message .= "Content-ID: <loggan>\n\n";


      $file = "/home/www/10.gif";
      $fp = fopen($file, "rb");
      $content = fread($fp, filesize($file));

      $message .= chunk_split(base64_encode($content));

      $message .= "\n";

     */
    $message .= "\n------=MIME_BOUNDRY_main_message--\n\n";





    if (isset($to)) {
        require_once("class.phpmailer.php");
        //echo "här: " . $orderrow->email;


        $mail = new phpmailer();

        $mail->From = "order@cyberphoto.se";
        $mail->FromName = "CyberPhoto";
        $mail->Host = "81.8.240.27";
        //$mail->Mailer   = "smtp";

        $mail->AddAddress($orderrow->email, $orderrow->namn);
        $mail->Body = $messageHtml;
        //$mail->AltBody = $messageText;

        $mail->Subject = "Preliminär orderbekräftelse för " . $orderrow->namn;
        $subject = "Preliminär orderbekräftelse för " . $orderrow->namn;

        $mail->IsHTML(true);
        $mail->IsSMTP(true);
        $mail->AddEmbeddedImage("/home/www/logo.gif", "logo.gif", "logo.gif", "base64", "image/gif");

        $mail->AddEmbeddedImage("/home/www/vertline.gif", "vertline.gif", "vertline.gif", "base64", "image/gif");
        $mail->AddEmbeddedImage("/home/www/dotv.gif", "dotv.gif", "dotv.gif", "base64", "image/gif");

        $mail->AddEmbeddedImage("/home/www/dotsv.gif", "dotsv.gif", "dotsv.gif", "base64", "image/gif");


        if (!$mail->Send())
            echo "Orderbekräftelse kunden inte mailas iväg";

        // Clear all addresses and attachments for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();

        // kopia till oss
        $ordermessage = "Kommentar: \n" . $orderrow->kommentarKund . "\n\n" . $messageText;
        $headers = preg_replace("/(To:){1}(.)*(\n){1}/", "", $headers); // används inte men lät den stå kvar. Tar bort raden med "To" från headers

        $headers2 = "From: CyberPhoto <order@cyberphoto.se>\n";

        mail("$orderrecipient", "$subject", "$ordermessage", "$headers2");
    }
# ordna så att mailen inte skickas igen om någon laddar om sidan
    unset($to, $messageHtml, $headers, $subject);
}

function process_orderrowslight($show) {
    global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow, $fi,
    $bestallningsgrans, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $bask,
    $count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue,
    $est_delivery, $fi, $sv, $no, $bask, $conn_standard, $conn_my, $conn_ms, $conn_master;

    $betalsatt_id = $orderrow->betalsatt_id;

    // Artikel rubrik i mail meddelande
    if ($fi)
        $messageText .= "\nlukumäärä\t  Tuote  \t\t\t\t\t   sisällä \n\n";
    else
        $messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

    $stack = array();
    // kolla här om det finns några värdepaket
    $select = "SELECT DISTINCT Orderposter.pacKey ";
    $select .= "FROM cyberorder.Orderposter ";
    $select .= "WHERE ordernr = " . $ordernrladdaom . " AND NOT (Orderposter.pacKey = '') ";
    // echo $select;
    // exit;
    $res = mysqli_query($conn_master, $select);

    if (mysqli_num_rows($res) > 0) {

        while ($row = mysqli_fetch_object($res)):

            array_push($stack, $row->pacKey);

        endwhile;
    }

    foreach ($stack as $value) {
        // echo $value . "<br />";
        echo "<tr>\n";
        echo "\t<td colspan=\"3\" class=\"align_left bold italic\">" . displayPacName($value) . "</td>\n";
        echo "\t<td class=\"align_right\">" . displayPricePac($ordernrladdaom, $value) . "</td>\n";
        echo "</tr>\n";
        viewOrderLinesInPac($ordernrladdaom, $value);
    }


    // Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
    $itemsselect = "SELECT Orderposter.artnr, Orderposter.utpris, Orderposter.levDatum, Orderposter.bokad, Orderposter.antal, Orderposter.momssats, Orderposter.beskrivning, Orderposter.beskrivning_alt, Orderposter.visualPrice ";

    $itemsselect .= " ";
    $itemsselect .= " FROM cyberorder.Orderposter WHERE Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') AND NOT (isHidden = -1)  ";
    foreach ($stack as $value) {
        $itemsselect .= " AND NOT pacKey = '$value'";
    }

    $itemres = mysqli_query($conn_master, $itemsselect);


    while ($row = mysqli_fetch_object($itemres)) :

        $val = Locs::getCurrency();
        $momssats = $row->momssats;
        $beskrivning_alt = $row->beskrivning_alt;
        $beskrivning = $row->beskrivning;

        $count = $row->antal;
        $levDatum = $bask->check_lager_bask_se($row);

        if (strlen($beskrivning_alt) > 2) {
            $benamning = $beskrivning_alt;
        } else {
            $benamning = $beskrivning;
        }
        // måste plocka tillverkare från mysql, det är inte säkert det finns i mssql längre
        $s_tillverkare = "SELECT tillverkare FROM Tillverkare, Artiklar  WHERE Tillverkare.tillverkar_id = Artiklar.tillverkar_id AND Artiklar.artnr = '" . $row->artnr . "'";

        $r = mysqli_query($conn_my, $s_tillverkare);
        $rw = mysqli_fetch_object($r);


        if ($rw->tillverkare != '.' && strlen($beskrivning_alt) < 2)
            $benamning = $rw->tillverkare . " " . $benamning;

        if ($show) {
            ?>
            <tr>
                <td class="align_left"><?php echo $count; ?> st</td>
                <td class="align_left"><?php echo $benamning; ?></td>
                <td class="align_left">&nbsp;</td>
                <?php if ($row->visualPrice > 0) { ?>
                    <td class="align_right"><?php echo number_format(($row->visualPrice + $row->visualPrice * $momssats) * $row->antal, 0, ',', ' ') . " " . $val; ?></td>
                <?php } elseif ($row->kategori_id == 629) { ?>
                    <td class="align_right">&nbsp;</td>
                <?php } else { ?>
                    <td class="align_right"><?php echo number_format(($row->utpris + $row->utpris * $momssats) * $row->antal, 0, ',', ' ') . " " . $val; ?></td>
                <?php } ?>
            </tr>
            <?php
        }

    endwhile;

    // Sen plocka fram frakten (så att det hamnar sist)
    $itemsselect = "SELECT Orderposter.artnr, Orderposter.beskrivning, Orderposter.utpris, Orderposter.levDatum, Orderposter.bokad, Orderposter.antal, Orderposter.momssats ";
    $itemsselect .= " FROM cyberorder.Orderposter WHERE Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";

    $itemres = mysqli_query($conn_master, $itemsselect);

    while ($row = mysqli_fetch_object($itemres)) :
        if ($fi) {
            $val = "EUR";
            $momssats = $row->momssats;
        } elseif ($no) {
            $val = "NOK";
            $momssats = $row->momssats;
        } else {
            $val = "SEK";
            $momssats = $row->momssats;
        }
        $beskrivning = $row->beskrivning;
        # If manufacturer is a single dot, set it to ''
        $benamning = $beskrivning;

        // måste plocka tillverkare från mysql, det är inte säkert det finns i mssql längre
        $s_tillverkare = "SELECT tillverkare FROM Tillverkare, Artiklar  WHERE Tillverkare.tillverkar_id = Artiklar.tillverkar_id AND Artiklar.artnr = '" . $row->artnr . "'";
        $r = mysqli_query($conn_my, $s_tillverkare);
        $rw = mysqli_fetch_object($r);
        if ($rw->tillverkare != '.')
            $benamning = $row->tillverkare . " " . $benamning;

        if ($show) {
            ?>

            <tr>
                <td class="align_left">1 st</td>
                <td class="align_left"><?php echo $benamning; ?></td>
                <td>&nbsp;</td>
                <td class="align_right"><?php echo number_format(($row->utpris + $row->utpris * $momssats) * $row->antal, 0, ',', ' ') . " " . $val; ?></td>
            </tr>

            <?php
        }

    endwhile;

    // if ($orderrow->paketRabatt > 0) { 
    if ($orderrow->paketRabatt == "tjosan") { // nu visar vi inte paketrabatten längre....
        if ($fi && !$sv) {
            $Paketrabatt = "Pakettialennus";
        } elseif ($no) {
            $Paketrabatt = "Pakkerabatt";
        } else {
            $Paketrabatt = "Paketrabatt";
        }

        if ($fi) {
            // $momssats = 0.22;
            $val = "EUR";
        } else {
            // $momssats = 0.25;
            $val = "kr";
        }
        ?>
        <?php if ($show) { ?>
            <tr>
                <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
                <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
                <td align="right"><font face="Verdana, Arial" size="1"><% echo $Paketrabatt; %></font></td>
                <td align="right"><font face="Verdana, Arial" size="1">-&nbsp;<?php echo number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
            </tr>
            <?php
        }
    }
    if ($orderrow->rabatt > 0) {
        if ($fi && !$sv) {
            $Paketrabatt = "Pakettialennus";
        } elseif ($no) {
            $Paketrabatt = "Øvrig rabatt";
        } else {
            $Paketrabatt = "Övrig rabatt";
        }

        if ($fi) {
            // $momssats = 0.22;
            $val = "EUR";
        } elseif ($no) {
            // $momssats = 0.22;
            $val = "NOK";
        } else {
            // $momssats = 0.25;
            $val = "SEK";
        }
        ?>
        <?php if ($show) { ?>
            <tr>
                <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
                <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
                <td align="right"><font face="Verdana, Arial" size="1"><% echo $Paketrabatt; %></font></td>
                <td align="right"><font face="Verdana, Arial" size="1">-&nbsp;<?php echo number_format(($orderrow->rabatt * $momssats) + $orderrow->rabatt, 0, ',', ' ') . " " . $val; ?></font></td>
            </tr>
            <?php
        }
    }
}

function displayPacName($artnr) {
    global $fi, $sv, $conn_my, $conn_ms;

    $select = "SELECT CONCAT(Tillverkare.tillverkare, ' ', Artiklar.beskrivning) AS PacBeskrivning, CONCAT(Tillverkare.tillverkare, ' ', Artiklar_fi.beskrivning_fi) AS PacBeskrivning_fi ";
    $select .= "FROM Artiklar ";
    $select .= "JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi ";
    $select .= "JOIN Tillverkare ON Artiklar.tillverkar_id = Tillverkare.tillverkar_id ";
    $select .= "WHERE artnr = '" . $artnr . "' ";
    // echo $select;
    // exit;
    $res = mysqli_query($conn_my, $select);

    if (mysqli_num_rows($res) > 0) {

        while ($row = mysqli_fetch_object($res)):

            if ($fi && !$sv) {
                return $row->PacBeskrivning_fi;
            } else {
                return $row->PacBeskrivning;
            }

        endwhile;
    }
}

function viewOrderLinesInPac($ordernr, $artnrpac) {
    global $fi, $sv, $conn_my, $conn_ms, $conn_master;

    $select = "SELECT artnr, antal, beskrivning ";
    $select .= "FROM cyberorder.Orderposter ";
    // $select .= "WHERE ordernr = '" . $ordernr . "' AND paketArtnr = '" . $artnrpac . "'";
    $select .= "WHERE ordernr = '" . $ordernr . "' AND pacKey = '" . $artnrpac . "'";
    // echo $select;
    // exit;

    $res = mysqli_query($conn_master, $select);

    while ($row = mysqli_fetch_object($res)) {

        $description = "";

        // måste plocka tillverkare från mysql, det är inte säkert det finns i mssql längre
        $s_tillverkare = "SELECT tillverkare FROM Tillverkare, Artiklar  WHERE Tillverkare.tillverkar_id = Artiklar.tillverkar_id AND Artiklar.artnr = '" . $row->artnr . "'";

        $r = mysqli_query($conn_my, $s_tillverkare);
        $rw = mysqli_fetch_object($r);

        if ($rw->tillverkare != '.')
            $description = $rw->tillverkare . " ";

        $description .= $row->beskrivning;

        echo "<tr>\n";
        echo "\t<td class=\"align_right\">" . $row->antal . " st</td>\n";
        echo "\t<td class=\"align_left\">&nbsp;&nbsp;-&nbsp;" . $description . "</td>\n";
        echo "\t<td class=\"align_left\">&nbsp;</td>\n";
        echo "\t<td class=\"align_left\">&nbsp;</td>\n";
        echo "</tr>\n";
    }
}

function displayPricePac($ordernr, $artnrpac) {
    global $fi, $sv, $no, $conn_my, $conn_ms, $conn_master;

    $totalsumpac = 0;
    if ($fi) {
        $valuta = " EUR";
    } elseif ($no) {
        $valuta = " NOK";
    } else {
        $valuta = " SEK";
    }

    $select = "SELECT Orderposter.artnr, Orderposter.antal, Orderposter.utpris, Orderposter.momssats, Orderposter.paketRabatt ";
    $select .= "FROM cyberorder.Orderposter ";
    $select .= "JOIN cyberphoto.Moms ON Orderposter.momskod = Moms.moms_id ";
    // $select .= "WHERE ordernr = '" . $ordernr . "' AND paketArtnr = '" . $artnrpac . "'";
    $select .= "WHERE ordernr = '" . $ordernr . "' AND pacKey = '" . $artnrpac . "'";
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
		echo $select;
		exit;
	}
    $res = mysqli_query($conn_master, $select);

    while ($row = mysqli_fetch_object($res)) {

        $totalsumpac += ($row->antal * ($row->utpris - $row->paketRabatt)) + (($row->antal * ($row->utpris - $row->paketRabatt)) * $row->momssats);
    }

    if ($totalsumpac == 0 && $artnrpac == "abbpkt") {
        return 0 . $valuta;
    } elseif ($totalsumpac < 1 && $artnrpac == "abbpkt") {
        return 1 . $valuta;
	} elseif ($totalsumpac < 1) {
        return 0 . $valuta;
    } else {
        return round($totalsumpac, 0) . $valuta;
    }
}

function process_orderrows($show) {
    global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow, $fi,
    $bestallningsgrans, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $bask,
    $count, $lagersaldo, $lagersaldo_fi, $bestallt, $bestallt_fi, $lev_datum, $lev_datum_fi, $lev_datum_norm, $lev_datum_norm_fi, $package_stock, $bestallningsgrans, $bestallningsgrans_fi, $queue,
    $est_delivery, $fi, $sv, $fi, $sv, $bask, $conn_standard, $conn_ms, $conn_my, $conn_master;


    $betalsatt_id = $orderrow->betalsatt_id;

    // Artikel rubrik i mail meddelande
    if ($fi)
        $messageText .= "\nlukumäärä\t  Tuote  \t\t\t\t\t   sisällä \n\n";
    else
        $messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

    // Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
    //if ($fi)
    //	$itemsselect  = "SELECT Orderposter_fi.artnr, Orderposter_fi.utpris, Orderposter_fi.levDatum, Orderposter_fi.bokad, Orderposter_fi.antal, Moms.momssats_fi as momssats, Orderposter_fi.beskrivning,   ";
    //else 
    $itemsselect = "SELECT Orderposter.artnr, Orderposter.utpris, Orderposter.levDatum, Orderposter.bokad, Orderposter.rest, Orderposter.antal, Orderposter.momssats, Orderposter.beskrivning, Orderposter.beskrivning_alt, Orderposter.visualPrice ";

    //$itemsselect .= "tillverkare, lagersaldo, lagersaldo_fi, bestallt, bestallt_fi, releaseDate as rel, lev_datum_fi, lev_datum, bestallningsgrans, bestallningsgrans_fi, lev_datum_norm, lev_datum_norm_fi, kategori_id  ";
    //if ($fi) {	
    //	$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";
    //} else {
    $itemsselect .= " FROM cyberorder.Orderposter WHERE Orderposter.ordernr = $ordernrladdaom AND NOT (Orderposter.artnr like 'frakt%' OR Orderposter.artnr = 'invoicefee') AND NOT (isHidden = -1) ";
    //}
    //$itemsselect .= " AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod ";
    //echo $itemsselect;
    /**
      if ($fi) {
      $itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar.beskrivning,Artiklar_fi.beskrivning_fi, Orderposter_fi.utpris, tillverkare, antal, Moms.momssats_fi as momssats, Artiklar.lev_datum, Artiklar.lev_datum_fi,  ";
      $itemsselect .= "Orderposter_fi.levDatum, Orderposter_fi.bokad, Artiklar_fi.bestallningsgrans_fi as bestallningsgrans, Artiklar_fi.lagersaldo_fi as lagersaldo, Artiklar_fi.lev_datum_norm_fi ";
      $itemsselect .= "FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.momskod = Moms.moms_id AND Orderposter_fi.artnr = Artiklar.artnr AND ";
      $itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
      $itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";

      } else {
      $itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, Moms.momssats, Artiklar.lev_datum, Artiklar.lev_datum_norm,  ";
      $itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal ";
      $itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Artiklar.momskod = Moms.moms_id AND Orderposter.artnr = Artiklar.artnr AND ";
      $itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND ";
      $itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";
      }
     */
    //echo $itemsselect;
    $itemres = mysqli_query($conn_master, $itemsselect);

    while ($row = mysqli_fetch_object($itemres)) :
        /**
          if ($fi) {
          $val = "EUR";
          $momssats = $row->momssats_fi;

          if ($sv || $row->beskrivning_fi == "")
          $beskrivning = $row->beskrivning;
          else
          $beskrivning = $row->beskrivning_fi;
          } else {
          $val = "SEK";
          $momssats = $row->momssats;
          $beskrivning = $row->beskrivning;
          }
         */
        if ($fi) {
            $val = "EUR";
            //$momssats = $row->momssats_fi;
        } else {
            $val = "kr";
            //$momssats = $row->momssats;
        }
        $momssats = $row->momssats;
        $beskrivning_alt = $row->beskrivning_alt;
        $beskrivning = $row->beskrivning;

        $count = $row->antal;
        //if ($fi)
        //	$levDatum = $bask->check_lager_bask_fi($row);
        //else 
        /**
          if ($row->kategori_id == 629) {
          $levDatum = "&nbsp;";
          } else {
          $levDatum = $bask->check_lager_bask_se($row);
          }
         */
        // hm, vi skickar nog inte orderbekräftelser i efterhand längre på det hrä sättet. Så det här får nog ingen funktionlängre. Värdena finns inte längre heller
        /**
          if ($count < 1) { // detta är endast till för bekräftelser som vi skickar i efterhand. Ex. när vi gjort restorder SS

          if ($row->bokad == 1) {
          if ($row->rest == 1) {
          if (!$sv)
          $levDatum = "<font size=\"1\" face=\"Verdana, Arial\" color=\"#000000\">Jälkitoimitus, lähetetään myöhemmin</font>";
          else
          $levDatum = "<font size=\"1\" face=\"Verdana, Arial\" color=\"#000000\">Restnoterad, skickas senare</font>";
          }
          else {
          if (!$sv)
          $levDatum = "Valmis";
          else
          $levDatum = "Klar";
          }
          }

          elseif ($row->bokad == 2) {
          if (!$sv)
          $levDatum = "Valmis (lähetetään suoraan meidän toimittajaltamme";
          else
          $levDatum = "Klar (skickas direkt från vår leverantör)";
          }

          elseif ($bokad == 4) {
          if (!$sv)
          $levDatum = "<font color=\"#85000D\">Tuote on poistunut! Ystävällisesti <a href=\"mailto:info@cyberphoto.fi?subject=Poistunut tuote ($artnr) $beskrivning tilaus $ordernr\"><u><font color=\"#85000D\">ota yhteyttä meihinför</font></u></a> vaihtoehtoihiin</font>";
          else
          $levDatum = "<font color=\"#85000D\">Produkten är utgången! Vänligen <a href=\"mailto:produkt@cyberphoto.se?subject=Utgången produkt ($artnr) $beskrivning på order $ordernr\"><u><font color=\"#85000D\">kontakta oss</font></u></a> för alterantiv</font>";

          }

          elseif ($row->kategori_id == 629) {
          if (!$sv)
          $levDatum = "<font color=\"#85000D\">&nbsp;</font>";
          else
          $levDatum = "<font color=\"#85000D\">&nbsp;</font>";

          } else {

          $levDatum = $bask->check_lager_bask_se($row);

          }
          }
         */
        # If manufacturer is a single dot, set it to ''
        if (strlen($beskrivning_alt) > 2) {
            $benamning = $beskrivning_alt;
        } else {
            $benamning = $beskrivning;
        }
        // $benamning = $beskrivning;
        // måste plocka tillverkare från mysql, det är inte säkert det finns i mssql längre
        $s_tillverkare = "SELECT tillverkare FROM Tillverkare, Artiklar  WHERE Tillverkare.tillverkar_id = Artiklar.tillverkar_id AND Artiklar.artnr = '" . $row->artnr . "'";
        $r = mysqli_query($conn_my, $s_tillverkare);
        $rw = mysqli_fetch_object($r);

        if ($rw->tillverkare != '.' && strlen($beskrivning_alt) < 2)
            $benamning = $rw->tillverkare . " " . $benamning;

        if ($show) {
            ?>
            <tr><td bgcolor="#ECECE6"><small><font face="Verdana, Arial" size="1">
                        <?php echo $benamning; ?>
                    </font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo $count; ?> st</font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
                    <?php echo number_format($row->utpris * $row->antal, 0, ',', ' ') . " " . $val; ?></font></td>
                <td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
                    <?php echo number_format(($row->utpris + $row->utpris * $momssats) * $row->antal, 0, ',', ' ') . " " . $val; ?>
                    </font></td>
                <?php
                print "</tr>\n";
            }

            $messageHtml .= "\n<tr>";
            $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>\n";
            $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>\n";
            /*
              $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
              if ($row->visualPrice > 0) {
              $messageHtml .= number_format($row->visualPrice * $row->antal, 0, ',', ' ') . " " . $val;
              } else {
              $messageHtml .= number_format($row->utpris * $row->antal, 0, ',', ' ') . " " . $val;
              }
              $messagehtml .= "</td>\n";
             */
            $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
            if ($row->visualPrice > 0) {
                $messageHtml .= number_format(($row->visualPrice + $row->visualPrice * $momssats) * $row->antal, 0, ',', ' ') . " " . $val;
            } elseif ($row->kategori_id == 629) {
                $messageHtml .= "&nbsp;";
            } else {
                $messageHtml .= number_format(($row->utpris + $row->utpris * $momssats) * $row->antal, 0, ',', ' ') . " " . $val;
            }
            $messagehtml .= "</td>\n";

            // $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\">" . $levDatum . "</td>\n";
            $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\">&nbsp;</td>\n";
            $messageHtml .= "</tr>";

            if (mb_strlen($benamning) >= '30')
                $benamning = mb_substr($benamning, 0, 30) . "...";

            $messageText .= "  $count   ";

            $messageText .= sprintf("%-35s", $benamning);
            $messageText .= number_format(($row->utpris + $row->utpris * $momssats) * $row->antal, 0, ',', ' ') . " " . $val;
            $messageText .= "\n";

        endwhile;

        // Sen plocka fram frakten (så att det hamnar sist)
        //if ($fi)
        //	$itemsselect  = "SELECT Orderposter_fi.artnr, Orderposter_fi.utpris, Orderposter_fi.levDatum, Orderposter_fi.bokad, Orderposter_fi.antal, Moms.momssats_fi as momssats, Orderposter_fi.beskrivning ";
        //else 
        $itemsselect = "SELECT Orderposter.artnr, Orderposter.utpris, Orderposter.beskrivning, Orderposter.levDatum, Orderposter.bokad, Orderposter.antal, Orderposter.momssats ";

        //$itemsselect .= "Artiklar.beskrivning, Artiklar_fi.beskrivning_fi  ";
        //if ($fi) {	
        //	$itemsselect .= " FROM Artiklar, Artiklar_fi, Orderposter_fi, Tillverkare, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr like 'frakt%') ";
        //} else {
        $itemsselect .= " FROM Orderposter WHERE Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%' OR Orderposter.artnr = 'invoicefee') ";
        //}
        //$itemsselect .= " AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Moms.moms_id = Artiklar.momskod ";	
        $itemres = mysqli_query($conn_master, $itemsselect);

        while ($row = mysqli_fetch_object($itemres)) :
            if ($fi) {
                $val = "EUR";
                //$momssats = $row->momssats_fi;
                /**
                  if ($sv || $row->beskrivning_fi == "")
                  $beskrivning = $row->beskrivning;
                  else
                  $beskrivning = $row->beskrivning_fi;
                 */
            } else {
                $val = "kr";
                //$momssats = $row->momssats;
                //$beskrivning = $row->beskrivning;
            }
            $momssats = $row->momssats;
            $beskrivning = $row->beskrivning;
            # If manufacturer is a single dot, set it to ''
            $benamning = $beskrivning;
            // måste plocka tillverkare från mysql, det är inte säkert det finns i mssql längre
            $s_tillverkare = "SELECT tillverkare FROM Tillverkare, Artiklar  WHERE Tillverkare.tillverkar_id = Artiklar.tillverkar_id AND Artiklar.artnr = '" . $row->artnr . "'";
            $r = mysqli_query($conn_my, $s_tillverkare);
            $rw = mysqli_fetch_object($r);

            if ($rw->tillverkare != '.')
                $benamning = $rw->tillverkare . " " . $benamning;

            if ($show) {
                ?>

            <tr><td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
                    <?php echo $benamning; ?></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">
                    <?php
                    echo number_format(($row->utpris), 0, ',', ' ') . " " . $val;
                    ?>
                    </font></td>
                <td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
                    <?php
                    echo number_format(($row->utpris + $row->utpris * $momssats), 0, ',', ' ') . " " . $val;
                    ?>
                    </font></td>
            </tr>
            <?php
        }

        $price = $row->utpris + $row->utpris * $momssats;
        $messageText .= "  1   ";

        $messageText .= sprintf("%-35s", $benamning);

        $messageText .= number_format($price, 0, ',', ' ') . " " . $val;

        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">" . $row->antal . "</td>";
        /*
          $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
          $messageHtml .= number_format(($row->utpris ), 0, ',', ' ') . " " . $val;
          $messagehtml .= "</td>";
         */
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= number_format(($price), 0, ',', ' ') . " " . $val;
        $messagehtml .= "</td>";

        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";


    endwhile;

    if ($orderrow->paketRabatt > 0) {
        if ($sv) {
            $Paketrabatt = "Paketrabatt";
        } else {
            $Paketrabatt = "Pakettialennus";
        }

        if ($fi) {
            // $momssats = 0.22;
            $val = "EUR";
        } else {
            // $momssats = 0.25;
            $val = "kr";
        }
        ?>
        <?php if ($show) { ?>
            <tr>
                <td bgcolor="#ECECE6" align="left"><font face="Verdana, Arial" size="1"><% echo $Paketrabatt; %></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
            </tr>
            <?php
        }
        $messageText .= "  1   ";
        $messageText .= sprintf("%-35s", $Paketrabatt);
        $messageText .= number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$Paketrabatt</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;</td>";
        /*
          $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
          $messageHtml .= number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
          $messagehtml .= "</td>";
         */
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">- ";
        $messageHtml .= number_format(($orderrow->paketRabatt * $momssats) + $orderrow->paketRabatt, 0, ',', ' ') . " " . $val;
        $messagehtml .= "</td>";

        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";
    }


    if ($orderrow->rabatt > 0) {
        if ($sv) {
            $Paketrabatt = "Övrig rabatt";
        } else {
            $Paketrabatt = "Pakettialennus";
        }

        if ($fi) {
            // $momssats = 0.22;
            $val = "EUR";
        } else {
            // $momssats = 0.25;
            $val = "kr";
        }
        ?>
        <?php if ($show) { ?>
            <tr>
                <td bgcolor="#ECECE6" align="left"><font face="Verdana, Arial" size="1"><% echo $Paketrabatt; %></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1 st</font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format($orderrow->rabatt, 0, ',', ' ') . " " . $val; ?></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo number_format(($orderrow->rabatt * $momssats) + $orderrow->rabatt, 0, ',', ' ') . " " . $val; ?></font></td>
            </tr>
            <?php
        }
        $messageText .= "  1   ";
        $messageText .= sprintf("%-35s", $Paketrabatt);
        $messageText .= number_format(($orderrow->paketRabatt * $momssats) + $orderrow->rabatt, 0, ',', ' ') . " " . $val;
        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$Paketrabatt</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;</td>";
        /*
          $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
          $messageHtml .= number_format($orderrow->rabatt, 0, ',', ' ') . " " . $val;
          $messagehtml .= "</td>";
         */
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">- ";
        $messageHtml .= number_format(($orderrow->rabatt * $momssats) + $orderrow->rabatt, 0, ',', ' ') . " " . $val;
        $messagehtml .= "</td>";

        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";
    }


    if ($orderrow->presentkortAvdrag > 0) {
        if ($sv) {
            $Paketrabatt = "Avgår presentkort";
        } else {
            $Paketrabatt = "Pakettialennus";
        }
        ?>
        <?php if ($show) { ?>
            <tr>
                <td colspan="4" bgcolor="#ECECE6" align="left"><font face="Verdana, Arial" size="1"><?php echo $Paketrabatt; ?></font></td>
                <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><?php echo "- " . number_format($orderrow->presentkortAvdrag, 0, ',', ' ') . " " . $val; ?></font></td>
            </tr>
            <?php
        }
        $messageText .= "  1   ";
        $messageText .= sprintf("%-35s", $Paketrabatt);
        $messageText .= number_format($orderrow->presentkortAvdrag, 0, ',', ' ') . " " . $val;
        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\" colspan=\"2\"><font face=\"Verdana, Arial\" size=\"1\">$Paketrabatt</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">- " . number_format($orderrow->presentkortAvdrag, 0, ',', ' ') . " " . $val;
        $messagehtml .= "</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";
    }

    $messageHtml .= <<<EOF
<tr><td colspan="4"><hr noshade color="#CCCCCC" size="1"></td></tr>
EOF;
}

function process_orderrowsMail() { // används inte längre. 
    global $ordernrladdaom, $message, $orderrow, $messageText, $messageHtml, $orderrow, $fi,
    $lev_datum, $lev_datum_normal, $bestallningsgrans, $sv, $bestallt_fi, $lev_datum_norm_fi, $lev_datum_norm, $release, $conn_master;


    $betalsatt_id = $orderrow->betalsatt_id;

    // Artikel rubrik i mail meddelande
    $messageText .= "\nAntal\t  Vara  \t\t\t\t\t   Pris/st\n\n";

    // Först en fråga för att ta fram allt utom frakten (så att det hamnar först)
    /**
      if ($fi) {
      $itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Orderposter_fi.utpris as utpris, tillverkare, antal, ";
      $itemsselect .= "Orderposter_fi.levDatum, Orderposter_fi.bokad, Artiklar_fi.bestallningsgrans_fi as bestallningsgrans, Artiklar.lagersaldo, Artiklar_fi.lagersaldo_fi, ";
      $itemsselect .= "Artiklar.lev_datum_normal, Artiklar_fi.lev_datum_norm_fi, Moms.momssats_fi as momssats_fi ";
      $itemsselect .= "FROM Artiklar, Orderposter_fi, Tillverkare, Moms, Artiklar_fi WHERE Artiklar.artnr = Artiklar_fi.artnr_fi AND Orderposter_fi.artnr = Artiklar.artnr AND ";
      $itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
      $itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr not like 'frakt%') ";
      } else {
      $itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, tillverkare, antal, ";
      $itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal, Moms.momssats ";
      $itemsselect .= "FROM Artiklar, Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND ";
      $itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
      $itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";
      }
     */
    $itemsselect = "SELECT Orderposter.artnr, Orderposter.beskrivning, Orderposter.utpris, tillverkare, antal, ";
    $itemsselect .= "Orderposter.levDatum, Orderposter.bokad, Artiklar.bestallningsgrans, Artiklar.lagersaldo, Artiklar.lev_datum_normal, Moms.momssats ";
    $itemsselect .= "FROM Artiklar, cyberorder.Orderposter, Tillverkare, Moms WHERE Orderposter.artnr = Artiklar.artnr AND ";
    $itemsselect .= "Artiklar.tillverkar_id = Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND ";
    $itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr not like 'frakt%') ";

    //echo $itemsselect;
    $itemres = mysqli_query($conn_master, $itemsselect);

    while ($row = mysqli_fetch_object($itemres)) :
        $artnr = $manufacurer = $name = $count = $outprice = $levDatum = $bokad = $bestallningsgrans = $lagersaldo = $normal_leverasntid = "";
        $artnr = $row->artnr;
        $manufacturer = $row->tillverkare;
        /**
          if ($fi) {
          if ($row->beskrivning_fi != "")
          $name = $row->beskrivning_fi;
          else
          $name = $row->beskrivning;
          } else {
          $name = $row->beskrivning;
          }
         */
        $name = $row->beskrivning;

        $count = $row->antal;
        $outprice = $row->utpris;
        $levDatum = $row->levDatum;
        $bokad = $row->bokad;
        $bestallningsgrans = $row->bestallningsgrans;
        $lagersaldo = $row->$lagersaldo;
        $normal_leveranstid = $row->lev_datum_normal;
        $momssats = $row->momssats;

        //$price = $outprice * 1.25;
        if ($manufacturer == ".")
            $benamning = $name;
        else
            $benamning = $manufacturer . " " . $name;
        $status = "";
        // TODO:  här skall nya saker ske, hmm..
        if ($bokad == 0) {
            if ($levDatum == "-" || $levDatum == "" || $levDatum == " ")
                $status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">leveransdatum okänt</font>";
            elseif ($levDatum == "0") {
                if ($bestallningsgrans == "0")
                    $status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara, normal leveranstid $normal_leveranstid</font>";
                else {
                    $status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">leveransdatum okänt</font>";
                    //echo "<p>$levDatum  <br>  $status<p>";
                }
            }
            else
                $status .= "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beräknas in i lager $levDatum</font>";
        }
        else {
            $status .= "\t\t<font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\">finns i lager</font>";
        }


        $messageHtml .= "\n<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$benamning</td>\n";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>\n";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= sprintf("%10.0f SEK", $outprice * $count);
        $messagehtml .= "</td>\n";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= sprintf("%10.0f SEK", ($outprice + $outprice * $momssats) * $count);
        $messagehtml .= "</td>\n";

        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">$status</td>\n";
        $messageHtml .= "</tr>";

        if (mb_strlen($benamning) >= '30')
            $benamning = mb_substr($benamning, 0, 30) . "...";

        $messageText .= "  $count   ";

        $messageText .= sprintf("%-35s", $benamning);
        $messageText .= sprintf("%10.0f SEK", ($outprice + $outprice * $momssats) * $count);
        $messageText .= "\n";


    endwhile;

    // Sen plocka fram frakten (så att det hamnar sist)
    /**
      if ($fi) {
      $itemsselect  = "SELECT Orderposter_fi.artnr, Artiklar_fi.beskrivning_fi as beskrivning, Orderposter_fi.utpris, Orderposter_fi.antal, Moms.momssats_fi as momssats ";
      $itemsselect .= "FROM Artiklar, Artiklar_fi, Orderposter_fi, Moms WHERE Orderposter_fi.artnr = Artiklar.artnr AND Artiklar.artnr = Artiklar_fi.artnr_fi AND Moms.moms_id = Artiklar.momskod AND ";
      $itemsselect .= "Orderposter_fi.ordernr = $ordernrladdaom AND (Orderposter_fi.artnr like 'frakt%') ";
      } else {
      $itemsselect  = "SELECT Orderposter.artnr, Artiklar.beskrivning, Orderposter.utpris, Orderposter.antal, Moms.momssats ";
      $itemsselect .= "FROM Artiklar, Orderposter, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Moms.moms_id = Artiklar.momskod AND ";
      $itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";
      } */
    $itemsselect = "SELECT Orderposter.artnr, Orderposter.beskrivning, Orderposter.utpris, Orderposter.antal, Moms.momssats ";
    $itemsselect .= "FROM Artiklar, Orderposter, Moms WHERE Orderposter.artnr = Artiklar.artnr AND Moms.moms_id = Artiklar.momskod AND ";
    $itemsselect .= "Orderposter.ordernr = $ordernrladdaom AND (Orderposter.artnr like 'frakt%') ";

    $itemres = mysqli_query($conn_master, $itemsselect);

    while ($row = mysqli_fetch_object($itemres)) :
        $name = $row->beskrivning;
        $count = $row->antal;
        $outprice = $row->utpris;
        $count = $row->antal;
        $price = $outprice + $outprice * $row->momssats;
        $messageText .= "  1   ";

        $messageText .= sprintf("%-35s", $name);
        if ($fi)
            $messageText .= sprintf("%10.0f EUR", $price);
        else
            $messageText .= sprintf("%10.0f SEK", $price);
        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">$name</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">$count</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= sprintf("%10.0f SEK", $outprice);
        $messagehtml .= "</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= sprintf("%10.0f SEK", $price);
        $messagehtml .= "</td>";

        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"left\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";


    endwhile;

    if ($orderrow->paketRabatt > '0') {

        $messageText .= "  1   ";
        $messageText .= sprintf("%-35s", Paketrabatt);
        $messageText .= sprintf("%10.0f SEK", ($orderrow->paketRabatt * 1.25));
        $messageText .= "\n";

        $messageHtml .= "<tr>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">Paketrabatt</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"center\"><font face=\"Verdana, Arial\" size=\"1\">1</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\">";
        $messageHtml .= sprintf("%10.0f SEK", $orderrow->paketRabatt);
        $messagehtml .= "</td>";
        $messageHtml .= "<td bgcolor=\"#ECECE6\" align=\"right\"><font face=\"Verdana, Arial\" size=\"1\" align=\"right\">";
        $messageHtml .= sprintf("%10.0f SEK", $orderrow->paketRabatt * 1.25);
        $messagehtml .= "</td>";

        $messageHtml .= "<td bgcolor=\"#ECECE6\"><font face=\"Verdana, Arial\" size=\"1\">&nbsp;&nbsp;</td>";
        $messageHtml .= "</tr>";
    }
    $messageHtml .= <<<EOF
<tr><td bgcolor="#ECECE6" colspan="5" height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;</font></td></tr>
EOF;
}

function show_summaryMail() {

    global $orderrow, $messageText, $messageHtml, $fi, $sv;



    if ($fi)
        $val = " EUR";
    else
        $val = " kr";

    if (!$fi && $orderrow->totalsumma < 2) {
        $summaexmoms = number_format($orderrow->netto, 1, ',', ' ') . " " . $val;
    } else {
        $summaexmoms = number_format($orderrow->netto, 0, ',', ' ') . " " . $val;
    }

    if ($orderrow->land_id == 47 || $orderrow->land_id == '999') {
        $moms = sprintf("%10.0f SEK", 0);
        $summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
    } else {
        if (!$fi && $orderrow->totalsumma < 2) {
            $moms = number_format($orderrow->moms, 1, ',', ' ') . " " . $val;
        } else {
            $moms = number_format($orderrow->moms, 0, ',', ' ') . " " . $val;
        }

        $summaMedMoms = number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val;
    }

    if ($fi && !$sv) {
        $messageText .= <<<EOF

  Alv 0%:  $summaexmoms
  Alv:     $moms
  Sis Alv: $summaMedMoms

EOF;
        $messageHtml .= <<<EOF
<tr>
  <td height="15"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;
    </font></td>
  <td colspan="2" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv 0%:</font></p>
  </td><td bgcolor="#ECECE6" height="15" align="right"><font face="Verdana, Arial"><font size="1">$summaexmoms</font>
</font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="2" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Alv:</font>
  </td><td bgcolor="#ECECE6" height="15" align="right"><font face="Verdana, Arial"><font size="1">$moms</font></font></td>
</tr>
<tr>
  <td height="15"></td>
  <td colspan="2" height="15">
    <p align="right"><font face="Verdana, Arial" size="1">Sis Alv:</font>
  </td><td bgcolor="#ECECE6" height="15" align="right"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b> 
</font></td>
</tr>
EOF;
    } else {
        $messageText .= <<<EOF

  exkl. moms:       $summaexmoms
  Moms:             $moms
  Totalt:           $summaMedMoms

EOF;
        $messageHtml .= <<<EOF
<tr>
  <td bgcolor="#ECECE6" colspan="2" align="left"><font face="Verdana, Arial" size="1"><b>Totalt:</b></font></td>
  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial"><b><font size="1">$summaMedMoms</font></b></font></td>
  <td>&nbsp;</td>
</tr>
</table>
</center>
</div>
EOF;
    }
}

function write_invoiceinfo() {
    global $orderrow, $kundrow, $fi, $sv;
    ?>

    <table width="100%" border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b>
                            <?php if ($fi & !$sv) { ?>
                                <?php if ($kundrow->foretag == -1) { ?>Laskutusosoite<?php } else { ?>Osoite: <?php } ?>
                            <?php } else { ?>
                                <?php if ($kundrow->foretag == -1) { ?>Fakturaadress<?php } else { ?>Adress: <?php } ?>
                            <?php } ?>
                        </b></font></small></small></td>
        </tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->namn; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->co; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->adress; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->postnr . " " . $orderrow->postadr; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->land; ?></font></small></small></td></tr>
    </table>

    <?php
}

# End of write_invoiceinfo
// Create a table with delivery address

function write_deliveryinfo() {
    global $orderrow, $sv;
    ?>
    <table width="100%" border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td bgcolor="#FFFFFF"><small><small><font face="Verdana, Arial"><b>
                            <?php if ($sv) { ?>Leveransadress<?php } else { ?>Toimitusosoite<?php } ?>
                        </b></font></small></small></td>
        </tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lnamn; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lco; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->ladress; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->lpostnr . " " . $orderrow->lpostadr; ?></font></small></small></td></tr>
        <tr><td bgcolor="#ECECE6"><small><small><font face="Verdana, Arial"><?php echo $orderrow->land; ?></font></small></small></td></tr>
    </table>

    <?php
}

# End of write_deliveryinfo
?>
