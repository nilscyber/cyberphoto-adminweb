<%

function show_info() {
	
	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == "" || $arg == "-")
		echo "<i>Uppgift saknas</i>";
	else
		echo $arg;
	
	
}

function show_infoRound() {
	
	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == "" || $arg == "-")
		echo "<i>Uppgift saknas</i>";
	else {
		$arg = number_format ($arg, 1, ",", "");
		echo $arg;
	}
	
	
}
function show_infoRound2() {
	
	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == "" || $arg == "-")
		echo "<i>Uppgift saknas</i>";
	else {
		$arg = number_format ($arg, 3, ",", "");
		echo $arg;
	}
	
	
}

function show_info3() {
	
	$arg = func_get_arg (0);

	echo $arg;
	
	
}


function show_info2() {

	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == "")
		echo "<i>Uppgift saknas</i>";
	else {

		$arg = number_format ($arg, 0, ",", " ");
		echo $arg;
		}

}
function show_trueFalse() {

	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == -1)
		echo "ja";
	else 
		echo "nej";
}		


function show_pic() {

	$arg = func_get_arg (0);

	if (!(isset($arg))):
		echo "<i>Testbilder saknas</i>";
	
	elseif (!(eregi("\|", $arg))):
		echo $arg;
	else:
		$newargument = split ("\|", $arg);
		$n = count($newargument);
		echo "\n";

		for ($i=0; $i<$n; $i+=2) {

			$link = $newargument[$i];
			$name = $newargument[$i+1];
echo <<<eof
	<a href="$link">$name</a><br>\n
eof;

			}

	endif;

}


function show_digikam($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT Tekniska_data.artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.artnr_tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN Tekniska_data ON Tekniska_data.artnr = Artiklar.artnr WHERE Artiklar.kategori_id = 6 && ej_med=0 ";
	$select .= "AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "&& (beskrivning not like '%magic%') AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare2.php" method="get">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	        //visa bara om det är en php sida ELLER   en automatisk sida  OCH att den har en rad i tekniska data alternativt ett alternativt artnr för tekniska data (antar då att det finns artnr till det alternativta artikelnumret)
		if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->artnr_tekniska_data != "")   ):
			/*
			if ($row->artnr_tekniska_data != "")
				$artnr_tekniska_data = $row->artnr_tekniska_data;
			else
				$artnr_tekniska_data = $row->artnrTekn;
			*/	
			if (!($row->tillverkare == '.'))
				$description = $row->tillverkare . " ";
			
			$description .= $row->beskrivning;

			if (strlen($description) >= 30)
				$description = substr ($description, 0, 30) . "...";

			echo "<option value=\"" . $row->artnr . "\">" . $description. " </option>\n";

			unset($description);
		endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}

function show_projektor($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT artnr, beskrivning, tillverkare, ej_med, link FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id = '171' && ej_med = 0 AND (utgangen=0 OR lagersaldo > 0) AND (artnr not like '%pac') ";
	$select .= "order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_proj.php" method="get">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	#if ((eregi(".php$", $row->link ) )):
	
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
			$description .= $row->beskrivning;

		if (strlen($description) >= '27')
			$description = substr ($description, 0, 27) . "...";

		echo "<option value=\"$row->artnr\">$description</option>\n";

		unset($description);
	#endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}

function show_screenTV($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT Tekn_screenTV.artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.tekniska_data as tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN Tekn_screenTV ON Tekn_screenTV.artnr = Artiklar.artnr WHERE Artiklar.kategori_id = 328 && ej_med=0 ";
	$select .= "AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_n.php" method="get">
	<input type="hidden" name="art" value="screenTV">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->tekniska_data != "")   ):
	
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
			$description .= $row->beskrivning;

		if (strlen($description) >= '30')
			$description = substr ($description, 0, 30) . "...";

		echo "<option value=\"$row->artnr\">$description</option>\n";

		unset($description);
	endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}

function show_screen($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT Tekn_screen.artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.tekniska_data as tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN Tekn_screen ON Tekn_screen.artnr = Artiklar.artnr WHERE Artiklar.kategori_id = 330 && ej_med=0 ";
	$select .= "AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_n.php" method="get">
	<input type="hidden" name="art" value="screen">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->tekniska_data != "")   ):
	
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
			$description .= $row->beskrivning;

		if (strlen($description) >= '30')
			$description = substr ($description, 0, 30) . "...";

		echo "<option value=\"$row->artnr\">$description</option>\n";

		unset($description);
	endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}
