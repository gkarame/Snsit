<?php

class ProjectSummaryController extends Controller
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
		
		$searchArray = isset($_POST['Projects']) ? $_POST['Projects'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/reports/projectsummary/index' => array(
								'label'=>Yii::t('translations', 'Project Profitability'),
								'url' => array('/reports/projectsummary/index'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1,
								'search' => $searchArray,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new Projects('search');
		$model->attributes = $searchArray;
		$_POST['Projects'] = $searchArray;
		if(isset($model->customer_id))
			$model->id_customer = Customers::getIdByName($model->customer_id); 
		$resp = array();
		$message = "";
		$profit = "No";
		if (isset($_POST['Projects']))
		{

			$where = " WHERE 1=1 and  e.id_project = p.id AND e.id_customer = p.customer_id and e.id = ei.id_ea and p.customer_id = c.id 
						";
			$order = " group by e.id ORDER BY ";
			$i = 0;
			$select = "	select e.id_customer as customer_id,e.id as id_ea,e.id_project,sum(ei.man_days) as man_days , sum(ei.amount) as amount ,e.id_parent_project,p.id_type,c.name
						FROM projects p, eas e, eas_items ei, customers c ";
			if (isset($_POST['Projects']['customer_id'])&& $_POST['Projects']['customer_id']!=null)
			{
				$customer_id = Customers::getIdByName($_POST['Projects']['customer_id']);
				$where .= " AND p.customer_id ='$customer_id'";
				$i = 1;
			}
			if (isset($_POST['Projects']['id'])&& $_POST['Projects']['id']!=null)
			{
				$project_id =$_POST['Projects']['id'];
				$where .= " AND p.id ='$project_id'";
				$i = 1;
			}
			if (isset($_POST['Projects']['project_manager'])&& $_POST['Projects']['project_manager']!=null)
			{

				$pm = Users::getIdByNameTrim($_POST['Projects']['project_manager']);  
				$where .= " AND p.project_manager =".$pm." ";
			}
			if (isset($_POST['Projects']['status'])&& $_POST['Projects']['status']!=null)
			{
				$status= $_POST['Projects']['status'];
				if($status == 'Inactive')
					$where .= " AND p.status = 0 ";
				else 
					$where .= " AND p.status ='$status'";
				$i = 1;
			}
			if (isset($_POST['Projects']['id_type']) && $_POST['Projects']['id_type']!=null)
			{
				$id_type = $_POST['Projects']['id_type'];
				$where .= " AND p.id_type ='$id_type'";
				$i = 1;
			}
			if (isset($_POST['Projects']['order']) && $_POST['Projects']['order']!=null)
			{
				$order_type = $_POST['Projects']['order'];
				if($order_type == 'Customer')
					$order .= " c.name ASC,";
				else if($order_type == 'Profit') 
					$profit = "Yes";
				$i = 1;
			}
			$order .= " p.id ASC ";  
			$expenses = Yii::app()->db->createCommand($select.$where.$order)->queryAll();
			foreach ($expenses as $row)
			{

				$resp['projects'][$row['id_project']]['id_ea'][$row['id_ea']] = $row; 
				$actuals=Projects::getProjectActualManDays($row['id_project']);
				if($actuals != 0)
					$resp['projects'][$row['id_project']]['profit'] = $row['amount'] - $row['amount']/ $actuals;
				else 
					$resp['projects'][$row['id_project']]['profit'] = 0;
				if($row['id_parent_project'] != null && isset($_POST['Projects']['id_type']) && $_POST['Projects']['id_type']!=null && $_POST['Projects']['id_type'] !='28' ){
					$resp[$row['customer_id']]['projects'][$row['id_parent_project']]['id_ea'][$row['id_ea']] = $row;
					if($actuals != 0)
						$resp[$row['customer_id']]['projects'][$row['id_project']]['profit'] += $row['amount'] - $row['amount']/ $actuals;
				
				}
				
			}

		//	print_r($resp);exit;
			if ($resp != null)
			{	
				if (isset($_POST['Projects']['file'])&& $_POST['Projects']['file']!=null)
				{
					if($_POST['Projects']['file'] == "Pdf")
						self::createPdf($resp,$profit);
					elseif ($_POST['Projects']['file'] == "Excel")
						self::createExcel($resp,$profit);
						
				}
			}else{
				$message = "No search results found";					
			}
			$this->render('index',array(
					'model'=>$model,
					'projects' => $resp,
					'message' =>$message,
					'profit' => $profit
			));
			exit;
		}
		
		$this->render('index',array(
			'model'=>$model,
			'projects' => $resp,
			'message' => $message,
			'profit' => $profit
		));
		
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.projectSummary.','L',$profit); 
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

				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':S'.$ct)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF'
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':S'.$ct)->applyFromArray($styleArray2); 


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
					
					
				if(Projects::getProjectActualManDays($val['id_project']) != 0)
				{ 
					$actual_echo = Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id_project'])); 
					$actual += $val['amount']/Projects::getProjectActualManDays($val['id_project']);
				}
				if(Projects::getProjectActualManDays($val['id_project']) != 0)
				{ 
					$remaining_echo = Utils::formatNumber($val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id_project']));
					$remaining+= $val['amount'] - $val['amount']/Projects::getProjectActualManDays($val['id_project']);
				}
				if(Projects::getProjectActualManDays($val['id_project']) != 0)
				{ 
					$actual_rate_echo = Utils::formatNumber($val['amount']/Projects::getProjectActualManDays($val['id_project']));
					$actual_rate += $val['amount']/ Projects::getProjectActualManDays($val['id_project']);
				}
				if(Projects::getProjectActualManDays($key_project) != 0)
				{ 
					$profit_echo =  Utils::formatNumber($val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id_project'])); 
					$profit+=$val['amount']- $val['amount']/Projects::getProjectActualManDays($val['id_project']);
				}
				$man_days+= $val['man_days'];
				$remaining_man_days += $val['man_days'] - Projects::getProjectActualManDays($val['id_project']);
				$cost += Projects::getProjectActualManDays($val['id_project'])*SystemParameters::getCost()*8;
				$actual_man_days += Projects::getProjectActualManDays($val['id_project']);
				$budget += $val['amount']; 
				$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.$ct_0, $val['id_ea'])
					->setCellValue('C'.$ct_0, $val['man_days'])
					->setCellValue('E'.$ct_0, Projects::getProjectActualManDays($val['id_project']))
					->setCellValue('G'.$ct_0, $val['man_days'] - Projects::getProjectActualManDays($val['id_project']))
					->setCellValue('I'.$ct_0, Utils::formatNumber($val['amount']))
					->setCellValue('K'.$ct_0, $actual_echo)
					->setCellValue('M'.$ct_0, $remaining_echo)
					->setCellValue('O'.$ct_0, $actual_rate_echo)
					->setCellValue('Q'.$ct_0, Utils::formatNumber(Projects::getProjectActualManDays($val['id_project'])*SystemParameters::getCost()*8))
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