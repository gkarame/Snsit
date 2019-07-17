<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->eCustomer->name); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('project')); ?></div>
	<div class="general_col4 "><?php echo ($model->maintenance == 0 ) ? CHtml::encode(Projects::getNameById($model->project)): CHtml::encode(Maintenance::getMaintenanceDescription(substr($model->project, 0, -1))); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('expected_starting_date')); ?></div>
	<div class="general_col2 "><?php echo !empty ($model->expected_starting_date) ? date('d/m/Y', strtotime($model->expected_starting_date)) : ''; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('deadline_date')); ?></div>
	<div class="general_col4 "><?php echo !empty ($model->deadline_date) ? date('d/m/Y', strtotime($model->deadline_date)) : ' '; ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('installation_locally')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->getlocallycustomerlabel($model->installation_locally)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('installation_location')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(isset($model->installation_location)? $model->getInstallationLabel($model->installation_location) : ''); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('disaster_recovery')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->getDisasterLabel($model->disaster_recovery)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('assigned_to')); ?></div>
	<div class="general_col4 "><?php echo ! ($model->assigned_to == 0) ? CHtml::encode($model->eAssigned_to->fullname) : ' '; ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('requested_by')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->eRequested_by->fullname); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->notes); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('prerequisites')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->getPrerequisitesLabel($model->prerequisites)); ?></div>
</div>
<?php if ($model->installation_locally != InstallationRequests::LOCALLY_LOCALLY && $model->installation_location != InstallationRequests::INSTALL_LOCATION_ONSITE){ ?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('customer_contact_name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->customer_contact_name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('customer_contact_email')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->customer_contact_email); ?></div>
</div>
<!--
/*
 * Author: Mike
 * Date: 12.07.19
 * Under Environment add a 3rd Radio Button: Hosted  -  When Clicked it shows in addition to Customer Contact Name and Email, Hosting Contact Name & Email   On different note, add inside the IR, under Authentication drop don list: Hybrid
 */
 -->
<?php } ?>
<?php if ($model->installation_locally == InstallationRequests::HOSTED){ ?>
    <div class="view_row">
        <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('hosting_contact_name')); ?></div>
        <div class="general_col2 "><?php echo CHtml::encode($model->hosting_contact_name); ?></div>
        <div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('hosting_contact_email')); ?></div>
        <div class="general_col4 "><?php echo CHtml::encode($model->hosting_contact_email); ?></div>
    </div>
<?php } ?>
<?php if ($model->installation_locally != InstallationRequests::LOCALLY_LOCALLY){ ?>
<div class="view_row">
	<div class="general_col1"><?php echo "Connections Link"; ?></div>
	<div class="general_col2 "> <a href="<?php echo Yii::app()->createAbsoluteUrl("customers/view/".$model->customer);?>"> Click Here </a></div>
</div> <?php } ?>