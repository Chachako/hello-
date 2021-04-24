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
    case 'add_account':
        $username = addslashes(trim($_POST['username']));
        $password = md5(trim($_POST['password']));
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $long_phone = trim($_POST['long_phone']);
        $level = trim($_POST['level']);
        $group = trim($_POST['group']);
        $group_category = trim($_POST['group_category']);

        $group_select_sql = "SELECT `group` FROM `group` WHERE `id` = '" . $group . "'";
        $group_select_result = $db->query($group_select_sql);
        if ($group_select_result[0]['group'] == 'F1') {
            $level = 2;
        } else {
            $level = 0;
        }
        $product = $_POST['product'];
         
        $project = $_POST['project'];
  
        $project = implode('|', $project);
           
      // var_dump($project);

        foreach ($product as $key => $value) {
            $temp[] = $key;
        }
        $product = implode('|', $temp);
    
        $post = array(
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "phone" => $phone,
            "long_phone" => $long_phone,
            "level" => $level,
            "group" => $group,
            "group_category" => $group_category,
            "product_id" => $product,
            "project_id" => $project,
        );



        $msg = insertAddAccount($db, $post);
        break;
    case 'update_account':
        // var_dump($_POST);

        $level = trim($_POST['level']);
        
        $product = $_POST['product'];
        foreach ($product as $key => $value) {
            $temp[] = $key;
        }
        $product = implode('|', $temp);
 

        $project_id= $_POST['project'];
        $project_id = implode('|', $project_id);
      



        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $enable = trim($_POST['enable']);
        $userId = trim($_POST['userId']);
       
        $long_phone = trim($_POST['long_phone']);
        $group_category=trim($_POST['group_category']);
        $post = array(

            'product_id' => $product,
            'project_id' => $project_id,
            'email' => $email,
            'phone' => $phone,
            'long_phone' => $long_phone,
            'enable' => $enable,
            'group_category' =>$group_category,
        );
        $msg = updateAccount($db, $post, $userId);
        break;

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
    case 'pass_list':
        $id=trim($_POST['id']);
        $msg = passlist($db, $id);
        break;
    case 'pass_uplist':
        $up_id=trim($_POST['up_id']);
        $id=trim($_POST['id']);
        $msg = passuplist($db, $up_id,$id);
        break;
    case 'refuse_list':
        $id=trim($_POST['id']);
        $msg = refuselist($db, $id);
        break; 
    case 'refuse_uplist':
        $id=trim($_POST['id']);
        $msg = refuseuplist($db, $id);
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
    case 'station_list':
       $page = trim($_GET['page']);
       $limit = trim($_GET['limit']);
       $page = ($page - 1) * $limit;
       $msg = searchStationList($db, $page, $limit);
       break;
    case 'update_list':
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
        $station_intro_id=trim($_POST['up_id']);
        $page = ($page - 1) * $limit;
        $msg = searchUpdateList($db,$station_intro_id,$page, $limit);
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
    case 'search_user':
        $post=$_POST;
       
       
     
        $msg = searchUser($db, $post);

        break;



    default:
        $msg = "Parameter error";
        break;
}
echo json_encode($msg);

// var_dump($msg);





