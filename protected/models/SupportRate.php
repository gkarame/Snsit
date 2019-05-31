<?php
class SupportRate extends CActiveRecord{
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName()	{
		return 'support_rate';
	}
	public function rules()	{
		return array(
			array('plan, rate, date', 'required'),
			array('plan', 'numerical', 'integerOnly'=>true),
			array('rate', 'numerical'),
			array('id, plan, rate, date', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'planValue' => array(self::BELONGS_TO, 'Codelists', 'yearly_support'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'plan' => 'Plan',
			'rate' => 'Rate',
			'date' => 'Date',
		);
	}
	public function search()	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('plan',$this->plan);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('date',$this->date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getSupportRate($id)	{	
		$rates =   Yii::app()->db->createCommand()
				->select('id, rate')
    			->from('support_rate')
    			->where('plan = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryAll();
    	$result = array();
    	if(count($rates)>0) $result = $rates[0];
    	return $result;
    }
    public static function getRate($id)	{	
		$rate  =   Yii::app()->db->createCommand()
				->select('rate')
    			->from('support_rate')
    			->where('plan = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();    	
    	return $rate;
    }
}?>