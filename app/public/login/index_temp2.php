<?php


	session_start();
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
				// window.location.href = 'home.php';
            }).fail(function() { 
                alert( "Posting failed." );
            });
      }


    }
</script>

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