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

	    <div align="center">
          <center>	    <table border="0" cellpadding="5" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="85%">
          <tr>
            <td width="100%">
            
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%">
          <tr>
            <td width="50"><font size="1" face="Verdana">Prislista</font></td>
            <td width="20">
            <input type="radio" value="0" name="prislistan" onClick="submit()"<% if (($_SESSION['RememberPageView'] == "" && $prislistan == 0 ) || $prislistan == "" ) echo " checked";%>></td>
            <td width="25"><font size="1">&nbsp;&nbsp;&nbsp;|</font></td>
            <td width="50"><font size="1" face="Verdana">Bildgalleri</font></td>
            <td width="20">
            <font size="1" color="#000000" face="Verdana">
            <input type="radio" option value="1" name="prislistan" onClick="submit()"<% if ($_SESSION['RememberPageView'] == 1 ) echo " checked";%>></font></td>
            <td align="right">
			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
              <tr>
                <td>
                <img border="0" src="ralph/kopknappar/gron_1.gif" width="32" height="11"></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td><font size="1" face="Verdana">Finns i lager&nbsp;&nbsp;</font></td>
                <td>
                <img border="0" src="ralph/kopknappar/orange_1.gif" width="32" height="11"></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td><font size="1" face="Verdana">Varan beställd&nbsp;&nbsp;</font></td>
                <td>
                <img border="0" src="ralph/kopknappar/bla_1.gif" width="32" height="11"></td>
                <td><font face="Verdana" size="1">&nbsp;</font></td>
                <td><font size="1" face="Verdana">Beställningsvara</font></td>
              </tr>
            </table>
			</td>
          </tr>
        </table>            
        
            </td>
          </tr>
        </table></center>
        </div>