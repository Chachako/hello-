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
    header("location:../index.html");
}

$userId = $_SESSION['cooper_user_info'][0]['id'];

require_once "../Include/db_connect.php";

$action = $_GET['action'];

switch ($action) {
    case 'get_stage_station_list':
        $msg = getStageStationlist($db, $userId);
        break;

    case 'my_account_list':
        $stationId=$_GET['stationId'];
        $stageId=$_GET['stageId'];
        $filterId=$_GET['filter_id'];
        $msg = searchmyAccountList($db, $filterId,$stationId,$stageId);
        break;
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
        $post = joinField($_POST);
        $msg = updatefilter($db, $post);
        break;

    // case "update_filterid":
    //     $filterId = trim($_POST['filterId']);
    //     $msg = updateUserfilter($db, $userId, $filterId);
    //     break;

    case 'get_filter_list':
        $msg = getFilterList($db, $userId);
        break;

    case 'get_filter_info':
        $filterId = $_POST['filter_id'];
        $msg = getfilterInfo($db, $filterId);
        break;

    case 'get_filter':
        $msg = getfilter($db, $userId);
        break;
    default:
        $msg = "Parameter Error";
        break;
}

echo json_encode($msg);

function getStageStationlist($db, $userId)
{
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
    $where3 = "";
    for($i = 0; $i < count($pro_result); $i++){
        if(count($pro_result)>1){
            if($i == 0){
                $where1 .= " and (a.project_id = '".$pro_result[$i]['project_id']."'";
            }else if($i == count($pro_result)-1){
                $where1 .= " or a.project_id = '".$pro_result[$i]['project_id']."')";
            }else{
                $where1 .= " or a.project_id = '".$pro_result[$i]['project_id']."'";
            }
        }
        else{
            $where1 .= " and a.project_id = '".$pro_result[$i]['project_id']."'";
        }
    }
    $filter_build_sql = "select a.id,a.build_id,a.station_id from `filter` as a  where (`user_id` = '$userId' and `product_id` = '20' ) or `user_id` = '1' $where1 ";
    $filter_build_result = $db->query($filter_build_sql);
    $a=array();
    $b=array();
    if($filter_build_result){
        for($j=0;$j<count($filter_build_result);$j++){

            if(strstr($filter_build_result[$j]['build_id'],'|')!=false){
                $build_id=explode("|",$filter_build_result[$j]['build_id']) ;
                for($k=0;$k<count($build_id);$k++){
                    $build_id1= $build_id[$k];
                    array_push($a, $build_id1);
                }
            }else{
                $build_id1=$filter_build_result[$j]['build_id'];
                array_push($a,$build_id1);
            }
            if(strstr($filter_build_result[$j]['station_id'],'|')!=false){
                $station_id=explode("|",$filter_build_result[$j]['station_id']) ;
                for($k=0;$k<count($station_id);$k++){
                    $station_id1= $station_id[$k];
                    array_push($b, $station_id1);
                }
            }else{
                $station_id1=$filter_build_result[$j]['station_id'];
                array_push($b,$station_id1);
            }
        }
        for ($l = 0; $l < count($a); $l++) {
            if(count($a)>1){
                if($l == 0)
                {
                    $where2 .= " and (a.id = '".$a[$l]."'";
                }else if($l == count($a)-1)
                {    
                    $where2 .= " or a.id = '".$a[$l]."')";
                    
                }
                else{
                    $where2 .= " or a.id= '".$a[$l]."'";  
                }
            }
            else{
                $where2 .= " and a.id = '".$a[$l]."'"; 
            }
        }
        for ($n = 0; $n< count($b); $n++) {
            if(count($b)>1){
                if($n == 0)
                {
                    $where3 .= " and (a.id = '".$b[$n]."'";
                }else if($n == count($b)-1)
                {    
                    $where3 .= " or a.id = '".$b[$n]."')";
                    
                }
                else{
                    $where3 .= " or a.id= '".$b[$n]."'";  
                }
            }
            else{
                $where3 .= " and a.id = '".$b[$n]."'"; 
            }
        }
        $build_sql="select distinct id,build from build as a where enable='1' $where2";
        $build_result = $db->query($build_sql);
        for($m=0;$m<count( $build_result);$m++){
    
            $msg['stage'][] = array(
        
                "name" =>$build_result[$m]['build'],
         
                "value" =>$build_result[$m]['id'],
             //    "selected" => $check,
            );
        }
        $station_sql="select distinct id,station from station as a where enable='1' $where3";
        $station_result =$db->query($station_sql);
        for($m=0;$m<count( $station_result);$m++){
    
            $msg['station'][] = array(
        
                "name" =>$station_result[$m]['station'],
         
                "value" =>$station_result[$m]['id'],
             //    "selected" => $check,
            );
        }
    }else{
    $msg="NO DATA";

    }
 
    return $msg;
}


