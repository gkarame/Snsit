<?php
class Dashboards extends CActiveRecord{
	const PROJECT_WIDGET = 6;	const REVENUES = 17;	const PROJECT_FINANCIALS = 21;	const PROJECT_RUNOUT = 40;	const OLD_INVOICES = 50;
	public static function model($className=__CLASS__)	{
		return parent::model($className);
	}
	public function tableName(){
		return 'dashboards';
	}
	public function rules()	{
		return array(
			array('code, name', 'required'),
			array('code', 'length', 'max'=>25),
			array('name', 'length', 'max'=>255),
			array('status', 'numerical', 'integerOnly'=>true, 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, code, name, status', 'safe', 'on'=>'search'),
		);
	}
	public function relations()	{
		return array(
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'name' => 'Name',
			'status' => 'Status',
		);
	}
	public function search(){
		$criteria = new CDbCriteria;	$criteria->compare('id',$this->id);	$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);	$criteria->compare('status',$this->status,true);
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	public static function getAllDashboards(){	 
		if(Yii::app()->user->isAdmin ){
		return Yii::app()->db->createCommand("SELECT id, name FROM dashboards WHERE status=1 and id!='5'")->queryAll(); } else {		
			return Yii::app()->db->createCommand("SELECT id, name FROM dashboards WHERE status=1 and id='5'")->queryAll(); 
		}
	}	 
	public static function getBigSizeWidget($id){
		$id_widget = Yii::app()->db->createCommand()
    		->select('widget_id')
    		->from('user_widgets')
    		->where('id =:id', array(':id'=>$id))
    		->queryScalar();
    	if ($id_widget == self::OLD_INVOICES || $id_widget == self::PROJECT_WIDGET || $id_widget == self::PROJECT_RUNOUT || $id_widget == self::REVENUES || $id_widget == self::PROJECT_FINANCIALS )
    		return 'bigsize';
    	else 
    		return null;
	}
}?>