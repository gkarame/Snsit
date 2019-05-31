<?php

class PendingTimesheetController extends Controller
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
		$searchArray = isset($_POST['TimesheetSummary']) ? $_POST['TimesheetSummary'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/timesheetSummary/index' => array(
						'label'=>Yii::t('translations', 'Pending Timesheets'),
						'url' => array('/reports/timesheetSummary/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new TimesheetSummary('search');
		$model->attributes = $searchArray;
		
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		
		$i = 0;
		$timesheetSummary = array();
		$message = "";

		if(count(array_filter($searchArray)) > 0) {
			$select = "select p.customer_id, c.name as customer_name, p.name as project_name, 
			 CONCAT_WS(' ', firstname, lastname) AS username, pp.id_project ,ut.id_user , pt.description ,ut.comment  , ut.date , SUM(ut.amount) AS amount
			 from user_time ut";
			$select .= " LEFT JOIN projects_tasks pt ON ( ut.id_task = pt.id) 
			 LEFT JOIN  projects_phases pp ON ( pt.id_project_phase = pp.id) 
			 LEFT JOIN  projects p ON ( p.id = pp.id_project)
			 LEFT JOIN customers c ON c.id = p.customer_id
			 LEFT JOIN users u ON u.id = ut.id_user";

			$where = "WHERE ut.default=0";
			$groupBy = "GROUP BY p.customer_id, pp.id_project, p.name, ut.id_user, pt.description, ut.comment, ut.date";
			$order = "ORDER BY p.id ASC";

			if (!empty($model->customer_id))
			{
				$customer_id = Customers::getIdByName($model->customer_id);
				$where .= " AND p.customer_id = '{$customer_id}'";
			}
			if (!empty($model->id_project))
			{
				$project_id = (int)$model->id_project;
				$where .= " AND p.id ='{$project_id}'";
			}
			if (!empty($model->id_user))
			{
				$user_id = (int)$model->id_user;
				$where .= " AND ut.id_user ='{$user_id}'";
			}
			if (!empty($model->id_user))
			{
				$user_id = (int)$model->id_user;
				$where .= " AND ut.id_user ='{$user_id}'";
			}
			if (!empty($model->from))
			{
				$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
				$where .= " AND ut.date >= '{$from}'";    
			}
			if (!empty($model->to))
			{
				$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
				$where .= " AND ut.date <= '{$to}'";
			}
			$timesheetSummary = Yii::app()->db->createCommand($select." ".$where." ".$groupBy." ".$order)->queryAll();
		}

		if (!empty($timesheetSummary)) {
			if (!empty($model->file))
			{
				if ($model->file == "Pdf")
					self::createPdf($timesheetSummary);
				elseif ($model->file == "Excel")
					self::createExcel($timesheetSummary);
					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'timesheetSummary' => $timesheetSummary,
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.timesheetSummary.','L',$profit); 
		$file = Utils::getFileReport();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=REPORTS.pdf');
			header('Content-type: application/pdf');
			readfile($file);
		} 
	}
	
	public function createExcel($resp, $profit = null){
		
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
		
		$styleArray = array('font' => array('italic' => true, 'bold'=> true    ),
		'borders' => array(
		'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),
		
		),);
		$objPHPExcel->getActiveSheet()->getStyle('F2:J2')->applyFromArray($styleArray);
		for($i = 4;$i<8 ; $i++){
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$i.':G'.$i);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$i.':J'.$i);
		}
		$sheetId = 0;
		if(isset($_POST['Projects']['id']))
			$id_projet = $_POST['Projects']['id'];
		else
			$id_projet = null;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('F4', 'Customer')
		->setCellValue('F5', 'Project')
		->setCellValue('F6', 'Start Date')
		->setCellValue('F7', 'End Date')
		->setCellValue('H4', $_POST['Projects']['customer_id'])
		->setCellValue('H5', $id_projet)
		->setCellValue('H6', $_POST['Projects']['status'])
		->setCellValue('H7', $_POST['Projects']['id_type']);
		
		$ct = 9;
		foreach($resp as $key=>$expens){
			if( $profit == "Yes")		
				uasort($expens, $this->build_sorter('profit'));
			foreach($expens as $key_project=>$project){ 
			 $types = $project['id_ea'];
			 foreach ($types as $type)
			 	$id_customer = $type['customer_id'];
				
				$ct_first = $ct+2;
				$ct_second = $ct+3;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':'.'J'.$ct)->applyFromArray($styleArray);
				$ct_prim = $ct+4;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct_prim.':'.'J'.$ct_prim)->applyFromArray($styleArray);
				
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
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':T'.$ct)->applyFromArray($styleArray1);
				
				for($k="A", $i=$ct;$k<"U";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$i.':'.++$k.$i);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
				}
							
				
				
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_first, 'Customer: '.Customers::getNameById($id_customer))
				->setCellValue('A'.$ct_second, 'Project: '.Projects::getNameById($key_project))
				->setCellValue('D'.$ct_first, 'Status: '.Projects::getStatusLabel(Projects::getId('status', $key_project)))
				->setCellValue('D'.$ct_second, 'Type: '.Codelkups::getCodelkup(Projects::getId('id_type', $key_project)))
				->setCellValue('A'.$ct, 'EA')
				->setCellValue('C'.$ct, 'Man Days')
				->setCellValue('E'.$ct, 'Actual Man Days')
				->setCellValue('G'.$ct, 'Remaining Man Days')
				->setCellValue('I'.$ct, 'Budget')
				->setCellValue('K'.$ct, 'Actual')
				->setCellValue('M'.$ct, 'Remaining')
				->setCellValue('O'.$ct, 'Actual Rate')
				->setCellValue('Q'.$ct, 'Cost')
				->setCellValue('S'.$ct, 'Profit')
				;
				$index = 0;
				$man_days = 0;
				$actual_man_days = 0;
				$remaining_man_days = 0;
				$budget = 0;
				$actual = 0;
				$actual_echo = 0;
				$remaining_echo = 0;
				$actual_rate_echo =0;
				$profit_echo =0;
				$remaining = 0;
				$actual_rate = 0;
				$cost = 0;
				$profit = 0;
				$types = $project['id_ea'];
				$ct_0 =++$ct;
				$ct_pr = 1; 
				foreach ($types as $key_type =>$val) {
					
					
				if(Projects::getProjectActualManDays($val['id']) != 0)
				{ 
					$actual_echo = Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id'])); 
					$actual += $val['amount']/Projects::getProjectActualManDays($val['id']);
				}
				if(Projects::getProjectActualManDays($val['id']) != 0)
				{ 
					$remaining_echo = Utils::formatNumber($val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id']));
					$remaining+= $val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id']);
				}
				if(Projects::getProjectActualManDays($val['id']) != 0)
				{ 
					$actual_rate_echo = Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id']));
					$actual_rate += $val['amount']/ Projects::getProjectActualManDays($val['id']);
				}
				if(Projects::getProjectActualManDays($key_project) != 0)
				{ 
					$profit_echo =  Utils::formatNumber($val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id'])); 
					$profit+=$val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id']);
				}
				$man_days+= $val['man_days'];
				$remaining_man_days += $val['man_days'] - Projects::getProjectActualManDays($val['id']);
				$cost += Projects::getProjectActualManDays($val['id'])*SystemParameters::getCost()*8;
				$actual_man_days += Projects::getProjectActualManDays($val['id']);
				$budget += $val['amount']; 
				$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.$ct_0, $val['id_ea'])
					->setCellValue('C'.$ct_0, $val['man_days'])
					->setCellValue('E'.$ct_0, Projects::getProjectActualManDays($val['id']))
					->setCellValue('G'.$ct_0, $val['man_days'] - Projects::getProjectActualManDays($val['id']))
					->setCellValue('I'.$ct_0, Utils::formatNumber($val['amount']))
					->setCellValue('K'.$ct_0, $actual_echo)
					->setCellValue('M'.$ct_0, $remaining_echo)
					->setCellValue('O'.$ct_0, $actual_rate_echo)
					->setCellValue('Q'.$ct_0, Utils::formatNumber(Projects::getProjectActualManDays($val['id'])*SystemParameters::getCost()*8))
					->setCellValue('S'.$ct_0, $profit_echo);
					for($k="A",$i=$ct_0;$k<"U";$k++){
						$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
						$objPHPExcel->getActiveSheet()->getStyle(--$k.$i.':'.$k.$i)->applyFromArray($styleLeft);
					}
					$ct_0++;
					$ct_pr++;
				}
				for($k="A",$i=$ct_0;$k<"U";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
				}
				for($k="A",$i=$ct_0;$k<"U";$k++){
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleArray1);
				}
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_0, 'Total')
				->setCellValue('C'.$ct_0, Utils::formatNumber($man_days))
				->setCellValue('E'.$ct_0, Utils::formatNumber($actual_man_days))
				->setCellValue('G'.$ct_0, Utils::formatNumber($remaining_man_days))
				->setCellValue('I'.$ct_0, Utils::formatNumber($budget))
				->setCellValue('K'.$ct_0, Utils::formatNumber($actual))
				->setCellValue('M'.$ct_0, Utils::formatNumber($remaining))
				->setCellValue('O'.$ct_0, Utils::formatNumber($actual_rate))
				->setCellValue('Q'.$ct_0, Utils::formatNumber($cost))
				->setCellValue('S'.$ct_0, Utils::formatNumber($profit))
				;
			$ct = $ct + $ct_pr + 10;
			}
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Projects Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Projects_Reports.xls"');
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