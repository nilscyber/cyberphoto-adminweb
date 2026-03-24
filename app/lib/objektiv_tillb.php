<%
include ("CConnect.php");

$select = "SELECT filterd, motljsk FROM Artiklar WHERE artnr='$visa_tillb'";
$res = mysqli_query($select);
$row = mysqli_fetch_array($res);
if ($row)
extract($row);

#echo "sture: " . $filterd . " " .$motljsk;
include ("CArticle.php");
# The header of the pricelist

if (($motljsk != "") || ($filterd != "")): 
%>
<div align="center"><center>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<%

	include ("PricelistHeader.php");
	
	$criteria = "WHERE ";
	
	if (eregi("\++", $motljsk)) {
		eregi("([_0-9a-z\-]+)(\040*\++\040*)(.*)", $motljsk, $matches);
		$solsk[1] = $matches[1];
		$solsk[2] = $matches[3];
		$criteria .= "(Artiklar.kategori_id = '16' && (beskrivning like '%$solsk[1]%' || beskrivning like '%$solsk[2]%') ) ";
		$earlier = "yes";
	}
	
	elseif ($motljsk != "") {
		$criteria .= "(Artiklar.kategori_id = '16' && (beskrivning like '%$motljsk%')) ";
		$earlier = "yes";
		}
	
	
	if (eregi("\++", $filterd)) {
		eregi("([0-9]+)(\040*\++\040*)(.*)", $filterd, $matches);
		$filter[1] = $matches[1];
		$filter[2] = $matches[3];
		if ($earlier == 'yes')
		$criteria .= "|| ";
		$criteria .= "(Artiklar.kategori_id = '20' && (beskrivning like '%$filter[1]%' || beskrivning like '%$filter[2]%')) ";
			
	}
	elseif ($filterd != "") {
		if ($earlier == 'yes')
		$criteria .= "|| ";
		
		$criteria .= "(Artiklar.kategori_id = '20' && beskrivning like '%$filterd%') ";
		
	}
	
	$criteria .= "&& ej_med = '0' order by kategori DESC, beskrivning ASC ";
	
	#echo $criteria;

	$headline = "";

	# The pricelist code
	include ("Pricelist.php");
%>
</table></center></div>
<% endif; %>