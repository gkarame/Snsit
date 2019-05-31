<div class="wide search" id="search-travel"><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),	'method'=>'get',	)); ?>	
		<div class="row">		<div class="inputBg_txt">		<?php echo $form->label($model,'Customer'); ?>		<span class="spliter"></span>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'customer_name','source'=>Travel::getCustomersAutocomplete(),
						'options'=>array('minLength'	=>'0','showAnim'	=>'fold',
							'select'	=>"js: function(event, ui){ 
							$('#Travel_id_customer').val(ui.item.id);										
							refreshProjectListsTravel(ui.item.id); 	}"	),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'onblur' => 'blurAutocomplete(event, this, "#Travel_id_customer", refreshProjectListsTravel);',
						),		));		?>		</div>	<?php echo $form->hiddenField($model, 'id_customer'); ?> 	</div>	

		<div class="row">	<div class="selectBg_search">	<?php echo $form->labelEx($model,'Proj/Train'); ?>	<span class="spliter"></span>	<div class="select_container">
					<?php echo $form->dropDownList($model, 'id_project', Projects::getAllProjectsTrainingsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
				</div>		</div>	<?php echo $form->error($model,'id_project'); ?>	</div>	

		<div class="row author "><div class="inputBg_txt">	<?php echo $form->label($model,'id_user'); ?>	<span class="spliter"></span>
		<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_user','source'=>Travel::getUsersAutocomplete(),'options'=>array('minLength'=>'0',		'showAnim'=>'fold',		),
						'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width141",),	));		?>		</div>		</div>		

		<div class="row margint10 ">		<div class="selectBg_search">	<?php echo $form->label($model, 'status'); ?>
				<span class="spliter"></span>		<div class="select_container">	<?php echo $form->dropDownList($model, 'status', Travel::getAllStatus(), array('prompt'=>'')); ?>
				</div>	</div>		</div>


		<div class="row margint10 ">		<div class="selectBg_search">	<?php echo $form->label($model, 'type'); ?>
				<span class="spliter"></span>		<div class="select_container">	<?php echo $form->dropDownList($model, 'expense_type', Travel::getExpenseTypesSelect(), array('prompt'=>'')); ?>
				</div>	</div>		</div>

		<div class="row dateRow margint10 ">
			<div class="dateSearch inputBg_txt">
				<?php echo $form->label($model,'date'); ?>
				<span class="spliter"></span>
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 
			        'attribute'=>'date', 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy'
			    	),
			    	'htmlOptions'=>array('class'=>'', 'autocomplete'=>'off'),
			    	
			    ));
				?>
				<span class="calendar calfrom" style="    top: 7px !important;"></span>
			</div>
		</div>

		<div class="btn">	<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>		
			<?php if(GroupPermissions::checkPermissions('travel-list','write'))	{		?>	<?php echo CHtml::link(Yii::t('translation', 'New Travel'), array('create'), array('class'=>'add-travel add-btn')); ?>	<?php 	}	?>
		</div>	<div class="horizontalLine search-margin"></div>	<?php $this->endWidget(); ?></div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetProjectsTrainingsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
	refreshProjectListsTravel();
</script>