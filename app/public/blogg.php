<?php 
	include_once("top.php");
	include_once("header.php");
	
	if ($treated == "yes") {
		echo "<h1>Behandlade bloggkommentarer</h1>\n";
		echo "<div><a href=\"" . $_SERVER['PHP_SELF'] . "\">Ej behandlade</a>&nbsp;(" . $blogg->getComments(0) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?treated=yes\">Behandlade</a>&nbsp;(" . $blogg->getComments(1) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?deleted=yes\">Borttagna</a>&nbsp;(" . $blogg->getComments(2) . ")</div>\n";
		$blogg->showCommentsPub();
	} elseif ($deleted == "yes") {
		echo "<h1>Borttagna bloggkommentarer</h1>\n";
		echo "<div><a href=\"" . $_SERVER['PHP_SELF'] . "\">Ej behandlade</a>&nbsp;(" . $blogg->getComments(0) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?treated=yes\">Behandlade</a>&nbsp;(" . $blogg->getComments(1) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?deleted=yes\">Borttagna</a>&nbsp;(" . $blogg->getComments(2) . ")</div>\n";
		$blogg->showCommentsDenyed();
	} else {
		echo "<h1>Ej behandlade bloggkommentarer</h1>\n";
		echo "<div><a href=\"" . $_SERVER['PHP_SELF'] . "\">Ej behandlade</a>&nbsp;(" . $blogg->getComments(0) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?treated=yes\">Behandlade</a>&nbsp;(" . $blogg->getComments(1) . ")&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"" . $_SERVER['PHP_SELF'] . "?deleted=yes\">Borttagna</a>&nbsp;(" . $blogg->getComments(2) . ")</div>\n";
		$blogg->showCommentsNotPub();
	}
	
	include_once("footer.php");
?>