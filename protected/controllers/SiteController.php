<?php
Yii::import('application.extensions.EWideImage.EWideImage');
class SiteController extends Controller{
    private $_identity;
    public function filters(){
        return array(
            'postOnly + delete + shareBy'
        );
    }    
    public function init(){
        parent::init();
    }    
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('login','logout','changePassword','forgetPassword','ResetPassword','Surveys'),
                'users' => array('*')
            ),
            array(
                'allow',
                'actions' => array('changeLoginPicture','closeTab','GetWidget','rememberTab','renderTab','autoComplete','shareBy','download','dashboard'),
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
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF
            ),
            'page' => array(
                'class' => 'CViewAction'
            )
        );
    }
    public function actionIndex(){
        if (Yii::app()->user->isGuest)
            $this->redirect(array(
                'site/login'
            ));
        if (Yii::app()->user->isAdmin) {
            $subtab            = $this->getSubTab();
            $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
                '/site/index' => array('label' => 'Welcome Page','url' => array(    'site/index'),'itemOptions' => array(    'class' => 'link'),'subtab' => $subtab,'order' => Utils::getMenuOrder() + 1
                )
            )))));
        } else {
            $subtab            = $this->getSubTab();
            $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
                '/site/index' => array('label' => 'Analytics','url' => array(    'site/index'),'itemOptions' => array(    'class' => 'link'),'subtab' => $subtab,'order' => Utils::getMenuOrder() + 1
                )
            )))));
        }
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = $subtab;        
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/globalize.min.js', CClientScript::POS_HEAD)->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/dx.chartjs.js', CClientScript::POS_HEAD)->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-core.js', CClientScript::POS_HEAD) //<!-- required -->
            ->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-viz-core.js', CClientScript::POS_HEAD) //<!-- required -->
            ->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-viz-charts.js', CClientScript::POS_HEAD); //<!-- dxChart -->
        $this->render('index', array(
            'dashboards' => Dashboards::getAllDashboards(),
            'activesubtab' => $subtab
        ));
    }
    public function actionGetWidget(){
        if (Yii::app()->user->isAdmin) {            
            if (isset($_POST['id'])) {
                $lastOrder = Yii::app()->db->createCommand('
				SELECT user_widgets.order as ord FROM user_widgets 
				WHERE user_widgets.user_id=' . Yii::app()->user->id . ' ORDER BY user_widgets.order DESC LIMIT 1 ')->queryRow();                
                $order         = $lastOrder['ord'] + 1;
                $values_phases = '(' . $_POST['id'] . ',' . Yii::app()->user->id . ',' . $order . ')';
                Yii::app()->db->createCommand('INSERT IGNORE INTO user_widgets (widget_id, user_id , `order`) VALUES ' . $values_phases)->execute();
            }            
            echo json_encode(array(
                'status' => 'saved'
            )); 
        } else {
            if (isset($_POST['id'])) {
                $lastOrder = Yii::app()->db->createCommand('
				SELECT customer_widgets.order as ord FROM customer_widgets 
				WHERE customer_widgets.user_id=' . Yii::app()->user->id . ' ORDER BY customer_widgets.order DESC LIMIT 1 ')->queryRow();                
                $widadd            = new CustomerWidgets();
                $widadd->order     = $lastOrder['ord'] + 1;
                $widadd->user_id   = Yii::app()->user->id;
                $widadd->widget_id = $_POST['id'];
                $widadd->save();
            }            
            echo json_encode(array(
                'status' => 'success'
            ));
        }
    }
    public function actionDashboard(){        
        if (Yii::app()->user->isGuest)
            $this->redirect(array(
                'site/login'
            ));        
        $id = (int) $_GET['id'];
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/site/dashboard/' . $id => array(
                'label' => 'Dashboard',
                'url' => array('site/dashboard','id' => $id
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/globalize.min.js', CClientScript::POS_HEAD)->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/dx.chartjs.js', CClientScript::POS_HEAD)->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-core.js', CClientScript::POS_HEAD) //<!-- required -->
            ->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-viz-core.js', CClientScript::POS_HEAD) //<!-- required -->
            ->registerScriptFile(Yii::app()->baseUrl . '/scripts/charts/modules/dx.module-viz-charts.js', CClientScript::POS_HEAD); //<!-- dxChart -->
        echo $this->renderPartial('dashboard', array(
            'id' => $id,
            'activesubtab' => 1
        ), true);
    }
    public function actionError(){
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }    
    public function actionChangeLoginPicture(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/site/index' => array(
                'label' => 'Background Picture',
                'url' => array('site/changeLoginPicture'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        if (isset($_POST["UploadBackgroundPicture"]['url_link']) && $_POST["UploadBackgroundPicture"]['url_link'] != null) {
            $link  = '"' . $_POST["UploadBackgroundPicture"]['url_link'] . '"';
            $title = '"' . $_POST["UploadBackgroundPicture"]['title'] . '"';
            $desc  = '"' . $_POST["UploadBackgroundPicture"]['short_description'] . '"';
            unset($_POST["UploadBackgroundPicture"]['url_link']);
            Yii::app()->db->createCommand('INSERT INTO url_log (link,title,short_description) VALUES (' . $link . ',' . $title . ',' . $desc . ')')->execute();
        }        
        $model        = new UploadBackgroundPicture();
        $dirPath      = dirname(Yii::app()->request->scriptFile) . '/uploads/background_picture/';
        $thumbDirPath = $dirPath . 'thumb/';
        if (isset($_POST['submit'])) {
            $model->upload_file = CUploadedFile::getInstance($model, 'upload_file');
            if (!empty($model->upload_file) && $model->validate()){
                Utils::emptyDirectory($dirPath);
                if (!is_dir($thumbDirPath)) {mkdir($thumbDirPath, 0777, true);chmod($thumbDirPath, 0777);
                }
                EWideImage::loadFromFile($model->upload_file->tempName)->resize(1698)->saveToFile($dirPath . '/background_picture.' . $model->upload_file->extensionName);
                EWideImage::loadFromFile($model->upload_file->tempName)->resize(150)->saveToFile($thumbDirPath . '/background_picture.' . $model->upload_file->extensionName);
                $this->refresh();
            }
        }
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/scripts/forms.js");
        $this->render('change_login_picture', array(
            'model' => $model,
            'fileName' => SiteController::getBackgroundPicture(false, true)
        ));
    }    
    public static function getBackgroundPicture($path = false, $thumb = false){
        $dirPath  = dirname(Yii::app()->request->scriptFile) . '/uploads/background_picture/';
        $fileName = Yii::app()->getBaseUrl(true) . '/uploads/background_picture/';
        $filePath = $dirPath;
        if ($thumb) {
            $dirPath .= 'thumb/';
            $fileName .= 'thumb/';
            $filePath .= 'thumb/';
        }        
        $exists   = false;
        $iterator = new DirectoryIterator($dirPath);
        foreach ($iterator as $file) {
            if ($file->isFile() && strpos($file->getFilename(), 'background_picture') !== false) {
                $fileName .= $file->getFilename();
                $filePath .= $file->getFilename();
                $exists = true;
                break;
            }
        }
        if ($exists) {
            return $path ? $filePath : $fileName;
        }
        return null;
    }
    public function actionSurveys(){        
        if (isset($_GET['p'])) {
            $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
                '/site/Surveys' => array('label' => 'Surveys','url' => array(    'site/Surveys'),'itemOptions' => array(    'class' => 'link'),'order' => Utils::getMenuOrder() + 1
                )
            )))));            
            $this->layout = 'survey_screen';
            $model        = new Surveys;            
            $project = $_GET['p'];
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'surveys-form') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            $this->render('surveys', array(
                'model' => $model,
                'project' => $project
            ));
        }
    }    
    public function actionShare(){        
        $form = $this->renderPartial('share');
        echo json_encode(array(
            "status" => "failure",
            "form" => $form
            
        ));
        exit;        
    }
    public function actionLogin(){
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array(
                'site/index'
            ));
        }
        $this->layout = 'login_column1';
        $model        = new LoginForm;
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        $this->render('login', array(
            'model' => $model
        ));
    }
    public function actionLogout(){
        Yii::app()->user->logout();
        $this->redirect('login');
    }    
    public function actionChangePassword(){        
        $this->layout = 'login_column1';
        $model        = new ChangePasswordForm;
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'change-password-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['ChangePasswordForm'])) {
            $model->attributes = $_POST['ChangePasswordForm'];
            if ($model->validate() && $model->changePassword())
                $this->redirect('login');
        }
        $this->render('change_password', array(
            'model' => $model
        ));
    }  
    public function actionForgetPassword(){        
        $this->layout = 'login_column1';
        $model        = new ForgetPasswordForm;
        if (isset($_POST['ajax']) && $_POST['ajax'] == 'forget-password-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }        
        if (isset($_POST['ForgetPasswordForm'])) {
            $model->attributes = $_POST['ForgetPasswordForm'];
            if ($model->validate() && $model->forgetPassword())
                $this->redirect('login');
        }
        $this->render('forget_password', array(
            'model' => $model
        ));
    }
    public function actionResetPassword(){
        if (isset($_GET['a4tgalnngqhe5gvlaehg']) && $_GET['a4tgalnngqhe5gvlaehg'] != ' ' && $_GET['a4tgalnngqhe5gvlaehg'] != '') {            
            $id_user = $_GET['a4tgalnngqhe5gvlaehg'];
        }
        if (isset($_GET['cioi2uef9']) && $_GET['cioi2uef9'] != ' ' && $_GET['cioi2uef9'] != '') {
            $user = $_GET['cioi2uef9'];
        }
        $new_pass = substr(substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 10), 1) . substr(md5(time()), 1), 0, 13);
        if ($user == 1) {
            Yii::app()->db->createCommand("UPDATE users SET password='" . sha1($new_pass) . "' WHERE id='" . $id_user . "'")->execute();
            $email = Yii::app()->db->createCommand("SELECT email FROM user_personal_details upd, users u WHERE u.id = upd.id_user  and u.active=1 and u.id ='" . $id_user . "' ")->queryScalar();
            Yii::app()->mailer->ClearAddresses();
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
                Yii::app()->mailer->AddAddress($email);
            $body                       = "Dear User , <br/><br/> Kindly note that your new password is <b> " . $new_pass . "</b>  <br/><br/><u> It is recommended that you change your password next time you want to login. </u> <br/><br/> Best Regards, <br/>SNSit Administrator";
            Yii::app()->mailer->Subject = "SNSit Password Reset";
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
            $this->redirect(array(
                'site/login'
            ));
        } else {
            Yii::app()->db->createCommand("UPDATE customers_contacts SET password=" . $new_pass . " WHERE id='" . $id_user . "'")->execute();            
            $email = Yii::app()->db->createCommand("select email FROM customers_contacts where id='" . $id_user . "' ")->queryScalar();
            Yii::app()->mailer->ClearAddresses();
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
                Yii::app()->mailer->AddAddress($email);
            $body                       = "Dear User , <br/><br/> Kindly note that your new password is <b> " . $new_pass . "</b> <br/><br/> <u> It is recommended that you change your password next time you want to login. </u> <br/><br/> Best Regards, <br/>SNSit Administrator";
            Yii::app()->mailer->Subject = "SNSit Password Reset";
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
            $this->redirect(array(
                'site/login'
            ));            
        }
    }
    public function actionRememberTab(){
        if (isset($_POST['url'], $_POST['index'], Yii::app()->session['menu'])) {
            $menu = Yii::app()->session['menu'];
            $keys = array_keys($menu);            
            $baseurl = Yii::app()->request->getBaseUrl(true);
            $baseUrl = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $baseurl);
            $url     = str_replace($baseUrl, "", $_POST['url']);
            if (isset($menu[$url])) {
                $menu[$url]['subtab']       = (int) $_POST['index'];
                Yii::app()->session['menu'] = $menu;
                $subtab = $menu[$url]['subtab'] + 1;
                echo json_encode(array('status' => 'success','subtab' => $subtab
                ));
                exit;
            }
        }
    }
    public function actionCloseTab(){
        unset(Yii::app()->session['id']);
        unset(Yii::app()->session['id_files']);
        if (isset($_POST['url']) && isset($_POST['params'])) {
            $response = Utils::closeTab($_POST['url'], $_POST['params']);
            echo json_encode($response);
            exit;
        } else if (isset($_POST['url'])) {
            $response = Utils::closeTab($_POST['url']);
            echo json_encode($response);
            exit;
        }
    }    
    public function actionAutocomplete($with = false){
        $result = array();
        if (isset($_GET['term'])) {
            $sql     = 'SELECT users.firstname, users.lastname, upd.email as email FROM user_personal_details upd LEFT JOIN users ON users.id=upd.id_user WHERE upd.email LIKE :term OR users.lastname LIKE :term OR users.firstname LIKE :term';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindValue(":term", '%' . $_GET['term'] . '%', PDO::PARAM_STR);
            $users = $command->query();            
            foreach ($users as $user) {
                if ($user['email'] == '')continue;
                $res = '';
                if ($user['firstname'] != '') {$res .= $user['firstname'] . ' ';
                }
                if ($user['lastname'] != '') {$res .= $user['lastname'] . ' ';
                }
                if ($res != '') {$res .= '<' . $user['email'] . '>';
                } else {$res .= $user['email'];
                }
                $result[] = $res;
            }            
            if ($with) {
                $sql     = 'SELECT name, email FROM customers_contacts WHERE email LIKE :term OR name LIKE :term';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindValue(":term", '%' . $_GET['term'] . '%', PDO::PARAM_STR);
                $customers = $command->query();
                foreach ($customers as $customer) {if ($customer['email'] == '')    continue;$res = '';if ($customer['name'] != '') {    $res .= $customer['name'] . ' ';}if ($res != '') {    $res .= '<' . $customer['email'] . '>';} else {    $res .= $customer['email'];}$result[] = $res;
                }
            }
            echo json_encode($result);
        }
    }    
    public function actionShareBy($id){
        $model = new ShareByForm;
        $id    = (int) $id;
        $file  = "file";        
        if (isset($_POST['model']) && $id > 0) {
            $class = $_POST['model'];
            switch ($class) {
                case 'eas':$item = Eas::model()->findByPk($id);$this->generatePdf('eas', $id);break;
                case 'invoices':$item = Invoices::model()->findByPk($id);break;
                case 'documents':$item = Documents::model()->findByPk($id);break;
                case 'invoicesshare':$item = Invoices::model()->findByPk($id);if ($item->final_invoice_number == null)    $this->generatePdf('invoicesShare', $id);break;
            }
            if ($item) {
                if (isset($_POST['ShareByForm'])) {if ($class == 'invoicesshare' || $class == 'invoices') {    if ($item->final_invoice_number != null) {        $file = $item->getFilePrinted(true);    } else {        $file = $item->getFileShare(true);    }} else {    $file = $item->getFile(true);}$model->attributes = $_POST['ShareByForm'];if ($model->validate() && $file != NULL) {    $emails_to      = explode(',', $model->to);    $emails_invalid = array();    $validator      = new CEmailValidator;    foreach ($emails_to as $em) {        $email = '';        if (trim($em)) {            $arr = explode('<', $em);            if (count($arr) == 2) {                $to                         = array();                $to[substr($arr[1], 0, -1)] = $arr[0];                if ($validator->validateValue(substr($arr[1], 0, -1))) {                    $email = $to;                }            } else {                if ($validator->validateValue($em)) {                    $email = $em;                }            }            if (!empty($email)) {                Yii::app()->mailer->AddAddress($email);            } else {                $emails_invalid[] = $em;            }        }    }        if ($class != 'eas' && $class != 'invoices' && $item->id_category == '17' && $item->model_table == 'projects') {        if ($item->id_model) {            $pm  = Projects::getProjectManagerEmail($item->id_model);            $bm  = Projects::getBusinessManagerEmail($item->id_model);            $ops = Projects::getActiveOpsAssignedIds($item->id_model);                        if ($validator->validateValue($pm)) {                if (!empty($pm)) {                 /*   Yii::app()->mailer->AddCcs($pm);*/     Yii::app()->mailer->AddAddress($pm);               Yii::app()->mailer->From = $pm;                }            }                        if ($validator->validateValue($bm)) {                if (!empty($bm)) {                 /*   Yii::app()->mailer->AddCcs($bm);  */      Yii::app()->mailer->AddAddress($bm);         }            }                        foreach ($ops as $key => $op) {                $opemail = Users::getEmailbyID($ops[$key]['id_user']);                                if ($validator->validateValue($opemail)) {                    if (!empty($opemail)) {                  /*      Yii::app()->mailer->AddCcs($opemail);*/        Yii::app()->mailer->AddAddress($opemail);            }                }            }                        Yii::app()->mailer->Subject = $model->subject . ' - ' . Projects::getNameById($item->id_model);        } else {            Yii::app()->mailer->Subject = $model->subject;        }    } else {        Yii::app()->mailer->Subject = $model->subject;    }            $sent = Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($model->body) . "</div>")->AddFile($file)->Send(true);        echo json_encode(array(        "status" => "success",        'sent' => $sent,        'not_sent_to' => implode(',', $emails_invalid)    ));    exit;}
                }                
                $form = $this->renderPartial('shareby', array('model' => $model,'item' => $item,'class' => $class
                ), true, true);
                echo json_encode(array("status" => "failure","form" => $form,"file_found" => $file == NULL ? 0 : 1
                ));
                exit;
            }
        }
    }    
    public function actionDownload($file){
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_end_clean();
            flush();
            readfile($file);
            exit;
        }
    }    
    public function actionHoliday(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/site/holiday' => array(
                'label' => 'Holidays',
                'url' => array('site/holiday'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $result = Yii::app()->db->createCommand(" select public_holiday ,date, office ,comments from public_holidays  order by office asc , date asc ")->queryAll();
        $this->render('holiday', array(
            'public_holidays' => $result
        ));
    }
    public function actionrenderTab(){        
        if (isset($_POST['url'], $_POST['index'], Yii::app()->session['menu'])) {
            $menu = Yii::app()->session['menu'];
            $keys = array_keys($menu);            
            $baseurl = Yii::app()->request->getBaseUrl(true);
            $baseUrl = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $baseurl);
            $url     = str_replace($baseUrl, "", $_POST['url']);
            if (isset($menu[$url])) {
                $menu[$url]['subtab']       = (int) $_POST['index'];
                Yii::app()->session['menu'] = $menu;
                echo json_encode(array('status' => 'success','subtab' => $menu[$url]['subtab']
                ));
                exit;
            }
        }        
        $this->render('holiday');
    }
    public function actionExtension(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/site/extension' => array(
                'label' => 'Phone Extensions',
                'url' => array('site/extension'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu; 
        $this->render('extension');
    }
    public function actionTravel(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/site/travel' => array(
                'label' => 'Travel Policy',
                'url' => array('site/travel'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $this->render('travel');
    } 
}?>

