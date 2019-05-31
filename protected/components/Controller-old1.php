<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	/**
	 * 
	 * @var array context tabs menu
	 */
	public $action_menu = array();
	
	//javascript configuration object to be placed in global namespace;
	public $jsConfig = null;
	
	public function init()
	{
		//init javascript configuration object
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
			case 'invoices' : 
		
				$expenses_ids = array();
				$html = '';
				$pdf = Yii::app()->ePdf->mpdf();

				foreach ($id as $model) 
				{
					$pdff = Yii::app()->ePdf->mpdf();
					$pdff_snsi = Yii::app()->ePdf->mpdf();
					$pdff_snsapj = Yii::app()->ePdf->mpdf();
					$pdff_apj = Yii::app()->ePdf->mpdf();
					$invoices = array();
					$exp = array();
					$models = Yii::app()->db->createCommand("SELECT id,id_ea,final_invoice_number FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND status = 'To Print' AND id IN $ids_invoices ")->queryAll();
					$expenses_ids = Yii::app()->db->createCommand("SELECT distinct id_expenses FROM invoices WHERE id_customer = '{$model['id_customer']}' AND id_project = '{$model['id_project']}' AND partner = '{$model['partner']}' AND id IN $ids_invoices ")->queryAll();
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
					if (Codelkups::getCodelkup($model['partner']) == 'SPAN' && $model['old'] == "Yes")
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

					$ids_new_inv = Travel::getTravel($model['id_project'], $model['id_customer'],$final_number);
					foreach($expenses_ids as $expenses_id1)
					{		
						$expenses = Expenses::model()->findByPk((int)$expenses_id1['id_expenses']);

						array_push($exp, $expenses);
						Expenses::changeStatusInvoiced((int)$expenses_id1['id_expenses']);
					}
					//print invoice
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
			        		
					}else if(Codelkups::getCodelkup($one_model->partner) == 'SNS APJ'){
							$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$snsapj = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							
							$pdff_snsapj->writeHTML($snsapj, 0, true, true);
							$pdff_snsapj->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
							$pdff_snsapj->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id,false,true).'INVOICE_'.str_replace('/','_',$one_model->snsapj_partner_inv).'.pdf', 'F');
							
							$pdf->AddPage();
			        		$pdf->writeHTML($html, 0, true, true);
			        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		$snsapj .= $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
			        		
			        		$pdf->AddPage();
				        	$pdf->writeHTML($html, 0, true, true);
				        	
				        	$pdff->writeHTML($snsapj, 0, true, true);
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
			        		
					}else if((Codelkups::getCodelkup($one_model->partner) == 'SPAN')){ 
								
							$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$invoices,'model'=>$one_model,'ids_news' => $ids_new_inv), true);
							$pdf->AddPage();
		        			$pdf->writeHTML($html, 0, true, true);
		        			
		        			$pdff->writeHTML($html, 0, true, true);
		        			$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
		        			if($one_model->old=='Yes'){
		        				$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->invoice_number).'.pdf', 'F');	
		        			}else{
							$pdff->Output(Invoices::getDirPath($one_model->id_customer, $one_model->id).'INVOICE_'.str_replace('/','_',$one_model->final_invoice_number).'.pdf', 'F');	
							}
					}	


					/*
			        //print eas
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
			        //print expenses
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
						        $pdf->AddPage();
						        $pdf->writeHTML($html, 0, true, true);
						        
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
					} */
					//send Email
					if (Codelkups::getCodelkup($one_model->partner) != 'SPAN')
						InvoicesController::sendNotificationsEmailsPrinted($one_model, 'printed', $ids_invoices); 
				}
				$pdf->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
				$pdf->Output(Invoices::getDirPathMoreInv().'INVOICE_'.date('Y-m-d').'.pdf', 'F');	
				return true;
				break;
			case 'invoicesOne' : 
				
			 	$model = Invoices::model()->findByPk((int)$id);
				$exp = array();
				$array = array();//var_dump($model->partner);
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
					if(Codelkups::getCodelkup($model->partner) == 'SNS')
					{
						$html = $this->renderPartial('application.views.invoices._export_n_a_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);				
						$pdf->AddPage();
					    $pdf->writeHTML($html, 0, true, true);

					}else if(Codelkups::getCodelkup($model->partner) == 'SNSI')
					{ 		
						$html = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$snsi = $this->renderPartial('application.views.invoices._export_snsi_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						
						$pdf->AddPage();
		        		$pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
		        		$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
			        	
			        	$pdff->writeHTML($snsi, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,true,false).'INVOICE_'.str_replace('/','_',$model->partner_inv).'.pdf', 'F');
			        	
						}else if(Codelkups::getCodelkup($model->partner) == 'SNS APJ')
					{
						$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$snsapj = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						
						$pdf->AddPage();
		        		$pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
		        		$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
			        	
			        	$pdff->writeHTML($snsapj, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,false,true).'INVOICE_'.str_replace('/','_',$model->snsapj_partner_inv).'.pdf', 'F');
			        	
						}else if(Codelkups::getCodelkup($model->partner) == 'APJ')
						{
						$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						$apj = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
						
						$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
			        	
			        	$pdff->writeHTML($apj, 0, true, true);
			        	$pdff->SetHTMLFooter($this->renderPartial('application.views.invoices._footer_pdf', array(), true));
			        	$pdff->Output(Invoices::getDirPath($model->id_customer, $model->id,false,true).'INVOICE_'.str_replace('/','_',$model->snsapj_partner_inv).'.pdf', 'F');
			        	
						}else if((Codelkups::getCodelkup($model->partner) == 'SPAN'))
						{
							$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$array,'model'=>$model,'ids_news' => $ids_new_inv), true);
							$pdf->AddPage();
		        			$pdf->writeHTML($html, 0, true, true);
						}
				 	  foreach ($eas as $ea)
				 	  {
						if($ea['final_invoice_number'] == null && $ea['id_ea'] != null){
					    	$model_ea = Eas::model()->findByPk((int)$ea['id_ea']);
					    	if(isset($model_ea))
					    	{
						    	if ($model_ea->getFile(false,true) != null)
						       	{
							        $pdf->AddPage();
							        $ext = pathinfo($model_ea->getFile(false,true), PATHINFO_EXTENSION);
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
				    }else
				    {
			   	 		$pdf->Output(Invoices::getDirPath($model->id_customer, $model->id).'INVOICE_'.str_replace('/','_',$model->final_invoice_number).'.pdf', 'F');	
				    }
				    return true;
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
			 
			
			 case 'check' :
				$model = SuppliersPrint::model()->findByPk((int)$id);
				if (isset($model))
			 	{
			 		$html = $this->renderPartial('application.views.suppliers._bank_check_pdf', array('model'=>$model), true);
			 		//$pdf = Yii::app()->ePdf->mpdf();
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
						$pdf->AddPage();
		        		$pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsi_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
				    
					}else if(Codelkups::getCodelkup($model->partner) == 'SNS APJ')
					{
						$html = $this->renderPartial('application.views.invoices._export_snsapj_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage();
		        		$pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_snsapj_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
				    
					}else if(Codelkups::getCodelkup($model->partner) == 'APJ')
					{
						$html = $this->renderPartial('application.views.invoices._export_apj_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage();
		        		$pdf->writeHTML($html, 0, true, true);
		        		$html = $this->renderPartial('application.views.invoices._export_apj_noex_2_pdf', array('models'=>$array,'model'=>$model), true);
		        		$pdf->AddPage();
			        	$pdf->writeHTML($html, 0, true, true);
				    
					}else{ 
						$html = $this->renderPartial('application.views.invoices._export_span_pdf', array('models'=>$array,'model'=>$model), true);
						$pdf->AddPage();
	        			$pdf->writeHTML($html, 0, true, true);
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
					else
					{
						$pdf = Yii::app()->ePdf->mpdf();
					}
					$html = $this->renderPartial($route.'pdf_report_head', array('expenses'=>$id), true);
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
								$pdf->AddPage();
			        			$pdf->writeHTML($html, 0, true, true);
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
							$pdf->AddPage();
		        			$pdf->writeHTML($html, 0, true, true);
						}
					}
					
					//$pdf->WriteHTML($html, 0, true, false);  
					$pdf->SetHTMLFooter($this->renderPartial($route.'_footer_pdf', array(), true));
					$pdf->Output(Utils::getDirPathReports().'REPORTS.pdf', 'F');
					return true;
				}
				break;
		}
		return false;
	}
	
	public function getSubTab($curl = null)
	{
		$baseurl = Yii::app()->request->getBaseUrl(true);
		$baseUrl = str_replace(Yii::app()->request->getHostInfo(), "", $baseurl);
		// the real controller/action
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
		// put config object in the global namespace
		Yii::app()->clientScript->registerScript('_configJs', 'var configJs = ' . json_encode($this->jsConfig) . '; ', CClientScript::POS_HEAD);

	  	return true;
	}
}