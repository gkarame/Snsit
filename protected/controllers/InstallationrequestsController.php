<?php
class InstallationRequestsController extends Controller{
	public $layout='//layouts/column1';
	public function filters(){
		return array(
			'accessControl', 
			'postOnly + delete', 
		);
	}
	public function init(){
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - Installation Requests');
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array('SendAssignedEmail','SendCreateEmail','SendProductdoneNoinfo','SendCompletedEmail','SendChangedEmail'),
				'users'=>array('*'),
			),

			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(
						'index','view','create','update', 'delete','deleteProduct','manageProduct','Assigned',
						'upload','deleteUpload','UpdateHeader','Createmodelinfo','GetExcel','updateinfoheader',
						'SaveIR','ChangeProductStatus'
						
				),
				 'expression'=>'!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actions(){
		 return array(
			 'upload'=>array(
				 'class'=>'xupload.actions.CustomXUploadAction',
				 'path' => Yii::app() -> getBasePath() . "/../uploads/tmp",
				 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads/tmp",
		 		 'stateVariable' => 'installationrequests_uploads'
			 ),
		 );
	}	
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('ir-general-installationrequests')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['InstallationRequests']) ?  $_GET['InstallationRequests'] : Utils::getSearchSession();		
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge( 
			$this->action_menu,
			array(
				'/installationrequests/index' => array(
					'label'=>Yii::t('translations','Installation Requests'),
					'url' => array('installationrequests/index'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => -1,
					'order' => Utils::getMenuOrder()+1,
					'search' => $searchArray,
				),
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model = new InstallationRequests('search');
		$model->unsetAttributes(); 
		$model->addProductID();
		if (isset($_GET['InstallationRequests'])){
			$model->attributes = $_GET['InstallationRequests'];
		}
		if (isset($_GET['InstallationRequests']['status'])){
			$model->status=$_GET['InstallationRequests']['status'];
		}
		if(isset($_GET['id_product'])){
			$model->addProductID($_GET['id_product']);
		}
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionView($id, $new =null){
		$id = (int)$id;
		$model = $this->loadModel($id);
		if($model->status == InstallationRequests::STATUS_COMPLETED)
			$infomodel = $this->loadInfoModel($model->id_info);		
		if ($new == 1) {
				Utils::closeTab(Yii::app()->createUrl('installationrequests/update/'.$id));
				$this->action_menu = Yii::app()->session['menu'];
		}
		$arr = Utils::getShortText($model->ir_number);
		$subtab = $this->getSubTab(Yii::app()->createUrl('installationrequests/view', array('id' => $id)));
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/installationrequests/view/'.$id => array(
						'label'=> "IR #".$arr['text'],
						'url' => array('installationrequests/view', 'id'=>$id),
						'itemOptions'=>array('class'=>'link','title' => $arr['shortened'] ? $model->ir_number : ''),
						'subtab' => $subtab,
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$this->render('view', array('model' => $model,'infomodel'=> (($model->status == InstallationRequests::STATUS_COMPLETED) ? $infomodel:'')));
	}
	public function actionCreate(){
		$error1 = false;	$error2 = false;$error3 = false;	$error_message = '';
		if(!GroupPermissions::checkPermissions('ir-general-installationrequests','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
			$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/installationrequests/create' => array(
					'label'=> 'New IR',
					'url' => array('installationrequests/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$this->jsConfig->current['activeTab'] = 0;
		$model = new InstallationRequests;
		$model->status = InstallationRequests::STATUS_INPROGRESS;
		$model->prerequisites = InstallationRequests::prerequisite_NO;
		$model->installation_location = InstallationRequests::INSTALL_LOCATION_REMOTE;
		$model->ir_number = "00000";
  		if(isset($_POST['InstallationRequests'])){
			try{				
				if(!isset($_POST['InstallationRequests']['expected_starting_date']) || $_POST['InstallationRequests']['expected_starting_date'] == '1970-01-01'){
					$model->addCustomError('expected_starting_date', 'Servers ready on date cannot be blank');					
				}
				if(!isset($_POST['InstallationRequests']['deadline_date']) || $_POST['InstallationRequests']['expected_starting_date'] == '1970-01-01'){
					$model->addCustomError('deadline_date', 'Delivery Date cannot be blank');					
				}			
				$model->attributes = $_POST['InstallationRequests'];
				$model->expected_starting_date =  date('Y-m-d',strtotime($_POST['InstallationRequests']['expected_starting_date']));
				$model->deadline_date =  date('Y-m-d',strtotime($_POST['InstallationRequests']['deadline_date']));
				$model->requested_by = Yii::app()->user->id;
				$model->assigned_to = 67;
				if($_POST['InstallationRequests']['installation_locally'] == InstallationRequests::LOCALLY_CUSTOMER_SERVERS && $_POST['InstallationRequests']['installation_location'] == InstallationRequests::INSTALL_LOCATION_REMOTE){
					if($model->customer_contact_name == null){						
					$model->addCustomError('customer_contact_name', 'Contact name cannot be blank');
					}
					if($model->customer_contact_email == null){						
						$model->addCustomError('customer_contact_email', 'Contact email cannot be blank');
					}
					if($model->customer != null){
					$conn = Yii::app()->db->createCommand("select id from connections where id_customer =". $model->customer)->queryAll();
					if(! $conn){
						$error1 =true;
					}
					}
				}
                /*
                * Author: Mike
                * Date: 12.07.19
                * Under Environment add a 3rd Radio Button: Hosted  -  When Clicked it shows in addition to Customer Contact Name and Email, Hosting Contact Name & Email   On different note, add inside the IR, under Authentication drop don list: Hybrid
                */
				if ($_POST['InstallationRequests']['installation_locally'] == InstallationRequests::HOSTED){
                    if($model->hosting_contact_name == null){
                        $model->addCustomError('hosting_contact_name', 'Contact hosting name cannot be blank');
                    }
                    if($model->hosting_contact_email == null){
                        $model->addCustomError('hosting_contact_email', 'Contact hosting email cannot be blank');
                    }
                }
				if($_POST['InstallationRequests']['installation_locally'] == InstallationRequests::LOCALLY_LOCALLY){
					$model->installation_location = null;
				}else{
					$model->installation_location = InstallationRequests::INSTALL_LOCATION_REMOTE;
				}

                if ((int)$_POST['source_type'] === 1){
                    $maintenance_services = Yii::app()->db->createCommand("SELECT * FROM `maintenance_services` where id_contract={$model->project} AND id_service='15'")->queryRow();
                    if (!empty($maintenance_services)){
                        $actuals = MaintenanceServices::getActual($maintenance_services['id'],15,'1');
                        if ((int)$maintenance_services['limit'] <= (int)$actuals){
                            $model->addError('project','Sorry, you don’t have any free IR in  selected support plan');
                            $error3 = true;
                        }
                    }else{
                        $model->addError('project','Sorry, you don’t have any free IR in  selected support plan');
                        $error3 = true;
                    }
                    $model->project = $model->project.'m';
                }
				if(substr($model->project, -1) == 'm'){
					$model->maintenance = 1;	
				}else{
					$model->maintenance = 0;
				}
				if(!$error1 && !$error2 && !$error3){
				if($model->validate()){
					if($model->save()){
					$model->ir_number = Utils::paddingCode($model->id);
					$model->save();
					$this->redirect(array('installationrequests/update', 'id'=>$model->id, 'new' => 1));
					}
				}
			 }
			}
			catch(Exception $e){
			    throw $e;
			}
		}
		$this->render('create',array('model'=>$model,'error1' => $error1,'error2' => $error2));
	}
	public function actionUpdate($id, $new = 0, $idprd = null){
		if(!(GroupPermissions::checkPermissions('ir-general-installationrequests','write'))){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$id = (int) $id;	$model = $this->loadModel($id);	$extra = array();
		$error = false;
		if (isset(Yii::app()->session['menu']) && $new == 1) {
				Utils::closeTab(Yii::app()->createUrl('installationrequests/create/'));
				$this->action_menu = Yii::app()->session['menu'];
		}
		if(isset(Yii::app()->session['menu']) && $new == 2){
			Utils::closeTab(Yii::app()->createUrl('installationrequests/createmodelinfo/'.$idprd));
			$this->action_menu = Yii::app()->session['menu'];
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/installationrequests/update/'.$id => array(
						'label'=> 'IR #'.$model->ir_number,
						'url' => array('installationrequests/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		if (isset($_POST['submitid'])){
			$prodCount=Yii::app()->db->createCommand("SELECT count(*) FROM installation_requests_products WHERE id_ir = ".$_POST['submitid']." ")->queryScalar();
		if ($prodCount == 0){
				$error = 1;
				$extra['error'] = 'No IR Products added.';
			}
			if($error != 1){
				if($new == 1){
					self::SendCreateEmail($model->id);
				}
			echo json_encode(array_merge(array('status'=>'saved')));
			Yii::app()->end();
		}else{
			echo json_encode(array_merge(array('status'=>'fail','message'=>$extra['error'])));
			Yii::app()->end();
		}			
		}
		$this->render('update', array('model' => $model, 'new' => $new,
		'can_modify' => $model->isEditable()));		
	}
	public function actionUpdateinfoheader($id){
		$id = (int) $id;
		$model =  InstallationRequestsInfo::model()->findByPk($id);
		if(isset($_POST['InstallationRequestsInfo'])){
			$model->attributes = $_POST['InstallationRequestsInfo'];
			if($model->validate()){
				$model->save();
				echo json_encode(array_merge(array(
					'status' => 'saved',					
					'html' => $this->renderPartial('_header_content_info', array('model' => $model), true, false)
					)));
					exit();
			}
		}
		echo json_encode(array_merge(array(
						'status' => 'success',					
						'html' => $this->renderPartial('_edit_header_content_info', array('model' => $model), true, false)
				)));
		exit();
	}
	public function actionUpdateHeader($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$redirect = 0;
		$error = false;
		$create = false;
		if(isset($_POST['InstallationRequests'])){
			if(!$model->isEditable()){
				if(isset($_POST['InstallationRequests']['notes'])){
				$model->notes = $_POST['InstallationRequests']['notes'];
				$extra['info'][] = "Only Notes are saved";
				}
			}else{
				 	$initialAssigned= $model->assigned_to;
				 	$model->attributes = $_POST['InstallationRequests'];

				 	if ( isset($_POST['InstallationRequests']['assigned_to']) && $initialAssigned != $model->assigned_to)
				 	{
						self::SendReAssignedEmail($model->id, $_POST['InstallationRequests']['assigned_to']);
				 	} 
					if(isset($_POST['InstallationRequests']['installation_locally']) && $_POST['InstallationRequests']['installation_locally']==0){
							$model->installation_location = null;
						}
					$extra['info'][] = "Saved !";
			}
            if ((int)$_POST['source_type'] === 1){
                $maintenance_services = Yii::app()->db->createCommand("SELECT * FROM `maintenance_services` where id_contract={$model->project} AND id_service='15'")->queryRow();
                if (!empty($maintenance_services)){
                    $actuals = MaintenanceServices::getActual($maintenance_services['id'],15,'1');
                    if ((int)$maintenance_services['limit'] <= (int)$actuals){
                        echo 'false';
                        die();
                    }
                }else{
                    echo 'false';
                    die();
                }
                $model->project = $model->project.'m';
            }
			if(substr($model->project, -1) == 'm'){
					$model->maintenance = 1;	
				}else{
					$model->maintenance = 0;
				}
				if($model->validate()){
					if($redirect == 0){
					$model->save();
					echo json_encode(array_merge(array(
					'status' => 'saved',					
					'can_modify' => $model->isEditable(),
					'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
					), $extra));
					exit();
					}else{
					echo json_encode(array_merge(array(
					'status' => 'redirect',					
					'can_modify' => $model->isEditable(),
					'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
					), $extra));
					exit();
					}
					}			
		}
		echo json_encode(array_merge(array(
						'status' => 'success',					
						'can_modify' => $model->isEditable(),
						'html' => $this->renderPartial('_edit_header_content', array('model' => $model), true, false)
				), $extra));
		exit();
	}
	public function loadModel($id){
		$model = InstallationRequests::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function loadInfoModel($id){
		$model = InstallationRequestsInfo::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function actiondeleteProduct($id){
		$id = (int) $id;
		Yii::app()->db->createCommand("DELETE FROM installation_requests_products WHERE id='{$id}'")->execute();
	}
	/*
	 * Author: Mike
	 * Date: 17.06.19
	 * remove the closure notification email and on the last product closure on the IR include in the subject IR# is now closed
	 */
	public function actionmanageProduct($id = NULL){
		$new = false;
		if (!isset($_POST['id_ir']))
    		exit;
		if($id == null){
			$new = true;	$model = new InstallationrequestsProducts;
			$model->id_ir = (int)$_POST['id_ir'];
			$model->status = InstallationrequestsProducts::STATUS_PENDING;
		}else{
			$id = (int)$id;
    		$model = InstallationrequestsProducts::model()->findByPk($id);
		}
		if(isset($_POST['InstallationrequestsProducts']))
		{
			if ($id == NULL) {
    			$model->attributes = $_POST['InstallationrequestsProducts']['new'];    		
    		} else {
				if(!empty($_POST['InstallationrequestsProducts'][$id]['id_product'])){
					$model->attributes = $_POST['InstallationrequestsProducts'][$id];
				}else	{
					$model->attributes = $_POST['InstallationrequestsProducts'][$id];
				}				
    		}
    		if($model->id_product== 64){
    			if( $model->db_collation == null)
    				$model->addCustomError('db_collation','DB collation cannot be empty');
    			if($model->number_of_nodes == null)
    				$model->addCustomError('number_of_nodes','# of Nodes cannot be empty');
    			if($model->number_of_schemas == null)
    				$model->addCustomError('number_of_schemas','# of Schemas cannot be empty');
    			if($model->authentication == null)
    				$model->addCustomError('authentication','Authentication type cannot be empty');
    			if($model->reporting_type == null)
    				$model->addCustomError('reporting_type','Reporting type cannot be empty');
    			if($model->language_pack == null)
    				$model->addCustomError('language_pack','Language pack cannot be empty');
    			if($model->license_type == null)
    				$model->addCustomError('license_type','license type cannot be empty');
    		}
    	if($model->validate()){
    		$model->save();
			if ($model->status == 1){
				if ($model->id_product ==416 || $model->id_product ==679 )	{
					$id_user = Yii::app()->user->id;
					if(!empty($id_user)){
						self::SendProductdoneNoinfo($model->id_ir,  $model->id_product ,$id_user);				
					}
					$closed = Yii::app()->db->createCommand("select (total = finished) from(
						(select count(*) as total from installation_requests_products where id_ir = ".$model->id_ir.") as q1
		,
					(select count(*) as finished from installation_requests_products where id_ir = ".$model->id_ir." and status =".InstallationrequestsProducts::STATUS_CLOSED.") as q2)")->queryScalar();
				if($closed != 0){
					$update_ir = Yii::app()->db->createCommand("update installation_requests set status =".InstallationRequests::STATUS_COMPLETED." where id = ".$model->id_ir)->execute();
					if(!empty($id_user)) {
                        $ir_products = InstallationrequestsProducts::model()->findAll("id_ir=:id_ir",array(':id_ir' => $model->id_ir));
                        foreach ($ir_products as $product){
                            self::SendProductdone((int)$model->id_ir,(int)$product->id,(int)$product->id_product,(int)$id_user);
                        }
					}				
				}
					echo json_encode(array('status' => 'saved'));
			       	exit;
				}else{
					echo json_encode(array('status' => 'redirect'));
					exit;
				}
			} else if ($model->status == 2){
					$closed = Yii::app()->db->createCommand("select (total = finished) from(
						(select count(*) as total from installation_requests_products where id_ir = ".$model->id_ir.") as q1
		,
					(select count(*) as finished from installation_requests_products where id_ir = ".$model->id_ir." and status =2) as q2)")->queryScalar();
				if($closed != 0){
					$update_ir = Yii::app()->db->createCommand("update installation_requests set status =4 where id = ".$model->id_ir)->execute();
					}
			}		
    		echo json_encode(array('status' => 'saved'));
			exit;
    	}
}
    	Yii::app()->clientScript->scriptMap=array(
        	'jquery.js'=>false,
    		'jquery.min.js' => false,
    		'jquery-ui.min.js' => false,
		);
    	echo json_encode(array('status'=>'success', 'form'=>$this->renderPartial('_product_form', array(
            	'model'=> $model,
        	), true, true)));			
       exit;
	}
	public function actionDeleteUpload(){	
		 if($_GET['id'] != null){				
				$id_ir = $_GET['id'];
				$model = $this->loadModel($id_ir);
				$filepath = InstallationRequests::getDirPath($_GET['customer'], $id_ir).$_GET['filename'];
				$success = is_file( $filepath ) && $filepath !== '.' && unlink( $filepath );
				if ($success){
					$id_att_ir = Yii::app()->db->createCommand("SELECT id FROM installation_requests_attachments WHERE id_ir = {$id_ir} AND filename ='{$_GET['filename']}' ")->queryScalar();
					if($id_att_ir != null){
						$nr = Yii::app()->db->createCommand("Delete from installation_requests_attachments where id = {$id_att_ir}")->execute();
						echo json_encode(array ('status' =>'success', true, false));
					}
				}
		}
	}
	public function actionAssigned() {
		$id = (int)$_POST['id_ir'];		
		$model = InstallationRequests::model()->findByPk($id);		
		$model->assigned_to = $_POST['value'];			
	    if ($model->save()){
	    	if($model->assigned_to !=null)
	    		self::SendAssignedEmail($model->id);
			echo json_encode(array_merge(array('status'=>'success')));
			exit;
	    }
	    echo json_encode(array('status'=>'failure', 'error'=>$model->getErrors()));
		exit;
	}
    /*
     * Author: Mike
     * Date: 17.06.19
     * remove the closure notification email and on the last product closure on the IR include in the subject IR# is now closed
     */
	public function actionChangeProductStatus(){
		$error = 0;	$error_message = '';	$id = (int) $_POST['id'];
		$id_user = Yii::app()->user->id;
		$status = (int) $_POST['status'];
		$redirect=0;
		$model = InstallationrequestsProducts::model()->findByPk($id);
		if($model->id_product !=416 && $model->id_product !=679 && $status == 1){
			$redirect=1;
		}else if ($model->id_product == 28) {
			$info = Yii::app()->db->createCommand("select count(*) from installation_requests_info where id_irprd = ".$model->id)->queryScalar();
			if($info == 0){
				$error = 1;
				$error_message = "You can't close a product until you enter installation information";
			}			
		}
		if($error == 0){
		$model->status = $status;
		if($model->save()){
				$cancelled = Yii::app()->db->createCommand("select (total = finished) from(
						(select count(*) as total from installation_requests_products where id_ir = ".$model->id_ir.") as q1,
					(select count(*) as finished from installation_requests_products where id_ir = ".$model->id_ir." and status =2) as q2)")->queryScalar();
				if($cancelled != 0){
					$update_ircancelled = Yii::app()->db->createCommand("update installation_requests set status =4 where id = ".$model->id_ir)->execute();
				}		 
			$closed = Yii::app()->db->createCommand("select (total = finished) from(
						(select count(*) as total from installation_requests_products where id_ir = ".$model->id_ir.") as q1,
					(select count(*) as finished from installation_requests_products where id_ir = ".$model->id_ir." and status =".InstallationrequestsProducts::STATUS_CLOSED.") as q2)")->queryScalar();
			if($closed != 0){					
					$update_ir = Yii::app()->db->createCommand("update installation_requests set status =".InstallationRequests::STATUS_COMPLETED." where id = ".$model->id_ir)->execute();
					if(!empty($id_user)) {
					    $ir_products = InstallationrequestsProducts::model()->findAll("id_ir=:id_ir",array(':id_ir' => $model->id_ir));
					    foreach ($ir_products as $product){
					        self::SendProductdone((int)$model->id_ir,(int)$product->id,(int)$product->id_product,(int)$id_user);
                        }
					}
				}
			if ($redirect == 0)	{
				echo json_encode(array_merge(array('status'=>'success')));
				exit;
				}else{
						echo json_encode(array('status' => 'redirect', 'id'=> $model->id));
						exit; 
				}			
			}
		}
		echo json_encode(array('status'=>'failure', 'message'=>$error_message));
		exit;
	}
	public function SendReAssignedEmail($id, $new){
			$model = self::loadModel($id);
			$subject = "Assignment of Installation Request # ".$model->ir_number;
			$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$new)->queryScalar();
			$products = Yii::app()->db->createCommand('
			select codelkup from codelkups c join installation_requests_products irp on irp.id_product =  c.id 
			where  irp.id_ir = '.$model->id)->queryAll();	$prd_txt = "";
			foreach($products as $prod){
					$prd_txt.= "<li>".$prod['codelkup']."</li>";	
					}

			$adminemails = Yii::app()->db->createCommand("select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
			$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
			if($model->maintenance == 0)
			{
				$str="<br/>Project: <b>".Projects::getNameById($model->project)."</b><br>";
			}else{
				$str="<br/>Contract: <b>".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</b><br>";
			}
				 
			$body = "Dear ".Users::getNameById($new).",<br/>
					You have been assigned to complete Installation Request # <a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> with the following details: <br><br/>
					Customer: <b>".$model->eCustomer->name."</b><br/> ".$str."
					Created by: <b>".$model->eRequested_by->fullname."</b><br/>
					Servers ready on: <b>".date('d/m/Y', strtotime($model->expected_starting_date))."</b><br/>
					Delivery date: <b>".date('d/m/Y', strtotime($model->deadline_date))."</b><br/> 
					Notes: ".$model->notes."<br/>
					The list for products required for this installation:<br/><b><ul>".
					$prd_txt
					."</ul> </b><br/>
					Best Regards";
			Yii::app()->mailer->ClearAddresses();			
			if (!empty($instemail))	{
					Yii::app()->mailer->AddAddress($instemail);
			}
		foreach ($adminemails as $email) {
			if (!empty($email['email'])){
				Yii::app()->mailer->AddAddress($email['email']);
			}			
		}
		if($model->maintenance == 0)
		{
				$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();			
				$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select business_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
				if (!empty($pm_email)){
					Yii::app()->mailer->AddAddress($pm_email);
				}
				if (!empty($bm_email)){
					Yii::app()->mailer->AddAddress($bm_email);
				}	
		}
		
		if (!empty($requesteedby_email)){
			Yii::app()->mailer->AddAddress($requesteedby_email);
		}
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){				
			}
		}
		public function SendAssignedEmail($id){
			$model = self::loadModel($id);
			$subject = "Assignment of Installation Request # ".$model->ir_number;
			$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->assigned_to)->queryScalar();
			$products = Yii::app()->db->createCommand('
			select codelkup from codelkups c join installation_requests_products irp on irp.id_product =  c.id 
			where  irp.id_ir = '.$model->id)->queryAll();
			$prd_txt = "";
			foreach($products as $prod){
					$prd_txt.= "<li>".$prod['codelkup']."</li>";	
					}

			$adminemails = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
			$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
			if($model->maintenance == 0)
			{
				$str="<br/>Project: <b>".Projects::getNameById($model->project)."</b><br>";
			}else{
				$str="<br/>Contract: <b>".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</b><br>";
			}
			$body = "Dear ".Users::getNameById($model->assigned_to).",<br/>
					You have been assigned to complete Installation Request # <a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> with the following details: <br><br/>
					Customer: <b>".$model->eCustomer->name."</b><br/> ".$str."
					Created by: <b>".$model->eRequested_by->fullname."</b><br/>
					Servers ready on: <b>".date('d/m/Y', strtotime($model->expected_starting_date))."</b><br/>
					Delivery date: <b>".date('d/m/Y', strtotime($model->deadline_date))."</b><br/> 
					Notes: ".$model->notes."<br/>
					The list for products required for this installation:<br/><b><ul>".
					$prd_txt
					."</ul> </b><br/>
					Best Regards";
			Yii::app()->mailer->ClearAddresses();			
			if (!empty($instemail)){
					Yii::app()->mailer->AddAddress($instemail);
				}
		foreach ($adminemails as $email) {
			if (!empty($email['email'])){
				Yii::app()->mailer->AddAddress($email['email']);
			}			
		}
		if($model->maintenance == 0)
		{
			$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
						select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();			
			$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
						select business_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			if (!empty($pm_email)){
				Yii::app()->mailer->AddAddress($pm_email);
			}
			if (!empty($bm_email)){
				Yii::app()->mailer->AddAddress($bm_email);
			}		
		}	
		
		if (!empty($requesteedby_email)){
			Yii::app()->mailer->AddAddress($requesteedby_email);
		}
		Yii::app()->mailer->Subject  = $subject;
		Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
		if (Yii::app()->mailer->Send(true)){				
		}
	}
	public function SendCreateEmail($id){
			$notif = EmailNotifications::getNotificationByUniqueName('ir_new');
			$model = self::loadModel($id);
			if($notif != null){
				if 	(EmailNotifications::isNotificationSent($model->id, 'installationrequests', $notif['id']) == false) {
					$subject = "New Installation Request # ".$model->ir_number;
					$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->assigned_to)->queryScalar();
					$products = Yii::app()->db->createCommand('
					select codelkup from codelkups c 
					join installation_requests_products irp on irp.id_product =  c.id
					where  irp.id_ir = '.$model->id)->queryAll();
					$prd_txt = "";
					foreach($products as $prod){
						if ($prod['codelkup'] == 'Infor WMS'){
							$vers_wms= InstallationrequestsProducts::getswversion($id, 64);							
							if (!empty($vers_wms) && $vers_wms!=""){
								$prd_txt.= "<li>".$prod['codelkup']." - Version: ". Codelkups::getCodelkup($vers_wms)." </li>";
							}else{
								$prd_txt.= "<li>".$prod['codelkup']."</li>";
							}
						}else{
							$prd_txt.= "<li>".$prod['codelkup']."</li>";
						}							
					}
					$adminemails = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
					$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
					if($model->maintenance == 0)
					{
						$str="<br/>Project: <b>".Projects::getNameById($model->project)."</b><br>";
					}else{
						$str="<br/>Contract: <b>".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</b><br>";
					}
					$body = "Dear ".Users::getNameById($model->assigned_to).",<br/>
							A New Installation Request # <a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> is created with the following details: <br><br/>
							Customer: <b>".$model->eCustomer->name."</b><br/> ".$str."
							Created by: <b>".$model->eRequested_by->fullname."</b><br/>
							Servers ready on: <b>".date('d/m/Y', strtotime($model->expected_starting_date))."</b><br/>
							Environment: <b>".$model->getlocallycustomerlabel($model->installation_locally)."</b><br/>
							Delivery date: <b>".date('d/m/Y', strtotime($model->deadline_date))."</b><br/> 
							Notes: ".$model->notes."<br/>
							The list for products required for this installation:<br/><b><ul>".
							$prd_txt
							."</ul> </b><br/>
							Best Regards";
				Yii::app()->mailer->ClearAddresses();
				if (!empty($instemail)){
					Yii::app()->mailer->AddAddress($instemail);
				}
				foreach ($adminemails as $email) {
					if (!empty($email['email'])){
						Yii::app()->mailer->AddAddress($email['email']);
					}					
				}
				if (!empty($requesteedby_email)){
					Yii::app()->mailer->AddAddress($requesteedby_email);
				}
				if($model->maintenance == 0)
				{
					$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
								select project_manager from projects join installation_requests on
								projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();					
					$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
								select business_manager from projects join installation_requests on
								projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();					
					if (!empty($bm_email)){
						Yii::app()->mailer->AddAddress($bm_email);
					}
					if (!empty($pm_email)){
						Yii::app()->mailer->AddAddress($pm_email);
					}
				}
				
				$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
				foreach($emails as $email) {
						if (!empty($email)){
							Yii::app()->mailer->AddAddress($email);
						}						
					}		
					Yii::app()->mailer->Subject  = $subject;
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
					if (Yii::app()->mailer->Send(true)){
						EmailNotifications::saveSentNotification($model->id, 'installationrequests', $notif['id']);
					}
				}
			}
		}
		public function SendProductdoneNoinfo($id, $prd,$iduser){
			$notif = EmailNotifications::getNotificationByUniqueName('ir_product');
			$model = self::loadModel($id);
			if($notif != null){				
				$subject = "Installation Request #".$model->ir_number." - ".Codelkups::getCodelkup($prd)."";
				$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->assigned_to)->queryScalar();
				if($model->maintenance == 0)
				{
					$pm_num = Yii::app()->db->createCommand("select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id)->queryScalar();
				}else{
					$pm_num = $model->requested_by;
				}
								
				$adminemails = Yii::app()->db->createCommand("select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
				$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
				$closed_by = Users::getUsername($iduser);
				$prd_txt="";
				if($model->maintenance == 0)
				{
					$str="<li><b> Project:</b> ".Projects::getNameById($model->project)."</li>";
				}else{
					$str="<li><b> Contract:</b> ".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</li>";
				}
				$prd_txt.="<li><b> Customer:</b> ".$model->eCustomer->name." </li>".$str."<li><b> Environment:</b> ".$model->getlocallycustomerlabel($model->installation_locally)."</li><li><b> Created by:</b> ".$model->eRequested_by->fullname."</li><br/>";
				$body = "Dear ".Users::getNameById($pm_num).",<br/>
						Installation Request #<a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> product: <b>".Codelkups::getCodelkup($prd)."</b> is ready.
						<br/>".$prd_txt."
						Best Regards,<br/>SNSit Team";
				Yii::app()->mailer->ClearAddresses();	
			if (!empty($instemail)){
				Yii::app()->mailer->AddAddress($instemail);
			}
			foreach ($adminemails as $email) {
			if (!empty($email['email'])){
				Yii::app()->mailer->AddAddress($email['email']);
			}			
		}
		if($model->maintenance == 0)
		{
			$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select business_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			if (!empty($pm_email)){
				Yii::app()->mailer->AddAddress($pm_email);
			}
			if (!empty($bm_email)){
				Yii::app()->mailer->AddAddress($bm_email);
			}
		}
			
			if (!empty($requesteedby_email)){
				Yii::app()->mailer->AddAddress($requesteedby_email);
			}
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach($emails as $email) {
				if (!empty($email))
						Yii::app()->mailer->AddAddress($email);
			}	
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
	public function SendProductdone($id, $id_prd, $prd,$iduser){
			$notif = EmailNotifications::getNotificationByUniqueName('ir_product');
			$model = self::loadModel($id);
			if($notif != null){				
				$subject = "Installation Request #".$model->ir_number." - ".Codelkups::getCodelkup($prd)."";
				$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->assigned_to)->queryScalar();
				$product = Yii::app()->db->createCommand("SELECT * FROM installation_requests_info where id_irprd=".$id_prd."")->queryRow();
				$prd_txt = "";
				if($model->maintenance == 0)
				{
					$str ="<li><b> Project:</b> ".Projects::getNameById($model->project)."</li>";
				}else{
					$str ="<li><b> Contract:</b> ".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</li>";
				}
				$prd_txt.="<li><b> Customer: </b>".$model->eCustomer->name." </li>".$str."<li><b> Environment:</b> ".$model->getlocallycustomerlabel($model->installation_locally)."</li><li><b>Created by:</b> ".$model->eRequested_by->fullname."</li>";
				if (!empty($product)){
						$prd_txt.= "<li><b> APP URL:</b> ".$product['app_url']."</li><li><b> APP Server Hostname:</b> ".$product['app_server_hostname']."</li><li><b> APP Username:</b> ".$product['app_username']." -<b> APP Password:</b> ".$product['app_username']."</li>";
						$prd_txt.="<li><b> DB Server Hostname:</b> ".$product['db_server_hostname']."</li><li><b> DB Name:</b> ".$product['db_name']."</li><li><b> DB Username:</b> ".$product['db_username']." -<b> DB Password:</b> ".$product['db_password']."</li>";	
						$prd_txt.="<li><b> DB Backup Path:</b> ".$product['db_local_bkup']."</li><li><b> Infor Backup Path:</b> ".$product['infor_local_bkup']."</li><li><b> License Type:</b> ".InstallationrequestsProducts::getLicenseLabel($product['license_type'])."</li>";
				if($model->maintenance == 0)
				{		
					$pm_num = Yii::app()->db->createCommand("select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id)->queryScalar();
				}else{
					$pm_num = $model->requested_by;
				}
								
				$adminemails = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
				$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
				$closed_by = Users::getUsername($iduser);
				$body = "Dear ".Users::getNameById($pm_num).",<br/>
						Installation Request #<a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> product: <b>".Codelkups::getCodelkup($prd)."</b> is ready. Kindly find below its details: <br/>
						<ul>".
						$prd_txt
						."</ul> 
						Best Regards,<br/>SNSit Team";
				Yii::app()->mailer->ClearAddresses();	
		if (!empty($instemail)){
				Yii::app()->mailer->AddAddress($instemail);
			}
			foreach ($adminemails as $email) {
			if (!empty($email['email']))	{
				Yii::app()->mailer->AddAddress($email['email']);
			}			
		}
		if($model->maintenance == 0)
		{
			$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select project_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
							select business_manager from projects join installation_requests on
							projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			if (!empty($pm_email)){
				Yii::app()->mailer->AddAddress($pm_email);
			}
			if (!empty($bm_email))	{
				Yii::app()->mailer->AddAddress($bm_email);
			}
		}
			
			if (!empty($requesteedby_email)){
				Yii::app()->mailer->AddAddress($requesteedby_email);
			}				
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach($emails as $email) {
				if (!empty($email))
					Yii::app()->mailer->AddAddress($email);
			}	
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			Yii::app()->mailer->Send(true);
		}
	}
}
		public function SendCompletedEmail($id,$iduser){
			$notif = EmailNotifications::getNotificationByUniqueName('ir_closed');
			$model = self::loadModel($id);
			if($notif != null){
				if 	(EmailNotifications::isNotificationSent($model->id, 'installationrequests', $notif['id']) == false) {
			$subject = "Installation Request # ".$model->ir_number." is Closed";
			$instemail = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->assigned_to)->queryScalar();
			$products = Yii::app()->db->createCommand('
			select c.id as code, c.codelkup, irp.id from codelkups c join installation_requests_products irp on irp.id_product =  c.id 
			where  irp.id_ir = '.$model->id)->queryAll();
			$prd_txt = "";
			foreach($products as $prod){
				$licen='';
				if ($prod['code'] == 64)	{
					$licen=' - License: '.InstallationrequestsProducts:: getLicensePerProduct($prod['id']);
				}
				$prd_txt.= "<li>".$prod['codelkup'].$licen."</li>";	
			}
			if($model->maintenance == 0)
			{	
				$pm_num = Yii::app()->db->createCommand("select project_manager from projects join installation_requests on
						projects.id = installation_requests.project where installation_requests.id =".$model->id)->queryScalar();
			}else{
				$pm_num = $model->requested_by;
			}			
			$adminemails = Yii::app()->db->createCommand("select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
														where user_groups.id_group in (18,25) and (select u.id from users u where u.id=user_personal_details.id_user and u.active=1 ) >0")->queryAll();
			$requesteedby_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=".$model->requested_by)->queryScalar();
			$closed_by = Users::getUsername($iduser);
			if($model->maintenance == 0)
			{
				$str= "<br/>Project: <b>".Projects::getNameById($model->project)."</b><br/>";
			}else{
				$str= "<br/>Contract: <b>".Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))."</b><br/>";
			}
			$body = "Dear ".Users::getNameById($pm_num).",<br/>
					Installation Request # <a href='".Yii::app()->createAbsoluteUrl("installationrequests/update", array("id" => $model->id))."' >".$model->ir_number."</a> is Closed with the following details: <br><br/>
					Customer: <b>".$model->eCustomer->name."</b><br/> ".$str."
					Environment: <b>".$model->getlocallycustomerlabel($model->installation_locally)."</b><br/>
					Created by: <b>".$model->eRequested_by->fullname."</b><br/>
					Servers ready on: <b>".date('d/m/Y', strtotime($model->expected_starting_date))."</b><br/>
					Delivery date: <b>".date('d/m/Y', strtotime($model->deadline_date))."</b><br/> 
					Notes: ".$model->notes."<br/>
					The user who closed the IR is: ".$closed_by."<br/>
					The list for products required for this installation:<br/><b><ul>".
					$prd_txt
					."</ul> </b><br/>
					Best Regards";
			Yii::app()->mailer->ClearAddresses();		
			Yii::app()->mailer->AddAddress($instemail);
		foreach ($adminemails as $email) {
			if (!empty($email['email'])){
				Yii::app()->mailer->AddAddress($email['email']);
			}			
		}
		if($model->maintenance == 0)
		{
			$pm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
						select project_manager from projects join installation_requests on
						projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			$bm_email = Yii::app()->db->createCommand("select email from user_personal_details where id_user=(
						select business_manager from projects join installation_requests on
						projects.id = installation_requests.project where installation_requests.id =".$model->id.")")->queryScalar();
			if (!empty($pm_email)){
				Yii::app()->mailer->AddAddress($pm_email);
			}
			if (!empty($bm_email)){
				Yii::app()->mailer->AddAddress($bm_email);
			}
		}
			
			if (!empty($requesteedby_email)){
				Yii::app()->mailer->AddAddress($requesteedby_email);
			}			
			$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
			foreach($emails as $email) {
				if (!empty($email))
				{	Yii::app()->mailer->AddAddress($email);	}
			}			
			Yii::app()->mailer->Subject  = $subject;
			Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>".nl2br($body)."</div>");
			if (Yii::app()->mailer->Send(true)){
				EmailNotifications::saveSentNotification($model->id, 'installationrequests', $notif['id']);
			}
		}
	}
}

    /*
     * Author: Mike
     * Date: 17.06.19
     * remove the closure notification email and on the last product closure on the IR include in the subject IR# is now closed
     */
	public function actionCreatemodelinfo($id = null){
		if(!(GroupPermissions::checkPermissions('ir-general-installationrequests','write'))){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		if($id == null){
			exit();
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/installationrequests/createmodelinfo/'.$id => array(
						'label'=> 'IR Product Info',
						'url' => array('installationrequests/createmodelinfo', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => '',
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$new = false;	$id_irprd= $id;
		$id_ir = Yii::app()->db->createCommand("select id_ir from installation_requests_products where id =".$id_irprd." ")->queryScalar();
		$prd = Yii::app()->db->createCommand("select id_product from installation_requests_products where id =".$id_irprd."  ")->queryScalar();
		$idinfo = Yii::app()->db->createCommand("select id from installation_requests_info where id_irprd =".$id_irprd)->queryScalar();
		if($idinfo == null){
			$new = true;
			$model = new InstallationRequestsInfo;
			$model->id_irprd = (int) $id_irprd;	
		}else{
			$model = InstallationRequestsInfo::model()->findByPk($idinfo);
		}		
		if(isset($_POST['InstallationRequestsInfo'])){
			try	{
				$model->attributes = $_POST['InstallationRequestsInfo'];				
				if($model->validate()){
					if($model->save()){
					$id_user = Yii::app()->user->id;
     /*
     * Author: Mike
     * Date: 10.07.19
     * remove the closure notification email and on the last product closure on the IR include in the subject IR# is now closed
     */
						//self::SendProductdone($id_ir, $id_irprd, $prd,$id_user);
						$closed = Yii::app()->db->createCommand("select (total = finished) from(
						(select count(*) as total from installation_requests_products where id_ir = ".$id_ir.") as q1,
					(select count(*) as finished from installation_requests_products where id_ir = ".$id_ir." and status =".InstallationrequestsProducts::STATUS_CLOSED.") as q2)")->queryScalar();
					if($closed != 0 && $closed != 1){
					$update_ir = Yii::app()->db->createCommand("update installation_requests set status =".InstallationRequests::STATUS_COMPLETED." where id = ".$id_ir)->execute();
					self::SendCompletedEmail($id_ir,$id_user);			
					}
					$this->redirect(array('installationrequests/update', 'id'=> $id_ir,'new'=>2,'idprd'=>$id));
					}
				}			  
			}
			catch(Exception $e){
			    throw $e;
			}
		}
		$this->render('createmodelinfo',array('model'=>$model));
	}
	public function actionGetExcel()
	{
		$model = new InstallationRequests('getAll');
		$data = $model->getAll(null, true)->getData();	
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("SNS IRs Export");		
		$sheetId = 0;

		 $nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($styleArray); 


		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Request #')
		->setCellValue('B1', 'Customer')
		->setCellValue('C1', 'Project')
		->setCellValue('D1', 'Deadline Date')
		->setCellValue('E1', 'Starting Date')
		->setCellValue('F1', 'Disaster Recovery Plan')
		->setCellValue('G1', 'Placement')
		->setCellValue('H1', 'Installation to be done ?')
		->setCellValue('I1', 'Assigned To')
		->setCellValue('J1', 'Requested By')
		->setCellValue('K1', 'Status')
		->setCellValue('L1', 'Notes')
		;
		$i = 1;
		foreach($data as $d => $row)
		{
			$i++;
			if($row->maintenance == 0)
			{
				$str= Projects::getNameById($row->project);
			}else{
				$str= Maintenance::getMaintenanceDescription(substr($row->project, 0, -1));
			}
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, $row->ir_number)
			->setCellValue('B'.$i, $row->eCustomer->name)
			->setCellValue('C'.$i, $str)
			->setCellValue('D'.$i, date('d-m-Y',strtotime($row->deadline_date))) 
			->setCellValue('E'.$i, date('d-m-Y',strtotime($row->expected_starting_date)))
			->setCellValue('F'.$i, InstallationRequests::getDisasterLabel($row->disaster_recovery))
			->setCellValue('G'.$i, InstallationRequests::getlocallycustomerlabel($row->installation_locally))
			->setCellValue('H'.$i, InstallationRequests::getInstallationLabel($row->installation_location))
			->setCellValue('I'.$i, (Users::getNameById($row->assigned_to)))
			->setCellValue('J'.$i, (Users::getNameById($row->requested_by)))
			->setCellValue('K'.$i, InstallationRequests::getStatusLabel($row->status))
			->setCellValue('L'.$i, $row->notes)
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('IRs - '.date("d m Y"));
		$objPHPExcel->setActiveSheetIndex(0);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="IRs_'.date("d_m_Y").'.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}
?>