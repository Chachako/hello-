<?php

/*
 * @Author: moxuan
 * @Date: 2019-03-11 09:55:23
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-15 16:23:36
 */

/**
 * filter_handle 相关接口数据处理
 */
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../../index.html");
}

$userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {
    case 'add_filter':
        $post = joinField($_POST);
        $msg = addfilter($db, $userId, $post);
        break;

    case 'del_filter':
        $msg = deletefilter($db, $userId);
        break;

    case 'del_filterId':
        $filter_id = trim($_POST['filter_id']);
        $msg = deleteFilterId($db, $filter_id);
        break;

    case 'update_filter':
        $filterId = trim($_POST['id']);
       
        $post = joinField($_POST['data']);
        $msg = updatefilter($db, $filterId, $post);
        break;
    case 'get_filter_info':

        $filterId = trim($_POST['filter_id']);
        $msg = getfilterInfo($db, $filterId);
        break;
  
    case 'get_filter':
        $filterId = trim($_POST['filter_id']);
        $msg = getfilter($db, $filterId);
        break;
    case 'get_project':
        $msg = getproject($db, $userId);
        break;
    case 'get_station':
        $msg = getstation($db, $userId);
        break;
    case 'get_buildstation':
        $msg = getbuildstation($db, $userId);
        break;

    default:
        $msg = "Parameter Error";
        break;
}

echo json_encode($msg);

/**
 * 新增filter表设置
 *
 * @param [type] $db
 * @param [type] $userId
 * @param [type] $post
 * @return void
 */
