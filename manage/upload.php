<?php
session_start();
$userId = $_SESSION['cooper_user_info'][0]['id'];
$project = explode('|', $_SESSION['cooper_user_info'][0]['project_id']);

require_once "../Include/db_connect.php";
$action = $_GET['action'];

switch($action){
    case 'update':
       $post1=$_POST ;
    
        $msg=update($db,$post1);
    break;
    case 'lay_img_upload':
        $msg = lay_img_upload();
    break;
    case 'search':
       
        $msg = search($db,$userId);
        break;
    case 'search_content':
        $id = trim($_POST['id']);
        $msg = searchcontent($db,$id);
        break; 
    case 'select_name':
      
        $station_name = trim($_POST['station_name']);
        $product = trim($_POST['product']);
		$msg = selectname($db,$station_name,$product);
		break;
    case 'add':

        $head = trim($_POST['head']);
        $content = trim($_POST['content']);
        $station_name = trim($_POST['station_name']);
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
function selectname($db,$station_name,$product){
    $sql1="select * from product where  product='$product' and enable='1'";
    $sql1_result=$db->query($sql1);
    $product_id= $sql1_result[0]['id'];
    $sql="select * from `station_introduce` where `station_name`='$station_name' and  `product_id`=' $product_id' order by sort ";
    $sql_result=$db->query($sql);
    return $sql_result;
        
}
function searchcontent($db,$id){
	$sql="select `head`, `content` from `station_introduce` where `id`='$id' and `enable`='1' and `status`='1' ";
	$sql_result=$db->query($sql);
    return $sql_result;
        
}
 function lay_img_upload(){
   
     if ($_FILES["file"]["error"] == 0) {
     $fileUrl = './uploads';
      $dir = iconv("UTF-8", "GBK", $fileUrl);
       if (!file_exists($dir)) {
              mkdir($dir, 777, true);
          }
          $suffix = '.' . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        //   $picUrl = $fileUrl . '/' . self::GetRandStr() . $suffix;
        move_uploaded_file($_FILES["file"]["tmp_name"], $suffix);
          $data =array(
            "code"=> 0 
             ,"msg"=> "" 
             ,"data"=>array(
            "src"=> $suffix
            ,"title"=> $_FILES["file"]["name"]
                )
             );
    //    echo json_encode( $data );
        } 
      return json_encode( $data )  ;
}
function update($db,$post1){
    
    $station_name=$post1["station_name"];
    $c=$post1["c"];
    $sql="select * from `station_introduce` where `station_name`='$station_name'  ";
    $sql_result=$db->query($sql);
    for($i=0;$i<count( $sql_result);$i++){
        $a=$c[$i];
        $b=explode('-', $a);
        $id=$b[0];
        $sort=$b[1];
        $sql1="update  station_introduce set sort='$sort' where id='$id'";
        $sql1_result=$db->execSql($sql1);
       
    }
   
  
  $msg="success";
 return $msg;

}