<?php
require_once "../Include/db_connect.php";

// error_reporting(0);
$action = $_GET['action'];
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}





switch ($action) {
    case 'create_search':
        $msg = create_search($db);
        break;
    case 'create_task':
        $msg = create_task($db);
        break;
    case 'station_search':
        $msg = station_search($db);
        break;
    case 'station_search1':
        $msg = station_search1($db);
        break;
    case 'station_search2':
        $msg = station_search2($db);
        break;
    // case 'station_search2':
    //     $msg = station_search2($db);
    //     break;
    default:
        $msg = "参数错误";
        break;
}
echo json_encode($msg);




function create_search($db)
{
    //根据传递的用户id判断他有几个产品的权限
    $user_id=$_SESSION['cooper_user_info'][0]['id'];
    $level=$_SESSION['cooper_user_info'][0]['level'];
    // $product_id=$_SESSION['cooper_user_info']['product_id'];
    $userproject=$_SESSION['cooper_user_info']['project_id'];
    // var_dump($product_id);die;


    // $product_id=explode('|',$product_id);
    // foreach($product_id as $v){
    //     $product_sql="select * from `product` where enable='1' and id='$v'";
    //     $product_res=$db->query($product_sql);
    //     $product[]= $product_res[0];

    // }
    $product_sql="select * from `product` where enable='1' and product='ipad'";
    $product_res=$db->query($product_sql);
    $product= $product_res;
    $where="";
    // var_dump($_POST['product_id']) ;

    $get_product_id=$product[0]['id'];
    if($userproject!=''&&$userproject!=NULL){
        $project_id=explode('|', $userproject) ;
        for ($i = 0; $i < count($project_id); $i++) {
            if (count($project_id) > 1) {
                if ($i == 0) {
                    $where .= "  and ( pe.`id` = '$project_id[$i]' ";
                } else if ($i == count($project_id) - 1) {
                    $where .= " OR pe.`id` = '$project_id[$i]' ) ";
                } else {
                    $where .= " OR pe.`id` = '$project_id[$i]' ";
                }
            } else {
                $where .= " and  pe.`id` = '$project_id[$i]' ";
            }
        }
    }

    $sql="select * from product_project as pp left join project as pe on 
    pp.project_id=pe.id where pp.product_id='$get_product_id' and pe.`enable`='1' and pp.`enable`='1' ORDER by pe.id desc";
   

    $sql.= $where;
    // var_dump("$sql");
    $project=$db->query($sql);


    $station_sql="select  DISTINCT s.station ,s.id  from station as s 
    left join station_project as sp  on  s.id= sp.station 
    LEFT JOIN project as p on p.id=sp.project
    left join product_project as pjp on pjp.project_id = p.id
    left join product as pd on pd.id=pjp.product_id
    where pd.id=$get_product_id and sp.`enable`='1' and pjp.`enable`='1' ORDER by s.station asc";
    $station_res=$db->query($station_sql);

    if(empty($_POST['project_id'])){
        $get_project_id=$project[0]['project_id'];
        $build_sql="select b.* from build b left join build_project bp on b.id =bp.build_id where bp.project_id=  $get_project_id and bp.`enable`='1'";
    }else{
        $get_project_id=trim($_POST['project_id']);
        $build_sql="select b.* from build b left join build_project bp on b.id =bp.build_id where bp.project_id=  $get_project_id and bp.`enable`='1'";
    }




   
    // $build_sql="select * from build";


    $build_res=$db->query($build_sql);
    
    // $station_sql="select * from station";
    // $station_res=$db->query($station_sql);

    $group_sql="select * from `group` where 1 and (`product_id` like '14' or `product_id` like '14|%' or `product_id` like '%|14|%' or `product_id` like '%|14') and `enable` = '1'";
    $group_res=$db->query($group_sql);

    $pro_id = 14;
    $appleDRI_sql="select * from user_list where level='2' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and enable='1' ORDER by username asc";
    $appleDRI_res=$db->query($appleDRI_sql);
    

    $requestor_sql="select id,username from user_list where username!='admin' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and enable='1' ORDER by username asc ";
    $requestor_res=$db->query($requestor_sql);

    $search_data['product']=$product;       
    $search_data['project']=$project;
    $search_data['build']=$build_res;
    $search_data['station']=$station_res;
    $search_data['group']=$group_res;
    $search_data['appleDRI']=$appleDRI_res;
    $search_data['requestor']=$requestor_res;

    return $search_data;
}


