<div class="clear"></div>
<div class="top20"></div>
<hr noshade color="#0000FF" align="left" width="850" size="1">
<h3>Orderstatus (så som kunden ser den just nu)</h3>
<?php if ($orderrow->docstatus == "IN") { ?>
<h6>Dokumentstatus är "Felaktig". Kunden ser ej denna order. Kontrollera och åtgärda detta i ADempiere.</h6>
<?php } ?>
<?php if ($orderrow->docstatus == "VO") { ?>
<h6>Denna order är annullerad. Kunden ser ej denna order.</h6>
<?php } ?>
<div>

	<table border="0" width="830px" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2"><font face="Verdana, Arial" size="1">&nbsp;&nbsp;&nbsp;&nbsp;</font></td>
      </tr>
      <tr>
        <td colspan="2">

<table border="0" cellspacing="1" cellpadding="3" width="100%">

<?php if ($sv): ?>
<tr>
<td width="420"><font face="Verdana, Arial" size="1"><b>Vara</b></font></td>
<td><font face="Verdana, Arial" size="1"><b>antal</b></font></td>
<td width="75"><b><font face="Verdana, Arial" size="1">exkl moms</font></b></td>
<td width="75"><b><font face="Verdana, Arial" size="1">inkl moms</font></b></td>
<td><b><font face="Verdana, Arial" size="1">lagerstatus </b></font></a></td>
</tr>
<?php else: ?>
<tr>
<td width="420"><font face="Verdana, Arial" size="1"><b>Tuote</b></font></td>
<td><font face="Verdana, Arial" size="1"><b>lukumäärä</b></font></td>
<td width="75"><b><font face="Verdana, Arial" size="1">Hinta alv 0%</font></b></td>
<td width="75"><b><font face="Verdana, Arial" size="1">Hinta sis. alv</font></b></td>
<td><b><font face="Verdana, Arial" size="1">Varasto tilanne </b></font></a></td>
</tr>
<?php endif; ?>

<?php 
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
		// echo "här: ";
		$status->viewOrderLines($orderrow, $old, $bask); 
	} else {
		$status->viewOrderLines($orderrow, $old, $bask); 
	}
if ($fi) {
	$val = "EUR";
} elseif ($no) {
	$val = "NOK";
} else {
	$val = "SEK";
}
?>
<!--
<tr>
<td bgcolor="#ECECE6" colspan="5" height="1"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
-->
<?php if ($orderrow->paketRabatt > 0): ?>
<tr>
  <td colspan="2">
    <p align="right"><font face="Verdana, Arial" size="1"><?php if ($sv): ?>Paketrabatt<?php else: ?>Kokonaisalennus<?php endif; ?>:&nbsp;</font></p></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">-<?php echo number_format($orderrow->paketRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
    <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
<?php endif; ?>

<?php if ($orderrow->rabatt > 0): ?>
<tr>
  <td colspan="2">
    <p align="right"><font face="Verdana, Arial" size="1"><?php if ($sv): ?>Rabatt<?php else: ?>Rahti<?php endif; ?>:&nbsp;</font></p></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">-<?php echo number_format($orderrow->rabatt, 0, ',', ' ') . " " . $val; ?></font></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
    <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
<?php endif; ?>
<?php if ($orderrow->avtalsRabatt > 0): ?>
<tr>
  <td colspan="2">
    <p align="right"><font face="Verdana, Arial" size="1"><?php if ($sv): ?>Avtalsrabatt<?php else: ?>Sopimusalennus<?php endif; ?>:&nbsp;</font></p></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">-<?php echo number_format($orderrow->avtalsRabatt, 0, ',', ' ') . " " . $val; ?></font></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
    <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
<?php endif; ?>
<?php if ($orderrow->presentkortAvdrag > 0): ?>
<tr>
  <td colspan="2">
    <p align="right"><font face="Verdana, Arial" size="1"><?php if ($sv): ?>Presentkort<?php else: ?>Sopimusalennus<?php endif; ?>:&nbsp;</font></p></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">&nbsp;</font></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">-<?php echo number_format($orderrow->presentkortAvdrag, 0, ',', ' ') . " " . $val; ?></font></td>
    <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
<?php endif; ?>

<tr>
  <td colspan="2">
    <p align="right"><font face="Verdana, Arial" size="1"><?php if ($sv): ?>Totalt<?php else: ?>Yhteensä<?php endif; ?>:&nbsp;</font></td>
	<td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><b><?php echo number_format($orderrow->netto, 0, ',', ' ') . " " . $val; ?></b></font></td>
    <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1"><b><?php echo number_format($orderrow->totalsumma, 0, ',', ' ') . " " . $val; ?></b></font></td>
    <td><font face="Verdana, Arial" size="1">&nbsp;</font></td>
