<?php

class TimeSheetSummaryController extends Controller
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
		//print_r($searchArray);exit;
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
					'/reports/timesheetSummary/index' => array(
						'label'=>Yii::t('translations', 'Timesheet Summary'),
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
	//print_r($model->attributes);exit;
		//if (isset($model->customer_id))
		//	$model->id_customer = Customers::getIdByName($model->customer_id); 
		
		$i = 0;
		$timesheetSummary = array();
		$message = ""; $internal= true; $onlyinternal =false;
			
		if(count(array_filter($searchArray)) > 0) {
			

				$select1="select 
				'177' as customer_id,
				 'SNS INTERNAL' as customer_name , 
				 'Support' as project_name,
 				CONCAT_WS(' ', firstname, lastname) AS username , 
 				'275' as id_project,
 				 ut.id_user ,
 				  dt.name as description,
 				   ut.comment ,
 				    ut.date  ,
 				    ut.amount ,
 				    dt.billable, CASE ut.status when '1' then 'Yes' else 'No' end as approved
  						from  users u ,user_time ut , default_tasks dt";
  				
  				$select4="select '177' as customer_id, c.name as customer_name , cd.codelkup as project_name, CONCAT_WS(' ', firstname, lastname) AS username , m.id_maintenance as id_project, 
				 ut.id_user , dtn.service as description, ut.comment , ut.date , ut.amount , 'Yes' as billable, CASE ut.status when '1' then 'Yes' else 'No' end as approved from users u ,user_time ut , 
				 maintenance_services dt, support_services dtn, maintenance m ,customers c, codelkups cd ";

    			$where1="where ut.id_task=dt.id 
    			and  ut.id_user = u.id and dt.id_parent='27' and ut.amount>0 and ut.default=1";

    			$where4=" where cd.id= m.support_service and c.id=m.customer and m.id_maintenance= dt.id_contract and  dtn.id= dt.id_service and ut.id_task=dt.id and ut.id_user = u.id and  ut.amount>0 and ut.default=2 ";
    			$groupBy1 =" ";
    				$order1 = "ORDER BY date ASC";
				
    			$select3="SELECT '177' as customer_id , 'SNS INTERNAL' as customer_name,'SNS INTERNAL' as project_name ,
			 CONCAT_WS(' ', firstname, lastname) AS username, '171' as id_project, ut.id_user ,  dt.name as description, ut.comment as comment , ut.date , ut.amount , dt.billable , CASE ut.status when '1' then 'Yes' else 'No' end as approved FROM user_time ut , default_tasks dt , users u 
";					
					$select5="
 select ca.id_customer as customer_id, 'CA' as customer_name , 'CA' as project_name, CONCAT_WS(' ', firstname, lastname) AS username , '275' as id_project, ut.id_user ,
 dt.name as description, ut.comment , ut.date , ut.amount , dt.billable, CASE ut.status when '1' then 'Yes' else 'No' end as approved

from user_time ut LEFT JOIN default_tasks dt ON ( ut.id_task=dt.id) LEFT JOIN users u   ON (ut.id_user = u.id) 
LEFT JOIN ca_customer ca ON ( ca.id_user_time=ut.id)  where   ut.id_task=dt.id and ut.id_user = u.id and dt.id_parent in ('526','828') and ut.amount>0 and ut.default=1 ";
				$gourpby5=" group by ut.id";
				$where5=" AND 1=1 ";
				$where3="where dt.id_parent<>'27' and dt.id_parent<>'526' and dt.id_parent<>'828' and ut.default='1' and ut.amount>0 and ut.id_task=dt.id and ut.id_user=u.id";
    		

    		$select6 = "select
			 '177' as customer_id, 
			 'SNS INTERNAL' as customer_name, 
			 p.name as project_name, 
			 CONCAT_WS(' ', firstname, lastname) AS username,
			  pt.id_internal ,
			  ut.id_user , 
			  pt.description as description ,
			  ut.comment  ,
			   ut.date , 
			SUM(ut.amount) AS amount ,pt.billable , CASE ut.status when '1' then 'Yes' else 'No' end as approved
			 from user_time ut LEFT JOIN internal_tasks pt ON ( ut.id_task = pt.id) 
			 LEFT JOIN  internal p ON ( p.id = pt.id_internal)
			 LEFT JOIN customers c ON c.id = 177
			 LEFT JOIN users u ON u.id = ut.id_user";
			$where6 = "WHERE ut.default=3";
			$groupBy6 = "GROUP BY pt.id_internal, p.name, ut.id_user, pt.id, ut.comment, ut.date having SUM(ut.amount)>0  ";


			$select = "select
			 p.customer_id, 
			 c.name as customer_name, 
			 p.name as project_name, 
			 CONCAT_WS(' ', firstname, lastname) AS username,
			  pp.id_project ,
			  ut.id_user , 
			  pt.id as description ,
			  ut.comment  ,
			   ut.date , 
			SUM(ut.amount) AS amount ,pt.billable , CASE ut.status when '1' then 'Yes' else 'No' end as approved
			 from user_time ut";
			$select .= " LEFT JOIN projects_tasks pt ON ( ut.id_task = pt.id) 
			 LEFT JOIN  projects_phases pp ON ( pt.id_project_phase = pp.id) 
			 LEFT JOIN  projects p ON ( p.id = pp.id_project)
			 LEFT JOIN  eas e ON (p.id=e.id_project)
			 LEFT JOIN customers c ON c.id = p.customer_id
			 LEFT JOIN users u ON u.id = ut.id_user";

			$where = "WHERE ut.default=0";
			$groupBy = "GROUP BY p.customer_id, pp.id_project, p.name, ut.id_user, pt.id, ut.comment, ut.date having SUM(ut.amount)>0  ";
			$order = "ORDER BY ut.date ASC";

			if (!empty($model->id_phase))
			{
				$where .= " AND pp.id=".$model->id_phase."";
			}

			if (!empty($model->tm))
			{
				$where .= " AND e.TM=1";
			}
			if (!empty($model->customer_id))
			{
				$customer_id = Customers::getIdByName($model->customer_id);
				if($customer_id != 177){
					$internal= false;
				}
				$where .= " AND p.customer_id = '{$customer_id}'";
				$where4 .= " AND m.customer = '{$customer_id}' ";
				$where5.= " AND ca.id_customer = '{$customer_id}' ";
			}
			if (!empty($model->id_project))
			{
				if(substr($model->id_project, -1) == 'i') {
					$model->id_project= substr($model->id_project, 0, -1);
					$onlyinternal= true;
				}
				$project_id = (int)$model->id_project;
				$where6 .= " AND p.id ='{$project_id}'";
				$where .= " AND p.id ='{$project_id}'";
			}
			if (!empty($model->id_user) && isset($model->id_user)  && ($model->id_user!=0))
			{
				$user_id = (int)$model->id_user;
				$where .= " AND ut.id_user ='{$user_id}'";
				$where6 .= " AND ut.id_user ='{$user_id}'";
			}
			
			if (!empty($model->from))
			{
				$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
				$where .= " AND ut.date >= '{$from}'";    
				$where6 .= " AND ut.date >= '{$from}'";    
			}
			if (!empty($model->to))
			{
				$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
				$where .= " AND ut.date <= '{$to}'";
				$where6 .= " AND ut.date <= '{$to}'";
			}

			 if (!empty($model->customer_id) && $model->customer_id != '177' && $model->customer_id !='SNS INTERNAL')
			{
					if ( $model->id_project!=275)
					{
							$where1.= " AND  0=1 ";
							$where3.= " AND  0=1 ";
					 }
					
			}else { 

				if(!empty($model->customer_id) && ($model->customer_id!=177 &&  $model->customer_id !='SNS INTERNAL')) {
					$internal= false;
				}
				$where1 .= " AND 1=1 "; $where3 .= " AND 1=1 "; $where4.= " AND 1=1 "; 
			}

			if (!empty($model->id_user) && isset($model->id_user)  && ($model->id_user!=0))
			{
				$user_id = (int)$model->id_user;
				$where1 .= " AND ut.id_user ='{$user_id}'";
				$where4.= " AND ut.id_user ='{$user_id}'";
				$where5.= " AND ut.id_user ='{$user_id}'";
				$where6 .= " AND ut.id_user ='{$user_id}'";
			}
			
			if (!empty($model->from))
			{
				$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
				$where1 .= " AND ut.date >= '{$from}'";   
				$where4.= " AND ut.date >= '{$from}'"; 
				$where5.= " AND ut.date >= '{$from}'"; 
				$where6.= " AND ut.date >= '{$from}'"; 
			}
			if (!empty($model->to))
			{
				$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
				$where1 .= " AND ut.date <= '{$to}'";
				$where4 .= " AND ut.date <= '{$to}'";
				$where5 .= " AND ut.date <= '{$to}'";
				$where6 .= " AND ut.date <= '{$to}'";
			}

			
			if (!empty($model->id_user) && isset($model->id_user)  && ($model->id_user!=0))
			{
				$user_id = (int)$model->id_user;
				$where3 .= " AND ut.id_user ='{$user_id}'";
			}
			if (!empty($model->from))
			{
				$from = DateTime::createFromFormat('d/m/Y', $model->from)->format('Y-m-d 00:00:00');
				$where3 .= " AND ut.date >= '{$from}'";    
			}
			if (!empty($model->to))
			{
				$to = DateTime::createFromFormat('d/m/Y', $model->to)->format('Y-m-d 00:00:00');
				$where3 .= " AND ut.date <= '{$to}'";
			}
			if (!empty($model->unit))
			{
				if ($model->unit == 'Tech')
				{

					$where1 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') )";
					$where4 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') )";
					$where .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup  in ('Tech - CS','Tech - PS') ) ";
					$where3 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') ) ";
					$where5 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') ) ";
					$where6 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') ) ";
				
				}
				else
				{
					$where1 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup like '%".$model->unit."%') ";
					$where4 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup like '%".$model->unit."%') ";
					$where .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup like '%".$model->unit."%') ";
					$where3 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup like '%".$model->unit."%') ";
					$where5 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup like '%".$model->unit."%') ";
					$where6 .= " AND ut.id_user in (select upd.id_user from user_personal_details upd, codelkups c where upd.unit=c.id and c.codelkup in ('Tech - CS','Tech - PS') ) ";
				
				}
			}

