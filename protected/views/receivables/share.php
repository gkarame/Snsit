<div class="headli"></div><div class="contentli"><?php $form = $this->beginWidget('CActiveForm', array(	'id'=>'shareby-form',	)); ?><fieldset class="shareby_fieldset">
		<div class="create"><div class="title">SHARE</div>	<div class="row clear">	<?php echo $form->labelEx($model,'to'); ?>	<div class="inputBg_create">
					<?php  echo $form->textField($model,'to', array('value'=>$billemail,'class' => 'auto_email',));	?>
				</div><?php echo CHtml::error($model,"to"); ?>	</div>
			<div class="row clear attachments">	<span class="attachm">*Attachment(s):</span> 
					<?php  foreach($filenames as $filename){ echo '<span class="attachm invoiceFile">'.$filename.'</span><br/>'; }		?>
			</div>	<div class="row clear">
				 Email Template: <br/>
					<?php echo CHtml::dropDownList('template_email', '', Codelkups::getCodelkupsDropDown('template_email'), array('prompt'=>'Choose your template', 'class'=>'codelist_dropdown','onchange'=>'getTemplate(value)','style'=>'width: 270px;')); ?>
			</div>	<div class="row clear">	<?php echo $form->labelEx($model,'header'); ?>	<div class="inputBg_create">
					<?php echo $form->textField($model,'header', array('value'=>$billname,)); ?>	</div>
				<?php echo CHtml::error($model,"header"); ?></div>	<div class="row clear">	<?php echo $form->labelEx($model,'body'); ?>
				<div class="textareaBg_create"> <?php echo $form->textArea($model,'body');  ?> </div> <?php echo CHtml::error($model,"body"); ?></div>			
			<div class="row clear">	<?php echo $form->labelEx($model,'footer'); ?>	<div class="textareaBg_create">
					<div class="contenteditable" contenteditable="true" id="ShareByForm_footer"><?php echo $model->footer;?></div>	</div><?php echo CHtml::error($model,"footer"); ?>	</div>
			<div class="row buttons clear">
				<a href="#" class="save customSaveBtn ua" onclick="shareReceivables(this);return false;"><?php echo Yii::t('translation', 'SEND');?></a>
				<div class="loader"></div><a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('.popup_shareby').fadeOut(100);$('.popup_shareby').html('');"><?php echo Yii::t('translation', 'CANCEL');?></a>
				<a href="javascript:void(0);" class="customCancelBtn ua soaBtn" onclick="getSoAFiles();"><?php echo Yii::t('translation', 'INCLUDE SOA');?></a>
			</div></div></fieldset><?php $this->endWidget(); ?></div><div class="ftrli"></div>
<script type="text/javascript">$(document).ready(function() {}); </script>