<div class="view"><b><?php echo CHtml::encode($data->getAttributeLabel('id_maintenance')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id_maintenance), array('view', 'id'=>$data->id_maintenance)); ?>
	<br /><b><?php echo CHtml::encode($data->getAttributeLabel('short_description')); ?>:</b>
	<?php echo CHtml::encode($data->short_description); ?><br /><b><?php echo CHtml::encode($data->getAttributeLabel('customer')); ?>:</b>
	<?php echo CHtml::encode($data->customer); ?><br /><b><?php echo CHtml::encode($data->getAttributeLabel('owner')); ?>:</b>
	<?php echo CHtml::encode($data->owner); ?><br /><b><?php echo CHtml::encode($data->getAttributeLabel('product')); ?>:</b>
	<?php echo CHtml::encode($data->product); ?><br /><b><?php echo CHtml::encode($data->getAttributeLabel('support_service')); ?>:</b>
	<?php echo CHtml::encode($data->support_service); ?><br /><b><?php echo CHtml::encode($data->getAttributeLabel('frequency')); ?>:</b>
	<?php echo CHtml::encode($data->frequency); ?>	<br /></div>