<?php
/*
 * @Author: moxuan
 * @Date: 2019-03-02 09:32:03
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-06 11:04:01
 */
require_once "../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {
    case 'add_account':
        $username = addslashes(trim($_POST['username']));
        $password = md5(trim($_POST['password']));
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $level = trim($_POST['level']);
        $group = trim($_POST['group']);
        $group_category = trim($_POST['group_category']);

        if ($level == '2') {
            $product = $_POST['product'];
            foreach ($product as $key => $value) {
                $temp[] = $key;
            }
            $product = implode('|', $temp);
        } else {
            $product = trim($_POST['product']);
        }

        $post = array(
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "phone" => $phone,
            "level" => $level,
            "group" => $group,
            "group_category" => $group_category,
            "product_id" => $product,
        );
        $msg = insertAddAccount($db, $post);
        break;
    case 'update_account':
        $level = trim($_POST['level']);
        if ($level == '2') {
            $product = $_POST['product'];
            foreach ($product as $key => $value) {
                $temp[] = $key;
            }
            $product = implode('|', $temp);
        } else {
            $product = trim($_POST['product']);
        }
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $enable = trim($_POST['enable']);
        $userId = trim($_POST['userId']);
        $post = array(
            'product_id' => $product,
            'email' => $email,
            'phone' => $phone,
            'enable' => $enable,
        );
        $msg = updateAccount($db, $post, $userId);
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
    case 'isset_username':
        $username = trim($_POST['username']);
        $msg = searchCol($db, $username, 'username');
        break;
    default:
        $msg = "Parameter error";
        break;
}
echo json_encode($msg);

// var_dump($msg);

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
        "level" => $post['level'],
        "group_id" => $post['group'],
        "group_category" => $post['group_category'],
        "product_id" => $post['product_id'],
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
    $account_select_sql = "SELECT `id`,`username`,`product_id`,`group_id`,`email`,`phone`,`enable`,`level` FROM `user_list` limit $page,$limit";
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

            $group_select_sql = "SELECT `group` FROM `group` WHERE `id`='" . $account_select_result[$i]['group_id'] . "'";

            $group_select_result = $db->query($group_select_sql);

            if (is_array($temp[$i])) {
                $temp_product = implode("|", $temp[$i]);
            }

            $array[] = array(
                'id' => $account_select_result[$i]['id'],
                'username' => $account_select_result[$i]['username'],
                'product' => $temp_product,
                'group' => $group_select_result[0]['group'],
                'email' => $account_select_result[$i]['email'],
                'phone' => $account_select_result[$i]['phone'],
                'enable' => $account_select_result[$i]['enable'],
                'level' => $account_select_result[$i]['level'],
            );
        }
    }
    $account_select_count_sql = "SELECT count(id) as num FROM `user_list`";
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
        $msg = $result_array;
    }
    return $msg;
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
        $col_select_sql = "SELECT count(id) as count from `user_list` where `$col`='$data' ";
    } else {
        $col_select_sql = "SELECT count(id) as count from `user_list` where `$col`='$data' and `id`='$userId'";
    }

    $col_select_result = $db->query($col_select_sql);

    // $col_select_result 查找到数据时 返回success
    if ($col_select_result[0]['count']) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}
