<?php
require_once("CArticle.php");
class CArticleFunctions {
    # Functions that work on the CArticle class but are not
    # part of the class definition
    ######################################################
    # Creates a select statement for the CArticle class
    # return:	Returns an SQL query string for the

    function createArticleSelect() {
        return("SELECT Artiklar.artnr, Artiklar.kategori_id, Artiklar.beskrivning, Artiklar.kommentar, utpris, bild, tillverkare, link, link_fi,
				kategori, kategori_no, ej_med, no_buy, motljsk, filterd, lagersaldo, zoom, ccd, betyg, 
				zoom_digikam, Moms.momssats, bestallningsgrans, campaignLink, kortinfo, Artiklar.tillverkar_id, 
				Artiklar.ej_brev, Artiklar.demo, Artiklar.abb_data, Artiklar.frakt, Artiklar.manufacturerproductno, 
				Artiklar.beskrivningKort, Kategori.kategori_id_parent, Artiklar.beskrivning_no, Artiklar.kommentar_no, 
				Artiklar.no_buy_no, Artiklar.ej_med_no, Artiklar.utgangen_no, Moms.momssats_no, Artiklar.demo_no,
				Artiklar.utpris_no, Artiklar.isSalesBundle, Artiklar.m_product_id, Artiklar_fi.beskrivning_fi, 
				Artiklar_fi.kommentar_fi, Artiklar_fi.utpris_fi,  Artiklar_fi.link2_fi, kategori_fi, 
				ej_med_fi, Moms.momssats_fi, utgangen_fi, campaignLink_fi, kortinfo_fi, Artiklar_fi.demo_fi, 
				Artiklar_fi.no_buy_fi, Artiklar.utgangen, Artiklar.isParent, Artiklar.spec12, Artiklar.spec13, Artiklar.upc, 
				CASE WHEN i.artnr_cont is not null THEN  
				i2.usp  
				ELSE 
				i.usp
				END AS usp, Artiklar.isTradeIn, Artiklar.date_add, Artiklar.art_id, Artiklar.WebshopURL, Artiklar.WebshopImageURL, Artiklar.WebshopCategory " .
                "FROM Artiklar " .
				"LEFT JOIN Artiklar_fi ON Artiklar.artnr = Artiklar_fi.artnr_fi  " .
				"LEFT JOIN Info_page i on i.artnr = Artiklar.artnr " . 
				"LEFT JOIN Info_page i2 on i2.artnr = i.artnr_cont " .
                "LEFT JOIN Tillverkare ON Artiklar.tillverkar_id=Tillverkare.tillverkar_id " .
                "LEFT JOIN Kategori ON Artiklar.kategori_id=Kategori.kategori_id " .
                "LEFT JOIN Moms on Artiklar.momskod = Moms.moms_id ");

					
    }

    function checkConnection2() {
        global $connLocal;

        if (!$connLocal) {
            # Create new connection
            $connLocal = new CConnection();
            $connLocal->connect();
        }
    }

    # Reads articles matching the given criteria
    # param:	criteria	The "where clause" in the SQL query including the word
    # return:	an array of CArticle objects matching the criteria

    function readArticlesWithCriteria($criteria) {
        # Get the connection from CConnection class
		
        global $connLocal;
        $this->checkConnection2();
        # Make an empty array for storing the result

        $resultset = array();
 		//echo $criteria; exit;
 		//echo $criteria . "adsf<p>";
 		$criteria = str_replace(' artnr', ' Artiklar.artnr ', $criteria);
 		//echo $criteria; exit; 
        # Do the query
        //$criteria = mysqli_real_escape_string($criteria);
        $sel = $this->createArticleSelect() . " $criteria";
        //echo "<p>" . $sel . "<p>"; exit;
        $sel = eregi_replace("union", "", $sel);
        $sel = eregi_replace(";", "", $sel);
        $sel = eregi_replace("--", "", $sel);

        // if no where-clause, return null
        if (stripos($criteria, "where") === false) {
            return null;
        }
        if ($_COOKIE['login_mail'] == 'sjabo@cyberphoto.nuX') 
		echo $sel; 
        $connLocal->query($sel);

        # Read all resulting records
        while ($connLocal->next_record()) {
            # Create a new article object using the row
            $article = new CArticle($connLocal->Record);

            # Push the object on the result array
            array_push($resultset, $article);
        }

        // $num_of_rows = count($resultset);
        // echo $num_of_rows;
        # Return the result
        # If no matches were found, the result set will be empty.
        return($resultset);
    }

}
?>
