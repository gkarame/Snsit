<?php

class LeaveSummaryController extends Controller
{
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(
						'index','pieChart', 
				),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex()
	{
		$searchArray = isset($_POST['Requests']) ? $_POST['Requests'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/reports/leavesummary/index' => array(
								'label'=>Yii::t('translations', 'Detailed Leaves Report'),
								'url' => array('/reports/leavesummary/index'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1,
								'search' => $searchArray,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model = new Requests('search');
		$model->attributes = $searchArray;
		$_POST['Requests'] = $searchArray;
		$respp = array();
		$message = "";
		if(isset($_POST['Requests'])){
			//print_r($_POST['Requests']);exit();
			//$where = " WHERE  r.status = 1 ";
			$where = " where uts.id_user=u.id and upd.id_user=u.id and c.id_codelist='12' and upd.unit=c.id and uts.`default`='1' and  uts.id_task in (select id from `default_tasks` where id_parent='2') and uts.amount>0  and u.active='1'";
			$val_type_time = '('.Timesheets::ITEM_VACATION .','. Timesheets::ITEM_DAYOFF .','. Timesheets::ITEM_SICK_LEAVE .')'; 
			$i = 0;
			/*$select = "Select r.user_id,r.startDate as date,r.type,r.endDate
						FROM requests r
						LEFT JOIN  users u ON u.id = r.user_id
					";*/

			$select = "SELECT uts.id_user, id_task, date ,
				 case uts.id_task when '11' then 'Day Off' 
				 			  when '12' then 'Sick Leave'
				 			  when '13' then 'Vacation'
				 			  when '14' then 'Public Holiday'
				 			  when '15' then 'Emergency Leave' 
				 			  when '319' then 'Administration Leave' end as leave_type  FROM `user_time` uts ,users u ,user_personal_details upd ,codelkups c";
    	
			if(isset($_POST['Requests']['user_id'])&& $_POST['Requests']['user_id']!=null){
				$user = $_POST['Requests']['user_id'];
				$first = strstr($user, ' ', true);
				$last = substr(strstr($user, ' '),1);
				$where .= " AND u.firstname = '$first' AND u.lastname = '$last'";
				$i=1;
				$name =$user;
			}else{
				$name = "All";
			}
			if(isset($_POST['Requests']['type'])&& $_POST['Requests']['type']!=null){
				
				//$Req_type = $_POST['Requests']['type']; 
				$val_type =$_POST['Requests']['type'];
					
					//r.type
					$where .= " AND id_task ='$val_type'";
				$i=1;
				//Georgi Wrong Fix
				$type_name = Codelkups::getTaskName($val_type);	
			}else if(isset($_POST['Requests']['type'])&& $_POST['Requests']['type']==null){
				//$val_type = '('.Requests::ITEM_VACATION .','. Requests::ITEM_DAYOFF .','. Requests::ITEM_SICK_LEAVE .')'; 
				$val_type = '('.Timesheets::ITEM_VACATION .','. Timesheets::ITEM_DAYOFF .','. Timesheets::ITEM_SICK_LEAVE .')'; 
				//r.type
				$where .= " AND id_task IN $val_type";
				$i=1;	
				$type_name = "All";
			}
			if((isset($_POST['Requests']['startDate'])&& $_POST['Requests']['startDate']!=null) && (isset($_POST['Requests']['endDate']) && $_POST['Requests']['endDate']!=null) ){
				$startDate = DateTime::createFromFormat('d/m/Y',$_POST['Requests']['startDate'])->format('Y-m-d');
				$endDate = DateTime::createFromFormat('d/m/Y',$_POST['Requests']['endDate'])->format('Y-m-d');
				//$where .= " AND r.startDate >='$startDate  r.endDate'";
				$where .= " AND date >= '$startDate' AND date <= '$endDate'";
				$i=1;
					
			}else if(isset($_POST['Requests']['startDate'])&& $_POST['Requests']['startDate']!=null){
				$startDate = DateTime::createFromFormat('d/m/Y',$_POST['Requests']['startDate'])->format('Y-m-d');
				//$where .= " AND r.startDate >'$startDate'  ";
				$where .= " AND date >'$startDate'  ";
			}else if(isset($_POST['Requests']['endDate']) && $_POST['Requests']['endDate']!=null)
			{
				$endDate = DateTime::createFromFormat('d/m/Y',$_POST['Requests']['endDate'])->format('Y-m-d');
				//$where .= " AND r.endDate <='$endDate'  ";
				$where .= " AND date <='$endDate'  ";
				$i=1;
			}
			//echo $select.$where.$i;
			//$order = " ORDER BY r.id ASC ";
			$order = " ORDER BY uts.id_user , leave_type ASC";
			//$group = " group by uts.id_task , uts.id_user"; 
			//echo $select.$where.$order;

			//print_r($select.$where.$order);exit();
			if($i != 0){
				$expenses = Yii::app()->db->createCommand($select.$where.$order)->queryAll();
				foreach ($expenses as $row)
				{
					//$id_user = $row['user_id'];
					$id_user = $row['id_user'];
					/*$start_date = $startDate;//$row['date'];
					$end_date = $endDate;//$row['endDate'];*/
					//$val_type = $row['type'];
					$val_type = $row['id_task'];
					$date = $row['date'];
					/*$dates = array();
					$dates = self::createRange($start_date,$end_date);*/
					//print_r($dates);exit();
					//foreach ($dates as $date){
						//echo $date."<br/>";
						/*if isset post->type bring only amount from user_time where id_task = type*/
					/*	if($val_type == Timesheets::ITEM_DAYOFF){
							$val_type_time = '('.Timesheets::ITEM_DAYOFF .')';
						}else if($val_type == Timesheets::ITEM_VACATION){
								$val_type_time = '('.Timesheets::ITEM_VACATION .')';
							}
							else if($val_type == Timesheets::ITEM_SICK_LEAVE){
									$val_type_time = '('.Timesheets::ITEM_SICK_LEAVE .')';
							}*/
						//print_r($val_type_time);exit();

						$results_time = Yii::app()->db->createCommand("
							SELECT ut.amount,ut.comment,ut.date,ut.id_user,ut.id_task,ut.id
							FROM user_time ut
							WHERE ut.date = '$date' AND id_task IN $val_type_time AND id_user = '$id_user' AND ut.amount>0
							ORDER BY ut.date ASC
						")->queryAll();

						//print_r($results_time);exit();
						foreach ($results_time as $result_time){
							//id_user == user_id
							$respp[$row['id_user']]['project'] = $row['leave_type'];
							$respp[$row['id_user']]['name'] = $name;
							$respp[$row['id_user']]['type_name'] = $type_name;
							if(isset($_POST['Requests']['startDate'])&& $_POST['Requests']['startDate']!=null)
								$respp[$row['id_user']]['start_time'] = $startDate;
							else 
								$respp[$row['id_user']]['start_time'] = Requests::min();
							if(isset($_POST['Requests']['endDate'])&& $_POST['Requests']['endDate']!=null)
								$respp[$row['id_user']]['end_time'] = $endDate;
							else 
								$respp[$row['id_user']]['end_time'] = Requests::max();
							$respp[$row['id_user']]['type'][$result_time['id_task']]['date'][$result_time['date']] = $result_time;
						}
					//}
					//$result_time = Yii::app()->db->createCommand("")->queryAll();
				}
				//print_r($respp);
				//if not result return print a message
				if($respp != null){
					if(isset($_POST['Requests']['file'])&& $_POST['Requests']['file']!=null){
						if($_POST['Requests']['file'] == "Pdf")
							self::createPdf($respp);
						elseif ($_POST['Requests']['file'] == "Excel")
							self::createExcel($respp);
					}
				}else{
					$message = "No search results found";
				}
				unset($_POST['Expenses']);
				$this->render('index',array(
					'model'=>$model,
					'leaves' => $respp,
					'message' =>$message
				));
				exit;
				
			}
		}
		unset($_POST['Expenses']);
		$this->render('index',array(
			'model'=>$model,
			'leaves' => $respp,
			'message' =>$message
		));
		
	}
	
	public function createPdf($resp){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.leaveSummary.'); 
		$file = Utils::getFileReport();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=REPORTS.pdf');
			header('Content-type: application/pdf');
			readfile($file);
		} 
		//return true;
	}
	
	public function createExcel($resp){
		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		
		
		// Create new PHPExcel object
		$objPHPExcel = XPHPExcel::createPHPExcel();
		
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Seve Alex")
		->setLastModifiedBy("Seve Alex")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");
	
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('PHPExcel logo');
		$objDrawing->setDescription('PHPExcel logo');
		$objDrawing->setPath(dirname(Yii::app()->request->scriptFile).'/images/logo_pdf.png');       // filesystem reference for the image file
		$objDrawing->setHeight(36);                 // sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates('A1');    // pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		
		$styleArray = array('font' => array('italic' => true, 'bold'=> true,    ),
		'borders' => array(
		'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),
		
		),);
		$objPHPExcel->getActiveSheet()->getStyle('F2:J2')->applyFromArray($styleArray);
		for($i = 4;$i<8 ; $i++){
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$i.':G'.$i);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$i.':J'.$i);
		}
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('F4', 'Resource')
		->setCellValue('F5', 'Type')
		->setCellValue('F6', 'Start Date')
		->setCellValue('F7', 'End Date')
		->setCellValue('H4', $_POST['Requests']['user_id'])
		->setCellValue('H5', $_POST['Requests']['type'])
		->setCellValue('H6', $_POST['Requests']['startDate'])
		->setCellValue('H7', $_POST['Requests']['endDate']);
		
		$ct = 9;
		//print_r($resp);exit;
		foreach($resp as $key=>$user){
			 $types = $user['type'];
		
				
				$ct_first = $ct+2;
				$ct_second = $ct+3;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':'.'F'.$ct)->applyFromArray($styleArray);
				$ct_prim = $ct+4;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct_prim.':'.'F'.$ct_prim)->applyFromArray($styleArray);
				
