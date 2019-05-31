<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Customer Details');?></span></div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode(Yii::t('translations','Name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->name)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('website')); ?></div>
	<div class="general_col4 ">
		<?php  $href = trim($model->website);
			if (strpos($model->website, 'http://') === false){    $href = 'http://'.$href; }	?>
		<?php if (!empty($model->website)) { ?>
		<a href="<?php echo $href;?>" target="_blank"><?php echo $model->website;?></a>
		<?php } ?>
	</div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('industry')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Customers::getIndustry($model->industry)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('primary_contact_name')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode($model->primary_contact_name); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('primary_contact_job_title')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->job_title); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('primary_contact_email')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->primary_contact_email); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('main_phone')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->main_phone); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('mobile_number')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->mobile_number); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->status == '0' ? 'Inactive' : 'Active' ); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('strategic')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->strategic); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('type_of_items')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->product_type); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('brands')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode($model->brands); ?></div>
</div>
<div class="view_row">			
	<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('erp')); ?></div>
	<div class="general_col2"><?php echo CHtml::encode($model->erp); ?></div> <?php } ?>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('address')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->address); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('city')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->city); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('country')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->cCountry->codelkup); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->cCurrency->codelkup); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('region')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->cRegion->codelkup); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Week-End Days')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->week_end); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('time_zone')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->time_zone); ?></div>
</div>
<div class="view_row"> 
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('custsupport')); ?></div>
	<div class="general_col2 "><div class="checkbox_div column30 item side-borders side-borders-last  no-border " style="margin-top:-7px;" >
		<input  id="checks_<?php echo $model->id?>" type="checkbox" <?php echo $model->custsupport; ?>  onclick="checkSupp('<?php echo $model->id;?>');"/>
	</div></div>
</div>

<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Billing Details');?></span></div>
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('customer_reference')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Customers::getNameById($model->customer_reference)); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('bill_to_contact_person')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->bill_to_contact_person); ?></div>	
</div>
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('bill_to_address')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->bill_to_address); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('bill_to_contact_email')); ?></div>
	<div class="general_col4 " style="word-wrap: break-word;"><?php echo CHtml::encode($model->bill_to_contact_email); ?></div>	
</div>
<div class="view_row">	
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('bank')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode((isset($model->bank))? $model->rbank->codelkup : "" ); ?></div>	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('aux')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode((isset($model->aux))? $model->raux->codelkup : ""); ?></div>	
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('lpo_required')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->lpo_required); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_assigned')); ?></div>
	<div class="general_col4"><?php if ($model->id_assigned == '40'){echo CHtml::encode('Irene Rabbah');}else if($model->id_assigned == '23'){echo CHtml::encode('Nadine Abboud');}else if($model->id_assigned == '19'){echo CHtml::encode('Micheline Daaboul');} else if ($model->id_assigned == '11') {echo CHtml::encode('Claudine Daaboul');} else{echo CHtml::encode('');} ?></div>	
</div>
<div class="view_row">	
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('dolphin_aux')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->dolphin_aux); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('checkb')); ?></div>
	<div class="general_col4 "><div class="checkbox_div column30 item side-borders side-borders-last  no-border " style="margin-top:-7px;" >
		<input  id="chech_<?php echo $model->id?>" type="checkbox" <?php echo $model->checkb; ?>  onclick="checkSens('<?php echo $model->id;?>');"/>
	</div></div>
</div>

<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Account Details');?></span></div>
<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>
<div class="view_row">
	<div class="general_col1"><?php echo "Support Status";?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Customers::getActiveMaint($model->id) == '0' ? 'Inactive' : 'Active' ); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('ca')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode(Users::getUsername($model->ca)); ?></div>
</div><?php } 
 $support_weekend=$model->support_weekend; if($support_weekend!="N/A"){ ?>
<div class="view_row">
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('account_manager')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Users::getUsername($model->account_manager)); ?></div>
<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>	
	<div class="general_col3" id="cs1"><?php echo CHtml::encode($model->getAttributeLabel('cs_representative')); ?></div>	
	<div class="general_col4" id="cs2"><?php echo CHtml::encode(Users::getUsername($model->cs_representative)); ?></div> <?php } ?>
</div>
<?php } if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('n_licenses_allowed')); ?></div>
	<div class="general_col2"><?php echo CHtml::encode($model->n_licenses_allowed); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('n_licenses_audited')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode($model->n_licenses_audited); ?></div>
