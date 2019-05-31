<div class="mytabs expenses_edit">	<div class="no-border " style="margin-top:-7px;" ><?php echo "Display Closed Actions";?>
		<input  id="chech_<?php echo $id_sma?>" type="checkbox" <?php echo $checkv;?>  onclick="displayclosed('<?php echo  $id_sma;?>');"/>
	</div><div class="header_export" id="export" style="margin-top:-30px;">	<a onclick="getExcel()">Export to Excel</a>	</div><div id="popupavoid"> 
			<div class='titre red-bold'>Description</div> <div class='closereason'> </div>	<div class='reasoncontainer'>
				<textarea id="solution_message" style="width:270px;height:120px;resize:none;" name="solution_message"></textarea>	</div> 
			<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'SetDescription();return false;','id'=>'createbut')); ?>
	</div></div><div id="popupavoidsol"> <div class='titre red-bold'>Suggested Solution</div> 
			<div class='closereason'> </div><div class='reasoncontainer'>
				<textarea id="solution_messagesol" style="width:270px;height:120px;resize:none;" name="solution_messagesol"></textarea>	</div> 
			<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'SetSolution();return false;','id'=>'createbut')); ?>
	</div></div>	<div id="expenses_items">	<div class="theme" style="padding-top:0px; background:none"><b><?php echo Yii::t('translations', ' ');?></b></div>
		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('ext.groupgridview.GroupGridView', array('id'=>'items-grid-smactions','dataProvider'=>$model->searchSmaActions($instance, $customer, $id_sma),
						'summaryText' => '','extraRowColumns' => array('description','suggested_sol'),
						'extraRowExpression' => '"<div style=\"color:black !important;text-transform: none;font-size: 0.95em !important;\"><div style=\"font-weight: 600;float:left;padding-right:5px;\">Description: </div>  ".Sma::formatdescr($data->description)."<br/></div>?<div style=\"color:black !important;text-transform: none;font-size: 0.95em !important;\"><div style=\"font-weight: 600;float:left;padding-right:5px;\">Suggested Solution:</div>  ".Sma::formatSol($data->suggested_sol)."</div>?".$data->id.""',
      					'extraRowPos' => 'below','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
						'columns'=>array(
							array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'checkbox_grid_invoice check_invoice'),	'selectableRows'=>2,'headerHtmlOptions' => array('class' => 'width10'),),
					 array('header'=>'#','value'=>'$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)','htmlOptions' => array('class' => 'width55'),'headerHtmlOptions' => array('class' => 'width55'),),		
					 array('name' => 'id_sma','header' => 'SMA','value' => 'Utils::paddingCode($data->id_sma)','visible' => true,'htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
					 array(	'header' => 'title','value' => '$data->title','visible' => true,'htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
					array('header' => 'severity','value' => 'SmaActions::getSeverityLabel($data->severity)','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),), 
					array('header' => 'tier','value' => '$data->tier','visible' => true,'htmlOptions' => array('class' => 'column90'),'headerHtmlOptions' => array('class' => 'column90'),),
					array('header' => 'responsibility','value' => '$data->responsibility','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),),
					array('header' => 'logged by','value' => 'Users::getNameById($data->addwho)','visible' => true, 
						'htmlOptions' => array('class' => 'column130 '),'headerHtmlOptions' => array('class' => 'column130 '),),
					array( 'name' => 'status',	'header' => 'Status','value' => 'SmaActions::getStatusLabel($data->status)','visible' => true, 
						'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),	),
					array('header' => 'logged on','value' => 'isset($data->adddate)? date("d/m/Y",strtotime($data->adddate)): ""','visible' => true,'htmlOptions' => array('class' => 'column100 '), 'headerHtmlOptions' => array('class' => 'column100'),),
					array( 'name' => 'eta',	'header' => 'eta','value' => 'date_format(DateTime::createFromFormat(\'Y-m-d\', $data->eta), \'d/m/Y\')','visible' => true, 	'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100 paddingl10'),),
 					array('header' => 'closure date','value' => 'isset($data->close_date)? date("d/m/Y",strtotime($data->close_date)): ""','visible' => true, 'htmlOptions' => array('class' => 'column100 '), 'headerHtmlOptions' => array('class' => 'column100'),
					),
					array
				(			'class'=>'CCustomButtonColumn',			'template'=>' {update} {delete}',			'htmlOptions'=>array('class' => 'button-column'),			'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
											  					$.each(response.amounts, function(i, item) {
											  		    			$("#"+i).html(item);
											  					});
											  					$.fn.yiiGridView.update("terms-grid");
											  				}}',
									'buttons'=>array(					  
						            	'update' => array('label' => Yii::t('translations', 'Edit'), 'imageUrl' => null,'url' => 'Yii::app()->createUrl("sma/manageSmactionItem", array("id"=>$data->id))',
						            		'options' => array('onclick' => 'showSmactionItemForm(this, false);return false;'),	),
										'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("sma/deleteSmactionItem", array("id"=>$data->id))',  
						                	'options' => array('class' => 'delete','onclick' => 'validatePivileges(this, false);return false;'),),),),),)); ?>			
			<div class="tache-smaction new_item_smaction"><div onclick="showSmactionItemForm(this, true);" class="newsmaction"><u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>
			</div>	</div>	
        <?php $form=$this->beginWidget('CActiveForm', array('id'=>'smaction-form','enableAjaxValidation'=>false,'htmlOptions' => array(
				'class' => 'ajax_submit','enctype' => 'multipart/form-data',	'action' => Yii::app()->createUrl("sma/update", array("id"=>$model->id))),	)); ?>	
		<?php $this->endWidget(); ?></div></div>
