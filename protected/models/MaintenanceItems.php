<?php
class MaintenanceItems extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'maintenance_items';
	}
	public function rules(){
		return array(
			array('id_contract,licences, contract_description, amount, status, currency, sns_share', 'required'),
			array('id_contract,licences,ea, currency', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('ea','validateEa'),
			array('sns_share', 'numerical', 'max'=>100, 'min'=>0),
			array('licences', 'numerical', 'min'=>1),
			array('id, id_contract, contract_description, amount, currency, sns_share', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){ 
		return array(
			'snsShare' => array(self::BELONGS_TO, 'Codelkups', 'sns_share'),
			'idContract' => array(self::BELONGS_TO, 'Maintenance', 'id_contract'),
			'currency0' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'maintenance0' => array(self::BELONGS_TO, 'Maintenance', 'id_contract'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_contract' => 'Id Contract',
			'contract_description' => 'Contract Description',
			'amount' => 'Amount',
			'currency' => 'Currency',
			'sns_share' => 'Sns Share',
			'licences' => 'licence',
		);
	}
	public static function getStatusList(){		
			return array('1' => 'Active','2' => 'Inactive'); 
	}
	public function validateEa($attribute, $params){		
	        if ( !empty($this->ea) &&  !Eas::validateMaintenanceEaItem($this->ea,$this->id_contract ) ){
	            $this->addError('ea','EA# is not valid');
			}else if ((empty($this->id) || ($this->id>93)) && $this->status == '1' && empty($this->ea))
			{
				 $this->addError('ea','EA# cannot be blank.');
			}
	}	
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_contract',$this->id_contract);
		$criteria->compare('contract_description',$this->contract_description,true);	$criteria->compare('amount',$this->amount);
		$criteria->compare('currency',$this->currency);	$criteria->compare('sns_share',$this->sns_share);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getDescription($id_contract){
		$desc = "";
		$result = Yii::app()->db->createCommand("SELECT contract_description FROM maintenance_items WHERE id_contract='{$id_contract}'")->queryAll();		
		foreach ($result as $res){
			$desc .= ' '.$res['contract_description'] .",";
		}		
		return $desc;
	}
}?>