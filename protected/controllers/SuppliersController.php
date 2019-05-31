<?php
class SuppliersController extends Controller{
    public function filters(){
        return array(
            'accessControl'
        );
    }
    public function accessRules(){
        return array(
            array(
                'allow', 
                'actions' => array('index','view','create','directFlag','update','CheckPrint','print','delete','printCheck','manageCheck','printLetter','createCheck','createLetter'
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
    public function loadModel($id){
        $model = Suppliers::model()->with('idType')->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('suppliers-list')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $searchArray                = isset($_GET['Suppliers']) ? $_GET['Suppliers'] : Utils::getSearchSession();
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/suppliers/index' => array(
                'label' => Yii::t('translations', 'Suppliers'),
                'url' => array('suppliers/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1 + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new Suppliers('search');
        $model->unsetAttributes(); 
        $model->attributes = $searchArray;        
        $this->render('index', array(
            'model' => $model
        ));
    }
    public function actionView($id){
        $model  = $this->loadModel($id);
        $arr    = Utils::getShortText($model->name);
        $subtab = $this->getSubTab(Yii::app()->createUrl('suppliers/view', array(
            'id' => $id
        )));        
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/suppliers/view/' . $id => array(
                'label' => $arr['text'],
                'url' => array('suppliers/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? $model->name : ''
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
        if (!GroupPermissions::checkPermissions('suppliers-list', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/suppliers/create' => array(
                'label' => Yii::t('translations', 'New Supplier'),
                'url' => array('suppliers/create'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => $this->getSubTab(),
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = 0;        
        $model = new Suppliers();
        if (isset($_POST['Suppliers'])) {
            $model->attributes = $_POST['Suppliers'];            
            if ($model->save()) {
                Utils::closeTab(Yii::app()->request->url);
                $this->redirect(array('suppliers/view','id' => $model->id
                ));
            }
        }        
        $this->render('create', array(
            'model' => $model
        ));
    }
    public function actionmanageCheck($id = NULL){        
         $new = false;
        $id    = (int) $id;
        $model = SuppliersPrint::model()->findByPk($id);        
        if (isset($_POST['SuppliersPrint'])) {            
            $model->attributes = $_POST['SuppliersPrint'][$id]; 
			 
				if(isset($_POST['SuppliersPrint'][$id]['date']))
				{
					$model->date = str_replace('/','-',$_POST['SuppliersPrint'][$id]['date']);
					
					$model->date= date('Y-m-d',strtotime($model->date));
				}
				//print_r($model->date);exit;
					//print_r($model->attributes);exit;
            if (isset($model->jv_nb) && trim($model->jv_nb) != '') {
                $strings = explode(',', $model->jv_nb);
                $str     = '(';
                foreach ($strings as $key => $string) {$string = str_pad($string, 10, '0', STR_PAD_LEFT);$str    = $str . ' 2019' . $string . ',';
                }
                $str                 = $str . '0)';
                $model->jv_nb_hidden = $str;
            }            
            if ($model->validate()) {
                $model->save();
                echo json_encode(array('status' => 'saved'
                ));
                exit;
            }
        }
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery-ui.min.js' => false
        );        
        $model->date= date('d/m/Y',strtotime($model->date));
        echo json_encode(array(
            'status' => 'success',
            'form' => $this->renderPartial('_check_form', array(
                'model' => $model
            ), true, true)
        ));        
        exit;
    }
    public function actionUpdate($id){
        $model  = $this->loadModel($id);
        $arr    = Utils::getShortText($model->name);
        $subtab = $this->getSubTab(Yii::app()->createUrl('suppliers/update', array(
            'id' => $id
        )));        
        $this->action_menu                    = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/suppliers/update/' . $id => array(
                'label' => $arr['text'],
                'url' => array('suppliers/update','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? $model->name : ''
                ),
                'subtab' => $subtab,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu']           = $this->action_menu;
        $this->jsConfig->current['activeTab'] = $subtab;        
        if (isset($_POST['Suppliers'])) {
            $model->attributes = $_POST['Suppliers'];            
            if ($model->save()) {
                Utils::closeTab(Yii::app()->request->url);
                $this->redirect(array('suppliers/view','id' => $model->id
                ));
            }
        }        
        $this->render('update', array(
            'model' => $model
        ));
    }    
    public function actiondirectFlag(){        
        if (isset($_POST['id'])) {
            $value = $_POST['val'];
            $id    = (int) $_POST['id'];
            $j     = Yii::app()->db->createCommand("UPDATE suppliers_print SET direct = '{$value}' WHERE id = {$id} ")->execute();
        }
    }
    public function actionDelete($id){
        $id = (int) $id;
        Yii::app()->db->createCommand("DELETE FROM suppliers WHERE id='{$id}'")->execute();
    }

    public function actionCheckPrint(){
    	if(isset($_POST['checkinvoice']))
    	{
    		$check = implode(',',$_POST['checkinvoice']);	
    		$c= Yii::app()->db->createCommand("SELECT count(1) FROM suppliers_print where id in (".$check.") and (`check`='' or `check` is null)")->queryScalar();	
    		if($c>0)
    		{
    			echo json_encode(array(
				'status' => "fail",
				'message' => 'Check# is required!'
			));	
    			exit;
    		}
    		echo json_encode(array(
			'invoices_ids' => isset($_POST['checkinvoice']) ? $_POST['checkinvoice'] : "",
		));		
    	}else{
    		echo json_encode(array(
			'status' => "fail",
			'message' => 'No Checks Selected!'
		));		
    	}
		
	}

    public function actionPrint(){

    	$ids_invoices = explode(',',$_GET['checkinvoice']);	
    	$title= implode('_', $ids_invoices);
    	$ids= implode(',', $ids_invoices);
    	$j2     = Yii::app()->db->createCommand("UPDATE suppliers_print SET status = 3 WHERE id in (".$ids.") and status=2")->execute();
    	$j     = Yii::app()->db->createCommand("UPDATE suppliers_print SET status = 2 WHERE id in (".$ids.") and status=1")->execute();
    	$this->generatePdf('checkm',   null,  null, null, null, $ids_invoices);
        $supplier= SuppliersPrint::getSupplierByCheck($ids_invoices[0]);
        $file = SuppliersPrint::getFileCheckmulti($supplier,$title);
        if ($file !== null) {
            header('Content-disposition: attachment; filename=BANK_CHECK_'.$title.'.pdf');
            header('Content-type: application/pdf');            
            readfile($file);
            Yii::app()->end();            
        }
        $this->redirect(Yii::app()->user->returnUrl);
    }

    public function actionPrintCheck($id){
        $model = SuppliersPrint::model()->findByPk((int) $id);
        $this->generatePdf('check', $model->id);
        $file = $model->getFileCheck();
        if ($file !== null) {
            header('Content-disposition: attachment; filename=BANK_CHECK_' . $model->id . '.pdf');
            header('Content-type: application/pdf');            
            readfile($file);
            Yii::app()->end();            
        }
        $this->redirect(Yii::app()->user->returnUrl);
    }
    public function actionPrintLetter($id){
        $model = SuppliersPrint::model()->findByPk((int) $id);
        $this->generatePdf('letter', $model->id);
        $file = $model->getFileLetter();
        if ($file !== null) {
            header('Content-disposition: attachment; filename=BANK_LETTER_' . $model->id . '.pdf');
            header('Content-type: application/pdf');            
            readfile($file);
            Yii::app()->end();            
        }
        $this->redirect(Yii::app()->user->returnUrl);
    }
    public function actionCreateCheck(){
        $model = new SuppliersPrint;
        if (isset($_POST['checksuppliers']))
            $name = Suppliers::getNameById((int) $_POST['checksuppliers'][0]);
        else
            $name = "";        
        $file = "file";
        if (isset($_POST['SuppliersPrint'])) {
            $model->attributes  = $_POST['SuppliersPrint'];
            $model->id_supplier = Suppliers::getIdByName($_POST["supplier_name"]);
            if ($_POST['SuppliersPrint']['date'] != null)
                $model->date = DateTime::createFromFormat('d/m/Y', $_POST['SuppliersPrint']['date'])->format('Y-m-d');
            $name           = $_POST["supplier_name"];
            $model->id_user = Yii::app()->user->id;            
            if (isset($model->jv_nb) && trim($model->jv_nb) != '') {
                $strings = explode(',', $model->jv_nb);
                $str     = '(';
                foreach ($strings as $key => $string) {$string = str_pad($string, 10, '0', STR_PAD_LEFT);$str    = $str . ' 2019' . $string . ',';
                }
                $str                 = $str . '0)';
                $model->jv_nb_hidden = $str;
            }            
            if ($model->validate()) {
                if ($model->save()) {echo json_encode(array(    "status" => "success",    "id" => $model->id));exit;
                }
            }
        }        
        $form = $this->renderPartial('sharebyCheck', array(
            'model' => $model,
            'name' => $name
        ), true, true);
        echo json_encode(array(
            "status" => "failure",
            "form" => $form
        ));
        exit;
    }    
    public function actionCreateLetter(){
        $model = new SuppliersPrint;
        if (isset($_POST['checksuppliers']))
            $name = Suppliers::getNameById((int) $_POST['checksuppliers'][0]);
        else
            $name = "";
        $file = "file";
        if (isset($_POST['SuppliersPrint'])) {
            $model->attributes  = $_POST['SuppliersPrint'];
            $model->id_supplier = Suppliers::getIdByName($_POST["supplier_name"]);
            $model->id_user     = Yii::app()->user->id;
            if ($_POST['SuppliersPrint']['date'] != null)
                $model->date = DateTime::createFromFormat('d/m/Y', $_POST['SuppliersPrint']['date'])->format('Y-m-d');            
            $name = $_POST["supplier_name"];
            if ($model->validate()) {
                if ($model->save()) {echo json_encode(array(    "status" => "success",    "id" => $model->id));exit;
                }
            }
        }        
        $form = $this->renderPartial('shareby', array(
            'model' => $model,
            'name' => $name
        ), true, true);
        echo json_encode(array(
            "status" => "failure",
            "form" => $form
        ));
        exit;        
    }
}?>