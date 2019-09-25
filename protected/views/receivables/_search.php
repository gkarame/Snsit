<div class="wide search" id="receivablesSearch" ><?php $form=$this->beginWidget('CActiveForm', array('method'=>'get','id'=>'search_receivable')); ?>
<div class="row customer"><div class="inputBg_txt">	<?php echo $form->label($model, 'customer'); ?>	<span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_customer',	'source'=>Invoices::getCustomersAutocomplete(),
'options'=>array('minLength'=>'0','showAnim'=>'fold',),'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'	  => "width75",),));?></div></div>
<div class="row status  " style="width:170px;">	<div class="selectBg_search"><?php echo $form->label($model, 'status'); ?>	<span class="spliter" ></span>
				<div class="select_container " style="width:103px !important"><?php echo $form->dropDownList($model, 'status', Invoices::getStatusListReceivables(array('New','To Print')), array('prompt'=>'')); ?></div>
			</div></div><div class="row " style="width:225px;">	<div class="selectBg_search">	<?php echo $form->label($model, 'partner status', array('style'=>'width:110px;')); ?>
				<span class="spliter"></span><div class="select_container" style="width:100px !important">
				<?php echo $form->dropDownList($model, 'partner_status', Receivables::getPartnerStatus(), array('prompt'=>'')); ?></div></div>	</div>		
	<div class="row partener ">	<div class="selectBg_search">	<?php echo $form->label($model, 'partner'); ?>	<span class="spliter"></span>
				<div class="select_container "><?php echo $form->dropDownList($model, 'partner', Codelkups::getCodelkupsDropDown('partner'), array('prompt'=>'')); ?></div>	</div>	</div>
<div class="row idea_age" >	<div class="selectBg_search">	<?php echo $form->label($model, 'age'); ?>	<span class="spliter"></span>
				<div class="select_container"><?php echo $form->dropDownList($model, 'age', Receivables::getAgeOptions(), array('prompt'=>'')); ?></div></div>	</div>
		<div class="row invoice_number  margint10"  style="width:170px;">	<div class="inputBg_txt" >	<?php echo $form->label($model, 'invoice_number'); ?>
				<span class="spliter"></span><?php echo $form->textField($model, 'final_invoice_number',array('class' => 'width77', 'size'=>50,'maxlength'=>50)); ?></div>	</div>
	<div class="row idea_short margint10" style="width:170px;">	<div class="inputBg_txt"><?php echo $form->label($model, 'id_ea', array('style'=>'width:55px;')); ?>
				<span class="spliter"></span><?php echo $form->textField($model, 'id_ea', array('class' => 'width93', 'size'=>50,'maxlength'=>50)); ?>	</div>	</div>
		<div class="row margint10" style="width:225px;"><div class="selectBg_search"><?php echo $form->label($model, 'Assigned To', array('style'=>'width:110px;')); ?>
				<span class="spliter"></span><div class="select_container" style="width:100px !important">
				<?php echo $form->dropDownList($model, 'id_assigned', Receivables::getAssignedto(), array('prompt'=>'')); ?></div>	</div>	</div>
	<div class="row partener  margint10">	<div class="inputBg_txt">	<?php echo $form->label($model,'Project'); ?><span class="spliter"></span>
				<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'project_name','source'=>Projects::getProjectsAutocomplete(),
'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'		=> "width74"),));?>	</div>	</div>
		


<div class="row margint10 idea_age"   ><div class="selectBg_search">	<?php echo $form->label($model,'old'); ?>
			<span class="spliter"></span><div class="select_container"  >
				<?php echo $form->dropDownList($model, 'old',array('No'=>'No','Yes'=>'Yes'), array('prompt' => '','class' => '')); ?>
			</div>	</div>	</div>	

			

			<div class="row margint10" style="width:170px;"><div class="selectBg_search">	<?php echo $form->label($model,'month'); ?>
			<span class="spliter"></span><div class="select_container"  style="width:80px;"  >
				<?php echo $form->dropDownList($model, 'invoice_date_month', Invoices::getMonths(), array('prompt' => '','style'=>'width:80px;')); ?>
			</div>	</div>	</div>	


			<div class="row status margint10" style="width:170px;"><div class="selectBg_search">	<?php echo $form->label($model,'Year'); ?>
			<span class="spliter"></span><div class="select_container"  style="width:103px;"  >
				<?php echo $form->dropDownList($model, 'invoice_date_year', Invoices::getYears(), array('prompt' => '')); ?>
			</div>	</div>	</div>	



			<div class="row invoice_number  margint10"  style="width:225px;">	<div class="inputBg_txt" >	<?php echo $form->label($model, 'partner inv#', array('style'=>'width:110px;')); ?>
				<span class="spliter"></span><?php echo $form->textField($model, 'partner_inv',array('size'=>50,'maxlength'=>50)); ?></div>	</div>

<div class="row partener margint10" ><div class="inputBg_txt">	<?php echo $form->label($model,'type',array('style'=>'')); ?>
			<span class="spliter"></span><div class="select_container"  onclick='javascript:showdropdown();' >
				<?php echo $form->dropDownList($model, 'type', array('Standard'=>'Standard' ,'Travel Expenses'=>'Travel Expenses', 'Expenses'=>'Expense Sheet', 'Maintenance'=>'Maintenance' , 'T&M'=>'T&M'), array('id'=>'inv_type','multiple' => 'multiple','style'=>'height: 90px;overflow: hidden;width: 105px;background-color: white;visibility: visible;position: absolute;')); ?>
			</div>	</div>	</div>	

		<div class="btn receivables-grid" onclick="js:hidedropdown();">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
			<div class="wrapper_action" id="action_tabs_right">	<div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="margin-top: 80px;">  	<div class="headli"></div>	<div class="contentli">	
				<?php if (GroupPermissions::checkPermissions('financial-receivables', 'write')){ ?>
						<div class="cover"><div class="li noborder" onclick="showTransfer();">Create Transfer</div></div>
						<div class="cover"><div class="li noborder" onclick="changeStatusPaid();">Set Partner to Paid</div></div>
						<div class="cover"><div class="li noborder" onclick="printReceivables();">Download</div></div><?php } ?>
						<!-- <div class="cover"><div class="li noborder"><a class="shareby_button" href="#" onclick="shareReceivables(this);return false;" >Share</a></div></div>-->
					<?php if (GroupPermissions::checkPermissions('financial-receivables','write')){	?>
					<!--<div class="cover"><div class="li noborder" onclick="changeStatusSNSPaid();">Set SNS to Paid</div></div>-->
					<div class="cover"><div class="li noborder" onclick="getUsers();"> Assign New User</div></div><?php }	?>
					<div class="cover"><div class="li noborder" onclick="getExcel();">Export to Excel</div></div>
					<div class="cover"><div class="li noborder" onclick="getReport();">Receivables Report</div></div></div><div class="ftrli"></div></div>
			    <div id="users-list" style="display:none;"></div></div></div><div class="horizontalLine search-margin"></div><?php $this->endWidget(); ?></div>