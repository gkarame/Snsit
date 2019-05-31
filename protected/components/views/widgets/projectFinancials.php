<div class="bcontenu">
	<?php
	Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('projectFinancials-grid', {
			data: $(this).serialize()
		});
		$.fn.yiiGridView.update('projectFinancials-grid1', {
			data: $(this).serialize()
		});
		return false;
	});	");?>	
	<div class="search-form">
		<div class="wide search" id="search-projectFinancials">		
			<?php $form=$this->beginWidget('CActiveForm', array(
				'action'=>Yii::app()->request->requestUri,
				'method'=>'get',)); ?>
				<div class="row width_common">
					<div class="inputBg_txt">
						<?php echo $form->label($model,'Customer'); ?>
						<span class="spliter"></span>
						<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
								'model' => $model,
								'attribute' => 'customer_id',		
								'source'=>ProjectFinancials::getCustomersAutocomplete(),
								// additional javascript options for the autocomplete plugin
								'options'=>array(
									'minLength'	=>'0',
								),
								'htmlOptions'	=>array(
									'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
									'class'		=> "width94"
								),));?>
					</div>
				</div>
				<div class="row width_project_name ">
					<div class="inputBg_txt">
						<?php echo $form->label($model,'Project'); ?>
						<span class="spliter"></span>
						<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
								'model' => $model,
								//'attribute' => 'name',		
								'attribute' => 'name',
								'source'=>ProjectFinancials::getProjectsAutocomplete(),
								// additional javascript options for the autocomplete plugin
								'options'=>array(
									'minLength'	=>'0',
									'showAnim'	=>'fold',
								),
								'htmlOptions'	=>array(
									'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
									'class'		=> "width100"
								),));?>
					</div>
				</div>
				<div class="row">
					<div class="selectBg_search">
						<?php echo $form->label($model, 'status'); ?>
						<span class="spliter"></span>
						<div class="select_container width111">
							<div class="arrow"></div>
							<?php echo $form->dropDownList($model, 'status', ProjectFinancials::getStatusList(), array('prompt'=>'', 'class' => 'projectFinancesSelect')); ?>
						</div>
					</div>
				</div>
				<div class="btn"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	</div>
				<div class="horizontalLine search-margin"></div>
			<?php $this->endWidget(); ?>
			</div>
	</div>	
	<?php $this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'projectFinancials-grid',
			'dataProvider'=>$provider,
			'summaryText' => '',
			'pager'=> Utils::getPagerArray(),
		    'template'=>'{items}{pager}',
			//'filter'=>$model,
			'columns'=>array(	
				array(
					'name'=>'project',
					'type' => 'raw',
					'header'=> Yii::t('translations', 'Project'),
				),
				array(
					'name'=>'customer',
					'header'=> Yii::t('translations', 'Customer'),
				),
				array(
					'name'=>'total_amount',
					'header'=> Yii::t('translations', 'Amount'),
				),
				array(
					'name'=>'md',
					'header'=> Yii::t('translations', 'MDs'),
					'htmlOptions' => array('style' => 'text-transform:none !important'),
				),
				array(
					'name'=>'actual_md',
					'header'=> Yii::t('translations', 'A. MDs'),
				),
				array(
					'name'=>'remaining_md',
					'header'=> Yii::t('translations', 'Rem. MDs'),
				),
				array(
					'name'=>'original_rate',
					'header'=> Yii::t('translations', 'O. Rate'),
				),
				array(
					'name'=>'actual_rate',
					'header'=> Yii::t('translations', 'A. Rate'),
				),
				array(
					'name'=>'expenses_balance',
					'header'=> Yii::t('translations', 'EX Balance'),
				),
				array(
					'name'=>'cost',
					'header'=> Yii::t('translations', 'Cost'),
				),
				array(
					'name'=>'profit',
					'header'=> Yii::t('translations', 'Profit'),
				),
			),
	)); ?>	<br clear="all" />
</div>
<?php $id = $this->getId();
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'u'.$id,
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Dialog box 1',
        'autoOpen'=>false,
		'modal'=>true,
	    'width'=>1024,
	    'height'=>860,
		'resizable'=>false,
		'closeOnEscape' => true,
    ),));?>
<div class="bcontenu z-index">
	<div class="board inline-block ui-state-default noborder">
		<div class="bhead" id="maispecial">
			<div class="title"><?php echo  WidgetProjectFinancials::getName();;?></div>
			<div class="close left970" onclick='js:closeDialog(<?php echo $id ?>)'></div>
		</div> <div class="ftr"></div>
	</div>	
	<?php	Yii::app()->clientScript->registerScript('search', "
	$('.search-button').click(function(){
		$('.search-form').toggle();
		return false;
	});
	$('.search-form form').submit(function(){
		$.fn.yiiGridView.update('projectFinancials-grid1', {
			data: $(this).serialize()
		});
		return false;
	});
	");	
	$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'projectFinancials-grid1',
			'dataProvider'=>$provider,
			'summaryText' => '',
			'pager'=> Utils::getPagerArray(),
		    'template'=>'{items}{pager}',
			'columns'=>array(	
				array(
					'name'=>'project',
					'type' => 'raw',
					'header'=> Yii::t('translations', 'Project'),
				),
				array(
					'name'=>'customer',
					'header'=> Yii::t('translations', 'Customer'),
				),
				array(
					'name'=>'total_amount',
					'header'=> Yii::t('translations', 'Amount'),
				),
				array(
					'name'=>'md',
					'header'=> Yii::t('translations', 'MDs'),
				),
				array(
					'name'=>'actual_md',
					'header'=> Yii::t('translations', 'A. MDs'),
				),
				array(
					'name'=>'remaining_md',
					'header'=> Yii::t('translations', 'Rem. MDs'),
				),
				array(
					'name'=>'original_rate',
					'header'=> Yii::t('translations', 'O. Rate'),
				),
				array(
					'name'=>'actual_rate',
					'header'=> Yii::t('translations', 'A. Rate'),
				),
				array(
					'name'=>'expenses_balance',
					'header'=> Yii::t('translations', 'EX Balance'),
				),
				array(
					'name'=>'cost',
					'header'=> Yii::t('translations', 'Cost'),
				),array(
					'name'=>'profit',
					'header'=> Yii::t('translations', 'Profit'),
				),			),
)); ?>	<br clear="all" /></div><?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>