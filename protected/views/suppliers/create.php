<?php $form=$this->beginWidget('CActiveForm', array(	'id'=>'suppliers-form',	'enableAjaxValidation'=>false,)); ?><fieldset id="supplier_fields" class="create"> 	<div class="formColumn">	<div class="row">	<?php echo CHtml::activeLabelEx($model,'name'); ?>		
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'name',array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model,'name', array('id'=>"Suppliers_name_em_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model,'currency'); ?>			
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'currencyId', Codelkups::getCodelkupsDropDown('currency'), array('prompt'=>Yii::t('translations', 'Select currency'))); ?>
			</div><?php echo CCustomHtml::error($model,'currency', array('id'=>"Suppliers_currency_id_")); ?>
		</div>		<div class="row">	<?php echo CHtml::activeLabelEx($model,'main_contact'); ?>			<div class="inputBg_create"><?php echo CHtml::activeTextField($model,'main_contact',array('autocomplete'=>'off')); ?>
			</div><?php echo CCustomHtml::error($model,'main_contact', array('id'=>"Suppliers_main_contact_em_")); ?>
		</div><div class="row"><?php echo CHtml::activeLabelEx($model,'bank_name'); ?>	<div class="inputBg_create"><?php echo CHtml::activeTextField($model,'bank_name'); ?>		</div>			
			<?php echo CCustomHtml::error($model,'bank_name', array('id'=>"Suppliers_bank_name_em_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model,'swift'); ?>			
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'swift',array('autocomplete'=>'off')); ?>
			</div>		<?php echo CCustomHtml::error($model,'swift', array('id'=>"Suppliers_swift_em_")); ?>
		</div><div class="textBox three-smaller inline-block itms" title="Emails separated by semi-colon, up to 500 characters">
		<div class="row">			<?php echo CHtml::activeLabelEx($model,'emails'); ?>			
			<div class="inputBg_create">				<?php echo CHtml::activeTextField($model,'emails',array('autocomplete'=>'off')); ?>			</div>		
			<?php echo CCustomHtml::error($model,'emails', array('id'=>"Suppliers_emails_em_")); ?>		</div></div>	</div>	<div class="formColumn">		<div class="row">	<?php echo CHtml::activeLabelEx($model,'id_type'); ?>		
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'id_type', Codelkups::getCodelkupsDropDown('supplier_type'), array('prompt'=>Yii::t('translations', 'Select type'))); ?>
			</div>			<?php echo CCustomHtml::error($model,'id_type', array('id'=>"Suppliers_id_type_em_")); ?>	</div>
		<div class="row"><?php echo CHtml::activeLabelEx($model,'country'); ?>			
			<div class="selectBg_create"><?php echo CHtml::activeDropDownList($model, 'countryId', Codelkups::getCodelkupsDropDown('country'), array('prompt'=>Yii::t('translations', 'Select country'))); ?>
			</div>	<?php echo CCustomHtml::error($model,'country', array('id'=>"Suppliers_country_id_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model,'main_phone'); ?><div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'main_phone',array('autocomplete'=>'off')); ?>		</div>			<?php echo CCustomHtml::error($model,'main_phone', array('id'=>"Suppliers_main_phone_em_")); ?>	</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model,'account_name'); ?><div class="inputBg_create"><?php echo CHtml::activeTextField($model,'account_name',array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model,'account_name', array('id'=>"Suppliers_account_name_em_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model,'dolphin_code'); ?><div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'dolphin_code',array('autocomplete'=>'off')); ?></div>		
			<?php echo CCustomHtml::error($model,'dolphin_code', array('id'=>"Suppliers_dolphin_code_em_")); ?>	</div>	</div><div class="formColumn"><div class="row"><?php echo CHtml::activeLabelEx($model,'category'); ?>		
			<div class="selectBg_create"><?php echo CHtml::activeDropDownList($model, 'category',Suppliers::getCategories(), array('prompt'=>Yii::t('translations', 'Select Category'))); ?>
			</div>	<?php echo CCustomHtml::error($model,'category', array('id'=>"Suppliers_category_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model, 'city'); ?>		<div class="inputBg_create">	<?php echo CHtml::activeTextField($model, 'city',array('autocomplete'=>'off')); ?>	</div>		
			<?php echo CCustomHtml::error($model, 'city', array('id'=>"Suppliers_city_em_")); ?></div>	<div class="row">	<?php echo CHtml::activeLabelEx($model,'other_phone'); ?>			
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'other_phone',array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model,'other_phone', array('id'=>"Suppliers_other_phone_em_")); ?></div>
		<div class="row">	<?php echo CHtml::activeLabelEx($model,'iban'); ?>		
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'iban',array('autocomplete'=>'off')); ?>
			</div>		<?php echo CCustomHtml::error($model,'iban', array('id'=>"Suppliers_iban_em_")); ?>
		</div><div class="row">	<?php echo CHtml::activeLabelEx($model,'preffered'); ?>			
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model, 'preffered',Suppliers::getPreffered(), array('prompt'=>'')); ?>
			</div>	<?php echo CCustomHtml::error($model,'preffered', array('id'=>"Suppliers_preffered_em_")); ?>		</div>	</div></fieldset>
<div class="row buttons saveDiv">	<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
</div><?php $this->endWidget(); ?>