<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Internal::getStatusLabel($model->status)); ?></div>
</div>

<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('project manager')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getNameById($model->project_manager)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('business manager')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Users::getNameById($model->business_manager)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('adddate')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->adddate))); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('eta')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->eta))); ?></div>
</div>
<div class="view_row">
	<div class="general_col1" style="text-transform:none;"><?php echo CHtml::encode('TOTAL MDs'); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->estimated_effort); ?></div>
	<div class="general_col3" style="text-transform:none;"><?php echo CHtml::encode('ACTUAL MDs'); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber(Internal::getTimeSpentPerProject($model->id))); ?></div>
</div>

<div class="view_row">	
	<div class="general_col1"><?php  echo CHtml::encode('Recipients');  ?></div>
	<div class="general_col2 " id="allrecipients"><?php echo CHtml::encode(Internal::getUsersNames($model->recipients)); ?></div>
	<?php if ($model->status == 1) { ?>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('close_date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->close_date))); ?></div>	
	<?php }?>
</div> 
<div class="horizontalLine smaller_margin"></div>