<?php
require_once "./../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {

    case 'delete_product':
        $productid = trim($_POST['id']);
        $msg = deleteProduct($db, $productid);
        break;

    case 'forbidden_product':
        $productid = trim($_POST['id']);
        $msg = forbiddenproduct($db, $productid);
        break;

    case 'renew_product':
        $productid = trim($_POST['id']);
        $msg = renewproduct($db, $productid);
        break;
   
    case 'delete_station':
        # code...
        // $station_id=trim($_POST['id']);
        $station_id=trim($_POST['station']);
        $msg = deleteStation($db, $station_id);
        break;

    case 'forbidden_station':
        # code...
        // $station_id=trim($_POST['id']);
        $station_id=trim($_POST['id']);
        $msg = forbiddenstation($db, $station_id);
        break;

    case 'renew_station':
        # code...
        // $station_id=trim($_POST['id']);
        $station_id=trim($_POST['id']);
        $msg = renewstation($db, $station_id);
        break;

    case 'delete_project':  
        $project_id=trim($_POST['id']);
        $msg = deleteProject($db, $project_id);
        break;

    case 'forbidden_prodject':  
        $project_id=trim($_POST['id']);
        $msg = forbiddenprodject($db, $project_id);
        break;

    case 'renew_project':  
        $project_id=trim($_POST['id']);
        $msg = renewproject($db, $project_id);
        break;

    case 'delete_build':
        $build_project_id = trim($_POST['id']);
        $msg = deleteBuild($db, $build_project_id);
        break;  

    case 'forbidden_build':
        $build_project_id = trim($_POST['id']);
        $msg = forbiddenbuild($db, $build_project_id);
        break;

    case 'renew_build':
        $build_project_id = trim($_POST['id']);
        $msg = renewbuild($db, $build_project_id);
        break;

    case 'delete_group':
        $group_id = trim($_POST['id']);
        $msg = deleteGroup($db, $group_id);  
        break; 
    
    case 'forbidden_group':
        $group_id = trim($_POST['id']);
        $msg = forbiddengroup($db, $group_id);  
        break; 

    case 'renew_group':
        $group_id = trim($_POST['id']);
        $msg = renewgroup($db, $group_id);  
        break;   

    case 'add_product':
        $product = trim($_POST['product']);
        $msg = addProduct($db, $product);
        break;

    case 'add_project':
        $product_id = trim($_POST['product']);
        $project = strtoupper(trim($_POST['project']));
        $msg = addProject($db, $product, $project);
        break;

    case 'add_build':
      
        $build = trim($_POST['build']);

        $project_id =trim($_POST['project']);
        $msg = addBuild($db, $build,$project_id);
        break;

    case 'add_station':
        $project = trim($_POST['project']);
        $station = trim($_POST['station']);
        $msg = addStation($db, $project, $station);
        break;

    case 'add_group':
        $group = trim($_POST['group']);
        $productid = trim($_GET['productId']);
        $msg = addGroup($db, $group, $productid);
        break;

    case 'add_station_user':
        $station_project_id = trim($_POST['station_project_id']);
        $group = trim($_POST['group']);
        $username = trim($_POST['username']);
        $msg = addStationUser($db, $station_project_id, $group, $username);
        break;

    case 'get_product_group':
        $msg = getProductGroup($db);
        break;

    case 'get_product_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = getProductList($db, $page, $limit);
        break;
    case 'get_project_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = getProjectList($db, $page, $limit);
        break;

    case 'get_build_station':
        $project= $_POST['buildArr'];
        $project= $_POST['stationArr'];
        $msg = getBuildStationList($db, $project);
        break;

    case 'get_build_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = getBuildList($db, $page, $limit);
        break;
    

    case 'get_station_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = getStationList($db, $page, $limit);
        break;


    case 'get_station_project':
        $project = trim($_POST['project']);
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
        $page = ($page - 1) * $limit;
        $msg = getStationProjectList($db, $project, $page, $limit);
        break;

    case 'get_station_user':
        $station_project_id = trim($_POST['station_project_id']);
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
        $page = ($page - 1) * $limit;
        $msg = getStationUserList($db, $station_project_id, $page, $limit);
        break;

    case 'get_group_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = getGroupList($db, $page, $limit);
        break;

    case 'get_group_list_create':
        $msg = getGroupList_create($db);
        break;

    case 'get_product':
        $msg = getProduct($db);
        break;

    case 'get_project':
        $msg = getProject($db);
        break;
    case 'get_login_info':
        $msg = get_login_info($db);
        break;   

    case 'get_login_out_info':
        // var_dump($post);
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
     
        $post = array(
            'page' => $page,
            'limit' => $limit,
         
        );
        $msg = get_login_out_info($db,$post);
        break;   
     

    case 'get_account_project':

        $msg = get_account_project($db);
        break;   
    
    case 'delete_login_out_info':
   
        $msg = delete_login_out_info($db,$post);

    default:
        $msg = "aaa";
        break;
}

