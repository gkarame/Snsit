<div class="wide search" id="search-trainings" style="overflow:inherit;">	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',	)); ?><div>	
		<div class="row training_number width211" ><div class="selectBg_search" ><?php echo $form->label($model,'training_number', array('class'=>"width70")); ?>
				<span class="spliter"></span><div class="select_container width111">
					<?php echo $form->dropDownList($model, 'training_number', TrainingsNewModule::getTrainingsnumbers(), array('prompt' => Yii::t('translations', 'Choose Training'), 'disabled' => false)); ?>
				</div>	</div>	</div>	

 

<div class="row customer width211">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'Customer', array('class'=>"width70")); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,	'attribute' => 'customer_name',		
						'source'=>TrainingsNewModule::getCustomersAutocomplete(),
						'options'=>array('minLength'	=>'0',	'showAnim'	=>'fold',
							'select'	=>"js: function(event, ui){ 
							$('#TrainingsNewModule_customer').val(ui.item.id); }"
						),
						'htmlOptions'	=>array(
							'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
							'onblur' => 'blurAutocomplete(event, this, "#TrainingsNewModule_customer");',
							'class'	  => "width111",
						),
				));	?>
			</div>
			<?php echo $form->hiddenField($model, 'customer'); ?>
		</div>



				<div class="row course_name width211">	<div class="selectBg_search">
				<?php echo $form->label($model,'course_name', array('class'=>"width70")); ?><span class="spliter"></span>	<div class="select_container width111">
					<?php echo $form->dropDownList($model, 'course_name', Codelkups::getCodelkupsDropDown('training_course'), array('prompt' => Yii::t('translations', 'Choose Course'), 'disabled' => false)); ?>
				</div>	</div>	<?php echo $form->error($model,'course_name'); ?>	</div>


		<div class="row country  width211">	<div class="selectBg_search">	<?php echo $form->label($model,'country', array('class'=>"width70")); ?><span class="spliter"></span>
				<div class="select_container width111">		<?php echo $form->dropDownList($model, 'country', Codelkups::getCodelkupsDropDown('country'), array('prompt' => Yii::t('translations', 'Choose Country'), 'disabled' => false)); ?>
				</div>		</div>			<?php echo $form->error($model,'country'); ?>
		</div>	<div class="row year margint10 width211" >	<div class="inputBg_txt">
			<?php echo $form->label($model,'year', array('class'=>"width70")); ?>	<span class="spliter"></span>	<div class="select_container width111" ><?php echo $form->dropDownList($model,'year',TrainingsNewModule::getYears(),array('prompt'=>'')); ?>	</div></div></div>
		<div class="row status margint10 width211">	<div class="selectBg_search">	<?php echo $form->label($model, 'status', array('class'=>"width70")); ?>	<span class="spliter"></span>
				<div class="select_container width111"><?php echo $form->dropDownList($model, 'status', TrainingsNewModule::getStatusList("9"), array('prompt'=>'')); ?></div>	</div>	</div>
		<div class="row type margint10 width211"><div class="selectBg_search">	<?php echo $form->label($model, 'type', array('class'=>"width70")); ?>	<span class="spliter"></span>
				<div class="select_container width111"><?php echo $form->dropDownList($model, 'type', TrainingsNewModule::getTypesList(), array('prompt'=>'')); ?></div></div>
		</div>	<div class="row city margint10 width211">	<div class="inputBg_txt">	<?php echo $form->label($model,'city', array('class'=>"width70")); ?>	<span class="spliter"></span>
				<?php echo $form->textField($model,'city',array('class' => 'width111')); ?>	</div>	</div>	
		<div class="row instructor margint10 width211">	<div class="inputBg_txt">	<?php echo $form->label($model,'instructor', array('class'=>"width75")); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'instructor','source'=>TrainingsNewModule::getUsersAutocomplete(),
						'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width107",),	));		?>		</div>		</div>	</div>	
		<div class="btn">	<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	
		<div class="wrapper_action" id="action_tabs_right">	<div onclick="chooseActions();" class="action triggerAction" ><u><b>ACTION</b></u></div>	<div class="action_list actionPanel">   	<div class="headli"></div>
					<div class="contentli" ><?php 	if(GroupPermissions::checkPermissions('general-trainings','write')){	?>
					<?php if( TrainingsNewModule::getCertifiedUsers(Yii::app()->user->id) > 0 ) {     ?>
						<div class="cover">	<div class="li noborder"><a href="create">NEW TRAINING</a></div>	</div> 	<?php } ?>
						<div class="cover">	<div class="li noborder" onclick="freeInvite();">FREE INVITE</div>	</div>	<?php } ?>
					<div class="cover">	<div class="li noborder" onclick="getExcel();">EXPORT TO EXCEL</div>
					</div>	</div>	<div class="ftrli"></div>    </div>    <div id="users-list" style="display:none;"></div> </div>		</div>
		<div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>