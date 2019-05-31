<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('RSR#')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->rsr_no); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('short_description')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->short_description); ?></div>
</div>
<div class="view_row">
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Customer')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Customers::getNameById($model->id_customer)); ?></div>
	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('assigned_to')); ?></div>
	<?php if((Users::checkCSManagers(Yii::app()->user->id)) > 0  && $model->status != 6){?>	
					<div class="general_col4 "><?php echo SupportRequest::getRSRUsers($model->id,$model->assigned_to)?></div>
	<?php }else{?>	<div class="general_col4 "><?php echo CHtml::encode(Users::getUsername($model->assigned_to)); ?></div><?php }?>
</div>
<div class="view_row">
<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<?php if((Users::checkCSManagers(Yii::app()->user->id)) > 0  && $model->status != 6){?>
		<div class="general_col2 "><?php echo SupportRequest::getCategories($model->id,$model->category);?></div>
		<?php }else{?>	
		<div class="general_col2 "><?php echo CHtml::encode(SupportRequest::getCategoryLabel($model->category));?></div>
		<?php }?>

		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	

	<?php if((Users::checkCSManagers(Yii::app()->user->id)) > 0 && $model->status != 6){?>	
					<div class="general_col4 "><?php echo SupportRequest::getAllstatuses($model->id,$model->status)?></div>
	<?php }else{?>	<div class="general_col4 "><?php echo SupportRequest::getprivilegedstatuses($model->id,$model->status)?></div><?php }?>
</div>

<div class="view_row">
	

	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('eta')); ?></div>
	<div class="general_col2 "><?php echo CHtml::textField("change_".$model->id,($model->eta != null)?date("d/m/Y",strtotime($model->eta)):"",array("style"=>"width:70px;text-align:left;border:none;background:#F0F0F0;color:#555555;font-family:Arial;font-size:12px","onClick"=>"changeDate($model->id)","onchange"=>"changeInput(value,$model->id,'1')")) ?></div>
	
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('root')); ?></div>
	<div class="general_col4 "><?php echo CHtml::activeDropDownList($model, "root", SupportRequest::getRootList(), array('class'=>'input_text_value','prompt'=>" ",'style'=>'width:140px;border:none;',"onchange"=>"changeInput(value,$model->id,3)")); ?></div>
</div>
<div class="view_row">
	<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('product')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->product0->codelkup); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('version')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->version0->codelkup); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('severity')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->severity); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('schema')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->schema); ?></div>

</div>
	<div class="view_row">	

<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('dbms')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->dbms)); ?></div>		
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Logged By')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Users::getNameById($model->logged_by)); ?></div>
	</div>
	<div class="view_row">			
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Submitted By')); ?></div>
		<div class="general_col2 "><?php echo $model->submitter_name; ?></div>

		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('CA')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Users::getUsername(Customers::getCAbyCustomer($model->id_customer))); ?></div>
		
	</div>
	<div class="view_row">

	<div class="general_col1 " style="    text-transform: none !important;"><?php echo CHtml::encode('LINKED SRs#'); ?></div>
	<div class="general_col2 "><?php echo SupportRequest::getSRs($model->sr); ?></div> 
	<div class="general_col3"><?php echo CHtml::encode("SRs Type"); ?></div>
	<div class="general_col4 "><?php echo SupportRequest::getSRsTypes($model->sr); ?></div> 
	
	</div>
	<div class="view_row">
	 <div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Time Spent(Hrs)')); ?></div>
		<div class="general_col2 "><?php echo SupportRequest::countTime($model->id, $model->id_customer, $model->sr); ?></div>
		<div class="general_col3"><?php echo "Connections Link"; ?></div>
		<div class="general_col4 "> <a href="<?php echo Yii::app()->createAbsoluteUrl("customers/view/".$model->id_customer);?>"> Click Here </a></div>
 
		</div>
		</div>
<script>
function changeDate(id){
	$("input#change_"+id).datepicker({ dateFormat: 'dd/mm/yy' }).datepicker( "show" );
 	$('#ui-datepicker-div').css('top',parseFloat($("input#change_"+id).offset().top) + 25.0);
 	$('#ui-datepicker-div').css('left',parseFloat($("input#change_"+id).offset().left));
}
</script>