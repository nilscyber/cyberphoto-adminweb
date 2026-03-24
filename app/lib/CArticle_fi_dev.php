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
	var $beskrivning;
	var $beskrivning_fi; 
	var $kommentar;
	var $kommentar_fi;
	var $utpris_fi;	
	var $utpris;
        var $bild;
	var $tillverkare;
	var $link;
	var $link2_fi;
	var $kategori;
	var $kategori_fi;
    var $ej_med_fi;
    var $no_buy_fi;
    var $motljsk;
    var $filterd;
    var $skick;
    var $garanti;
    var $lagersaldo;
    var $lagersaldo_fi;
    var $filmkat_id;
    var $release;
    var $zoom;
    var $ccd;
    var $betyg;
    var $zoom_digikam;
    var $momssats_fi;
    var $bestallningsgrans;
    var $bestallningsgrans_fi;
    var $utgangen;

	# Constructor
	# param:	ar	An array of values that the article will be initialized with
	#						  This is usually from a row record from an SQL query.
	function CArticle( $ar ) {
		$this->set( $ar );
	}

	# Sets the values of the object
	function set ( $ar ) {
		list ( 		 $this->artnr,
					 $this->beskrivning,
					 $this->beskrivning_fi, 
					 $this->kommentar,
					 $this->kommentar_fi, 
					 $this->utpris_fi,	
					 $this->utpris, 				 
                                         $this->bild,
					 $this->tillverkare,
					 $this->link,
					 $this->link2_fi,
					 $this->kategori,
					 $this->kategori_fi,
                     $this->ej_med,
                     $this->no_buy,
                     $this->motljsk,
                     $this->filterd,
                     $this->skick,
                     $this->garanti,
                     $this->lagersaldo,
                     $this->lagersaldo_fi,
                     $this->filmkat_id,
                     $this->release,
                     $this->zoom,
                     $this->ccd,
                     $this->betyg,
                     $this->zoom_digikam,
                     $this->momssats,
                     $this->bestallningsgrans, 
                     $this->bestallningsgrans_fi, 
                     $this->utgangen

				 ) = $ar;
	}
}

# Functions that work on the CArticle class but are not
# part of the class definition
######################################################

# Creates a select statement for the CArticle class
# return:	Returns an SQL query string for the
function createArticleSelect() {
	return("SELECT artnr, Artiklar.beskrivning, Artiklar_fi.beskrivning_fi, Artiklar.kommentar, Artiklar_fi.kommentar_fi, utpris_fi, utpris, bild, tillverkare, link, Artiklar_fi.link2_fi, " . 
		"kategori, kategori_fi, ej_med_fi as ej_med, no_buy, motljsk, filterd, skick, garanti, lagersaldo_fi, lagersaldo, filmkat_id, release, zoom, ccd, betyg, " . 
		"zoom_digikam, Moms.momssats_fi as momssats, bestallningsgrans_fi, bestallningsgrans, utgangen_fi as utgangen ".
			 "FROM Artiklar LEFT JOIN  Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id " . 
			 "LEFT JOIN Kategori ON Artiklar.kategori_id=Kategori.kategori_id LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ");
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
	$conn->query(createArticleSelect()." $criteria");
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