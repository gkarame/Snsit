<?php
Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false; }); $('.search-form form').submit(function(){
	$.fn.yiiGridView.update('receivables-grid', {	data: $(this).serialize() });	return false; }); "); ?>
<div class="search-form" style="overflow: inherit;"><?php $this->renderPartial('_search',array('model'=>$model,));?></div>


<div id="popupinv" > <div class='titre red-bold'>New Incoming Transfer</div> 	<div class='closetandm2'> </div><div class='maintdatescontainer'>					
	<div class="row ">	<?php  echo "Amount"; ?>	<div class="inputBg_create">
			<?php  echo CHtml::activeTextField($model,"net_amount", array('class' => '', 'style'=>'')); ?> 
			<?php echo CCustomHtml::error($model,'net_amount');  ?>	</div></div>	
<div class="row " style="    margin-left: 40%;    margin-top: -12%;">
		<?php echo "Currency"; ?>	<div class="inputBg_create">
			<?php echo CHtml::activeDropDownList($model,"currency",Codelkups::getCodelkupsDropDown('currency'), array('prompt'=>'','style'=>'margin-top:3px; width: 32%;','onchange'=>'validaterate(this);')); 	?>
			<?php echo  CCustomHtml::error($model,'currency'); ?>
		</div>	</div>


		<div class="row ">	<?php  echo "Bank Charges ($)"; ?>	<div class="inputBg_create">
			<?php  echo CHtml::activeTextField($model,"partner_amount", array('class' => '', 'style'=>'')); ?> 
			<?php echo CCustomHtml::error($model,'partner_amount');  ?>	</div></div>

			<div class="row " style="    margin-left: 40%;    margin-top: -12%;">
		<?php echo "Offsetting"; ?>	<div class="inputBg_create">
			<?php echo CHtml::activeDropDownList($model,"notes",IncomingTransfers::getOffsettingList(), array('prompt'=>'','style'=>'margin-top:3px; width: 32%;')); 	?>
			<?php echo  CCustomHtml::error($model,'notes'); ?>
		</div>	</div>

		<div class="row " >
		<?php echo "Bank"; ?>	<div class="inputBg_create">
			<?php echo CHtml::activeDropDownList($model,"payment",Codelkups::getCodelkupsDropDown('bank_code'), array('prompt'=>'','style'=>'margin-top:3px; width: 37%;')); 	?>
			<?php echo  CCustomHtml::error($model,'payment'); ?>
		</div>	</div>

		<div class="row " style="    margin-left: 40%;    margin-top: -11%;">
		<?php echo "Auxiliary"; ?>	<div class="inputBg_create">
			<?php echo CHtml::activeDropDownList($model,"payment_procente",Codelkups::getCodelkupsDropDown('aux_code'), array('prompt'=>'','style'=>'margin-top:3px; width: 32%;')); 	?>
			<?php echo  CCustomHtml::error($model,'payment_procente'); ?>
		</div>	</div>

		<div class="row hidden" style="margin-top: -31%;   padding-left: 65%;" id="rate">	<?php  echo "Rate"; ?>	<div class="inputBg_create">
			<?php  echo CHtml::activeTextField($model,"gross_amount", array( 'style'=>'width:60%')); ?> 
			<?php echo CCustomHtml::error($model,'gross_amount');  ?>	</div></div>	

	</div> 
	<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-top:-5px !important;margin-left:135px;' ,'onclick' => 'createTransferInvoices();return false;','id'=>'createbut')); ?>
				<span class="cancel" style="cursor: pointer;font-size:13px;margin-left:-15px !important;" onclick="$('#popupinv').fadeOut();">CANCEL</span></div>
	</div>



