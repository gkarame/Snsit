<div class="bg" style=" height: 229px;    background-size: 100% 101%;"> </div>
<fieldset id="header_fieldset" >	
	
	<div class="textBox bigger_amt inline-block marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"description"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "description", array('id'=>"")); ?>
	</div>
	<div class="textBox item inline-block time normal marginl20" style="cursor:pointer; margin-left:10px;">
				<div class="input_text_desc ">	<?php echo CHtml::activelabelEx($model,"dep_date"); ?></div>
					<div class="dataRow dep_cal">
						<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=> $model,'attribute' => "dep_date", 
							'cssFile' => false,'options'=>array('showAnim' => 'fadeIn'),
							'htmlOptions' => array('class' => 'datefield'),)); ?>
						<span class="calendar calfrom"></span>
					</div>
					<?php echo CCustomHtml::error($model, "dep_date", array('id'=>"")); ?>
				</div>
	<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"infor_version"); ?></div>
		<div class="input_text width195">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model,"infor_version",  Maintenance::getVersionListPerCustomer($model->id_customer), array('class'=>'input_text_value','onchange' => 'changeCategory(this);')); ?>
			</div></div>
		<?php echo CCustomHtml::error($model, "infor_version", array('id'=>"")); ?>
	</div>	
	<div class="textBox bigger_amt inline-block marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"module"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "module", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "module", array('id'=>"")); ?>
	</div>
	<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text width190">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "status", Deployments::getStatusList(), array('class'=>'input_text_value')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "status", array('id'=>"")); ?>
	</div>
	<div class="textBox inline-block bigger_amt marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"source"); ?></div>
		<div class="input_text width195">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "source", Deployments::getProjectsDD($model->id_customer), array('class'=>'input_text_value','prompt'=>" ",'style'=>'', 'onchange' => 'showSrs(this);')); ?>
			</div>
		</div>
		<?php echo CCustomHtml::error($model, "source", array('id'=>"")); ?>
	</div>
	<div class="textBox bigger_amt inline-block marginl20 " id="srs_dep">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"assigned_srs"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "assigned_srs", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "assigned_srs", array('id'=>"")); ?>
	</div>

	<div class="textBox bigger_amt inline-block marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"location"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "location", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "location", array('id'=>"")); ?>
	</div>

	<div class="textBox bigger_amt inline-block marginl20">
		<div class="input_text_desc"><?php echo CHtml::activelabelEx($model,"notes"); ?></div>
		<div class="input_text width195">
			<?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value')); ?>
		</div>
		<?php echo CCustomHtml::error($model, "notes", array('id'=>"")); ?>
	</div>

	 <div style="right:105px;top:190px" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right:45px;color:#333;top:190px" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset>
<script type="text/javascript">
$(function() {
		showSrs('#Deployments_source');
}); 
	function showSrs(element) {
		$this =  $(element);
		if($this.val() == '663') {
			$('#srs_dep').removeClass('hidden');
		}else{
			$('#srs_dep').addClass('hidden');
		}
	}
	</script>