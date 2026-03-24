<?php
	// echo $_SERVER['HTTP_HOST'];
	// exit;
	
	session_start();
	if (isset($_REQUEST['from_page'])) {
		$_SESSION['admin_from_product'] = true;
		$_SESSION['admin_rem_page'] = $_REQUEST['from_page'];
	}
	if (isset($_REQUEST['reset'])) {
		unset($_SESSION['token']);
		// unset($_SESSION['admin_ok']);
		setcookie('login_ok', '', time() - 60, '/');
		setcookie('login_name', '', time() - 60, '/');
		setcookie('login_userid', '', time() - 60, '/');
		setcookie('login_mail', '', time() - 60, '/');
		// unset($_SESSION['admin_info']);
		// unset($_SESSION['admin_userid']);
		header('Location: ' . $_SESSION['admin_rem_page']); //redirect user back to page
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login with Google Account</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link href="mycss.css" rel="stylesheet">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<?php if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") { ?>
	<meta name="google-signin-client_id" content="641591682336-jqcn7dkie5tt5s472oi6lpcusvhe79dc.apps.googleusercontent.com" >
	<?php } else { ?>
	<meta name="google-signin-client_id" content="641591682336-th8ua58mvcaobsmkopnpphnbm0rpe8fd.apps.googleusercontent.com" >
	<?php } ?>
</head>

<body>

<div class="g-signin2" data-onsuccess="onSignIn"></div>

<script type="text/javascript">
	function onSignIn(googleUser) {
	  var profile = googleUser.getBasicProfile();


      if(profile){
          $.ajax({
                type: 'POST',
                url: 'login_pro.php',
                data: {id:profile.getId(), name:profile.getName(), email:profile.getEmail()}
            }).done(function(data){
                console.log(data);
				<?php if ($_SERVER['HTTP_HOST'] == "admin.cyberphoto.se") { ?>
				window.location.href = '<?php echo $_SESSION['admin_rem_page']; ?>';
				<?php } else { ?>
				window.location.href = '<?php echo $_SESSION['admin_rem_page']; ?>';
				<?php } ?>
            }).fail(function() { 
                alert( "Posting failed." );
            });
      }


    }
</script>

</body>
</html>