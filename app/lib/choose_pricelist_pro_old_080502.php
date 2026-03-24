	    <% 	if ($_SESSION['RememberPageView'] == 1 && $prislistan == "" ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    	elseif ($_SESSION['RememberPageView'] == 1 && $prislistan == 0 ) {
	    	$_SESSION['RememberPageView'] = "";
	    	}
	    	elseif ($prislistan == 1 ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    	
	    	if ($_SESSION['RememberMoms'] == 1 && $showmoms == "" ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}
	    	elseif ($_SESSION['RememberMoms'] == 1 && $showmoms == 0 ) {
	    	$_SESSION['RememberMoms'] = "";
	    	}
	    	elseif ($showmoms == 1 ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}

	    	if ($PRO != "Yes") {
	    		if ($_SESSION['RememberPicture'] == 1 && $picture == "1" ) {
	    		$_SESSION['RememberPicture'] = 1;
	    		}
	    		elseif ($_SESSION['RememberPicture'] == 1 && $picture == "" ) {
	    		$_SESSION['RememberPicture'] = "";
	    		}
	    		elseif ($picture == 1 ) {
	    		$_SESSION['RememberPicture'] = 1;
	    		}
	    	}
	    %>

	<div align="center"><center>
       	<table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse; background-repeat:no-repeat; background-image:url('pic/choose.jpg')" width="93%" height="60">
          <tr>
            <td valign="top">
            
        <table border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="5">&nbsp;</td>
            <td width="50"><a onMouseOver="return escape('<b>Prislista</b><br>Välj denna för vanlig prislista utan bilder.')"><font size="1" face="Verdana">Prislista</font></a></td>
            <td width="20">
            <input type="radio" value="0" name="prislistan" onClick="submit()"<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) echo " checked";%>></td>
            <td width="50"><font size="1">&nbsp;</font></td>
            <td width="75"><font face="Verdana" size="1">&nbsp;Sortera på:</font></td>
            <td width="65">
            <font face="Verdana" size="1">Tillverkare</font></td>
            <td width="20">
            <input type="radio" value="tillverkare" name="sortera" onClick="submit()"<% if ($sortera == "tillverkare" || $sortera == "") echo " checked";%>></td>
            <td width="60">
            <font face="Verdana" size="1">&nbsp;</font></td>
            <td width="100">
            <p align="center"><font face="Verdana" size="1">Visa prislista:</font></td>
            <td width="105">
			<font size="1" face="Verdana">Priser med moms</font></td>
            <td width="20">
            <input type="radio" value="0" name="showmoms" onClick="submit()"<% if (($_SESSION['RememberMoms'] == "" && $showmoms == 0 ) || $showmoms == "" ) echo " checked";%>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>

            <td width="115"><a onMouseOver="return escape('<b>Endast i lager</b><br>Filtrerar listan så att endast produkter som finns på hyllan i vårt lager visas.')"><font face="Verdana" size="1">
            Endast varor i lager</font></a></td>
            <td width="20">
            <input type="checkbox" name="onshelf" onClick="submit()" value="yes" size="20"<% if ($onshelf == "yes" ) echo " checked";%>>
            </td>
			<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) { %>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>

            <td width="15">
			<font size="1" face="Verdana">Bild</font></td>

            <td width="15">
            <input type="checkbox" name="picture" onClick="submit()" value="1" size="20"<% if ($_SESSION['RememberPicture'] == 1) echo " checked";%>></td>
			<% } %>
          </tr>
          <tr>
            <td width="5">&nbsp;</td>
            <td width="50"><font face="Verdana" size="1">G</font><a onMouseOver="return escape('<b>Bildgalleri</b><br>Välj denna för att se prislistan med bilder för snabbt överblick.')"><font size="1" face="Verdana">alleri</font></a></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="1" name="prislistan" onClick="submit()"<% if ($_SESSION['RememberPageView'] == 1 ) echo " checked";%>></font></td>
            <td width="50"><font size="1">&nbsp;</font></td>
            <td width="75"><font face="Verdana" size="1">&nbsp;</font></td>
            <td width="65"><font face="Verdana" size="1">Pris</font></td>
            <td width="20">
            <input type="radio" value="utpris" name="sortera" onClick="submit()"<% if ($sortera == "utpris") echo " checked";%>></td>
            <td width="60"><font face="Verdana" size="1">&nbsp;</font></td>
            <td width="100"><font face="Verdana" size="1">&nbsp;</font></td>
            <td width="105"><font face="Verdana" size="1">Priser utan moms</font></td>
            <td width="20">
            <input type="radio" value="1" name="showmoms" onClick="submit()"<% if ($_SESSION['RememberMoms'] == 1 ) echo " checked";%>></td>
            <td width="25"><font face="Verdana" size="1">&nbsp;</font></td>

            <td width="115"><font face="Verdana" size="1">&nbsp;</font></td>
            <td width="20">
            <font face="Verdana" size="1">&nbsp;</font></td>
			<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) { %>
            <td width="25"><font face="Verdana" size="1">&nbsp;</font></td>

            <td width="15">
			<font face="Verdana" size="1">&nbsp;</font></td>

            <td width="15">
            <font face="Verdana" size="1">&nbsp;</font></td>
			<% } %>
          </tr>
        </table>            
            </td>
          </tr>
        </table></center>
        </div>