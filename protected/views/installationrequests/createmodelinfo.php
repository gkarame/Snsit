<div class="create">
<?php $form=$this->beginWidget('CActiveForm', array('id'=>'irs-info-form','enableAjaxValidation'=>false,)); ?>
<table><tr><td>
			<div class="row AppURlRow " >
				<div>
					<?php echo $form->labelEx($model,'app_url'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'app_url',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'app_url'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row AppSRVNARow " >
				<div>
					<?php echo $form->labelEx($model,'app_server_hostname'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'app_server_hostname',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'app_server_hostname'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row AppUSRNARow " >
				<div>
					<?php echo $form->labelEx($model,'app_username'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'app_username',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'app_username'); ?>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="row AppPASSRow margint10" >
				<div>
					<?php echo $form->labelEx($model,'app_password'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'app_password',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'app_password'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row DBSRVNARow margint10" >
				<div>
					<?php echo $form->labelEx($model,'db_server_hostname'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'db_server_hostname',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'db_server_hostname'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row DBNARow margint10" >
				<div>
					<?php echo $form->labelEx($model,'db_name'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'db_name',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'db_name'); ?>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="row DBUSRNARow margint10" >
				<div>
					<?php echo $form->labelEx($model,'db_username'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'db_username',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'db_username'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row DBPASSRow margint10" >
				<div>
					<?php echo $form->labelEx($model,'db_password'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'db_password',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'db_password'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row DBLclBkpRow margint10" >
				<div>
					<?php echo $form->labelEx($model,'db_local_bkup'); ?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'db_local_bkup',array('autocomplete'=>'off')); ?>
					</div>
				<?php echo $form->error($model,'db_local_bkup'); ?>
				</div>
			</div>
		</td>
	</tr>
	<tr>
	<td>
		<div class="row InforBkupRow margint10">
				<div>
					<?php echo $form->label($model, 'infor_local_bkup');?>
					<div class="inputBg_create">
						<?php echo $form->textField($model, 'infor_local_bkup',array('autocomplete'=>'off')); ?>
					</div>
					<?php echo $form->error($model,'infor_local_bkup'); ?>
				</div>
			</div>
		</td>
		<td>
			<div class="row LicenseTypeRow margint10">
				<div>
					<?php echo $form->label($model, 'license_type'.' *'); ?>
					<div class="selectBg_create">
						<?php echo $form->dropDownList($model, 'license_type', InstallationRequestsInfo::getLicenseTypeList(), array('prompt'=>'')); ?>
					</div>
					<?php echo $form->error($model,'license_type'); ?>
				</div>
			</div>
		</td>	
	</tr>
</table>
<div class="horizontalLine"></div>
	<div class="save">
		<?php echo CHtml::submitButton(Yii::t('translations','Save')); ?>
	</div>
	<br clear="all" />
<?php $this->endWidget(); ?>
</div>
<br clear="all" />