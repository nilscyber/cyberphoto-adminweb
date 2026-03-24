
<html>

<?php
if ($tecken == "")
	$tecken = 16;

echo generate_pass($tecken);

function generate_pass ($pass_len)  { 


$nps = ""; 

// Seed the random number generator
mt_srand ((double) microtime() * 1000000); 

while (strlen($nps)<$pass_len) { 
	
	// Ge $c ett värde från slumpmässigt valt ASCII värde
	//$c = chr(mt_rand (33,122)); 
        $c = chr(mt_rand (33,125));
	
	// Lägg till på $nps om det är i rätt format
	//if (eregi("^[a-zA-Z0-9]$", $c)) 
		$nps = $nps.$c; 
}
 	return ($nps); 
}

?>
</html>
