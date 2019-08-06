<div class="settings-view mytabs hidden"><?php $tabs = array();
	if (GroupPermissions::checkPermissions('settings-general_settings')){	$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general', array('settings'=>$settings), true); }
	if (GroupPermissions::checkPermissions('settings-codelists')){	$tabs[Yii::t('translations', 'Code Lists')] =  $this->renderPartial('_codeLists', array('codelists_categories'=>$codelists_categories,'all_countries_perdiem' => $all_countries_perdiem), true);	}
	$this->widget('CCustomJuiTabs', array( 'tabs'=>$tabs,   'options'=>array( 'collapsible'=>false,	'active' =>  'js:configJs.current.activeTab'   ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>', ));	?></div>
