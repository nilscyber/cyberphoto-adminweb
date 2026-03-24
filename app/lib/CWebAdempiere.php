<?php

/*

PHP login object
author		Stefan Sjöberg
version		1.0 2011-02-28

changelog: 
v11: fixat bugg med visas som leveranstid okänd när det är en beställningsvara

*/

include("connections.php");
// require_once("Locs.php");
require_once("CCheckIpNumber.php");
require_once("Db.php");

Class CWebAdempiere {

		var $conn_my;
		var $conn_ad;

	function __construct() {
		$this->conn_my = Db::getConnection();
		$this->conn_ad = Db::getConnectionAD();

	}

	function check_lager($artnr,$store,$count) {
		global $fi, $sv, $no, $bestallningsgrans, $mobilsite, $pricesite;
		
		if ($artnr == "forsakring") {
			return "";
		}
		
		$select = "SELECT delivery.* FROM (SELECT * FROM get_best_datepromised_web('$artnr', $store, $count)) AS delivery ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") { // endast test
			echo $select;
			//exit;
		}

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		$row = $res ? pg_fetch_object($res) : null;
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $this->conn_ad;
		}

			if ($res && pg_num_rows($res) > 0) {
				
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
					echo $row->a_on_stock . "<br>";
					echo $row->a_datepromised . "<br>";
					echo $row->a_datepromisedprecision . "<br>";
				}
				
				if ($row->a_on_stock == "t") { // om produkten ändå finns i lager. Detta skall inte behöva hända men ändå.....
					if ($fi && !$sv) {
						if ($pricesite) {
							return "varastossa";
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\"><a onMouseOver=\"return escape('Tuote löytyy varastosta ja lähetetään normaalisti samana päivänä kuin teet tilauksesi')\" style=\"text-decoration: none\"> varastossa</a></font>";
						}
					} elseif ($no) {
						if ($pricesite) {
							return "Finnes på lager";
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\"><a onMouseOver=\"return escape('Varen finnes på lager og sendes rett etter bestilling.')\" style=\"text-decoration: none\">finnes på lager</a></font>";
						}
					} else {
						if ($mobilsite) {
							echo "<span class=\"instore\">Finns i lager</span>";
						} elseif ($pricesite) {
							return "Finns i lager";
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#385F39\"><a onMouseOver=\"return escape('Varan finns på lager och skickas normalt samma dag som ni beställer.')\" style=\"text-decoration: none\">Finns i lager</a></font>";
						}
					}
				} else { // om produkten inte finns i lager
					if ($fi && !$sv) {						
						if ($pricesite) {
							return $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv);
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000d\"><a onMouseOver=\"return escape('Päivämäärä viittaa päivämäärään jolloin tuote arvioidaan saapuvan varastoon. Huomioi että tämä on arvioitu päivämäärä')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv) . "</a></font>";
						}
					} elseif ($no) {
						if ($pricesite) {
							return $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv);
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000d\"><a onMouseOver=\"return escape('Datoen viser den dato da varen er beregnet å komme inn til vårt lager. Vær oppmerksom på at dette er omtrentlig dato.')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv) . " </a></font>";
						}
					} else {
						if ($mobilsite) {
							echo "<span class=\"notinstore\">" . $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv) . "</span>";
						} elseif ($pricesite) {
							return $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv);
						} else {
							echo "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000d\"><a onMouseOver=\"return escape('Datumet avser datum när varan <b>beräknas</b> komma in till vårt lager. Observera att detta är ungefärligt datum. ')\" style=\"text-decoration: none\">" . $this->showDeliveryDate($row->a_datepromised, $row->a_datepromisedprecision, $fi, $sv) . " </a></font>";
						}
					}
				}
				
			} else {
			
				if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89") {
					echo "ingen träff";
				}
				echo "&nbsp;";
			
			}
		
	}

	function showDeliveryDate($dat, $prec, $fi, $sv) {
		global $bestallningsgrans, $mobilsite, $pricesite, $no;
		
		if ($dat != "")
			$dat = substr($dat, 0, 10);
		
		$timestmp = strtotime($dat);		
		if ($prec == "D") { // exakt datum
			if ($fi) {								
				if ($sv) {
					return "Beräknas in<br>" . date("d-m-Y", $timestmp);
				} else {
					return "Saapuu " . date("d-m-Y", $timestmp);
				}
			} elseif ($no) {
				return "Forventes inn<br>" . date("Y-m-d", $timestmp);
			} else {
				return "Beräknas in<br>" . date("Y-m-d", $timestmp);
			}
			return $dat;
		} elseif ($prec == "W") {	// visas som vecka
			if ($fi && !$sv) {
				return "Oletettu saapumisaika viikko " .  strtolower (date("W", strtotime($dat)));		
			} elseif ($no) {
				return "Foventes inn i uke " .  strtolower (date("W", strtotime($dat)));
			} else {
				return "Beräknas in vecka " .  strtolower (date("W", strtotime($dat)));
			}
				
		} elseif ($prec == "P") {	// del av månad
			$day = date("j", strtotime($dat));
			$month = date("n", strtotime($dat));

			if ($day > 0 && $day <= 10 ) {
				if ($fi && !$sv) {
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " alussa";
				} elseif ($no) {
					return "Forventes inn i begynnelsen av " . $this->getMonthSv($month);
				} else {
					return "Beräknas in i början av " . $this->getMonthSv($month);
				}
					
			} elseif ($day > 10 && $day <= 20) {
				if ($fi && !$sv) {
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " puolivälissä";		
				} elseif ($no) {
					return "Forventes inn i midten av " . $this->getMonthSv($month);
				} else {
					return "Beräknas in i mitten av " . $this->getMonthSv($month);
				}
			} elseif ($day > 20) {
				if ($fi && !$sv) {
					return "Oletettu saapumisaika " . $this->getMonthFi($month) . " loppupuolella";
				} elseif ($no) {
					return "Forventes inn i slutten av " . $this->getMonthSv($month);
				} else {
					return "Beräknas in i slutet av " . $this->getMonthSv($month);
				}
					
			} else { // tja, när är den något annat? Tomt blir nog bra
				return "";
			}
			
		} elseif ($prec == "M") { // månad
			$month = date("n", strtotime($dat));
			if ($fi && !$sv) {
				return "Oletettu saapumisaika " . $this->getMonthFi($month);		
			} elseif ($no) {
				return "Forventes inn i " . $this->getMonthSv($month);
			} else {
				return "Beräknas in i " . $this->getMonthSv($month);
			}

		} elseif ($prec == "U") { // detta om det är ett okänt leveransbesked
		    if ($bestallningsgrans == 0)  {
			
				if ($fi && !$sv) {
					if ($pricesite) {
						return "Tilaustuote";
					} else {
						// return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\">Tilaustuote</></font></a>";													
						return "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a>";
					}
				} elseif ($no) {
					if ($pricesite) {
						return "bestillingsvare";
					} else {
						return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produktet er tatt hjem etter bestilling.')\" style=\"text-decoration: none\">bestillingsvare</></font></a>";
					}
				} else {
					if ($mobilsite || $pricesite) {
						return "beställningsvara";
					} else {
						// return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning.')\" style=\"text-decoration: none\">beställningsvara</></font></a>";
						return "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a>";
					}
				}
			} else if ($fi && !$sv) {
				if ($pricesite) {
					return "toimituspäivämäärä ei ole määritelty";
				} else {
					return "<a onMouseOver=\"return escape('Tuote on tilattu mutta toimitusaika ei ole tiedossa. Emme ole saaneet tilausvahvistusta toimittajalta')\" style=\"text-decoration: none\">Toimituspäivämäärä ei ole määritelty</a>";
				}
			} elseif ($no) {
				if ($pricesite) {
					return "ukjent leveringsdato";
				} else {
					return "<a onMouseOver=\"return escape('Leveringsdato ukjent innebærer at vår leverandør per i dag ikke har noen beregnet dato for når de får inn varen hos seg. Så fort vi får ny leveringsdato oppdateres dette.')\" style=\"text-decoration: underline\">Ukjent leveringsdato</a>";
				}
			} else {
				if ($mobilsite || $pricesite) {
					return "leveransdatum okänt";
				} else {
					return "<a onMouseOver=\"return escape('Leveransdatum okänt innebär att vår leverantör i dagsläget inte har något beräknat leveransdatum för när de får in varan till sig. Så fort vi får ett nytt leveransbesked uppdateras detta.')\" style=\"text-decoration: underline\">Leveransdatum okänt</a>";
				}
			}

		} elseif ($prec == "QU" && $bestallningsgrans != 0) { // om det inte finns tillräckligt med beställda antal för att täck de som finns på kö
			if ($fi && !$sv) {
				if ($pricesite) {
					return "Tilapäisesti loppu";
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilapäisesti lopussa. <br>Normaali toimitusaika on  <b>$lev_datum_norm_fi </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: underline\">Tilapäisesti loppu</font></a>";
				}
			} elseif ($no) {
				if ($pricesite) {
					return "midlertidig utsolgt";
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produktet er for øyeblikket utsolgt.')\" style=\"text-decoration: none\">Midlertidig utsolgt</font></a>";
				}
			} else {
				if ($mobilsite || $pricesite) {
					return "tillfälligt slut";
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten är tillfälligt slut i lager.')\" style=\"text-decoration: none\">Tillfälligt slut</font></a>";
				}
			}

		} elseif ($bestallningsgrans == 0)  {
			
			if ($fi && !$sv) {
				if ($pricesite) {
					return "Tilaustuote";
				} else {
					// return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Tuote on tilaustavara. <br>Normaali toimitusaika on <b>$lev_datum_norm </b> päivää varastoomme<br>Tämä toimitusaika vaatii että toimittajalla on tuote varastossa')\" style=\"text-decoration: none\">Tilaustuote</></font></a>";													
					return "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a>";
				}
			} elseif ($no) {
				if ($pricesite) {
					return "bestillingsvare";
				} else {
					return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produktet er tatt hjem etter bestilling.')\" style=\"text-decoration: none\">bestillingsvare</></font></a>";
				}
			} else {
				if ($mobilsite || $pricesite) {
					return "beställningsvara";
				} else {
					// return "<font size=\"1\" face=\"Verdana, Arial\" color=\"#85000D\"><a onMouseOver=\"return escape('Produkten tas hem på beställning.')\" style=\"text-decoration: none\">beställningsvara</></font></a>";
					return "<a onMouseOver=\"this.T_FONTSIZE='12px';this.T_PADDING=5;this.T_STATIC=true;this.T_SHADOWWIDTH=2;this.T_WIDTH=400;this.T_BGCOLOR='#FFF8BA';this.T_TEXTALIGN='left';return escape('" . l('Delivery info onmouseover') . "')\">" . l('Delivery info') . "</a>";
				}
			}
				
		} else {
			return $dat;
		}
		
	}
	function getMonthFi($month) {
		if ($month == 1)
			return "tammikuu";
		elseif ($month == 2)
			return "helmikuu";
		elseif ($month == 3)
			return "maaliskuu";
		elseif ($month == 4)
			return "huhtikuu";
		elseif ($month == 5)
			return "toukokuu";
		elseif ($month == 6)
			return "kesäkuu";
		elseif ($month == 7)
			return "heinäkuu";
		elseif ($month == 8)
			return "elokuu";
		elseif ($month == 9)
			return "syyskuu";
		elseif ($month == 10)
			return "lokakuu";
		elseif ($month == 11)
			return "marraskuu";
		elseif ($month == 12)
			return "joulukuu";
		else 
			return "";
	}
	function getMonthSv($month) {
		global $no;
		if ($month == 1) {
			if ($no) {
				return "januar";
			} else {
				return "januari";
			}
		} elseif ($month == 2) {
			if ($no) {
				return "februar";
			} else {
				return "februari";
			}
		} elseif ($month == 3) {
			if ($no) {
				return "mars";
			} else {
				return "mars";
			}
		} elseif ($month == 4) {
			if ($no) {
				return "april";
			} else {
				return "april";
			}
		} elseif ($month == 5) {
			if ($no) {
				return "mai";
			} else {
				return "maj";
			}
		} elseif ($month == 6) {
			if ($no) {
				return "juni";
			} else {
				return "juni";
			}
		} elseif ($month == 7) {
			if ($no) {
				return "juli";
			} else {
				return "juli";
			}
		} elseif ($month == 8) {
			if ($no) {
				return "august";
			} else {
				return "augusti";
			}
		} elseif ($month == 9) {
			if ($no) {
				return "september";
			} else {
				return "september";
			}
		} elseif ($month == 10) {
			if ($no) {
				return "oktober";
			} else {
				return "oktober";
			}
		} elseif ($month == 11) {
			if ($no) {
				return "november";
			} else {
				return "november";
			}
		} elseif ($month == 12) {
			if ($no) {
				return "desember";
			} else {
				return "december";
			}
		} else {
			return "";
		}
	}

	function checkOnQueue($artnr) {
		
		$select = "SELECT m_storage.qtyreserved AS queue ";
		$select .= "FROM m_product ";
		$select .= "JOIN m_storage ON m_storage.m_product_id = m_product.m_product_id ";
		$select .= "WHERE m_product.m_product_id = '$artnr' ";
		// echo $select;

		$res = ($this->conn_ad) ? @pg_query($this->conn_ad, $select) : false;
		$row = $res ? pg_fetch_object($res) : null;

			if ($res && pg_num_rows($res) > 0) {
				
				echo $row->queue;
				
			} else {
			
				echo "0&nbsp;";
			
			}
		
	}

