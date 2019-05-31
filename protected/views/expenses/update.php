<div class="mytabs expenses_edit">
	<?php if (isset($_GET['returnn'])) {
		echo '<script type="text/javascript">
			$(document).ready(function(){ notUpload("'.$_GET['returnn'].'"); });
			function notUpload(mes) {
				var action_buttons = {
			        "Ok": {
						click: function() { $(this).dialog( "close" ); },
				        class : "ok_button"
			        }
				}
				custom_alert("ERROR MESSAGE", mes, action_buttons);
			}			
        </script>';
		unset($_GET['returnn']); } ?>
	<div id="expenses_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'EXPENSE HEADER');?></span>
			<a class="header_button print_butt" href="<?php echo $this->createUrl('expenses/print', array('id' => $model->id));?>">Print</a>
		<?php if ($isEditable) { ?>
			<a id="showHeader" class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('expenses/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>
			<a id="hideHeader" class="header_button" style="font-style:normal" onclick="hideHeader();return false;" href="javascript:void(0)"><?php echo Yii::t('translations', 'CANCEL');?></a>
			<a id="saveHeader" class="header_button" style="font-style:normal; color:#8d0719" onclick="saveHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('expenses/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'SAVE');?></a>
			<?php } ?>			
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<div class="hidden edit_header_content tache new"></div>
		<br clear="all" />
	</div>
	<div id="expenses_items">
		<div class="theme" style="padding-top:23px; background:none"><b><?php echo Yii::t('translations', 'DETAILS');?></b></div>

		<div class="wrapper_action" id="action_tabs_right"><div onclick="chooseActions();" class="action triggerAction"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="    margin-top: -23px;"><div class="headli"></div><div class="contentli">
						<div class="cover"><div class="li noborder delete" onclick="duplicateitem();">Duplicate Item</div></div> 
				</div><div class="ftrli"></div></div><div id="users-list" style="display:none;"></div></div>



		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array( 'id'=>'items-grid', 'dataProvider'=>$expensDetails->search(),
						'summaryText' => '', 'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
						'columns'=>array(
							array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'checkbox_grid'),'selectableRows'=>2,),
							array('header'=> Yii::t('translations', 'ITEM').'#',	'value' => function($data,$row) { return Utils::paddingCode($row+1); },
								'type'=>'raw', 'htmlOptions'=>array('class'=>'paddingl16') ),
							array('header' => 'type', 'header'=> Yii::t('translations', 'TYPE'), 'value' => '$data->type0->codelkup', 'type'=>'raw' ),
							array('header' => 'original_amount', 'header'=> Yii::t('translations', 'AMOUNT'), 'value' => 'Utils::formatNumber($data->original_amount)', 'type'=>'raw' ),
							array('header' => 'original_currency', 'header'=> Yii::t('translations', 'CURRENCY'), 'value' => '$data->currency1->codelkup', 'type'=>'raw' ),
							array('header' => 'rate', 'header'=> Yii::t('translations', 'RATE'), 'value' => '$data->currencyRate->rate', 'type'=>'raw' ),
							array('header' => 'amount', 'header'=> Yii::t('translations', 'USD AMOUNT'), 'value' => 'Utils::formatNumber($data->amount)', 'type'=>'raw' ),
							array('header' => 'billable', 'header'=> Yii::t('translations', 'BILLABLE'), 'value' => '$data->billable', 'type'=>'raw' ),
							array('header' => 'payable', 'header'=> Yii::t('translations', 'PAYABLE'), 'value' => '$data->payable', 'type'=>'raw' ),
							array( 'header' => 'date', 'header'=> Yii::t('translations', 'DATE'), 'value' => '$data->date != "0000-00-00" ? date("d/m/Y", strtotime($data->date)):""', 'type'=>'raw' ),	
							array( 'value' => '$data->getNotesGrid()', 'type'=>'raw' ),
							array(
									'class'=>'CCustomButtonColumn',
									'template'=>'{update} {delete}',
									'htmlOptions'=>array('class' => 'button-column'),
									'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
																// update amounts
											  					$.each(response.amounts, function(i, item) {
											  		    			$("#"+i).html(item);
											  					});
											  					$.fn.yiiGridView.update("terms-grid");
											  				}}',
									'buttons'=>array(						            									
						            	'update' => array(
											'label' => Yii::t('translations', 'Edit'), 
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("expenses/manageItem", array("id"=>$data->id))',
						            		'options' => array( 'onclick' => 'showItemForm(this, false);return false;' ), ),
										'delete' => array(
											'label' => Yii::t('translations', 'Delete'),
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("expenses/deleteItem", array("id"=>$data->id))',  
						                	'options' => array( 'class' => 'delete' ), ), ),
							), ))); ?>
			<?php if($isEditable){ ?>
			<div class="tache new_item">
				<div onclick="showItemForm(this, true);" class="newtask">
					<u><b>+ <?php echo Yii::t('translations', 'NEW EXPENSE ITEM');?></b></u>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="row em" style="margin-bottom:17px;">
              <div class="theme paddigl0"><b><?php echo Yii::t('translations', 'SUMMARY SECTION');?></b></div>
        </div>
        <div class="usersDiv">
              <div class="row title em">
			  <?php $branch= Expenses::getBranchByUser(Yii::app()->user->id); if ($branch!= 31){ ?>
			  	<div class="item user inline-block normal noBg paddigl0 "><?php echo Yii::t('translations', 'TOTAL AMOUNT('.Expenses::getCurrencyByBranch($branch).')');?></div>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'TOTAL AMOUNT(USD)');?></div>
			<?php  } else {?>
			<div class="item user inline-block normal noBg paddigl0"><?php echo Yii::t('translations', 'TOTAL AMOUNT(USD)');?></div>
			<?php } ?>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'TOTAL AMOUNT BILLABLE(USD)');?></div>
                <div class="item inline-block normal"><?php echo Yii::t('translations', 'TOTAL AMOUNT PAYABLE(USD)');?></div>
              </div>
              <div class="row sums em">
			  <?php 
				if ($branch!= 31){ $ratess=Expenses::getRateByBranch($branch); if($ratess == 0) { $ratess =1;} ?> 
			  <div class="item user inline-block normal paddbtm noBg paddigl0" id="total_amount_curr"><?php echo round($model->total_amount/$ratess,2) ;?></div>        
			  <div class="item inline-block normal paddbtm" id="total_amount"><?php echo Utils::formatNumber($model->total_amount) ;?></div> 			
				<?php } else {?>
				  <div class="item user inline-block normal paddbtm noBg paddigl0" id="total_amount"><?php echo Utils::formatNumber($model->total_amount) ;?></div> 		
				 <?php } ?>
                <div class="item inline-block normal paddbtm" id="billable_amount"><?php echo Utils::formatNumber($model->billable_amount) ;?></div>
                <div class="item inline-block normal paddbtm" id="payable_amount"><?php echo Utils::formatNumber($model->payable_amount) ;?></div>
              </div>
        </div>		
        <?php $form=$this->beginWidget('CActiveForm', array( 'id'=>'expenses-form-details', 'enableAjaxValidation'=>false,
			'htmlOptions' => array( 'class' => 'ajax_submit', 'enctype' => 'multipart/form-data',
				'action' => Yii::app()->createUrl("expenses/update", array("id"=>$model->id)) ), )); ?>
		<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery">
			<?php if ($isEditable) { ?>
				<?php	Yii::import("xupload.models.XUploadForm");
					$this->widget( 'xupload.XUpload', array( 'url' => Yii::app( )->createUrl("expenses/upload"),
			            'model' => new XUploadForm(), 'htmlOptions' => array('id'=>'expenses-form-details'),
						'formView' => 'small_form_exp', 'attribute' => 'file', 'autoUpload' => true, 'multiple' => true,
						'options' => array(
							'maxFileSize' => 15728640, 
							'submit' => "js:function (e, data) {
								var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer_id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer_id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."expenses".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
								var model_id = '".$model->id."';
			                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
			                    return true;
			                }", 'completed' => 'js: function(e, data) {
								if(data.jqXHR.responseText == "[{\"error\":\"full\"}]"){
									var action_buttons = {
								        "Ok": {
											click: function() { $( this ).dialog( "close" ); },
									        class : \'ok_button\'
								        }
									}
									custom_alert(\'ERROR MESSAGE\', \'You have 5 uploaded file!\', action_buttons);
								}
								console.log(data); }', ), )); ?>
			<?php } ?>	
			<div class="attachments_pic exppage"></div>
			<?php $files = $model->getFiles();
				foreach ($files as $file){ $path_parts = pathinfo($file['path']); ?>
				<div class="box template-download fade">
					<div class="title">
						<a href="<?php echo $this->createUrl('site/download', array('file' => $file['url']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
					</div>				       	
			       	<div class="size">
			        	<span><?php echo Utils::getReadableFileSize(filesize($file['path']));?></span>
			        </div>
			        <?php if ($isEditable) { ?>
					<div class="delete">
						<button class="btn btn-danger delete"
							 data-url="<?php echo $this->createUrl( "expenses/deleteUpload", array(
	                          	"id_customer" =>$model->customer_id, "model_id" => $model->id, "file" => $path_parts['basename'], ));?>" 
							data-type="POST">
						</button>
					</div>
					<?php } ?>	
				</div>
			<?php } ?>
		</div>	
		<?php $transportation= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('42','44') ")->queryScalar();
			$phone= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('46') ")->queryScalar();
			$meals= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type in ('43') ")->queryScalar();
			$misc= Yii::app()->db->createCommand("SELECT sum(amount) FROM expenses_details where expenses_id=".$model->id." and type not in ('42','44','43','46') ")->queryScalar(); ?> 	
		<?php if (isset($transportation)||isset($phone)||isset($meals)||isset($misc)){?>
			<div id="expenses_items">
		<div class="row em" style="margin-bottom:17px;">          
        </div> 
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
		<?php if ($isEditable) { ?>
			<div class="horizontalLine margint20"></div>
	        <div class="buttons">
	        	<?php echo $form->hiddenField($model,'status',array('value'=>Expenses::STATUS_SUBMITTED)); ?> 
				<div class="submit"><?php echo CHtml::submitButton(Yii::t('translations','Submit'), array('class'=>'submit', 'onclick' => 'js:checkPendingTimesheets(this);return false;')); ?></div>
			</div>
		<?php } $this->endWidget(); ?>
	</div>
</div>
<script > 

	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';	var updateItemExpensUrl = '<?php echo Yii::app()->createUrl("expenses/createItem"); ?>';

function checkPendingTimesheets(element) {
			
			$.ajax({ type: "POST", url: '<?php echo Yii::app()->createAbsoluteUrl('timesheets/checkIfPendingTimesheets');?>', dataType: "json", data: {id: modelId},
	 			success: function(data) {
	 				if (data) {
		 					if (data.status == 'success') { 
		 						window.location.replace('<?php  echo Yii::app()->createUrl('expenses/UpdateHeaderList');?>'+'?id='+modelId);
		 					}else{
	 					var action_buttons = {
						        "Ok": {
									click: function() { $( this ).dialog( "close" ); },
							        class : 'ok_button'
						        }
							}
		  				custom_alert('ERROR MESSAGE', data.message, action_buttons);		  				
		  			} } } }); }

function duplicateitem(element) {

	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('expenses/duplicateitems');?>",dataType: "json",data:  $('.checkbox_grid input').serialize(),
success: function(data) {if (data) {
	if (data.status == 'error') {
		var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}  
		custom_alert('ERROR MESSAGE', data.inv, action_buttons); 
	}else {
		$('.action_list').hide();
		$.fn.yiiGridView.update('items-grid');		
		$('#total_amount').html(data.total_amount);	
		$('#billable_amount').html(data.billable_amount);	
		$('#payable_amount').html(data.payable_amount);	
		$('#total_amount_curr').html(data.total_amount_curr);	
	}
	}}});
}


</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>