<?php 
class IncomingTransfersDetails extends CActiveRecord {
	public $customErrors = array();

	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'incoming_transfers_details';
	}
	public function rules(){
		return array(
			array('invoice_number ,original_currency,id_it,received_currency,original_amount, paid_amount, rate, received_amount, id_customer', 'required'),
			array('original_currency,id_it,received_currency,id_user,id_customer', 'numerical', 'integerOnly'=>true),
            array('original_amount,  received_amount, rate', 'numerical', 'integerOnly'=>false),
            array('final_invoice_number', 'length', 'max'=>7),
            array('invoice_number', 'length', 'max'=>1000),
            array('paid_amount', 'length', 'max'=>15),
			array('received_currency','validateCurrency'),
			array('final_invoice_number,id_it,id_customer', 'safe', 'on'=>'search'),
			);
	}
	public function relations(){
		return array(
			'IncomingTransfer' => array(self::BELONGS_TO, 'IncomingTransfers', 'id_it'),
			'eCustomer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			);
	}
	public function search(){
		$criteria=new CDbCriteria;		$criteria->compare('id',$this->id);	
		$criteria->compare('id_it',$this->id_it);	$criteria->compare('final_invoice_number',$this->final_invoice_number);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function validateCurrency(){
		$tr_currency= Yii::app()->db->createCommand("select currency from incoming_transfers where id =".$this->id_it."")->queryScalar();
	 	if ( $this->received_currency != $tr_currency ){
	 		$this->addError('received_currency','Received Currency doesnt match transfer');
	    }			
	}

	public function renderfinal_invoice_number(){
		$inv= explode(',',  $this->invoice_number);
		echo '<a class="show_link" href="'.Yii::app()->createUrl("receivables/view", array("id" => $inv[0])).'">'.$this->final_invoice_number.'</a>';
	}
	
	public function attributelabels(){
		return array(
			'invoice_number' => 'Invoice#',
			'id_customer' => 'Customer',
			'final_invoice_number' => 'Final Invoice#',
			'original_currency' => 'Original Currency',
			'received_currency' => 'Received Currency',
			'original_amount' => 'Original Amount',
			'paid_amount' => 'Paid Amount',
			'received_amount' => 'Received Amount',
		);
	}
 	public static function getPaidOptions(){
		return  array(
					1 => 'Fully',
					2 => 'Partial');
	}
	public static function getPaidLabel($value){
		if(!empty($value))
		{
			$list = self::getPaidOptions();
			return $list[$value];
		}else{
			return '';
		}
	}
	protected function beforeValidate() {
        $r = parent::beforeValidate();
        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }	
	public function addCustomError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }

	public static function displayRate($tr, $currency)
	{
		$count=Yii::app()->db->createCommand("select count(1)  from incoming_transfers_details where id_it=".$tr." and original_currency != ".$currency)->queryScalar();
		if($count>0)
			return true;
		else
			return false;
	}

	public static function getPaidPerInvoice($id){
		return Yii::app()->db->createCommand("select received_amount/rate from incoming_transfers_details where invoice_number ='".$id."'")->queryScalar();
	}

}
?>