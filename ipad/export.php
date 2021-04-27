<?php
require_once "../Include/db_connect.php";

// error_reporting(0);
$action = $_GET['action'];
session_start();
if(empty($_SESSION['cooper_user_info'])){
    header("location:../index.html");
}





switch ($action) {
    case 'export_excel':
        $userId = $_SESSION['cooper_user_info'][0]['id'];
        $post = joinField($_POST);
        // var_dump($post);die;

        $msg = export_excel($db,$post,$userId);
        break;
   
    default:
        $msg = "参数错误";
        break;
}
echo json_encode($msg);





function joinField($post)
{
    foreach ($post as $key => $value) {
        if (!is_array($value)) {
            $b[$key] = trim($value);
            continue;
        } elseif (is_numeric($value[0])) {
            $b[$key] = implode('|', $value);
            continue;
        }
        $temp = array_keys($value);
        $a = implode('|', $temp);
        $b[$key] = $a;
    }
    return $b;
}


// export_excel($db);


function export_excel($db,$post,$userId)
{

    include_once 'PHPExcel.php';

    //获取数据

    $arr=get_data($db, $post,$userId);
    $countarr=ceil(count($arr)/2);
    //  var_dump($countarr);
    //  die;

    
    // require_once 'PHPExcel.php';
    //实例化
     $objPHPExcel = new PHPExcel();
    /*右键属性所显示的信息*/ 
    $objPHPExcel->getProperties()->setCreator("Luke") //作者
     ->setLastModifiedBy("Luke") //最后一次保存者 
     ->setTitle('数据EXCEL导出') //标题 
     ->setSubject('数据EXCEL导出') //主题 
     ->setDescription('导出数据') //描述 
     ->setKeywords("excel") //标记 
     ->setCategory("result file"); //类别


    //设置当前的表格 
    $objPHPExcel->setActiveSheetIndex(0);
    // 设置表格第一行显示内容 
    $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', 'No')
        ->setCellValue('B1', 'Product')
        ->setCellValue('C1', 'Project')
        ->setCellValue('D1', 'Stage')
        ->setCellValue('E1', 'Station')
        ->setCellValue('F1', 'Task')
        ->setCellValue('G1', 'Task Description')

        ->setCellValue('H1', 'Status Description')
        ->setCellValue('I1', 'Status')

        ->setCellValue('J1', 'Priority')
        ->setCellValue('K1', 'DRI')
        ->setCellValue('L1', 'Requestor')
        ->setCellValue('M1', 'ETA')
        ->setCellValue('N1', 'Ver')
        ->getStyle('A1:N1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED) ; //颜色
    $objPHPExcel->getActiveSheet()-> getStyle('A1:N1')->getFont() ->setSize(15); //字体
    $objPHPExcel->getActiveSheet()-> getStyle('A1:N1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平居中 
    $objPHPExcel->getActiveSheet()-> getStyle('A1:N1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中


    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);
  
      
    // $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
    // $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

    //    var_dump(1111); die;

        //设置第一行为红色字体 
        // ->getStyle('A1:D1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

    $key = 1;
    /*以下就是对处理Excel里的数据，横着取数据*/ 

    //  $arr= array(array('name'=>'63','pwd'=>'56','phone'=>'47','address'=>'47'),array('name'=>'63','pwd'=>'56','phone'=>'47','address'=>'47'));
   
    
  
    foreach($arr as $v){
        

    //设置循环从第二行开始
     $key++;
     $objPHPExcel->getActiveSheet()
                 //Excel的第A列，name是你查出数组的键值字段，下面以此类推 
                  ->setCellValue('A'.$key, $key-1) 
                  ->setCellValue('B'.$key, $v['product'])    
                  ->setCellValue('C'.$key, $v['project'])
                  ->setCellValue('D'.$key, $v['build'])
                  ->setCellValue('E'.$key, $v['station'])
                  ->setCellValue('F'.$key, $v['task_title'])
                  ->setCellValue('G'.$key, $v['task_description'])
                  ->setCellValue('H'.$key, $v['new_status'])

                  ->setCellValue('I'.$key, $v['status'])
                  ->setCellValue('J'.$key, $v['priority'])
                  ->setCellValue('K'.$key, $v['toUser'])
                  ->setCellValue('L'.$key, $v['requestor'])
                  ->setCellValue('M'.$key, $v['eta'])
                  ->setCellValue('N'.$key, $v['version']);
    $objPHPExcel->getActiveSheet()-> getStyle('A'.$key.':E'.$key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平居中 
    $objPHPExcel->getActiveSheet()-> getStyle('A'.$key.':E'.$key)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
    $objPHPExcel->getActiveSheet()-> getStyle('I'.$key.':N'.$key)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平居中 
    $objPHPExcel->getActiveSheet()-> getStyle('I'.$key.':N'.$key)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
      
              
                  
    if($v['status']=='Ongoing'){
        $objPHPExcel->getActiveSheet()   ->getStyle('I'.$key)->getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);
        }
    if($v['status']=='Cancel'){
        $objPHPExcel->getActiveSheet()   ->getStyle('I'.$key)->getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB('a3a2a2');
        }     
    if($v['status']=='Done'){
        $objPHPExcel->getActiveSheet()   ->getStyle('I'.$key)->getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
         }     
    if($v['status']=='Block'){
        $objPHPExcel->getActiveSheet()   ->getStyle('I'.$key)->getFill() ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
         ->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
         }                


    }
    $styleThinBlackBorderOutline = array(
        'borders' => array(
            'allborders' => array(
                //设置全部边框
                'style' => PHPExcel_Style_Border::BORDER_THIN, //粗的是thick
            ),

        ),
    );
    $objPHPExcel->getActiveSheet()->getStyle('A1:N1'.$countarr)->applyFromArray($styleThinBlackBorderOutline);



    $name='Teamease_'.date("Y-m-d_H.i.s");
    //设置当前的表格 
    $objPHPExcel->setActiveSheetIndex(0);
// 　　  ob_end_clean();     //清除缓冲区,避免乱码
     header('Content-Type: application/vnd.ms-excel'); //文件类型 
     header('Content-Disposition: attachment;filename="'.$name.'.xls"'); //文件名 
     header('Cache-Control: max-age=0');
     header('Content-Type: text/html; charset=utf-8'); //编码 
     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //excel 2003 

    //  header("Content-type:text/html;charset=utf-8");
  
    // var_dump($name);
     $objWriter->save('./exportfile/'.$name.'.xls');  
    //  $objWriter->save('php://output');  
     return $name;
   

}

function get_data($db, $post,$userId)
{

    $product = $post['product'];
    $project = $post['project'];
    $build = $post['build'];
    $station = $post['station'];
    $status = $post['status'];
    $start_time = $post['start_time'];
    $end_time = $post['end_time'];


    // 详细信息查询
    $task_select_sql = "SELECT a.`id`,a.`task_title`,a.`task_description`, a.`eta`,a.`cancel_user`,a.`done_user`,a.`version`,b.`product`,c.`project`,d.`build`,e.`station`,f.`status`,a.`cancel_content`,a.`cancel_done_time`, 
    u.`username` ,us.`username` as `toUser`, a.`priority`, ul.`username` as requestor  from team_ipad.issue_list as a
    left join `product` as b on a.`product_id` = b.`id`
    left join `project` as c on a.`project_id` = c.`id`
    left join `build` as d on a.`build_id` = d.`id`
    left join `station` as e on a.`station_id` = e.`id`
    left join `status` as f on a.`status_id` = f.`id`
    left join `user_list` as u on  a.`create_user_id` = u.`id`
    left join `user_list` as us on  a.`check_user` =us.`id`
    left join `user_list` as ul on  a.`requestor_id`= ul.`id`
    ";

    $temp_sql = ' where 1 ';

    // 对于详细信息进行分页处理  对ID进行倒序
    $limit_sql = " ORDER BY a.`id` ASC ";

        //TODO 产品和专案 未做对应表连接
        if ('' != $product) {
            $product = explode("|", $product);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        } else {
            $product_result = searchProduct($db, $userId);
            $product = explode("|", $product_result[0]['product_id']);
            $temp_sql .= taskAllListInitSqlLine($product, 'a.`product_id`');
        }

        if ('' != $project) {
            $project = explode("|", $project);
            $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
        }else{
            $project_select_sql = "SELECT `project_id` FROM `user_list` WHERE `id` = '$userId'";
            $project_result = $db->query($project_select_sql);
    
            if($project_result[0]['project_id']!=NUll&& $project_result[0]['project_id']!=''){
             $project = explode("|", $project_result[0]['project_id']);
             // var_dump($project);
             $temp_sql .= taskAllListInitSqlLine($project, 'a.`project_id`');
    
            }
            
        }

        if ('' != $build) {
            $build = explode('|', $build);
            $temp_sql .= taskAllListInitSqlLine($build, 'a.`build_id`');
        }

        if ('' != $station) {
            $station = explode("|", $station);
            $temp_sql .= taskAllListInitSqlLine($station, 'a.`station_id`');
        }

        if ('' != $status) {
            $status = explode('|', $status);
            $temp_sql .= taskAllListInitSqlLine($status, 'a.`status_id`');
        }

        if ($start_time!='') {
            $start_time2 = strtotime($start_time);
            $end_time2 = strtotime($end_time);

            if($start_time==$end_time){
                $end_time=date("Y-m-d",strtotime("+1 day",$end_time2));
            }

            if ($start_time || $end_time) {
                $temp_sql .= " and a.`create_time` between '" . $start_time  . "' and '" .  $end_time  . "' ";
            }

        }
   

    $task_select_sql .= $temp_sql;
    // $task_count_select_sql .= $temp_sql;

    // sql 语句拼接
    $task_select_sql .= $limit_sql;
    // $task_count_select_sql .= $limit_sql;

    //   var_dump( $task_select_sql);

    $task_select_result = $db->query($task_select_sql);
  

    for ($i = 0; $i < count($task_select_result); $i++) {

        $versionLast=$task_select_result[$i]['version'];
        
        $task_select_result[$i]['version'].='
';

        $task_select_result[$i]['new_status'] = '1: New Task
';

        $history_status_sql = "SELECT `status`,`version` FROM team_ipad.history WHERE `issue_list_id` = '" . $task_select_result[$i]['id'] . "' ORDER BY `id` ASC";

        $history_status_result = $db->query($history_status_sql);
        for ($j = 0; $j < count($history_status_result); $j++) {
         
            $h=strval($j+2).': ';
           
            $task_select_result[$i]['new_status'].= $h;
            $task_select_result[$i]['new_status'] = $task_select_result[$i]['new_status'] .$history_status_result[$j]['status'].'
';
            // if ($j == count($history_status_result) - 1) {
            //     $task_select_result[$i]['new_status'] =  $history_status_result[$j]['status'];
            // } else {
            //     if (!empty($history_status_result[$j]['status'])) {
            //         $task_select_result[$i]['new_status'] = $history_status_result[$j]['status'];
            //         break;
            //     }
            // }
            if($history_status_result[$j]['version']!=null&&$history_status_result[$j]['version']!=''&&$history_status_result[$j]['version']!=$versionLast){
                $task_select_result[$i]['version'].=$history_status_result[$j]['version'].='
';
            }

        }
        $length=strval(count($history_status_result)+2);
        if (!empty($task_select_result[$i]['cancel_content'])) {
            $task_select_result[$i]['new_status'] .=  $length.': ';
            $task_select_result[$i]['new_status'] .=$task_select_result[$i]['cancel_content'] ;
        }

        if(!empty($task_select_result[$i]['cancel_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['cancel_user'];
        }
        if(!empty($task_select_result[$i]['done_user'])){
             $task_select_result[$i]['toUser'] = $task_select_result[$i]['done_user'];
        }
    }
    // $task_count_select_result = $db->query($task_count_select_sql);

    // 数据返回接口格式
    // $task_result = array(
    //     "code" => 0,
    //     "msg" => $task_select_sql,
    //     "count" => $task_count_select_result[0]['num'],
    //     "data" => $task_select_result,
    // );

    return $task_select_result;

}





/**
 * taskAllListInit where条件拼接
 *
 * @param Array $array   需要循环的字段的值
 * @param String $field  需要遍历的字段
 * @return void
 */
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


/**
 * 查询当前所拥有的产品权限
 *
 * @param Object $db
 * @param Int $userId
 * @return Array
 */
function searchProduct($db, $userId)
{
    $product_select_sql = "SELECT `product_id` FROM `user_list` WHERE `id` = '$userId'";
    $product_select_result = $db->query($product_select_sql);
    return $product_select_result;
}






?>