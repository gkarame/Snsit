<div id="popuptandm" style="width:700px"> <div class='titre red-bold'>Invoices</div> <div class='closetandm' style="margin-left: 700px !important;"> </div>		 
			<div class='tandratecontainer'></div> 		
			<div class='submitandm'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:135px;margin-top: 13px;' ,'onclick' => 'submitOffsets();','id'=>'createbut')); ?>
				<img src="<?php echo Yii::app()->getBaseUrl().'/images/loader.gif';?>" id="img"  style="display:none;padding-top:5px;width:20px;height:20px;"/ ></div></div>

<div class="mytabs ir_edit">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'tr-form','enableAjaxValidation'=>false,
		'htmlOptions' => array('class' => 'ajax_submit','enctype' => 'multipart/form-data',),	)); ?>
	<div id="ir_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'TR HEADER');?></span>			
<?php if(GroupPermissions::checkPermissions('financial-incomingTransfers', 'write') && $model->status==1){ ?>
		<!--	<a class="header_button" onclick="getCustomers();">Add Customer</a> -->
			<a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>	
		<?php } ?>
		</div>
		<div class="header_content tache">
			<?php $this->renderPartial('_header_content', array('model' => $model));?>
		</div>
		<div class="hidden edit_header_content tache new"></div>
		<br clear="all" />
	</div>
