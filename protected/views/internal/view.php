<div class="maintenance-view mytabs hidden size12">
	<?php $tabs = array();
		if(GroupPermissions::checkPermissions('general-internal','write'))		{
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('update', array('model'=>$model), true);
			$tabs[Yii::t('translations', 'Tasks')] = $this->renderPartial('_tasks', array('model'=>$model), true);
		//	$tabs[Yii::t('translations', 'Tasks 2')] = $this->renderPartial('_tasks_tab_form', array('model'=>$model,'edit'=>false,'id_internal'=>$model->id), true);	
		
		}	
		
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab',  ),
	    'headerTemplate'=> '<li><a href="{url}" >{title}</a></li>',	));	?></div>
  <script type="text/javascript">


$(document).ready(function() {
	if ($(".scroll1").length > 0) {
		if (!$(".scroll1").find(".mCustomScrollBox").length > 0) {
			$(".scroll1").mCustomScrollbar();
		}
	}
	panelClip('.item_clip');
	panelClip('.term_clip');
	
});


function panelClip(element) {
	var width = 0;
	if (element == '.item_clip')
		width = 300;
	else
		width = 90;
		
	$(element).each(function() {
		if ($(this).width() < width) {
			$(this).parent().find('u').hide();
			console.log($(this).parent().find('u').attr('class'));
		}
	});
}

    </script> 