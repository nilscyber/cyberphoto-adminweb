<div align="center">
  <center>
  <table border="0" cellpadding="2" cellspacing="0" width="93%" style="border: 1px solid #85000D; background-color: #F7F7F4">
    <tr>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter1" value="yes" onClick="submit()"<% if ($filter1 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vatten_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter1 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Vatten/stöt -skyddad<% else: %>Vesi/isku -suojattu<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter3" value="yes" onClick="submit()"<% if ($filter3 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/aa.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/aa_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter3 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>AA batterier<% else: %>AA paristot<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter5" value="yes" onClick="submit()"<% if ($filter5 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/optsok.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/optsok_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter5 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Optisk sökare<% else: %>Optinen etsin<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter7" value="yes" onClick="submit()"<% if ($filter7 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vidvinkel.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/vidvinkel_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter7 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Vidvinkel < 30mm<% else: %>Laajakulma z 30mm<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter9" value="yes" onClick="submit()"<% if ($filter9 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/raw.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/raw_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter9 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>RAW-format<% else: %>RAW-muoto<% endif; %></font></b></td>
    </tr>
    <tr>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter2" value="yes" onClick="submit()"<% if ($filter2 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/optstab.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/optstab_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter2 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Optisk bildstabilisering<% else: %>Optinen vakaaja<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter4" value="yes" onClick="submit()"<% if ($filter4 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/blixtsko.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/blixtsko_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter4 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Blixtsko<% else: %>Salamakenkä<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter6" value="yes" onClick="submit()"<% if ($filter6 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/hd.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/hd_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter6 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>HD filmning<% else: %>HD-kuvaus<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter8" value="yes" onClick="submit()"<% if ($filter8 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/manexp.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/manexp_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter8 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Manuell exponering<% else: %>Manuelli valotus<% endif; %></font></b></td>
      <td width="20"><font face="Verdana" size="1"><input type="checkbox" name="filter10" value="yes" onClick="submit()"<% if ($filter10 == "yes") echo " checked";%>></font></td>
      <td>
      <% if ($sv) { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/lcd.php"); %>')">
      <% } else { %>
      <a onMouseOver="return escape('<% include ("explanation/digikam/lcd_fi.php"); %>')">
      <% } %>
      <font face="Verdana" size="1"><% if ($filter10 == "yes") { %><b><font color="#85000D"><% } %><% if ($sv): %>Vikbar LCD-skärm<% else: %>Käännettävä LCD-näyttö<% endif; %></font></b></td>
    </tr>
    </table>
  </center>
</div>