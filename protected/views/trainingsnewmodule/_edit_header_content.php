<div class=" training_bg"><?php $can_modify=true; ?></div>
<div class="create" style="background:white;">
<fieldset id="header_fieldset">
	<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"course_name"); ?></div>
		<div class="input_text">
			<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "course_name", Codelkups::getCodelkupsDropDown('training_course'), array('class'=>'input_text_value','value'=>$model->course_name)); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "course_name", array('id'=>"TrainingsNewModule_course_name_em_")); ?>	</div>
	<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"status"); ?></div>
		<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "status", TrainingsNewModule::getStatusList($model->status), array('class'=>'input_text_value','value'=>$model->status)); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "status", array('id'=>"TrainingsNewModule_status_em_")); ?>	</div>
	<div class="textBox inline-block bigger_amt2">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"start_date"); ?></div>
		<div class="input_text"><?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model,'attribute'=>'start_date','value' => $model->start_date,
			    	'cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'),));			?>
		</div>	<?php echo CCustomHtml::error($model, "start_date", array('id'=>"TrainingsNewModule_start_date_em_")); ?>	</div>
	<div class="textBox inline-block bigger_amt2">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"end_date"); ?></div>
		<div class="input_text"><?php $this->widget('zii.widgets.jui.CJuiDatePicker',array('model'=>$model, 
			        'attribute'=>'end_date','value' => $model->end_date,'cssFile' => false,'options'=>array('dateFormat'=>'dd-mm-yy'),'htmlOptions'=>array('class'=>'width111'),)); ?>
		</div>	<?php echo CCustomHtml::error($model, "end_date", array('id'=>"TrainingsNewModule_end_date_em_")); ?>	</div>
<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"instructor"); ?></div>
		<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "instructor", Users::getAllSelect(), array('class'=>'input_text_value','value'=>$model->instructor)); ?>
			</div>	</div><?php echo CCustomHtml::error($model, "instructor", array('id'=>"TrainingsNewModule_instructor_em_")); ?></div>
	<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"country"); ?></div>
		<div class="input_text"><div class="hdselect">	<?php echo CHtml::activeDropDownList($model, "country", Codelkups::getCodelkupsDropDown('country'), array('class'=>'input_text_value','value'=>$model->country)); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "country", array('id'=>"TrainingsNewModule_country_em_")); ?>	</div>
	<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"city"); ?></div>
		<div class="input_text">	<?php echo CHtml::activeTextField($model,"city",array('class'=>'input_text_value','value'=>$model->city)); ?>
		</div>	<?php echo CCustomHtml::error($model, "city", array('id'=>"TrainingsNewModule_city_em_")); ?>	</div>
	<div class="textBox inline-block " style="width:270px;"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"location"); ?></div>
		<div class="input_text"><?php echo CHtml::activeTextField($model, "location",array('value'=>$model->location)); ?>	</div>	</div>	
	<?php if($model->type == TrainingsNewModule::TYPE_PRIVATE){?>
	<div class="textBox inline-block bigger_amt">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"customer"); ?></div>
		<div class="input_text">	<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "customer", Customers::getAllCustomersSelect(), array('class'=>'input_text_value','value'=>$model->customer)); ?>
			</div>	</div>	<?php echo CCustomHtml::error($model, "customer", array('id'=>"TrainingsNewModule_customer_em_")); ?>	</div>	<?php }?>
			<?php if($model->type == TrainingsNewModule::TYPE_PARTNER){?>	<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"partner"); ?></div>
		<div class="input_text">	<div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "partner", Customers::getAllCustomersSelect(), array('class'=>'input_text_value','value'=>$model->partner)); ?>
			</div>		</div>		<?php echo CCustomHtml::error($model, "partner", array('id'=>"TrainingsNewModule_partner_em_")); ?>
	</div>	<?php }?>	<?php if($model->type == TrainingsNewModule::TYPE_PUBLIC){?>
	<div class="textBox inline-block " style="width:160px;">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activeLabel($model,"min_participants",array('required' => true)); ?></div>
		<div class="input_text">		<div class="hdselect">
				<?php echo CHtml::activeTextField($model, "min_participants",array('style' => 'width:130px;','value'=>$model->min_participants)); ?>
			</div>	</div>	</div>	<div class="textBox inline-block ">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activeLabel($model,"cost_per_participant",array('required' => true)); ?></div>
		<div class="input_text"><div class="hdselect">		<?php echo CHtml::activeTextField($model, "cost_per_participant",array('style' => 'width:130px;','value'=>$model->cost_per_participant)); ?>
			</div>	</div></div><?php }?>

		<?php if( TrainingsNewModule::getCertifiedUsers(Yii::app()->user->id) > 0 && $model->type == 640) {     ?>


			<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"man_days").'*'; ?></div>
		<div class="input_text">	<?php echo CHtml::activeTextField($model,"man_days",array('class'=>'input_text_value','value'=>$model->man_days)); ?>
		</div>	<?php echo CCustomHtml::error($model, "man_days", array('id'=>"TrainingsNewModule_man_days_em_")); ?>	</div>


		<div class="textBox inline-block bigger_amt"><div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model,"md_rate").'*'; ?></div>
		<div class="input_text">	<?php echo CHtml::activeTextField($model,"md_rate",array('class'=>'input_text_value','value'=>$model->md_rate)); ?>
		</div>	<?php echo CCustomHtml::error($model, "md_rate", array('id'=>"TrainingsNewModule_md_rate_em_")); ?>	</div>

		<?php }?>
				<div class="textBox inline-block bigger_amt">
		<div class="input_text_desc padding_smaller"><?php echo CHtml::activeLabel($model,"survey_score",array('required' => true)); ?></div>
		<div class="input_text">	<?php echo CHtml::activeTextField($model, "survey_score",array('class'=>'input_text_value','value'=>$model->survey_score)); ?>	
		</div>	</div> 
<div class="textBox inline-block bigger_amt"></div>
<div class="textBox inline-block bigger_amt"></div>
<div class="textBox inline-block bigger_amt"></div>	<div style="right:90px;margin-top:8%;" class="save" onclick="updateHeader(this);return false;"><u><b>SAVE</b></u></div>
	<div style="right:35px;color:#333;margin-top:8%;" class="save" onclick="$(this).parents('.tache.new').siblings('.tache').removeClass('hidden');$(this).parents('.tache.new').addClass('hidden');$(this).parents('.tache.new').html('');"><u><b>CANCEL</b></u></div>
</fieldset></div><div class="horizontalLine smaller_margin"></div>