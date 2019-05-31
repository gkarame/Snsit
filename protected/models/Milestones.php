<?php
class Milestones extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'milestones';
	}
	public function rules(){
		return array(
			array('id_category, milestone_number, description', 'required'),
			array('id_category, milestone_number', 'numerical', 'integerOnly'=>true),
			array('id, id_category, milestone_number, description', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idCategory' => array(self::BELONGS_TO, 'Codelkups', 'id_category'),
			'projectsMilestones' => array(self::HAS_MANY, 'ProjectsMilestones', 'id_milestone'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_category' => 'Id Category',
			'milestone_number' => 'Milestone Number',
			'description' => 'Description',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_category',$this->id_category);
		$criteria->compare('milestone_number',$this->milestone_number);	$criteria->compare('description',$this->description,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getMilestoneNumber($id){
		return Yii::app()->db->createCommand()
					->select('milestone_number')
    				->from('milestones')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}	
	public static function getMilestoneDescription($id){
		return Yii::app()->db->createCommand()
					->select('description')
    				->from('milestones')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}
	public static function getMilestoneDescriptionTemplate($id, $project){
		$milestone=  Yii::app()->db->createCommand()
					->select('description')
    				->from('milestones')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
    	$template = Yii::app()->db->createCommand('SELECT template from projects where id='.$project.' ' )->queryScalar();		
		if (($milestone == 'SOP Design and Documentation' || $milestone == 'SOP Sign Off') && $template == 2){
			$milestone = str_replace("SOP","Integration",$milestone);;
		}	
		return $milestone;	
	}
	public static function getMilestoneDescriptionShort($id){
		$milestone= Yii::app()->db->createCommand()
					->select('description')
    				->from('milestones')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
					
		if ($milestone == 'Development of Customizations and Integration'){
			$milestone = 'Development';
		}else if ($milestone == 'SOP DESIGN AND DOCUMENTATION'){
			$milestone == 'SOP';
		}		
		return $milestone;
	}	
	public static function getAllMilestonesByCtegoryId($id_category){
		return Yii::app()->db->createCommand('SELECT id, milestone_number from milestones where id_category='.$id_category.' order by milestone_number' )->queryAll();		
	}
	public static function getAllMilestonesByCtegoryIdTemplate($id_category, $template){
		if($template  == 4) //rollout
		{
			return Yii::app()->db->createCommand('SELECT id, milestone_number from milestones where id_category='.$id_category.' and id not in (2,3,4,16) order by milestone_number' )->queryAll();		
		}
		else if($template  == 2) //integration
		{
			return Yii::app()->db->createCommand('SELECT id, milestone_number from milestones where id_category='.$id_category.' and id not in (2,16) order by milestone_number' )->queryAll();		
		}else if($template  == 5 || $template == 6 ) //consultancy and customizations
		{
			return Yii::app()->db->createCommand('SELECT id, milestone_number from milestones where id_category='.$id_category.' and  id  in (9,5,17,18,19)  order by milestone_number' )->queryAll();		
		}

		else if ($template!=7){
			return Yii::app()->db->createCommand('SELECT id, milestone_number from milestones where id_category='.$id_category.' order by milestone_number' )->queryAll();		
		}
	}
	public static function getAllMilestonesByCtegoryIdDDL($id_category, $checklist){
		$list = array();
		if($checklist){
			$milestones = Yii::app()->db->createCommand('SELECT id, description from milestones where id_category='.$id_category.' and id in (select distinct(id_phase) from checklist)')->queryAll();		
		}else{
			$milestones = Yii::app()->db->createCommand('SELECT id, description from milestones where id_category='.$id_category.' and id in (select distinct(id_phase) from checklist)')->queryAll();		
		}		
		foreach ($milestones as $key => $miles) {	$list[$miles['id']] = $miles['description'];}
		return $list;
	}	
	public static function getStatus($id_milestone,$status,$id_project){
		return CHtml::dropDownlist('status', $status, array('Pending'=>'Pending','In Progress'=>'In Progress','Closed'=>'Closed'), array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInputMilestone('."value".','. $id_milestone.','.$id_project.','."1".')',
		    	'style'=>'width:105px;border:none;',
		    	'prompt'=>""
		    ));
	}
	public static function getAlert($milestone){		
		$m=Yii::app()->db
		->createCommand("SELECT count(*) FROM projects_milestones WHERE id= '$milestone' and  applicable='Yes'  and 
((`status` not in ('Closed') and estimated_date_of_completion < CURRENT_DATE()) OR (`status` ='Pending' and estimated_date_of_start<CURRENT_DATE()))")->queryScalar();
		if ($m>0){
			return true;
		}else{
			return false;
		}
	}
	public static function getApplicable($id_milestone,$status,$id_project){
		return CHtml::dropDownlist('applicable', $status, array('Yes'=>'Yes','No'=>'No'), array(
		        'class'     => 'assigned_to',
		    	'onchange'=>'changeInputMilestone('."value".','. $id_milestone.','.$id_project.','."3".')',
		    	'style'=>'width:105px;border:none;',
		    	'prompt'=>""
		    ));
	}
}?>