<div class="horizontalLine smaller_margin"></div>
<br>
	<div id="ir_products">
	<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'TR INVOICES');?></span>
			<?php if($model->status == 1){?> <a class="header_button" onclick="showInvoices(<?php echo  $model->id;?>);" >Add Invoices</a><?php }?>
		</div>
		<div id="ir_products_content" class="border-grid grid">
			<?php	$buttons = array();		$tmp = '';
			if ($can_modify && GroupPermissions::checkPermissions('financial-incomingTransfers', 'write')) {
			$tmp = '{update}{delete}';
		//	$tmp = '{delete}';
			$buttons = array(
	            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,
				 		'url' => 'Yii::app()->createUrl("IncomingTransfers/manageInvoice", array("id"=>$data->id))',
 					'options' => array('onclick' => 'showProductForm(this);return false;'),),
					'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("IncomingTransfers/deleteInvoice", array("id"=>$data->id))',  
	                	'options' => array('class' => 'delete',),
					),   );
		}
 $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid','dataProvider'=>IncomingTransfers::getInvoicesProvider($model->id),
                'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
                'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
                'columns' => array(
        array('header'=>Yii::t('translations', 'final#'),'name' => 'final_invoice_number','value' => '$data->renderfinal_invoice_number()',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => 'width180'),),
        array('header'=>Yii::t('translations', 'invoice#'),'name' => 'invoice_number','value' => '$data->invoice_number',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => ''),),
        array('header'=>Yii::t('translations', 'original / partial amt'),'name' => 'original_amount','value' => 'Utils::formatNumber($data->original_amount).\' / \'.Utils::formatNumber(IncomingTransfersDetails::getPaidPerInvoice($data->invoice_number))',
            'htmlOptions' => array('class' => 'width245'),          'headerHtmlOptions' => array('class' => 'width245'),),
        array('header'=>Yii::t('translations', 'original currency'),'name' => 'original_currency','value' => 'Codelkups::getCodelkup($data->original_currency)',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => 'width245'),),
        array('header'=>Yii::t('translations', 'paid amt'),'name' => 'paid_amount','value' => 'IncomingTransfersDetails::getPaidLabel($data->paid_amount)',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => 'column150'),),
		array('header'=>Yii::t('translations', 'received amt'),'name' => 'received_amount','value' => 'Utils::formatNumber($data->received_amount)',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => 'width171'),),
		array('header'=>Yii::t('translations', 'received currency'),'name' => 'received_currency','value' => 'Codelkups::getCodelkup($data->received_currency)',
            'htmlOptions' => array('class' => 'column150'),          'headerHtmlOptions' => array('class' => 'width245'),),
		array('class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons, ), ),	));
		if(GroupPermissions::checkPermissions('financial-incomingTransfers', 'write')){	?>
			<div class="tache new_item" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
				<div onclick="showProductForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'ADD INVOICE');?></b></u></div>
			</div>	<div>	</div>	<?php } ?>	</div>	</div>
	<br/><br/>
	<div class="horizontalLine smaller_margin"></div>		
		</div>
	<fieldset id="sub">
		<input type="hidden" name="submitted" value="1" />			
	</fieldset>
	<div class="row buttons">
		<div class="save"></div>
	</div>
	<?php $this->endWidget();?>
</div>
<script>
$(document).ready(function(){	$('#popuptandm').hide();});
$(".closetandm").click(function() { $('#popuptandm').hide(); });
function showInvoices(tr){
//$('#popuptransfers').stop().show();
		$.ajax({
	 		type: "POST",
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/GetInvoices');?>", 
		  	dataType: "json",
		  	data: 'tr= '+tr,
		  	success: function(data) {
		  		if (data) {
		  			if(data.status=='success'){
		  				 $('.tandratecontainer').html(data.rate_table); $('#popuptandm').stop().show();
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
				  		custom_alert('ERROR MESSAGE', 'TR doesn\'t have eligible invoices', action_buttons);
		  			}	}}});		  	
}
function showProductForm(element, newItem) {
		var url;
		if (newItem) { url = "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/manageInvoice');?>";
		} else { url = $(element).attr('href');	}
		$.ajax({ type: "POST", url: url, dataType: "json", data: {'id_it':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) { $('.new_item').hide(); $('.new_item').after(data.form);
					  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>'); 	}
				  	}
			  	}	}	});
	}
	function updateProduct(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/manageInvoice');?>";
		if (id != 'new'){ url += '/'+parseInt(id); }
		$.ajax({ type: "POST",
	 		data: $(element).parents('.new_product').serialize() + '&IncomingTransfersDetails['+id+'][id_it]=<?php echo $model->id;?>' + '&id_it=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') { $(element).parents('.tache.new').remove(); $('.new_item').show(); $.fn.yiiGridView.update('items-grid'); }	
				  		$.fn.yiiGridView.update('items-grid');			
				  	} else {
				  		if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); }
				  	} } } });
	}
	function updateHeader(element) {
		$.ajax({ type: "POST", data: $('#header_fieldset').serialize()  + '&ajax=tr-form',					
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('IncomingTransfers/updateHeader', array('id' => $model->id));?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html); $('.header_content').removeClass('hidden'); $('.edit_header_content').addClass('hidden');
				  		
					  	if (data.can_modify == false) { modeNotEditable();	document.getElementsByClassName('header_button')[0].setAttribute("class", 'hidden'); 
					  	} else { modeEditable(); }
				  	} else {
				  		if (data.status == 'success' && data.html) { $('.edit_header_content').html(data.html); }
				  	}
				  	showErrors(data.error);				  	
			  	}	}	});
	}
	function modeNotEditable() { $.fn.yiiGridView.update('items-grid'); $('.tache.new_item').hide();	}
	function modeEditable() {	$.fn.yiiGridView.update('items-grid');	$('.tache.new_item').show();	}	
	function showHeader(element){
		var url = $(element).attr('href');
		$.ajax({ type: "POST", url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
						$('.edit_header_content').html(data.html); $('.edit_header_content').removeClass('hidden'); $('.header_content').addClass('hidden');
				  		if(data.state!=1) {  document.getElementsByClassName('header_button')[0].setAttribute("class", 'hidden');      }
				  	}
			  	} } });
	}
	function submitOffsets(){
 	
 	var table= document.getElementById("inputratetable");
 	var tot = document.getElementById("inputratetable").rows.length;
 	var invoices = [];alertts='';
	/*for (var i = 0; i < tot; i++) {
    	invoices[invoices.length] = "HOUDA" + table[i].innerHTML;
    }*/
    var table = document.getElementById('inputratetable');
    $("#inputratetable input[type=checkbox]:checked").each(function () {
    	var row = $(this).closest("tr")[0];
		message='';
    	message += row.cells[1].innerHTML+',';
    	message += row.cells[2].innerHTML+',';
    	message += row.cells[3].innerHTML+',';
        message += row.cells[4].children[0].value.replace(',','')+',';
		message += row.cells[5].children[0].value+',';
		message += row.cells[6].children[0].value.replace(',','');
 //alert(message);
		if(row.cells[4].children[0].value.trim() == '')
    	{
    		alertts+="INV#"+ row.cells[1].innerHTML+' rate cannot be empty <br>	';
    	}
    	if(row.cells[5].children[0].value.trim() == '')
    	{
    		if(alertts.length>0)
    		{
				alertts+=" and paid amount must be specified <br>	";
    		}else{
    			alertts+="INV#"+ row.cells[1].innerHTML+' paid amount must be specified <br>';
    		}
    	}
    	if(row.cells[6].children[0].value.trim() == '' && row.cells[5].children[0].value == 2)
    	{
    		if(alertts.length>0)
    		{
				alertts+=" and received amount must be specified due to partial payment<br>	";
    		}else{
    			alertts+="INV#"+ row.cells[1].innerHTML+' received amount must be specified due to partial payment<br>';
    		}
    	}

		invoices[invoices.length]= message;
     });
   
    if(alertts != '')
    {
    	var buttons = {
							        
							        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
							        }
							}
				  		custom_alert('ERROR MESSAGE', alertts, buttons); 
    }else{
    	$('#img').show();
    	$.ajax({	type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('incomingTransfers/closeInvoices');?>",
		  	dataType: "json",  	data: { 'data': invoices,'tr': <?php echo  $model->id;?>,'currency': <?php echo  $model->currency;?>},
		  	success: function(data) {
			  	if (data) {
					  if (data.status == 'success') { 		$('#img').hide(); 		$('#popuptandm').hide();    var buttons = {
							        
							        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
							        }
							}
				  		custom_alert('ALERT MESSAGE', data.message, buttons);
$.fn.yiiGridView.update('items-grid');
					}else{			  				
				  	$('#img').hide();$('#popuptandm').hide();  	$('#popuptandm').hide();   $.fn.yiiGridView.update('items-grid')
				  		var buttons = {
							        
							        "Ok": {
							        	class: 'ok_button',
							        	click: function() 
								        {
								            $( this ).dialog( "close" );
								        }
							        }
							}
				  		custom_alert('ERROR MESSAGE', data.message, buttons); } } }	});
    }
}
	/*function submitForm() {
		var data = $("#tr-form").serialize(); 
		$.ajax({ type: "POST", data: {'submitid':<?php echo $model->id?>}, dataType: "json", url : $("#tr-form").attr("action"),
		  	success: function(data) {
			  	if (data && data.status) {			  		
			  	if (data.status == "saved"){ closeTab(configJs.current.url);	
				  	} else{
						    var action_buttons = {
                                "Ok": {
                                    click: function(){ $( this ).dialog( "close" ); },
                                    class : 'ok_button'
                                }
                            }
                        custom_alert('ERROR MESSAGE', data.message, action_buttons);
			  	}
	  		}  	}	});
	}*/
</script>