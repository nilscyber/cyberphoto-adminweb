<?php

Class CEmployees {

	var $conn_my;

	function __construct() {
			
		$this->conn_my = Db::getConnection();
		
	}

	function listRandomEmployees($rand_num) {

		$sel = "SELECT * FROM Anstallda WHERE jobbar = -1";
		// echo $sel;
		$res = mysqli_query($this->conn_my, $sel);
		$num_rows = mysqli_num_rows($res);
		// echo "här:" . $num_rows;
		//echo "<br>";
		if ($rand_num == $num_rows) {
			echo "<b>Det antal du angett är alla anställda och således inget slumpmässigt urval</b>";
			return null;
		}
		if ($rand_num > $num_rows) {
			echo "<b>Det antal du angett är fler än alla anställda och således inget slumpmässigt urval</b>";
			return null;
		}
		echo "<h1>Resultat:</h1><br>";
		
		$count = 0;
	//	$arg[0] = 0;
		$emp = "";
		while (count($arg) < $rand_num) {
			$duplicate = false;
			$rand = 0;
			//echo "<br>";
			mt_srand ((double) microtime() * 1000000);
			$rand = mt_rand (0, $num_rows - 1);
			//echo "<br>";
			//echo "h?r: ".		
			$emp = $this->get_employee($rand);
			
			$i = 0;
			$n = count($arg); 
			while ($i <= $n ) {
				
				//echo $i . ": " . $arg[$i] . ", " . $emp . "<p>";
				if ($arg[$i] == $emp) {
					//echo "duplikat: " . $emp;
					$duplicate = true;				
				}
				$i += 1;
			}


			if (!$duplicate)
				$count += 1;
			else
				$emp = null;
				
			if ($emp != null) {
				echo $emp . "<br>";
				$arg[$count] = $emp;
			}
		}

	}
	function checkDuplicate($arg, $new) {
		echo "h?r: " . $n = count($arg); 

	}

	function get_employee($rand_num) {
		
		$sel = "SELECT * FROM Anstallda WHERE jobbar = -1 OR jobbar = 1 ORDER BY sign ";
		$res = mysqli_query($this->conn_my, $sel);
		$i = 0;
		while ($row = mysqli_fetch_object($res)) {
			if ($i == $rand_num) {
				//echo "ja!" . $row->sign . " " . $row->namn. "<br>";	
				
				return $row->sign . " " . $row->namn;

				}
			else {
				//echo "<br>h?r: " . $i . ", " . $rand_num . "<br>";
			}
			$num_rows = mysqli_num_rows($res);
			$i += 1;
		}	
		return null;
	}
	function gen_pass ($pass_len)  { 

		$nps = ""; 
		$c = "";

		// Seed the random number generator
		mt_srand ((double) microtime() * 1000000); 
		while (strlen($nps)<$pass_len) { 

			// Ge $c ett värde från slumpmässigt valt ASCII värde

			$c = chr(mt_rand (48,57)); 

			// Lägg till på $nps om det är i rätt format
			if (eregi("^[0-9]$", $c)) 
			$nps = $nps.$c; 
		}
		return ($nps);
	}

}

?>
