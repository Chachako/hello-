<?php
/*
 * @Author: moxuan
 * @Date: 2019-03-02 09:32:03
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-26 18:53:28
 */
require_once "../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {

    case 'delete_account':
        // var_dump($_POST);
        $id=trim($_POST['id']);
        $msg = deleteUser($db, $id);
        break;

    case 'forbidden_account':
        // var_dump($_POST);
        $id=trim($_POST['id']);
        $msg = forbiddenUser($db, $id);
        break;

    case 'renew_account':
        // var_dump($_POST);
        $id=trim($_POST['id']);
        $msg = renewUser($db, $id);
        break;

    case 'update_password':
        $password = trim($_POST['password']);
        $password = md5($password);
        $userId = trim($_POST['userId']);
        $msg = updatePassword($db, $password, $userId);
        break;

    case 'user_enable':
        $usreId = trim($_POST['userId']);
        $enable = trim($_POST['enable']);
        $msg = userEnable($db, $userId, $enable);
        break;
    case 'personal_info':
        $usdrId = trim($_POST['userId']);
        $msg = searchPersonalInfo($db, $usdrId);
        break;
    case 'account_list':
        $page = trim($_GET['page']);
        $limit = trim($_GET['limit']);
        $page = ($page - 1) * $limit;
        $msg = searchAccountList($db, $page, $limit);
        break;
    case 'my_account_list':
        // $page = trim($_GET['page']);
        // $limit = trim($_GET['limit']);
        // $page = ($page - 1) * $limit;
        $stageId = trim($_GET['stageId']);
        $stationId = trim($_GET['stationId']);
        $msg = searchmyAccountList($db,$stageId,$stationId);
        break;
    case 'isset_username':
        $username = trim($_POST['username']);
        $msg = searchCol($db, $username, 'username');
        break;

    case 'get_group_account':
        $group = trim($_POST['group']);
        $msg = getGroupAccount($db, $group);
        break;

    case 'getgroupname':
        $group = trim($_POST['group']);
        $station_pro_id =trim($_POST['station_project_id']);
        // var_dump($station_pro_id);
        //  var_dump($group); die();
        $msg = getGroupName($db, $group, $station_pro_id);
        break;

    case 'get_product_project':
        // $product_id = trim($_POST['product_id']);

        $product= $_POST['productArr'];

        // $product = implode('|', $productArr);
         // var_dump($productArr);
        $msg = getProductProject($db, $product);

        break;

    case 'search_filter':
            $post=$_POST;
            $msg = searchFilter($db, $post);
            break;
    case 'del_filterId':

            $post=$_POST;
            $msg = deleteFilterId($db, $post);
            break;
    case 'get_product_list':
            
            $post=$_POST;
            $msg = getProductList($db, $post);
            break; 

    default:
        $msg = "Parameter error";
        break;
}
echo json_encode($msg);

// var_dump($msg);