<script> 
	var modelId = '<?php echo $model->id;?>';
	var createItemSmactionUrl = '<?php echo Yii::app()->createUrl("sma/createSmactionItem/?id_sma=$id_sma"); ?>';
	var updateItemSmactionUrl = '<?php echo Yii::app()->createUrl("sma/manageSmactionItem"); ?>';
$(".closereason").click(function() {	$('#popupavoid').hide();	$('#popupavoidsol').hide();		});
$(document).ready(function() {	$('#popupavoid').hide();	$('#popupavoidsol').hide();
		$(document).on('click', '.plus-minus', function() {
			if ($(this).attr('data-collapsed') == 1){
				$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');
				$(this).parents('.project_thead').nextUntil('.project_thead').hide();
			}else{
				$(this).attr('data-collapsed', '1').css('background-position', '0px -22px');
				$(this).parents('.project_thead').nextUntil('.project_thead').show();
			}	});
		collapseOrNot();});	
	function collapseOrNot(){
		$('.plus-minus').each(function(index){
			$(this).attr('data-collapsed', '0').css('background-position', '0px -1px');
			$(this).parents('.project_thead').nextUntil('.project_thead').hide();	});	}
	function SetSolution(){
		var msg2 = document.getElementById('solution_messagesol').value;
		$.ajax({type: "POST",	data: {'solution':msg2},	url: "<?php echo Yii::app()->createAbsoluteUrl('sma/UpdateSolution');?>", dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			if (data.status == 'success' ) {	$('#popupavoidsol').hide();		$.fn.yiiGridView.update('items-grid-smactions');	}	}  	}	});	}
	function SetDescription(){		
		var msg2 = document.getElementById('solution_message').value;
		$.ajax({type: "POST",data: {'description':msg2},url: "<?php echo Yii::app()->createAbsoluteUrl('sma/UpdateDescription');?>", 	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
				  	if (data.status == 'success' ) {	$('#popupavoid').hide();	$.fn.yiiGridView.update('items-grid-smactions');	}	} 	}	});	}
function showPopSol(id){
		$.ajax({type: "POST",	url: "<?php echo Yii::app()->createAbsoluteUrl('sma/updatesolDisplay');?>",	  	dataType: "json",  	data: {'sma_action':id},
		  	success: function(data) {
		  		if (data.status == 'success') {		$('#popupavoidsol').stop().show();		$('#solution_messagesol').val(data.message);
				  		}else{
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
						  }	} }); }
function showPop(id){
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('sma/updateDescriptionDisplay');?>", dataType: "json", 	data: {'sma_action':id},
		  	success: function(data) {
		  		if (data.status == 'success') {	$('#popupavoid').stop().show();	$('#solution_message').val(data.message);
				  		}else{
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
						  }	} }); }
function displayclosed(sma) {
			if ($('#chech_'+sma).is(':checked')){	check='checked';	}else{	check='';	}
			$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('sma/updateActionDisplay');?>",   	dataType: "json", 	data: {'sma':sma,'check':check},
		  	success: function(data) { 		$.fn.yiiGridView.update('items-grid-smactions'); 		}	});	}
function updateSmactionItem(element, id, url){
	$.ajax({type: "POST",data: $(element).parents('#smaction-form').serialize() +'&ajax=smaction-form',url: url+'?id='+id, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-smaction.new').remove();
					  	$('.new_item_smaction').show();				  				
				  		$.fn.yiiGridView.update('items-grid-smactions');
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-smaction.new').replaceWith(data.form);
				  		}else{
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
						  }	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}	}	}); }
function validatePivileges(element, newItem) {
	var url;	url = $(element).attr('href');	
	$.ajax({type: "POST", 	url: url,  	dataType: "json",  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {					  
				  }else{
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
				  }  	} 		}	}); }
function showSmactionItemForm(element, newItem) {
	var url;
	if (newItem) {	url = createItemSmactionUrl;	} else {	url = $(element).attr('href');	}
	$.ajax({type: "POST", 	url: url, 	dataType: "json",  	data: {'expenses_id':modelId},
  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {  	$('.new_item_smaction').hide();	  	$('.new_item_smaction').after(data.form);
					  } else {		$(element).parents('tr').addClass('noback').html('<td colspan="16" class="noback">' + data.form + '</td>');	  }					  
				  }else{
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
				  }  	} 	} });}
function getExcel(){
	if ($('.checkbox_grid_invoice input').serialize() !=''){
		window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('sma/getExcel');?>/?"+$('.checkbox_grid_invoice input').serialize());
	}else{
	  		var action_buttons = {
		        "Ok": {
					click: function() 
			        {
			            $( this ).dialog( "close" );
			        },
			        class : 'ok_button'
		        }
			}
  			custom_alert('ERROR MESSAGE', "You have to select at least one action!", action_buttons);
	} }
function createSmactionItem(element, expensId, url){
	$.ajax({type: "POST",data: $(element).parents('#smaction-form').serialize() + '&expenses_id='+expensId+'&ajax=smaction-form', url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {  	$(element).parents('.tache-smaction.new').remove();	  	$('.new_item_smaction').show();		$.fn.yiiGridView.update('items-grid-smactions');
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});	
				  	} else {
				  		if (data.status == 'success') {		$(element).parents('.tache-smaction.new').replaceWith(data.form);
				  		}else{
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
				  } 	}
				  showErrors(data.errors);
				  showErrors(data.alert);  	}		}		});}
</script>
