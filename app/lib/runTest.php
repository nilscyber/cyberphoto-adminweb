#!/usr/src/php-4.2.3/php 
## #/usr/local/bin/php -q

<?php

set_time_limit(600);

// var_dump(gd_info());

// Get a connection in variable conn_my
require("/home/phplib/CConnect.php");

//error_reporting(E_ERROR);
error_reporting(E_ALL);

$WWWROOT = "/home/www/";

$query = "select artnr, bild from Artiklar where ".
	 "bild is not null";

$result = mysqli_query($query) or die('Query failed: ' . mysqli_error());

while($line = mysqli_fetch_array($result, MYSQL_NUM)) {
	scaleImage($line[0],$line[1]);
}

mysqli_free_result($result);
mysqli_close($conn_my);

// End of script

// =========================================================================
// == FUNCTIONS
// =========================================================================

// Return path to where the medium thumb will be generated
function getMediumPath($absPath) {
	global $WWWROOT;

	$mediumPath = str_replace ($WWWROOT, $WWWROOT.'thumbs/medium/', $absPath);
	return($mediumPath); 
}

// Return path to where the small thumb will be generated.
function getSmallPath($absPath) {
	global $WWWROOT;

	$pattern = '/^$WWWROOT/';
	$smallPath = str_replace ($WWWROOT, $WWWROOT.'thumbs/small/', $absPath);
	return($smallPath); 
}

// Return true if the images needs to be rescaled.
function needsRescale($absPath) {
	// Check if the thumbsfiles exists
 	$mediumPath = getMediumPath($absPath);
	if (!file_exists($mediumPath)) return(TRUE);
	$smallPath = getSmallPath($absPath);
	if (!file_exists($smallPath)) return(TRUE);

	// Check if the original file is newer than the thumbs
	$origTime = filemtime($absPath);
	$mediumTime = filemtime($mediumPath);
	$smallTime = filemtime($smallPath);			
	if ($mediumTime<$origTime or $smallTime<$origTime) {
		return(TRUE);
	} 
	return(FALSE);
}

function rescaleImage($srcPath, $dstPath, $max) {
	list($width, $height, $type, $attr) = getimagesize($srcPath);
	if ($width>$height) {  // Portrait
		$percent = $max/$width;	
	} else {
		$percent = $max/$height;
	}
	$newWidth = round($width*$percent);
	$newHeight = round($height*$percent);
	print "Create new image $srcPath -> $dstPath\n";
	if ($type==2) {
		$image = imagecreatefromjpeg($srcPath);
	}
	if ($type==1) {
		return(FALSE);
		$image = @imagecreatefromgif($srcPath);
	}
	$image_p = imagecreate($newWidth, $newHeight) or die('Create failed');
	if ($image) {
		imagecopyresized($image_p, $image,0,0,0,0,$newWidth, $newHeight, $width, $height);
		if ($type==2) {
			imagejpeg($image_p, $dstPath);
			return(TRUE);
		}
		if ($type==1) {
			imagegif($image_p, $dstPath);
			return(TRUE);
		}
	}
	return(FALSE);
}

function scaleImage($articleId, $imgPath) {
	global $WWWROOT;

	// Find absolute path
	$pattern = '/^\.\.\//';
	$absPath = preg_replace ( $pattern ,$WWWROOT, $imgPath);
	if (!strcmp($absPath,$imgPath)) {
		$absPath = $WWWROOT."bilder/".$imgPath;
	}
	if ( list($width, $height, $type, $attr) = getimagesize($absPath) ) {
		print("$articleId\t$imgPath\t$absPath\t$width x $height $type\n");
		if (needsRescale($absPath)) {
			$mediumPath = getMediumPath($absPath);
			$smallPath = getSmallPath($absPath);
			$mediumDirectory = getDirectory($mediumPath);
			$smallDirectory = getDirectory($smallPath);
			if (!file_exists($mediumDirectory)) {
				exec("mkdir -p $mediumDirectory");
			}
			if (!file_exists($smallDirectory)) {
				exec("mkdir -p $smallDirectory");
			}
		        rescaleImage($absPath, $smallPath, 48);
			rescaleImage($absPath, $mediumPath, 128);
		} else {
		  print("No rescale of $imgPath needed.\n");
		}
	} else {
	  print("$articleId\t$absPath - no such file\n");
	}
}

function getDirectory($path) {
	if (is_dir($path)) return($path);
	// Get the directory part
	$pos = strrpos($path, "/");
	if ($pos === false) {
          // Not a directory
	  return(FALSE);
	}
	$dir = substr($path,0,$pos);
	return($dir);
}

?>