<div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('final_invoice_number')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->final_invoice_number); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('invoice_title')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(ucwords($model->invoice_title)); ?></div></div>
<div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->customer->name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('project_name')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->project_name); ?></div>	</div>
<div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('id_ea')); ?></div>
	<div class="general_col2 "><?php if (empty($model->ea->ea_number)){  echo CHtml::encode("");   }else{  echo Receivables::getEASerInvoice($model->id_ea);} ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->rCurrency->codelkup); ?></div></div>
<div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('net_amount')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->net_amount)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('gross_amount')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->gross_amount)); ?></div></div>
<?php if ($model->partner != null && $model->sold_by != Maintenance::PARTNER_SNS) {?><div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('sns_share')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->sns_share."%"); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('partner')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->rPartner->codelkup); ?></div></div>
	<?php if($model->partner != '77'){?><div class="view_row">	
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('partner_inv')); ?></div>
		<div class="general_col2 "><?php if( $model->partner=='78' || $model->partner=='1218'  || $model->partner=='1336'){ echo CHtml::encode($model->partner_inv); } if($model->partner=='79'){echo CHtml::encode($model->span_partner_inv);} if($model->partner=='201' || $model->partner=='554'){ echo CHtml::encode($model->snsapj_partner_inv); }?></div>	
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('partner_amount')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->partner_amount)); ?></div></div><?php }?>
<div class='view_row'>
	<?php if($model->partner != '77'){?>
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('partner_status')); ?></div>
		<div class="general_col2 status_partner"><?php echo CHtml::encode($model->partner_status); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('id_assigned')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Users::getUsername($model->id_assigned)); ?></div>
		<?php } else{ ?>
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('id_assigned')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Users::getUsername($model->id_assigned)); ?></div>	<?php }?></div>		
<?php } else {?><div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('sns_share')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->sns_share."%"); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('id_assigned')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Users::getUsername($model->id_assigned)); ?></div></div><?php } ?>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('payment_procente')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->payment_procente."%"); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('payment')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->payment); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('printed_date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode((!empty($model->printed_date) && $model->printed_date != '0000-00-00 00:00:00') ? date('d/m/Y', strtotime($model->printed_date)) : "");?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Pay before date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode((!empty($model->printed_date) && $model->printed_date != '0000-00-00 00:00:00') ?date('d/m/Y', strtotime( $model->printed_date." +1 month" )) : ""); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('old')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->old); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('age')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->getAge()); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 status_invoice "><?php echo CHtml::encode($model->status); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('paid_date')); ?></div>
	<div class="general_col4 paid_date">
	<?php if ((($model->partner=='77' && $model->status == Invoices::STATUS_PAID) || ($model->partner!='77' && $model->partner_status == Invoices::STATUS_PAID) ) && !empty($model->paid_date) && $model->paid_date != '0000-00-00') { ?>
		<?php echo CHtml::encode(date('d/m/Y', strtotime($model->paid_date)));?>
	<?php } ?>	</div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Transfer#')); ?></div>
	<div class="general_col2 "><?php echo Invoices::getTransfersPerInvoice($model->invoice_number); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->notes); ?></div></div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('remarks')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->remarks); ?></div>

	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('invoice_number')); ?></div>
	<div class="general_col4 "><?php echo Receivables::getlinkperInvoice($model->invoice_number); ?></div>
</div>	
<div class="horizontalLine smaller_margin"></div>