<?php 
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Content-type:text/html;charset:utf-8");
require "pdo.php";




$db_host = 'localhost';
$db_user = 'root';
$db_pwd = 'root';
//$db_pwd = '1234567Abc,.';
$db_name = 'teamease';
$db = mypdo::getInstance($db_host, $db_user, $db_pwd, $db_name, 'utf8');
$sql="select e.*,`status`.`status` ,u.`username`,us.`username` as toUser,ul.`username` as requestor  from 
    (select d.*,st.station from 
    (select c.*,bd.build from 
    (select b.*,pj.project from 
    (select a.*,pd.product from team_ipad.issue_list as a LEFT JOIN product as pd on a.product_id=pd.id order by id desc) as b LEFT JOIN project as pj on b.project_id=pj.id) as c
    LEFT JOIN build as bd on c.build_id=bd.id) as d LEFT JOIN station as st on d.station_id=st.id) 
    as e LEFT JOIN status as `status` on e.status_id=`status`.`id`  
    left join user_list as u on e.create_user_id =u.id 
    left join user_list as us on e.check_user=us.id
    left join user_list as ul on  e.requestor_id= ul.id";
$task=$db->query($sql);
var_dump($task);
?> 