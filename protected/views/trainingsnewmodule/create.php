<div class="create"><?php $form=$this->beginWidget('CActiveForm', array('id'=>'trainings-header-form','enableAjaxValidation'=>false,)); ?>
<table><tr><td>	<div class="row courseRow marginA">		<div>
		<?php echo $form->labelEx($model,'course_name'); ?>		<div class="selectBg_create">
			<?php echo $form->dropDownList($model,'course_name', Codelkups::getCodelkupsDropDown('training_course'), array('prompt' => Yii::t('translations', 'Choose course'))); ?>
		</div>	<?php echo $form->error($model,'course_name'); ?>	</div>	</div></td><td>
	<div class="row starDateRow marginA ">	<div>	<?php echo $form->labelEx($model,'start_date'); ?>	<div class="inputBg_create ">
	<?php    $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 'attribute'=>'start_date','cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'),    ));	?>
			</div>	<?php echo $form->error($model,'start_date'); ?>	</div>	</div></td><td>	<div class="row EndDateRow marginA">
		<div>	<?php echo $form->labelEx($model,'end_date'); ?>			<div class="inputBg_create ">			
		<?php 	    $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'end_date','cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),
			    	'htmlOptions'=>array('class'=>'width111'),    ));		?>
			</div>	<?php echo $form->error($model,'end_date'); ?>	</div>	</div>	</td></tr>
<tr><td><div class="row  marginA" ><div>	<?php echo $form->labelEx($model,'location'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'location',array('autocomplete'=>'off')); ?>
		</div>	<?php echo $form->error($model,'location'); ?></div></div></td><td><div class="row  marginA" ><div>
		<?php echo $form->labelEx($model,'city'); ?>	<div class="inputBg_create">	<?php echo $form->textField($model, 'city'); ?>	</div>
		<?php echo $form->error($model,'city'); ?></div></div></td><td><div class="row  marginA" ><div>		<?php echo $form->labelEx($model,'country'); ?>
		<div class="selectBg_create">	<?php echo $form->dropDownList($model,'country', Codelkups::getCodelkupsDropDown('country'), array('prompt' => Yii::t('translations', 'Choose country'))); ?>
		</div>	<?php echo $form->error($model,'country'); ?></div></div></td></tr><tr><td>	<div class="row rowInstructor marginA">
		<div>	<?php echo $form->labelEx($model, 'instructor'); ?>		<div class="selectBg_create">
			<?php echo $form->dropDownList($model, 'instructor',Users::getAllSelect(), array('prompt' => Yii::t('translations', 'Choose instructor'))); ?>
		</div>		<?php echo $form->error($model,'instructor'); ?>		</div>	</div></td><td>	<div class="row marginA">		<div>		<?php echo $form->labelEx($model,'type'); ?>
		<div class="selectBg_create">	<?php echo $form->dropDownList($model,'type', Codelkups::getCodelkupsDropDown('training_type'), array('prompt' => Yii::t('translations', 'Choose type'), 'onchange' => 'changeType(this);')); ?>
		</div>	<?php echo $form->error($model,'type'); ?>		</div>	</div></td>
		<td>	<div class="row customerRow hidden marginA">	<div>
		<?php echo $form->labelEx($model,'customer'.' *'); ?>	<div class="inputBg_create">
		<?php 
		$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name',	
				'source'=>Customers::getAllAutocomplete(),'options'=>array(
					'minLength'=>'0',
					'showAnim'=>'fold',
					'select'=>"js:function(event, ui) {
                    				$('#TrainingsNewModule_customer').val(ui.item.id);
                    			}",
					'change'=>"js:function(event, ui) {
                   					if (!ui.item) {
                                    	$('#TrainingsNewModule_customer').val('');
									}	}",	),	'htmlOptions'=>array(	'onfocus' => "javascript:$(this).autocomplete('search','');",
					'onblur' => 'blurAutocomplete(event, this, "#TrainingsNewModule_customer");'),	));		?>	</div></div>	<?php echo $form->hiddenField($model, 'customer'); ?>
	<?php echo $form->error($model,'customer'); ?>	</div>	</td></tr><tr>



		<td><div class="row hidden marginA" ><div>	<?php echo $form->labelEx($model,'man_days'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'man_days',array('autocomplete'=>'off')); ?>
		</div>	<?php echo $form->error($model,'man_days'); ?></div></div></td>

		<td><div class="row hidden marginA" ><div>	<?php echo $form->labelEx($model,'md_rate'); ?>
		<div class="inputBg_create"><?php echo $form->textField($model, 'md_rate',array('autocomplete'=>'off')); ?>
		</div>	<?php echo $form->error($model,'md_rate'); ?></div></div></td>

		


