<fieldset id="hr_details" class="create">
<div class="formColumn smallerFormColumn">	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'employment_date'); ?>
		<div class="dateInput">		<?php     $this->widget('zii.widgets.jui.CJuiDatePicker',array(      'model'=>$model->userHrDetails,       'attribute'=>'employment_date',    	'cssFile' => false,
		        'options'=>array(  		'id' => 'employment_date',   		'dateFormat'=>'dd/mm/yy'    	),	    ));	?>
			<div class="calendar"></div>	</div>	<?php echo CCustomHtml::error($model->userHrDetails, 'employment_date', array('id'=>"UserHrDetails_employment_date_em_")); ?>
	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'evaluation_date'); ?>
		<div class="dateInput">		<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model->userHrDetails,'attribute'=>'evaluation_date', 'cssFile' => false,'options'=>array('id' => 'expiry_date','dateFormat'=>'dd/mm/yy'),   ));		?>
			<div class="calendar"></div>	</div>	<?php echo CCustomHtml::error($model->userHrDetails, 'evaluation_date', array('id'=>"UserHrDetails_evaluation_date_em_")); ?>
	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'contract_expiry_date'); ?>	<div class="dateInput">	
			<?php   $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model->userHrDetails, 'attribute'=>'contract_expiry_date','cssFile' => false,'options'=>array('dateFormat'=>'dd/mm/yy'),));?>
			<div class="calendar"></div>	</div>	<?php echo CCustomHtml::error($model->userHrDetails, 'contract_expiry_date', array('id'=>"UserHrDetails_contract_expiry_date_em_")); ?>
	</div>
	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'Credit Card Bank'); ?>
		<div class="selectBg_create width125">	<?php echo CHtml::activeDropDownList($model->userHrDetails,'bank_dolphin', Codelkups::getCodelkupsDropDown('bank_code'),array('prompt' => Yii::t('translations', 'Choose Bank'))); ?>
		</div>	<?php echo CCustomHtml::error($model->userHrDetails,'bank_dolphin', array('id'=>"UserHrDetails_bank_dolphin_em_")); ?>
	</div>	</div>

<div class="formColumn">   	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'contract_signed'); ?>
		<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model->userHrDetails, 'contract_signed', array('y'=>Yii::t('translations', 'Yes'), 'n' => Yii::t('translations', 'No'))); ?>
		</div>	<?php echo CCustomHtml::error($model->userHrDetails,'contract_signed', array('id'=>"UserHrDetails_contract_signed_em_")); ?>
	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'hr_manual_signed'); ?>
		<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model->userHrDetails,'hr_manual_signed', array('y'=>Yii::t('translations', 'Yes'), 'n' => Yii::t('translations', 'No'))); ?>
		</div>	<?php echo CCustomHtml::error($model->userHrDetails,'hr_manual_signed', array('id'=>"UserHrDetails_hr_manual_signed_em_")); ?>
	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'evaluation_batch'); ?>	<div class="inputBg_create">
			<?php echo CHtml::activeTextField($model->userHrDetails,'evaluation_batch'); ?>	</div>	<?php echo CCustomHtml::error($model->userHrDetails,'evaluation_batch', array('id'=>"UserHrDetails_evaluation_batch_em_")); ?>
	</div>
	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'Auxiliary'); ?>
		<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model->userHrDetails,'aux_dolphin', Codelkups::getCodelkupsDropDown('aux_code'),array('prompt' => Yii::t('translations', 'Choose Auxiliary'))); ?>
		</div>	<?php echo CCustomHtml::error($model->userHrDetails,'aux_dolphin', array('id'=>"UserHrDetails_aux_dolphin_em_")); ?>
	</div>
	</div>

<div class="formColumn"> <div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'mof'); ?>	<div class="inputBg_create">
			<?php echo CHtml::activeTextField($model->userHrDetails, 'mof'); ?>		</div>	<?php echo CCustomHtml::error($model->userHrDetails, 'mof', array('id'=>"UserHrDetails_mof_em_")); ?>
	</div>	
	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'ssnf'); ?>	
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userHrDetails, 'ssnf'); ?></div>
			<?php echo CCustomHtml::error($model->userHrDetails, 'ssnf', array('id'=>"UserHrDetails_ssnf_em_")); ?>	</div>	
	<div class="row">
		<?php echo CHtml::activeLabelEx($model->userHrDetails,'bank_account'); ?>		<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userHrDetails, 'bank_account'); ?>
		</div>	<?php echo CCustomHtml::error($model->userHrDetails, 'bank_account', array('id'=>"UserHrDetails_bank_account_em_")); ?>	</div>
	<div class="row">	<?php echo CHtml::activeLabelEx($model->userHrDetails,'iban'); ?>	<div class="inputBg_create">
			<?php echo CHtml::activeTextField($model->userHrDetails,'iban'); ?>	</div>		<?php echo CCustomHtml::error($model->userHrDetails,'iban', array('id'=>"UserHrDetails_iban_em_")); ?>
	</div>
	</div> </fieldset>
