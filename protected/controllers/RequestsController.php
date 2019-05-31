<?php
class RequestsController extends Controller{
	public $message;
	public function filters(){
		return array(
				'accessControl', 
				'postOnly + delete',
		);
	}
	public function accessRules(){
		return array(
				array('allow', 
						'actions'=>array(
								'index', 'create', 'update','VacationDaysHRRequest','DayoffRequest'
						),
						 'expression'=>'!$user->isGuest && isset($user->isAdmin)',
				),
				array('deny',
						'users'=>array('*'),
				),
		);
	}
	public function actionIndex(){
		$searchArray = isset($_GET['Requests']) ? $_GET['Requests'] : Utils::getSearchSession();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/requests/index' => array(
								'label'=>Yii::t('translations', 'Leave Request'),
								'url' => array('requests/index'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => -1,
								'order' => Utils::getMenuOrder()+1,
								'search' => $searchArray,
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$model = new Requests('search');
		$model->unsetAttributes();  
		if (isset($_GET['Requests'])){
			if ($_GET['Requests']['startDate'] != ''){
				$_GET['Requests']['startDate'] = DateTime::createFromFormat('d/m/Y', $_GET['Requests']['startDate'])->format('Y-m-d');
			}
			if ($_GET['Requests']['endDate'] != ''){
				$_GET['Requests']['endDate'] = DateTime::createFromFormat('d/m/Y', $_GET['Requests']['endDate'])->format('Y-m-d');
			}
			if (!isset($_GET['Requests']['status'] )){				
				$_GET['Requests']['status']='New'; 
			}		
			$model->attributes=$_GET['Requests'];
		}else{

				$_GET['Requests']['status']='New'; 
				$model->attributes=$_GET['Requests'];
		}
		$this->render('index',array(
				'model'=>$model
		));
	}
	public function actionView($id){	}
	public function actionUpdate(){
		if (isset($_POST['status']) && isset($_POST['id'])){ 		
			 $id = (int) $_POST['id'];
			 $status = (int) $_POST['status'];
			$model = $this->loadModel($id);
			$usr_request = UserPersonalDetails::getUserDetails($model->user_id);
			if(($usr_request['line_manager'] != Yii::app()->user->id && (Users::checkIfDirector(Yii::app()->user->id))==NULL ) || $model->user_id == Yii::app()->user->id){
				throw new CHttpException(404,'You have to be the designated line manager.');
			}			
			$model->status = $status;
			if ($model->save()){
				Requests::sendNotificationsEmails($model,2);
				array('id' => $model->id);
			}
			$this->redirect(Yii::app()->createUrl('site/index'));			
		}
		elseif(isset($_POST['Requests'])&&!isset($_POST['Requests']['id'])&&!isset($_POST['Requests']['status'])){
			$id = (int) $_GET['id'];
			$status = $_GET['status'];
			$model = $this->loadModel($id);
			echo "OLD MODEL:".$model->id."Stat:".$model->status." Type:".$model->type." Start:".$model->startDate." End:".$model->endDate;
			echo "New MODEL:".$id."Stat:".$status." Type:".$_POST['Requests']['type']." Start:".$_POST['Requests']['startDate']." End:".$_POST['Requests']['endDate'];
			if(stripos($_POST['Requests']['startDate'], "-")==false){
				$startday=substr($_POST['Requests']['startDate'],0,2);
				$startmonth=substr($_POST['Requests']['startDate'],3,2);
				$startyear=substr($_POST['Requests']['startDate'], 6);
				$start=$startyear."-".$startmonth."-".$startday;
			}else{
				$start=$_POST['Requests']['startDate'];
			}
			if (stripos($_POST['Requests']['endDate'], "-")==false) {
				$endday=substr($_POST['Requests']['endDate'], 0,2);
				$endmonth=substr($_POST['Requests']['endDate'],3,2);
				$endyear=substr($_POST['Requests']['endDate'], 6);
				$end=$endyear."-".$endmonth."-".$endday;
			}else{
				$end=$_POST['Requests']['endDate'];
			}
			$model->type = $_POST['Requests']['type'];
			$model->startDate = $start;
			$model->endDate = $end;
			$model->user_id = Yii::app()->user->id;
			$error = false;
			$model->status = $status;//new
			if (isset($_POST['Requests']['cc'])){
				$cc=$_POST['Requests']['cc'];
				if($cc !=null){
					foreach ($cc as $user)
						$users[] = $user;
					$users = array_filter($users);
					$users = implode(',',$users);
				}
				if($users != null)
					$model->cc = $users;
				else
					$model->cc = "";
			}
			if ($_POST['Requests']['endDate'] < $_POST['Requests']['startDate']){
				$model->addCustomError('endDate', 'End date cannot be smaller than start date');
				$error = true;
			}			
			if ($model->save()){
			 echo " New MODEL:".$model->id."Stat:".$model->status." Type:".$model->type." Start:".$model->startDate." End:".$model->endDate;
				Requests::sendNotificationsEmails($model,1);
				array('id' => $model->id);
			}
			echo Requests::getStatusLabel($status);				
		}
		elseif(isset($_POST['Requests'])){
			$id = (int) $_POST['Requests']['id'];
			$status = $_POST['Requests']['status'];
			$model = $this->loadModel($id);
			$usr_request = UserPersonalDetails::getUserDetails($model->user_id);
			if (($usr_request['line_manager'] != Yii::app()->user->id && (Users::checkIfDirector(Yii::app()->user->id))==NULL)  || $model->user_id == Yii::app()->user->id){
				throw new CHttpException(403,"You have to be the designated line manager.");
			}				
			$model->status = $status;
			if ($model->save()){
				Requests::sendNotificationsEmails($model,2);
				array('id' => $model->id);
			}
			echo Requests::getStatusLabel($status);				
		}else{
			$id=$_GET['id'];
			$status=$_GET['status']; 
			$model = $this->loadModel($id);
			$usr_request = UserPersonalDetails::getUserDetails($model->user_id);			
			if($status=='0'){
				if($model->user_id != Yii::app()->user->id){
					throw new CHttpException(404,'You have to be the user who logged this vacation.');
				}
				$this->render('update', array('model' => $model));
				exit;
			}else{
			if(($usr_request['line_manager'] != Yii::app()->user->id && (Users::checkIfDirector(Yii::app()->user->id))==NULL) || $model->user_id == Yii::app()->user->id){
				throw new CHttpException(404,'You have to be the designated line manager.');
			}
			if($model->status != $status){			
				$model->status = $status;
				if ($model->save()){
					Requests::sendNotificationsEmails($model,2);
					array('id' => $model->id);
				}
			}
		}
		}
		$this->redirect(Yii::app()->createAbsoluteUrl('/requests'));
	}	
	public function actionCreate(){
		$model = new Requests();
		$this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
				$this->action_menu,
				array(
						'/requests/create' => array(
								'label'=> 'New Leave Request',
								'url' => array('requests/create'),
								'itemOptions'=>array('class'=>'link'),
								'subtab' => '',
								'order' => Utils::getMenuOrder()+1
						)
				)
		))));
		Yii::app()->session['menu'] = $this->action_menu;		
		$error = false;
		$resp = 0;
		if (isset($_POST['Requests'])){				 
			$model->type = $_POST['Requests']['type'];
			$model->startDate = $_POST['Requests']['startDate'];
			$model->endDate = $_POST['Requests']['endDate'];
			if($_POST['Requests']['inLieuOf1']!= null && !empty($_POST['Requests']['inLieuOf1']) && isset($_POST['Requests']['inLieuOf1'])){
				$model->inLieuOf1 = $_POST['Requests']['inLieuOf1'];
			}
			if($_POST['Requests']['inLieuOf2']!= null && !empty($_POST['Requests']['inLieuOf2']) && isset($_POST['Requests']['inLieuOf2']))	{
				$model->inLieuOf2 = $_POST['Requests']['inLieuOf2'];
			}
			$model->user_id = Yii::app()->user->id;				
			$start= str_replace('/', '-', $model->startDate);
			$end= str_replace('/', '-', $model->endDate);  
			if (strtotime($start)>	strtotime($end) ){
				$model->addError('endDate', 'End date cannot be earlier than start date');
				$error = true;
			}
			if(isset($_POST['Requests']['description'])){
				$model->description=$_POST['Requests']['description'];
			}
			if (!$error && $model->validate()){					
				if(($_POST['Requests']['type']=='90')){
					if(isset($_POST['Requests']['halfday'])){

								$halfday=$_POST['Requests']['halfday'] ;
							}else{
								$halfday=null;
							}
						if (isset($_POST['Requests']['cc'])){
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],$_POST['Requests']['inLieuOf1'],$_POST['Requests']['inLieuOf2'],$halfday,$_POST['Requests']['description'],$_POST['Requests']['cc']);
								}else{
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],$_POST['Requests']['inLieuOf1'],$_POST['Requests']['inLieuOf2'],$halfday,$_POST['Requests']['description']);
								}	
				}else{ 
					if(($_POST['Requests']['type']=='533')||($_POST['Requests']['type']=='534')){						
						if (isset($_POST['Requests']['cc']))
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],null,null,null,$_POST['Requests']['description'],$_POST['Requests']['cc']);
						else
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],null,null,null,$_POST['Requests']['description']);		
					} else if(($_POST['Requests']['type']=='91')){
						if(isset($_POST['Requests']['halfday'])){
								$halfday=$_POST['Requests']['halfday'] ;
							}else{
								$halfday=null;
							}
						if (isset($_POST['Requests']['cc']))							
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],'','',$halfday,'',$_POST['Requests']['cc']);
						else
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],'','',$halfday,'');
					}else{
						if (isset($_POST['Requests']['cc']))
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],'','','','',$_POST['Requests']['cc']);
						else
							$resp = Requests::createRequest($_POST['Requests']['type'], $_POST['Requests']['startDate'], $_POST['Requests']['endDate'],'','','','');
									
						}
				}	
			}			
		}
			if ($resp >0 && !$error){
			Utils::closeTab(Yii::app()->request->url);
			$this->redirect(array(Utils::getMenuOrder(true)));			
		}else{
			$this->render('create', array('model' => $model));
		
		}
	}
	public function loadModel($id){
		$model = Requests::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	public function actionDayoffRequest() {
		$date=$_POST['dat'];  
		$id_user=Yii::app()->user->id;
		$valid= Yii::app()->db->createCommand("SELECT count(1) from user_time where `date`= DATE_FORMAT(STR_TO_DATE('".$date."', '%d/%m/%Y'), '%Y-%m-%d') and `status`<>'3' and amount>0 and id_task not in ('90' , '91' , '92' ,'93' ) and id_user='".$id_user."' ")->queryScalar();
		$returnArr['valid']=$valid;		
		echo json_encode($returnArr);
	}
	public function actionVacationDaysHRRequest() {
		$year=$_POST['selected']; 
		$start= $_POST['start']; 
		$id_user=Yii::app()->user->id;
		$user_branch=Requests::getUserBranch();
		$sum = 0;
		$days_allowed=0;
		$valid=1;
		$stratd=DateTime::createFromFormat('m/d/Y', $start);
		$mon=date("m", strtotime($stratd->format('Y-m-d')));
		if($mon == 1 )
		{
			$year = date("Y")-1;
		}else{
			$year = date("Y");
		}
		$id_user = $id_user;
		$id_vac = 13;
		$requested=0;
		/*$startdaterequest= Yii::app()->db->createCommand("SELECT startDate as start,endDate as end from requests where type=91 and user_id=".$id_user." and ((MONTH(startDate)!='01' and YEAR(startDate)=YEAR(CURRENT_DATE())) or (MONTH(startDate)='01' and YEAR(startDate)=(YEAR(CURRENT_DATE())+1))) and ((MONTH(endDate)!='01' and YEAR(endDate)=YEAR(CURRENT_DATE())) or (MONTH(endDate)='01' and YEAR(endDate)=(YEAR(CURRENT_DATE())+1))) and status=1")->queryAll();
		foreach ($startdaterequest as $row) {
			$rowstart=$row['start'];
			$rowend=$row['end'];
			$begin = new DateTime($rowstart);
			$end = new DateTime($rowend );
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end); 
			$extra=true;
			foreach ($period as $date) {
				$date = $date->format('Y-m-d H:i:s');
				$date = strtotime($date);
  				$date = date("l", $date);
 				$date = strtolower($date);
  				if($user_branch!="UAE"){
  					if($date == "saturday" || $date == "sunday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}else{
  					if($date == "saturday" || $date == "friday"){
  					} 
  					else {
  						if($extra){
  							$extra=false;
  							$sum++;
  						}
   						$sum++;
  					}
  				}
    		}
		}		*/

		$total = 0;
		//print_r($year);exit;
		for ($k = 2;$k <= 12; $k++) { 
			$total += Timesheets::getVacationsDays($id_user, $year, $k);
		}
		$januaryHours =  Timesheets::getVacationsDays($id_user, $year+1, 1);
		$sum= $total +$januaryHours;

		$days_allowed=(int) YII::app()->db->createCommand("SELECT uhd.annual_leaves from user_personal_details uhd  WHERE uhd.id_user=".$id_user." ")->queryScalar();
		$diff=$days_allowed-$sum;
		if($diff<=0)
			$valid=0;
		else{
			$valid=1;
		}
		$returnArr['valid']=$valid;
		$returnArr['daysleft']=$diff;
		echo json_encode($returnArr);
	}
}
?>