</tr><tr><td>	

	<div class="row partnerRow hidden marginA">	<div>		<?php echo $form->labelEx($model,'partner'.' *'); ?>
		<div class="inputBg_create">	<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(	'model' => $model,	'attribute' => 'partner_name','source'=>Customers::getAllAutocomplete(),
				'options'=>array(
					'minLength'=>'0',
					'showAnim'=>'fold',
					'select'=>"js:function(event, ui) {  $('#TrainingsNewModule_partner').val(ui.item.id);  }",
					'change'=>"js:function(event, ui) {
                   					if (!ui.item) {
                                    	$('#TrainingsNewModule_partner').val('');
									} }",		),
				'htmlOptions'=>array(	'onfocus' => "javascript:$(this).autocomplete('search','');",		'onblur' => 'blurAutocomplete(event, this, "#TrainingsNewModule_partner");'				),		));		?>
		</div>	<?php echo $form->hiddenField($model, 'partner'); ?>	<?php echo $form->error($model,'partner'); ?> 		</div>	</div>	</td></tr>


	<tr><td>	<div class="row minparticipantRow hidden marginA">
	<div>	<?php echo CHtml::activeLabel($model,'min_participants',array('required' => true)); ?>
		<div class="inputBg_create">		<?php echo $form->textField($model, 'min_participants',array('autocomplete'=>'off')); ?>	</div></div>
	<?php echo $form->error($model,'min_participants'); ?> </div></td><td>    <div class="row costparticipantRow hidden marginA">    <div>		<?php echo CHtml::activeLabel($model,'cost_per_participant',array('required' => true));?> 
		<div class="inputBg_create">		<?php echo $form->textField($model, 'cost_per_participant',array('autocomplete'=>'off')); ?>	</div>	<?php echo $form->error($model,'cost_per_participant'); ?>	</div>	</div></td>  </tr></table> 	
	<div class="horizontalLine"></div>	<div class="row buttons">		<?php echo CHtml::submitButton('Submit', array('class'=>'next_submit')); ?>	</div>	<br clear="all" /><?php $this->endWidget(); ?></div><br clear="all" />
<script type="text/javascript">
	$(function() {	changeType('#TrainingsNewModule_type');	});
	function changeType(element) {
		$this =  $(element);
		switch ($this.val()) {
			case '639': 
				$('#training').parents('.row').removeClass('hidden');				
				$('#TrainingsNewModule_min_participants').parents('.row').removeClass('hidden');
				$('#TrainingsNewModule_cost_per_participant').parents('.row').removeClass('hidden');
				$('#TrainingsNewModule_partner').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_customer').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_md_rate').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_man_days').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_min_participants').val('');
				$('#TrainingsNewModule_confirmed_participants').val('');
				$('#TrainingsNewModule_cost_per_participant').val('');
				$('#TrainingsNewModule_revenues').val('');
				break;
			case '640': 
				$('#TrainingsNewModule_min_participants').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_cost_per_participant').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_partner').parents('.row').addClass('hidden');	
				$('#TrainingsNewModule_customer').parents('.row').removeClass('hidden');
				$('#TrainingsNewModule_customer').val('');
				$('#TrainingsNewModule_man_days').parents('.row').removeClass('hidden');
				$('#TrainingsNewModule_man_days').val('');
				$('#TrainingsNewModule_md_rate').parents('.row').removeClass('hidden');
				$('#TrainingsNewModule_md_rate').val('');
				break;
			case '641':
				$('#TrainingsNewModule_min_participants').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_cost_per_participant').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_partner').parents('.row').removeClass('hidden');	
				$('#TrainingsNewModule_customer').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_md_rate').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_man_days').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_partner').val('');	
				break;
			default:
				$('#TrainingsNewModule_min_participants').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_cost_per_participant').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_md_rate').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_man_days').parents('.row').addClass('hidden');
				$('#TrainingsNewModule_partner').parents('.row').addClass('hidden');	
				$('#TrainingsNewModule_customer').parents('.row').addClass('hidden');
				break;				
		}
	}
</script>