function searchFilter($db,$post){
    $page = trim($post['page']);
    $limit = trim($post['limit']);
    $page = ($page - 1) * $limit;

    $filtername=trim($post['filtername']);
    $product=trim($post['product']);
    $project=trim($post['project']);
    $username=trim($post['username']);
    
    $account_select_sql = "select f.* from filter as f ";
    $where= 'where 1=1 ' ;
    if($filtername!=''){
        $where.="and f.filter_name like '%$filtername%' ";
    }
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
    if($project!=""){

           
       $project_id_sql="select * from project where project like '%$project%' ";
       $project_id_sql_result = $db->query($project_id_sql);

       if(!$project_id_sql_result){
        $where .= " and f.project_id ='no_project'  ";
       }

       if($project_id_sql_result){
           if(count($project_id_sql_result)==1){
            $where.=" and FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(f.project_id, '|',','))";
           }else{
               for($i=0;$i<count($project_id_sql_result);$i++){
                if ($i == 0) {
                    
                    $where .= " and ( FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(f.project_id, '|',','))";
                } else if ($i == count($project_id_sql_result) - 1) {
                    $where .= " OR  FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(f.project_id, '|',','))) ";
                } else {
                    $where .= " OR FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(f.project_id, '|',',')) ";
                }

               } 
           }
        
       }
        
    }
    if($username!=""){

           
        $username_id_sql="select * from user_list where username like '%$username%' ";
    
        $username_id_sql_result = $db->query($username_id_sql);
        if(!$username_id_sql_result){
         $where .= " and f.user_id ='no_username'  ";
        }
 
        if($username_id_sql_result){
          
            if(count($username_id_sql_result)==1){
                $where .= " and f.user_id  = ".$username_id_sql_result[0]['id']."  ";
               
            }else{

                for($i=0;$i<count($username_id_sql_result);$i++){
                  
                 if ($i == 0) {
                   
                     $where .= " and (f.user_id = ".$username_id_sql_result[0]['id']." ";
                    
                 } else if ($i == count($username_id_sql_result) - 1) {
                     $where .= " OR f.user_id = ".$username_id_sql_result[$i]['id']." ) ";
                     
                 } else {
                     $where .= " OR f.user_id = ".$username_id_sql_result[$i]['id']." ";
                  
                 }

 
                } 
            }
         
        }
         
     
    }
    $account_select_sql.=$where;
    $account_select_sql.="limit $page,$limit  ";
    $account_select_result = $db->query($account_select_sql);

    $account_select_count_select = "SELECT count(1) as num from filter as f $where" ;
    $account_select_count_result = $db->query($account_select_count_select,'Row');

    if ($account_select_result) {
        for ($i = 0; $i < count($account_select_result); $i++) {


            $username = $account_select_result[$i]['user_id'];         
            if (!empty($username)) {
                        $username = explode('|', $username);

                        for ($j = 0; $j < count($username); $j++) {
                            $username_select_sql = "SELECT `username` FROM `user_list` WHERE  `id`='" . $username[$j] . "' ";
                            $username_select_result = $db->query($username_select_sql);
                            $temp[$i][] = $username_select_result[0]['username'];
                            $userIDArr[$i][]=$username[$j];

                        }

                    }

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

            $project = $account_select_result[$i]['project_id'];

            if( $project!=""&& $project!=NULL){
                $project = explode('|', $project);
                for ($j = 0; $j < count($project); $j++) {
                    $project_select_sql = "SELECT `project` FROM `project` WHERE  `id`='" . $project[$j] . "' ";
                    $project_select_result = $db->query($project_select_sql);
                    $temp3[$i][] = $project_select_result[0]['project'];
                    $projectIdArr[$i][] = $project[$j];

                }
            }else {
                $temp3[$i][]="";
            }

       

            $build = $account_select_result[$i]['build_id'];

            if( $build!=""&& $build!=NULL){
                $build = explode('|', $build);
                for ($j = 0; $j < count($build); $j++) {
                    $build_select_sql = "SELECT `build` FROM `build` WHERE  `id`='" . $build[$j] . "' ";
                    $build_select_result = $db->query($build_select_sql);
                    $temp4[$i][] = $build_select_result[0]['build'];
                    $buildIdArr[$i][] = $build[$j];

                }
            }else {
                $temp4[$i][]="";
            }
           


            $station = $account_select_result[$i]['station_id'];

            if( $station!=""&& $station!=NULL){
                $station = explode('|', $station);
                for ($j = 0; $j < count($station); $j++) {
                    $station_select_sql = "SELECT `station` FROM `station` WHERE  `id`='" . $station[$j] . "' ";
                    $station_select_result = $db->query($station_select_sql);

                    $temp5[$i][] = $station_select_result[0]['station'];
                    
                    $stationIdArr[$i][] = $station[$j];
                }
            }else {
                $temp5[$i][]="";
            }
            
            $status = $account_select_result[$i]['status_id'];

            if( $status!=""&& $status!=NULL){
                $status = explode('|', $status);
                for ($j = 0; $j < count($status); $j++) {
                    $status_select_sql = "SELECT `status` FROM `status` WHERE  `id`='" . $status[$j] . "' ";
                    $status_select_result = $db->query($status_select_sql);

                  
                    $temp6[$i][] = $status_select_result[0]['status'];
                    $statusIdArr[$i][] = $status[$j];

                }
            }else{
                $temp6[$i][]="";
            }
           
            
            if (is_array($temp2[$i])) {
                $temp_product = implode("|", $temp2[$i]);
            }

            if (is_array($temp3[$i])) {
                $temp_project = implode("|", $temp3[$i]);
            }
            if (is_array($temp4[$i])) {
                $temp_build = implode("|", $temp4[$i]);
            }

            if (is_array($temp5[$i])) {
                $temp_station = implode("|", $temp5[$i]);
            }
            if (is_array($temp6[$i])) {
                $temp_status = implode("|", $temp6[$i]);
            }
            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' =>$temp[$i],
                'filtername' => $account_select_result[$i]['filter_name'],

                'product' => $temp2[$i],
                'temp_product' => $temp_product,
                'product_id'=>$productIdArr[$i],

                'project' => $temp3[$i],
                'temp_project' => $temp_project,
                'project_id' => $projectIdArr[$i],

                'build'   => $temp4[$i],
                'temp_build'=>$temp_build,
                'build_id'   => $buildIdArr[$i],
 
                'station' => $temp5[$i],
                'temp_station' => $temp_station,
                'station_id' => $stationIdArr[$i],

                'status' => $temp6[$i],
                'temp_status' => $temp_status,
                'status_id'=>$statusIdArr[$i],
                
                'start_time' => $account_select_result[$i]['start_time'],
                'end_time' => $account_select_result[$i]['end_time'],
            );
        }
    }
