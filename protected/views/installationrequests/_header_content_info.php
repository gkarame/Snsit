<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('app_url')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->app_url); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('app_server_hostname')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->app_server_hostname); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo  CHtml::encode($model->getAttributeLabel('app_username')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->app_username); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('app_password')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->app_password); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('db_server_hostname')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->db_server_hostname); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('db_name')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->db_name); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('db_username')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->db_username); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('db_password')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->db_password); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('db_local_bkup')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->db_local_bkup); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('infor_local_bkup')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->infor_local_bkup); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('license_type')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->getLicenseTypeLabel($model->license_type)); ?></div>
</div>