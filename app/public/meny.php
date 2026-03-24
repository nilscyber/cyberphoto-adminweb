    <table border="0" cellpadding="2" cellspacing="0" width="100%">
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Administrera</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/front\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="front.php">Frontsidan</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/banner\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="banner.php">Övriga banners</a></font></td>
      </tr>
      <?php if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") { ?>
      <tr>
        <td width="100%"><font face="Verdana" size="1">&nbsp;</font></td>
      </tr>
      <tr>
        <td width="100%"><b><font face="Verdana" size="1">Sjabo utveckling</font></b></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/poll\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="poll.php">Webbundersökning</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/ordinary_question\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="ordinary_question.php">Vanliga frågor</a></font></td>
      </tr>
	  <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/tomteverkstan2010\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="tomteverkstan2010.php">Tomteönskningar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/promotioncode\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="promotioncode.php">Rabattkoder</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/delbetalningar\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="delbetalningar.php">Avbetalningar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/incoming\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="incoming.php">Beställda varor</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/articles_in_stock\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="articles_in_stock.php">Surdegar</a></font></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a <?php if (preg_match("/turnover\.php/i", $_SERVER['PHP_SELF'])) { ?>class="current"<?php } ?> href="turnover.php">Aktuell omsättning</a></font></td>
      </tr>
	  <?php } ?>
      <tr>
        <td width="100%"><hr noshade color="#CCCCCC" size="1"></td>
      </tr>
      <tr>
        <td width="100%"><font face="Verdana" size="1"><a href="/order/admin/">Till startsidan</a></font></td>
      </tr>
    </table>
