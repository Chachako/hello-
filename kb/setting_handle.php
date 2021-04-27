<?php
/*
 * @Author: moxuan
 * @Date: 2019-03-06 17:07:17
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-11 16:23:24
 */

/**
 * setting_handle 相关接口数据处理
 */
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}

$userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {
    case 'add_setting':
        $post = joinField($_POST);
        // $userId = 2;
        $msg = addSetting($db, $userId, $post);
        break;

    case 'del_setting':
        // $userId = 1;
        $setting_id = trim($_POST['setting_id']);
        $msg = deleteSetting($db, $userId, $setting_id);
        break;

    case 'del_settingId':
        $setting_id = trim($_POST['setting_id']);
        $msg = deleteSettingId($db, $setting_id, $userId);
        break;

    case 'update_setting':
        $post = joinField($_POST);
        $msg = updateSetting($db, $post);
        break;

    case "update_settingid":
        $settingId = trim($_POST['settingId']);
        // $userId = 2;
        $msg = updateUserSetting($db, $userId, $settingId);
        break;

    case 'get_setting_list':
        // $userId = 2;
        $msg = getSettingList($db, $userId);
        break;

    case 'get_setting_info':
        $settingId = $_POST['setting_id'];
        // $settingId = 6;
        $msg = getSettingInfo($db, $settingId);
        break;

    case 'get_setting':
        // $userId = 2;
        $msg = getSetting($db, $userId);
        break;

    case 'get_default_settingid':
        $msg = getDefaultSettingid($db, $userId);
        break;

    default:
        $msg = "Parameter Error";
        break;
}

echo json_encode($msg);
// var_dump($msg);

/**
 * 新增setting表设置
 *
 * @param [type] $db
 * @param [type] $userId
 * @param [type] $post
 * @return void
 */
function addSetting($db, $userId, $post)
{
    $setting_name = $post['setting_name'];
    $product = $post['product'];
    $project = $post['project'];
    $build = $post['build'];
    $station = $post['station'];
    $status = $post['status'];

    if ($project && $product) {
        $verify_result = verifyProject($db, $product, $project);
        if ($verify_result == 0) {
            $msg = "error";
            return $msg;
        }
    }

    $db->beginTransaction();
    $setting_insert_sql = "INSERT into `setting` value('','$userId','$setting_name','$product','$project','$build','$station','$status')";

    try {
        $db->execSql($setting_insert_sql);
        $setting_insert_id = $db->lastInsertId();

        $user_updatesetting_sql = "UPDATE `user_list` set `setting_id` = '$setting_insert_id' where `id` = '$userId'";
        $db->execSql($user_updatesetting_sql);

        $db->commit();
        $msg = "success";
    } catch (Exception $e) {
        // $e->getMessage();
        $db->rollback();
        $msg = "fail";
    }
    return $msg;
}

/**
 * 删除Setting设置
 * 删除当前用户的所有setting数据
 *
 * @param [type] $db
 * @param [type] $userId
 * @param [type] $setting_id
 * @return void
 */
function deleteSetting($db, $userId)
{
    $db->beginTransaction();
    try {
        $setting_delete_sql = "DELETE FROM `setting` WHERE `user_id` ='$userId'";

        $userlist_update_sql = "UPDATE `user_list` set `setting_id`='0' WHERE `id` = '$userId'";

        $db->execSql($setting_delete_sql);
        $db->execSql($userlist_update_sql);

        $db->commit();
        $msg = "success";
    } catch (Exception $e) {

        $db->rollback();
        $msg = "fail";
    }
    return $msg;
}

