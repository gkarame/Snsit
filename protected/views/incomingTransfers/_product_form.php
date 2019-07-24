<div class="tache new">
	<div class=""></div>
	<fieldset class="new_product">
	<?php $id = $model->isNewRecord ? 'new' : $model->id;	?>
		<div class="textBox  marginr22 small_one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]final_invoice_number"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeDropDownList($model,"[$id]final_invoice_number",IncomingTransfers::getInvoices($model->id_it, $model->invoice_number, $model->IncomingTransfer->id_customer, $model->IncomingTransfer->partner, $model->IncomingTransfer->month), array('onchange'=>'updateInvoiceFields(this);','prompt' =>'', 'style'=>'border:none;margin-top:3px;margin-left:2px;width:100%', 'on'=>'update' )); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]final_invoice_number",array('id'=>"IncomingTransfersDetails_".$id."_final_invoice_number_em_")); ?>
		</div>
		<div class="textBox marginr22 inline-block width180 first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]original_amount"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]original_amount", array('class' => 'IncomingTransfersDetails_original_amount','readonly '=>'readonly ', 'style'=>'opacity: 0.7;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]original_amount",array('id'=>"IncomingTransfersDetails_".$id."_original_amount_em_")); ?>
		</div>
		<div class="textBox  marginr22 small_one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]original_currency"); ?> </div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]original_currency",Codelkups::getCodelkupsDropDown('currency'), array('prompt'=>'','style'=>'opacity: 0.7;pointer-events:none;border:none;margin-top:3px;margin-left:2px;width:100%;','class' => 'IncomingTransfersDetails_original_currency')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]original_currency",array('id'=>"IncomingTransfersDetails_".$id."_original_currency_em_")); ?>
		</div>
		<div class="textBox marginr22 inline-block width200 first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]rate"); ?> </div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]rate", array('class'=>'IncomingTransfersDetails_rate')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]rate",array('id'=>"IncomingTransfersDetails_".$id."_rate_em_")); ?>
		</div>

		<div class="textBox  marginr22 small_one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]paid_amount"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeDropDownList($model,"[$id]paid_amount",IncomingTransfersDetails::getPaidOptions(), array('prompt'=>'','onchange'=>'validateamount(this);','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;','class' => '')); ?>
			
			</div>
			<?php echo CCustomHtml::error($model, "[$id]paid_amount",array('id'=>"IncomingTransfersDetails_".$id."_paid_amount_em_")); ?>
		</div>
		<div class="textBox marginr22 inline-block width180 first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]received_amount", array('class' =>'IncomingTransfersDetails_received_amount')); ?> </div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]received_amount", array('class'=>'IncomingTransfersDetails_received_amount')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]received_amount",array('id'=>"IncomingTransfersDetails_".$id."_received_amount_em_")); ?>
		</div>
		<div class="textBox  marginr22 small_one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]received_currency"); ?> </div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]received_currency",Codelkups::getCodelkupsDropDown('currency'), array('readonly '=>'readonly ','prompt'=>'','style'=>'opacity: 0.7;pointer-events:none;border:none;margin-top:3px;margin-left:2px;width:100%;','class' => 'IncomingTransfersDetails_received_currency')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]received_currency",array('id'=>"IncomingTransfersDetails_".$id."_received_currency_em_")); ?>
		</div>
		
		<div style="right:75px;top:80%;" class="save" onclick="updateProduct(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;top:80%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
<script> 

function validateamount(element){
	$this = $(element);	var val = $this.val();
	original_currency=$('.IncomingTransfersDetails_original_currency').val();
	received_currency=$('.IncomingTransfersDetails_received_currency').val();	
	tot= $('.IncomingTransfersDetails_original_amount').val();
	rate=  $('.IncomingTransfersDetails_rate').val();
	//alert(rate);
	if(val == 1 && original_currency == received_currency){
		$('.IncomingTransfersDetails_received_amount').val(tot);
	}else if (val == 1 ) {
		/*$.ajax({ type: "POST",
			 		data: {'id': <?php echo $model->id_it; ?>, 'amount': tot} ,					
				  	url: "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/getAmtIncurrency');?>", dataType: "json",
				  	success: function(data) {
					  	if (data) {
					  		if (data.status == 'success') {
		$('.IncomingTransfersDetails_received_amount').val(data.net);
				} } } 
			});*/
		net= tot*rate;
		$('.IncomingTransfersDetails_received_amount').val(net);
	}
	
} 
function updateInvoiceFields(element){
	$this = $(element);	var val = $this.val();
	var url = "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/getInvoiceDetail');?>";
	$.ajax({ type: "POST",
	 		data: {'id': val} ,					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'success') {
					  			document.getElementsByClassName('IncomingTransfersDetails_original_amount')[0].setAttribute("value", data.net);
					  			document.getElementsByClassName("IncomingTransfersDetails_original_currency")[0].value = data.curr;
		} } } 
	});
} 
</script>