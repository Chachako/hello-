<?php
require_once "./Include/db_connect.php";

$issue_sql="select id from team_ipad.issue_list";
$issue_l=$db->query($issue_sql);
for($i = 0; $i < count($issue_l); $i++){
	$issue_id = $issue_l[$i]['id'];
	$issue_id1 =$issue_id;
	$supply='';
    for($j=0;$j<6-strlen($issue_id1);$j++){
        $supply.='0';
    }
    $no='PG'.$supply.$issue_id;
	$issue_update="update team_ipad.issue_list set no = '$no' where id = '$issue_id' and project_id='1'";
	$db->execSql($issue_update);
	$no='PP'.$supply.$issue_id;
	$issue_update="update team_ipad.issue_list set no = '$no' where id = '$issue_id' and project_id='2'";
	$db->execSql($issue_update);
	$no='PV'.$supply.$issue_id;
	$issue_update="update team_ipad.issue_list set no = '$no' where id = '$issue_id' and project_id='8'";
	$db->execSql($issue_update);
}

$issue_sql="select id from team_keyboard.issue_list";
$issue_l=$db->query($issue_sql);
for($i = 0; $i < count($issue_l); $i++){
	$issue_id = $issue_l[$i]['id'];
	$issue_id1 =$issue_id;
	$supply='';
    for($j=0;$j<6-strlen($issue_id1);$j++){
        $supply.='0';
    }
    $no='ST'.$supply.$issue_id;
	$issue_update="update team_keyboard.issue_list set no = '$no' where id = '$issue_id' and project_id='4'";
	$db->execSql($issue_update);
	$no='SA'.$supply.$issue_id;
	$issue_update="update team_keyboard.issue_list set no = '$no' where id = '$issue_id' and project_id='5'";
	$db->execSql($issue_update);
}

$issue_sql="select id from team_watch.issue_list";
$issue_l=$db->query($issue_sql);
for($i = 0; $i < count($issue_l); $i++){
	$issue_id = $issue_l[$i]['id'];
	$issue_id1 =$issue_id;
	$supply='';
    for($j=0;$j<6-strlen($issue_id1);$j++){
        $supply.='0';
    }
    $no='WS'.$supply.$issue_id;
	$issue_update="update team_watch.issue_list set no = '$no' where id = '$issue_id' and project_id='6'";
	$db->execSql($issue_update);
	$no='WL'.$supply.$issue_id;
	$issue_update="update team_watch.issue_list set no = '$no' where id = '$issue_id' and project_id='7'";
	$db->execSql($issue_update);
	$no='FA1'.$supply.$issue_id;
	$issue_update="update team_watch.issue_list set no = '$no' where id = '$issue_id' and project_id='9'";
	$db->execSql($issue_update);
	$no='FA2'.$supply.$issue_id;
	$issue_update="update team_watch.issue_list set no = '$no' where id = '$issue_id' and project_id='10'";
	$db->execSql($issue_update);
}

?>
