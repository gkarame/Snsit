<?php

class FbrsListController extends Controller
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
	public function init()
	{
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - FBRs List');
	}
	public function actionIndex()
	{
		$searchArray = isset($_POST['fbrsList']) ? $_POST['fbrsList'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/fbrsList/index' => array(
						'label'=>Yii::t('translations', 'FBRs List'),
						'url' => array('/reports/fbrsList/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new fbrsList('search');
		$model->attributes = $searchArray;
		
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		
		$i = 0;
		$fbrsList = array();
		$message = "";
		$where="";
		$select = "select pt.id as task, pt.description, pt.billable, pt.complexity,c.name as customer, p.name as project, p.project_manager, pp.description as phase, pt.module, pt.keywords, pt.existsfbr, pt.parent_fbr, pt.notes, p.product, p.version from projects_tasks pt, projects_phases pp, projects p, users u, customers c where c.id= p.customer_id and u.id= p.project_manager and pt.type=1 and pt.id_project_phase=pp.id and pp.id_project=p.id   AND YEAR(pt.adddate) >2016  ";

		
		if (!empty($model->project))
			{				
				$p= Projects::getIdByName($model->project);
				
				 $where .= " AND p.id=".$p; 
			}

		if (!empty($model->customer))
			{	

				$where.=" and c.name like '%".$model->customer."%' ";
			
			}	

		if (!empty($model->project_manager))
			{				
				$first = strstr($model->project_manager, '  ', true);
			
				$last = substr(strstr($model->project_manager, '  '),2);
				
				 $where .= " AND u.firstname like '%$first%' AND u.lastname like '%$last%'"; 
			}
		
		if (!empty($model->description))
			{				
				 $where .= " AND pt.description like '%".$model->description."%'"; 
			}

		if (!empty($model->module))
			{				
				 $where .= " AND pt.module =".$model->module." "; 
			}

		if (!empty($model->keywords))
			{				
				  $where .= " AND pt.keywords like '%".$model->keywords."%'"; 
			}
 		
 		if (!empty($model->complexity))
			{				
				 $where .= " AND pt.complexity =".$model->complexity." "; 
			}

		if (!empty($model->existsfbr))
			{				
				 $where .= " AND pt.existsfbr =".$model->existsfbr." "; 
			}

		if (!empty($model->product))
			{				
				 $where .= " AND p.product =".$model->product." "; 
			}

		if (!empty($model->version))
			{				
				 $where .= " AND p.version =".$model->version." "; 
			}


		$fbrsList = Yii::app()->db->createCommand($select.$where)->queryAll(); 

		
		if (!empty($fbrsList)) {
			if (!empty($model->file))
			{
				if ($model->file == "Pdf")
					self::createPdf($fbrsList);
				elseif ($model->file == "Excel")
					self::createExcel($fbrsList);
					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'fbrsList' => $fbrsList,
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.fbrsList.','L',$profit); 
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
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->applyFromArray($styleArray);
		$objPHPExcel->setActiveSheetIndex($sheetId)

					->setCellValue('A'.'4', 'Customer')
					->setCellValue('B'.'4', 'Project')
					->setCellValue('C'.'4', 'PM')
					->setCellValue('D'.'4', 'Product')
					->setCellValue('E'.'4', 'Version')
					->setCellValue('F'.'4', 'Phase')
					->setCellValue('G'.'4', 'FBR')
					->setCellValue('H'.'4', 'Module')
					->setCellValue('I'.'4', 'Keywords')
					->setCellValue('J'.'4', 'complexity')
					->setCellValue('K'.'4', 'Previously Done?')
					->setCellValue('L'.'4', 'Parent FBR')
					->setCellValue('M'.'4', 'Assigned Resources')
					->setCellValue('N'.'4', 'Actual MDs')
					->setCellValue('O'.'4', 'Notes');
			
			$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF'
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A4:O4')->applyFromArray($styleArray2); 
		        			
		$ct = 5;
		foreach($resp as $key=>$tim){
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':C'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, $tim['customer'])
						->setCellValue('B'.$ct, $tim['project'])
						->setCellValue('C'.$ct, Users::getNameById($tim['project_manager']))
						->setCellValue('D'.$ct, Codelkups::getCodelkup($tim['product']))
						->setCellValue('E'.$ct, Codelkups::getCodelkup($tim['version']))
						->setCellValue('F'.$ct, $tim['phase'])
						->setCellValue('G'.$ct, $tim['description'])
						->setCellValue('H'.$ct, Codelkups::getCodelkup($tim['module']))
						->setCellValue('I'.$ct, $tim['keywords'])
						->setCellValue('J'.$ct, ProjectsTasks::getComplexityLabel($tim['complexity']))
						->setCellValue('K'.$ct, ProjectsTasks::getExistsLabel($tim['existsfbr']))
						->setCellValue('L'.$ct, ProjectsTasks::getTPName($tim['parent_fbr']))
						->setCellValue('M'.$ct, ProjectsTasks::getAllUsersTaskReport($tim['task']))
						->setCellValue('N'.$ct, Utils::formatNumber(ProjectsTasks::getTimeSpentperTaskID($tim['task']),2))
						->setCellValue('O'.$ct, $tim['notes'])
						;
						
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
			header('Content-Disposition: attachment;filename="FBRs_Report.xls"');
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