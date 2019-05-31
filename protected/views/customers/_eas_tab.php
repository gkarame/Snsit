<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'eas-grid',	'dataProvider'=>$model->eas,	'summaryText' => '',	'pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}',	'htmlOptions' => array('class' => 'eas_grid view_tab grid-view'),
	'columns'=>array(
		array(            
            'header'=>Yii::t('translations', 'EA #'),
            'value'=>'$data->renderEANumber()',
			'name' => 'ea_number',
			'htmlOptions' => array('class' => 'column50'),
			'headerHtmlOptions' => array('class' => 'column50'),
        ),
		array(
			'name' => 'project_name',
			'value' => 'isset($data->project) ? $data->project->name : ""',
			'htmlOptions' => array('class' => 'width139'),
			'headerHtmlOptions' => array('class' => 'width139'),
		),
		array(
            'name' => 'status',
            'value'=>'$data->getStatusLabel($data->status)',
			'htmlOptions' => array('class' => 'column65'),
			'headerHtmlOptions' => array('class' => 'column65'),
        ),
        array(
			'name' => 'eCategory.codelkup',
			'header' => Yii::t('translations', 'Category'),
			'value' => 'isset($data->eCategory->codelkup) ? $data->eCategory->codelkup : ""',
			'htmlOptions' => array('class' => 'column100'),
			'headerHtmlOptions' => array('class' => 'column100'),
		),
        array(
        	'name' => 'amount',
        	'value' => 'Utils::formatNumber($data->getNetAmount(true))." USD"',
        	'htmlOptions' => array('class' => 'column90'),
        	'headerHtmlOptions' => array('class' => 'column90'),
        ),
        array(
			'name' => 'eAuthor.fullname',
			'header' => Yii::t('translations', 'Author'),
			'value' => 'isset($data->eAuthor->fullname) ? $data->eAuthor->fullname : ""',
			'htmlOptions' => array('class' => 'column90'),
        	'headerHtmlOptions' => array('class' => 'column90'),
		),
		array(
			'name' => 'created',
			'value' => 'date("d/m/Y", strtotime($data->created))',
			'htmlOptions' => array('class' => 'column50'),
			'headerHtmlOptions' => array('class' => 'column50'),
		),
		array(
			'class'=>'CCustomButtonColumn',
			'template'=>'{download} {share}',
			'htmlOptions'=>array('class' => 'button-column'),
            'buttons'=>array
            (
				'download' => array(
					'label' => Yii::t('translations', 'Download'), 
					'url' => 'Yii::app()->createUrl("eas/print", array("id"=>$data->id))',
				),
				'share' => array(
					'label' => Yii::t('translations', 'Share'), 
					'url' => 'Yii::app()->createUrl("site/shareby", array("id"=>$data->id))',
                	'options' => array(
						'onclick' => 'shareBySubmit(this, "eas");return false;',
						'class'=>'shareby_button',
					),
				),
            ),
		),
	),
));?>

