<?php

class CustomerPlanController extends Controller
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
		$this->setPageTitle(Yii::app()->name.' - Customer Support Plan');
	}
	
	public function actionIndex()
	{
		$searchArray = isset($_POST['CustomerPlan']) ? $_POST['CustomerPlan'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/CustomerPlan/index' => array(
						'label'=>Yii::t('translations', 'Customer Support Plan'),
						'url' => array('/reports/CustomerPlan/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new CustomerPlan('search');
		$model->attributes = $searchArray;
		
		$i = 0;
		$CustomerPlan = array();
		$workingavg = array();
		$billabilty = array();

		$snapshots = array();
		$message = "";


				$select="select distinct c.id, c.name,m.id_maintenance, m.contract_description, m.support_service from customers c left outer join maintenance m  on c.id=m.end_customer left outer join users u  on c.ca=u.id  left outer join users u2  on c.account_manager=u2.id";
				$where=" where (m.status='active' or m.status='Inactive') and c.status='1' and m.support_service in (501,502) " ;
				$order=" order by name ";


			if (!empty($model->name))
			{	

				$where.=" and c.name like '%".$model->name."%' ";
			
			}	
			
			if (!empty($model->support_service))
			{	
				$where.=" and m.support_service=  '".$model->support_service."' ";
			
			}
		//	print_r($select." ".$where."  ".$order);exit;
			 $snapshots = Yii::app()->db->createCommand($select." ".$where."  ".$order )->queryAll(); 

			$CustomerPlan= $snapshots;
		

	

		if (!empty($CustomerPlan)) {
			if (!empty($model->file))
			{				
				if ($model->file == "Excel"){
					self::createExcel($CustomerPlan);
				}					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'CustomerPlan' => $CustomerPlan,			
				'message' =>$message,
		));
	}	
	
	public function createExcel($CustomerPlan, $profit = null)
	{
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

		//print_r($CustomerPlan);exit;
		$counter=4;
			$counter2=5;
			$counter3=6;
			$ct = 7;

		foreach($CustomerPlan as $CustomerP)
		{
			
			$sheetId = 0;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$counter, 'Customer')
			->setCellValue('A'.$counter2, 'Maintenance Contract')
			->setCellValue('A'.$counter3, 'Support Service')
			->setCellValue('C'.$counter, Customers::getNameById($CustomerP['id']))
			->setCellValue('C'.$counter2, $CustomerP['contract_description'])
			->setCellValue('C'.$counter3, Codelkups::getCodelkup($CustomerP['support_service']))
			;

	        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':H'.$ct)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
	        $styleArray2 = array(
	            'font' => array(
	                'color' => array('rgb' => 'FFFFFF'
	                )
	            ),
	            'borders' => array(
	                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
	                )
	            ));

	        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':H'.$ct)->applyFromArray($styleArray2); 

			$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct, 'Item')
				->setCellValue('B'.$ct, 'Service')
				->setCellValue('G'.$ct, 'Quota')
				->setCellValue('H'.$ct, 'Actuals')
				;

		$services= MaintenanceServices::getSupportServicesPerMaint($CustomerP['id_maintenance']);
		$ct_pr = 1; 

			foreach($services as $service)
			{
				
				$ct_first = $ct+1;
				$ct_second = $ct+2;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':'.'H'.$ct)->applyFromArray($styleArray);
				$ct_prim = $ct+3;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct_first.':'.'H'.$ct_first)->applyFromArray($styleArray);
				
				$sheetId = 0;
				
				
				$styleArray1 = array('font' => array( 'bold'=> true,    ),
				'borders' => array(
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				),);
				
				$styleLeft = array('font' => array( 'bold'=> true,    ),
				'borders' => array(
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
				),);
				
				
				$ct_0 =++$ct;
				
				if ($service['id_service'] == 7 || $service['id_service'] == 19) 
    			{
    				$quota= 'Unlimited';	
    			}else
    			{
    				$quota= Utils::formatNumber($service['quota']);
    			}

				$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.$ct, $ct_pr)
					->setCellValue('B'.$ct, MaintenanceServices::getNameById($service['id_service']))
					->setCellValue('G'.$ct, $quota)
					->setCellValue('H'.$ct, Utils::formatNumber(MaintenanceServices::getActualExcel($CustomerP['id_maintenance'],$service['id_service'],$service['field_type'])))
					;
					
				
				$ct_pr++;	
			}
			$counter= $ct+3;
			$counter2=$ct+4;
			$counter3=$ct+5;
			$ct= $counter3+1;
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Maintenance Contracts Reports');
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Maintenance_Reports.xls"');
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