<div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('employment_date')); ?></div>
	<div class="general_col2 "><?php echo !empty ($model->userHrDetails->employment_date) ? date('d/m/Y', strtotime($model->userHrDetails->employment_date)) : ''; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('contract_expiry_date')); ?></div>
	<div class="general_col4 "><?php echo !empty ($model->userHrDetails->contract_expiry_date) ? date('d/m/Y', strtotime($model->userHrDetails->contract_expiry_date)) : ''; ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('evaluation_date')); ?></div>
	<div class="general_col2 "><?php echo !empty ($model->userHrDetails->evaluation_date) ? date('d/m/Y', strtotime($model->userHrDetails->evaluation_date)) : ''; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('hr_manual_signed')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userHrDetails->hr_manual_signed == 'y' ? 'Yes' : 'No'); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('evaluation_batch')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userHrDetails->evaluation_batch); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('mof')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userHrDetails->mof); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('contract_signed')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userHrDetails->contract_signed == 'y' ? 'Yes' : 'No'); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('ssnf')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userHrDetails->ssnf); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('bank_account #')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userHrDetails->bank_account); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('iban #')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userHrDetails->iban); ?></div>
</div>
<div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('Credit Card Bank')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->userHrDetails->rbank->codelkup); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->userHrDetails->getAttributeLabel('Auxiliary')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->userHrDetails->raux->codelkup); ?></div>
</div>
<br clear="all" />