<?php
class SettingsController extends Controller{
    public function filters(){
        return array(
            'accessControl', 
            'postOnly + deleteCodelkup'
        );
    }
    public function accessRules(){
        return array(
            array(
                'allow', 
                'actions' => array('index','settings','editSettings','editCodeList','manageCodelkup','deleteCodelkup',
				'ManageCodelkupCurrency','manageCodelkupSupportPlan','manageCodelkupTemplate','ManageCodelkupYearlySales','writequery','exestatment'),
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
    public function actionIndex(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/settings/index' => array(
                'label' => Yii::t('translations', 'Settings'),
                'url' => array('settings/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $this->jsConfig->current['activeTab'] = $this->getSubTab();        
        $settings = new SystemParameters('search');
        $settings->unsetAttributes();        
        $codelists_categories = CodelistsCategories::model()->with('codelists')->findAll(array(
            'order' => 'list_order ASC'
        ));        
        $this->render('index', array(
            'settings' => $settings,
            'codelists_categories' => $codelists_categories
        ));
    }    
    public function actionEditSettings(){
        if (!GroupPermissions::checkPermissions('settings-general_settings', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/settings/editSettings' => array(
                'label' => Yii::t('translations', 'Edit Settings'),
                'url' => array('settings/editSettings'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $settings = SystemParameters::model()->findAll(array(
            'index' => 'id'
        ));        
        if (isset($_POST['SystemParameters'])) {
            $saved = true;
            foreach ($settings as $i => $setting) {
                if (isset($_POST['SystemParameters'][$i])) {$setting->attributes = $_POST['SystemParameters'][$i];// validation of special parameters like numericswitch ($setting->system_parameter) {    case 'page_size':        $setting->value = (int) $setting->value;        if ($setting->value == 0) {            $setting->value = 25;        }        break;}
                }
                $saved = $setting->save() && $saved;
            }
            if ($saved) {
                Utils::closeTab(Yii::app()->createUrl('settings/editSettings'));
                $this->action_menu = Yii::app()->session['menu'];
                $this->redirect(array('index'
                ));
            }
        }        
        $this->render('editSettings', array(
            'settings' => $settings
        ));
    }    
    public function actionManageCodelkup(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['Codelkups'])) {
            if (isset($_POST['Codelkups']['id'])) {
                $model = Codelkups::model()->findByPk((int) $_POST['Codelkups']['id']);
                if ($model === null) {echo json_encode(array(    'status' => 'failure',    'error' => 'Invalid data received'));Yii::app()->end();
                }
            } else {
                $model = new Codelkups();
                
            }
            $model->attributes = $_POST['Codelkups'];
            if ($model->save()) {
                $dropDown = $this->renderPartial('_codelist_select', array('codelist' => $model->codelist
                ), true);
                echo json_encode(array('status' => 'success','dropDown' => $dropDown
                ));
            } else {
                echo json_encode(array('status' => 'failure','errors' => json_encode(CCustomActiveForm::validate($model, null, true, false))
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }    
    public function actionmanageCodelkupSupportPlan(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['Codelkups'])) {
            $model_plan = new SupportRate();
            if (isset($_POST['Codelkups']['id'])) {
                $model = Codelkups::model()->findByPk((int) $_POST['Codelkups']['id']);
                if ($model === null) {echo json_encode(array(    'status' => 'failure',    'error' => 'Invalid data received'));Yii::app()->end();
                }
                $model_plan->plan = $_POST['Codelkups']['id'];
                $ok                       = 0;
            } else {
                $model = new Codelkups();
                $ok    = 1;
            }
            $model->attributes = $_POST['Codelkups'];            
            if ($model->save()) {
                if ($ok == 1) {$model_plan->plan = $model->id;
                }
                $model_plan->rate = $_POST['SupportRate']['rate'];
                $model_plan->date = date('Y-m-d', strtotime("now"));
                if ($model_plan->save()) {
                } else {echo json_encode(array(    'status' => 'failure',    'errors' => json_encode(CCustomActiveForm::validate($model_plan, null, true, false))));
                }                
                $dropDown = $this->renderPartial('_codelist_select', array('codelist' => $model->codelist
                ), true);
                echo json_encode(array('status' => 'success','dropDown' => $dropDown
                ));
            } else {
                echo json_encode(array('status' => 'failure','errors' => json_encode(CCustomActiveForm::validate($model, null, true, false))
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }
    public function actionManageCodelkupCurrency(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['Codelkups'])) {
            $model_currency = new CurrencyRate();
            if (isset($_POST['Codelkups']['id'])) {
                $model = Codelkups::model()->findByPk((int) $_POST['Codelkups']['id']);
                if ($model === null) {echo json_encode(array(    'status' => 'failure',    'error' => 'Invalid data received'));Yii::app()->end();
                }
                $model_currency->currency = $_POST['Codelkups']['id'];
                $ok                       = 0;
            } else {
                $model = new Codelkups();
                $ok    = 1;
            }
            $model->attributes = $_POST['Codelkups'];            
            if ($model->save()) {
                if ($ok == 1) {$model_currency->currency = $model->id;
                }
                $model_currency->rate = $_POST['CurrencyRate']['rate'];
                $model_currency->date = date('Y-m-d', strtotime("now"));
                if ($model_currency->save()) {
                } else {echo json_encode(array(    'status' => 'failure',    'errors' => json_encode(CCustomActiveForm::validate($model_currency, null, true, false))));
                }                
                $dropDown = $this->renderPartial('_codelist_select', array('codelist' => $model->codelist
                ), true);
                echo json_encode(array('status' => 'success','dropDown' => $dropDown
                ));
            } else {
                echo json_encode(array('status' => 'failure','errors' => json_encode(CCustomActiveForm::validate($model, null, true, false))
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }
    public function actionManageCodelkupTemplate(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['Codelkups'])) {
            $id_codelkup = (int) $_POST['Codelkups']['id_codelist'];
            $codelkup    = $_POST['Codelkups']['codelkup'];            
            $criteria            = new CDbCriteria();
            $criteria->condition = "id_codelist='" . $id_codelkup . "' and codelkup='" . $codelkup . "' ";
            $getid               = Codelkups::model()->findAll($criteria);
            if (!empty($getid)) {
                foreach ($getid as $id) {$idcode = $id['id'];
                }                
                $criteria            = new CDbCriteria();
                $criteria->condition = "id_codelkup='" . $idcode . "'";
                $model_template      = ReceivablesTemplateEmails::model()->findAll($criteria);                
                $model_template = reset($model_template);
                $model = Codelkups::model()->findByPk($idcode);
                if ($model === null) {echo json_encode(array(    'status' => 'failure',    'error' => 'Invalid data received'));Yii::app()->end();
                }
                $ok = 0;                
            } else {
                $model_template    = new ReceivablesTemplateEmails();
                $model             = new Codelkups();
                $model->attributes = $_POST['Codelkups'];
                $ok                = 1;
                
            }            
            if ($model->save()) {
                if ($ok == 1) {$model_template->id_codelkup = $model->id;
                }
                $model_template->template = $_POST['Template']['template_message'];
                if ($model_template->save()) {//echo json_encode(array('status' => 'success'));
                } else {echo json_encode(array(    'status' => 'failure',    'errors' => json_encode(CCustomActiveForm::validate($model_template, null, true, false))));
                }                
                $dropDown = $this->renderPartial('_codelist_select', array('codelist' => $model->codelist
                ), true);
                echo json_encode(array('status' => 'success','dropDown' => $dropDown
                ));
            } else {
                echo json_encode(array('status' => 'failure','errors' => json_encode(CCustomActiveForm::validate($model, null, true, false))
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }
    public function actionManageCodelkupYearlySales(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['Codelkups'])) {
            $id_codelkup = (int) $_POST['Codelkups']['id_codelist'];
            $codelkup    = $_POST['Codelkups']['codelkup'];            
            $criteria            = new CDbCriteria();
            $criteria->condition = "id_codelist='" . $id_codelkup . "' and codelkup='" . $codelkup . "' ";
            $getid               = Codelkups::model()->findAll($criteria);
            if (!empty($getid)) {
                foreach ($getid as $id) {$idcode = $id['id'];
                }                
                $criteria            = new CDbCriteria();
                $criteria->condition = "id_codelkup='" . $idcode . "'";
                $yearly_amount       = YearlySales::model()->findAll($criteria);
                $yearly_amount       = reset($yearly_amount);                
                $model = Codelkups::model()->findByPk($idcode);
                if ($model === null) {echo json_encode(array(    'status' => 'failure',    'error' => 'Invalid data received'));Yii::app()->end();
                }
                $ok = 0;                
            } else {
                $yearly_amount     = new YearlySales();
                $model             = new Codelkups();
                $model->attributes = $_POST['Codelkups'];
                $ok                = 1;
            }            
            if ($model->save()) {
                if ($ok == 1) {$yearly_amount->id_codelkup = $model->id;
                }
                $yearly_amount->Amount = $_POST['Amount']['yearly_amount'];
                if ($yearly_amount->save()) {
                } else {echo json_encode(array(    'status' => 'failure',    'errors' => json_encode(CCustomActiveForm::validate($yearly_amount, null, true, false))));
                }                
                $dropDown = $this->renderPartial('_codelist_select', array('codelist' => $model->codelist
                ), true);
                echo json_encode(array('status' => 'success','dropDown' => $dropDown
                ));
            } else {
                echo json_encode(array('status' => 'failure','errors' => json_encode(CCustomActiveForm::validate($model, null, true, false))
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }
    public function actionDeleteCodelkup(){
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            if (Codelkups::model()->deleteByPk((int) $_POST['id'])) {
                echo json_encode(array('status' => 'success'
                ));
            } else {
                echo json_encode(array('status' => 'failure','error' => 'The item coudn\'t be completed due to an error'
                ));
            }
            Yii::app()->end();
        }
        echo json_encode(array(
            'status' => 'failure',
            'error' => 'No data received'
        ));
        Yii::app()->end();
    }
    public function loadModel($id){
        $model = Codelists::model()->with('codelkups')->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    public function actionwritequery(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/settings/index' => array(
                'label' => Yii::t('translations', 'Query'),
                'url' => array('settings/querybuilder'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $this->jsConfig->current['activeTab'] = $this->getSubTab();        
        $settings = new SystemParameters('search');
        $settings->unsetAttributes(); 
        $codelists_categories = CodelistsCategories::model()->with('codelists')->findAll(array(
            'order' => 'list_order ASC'
        ));        
        $this->render('querybuilder', array(
            'settings' => $settings,
            'codelists_categories' => $codelists_categories
        ));
    }    
    public function actionexestatment(){
        if (isset($_POST['query'])) {            
            $query = $_POST['query']; 
            $arr = explode(' ', trim($query));
            if ($arr[0] == 'select' || 'Select') {                
                $resultset = " <table border='1'>";
                $result    = Yii::app()->db->createCommand($query)->queryAll();
                foreach ($result as $value) {$resultset .= " <tr>";foreach ($value as $key) {    $resultset .= " <td>" . $key . " </td> ";}$resultset .= " </tr>";
                }
                $resultset .= " </table>";                
                echo json_encode(array('resultset' => $resultset
                ));
                exit;                
            } else {                
                Yii::app()->db->createCommand($query)->execute();
                echo json_encode(array('resultset' => 'Query Executed Successfully!'
                ));
                exit;                
            }
        }
    }
}?>