$db->destruct();
echo json_encode($msg);
// var_dump($msg);


function delete_login_out_info($db){
    $sql=" DELETE FROM login_time WHERE 1";
    $result = $db-> query($sql);
    return $result;
}



function get_account_project($db){

    // $sql=" "

    $sql=" SELECT pp.*, p.`project` FROM product_project pp left join project p on pp.`project_id`=p.`id`   ";
    $result = $db-> query($sql);
    return $result;

}

function get_login_out_info($db,$post )
{
    $page = $post['page'];
    $limit = $post['limit'];
    $page = ($page - 1) * $limit;
  
    $info_sql="SELECT * from  login_time    where   DATE_FORMAT(login_time,'%Y-%m-%d') =CURRENT_DATE    ORDER by id  DESC limit $page,$limit ";

    // var_dump($info_sql);

    $res=$db->query($info_sql);


    $count_sql="SELECT * from  login_time   where   DATE_FORMAT(login_time,'%Y-%m-%d') =CURRENT_DATE  ";
      $rescount=$db->query($count_sql);
    $count=count($rescount);
    $result = array(
        "code" => 0,
        "msg" => "",
        "count" => $count,
        "data" => $res,
    );
    return $result;
}





function get_login_info($db)
{
    $nowtime=time()-60;
    $info_sql="SELECT * from  user_online where `online_time`>'$nowtime'  ";
    $res=$db->query($info_sql);
 
    $count=count($res);


    $result = array(
        "code" => 0,
        "msg" => "",
        "count" => $count,
        "data" => $res,
    );




    return $result;
}







/**
 * 添加产品
 *
 * @param Object $db  数据库连接句柄
 * @param String $product
 * @param String $enable
 * @return void
 */
function addProduct($db, $product)
{
    $field_result = searchRepeatField($db, 'product', 'product', $product);
    if ($field_result) {
        $msg = "exist";
        return $msg;
    }

    $product_name = strtolower($product);
    $product_name = "team_".$product_name;
    $creat_db = "CREATE DATABASE $product_name";
    $creat_db_result = $db->execSql($creat_db);

    $creat_iusse = "CREATE TABLE $product_name.issue_list (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `create_user_id` varchar(255) NOT NULL COMMENT '创建用户ID',
      `product_id` varchar(255) DEFAULT NULL,
      `project_id` int(11) NOT NULL COMMENT '专案ID',
      `build_id` int(11) NOT NULL COMMENT 'bulid_id',
      `station_id` int(11) NOT NULL COMMENT '公站id',
      `task_title` varchar(255) NOT NULL COMMENT 'tast标题',
      `task_description` text NOT NULL COMMENT 'task描述',
      `uploadpath` text NOT NULL,
      `olduploadpath` text NOT NULL,
      `eta` datetime NOT NULL COMMENT '结束时间',
      `create_time` datetime NOT NULL COMMENT '创建时间',
      `step` varchar(255) NOT NULL COMMENT '当前到哪一步，1为刚创建',
      `assign` varchar(255) NOT NULL,
      `create_assign` varchar(255) DEFAULT NULL,
      `access_user_id` text NOT NULL COMMENT '存放访问的人员id，有id代表看过，不再显示小红点',
      `status_id` int(11) NOT NULL COMMENT '状态id',
      `cancel_content` text,
      `done_content` text,
      `done_score` varchar(255) DEFAULT NULL,
      `cancel_user` varchar(255) DEFAULT NULL,
      `done_user` varchar(255) DEFAULT NULL,
      `cancel_done_time` datetime DEFAULT NULL,
      `check_user` varchar(255) DEFAULT NULL COMMENT 'station指定个人id与部门leader的id',
      `create_check_user` varchar(255) DEFAULT NULL,
      `appleDRI` varchar(255) DEFAULT NULL,
      `priority` varchar(255) DEFAULT NULL,
      `onlinetime` varchar(255) DEFAULT NULL,
      `version` varchar(255) DEFAULT NULL,
      `requestor_id` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    )";
    $creat_iusse_result = $db->execSql($creat_iusse);

    $creat_history = "CREATE TABLE $product_name.history (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `issue_list_id` int(11) NOT NULL,
      `user_id` varchar(255) DEFAULT NULL,
      `update_time` datetime DEFAULT NULL,
      `status` text,
      `upload` text,
      `oldupload` text,
      `assign` varchar(255) DEFAULT NULL,
      `assign_user` varchar(255) DEFAULT NULL,
      `version` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    )";
    $creat_history_result = $db->execSql($creat_history);



    $product_insert_sql = "INSERT into `product`(`product`) values('$product')";
    $product_insert_result = $db->execSql($product_insert_sql);

    if ($product_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}


