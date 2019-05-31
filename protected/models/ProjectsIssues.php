<?php
class ProjectsIssues extends CActiveRecord{
	const STATUS_PENDING = 0;	const STATUS_FIXED = 1;	const STATUS_CLOSED = 2;	const STATUS_CANCELLED = 3;
	const PRIORITY_LOW = 0;	const PRIORITY_MEDIUM = 1;	const PRIORITY_HIGH = 2;
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName(){
		return 'projects_issues';
	}
	public function rules(){
		return array(
			array('id_project, id_issue, type ,status, priority, description,logged_by, lastupdateby,lastupdateddate, logged_date, module', 'required'),
			array('id_project, id_issue, status, priority,logged_by, module ', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>2000),	
			array('type', 'length', 'max'=>15),		array('fbr', 'length', 'max'=>500),	
			array('file', 'length', 'max' => 1000),	
			array('fix', 'length', 'max' => 2000),	
			array('notes', 'length', 'max' => 2000),	
			array('id, id_project, id_issue, priority,status', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idUser' => array(self::BELONGS_TO, 'users', 'logged_by'),
			'idProject' => array(self::BELONGS_TO, 'Projects', 'id_project'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_project' => 'Project',
			'id_issue' => 'Issue#',
			'status' => 'Status',
			'type' => 'Type',
			'fix' => 'Fix',
			'notes' => 'Notes',
			'fbr' => 'FBR',
			'priority' => 'Priority',
			'description' => 'Description',
			'logged_by' => 'Logged By',
			'logged_date' => 'Logged Date',
			'lastupdateby' => 'Last Updated By',
			'lastupdateddate' => 'Last Updated Date',
			'fixed_date' => 'Fixed Date',
			'close_date' => 'Closed Date',
			'module' => 'Module',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('status',$this->status);	$criteria->compare('priority',$this->priority);		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getTypeList(){
		return array(
			'Base Bug' => 'Base Bug',
			'Interface' => 'Interface',
			'FBR' => 'FBR', 
			'Change Request' => 'Change Request' 			
		); 
	}
	public static function getAsignUsers($id_task){
		return 	Yii::app()->db->createCommand('SELECT Distinct u.id, u.username, u.firstname, u.lastname FROM users u left join user_issues on user_issues.id_user=u.id where id_issue in ('.$id_task.')')->queryAll();
	}
	public static function getAssignedto($id){
		$result =  Yii::app()->db->createCommand('SELECT id_user from user_issues where id_issue= '.$id.' ')->queryAll();	$name = "";
		foreach($result as $res){
			$name.=Users::getCredentialsbyId($res['id_user']) .', ';
		}
		//$name= substr($name,0, (strlen($name)-1));
		return $name;
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_PENDING => 'Pending',
			self::STATUS_FIXED => 'Fixed', 
			self::STATUS_CLOSED => 'Closed',
			self::STATUS_CANCELLED => 'Cancelled' 			
		); 
	}
	public function renderNumber()
	{
		echo '<a class="show_link" href="'.Yii::app()->createUrl("projects/issue", array("id" => $this->id)).'">'.str_pad($this->id_issue, 3, "0", STR_PAD_LEFT).'</a>';
	}
	public static function getStatusCustomList($s){	
	if($s == null ){	
		return array(
			self::STATUS_PENDING => 'Pending'); 
		}else if ($s==0)
		{
return array(
	self::STATUS_PENDING => 'Pending',
			self::STATUS_FIXED => 'Fixed', 
			self::STATUS_CANCELLED => 'Cancelled' 			
		); 
		}else if($s==1)
		{
return array(
	self::STATUS_FIXED => 'Fixed', 
			self::STATUS_PENDING => 'Pending',
			self::STATUS_CLOSED => 'Closed',			
		); 

		}else{			
			return array(
			self::STATUS_PENDING => 'Pending',
			self::STATUS_FIXED => 'Fixed', 
			self::STATUS_CLOSED => 'Closed',
			self::STATUS_CANCELLED => 'Cancelled' 			
		); 
		}
	}
	
	public static function getPriorities(){		
		return array(			
			self::PRIORITY_MEDIUM => 'Medium', 
			self::PRIORITY_LOW => 'Low',
			self::PRIORITY_HIGH => 'High'			
		); 
	}
	public static function getperstatusperproject($project){
		$project = (int) $project;
		$items= Yii::app()->db->createCommand("SELECT distinct(status), count(*) as items  FROM `projects_issues` where id_project='$project' and  status<>'3' group By status")->queryAll();
		return $items;
	}
	public static function getlastissue($id_project){		
		$id= Yii::app()->db->createCommand("SELECT max(id_issue)+1 from projects_issues WHERE id_project=".$id_project." ")->queryScalar();
		if($id==0) { return 1;}else{ return $id;}
	}
	public static function getStatus($status){
		if($status == 0){ return 'Pending';}elseif ($status == 1) { return 'Fixed';} elseif ($status == 2) { return 'Closed';}elseif ($status == 3) { return 'Cancelled';}
	}
	public static function getTotalStatus($value, $project){
		$result = Yii::app()->db->createCommand("Select count(1) from projects_issues where status = '$value' and id_project= '$project' ")->queryScalar();
		return $result;	
	}
	public static function getTotalIssues($project){
		$result = Yii::app()->db->createCommand("Select count(1) from projects_issues where id_project= '$project' ")->queryScalar();
		return $result;	
	}
	public static function checkOpenIssues($project){
		$project = (int) $project;
		return Yii::app()->db->createCommand("SELECT count(1) FROM projects_issues	WHERE id_project= '$project' and `status` in (0,1)")->queryScalar();
	}
	public static function getStatusContor($project){
		$status = array();
		for ($i=0; $i<4; $i++)	{	$status[$i] = self::getTotalStatus($i, $project);	}
		return $status;
	}

	private function addAttachments() {
		if (Yii::app( )->user->hasState('attach_conn')) {
	        $attachments = Yii::app( )->user->getState('attach_conn');
	        $path = Yii::app()->getBasePath().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$this->id_project.DIRECTORY_SEPARATOR.'issues'.DIRECTORY_SEPARATOR.$this->id_issue.DIRECTORY_SEPARATOR;
	        if( !is_dir($path))   {
	            mkdir($path, 0777, true);
	            chmod($path, 0777);
	        }
	        foreach ($attachments as $attach){
	            if (is_file( $attach["path"])){
	                if (rename( $attach["path"], $path.$attach["name"])){
	                    chmod($path.$attach["name"], 0777);
	                    self::model()->updateByPk($this->id, array('file' => $attach["name"]));
	                }
	            } 
	        }
	        Yii::app( )->user->setState( 'attach_conn', null );
	    }
	}	
	public function getFile($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$this->id_project.DIRECTORY_SEPARATOR.'issues'.DIRECTORY_SEPARATOR.$this->id_issue.DIRECTORY_SEPARATOR.$this->file;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$this->id_project.DIRECTORY_SEPARATOR.'issues'.DIRECTORY_SEPARATOR.$this->id_issue.DIRECTORY_SEPARATOR.$this->file;
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public function getFileDownload(){
		$filePath = 'uploads'.DIRECTORY_SEPARATOR.'projects'.DIRECTORY_SEPARATOR.$this->id_project.DIRECTORY_SEPARATOR.'issues'.DIRECTORY_SEPARATOR.$this->id_issue.DIRECTORY_SEPARATOR.$this->file;
		return $filePath;
	}
	public function getFileName(){
		$path = $this->getFile(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}
	public static function getUsersGrid($sir){
		if ($sir != '')	{
			$pos1 = strpos($sir, '|');	$pos2 = strpos($sir, '|', $pos1 + strlen('|'));			
			if($pos2 == null || $pos2 == strlen($sir)-2)
				return
					'<div class="first_it panel_container width100">'
							.'<div class="item_clip">'.substr(trim($sir),0,-1).'</div>'
					.'</div>';
			else
				$short_sir = Utils::getShortText($sir,$pos2);				
			return '<div class="first_it panel_container width100">'
							.'<div class="item_clip" style="float:left" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">'.$short_sir['text'].'</div>'
							.'<u class="red" onmouseover="showToolTip(this);" onmouseout="hideToolTip(this);">+</u>'
							.'<div class="panel" style="left:80px;">'
								.'<div class="phead"></div>'
								.'<div class="pcontent"><div class="cover">'.substr(trim($sir),0,-1).'</div></div>'
								.'<div class="pftr"></div>'
							.'</div>'
						.'</div>';
		}
		return "";
	}

	public function renderAttachment(){
		if(!empty($this->getFileName()))
		{
			echo '<a href="'. Yii::app()->createUrl("site/download", array("file" => $this->getFileDownload())).'"><img style="" width="18"  src="'.Yii::app()->request->baseUrl.'/images/filep.png"></img></a>';
		}else{
			echo '';
		}
	}
	public static function getFbrsList($id_project){
$result =  Yii::app()->db->createCommand("SELECT pt.id,pt.description as descr from projects_tasks pt, projects_phases pp
where pp.id_project= ".$id_project." and pt.description like '%FBR%' and pt.description not in ('FBRXXX','FBR Testing','FBR Documentation') and  pp.id=pt.id_project_phase and pp.description like '%Development%' order by descr")->queryAll();
		$fbrs = array();	foreach ($result as $i=>$res){	$fbrs[$res['id']]= $res['descr'];	}	return $fbrs;
	}
	public static function loggedlastWeek($id_project){
	return Yii::app()->db->createCommand("select count(1) from projects_issues where id_project=".$id_project." and logged_date> CURRENT_DATE - INTERVAL 1 WEEK")->queryScalar();		
	}
	public static function closedlastWeek($id_project){
	return Yii::app()->db->createCommand("select count(1) from projects_issues where id_project=".$id_project." and status =2 and logged_date> CURRENT_DATE - INTERVAL 1 WEEK")->queryScalar();		
	}
	public static function fixedlastWeek($id_project){
	return Yii::app()->db->createCommand("select count(1) from projects_issues where id_project=".$id_project." and status =1 and logged_date> CURRENT_DATE - INTERVAL 1 WEEK")->queryScalar();		
	}
	public static function getIssuesPending($id_project){
	return Yii::app()->db->createCommand("select * from projects_issues where id_project=".$id_project." and status=0 order by status, priority DESC")->queryAll();		
	}
	public static function getIssuesupdated($id_project){
	return Yii::app()->db->createCommand("select * from projects_issues where id_project=".$id_project." and lastupdateddate > (CURRENT_DATE() - INTERVAL 1 DAY) and logged_date < CURRENT_DATE()  order by status, priority DESC")->queryAll();		
	}

	public static function getIssuesCreated($id_project){
	return Yii::app()->db->createCommand("select * from projects_issues where id_project=".$id_project." and logged_date > (CURRENT_DATE() - INTERVAL 1 DAY) order by status, priority DESC")->queryAll();		
	}
	
	public static function getIssuesperStatus($id_project, $status){
	return Yii::app()->db->createCommand("select * from projects_issues where id_project=".$id_project." and status =".$status." order by priority DESC")->queryAll();		
	}
	public static function getIssuesPendingorFixed($id_project){
	return Yii::app()->db->createCommand("select * from projects_issues where id_project=".$id_project." and status in (0,1) order by status, priority DESC")->queryAll();		
	}
	public static function getprio($p){
		if($p == '0'){ return "Low"; }else if ($p == '1' ){ return "Medium"; }else {return "High"; }
	}
	public static function getUsersProj($project){
return Yii::app()->db->createCommand("SELECT upd.email FROM `user_personal_details` upd, users u where upd.id_user in (SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and ((pp.phase_number >=5 and pp.description !='Installations and Technical Trainings') or p.id_type=28) ) and u.id= upd.id_user and u.active=1")->queryAll();
	}

	public static function getUsersProjLM($project){
	$allemails= Yii::app()->db->createCommand("SELECT upd.email, upd.id_user, upd.line_manager FROM `user_personal_details` upd, users u where upd.id_user in (SELECT distinct(uta.id_user) FROM projects p , projects_phases pp , projects_tasks pt , user_task uta WHERE uta.id_task=pt.id and pt.id_project_phase=pp.id and pp.id_project=p.id and p.id=".$project." and ((pp.phase_number >=5 and pp.description !='Installations and Technical Trainings') or p.id_type=28) ) and u.id= upd.id_user and u.active=1")->queryAll();
	$emails = array_column($allemails, 'email');
		$users = array_column($allemails, 'id_user');
		foreach ($allemails as  $value) {
			if(!in_array($value['line_manager'], $users))
			{
				$email= UserPersonalDetails::getEmailById($value['line_manager']);
				array_push($emails, $email);
			}
			
		}
		return $emails;
	}


}?>