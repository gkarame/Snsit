<div class="visas" id="visas">	<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'visas-grid','dataProvider'=>$model->getVisas(),	'summaryText' => '',
		'pager'=> Utils::getPagerArray(),   'template'=>'{items}{pager}','columns'=>array(	'type',
			array('name' => 'expiry_date','value' => 'date("d/m/Y", strtotime($data->expiry_date))'),
'visa_type','duration_of_stay',array('name' => 'Country','type'  => 'raw','value'=>'UserVisas::getCountry($data->country,$data->id)','htmlOptions' => array('style' => 'text-align:left;')),
array('value' => '$data->getNotesGrid()','type'=>'raw'),		
			array('class'=>'CCustomButtonColumn','template'=>'{update} {delete}','htmlOptions'=>array('class' => 'button-column'),'buttons'=>array
('update' => array('label' => Yii::t('translations', 'Edit'), 'imageUrl' => null,'url' => 'Yii::app()->createUrl("users/manageVisa", array("id"=>$data->id))','options' => array('onclick' => 'showVisaForm(this);return false;'),),
					'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("users/deleteVisa", array("id"=>$data->id))',  'options' => array('class' => 'delete',)),),),),)); ?>
	<div class="tache new_vis">	<div onclick="showVisaForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW VISA');?></b></u></div>	</div></div>
<script type="text/javascript">
function changeInput(country,id_visa){
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('users/changeCountry');?>", 	dataType: "json",  	data: {'country':country,'id_visa':id_visa},
	  	success: function(data) {
		  	if (data) {	  }  	}	}); }
	function showVisaForm(element, newVisa) {
		if ($('#Users_firstname').val() == '' || $('#Users_lastname').val() == '' || $('#Users_username').val() == '' || $('#Users_password_new').val() == '') {
			$(element).addClass('invalid');	} else {  $(element).removeClass('invalid');	}		
		if (!$(element).hasClass('invalid')) {
			var url, data;
			if (newVisa) {
				url = "<?php echo Yii::app()->createAbsoluteUrl('users/manageVisa');?>";
				data = "id_user=<?php echo $model->id;?>";
			} else {	url = $(element).attr('href');		}
			$.ajax({type: "POST",  	url: url,  	data: data,   	dataType: "json",
			  	success: function(data) {
				  	if (data) {
					  	if (data.status == 'success') {
						  	if (newVisa) {
						  		$('.new_vis').hide();
						  		$('.new_vis').after(data.form);
						  	} else {	$(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>');	  	}
						  	createPickers($('.tache.new'));
					  	}  	}	}	});
		} else {	alert('The form is not valid!');	}	}	
	function saveVisa(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('users/manageVisa');?>";
		var data;
		if (id != 'new') {	url += '/'+parseInt(id);  } else {	data = 'id_user=<?php echo $model->id;?>';	}
		$.ajax({type: "POST",	data: $(element).parents('.new_visa').serialize()+'&'+data,url: url,  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved') {
					  	if (id == 'new') {
					  		$(element).parents('.tache.new').remove();
					  		$('.new_vis').show();
					  	}	
				  		$.fn.yiiGridView.update('visas-grid');
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache.new').replaceWith(data.form);
				  			createPickers($('.tache.new'));
				  		}  	}  	}	}	});	}
function changeCountry(value,id_visa,type){
	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('users/changeCountry');?>", 	dataType: "json",  	data: {'country':value,'id_visa':id_visa},
	  	success: function(data) { 
		  	if (data) { 
			  	if (data.status!='success'){
				  			var action_buttons = {
							        "Ok": {
										click: function() 
								        {
								            $( this ).dialog( "close" );
								        },
								        class : 'ok_button'
							        }
								}
			  				custom_alert('ERROR MESSAGE', 'Country field cannot be blank.', action_buttons);
				  			}  	}	}  }); }
</script>