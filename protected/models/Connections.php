<?php
class Connections extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'connections';
	}
	public function rules()	{
		return array(
			array('id_customer, name, type, server_name, password', 'required'),
			array('id_customer, type', 'numerical', 'integerOnly'=>true),
			array('notes, name, server_name', 'length', 'max'=>255),
			array('password', 'length', 'max'=>50),
			array('file', 'length', 'max' => 500),
			array('id, id_customer, name, type, server_name, password', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'cType' => array(self::BELONGS_TO, 'Codelkups', 'type'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_customer' => 'Customer',
			'name' => 'Connection Name',
			'type' => 'Connection Type',
			'server_name' => 'Server Name',
			'password' => 'Password',
			'notes'=>'Notes'
		);
	}
	public function search(){
		$criteria=new CDbCriteria;		
		$criteria->with = array('cType');		
		$criteria->compare('id',$this->id);
		$criteria->compare('id_customer',$this->id_customer);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('server_name',$this->server_name,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',   
            	'attributes'=>array(
		            'cType.codelkup'=>array(
		                'asc'=>'cType.codelkup',
		                'desc'=>'cType.codelkup DESC',
		            ),
		            '*',
		        ),
		    ),  
		));
	}	
	public function afterSave() {
		parent::afterSave();
	}	
	private function addAttachments() {
		if (Yii::app( )->user->hasState('attach_conn')) {
	        $attachments = Yii::app( )->user->getState('attach_conn');
	        $path = Yii::app()->getBasePath().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR;
	        if( !is_dir($path))   {
	            mkdir($path, 0777, true);
	            chmod($path, 0777);
	        }
	        foreach ($attachments as $attach){
	            if (is_file( $attach["path"])){
	                if (rename( $attach["path"], $path.$attach["name"])){
	                    chmod($path.$attach["name"], 0777);
	                    self::model()->updateByPk($this->id, array('file' => $attach["name"]));
	                }
	            } 
	        }
	        Yii::app( )->user->setState( 'attach_conn', null );
	    }
	}	
	public function getFile($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->file;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->file;
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
	public function getFileDownload(){
		$filePath = 'uploads'.DIRECTORY_SEPARATOR.'customers'.DIRECTORY_SEPARATOR.$this->id_customer.DIRECTORY_SEPARATOR.'connections'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->file;
		return $filePath;
	}
	public function getFileName(){
		$path = $this->getFile(true);
		if ($path != NULL){
			return pathinfo($path, PATHINFO_BASENAME);	
		}
		return NULL;
	}	
	public function renderAttachment(){
		echo '<a href="'. Yii::app()->createUrl("site/download", array("file" => $this->getFileDownload())).'">'.$this->getFileName().'</a>';
	}
}?>