<div class="tache new">
	<div class=""></div>
	<fieldset class="new_product">
	<?php $id = $model->isNewRecord ? 'new' : $model->id;	?>
		<div class="textBox small_one inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]id_product"); ?></div>
			<div class="input_text">
				<?php echo CHtml::activeDropDownList($model,"[$id]id_product",Codelkups::getCodelkupsDropDownIR($model->id_ir), array('onchange'=>'changeProduct(this);','prompt' =>'', 'style'=>'border:none;margin-top:3px;margin-left:2px;width:100%', 'on'=>'update' )); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]id_product",array('id'=>"InstallationrequestsProducts_".$id."_id_product_em_")); ?>
		</div>
	<?php if (InstallationrequestsProducts::checkMaint($model->id_ir)){ ?>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]version"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]version",InstallationrequestsProducts::getVersionList($model->id_ir), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]version",array('id'=>"InstallationrequestsProducts_".$id."_version_em_")); ?>
		</div>	<?php } ?>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]number_of_nodes"); ?> *</div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]number_of_nodes", array('style'=>'width:40px;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]number_of_nodes",array('id'=>"InstallationrequestsProducts_".$id."_number_of_nodes_em_")); ?>
		</div>
	<?php if (InstallationrequestsProducts::checkMaint($model->id_ir)){ ?>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]dbtype"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]dbtype",InstallationrequestsProducts::getdbtypeList($model->id_ir), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]dbtype",array('id'=>"InstallationrequestsProducts_".$id."_dbtype_em_")); ?>
		</div>	<?php } ?>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]db_collation"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]db_collation",InstallationrequestsProducts::getDBCollationList(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]db_collation",array('id'=>"InstallationrequestsProducts_".$id."_db_collation_em_")); ?>
		</div>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]number_of_schemas"); ?> *</div>
			<div class="input_text">
				<?php echo CHtml::activeTextField($model,"[$id]number_of_schemas", array('style'=>'width:40px;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]number_of_schemas",array('id'=>"InstallationrequestsProducts_".$id."_number_of_schemas_em_")); ?>
		</div>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]authentication"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]authentication",InstallationrequestsProducts::getAuthenticationList(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]authentication",array('id'=>"InstallationrequestsProducts_".$id."_authentication_em_")); ?>
		</div>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]reporting_type"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]reporting_type",InstallationrequestsProducts::getReportingTypeList(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]reporting_type",array('id'=>"InstallationrequestsProducts_".$id."_reporting_type_em_")); ?>
		</div>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]language_pack"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]language_pack",InstallationrequestsProducts::getLanguagePackList(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]language_pack",array('id'=>"InstallationrequestsProducts_".$id."_language_pack_em_")); ?>
		</div>		
		<div class="textBox  inline-block first" style="width:75px !important;">
			<div class="input_text_desc">  <?php echo CHtml::activelabelEx($model,"[$id]status"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]status",InstallationrequestsProducts::getStatusList2(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]status",array('id'=>"InstallationrequestsProducts_".$id."_status_em_")); ?>
		</div>
		<div class="textBox  inline-block first">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]license_type"); ?> *</div>
			<div class="input_text">
			<?php echo CHtml::activeDropDownList($model,"[$id]license_type",InstallationrequestsProducts::getLicenseList(), array('prompt'=>'','style'=>'border:none;margin-top:3px;margin-left:2px;width:100%;')); ?>
			</div>
			<?php echo CCustomHtml::error($model, "[$id]license_type",array('id'=>"InstallationrequestsProducts_".$id."_license_type_em_")); ?>
		</div>
		<div class="textBox  inline-block first" style="width:550px;">
			<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"[$id]notes"); ?></div>
			<div class="input_text" style="width:548px;">
			<div class="hdselect" style="width:548px;">
				<?php echo CHtml::activetextField($model,"[$id]notes",array('autocomplete'=>'off','style'=>' width:548px;resize: none;border:none;')); ?>
			</div>
		</div>
		</div>
		<div style="right:75px;top:80%;" class="save" onclick="updateProduct(this, '<?php echo $id;?>');return false;"><u><b>SAVE</b></u></div>
		<div style="color:#333;top:80%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');panelClip('.item_clip');
		panelClip('.term_clip');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset>
</div>
<script> 
$(function() {
	if(<?php echo "'".$id."'";?> != 'new' ){
		if(<?php echo (isset($model->id_product))?  $model->id_product :  63;?> != 64){
		$('#InstallationrequestsProducts_<?php echo $id ?>_db_collation').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_schemas').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_authentication').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_reporting_type').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_language_pack').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_nodes').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_version').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_dbtype').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_license_type').parents('.textBox').addClass('hidden');
	} }else{
		if(<?php echo (isset($model->id_product))?  $model->id_product:  63;?> != 64){
		$('#InstallationrequestsProducts_<?php echo $id ?>_db_collation').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_schemas').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_authentication').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_reporting_type').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_language_pack').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_nodes').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_version').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_dbtype').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_license_type').parents('.textBox').addClass('hidden');	}	}	}); 
function changeProduct(element){
	$this = $(element);	var val = $this.val();
	if(val == 64){
		$('#InstallationrequestsProducts_<?php echo $id ?>_db_collation').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_schemas').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_authentication').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_reporting_type').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_language_pack').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_nodes').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_version').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_dbtype').parents('.textBox').removeClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_license_type').parents('.textBox').removeClass('hidden');
	}else{
		$('#InstallationrequestsProducts_<?php echo $id ?>_db_collation').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_schemas').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_authentication').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_reporting_type').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_language_pack').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_number_of_nodes').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_version').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_dbtype').parents('.textBox').addClass('hidden');
		$('#InstallationrequestsProducts_<?php echo $id ?>_license_type').parents('.textBox').addClass('hidden');
	}
}
</script>