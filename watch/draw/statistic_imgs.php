<?php
require_once "../../Include/db_connect.php";
session_start();
if(empty($_SESSION['cooper_user_info'])){
if(empty($_COOKIE['cooper_username'])||empty($_COOKIE['cooper_password'])){
header("location:../../index.html");
}else{
$row=check_user($_COOKIE['cooper_username'],$_COOKIE['cooper_password'],$db);
if(empty($row)){
header("location:../../index.html");
}else{
$_SESSION['cooper_user_info']=$row;
}
}
}

function check_user($username, $password, $db)
{
    $sql = "SELECT `id`,`username`,`email`,`phone`,`product_id`,`group_id`,`group_category`,`level` from user_list where username='$username' and password='$password' and enable = 1";
    $userinfo = $db->query($sql);
    return $userinfo;
}
$sel_meth = trim($_GET['sel_meth']);
$search_data = trim($_GET['search_data']);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
function checkform(e){
	var search_data = document.getElementById(e).value;
		if(search_data==''){
			alert("please select Project");
			return false;
		}
}
</script>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
<tr>

	<td colspan="2" >
	<!-- iPATS Chart ( Data source from search result, dynamically generate charts ) -->
	</td>
</tr>

<tr>
	<td align="center" colspan="2">
	<form name="form1" id="form1" method="get" action="" onsubmit="checkform('search_data')">
	Project:
	<select name="search_data" id="search_data">
	<?
	$user_id=$_SESSION['cooper_user_info'][0]['id'];
	$sql="select * from user_list where id='$user_id'";
    $user=$db->query($sql);
    $level=$user[0]['level'];
    $product_id=$user[0]['product_id'];
	$userproject=$user[0]['project_id'];

	$product_sql="select * from `product` where enable='1' and product='Watch'";
	$product_res=$db->query($product_sql);
	$product= $product_res;
	$where="";
	// var_dump($_POST['product_id']) ;
	$get_product_id=$product[0]['id'];
	if($userproject!=''&&$userproject!=NULL){
		$project_id=explode('|', $userproject) ;
		for ($i = 0; $i < count($project_id); $i++) {
			if (count($project_id) > 1) {
				if ($i == 0) {
					$where .= "  and ( pe.`id` = '$project_id[$i]' ";
				} else if ($i == count($project_id) - 1) {
					$where .= " OR pe.`id` = '$project_id[$i]' ) ";
				} else {
					$where .= " OR pe.`id` = '$project_id[$i]' ";
				}
			} else {
				$where .= "and  pe.`id` = '$project_id[$i]' ";
			}
		}
	}

	$sql="select * from product_project as pp left join project as pe on 
            pp.project_id=pe.id where pp.product_id='$get_product_id' and pe.`enable`='1' and pp.`enable`='1'";
    $sql.= $where;
    // var_dump("$sql");
    $project=$db->query($sql);
    for ($i = 0; $i < count($project); $i++) {
    	$mysearchname = $project[$i]['project'];
    	$mysearchid = $project[$i]['project_id'];
		echo "<option value='$mysearchid'";
		if($search_data==$mysearchid){
			echo "selected='selected'";
		}
		echo ">$mysearchname</option>";
    }
	?>
	</select>
	Chart:
		<select name="sel_meth" id="sel_meth">
		<?
		$Chart_array = Array("0"=>"imgcolumn.php","1"=>"imgcolumn3D.php","2"=>"imgline.php","3"=>"imgarea.php","4"=>"imgbar.php","5"=>"imgbar3D.php","6"=>"imgpie.php","7"=>"imgpie3D.php","8"=>"imgpie_explode.php"); 
			foreach($Chart_array as $key=>$value){
				echo "<option value='".$value."' ";
				if($sel_meth==$value){
					echo "selected";
				}
				$value = str_replace("img","",$value);
				$value = str_replace(".php","",$value);
				echo ">".$value."</option>";
			}
		?>
		</select>
		&nbsp;
		<input type="submit" name="submit" id="submit" value="View Chart"> 
	</form>
	</td>
</tr>	
<?
if(trim($_GET['search_data'])!=''&&trim($_GET['search_data'])!=null){
	require_once('Chart_Data.php');
	if(isset($station_name)){
?>
		<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th width="72%" bgcolor="#000000"><font color="#ffffff">Test Station</font></th>
		<th width="28%" bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
	<!--Test Station start-->
<?
foreach($station_name as $key=>$value){
?>	
	<tr>
		<td bgcolor="#ffffff" style="word-break: break-all;"><?=$station_name[$key]?></td>
		<td bgcolor="#ffffff"><?=$station_num[$key]?></td>
	</tr>
<?
}
?>	

	</table>
	</td>
	<td valign="top">
		<?php
	foreach($Chart_array as $key=>$value){
		if($sel_meth==$value){
			echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show1' scrolling='yes' src='".$sel_meth."?divid=1&search_data=".$search_data."'></iframe>";
			break;
		}
	}
	?>
	</td>	
</tr>
<?	}else{
	echo "<script language='javascript'>alert('No Data')</script>";
}
?>
<!--Test Station end-->
<!--Priority start-->
<? if(isset($priority_name)){ ?>
<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th bgcolor="#000000"><font color="#ffffff">Priority</font></th>
		<th bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
<?php
foreach($priority_name as $key=>$value){
?>	
	<tr>
		<td bgcolor="#ffffff" style="word-break: break-all;"><?=$priority_name[$key]?></td>
		<td bgcolor="#ffffff"><?=$priority_num[$key]?></td>
	</tr>
<?php }?>	
	</table>	
	</td>
	<td valign="top">
	<?php
	foreach($Chart_array as $key=>$value){
		if($sel_meth==$value){
			echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='".$sel_meth."?divid=2&search_data=".$search_data."'></iframe>";
			break;
		}
	}?> 
	</td>	
</tr>
<?	}?>
<!--Priority end-->
<!--Requestor start-->
<? if(isset($requestor_name)){ ?>
<tr>
	<td width="20%" valign="top">
	<table width="100%" border="0" bgcolor="#cccccc" align="center" cellpadding="2" cellspacing="2">
	<tr height="35">
		<th bgcolor="#000000"><font color="#ffffff">Requestor</font></th>
		<th bgcolor="#000000"><font color="#ffffff">Count</font></th>
	</tr>
<?php
foreach($requestor_name as $key=>$value){
?>	
	<tr>
		<td bgcolor="#ffffff" style="word-break: break-all;"><?=$requestor_name[$key]?></td>
		<td bgcolor="#ffffff"><?=$requestor_num[$key]?></td>
	</tr>
<?php }?>	
	</table>	
	</td>
	<td valign="top">
	<?php
	foreach($Chart_array as $key=>$value){
		if($sel_meth==$value){
			echo "<iframe align='top' marginwidth='0' width='100%' height='600px' frameborder='0' marginheight='0' marginwidth='0' name='show4' scrolling='yes' src='".$sel_meth."?divid=3&search_data=".$search_data."'></iframe>";
			break;
		}
	}?> 
	</td>	
</tr>
<?	}?>
<!--Requestor end-->
<?	}?>
</table>
