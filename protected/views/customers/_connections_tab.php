<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'connections-grid',
	'dataProvider'=>$model->getConnections(),
	'summaryText' => '',
	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',
	'columns'=>array(
		'name',
		array(
			'name' => 'cType.codelkup',
			'header' => 'Connection Type',
			'value' => 'isset($data->cType->codelkup) ? $data->cType->codelkup : ""'
		),
		'server_name',
		array(
			'name' => 'password',
			'sortable' => false,
		),
		array(            
            'header'=>Yii::t('translations', 'Attachment'),
            'value'=>'$data->renderAttachment()',
			'name' => 'attachment',
			'sortable' => false,
        ),
	),
)); ?>