/*
function show_tekniska_data() { # behövs inte, tas bort
	global $tekniska_artnr, $num_of_articles, $test_artnr;
///*
	$n = 5; # maximalt antal loopar
	if ($num_of_articles > 5)
		$n = 5
	else
		$n = $num_of_articles;
	
	for ($i = 1; $i <= $; $i++):
		$tekniska_artnr[$i] = func_get_arg($i-1);
	endfor;
	
	
	///*
	$n = 5; # maximalt antal loopar
	for ($i = 1; $i <= $n; $i++):
		if (@func_get_arg($i-1) == "")
		break;
		$tekniska_artnr[$i] = func_get_arg($i-1);
		
	endfor;

	$num_of_articles = $i-1;
	//	
	
	%>
		<td width="45%" valign="top" align="left"><font face="Verdana, Arial" size="1">
        <div align="center"><center><table border="0" cellpadding="2"
        cellspacing="1" width="100%">

		<tr>
		<td width="45%" valign="top" align="left"><font face="Verdana, Arial" size="1">

		<br>
		</font></td></tr>
				<%
				$comparepage = 'yes';
				include ("Tekniska_data_1.php");
				%>
		</table>
		</center></div>
		</font></td>
		<%
}
*/
function show_mp3($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT Tekn_mp3.artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.artnr_tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN Tekniska_data ON Tekniska_data.artnr = Artiklar.artnr WHERE Artiklar.kategori_id = 130 && ej_med=0 ";
	$select .= "AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_n.php" method="get">
	<input type="hidden" name="art" value="screenTV">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->tekniska_data != "")   ):
		if ($row->artnr_tekniska_data != "")
			$artnr_tekniska_data = $row->artnr_tekniska_data;
		else
			$artnr_tekniska_data = $artnrTekn;
	
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
			$description .= $row->beskrivning;

		if (strlen($description) >= '30')
			$description = substr ($description, 0, 30) . "...";

		echo "<option value=\"$row->artnr\">$description</option>\n";

		unset($description);
	endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}

function show_comp($tekniska_data, $extra_articles, $kategorier, $kat) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT " . $kat . ".artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.artnr_tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN " . $kat . " ON " . $kat . ".artnr = Artiklar.artnr WHERE " . $kategorier . " && ej_med=0 ";
	$select .= "AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";
	//echo $select;
	$res = mysqli_query($select);
	
%>
	<form action="/compare_n2.php" method="get">
	<input type="hidden" name="art" value="<% echo $kat; %>">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->tekniska_data != "")   ):
		/*
		if ($row->artnr_tekniska_data != "")
			$artnr_tekniska_data = $row->artnr_tekniska_data;
		else
			$artnr_tekniska_data = $row->artnrTekn;
		*/
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
		$description .= $row->beskrivning;

		if (strlen($description) >= 30)
			$description = substr ($description, 0, 30) . "...";

		echo "<option value=\"" . $row->artnr . "\">$description</option>\n";

		unset($description);
	endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}

function show_videokamera($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT artnr, beskrivning, tillverkare, ej_med, link FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.kategori_id = '57' OR Artiklar.kategori_id = 6 OR Artiklar.kategori_id = '213' ";
	$select .= "OR Artiklar.kategori_id = '52' OR Artiklar.kategori_id = '203' OR Artiklar.kategori_id = '51') ";
	$select .= "&& ej_med = '0' && (lagersaldo > 0 or utgangen = 0) && (artnr not like '%pac') ";
	$select .= "&& link not like '%shtml' ";
	$select .= "order by tillverkare ASC, beskrivning ASC ";	

	$select  = "SELECT Tekniska_data.artnr as artnrTekn, Artiklar.artnr as artnr, Artiklar.beskrivning, ";
	$select .= "Tillverkare.tillverkare, Artiklar.ej_med, Artiklar.link, Info_page.artnr as artnrInfo, ";
	$select .= "Info_page.artnr_tekniska_data FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "LEFT JOIN Info_page ON Info_page.artnr = Artiklar.artnr ";
	$select .= "LEFT JOIN Tekniska_data ON Tekniska_data.artnr = Artiklar.artnr WHERE ";
	$select .= "(Artiklar.kategori_id = '57' OR Artiklar.kategori_id = 6 OR Artiklar.kategori_id = 213 ";

	$select .= "OR Artiklar.kategori_id = 52 OR Artiklar.kategori_id = 203 OR Artiklar.kategori_id = 51) ";	
	$select .= "AND ej_med=0 AND (utgangen=0 OR lagersaldo > 0) && ((Artiklar.artnr not like '%pac') )";
	$select .= "AND (demo=0 OR lagersaldo > 0) order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_videokam.php" method="get">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ( ( eregi(".php$", $row->link )  || $row->artnrInfo != "") AND (   $row->artnrTekn != "" || $row->tekniska_data != "")   ):
		/*
		if ($row->artnr_tekniska_data != "")
			$artnr_tekniska_data = $row->artnr_tekniska_data;
		else
			$artnr_tekniska_data = $row->artnrTekn;
		*/
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
		$description .= $row->beskrivning;

		if (strlen($description) >= 30)
			$description = substr ($description, 0, 30) . "...";

		echo "<option value=\"" . $row->artnr . "\">$description</option>\n";

		unset($description);
	endif;
	endwhile;
	%>
	</select>
	<% if ($extra_articles != 'yes'): %>
	<input type="hidden" name="tekniska_artnr[2]" value="<% echo $tekniska_data; %>">
	<input type="hidden" name="num_of_articles" value="2">
	<% 
	else: 
	for ($i = 1; $i <= $num_of_articles; $i++):
	%>
	<input type="hidden" name="tekniska_artnr[<% echo $i+1; %>]" value="<% echo $tekniska_artnr[$i]; %>">
	<% endfor; %>
	<input type="hidden" name="num_of_articles" value="<% echo $i; %>">
	<% endif; %>
	<input type="hidden" name="comparepage" value="yes">
	</form>
	<%
}


%>
