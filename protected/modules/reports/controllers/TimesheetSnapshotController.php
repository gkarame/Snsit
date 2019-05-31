<?php

class TimesheetSnapshotController extends Controller
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
		$searchArray = isset($_POST['TimesheetSnapshot']) ? $_POST['TimesheetSnapshot'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/timesheetSnapshot/index' => array(
						'label'=>Yii::t('translations', 'Timesheet Snapshot'),
						'url' => array('/reports/timesheetSnapshot/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new TimesheetSnapshot('search');
		$model->attributes = $searchArray;
		
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		//print_r($searchArray);
		$i = 0;
		$timesheetSnapshot = array();
		$workingavg = array();
		$billabilty = array();

		$snapshots = array();
		$message = "";

		if(count(array_filter($searchArray)) > 0) {
			

				$select="SELECT uts.id_user, SUM(uts.amount)/8 as leaves_tasks ,
				 case uts.id_task when '11' then 'Day Off' 
				 			  when '12' then 'Sick Leave'
				 			  when '13' then 'Vacation'
				 			  when '14' then 'Public Holiday'
				 			  when '15' then 'Emergency Leave' 
				 			  when '319' then 'Administration Leave' end as leave_type  FROM `user_time` uts ,users u ,user_personal_details upd ,codelkups c";
    			$where="where uts.id_user=u.id and upd.id_user=u.id and c.id_codelist='12' and upd.unit=c.id and uts.`default`='1' and  uts.id_task in (select id from `default_tasks` where id_parent='2') and uts.amount>0  and u.active='1'";
    			$groupBy ="group by uts.id_task , uts.id_user";
    			$order = "ORDER BY uts.id_user , leave_type ASC";


				

    		if (!empty($model->unit))
			{	
				$unit = $model->unit;
				$where .= " AND c.codelkup like '%$unit%'";
			}	
				
    		if (!empty($model->id_user))
			{	
				$user_id = (int)$model->id_user;
				$where .= " AND uts.id_user ='{$user_id}'";
			}				
			
			if (!empty($model->user))
			{	
				$users = $model->user;
				$first = strstr($users, ' ', true);
				$last = substr(strstr($users, ' '),1);
				$where .= " AND u.firstname like '%$first%' AND u.lastname like '%$last%'";;
				
			}
			
			if (!empty($model->from))
			{
				$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
				$where .= " AND date >= '{$from}'";    
			}
			if (!empty($model->to))
			{
				$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
				$where .= " AND date <= '{$to}'";
			}


			
		//	echo $select." ".$where." ".$groupBy." ".$order; exit;
			
			 $snapshots = Yii::app()->db->createCommand($select." ".$where." ".$groupBy." ".$order )->queryAll(); 
			foreach ($snapshots as $snap) {

				$timesheetSnapshot[$snap['id_user']][$snap['leave_type']]=Utils::formatNumber($snap['leaves_tasks']);
				$workingavg[$snap['id_user']]['avg']=Utils::formatNumber(Timesheets::getWorkPerResc($snap['id_user'],$from,$to));
				$workingavg[$snap['id_user']]['avgoff']=Utils::formatNumber(Timesheets::getWorkPerRescWeekend($snap['id_user'],$from,$to));
				$workingavg[$snap['id_user']]['actualhours']=Utils::formatNumber(Timesheets::getTotalHoursWorkPerRes($snap['id_user'],$from,$to));
				$billabilty[$snap['id_user']]['bill']=Utils::formatNumber(Timesheets::getBillPerResc($snap['id_user'],$from,$to));
			}

		

		}

		if (!empty($timesheetSnapshot)) {
			if (!empty($model->file))
			{
				if ($model->file == "Pdf")
					self::createPdf($timesheetSnapshot);
				elseif ($model->file == "Excel")
					self::createExcel($timesheetSnapshot, $workingavg, $billabilty);
					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'timesheetSnapshot' => $timesheetSnapshot,
				'workingavg'=>$workingavg,
				'billabilty'=>$billabilty,
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.timesheetSnapshot.','L',$profit); 
		$file = Utils::getFileReport();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=REPORTS.pdf');
			header('Content-type: application/pdf');
			readfile($file);
		} 
	}
	
	public function createExcel($resp, $workingavg, $billabilty){
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
		
			//My styles
				$styleArray = array('font' => array('italic' => false, 'bold'=> true,    ),
				'borders' => array(
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),);	
				
				$styleArray1 = array('font' => array('italic' => false, 'bold'=> false,    ),
				'borders' => array(
				'bottom' => array('color' => array('argb' => '11666739')),
				'top' => array('color' => array('argb' => '11666739')),
				'right' => array('color' => array('argb' => '11666739')),
				),);	
				
				$styleLeft = array('font' => array('italic' => false, 'bold'=> true,    ),
				'borders' => array(
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				),);
		
		$sheetId = 0;	
		$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray);
		$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.'4', 'Resource')
					->setCellValue('B'.'4', 'Annual Leaves')
					->setCellValue('C'.'4', 'Day Off')
					->setCellValue('D'.'4', 'Sick Leaves')
					->setCellValue('E'.'4', 'Emergency/Administrative Leaves')
					->setCellValue('F'.'4', 'Average Working Hours per Day')
					->setCellValue('G'.'4', 'Average Working Hours per Weekend')
					->setCellValue('H'.'4', 'Total Working Hours')
					->setCellValue('i'.'4', 'Billabilty');
					
		$ct = 6;
		$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF'
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray2); 

		foreach($resp as $key => $timesheet){
				//My Variables
				$other=0;
					$si=0;
					$vac=0;
					$do=0;
					$ph=0;


					foreach ($timesheet as $k=>$value) {

					
							switch ($k) {
								case 'Sick Leave':
										$si=$value;
									break;
								case 'Vacation':
										$vac=$value;
									break;
								case 'Day Off':
										$do=$value;
									break;	
								case 'Public Holiday':	
										$ph=0;
										break;								
								default:
									$other=$other+$value;
									break;
							}
						
						}
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':I'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, Users::getNameByid($key))
						->setCellValue('B'.$ct, $vac)
						->setCellValue('C'.$ct, $do)
						->setCellValue('D'.$ct, $si)
						->setCellValue('E'.$ct, $other)
						->setCellValue('F'.$ct, $workingavg[$key]['avg'])
						->setCellValue('G'.$ct, $workingavg[$key]['avgoff'])
						->setCellValue('H'.$ct, $workingavg[$key]['actualhours'])
						->setCellValue('I'.$ct, $billabilty[$key]['bill']."%" );
				
						
						$ct=$ct+1;
						
				 
			
				/*
				
				$ct_first = $ct+2;
				$ct_second = $ct+3;
				
				$ct_prim = $ct+4;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct_prim.':'.'F'.$ct_prim)->applyFromArray($styleArray);
				
				$sheetId = 0;						
				
				
							*/
					
					
		}
				
	
		
	
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Projects Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="TimeSheet_Summary_Report.xls"');
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
	
	
	public function build_sorter($key, $key2 = null) 
	{
	    return function ($a, $b) use ($key, $key2) 
	    {
	    	$result = strnatcmp($b[$key],$a[$key]);
	    	if ($key2 != null && $result == 0)
	    	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	
}