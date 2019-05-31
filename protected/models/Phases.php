<?php
class Phases extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'phases';
	}
	public function rules(){
		return array(
			array('id_category, phase', 'required'),
			array('id_category', 'numerical', 'integerOnly'=>true),
			array('id, id_category, phase', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_category' => 'Id Category',
			'phase' => 'Phase',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);		$criteria->compare('id_category',$this->id_category);	$criteria->compare('phase',$this->phase,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getAllByCategoryId($id_category){
		$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where id_category='.(int)$id_category)->queryAll();		
		return $phases;
	}
	public static function getAllByCategoryTemplate($id_category, $template){
		if($template  == 1 || $template == 3) //default and opsi
		{
			$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where template=1 and id_category='.(int)$id_category)->queryAll();		
		}else if($template  == 2) //integration
		{
			$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where (template=1 or template= 2) and id not in (4,2,3,9,21) and id_category='.(int)$id_category)->queryAll();		
		}else if($template  == 4) //rollout
		{
			$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where template=1 and id not in (3,4) and id_category='.(int)$id_category)->queryAll();		
		}else if($template  == 5 || $template ==6 ) //consultancy and customizations
		{
			$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where (template=1 or template= 5) and id not in (5,1,2,3,4,9,20,21,6,7, 8) and   id_category='.(int)$id_category)->queryAll();		
		}else if($template  == 7) //integration
		{
			$phases = Yii::app()->db->createCommand('SELECT id,phase,phase_number from phases where template=1 and id =20')->queryAll();		
		}
		return $phases;
	}
	public static function getname($phase){
		$phase = Yii::app()->db->createCommand("SELECT phase from phases where id='$phase'")->queryScalar();		
		return $phase;
	}
}?>