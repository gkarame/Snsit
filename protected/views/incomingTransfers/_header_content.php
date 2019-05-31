<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(IncomingTransfers::getCustomers($model->id)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('partner')); ?></div>
	<div class="general_col4 "><?php  echo CHtml::encode(Codelkups::getCodelkup($model->partner));  ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('received_amount')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->received_amount)); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col4 "><?php  echo CHtml::encode(Codelkups::getCodelkup($model->currency));  ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('bank_dolphin')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->bank_dolphin)); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('aux')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->aux)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('bank')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Utils::formatNumber($model->bank)); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('offsetting')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(IncomingTransfers::getOffsettingLabel($model->offsetting)); ?></div>
</div>

<?php  if ( IncomingTransfersDetails::displayRate($model->id,$model->currency)) {   ?>

<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(IncomingTransfers::getStatusLabel($model->status)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('rate')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(floatval($model->rate)); ?></div>

</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->notes); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('remarks')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->remarks); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_user')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getNameById($model->id_user)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('adddate')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode(date('d/m/Y', strtotime($model->adddate))); ?></div>
</div>

	<?php }else{ ?>

	<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(IncomingTransfers::getStatusLabel($model->status)); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_user')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Users::getNameById($model->id_user)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->notes); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('remarks')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->remarks); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('adddate')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(date('d/m/Y', strtotime($model->adddate))); ?></div>
	</div>

	<?php } ?>
