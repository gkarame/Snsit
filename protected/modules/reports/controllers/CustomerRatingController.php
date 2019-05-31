<?php

class CustomerRatingController extends Controller
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
		$searchArray = isset($_POST['CustomerRating']) ? $_POST['CustomerRating'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/CustomerRating/index' => array(
						'label'=>Yii::t('translations', 'Customer Ratings'),
						'url' => array('/reports/CustomerRating/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new CustomerRating('search');
		$model->attributes = $searchArray;
		
		$i = 0;
		$CustomerRating = array();
	
		$snapshots = array();
		$message = "";


				$select="select c.name ,s.sd_no, s.rate ,s.rate_comment, DATE(s.rate_date) AS rate_date , s.assigned_to, MONTH(s.rate_date) as month, YEAR(s.rate_date) as year from support_desk s ,customers c  ";
				$where=" where s.id_customer=c.id and s.rate <>0 and s.rate is not null and s.rate <4 " ;
				$order=" order by s.rate desc";

			
		
			if (!empty($model->name))
			{	

				$where.=" and c.name like '%".$model->name."%' ";
			
			}	
			
			

			if (!empty($model->sd_no))
			{	
				$where.=" and s.sd_no=  '".$model->sd_no."' ";
			
			}


			if (!empty($model->rate))
			{	
				$where.=" and s.rate=  '".$model->rate."' ";
			
			}

			if (!empty($model->assigned_to))
			{	
				$id= Users::getIdByName($model->assigned_to);
				$where.=" and s.assigned_to=  '".$id."' ";
			
			}
			if (!empty($model->month))
			{	
				
				$where.=" and MONTH(s.rate_date)=  '".$model->month."' ";
			
			}
			if (!empty($model->year))
			{	
				
				$where.=" and YEAR(s.rate_date)=  '".$model->year."' ";
			
			}else
			{
				$where.=" and YEAR(s.rate_date)=   YEAR(CURRENT_DATE())";
			}
					
		
			 $snapshots = Yii::app()->db->createCommand($select." ".$where."  ".$order )->queryAll(); 

			$CustomerRating= $snapshots;
		

	

		if (!empty($CustomerRating)) {
			if (!empty($model->file))
			{				
				if ($model->file == "Excel"){
					self::createExcel($CustomerRating);
				}					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'CustomerRating' => $CustomerRating,			
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.CustomerRating.','L',$profit); 
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
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleArray);
		 $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray2 = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray($styleArray2); 

		$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.'4', 'Customer Name')
					->setCellValue('B'.'4', 'SR#')
					->setCellValue('C'.'4', 'Rate')
					->setCellValue('D'.'4', 'Comment');
					
		$ct = 6;
		foreach($resp as $key=>$tim){
				//My Variables
				$customer = $tim['name'];			
				$sr = $tim['sd_no'];			
				$rate = $tim['rate'];			
				$comment = $tim['rate_comment'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':D'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, $customer)
						->setCellValue('B'.$ct, $sr)
						->setCellValue('C'.$ct, $rate)
						->setCellValue('D'.$ct, $comment);
						
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
		$objPHPExcel->getActiveSheet()->setTitle('Customer Rating Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Customer_rating_Report.xls"');
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