public static function getBestDatePromisedWeb($art, $warehouseId, $count) {
    $pg = Db::getConnectionAD(false);
    if (!$pg) { throw new Exception('PostgreSQL-anslutning saknas.'); }

    $warehouseId = (int)$warehouseId;
    $count = (int)$count;
    if ($count < 1) $count = 1;

    // Säkerställ att vi använder produktens VALUE
    $value = trim((string)$art);
    if ($value === '') return null;

    if (ctype_digit($value)) {
        $res = ($pg) ? @pg_query_params($pg, "SELECT value FROM m_product WHERE m_product_id = $1", array((int)$value)) : false;
        if (!$res || pg_num_rows($res) < 1) return null;
        $row = $res ? pg_fetch_assoc($res) : null;
        $value = $row['value'];
    } else {
        $res = ($pg) ? @pg_query_params($pg, "SELECT value FROM m_product WHERE value = $1", array($value)) : false;
        if (!$res || pg_num_rows($res) < 1) {
            $res = ($pg) ? @pg_query_params($pg, "SELECT value FROM m_product WHERE UPPER(value) = UPPER($1)", array($value)) : false;
            if (!$res || pg_num_rows($res) < 1) return null;
            $row = $res ? pg_fetch_assoc($res) : null;
            $value = $row['value'];
        }
    }

    $sql = "SELECT * FROM get_best_datepromised_web($1::bpchar, $2::numeric, $3::numeric)";
    $res = ($pg) ? @pg_query_params($pg, $sql, array($value, $warehouseId, $count)) : false;
    if ($res === false) throw new Exception('PG query error: ' . pg_last_error($pg));
    if ($res && pg_num_rows($res) < 1) return null;

    $row = $res ? pg_fetch_assoc($res) : null;
    $onStock = false;
    if (isset($row['a_on_stock'])) {
        $v = $row['a_on_stock'];
        $onStock = ($v === 't' || $v === '1' || $v === 1 || $v === true);
    }

    return array(
        'on_stock'     => $onStock,
        'datepromised' => (!empty($row['a_datepromised'])) ? substr($row['a_datepromised'], 0, 10) : null,
        'precision'    => isset($row['a_datepromisedprecision']) ? trim($row['a_datepromisedprecision']) : null,
    );
}

