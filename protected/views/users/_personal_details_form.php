<fieldset id="personal_details" class="create"> <div class="formColumn">	<div class="row">		<?php echo CHtml::activeLabelEx($model,'firstname'); ?>
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model,'firstname'); ?>		</div>			
			<?php echo CCustomHtml::error($model,'firstname', array('id'=>"Users_firstname_em_")); ?>	</div>	
		<div class="row">	<?php echo CHtml::activeLabelEx($model,'lastname'); ?>	<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'lastname'); ?>		</div>	<?php echo CCustomHtml::error($model,'lastname', array('id'=>"Users_lastname_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model,'username'); ?>		<div class="inputBg_create"><?php echo CHtml::activeTextField($model,'username', array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model,'username', array('id'=>"Users_username_em_")); ?>	</div>	<div class="row">
			<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'email'); ?>	<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model->userPersonalDetails, 'email', array('autocomplete'=>'off')); ?>	</div>			
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'email', array('id'=>"UserPersonalDetails_email_em_")); ?>	</div>		
		<?php if ($model->isNewRecord) { ?>	<div class="row">	<?php echo CHtml::activeLabelEx($model,'password_new'); ?>		
			<div class="inputBg_create">	<?php echo CHtml::activePasswordField($model,'password_new', array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model,'password_new', array('id'=>"Users_password_new_em_")); ?>
		</div>		<?php } ?>	<div class="row">	<?php echo CHtml::activeLabelEx($model,'active'); ?><div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model,'active', Users::getStatusList()); ?>	</div>	<?php echo CCustomHtml::error($model,'active', array('id'=>"Users_active_em_")); ?>
		</div>   <div class="row"><?php echo CHtml::activeLabelEx($model->userPersonalDetails,'gender'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails, 'gender', array('m'=>Yii::t('translations', 'Male'), 'f' => Yii::t('translations', 'Female'))); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'gender', array('id'=>"UserPersonalDetails_gender_new_em_")); ?>
		</div>		<div class="row">		<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'birthdate'); ?>			
			<div class="dateInput">			<?php 	    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model->userPersonalDetails,       'attribute'=>'birthdate',    	'cssFile' => false,	        'options'=>array(   		'dateFormat'=>'dd/mm/yy'		    	),	    ));		?>			
				<div class="calendar"></div>	</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'birthdate', array('id'=>"UserPersonalDetails_birthdate_new_em_")); ?>		</div>
	<div class="row"><?php echo CHtml::activeLabelEx($model->userPersonalDetails,'billable'); ?>	<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails,'billable',array('Yes','No')); ?>	</div>	<?php echo CCustomHtml::error($model->userPersonalDetails,'billable', array('id'=>"Users_billable_em_")); ?>
		</div></div> <div class="formColumn"><img height='1' border='0' src="../../images/empty.png" >
		<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'nationality'); ?>			
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userPersonalDetails, 'nationality'); ?>		</div>			
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'nationality', array('id'=>"UserPersonalDetails_nationality_new_em_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model->userPersonalDetails,'marital_status'); ?>			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails, 'marital_status', array('single' => Yii::t('translations', 'Single'), 'married' => Yii::t('translations', 'Married'))); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'marital_status', array('id'=>"UserPersonalDetails_marital_status_new_em_")); ?>
		</div>	<div class="row"><?php echo CHtml::activeLabelEx($model->userPersonalDetails,'job_title'); ?>		<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model->userPersonalDetails, 'job_title'); ?>	</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'job_title', array('id'=>"UserPersonalDetails_job_title_new_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'branch'); ?>			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails, 'branch', Codelkups::getCodelkupsDropDown('branch'),array('prompt' => Yii::t('translations', 'Choose branch'))); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'branch', array('id'=>"UserPersonalDetails_branch_new_em_")); ?>
		</div>		<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'unit'); ?>		<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails, 'unit', Codelkups::getCodelkupsDropDown('unit'),array('prompt' => Yii::t('translations', 'Choose unit'))); ?>
			</div>		<?php echo CCustomHtml::error($model->userPersonalDetails, 'unit', array('id'=>"UserPersonalDetails_unit_new_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'line_manager'); ?>
			<div class="selectBg_create">	<?php echo CHtml::activeDropDownList($model->userPersonalDetails, 'line_manager', Users::getLineManagers(), array('prompt' => Yii::t('translations', 'Choose line manager'))); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'line_manager', array('id'=>"UserPersonalDetails_gender_line_manager_em_")); ?>
		</div><div class="row">		<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'skype_id'); ?>
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userPersonalDetails, 'skype_id', array('autocomplete'=>'off')); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'skype_id', array('id'=>"UserPersonalDetails_skype_id_em_")); ?>
		</div>	<div class="row"><img height='5' border='0' src="../../images/empty.png" >
			<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'performance'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model->userPersonalDetails,'performance',array('Yes','No') ,array('prompt'=>' ')); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails,'performance', array('id'=>"Users_performance_em_")); ?>
		</div>	</div> 	<div class="formColumn">	<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'home_address'); ?>
			<div class="textareaBg_create">			<?php echo CHtml::activeTextArea($model->userPersonalDetails, 'home_address'); ?>			</div>	
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'home_address', array('id'=>"UserPersonalDetails_home_address_new_em_")); ?>
		</div>		<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'mobile'); ?>			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model->userPersonalDetails, 'mobile'); ?>		</div>
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'mobile', array('id'=>"UserPersonalDetails_mobile_new_em_")); ?>	</div>	
		<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'ice_contact'); ?>		<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model->userPersonalDetails, 'ice_contact'); ?>		</div>
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'ice_contact', array('id'=>"UserPersonalDetails_ice_contact_new_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'ice_mobile'); ?>		<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model->userPersonalDetails, 'ice_mobile'); ?>	</div>			
			<?php echo CCustomHtml::error($model->userPersonalDetails, 'ice_mobile', array('id'=>"UserPersonalDetails_ice_mobile_new_em_")); ?>
		</div>	<div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'extension'); ?>		
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userPersonalDetails, 'extension'); ?>
			</div>	<?php echo CCustomHtml::error($model->userPersonalDetails, 'extension', array('id'=>"UserPersonalDetails_extension_new_em_")); ?>
		</div><div class="row">	<?php echo CHtml::activeLabelEx($model->userPersonalDetails,'annual_leaves'); ?>		
			<div class="inputBg_create">	<?php echo CHtml::activeTextField($model->userPersonalDetails, 'annual_leaves'); ?>
			</div>		<?php echo CCustomHtml::error($model->userPersonalDetails, 'annual_leaves', array('id'=>"UserPersonalDetails_annual_leaves_new_em_")); ?>
		</div>	<div class="row" id="sns_admin">									
            	<div class="row <?php echo ($model->userPersonalDetails->sns_admin == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInput(this);">		
					<?php echo CHtml::activeLabelEx($model, 'Disable TS');?>
					<?php  echo CHtml::CheckBox('UserPersonalDetails[sns_admin]',($model->userPersonalDetails->sns_admin == '1')?'checked' : '' ); ?> 
				</div>	<?php echo  CCustomHtml::error($model,'sns_admin'); ?>	</div>
	<div class="row" id="pqa" style="padding-left:15px;">									
            	<div class="row <?php echo ($model->userPersonalDetails->pqa == '1')?'checked' : ''?>"  onclick="CheckOrUncheckInput(this);">		
					<?php echo CHtml::activeLabelEx($model, 'PROJECT QA');?>
					<?php  echo CHtml::CheckBox('UserPersonalDetails[pqa]',($model->userPersonalDetails->pqa == '1')?'checked' : '' ); ?> 
				</div> 	<?php echo  CCustomHtml::error($model,'pqa'); ?></div>	</div> </fieldset> 
<script type="text/javascript">
	function CheckOrUncheckInput(obj){
		var checkBoxDiv = $(obj);
		var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {
			checkBoxDiv.removeClass('checked');
			input.prop('checked', false);
		}else {
			checkBoxDiv.addClass('checked');
			input.prop('checked', true);
		}
	}
</script>
