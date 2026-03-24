<table border="0" cellpadding="2" cellspacing="1" width="93%">
  <tr>
    <td><font face="Verdana" size="1">
    <% if (!$sv && $fi) { %>
    Etsintäsi tuotti <b><?php echo count($articles); ?></b> osumaa
    <% } else { %>
    Antal produkter <b><?php echo count($articles); ?></b> st
    <% } %>    
    </font></td>
  </tr>
</table>