</tr>
<tr>
<td bgcolor="#FFFFFF" valign="bottom" colspan="6">
<?php
if (($orderrow->skickad_av == 'XX') AND ($orderrow->betalsatt_id == 7) AND !$fi):

	# Lägg på moms
	//$totalsumma = $orderrow->netto*1.25;
	$totalsumma = $orderrow->totalsumma;
	# Avrunda ner till närmaste heltal eftersom
	# access är programmerat så
	//$totalsumma = floor($totalsumma);
	
	$totalsumma = number_format ($totalsumma, 0, "", "");

	// $output = `/usr/java/jdk1.3.1_07/bin/java -cp /usr/java/lib/HFAffar.jar se.hbfinans.netpay.store.HFStoreModule 900080 $ordernr $totalsumma SEK "" SV http://www.cyberphoto.se/?ordernr=$ordernrladdaom&` ;
	// nedanstående när vi byter server

	$output = `/usr/bin/java -cp /usr/lib/java/HFAffar.jar se.hbfinans.netpay.store.HFStoreModule 900080 $ordernr $totalsumma SEK "" SV http://www.cyberphoto.se/?ordernr=$ordernrladdaom&` ;


//echo $output
?>


<font face="Verdana, Arial" size="1">
<br>
<b>Obs! </b>
Ordern är ännu inte registerad hos netpay. Om du har missat att gå vidare till handelsbanken finans (netpay) så klicka på knappen nedanför.
Registreringen görs manuellt så det är en viss fördröjning. Om du redan har gått vidare till netpay och fyllt i alla uppgifter kan du bortse
från den här texten.<br>
</font>
</td>
</tr>
<tr>
<td bgcolor="#FFFFFF" valign="bottom" colspan="6">
<font face="Verdana, Arial" size="1">
<form action="https://www.netpay.saljfinans.com/reservation" target="_parent" method="get">
<input type="hidden" name="reservation" value="<?php echo $output; ?>">
<input type="image" src="netpay_s.gif" border="0" value="Klicka här för att gå vidare till Netpay"><b>&nbsp;Klicka bara en gång.</b> Sidan är långsam och kan ta lång tid att ladda</input>
</form>

</font>
<?php elseif (($orderrow->levklar == 123412341234) AND ($orderrow->betalsatt_id == 5)): ?>

<font face="Verdana, Arial" size="1">
<br>
<?php if ($fi && !$sv): ?>
<b>Huom!!</b> Odotamme korttimaksuanne <a href="kortMan_fi.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>&sv=<?php echo $sv; ?>">klikkaa tästä mikäli haluat yrittää uudelleen</a>
<?php elseif ($fi && $sv): ?>
<b>Obs!!</b> Vi väntar på kortbetalningen från er <a href="kortMan_fi.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>&sv=<?php echo $sv; ?>">klicka här om du vill pröva igen</a>
<?php else:  ?>
<b>Obs!!</b> Vi väntar på kortbetalningen från er <a href="kortMan.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>">klicka här om du vill pröva igen</a>
<?php endif; ?>
<br>

</font>

<?php endif; ?>

</td>
</tr>
<tr>
  <td colspan="3">
    <b><font face="Verdana" size="1"><?php if ($sv): ?>Kunduppgifter<?php else: ?>Asiakas tietosi<?php endif; ?></font></b>
  </td><td colspan="2"></td>
