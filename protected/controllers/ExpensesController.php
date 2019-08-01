<?php
class ExpensesController extends Controller{	
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete', 
		);
	}
	public function init(){
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - Expenses');
	}
	public function accessRules(){
		return array(
			array('allow', 
				'actions'=>array(
						'index','create', 'delete', 'update', 'deleteItem', 'approval', 'view','duplicateitems','multiApproval', 'multipay', 'multipayprint',
						'updateHeader' ,'UpdateHeaderList', 'createItem', 'getUSDRate', 'deleteItem', 'getNote', 'manageItem',
						 'approveExpens', 'rejectExpens', 'print', 'printSelected','upload','deleteUpload','createInvoice',
						'printBankTransfer','generateTransfer','GetCountryCustomerProject'
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}
	public function loadModel($id){
		$model = Expenses::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($array){
		$errors = array();
		if (isset($_POST['ajax']) && $_POST['ajax']==='expenses-form')
		{
			$result = array();
			foreach ($array as $key=> $model) {
				if (is_array($model)) {
					$result = CCustomActiveForm::validateTabular($model, null, true, false);
					$errors = array_merge($errors, $result);
					unset($array[$key]);
				}
			}
			
			$errors = array_merge(CCustomActiveForm::validate($array, null, true, false), $errors);
			if (empty($errors)) 
				echo json_encode(array('status'=>'success'));
			else 
				echo json_encode(array('status'=>'failure', 'errors' => $errors));
			Yii::app()->end();
		}
	}	
	public function actions(){
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' => Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'expenses_uploads'
			 ),
		 );
	}
	public function actionGetCountryCustomerProject($id){
	    $command = Yii::app()->db->createCommand();
	    $country_name = $command->select('codelkups.codelkup')
            ->from('projects')
            ->join('customers','customers.id=projects.customer_id')
            ->join('codelkups','codelkups.id=customers.country ')
            ->where('projects.id=:id', array(':id'=>$id))
            ->queryRow();

	    echo CJSON::encode($country_name);
    }
	public function actionIndex(){
		$searchArray = isset($_GET['Expenses']) ? $_GET['Expenses'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/expenses/index' => array(
					'label'=>Yii::t('translations','My Expenses'),
					'url' => array('expenses/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));		
		Yii::app()->session['menu'] = $this->action_menu;			
		$model = new Expenses('search');	$model->unsetAttributes(); 
		$model->user_id = Yii::app()->user->id;	$model->attributes= $searchArray;
		if(isset($model->customer_id)){
			$model->customer_name = Customers::getIdByName($model->customer_id); 
		}
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionApproval(){
		if (!GroupPermissions::checkPermissions('expenses-expenses_approval','read', Yii::app()->user->id, 1)){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$searchArray = isset($_GET['Expenses']) ? $_GET['Expenses'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/expenses/approval' => array(
					'label'=>Yii::t('translations','Expenses Approval'),
					'url' => array('expenses/approval'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));	
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Expenses('search');	$model->unsetAttributes();  
		$model->attributes= $searchArray;
		if(isset($model->customer_id))
			$model->customer_name = Customers::getIdByName($model->customer_id); 
		$this->render('approval',array(
			'model'=>$model,
		));		
	}	
	public function actionCreate(){
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/expenses/create' => array(
					'label'=> 'New Expense',
					'url' => array('expenses/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Expenses();		
		if (isset($_POST['Expenses'])){
		    $prediem = CountryPerdiem::model()->find('id_country=:id',array('id' => (int)$_POST['Expenses']['country_id']));
            $has_perdiem = false;

		    if (!empty($_POST['Expenses']['project_id'])){
                $prediem_eas = Eas::model()->findAll('id_project=:project',array('project' => (int)$_POST['Expenses']['project_id']));

                foreach ($prediem_eas as $item){
                    if (isset($item['country_perdiem_id'])) $has_perdiem = true;
                }
            }

		    if (!isset($prediem) && $has_perdiem){
                $model->addError('country_id','Country is not specified under settings');
            }

		    if (empty($model->getErrors())){
                $model->attributes = $_POST['Expenses'];
                $model->no = "00000";
                $model->user_id = Yii::app()->user->id;
                $model->status = Expenses::STATUS_NEW;
                $model->creationDate =  date('Y-m-d');
                if(substr($model->project_id, -1) == 't'){
                    $model->training = 1;	$model->project_id = substr($model->project_id, 0, -1);
                }
                $model->billable = Projects::isBillable($model->project_id) == 1 ? 'yes' : 'no';
                if (empty($_POST['Expenses']['currency'])){
                    $model->currency = Customers::getId('default_currency', $model->customer_id);
                }
                if($model->customer_id == 0 && $model->customer_id != null){
                    $model->project_id = 0;
                }

                if ($model->save()) {
                    $model->no = Utils::paddingCode($model->id);
                    $model->startDate = date('d/m/Y',strtotime($model->startDate));
                    $model->endDate = date('d/m/Y',strtotime($model->endDate));

                    if ($has_perdiem){
                        $diff_date = date_diff(new DateTime(str_ireplace('/','-',$model->endDate)), new DateTime(str_ireplace('/','-',$model->startDate)))->days;

                        $expense_prediem = new ExpensesDetails();
                        $expense_prediem->expenses_id = $model->id;
                        $expense_prediem->type = 47;
                        $expense_prediem->currency_rate_id = 66;
                        $expense_prediem->original_amount = (float)$prediem['per_diem'] * $diff_date;
                        $expense_prediem->amount = $expense_prediem->original_amount;
                        $expense_prediem->billable = "Yes";
                        $expense_prediem->payable = "Yes";
                        $expense_prediem->notes = "";
                        $expense_prediem->date = date('d/m/Y',time());
                        $expense_prediem->original_currency=9;

                        if ($expense_prediem->save()){
                            $model->billable_amount = $expense_prediem->original_amount;
                            $model->payable_amount = $expense_prediem->original_amount;
                            $model->total_amount = $expense_prediem->original_amount;
                        }
                    }

                    $model->save();

                    $this->redirect(array('expenses/update', 'id'=>$model->id, 'new' => 1));
                }
            }
		}		
		$this->render('create', array('model' => $model));
	}

	public function actionduplicateitems()	{
		if (isset($_POST['checkinvoice'])){ 
			$exp= implode(',', $_POST['checkinvoice']);
			Yii::app()->db->createCommand("INSERT INTO expenses_details (expenses_id,type, original_currency,original_amount,amount,currency, currency_rate_id,billable,payable,date, notes ) SELECT expenses_id,type, original_currency,original_amount,amount,currency, currency_rate_id,billable,payable,date, notes  FROM expenses_details where id in (".$exp.") ")->execute();
			$expense = Yii::app()->db->createCommand("SELECT expenses_id FROM expenses_details where id in (".$exp.") LIMIT 1 ")->queryScalar();
			$model = $this->loadModel($expense);
			$inv='';
			$status='saved';
			$branch= Expenses::getBranchByUser(Yii::app()->user->id); 
			$ratess=Expenses::getRateByBranch($branch); 
			$total = 0;	$billable = 0;	$payable = 0;		
			$items = $model->expensesDetails;
			foreach ($items as $item){
				if ($item->billable == 'Yes') $billable += $item->amount;
				if ($item->payable == 'Yes') $payable += $item->amount;
				if($item->type != '52' ){ $total += $item->amount; }
			}		
			Yii::app()->db->createCommand("UPDATE expenses SET total_amount='{$total}', billable_amount='{$billable}', payable_amount='{$payable}' WHERE id='{$model->id}'")->execute();
			if ($branch!= 31){
					echo json_encode(array(
				'inv' => $inv,'status'=> $status,
				'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",'total_amount' => Utils::formatNumber($total), 
						'billable_amount'=> Utils::formatNumber($billable), 
						'payable_amount' => Utils::formatNumber($payable),
						'total_amount_curr'=> round($total/$ratess,2),
					));				
			} else {
					
		echo json_encode(array(
				'inv' => $inv,'status'=> $status,
				'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
						'total_amount' => Utils::formatNumber($total), 
						'billable_amount'=> Utils::formatNumber($billable), 
						'payable_amount' => Utils::formatNumber($payable)
			));	
			}

		}else{ 
			$inv = 'You have to select at least one invoice!';
			$status='error';
			echo json_encode(array(
				'inv' => $inv,'status'=> $status,
				'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
			));	
		}
			
	}
	public function actionUpdate($id, $new = 0)
	{
		$id = (int) $id;	$new = (int) $new;
		$error_billable_item = false;
		$model = $this->loadModel($id);
		if ($model->user_id != Yii::app()->user->id && Projects::getId('project_manager', $model->project_id) != Yii::app()->user->id){
		 	throw new CHttpException(403, 'You don\'t have permission to access this page');
		}		
		if ($new == 1) {
			if (isset(Yii::app()->session['menu'])) {
				Utils::closeTab(Yii::app()->createUrl('expenses/create'));
				$this->action_menu = Yii::app()->session['menu'];
			}
		}
		if (isset($_POST['Expenses'])){	
			$model->attributes = $_POST['Expenses'];
			$model->startDate = date('d/m/Y',strtotime($model->startDate));
			$model->endDate = date('d/m/Y',strtotime($model->endDate));
			if ($model->validate())	{	
				$count= Timesheets::getPendingTimesheetsCount();				
				if ($count>1){
					$timesheet=true;
					$message="You have more than 1 Time Sheet Pending, Cannot create a new Expense Sheet";	
				}else{
					$timesheet=false;
				}
				$count2= Timesheets::getPendingTimesheetsCount2($model->startDate, $model->endDate);
				if ($count2>1){
					$timesheet2=true;
					$message="You have at least one timesheet falling under the same timeframe.";	
				}else{
					$timesheet2=false;
				}
				$is_billable = ExpensesDetails::getNumberBillableItem($model->id);
				if ($is_billable == 1) {
					if ($model->number_file > 0){
						$error_billable_item = false;
					}else{
						$error_billable_item = true;
						$message="You have at least one billable item but you haven't uploaded a file";
					}
				}
				if (!$error_billable_item && !$timesheet && !$timesheet2){
					if ($model->save()) {
						$this->sendNotificationsEmails($model);
						Utils::closeTab(Yii::app()->request->url);
						$this->redirect(array(Utils::getMenuOrder(true)));	
					}	
				}else{
					$this->redirect(array('expenses/update/', 'id'=>$id, 'returnn'=>$message));
				}
			}	
		}			
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
		$this->action_menu,
			array( 
					'/expenses/update/'.$id => array(
							'label'=> 'Expense #'.$model->no,
							'url' => array('expenses/update', 'id'=>$id),
							'itemOptions'=>array('class'=>'link'),
							'subtab' => '',
							'order' => Utils::getMenuOrder()+1,
					)
				)
			))));
		Yii::app()->session['menu'] = $this->action_menu;			
		$expenseDetails = new ExpensesDetails('search');
		$expenseDetails->unsetAttributes(); 
		$expenseDetails->expenses_id = $model->id;
		$this->render('update', array('model' => $model, 'expensDetails' => $expenseDetails, 'isEditable' => self::isEditable($model->status)));
	}
	public function actionUpdateHeader($id){ 	
		$model = $this->loadModel($id);
		if ($model->user_id != Yii::app()->user->id && Projects::getId('project_manager', $model->project_id) != Yii::app()->user->id){
		 	throw new CHttpException(403, 'You don\'t have permission to access this page.');
		}
		if ($model->startDate){
			$model->startDate = date('d/m/Y',strtotime($model->startDate));
		}
		if ($model->endDate){
			$model->endDate = date('d/m/Y',strtotime($model->endDate));
		}
		if(isset($model->training)){
			$model->project_id_t = $model->project_id.'t';
		}
		if (isset($_POST['Expenses']) && self::isEditable($model->status)){
			$model->attributes = $_POST['Expenses'];
			if(substr($model->project_id, -1) == 't'){
				$model->training = 1;
				$model->project_id ='286';
			}
			if(isset($_POST['Expenses']['project_id_t'])){
				if(substr($_POST['Expenses']['project_id_t'], -1) != 't')
				{
					$model->training = null;
					$model->project_id = $_POST['Expenses']['project_id_t'];
				}
			}
			$model->billable = Projects::isBillable($model->project_id);
			if ($model->save())	{
				Yii::app()->clientScript->scriptMap=array(
					'jquery.js'=>false,
					'jquery.min.js' => false,
					'jquery-ui.min.js' => false,	);
				echo json_encode(array(
						'status'=>'saved', 
						'html'=>$this->renderPartial('_header_content', array('model'=> $model), true, true)
					));
				Yii::app()->end();
			}
		}		
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
			'jquery.min.js' => false,
			'jquery-ui.min.js' => false,
		);
		echo json_encode(array(
				'status'=>'success', 
				'html'=>$this->renderPartial('_update_header_content', array('model'=> $model), true, true)
			));
		Yii::app()->end();
	}
	public function actionUpdateHeaderList($id= null)
	{
		if (isset($_GET['id'])){
			$id=$_GET['id'];
		}
		$model = $this->loadModel($id);
		if ($model->user_id != Yii::app()->user->id && Projects::getId('project_manager', $model->project_id) != Yii::app()->user->id){
		 	throw new CHttpException(403, 'You don\'t have permission to access this page.');
		}
		if ($model->startDate){
			$model->startDate = date('d/m/Y',strtotime($model->startDate));
		}
		if ($model->endDate){
			$model->endDate = date('d/m/Y',strtotime($model->endDate));
		}
		if(isset($model->training))
		{
			$model->project_id_t = $model->project_id.'t';
		}	
		$model->status='Submitted';
		if ($model->save()){

						$this->sendNotificationsEmails($model);
				Yii::app()->clientScript->scriptMap=array(
					'jquery.js'=>false,
					'jquery.min.js' => false,
					'jquery-ui.min.js' => false,
				);
				echo json_encode(array(
						'status'=>'saved', 
						'html'=>$this->redirect(array('expenses/index'))
					));			
				Yii::app()->end();
		}
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
			'jquery.min.js' => false,
			'jquery-ui.min.js' => false,
		);
		echo json_encode(array(
				'status'=>'success', 
			));
		Yii::app()->end();
	}
	public function actionGetNote($id){
		$this->layout='';
		echo json_encode(ExpensesDetails::getNote((int)$id));
		exit();
	}
	public static function isEditable($status){
		return in_array($status, array(Expenses::STATUS_REJECTED, Expenses::STATUS_NEW));
	}
	public function actionCreateItem(){
		$model = new ExpensesDetails();
		if (!isset($_POST['expenses_id'])){
			exit;
		}		
		$model->expenses_id = $_POST['expenses_id']; 
		$model->billable = ucfirst($model->expenses->billable);
		$model->original_currency = $model->expenses->currency;			
		$project_id = (int)$model->expenses->project_id;
		$customer_id = (int)$model->expenses->customer_id;
		$expens = Yii::app()->db->createCommand("SELECT expense FROM eas where id_project = '$project_id' AND id_customer = '$customer_id' ")->queryScalar();
		if (isset($_POST['ExpensesDetails'])){
   			$model->attributes = $_POST['ExpensesDetails'];   			
   			if (!isset($_POST['ExpensesDetails']['billable'])){
   				$model->billable='No';
   			}
   			$rateData = CurrencyRate::getCurrencyRate($model->original_currency);
   			$model->amount = Utils::formatNumber($rateData['rate']*$model->original_amount, 3, ".", "");
   			$model->currency_rate_id = $rateData['id'];
			$model->notes = $_POST['ExpensesDetails']['notes'];
   			if ($model->save()){
				$expens = $this->loadModel($model->expenses_id);
				echo json_encode(array('status'=>'saved', 'amounts' => self::refreshAmounts($expens), 'form'=>$this->renderPartial('_item_form', array(
	            	'model'=> $model, 'expens' => $expens
	        	), true, true)));
	        	exit;	
			}
   		}
		Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form', array(
            	'model'=> $model, 'expens' => $expens,'expense_model' => Expenses::model()->find('id=:id',array('id' => $model->expenses_id))
        	), true, true)));
        exit;
	}
	public function actionManageItem($id){
		$model = ExpensesDetails::model()->findByPk((int)$id);
		if ($model->date){
			$model->date = date('d/m/Y', strtotime($model->date));
		}
		Yii::app()->clientScript->scriptMap=array(
						'jquery.js'=>true,
						'jquery.min.js' => true,
						'jquery-ui.min.js' => true,	);

		$project_id = (int)$model->expenses->project_id;
		$customer_id = (int)$model->expenses->customer_id;
		$expens = Yii::app()->db->createCommand("SELECT expense FROM eas where id_project = '$project_id' AND id_customer = '$customer_id' ")->queryScalar();
		if (isset($_POST['ExpensesDetails'])){
   			$model->attributes = $_POST['ExpensesDetails'];
   			$rateData = CurrencyRate::getCurrencyRate($model->original_currency);
   			$model->amount = Utils::formatNumber($rateData['rate']*$model->original_amount, 3, ".", "");
   			$model->currency_rate_id = $rateData['id'];
   			$model->notes = $_POST['ExpensesDetails']['notes'];
   			if ($model->save()){
					$model->save();					
					echo json_encode(array('status'=>'saved', 'amounts' => self::refreshAmounts($this->loadModel($model->expenses_id)), 'form'=>$this->renderPartial('_item_form', array(
		            	'model'=> $model, 'expens' => $expens
		        	), true, true)));
		        	exit;	
			}
   		}
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_item_form', array(
            	'model'=> $model, 'expens' => $expens
        	), true, true)));
        Yii::app()->end();
	}
	public function actionDeleteItem($id){
		$model = ExpensesDetails::model()->findByPk($id);
		if ($model===null)
			echo json_encode(array('status'=>'error', 'error' => '404'));	
		if ($model->delete()) {
			$expens = $this->loadModel($model->expenses_id);
			echo json_encode(array('status'=>'saved', 'amounts' => self::refreshAmounts($expens)));	
		}
	}	
	public function actionDelete($id){
		$model = $this->loadModel($id);
		if ($model->delete()) {			
			echo json_encode(array('status'=>'saved'));	
			exit();
		} 
		echo json_encode(array('status'=>'error'));	
	}
	public function actionGetUSDRate($id){
		$this->layout='';
		echo json_encode(CurrencyRate::getCurrencyRate((int)$id));
		exit();
	}	
	public static function refreshAmounts($model){	
		$branch= Expenses::getBranchByUser(Yii::app()->user->id); 
		$ratess=Expenses::getRateByBranch($branch); 
		$total = 0;	$billable = 0;	$payable = 0;		
		$items = $model->expensesDetails;
		foreach ($items as $item){
			if ($item->billable == 'Yes') $billable += $item->amount;
			if ($item->payable == 'Yes') $payable += $item->amount;
			if($item->type != '52' ){ $total += $item->amount; }
		}		
		Yii::app()->db->createCommand("UPDATE expenses SET total_amount='{$total}', billable_amount='{$billable}', payable_amount='{$payable}' WHERE id='{$model->id}'")->execute();
			if ($branch!= 31){
		return array(
					'total_amount' => Utils::formatNumber($total), 
					'billable_amount'=> Utils::formatNumber($billable), 
					'payable_amount' => Utils::formatNumber($payable),
					'total_amount_curr'=> round($total/$ratess,2)
		); } else {
		return array(
					'total_amount' => Utils::formatNumber($total), 
					'billable_amount'=> Utils::formatNumber($billable), 
					'payable_amount' => Utils::formatNumber($payable)
					
		); 		}
	}
	public function actionView($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		if ($model->user_id != Yii::app()->user->id && 
			Projects::getId('project_manager', $model->project_id) != Yii::app()->user->id && 
			!(GroupPermissions::checkPermissions('expenses-expenses_approval', 'read', Yii::app()->user->id, 1))){
		 	throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		if (isset($_POST['Expenses'])){	
			$model->attributes = $_POST['Expenses'];			
			$model->startDate = date('d/m/Y',strtotime($model->startDate));
			$model->endDate = date('d/m/Y',strtotime($model->endDate));			
			if ($model->save()) {  	
				$this->sendNotificationsEmails($model);
				if($model->status == Expenses::STATUS_PAID){
					self::CreateInvoice($model->id);
					echo json_encode(array('status'=>'success'));
					exit;
				}
				$this->redirect(array('expenses/approval'));
			}
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/expenses/view/'.$id => array(
					'label'=> 'Expense #'.$model->no,
					'url' => array('expenses/view', 'id' => $model->id),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$expenseDetails = new ExpensesDetails('search');
		$expenseDetails->unsetAttributes();
		$expenseDetails->expenses_id = $model->id;
		$this->render('view', array('model' => $model, 'expensDetails' => $expenseDetails));
	}
	private function sendNotificationsEmails($model){
		$notif = EmailNotifications::getNotificationByUniqueName('expenses_'.strtolower($model->status));
		$customer_id = (int) Yii::app()->db->createCommand("SELECT customer_id FROM expenses WHERE id = ".$model->id."")->queryScalar();
		$project_id = (int) Yii::app()->db->createCommand("SELECT project_id FROM expenses WHERE id = ".$model->id."")->queryScalar();
		if ($notif != NULL) {
	    	if(EmailNotifications::isNotificationSent($model->id, 'expenses', $notif['id']) == false) {
					$subject = str_replace('{no}', $model->no, $notif['name']);
					$to_replace = array(
						'{no}',
						'{no_url}', 
						'{amount}', 
						'{username}', 
						'{billableItems}', 
						'{payable}',	
						'{not_payable}', 
						'{billable}', 
						'{not_billable}', 
						'{currentUser}', 
						'{reason}',
						'{customer_name}',
						'{projectname}'
					);
					$i = 1;		$billableItems = '<ul>';
					foreach($model->expensesDetails as $item)	{							
						if($item->billable == 'Yes'){
					$billableItems .= '<li>'.$i++.' - '.$item->type0->codelkup.' - '.Utils::formatNumber($item->original_amount).$item->currency1->codelkup.' - '.Customers::getNameById($customer_id).'</li>';
						}
					}
					$billableItems .= '</ul>';
					$replace = array(
						$model->no, 
						'<a href="'.Yii::app()->createAbsoluteUrl('expenses/view', array('id'=>$model->id)).'">'.$model->no.'</a>', 
						Utils::formatNumber($model->total_amount), 
						Users::getNameById($model->user->id), 
						$billableItems,
						Utils::formatNumber($model->payable_amount), 
						Utils::formatNumber(($model->total_amount-$model->payable_amount)), 
						Utils::formatNumber($model->billable_amount), 
						Utils::formatNumber(($model->total_amount-$model->billable_amount)),
						Users::getNameById(Yii::app()->user->id), 
						(isset($_POST['rejected_message'])?$_POST['rejected_message'].'<br/><br/>Kindly update this <a href="'.Yii::app()->createAbsoluteUrl('expenses/update', array('id'=>$model->id)).'">expense</a>.':''),
						Customers::getNameById($customer_id),
						Projects::getNameById($project_id)

					);					
					$body = str_replace($to_replace, $replace, $notif['message']);
					$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
					Yii::app()->mailer->ClearAddresses();
					foreach($emails as $email) {
						if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
							Yii::app()->mailer->AddAddress($email);
					}
					if($model->status == Expenses::STATUS_SUBMITTED){
						$adr1 = UserPersonalDetails::getEmailById($model->project->project_manager);
						if(filter_var($adr1, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($adr1);
					} 
					elseif($model->status == Expenses::STATUS_APPROVED || $model->status == Expenses::STATUS_PAID || $model->status == Expenses::STATUS_REJECTED)
					{
						$adr2 = UserPersonalDetails::getEmailById($model->user_id);
						if(filter_var($adr2, FILTER_VALIDATE_EMAIL))
							Yii::app()->mailer->AddAddress($adr2);
					}
					//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");					
					if (Yii::app()->mailer->Send(true))	{
						EmailNotifications::saveSentNotification($model->id, 'expenses', $notif['id']); 
						return true;
					}
    			} 
    	}
    	return false;
	}	
	public function actionPrint($id) {
		$model = $this->loadModel((int) $id);	
		$dir = dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$model->customer_id.'/expenses/';
		if (!file_exists($dir)) {
		    mkdir($dir, 0777, true);
		}
		if ($this->generatePdf('expenses', $id)) 
		{
			if (file_exists(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$model->customer_id.'/expenses/Expenses'.$model->no.'.pdf')) 
			{
				header('Content-disposition: attachment; filename=Expenses'.$model->no.'.pdf');
				header('Content-type: application/pdf');
				readfile(dirname(Yii::app()->request->scriptFile).'/uploads/customers/'.$model->customer_id.'/expenses/Expenses'.$model->no.'.pdf');
				Yii::app()->end();
			} 
		} 
		$this->redirect(Yii::app()->user->returnUrl);
	}	
	public function actionprintSelected() {
		if(!empty( $_POST['inv'])){
			$ids= $_POST['inv'];
			$this->generatePdf('expensesAll',$ids); 
			$file = Utils::getFileexpenses();	
			 echo json_encode(array(
            'status' => 'success',
            'url' => $file ,
        ));
        exit;
			 
		}		 
	}	
	public function actionmultipayprint()
	{
		if(!empty( $_POST['inv'])){
			$ids= $_POST['inv'];
			$users= array();
			if(!empty($ids))
			{
				$users=  Yii::app()->db->createCommand("SELECT user_id, sum(payable_amount) as amt FROM expenses WHERE id in (".implode(',', $ids).") group by user_id ")->queryAll();	
			}

			foreach ($ids as $key => $id) {
				$stat = Yii::app()->db->createCommand("SELECT status FROM expenses WHERE id = ".$id." ")->queryScalar();	
				if($stat == 'Approved')	
				{					
					Yii::app()->db->createCommand("update expenses set status= 'Paid' where id=".$id." ")->execute();
					$model = $this->loadModel($id);
					self::CreateInvoice($model->id);
					$this->sendNotificationsEmails($model);
				}
			}
			if(!empty($users))
			{				
				$this->generatePdf('bankTransferAll',$users); 
				$file = Utils::getFileexpensesBankTr();	
			}else{
				$file='no';
			}

			echo json_encode(array(
            'status' => 'success',
            'url' => $file ,
        ));
        exit;
			 
		}	
	}

	public function actionmultipay(){
		if(!empty( $_POST['inv'])){
			$ids= $_POST['inv'];
			
			foreach ($ids as $key => $id) {
				$stat = Yii::app()->db->createCommand("SELECT status FROM expenses WHERE id = ".$id." ")->queryScalar();	
				if($stat == 'Approved')	
				{					
					Yii::app()->db->createCommand("update expenses set status= 'Paid' where id=".$id." ")->execute();
					$model = $this->loadModel($id);
					self::CreateInvoice($model->id);
					$this->sendNotificationsEmails($model);
				}
			}
			echo json_encode(array(
            'status' => 'success',
        ));
        exit;
			 
		}		
	}
	public function actionmultiApproval(){
		if(!empty( $_POST['inv'])){
			$ids= $_POST['inv']; 
			foreach ($ids as $key => $id) {
				$stat =Yii::app()->db->createCommand("SELECT status FROM expenses WHERE id = ".$id." ")->queryScalar();				
				if($stat == 'Submitted' )	
				{					
					Yii::app()->db->createCommand("update expenses set status= 'Approved' where id=".$id." ")->execute();
					$model = $this->loadModel($id);
					$this->sendNotificationsEmails($model);
				}
			}
			echo json_encode(array(
            'status' => 'success',
        ));
        exit;
			 
		}		
	}

	public function actionPrintBankTransfer($id){		
		$model = $this->loadModel((int) $id);
		$file = $model->getFileBankTransfer(true);
		if($file == null)
			$this->generatePdf('bankTransfer', $id);
		$file = $model->getFileBankTransfer(true);
		if ($file !== null){
			chmod($file, 0777);
			header('Content-disposition: attachment; filename=BANK_TRANSFER_'.$model->no.'.pdf');
			header('Content-type: application/pdf');			
			readfile($file);
			Yii::app()->end();			
		}else{
			$this->redirect(array(Utils::getMenuOrder(true)));
		}
	}
	public function actionDeleteUpload(){
		if (isset($_GET['model_id'], $_GET['file'])){
			$id = (int)$_GET['model_id'];
			if (isset($_GET['id_customer'])){
				$customer = (int)$_GET['id_customer'];
			}else {
				$customer = (int) Yii::app()->db->createCommand("SELECT customer_id FROM expenses WHERE id = $id")->queryScalar();				
			}
			$filepath = Expenses::getDirPath($customer, $id).$_GET['file'];
			$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
			if ($success){
				$query = "UPDATE `expenses` SET number_file=number_file-1 WHERE id='{$id}'";
				Yii::app()->db->createCommand($query)->execute();
				$query = "DELETE FROM `expenses_uploads`  WHERE expenses_id='{$id}' and file = '{$_GET['file']}'";
				Yii::app()->db->createCommand($query)->execute();
			}
		}
	}
	public function CreateInvoice($id_expenses)	{		
		$expenses = $this->loadModel((int) $id_expenses);
		$select = Yii::app()->db->createCommand("SELECT id FROM expenses_details WHERE expenses_id='$id_expenses' AND billable='Yes' LIMIT 1")->queryRow();
		$count = Yii::app()->db->createCommand("SELECT count(1) FROM invoices WHERE id_customer=".$expenses->customer_id." AND type = 'Expenses' and id_expenses = ".$id_expenses."" )->queryScalar();
		if($select != null && $count == 0 ){
			$title_name = "";	$model = new Invoices();	$model->invoice_number = "00000";	$model->id_customer = $expenses->customer_id;
			if($expenses->training == 1){
				$startdate=Utils::formatDate($expenses->startDate);
				$enddate=Utils::formatDate($expenses->endDate);		
				$model->project_name = Customers::getNameById($expenses->customer_id)." training From: ".$startdate." - To: ".$enddate;
				$model->id_project = "0";			
			}else{
				$model->project_name = $expenses->project->name;
				$model->id_project = $expenses->project_id;
			}
			$ea = $expenses->EaId;
			if($model->project_name != null){
				$title = Users::getUsername($expenses->user_id)." - Travel Expense Sheet associated with " .$model->project_name." from ".$expenses->startDate." to ".$expenses->endDate." - ".$expenses->no;
			}else{
				foreach ($ea->eItems as $name)
					$title_name .= $name['description']." ";
				$title = $title_name." - ".$cardinal_number." ". $payment['payment_term'] ."% Payment - ".Codelkups::getCodelkup($payment['milestone']);
			}

			$model->invoice_title = $title;	$model->id_ea = $ea['id'];	$model->currency = 9;
			$model->status = "To Print";		$model->payment = 0;	$model->payment_procente= 0;

			if(!empty($model->id_ea))
			{
				$cat= Yii::app()->db->createCommand("SELECT category FROM eas WHERE id=".$model->id_ea )->queryScalar();
			}else{
				$cat='';
			}
			$region=Yii::app()->db->createCommand("SELECT region, country from customers where id=".$model->id_customer." ")->queryRow(); 
			if($region['region'] =='59'){
				if($cat =='27' || $cat =='28'){
						if($region['country'] =='113' || $region['country']=='115'){
								$model->partner= Maintenance::PARTNER_SNS;
								$model->sns_share = 100;	
						}else {
								 $model->partner= '79' ; 
								 $model->sns_share = 80;
								 $model->partner_status='Not Paid';
								  }
			 }	else{
				 	$model->partner= Maintenance::PARTNER_SNS;
				 	$model->sns_share = 100;
				 }
			}elseif($region['region'] =='63') {
				{
					$model->partner= '201'; 
				}
				$model->sns_share = 80; 
				$model->partner_status='Not Paid';
			}else{
				$model->partner= Maintenance::PARTNER_SNSI;
				$model->sns_share = 80;
				$model->partner_status='Not Paid';
			}
			$model->sns_share = 100;
			$amount = Expenses::getNetAmountNoFormat($expenses,$model->currency);
			$model->amount = $amount['billable_amount'];	$model->net_amount = $amount['billable_amount'];
			$model->gross_amount = $amount['billable_amount'];		$model->id_expenses = $id_expenses;	$model->sold_by = ""; 
			$model->type = "Expenses";	
			$assigneduser= Yii::app()->db->createCommand("SELECT id_assigned from customers where id= ".$expenses->customer_id." ")->queryScalar();
			if ( !empty($assigneduser) && $assigneduser!= 0){
				$model->id_assigned=$assigneduser;
				$updateinvoices = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='".$assigneduser."' WHERE id_customer='".$expenses->customer_id."' and (id_assigned is null or id_assigned=0) ")->execute();
			}
			if($model->save(false)){				
					$sum = InvoicesExpenses::createInvoiceExpenses($model,$expenses);
					$model->amount = $sum;
					$model->net_amount = $sum;
					$model->gross_amount = $sum;
					$model->invoice_number = Utils::paddingCode($model->id);
					if($model->invoice_number == "99999"){
							$model->invoice_number = "00000";
					}			
					$model->save();
			}else{echo 'asd'; exit;}			
		}	
		return true;
	}
	public function actiongenerateTransfer(){
		$expenses=Yii::app()->db->createCommand("select  'MA' as ma, uhd.bank_account , '10' as ten ,  uhd.iban as IBAN ,sum(e.payable_amount) as amount  ,e.user_id as user from expenses e , user_hr_details uhd , user_personal_details ud where  e.user_id=uhd.id_user  and e.payable_amount>0  and  e.user_id=ud.id_user and   e.`status`='Paid' and (ud.branch in ('31', '689') or  e.user_id= 16) GROUP BY e.user_id  having  sum(e.payable_amount) > 0")->queryAll();
		self::CreateExcel($expenses);
	}
	public function CreateExcel($resp, $profit = null){
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("Seve Alex")
		->setLastModifiedBy("Seve Alex")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");
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
		$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('E1:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->setActiveSheetIndex($sheetId);
		$ct = 1;
		foreach($resp as $key=>$tim){
			if($tim['user'] != 16)
			{	$ma = $tim['ma'];
				$bank_account = (int)$tim['bank_account'];
				$bank_account = sprintf( '%06d', $bank_account );
				$ten = $tim['ten'];
				$iban = $tim['IBAN'];
			 	$amount = (double)($tim['amount']);
			 	$amount= number_format($amount, 2);
				$resource= $tim['user'];
				$objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':I'.$ct)->applyFromArray($styleArray1);				
				$objPHPExcel->getActiveSheet()
   				 ->getStyle('A'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
  				 $objPHPExcel->getActiveSheet()
   				 ->getStyle('B'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
  				 $objPHPExcel->getActiveSheet()
   				 ->getStyle('C'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
  				 $objPHPExcel->getActiveSheet()
   				 ->getStyle('D'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
  				 $objPHPExcel->getActiveSheet()
   				 ->getStyle('E'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
  				 $objPHPExcel->getActiveSheet()
   				 ->getStyle('F'.$ct)
  				 ->getNumberFormat()
  				 ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
						$objPHPExcel->setActiveSheetIndex($sheetId)						
						->setCellValue('A'.$ct, $ma)
						->setCellValue('B'.$ct, $bank_account)
						->setCellValue('C'.$ct, $ten)
						->setCellValue('D'.$ct, '' . $iban . ' ')
						->setCellValue('E'.$ct, $amount)
						->setCellValue('F'.$ct, Users::getNameById($resource));
						$ct=$ct+1;
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle('Bank_Transfer');
		$objPHPExcel->setActiveSheetIndex(0);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="BOB_Bank_Transfer.xls"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header ('Cache-Control: cache, must-revalidate');
			header ('Pragma: public');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');			
			$upexpenses=Yii::app()->db->createCommand("select distinct e.id as expense_id from expenses e , user_hr_details uhd , user_personal_details ud where e.user_id=uhd.id_user and  e.user_id=ud.id_user and   e.`status`='Paid' and (ud.branch in ('31', '689') or  e.user_id= 16)  and (select count(1) from expenses_details ed where ed.expenses_id=e.id and ed.payable='Yes') >0 ")->queryAll();
		foreach ($upexpenses as  $value) {
				$id=$value['expense_id']; 
				Yii::app()->db->createCommand("UPDATE expenses SET status = 'Transferred' WHERE id = $id ")->execute();
		}			
		exit;
	}
}?>