<?php
require_once "../Include/db_connect.php";
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}
$userId = $_SESSION['cooper_user_info'][0]['id'];

$action = $_GET['action'];
// var_dump($action);die;


switch ($action) {
   
    case 'search_all_filter':
        $stageId = trim($_GET['stageId']);
        $stationId = trim($_GET['stationId']);
        $depId = trim($_GET['depId']);
        $msg = search_all_filter($db,$stageId,$stationId,$userId);
        break;
    case 'search_my_filter':
        $stageId = trim($_GET['stageId']);
        $stationId = trim($_GET['stationId']);
        $depId = trim($_GET['depId']);
        $msg = search_my_filter($db,$stageId,$stationId,$userId);
        break;
    case 'search_all_task':
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $msg = search_all_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_my_task':
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $msg = search_my_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_ongoing_task':
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $msg = search_ongoing_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_block_task':
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $msg = search_block_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_cancel_task':
        $depId = trim($_GET['depId']);
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $msg = search_cancel_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_done_task':
        $stationId=trim($_GET['stationId']);
        $stageId=trim($_GET['stageId']);
        $depId = trim($_GET['depId']);
        $msg = search_done_task($db,$depId,$stageId,$stationId);
        break;
    case 'search_message_task':
        $msg = search_message_task($db);
        break;
    case 'login_info':
        $msg = login_info($db);
        break;

     
    case 'login_time':
        $msg = login_time($db);
        break;   
    // case 'logout_time':
    //     $msg = logout_time($db);
    //     break;   

    default:
        $msg = "参数错误";
        break;
}
echo json_encode($msg);


// //获取退出时间
// function logout_time($db){
//      session_start();
//         $user_name=$_SESSION['cooper_user_info'][0]['username'];
//          $user_id=$_SESSION['cooper_user_info'][0]['id'];


//        $sql= "select * from login_time where user_id='$user_id' and user_name=' $user_name' ";
//        $result = $db->query($sql);

//         $login_time=date('Y-m-d H:i:s',time());
//         $insert_sql= "";
//         // var_dump($insert_sql);die;
//         $db->query($insert_sql);

// }


//获取登陆时间 
function login_time($db){
     session_start();
        $user_name=$_SESSION['cooper_user_info'][0]['username'];
         $user_id=$_SESSION['cooper_user_info'][0]['id'];

        $login_time=date('Y-m-d H:i:s',time());
        $insert_sql= "INSERT into login_time  (user_id,user_name,login_time) values ('$user_id','$user_name','$login_time' )";
        // var_dump($insert_sql);die;
        $db->query($insert_sql);

}

function search_all_filter($db,$stageId,$stationId,$userId){
  
    session_start();
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where = " where 1 and( f.user_id='1' or f.user_id='$userId') and product_id='20' ";
    $where1="  where 1 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) "; 
                    $where1 .= "  and ( FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) "; 
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ) ";
                    $where1 .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ) ";
                } else {
                    $where1 .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                    $where .= " OR FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
                $where1 .= " and FIND_IN_SET('$project_id[$i]',replace(f.project_id, '|',',')) ";
            }
        }
    } if($stageId!=''&&$stageId!=NULL){
        $stageId=explode('|', $stageId) ;
        for ($i = 0; $i < count($stageId); $i++) {
            if (count($stageId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) "; 
                    $where1 .= "  and ( FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) "; 
                } else if ($i == count($stageId) - 1) {
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ) ";
                    $where1 .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ) ";
                } else {
                    $where1 .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                    $where .= " OR FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
                $where1 .= " and FIND_IN_SET('$stageId[$i]',replace(f.build_id, '|',',')) ";
            }
        }
    }
    if($stationId!=''&&$stationId!=NULL){
        $stationId=explode('|', $stationId) ;
        for ($i = 0; $i < count($stationId); $i++) {
            if (count($stationId) > 1) {
                if ($i == 0) {
                    $where .= "  and ( FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) "; 
                    $where1 .= "  and ( FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) "; 
                } else if ($i == count($stationId) - 1) {
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ) ";
                    $where1 .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ) ";
                } else {
                    $where1 .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                    $where .= " OR FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
                $where1 .= " and FIND_IN_SET('$stationId[$i]',replace(f.station_id, '|',',')) ";
            }
        }
    }
    $account_select_sql = "SELECT f.* FROM `filter` as f  $where";
    $account_select_result = $db->query($account_select_sql);
    $account_select_count_select = "SELECT count(1) as num from filter as f $where";
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



