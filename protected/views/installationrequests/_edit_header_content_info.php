<div class="training_bg">
<fieldset id="header_info_fieldset">
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"app_url"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'app_url',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "app_url", array('id'=>"InstallationRequestsInfo_app_url_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"app_server_hostname"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'app_server_hostname',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "app_server_hostname", array('id'=>"InstallationRequestsInfo_app_server_hostname_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"app_username"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'app_username',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "app_username", array('id'=>"InstallationRequestsInfo_app_username_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"app_password"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'app_password',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "app_password", array('id'=>"InstallationRequestsInfo_app_password_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"db_server_hostname"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'db_server_hostname',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "db_server_hostname", array('id'=>"InstallationRequestsInfo_db_server_hostname_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"db_name"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'db_name',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "db_name", array('id'=>"InstallationRequestsInfo_db_name_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"db_username"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'db_username',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "db_username", array('id'=>"InstallationRequestsInfo_db_username_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"db_password"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'db_password',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "db_password", array('id'=>"InstallationRequestsInfo_db_password_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"db_local_bkup"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'db_local_bkup',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "db_local_bkup", array('id'=>"InstallationRequestsInfo_db_local_bkup_em_")); ?>
</div>
<div class="textBox three-smaller inline-block itms">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"infor_local_bkup"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activetextArea($model, 'infor_local_bkup',array('autocomplete'=>'off','cols'=>'28','rows'=>'4','style'=>' resize: none;border:none;')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "infor_local_bkup", array('id'=>"InstallationRequestsInfo_infor_local_bkup_bkup_em_")); ?>
</div>
<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"license_type"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "license_type", InstallationRequestsInfo::getLicenseTypeList(), array('class'=>'input_text_value', 'value'=>$model->license_type)); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "license_type", array('id'=>"InstallationRequestsInfo_license_type_em_")); ?>
</div>
<div style="right:75px;top:95%;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
<div style="color:#333;top:95%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
</div>