<div class="bg"></div>
<fieldset id="header_fieldset" >	
	  

<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"project manager *"); ?></div>
		<div class="input_text width195">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"project_manager",  Projects::getAllActiveUsers() , array('class'=>'input_text_value')); ?>
			</div></div>
		<?php echo CCustomHtml::error($model, "project_manager", array('id'=>"")); ?>
	</div>	

<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"business manager *"); ?></div>
		<div class="input_text width195">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"business_manager",  Projects::getAllActiveUsers() , array('class'=>'input_text_value')); ?>
			</div></div>
		<?php echo CCustomHtml::error($model, "business_manager", array('id'=>"")); ?>
	</div>	

	<div class="textBox item inline-block time normal marginl20" style="cursor:pointer; margin-left:10px;">
				<div class="input_text_desc ">	<?php echo CHtml::activelabelEx($model,"eta"); ?></div>
					<div class="dataRow dep_cal">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "eta", 
							'cssFile' => false,'options'=>array('showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php echo CCustomHtml::error($model, "eta", array('id'=>"")); ?>
				</div>


	<div class="textBox bigger_amt inline-block marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"estimated_effort"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "estimated_effort", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "estimated_effort", array('id'=>"")); ?>
	</div>
	

	<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text width195">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status", internal::getStatusList(), array('class'=>'input_text_value')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"")); ?>
	</div>

	 <div style="right:105px;top:120px" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right:45px;color:#333;top:120px" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<script type="text/javascript">

	</script>