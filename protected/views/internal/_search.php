<?php
/* @var $this MaintenanceController */
/* @var $model Maintenance */
/* @var $form CActiveForm */
?>

<div class="wide search" id="search-internal" style="overflow:inherit;">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
		'id'=>'search_internal'
	)); ?>
	
		<div class="row">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'name'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'name',array('style'=>'width:111px;')); ?>
			</div>
		</div>	
		<div class="row width203">
			<div class="inputBg_txt">
				<label><?php echo Yii::t('translations', 'PM');?></label>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'project_manager',		
						'source'=>Internal::getAllUsers(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
		</div>
		<div class="row width_common">	<div class="inputBg_txt">	<?php echo $form->label($model,'status'); ?>	<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'status','source'=>Internal::getStatusLabelDrop(),'options'=>array('minLength'	=>'0','showAnim'	=>'fold'),
						'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width94",'value' =>'Active'),
				));	?>	</div>	</div>	

				<div class="row width203">
			<div class="inputBg_txt">
				<label><?php echo Yii::t('translations', 'Resource');?></label>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'notes',		
						'source'=>Internal::getAllUsers(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
		</div>
		
	<div class="btn">
		<div class="action-maintenance">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>			
		</div>
			<div class="wrapper_action" id="action_tabs_right">
					<div onclick="chooseActions()" class="action triggerAction"><u><b>ACTION</b></u>
						<div class="action_list actionPanel " style="margin-top:-100px; margin-right:-30px;" >
			    	<div class="headli"></div>
					<div class="contentli" >
						<?php 
						if(GroupPermissions::checkPermissions('general-internal','write'))
							{
						?>
						<div class="cover">
							<div class="li noborder" ><a class="special_edit_header" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('internal/create');?>"><?php echo Yii::t('translations', 'New Internal Project');?></a></div>
						</div>
						<?php } ?>
					</div>
					<div class="ftrli"></div>
			    </div>
			    <div id="users-list" style="display:none;"></div>
			 </div></div>
		</div>		
		<div class="horizontalLine search-margin"></div>			
<?php $this->endWidget(); ?>		
</div><!-- search-form -->