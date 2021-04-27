<?
	require_once "../../Include/db_connect.php";

	// error_reporting(0);
	session_start();
	if(empty($_SESSION['cooper_user_info'])){
	    header("location:../index.html");
	}

	$search_project = $_GET['search_project'];
	$search_stage = $_GET['search_stage'];
	$sel_meth = $_GET['sel_meth'];
	$issue_sql = "select * from team_keyboard.issue_list where 1 and project_id='".$search_project."'";
	if($search_stage != 0){
		$issue_sql .= " and build_id='".$search_stage."'";
	}

/////////////////////////// Test Station
	$qs1="select a.id,a.station,b.number from station as a right join (select station_id,count(station_id) as number from (".$issue_sql.") as c group by station_id) as b on a.id = b.station_id order by a.id asc";
	$res1=$db->query($qs1);
	$total_station = 0;
	$total_num = 0;
	for ($i = 0; $i < count($res1); $i++) {
		$station_name[$i] =$res1[$i]['station'];
		$station_num[$i] = $res1[$i]['number'];
		$total_num += $res1[$i]['number'];
		$total_station++;
	}
	if($total_station == 1){
		$stations = 'station';
	}else{
		$stations = 'stations';
	}
	if($total_num == 1){
		$tasks = 'task';
	}else{
		$tasks = 'tasks';
	}
	$station_total = 'Total: '.$total_station.' test '.$stations.', '.$total_num.' '.$tasks.'.';
/////////////////////////// Test Station

/////////////////////////// Priority
	$qs2 = "select priority,count(priority) as number from (".$issue_sql.") as c group by priority";
	$res2=$db->query($qs2);
	$total_num = 0;
	for ($i = 0; $i < count($res2); $i++) {
		$priority_name[$i] =$res2[$i]['priority'];
		$priority_num[$i] = $res2[$i]['number'];
		$total_num += $res2[$i]['number'];
	}
	if($total_num == 1){
		$tasks = 'task';
	}else{
		$tasks = 'tasks';
	}
	$priority_total = 'Total: '.$total_num.' '.$tasks.'.';
/////////////////////////// Priority

/////////////////////////// Requestor
	$qs3="select a.id,a.username,b.number from user_list as a right join (select requestor_id,count(requestor_id) as number from (".$issue_sql.") as c group by requestor_id) as b on a.id = b.requestor_id order by a.id asc";
	$res3=$db->query($qs3);
	$total_requestor = 0;
	$total_num = 0;
	for ($i = 0; $i < count($res3); $i++) {
		$requestor_name[$i] =$res3[$i]['username'];
		$requestor_num[$i] = $res3[$i]['number'];
		$total_num += $res3[$i]['number'];
		$total_requestor++;
	}
	if($total_requestor == 1){
		$requestors = 'requestor';
	}else{
		$requestors = 'requestors';
	}
	if($total_num == 1){
		$tasks = 'task';
	}else{
		$tasks = 'tasks';
	}
	$requestor_total = 'Total: '.$total_requestor.' '.$requestors.', '.$total_num.' '.$tasks.'.';
/////////////////////////// Requestor
	$msg['station_name'] = $station_name;
	$msg['station_num'] = $station_num;
	$msg['station_total'] = $station_total;
	$msg['priority_name'] = $priority_name;
	$msg['priority_num'] = $priority_num;
	$msg['priority_total'] = $priority_total;
	$msg['requestor_name'] = $requestor_name;
	$msg['requestor_num'] = $requestor_num;
	$msg['requestor_total'] = $requestor_total;
	echo  json_encode($msg);

?>
