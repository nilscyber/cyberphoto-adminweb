<?php
if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89" || $_SERVER['REMOTE_ADDR'] == "192.168.1.98") {
	include ("CArticle.php");
} else {
	include ("CArticle.php");
}
?>