<?php
class Widgets extends CActiveRecord{
	public function tableName(){
		return 'widgets';
	}
	public $customErrors = array();
	public function rules(){
		return array(
			array('id, id_dashboard', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('id, name, type', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'user_widgets' => array(self::HAS_MANY, 'UserWidgets', 'widget_id'),
			'id_dashboard' => array(self::BELONGS_TO, 'Dashboards', 'id_dashboard')
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'type' => 'Type',
			'id_dashboard' => 'Dashboard',
		);
	}
	public function search(){
		$criteria = new CDbCriteria;	$criteria->with = array('user_widgets');	$criteria->together = true;
		$criteria->compare('id',$this->id);	$criteria->compare('name',$this->name,true);	$criteria->compare('type',$this->type);
		$criteria->compare('user_widgets.user_id', Yii::app()->user->id);		
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'sort'=>array(
						'defaultOrder'=>'user_widgets.order, t.name ASC')
				
		));
	}	
	public static function getAllWidgets(){
		return Yii::app()->db->createCommand('SELECT * FROM widgets ORDER BY name')->queryAll();
	}
	public static function getWidgetsOn($id){		
		$id = (int) $id;	$id_user = Yii::app()->user->id;		
		 if(Yii::app()->user->isAdmin){
		$all_w = Yii::app()->db->createCommand("
			SELECT w.*, uw.id as uid FROM widgets w	LEFT JOIN user_widgets uw ON w.id=uw.widget_id	LEFT JOIN dashboards d ON d.id=w.id_dashboard
			WHERE d.id='{$id}' AND uw.user_id='{$id_user}'	ORDER BY uw.order")->queryAll(); } ELSE {;	
			$all_w = Yii::app()->db->createCommand("SELECT w.*, uw.id as uid FROM widgets w		LEFT JOIN customer_widgets uw ON w.id=uw.widget_id
			LEFT JOIN dashboards d ON d.id=w.id_dashboard WHERE d.id='{$id}' AND uw.user_id='{$id_user}'ORDER BY uw.order")->queryAll();}
		$widgets = array();	return $all_w;
	}
	public static function getCustomerWidgetsOn($id){
		$id=(int)$id;		$id_user = Yii::app()->user->id;
		$all_w = Yii::app()->db->createCommand("SELECT w.*, cw.id as uid FROM widgets w	LEFT JOIN customer_widgets cw ON w.id=cw.widget_id
			LEFT JOIN dashboards d ON d.id=w.id_dashboard WHERE d.id='{$id}' AND cw.user_id='{$id_user}'")->queryAll();
		$widgets = array();
		if (!empty($all_w)){	foreach ($all_w as $w){	$widgets[] = $w;	}	}		
		return $widgets;
	}
	public static function getWidgetsOff($id, $select = false){
		$id = (int) $id;	$id_user = Yii::app()->user->id;
		if(Yii::app()->user->isAdmin==true){
		$all_w = Yii::app()->db->createCommand("SELECT w.* FROM widgets w	LEFT JOIN dashboards d ON d.id=w.id_dashboard
			WHERE d.id='{$id}' AND w.id!=39 AND w.id NOT IN (SELECT user_widgets.widget_id FROM user_widgets WHERE user_widgets.user_id='{$id_user}')")->queryAll();
		$widgets = array();
		if (!empty($all_w)){
			foreach ($all_w as $w){
				if (GroupPermissions::checkPermissions('dashboard-'.$w['model'])){
					if ($select){	$widgets[$w['id']] = $w['name'];	}
					else{	$widgets[] = $w;	}
				}
			}
		}
		}else{
		$all_w = Yii::app()->db->createCommand("SELECT w.* FROM widgets w	LEFT JOIN dashboards d ON d.id=w.id_dashboard
			WHERE d.id='{$id}' AND w.id NOT IN (SELECT customer_widgets.widget_id FROM customer_widgets WHERE customer_widgets.user_id='{$id_user}')")->queryAll();
		$widgets = array();
		if (!empty($all_w)){
			foreach ($all_w as $w){			
					if ($select){	$widgets[$w['id']] = $w['name']; }else{	$widgets[] = $w; }				
			}
		}
		}
		return $widgets;
	}
	public static function getWidgetsOffCustomer($id, $select = false){
		$id = (int) $id;	$id_user = Yii::app()->user->id;		
		$all_w = Yii::app()->db->createCommand("SELECT w.* FROM widgets w	LEFT JOIN dashboards d ON d.id=w.id_dashboard
			WHERE d.id='{$id}' AND w.id NOT IN (SELECT customer_widgets.widget_id FROM customer_widgets WHERE customer_widgets.user_id='{$id_user}')")->queryAll();
		$widgets = array();
		if (!empty($all_w)){
			foreach ($all_w as $w){
					if ($select){	$widgets[$w['id']] = $w['name'];	}else{	$widgets[] = $w; }			
			}
		}		
		return $widgets;
	}
	public static function getAllWidgetsForPermission(){
		$array = array();
		foreach(self::getAllWidgets() as $k=>$v){
			$array[$v['model']] = array('label' =>Yii::t('translation', $v['name']));
		}
		return $array;
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}	
	protected function beforeValidate(){
		$r = parent::beforeValidate();
		foreach ($this->customErrors as $param) {	$this->addError($param[0], $param[1]);	}
		return $r;
	}	
	public function addCustomError($attribute, $error){
		$this->customErrors[] = array($attribute, $error);
	}	
	public static function getLast3Years(){
    	$years = array();
    	for($i = 0;$i<3;$i++){	$years[] = date('Y',strtotime('now - '.$i.' year'));   	}
    	return $years;		
    }    
	public static function build_sorter($key, $key2 = null) {
	    return function ($a, $b) use ($key, $key2){
	    	$result = strnatcmp($b[$key],$a[$key]);
	    	if ($key2 != null && $result == 0)	{
				return 	strnatcmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
	public static function build_sorter_num($key, $key2 = null){
	    return function ($a, $b) use ($key, $key2){
	    	$result = gmp_cmp($b[$key],$a[$key]);
	    	if ($key2 != null && $result == 0)
	    	{
				return 	gmp_cmp($a[$key2], $b[$key2]);   			
	    	}
	        return $result;
	    };
	}
}
?>