<div class="projects-view mytabs hidden size12"><?php 	$tabs = array();
		if(GroupPermissions::checkPermissions('general-trainings')){
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model), true);		
			$tabs[Yii::t('translations', 'Participants')] = $this->renderPartial('_participants_tab', array('model'=>$model), true);			
			$tabs[Yii::t('translations', 'Invitees')] = $this->renderPartial('_candidates_tab', array('model'=>$model), true);
			 if( TrainingsNewModule::getCertifiedUsers(Yii::app()->user->id) > 0 ) { 
			$tabs[Yii::t('translations', 'Costs')] = $this->renderPartial('_costs_tab', array('model'=>$model), true); }
		}		
	$this->widget('CCustomJuiTabs', array(  'tabs'=>$tabs,   'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab',   ),	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',	));	?></div>