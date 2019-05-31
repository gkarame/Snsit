<div class="header_title" style="    font-family: Arial;">	<span class="red_title"><?php echo Yii::t('translations', 'SUPPLIER');?></span>
			<?php  if(GroupPermissions::checkPermissions('suppliers-list','write'))	{?>
			 <a class="header_button"  href="<?php echo Yii::app()->createAbsoluteUrl('suppliers/update', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit');?></a>	
		<?php } ?></div><div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->name)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_type')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(($model->idType->codelkup)); ?></div></div>
<div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('currency')); ?></div>
	<div class="general_col2 "><?php echo $model->currencyId > 0 ? CHtml::encode(($model->idCurrency->codelkup)) : ""; ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('category')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(($model->category)); ?></div>
</div><div class="view_row">	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('country')); ?></div>
	<div class="general_col2 "><?php echo $model->countryId > 0 ? CHtml::encode(($model->idCountry->codelkup)) : ""; ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('city')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->city); ?></div>
</div><div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('main_contact')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(($model->main_contact)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('main_phone')); ?></div>
	<div class="general_col4"><?php echo CHtml::encode(($model->main_phone)); ?></div>
</div><div class="view_row"><div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('other_phone')); ?></div>
	<div class="general_col2"><?php echo CHtml::encode($model->other_phone); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('account_name')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->account_name); ?></div></div>
<div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('bank_name')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->bank_name); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('iban')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(($model->iban)); ?></div>
</div><div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('swift')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->swift); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('dolphin_code')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->dolphin_code); ?></div>	 
</div><div class="view_row"><div class="general_col1 "><?php echo CHtml::encode($model->getAttributeLabel('preffered')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->preffered); ?></div>
	<div class="general_col3 "><?php echo CHtml::encode($model->getAttributeLabel('emails')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Suppliers::getemails($model->emails)); ?></div>	 
</div><div class="header_title" style="    font-family: Arial;">	<span class="red_title"><?php echo Yii::t('translations', 'PRINTED');?></span>
	<div class="wrapper_action" id="action_tabs_right" style="margin-top:-30px;"><div onclick="chooseActions();" class="action triggerAction" style="margin-right:20px;"><u><b>ACTION</b></u></div>
				<div class="action_list actionPanel" style="margin-top:-25px;"><div class="headli"></div><div class="contentli">
						<div class="cover"><div class="li noborder" onclick="printCheck();">Reprint Check(s)</div></div>
						<div class="cover"><div class="li noborder" onclick="triggerSDSearch(2);">Show History</div></div>	
					</div><div class="ftrli"></div>
					</div></div>
</div>
<?php Yii::app()->clientScript->registerScript('getSuppliersProvider', "$('.search-form-checklist form').submit(function(){
	$.fn.yiiGridView.update('budget-record-grid', {		data: $(this).serialize()	});	return false; });"); ?>
<div class="search-form-checklist hidden"><?php $this->renderPartial('_searchChecklist',array(	'model'=>$model,)); ?></div>

<div id="print_grid"  class="grid border-grid">
<?php 	$buttons = array();	$tmp = '';	if(GroupPermissions::checkPermissions('suppliers-list','write')){
			$tmp = '{update}';
			$buttons = array(
	            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("Suppliers/manageCheck", array("id"=>$data->id))',
	            		'options' => array('onclick' => 'showProductForm(this);return false;'),),);		}
	$provider = $model->getSuppliersProvider();
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'budget-record-grid','dataProvider'=>$provider,	'summaryText' => '',
		'pager'=> Utils::getPagerArray(),	'template'=>'{items}{pager}',
		'columns'=>array(
			array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
			array('header'=>Yii::t('translations', 'Check #'),'value'=>'$data->check','name' => 'ea_number','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
			array('name' => 'JV#','value' => 'chunk_split($data->jv_nb, 44, " ")','htmlOptions' => array('class' => 'column100','style'=>'padding-left:10px;'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('name' => 'Description','value' => '$data->description','htmlOptions' => array('class' => 'column450'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('name' => 'Amount','value' => 'Utils::formatNumber($data->amount)','htmlOptions' => array('class' => 'column65','style'=>'padding-left:10px;'),'headerHtmlOptions' => array('class' => 'column65'),			),
			array('name' => 'Acc#','value' => 'isset($data->caccount->codelkup) ? $data->caccount->codelkup : ""','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
			array('name' => 'Bank Code','value' => 'isset($data->cbank->codelkup) ? $data->cbank->codelkup : ""','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('name' => 'Aux Code','value' => 'isset($data->caux->codelkup) ? $data->caux->codelkup : ""','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('header' => 'Date','value' => 'date("d/m/Y",strtotime($data->date))','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('name' => 'Status','value' => 'isset($data->status) ? SuppliersPrint::getStatusLabel($data->status) : ""','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
			array('header' => 'User','value' => '$data->idUser->firstname." ".$data->idUser->lastname','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
			array('header'=>'Direct',	'id'=>'checkdirect','type'=>'raw',		 
			'value'=>' GroupPermissions::checkPermissions("suppliers-list","write")?CHtml::CheckBox("direct",($data->direct == "1")?true:false , array (                                       
                                        "class"=>($data->direct == "1")?"checked":"",
                                        "id" =>"red_$data->id",
                                        "onClick"=>"direct($data->id)")):CHtml::CheckBox("direct",($data->direct == "1")?true:false , array (                                       
                                        "class"=>($data->direct == "1")?"checked":"",
                                        "id" =>"red_$data->id",
                                        "disabled"=>true));',),
			array('class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'), 'buttons'=>$buttons,  ), 	),	)); ?></div>
<script>
function triggerSDSearch(status) {	status = parseInt(status);	$('#Suppliers_status').val(status); $('.search-btn').trigger('click'); }
function printCheck() {

	$.ajax({ type: "POST",  url: "<?php echo Yii::app()->createAbsoluteUrl('suppliers/checkPrint');?>",  dataType: "json",  data:  $('.checkbox_grid_invoice input').serialize(),
success: function(data) {  

	if (data) {  
	{ 
		if(data.status == "fail")
		{
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
		}else{
			$('.action_list').hide();
			var token = new Date().getTime();//blockUIForDownload(token);
			window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('suppliers/print');?>'+'?token='+token+'&checkinvoice='+data.invoices_ids);
			triggerSDSearch(2);
		}
	}
}  }});}
function direct(id,element){
		if ($('#red_'+id).hasClass('checked')){ val = 0;	$('#red_'+id).removeClass('checked');
		}else{	val = 1;	$('#red_'+id).addClass('checked');	}
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('Suppliers/directFlag');?>", dataType: "json",
		  	data: {'id':id,'val':val},
		  	success: function(data) {
	  		}
		});	}
function showProductForm(element, newItem) {
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('Suppliers/manageCheck');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({
	 		type: "POST", 	url: url, 	dataType: "json", 	data: {'id':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) { 		$('.new_item').hide();		$('.new_item').after(data.form);
					  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="13" class="noback">' + data.form + '</td>');  	}  	}  	}	}	});	}
function updateCheck(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('Suppliers/manageCheck');?>";
		if (id != 'new') {	url += '/'+parseInt(id);	}
		$.ajax({type: "POST",	data: $(element).parents('.new_product').serialize() + '&SuppliersPrint['+id+'][id_supplier]=<?php echo $model->id;?>' + '&id_supplier=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {  		if (data.status == 'saved') {	$.fn.yiiGridView.update('budget-record-grid');	}  	}  }	});	}

</script>