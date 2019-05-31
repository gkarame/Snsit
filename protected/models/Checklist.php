<?php
class Checklist extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'checklist';
	}
	public function rules(){
		return array(
			array('category, checklist_number,descr, responsibility, id_phase', 'required'),
			array('id_phase,checklist_number', 'numerical', 'integerOnly'=>true),
			array('id, category, descr, responsibility, id_phase,checklist_number', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(			
			'projectsChecklist' => array(self::HAS_MANY, 'ProjectsChecklist', 'id_checklist'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'category' => 'Category',
			'checklist_number' => 'Checklist Number',
			'descr' => 'Description',
			'responsibility' =>'Responsibility',
			'id_phase' => 'ID Phase'
		);
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('category',$this->category);
		$criteria->compare('checklist_number',$this->checklist_number);
		$criteria->compare('description',$this->descr,true);
		$criteria->compare('responsibility',$this->responsibility,true);
		$criteria->compare('id_phase',$this->id_phase,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getChecklistNumber($id){
		return Yii::app()->db->createCommand()
					->select('checklist_number')
    				->from('checklist')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}	
	public static function getMilestoneDescription($id){
		return Yii::app()->db->createCommand()
					->select('descr')
    				->from('checklist')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}	
	public static function getCategory($id){
		return Yii::app()->db->createCommand()
					->select('category')
    				->from('checklist')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}
public static function getresponsibility($id){
		return Yii::app()->db->createCommand()
					->select('responsibility')
    				->from('checklist')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}	
	public static function getAllChecklistByPhaseId($id_phase){
		return Yii::app()->db->createCommand('SELECT id, checklist_number from checklist where id_phase='.$id_phase)->queryAll();		
		
	}
	public static function getAll(){
		return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where type='default' ")->queryAll();		
		
	}
	public static function getAllPerTemplate($template){
		if($template == 2)//integration
		{
			return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where type='default' and id in (13,14,15,16,32,35,48,53,54,55,64,68,73,80,85, 114) ")->queryAll();	
		}else if($template == 3)//opsi
		{
			return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where type='opsi' and id not in (113)")->queryAll();	
		}else if($template == 4)//rollout
		{
			return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where  type='default' and id not in ( 0, 5,7,11,69,72,73,74,75,76,78,79,80,81,82,83,84,85,71,116 )")->queryAll();	
		}
		else if($template != 1)//not STD
		{
			return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where type='default' and id not in (71,72,73,78,85,116 )")->queryAll();	
		}
		else{
			return Yii::app()->db->createCommand("SELECT id, checklist_number, id_phase from checklist where type='default' and id not in (71,72,73,78,85 )")->queryAll();	
		}					
	}	
	
	public static function getStatus($id_checklist,$status,$id_project){
		return CHtml::dropDownlist('status', $status, array('Open'=>'Open','Completed'=>'Completed','N/A'=>'N/A'), array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInputchecklist('."value".','. $id_checklist.','.$id_project.')',
		    	'style'=>'width:105px;border:none;',
		    	'prompt'=>""
		    ));
	}
	public static function getStatusList(){
		return array('Open'=>'Open','Completed'=>'Completed','N/A'=>'N/A');
	}
	public static function getresponsibilityList(){
		return array('Client'=>'Client','Client/SNS'=>'Client/SNS','Ops'=>'Ops','SNS'=>'SNS','Sys Admin'=>'Sys Admin','Tech'=>'Tech');
	}
	public static function getTypePerId($id){
		return Yii::app()->db->createCommand("SELECT type from checklist where id=".$id." ")->queryScalar();		
		
	}
	public static function getPhasePerId($id){
		return Yii::app()->db->createCommand("SELECT id_phase from checklist where id=".$id." ")->queryScalar();		
		
	}
	public static function checkpermperItem($check, $project){		
		$checknb= yii::app()->db->createCommand("Select id_checklist from projects_checklist where id= ".$check." ")->queryScalar();
		$phase= Checklist::getPhasePerId($checknb);
		if ($phase == 9){
			$userid=Yii::app()->user->id;
			$checkflagQA=yii::app()->db->createCommand("Select count(*) from `user_personal_details` where id_user= ".$userid." and (pqa='1' or id_user= (select cs_representative from customers where id= (select customer_id from projects where id=".$project.")))")->queryScalar();
			if ($checkflagQA>0){
				return true;
			}else{
				return false;
			}
		}else if (GroupPermissions::checkPermissions("projects-milestones","write")){
			return true;
		}		
	}
}?>