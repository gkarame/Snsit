<div class="wide search" id='search-support-desk'>
	<?php
		if (isset(Yii::app()->user->customer_id) && Yii::app()->user->customer_id != null)
			$id_customer = Yii::app()->user->customer_id;
		else 
			$id_customer = null;
			$status = SupportDesk::getStatusContor($model->search(null,true));
	?>
	<?php if (!Yii::app()->user->isAdmin) {?>
		<?php	$model_customer = Customers::model()->findByPk(Yii::app()->user->customer_id);	 $model_customer->cs_representative; ?>
		<?php if ($model_customer->cs_representative != 0) {
			if(Customers::getNewCaCust(Yii::app()->user->customer_id) > 0)
			{   	?>
		<div class="suppInfo" style="height: 120px;"> 
				<?php $this->renderPartial('_info_user2',array(	'model' => $model,	'model_customer' =>$model_customer	)); ?>
			</div>
		<?php 	}else{	?>

		<div class="suppInfo"> 
				<?php $this->renderPartial('_info_user',array(	'model' => $model,	'model_customer' =>$model_customer	)); ?>
			</div>
		<?php 	}

			?>
				<?php }?><?php } ?>
	<div id="incidents"><div class="div2 em child">	<?php  foreach ($status as $key_status => $stat) { ?>
				<div onclick="triggerSDSearch('<?php echo $key_status;?>');" class="phase inline-block normal st_<?php echo $key_status; ?>">
					<span class="text"><?php echo SupportDesk::getStatusLabel($key_status);?></span>
					<span class="number"><?php echo $stat;?> <span style="color:#989898;"><?php if(Yii::app()->user->isAdmin){ echo "/ ";echo SupportDesk::getTotalStatus($key_status);} ?></span></span>
				</div>	<?php } ?>	</div>	</div><div class="sep"></div>	<div class ="toggleBtn">	<div class ="toggle">	<a id="icon-hide-show" class="icon-plus">Hide searchbar</a>
		</div>	<div style="float:right;margin-top: 10px;width: 10px;">	<span>|</span>	</div>
		<div class="header_export" id="export">	<a>Export to Excel</a>	</div>	</div><?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get','id'=>'search_support')); ?>
	<div class="row ea_number margint10 width220" >	<div class="inputBg_txt" >	<?php echo $form->label($model,'sr #'); ?>	<span class="spliter"></span>
			<?php echo $form->textField($model,'sd_no',array('style'=>'width:161px')); ?>	</div>	</div>	<div class="row customer margint10">
		<div class="inputBg_txt">	<?php echo $form->label($model,'Customer'); ?>	<span class="spliter"></span>	
		<?php 	$this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_customer','source'=>SupportDesk::getCustomersAutocomplete(),
					'options'=>array(	'minLength'=>'0',	'showAnim'=>'fold',	),	'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",	'class'	  => "width111", ),	));		?>
		</div>	</div><div class="row margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'severity'); ?>	<span class="spliter"></span>
			<div class="select_container width111"><?php echo $form->dropDownList($model, 'severity', array('High'=>'High','Medium'=>'Medium','Low'=>'Low'), array('prompt'=>'')); ?></div>
		</div>	</div><div class="row status margint10 width190">	<div class="selectBg_search">	<?php echo $form->label($model, 'status'); ?>
			<span class="spliter " ></span>	<div class="select_container width111" id="status_dd" onclick='javascript:showdropdown();'>
<p id="status_str" style="padding-top:12px;"></p>
			<?php echo $form->dropDownList($model, 'status', SupportDesk::getStatusList(), array('id'=>'inv_status','onchange'=>'changestat($(this).val())','prompt'=>'','multiple' => 'multiple','style'=>'margin-top:-15px;z-index:100;position: absolute;width:120px;height:125px;overflow:hidden; background-color:white; visibility:hidden')); ?></div>
		</div>	</div>	


			<div class="row margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'reason'); ?>	<span class="spliter"></span>
			<div class="select_container width111"><?php echo $form->dropDownList($model, 'reason', Codelkups::getCodelkupsDropDown('reason'), array('prompt'=>'')); ?></div>
		</div>	</div>

		<div class="row customer margint10">	<div class="inputBg_txt">	<?php echo $form->label($model, 'Assignee'); ?>
			<span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,'attribute' => 'assigned_to',		
					'source'=>SupportDesk::getUsersAutocomplete(),'options'=>array(	'minLength'=>'0','showAnim'=>'fold',),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",'autocomplete'=>'off'	),	));	?>	</div>	</div> 

<?php if (Yii::app()->user->isAdmin){ ?>
		<div class="row margint10">	<div class="selectBg_search">	<?php echo $form->label($model, 'ca'); ?>		<span class="spliter"></span>
			<div class="select_container width111"><?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,'attribute' => 'responsibility',		
					'source'=>SupportDesk::getUsersAutocomplete(),'options'=>array(	'minLength'=>'0','showAnim'=>'fold',),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",'autocomplete'=>'off'	),	));	?>
					 </div>
		</div>	</div>

		<div class="row margint10 width190" >	<div class="selectBg_search">	<?php echo $form->label($model, 'cs rep', array('style'=>'width:50px;')); ?>		<span class="spliter"></span>
			<div class="select_container width111"><?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,'attribute' => 'product',		
					'source'=>SupportDesk::getUsersAutocomplete(),'options'=>array(	'minLength'=>'0','showAnim'=>'fold',),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width111",	'autocomplete'=>'off'),	));	?>
					 </div>
		</div>	</div>


<div class="row margint10"><div class="selectBg_search">	<?php echo $form->label($model, 'team'); ?>	<span class="spliter"></span>
			<div class="select_container width111"><?php echo $form->dropDownList($model, 'submitter_name', array('CS'=>'CS','OPS'=>'OPS','PS'=>'PS'), array('prompt'=>'')); ?></div>
		</div>	</div>


<?php } ?>



			<div class="btn_search_support" style="<?php if (Yii::app()->user->isAdmin){ echo'float: right;    margin-right: 10px;'; }?>"><?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		<?php if (!Yii::app()->user->isAdmin) {	
			$msg = 	SupportDesk::checkUnratedbyCustomer($model_customer->id);
			if($msg=='' && Customers::getNewCaCust(Yii::app()->user->customer_id) ==  0){
					echo CHtml::link(Yii::t('translation', 'New'), array('create'), array('class'=>'add-incident add-btn','id'=>'reset','style'=>'float:left')); 
			}else if($msg !=''){
?>						<br/><br/>	<br/><br/><br/><br/>	<br/><br/>
					<div style="width:100%; height:60px;background-color:#f2e9e9!important; margin-top:20px;"><img src='../images/validations_alert.png' height='25' width='25' style="margin-top:20px; margin-left:20px; float:left;">
					 <div style=" font-weight:bold;   padding-top:27px; padding-left:15px; float:left" class="red" > <?php echo $msg; ?> </div> </div><?php 		}	} else {	?>	<a href="javascript:void(0);" class="followUpButton" style="margin-top:10px;"></a>
		<?php } ?>	</div>	<div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?>
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
	fdate= fdate.replace('0','New');fdate= fdate.replace('1','In Progress');fdate= fdate.replace('2','Awaiting Customer');
	fdate= fdate.replace('3','Solution Proposed');fdate= fdate.replace('4','Reopened');fdate= fdate.replace('5','Closed');
	fdate= fdate.replace(' ','');
	fdate= fdate.substring(0, 18);	$('#status_str').html(fdate); }
</script>