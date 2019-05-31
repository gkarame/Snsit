<?php
class RsrTime extends CActiveRecord{
	const AGE_365 = 365;	const AGE_270 = 270;	const AGE_180 = 180;	const AGE_90 = 90;	const AGE_30 = 30;	const AGE_0	 = -30;	const GROUP_HR = 6;
	const GROUP_OFFICE_ASSISTANTS = 7;	const GROUP_recruitment = 15;	const GROUP_office_admin = 11;	const PARTNER_STATUS_PAID = 'Paid';	const PARTNER_STATUS_NOT_PAID = 'Not Paid';
	public $age, $textdays;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'rsr_time';
	}
	public function rules(){
		return array(
			array('id,sr', 'required'),
			 array('id', 'length', 'max'=>5),
			 array('amount', 'numerical'),
			array('id,amount', 'safe', 'on'=>'search'),	);	
	}
	public function relations(){
		return array( 'id' => array(self::BELONGS_TO, 'SupportRequest', 'id'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'RSR#',
			'sr' => 'Linked SRs#',
		);
	}
	public function search(){
		$criteria=new CDbCriteria; 
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	     
}?>