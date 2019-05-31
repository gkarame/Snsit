<?php
class SuppliersPrint extends CActiveRecord{
	public function tableName(){
		return 'suppliers_print';
	}
	public function rules(){
		return array(
			array('id_supplier,amount, description, date', 'required'),
			array('id_supplier, check, status, id_user,bank_code, aux_code, acc_nb, direct', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>256),
			array('jv_nb', 'length', 'max'=>500),
			array('jv_nb_hidden', 'length', 'max'=>1000),
			array('id, id_supplier, amount, description, check, date', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'idSupplier' => array(self::BELONGS_TO, 'Suppliers', 'id_supplier'),
			'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
				'cbank' => array(self::BELONGS_TO, 'Codelkups', 'bank_code'),
					'caux' => array(self::BELONGS_TO, 'Codelkups', 'aux_code'),
						'caccount' => array(self::BELONGS_TO, 'Codelkups', 'acc_nb'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_supplier' => 'Id Supplier',
			'amount' => 'Amount',
			'description' => 'Description',
			'check' => 'Check',
			'date' => 'Date',
			'acc_nb' => 'Account #',
			'bank_code' => 'Bank Code',
			'aux_code' => 'Aux Code',
			
		);
	}
	public static function getStatusList(){		
		return array(
			1 => 'Pending',
			2 => 'Printed', 
			3 => 'Reprinted'		
		); 
	}
	public static function getStatusLabel($value){
		$list = self::getStatusList($value);
		return $list[$value];
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_supplier',$this->id_supplier);
		$criteria->compare('amount',$this->amount);	$criteria->compare('description',$this->description,true);
		$criteria->compare('check',$this->check);	$criteria->compare('date',$this->date,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'date DESC',  
            ),
		));
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}	
	public function getFileLetter($path = false){		
		$path = "uploads".DIRECTORY_SEPARATOR."suppliers".DIRECTORY_SEPARATOR.$this->id_supplier.DIRECTORY_SEPARATOR."letter".DIRECTORY_SEPARATOR;
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$fileName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;
		$filePath .= 'BANK_LETTER_'.$this->id.'.pdf';	$fileName .= 'BANK_LETTERR_'.$this->id.'.pdf';		
		if (file_exists($filePath) && is_file($filePath)){	return $path ? $filePath : $fileName;	}
		return null;
	}	
	public function getFileCheck($path = false){
		$path = "uploads".DIRECTORY_SEPARATOR."suppliers".DIRECTORY_SEPARATOR.$this->id_supplier.DIRECTORY_SEPARATOR."check".DIRECTORY_SEPARATOR;
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$fileName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;			
		$filePath .= 'BANK_CHECK_'.$this->id.'.pdf';	$fileName .= 'BANK_CHECK_'.$this->id.'.pdf';		
		if (file_exists($filePath) && is_file($filePath)){	return $path ? $filePath : $fileName; }
		return null;
	}
	public static function getSupplierByCheck($check){
		return  Yii::app()->db->createCommand("SELECT id_supplier FROM suppliers_print s WHERE id=".$check)->queryScalar();	
	}
	public static function getFileCheckmulti($id_supplier, $title){
		$path = "uploads".DIRECTORY_SEPARATOR."suppliers".DIRECTORY_SEPARATOR.$id_supplier.DIRECTORY_SEPARATOR."check".DIRECTORY_SEPARATOR;
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.$path;
		$fileName =  Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.$path;			
		$filePath .= 'BANK_CHECK_'.$title.'.pdf';	$fileName .= 'BANK_CHECK_'.$title.'.pdf';		
		if (file_exists($filePath) && is_file($filePath)){	return $path ? $filePath : $fileName; }
		return null;
	}


}?>
