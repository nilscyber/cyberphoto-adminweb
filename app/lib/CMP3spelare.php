<%
function show_videokamera($tekniska_data, $extra_articles) {

	global $num_of_articles, $tekniska_artnr;
	$select  = "SELECT artnr, beskrivning, tillverkare, ej_med, link FROM Artiklar ";
	$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
	$select .= "WHERE (Artiklar.kategori_id = '57' OR Artiklar.kategori_id = '7' OR Artiklar.kategori_id = '213' ";
	$select .= "OR Artiklar.kategori_id = '52' OR Artiklar.kategori_id = '203' OR Artiklar.kategori_id = '51') ";
	$select .= "&& ej_med = '0' && (lagersaldo > 0 or utgangen = 0) && (artnr not like '%pac') ";
	$select .= "&& link not like '%shtml' ";
	$select .= "order by tillverkare ASC, beskrivning ASC ";

	$res = mysqli_query($select);
	
%>
	<form action="/compare_videokam.php" method="get">
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
%>