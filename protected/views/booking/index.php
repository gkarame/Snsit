<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('booking-grid', {
		data: $(this).serialize()
	});
	return false;
});
"); ?>

<div class="search-form">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>

<?php 
	$buttons = array();
	$tmp = '';
	if (GroupPermissions::checkPermissions('booking-list', 'write'))
	{
		$tmp = '{update} {delete}';
		$buttons = array(
			'update' => array(
					'label' => Yii::t('translations', 'Edit'),
					'imageUrl' => null,
			),
			'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
			),
		);
	}
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'booking-grid',
		'dataProvider'=>$model->search(),
		'summaryText' => '',
		'selectableRows'=>1,
		'pager'=> Utils::getPagerArray(),
	    'template'=>'{items}{pager}',
		'columns'=>array(
			array(            
            'header'=>Yii::t('translations', 'TR#'),
            'value'=>'$data->renderTravelBookingNumber()',
			'name' => 'id',
        	),
        	array(
	            'name' => 'traveler',
	            'value' => 'Users::getUsername($data->traveler)'
	        ),
	        array(
	            'name' => 'destination',
	            'value' => 'Codelkups::getCodelkup($data->destination)'
	        ),
			array(
				'name' => 'id_customer',
				'value'	=> 'Booking::getName($data->id_customer)'
			),
			'purpose',
			array(
				'name' => 'departure_date',
				'value'	=> '$data->departure_date',
			),
			array(
				'name' => 'return_date',
				'value'	=> '$data->return_date',
			),
			array(
				'name' => 'billable',
				'value'	=> '$data->billable',
			),
			array(
				'name' => 'status',
				'value'=>'$data->getStatusLabel($data->status)',
			),
			array(
				'class'=>'CCustomButtonColumn',
				'template'=>$tmp,
				'htmlOptions'=>array('class' => 'button-column'),
				'buttons'=>$buttons,
			),
		),
)); ?>

<script type="text/javascript">
function changeInput(value,id_request){
	$.ajax({
 		type: "POST",
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('booking/update');?>",
	  	dataType: "json",
	  	data: {'status':value,'id':id_request},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {
			  		console.log('da');
			  	}
		  	}
  		}
	});
}
</script>
