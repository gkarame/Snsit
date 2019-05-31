<?php

class LicensingController extends Controller
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
		$searchArray = isset($_POST['Licensing']) ? $_POST['Licensing'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/Licensing/index' => array(
						'label'=>Yii::t('translations', 'License Audit Report'),
						'url' => array('/reports/Licensing/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new Licensing('search');
		$model->attributes = $searchArray;
		
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		
		$i = 0;
		$Licensing = array();
		$message = "";

		$select1 = "(select e.approved, ei.man_days,c.id , c.name, 
				IFNULL(c.n_licenses_allowed ,0) as allowed , 
				 IFNULL(c.n_licenses_audited ,0) as audited ,
				  IFNULL( c.date_audit ,' ') as lastaudit,
				  	c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0) as delta
				  from customers c , eas e , eas_items ei";

			$where1=" where c.id=e.id_customer and e.id=ei.id_ea and e.category='25' and e.`status`='2' and c.n_licenses_allowed<>0  and c.n_licenses_allowed>1 and c.status=1 and c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null  and ei.description not like '%Yearly%' and ei.settings_codelkup in ('64','394','395')";



			$groupby1="GROUP BY c.id ,c.name , c.n_licenses_allowed ,c.n_licenses_audited ,c.date_audit, ei.man_days order by approved desc ) ";
			$union="UNION ALL ";

			$select2="(select '' as approved , '0' as man_days, c .id , c.name, 
							IFNULL(c.n_licenses_allowed ,0) as allowed , 
	 						IFNULL(c.n_licenses_audited ,0) as audited ,
	  						IFNULL( c.date_audit ,' ') as lastaudit,
	  						c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  as delta
	  						from customers c ";


			$where2=" where  c.n_licenses_allowed<>0 and c.n_licenses_allowed>1 and c.status=1  and c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)  is not null
 				and not EXISTS( select 1 from eas where category='25' and status='2' and id_customer=c.id) ";


 			if(!empty($model->id)){
 				$customer_id = Customers::getIdByName($model->id);
 				$where1.=" AND c.id = '{$customer_id}'";
 				$where2.=" AND c.id = '{$customer_id}'";
 			}

 			if($model->delta != null){
 				
					$where1=$where1." and c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)<".$model->delta." ";

 					$where2=$where2." and c.n_licenses_allowed - IFNULL(c.n_licenses_audited ,0)<".$model->delta." ";
	
 			}
 			


			$Licensing = Yii::app()->db->createCommand($select1.$where1.$groupby1.$union.$select2.$where2.") order by delta asc")->queryAll(); 

		

		if (!empty($Licensing)) {
			if (!empty($model->file))
			{
				if ($model->file == "Pdf")
					self::createPdf($Licensing);
				elseif ($model->file == "Excel")
					self::createExcel($Licensing);
					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'Licensing' => $Licensing,
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.Licensing.','L',$profit); 
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
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($styleArray);
		 $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
	        $styleArray2 = array(
	            'font' => array(
	                'color' => array('rgb' => 'FFFFFF'
	                )
	            ),
	            'borders' => array(
	                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
	                )
	            ));

	        $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($styleArray2); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.'4', 'Customer Name')
					->setCellValue('B'.'4', '# of Licenses Allowed')
					->setCellValue('C'.'4', '# of Licenses Audited')
					->setCellValue('D'.'4', 'Last Audit Date')
					->setCellValue('E'.'4', 'Delta');
					
		$ct = 6;
		foreach($resp as $key=>$tim){
				//My Variables
				$customer_id = $tim['id'];
				$allowed = $tim['allowed'];
				$audited = $tim['audited'];
				$lastaudit = $tim['lastaudit'];
				$delta = $tim['delta'];
				
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':E'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, Customers::getNameById($customer_id))
						->setCellValue('B'.$ct, $allowed)
						->setCellValue('C'.$ct, $audited)
						->setCellValue('D'.$ct, $lastaudit)
						->setCellValue('E'.$ct, $delta)	;
						
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
			header('Content-Disposition: attachment;filename="Licensing_Report.xls"');
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