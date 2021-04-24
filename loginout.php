<?php
require_once "./Include/db_connect.php";
session_start();
// unset($_SESSION);

$user_name = $_SESSION['cooper_user_info'][0]['username'];
$qs = "select * from `login_time` where `user_name`='".$user_name."' order by id desc";
$userinfo = $db->query($qs);
$login_user_id= $userinfo[0]['id'];


$logout_time=date('Y-m-d H:i:s',time());
$qs1="update `login_time` set `logout_time`='".$logout_time."' where id='$login_user_id'";
$userinfo = $db->execSql($qs1);


session_unset();
// unset($_COOKIE);
setcookie("cooper_username", '', time() - 3600);
setcookie("cooper_password", '', time() - 3600);
// echo 1;
header("Location:index.html");
?>