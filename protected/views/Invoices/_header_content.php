<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('invoice_number')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->invoice_number); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('final invoice number')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode((isset($model->final_invoice_number))?$model->final_invoice_number:$model->old_sns_inv);?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->customer->name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('project_name')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->project_name); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('invoice_title')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->invoice_title); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('ea#')); ?></div>
	<div class="general_col4 "><?php echo ($model->id_ea != null)?CHtml::link(Yii::t('translation', Utils::paddingCode($model->id_ea)), array('eas/update','id'=>$model->id_ea)):""; ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('payment #')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->payment); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('payment %')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->payment_procente)." %"; ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->iCurrency->codelkup); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col4 status_invoice "><?php echo CHtml::encode($model->status); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('partner')); ?></div>
	<?php if($model->partner != null){?>	<div class="general_col2 "><?php echo CHtml::encode($model->iAuthor->codelkup); ?></div>
	<?php }else{ ?>	<div class="general_col2 "></div> <?php }?>
	<?php if($model->partner != '77'){?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('partner_inv')); ?></div>
	<div class="general_col4 "><?php if( $model->partner=='78' || $model->partner=='1218' || $model->partner=='1336' ){ echo CHtml::encode($model->partner_inv); } if($model->partner=='79'){echo CHtml::encode($model->span_partner_inv);} if($model->partner=='201' || $model->partner=='554'){ echo CHtml::encode($model->snsapj_partner_inv); } ?></div>
	<?php }?>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('sns_share')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->sns_share."%"); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('net_amount')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->net_amount)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('gross_amount')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->gross_amount)); ?></div>
	<?php if($model->partner != '77'){?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('partner_amount')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->partner_amount)); ?></div>
	<?php }?>
</div>
<div class="view_row">
	<?php $j = ($model->invoice_date_month)?(date("F", mktime(0, 0, 0,$model->invoice_date_month))." "):" ";  $j.= ($model->invoice_date_year)?$model->invoice_date_year:"";?>
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('invoice_date_month')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($j); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('sold_by')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(( $model->sold_by != 0)?$model->iUnit->codelkup:""); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('old')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->old); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('printed_date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode((isset($model->printed_date))?date('d/m/Y', strtotime($model->printed_date)):"");?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Pay before date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->printed_date)?date('d/m/Y', strtotime( $model->printed_date."+1 month" )):""); ?></div>
	<?php if($model->partner != '77'){?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('partner_status')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->partner_status); ?></div>
	<?php }else{?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Creation Date')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->ADDDATEINV))); ?></div>
	<?php }?>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('type')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Invoices::getType($model->type)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Transfer#')); ?></div>
	<div class="general_col4 "><?php echo Invoices::getTransfersPerInvoice($model->invoice_number); ?></div>
</div>
<?php if($model->partner != '77'){?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Creation Date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->ADDDATEINV))); ?></div>
	<?php if($model->type == 'Maintenance'){?>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('po')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->po);  ?></div>
	<?php }?>
</div>
<?php }?>

<?php if($model->partner != '77' && $model->type == 'Maintenance'){?>
<div class="view_row">
<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('escalation')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->escalation.'%');  ?></div>
</div>
<?php }?>

<?php if($model->partner == '77' && $model->type == 'Maintenance'){?>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('po')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->po);  ?></div>

	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('escalation')); ?></div>
	<div class="general_col4 "><?php echo ''.$model->escalation.'%';  ?></div>
</div>
<?php }?>

<div class="horizontalLine smaller_margin"></div>
<script type="text/javascript">
function updateAmt(value, id_invoice)
{
	$.ajax({
	 type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/updateAmtEscalation');?>",dataType: "json",data: {'value':value,'id_invoice':id_invoice},
	  	success: function(data) {  	if (data) { if (data.status == 'success') { $('#Invoices_gross_amount').val(data.gross_amount); } } } }); 
}

function changeInput(value,id_invoice,type){
$.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeInput');?>",dataType: "json",data: {'value':value,'id_invoice':id_invoice,'type':type},
	  	success: function(data) {  	if (data) { if (data.status == 'success') { console.log('da'); } } } }); }
</script>