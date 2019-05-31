<?php

class CSDController extends Controller
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
		$searchArray = isset($_POST['CSD']) ? $_POST['CSD'] : Utils::getSearchSession();

		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/CSD/index' => array(
						'label'=>Yii::t('translations', 'Customer Solution'),
						'url' => array('/reports/CSD/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new CSD('search');
		$model->attributes = $searchArray;
		
		$i = 0;
		$CSD = array();
		$workingavg = array();
		$billabilty = array();

		$snapshots = array();
		$message = "";


				$select="select distinct c.id, c.name, c.strategic, GROUP_CONCAT(m.soft_version) as soft_version, GROUP_CONCAT(m.product) as product,c.support_weekend, c.industry , GROUP_CONCAT(m.wms_db_type) as wms_db_type, c.ca, c.cs_representative, c.product_type , c.erp , c.brands, c.account_manager,c.n_licenses_allowed as licenses from customers c left outer join maintenance m  on c.id=m.end_customer left outer join users u  on c.ca=u.id  left outer join users u2  on c.account_manager=u2.id ";
				$where=" where (m.status='active' or m.status='Inactive') and c.status='1' " ;
				$order=" group by c.name order by name , product asc";

			
		 	

			if (!empty($model->name))
			{	

				$where.=" and c.name like '%".$model->name."%' ";
			
			}	
			
			if (!empty($model->industry))
			{
				$where.=" and (  ";
				$industries= $model->industry;
				$industries=array_filter($industries);
				$x= count($industries);
				
				foreach ($industries as $industry) {
					$x--;
					$where.=" c.industry like '%".$industry."%' ";
					if ($x>0)
					{
						$where.=' or ';
					}
					else
					{
						$where .=")"; 
					}
					
				}


			//	$where.=" and c.industry=  '".$model->industry."' ";
			
			}

			if (!empty($model->product))
			{	
				$where.=" and m.product=  '".$model->product."' ";
			
			}

			if (!empty($model->country))
			{	
				$where.=" and c.country=  ".$model->country." ";
			
			}

			if (!empty($model->account_manager))
			{

				$account_manager=Users::getIdByName($model->account_manager);
				
				$where .= " AND u2.id =".$account_manager." ";


				/*$account_manager = $model->account_manager;
				$first = strstr($account_manager, '  ', true);
				$last = substr(strstr($account_manager, '  '),1);
				$last_name =ltrim($last);
				$where .= " AND u2.firstname like '%$first%' AND u2.lastname like '%$last_name%'";*/
				
			}
			
				if (!empty($model->erp))
			{	
					$where.=" and c.erp like  '%".$model->erp."%' ";
			}
				if (!empty($model->brands))
			{	
				
				$where.=" and c.brands like  '%".$model->brands."%' ";
			}
				if (!empty($model->soft_version))
			{	
				$where.=" and m.soft_version = '".$model->soft_version."' ";
			}	
				if (!empty($model->product_type))
			{	
					$where.=" and c.product_type like  '%".$model->product_type."%' ";
			}
			
			if (!empty($model->ca))
			{	
				/*$ca = $model->ca;
				$first1 = strstr($ca, '  ', true);
				$last2 = substr(strstr($ca, '  '),1);
				$lastname2=ltrim($last2);*/

				$ca=Users::getIdByName($model->ca);
				
				$where .= " AND u.id =".$ca." ";
				//$where .= " AND u.firstname like '%$first1%' AND u.lastname like '%$lastname2%'";
				
			}

				if (!empty($model->wms_db_type))
			{	
					$where.=" and m.wms_db_type =  '".$model->wms_db_type."' ";
			}

			//print_r($model); 

			//print_r($select." ".$where."  ".$order);exit;
			 $snapshots = Yii::app()->db->createCommand($select." ".$where."  ".$order )->queryAll(); 

			$CSD= $snapshots;
		

	

		if (!empty($CSD)) {
			if (!empty($model->file))
			{				
				if ($model->file == "Excel"){
					self::createExcel($CSD);
				}					
			}
		} else {
			$message = "No search results found";					
		}
		
		$this->render('index',array(
				'model'=>$model,
				'CSD' => $CSD,			
				'message' =>$message,
		));
	}
	
	public function createPdf($resp,$profit = null){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.CSD.','L',$profit); 
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
		        
        $objPHPExcel->getActiveSheet()->getStyle('A4:N4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray2 = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArray2); 
		$objPHPExcel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArray);
		$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.'4', 'Customer Name')
					->setCellValue('B'.'4', 'Industry')					
					->setCellValue('C'.'4', 'Product')
					->setCellValue('D'.'4', 'Type of Items')
					->setCellValue('E'.'4', 'Brands')
					->setCellValue('F'.'4', 'Account Manager')
					->setCellValue('G'.'4', 'CA')
					->setCellValue('H'.'4', 'CS Representative')
					->setCellValue('I'.'4', 'Support Plan')
					->setCellValue('J'.'4', 'WMS Version')
					->setCellValue('K'.'4', 'ERP')
					->setCellValue('L'.'4', 'WMS DB Type')
					->setCellValue('M'.'4', 'Strategic')
					->setCellValue('N'.'4', 'Allowed Licenses');
		$ct = 5;
		foreach($resp as $key=>$tim){
				//My Variables
				$customer = $tim['name'];
				$product =  Codelkups::getCodelkupPerMultiple($tim['product']);
				$industry = Customers::getIndustry($tim['industry']);
				$strategic= $tim['strategic']; 
				$brands = $tim['brands'];
				$cs = Users::getNameById($tim['cs_representative']);
				$account_manager = Users::getNameById($tim['account_manager']);
			    $ca =   Users::getNameById($tim['ca']);
				$product_type = $tim['product_type'];
				$soft_version =  Codelkups::getCodelkupPerMultiple($tim['soft_version']);
				$wms_db_type =   Codelkups::getCodelkupPerMultiple($tim['wms_db_type']);
				$erp = $tim['erp'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':M'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, $customer)
						->setCellValue('B'.$ct, $industry)
						->setCellValue('C'.$ct, $product)
						->setCellValue('D'.$ct, $product_type)
						->setCellValue('E'.$ct, $brands)
						->setCellValue('F'.$ct, $account_manager)
						->setCellValue('G'.$ct, $ca)
						->setCellValue('H'.$ct, $cs)
						->setCellValue('I'.$ct, $tim['support_weekend'])
						->setCellValue('J'.$ct, $soft_version)
						->setCellValue('K'.$ct, $erp)
						->setCellValue('L'.$ct, $wms_db_type)
						->setCellValue('M'.$ct, $strategic)
						->setCellValue('N'.$ct,  $tim['licenses']);

						$ct=$ct+1;		
		}
				
	
		
	
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Projects Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Customer_solution_Report.xls"');
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