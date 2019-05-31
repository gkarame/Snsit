<?php
class Codelkups extends CActiveRecord{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'codelkups';
	}
	public function rules(){
		return array(
			array('id_codelist, codelkup', 'required'),
			array('id_codelist', 'numerical', 'integerOnly'=>true),
			array('id, id_codelist, codelkup', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'codelist' => array(self::BELONGS_TO, 'Codelists', 'id_codelist'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',	'id_codelist' => 'Codelist',	'codelkup' => 'Code Lookup',
		);
	}
	public static function getCodelkupsDropDown($label){
		$criteria = new CDbCriteria(array(
            'with'=>array(
                'codelist'=>array(
	                'alias'=>'cl',
	                'together'=>true
               ),
            ),
            'select' => 'cl.id, cl.codelkup',
            'condition' =>'cl.codelist LIKE :value',
			'params' => array(':value' => '%'.$label.'%'), 
            'order'=>'codelkup ASC',
        ));
        return CHtml::listData(self::model()->findAll($criteria), 'id','codelkup');
	}
	public static function getCodelkupsDropDownOriginals($label){
		$criteria = new CDbCriteria(array(
            'with'=>array(
                'codelist'=>array(
	                'alias'=>'cl',
	                'together'=>true
               ),
            ),
            'select' => 'cl.id, cl.codelkup',
            'condition' =>'custom=0 and cl.codelist LIKE :value',
			'params' => array(':value' => '%'.$label.'%'), 
            'order'=>'codelkup ASC',
        ));
        return CHtml::listData(self::model()->findAll($criteria), 'id','codelkup');
	}
	public static function getCodelkupsDropDownIR($id_ir, $label ='Product'){
		$idcustomer = Yii::app()->db->createCommand("select customer from installation_requests where id =".$id_ir)->queryScalar();
    	$products = array();
		$results = Yii::app()->db->createCommand("
select distinct maintenance.id_maintenance,maintenance.product, c1.codelkup as product_name from maintenance join codelkups c1 on maintenance.product = c1.id
		join customers on maintenance.customer = customers.id 
		where maintenance.customer = ".$idcustomer." 
and maintenance.product not in (select product from maintenance m join installation_requests_products irp on irp.id_product =  m.id_maintenance where irp.id_ir =".$id_ir.")
		group by maintenance.product")->queryAll();
		if(empty($results)|| $idcustomer==177){
			$results = Yii::app()->db->createCommand("select 0 as id_maintenance, id as product, codelkup as product_name from codelkups where id_codelist=3")->queryAll();
		}
		foreach ($results as $i => $res){
			$products[$res['product']] = $res['product_name'];
		}
		return $products;
	}
	public static function getCodelkupsDropDownUniqueEas($ea){
		$ids= Yii::app()->db->createCommand("select id,codelkup from codelkups where id_codelist=9 and (custom=0 or id in (SELECT codelkup FROM eas_specific_notes  WHERE id_ea=".$ea.") or id in (SELECT id_note FROM eas_notes  WHERE id_ea=".$ea." )) order by codelkup")->queryAll();
		$notes = array();
		$j = 0;
		foreach ($ids as $i=>$res){
			$notes[$res['id']] = $res['codelkup'];				
		}
		return $notes;
	}
	public function search(){
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('id_codelist',$this->id_codelist);
		$criteria->compare('codelkup',$this->codelkup,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'codelkup ASC',        
			),
		));
	}
	
	public static function getCodelkupPerMultiple($ids) {
 		if($ids != null){
    	$str= Yii::app()->db->createCommand("select  GROUP_CONCAT( codelkup,'') from codelkups where id in (".$ids.") ")->queryScalar();
    	$str = str_replace(',', ', ', $str);
    	return $str;
		}else{
    		return " ";
    	}
 	}
 	public static function getCodelkup($id) {
 		if($id != null){
    	return Yii::app()->db->createCommand()
    		->select('codelkup')
    		->from('codelkups')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	}else{
    		return " ";
    	}
 	}
 	public static function getTaskName($id){
 		 		if($id != null){
    	return Yii::app()->db->createCommand()
    		->select('name')
    		->from('default_tasks')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	}else{
    		return " ";
    	}
 	}
 	public static function insertValue($id_codelist, $value){
 		Yii::app()->db->createCommand("INSERT INTO codelkups (id_codelist,codelkup) VALUES($id_codelist,'$value')")->execute();
 		return Yii::app()->db->getLastInsertID();
 	}
 	public static function insertValueCustom($id_codelist, $value){
 		Yii::app()->db->createCommand("INSERT INTO codelkups (id_codelist,codelkup, custom) VALUES($id_codelist,'$value', 1)")->execute();
 		return Yii::app()->db->getLastInsertID();
 	}
}?>