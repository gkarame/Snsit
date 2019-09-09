<?php Yii::app()->clientScript->registerScript('search', "
 $('.search-button').click(function(){  $('.search-form').toggle(); return false;});  $('.search-form form').submit(function(){  $.fn.yiiGridView.update('invoice-grid', { data:  $(this).serialize() });	return false;});");?>
<div class="search-form"  style="overflow: inherit;">
<?php  $this->renderPartial('_search',array(	'model'=> $model,)); ?></div>
<div id="popupinvoices" style="display: block;height: 130px;width: 300px;left: 320px;"> 	<div class='titre red-bold'>Invoices</div> 	<div class='closeinvoices' style="   margin-left: 300px;"> </div>	<div class='invoicescontainer'><b>Date</b><br/>
				<?php  $months = Invoices::getMonths();  $years = Invoices::getYearsGrid();
				echo CHtml::dropDownlist('months',"", $months, array('prompt'=>'','class' => 'status ','onchange'=>'changeInvoiceDatePopUp('."value".',"month");',)).CHtml::dropDownlist('years',"", $years, array('prompt'=>'','class'     => 'status marginl10','onchange'=>'changeInvoiceDatePopUp('."value".',"year");', )); ?>
			</div>	<div class='submitinvoices'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px;' ,'onclick' => 'changeStatus();return false;','id'=>'createbut')); ?>
		</div></div>
	<div id="popuptransfers"><div class='titre red-bold' id="transfernb"></div><div class='closeinvoices'> </div><div class='transfersdropdown'><b>Template</b><br/>
				<?php  $templates = Invoices::gettemplates();
				echo CHtml::dropDownlist('templates',"", $templates, array('prompt'=>' ','class' => 'status margint10'  ,'style' => ' width:200px !important;')); ?>
			</div><div class='submitinvoices'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px;' ,'onclick' => 'printTransfer();return false;','id'=>'createbut')); ?>
		</div>
	</div>
<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'invoice-grid','dataProvider'=> $model->search(),'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
		array('header'=>Yii::t('translations', 'INV#'),'value'=>' $data->renderNumber()','name' => 'invoice_number','cssClassExpression' => '($data->sentemail == 0 && $data->partner!=79 && $data->partner!=1336 && $data->status=="Printed") ? "boldw" : ""','htmlOptions' => array('class' => 'color')),
        array('name' => 'customer','value'=>'$data->renderCustomer()','headerHtmlOptions' => array('class' => 'width100'),),
        array('header'=>Yii::t('translations', 'INVOICE TITLE'),'value'=>' $data->getDescriptionGrid()','type'=>'raw'
		,'htmlOptions' => array('class' => '', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),'headerHtmlOptions' => array('class' => 'width151'),),
        array('header'=>Yii::t('translations', 'EA#'),'value'=>' $data->renderEANumber()','name' => 'id_ea','htmlOptions' => array('class' => 'color')),
        array('name' => 'net amount','value'=>'Utils::formatNumber( $data->net_amount)','htmlOptions' => array('class' => 'center'),'headerHtmlOptions' => array('class' => 'width80')),
		array('name' => 'status','value'=>' $data->status','htmlOptions' => array('class' => 'center')),
        array('name' => 'Type','value'=>'Invoices::getType( $data->type)','htmlOptions' => array('class' => 'center')),
        array('name' => 'Partner','type'  => 'raw','value'=>'Invoices::getAllPartners( $data->id, $data->partner)','htmlOptions' => array('class' => 'center')),
        array('name' => 'Share','type'  => 'raw','value'=>'(GroupPermissions::checkPermissions("financial-invoices","write"))? CHtml::textField("change", $data->sns_share,array("style"=>"width:35px;text-align:center;border:none","onClick"=>"this.select()","onkeyup"=>"changeInput(value, $data->id,5)")):(( $data->partner != Maintenance::PARTNER_SNS)?CHtml::textField("change", $data->sns_share,array("style"=>"width:35px;text-align:center","disabled"=>true)):"")',
       		'htmlOptions' => array('class' => 'center column5 item' ,'style'=>'padding-left:5px;')),
		array('name' => 'Old','type'  => 'raw','value'=>'Invoices::getOld( $data->id, $data->old)','htmlOptions' => array('class' => 'center')),
		array('name' => 'Invoice date','type'  => 'raw','value'=>'Invoices::getInvoiceDate( $data->invoice_date_month, $data->invoice_date_year, $data->id)',
        	'htmlOptions' => array('class' => 'column6 custom_date'),'headerHtmlOptions' => array('class' => 'inv_date'),),
        array('name' => 'Notes','type'=>'raw' ,'value'=>'empty($data->notes)?"":"<div class=\"invoice_notes_block\"><img src=\"https://img.icons8.com/office/16/000000/note.png\"><div class=\"invoice_notes\">".$data->notes."<div></div>"','htmlOptions' => array('class' => 'center')),),)); ?>
		<br clear="all"><br><div class="new_inv">
	<?php	if(GroupPermissions::checkPermissions('financial-invoices','write')){	echo CHtml::link(Yii::t('translation', 'New Invoice'), array('create'), array('class'=>'add-invoice add-btn')); } ?></div>
