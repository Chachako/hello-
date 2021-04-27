<?php
require_once "./Include/db_connect.php";
$username = ($_POST['username']);
$password = md5(sqlSafe($_POST['password']));
$remember = sqlSafe($_POST['remember']); //是否自动登录标示

if ($username == '' || $password == '') {
    $msg = "Please enter your account and password";
} else {
    $row = checkUser($username, $password, $db);
    if (empty($row)) {
        $msg = "account or password is wrong";
    } else {

        
        session_start();

        // echo "路径：".session_save_path(); die;


        // $time=session_cache_expire();
        // var_dump( $time);
       
        $_SESSION['cooper_user_info'] = $row;


        $user_name=$_SESSION['cooper_user_info'][0]['username'];
         $user_id=$_SESSION['cooper_user_info'][0]['id'];

        $login_time=date('Y-m-d H:i:s',time());
        $insert_sql= "INSERT into login_time  (user_id,user_name,login_time) values ('$user_id','$user_name','$login_time' )";
        // var_dump($insert_sql);die;
        $db->query($insert_sql);

        
        if ($remember == '1') { //如果用户选择了，记录登录状态就把用户名和加了密的密码放到cookie里面
            setcookie("cooper_username", $username, time() + 3600);
            setcookie("cooper_password", $password, time() + 3600);
        }

        $msg = $_SESSION['cooper_user_info'];

        // $session_id=session_id();
        // $update_sql= "update user_list set `session_id`='$session_id' where id='$user_id' "  ;


        // var_dump($session_id);

        // die;


    }
}
echo json_encode($msg);

function checkUser($username, $password, $db)
{
    $sql = "SELECT `id`,`username`,`email`,`phone`,`product_id`,`group_id`,`group_category`,`level`,`project_id` from user_list where username='$username' and password='$password' and enable = 1";
    $userinfo = $db->query($sql);
    return $userinfo;
}

//设置session 时间
function loginSession(){
    $Lifetime = 3600;
 
    // $DirectoryPath = "./tmp";
    
    // is_dir($DirectoryPath) or mkdir($DirectoryPath, 0777);
    
    //是否开启基于url传递sessionid,这里是不开启，发现开启也要关闭掉
    
    // if (ini_get("session.use_trans_sid") == true) {
    //     ini_set("url_rewriter.tags", "");
    //     ini_set("session.use_trans_sid", false);
    // }
    ini_set("session.gc_maxlifetime", $Lifetime);//设置session生存时间
    ini_set("session.gc_divisor", "1");
    ini_set("session.gc_probability", "1");
    ini_set("session.cookie_lifetime", "0");//sessionID在cookie中的生存时间
//    ini_set("session.save_path", $DirectoryPath);//session文件存储的路径
    

}