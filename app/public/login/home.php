<?php


	session_start();

	$_SESSION["id"] = $_POST["id"];
	$_SESSION["name"] = $_POST["name"];
	$_SESSION["email"] = $_POST["email"];

	/*
	setcookie('login_ok', 'true', time() + 36000, '/');
	setcookie('login_name', $_SESSION["name"], time() + 36000, '/');
	setcookie('login_userid', $intern->findUserId($_SESSION["email"]), time() + 36000, '/');
	setcookie('login_mail', $email, time() + 36000, '/');
	*/

?>
<!DOCTYPE html>
<html>
<head>
	<title>PHP and MySQL - Login with Google Account Example</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link href="mycss.css" rel="stylesheet">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<meta name="google-signin-client_id" content="641591682336-th8ua58mvcaobsmkopnpphnbm0rpe8fd.apps.googleusercontent.com" >
</head>
<html>
<head>
	<title>sdfsdf</title>
</head>
<body>


<h1>Website Home Page</h1>
<p><strong>Id: </strong><?php echo $_SESSION['id'];  ?></p>
<p><strong>Name: </strong><?php echo $_SESSION['name'];  ?></p>
<p><strong>Email: </strong><?php echo $_SESSION['email'];  ?></p>


<a href="#" onclick="signOut();">Sign out</a>
<script>
  function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
  }
</script>
</body>
</html>