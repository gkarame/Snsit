<?php
class TrainingEas extends CActiveRecord{
	public $customErrors = array();
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'training_eas';
	}
	public function rules(){
		return array(
			array('id_training, id_ea', 'required'),
			array('id_training, id_ea', 'numerical', 'integerOnly'=>true),
			array('id, id_training, id_ea', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'eTraining' => array(self::BELONGS_TO, 'TrainingsNewModule', 'id_training'),
			'eEa' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
		);
	}
	protected function beforeValidate(){
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {       $this->addError($param[0], $param[1]);   }
        return $r;
    }
	public function addCustomError($attribute, $error) {   $this->customErrors[] = array($attribute, $error);   }
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_training' => 'Training ID',
			'id_ea' => 'Ea ID',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_training',$this->id_training);	$criteria->compare('id_ea',$this->id_ea);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getEasProvider($id_training){
		$criteria=new CDbCriteria;	
		$criteria->condition = "(id in (Select training_eas.id_ea from training_eas, eas where training_eas.id_ea = eas.id and eas.status >1 and training_eas.id_training =".$id_training."))";
		return new CActiveDataProvider('Eas', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'id ASC',  
            ),
		));
	}
}?>