//    echo $result_array;die;
    $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" => $array,
    );
    // $msg = $result_array;
    return $result_array;   
}

/**
 * 获取product列表
 *
 * @param [type] $db
 * @param [type] $page
 * @param [type] $limit
 * @return void
 */
function getProductList($db, $post)
{
    $id=trim($post['id']);
    $product=trim($post['product']);
    // var_dump($id);die;
    $get_product_sql = "SELECT `id`,`product` from `product` limit $page,$limit";

    $get_product_count_sql = "SELECT count(id) as count from `product`";
    $get_product_result = $db->query($get_product_sql);
    $get_product_count_result = $db->query($get_product_count_sql);
    // var_dump($get_product_sql);die;
    $product_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $get_product_count_result[0]['count'],
        "data" => $get_product_result,
    );

    return $product_result;
}

function getProductProject($db, $product){

    $str = '  and ';
    for ($i = 0; $i < count($product); $i++) {
        if (count($product) > 1) {
            if ($i == 0) {
                $str .= " `product_id` = '$product[$i]' ";
            } else if ($i == count($product) - 1) {
                $str .= " OR `product_id` = '$product[$i]' ";
            } else {
                $str .= " OR `product_id` = '$product[$i]' ";
            }
        } else {
            $str .= " `product_id` = '$product[$i]' ";
        }
    }

    $sql=" SELECT pp.*, p.`project` FROM product_project pp left join project p on pp.`project_id`=p.`id`  where pp.`enable` = '1'  ";

    $sql.=$str;

    $result = $db-> query($sql);
    //   var_dump($str);

    return $result;

}



/**
 * 增加新用户
 *
 * @param [type] $db 数据库连接句柄
 * @param string $post
 * @return void
 */
function insertAddAccount($db, $post)
{
    $table = "user_list";
    $addAccount_insert_sql = array(
        "username" => $post['username'],
        "password" => $post['password'],
        "email" => $post['email'],
        "phone" => $post['phone'],
        "long_phone" => $post['long_phone'],
        "level" => $post['level'],
        "group_id" => $post['group'],
        "group_category" => $post['group_category'],
        "product_id" => $post['product_id'],
        "project_id" => $post['project_id'],
    );

    $isUsersExist = searchCol($db, $post['username'], 'username');
    if ($isUsersExist == 'success') {
        $msg = "exists";
        return $msg;
    }

    $addAccount_insert_result = $db->insert($table, $addAccount_insert_sql);

    if ($addAccount_insert_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }

    return $msg;
}



