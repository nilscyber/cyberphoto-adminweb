	    <?php 	If ($_SESSION['RememberPageView'] == 1 && $prislistan == "" ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    	elseif ($_SESSION['RememberPageView'] == 1 && $prislistan == 0 ) {
	    	$_SESSION['RememberPageView'] = "";
	    	}
	    	elseif ($prislistan == 1 ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    	
	    	If ($_SESSION['RememberMoms'] == 1 && $showmoms == "" ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}
	    	elseif ($_SESSION['RememberMoms'] == 1 && $showmoms == 0 ) {
	    	$_SESSION['RememberMoms'] = "";
	    	}
	    	elseif ($showmoms == 1 ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}
	    ?>

	<div align="center"><center>
       	<table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="93%">
          <tr>
            <td width="100%">
            
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
          <tr>
            <td width="50"><a onMouseOver="return escape('<b>Prislista</b><br>Välj denna för vanlig prislista utan bilder.')"><font size="1" face="Verdana">Prislista</font></a></td>
            <td width="20">
            <input type="radio" value="0" name="prislistan" onClick="submit()"<?php if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) echo " checked";?>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="50"><font size="1" face="Verdana"><a onMouseOver="return escape('<b>Bildgalleri</b><br>Välj denna för att se prislistan med bilder för snabbt överblick.')">Bildgalleri</font></a></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="1" name="prislistan" onClick="submit()"<?php if ($_SESSION['RememberPageView'] == 1 ) echo " checked";?>></font></td>

		<?php
		
		$ip_address = $REMOTE_ADDR;
		
		if ((eregi("81.8.240.", $ip_address)) || ($ip_address == "81.8.144.102") || ($ip_address == "213.79.137.61") || ($ip_address == "81.8.223.103") || (eregi("192.168.1.", $ip_address)))
		
		{ ?>

            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="40"><a onMouseOver="return escape('<b>Endast i lager</b><br>Filtrerar listan så att endast produkter som finns på hyllan i vårt lager visas.')"><font face="Verdana" size="1">
            I lager</font></a></td>
            <td width="20">
            <input type="checkbox" name="onshelf" onClick="submit()" value="yes" size="20"<?php if ($onshelf == "yes" ) echo " checked";?>>
            </td>

		<?php } ?>

            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="100">
			<select name="showmoms" size="1" onchange="this.form.submit(this.options[this.selectedIndex].value)" style="font-family: Verdana; font-size: 8pt; <?php If ($_SESSION['RememberMoms'] == 1) { ?> color: #D90000"<?php } else { ?>"<?php } ?>">
			<option value="0"<?php if (($_SESSION['RememberMoms'] == "" && $showmoms == 0 ) || $showmoms == "" ) echo " selected";?>>Pris med moms</option>
			<option value="1"<?php if ($_SESSION['RememberMoms'] == 1 ) echo " selected";?>>Pris utan moms</option>
			</select></td>

            <td width="15">
			<font size="1">&nbsp;</font></td>

            <td align="right">
	    
	    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
              <tr>
                <td>
		<a onMouseOver="return escape('<b>Finns i lager</b><br>Varan finns i vårt lager för omgående leverans.')">
                <img border="0" src="pic/01.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Finns i lager</b><br>Varan finns i vårt lager för omgående leverans.')">
                <font size="1" face="Verdana">Finns i lager&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('<b>Varan beställd</b><br>Varan är tillfälligt slut, är beställd från leverantör.')">
                <img border="0" src="pic/06.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Varan beställd</b><br>Varan är tillfälligt slut, är beställd från leverantör.')">
                <font size="1" face="Verdana">Varan beställd&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('<b>Beställningsvara</b><br>Varan tas hem på beställning.')">
                <img border="0" src="pic/09.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Beställningsvara</b><br>Varan tas hem på beställning.')">
                <font size="1" face="Verdana">Beställningsvara</font></a></td>
              </tr>
            </table>
	    
	    </td>
          </tr>
        </table>            
        
            </td>
          </tr>
        </table></center>
        </div>