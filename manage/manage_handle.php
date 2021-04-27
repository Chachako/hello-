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

require_once "../Include/db_connect.php";
$action = $_GET['action'];

switch ($action) {
    case 'search_name':
      
        $post=$_POST;
        $msg = searchname($db, $post);
        break;
    case 'add':
        $head = trim($_POST['head']);
        $content = trim($_POST['content']);
        $station_name = trim($_POST['station_name']);
        $product_id=trim($_POST['product_id']);
        $msg = add($db,$head,$content, $station_name, $product_id);
        break;
    case 'search_content':
        $id = trim($_POST['id']);
        $msg = searchcontent($db,$id);
        break; 
    case 'edit':
        $head = trim($_POST['head']);
        $content = trim($_POST['content']);
        $id = trim($_POST['id']);
        $product_id = trim($_POST['product_id']);
        $station_name = trim($_POST['station_name']);
        $msg = edit($db,$head,$content,$id,$product_id, $station_name);
        break;
    case 'delete':
        $id = trim($_POST['id']);
        $msg = delete($db,$id);
        break;
	case 'get_product':
		$msg = getProdute($db);
		break;
	case 'get_group_product':
		$msg = getGroupPproduct($db);
		break;
	case 'edit_group':
		$msg = editGroup($db);
		break;
	case 'user_info':
		$user_id = $_GET['user_id'];
		$msg = userInfo($db, $user_id);
		break;
	case 'login_list':
		$user_id = $_GET['user_id'];
		$page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
     
        $post = array(
            'page' => $page,
            'limit' => $limit,
        	'user_id' => $user_id,
        );
		$msg = loginList($db, $post);
		break;
}

echo json_encode($msg);

function getProdute($db)
{
	$product_sql = "select * from `product` ";
	$product_result = $db->query($product_sql);
	if($product_result){
		for($j = 0; $j < count($product_result); $j++){
	        $msg[] = array(
	            "name" => $product_result[$j]['product'],
	            "value" => $product_result[$j]['id'],
	        );
	    }
	}
	return $msg;
}

function searchcontent($db,$id){
	$sql="select * from `station_introduce` where `id`='$id'  ";
    $sql_result=$db->query($sql);
    // $sql_result[0]['content'] = htmlspecialchars_decode(stripslashes($sql_result[0]['content']));
    // $sql_result[0]['head'] = htmlspecialchars_decode(stripslashes($sql_result[0]['head']));
    return $sql_result;
    
}
function add($db,$head,$content, $station_name, $product_id){
    // $db->beginTransaction();
    // $content = addslashes(htmlspecialchars($content));
    // $head=addslashes(htmlspecialchars($head));
    $insert_sql= "INSERT into `station_introduce`  (`head`,`content`,`station_name`,`product_id`) values ('$head' ,'$content','$station_name','$product_id' ) ";      
    $insert_sql_result=$db->execSql($insert_sql);
   
    if( $insert_sql_result){
        $msg = "success";
    }else{
        $msg = "fail";
    }
    return $msg;

}
function edit($db,$head,$content,$id,$product_id,$station_name){
//    $content = addslashes(htmlspecialchars($content));
//    $head=addslashes(htmlspecialchars($head));
   $sql=" update  station_introduce set `head`='$head',`content`='$content',`product_id`='$product_id' ,`station_name`='$station_name' where `id`='$id'";
   $sql_result=$db->execSql($sql);
   if( $sql_result){
    $msg = "success";
   }else{
    $msg = "fail";
   }
   return $msg;



}

function searchname($db,$post){
  
    $page = trim($post['page']);
    $limit = trim($post['limit']);
    $page = ($page - 1) * $limit;
    $station_name=trim($post['station_name']);
    $product=trim($post['product']);


    $account_select_sql = "select f.* from station_introduce as f ";
    $where= 'where 1=1 ' ;
    if($station_name!=''){
        $where.="and f.station_name like '%$station_name%' ";
    }else{
        $where.=" ";
    }
    $account_select_result = $db->query($account_select_sql);

    if($product!=""){
   
        $product_id_sql="select * from product where product like '%$product%' ";
        $product_id_sql_result = $db->query($product_id_sql);
        if(!$product_id_sql_result){
         $where .= " and f.product_id ='no_product'  ";
        }
 
  
        if($product_id_sql_result){
            if(count($product_id_sql_result)==1){
             $where.=" and FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(f.product_id, '|',','))";
             //    $where .= " and f.product_id  = ".$product_id_sql_result[0][id]."  ";
            }else{
                for($i=0;$i<count($product_id_sql_result);$i++){
                
                 if ($i == 0) {
                     $where .= " and (FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(f.product_id, '|',',')) ";
                 } else if ($i == count($product_id_sql_result) - 1) {
                     $where .= " FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(f.product_id, '|',',')) )";
                 } else {
                     $where .= " OR FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(f.product_id, '|',',')) ";
                 }
 
                } 
            }
         
        }
         
     }
     $account_select_sql.=$where;
     $account_select_sql.="limit $page,$limit  ";
     $account_select_result = $db->query($account_select_sql);
     $account_select_count_select = "SELECT count(1) as num from station_introduce as f $where" ;
     $account_select_count_result = $db->query($account_select_count_select,'Row');
    if ($account_select_result) {
        
        for ($i = 0; $i < count($account_select_result); $i++) {

            $product = $account_select_result[$i]['product_id'];
            
            if (!empty($product)) {
                $product = explode('|', $product);

                for ($j = 0; $j < count($product); $j++) {
                    $product_select_sql = "SELECT `product` FROM `product` WHERE  `id`='" . $product[$j] . "' ";
                    $product_select_result = $db->query($product_select_sql);
                    $temp2[$i][] = $product_select_result[0]['product'];
                    $productIdArr[$i][] = $product[$j];
                }

            }

            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'station_name' => $account_select_result[$i]['station_name'],
                // 'content' => htmlspecialchars_decode(stripslashes($account_select_result[$i]['content'])),
                'content' => $account_select_result[$i]['content'],
                'head' => $account_select_result[$i]['head'],
                'sort'=> $account_select_result[$i]['sort'],
                // "product"=>$account_select_result[$i]['product_id'],
                 "product"=> $temp2[$i],
                // 'head' => htmlspecialchars_decode(stripslashes($account_select_result[$i]['head'])),
            );
            
        }
    }
    // $account_select_count_select = "SELECT count(1) as num from station_introduce where station_name like '%$station_name%'";
    // $account_select_count_result = $db->query($account_select_count_select,'Row');



    $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" =>  $array ,
    );
    // $msg = $result_array;
    return $result_array;   
}

