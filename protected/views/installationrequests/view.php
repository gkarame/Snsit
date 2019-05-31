<div class="projects-view mytabs hidden size12">
	<?php $tabs = array();
		if(GroupPermissions::checkPermissions('ir-close-installationrequests') || GroupPermissions::checkPermissions('ir-general-installationrequests')){
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model), true);
		}
		if($model->status == InstallationRequests::STATUS_COMPLETED){
		if(GroupPermissions::checkPermissions('ir-close-installationrequests') || GroupPermissions::checkPermissions('ir-general-installationrequests')){
			$tabs[Yii::t('translations', 'Installation Info')] = $this->renderPartial('_info_tab', array('infomodel'=>$infomodel), true);
		}
		}		
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab', ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',	));	?>
</div>
<script> </script>