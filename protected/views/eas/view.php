<div class="mytabs ea_edit">
	<div id="ea_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EA HEADER');?></span>
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<br clear="all" />
	</div>
	<div class="attachDiv">
		<div class="label inline-block"></div>
		<div class="files inline-block" data-toggle="modal-gallery" data-target="#modal-gallery">
			<?php if (($filepath = $model->getFile(true, true)) != null) {	$path_parts = pathinfo($filepath);	?>
			<div class="box template-download fade" id="tr0">
				<div class="title">
					<a href="<?php echo $model->getFile(false, true);?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
				</div>				       	
		       	<div class="size">
		        	<span><?php echo Utils::getReadableFileSize(filesize($filepath));?></span>
		        </div>			        
				<div class="delete">
					<button class="btn btn-danger delete"
						 data-url="<?php echo $this->createUrl( "upload", array( "_method" => "delete", "file" => $filepath ));?>" 
						data-type="POST">
					</button>
				</div>
			</div>	<?php } ?>
		</div>
	</div>
	<div id="ea_items">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EA ITEMS');?></span>
		</div>
		<div class="horizontalLine smaller_margin"></div>
		<div id="ea_items_content">
			<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid','dataProvider'=>$model->items,
				'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
				'columns'=>array(
					array( 'name' => 'description', 'header'=> Yii::t('translations', 'Description'), 'value' => '$data->getDescriptionGrid()', 'type'=>'raw' ),
					'amount',					
					array( 'header' => 'Currency', 'value' => '$data->ea->eCurrency->codelkup'),
					'man_days',
					array( 'header' => 'Man Day Rate', 'value' => '$data->getManDayRate()',),
					array( 'name' => 'settingsCodelkup.codelkup', 'header' => $model->getItemsCodelist(), 'value' => 'isset($data->settingsCodelkup->codelkup) ? $data->settingsCodelkup->codelkup : ""',
						'visible' => $model->getItemsCodelist() ? true : false,), ),	));?>		
			<div class="total_amounts totalrow">
				<?php if ($model->TM !='1') { ?>
				<div class="column inline-block">
					<span class="title"><?php echo Yii::t('translations', 'TOTAL AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="total_amount" class="value"><?php echo $model->getTotalAmount();?></span>
				</div>
				<?php if($model->ea->category != 454) { ?>
				<div class="column inline-block middleitem">
					<span class="title"><?php echo Yii::t('translations', 'TOTAL MAN DAYS');?></span>
					<br /><br />			
					<span id="total_man_days" class="value"><?php echo $model->getTotalManDays();?></span>
				</div> <?php } }?>	
				<?php if($model->ea->category != 454) { ?>
				<div class="column inline-block middleitem">
					<span class="title"><?php echo Yii::t('translations', 'AVERAGE MAN DAY RATE');?></span>
					<br /><br />
					<span id="man_day_rate" class="value"><?php echo $model->getManDayRate();?></span>
				</div>	<?php } ?>
				<div class="column inline-block last">
					<span class="title"><?php echo Yii::t('translations', 'DISCOUNT');?></span>
					<br /><br />
					<span class="value"><?php echo $model->discount;?></span>
				</div>
			</div>
			<div class="net_amounts totalrow">
				<div class="column inline-block">
					<span class="red title"><?php echo Yii::t('translations', 'NET AMOUNT').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_amount" class="value"><?php echo $model->getNetAmount();?></span>
				</div>
				<?php if($model->ea->category != 454) { ?>
				<div class="column  inline-block middleitem">
					<span class="red title"><?php echo Yii::t('translations', 'NET MAN DAY RATE').' ('.$model->eCurrency->codelkup.') ';?></span>
					<br /><br />
					<span id="net_man_day_rate" class="value"><?php echo $model->getNetManDayRate();?></span>
				</div>	<?php } ?>
			</div>
		</div>
		<br clear="all" />
	</div>
	<div id="ea_terms">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'PAYMENT TERMS');?></span>
		</div>
		<div class="horizontalLine smaller_margin"></div>
		<div id="ea_items_content">
			<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'terms-grid','dataProvider'=>$model->terms,
				'summaryText' => '','pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
				'columns'=>array(
					array('name' => 'payment_term','header' => 'Payment Term %',),
					'amount',
					array('name' => 'eMilestone.codelkup','header' => 'Milestone','value' => 'isset($data->eMilestone->codelkup) ? $data->eMilestone->codelkup : ""'),
				),
			)); ?>
		</div>
		<br clear="all" />
	</div>
	<div id="ea_notes">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'NOTES');?></span>
		</div>
		<fieldset id="notes">
			<?php $notes = Codelkups::getCodelkupsDropDown('ea_notes');	$ea_notes = $model->getNotes();
				foreach ($notes as $key => $note) { ?>
				<div class="row note_item chk" onclick="CheckOrUncheckInput(this)">
					<div class="input"></div>
					<input type="checkbox" name="checked[]" value="<?php echo $key;?>" 
					<?php echo in_array($key, $ea_notes) ? 'checked' : ''; echo 'disabled="disabled"';?> />
					<label><?php echo $note;?></label>
				</div>
			<?php }	?>
		</fieldset>
	</div>
