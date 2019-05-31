<div class="mytabs support_edit">
	<div id="support_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'TASK INFORMATION');?></span>			
		</div>
		<div class="header_content tache">	
			<div class="view_row">
				<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode(chunk_split($model->description, 58, ' ')); ?></div>
				<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('priority')); ?></div>
				<div class="general_col4 "><?php echo CHtml::encode(InternalTasks::getPriority($model->priority)); ?></div>
			</div>
			<div class="view_row">
				<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode(InternalTasks::getStatus($model->status)); ?></div>
				<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('users')); ?></div>
				<div class="general_col4 "><?php echo CHtml::encode(InternalTasks::getAllUsersTask($model->id)); ?></div>
			</div>
			<div class="view_row">
				<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('eta')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode($model->eta); ?></div>
				<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('close_date')); ?></div>
				<div class="general_col4 "><?php echo CHtml::encode(($model->close_date == '0000-00-00 00:00:00' || $model->close_date == null) ? "": date('d/m/Y', strtotime($model->close_date))); ?></div>
			</div>
			<div class="view_row">
				<div class="general_col1" style="text-transform:none;"><?php echo CHtml::encode('EMDs'); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode($model->estimated_effort); ?></div>
				<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('actual')); ?></div>
				<div class="general_col4 "><?php echo CHtml::encode(InternalTasks::getTimeSpent($model->id)); ?></div>
			</div>
			<div class="view_row">
				<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
				<div class="general_col2 "><?php echo CHtml::encode($model->notes); ?></div>
			</div>
		</div>				
		<div class="hidden edit_header_content tache new" style="width:97%"></div>
		<br clear="all" />
	</div>
</div>