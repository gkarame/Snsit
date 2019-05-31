<?php
/* @var $this MaintenanceController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('deployments-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<div class="search-form" style="overflow: inherit;">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<?php $searchArray = Utils::getSearchSession();?>
<!-- <div class="header_title" id="export">	
	<a>Export to Excel</a>

</div>-->

</div><!-- search-form -->
<?php
	
	$tmpl = '{delete}';
		$butoane = array
            (
            	'delete' => array(
            		'label' => Yii::t('translations', 'Delete'),
            		'imageUrl' => null,		
            	),
               
            );
		
	

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'deployments-grid',
	'dataProvider'=>$model->search(),
	'summaryText' => '',
	'selectableRows'=>1,
	//'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
	'columns'=>array(
		array(    
			'class'=>'CCheckBoxColumn',        
			'id'=>'checkinvoice',
			'htmlOptions' => array('class' => 'item checkbox_grid_invoice'),
			'selectableRows'=>2,
		),
		array(
		    'name' => 'dep_no',
			'value'=>'$data->renderDepNumber()',
			'headerHtmlOptions' => array('class' => 'column50'),
		),
		array(
		    'name' => 'id_customer',
			'value'=>'Customers::getNameById($data->id_customer)',
			'headerHtmlOptions' => array('class' => 'column100'),
		),
		array(
		    'name' => 'dep_date',
			'value'=>'empty($data->dep_date)? " " : date(\'d/m/Y\', strtotime($data->dep_date))',
			'headerHtmlOptions' => array('class' => 'column85'),
		),
		array(
		    'name' => 'module',
			'value'=>'$data->module',
			'headerHtmlOptions' => array('class' => 'column50'),
		),
		array(
		    'name' => 'infor_version',
			'value'=>'codelkups::getCodelkup($data->infor_version)',
			'headerHtmlOptions' => array('class' => 'column120'),
		),
		
		
		array(
		    'name' => 'dep_version',
			'value'=>'$data->dep_version',
			'headerHtmlOptions' => array('class' => 'column110'),
		),
		
		array(
		    'name' => 'source',
			'value'=>'Projects::getNameById($data->source)',
			'headerHtmlOptions' => array('class' => 'column100'),
		),
		array(
		    'name' => 'assigned_srs',
			'value'=>'$data->assigned_srs',
			'headerHtmlOptions' => array('class' => 'column120'),
		),
		array(
		    'name' => 'user',
			'value'=>'Users::getNameById($data->user)',
			'headerHtmlOptions' => array('class' => 'column50'),
		),
		
	/*	array(
			'class' => 'CCustomButtonColumn',
			'template'=>$tmpl,
			'htmlOptions'=>array('class' => 'button-column','style'=>'text-align:left;'),
            'buttons'=>$butoane,
		),*/
	),
)); ?>


<script type="text/javascript">

$(document).ready(function(){
});


function createContract(){
		$.ajax({
	 		type: "POST",
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('deployments/create');?>",
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('.action_list').hide();
				  		
			  	}
	  		}
	  	}
		});
}

function exportexcel(){
		$.ajax({
 		type: "POST",
 		data: $('#search_deployments').serialize()  + '&ajax=deployments-form',					
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('deployments/getExcel');?>", 
	  	dataType: "json",
	  	success: function(data) {
			  	if(data.success == 'success')
			  	{
			  		window.location = "<?php echo Yii::app()->createAbsoluteUrl("site/download", array('file'=>Utils::getFileExcelDep(true)));?>";
			  			$('.action_list').hide();
			  	}
			  	
  		}
	});
}	
</script>