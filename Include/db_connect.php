<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-type:text/html;charset:utf-8");
require "pdo.php";




$db_host = 'localhost';
$db_user = 'root';
// $db_pwd = 'root';
$db_pwd = 'root';
$db_name = 'teamease';
// $db_name_ipad = 'team_ipad';
// $db_name_kb = 'team_keyboard';


$db = mypdo::getInstance($db_host, $db_user, $db_pwd, $db_name, 'utf8');
// $db_ipad = mypdo::getInstance($db_host, $db_user, $db_pwd, $db_name_ipad, 'utf8');
// $db_kb = mypdo::getInstance($db_host, $db_user, $db_pwd, $db_name_kb, 'utf8');



function sqlSafe($a){
    return addslashes(htmlspecialchars(trim($a)));
}
// session_start();
// if(empty($_SESSION['cooper_user_info'])){
//     header('location: '.$_SERVER['HTTP_REFERER']);
// }