<?php switch(true){		case (isset($_GET['group']) && $_GET['group'] == 'customer'):
			$this->widget('ext.groupgridview.GroupGridView', array(	'id' => 'receivables-grid',	'dataProvider' => $model->searchReceivablesGr('customer.name'),
				'extraRowColumns' => array('id_customer'),'extraRowExpression' => '$data->customer->name','summaryText' => '','enablePagination' => false,
			    'template'=>'{items}','rowCssClassExpression' => '"test"','columns'=>array(
						array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice check_invoice'),'selectableRows'=>2,),
						array('header' => 'Invoice #','value' => '$data->final_invoice_number == NULL ? CHtml::link($data->snsapj_partner_inv, Yii::app()->createUrl("receivables/view",array("id"=>$data->id))): CHtml::link($data->final_invoice_number, Yii::app()->createUrl("receivables/view",array("id"=>$data->id)))',												'type'=>'raw',
								'name' => 'final_invoice_number','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
						array('name' => 'customer','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables">customer</a>',
								'value'=>'$data->customer->name','htmlOptions' => array('class' => 'column65 customer_name'),'headerHtmlOptions' => array('class' => 'column65 customer_name'),),
						array('header'=>Yii::t('translations', 'Net Amount'),'value' => 'Utils::formatNumber($data->net_amount)." ".$data->rCurrency->codelkup',	'htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('header'=>Yii::t('translations', ' Gross Amount'),'value' => 'Utils::formatNumber($data->gross_amount)." ".$data->rCurrency->codelkup',
								'htmlOptions' => array('class' => 'column75'),'headerHtmlOptions' => array('class' => 'column75'),),							
						array('name' => 'status','value'=>'$data->getstatus()','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner','value'=>'$data->rPartner->codelkup','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner_status','value'=>'($data->partner==77)? "":$data->partner_status','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner_inv','value'=>'Receivables::getpartinv($data->final_invoice_number,$data->rPartner->codelkup, $data->old, $data->partner_inv)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				        array('name' => 'printed_date','value' => 'date("d/m/Y", strtotime($data->printed_date))','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
						array('name' => 'age','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables?group=age">age</a>','value' => '$data->age','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),),));
			break;
			case (isset($_GET['group']) && $_GET['group'] == 'age'):
				$this->widget('ext.groupgridview.GroupGridView', 
					array('id' => 'receivables-grid','dataProvider' => $model->searchReceivablesGr('textdays'),'extraRowColumns' => array('textdays'),'extraRowExpression' => '$data->textdays','summaryText' => '','enablePagination' => false,'template'=>'{items}','rowCssClassExpression' => '"test"','columns'=>array(
						array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice check_invoice'),'selectableRows'=>2,),
						array('header' => 'Invoice #',
'value' => '$data->final_invoice_number == NULL ? CHtml::link($data->snsapj_partner_inv, Yii::app()->createUrl("receivables/view",array("id"=>$data->id)) ,array("title"=>($data->invoice_title)): CHtml::link($data->final_invoice_number, Yii::app()->createUrl("receivables/view",array("id"=>$data->id)))',												'type'=>'raw',
'name' => 'final_invoice_number','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
						array('name' => 'customer','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables?group=customer">customer</a>','value'=>'$data->customer->name','htmlOptions' => array('class' => 'column65 customer_name'),'headerHtmlOptions' => array('class' => 'column65 customer_name'),),
						array('header'=>Yii::t('translations', 'Net Amount'),'value' => 'Utils::formatNumber($data->net_amount)." ".$data->rCurrency->codelkup','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('header'=>Yii::t('translations', ' Gross Amount'),'value' => 'Utils::formatNumber($data->gross_amount)." ".$data->rCurrency->codelkup',
							'htmlOptions' => array('class' => 'column75'),'headerHtmlOptions' => array('class' => 'column75'),),							
						array('name' => 'status','value'=>'$data->getstatus()','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner','value'=>'$data->rPartner->codelkup','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner_status','value'=>'($data->partner==77)? "":$data->partner_status','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'partner_inv','value'=>'Receivables::getpartinv($data->final_invoice_number,$data->rPartner->codelkup,$data->old, $data->partner_inv)',
							'htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
						array('name' => 'printed_date','value' => 'date("d/m/Y", strtotime($data->printed_date))','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),
						),array('name' => 'age','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables">age</a>','value' => '$data->age','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
						array('name' => 'textdays','value' => '$data->textdays','htmlOptions' => array('class' => 'hidden'),'headerHtmlOptions' => array('class' => 'hidden'),),),));
				break;
		default:
			$this->widget('zii.widgets.grid.CGridView', array('id'=>'receivables-grid',	'dataProvider'=>$model->searchReceivablesGr(),
			'summaryText' => '','pager'=> Utils::getPagerArray(),    'template'=>'{items}{pager}',	'columns'=>array(
				array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice check_invoice'),'selectableRows'=>2,'name' => 'invoice_number'),
				array('header' => 'Invoice #','value' => '$data->final_invoice_number == NULL ? CHtml::link($data->snsapj_partner_inv, Yii::app()->createUrl("receivables/view",array("id"=>$data->id)),array("title"=>($data->invoice_title))): CHtml::link($data->final_invoice_number, Yii::app()->createUrl("receivables/view",array("id"=>$data->id)),array("title"=>($data->invoice_title)))',				
'type'=>'raw','name' => 'final_invoice_number','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
				array('name' => 'customer','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables?group=customer">customer</a>','value'=>'$data->customer->name','htmlOptions' => array('class' => 'column65 customer_name'),'headerHtmlOptions' => array('class' => 'column65 customer_name'),),
				array('header'=>Yii::t('translations', 'Net Amount'),'value' => 'Utils::formatNumber($data->net_amount)." ".$data->rCurrency->codelkup','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
				array('header'=>Yii::t('translations', ' Gross Amount'),'value' => 'Utils::formatNumber($data->gross_amount)." ".$data->rCurrency->codelkup','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
				array('name' => 'status','value'=>'$data->getstatus()','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				array('name' => 'partner','value'=>'$data->rPartner->codelkup','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				array('name' => 'partner_status','value'=>'($data->partner==77)? "":$data->partner_status','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				array('name' => 'partner_inv','type'=>'raw','value'=>'Receivables::getpartinvBox($data->final_invoice_number,$data->rPartner->codelkup,$data->old, $data->partner_inv)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		        array('name' => 'Inv Date','value' => 'date("d/m/Y", strtotime($data->getinvdate()))','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
				array('name' => 'Paid Date','value' => '$data->paid_date != "0000-00-00" ? date("d/m/Y", strtotime($data->paid_date)) :"" ','htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
				array('name' => 'age','value' => '$data->age','header' => '<a href="'.Yii::app()->getBaseUrl(true).'/receivables?group=age">age</a>','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				array('name' => 'Ea','type'=>'raw','value'=>'Receivables::getEASerInvoice($data->id_ea)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
				
				),)); } ?>
<script type="text/javascript">
$( function() {
    $( "#popupinv" ).draggable();
  } );
	var isSubmitted = false;
	$(".closetandm2").click(function() { $('.action_list').hide();	 	$('#popupinv').fadeOut();	});
	$(document).ready(function() {
		$('#popupinv').hide();
		$(document).on('click', '.plus-minus', function() {
			if ($(this).attr('data-collapsed') == 1){
				$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');	$(this).parents('.project_thead').nextUntil('.project_thead').hide();
			}else{
				$(this).attr('data-collapsed', '1').css('background-position', '0px -22px'); $(this).parents('.project_thead').nextUntil('.project_thead').show();
			}	});
		collapseOrNot();hidedropdown(); });
function hidedropdown(){document.getElementById('inv_type').style.visibility="hidden";}
	function UpdatePartner(v, final, partner, old){
		v=v.target.value;
		$.ajax({
			type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/updatePartnerInv');?>",
			dataType: "json",data: {'value':v,'final':final,'partner':partner, 'old': old},
	  		success: function(data) {
		  	if (data) {	
		  		if (data.status=='success') {	
		  			 $('.search-form form').submit();
		  		}  	
		  	}	
		  }	
		});
	}	
	function collapseOrNot(){
		$('.plus-minus').each(function(index){	$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');		$(this).parents('.project_thead').nextUntil('.project_thead').hide();	}); }
	function assignUsers() {
		if ($('#assign-users-form').serialize() != '') {
			$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/assignUsers');?>",dataType: "json",
			  	data:  $('#assign-users-form').serialize()+'&'+$('.checkbox_grid_invoice input').serialize(),
			  	success: function(data) {
			  		if (data.status == "success") {
			  			$("input[type='checkbox']").prop('checked', false);	 	 $('.search-form form').submit();		$('#users-list').fadeOut(100);		}
			  		else{
			  			var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
						custom_alert('ERROR MESSAGE', data.message, action_buttons);
			  		}	}	});
		} else {
			var action_buttons = {
			        "Ok": {
						click: function() 
				        {
				            $( this ).dialog( "close" );
				        },
				        class : 'ok_button'
			        }
				}
				custom_alert('ERROR MESSAGE', 'You have to select a user in order to save!', action_buttons);
		} }	

		function createTransferInvoices(){
			var amount=$("#Receivables_net_amount").val();
			var cur=$("#Receivables_currency").val();
			var bank= $("#Receivables_partner_amount").val();
			var off=$("#Receivables_notes").val();
			var dolphinbank= $("#Receivables_payment").val();
			var dolphinaux=  $("#Receivables_payment_procente").val();

			 
			
			if(document.getElementById('rate').classList.contains("hidden"))
			{
			var rate= "1";

			}else
			{
			var rate= $("#Receivables_gross_amount").val();

			}
			//alert(typeof amount);
	//		alert(cur);
			//( typeof bank);

			if(typeof  amount == 'undefined' || typeof  rate == 'undefined' || typeof cur =='undefined' || typeof bank == 'undefined' || typeof off == 'undefined'  || typeof dolphinbank == 'undefined' || typeof dolphinaux == 'undefined' || amount.trim() == '' || rate.trim() == '' || cur.trim() == '' || bank.trim() == ''  || off.trim() == '' || dolphinbank.trim() == ''  || dolphinaux.trim() == '' )				
			{
						var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', 'All Values must be specified!', action_buttons);
			}else if( amount.match(/^[0-9.]+$/) == null || bank.match(/^[0-9.]+$/) == null )
			{
						var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', 'Received Amount and Bank Charges must be Numeric Values! ', action_buttons);
			}
			else{

				$.ajax({type: "POST",	data: $('.checkbox_grid_invoice input').serialize()  + '&ajax=receivables-form&amt='+amount+'&curr='+cur+'&bank='+bank+'&off='+off+'&rate='+rate+'&dbank='+dolphinbank+'&daux='+dolphinaux,	
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/createTransfer');?>", dataType: "json",  	success: function(data) {
				  	if(data.status == 'success'){ $('#popupinv').fadeOut(); 
					  	var action_buttons = {
							        "Ok": {
								        	class: 'ok_button',
								        	click: function() 
									        {
									            $( this ).dialog( "close" );
									        }
							        }
					  		}
					  		//$.fn.yiiGridView.update('receivables-grid');
					  		var mesg ="TR#" +data.tr+" has been successfully created";
					  		custom_alert('ERROR MESSAGE', mesg, action_buttons);
				  }else {
				  		var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message, action_buttons);
				  	} } });

			}
		}

	function getUsers() {
		if ($('.checkbox_grid_invoice input').serialize() != '') {
			if (!$('#users-list').is(':visible')) {
				$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/getUsers');?>",dataType: "json",
				  	data: $('.checkbox_grid_invoice input').serialize(),
				  	success: function(data) {
				  		if (data.status == "success") {
					  		$('#users-list').html(data.div); 		$('.action_list').hide(); 		$('#users-list').show(); 		$('#users-list').find('.scroll_div').mCustomScrollbar();
					  	} 		}	});
			} else {	$('#users-list').fadeOut(100);	}
		}else{
			var action_buttons = {
			        "Ok": {
						click: function() 
				        {
				            $( this ).dialog( "close" );
				        },
				        class : 'ok_button'
			        }
				}
				custom_alert('ERROR MESSAGE', 'You have to select at least one invoice!', action_buttons);
		}	}	
	function changeStatus() {
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeStatus');?>",  	dataType: "json",  	data: $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) {  	if (data.status == 'success') {		$('.action_list').hide(); 		$.fn.yiiGridView.update('invoice-grid');
				  	} else {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
							$('.action_list').hide();
					}  	} 		}	});	}	
	function printReceivables() {
		if ($('.checkbox_grid_invoice input').serialize() != '') {	
			$('.action_list').hide();
			window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('invoices/PrintReceivables');?>/?"+$('.checkbox_grid_invoice input').serialize());
		} else {
			$('.action_list').hide();
	  		var action_buttons = {
		        "Ok": {
					click: function() 
			        {
			            $( this ).dialog( "close" );
			        },
			        class : 'ok_button'
		        }
			}
  			custom_alert('ERROR MESSAGE', "You have to select at least one invoice!", action_buttons);
		}	} 
	function getExcel() {	
		//$('.action_list').hide();		window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('receivables/getExcel');?>/?");	


$.ajax({type: "POST",	data: $('#search_receivable').serialize()  + '&ajax=receivable-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/getExcel');?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if(data.success == 'success')
		  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";
		  	
  		}	});


	}
function getReport() {	$('.action_list').hide();	window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('receivables/getReport');?>/?");	}
	function changeStatusSNSPaid() {
		if ($('.checkbox_grid_invoice input').serialize() != '') {
			$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/changeStatusSNSPaid');?>",dataType: "json",data: $('.checkbox_grid_invoice input').serialize(),
			  	success: function(data) {
				  	if (data) {
				  		$('.action_list').hide();
					  	if (data.status == 'success') {
					  		$("input[type='checkbox']").prop('checked', false);
					  		if (!data.no_change){  $('.search-form form').submit(); }
					  	} else {
					  		var action_buttons = {
							        "Ok": {
										click: function() 
								        {
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
				  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
						}  	}	}	});
		} else {
			$('.action_list').hide();
			var action_buttons = {
				"Ok": {
					click: function() 
			        {
			            $( this ).dialog( "close" );
			        },
			        class : 'ok_button'
		        }
			}
  			custom_alert('ERROR MESSAGE', "You have to select at least one invoice!", action_buttons);
		}	}	

function validaterate(element){
	$this = $(element);	var val = $this.val();
	var url = "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/validateRate');?>";
	

				$.ajax({type: "POST",	data: $('.checkbox_grid_invoice input').serialize()  + '&ajax=receivables-form&curr='+val,	
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/validateRate');?>", dataType: "json",  	success: function(data) {
				  	if(data.status == 'success'){ 
				  					$('#rate').addClass('hidden');
				  	 }else {
				  					$('#rate').removeClass('hidden');
				  	} } });
	} 

	function showTransfer()
	{

		$("#Receivables_net_amount").val('');
		$("#Receivables_currency").val('');
		 $("#Receivables_partner_amount").val('');
			$("#Receivables_notes").val('');
			$("#Receivables_gross_amount").val('');

		$.ajax({ type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/inputInv');?>",dataType: "json",  	data: $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) { 
		  			if (data) { if(data.status=='success'){ 
		  				$('#rate').addClass('hidden'); $('#popupinv').stop().show(); 
						$("#Receivables_payment").val(data.bank);
						$("#Receivables_payment_procente").val(data.aux);

			}else{
					var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
				  		custom_alert('ERROR MESSAGE', data.message, action_buttons);
		  			} }}
		  		}); 
		$('.action_list').hide();	
	}




	function changeStatusPaid() {
		if ($('.checkbox_grid_invoice input').serialize() != '') {
			$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/changeStatusPaid');?>",dataType: "json",data: $('.checkbox_grid_invoice input').serialize(),
			  	success: function(data) {
				  	if (data) {
				  		$('.action_list').hide();
					  	if (data.status == 'success') {
					  		$("input[type='checkbox']").prop('checked', false);
					  		if (!data.no_change){ $('.search-form form').submit();	}
					  	} else {
					  		var action_buttons = {
							        "Ok": {
										click: function() 
								        {
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
				  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
						} } } });
		} else {
			$('.action_list').hide();
			var action_buttons = {
				"Ok": {
					click: function() 
			        {
			            $( this ).dialog( "close" );
			        },
			        class : 'ok_button'
		        }
			}
  			custom_alert('ERROR MESSAGE', "You have to select at least one invoice!", action_buttons);
		}	}	
	function changeInvoiceDate(date,type,id) {
		$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeInvoiceDate');?>",dataType: "json",  	data: {'value':date,'type':type,'id':id},
		  	success: function(data) {
			  	if (data) {  	if (data.status == 'success') {	$.fn.yiiGridView.update('invoice-grid'); 	} else {
				  		var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
						$('.action_list').hide();
					}  	}	}	}); }
	function getTemplate(val)	{
		if(val != ''){
			$.ajax({type: "POST",  	dataType: "json", url : configJs.urls.baseUrl + '/projects/GetCurrencyRate/'+val,
			  	success: function(data) {
			  		if (data) {  	$('#ShareByForm_body').val(data.template); 	}	}		});		}	}
	function showdropdown()	{		document.getElementById('inv_type').style.visibility="visible";	}	
	function shareReceivables(element) {
		if (isSubmitted == false) {
			isSubmitted = true;	var send = $('.checkbox_grid_invoice input').serialize();	var dialog = $('.popup_list');
			if (!$(element).hasClass('shareby_button')) {
				send += '&'+ $(element).parents('.shareby_fieldset').serialize()+'&ShareForm[footer]='+$('#ShareByForm_footer').html();
				$('.loader').show();
			}else {		dialog.removeClass('popup_shareby').hide().html('');	}			
			var action_buttons = {
			        "Ok": {
				        	class: 'ok_button',
				        	click: function() 
					        {
					            $( this ).dialog( "close" );
					        }
			        }
	  		}			
			$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('receivables/share');?>", 	dataType: "json", 	data: send,
			  	success: function(data) {
			  		if (data) {
			  			isSubmitted = false;		  			
			  			if (dialog.find('.loader').length) {	$('.loader').hide();	}
				  		if (data.status == "failure") {
				  			dialog.html(data.form);
				  			dialog.addClass('popup_shareby');
				  			if (data.file_found == 0){	custom_alert('ERROR MESSAGE', 'The file was not found on the server', action_buttons);	}
				  			dialog.show();
				  		} else {
				  			if (data.status == "success") {
				  				if (!data.not_sent_to){
					  				dialog.fadeOut(100);
					  				dialog.html('');
				  				}else{ custom_alert('ERROR MESSAGE', 'The email was not sent to '+data.not_sent_to, action_buttons); }
				  			} else {
				  				if (data.status == "fail") {	custom_alert('ERROR MESSAGE', data.message, action_buttons);
						  		}else{	custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons); }
				  			} 		} 		} 		},
				error: function() {
					isSubmitted = false;
					if (dialog.find('.loader').length) {	$('.loader').hide(); 			}
					custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);
				}	});	}	}
	function getSoAFiles(){
		if (!$('.soaBtn').hasClass('checked')){
			$('.soaBtn').addClass('checked');
			$.ajax({type: "POST",dataType: "json",data :$('.checkbox_grid_invoice input').serialize(),url : configJs.urls.baseUrl + '/receivables/getSoA/',
			  	success: function(data) {
			  		if (data && data.file) {  	$('.attachments').append(data.file);
			  		} else {
			  			var action_buttons = {
						        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
						        }
				  		}
			  			custom_alert('ERROR MESSAGE', 'The file was not found on the server', action_buttons);
			  			$('.soaBtn').removeClass('checked');
			  		} }	});	}else{
			$('.soaBtn').removeClass('checked');	$('.attachments .soaFile').remove();	}	}
	function checkStatus(){}
</script>

