<?php
class CurrencyRate extends CActiveRecord{
	const OFFICIAL_CURRENCY = 9; 
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName()	{
		return 'currency_rate';
	}
	public function rules()	{
		return array(
			array('currency, rate, date', 'required'),
			array('currency', 'numerical', 'integerOnly'=>true),
			array('rate', 'numerical'),
			array('id, currency, rate, date', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'currencyValue' => array(self::BELONGS_TO, 'Codelists', 'currency'),
			'expensesDetails' => array(self::HAS_MANY, 'ExpensesDetails', 'currency_rate_id'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'currency' => 'Currency',
			'rate' => 'Rate',
			'date' => 'Date',
		);
	}
	public function search()	{
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('date',$this->date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getCurrencyRate($id)	{	
		$rates =   Yii::app()->db->createCommand()
				->select('id, rate')
    			->from('currency_rate')
    			->where('currency = :id', array(':id'=>$id))
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
    			->from('currency_rate')
    			->where('currency = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();    	
    	return $rate;
    }
}?>