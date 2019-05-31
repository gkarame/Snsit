<?php
class InvoicesExpenses extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'invoices_expenses';
	}
	public function rules(){
		return array(
			array('id_invoice, id_expenses_details, amount, currency, currency_rate_id', 'required'),
			array('id_invoice, id_expenses_details, amount, currency, currency_rate_id', 'numerical', 'integerOnly'=>true),
			array('id, id_invoice, id_expenses_details, amount, currency, currency_rate_id', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'currencyRate' => array(self::BELONGS_TO, 'CurrencyRate', 'currency_rate_id'),
			'idInvoice' => array(self::BELONGS_TO, 'Invoices', 'id_invoice'),
			'idExpensesDetails' => array(self::BELONGS_TO, 'ExpensesDetails', 'id_expenses_details'),
			'currency1' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_invoice' => 'Id Invoice',
			'id_expenses_details' => 'Id Expenses Details',
			'amount' => 'Amount',
			'currency' => 'Currency',
			'currency_rate_id' => 'Currency Rate',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_invoice',$this->id_invoice);
		$criteria->compare('id_expenses_details',$this->id_expenses_details);	$criteria->compare('amount',$this->amount);
		$criteria->compare('currency',$this->currency);	$criteria->compare('currency_rate_id',$this->currency_rate_id);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function createInvoiceExpenses($model_invoice,$expenses){ 
		$items = $expenses->expensesDetails;	$sum = 0;			
		foreach ($items as $item){
			if ($item->billable == 'Yes'){
				$inv = new InvoicesExpenses();
				$billable = $item->original_amount;
				$rate = CurrencyRate::getCurrencyRate($item->original_currency);
				if($item->original_currency != $model_invoice->currency){
					if (isset($rate['rate'])){
						$billable = $billable * $rate['rate'];
					}
				}
				$inv->id_invoice = $model_invoice->id;	$inv->id_expenses_details = $item->id;	$inv->amount = $billable;
				$inv->currency = $model_invoice->currency;	$inv->currency_rate_id = $rate['id'];	$sum += $billable;
				$inv->save();
			}
		}
		return $sum;
	}
}?>