function search_message_task($db){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
    $product_id=$_SESSION['cooper_user_info'][0]['product_id'];

    $product_id=explode('|',$product_id);
    $where=' and (';
    foreach($product_id as $value){
    $where.=' product_id='.$value;
    $where.=' or';
    }
    $where=substr($where,0,-2);
    $where.=')';
    
    $sql="select id,task_title,access_user_id from team_watch.issue_list where assign='$group_id' and (step='1' or step='2') $where order by id desc";

  
    $all_task=$db->query($sql);

    $new_all_task=array();
    $count=0;
    foreach($all_task as $value){
    $access_user_id=explode(',',$value['access_user_id']);
    if(!in_array($user_id,$access_user_id)){
    $new_all_task[]=$value;
    $count++;
    }
    }
    $msg['new_all_task']=$new_all_task;
    $msg['count']=$count;
    return $msg;
}
    
    
    



function login_info($db){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
    $product_id=$_SESSION['cooper_user_info'][0]['product_id'];

    //id,user_id,username,online_time
    // ------------------------
    $user_name=$_SESSION['cooper_user_info'][0]['username'];
    $sql="select * from user_online where user_id='$user_id'";

    $res=$db->query($sql);
    $nowtime=time();
    if(empty($res)){
        $sql="insert into user_online (`user_id`,`username`,`online_time`) values ('$user_id','$user_name','$nowtime')";
        $db->query($sql);
    }else{
        $sql="update user_online set online_time='$nowtime' where user_id='$user_id'";
        $db->query($sql);
    }

}
    
function search_ongoing_task($db,$depId,$stageId,$stationId){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];

    $where = " where step=1 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;         
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where";
        $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC ";
    $sql .= $limit_sql;
    $task=$db->query($sql);
    $count_sql="select count(id) as count from team_watch.issue_list as a $where";
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
}
    
    

function search_block_task($db,$depId,$stageId,$stationId){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where = " where step=2 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;         
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where";
        $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC";
    $sql .= $limit_sql;
    $task=$db->query($sql);
    $count_sql="select count(id) as count from team_watch.issue_list as a $where";
    
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
}

function search_cancel_task($db,$depId,$stageId,$stationId){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];

    $where = " where step=3 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;         
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where";
        $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC ";
    $sql .= $limit_sql;
// var_dump($sql);die;
    $task=$db->query($sql);
    
    $count_sql="select count(id) as count from team_watch.issue_list as a $where";
    
    $count=$db->query($count_sql);
    
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
}



function search_done_task($db,$depId,$stageId,$stationId){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    
    $where = " where step=4 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;         
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";


    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where";
        $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC ";
    $sql .= $limit_sql;
// var_dump($sql);die;
    $task=$db->query($sql);
    
    $count_sql="select count(id) as count from team_watch.issue_list as a $where";
    
    $count=$db->query($count_sql);
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
}

function search_all_task($db,$depId,$stageId,$stationId){
    session_start();
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $where = " where 1 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where";
    $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC ";
    $sql .= $limit_sql;
    $task=$db->query($sql);
    
    $count_sql="select count(id) as count from team_watch.issue_list as a $where";
    
    $count=$db->query($count_sql);
    
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
        
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
}


