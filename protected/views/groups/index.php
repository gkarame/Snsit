<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){	$('.search-form').toggle();	return false; });
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('groups-grid', { data: $(this).serialize()	});	return false; }); ");
$columns = array('name','description');
if(GroupPermissions::checkPermissions('groups-list','write')){
	$columns[] = array(
			'class'=>'CCustomButtonColumn',	'template'=>'{update}', 'htmlOptions'=>array('class' => 'button-column'),
			'buttons'=>array( 'update' => array( 'label' => Yii::t('translations', 'Edit Group'), 'imageUrl' => null, ), ), ); 
			}
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'groups-grid','dataProvider'=>$model->search(),'summaryText' => '',
	'selectableRows'=>1,'selectionChanged'=>'function(id){var gid= $.fn.yiiGridView.getSelection(id);location.href = "'.$this->createUrl('view').'/"+gid}',
	'pager'=> Utils::getPagerArray(),'columns'=> $columns, )); 
if(GroupPermissions::checkPermissions('groups-list','write')){ ?>
<div class="addNewGroup">
	<?php echo CHtml::link(Yii::t('translation', 'Create Group'), 'javascript:void(0);', array('onclick'=>'showInput();', 'class'=>'add-group')); ?>	
	<div id="create_input_div" class="add-group-field hidden">
		<label for="create_input"><?php echo Yii::t('translation', 'GROUP NAME');?></label>
		<input type="text" name="name" id="create_input" autocomplete='off'/>
		<a href="javascript:void(0);" onclick="saveGroup();" class="save"><?php echo Yii::t('translation', 'SAVE');?></a>
		<a href="javascript:void(0);" onclick="cancelGroup();"><?php echo Yii::t('translation', 'CANCEL');?></a>
	</div>
</div>
<script type="text/javascript">
	function showInput() { $('#create_input_div').toggleClass('hidden'); }
	function cancelGroup() { $('#create_input_div').addClass('hidden'); $('#create_input').val("");	}
	function saveGroup() { 
	if ($('#create_input').val().trim() != '') {
			$.ajax({ type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/create');?>", dataType: "json", data: {'Groups[name]' : $('#create_input').val()},
				  	success: function(data) {
				  		if (data.status == "success") { $('#create_input_div').toggleClass('hidden'); $('#create_input').val("");	
				  			$.fn.yiiGridView.update('groups-grid', { data: $(this).serialize() }); } } }); } }
</script>
<?php } ?>
