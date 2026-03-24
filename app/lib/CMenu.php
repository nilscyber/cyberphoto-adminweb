<?php

Class CMenu {

	function getMenu($websection) {
		global $sv, $no, $fi;
	
		$select  = "SELECT menuID, kategori, kategori_fi, kategori_no, menuByCat, menuNameSE, menuLincSE, menuNameNO, menuLincNO, menuNameFI, menuLincFI, menuShowPublic, menuIsParent, menuIsSpacing, menuSection ";
		$select .= "FROM cyberphoto.menu_web ";
		$select .= "JOIN Kategori ON menuByCat = kategori_id ";
		$select .= "WHERE menuSection = '$websection' ";
		if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND menuShowPublic = -1 ";
		}
		$select .= "AND menuParentMenu = 0 ";
		if ($fi) {
			$select .= "AND menuActiveFI = -1 ";
			if ($sv) {
				$select .= "ORDER BY menuOrder ASC, kategori ASC, menuNameSE ASC ";
			} else {
				$select .= "ORDER BY menuOrder ASC, kategori_fi ASC, menuNameFI ASC ";
			}
		} elseif ($no) {
			$select .= "AND menuActiveNO = -1 ";
			$select .= "ORDER BY menuOrder ASC, kategori_no ASC, menuNameNO ASC ";
		} else {
			$select .= "AND menuActiveSE = -1 ";
			$select .= "ORDER BY menuOrder ASC, kategori ASC, menuNameSE ASC ";
		}
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_object($res)) {
			
				if ($row->menuByCat > 0) {
					// $menuName = $this->getMenuCategoryName($row->menuByCat);
					if ($fi && !$sv) {
						$menuName = $row->kategori_fi;
					} elseif ($no) {
						$menuName = $row->kategori_no;
					} else {
						$menuName = $row->kategori;
					}
				} else {
					if ($fi && !$sv) {
						if ($row->menuNameFI != "") {
							$menuName = $row->menuNameFI;
						} else {
							$menuName = $row->menuNameSE;
						}
					} elseif ($no) {
						if ($row->menuNameNO != "") {
							$menuName = $row->menuNameNO;
						} else {
							$menuName = $row->menuNameSE;
						}
					} else {
						$menuName = $row->menuNameSE;
					}
				}
				
				if ($row->menuByCat > 0 && $row->menuLincSE == "") {
					$menuLinc = $row->menuSection . "/category/" . $row->menuByCat;
				} elseif ($fi && !$sv) {
					if ($row->menuLincFI != "") {
						$menuLinc = $row->menuLincFI;
					} else {
						$menuLinc = $row->menuLincSE;
					}
				} elseif ($no) {
					if ($row->menuLincNO != "") {
						$menuLinc = $row->menuLincNO;
					} else {
						$menuLinc = $row->menuLincSE;
					}
				} else {
					$menuLinc = $row->menuLincSE;
				}
				
				if (preg_match("/category/i", $menuLinc)) {
					if ($fi && !$sv) {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori_fi)));
					} elseif ($no) {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori_no)));
					} else {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori)));
					}
				}

				if ($fi && !$sv) {
					$menuLinc = preg_replace("/jakt\-fritid/", "outdoor", $menuLinc);
					$menuLinc = preg_replace("/mobiltelefoni/", "mobiili", $menuLinc);
					$menuLinc = preg_replace("/mobiltelefoner/", "matkapuhelimet", $menuLinc);
					$menuLinc = preg_replace("/alla/", "kaikki", $menuLinc);
					$menuLinc = preg_replace("/batterier/", "akut", $menuLinc);
				} else {
					$menuLinc = preg_replace("/jakt\-fritid/", "outdoor", $menuLinc);
				}
				/*
				$menuLinc = preg_replace("/\,/", "", $menuLinc);
				$menuLinc = preg_replace("/---/", "-", $menuLinc);
				$menuLinc = preg_replace("/--/", "-", $menuLinc);
				*/
				
				if ($row->menuShowPublic == 0) {
					$menuName = "<span class=\"text_grey\">" . $menuName . "</span>";
				}
				if ($row->menuByCat > 0 && ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.64x")) {
					$menuName .= " (" . $row->menuByCat . ")";
				}
				
				if ($row->menuIsSpacing == -1) {
					echo "<li>&nbsp;</li>\n";
				} elseif ($fi && !$sv) {
					if ($row->menuIsParent == -1) {
						echo "<li>" . $menuName . "\n";
						$this->getSubMenu($row->menuID);
					} else {
						echo "<li><a href=\"/" . $menuLinc . "\">" . $menuName . "</a></li>\n";
					}
				} elseif ($no) {
					if ($row->menuIsParent == -1) {
						echo "<li>" . $menuName . "\n";
						$this->getSubMenu($row->menuID);
					} else {
						echo "<li><a href=\"/" . $menuLinc . "\">" . $menuName . "</a></li>\n";
					}
				} else {
					if ($row->menuIsParent == -1) {
						echo "<li>" . $menuName . "\n";
						$this->getSubMenu($row->menuID);
					} else {
						echo "<li><a href=\"/" . $menuLinc . "\">" . $menuName . "</a></li>\n";
					}
				}
				
			}
		
		}

	}

	function getSubMenu($menuparent) {
		global $sv, $no, $fi;
	
		echo "\t<ul>\n";

		$select  = "SELECT kategori, kategori_fi, kategori_no, menuByCat, menuNameSE, menuLincSE, menuNameNO, menuLincNO, menuNameFI, menuLincFI, menuShowPublic, menuIsSpacing, menuSection ";
		$select .= "FROM cyberphoto.menu_web ";
		$select .= "JOIN Kategori ON menuByCat = kategori_id ";
		$select .= "WHERE menuParentMenu = " . $menuparent . " ";
		if (!CCheckIP::checkIpAdressExtendedPrivileges($_SERVER['REMOTE_ADDR'])) {
			$select .= "AND menuShowPublic = -1 ";
		}
		if ($fi) {
			$select .= "AND menuActiveFI = -1 ";
			if ($sv) {
				$select .= "ORDER BY menuOrder DESC, kategori ASC, menuNameSE ASC ";
			} else {
				$select .= "ORDER BY menuOrder DESC, kategori_fi ASC, menuNameFI ASC ";
			}
		} elseif ($no) {
			$select .= "AND menuActiveNO = -1 ";
			$select .= "ORDER BY menuOrder DESC, kategori_no ASC, menuNameNO ASC ";
		} else {
			$select .= "AND menuActiveSE = -1 ";
			$select .= "ORDER BY menuOrder DESC, kategori ASC, menuNameSE ASC ";
		}
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {
			
				if ($fi && !$sv && $row->menuNameFI != "") {
					$menuName = $row->menuNameFI;
				} elseif ($fi && $sv && $row->menuNameSE != "") {
					$menuName = $row->menuNameSE;
				} elseif ($no && $row->menuNameNO != "") {
					$menuName = $row->menuNameNO;
				} elseif ($sv && $row->menuNameSE != "") {
					$menuName = $row->menuNameSE;
				} elseif ($row->menuByCat > 0) {
					// $menuName = $this->getMenuCategoryName($row->menuByCat);
					if ($fi && !$sv) {
						$menuName = $row->kategori_fi;
					} elseif ($no) {
						$menuName = $row->kategori_no;
					} else {
						$menuName = $row->kategori;
					}
				} else {
					if ($fi && !$sv) {
						if ($row->menuNameFI != "") {
							$menuName = $row->menuNameFI;
						} else {
							$menuName = $row->menuNameSE;
						}
					} elseif ($no) {
						if ($row->menuNameNO != "") {
							$menuName = $row->menuNameNO;
						} else {
							$menuName = $row->menuNameSE;
						}
					} else {
						$menuName = $row->menuNameSE;
					}
				}
				if ($row->menuShowPublic == 0) {
					$menuName = "<span class=\"text_grey\">" . $menuName . "</span>";
				}
				if ($row->menuByCat > 0 && ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x" || $_SERVER['REMOTE_ADDR'] == "192.168.1.64x")) {
					$menuName .= " (" . $row->menuByCat . ")";
				}
				if ($row->menuByCat > 0 && $row->menuLincSE == "") {
					$menuLinc = $row->menuSection . "/category/" . $row->menuByCat;
				} elseif ($fi && !$sv) {
					if ($row->menuLincFI != "") {
						$menuLinc = strtolower($row->menuLincFI);
					} else {
						$menuLinc = strtolower($row->menuLincSE);
					}
				} elseif ($no) {
					if ($row->menuLincNO != "") {
						$menuLinc = strtolower($row->menuLincNO);
					} else {
						$menuLinc = strtolower($row->menuLincSE);
					}
				} else {
					$menuLinc = strtolower($row->menuLincSE);
				}
				if ($row->menuByCat > 0 && preg_match("/category/i", $menuLinc)) {
					if ($fi && !$sv) {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori_fi)));
					} elseif ($no) {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori_no)));
					} else {
						$menuLinc = $menuLinc . "/" . strtolower(Tools::replace_special_char(trim($row->kategori)));
					}
				}
				
				if ($fi && !$sv) {
					$menuLinc = preg_replace("/jakt\-fritid/", "outdoor", $menuLinc);
					$menuLinc = preg_replace("/mobiltelefoni/", "mobiili", $menuLinc);
					$menuLinc = preg_replace("/mobiltelefoner/", "matkapuhelin", $menuLinc);
					$menuLinc = preg_replace("/batterier/", "akut", $menuLinc);
				} else {
					$menuLinc = preg_replace("/jakt\-fritid/", "outdoor", $menuLinc);
				}
				/*
				$menuLinc = preg_replace("/\,/", "", $menuLinc);
				$menuLinc = preg_replace("/---/", "-", $menuLinc);
				$menuLinc = preg_replace("/--/", "-", $menuLinc);
				*/

				if ($row->menuIsSpacing == -1) {
					echo "<li>&nbsp;</li>\n";
				} else {
					echo "<li><a href=\"/" . $menuLinc . "\">" . $menuName . "</a></li>\n";
				}
				
			}

		}

		echo "\t</ul>\n";
		echo "</li>\n";

	}

	function getMenuCategoryName($category) {
		global $sv, $fi, $no;
		
		if ($fi && !$sv) {
			$select = "SELECT kategori_fi AS kategori FROM Kategori WHERE kategori_id = $category ";
		} elseif ($no) {
			$select = "SELECT kategori_no AS kategori FROM Kategori WHERE kategori_id = $category ";
		} else {
			$select = "SELECT kategori FROM Kategori WHERE kategori_id = $category ";
		}
		
		$res = mysqli_query(Db::getConnection(), $select);
		$row = mysqli_fetch_object($res);

		if (mysqli_num_rows($res) > 0) {
			return $row->kategori;
		} else {
			return;
		}
	}

	function replace_char($string) {
        $from = array("å", "ä", "ö", "Å", "Ä", "Ö", ".", "-", "?", " ", "ø", "(", ")");
        $to = array("a", "a", "o", "A", "A", "O", "-", "-", "-", "-", "o", "", "");
        return str_replace($from, $to, $string);
	}
	
	// ***************************** NEDAN ÄR FÖR ADMIN ******************************************
	
	function getMenuAdmin($websection) {
		global $sv;
	
		$select  = "SELECT menuID, kategori, menuByCat, menuNameSE, menuLincSE, menuShowPublic, menuIsParent, menuIsSpacing, menuOrder ";
		$select .= "FROM cyberphoto.menu_web ";
		$select .= "JOIN Kategori ON menuByCat = kategori_id ";
		$select .= "WHERE menuSection = '$websection' ";
		$select .= "AND menuParentMenu = 0 ";
		$select .= "AND menuActiveSE = -1 ";
		$select .= "ORDER BY menuOrder ASC, kategori ASC, menuNameSE ASC ";
		// echo $select;
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
		
			while ($row = mysqli_fetch_object($res)) {
			
				if ($row->menuByCat > 0) {
					$menuName = $row->kategori;
				} else {
					$menuName = $row->menuNameSE;
				}
				
				if ($row->menuByCat > 0) {
					$menuName .= " (" . $row->menuByCat . ")";
				}
				
				if ($row->menuIsSpacing == -1) {
					echo "<li><span class=\"menusortOrder\">" . $row->menuOrder . "</span>&nbsp;<a href=\"?change=" . $row->menuID . "\">Ändra</a></li>\n";
				} else {
					if ($row->menuIsParent == -1) {
						echo "<li><span class=\"menusortOrder\">" . $row->menuOrder . "</span>&nbsp;" . $menuName . " - <a href=\"?change=" . $row->menuID . "\">Ändra</a>\n";
						$this->getSubMenuAdmin($row->menuID);
					} else {
						echo "<li><span class=\"menusortOrder\">" . $row->menuOrder . "</span>&nbsp;" . $menuName . " - <a href=\"?change=" . $row->menuID . "\">Ändra</a></li>\n";
					}
				}
				
			}
		
		}

	}

	function getSubMenuAdmin($menuparent) {
		global $sv, $no, $fi;
	
		echo "\t<ul>\n";

		$select  = "SELECT menuID, kategori, menuByCat, menuNameSE, menuLincSE, menuShowPublic, menuIsSpacing, menuOrder ";
		$select .= "FROM cyberphoto.menu_web ";
		$select .= "JOIN Kategori ON menuByCat = kategori_id ";
		$select .= "WHERE menuParentMenu = " . $menuparent . " ";
		$select .= "AND menuActiveSE = -1 ";
		$select .= "ORDER BY menuOrder DESC, kategori ASC, menuNameSE ASC ";
		$res = mysqli_query(Db::getConnection(), $select);
		$check = mysqli_num_rows($res);

		if (mysqli_num_rows($res) > 0) {
			
			while ($row = mysqli_fetch_object($res)) {
			
				if ($row->menuByCat > 0) {
					$menuName = $row->kategori;
				} else {
					$menuName = $row->menuNameSE;
				}
				if ($row->menuByCat > 0) {
					$menuName .= " (" . $row->menuByCat . ")";
				}

				if ($row->menuIsSpacing == -1) {
					echo "<li><span class=\"menusortOrder\">" . $row->menuOrder . "</span>&nbsp;<a href=\"?change=" . $row->menuID . "\">Ändra</a></li>\n";
				} else {
					echo "<li><span class=\"menusortOrder\">" . $row->menuOrder . "</span>&nbsp;" . $menuName . " - <a href=\"?change=" . $row->menuID . "\">Ändra</a></li>\n";
				}
				
			}

		}

		echo "\t</ul>\n";
		echo "</li>\n";

	}

	function getSpecMenu($menuID) {

		$select  = "SELECT * FROM cyberphoto.menu_web WHERE menuID = '" . $menuID . "' ";
		$res = mysqli_query(Db::getConnection(), $select);
		$rows = mysqli_fetch_object($res);
		return $rows;

	}

	function getMenuKategori() {
		global $addByCat;

		$select  = "SELECT kategori, kategori_id FROM cyberphoto.Kategori WHERE visas = -1 ORDER BY kategori ASC ";

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_object($res)) {
		
			echo "<option value=\"" . $row->kategori_id . "\"";
				
			if ($addByCat == $row->kategori_id) {
				echo " selected";
			}
				
			echo ">" . $row->kategori . " (" . $row->kategori_id . ")</option>\n";
				
		}

	}

	function getParentKategori() {
		global $addParentMenu;

		if ($_SESSION['menudepartment'] == 5) {
			$menuSection = "cybairgun";
		} elseif ($_SESSION['menudepartment'] == 4) {
			$menuSection = "jakt-fritid";
		} elseif ($_SESSION['menudepartment'] == 3) {
			$menuSection = "batterier";
		} elseif ($_SESSION['menudepartment'] == 2) {
			$menuSection = "mobiltelefoni";
		} else {
			$menuSection = "foto-video";
		}

		$select  = "SELECT menuID, menuNameSE, menuByCat FROM cyberphoto.menu_web WHERE menuIsParent = -1 AND menuSection = '" . $menuSection . "' ORDER BY menuOrder ASC, menuNameSE ASC ";

		$res = mysqli_query(Db::getConnection(), $select);

		while ($row = mysqli_fetch_object($res)) {
		
			echo "<option value=\"" . $row->menuID . "\"";
				
			if ($addParentMenu == $row->menuID) {
				echo " selected";
			}
			
			if ($row->menuByCat > 0) {
				$visanamnet = $this->getMenuCategoryName($row->menuByCat);
			} else {
				$visanamnet = $row->menuNameSE;
			}
			echo ">" . $visanamnet . " (" . $row->menuID . ")</option>\n";
				
		}

	}

	function menuAdminChange($addid,$addByCat,$addActiveSE,$addActiveNO,$addActiveFI,$addNameSE,$addNameNO,$addNameFI,$addLincSE,$addLincNO,$addLincFI,$addOrder,$addIsSpacing,$addShowPublic,$addIsParent,$addParentMenu,$addcreatedby) {

		$updt  = "UPDATE cyberphoto.menu_web ";
		$updt .= "SET menuByCat = '$addByCat',  ";
		$updt .= "menuActiveSE = '$addActiveSE', menuNameSE = '$addNameSE', menuLincSE = '$addLincSE', ";
		$updt .= "menuActiveNO = '$addActiveNO', menuNameNO = '$addNameNO', menuLincNO = '$addLincNO', ";
		$updt .= "menuActiveFI = '$addActiveFI', menuNameFI = '$addNameFI', menuLincFI = '$addLincFI', ";
		$updt .= "menuIsSpacing = '$addIsSpacing', menuOrder = '$addOrder', menuShowPublic = '$addShowPublic', ";
		$updt .= "menuParentMenu = '$addParentMenu', menuIsParent = '$addIsParent', ";
		$updt .= "menuUpdateBy = '$addcreatedby', menuUpdateDate = now(), menuUpdateIP = '" . $_SERVER['REMOTE_ADDR'] . "' ";
		$updt .= "WHERE menuID = '$addid'";

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: menu_web.php");

	}

	function menuAdminAdd($addByCat,$addActiveSE,$addActiveNO,$addActiveFI,$addNameSE,$addNameNO,$addNameFI,$addLincSE,$addLincNO,$addLincFI,$addOrder,$addIsSpacing,$addShowPublic,$addIsParent,$addParentMenu,$addcreatedby) {

		if ($_SESSION['menudepartment'] == 5) {
			$menuSection = "cybairgun";
		} elseif ($_SESSION['menudepartment'] == 4) {
			$menuSection = "jakt-fritid";
		} elseif ($_SESSION['menudepartment'] == 3) {
			$menuSection = "batterier";
		} elseif ($_SESSION['menudepartment'] == 2) {
			$menuSection = "mobiltelefoni";
		} else {
			$menuSection = "foto-video";
		}
		
		$updt  = "INSERT INTO cyberphoto.menu_web ";
		$updt .= "(menuByCat,menuActiveSE,menuNameSE,menuLincSE,menuActiveNO,menuNameNO,menuLincNO,menuActiveFI,menuNameFI,menuLincFI, "; 
		$updt .= "menuIsSpacing,menuOrder,menuShowPublic,menuParentMenu,menuIsParent,menuAddBy,menuAddDate,menuAddIP,menuSection) ";
		$updt .= "VALUES ";
		$updt .= "('$addByCat','$addActiveSE','$addNameSE','$addLincSE','$addActiveNO','$addNameNO','$addLincNO','$addActiveFI','$addNameFI','$addLincFI', ";
		$updt .= "'$addIsSpacing','$addOrder','$addShowPublic','$addParentMenu','$addIsParent', ";
		$updt .= "'$addcreatedby',now(),'" . $_SERVER['REMOTE_ADDR'] . "','$menuSection') ";

		// echo $updt;
		// exit;
		
		$res = mysqli_query(Db::getConnection(true), $updt);

		header("Location: menu_web.php");

	}
	
}
?>