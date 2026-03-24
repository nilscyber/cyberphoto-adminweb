<?php

# PHP article object
# author		Daniel Norin
# version		2000-11-17

# Define to prevent multiple inclusion
define("_CArticle", 1);

# Include the CConnection class
if (_CConnection != 1) {
	include("CConnection.php");
}

# CArticle class definition
####################################################

class CArticle {
	var $artnr;
	var $kategori_id;
        var $datum_inpris;
	var $beskrivning;
	var $kommentar;
	var $utpris;
        var $bild;
	var $tillverkare;
	var $link;
	var $link_fi;
	var $kategori;
	var $kategori_fi;
        var $ej_med;
        var $no_buy;
        var $motljsk;
        var $filterd;
        var $skick;
        var $garanti;
        var $lagersaldo;
        var $filmkat_id;
        var $release;
        var $zoom;
        var $ccd;
        var $betyg;
        var $zoom_digikam;
        var $momskod;
        var $bestallningsgrans;
        var $campaignLink;

	# Constructor
	# param:	ar	An array of values that the article will be initialized with
	#						  This is usually from a row record from an SQL query.
	function CArticle( $ar ) {
		$this->set( $ar );
	}

	# Sets the values of the object
	function set ( $ar ) {
		list ( $this->artnr,
					 $this->kategori_id,
                                         $this->datum_inpris,
					 $this->beskrivning,
					 $this->kommentar,
					 $this->utpris,
                                         $this->bild,
					 $this->tillverkare,
					 $this->link,
					 $this->link_fi,
					 $this->kategori,
					 $this->kategori_fi,

                                         $this->ej_med,
                                         $this->no_buy,
                                         $this->motljsk,
                                         $this->filterd,
                                         $this->skick,
                                         $this->garanti,
                                         $this->lagersaldo,
                                         $this->filmkat_id,
                                         $this->release,
                                         $this->zoom,
                                         $this->ccd,
                                         $this->betyg,
                                         $this->zoom_digikam,
                                         $this->momssats,
                                         $this->bestallningsgrans,
                                         $this->campaignLink


				 ) = $ar;
	}
}

# Functions that work on the CArticle class but are not
# part of the class definition
######################################################

# Creates a select statement for the CArticle class
# return:	Returns an SQL query string for the
function createArticleSelect() {
	return("SELECT artnr, Artiklar.kategori_id, Artiklar.datum_inpris, Artiklar.beskrivning, Artiklar.kommentar, utpris, bild, tillverkare, link, link_fi, kategori, kategori_fi, ej_med, no_buy, motljsk, filterd, skick, garanti, lagersaldo, filmkat_id, releaseDate as rel, zoom, ccd, betyg, zoom_digikam, Moms.momssats, bestallningsgrans, campaignLink, campaignLink_fi ".
				 "FROM Artiklar LEFT JOIN Tillverkare ".
				 "ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id ".
				 "LEFT JOIN Kategori ".
				 "ON Artiklar.kategori_id=Kategori.kategori_id " .
				 "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ");
}

# Reads articles matching the given criteria
# param:	criteria	The "where clause" in the SQL query including the word
# return:	an array of CArticle objects matching the criteria
function readArticlesWithCriteria($criteria) {
	# Get the connection from CConnection class
	global $conn;
	checkConnection();
	# Make an empty array for storing the result
	$resultset = array();
	
	# Do the query
	
	//$criteria = mysqli_real_escape_string($criteria);
	$sel = createArticleSelect()." $criteria";	
	//echo "<p>" . $sel . "<p>";
	//exit;
	$sel = eregi_replace("union", "", $sel);
	$sel = eregi_replace(";", "",  $sel);
	$conn->query($sel);
	
	# Read all resulting records
	while ($conn->next_record()) {
		# Create a new article object using the row
		$article = new CArticle($conn->Record);
		# Push the object on the result array
		array_push($resultset, $article);
	}
	# Return the result
	# If no matches were found, the result set will be empty.
	return($resultset);
}