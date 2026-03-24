<%

function show_info() {

$arg = func_get_arg (0);

if (!(isset($arg)))
echo "-";
else
echo $arg;

}

function show_pic() {

$arg = func_get_arg (0);

if (!(isset($arg))):
echo "-";

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


function show_digikam() {

global $tekniska_data;
$select  = "SELECT artnr, beskrivning, tillverkare, ej_med FROM Artiklar ";
$select .= "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ";
$select .= "LEFT JOIN Kategori ON Artiklar.kategori_id=Kategori.kategori_id ";
$select .= "WHERE Artiklar.kategori_id = '6' && ej_med = '0' order by tillverkare ASC, beskrivning ASC ";

$res = mysqli_query($select);

%>
	<form action="/compare.php" method="get">
	<select size="1" name="test_artnr2" onchange="this.form.submit()">
	<option value="">Välj här</option>
<%

while ($row = mysqli_fetch_object($res)) :

	if (!($row->tillverkare == '.'))
		$description  = $row->tillverkare . " ";
		$description .= $row->beskrivning;

	if (strlen($description) >= '27')
		$description = substr ($description, 0, 27) . "...";

	echo "<option value=\"$row->artnr\">$description</option>\n";

	unset($description);
endwhile;
%>
</select>
<input type="hidden" name="test_artnr" value="<% echo $tekniska_data; %>">
<input type="submit" value="-->">
</form>
<%
}
%>