function searchUser($db,$post){
    $page = trim($post['page']);
    $limit = trim($post['limit']);
    $page = ($page - 1) * $limit;

    $name=trim($post['name']);
    $group=trim($post['department']);
    $product=trim($post['product']);
    $project=trim($post['project']);


  
    $account_select_sql='select u.* from user_list as u left join `group` as g on g.id=u.group_id ';
    $where= 'where 1=1 ' ;
    if($name!=''){
        $where.=" and u.username like '%$name%' ";
    }
    if($group!=""){
        $where .= " and g.group like '%$group%'  ";
    }

    // echo $account_select_sql.$where;die;

    if($product!=""){
   
       $product_id_sql="select * from product where product like '%$product%' ";
       $product_id_sql_result = $db->query($product_id_sql);
    //    var_dump($product_id_sql_result[0]['id']);die;
       if(!$product_id_sql_result){
        $where .= " and u.product_id ='no_product'  ";
       }
       if($product_id_sql_result){
           
           if(count($product_id_sql_result)==1){

            $where.=" and FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(u.product_id, '|',','))";
            // $account_select_sql.=$where;
            // // var_dump($account_select_sql);die;
            //    $where .= " and u.product_id  = ".$product_id_sql_result[0][id]."  ";
            //    var_dump($where);die;
           }else{
               for($i=0;$i<count($product_id_sql_result);$i++){
               
                if ($i == 0) {
                    $where .= " and (FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(u.product_id, '|',',')) ";
                } else if ($i == count($product_id_sql_result) - 1) {
                    $where .= " FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(u.product_id, '|',',')) )";
                } else {
                    $where .= " OR FIND_IN_SET('".$product_id_sql_result[0]['id']."',replace(u.product_id, '|',',')) ";
                }

               } 
            }
           }
        
        
    }

    if($project!=""){

           
       $project_id_sql="select * from project where project like '%$project%' ";
       $project_id_sql_result = $db->query($project_id_sql);

       if(!$project_id_sql_result){
        $where .= " and u.project_id ='no_project'  ";
       }

       if($project_id_sql_result){
           if(count($project_id_sql_result)==1){
            $where.=" and FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(u.project_id, '|',','))";
            //    $where .= " and u.project_id  = ".$project_id_sql_result[0][id]."  ";
           }else{
               for($i=0;$i<count($project_id_sql_result);$i++){
               
                if ($i == 0) {
                    
                    $where .= " and ( FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(u.project_id, '|',','))";
                } else if ($i == count($project_id_sql_result) - 1) {
                    $where .= " OR  FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(u.project_id, '|',','))) ";
                } else {
                    $where .= " OR FIND_IN_SET('".$project_id_sql_result[0]['id']."',replace(u.project_id, '|',',')) ";
                }

               } 
           }
        
       }

        
    }
   $account_select_sql.=$where;
   
   
    $account_select_sql.="limit $page,$limit  ";
    // var_dump($account_select_sql); die;
  

    // $account_select_sql = "SELECT `id`,`username`,`product_id`,`group_id`,`email`,`phone`,`long_phone`,`enable`,`level` ,`group_category`,`project_id` FROM `user_list`  limit $page,$limit  ";

    $account_select_result = $db->query($account_select_sql);

    if ($account_select_result) {
        for ($i = 0; $i < count($account_select_result); $i++) {
            // $temp;
            $product = $account_select_result[$i]['product_id'];
            if (!empty($product)) {
                $product = explode('|', $product);

                for ($j = 0; $j < count($product); $j++) {
                    $product_select_sql = "SELECT `product` FROM `product` WHERE  `id`='" . $product[$j] . "' ";
                    $product_select_result = $db->query($product_select_sql);
                    $temp[$i][] = $product_select_result[0]['product'];
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
                    $temp2[$i][] = $project_select_result[0]['project'];

                }
            }else{
                $temp2[$i][]="";
            }

            $group_select_sql = "SELECT `group` FROM `group` WHERE `id`='" . $account_select_result[$i]['group_id'] . "'";

            $group_select_result = $db->query($group_select_sql);

            if (is_array($temp[$i])) {
                $temp_product = implode("|", $temp[$i]);
            }

            if (is_array($temp2[$i])) {
                $temp_project = implode("|", $temp2[$i]);
            }
             // var_dump($temp_project) ;

            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' => $account_select_result[$i]['username'],
                'product' => $temp_product,
                'project' => $temp_project,
                'group' => $group_select_result[0]['group'],
                'email' => $account_select_result[$i]['email'],
                'phone' => $account_select_result[$i]['phone'],
                'long_phone' => $account_select_result[$i]['long_phone'],
                'enable' => $account_select_result[$i]['enable'],
                'level' => $account_select_result[$i]['level'],
                'group_category' =>$account_select_result[$i]['group_category'],
            );
        }
    }

    $account_select_count_sql = "SELECT count(u.id) as num FROM `user_list`  as u left join `group` as g on g.id=u.group_id  ";
    $account_select_count_sql .= $where;
    // select u.* from user_list as u left join `group` as g on g.id=u.group_id


    $account_select_count_result = $db->query($account_select_count_sql);

    if ($account_select_result && $account_select_count_result[0]['num']) {
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    } else {
        $arrresult_arrayay = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $arrresult_arrayay;
    }

    return $msg;
    
}



//查询product 下的 project

