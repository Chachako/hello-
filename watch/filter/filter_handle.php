<?php

/*
 * @Author: moxuan
 * @Date: 2019-03-05 13:39:11
 * @Last Modified by: moxuan
 * @Last Modified time: 2019-03-12 15:17:26
 */

session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../../index.html");
}

$userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../../Include/db_connect.php";
$action = $_GET['action'];

switch ($action) {
    case 'my_account_list':
        $stageId = trim($_GET['stageId']);
        $stationId = trim($_GET['stationId']);
        $msg = searchmyAccountList($db,$stageId,$stationId);
        break;
    case 'account_list':
        $stageId = trim($_GET['stageId']);
        $stationId = trim($_GET['stationId']);
        $msg = searchAccountList($db, $userId,$stageId,$stationId);
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
    case 'get_buildstation':
        $msg = getbuildstation($db, $userId);
        break;
    case 'get_filter':
        $filterId = trim($_POST['filter_id']);

        $msg = getfilter($db, $filterId,$userId);
        break;
    case 'filter_all_list':
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
        $msg = filterAllListInit($db, $post, $choose);
        break;

    case 'filter_list':
        $page = trim($_POST['page']);
        $limit = trim($_POST['limit']);
        $filterId = trim($_POST['filterId']);
        $field = trim($_POST['field']);
        $order = trim($_POST['order']);
        $stageId = trim($_POST['stageId']);
        $depId = trim($_POST['depId']);
        $page = ($page - 1) * $limit;
        $post = array(
            'page' => $page,
            'limit' => $limit,
            'field' => $field,
            'order' => $order,
            'userId' => $userId,
        );
        $msg = taskFilterList($db, $post, $filterId, $stageId, $depId);
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
    case 'get_project':
        $msg = getproject($db, $userId);
        break;
    default:
        $msg = "Parameter Error";
        break;
}

echo json_encode($msg);


function searchAccountList($db,$userId,$stageId,$stationId )

{     session_start();
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where = " where 1 ";
    if ($project_id!=''&&$project_id!=null) {
        $project_id=explode('|', $project_id) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                } elseif ($i == count($project_id) - 1) {
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
            }
        }
    }
    $where .="and( f.user_id='1' or f.user_id='$userId') and product_id='20'";
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
    $account_select_count_result = $db->query($account_select_count_select, 'Row');
    // var_dump($account_select_count_select);
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
            if ($project!=""&& $project!=null) {
                $project = explode('|', $project);
                for ($j = 0; $j < count($project); $j++) {
                    $project_select_sql = "SELECT `project` FROM `project` WHERE  `id`='" . $project[$j] . "' ";
                    $project_select_result = $db->query($project_select_sql);
                    $temp3[$i][] = $project_select_result[0]['project'];
                    $projectIdArr[$i][] = $project[$j];
                }
            } else {
                $temp3[$i][]="";
            }

            $build = $account_select_result[$i]['build_id'];
            if ($build!=""&& $build!=null) {
                $build = explode('|', $build);
                for ($j = 0; $j < count($build); $j++) {
                    $build_select_sql = "SELECT `build` FROM `build` WHERE  `id`='" . $build[$j] . "' ";
                    $build_select_result = $db->query($build_select_sql);
                    $temp4[$i][] = $build_select_result[0]['build'];
                    $buildIdArr[$i][] = $build[$j];
                }
            } else {
                $temp4[$i][]="";
            }
            
            $station = $account_select_result[$i]['station_id'];
            if ($station!=""&& $station!=null) {
                $station = explode('|', $station);
                for ($j = 0; $j < count($station); $j++) {
                    $station_select_sql = "SELECT `station` FROM `station` WHERE  `id`='" . $station[$j] . "' ";
                    $station_select_result = $db->query($station_select_sql);
                    $temp5[$i][] = $station_select_result[0]['station'];
                    $stationIdArr[$i][] = $station[$j];
                }
            } else {
                $temp5[$i][]="";
            }
            $status = $account_select_result[$i]['status_id'];

            if ($status!=""&& $status!=null) {
                $status = explode('|', $status);
                for ($j = 0; $j < count($status); $j++) {
                    $status_select_sql = "SELECT `status` FROM `status` WHERE  `id`='" . $status[$j] . "' ";
                    $status_select_result = $db->query($status_select_sql);
                    //为空时 没值
                    $temp6[$i][] = $status_select_result[0]['status'];
                    $statusIdArr[$i][] = $status[$j];
                }
            } else {
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
        $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" => $array,
    );
        // $msg = $result_array;

        return $result_array;
    }else{
        $array=array();
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result['num'],
            "data" => $array,
        );
        return $result_array;
    }
}