function searchmyAccountList($db, $filterId, $stationId, $stageId)
{
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where =" where 1 and  `product_id`='20' and( `user_id`='$user_id' or `user_id`='1' )";
    if ($filterId!=''&&$filterId!=null) {
        $where.=" and id=' $filterId'";
    }
    if ($stageId!=''&&$stageId!=null) {
        $stageId=explode('|', $stageId) ;
        for ($i = 0; $i < count($stageId); $i++) {
            if (count($stageId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                } elseif ($i == count($stageId) - 1) {
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
            }
        }
    }
    if ($stationId!=''&&$stationId!=null) {
        $stationId=explode('|', $stationId) ;
        for ($i = 0; $i < count($stationId); $i++) {
            if (count($stationId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                } elseif ($i == count($stationId) - 1) {
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
            }
        }
    }
    $account_select_sql = "SELECT `id`,`user_id`,`filter_name`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id`,`start_time`,`end_time` FROM `filter` as f   $where ";
    $account_select_result = $db->query($account_select_sql);
    $account_select_count_select = "SELECT count(1) as num from filter where `id`='$filterId' and `product_id`='20'";
    $account_select_count_result = $db->query($account_select_count_select, 'Row');

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

                    // var_dump($project_select_result);
                    //为空时 没值
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

                    // var_dump($project_select_result);
                    //为空时 没值
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

                    // var_dump($project_select_result);
                    //为空时 没值
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
    } else {
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
    $product = $post['product'];
    $project = $post['project'];
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
function updatefilter($db, $post)
{
    // var_dump($post);die;
    $filterId = $post['filter_id'];
    $product = $post['product'];
    $project = $post['project'];
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
function getFilterList($db, $userId)
{
    // TODO 暂未考虑翻页
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
    $where .="and f.product_id='20' and (f.user_id = '$userId' or f.user_id = '1') order by `user_id` desc ,id asc";
   

    $filterlist_select_sql="select f.* from filter as f $where";
    $filterlist_select_result=$db->query($filterlist_select_sql);




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

    $result_array = array(
        "product" => $product,
        "project" => $project,
        "build" => $build,
        "station" => $station,
        "status" => $status,
        "start_time" => $start_time,
        "end_time" => $end_time,
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
function getfilter($db, $userId)
{
    $filter_select_sql = "SELECT `product_id` from `user_list` where `id`='$userId'";

    $filter_select_result = $db->query($filter_select_sql);

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

            // $project_select_result = $db->query($project_select_sql);

            // if ($project_select_result) {
            //     for ($j = 0; $j < count($project_select_result); $j++) {
            //         $project_info_sql = "SELECT `project` from `project` where `id` = '" . $project_select_result[$j]['project_id'] . "'";
            //         $project_info_result = $db->query($project_info_sql);
            //         $temp['project'][] = array(
            //             "id" => $project_select_result[$j]['project_id'],
            //             "project" => $project_info_result[0]['project'],
            //         );
            //     }
            // }

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
