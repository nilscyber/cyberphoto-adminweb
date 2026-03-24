<?php
/*

PHP login "object"
author		Nils Kohlström
version		2001-05-16

Inkluderade funktioner:
	viewPacketDelivery, visar innehållet i ett paket (pac)
	viewItemsInBasket(), skriver ut tabellrader med innehållet i kundvagnen
	check_lager(), visar lagerstatus på produkt, tillhör viewItemsInbasket()
	check_package(), kontrollerar lagerstatus på paket (artnr som slutar på pac)
	check_queue(), kontrollerar antal på kö på produkten. 
	generate_pass(), genererar slumpmässiga lösenord
*/



// Ta bort gamla uppgifter från ev. tidigare lagd order 
# registrera först de gamla variablerna så att vi kan...
session_register("kundnrladdaom", "ordernrladdaom");
# ... förstöra dom: 
$kundnrladdaom = "";
$ordernrladdaom = "";

function viewPacketDelivery($artnr, $mangd, $fi) {
	global $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans;
	
	include ("CConnect.php");


	$goodscounter=0;
	$goodsvalue=0;
	
	//echo $artnr;
	$select =  "SELECT Paketpriser.artnr_paket, Paketpriser.artnr_del, Paketpriser.antal, Artiklar.artnr, ";
	$select .= "Artiklar.beskrivning, Artiklar.lagersaldo, Artiklar.beskrivning, ";
	$select .= "tillverkare, Artiklar.lagersaldo, Artiklar.bestallt, Artiklar.lev_datum, ";
	$select .= "Artiklar.bestallningsgrans, Artiklar.lev_datum_normal ";
	$select .= "FROM Artiklar INNER JOIN Paketpriser ON Artiklar.artnr = Paketpriser.artnr_del ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE Paketpriser.artnr_paket = '$artnr' ";

	//echo $select;
	/* 	while ($row = mysqli_fetch_array($res) )   {
	extract ($row); */
	$res = mysqli_query($select);
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)):
		
		$description = "";
		
		extract($row);
		$count = $antal*$mangd;
		if ($tillverkare != '.')
			$description = $tillverkare . " ";

		$description .= $beskrivning;


		?>
		
		<tr>
		  <td bgcolor="#ECECE6"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
		  <td bgcolor="#ECECE6" align="center"><font color="#2B2B2B" face="Verdana, Arial" size="1"><?php echo $antal*$mangd; ?></font></td>
		  <td bgcolor="#ECECE6"><font color="#2B2B2B" face="Verdana, Arial" size="1">
		<?php	
		//($artnr, $count, NULL);
		check_lager($artnr, $fi); 
		?>
		</font></td>
		</tr>
		<?php
		endwhile;

	}
	else { ?>

		  </font></td>
		</tr>
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1">
		  <a href="mailto:order@cyberphoto.se"><?php if ($fi == 'yes'): ?>Information saknas, kontakta cyberphoto för mer info<?php else: ?>Information saknas, kontakta cyberphoto för mer info<?php endif; ?></a>
		  </font></td>
		</tr>
		<?php
	}
	?>
	  </font></td>
	</tr>
		


<?php
	
}




function generate_pass ($pass_len)  { 
	/*
	Input: $pass_len, Längd på lösenord
	Output: $nps, lösenord bestående av stora och små bokstäver och siffror
	
	*/
$nps = ""; 

// Seed the random number generator
mt_srand ((double) microtime() * 1000000); 

while (strlen($nps)<$pass_len) { 
	
	// Ge $c ett värde från slumpmässigt valt ASCII värde
	// bara A-Z, a-z och siffror för att inte få några
	// skumma tecken
	$randvalue = mt_rand (48, 122);
	$c = chr($randvalue); 
	
	// Lägg till på $nps om det är i rätt format
	#if (eregi("^[a-z0-9]$", $c)) {
	if (eregi("[a-z0-9]", $c)) {
		$nps = $nps.$c;
		// förenkla för kunden genom att bara använda små bokstäver
		$nps = strtolower($nps);
	}
	
}
 	return ($nps); 
}


