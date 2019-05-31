<div class="wide search" id='search-support-desk'>
	<?php
		if(isset(Yii::app()->user->customer_id) && Yii::app()->user->customer_id != null)
			$id_customer = Yii::app()->user->customer_id;
		else 
			$id_customer = null;
		$status = SupportDesk::getStatusContor($model->search(null,true));	?>
	<div id="incidents">	<div class="div2 em child">	<?php  foreach ($status as $key_status => $stat) { ?>				
				<div onclick="triggerSDSearch('<?php echo $key_status;?>');" class="phase inline-block normal st_<?php echo $key_status; ?>">
					<span class="number"><?php echo $stat;?></span>	<span class="text"><?php echo SupportDesk::getStatusLabel($key_status);?></span>
					<span class="text"><?php echo "/8" ?></span>	</div>	<?php } ?>	</div>	</div>	<?php if(!Yii::app()->user->isAdmin){?>
		<?php $model_customer = Customers::model()->findByPk(Yii::app()->user->customer_id);?>
		<?php if($model_customer->cs_representative != 0){?>
			<div class="suppInfo">	<?php $this->renderPartial('_info_user',array(	'model' => $model,	'model_customer' =>$model_customer	)); ?>		</div>	<?php }?><?php }?>
	<div class="sep"></div>	<div class ="toggleBtn"><div class ="toggle">
			<a id="icon-hide-show" class="icon-plus">Hide searchbar</a>	</div>	<div style="float:right;margin-top: 10px;width: 10px;">		<span>|</span>	</div>		<div class="header_export" id="export">				<a>Export to Excel</a>		</div>	</div>
<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get','id'=>'search_support' )); ?>
	<div class="row ea_number margint10" >	<div class="inputBg_txt" >	<?php echo $form->label($model,'sr #'); ?>
			<span class="spliter"></span>	<?php echo $form->textField($model,'sd_no',array('style'=>'width:161px')); ?>	</div>	</div>
	<div class="row customer margint10">	<div class="inputBg_txt">	<?php echo $form->label($model,'Customer'); ?>	<span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(	'model' => $model,'attribute' => 'id_customer',		
					'source'=>SupportDesk::getCustomersAutocomplete(),'options'=>array(	'minLength'=>'0','showAnim'=>'fold',	),'htmlOptions'=>array(	'onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",  ),	));	?>
		</div></div><div class="row margint10">	<div class="selectBg_search"><?php echo $form->label($model, 'severity'); ?>
			<span class="spliter"></span><div class="select_container width111"><?php echo $form->dropDownList($model, 'severity', array('High'=>'High','Medium'=>'Medium','Low'=>'Low'), array('prompt'=>'')); ?></div>
		</div>	</div>	<div class="row status margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'status'); ?><span class="spliter"></span>
			<div class="select_container "><?php echo $form->dropDownList($model, 'status', SupportDesk::getStatusList(), array('prompt'=>'')); ?></div></div></div>	
	<div class="row dateRow margint10"><div class="dateSearch selectBg_search"><?php echo $form->label($model,'due_date'); ?>
			<span class="spliter"></span>	<?php echo $form->textField($model,'due_date',array('class'=>'width111')); ?><span class="calendar calfrom"></span>	</div>	</div>	
	<div class="row customer margint10">	<div class="inputBg_txt">	<?php echo $form->label($model,'Users'); ?>		<span class="spliter"></span>
			<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array(	'model' => $model,	'attribute' => 'assigned_to',	'source'=>SupportDesk::getUsersAutocomplete(),
					'options'=>array(	'minLength'=>'0',	'showAnim'=>'fold',		),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",),		));	?>	</div>	</div>	
	<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->label($model, 'product'); ?>		<span class="spliter"></span>
			<div class="select_container width111"><?php echo $form->dropDownList($model, 'product', Codelkups::getCodelkupsDropDown('product'), array('prompt'=>'')); ?></div>
		</div>	</div>	
	<div class="btn_search_support">	<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		<?php if(!Yii::app()->user->isAdmin)
		{	echo CHtml::link(Yii::t('translation', 'New'), array('create'), array('class'=>'add-incident add-btn','id'=>'reset','style'=>'float:left')); } ?>	</div>	<div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>
<script>
$("#reset").click(function(){
	<?php 
	unset(Yii::app()->session['id']);
	unset(Yii::app()->session['id_files']);?>
});
</script>