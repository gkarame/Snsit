<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('dep_no')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->dep_no); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_customer')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Customers::getNameById($model->id_customer)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->description); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('dep_date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->dep_date); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('infor_version')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->infor_version)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('dep_version')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->dep_version); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('module')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->module); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Deployments::getStatusLabel($model->status)); ?></div>	
</div>
<?php if ($model->source == '663') { ?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('source')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Projects::getNameById($model->source)); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('assigned_srs')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->assigned_srs); ?></div>
</div>
<div class="view_row">	
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('location')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->location); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('user')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Users::getNameById($model->user)); ?></div>
</div>	
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->notes); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('adddate')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->adddate))); ?></div>
</div>	
	<?php }else{ ?>
<div class="view_row">	
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('source')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Projects::getNameById($model->source)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('location')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->location); ?></div>
</div>
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('user')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getNameById($model->user)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->notes); ?></div>
</div>
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('adddate')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->adddate))); ?></div>
</div>
	<?php }?>
<div class="horizontalLine smaller_margin"></div>