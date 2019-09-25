<div class="mytabs hidden projects-view"><?php 	$tabs = array();
	if (empty($model->id)) {	if(GroupPermissions::checkPermissions('projects-projects_general','write'))	{	$tabs = array(	Yii::t('translations', 'General') => $this->renderPartial('_general_form_tab', array('model'=>$model), true),	);	}	}
	else if($model->id_type == 26){
		$tabs = array(
			Yii::t('translations', 'General') => $this->renderPartial('_general_tab', array('model'=>$model,'edit'=> GroupPermissions::checkPermissions('projects-projects_general','write') ? true : false), true),
	        Yii::t('translations', 'Tasks') => $this->renderPartial('_tasks_tab', array('model'=>$model), true),
	        Yii::t('translations', 'Milestones') => $this->renderPartial('_milestone_tab_form', array('model'=>$model), true),
			Yii::t('translations', 'Risks') => $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'id_project'=>$model->id), true),
			Yii::t('translations', 'Alerts') => $this->renderPartial('_alerts_tab', array('model' => $model), true),
			Yii::t('translations', 'Documents') => $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'update', 'active' => $active,'module_category'=>$model->id_type)), true),
		); }	else if($model->id_type == 27){
		$tabs = array(
			Yii::t('translations', 'General') => $this->renderPartial('_general_tab', array('model'=>$model,'edit'=>GroupPermissions::checkPermissions('projects-projects_general','write') ? true : false), true),
	        Yii::t('translations', 'Tasks') => $this->renderPartial('_tasks_tab', array('model'=>$model), true),
	        Yii::t('translations', 'Milestones') => $this->renderPartial('_milestone_tab_form', array('model'=>$model), true),
			Yii::t('translations', 'Risks') => $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'id_project'=>$model->id), true),
	        Yii::t('translations', 'Checklist') => $this->renderPartial('_checklist_items_tab_form', array('model'=>$model), true),
			Yii::t('translations', 'Alerts') => $this->renderPartial('_alerts_tab', array('model' => $model), true),
			Yii::t('translations', 'Documents') => $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'update', 'active' => $active)), true),
		); }else{
		$tabs = array(
			Yii::t('translations', 'General') => $this->renderPartial('_general_tab', array('model'=>$model,'edit'=>GroupPermissions::checkPermissions('projects-projects_general','write') ? true : false), true),
	        Yii::t('translations', 'Tasks') => $this->renderPartial('_tasks_tab', array('model'=>$model), true),
	        Yii::t('translations', 'Milestones') => $this->renderPartial('_milestone_tab_form', array('model'=>$model), true),
			Yii::t('translations', 'Alerts') => $this->renderPartial('_alerts_tab', array('model' => $model), true),
			Yii::t('translations', 'Documents') => $this->renderPartial('application.views.documents.index', array_merge(array('id_model' => $model->id, 'model_table' => 'projects', 'action' => 'update', 'active' => $active)), true),
			Yii::t('translations', 'Risks') => $this->renderPartial('_risks_tab', array('model'=>ProjectsRisks::model(),'id_project'=>$model->id), true),
		); }	
	$this->widget('CCustomJuiTabs', array(   'tabs'=> $tabs,   'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab', ),  'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));	?></div>
<?php	Yii::import("xupload.XUpload"); $x = new XUpload;	$x->publishAssets(); ?>
<script type="text/javascript">
	function submitForm() {
var id_project ="<?php echo $model->id ;?>" ;  var status = $("#status_dropdown option:selected").val();
	if (status == '2') {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateMilestones');?>"; 	var url2 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateAlerts');?>";  	var url3 = "<?php echo Yii::app()->createAbsoluteUrl('projects/ValidateTimesheet');?>";
	$.ajax({type: "POST",data: {selected:id_project},	url: url, 
	  	dataType: "json", 	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') {				
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with pending milestones.', action_but);	
var ddl = document.getElementById('status_dropdown'); var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
								}
							} } } }	}); 
		$.ajax({type: "POST",	data: {selected:id_project},  	url: url2,   	dataType: "json",
		success: function(data) {
		  	if (data) {
			if(data.valid == '0'){				
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with pending User Controlled Alerts.', action_but);				
						var ddl = document.getElementById('status_dropdown');	var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
								}
							}	}  	}	} });	
	$.ajax({type: "POST",	data: {selected:id_project},  	url: url3, dataType: "json",	   
	  	success: function(data) {
		  	if (data) {
		  		if (data.valid == '0') {				 
				var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('ERROR MESSAGE', 'Cannot close project with pending timesheet tasks.', action_but);	
				var ddl = document.getElementById('status_dropdown');	
						var opts = ddl.options.length;
						for (var i=0; i<opts; i++){
						if (ddl.options[i].value == select ){
							ddl.options[i].selected = true;
							break;
								}
							}
				} 	} }	}); }
	var url3 = "<?php echo Yii::app()->createAbsoluteUrl('projects/GetAlertsPerProject');?>";	var id_project ="<?php echo $model->id ;?>" ;
		$.ajax({type: "POST",	data: {selected:id_project},  	url: url3, 	dataType: "json" });
		var data = $("#projects_fields").serialize() + '&ajax=projects-form';
		$.ajax({type: "POST",		data: data,  	dataType: "json",  	url : $("#projects-form").attr("action"),
		  	success: function(data) {
			  	if (data && data.status) { 	console.log(data);
				  	if (data.status == "saved") {	console.log("success");  		closeTab(configJs.current.url);
				  	} else {
				  		console.log("failure");
					  	if (data.status == "failure") {
					  		$.each(data.errors, function (id, message) { $('#Projects_'+id+"_em_").html(message); });
					  	} 	} 	} 		}	});	}
	function changeInputMilestone(value,id_milestone,id_project,type){
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('projects/manageMilestone');?>",
		  	dataType: "json", 	data: {'value':value,'id_milestone':id_milestone,'type':type,'id_project':id_project},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {		console.log('da');
				  	} else if (data.status == 'error'){
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
				  	} 	} }	}); }	
</script>