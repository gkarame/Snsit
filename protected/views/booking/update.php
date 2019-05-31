<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'booking-form',
	'enableAjaxValidation'=>false,
)); ?>
<fieldset id="booking_fields" class="create"> 
<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Trip Details');?></span></div>
	<div class="formColumn">	
		
		<div class="row">
		<?php echo CHtml::activeLabelEx($model,'traveler'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'traveler', Users::getAllSelect(), array('prompt'=>Yii::t('translations', 'Select Traveler'),'onchange'=>'refreshAlerts(); refreshbranch();')); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'traveler', array('id'=>"Booking_traveler_em_")); ?> 		
		</div>		
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'origin'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'origin', Codelkups::getCodelkupsDropDown('branch'), array('prompt'=>Yii::t('translations', 'Select Origin'),'onchange'=>"refreshsupp('travel');")); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'origin', array('id'=>"Booking_origin_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'departure_time'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'departure_time', Booking::getTimeList(), array('prompt'=>Yii::t('translations', ''))); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'departure_time', array('id'=>"Booking_departure_time_em_")); ?>
		</div>	
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'return_time'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'return_time', Booking::getTimeList(), array('prompt'=>Yii::t('translations', ''))); ?>
			</div>		
			<?php echo CCustomHtml::error($model,'return_time', array('id'=>"Booking_return_time_em_")); ?>
		</div>
		
		<div class="row">
		<?php echo CHtml::activeLabelEx($model,'travel_supplier'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'travel_supplier', Suppliers::getTravelSuppPerOriginDD($model->origin)); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'travel_supplier', array('id'=>"Booking_travel_supplier_em_")); ?> 		
		</div>	
	</div>
	<div class="formColumn">
	<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'purpose'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'purpose', Booking::getPurposeList(), array('onchange' => 'changeCategory(this);','prompt'=>Yii::t('translations', 'Select Purpose'))); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'purpose', array('id'=>"Booking_purpose_em_")); ?>
		</div>
		 <div class="row ">
			<?php echo CHtml::activeLabelEx($model, 'destination'); ?>
		
			<div class="inputBg_create">
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'destination',		
						'source'=>Booking::getAllCitiesAutocomplete(),
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
							'select'=>"js:function(event, ui) {
							this.value = ui.item.value;
							refreshsupp('hotel',ui.item.value);
							refreshsupp('car',ui.item.value);
							return false;	}"
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));
				?>
			</div>
			<?php echo CCustomHtml::error($model, 'destination', array('id'=>"Booking_destination_em_")); ?>
			
		</div>
		<div class="row item inline-block normal">
			<?php echo CHtml::activeLabelEx($model, 'departure_date'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "departure_date", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn',
			    		'onSelect'=> 'js:function( selectedDate ) {
			    			refreshAlerts();    }'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield',
			    		'style' => 'border: none;height:23px;'
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'date', array('id'=>"Booking_departure_date_em_")); ?>
		</div>	
		<div class="row item inline-block normal ">
			<?php echo CHtml::activeLabelEx($model, 'return_date'); ?>
			<div class="dataRow ">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "return_date", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield ',
			    		'style' => 'border: none;height:23px;'
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'return_date', array('id'=>"Booking_return_date_em_")); ?>
		</div>
		
		
		<div class="row marginb20  descr row_textarea_ea">
			<?php echo $form->labelEx($model, 'notes'); ?>
			<div class="inputBg_create">
			<?php echo $form->textField($model, 'notes',array('autocomplete'=>'off')); ?>
			</div>		
			<?php echo $form->error($model,'notes'); ?>
		</div>	
		
	</div>
	<div class="formColumn">

		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'id_customer'); ?>		
			<div class="inputBg_create">
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'id_customer',
						'source'=>Customers::getAllAutocomplete(true),
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
							'select'=>"js:function(event, ui) {
				            var terms = split(this.value);
				            terms.pop();
				            terms.push( ui.item.value );
				            terms.push('');
				            this.value = terms.join(', ');
				            refreshProjectListsProjects();
				            refreshDestination();
				            (document.getElementById('.header_title')).onclick();				            
				            return false;	        }"
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
						),
				));		?>
			</div>
			<?php echo CCustomHtml::error($model, 'id_customer', array('id'=>"Booking_id_customer_em_")); ?>			
		</div>
		<div class="row ">
			<?php echo CHtml::activeLabelEx($model, 'id_project'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'id_project', Projects::getAllProjectsSelect2($model->id_customer), array('onchange' => 'refreshBillable(this);','prompt'=>'')); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'id_project', array('id'=>"Booking_id_project_em_")); ?>
		</div>
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'billable'); ?>
			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'billable', Booking::getBillableList()); ?>
			</div>
			
			<?php echo CCustomHtml::error($model,'billable', array('id'=>"Booking_billable_em_")); ?>
		</div>	
		<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'status'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'status', Booking::getStatusList()); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'status', array('id'=>"Booking_status_em_")); ?>
		</div>	
		
	</div>
