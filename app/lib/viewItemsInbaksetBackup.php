<%
function viewItemsInBasket($firstbasket, $fi) {
	global $kundvagn, $goodsvalue, $utpris, $goodsvalueMoms, $moms, $artnr, $count, $lagersaldo, $bestallt, $lev_datum, $lev_datum_normal, $bestallningsgrans, $betalsatt, 
	$PHP_SELF, $brev, $bestallningsgrans, $pack, $alltidBrev, $pallDelivery;	
	
	$freight_check = NULL;
	$pallDelivery = false; // visar om någon produkt innehåller frakttillägg. Används bl.a. till att tyngre produkter inte skall skickas som hempkaet
	$brev = true;
	$alltidBrev = false;	
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
			echo "1";
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
if (!(eregi("fraktbutik", $artnr)) && $firstbasket != 'nooutput' && !(eregi("presentkort", $artnr))) {

?>

				<tr>
				  <td valign="bottom" bgcolor="#ECECE6" width="262"><font size="1" face="Verdana"><?php echo $description; ?></font></td>
				  <td align="center" valign="bottom" bgcolor="#ECECE6" width="30"><font size="1" face="Verdana"><?php echo $count; ?></font></td>				  
				  <td align="center" valign="bottom" bgcolor="#ECECE6" width="81"><font size="1" color="#008000" face="Verdana">
				<?php		if (!(eregi("^frakt", $artnr))) { ?>
		
				<?php		$this->check_lager($artnr, $fi); ?>
				<?php		
							} else { ?>
						&nbsp;&nbsp;
				<?php 		} ?>
		
						</font></td>
				<td align="center" valign="bottom" bgcolor="#ECECE6" width="66"><font size="1" face="Verdana">

				<?php if (!(eregi("^frakt", $artnr))) { ?>

				<A HREF="javascript:modifyItemsInBasket('<?php echo $artnr; ?>', '1')">
				<font face="Verdana, Arial" size="1">
				<?php if ($fi == 'yes') { ?>
				muuta lukumäärä
				</A>
				<?php } else { ?>
				ändra antal</font></A>
				<?php } ?>
				<?php } else { ?>
				&nbsp;&nbsp;
				<?php }; ?>							
				</font></td>
				<td align="right" valign="bottom" bgcolor="#ECECE6" width="79"><font size="1" face="Verdana">
				<?php echo number_format($utpris*$count, 0, ',', ' ') . " SEK";  ?>
				</font></td>
				<td align="right" valign="bottom" bgcolor="#ECECE6" width="82"><font size="1" face="Verdana">
				<?php echo number_format(($utpris + $utpris * $momssats) * $count, 0, ',', ' ') . " SEK"; ?>
				
				</font></td>
				
				</tr>

		<?php  	
		} 
	}

	
	if ($extra_freight && $firstbasket == 'yes' && $firstbasket != 'nooutput' && $extra_freight != 999) {
	
	    if ($extra_freight == 1) $extra_freight_artnr = 'frakt+';
	    elseif ($extra_freight == 2) $extra_freight_artnr = 'frakt+2';
	    elseif ($extra_freight == 3) $extra_freight_artnr = 'frakt+3';
	    else $extra_freight_artnr = 'frakt+'; // för säkerhets skull
	    $select  = "select Artiklar.beskrivning, kommentar, utpris, Moms.momssats from Artiklar, Moms where Artiklar.momskod = Moms.moms_id AND ";
	    $select .= " artnr='$extra_freight_artnr'";
	
	    $res = mysqli_query($this->conn_my, $select);
	    $row = mysqli_fetch_object($res);
	
	    $name = $row->beskrivning;
	    $comment = $row->kommentar;
	    $outprice = $row->utpris;
	    $momsts = $row->momssats;
	
	    # Set variables
	    //$artnr = "frakt+";
	    if ($extra_freight == 3) {
	    	    
	    ?>
		<tr>
		  <td bgcolor="#ECECE6" colspan="6"><font face="Verdana, Arial" size="1">En vara i kundvagnen kräver leverans på pall. Se kostnad efter val av frakt och betalsätt (kostar normalt 599 kr)</font>
		</td>
		</tr>	    	
	    <?php
	    }
	    else {
	    $manufacturer = "";
	    $goodsvalue += $outprice;
?>	    
	    
		<tr>
		  <td bgcolor="#ECECE6"><font face="Verdana, Arial" size="1"><?php echo $name; ?></font></td>
		  <td bgcolor="#ECECE6" align="right"><font face="Verdana, Arial" size="1">1</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f", $outprice);  ?>
		</font></td>
		<td bgcolor="#ECECE6" align=right><font face="Verdana, Arial" size="1">
<?php		printf("%10.0f", $outprice + $outprice * $momsts); ?>
		</font></td>
		<td bgcolor="#ECECE6" align="left"><font size="1" face="Verdana, Arial">&nbsp;&nbsp;
		</font></td>
		<td bgcolor="#ECECE6">&nbsp;&nbsp;</td>
		</tr>
	<?php  
	    } 
	}
}

%>