function viewBasketShort($kundvagn) {
	
	include ("CConnect.php");

	$output = "";
	if (ereg ("(grejor:)(.*)", $kundvagn,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	 
	$n = count($argument);
	//for ($i=0; ($i < $n);  $i+=2) {
	for ($i=$n-2; ($i > -1); $i+=-2) {
		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article

		$select  = "SELECT artnr, beskrivning, kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt FROM Artiklar ";
		$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
		$select .= "WHERE artnr='$arg'";
		
		# Alla värden försvinner inte i varje loop, så därför måste vi göra enligt nedan
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";

		$row = mysqli_fetch_array(mysqli_query($select));
		extract($row);
		
		
		$goodscounter += '1';
		$goodsvalue += ($utpris*$count);
		
		$description = $count . "st ";
		if ($tillverkare != '.')
			$description .= $tillverkare . " ";
		$description .= $beskrivning;
		
		if (strlen($description) >= '24')
			$description = substr ($description, 0, 24) . "...";

		if (!eregi("frakt", $artnr)) {
		$output .= "<option value=\"\">$description</option>";
	
	
		}
	}

	return $output;

}

function viewItemsInBasket($firstbasket, $fi) {
	global $kundvagn, $goodsvalue, $utpris, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, $PHP_SELF;
	
	include ("CConnect.php");

	
	$freight_check = NULL;
	$pallDelivery = false; // visar om någon produkt innehåller frakttillägg. Används bl.a. till att tyngre produkter inte skall skickas som hempkaet
	# Get the cookie kundvagn
	$answers = $kundvagn;
		
	if (ereg ("(grejor:)(.*)", $answers,$matches)) {
		# Split the number of items and article id s into a list
		$orderlista = $matches[2];
		$argument = split ("\|", $orderlista);
	}

	$goodscounter=0;
	$goodsvalue=0;
	$goodsvalueMoms=0;
	$moms = 0;
	
	 
	$n = count($argument);
	$j = 0;
	for ($i=0; ($i < $n);  $i+=2) {

		$arg = $argument[$i];        # Article id
		$count = $argument[$i+1];    # Keeps track of the number of the same article

		$select  = "SELECT artnr, Artiklar.beskrivning, kommentar, utpris, tillverkare, frakt, lagersaldo, bestallt, ";
		$select .= "lev_datum, bestallningsgrans, lev_datum_normal, frakt, link, momssats FROM Artiklar, Tillverkare, Moms ";
		$select .= "WHERE Artiklar.tillverkar_id=Tillverkare.tillverkar_id AND Artiklar.momskod = Moms.moms_id AND "; 
		$select .= "artnr='$arg'";

		# Alla värden försvinner inte, så därför måste vi göra enligt nedan
		$artnr = $description = $kommentar = $tillverkare = $beskrivning = $utpris = $frakt = $lagersaldo = $bestallt = $lev_datum = $bestallningsgrans = $lev_datum_normal = "";
		$momssats = 0;

		$row = mysqli_fetch_array(mysqli_query($select));
		extract($row);
		if ($artnr == 'fraktbutik') // om hämtas
			$extra_freight = 999;
			
		// Lägg på extra frakt om det behövs och inte är frakt i butik
		if ($frakt AND $frakt <> 7 AND $extra_freight <> 999 ) {
			//echo "1";
			if ($extra_freight < $frakt) { // antagligen så att det blir den högsta fraktkostanden				
				$extra_freight = $frakt; 				
			}
		}
		if ($extra_freight == 3) 
			$pallDelivery = true;
			
		if (eregi("presentkort", $artnr)) {	
			ereg ("(presentkort)([0-9]+)", $arg,$matches) ;
				$j = $matches[2];
		
			if ($firstbasket == "nooutput")
				wiewGiftCardInBasket($j, false);
			else
				wiewGiftCardInBasket($j, true);
			//$j +=1;
		}
		//echo "tjosan $utpris";
		$goodscounter += 1;
		$goodsvalue += ($utpris*$count);
		$goodsvalueMoms += ($utpris + $utpris * $momssats)*$count;
		$moms += $utpris*$count*$momssats;
		
		
		if ($tillverkare != '.')
			$description = $tillverkare . " ";
		
		$description .= $beskrivning . " " . $kommentar;

// visa bara info om det inte är kostnadsfri frakt
if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr))):

?>

		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php echo $description; ?></font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1"><?php echo $count; ?></font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", $utpris*$count);  ?>

		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", ($utpris + $utpris * $momssats) * $count); ?>
		</font></td>
		<td bgcolor="#ECECE6" align="left">

<?php		
		if (!((eregi("^frakt", $artnr)) OR (eregi("Lott", $artnr))) ):
		//if (!(eregi("^frakt", $artnr))): 
?>

<?php		check_lager($artnr, $fi); ?>
<?php		
		else: ?>
		&nbsp;&nbsp;
