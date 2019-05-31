<?php
class Documents extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'documents';
	}
	public function rules()	{
		return array(
			array('id_model, model_table, id_category, document_title, uploaded_by, file', 'required'),
			array('id_model, id_category, uploaded_by,fbr', 'numerical', 'integerOnly'=>true),
			array('model_table, subcategory', 'length', 'max'=>50),
			array('document_title, file', 'length', 'max'=>500),
			array('description, uploaded', 'safe'),
			array('id_category','validatesubcategory'),
			array('id, id_model, model_table, id_category, document_title, uploaded, uploaded_by, description, file', 'safe', 'on'=>'search'),
		);
	}
	public function validatesubcategory(){
	 if ( $this->id_category == 31 || $this->id_category == 30){
	        if(empty($this->subcategory)){
	        	$this->addError('subcategory','Type must be specified.');
	        }      	
	    }	else if 	($this->id_category == 15)	
	    {
			if(empty($this->fbr)){
	        	$this->addError('fbr','FBR must be specified.');
	        } 
	    }
	}
	public function relations()	{
		return array(
			'author' => array(self::BELONGS_TO, 'Users', 'uploaded_by'),
			'category' => array(self::BELONGS_TO, 'DocumentsCategories', 'id_category'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_model' => 'Model',
			'model_table' => 'Table',
			'id_category' => 'Category',
			'document_title' => 'Document Title',
			'uploaded' => 'Uploaded',
			'uploaded_by' => 'Uploaded By',
			'description' => 'Description',
			'file' => 'File',
			'subcategory' => 'Type',
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('id_model',$this->id_model);
		$criteria->compare('model_table',$this->model_table,true);	$criteria->compare('id_category',$this->id_category);
		$criteria->compare('document_title',$this->document_title,true);	$criteria->compare('uploaded',$this->uploaded,true);
		$criteria->compare('uploaded_by',$this->uploaded_by);	$criteria->compare('description',$this->description,true);
		$criteria->compare('file',$this->file,true);
		return new CActiveDataProvider('Documents', array(
			'criteria' => $criteria,
			'pagination'=>array(
                'pageSize' => Utils::getPageSize(),
            ),
            'sort'=>array(
    			'defaultOrder'=>'t.id DESC',            
		    ),
		));
	}	
	public function getFile($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$this->model_table.DIRECTORY_SEPARATOR.$this->id_model.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->file;
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$this->model_table.DIRECTORY_SEPARATOR.$this->id_model.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.$this->file;
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFilePath($model)	{
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$model['model_table'].DIRECTORY_SEPARATOR.$model['id_model'].DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$model['id'].DIRECTORY_SEPARATOR.$model['file'];
		if (file_exists($filePath)){
			return $filePath;
		}
		return null;
	}	
	public function getFilename(){
		$path = $this->getFile(true);
		if ($path != NULL){		return pathinfo($path, PATHINFO_BASENAME);			}
		return NULL;
	}	
	public function getExtension()	{
		$path = $this->getFile(true);	
		if ($path != NULL)		{		return pathinfo($path, PATHINFO_EXTENSION);			}
		return "";
	}	
	protected function beforeValidate(){
        if ($this->isNewRecord){   $this->uploaded_by = Yii::app()->user->id;	$this->uploaded = date('Y-m-d H:i:s');   }
		if (Yii::app( )->user->hasState('documents')){
			$attachments = Yii::app( )->user->getState('documents');
        	$attach = end($attachments); 
        	if (is_file( $attach["path"] ))	{
        		$this->file = $attach["name"];
        	}
		}
        return parent::beforeValidate();
    }    
	public function afterSave()	{	parent::afterSave(); 	$this->addAttachment();	}    
	private function addAttachment() {
        $path = Yii::app()->getBasePath().DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$this->model_table.DIRECTORY_SEPARATOR.$this->id_model.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {   mkdir($path, 0777, true);    chmod($path, 0777);      }
		if (is_file(Yii::app()->getBasePath().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file)) 
		{
			if (rename( Yii::app()->getBasePath().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.$this->file, $path.$this->file)) 
			{
				chmod( $path.$this->file, 0777 );
			}
		} 
    	Yii::app()->user->setState('documents', null);
	}	
	public static function checkIfAnyDocByCategory($id_model, $model_table, $id_category){
		$id_model = (int) $id_model;	$id_category = (int) $id_category;		
		return Yii::app()->db
			->createCommand("SELECT uploaded FROM documents WHERE id_model={$id_model} 
				AND model_table = '{$model_table}' AND id_category = {$id_category} 
				ORDER BY uploaded DESC LIMIT 1"  
			)
			->queryScalar();
	}	
	public static function validateMilestoneDoc($project, $subcategory){
		return Yii::app()->db->createCommand("SELECT count(1) from documents where id_model=".$project." and subcategory='".$subcategory."' ")->queryScalar();		
	}
	public static function getLastDocByCategory($id_model, $model_table, $id_category){
		$id_model = (int) $id_model;	$id_category = (int) $id_category;
		return Yii::app()->db->createCommand("SELECT * FROM documents WHERE id_model={$id_model}	AND model_table = '{$model_table}' AND id_category = {$id_category}
		ORDER BY uploaded DESC LIMIT 1")->queryRow();
	}	
	public static function checkLastModifiedByPeriod($id_model, $model_table, $id_category, $period = "+ 2 week"){
		$uploaded = Documents::checkIfAnyDocByCategory($id_model, $model_table, $id_category);
		$now = strtotime("now");
		if ($uploaded){
			$upload_period = strtotime($uploaded.' '.$period);
			if ($upload_period <= $now)
				return true;
			return false;
		} else {
			if ($id_category==17){	$uploaded = Documents::checkIfAnyDocByCategory($id_model, $model_table, 24);
			} else if ($id_category==24){
				$uploaded = Documents::checkIfAnyDocByCategory($id_model, $model_table, 17);
			}
			if ($uploaded){
				$upload_period = strtotime($uploaded.' '.$period);
				if ($upload_period <= $now)
					return true;
				return false;
			}
		}
		return false;
	}
}?>