function delete($db,$id){
    $sql=" delete from  station_introduce  where `id`='$id'";
    $sql_result=$db->execSql($sql);
    if( $sql_result){
     $msg = "success";
    }else{
     $msg = "fail";
    }
    return $msg;
 
 
 
 }
function getGroupPproduct($db)
{
	$id = $_GET['groupId'];

	$product_sql = "select * from `product` ";
	$product_result = $db->query($product_sql);

	$proup_sql = "select * from `group` where id=".$id;
	$proup_result = $db->query($proup_sql);
	$product_id = $proup_result[0]['product_id'];
	$product_id = explode('|', $product_id);

	if($product_result){
		for($j = 0; $j < count($product_result); $j++){
			$check = false;
			for($i = 0; $i < count($product_id); $i++){
				if($product_id[$i] == $product_result[$j]['id']){
					$check = true;
					break;
				}
			}
	        $msg[] = array(
	            "name" => $product_result[$j]['product'],
	            "value" => $product_result[$j]['id'],
	            "selected" => $check,
	        );
	    }
	}
	return $msg;
}

function editGroup($db)
{
	$id = $_POST['groupId'];
	$group = $_POST['group_edit'];
	$productid = $_POST['select'];
	$productid = str_replace(",","|",$productid);

    $group_edit_sql = "update `group` set `group` = '$group', `product_id`='$productid' where `id`='".$id."'";
    $group_edit_result = $db->execSql($group_edit_sql);

    $msg = "success";

    return $msg;
}

function userInfo($db, $user_id)
{
    $account_select_sql = "select * from `user_list` where `id`='".$user_id."'";
    $account_select_result = $db->query($account_select_sql);

    $product = $account_select_result[0]['product_id'];
    if (!empty($product)) {
        $product = explode('|', $product);

        for ($j = 0; $j < count($product); $j++) {
            $product_select_sql = "SELECT `product` FROM `product` WHERE  `id`='" . $product[$j] . "' ";
            $product_select_result = $db->query($product_select_sql);
            $temp[] = $product_select_result[0]['product'];
        }
        if (is_array($temp)) {
            $temp_product = implode("|", $temp);
        }
    }

    $project = $account_select_result[0]['project_id'];
    if( $project!=""&& $project!=NULL){
        $project = explode('|', $project);

        for ($j = 0; $j < count($project); $j++) {
            $project_select_sql = "SELECT `project` FROM `project` WHERE  `id`='" . $project[$j] . "' ";
            $project_select_result = $db->query($project_select_sql);

            // var_dump($project_select_result);
            //为空时 没值
            $temp2[] = $project_select_result[0]['project'];
        }
        if (is_array($temp2)) {
            $temp_project = implode("|", $temp2);
        }
    }

    $group_select_sql = "SELECT `group` FROM `group` WHERE `id`='" . $account_select_result[0]['group_id'] . "'";
	$group_select_result = $db->query($group_select_sql);

    $array[] = array(
        'id' => $account_select_result[0]['id'],
        'username' => $account_select_result[0]['username'],
        'product' => $temp_product,
        'project' => $temp_project,
        'group' => $group_select_result[0]['group'],
        'email' => $account_select_result[0]['email'],
        'phone' => $account_select_result[0]['phone'],
        'long_phone' => $account_select_result[0]['long_phone'],
        'enable' => $account_select_result[0]['enable'],
        'level' => $account_select_result[0]['level'],
        'group_category' =>$account_select_result[0]['group_category'],
    );

    return $array;
}

function loginList($db, $post)
{
	$user_id = $post['user_id'];
	$page = $post['page'];
    $limit = $post['limit'];
    $page = ($page - 1) * $limit;

    $login_select_sql = "select * from `login_time` where `user_id`='".$user_id."' order by `id` desc limit $page,$limit";
    $login_select_result = $db->query($login_select_sql);

    $login_select_count_sql = "SELECT count(id) as num FROM `login_time`  WHERE `user_id`='".$user_id."'";
    $login_select_count_result = $db->query($login_select_count_sql);

    $result = array(
        "code" => 0,
        "msg" => "",
        "count" => $login_select_count_result[0]['num'],
        "data" => $login_select_result,
    );
    return $result;
}

?>