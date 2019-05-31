<div class="wide search" id="search-customers">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); ?>	
		
		<div class="row width203">
			<div class="inputBg_txt " >
				<label><?php echo Yii::t('translations', 'name');?></label>
				<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,	'attribute' => 'name',	'source'=>Customers::getCustDD(),
						'options'=>array('minLength'=>'0',		'showAnim'=>'fold',	),
						'htmlOptions'=>array(
							'style'		=> "",	'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
		</div>		
		<div class="row width203">
			<div class="selectBg_search">
				<?php echo $form->label($model,'status'); ?>
				<span class="spliter"></span>
				<div class="select_container">
					<?php echo $form->dropDownList($model,'status', Customers::getStatusList(), array('prompt'=>'Select status')); ?>
				</div>
			</div>
		</div>		
		<div class="row " >
			<div class="selectBg_search">
				<?php echo $form->label($model, 'country'); ?>
				<span class="spliter"></span>
				<div class="select_container width111">
					<?php echo $form->dropDownList($model, 'country', Codelkups::getCodelkupsDropDown('country'), array('prompt'=>'Select country')); ?>
				</div>
			</div>
		</div>	
		<div class="row margin_right0 width203">
			<div class="inputBg_txt">
				<label><?php echo Yii::t('translations', 'Contact');?></label>
				<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,				'attribute' => 'contact_name',	'source'=>Customers::getContactsAutocomplete(),
						'options'=>array('minLength'=>'0',	'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
		</div>
		<div class="row margint10 width203">
			<div class="inputBg_txt " >
				<label><?php echo Yii::t('translations', 'erp');?></label>
				<span class="spliter"></span>
				<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,	'attribute' => 'erp',	'source'=>Customers::getERPs(),
						'options'=>array('minLength'=>'0',		'showAnim'=>'fold',	),
						'htmlOptions'=>array(
							'style'		=> "",	'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
		</div>		
		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); 
			if(GroupPermissions::checkPermissions('customers-list','write') && Users::ValidateGroupAdmin(Yii::app()->user->id) > 0){?>
			<div class="action" onclick="chooseActions();">				<u><b>ACTION</b></u>
			</div>
			<div class="action_list " style="padding-top:12px;">
				<div class="headli"></div>
				<div class="contentli">
					<div class="cover">
						<div class="li noborder"><?php echo CHtml::link(Yii::t('translation', 'ADD CUSTOMER'), array('create'), array('class'=>'add-customer')); ?> </div>
					</div>
				</div>
				<div class="ftrli"></div>	
			</div>	<?php }	?>
		</div>
		<div class="horizontalLine search-margin"></div>			
	<?php $this->endWidget(); ?>	
</div>