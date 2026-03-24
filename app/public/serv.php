<?php
echo "1. " . $_SERVER['SERVER_ADDR'];
echo "<br>2. " . $_SERVER["HTTP_X_FORWARDED_FOR"] . "";
echo "<br>3. " .$_SERVER["REMOTE_ADDR"] . "";
?>
