 <div class="supportplan">
<span> <?php if(isset($model->starting_date)){ echo "<span  style=\"font: bold 12px Arial!important;\">Contract Starting Date: ".date("d/m/Y", strtotime($model->starting_date))."</span>"; }else{ echo "<span style=\"font: bold 12px Arial!important;\">Contract starting date is not set.</span>";} ?></span>
 <?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-services','dataProvider'=>$model->maintenanceServices,
					'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','afterAjaxUpdate' => 'js:function() {panelClip(".item_clip");panelClip(".term_clip");}',
					'columns'=>array(						
						array('header'=> Yii::t('translations', Maintenance::getSupportServiceDesc($model->support_service).' Services'),'value' => 'MaintenanceServices::getDescription($data->id_service, $data->limit)','type'=>'raw','htmlOptions'=>array('class' => 'black'),),
						array('header' => 'limit','header'=> Yii::t('translations', 'Quota'),'value'=>'MaintenanceServices::getLimit($data->id,$data->id_service, $data->field_type,$data->limit)','type'=>'raw',),	
						array('header' => 'Actuals','header'=> Yii::t('translations', 'actuals'),'type'=>'raw','value'=>'MaintenanceServices::getActual($data->id,$data->id_service, $data->field_type )',),							
						array('class'=>'CCustomButtonColumn','template'=>'{delete}','htmlOptions'=>array('class' => 'button-column'),
							            'buttons'=>array(
											'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("Maintenance/deleteService", array("id"=>$data->id))',
											),),),),));  ?>
<div class="tache new_item" id="services_newitem"><div onclick="showServiceForm(this, true);" class="newtask"><u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>	</div>
<script type="text/javascript">
function showdropdown(){	document.getElementById('inv_type').style.visibility="visible";	}
function changeInput(value,id){
	$.ajax({ type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('Maintenance/changeInput');?>", 
		  	dataType: "json",  	data: {'value':value,'id':id}, success: function(data){ }		});	}
function changeInput2(value,id){
	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('Maintenance/changeInput2');?>",dataType: "json",data: {'value':value,'id':id},
		  	success: function(data) { } });	}
function showServiceForm(element, newService) {	var url;
		if (newService){ url = "<?php echo Yii::app()->createAbsoluteUrl('Maintenance/manageService');?>";
		} else { url = $(element).attr('href');	}
		$.ajax({type: "POST",url: url,dataType: "json",data: {'id':<?php echo $model->id_maintenance; ?> },
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newService){ $('#services_newitem').hide();$('#services_newitem').after(data.form);
					  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>'); } } } } });	}
	function updateService(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('maintenance/manageService');?>";		
		$.ajax({type: "POST",data: $(element).parents('.items_fieldset').serialize() + '&MaintenanceServices[id_contract]=<?php echo $model->id_maintenance;?>',					
		  	url: url,dataType: "json",
			success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved'){ $.fn.yiiGridView.update('items-grid-services'); $('#services_form').hide(); $('#services_newitem').show();				  		 		
				  	} else if (data.status == 'bad') {	$.fn.yiiGridView.update('items-grid-services'); $('#services_form').hide(); $('#services_newitem').show();
				  		 		var action_buttons = {
								        "Ok": {
											click: function() 
									        {
									            $( this ).dialog( "close" );
									        },
									        class : 'ok_button'
								        }
									}
					  			custom_alert('ERROR MESSAGE', "The required service is already assigned.", action_buttons);		
				  	} } } }); }
</script>