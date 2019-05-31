<?php Yii::app()->clientScript->registerScript('getChecklist', "$('.search-form-checklist form').submit(function(){
	$.fn.yiiGridView.update('Checklist-grid', {		data: $(this).serialize()	});	return false; });"); ?>
<div class="search-form-checklist"><?php $this->renderPartial('_searchChecklist',array(	'model'=>$model,)); ?></div>
<?php $tmp = ''; $buttons = array();
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'Checklist-grid','dataProvider'=>$model->getChecklist(),'summaryText' => '',
	'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('name' => 'idChecklist.description','header' => 'Checklist','htmlOptions'=>array('class' => 'black'),'value' => '$data->idChecklist->descr'),
		array('name' => 'idChecklist.category','header' => 'category','htmlOptions'=>array('class' => 'width131'),'value' => '$data->idChecklist->category'),
		array('name' => 'idChecklist.responsibility','header' => 'Responsibility','htmlOptions'=>array('class' => 'width100'),'value' => '$data->idChecklist->responsibility'),
		array('name' => 'idChecklist.phase','header' => 'Phase','value' => 'Milestones::getMilestoneDescriptionShort($data->idChecklist->id_phase)'),
		array('name' => 'status','header' => 'Status','type'  => 'raw',	'value' => '(Checklist::checkpermperItem($data->id, $data->id_project)  && $data->status!=\'Completed\' )? Checklist::getStatus($data->id,$data->status,$data->id_project):$data->status'),),)); ?>
			<script type="text/javascript">
	function showChecklistForm(element, newConn) {
		if (false) {	$(element).addClass('invalid');	} else {	$(element).removeClass('invalid');	}		
		if (!$(element).hasClass('invalid')) {	var url;	url = $(element).attr('href');
			$.ajax({	type: "POST", url: url, data: 'id_project='+<?php echo $model->id;?>, dataType: "json",
			  	success: function(data) {
				  	if (data) { if (data.status == 'success'){ $(element).parents('tr').addClass('noback').html('<td colspan="6" class="noback">' + data.form + '</td>'); } } } });
		}else{	alert('The form is not valid!');} }	
	function saveChecklist(element, id) {
		var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/manageChecklist');?>";
		if (id != 'new') {	url += '/'+parseInt(id);	}		
		$.ajax({type: "POST",data: $(element).parents('.new_checklist').serialize(),url: url,dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved'){ $.fn.yiiGridView.update('Checklist-grid');
				  	} else {	if (data.status == 'success') {	$(element).parents('.tache.new').replaceWith(data.form); } } } } }); }
	function changeInputchecklist(value,id_checklist,id_project){
		$.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('projects/manageChecklist');?>", dataType: "json",
		  	data: {'value':value,'id_checklist':id_checklist,'id_project':id_project},
		  	success: function(data) {
			  	if (data) { if (data.status == 'saved') { $.fn.yiiGridView.update('Checklist-grid'); } } } });	}	
</script>