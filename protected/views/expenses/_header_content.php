<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('no')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->no); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('customer_id')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->customer->name); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('user_id')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->user->firstname.' '.$model->user->lastname);?></div>
	<div class="general_col1"><?php echo (isset($model->training))?CHtml::encode($model->getAttributeLabel('training')):CHtml::encode($model->getAttributeLabel('project_id')); ?></div>
	<div class="general_col2 "><?php echo (isset($model->training))?  CHtml::encode(Trainings::getName($model->project_id)):CHtml::encode($model->project->name);?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 status_expenses"><?php echo CHtml::encode($model->status);?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('startDate')); ?></div>
	<div class="general_col4 "><?php echo date('d/m/Y',strtotime($model->startDate)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('endDate')); ?></div>
	<div class="general_col2 "><?php echo date('d/m/Y',strtotime($model->endDate)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('creationDate')); ?></div>
	<div class="general_col4 "><?php echo date('d/m/Y',strtotime($model->creationDate)); ?></div>
</div>
<div class="horizontalLine smaller_margin"></div>