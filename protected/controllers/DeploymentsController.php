<?php
class DeploymentsController extends Controller{
	public $layout='//layouts/column1';
	public function filters()
	{
		return array(
			'accessControl',
			'postOnly + delete',
		);
	}
	public function accessRules(){
		return array(
			array(
				'allow',
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(
						'upload','GetProjectsByClient','GetVersionsPerClient','index','view','create','update', 'delete','updateHeader','getExcel'
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
		 		 'stateVariable' => 'deployments'
			 ),
		 );
	}
	public function actionView($id){
		if(!GroupPermissions::checkPermissions('general-deployments'))	{
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}		
		$id = (int) $id;
		$model = $this->loadModel($id);
		if ($model->assigned_srs){
			$support_desk = SupportDesk::model()->findByPk($model->assigned_srs);
			$rsr = SupportRequest::getRSR($support_desk->id);
		}

		$subtab = $this->getSubTab(Yii::app()->createUrl('deployments/view', array('id' => $id)));
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/deployments/view/'.$id => array(
					'label'=> 'Deployment#'.$model->dep_no,
					'url' => array('deployments/view', 'id' => $model->id),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1,
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		Yii::app()->clientScript
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/globalize.min.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/dx.chartjs.js',CClientScript::POS_HEAD)
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-core.js',CClientScript::POS_HEAD) //<!-- required -->
			->registerScriptFile(Yii::app()->baseUrl.'/scripts/charts/modules/dx.module-viz-charts.js',CClientScript::POS_HEAD);//<!-- dxChart -->
		$this->jsConfig->current['activeTab'] = $subtab;
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'rsr' => isset($rsr)?$rsr:null
		));		
	}
	public function actionCreate(){
		if (!GroupPermissions::checkPermissions('general-deployments','write')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/deployments/create' => array(
					'label'=> 'Add Deployment',
					'url' => array('deployments/create'),
					'itemOptions'=>array('class'=>'link'),
					'subtab' => '',
					'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;
		$model=new Deployments();
		$sr= Yii::app()->getRequest()->getParam('id');
		if(!empty($sr))
		{
			$model->id_customer=SupportDesk::getCustomerIdBySr($sr);
			$model->customer_name= Customers::getNameById($model->id_customer);
			$model->source= 663;
			$model->assigned_srs= $sr;
		}
		if (isset($_POST['Deployments']))	{

			$model->attributes = $_POST['Deployments'];
			$model->dep_no = "00000"; 
			$model->adddate = $date = date('Y-m-d H:i:s');

			$support_desk = SupportDesk::model()->findByPk($model->assigned_srs);
			if (isset($support_desk)){
				$rsr = SupportRequest::getRSR($support_desk->id);
			}

			if (!empty($rsr)){
				$model->status = 1;
			}

			if ($model->save())	{
				$model->user=Yii::app()->user->id;
				$model->dep_no = Utils::paddingCode($model->id);
				$version = Yii::app()->db->createCommand("select max(dep_version) from deployments where id_customer='".$model->id_customer."' ")->queryScalar();
				if($version){ 
					$model->dep_version= $version+0.1; 
				}else{
					$model->dep_version='1.0';
				}
				$model->save();	
				Utils::closeTab(Yii::app()->createUrl('deployments/create'));
				$this->action_menu = Yii::app()->session['menu'];
				$this->redirect(array('deployments/view', 'id'=>$model->id));
			}
		}
		$this->render('create',array(
			'model'=>$model,
			'dep_date'=>isset($_POST['deployments']['dep_date'])?$_POST['Deployments']['dep_date']:"",
			'id_customer'=>isset($_POST['deployments']['id_customer'])?$_POST['Deployments']['id_customer']:""
		));
	}
	public function actionUpdateHeader($id){
		$id = (int) $id;
		$model = $this->loadModel($id);
		$extra = array();
		if (isset($_POST['Deployments']))
		{
			$model->attributes = $_POST['Deployments'];
			if($model->source != '663')
			{
				$model->assigned_srs='';
			}
			if ($model->save()){
					echo json_encode(array_merge(array(
							'status' => 'saved',
							'html' => $this->renderPartial('_header_content', array('model' => $model), true, false)
					), $extra));
					Yii::app()->end();
				}
		}	
		Yii::app()->clientScript->scriptMap=array(
		'jquery.js'=>false,
		'jquery.min.js' => false,
		'jquery-ui.min.js' => false,
		);
		echo json_encode(array_merge(array(
				'status'=>'success',
				'html'=>$this->renderPartial('_edit_header_content', array('model'=> $model), true, true)
		), $extra));
	}
	public function actionUpdate($id){
		$model=$this->loadModel($id);
		if (isset(Yii::app()->session['menu']) && $new == 1) {
				Utils::closeTab(Yii::app()->createUrl('deployments/create'));
				$this->action_menu = Yii::app()->session['menu'];
		}
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
			$this->action_menu,
			array( 
				'/deployments/update/'.$id => array(
						'label'=> 'Deployment#'.$model->dep_no,
						'url' => array('deployments/update', 'id'=>$id),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => $subtab,
						'order' => Utils::getMenuOrder()+1
				)
			)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		if (isset($_POST['Deployments'])){
			$model->attributes=$_POST['Deployments'];
			if ($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('update',array(
			'model'=>$model,
		));
	}
	public function actionIndex(){
		if(!GroupPermissions::checkPermissions('general-deployments')){
			throw new CHttpException(403,'You don\'t have permission to access this page.');
		}
		$searchArray = isset($_GET['Deployments']) ? $_GET['Deployments'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu, 
				array(
					'/deployments/index' => array(
						'label'=>Yii::t('translations', 'Deployments'),
						'url' => array('deployments/index'),
						'itemOptions'=>array('class'=>'link'),
						'subtab' => -1,
						'order' => Utils::getMenuOrder()+1+1,
						'search' => $searchArray,
					)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Deployments('search');
		$model->unsetAttributes(); 
		$model->attributes= $searchArray;
		$this->render('index',array(
			'model'=>$model,
		));
	}
	public function actionGetVersionsPerClient()
	{
		$this->layout='';
		$customer=$_GET['id'];
		echo json_encode(Maintenance::getVersionListPerCustomer($customer));
		exit();
	}
	public function actionGetProjectsByClient(){
		$this->layout='';
		$customer=$_GET['id'];
		echo json_encode(Deployments::getProjectsDD($customer));
		exit();
	}
	public function loadModel($id){
		$model=Deployments::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model){
		if (isset($_POST['ajax']) && $_POST['ajax']==='deployments-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionDelete($id){
		$this->loadModel($id)->delete();
		if (!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	public function actionGetExcel(){
		
		Yii::import('ext.phpexcel.XPHPExcel');
		if (PHP_SAPI == 'cli')
			die('Error PHP Excel extension');
		$objPHPExcel = XPHPExcel::createPHPExcel();
		$objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")
		->setLastModifiedBy("http://www.sns-emea.com")
		->setTitle("Deployments");
		$model =Deployments::model(); 
		$model->dep_no= $_POST['Deployments']['dep_no'];	
		$select = "SELECT * FROM deployments ";
		$where = " WHERE 1=1 ";

		if(isset($_POST['Deployments']['dep_no']) && trim($_POST['Deployments']['dep_no']) !='')
		{
			$where .= " AND dep_no =".$_POST['Deployments']['dep_no']." ";
		}
		if(isset($_POST['Deployments']['id_customer'])  && trim($_POST['Deployments']['id_customer']) !='')
		{
			$where .= " AND id_customer in (select c.id from customers c where c.name like '%".$_POST['Deployments']['id_customer']."%') ";
		}
		if(isset($_POST['Deployments']['source']) && trim($_POST['Deployments']['source']) !='')
		{
			$where .= " AND source in (select p.id from projects p where p.name like '%".$_POST['Deployments']['source']."%') ";
		}
		if(isset($_POST['Deployments']['assigned_srs']) && trim($_POST['Deployments']['assigned_srs']) !='')
		{
			$where .= " AND assigned_srs like '%".$_POST['Deployments']['assigned_srs']."%' ";
		}
		if(isset($_POST['Deployments']['module']) && trim($_POST['Deployments']['module']) !='')
		{
			$where .= " AND module like '%".$_POST['Deployments']['module']."%' ";
		}
		if(isset($_POST['Deployments']['dep_version']) && trim($_POST['Deployments']['dep_version']) !='')
		{
			$where .= " AND dep_version like '%".$_POST['Deployments']['dep_version']."%' ";
		}
		if(isset($_POST['Deployments']['infor_version']) && trim($_POST['Deployments']['infor_version']) !='')
		{
			$where .= " AND infor_version = ".$_POST['Deployments']['infor_version']." ";
		}

		$data = Yii::app()->db->createCommand($select.$where)->queryAll();	
		$nb = sizeof($data);         	

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray); 
        
		$sheetId = 0;
		$objPHPExcel->setActiveSheetIndex($sheetId)
		->setCellValue('A1', 'Dep#')
		->setCellValue('B1', 'Customer')
		->setCellValue('C1', 'Description')
		->setCellValue('D1', 'Deployment Date')
		->setCellValue('E1', 'Infor Version')
		->setCellValue('F1', 'Deployment Version')
		->setCellValue('G1', 'Module')
		->setCellValue('H1', 'Source')
		->setCellValue('I1', 'Assigned Srs')
		->setCellValue('J1', 'Resource')
		->setCellValue('K1', 'Creation Date')
		;		
		$i = 1;
		foreach($data as $d => $row){
			$i++;
			$objPHPExcel->setActiveSheetIndex($sheetId)
			->setCellValue('A'.$i, ' '.$row['dep_no'])
			->setCellValue('B'.$i, Customers::getNameById($row['id_customer']))
			->setCellValue('C'.$i, $row['description'])
			->setCellValue('D'.$i, $row['dep_date'])
			->setCellValue('E'.$i, Codelkups::getCodelkup($row['infor_version'])) 
			->setCellValue('F'.$i, $row['dep_version'])
			->setCellValue('G'.$i, $row['module'])
			->setCellValue('H'.$i, Projects::getNameById($row['source']))
			->setCellValue('I'.$i, $row['assigned_srs'] )			
			->setCellValue('J'.$i, Users::getNameById($row['user']))	
			->setCellValue('K'.$i, date('d/m/Y', strtotime($row['adddate'])))	
			;
		}
		$objPHPExcel->getActiveSheet()->setTitle('Deployments');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Deployments.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public'); 		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/excel/deployments.xls";
		$objWriter->save($path);

		echo json_encode(array ('success' =>'success'));		
		exit;
	}
}
?>


