<div class="projects-view mytabs hidden size12"><?php $tabs = array();	if(GroupPermissions::checkPermissions('general-sma')){
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model,'edit'=>false), true);
			$tabs[Yii::t('translations', 'SMA Actions')] = $this->renderPartial('_actions_tab', array('model'=>SmaActions::model(),'edit'=>false,'id_sma'=>$model->id,'instance'=>$model->instance,'customer'=>$model->id_customer,'checkv'=>$model->displayClosed), true);
	}
	$this->widget('CCustomJuiTabs', array(  'tabs'=>$tabs,  'options'=>array( 'collapsible'=>false,	'active' =>  'js:configJs.current.activeTab',   ),   'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));	?>
</div> <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>