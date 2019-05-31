<?php
class TrainingFreeCandidates extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'trainings_free_candidates';
	}
	public function rules(){
		return array(
			array('id_customer, id_training,contact_name,contact_email', 'required'),
			array('id_training, id_customer', 'numerical', 'integerOnly'=>true),
			array('id, id_training, id_customer', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eTraining' => array(self::BELONGS_TO, 'TrainingsNewModule', 'id_training'),
			'eCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
		);
	}
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {   $this->addError($param[0], $param[1]);   }
        return $r;
    }
	public function addCustomError($attribute, $error) {   $this->customErrors[] = array($attribute, $error);   }
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_training' => 'Training',
			'id_customer' => 'Customer',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_training',$this->id_training);	$criteria->compare('id_customer',$this->id_ea);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getCandidatesProvider($id_training){
		$criteria=new CDbCriteria;	$criteria->condition = "(id in (Select id from trainings_free_candidates where id_training =".$id_training."))";
		return new CActiveDataProvider('TrainingFreeCandidates', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
} ?>