if (!empty($model->tm))
			{
				$timesheetSummary = Yii::app()->db->createCommand($select." ".$where." ".$groupBy."  ORDER BY DATE desc" )->queryAll();
				//echo 'tm'.$select." ".$where." ".$groupBy."  ORDER BY DATE desc";
				}
				else if ($onlyinternal){
				$timesheetSummary = Yii::app()->db->createCommand($select6." ".$where6." ".$groupBy6."  ORDER BY DATE desc" )->queryAll();
				}
				else if ($internal){
					//echo $select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1."  UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select3." ".$where3." ORDER BY DATE desc";
//	echo 'NOTtm'.$select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1."  UNION ".$select6." ".$where6." ".$groupBy6."  UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select5." ".$where5." ".$gourpby5." UNION ".$select3." ".$where3." ORDER BY DATE desc";
//	exit;
	$timesheetSummary = Yii::app()->db->createCommand($select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1."  UNION ".$select6." ".$where6." ".$groupBy6."  UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select5." ".$where5." ".$gourpby5." UNION ".$select3." ".$where3." ORDER BY DATE desc" )->queryAll();
		}else{
					//echo $select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1."  UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select3." ".$where3." ORDER BY DATE desc";
//	echo 'NOTtm'.$select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1." UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select5." ".$where5." ".$gourpby5." UNION ".$select3." ".$where3." ORDER BY DATE desc";
//	exit;
	$timesheetSummary = Yii::app()->db->createCommand($select." ".$where." ".$groupBy."  UNION ".$select1." ".$where1." ".$groupBy1." UNION ".$select4." ".$where4." ".$groupBy1." UNION ".$select5." ".$where5." ".$gourpby5." UNION ".$select3." ".$where3." ORDER BY DATE desc" )->queryAll();
		}

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
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
		        $styleArray2 = array(
		            'font' => array(
		                'color' => array('rgb' => 'FFFFFF'
		                )
		            ),
		            'borders' => array(
		                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
		                )
		            ));

		        $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleArray2); 
		        
		$objPHPExcel->setActiveSheetIndex($sheetId)
					->setCellValue('A'.'4', 'Customer Name')
					->setCellValue('B'.'4', 'Project Name')
					->setCellValue('C'.'4', 'Resource Name')
					->setCellValue('D'.'4', 'Phase')
					->setCellValue('E'.'4', 'Task')
					->setCellValue('F'.'4', 'Description')
					->setCellValue('G'.'4', 'Date')
					->setCellValue('H'.'4', 'Hours')
					->setCellValue('I'.'4', 'Billable')
					->setCellValue('J'.'4', 'Approved');
		$ct = 5;
		foreach($resp as $key=>$tim){
				$phase=''; $task="";
				$customer_id = $tim['customer_id'];
				if($tim['project_name']=='CA')
				{
					if ($tim['customer_id']!=0) { $customername=Customers::getNameById($tim['customer_id']);}
					else{ $customername=$tim['description'];}
				}else{ $customername=$tim['customer_name']; }				
				$pname= $tim['project_name'];
				$user = $tim['username']; 
				if ($tim['project_name']=='CA'){ $phase = 'CA'; }
				else if (($tim['customer_id']=='177' )) { $phase = $tim['description']; }
				else{ $phase =ProjectsTasks::getPhaseDescByid($tim['description']);}
				if ($tim['project_name']=='CA'){ $task = 'CA'; }
				else if (($tim['customer_id']=='177' )) { $task = $tim['description']; }
				else{ $task=ProjectsTasks::getTaskDescByid($tim['description']);}
				$comment = $tim['comment'];
				$date = $tim['date'];
				$amount = Utils::formatNumber($tim['amount']);
				$billable = $tim['billable'];
				$approved = $tim['approved'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':I'.$ct)->applyFromArray($styleArray1);
				
						$objPHPExcel->setActiveSheetIndex($sheetId)
						->setCellValue('A'.$ct, $customername)
						->setCellValue('B'.$ct, $pname)
						->setCellValue('C'.$ct, $user)
						->setCellValue('D'.$ct, $phase)
						->setCellValue('E'.$ct, $task)
						->setCellValue('F'.$ct, $comment)
						->setCellValue('G'.$ct, Utils::formatDate($date))
						->setCellValue('H'.$ct, $amount)
						->setCellValue('I'.$ct, $billable)
						->setCellValue('J'.$ct, $approved);
						
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
			header('Content-Disposition: attachment;filename="TimeSheet_Summary_Report.xls"');
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