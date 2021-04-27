<?php
require_once "../Include/db_connect.php";
session_start();

if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}
$userId = $_SESSION['cooper_user_info'][0]['id'];

$action = $_GET['action'];
switch($action){
    case 'search_all_filter':
        $depId = trim($_GET['depId']);
        $msg = search_all_filter($db,$depId,$userId);
        break;
    case 'task_all_list':
        $choose=$_GET['choose'];
        $depId = trim($_GET['depId']);
        $post = array(
             'userId' => $userId,
            );
        $msg = taskAllListInit($db, $post, $choose,$depId);
        break;
    case 'task_my_list':
        $choose=$_GET['choose'];
        $depId = trim($_GET['depId']);
        $post = array(
            'userId' => $userId,
        );
        $msg = taskMYListInit($db, $post, $choose,$depId);
        break;
    case  'search_all_list':
         $depId = trim($_GET['depId']);
         $msg = search_all_task($db,$depId);
         break;
    case  'search_my_list':
         $depId = trim($_GET['depId']);
         $page = ($page - 1) * $limit;
         $msg = search_my_task($db,$depId);
         break;

        default:
        $msg = "参数错误";
        break;
}
echo json_encode($msg);
// function getall($db,$filterSos){
//  $value=$filterSos['value'];
//     if($filterSos['field']=='project'){
      
