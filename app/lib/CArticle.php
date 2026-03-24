<?php

# PHP article object
# author		Daniel Norin
# version		2000-11-17
# Define to prevent multiple inclusion
//define("_CArticle", 1);
# Include the CConnection class
//if (_CConnection != 1) {
//include("CConnection.php");
//}
# CArticle class definition
####################################################

class CArticle {

    var $artnr;
    var $kategori_id;
    var $beskrivning;
    var $kommentar;
    var $utpris;
    var $bild;
    var $tillverkare;
    var $link;
    var $link_fi;
    var $kategori;
    var $kategori_no;
    var $ej_med;
    var $no_buy;
    var $motljsk;
    var $filterd;
    var $lagersaldo;
    var $zoom;
    var $ccd;
    var $betyg;
    var $zoom_digikam;
    var $momssats;
    var $bestallningsgrans;
    var $campaignLink;
    var $kortinfo;
    var $tillverkar_id;
    var $ej_brev;
    var $demo;
    var $abb_data;
    var $frakt;
    var $manufacturerproductno;
    var $beskrivningKort;
    var $kategori_id_parent;
    var $beskrivning_no;
    var $kommentar_no;
    var $no_buy_no;
    var $ej_med_no;
    var $utgangen_no;
    var $momssats_no;
    var $demo_no;
    var $utpris_no;
    var $isSalesBundle;
    var $m_product_id;
	var $beskrivning_fi ;
	var $kommentar_fi ;
	var $utpris_fi ;
	var $link2_fi ;
	var $kategori_fi ;
	var $ej_med_fi ;
	var $momssats_fi ;
	var $utgangen_fi ;
	var $campaignLink_fi ;
	var $kortinfo_fi ;
	var $demo_fi ;
	var $no_buy_fi ;
	var $utgangen ;
	var $isParent ;
	var $spec12 ;
	var $spec13 ;
	var $upc ;
	var $usp ;
	var $isTradeIn ;
	var $date_add ;
	var $art_id;
	var $WebshopURL;
	var $WebshopImageURL;
	var $WebshopCategory;


    // var $num_of_rows;
    // var $pris_inkl_moms_se;
    # Constructor
    # param:	ar	An array of values that the article will be initialized with
    #						  This is usually from a row record from an SQL query.
    function CArticle($ar) {
        $this->set($ar);
    }

    # Sets the values of the object

    function set($ar) {
        list (
                $this->artnr,
                $this->kategori_id,
                $this->beskrivning,
                $this->kommentar,
                $this->utpris,
                $this->bild,
                $this->tillverkare,
                $this->link,
                $this->link_fi,
                $this->kategori,
                $this->kategori_no,
                $this->ej_med,
                $this->no_buy,
                $this->motljsk,
                $this->filterd,
                $this->lagersaldo,
                $this->zoom,
                $this->ccd,
                $this->betyg,
                $this->zoom_digikam,
                $this->momssats,
                $this->bestallningsgrans,
                $this->campaignLink,
                $this->kortinfo,
                $this->tillverkar_id,
                $this->ej_brev,
                $this->demo,
                $this->abb_data,
                $this->frakt,
                $this->manufacturerproductno,
                $this->beskrivningKort,
                $this->kategori_id_parent,
                $this->beskrivning_no,
                $this->kommentar_no,
                $this->no_buy_no,
                $this->ej_med_no,
                $this->utgangen_no,
                $this->momssats_no,
                $this->demo_no,
                $this->utpris_no,
                $this->isSalesBundle, 
                $this->m_product_id, 
				$this->beskrivning_fi ,
				$this->kommentar_fi ,
				$this->utpris_fi ,
				$this->link2_fi ,
				$this->kategori_fi ,
				$this->ej_med_fi ,
				$this->momssats_fi ,
				$this->utgangen_fi ,
				$this->campaignLink_fi ,
				$this->kortinfo_fi ,
				$this->demo_fi,
                $this->no_buy_fi,
                $this->utgangen,
                $this->isParent,
                $this->spec12,
                $this->spec13,
                $this->upc,
                $this->usp,
                $this->isTradeIn, 
                $this->date_add, 
				$this->art_id, 
				$this->WebshopURL, 
				$this->WebshopImageURL, 
				$this->WebshopCategory 


                ) = $ar;
    }
}

?>