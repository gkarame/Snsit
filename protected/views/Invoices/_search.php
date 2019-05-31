<div class="wide search" id="search-invoices" style="overflow:inherit;">
<?php $form=$this->beginWidget('CActiveForm', array('action'=>Yii::app()->createUrl($this->route),'method'=>'get','id'=>'search_invoice')); ?>
	<div class="row width_project_name "><div class="inputBg_txt">
			<?php echo $form->label($model,'Project Name'); ?><span class="spliter"></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'project_name','source'=>Projects::getProjectsAutocomplete(),
					'options'=>array('minLength'	=>'0','showAnim'	=>'fold',),'htmlOptions'	=>array('onfocus' 	=> "javascript:$(this).autocomplete('search','');",'class'=> "width150"),)); ?>
		</div></div>
	<div class="row">	<div class="inputBg_txt" style="width:235px;">
			<label style="width:100px"><?php echo Yii::t('translations', 'Customer');?></label>
			<span class="spliter" ></span>
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array('model' => $model,'attribute' => 'id_customer','source'=>Maintenance::getCustomersAutocomplete(),
					'options'=>array('minLength'=>'0','showAnim'=>'fold',),
					'htmlOptions'=>array('onfocus' => "javascript:$(this).autocomplete('search','');",'class'=> 'width116',),));?>
		</div></div>
	<div class="row " style="width:300px;">	<div class="inputBg_txt">
			<?php echo $form->label($model,'Invoice #',array('style'=>'width:95px')); ?><span class="spliter "></span>
			<?php echo $form->textField($model,'final_invoice_number',array('size'=>50,'maxlength'=>50,'style'=>'width:176px')); ?>
		</div></div>	
	<div class="row margint10" style="width:278px;">	<div class="selectBg_search">
			<?php echo $form->label($model,'status',array('style'=>'width:100px')); ?><span class="spliter"></span>
			<div class="select_container" style="width:149px !important">
				<?php echo $form->dropDownList($model, 'status', Invoices::getStatusList(), array('prompt'=>'')); ?>
			</div></div></div>
	<div class="row dateRow margint10">	<div class="inputBg_txt">	<?php echo $form->label($model,'invoice_month',array('style'=>'width:100px')); ?>
			<span class="spliter"></span><div class="select_container width116">
				<?php echo $form->dropDownList($model,'invoice_date_month',Invoices::getMonths(),array('prompt'=>'')); ?>
			</div>	</div> </div>	
	<div class="row dateRow margint10" style="width:300px;"><div class="inputBg_txt">
			<?php echo $form->label($model,'invoice_year',array('style'=>'width:95px')); ?>	<span class="spliter"></span><div class="select_container width176" >	
				<?php echo $form->dropDownList($model,'invoice_date_year',Invoices::getYears(),array('prompt'=>'')); ?>
			</div></div></div>
	<div class="row margint10" style="width:278px;"><div class="selectBg_search">	<?php echo $form->label($model,'Partner',array('style'=>'width:100px')); ?>
			<span class="spliter"></span><div class="select_container" style="width:149px !important">
				<?php echo $form->dropDownList($model, 'partner', Codelkups::getCodelkupsDropDown('partner'), array('prompt'=>'')); ?>
			</div></div></div>
	<div class="row margint10" style="width:245px;"><div class="selectBg_search">
			<?php echo $form->label($model,'type',array('style'=>'width:100px')); ?><span class="spliter"></span>
			<div class="select_container width116"  onclick='javascript:showdropdown();' ><?php echo $form->dropDownList($model, 'type', array('Standard'=>'Standard' ,'Travel Expenses'=>'Travel Expenses', 'Expenses'=>'Expense Sheet', 'Maintenance'=>'Maintenance' , 'T&M'=>'T&M'), array('id'=>'inv_type','multiple' => 'multiple','style'=>'height:90px;overflow:hidden; background-color:white; visibility:hidden')); ?>
	</div></div></div>
	<div class="row margint10" style="width:345px;"> <div class="inputBg_txt" style="width:290px;">	<?php echo $form->label($model,'EA #',array('style'=>'width:95px')); ?>
			<span class="spliter"></span><?php echo $form->textField($model,'id_ea', array('size'=>50,'maxlength'=>1000,'style'=>'width:176px')); ?>
		</div></div>
	<div class="row margint10" style="width:278px;">	<div class="selectBg_search">
			<?php echo $form->label($model,'1st payment',array('style'=>'width:100px')); ?><span class="spliter"></span>
			<div class="select_container" style="width:149px !important">
				<?php echo $form->dropDownList($model, 'payment', Invoices::getpaymentList(), array('prompt'=>'')); ?>
	</div></div></div>
	<div class="btn" onclick="js:hidedropdown();">
		<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>	
			<div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel"><div class="headli"></div><div class="contentli">
						<?php if(GroupPermissions::checkPermissions('financial-invoices','write')){ ?>
						<div class="cover"><div class="li noborder" onclick="showInvoicesDates();">TO PRINT</div></div>
						<div class="cover"><div class="li noborder" onclick="checkPrint();">PRINT</div></div>
						 <!--<div class="cover"><div class="li noborder" onclick="checkTransfer();">PRINT TRANSFER</div></div>-->
						 <?php if(Yii::app()->user->id == 19){ ?>
						<div class="cover"><div class="li noborder delete" onclick="deleteInv();">DELETE</div></div>
						<?php } }?>
						<div class="cover"><div class="li noborder delete" onclick="sendToCustomer(this);">SEND TO CUSTOMER</div></div> 
						<div class="cover"><div class="li noborder delete" onclick="getExcel(this);">EXPORT TO EXCEL</div></div> 
						<div class="cover"><div class="li noborder delete" onclick="share(this);">SHARE</div></div> 
				</div><div class="ftrli"></div></div><div id="users-list" style="display:none;"></div></div>
	</div>
<div class="horizontalLine search-margin"></div>
<?php $this->endWidget(); ?></div>