</tr>
<tr>
  <td colspan="2">
        <table border="0" width="100%" cellspacing="1" cellpadding="2">
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Kundnummer<?php else: ?>Asiakasnumero<?php endif; ?></font></td>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php printf("%d", $orderrow->kundnr); ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($old_foretag == -1) { ?><?php if ($sv): ?>Företag<?php else: ?>Tilaajan nimi tai Yritys<?php endif; ?><?php } else { ?><?php if ($sv): ?>Företag / Namn<?php else: ?>Tilaajan nimi tai Yritys<?php endif;  } ?></font></td>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $orderrow->namn; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
			<!--
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Email<?php else: ?>Email<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->email; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>

	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Telefon<?php else: ?>Puhelin<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->telefon; ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Mobilnummer<?php else: ?>Matkapuhelin<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->mobilnr; ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>	        
			-->
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Adressrad 1 <?php else: ?>Osoite 1<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->co; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Adressrad 2 <?php else: ?>Osoite 2<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->adress; ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Postnummer<?php else: ?>Postinumero<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->postnr; ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Postadress<?php else: ?>Postiosoite<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->postadress; ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Land<?php else: ?>Maa<?php endif; ?></font></td>

	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $status->getCountry ($orderrow->fland_id); ?>&nbsp;</font></td>

	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150"><font face="Verdana" size="1">&nbsp;</font></td>
	          <td><font size="1" face="Verdana">&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td colspan="2" width="150" bgcolor="#FFFFFF"><b><font face="Verdana" size="1"><?php if ($sv): ?>Leveransuppgifter<?php else: ?>Toimitusosoite (mikäli eri  kuin yllämainittu) <?php endif; ?></font></b></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Företag / Namn<?php else: ?>Vastaanottaja <?php endif; ?></font></td>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $orderrow->lnamn; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	       <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Adressrad 1:<?php else: ?>Osoite 1:<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->lco; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Adressrad 2:<?php else: ?>Osoite 2:<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->ladress; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Postnummer<?php else: ?>Postinumero<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->lpostnr; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Postadress<?php else: ?>Postitoimipaikka<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $orderrow->lpostadr; ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	        <tr>
	          <td width="150" bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Land<?php else: ?>Maa<?php endif; ?></font></td>
	          <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $status->getCountry ($orderrow->land_id); ?>&nbsp;</font></td>
	          <td width="5"></td>
	        </tr>
	      </table>
  </td><td colspan="3" valign="top">
    <table border="0" width="100%" cellspacing="1" cellpadding="2">
      <tr>
	             	<td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Ordernummer<?php else: ?>Tilausnumero<?php endif; ?></font></td>
	             	<td bgcolor="#ECECE6"><font face="Verdana" size="1"><b><?php printf("%d", $orderrow->ordernr);  ?></b></font></td>
      </tr>
      <tr>
	                <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php if ($sv): ?>Orderkommentar<?php else: ?>Tilauskommentti<?php endif; ?></font></td>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $orderrow->kommentar; ?>&nbsp;</font></td>
      </tr>
      <tr>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Fraktsätt<?php else: ?>Toimitustapa<?php endif; ?></font></td>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $status->getLeveranssatt ($orderrow->leveranssatt_id); ?></font></td>
      </tr>
      <tr>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?>Betalsätt<?php else: ?>Maksutapa<?php endif; ?></font></td>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $status->getBetalsatt ($orderrow->betalsatt_id); ?></font></td>
      </tr>
      <?php if ($orderrow->betalsatt_id == 5 && $orderrow->levklar == 123412341234): ?>

      <tr>
      				<?php if ($sv): ?><?php else: ?><?php endif; ?>
	                <td bgcolor="#ECECE6" colspan="2"><font face="Verdana" size="1">
	                <?php if ($fi && !$sv): ?>
	                <b>Huom!.</b> Odotamme korttimaksuanne <a href="kortMan_fi.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>&sv=<?php echo $sv; ?>">klikkaa tästä mikäli haluat yrittää uudelleen</a>
	                <?php elseif ($fi && $sv): ?>
	                <b>Obs!!</b> Vi väntar på kortbetalningen från er <a href="kortMan_fi.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>&sv=<?php echo $sv; ?>">klicka här om du vill pröva igen</a>
	                <?php else: ?>
	                <b>Obs!!</b> Vi väntar på kortbetalningen från er <a href="kortMan.php?ordernr_check=<?php $ordernr_check = $ordernr; echo $ordernr_check; ?>">klicka här om du vill pröva igen</a>
	                <?php endif; ?>
	                </font></td>

      </tr>
      <?php elseif (($orderrow->betalsatt_id == 1) && ($orderrow->levklar == 0)): ?>
      <tr>			
      				<?php if ($sv): ?><?php else: ?><?php endif; ?>
	                <td bgcolor="#ECECE6" colspan="2"><font face="Verdana" size="1">Förskottsinbetalning har ännu inte inkommit. Vänligen <a href="mailto:ekonomi@cyberphoto.se?subject=Förskottsinbetalning%20på%20order%20<?php echo $ordernr; ?>">kontakta oss</a> om det gått mer än en vecka sen ni gjort inbetalningen</font></td>

      </tr>
      <?php endif; ?>

      <tr>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><img src="/pic/10.gif" width="8" height="8"></font></td>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><img src="/pic/10.gif" width="8" height="8"></font></td>
      </tr>
      <tr>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php if ($sv): ?><?php else: ?><?php endif; ?>Status</font></td>
	                <?php
	                setlocale (LC_ALL, 'en_US');

			if ($_SERVER['REMOTE_ADDR'] == "81.8.240.115") {
				// echo $orderrow->skickat . "<br>";
			}
	                
			$skickat = preg_replace('/:[0-9][0-9][0-9]/','', $orderrow->skickat);
			if ($fi) {
				$skickat = strftime ("%m-%d-%Y", strtotime($skickat));
			} else {
				$skickat = strftime ("%Y-%m-%d", strtotime($skickat));
			}

			if ($skickat == "1970-01-01" || $skickat == "01-01-1970") {
				$skickat = "";
			}

	                
	                ?>
	                <td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php echo $status->levStatusAD($skickat, $orderrow->behandlat, $orderrow->levklar, $orderrow->betalsatt_id, $orderrow->leveranssatt_id, $orderrow->faktura_ok, $orderrow->ordernr, $orderrow->skickad_av); ?></font></td>
	                <?php setlocale (LC_ALL, 'sv_SE'); ?>
      </tr>
      <?php if (!$fi): ?>
	  <!--
      <tr>
	                <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php if ($sv): ?><?php else: ?><?php endif; ?>Kollinummer på försändelsen </font></td>
	                <td bgcolor="#ECECE6"><font size="1" face="Verdana"><?php echo $status->getKollinrOnline($orderrow->ordernr, $orderrow->land_id, $orderrow->ant_kolli, true); ?></font></td>
      </tr>
	  -->
      <?php endif; ?>
      <?php if ($orderrow->restorder != "" && $orderrow->restorder == "asdfasdfasdfafasfa"):  // använder inte, det verkar inte fungera. 
      ?>
      <tr>
	             	<td bgcolor="#ECECE6"><font size="1" face="Verdana">Restorder</font></td>
	             	<td bgcolor="#ECECE6"><font face="Verdana" size="1"><a href="show_order.php?ordernr=<?php echo $orderrow->restorder; ?>"><?php echo $orderrow->restorder; ?></a> (klicka
                      för detaljer)</font></td>
      </tr>
      <?php endif; ?>
	  <?php if ($orderrow->leveranssatt_id == 3 || $orderrow->leveranssatt_id == 23) { ?>
      <tr>
           	<td valign="top" bgcolor="#ECECE6"><font size="1" face="Verdana"><?php if (!$sv && $fi): ?>Kollinumero lähetykselle<?php else: ?>Kollinummer på försändelsen<?php endif; ?></font></td>
           	<td bgcolor="#ECECE6"><font face="Verdana" size="1"><?php $webkolli->getKollinr($orderrow->ordernr); ?></td>
      </tr>
	  <?php } ?>
      <!--
	  <tr>
	             	<td bgcolor="#ECECE6">&nbsp;</td>
	             	<td bgcolor="#ECECE6">&nbsp;</td>
      </tr>
	  -->
      <tr>
      				<?php if ($fi) $mailLink = "order@cyberphoto.fi"; else $mailLink = "order@cyberphoto.se"; ?>
			        <?php if ($sv): ?>
      				<!--
      				Meilaa meille mielipiteesi tästä sivusta. Onko jotain mitä kaipaat tai jotain vastaavaa 
					Tästä klikkaamalla voit lähettää meille e-mailin. -->
	             	<td colspan="2"><font face="Verdana" size="1"><br>
                      För frågor om din order, vänligen maila oss<br>
                      Observera att lagerstatus visas på varje enskild produkt. För mer info om vad leveranstiderna betyder, håll muspekaren över leveranstiden<br>
                      <a href="mailto:<?php echo $mailLink; ?>?subject=Kommentar om orderstatus, ordernr: <?php printf("%d", $orderrow->ordernr); ?>">Klicka
                      här för att skicka oss ett mail.&nbsp;</a></font></td>
                   <?php else: ?>
	             	<td colspan="2"><font face="Verdana" size="1"><br>
                      Kysymyksiä koskien tilaustasi, voit ystävällisesti mailata meille<br>
                      Huomioi että varastotillanne näytetään jokaiselle tuotteelle.<br>
                      <a href="mailto:<?php echo $mailLink; ?>?subject=Kommentti tilauksen asemasta, Tilausnr: <?php printf("%d", $orderrow->ordernr); ?>">Klikkaa tästä lähettääksesi meille sähköpostin&nbsp;</a></font></td>                   
                   <?php endif; ?>
      </tr>
    </table>
  </td>
</tr>
</table>
</td>
      </tr>
      <?php if ($fi) { ?>
      <tr>
        <td colspan="2">
    	<p align="center"><font face="Verdana, Arial" size="1"><br>
    	<a style="text-decoration: none" href="http://www.cyberphoto.fi" target="top">© CyberPhoto</a></font>
    	</td>
      </tr>
      <?php } else { ?>
      <tr>
        <td colspan="2">
    	<p align="center"><font face="Verdana, Arial" size="1"><br>
    	<a style="text-decoration: none" href="http://www.cyberphoto.se" target="top">© CyberPhoto AB</a></font>
    	</td>
      </tr>
      <?php } ?>
    </table>
</div>
<hr noshade color="#0000FF" align="left" width="850" size="1">