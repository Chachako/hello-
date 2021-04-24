<?php
require_once "./Include/db_connect.php";

// error_reporting(0);
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:./index.html");
}

$product = explode('|', $_SESSION['cooper_user_info'][0]['product_id']);

$flag_ipad = 0;
$flag_kb = 0;
$flag_watch = 0;
for($i = 0; $i < count($product); $i++){
  if($product[$i] == 14)
    $flag_ipad = 1;
  if($product[$i] == 17)
    $flag_kb = 1;
  if($product[$i] == 20)
    $flag_watch = 1;
}

$sql_ipad = "select c.*,d.project from 
(select a.*,b.product from 
(select * from product_project where product_id = '14' and enable = '1' ) as a 
left join product as b on b.id = a.product_id where b.enable = 1) as c 
left join project as d on c.project_id = d.id where d.enable = 1";

$user_ipad = $db->query($sql_ipad);
$pro_ipad = "";
for($i = 0; $i < count($user_ipad); $i++){
	$pro_ipad .= $user_ipad[$i]['project'];
	if(count($user_ipad) != 1 && $i < count($user_ipad)-1){
		$pro_ipad .= " | ";
	}
}

$sql_kb = "select c.*,d.project from 
(select a.*,b.product from 
(select * from product_project where product_id = '17' and enable = '1' ) as a 
left join product as b on b.id = a.product_id where b.enable = 1) as c 
left join project as d on c.project_id = d.id where d.enable = 1";

$user_kb = $db->query($sql_kb);
$pro_kb = "";
for($i = 0; $i < count($user_kb); $i++){
	$pro_kb .= $user_kb[$i]['project'];
	if(count($user_kb) != 1 && $i < count($user_kb)-1){
		$pro_kb .= " | ";
	}
}

$sql_watch = "select c.*,d.project from 
(select a.*,b.product from 
(select * from product_project where product_id = '20' and enable = '1' ) as a 
left join product as b on b.id = a.product_id where b.enable = 1) as c 
left join project as d on c.project_id = d.id where d.enable = 1";

$user_watch = $db->query($sql_watch);
$pro_watch = "";
for($i = 0; $i < count($user_watch); $i++){
	$pro_watch .= $user_watch[$i]['project'];
	if(count($user_watch) != 1 && $i < count($user_watch)-1){
		$pro_watch .= " | ";
	}
}

$search_data['flag_ipad']=$flag_ipad;       
$search_data['pro_ipad']=$pro_ipad;
$search_data['flag_kb']=$flag_kb;
$search_data['pro_kb']=$pro_kb;
$search_data['flag_watch']=$flag_watch;
$search_data['pro_watch']=$pro_watch;


echo json_encode($search_data);

?>