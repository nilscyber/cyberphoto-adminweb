       <table border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="2" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	  </tr>
	  <tr>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="2" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	  </tr>

	  <tr>
	    <% if ($ccd != "") { %>
	    <td width="40" height="30" align="center" background="picinfo/frame.jpg"><b><font face="Verdana" size="1" color="#333333"><a onMouseOver="return escape('<% if ($fi && !$sv) { include ("explanation/tek/upplosning_fi.php"); } else { include ("explanation/tek/upplosning.php"); } %>')"><% echo number_format($ccd / 1000000, 1). "<br>Mpix"; %></a></font></b></td>
	    <td width="2" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <% } else {%>
	    <td width="40" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="2" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <% } %>
	    <% if ($motljsk == "") { %>
	    	<% if ($zoom_digikam != "" && $kategori_id != 395) { %>
			<td width="40" height="30" align="center" background="picinfo/frame.jpg"><b><font face="Verdana" size="1" color="#333333"><a onMouseOver="return escape('<% if ($fi && !$sv) { include ("explanation/tek/zoom_fi.php"); } else { include ("explanation/tek/zoom.php"); } %>')"><% echo number_format($zoom_digikam, 0). "x<br>zoom"; %></a></font></b></td>
		    	<% } else {%>
		    	<td width="40" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
		    	<% } %>
	    <% } else {%>
	    <td width="40" height="30" align="center" background="picinfo/frame.jpg"><b><font face="Verdana" size="1" color="#333333"><a onMouseOver="return escape('<% if ($fi && !$sv) { include ("explanation/tek/lcd_fi.php"); } else { include ("explanation/tek/lcd.php"); } %>')"><% echo $motljsk. "''<br>LCD"; %></a></font></b></td>
	    <% } %>
	  </tr>
	  <tr>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="2" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="40" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	  </tr>
	  <tr>
	    <td width="40" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <td width="2" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    <% if ($motljsk != "") { %>
	    	<% if ($zoom_digikam != "" && $kategori_id != 395) { %>
	    	<td width="40" height="30" align="center" background="picinfo/frame.jpg"><b><font face="Verdana" size="1" color="#333333"><a onMouseOver="return escape('<% if ($fi && !$sv) { include ("explanation/tek/zoom_fi.php"); } else { include ("explanation/tek/zoom.php"); } %>')"><% echo number_format($zoom_digikam, 0). "x<br>zoom"; %></a></font></b></td>
	    	<% } else {%>
	    	<td width="40" height="30"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
	    	<% } %>
	    <% } %>
	  </tr>
	</table>