<?php $support_weekend= $model->support_weekend;?>
<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Customer Details');?></span></div>
<div class="horizontalLine smaller_margin marginb10"></div>
<fieldset id="customer_fields" class="create"> 
	<div class="formColumn">
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'name'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'name'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'name', array('id'=>"Customers_name_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'city'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'city'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'city', array('id'=>"Customers_city_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'country'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'country', Codelkups::getCodelkupsDropDown('country'), array('prompt'=>Yii::t('translations', 'Select country'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'country', array('id'=>"Customers_country_em_")); ?>
		</div>		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'region'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'region', Codelkups::getCodelkupsDropDown('region'), array('prompt'=>Yii::t('translations', 'Select region'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'region', array('id'=>"Customers_region_em_")); ?>
		</div>
		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'strategic'); ?>
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'strategic', array('Low'=>'Low','Medium'=>'Medium','High'=>'High'), array('prompt' => Yii::t('translations', ''))); ?>
			</div>
			<?php echo  CCustomHtml::error($model,'strategic'); ?>
		</div>

			
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'brands'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'brands'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'brands', array('id'=>"Customers_brands_em_")); ?>
		</div>

		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'type_of_items'.' *'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'product_type'); ?>
			</div>
			<?php echo CCustomHtml::error($model,'product_type', array('id'=>"Customers_product_type_em_")); ?>
		</div>	
		<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0)  { ?>			
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'erp'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'erp'); ?>
			</div>
			<?php echo CCustomHtml::error($model,'erp', array('id'=>"Customers_erp_em_")); ?>
		</div>
		<?php }?>	
		<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'billing Details');?></span></div>
		<div class="horizontalLine smaller_margin marginb10" style="width:300% !important;"></div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'bill_to_contact_person'); ?>			
			<div class="inputBg_create">
                <!--
                    /*
                     * Author: Mike
                     * Date: 19.06.19
                     * disable autofilled fields
                     */
                -->
				<?php echo CHtml::activeTextField($model, 'bill_to_contact_person',['disabled' => 'disabled']); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'bill_to_contact_person', array('id'=>"Customers_bill_to_contact_person_em_")); ?>
		</div>
