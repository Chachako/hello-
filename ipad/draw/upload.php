<?php
session_start();
$userId = $_SESSION['cooper_user_info'][0]['id'];
$project = explode('|', $_SESSION['cooper_user_info'][0]['project_id']);

require_once "../../Include/db_connect.php";
$action = $_GET['action'];

switch($action){
   
    case 'search':
       
        $msg = search($db,$userId);
        break;
    case 'search_content':
        $id = trim($_POST['id']);
        $msg = searchcontent($db,$id);
        break; 
    case 'select_name':
        $station_name = trim($_POST['station_name']);
        // var_dump($station_name);
		$msg = selectname($db,$station_name);
		break;
    case 'add':

        $head = trim($_POST['head']);
        $content = trim($_POST['content']);
        $station_name = trim($_POST['station_name']);
        var_dump($station_name);
        $msg = add($db, $userId, $head,$content, $station_name);
        break;
    case 'edit':
        $head = trim($_POST['head']);
        $content = trim($_POST['content']);
        $station_name = trim($_POST['station_name']);
        $id = trim($_POST['id']);
        $msg = edit($db,$head,$content,$id,$userId,$station_name);
        break;
   default:
   $msg = "Parameter error";
   break;
}
echo json_encode($msg);
function search($db,$userId){
     $sp_sql="select `station_project_id` from station_user where `user`='$userId' ";
     $sp_sql_result=$db->query($sp_sql);
     if($sp_sql_result){
        for($i = 0; $i < count($sp_sql_result); $i++){

              $id= $sp_sql_result[$i]['station_project_id'];
              $station_project_sql="select `station` from station_project where `id`=' $id' and `enable`='1'";
              $station_project_sql_result=$db->query($station_project_sql); 
           
                $station_id= $station_project_sql_result[0]['station'];
                $station1_select_sql = "SELECT `station` FROM `station` WHERE  `id`='$station_id' ";
                $station1_select_result = $db->query($station1_select_sql);
                $temp[$i][] = $station1_select_result[0]['station'];  
                // var_dump($temp[$i][0]);
                $array[] = array(
                    'station' => $temp[$i][0],
                );
             
            
            
        }
        
     }
  $result_array = array(
        "code" => 0,
        "msg" => "success",
        "data" => $array,
    );
    return $result_array;      
}
function add($db, $userId, $head,$content, $station_name){
    // $db->beginTransaction();
    $insert_sql= "INSERT into `station_introduce`  (`head`,`content`,`station_name`) values ('$head' ,'$content','$station_name') ";      
    $insert_sql_result=$db->execSql($insert_sql);
    if( $insert_sql_result){
        $msg = "success";
    }else{
        $msg = "fail";
    }
    return $msg;

}
function edit($db,$head,$content,$id,$userId,$station_name){
    $sql="select `head`,`content` from `station_introduce` where `id`=$id ";
    $sql_result=$db->query($sql);
    $previous_head= $sql_result[0]['head'];
    $previous_content= $sql_result[0]['content'];
    $insert_sql= "INSERT into `update_introduce`  (`head`,`content`,`user_id`,`station_name`,`enable`,`status`,`station_intro_id`,`previous_head`,`previous_content`) values ('$head' ,'$content','$userId','$station_name','0','0','$id','$previous_head','$previous_content' ) ";      
    $insert_sql_result=$db->execSql($insert_sql);
    if( $insert_sql_result){
        $msg = "success";
    }else{
        $msg = "fail";
    }
    return $msg;

    // $up_sql="update  `station_introduce` set `head`='$head' ,`content`='$content'where `id`='$id'";
    // $up_sql_result=$db->execSql($up_sql);
    // if( $up_sql_result){
    //     $msg = "success";
    // }else{
    //     $msg = "fail";
    // }
    // return $msg;

}
function selectname($db,$station_name){
	$sql="select * from `station_introduce` where `station_name`='$station_name' and product_id='14' order by sort   ";
	$sql_result=$db->query($sql);

    return $sql_result;
        
}
function searchcontent($db,$id){
	$sql="select `head`, `content` from `station_introduce` where `id`='$id' and `enable`='1' and `status`='1' ";
	$sql_result=$db->query($sql);
    return $sql_result;
        
}

