<div class=" training_bg"><?php $can_modify=true; ?> </div>
<fieldset id="header_fieldset">
<tabel><tr>
<td><div class="textBox marginr22 inline-block width137">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status", IncomingTransfers::getStatusList(), array('class'=>'width131', 'value'=>$model->status)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"IncomingTransfers_status_em_")); ?>
</div></td>
<td>
	<div class="textBox marginr22 inline-block width137" >
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"received_amount"); ?></div>
		<div class="input_text">
			<div class="">
				<?php echo CHtml::activetextField($model, 'received_amount',array('autocomplete'=>'off','style'=>'width: 125px;top:4px;resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "received_amount", array('id'=>"IncomingTransfers_received_amount_em_")); ?>
</div>

</td>
<td><div class="textBox  marginr22 inline-block width137">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"currency"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "currency", Codelkups::getCodelkupsDropDown('currency'), array('class'=>'width131', 'value'=>$model->currency)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "currency", array('id'=>"IncomingTransfers_currency_em_")); ?>
</div></td>
<td>
	<div class="textBox marginr22 inline-block width137" >
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"bank"); ?></div>
		<div class="input_text">
			<div class="">
				<?php echo CHtml::activetextField($model, 'bank',array('autocomplete'=>'off','style'=>'width: 125px;top:4px;resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "bank", array('id'=>"IncomingTransfers_received_amount_em_")); ?>
</div>

</td>
</tr>
<tr>
<td><div class="textBox marginr22 inline-block width137">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"offsetting"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "offsetting", IncomingTransfers::getOffsettingList(), array('class'=>'width131', 'value'=>$model->currency)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "offsetting", array('id'=>"IncomingTransfers_offsetting_em_")); ?>
</div></td>
<td><div class="textBox  inline-block marginr22 width380">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"notes"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextField($model, 'notes',array('autocomplete'=>'off','class'=>' width370','style'=>'top:0px')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "notes", array('id'=>"IncomingTransfers_notes_em_")); ?>
</div></td></tr>
<tr><td>
<div class="textBox  inline-block width380" >
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"remarks"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextField($model, 'remarks',array('autocomplete'=>'off','class'=>'width370','style'=>'top:0px;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "remarks", array('id'=>"IncomingTransfers_remarks_em_")); ?>
</div></td>
</tr>
</tabel>
<div style="right:70px;top:75%;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
<div style="color:#333;top:75%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<div class="horizontalLine smaller_margin"></div>
<script>
</script>