<?php
/* @var $this TimesheetsController */
/* @var $dataProvider CActiveDataProvider */
?>

<script>
    $(document).ready(function()
    {
        $('body').on('dblclick', '#timesheets-grid tbody tr', function(event)
        {
            var
                rowNum = $(this).index(),
                keys = $('#timesheets-grid > div.keys > span'),
                rowId = keys.eq(rowNum).text();

            location.href = '<?php echo Yii::app()->createUrl('timesheets/view'); ?>/' + rowId;
        });
    });
</script>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'timesheets-grid',
	'htmlOptions' => array('class' => 'oddBackgrounds'),
	'dataProvider'=>Timesheets::userList(Yii::app()->user->id),
	'summaryText' => '',
	'selectableRows'=>1,
	'selectionChanged'=>'function(id){idg = $.fn.yiiGridView.getSelection(id); location.href = "'.$this->createUrl('view').'/"+idg;}',
	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
	'columns'=>array(
		array(
            'name' => 'timesheet_cod',
			'header' => 'Time Sheet ID',
            'value' => '$data->timesheet_cod'
        ),
        array(
            'name' => 'week',
			'header' => 'Week No',
            'value' => '$data->week'
        ),
		array(
			'name' => 'week_start',
			'header' => 'Week Start',
			'value' => 'date("j/n/Y", strtotime($data->week_start))'
		),
		array(
			'name' => 'week_end',
			'header' => 'Week End',
			'value' => 'date("j/n/Y", strtotime($data->week_end))'
		),
		array(
			'name' => 'status',
			'header' => 'Status',
			'value' => '$data->status'
		),
		array(
				'name' => 'total_hours',
				'header' => 'Total Hours',
				'value' => 'Timesheets::getUserTimesheetHours($data->id)'
		)
	),
)); ?>