<div class="horizontalLine smaller_margin"></div>
<div class="formColumn">
<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Additional Bookings');?></span></div>
<br/>	<div class="row">
			<?php echo CHtml::activeLabelEx($model, 'hotel_booking'); ?>
			
			 
				<?php echo $form->radioButtonList($model, 'hotel_booking', Booking::getRadioBut(),  array('separator' => "  ",'onChange'=>'checkadditionalHotel(this);', 'labelOptions'=>array('style'=>'display:inline'))); ?>
	
		 
			
			<?php echo CCustomHtml::error($model,'hotel_booking', array('id'=>"Booking_hotel_booking_em_")); ?>
		</div>
		<div class="row hidden">
		<?php echo CHtml::activeLabelEx($model,'hotel_supplier'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'hotel_supplier',  Suppliers::getHotelSuppPerCityDD($model->destination),  array('onchange'=>'refreshfields();')); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'hotel_supplier', array('id'=>"Booking_hotel_supplier_em_")); ?> 		
		</div>	
		<div class="row hidden marginb28">
		<?php echo $form->labelEx($model, 'hotelname'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'hotelname',array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'hotelname'); ?>
		</div>
	
		<div class="row hidden">
		<?php echo CHtml::activeLabelEx($model,'car_supplier'); ?>			
			<div class="selectBg_create">
				<?php echo CHtml::activeDropDownList($model, 'car_supplier',  Suppliers::getCarSuppPerCityDD($model->destination), array('onchange'=>'refreshfieldsCar();')); ?>
			</div>			
			<?php echo CCustomHtml::error($model,'car_supplier', array('id'=>"Booking_car_supplier_em_")); ?> 		
		</div>	
		<div class="row hidden marginb28">
		<?php echo $form->labelEx($model, 'carname'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'carname',array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'carname'); ?>
		</div>	
		
</div>
<div class="formColumn"><br/><br/><br/>
<div class="row" style="margin-top:2px;">
			<?php echo CHtml::activeLabelEx($model, 'car_booking'); ?>
			
			 
				<?php echo $form->radioButtonList($model, 'car_booking', Booking::getRadioBut(),  array('separator' => "  ",'onChange'=>'checkadditionalcar(this);', 'labelOptions'=>array('style'=>'display:inline'))); ?>
	
		 
			
			<?php echo CCustomHtml::error($model,'car_booking', array('id'=>"Booking_car_booking_em_")); ?>
		</div>
<div class="row item inline-block normal hidden" style="margin-top:3px;">
			<?php echo CHtml::activeLabelEx($model, 'checkin'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "checkin", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield',
			    		'style' => 'border: none;height:23px;',
			    		'value'=> !isset($model->checkin)? (!isset($model->departure_date)?date("d/m/Y"):$model->departure_date):$model->checkin,
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'checkin', array('id'=>"Booking_checkin_em_")); ?>
		</div>
		<div class="row marginb28 hidden">
		<?php echo $form->labelEx($model, 'reason'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'reason',array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'reason'); ?>
		</div>
		<div class="row item inline-block normal hidden" style="margin-top:2px;">
			<?php echo CHtml::activeLabelEx($model, 'pickup'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "pickup", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield',
			    		'style' => 'border: none;height:23px;',
			    		'value'=> !isset($model->pickup)? (!isset($model->departure_date)?date("d/m/Y"):$model->departure_date):$model->pickup,
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'pickup', array('id'=>"Booking_pickup_em_")); ?>
		</div>
		<div class="row marginb28 hidden">
		<?php echo $form->labelEx($model, 'carreason'); ?>
		<div class="inputBg_create">
		<?php echo $form->textField($model, 'carreason',array('autocomplete'=>'off')); ?>
		</div>		
		<?php echo $form->error($model,'carreason'); ?>
		</div>
