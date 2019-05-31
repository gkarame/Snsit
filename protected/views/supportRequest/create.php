<div class="create supportCreate">	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'support-desk-header-form',	'enableAjaxValidation'=>false,	'htmlOptions' => array(	'class' => 'ajax_submit',	'enctype' => 'multipart/form-data',	),	)); ?>
		<div class="row marginb20 marginr36 row_textarea_ea"><?php echo $form->labelEx($model,'short_description'); ?>
			<div class="inputBg_create"><?php echo $form->textField($model,'short_description',array('autocomplete'=>'off')); ?></div>		
			<?php echo $form->error($model,'short_description'); ?>	</div>	
		<div class="horizontalLine"></div>	<div class="row marginb20 marginr36"><?php echo $form->labelEx($model, 'product'); ?>
			<div class="selectBg_create"><?php echo $form->dropDownList($model, 'product', SupportDesk::getProductwithActiveMaint(Yii::app()->user->customer_id) , array('prompt' => Yii::t('translations', 'Choose product'))); ?>
			</div>	<?php echo $form->error($model,'product'); ?>	</div>		<div class="row marginb20 marginr36">
			<?php echo $form->labelEx($model, 'environment'); ?>	<div class="selectBg_create">
				<?php echo $form->dropDownList($model, 'environment', array('Test'=>'Test','UAT'=>'UAT','Live'=>'Live'), array('prompt' => Yii::t('translations', 'Choose environment'),'id'=>'environment', 'onchange' => 'changeEnvironment(this);')); ?>
			</div>	<?php echo $form->error($model,'environment'); ?>	</div>	<div class="row marginb20 marginr36">	<?php echo $form->labelEx($model, 'schema'); ?>
			<div class="selectBg_create">	<?php echo $form->dropDownList($model, 'schema', SupportDesk::getSchemaList(), array('prompt' => Yii::t('translations', 'Choose schema'))); ?>
			</div>	<?php echo $form->error($model,'schema'); ?>	</div>	<div class="row marginb20 marginr36">
			<?php echo $form->labelEx($model, 'severity'); ?>	<div class="selectBg_create">
				<?php echo $form->dropDownList($model, 'severity', array('Low'=>'Low','Medium'=>'Medium','High'=>'High'), array('prompt' => Yii::t('translations', 'Choose severity'), 'id'=>'severity' ,'onchange' => 'changeCategory(this);')); ?>
			</div>	<?php echo $form->error($model,'severity'); ?>	</div>	<div class="row marginb20 marginr36 row_textarea_ea">
			<?php echo $form->labelEx($model,'Name'); ?><div class="inputBg_create"><?php echo $form->textField($model,'submitter_name',array('autocomplete'=>'off')); ?>
			</div>		<?php echo $form->error($model,'submitter_name'); ?> 	</div>	<div class="row marginb20 marginr36 hidden br">
			<?php echo $form->labelEx($model, 'system_down'); ?>	<div class="">	<?php echo $form->radioButtonList($model,'system_down',array('No'=>'No','Yes'=>'Yes')); ?>
			</div>	<?php echo $form->error($model,'system_down'); ?>	</div>	<div class="horizontalLine"></div>
			<div class="row marginb20 marginr36 row_textarea_ea"><?php echo $form->labelEx($model,'description'); ?><br/>
			<div class="red" style="width:900px;"><i>Please enter a concise explanation of your issue. If you are reporting an error, kindly attach a screenshot of the error details window. Remember, the more information you provide, the better our support team can help you.</i></div><br/>
		 	<div class="Div2">	<?php echo $form->textArea($model,'description'); ?>	</div>		<?php echo $form->error($model,'description'); ?>	</div>	
		<div class="horizontalLine"></div>	<div class="row marginb20 marginr36  br right_bar">	<?php echo $form->labelEx($model, 'INCIDENT OCCURED PREVIOUSLY ? *'); ?>
			<div class="">	<?php echo $form->radioButtonList($model,'issue_incurred_previously',array('No'=>'No','Yes'=>'Yes'),array('checkValue'=>null)); ?>	</div>
			<?php echo $form->error($model,'issue_incurred_previously'); ?>	</div>	<div class="row marginb20 marginr36  br">
			<?php echo $form->labelEx($model, 'DO YOU THINK THIS IS AN ISSUE RELATED TO A RECENT CUSTOMIZATION ? *'); ?>
			<div class="issue">	<?php echo $form->radioButtonList($model,'issue',array('No'=>'No','Yes'=>'Yes'),array(''=>'')); ?>
			</div>	<?php echo $form->error($model,'issue'); ?>	</div>	<br clear="all">	
		<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery" >
			<?php	Yii::import("xupload.models.XUploadForm");
				$this->widget( 'xupload.XUpload', array(
		        	'url' => Yii::app( )->createUrl("supportDesk/upload"),
		            'model' => new XUploadForm(),
		            'htmlOptions' => array('id'=>'support-desk-header-form'),
					'formView' => 'small_form_exp',
		            'attribute' => 'file',
					'autoUpload' => true,
		            'multiple' => true,
					'options' => array(
						'maxFileSize' => 15728640, 
						'submit' => "js:function (e, data) {
							var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
							var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.Yii::app()->user->customer_id.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."supportdesk".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
							//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/".Yii::app()->user->customer_id."/supportdesk/"."';
							//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/".Yii::app()->user->customer_id."/supportdesk/"."';
		        			var model_id = '".$model->id."';
		                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
		                    return true;
		                }",
						'completed' => 'js: function(e, data) {
							console.log(data);
							$("#specialid").remove();	}',	),   ));?>	</div><div class="files" data-toggle="modal-gallery" data-target="#modal-gallery" style="margin-top:10px;">
			<?php if (($files = $model->getFileCreate()) != null) {	foreach ($files as $file) {		$path_parts = pathinfo($file['path']); ?>
					<div class="box template-download fade marginr10 margint10" id="tr0">	<div class="title">
							<a href="<?php echo $this->createUrl('site/download', array('file' => $path_parts['dirname'].DIRECTORY_SEPARATOR.$path_parts['basename']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
						</div>    	<div class="size">    	<span><?php echo Utils::getReadableFileSize(filesize($file['path']));?></span>
				        </div>	<div class="delete">
							<button class="btn btn-danger delete"
								 data-url="<?php echo $this->createUrl( "supportDesk/deleteUpload", array(
		                          	"id_customer" =>$model->id_customer,
									"model_id" => $model->id,					 			
		                          	"file" => $path_parts['basename'],
		                            ));?>" 
								data-type="POST">
							</button>	</div>	</div>	<?php }	} ?></div>	<div class="horizontalLine"></div>		
		<div class="row buttons">	<?php echo CHtml::submitButton('Submit', array('class'=>'submit' ,'onclick' => 'disablebutton()','id'=>'createbut')); ?></div>	<br clear="all" /><?php $this->endWidget(); ?></div>
