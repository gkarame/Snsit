<?php

class eaSummaryController extends Controller
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
		$searchArray = isset($_POST['eaSummary']) ? $_POST['eaSummary'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/eaSummary/index' => array(
						'label'=>Yii::t('translations', 'EA Summary'),
						'url' => array('/reports/eaSummary/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new eaSummary('search');
		$model->attributes = $searchArray;
		
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		
		$i = 0;
		$eaSummary = array();
		$message = "";

			$select = "SELECT DISTINCT c.name, c.id as customer_id, e.ea_number as eanumber, e.approved as eadate, SUM(man_days) as licnumber,e.id_customer from customers c , eas e , eas_items ei";
			$where = "where c.id=e.id_customer and e.id=ei.id_ea and e.category='25'";
			$groupBy =	"GROUP BY e.id_customer";
			
			if(!empty($model->id)){
 				$customer_id = Customers::getIdByName($model->id);
 				$where.=" AND c.id = '{$customer_id}'";
 			}
			
			$eaSummary = Yii::app()->db->createCommand($select." ".$where." ".$groupBy)->queryAll(); 
		

		if (!empty($eaSummary)) {
			if (!empty($model->file))
			{
				if ($model->file == "Pdf")
					self::createPdf($eaSummary);
				elseif ($model->file == "Excel")
					self::createExcel($eaSummary);
					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'eaSummary' => $eaSummary,
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.eaSummary.','L',$profit); 
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
					->setCellValue('B'.'4', 'EA #')
					->setCellValue('C'.'4', '# of Licenses')
					->setCellValue('D'.'4', 'EA Approve Date');
					
		$ct = 6;
		foreach($resp as $key=>$tim){
				//My Variables
				$customer_id = $tim['customer_id'];
				$eanumber = $tim['eanumber'];
				$licnumber = $tim['licnumber'];
				$eadate = $tim['eadate'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':D'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, Customers::getNameById($customer_id))
						->setCellValue('B'.$ct, $eanumber)
						->setCellValue('C'.$ct, $licnumber)
						->setCellValue('D'.$ct, $eadate);
						
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
			header('Content-Disposition: attachment;filename="eaSummary_Report.xls"');
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