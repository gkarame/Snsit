<?php
class RequestsHrController extends Controller{
    public $message;
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete'
        );
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('index','create','update'),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny',
                'users' => array('*'
                )
            )
        );
    }
    public function actionIndex(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/requestsHr/index' => array(
                'label' => Yii::t('translations', 'Hr Request'),
                'url' => array('requestsHr/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu; 
        $model = new RequestsHr('search');
        $model->unsetAttributes(); 
        if (isset($_GET['RequestsHr'])) {
            if ($_GET['RequestsHr']['startDate'] != '') {
                $_GET['RequestsHr']['startDate'] = DateTime::createFromFormat('d/m/Y', $_GET['RequestsHr']['startDate'])->format('Y-m-d');
            }            
            $model->attributes = $_GET['RequestsHr'];
        }
        $this->render('index', array(
            'model' => $model
        ));        
    }
    public function actionCreate(){
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/requestsHr/create' => array(
                'label' => 'New HR Request',
                'url' => array('requestsHr/create'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => '',
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $error                      = false;
        $resp                       = 0;
        $model                      = new RequestsHr();
        if (isset($_POST['RequestsHr'])) {
            $model->type      = $_POST['RequestsHr']['type'];
            $model->startDate = $_POST['RequestsHr']['startDate'];
            $model->note      = $_POST['RequestsHr']['note'];
            $model->user_id   = Yii::app()->user->id;
            if ($model->validate()) {                
                $resp = RequestsHr::createRequestHr($_POST['RequestsHr']['type'], $_POST['RequestsHr']['startDate'], $_POST['RequestsHr']['note']);
            }
        }
        if ($resp > 0) {
            $this->render('successfully', array(
                'model' => $model
            ));
        } else
            $this->render('create', array(
                'model' => $model
            ));
    }
    public function actionUpdate($id){
        $id     = (int) $id;
        $status = (int) $_GET['status'];        
        $model       = $this->loadModel($id);
        $usr_request = UserPersonalDetails::getUserDetails($model->user_id);        
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/requestsHr/update' => array(
                'label' => 'Request ' . RequestsHr::getStatusLabel($status),
                'url' => array('requestsHr/update'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => '',
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $model->status              = $status; //new
        if ($model->save()) {
            RequestsHr::sendNotificationsEmails($model, 2);
            array(
                'id' => $model->id
            );
        }
        $this->render('update', array(
            'model' => $model
        ));        
    }
    public function loadModel($id){
        $model = RequestsHr::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
}?>