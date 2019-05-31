<div class="mytabs expenses_edit">
	<div id="expenses_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EXPENSE HEADER');?></span>
			<a class="header_button margin_right0" href="<?php echo $this->createUrl('expenses/print', array('id' => $model->id));?>">Print</a>
			<?php if($model->status == 'Paid' || $model->status == "Invoiced"){?>
				<a class="header_button " href="<?php echo $this->createUrl('expenses/PrintBankTransfer', array('id' => $model->id));?>">Print Bank Transfer</a>
			<?php }?>
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<div class="hidden edit_header_content tache new"></div>
		<br clear="all" />
	</div>
	<div id="expenses_items">
		<div class="theme paddigl0" style="padding-top:23px;"><b><?php echo Yii::t('translations', 'DETAILS');?></b></div>
		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array( 'id'=>'items-grid', 'dataProvider'=>$expensDetails->search(),
						'summaryText' => '', 'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
						'columns'=>array(
							array('header'=> Yii::t('translations', 'ITEM').'#', 'value' => function($data,$row) { return Utils::paddingCode($row+1); },
								'type'=>'raw', 'htmlOptions'=>array('class'=>'paddingl16') ),
							array( 'header' => 'type', 'header'=> Yii::t('translations', 'TYPE'), 'value' => '$data->type0->codelkup', 'type'=>'raw' ),
							array( 'header' => 'original_amount', 'header'=> Yii::t('translations', 'AMOUNT'), 'value' => 'Utils::formatNumber($data->original_amount)', 'type'=>'raw' ),
							array( 'header' => 'original_currency', 'header'=> Yii::t('translations', 'CURRENCY'), 'value' => '$data->currency1->codelkup', 'type'=>'raw' ),
							array( 'header' => 'rate', 'header'=> Yii::t('translations', 'RATE'), 'value' => '$data->currencyRate->rate', 'type'=>'raw' ),
							array( 'header' => 'amount', 'header'=> Yii::t('translations', 'USD AMOUNT'), 'value' => 'Utils::formatNumber($data->amount)', 'type'=>'raw'),
							array( 'header' => 'billable', 'header'=> Yii::t('translations', 'BILLABLE'), 'value' => '$data->billable', 'type'=>'raw' ),
							array( 'header' => 'payable', 'header'=> Yii::t('translations', 'PAYABLE'), 'value' => '$data->payable', 'type'=>'raw'),
							array( 'header' => 'date','header'=> Yii::t('translations', 'DATE'), 'value' => 'date("d/m/Y",strtotime($data->date))', 'type'=>'raw' ),
							) )); ?>
		</div>
		<div class="row em" style="margin-bottom:17px;">
              <div class="theme paddigl0"><b><?php echo Yii::t('translations', 'SUMMARY');?></b></div>
        </div>
        <div class="usersDiv">
              <div class="row title em">
                <div class="item user inline-block normal noBg paddigl0"><?php echo Yii::t('translations', 'TOTAL AMOUNT(USD)');?></div>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'TOTAL AMOUNT BILLABLE(USD)');?></div>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'TOTAL AMOUNT PAYABLE(USD)');?></div>
              </div>
              <div class="row sums em">
                <div class="item user inline-block normal paddbtm noBg paddigl0" id="total_amount"><?php echo Utils::formatNumber($model->total_amount) ;?></div>
                <div class="item inline-block normal paddbtm" id="billable_amount"><?php echo Utils::formatNumber($model->billable_amount) ;?></div>
                <div class="item inline-block normal paddbtm" id="payable_amount"><?php echo Utils::formatNumber($model->payable_amount) ;?></div>
              </div>
        </div>
	</div>
	<div class="files exppage expview" data-toggle="modal-gallery" data-target="#modal-gallery">
		<div class="attachments_pic exppage"></div>
		<?php $files = $model->getFiles();
		foreach ($files as $file) { $path_parts = pathinfo($file['path']); ?>
		<div class="box template-download fade" id="tr0">
			<div class="title">
				<a href="<?php echo $this->createUrl('site/download', array('file' => $file['url']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
			</div>				       	
	       	<div class="size">
	        	<span><?php echo Utils::getReadableFileSize(filesize($file['path']));?></span>
	        </div>	
		</div>
		<?php } ?>
		<br clear="all" />
	</div>
	<div class="horizontalLine margint20"></div>
	<?php $perms = Groups::getExpensePermissions();	?>
	<div class="saveDiv">
	        <?php if(Expenses::STATUS_APPROVED == $model->status && $perms['write']) {
		        $form=$this->beginWidget('CActiveForm', array(
					'id'=>'expenses-form-details', 'enableAjaxValidation'=>false, 'htmlOptions' => array(
						'class' => 'ajax_submit', 'enctype' => 'multipart/form-data', 'action' => Yii::app()->createUrl("expenses/update", array("id"=>$model->id))
					), ));
				echo $form->hiddenField($model,'status',array('value'=>Expenses::STATUS_PAID));
				if (isset($_GET['option'])) { echo CHtml::Button(Yii::t('translations','Pay'), array('class'=>'pay', 'onclick' => 'pay()')); } 
				$this->endWidget();
	        }elseif(Expenses::STATUS_SUBMITTED == $model->status && ($perms['write']) ){
	        	$form=$this->beginWidget('CActiveForm', array( 'id'=>'expenses-form-details', 'enableAjaxValidation'=>false,
					'htmlOptions' => array( 'class' => 'ajax_submit', 'enctype' => 'multipart/form-data',
						'action' => Yii::app()->createUrl("expenses/update", array("id"=>$model->id)) ), )); 				
				echo $form->hiddenField($model, 'status', array('value'=>Expenses::STATUS_APPROVED)); 
				if(isset($_GET['option']))
					echo CHtml::submitButton(Yii::t('translations','Approve'), array('class'=>'approve', 'onclick' => 'approve()')); 
				$this->endWidget();
			}
			if((Expenses::STATUS_SUBMITTED == $model->status || Expenses::STATUS_APPROVED == $model->status) && $perms['write'] ) {
	        	if(isset($_GET['option']))
	        		echo CHtml::button(Yii::t('translations','Reject'), array('class'=>'reject', 'onclick' => 'reject()')); 
			} ?>
			<div class="loader" style="position:absolute;margin-left:226px;margin-top: -27px;"></div>
        <?php if (Expenses::STATUS_SUBMITTED == $model->status || Expenses::STATUS_APPROVED == $model->status) {
        	$form=$this->beginWidget('CActiveForm', array( 'id'=>'expenses-form-details', 'enableAjaxValidation'=>false,
				'htmlOptions' => array( 'class' => 'ajax_submit','enctype' => 'multipart/form-data',
					'action' => Yii::app()->createUrl("expenses/update", array("id"=>$model->id)) ), )); 
			echo $form->hiddenField($model,'status',array('value'=>Expenses::STATUS_REJECTED)); ?>
	        <div class="saveDiv1">
	            <textarea id="rejected_message" name="rejected_message"></textarea>
		        <?php echo CHtml::submitButton(Yii::t('translations','Save'), array('class'=>'save'));  ?>
	        </div>
	        <?php $this->endWidget();
		} ?>
		<?php if ((Expenses::STATUS_NEW == $model->status || Expenses::STATUS_REJECTED == $model->status) && $perms['write'] ) {
        	$form=$this->beginWidget('CActiveForm', array( 'id'=>'expenses-form-details', 'enableAjaxValidation'=>false,
			'htmlOptions' => array( 'class' => 'ajax_submit', 'enctype' => 'multipart/form-data',
				'action' => Yii::app()->createUrl("expenses/update", array("id"=>$model->id)) ), )); 
			$transportation= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('42','44') ")->queryScalar();
			$phone= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('46') ")->queryScalar();
			$meals= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('43') ")->queryScalar();
			$misc= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type not in ('42','44','43','46') ")->queryScalar();
		 if (isset($transportation)||isset($phone)||isset($meals)||isset($misc)){?>
			<div id="expenses_items">		
		<div class="row em" style="margin-bottom:17px;">
              <div class="theme paddigl0"><b><?php echo Yii::t('translations', 'TOTAL AMOUNT BY CATEGORY');?></b></div>
        </div> 
        <div class="usersDiv">
              <div class="row title em">
			  <?PHP if(isset($transportation)){?>
                <div class="item user inline-block normal noBg paddigl0"><?php echo Yii::t('translations', 'TRANSPORTATION (USD)');?></div> <?php }				
				 if(isset($phone)){?>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'PHONE & INTERNET (USD)');?></div> <?php }
				 if(isset($meals)){?>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'MEALS (USD)');?></div> <?php }
				 if(isset($misc)){?>
				  <div class="item inline-block normal"><?php echo Yii::t('translations', 'MISC (USD)');?></div> <?php } ?>
              </div>
              <div class="row sums em">
			  	  <?PHP if(isset($transportation)){?>
                <div class="item user inline-block normal paddbtm noBg paddigl0" id="total_amount"><?php echo $transportation;	?></div><?php }
				if(isset($phone)){?>				
                <div class="item inline-block normal paddbtm" id="billable_amount"><?php echo $phone ;?></div><?php }
				if(isset($meals)){?>
                <div class="item inline-block normal paddbtm" id="payable_amount"><?php echo $meals ;?></div><?php }	
				if(isset($misc)){?>				
				<div class="item inline-block normal paddbtm" id="payable_amount"><?php echo $misc ;?></div><?php }		?>
              </div>
        </div> </DIV><?php } ?>
	        <!--<div class="buttons">
	        	<?php // echo $form->hiddenField($model,'status',array('value'=>Expenses::STATUS_SUBMITTED)); ?> 
				<div class="submit"><?php //echo CHtml::submitButton(Yii::t('translations','Submit'), array('class'=>'submit', 'onclick' => 'event.preventDefault(); checkPendingTimesheets(this);')); ?></div>
			</div>-->
			<?php $this->endWidget(); ?>
		<?php }?>
		<br clear="all" />
	</div>
