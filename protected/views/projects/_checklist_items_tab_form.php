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

<!--
    /*
    * Author: Mike
    * Date: 03.07.19
    * Add the option of adding new check list items
    */
-->
<div class="row">
    <div id="check_list_load" class="grid-view grid-view-loading hidden"></div>

    <style>
        #add_check_list_form form{
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        #add_check_list_form form .row{
            display: flex;
        }
        #add_check_list_form form label{
            width: 30%;
            display: block;
        }
        #add_check_list_form form select,#add_check_list_form form textarea{
            width: 70%;
            display: block;
        }
    </style>
    <div class="marginr22 marginb20 hidden" id="add_check_list_form">
        <h2 class="margin20 header_title"><strong>Add check list item</strong></h2>
        <?php $form=$this->beginWidget('CActiveForm',array('action' => '/projects/AddChecklist')); ?>

        <?=CHtml::hiddenField('project_id',$model->id)?>

        <div class="row margin20">
            <?=$form->label(Checklist::model(),'descr'); ?>
            <?=$form->textArea(Checklist::model(),'descr') ?>
        </div>

        <div class="row margin20">
            <?=$form->label(Checklist::model(),'category'); ?>
            <?=$form->dropDownList(Checklist::model(),'category',Checklist::getAllCategories()) ?>
        </div>

        <div class="row margin20">
            <?=$form->label(Checklist::model(),'responsibility'); ?>
            <?=$form->dropDownList(Checklist::model(),'responsibility',Checklist::getresponsibilityList()) ?>
        </div>

        <div class="row margin20">
            <?=$form->label(Checklist::model(),'id_phase'); ?>
            <?=$form->dropDownList(Checklist::model(),'id_phase',Checklist::getAllMilestones()) ?>
        </div>

        <div class="row margin20">
            <?=CHtml::label('Status','responsibility'); ?>
            <?=CHtml::dropDownList('responsibility','',Checklist::getStatusList(),array('id' => 'responsibility')) ?>
        </div>

        <div class="row margin20">
            <?=CHtml::submitButton('Add', array('class'=>'next_submit')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <div class="tache-issue new_issue">
        <div onclick="$('#add_check_list_form').removeClass('hidden');" class="newrisk">	<u><b>+ ADD ITEM</b></u></div>
    </div>
</div>

<script type="text/javascript">
    $('#add_check_list_form form').submit(function (event) {
        event.preventDefault();
        const data = $(this).serialize();
        $('#check_list_load').removeClass('hidden');
        $.post(`${$(this).attr('action')}`,data,function (data) {
            if (data){
                $('#check_list_load').addClass('hidden');
            } else {
                alert('Error!!!');
            }
        });
    });

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