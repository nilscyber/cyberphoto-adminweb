<?php
require_once("CCheckIpNumber.php");
require_once("Locs.php");
include_once 'Db.php';

Class CSeo {

	// var $conn_my;

	function __construct() {

		// $this->conn_my = @mysqli_connect(getenv('DB_HOST') ?: 'db', getenv('DB_USER') ?: 'appuser', getenv('DB_PASS') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');
		// $this->conn_my = @mysqli_connect(getenv('DB_HOST_MASTER') ?: 'db', getenv('DB_USER_MASTER') ?: 'appuser', getenv('DB_PASS_MASTER') ?: 'apppass');
		// @mysqli_select_db($this->conn_my, getenv('DB_NAME') ?: 'cyberphoto');

	}

	function getIfSeoOnPage($page) {
		global $fi, $sv, $no, $article, $q;
		$page = Tools::sql_inject_clean($page);
		if ($page == "") { // det kan bara inträffa om vi är på toppen. Dvs. www.cyberphoto.se och inget annat
			$page = "/";
		}
	
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.seo ";
		if ($fi && !$sv) {
			$select .= "WHERE seoPageLinc_FI = '" . $page . "' ";
		} elseif ($no) {
			$select .= "WHERE seoPageLinc_NO = '" . $page . "' ";
		} else {
			$select .= "WHERE seoPageLinc_SE = '" . $page . "' ";
		}
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select . "<br>";
			echo $_SERVER['REQUEST_URI'];
		}

		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		if ($fi && !$sv) {
			$page = $row->seoPageLinc_FI;
		} elseif ($no) {
			$page = $row->seoPageLinc_NO;
		} else {
			$page = $row->seoPageLinc_SE;
		}

		if (mysqli_num_rows($res) > 0) {
			echo "<div class=\"left5 bottom5\"><a class=\"\" href=\"javascript:winPopupCenter(720, 900, 'http://" . $_SERVER["HTTP_HOST"] . "/order/admin/seo_change.php?page=" . $page . "&change=" . $row->seoID . "');\">Ändra SEO data</a></div>\n";
		} else {
			if ($sv && !$fi && !$no) {
				if (preg_match("/info\.php/i", $_SERVER['PHP_SELF'])) {
					echo "<div class=\"left5 bottom5\"><a class=\"\" href=\"javascript:winPopupCenter(720, 900, 'http://" . $_SERVER["HTTP_HOST"] . "/order/admin/seo_change.php?page=" . $_SERVER['REQUEST_URI'] . "&create=yes&name=" . $article . "');\">Skapa SEO data</a></div>\n";
				} elseif (preg_match("/search\.php/i", $_SERVER['PHP_SELF'])) {
					echo "<div class=\"left5 bottom5\"><a class=\"\" href=\"javascript:winPopupCenter(720, 900, 'http://" . $_SERVER["HTTP_HOST"] . "/order/admin/seo_change.php?page=" . $_SERVER['REQUEST_URI'] . "&create=yes&name=" . $q . "');\">Skapa SEO data</a></div>\n";
				} else {
					echo "<div class=\"left5 bottom5\"><a class=\"\" href=\"javascript:winPopupCenter(720, 900, 'http://" . $_SERVER["HTTP_HOST"] . "/order/admin/seo_change.php?page=" . $_SERVER['REQUEST_URI'] . "&create=yes');\">Skapa SEO data</a></div>\n";
				}
			}
		}
	
	}
	
	function getSeoInfo($page) {
		global $fi, $sv, $no;
		$page = Tools::sql_inject_clean($page);
		if ($page == "") { // det kan bara inträffa om vi är på toppen. Dvs. www.cyberphoto.se och inget annat
			$page = "/";
		}
	
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.seo ";
		if ($fi && !$sv) {
			$select .= "WHERE seoPageLinc_FI = '" . $page . "' ";
		} elseif ($no) {
			$select .= "WHERE seoPageLinc_NO = '" . $page . "' ";
		} else {
			$select .= "WHERE seoPageLinc_SE = '" . $page . "' ";
		}
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		return $rows;
	
	}

	function getSeoInfoChange($ID) {
		global $fi, $sv, $no;
		$ID = (int)$ID;
		if ($page == "") { // det kan bara inträffa om vi är på toppen. Dvs. www.cyberphoto.se och inget annat
			$page = "/";
		}
	
		$select  = "SELECT * ";
		$select .= "FROM cyberphoto.seo ";
		$select .= "WHERE seoID = '" . $ID . "' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		
		return $rows;
	
	}

	function addSeoData() {
		global $seoPage, $seoReplace, $seoPageLinc_SE, $seoPageLinc_FI, $seoPageLinc_NO, $seoTitle_SE, $seoTitle_FI, $seoTitle_NO,
		$seoCanonical_SE, $seoCanonical_FI, $seoCanonical_NO,
		$seoMetaDescription_SE,	$seoMetaDescription_FI,	$seoMetaDescription_NO, $seoH1_SE, $seoH1_FI, $seoH1_NO,
		$seoH2_SE, $seoH2_FI, $seoH2_NO, $seoBody_SE, $seoBody_FI, $seoBody_NO, $seoBody2_SE, $seoBody2_FI, $seoBody2_NO;

		$updt  = "INSERT INTO cyberphoto.seo ";
		$updt .= "( ";
		$updt .= "seoPage, ";
		if ($seoReplace != "") {
			$updt .= "seoReplace, ";
		}
		if ($seoCanonical_SE != "") {
			$updt .= "seoCanonical_SE, ";
		}
		if ($seoCanonical_FI != "") {
			$updt .= "seoCanonical_FI, ";
		}
		if ($seoCanonical_NO != "") {
			$updt .= "seoCanonical_NO, ";
		}
		$updt .= "seoPageLinc_SE, ";
		if ($seoPageLinc_FI != "") {
			$updt .= "seoPageLinc_FI, ";
		}
		if ($seoPageLinc_NO != "") {
			$updt .= "seoPageLinc_NO, ";
		}
		if ($seoTitle_SE != "") {
			$updt .= "seoTitle_SE, ";
		}
		if ($seoTitle_FI != "") {
			$updt .= "seoTitle_FI, ";
		}
		if ($seoTitle_NO != "") {
			$updt .= "seoTitle_NO, ";
		}
		if ($seoMetaDescription_SE != "") {
			$updt .= "seoMetaDescription_SE, ";
		}
		if ($seoMetaDescription_FI != "") {
			$updt .= "seoMetaDescription_FI, ";
		}
		if ($seoMetaDescription_NO != "") {
			$updt .= "seoMetaDescription_NO, ";
		}
		if ($seoH1_SE != "") {
			$updt .= "seoH1_SE, ";
		}
		if ($seoH1_FI != "") {
			$updt .= "seoH1_FI, ";
		}
		if ($seoH1_NO != "") {
			$updt .= "seoH1_NO, ";
		}
		if ($seoH2_SE != "") {
			$updt .= "seoH2_SE, ";
		}
		if ($seoH2_FI != "") {
			$updt .= "seoH2_FI, ";
		}
		if ($seoH2_NO != "") {
			$updt .= "seoH2_NO, ";
		}
		if ($seoBody_SE != "") {
			$updt .= "seoBody_SE, ";
		}
		if ($seoBody_FI != "") {
			$updt .= "seoBody_FI, ";
		}
		if ($seoBody_NO != "") {
			$updt .= "seoBody_NO, ";
		}
		if ($seoBody2_SE != "") {
			$updt .= "seoBody2_SE, ";
		}
		if ($seoBody2_FI != "") {
			$updt .= "seoBody2_FI, ";
		}
		if ($seoBody2_NO != "") {
			$updt .= "seoBody2_NO, ";
		}
		$updt .= "seoAddBy, ";
		$updt .= "seoAddTime ";
		$updt .= ") ";
		$updt .= "VALUES ";
		$updt .= "( ";
		$updt .= "'" . trim($seoPage) . "', ";
		if ($seoReplace != "") {
			$updt .= "'" . trim($seoReplace) . "', ";
		}
		if ($seoCanonical_SE != "") {
			$updt .= "'" . trim($seoCanonical_SE) . "', ";
		}
		if ($seoCanonical_FI != "") {
			$updt .= "'" . trim($seoCanonical_FI) . "', ";
		}
		if ($seoCanonical_NO != "") {
			$updt .= "'" . trim($seoCanonical_NO) . "', ";
		}
		$updt .= "'" . trim($seoPageLinc_SE) . "', ";
		if ($seoPageLinc_FI != "") {
			$updt .= "'" . trim($seoPageLinc_FI) . "', ";
		}
		if ($seoPageLinc_NO != "") {
			$updt .= "'" . trim($seoPageLinc_NO) . "', ";
		}
		if ($seoTitle_SE != "") {
			$updt .= "'" . trim($seoTitle_SE) . "', ";
		}
		if ($seoTitle_FI != "") {
			$updt .= "'" . trim($seoTitle_FI) . "', ";
		}
		if ($seoTitle_NO != "") {
			$updt .= "'" . trim($seoTitle_NO) . "', ";
		}
		if ($seoMetaDescription_SE != "") {
			$updt .= "'" . trim($seoMetaDescription_SE) . "', ";
		}
		if ($seoMetaDescription_FI != "") {
			$updt .= "'" . trim($seoMetaDescription_FI) . "', ";
		}
		if ($seoMetaDescription_NO != "") {
			$updt .= "'" . trim($seoMetaDescription_NO) . "', ";
		}
		if ($seoH1_SE != "") {
			$updt .= "'" . trim($seoH1_SE) . "', ";
		}
		if ($seoH1_FI != "") {
			$updt .= "'" . trim($seoH1_FI) . "', ";
		}
		if ($seoH1_NO != "") {
			$updt .= "'" . trim($seoH1_NO) . "', ";
		}
		if ($seoH2_SE != "") {
			$updt .= "'" . trim($seoH2_SE) . "', ";
		}
		if ($seoH2_FI != "") {
			$updt .= "'" . trim($seoH2_FI) . "', ";
		}
		if ($seoH2_NO != "") {
			$updt .= "'" . trim($seoH2_NO) . "', ";
		}
		if ($seoBody_SE != "") {
			$updt .= "'" . trim($seoBody_SE) . "', ";
		}
		if ($seoBody_FI != "") {
			$updt .= "'" . trim($seoBody_FI) . "', ";
		}
		if ($seoBody_NO != "") {
			$updt .= "'" . trim($seoBody_NO) . "', ";
		}
		if ($seoBody2_SE != "") {
			$updt .= "'" . trim($seoBody2_SE) . "', ";
		}
		if ($seoBody2_FI != "") {
			$updt .= "'" . trim($seoBody2_FI) . "', ";
		}
		if ($seoBody2_NO != "") {
			$updt .= "'" . trim($seoBody2_NO) . "', ";
		}
		$updt .= "'" . $_COOKIE['login_mail'] . "', ";
		$updt .= "now() ";
		$updt .= ") ";
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}
	
	function updateSeoData($addid) {
		global $seoPage, $seoReplace, $seoPageLinc_SE, $seoPageLinc_FI, $seoPageLinc_NO, $seoTitle_SE, $seoTitle_FI, $seoTitle_NO,
		$seoCanonical_SE, $seoCanonical_FI, $seoCanonical_NO,
		$seoMetaDescription_SE,	$seoMetaDescription_FI,	$seoMetaDescription_NO, $seoH1_SE, $seoH1_FI, $seoH1_NO,
		$seoH2_SE, $seoH2_FI, $seoH2_NO, $seoBody_SE, $seoBody_FI, $seoBody_NO, $seoBody2_SE, $seoBody2_FI, $seoBody2_NO;

		$updt  = "UPDATE cyberphoto.seo ";		
		$updt .= "SET ";
		if ($seoPage != "") {
			$updt .= "seoPage = '" . trim($seoPage) . "', ";
		} else {
			$updt .= "seoPage = NULL, ";
		}
		if ($seoReplace != "") {
			$updt .= "seoReplace = '" . trim($seoReplace) . "', ";
		} else {
			$updt .= "seoReplace = NULL, ";
		}
		if ($seoCanonical_SE != "") {
			$updt .= "seoCanonical_SE = '" . trim($seoCanonical_SE) . "', ";
		} else {
			$updt .= "seoCanonical_SE = NULL, ";
		}
		if ($seoCanonical_FI != "") {
			$updt .= "seoCanonical_FI = '" . trim($seoCanonical_FI) . "', ";
		} else {
			$updt .= "seoCanonical_FI = NULL, ";
		}
		if ($seoCanonical_NO != "") {
			$updt .= "seoCanonical_NO = '" . trim($seoCanonical_NO) . "', ";
		} else {
			$updt .= "seoCanonical_NO = NULL, ";
		}
		if ($seoPageLinc_SE != "") {
			$updt .= "seoPageLinc_SE = '" . trim($seoPageLinc_SE) . "', ";
		} else {
			$updt .= "seoPageLinc_SE = NULL, ";
		}
		if ($seoPageLinc_FI != "") {
			$updt .= "seoPageLinc_FI = '" . trim($seoPageLinc_FI) . "', ";
		} else {
			$updt .= "seoPageLinc_FI = NULL, ";
		}
		if ($seoPageLinc_NO != "") {
			$updt .= "seoPageLinc_NO = '" . trim($seoPageLinc_NO) . "', ";
		} else {
			$updt .= "seoPageLinc_NO = NULL, ";
		}
		if ($seoTitle_SE != "") {
			$updt .= "seoTitle_SE = '" . trim($seoTitle_SE) . "', ";
		} else {
			$updt .= "seoTitle_SE = NULL, ";
		}
		if ($seoTitle_FI != "") {
			$updt .= "seoTitle_FI = '" . trim($seoTitle_FI) . "', ";
		} else {
			$updt .= "seoTitle_FI = NULL, ";
		}
		if ($seoTitle_NO != "") {
			$updt .= "seoTitle_NO = '" . trim($seoTitle_NO) . "', ";
		} else {
			$updt .= "seoTitle_NO = NULL, ";
		}
		if ($seoMetaDescription_SE != "") {
			$updt .= "seoMetaDescription_SE = '" . trim($seoMetaDescription_SE) . "', ";
		} else {
			$updt .= "seoMetaDescription_SE = NULL, ";
		}
		if ($seoMetaDescription_FI != "") {
			$updt .= "seoMetaDescription_FI = '" . trim($seoMetaDescription_FI) . "', ";
		} else {
			$updt .= "seoMetaDescription_FI = NULL, ";
		}
		if ($seoMetaDescription_NO != "") {
			$updt .= "seoMetaDescription_NO = '" . trim($seoMetaDescription_NO) . "', ";
		} else {
			$updt .= "seoMetaDescription_NO = NULL, ";
		}
		if ($seoH1_SE != "") {
			$updt .= "seoH1_SE = '" . trim($seoH1_SE) . "', ";
		} else {
			$updt .= "seoH1_SE = NULL, ";
		}
		if ($seoH1_FI != "") {
			$updt .= "seoH1_FI = '" . trim($seoH1_FI) . "', ";
		} else {
			$updt .= "seoH1_FI = NULL, ";
		}
		if ($seoH1_NO != "") {
			$updt .= "seoH1_NO = '" . trim($seoH1_NO) . "', ";
		} else {
			$updt .= "seoH1_NO = NULL, ";
		}
		if ($seoH2_SE != "") {
			$updt .= "seoH2_SE = '" . trim($seoH2_SE) . "', ";
		} else {
			$updt .= "seoH2_SE = NULL, ";
		}
		if ($seoH2_FI != "") {
			$updt .= "seoH2_FI = '" . trim($seoH2_FI) . "', ";
		} else {
			$updt .= "seoH2_FI = NULL, ";
		}
		if ($seoH2_NO != "") {
			$updt .= "seoH2_NO = '" . trim($seoH2_NO) . "', ";
		} else {
			$updt .= "seoH2_NO = NULL, ";
		}
		if ($seoBody_SE != "") {
			$updt .= "seoBody_SE = '" . trim($seoBody_SE) . "', ";
		} else {
			$updt .= "seoBody_SE = NULL, ";
		}
		if ($seoBody_FI != "") {
			$updt .= "seoBody_FI = '" . trim($seoBody_FI) . "', ";
		} else {
			$updt .= "seoBody_FI = NULL, ";
		}
		if ($seoBody_NO != "") {
			$updt .= "seoBody_NO = '" . trim($seoBody_NO) . "', ";
		} else {
			$updt .= "seoBody_NO = NULL, ";
		}
		if ($seoBody2_SE != "") {
			$updt .= "seoBody2_SE = '" . trim($seoBody2_SE) . "', ";
		} else {
			$updt .= "seoBody2_SE = NULL, ";
		}
		if ($seoBody2_FI != "") {
			$updt .= "seoBody2_FI = '" . trim($seoBody2_FI) . "', ";
		} else {
			$updt .= "seoBody2_FI = NULL, ";
		}
		if ($seoBody2_NO != "") {
			$updt .= "seoBody2_NO = '" . trim($seoBody2_NO) . "', ";
		} else {
			$updt .= "seoBody2_NO = NULL, ";
		}
		$updt .= "seoUpdateTime = now(), ";
		$updt .= "seoUpdateBy = '" . $_COOKIE['login_mail'] . "' ";
		$updt .= "WHERE seoID = '" . $addid . "' ";		
		
		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);
		
	}

	function getValidCategory() {
		global $seoReplace;

		$select  = "SELECT kategori_id, kategori  ";
		$select .= "FROM cyberphoto.Kategori  ";
		$select .= "WHERE visas = -1 ";
		$select .= "ORDER BY kategori ASC ";

		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

			while ($row = mysqli_fetch_object($res)) {
			
				echo "<option value=\"" . $row->kategori_id . "\"";
					
				if ($seoReplace == $row->kategori_id) {
					echo " selected";
				}
					
				echo ">" . $row->kategori . " (" . $row->kategori_id . ")</option>\n";
			
			}

	}

	static function getLincIfSeoReplacePage($page_id) {
		global $fi, $sv, $no;
		$page_id = (int)$page_id;
		unset($pageLinc);
	
		$select  = "SELECT seoPageLinc_SE, seoPageLinc_FI, seoPageLinc_NO  ";
		$select .= "FROM cyberphoto.seo ";
		$select .= "WHERE seoReplace = '" . $page_id . "' ";
	
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select;
		}

		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);
		
		if ($fi && !$sv) {
			$pageLinc = $row->seoPageLinc_FI;
		} elseif ($no) {
			if ($row->seoPageLinc_NO == "" && $row->seoPageLinc_SE != "") {
				$pageLinc = $row->seoPageLinc_SE;
			} else {
				$pageLinc = $row->seoPageLinc_NO;
			}
		} else {
			$pageLinc = $row->seoPageLinc_SE;
		}
		
		return $pageLinc;
	
	}
	
}

?>
