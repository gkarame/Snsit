<div class="tache new">
	<div class="bg"></div>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'expenses-form',
		'enableAjaxValidation'=>false,	)); ?>
	    <?php echo $form->hiddenField($model,'currency_rate_id'); ?> 		
		<div class="item inline-block one normal left11">
			<?php echo $form->labelEx($model,'type'); ?>
			<div class="selectRow">
			<?php echo $form->dropDownList($model, 'type', ExpensesDetails::getTypes(),array('prompt'=>'')); ?> 
			</div>
			<?php echo $form->error($model,'type'); ?>
		</div>
		<div class="item inline-block two normal">
			<?php echo $form->labelEx($model,'original_amount'); ?>
			<div class="dataRow"><?php echo $form->textField($model,'original_amount', array('onkeyup' => 'getUSDAmount("'.Yii::app()->createAbsoluteUrl('expenses/GetUSDRate').'")', 'value' => (($model->original_amount)?$model->original_amount:''))); ?></div>
			<?php echo $form->error($model,'original_amount'); ?>
		</div>
		<div class="item inline-block two normal amt">
			<?php echo $form->labelEx($model,'original_currency'); ?>
			<div class="selectRow">
			<?php echo $form->dropDownList($model, 'original_currency', ExpensesDetails::getCurrencies(), array('onchange' => 'getUSDAmount("'.Yii::app()->createAbsoluteUrl('expenses/GetUSDRate').'")')); ?> 
			</div>
			<?php echo $form->error($model,'original_currency'); ?>
		</div>
		<div class="item inline-block two normal">
			<?php echo $form->labelEx($model,'amount'); ?>
			<div class="dataRow"><?php echo $form->textField($model,'amount', array('readonly' => true)); ?></div>
			<?php echo $form->error($model,'amount'); ?>
		</div>
		<?php $project_id = $model->expenses->project_id; $customer_id = $model->expenses->customer_id;	
		$expens = Yii::app()->db->createCommand("SELECT expense FROM eas where id_project = '$project_id' AND id_customer = '$customer_id' ")->queryScalar();
		$pay= Yii::app()->db->createCommand("SELECT payable FROM expenses where id=".$model->expenses_id." ")->queryScalar();
		if($expens == "N/A" || $expens == "Actuals" || $expens == null || $project_id == '535'){?>
			<div class="item inline-block two normal amt" style="clear:right;">
				<?php echo $form->labelEx($model,'billable'); ?>
				<div class="selectRow">
				<?php echo $form->dropDownList($model, 'billable', array('Yes'=>'Yes','No'=>'No')); ?> 
				</div>
				<?php echo $form->error($model,'billable'); ?>
			</div>
		<?php }else {?>
<div class="item inline-block two normal amt" style="clear:right;">
				<?php echo $form->labelEx($model,'billable'); ?>
				<div class="selectRow">
				<?php echo $form->dropDownList($model, 'billable', array('No'=>'No','Yes'=>'Yes')); ?> 
				</div>
				<?php echo $form->error($model,'billable'); ?>
			</div>
		<?php }?>
		<div class="item inline-block two normal amt left11 payable_field">
			<?php echo $form->labelEx($model,'payable'); ?>
			<div class="selectRow">
			<?php  if (!empty($pay) && $pay=='No' ){ echo $form->dropDownList($model, 'payable', array('No'=>'No', 'Yes'=>'Yes',));	}
			ELSE{ echo $form->dropDownList($model, 'payable', array('Yes'=>'Yes','No'=>'No'));}?> 
			</div>
			<?php echo $form->error($model,'payable'); ?>
		</div>
		<div class="item inline-block time normal" style="cursor:pointer; margin-left:0px;">
			<?php echo $form->labelEx($model,'date'); ?>
			<div class="dataRow">
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array( 'model'=> $model,'attribute' => "date",'cssFile' => false,
			        'options'=>array('dateFormat'=>'dd/mm/yy','showAnim' => 'fadeIn'),
			    	'htmlOptions' => array('class' => 'datefield','autocomplete' => 'off'),	)); ?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo $form->error($model,'date'); ?>
		</div>
		<div class="item inline-block notes normal">
			<?php echo $form->labelEx($model,'notes'); ?>
			<div class="dataRow"><?php echo $form->textField($model,'notes'); ?></div>
			<?php echo $form->error($model,'notes'); ?>
		</div>
		<?php if ($model->isNewRecord) { ?>
			<div style="right:78px;" class="save" onclick="createItem(this, <?php echo $model->expenses_id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('expenses/createItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } else { ?>
			<div style="right:78px;" class="save" onclick="updateItem(this, <?php echo $model->id; ?>, '<?php echo Yii::app()->createAbsoluteUrl('expenses/manageItem');?>');return false;"><u><b>SAVE</b></u></div>
		<?php } ?>
		<div style="color:#333;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('items-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	<?php $this->endWidget(); ?>
</div>
<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>