function addfilter($db, $userId, $post)
{
    $filter_name = $post['filter_name'];
   
    $product = $post['productid'];
    $project = $post['projectid'];
    $build = $post['build'];
    $station = $post['station'];
    $status = $post['status'];
    $start_time = $post['start_time'];
    $end_time = $post['end_time'];

    if ($product && $project) {
        $verify_result = verifyProject($db, $product, $project);

        if ($verify_result == 0) {
            $msg = "error";
            return $msg;
        }
    }

    if ($start_time == $end_time && $start_time) {
        $end_time .= " 23:59:59";
    }

    $db->beginTransaction();
    // $filter_insert_sql = "INSERT into `filter` value('','$userId','$filter_name','$product','$project','$build','$station','$status','$start_time','$end_time')";

     $filter_insert_sql = "INSERT into `filter` (`user_id`,`filter_name`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time`)  values('$userId','$filter_name','$product','$project','$build','$station','$status','$start_time','$end_time')";
    try {
        $db->execSql($filter_insert_sql);
        $db->commit();
        $msg = "success";
    } catch (Exception $e) {
        $db->rollback();
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
 * 根据filterId 删除filter设置
 *
 * @param [type] $db
 * @param [type] $filterId
 * @return void
 */
function deleteFilterId($db, $filterId)
{
    $filterId_delete_sql = "DELETE FROM `filter` WHERE `id`='$filterId'";
    $filterId_deleter_result = $db->execSql($filterId_delete_sql);
    if ($filterId_deleter_result) {
        $msg = "success";
    } else {
        $msg = "fail";
    }
    return $msg;
}

/**
 * 更新filter表
 *
 * @param [type] $db
 * @param [type] $post
 * @return void
 */
function updatefilter($db, $filterId, $post)
{
    // var_dump($post);
    $filterId = $_POST['id'];
    
    $product = $post['productid'];
    $project = $post['projectid'];
    $build = $post['build'];
    $station = $post['station'];
    $status = $post['status'];
    $start_time = $post['start_time'];
    $end_time = $post['end_time'];

    if ($project && $product) {
        $verify_result = verifyProject($db, $product, $project);
        if ($verify_result == 0) {
            $msg = "error";
            return $msg;
        }
    }

    if ($start_time == $end_time && $start_time) {
        $end_time .= " 23:59:59";
    }
    
    $filter_update_sql = "UPDATE `filter` SET `product_id` = '$product'  , `project_id` = '$project'  , `build_id` = '$build'  , `station_id` = '$station'  , `status_id` = '$status' , `start_time` = '$start_time' , `end_time` ='$end_time' where `id` = '$filterId'";

    $filter_update_result = $db->execSql($filter_update_sql);

    if ($filter_update_result) {
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
function getFilterList($db, $filterId){
//  var_dump($userId);die;
    // TODO 暂未考虑翻页
    $filterlist_select_sql = "SELECT `id`,`filter_name` FROM `filter` WHERE `id` = '$filterId' ";

    $filterlist_select_result = $db->query($filterlist_select_sql);
    // var_dump( $filterlist_select_result);die;
    if ($filterlist_select_result) {
        $msg = array(
            "code" => 0,
            "msg" => "",
            "count" => "",
            "data" => $filterlist_select_result,
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
 * @param [type] $filterId
 * @return void
 */
function getfilterInfo($db, $filterId)
{
    $info_select_sql = "SELECT `product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` FROM `filter` where `id`= '$filterId'";
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
    $start_time = $info_select_result[0]['start_time'];
    $end_time = $info_select_result[0]['end_time'];

    $product = fieldInfo($db, 'product', 'product', $product);
    $project = fieldInfo($db, 'project', 'project', $project);
    $build = fieldInfo($db, 'build', 'build', $build);
    $station = fieldInfo($db, 'station', 'station', $station);
    $status = fieldInfo($db, 'status', 'status', $status);

    $product_info_sql = "SELECT `id`,`product` from `product` where `enable` = '1'";
  
    $product_info_result = $db->query($product_info_sql);
   
    $msg['product'] = $product_info_result;

    $temp = $product['id'][0];
    $project_select_sql = "select b.id,b.`project` from `product_project` as a left join `project` as b on a.`project_id`=b.`id` where a.`enable` = '1' and a.`product_id` = '".$temp."'";
    $project_select_result = $db->query($project_select_sql);
    $msg['project'] = $project_select_result;

    $where = "";
    $projectid = $project['id'];
    for($i=0;$i<count($projectid);$i++){
        if (count($projectid) > 1) {
            if ($i == 0) {
                $where .= " and ( a.`project_id` = '$projectid[$i]' ";
            } else if ($i == count($projectid) - 1) {
                $where .= " or a.`project_id` = '$projectid[$i]' ) ";
            } else {
                $where .= " or a.`project_id` = '$projectid[$i]' ";
            }
        } else {
            $where .= " and a.`project_id` = '$projectid[$i]' ";
        }
    }
    $build_select_sql = "select distinct(a.`build_id`) as id,b.`build` from `build_project` as a left join `build` as b on a.`build_id`=b.`id` where a.`enable` = '1' $where order by b.sort";
    $build_select_result = $db->query($build_select_sql);
    $msg['build'] = $build_select_result;
    $where = "";
    for($i=0;$i<count($projectid);$i++){
        if (count($projectid) > 1) {
            if ($i == 0) {
                $where .= " and ( a.`project` = '$projectid[$i]' ";
            } else if ($i == count($projectid) - 1) {
                $where .= " or a.`project` = '$projectid[$i]' ) ";
            } else {
                $where .= " or a.`project` = '$projectid[$i]' ";
            }
        } else {
            $where .= " and a.`project` = '$projectid[$i]' ";
        }
    }

    $station_select_sql = "select distinct(a.`station`) as id,b.`station` from `station_project` as a left join `station` as b on a.`station`=b.`id` where a.`enable` = '1' $where";
    $station_select_result = $db->query($station_select_sql);

    $msg['station'] = $station_select_result;

    $msg['data'] = array(
        "product" => $product,
        "project" => $project,
        "build" => $build,
        "station" => $station,
        "status" => $status,
        "start_time" => $start_time,
        "end_time" => $end_time,
    );
    return $msg;
}
/**
 * 获取有哪些配置选项
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getfilter($db, $filterId)
{  
    $product_info_sql = "SELECT `id`,`product` from `product` where `enable` = '1'";
    $product_info_result = $db->query($product_info_sql);
    $temp['product'] = $product_info_result;

    $project_select_sql = "select b.id,b.`project` from `product_project` as a left join `project` as b on a.`project_id`=b.`id` where a.`enable` = '1' and a.`product_id` = '".$temp['product'][0]['id']."'";
    $project_select_result = $db->query($project_select_sql);
    $temp['project'] = $project_select_result;
    
    $status_select_sql = "SELECT `id`,`status` FROM `status`";
    $status_select_result = $db->query($status_select_sql);

    $temp['status'] = $status_select_result;

    $build_select_sql="select b.id,b.`build` from `build_project` as a left join `build` as b on a.`build_id`=b.`id` where a.`enable` = '1' and a.`project_id` = '".$temp['project'][0]['id']."'";
    $build_select_result = $db->query($build_select_sql);
    $temp['build'] = $build_select_result;

    $station_select_sql="select b.id,b.`station` from `station_project` as a left join `station` as b on a.`station`=b.`id` where a.`enable` = '1' and a.`project` = '".$temp['project'][0]['id']."'";
    $station_select_result = $db->query($station_select_sql);
    $temp['station'] = $station_select_result;
    $msg = $temp;
    return $msg;
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
        if (!is_array($value)) {
            $b[$key] = trim($value);
            continue;
        } elseif (is_numeric($value[0])) {
            $b[$key] = implode('|', $value);
            continue;
        }
        $temp = array_keys($value);
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
 * 数组去重
 *
 * @param [type] $array2D
 * @param [type] $field
 * @return void
 */
function arrayUnique($array2D)
{
    foreach ($array2D as $key => $value) {
        $valuekey = array_keys($value);
        $arr[] = implode('|', $value);
    }
    $arr1 = array_unique($arr);
    foreach ($arr1 as $keys => $values) {
        $temps[] = explode("|", $values);
    }
    for ($i = 0; $i < count($temps); $i++) {
        $temp[] = array(
            "$valuekey[0]" => $temps[$i][0],
            "$valuekey[1]" => $temps[$i][1],
        );
    }
    return $temp;
}

/**
 * 获取有哪些配置选项
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getproject($db, $userId)
{
    $temp = $_GET['depId'];
    $project_select_sql = "select b.id,b.`project` from `product_project` as a left join `project` as b on a.`project_id`=b.`id` where a.`enable` = '1' and a.`product_id` = '".$temp."'";
    $project_select_result = $db->query($project_select_sql);
    $msg = $project_select_result;
    
    return $msg;
}

function getstation($db, $userId)
{   
    $temp = $_GET['depId'];
    $project_select_sql = "select b.id,b.`project` from `product_project` as a left join `project` as b on a.`project_id`=b.`id` where a.`enable` = '1' and a.`product_id` = '".$temp."'";
    $project_select_result = $db->query($project_select_sql);
    $where = " ";
    for($i=0;$i<count( $project_select_result);$i++){
        if (count( $project_select_result) > 1) {
            if ($i == 0) {
                $project_id=$project_select_result[$i]['id'];
                $where .= "  and ( c.`project` = '$project_id' ";
            } else if ($i == count($project_select_result) - 1) {
                $project_id=$project_select_result[$i]['id'];
                $where .= " or c.`project` = ' $project_id' ) ";
            } else {
                $project_id=$project_select_result[$i]['id'];
                $where .= " or c.`project` = '$project_id' ";
            }
        } else {
            $project_id=$project_select_result[$i]['id'];
            $where .= " and c.`project` = ' $project_id' ";
        }
        
    }
    $station_select_sql = "select distinct(c.`station`) as id,d.`station` from `station_project` as c left join `station` as d on c.`station`=d.`id` where c.`enable` = '1' $where";
  $station_select_result = $db->query($station_select_sql);
    $msg['station'] = $station_select_result;
    return $msg;
    //  $a=array();
    // for($i=0;$i<count($project_select_result);$i++){
    // $project_id= $project_select_result[$i]['id'];
    // $station_select_sql = "select `station` from `station_project` where project='$project_id' and enable='1'";
    // $station_select_sql_result = $db->query($station_select_sql);
    //  for($j=0;$j<count($station_select_sql_result);$j++){
    //   $station_id=$station_select_sql_result[$j]['station'];
    //    $sql="select station from  station where id=' $station_id'";
    //    $sql_result=$db->query($sql);
    //    $station_name=$sql_result[0]['station'];
    //   array_push($a, $station_name);
    
      
    //  }
     
    // }
    // var_dump($a);
    
    // return $a;
 }
/**
 * 获取有哪些配置选项
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getbuildstation($db, $filterId)
{
    $project_id = explode("|", $_GET['stageId']);
    $where = "";
    for($i=0;$i<count($project_id);$i++){
        if (count($project_id) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
            } else if ($i == count($project_id) - 1) {
                $where .= " or a.`project_id` = '$project_id[$i]' ) ";
            } else {
                $where .= " or a.`project_id` = '$project_id[$i]' ";
            }
        } else {
            $where .= " and a.`project_id` = '$project_id[$i]' ";
        }
    }

    $project_select_sql = "select distinct(a.`build_id`) as id,b.`build` from `build_project` as a left join `build` as b on a.`build_id`=b.`id` where a.`enable` = '1' $where order by b.sort";
    $project_select_result = $db->query($project_select_sql);

    $msg['build'] = $project_select_result;

    $where = "";
    for($i=0;$i<count($project_id);$i++){
        if (count($project_id) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`project` = '$project_id[$i]' ";
            } else if ($i == count($project_id) - 1) {
                $where .= " or a.`project` = '$project_id[$i]' ) ";
            } else {
                $where .= " or a.`project` = '$project_id[$i]' ";
            }
        } else {
            $where .= " and a.`project` = '$project_id[$i]' ";
        }
    }

    $station_select_sql = "select distinct(a.`station`) as id,b.`station` from `station_project` as a left join `station` as b on a.`station`=b.`id` where a.`enable` = '1' $where ";
    $station_select_result = $db->query($station_select_sql);
    $msg['station'] = $station_select_result;
    
    return $msg;

}