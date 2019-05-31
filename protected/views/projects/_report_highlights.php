<div id="">	<div class="theme" style="padding-top:0px; background:none">
</div>	<div id="expenses_items_content"  class="grid border-grid">
<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-highlightsn','dataProvider'=>StatusReportHighlights::getHighlightsProvider($model->project),
						'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
					'columns'=>array(
					array('name' => 'description','value' => '$data->description','htmlOptions' => array('class' => 'column160'),'headerHtmlOptions' => array('class' => 'column160'),),
					array('class'=>'CCustomButtonColumn','template'=>' {update} {delete}','htmlOptions'=>array('class' => 'button-column'),
						'buttons'=>array(			  
			            	'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("projects/manageHighlight", array("id"=>$data->id))',
			            		'options' => array('onclick' => 'showHighlightForm(this, false);return false;'),),
							'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,'url' => 'Yii::app()->createUrl("projects/deleteHighlight", array("id"=>$data->id))',  
			                	'options' => array('class' => 'delete'),),  ),	), ), )); ?>			
			<div class="tache-highlightn new_item_highlightn">	<div style="margin-left:2%;margin-top:2%;" onclick="showHighlightForm(this, true);" class="newhighlightn">
					<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>	</div>	</div>	</div>
<script>
function showHighlightForm(element, newItem) {
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageHighlight');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({type: "POST",url: url,dataType: "json",data: {'status_report':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) { $('.new_item_highlightn').hide(); $('.new_item_highlightn').after(data.form);
					  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>');
					  	} } } } });	}
	function updateHighlight(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageHighlight');?>";
		if (id != 'new') {	url += '/'+parseInt(id); }
		$.ajax({ type: "POST",	data: $(element).parents('.new_highlightn').serialize() + '&status_report=<?php echo $model->id;?>', url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {
					  	if (id == 'new') { $(element).parents('.tache.new').remove(); $('.new_item_highlightn').show(); $.fn.yiiGridView.update('items-grid-highlightsn'); 	}	
				  		$.fn.yiiGridView.update('items-grid-highlightsn');				
				  	} else { if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); } } } }	});	}
</script>
