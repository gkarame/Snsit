<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'documents-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array(
			'autocomplete' => 'off'
		),
)); ?>
	<div class="formColumn">
		<div class="row">
			<?php echo $form->labelEx($model,'document_title'); ?>			
			<div class="inputBg_create">
				<?php echo $form->textField($model,'document_title', array( 'autocomplete'=>'off')); ?>
			</div>			
			<?php echo $form->error($model,'document_title'); ?>
		</div>
	</div>
	<div class="formColumn">
		<div class="row">
			<?php echo $form->labelEx($model,'id_category'); ?>			
			<div class="selectBg_create">
				<?php echo $form->dropDownList($model,'id_category', DocumentsCategories::getAll($model->model_table, $model->category ? $model->category->category : (isset($module_category) ? $module_category : NULL)), array('prompt' => Yii::t('translations', 'Choose a category'), 'onchange' => 'ShowSubCategory(this);')); ?>
			</div>			
			<?php echo $form->error($model,'id_category'); ?>
		</div>
	</div>	
	 
		<div class="row hidden">
			<?php echo $form->labelEx($model,'subcategory'); ?>			
			<div class="selectBg_create">
				<?php echo $form->dropDownList($model,'subcategory', DocumentsCategories::getSubcategoriesTypes(), array('prompt' => Yii::t('translations', 'Choose Type'))); ?>
			</div>			
			<?php echo $form->error($model,'subcategory'); ?>
		</div>
	
	<div class="row hidden">
			<?php echo $form->labelEx($model,'FBR'); ?>			
			<div class="selectBg_create">
				<?php echo $form->dropDownList($model,'fbr', DocumentsCategories::getFBRsPerProject($model->id_model), array('prompt' => Yii::t('translations', 'Choose FBR'))); ?>
			</div>			
			<?php echo $form->error($model,'fbr'); ?>
		</div>


	<div class="row textarea_row">
		<?php echo $form->labelEx($model,'description'); ?>
		<div class="textarea_div">
		<?php echo $form->textArea($model, 'description', array('rows'=>6)); ?>
		</div>
		<?php echo $form->error($model, 'description'); ?>
	</div>	
	<?php	Yii::import("xupload.models.XUploadForm");
		$this->widget( 'xupload.XUpload', array(
        	'url' => Yii::app( )->createUrl("documents/upload"),
            'model' => new XUploadForm(),
            'htmlOptions' => array('id'=>'documents-form'),
            'attribute' => 'file',	'autoUpload' => true,  'multiple' => false,
			'options' => array(
				'maxFileSize' => 15728640,		
				'completed' => 'js: function(e, data) {		$(".files div.box:not(:last)").remove();	}',
			),
        ));	?>	
	<div class="files inline-block" data-toggle="modal-gallery" data-target="#modal-gallery">
		<?php	if (($filepath = $model->getFile(true)) != null) {	$path_parts = pathinfo($filepath);	?>
		<div class="box template-download fade" id="tr0">
			<div class="title">
				<a href="<?php echo $this->createUrl('site/download', array('file' => $filepath));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
			</div>				       	
	       	<div class="size">
	        	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>
	        </div>			        
			<div class="delete">
				<button class="btn btn-danger delete"
					 data-url="<?php echo $this->createUrl( "upload", array(
                                "_method" => "delete",
                                "file" => $filepath
                            ));?>" 
					data-type="POST">
				</button>
			</div>
		</div>	<?php } ?>
	</div>
	<div class="row"><?php echo $form->error($model, 'file'); ?></div>	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('class'=>'submit')); ?>
	</div>	
	<br clear="all"/>
<?php $this->endWidget(); ?>
</div>
<br clear="all" />

<script>
$(function() {
	ShowSubCategory($("#Documents_id_category"));
});
	function ShowSubCategory(element) {
		$this =  $(element);
		if($this.val() == 30 || $this.val() == 31)
		{
			$('#Documents_subcategory').parents('.row').removeClass('hidden');
		}else{
			$('#Documents_subcategory').parents('.row').addClass('hidden');
		}	

		if($this.val() == 15)
		{
			$('#Documents_fbr').parents('.row').removeClass('hidden');
		}else{
			$('#Documents_fbr').parents('.row').addClass('hidden');
		}		
	}
</script>