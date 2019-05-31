<?php
class SmaActions extends CActiveRecord{
	public $dataprovider;	const STATUS_OPEN = 0;	const STATUS_AWAITING = 1;	const STATUS_PROGRESS = 2;	const STATUS_SOLUTION_PROVIDED = 3;
	const STATUS_IN_MONITORING = 4;	const STATUS_CLOSED = 5;
	const RESPONSIBILITY_SNS = 'SNS';	const RESPONSIBILITY_CLIENT = 'Customer';	const RESPONSIBILITY_BOTH = 'Both';
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'sma_actions';
	}
	public function rules(){
		return array(
			array('title,responsibility,action_instance,suggested_sol,id_customer,status,severity,description,tier,addwho,eta', 'required'), //readd id_sma 
			array('status,severity,id_customer', 'numerical', 'integerOnly'=>true),
			array('suggested_sol,description', 'length', 'max'=>1000),
			array('title,responsibility,tier,action_instance', 'length', 'max'=>50),
			array('severity','validateSeverity'),
			array('id_sma,close_date','safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'sma' => array(self::BELONGS_TO, 'Sma', 'id_sma')
		);
	}
	public function validateSeverity(){
		if(!empty($this->id)){
			$result =  Yii::app()->db->createCommand("SELECT severity FROM sma_actions WHERE id=".$this->id." ")->queryScalar();
		    	if($result>$this->severity)	{	 $this->addError('severity','Cannot be changed to lower values');  	}
		}		    
	}
	public function attributeLabels(){
		return array( 
			'id_sma' => 'Sma',
			'responsibility' => 'Responsibility',
			'action_instance' => 'Instance',
			'status' => 'Status',
			'severity' => 'Severity',
			'description' => 'Description',
			'tier' => 'Tier',
			'adddate' => 'Logged On',
			'addwho' => 'Logged By',
			'close_date'=> 'Closed Date',
			'eta' => 'ETA',
			'suggested_sol'=>'Suggested Solution'
		);
	}
	public function searchSmaActions($instance, $customer, $id_sma){
		$criteria=new CDbCriteria;	$criteria->compare('id_customer',$customer);	$criteria->compare('action_instance',$instance);
		$ClosedAction= Yii::app()->db->createCommand("SELECT displayClosed FROM sma WHERE id=".$id_sma."")->queryScalar();	
		if(empty($ClosedAction)){	$criteria->compare('status','<>'.self::STATUS_CLOSED);	}		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria
		));
	}
	public static function getSmaActionsProvider($id_sma){
		$criteria= new CDbCriteria;		$criteria->condition = "status != ".self::STATUS_CLOSED."";
		return new CActiveDataProvider('SmaActions', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'title ASC',  
            ),
		));
	}
	public static function getResponsibilityTypes(){
		return array(
			self::RESPONSIBILITY_SNS => 'SNS',
			self::RESPONSIBILITY_CLIENT => 'Customer',
			self::RESPONSIBILITY_BOTH=> 'Both'
			);
	}
	public static function getSeverityTypes(){		
		return array(
			0 => 'Low',
			1 => 'Medium',
			2 => 'High',
			3 => 'Critical',	
		); 
	}
	public static function getSeverityLabel($value)	{	$list = self::getSeverityTypes();	return $list[$value];	}
	public static function getStatusList(){		
		return array(
			self::STATUS_OPEN => 'Open',
			self::STATUS_AWAITING => 'Awaiting Customer',
			self::STATUS_PROGRESS => 'In Progress',
			self::STATUS_SOLUTION_PROVIDED => 'Solution Provided',
			self::STATUS_IN_MONITORING => 'In Monitoring',
			self::STATUS_CLOSED => 'Closed',		
		); 
	}	 
	 public static function getStatusListNoClose(){		
		return array(
			self::STATUS_OPEN => 'Open',
			self::STATUS_AWAITING => 'Awaiting Customer',
			self::STATUS_PROGRESS => 'In Progress',
			self::STATUS_SOLUTION_PROVIDED => 'Solution Provided',
			self::STATUS_IN_MONITORING => 'In Monitoring'	
		); 
	}	
	public static function getActionsNumberOpen($customer, $action_instance, $severity){
		return Yii::app()->db->createCommand("SELECT count(1) FROM sma_actions WHERE id_customer=".$customer." and  action_instance='".$action_instance."'  and  severity=".$severity." and status in (0,1,2,4)")->queryScalar();
	}
	public static function getActionsNumber($sma, $severity){
		return Yii::app()->db->createCommand("SELECT count(1) FROM sma_actions WHERE id_sma=".$sma." and  severity=".$severity." ")->queryScalar();
	}	
	public static function getPendingPerCustomer($customer, $instance){
		return Yii::app()->db->createCommand("SELECT count(1) FROM sma_actions WHERE status not in (".self::STATUS_CLOSED.",".self::STATUS_SOLUTION_PROVIDED.",".self::STATUS_IN_MONITORING.") and id_customer= ".$customer." and action_instance='".$instance."'")->queryScalar();
	}
	public static function getStatusLabel($value){	$list = self::getStatusList();	return $list[$value];}
	public static function getAll($ids=null){
		if ($ids) {	}else{
			$smaactions = Yii::app()->db->createCommand("SELECT * FROM sma_actions WHERE status not in (".self::STATUS_SOLUTION_PROVIDED.",".self::STATUS_CLOSED.")")->queryScalar();
		}		
		return $smaactions;
	}
	public static function getSmaAction($id){
		$risk = Yii::app()->db->createCommand()
			->select('*')
			->from('sma_actions')
			->where('id = :id',array(':id' =>$id))
			->queryAll();
		return $risk;
	}	
	public static function build_sorter($key, $key2 = null){
	    return function ($a, $b) use ($key, $key2) {
	    	$result = strnatcmp($a[$key], $b[$key]);
	    	if ($key2 != null && $result == 0) 	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}	 
	public static function getSmaActions($id_sma){
		return  Yii::app()->db->createCommand()
			->select('*')
			->from('sma')
			->where("status <> ".self::STATUS_CLOSED." and id_sma = :id", array(':id'=>$id_sma))
			->queryAll();
		
	}
} ?>