function getproject($db, $userId)
{
    $temp = $_GET['depId'];
    $project_select_sql = "select b.id,b.`project` from `product_project` as a left join `project` as b on a.`project_id`=b.`id` where a.`enable` = '1' and a.`product_id` = '".$temp."'";
    $project_select_result = $db->query($project_select_sql);
    $msg = $project_select_result;
    
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
function searchmyAccountList($db,$stageId,$stationId)
{
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $account_select_sql = "SELECT `id`,`user_id`,`filter_name`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` FROM `filter` where `user_id`='$user_id' and `product_id`='20'  ";
    
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
    $where .="and f.product_id='20' and f.user_id=' $user_id' ";
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
    $account_select_count_select = "SELECT count(1) as num from filter where `user_id`='$user_id' and `product_id`='20'";
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
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result['num'],
            "data" => $array,
        );
    }else{
        $array=array();
        $result_array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $account_select_count_result['num'],
            "data" => $array,
        );
    }
   
   
    // $msg = $result_array;

    return $result_array;
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
 * taskList 默认列表
 *
 * @param [type] $db
 * @param [type] $post
 * @param string $choose 选择setting 或 filter
 * @return void
 */

function getfilter($db, $filterId,$userId)
{  
    $filter_select_sql = "SELECT `product_id` from `user_list` where `id`='$userId'";

    $filter_select_result = $db->query($filter_select_sql);
// var_dump($filter_select_result);die;
    if ($filter_select_result) {
        $product = explode("|", $filter_select_result[0]['product_id']);




              $user_name=$_SESSION['cooper_user_info'][0]['username'];
              $user_id=$_SESSION['cooper_user_info'][0]['id'];
              $user_project=$_SESSION['cooper_user_info'][0]['project_id'];

              if($user_project==""||$user_project==NULL){

                  $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '20'";
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
                $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '20'";
                $project_select_result = $db->query($project_select_sql);
                $project_id = explode("|", $user_project);
                for ($j = 0; $j < count($project_select_result); $j++) {
                    for ($k = 0; $k < count($project_id); $k++) {
                        if($project_select_result[$j]['project_id'] == $project_id[$k])
                            $project_id1[] = $project_id[$k];
                    }
                }

                if(count($project_id1)!=0 ){
                    for ($j = 0; $j < count($project_id1); $j++) {
                      $project_info_sql = "SELECT `project` from `project` where `enable` = '1' and `id` = '" . $project_id1[$j] . "'";
                      $project_info_result = $db->query($project_info_sql);
                      $temp['project'][] = array(
                        "id" => $project_id1[$j],
                        "project" => $project_info_result[0]['project'],
                    );}
                }else{
                    $project_select_sql = "SELECT `project_id` from `product_project` where `enable` = '1' and `product_id` = '20'";
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
            $product_info_sql = "SELECT `product` from `product` where `enable` = '1' and `id` = '20' ";
            $product_info_result = $db->query($product_info_sql);

            $temp['product'][] = array(
                "id" => 20,
                "product" => $product_info_result[0]['product'],
            );


        for ($i = 0; $i < count($temp['project']); $i++) {
            $stationProject_select_sql = "SELECT `station` FROM `station_project` WHERE `enable` = '1' and `project` = '" . $temp['project'][$i]['id'] . "' ";
            $stationProject_select_result = $db->query($stationProject_select_sql);
            //$temp['sql'][] = $stationProject_select_sql;
            $temp['sql'][] = array(
                'id' => $i,
                'sql' => $stationProject_select_sql,
            );
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
        $temp['station'] = arrayUnique($temp['station']);

        for ($i = 0; $i < count($temp['project']); $i++) {
            $stationProject_select_sql = "SELECT `build_id` FROM `build_project` WHERE `enable` = '1' and `project_id` = '" . $temp['project'][$i]['id'] . "' ";
            $stationProject_select_result = $db->query($stationProject_select_sql);
            //$temp['sql'][] = $stationProject_select_sql;
            if ($stationProject_select_result) {
                for ($j = 0; $j < count($stationProject_select_result); $j++) {
                    $station_select_sql = "SELECT `build` FROM `build` WHERE `id` = '" . $stationProject_select_result[$j]['build_id'] . "' and `enable` = '1' ";
                    $station_select_result = $db->query($station_select_sql);
                    $temp['build'][] = array(
                        'id' => $stationProject_select_result[$j]['build_id'],
                        'build' => $station_select_result[0]['build'],
                    );
                }
            }
        }
        $temp['build'] = arrayUnique($temp['build']);

        // $build_select_sql = "SELECT `id`,`build` FROM `build` WHERE `enable`='1'";
        $status_select_sql = "SELECT `id`,`status` FROM `status`";

        // $build_select_result = $db->query($build_select_sql);
        $status_select_result = $db->query($status_select_sql);

        // $temp['build'] = $build_select_result;
        $temp['status'] = $status_select_result;

        $msg = $temp;
    } else {
        $msg = "fail";
    }
    return $msg;
}

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

function filterAllListInit($db, $post, $choose)
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
            $temp_sql .= filterAllListInitSqlLine($product, 'a.`product_id`');
        } else {
            $product_result = searchProduct($db, $userId);
            $product = explode("|", $product_result[0]['product_id']);
            $temp_sql .= filterAllListInitSqlLine($product, 'a.`product_id`');
        }

        if ('' != $setting_data['project_id']) {
            $project = explode("|", $setting_data['project_id']);
            $temp_sql .= filterAllListInitSqlLine($project, 'a.`project_id`');
        }

        if ('' != $setting_data['build_id']) {
            $build = explode('|', $setting_data['build_id']);
            $temp_sql .= filterAllListInitSqlLine($build, 'a.`build_id`');
        }

        if ('' != $setting_data['station_id']) {
            $station = explode("|", $setting_data['station_id']);
            $temp_sql .= filterAllListInitSqlLine($station, 'a.`station_id`');
        }

        if ('' != $setting_data['status_id']) {
            $status = explode('|', $setting_data['status_id']);
            $temp_sql .= filterAllListInitSqlLine($status, 'a.`status_id`');
        }

    } else {
        
        $product_result = searchProduct($db, $userId);
        $product = explode("|", $product_result[0]['product_id']);
        $temp_sql .= filterAllListInitSqlLine($product, 'a.`product_id`');

       
        //到产品
        $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
        $project_result = $db->query($project_select_sql);

        if($project_result[0]['project_id']!=NUll&& $project_result[0]['project_id']!=''){
         $project = explode("|", $project_result[0]['project_id']);
         // var_dump($project);
         $temp_sql .= filterAllListInitSqlLine($project, 'a.`project_id`');

        }
        // 只允许查看所拥有的产品权限
    }

    $stage_sql="select build_id from `user_build` where `user_id`='$userId' and `product_id`='20'";
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

    $station_sql="select station_id from `user_station` where `user_id`='$userId' and `product_id`='20'";
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

    $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='20'";
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

    $station_select_sql = "select distinct(a.`station`) as id,b.`station` from `station_project` as a left join `station` as b on a.`station`=b.`id` where a.`enable` = '1' $where";
    $station_select_result = $db->query($station_select_sql);

    $msg['station'] = $station_select_result;
    
    return $msg;
}



