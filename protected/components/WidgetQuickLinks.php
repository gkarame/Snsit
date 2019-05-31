<?php
class WidgetQuickLinks extends CWidget 
{
    protected $links;
    public $widget;    
    public function getId($autoGenerate=false) {
    	$model = __CLASS__;
    	return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model'")->queryScalar();
    }    
    public static function getName(){
    	$model = __CLASS__;
    	return Yii::app()->db->createCommand("SELECT name FROM widgets WHERE model = '$model'")->queryScalar();
    }    
    public function init()
    {
        $this->links = Quicklinks::model()->findAll(array('order'=>'name ASC'));       
    }
    public function run()
    {
        $this->render('/widgets/quicklinks', array(
            'links'=>$this->links,
        ));
    }
}?>
