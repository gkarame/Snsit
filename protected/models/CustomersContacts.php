<?php
class CustomersContacts extends CActiveRecord{
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName()	{
		return 'customers_contacts';
	}
	public function rules()	{
		return array(
			array('id_customer, name, email', 'required'),
			array('id_customer', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>500),
			array('email, job_title', 'length', 'max'=>255),
			array('email', 'email'),			
			array('access', 'length', 'max'=>3),
			array('mobile_number', 'length', 'max'=>100),
			array('username, password', 'length', 'max'=>256),
			array('id, id_customer, name, email, job_title, mobile_number', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_customer' => 'Customer',
			'name' => 'Contact Full Name',
			'email' => 'Email',
			'job_title' => 'Job Title',
			'mobile_number' => 'Mobile Number',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_customer',$this->id_customer);
		$criteria->compare('name',$this->name,true);	$criteria->compare('email',$this->email,true);
		$criteria->compare('job_title',$this->job_title,true);	$criteria->compare('mobile_number',$this->mobile_number,true);		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'name ASC',            
		    ),
		));
	}	
	public static function getNameBySR($id)	{
		$name = Yii::app()->db->createCommand()
    		->select('submitter_name')
    		->from('support_desk')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	return $name;
	}	
	public static function getNameById($id_customer_contact){
		$name = Yii::app()->db->createCommand()
    		->select('name')
    		->from('customers_contacts')
    		->where('id =:id', array(':id'=>$id_customer_contact))
    		->queryScalar();
    	return $name;
	}
	public static function getCustomerById($id_customer_contact){
		$name = Yii::app()->db->createCommand()
    		->select('id_customer')
    		->from('customers_contacts')
    		->where('id =:id', array(':id'=>$id_customer_contact))
    		->queryScalar();
    	return $name;
    }
}?>