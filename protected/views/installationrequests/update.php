<div class="mytabs ir_edit">
	<?php $form=$this->beginWidget('CActiveForm', array('id'=>'ir-form','enableAjaxValidation'=>false,
		'htmlOptions' => array('class' => 'ajax_submit','enctype' => 'multipart/form-data',),	)); ?>
	<div id="ir_header" class="edit_header">
		<div class="header_title">	
			<span class="red_title"><?php echo Yii::t('translations', 'IR HEADER');?></span>
			<a class="header_button" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('Installationrequests/updateHeader', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit Header');?></a>	
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
			<span class="red_title"><?php echo Yii::t('translations', 'IR Products');?></span>
		</div>
		<div id="ir_products_content" class="border-grid grid">
			<?php	$buttons = array();		$tmp = '';
			if ($can_modify && GroupPermissions::checkPermissions('ir-general-installationrequests', 'write')) {
			$tmp = '{update}{delete}';
			$buttons = array(
	            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("Installationrequests/manageProduct", array("id"=>$data->id))',
	            		'options' => array('onclick' => 'showProductForm(this);return false;'),),
					'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("Installationrequests/deleteProduct", array("id"=>$data->id))',  
	                	'options' => array('class' => 'delete',),
					),   );
		}
 $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid','dataProvider'=>InstallationrequestsProducts::getProductsProvider($model->id),
                'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
                'afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
                'columns' => array(
        array('header'=>Yii::t('translations', 'Product'),'name' => 'product_name','value' => 'Codelkups::getCodelkup($data->id_product)',
            'type'=>'raw','htmlOptions' => array('class' => 'column150','onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"), 
            'headerHtmlOptions' => array('class' => 'column150'),),
        array('header'=>Yii::t('translations', 'Version'),'name' => 'software_version','value' => 'Codelkups::getCodelkup($data->getswversion($data->id_ir, $data->id_product))',
            'htmlOptions' => array('class' => 'column85'),'headerHtmlOptions' => array('class' => 'column85'),),
        array('name' => 'number_of_nodes','value' => '$data->number_of_nodes','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
        array('name' => 'db_type','value' => 'Codelkups::getCodelkup($data->getwmsdbtype($data->id_ir, $data->id_product))',
            'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('header'=>Yii::t('translations', 'Collation'),'name' => 'db_collation','value' => '$data->getDBCollationLabel($data->db_collation)',
            'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),
        array('name' => 'number_of_schemas','value' => '$data->number_of_schemas','htmlOptions' => array('class' => 'column120'), 
            'headerHtmlOptions' => array('class' => 'column120'),),
        array('name' => 'authentication','value' => '$data->getAuthenticationLabel($data->authentication)',
            'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
        array('name' => 'reporting_type','value' => '$data->getReportingTypeLabel($data->reporting_type)',
            'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
         array('header' => 'License','value' => '$data->getLicenseLabel($data->license_type)','htmlOptions' => array('class' => 'column90'), 
            'headerHtmlOptions' => array('class' => 'column90'),),
         array('name' => 'status','header' => 'Status','htmlOptions' => array('class' => 'column50'),
         	'type'=>'raw','value' => '(!GroupPermissions::checkPermissions(\'ir-assign-installationrequests\',\'write\'))?  InstallationrequestsProducts::getStatusLabel($data->status) : InstallationrequestsProducts::getStatusDropDown($data->id,'.$model->status.',$data->status)',  ),
		  array('class'=>'CCustomButtonColumn','template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons, ), ),	));
		if(GroupPermissions::checkPermissions('ir-general-installationrequests', 'write')){	?>
			<div class="tache new_item" <?php echo (!$can_modify) ? 'style="display:none;"' : '';?>>
				<div onclick="showProductForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW Product');?></b></u></div>
			</div>	<div>	</div>	<?php } ?>	</div>	</div>
	<br/><br/>
	<div class="horizontalLine smaller_margin"></div>	
	<div class="supportdesk_attache" id="attachament-change">
	<?php $this->renderPartial('_attachements',array('model'=>$model,)); 
 if($model->isEditable() && GroupPermissions::checkPermissions('ir-general-installationrequests', 'write')){ ?>	</div>
	<div class=" exppage" style="margin-top:110px;" id="fileuploads">
		<?php	Yii::import("xupload.models.XUploadForm");
			$this->widget('xupload.XUpload', array('url' => Yii::app( )->createUrl("Installationrequests/upload"),'model' => new XUploadForm(),
	            'htmlOptions' => array('id'=>'ir-form'),'formView' => 'small_form','attribute' => 'file','autoUpload' => true,'multiple' => false,
				'options' => array('maxFileSize' => 15728640, 
					'submit' => "js:function (e, data) {
						var path = '". addslashes(Yii::app( )->getBasePath( )).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."installationrequests".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
						var publicPath = '". addslashes(str_replace("/","\\",Yii::app( )->getBaseUrl( ))).DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."customers".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->customer}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."installationrequests".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."{$model->id}".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.""."';
        				var model_id = '".$model->id."';
	                    data.formData = {path : path, publicPath: publicPath, modelId : model_id};
	                    return true; }",				
				), )); }	?>
			</div>
		</div>
	<fieldset id="sub">
		<input type="hidden" name="submitted" value="1" />			
	</fieldset>
	<div class="row buttons">
		<div class="save"><?php echo CHtml::submitButton(Yii::t('translations','Save'), array('onclick' => 'js:submitForm();return false;','class'=>'marginl9')); ?></div>
	</div>
	<?php $this->endWidget();?>
</div>
<script>
$( "span.attachFile" ).addClass(" support_file");
function changeProductStatus(val,id){
	$.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('Installationrequests/ChangeProductStatus');?>",dataType: "json",data: {'status':val,'id':id},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') { console.log('da');
			  	}else if(data.status == 'redirect'){
						window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('Installationrequests/createmodelinfo');?>'+'?new='+1+'&id='+data.id);
				}else{
						    var action_buttons = {
                                "Ok": {
                                    click: function(){ $( this ).dialog( "close" ); },
                                    class : 'ok_button'
                                }
                            }
                        custom_alert('ERROR MESSAGE', data.message, action_buttons);
			  	}
			  	$.fn.yiiGridView.update('items-grid');
		  	} }	});
}
function showProductForm(element, newItem) {
		var url;
		if (newItem) { url = "<?php echo Yii::app()->createAbsoluteUrl('Installationrequests/manageProduct');?>";
		} else { url = $(element).attr('href');	}
		$.ajax({ type: "POST", url: url, dataType: "json", data: {'id_ir':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) { $('.new_item').hide(); $('.new_item').after(data.form);
					  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>'); 	}
				  	}
			  	}	}	});
	}
	function updateProduct(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('Installationrequests/manageProduct');?>";
		if (id != 'new'){ url += '/'+parseInt(id); }
		$.ajax({ type: "POST",
	 		data: $(element).parents('.new_product').serialize() + '&InstallationrequestsProducts['+id+'][id_ir]=<?php echo $model->id;?>' + '&id_ir=<?php echo $model->id;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') { $(element).parents('.tache.new').remove(); $('.new_item').show(); $.fn.yiiGridView.update('items-grid'); }	
				  		$.fn.yiiGridView.update('items-grid');			
				  	} else if(data.status == 'redirect'){
						window.location.replace('<?php  echo Yii::app()->createAbsoluteUrl('Installationrequests/createmodelinfo');?>'+'?new='+1+'&id='+id);
				  	}else {
				  		if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); }
				  	} } } });
	}
	function updateHeader(element) {
		$.ajax({ type: "POST", data: $('#header_fieldset').serialize()  + '&ajax=ir-form',					
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('Installationrequests/updateHeader', array('id' => $model->id));?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html); $('.header_content').removeClass('hidden'); $('.edit_header_content').addClass('hidden');
				  	} else {
				  		if (data.status == 'success' && data.html) { $('.edit_header_content').html(data.html); }
				  	}
				  	if (data.can_modify == false) { modeNotEditable();
				  	} else { modeEditable(); }
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
				  	}
			  	} } });
	}
	function submitForm() {
		var data = $("#ir-form").serialize(); 
		$.ajax({ type: "POST", data: {'submitid':<?php echo $model->id?>}, dataType: "json", url : $("#ir-form").attr("action"),
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
	}
</script>