				$sheetId = 0;
				
				
				$styleArray1 = array('font' => array('italic' => true, 'bold'=> true,    ),
				'borders' => array(
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				),);
				
				$styleLeft = array('font' => array('italic' => true, 'bold'=> true,    ),
				'borders' => array(
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				),);
				
				$ct +=6;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':F'.$ct)->applyFromArray($styleArray1);
				
				for($k="A", $i=$ct;$k<"G";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$i.':'.++$k.$i);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
				}
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_first, 'Resource: '.$user['name'])
				->setCellValue('A'.$ct_second, 'Type: '.$user['type_name'])
				->setCellValue('D'.$ct_first, 'Start Date: '.date('d/m/Y',strtotime($user['start_time'])))
				->setCellValue('D'.$ct_second, 'End Date: '.date('d/m/Y',strtotime($user['end_time'])))
				->setCellValue('A'.$ct, 'Date')
				->setCellValue('C'.$ct, 'Leave Type')
				->setCellValue('E'.$ct, 'Comment');

				 $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':F'.$ct)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
	        $styleArray2 = array(
	            'font' => array(
	                'color' => array('rgb' => 'FFFFFF'
	                )
	            ),
	            'borders' => array(
	                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
	                )
	            ));

	        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':F'.$ct)->applyFromArray($styleArray2); 


				$index = 0;
				$ct_0 =++$ct;
				$ct_pr = 1; 
				foreach ($types as $key_type =>$type) {
					uasort($type, $this->build_sorter('date'));
					$dates = $type['date'];
					$total[Timesheets::ITEM_DAYOFF] = 0;
					$total[Timesheets::ITEM_VACATION] = 0;
					$total[Timesheets::ITEM_SICK_LEAVE] = 0;
					$sum[Timesheets::ITEM_DAYOFF] = 0;
					$sum[Timesheets::ITEM_VACATION] = 0;
					$sum[Timesheets::ITEM_SICK_LEAVE] = 0;
					foreach ($dates as $key_date => $date ){
					$sum[$key_type] += $date['amount'];
					$key1= DateTime::createFromFormat('Y-m-d',$key_date)->format('d/m/Y');
					
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct_0, $key1)
						->setCellValue('C'.$ct_0, DefaultTasks::getDescription($key_type))
						->setCellValue('E'.$ct_0, $date['comment']);
						for($k="A",$i=$ct_0;$k<"G";$k++){
							$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
							$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
						}
						$ct_0++;
						$ct_pr++;
					}
				$total[$key_type]= count ($dates);
				}
				for($k="A",$i=$ct_0;$k<"G";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleArray1);
				}
				
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_0, 'Total '.DefaultTasks::getDescription(Timesheets::ITEM_DAYOFF).": ".$total[Timesheets::ITEM_DAYOFF])
				->setCellValue('C'.$ct_0, 'Total '.DefaultTasks::getDescription(Timesheets::ITEM_VACATION).": ".$total[Timesheets::ITEM_VACATION])
				->setCellValue('E'.$ct_0, 'Total '.DefaultTasks::getDescription(Timesheets::ITEM_SICK_LEAVE).": ".$total[Timesheets::ITEM_SICK_LEAVE]);
				
				$ct_0++;
				$ct_pr++;
				for($k="A",$i=$ct_0;$k<"G";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleArray1);
				}
				if($total[Timesheets::ITEM_DAYOFF] !=0)
					$av_day = $sum[Timesheets::ITEM_DAYOFF]/$total[Timesheets::ITEM_DAYOFF];
				else 
					$av_day = 0;
				if($total[Timesheets::ITEM_VACATION] !=0)
					$av_vac =  $sum[Timesheets::ITEM_VACATION]/$total[Timesheets::ITEM_VACATION];
				else 
					$av_vac = 0;
				if($total[Timesheets::ITEM_SICK_LEAVE])
					$av_sick = $sum[Timesheets::ITEM_SICK_LEAVE]/$total[Timesheets::ITEM_SICK_LEAVE];
				else 
					$av_sick = 0;
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_0, 'Average '.DefaultTasks::getDescription(Timesheets::ITEM_DAYOFF).": ".Utils::formatNumber($av_day))
				->setCellValue('C'.$ct_0, 'Average '.DefaultTasks::getDescription(Timesheets::ITEM_VACATION).": ".Utils::formatNumber($av_vac))
				->setCellValue('E'.$ct_0, 'Average '.DefaultTasks::getDescription(Timesheets::ITEM_SICK_LEAVE).": ".Utils::formatNumber($av_sick));
				
			$ct = $ct + $ct_pr + 10;
			
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Expenses Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Expenses_Reports.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			
			/*$path = dirname(Yii::app()->request->scriptFile)."/uploads/customers/INVOICE_".date("dmYHis").".xls";
			$objWriter->save($path);
			Yii::app()->request->redirect('http://snsit.com');
			*/
		exit;
	}
	
	function createRange($startDate, $endDate) {
	    $tmpDate = new DateTime($startDate);
	    $tmpEndDate = new DateTime($endDate);
	
	    $outArray = array();
	    do {
	        $outArray[] = $tmpDate->format('Y-m-d');
	    } while ($tmpDate->modify('+1 day') <= $tmpEndDate);
	
	    return $outArray;
	}
	public function build_sorter($key, $key2 = null) 
	{
	    return function ($a, $b) use ($key, $key2) 
	    {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0)
	    	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
}