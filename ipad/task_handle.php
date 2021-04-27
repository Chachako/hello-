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

$userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../Include/db_connect.php";
$action = $_GET['action'];

switch ($action) {
    case 'task_all_list':
        $choose=$_GET['choose'];
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $post = array(
             'userId' => $userId,
            );
        $msg = taskAllListInit($db, $post, $choose,$depId,$stageId,$stationId);
        break;
    case 'task_all_list1':
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
        $choose = trim($_POST['choose']);
        $field = trim($_POST['field']);
        $order = trim($_POST['order']);
        $page = ($page - 1) * $limit;
        $post = array(
            'page' => $page,
            'limit' => $limit,
            'field' => $field,
            'order' => $order,
            'userId' => $userId,
        );
        $msg = taskAllListInit1($db, $post, $choose);
        break;
    case 'task_my_list':
        $choose=$_GET['choose'];
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $post = array(
            'userId' => $userId,
        );
        $msg = taskMYListInit($db, $post, $choose,$depId,$stageId,$stationId);
        break;
    case 'task_filter_list':
        $depId = trim($_GET['depId']);
        $filterId= trim($_GET['filterId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $post = array(
            'userId' => $userId,
        );
        $msg = taskFilterList($db, $post, $filterId, $depId,$stageId,$stationId);
        break;

    case 'get_stage_station_list':
        $msg = getStageStationlist($db, $userId);
        break;

    case 'get_group_list':
        $msg = getGrouplist($db, $userId);
        break;

    case 'task_filter':
        $post = joinField($_POST);
        $msg = taskFilter($db, $post, $userId);
        break;

    default:
        $msg = "Parameter Error";
        break;
}

echo json_encode($msg);

/**
 * taskmyList 默认列表
 *
 * @param [type] $db
 * @param [type] $post
 * @param string $choose 选择setting 或 filter
 * @return void
 */
function taskMyListInit($db, $post, $choose,$depId,$stageId,$stationId)
{   
    $userId = $post['userId'];
       $setting_select_result = chooseTable($db, $userId, $choose);
       $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`eta`,a.`version`,a.`new_status`,a.`cancel_user`,a.`done_user`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,u.`username` ,us.`username` as `toUser`,a.`priority` , ul.`username` as requestor  from team_ipad.issue_list as a
       left join `product` as b on a.`product_id` = b.`id`
       left join `project` as c on a.`project_id` = c.`id`
       left join `build` as d on a.`build_id` = d.`id`
       left join `station` as e on a.`station_id` = e.`id`
       left join `status` as f on a.`status_id` = f.`id`   
       left join `user_list` as u on  a.`create_user_id` = u.`id`
       left join `user_list` as us on  a.`check_user` =us.`id`
       left join `user_list` as ul on  a.`requestor_id`= ul.`id`
       ";
       $task_count_select_sql = "SELECT count(a.`id`) as num from team_ipad.issue_list as a";
       $task_count_select_result = $db->query($task_count_select_sql);
       $limit_sql = " ORDER BY a.`id` DESC ";
       $temp_sql = ' where 1 ';
   
       if (null != $setting_select_result[0]['id']) {
           $setting_data = $setting_select_result[0];
   
           //TODO 产品和专案 未做对应表连接
           if ('' != $setting_data['product_id']) {
               $product = explode("|", $setting_data['product_id']);
               $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
           } else {
               $product_result = searchProduct($db, $userId);
               $product = explode("|", $product_result[0]['product_id']);
               $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
           }
   
           if ('' != $setting_data['project_id']) {
               $project = explode("|", $setting_data['project_id']);
               $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
           }
   
           if ('' != $setting_data['build_id']) {
               $build = explode('|', $setting_data['build_id']);
               $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
           }
   
           if ('' != $setting_data['station_id']) {
               $station = explode("|", $setting_data['station_id']);
               $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
           }
   
           if ('' != $setting_data['status_id']) {
               $status = explode('|', $setting_data['status_id']);
               $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
           }
       } else {
           $product_result = searchProduct($db, $userId);
           $product = explode("|", $product_result[0]['product_id']);
           $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');

           //到产品
           $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
           $project_result = $db->query($project_select_sql);
   
           if ($project_result[0]['project_id']!=null&& $project_result[0]['project_id']!='') {
               $project = explode("|", $project_result[0]['project_id']);
               // var_dump($project);
               $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
           }
           // 只允许查看所拥有的产品权限
       }
       $stage_sql="select id from `user_build` where `user_id`='$userId' and `product_id`='14'";
       $stage_task=$db->query($stage_sql);
       if($stage_task){
           $stage_id = $stage_task[0]['id'];
           $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
           $stage_update_task=$db->execSql($stage_update);
       }else{
           $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$userId','14','$stageId')";
           $stage_insert_task = $db->execSql($stage_insert);
       }
   
       $station_sql="select id from `user_station` where `user_id`='$userId' and `product_id`='14'";
       $station_task=$db->query($station_sql);
       if($station_task){
           $station_id = $station_task[0]['id'];
           $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
           $station_update_task=$db->execSql($station_update);
       }else{
           $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$userId','14','$stationId')";
           $station_insert_task = $db->execSql($station_insert);
       }

       $stageId=explode('|', $stageId);
       for ($i = 0; $i < count($stageId); $i++) {
           $stageIdname=explode('-', $stageId[$i]);
           if (count($stageId) > 1) {
               if ($i == 0) {
                   $temp_sql .= "  and ( (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
               } else if ($i == count($stageId) - 1) {
                   $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ) ";
               } else {
                   $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
               }
           } else {
               if($stageIdname[0]!='' && $stageIdname[1]!='')
                   $temp_sql .= " and (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
           }
       }
   
       $stationId=explode('|', $stationId);
       for ($i = 0; $i < count($stationId); $i++) {
           if (count($stationId) > 1) {
               if ($i == 0) {
                   $temp_sql .= "  and ( a.`station_id` = '$stationId[$i]' ";
               } else if ($i == count($stationId) - 1) {
                   $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ) ";
               } else {
                   $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ";
               }
           } else {
               if($stationId[$i]!='')
                   $temp_sql .= " and  a.`station_id` = '$stationId[$i]' ";
           }
       }
       $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='14'";
       $group_task=$db->query($group_sql);
       if ($group_task) {
           $depId = $group_task[0]['group_id'];
           if ($depId!=0&&$depId!='') {
               $temp_sql .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
   select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
   select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";
           }
       }
       $temp_sql.=" and `create_user_id`='$userId'";
       $task_select_sql .= $temp_sql;
       $task_count_select_sql .= $temp_sql;
    //    $task_select_sql .=" and `create_user_id`='$userId'";
       // sql 语句拼接
       $task_select_sql .= $limit_sql;
       $task_select_result = $db->query($task_select_sql);
 
       $task_count=count($task_select_result);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task_select_result[$i]['cancel_content'])) {
             $task_select_result[$i]['new_status'] =htmlspecialchars($task_select_result[$i]['cancel_content']);
         }
         if(!empty($task_select_result[$i]['cancel_user'])){
              $task_select_result[$i]['toUser'] = $task_select_result[$i]['cancel_user'];
         }
         if(!empty($task_select_result[$i]['done_user'])){
              $task_select_result[$i]['toUser'] = $task_select_result[$i]['done_user'];
         }
     }
       $task_count_select_result = $db->query($task_count_select_sql);
       $task_result = array(
        "code" => 0,
        "msg" => "success",
        "count" => $task_count_select_result[0]['num'],
        "data" =>  $task_select_result,
    );
       return $task_result;
}
function taskAllListInit1($db, $post, $choose)
{
    $userId = $post['userId'];
    $page = $post['page'];
    $limit = $post['limit'];
    $field = $post['field'];
    $order = $post['order'];
    $setting_select_result = chooseTable($db, $userId, $choose);

   $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`eta`,a.`cancel_user`,a.`done_user`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,u.`username` ,us.`username` as `toUser`,a.`priority` , ul.`username` as requestor  from team_ipad.issue_list as a
    left join `product` as b on a.`product_id` = b.`id`
    left join `project` as c on a.`project_id` = c.`id`
    left join `build` as d on a.`build_id` = d.`id`
    left join `station` as e on a.`station_id` = e.`id`
    left join `status` as f on a.`status_id` = f.`id`   
    left join `user_list` as u on  a.`create_user_id` = u.`id`
    left join `user_list` as us on  a.`check_user` =us.`id`
    left join `user_list` as ul on  a.`requestor_id`= ul.`id`
    ";




    $task_count_select_sql = "SELECT count(a.`id`) as num from team_ipad.issue_list as a";

    if(''!=$field && ''!=$order)
        $limit_sql = " order by $field $order limit $page,$limit";
    else
        $limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";

    $temp_sql = ' where 1 ';

    if (null != $setting_select_result[0]['id']) {

        $setting_data = $setting_select_result[0];

        //TODO 产品和专案 未做对应表连接
        if ('' != $setting_data['product_id']) {
            $product = explode("|", $setting_data['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        } else {
            $product_result = searchProduct($db, $userId);
            $product = explode("|", $product_result[0]['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        }

        if ('' != $setting_data['project_id']) {
            $project = explode("|", $setting_data['project_id']);
            $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
        }

        if ('' != $setting_data['build_id']) {
            $build = explode('|', $setting_data['build_id']);
            $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
        }

        if ('' != $setting_data['station_id']) {
            $station = explode("|", $setting_data['station_id']);
            $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
        }

        if ('' != $setting_data['status_id']) {
            $status = explode('|', $setting_data['status_id']);
            $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
        }

    } else {
        
        $product_result = searchProduct($db, $userId);
        $product = explode("|", $product_result[0]['product_id']);
        $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');

       
        //到产品
        $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
        $project_result = $db->query($project_select_sql);

        if($project_result[0]['project_id']!=NUll&& $project_result[0]['project_id']!=''){
         $project = explode("|", $project_result[0]['project_id']);
         // var_dump($project);
         $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');

        }
        // 只允许查看所拥有的产品权限
    }

    $stage_sql="select build_id from `user_build` where `user_id`='$userId' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stageId=$stage_task[0]['build_id'];
        $stageId=explode('|', $stageId);
        for ($i = 0; $i < count($stageId); $i++) {
            $stageIdname=explode('-', $stageId[$i]);
            if (count($stageId) > 1) {
                if ($i == 0) {
                    $temp_sql .= "  and ( (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
                } else if ($i == count($stageId) - 1) {
                    $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ) ";
                } else {
                    $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
                }
            } else {
                if($stageIdname[0]!='' && $stageIdname[1]!='')
                    $temp_sql .= " and (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            }
        }
    }

    $station_sql="select station_id from `user_station` where `user_id`='$userId' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $stationId=$station_task[0]['station_id'];
        $stationId=explode('|', $stationId);
        for ($i = 0; $i < count($stationId); $i++) {
            if (count($stationId) > 1) {
                if ($i == 0) {
                    $temp_sql .= "  and ( a.`station_id` = '$stationId[$i]' ";
                } else if ($i == count($stationId) - 1) {
                    $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ) ";
                } else {
                    $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ";
                }
            } else {
                if($stationId[$i]!='')
                    $temp_sql .= " and  a.`station_id` = '$stationId[$i]' ";
            }
        }
    }

    $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='14'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $depId = $group_task[0]['group_id'];
        if($depId!=0&&$depId!='')
            $temp_sql .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";
    }

    $task_select_sql .= $temp_sql;
    $task_count_select_sql .= $temp_sql;

    // sql 语句拼接
    $task_select_sql .= $limit_sql;
    $task_select_result = $db->query($task_select_sql);
    for ($i = 0; $i < count($task_select_result); $i++) {
        $task_select_result[$i]['new_status'] = '';

        $history_status_sql = "SELECT `status` FROM team_ipad.history WHERE `issue_list_id` = '" . $task_select_result[$i]['id'] . "' ORDER BY `id` DESC";

        $history_status_result = $db->query($history_status_sql);
        for ($j = 0; $j < count($history_status_result); $j++) {
            if ($j == count($history_status_result) - 1) {
                $task_select_result[$i]['new_status'] = htmlspecialchars($history_status_result[$j]['status']);
            } else {
                if (!empty($history_status_result[$j]['status'])) {
                    $task_select_result[$i]['new_status'] = htmlspecialchars($history_status_result[$j]['status']);
                    break;
                }
            }
        }
        if (!empty($task_select_result[$i]['cancel_content'])) {
            $task_select_result[$i]['new_status'] =htmlspecialchars($task_select_result[$i]['cancel_content']);
        }

        if(!empty($task_select_result[$i]['cancel_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['cancel_user'];
        }
        if(!empty($task_select_result[$i]['done_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['done_user'];
        }

    }
    $task_count_select_result = $db->query($task_count_select_sql);

    // 数据返回接口格式
    $task_result = array(
        "code" => 0,
        "msg" => "",
        "count" => $task_count_select_result[0]['num'],
        "data" => $task_select_result,
    );

    return $task_result;
}
function taskAllListInit($db, $post, $choose,$depId,$stageId,$stationId)
{   
    $userId = $post['userId'];
    $setting_select_result = chooseTable($db, $userId, $choose);
    $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`new_status`,a.`eta`,a.`version`,a.`cancel_user`,a.`done_user`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,u.`username` ,us.`username` as `toUser`,a.`priority` , ul.`username` as requestor  from team_ipad.issue_list as a
    left join `product` as b on a.`product_id` = b.`id`
    left join `project` as c on a.`project_id` = c.`id`
    left join `build` as d on a.`build_id` = d.`id`
    left join `station` as e on a.`station_id` = e.`id`
    left join `status` as f on a.`status_id` = f.`id`   
    left join `user_list` as u on  a.`create_user_id` = u.`id`
    left join `user_list` as us on  a.`check_user` =us.`id`
    left join `user_list` as ul on  a.`requestor_id`= ul.`id`
    ";
    // $task_count_select_sql = "SELECT count(a.`id`) as num from team_ipad.issue_list as a";
    $limit_sql = " ORDER BY a.`id` DESC  ";
    $temp_sql = ' where 1 ';
    if (null != $setting_select_result[0]['id']) {
        $setting_data = $setting_select_result[0];
        //TODO 产品和专案 未做对应表连接
        if ('' != $setting_data['product_id']) {
            $product = explode("|", $setting_data['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        } else {
            $product_result = searchProduct($db, $userId);
            $product = explode("|", $product_result[0]['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        }

        if ('' != $setting_data['project_id']) {
            $project = explode("|", $setting_data['project_id']);
            $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
        }

        if ('' != $setting_data['build_id']) {
            $build = explode('|', $setting_data['build_id']);
            $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
        }

        if ('' != $setting_data['station_id']) {
            $station = explode("|", $setting_data['station_id']);
            $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
        }

        if ('' != $setting_data['status_id']) {
            $status = explode('|', $setting_data['status_id']);
            $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
        }

    } else {
        
        $product_result = searchProduct($db, $userId);
        $product = explode("|", $product_result[0]['product_id']);
        $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        //到产品
        $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
        $project_result = $db->query($project_select_sql);

        if($project_result[0]['project_id']!=NUll&& $project_result[0]['project_id']!=''){
         $project = explode("|", $project_result[0]['project_id']);
         // var_dump($project);
         $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');

        }
        // 只允许查看所拥有的产品权限
    }
    
    $stage_sql="select id from `user_build` where `user_id`='$userId' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$userId','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$userId' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$userId','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
    }

    $stageId=explode('|', $stageId);
    for ($i = 0; $i < count($stageId); $i++) {
        $stageIdname=explode('-', $stageId[$i]);
        if (count($stageId) > 1) {
            if ($i == 0) {
                $temp_sql .= "  and ( (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            } else if ($i == count($stageId) - 1) {
                $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ) ";
            } else {
                $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            }
        } else {
            if($stageIdname[0]!='' && $stageIdname[1]!='')
                $temp_sql .= " and (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
        }
    }

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $temp_sql .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $temp_sql .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='14'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $depId = $group_task[0]['group_id'];
        if($depId!=0&&$depId!='')
            $temp_sql .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
              select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
              select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";
    }
    $task_select_sql .= $temp_sql;
    $task_select_sql .= $limit_sql;
    $task_select_result = $db->query($task_select_sql);
     $task_count=count($task_select_result);
      for ($i = 0; $i <  $task_count; $i++) {
        if (!empty($task_select_result[$i]['cancel_content'])) {
            $task_select_result[$i]['new_status'] =htmlspecialchars($task_select_result[$i]['cancel_content']);
        }
        if(!empty($task_select_result[$i]['cancel_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['cancel_user'];
        }
        if(!empty($task_select_result[$i]['done_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['done_user'];
        }
    }
    // $task_count_select_result = $db->query($task_count_select_sql);
    // 数据返回接口格式
    if($task_select_result){
        $task_result = array(
            "code" => 0,
            "msg" => "success",
            "count" => count($task_select_result),
            "data" =>  $task_select_result,
        );
    }else{
        $task_result = array(
            "code" => 0,
            "msg" => "fail",
            "count" => count($task_select_result),
            "data" =>  $task_select_result,
        );
    }
   
    return $task_result;
}

/**
 * 使用filter时 获取返回结果
 *
 * @param Object $db
 * @param Array $post
 * @param Int $filterId
 * @return void
 */
function taskFilterList($db, $post, $filterId,  $depId,$stageId,$stationId)
{
    $userId = $post['userId'];
    $filter_select_result = filterTable($db, $filterId);
    // 详细信息查询
    $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`eta`,a.`cancel_user`,a.`new_status`,a.`done_user`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,a.`cancel_done_time`, 
    u.`username` ,us.`username` as `toUser`, a.`priority`, ul.`username` as requestor  from team_ipad.issue_list as a
    left join `product` as b on a.`product_id` = b.`id`
    left join `project` as c on a.`project_id` = c.`id`
    left join `build` as d on a.`build_id` = d.`id`
    left join `station` as e on a.`station_id` = e.`id`
    left join `status` as f on a.`status_id` = f.`id`
    left join `user_list` as u on  a.`create_user_id` = u.`id`
    left join `user_list` as us on  a.`check_user` =us.`id`
    left join `user_list` as ul on  a.`requestor_id`= ul.`id`
    ";

    // 获取当前详细信息条数
    $task_count_select_sql = "SELECT count(a.`id`) as num from team_ipad.issue_list as a";

    $temp_sql = ' where 1 ';
    $limit_sql='';
    // 对于详细信息进行分页处理  对ID进行倒序
        $limit_sql = " ORDER BY a.`id` DESC ";/*limit $page,$limit*/ 
    //$limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";
    if (null != $filter_select_result[0]['id']) {
        $filter_data = $filter_select_result[0];

        //TODO 产品和专案 未做对应表连接
        if ('' != $filter_data['product_id']) {
            $product = explode("|", $filter_data['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        } else {
            $product_result = searchProduct($db, $userId);
            $product = explode("|", $product_result[0]['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');

        }

        if ('' != $filter_data['project_id']) {
            $project = explode("|", $filter_data['project_id']);
            $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
        }

        if ('' != $filter_data['build_id']) {
            $build = explode('|', $filter_data['build_id']);
            $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
        }

        if ('' != $filter_data['station_id']) {
            $station = explode("|", $filter_data['station_id']);
            $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
        }

        if ('' != $filter_data['status_id']) {
            $status = explode('|', $filter_data['status_id']);
            $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
        }

        if (isset($filter_data['start_time'])) {
            $start_time = strtotime($filter_data['start_time']);
            $end_time = strtotime($filter_data['end_time']);

            if ($start_time || $end_time) {
                $temp_sql .= " and a.`create_time` between '" . $filter_data['start_time'] . "' and '" . $filter_data['end_time'] . "' ";
            }

        }
    } else {
        $product_result = searchProduct($db, $userId);
        $product = explode("|", $product_result[0]['product_id']);
        $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');

            //到产品
        $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
        $project_result = $db->query($project_select_sql);

        if($project_result[0]['project_id']!=NUll&& $project_result[0]['project_id']!=''){
         $project = explode("|", $project_result[0]['project_id']);
         // var_dump($project);
         $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');

        }

    }
    $stage_sql="select id from `user_build` where `user_id`='$userId' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$userId','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }
    $station_sql="select id from `user_station` where `user_id`='$userId' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$userId','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
    }

    $stageId=explode('|', $stageId);
    for ($i = 0; $i < count($stageId); $i++) {
        $stageIdname=explode('-', $stageId[$i]);
        if (count($stageId) > 1) {
            if ($i == 0) {
                $temp_sql .= "  and ( (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            } else if ($i == count($stageId) - 1) {
                $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ) ";
            } else {
                $temp_sql .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            }
        } else {
            if($stageIdname[0]!='' && $stageIdname[1]!='')
                $temp_sql .= " and (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
        }
    }

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $temp_sql .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $temp_sql .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $temp_sql .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $where="";
    $group_sql="select id from `user_group` where `user_id`='$userId' and `product_id`='14'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$userId','14','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $temp_sql .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

    $task_select_sql .= $temp_sql;
    $task_count_select_sql .= $temp_sql;

    // sql 语句拼接
    $task_select_sql .= $limit_sql;
    // $task_count_select_sql .= $limit_sql;
    $task_select_result = $db->query($task_select_sql);
    for ($i = 0; $i < count($task_select_result); $i++) {

        if (!empty($task_select_result[$i]['cancel_content'])) {
            $task_select_result[$i]['new_status'] =htmlspecialchars($task_select_result[$i]['cancel_content']) ;
        }

        if(!empty($task_select_result[$i]['cancel_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['cancel_user'];
        }
        if(!empty($task_select_result[$i]['done_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['done_user'];
        }
    }
    $task_count_select_result = $db->query($task_count_select_sql);

    // 数据返回接口格式
    $task_result = array(
        "code" => 0,
        "msg" => $task_select_sql,
        "count" => $task_count_select_result[0]['num'],
        "data" => $task_select_result,
    );

    return $task_result;

}

/**
 * 获取当前用户拥有的设置
 *
 * @param [type] $db
 * @param [type] $userId
 * @return void
 */
function getStageStationlist($db, $userId)
{
    // TODO 暂未考虑翻页
    $project_sql = "select project_id from `user_list` where `id` = '$userId'";
    $project_result = $db->query($project_sql);
    $where = "";

    $project_id = $project_result[0]['project_id'];
    $pro_id = explode("|", $project_id);
    for($i = 0; $i < count($pro_id); $i++){
        if(count($pro_id)>1){
            if($i == 0){
                $where .= " and (project_id = '".$pro_id[$i]."'";
            }else if($i == count($pro_id)-1){
                $where .= " or project_id = '".$pro_id[$i]."')";
            }else{
                $where .= " or project_id = '".$pro_id[$i]."'";
            }
        }else{
            if($pro_id[$i]!='')
                $where .= " and project_id = '".$pro_id[$i]."'";
        }
    }

    $pro_sql = "select project_id from `product_project` where `product_id` = '14' and `enable` = '1' $where";
    $pro_result = $db->query($pro_sql);
    $where1 = "";
    $where2 = "";
    for($i = 0; $i < count($pro_result); $i++){
        if(count($pro_result)>1){
            if($i == 0){
                $where1 .= " and (a.project_id = '".$pro_result[$i]['project_id']."'";
                $where2 .= " and (a.project = '".$pro_result[$i]['project_id']."'";
            }else if($i == count($pro_result)-1){
                $where1 .= " or a.project_id = '".$pro_result[$i]['project_id']."')";
                $where2 .= " or a.project = '".$pro_result[$i]['project_id']."')";
            }else{
                $where1 .= " or a.project_id = '".$pro_result[$i]['project_id']."'";
                $where2 .= " or a.project = '".$pro_result[$i]['project_id']."'";
            }
        }
        else{
            $where1 .= " and a.project_id = '".$pro_result[$i]['project_id']."'";
            $where2 .= " and a.project = '".$pro_result[$i]['project_id']."'";
        }
    }

    $user_build_sql = "select build_id from `user_build` where `user_id` = '$userId' and `product_id` = '14'";
    $user_build_result = $db->query($user_build_sql);

    $build_sql = "select a.project_id,b.project,a.build_id,c.build from `build_project`  as a left join `project` as b on a.`project_id` = b.`id` left join `build` as c on a.`build_id` = c.`id` where a.`enable` = '1' $where1 order by b.id asc,c.sort asc";
    $build_result = $db->query($build_sql);
    if($build_result){
        if($user_build_result){
            $user_build = explode("|", $user_build_result[0]['build_id']);
            for($j = 0; $j < count($build_result); $j++){
                $check = false;
                for($k = 0; $k < count($user_build); $k++){
                    $user_build_str = explode("-", $user_build[$k]);
                    if($build_result[$j]['project_id'] == $user_build_str[0]&&$build_result[$j]['build_id'] == $user_build_str[1])
                        $check = true;
                }
                $msg['stage'][] = array(
                    "name" => $build_result[$j]['project']."-".$build_result[$j]['build'],
                    "value" => $build_result[$j]['project_id']."-".$build_result[$j]['build_id'],
                    "selected" => $check,
                );
            }
        }else{
            for($j = 0; $j < count($build_result); $j++){
                $msg['stage'][] = array(
                    "name" => $build_result[$j]['project']."-".$build_result[$j]['build'],
                    "value" => $build_result[$j]['project_id']."-".$build_result[$j]['build_id'],
                );
            }
        }
    }

    $user_station_sql = "select station_id from `user_station` where `user_id` = '$userId' and `product_id` = '14'";
    $user_station_result = $db->query($user_station_sql);

    $station_sql = "select distinct(a.station) as station_id,c.station from `station_project`  as a  left join `station` as c on a.`station` = c.`id` where a.`enable` = '1' $where2";
    $station_result = $db->query($station_sql);
    if($station_result){
        if($user_build_result){
            $user_build = explode("|", $user_station_result[0]['station_id']);
            for($j = 0; $j < count($station_result); $j++){
                $check = false;
                for($k = 0; $k < count($user_build); $k++){
                    if($station_result[$j]['station_id'] == $user_build[$k])
                        $check = true;
                }
                $msg['station'][] = array(
                    "name" => $station_result[$j]['station'],
                    "value" => $station_result[$j]['station_id'],
                    "selected" => $check,
                );
            }
        }else{
            for($j = 0; $j < count($station_result); $j++){
                $msg['station'][] = array(
                    "name" => $station_result[$j]['station'],
                    "value" => $station_result[$j]['station_id'],
                );
            }
        }
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
function getGrouplist($db, $userId)
{
    // TODO 暂未考虑翻页
    $group_sql="select * from `group` where 1 and (`product_id` like '14' or `product_id` like '14|%' or `product_id` like '%|14|%' or `product_id` like '%|14') and `enable` = '1'";
    $group_res=$db->query($group_sql);
    $select_user_dep = "select * from user_group where `product_id` = '14' and `user_id` = '$userId'";
    $select_user_dep_result = $db->query($select_user_dep);

    if($select_user_dep_result){
        $user_dep_id = $select_user_dep_result[0]['group_id'];
        for($i = 0; $i < count($group_res); $i++){
            if($user_dep_id == $group_res[$i]['id'])
                $msg[] = array(
                    "id" => $group_res[$i]['id'],
                    "group" => $group_res[$i]['group'],
                    "checked" => 1,
                );
            else
                $msg[] = array(
                    "id" => $group_res[$i]['id'],
                    "group" => $group_res[$i]['group'],
                    "checked" => 0,
                );
        }
        if($user_dep_id == 0)
            $msg[] = array(
                "id" => 0,
                "group" => "All",
                "checked" => 1,
            );
        else
            $msg[] = array(
                "id" => 0,
                "group" => "All",
                "checked" => 0,
            );
    }else{
        for($i = 0; $i < count($group_res); $i++){
            $msg[] = array(
                "id" => $group_res[$i]['id'],
                "group" => $group_res[$i]['group'],
                "checked" => 0,
            );
        }
        $msg[] = array(
            "id" => 0,
            "group" => "All",
            "checked" => 1,
        );
    }
    return $msg;
}

/**
 *
 *
 * @param [type] $db
 * @param [type] $post
 * @param [type] $userId
 * @return void
 */
function taskFilter($db, $post, $userId)
{
    // return $post;
    $page = trim($post['page']);
    $limit = trim($post['limit']);
    $field = trim($post['field']);
    $order = trim($post['order']);
    $page = ($page - 1) * $limit;

    // 详细信息查询
    $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`eta`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,a.`cancel_done_time`,
       u.`username` ,us.`username` as `toUser`, a.`priority` , ul.`username` as requestor  from team_ipad.issue_list as a
       left join `product` as b on a.`product_id` = b.`id`
       left join `project` as c on a.`project_id` = c.`id`
       left join `build` as d on a.`build_id` = d.`id`
       left join `station` as e on a.`station_id` = e.`id`
       left join `status` as f on a.`status_id` = f.`id`
       left join `user_list` as u on  a.`create_user_id` = u.`id`
       left join `user_list` as us on  a.`check_user` =us.`id`
       left join `user_list` as ul on  a.`requestor_id`= ul.`id`
       ";

    // 获取当前详细信息条数
    $task_count_select_sql = "SELECT count(a.`id`) as num
       from team_ipad.issue_list as a";

    $temp_sql = ' where 1 ';
     $limit_sql='';
    // 对于详细信息进行分页处理  对ID进行倒序
    //$limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";
    if(''!=$field && ''!=$order)
        $limit_sql .= " order by $field $order limit $page,$limit";
    else
        $limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";

    //TODO 产品和专案 未做对应表连接
    if ('' != $post['product']) {
        $product = explode("|", $post['product']);
        $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
    } else {
        $product_result = searchProduct($db, $userId);
        $product = explode("|", $product_result[0]['product_id']);
        $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');

    }

    if ('' != $post['project']) {
        $project = explode("|", $post['project']);
        $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
    }

    if ('' != $post['build']) {
        $build = explode('|', $post['build']);
        $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
    }

    if ('' != $post['station']) {
        $station = explode("|", $post['station']);
        $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
    }

    if ('' != $post['status']) {
        $status = explode('|', $post['status']);
        $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
    }

    if (isset($post['start_time'])) {
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if ($start_time == $end_time && $start_time) {
            $end_time .= " 23:59:59";
        }
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        if ($start_time || $end_time) {
            $temp_sql .= " and a.`create_time` between '" . $post['start_time'] . "' and '" . $post['end_time'] . "' ";
        }
    }
     $where='';
    $stage_sql="select build_id from `user_build` where `user_id`='$userId' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stageId=$stage_task[0]['build_id'];
        $stageId=explode('|', $stageId);
        for ($i = 0; $i < count($stageId); $i++) {
            $stageIdname=explode('-', $stageId[$i]);
            if (count($stageId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
                } else if ($i == count($stageId) - 1) {
                    $where .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ) ";
                } else {
                    $where .= " or (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
                }
            } else {
                if($stageIdname[0]!='' && $stageIdname[1]!='')
                    $where .= " and (a.`project_id` = '$stageIdname[0]' and a.`build_id` = '$stageIdname[1]') ";
            }
        }
    }

    $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='14'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $depId = $group_task[0]['group_id'];
        if($depId!=0&&$depId!='')
            $temp_sql .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";
    }
    $task_select_sql .= $temp_sql;
    $task_count_select_sql .= $temp_sql;
    // sql 语句拼接
    $task_select_sql .= $limit_sql;
    // $task_count_select_sql .= $limit_sql;
    $task_select_result = $db->query($task_select_sql);
    for ($i = 0; $i < count($task_select_result); $i++) {
        $task_select_result[$i]['new_status'] = '';
        $history_status_sql = "SELECT `status` FROM team_ipad.history WHERE `issue_list_id` = '" . $task_select_result[$i]['id'] . "' ORDER BY `id` DESC";
        $history_status_result = $db->query($history_status_sql);
        for ($j = 0; $j < count($history_status_result); $j++) {
            if ($j == count($history_status_result) - 1) {
                $task_select_result[$i]['new_status'] = htmlspecialchars($history_status_result[$j]['status']);
            } else {
                if (!empty($history_status_result[$j]['status'])) {
                    $task_select_result[$i]['new_status'] = htmlspecialchars($history_status_result[$j]['status']);
                    break;
                }
            }
        }

        if (!empty($task_select_result[$i]['cancel_content'])) {
            $task_select_result[$i]['new_status'] = htmlspecialchars($task_select_result[$i]['cancel_content']);
        }
    }
    $task_count_select_result = $db->query($task_count_select_sql);

    // 数据返回接口格式
    $task_result = array(
        "code" => 0,
        "msg" => $task_select_sql,
        "count" => $task_count_select_result[0]['num'],
        "data" => $task_select_result,
    );

    return $task_result;

}
/**
 * Filter 查询数据返回当前filter的设置
 *
 * @param [type] $db
 * @param [type] $userId
 * @param string $choose
 * @return void
 */
function filterTable($db, $filterId)
{
    $choose_select_sql = "SELECT `id`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` from `filter` where `id`= '$filterId' ";
    $choose_select_result = $db->query($choose_select_sql);

    return $choose_select_result;
}

/**
 * 选择user_list表中用户设置的setting或filter中的设置返回内容
 *
 * @param [type] $db
 * @param [type] $userId
 * @param string $choose
 * @return void
 */
function chooseTable($db, $userId, $choose)
{
    $chooseId = $choose . "_id";
    //$choose_select_sql = "SELECT b.`id`,b.`product_id`,b.`project_id`,b.`build_id`,b.`station_id`,b.`status_id` from `user_list` as a left join `$choose` as b on a.`$chooseId` = b.`id` where a.`id` = '$userId' ";
    $choose_select_sql = "SELECT `id`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id` from `$choose` where product_id = 14 and `user_id` = '$userId' ";

    $choose_select_result = $db->query($choose_select_sql);
    return $choose_select_result;
}

/**
 * taskAllListInit where条件拼接
 *
 * @param Array $array   需要循环的字段的值
 * @param String $field  需要遍历的字段
 * @return void
 */
function taskAllListInitSqlLine($array, $field)
{
    $str = ' AND ';
    for ($i = 0; $i < count($array); $i++) {
        if (count($array) > 1) {
            if ($i == 0) {
                $str .= " ($field = '$array[$i]' ";
            } else if ($i == count($array) - 1) {
                $str .= " OR $field = '$array[$i]') ";
            } else {
                $str .= " OR $field = '$array[$i]' ";
            }
        } else {
            $str .= " $field = '$array[$i]' ";
        }
    }
    return $str;
}

/**
 * 查询当前所拥有的产品权限
 *
 * @param Object $db
 * @param Int $userId
 * @return Array
 */
function searchProduct($db, $userId)
{
    $product_select_sql = "SELECT `product_id` FROM `user_list` WHERE `id` = '$userId'";
    $product_select_result = $db->query($product_select_sql);
    return $product_select_result;
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
