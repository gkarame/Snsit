<?php
/* @var $this TimesheetsController */
/* @var $data Timesheets */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('timesheet_cod')); ?>:</b>
	<?php echo CHtml::encode($data->timesheet_cod); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_user')); ?>:</b>
	<?php echo CHtml::encode($data->id_user); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('week')); ?>:</b>
	<?php echo CHtml::encode($data->week); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('week_start')); ?>:</b>
	<?php echo CHtml::encode($data->week_start); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('week_end')); ?>:</b>
	<?php echo CHtml::encode($data->week_end); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_status')); ?>:</b>
	<?php echo CHtml::encode($data->id_status); ?>
	<br />


</div>