</div>
<div class="formColumn"><br/><br/>
<div class="row item inline-block normal "></div>
<div class="row item inline-block normal hidden margint85">
			<?php echo CHtml::activeLabelEx($model, 'checkout'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "checkout", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield',
			    		'style' => 'border: none;height:23px;',
			    		'value'=> !isset($model->checkout)? (!isset($model->return_date)?date("d/m/Y"):$model->return_date):$model->checkout,
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'checkout', array('id'=>"Booking_checkout_em_")); ?>
		</div>
		<div class="row item inline-block normal hidden">
			<?php echo CHtml::activeLabelEx($model, 'return'); ?>
			<div class="dataRow">
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=> $model,
			    	'attribute' => "return", 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy',
			    		'showAnim' => 'fadeIn'
			    	),
			    	'htmlOptions' => array(
			    		 'autocomplete'=>'off',
			    		'class' => 'datefield',
			    		'style' => 'border: none;height:23px;',
			    		'value'=> !isset($model->return)? (!isset($model->return_date)?date("d/m/Y"):$model->return_date):$model->return,
			    	),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
			<?php echo CCustomHtml::error($model,'return', array('id'=>"Booking_return_em_")); ?>
		</div>


</div>

<div class="horizontalLine smaller_margin"></div>
<div class="formColumn">
<div class="header_title">	<span class="red_title book" ><?php echo Yii::t('translations', 'Alerts');?></span></div>
<div id="alerts" style="width:900px;padding-bottom:10px;"></div>
</div>
</fieldset>

<div class="row buttons saveDiv">
	<div class="save" id="createb"><?php echo CHtml::submitButton(Yii::t('translations','Save')); ?></div>
	<div class="cancel"><?php echo CHtml::button(Yii::t('translations', 'Cancel'), array('onclick'=>'js:custom_cancel()')); ?></div>
