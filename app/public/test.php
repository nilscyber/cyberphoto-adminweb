<?php
require_once 'src/apiClient.php';
require_once 'src/contrib/apiPlusService.php';
session_start();

$client = new apiClient();
$client->setApplicationName('Google+ PHP Starter Application');
// Visit https://code.google.com/apis/console?api=plus to generate your
// client id, client secret, and to register your redirect uri.
// $client->setClientId('986475588225.apps.googleusercontent.com');
// $client->setClientSecret('EkfJuBDXQ8zs5LxyZue7vV3y');
// $client->setRedirectUri('http://www.cyberphoto.se/order/admin/test.php');
// $client->setDeveloperKey('AIzaSyAlto41PXqtmEC6aE4iP5uJVWIOKtso2so');
$plus = new apiPlusService($client);

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $optParams = array('maxResults' => 100);
  $activities = $plus->activities->listActivities('me', 'public', $optParams);

  print 'Your Activities: <pre>' . print_r($activities, true) . '</pre>';

  // The access token may have been updated lazily.
  $me = $plus->people->get('me');
  $_SESSION['token'] = $client->getAccessToken();
  
  if(isset($me))
	{ 
	$_SESSION['gplusdata'] = $me;
	// header("location: home.php");
	} 
  
} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}

if (!isset($_SESSION['gplusdata'])) 
{
// Redirection to home page
// header("location: index.php");
}
else
{
$me = $_SESSION['gplusdata'];
// print_r($me);
echo $me['id'] . "<br>";
echo $me['url'] . "<br>";
/*
echo "<img src='$me['image']['url']; ' />";
echo "Name: $me['displayName']; ";
echo "Gplus Id:  $me['id']";
echo "Male: $me['gender']";
echo "Relationship: $me['relationshipStatus']";
echo "Location: $me['placesLived'][0]['value']";
echo "Tagline: $me['tagline'];
print "<a class='logout' href='index.php?logout'>Logout</a> ";
*/
}
?>