/**
 * 使用filter时 获取返回结果
 *
 * @param Object $db
 * @param Array $post
 * @param Int $filterId
 * @return void
 */
function taskFilterList($db, $post, $filterId, $stageId, $depId)
{
    $userId = $post['userId'];
    $page = $post['page'];
    $field = $post['field'];
    $order = $post['order'];
    $limit = $post['limit'];

    $filter_select_result = filterTable($db, $filterId);

    // 详细信息查询
    $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`,a.`eta`,a.`cancel_user`,a.`done_user`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,a.`cancel_done_time`, 
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

    // 对于详细信息进行分页处理  对ID进行倒序
    if(''!=$field && ''!=$order)
        $limit_sql = " order by $field $order limit $page,$limit";
    else
        $limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";
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
    $where="";
    $stage_sql="select id from `user_build` where `user_id`='$userId' and `product_id`='20'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$userId','20','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

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

    $group_sql="select id from `user_group` where `user_id`='$userId' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$userId','20','$depId')";
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
        $task_select_result[$i]['new_status'] = '';

        $history_status_sql = "SELECT `status` FROM team_ipad.history WHERE `issue_list_id` = '" . $task_select_result[$i]['id'] . "' ORDER BY `id` DESC";

        $history_status_result = $db->query($history_status_sql);
        for ($j = 0; $j < count($history_status_result); $j++) {
            if ($j == count($history_status_result) - 1) {
                $task_select_result[$i]['new_status'] =  htmlspecialchars($history_status_result[$j]['status']);
            } else {
                if (!empty($history_status_result[$j]['status'])) {
                    $task_select_result[$i]['new_status'] = htmlspecialchars($history_status_result[$j]['status']);
                    break;
                }
            }
        }

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

    $pro_sql = "select project_id from `product_project` where `product_id` = '20' and `enable` = '1' $where";
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

    $user_build_sql = "select build_id from `user_build` where `user_id` = '$userId' and `product_id` = '20'";
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

    $user_station_sql = "select station_id from `user_station` where `user_id` = '$userId' and `product_id` = '20'";
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
    $group_sql="select * from `group` where 1 and (`product_id` like '20' or `product_id` like '20|%' or `product_id` like '%|20|%' or `product_id` like '%|20') and `enable` = '1'";
    $group_res=$db->query($group_sql);
    $select_user_dep = "select * from user_group where `product_id` = '20' and `user_id` = '$userId'";
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

    // 对于详细信息进行分页处理  对ID进行倒序
    //$limit_sql = " ORDER BY a.`id` DESC limit $page,$limit";
    if(''!=$field && ''!=$order)
        $limit_sql = " order by $field $order limit $page,$limit";
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
    $where="";
    $stage_sql="select build_id from `user_build` where `user_id`='$userId' and `product_id`='20'";
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

    $group_sql="select group_id from `user_group` where `user_id`='$userId' and `product_id`='20'";
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
    $choose_select_sql = "SELECT `id`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id` from `$choose` where product_id = 20 and `user_id` = '$userId' ";

    $choose_select_result = $db->query($choose_select_sql);
    return $choose_select_result;
}

/**
 * filterAllListInit where条件拼接
 *
 * @param Array $array   需要循环的字段的值
 * @param String $field  需要遍历的字段
 * @return void
 */
function filterAllListInitSqlLine($array, $field)
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
