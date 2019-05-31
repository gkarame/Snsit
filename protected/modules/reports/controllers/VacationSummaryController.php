<?php

class VacationSummaryController extends Controller
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
		$searchArray = isset($_POST['UserPersonalDetails']) ? $_POST['UserPersonalDetails'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/reports/vacationsummary/index' => array(
						'label'=>Yii::t('translations', 'Vacation Report'),
						'url' => array('/reports/vacationsummary/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1,
						'search' => $searchArray,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		
		$model = new UserPersonalDetails('search');
		$model->attributes = $searchArray;
		$_POST['UserPersonalDetails'] = $searchArray;
		$resp = array();
		$user = "";
		$message = "";
		$checkAdmin=0;
		if (isset($_POST['UserPersonalDetails']))
		{		
			if (isset($_POST['UserPersonalDetails']['id_user']) && $_POST['UserPersonalDetails']['id_user'] != null)
			{
				
				$id= Users::getIdByName( $_POST['UserPersonalDetails']['id_user']);
				$checkAdmin=Users::getcountAdminUser($id);
			}
			else
			{
			
				$select2forAdmins=  "
						SELECT u.id, u.firstname, u.lastname, upd.branch 
						FROM users u 
						LEFT JOIN user_personal_details upd ON u.id = upd.id_user 
						LEFT JOIN requests rt ON u.id = rt.user_id
						";
			
			}
			$where = " WHERE 1=1 ";
			$where2 = " WHERE 1=1 ";
			$i = 0;
			
			if($checkAdmin>0)
			{
				$select = "
						SELECT u.id, u.firstname, u.lastname, upd.branch 
						FROM users u 
						LEFT JOIN user_personal_details upd ON u.id = upd.id_user 
						LEFT JOIN requests rt ON u.id = rt.user_id
						";
			}
			else
			{
				$select = "SELECT u.id, u.firstname, u.lastname, upd.branch
						FROM users u 
						LEFT JOIN user_personal_details upd ON u.id = upd.id_user
						LEFT JOIN user_time ut ON u.id = ut.id_user
						";
			}
			if (isset($_POST['UserPersonalDetails']['id_user']) && $_POST['UserPersonalDetails']['id_user'] != null)
			{
				$user = $_POST['UserPersonalDetails']['id_user'];
				$first = strstr($user, ' ', true);
				$last = substr(strstr($user, ' '),1);
				$where .= " AND u.firstname = '$first' AND u.lastname = '$last'";
				$i=1;
			}
			if (isset($_POST['UserPersonalDetails']['branch'])&& $_POST['UserPersonalDetails']['branch'] != null)
			{
				$branch = (int) $_POST['UserPersonalDetails']['branch'];
					$where .= " AND upd.branch ='$branch'";
					$where2.= " AND upd.branch ='$branch'";
				$i=1;	
			} 
			if (isset($_POST['UserPersonalDetails']['years'])&& $_POST['UserPersonalDetails']['years'] != null)
			{
				$years = (int) $_POST['UserPersonalDetails']['years'];
				if ($years == date("Y")) {
					$where .= " AND u.active = 1";
					$where2 .= " AND u.active = 1";
				}
				
				$next_year = $years+1;
				if($checkAdmin>0)
				{	$where .= " AND rt.status='1' and ((YEAR(rt.startDate) = '$years' AND MONTH(rt.startDate) != '01') || (YEAR(rt.startDate) = '$next_year' AND MONTH(rt.startDate) = '01'))";}
				else
				{	$where .= " AND ((YEAR(ut.date) = '$years' AND MONTH(ut.date) != '01') || (YEAR(ut.date) = '$next_year' AND MONTH(ut.date) = '01'))";
				}
				$where2 .= " AND  upd.sns_admin='1' AND rt.status='1' and  ((YEAR(rt.startDate) = '$years' AND MONTH(rt.startDate) != '01') || (YEAR(rt.startDate) = '$next_year' AND MONTH(rt.startDate) = '01'))";
				$i=1;	
			} else {
				$i = 0;
				$message = "Year is mandatory.";
			}
			$order = " ORDER BY u.firstname ASC";
			$group = " GROUP BY u.id";
			if ($i != 0)
			{
			
		
				$expenses = Yii::app()->db->createCommand($select.$where.$group.$order)->queryAll();
				
				foreach ($expenses as $row)
				{
					$resp[$row['branch']][] = $row;
				}
					
				if (!(isset($_POST['UserPersonalDetails']['id_user']) && $_POST['UserPersonalDetails']['id_user'] != null))
				{
					$seconds= Yii::app()->db->createCommand($select2forAdmins.$where2.$group.$order)->queryAll();
					foreach ($seconds as $row)
					{
						$resp[$row['branch']][] = $row;
					}
				}
				if ($resp != null) {
					if (isset($_POST['UserPersonalDetails']['file'])&& $_POST['UserPersonalDetails']['file'] != null)
					{
						if ($_POST['UserPersonalDetails']['file'] == "Pdf")
							self::createPdf($resp);
						elseif ($_POST['UserPersonalDetails']['file'] == "Excel")
							self::createExcel($resp);
					}
				} 
				else
				{
					$message .= "No search results found";
				}
				
				unset($_POST['UserPersonalDetails']);
				$this->render('index',array(
					'model'=>$model,
					'branches' => $resp,
					'year' => $years,
					'message' => $message
				));
				exit;
			}
		}

		unset($_POST['UserPersonalDetails']);
		$this->render('index',array(
			'model'=>$model,
			'branches' => $resp,
			'year' => "",
			'message' => $message
		));
		
	}
	
	public function createPdf($resp){
		$this->generatePdf('reports',$resp,'application.modules.reports.views.vacationSummary.','L'); 
		$file = Utils::getFileReport();	
		if ($file !== null) 
		{
			header('Content-disposition: attachment; filename=REPORTS.pdf');
			header('Content-type: application/pdf');
			readfile($file);
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
		for($i = 4;$i<8 ; $i++)
		{
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$i.':G'.$i);
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$i.':J'.$i);
		}
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('F4', 'Resource')
		->setCellValue('F5', 'Year')
		->setCellValue('F6', 'Branch')
		->setCellValue('H4', $_POST['UserPersonalDetails']['id_user'])
		->setCellValue('H5', $_POST['UserPersonalDetails']['years'])
		->setCellValue('H6', $_POST['UserPersonalDetails']['branch']);
		
		$ct = 9;
		$all = 0;
		foreach($resp as $key=>$user)
		{
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
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':P'.$ct)->applyFromArray($styleArray1);
				
				for($k="A", $i=$ct;$k<"P";$k++){
					//$objPHPExcel->setActiveSheetIndex(0)->mergeCells($k.$i.':'.++$k.$i);
					$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
				}
				if($all = 0){
					if(count($user) != 1 ){ 
						$name = "All";
						$all = 1;
				}else{
						foreach($user as $key_user=>$user1)
							$name = $user1['firstname']." ".$user1['lastname'];
							 
					}
				}else{
					$name = "All";
				}
				$yearr = $_POST['UserPersonalDetails']['years']	;		
				$year = substr($_POST['UserPersonalDetails']['years'],2,2);
				$next_year = $year+1;
				$next_yearr = $yearr+1;


				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':P'.$ct)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF'
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':P'.$ct)->applyFromArray($styleArray2); 




				$objPHPExcel->setActiveSheetIndex($sheetId)
				->setCellValue('A'.$ct_first, 'Resource: '.$name)
				->setCellValue('A'.$ct_second, 'Branch: '.Codelkups::getCodelkup($key))
				->setCellValue('D'.$ct_first, 'Year: '.$_POST['UserPersonalDetails']['years'])
				->setCellValue('A'.$ct, 'Resource')
				->setCellValue('B'.$ct, 'Feb - '.$year)
				->setCellValue('C'.$ct, 'Mar - '.$year)
				->setCellValue('D'.$ct, 'Apr - '.$year)
				->setCellValue('E'.$ct, 'May - '.$year)
				->setCellValue('F'.$ct, 'Iun - '.$year)
				->setCellValue('G'.$ct, 'Jul - '.$year)
				->setCellValue('H'.$ct, 'Aug - '.$year)
				->setCellValue('I'.$ct, 'Sep - '.$year)
				->setCellValue('J'.$ct, 'Oct - '.$year)
				->setCellValue('K'.$ct, 'Nov - '.$year)
				->setCellValue('L'.$ct, 'Dec - '.$year)
				->setCellValue('M'.$ct, 'Jan - '.$next_year)
				->setCellValue('N'.$ct, 'Total')
				->setCellValue('O'.$ct, 'Eligible for ')
				->setCellValue('P'.$ct, 'Balance')
				;
				$index = 0;
				$ct_0 =++$ct;
				$ct_pr = 1; 
				foreach ($user as $key_user=>$user1)
				{
					$total = 0;
					for($q = '02';$q<='12';$q++)
					{	$total += Timesheets::getVacationsDays($user1['id'],$yearr, $q); }
					$total += Timesheets::getVacationsDays($user1['id'],$next_year, 01);
					$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.$ct_0, $user1['firstname']." ".$user1['lastname'])
					->setCellValue('B'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,02))
					->setCellValue('C'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,03))
					->setCellValue('D'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,04))
					->setCellValue('E'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,05))
					->setCellValue('F'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,06))
					->setCellValue('G'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,07))
					->setCellValue('H'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,08))
					->setCellValue('I'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,09))
					->setCellValue('J'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,10))
					->setCellValue('K'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,11))
					->setCellValue('L'.$ct_0, Timesheets::getVacationsDays($user1['id'],$yearr,12))
					->setCellValue('M'.$ct_0, Timesheets::getVacationsDays($user1['id'],$next_yearr,01))
					->setCellValue('N'.$ct_0, $total )
					->setCellValue('O'.$ct_0, UserPersonalDetails::getAnnualLeaves($user1['id']))
					->setCellValue('P'.$ct_0, (UserPersonalDetails::getAnnualLeaves($user1['id'])-($total)));
					for($k="A",$i=$ct_0;$k<"Q";$k++)
					{
						$objPHPExcel->getActiveSheet()->getStyle($k.$i.':'.$k.$i)->applyFromArray($styleLeft);
					}
					
					$ct_0++;
					$ct_pr++;
				}
			$ct = $ct + $ct_pr + 10;
			
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Vacantion Reports');
		
		//$objWorkSheet = $objPHPExcel->createSheet(1);
		//$sheetId = 1;
		
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Vacation_Reports.xls"');
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
	
	
	
}