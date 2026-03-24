	    <% If ($_SESSION['RememberPageView'] == 1 && $prislistan == "" ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    	elseif ($_SESSION['RememberPageView'] == 1 && $prislistan == 0 ) {
	    	$_SESSION['RememberPageView'] = "";
	    	}
	    	elseif ($prislistan == 1 ) {
	    	$_SESSION['RememberPageView'] = 1;
	    	}
	    %>

	<div align="center"><center>
       	<table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="85%">
          <tr>
            <td width="100%">
            
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
          <tr>
            <td width="50"><a onMouseOver="return escape('Välj prislista för att se produkterna utan bilder.')"><font size="1" face="Verdana">Prislista</font></a></td>
            <td width="20">
            <input type="radio" value="0" name="prislistan" onClick="submit()"<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) echo " checked";%>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="50"><font size="1" face="Verdana"><a onMouseOver="return escape('Välj bildgalleri för att snabbt få en överblick med bilder.')">Bildgalleri</font></a></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="1" name="prislistan" onClick="submit()"<% if ($_SESSION['RememberPageView'] == 1 ) echo " checked";%>></font></td>

		<%
		
		$ip_address = $REMOTE_ADDR;
		
		if ((eregi("81.8.240.", $ip_address)) || ($ip_address == "81.8.144.102") || ($ip_address == "213.79.137.61") || ($ip_address == "81.8.223.103") || (eregi("192.168.1.", $ip_address)))
		
		{ %>

            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="80"><a onMouseOver="return escape('Filtrerar listan så att endast produkter i vårt lager visas.')"><font face="Verdana" size="1">Endast i lager</font></a></td>
            <td width="20">
            <input type="checkbox" name="onshelf" onClick="submit()" value="yes" size="20"<% if ($onshelf == "yes" ) echo " checked";%>>
            </td>
		<% } %>
            <td align="right">
	    
	    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
              <tr>
                <td>
				<a onMouseOver="return escape('Varan finns i vårt lager för omgående leverans.')">
                <img border="0" src="pic/01.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('Varan finns i vårt lager för omgående leverans.')">
                <font size="1" face="Verdana">Finns i lager&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('Varan är tillfälligt slut, är beställd från leverantör.')">
                <img border="0" src="pic/06.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('Varan är tillfälligt slut, är beställd från leverantör.')">
                <font size="1" face="Verdana">Varan beställd&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('Varan tas hem på beställning.')">
                <img border="0" src="pic/09.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('Varan tas hem på beställning.')">
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