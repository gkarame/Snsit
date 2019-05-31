<div class="mytabs hidden">
	<form id="customers-form" action='<?php echo Yii::app()->createAbsoluteUrl('customers/update', array('id'=> ($model->id ? $model->id : null)));?>' 
		method="post" enctype='multipart/form-data' class="ajax_submit" autocomplete="off">
	<?php $tabs = array();
	if (empty($model->id) && GroupPermissions::checkPermissions('customers-general_customers','write')){
		$tabs = array( Yii::t('translations', 'General') => $this->renderPartial('_general_form_tab', array('model'=>$model), true), );	}
	else{
		if(GroupPermissions::checkPermissions('customers-general_customers','write')){
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_form_tab', array('model'=>$model), true);
		}else{
			$tabs[Yii::t('translations', 'General')] = $this->renderPartial('_general_tab', array('model'=>$model), true);
		}
		if(GroupPermissions::checkPermissions('customers-eas','write')){
			$tabs[Yii::t('translations', 'EAs')] = $this->renderPartial('_eas_form_tab', array('model'=>$model), true);
		}else{
			$tabs[Yii::t('translations', 'EAs')] = $this->renderPartial('_eas_tab', array('model'=>$model), true);
		}
		if(GroupPermissions::checkPermissions('customers-invoices','write')){
			$tabs[Yii::t('translations', 'Invoices')] = $this->renderPartial('_invoices_form_tab', array('model'=>$model), true);
		}else{
			$tabs[Yii::t('translations', 'Invoices')] = $this->renderPartial('_invoices_tab', array('model'=>$model), true);
		}		
		$tabs[Yii::t('translations', 'Documents')] = $this->renderPartial('application.views.documents.index', array('id_model' => $model->id, 'model_table' => 'customers', 'action' => 'update', 'active' => $active), true);
		if(GroupPermissions::checkPermissions('customers-connections','write')){
			$tabs[Yii::t('translations', 'Connections')] = $this->renderPartial('_connections_form_tab', array('model'=>$model), true);
		}else{
			$tabs[Yii::t('translations', 'Connections')] = $this->renderPartial('_connections_tab', array('model'=>$model), true);
		}		
	} 
	$this->widget('CCustomJuiTabs', array('tabs'=> $tabs,'options'=>array('collapsible'=>false,
	    	'active' =>  'js:configJs.current.activeTab',
	    	'activate' => 'js:function() {
	    		if ($(".documents_div").is(":visible")  || $("#eas-grid").is(":visible") || $("#invoices-grid").is(":visible")) {
					$(".saveDiv").hide();
				} else { $(".saveDiv").show(); } }', ),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',	));	?>	
	<div class="row buttons saveDiv">
		<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('onclick' => 'js:submitForm();return false;')); ?></div>
		<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
	</div>
	</form>
</div>
<?php	Yii::import("xupload.XUpload"); $x = new XUpload; $x->publishAssets(); ?>
<script type="text/javascript">
	function submitForm() {
		var data = $("form").serialize() + '&ajax=customers-form';
		$.ajax({ type: "POST", data: data, dataType: "json", url : $("form").attr("action"),
		  	success: function(data){
			  	if (data && data.status) {
			  		$('.errorMessage').html(''); console.log(data);
				  	if (data.status == "saved") {
				  		console.log("success");
					  	if (data.url) { window.location = data.url; }
					  	if (data.update_connection) { $('#connections .tache.new').remove(); $('#connections .new_conn').show(); }
					  	if (data.update_contact) {
					  		$('#contact_fields .tache.new').remove(); $('#contact_fields .new_cont').show(); $.fn.yiiGridView.update('contacts-grid');
					  	}
					  	closeTab(configJs.current.url);
				  	} else {
				  		console.log("failure");
					  	if (data.status == "failure") {
					  		$.each(data.errors, function (id, message) { console.log(id); console.log(message); $("#"+id+"_em_").html(message); });
					  	} } } } });	}
</script>