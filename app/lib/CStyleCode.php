<?php
include("connections.php");

Class CStyleCode {

	function __construct() {
		global $fi;
			
		$this->conn_my = Db::getConnection();
//		$this->conn_ms = @mssql_pconnect ("81.8.240.66", "apache", "aKatöms#1");
//		@mssql_select_db ("cyberphoto", $this->conn_ms);
		$this->conn_fi = $this->conn_ms;
		
	}

	function StyleText($text) {
		global $fi,$sv;

		if ($fi && !$sv) {

			if (eregi("ale!", $text)) {
				$text = str_replace("ALE!", "<b><font color='#85000D'>ALE!</font></b>", $text);
			}
			if (eregi("kampanja!", $text)) {
				$text = str_replace("Kampanja!", "<b><font color='#85000D'>Kampanja!</font></b>", $text);
			}
		
		} else { 

			if (eregi("rea!", $text)) {
				$text = str_replace("REA!", "<b><font color='#85000D'>REA!</font></b>", $text);
			}
			if (eregi("REA - Utförsäljning", $text)) {
				$text = str_replace("REA - Utförsäljning", "<b><font color='#85000D'>REA - Utförsäljning</font></b>", $text);
			}
			if (eregi("kampanj!", $text)) {
				$text = str_replace("Kampanj!", "<b><font color='#85000D'>Kampanj!</font></b>", $text);
			}
			if (eregi("prissänkt!", $text)) {
				$text = str_replace("Prissänkt!", "<b><font color='#85000D'>Prissänkt!</font></b>", $text);
			}
			if (eregi("prissänkt#", $text)) {
				$text = str_replace("Prissänkt#", "<b><font color='#85000D'>Prissänkt!</font></b>", $text);
			}
			if (eregi("REA#", $text)) {
				$text = str_replace("REA#", "<b><font color='#85000D'>REA!</font></b>", $text);
			}
			if (eregi("kampanj#", $text)) {
				$text = str_replace("Kampanj#", "<b><font color='#85000D'>Kampanj!</font></b>", $text);
			}
			if (eregi("förhandsboka!", $text)) {
				$text = str_replace("Förhandsboka!", "<b><font color='#85000D'>Förhandsboka!</font></b>", $text);
			}
			if (eregi("nyhet", $text)) {
				$text = str_replace("Nyhet!", "<b><font color='#85000D'>Nyhet!</font></b>", $text);
			}
			if (eregi("utförsäljning", $text)) {
				$text = str_replace("Utförsäljning!", "<b><font color='#85000D'>Utförsäljning!</font></b>", $text);
			}

		}

	echo $text;

	}
    function StyleText_v3($text) {
        global $fi,$sv,$no;
        
        if ($no || ($fi && $sv)) {
            if (preg_match("/Utförsäljning!/i",$text)) {
                $text = $this->removeSEPrice($text);
            }
        }

        if ($fi && !$sv) {

            if (eregi("ale!", $text)) {
                $text = str_replace("ALE!", "<span class='redmark'>ALE!</span>", $text);
            }
            if (eregi("kampanja!", $text)) {
                $text = str_replace("Kampanja!", "<span class='redmark'>Kampanja!</span>", $text);
            }
        
        } else { 

            if (eregi("rea!", $text)) {
                //$text = str_replace("REA!", "<span class='redmark'>REA!</span>", $text);
                $text = "<span class='redmark'>REA!</span>";
            }
            if (eregi("REA - Utförsäljning", $text)) {
                //$text = str_replace("REA - Utförsäljning", "<span class='redmark'>REA - Utförsäljning</span>", $text);
                $text = "<span class='redmark'>REA - Utförsäljning</span>";
            }
            if (eregi("kampanj!", $text)) {
                //$text = str_replace("Kampanj!", "<span class='redmark'>Kampanj!</span>", $text);
                $text = "<span class='redmark'>Kampanj!</span>";
            }
            if (eregi("kampanje!", $text)) {
                //$text = str_replace("Kampanje!", "<span class='redmark'>Kampanje!</span>", $text);
                $text = "<span class='redmark'>Kampanje!</span>";
            }
            if (eregi("prissänkt!", $text)) {
                //$text = str_replace("Prissänkt!", "<span class='redmark'>Prissänkt!</span>", $text);
                $text = "<span class='redmark'>Prissänkt!</span>";
            }
            if (eregi("prissänkt#", $text)) {
                //$text = str_replace("Prissänkt#", "<span class='redmark'>Prissänkt!</span>", $text);
                $text = "<span class='redmark'>Prissänkt!</span>";
            }
            if (eregi("REA#", $text)) {
                //$text = str_replace("REA#", "<span class='redmark'>REA!</span>", $text);
                $text = "<span class='redmark'>REA!</span>";
            }
            if (eregi("kampanj#", $text)) {
                //$text = str_replace("Kampanj#", "<span class='redmark'>Kampanj!</span>", $text);
                $text = "<span class='redmark'>Kampanj!</span>";
            }
            if (eregi("förhandsboka!", $text)) {
                //$text = str_replace("Förhandsboka!", "<span class='redmark'>Förhandsboka!</span>", $text);
                $text = "<span class='redmark'>Förhandsboka!</span>";
            }
            if (eregi("nyhet", $text)) {
                //$text = str_replace("Nyhet!", "<span class='redmark'>Nyhet!</span>", $text);
                $text = "<span class='redmark'>Nyhet!</span>";
            }
            if (eregi("utförsäljning", $text)) {
                if ($no) {
                    $text = str_replace("Utförsäljning!", "<span class='redmark'>Lagersalg!</span>", $text);
                }
                $text = str_replace("Utförsäljning!", "<span class='redmark'>Utförsäljning!</span>", $text);
                $text = str_replace("Utförsäljning#", "<span class='redmark'>Utförsäljning!</span>", $text);
                $text = "<span class='redmark'>Utförsäljning!</span>";
            }
            if (preg_match("/BELYST/", $text)) {
                $text = str_replace("BELYST", "<span class='text_red'>BELYST</span>", $text);
            }
            if (preg_match("/Spara 200kr/", $text)) {
                $text = str_replace("Spara 200kr", "<span class='bold text_blue italic'>Spara 200kr</span>", $text);
            }
            if (preg_match("/Spara 250kr/", $text)) {
                $text = str_replace("Spara 250kr", "<span class='bold text_blue italic'>Spara 250kr</span>", $text);
            }
            if (preg_match("/Spara 300kr/", $text)) {
                $text = str_replace("Spara 300kr", "<span class='bold text_blue italic'>Spara 300kr</span>", $text);
            }
            if (preg_match("/Spara 400kr/", $text)) {
                $text = str_replace("Spara 400kr", "<span class='bold text_blue italic'>Spara 400kr</span>", $text);
            }
            if (preg_match("/Spara 500kr/", $text)) {
                $text = str_replace("Spara 500kr", "<span class='bold text_blue italic'>Spara 500kr</span>", $text);
            }
            if (preg_match("/Spara 600kr/", $text)) {
                $text = str_replace("Spara 600kr", "<span class='bold text_blue italic'>Spara 600kr</span>", $text);
            }
            if (preg_match("/Spara 700kr/", $text)) {
                $text = str_replace("Spara 700kr", "<span class='bold text_blue italic'>Spara 700kr</span>", $text);
            }
            if (preg_match("/Spara 800kr/", $text)) {
                $text = str_replace("Spara 800kr", "<span class='bold text_blue italic'>Spara 800kr</span>", $text);
            }
            if (preg_match("/Spara 900kr/", $text)) {
                $text = str_replace("Spara 900kr", "<span class='bold text_blue italic'>Spara 900kr</span>", $text);
            }
            if (preg_match("/Spara 1000kr/", $text)) {
                $text = str_replace("Spara 1000kr", "<span class='bold text_blue italic'>Spara 1000kr</span>", $text);
            }
            if (preg_match("/Spara 1500kr/", $text)) {
                $text = str_replace("Spara 1500kr", "<span class='bold text_blue italic'>Spara 1500kr</span>", $text);
            }
            if (preg_match("/Spara 2000kr/", $text)) {
                $text = str_replace("Spara 2000kr", "<span class='bold text_blue italic'>Spara 2000kr</span>", $text);
            }
            if (preg_match("/Spara 5000kr/", $text)) {
                $text = str_replace("Spara 5000kr", "<span class='bold text_blue italic'>Spara 5000kr</span>", $text);
            }

        }

    return $text;

    }
	function StyleText_v2($text) {
		global $fi,$sv,$no;
		
		if ($no || ($fi && $sv)) {
			if (preg_match("/Utförsäljning!/i",$text)) {
				$text = $this->removeSEPrice($text);
			}
		}

		if ($fi && !$sv) {

			if (eregi("ale!", $text)) {
				$text = str_replace("ALE!", "<span class='redmark'>ALE!</span>", $text);
			}
			if (eregi("kampanja!", $text)) {
				$text = str_replace("Kampanja!", "<span class='redmark'>Kampanja!</span>", $text);
			}
		
		} else { 

			if (eregi("rea!", $text)) {
				$text = str_replace("REA!", "<span class='redmark'>REA!</span>", $text);
			}
			if (eregi("REA - Utförsäljning", $text)) {
				$text = str_replace("REA - Utförsäljning", "<span class='redmark'>REA - Utförsäljning</span>", $text);
			}
			if (eregi("kampanj!", $text)) {
				$text = str_replace("Kampanj!", "<span class='redmark'>Kampanj!</span>", $text);
			}
			if (eregi("kampanje!", $text)) {
				$text = str_replace("Kampanje!", "<span class='redmark'>Kampanje!</span>", $text);
			}
			if (eregi("prissänkt!", $text)) {
				$text = str_replace("Prissänkt!", "<span class='redmark'>Prissänkt!</span>", $text);
			}
			if (eregi("prissänkt#", $text)) {
				$text = str_replace("Prissänkt#", "<span class='redmark'>Prissänkt!</span>", $text);
			}
			if (eregi("REA#", $text)) {
				$text = str_replace("REA#", "<span class='redmark'>REA!</span>", $text);
			}
			if (eregi("kampanj#", $text)) {
				$text = str_replace("Kampanj#", "<span class='redmark'>Kampanj!</span>", $text);
			}
			if (eregi("förhandsboka!", $text)) {
				$text = str_replace("Förhandsboka!", "<span class='redmark'>Förhandsboka!</span>", $text);
			}
			if (eregi("nyhet", $text)) {
				$text = str_replace("Nyhet!", "<span class='redmark'>Nyhet!</span>", $text);
			}
			if (eregi("utförsäljning", $text)) {
				if ($no) {
					$text = str_replace("Utförsäljning!", "<span class='redmark'>Lagersalg!</span>", $text);
				}
				$text = str_replace("Utförsäljning!", "<span class='redmark'>Utförsäljning!</span>", $text);
				$text = str_replace("Utförsäljning#", "<span class='redmark'>Utförsäljning!</span>", $text);
			}
			if (preg_match("/BELYST/", $text)) {
				$text = str_replace("BELYST", "<span class='text_red'>BELYST</span>", $text);
			}
			if (preg_match("/Spara 200kr/", $text)) {
				$text = str_replace("Spara 200kr", "<span class='bold text_blue italic'>Spara 200kr</span>", $text);
			}
			if (preg_match("/Spara 250kr/", $text)) {
				$text = str_replace("Spara 250kr", "<span class='bold text_blue italic'>Spara 250kr</span>", $text);
			}
			if (preg_match("/Spara 300kr/", $text)) {
				$text = str_replace("Spara 300kr", "<span class='bold text_blue italic'>Spara 300kr</span>", $text);
			}
			if (preg_match("/Spara 400kr/", $text)) {
				$text = str_replace("Spara 400kr", "<span class='bold text_blue italic'>Spara 400kr</span>", $text);
			}
			if (preg_match("/Spara 500kr/", $text)) {
				$text = str_replace("Spara 500kr", "<span class='bold text_blue italic'>Spara 500kr</span>", $text);
			}
			if (preg_match("/Spara 600kr/", $text)) {
				$text = str_replace("Spara 600kr", "<span class='bold text_blue italic'>Spara 600kr</span>", $text);
			}
			if (preg_match("/Spara 700kr/", $text)) {
				$text = str_replace("Spara 700kr", "<span class='bold text_blue italic'>Spara 700kr</span>", $text);
			}
			if (preg_match("/Spara 800kr/", $text)) {
				$text = str_replace("Spara 800kr", "<span class='bold text_blue italic'>Spara 800kr</span>", $text);
			}
			if (preg_match("/Spara 900kr/", $text)) {
				$text = str_replace("Spara 900kr", "<span class='bold text_blue italic'>Spara 900kr</span>", $text);
			}
			if (preg_match("/Spara 1000kr/", $text)) {
				$text = str_replace("Spara 1000kr", "<span class='bold text_blue italic'>Spara 1000kr</span>", $text);
			}
			if (preg_match("/Spara 1500kr/", $text)) {
				$text = str_replace("Spara 1500kr", "<span class='bold text_blue italic'>Spara 1500kr</span>", $text);
			}
			if (preg_match("/Spara 2000kr/", $text)) {
				$text = str_replace("Spara 2000kr", "<span class='bold text_blue italic'>Spara 2000kr</span>", $text);
			}
			if (preg_match("/Spara 5000kr/", $text)) {
				$text = str_replace("Spara 5000kr", "<span class='bold text_blue italic'>Spara 5000kr</span>", $text);
			}

		}

	return $text;

	}
	
	function removeSEPrice($str) {
		
		$param="Utförsäljning!";
		
		$pos = strpos($str, $param);
		$endpoint = $pos + strlen($param);
		$newStr = substr($str,0,$endpoint );
		
		return $newStr;
		
	}
	
	function StyleNewProduct($artnr,$where,$demo) {
		global $fi,$sv;
		
		// 2016-10-19 blockera vissa artiklar
		if ($artnr == "nid5B" || $artnr == "1dxmk2B" || $artnr == "5DMK4_B") { 
			$artnr = "XXXXXXX";
		}

		$newproducttime = (time() - strtotime($this->checkSkapad($artnr)));
		$newproduct = round($newproducttime / 60 / 60 / 24);
		
		if ($where == 1 && $newproduct < 60 && $demo == 0) {
			if ($fi && !$sv) {
				// echo "<img border=\"0\" src=\"/pic/newproduct_fi_v2.gif\">";
				echo "<img border=\"0\" src=\"/pic/nyhet_fi.png\"><br>";
			} else {
				// echo "<img border=\"0\" src=\"/pic/newproduct_v2.gif\">";
				echo "<img border=\"0\" src=\"/pic/nyhet.png\"><br>";
			}
		}

		if ($where == 2 && $newproduct < 60 && $demo == 0) {
			if ($fi && !$sv) {
				echo " <b><font color='#85000D'>Uutuus!</font></b>";
			} else {
				echo " <b><font color='#85000D'>Nyhet!</font></b>";
			}
		}

	// echo $newproduct;

	}

	function StyleNewProduct_v2($artnr,$where,$demo) {
		global $fi,$sv;

		$newproducttime = (time() - strtotime($this->checkSkapad($artnr)));
		$newproduct = round($newproducttime / 60 / 60 / 24);
		
		if ($where == 1 && $newproduct < 60 && $demo == 0) {
			if ($fi && !$sv) {
				// echo "<img border=\"0\" src=\"/pic/newproduct_fi_v2.gif\">";
				echo "<img border=\"0\" src=\"/pic/nyhet_fi.png\">";
			} else {
				// echo "<img border=\"0\" src=\"/pic/newproduct_v2.gif\">";
				echo "<img border=\"0\" src=\"/pic/nyhet.png\">";
			}
		}

		if ($where == 2 && $newproduct < 60 && $demo == 0) {
			if ($fi && !$sv) {
				return " <span class=\"redmark\">Uutuus!</span>";
			} else {
				return " <span class=\"redmark\">Nyhet!</span>";
			}
		}

	// echo $newproduct;

	}
	
	function checkSkapad($artnr) {

		$select = "SELECT skapad_datum FROM Artiklar WHERE artnr = '" . $artnr . "' ";

		$res = mysqli_query($this->conn_my, $select);

			if (mysqli_num_rows($res) > 0) {
			
				while ($row = mysqli_fetch_array($res)):
			
				extract($row);
				
				return $skapad_datum;
				
				endwhile;
				
			} else {
			
				return "";
			
			}

	}

	function displayNikonNPS($artnr) {

		if ($artnr == "nid4") {
			return true;
		} elseif ($artnr == "nid800") {
			return true;
		} elseif ($artnr == "nid800e") {
			return true;
		} elseif ($artnr == "nid300s") {
			return true;
		} elseif ($artnr == "nid700") {
			return true;
		} else {
			return false;
		}

	}

	function displayOurTestReview($betyg) {
		global $sv, $fi, $no;
		
		if ($betyg == 1) {
			if ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/120x120guld_FI.png\" title=\"Huippuluokkaa\">";
			} elseif ($no) {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Førsteklasses', '120x120guld_NO.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120guld_NO.png\" title=\"Førsteklasses, klikk for å lese om våre rangeringer\"></a>";
			} else {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Toppklass', '120x120guld_SE.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120guld_SE.png\" title=\"Toppklass, klicka för att läsa om våra betyg\"></a>";
			}
		} elseif ($betyg == 20) {
			if ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/120x120silver_FI.png\" title=\"Hyvä ostos\">";
			} elseif ($no) {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Bra kjøp', '120x120silver_NO.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120silver_NO.png\" title=\"Bra kjøp, klikk for å lese om våre rangeringer\"></a>";
			} else {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Bra köp', '120x120silver_SE.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120silver_SE.png\" title=\"Bra köp, klicka för att läsa om våra betyg\"></a>";
			}
		} elseif ($betyg == 30) {
			if ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/120x120brons_FI.png\" title=\"Hinnan arvoinen\">";
			} elseif ($no) {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Prisgunstig', '120x120brons_NO.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120brons_NO.png\" title=\"Prisgunstig, klikk for å lese om våre rangeringer\"></a>";
			} else {
				echo "<a onClick=\"_gaq.push(['_trackEvent', 'ProductClickPlugg', 'Betyget - Prisvärd', '120x120brons_SE.png']);\" href=\"/blogg.php?ID=7729\"><img border=\"0\" src=\"/images/120x120brons_SE.png\" title=\"Prisvärd, klicka för att läsa om våra betyg\"></a>";
			}
		} else {
			echo "&nbsp;";
		}
	}

	function displayOurTestReviewPricelist($betyg) {
		global $sv, $fi, $no, $plist;
		
		if ($betyg == 1) {
			if ($plist == "plain_row" || $plist == "admin_row") {
				if ($fi && !$sv) {
					echo "Huippuluokkaa";
				} elseif ($no) {
					echo "Førsteklasses";
				} else {
					echo "Toppklass";
				}
			} elseif ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/50x50guld_FI.png\" title=\"Huippuluokkaa\">";
			} elseif ($no) {
				echo "<img border=\"0\" src=\"/images/50x50guld_NO.png\" title=\"Førsteklasses!\">";
			} else {
				echo "<img border=\"0\" src=\"/images/50x50guld_SE.png\" title=\"Toppklass!\">";
			}
		} elseif ($betyg == 20) {
			if ($plist == "plain_row" || $plist == "admin_row") {
				if ($fi && !$sv) {
					echo "Hyvä ostos";
				} elseif ($no) {
					echo "Bra kjøp";
				} else {
					echo "Bra köp";
				}
			} elseif ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/50x50silver_FI.png\" title=\"Hyvä ostos\">";
			} elseif ($no) {
				echo "<img border=\"0\" src=\"/images/50x50silver_NO.png\" title=\"Bra kjøp!\">";
			} else {
				echo "<img border=\"0\" src=\"/images/50x50silver_SE.png\" title=\"Bra köp!\">";
			}
		} elseif ($betyg == 30) {
			if ($plist == "plain_row" || $plist == "admin_row") {
				if ($fi && !$sv) {
					echo "Hinnan arvoinen";
				} elseif ($no) {
					echo "Prisgunstig";
				} else {
					echo "Prisvärd";
				}
			} elseif ($fi && !$sv) {
				echo "<img border=\"0\" src=\"/images/50x50brons_FI.png\" title=\"Hinnan arvoinen\">";
			} elseif ($no) {
				echo "<img border=\"0\" src=\"/images/50x50brons_NO.png\" title=\"Prisgunstig!\">";
			} else {
				echo "<img border=\"0\" src=\"/images/50x50brons_SE.png\" title=\"Prisvärd!\">";
			}
		} else {
			echo "&nbsp;";
		}
	}
	
	function displayRecommended() {
		global $sv, $fi, $no, $plist;
		
		if ($plist == "plain_row" || $plist == "admin_row") {
			if ($fi && !$sv) {
				echo "HYVÄ VALINTA";
			} elseif ($no) {
				echo "SMART VALG";
			} else {
				echo "BRA VAL";
			}
		} elseif ($fi && !$sv) {
			echo "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('Tämä tuote olemme valinneet Hyväksi valinnaksi sinulle joka haluat nopeasti löytää hyvän ja turvallisen oston!')\"><img border=\"0\" src=\"/images/BRA_FI.png\"></a>";
		} elseif ($no) {
			echo "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('Dette produktet har vi valgt ut som et smart valg for deg som raskt vil finne et bra og trygt kjøp!')\"><img border=\"0\" src=\"/images/BRA_NO.png\"></a>";
		} else {
			echo "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('Denna produkt har vi valt ut som ett Bra val för dig som snabbt vill hitta ett bra och tryggt köp!')\"><img border=\"0\" src=\"/images/BRA_SE.png\"></a>";
		}
		
	}
	
	function displayCanonCashback($artnr) {

		if ($artnr == "128684") {
			return true;
		} elseif ($artnr == "128685") {
			return true;
		} elseif ($artnr == "EF-S6028") {
			return true;
		} elseif ($artnr == "232104") {
			return true;
		} elseif ($artnr == "100macroL") {
			return true;
		} elseif ($artnr == "232054") {
			return true;
		} elseif ($artnr == "123225") {
			return true;
		} elseif ($artnr == "17-85_IS") {
			return true;
		} elseif ($artnr == "123925") {
			return true;
		} elseif ($artnr == "232205") {
			return true;
		} elseif ($artnr == "123453") {
			return true;
		} elseif ($artnr == "232201") {
			return true;
		} elseif ($artnr == "126708") {
			return true;
		} elseif ($artnr == "17-40") {
			return true;
		} elseif ($artnr == "1585is") {
			return true;
		} elseif ($artnr == "10-22") {
			return true;
		} elseif ($artnr == "17-55is") {
			return true;
		} elseif ($artnr == "EF24105L") {
			return true;
		} elseif ($artnr == "EF1635II") {
			return true;
		} elseif ($artnr == "128674") {
			return true;
		} elseif ($artnr == "232125") {
			return true;
		} elseif ($artnr == "7D") {
			return true;
		} elseif ($artnr == "7D1585") {
			return true;
		} elseif ($artnr == "7D18135") {
			return true;
		} elseif ($artnr == "60D") {
			return true;
		} elseif ($artnr == "60D1785") {
			return true;
		} elseif ($artnr == "60D17300") {
			return true;
		} elseif ($artnr == "60D18135") {
			return true;
		} elseif ($artnr == "60D1855ISII") {
			return true;
		} elseif ($artnr == "600EXRT") {
			return true;
		} elseif ($artnr == "430EX2") {
			return true;
		} else {
			return false;
		}

	}
	
}

?>
