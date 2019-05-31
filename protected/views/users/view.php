<div class="user-view mytabs hidden">	<?php 	$tabs = array();	if (GroupPermissions::checkPermissions('users-personal')){
		$tabs[Yii::t('translations', 'Personal Details')] = $this->renderPartial('_personal_details', array('model'=>$model), true);	}
	if (GroupPermissions::checkPermissions('users-hr'))	{	$tabs[Yii::t('translations', 'Hr Details')] = $this->renderPartial('_hr_details', array('model'=>$model), true);
	}	if (GroupPermissions::checkPermissions('users-visas')){	$tabs[Yii::t('translations', 'Visas')] = $this->renderPartial('_visas_view', array('model'=>$model), true);
	}	if (GroupPermissions::checkPermissions('users-attachments')){
		$tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array('id_model' => $model->id, 'model_table' => 'users', 'action' => 'view', 'active' => $active), true);
	}	
	$this->widget('CCustomJuiTabs', array( 'tabs'=>$tabs,   'options'=>array( 'collapsible'=>false,'active' =>  'js:configJs.current.activeTab',    ),    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',	));	?></div>