<%

function show_info() {
	
	$arg = func_get_arg (0);

	#if (!(isset($arg)))
	if ($arg == "" || $arg == "-")
		echo "<i>Uppgift saknas</i>";
	else
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
	$select  = "SELECT artnr, beskrivning, tillverkare, ej_med, link FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id = '6' && ej_med=0 AND (utgangen=0 OR lagersaldo > 0) && ((artnr not like '%pac') || (artnr like '%pac' && artnr like 'QV2800UXpac'))";
	$select .= "&& (beskrivning not like '%magic%') order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare.php" method="get">
	<select size="1" name="tekniska_artnr[1]" onchange="this.form.submit()">
	<option value="nochoice">Välj här</option>
<%

	while ($row = mysqli_fetch_object($res)) :
	if ((eregi(".php$", $row->link ) )):
	
		if (!($row->tillverkare == '.'))
			$description  = $row->tillverkare . " ";
			$description .= $row->beskrivning;

		if (strlen($description) >= '27')
			$description = substr ($description, 0, 27) . "...";

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

function show_projektor($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT artnr, beskrivning, tillverkare, ej_med, link FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE Artiklar.kategori_id = '171' && ej_med = '0' && (artnr not like '%pac') ";
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

function show_tekniska_data() { # behövs inte, tas bort
	global $tekniska_artnr, $num_of_articles, $test_artnr;
/*
	$n = 5; # maximalt antal loopar
	if ($num_of_articles > 5)
		$n = 5
	else
		$n = $num_of_articles;
	
	for ($i = 1; $i <= $; $i++):
		$tekniska_artnr[$i] = func_get_arg($i-1);
	endfor;
	
	
	/*
	$n = 5; # maximalt antal loopar
	for ($i = 1; $i <= $n; $i++):
		if (@func_get_arg($i-1) == "")
		break;
		$tekniska_artnr[$i] = func_get_arg($i-1);
		
	endfor;

	$num_of_articles = $i-1;
	*/	
	
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

%>
