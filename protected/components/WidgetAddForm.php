<?php
class WidgetAddForm extends CWidget 
{
	public $id_dashboard;
	public $widget;	
    public function init()
    { }
    public function run()
    {
        $this->render('/widgets/addForm');
    }    
    public function getId($autoGenerate=false) {
    	$model = __CLASS__;
    	return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model'")->queryScalar();
    }    
    public static function getName(){
    	$model = __CLASS__;
    	return Yii::app()->db->createCommand("SELECT name FROM widgets WHERE model = '$model'")->queryScalar();
    }
}
?>