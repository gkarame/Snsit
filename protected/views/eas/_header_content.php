<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_customer')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->customer->name); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('ea_number')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->ea_number); ?></div>
</div>
	<?php 
switch ($model->category) { 
	case 454:	?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
<?php 
		break;
	case 496:	?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
<?php 
		break;
	case 623:	?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
<?php 
		break;
	case 24:	?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eAuthor->fullname); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_lpo')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->customer_lpo); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></div>
		<div class="general_col2 "><?php echo !empty ($model->created) ? date('d/m/Y', strtotime($model->created)) : ''; ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCurrency->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('expense')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getFormatExpense()); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->description); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>	
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('T&M')); ?></div>
		<div class="general_col2 "><?php if($model->TM=='1'){echo'Yes';}else {echo 'No';};  ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Primary Contact')); ?></div>
		<div class="general_col4 "><?php  if(isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'')) { echo $model->primary_contact_name ; }else{ echo CHtml::encode($model->getPrimaryContact($model->id_customer));} ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Bill To Contact Person'));  ?></div>
		<div class="general_col2 "><?php if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo CHtml::encode($model->getBillToContact($model->id_customer));} ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Bill To Address')); ?></div>
		<div class="general_col4 "><?php  if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else {echo CHtml::encode($model->getBillToAddress($model->id_customer));} ?></div>
	</div>		
<?php 
		break;
	case 25: ?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eAuthor->fullname); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_lpo')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->customer_lpo); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></div>
		<div class="general_col2 "><?php echo !empty ($model->created) ? date('d/m/Y', strtotime($model->created)) : ''; ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->description); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>
	<div class="view_row">
			<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Primary Contact')); ?></div>
			<div class="general_col2 "><?php  if(isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'')) { echo $model->primary_contact_name ; }else{ echo CHtml::encode($model->getPrimaryContact($model->id_customer));} ?></div>
	
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Bill To Contact Person'));  ?></div>
	<div class="general_col4 "><?php if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo CHtml::encode($model->getBillToContact($model->id_customer));} ?></div>
		</div>
<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Bill To Address')); ?></div>
		<div class="general_col2 "><?php  if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else {echo CHtml::encode($model->getBillToAddress($model->id_customer));} ?></div>
		</div>
<?php 
		break;
	case 26: ?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_project'));?></div>
		<?php if(isset($model->id_project)){ ?>
		<div class="general_col2 "><a  href="<?php echo Yii::app()->createUrl("projects/view", array("id" =>$model->id_project)) ?>" ><?php echo CHtml::encode($model->project_name);?></a></div>
		<?php }else{ ?>
		<div class="general_col2 "><?php echo CHtml::encode($model->project_n); ?></div>
		<?php } ?>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_lpo')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->customer_lpo); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eAuthor->fullname); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->eCurrency->codelkup); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></div>
		<div class="general_col2 "><?php echo !empty ($model->created) ? date('d/m/Y', strtotime($model->created)) : ''; ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('expense')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getFormatExpense()); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->description); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col2 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('T&M')); ?></div>
		<div class="general_col4 "><?php if($model->TM=='1'){echo'Yes';}else {echo 'No';}; ?></div>
	</div>
	<div class="view_row">		
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Primary Contact')); ?></div>
			<div class="general_col2 "><?php  if(isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'')) { echo $model->primary_contact_name ; }else{ echo CHtml::encode($model->getPrimaryContact($model->id_customer));} ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Bill To Contact Person'));  ?></div>
			<div class="general_col4 "><?php if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo CHtml::encode($model->getBillToContact($model->id_customer));} ?></div>
	</div>	
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Bill To Address')); ?></div>
		<div class="general_col2 "><?php  if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else {echo CHtml::encode($model->getBillToAddress($model->id_customer));} ?></div>
	
	</div>
