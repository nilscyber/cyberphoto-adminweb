<?php
include("connections.php");

function getCampaignIdData($kampanjid) {
global $conn_my;	
$select  = "SELECT * FROM discountCodes WHERE cnt = '" . $kampanjid . "' ";
$res = mysqli_query($conn_my, $select);

	extract(mysqli_fetch_array($res));

	// echo $validFrom . "<br>";
	// echo $validDate . "<br>";

	if (strtotime($validFrom) < strtotime("now") && strtotime($validDate) > strtotime("now") && getCampaignArticle($cnt)) {
	// if (mssql_num_rows($res) > 0) {
	// if (mssql_num_rows($res) > 0 && getCampaignArticle($cnt)) {

	return true;

	} else {

	return false;

	}
}	

function getCampaignArticle($kampanjid) {
global $conn_my;	
$select  = "SELECT includedArticle FROM discountCodeArticles WHERE dscntId = '" . $kampanjid . "' ";
$res = mysqli_query($conn_my, $select);

	extract(mysqli_fetch_array($res));
	// echo $includedArticle . "<br>";

	// if (mssql_num_rows($res) > 0) {
	if (mysqli_num_rows($res) > 0 && getCampaignArticleStore($includedArticle)) {
	
	return true;

	} else {

	return false;

	}
}	

function getCampaignArticleStore($artnr) {
	global $conn_my;
$select  = "SELECT lagersaldo FROM Artiklar WHERE artnr = '" . $artnr . "' ";
$res = mysqli_query($conn_my, $select);

	if (mysqli_num_rows($res) > 0) {

	extract(mysqli_fetch_array($res));

	// echo $lagersaldo . "<br>";
	
		if ($lagersaldo > 0) {
	
		return true;

		} else {

		return false;

		}
	}
}	

?>
