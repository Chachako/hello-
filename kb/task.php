<?php
require_once "../Include/db_connect.php";
session_start();
if(empty($_SESSION['cooper_user_info'])){
header("location:../index.html");
}

$action = $_GET['action'];
// var_dump($action);die;


switch ($action) {
    case 'task':
        $msg = task($db);
        break;
    case 'task_update':
        $msg = task_update($db);
        break;
    case 'task_block':
        $msg = task_block($db);
        break;
    case 'task_cancel':
        $msg = task_cancel($db);
        break;
    case 'task_done':
        $msg = task_done($db);
        break;
    case 'update_eta':
        $msg = update_eta($db);
        break;
    case 'task_select_user':
        $msg = task_select_user($db);
        break;
    case 'task_assign_user':
        $msg = task_assign_user($db);
        break;
    case 'clear_message':
        $msg = clear_message($db);
        break;
    default:
        $msg = "参数错误";
        break;
}
echo json_encode($msg);

function task_select_user($db)
{
    session_start();
    $user_id = $_SESSION['cooper_user_info'][0]['id'];
    $task_id = trim($_GET['task_id']);


    $sql = "select * from user_list where id='$user_id'";
    $user = $db->query($sql);
    $level = $user[0]['level'];
    $group = $user[0]['group_id'];

    $group_category = $user[0]['group_category'];

    $select_sql = "select product_id,project_id,check_user from team_keyboard.issue_list where `id`='$task_id'";
    $select_sql_result = $db->query($select_sql);
    $pro_id = $select_sql_result[0]['product_id'];
    $project_id = $select_sql_result[0]['project_id'];
    $check_user=$select_sql_result[0]['check_user'];

    // $sql = "select id,username from user_list where `group_id`='$group' group by username";
    $sql = "select id,username,product_id,project_id from user_list where id!='$check_user' and group_id='$group' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and (project_id is NULL or (project_id like '$project_id' or project_id like '$project_id|%' or project_id like '%|$project_id|%' or project_id like '%|$project_id' ) ) order by username asc ";

    $user_list = $db->query($sql);
    return $user_list;
}

function task_assign_user($db)
{
    session_start();
    $user_id = $_SESSION['cooper_user_info'][0]['id'];
    $task_id = sqlSafe($_POST['task_id']);


    $assign_user = sqlSafe($_POST['assign_user']);
    $check_user=$assign_user;
    // $assign_user = explode(',',$assign_user);
    // $user_array=array();
    // foreach($assign_user as $value){
    //     $user_array[]= $value;
    // }

    // $sql = "select * from team_keyboard.issue_list where id='$task_id'";
    // $check_user = $db->query($sql);
    // $check_user = $check_user[0]['check_user'];
    // $check_user = explode(',', $check_user);
    // foreach($check_user as $value){
    //     if(!in_array($value,$user_array)){
    //         $user_array[]=$value;
    //     }
    // }
    // $check_user=implode(',',$user_array);
    // return $check_user;
    if(empty($task_id)){
        die;
    }
    $sql="update team_keyboard.issue_list set check_user='$check_user' where id='$task_id' ";
    $res=$db->execSql($sql);
    if($res){
        $msg='success';
        return $msg;
    }    
}

