	    <%	If ($_SESSION['RememberMoms'] == 1 && $showmoms == "" ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}
	    	elseif ($_SESSION['RememberMoms'] == 1 && $showmoms == 0 ) {
	    	$_SESSION['RememberMoms'] = "";
	    	}
	    	elseif ($showmoms == 1 ) {
	    	$_SESSION['RememberMoms'] = 1;
	    	}
	    %>
	    <div align="center">
          <center>	    <table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="93%">
          <tr>
            <td width="100%">
            
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
          <tr>
            <td width="50"><font size="1" face="Verdana"><a onMouseOver="return escape('<b>Hintalista</b><br>Hintalista ilman kuvia.')"><font size="1" face="Verdana">Hintalista</font></a></font></td>
            <td width="20">
            <input type="radio" value="" name="prislistan" onClick="submit()"<% if ($prislistan == "") echo " checked";%>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="50"><font size="1" face="Verdana"><a onMouseOver="return escape('<b>Kuvagalleria</b><br>Kuvallinen hintalista.')">Kuvagalleria</font></a></font></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="pricelist" name="prislistan" onClick="submit()"<% if ($prislistan == "pricelist") echo " checked";%>></font></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="100">
			<select name="showmoms" size="1" onchange="this.form.submit(this.options[this.selectedIndex].value)" style="font-family: Verdana; font-size: 8pt; <% If ($_SESSION['RememberMoms'] == 1) { %> color: #D90000"<% } else { %>"<% } %>">
			<option value="0"<% if (($_SESSION['RememberMoms'] == "" && $showmoms == 0 ) || $showmoms == "" ) echo " selected";%>>Hinta alv 23%</option>
			<option value="1"<% if ($_SESSION['RememberMoms'] == 1 ) echo " selected";%>>Hinta alv 0%</option>
			</select></td>

            <td width="15">
			<font size="1">&nbsp;</font></td>

            <td align="right">
			&nbsp;</td>
          </tr>
        </table>            
        
            </td>
          </tr>
        </table></center>
        </div>