</div>
<?php $this->endWidget(); ?>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetProjectsByClientsMulti');?>';
	var getCitiesByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/GetCityByClientsMulti');?>';
	var testProjectActualsExpensesUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/testProjectActualExpenses');?>';
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
$(function() {
		changeCategory('#Booking_purpose');	
		changeBooking();
		refreshfields();
		refreshfieldsCar();
		refreshAlerts();
	});
	function changeBooking() {
		var radioValue = $('input[name="Booking[hotel_booking]"]:checked').val();
	    if(radioValue == "Yes"){
	            $('#Booking_checkin').parents('.row').removeClass('hidden');
				$('#Booking_checkout').parents('.row').removeClass('hidden');
				$('#Booking_hotel_supplier').parents('.row').removeClass('hidden');
				refreshfields();
	    }else{

			$('#Booking_hotelname').parents('.row').addClass('hidden');
			$('#Booking_reason').parents('.row').addClass('hidden');
	    }
	    var radioCar = $('input[name="Booking[car_booking]"]:checked').val();
	    if(radioCar == "Yes"){
	            $('#Booking_pickup').parents('.row').removeClass('hidden');
				$('#Booking_return').parents('.row').removeClass('hidden');
				$('#Booking_car_supplier').parents('.row').removeClass('hidden');
				refreshfieldsCar();
	    }else{
	    	$('#Booking_carname').parents('.row').addClass('hidden');
			$('#Booking_carreason').parents('.row').addClass('hidden');
	    }
	    if(radioValue != "Yes" && radioCar == "Yes"){
	    	$('#Booking_return').parents('.row').addClass('margint85');
	    }
	}
	function refreshfields()
	{
		var hotel=$('#Booking_hotel_supplier').find(":selected").text();
		var radioValue = $('input[name="Booking[hotel_booking]"]:checked').val();
		var carValue = $('input[name="Booking[car_booking]"]:checked').val();
    	if(radioValue == "Yes"  && hotel == 'Choose another hotel') 
		{
			$('#Booking_hotelname').parents('.row').removeClass('hidden');
			$('#Booking_reason').parents('.row').removeClass('hidden');
			$('#Booking_return').parents('.row').addClass('margint85');
		}else{
			$('#Booking_return').parents('.row').removeClass('margint85');
			$('#Booking_hotelname').parents('.row').addClass('hidden');
			$('#Booking_reason').parents('.row').addClass('hidden');
		}

	}
	function refreshbranch()
  	{
	  	var user=$('#Booking_traveler').val();
	  	$.ajax({type: "POST",data: {'traveler':user},	url: "<?php echo Yii::app()->createAbsoluteUrl('booking/getBranch')?>", dataType: "json",
			  	success: function(data) {
				  	if (data) {	if (data.status == 'success') {	$("#Booking_origin").val(data.branch); 	} 	
				  	refreshsupp('travel'); }
		} });
 	}
 	function refreshsupp(type, origin1=null)
  {
	  if(type=='travel')
	  {
		  var origin=$('#Booking_origin').find(":selected").text(); 
	  }
	  else{
	  	var origin=$('#Booking_destination').val();
	  }
  	if(origin !='')
  	{
  		$.ajax({type: "POST",data: {'origin':origin, 'type': type},	url: "<?php echo Yii::app()->createAbsoluteUrl('booking/getSupplier')?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  	if(type == 'travel')	
			  		{ 
			  		var selected=' selected="selected" ';
				  	//	var selectOptions = '<option value=""></option>';
				  	var selectOptions ='';
					  	var index = 1;
					  	$.each(data,function(id,name){
					  		selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
					  		selected =   ' '; 
					  	});
						 $('#Booking_travel_supplier').html(selectOptions); }	
			  	else if(type == 'car')	
				{	
					var selected=' selected="selected" ';
				  	//	var selectOptions = '<option value=""></option>';
				  	var selectOptions ='';
					  	var index = 1;
					  	$.each(data,function(id,name){
					  		selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
					  		selected =   ' '; 
					  	});
						 $('#Booking_car_supplier').html(selectOptions);
						  var radioValue = $('input[name="Booking[car_booking]"]:checked').val();
    					if(radioValue == "Yes"){	refreshfieldsCar();	}
				}
			  	else if(type == 'hotel')	
			  	{	
			  		var selected=' selected="selected" ';
			  			var selectOptions ='';
				  		var index = 1;
				  		$.each(data,function(id,name){
				  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
				  			selected =   ' '; 
				  		});
				  		$('#Booking_hotel_supplier').html(selectOptions);
				  		var radioValue = $('input[name="Booking[hotel_booking]"]:checked').val();
    					if(radioValue == "Yes"){	refreshfields();	}
				}
			}else
			  	{
			  		if(type == 'travel')	
			  		{ $("#Booking_travel_supplier").val(''); }	
			  	else if(type == 'car')	
			  		{	$("#Booking_car_supplier").val('');	}
			  	else if(type == 'hotel')	
			  		{	$("#Booking_hotel_supplier").val(''); }
			  	}
		}
		});
  	}
  }