<div class="row ">
			<?php echo CHtml::activeLabelEx($model,'customer_reference'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'customer_reference', Customers::getCustomersBilling(),array('prompt'=>Yii::t('translations', ''))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'customer_reference', array('id'=>"Customers_customer_reference_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'bank'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'bank', Codelkups::getCodelkupsDropDown('bank_code'), array('prompt'=>Yii::t('translations', 'Select Bank'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'bank', array('id'=>"Customers_bank_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'dolphin_aux'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'dolphin_aux'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'dolphin_aux', array('id'=>"Customers_dolphin_aux_em_")); ?>
		</div>
		<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Account Details');?></span></div>
<div class="horizontalLine smaller_margin marginb10" style="width:300% !important;"></div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'account_manager'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'account_manager', Users::getAllSelect(), array('prompt'=>Yii::t('translations', 'Select Account Manager'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'account_manager', array('id'=>"Customers_account_manager_em_")); ?>
		</div>

			<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'cs_representative'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'cs_representative', Users::getAllSelect(), array('prompt'=>Yii::t('translations', 'Select CS Rep'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'cs_representative', array('id'=>"Customers_cs_representative_em_")); ?>
		</div>
		

		<?php } ?>	
	</div>
	<div class="formColumn">
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'status'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'status', Customers::getStatusList()); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'status', array('id'=>"Customers_status_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'website'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model, 'website'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'website', array('id'=>"Customers_website_em_")); ?>
		</div>
		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'main_phone'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'main_phone'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'main_phone', array('id'=>"Customers_main_phone_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'time_zone'); ?>
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'time_zone', Customers::timeZones(), array('prompt'=>Yii::t('translations', 'Select Time Zones'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model, 'time_zone', array('id'=>"Customers_time_zone_em_")); ?>
		</div>	
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'Week-End Days *'); ?>
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'week_end', array('Thursday/Friday'=>'Thursday/Friday','Friday/Saturday'=>'Friday/Saturday','Saturday/Sunday'=>'Saturday/Sunday'), array('prompt' => Yii::t('translations', ''))); ?>
			</div>
		<?php echo CCustomHtml::error($model, 'week_end', array('id'=>"Customers_week_end_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'industry'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'industry', Codelkups::getCodelkupsDropDown('industry'), array('multiple'=>'miltuple',  'style'=>'height: 104px;')); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'industry', array('id'=>"Customers_industry_em_")); ?>
		</div>
		<div class="row <?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0)  { echo 'margint115';}else{ echo 'margint36';} ?> ">
			<?php echo CHtml::activeLabelEx($model,'bill_to_contact_email'); ?>			
			<div class="inputBg_create">
                <!--
                    /*
                     * Author: Mike
                     * Date: 19.06.19
                     * disable autofilled fields
                     */
                -->
                    <?php echo CHtml::activeTextField($model, 'bill_to_contact_email',['disabled' => 'disabled']); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'bill_to_contact_email', array('id'=>"Customers_bill_to_contact_email_em_")); ?>
		</div>
		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'id_assigned'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'id_assigned', Receivables::getAssignedto()); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'status', array('id'=>"Customers_status_em_")); ?>
		</div>

		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'aux'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'aux', Codelkups::getCodelkupsDropDown('aux_code'), array('prompt'=>Yii::t('translations', 'Select Auxiliary'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'aux', array('id'=>"Customers_aux_em_")); ?>
		</div>

		
		
	<div class="row <?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { echo 'margint119'; } else{ echo 'margint119'; } ?>	 ">
			<?php echo CHtml::activeLabelEx($model, 'reassign_notification'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'reassign_notification', array('Yes'=>'Yes', 'No'=>'No') , array('prompt'=>Yii::t('translations', ''))); ?>
			</div>			
			<?php echo CCustomHtml::error($model, 'reassign_notification', array('id'=>"Customers_reassign_notification_em_")); ?>
		</div>
		

		<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>			
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'n_licenses_audited'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model, 'n_licenses_audited'); ?>
			</div>			
			<?php echo CCustomHtml::error($model, 'n_licenses_audited', array('id'=>"Customers_licencesau_code_em_")); ?>
		</div>
		<?php } ?>
	</div>	
	<div class="formColumn">
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'primary_contact_name'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model,'primary_contact_name'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'primary_contact_name', array('id'=>"Customers_primary_contact_name_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'job_title'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model, 'job_title'); ?>
			</div>			
			<?php echo CCustomHtml::error($model, 'job_title', array('id'=>"Customers_job_title_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'primary_contact_email'); ?>
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model, 'primary_contact_email'); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'primary_contact_email', array('id'=>"Customers_primary_contact_email_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'mobile_number'); ?>			
			<div class="inputBg_create">
				<?php echo CHtml::activeTextField($model, 'mobile_number'); ?>
			</div>
			<?php echo CCustomHtml::error($model, 'mobile_number', array('id'=>"Customers_mobile_number_em_")); ?>
		</div>
	
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'default_currency'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'default_currency', Codelkups::getCodelkupsDropDown('currency'), array('prompt'=>Yii::t('translations', 'Select currency'))); ?>
			</div>
			<?php echo CCustomHtml::error($model,'default_currency', array('id'=>"Customers_default_currency_em_")); ?>
		</div>	
		<div class="row">
			<?php echo CHtml::activeLabelEx($model,'address'); ?>			
			<div class="textareaBg_create">
				<?php echo CHtml::activeTextArea($model, 'address'); ?>
			</div>					
			<?php echo CCustomHtml::error($model, 'address', array('id'=>"Customers_address_em_")); ?>
		</div>

		<div class="row <?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0)  { echo 'margint115';}else{ echo 'margint36';} ?>">
		
			<?php echo CHtml::activeLabelEx($model,'bill_to_address'); ?>			
			<div class="textareaBg_create">
                <!--
                    /*
                     * Author: Mike
                     * Date: 19.06.19
                     * disable autofilled fields
                     */
                -->
				<?php echo CHtml::activeTextArea($model, 'bill_to_address',['disabled' => 'disabled']); ?>
			</div>					
			<?php echo CCustomHtml::error($model, 'bill_to_address', array('id'=>"Customers_bill_to_address_em_")); ?>
		</div>
		<div class="row width254">
			<?php echo CHtml::activeLabelEx($model, 'lpo_required'); ?>
			<div class="">				
            	<div class="row input <?php echo ($model->lpo_required == 'Yes')?'checked' : ''?>" onclick="CheckOrUncheckInput(this);">
					<?php  echo CHtml::CheckBox('Customers[lpo_required]',($model->lpo_required == 'Yes')?'checked' : '' , array (
                                        'style'=>'width:10px;margin-left: 17px;margin-top: 8px;')); ?>
				</div>
            </div>
			<?php echo  CCustomHtml::error($model,'lpo_required'); ?>
		</div>	
		<?php if(Customers::hasMaint($model->id) != 0 || Customers::hasMaintMainCustomer($model->id) != 0) { ?>
			<div class="row margint134">
			<?php echo CHtml::activeLabelEx($model,'ca'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'ca', Users::getAllSelect(), array('prompt'=>Yii::t('translations', 'Select CA'))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'ca', array('id'=>"Customers_ca_em_")); ?>
		</div>
			<?php } ?>			
		<div class="row <?php if(empty($model->id) || (Customers::hasMaint($model->id) == 0 && Customers::hasMaintMainCustomer($model->id) == 0))  { echo 'margint134';}?>">
			<?php echo CHtml::activeLabelEx($model,'complexity'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'complexity', Customers::getcomplexity(), array()); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'complexity', array('id'=>"Customers_complexity_em_")); ?>
		</div>	
			
	</div>
</fieldset> 
<div class="separator" style="margin: 0px auto !important;"></div>
<div id="contact_fields">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'contacts-grid',
	'dataProvider'=>$model->contacts,
	'summaryText' => '',
	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
	'columns'=>array(
		'name',
		'email',
		'job_title',
		'mobile_number',
		array(
			'class'=>'CCustomButtonColumn',
			'template'=>'{update} {delete}',
			'htmlOptions'=>array('class' => 'button-column'),
			'buttons'=>array
            (
            	'update' => array(
					'label' => Yii::t('translations', 'Edit'), 
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("customers/manageContact", array("id"=>$data->id))',
            		'options' => array(
            			'onclick' => 'showContactForm(this);return false;'
            		),
				),
				'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("customers/deleteContact", array("id"=>$data->id))',  
                	'options' => array(
                		'class' => 'delete',
					)
				),
            ),
		),
	),
)); ?>
	<div class="tache new_cont">
		<div onclick="showContactForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW CONTACT');?></b></u></div>
	</div>
