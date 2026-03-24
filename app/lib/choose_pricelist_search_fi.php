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
        <div align="center"><center>
	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="93%">
          <tr>
            <td width="100">
			<select name="showmoms" size="1" onchange="this.form.submit(this.options[this.selectedIndex].value)" style="font-family: Verdana; font-size: 8pt; <% If ($_SESSION['RememberMoms'] == 1) { %> color: #D90000"<% } else { %>"<% } %>">
			<option value="0"<% if (($_SESSION['RememberMoms'] == "" && $showmoms == 0 ) || $showmoms == "" ) echo " selected";%>>Hinta alv 23%</option>
			<option value="1"<% if ($_SESSION['RememberMoms'] == 1 ) echo " selected";%>>Hinta alv 0%</option>
			</select>
	     </td>

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
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
          <tr>
            <td height="1"><img src="10.gif" width="1" height="1"></td>
          </tr>
        </table>
	</div></center>