<?php 		endif; ?>

		</font></td>
		<td bgcolor="#ECECE6" nowrap>
<?php		if (!((eregi("^frakt", $artnr)) OR (eregi("Lott", $artnr))) ): ?>

		<A HREF="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '1')">
		<font face="Verdana, Arial" size="1">
		<?php if ($fi == 'yes'): ?>
		<img src="antal_fi.gif" border=0 alt="muuta lukumäärä"></font></A>
		<?php else: ?>
		<img src="antal.gif" border=0 alt="klicka här för att ändra antal"></font></A>
		<?php endif; ?>
		<?php else: ?>
		&nbsp;&nbsp;
		<?php endif; ?>
		</td>
		</tr>
		
<?php  endif; ?>
<?php	}

	
	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight <> 999): 
		    
		if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
	    elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
	    //elseif ($extra_freight == 3) $extra_freight_artnr = 'frakt+3';
	    else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
	    $select  = "select Artiklar.beskrivning, kommentar, utpris, Moms.momssats from Artiklar, Moms where Artiklar.momskod = Moms.moms_id AND ";
	    $select .= " artnr='$extra_freight_artnr'";
	
	    $res = /* TODO: mysql_db_query replaced - needs manual review (was selecting db + querying) */ mysqli_query("cyberphoto", "$select");
	    $row = mysqli_fetch_object($res);
	
	    $name = $row->beskrivning;
	    $comment = $row->kommentar;
	    $outprice = $row->utpris;
	    $momsts = $row->momssats;
	
	    # Set variables
	    //$artnr = "frakt+";
	    $manufacturer = "";
	    $goodsvalue += $outprice;
	    $goodsvalueMoms += $outprice + $outprice * $momsts;
	    $moms += $outprice*$momsts;
	    
	    if ($extra_freight == 3 && !eregi("bekrafta", $PHP_SELF )) {
	 	
	    ?>
		<tr>
		  <td bgcolor="#ECECE6" colspan="6"><font face="Verdana, Arial" size="1">En vara i kundvagnen kräver leverans på pall. Se kostnad efter val av frakt och betalsätt (kostar normalt 599 kr)</font>
		</td>
		</tr>	    	
	    <?php
	    }
	    elseif ($extra_freight != 3) {	    
?>	    
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php echo $name . " " . $comment; ?></font></td>
		  <td bgcolor="#ECECE6" align="center"><font face="Verdana, Arial" size="1">1</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", $outprice);  ?>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f SEK", $outprice + $outprice * $momsts); ?>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">&nbsp;&nbsp;
		</font></td>
		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		</tr>
	    
	<?php } 
	endif; 

}

function check_lager($artnr, $fi = false) {
	global $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $package_stock, $bestallningsgrans, $queue, $est_delivery;
	
	include ("CConnect.php");

	
	$package_stock = NULL;  # clear package check
	# Make a check if freigt is already selected. 
	if (ereg("^frakt", $artnr))
		{ $freight_check = "1"; }

	if (ereg("pac$", $artnr)) # kollar tillgången om det är ett paket
		{   check_package();  }
	if (!(ereg("frakt",$artnr))) {

		if ($lagersaldo >= $count || ($package_stock == '1') )  { 
			?>
			<font size="1" face="Verdana, Arial" color="#385F39">
			<?php
			if ($fi == 'yes')
				print "arastossa"; 
			else {
				print "finns i lager";
			}
		}
		else {

			# Kolla hur många det finns på kö
			# antal på köp visas i $queue, nollställes först. 
			$queue = NULL;
			check_queue();
			$neededStock = $queue + $count;
			if ($bestallt >= $neededStock) {
				
				echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">".kollaLevtid ($artnr, $count, NULL, $fi);
				
			}

			else  { 
			?>
			
			<?php

				if (ereg("pac$",$artnr))
					{  
					
						if ($fi == 'yes')
							print "<a href=\"javascript:levStatusPaket('$artnr', '$count') \"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">lisää tietoja tästä</a>";
						else
							print "<a href=\"javascript:levStatusPaket('$artnr', '$count') \"><font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">klicka för info</a>";
					}
				elseif ($bestallningsgrans == '0') 
					{  
					
						if ($fi == 'yes')
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tilaustuote, normaali toimitusaika $lev_datum_normal";
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">beställningsvara, normal leveranstid $lev_datum_normal <a href=\"javascript:levForklaringb()\"></font><font size=1 face=\"Verdana\">(mer info)</font></a>";
					}
				else
					{ 
						if ($fi == 'yes')
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tilapäisesti loppunut, normaali toimitusaika $lev_datum_normal" ;
						else
							print "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\">tillfälligt slut, normal leveranstid $lev_datum_normal" ;
					}
				}
		}         
	}
}

