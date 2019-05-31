<?php
/* @var $this MaintenanceController */
/* @var $model Maintenance */
/* @var $form CActiveForm */
?>

<div class="wide search" id="search-deployments" style="overflow:inherit;">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
		'id'=>'search_deployments'
	)); ?>
	
		<div class="row">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'dep_no'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'dep_no',array('size'=>5,'maxlength'=>5,'style'=>'width:111px;')); ?>
			</div>
		</div>	
		<div class="row width203">
			<div class="inputBg_txt">
				<label><?php echo Yii::t('translations', 'Customer');?></label>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'id_customer',		
						'source'=>Deployments::getCustomersAutocomplete(),
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
		<div class="row  width203">
			<div class="inputBg_txt">
				<?php echo $form->label($model, 'source',array('class'=>'width71')); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'source',		
						'source'=>Deployments::getProjectsAutocomplete(),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
							'class'=>'width103'
						),
				));
				?>
			</div>
		</div>
		<div class="row ">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'sr(s)'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'assigned_srs',array('size'=>15,'maxlength'=>15,'style'=>'width:111px;')); ?>
			</div>
		</div>
		<div class="row margint10">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'module'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'module',array('size'=>500,'maxlength'=>500,'style'=>'width:111px;')); ?>
			</div>
		</div>	
		
		<div class="row  width203 margint10">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'dep ver'); ?>
				<span class="spliter"></span>
				<?php echo $form->textField($model,'dep_version',array('size'=>15,'maxlength'=>15)); ?>
			</div>
		</div>
		<div class="row width203 margint10">
			<div class="selectBg_search">
				<?php echo $form->label($model,'infor',array('class'=>'width71')); ?>
				<span class="spliter"></span>
				<?php echo CHtml::activeDropDownList($model, 'infor_version', Codelkups::getCodelkupsDropDown('soft_version'), array('prompt'=>Yii::t('translations', 'Select Version'),'class'=>'width111 paddingtb10 smaller_margin')); ?>
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
						if(GroupPermissions::checkPermissions('general-deployments','write'))
							{
						?>
						<div class="cover">
							<div class="li noborder" ><a class="special_edit_header" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('deployments/create');?>"><?php echo Yii::t('translations', 'New Deployment');?></a></div>
						</div>
						<div class="cover">
							<div class="li noborder special_edit_header" onclick="exportexcel();">Export to Excel</div>
						</div>
						<?php } ?>
					</div>
					<div class="ftrli"></div>
			    </div>
			    <div id="users-list" style="display:none;"></div>
			 </div></div>
		</div>
	

		<!--<div class="btn" onclick="js:hidedropdown();">-->
		
		<div class="horizontalLine search-margin"></div>	
		
<?php $this->endWidget(); ?>	
	
</div><!-- search-form -->