</div>
<script>
	$("#sw_dropdown").change(function(){
		var selected= $("#sw_dropdown option:selected").val()
		if(selected=="N/A"){	$("#cs_div").hide();	$("#sd_checkbox").hide();
		}else{
			$("#cs_div").show();	$("#sd_checkbox").show();
		}
	});
	function showContactForm(element, newConn) {
		if (false) {	$(element).addClass('invalid');	} else {	$(element).removeClass('invalid');	}
				if (!$(element).hasClass('invalid')) {
			var url, data;
			if (newConn) {	url = "<?php echo Yii::app()->createAbsoluteUrl('customers/manageContact');?>";
			} else {	url = $(element).attr('href');	}
			$.ajax({
		 		type: "POST",  	url: url,
			  	data: 'update=<?php echo $model->isNewRecord ? 0 : 1; ?>', 
			  	dataType: "json",
			  	success: function(data) {
				  	if (data) {
					  	if (data.status == 'success') {
						  	if (newConn) {
						  		$('.new_cont').hide();
						  		$('.new_cont').after(data.form);
						  	} else {
								$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');
						  	}
					  	}
				  	}
		  		}
			});
		} else {		alert('The form is not valid!');		}
	}	
	function saveContact(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('customers/manageContact');?>";
		if (id != 'new') {	url += '/'+parseInt(id);	}		
		$.ajax({
	 		type: "POST",
	 		data: $(element).parents('.new_contact').serialize() + '&CustomersContacts['+id+'][id_customer]=<?php echo $model->id;?>&update=<?php echo $model->isNewRecord ? 0 : 1;?>',					
		  	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') {
					  	if (id == 'new') {
					  		$(element).parents('.tache.new').remove();	$('.new_cont').show();
					  	}		
				  		$.fn.yiiGridView.update('contacts-grid');		
				  	} if(data.status == 'unique'){
					  		var action_buttons = {
							        "Ok": {
										click: function(){
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
			  				custom_alert('ERROR MESSAGE', 'Username already exists', action_buttons);
					  	}else if(data.status == 'password'){
					  		var action_buttons = {
							        "Ok": {
										click: function(){
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
			  				custom_alert('ERROR MESSAGE', 'Password cannot be blank', action_buttons);
						  	}else if (data.status == 'success') {
				  			$(element).parents('.tache.new').replaceWith(data.form);
				  		}
			  	}
	  		}
		});
	}
	function CheckOrUncheckInput(obj){
		var checkBoxDiv = $(obj);	var input = checkBoxDiv.find('input[type="checkbox"]');		
		if (checkBoxDiv.hasClass('checked')) {
			checkBoxDiv.removeClass('checked');		input.prop('checked', false);
		}else {
			checkBoxDiv.addClass('checked');		input.prop('checked', true);
		}
	}
</script>



