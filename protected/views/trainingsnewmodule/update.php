<div class="mytabs training_edit">	<?php $form=$this->beginWidget('CActiveForm', array(	'id'=>'trainings-form',	'enableAjaxValidation'=>false,	'htmlOptions' => array(		'class' => 'ajax_submit',		'enctype' => 'multipart/form-data',		),	)); ?>
	<div id="trainings_header" class="edit_header">		<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'Trainings HEADER');?></span>
		</div>	<div class="header_content tache">	<?php $this->renderPartial('_edit_header_content', array('model' => $model, 'edit' =>true));?>
		</div>	<br clear="all" /></div>	<div class="horizontalLine smaller_margin"></div>	</div>	<div class="row buttons">
		<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('onclick' => 'js:updateHeader(this);return false;','class'=>'marginl9')); ?></div>
		<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
	</div>	<?php $this->endWidget(); ?></div><br clear="all" />
<script type="text/javascript">	
	function updateHeader(element) {
		$.ajax({		type: "POST",		data: $('#header_fieldset').serialize()  + '&ajax=trainings-form',  	url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/updateheader', array('id' => $model->idTrainings));?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.url) {
				  		window.location= data.url;
				  	} else {
				  		if (data.status == 'success' && data.html) {
				  			$('.header_content').html(data.html);
				  		}
				  	}
				  	showErrors(data.error);
			  	}
	  		}
		});
	}	
</script>