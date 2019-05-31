<div class="wide search" id='search-support-desk'>
	<?php
			$status = SupportRequest::getStatusContor($model->search(null,true));
	?>
	<div id="incidents"><div class="div2 em child margintm40">	<?php  foreach ($status as $key_status => $stat) { ?>
				<div onclick="triggerSDSearch('<?php echo $key_status;?>');" class="phase inline-block normal width121 st_<?php echo $key_status; ?>">
					<span class="text"><?php echo SupportRequest::getStatusLabel($key_status);?></span>
					<span class="number"><?php echo $stat;?> <span style="color:#989898;"><?php  echo "/ ";echo SupportRequest::getTotalStatus($key_status); ?></span></span>
				</div>	<?php } ?>	</div>	</div>
				<div class="sep"></div>	<div class ="toggleBtn">	
				<div class ="toggle">	<a id="icon-hide-show" class="icon-plus">Hide searchbar</a>	</div>	
		<div style="float:right;margin-top: 10px;width: 10px;">	<span>|</span>	</div>
		<div class="header_export" id="export">	<a>Export to Excel</a>	</div>	

	</div><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get','id'=>'search_support')); ?>
	<div class="row ea_number margint10 width220" >	<div class="inputBg_txt" >	
			<?php echo $form->label($model,'rsr #',array('class' => 'width90')); ?>	<span class="spliter"></span>
			<?php echo $form->textField($model,'rsr_no',array('class'=>'width101')); ?>	</div>	</div>

	<div class="row customer margint10">
		<div class="inputBg_txt">	<?php echo $form->label($model,'Customer'); ?>	<span class="spliter"></span>	
		<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,
					'attribute' => 'id_customer','source'=>SupportRequest::getCustomersAutocomplete(),
					'options'=>array(	'minLength'=>'0',	'showAnim'=>'fold',	),	
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width111", ),	));	
				?>
		</div>	</div>

	<div class="row margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'severity'); ?>	<span class="spliter"></span>
			<div class="select_container width111">
			<?php echo $form->dropDownList($model, 'severity', array('High'=>'High','Medium'=>'Medium','Low'=>'Low'), array('prompt'=>'')); ?>
			</div>
	</div>	</div>

	<div class="row status margint10 width190">	<div class="selectBg_search">	<?php echo $form->label($model, 'status'); ?>
			<span class="spliter " ></span>	<div class="select_container width111" id="status_dd" onclick='javascript:showdropdown();'>
			<p id="status_str" style="padding-top:10px;"></p>
			<?php echo $form->dropDownList($model, 'status', SupportRequest::getStatusListSearch(), array('id'=>'inv_status','onchange'=>'changestat($(this).val())','prompt'=>'','multiple' => 'multiple','style'=>'margin-top:-15px;z-index:100;position: absolute;width:120px;height:170px;overflow:hidden; background-color:white; visibility:hidden')); ?></div>
		</div>	
	</div>

	<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->label($model, 'version',array('class' => 'width90')); ?>		<span class="spliter"></span>
			<div class="select_container width101"><?php echo $form->dropDownList($model, 'version', Codelkups::getCodelkupsDropDown('soft_version'), array('prompt'=>'')); ?></div>
		</div>	</div>	

	<div class="row customer margint10">	<div class="inputBg_txt">	<?php echo $form->label($model, 'Users'); ?>
			<span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,'attribute' => 'assigned_to',		
					'source'=>SupportRequest::getUsersAutocomplete(),'options'=>array(	'minLength'=>'0','showAnim'=>'fold',),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",	),	));	?>	
	</div>	</div> 


	<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->label($model, 'product'); ?>		<span class="spliter"></span>
			<div class="select_container width111"><?php echo $form->dropDownList($model, 'product', Codelkups::getCodelkupsDropDown('product'), array('prompt'=>'')); ?></div>
		</div>	</div>	

		<div class="btn_search_support"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		</div>	<div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?>
</div><script>
$(document).ready(function() {
	$("#reset").click(function() {
		<?php 
			unset(Yii::app()->session['id']);
			unset(Yii::app()->session['id_files']);
		?>
	});
});

function changestat(element) {
	fdate = element.toString();
	fdate= fdate.replace('0','Open');fdate= fdate.replace('1','Pending Info');fdate= fdate.replace('2','In Research');
	fdate= fdate.replace('3','In Development');fdate= fdate.replace('4','Resolved');fdate= fdate.replace('5','Reopened');
	fdate= fdate.replace('6','Closed'); fdate= fdate.replace('7','Cancelled'); fdate= fdate.replace('8','Not Confirmed'); 
	fdate= fdate.replace(' ','');
	fdate= fdate.substring(0, 18);	
	$('#status_str').html(fdate); }
</script>