//     }
// }
function search_all_task($db,$depId){
   
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
         
    $count_sql="select count(id) as count from team_ipad.issue_list as a $where";
    
    $count=$db->query($count_sql);
    $limit_sql=" ORDER BY a.`id` DESC ";
    // if(''!=$field && ''!=$order)
    //     $limit_sql .= " order by $field $order limit $page,$limit";
    // else
    //     $limit_sql .= " ORDER BY a.`id` DESC limit $page,$limit";
    $sql .= $limit_sql;
    $task=$db->query($sql);
   
    
   
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
function search_my_task($db,$depId){
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


     $sql="select DISTINCT(issue_list_id) from team_ipad.history where user_id='$user_id'";
     $ids=$db->query($sql);

     if($ids){
        foreach($ids as $v){
            $ids_array[]=$v['issue_list_id'];
        }
        $ids=implode(',',$ids_array);
     }else{
         $ids="0";
     }

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
        $where and (a.create_user_id='$user_id' or a.check_user='$user_id')";
        $limit_sql='';
    // if(''!=$field && ''!=$order)
    //     $limit_sql .= " order by $field $order limit $page,$limit";
    // else
    //     $limit_sql .= " ORDER BY a.`id` DESC limit $page,$limit";
    $limit_sql=' ORDER BY a.`id` DESC';
    $sql .= $limit_sql;
    $task=$db->query($sql);
    
    $count_sql="select count(id) as count from team_ipad.issue_list as a $where and (a.create_user_id='$user_id' or a.check_user='$user_id')";
    
    $count=$db->query($count_sql);

    
    // foreach($task as $key => $value){
    //     if($value['step']=='3'){
    //         $task[$key]['toUser']=$value['cancel_user'];
    //     $task[$key]['new_status']=htmlspecialchars($value['cancel_content']);
    //     }else{
    //      $issue_id=$value['id'];
    //      $sql="select a.*,u.username,g.group from team_ipad.history as a left join user_list as u on u.id=a.user_id left join `group` as g on a.assign=g.id where a.issue_list_id= '$issue_id' order by a.id desc";
    //      $history=$db->query($sql);
    //      $status='';
    //      foreach($history as $v){
    //      if(!empty($v['status'])){
    //      $status=$v['status'];
    //      break;
    //      }
    //      }
    //      $task[$key]['new_status']=htmlspecialchars($status);
    //     }
    //     if ($value['step']=='4') {
    //         $task[$key]['toUser']=$value['done_user'];
    //     }


    //     }
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

function search_all_filter($db,$depId, $userId)
{
    session_start();
    $project_id=$_SESSION['cooper_user_info'][0]['project_id'];
    $where =' where 1';
    $where1='where1';
    if ($project_id!=null&&$project_id!='') {
        $project_id=explode("|", $project_id);
        for ($i=0;$i<count($project_id);$i++) {
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
    if ($stageId!=null&&$stageId!='') {
        $stageId=explode("|", $stageId);
        for ($j=0;$j<count($stageId);$j++) {
            if (count($stageId) > 1) {
                if ($j == 0) {
                    $where1 .= "  and ( FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
                    $where .= "  and ( FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
                } elseif ($j == count($stageId) - 1) {
                    $where1 .= " OR FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ) ";
                    $where .= " OR FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ) ";
                } else {
                    $where1 .= " OR FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
                    $where .= " OR FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
                }
            } else {
                $where1 .= " and FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
                $where .= " and FIND_IN_SET('$stageId[$j]',replace(f.build_id, '|',',')) ";
            }
        }
    }
    if ($stationId!=null&&$stationId!='') {
        $stationId=explode("|", $stationId);
        for ($k=0;$k<count($stationId);$k++) {
            if (count($stationId) > 1) {
                if ($k == 0) {
                    $where .= "  and ( FIND_IN_SET('$stationId[$k]',replace(f.station_id, '|',',')) ";
                } elseif ($k == count($stationId) - 1) {
                    $where .= " OR FIND_IN_SET('$stationId[$k]',replace(f.station_id, '|',',')) ) ";
                } else {
                    $where .= " OR FIND_IN_SET('$stationId[$k]',replace(f.station_id, '|',',')) ";
                }
            } else {
                $where .= " and FIND_IN_SET('$stationId[$k]',replace(f.station_id, '|',',')) ";
            }
        }
    }
   
    $where .=" and( f.user_id='1' or f.user_id='$userId') and product_id='14'";
    $countWhere = $where;
    $where .="limit $page,$limit ";
    $account_select_sql = "SELECT f.* FROM `filter` as f $where ";
    $account_select_result = $db->query($account_select_sql);
    $account_select_count_select = "SELECT count(1) as num from filter as f $countWhere";
    $account_select_count_result = $db->query($account_select_count_select,'Row');
    if ($account_select_result) {
        for ($i = 0; $i < count($account_select_result); $i++) {
            $username = $account_select_result[$i]['user_id'];
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
    }
    $result_array = array(
        "code" => 0,
        "msg" => "success",
        "count" => $account_select_count_result['num'],
        "data" => $array,
    );
    // $msg = $result_array;
    return $result_array;

}
function taskMyListInit($db, $post, $choose)
{   
    $userId = $post['userId'];
    // $page = $post['page'];
    // $limit = $post['limit'];
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

function taskAllListInit($db, $post, $choose,$depId)
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
    $task_count_select_sql = "SELECT count(a.`id`) as num from team_ipad.issue_list as a";
    $task_count_select_result = $db->query($task_count_select_sql);
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
    // $history_select_sql1="SELECT * FROM (SELECT `id`,`issue_list_id`,`status` FROM team_ipad.history ORDER BY `update_time` DESC)b1 GROUP BY `issue_list_id`  ";
    // $history_select_sql1_result=$db->query($history_select_sql1);
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
    // 数据返回接口格式
    // $task_result = array(
    //     "code" => 0,
    //     "msg" => "success",
    //     "count" => $task_count_select_result[0]['num'],
    //     "data" =>  $task_select_result,
    // );
    $temp=array();$temp1=array();$temp2=array();$temp3=array();$temp4=array();$temp5=array();
    for($m=0;$m<count($task_select_result);$m++){
        $project=array($task_select_result[$m]['project']);
        $build=array($task_select_result[$m]['build']);
        $station=array($task_select_result[$m]['station']);
        $toUser=array($task_select_result[$m]['toUser']);
        $requestor=array($task_select_result[$m]['requestor']);
        $eta=array($task_select_result[$m]['eta']);
        $temp[]= $project[0];$temp1[]= $build[0];$temp2[]= $station[0];
        $temp3[]=$toUser[0];$temp4[]=$requestor[0];$temp5[]=$eta[0];
    }
    $task_result = array(
      "project"=>array_merge(array_unique($temp)),
      "build"=>array_merge(array_unique($temp1)),
      "station"=>array_merge(array_unique($temp2)),
      "toUser"=>array_merge(array_unique($temp3)),
      "requestor"=>array_merge(array_unique($temp4)),
      "eta"=>array_merge(array_unique($temp5)),
);

    return $task_result;
}
function chooseTable($db, $userId, $choose)
{
    $chooseId = $choose . "_id"; 
    //$choose_select_sql = "SELECT b.`id`,b.`product_id`,b.`project_id`,b.`build_id`,b.`station_id`,b.`status_id` from `user_list` as a left join `$choose` as b on a.`$chooseId` = b.`id` where a.`id` = '$userId' and product_id = '14' ";
    $choose_select_sql = "SELECT `id`,`product_id`,`project_id`,`build_id`,`station_id`,`status_id` from `$choose` where product_id = '14' and `user_id` = '$userId' ";
    $choose_select_result = $db->query($choose_select_sql);
    return $choose_select_result;
}
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
function searchProduct($db, $userId)
{
    $product_select_sql = "SELECT `product_id` FROM `user_list` WHERE `id` = '$userId'";
    $product_select_result = $db->query($product_select_sql);
    return $product_select_result;
}