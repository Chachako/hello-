<?php // content="text/plain; charset=utf-8"
require_once('../../Include/db_connect.php');
$divid = trim($_GET['divid']);
$search_project = trim($_GET['search_project']);
$search_stage = trim($_GET['search_stage']);

$issue_sql = "select * from team_ipad.issue_list where 1 and project_id='".$search_project."'";
if($search_stage != '0'){
	$issue_sql .= " and build_id='".$search_stage."'";
}

switch($divid)
{
	case 1:
	/////////////////////////// Test Station
	$qs1="select a.id,a.station,b.number from station as a right join (select station_id,count(station_id) as number from (".$issue_sql.") as c group by station_id) as b on a.id = b.station_id order by a.id asc";
	$res1=$db->query($qs1);
	for ($i = 0; $i < count($res1); $i++) {
		$station_name[$i] =$res1[$i]['station'];
		$station_num[$i] = $res1[$i]['number'];
	}
	/////////////////////////// Test Station
	$all_num = $station_num;	
	$departs = $station_name;
	$gra_title1 = "By Test Station";
	$gra_title2 = "Test Station";
	$gra_title3 = "Qty";
	break;

	case 2:
	
/////////////////////////// Priority
	$qs2 = "select priority,count(priority) as number from (".$issue_sql.") as c group by priority";
	$res2=$db->query($qs2);
	for ($i = 0; $i < count($res2); $i++) {
		$priority_name[$i] =$res2[$i]['priority'];
		$priority_num[$i] = $res2[$i]['number'];
	}
/////////////////////////// Priority
	$all_num = $priority_num;	
	$departs = $priority_name;
	$gra_title1 = "By Priority";
	$gra_title2 = "Priority";
	$gra_title3 = "Qty";
	break;
	
	case 3:
	/////////////////////////// Requestor
	$qs3="select a.id,a.username,b.number from user_list as a right join (select requestor_id,count(requestor_id) as number from (".$issue_sql.") as c group by requestor_id) as b on a.id = b.requestor_id order by a.id asc";
	$res3=$db->query($qs3);
	for ($i = 0; $i < count($res3); $i++) {
		$requestor_name[$i] =$res3[$i]['username'];
		$requestor_num[$i] = $res3[$i]['number'];
	}
	/////////////////////////// Requestor
	$all_num = $requestor_num;	
	$departs = $requestor_name;
	$gra_title1 = "By Requestor";
	$gra_title2 = "Requestor";
	$gra_title3 = "Qty";
	break;
}


//switch($divid)
//{
//	case 1:
//	$all_num = $station_num;	
//	$departs = $station_name;
//	$gra_title1 = "By Test Station";
//	$gra_title2 = "Test Station";
//	$gra_title3 = "Qty";
//	break;
//
//	case 2:
//	$all_num = $cause_num;	
//	$departs = $cause_name;
//	$gra_title1 = "By Root Cause";
//	$gra_title2 = "Root Cause";
//	$gra_title3 = "Qty";
//	break;
//	
//	case 3:
//	$all_num = $Config_num;	
//	$departs = $Config_name;
//	$gra_title1 = "By Config";
//	$gra_title2 = "Config";
//	$gra_title3 = "Qty";
//	break;	
//	
//	case 4:
//	$all_num = $symp_num;	
//	$departs = $symp_name;
//	$gra_title1 = "By Failure Symptom";
//	$gra_title2 = "Failure Symptom";
//	$gra_title3 = "Qty";
//	break;		
//}

$x_num = count($departs);


require_once ('../../jpgraph/jpgraph.php');
require_once ('../../jpgraph/jpgraph_pie.php');

// A new pie graph
if($x_num>100)
{
	$graph = new PieGraph(4000,580,"auto"); 
}
elseif($x_num>20 && $x_num<=100)
{
	$graph = new PieGraph(2500,1500,"auto"); 
}
else
{
	$graph = new PieGraph(920,580,"auto");
}

$graph->SetShadow();

// Title setup
$graph->title->Set($gra_title1);
$graph->title->SetFont(FF_VERDANA,FS_BOLD,10);

// Setup the pie plot
$p1 = new PiePlot($all_num);

// Adjust size and position of plot
$p1->SetSize(0.3);
$p1->SetCenter(0.3,0.5);

// Setup slice labels and move them into the plot
$p1->value->SetFont(FF_FONT1,FS_BOLD);
$p1->value->SetColor("darkred");
$p1->SetLabelPos(0.65);

// Explode all slices
$p1->ExplodeAll(10);

$p1->SetLegends($departs);

// Finally add the plot
$graph->Add($p1);

// ... and stroke it
$graph->Stroke();

?>
