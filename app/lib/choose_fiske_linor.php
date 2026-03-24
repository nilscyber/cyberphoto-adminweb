<div align="center">
  <center>
  <table border="0" cellpadding="5" cellspacing="0" width="93%" style="border: 1px solid #E2E2E2">
    <tr>
      <td align="left" width="150"><b>Välj storlek på linan:</b></td>
      <td align="left">
        <select name="linstorlek" onchange="this.form.submit(this.options[this.selectedIndex].value)" style="background-color: #FFFF99">
  	<option value=""><?php if (!$sv): ?>Kaikki<?php else: ?>Alla<?php endif; ?></option>
  	<option value="10mm"<?php if ($linstorlek == "10mm") echo " selected";?>>0,10mm</option>
  	<option value="12mm"<?php if ($linstorlek == "12mm") echo " selected";?>>0,12mm</option>
  	<option value="13mm"<?php if ($linstorlek == "13mm") echo " selected";?>>0,13mm</option>
  	<option value="14mm"<?php if ($linstorlek == "14mm") echo " selected";?>>0,14mm</option>
  	<option value="16mm"<?php if ($linstorlek == "16mm") echo " selected";?>>0,16mm</option>
  	<option value="18mm"<?php if ($linstorlek == "18mm") echo " selected";?>>0,18mm</option>
  	<option value="19mm"<?php if ($linstorlek == "19mm") echo " selected";?>>0,19mm</option>
  	<option value="20mm"<?php if ($linstorlek == "20mm") echo " selected";?>>0,20mm</option>
  	<option value="23mm"<?php if ($linstorlek == "23mm") echo " selected";?>>0,23mm</option>
  	<option value="28mm"<?php if ($linstorlek == "28mm") echo " selected";?>>0,28mm</option>
  	<option value="30mm"<?php if ($linstorlek == "30mm") echo " selected";?>>0,30mm</option>
  	<option value="32mm"<?php if ($linstorlek == "32mm") echo " selected";?>>0,32mm</option>
  	<option value="34mm"<?php if ($linstorlek == "34mm") echo " selected";?>>0,34mm</option>
  	<option value="36mm"<?php if ($linstorlek == "36mm") echo " selected";?>>0,36mm</option>
  	<option value="41mm"<?php if ($linstorlek == "41mm") echo " selected";?>>0,41mm</option>
  	</select>
      </td>
    </tr>
  </table>
  </center>
</div>