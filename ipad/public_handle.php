<?php
/**
 * 一些公用的函数
 */

session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}
$userId = $userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../Include/db_connect.php";

$action = trim($_GET['action']);

switch ($action) {

    // 获取用户的详细信息
    case 'personal_info':

        $msg = searchPersonalInfo($db, $userId);
        break;

    case 'update_password':
        $password = trim($_POST['password']);
        $password = md5($password);
        $msg = updatePassword($db, $password, $userId);
        break;

    case 'update_userinfo':
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $long_phone = trim($_POST['long_phone']);
        $post = array(
            'email'=>$email,
            'phone'=>$phone,
            'long_phone'=>$long_phone
        );
        $msg = updateUserinfo($db,$post,$userId);
        break;

    default:
        # code...
        $msg = "Parameter Error";
        break;
}
echo json_encode($msg);

/**
 * 获取用户信息
 *
 * @param Object $db
 * @param Int $id
 * @return Array
 */
function searchPersonalInfo($db, $userId)
{
    $info_select_sql = "SELECT a.`username`,b.`group`,a.`email`,a.`phone`,a.`long_phone` FROM `user_list` as a LEFT JOIN `group` as b ON a.`group_id` = b.`id` WHERE a.`id` = $userId";
    $info_select_result = $db->query($info_select_sql);

    if ($info_select_result) {
        $msg = $info_select_result;
    } else {
        $msg = "fail";
    }
    return $msg;
}

function updateUserinfo($db,$post,$userId){
    $user_update_sql = "UPDATE `user_list` SET `email`='".$post['email']."' , `phone`='".$post['phone']."' , `long_phone`='".$post['long_phone']."' where `id` = '".$userId."'";
    $user_update_result = $db->execSql($user_update_sql);

    if($user_update_result){
        $msg = "success";
    }else{
        $msg = "fail";
    }
    return $msg;
}

/**
 * 用户修改密码
 *
 * @param [type] $db
 * @param [type] $password
 * @param [type] $userId
 * @return void
 */
function updatePassword($db, $password, $userId)
{
    $password_select_sql = "SELECT count(id) as num from `user_list` where `password`= '$password' and `id` = '$userId '";

    $password_select_result = $db->query($password_select_sql);

    if ($password_select_result[0]['num']) {
        $msg = -1;
        return $msg;
    }

    $password_update_sql = "UPDATE `user_list` SET `password` = '$password' WHERE `id`= '$userId' ";
    $password_update_result = $db->execSql($password_update_sql);

    if ($password_update_result) {
        $msg = 1;
    } else {
        $msg = 0;
    }

    return $msg;
}
