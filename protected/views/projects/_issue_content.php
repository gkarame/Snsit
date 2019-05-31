<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('id_issue')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(str_pad($model->id_issue, 3, "0", STR_PAD_LEFT)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->description); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('priority')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->priority==0 ? "Low" :($model->priority==1? "Medium": "High")); ?></div>
	<div class="general_col3"><?php echo CHtml::encode("Assigned To"); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(ProjectsIssues::getAssignedto($model->id)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('type')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->type); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('fbr')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(ProjectsTasks::getTaskDescByid($model->fbr)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(ProjectsIssues::getStatus($model->status)); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('module')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->module)); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('logged_by')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getCredentialsbyId($model->logged_by)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('logged_date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date("d/m/Y", strtotime($model->logged_date))); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('lastupdateby')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getCredentialsbyId($model->lastupdateby)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('lastupdateddate')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date("d/m/Y", strtotime($model->lastupdateddate))); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('close_date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->close_date !="0000-00-00")? date("d/m/Y", strtotime($model->close_date)): " "); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('fix')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(chunk_split($model->fix, 58, ' ')); ?></div>
</div>
<div class="view_row">
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(chunk_split($model->notes, 29, ' ')); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('attachment')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->renderAttachment()); ?></div>
</div>
<script>

		

</script>