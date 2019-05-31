<?php
class TrainingsnewmoduleController extends Controller{
    public $layout = '//layouts/column1';
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete'
        );
    }    
    public function init(){
        parent::init();
        $this->setPageTitle(Yii::app()->name . ' - Trainings');
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('SendFreeInviteEmail','SendEmailCEOdirect','SendTrainingApprovedEmail','SendTrainingCancelledEmail','SendTrainingCompletedEmail','SendEmailCommunicationManager','SendEmailTravel','SendEmailCEO','SendEmailReminderCEO','SendEmailParticipants','SendInstructorCompleted','SendTrainingCancelledParticipants'
                ),
                'users' => array('*')
            ),            
            array(
                'allow', 
                'actions' => array('index','view','create','update','delete','getTrainingDesc','GetExcel','RenderNewCost','deleteCost','saveTrainingCost','print','ManageParticipant','deleteParticipant','deleteFreeCandidate','submitFreeInvitation','RenderNewInvitation','upload','testPdf','updateHeader','deleteUpload','checkDuration','updateDuration','getCandidateDesc'
                ),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }    
    public function actions(){
        return array(
            'upload' => array(
                'class' => 'xupload.actions.CustomXUploadAction',
                'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
                'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
                'stateVariable' => 'trainings_new_module'
            )
        );
    }    
    public function actionCreate(){
        if (!GroupPermissions::checkPermissions('general-trainings', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/trainingsnewmodule/create' => array(
                'label' => 'New Training',
                'url' => array('trainingsnewmodule/create'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => '',
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = 0;
        $model                                = new TrainingsNewModule;
        $model->status                        = TrainingsNewModule::STATUS_NEW;
        $model->training_number               = "00000";        
        if (isset($_POST['TrainingsNewModule'])) {
            if (empty($_POST['TrainingsNewModule']['start_date'])) {
                $model->addCustomError('start_date', 'Start Date Cannot be blank');
            }
            if (empty($_POST['TrainingsNewModule']['end_date'])) {
                $model->addCustomError('end_date', 'End Date Cannot be blank');
            }
            if (isset($_POST['TrainingsNewModule']['type'])) {
                switch ($_POST['TrainingsNewModule']['type']) {case TrainingsNewModule::TYPE_PARTNER:    if (empty($_POST['TrainingsNewModule']['partner'])) {        $model->addCustomError('partner', 'Partner Cannot be blank');    } else {        $model->partner = (int) $_POST['TrainingsNewModule']['partner'];    }    break;case TrainingsNewModule::TYPE_PRIVATE:    if (empty($_POST['TrainingsNewModule']['customer'])) {        $model->addCustomError('customer', 'Customer Cannot be blank');    } else {        $model->customer = (int) $_POST['TrainingsNewModule']['customer'];    }    break;case TrainingsNewModule::TYPE_PUBLIC:    if (empty($_POST['TrainingsNewModule']['min_participants'])) {        $model->addCustomError('min_participants', 'Min. # of Participants Cannot be blank');    } else {        $model->min_participants = (int) $_POST['TrainingsNewModule']['min_participants'];    }        if (empty($_POST['TrainingsNewModule']['cost_per_participant'])) {        $model->addCustomError('cost_per_participant', 'Cost per Participant Cannot be blank');    } else {        $model->cost_per_participant = (float) $_POST['TrainingsNewModule']['cost_per_participant'];    }    /*if(!empty($_POST['TrainingsNewModule']['confirmed_participants']))    $model->confirmed_participants = (int) $_POST['TrainingsNewModule']['confirmed_participants'];        if(!empty($_POST['TrainingsNewModule']['revenues']))    $model->revenues = (float) $_POST['TrainingsNewModule']['revenues'];    */    break;default:    $model->addCustomError('type', 'Unknown Training Type');    break;
                }
            }
            $model->course_name  = (int) $_POST['TrainingsNewModule']['course_name'];
            $model->type         = (int) $_POST['TrainingsNewModule']['type'];
            $model->start_date   = date('Y-m-d', strtotime($_POST['TrainingsNewModule']['start_date']));
            $model->end_date     = date('Y-m-d', strtotime($_POST['TrainingsNewModule']['end_date']));
            $model->year         = date('Y', strtotime($model->start_date));
            $model->city         = $_POST['TrainingsNewModule']['city'];
            $model->country      = (int) $_POST['TrainingsNewModule']['country'];
            $model->location     = $_POST['TrainingsNewModule']['location'];
            $model->instructor   = (int) $_POST['TrainingsNewModule']['instructor'];

             $model->man_days     = $_POST['TrainingsNewModule']['man_days'];
            $model->md_rate   = (int) $_POST['TrainingsNewModule']['md_rate'];

            
            $model->email_status = TrainingsNewModule::EMAIL_STATUS_NOTHING_SENT;
            if ($model->validate()) {
                if ($model->save()) {$model->training_number = Utils::paddingCode($model->idTrainings);$model->save();$this->redirect(array(    'trainingsnewmodule/view',    'id' => $model->idTrainings,    'new' => 1));
                }                
            }
        }
        $this->render('create', array(
            'model' => $model
        ));
    }    
    public function actionUpdateHeader($id)
	{
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		$error = false;
		$create = false;
		$stat= $model->status;
		$app=0;
		if (isset($_POST['TrainingsNewModule']))
		{
			if($model->type == TrainingsNewModule::TYPE_PARTNER && isset($_POST['TrainingsNewModule']['partner']) && $model->partner != $_POST['TrainingsNewModule']['partner'])
				$model->partner = $_POST['TrainingsNewModule']['partner'];			
			if($model->type == TrainingsNewModule::TYPE_PRIVATE && isset($_POST['TrainingsNewModule']['customer']) && $model->customer != $_POST['TrainingsNewModule']['customer'] )
				$model->customer = $_POST['TrainingsNewModule']['customer'];
			if($model->type == TrainingsNewModule::TYPE_PUBLIC){
				if(isset($_POST['TrainingsNewModule']['min_participants']) && $model->min_participants != $_POST['TrainingsNewModule']['min_participants'])
					$model->min_participants = $_POST['TrainingsNewModule']['min_participants'];

				if(isset($_POST['TrainingsNewModule']['cost_per_participant']) && $model->cost_per_participant != $_POST['TrainingsNewModule']['cost_per_participant'])
					$model->cost_per_participant = $_POST['TrainingsNewModule']['cost_per_participant'];
			}				
			if(isset($_POST['TrainingsNewModule']['instructor'])){
				$model->instructor = $_POST['TrainingsNewModule']['instructor'];
			}
			if(isset($_POST['TrainingsNewModule']['md_rate'])){
				$model->md_rate = $_POST['TrainingsNewModule']['md_rate'];
			}
			if(isset($_POST['TrainingsNewModule']['man_days'])){
				$model->man_days = $_POST['TrainingsNewModule']['man_days'];
			}
			if (isset($_POST['TrainingsNewModule']['start_date']) && $_POST['TrainingsNewModule']['start_date'] != $model->start_date){		
				$model->start_date = date('Y-m-d',strtotime($_POST['TrainingsNewModule']['start_date']));
			} 
			if (isset($_POST['TrainingsNewModule']['end_date']) && $_POST['TrainingsNewModule']['end_date'] != $model->end_date){		
				
				$model->end_date = date('Y-m-d',strtotime($_POST['TrainingsNewModule']['end_date']));
			}
			if (isset($_POST['TrainingsNewModule']['start_date']) && Date('Y',strtotime($_POST['TrainingsNewModule']['start_date'] )) != $model->year)
			{		
				$model->year= Date('Y',strtotime($_POST['TrainingsNewModule']['start_date'])); 
			}
			if (isset($_POST['TrainingsNewModule']['location']) && $_POST['TrainingsNewModule']['location'] != $model->location){		
				$model->location= $_POST['TrainingsNewModule']['location']; 
			} 
			if (isset($_POST['TrainingsNewModule']['city']) && $_POST['TrainingsNewModule']['city'] != $model->city){		
				$model->city= $_POST['TrainingsNewModule']['city']; 
			} 
			if (isset($_POST['TrainingsNewModule']['country']) && $_POST['TrainingsNewModule']['country'] != $model->country){		
				$model->country= $_POST['TrainingsNewModule']['country']; 
			} 
			if (isset($_POST['TrainingsNewModule']['survey_score']) && $_POST['TrainingsNewModule']['survey_score'] != $model->survey_score){		
				$model->survey_score= $_POST['TrainingsNewModule']['survey_score']; 
			} 				
			if (isset($_POST['TrainingsNewModule']['status'])){
					$error_message = '';
					$app=$_POST['TrainingsNewModule']['status'];
					if ($_POST['TrainingsNewModule']['status'] != $stat){
						if($_POST['TrainingsNewModule']['status'] == TrainingsNewModule::STATUS_CANCELLED){
							$model->status = TrainingsNewModule::STATUS_CANCELLED;
							$ea_tr_res = Yii::app()->db->createCommand("select id_ea from training_eas where id_training = ".$model->idTrainings)->queryAll();
							if($ea_tr_res){
							foreach($ea_tr_res as $ea){
								Eas::setStatus($ea['id_ea'],Eas::STATUS_CANCELLED);
								$inv_ea_tr_res = Yii::app()->db->createCommand("update invoices set status = '".Invoices::STATUS_CANCELLED."' where id_ea = ".$ea['id_ea'])->execute();
							}	
							}
						}else if($_POST['TrainingsNewModule']['status'] == TrainingsNewModule::STATUS_APPROVED){
							$costs_tr = Yii::app()->db->createCommand('
									select ((select distinct count(cost_type) from training_costs where id_training = '.$model->idTrainings.') >= 3)')->queryScalar();
							if($costs_tr == 0){
									$error_message = " You can't approve a training that doesn't have all costs submitted ";
								$error = true;
							}								
							$eas_tr = Yii::app()->db->createCommand('select id_ea from training_eas join eas on training_eas.id_ea = eas.id where eas.status <> '.Eas::STATUS_CANCELLED.' and id_training='.$model->idTrainings)->queryAll();
							if($eas_tr ==null){
								if($error){ 
								$error_message .= " and  that doesn't have any EA ";
								}else{
									$error_message = " You can't approve a training that doesn't have any EA ";
									$error = true;
								}
							}
							$minpart = Yii::app()->db->createCommand("select min_participants from trainings_new_module where idTrainings=".$model->idTrainings." ")->queryScalar();
							if ($minpart !=null){
								$curr_particp = Yii::app()->db->createCommand("
									select sum(man_days) from eas_items join eas on eas_items.id_ea = eas.id
									where  eas.id in (select id_ea from training_eas where id_training = ".$model->idTrainings." and status not in (0,1))")->queryScalar();
								if($curr_particp<$minpart){
									if($error){ 
									$error_message .= " and that doesn't have the minimal number of participants ";
									}else{
										$error_message = " You can't approve a training that doesn't have the minimal number of participants ";
										$error = true;
									}
								}
							}
						}
						else if($_POST['TrainingsNewModule']['status'] == TrainingsNewModule::STATUS_DONE){	
							if($_POST['TrainingsNewModule']['survey_score'] == null){
								if($error){
								$error_message .= " and Kindly fill the survey score in order to close the training.";
							}else{
									$error_message = " Kindly fill the survey score in order to close the training.";
							}
								$error = true;

							}
							$eas_tr2 = Yii::app()->db->createCommand('select id_ea from training_eas join eas on training_eas.id_ea = eas.id where eas.status <> '.Eas::STATUS_CANCELLED.' and id_training='.$model->idTrainings)->queryAll();
							foreach ($eas_tr2 as $ea){
								$ea_stat = Yii::app()->db->createCommand('select status from eas where id='.$ea['id_ea'])->queryScalar();								
								if($ea_stat != Eas::STATUS_APPROVED && $ea_stat != Eas::STATUS_FULLY_INVOICED){									if($error){
									$error_message .= ' And You can not change the status to Closed until All Eas are approved or fully invoiced.';
									}
									else{
										$error_message = ' You can not change this Trainings\'s status to Closed until All Eas are approved or fully invoiced.';
									}
									$error = true;
									break;
								}
							}							
						}
						if(!$error){
							$model->status = $_POST['TrainingsNewModule']['status'];
							}
						else
							{
							$extra['error'][] = $error_message; 
							unset($_POST['TrainingsNewModule']['status']);	
							}
					}
				}
				if($model->validate()){
					if(!$error){
					if($model->save()){
						if($model->status == TrainingsNewModule::STATUS_CANCELLED && $model->type !=640 && $model->email_status != 7  ){	
							self::SendTrainingCancelledEmail($model->idTrainings);							
						}else if($model->status == TrainingsNewModule::STATUS_APPROVED && $model->type !=640 && $model->email_status != 6 ){
							self::SendTrainingApprovedEmail($model->idTrainings);							
						}else if($model->status == TrainingsNewModule::STATUS_DONE && $model->type !=640 && $model->email_status !=10 ){
							self::SendTrainingCompletedEmail($model->idTrainings);
							self::SendInstructorCompleted($model->idTrainings);
						}
						echo json_encode(array_merge(array(
						'status' => 'saved',					
						'can_modify' => true,
						'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
						), $extra));
						Yii::app()->end();
					}
					}
				}	
			}
			echo json_encode(array_merge(array(
						'status' => 'success',					
						'can_modify' => true,
						'html' => $this->renderPartial('_edit_header_content', array('model' => $model),true,false)), $extra));
				

		Yii::app()->end();
		}
    public function actionUpdate($id, $new = 0, $view = 0){
        if (!GroupPermissions::checkPermissions('general-trainings', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $id    = (int) $id;
        $model = $this->loadModel($id);
        $extra = array();
        $error = false;
        if (isset(Yii::app()->session['menu']) && $new == 1) {
            Utils::closeTab(Yii::app()->createUrl('trainingsnewmodule/create'));
            $this->action_menu = Yii::app()->session['menu'];
        } else {
            if ($view == 1) {
                Utils::closeTab(Yii::app()->createUrl('trainingsnewmodule/view', array('id' => $id
                )));
                $this->action_menu = Yii::app()->session['menu'];
            }
        }
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/trainingsnewmodule/update/' . $id => array(
                'label' => 'Training #' . $model->training_number,
                'url' => array('trainingsnewmodule/update','id' => $id
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => '',
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $this->render('update', array(
            'model' => $model,
            'new' => $new,
            'can_modify' => true
        ));
    }    
    public function actionView($id){
        $id                         = (int) $id;
        $model                      = $this->loadModel($id);
        $arr                        = Utils::getShortText($model->training_number);
        $subtab                     = $this->getSubTab(Yii::app()->createUrl('trainingsnewmodule/view', array(
            'id' => $id
        )));
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/trainingsnewmodule/view/' . $id => array(
                'label' => "Training #" . $arr['text'],
                'url' => array('trainingsnewmodule/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? $model->training_number : ''
                ),
                'subtab' => $subtab,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $this->render('view', array(
            'model' => $model
        ));
    }
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('general-trainings')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $searchArray                = isset($_GET['TrainingsNewModule']) ? $_GET['TrainingsNewModule'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/trainingsnewmodule/index' => array(
                'label' => Yii::t('translations', 'Trainings'),
                'url' => array('trainingsnewmodule/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new TrainingsNewModule('search');
        $model->unsetAttributes();
        if (isset($_GET['TrainingsNewModule'])) {
            $model->attributes = $_GET['TrainingsNewModule'];
        }
        if (isset($_GET['TrainingsNewModule']['type'])) {
            $model->type = $_GET['TrainingsNewModule']['type'];            
        }
        if (isset($_GET['TrainingsNewModule']['status'])) {
            $model->status = $_GET['TrainingsNewModule']['status'];
        }        
        $model->attributes = $searchArray;        
        $this->render('index', array(
            'model' => $model
        ));
    }
    public function loadModel($id){
        $model = TrainingsNewModule::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    protected function performAjaxValidation($array){
        $errors = array();
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'trainings-form') {
            $result = array();
            foreach ($array as $key => $model) {
                if (is_array($model)) {$result = CCustomActiveForm::validateTabular($model, null, true, false);$errors = array_merge($errors, $result);unset($array[$key]);
                }
            }            
            $errors = array_merge(CCustomActiveForm::validate($array, null, true, false), $errors);
            if (empty($errors))
                echo json_encode(array('status' => 'success'
                ));
            else
                echo json_encode(array('status' => 'failure','errors' => $errors
                ));
            Yii::app()->end();
        }
    }
    public function actioncheckDuration(){        
        if (isset($_POST['course'])) {            
            $course = $_POST['course'];
            $id     = $_POST['id'];            
            $id_codelist = Codelists::getIdByCodelist('training_course');
            $dur         = Yii::app()->db->createCommand("SELECT comments FROM codelkups WHERE id_codelist = '$id_codelist' AND id = '$course' LIMIT 1")->queryScalar();
            $duration = "  Duration:" . $dur . " Days";
            echo $duration;
            exit;
            $query = "update eas_items set description= CONCAT(description,'" . $duration . "') where id ='" . $id . "' ";            
            Yii::app()->db->createCommand($query)->execute();            
            echo json_encode(array(
                'status' => 'success',
                'duration' => $duration
            ));
        }
    }    
    public function actiondeleteParticipant($id){        
        $id = (int) $id;
        Yii::app()->db->createCommand("DELETE FROM training_participants WHERE id='{$id}'")->execute();
    }    
    public function actiondeleteCost($id){        
        $id = (int) $id;
        Yii::app()->db->createCommand("DELETE FROM training_costs WHERE id='{$id}'")->execute();        
    }
    public function actiondeleteFreeCandidate($id_training = null){        
        if (!isset($_POST['checkcandidate'])) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select a Candidate'
            ));
            exit;
        }
        $ids_candidates = $_POST['checkcandidate'];
        foreach ($ids_candidates as $candidate) {
            Yii::app()->db->createCommand("DELETE FROM trainings_free_candidates WHERE id= " . $candidate)->execute();
        }
        echo json_encode(array(
            'status' => 'sent'
        ));
        exit();        
    }
    public function actionManageParticipant($id = NULL){
        if ($id == NULL) {            
            $model = new TrainingParticipants();
        } else {
            $id    = (int) $id;
            $model = TrainingParticipants::model()->findByPk($id);
        }        
        if (isset($_POST['update'], $_POST['TrainingParticipants']) && $_POST['update'] == 1) {
            if ($id == NULL) {
                $model->id_training = $_POST['TrainingParticipants']['new']['id_training'];
                $model->firstname   = $_POST['TrainingParticipants']['new']['firstname'];
                $model->lastname    = $_POST['TrainingParticipants']['new']['lastname'];
                $model->email       = $_POST['TrainingParticipants']['new']['email'];
                $model->title       = $_POST['TrainingParticipants']['new']['title'];
                $model->customer    = $_POST['TrainingParticipants']['new']['customer'];                
                $par_num = Yii::app()->db->createCommand('select count(*) from training_participants where id_training=' . $model->id_training)->queryScalar();
                if (isset($par_num)) {$model->participant_number = $par_num + 1;
                } else {$model->participant_number = 1;
                }     
            } else {
                $model->firstname = $_POST['TrainingParticipants'][$id]['firstname'];
                $model->lastname  = $_POST['TrainingParticipants'][$id]['lastname'];
                $model->email     = $_POST['TrainingParticipants'][$id]['email'];
                $model->title     = $_POST['TrainingParticipants'][$id]['title'];
                $model->customer  = $_POST['TrainingParticipants'][$id]['customer'];
            }
            if ($model->validate()) {
                if ($model->save()) {echo json_encode(array(    'status' => 'saved'));exit;
                }
            } else {
                echo json_encode(array('status' => 'success','form' => $this->renderPartial('_participants_form', array(    'model' => $model,    'update' => isset($_POST['update']) && $_POST['update'] == 1), true, true)
                ));
                exit;
            }
        }        
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false
        );
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_participants_form', array(
                'model' => $model,
                'update' => isset($_POST['update']) && $_POST['update'] == 1
                
            ), true, true)
        ));
        exit;
    }    
    public function actionsaveTrainingCost($id = null){
        if (!isset($_POST['TrainingCosts']['amount'])) {
            echo json_encode(array(
                'status' => 'fail',
                'message' => 'please Specify the Amount'
            ));
            exit();            
        }else if ($_POST['type'] == null) {
            echo json_encode(array(
                'status' => 'fail',
                'message' => 'please Specify the Type'
            ));
            exit();
            
        } else if ((float) ($_POST['TrainingCosts']['amount']) == 0) {
            echo json_encode(array(
                'status' => 'fail',
                'message' => 'Amount cannot be zero'
            ));
            exit();
        }        
        $model = new TrainingCosts;        
        $model->cost_type   = $_POST['type'];
        $model->amount      = $_POST['TrainingCosts']['amount'];
        $model->id_training = $_POST['id_training'];        
        if ($model->validate()) {
            $model->save();            
            echo json_encode(array(
                'status' => 'saved'
            ));
            exit();
        }        
        echo json_encode(array(
            'status' => 'fail',
            'message' => 'Could not add costs'
        ));
        exit();
    }    
    public function actionupdateDuration(){        
        if (isset($_POST['training'])) {            
            $train = $_POST['training'];
        }        
        $dv = Yii::app()->db->createCommand("SELECT `values` from codelkups where id='" . $train . "' ")->queryScalar();
        echo json_encode(array(
            'status' => 'success',
            'duration' => $dv
        ));
    }    
    public function actionGetExcel(){
        $model = new TrainingsNewModule('getAll');
        $data  = $model->getAll(null, true)->getData();        
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('Error PHP Excel extension');       
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")->setLastModifiedBy("http://www.sns-emea.com")->setTitle("SNS Trainings Export");
        $sheetId = 0;
        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A1', 'Training Number')->setCellValue('B1', 'Course Name')->setCellValue('C1', 'Start Date')->setCellValue('D1', 'End Date')->setCellValue('E1', 'Instructor')->setCellValue('F1', 'Location')->setCellValue('G1', 'City')->setCellValue('H1', 'Country')->setCellValue('I1', 'Type')->setCellValue('J1', 'Partner')->setCellValue('K1', 'Customer')->setCellValue('L1', 'Minimum Number of Participant')->setCellValue('M1', 'Confirmed Participants')->setCellValue('N1', 'Cost Per Participant')->setCellValue('O1', 'Revenues')->setCellValue('P1', 'Status');
        $i = 1;
        $nb = sizeof($data);          
        $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray); 
        foreach ($data as $d => $row) {
            $i++;
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $i, $row->training_number)->setCellValue('B' . $i, $row->eCourse->codelkup)->setCellValue('C' . $i, date('Y', strtotime($row->start_date)))->setCellValue('D' . $i, date('Y', strtotime($row->end_date)))->setCellValue('E' . $i, (Users::getNameById($row->instructor)))->setCellValue('F' . $i, $row->location)->setCellValue('G' . $i, $row->city)->setCellValue('H' . $i, $row->eCountry->codelkup)->setCellValue('I' . $i, $row->eType->codelkup)->setCellValue('J' . $i, isset($row->partner) ? $row->ePartner->name : "")->setCellValue('K' . $i, isset($row->customer) ? $row->eCustomer->name : "")->setCellValue('L' . $i, isset($row->min_participants) ? $row->min_participants : "")->setCellValue('M' . $i, isset($row->confirmed_participants) ? $row->confirmed_participants : "")->setCellValue('N' . $i, isset($row->cost_per_participant) ? $row->cost_per_participant : "")->setCellValue('O' . $i, isset($row->revenues) ? $row->revenues : "")->setCellValue('P' . $i, (TrainingsNewModule::getStatusLabel($row->status)));
        }        
        $objPHPExcel->getActiveSheet()->setTitle('Trainings - ' . date("d m Y"));
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Trainings_' . date("d_m_Y") . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }    
    public function actionRenderNewInvitation(){        
        $model = new TrainingFreeCandidates;
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_candidate_form', array(
                'model' => $model
            ), true)
        ));        
    } 
    public function actionRenderNewCost(){        
        $model = new TrainingCosts;
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_cost_form', array(
                'model' => $model
            ), true)
        ));        
    }    
    public function actiongetCandidateDesc($id){
        $res = Yii::app()->db->createCommand("select primary_contact_name,primary_contact_email from customers where id =" . $id)->queryAll();
        echo CJSON::encode(array(
            'name' => $res[0]['primary_contact_name'],
            'email' => $res[0]['primary_contact_email']
        ));
        exit;
    }    
    public function actionsubmitFreeInvitation($id = NULL){        
        $new = false;        
        if (!isset($_POST['train_id']))
            exit;        
        if ($id == NULL) {
            $new                = true;
            $model              = new TrainingFreeCandidates;
            $model->id_training = (int) $_POST['train_id'];
        } else {
            $id    = (int) $id;
            $model = TrainingFreeCandidates::model()->findByPk($id);
        }        
        if (isset($_POST['TrainingFreeCandidates'])) {
            if ($id == NULL) {
                $model->id_customer   = $_POST['TrainingFreeCandidates']['new']['id_customer'];
                $model->contact_name  = $_POST['TrainingFreeCandidates']['new']['contact_name'];
                $model->contact_email = $_POST['TrainingFreeCandidates']['new']['contact_email'];
            } else {
                $model->id_customer   = $_POST['TrainingFreeCandidates'][$id]['id_customer'];
                $model->contact_name  = $_POST['TrainingFreeCandidates'][$id]['contact_name'];
                $model->contact_email = $_POST['TrainingFreeCandidates'][$id]['contact_email'];
            }            
            if ($model->validate()) {
                if ($model->save()) {echo CJSON::encode(array(    'status' => 'saved'));exit;
                }
            }
        }
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false
        );
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_candidate_form', array(
                'model' => $model
            ), true, true)
        ));
        exit;
    }
    private function SendTrainingApprovedEmail($id_training){
        $model   = TrainingsNewModule::getTrainingActive($id_training);
        $subject = Codelkups::getCodelkup($model->course_name) . " Training Approved";
        $body    = "Dear " . Users::getNameById($model->instructor) . ",<br/><br/> Kindly note that the training <b>" . Codelkups::getCodelkup($model->course_name) . "</b> that is planned to be held at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b> between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> has been <b>approved</b>.";
        $emails  = Yii::app()->db->createCommand("select email from user_personal_details where id_user=" . $model->instructor)->queryScalar();
        $lm      = EmailNotificationsGroups::getLMNotificationUsers($model->instructor);
        Yii::app()->mailer->ClearAddresses();
      //  Yii::app()->mailer->AddAddress($emails);
        //Yii::app()->mailer->AddCCs($lm);
		//Yii::app()->mailer->AddAddress($lm);
       // Yii::app()->mailer->AddCCs("Micheline.Daaboul@sns-emea.com");
        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
        Yii::app()->mailer->Subject = $subject;
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
        if (Yii::app()->mailer->Send(true)) {
            $model->email_status = TrainingsNewModule::EMAIL_TRAINING_APPROVED;
            if ($model->validate()) {
                $model->save();
            }
        }
    }
    private function SendTrainingCancelledEmail($id_training){
        $model               = TrainingsNewModule::getTrainingActive($id_training);
        $email_communication = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 15")->queryScalar();
        $subject             = Codelkups::getCodelkup($model->course_name) . " Training Cancelled";
        $body                = "Dear " . Users::getNameById($model->instructor) . ",<br/><br/> Kindly note that the training <b>" . Codelkups::getCodelkup($model->course_name) . "</b> that was supposed to be held at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b> between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> has been <b>Cancelled</b>.<br><br>Regards,<br>SNSit";
        $emails              = Yii::app()->db->createCommand("select email from user_personal_details where id_user=" . $model->instructor)->queryScalar();
        $lm                  = EmailNotificationsGroups::getLMNotificationUsers($model->instructor);
        $emailCEO = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group =23 ")->queryAll();
        Yii::app()->mailer->ClearAddresses();        
        foreach ($emailCEO as $emailc) {
       //     Yii::app()->mailer->AddAddress($emailc['email']);
        }        
       // Yii::app()->mailer->AddAddress($emails);
        //Yii::app()->mailer->AddAddress($email_communication);
        //Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");
        //Yii::app()->mailer->AddAddress($lm);
        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
        Yii::app()->mailer->Subject = $subject;
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
        if (Yii::app()->mailer->Send(true)) {
            $model->email_status = TrainingsNewModule::EMAIL_TRAINING_CANCELLED;
            if ($model->validate()) {
                $model->save();
            }
        }
    }    
    public function actionSendTrainingCancelledParticipants(){        
        $error     = false;
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where DATEDIFF(start_date , NOW()) = 5  and type !=640 and status =" . TrainingsNewModule::STATUS_CANCELLED)->queryAll();
        if ($trainings != null) {
            $subject = "Training Cancelled";
            foreach ($trainings as $training) {                
                $model  = $this->loadModel($training['idTrainings']);
                $emails = Yii::app()->db->createCommand("select concat(firstname,' ',lastname) as contact_name, email as contact_email  from training_participants
													where id_training = " . $training['idTrainings'] . " 
										UNION
										select contact_name,contact_email from trainings_free_candidates where id_training = " . $training['idTrainings'])->queryAll();
                foreach ($emails as $email) {$body = "Dear " . $email['contact_name'] . ",<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> which is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b> is <b>Cancelled</b><br/>
					<br/><br/>Regards,<br/> Micheline Daaboul";Yii::app()->mailer->ClearAddresses();
        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");//Yii::app()->mailer->AddAddress($email['contact_email']);//Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");Yii::app()->mailer->Subject = $subject;Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");Yii::app()->mailer->From = "Micheline.Daaboul@sns-emea.com";if (!Yii::app()->mailer->Send(true)) {    $error = true;}
                }
            }
        } else {
            exit();
        }        
    }
    private function SendInstructorCompleted($id_training){        
        $model       = TrainingsNewModule::getTrainingActive($id_training);
        $subject     = Codelkups::getCodelkup($model->course_name) . " Training Completed";
        $instemail   = Yii::app()->db->createCommand("select email from user_personal_details where id_user=" . $model->instructor)->queryScalar();
        $adminemails = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 1 or user_groups.id_group = 14")->queryAll();
        $body        = "Dear " . Users::getNameById($model->instructor) . ",<br/><br/>
					The training <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is now <b>closed</b>, the survey score is: <b>" . $model->survey_score . " %</b>.<br/>
					Please let us know if you have identified any consultancy or software opportunities.<br/><br/>
					Thank you.<br>SNSit";
        
        Yii::app()->mailer->ClearAddresses();
        $lm = EmailNotificationsGroups::getLMNotificationUsers($model->instructor);
       // Yii::app()->mailer->AddAddress($instemail);
      //  Yii::app()->mailer->AddAddress($lm);
        foreach ($adminemails as $email) {
          //  Yii::app()->mailer->AddAddress($email['email']);
        }

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
        Yii::app()->mailer->Subject = $subject;
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
        if (Yii::app()->mailer->Send(true)) {
            $model->email_status = TrainingsNewModule::EMAIL_TRAINING_COMPLETED_INSTRUCTOR;
            if ($model->validate()) {
                $model->save();
            }
        }
    }  
    private function SendTrainingCompletedEmail($id_training){
        $model   = TrainingsNewModule::getTrainingActive($id_training);
        $subject = Codelkups::getCodelkup($model->course_name) . " Training Completed";
        $body    = "Dear,<br/><br/> Kindly note that the training <b>" . Codelkups::getCodelkup($model->course_name) . "</b> that was held at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b> between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> has been <b>Completed</b>.
		<br/> The Calculated Revenue is: <b>" . (isset($model->revenues) ? Utils::formatNumber($model->revenues) : "0") . "$</b><br/> The Calculated Profit is: <b>" . Utils::formatNumber((TrainingsNewModule::getTrainingProfit($model->idTrainings)), 2) . "$</b><br/> The Confirmed Number of Participants is: <b>" . $model->confirmed_participants . "</b><br/><br/><br/> Regards,<br/>SNSit!";
        $emails = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 1")->queryAll();
        Yii::app()->mailer->ClearAddresses();
        foreach ($emails as $email) {
          //  Yii::app()->mailer->AddAddress($email['email']);
        }        

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
        Yii::app()->mailer->Subject = $subject;
        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
        if (Yii::app()->mailer->Send(true)) {
            $model->email_status = TrainingsNewModule::EMAIL_TRAINING_COMPLETED;
            if ($model->validate()) {
                $model->save();
            }
        }
    }
    public function actionSendFreeInviteEmail($id_training = null){        
        if (!isset($_POST['checkcandidate'])) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select a Candidate'
            ));
            exit;
        }        
        $ids_candidates = $_POST['checkcandidate'];
        $model      = TrainingsNewModule::getTrainingActive($id_training);
        $error      = false;
        $subject    = 'Free Invitation for ' . Codelkups::getCodelkup($model->course_name) . ' by SNS';
        $to_replace = array(
            '{contact}',
            '{course_name}',
            '{start_date}',
            '{end_date}',
            '{location}',
            '{city}',
            '{country}'
        );
        $message    = "Dear {contact},<br>
    			SNS would like to extend One free seat  to your esteemed company to attend our <b>{course_name}</b>.<br>
    			The training will be held between <b>{start_date}</b> and <b>{end_date}</b> at <b>{location}, {city}, {country}</b>.<br>
    			Please feel free to share with me the name of the guest that you would like to register. Also let us know if you are interested to enroll additional participants.<br>
    			Thank you and I shall be waiting for your feedback as the places are limited.<br><br>Regards,<br>Micheline Daaboul<br>";
        foreach ($ids_candidates as $cand) {
            $candidate     = Yii::app()->db->createCommand("select id_customer from trainings_free_candidates where id =" . $cand)->queryScalar();
            $candidate_em  = Yii::app()->db->createCommand("select email_sent from trainings_free_candidates where id =" . $cand)->queryScalar();
            $contact_email = Yii::app()->db->createCommand("select contact_email from trainings_free_candidates where id =" . $cand)->queryScalar();
            $contact_name  = Yii::app()->db->createCommand("select contact_name from trainings_free_candidates where id =" . $cand)->queryScalar();
            $replace = array(
                $contact_name,
                Codelkups::getCodelkup($model->course_name),
                date('d-m-Y', strtotime($model->start_date)),
                date('d-m-Y', strtotime($model->end_date)),
                $model->location,
                $model->city,
                Codelkups::getCodelkup($model->country)
            );
            $body    = str_replace($to_replace, $replace, $message);
            Yii::app()->mailer->ClearAddresses();
           // Yii::app()->mailer->AddAddress($contact_email);
        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
            Yii::app()->mailer->From    = 'Micheline.Daaboul@sns-emea.com';
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");            
            if (Yii::app()->mailer->Send(true)) {
                Yii::app()->db->createCommand("update trainings_free_candidates set email_sent =1 where id =" . $cand)->execute();
            } else {
                $error = true;
            }
        }
        if (!$error) {
            echo json_encode(array(
                'status' => 'sent'
            ));
            exit();
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => 'The Email was NOT sent to All recipients'
            ));
            exit();
        }
    }
    public function actionSendEmailCommunicationManager(){        
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where DATEDIFF(start_date , NOW()) = 45 and type!=640 and status !=0 ")->queryAll(); //45
        if ($trainings != null) {
            $subject = "Upcoming Training";
            $emails  = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 15")->queryAll();
            foreach ($trainings as $training) {
                $model = $this->loadModel($training['idTrainings']);
                $body  = "Dear,<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b>.<br/> The assigned instructor for this event is: <b>" . Users::getNameById($model->instructor) . "</b><br/>Please proceed with the blast of the communication material.<br/><br/> Regards,<br/>SNSit";
                Yii::app()->mailer->ClearAddresses();
               // foreach ($emails as $email) {Yii::app()->mailer->AddAddress($email['email']);//$body.= '<br/>'. $email['email'];
             //   }

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
                Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_COMM_MANAGER;if ($model->validate()) {    $model->save();}
                }
            }            
        } else {
            exit();
        }        
    }
    public function actionSendEmailTravel(){        
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where DATEDIFF(start_date , NOW()) = 31 and status !=0 and type !=640")->queryAll(); //31
        if ($trainings != null) {
            $subject = "Upcoming Training";           
            foreach ($trainings as $training) {
                $model = $this->loadModel($training['idTrainings']);
                $body  = "Dear,<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b>.<br/> The assigned instructor for this event is: <b>" . Users::getNameById($model->instructor) . "</b><br/><br/>Regards,<br/>SNSit";
                Yii::app()->mailer->ClearAddresses();
        //        Yii::app()->mailer->AddAddress("sns-travel@sns-emea.com");

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
                Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_SNS_TRAVEL;if ($model->validate()) {    $model->save();}
                }
            }
        } else {
            exit();
        }
    }
    public function actionSendEmailCEOdirect(){        
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where idTrainings=25 and type!=640")->queryAll();
        if ($trainings != null) {
            $subject = "Upcoming Training";
            $emails  = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 1 or user_groups.id_group = 14 or user_groups.id_group = 27")->queryAll();
            foreach ($trainings as $training) {
                $model = $this->loadModel($training['idTrainings']);
                $body  = "Dear,<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b>.<br/> The assigned instructor for this event is: <b>" . Users::getNameById($model->instructor) . "</b><br/> The Minimum Number of Participants for this training is: <b>" . (isset($model->min_participants) ? $model->min_participants : " ") . "</b><br/>The Confirmed Number of Participants is: <b>" . (isset($model->confirmed_participants) ? $model->confirmed_participants : "0") . "</b><br/>Calculated Revenue is: <b>" . (isset($model->revenues) ? Utils::formatNumber($model->revenues) : "0") . "$</b><br/>Calculated Profit is: <b>" . Utils::formatNumber((TrainingsNewModule::getTrainingProfit($model->idTrainings)), 2) . "$</b><br/>Cost is: <b>" . Utils::formatNumber((TrainingsNewModule::getTrainingCosts($model->idTrainings))) . "$</b><br/><br/>Regards,<br/>SNSit";
                Yii::app()->mailer->ClearAddresses();
               // foreach ($emails as $email) {Yii::app()->mailer->AddAddress($email['email']);//$body.= '<br/>'.$email['email'];
               // }

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
                Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_SNS_SENIOR_ADMIN_CEO;if ($model->validate()) {    $model->save();}
                }
            }
        } else {
            exit();
        }
    }
    public function actionSendEmailCEO(){        
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where DATEDIFF(start_date , NOW())= 7  and type!=640 and status !=0")->queryAll();
        if ($trainings != null) {
            $subject = "Upcoming Training";
            $emails  = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 1 or user_groups.id_group = 14 or user_groups.id_group = 27")->queryAll();
            foreach ($trainings as $training) {
                $model = $this->loadModel($training['idTrainings']);
                $body  = "Dear,<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b>.<br/> The assigned instructor for this event is: <b>" . Users::getNameById($model->instructor) . "</b><br/> The Minimum Number of Participants for this training is: <b>" . (isset($model->min_participants) ? $model->min_participants : " ") . "</b><br/>The Confirmed Number of Participants is: <b>" . (isset($model->confirmed_participants) ? $model->confirmed_participants : "0") . "</b><br/>Calculated Revenue is: <b>" . (isset($model->revenues) ? Utils::formatNumber($model->revenues) : "0") . "$</b><br/>Calculated Profit is: <b>" . Utils::formatNumber((TrainingsNewModule::getTrainingProfit($model->idTrainings)), 2) . "$</b><br/>Cost is: <b>" . Utils::formatNumber((TrainingsNewModule::getTrainingCosts($model->idTrainings))) . "$</b><br/><br/>Regards,<br/>SNSit";
                Yii::app()->mailer->ClearAddresses();
             //   foreach ($emails as $email) {Yii::app()->mailer->AddAddress($email['email']);//$body.= '<br/>'.$email['email'];
             //   }

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
				Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_SNS_SENIOR_ADMIN_CEO;if ($model->validate()) {    $model->save();}
                }
            }
        } else {
            exit();
        }
    }
    public function actionSendEmailReminderCEO(){
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where  DATEDIFF(start_date , NOW())= 5 and type!=640 and status !=0")->queryAll(); //4
        if ($trainings != null) {
            $subject = "Upcoming Training";
            $emails  = Yii::app()->db->createCommand(" select email from user_personal_details  join user_groups on user_groups.id_user = user_personal_details.id_user 
												where user_groups.id_group = 1")->queryAll();
            foreach ($trainings as $training) {
                $model = $this->loadModel($training['idTrainings']);
                $body  = "Dear,<br/><br/> Kindly note that a training event for <b>" . Codelkups::getCodelkup($model->course_name) . "</b> is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b>.<br/> The assigned instructor for this event is: <b>" . Users::getNameById($model->instructor) . "</b>.<br/> This is a reminder to change the status of the training.<br/>
					<br/><br/>Regards,<br/>SNSit";
                Yii::app()->mailer->ClearAddresses();                
              //  foreach ($emails as $email) {Yii::app()->mailer->AddAddress($email['email']);
               // }

        Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
                Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_SENIOR_ADMIN_REMINDER;if ($model->validate()) {    $model->save();}
                }
            }
        } else {
            exit();
        }        
    }
    public function actionSendEmailParticipants(){
        $error     = false;
        $trainings = Yii::app()->db->createCommand("select idTrainings FROM trainings_new_module where DATEDIFF(start_date , NOW()) = 3 and type !=640  and  status =" . TrainingsNewModule::STATUS_APPROVED)->queryAll();
        if ($trainings != null) {
            $subject = "Upcoming Training";
            foreach ($trainings as $training) {
                $model  = $this->loadModel($training['idTrainings']);
                $emails = Yii::app()->db->createCommand(" select concat(firstname,' ',lastname) as contact_name , email as contact_email from training_participants
													where id_training = " . $training['idTrainings'] . " 
										UNION
										select contact_name,contact_email from trainings_free_candidates where id_training = " . $training['idTrainings'])->queryAll();
                foreach ($emails as $email) {
                	$body = "Dear " . $email['contact_name'] . ",<br/><br/> Kindly note that a training course  <b>" . Codelkups::getCodelkup($model->course_name) . "</b> which is planned between <b>" . date('d-m-Y', strtotime($model->start_date)) . "</b> and <b>" . date('d-m-Y', strtotime($model->end_date)) . "</b> at <b>" . $model->location . ", " . $model->city . ", " . Codelkups::getCodelkup($model->country) . "</b> is <b>confirmed</b> and will take place on the defined date and location<br/>
					<br/>Regards,<br/> Micheline Daaboul"; Yii::app()->mailer->ClearAddresses();
					//if (!empty($email['contact_email'])) {    Yii::app()->mailer->AddAddress($email['contact_email']);}
					Yii::app()->mailer->Subject = $subject;  
					Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
					Yii::app()->mailer->From = "Micheline.Daaboul@sns-emea.com";
					//Yii::app()->mailer->AddCCs("Nadine.Abboud@sns-emea.com");
					//Yii::app()->mailer->AddAddress("Nadine.Abboud@sns-emea.com");
					//Yii::app()->mailer->AddCCs("Claudia.Daaboul@sns-emea.com");
					//Yii::app()->mailer->AddAddress("Claudia.Daaboul@sns-emea.com");
					//Yii::app()->mailer->AddCCs("Micheline.Daaboul@sns-emea.com");
					Yii::app()->mailer->AddAddress("Micheline.Daaboul@sns-emea.com");
					if (!Yii::app()->mailer->Send(true)) {    $error = true;    }
                }
                if (!$error) {$model->email_status = TrainingsNewModule::EMAIL_STATUS_PARTICIPANTS_REMINDER;if ($model->validate()) {    $model->save();}
                }
            }
        } else {
            exit();
        }
    } 
}?>