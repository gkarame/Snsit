<div class="customer-view mytabs hidden">
	<?php $tabs = array();
	if(GroupPermissions::checkPermissions('customers-general_customers') && GroupPermissions::checknotCSonly()){
		$tabs[Yii::t('translations', 'General')] =$this->renderPartial('_general_tab', array('model'=>$model), true);
	}	
	if(GroupPermissions::checkPermissions('customers-eas') && GroupPermissions::checknotCSonly()){
		$tabs[Yii::t('translations', 'EAs')] = $this->renderPartial('_eas_tab', array('model'=>$model), true);
	}	
	if(GroupPermissions::checkPermissions('customers-invoices') && GroupPermissions::checknotCSonly()){
		$tabs[Yii::t('translations', 'Invoices')] = $this->renderPartial('_invoices_tab', array('model'=>$model), true);
	}	
	if(GroupPermissions::checkPermissions('customers-attachments') && GroupPermissions::checknotCSonly())	{
		$tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array('id_model' => $model->id, 'model_table' => 'customers', 'action' => 'view', 'active' => $active), true);
	}	
	if(GroupPermissions::checkPermissions('customers-connections')){
		$tabs[Yii::t('translations', 'Connections')] = $this->renderPartial('_connections_tab', array('model'=>$model), true);
	}
	$this->widget('CCustomJuiTabs', array(
	    'tabs'=>$tabs,
	    'options'=>array(
	        'collapsible'=>false,
	    	'active' =>  'js:configJs.current.activeTab',
	    ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',
	    'html_to_add' => '<a class="tabs_extra extra_edit" id="extra_edit_customer" title="'.Yii::t('translations', 'Edit Customer').'" href="'.$this->createUrl('customers/update', array('id' => $model->id, 'view'=> 1)).'"></a>' 
	));	?>
</div>