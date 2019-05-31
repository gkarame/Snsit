<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Trip Details');?></span>
<span class=" book_edit"><?php  if(GroupPermissions::checkPermissions('booking-list','write'))	{?>
			 <a class="header_button"  href="<?php echo Yii::app()->createAbsoluteUrl('booking/update', array('id' => $model->id));?>"><?php echo Yii::t('translations', 'Edit');?></a>	
		<?php } ?></div></span>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(str_pad($model->id, 5, '0', STR_PAD_LEFT)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode(Yii::t('translations', 'Traveler')); ?></div>
	<div class="general_col4 " id="traveler"><?php echo CHtml::encode(Users::getNameById($model->traveler)); ?></div>
</div>
<?php if($model->purpose != 'Exhibition'){?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('id_customer')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode(Booking::getName($model->id_customer)); ?></div>
		<?php if($model->purpose == 'Project'){?>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('id_project')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Booking::getProjectByIds($model->id_project)); ?></div>
		<?php }?>
	</div>
<?php }?>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('origin')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Codelkups::getCodelkup($model->origin)); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('destination')); ?></div>
	<div class="general_col4 capitalize"><?php echo CHtml::encode(Codelkups::getCodelkup($model->destination)); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('departure_date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->departure_date); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('departure_time')); ?></div>
	<div class="general_col4 " id ="departure_date"><?php echo CHtml::encode($model->departure_time); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('return_date')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->return_date); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('return_time')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->return_time); ?></div>
</div>
<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('purpose')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode($model->purpose); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('billable')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode($model->billable); ?></div>
</div>

<div class="view_row">
	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('notes')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(chunk_split($model->notes, 58, ' ')); ?></div>
	<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?></div>
	<div class="general_col4 "><?php echo CHtml::encode(Booking::getStatusLabel($model->status)); ?></div>
</div>

<div class="view_row">

	<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('travel_supplier')); ?></div>
	<div class="general_col2 "><?php echo CHtml::encode(Suppliers::getNameById($model->travel_supplier)); ?></div>
</div>


<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Additional Bookings');?></span></div>
<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('hotel_booking')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->hotel_booking); ?></div>
		<?php if($model->hotel_booking == 'Yes'){	 if($model->hotel_supplier == 'Choose another hotel'){	?>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('hotel_supplier')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->hotelname); ?></div>
		<?php }else{	?>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('hotel_supplier')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode(Suppliers::getNameById($model->hotel_supplier)); ?></div>
		<?php	}	}else{?>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('car_booking')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->car_booking); ?></div>
		<?php }?>
	</div>
<?php if($model->hotel_booking == 'Yes'){?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('checkin')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->checkin); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('checkout')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->checkout); ?></div>
	</div>
<?php }?>
<?php if($model->hotel_booking == 'Yes'){?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('car_booking')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->car_booking); ?></div>
		<?php if($model->car_booking == 'Yes'){?>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('car_supplier')); ?></div>
		<div class="general_col4 "><?php 
		if($model->car_supplier == 'Choose another supplier'){
			echo CHtml::encode($model->carname);
		}else{
			echo CHtml::encode(Suppliers::getNameById($model->car_supplier)); }
		?></div>		
		<?php }?>
	</div>
<?php } else { if($model->car_booking == 'Yes'){ ?>
<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('car_supplier')); ?></div>
		<div class="general_col2 "><?php if($model->car_supplier == 'Choose another supplier'){
			echo CHtml::encode($model->carname);
		}else{
			echo CHtml::encode(Suppliers::getNameById($model->car_supplier)); }
		 ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('pickup')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->pickup); ?></div>		
	</div>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('return')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->return); ?></div>	
	</div>
<?php } } ?>
<?php if($model->car_booking == 'Yes' && $model->hotel_booking == 'Yes'){?>
	<div class="view_row">
		<div class="general_col1"><?php echo CHtml::encode($model->getAttributeLabel('pickup')); ?></div>
		<div class="general_col2 "><?php echo CHtml::encode($model->pickup); ?></div>
		<div class="general_col3"><?php echo CHtml::encode($model->getAttributeLabel('return')); ?></div>
		<div class="general_col4 "><?php echo CHtml::encode($model->return); ?></div>
	</div>
<?php }?>
<div class="formColumn">
<div class="header_title">	<span class="red_title book"><?php echo Yii::t('translations', 'Alerts');?></span></div>
<div id="alerts" style="width:900px;padding-bottom:10px;"></div>
</div>

<!--
<div class="""slidecontainer">
  <input type="range" min="1" max="100" value="50" class="slider" id="myRange">

  <p><br/>Value: <span id="demo"></span></p>
</div>-->



<script> 
$(function() {
		refreshAlerts();
	//	var slider = document.getElementById("myRange");
	//	var output = document.getElementById("demo");
	//	output.innerHTML = slider.value;

		//slider.oninput = function() {
	//	  output.innerHTML = this.value;
		//}
	});
function refreshAlerts() 
  {
  	var departuredate= $('#departure_date').text();
  	var user=$('#traveler').text();
  	
	$.ajax({type: "POST",data: {"departuredate":departuredate, 'traveler':user},	url: "<?php echo Yii::app()->createAbsoluteUrl('booking/updateAlerts')?>", dataType: "json",
		  	success: function(data) {
			  	if (data) {	if (data.status == 'success') {	$('#alerts').html(data.alerts);	} 	}
	} });
  }
</script>