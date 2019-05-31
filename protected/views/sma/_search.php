<div class="wide search" id='search-sma'>	<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get',	'id'=>'search_sma')); ?>
	<div class="row status margint10" >	<div class="inputBg_txt" >	<?php echo $form->label($model,'ssn#'); ?>	<span class="spliter"></span><?php echo $form->textField($model,'id_no',array('class'=>'width80')); ?>	</div>	</div>
	<div class="row customer margint10 width230">	<div class="inputBg_txt ">	<?php echo $form->label($model,'Customer',array('class'=>'width90')); ?>
			<span class="spliter"></span>	<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,	'attribute' => 'id_customer',	'source'=>Sma::getCustomersAutocomplete(),
					'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width111",),	));	?>
		</div></div>	<div class="row customer margint10"><div class="selectBg_search"><?php echo $form->label($model,'sma_month'); ?>
			<span class="spliter"></span>	<div class="select_container width111">	<?php echo $form->dropDownList($model,'sma_month',Invoices::getMonths(),array('prompt'=>'','class'=>'width111')); ?>
			</div>	</div></div><div class="row status margint10" >	<div class="selectBg_search">	<?php echo $form->label($model,'sma_year'); ?>
			<span class="spliter"></span>	<div class="select_container " ><?php echo $form->dropDownList($model,'sma_year',Invoices::getYears(),array('prompt'=>'')); ?>
			</div></div></div><div class="row status margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'status'); ?>
			<span class="spliter"></span>	<div class="select_container "><?php echo $form->dropDownList($model, 'status', Sma::getStatusList(), array('prompt'=>'')); ?></div>
		</div></div><div class="row customer margint10 width230"><div class="inputBg_txt ">	<?php echo $form->label($model, 'assigned_to' ,array('class'=>'width90')); ?>
			<span class="spliter"></span><?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,	'attribute' => 'assigned_to','source'=>Sma::getUsersAutocomplete(),
					'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",),	));	?>	</div>	</div>  
	<div class="btn"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?></div><div class="horizontalLine search-margin"></div>
<?php $this->endWidget(); ?></div>
<script>
$(document).ready(function() {
	$("#reset").click(function() {
		<?php 
			unset(Yii::app()->session['id']);
			unset(Yii::app()->session['id_files']);
		?>
	}); });
</script>