<div class="customer-view mytabs hidden" >
<?php $tabs = array();
		$tabs[Yii::t('translations', 'Out. Working Hrs')] =$this->renderPartial('_After_hours_tab', array('model'=>$model), true);		
		$tabs[Yii::t('translations', 'Sunday/Holidays')] =$this->renderPartial('_Sunday_support_tab', array('model'=>$model), true);
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab', ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>'));?>
</div>