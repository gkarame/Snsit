<div class="mytabs expenses_edit">
	
	<div id="expenses_items">
		<div class="theme" style="padding-top:0px; background:none"><b><?php echo Yii::t('translations', ' ');?></b></div>
		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array(
						'id'=>'items-grid-scenarios',
						'dataProvider'=>$model->searchScenarios($id_project),
						'summaryText' => '',
						'pager'=> Utils::getPagerArray(),
						'template'=>'{items}{pager}',
						'columns'=>array(
							
					 array(
            'header'=>'Scenario#',
            'value'=>'$this->grid->dataProvider->pagination->currentPage * $this->grid->dataProvider->pagination->pageSize + ($row+1)',
        		'htmlOptions' => array('class' => 'column100'), 
						'headerHtmlOptions' => array('class' => 'column100'),
        ),
							array(
						'name' => 'scenario',
						'header' => 'Scenario',
						'value' => '$data->scenario',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column275'), 
						'headerHtmlOptions' => array('class' => 'column275'),
					),
				 
					array(
						'name' => 'status',
						'header' => 'Status',
						'value' => '$data->status ==0? "Pending":"Completed"',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column100'), 
						'headerHtmlOptions' => array('class' => 'column100'),
					),
					
					
			 
				array
				(
									'class'=>'CCustomButtonColumn',
									'template'=>' {update} {delete}',
									'htmlOptions'=>array('class' => 'button-column'),
									'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
																// update amounts
											  					$.each(response.amounts, function(i, item) {
											  		    			$("#"+i).html(item);
											  					});
											  					$.fn.yiiGridView.update("terms-grid");
											  				}}',
									'buttons'=>array
						            (
						  
						            	'update' => array(
											'label' => Yii::t('translations', 'Edit'), 
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("projects/manageScenarioItem", array("id"=>$data->id))',
						            		'options' => array(
						            			'onclick' => 'showScenarioItemForm(this, false);return false;'
						            		),
										),
										'delete' => array(
											'label' => Yii::t('translations', 'Delete'),
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("projects/deleteScenarioItem", array("id"=>$data->id))',  
						                	'options' => array(
						                		'class' => 'delete'
											),
										),
						            ),
								),
							),
						)); ?>
			
			<div class="tache-scenario new_item_scenario">
				<div onclick="showScenarioItemForm(this, true);" class="newscenario">
					<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u>
				</div>
			</div>
		
		</div>
		
        <?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'scenario-form',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array(
				'class' => 'ajax_submit',
				'enctype' => 'multipart/form-data',
				'action' => Yii::app()->createUrl("projects/update", array("id"=>$model->id))
			),
		)); ?>
		
		
		
	
		<?php $this->endWidget(); ?>
	</div>
</div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
	var createItemScenarioUrl = '<?php echo Yii::app()->createUrl("projects/createScenarioItem/?id_project=$id_project"); ?>';
	var updateItemScenarioUrl = '<?php echo Yii::app()->createUrl("projects/manageScenarioItem"); ?>';


function updateScenarioItem(element, id, url)
{
	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#scenario-form').serialize() +'&ajax=scenario-form',				
	 		url: url+'?id='+id, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-scenario.new').remove();
					  	$('.new_item_scenario').show();
					  				
				  		$.fn.yiiGridView.update('items-grid-scenarios');
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-scenario.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}



function showScenarioItemForm(element, newItem) 
{

	 
	var url;
	if (newItem) {
		url = createItemScenarioUrl;
	} else {
		url = $(element).attr('href');
	}

	 
	$.ajax({
 		type: "POST",
	  	url: url,
	  	dataType: "json",
	  	data: {'expenses_id':modelId},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {
					  	$('.new_item_scenario').hide();
					  	$('.new_item_scenario').after(data.form);
					  } else {
							$(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
					  }
					  
				  }
		  	}
  		}
	});
}




function createScenarioItem(element, expensId, url)
{


	$.ajax({
	 		type: "POST",
	 		data: $(element).parents('#scenario-form').serialize() + '&expenses_id='+expensId+'&ajax=scenario-form',				
	 		url: url, 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-scenario.new').remove();
					  	$('.new_item_scenario').show();
					  		$.fn.yiiGridView.update('items-grid-scenarios');
					 	// update amounts
				  		$.each(data.amounts, function(i, item) {
				  		    $('#'+i).html(item);
				  		});
				  				
				  		
				  	} else {
				  		if (data.status == 'success') {
				  			$(element).parents('.tache-scenario.new').replaceWith(data.form);
				  		}
				  	}
				  	showErrors(data.errors);
				  	showErrors(data.alert);
			  	}
	  		}
		});

}
</script>
