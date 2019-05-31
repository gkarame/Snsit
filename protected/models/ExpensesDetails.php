<?php
class ExpensesDetails extends CActiveRecord{
	const TYPE = 15; 	const CURRENCIES = 8;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'expenses_details';
	}
	public function rules(){
		return array(
			array('expenses_id, type, original_amount, original_currency, amount, currency_rate_id, billable, payable, date', 'required'),
			array('expenses_id, type, original_currency, currency, currency_rate_id', 'numerical', 'integerOnly'=>true),
			array('date', 'type', 'type' => 'date', 'message' => '{attribute} is not valid', 'dateFormat' => 'dd/MM/yyyy'),
			array('original_amount, amount', 'numerical'),
			array('billable, payable', 'length', 'max'=>3),
			array('id, expenses_id, type, original_amount, original_currency, amount, currency, currency_rate_id, billable, payable, date, notes', 'safe', 'on'=>'search'),
		);
	}
	public function beforeSave(){
		if (parent::beforeSave()){
			$this->date = DateTime::createFromFormat('d/m/Y', $this->date)->format('Y-m-d');
			return true;
		}
		return false;
	}
	public function relations(){
		return array(
			'currencyRate' => array(self::BELONGS_TO, 'CurrencyRate', 'currency_rate_id'),
			'expenses' => array(self::BELONGS_TO, 'Expenses', 'expenses_id'),
			'type0' => array(self::BELONGS_TO, 'Codelkups', 'type'),
			'currency0' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'currency1' => array(self::BELONGS_TO, 'Codelkups', 'original_currency'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'expenses_id' => 'Expenses',
			'type' => 'Expenses category',
			'original_amount' => 'Amount',
			'original_currency' => 'Currency',
			'amount' => 'USD Amount',
			'currency' => 'Currency',
			'currency_rate_id' => 'Rate',
			'billable' => 'Billable',
			'payable' => 'Payable',
			'date' => 'Date',
			'notes' => 'Notes',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);		$criteria->compare('expenses_id',$this->expenses_id);	$criteria->compare('type',$this->type);
		$criteria->compare('original_amount',$this->original_amount);	$criteria->compare('original_currency',$this->original_currency);
		$criteria->compare('amount',$this->amount);	$criteria->compare('currency',$this->currency);	$criteria->compare('currency_rate_id',$this->currency_rate_id);
		$criteria->compare('billable',$this->billable,true);	$criteria->compare('payable',$this->payable,true);
		$criteria->compare('date',$this->date,true);	$criteria->compare('notes',$this->notes,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getNote($id){
		return Yii::app()->db->createCommand()
    				->select('notes')
    				->from('expenses_details')
    				->where('id =:id', array(':id'=>$id))
    				->queryScalar();
	}
	public static function getTypes(){
		$types = Yii::app()->db->createCommand()
    				->select('id, codelkup')
    				->from('codelkups')
    				->where('id_codelist = :id', array(':id'=>self::TYPE))
    				->queryAll();
    	$result = array();
    	foreach($types as $type)
    		$result[$type['id']] = $type['codelkup'];
    	return $result;
	}
	public static function getCurrencies(){
		$currencies = Yii::app()->db->createCommand()
    				->select('id, codelkup')
    				->from('codelkups')
    				->where('id_codelist = :id', array(':id'=>self::CURRENCIES))
    				->queryAll();
    	$result = array();
    	foreach($currencies as $currency)
    		$result[$currency['id']] = $currency['codelkup'];
    	return $result;
	}
	public static function getCurrencies2($id){
		return   Yii::app()->db->createCommand()
				->select('rate')
    			->from('currency_rate')
    			->where('currency = :id', array(':id'=>$id))
    			->order('date DESC')
    			->order('id DESC')
    			->limit('1')
    			->queryScalar();
	}
	public function getNotesGrid(){
		if ($this->notes != null)
			return '<div class="first_it panel_container panel_container_expenses" style="color:#990000 !important" onmouseover="showToolTipExpenses(this);" onmouseout="hideToolTipExpenses(this);">'.'Notes'
						.'<div class="panel_expenses">'
							.'<div class="phead"></div>'
							.'<div class="pcontent"><div class="cover">'.$this->notes.'</div></div>'
							.'<div class="pftr"></div>'
						.'</div>'
					.'</div>';
		return false;
	}
	public static function getNumberBillableItem($id_expenses){
		$number = 0;
		$result = Yii::app()->db->createCommand()
    				->select('id')
    				->from('expenses_details')
    				->where('expenses_id = :id_expenses AND billable = "Yes"',
    						 array(':id_expenses'=>$id_expenses))
    				->limit(1)
    				->queryScalar();
    	if ($result !== false){	$number = 1;}
    	return $number;
	}
}?>