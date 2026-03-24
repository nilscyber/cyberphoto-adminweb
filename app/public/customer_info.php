<?php
require_once("CBusinessPartner.php");
$customer = new CBusinessPartner();
?>
<html>

<head>
<link rel="shortcut icon" href="https://admin.cyberphoto.se/admin.ico">
<title>Affärspartner <?php echo $knr; ?></title>
<STYLE>
body {
	/*background-image: url(/order/logo.jpg); */
	background-repeat: no-repeat; 
	background-attachment:fixed; 
	background-position:top right; 
	background-color: #FFFFFF; 
	font-family: Verdana; font-size: 11px; 
	margin-top: 5; 
	margin-right: 5; 
	margin-bottom: 5; 
	margin-left: 5; 

}
h1 {
	font-family: Arial; 
	font-size: 16px; 
	color: #0000FF ;
}
.bold { font-weight:bold }
table           
{
 background-color: #FFFFFF; 
 border: 1px solid grey;
}
td           
{
 padding: 2px;
 font-family: Verdana, Arial, Helvetica;
 font-size: 11px
}
.chat {
	background-image: url(/order/chat.png); 
	background-repeat: no-repeat; 
	background-position:top left; 
	padding-left: 20px;
	font-weight:bold;
	margin-top: 20px;
}
.tbtgcolorgreen { color:#009933; font: 12px Verdana; font-weight:bold }
.tbtgcolorred { color:#CC0000; font: 12px Verdana; font-weight:bold }
.search_line1 {
    background-color: #EDEDED;
}
.search_line2 {
    background-color: #FFFFFF;
}
</style>
</head>

<body>
<h1>Uppgifter om affärspartner <?php echo $knr; ?></h1>
<?php
if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
	$customer->getAllCustomerInfo($knr); 
} else {
	$customer->getAllCustomerInfo($knr); 
}
?>
</body>

</html>