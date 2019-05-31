<div class="projects-view mytabs hidden size12"><?php $tabs = array();	
			if($ps_list == 'empty'){	$plist = 'empty';	}else{		$plist = $ps_list;	}
	if ($model->id_type == 27){
		if(GroupPermissions::checkPermissions('projects-projects_general')){ $tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model,'p_list'=>$plist,'edit'=>false), true);	}		
		if(GroupPermissions::checkPermissions('projects-tasks')){	$tabs[Yii::t('translations', 'Tasks')] = $this->renderPartial('_tasks_tab', array('model'=>$model,'edit'=>false), true); }		
		if(GroupPermissions::checkPermissions('projects-milestones')){	$tabs[Yii::t('translations', 'Milestones')] = $this->renderPartial('_milestone_tab_form', array('model'=>$model,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-alerts')){	$tabs[Yii::t('translations', 'Risks')] = $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'edit'=>false,'id_project'=>$model->id), true);	}
		if( $model->template != 5 && $model->template != 6){	$tabs[Yii::t('translations', 'Checklist')] = $this->renderPartial('_checklist_items_tab_form', array('model'=>$model,'edit'=>false), true);	}
		$tabs[Yii::t('translations', 'Issues')] = $this->renderPartial('_issues_items_tab_form', array('model'=>$model,'edit'=>false,'id_project'=>$model->id), true);	
		if(GroupPermissions::checkPermissions('projects-alerts')){	$tabs[Yii::t('translations', 'Alerts')] = $this->renderPartial('_alerts_tab', array('model'=>$model,'edit'=>false), true);	}
		{ $tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'view', 'active' => $active)), true); }
	}else if ($model->id_type == 28){
		if(GroupPermissions::checkPermissions('projects-projects_general')){	$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model,'p_list'=>$plist,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-tasks')){	$tabs[Yii::t('translations', 'Tasks')] = $this->renderPartial('_tasks_tab', array('model'=>$model,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-alerts')){		$tabs[Yii::t('translations', 'Risks')] = $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'edit'=>false,'id_project'=>$model->id), true);	}
		$tabs[Yii::t('translations', 'Issues')] = $this->renderPartial('_issues_items_tab_form', array('model'=>$model,'edit'=>false,'id_project'=>$model->id), true);	
		
		if(GroupPermissions::checkPermissions('projects-alerts')){		$tabs[Yii::t('translations', 'Alerts')] = $this->renderPartial('_alerts_tab', array('model'=>$model,'edit'=>false), true);	}
		{  $tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'view', 'active' => $active)), true); }
	}else if($model->id_type == 26){
		if(GroupPermissions::checkPermissions('projects-projects_general')){	$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model,'p_list'=>$plist,'edit'=>false), true); }
		
		if(GroupPermissions::checkPermissions('projects-tasks')){	$tabs[Yii::t('translations', 'Tasks')] = $this->renderPartial('_tasks_tab', array('model'=>$model,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-milestones')){	$tabs[Yii::t('translations', 'Milestones')] = $this->renderPartial('_milestone_tab_form', array('model'=>$model,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-alerts')){	$tabs[Yii::t('translations', 'Risks')] = $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'edit'=>false,'id_project'=>$model->id), true); }
		if(GroupPermissions::checkPermissions('projects-alerts')){	$tabs[Yii::t('translations', 'Alerts')] = $this->renderPartial('_alerts_tab', array('model'=>$model,'edit'=>false), true); }
		{  $tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'view', 'active' => $active,'module_category'=>$model->id_type)), true); }
	}else{
		if(GroupPermissions::checkPermissions('projects-projects_general')){$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model,'p_list'=>$plist,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-tasks')){	$tabs[Yii::t('translations', 'Tasks')] = $this->renderPartial('_tasks_tab', array('model'=>$model,'edit'=>false), true); }
		if(GroupPermissions::checkPermissions('projects-milestones')){	$tabs[Yii::t('translations', 'Milestones')] = $this->renderPartial('_milestone_tab_form', array('model'=>$model,'edit'=>false), true);	}
		if(GroupPermissions::checkPermissions('projects-alerts')){	$tabs[Yii::t('translations', 'Risks')] = $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'edit'=>false,'id_project'=>$model->id), true); }
		if(GroupPermissions::checkPermissions('projects-alerts')){		$tabs[Yii::t('translations', 'Alerts')] = $this->renderPartial('_alerts_tab', array('model'=>$model,'edit'=>false), true);	}
		{  $tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'view', 'active' => $active)), true);}
	}
	$this->widget('CCustomJuiTabs', array( 'tabs'=>$tabs,    'options'=>array(  'collapsible'=>false,  	'active' =>  'js:configJs.current.activeTab',   ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',)); ?></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
<script type="text/javascript">
 (function($){       $(window).load(function(){  hidedropdown();   $('#popupps').hide();    });   })(jQuery);
 $(".fq").click(function() {
         var project= $(this).attr('project'); var type= $(this).attr('type'); var respdate = $(this).attr('resp_date'); var surveyee = $(this).attr('surveyee');
         pshover(project,type,respdate,surveyee); $("#popupps").removeClass("hidden"); $('#popupps').stop().show();    } );
$(".closefq").click(function() {   $('#popupps').stop().hide(); } );
function showdropdown(){		document.getElementById('inv_status').style.visibility="visible";	}
function hidedropdown(){		document.getElementById('inv_status').style.visibility="hidden";	}
function pshover(project,surv_type,resp_date,surveyee){
   $.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('projects/readsurvey');?>",
          dataType: "json", data: {'project': project , 'surv_type':surv_type  },
          success: function(data)
          { if(data){
		  if(data.status=="success"){
                     $(".surveyscontainer").html(" "); $(".surveyscontainer").append(data.readsurvey);
                      $(".titre").html(" "); $(".titre").append('Project Survey By <b>'+surveyee+'</b> Date <b>'+resp_date+'</b>'); } } }  }); }

   function saveMilestone(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageMilestone');?>";
		if (id != 'new') {	url += '/'+parseInt(id);	}		
		$.ajax({ type: "POST", data: $(element).parents('.new_milestone').serialize() , url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') { 		$.fn.yiiGridView.update('milestones-grid');		$.fn.yiiGridView.update('Checklist-grid');	
				  	} else { if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); } } } }	});	}

	function changeInputMilestone(value,id_milestone,id_project,type){
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/manageMilestone');?>",
		  	dataType: "json",  	data: {'value':value,'id_milestone':id_milestone,'type':type,'id_project':id_project},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {	$.fn.yiiGridView.update('milestones-grid');	 $.fn.yiiGridView.update('Checklist-grid');	
				  	} else if (data.status == 'error')	{
				  		var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
				  	} } } });	}
</script>