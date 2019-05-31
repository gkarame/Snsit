<?php
class SearchApprovalForm extends CFormModel{
	const STATUS_IN_APPROVAL = 'In Approval';	const STATUS_SUBMITTED = 'Submitted';	
	public $id_customer, $customer_name, $user_name, $id_project, $status, $id_user, $from, $to;
	public function rules(){
		return array(
			array('id_user', 'exist', 'attributeName' => 'id', 'className' => 'Users', 'allowEmpty'=>true),
			array('id_project', 'exist', 'attributeName' => 'id', 'className' => 'Projects', 'allowEmpty'=>true),
			array('id_customer', 'exist', 'attributeName' => 'id', 'className' => 'Customers', 'allowEmpty'=>true),
			array('customer_name', 'exist', 'attributeName' => 'name', 'className' => 'Customers', 'allowEmpty'=>true),
			array('from, to', 'type', 'type' => 'date', 'message' => '{attribute} is not a valid date!', 'dateFormat' => 'dd/MM/yyyy', 'allowEmpty'=>true),
			array('status', 'in', 'range' => array('Submitted', 'In Approval'), 'allowEmpty'=>true),
		);
	}
	public function attributeLabels(){
		return array(
			'id_user' => Yii::t('translations', 'User'),
			'id_customer' => Yii::t('translations', 'Customer'),
			'id_project' => Yii::t('translations', 'Project'),
			'from' => Yii::t('translations', 'From Date'),
			'to' => Yii::t('translations', 'To date'),
			'status' => Yii::t('translations', 'Status'),
		);
	}
	public static function getStatusList(){		
		return array(
			self::STATUS_IN_APPROVAL => 'In Approval',
			self::STATUS_SUBMITTED => 'Submitted',			
		); 
	}	
	public static function getStatusLabel($value)	{
		$list = self::getStatusList();	return $list[$value];
	}
} ?>