<div id="expenses_items"><div class="theme" style="padding-top:0px; background:none">
</div>
<div id="expenses_items_content"  class="grid border-grid">
<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-milestonesn','dataProvider'=>StatusReportMilestones::getMilestonesProvider($model->project),
						'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
					'columns'=>array(
					array('name' => 'milestone','value' => '$data->milestone','htmlOptions' => array('class' => 'column160'),'headerHtmlOptions' => array('class' => 'column160'),),
					array('class'=>'CCustomButtonColumn','template'=>' {update} {delete}','htmlOptions'=>array('class' => 'button-column'),
						'buttons'=>array(		  
			            	'update' => array('label' => Yii::t('translations', 'Edit'), 
								'imageUrl' => null,'url' => 'Yii::app()->createUrl("projects/manageMilestonen", array("id"=>$data->id))',
			            		'options' => array('onclick' => 'showMilestoneForm(this, false);return false;'),),
							'delete' => array('label' => Yii::t('translations', 'Delete'),
							'imageUrl' => null,'url' => 'Yii::app()->createUrl("projects/deleteMilestone", array("id"=>$data->id))',  
			                'options' => array('class' => 'delete'),),),),),)); ?>			
			<div class="tache-milestonen new_item_milestonen"><div style="margin-left:2%;margin-top:2%;"  onclick="showMilestoneForm(this, true);" class="newmilestonen">
					<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u></div>	</div>	</div>	</div>
<script>
function showMilestoneForm(element, newItem) {
		var url;
		if (newItem) {	url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageMilestonen');?>";
		} else {	url = $(element).attr('href');	}
		$.ajax({ type: "POST", url: url, dataType: "json", data: {'status_report':<?php echo $model->id;?>},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
					  	if (newItem) { $('.new_item_milestonen').hide(); $('.new_item_milestonen').after(data.form);
					  	} else { $(element).parents('tr').addClass('noback').html('<td colspan="12" class="noback">' + data.form + '</td>'); } } } } }); }
	function updateMilestone(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageMilestonen');?>";
		if (id != 'new') {	url += '/'+parseInt(id);	}
		$.ajax({ type: "POST",		data: $(element).parents('.new_milestonen').serialize() + '&status_report=<?php echo $model->id;?>', 	url: url, dataType: "json",
		  	success: function(data) {
			  	if (data) {
			  		if (data.status == 'saved') {			  			
					  	if (id == 'new') { $(element).parents('.tache.new').remove();		$('.new_item_milestonen').show();
					  		$.fn.yiiGridView.update('items-grid-milestonesn');  	}	
				  		$.fn.yiiGridView.update('items-grid-milestonesn');				
				  	} else { if (data.status == 'success') { $(element).parents('.tache.new').replaceWith(data.form); }  	} } } });	}
</script>
