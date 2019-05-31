<?php
$this->widget('zii.widgets.grid.CGridView', array(
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
			'name' => 'notes',
			'sortable' => false,
		),
		array(            
            'header'=>Yii::t('translations', 'Attachment'),
            'value'=>'$data->renderAttachment()',
			'name' => 'attachment',
			'sortable' => false,
        ),
		array(
			'class'=>'CCustomButtonColumn',
			'template'=>'{update} {delete}',
			'htmlOptions'=>array('class' => 'button-column'),
			'buttons'=>array
            (
            	'update' => array(
					'label' => Yii::t('translations', 'Edit'), 
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("customers/manageConnection", array("id"=>$data->id))',
            		'options' => array(
            			'onclick' => 'showConnectionForm(this);return false;'
            		),
				),
				'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("customers/deleteConnection", array("id"=>$data->id))',  
                	'options' => array(
                		'class' => 'delete',
					)
				),
            ),
		),
	),
)); ?>
