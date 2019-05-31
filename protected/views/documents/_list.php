<?php if ($documents->itemCount > 0) { ?>
<form method="post" id="docs_delete">
<?php $this->widget('zii.widgets.CListView', array(
	    'dataProvider' => $documents, 'itemView' => '_doc', 'id' => 'docs_list',
		'summaryText' => '',	'pager'=> Utils::getPagerArray(),	'template'=>'{items}{pager}',
		'viewData' => array('action' => $action)
	));
?>
</form><?php } ?>
