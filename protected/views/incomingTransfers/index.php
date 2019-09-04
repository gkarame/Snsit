<?php Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('it-grid', { data: $(this).serialize()	});	return false; }); "); ?>
<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array('model'=>$model,)); ?>
</div>
<div id="popuptransfers"  style="display: none;height:120px;"><div class='titre red-bold' id="transfernb"></div><div class='closeinvoices' style="top: -15px;"> </div>
<div class='transfersdropdown'><b>Template</b><br/>
				<?php  $templates = Invoices::gettemplates();
				echo CHtml::dropDownlist('templates',"", $templates, array('prompt'=>' ','class' => 'status margint10'  ,'style' => ' width:200px !important;')); ?>
			</div><div class='submitinvoices'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px;' ,'onclick' => 'printTransfer();return false;','id'=>'createbut')); ?>
		</div>
	</div>	

	<div id="recipients-list" style="display:none;top: 0%;left: 35%;    position: absolute;"></div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'it-grid','dataProvider'=>$model->search(),'summaryText' => '','emptyText' => 'No Results Found.',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
		array('header'=>Yii::t('translations', 'TR #'),'value'=>'$data->renderRequestNumber()','name' => 'it_no',
			'htmlOptions' => array('class' => 'column50'),'headerHtmlOptions' => array('class' => 'column50'),),
        array('name' => 'id_customer','header' => Yii::t('translations', 'Customer'),'value' => 'IncomingTransfers::getDescriptionGrid(IncomingTransfers::getName($data->id_customer))',
			'type'=>'raw','htmlOptions' => array('style' => 'width: 190px !important;', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),'headerHtmlOptions' => array('style' => 'width: 190px !important;'),),	

array('name' => 'partner','header' => Yii::t('translations', 'Partner'),'value' => 'Codelkups::getCodelkup($data->partner)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),	
array('name' => 'adddate','header' => 'date','value' => 'date("d/m/Y", strtotime($data->adddate))',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column110'),),		
array('name' => 'received_amount','header' => 'amount','value' => 'Utils::formatNumber($data->received_amount)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column110'),),	
array('name' => 'currency','header' => 'currency','value' => 'Codelkups::getCodelkup($data->currency)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),	
array('name' => 'offsetting','header' => 'offsetting','value' => 'IncomingTransfers::getOffsettingLabel($data->offsetting)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
array('name' => 'status','header' => 'status','value' => 'IncomingTransfers::getStatusLabel($data->status)',
			'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
		 array('name'=>'id_user','header'=>'Created By','value'=>'Users::getNameById($data->id_user)',
			'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),), )); ?>
<script src="http://malsup.github.io/jquery.blockUI.js" ></script>
<script type="text/javascript">

$( function() {
    $( "#popuptransfers" ).draggable();
  } );
$(document).ready(function(){   $('#popuptransfers').hide(); });
 $(".closeinvoices").click(function(){  $('#popuptransfers').hide()});
function showtrpop(){ 
	if ( $('.checkbox_grid_invoice input').serialize() != ''){
			$('#popuptransfers').stop().show();}else{
var action_buttons = {
					        "Ok": {
								click: function() 
						        {
						            $( this ).dialog( "close" );
						        },
						        class : 'ok_button'
					        }
						}
		  			custom_alert('ERROR MESSAGE', 'No Transfer(s) selected', action_buttons);
	}
}
function blockUIForDownload(token) { $.blockUI(); fileDownloadCheckTimer = window.setInterval(function () { var cookieValue =  $.cookie('fileDownloadToken');
      if (cookieValue == token)
       finishDownload(); 
    }, 1000); }
function finishDownload() { window.clearInterval(fileDownloadCheckTimer);  $.removeCookie('fileDownloadToken');  $('.search-form form').submit();  $.unblockUI();}
function printTransfer()
{		
	var temp=document.getElementById("templates").value;
	var transfernb='001';
	if(temp)
	{ 
		$.ajax({type:"POST",url:"<?php echo Yii::app()->createAbsoluteUrl('invoices/getTransferInv');?>",dataType:"json",data: $('.checkbox_grid_invoice input').serialize(),
			success:function(data){
				if(data){ 
					if(data.count == 1)
					{
						$('#popuptransfers').hide();//var token=new Date().getTime();blockUIForDownload(token);
						window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/PrintTransfer');?>'+'?&checkinvoice='+data.TR_ids+'&template='+temp+'&checktransfer='+transfernb);
							//	window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('invoices/PrintReceivables');?>/?"+$('.checkbox_grid_invoice input').serialize());

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
		  				custom_alert('ERROR MESSAGE', 'Please select one Transfer', action_buttons);
						}
				}
			}
		})
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
		  			custom_alert('ERROR MESSAGE', 'Template must be selected', action_buttons);
	}
}

function getRecipients() {
	if (!$('#recipients-list').is(':visible')) {
		$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/GetUnssignedInvoices');?>", 
		  	dataType: "json",  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") { 		
		  			$('#recipients-list').html(data.div); 		$('.action_list').hide(); 
		  			$('#recipients-list').show(); 		$('#recipients-list').find('.scroll_div').mCustomScrollbar();
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
			  	} }	});	
	} else { 
		$('#recipients-list').fadeOut(100);	
	}
}
function assignrecipients() {
	if ($('#unassigned-recipients-form').serialize() != '') {
		$.ajax({
			type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/assignInvoices');?>", 
		  	dataType: "json",data:  $('#unassigned-recipients-form').serialize()+'&'+$('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
		  		if (data.status == "success") {		
		  			$('#recipients-list').fadeOut(100);	UncheckAll(); 
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
			  	}  }	});
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
			custom_alert('ERROR MESSAGE', 'You have to select at least one invoice to save!', action_buttons);
	} 
}

function UncheckAll () { $('[id^="checkinvoice"]').attr('checked',false);	 }

function getExcel() {	

		//	$('.action_list').hide(); window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/getExcel');?>/?"); 

$.ajax({type: "POST",	data:$('.checkbox_grid_invoice input').serialize() + '&' +  $('#search_incomingTransfers').serialize()+ '&ajax=incomingTransfers-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/getExcel');?>", 
	  	dataType: "json",
	  	success: function(data) {
		  	if(data.success == 'success')
		  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";
		  	
  		}	});
		}

</script>