<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){	$('.search-form').toggle();	return false; });
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('expenses-grid', {	data: $(this).serialize() });	return false; }); "); ?>
<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search_approved',array('model'=>$model,)); ?>
 
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'expenses-grid','dataProvider'=>$model->search(),'summaryText' => '',
	'pager'=> Utils::getPagerArray(), 'template'=>'{items}{pager}',
	'columns'=>array(
		array('class'=>'CCheckBoxColumn','id'=>'inv','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,),
		array( 'header'=>Yii::t('translations', 'SHEET #'), 'value'=>'$data->renderExpensesNumberApproval()', 'name' => 'expenses_number', 'type'=> 'raw',
			'htmlOptions' => array('class' => 'column75'), 'headerHtmlOptions' => array('class' => 'column75'),  ),
		array(	'header'=>Yii::t('translations', 'USER'), 'name' => 'user.username', 'value'=>'Users::getUsername($data->user_id)',	),
		array( 'header'=>Yii::t('translations', 'CUSTOMER'), 'value' =>'$data->customer->name', 'name' => 'customer_id' ),
		array( 'header'=>Yii::t('translations', 'PROJECT NAME'), 'value' =>'(isset($data->training))? Trainings::getName($data->project_id):$data->project->name',
			'name' => 'project.name' ),
		array( 'header'=>Yii::t('translations', 'STATUS'), 'name' => 'status', 'value'=>'$data->status', ),
		array( 'header'=>Yii::t('translations', 'FROM'), 'name' => 'startDate', 'value'=>'date("d/m/Y",strtotime($data->startDate))', ),
		array('header'=>Yii::t('translations', 'TO'), 'name' => 'endDate', 'value'=>'date("d/m/Y",strtotime($data->endDate))', ),
		array( 'header'=>Yii::t('translations', 'AMOUNT'), 'name' => 'total_amount', 'value'=>'Utils::formatNumber($data->total_amount)', ),
		array( 'header'=>Yii::t('translations', 'BILLABLE'), 'name' => 'billable_amount', 'value'=>'Utils::formatNumber($data->billable_amount)', ),
		array( 'header'=>Yii::t('translations', 'PAYABLE'), 'name' => 'payable_amount', 'value'=>'Utils::formatNumber($data->payable_amount)', ), ), )); ?>
<script> var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; </script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>
<?php /*Yii::app()->clientScript->registerScript('gridclick', "$('#expenses-grid table tbody tr td:first-child').click(function()
{
        location.href = '".$this->createUrl('/expenses/view')."/'+parseInt($(this).text())+'?option=approval';
});");*/
?>
<script type="text/javascript">	$('#mylink').attr('href', "<?php echo Yii::app( )->getBaseUrl( ); ?>"+"/expenses/generateTransfer "); 

function printexpenses(){ 

	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('expenses/printSelected');?>",		  	dataType: "json",
		  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) { 
							 $('.action_list').hide();
							 window.open(data.url, 'Download');  
					} } }); }

function approve(){ 

	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('expenses/multiApproval');?>",		  	dataType: "json",
		  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) { 
							 $('.action_list').hide();$.fn.yiiGridView.update('expenses-grid');  

					} } }); }




function payexp(){ 

	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('expenses/multipay');?>",		  	dataType: "json",
		  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) { 
							 $('.action_list').hide();$.fn.yiiGridView.update('expenses-grid');  
					} } }); }


function payexpprint(){ 

	$.ajax({type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('expenses/multipayprint');?>",		  	dataType: "json",
		  	data:  $('.checkbox_grid_invoice input').serialize(),
		  	success: function(data) {
			  	if (data) { 
							 $('.action_list').hide(); window.open(data.url, 'Download'); $.fn.yiiGridView.update('expenses-grid');  
					} } }); }


 function checkpay(){ 
 	$('.action_list').hide();
 	buttons = {"YES": {class: 'yes_button',click: function() {     $( this ).dialog( "close" );payexpprint(); }}
			,"NO": {class: 'no_button',click: function() {     $( this ).dialog( "close" ); payexp(); }}} 
		custom_alert("DELETE MESSAGE", "Do you want to print the bank transfer?", buttons);
	}



</script>