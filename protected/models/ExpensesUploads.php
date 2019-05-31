<?php
class ExpensesUploads extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'expenses_uploads';
	}
	public function rules(){
		return array(
			array('expenses_id, file', 'required'),
			array('expenses_id', 'numerical', 'integerOnly'=>true),
			array('id, expenses_id, file', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'expenses' => array(self::BELONGS_TO, 'Expenses', 'expenses_id'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'expenses_id' => 'Expenses',
			'file' => 'File',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);
		$criteria->compare('expenses_id',$this->expenses_id);	$criteria->compare('file',$this->file,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}	
	public function getFileUpload($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile). DIRECTORY_SEPARATOR ."uploads" . DIRECTORY_SEPARATOR. "customers". DIRECTORY_SEPARATOR .$this->expenses->customer_id. DIRECTORY_SEPARATOR ."expenses". DIRECTORY_SEPARATOR .$this->expenses_id. DIRECTORY_SEPARATOR;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->expenses->customer_id.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.$this->expenses_id.DIRECTORY_SEPARATOR;
		if ($this->file) {
			$filePath .= $this->file;
			$fileName .= $this->file;
		}else{
			return null;
		}		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
}?>