<?php if(GroupPermissions::checkPermissions('settings-general_settings','write')){ ?>
	<div id="edit-settings"><a href="<?php echo $this->createAbsoluteUrl('settings/editSettings');?>"><?php echo Yii::t('translations', 'Edit');?></a>
	</div><div id="edit-settings"><a href="<?php echo $this->createAbsoluteUrl('settings/writequery');?>"><?php echo Yii::t('translations', 'Write Query');?></a>
	</div><?php }   $this->widget('zii.widgets.grid.CGridView', array('id'=>'settings-grid',	'dataProvider'=>$settings->search(),	'summaryText' => '',	'pager'=> Utils::getPagerArray(),
	'columns'=>array(	array('header' => 'Setting',	'name' => 'label'),	'value',	), )); ?>