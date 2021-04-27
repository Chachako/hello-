<?php
require_once "../Include/db_connect.php";
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}

session_start();
$project_id=$_SESSION['cooper_user_info'][0]['project_id'];
$user_id=$_SESSION['cooper_user_info'][0]['id'];


$page = 0;
$limit = 20;

// var_dump($project_id==NUll);
$where = "where 1 ";
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

$stageId=explode('|', $stageId);
for ($i = 0; $i < count($stageId); $i++) {
    if (count($stageId) > 1) {
        if ($i == 0) {
            $where .= "  and ( a.`build_id` = '$stageId[$i]' ";
        } else if ($i == count($stageId) - 1) {
            $where .= " OR a.`build_id` = '$stageId[$i]' ) ";
        } else {
            $where .= " OR a.`build_id` = '$stageId[$i]' ";
        }
    } else {
        $where .= " and  a.`build_id` = '$stageId[$i]' ";
    }
}

$group_sql="select id from `user_group` where `user_id`='$user_id' and `product_id`='14'";
$group_task=$db->query($group_sql);
if($group_task){
    $groupid = $group_task[0]['id'];
    $group_update="update `user_group` set `group_id`='$depId' where `id`='$groupid'";
    $group_update_task=$db->execSql($group_update);
}else{
    $group_insert = "INSERT INTO `user_group`(`user_id`,`product_id`,`group_id`) values('$user_id','14','$depId')";
    $group_insert_task = $db->execSql($group_insert);
}

if($depId != 0)
    $where .= " and exists (select 1 from (select h.id from team_ipad.issue_list as h left join user_list as i on h.check_user = i.id where i.group_id = '$depId' and h.`done_user` is null and h.`cancel_user` is null union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`done_user` = i.`username` where i.group_id = '$depId' union
select h.id from team_ipad.issue_list as h left join user_list as i on h.`cancel_user` = i.`username` where i.group_id = '$depId') as j where j.id=a.id) ";

$sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.`username`,us.`username` as toUser,ul.`username` as requestor 
    from team_ipad.issue_list as a 
    left join product as pd on a.product_id=pd.id 
    left join project as pj on a.project_id=pj.id 
    left join build as bd on a.build_id=bd.id 
    left join station as st on a.station_id=st.id 
    left join status as `status` on a.status_id=`status`.`id`
    left join user_list as u on a.create_user_id =u.id 
    left join user_list as us on a.check_user=us.id
    left join user_list as ul on  a.requestor_id= ul.id
    $where";

if(''!=$field && ''!=$order)
    $limit_sql .= " order by $field $order limit $page,$limit";
else
    $limit_sql .= " ORDER BY a.`id` DESC limit $page,$limit";
$sql .= $limit_sql;
// $task=$db->query($sql);

// $count_sql="select count(id) as count from team_ipad.issue_list as a $where";

// $count=$db->query($count_sql);

// foreach($task as $key => $value){
//     if($value['step']=='3'){
//         $task[$key]['toUser']=$value['cancel_user'];
//         $task[$key]['new_status']=htmlspecialchars($value['cancel_content'])  ;
//     }else{
//         $issue_id=$value['id'];
//         $sql="select a.*,u.username,g.group from team_ipad.history as a left join user_list as u on u.id=a.user_id left join `group` as g on a.assign=g.id where a.issue_list_id= '$issue_id' order by a.id desc";
//         $history=$db->query($sql);
//         $status='';
//         foreach($history as $v){
//             if(!empty($v['status'])){
//                 $status=$v['status'];
//                 break;
//             }
//         }
//         $task[$key]['new_status']=htmlspecialchars($status) ;
//     }

//     // if ($value['step']=='3') {
//     //     $task[$key]['toUser']=$value['cancel_user'];
//     // }
//     if ($value['step']=='4') {
//         $task[$key]['toUser']=$value['done_user'];
//     }
// }
    
// if ($task) {
//     $array = array(
//         "code" => 0,
//         "msg" => "success",
//         "count" => $count[0]['count'],
//         "data" => $task,
//     );
//     $msg = $array;
// } else {
//     $array = array(
//         "code" => 0,
//         "msg" => "fail",
//         "count" => $count[0]['count'],
//         "data" => $task,
//     );
//     $msg = $array;
// }
echo $sql;
?>