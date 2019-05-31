<div class="view_row">	<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->fullname)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('branch')); ?></div>
	<div class="general_col4 ">
		<?php echo isset($model->userPersonalDetails->rBranch) ? CHtml::encode($model->userPersonalDetails->rBranch->codelkup) : ''; ?>
	</div></div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->username)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('unit')); ?></div>
	<div class="general_col4 ">		<?php echo isset($model->userPersonalDetails->rUnit) ? CHtml::encode($model->userPersonalDetails->rUnit->codelkup) : ''; ?>
	</div></div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('email')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->email); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('line_manager')); ?></div>
	<?php if(isset($model->userPersonalDetails->lineManager->username)){?>
		<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->lineManager->firstname." ".$model->userPersonalDetails->lineManager->lastname); ?></div>
	<?php }?></div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('active')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->active == '0' ? 'Inactive' : 'Active' ); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('mobile')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->mobile); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('gender')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->gender == 'f' ? 'Female' : ($model->userPersonalDetails->gender == 'm' ? 'Male' : '') ); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('home_address')); ?>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->home_address); ?></div></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('birthdate')); ?></div>
	<div class="general_col2 "><?php echo !empty ($model->userPersonalDetails->birthdate) ? date('d/m/Y', strtotime($model->userPersonalDetails->birthdate)) : ''; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('ice_contact')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->ice_contact); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('nationality')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->nationality); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('ice_mobile')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->ice_mobile); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('marital_status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->marital_status); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('extension')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->extension); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('job_title')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->job_title); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('skype_id')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->skype_id); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('disable_ts')); ?></div>
	<div class="general_col2 "><?php if($model->userPersonalDetails->sns_admin=='1'){echo 'Yes';}else{echo 'No';}; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('annual_leaves')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->annual_leaves); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('billable')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userPersonalDetails->billable == '0' ? 'Yes' : 'No' ); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('performance')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userPersonalDetails->performance == '0' ? 'Yes' : 'No' ); ?></div>
	</div><div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->userPersonalDetails->getAttributeLabel('PROJECT QA')); ?></div>
		<div class="general_col2 "><?php if($model->userPersonalDetails->pqa=='1'){echo 'Yes';}else{echo 'No';}; ?></div>
</div><br clear="all" />