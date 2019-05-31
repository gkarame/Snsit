<div class="mytabs ir_edit">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'tr-form','enableAjaxValidation'=>false,
		'htmlOptions' => array('class' => 'ajax_submit','enctype' => 'multipart/form-data',),	)); ?>
	<div id="ir_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'TR HEADER');?></span>			
<?php if(GroupPermissions::checkPermissions('financial-incomingTransfers', 'write') && $model->status==1){ ?>
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