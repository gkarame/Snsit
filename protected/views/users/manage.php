<div class="mytabs hidden">	<form id="users-form" action='<?php echo Yii::app()->createAbsoluteUrl('users/update', array('id'=> ($model->id ? $model->id : null)));?>' method="post" 
		enctype='multipart/form-data' autocomplete="off" class="ajax_submit"><?php if ($model->id) {	$tbs = array();
		if(GroupPermissions::checkPermissions('users-personal','write')){	$tbs[Yii::t('translations', 'Personal Details')] = $this->renderPartial('_personal_details_form', array('model'=>$model), true);
		}else{		$tbs[Yii::t('translations', 'Personal Details')] = $this->renderPartial('_personal_details', array('model'=>$model), true);	}		
		if(GroupPermissions::checkPermissions('users-hr','write')){	$tbs[Yii::t('translations', 'Hr Details')] = $this->renderPartial('_hr_details_form', array('model'=>$model), true);
		}else{	$tbs[Yii::t('translations', 'Hr Details')] = $this->renderPartial('_hr_details', array('model'=>$model), true);	}		
		if(GroupPermissions::checkPermissions('users-visas','write')){		$tbs[Yii::t('translations', 'Visas')] = $this->renderPartial('_visas_tab', array('model'=>$model), true);
		}if(GroupPermissions::checkPermissions('users-attachments','write')){	$tbs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array('id_model' => $model->id, 'model_table' => 'users', 'action' => 'update', 'active' => $active), true);
		}	$tabs = $tbs;	} 
	elseif(GroupPermissions::checkPermissions('users-personal','write') AND GroupPermissions::checkPermissions('users-hr','write')){
		$tabs = array(	Yii::t('translations', 'Personal Details') => $this->renderPartial('_personal_details_form', array('model'=>$model), true),
	        Yii::t('translations', 'Hr Details') => $this->renderPartial('_hr_details_form', array('model'=>$model), true)	);
	}else {		throw new CHttpException(403,'You don\'t have permission to access this page.');	}
	$this->widget('CCustomJuiTabs', array( 'tabs'=>$tabs,    'options'=>array(   'collapsible'=>false, 	'active' =>  'js:configJs.current.activeTab',  	'activate' => 'js:function() {
	    		if ($(".documents_div").is(":visible")) {	$(".saveDiv").hide(); } else {  $(".saveDiv").show();	} 	}',
	    ),   'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));	?>	<div class="row buttons saveDiv">
		<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('onclick' => 'js:submitForm();return false;')); ?></div>
		<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>	</div>	</form></div>
<script type="text/javascript">
	function submitForm() {
		var data = $("form").serialize() + '&ajax=users-form';
		$.ajax({type: "POST",	data: data,	 	dataType: "json",
		  	url : $("form").attr("action"),
		  	success: function(data) {
			  	if (data && data.status) {
			  		$('.errorMessage').html('');
				  	console.log(data);
				  	if (data.status == "saved") {
				  		console.log("success");				  		
					  	if (data.url) {  		window.location = data.url;	  	}
					  	if (data.update_visa) {
					  		$('#visas .tache.new').remove();
					  		$('.new_vis').show();
					  		$.fn.yiiGridView.update('visas-grid');
					  	}
					  	closeTab(configJs.current.url);
				  	} else {
				  		console.log("failure");
					  	if (data.status == "failure") {
					  		$.each(data.errors, function (id, message) {
						  		console.log(id);
						  		console.log(message);
								$("#"+id+"_em_").html(message);
							});
					  	}
				  	}
			  	}
	  		}
		});
	}
</script>