function task($db){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $group_category=$_SESSION['cooper_user_info'][0]['group_category'];
    $task_id=sqlSafe($_GET['task_id']);

    $sql="select * from user_list where id='$user_id'";
    $user=$db->query($sql);
    $level=$user[0]['level'];


    $sql="select * from team_keyboard.issue_list where id='$task_id'";
    $access_user_id=$db->query($sql);
    $access_user_id=$access_user_id[0]['access_user_id'];
    $access_user_id=explode(',',$access_user_id);
    if(!in_array($user_id,$access_user_id)){
    $access_user_id[]=$user_id;
    $access_user_id= implode(',',$access_user_id);
    if(empty($task_id)){
        die;
    }
    $sql="update team_keyboard.issue_list set access_user_id='$access_user_id' where id='$task_id'";
    $db->query($sql);
    }
    
    



    //     $sql="select x.*,g.`group` as create_user_group_name , ul.username as requestor ,ult.username as dri  from (select h.*,g.`group` from `group` as g LEFT JOIN
    // (select f.*,u.username,u.phone as create_user_phone,u.`group_id` as create_user_group ,u.email ,u.long_phone  from user_list as u left JOIN
    // (select e.*,`status`.`status` from
    // (select d.*,st.station from
    // (select c.*,bd.build from
    // (select b.*,pj.project from
    // (select a.*,pd.product from team_keyboard.issue_list as a LEFT JOIN product as pd on a.product_id=pd.id) as b LEFT JOIN project as pj on b.project_id=pj.id) as c
    // LEFT JOIN build as bd on c.build_id=bd.id) as d LEFT JOIN station as st on d.station_id=st.id)
    // as e LEFT JOIN status as `status` on e.status_id=`status`.`id` ) as f on u.id=f.create_user_id) as h on g.id=h.create_assign where h.id='$task_id') as x
    // LEFT JOIN `group` as g on x.create_user_group=g.id  
    //  left join `user_list` as ul on x.requestor_id= ul.id  left join `user_list` as ult on x.appleDRI= ult.id";
     $sql="select a.*,pd.product,pj.project,bd.build,st.station,`status`.`status`,u.username,u.phone as create_user_phone,u.`group_id` as create_user_group ,u.email ,u.long_phone,g.`group`,gp.`group` as create_user_group_name , ul.username as requestor ,ult.username as dri 
from team_keyboard.issue_list as a
LEFT JOIN `product` as pd on a.product_id=pd.id 
LEFT JOIN `project` as pj on a.project_id=pj.id 
LEFT JOIN `build` as bd on a.build_id=bd.id 
LEFT JOIN `station` as st on a.station_id=st.id 
LEFT JOIN `status` as `status` on a.status_id=`status`.`id`
left JOIN `user_list` as u on a.create_user_id=u.id 
LEFT JOIN `group` as g on a.create_assign=g.id
LEFT JOIN `group` as gp on u.`group_id`=gp.id  
left join `user_list` as ul on a.requestor_id= ul.id  
left join `user_list` as ult on a.appleDRI= ult.id where a.id='$task_id'";

     // var_dump($sql);die();

    
    $task=$db->query($sql);

     

        $create_check_user=$task[0]['create_check_user'];
        // $check_user=explode(',',$check_user);
        // $check_user1=$check_user[0];
        $sql="select * from user_list where id='$create_check_user'";
        $create_check_user=$db->query($sql);
        $task[0]['create_assign_user'] = $create_check_user[0]['username'];
        $task[0]['create_assign_user_phone'] = $create_check_user[0]['phone'];
        $task[0]['create_assign_longTel'] = $create_check_user[0]['long_phone'];
        $task[0]['create_assign_email'] = $create_check_user[0]['email'];

       $requestor_id=$task[0]['requestor_id'];

       $requ_sql="select * from user_list where id='$requestor_id'";
        $requ_user=$db->query($requ_sql);
        $task[0]['req_user_phone'] = $requ_user[0]['phone'];
        $task[0]['req_longTel'] = $requ_user[0]['long_phone'];
        $task[0]['req_email'] = $requ_user[0]['email'];
       $requ_group= $requ_user[0]['group_id'];
       $task[0]['requ_name'] = $requ_user[0]['id'];


      
        $apple_id=$task[0]['appleDRI'];
        $apple_sql="select * from user_list where id='$apple_id'";
        $apple_user=$db->query($apple_sql);

        $task[0]['apple_user_phone'] = $apple_user[0]['phone'];
        $task[0]['apple_longTel'] = $apple_user[0]['long_phone'];
        $task[0]['apple_email'] = $apple_user[0]['email'];

        $cancel_username=$task[0]['cancel_user'];
        $can_sql="SELECT  u.* ,g.`group` from user_list as  u   left join `group` as  g on u.`group_id`=g.`id`  where  u.`username`='$cancel_username' ";
        $can_sql_result=$db->query($can_sql);

        $task[0]['cancel_group']=$can_sql_result[0]['group'];



//     $sql="select x.*,g.`group` as update_group from (select d.*,e.username as assign_username,e.phone as assign_user_phone ,e.email as assign_email ,
//      e.long_phone as assign_user_longphone  from (select c.*,g.group from `group` as g RIGHT JOIN
//     (select b.*,u.username,u.phone,u.group_id ,u.email,u.long_phone  from user_list as u RIGHT JOIN
//     (select * from team_keyboard.history where issue_list_id= '$task_id' order by id) as b on u.id=b.user_id) as c on c.assign=g.id) as d LEFT JOIN user_list as e ON
// d.assign_user=e.id) as x LEFT JOIN `group` as g on x.group_id=g.id";
    $sql="select b.*,u.username,u.phone,u.group_id ,u.email,u.long_phone,g.group,e.username as assign_username,e.phone as assign_user_phone ,e.email as assign_email ,e.long_phone as assign_user_longphone,gp.`group` as update_group from 
team_keyboard.history as b
left join user_list as u on b.user_id=u.id
left join `group` as g on b.assign=g.id
LEFT JOIN user_list as e ON b.assign_user=e.id
LEFT JOIN `group` as gp on u.group_id=gp.id where b.issue_list_id= '$task_id' ";
    // var_dump($sql);die;
    if($task[0]['step'] == '3')
        $sql.=" and b.id != (select b.* from (select id from team_keyboard.history where issue_list_id='$task_id' order by id desc limit 1) as b) order by id";
    else
        $sql.=" order by id";
    $history_list = $db->query($sql);

    $data['task']=$task[0];
    $data['history']=$history_list;
    
    $data['task']['uploadpath']=explode(',',$data['task']['uploadpath']);
    $data['task']['olduploadpath']=explode(',',$data['task']['olduploadpath']);
    foreach($data['history'] as $key=>$value){
    $value['upload']=explode(',',$value['upload']);
    $value['oldupload']=explode(',',$value['oldupload']);
    $data['history'][$key]['upload']=$value['upload'];
    $data['history'][$key]['oldupload']=$value['oldupload'];
    }
    
    

    $check_user = $task[0]['check_user'];
    // var_dump($check_user);die;
  
    
//先判断当前task是否done或cancel，若是，则只能所有人只能看，若否，则根据下面分别判断
// if ($task[0]['step'] == '3' || $task[0]['step'] == '4') {
//     //cancel为3，done为4,just look
//     $data['content'] = 5;
// } else {
//     if ($level == 2) {
//         //im kehu
//         if ($task[0]['assign'] == $user[0]['group_id']) {
//             //upodate done block cancel
//             $data['content'] = 1;
//         } else {
//             //done cancel
//             $data['content'] = 2;
//         }
//     } else {
//         //im not kehu
//         if ($task[0]['create_user_id'] == $user[0]['id']) { //我创建
//             if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门


//                 if ($user_id== $check_user) { //在我手里
//                 // if (in_array($user_id, $check_user)) { //在我手里
//                     //update block cancel done
//                     $data['content'] = 1;
//                 } else { //不在我手里
//                     //done  cancel
//                     $data['content'] = 2;
//                 }


//             } else {
//                 //是我创建，不在我部门，done cancel
//                 $data['content'] = 2;
//             }
//         } else { //不是我创建
//             if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门


//                 if ($user_id== $check_user) { //在我手里
//                     //update,block
//                     $data['content'] = 4;
//                 } else { //不在我手里
//                     //just look
//                     $data['content'] = 5;
//                 }


//             } else {
//                 //不在我部门,just look
//                 $data['content'] = 5;
//             }
//         }


//     }
// }

//创建者
// // //先判断当前task是否done或cancel，若是，则只能所有人只能看，若否，则根据下面分别判断
// if ($task[0]['step'] == '3' || $task[0]['step'] == '4') {
//     //cancel为3，done为4,just look
//     $data['content'] = 5;
// } else {
//     if ($level == 2) {
//         //im kehu
//         if ($task[0]['assign'] == $user[0]['group_id']) {
//             //upodate done block cancel
//             $data['content'] = 1;
//         } else {
//             //done cancel
//             $data['content'] = 2;
//         }
//     } else {
//         //im not kehu
//         if ($task[0]['create_user_id'] == $user[0]['id']) { //我创建
//             if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门
//                 if ($group_category == '1') { //我是leader
//                     //update done block cancel,assign
//                     $data['content'] = 6;


//                 } else { //我不是leader
//                     if ($user_id== $check_user) { //在我手里
//                         //update done block cancel
//                         $data['content'] = 1;
//                     } else { //不在我手里
//                         //done  cancel
//                         $data['content'] = 2;
//                     }
//                 }
//             } else {
//                 //是我创建，不在我部门，done cancel
//                 $data['content'] = 2;
//             }
//         } else { //不是我创建
//             if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门
//                 if ($group_category == '1') { //我是leader
//                     //update,block,cancel,assign,done
//                     $data['content'] = 6;
//                 } else { //我不是leader
//                     if ($user_id== $check_user) { //在我手里
//                         //update,block
//                         $data['content'] = 4;
//                     } else { //不在我手里
//                         //just look
//                         $data['content'] = 5;
//                     }
//                 }
//             } else {
//                 //不在我部门,也不是我创建
//                 if($task[0]['create_user_group']==$user[0]['group_id'] && $group_category=='1'){
//                         $data['content']=2;
//                 }else{
//                         $data['content'] = 5;
//                 }
//             }
//         }


//     }
// }



 // 请求者
// //先判断当前task是否done或cancel，若是，则只能所有人只能看，若否，则根据下面分别判断
if ($task[0]['step'] == '3' || $task[0]['step'] == '4') {
    //cancel为3，done为4,just look
    $data['content'] = 5;
} else {
    if ($level == 2) {
        //im kehu
        if ($task[0]['assign'] == $user[0]['group_id']) {
            //upodate done block cancel
            $data['content'] = 1;
        } else {
            //done cancel
            $data['content'] = 2;
        }
    } else {
        //im not kehu
        if ($task[0]['requestor_id'] == $user[0]['id']|| $task[0]['create_user_id'] == $user[0]['id']) { //我是请求者 或者我是创建者
            if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门
                if ($group_category == '1') { //我是leader
                    //update done block cancel,assign
                    $data['content'] = 6;

                } else { //我不是leader
                    if ($user_id== $check_user) { //在我手里
                        //update done block cancel
                        $data['content'] = 1;
                    } else { //不在我手里
                        //done  cancel
                        $data['content'] = 2;
                    }
                }
            } else {
                //是我请求，不在我部门，done cancel
                $data['content'] = 2;
            }
        } else { //不是我请求 不是我创建

            if ($task[0]['assign'] == $user[0]['group_id']) { //在我部门
                if ($group_category == '1') { //我是leader
                    //update,block,cancel,assign,done
                    $data['content'] = 6;
                } else { //我不是leader
                    if ($user_id== $check_user) { //在我手里
                        //update,block
                        $data['content'] = 4;
                    } else { //不在我手里
                        //just look
                        $data['content'] = 5;
                    }
                }
            } else {
                //不在我部门,也不是我请求，也
                //我请求 在我部门
                   //task在请求者部门 &&是leader
                if($requ_group==$user[0]['group_id'] && $group_category=='1'){
                        $data['content']=2;
                }elseif ($task[0]['create_user_group']==$user[0]['group_id'] && $group_category=='1') {
                      $data['content']=2;
                }else{
                        $data['content'] = 5;
                }

               


            }
        }


    }
}
    
    

    return $data;
}