<?php 	break;
	case 27: ?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('template')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Eas::getTemplateLabel($model->template)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_project'));?></div>
		<?php if(isset($model->id_project)){ ?>
		<div class="general_col2 "><a  href="<?php echo Yii::app()->createUrl("projects/view", array("id" =>$model->id_project)) ?>" ><?php echo CHtml::encode($model->project_name);?></a></div>
		<?php }else{ ?>
		<div class="general_col2 "><?php echo CHtml::encode($model->project_n); ?></div>
		<?php } ?>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_lpo')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->customer_lpo); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eAuthor->fullname); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->eCurrency->codelkup); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->description); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->created) ? date('d/m/Y', strtotime($model->created)) : ''; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('expense')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->getFormatExpense()); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>
	</div>
	<div class="view_row">		
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Primary Contact')); ?></div>
			<div class="general_col2 "><?php  if(isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'')) { echo $model->primary_contact_name ; }else{ echo CHtml::encode($model->getPrimaryContact($model->id_customer));} ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('Bill To Contact Person'));  ?></div>
			<div class="general_col4 "><?php if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo CHtml::encode($model->getBillToContact($model->id_customer));} ?></div>
	</div>	
	<div class="view_row">
		<div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('Bill To Address')); ?></div>
		<div class="general_col2 "><?php  if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else {echo CHtml::encode($model->getBillToAddress($model->id_customer));} ?></div>
		
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('T&M')); ?></div>
		<div class="general_col4 "><?php if($model->TM=='1'){echo'Yes';}else {echo 'No';}; ?></div>
	</div>
	<div class="view_row">
	<?php if($model->customization=='1'){ ?>

		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('support_percent')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->support_percent.'%'); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('support_amt')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->support_amt)); ?></div>

	<?php }else{ ?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('customization')); ?></div>
		<div class="general_col2 "><?php if($model->customization=='1'){echo'Yes';}else {echo 'No';}; ?></div>
		<?php } ?>
	</div>
<?php 
		break;
case 28: ?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCategory->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getStatusLabel($model->status)); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_project'));?></div>
<?php if(isset($model->id_project)){ ?>
		<div class="general_col2 "><a  href="<?php echo Yii::app()->createUrl("projects/view", array("id" =>$model->id_project)) ?>" ><?php echo CHtml::encode($model->project_name);?></a></div>
		<?php }else{ ?>
		<div class="general_col2 "><?php echo CHtml::encode($model->project_n); ?></div>
		<?php } ?>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('id_parent_project'));?></div>
		<div class="general_col4 "><a  href="<?php $id=Projects::getIdByName($model->parent_project); echo Yii::app()->createUrl("projects/view", array("id" =>$id)) ?>" ><?php echo CHtml::encode($model->parent_project); ?></a></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('author')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eAuthor->fullname); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('customer_lpo')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->customer_lpo); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></div>
		<div class="general_col2 "><?php echo !empty ($model->created) ? date('d/m/Y', strtotime($model->created)) : ''; ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('approved')); ?></div>
		<div class="general_col4 "><?php echo !empty ($model->approved) ? date('d/m/Y', strtotime($model->approved)) : 'No'; ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->eCurrency->codelkup); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('expense')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->getFormatExpense()); ?></div>
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->description); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('crmOpp')); ?></div>
		<div class="general_col4 "><?php if(isset($model->crmOpp)){ echo CHtml::encode($model->crmOpp);}else { echo ''; }  ?></div>	
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('T&M')); ?></div>
		<div class="general_col2 "><?php if($model->TM=='1'){echo'Yes';}else {echo 'No';}; ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Primary Contact')); ?></div>
			<div class="general_col4 "><?php  if(isset($model->primary_contact_name) && ($model->primary_contact_name <>' ') && ($model->primary_contact_name <>'')) { echo $model->primary_contact_name ; }else{ echo CHtml::encode($model->getPrimaryContact($model->id_customer));} ?></div>
	</div>	
	<!-- Add Bill to contact and address -->
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('Bill To Contact Person'));  ?></div>
		<div class="general_col2 "><?php if(isset($model->billto_contact_person)&& ($model->billto_contact_person <>' ')&& ($model->billto_contact_person <>'')){ echo $model->billto_contact_person ;}else {echo CHtml::encode($model->getBillToContact($model->id_customer));} ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('Bill To Address')); ?></div>
		<div class="general_col4 "><?php  if(isset($model->billto_address)&& ($model->billto_address <>' ')&& ($model->billto_address <>'')){ echo $model->billto_address ;}else {echo CHtml::encode($model->getBillToAddress($model->id_customer));} ?></div>
	</div>
	<div class="view_row">
	<?php if($model->customization=='1'){ ?>

		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('support_percent')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->support_percent.'%'); ?></div>
		<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('support_amt')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Utils::formatNumber($model->support_amt)); ?></div>

	<?php }else{ ?>
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('customization')); ?></div>
		<div class="general_col2 "><?php if($model->customization=='1'){echo'Yes';}else {echo 'No';}; ?></div>
		<?php } ?>
	</div>
<?php 
	break;
} ?>
<div class="horizontalLine smaller_margin"></div>