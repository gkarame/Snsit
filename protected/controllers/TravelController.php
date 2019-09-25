<?php
class TravelController extends Controller{
    public function filters(){
        return array(
            'accessControl'
        );
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('index','view','create','update','delete','upload','deleteUpload'
                ),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny', 
                'users' => array('*')
            )
        );
    }    
    public function init(){
        parent::init();
    }    
    public function actions(){
        return array(
            'upload' => array(
                'class' => 'xupload.actions.CustomXUploadAction',
                'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
                'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
                'stateVariable' => 'travel'
            )
        );
    }
    public function loadModel($id){
        $model = Travel::model()->with('currencyType', 'idUser', 'idCustomer', 'idProject', 'expenseType')->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('travel-list')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $searchArray                = isset($_GET['Travel']) ? $_GET['Travel'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travel/index' => array(
                'label' => Yii::t('translations', 'Travel Expenses'),
                'url' => array('travel/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1 + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new Travel('search');
        $model->unsetAttributes(); 
        $model->attributes = $searchArray;        
        $this->render('index', array(
            'model' => $model
        ));
    }
    public function actionDeleteUpload(){
        if (isset($_GET['model_id'], $_GET['file'])) {
            $id = (int) $_GET['model_id'];
            if (isset($_GET['id_customer'])) {
                $customer = (int) $_GET['id_customer'];
            } else {
                $customer = (int) Yii::app()->db->createCommand("SELECT id_customer FROM travel WHERE id = $id")->queryScalar();
            }
            $filepath = Eas::getDirPath($customer, $id) . $_GET['file'];
            $success  = is_file($filepath) && $filepath !== '.' && unlink($filepath);
            if ($success) {
                $query = "UPDATE `trave` SET file='' WHERE id='$id'";
                Yii::app()->db->createCommand($query)->execute();
            }
        }
    }
    public function actionView($id){
        $model  = $this->loadModel($id);
        $arr    = Utils::getShortText(Yii::t('translations', 'Travel #' . $model->travel_cod));
        $subtab = $this->getSubTab(Yii::app()->createUrl('travel/view', array(
            'id' => $id
        )));        
        $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travel/view/' . $id => array(
                'label' => $arr['text'],
                'url' => array('travel/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? Yii::t('translations', 'Travel #' . $model->travel_cod) : ''
                ),
                'subtab' => $subtab,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));        
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = $subtab;
        $this->render('view', array(
            'model' => $model
        ));
    }
    public function actionUpdate($id = null){
        if($id!=null){
		$model = $this->loadModel($id);
		$arr = Utils::getShortText(Yii::t('translations', 'Travel #'.$model->travel_cod));
		$subtab = $this->getSubTab(Yii::app()->createUrl('travel/update', array('id' => $id)));	
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array(
				'/travel/update/'.$id => array(
					'label'=>$arr['text'],
					'url' => array('travel/update', 'id'=>$id),
					'itemOptions'=>array('class'=>'link', 'title' => $arr['shortened'] ? Yii::t('translations', 'Travel #'.$model->travel_cod) : ''),
					'subtab' =>  $subtab,
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = $subtab;			
		if (isset($_POST['Travel']))
		{	
			$oldbillability= $model->billable;
			//echo $model->billable; echo $model->amount;
			if ($_POST['Travel']['billable'] == 'no'){
				$_POST['Travel']['status'] = Travel::STATUS_CLOSED;
			}	
			if (($_POST['Travel']['billable'] == 'yes' || $_POST['Travel']['billable'] == 'Yes' ) && $model->billable=='No'){
				$model->attributes = $_POST['Travel'];
				$id=Invoices::checkToPrintTravelInvoices($model);
				if(substr($model->id_project, -1) == 't') { $model->id_project = substr($model->id_project, 0, -1);}
						if(!empty($id) && isset($id)){														
							Invoices::UpdateInvoiceAmount($id,$model->amount);
							$final_number_inv=Invoices::getFinalInvNumberById($id);
							Travel::changeStatusInvOne($model->id,$id,$final_number_inv);
								$model->status=1;							
						}else{
								Travel::createInvoice($model); 
								$model->status=1;
							}
				
			}												
			$model->attributes = $_POST['Travel'];
			if(substr($model->id_project, -1) == 't'){
				$model->training = 1;
				$model->id_project = substr($model->id_project, 0, -1);
			}
			if($model->id_customer == 0 && $model->id_customer != null){
				$model->id_project = 0;
			}
			if ($model->save()){
				if ($model->billable=='yes' || $model->billable=='Yes'){
					$checkcust= Yii::app()->db->createCommand("select count(*) from  customers WHERE id =".$model->id_customer." and country=113")->queryScalar();
					if ($checkcust>0 && $model->expense_type ==102 ){
						$getid=Yii::app()->db->createCommand("select max(id)+1 from travel")->queryScalar();
						$travelcodevisa = Utils::paddingCode($getid);
						if($model->training=='1')
						{$checkcust= Yii::app()->db->createCommand("insert into travel (id, travel_cod, id_user, id_customer,id_project,expense_type, amount, currency, billable, status,date,inv_number,final_inv_number, training,file)
						values (".$getid.", '".$travelcodevisa."', ".$model->id_user.", ".$model->id_customer.", ".$model->id_project.", 104,250, 9, 'Yes', 1, '".$model->date."', null, null,'1', null)")->execute();
						

						}else{
							$checkcust= Yii::app()->db->createCommand("insert into travel (id, travel_cod, id_user, id_customer,id_project,expense_type, amount, currency, billable, status,date,inv_number,final_inv_number, training,file)
						values (".$getid.", '".$travelcodevisa."', ".$model->id_user.", ".$model->id_customer.", ".$model->id_project.", 104,250, 9, 'Yes', 1, '".$model->date."', null, null,null, null)")->execute();
						
						}
						$visa= Travel::model()->findByPk($getid);
						$idvisa=Invoices::checkToPrintTravelInvoices($visa);

							if(!empty($idvisa) && isset($idvisa)){															
								Invoices::UpdateInvoiceAmount($idvisa,$visa->amount);
								$final_number_inv2=Invoices::getFinalInvNumberById($idvisa);
								Travel::changeStatusInvOne($visa->id,$idvisa,$final_number_inv2);
									$model->status=1;								
							}else{

									Travel::createInvoice($visa); 
									$visa->status=1;
								}

					}
				}else if( $model->training != 1 && !empty($model->id_project) && $model->id_project != 0 && ($oldbillability == 'yes' || $oldbillability == 'Yes') ){
                	$b= Projects::getBillability($model->id_project);
                	if(!empty($b) && $b == 1)
                	{
                		self::sendBillabilityAlert($model->id_project,$model->id_customer,$model->id_user,$model->id,$model->travel_cod );
                	}
                }   
				Utils::closeTab(Yii::app()->request->url);
				$this->redirect(array('travel/index'));
			}
		}	
		$this->render('update',array(
			'model'=>$model,
		));
		}else{
			if(isset($_POST['status'])&& isset($_POST['id'])){
				$model = $this->loadModel($_POST['id']);
				$model->status= $_POST['status'];
				$model->save();
			}
		}
    }
    public function actionCreate(){
        if (!GroupPermissions::checkPermissions('travel-list', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travel/create' => array(
                'label' => Yii::t('translations', 'New Travel Expense'),
                'url' => array('travel/create'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = 0; 
        $model = new Travel();
        if (isset($_POST['Travel'])) {
            if ($_POST['Travel']['billable'] == 'no') {
                $_POST['Travel']['status'] = Travel::STATUS_CLOSED;
            }
            $_POST['Travel']['travel_cod'] = '00000';
            $_POST['Travel']['currency']   = CurrencyRate::OFFICIAL_CURRENCY;            
            $model->attributes = $_POST['Travel'];
            if (isset($_POST['Travel']['id_user'])) {
                $array_first_name = (explode(' ', $_POST['Travel']['id_user']));
                $first_name       = $array_first_name[0];
                $last_name        = trim(substr(strstr($_POST['Travel']['id_user'], ' '), 1));
                $id_user        = Yii::app()->db->createCommand("SELECT id FROM users WHERE firstname = '$first_name' AND lastname = '$last_name'")->queryScalar();
                $model->id_user = $id_user;
            }
            if (isset($_POST['Travel']['id_customer'])) {
                $model->id_customer = Customers::getIdByName($_POST['Travel']['id_customer']);
            }
            if (substr($model->id_project, -1) == 't') {
                $model->training   = 1;
                $model->id_project = substr($model->id_project, 0, -1);
            }
            if ($model->id_customer == 0 && $model->id_customer != null) {
                $model->id_project = 0;
            }            
            if ($model->save()) {
                $model->travel_cod = Utils::paddingCode($model->id);
                if ($model->billable == 'yes' || $model->billable == 'Yes') {
                	$id = Invoices::checkToPrintTravelInvoices($model);
                	if (!empty($id) && isset($id)) { 
                	    Invoices::UpdateInvoiceAmount($id, $model->amount);    
                	    $final_number_inv = Invoices::getFinalInvNumberById($id);    
                	    Travel::changeStatusInvOne($model->id, $id, $final_number_inv);    
                	  //  $model->status = 1;    
                	} else {     
                	       Travel::createInvoice($model);   // $model->status = 1;
                	}
                } else if( $model->training != 1 && !empty($model->id_project) && $model->id_project != 0){
                	$b= Projects::getBillability($model->id_project);
                	if(!empty($b) && $b == 1)
                	{
                		self::sendBillabilityAlert($model->id_project,$model->id_customer,$model->id_user,$model->id,$model->travel_cod );
                	}
                }               
                $model->save();
                Utils::closeTab(Yii::app()->request->url);
                $this->redirect(array('travel/update','id' => $model->id
                ));
            } else {
                if (isset($_POST['Travel']['id_user'])) {$model->id_user = $_POST['Travel']['id_user'];
                }
                if (isset($_POST['Travel']['id_customer'])) {$model->id_customer = $_POST['Travel']['id_customer'];
                }
                if (isset($_POST['Travel']['id_project'])) {$model->id_project = $_POST['Travel']['id_project'];
                }
            }
        }
        
        $this->render('create', array(
            'model' => $model
        ));
    }
    
    public function sendBillabilityAlert($project, $customer, $user, $tr, $code)
	{
		$notif = EmailNotifications::getNotificationByUniqueName('travel_billability_alert');
		$subject = 'Travel Billability Alert - '.Users::getNameById($user);	
    	if ($notif != NULL){
    		
    		$txt='Dears,  <br/><br/>Kindly note that travel expense #<a class="show_link" href="'.Yii::app()->createUrl("travel/view", array("id" => $tr)).'">'.$code.'</a> has been created as non billable for <b>'.Users::getNameById($user).'</b> on project <b>'.Projects::getNameById($project).'</b> for customer <b>'.Customers::getNameById($customer).'</b> with billability.<br/><br/>Best Regards,<br/>SNSit';

			Yii::app()->mailer->ClearAddresses();
    		
    		$to_replace = array('{body}');
    		$replace = array($txt);

			$body = str_replace($to_replace, $replace, $notif['message']);
				
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);	
			foreach($emails as $email) {
			if (!empty($email))
				{		
					Yii::app()->mailer->AddAddress($email);
				}
			}
				
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
    public function actionDelete($id){
        $id = (int) $id;
        Yii::app()->db->createCommand("DELETE FROM travel WHERE id='{$id}'")->execute();
    }
}?>