function check_package() {

	global $artnr, $package_stock, $count;

	include ("CConnect.php");

	$select = "SELECT lagersaldo, antal ";
	$select .= "FROM Artiklar, Paketpriser WHERE Artiklar.artnr=Paketpriser.artnr_del ";
	$select .= "AND Paketpriser.artnr_paket = '$artnr' ";

	$res = mysqli_query($select);
	
	unset ($check);

	while ($row = mysqli_fetch_array($res))
	{
	extract($row);

	$check = $antal*$count;



	  if ($check > $lagersaldo) {
	    $package_stock = "";
	    break;
	  }
	  else {
	    $package_stock = "1";
	  }
	}

}


function check_queue() {
	global $artnr, $queue;
	
	include ("CConnect.php");
	
	$select = "SELECT antal FROM Orderposter WHERE bokad = '0' && artnr = '$artnr'";
	$res = mysqli_query($select);
	if (mysqli_num_rows($res) > '0') {	
		while ($row = mysqli_fetch_array($res) )   {
		extract ($row);
			$queue += $antal;

		}
	}
	else
		$queue = 0;
}

//-------------------------------------------------------------------------------------

function kollaLevtid ($artnr, $count, $ordernr, $fi)  { // artnr som skall kollas, antal samt inkommet datum på ordern
	global $fi;

	// kolla först inkommet för att få fram köplats. 
	// ange dagens datum om koll för kundvagnen 
	
	if ($ordernr == NULL) {
		$inkommetOur = date("Y-m-d H:i:s");
	}
	else {
		$select = "SELECT inkommet from Ordertabell WHERE ordernr = '$ordernr' ";

		$res = mysqli_query($select);

		if ($res)  {
			$row = mysqli_fetch_object($res);
			$inkommetOur = $row->inkommet;
		}
	}
	
	
	
	$totalNeed = 0;
	$ordernr = NULL; // tag bort värdet eftersom samma variable namn används nedan
	//echo $inkommetOur;	
	// räkna först ut hur många som står före
	$select =  "SELECT Orderposter.ordernr, Orderposter.antal, Ordertabell.inkommet FROM Orderposter, Ordertabell ";
	$select .= "WHERE Orderposter.ordernr = Ordertabell.ordernr AND ";
	$select .= "Orderposter.artnr = '$artnr' AND Orderposter.bokad = 0 ";
	$select .= "ORDER BY Ordertabell.inkommet ASC";
	
	//echo $select;
	
	$res = mysqli_query($select);
	
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)):
			extract ($row);
			if ($inkommet < $inkommetOur) {
				
				$totalNeed += $antal; // antalet som står före.
				//echo "$totalNeed, $ordernr";
			}
			else
				break;
				
		endwhile;
		
	}
	
	$totalNeed += $count; // lägg på de vi behöver till totala behovet (för att det skall 
			      // nå fram till "vår" köplats)
	
	$antal_sum = 0;
	$select = "SELECT inkopsnr, antal, levdatum, levererat FROM Inkopsposter WHERE artnr = '$artnr' "; 
	$select .= "AND antal != levererat ";
	$select .= "ORDER BY inkopsnr ASC ";
	$res = mysqli_query($select);
	
	//echo $select;
	if (mysqli_num_rows($res) > '0') {
		
		while ($row = mysqli_fetch_array($res)): 
			extract ($row);
			$antal_sum += ($antal - $levererat);
			
			if ($antal_sum >= $totalNeed) {
				$articleinfo = $levdatum;
				break;
			}
		//echo "$antal_sum $totalNeed.$articleinfo<br>";
		$antal = $levererat = 0;
		endwhile;	
	
	}
	else
	
	{
	
		if ($fi == 'yes')
			$articleinfo = "toimituspäivämäärä ei ole määritelty";
		else
			$articleinfo = "leveransdatum okänt";
	
	}

if ($articleinfo == "" || $articleinfo == "-") {
	if ($fi == 'yes')
		$articleinfo = "toimituspäivämäärä ei ole määritelty";
	else
		$articleinfo = "leveransdatum okänt";
}
return $articleinfo;
}


?>