function deleteProduct($db,$productid){
    $product_delete_sql="delete from `product` where `id` = $productid  ";
     // var_dump($product_delete_sql);
    $Account_delete_result=$db->execSql( $product_delete_sql);
   
    if ($Account_delete_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function forbiddenproduct($db,$productid){
    $product_update_sql="update product set enable='0' where `id` = $productid  ";
     // var_dump($product_delete_sql);
    $Account_update_result=$db->execSql( $product_update_sql);
   
    if ($Account_update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function renewproduct($db,$productid){
    $product_update_sql="update product set enable='1' where `id` = $productid  ";
     // var_dump($product_delete_sql);
    $Account_update_result=$db->execSql( $product_update_sql);
   
    if ($Account_update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function deleteStation($db,$station_id){
    $db->beginTransaction();

    try {
        //删station表
        $station_delete_sql="delete from `station` where `id` = $station_id ";
        $delete_result=$db->execSql($station_delete_sql);
      
        //删station_project表
        $product_project_delete_sql = "delete from `station_project` where `station` = $station_id";
        $db->execSql($product_project_delete_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }

 return $msg;
    // $station_delete_sql="delete from `station` where `id` = $station_id ";
    //  // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($station_delete_sql);
   
    // if ($delete_result) {
    //     $msg = "success";
    // } else {
    //     $msg = "fail";
    // }
    // // var_dump($msg);
    // return $msg;
}

function forbiddenstation($db,$station_id){
    $db->beginTransaction();

    try {
        //删station表
        //$station_delete_sql="delete from `station` where `id` = $station_id ";
        //$delete_result=$db->execSql($station_delete_sql);
      
        //删station_project表
        $product_project_delete_sql = "update station_project set enable='0' where  `id` = $station_id";
        $db->execSql($product_project_delete_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }

 return $msg;
    // $station_delete_sql="delete from `station` where `id` = $station_id ";
    //  // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($station_delete_sql);
   
    // if ($delete_result) {
    //     $msg = "success";
    // } else {
    //     $msg = "fail";
    // }
    // // var_dump($msg);
    // return $msg;
}

function renewstation($db,$station_id){
    $db->beginTransaction();

    try {
        //删station表
        //$station_delete_sql="delete from `station` where `id` = $station_id ";
        //$delete_result=$db->execSql($station_delete_sql);
      
        //删station_project表
        $product_project_delete_sql = "update station_project set enable='1' where  `id` = $station_id";
        $db->execSql($product_project_delete_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }

 return $msg;
    // $station_delete_sql="delete from `station` where `id` = $station_id ";
    //  // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($station_delete_sql);
   
    // if ($delete_result) {
    //     $msg = "success";
    // } else {
    //     $msg = "fail";
    // }
    // // var_dump($msg);
    // return $msg;
}


function deleteBuild($db,$build_project_id){

    $build_delete_sql="delete from `build_project` where `id` = $build_project_id ";
     // var_dump($product_delete_sql);
    $delete_result=$db->execSql($build_delete_sql);
   
    if ($delete_result) {
        $msg = "success";


        
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function forbiddenbuild($db,$build_project_id){

    //$build_delete_sql="delete from `build_project` where `id` = $build_project_id ";
    $build_update_sql="update build_project set enable='0' where `id` = $build_project_id  ";
     // var_dump($product_delete_sql);
    $update_result=$db->execSql($build_update_sql);
   
    if ($update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function renewbuild($db,$build_project_id){

    //$build_delete_sql="delete from `build_project` where `id` = $build_project_id ";
    $build_update_sql="update build_project set enable='1' where `id` = $build_project_id  ";
     // var_dump($product_delete_sql);
    $update_result=$db->execSql($build_update_sql);
   
    if ($update_result) {
        $msg = "success";


        
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}


function deleteGroup($db,$group_id){

    $group_delete_sql="delete from `group` where `id` = $group_id ";
     // var_dump($product_delete_sql);
    $delete_result=$db->execSql($group_delete_sql);
   
    if ($delete_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function forbiddengroup($db,$group_id){

    $group_update_sql="update `group` set `enable`=0 where `id` = $group_id ";
     // var_dump($product_delete_sql);
    $update_result=$db->execSql($group_update_sql);
   
    if ($update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function renewgroup($db,$group_id){

    $group_update_sql="update `group` set `enable`=1 where `id` = $group_id ";
     // var_dump($product_delete_sql);
    $update_result=$db->execSql($group_update_sql);
   
    if ($update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function deleteProject($db,$project_id){


     // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($group_delete_sql);

    $db->beginTransaction();

    try {
        $project_delete_sql="delete from `project` where `id` = $project_id ";
        $db->execSql($project_delete_sql);
      

        $product_project_delete_sql = "delete from `product_project` where `project_id` = $project_id";
        $db->execSql($product_project_delete_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }
     return $msg;
}

function forbiddenprodject($db,$project_id){


     // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($group_delete_sql);

    $db->beginTransaction();

    try {
        //$project_delete_sql="delete from `project` where `id` = $project_id ";
        $project_update_sql="update project set enable='0' where `id` = $project_id  ";
        $db->execSql($project_update_sql);
      
        $product_project_update_sql ="update product_project set enable='0' where `project_id` = $project_id  ";
        //$product_project_delete_sql = "delete from `product_project` where `project_id` = $project_id";
        $db->execSql($product_project_update_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }
     return $msg;
}

function renewproject($db,$project_id){


     // var_dump($product_delete_sql);
    // $delete_result=$db->execSql($group_delete_sql);

    $db->beginTransaction();

    try {
        //$project_delete_sql="delete from `project` where `id` = $project_id ";
        $project_update_sql="update project set enable='1' where `id` = $project_id  ";
        $db->execSql($project_update_sql);
      
        $product_project_update_sql ="update product_project set enable='1' where `project_id` = $project_id  ";
        //$product_project_delete_sql = "delete from `product_project` where `project_id` = $project_id";
        $db->execSql($product_project_update_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }
     return $msg;
}






/**
 * 添加部门
 *
 * @param Object $db  数据库连接句柄
 * @param String $product
 * @param String $enable
 * @return void
 */
function addGroup($db, $group, $productid, $enable = '1')
{
    $field_result = searchRepeatField($db, 'group', 'group', $group);
    if ($field_result) {
        $msg = "exist";
        return $msg;
    }

    $group_insert_sql = "INSERT into `group` values('','$group','$productid','$enable')";
    $group_insert_result = $db->execSql($group_insert_sql);

    if ($group_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 添加专案 project
 *
 * @return void
 */
function addProject($db, $product, $project)
{
   $product=$_POST['product'];
   $project=$_POST['project'];

    $field_result = searchRepeatField($db, 'project', 'project', $project);
    if ($field_result) {
        $msg = "exist";
        return $msg;
    }

    $db->beginTransaction();
    $project_insert_sql = "INSERT into `project`(`project`) values('$project')";
    try {
        $db->execSql($project_insert_sql);
        $project_id = $db->lastInsertId();
        $product_project_insert_sql = "INSERT into `product_project`(`product_id`,`project_id`,`enable`) value ('$product','$project_id','1')";
        $db->execSql($product_project_insert_sql);
        $msg = "success";
        $db->commit();
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $db->rollback();
        return $msg;
    }
    return $msg;
}

/**
 * 添加build
 *
 * @return void
 */
function addBuild($db, $build,$project_id, $enable = '1')
{

    $field_result = searchRepeatField($db, 'build', 'build', $build);
    //如果这个build已经存在 则我们去查询这个关系是否在project——build中也存在
    if ($field_result) {
        
        $build_id_sql="SELECT  * from build where `build`='$build' ";
        $build_id_result=$db->query($build_id_sql);
        $build_id=$build_id_result[0]['id']; 
        //
        $pj_bu_sql="select * from build_project where `build_id`='$build_id' and `project_id`='$project_id' ";
        $pj_bu_result=$db->query($pj_bu_sql);
         
        // var_dump($pj_bu_result); 
        if($pj_bu_result){
        $msg = "exist";
        return $msg;

        }else{
            $project_build_sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
            $project_build_result=$db->execSql($project_build_sql);

            
            if ($project_build_result) {
              $msg = "success";
            } else {
              $msg = "fail";
             }
              return $msg;

        }


    }

    $build_insert_sql = "INSERT into `build` (`build`,`enable`) values('$build','$enable')";
    $build_insert_result = $db->execSql($build_insert_sql);

    $build_id_sql="SELECT  * from build where `build`='$build' ";
    $build_id_result=$db->query($build_id_sql);
    $build_id=$build_id_result[0]['id'];
    $project_build_sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
    $project_build_result=$db->execSql($project_build_sql);

    if ($build_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 添加工站 Station
 *
 * @param [type] $db
 * @param [type] $project
 * @param [type] $station
 * @param string $enable
 * @return void
 */
function addStation($db, $project, $station, $enable = '1')
{
    $station_insert_sql = "INSERT into `station` values('','$station','$enable')";
    $field_result = searchRepeatField($db, 'station', 'station', $station);
    if ($field_result) {
        // 现输入的station和project是否存在关系链接
        $station_id_sql = "SELECT `id` from `station` where `station`='$station' ";
        $station_id_result = $db->query($station_id_sql);
        $station_id = $station_id_result[0]['id'];
        $project_result = searchRepeatDoubleField($db, 'station_project', 'station', 'project', $station_id, $project);
        if ($project_result) {
            $msg = "exist";
            return $msg;
        } else {
            $station_insert_sql = "";
        }
    }

    if (empty($station_insert_sql)) {
        $stationUser_insert_sql = "INSERT INTO `station_project`(`station`,`project`,`enable`) values('$station_id','$project','1')";
        $stationUser_insert_result = $db->execSql($stationUser_insert_sql);
    } else {
        $db->beginTransaction();
        try {
            $station_insert_result = $db->execSql($station_insert_sql);
            $station_id = $db->lastInsertId();
            $stationUser_insert_sql = "INSERT INTO `station_project`(`station`,`project`,`enable`) values('$station_id','$project','1')";
            $stationUser_insert_result = $db->execSql($stationUser_insert_sql);
            $db->commit();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $db->rollback();
            return $msg;
        }
    }
    if ($stationUser_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * Undocumented function
 *
 * @param [type] $db
 * @param [type] $station_project_id
 * @param [type] $group
 * @param [type] $username
 * @return void
 */
function addStationUser($db, $station_project_id, $group, $username)
{
    $stationUser_insert_sql = "INSERT INTO `station_user`(`station_project_id`,`group`,`user`) values('$station_project_id','$group','$username')";
    $array = array(
        'station_project_id' => $station_project_id,
        'group' => $group,
    );
    $repear_result = RepeatField($db, 'station_user', $array);
    if ($repear_result) {

        //如果该工站有人负责 则跟新工站负责人
        $update_station_user_sql="UPDATE `station_user` set `user`='$username' WHERE `station_project_id`='$station_project_id' and `group`='$group'";
        // var_dump($update_station_user_sql);
        $update_result=$db->execSql($update_station_user_sql);
     
        if ($update_result) {
        $msg = "success";
        } else {
        $msg = "fail";
        }

        
        return $msg;
    }
    $stationUser_insert_result = $db->execSql($stationUser_insert_sql);
    if ($stationUser_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 获取可用的Product
 *
 * @param [type] $db
 * @return void
 */
function getProduct($db)
{
    $get_product_sql = "SELECT `id`,`product` from `product` where `enable` = '1'";
    $get_product_result = $db->query($get_product_sql);

    return $get_product_result;
}

/**
 * 获取product列表
 *
 * @param [type] $db
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getProductList($db, $page, $limit)
{
    $get_product_sql = "SELECT `id`,`product`,`enable` from `product` limit $page,$limit";
    $get_product_count_sql = "SELECT count(id) as count from `product`";
    $get_product_result = $db->query($get_product_sql);
    $get_product_count_result = $db->query($get_product_count_sql);

    $product_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_product_count_result[0]['count'],
        "data" => $get_product_result,
    );

    return $product_result;
}

/**
 * 获取Project
 *
 * @param [type] $db
 * @return void
 */
function getProject($db)
{
    $get_project_sql = "SELECT `id`,`project` from `project` where `enable` = '1'";
    $get_project_result = $db->query($get_project_sql);

    return $get_project_result;
}

/**
 * 获取project列表
 *
 * @param [type] $db
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getProjectList($db, $page, $limit)
{
    $get_project_sql = "SELECT `id`,`project`,`enable` from `project` limit $page,$limit";
    $get_project_result = $db->query($get_project_sql);

    foreach ($get_project_result as $key => $value) {
        $project_id[] = $value['id'];
    }

    for ($i = 0; $i < count($project_id); $i++) {
        $product_select_sql = "SELECT b.`product` from `product_project` as a
            left join `product` as b on a.`product_id`=b.`id`
            where a.`project_id` = '$project_id[$i]'
         ";
        $product_select_result = $db->query($product_select_sql);
        if ('' == $product_select_result[0]) {
            // 刪除空白链接
            unset($get_project_result[$i]);
            continue;
        }
        for ($j = 0; $j < count($product_select_result); $j++) {
            $product_result[] = $product_select_result[$j]['product'];
        }

        $get_project_result[$i]['product'][] = implode("|", $product_result);
        // 清空缓存字段
        unset($product_result);
    }

    $get_project_count_sql = "SELECT count(id) as num from `project`";
    $get_project_count_result = $db->query($get_project_count_sql);

    $project_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_project_count_result[0]['num'],
        "data" => $get_project_result,
    );
    return $project_result;
}

function getBuildList($db, $page, $limit)
{   
     $get_build_sql= "select a.*, b.build  from (select bp.*,p.project from build_project  bp  left join  project p on  p.id = bp.project_id where p.enable='1') as a  left join build  b on b.id =a.build_id limit $page,$limit ";


     
    // $get_build_sql = "SELECT `id`,`build`,`enable` from `build` limit $page,$limit";
    $get_build_count_sql = "SELECT count(bp.id) as count from `build_project` bp left join project p on  p.id = bp.project_id where p.enable='1'";
    $get_build_result = $db->query($get_build_sql);
    $get_build_count_result = $db->query($get_build_count_sql);

    $build_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_build_count_result[0]['count'],
        "data" => $get_build_result,
    );

    return $build_result;
}
function getBuildStationList($db, $project)
{    
    $str=' where 1=1  ';
    for ($i = 0; $i < count($project); $i++) {
        if (count($project) > 1) {
            if ($i == 0) {
                $str .= " `project_id` = '$project[$i]' ";
            } else if ($i == count($project) - 1) {
                $str .= " OR `project_id` = '$project[$i]' ";
            } else {
                $str .= " OR `project_id` = '$project[$i]' ";
            }
        } else {
            $str .= " `project_id` = '$project[$i]' ";
        }
    }
    //  $get_build_sql= "select a.*, b.build  from (select bp.*,p.project from build_project  bp  left join  project p on  p.id = bp.project_id where p.enable='1') as a  left join build  b on b.id =a.build_id limit $page,$limit ";
    // $sql=" SELECT sp.*, s.`station` FROM station_project sp left join station s on sp.`station_id`=s.`id`   ";
    $sql_build=" SELECT bp.*, b.`build` FROM build_project bp left join build b on bp.`build_id`=b.`id`  ";
    $sql_build.=$str;
    $result_build = $db-> query($sql_build);
    $msg['build']= $result_build;
    for ($i = 0; $i < count($project); $i++) {
        if (count($project) > 1) {
            if ($i == 0) {
                $str .= " `project` = '$project[$i]' ";
            } else if ($i == count($project) - 1) {
                $str .= " OR `project` = '$project[$i]' ";
            } else {
                $str .= " OR `project` = '$project[$i]' ";
            }
        } else {
            $str .= " `project` = '$project[$i]' ";
        }
    }
     $sql_station=" SELECT sp.*, s.`station` FROM station_project sp left join station s on sp.`station`=s.`id`   ";

    $sql_station.=$str;

    $result_station = $db-> query($sql_station);
    $msg['station']= $result_station;





//    var_dump($result);die;
    return $result;
}

/**
 * 获取station列表
 *
 * @param [type] $db
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getStationList($db, $page, $limit)
{
    $get_station_sql="select a.*, b.station as stationName from (select bp.*,p.project as projectName from station_project  bp  left join  project p on  p.id = bp.project where p.enable='1') as a  left join station  b on b.id =a.station ORDER BY a.`id` DESC limit $page,$limit " ;

    $get_station_count_sql = "SELECT count(bp.id) as count from `station_project` bp left join project p on  p.id = bp.project where p.enable='1'";
    $get_station_result = $db->query($get_station_sql);
    $get_station_count_result = $db->query($get_station_count_sql);


    // $get_station_sql = "SELECT `id`,`station`,`enable` from `station` ORDER BY `id` DESC limit $page,$limit";
    // $get_station_count_sql = "SELECT count(id) as count from `station`";
    // $get_station_result = $db->query($get_station_sql);
    // $get_station_count_result = $db->query($get_station_count_sql);

    $station_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_station_count_result[0]['count'],
        "data" => $get_station_result,
    );

    return $station_result;
}


/**
 * 获取stationUser列表
 *
 * @param [type] $db
 * @param [type] $project
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getStationProjectList($db, $project, $page, $limit)
{
    $stationProject_select_sql = "SELECT a.`id`,b.`project`,c.`station` FROM `station_project` as a
    LEFT JOIN `project` as b ON b.`id` = a.`project`
    LEFT JOIN `station` as c ON c.`id` = a.`station`
    WHERE a.`project`='$project' ORDER BY a.`id` DESC limit $page,$limit";

    $stationProject_count_sql = "SELECT count(a.`id`) as num FROM `station_project` as a
    LEFT JOIN `project` as b ON b.`id` = a.`project`
    LEFT JOIN `station` as c ON c.`id` = a.`station`
    WHERE a.`project`='$project'";

    $stationProject_select_result = $db->query($stationProject_select_sql);
    $stationProject_count_result = $db->query($stationProject_count_sql);

    $stationProject_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $stationProject_count_result[0]['num'],
        "data" => $stationProject_select_result,
    );
    return $stationProject_result;
}

/**
 * Undocumented function
 *
 * @param [type] $db
 * @param [type] $project
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getStationUserList($db, $station_project_id, $page, $limit)
{
    $stationUser_select_sql = "SELECT a.`id`,b.`group`,c.`username` FROM `station_user` as a
    LEFT JOIN `group` as b ON b.`id` = a.`group`
    LEFT JOIN `user_list` as c ON c.`id` = a.`user`
    WHERE a.`station_project_id`='$station_project_id' ORDER BY a.`id` DESC limit $page,$limit";

    $stationUser_count_sql = "SELECT count(a.`id`) as num FROM `station_user` as a
    LEFT JOIN `group` as b ON b.`id` = a.`group`
    LEFT JOIN `user_list` as c ON c.`id` = a.`user`
    WHERE a.`station_project_id`='$station_project_id'";

    $stationUser_select_result = $db->query($stationUser_select_sql);
    $stationUser_count_result = $db->query($stationUser_count_sql);

    $stationUser_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $stationUser_count_result[0]['num'],
        "data" => $stationUser_select_result,
    );
    return $stationUser_result;
}

/**
 * Undocumented function
 *
 * @param [type] $db
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getGroupList($db, $page, $limit)
{
    $get_group_sql = "SELECT `id`,`group`,`product_id`,`enable` from `group` limit $page,$limit";
    $get_group_count_sql = "SELECT count(id) as count from `group`";
    $get_group_result = $db->query($get_group_sql);
    $get_group_count_result = $db->query($get_group_count_sql);

    if ($get_group_result) {
        for ($i = 0; $i < count($get_group_result); $i++) {
            $product = $get_group_result[$i]['product_id'];
            if (!empty($product)) {
                $product = explode('|', $product);
                for ($j = 0; $j < count($product); $j++) {
                    $product_select_sql = "SELECT `product` FROM `product` WHERE  `id`='" . $product[$j] . "' ";
                    $product_select_result = $db->query($product_select_sql);
                    $temp[$i][] = $product_select_result[0]['product'];
                }
            }
            if (is_array($temp[$i])) {
                $temp_product = implode("|", $temp[$i]);
            }
            $array[] = array(
                'id' => $get_group_result[$i]['id'],
                'group' => $get_group_result[$i]['group'],
                'product_id' => $temp_product,
                'enable' => $get_group_result[$i]['enable'],
            );
        }
    }

    $group_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_group_count_result[0]['count'],
        "data" => $array,
    );

    return $group_result;
}
function getGroupList_create($db)
{
    $get_group_sql = "SELECT `id`,`group`,`enable` from `group` WHERE `enable` =1";
    $get_group_count_sql = "SELECT count(id) as count from `group` WHERE `enable` =1";
    $get_group_result = $db->query($get_group_sql);
    $get_group_count_result = $db->query($get_group_count_sql);

    $group_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_group_count_result[0]['count'],
        "data" => $get_group_result,
    );

    return $group_result;
}
/**
 * 返回产品和部门
 *
 * @param [type] $db
 * @return void
 */
function getProductGroup($db)
{
    $get_product_sql = "SELECT `id`,`product` from `product` ";
    $get_group_sql = "SELECT `id`,`group` from `group`";

    $get_product_result = $db->query($get_product_sql);
    $get_group_result = $db->query($get_group_sql);

    $get_result = array(
        'product' => $get_product_result,
        'group' => $get_group_result,
    );

    return $get_result;
}

/**
 * Undocumented function
 *
 * @param Object $db
 * @param String $table
 * @param Array $array
 * @return void
 */
function RepeatField($db, $table, $array)
{
    $field_select_sql = "SELECT count(id) as num from `$table` WHERE 1=1 ";
    foreach ($array as $key => $value) {
        $temp_sql .= "and `$key`='$value' ";
    }
    $field_select_sql .= $temp_sql;
    $field_select_result = $db->query($field_select_sql);
    
    return $field_select_result[0]['num'];
}

/**
 * 查询字段是否重复
 *
 * @param object $db    数据库句柄
 * @param string $table 查询的数据库
 * @param string $field 查询的字段
 * @param string $value 查询的值
 * @return string
 */
function searchRepeatField($db, $table, $field, $value)
{
    $field_select_sql = "SELECT count(id) as num from `$table` where `$field` = '$value' ";
    $field_select_result = $db->query($field_select_sql);
    if ($field_select_result[0]['num']) {
        $msg = 1;
    } else {
        $msg = 0;
    }
    return $msg;
}

/**
 * 验证单表中的两个字段
 *
 * @param [type] $db
 * @param [type] $table
 * @param [type] $field1
 * @param [type] $field2
 * @param [type] $value1
 * @param [type] $value2
 * @return void
 */
function searchRepeatDoubleField($db, $table, $field1, $field2, $value1, $value2)
{
    $field_select_sql = "SELECT count(id) as num from `$table` where `$field1` = '$value1' and `$field2` = '$value2' ";
    $field_select_result = $db->query($field_select_sql);
    if ($field_select_result[0]['num']) {
        $msg = 1;
    } else {
        $msg = 0;
    }
    return $msg;
}
