<?php Yii::app()->clientScript->registerScript('search', "$('.search-form form').submit(function(){	$.fn.yiiGridView.update('requests-grid', {		data: $(this).serialize()	});	return false; }); "); $ptrdata = '';
foreach(Requests::getAllStatus() as $k=>$v){	$ptrdata .= '\''.$k.'\':\''.$v.'\','; } $ptrdata = '{'.substr($ptrdata, 0, -1).'}'; ?>
<div class="search-form"><?php $this->renderPartial('_search',array('model'=>$model,)); ?></div>
<?php	$cls = 'editable_select'; 	$this->widget('zii.widgets.grid.CGridView', array('id'=>'requests-grid','dataProvider'=>$model->search(),	'summaryText' => '',
	'selectableRows'=>1,'pager'=> Utils::getPagerArray(),   'template'=>'{items}{pager}','columns'=>array(
		array('name' => 'request #','value' => 'Utils::paddingCode($data->id)'),
		array('name' => 'user_id','header' => 'User name','value' => 'Users::getUsername($data->user_id)'),
		array('name' => 'type','header' => 'Type','value' => 'Requests::getTypeStatus($data->type)','htmlOptions' => array('class' => 'column120')),
		array('name' => 'startDate','header' => 'From','value' => 'Utils::formatDate($data->startDate)'),
		array('name' => 'endDate','header' => 'To','value' => 'Utils::formatDate($data->endDate)'),
		array('name' => 'halfday','header' => 'halfday','value' => '$data->halfday == 1 ? \'Yes\' : \'No\''),
		array('name' => 'status','header' => 'STATUS','type'=>'raw','value' => 'Requests::getStatusforApproval($data->id,$data->status,$data->user_id)','htmlOptions' => array('class' => 'column120')),
		array('class'=>'CCustomButtonColumn','template'=>'{update}','htmlOptions'=>array('class' => 'button-column'),'buttons'=>array(
				'update' => array('label' => Yii::t('translations', 'Edit'),'imageUrl' => null,	'url' => 'Yii::app()->createUrl("requests/update/?id=".$data->id."&status=".$data->status)',),),),), )); ?>
<script type="text/javascript">
	var ptrdata = <?php echo $ptrdata?>;
	var request = '<?php echo Yii::app()->createAbsoluteUrl('requests/update/');?>';
function changeInput(value,id_request){
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('requests/update');?>", 	dataType: "json", 	data: {'status':value,'id':id_request},
	  	success: function(data) {
		  	if (data) {	  	if (data.status == 'success') {  		console.log('da'); 	}  	}	} }); }
</script>
