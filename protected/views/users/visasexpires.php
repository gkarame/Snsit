<div class="user-view mytabs hidden"><?php 	$this->widget('CCustomJuiTabs', array(  'options'=>array(    'collapsible'=>false,   	'active' =>  'js:configJs.current.activeTab',   ),   'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',));	?>
	<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'customers-grid','dataProvider'=> UserVisas::getVisasExpires(),'summaryText' => '','selectableRows'=>1,'pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
	'columns'=>array(
		array('value' => '$data->user->fullname','header'=>'name'),array('name' => 'type','value' => '$data->type',	'htmlOptions' => array('class' => 'capitalize'),	),
	   	array('name' => 'expiry_date',		'value' => 'Utils::formatDate($data->expiry_date)'),        
		array('value' => '$data->diff','header'=>'remaining days'),	'notes',),)); ?></div>