function task_update($db){
    session_start();
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
    $task_id=sqlSafe($_POST['task_id']); //传递task_id
    $update_time=date('Y-m-d H:i:s',time()); 
    $status=$_POST['status']; //传递status
    $assign=sqlSafe($_POST['assign']); //传递assign
    
    if(empty($_FILES)){
    $uploadpath='';
    $olduploadpath='';
    }else{
    for($i=0; $i<count($_FILES['upload']['name']); $i++) {
    
    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
    if ($tmpFilePath != ""){
    $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
    $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
    $oldFilePath = $_FILES['upload']['name'][$i];
    if(move_uploaded_file($tmpFilePath, $newFilePath)){
    $uploadpath[]=$newFilePath;
    $olduploadpath[]=$oldFilePath;
    }else{
    foreach($uploadpath as $value){
    unlink('./upload/'.$value);
    }
        $msg='error';
        return $msg;
    }
    }
    }
    $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
    $olduploadpath=implode(',',$olduploadpath);//原文件名字
    }
    
    if(empty($task_id)){
        die;
    }
    $sql="select * from team_keyboard.issue_list where id='$task_id'";
    $task=$db->query($sql);
    $task_group_id=$task[0]['assign'];//当前task所在的部门



    $assign_user_id=sqlSafe($_POST['assign_user_id']);//从前台传，不需要上边自己查了
    if($assign_user_id=='undefined')
    {
        $msg='error';
        return $msg; 
    }

    // $check_user = $task[0]['check_user'];
    // $check_user = explode(',', $check_user);
    // $new_check_user = array();
    // foreach ($check_user as $key => $value) {
    //     if ($value !== '') {
    //         $new_check_user[] = $value;
    //     }
    // }
    // if (!in_array($assign_user_id, $new_check_user)) {
    //     $new_check_user[] = $assign_user_id;
    // }
    $new_check_user = $assign_user_id;
    //end
    
    //overlay 版本
    $version=trim($_POST['version']);


    // var_dump($version);
    // die();


    if($task_group_id==$group_id){//若当前用户的部门和当前task所在的部门为同一个，则执行插入
        if($task[0]['step']=='3' or $task[0]['step']=='4'){
        $msg='error';
        return $msg;
        }else{
    
            if($version!=""&&$group_id==10){
                $sql="update team_keyboard.issue_list set `step`='1',`assign`='$assign',access_user_id='$user_id',status_id='1',check_user='$new_check_user' ,version='$version',new_status='$status' where id='$task_id'";
               
            }else{
                 $sql="update team_keyboard.issue_list set `step`='1',`assign`='$assign',access_user_id='$user_id',status_id='1',check_user='$new_check_user',new_status='$status'  where id='$task_id'";
            }
    
        $db->query($sql);
    
    
    
     if($assign==13)
     {
        $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`,`assign`,`assign_user`,`version`) values 
        ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath','$assign','','$version')";
    }else
    {
            $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`,`assign`,`assign_user`,`version`) values 
        ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath','$assign','$assign_user_id','$version')";
    }


    // die;
    $db->query($sql);
    $msg='success';
    return $msg;
    }
    }else{
    $msg='error';
    return $msg;
    }
    
    }
    
    
    function task_block($db){
        session_start();
        $user_id=$_SESSION['cooper_user_info'][0]['id'];
        $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
        $task_id=sqlSafe($_POST['task_id']); //传递task_id
        $update_time=date('Y-m-d H:i:s',time()); 
        $status=$_POST['status']; //传递status
        // $assign=trim($_POST['assign']); //传递assign.block还在当前部门，不需要assign
        
        if(empty($_FILES)){
        $uploadpath='';
        $olduploadpath='';
        }else{
        for($i=0; $i<count($_FILES['upload']['name']); $i++) {
        
        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
            $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
            $oldFilePath = $_FILES['upload']['name'][$i];
        if(move_uploaded_file($tmpFilePath, $newFilePath)){
        $uploadpath[]=$newFilePath;
        $olduploadpath[]=$oldFilePath;
        }else{
        foreach($uploadpath as $value){
        unlink('./upload/'.$value);
        }
        $msg='error';
        return $msg;
        }
        }
        }
        $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
        $olduploadpath=implode(',',$olduploadpath);//原文件名字
        }
        
        $sql="select * from team_keyboard.issue_list where id='$task_id'";
        $task=$db->query($sql);
        $task_group_id=$task[0]['assign'];//当前task所在的部门

        $version=trim($_POST['version']);


        if($task_group_id==$group_id){//若当前用户的部门和当前task所在的部门为同一个，则执行插入
        //block操作将step设为2，代表处于block状态
        if($task[0]['step']=='3' or $task[0]['step']=='4'){
        $msg='error';
        return $msg;
        }else{

             if($version!=""&&$group_id==10){
                $sql="update team_keyboard.issue_list set `step`='2',access_user_id='$user_id',status_id='2' ,version='$version',new_status='$status'  where id='$task_id'";
             }else{
                 $sql="update team_keyboard.issue_list set `step`='2',access_user_id='$user_id',status_id='2',new_status='$status'  where id='$task_id'";
             }



       
        $db->query($sql);
        
        $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`,`assign`,`assign_user`,`version`) values 
        ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath','$group_id','$user_id','$version')";
        $db->query($sql);
        $msg='success';
        return $msg;
        }
        }else{
        $msg='error';
        return $msg;
        }
        
        }
 


        function task_cancel($db){
      
            session_start();
            $user_id=$_SESSION['cooper_user_info'][0]['id'];
            $user=$_SESSION['cooper_user_info'][0]['username'];
            $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
            $level=$_SESSION['cooper_user_info'][0]['level'];
            $task_id=sqlSafe($_POST['task_id']); //传递task_id
            $update_time=date('Y-m-d H:i:s',time()); 
            
            $cancel_content=$_POST['cancel_content'];//cancel的理由
            
            $sql="select * from team_keyboard.issue_list where id='$task_id'";
            $task=$db->query($sql);
            
            $create_user_id=$task[0]['create_user_id'];
            $sql="select * from user_list where id='$create_user_id'";
            $user_res=$db->query($sql);
            $create_user_group_id=$user_res[0]['group_id'];
            $cancel_done_time=date('Y-m-d H:i:s',time());
            
            $version=trim($_POST['version']);

            //客户
            if($level==2){

            
            if($task[0]['step']=='3' or $task[0]['step']=='4'){ //已经有人cancel或者done了
            $msg='error';
            return $msg;
            }else{

                $status=$_POST['status'];
                $file=$_FILES;

           
            if(empty($status) && empty($file)){
                //     if($version==""){
                // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
                //     }else{
                // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version'  where id='$task_id'";        
                //     }

                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
                 //.    ,`assign`='13'
            $db->query($sql);
            $msg='success';
            return $msg;
            }else{
            $status=$_POST['status'];
            if(empty($_FILES)){
            $uploadpath='';
            $olduploadpath='';
            }else{
            for($i=0; $i<count($_FILES['upload']['name']); $i++) {
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
            if ($tmpFilePath != ""){
                $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
                $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
                $oldFilePath = $_FILES['upload']['name'][$i];
            if(move_uploaded_file($tmpFilePath, $newFilePath)){
            $uploadpath[]=$newFilePath;
            $olduploadpath[]=$oldFilePath;
            }else{
            foreach($uploadpath as $value){
            unlink('./upload/'.$value);
            }
            $msg='error';
            return $msg;
            }
            }
            }
            $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
            $olduploadpath=implode(',',$olduploadpath);//原文件名字
            }

            $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`) values 
            ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath')";
            $db->query($sql);
            
                //     if($version==""){
                // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
                //     }else{
                // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version'  where id='$task_id'";        
                //     }

            $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time'  where id='$task_id'";
            $db->query($sql);
            $msg='success';
            return $msg;
            }
            }
            }elseif($create_user_group_id==$group_id){     //不是客户
            if($task[0]['step']=='3' or $task[0]['step']=='4'){ //已经有人cancel或者done了 //创建者
            $msg='error';
            return $msg;
            }else{
                $status=$_POST['status'];
                $file=$_FILES;
            if(empty($status) && empty($file)){

                    if($version!=""&&$group_id==10){
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version' 
                 where id='$task_id'";   // ,`assign`='$group_id' 
                    }else{
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time'    where id='$task_id'";
                    }


            // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
            $db->query($sql);
            $msg='success';
            return $msg;
            }else{
            $status=$_POST['status'];
            if(empty($_FILES)){
            $uploadpath='';
            $olduploadpath='';
            }else{
            for($i=0; $i<count($_FILES['upload']['name']); $i++) {
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
            if ($tmpFilePath != ""){
                $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
                $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
                $oldFilePath = $_FILES['upload']['name'][$i];
            if(move_uploaded_file($tmpFilePath, $newFilePath)){
            $uploadpath[]=$newFilePath;
            $olduploadpath[]=$oldFilePath;
            }else{
            foreach($uploadpath as $value){
            unlink('./upload/'.$value);
            }
            $msg='error';
            return $msg;
            }
            }
            }
            $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
            $olduploadpath=implode(',',$olduploadpath);//原文件名字
            }
            $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`,`version`) values 
            ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath','$version')";
            $db->query($sql);

                    if($version!=""&&$group_id==10){
                     $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version' 
                       where id='$task_id'"; 
                    }else{
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time'     where id='$task_id'";
                    }


            // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
            $db->query($sql);
            $msg='success';
            return $msg;
            }
            }
            }else{
            //非客户，非创建者，只是一般的leader，先判断是否在自己部门
            if($task[0]['assign']==$group_id){ //在自己部门
            if($task[0]['step']=='3' or $task[0]['step']=='4'){ //已经有人cancel或者done了 
            $msg='error';
            return $msg;
            }else{
                $status=$_POST['status'];
                $file=$_FILES;
            if(empty($status) && empty($file)){
                 if($version!=""&&$group_id==10){
                 $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version'   where id='$task_id'"; 
                    }else{
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time'    where id='$task_id'";
                    }



            // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
            $db->query($sql);
            $msg='success';
            return $msg;
            }else{
            $status=$_POST['status'];
            if(empty($_FILES)){
            $uploadpath='';
            $olduploadpath='';
            }else{
            for($i=0; $i<count($_FILES['upload']['name']); $i++) {
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
            if ($tmpFilePath != ""){
                $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
                $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
                $oldFilePath = $_FILES['upload']['name'][$i];
            if(move_uploaded_file($tmpFilePath, $newFilePath)){
            $uploadpath[]=$newFilePath;
            $olduploadpath[]=$oldFilePath;
            }else{
            foreach($uploadpath as $value){
            unlink('./upload/'.$value);
            }
            $msg='error';
            return $msg;
            }
            }
            }
            $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
            $olduploadpath=implode(',',$olduploadpath);//原文件名字
            }
            $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`) values 
            ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath')";
            $db->query($sql);

             if($version!=""&&$group_id==10){
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time',version='$version' 
                 where id='$task_id'";   
                    }else{
                $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' 
                  where id='$task_id'";
                     
                    }
            // $sql="update team_keyboard.issue_list set `step`='3',cancel_content='$cancel_content',status_id='3',cancel_user='$user',cancel_done_time='$cancel_done_time' where id='$task_id'";
            $db->query($sql);
            $msg='success';
            return $msg;
            }
            }
            }else{
            $msg='error';
            return $msg;
            }
            
            }
            
            }
            
            function task_done($db){

                session_start();
                $user_id=$_SESSION['cooper_user_info'][0]['id'];
                $user=$_SESSION['cooper_user_info'][0]['username'];
                $group_id=$_SESSION['cooper_user_info'][0]['group_id'];
                $level=$_SESSION['cooper_user_info'][0]['level'];
                $task_id=sqlSafe($_POST['task_id']); //传递task_id
                $update_time=date('Y-m-d H:i:s',time()); 
                $done_content=sqlSafe($_POST['done_content']);//done 的评语
                $done_score=sqlSafe($_POST['done_score']); //done 的评分
                // var_dump($done_score);die;

                $version=sqlSafe($_POST['version']);
                $onlinetime=sqlSafe($_POST['onlinetime']);
                //默认好评
                if($done_content==''){
                    $done_content='OK!';
                }
                if($done_score=='0'){
                    $done_score='5';
                }
                $cancel_done_time=date('Y-m-d H:i:s',time());

                $sql="select * from team_keyboard.issue_list where id='$task_id'";
                $task=$db->query($sql);
                if($task[0]['step']=='3' or $task[0]['step']=='4'){ //已经有人cancel或者done了 //创建者
                $msg='error';
                return $msg;
                }else{
                    $status=$_POST['status'];
                    $file=$_FILES;

                  //如果status 和 file 都为空 则 不添加 history  
                if(empty($status) && empty($file)){
                    if($version!=""&&$group_id==10){
                    $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                     cancel_done_time='$cancel_done_time',onlinetime='$onlinetime',version='$version'     where id='$task_id'";
                    }else{
               
                     $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                     cancel_done_time='$cancel_done_time',onlinetime='$onlinetime'     where id='$task_id'";
                   }
                // $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                //      cancel_done_time='$cancel_done_time',onlinetime='$onlinetime'  where id='$task_id'";
                $db->query($sql);
                $msg='success';
                return $msg;
                }else{
                    $status=$_POST['status'];

                    if(empty($_FILES)){
                         $uploadpath='';
                         $olduploadpath='';
                         }else{
                for($i=0; $i<count($_FILES['upload']['name']); $i++) {
                $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                if ($tmpFilePath != ""){
                    $ext=pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION);
                    $newFilePath = "./upload/" .md5(time().mt_rand(1,1000000000)).'.'.$ext;
                    $oldFilePath = $_FILES['upload']['name'][$i];
                if(move_uploaded_file($tmpFilePath, $newFilePath)){
                $uploadpath[]=$newFilePath;
                $olduploadpath[]=$oldFilePath;
                }else{
                foreach($uploadpath as $value){
                unlink('./upload/'.$value);
                }
                $msg='error';
                return $msg;
                }
                }
                }
                $uploadpath=implode(',',$uploadpath);//在服务器保存的文件名字
                $olduploadpath=implode(',',$olduploadpath);//原文件名字
                }


                $sql="insert into team_keyboard.history (`issue_list_id`,`user_id`,`update_time`,`status`,`upload`,`oldupload`,`version`) values 
                ('$task_id','$user_id','$update_time','$status','$uploadpath','$olduploadpath','$version')";
                $db->query($sql);

                if($version!=""&&$group_id==10){
                    $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                    cancel_done_time='$cancel_done_time' ,onlinetime='$onlinetime',version='$version',new_status='$status'     where id='$task_id'";
                    }else{
                     $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                     cancel_done_time='$cancel_done_time' ,onlinetime='$onlinetime',new_status='$status'      where id='$task_id'";
                   }
                 // $sql="update team_keyboard.issue_list set `step`='4',done_content='$done_content',done_score='$done_score',status_id='4',done_user='$user',
                 // cancel_done_time='$cancel_done_time' ,onlinetime='$onlinetime',version='$version'  where id='$task_id'";
                $db->query($sql);
                $msg='success';
                return $msg;
                }
                }
                }
                
                
                
                
                        
            
            
            function update_eta($db){
             
                $update_eta=$_POST['update_eta_time'];
                $task_id=$_POST['task_id'];
                $sql="update team_keyboard.issue_list set `eta`='$update_eta' where id='$task_id'";
                $db->query($sql);
                $msg='success';
                return $msg;
            }

            function clear_message($db){ 
                session_start();
                $userId = $_SESSION['cooper_user_info'][0]['id'];
                $group_id = $_SESSION['cooper_user_info'][0]['group_id'];
                $product_id = $_SESSION['cooper_user_info'][0]['product_id'];
                $product_id = explode('|',$product_id);

                $where = ' and (';
                foreach($product_id as $value){
                    $where .= ' product_id='.$value;
                    $where .= ' or';
                }
                $where = substr($where, 0, -2);
                $where .= ')';

                $sql = "select id,task_title,access_user_id from team_keyboard.issue_list where assign='$group_id' and (step='1' or step='2') $where order by id desc";
                $sql_result = $db->query($sql);

                for ($i=0; $i < count($sql_result); $i++) { 
                    $task_id = $sql_result[$i]['id'];
                    $access_user_id = $sql_result[$i]['access_user_id'];
                    $access_user_id = explode(',',$access_user_id);
                    if(!in_array($userId, $access_user_id)){
                        $access_user_id[] = $userId;
                        $access_user_id = implode(',',$access_user_id);
                        if(empty($task_id)){
                            die;
                        }
                        $update_sql = "update team_keyboard.issue_list set access_user_id='$access_user_id' where id='$task_id'";
                        $db->execSql($update_sql);
                    }
                }
                
                $msg='success';
                return $update_sql;
            }
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
        
        
        




























?>