function refreshfieldsCar()
{
	var radioValue = $('input[name="Booking[car_booking]"]:checked').val();
	
	var radiohotel = $('input[name="Booking[hotel_booking]"]:checked').val();
	
	var hotel=$('#Booking_car_supplier').find(":selected").text(); ; 
	if(hotel == 'Choose another supplier' && radioValue == "Yes") 
	{
	$('#Booking_carname').parents('.row').removeClass('hidden');
	$('#Booking_carreason').parents('.row').removeClass('hidden');
	if(radiohotel != "Yes"){
	$('#Booking_return').parents('.row').addClass('margint85');
}
	}else{

		$('#Booking_carname').parents('.row').addClass('hidden');
		$('#Booking_carreason').parents('.row').addClass('hidden');
	}
}
 	function refreshAlerts() 
  	{
	  	var departuredate= $('#Booking_departure_date').val();
	  	var user=$('#Booking_traveler').val();  	
		$.ajax({type: "POST",data: {"departuredate":departuredate, 'traveler':user},	url: "<?php echo Yii::app()->createAbsoluteUrl('booking/updateAlerts')?>", dataType: "json",
			  	success: function(data) {
			  	if (data) {	if (data.status == 'success') {
			  		if (data.alerts && data.alerts!="") 
			  		{	
						$('#createb').addClass('hidden');
			  			$('#alerts').html(data.alerts); 
					}else{ 
						$('#createb').removeClass('hidden');
						$('#alerts').html(data.alerts); 
					}	} 	}
	} });
  	}
	function checkadditionalcar(element) {

$car =  $(element);

if($car.val() == 'Yes')
		{
			if($('input:radio[name="Booking[hotel_booking]"]:checked').val() != 'Yes')
			{
				$('#Booking_return').parents('.row').addClass('margint85');
			}
			refreshsupp('car');
			$('#Booking_pickup').parents('.row').removeClass('hidden');
			$('#Booking_return').parents('.row').removeClass('hidden');
			$('#Booking_car_supplier').parents('.row').removeClass('hidden');
}else{
	if($('input:radio[name="Booking[hotel_booking]"]:checked').val() != 'Yes')
			{
				$('#Booking_return').parents('.row').removeClass('margint85');
			}
	$('#Booking_pickup').parents('.row').addClass('hidden');
			$('#Booking_return').parents('.row').addClass('hidden');
			$('#Booking_car_supplier').parents('.row').addClass('hidden');

			$('#Booking_carname').parents('.row').addClass('hidden');
			$('#Booking_carreason').parents('.row').addClass('hidden');
		}
	}
	function checkadditionalHotel(element) {

$hotel =  $(element);
		if($hotel.val() == 'Yes')
		{
if($('input:radio[name="Booking[car_booking]"]:checked').val() == 'Yes')
			{
				$('#Booking_return').parents('.row').removeClass('margint85');
			}
		refreshsupp('hotel');
			$('#Booking_checkin').parents('.row').removeClass('hidden');
			$('#Booking_checkout').parents('.row').removeClass('hidden');
			$('#Booking_hotel_supplier').parents('.row').removeClass('hidden');
		}else{
if($('input:radio[name="Booking[car_booking]"]:checked').val() == 'Yes')
			{
				$('#Booking_return').parents('.row').addClass('margint85');
			}
			$('#Booking_checkin').parents('.row').addClass('hidden');
			$('#Booking_checkout').parents('.row').addClass('hidden');
			$('#Booking_hotel_supplier').parents('.row').addClass('hidden');

			$('#Booking_hotelname').parents('.row').addClass('hidden');
			$('#Booking_reason').parents('.row').addClass('hidden');
		}

	}
function refreshBillable(element){
		$element = $(element);
		var id_project = $element.val();		
		$.ajax({
			url: testProjectActualsExpensesUrl,
			data: {'id_project': id_project},
			type: 'post',
			dataType: 'json',
			success: function(res){
				if (res.code == 200){			
					if (res.type == 'true')
					{
						$("#Booking_billable").val('Yes'); 
					}else{
						$("#Booking_billable").val('No'); 
					}
				}
			}
		});
	}

	function changeCategory(element) {
		$this =  $(element);
		if($this.val() == 'Project')
		{
			$('#Booking_id_project').parents('.row').removeClass('hidden');
		}else{
			$('#Booking_id_project').parents('.row').addClass('hidden');
		}			
	}
	function refreshDestination() {	
		var id = $('#Booking_id_customer').val();
		if (id)
		{
			$.ajax({
	 			type: "GET",
	 			data: {'id' : id},					
	 			url: getCitiesByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) { if(data.result != false) {
				  	 $('#Booking_destination').val(data.result);  
				  		refreshsupp('hotel'); refreshsupp('car'); 
				  	} }
		  		}
			});
		}
	}
	function refreshProjectListsProjects() {
	
		var id = $('#Booking_id_customer').val();
		console.log(id);	
		if (id)
		{
			$('#Booking_id_project').removeAttr('disabled');
			$.ajax({
	 			type: "GET",
	 			data: {'id' : id},					
	 			url: getProjectsByClientUrl, 
	 			dataType: "json",
	 			success: function(data) {
				  	if (data) {
				  		var selectOptions = '<option value=""></option>';
				  		var index = 1;
				  		$.each(data,function(id,name){
				  			var selected =   ''; 
				  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
				  		});
					    $('#Booking_id_project').html(selectOptions);
				  	}
		  		}
			});
		} else {
			$('#Booking_id_project').html('');
			$('#Booking_id_project').attr('disabled', 'disabled');
		}
	}
</script>