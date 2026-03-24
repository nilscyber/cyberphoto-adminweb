<?php

function includeCampaign($campaignNr) {

	$select = "SELECT * FROM campaignQuestions WHERE campaignId = " . $campaignNr . " order by ordering";
	$select2 = "SELECT * FROM campaign WHERE id = " . $campaignNr;
	$res = mysqli_query($select);
	$res2 = mysqli_query($select2);
	//echo $select2;
	if (mysqli_num_rows($res) == 0)
		return;
	$row2 = mysqli_fetch_object($res2);
	?>	
	<input type="hidden" name="campaignNo" value="<?php echo $campaignNr ?>">
	  <tr>
	    <td bgcolor="#B90000">&nbsp;</td><td colspan="2" bgcolor="#B90000"><font face="Verdana" size="1" color="#FFFFFF"><b><% echo $row2->name; %></b></font></td>
	  </tr>
	  <?php 
		while ($row = mysqli_fetch_object($res)) {
		
		echo "<tr><td bgcolor=\"#B90000\">&nbsp;</td><td bgcolor=\"#B90000\"><font face=\"Verdana\" size=\"1\" color=\"#FFFFFF\">" . $row->question . "</font></td>";
		if ($row->typeTrueFalse) 
			echo "<td bgcolor=\"#B90000\"><input name=\"campaignRes[" . $row->questionNr . "]\" size=\"20\" type=\"checkbox\" value=yes checked></td></tr>";			
		else
			echo "<td bgcolor=\"#B90000\"><textarea cols=\"23\" rows=\"2\" style=\"font-family: Verdana; font-size: 8pt\" name=\"campaignRes[" . $row->questionNr . "]\" size=\"20\"></textarea></td></tr>";
				
		
		}
		
	  ?>	
	
<?php
}

?>