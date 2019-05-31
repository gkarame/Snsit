<?php
class Codelists extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'codelists';
	}
	public function rules()	{
		return array(
			array('codelist, label, description', 'required'),
			array('codelist, label', 'length', 'max'=>255),
			array('id, codelist, label, description', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'codelkups' => array(self::HAS_MANY, 'Codelkups', 'id_codelist'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'codelist' => 'Codelist',
			'label' => 'Label',
			'description' => 'Description',
		);
	}
	public function getCodelkups(){
		$criteria=new CDbCriteria;	
		$criteria->compare('id_codelist', $this->id);	
		return new CActiveDataProvider('Codelkups', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
             'sort' => array(
            	'defaultOrder'=>'codelkup ASC',
            ),
		));
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('codelist',$this->codelist,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('description',$this->description,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			 'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'label ASC',        
			),
		));
	}
	public static function getIdByCodelist($codelist){
		return Yii::app()->db->createCommand("SELECT id FROM codelists WHERE codelist LIKE '$codelist'")->queryScalar();
	}
}?>