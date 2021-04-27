<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th width="72%" bgcolor="#000000"><font color="#ffffff">Test Station</font></th>
		<th width="28%" bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
<?php
/////////////////////////// Test Station
foreach($station_name as $key=>$value){
?>	
	<tr>
		<td bgcolor="#ffffff" style="word-break: break-all;"><?=$station_name[$key]?></td>
		<td bgcolor="#ffffff"><?=$station_num[$key]?></td>
	</tr>
<?php
}
?>	
	</table>
	</td>
	<td valign="top">
	<?php
	
	switch($sel_meth)
	{
		case 0:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgcolumn.php?divid=1'></iframe>";
		break;
		
		case 1:
		//echo "<img src='imgcolumn3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgcolumn3D.php?divid=1'></iframe>";
		break;
		
		case 2:
		//echo "<img src='imgline.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgline.php?divid=1'></iframe>";
		break;		
		
		case 3:
		//echo "<img src='imgarea.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgarea.php?divid=1'></iframe>";
		break;
		
		case 4:
		//echo "<img src='imgbar.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgbar.php?divid=1'></iframe>";
		break;	
		
		case 5:
		//echo "<img src='imgbar3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgbar3D.php?divid=1'></iframe>";
		break;	
		
		case 6:
		//echo "<img src='imgpie.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgpie.php?divid=1'></iframe>";
		break;		
		
		case 7:
		//echo "<img src='imgpie3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgpie3D.php?divid=1'></iframe>";
		break;	
		
		case 8:
		//echo "<img src='imgpie_explode.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgpie_explode.php?divid=1'></iframe>";
		break;	
		
		default:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='imgcolumn.php?divid=1'></iframe>";
		break;				
	}
	?>
	</td>	
</tr>	

	
<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
		<tr height="35">
			<th width="72%" bgcolor="#000000"><font color="#ffffff">Config</font></th>
			<th width="28%" bgcolor="#000000"><font color="#ffffff">Count</font></th>
		</tr>
		<?php
		/////////////////////////// Config
		
		foreach($Config_name as $key=>$value)
		{
		?>	
			<tr>
				<td bgcolor="#ffffff" style="word-break: break-all;"><?=$Config_name[$key]?></td>
				<td bgcolor="#ffffff"><?=$Config_num[$key]?></td>
			</tr>
		<?php
		}
		/////////////////////////// Config
		?>
	</table>	
	</td>
	<td valign="top">
	<?php
	switch($sel_meth)
	{
		case 0:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgcolumn.php?divid=3'></iframe>";
		break;
		
		case 1:
		//echo "<img src='imgcolumn3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgcolumn3D.php?divid=3'></iframe>";
		break;
		
		case 2:
		//echo "<img src='imgline.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgline.php?divid=3'></iframe>";
		break;		
		
		case 3:
		//echo "<img src='imgarea.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgarea.php?divid=3'></iframe>";
		break;
		
		case 4:
		//echo "<img src='imgbar.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgbar.php?divid=3'></iframe>";
		break;	
		
		case 5:
		//echo "<img src='imgbar3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgbar3D.php?divid=3'></iframe>";
		break;	
		
		case 6:
		//echo "<img src='imgpie.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgpie.php?divid=3'></iframe>";
		break;		
		
		case 7:
		//echo "<img src='imgpie3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgpie3D.php?divid=3'></iframe>";
		break;	
		
		case 8:
		//echo "<img src='imgpie_explode.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgpie_explode.php?divid=3'></iframe>";
		break;	
		
		default:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show3' scrolling='yes' src='imgcolumn.php?divid=3'></iframe>";
		break;				
	}
	?>		
	</td>	
</tr>	

<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th bgcolor="#000000"><font color="#ffffff">Failure Symptom</font></th>
		<th bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
<?php


/////////////////////////// Failure Symptom
foreach($symp_name as $key=>$value){
?>	
	<tr>
		<td bgcolor="#ffffff" style="word-break: break-all;"><?=$symp_name[$key]?></td>
		<td bgcolor="#ffffff"><?=$symp_num[$key]?></td>
	</tr>
<?php
}/////////////////////////// Failure Symptom
?>	
	</table>	
	</td>
	<td valign="top">
	<?php
	
	switch($sel_meth)
	{
		case 0:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgcolumn.php?divid=4'></iframe>";
		break;
		
		case 1:
		//echo "<img src='imgcolumn3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgcolumn3D.php?divid=4'></iframe>";
		break;
		
		case 2:
		//echo "<img src='imgline.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgline.php?divid=4'></iframe>";
		break;		
		
		case 3:
		//echo "<img src='imgarea.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgarea.php?divid=4'></iframe>";
		break;
		
		case 4:
		//echo "<img src='imgbar.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgbar.php?divid=4'></iframe>";
		break;	
		
		case 5:
		//echo "<img src='imgbar3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgbar3D.php?divid=4'></iframe>";
		break;	
		
		case 6:
		//echo "<img src='imgpie.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgpie.php?divid=4'></iframe>";
		break;		
		
		case 7:
		//echo "<img src='imgpie3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgpie3D.php?divid=4'></iframe>";
		break;	
		
		case 8:
		//echo "<img src='imgpie_explode.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgpie_explode.php?divid=4'></iframe>";
		break;	
		
		default:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='imgcolumn.php?divid=4'></iframe>";
		break;				
	}
	?>		
	</td>	
</tr>
<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th width="72%" bgcolor="#000000"><font color="#ffffff">Root Cause</font></th>
		<th width="28%" bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
	<?php
	/////////////////////////// Root Cause
	foreach($cause_name as $key=>$value){
	?>	
		<tr>
			<td bgcolor="#ffffff" style="word-break: break-all;"><?=$cause_name[$key]?></td>
			<td bgcolor="#ffffff"><?=$cause_num[$key]?></td>
		</tr>
	<?php
	}/////////////////////////// Root Cause
	?>	
	</table>	
	</td>
	<td valign="top">
	<?php
	
	switch($sel_meth)
	{
		case 0:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgcolumn.php?divid=2'</iframe>";
		break;
		
		case 1:
		//echo "<img src='imgcolumn3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgcolumn3D.php?divid=2'></iframe>";
		break;
		
		case 2:
		//echo "<img src='imgline.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgline.php?divid=2'></iframe>";
		break;		
		
		case 3:
		//echo "<img src='imgarea.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgarea.php?divid=2'></iframe>";
		break;
		
		case 4:
		//echo "<img src='imgbar.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgbar.php?divid=2'></iframe>";
		break;	
		
		case 5:
		//echo "<img src='imgbar3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgbar3D.php?divid=2'></iframe>";
		break;	
		
		case 6:
		//echo "<img src='imgpie.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgpie.php?divid=2'></iframe>";
		break;		
		
		case 7:

		//echo "<img src='imgpie3D.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgpie3D.php?divid=2'></iframe>";
		break;	
		
		case 8:
		//echo "<img src='imgpie_explode.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgpie_explode.php?divid=2'></iframe>";
		break;	
		
		default:
		//echo "<img src='imgcolumn.php'>";
		echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show2' scrolling='yes' src='imgcolumn.php?divid=2'></iframe>";
		break;				
	}
	?>	
	</td>	
</tr>	
</table>