</div>
<script>
function createInv(){
	$.ajax({
 		type: "POST", url: '<?php echo Yii::app()->createUrl("expenses/createInvoice"); ?>', dataType: "json",
	  	data: {'expenses_id':'<?php echo $model->id;?>'},
	  	success: function(data) {
		  	if (data.status == 'success') { alert("gdfgdf"); }
			showErrors(data.error);	showErrors(data.alert); } });
	alert('ff');
}
</script>
<script type="text/javascript">
function checkPendingTimesheets(element) {
			$.ajax({
	 			type: "GET", url: '<?php echo Yii::app()->createAbsoluteUrl('timesheets/checkIfPendingTimesheets');?>', dataType: "json",
	 			success: function(data) {
		 			var count = parseInt(data) || 0;
					if (count > 1) {
						var action_buttons = {
						        "Ok": {
									click: function() { $( this ).dialog( "close" ); },
							        class : 'ok_button'
						        }
							}
		  				custom_alert('ERROR MESSAGE', 'You have more than 1 Time Sheet Pending, Cannot create a new Expense Sheet', action_buttons);
					} }	}); }
		</script>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
	var updateItemExpensUrl = '<?php echo Yii::app()->createUrl("expenses/createItem"); ?>';
	var updateExpensUrl = '<?php echo Yii::app()->createUrl("expenses/view", array('id'=>$model->id)); ?>';
	var PrintBankTransfer = '<?php echo Yii::app()->createUrl("expenses/PrintBankTransfer", array('id'=>$model->id)); ?>';
	var approval = '<?php echo Yii::app()->createUrl("expenses/approval"); ?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
