<?php

Class CCategories {

	function __construct() {
			
	}

	function getSubDomain($categories) {
	
		switch ($categories) {
			case ($categories == 1000147 || $categories == 1000050 || $categories == 1000065 || $categories == 1000066 || $categories == 1000051 || 
					$categories == 1000068 || $categories == 1000069 || $categories == 539 || $categories == 1000067 || $categories == 1000127 || $categories == 685):
				$subdomain = "cybairgun";
				break;
			case ($categories == 585 || $categories == 1000043 || $categories == 1000041 || $categories == 1000055 || $categories == 1000042):
				$subdomain = "mobil";
				break;
			case ($categories == 1000059 || $categories == 616 || $categories == 615 || $categories == 1000060 || $categories == 590 || $categories == 1000062 || 
					$categories == 617 || $categories == 1000063 || $categories == 1000061 || $categories == 584 || $categories == 1000077):
				$subdomain = "hobby";
				break;
			case ($categories == 1000082 || $categories == 1000083 || $categories == 1000118 || $categories == 1000116 || $categories == 1000088 || $categories == 1000100 || 
					$categories == 1000101 || $categories == 1000110):
				$subdomain = "house";
				break;
			default:
				$subdomain = "foto";
		}
		
		return $subdomain;

	}

	function controlCybairgun($categories) {
	
		if ($categories == 1000147 || $categories == 1000050 || $categories == 1000065 || $categories == 1000066 || $categories == 1000051 || 
			$categories == 1000068 || $categories == 1000069 || $categories == 539 || $categories == 1000067 || $categories == 1000127) {
			return true;
		} else {
			return false;
		}

	}

	function controlHobby($categories) {
	
		if ($categories == 584 || $categories == 1000071 || $categories == 1000077 || $categories == 578 || $categories == 1000054 || 
			$categories == 538 || $categories == 1000072 || $categories == 183 || $categories == 1000150 || $categories == 697 || $categories == 1000191) {
			return true;
		} else {
			return false;
		}

	}
	
	function getIsInsurable($category) {

		$select  = "SELECT isInsurable ";
		$select .= "FROM cyberphoto.Kategori ";
        $select .= "WHERE kategori_id = $category ";

		$row = mysqli_fetch_object(mysqli_query(Db::getConnection(), $select));
		
		if ($row->isInsurable == -1) {
			return true;
		} else {
			return false;
		}
	
	}

	static function getMainCategory($kategori_id_parent) {

		$select  = "SELECT kategori_id, kategori_id_parent ";
		$select .= "FROM cyberphoto.Kategori ";
        $select .= "WHERE kategori_id = $kategori_id_parent ";
		
		if ($_SERVER['REMOTE_ADDR'] == "192.168.1.89x") {
			echo $select . "<br>";
		}

		$row = mysqli_fetch_object(mysqli_query(Db::getConnection(), $select));
		
		if ($row->kategori_id_parent == 0) {
			return $row->kategori_id;
		} else {
			return CCategories::getMainCategory($row->kategori_id_parent);
		}
	
	}
	
}

?>
