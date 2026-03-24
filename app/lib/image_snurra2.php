			<table border="0" cellpadding="0" cellspacing="1">
			  <tr>
			    <td width="32" height="2"><img border="0" src="picinfo/twopix.jpg" width="2" height="2"></td>
			  </tr>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild; %>', 0, false); return false;">
					   <img src="<% echo "/thumbs/small/bilder/".$bild; %>" style="cursor:hand">
					   </a>
			   </td>
			 </tr>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild2; %>', 1, true); return false;">
					   <img src="<% echo "/thumbs2/small/bilder/".$bild2; %>" style="cursor:hand">
				    </a>
			   </td>
			 </tr>
			 <% if ($bild3 != "" && $bild2 != "") { %>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild3; %>', 2, true); return false;">
					   <img src="<% echo "/thumbs3/small/bilder/".$bild3; %>" style="cursor:hand">
				    </a>
			   </td>
			 </tr>
			 <% } %>
			 <% if ($bild4 != "" && $bild3 != "") { %>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild4; %>', 3, true); return false;">
					   <img src="<% echo "/thumbs4/small/bilder/".$bild4; %>" style="cursor:hand">
				    </a>
			   </td>
			 </tr>
			 <% } %>
			 <% if ($bild5 != "" && $bild4 != "") { %>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild5; %>', 3, true); return false;">
					   <img src="<% echo "/thumbs5/small/bilder/".$bild5; %>" style="cursor:hand">
				    </a>
			   </td>
			 </tr>
			 <% } %>
			 <% if ($bild6 != "" && $bild5 != "") { %>
			 <tr>
			   <td width="32" height="32" align="center" style="border:1px solid #C0C0C0">
				    <a onMouseOver="update('<% echo "/bilder/".$bild6; %>', 3, true); return false;">
					   <img src="<% echo "/thumbs6/small/bilder/".$bild6; %>" style="cursor:hand">
				    </a>
			   </td>
			</tr>
			 <% } %>
		      </table>
