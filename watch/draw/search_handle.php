<?php

/*
 * @Author: moxuan
 * @Date: 2019-03-05 13:39:11
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-12 15:17:26
 */

session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}

require_once "../../Include/db_connect.php";

$action = $_GET['action'];
switch ($action) {
	case 'project_list':
		# code...
		$msg = projectList($db);
		break;

	case 'stage_list':
		# code...
		$project_id = $_GET['project_id'];
		$msg = stageList($db, $project_id);
		break;
	
	default:
		# code...
		break;
}

echo json_encode($msg);

function projectList($db)
{
	# code...
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

	$sql = "select pp.project_id,pe.project from product_project as pp left join project as pe on 
	        pp.project_id=pe.id where pp.product_id='$get_product_id' and pe.`enable`='1' and pp.`enable`='1'";
	$sql .= $where;
	// var_dump("$sql");
	$project = $db->query($sql);

	$projectid = $project[0]['project_id'];
	$build_sql = "select a.`build_id`,b.`build` from `build_project` as a left join `build` as b on a.`build_id`=b.`id`  where a.`project_id` = '".$projectid."' and a.`enable`='1'";
	$build = $db->query($build_sql);

	$msg['project'] = $project;
	$msg['build'] = $build;
	return $msg;
}

function stageList($db, $project_id)
{
	$build_sql = "select a.`build_id`,b.`build` from `build_project` as a left join `build` as b on a.`build_id`=b.`id`  where a.`project_id` = '".$project_id."' and a.`enable`='1'";
	$build = $db->query($build_sql);
	return $build;
}

?>