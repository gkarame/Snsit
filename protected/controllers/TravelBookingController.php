<?php
class TravelBookingController extends Controller{
    public function filters(){
        return array(
            'accessControl'
        );
    }
    public function accessRules(){
        return array(
            array(
                'allow', 
                'actions' => array('index','view','create','createdetail','update','delete'
                ),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny', // deny all users
                'users' => array('*')
            )
        );
    }    
    public function init(){
        parent::init();
    }
    public function loadModel($id){
        $model = TravelBooking::model()->with('idUser', 'idProject')->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    public function actionIndex(){
        $searchArray                = isset($_GET['TravelBooking']) ? $_GET['TravelBooking'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travelbooking/index' => array(
                'label' => Yii::t('translations', 'Trip Booking'),
                'url' => array('travelbooking/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1 + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new TravelBooking('search');
        $model->unsetAttributes();
        $model->attributes = $searchArray;        
        $this->render('index', array(
            'model' => $model
        ));
    }
    public function actionView($id){
        $model  = $this->loadModel($id);
        $arr    = Utils::getShortText(Yii::t('translations', 'Trip #' . $model->id_book));
        $subtab = $this->getSubTab(Yii::app()->createUrl('travelbooking/view', array(
            'id' => $id
        )));        
        $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travelbooking/view/' . $id => array(
                'label' => $arr['text'],
                'url' => array('travelbooking/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? Yii::t('translations', 'Trip #' . $model->id_book) : ''
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
    public function actionCreate(){ 
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travelbooking/create' => array(
                'label' => Yii::t('translations', 'New Trip Booking'),
                'url' => array('travelbooking/create'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = 0;
        $model = new TravelBooking();
        if (isset($_POST['TravelBooking'])) {            
            $_POST['TravelBooking']['id_book'] = '00000';            
            $model->attributes = $_POST['TravelBooking'];            
            if (isset($_POST['TravelBooking']['id_user'])) {
                $first_name = trim(substr($_POST['TravelBooking']['id_user'], 0, strrpos($_POST['TravelBooking']['id_user'], " ")));
                $last_name  = trim(substr(strstr($_POST['TravelBooking']['id_user'], ' '), 1));
                echo $first_name . "->" . $last_name;
                $id_user        = Yii::app()->db->createCommand("SELECT id FROM users WHERE firstname = '$first_name' AND lastname = '$last_name'")->queryScalar();
                $model->id_user = $id_user;
            }
            if (isset($_POST['TravelBooking']['origin_country'])) {
                $model->origin_country = $_POST['TravelBooking']['origin_country'];
            }
            if (isset($_POST['TravelBooking']['destination_country'])) {
                $model->destination_country = $_POST['TravelBooking']['destination_country'];
            }            
            if (isset($_POST['TravelBooking']['from_date'])) {
                $model->from_date = $_POST['TravelBooking']['from_date'];
            }            
            if (isset($_POST['TravelBooking']['to_date'])) {
                $model->to_date = $_POST['TravelBooking']['to_date'];
            }
            if ($model->save()) {
                $model->id_book = Utils::paddingCode($model->id);
                $model->save();                
                Utils::closeTab(Yii::app()->request->url);
                $this->redirect(array('travelbooking/view','id' => $model->id
                ));
            } else {
                if (isset($_POST['TravelBooking']['id_user'])) {$model->id_user = $_POST['TravelBooking']['id_user'];
                }
                if (isset($_POST['TravelBooking']['project_id'])) {$model->project_id = $_POST['TravelBooking']['project_id'];
                }
                
            }
        }        
        $this->render('create', array(
            'model' => $model
        ));
    }    
    public function actioncreatedetail(){
        $id_book = $_GET['id_book'];        
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/travelbooking/createdetail' => array(
                'label' => Yii::t('translations', 'Add to the trip'),
                'url' => array('travelbooking/createdetail'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = 0; 
        $model = new TravelBooking();
        if (isset($_POST['TravelBooking'])) {            
            $_POST['TravelBooking']['id_book'] = $id_book;            
            $model->attributes = $_POST['TravelBooking'];
            $rez               = $Yii::app()->db->createCommand("SELECT id_user,origin_country FROM travel_booking WHERE id_book='" . $id_book . "' order by id limit 1 ")->queryScalar();
            $id_user           = $rez['id_user'];            
            $origin_country = $rez['origin_country'];            
            $model->id_user = $id_user;  
            $model->origin_country = $origin_country;            
            if (isset($_POST['TravelBooking']['destination_country'])) {
                $model->destination_country = $_POST['TravelBooking']['destination_country'];
            }            
            if (isset($_POST['TravelBooking']['from_date'])) {
                $model->from_date = $_POST['TravelBooking']['from_date'];
            }            
            if (isset($_POST['TravelBooking']['to_date'])) {
                $model->to_date = $_POST['TravelBooking']['to_date'];
            }
            if ($model->save()) {
                $model->id_book = $_POST['TravelBooking']['id_book'];
                $model->save();                
                Utils::closeTab(Yii::app()->request->url);
                $this->redirect(array('travelbooking/view','id' => $model->id
                ));
            } else {
                if (isset($_POST['TravelBooking']['id_user'])) {$model->id_user = $_POST['TravelBooking']['id_user'];
                }
                if (isset($_POST['TravelBooking']['project_id'])) {$model->project_id = $_POST['TravelBooking']['project_id'];
                }                
            }
        }        
        $this->render('createdetail', array(
            'model' => $model
        ));
    } 
}?>