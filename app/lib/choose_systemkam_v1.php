<div id="filtercontainer">
<div class="roundtop">
<div class="infor1"></div>
<div class="infor2"></div>
<div class="infor3"></div>
<div class="infor4"></div>
</div>

<div class="content">

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<?php if ($filter1 == "yes") echo " checked";?><?php if ($filter2 == "yes") echo " disabled";?>></font></td>
      <td align="left">
      <?php if ($sv) { ?>
      <a onMouseOver="return escape('<?php include ("explanation/digikam/optstab.php"); ?>')">
      <?php } else { ?>
      <a onMouseOver="return escape('<?php include ("explanation/digikam/optstab_fi.php"); ?>')">
      <?php } ?>
      <font face="Verdana" size="1"><?php if ($filter1 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Endast kamerahus<?php else: ?>Optinen vakaaja<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter2" value="yes" onClick="submit()"<?php if ($filter2 == "yes") echo " checked";?><?php if ($filter1 == "yes") echo " disabled";?>></font></td>
      <td align="left">
      <?php if ($sv) { ?>
      <a onMouseOver="return escape('<?php include ("explanation/digikam/zoom.php"); ?>')">
      <?php } else { ?>
      <a onMouseOver="return escape('<?php include ("explanation/digikam/zoom_fi.php"); ?>')">
      <?php } ?>
      <font face="Verdana" size="1"><?php if ($filter2 == "yes") { ?><b><font color="#85000D"><?php } ?><?php if ($sv): ?>Endast med kitobjektiv<?php else: ?>Yli 10x zoom<?php endif; ?></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter3" value="yes" onClick="submit()"<% if ($filter3 == "yes") echo " checked";%>></font></td>
      <td align="left">
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/aa.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/aa_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter3 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Endast med superzoom<% else: %>AA paristot<% endif; %></font></b></td>
    </tr>
    <tr>
      <td colspan="2" align="left">
	<select name="varianter" onchange="this.form.submit(this.options[this.selectedIndex].value)" size="1" <?php if ($varianter > "0"): echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 10px"'; else: echo 'style="color: #000000; font-weight: normal; font-family: Verdana; font-size: 10px"'; endif;?>>
		<option value=""><?php if (!$sv): ?>Kaikki valmistajat<?php else: ?>Visa alla kombinationer<?php endif; ?></option>
		<option value="3"<?php if ($varianter == "3") echo " selected";?>>Endast kamerahus</option>
		<option value="7"<?php if ($varianter == "7") echo " selected";?>>Kamera med kitobjektiv</option>
		<option value="9"<?php if ($varianter == "9") echo " selected";?>>Kamera med resezoom</option>
	</select>
      </td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter7" value="yes" onClick="submit()"<% if ($filter7 == "yes") echo " checked";%>></font></td>
      <td align="left">
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vidvinkel.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vidvinkel_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter7 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Fullformat<% else: %>Fullformat<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<% if ($filter1 == "yes") echo " checked";%>></font></td>
      <td align="left">
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter1 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Vatten/stöt -skyddad<% else: %>Vesi/isku -suojattu<% endif; %></font></b></td>
    </tr>
    <tr>
      <td colspan="2">
          <select size="1" name="prisMin" onchange="this.form.submit(this.options[this.selectedIndex].value)" <% if ($prisMin > "0") echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 8pt"';%>>
          <option value="">Minsta pris</option>
          <% if ($fi) { %>
        <option <% if ($prisMin == "100") echo " selected";%>>100</option>
        <option <% if ($prisMin == "150") echo " selected";%>>150</option>
        <option <% if ($prisMin == "200") echo " selected";%>>200</option>
        <option <% if ($prisMin == "250") echo " selected";%>>250</option>
        <option <% if ($prisMin == "300") echo " selected";%>>300</option>
        <option <% if ($prisMin == "350") echo " selected";%>>350</option>
        <option <% if ($prisMin == "400") echo " selected";%>>400</option>
        <option <% if ($prisMin == "500") echo " selected";%>>500</option>
        <option <% if ($prisMin == "600") echo " selected";%>>600</option>
        <option <% if ($prisMin == "800") echo " selected";%>>800</option>
        <option <% if ($prisMin == "1000") echo " selected";%>>1000</option>
        <option <% if ($prisMin == "1200") echo " selected";%>>1200</option>
        <option <% if ($prisMin == "1500") echo " selected";%>>1500</option>
        <option <% if ($prisMin == "2000") echo " selected";%>>2000</option>
        <option <% if ($prisMin == "5000") echo " selected";%>>5000</option>
        <option <% if ($prisMin == "10000") echo " selected";%>>10000</option>
        <% } else { %>
          <option <% if ($prisMin == "500") echo " selected";%>>500</option>
          <option <% if ($prisMin == "1000") echo " selected";%>>1000</option>
          <option <% if ($prisMin == "1500") echo " selected";%>>1500</option>
          <option <% if ($prisMin == "2000") echo " selected";%>>2000</option>
          <option <% if ($prisMin == "2500") echo " selected";%>>2500</option>
          <option <% if ($prisMin == "3000") echo " selected";%>>3000</option>
          <option <% if ($prisMin == "3500") echo " selected";%>>3500</option>
          <option <% if ($prisMin == "4000") echo " selected";%>>4000</option>
          <option <% if ($prisMin == "5000") echo " selected";%>>5000</option>
          <option <% if ($prisMin == "6000") echo " selected";%>>6000</option>
          <option <% if ($prisMin == "7000") echo " selected";%>>7000</option>
          <option <% if ($prisMin == "8000") echo " selected";%>>8000</option>
          <option <% if ($prisMin == "9000") echo " selected";%>>9000</option>
          <option <% if ($prisMin == "10000") echo " selected";%>>10000</option>
          <option <% if ($prisMin == "15000") echo " selected";%>>15000</option>
          <option <% if ($prisMin == "20000") echo " selected";%>>20000</option>
        <% } %>
          </select>
       </td>
       <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<% if ($filter1 == "yes") echo " checked";%>></font></td>
      <td align="left">
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter1 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Vatten/stöt -skyddad<% else: %>Vesi/isku -suojattu<% endif; %></font></b></td>
      <td colspan="2" align="left">
	<select name="marke" onchange="this.form.submit(this.options[this.selectedIndex].value)" size="1" <?php if ($marke > "0"): echo 'style="color: #85000D; font-weight: bold; font-family: Verdana; font-size: 10px"'; else: echo 'style="color: #000000; font-weight: normal; font-family: Verdana; font-size: 10px"'; endif;?>>
		<option value=""><?php if (!$sv): ?>Kaikki valmistajat<?php else: ?>Visa alla tillverkare<?php endif; ?></option>
		<option value="3"<?php if ($marke == "3") echo " selected";?>>Canon</option>
		<option value="7"<?php if ($marke == "7") echo " selected";?>>Nikon</option>
		<option value="9"<?php if ($marke == "9") echo " selected";?>>Olympus</option>
		<option value="24"<?php if ($marke == "24") echo " selected";?>>Panasonic</option>
		<option value="8"<?php if ($marke == "8") echo " selected";?>>Pentax</option>
		<option value="13"<?php if ($marke == "13") echo " selected";?>>Sony</option>
	</select>
      </td>
    </tr>
    </table>
    
</div>

<div class="roundbottom">
<div class="infor4"></div>
<div class="infor3"></div>
<div class="infor2"></div>
<div class="infor1"></div>
</div>
</div>
    