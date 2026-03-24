	    <% If ($_SESSION['RememberPageView'] == 1 && $prislistan == "" ) {
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
	    %>

	<div align="center"><center>
       	<table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="93%">
          <tr>
            <td width="100%">
            
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
          <tr>
            <td width="50"><a onMouseOver="return escape('<b>Hintalista</b><br>Hintalista ilman kuvia.')"><font size="1" face="Verdana">Hintalista</font></a></td>
            <td width="20">
            <input type="radio" value="0" name="prislistan" onClick="submit()"<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) echo " checked";%>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="50"><font size="1" face="Verdana"><a onMouseOver="return escape('<b>Kuvagalleria</b><br>Kuvallinen hintalista.')">Kuvagalleria</font></a></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="1" name="prislistan" onClick="submit()"<% if ($_SESSION['RememberPageView'] == 1 ) echo " checked";%>></font></td>

		<%
		
		$ip_address = $REMOTE_ADDR;
		
		if ((eregi("81.8.240.", $ip_address)) || ($ip_address == "81.8.144.102") || ($ip_address == "213.79.137.61") || ($ip_address == "81.8.223.103") || (eregi("192.168.1.", $ip_address)))
		
		{ %>
		
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="80"><a onMouseOver="return escape('<b>Endast i lager</b><br>Filtrerar listan så att endast produkter som finns på hyllan i vårt lager visas.')"><font face="Verdana" size="1">Endast i lager</font></a></td>
            <td width="20">
            <input type="checkbox" name="onshelf" onClick="submit()" value="yes" size="20"<% if ($onshelf == "yes" ) echo " checked";%>>
            </td>
		<% } %>

            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="100">
			<select name="showmoms" size="1" onchange="this.form.submit(this.options[this.selectedIndex].value)" style="font-family: Verdana; font-size: 8pt; <% If ($_SESSION['RememberMoms'] == 1) { %> color: #D90000"<% } else { %>"<% } %>">
			<option value="0"<% if (($_SESSION['RememberMoms'] == "" && $showmoms == 0 ) || $showmoms == "" ) echo " selected";%>>Hinta alv 24%</option>
			<option value="1"<% if ($_SESSION['RememberMoms'] == 1 ) echo " selected";%>>Hinta alv 0%</option>
			</select></td>

            <td width="15">
			<font size="1">&nbsp;</font></td>

            <td align="right">
	    
	    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
              <tr>
                <td>
		<a onMouseOver="return escape('<b>Varastossa</b><br>Tuote löytyy varastosta.')">
                <img border="0" src="pic/01_fi.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Varastossa</b><br>Tuote löytyy varastosta.')">
                <font size="1" face="Verdana">Varastossa&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('<b>Tuote tilauksessa</b><br>Tuote tilapäisesti loppu, tilattu toimittajalta.')">
                <img border="0" src="pic/06_fi.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Tuote tilauksessa</b><br>Tuote tilapäisesti loppu, tilattu toimittajalta.')">
                <font size="1" face="Verdana">Tulossa&nbsp;&nbsp;</font></a></td>
                <td>
                <a onMouseOver="return escape('<b>Tilaustuote</b><br>Tilaustuote.')">
                <img border="0" src="pic/09_fi.gif" width="32" height="11"></a></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td>
                <a onMouseOver="return escape('<b>Tilaustuote</b><br>Tilaustuote.')">
                <font size="1" face="Verdana">Tilaustuote</font></a></td>
              </tr>
            </table>
	    
	    </td>
          </tr>
        </table>            
        
            </td>
          </tr>
        </table></center>
        </div>