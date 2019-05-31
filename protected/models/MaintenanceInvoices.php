<?php
class MaintenanceInvoices extends CActiveRecord{
	public static function model( $className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'maintenance_invoices';
	}
	public function rules(){
		return array(
			array('id_invoice, id_contract, escalation_factor, from_period, to_period, status, currency, amount, date', 'required'),
			array('id_invoice, id_contract, escalation_factor, currency, amount', 'numerical', 'integerOnly'=>true),
			array('status', 'length', 'max'=>8),
			array('id, id_invoice, id_contract, escalation_factor, from_period, to_period, status, currency, amount, date', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idInvoice' => array(self::BELONGS_TO, 'Invoices', 'id_invoice'),
			'currency0' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'idContract' => array(self::BELONGS_TO, 'Maintenance', 'id_contract'),
			'escalation_factor' => array(self::BELONGS_TO, 'Maintenance', 'escalation_factor'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_invoice' => 'Id Invoice',
			'id_contract' => 'Id Contract',
			'escalation_factor' => 'Esc %',
			'from_period' => 'From Period',
			'to_period' => 'To Period',
			'status' => 'Status',
			'currency' => 'Currency',
			'amount' => 'Amount',
			'date' => 'Date',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_invoice',$this->id_invoice);
		$criteria->compare('id_contract',$this->id_contract);	$criteria->compare('escalation_factor',$this->escalation_factor);
		$criteria->compare('from_period',$this->from_period,true);	$criteria->compare('to_period',$this->to_period,true);
		$criteria->compare('status',$this->status,true);	$criteria->compare('currency',$this->currency);
		$criteria->compare('amount',$this->amount);	$criteria->compare('date',$this->date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public static function getLastInvoice($id_contract){
		return Yii::app()->db->createCommand()
    			->select('*')
    			->from('maintenance_invoices')
    			->where('id_contract = :id_maintenance',array(':id_maintenance'=>$id_contract))
    			->order('id DESC')
    			->limit(1)
    			->queryRow();
	}	
	public static function getLastInvoicebyDate($id_contract){
		return Yii::app()->db->createCommand()
    			->select('*')
    			->from('maintenance_invoices')
    			->where('id_contract = :id_maintenance',array(':id_maintenance'=>$id_contract))
    			->order('to_period DESC, id_invoice DESC')
    			->limit(1)
    			->queryRow();
	}	
	public static function getMonth($datee){
		$last_to = date("n",strtotime($datee));
		$today = date('n');
		if($last_to == $today )
			return true;
		return false;
	}	
	public function getAmountUsd(){
		if ($this->idInvoice->currency != CurrencyRate::OFFICIAL_CURRENCY){
		 	$rate = CurrencyRate::getCurrencyRate($this->idInvoice->currency);
		   	if (isset($rate['rate'])){
		   		$AmountUSD = $this->amount * $rate['rate'];
		   	}
		}else{ 
			$AmountUSD = $this->amount;
		}	
		return $AmountUSD;	
	} 
	public static function getTotalPerYear($contract, $year){
		$amount_usd =  Yii::app()->db->createCommand("select case when r.currency=9 
			THEN r.gross_amount 
			else r.gross_amount *(select c.rate from currency_rate c where c.currency=r.currency   order by date DESC  limit 1) end as gross_amount
			from invoices r	where r.status !='Cancelled' and r.id in (select id_invoice from maintenance_invoices where id_contract=".$contract." and YEAR(from_period) =".$year." )  ")->queryAll();
		if(empty($amount_usd))
		{
			$ct= 0;
		}else{
			$ct = array_sum(array_column($amount_usd,'gross_amount'));
		}
		return $ct;
		
	}	

	public static function getESCPerYear($contract, $year){
		$esc =  Yii::app()->db->createCommand("select max(escalation_factor) from maintenance_invoices where id_contract=".$contract." and YEAR(from_period) =".$year." LIMIT 1  ")->queryScalar();
		if(empty($esc))
		{
			return 0;
		}else{
			return $esc;
		}
	}
}	?>