function deleteSettingId($db, $settingId, $userId)
{
    $userDefaultSettingId = searchCol($db, $settingId, 'setting_id', $userId);
    // return $userDefaultSettingId;
    if (!$userDefaultSettingId) {
        $settingId_delete_sql = "DELETE FROM `setting` WHERE `id`='$settingId'";
        $settingId_delete_result = $db->execSql($settingId_delete_sql);
    } else {
        $userlist_update_sql = "UPDATE `user_list` set `setting_id`='0' WHERE `id` = '$userId'";
        $settingId_delete_sql = "DELETE FROM `setting` WHERE `id`='$settingId'";
        $db->execSql($userlist_update_sql);
        $settingId_delete_result = $db->execSql($settingId_delete_sql);
    }

    if ($settingId_delete_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}
/**
 * 更新Setting表
 *
 * @param [type] $db
 * @param [type] $post
 * @return void
 */
function updateSetting($db, $post)
{
    $settingId = $post['setting_id'];
    // $setting_name = $post['setting_name'];
    $product = $post['product'];
    $project = $post['project'];
    $build = $post['build'];
    $station = $post['station'];
    $status = $post['status'];

    $verify_result = verifyProject($db, $product, $project);

    if ($verify_result == 0) {
        $msg = "error";
        return $msg;
    }

    $setting_update_sql = "UPDATE `setting` SET `product_id` = '$product'  , `project_id` = '$project'  , `build_id` = '$build'  , `station_id` = '$station'  , `status_id` = '$status' where `id` = '$settingId'";

    $setting_update_result = $db->execSql($setting_update_sql);

    if (!$setting_update_result) {
        $msg = "fail";
    } else {
        $msg = "success";
    }
    return $msg;
}
/**
 * 修改用户的settingId
 *
 * @param [type] $db
 * @param [type] $userId
 * @param [type] $settingId
 * @return void
 */
function updateUserSetting($db, $userId, $settingId)
{
    $userSetting_update_sql = "UPDATE `user_list` SET `setting_id` = '$settingId' WHERE `id` = '$userId'";
    $userSetting_update_result = $db->execSql($userSetting_update_sql);

    if ($userSetting_update_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 获取当前用户拥有的设置
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getSettingList($db, $userId)
{
    // TODO 暂未考虑翻页
    $settinglist_select_sql = "SELECT `id`,`setting_name` FROM `setting` WHERE `user_id` = '$userId' ORDER BY `id` DESC";
    // $settinglist_count_select_sql = "SELECT count(id) as num FROM `setting` WHERE `user_id` = '$userId'";

    $settinglist_select_result = $db->query($settinglist_select_sql);
    if ($settinglist_select_result) {
        $msg = array(
            "code" => 0,
            "msg" => "",
            "count" => "",
            "data" => $settinglist_select_result,
        );
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 当前设置的详细信息
 *
 * @param [type] $db
 * @param [type] $settingId
 * @return void
 */
function getSettingInfo($db, $settingId)
{
    $info_select_sql = "SELECT `product_id`,`project_id`,`build_id`,`station_id`,`status_id` FROM `setting` where `id`= '$settingId' ";

    $info_select_result = $db->query($info_select_sql);

    if (!$info_select_result) {
        $msg = "fail";
        return $msg;
    }

    $product = $info_select_result[0]['product_id'];
    $project = $info_select_result[0]['project_id'];
    $build = $info_select_result[0]['build_id'];
    $station = $info_select_result[0]['station_id'];
    $status = $info_select_result[0]['status_id'];

    $product = fieldInfo($db, 'product', 'product', $product);
    $project = fieldInfo($db, 'project', 'project', $project);
    $build = fieldInfo($db, 'build', 'build', $build);
    $station = fieldInfo($db, 'station', 'station', $station);
    $status = fieldInfo($db, 'status', 'status', $status);

    $result_array = array(
        "product" => $product,
        "project" => $project,
        "build" => $build,
        "station" => $station,
        "status" => $status,
    );
    return $result_array;
}

/**
 * 获取有哪些配置选项
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getSetting($db, $userId)
{
    $setting_select_sql = "SELECT `product_id` from `user_list` where `id`='$userId'";

    $setting_select_result = $db->query($setting_select_sql);

    if ($setting_select_result) {
        $product = explode("|", $setting_select_result[0]['product_id']);


              $user_name=$_SESSION['cooper_user_info'][0]['username'];
              $user_id=$_SESSION['cooper_user_info'][0]['id'];
              $user_project=$_SESSION['cooper_user_info'][0]['project_id'];

              if($user_project==""||$user_project==NULL){

                  $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '17'";
                  $project_select_result = $db->query($project_select_sql);
                   if ($project_select_result) {
                    for ($j = 0; $j < count($project_select_result); $j++) {
                    $project_info_sql = "SELECT `project` from `project` where `enable` = '1' and `id` = '" . $project_select_result[$j]['project_id'] . "'";
                    $project_info_result = $db->query($project_info_sql);
                    $temp['project'][] = array(
                        "id" => $project_select_result[$j]['project_id'],
                        "project" => $project_info_result[0]['project'],
                      );
                   }
                 }
              }else{
                $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '17'";
                $project_select_result = $db->query($project_select_sql);
                $project_id = explode("|", $user_project);
                for ($j = 0; $j < count($project_select_result); $j++) {
                    for ($k = 0; $k < count($project_id); $k++) {
                        if($project_select_result[$j]['project_id'] == $project_id[$k])
                            $project_id1[] = $project_id[$k];
                    }
                }

                if(count($project_id1)!=0){
                    for ($j = 0; $j < count($project_id1); $j++) {
                      $project_info_sql = "SELECT `project` from `project` where `enable` = '1' and `id` = '" . $project_id1[$j] . "'";
                      $project_info_result = $db->query($project_info_sql);
                      $temp['project'][] = array(
                        "id" => $project_id1[$j],
                        "project" => $project_info_result[0]['project'],
                    );}
                }else{
                    $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '17'";
                    $project_select_result = $db->query($project_select_sql);
                    if ($project_select_result) {
                    for ($j = 0; $j < count($project_select_result); $j++) {
                    $project_info_sql = "SELECT `project` from `project` where `enable` = '1' and `id` = '" . $project_select_result[$j]['project_id'] . "'";
                    $project_info_result = $db->query($project_info_sql);
                    $temp['project'][] = array(
                        "id" => $project_select_result[$j]['project_id'],
                        "project" => $project_info_result[0]['project'],
                      );
                   }
                 }
                }
              }
 





        

    
            // $project_select_sql = "SELECT `project_id` from `product_project` where `product_id` = '$product[$i]'";

            $product_info_sql = "SELECT `product` from `product` where `enable` = '1' and `id` = '17' ";
            $product_info_result = $db->query($product_info_sql);
            // $project_select_result = $db->query($project_select_sql);

            // if ($project_select_result) {
            //     for ($j = 0; $j < count($project_select_result); $j++) {
            //         $project_info_sql = "SELECT `project` from `project` where `id` = '" . $project_select_result[$j]['project_id'] . "'";
            //         $project_info_result = $db->query($project_info_sql);
            //         // $temp['project']['id'][] = $project_select_result[$j]['project_id'];
            //         // $temp['project']['project'][] = $project_info_result[0]['project'];
            //         $temp['project'][] = array(
            //             "id" => $project_select_result[$j]['project_id'],
            //             "project" => $project_info_result[0]['project'],
            //         );
            //     }
            // }
            // $temp['product']['id'][] = $product[$i];
            // $temp['product']['product'][] = $product_info_result[0]['product'];

            $temp['product'][] = array(
                "id" => 17,
                "product" => $product_info_result[0]['product'],
            );


        for ($i = 0; $i < count($temp['project']); $i++) {
            $stationProject_select_sql = "SELECT `station` FROM `station_project` WHERE `enable` = '1' and `project` = '" . $temp['project'][$i] . "' ";
            $stationProject_select_result = $db->query($stationProject_select_sql);


            if ($stationProject_select_result) {
                for ($j = 0; $j < count($stationProject_select_result); $j++) {
                    $station_select_sql = "SELECT `station` FROM `station` WHERE `id` = '" . $stationProject_select_result[$j]['station'] . "' and `enable` = '1' ";
                    $station_select_result = $db->query($station_select_sql);
                    $temp['station'][] = array(
                        'id' => $stationProject_select_result[$j]['station'],
                        'station' => $station_select_result[0]['station'],
                    );
                }
            }
        }

        $pro_id = '';
        for ($i = 0; $i < count($temp['project']); $i++) {
            if($i>0 && $i<count($temp['project']))
                $pro_id .= " or ";
            $pro_id .= " `project_id` = '" . $temp['project'][$i]['id'] . "' ";
        }



        $build_select_sql = "SELECT `id`,`build` FROM `build` WHERE `enable`='1' and id in (SELECT `build_id` FROM `build_project` WHERE `enable` = '1' and ($pro_id))";
        $status_select_sql = "SELECT `id`,`status` FROM `status`";


        $build_select_result = $db->query($build_select_sql);
        $status_select_result = $db->query($status_select_sql);


        $temp['build'] = $build_select_result;
        $temp['status'] = $status_select_result;

        $msg = $temp;
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 获取当前用户默认的setting
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getDefaultSettingid($db, $userId)
{
    // $setting_select_sql = "SELECT `setting_id` FROM `user_list` WHERE `id`='$userId'";
    $setting_select_sql = "SELECT `id` FROM `setting` WHERE `user_id`='$userId' and product_id = 17";
    $setting_select_result = $db->query($setting_select_sql);
    if($setting_select_result)
        return $setting_select_result[0]['id'];
    else
        return 0;
}
/**
 * 验证project设置选项是否合法
 *
 * @param [type] $db
 * @param [type] $product
 * @param [type] $project
 * @return void
 */
function verifyProject($db, $product, $project)
{
    $product = explode('|', $product);
    $project = explode('|', $project);

    for ($i = 0; $i < count($project); $i++) {
        $verify_sql = "SELECT count(id) as num FROM `product_project` where `enable` = '1' and ";
        $temp_sql = joinProduct($product);

        $verify_sql .= $temp_sql;

        $joinproject_sql = " and `project_id` = '$project[$i]' ";

        $verify_sql .= $joinproject_sql;

        $verify_result = $db->query($verify_sql);

        // return $verify_result;

        if (!$verify_result[0]['num']) {
            return 0;
        }
    }
    return 1;
}

/**
 * 拼接product字段的where段
 *
 * @param [type] $product
 * @return void
 */
function joinProduct($product)
{
    $str = " ";
    for ($i = 0; $i < count($product); $i++) {
        if ($i == 0) {
            $str .= " `product_id` = '$product[$i]' ";
        } else {
            $str .= " or `product_id` = '$product[$i]' ";
        }
    }
    return $str;
}

/**
 * 拼接多选框的数据
 *
 * @param array $post
 * @return void
 */
function joinField($post)
{
    foreach ($post as $key => $value) {
        if (!is_array($post[$key])) {
            $b[$key] = trim($post[$key]);
            continue;
        }else if(is_numeric($value[0])){
            $b[$key] = implode('|',$value);
            continue;
        }
        $temp = array_keys($post[$key]);
        $a = implode('|', $temp);
        $b[$key] = $a;
    }
    return $b;
}

/**
 * 字段数据切割 查询对应的详细数据
 *
 * @param object $db     数据库连接句柄
 * @param string $table  表名
 * @param string $field  字段名称
 * @param string $data   1|2|3 ...
 * @return void
 */
function fieldInfo($db, $table, $field, $data)
{
    $data = explode('|', $data);
    for ($i = 0; $i < count($data); $i++) {
        $data_select_sql = "SELECT `$field` FROM `$table` WHERE `id`='$data[$i]'";
        $data_select_result = $db->query($data_select_sql);
        if ($data_select_result) {
            $result_data['id'][] = $data[$i];
            $result_data[$field][] = $data_select_result[0][$field];
        }
    }
    return $result_data;
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

    $col_select_result = $db->query($col_select_sql);

    // $col_select_result 查找到数据时 返回success
    if ($col_select_result[0]['num']) {
        $msg = 1;
    } else {
        $msg = 0;
    }
    return $msg;
}