function deleteUser($db, $id)
{
    //删除user_list表
    $delete_sql = "DELETE from `user_list` where `id`=$id ";
    //查询此用户是否创建task 创建task 则不允许删除 
    $select_sql = "SELECT count(id) as num  from team_ipad.issue_list where `create_user_id`='$id' ";
    $select_result=$db->query( $select_sql);

    $select_sql0 = "SELECT count(id) as num  from team_keyboard.issue_list where `create_user_id`='$id' ";
    $select_result0=$db->query( $select_sql0);

    if(!($select_result[0]['num'] || $select_result0[0]['num'])){

       $delete_result=$db->execSql( $delete_sql);
       if ($delete_result) {
        $msg = "success";
       } 
    } 
    else{
       $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}



/**
 * 根据filterId 删除filter设置
 *
 * @param [type] $db
 * @param [type] $filterId
 * @return void
 */
function deleteFilterId($db, $post){
//   echo($id);die;
     
    $id=trim($post['id']);
    // var_dump($id);
    $filterId_delete_sql = "DELETE FROM `filter` WHERE `id`='$id'";
    // var_dump($filterId_delete_sql);
    $filterId_deleter_result = $db->execSql($filterId_delete_sql);
    if ($filterId_deleter_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}


/**
 * 删除filter设置
 * 删除当前用户的所有filter数据
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function deletefilter($db, $userId)
{
    $db->beginTransaction();
    $filter_delete_sql = "DELETE FROM `filter` WHERE `user_id` ='$userId'";
    try {
        $db->execSql($filter_delete_sql);

        $db->commit();
        $msg = $filter_delete_sql;
    } catch (Exception $e) {
        $db->rollback();
        $msg = "fail";
    }
    return $msg;
}
/**
 * 更新用户信息
 *
 * @param Object $db
 * @param Array $post
 * @param Int $userId
 * @return Void
 */
function updateAccount($db, $post, $userId)
{
   
    $product_value = $post['product_id'];
    $product_result = searchCol($db, $product_value, 'product_id', $userId);

    if ($product_result == 'fail') {
          
        $filter_delete_sql = "DELETE FROM filter where `user_id` = '$userId'";
        $setting_delete_sql = "DELETE FROM setting where `user_id` = '$userId'";

        // var_dump($filter_delete_sql );
        //  var_dump($setting_delete_sql );
        $db->execSql($filter_delete_sql);
        $db->execSql($setting_delete_sql);

    }
   
    $table = 'user_list';
    $where = "id='$userId'";
    $account_update_result = $db->update($table, $post, $where);
    if ($account_update_result) {
        $msg = 1;
    } else {
        $msg = 0;
    }
    return $msg;
}

/**
 * 更新用户密码
 *
 * @param Object $db
 * @param String $password
 * @param Int $userId
 * @return void
 */
function updatePassword($db, $password, $userId)
{
    $password_select_sql = "SELECT count(id) as num from `user_list` where `password`= '{$password}' and `id` = '{$userId} '";

    $password_select_result = $db->query($password_select_sql);

    if ($password_select_result[0]['num']) {
        $msg = -1;
        return $msg;
    }

    $password_update_sql = "UPDATE `user_list` SET `password` = '{$password}' WHERE `id`= '{$userId}' ";
    $password_update_result = $db->execSql($password_update_sql);

    if ($password_update_result) {
        $msg = 1;
    } else {
        $msg = 0;
    }

    return $msg;
}

/**
 * 修改user的enable状态
 *
 * @param Object $db
 * @param Int $userId
 * @param Int $enable
 * @return String
 */
function userEnable($db, $userId, $enable)
{
    $userEnable_update_sql = "UPDATE `user_list` SET `enable`=`$enable` where `id`='$userId' ";

    $userEnable_update_result = $db->execSql($userEnable_update_sql);

    if ($userEnable_update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 获取用户信息
 *
 * @param [type] $db
 * @param [type] $id
 * @return void
 */
function searchPersonalInfo($db, $userId)
{
    $info_select_sql = "SELECT a.`username`,b.`group`,a.`email`,a.`phone` FROM `user_list` as a LEFT JOIN `group` as b ON a.`group_id` = b.`id` WHERE a.`id` = $userId";
    $info_select_result = $db->query($info_select_sql);

    if ($info_select_result) {
        $msg = $info_select_result;
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 获取用户列表
 *
 * @param Object $db
 * @param Int $page
 * @param Int $limit
 * @return Array
 */
function searchAccountList($db, $page, $limit)
{
    $account_select_sql = "SELECT `id`,`user_id`,`filter_name`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` FROM `filter`  limit $page,$limit  ";
    

    $account_select_result = $db->query($account_select_sql);
    // var_dump($account_select_result);die;
    $account_select_count_select = "SELECT count(1) as num from filter";
    $account_select_count_result = $db->query($account_select_count_select,'Row');

    if ($account_select_result) {

        for ($i = 0; $i < count($account_select_result); $i++) {

            $username = $account_select_result[$i]['user_id'];
     
            //  var_dump($username);die;
            if (!empty($username)) {
                $username = explode('|', $username);

                for ($j = 0; $j < count($username); $j++) {
                    $username_select_sql = "SELECT `username` FROM `user_list` WHERE  `id`='" . $username[$j] . "' ";
                    $username_select_result = $db->query($username_select_sql);
                   
                    $temp[$i][] = $username_select_result[0]['username'];
                }
            }

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

            $project = $account_select_result[$i]['project_id'];

            if( $project!=""&& $project!=NULL){
                $project = explode('|', $project);
                for ($j = 0; $j < count($project); $j++) {
                    $project_select_sql = "SELECT `project` FROM `project` WHERE  `id`='" . $project[$j] . "' ";
                    $project_select_result = $db->query($project_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp3[$i][] = $project_select_result[0]['project'];
                    $projectIdArr[$i][] = $project[$j];
                }
            }else {
                $temp3[$i][]="";
            }

       

            $build = $account_select_result[$i]['build_id'];

            if( $build!=""&& $build!=NULL){
                $build = explode('|', $build);
                for ($j = 0; $j < count($build); $j++) {
                    $build_select_sql = "SELECT `build` FROM `build` WHERE  `id`='" . $build[$j] . "' ";
                    $build_select_result = $db->query($build_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp4[$i][] = $build_select_result[0]['build'];
                    $buildIdArr[$i][] = $build[$j];

                }
            }else {
                $temp4[$i][]="";
            }
            


            $station = $account_select_result[$i]['station_id'];

            if( $station!=""&& $station!=NULL){
                $station = explode('|', $station);
                for ($j = 0; $j < count($station); $j++) {
                    $station_select_sql = "SELECT `station` FROM `station` WHERE  `id`='" . $station[$j] . "' ";
                    $station_select_result = $db->query($station_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp5[$i][] = $station_select_result[0]['station'];
                    
                    $stationIdArr[$i][] = $station[$j];
                }
            }else {
                $temp5[$i][]="";
            }
            
            $status = $account_select_result[$i]['status_id'];

            if( $status!=""&& $status!=NULL){
                $status = explode('|', $status);
                for ($j = 0; $j < count($status); $j++) {
                    $status_select_sql = "SELECT `status` FROM `status` WHERE  `id`='" . $status[$j] . "' ";
                    $status_select_result = $db->query($status_select_sql);
                    //为空时 没值
                    $temp6[$i][] = $status_select_result[0]['status'];
                    $statusIdArr[$i][] = $status[$j];

                }
            }else{
                $temp6[$i][]="";
            }
            $account_select_count_select = "SELECT count(1) as num from filter";
            $account_select_count_result = $db->query($account_select_count_select,'Row');

            if (is_array($temp2[$i])) {
                $temp_product = implode("|", $temp2[$i]);
            }

            if (is_array($temp3[$i])) {
                $temp_project = implode("|", $temp3[$i]);
            }
            if (is_array($temp4[$i])) {
                $temp_build = implode("|", $temp4[$i]);
            }

            if (is_array($temp5[$i])) {
                $temp_station = implode("|", $temp5[$i]);
            }
            if (is_array($temp6[$i])) {
                $temp_status = implode("|", $temp6[$i]);
            }
            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' =>$temp[$i],
                'filtername' => $account_select_result[$i]['filter_name'],

                'product' => $temp2[$i],
                'temp_product' => $temp_product,
                'product_id'=>$productIdArr[$i],

                'project' => $temp3[$i],
                'temp_project' => $temp_project,
                'project_id' => $projectIdArr[$i],

                'build'   => $temp4[$i],
                'temp_build'=>$temp_build,
                'build_id'   => $buildIdArr[$i],

                'station' => $temp5[$i],
                'temp_station' => $temp_station,
                'station_id' => $stationIdArr[$i],

                'status' => $temp6[$i],
                'temp_status' => $temp_status,
                'status_id'=>$statusIdArr[$i],
                
                'start_time' => $account_select_result[$i]['start_time'],
                'end_time' => $account_select_result[$i]['end_time'],
            );
           
        }
    }
   
    $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" => $array,
    );
    // $msg = $result_array;

    return $result_array;
}



function searchmyAccountList($db,$stageId,$stationId)
{
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $account_select_sql = "SELECT `id`,`user_id`,`filter_name`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` FROM `filter` where `user_id`='$user_id' and `product_id`='14'  ";
    
    session_start();
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where = " where 1 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) "; 
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
            }
        }
    }
    $where .="and f.product_id='14' and f.user_id=' $user_id' ";
    if($stageId!=''&&$stageId!=NULL){
        $stageId=explode('|', $stageId) ;
        for ($i = 0; $i < count($stageId); $i++) {
            if (count($stageId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) "; 
                } else if ($i == count($stageId) - 1) {
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
            }
        }
    }
    if($stationId!=''&&$stationId!=NULL){
        $stationId=explode('|', $stationId) ;
        for ($i = 0; $i < count($stationId); $i++) {
            if (count($stationId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) "; 
                  
                } else if ($i == count($stationId) - 1) {
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ) ";
                   
                } else {
                   
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
               
            }
        }
    }
    $countWhere = $where;
    // $where .="limit $page,$limit ";

    $account_select_sql="select f.* from filter as f $where";
    $account_select_result=$db->query($account_select_sql);
    $account_select_count_select = "SELECT count(1) as num from filter as f $countWhere ";
    $account_select_count_result = $db->query($account_select_count_select,'Row');


// echo $account_select_sql;die;
    $account_select_result = $db->query($account_select_sql);
    // var_dump($account_select_result);die;
    $account_select_count_select = "SELECT count(1) as num from filter where `user_id`='$user_id' and `product_id`='14'";
    $account_select_count_result = $db->query($account_select_count_select,'Row');

    if ($account_select_result) {

        for ($i = 0; $i < count($account_select_result); $i++) {

            $username = $account_select_result[$i]['user_id'];
     
            //  var_dump($username);die;
            if (!empty($username)) {
                $username = explode('|', $username);

                for ($j = 0; $j < count($username); $j++) {
                    $username_select_sql = "SELECT `username` FROM `user_list` WHERE  `id`='" . $username[$j] . "' ";
                    $username_select_result = $db->query($username_select_sql);
                   
                    $temp[$i][] = $username_select_result[0]['username'];
                }
            }

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

            $project = $account_select_result[$i]['project_id'];

            if( $project!=""&& $project!=NULL){
                $project = explode('|', $project);
                for ($j = 0; $j < count($project); $j++) {
                    $project_select_sql = "SELECT `project` FROM `project` WHERE  `id`='" . $project[$j] . "' ";
                    $project_select_result = $db->query($project_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp3[$i][] = $project_select_result[0]['project'];
                    $projectIdArr[$i][] = $project[$j];
                }
            }else {
                $temp3[$i][]="";
            }

       

            $build = $account_select_result[$i]['build_id'];

            if( $build!=""&& $build!=NULL){
                $build = explode('|', $build);
                for ($j = 0; $j < count($build); $j++) {
                    $build_select_sql = "SELECT `build` FROM `build` WHERE  `id`='" . $build[$j] . "' ";
                    $build_select_result = $db->query($build_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp4[$i][] = $build_select_result[0]['build'];
                    $buildIdArr[$i][] = $build[$j];

                }
            }else {
                $temp4[$i][]="";
            }
            


            $station = $account_select_result[$i]['station_id'];

            if( $station!=""&& $station!=NULL){
                $station = explode('|', $station);
                for ($j = 0; $j < count($station); $j++) {
                    $station_select_sql = "SELECT `station` FROM `station` WHERE  `id`='" . $station[$j] . "' ";
                    $station_select_result = $db->query($station_select_sql);

                    // var_dump($project_select_result);
                    //为空时 没值
                    $temp5[$i][] = $station_select_result[0]['station'];
                    
                    $stationIdArr[$i][] = $station[$j];
                }
            }else {
                $temp5[$i][]="";
            }
            
            $status = $account_select_result[$i]['status_id'];

            if( $status!=""&& $status!=NULL){
                $status = explode('|', $status);
                for ($j = 0; $j < count($status); $j++) {
                    $status_select_sql = "SELECT `status` FROM `status` WHERE  `id`='" . $status[$j] . "' ";
                    $status_select_result = $db->query($status_select_sql);
                    //为空时 没值
                    $temp6[$i][] = $status_select_result[0]['status'];
                    $statusIdArr[$i][] = $status[$j];

                }
            }else{
                $temp6[$i][]="";
            }
            // $account_select_count_select = "SELECT count(1) as num from filter";
            // $account_select_count_result = $db->query($account_select_count_select,'Row');

            if (is_array($temp2[$i])) {
                $temp_product = implode("|", $temp2[$i]);
            }

            if (is_array($temp3[$i])) {
                $temp_project = implode("|", $temp3[$i]);
            }
            if (is_array($temp4[$i])) {
                $temp_build = implode("|", $temp4[$i]);
            }

            if (is_array($temp5[$i])) {
                $temp_station = implode("|", $temp5[$i]);
            }
            if (is_array($temp6[$i])) {
                $temp_status = implode("|", $temp6[$i]);
            }
            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' =>$temp[$i],
                'filtername' => $account_select_result[$i]['filter_name'],

                'product' => $temp2[$i],
                'temp_product' => $temp_product,
                'product_id'=>$productIdArr[$i],

                'project' => $temp3[$i],
                'temp_project' => $temp_project,
                'project_id' => $projectIdArr[$i],

                'build'   => $temp4[$i],
                'temp_build'=>$temp_build,
                'build_id'   => $buildIdArr[$i],

                'station' => $temp5[$i],
                'temp_station' => $temp_station,
                'station_id' => $stationIdArr[$i],

                'status' => $temp6[$i],
                'temp_status' => $temp_status,
                'status_id'=>$statusIdArr[$i],
                
                'starttime' => $account_select_result[$i]['start_time'],
                'endtime' => $account_select_result[$i]['end_time'],
            );
           
        }
    }
   
    $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" => $array,
    );
    // $msg = $result_array;

    return $result_array;
}
/**
 * Undocumented function
 *
 * @param [type] $db
 * @param [type] $group
 * @return void
 */
function getGroupAccount($db, $group)
{
    $groupAccount_select_sql = "SELECT `id`,`username` FROM `user_list` WHERE `group_id` = '$group'";
    $groupAccount_select_result = $db->query($groupAccount_select_sql);

    return $groupAccount_select_result;
}


function getGroupName($db, $group,$station_pro_id)
{
    $userid = "SELECT `user` from `station_user` where `group`='$group' and `station_project_id` ='$station_pro_id' ";
    $usreId_res = $db->query($userid);

    if($usreId_res){
      return $usreId_res[0][user];
    }else{
      return 0;
    }



    
}



/**
 * 查询某个字段的数据是否存在
 *
 * @param Object $db
 * @param String $data
 * @param String $col
 * @param String $id  视情况看需不需要带id
 * @return String 查找到数据返回success 否则返回fail
 */
function searchCol($db, $data, $col, $filterId)
{
    if (empty($filterId)) {
        $col_select_sql = "SELECT count(id) as num from `filter` where `$col`='$data' ";
    } else {
        $col_select_sql = "SELECT count(id) as num from `filter` where `$col`='$data' and `id`='$filterId'";
    }
    // var_dump($col_select_sql);
    $col_select_result = $db->query($col_select_sql);

    // $col_select_result 查找到数据时 返回success
    if ($col_select_result[0]['num']) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}
