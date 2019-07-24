<?php
class Controller extends CController
{
	public $layout='//layouts/column1';
	public $menu=array();
	public $breadcrumbs=array();
	public $action_menu = array();
	public $jsConfig = null;
	public function init()
	{
		$this->jsConfig = new stdClass();
		$this->jsConfig->urls = array('baseUrl' => Yii::app()->getBaseUrl(true), 'relativeUrl' => Yii::app()->getBaseUrl(false));
		$this->jsConfig->current = array('controller' => Yii::app()->controller->id, 'url' => Yii::app()->request->url);
		if (isset(Yii::app()->session['menu']))
		{
			$this->action_menu = Yii::app()->session['menu'];
		}
		parent::init();
	}
	protected function generatePdf($table, $id, $route = null, $position = null, $profit = null, $ids_invoices = null)
	{
		switch ($table) 
		{
			case 'eas' : 
				$model = Eas::model()->findByPk((int)$id);
				if (isset($model)) 
				{
					$html = $this->renderPartial('application.views.eas._export_pdf', array('model'=>$model), true);	
					$pdf = Yii::app()->ePdf->mpdf();
					$pdf->WriteHTML($html, 0, true, false);  
					$pdf->SetHTMLFooter($this->renderPartial('application.views.eas._footer_pdf', array(), true));
					$pdf->Output(Eas::getDirPath($model->id_customer, $model->id).'EA_'.$model->ea_number.'.pdf', 'F');	
					return true;
				}
				break;
			case 'expensesAll' : 
				$pdf = Yii::app()->ePdf->mpdf(
							'',    // mode - default ''
							'A4-L',    // format - A4, for example, default ''
							0,     // font size - default 0
							'',    // default font family
							15,    // margin_left
							15,    // margin right
							16,     // margin top
							16,    // margin bottom
							9,     // margin header
							9,     // margin footer
							'L'
						);


				foreach ($id as $key =>$exp) {
					$model = Expenses::model()->findByPk((int)$exp);
					 if (isset($model)) 
					{
						$html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model), true);						
						$pdf->WriteHTML($html, 0, true, false);  
						$pdf->SetHTMLFooter($this->renderPartial('application.views.expenses._footer_pdf', array(), true));	
						if($key != (sizeof($id)-1))						
						{
							$pdf->AddPage();
						}
					}					
				}
				$pdf->Output('./uploads/expenses/expenses.pdf', 'F');
				return true;
				break;
			case 'expenses' : 
				 $model = Expenses::model()->findByPk((int)$id);
				 if (isset($model)) 
				{
					$html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model), true);	
					$pdf = Yii::app()->ePdf->mpdf(
						'',    // mode - default ''
						'A4-L',    // format - A4, for example, default ''
						0,     // font size - default 0
						'',    // default font family
						15,    // margin_left
						15,    // margin right
						16,     // margin top
						16,    // margin bottom
						9,     // margin header
						9,     // margin footer
						'L'
					);
					$pdf->WriteHTML($html, 0, true, false);  
					$pdf->SetHTMLFooter($this->renderPartial('application.views.expenses._footer_pdf', array(), true));
					$pdf->Output('./uploads/customers/'.$model->customer_id.'/expenses/Expenses'.$model->no.'.pdf', 'F');	
					return true;
				}
				break;
			case 'transfers' : 		
				$expenses_ids = array();
				$html = '';
				$pdf = Yii::app()->ePdf->mpdf();
				$template= $route;
				$transferid = $position;
				$invoices= $ids_invoices;				
				Invoices::postTransfer($transferid,$ids_invoices);				
				$total= array_sum(array_column($id, 'usd_amount')); 
				$totalpartner=array_sum(array_column($id, 'gross_amount')); 
				if ($template == '1')
				{
					$totalusd=$total;
					$getrate= (1/CurrencyRate::getRate(169));
					$total=$total*$getrate;
					$words= Utils::convert_number_to_words(round($total, 2), 'AUD');				
					$totalFormat= Utils::formatNumber($total);
					$html = $this->renderPartial('application.views.invoices._transfer_apj_template1', array('Transfernb'=>intval($transferid),'total'=>$total,'word'=>$words), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_apj_expenses', array('inv'=>$id,'total'=>$totalusd,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);			      	
			      	$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSAPJ").'TRANSFER_'.$transferid.'.pdf', 'F');					
				} else if  ($template == '2')
				{				
					$words= Utils::convert_number_to_words(round($total, 2));				
					$totalFormat= Utils::formatNumber($total);
					$html = $this->renderPartial('application.views.invoices._transfer_apj_template2', array('Transfernb'=>intval($transferid),'total'=>$total,'word'=>$words), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_apj_expenses', array('inv'=>$id,'total'=>$total,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);    	
			       $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSAPJ").'TRANSFER_'.$transferid.'.pdf', 'F');				
				} else if  ($template == '3')
				{
					$html = $this->renderPartial('application.views.invoices._transfer_apj_template3', array('Transfernb'=>intval($transferid),'total'=>$total), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_apj_expenses', array('inv'=>$id,'total'=>$total,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);			      	
			        $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSAPJ").'TRANSFER_'.$transferid.'.pdf', 'F');				
				} else if  ($template == '4')
				{
					$totalusd=$total;
					$getrate= (1/CurrencyRate::getRate(8));
					$total=$total*$getrate;					
					$html = $this->renderPartial('application.views.invoices._transfer_apj_template4', array('Transfernb'=>intval($transferid),'total'=>$total), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_apj_expenses', array('inv'=>$id,'total'=>$totalusd,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);
			        $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSAPJ").'TRANSFER_'.$transferid.'.pdf', 'F');				
				}  else if  ($template == '5')
				{					
					$words= Utils::convert_number_to_words(round($total, 2));				
					$totalFormat= Utils::formatNumber($total);
					$html = $this->renderPartial('application.views.invoices._transfer_snsi_template5', array('Transfernb'=>intval($transferid),'total'=>$total,'word'=>$words), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_snsi_expenses', array('inv'=>$id,'total'=>$total,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);
			       $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSI").'TRANSFER_'.$transferid.'.pdf', 'F');					
				} else if  ($template == '6')
				{
					$totalusd=$total;
					$getrate= (1/CurrencyRate::getRate(8));
					$total=$total*$getrate;
					$words= Utils::convert_number_to_words(round($total, 2), 'Euros');				
					$totalFormat= Utils::formatNumber($total);
					$html = $this->renderPartial('application.views.invoices._transfer_snsi_template6', array('Transfernb'=>intval($transferid),'total'=>$total,'word'=>$words), true);
					$pdf->AddPage();
			       	$pdf->writeHTML($html, 0, true, true);			       	
			       	$pdf->AddPage();
			       	$html2 = $this->renderPartial('application.views.invoices._transfer_snsi_expenses', array('inv'=>$id,'total'=>$totalusd,'totalpartner'=>$totalpartner), true);
					$pdf->writeHTML($html2, 0, true, true);
			       $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathTransfer("SNSI").'TRANSFER_'.$transferid.'.pdf', 'F');				
				} 
				return true;
				break;

			case 'receivables' : 
				$expenses_ids = array();
				$html = '';
				$pdf = Yii::app()->ePdf->mpdf();
				foreach ($id as $model) 
				{
					$pdff = Yii::app()->ePdf->mpdf(); $pdff_aust = Yii::app()->ePdf->mpdf();	$pdff_snsi = Yii::app()->ePdf->mpdf(); $pdff_snsapj = Yii::app()->ePdf->mpdf();
					$pdff_apj = Yii::app()->ePdf->mpdf(); $invoices = array(); 	$exp = array();
					$models = Yii::app()->db->createCommand("SELECT id,id_ea,final_invoice_number FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND id IN $ids_invoices ")->queryAll();
					$expenses_ids = Yii::app()->db->createCommand("SELECT distinct id_expenses FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND id IN $ids_invoices ")->queryAll();
					$travel = Yii::app()->db->createCommand("SELECT  id FROM travel WHERE inv_number IN $ids_invoices ")->queryAll();
					$final_number = $model['final_invoice_number'];
					if ($model['partner'] == Maintenance::PARTNER_SNSI)
					{
						$partner_inv = $model['partner_inv'];					
					}
					if ($model['partner'] == Maintenance::PARTNER_SNSAPJ)
					{
						$partner_inv =  $model['snsapj_partner_inv'];
					}
					if ($model['partner'] == Maintenance::PARTNER_APJ)
					{
						$partner_inv = $model['snsapj_partner_inv'];
					}	
					if ($model['partner'] == Maintenance::PARTNER_AUST)
					{
						$partner_inv = $model['partner_inv'];
					}	
					if (Codelkups::getCodelkup($model['partner']) == 'SPAN' && $model['old'] == "Yes")
					{ 
						$partner_inv = $model['old_sns_inv'];
					}
					foreach ($models as $inv)
					{
						$invo = Invoices::model()->findByPk((int)$inv['id']);
						array_push($invoices, $invo);
						$one_model = $invo;						
						if($inv['final_invoice_number'] == null)
						{
							$ea_id = $inv['id_ea'];
							array_push($expenses_ids , $inv['id_expenses']);
						}
					}
					$ids_new_inv = false;//Travel::getTravel($model['id_project'], $model['id_customer'],$final_number);
					foreach($expenses_ids as $expenses_id1)
					{		
						$expenses = Expenses::model()->findByPk((int)$expenses_id1['id_expenses']);
						array_push($exp, $expenses);
					}
					
					if (Codelkups::getCodelkup($one_model->partner) == 'SNS'){
						$html = $this->renderPartial('application.views.invoices._export_n_a_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);			        	
			        	$pdff->writeHTML($html, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
						$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
					} else if(Codelkups::getCodelkup($one_model->partner) == 'SNSI'){
							$html = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$snsi = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_snsi->writeHTML($snsi, 0, true, true);
							$pdff_snsi->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_snsi->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,true,false).'INVOICE_'.str_replace('/','_',$one_model->partner_inv).'.pdf', 'F');
							$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$snsi .= $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);				        	
				        	$pdff->writeHTML($snsi, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'SNS AUST'){
							$html = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$aust = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_aust->writeHTML($aust, 0, true, true);
							$pdff_aust->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_aust->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,false,false, true).'INVOICE_'.str_replace('/','_',$one_model->partner_inv).'.pdf', 'F');
							$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_aust_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$aust .= $this->renderPartial('application.views.invoices._export_aust_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$pdf->AddPage();		
			        		$pdf->writeHTML($html, 0, true, true);			        	
				        	$pdff->writeHTML($aust, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'SNS APJ'){
							$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$snsapj = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_snsapj->writeHTML($snsapj, 0, true, true);
							$pdff_snsapj->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_snsapj->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,true).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');
							$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$snsapj .= $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true); $pdff->writeHTML($snsapj, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'APJ'){
							$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$apj = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_apj->writeHTML($apj, 0, true, true);
							$pdff_apj->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_apj->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,true).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');
							$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);
				        	$pdff->writeHTML($apj, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');	
			        }else if((Codelkups::getCodelkup($one_model->partner) == 'SPAN') || (Codelkups::getCodelkup($one_model->partner) == 'LOG CUBES')){ 		
			        		if((Codelkups::getCodelkup($one_model->partner) == 'SPAN'))
			        		{					
								$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							}else{
								$html = $this->renderPartial('application.views.invoices._export_cubes_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							}

							$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);	 $pdff->writeHTML($html, 0, true, true);
		        			$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));	

							$ea_id =$one_model->id_ea;
					        if ($ea_id != null) {
						        $model_ea = Eas::model()->findByPk((int)$ea_id);
						       	if ($model_ea->getFile(false,true) != null)
						       	{
							        $pdf->AddPage();
							        $ext = pathinfo($model_ea->getFile(false,true), PATHINFO_EXTENSION);
							        switch(strtolower($ext))
									{
										case 'jpg':
										case 'jpeg':
										case 'png':
										case 'gif':
										case 'bmp':
										case 'wmf':
											$pdf->Image($model_ea->getFile(false,true), 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
											break;
										default:
											break;
									}
						       	}
					        }	
					        foreach($expenses_ids as $expenses_id)
					        {
					        	if ($expenses_id['id_expenses'] != null) 
					        	{
							        $model_expenses = Expenses::model()->findByPk((int)$expenses_id['id_expenses']);
							    	if(isset($model_expenses))
							    	{
							    		Expenses::changeStatusInvoiced($model_expenses->id);
							    		$pdfexpenses = Yii::app()->ePdf->mpdf(
											'',    // mode - default ''
											'A4-L',    // format - A4, for example, default ''
											0,     // font size - default 0
											'',    // default font family
											15,    // margin_left
											15,    // margin right
											16,     // margin top
											16,    // margin bottom
											9,     // margin header
											9,     // margin footer
											'L'
										);
							        	$html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);	
								        $pdf->AddPage();  $pdf->writeHTML($html, 0, true, true);						        
								        $html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);						        
								        $pdfexpenses->writeHTML($html, 0, true, false);
								        $pdfexpenses->SetHTMLFooter($this->renderPartial('application.views.expenses._footer_pdf', array(), true));
								        $pdfexpenses->Output(Expenses::getDirPathExp($one_model->id_customer, $one_model->id).'Expenses'.$model_expenses['no'].'.pdf', 'F');
								        if ($model_expenses->expensesUploads != null)
							    		{
							    			foreach ($model_expenses->expensesUploads as $upload)
							    			{
							    				$file = $upload->getFileUpload(false);
								    			$pdf->AddPage();
										        $ext = pathinfo($file, PATHINFO_EXTENSION);
									        	switch(strtolower($ext))
												{
													case 'jpg':
													case 'jpeg':
													case 'png':
													case 'gif':
													case 'bmp':
													case 'wmf':
														$pdf->Image($file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
														break;
													default:
														break;
												}
							    			}
							    		}
							    	}
					        	}
								} if($one_model->old=='Yes'){
				        				$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->invoice_number).'.pdf', 'F');	
				        			}else{
									$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
									}
							
					} 
				}
				$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
				$pdf->Output(Invoices::getDirPathMoreInv().'INVOICE_'.date('Y-m-d').'.pdf', 'F');	
				return true;
				break;


			case 'invoices' : 
				$expenses_ids = array();
				$html = '';
				$pdf = Yii::app()->ePdf->mpdf();
				foreach ($id as $model) 
				{
					$pdff = Yii::app()->ePdf->mpdf(); $pdff_aust = Yii::app()->ePdf->mpdf();	$pdff_snsi = Yii::app()->ePdf->mpdf(); $pdff_snsapj = Yii::app()->ePdf->mpdf();
					$pdff_apj = Yii::app()->ePdf->mpdf(); $invoices = array(); 	$exp = array();
					$models = Yii::app()->db->createCommand("SELECT id,id_ea,final_invoice_number FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND status = 'To Print' AND id IN $ids_invoices ")->queryAll();
					$expenses_ids = Yii::app()->db->createCommand("SELECT distinct id_expenses FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND id IN $ids_invoices ")->queryAll();
					$travel = Yii::app()->db->createCommand("SELECT  id FROM travel WHERE inv_number IN $ids_invoices ")->queryAll();
					$final_number = Utils::createInvNumber();
					if ($model['partner'] == Maintenance::PARTNER_SNSI)
					{
						$partner_inv = Utils::createInvNumberPartner();					
					}
					if ($model['partner'] == Maintenance::PARTNER_SNSAPJ)
					{
						$partner_inv = Utils::createInvNumberPartnerSNSAPJ();
					}
					if ($model['partner'] == Maintenance::PARTNER_APJ)
					{
						$partner_inv = Utils::createInvNumberPartnerAPJ();
					}	
					if ($model['partner'] == Maintenance::PARTNER_AUST)
					{
						$partner_inv = Utils::createInvNumberPartnerAust();
					}	
					if ($model['old'] == "Yes")
					{ 
						$partner_inv = Utils::createOldInvNumber();
					}
					foreach ($models as $inv)
					{
						$invo = Invoices::model()->findByPk((int)$inv['id']);
						array_push($invoices, $invo);
						Invoices::changeStatusInv($invo,$final_number,$partner_inv);
						$one_model = $invo;						
						if($inv['final_invoice_number'] == null)
						{
							$ea_id = $inv['id_ea'];
							array_push($expenses_ids , $inv['id_expenses']);
						}
					}
					$ids_new_inv = false;//Travel::getTravel($model['id_project'], $model['id_customer'],$final_number);
					foreach($expenses_ids as $expenses_id1)
					{		
						$expenses = Expenses::model()->findByPk((int)$expenses_id1['id_expenses']);
						array_push($exp, $expenses);
						Expenses::changeStatusInvoiced((int)$expenses_id1['id_expenses']);
					}

					foreach($travel as $expenses_id1)
					{		
						Travel::changeStatusInvoicedInfo((int)$expenses_id1['id'], $final_number);
					}
					
					if (Codelkups::getCodelkup($one_model->partner) == 'SNS'){
						$html = $this->renderPartial('application.views.invoices._export_n_a_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);			        	
			        	$pdff->writeHTML($html, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
						$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
					} else if(Codelkups::getCodelkup($one_model->partner) == 'SNSI'){
							$html = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$snsi = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_snsi->writeHTML($snsi, 0, true, true);
							$pdff_snsi->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_snsi->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,true,false).'INVOICE_'.str_replace('/','_',$one_model->partner_inv).'.pdf', 'F');
							$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        		$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$snsi .= $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);	
				        	$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);			        	
				        	$pdff->writeHTML($snsi, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'SNS AUST'){
							$html = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$aust = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_aust->writeHTML($aust, 0, true, true);
							$pdff_aust->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_aust->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,false,false, true).'INVOICE_'.str_replace('/','_',$one_model->partner_inv).'.pdf', 'F');
							$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        	//	//$html = $this->renderPartial('application.views.invoices._export_aust_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		//$aust .= $this->renderPartial('application.views.invoices._export_aust_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		//$pdf->AddPage();		
			        		//$pdf->writeHTML($html, 0, true, true);
			        		//$pdf->AddPage();		
			        		//$pdf->writeHTML($html, 0, true, true);			        	
				        	//$pdff->writeHTML($aust, 0, true, true);
				        	//$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							//$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'SNS APJ'){
							$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$snsapj = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_snsapj->writeHTML($snsapj, 0, true, true);
							$pdff_snsapj->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_snsapj->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,true).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');
							$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$snsapj .= $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true); 
			        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true); $pdff->writeHTML($snsapj, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
			        }else if(Codelkups::getCodelkup($one_model->partner) == 'APJ'){
							$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$apj = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdff_apj->writeHTML($apj, 0, true, true);
							$pdff_apj->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_apj->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,true).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');
							$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);
				        	$pdff->writeHTML($apj, 0, true, true);
				        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');	
			        }else if((Codelkups::getCodelkup($one_model->partner) == 'SPAN') || (Codelkups::getCodelkup($one_model->partner) == 'LOG CUBES')){ 		
			        		if((Codelkups::getCodelkup($one_model->partner) == 'SPAN'))
			        		{					
								$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
								$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
							}else{
								$html = $this->renderPartial('application.views.invoices._export_cubes_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							}

							$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);	 $pdff->writeHTML($html, 0, true, true);
		        			$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));	

							foreach($expenses_ids as $expenses_id)
					        {
					        	if ($expenses_id['id_expenses'] != null) 
					        	{
							        $model_expenses = Expenses::model()->findByPk((int)$expenses_id['id_expenses']);
							    	if(isset($model_expenses))
							    	{
							    		Expenses::changeStatusInvoiced($model_expenses->id);
							    		$pdfexpenses = Yii::app()->ePdf->mpdf(
											'',    // mode - default ''
											'A4-L',    // format - A4, for example, default ''
											0,     // font size - default 0
											'',    // default font family
											15,    // margin_left
											15,    // margin right
											16,     // margin top
											16,    // margin bottom
											9,     // margin header
											9,     // margin footer
											'L'
										);
							        	$html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);	
								        $pdf->AddPage();  $pdf->writeHTML($html, 0, true, true);						        
								        $html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);						        
								        $pdfexpenses->writeHTML($html, 0, true, false);
								        $pdfexpenses->SetHTMLFooter($this->renderPartial('application.views.expenses._footer_pdf', array(), true));
								        $pdfexpenses->Output(Expenses::getDirPathExp($one_model->id_customer, $one_model->id).'Expenses'.$model_expenses['no'].'.pdf', 'F');
								        if ($model_expenses->expensesUploads != null)
							    		{
							    			foreach ($model_expenses->expensesUploads as $upload)
							    			{
							    				$file = $upload->getFileUpload(false);
								    			$pdf->AddPage();
										        $ext = pathinfo($file, PATHINFO_EXTENSION);
									        	switch(strtolower($ext))
												{
													case 'jpg':
													case 'jpeg':
													case 'png':
													case 'gif':
													case 'bmp':
													case 'wmf':
														$pdf->Image($file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
														break;
													default:
														break;
												}
							    			}
							    		}
							    	}
					        	}
								} if($one_model->old=='Yes'){
				        				$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->invoice_number).'.pdf', 'F');	
				        			}else{
									$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
									}
							
					} 
					$ea_id =$one_model->id_ea;
					        if ($ea_id != null) {
								$html = $this->renderPartial('application.views.invoices._export_ea_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
								$pdf->AddPage();  $pdf->writeHTML($html, 0, true, true);	
					        }	
				}
				

				$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
				$pdf->Output(Invoices::getDirPathMoreInv().'INVOICE_'.date('Y-m-d').'.pdf', 'F');	
				return true;
				break;
			case 'invoicesOne' : 				
			 	$model = Invoices::model()->findByPk((int)$id);
				$exp = array();
				$array = array();
				if (isset($model)) 
				{
					$pdf = Yii::app()->ePdf->mpdf();
					$pdff = Yii::app()->ePdf->mpdf();
					$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$model->id_customer}' AND id_project = '{$model->id_project}' AND id = '{$model->id}' ")->queryAll();
					$eas = Yii::app()->db->createCommand("SELECT id_ea,final_invoice_number FROM invoices WHERE id_customer = '{$model->id_customer}' AND id_project = '{$model->id_project}' AND id = '{$model->id}' ")->queryAll();
					foreach($expenses_ids as $expenses_id1)
					{
						$expenses = Expenses::model()->findByPk((int)$expenses_id1['id_expenses']);
						if(isset($expenses))
							array_push($exp, $expenses);
					}
					$final_number = Utils::createInvNumber();
					if ($model->partner == Maintenance::PARTNER_SNSI)
					{
						$partner_inv = Utils::createInvNumberPartner();
					}
					if ($model->partner == Maintenance::PARTNER_AUST)
					{
						$partner_inv = Utils::createInvNumberPartnerAust();
					}
					if ($model->partner == Maintenance::PARTNER_SNSAPJ)
					{
						$partner_inv = Utils::createInvNumberPartnerSNSAPJ();
					}
					if ($model->partner == Maintenance::PARTNER_APJ)
					{
						$partner_inv = Utils::createInvNumberPartnerAPJ();
							}
					if (Codelkups::getCodelkup($model->partner) == 'SPAN' && $model->old == "Yes")
					{
						$partner_inv = Utils::createOldInvNumber();
					}
					Invoices::changeStatusInv($model,$final_number,$partner_inv);	
					$ids_new_inv = Travel::getTravel($model->id_project, $model->id_customer,$final_number);
					array_push($array, $model);
					if(Codelkups::getCodelkup($model->partner) == 'SNS') {
						$html = $this->renderPartial('application.views.invoices._export_n_a_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);				
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
					}else if(Codelkups::getCodelkup($model->partner) == 'SNSI') { 		
						$html = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$snsi = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
		        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);	$pdff->writeHTML($snsi, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,true,false).'INVOICE_'.str_replace('/','_',$model->partner_inv).'.pdf', 'F');
			        }else if(Codelkups::getCodelkup($model->partner) == 'SNS AUST')	{ 		
						$html = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$aust = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);	$pdff->writeHTML($aust, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,true,false).'INVOICE_'.str_replace('/','_',$model->partner_inv).'.pdf', 'F');
			        }else if(Codelkups::getCodelkup($model->partner) == 'SNS APJ')	{
						$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$snsapj = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
		        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true); $pdff->writeHTML($snsapj, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,false,true).'INVOICE_'.str_replace('/','_',$model->snsapj_partner_inv).'.pdf', 'F');
			        }else if(Codelkups::getCodelkup($model->partner) == 'APJ')	{
						$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$apj = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true); $pdff->writeHTML($apj, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,false,true).'INVOICE_'.str_replace('/','_',$model->snsapj_partner_inv).'.pdf', 'F');
			        }else if((Codelkups::getCodelkup($model->partner) == 'SPAN')) {
							$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
							$pdf->AddPage();
		        			$pdf->writeHTML($html, 0, true, true);
					}else if((Codelkups::getCodelkup($model->partner) == 'LOG CUBES')) {
							$html = $this->renderPartial('application.views.invoices._export_cubes_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
							$pdf->AddPage();
		        			$pdf->writeHTML($html, 0, true, true);
					}
				 	foreach ($eas as $ea) {
						if($ea['final_invoice_number'] == null && $ea['id_ea'] != null){
					    	$model_ea = Eas::model()->findByPk((int)$ea['id_ea']);
					    	if(isset($model_ea))
					    	{
						    	if ($model_ea->getFile(false,true) != null)
						       	{
							        $pdf->AddPage();   $ext = pathinfo($model_ea->getFile(false,true), PATHINFO_EXTENSION);
						        	switch($ext)
									{
										case 'jpg':
										case 'jpeg':
										case 'png':
										case 'gif':
										case 'bmp':
										case 'wmf':
											$pdf->Image($model_ea->getFile(false,true), 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
											break;
										default:
											break;
									}
						       	}
					    	}
							foreach($expenses_ids as $expenses_id)
							{
					        	if($expenses_id['id_expenses'] != null)
					        	{
							        $model_expenses = Expenses::model()->findByPk((int)$expenses_id['id_expenses']);
							        if(isset($model_expenses))
							        {
							        	Expenses::changeStatusInvoiced($model_expenses->id);
							        	$pdfexpenses = Yii::app()->ePdf->mpdf(
											'',    // mode - default ''
											'A4-L',    // format - A4, for example, default ''
											0,     // font size - default 0
											'',    // default font family
											15,    // margin_left
											15,    // margin right
											16,     // margin top
											16,    // margin bottom
											9,     // margin header
											9,     // margin footer
											'L'
										);
								        $html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);	
								        $pdf->AddPage();
								        $pdf->writeHTML($html, 0, true, true);
										$html = $this->renderPartial('application.views.expenses._export_pdf', array('model'=>$model_expenses), true);	
								        $pdfexpenses->writeHTML($html, 0, true, false);
								        $pdfexpenses->SetHTMLFooter($this->renderPartial('application.views.expenses._footer_pdf', array(), true));
								        $pdfexpenses->Output(Expenses::getDirPathExp($model->id_customer, $model->id).'Expenses'.$model_expenses['no'].'.pdf', 'F');
							        if ($model_expenses->expensesUploads != null)
							    		{
							    			foreach ($model_expenses->expensesUploads as $upload)
							    			{
							    				$file = $upload->getFileUpload(false);
								    			$pdf->AddPage();
										        $ext = pathinfo($file, PATHINFO_EXTENSION);
									        	switch($ext)
												{
													case 'jpg':
													case 'jpeg':
													case 'png':
													case 'gif':
													case 'bmp':
													case 'wmf':
														$pdf->Image($file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
														break;
													default:
														break;
												}
							    			}
							    		}
							        }
					        	}
					        }
						}
				    }
				    $pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
				    if(Codelkups::getCodelkup($model->partner) == 'SPAN' && $model->old == "Yes")
				    {	
				    	$pdf->Output(Invoices::getDirPath($model->id_customer, $model->id).'INVOICE_'.str_replace('/','_',$model->invoice_number).'.pdf', 'F');	
				    }
				    else
				    {
			   	 		$pdf->Output(Invoices::getDirPath($model->id_customer, $model->id).'INVOICE_'.str_replace('/','_',$model->final_invoice_number).'.pdf', 'F');	
				    }	return true;
				}
				break;			
			case 'bankTransfer' :
			 	$model = Expenses::model()->findByPk((int)$id);
			 	if (isset($model))
			 	{
			 		$html = $this->renderPartial('application.views.expenses._bank_transfer_pdf', array('model'=>$model), true);
			 		$pdf = Yii::app()->ePdf->mpdf();
			 		$pdf->WriteHTML($html, 0, true, false);
			 		$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			 		$pdf->Output(Expenses::getDirPathBankTransfer($model->customer_id, $model->id).'BANK_TRANSFER_'.$model->no.'.pdf', 'F');			 			
			 		return true;
			 	}
			 	break;
			case 'bankTransferAll' :
				$pdf = Yii::app()->ePdf->mpdf();
				
				foreach ($id as $key =>$exp) {
					//print_r($id);exit;
					//if (isset($id['user_id'])) 
					{
						$html = $this->renderPartial('application.views.expenses._bank_transfer_all_pdf', array('user'=>$exp['user_id'], 'amt'=> $exp['amt']), true);					
						$pdf->WriteHTML($html, 0, true, false);  
						$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));	
						if($key != (sizeof($id)-1))						
						{
							$pdf->AddPage();
						}
					}					
				}
				$pdf->Output('./uploads/expenses/bankTransfer.pdf', 'F');
				return true;
				break; 	
			case 'check' :
				$model = SuppliersPrint::model()->findByPk((int)$id);
				if (isset($model))
			 	{
			 		$html = $this->renderPartial('application.views.suppliers._bank_check_pdf', array('model'=>$model), true);
			 		$pdf = Yii::app()->ePdf->mpdf(
			 				'',    // mode - default ''
			 				'A4-L'//,    // format - A4, for example, default ''
			 				//0,     // font size - default 0
			 				//'',    // default font family
			 				//15,    // margin_left
			 				//15,    // margin right
			 				//16,     // margin top
			 				//16,    // margin bottom
			 				//9,     // margin header
			 				//9,     // margin footer
			 				//'L'
			 		);
			 		$pdf->WriteHTML($html, 0, true, false);
			 		$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			 		$pdf->Output(Suppliers::getDirPathCheck($model->id_supplier, $model->id).'BANK_CHECK_'.$model->id.'.pdf', 'F');			 			
			 		return true;
			 	}
			 	break;
			case 'checkm' :
				if (isset($ids_invoices))
			 	{
				 	$checks=$ids_invoices ;
				 	$title= implode('_', $checks);
				 	$supplier='';
				 	$pdf = Yii::app()->ePdf->mpdf(
					 				'',    // mode - default ''
					 				'A4-L'//,    // format - A4, for example, default ''
					 				//0,     // font size - default 0
					 				//'',    // default font family
					 				//15,    // margin_left
					 				//15,    // margin right
					 				//16,     // margin top
					 				//16,    // margin bottom
					 				//9,     // margin header
					 				//9,     // margin footer
					 				//'L'
					 		);
				 	$i=0;
				 	foreach ($checks as $id) {
				 		$model = SuppliersPrint::model()->findByPk((int)$id);
						if (isset($model))
					 	{
					 		$supplier= $model->id_supplier;
					 		$html = $this->renderPartial('application.views.suppliers._bank_check_pdf', array('model'=>$model), true);
					 		$pdf->WriteHTML($html, 0, true, true);
					 		$i++;
					 		if($i< count($checks))
					 		{
					 			$pdf->AddPage(); 
					 		}
					 		
					 	}
				 	}
				 	$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
				 	$pdf->Output(Suppliers::getDirPathCheckMultip($supplier).'BANK_CHECK_'.$title.'.pdf', 'F');			 			
				 	return true;	
				}			
			 	break;				
			case 'letter' :
				$model = SuppliersPrint::model()->findByPk((int)$id);
				if (isset($model))
			 	{
			 		$html = $this->renderPartial('application.views.suppliers._bank_letter_pdf', array('model'=>$model), true);
			 		$pdf = Yii::app()->ePdf->mpdf();
			 		$pdf->WriteHTML($html, 0, true, false);
			 		$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			 		$pdf->Output(Suppliers::getDirPathLetter($model->id_supplier, $model->id).'BANK_LETTER_'.$model->id.'.pdf', 'F');			 			
			 		return true;
			 	}
			 	break;			 
			 case 'invoicesShare' :
			 	$model = Invoices::model()->findByPk((int)$id);
			 	if (isset($model))
			 	{
				 	$pdf = Yii::app()->ePdf->mpdf();
				 	$exp = array();	
				 	$array = array();
				 	$expenses_ids = Yii::app()->db->createCommand("SELECT DISTINCT id_expenses FROM invoices WHERE id_customer = '{$model->id_customer}' AND project_name = '{$model->project_name}' ")->queryAll();
					$eas = Yii::app()->db->createCommand("SELECT id_ea,final_invoice_number FROM invoices WHERE id_customer = '{$model->id_customer}' AND project_name = '{$model->project_name}' AND id = '{$model->id}' ")->queryAll();
					foreach($expenses_ids as $expenses_id1)
					{
						$expenses = Expenses::model()->findByPk((int)$expenses_id1['id_expenses']);
						if(isset($expenses))
							array_push($exp, $expenses);
					}
					array_push($array, $model);
					if(Codelkups::getCodelkup($model->partner) == 'SNS')
					{
						$html = $this->renderPartial('application.views.invoices._export_n_a_pdf', array('models'=>$array,'model'=>$model), true);				
						$pdf->AddPage();
					    $pdf->writeHTML($html, 0, true, true); 
					}else if(Codelkups::getCodelkup($model->partner) == 'SNSI')
					{
						$html = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage(); 	$pdf->writeHTML($html, 0, true, true);				    
					}
					else if(Codelkups::getCodelkup($model->partner) == 'SNS AUST')
					{
						$html = $this->renderPartial('application.views.invoices._export_aust_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		//$html = $this->renderPartial('application.views.invoices._export_aust_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		//$pdf->AddPage(); 	$pdf->writeHTML($html, 0, true, true);				    
					}else if(Codelkups::getCodelkup($model->partner) == 'SNS APJ')
					{
						$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);				    
					}else if(Codelkups::getCodelkup($model->partner) == 'APJ')
					{
						$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_apj_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);				    
					}else{ 
						$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
					}
			 		$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
					$pdf->Output(Invoices::getDirPathShare($model->id_customer, $model->id).'INVOICE_'.$model->invoice_number.'.pdf', 'F');						
			 		return true;
			 	} 
			 	break;
		 	case 'reports' : 
				if (isset($id)) 
				{
					if($position == "L")
					{
						$pdf = Yii::app()->ePdf->mpdf(
							'',    // mode - default ''
							'A4-L',    // format - A4, for example, default ''
							0,     // font size - default 0
							'',    // default font family
							15,    // margin_left
							15,    // margin right
							16,     // margin top
							16,    // margin bottom
							9,     // margin header
							9,     // margin footer
							'L'
						);
					}
					else {
						$pdf = Yii::app()->ePdf->mpdf();
					}
                    $html = $this->renderPartial($route.'pdf_report_head', array('expenses'=>$id), true);
					if (isset($id['istravelreport']) && $id['istravelreport']){
                        $html = $this->renderPartial($route.'pdf_report', array('data' => $id['resp']), true);
                        $pdf->AddPage();	$pdf->writeHTML($html, 0, true, true);
                    }else{
                        $i = 0;
                        foreach ($id as $key=>$expens)
                        {
                            if (isset($expens['projects']))
                            {
                                $projects = $expens['projects'];
                                foreach($projects as $key_project=>$project)
                                {
                                    if($i == 0)
                                    {
                                        $html .= $this->renderPartial($route.'pdf_report', array('project'=>$project,'key_project'=>$key_project), true);
                                        $i = 1;
                                    }
                                    else
                                    {
                                        $html = $this->renderPartial($route.'pdf_report', array('project'=>$project,'key_project'=>$key_project), true);
                                    }
                                    $pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
                                }
                            }
                            else
                            {
                                if ($profit != null)
                                {
                                    $html .= $this->renderPartial($route.'pdf_report', array('projects'=>$expens,'key' => $key,'profit'=>$profit), true);
                                }
                                else
                                {
                                    if($i == 0)
                                    {
                                        $html .= $this->renderPartial($route.'pdf_report', array('user'=>$expens,'key' => $key), true);
                                        $i = 1;
                                    }
                                    else
                                    {
                                        $html = $this->renderPartial($route.'pdf_report', array('user'=>$expens,'key' => $key), true);
                                    }
                                }
                                $pdf->AddPage();	$pdf->writeHTML($html, 0, true, true);
                            }
                        }
                    }

					$pdf->SetHTMLFooter($this->renderPartial($route.'_footer_pdf', array(), true));
					$pdf->Output(Utils::getDirPathReports().'REPORTS.pdf', 'F');	return true;
				}
				break;
			case 'project_status_report':
			$uat_open_checklist = array();
			$uat_golive_pending_checklist = array();
				$model = Projects::model()->findByPk($id);
                /*
                 * Author: Mike
                 * Date: 11.07.19
                 * MDs are shown in the project main screen but not displayed in the Status Report
                 */
                $project_eas = Yii::app()->db->createCommand("SELECT * FROM " . (new Eas())->tableName() . " WHERE id_project={$id}")->queryAll();

                $project_phases = Yii::app()->db->createCommand("select  m.description, pm.status , estimated_date_of_start, estimated_date_of_completion from projects_milestones pm join milestones m on pm.id_milestone = m.id
				where pm.applicable='Yes' and  pm.id_project = ".$id." order by m.milestone_number")->queryAll();
				$project_highlights = Yii::app()->db->createCommand("select description from status_report_highlights where status_report in (select id from projects_status_reports where project = ".$id.")")->queryAll();
			//	$project_health_indicators = Yii::app()->db->createCommand("select project_scope, resources, timeline, project_finance, risks_issues, overall_project_health, indicators_date
			//	from status_report_health_indicators where status_report in (select id from projects_status_reports where project = ".$id.") order by indicators_date ASC limit 25")->queryAll();
				$project_health_indicators = Yii::app()->db->createCommand("SELECT * FROM ( select project_scope, resources, timeline, project_finance, risks_issues, overall_project_health, indicators_date from status_report_health_indicators where status_report in (select id from projects_status_reports where project = ".$id.") order by indicators_date DESC limit 25) sub ORDER BY indicators_date ASC")->queryAll();
				$sizeindic= sizeof($project_health_indicators);
				if ($sizeindic<25)
				{
					$x=25-$sizeindic;
					$countArr=$sizeindic;
					$start_date = $project_health_indicators[$sizeindic-1]['indicators_date']; 
					for ($i = 0; $i < $x; $i++) {
						$date = strtotime($start_date);		$date = strtotime("+7 day", $date);						
					    $project_health_indicators[$countArr]['project_scope'] = 0;
			            $project_health_indicators[$countArr]['resources'] = 0;
			            $project_health_indicators[$countArr]['timeline'] = 0;
			            $project_health_indicators[$countArr]['project_finance'] = 0;
			            $project_health_indicators[$countArr]['risks_issues'] = 0;
			            $project_health_indicators[$countArr]['overall_project_health'] = 0;
			            $project_health_indicators[$countArr]['indicators_date'] = date('Y-m-d', $date);			           
			           	$start_date = $project_health_indicators[$countArr]['indicators_date'];
			            $countArr++;
					} 
				}
				$project_milestones = Yii::app()->db->createCommand("select milestone from status_report_milestones where status_report in (select id from projects_status_reports where project = ".$id.")")->queryAll();
				$project_risks = Yii::app()->db->createCommand("select risk,priority, responsibility, planned_actions,status from projects_risks where status <> 'Closed' and privacy='External' and id_project = ".$id)->queryAll();
				$project_risks_closed = Yii::app()->db->createCommand("select risk from projects_risks where status = 'Closed' and privacy='External' and id_project = ".$id)->queryAll();
				$financialflag=Yii::app()->db->createCommand("select invoicesFlag, incluetimesheet, includechecklist from projects_status_reports where project = ".$id." order by id DESC,date DESC limit 1")->queryRow();
				if ($financialflag['invoicesFlag'] == 1)
				{
				$project_invoices = Yii::app()->db->createCommand("select CASE 
				    when partner='201' then snsapj_partner_inv
				    when partner='78'  then partner_inv
                    when partner='79'  and old='Yes' then old_sns_inv
				    when partner='79'  and old='No' then final_invoice_number
					when partner='77'  then final_invoice_number
					END as final_invoice_number, gross_amount , default_currency,DATE_ADD(printed_date, INTERVAL 1 MONTH) as due_date, DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age from invoices join customers on customers.id = invoices.id_customer where invoices.status <> 'Paid' and DATE_ADD(printed_date, INTERVAL 1 MONTH) < NOW()  and id_project = ".$id)->queryAll();
				}	else	{			$project_invoices = null;		}
				if ($financialflag['incluetimesheet'] == 1)
				{
				$project_time = Yii::app()->db->createCommand("select p.id, p.description, (select sum(amount) from user_time where `default`=0 and amount>0 and id_task in (select id from projects_tasks where id_project_phase = p.id))/8 as time from 
projects_phases p where p.id_project=  ".$id." and  ((select sum(amount) from user_time where `default`=0 and amount>0 and id_task in (select id from projects_tasks where id_project_phase = p.id))/8)>0")->queryAll();
				}	else	{			$project_time = null;		}
				
				$project_members = Yii::app()->db->createCommand("select id_user from user_task ut join projects_tasks pt on ut.id_task = pt.id
				join projects_phases pp on pt.id_project_phase = pp.id where pp.id_project = ".$id." group by id_user")->queryAll();
				$sop_signoff_stat = Yii::app()->db->createCommand("
				select count(id) > 0 from projects_milestones where id_milestone = 4 and status = 'Closed' and applicable = 'Yes' and id_project = ".$id)->queryScalar();
				if($sop_signoff_stat != null && $sop_signoff_stat !=0  && $financialflag['includechecklist'] == 1){
					$uat_open_checklist = Yii::app()->db->createCommand("select c.descr,c.category from  checklist  c join projects_checklist pc on c.id = pc.id_checklist where c.id_phase = 6 and c.responsibility = 'Client' and pc.status = 'Open' and  pc.id_project = ".$id)->queryAll();
					if($uat_open_checklist == null){
						$uat_golive_pending_checklist = Yii::app()->db->createCommand("select c.descr,c.category from  checklist  c join projects_checklist pc on c.id = pc.id_checklist where (( c.id_phase = 6 and c.responsibility = 'Client' and pc.status = 'Pending' ) or (c.id_phase = 7 and c.responsibility = 'Client' and pc.status = 'Open')) and  pc.id_project = ".$id)->queryAll();
					} }
				$html = $this->renderPartial('application.views.projects._status_report_pdf', array('project_members' => $project_members,'model' => $model,'project_phases'=>$project_phases,'project_highlights'=>$project_highlights,'project_health_indicators'=>$project_health_indicators,'project_milestones'=>$project_milestones,'project_risks' => $project_risks,'project_risks_closed' => $project_risks_closed,'project_invoices' =>$project_invoices, 'project_time'=>$project_time
					,'uat_open_checklist' => $uat_open_checklist,'uat_golive_pending_checklist' => $uat_golive_pending_checklist,'project_eas' => isset($project_eas[0])?$project_eas:null), true);
			 	$pdf = Yii::app()->ePdf->mpdf(
			 				'',    // mode - default ''
			 				'A4-L',//,    // format - A4, for example, default ''
			 				0,     // font size - default 0
			 				'',    // default font family
			 				5,    // margin_left
			 				5,    // margin right
			 				5,     // margin top
			 				5,    // margin bottom
			 				5,     // margin header
			 				5,     // margin footer
			 				'L'
			 		);	$pdf->WriteHTML($html, 0, true, false);		 		
			 		if(!empty($uat_open_checklist) || !empty($uat_golive_pending_checklist) || !empty($project_milestones)  || !empty($project_risks_closed) || !empty($project_risks) || !empty($project_invoices)  || !empty($project_time) )
			 		{
			 			$html = $this->renderPartial('application.views.projects._status_report_pdf2', array('project_members' => $project_members,'model' => $model,'project_phases'=>$project_phases,'project_highlights'=>$project_highlights,'project_health_indicators'=>$project_health_indicators,'project_milestones'=>$project_milestones,'project_risks' => $project_risks,'project_risks_closed' => $project_risks_closed,'project_invoices' =>$project_invoices, 'project_time'=>$project_time
						,'uat_open_checklist' => $uat_open_checklist,'uat_golive_pending_checklist' => $uat_golive_pending_checklist,isset($project_eas[0])?$project_eas:null), true);
			 			$pdf->AddPage(); $pdf->writeHTML($html, 0, true, true);
			 		}
			 		$doc_model = new Documents; $doc_model->id_model = $id; $doc_model->model_table = 'projects';
			 		$doc_model->id_category = 17; 	$cust_name= Customers::GetCustByProject($id); $cust_name= str_replace("/","_",$cust_name);
			 		 $doc_model->document_title = $cust_name."_SR_".date("dMy");
			 		$doc_model->uploaded_by = 1; $doc_model->file = $cust_name."_SR_".date("dMy",strtotime("now")).'.pdf';
			 		 if($doc_model->validate()){ $doc_model->save(); }

			 		$pdf->Output(Projects::getDirPath($model->id, $doc_model->id).$cust_name.'_SR_'.date("dMy",strtotime("now")).'.pdf', 'F');			 		
			 		$notif = EmailNotifications::getNotificationByUniqueName('statusreport_new');
			 		if($notif != null){
			 		$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);						
			 		$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user =".$model->business_manager)->queryScalar();
			 		$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user =".$model->project_manager)->queryScalar();
			 		$file_attach = $doc_model->getFile(true); $email_subject = $doc_model->file;
			 		$email_body = "Hello,<br><br>Please find attached status report for <b>".$model->name."</b>";
			 		Yii::app()->mailer->ClearAddresses();	$email_body .= "<br>".$bm_email."<br>".$pm_email."<br>";
			 		foreach($emails as $email) 
					{
						if (!empty($email))
							$email_body .= "<br>".$email;
					}	
			 		Yii::app()->mailer->Subject  = $email_subject;
			 		Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($email_body)."</div>");
			 		Yii::app()->mailer->AddFile($file_attach);
			 		if(Yii::app()->mailer->Send(true)){
			 			return true;
			 		}
			 	}			 	
				break;
		} return false;
	}	
	public function getSubTab($curl = null)
	{
		$baseurl = Yii::app()->request->getBaseUrl(true);
		$baseUrl = str_replace(Yii::app()->request->getHostInfo(), "", $baseurl);
		if ($curl != null) {
			$url = str_replace($baseUrl, "", $curl);
		} else {
			$url = str_replace($baseUrl, "", Yii::app()->request->url);	
		}
		if (@isset(Yii::app()->session['menu'][$url]['subtab']))
		{
			return Yii::app()->session['menu'][$url]['subtab'];
		}
		return 0;
	}	
	public function deleteFile($filepath, $id = null, $class = null, $callback = null)
	{
		if (is_file($filepath))
		{
			if (unlink($filepath))
			{
				if ($callback == null && $class == null && $id == null)
				{
					return true;	
				}
				else 
				{
					return call_user_func_array($class.'::'.$callback, array($id, $filepath));
				}		
			}
		}
		return false;
	}	
	public function beforeRender($view)
	{
		Yii::app()->clientScript->registerScript('_configJs', 'var configJs = ' . json_encode($this->jsConfig) . '; ', CClientScript::POS_HEAD);
	  	return true;
	}
}