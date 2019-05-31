<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'invoices-grid',	'dataProvider'=>$model->invoices,	'summaryText' => '', 'pager'=> Utils::getPagerArray(),    'template'=>'{items}{pager}',
	'columns'=>array(
		array(            
            'header'=>Yii::t('translations', 'Inv #'),
            'value'=>'$data->renderInvoiceNumber()',
			'name' => 'invoice_number',
			'htmlOptions' => array('class' => 'column50'),
        ),
		array(
			'name' => 'customer',
			'value' => '$data->customer->name',
			'htmlOptions' => array('class' => 'column90'),
		),
		array(
            'name' => 'invoice_title',
            'value'=>'$data->invoice_title',
			'htmlOptions' => array('class' => 'column200'),
        ),
		array(
            'name' => 'status',
            'value'=>'$data->status',
			'htmlOptions' => array('class' => 'column65'),
        ),
        array(
			'name' => 'payment %',
            'value'=>'$data->payment_procente ."%"',
		),
        array(
        	'name' => 'amount',
        	'value'=>'Utils::formatNumber($data->gross_amount)',
        	'htmlOptions' => array('class' => 'column65'),
        ),
        array(
			'name' => 'printed_date',
			'htmlOptions' => array('class' => 'column60'),
        	'value' => '($data->printed_date)?date("d/m/Y",strtotime($data->printed_date)):""',
		),
		array(
			'class'=>'CCustomButtonColumn',
			'template'=>'{download} {share} {update} {delete}',
			'htmlOptions'=>array('class' => 'button-column'),
            'buttons'=>array(
            	'update' => array(
					'label' => Yii::t('translations', 'Edit'), 
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("invoices/view", array("id"=>$data->id))',
				),
				'delete' => array(
					'label' => Yii::t('translations', 'Delete'),
					'imageUrl' => null,
					'url' => 'Yii::app()->createUrl("invoices/delete", array("id"=>$data->id))',  
                	'options' => array(
                		'class' => 'delete',
					)
				),
				'download' => array(
					'label' => Yii::t('translations', 'Download'), 
					'url' => 'Yii::app()->createUrl("invoices/download", array("id"=>$data->id))',
					'visible' => '$data->status != Invoices::STATUS_NEW && $data->status != Invoices::STATUS_TO_PRINT ',  
				),
				'share' => array(
					'label' => Yii::t('translations', 'Share'), 
					'url' => 'Yii::app()->createUrl("site/shareby", array("id"=>$data->id))',
                	'options' => array(
						'onclick' => 'shareBySubmit(this, "invoices");return false;',
						'class'=>'shareby_button',
					),
					'visible' => '$data->status != Invoices::STATUS_NEW && $data->status != Invoices::STATUS_TO_PRINT ',  
				),
            ),
		),
	),
)); ?>
