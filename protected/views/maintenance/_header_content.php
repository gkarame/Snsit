<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('contract_description')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->contract_description); ?></div>
	<div class="general_col3"><?php echo CHtml::encode("Customer ID"); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->customer0->name); ?></div>
</div><div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('owner')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->owner0->codelkup); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('product')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->product0->codelkup); ?></div>
</div><div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('support_service')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(empty($model->supportService->codelkup)?" ":$model->supportService->codelkup); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('frequency')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->frequency0->codelkup); ?></div>
</div><div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('original_amount')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->original_amount)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->currency0->codelkup); ?></div>
</div><div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Amount USD')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->amount)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Net Amount USD')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->amount*($model->sns_share)/100)); ?></div>
</div><div class="view_row"><?php switch ($model->frequency0->codelkup) { 
	case 'Biyearly': ?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Periodic Amount USD')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber(($model->amount*($model->sns_share)/100)/2)); ?></div>
	<?php break;
	case 'Quarterly': ?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Periodic Amount USD')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber(($model->amount*($model->sns_share)/100)/4)); ?></div>
	<?php break;
	case 'Monthly':	?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Periodic Amount USD')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber(($model->amount*($model->sns_share)/100)/12)); ?></div>
	<?php break;
		case 'Yearly': ?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Periodic Amount USD')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber(($model->amount*($model->sns_share)/100))); ?></div>
	<?php break; }	?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Partner Amount USD')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->amount*(1-$model->sns_share/100))); ?></div>
</div><div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('escalation_factor')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->escalation_factor.'%'); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('real_esc')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->real_esc.'%'); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('SNS SHARE')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->sns_share.'%'); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('licenses')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->licenses); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('starting_date')); ?></div>
	<div class="general_col2 "><?php echo empty($model->starting_date)? " ": date('d/m/Y',strtotime($model->starting_date)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->status); ?></div>
</div><div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('contract_duration')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->contract_duration); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('cpi')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->cpi); ?></div>
</div><div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('po_renewal')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->po_renewal); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('wms_db_type')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(empty($model->wmsdbtype->codelkup)?" ":$model->wmsdbtype->codelkup); ?></div>
</div><div class="view_row">	
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('sw_version')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(empty($model->swversion->codelkup)?" ":$model->swversion->codelkup); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('nbwarehourses')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(empty($model->nbwarehourses)?" ":$model->nbwarehourses); ?></div>
</div><div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('end_customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->endcustomer->name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('ea')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(empty($model->ea)?" ":$model->ea); ?></div>
</div>
<?php if($model->support_service != 503) {?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('sma_recipients')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(empty($model->sma_recipients)?" ": Sma::getrecep($model->sma_recipients)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('sma_instances')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(empty($model->sma_instances)?" ":$model->sma_instances); ?></div>
</div>
<?php } ?>
	<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(empty($model->notes)?" ":$model->notes); ?></div>
	<?php if($model->owner !=77 && (  $model->product == 1402 || $model->product== 1268)){ ?>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('net_share')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->net_share.'%'); ?></div>
	<?php } ?>
</div><div class="horizontalLine smaller_margin"></div>