function getProductProject($db, $product){

    $str = ' where 1=1 and ';
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

    $sql=" SELECT pp.*, p.`project` FROM product_project pp left join project p on pp.`project_id`=p.`id`   ";

    $sql.=$str;

    $result = $db-> query($sql);


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

    $isUsersExist = searchCol1($db, $post['username'], 'username', $post['email'],'email');
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

function forbiddenUser($db, $id)
{
    //删除user_list表
    // $delete_sql = "DELETE from `user_list` where `id`=$id ";
    $delete_sql = "update `user_list` set `enable`=0 where `id`=$id ";
    //查询此用户是否创建task 创建task 则不允许删除 
    $select_sql = "SELECT count(id) as num  from team_ipad.issue_list where `create_user_id`='$id' ";
    $select_result=$db->query( $select_sql);

    $select_sql0 = "SELECT count(id) as num  from team_keyboard.issue_list where `create_user_id`='$id' ";
    $select_result0=$db->query( $select_sql0);

    $select_sql1 = "SELECT count(id) as num  from team_watch.issue_list where `create_user_id`='$id' ";
    $select_result1=$db->query( $select_sql1);

    if(!(($select_result[0]['num'] || $select_result0[0]['num']) || $select_result1[0]['num'])){

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
function passlist($db, $id)
{
    $pass_sql = "update `station_introduce` set `enable`=1,`status`=1 where `id`=$id ";
       $pass_result=$db->execSql( $pass_sql);
       if ($pass_result) {
        $msg = "success";
       } 
    
    else{
       $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function passuplist($db, $up_id,$id)
{   
    $pass_sql = "update `update_introduce` set `enable`=1,`status`=1 where `id`='$id' ";
    $pass_result=$db->execSql( $pass_sql);
    $sql="select * from `update_introduce` where `id`='$id'  ";
    $sql_result=$db->query( $sql);
     $head= $sql_result[0]['head'];
     $content=$sql_result[0]['content'];
    $previous_sql="update `station_introduce` set `head`='$head',`content`='$content' where `id`='$up_id'";
    $previous_sql_result=$db->execSql( $previous_sql);

       if ($previous_sql_result) {
        $msg = "success";
       } 
    
    else{
       $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}
function refuselist($db, $id)
{
    $pass_sql = "update `station_introduce` set `enable`=0,`status`=1 where `id`=$id ";
       $pass_result=$db->execSql( $pass_sql);
       if ($pass_result) {
        $msg = "success";
       } 
    
    else{
       $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}

function refuseuplist($db, $id)
{
    $pass_sql = "update `update_introduce` set `enable`=0,`status`=1 where `id`=$id ";
       $pass_result=$db->execSql( $pass_sql);
       if ($pass_result) {
        $msg = "success";
       } 
    
    else{
       $msg = "fail";
    }
    // var_dump($msg);
    return $msg;

}
function renewUser($db, $id)
{
    //删除user_list表
    // $delete_sql = "DELETE from `user_list` where `id`=$id ";
    $delete_sql = "update `user_list` set `enable`=1 where `id`=$id ";
    $delete_result=$db->execSql( $delete_sql);
    if ($delete_result) {
        $msg = "success";
    } 
    else{
        $msg = "fail";
    }
    // var_dump($msg);
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
    $account_select_sql = "SELECT * FROM `user_list`  limit $page,$limit  ";

    $account_select_result = $db->query($account_select_sql);
    
    if ($account_select_result) {

        for ($i = 0; $i < count($account_select_result); $i++) {
            // $temp;
            $product = $account_select_result[$i]['product_id'];
            if (!empty($product)) {
                $product = explode('|', $product);

                for ($j = 0; $j < count($product); $j++) {
                    $product_select_sql = "SELECT `product` FROM `product` WHERE  `id`='" . $product[$j] . "' ";
                    $product_select_result = $db->query($product_select_sql);
                    $temp[$i][] = $product_select_result[0]['product'];
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
                    $temp2[$i][] = $project_select_result[0]['project'];

                }
            }else{
                $temp2[$i][]="";
            }

          

            $group_select_sql = "SELECT `group` FROM `group` WHERE `id`='" . $account_select_result[$i]['group_id'] . "'";

            $group_select_result = $db->query($group_select_sql);

            if (is_array($temp[$i])) {
                $temp_product = implode("|", $temp[$i]);
            }

            if (is_array($temp2[$i])) {
                $temp_project = implode("|", $temp2[$i]);
            }
             // var_dump($temp_project) ;

            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' => $account_select_result[$i]['username'],
                'product' => $temp_product,
                'project' => $temp_project,
                'group' => $group_select_result[0]['group'],
                'email' => $account_select_result[$i]['email'],
                'phone' => $account_select_result[$i]['phone'],
                'long_phone' => $account_select_result[$i]['long_phone'],
                'enable' => $account_select_result[$i]['enable'],
                'level' => $account_select_result[$i]['level'],
                'group_category' =>$account_select_result[$i]['group_category'],
            );
        }
    }


    $account_select_count_sql = "SELECT count(id) as num FROM `user_list`  ";
    $account_select_count_result = $db->query($account_select_count_sql);

    if ($account_select_result && $account_select_count_result[0]['num']) {
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    } else {
        $arrresult_arrayay = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $arrresult_arrayay;
    }

  

    return $msg;
}

function searchUpdateList($db,$station_intro_id,$page, $limit)
{
    $account_select_sql = "SELECT * FROM `update_introduce` WHERE `station_intro_id`='$station_intro_id'  limit $page,$limit  ";
    $account_select_result = $db->query($account_select_sql);
    // $account_select_sql1 = "SELECT * FROM `station_introduce` WHERE `id`='$station_intro_id' ";
    // $account_select_result1 = $db->query($account_select_sql1);
    // var_dump($account_select_result)  ;
    if ($account_select_result) {
        for ($i = 0; $i < count($account_select_result); $i++) {
             $username = $account_select_result[$i]['user_id'];   
         
            if (!empty($username)) {

                        for ($j = 0; $j < count($username); $j++) {
                            $username_select_sql = "SELECT `username` FROM `user_list` WHERE  `id`='$username' ";
                           
                            $username_select_result = $db->query($username_select_sql);
                            // var_dump($username_select_result);
                            $temp[$i][] = $username_select_result[0]['username'];
                            $userIDArr[$i][]=$username[$j];

                        }

                    }

            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'station_name' => $account_select_result[$i]['station_name'],
                'username' => $temp[$i],
                'content' => $account_select_result[$i]['content'],
                'head' => $account_select_result[$i]['head'],
                'enable' => $account_select_result[$i]['enable'],
                'status' => $account_select_result[$i]['status'],
                'station_intro_id'=>$account_select_result[$i]['station_intro_id'],
                'phead'=>$account_select_result[$i]['previous_head'],
                'pcontent'=>$account_select_result[$i]['previous_content'],
               
            );
        }
    }


    $account_select_count_sql = "SELECT count(id) as num FROM `station_introduce` ";
    $account_select_count_result = $db->query($account_select_count_sql);

    if ($account_select_result && $account_select_count_result[0]['num']) {
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    } else {
        $result_array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    }

   

    return $msg;
}
function searchStationList($db, $page, $limit)
{
    $account_select_sql = "SELECT * FROM `station_introduce`  limit $page,$limit  ";
    $account_select_result = $db->query($account_select_sql);
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
    $account_select_count_sql = "SELECT count(id) as num FROM `station_introduce` ";
    $account_select_count_result = $db->query($account_select_count_sql);

    if ($account_select_result && $account_select_count_result[0]['num']) {
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    } else {
        $result_array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $account_select_count_result[0]['num'],
            "data" => $array,
        );
        $msg = $result_array;
    }

   

    return $msg;
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
      return $usreId_res[0]['user'];
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
function searchCol($db, $data, $col, $userId = '')
{ 
    if (empty($userId)) {
        $col_select_sql = "SELECT count(id) as num from `user_list` where `$col`='$data' ";
    } else {
        $col_select_sql = "SELECT count(id) as num from `user_list` where `$col`='$data' and `id`='$userId'";
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
function searchCol1($db, $data, $col,$data1,$col1, $userId = '')
{   
    if (empty($userId)) {
        $col_select_sql = "SELECT count(id) as num from `user_list` where `$col`='$data' and `$col1`='$data1' ";
    } else {
        $col_select_sql = "SELECT count(id) as num from `user_list` where `$col`='$data' and `$col1`='$data1' and `id`='$userId'";
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