function create_task($db){
    $create_time=date('Y-m-d H:i:s',time());
    $step='1';//创建step=1
    $access_user_id=$_SESSION['cooper_user_info'][0]['id'];
    $create_user_id=$_SESSION['cooper_user_info'][0]['id'];
    $status_id='1';//ongoing状态

    
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

    $uploadpath=@implode(',',$uploadpath);
    $olduploadpath=@implode(',',$olduploadpath);


    $station_user_id=trim($_POST['station_user_id']);
    $station_user_id=explode(',',$station_user_id);


    $station=sqlSafe($_POST['station']);
    $station= explode(',',$station);

    foreach($station as $key=>$value){
        $product_id=trim($_POST['product']);
        $project_id=trim($_POST['project']);
        $sql="select * from station where station='$value'";
        $station_result=$db->query($sql);
        if($station_result){
            $station_id=$station_result[0]['id'];
            $select_sql="select * from station_project where station='$station_id' and project='$project_id'";
            $select_result=$db->query($select_sql);
            if($select_result){
                if('0' == $select_result[0]['enable']){
                    $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
                $db->query($sql);
            }
        }else{
            $sql="insert into station (`station`) values ('$value')";
            $db->query($sql);
            $sql="select * from station where station = '$value'";
            $station_res=$db->query($sql);
            $station_id=$station_res[0]['id'];
            $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
            $db->query($sql);
        }

        $build=sqlSafe($_POST['build']);
    


        $sql="select * from build where build='$build'";
        $build_result=$db->query($sql);
        if($build_result){
            $build_id=$build_result[0]['id'];
            
            $bp_sql="SELECT * from build_project where `build_id`='$build_id' and `project_id`='$project_id' ";
            $bp_sql_result=$db->query($bp_sql);
 
            if($bp_sql_result){
                if('0' == $bp_sql_result[0]['enable']){
                    $update_sql="update build_project set `enable`='1' where `build_id`='$build_id' and `project_id`='$project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
                $db->query($sql);
            }



        }else{
            $sql="insert into build (`build`) values ('$build')";
            $db->query($sql);
            $sql="select * from build where build = '$build'";
            $build_res=$db->query($sql);
            $build_id=$build_res[0]['id'];
             
            $sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
            $db->query($sql);
            
        }


        // $requestor=trim($_POST['Requestor']);
        // $req_sql="select * from user_list where username='$requestor'";
        // $req_result=$db->query($req_sql);
        // if($req_result){
        //     $req_id=$req_result[0]['id'];
        // }else{
        //     $req_insert="insert into user_list (`username`,`password`,`enable`) values ('$requestor','123456','1')";
        //     $db->query($req_insert);
        //     $req_sql="select * from user_list where username='$requestor'";
        //     $req_result=$db->query($req_sql);
        //     $req_id=$req_result[0]['id'];
        // }
        $req_id=trim($_POST['Requestor']);
      
        // $task_title=sqlSafe($_POST['task_title']);
        // $task_description=sqlSafe($_POST['task_description']);
        $task_title=$_POST['task_title'];
        $task_description=$_POST['task_description'];

        $eta=trim($_POST['eta']);
        $assign=trim($_POST['assign']);//获取assign下个部门的id，并存储id


        $appleDRI=trim($_POST['appleDRI']);

        $apple_sql="select * from user_list  where level='2' and username='$appleDRI'";
        $apple_result=$db->query($apple_sql);
        if($apple_result){
            $apple_id=$apple_result[0]['id'];
        }else{
            $apple_insert="insert into user_list (`username`,`password`,`enable`,`level`,`group_id`) values ('$appleDRI','123456','1','2','13')";
            // var_dump($apple_insert);
            $db->query($apple_insert);
            $apple_sql="select * from user_list where username='$appleDRI' and  level='2' ";
            $apple_result=$db->query($apple_sql);
            $apple_id=$apple_result[0]['id'];
        }


        $Priority=trim($_POST['Priority']);
        // var_dump($_POST)
        //  die();


        $check_user=$station_user_id[$key];
        $create_check_user=$station_user_id[$key];

        $pj_sql="SELECT * from project where `id`='$project_id' ";
        $pj_sql_result=$db->query($pj_sql);
        $pj_no=$pj_sql_result[0]['project'];
        $pj_issue_sql="SELECT no from team_ipad.issue_list where `project_id`='$project_id' order by id desc ";
        $pj_issue_sql_result=$db->query($pj_issue_sql);
        $pj_issue_no=$pj_issue_sql_result[0]['no'];

        $start=strlen($pj_no);
        $end=strlen($pj_issue_no)-strlen($pj_no);
        $shuzi=substr($pj_issue_no,$start,$end)+1;
        if(strlen($shuzi)<6){
            $supply='';
            for($i=0;$i<6-strlen($shuzi);$i++){
                $supply.='0';
            }
            $no = $pj_no.$supply.$shuzi;
        }else{
            $no = $pj_no.$shuzi;
        }
        $sql="insert into team_ipad.issue_list (no,create_user_id,product_id,project_id,build_id,
        station_id,task_title,task_description,uploadpath,olduploadpath,eta,create_time,step,access_user_id,assign,create_assign,status_id,new_status,check_user,create_check_user,
        appleDRI,priority,requestor_id) values 
        ('$no','$create_user_id','$product_id','$project_id','$build_id','$station_id','$task_title',
        '$task_description','$uploadpath','$olduploadpath','$eta','$create_time','$step','$access_user_id','$assign','$assign','$status_id','','$check_user','$create_check_user',
        '$apple_id','$Priority','$req_id')";
        // echo $sql;die;
        $db->query($sql);
          

    }


    if(sqlSafe($_POST['sync'])=='true'){
        

        $Priority=trim($_POST['Priority']);

        $product_id=sqlSafe($_POST['sync_product']);

        $project_id=sqlSafe($_POST['sync_project']);

        $build=sqlSafe($_POST['sync_build']);
        $sql="select * from build where build='$build'";
        $sync_build=$db->query($sql);
        if($sync_build){
            $build_id=$sync_build[0]['id'];

            $bp_sql="SELECT * from build_project where `build_id`='$build_id' and `project_id`='$project_id' ";
            $bp_sql_result=$db->query($bp_sql);
 
            if($bp_sql_result){
                if('0' == $bp_sql_result[0]['enable']){
                    $update_sql="update build_project set `enable`='1' where `build_id`='$build_id' and `project_id`='$project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
                $db->query($sql);
            }
        }else{
            $sql="insert into build (`build`) values ('$build')";
            $db->query($sql);
            $sql="select * from build where build = '$build'";
            $build_res=$db->query($sql);
            $build_id=$build_res[0]['id'];

            $sql="INSERT into build_project (`build_id`,`project_id`,`enable`) values ('$build_id','$project_id','1') ";
            $db->query($sql);
        }

        $sync_station_user_id=sqlSafe($_POST['sync_station_user_id']);
        $sync_station_user_id=explode(',',$sync_station_user_id);
        $station=sqlSafe($_POST['sync_station']);
        $station= explode(',',$station);
        foreach($station as $k=> $value){
            $sql="select * from station where station='$value'";
            $sync_station=$db->query($sql);
            if($sync_station){
                $station_id=$sync_station[0]['id'];
                $select_sql="select * from station_project where station='$station_id' and project='$project_id'";
                $select_result=$db->query($select_sql);
                if($select_result){
                    if('0' == $select_result[0]['enable']){
                        $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$project_id'";
                        $db->query($update_sql);
                    }
                }else{
                    $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
                    $db->query($sql);
                }
            }else{
                $sql="insert into station (`station`) values ('$value')";
                $db->query($sql);
                $sql="select * from station where station = '$value'";
                $station_res=$db->query($sql);
                $station_id=$station_res[0]['id'];
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
                $db->query($sql);
            }

            $sync_check_user=$sync_station_user_id[$k];
            $create_sync_check_user=$sync_station_user_id[$k];

            $pj_sql="SELECT * from project where `id`='$project_id' ";
            $pj_sql_result=$db->query($pj_sql);
            $pj_no=$pj_sql_result[0]['project'];
            $pj_issue_sql="SELECT no from team_ipad.issue_list where `project_id`='$project_id' order by id desc ";
            $pj_issue_sql_result=$db->query($pj_issue_sql);
            $pj_issue_no=$pj_issue_sql_result[0]['no'];

            $start=strlen($pj_no);
            $end=strlen($pj_issue_no)-strlen($pj_no);
            $shuzi=substr($pj_issue_no,$start,$end)+1;
            if(strlen($shuzi)<6){
                $supply='';
                for($i=0;$i<6-strlen($shuzi);$i++){
                    $supply.='0';
                }
                $no = $pj_no.$supply.$shuzi;
            }else{
                $no = $pj_no.$shuzi;
            }

            $sql="insert into team_ipad.issue_list (no,create_user_id,product_id,project_id,build_id,
            station_id,task_title,task_description,uploadpath,olduploadpath,eta,create_time,step,access_user_id,assign,create_assign,status_id,new_status,check_user,create_check_user, appleDRI,priority,requestor_id) values 
            ('$no','$create_user_id','$product_id','$project_id','$build_id','$station_id','$task_title',
            '$task_description','$uploadpath','$olduploadpath','$eta','$create_time','$step','$access_user_id','$assign','$assign','$status_id','','$sync_check_user','$create_sync_check_user','$apple_id','$Priority','$req_id')";
            $db->query($sql);


        }

    }
    $msg="success";
    return $msg;
}


// function station_search($db)
// {
//     $assign = trim($_POST['assign']);
//     $station = trim($_POST['station']);
//     $project_id = trim($_POST['project']);
    
//     $station = explode(',', $station);


//     $data = array();
//     foreach ($station as $value) {
//         $sql = "select * from station where station='$value'";
//         $station_result = $db->query($sql);
//         if ($station_result) {
//             $station_id = $station_result[0]['id'];
//         } else {
//             $sql = "insert into station (`station`) values ('$value')";
//             $db->query($sql);
//             $sql = "select * from station where station = '$value'";
//             $station_res = $db->query($sql);
//             $station_id = $station_res[0]['id'];
//         }
//         // $sql = "select * from station_user where station='$station_id' and group='$assign'";
//         $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
//         (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$project_id' )
//         as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
//          as e LEFT JOIN user_list as f ON
//        e.`user`=f.id) as x where x.`group`='$assign'";
//         $res = $db->query($sql);


//         if ($res) {
//             $data['station'][] = $res[0];
//         }

//     }

//     if(trim($_POST['sync'])=='true'){
//         $sync_station=trim($_POST['sync_station']);
//         $sync_project_id=trim($_POST['sync_project']);
//         $sync_station = explode(',', $sync_station);
//         foreach ($sync_station as $value) {
//             $sql = "select * from station where station='$value'";
//             $station_result = $db->query($sql);
//             if ($station_result) {
//                 $station_id = $station_result[0]['id'];
//             } else {
//                 $sql = "insert into station (`station`) values ('$value')";
//                 $db->query($sql);
//                 $sql = "select * from station where station = '$value'";
//                 $station_res = $db->query($sql);
//                 $station_id = $station_res[0]['id'];
//                 $sql = "insert into station_user (`project`,`station`) values ('$project_id','$station_id')";
//                 $db->query($sql);
//             }
//             // $sql = "select * from station_user where station='$station_id' and group='$assign'";
//             $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
//             (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$sync_project_id' )
//             as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
//              as e LEFT JOIN user_list as f ON
//            e.`user`=f.id) as x where x.`group`='$assign'";
//             $res = $db->query($sql);
    
    
//             if ($res) {
//                 $data['sync_station'][] = $res[0];
//             }
    
//         }
//     }

//     return $data;


// }

// function station_search1($db)
// {
//     $assign = trim($_POST['assign']);
//     $station = trim($_POST['station']);
//     $project_id = trim($_POST['project']);
    
//     $station = explode(',', $station);


//     $data = array();
//     foreach ($station as $value) {
//         $sql = "select * from station where station='$value'";
//         $station_result = $db->query($sql);
//         if ($station_result) {
//             $station_id = $station_result[0]['id'];
//         } else {
//             $sql = "insert into station (`station`) values ('$value')";
//             $db->query($sql);
//             $sql = "select * from station where station = '$value'";
//             $station_res = $db->query($sql);
//             $station_id = $station_res[0]['id'];
//         }
//         // $sql = "select * from station_user where station='$station_id' and group='$assign'";
//         $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
//         (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$project_id' )
//         as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
//          as e LEFT JOIN user_list as f ON
//        e.`user`=f.id) as x where x.`group`='$assign'";
//         $res = $db->query($sql);


//         if ($res) {
//             $data['station'][] = $res[0];
//         }

//     }

//     return $data;


// }

function station_search($db)
{
    $assign = sqlSafe($_POST['assign']);
    $station = sqlSafe($_POST['station']);
    $project_id = sqlSafe($_POST['project']);


    $station = explode(',', $station);


   $datas=array();
    foreach ($station as $value) {
        if(empty($value)){
            continue;
        }
        $data = array();
        $sql = "select * from station where station='$value'";
        $station_result = $db->query($sql);
        if ($station_result) {
            $station_id = $station_result[0]['id'];
            $select_sql="select * from station_project where station='$station_id' and project='$project_id'";
            $select_result=$db->query($select_sql);
            if($select_result){
                if('0' == $select_result[0]['enable']){
                    $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
                $db->query($sql);
            }
        } else {
            $sql = "insert into station (`station`) values ('$value')";
            $db->query($sql);
            $sql = "select * from station where station = '$value'";
            $station_res = $db->query($sql);
            $station_id = $station_res[0]['id'];
            $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
            $db->query($sql);
        }

        $prouct_sql = "SELECT * from product_project where project_id='$project_id'";
        $product_result = $db->query($prouct_sql);
        $pro_id = $product_result[0]['product_id'];

        $sql = "select id,username,product_id,project_id from user_list where group_id='$assign' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and (project_id is NULL or (project_id like '$project_id' or project_id like '$project_id|%' or project_id like '%|$project_id|%' or project_id like '%|$project_id' ) ) order by username asc ";
        $user = $db->query($sql);

       //  $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
       //  (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$project_id' )
       //  as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
       //   as e LEFT JOIN user_list as f ON
       // e.`user`=f.id) as x where x.`group`='$assign'";
       $sql = "select a.*,b.`group`,b.`user`,d.station,f.username from station_project as a 
        left join station_user as b on a.id=b.station_project_id 
        left join station as d on a.`station`=d.`id` 
        left join user_list as f on b.`user`=f.id 
        where a.station='$station_id' and a.project='$project_id' and b.`group`='$assign'";
    //    var_dump($sql) ; die;
        $res = $db->query($sql);
        $res=$res[0]['user'];
     
        foreach($user as $key=> $v){
            
            if($v['id']==$res){
                $user[$key]['checked']='1';
            }else{
                $user[$key]['checked']='2';
            }
        }


        $data['name']=$value;
        $data['user_list']=$user;
        $datas['station'][]=$data;
    }




    if (sqlSafe($_POST['sync']) == 'true') {
        $sync_station = sqlSafe($_POST['sync_station']);
        $sync_project_id = sqlSafe($_POST['sync_project']);
        $sync_station = explode(',', $sync_station);
        foreach ($sync_station as $value) {
            if(empty($value)){
                continue;
            }
            $data=array();
            $sql = "select * from station where station='$value'";
            $station_result = $db->query($sql);
            if ($station_result) {
                $station_id = $station_result[0]['id'];
                $select_sql="select * from station_project where station='$station_id' and project='$sync_project_id'";
                $select_result=$db->query($select_sql);
                if($select_result){
                    if('0' == $select_result[0]['enable']){
                        $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$sync_project_id'";
                        $db->query($update_sql);
                    }
                }else{
                    $sql = "insert into station_project (`project`,`station`,`enable`) values ('$sync_project_id','$station_id','1')";
                    $db->query($sql);
                }
            } else {
                $sql = "insert into station (`station`) values ('$value')";
                $db->query($sql);
                $sql = "select * from station where station = '$value'";
                $station_res = $db->query($sql);
                $station_id = $station_res[0]['id'];
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$sync_project_id','$station_id','1')";
                $db->query($sql);
            }
            // $sql = "select * from station_user where station='$station_id' and group='$assign'";
           //  $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
           //  (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$sync_project_id' )
           //  as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
           //   as e LEFT JOIN user_list as f ON
           // e.`user`=f.id) as x where x.`group`='$assign'";
            $sql = "select a.*,b.`group`,b.`user`,d.station,f.username from station_project as a 
        left join station_user as b on a.id=b.station_project_id 
        left join station as d on a.`station`=d.`id` 
        left join user_list as f on b.`user`=f.id 
        where a.station='$station_id' and a.project='$sync_project_id' and b.`group`='$assign'";

            $res = $db->query($sql);
            $res=$res[0]['user'];
            foreach($user as $key=> $v){
                if($v['id']==$res){
                    $user[$key]['checked']='1';
                }else{
                    $user[$key]['checked']='2';

                }
            }

            $data['name']=$value;
            $data['user_list']=$user;
            $datas['sync_station'][]=$data;


        }
    }


    return $datas;


}

function station_search1($db)
{
    $assign = sqlSafe($_POST['assign']);
    $station = sqlSafe($_POST['station']);
    $project_id = sqlSafe($_POST['project']);


    $station = explode(',', $station);


    $datas=array();
    foreach ($station as $value) {
        if(empty($value)){
            continue;
        }
        $data = array();
        $sql = "select * from station where station='$value'";
        $station_result = $db->query($sql);
        if ($station_result) {
            $station_id = $station_result[0]['id'];
            $select_sql="select * from station_project where station='$station_id' and project='$project_id'";
            $select_result=$db->query($select_sql);
            if($select_result){
                if('0' == $select_result[0]['enable']){
                    $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
                $db->query($sql);
            }
        } else {
            $sql = "insert into station (`station`) values ('$value')";
            $db->query($sql);
            $sql = "select * from station where station = '$value'";
            $station_res = $db->query($sql);
            $station_id = $station_res[0]['id'];
            $sql = "insert into station_project (`project`,`station`,`enable`) values ('$project_id','$station_id','1')";
            $db->query($sql);
        }

        
        // $sql = "SELECT id,username,product_id,project_id from user_list where group_id='$assign'  ORDER by username asc ";
        $prouct_sql = "SELECT * from product_project where project_id='$project_id'";
        $product_result = $db->query($prouct_sql);
        $pro_id = $product_result[0]['product_id'];

        $sql = "select id,username,product_id,project_id from user_list where group_id='$assign' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and (project_id is NULL or (project_id like '$project_id' or project_id like '$project_id|%' or project_id like '%|$project_id|%' or project_id like '%|$project_id' ) ) order by username asc ";
        $user = $db->query($sql);


       //  $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
       //  (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$project_id' )
       //  as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
       //   as e LEFT JOIN user_list as f ON
       // e.`user`=f.id) as x where x.`group`='$assign'";

        $sql = "select a.*,b.`group`,b.`user`,d.station,f.username from station_project as a 
        left join station_user as b on a.id=b.station_project_id 
        left join station as d on a.`station`=d.`id` 
        left join user_list as f on b.`user`=f.id 
        where a.station='$station_id' and a.project='$project_id' and b.`group`='$assign'";

        $res = $db->query($sql);
        $res=$res[0]['user'];
     
        foreach($user as $key=> $v){
            
            if($v['id']==$res){
                $user[$key]['checked']='1';
              
            }else{
                $user[$key]['checked']='2';

            }
        }


        $data['name']=$value;
        $data['user_list']=$user;
        $datas['station'][]=$data;
    }


    return $datas;


}

function station_search2($db)
{
    $assign = sqlSafe($_POST['assign']);
    $sync_station=sqlSafe($_POST['sync_station']);
    $sync_project_id=sqlSafe($_POST['sync_project']);
    $sync_station = explode(',', $sync_station);

    $datas = array();
    foreach ($sync_station as $value) {
        if(empty($value)){
            continue;
        }
        $data = array();
        $sql = "select * from station where station='$value'";
        $station_result = $db->query($sql);
        if ($station_result) {
            $station_id = $station_result[0]['id'];
            $select_sql="select * from station_project where station='$station_id' and project='$sync_project_id'";
            $select_result=$db->query($select_sql);
            if($select_result){
                if('0' == $select_result[0]['enable']){
                    $update_sql="update station_project set `enable`='1' where station='$station_id' and project='$sync_project_id'";
                    $db->query($update_sql);
                }
            }else{
                $sql = "insert into station_project (`project`,`station`,`enable`) values ('$sync_project_id','$station_id','1')";
                $db->query($sql);
            }
        } else {
            $sql = "insert into station (`station`) values ('$value')";
            $db->query($sql);
            $sql = "select * from station where station = '$value'";
            $station_res = $db->query($sql);
            $station_id = $station_res[0]['id'];
            $sql = "insert into station_project (`project`,`station`) values ('$sync_project_id','$station_id')";
            $db->query($sql);
        }


        // $sql = "select id,username from user_list where group_id='$assign' ORDER by username asc ";
        $prouct_sql = "SELECT * from product_project where project_id='$project_id'";
        $product_result = $db->query($prouct_sql);
        $pro_id = $product_result[0]['product_id'];

        $sql = "select id,username,product_id,project_id from user_list where group_id='$assign' and (product_id like '$pro_id' or product_id like '$pro_id|%' or product_id like '%|$pro_id|%' or product_id like '%|$pro_id') and (project_id is NULL or (project_id like '$project_id' or project_id like '$project_id|%' or project_id like '%|$project_id|%' or project_id like '%|$project_id' ) ) order by username asc ";
        $user = $db->query($sql);


           //  $sql = "select * from (select e.*,f.username from (select c.*,d.station as station_name from
           //  (select a.*,b.`group`,b.`user` from (select * from station_project where station='$station_id' and project='$sync_project_id' )
           //  as a LEFT JOIN station_user as b on a.id=b.station_project_id) as c LEFT JOIN station as d on c.`station`=d.`id`)
           //   as e LEFT JOIN user_list as f ON
           // e.`user`=f.id) as x where x.`group`='$assign'";
        $sql = "select a.*,b.`group`,b.`user`,d.station,f.username from station_project as a 
        left join station_user as b on a.id=b.station_project_id 
        left join station as d on a.`station`=d.`id` 
        left join user_list as f on b.`user`=f.id 
        where a.station='$station_id' and a.project='$sync_project_id' and b.`group`='$assign'";
            $res = $db->query($sql);
            $res=$res[0]['user'];
            // return $res;
            foreach($user as $key=> $v){
                if($v['id']==$res){
                    $user[$key]['checked']='1';
                }else{
                    $user[$key]['checked']='2';

                }
            }

            $data['name']=$value;
            $data['user_list']=$user;
            $datas['sync_station'][]=$data;


    }

    return $datas;
}















?>