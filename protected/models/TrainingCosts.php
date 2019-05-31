<?php
class TrainingCosts extends CActiveRecord{
	CONST COST_TYPE_ACCOMODATION = 1;	CONST COST_TYPE_TRAVEL_EXPENSE = 2;	CONST COST_TYPE_MATERIAL = 3;	CONST COST_TYPE_OTHER = 4;
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'training_costs';
	}
	public function rules()	{
		return array(
			array('cost_type,amount,id_training', 'required'),
			array('cost_type,id_training', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('id,cost_type,amount', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eTraining' => array(self::BELONGS_TO, 'TrainingsNewModule', 'id_training'),
		);
	}
	protected function beforeValidate(){
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }
	public function addCustomError($attribute, $error) {       $this->customErrors[] = array($attribute, $error);    }
    public static function getCostTypeLabel($id){	$list = self::getCostTypeList();	return $list[$id];    }
    public static function getCostTypeList(){
    	return (array(self::COST_TYPE_ACCOMODATION => 'Venue Expenses',
    		self::COST_TYPE_TRAVEL_EXPENSE => 'Travel Expenses',
    		self::COST_TYPE_MATERIAL => 'Material Expenses',
    		self::COST_TYPE_OTHER => 'Other Expenses'));
    }
    public function attributeLabels(){
		return array(
			'id' => 'ID',
			'cost_type' => 'Type',
			'amount' => 'Amount',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('cost_type',$this->cost_type); $criteria->compare('amount',$this->amount);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getCostsProvider($id_training){
		$criteria=new CDbCriteria;	$criteria->condition = "(id in (Select id from training_costs where id_training =".$id_training."))";	
		return new CActiveDataProvider('TrainingCosts', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
}
?>