<script type="text/javascript">
 $(document).ready(function(){   $('#popupinvoices').hide();  $('#popuptransfers').hide(); hidedropdown();	});
 $(".closeinvoices").click(function(){ $('.status').val(""); $('.search-form form').submit(); $('#popupinvoices').hide(); $('#popuptransfers').hide()});
function hidedropdown(){document.getElementById('inv_type').style.visibility="hidden";}
function showdropdown(){document.getElementById('inv_type').style.visibility="visible";}
function showInvoicesDates(){ $('#popupinvoices').stop().show();}
function showTransfers(){}
function getExcel() {	//$('.action_list').hide();		window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('invoices/getExcel');?>/?");

$.ajax({type: "POST",	data: $('#search_invoice').serialize()  + '&ajax=invoice-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/getExcel');?>",
	  	dataType: "json",
	  	success: function(data) {
		  	if(data.success == 'success')
		  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcel(true)));?>";

  		}	});
}
function setInvoicesDates(){var send= $('.checkbox_grid_invoice input').serialize();}
	function printTransfer()
{var temp=document.getElementById("templates").value;var a= $("#transfernb").text().split("#");
var transfernb=a[1];if(temp)
{ $.ajax({type:"POST",url:"<?php echo Yii::app()->createAbsoluteUrl('invoices/getTransferInv');?>",dataType:"json",data: $('.checkbox_grid_invoice input').serialize(),success:function(data){if(data){ $('#popuptransfers').hide();var token=new Date().getTime();blockUIForDownload(token);window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/PrintTransfer');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids+'&template='+temp+'&checktransfer='+transfernb);}}})}}
function changeStatus(){ $.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeStatus');?>",		  	dataType: "json",
		  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) { if (data.status == 'success') {  $('.action_list').hide();  $('.status').val("");  $('#popupinvoices').hide();  $('#popuptransfers').hide();
						 $('.search-form form').submit();
				  		if(data.ok == false){ var action_buttons = {
							        "Ok": {
										click: function()
								        {
								             $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
				  			 custom_alert('ERROR MESSAGE', "Error", action_buttons);
					  	}
				  	} else { var action_buttons = {
						        "Ok": {
									click: function()
							        {
							             $( this ).dialog( "close" );
							        },class : 'ok_button'
						        }
							}
			  			 custom_alert('ERROR MESSAGE', data.message, action_buttons);
							 $('.action_list').hide(); $('#popupinvoices').hide();  $('#popuptransfers').hide();
						 $('.search-form form').submit();
					} } } }); }
function changeInvoiceDate(date,type,id){ $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeInvoiceDate');?>",dataType: "json",
data: {'value':date,'type':type,'id':id},success: function(data) {if (data) {if (data.status != 'success'){var action_buttons = {"Ok": {click: function(){ $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', data.message, action_buttons); $('.action_list').hide();}}}});}
function changeInvoiceDatePopUp(date,type){if ( $('.checkbox_grid_invoice input').serialize() != ''){send =  $('.checkbox_grid_invoice input').serialize();
 $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/ChangeInvoiceDatePoPUp');?>",dataType: "json",data: {'ids':send,'value':date,'type':type},
success: function(data) {if (data) {if (data.status != 'success'){
var action_buttons = { "Ok": { click: function(){ $( this ).dialog( "close" );}, class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', data.message, action_buttons); $('.action_list').hide();}}}});}}
function sendgo(invoices_ids){str=''; $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/sendToCustomer');?>", dataType: "json",data: {'val':invoices_ids},
success: function(data){if (data.inv == 'failed') {str='failed';var action_buttons = { "Ok": { click: function() {  $( this ).dialog( "close" );}, class : 'ok_button'
}}
 custom_alert('ERROR MESSAGE', "Please ensure that the invoice has a valid Ea", action_buttons);}else{str='ok';var action_buttons = {"Ok": {click: function()
{ $( this ).dialog( "close" );}, class : 'ok_button'}}
 custom_alert('EMAIL MESSAGE', data.nb+" invoices were sent successfully to customers", action_buttons);  $('.search-form form').submit(); } }});}
function sendToCustomer(){ $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/CheckEmail');?>",dataType: "json",data:  $('.checkbox_grid_invoice input').serialize(),
success: function(data) {if (data) {if (data.inv.length == 40) {var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'
}}
custom_alert('ERROR MESSAGE', data.inv, action_buttons); }
else if(data.inv == 'not bill') {var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}
custom_alert('ERROR MESSAGE', "Please ensure all Bill To Information is properly filled", action_buttons);
}else if (data.inv == 'Toprint') {var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', "Please ensure invoice is in status printed", action_buttons);
}else if (data.inv == 'SPAN') {var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', "Please ensure that selected invoice(s) are not for partner SPAN", action_buttons);
}else if (data.inv == 'invalid'){var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', "Please ensure invoice's ea/project are valid", action_buttons); }else if (data.inv == 'printed'){ $('.action_list').hide();sendgo(data.invoices_ids);}}}});}
function checkTransfer() { $.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/checkTransfer');?>",dataType: "json",data:  $('.checkbox_grid_invoice input').serialize(),
success: function(data) {if (data) {if (data.inv.length > 39) {var action_buttons = {"Ok": {click: function() { $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', data.inv, action_buttons);}else if (data.inv.length < 39) { $('#popuptransfers').stop().show(); $('#transfernb').text(data.inv);  }   }  }}); }
function checkPrint() { $.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/checkPrint');?>",  dataType: "json",  data:  $('.checkbox_grid_invoice input').serialize(),
success: function(data) {  if (data) {  if (data.inv.length == 40 || data.inv.indexOf("dolphin auxiliary") !=-1) {  var action_buttons = {"Ok": {click: function(){ $( this ).dialog( "close" );},class : 'ok_button'}}
 custom_alert('ERROR MESSAGE', data.inv, action_buttons);}
else if(data.inv == 'not bill') {var action_buttons = {"Ok": {click: function() {     $( this ).dialog( "close" );},class : 'ok_button'}}
custom_alert('ERROR MESSAGE', "Please ensure all Bill To Information is properly filled", action_buttons);}
else if(data.inv == 'po') {var action_buttons = {"Ok": {click: function() {     $( this ).dialog( "close" );},class : 'ok_button'}}
custom_alert('ERROR MESSAGE', "Please ensure all maintenance invoices required po are filled", action_buttons);}
else if (data.inv == 'printed') { $('.action_list').hide();var token = new Date().getTime();blockUIForDownload(token);window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/print');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids);
}else {  buttons = {"Print": {class: 'yes_button',click: function() {     $( this ).dialog( "close" );    var token = new Date().getTime();blockUIForDownload(token);
window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('invoices/print');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids);}},
"Cancel": {class: 'no_button',click: function() {     $( this ).dialog( "close" );}}}
custom_alert("PRINT MESSAGE", data.inv, buttons);}  }  }});}
function deleteInv(){ $('.action_list').hide();buttons = {"YES": {class: 'yes_button',click: function() {     $( this ).dialog( "close" );deleteInvoices();
}},"NO": {class: 'no_button',click: function() {     $( this ).dialog( "close" );}}}
custom_alert("DELETE MESSAGE", "Are you sure you want to delete these invoices?", buttons);}
var fileDownloadCheckTimer;
function blockUIForDownload(token) { $.blockUI(); fileDownloadCheckTimer = window.setInterval(function () { var cookieValue =  $.cookie('fileDownloadToken');
      if (cookieValue == token)
       finishDownload();
    }, 1000); }
function finishDownload() { window.clearInterval(fileDownloadCheckTimer);  $.removeCookie('fileDownloadToken');  $('.search-form form').submit();  $.unblockUI();}
function deleteInvoices(){
	$.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/delete');?>",  dataType: "json",  data:  $('.checkbox_grid_invoice input').serialize(),
  	success: function(data) {  if (data) {
  		if (data.status == 'success') {  $('.search-form form').submit();   }
		else if (data.status == 'fail'){	var action_buttons = {"Ok": {class: 'ok_button',click: function() {
    										$( this ).dialog( "close" );}}  }
    										custom_alert('ERROR MESSAGE', data.message, action_buttons);
 		}else if (data.status == 'halffail'){
 			 $('.search-form form').submit();
 			var action_buttons = {"Ok": {class: 'ok_button',click: function() {
    										$( this ).dialog( "close" );}}  }
    										custom_alert('ERROR MESSAGE', data.message, action_buttons);
 		}
   }  }});
}
function checkStatus(){ $.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/checkStatus');?>",  dataType: "json",  data:  $('.checkbox_grid_invoice input').serialize(),
  success: function(data) {  if (data) {  if (data.status == 'success') {   $('.deletInv').show();  }else{   $('.deletInv').hide(); }  }  }});}
function share(element){var dialog =  $('.popup_list'), send =  $('.checkbox_grid_invoice input').serialize();if (! $(element).hasClass('shareby_button')) {
send += '&'+  $(element).parents('.shareby_fieldset').serialize(); $('.loader').show();}else {dialog.removeClass('popup_shareby').hide().html('');}
var action_buttons = {"Ok": {class: 'ok_button',click: function() {     $( this ).dialog( "close" );}}  }
var dialog =  $('.popup_list');  $.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/shareAll');?>",  dataType: "json",  data: send,  success: function(data) {
  if (data) {  if (dialog.find('.loader').length) {   $('.loader').hide();  }  if (data.status == "failure") {  dialog.html(data.form);  dialog.addClass('popup_shareby');
  if (data.file_found == 0)  {
  	custom_alert('ERROR MESSAGE', 'The file was not found on the server', action_buttons);  }  dialog.show();  } else {
  if (data.status == "success") {  if (!data.not_sent_to)  {  dialog.fadeOut(100);  dialog.html('');  }  else  {
   custom_alert('ERROR MESSAGE', 'The email was not sent to '+data.not_sent_to, action_buttons);
  }} else {
  	custom_alert('ERROR MESSAGE', 'There was an error. Please try again!', action_buttons);  }  }  }}});  }
function changeInput(value,id_invoice,type){ $.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('invoices/changeInput');?>",  dataType: "json",  data: {'value':value,'id_invoice':id_invoice,'type':type},
  success: function(data) {  if (data) {  if (data.status == 'success') { if( type == 2 || type == 5){ $('.search-form form').submit(); }   }else  {  var action_buttons = {"Ok": {class: 'ok_button',click: function() {
     $( this ).dialog( "close" );}}  }
     custom_alert('ERROR MESSAGE', 'ERROR', action_buttons);    }  }  }});}
</script>

