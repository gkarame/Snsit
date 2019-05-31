<?php $form=$this->beginWidget('CActiveForm', array('id'=>'travel-form','enableAjaxValidation'=>false,)); ?>
<fieldset id="travel_fields" class="create">	<div class="formColumn">	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'id_user'); ?>
			<div class="selectBg_create"><?php echo CHtml::activeDropDownList($model, 'id_user', Users::getAllSelect(), array('prompt'=>Yii::t('translations', 'Select user'))); ?>
			</div><?php echo CCustomHtml::error($model,'id_user', array('id'=>"Travel_id_user_em_")); ?>	</div>	<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'id_customer'); ?>	<div class="selectBg_create"><?php echo CHtml::activeDropDownList($model, 'id_customer', Customers::getAllCustomersSelect(), array('prompt'=>Yii::t('translations', 'Select customer'), 'onchange' => 'refreshProjectListsTravel()')); ?>
			</div>	<?php echo CCustomHtml::error($model,'id_customer', array('id'=>"Travel_id_customer_em_")); ?>	</div>
		<div class="row">	<?php echo CHtml::activeLabelEx($model, 'id_project'); ?>	<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'id_project', Projects::getAllProjectsTrainingsSelect($model->id_customer), array('onchange' => 'refreshBillableBasedOnProjectEA(this);')); ?>
			</div>	<?php echo CCustomHtml::error($model,'id_project', array('id'=>"Travel_id_project_em_")); ?>	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'expense_type'); ?>		
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'expense_type', Travel::getAllExpenseTypesSelect(), array('prompt'=>Yii::t('translations', 'Select Type'))); ?>
			</div>	<?php echo CCustomHtml::error($model,'expense_type', array('id'=>"Travel_expense_type_em_")); ?>
		</div></div>	<div class="formColumn">		<div class="row">			<?php echo CHtml::activeLabelEx($model,'amount'); ?>			
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'amount'); ?>		</div>	<?php echo CCustomHtml::error($model,'amount', array('id'=>"Travel_amount_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'billable'); ?>		<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'billable', Travel::getBillabledSelect()); ?>
			</div>	<?php echo CCustomHtml::error($model,'billable', array('id'=>"Travel_billable_em_")); ?>	</div>
		<div class="row item inline-block normal">	<?php echo CHtml::activeLabelEx($model, 'date'); ?>
			<div class="dataRow">	<?php    $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "date",
'cssFile' => false,'options'=>array('dateFormat'=>'yy-mm-dd','showAnim' => 'fadeIn'),'htmlOptions' => array('class' => 'datefield'),    ));		?>	<span class="calendar calfrom"></span></div>
			<?php echo $form->error($model,'date'); ?>		</div>	</div>	<div class="horizontalLine smaller_margin"></div>	<div class="row">
	<div id="fileuploads" >
		<?php	Yii::import("xupload.models.XUploadForm");
			$this->widget( 'xupload.XUpload', array(
	        	'url' => Yii::app( )->createUrl("travel/upload"),
	            'model' => new XUploadForm(),
	            'htmlOptions' => array('id'=>'travel-form'),
				'formView' => 'small_form',
	            'attribute' => 'file',
				'autoUpload' => true,
	            'multiple' => false,
				'options' => array(
					'maxFileSize' => 15728640, 
					'submit' => "js:function (e, data) {
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."travel".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id_customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."travel".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
        				//var path = '".Yii::app( )->getBasePath( )."/../uploads/customers/{$model->id_customer}/travel/{$model->id}/"."';
						//var publicPath = '".Yii::app( )->getBaseUrl( )."/uploads/customers/{$model->id_customer}/travel/{$model->id}/"."';
        				var model_id = '".$model->id."';
	                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
	                    return true;
	                }",	'completed' => 'js: function(e, data) {
						console.log(data);
						$(".files div.box:not(:last)").remove();
					}',
				),       ));	?>	<div class="attachments_pic" ></div>
		<div class="files" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php if (($filepath = $model->getFile(true, true)) != null) {	$path_parts = pathinfo($filepath);  ?>
				<div class="box template-download fade" id="tr0">
					<div class="title">		<a href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
					</div>    	<div class="size">        	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>        </div>	
					<div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "travel/deleteUpload", array(
	                          	"id_customer" =>$model->id_customer,
								"model_id" => $model->id,					 			
	                          	"file" => $path_parts['basename'],
	                            ));?>" 
							data-type="POST">
						</button>	</div>	</div>		<?php } ?>		</div>		</div>	</div></fieldset>
<div class="row buttons saveDiv">	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
</div><?php $this->endWidget(); ?>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var testProjectActualsExpensesUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/testProjectActualExpenses');?>'; 
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>