</div>
<div class="view_row">		
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('last_date_audit')); ?></div>
	<div class="general_col2"><?php echo CHtml::encode($model->date_audit); ?></div>
	<div class="general_col3"><?php echo "Product(s)";?></div>
	<div class="general_col4"><?php echo CHtml::encode(Customers::getProductwithActiveMaint($model->id)); ?></div>
</div>
<div class="view_row">	
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('reassign_notification')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->reassign_notification); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Complexity')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Customers::getComplexityLabel($model->complexity)); ?></div>
 </div> <?php }else{ ?>
 <div class="view_row">	
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Complexity')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Customers::getComplexityLabel($model->complexity)); ?></div>
 </div> 
 <?php } ?>
  <div class="view_row"></div>
<?php $support_weekend=$model->support_weekend;if($support_weekend!="N/A"){ ?>	
<div class="contacts">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'contacts-grid',	'dataProvider'=>$model->contacts,	'summaryText' => '',	'pager'=> Utils::getPagerArray(),
	'template'=>'{items}{pager}',
	'columns'=>array(
		'name',
		'email',
		'job_title',
		'mobile_number',
		array(    
			'header'=>'SD Access',       
			'id'=>'checkcustomers',
			'type'=>'raw',
			'htmlOptions' => array('class' => 'item checkbox_grid_customers'),
			'value'=>' GroupPermissions::checkPermissions("customers-list","write")?CHtml::CheckBox("access",($data->access == "Yes")?true:false , array (
                                        "style"=>"width:10px;margin-left: 17px;margin-top: 8px;",
                                        "class"=>($data->access == "Yes")?"checked":"",
                                        "id" =>"red_$data->id",
                                        "onClick"=>"access($data->id)")):CHtml::CheckBox("access",($data->access == "Yes")?true:false , array (
                                        "style"=>"width:10px;margin-left: 17px;margin-top: 8px;",
                                        "class"=>($data->access == "Yes")?"checked":"",
                                        "id" =>"red_$data->id",
                                        "disabled"=>true));',
		),
	),
)); ?>
</div>
<?php }else{ ?>
<div class="contacts">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'contacts-grid',
	'dataProvider'=>$model->contacts,
	'summaryText' => '',
	'pager'=> Utils::getPagerArray(),
	'template'=>'{items}{pager}',
	'columns'=>array(
		'name',
		'email',
		'job_title',
		'mobile_number'
	),
)); ?>
</div>
<?php }?>
<div id="more"  >
<img style="cursor: pointer;"  onclick="showMore()" src="<?php echo Yii::app()->getBaseUrl().'/images/showMore1.png';?>" />
</div>
<div id="less" class="hidden" style="">
<img style="cursor: pointer;"  onclick="showless()" src="<?php echo Yii::app()->getBaseUrl().'/images/showless1.png';?>" />
</div>
<?php unset($_SESSION['limit']); ?>
<br clear="all" />
<script>
function showless(){
$.ajax({	type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('customers/getless');?>", 
		  	dataType: "json",  	data: {'id_cust':'<?php echo $model->id;?>'},
		  	success: function(data) { }
		});   
$.fn.yiiGridView.update("contacts-grid"); $("#less").addClass("hidden");  $("#more").removeClass("hidden");
}
function showMore(){
$.ajax({
	 		type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('customers/getAll');?>", 
		  	dataType: "json",  	data: {'id_cust':'<?php echo $model->id;?>'},
		  	success: function(data) {	}
		});
$.fn.yiiGridView.update("contacts-grid"); $("#more").addClass("hidden");  $("#less").removeClass("hidden"); 
}

function checkSens (id_cust) {
			if ($('#chech_'+id_cust).is(':checked')){	check='checked';
			}else{	check='';	}
			$.ajax({	type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('customers/updateSensitive');?>", 
		  	dataType: "json",  	data: {'id_cust':id_cust,'check':check},
		  	success: function(data) {	}
		});
	}
function checkSupp (id_cust) {
			if ($('#checks_'+id_cust).is(':checked')){	check='checked';
			}else{	check='';	}
			$.ajax({	type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('customers/updateCustSupp');?>", 
		  	dataType: "json",  	data: {'id_cust':id_cust,'check':check},
		  	success: function(data) {	}
		});
	}
	function access(id,element){
		if ($('#red_'+id).hasClass('checked')){ val = "No";
			$('#red_'+id).removeClass('checked');
		}else{	val = "Yes";	$('#red_'+id).addClass('checked');	}
		$.ajax({
	 		type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('customers/accessFlag');?>", 
		  	dataType: "json",	data: {'id':id,'val':val},
		  	success: function(data) { }
		});
	}
</script>