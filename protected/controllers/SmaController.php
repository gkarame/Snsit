<?php
class SmaController extends Controller{
    public $layout = '//layouts/column1';
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete'
        );
    }

    public function init()
	{
		parent::init();
		$this->setPageTitle(Yii::app()->name.' - SMA');
	}
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('generate','sendSmas','reminderActionPts','sendCreatedSmas'),
                'users' => array('*')
            ),
            array(
                'allow', 
                'actions' => array('index','view','ExtraRowExpr','updateActionDisplay','update','CreateSmactionItem','UpdateSolution','updatesolDisplay','UpdateDescription','updateDescriptionDisplay','deleteSmactionItem','ManageSmactionItem','assigned','upload','deleteUpload','getExcel'
                ),
                'users' => array('@')
            )            
        );
    }
    public function actionView($id){        
        $model                      = $this->loadModel($id);
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/sma/view/' . $id => array(
                'label' => Yii::t('translations', 'SMA#' . $model->id_no),
                'url' => array('sma/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $model                      = $this->loadModel($id);
        $this->render('view', array(
            'model' => $this->loadModel($id)
        ));
    }    
    public function actionExtraRowExpr(){
        $dp = new CActiveDataProvider('SmaActions', array(
            'sort' => array(
                'attributes' => array('description'
                ),
                'defaultOrder' => 'description'
            ),
            'pagination' => array(
                'pagesize' => 30
            )
        ));        
        $this->render('extrarowexpr', array(
            'dp' => $dp
        ));
    }
    public function actions(){
        return array(
            'upload' => array(
                'class' => 'xupload.actions.CustomXUploadAction',
                'path' => Yii::app()->getBasePath() . "/../uploads/tmp",
                'publicPath' => Yii::app()->getBaseUrl() . "/uploads/tmp",
                'stateVariable' => 'sma'
            )
        );
    }    
    public function actionCreate(){
        $model              = new Sma;
        $model->id_no       = "00000";
        $model->status      = 0;
        $model->id_customer = Yii::app()->user->customer_id;
        $this->render('create', array(
            'model' => $model
        ));
    }    
    public function actionUpdate($id){
        $id    = (int) $id;
        $model = $this->loadModel($id);
        $extra = array();
        if (isset($_POST['Sma'])) {
            $oldstatus         = $model->status;
            $oldassigned       = $model->assigned_to;
            $model->attributes = $_POST['Sma'];
            if ($model->status == 3 && $oldstatus != 3) {
                $model->close_date = date('Y-m-d H:i:s');
            }
            if ($model->save()) {
                if ($model->assigned_to != $oldassigned) {self::sendAssigned($model->id_no, $model->assigned_to, $model->id_customer, $model->sma_month, $model->instance);
                }
                if ($model->status != $oldstatus) {self::sendSMAstatusUpdate($model->id_no, $oldstatus, $model->status, $model->assigned_to, $model->notes);
                }
                if ($model->status == 3 && $oldstatus != 3) {$file_attach = $model->getFile(true, true);self::sendSmatoCustomer($model->id, $model->assigned_to, $model->sma_month, $model->sma_year, $model->instance, $file_attach);
                }
                if ($model->status > 0) {$statusNotNew = true;
                } else {$statusNotNew = false;
                }
                echo json_encode(array_merge(array('status' => 'saved','statusNotNew' => $statusNotNew,'html' => $this->renderPartial('_header_content', array(    'model' => $model), true, false)
                ), $extra));
                Yii::app()->end();
            }
        }        
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery-ui.min.js' => false
        );
        echo json_encode(array_merge(array(
            'status' => 'success',
            'html' => $this->renderPartial('_edit_header_content', array(
                'model' => $model
            ), true, true)
        ), $extra));
        Yii::app()->end();
    }    
    public function actionsendCreatedSmas(){        
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('smas_created');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $smas = '';
            $i    = 1;            
            $total  = '';
            $total2 = '';
            $mon    = date("F", strtotime("now"));
            $year   = date('Y');            
            $models = Yii::app()->db->createCommand("select * from sma where MONTH(adddate)=MONTH(CURRENT_DATE()) and YEAR(adddate)=YEAR(CURRENT_DATE())")->queryAll();
            if (empty($models)) {
                $smas .= 'No SMAs were created for the month of ' . $mon . '.<br>';
            } else {                
                $smas .= "Kindly below all SMAs created for the month of " . $mon . ":<br/><br/>";
                $smas .= '<ul>';
                foreach ($models as $model) {$smas .= "<li><b>SMA#</b>" . $model['id_no'] . " - <b>Customer:</b> " . Customers::getNameById($model['id_customer']) . " - <b>Instance:</b> " . $model['instance'] . "</li>";
                }
                $smas .= "</ul>";
            }
            $subject = $notif['name'] . ' - ' . $mon . ' ' . $year;
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $smas
                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }        
        echo $body;
    }    
    public function actionreminderActionPts(){
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('smas_actionsreminder');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $AllSmas = Yii::app()->db->createCommand("SELECT distinct(id_sma) FROM `sma_actions` where status != 5 
				and eta<(CURRENT_DATE()+INTERVAL 2 WEEK) order by action_instance")->queryAll();            
            if (!empty($AllSmas)) {
                foreach ($AllSmas as $key => $AllSma) {$smas = 'Dear All,<br/><br/>';$models = Yii::app()->db->createCommand("SELECT  title,id_sma,description,eta, suggested_sol, responsibility,status  FROM `sma_actions` where status != 5 
					and id_sma=" . $AllSma['id_sma'] . " and eta<(CURRENT_DATE()+INTERVAL 2 WEEK) order by action_instance")->queryAll();$smas .= "Kindly below a list of open action points pending executions:<br/><br/>";$smas .= "<table border='1'  style='font-family:Calibri;' ><tr><th>Action #</th><th>Description</th><th>Suggested Solution</th><th>Assigned to</th><th>Status</th><th>ETA</th></tr>";$counter = 1;foreach ($models as $model) {    $smas .= "<tr><td>" . $counter . "</td> <td>" . $model['description'] . "</td>  <td>" . $model['suggested_sol'] . " </td> <td> " . $model['responsibility'] . " </td> <td style='text-align:center'>  " . SmaActions::getStatusLabel($model['status']) . "</td> <td style='text-align:center'>  " . date_format(DateTime::createFromFormat('Y-m-d', $model['eta']), 'd/m/Y') . "</td></tr>";    $counter++;}$smas .= "</table><br/><br/> Best Regards,<br/>SNSit";$subject = $notif['name'];$replace = array(    EmailNotificationsGroups::getGroupDescription($notif['id']),    $smas    );$body    = str_replace($to_replace, $replace, $notif['message']);Yii::app()->mailer->ClearAddresses();$assignedEmail = Users::getEmailbyID(Sma::getassignedSMA($model['id_sma']));if (filter_var($assignedEmail, FILTER_VALIDATE_EMAIL)) {    Yii::app()->mailer->AddAddress($assignedEmail);}$getCustomer = Sma::getCustomerEmailsSMA($model['id_sma']);$emailsCust  = explode(",", $getCustomer);foreach ($emailsCust as $key => $emailc) {    if (filter_var($emailc, FILTER_VALIDATE_EMAIL)) {        Yii::app()->mailer->AddAddress($emailc);    }}$emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);foreach ($emails as $email) {    Yii::app()->mailer->AddAddress($email);}//Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");Yii::app()->mailer->Subject = $subject;Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");Yii::app()->mailer->Send(true);echo $body;
                }
            }
        }
    }    
    public function sendSmatoCustomer($sma, $assigned, $month, $year, $instance, $file_attach){        
        $notif = EmailNotifications::getNotificationByUniqueName('smas_customer');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $smas = "Dear Team,<br/><br/>Kindly find attached the SMA Report issue of " . Sma::getMonthName($month) . " " . $year . ", " . $instance . " instance accompanied by the list of SMA-related action points. 
			We request you to please go through the attached documents and welcome any invitation to organize a call in order to discuss any of its contents.<br/><br/>Thank you,<br/>SNSit";
            $subject = Customers::getNameById(Sma::getCustomerSMA($sma)).' - Monthly SMA Report';
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $smas
                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
               //Yii::app()->mailer->AddCCs($email);
				Yii::app()->mailer->AddAddress($email);
            }            
            $getCustomer = Sma::getCustomerEmailsSMA($sma);
            $emailsCust  = explode(",", $getCustomer);            
            foreach ($emailsCust as $key => $emailc) {
                if (filter_var($emailc, FILTER_VALIDATE_EMAIL)) {Yii::app()->mailer->AddAddress($emailc);
                }
            }            
            $assignedEmail = Users::getEmailbyID($assigned);
            if (filter_var($assignedEmail, FILTER_VALIDATE_EMAIL)) {
               // Yii::app()->mailer->AddCCs($assignedEmail);
				Yii::app()->mailer->AddAddress($assignedEmail);
            }            
            $ids       = Yii::app()->db->createCommand("select id from sma_actions where (status !=5 or (status =5 and close_date <= (CURRENT_DATE() + INTERVAL 1 MONTH))) and id_customer=" .Sma::getCustomerSMA($sma). " ")->queryAll();
            $fileexcel = null;
            if (!empty($ids)) {
                $idsAct    = implode(',', array_column($ids, 'id'));
                $fileexcel = self::GetExcelFile($idsAct, 'email', $sma);
                if ($fileexcel) {Yii::app()->mailer->AddFile($fileexcel);
                }
            }            
            if ($file_attach) {
                Yii::app()->mailer->AddFile($file_attach);
            }            
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
            if ($fileexcel) {
                unlink($fileexcel);
            }
        }
    }
    public function actionsendSmas(){        
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('smas_summary');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $smas = '';
            $i    = 1;            
            $total  = '';
            $total2 = '';
            $mon    = date("F", strtotime("last month"));
            $year   = date('Y');            
            $models = Yii::app()->db->createCommand("select * from sma where sma_month=(MONTH(CURRENT_DATE - INTERVAL 2 WEEK))
				and sma_year=YEAR(CURRENT_DATE - INTERVAL 1 MONTH)")->queryAll();
            if (empty($models)) {
                $smas .= 'No SMAs to be displayed for the month of ' . $mon . '.<br>';
            } else {                
                $smas .= "Kindly find a summary of " . $mon . " SMAs:<br/><br/>";
                $smas .= "<table border='1'  style='font-family:Calibri;' ><tr><th>SMA#</th><th>Customer</th><th>Instance</th><th>Assigned To</th><th>Status</th><th>Closed On</th><th>Action pts Severity High</th><th>Severity Medium</th><th>Severity Low</th><th>Severity Critical</th></tr>";
                foreach ($models as $model) {if (isset($model['close_date']) && $model['close_date'] != '0000-00-00 00:00:00') {    $close = date("d/m/Y", strtotime($model['close_date']));} else {    $close = '';}$smas .= "<tr><td>" . $model['id_no'] . "</td> <td>" . Customers::getNameById($model['id_customer']) . "</td><td>" . $model['instance'] . " </td><td>" . Users::getNameById($model['assigned_to']) . " </td> <td>" . Sma::getStatusLabel($model['status']) . " </td> <td> " . $close . " </td> <td style='text-align:center'>  " . SmaActions::getActionsNumberOpen($model['id_customer'], $model['instance'], 2) . "</td> <td style='text-align:center'>  " . SmaActions::getActionsNumberOpen($model['id_customer'], $model['instance'], 1) . "</td> <td style='text-align:center'>  " . SmaActions::getActionsNumberOpen($model['id_customer'], $model['instance'], 0) . "</td><td style='text-align:center'>  " . SmaActions::getActionsNumberOpen($model['id_customer'], $model['instance'], 3) . "</td></tr>";
                }
                $smas .= "</table>";
            }
            $subject = $notif['name'] . ' - ' . $mon . ' ' . $year;
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $smas                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }        
        echo $body;
    }
    public function actionDeleteUpload(){
        if (isset($_GET['model_id'], $_GET['file'])) {
            $id = (int) $_GET['model_id'];
            if (isset($_GET['id_customer'])) {
                $customer = (int) $_GET['id_customer'];
            } else {
                $customer = (int) Yii::app()->db->createCommand("SELECT id_customer FROM sma WHERE id = $id")->queryScalar();
            }
            $filepath = Sma::getDirPath($customer, $id) . $_GET['file'];
            $success  = is_file($filepath) && $filepath !== '.' && unlink($filepath);
            if ($success) {
                $query = "UPDATE `sma` SET file='' WHERE id='$id'";
                Yii::app()->db->createCommand($query)->execute();
            }
        }
    }
    public function actionDelete($id){
        $this->loadModel($id)->delete();
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                'admin'
            ));
    }
    public function actionIndex(){
        if (Yii::app()->user->isAdmin && !GroupPermissions::checkPermissions('general-sma')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $searchArray                = isset($_GET['Sma']) ? $_GET['Sma'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/sma/index' => array(
                'label' => Yii::t('translations', 'All SMAs'),
                'url' => array('sma/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $model = new Sma('search');
        $model->unsetAttributes();        
        $model->attributes = $searchArray;        
        if (empty($searchArray)) {
            $day = (int) date('d');
            if ($day > 22) {
                $model->sma_month = (int) date('m');
                $model->sma_year  = date('Y');
            } else {
                $model->sma_month = (int) date("m", strtotime("-1 months"));
                $model->sma_year  = date("Y", strtotime("-1 months"));
            }
        }        
        $this->render('index', array(
            'model' => $model,
            'provider' => $model->search()
        ));
    }    
    public function actionAssigned(){
        $id = (int) $_POST['id'];        
        $model              = Sma::model()->findByPk($id);
        $model->assigned_to = $_POST['value'];        
        if ($model->save()) {
            self::sendAssigned($model->id_no, $model->assigned_to, $model->id_customer, $model->sma_month, $model->instance);
            echo json_encode(array_merge(array(
                'status' => 'success'
            )));
            exit;
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => $model->getErrors()
        ));
        exit;
    }    
    public function sendSMAstatusUpdate($sma, $oldstatus, $status, $assigned, $notes){
        $notif = EmailNotifications::getNotificationByUniqueName('sma_status');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $body    = 'Dear All,<br/><br/>Kindly note that SMA#' . $sma . ' has changed from ' . Sma::getStatusLabel($oldstatus) . ' to ' . Sma::getStatusLabel($status)."." ;
            if($status == 2 )
            {
            	$body.= "<br/><br/>Notes: ". $notes;
            }
             $body  .= '<br/><br/>Thank you,<br/>SNSit';
            $subject    = $notif['name'] . ' - ' . Customers::getnamebyid(Sma::getCustomerSMA($sma)) . ' ' . Sma::getSMAInstance($sma) . '';
            $replace    = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $body
                
            );
            $body       = str_replace($to_replace, $replace, $notif['message']);
            $emails     = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();            
            $assignedEmail = Users::getEmailbyID($assigned);
            if (filter_var($assignedEmail, FILTER_VALIDATE_EMAIL)) {
                Yii::app()->mailer->AddAddress($assignedEmail);
            }
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }
           // Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
        }
    }
    public function sendAssigned($sma, $assigned, $customer, $month, $instance){
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('sma_assigned');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );            
            $body    = 'Dear ' . Users::getNameById($assigned) . ',<br/><br/>Kindly note that you are assigned on <a href="' . Yii::app()->createAbsoluteUrl('sma/view', array(
                'id' => $sma
            )) . '">SMA#' . $sma . '</a> for customer <b>' . Customers::getNameById($customer) . '</b>, instance <b>' . $instance . '</b>.<br/><br/>Thank you,<br/>SMAit';
            $subject = Sma::getMonthName($month) . $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $body                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();            
            $assignedEmail = Users::getEmailbyID($assigned);
            if (filter_var($assignedEmail, FILTER_VALIDATE_EMAIL)) {
                Yii::app()->mailer->AddAddress($assignedEmail);
            }
            foreach ($emails as $email) {
                //Yii::app()->mailer->AddCCs($email);
				Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject . ', ' . Customers::getNameById($customer);
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }
    }
    public function actiondeleteSmactionItem($id){
        $smaction = SmaActions::model()->findByPk($id);        
        if ($smaction) {
            $user  = Yii::app()->user->id;
            $sma   = $smaction->id_sma;
            $check = Sma::checkUserAssignment($sma, $user);
            if ($check == 0 && !GroupPermissions::checkPermissions('general-sma', 'write')) {
                echo json_encode(array('status' => 'failed','message' => 'You cannot delete actions without being assigned on Open SMA'
                ));
                exit;
            }
        }        
        if ($smaction == null)
            exit;
        $smaction->delete();
        echo CJSON::encode(array(
            'status' => 'success'
        ));
        exit;
    }    
    public function actionCreateSmactionItem(){
        $user  = Yii::app()->user->id;
        $sma   = $_GET['id_sma'];
        $check = Sma::checkUserAssignment($sma, $user);
        if ($check == 0 && !GroupPermissions::checkPermissions('general-sma', 'write')) {
            echo json_encode(array(
                'status' => 'failed',
                'message' => 'You cannot add actions without being assigned on Open SMA'
            ));
            exit;
        }        
        $model = new SmaActions();        
        if (isset($_POST['SmaActions'])) {            
            $model->attributes = $_POST['SmaActions'];        
            $smaStatus = Sma::getStatusCheck($sma);
            if ($smaStatus == 0 && $model->severity == 3) {
                echo json_encode(array('status' => 'failed','message' => 'You cannot add actions of \'critical\' severity on closed SMA'
                ));
                exit;
            }            
            $model->id_sma = isset($_GET['id_sma']) ? $_GET['id_sma'] : Utils::getSearchSession();
            if (isset($model->id_sma)) {
                $modelSMA               = Sma::model()->findByPk($model->id_sma);
                $model->addwho          = $user;
                $model->action_instance = $modelSMA->instance;
                $model->id_customer     = $modelSMA->id_customer;
            }            
            if (isset($model->eta) && !empty($model->eta) && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $model->eta)) {
                $date       = DateTime::createFromFormat('d/m/Y', $model->eta);
                $model->eta = $date->format('Y-m-d');
            }  
            if ($model->status == 5) {
                $model->close_date = date('Y-m-d H:i:s');
            }
            if ($model->save()) {
                echo json_encode(array('status' => 'saved','form' => $this->renderPartial('_actions_form', array(    'model' => $model), true, true)
                ));
                exit;
            }
        }
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery-ui.min.js' => false
        );
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_actions_form', array(
                'model' => $model,
                'id_sma' => isset($_GET['id_sma']) ? $_GET['id_sma'] : Utils::getSearchSession()
            ), true, true)
        ));
        exit;
    }    
    public function actionUpdateSolution(){
        $id  = $_SESSION['actionSMA'];
        $sol = nl2br($_POST['solution']);
        unset($_SESSION['actionSMA']);
        Yii::app()->db->createCommand("UPDATE `sma_actions` SET suggested_sol='" . addslashes($sol) . "' WHERE id=" . $id)->execute();
        echo json_encode(array(
            'status' => 'success'
        ));
        exit;
    }
    public function actionupdateDescription(){
        $id          = $_SESSION['actionSMA'];
        $description = nl2br($_POST['description']);
        unset($_SESSION['actionSMA']);
        Yii::app()->db->createCommand("UPDATE `sma_actions` SET description='" . addslashes($description) . "' WHERE id=" . $id)->execute();
        echo json_encode(array(
            'status' => 'success'
        ));
        exit;
    }
    
    public function actionupdatesolDisplay(){
        if (isset($_POST['sma_action'])) {
            $id    = $_POST['sma_action'];
            $model = SmaActions::model()->findByPk((int) $id);
            $user  = Yii::app()->user->id;
            $sma   = $model->id_sma;
            $check = Sma::checkUserAssignmentEdit($sma, $user);
            if ($check == 0 && !GroupPermissions::checkPermissions('general-sma', 'write')) {
                echo json_encode(array('status' => 'failed','message' => 'You cannot edit actions without being assigned on SMA'
                ));
                exit;
            } else {
                $_SESSION['actionSMA'] = $id;
                echo json_encode(array('status' => 'success','message' => strip_tags($model->suggested_sol)
                ));
                exit;
            }
        }
    }
    public function actionupdateDescriptionDisplay(){
        if (isset($_POST['sma_action'])) {
            $id    = $_POST['sma_action'];
            $model = SmaActions::model()->findByPk((int) $id);
            $user  = Yii::app()->user->id;
            $sma   = $model->id_sma;
            $check = Sma::checkUserAssignmentEdit($sma, $user);
            if ($check == 0 && !GroupPermissions::checkPermissions('general-sma', 'write')) {
                echo json_encode(array('status' => 'failed','message' => 'You cannot edit actions without being assigned on SMA'
                ));
                exit;
            } else {
                $_SESSION['actionSMA'] = $id;
                echo json_encode(array('status' => 'success','message' => strip_tags($model->description)
                ));
                exit;
            }
        }
    }
    public function actionManageSmactionItem($id){        
        $model = SmaActions::model()->findByPk((int) $id);        
        $user  = Yii::app()->user->id;
        $sma   = $model->id_sma;
        $check = Sma::checkUserAssignmentEdit($sma, $user);
        if ($check == 0 && !GroupPermissions::checkPermissions('general-sma', 'write')) {
            echo json_encode(array(
                'status' => 'failed',
                'message' => 'You cannot edit actions without being assigned on SMA'
            ));
            exit;
        }        
        if ($model->eta) {
            $model->eta = date('d/m/Y', strtotime($model->eta));
        }        
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery-ui.min.js' => false
        );
        if (isset($_POST['SmaActions'])) {            
            $oldstatus         = $model->status;
            $model->attributes = $_POST['SmaActions'];            
            $smaStatus = Sma::getStatusCheck($sma);
            if ($smaStatus == 0 && $model->severity == 3) {
                echo json_encode(array('status' => 'failed','message' => 'You cannot add actions of \'critical\' severity on closed SMA'
                ));
                exit;
            }            
            if ($model->status == 5 && $oldstatus != 5) {
                $model->close_date = date('Y-m-d H:i:s');
            }            
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $model->eta)) {
                $date       = DateTime::createFromFormat('d/m/Y', $model->eta);
                $model->eta = $date->format('Y-m-d');
            }  
            if ($model->save()) {
                $model->save();
                
                echo json_encode(array('status' => 'saved','form' => $this->renderPartial('_actions_form', array(    'model' => $model), true, true)
                ));
                exit;
            }
        }
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_actions_form', array(
                'model' => $model
            ), true, true)
        ));
        Yii::app()->end();
    }    
    public function actionGenerate(){
        $number = 0;
        $to_period;
        $allContracts = Yii::app()->db->createCommand()->select('id_maintenance,customer, sma_instances')->from('maintenance')->where('sma_instances is not null and customer!= 101  and support_service=501 and status =:status', array(
            ':status' => 'Active'
        ))->queryAll();        
        foreach ($allContracts as $contract) {
            $instances = explode(",", $contract['sma_instances']);
            foreach ($instances as $key => $instance) {
                
                $inst            = trim($instance);
                $ensureNewRecord = Yii::app()->db->createCommand("Select count(1) from sma where id_maintenance=" . $contract['id_maintenance'] . " and id_customer=" . $contract['customer'] . " and  instance='" . $inst . "' and sma_month= MONTH(CURRENT_DATE()) and sma_year= YEAR(CURRENT_DATE())")->queryScalar();
                
                if ($ensureNewRecord == 0) {
                	$getlastID = Yii::app()->db->createCommand("Select MAX(id) from sma")->queryScalar();$id_no     = Utils::paddingCode($getlastID + 1);Yii::app()->db->createCommand("insert into sma (id_no, id_maintenance,id_customer,sma_month, sma_year,instance) values ('" . $id_no . "', " . $contract['id_maintenance'] . ", " . $contract['customer'] . ", MONTH(CURRENT_DATE), YEAR(CURRENT_DATE),'" . $inst . "' )")->execute();
                	$checknew = Yii::app()->db->createCommand("Select count(1) from sma where id_customer=" . $contract['customer'] . " and id_no!=". $id_no ." and  instance='" . $inst . "'")->queryScalar();
               		if($checknew == 0)
               		{
               			$adddefault= Yii::app()->db->createCommand("insert into sma_actions (id_sma, id_customer, action_instance, title, description, status, severity, responsibility, tier, suggested_sol, eta,  addwho, adddate)
						values ('".$id_no."' ,'".$contract['customer']."' ,'" . $inst . "', 'Manage Engine Configuration', 'Implement Manage Engine configuration for all monitors.', 0,3, 'SNS','Application','Implement Manage Engine configuration for all monitors.', date((CURRENT_DATE)+INTERVAL 7 DAY),1, CURRENT_DATE() ),
						('".$id_no."' ,'".$contract['customer']."' ,'" . $inst . "', 'Manage Engine SMS Alerts', 'Implement SMS Alerts for all critical monitors  2-        Increase the frequency of the e-mail sent for SMA Pending actions to 1 email every 2 weeks. ', 0,3, 'SNS','Application','Implement SMS Alerts for all critical monitors  2-        Increase the frequency of the e-mail sent for SMA Pending actions to 1 email every 2 weeks. ', date((CURRENT_DATE)+INTERVAL 7 DAY),1, CURRENT_DATE() )
						 ")->execute();
                	}
                }
            }
        }
    }    
    public function actionupdateActionDisplay(){
        $id    = (int) $_POST['sma'];
        $check = $_POST['check'];
        
        $model                = $this->loadModel($id);
        $model->displayClosed = $check;        
        $model->save();        
    }    
    public function actionGetExcel(){
        if (!isset($_REQUEST['checkinvoice']) && empty($actionsids)) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => ' You have to select at least one invoice!'
            ));
            exit;
        }
        $ids = implode(',', $_REQUEST['checkinvoice']);
        self::GetExcelFile($ids, 'page');        
    }    
    public function GetExcelFile($ids, $source, $extraid = null){
        $data      = Yii::app()->db->createCommand("SELECT * from sma_actions WHERE  id IN (" . $ids . ") ")->queryAll();
        $sma_model = $model = Sma::model()->findByPk($data[0]['id_sma']);
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('Error PHP Excel extension');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")->setLastModifiedBy("http://www.sns-emea.com")->setTitle("SNS SMA Export");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
        $nb = sizeof($data);
        $objPHPExcel->getActiveSheet()->getStyle('A1:V' . ($nb * 17 + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        $sheetId         = 0;
        $objDrawingPType = new PHPExcel_Worksheet_Drawing();
        $objDrawingPType->setWorksheet($objPHPExcel->setActiveSheetIndex($sheetId));
        $objDrawingPType->setName("logo");
        $objDrawingPType->setPath(Yii::app()->basePath . DIRECTORY_SEPARATOR . "../images/logo_status_report.png");
        $objDrawingPType->setCoordinates('B2');
        $objDrawingPType->setOffsetX(1);
        $objDrawingPType->setOffsetY(3);        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E2:F2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G2:H2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E3:F3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J2:K2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J3:K3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N2:O2');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N3:O3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G3:H3');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B5:P5');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B6:P8');
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B10:D10');
        $objPHPExcel->getActiveSheet()->getStyle('E2:F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6d6768');        
        $objPHPExcel->getActiveSheet()->getStyle('E3:F3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('J2:J3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('N2:N3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('B5:P5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->getActiveSheet()->getStyle('B11:O11')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');        
        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('E2', 'SMA  Status Report')->setCellValue('G2', '')->setCellValue('E3', 'Client Name')->setCellValue('G3', Customers::getNameById($sma_model->id_customer))->setCellValue('J2', 'Instance')->setCellValue('L2', $sma_model->instance)->setCellValue('J3', 'SMA Pending Actions')->setCellValue('L3', SmaActions::getPendingPerCustomer($sma_model->id_customer, $sma_model->instance))->setCellValue('N2', 'SSN')->setCellValue('P2', $sma_model->id_no . ' ')->setCellValue('N3', 'Report Date')->setCellValue('P3', Sma::getMonthName($sma_model->sma_month) . ', ' . $sma_model->sma_year)->setCellValue('B5', 'Notes')->setCellValue('B6', $sma_model->notes)->setCellValue('B10', 'Actions Points');
        $bold = array(
            'font' => array(
                'bold' => true
            )
        );        
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            )
        );        
        $styleborder = array(
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            )
        );        
        $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('J3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('N3')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B11:O11')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('L2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('L3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('P2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('P3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('F2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('K2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('K3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('O2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('O3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('H2')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleborder);
        $objPHPExcel->getActiveSheet()->getStyle('B5:P5')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B6:P8')->applyFromArray($styleborder);        
        $objPHPExcel->getActiveSheet()->getStyle('B10:D10')->applyFromArray($bold);        
        $i = 11;
        $c = 1;
        foreach ($data as $d => $row) {
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $i . ':L' . $i);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M' . $i . ':N' . $i);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('O' . $i . ':P' . $i);
            $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $i . ':L' . $i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('M' . $i . ':N' . $i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('O' . $i . ':P' . $i)->applyFromArray($styleArray);            
            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':L' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
            $objPHPExcel->getActiveSheet()->getStyle('M' . $i . ':N' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
            $objPHPExcel->getActiveSheet()->getStyle('O' . $i . ':P' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . ($i + 1) . ':L' . ($i + 1));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M' . ($i + 1) . ':N' . ($i + 1));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('O' . ($i + 1) . ':P' . ($i + 1));            
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 1))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('C' . ($i + 1) . ':L' . ($i + 1))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('M' . ($i + 1) . ':N' . ($i + 1))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 1) . ':P' . ($i + 1))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 1))->applyFromArray($bold);            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 2) . ':P' . ($i + 4));
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 2) . ':P' . ($i + 4))->applyFromArray($styleborder);            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 5) . ':C' . ($i + 5));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . ($i + 5) . ':L' . ($i + 5));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M' . ($i + 5) . ':N' . ($i + 5));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('O' . ($i + 5) . ':P' . ($i + 5));            
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 5) . ':C' . ($i + 5))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('D' . ($i + 5) . ':L' . ($i + 5))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('M' . ($i + 5) . ':N' . ($i + 5))->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 5) . ':P' . ($i + 5))->applyFromArray($styleArray);            
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . ($i + 6) . ':C' . ($i + 7));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . ($i + 6) . ':L' . ($i + 7));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M' . ($i + 6) . ':N' . ($i + 7));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('O' . ($i + 6) . ':P' . ($i + 7));            
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 6) . ':C' . ($i + 7))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('D' . ($i + 6) . ':L' . ($i + 7))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('M' . ($i + 6) . ':N' . ($i + 7))->applyFromArray($styleborder);
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 6) . ':P' . ($i + 7))->applyFromArray($styleborder);            
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 5) . ':C' . ($i + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
            $objPHPExcel->getActiveSheet()->getStyle('D' . ($i + 5) . ':L' . ($i + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
            $objPHPExcel->getActiveSheet()->getStyle('M' . ($i + 5) . ':N' . ($i + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6d6768');
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 5) . ':P' . ($i + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6d6768');
            $objPHPExcel->getActiveSheet()->getStyle('O' . ($i + 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFA500');
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('B' . $i, '#')->setCellValue('C' . $i, 'Description')->setCellValue('M' . $i, 'Date Tracked')->setCellValue('O' . $i, 'Rating / Priority')->setCellValue('B' . ($i + 1), $c)->setCellValue('C' . ($i + 1), $row['title'])->setCellValue('M' . ($i + 1), date("d/m/Y", strtotime($row['adddate'])))->setCellValue('O' . ($i + 1), SmaActions::getSeverityLabel($row['severity']))->setCellValue('B' . ($i + 2), strip_tags($row['description']))->setCellValue('B' . ($i + 5), 'Responsibility')->setCellValue('D' . ($i + 5), 'Suggested Solution')->setCellValue('M' . ($i + 5), 'ETA')->setCellValue('O' . ($i + 5), 'Status')->setCellValue('B' . ($i + 6), $row['responsibility'])->setCellValue('D' . ($i + 6), strip_tags($row['suggested_sol']))->setCellValue('M' . ($i + 6), date_format(DateTime::createFromFormat('Y-m-d', $row['eta']), 'd/m/Y'))->setCellValue('O' . ($i + 6), SmaActions::getStatusLabel($row['status']));
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($i + 2))->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('D' . ($i + 6))->getAlignment()->setWrapText(true);
            $c++;
            $i = $i + 10;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Action Points');
        $objPHPExcel->setActiveSheetIndex(0);        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Action Points_' . date("d/m/Y") . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        if ($source == 'email') {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $path      = dirname(Yii::app()->request->scriptFile) . "/uploads/customers/" . $sma_model->id_customer . "/smas/" . $extraid . "/Action_Points.xls";
            $objWriter->save($path);
            return $path;
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
        exit;
    }
    public function loadModel($id){
        $model = Sma::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    protected function performAjaxValidation($model){
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'sma-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
?>