public static function renderDeliveryLabelSv($info) {
    if ($info === null) return '';
    if (!empty($info['on_stock'])) return 'Finns i lager';

    $dat  = isset($info['datepromised']) ? $info['datepromised'] : null;
    $prec = isset($info['precision']) ? strtoupper($info['precision']) : null;

    if ($prec === 'D' && $dat) return 'Beräknas in ' . $dat;
    if ($prec === 'W' && $dat) return 'Beräknas in vecka ' . (int)date('W', strtotime($dat));
    if ($prec === 'P' && $dat) {
        $day   = (int)date('j', strtotime($dat));
        $month = (int)date('n', strtotime($dat));
        $svMon = array('', 'januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december');
        if ($day <= 10)  return 'Beräknas in i början av ' . $svMon[$month];
        if ($day <= 20)  return 'Beräknas in i mitten av '  . $svMon[$month];
        return 'Beräknas in i slutet av ' . $svMon[$month];
    }
    if ($prec === 'M' && $dat) {
        $month = (int)date('n', strtotime($dat));
        $svMon = array('', 'januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december');
        return 'Beräknas in i ' . $svMon[$month];
    }
    if ($prec === 'QU') return 'Tillfälligt slut';
    return 'Leveransdatum okänt';
}

public static function getNextDeliveryLabelForBox($art, $warehouseId = 1000000, $count = 1) {
    $count = (int)$count;
    if ($count < 1) $count = 1;

    $info = self::getBestDatePromisedWeb($art, (int)$warehouseId, $count);
    if (!$info || !empty($info['on_stock'])) {
        return '';
    }

    // Visa alltid etikett (D/W/P/M/U/QU) via render-funktionen
    return self::renderDeliveryLabelSv($info);
}

}
?>