<script type="text/javascript">
	$(document).ready(function() {	$( ".br br" ).remove();	$('span.btn-success').addClass('margint18'); });
function disablebutton() {
var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_alert('LOADING', 'Submitting ...');
}
	function changeCategory(element) {
		$this =  $(element);
		if($this.val() != <?php echo "'High'"; ?> && $this.val() != "") {
			$('#SupportDesk_system_down').parents('.row').addClass('hidden');
		}else if($this.val() == <?php echo "'High'"; ?> || $this.val() == null)
			{
			   if(document.getElementById('environment').value=='Test' || document.getElementById('environment').value=='UAT' ){
						var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_notification('ERROR MESSAGE', 'Kindly note that  High Severity issues can only be logged on Production environment.<br/>Thank you.', action_but);
				 $("#severity").val('');
					}
				$('#SupportDesk_system_down').parents('.row').removeClass('hidden');
			}	}
	function changeEnvironment(element){
			 if(document.getElementById('severity').value=='High' && (document.getElementById('environment').value=='Test' || document.getElementById('environment').value=='UAT')){
			 			var action_but = {
									"Ok": {
										click: function() 
										{
											$(this).dialog('close');
										},
										class: 'ok_button'	
									} 
							};
				custom_notification('ERROR MESSAGE', 'Kindly note that High Severity issues can only be logged on Production environment.<br/>Thank you.', action_but);
				 $("#environment").val('');		 }	}
</script>