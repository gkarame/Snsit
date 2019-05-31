<br clear="all"><div id="expenses_items">	<div class="theme" style="padding-top:0px; background:none">
</div><div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-risks','dataProvider'=>ProjectsRisks::getRisksProvider($model->project),
						'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}','columns'=>array(							
					array('name' => 'id','header' => 'ID','value' => '$data->id','visible' => false,'htmlOptions' => array('class' => 'column70'),'headerHtmlOptions' => array('class' => 'column70'),),
					array('name' => 'risk','header' => 'Risk','value' => '$data->risk','visible' => true, 'htmlOptions' => array('class' => 'column275'), 'headerHtmlOptions' => array('class' => 'column275'),	),
					array('name' => 'priority','header' => 'Priority','value' => '$data->priority==1 ? "HIGH" :($data->priority==2? "MEDIUM": "LOW")','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),	),
					array('name' => 'planned_actions','header' => 'Planned Actions','value' => '$data->planned_actions','visible' => true, 'htmlOptions' => array('class' => 'column275'), 'headerHtmlOptions' => array('class' => 'column275'),),
					array('name' => 'status','header' => 'Status','value' => '$data->status','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),	),
					array('name' => 'responsibility','header' => 'Resp.','value' => '$data->responsibility','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),	),
					array('name' => 'privacy','header' => 'Privacy','value' => '$data->privacy','visible' => true, 'htmlOptions' => array('class' => 'column100'), 'headerHtmlOptions' => array('class' => 'column100'),	),
					array('class'=>'CCustomButtonColumn','template'=>' {update} {delete}','htmlOptions'=>array('class' => 'button-column'),
					'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
											  					$.each(response.amounts, function(i, item) { $("#"+i).html(item); });
											  					$.fn.yiiGridView.update("terms-grid"); }}',
									'buttons'=>array('update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("projects/manageRiskItem", array("id"=>$data->id))',
						            		'options' => array('onclick' => 'showRiskItemForm(this, false);return false;'),),
										'delete' => array(
											'label' => Yii::t('translations', 'Delete'),'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("projects/deleteRiskItem", array("id"=>$data->id))',  
						                	'options' => array('class' => 'delete'),),),),),)); ?>			
		<div class="tache-risk new_item_risk"><div onclick="showRiskItemForm(this, true);" class="newrisk"><u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u>	</div></div>	</div>
       <?php $form=$this->beginWidget('CActiveForm', array('id'=>'risk-form','enableAjaxValidation'=>false,'htmlOptions' => array(
				'class' => 'ajax_submit','enctype' => 'multipart/form-data','action' => Yii::app()->createUrl("projects/update", array("id"=>$model->id))),		)); ?>
		<?php $this->endWidget(); ?></div><br clear="all">
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';	var createItemRiskUrl = '<?php echo Yii::app()->createUrl("projects/createRiskItem/?id_project=$model->project"); ?>';
	var updateItemRiskUrl = '<?php echo Yii::app()->createUrl("projects/manageRiskItem"); ?>';
function updateRiskItem(element, id, url){
	$.ajax({type: "POST",data: $(element).parents('#risk-form').serialize() +'&ajax=risk-form',		url: url+'?id='+id, 	dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-risk.new').remove();	  	$('.new_item_risk').show();					  				
				  		$.fn.yiiGridView.update('items-grid-risks');
				  		$.each(data.amounts, function(i, item) {    $('#'+i).html(item); 		});				  		
				  	} else { if (data.status == 'success') { $(element).parents('.tache-risk.new').replaceWith(data.form); } }
				  	showErrors(data.errors); 	showErrors(data.alert);  	} 		}	}); }
function showRiskItemForm(element, newItem) {
	var url;
	if (newItem) {	url = createItemRiskUrl; } else {	url = $(element).attr('href');	}
	$.ajax({type: "POST",  	url: url,  	dataType: "json",  	data: {'expenses_id':modelId},  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
					if (newItem) {  	$('.new_item_risk').hide();	  	$('.new_item_risk').after(data.form);
					  } else {	$(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');	  }					  
				  }	  	} 		}	}); }
function createRiskItem(element, expensId, url){
	$.ajax({type: "POST", data: $(element).parents('#risk-form').serialize() + '&expenses_id='+expensId+'&ajax=risk-form',url: url,dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	$(element).parents('.tache-risk.new').remove();  	$('.new_item_risk').show(); 		$.fn.yiiGridView.update('items-grid-risks');
				  		$.each(data.amounts, function(i, item) { $('#'+i).html(item); });
				  	} else {
				  		if (data.status == 'success') {	$(element).parents('.tache-risk.new').replaceWith(data.form); } } } } }); }
</script>
