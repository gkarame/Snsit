<?php

class ExpenseSummaryController extends Controller
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
		//print_r($_POST);
		$searchArray = isset($_POST['Expenses']) ? $_POST['Expenses'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/reports/expenseSummary/index' => array(
								'label'=>Yii::t('translations', 'Expense Summary'),
								'url' => array('/reports/expenseSummary/index'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1,
								'search' => $searchArray,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new Expenses('search');
		$model->unsetAttributes();  // clear any default values
		$model->attributes= $searchArray;
		if(isset($model->customer_id))
			$model->customer_name = Customers::getIdByName($model->customer_id); 
		$_POST['Expenses'] = $searchArray;
		$resp = array();
		$message = "";
		if(isset($_POST['Expenses'])){
			$where = " WHERE 1=1 ";
			$where_travel = " WHERE 1=1 ";
			$i = 0;
			$select = "Select ed.type,ed.amount,ed.billable,ed.payable,e.customer_id,e.id,e.project_id,ed.id as id_exp,e.startDate,e.endDate
						FROM expenses_details ed
						LEFT JOIN expenses e ON e.id = ed.expenses_id";
			$select_travel="select t.expense_type as type,t.amount,t.billable,t.id_customer as customer_id,t.id_project as project_id,t.id as id_exp,t.date as startDate,t.date as endDate
								from travel t";
			if(isset($_POST['Expenses']['customer_id'])&& $_POST['Expenses']['customer_id']!=null){
				$customer_id = Customers::getIdByName($_POST['Expenses']['customer_id']);
				$where .= " AND e.customer_id ='$customer_id'";
				$where_travel.=" AND t.id_customer='$customer_id'";

				$i=1;
			}
			if(isset($_POST['Expenses']['project_id'])&& $_POST['Expenses']['project_id']!=null){
				$project_id =$_POST['Expenses']['project_id'];
					$where .= " AND e.project_id ='$project_id'";
					$where_travel.= " AND t.id_project='$project_id'";
				$i=1;	
			}
			if(isset($_POST['Expenses']['startDate'])&& $_POST['Expenses']['startDate']!=null){
				$startDate = DateTime::createFromFormat('d/m/Y',$_POST['Expenses']['startDate'])->format('Y-m-d');
					$where .= " AND e.startDate >='$startDate'";
					$where_travel.= " AND t.date>='$startDate'";
				$i=1;	
			}
			if(isset($_POST['Expenses']['endDate']) && $_POST['Expenses']['endDate']!=null){
				$endDate = DateTime::createFromFormat('d/m/Y',$_POST['Expenses']['endDate'])->format('Y-m-d');
					$where .= " AND e.endDate <='$endDate'";
				$i=1;
			}
			$order = " ORDER BY e.project_id ASC , ed.type ASC";
			$order_travel=" ORDER BY t.id_project ASC, t.expense_type ASC";
				$expenses = Yii::app()->db->createCommand($select.$where.$order)->queryAll();
				$travels = YII::app()->db->createCommand($select_travel.$where_travel.$order_travel)->queryAll();
				foreach ($expenses as $row)
				{
					$resp[$row['customer_id']]['project'] = $row['project_id'];
					$resp[$row['customer_id']]['projects'][$row['project_id']]['type'][$row['type']][$row['id_exp']] = $row;
					
				}
				foreach ($travels as $row)
				{
					$resp[$row['customer_id']]['project'] = $row['project_id'];
					$resp[$row['customer_id']]['projects'][$row['project_id']]['type'][$row['type']][$row['id_exp']] = $row;
					
				}
				//if not result return print a message
				if($resp != null){
					if(isset($_POST['Expenses']['file'])&& $_POST['Expenses']['file']!=null){
						if($_POST['Expenses']['file'] == "Pdf")
							$val = self::createPdf($resp);
						elseif ($_POST['Expenses']['file'] == "Excel")
							$val = self::createExcel($resp);
							
					}
				}else{
					$message = "No search results found";					
				}
				unset($_POST['Expenses']);
				$this->render('index',array(
					'model'=>$model,
					'expenses' => $resp,
					'message' =>$message
				));
				exit;
				
		}
		unset($_POST['Expenses']);
		$this->render('index',array(
			'model'=>$model,
			'expenses' => $resp,
			'message' =>$message
		));
		
	}
	
	public function createPdf($resp){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.expenseSummary.'); 
		$file = Utils::getFileReport();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=REPORTS.pdf');
			header('Content-type: application/pdf');
            readfile(str_ireplace('\\','/',$file));
		} 
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
		if(isset($_POST['Expenses']['project_id']))
		{
			$p= Projects::getNameById($_POST['Expenses']['project_id']);
		}else{
			$p='';
		}
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('F4', 'Customer')
		->setCellValue('F5', 'Project')
		->setCellValue('F6', 'Start Date')
		->setCellValue('F7', 'End Date')
		->setCellValue('H4', $_POST['Expenses']['customer_id'])
		->setCellValue('H5', $p )
		->setCellValue('H6', $_POST['Expenses']['startDate'])
		->setCellValue('H7', $_POST['Expenses']['endDate']);
		
		$ct = 9;
		foreach($resp as $key=>$expens){
			$projects = $expens['projects'];
			foreach($projects as $key_project=>$project){
				
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
				//$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':J'.$ct)->applyFromArray($styleArray1);
				
				for($k="A", $i=$ct;$k<"I";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$i.':'.++$k.$i);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
				}
			 	$types = $project['type'];
				$start_date = new DateTime('0000-00-00');
				$end_datee = strtotime('2050-10-10');
					 foreach ($types as $key_type =>$type) {
						 foreach ($type as $val){
							 if($start_date > $val['startDate'])
								$start_date = $val['startDate'];
							 if($end_datee < $val['endDate'])
								$end_datee = $val['endDate'];
					}	
				}
							
				
				
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_first, 'Customer: '.Customers::getNameById($key))
				->setCellValue('A'.$ct_second, 'Project: '.Projects::getNameById($key_project))
				->setCellValue('D'.$ct_first, 'Start Date: '.date('d/m/Y',strtotime($start_date)))
				->setCellValue('D'.$ct_second, 'End Date: '.date('d/m/Y',strtotime($end_datee)))
				->setCellValue('A'.$ct, 'Item')
				->setCellValue('C'.$ct, 'Expense Type')
				->setCellValue('E'.$ct, 'Amount USD (Billable)')
				->setCellValue('G'.$ct, 'Amount USD (Not Billable)')
				;
				 $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':G'.$ct)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF' 
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':G'.$ct)->applyFromArray($styleArray2); 

				$index = 0;
				$total_bill = 0;
				$total_not_bill = 0;
				$total_amount = 0;
				$types = $project['type'];
				$ct_0 =++$ct;
				$ct_pr = 1; 
				foreach ($types as $key_type =>$type) {
					$bill = 0;
					$not_bill = 0;
					foreach ($type as $val){
						if($val['billable'] == "Yes")
							$bill += $val['amount'];
						else 
							$not_bill += $val['amount']; 
							
						$total_amount += $val['amount'];
					}
					
					
					
					$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.$ct_0, $ct_pr)
					->setCellValue('C'.$ct_0, Codelkups::getCodelkup($key_type))
					->setCellValue('E'.$ct_0, Utils::formatNumber($bill))
					->setCellValue('G'.$ct_0, Utils::formatNumber($not_bill));
					for($k="A",$i=$ct_0;$k<"I";$k++){
						$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
						$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
					}
					$ct_0++;
					$ct_pr++;
					$total_bill += $bill;
					$total_not_bill += $not_bill;
				}
				for($k="A",$i=$ct_0;$k<"I";$k++){
					$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$ct_0.':'.++$k.$ct_0);
				}
				for($k="A",$i=$ct_0;$k<"I";$k++){
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleArray1);
				}
				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('C'.$ct_0, 'Total')
				->setCellValue('E'.$ct_0, Utils::formatNumber($total_bill))
				->setCellValue('G'.$ct_0, Utils::formatNumber($total_not_bill));
			$ct = $ct + $ct_pr + 10;
			}
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Expenses Reports');
		
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
			
		exit;
	}
	
	
}