</div>
<br clear="all" />
<script type="text/javascript">
	$(document).ready(function(){	});
	function showHeader(element){
		var url = $(element).attr('href');
		$.ajax({type: "POST", 	url: url,	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
						$('.edit_header_content').html(data.html);	$('.edit_header_content').removeClass('hidden');	$('.header_content').addClass('hidden');
				  	} } } });
	}
	function updateTerm(element, id){
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTerm');?>";
		if (id != 'new') { url += '/'+parseInt(id); }
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.terms_fieldset').serialize() + '&EaPaymentTerms['+id+'][id_ea]=<?php echo $model->id;?>' + '&id_ea=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_term').show();	}		
				  		$.fn.yiiGridView.update('terms-grid');		
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form); }
				  	} }	} });
	}
	function updateItem(element, id){
		var url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageItem');?>";
		if (id != 'new') { url += '/'+parseInt(id);	}
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.items_fieldset').serialize() + '&EasItems['+id+'][id_ea]=<?php echo $model->id;?>' + '&id_ea=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') {	$(element).parents('.tache.new').remove();	$('.new_term').show(); }		
				  		$.fn.yiiGridView.update('items-grid');					
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form); }
				  	} } } });		
	}
	function submitForm(){
		var data = $("#ea-form").serialize() + '&ajax=eas-form';
		$.ajax({type: "POST",	data: data,	dataType: "json", url : $("#ea-form").attr("action"),
		  	success: function(data) {
			  	if (data) {
				  	console.log(data);
				  	if (data.status == "saved") {
				  		$('.header_content').html(data.html);	$('.header_content').removeClass('hidden');	$('.edit_header_content').addClass('hidden');
				  		showErrors(data.error);
				  	} else {
				  		console.log("failure");
					  	if (data.status == "failure") {
					  		$.each(data.errors, function (id, message) {	$("#"+id+"_em_").html(message);	});
					  	}	} 	}	} });
	}
	function showItemForm(element, newItem){
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageItem');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({type: "POST",	url: url,	dataType: "json", data: {'id_ea':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {
					  		$('.new_item').hide();	$('.new_item').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>'); 	}
				  	} } } });
	}	
	function showTermForm(element, newItem){
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('eas/manageTerm');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({ type: "POST",	url: url,  	data: {'id_ea':<?php echo $model->id;?>},	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) {
					  		$('.new_term').hide();	$('.new_term').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>'); 	}
				  	} } } });
	}
	function submitDiscount(element){
		var val = $('#Eas_discount').val();
		if (val != '') { 
			$.ajax({type: "POST",
			  	url: '<?php echo Yii::app()->createAbsoluteUrl('eas/saveDiscount', array('id' => $model->id));?>',
				data: { 'Eas[discount]' : val},	dataType: "json",
			  	success: function(data) {
				  	if (data) {
					  	if (data.status == 'saved') { $.each(data.amounts, function(i, item) { $('#'+i).html(item); });	}
				  	} } }); }
	}
</script>