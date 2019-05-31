<?php
class DocumentsCategories extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName()	{
		return 'documents_categories';
	}
	public function rules()	{
		return array(
			array('name', 'required'),
			array('id_module, id_parent, item_order', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('id, id_module, name, id_parent', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
			'documents' => array(self::HAS_MANY, 'Documents', 'id_category'),
			'parent' => array(self::BELONGS_TO, 'DocumentsCategories', 'id_parent'),
			'children' => array(self::HAS_MANY, 'DocumentsCategories', 'id_parent'),
		);
	}
	public function attributeLabels()	{
		return array(
			'id' => 'ID',
			'id_module' => 'Id Module',
			'name' => 'Name',
			'id_parent' => 'Id Parent',
			'item_order' => 'Order',
		);
	}
	public  static function getSubcategoriesTypes()	{
		return array(	
			'Integration' =>'Integration',		
			'SOP' => 'SOP',
			'UAT' => 'UAT',
			'Project Sign Off' => 'Project Sign Off'
		);
	}
	public static function getSubCategories($module, $id)	{
		$id = (int)$id;
		if ($id==17){	$categories = array($id, 24);
		}else if ($id==24){		$categories = array($id, 17);
		}else{		$categories = array($id);		}		
		$first_level = Yii::app()->db->createCommand("SELECT id FROM documents_categories WHERE module='$module' AND id_parent='$id'")->queryColumn();
		if (!empty($first_level) && is_array($first_level)){ 
			$categories = array_merge($categories, $first_level);
			$second_level = Yii::app()->db->createCommand("SELECT id FROM documents_categories WHERE module='$module' AND id_parent IN (" . implode(',', $first_level) . ")")->queryColumn();
			if (!empty($second_level) && is_array($second_level)){
				$categories = array_merge($categories, $second_level);
			}
		}
		return $categories; 
	}	
	public static function getFBRsPerProject($id){

		$items = Yii::app()->db->createCommand("select id, description from projects_tasks where type in (1,4) and id_project_phase in (select id from projects_phases where id_project= ".$id.")")->queryAll();
		$customers = array();
		foreach ($items as $i => $res)		{			$customers[$res['id']] = $res['description'];		}
		return $customers; 

	}
	public static function getCategories($id_model, $model_table, $category = NULL){
		if (!in_array($model_table, array('users','customers', 'projects')))
			throw new CHttpException(404,'Invalid Request.');
		$id_model = (int)$id_model;

		if($model_table == 'projects' && !GroupPermissions::checkPermissions($model_table.'-attachments','write')){
				$query = "SELECT * FROM documents_categories WHERE module='projects' and id in (15,16,17,18,20,24,27,28)";
		}else{
				$query = "SELECT * FROM documents_categories WHERE module='{$model_table}'";
		}
		if ($category != NULL){
			$category = (int) $category;
			$query .= " AND category = '{$category}'";
		}else{
			$query .= ' AND category IS NULL';
		}
		$items = Yii::app()->db->createCommand($query)->queryAll();		
		$categories = array();
		$categories[0] = array();
		foreach($items as &$item) {
			$categories[$item['id_parent']][] = &$item;
		}
		unset($item);		
		foreach($items as &$item){
			if ($item['id']==17){
				$item['count'] = Yii::app()->db->createCommand("SELECT COUNT(*) FROM documents WHERE id_category in (".$item['id'].",24) AND id_model='{$id_model}' AND model_table='{$model_table}'")->queryScalar();
			}else if ($item['id']==24){				
				$item['count'] = Yii::app()->db->createCommand("SELECT COUNT(*) FROM documents WHERE id_category in (".$item['id'].",17) AND id_model='{$id_model}' AND model_table='{$model_table}'")->queryScalar();
			}else{
				$item['count'] = Yii::app()->db->createCommand("SELECT COUNT(*) FROM documents WHERE id_category='{$item['id']}' AND id_model='{$id_model}' AND model_table='{$model_table}'")->queryScalar();
			}
			if (isset($categories[$item['id']])){
				$item['children'] = $categories[$item['id']];
			}
		}		
		foreach ($categories[0] as &$category) {
			if (isset($category['children'])) {
				$sum_total = $category['count'];				
				foreach ($category['children'] as &$cat) {
					if (isset($cat['children'])){	
						$sum = $cat['count'];
						foreach ($cat['children'] as $child){	$sum += $child['count'];}
						$cat['count'] = $sum;
						$sum_total += $cat['count']; 
					}
				}
				$category['count'] = $sum_total;
			}
		}
		return $categories[0];
	}	
	public static function getAll($module, $category = NULL){
		$condition = '`module` = :module';
		$params[':module'] = $module;
		if (!empty($category))	{
			$condition .= " AND category = :cat";	$params[':cat'] = (int) $category;
		}else{	$condition .= ' AND category IS NULL';		}
		return CHtml::listData(self::model()->findAll(
			array(
				'condition' => $condition, 
				'order' => 'name ASC',
				'params' => $params
				
			)), 'id', 'name');
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);	$criteria->compare('id_module',$this->id_module);
		$criteria->compare('name',$this->name,true);	$criteria->compare('id_parent',$this->id_parent);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}?>