function search_my_task($db,$depId,$stageId,$stationId){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];

    $where = " where 1 ";
    if($project_id!=''&&$project_id!=NULL){
        $project_id=explode('|', $project_id) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( a.`project_id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR a.`project_id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  a.`project_id` = '$project_id[$i]' ";
            }
        }
    }
    $stage_sql="select id from `user_build` where `user_id`='$user_id' and `product_id`='14'";
    $stage_task=$db->query($stage_sql);
    if($stage_task){
        $stage_id = $stage_task[0]['id'];
        $stage_update="update `user_build` set `build_id`='$stageId' where `id`='$stage_id'";
        $stage_update_task=$db->execSql($stage_update);
    }else{
        $stage_insert = "INSERT INTO `user_build`(`user_id`,`product_id`,`build_id`) values('$user_id','14','$stageId')";
        $stage_insert_task = $db->execSql($stage_insert);
    }

    $station_sql="select id from `user_station` where `user_id`='$user_id' and `product_id`='14'";
    $station_task=$db->query($station_sql);
    if($station_task){
        $station_id = $station_task[0]['id'];
        $station_update="update `user_station` set `station_id`='$stationId' where `id`='$station_id'";
        $station_update_task=$db->execSql($station_update);
    }else{
        $station_insert = "INSERT INTO `user_station`(`user_id`,`product_id`,`station_id`) values('$user_id','14','$stationId')";
        $station_insert_task = $db->execSql($station_insert);
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

    $stationId=explode('|', $stationId);
    for ($i = 0; $i < count($stationId); $i++) {
        if (count($stationId) > 1) {
            if ($i == 0) {
                $where .= "  and ( a.`station_id` = '$stationId[$i]' ";
            } else if ($i == count($stationId) - 1) {
                $where .= " OR a.`station_id` = '$stationId[$i]' ) ";
            } else {
                $where .= " OR a.`station_id` = '$stationId[$i]' ";
            }
        } else {
            if($stationId[$i]!='')
                $where .= " and  a.`station_id` = '$stationId[$i]' ";
        }
    }
    $group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='20'";
    $group_task=$db->query($group_sql);
    if($group_task){
        $groupid = $group_task[0]['id'];
        $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
        $group_update_task=$db->execSql($group_update);
    }else{
        $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','20','$depId')";
        $group_insert_task = $db->execSql($group_insert);
    }

    if($depId != 0)
        $where .= " and exists (select 1 from (select h.id from team_watch.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_watch.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_watch.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";


     $sql="select DISTINCT(issue_list_id) from team_watch.history where user_id='$user_id'";
     $ids=$db->query($sql);

     if($ids){
        foreach($ids as $v){
            $ids_array[]=$v['issue_list_id'];
        }
        $ids=implode(',',$ids_array);
        // var_dump($ids);
     }else{
         $ids="0";
     }
    $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
        from team_watch.issue_list as a 
        left join product as pd on a.product_id=pd.id 
        left join project as pj on a.project_id=pj.id 
        left join build as bd on a.build_id=bd.id 
        left join station as st on a.station_id=st.id 
        left join status as `status` on a.status_id=`status`.`id`
        left join user_list as u on a.create_user_id =u.id 
        left join user_list as us on a.check_user=us.id
        left join user_list as ul on  a.requestor_id= ul.id
        $where and (a.create_user_id='$user_id' or a.check_user='$user_id')";
        $limit_sql='';
        $limit_sql .= " ORDER BY a.`id` DESC ";
    $sql .= $limit_sql;
    $task=$db->query($sql);
    $count_sql="select count(id) as count from team_watch.issue_list as a $where and (a.create_user_id='$user_id' or a.check_user='$user_id')";
    $count=$db->query($count_sql);
    $count=$db->query($count_sql);
    $task_count=count($task);
       for ($i = 0; $i <  $task_count; $i++) {
         if (!empty($task[$i]['cancel_content'])) {
             $task[$i]['new_status'] =htmlspecialchars($task[$i]['cancel_content']);
         }
         if(!empty($task[$i]['cancel_user'])){
              $task[$i]['toUser'] = $task[$i]['cancel_user'];
         }
         if(!empty($task[$i]['done_user'])){
              $task[$i]['toUser'] = $task[$i]['done_user'];
         }
     }
    if ($task) {
        $array = array(
            "code" => 0,
            "msg" => "success",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    } else {
        $array = array(
            "code" => 0,
            "msg" => "fail",
            "count" => $count[0]['count'],
            "data" => $task,
        );
        $msg = $array;
    }
    return $msg;
    
}



















?>