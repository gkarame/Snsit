<?php Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){	$('.search-form').toggle();	return false;}); $('.search-form form').submit(function(){	$.fn.yiiGridView.update('suppliers-grid', {
		data: $(this).serialize()	});	return false;});"); ?>
<div class="search-form"><?php $this->renderPartial('_search',array('model'=>$model,)); ?></div><div class="header_title">
<?php if(GroupPermissions::checkPermissions('suppliers-list','write')){	?>
				<div class="btn" style="margin-top:-25px"><div class="wrapper_action" id="action_tabs_right">
						<div onclick="chooseActionsSuppliers();" class="action triggerAction"><u><b>ACTION</b></u></div>
					<div class="action_list actionPanel action_supplier"> 	<div class="headli"></div><div class="contentli"><div class="cover">
								<div class="li noborder"><a class="special_edit_header" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('suppliers/create');?>"><?php echo Yii::t('translations', 'NEW SUPPLIER');?></a></div>
							</div><div class="cover">	<div class="li noborder"><a class="special_edit_header" onclick="shareCheck(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('suppliers/create');?>"><?php echo Yii::t('translations', 'PRINT CHECK');?></a></div>
							</div>	<div class="cover"><div class="li noborder"><a class="special_edit_header" onclick="shareLetter(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('suppliers/create');?>"><?php echo Yii::t('translations', 'PRINT LETTER');?></a></div>
							</div></div>	<div class="ftrli"></div>   </div>    <div id="users-list" style="display:none;"></div>	 </div>	</div>	<?php }	?></div>
<?php 
	$buttons = array();	$tmp = '';
	if (GroupPermissions::checkPermissions('suppliers-list', 'write')){
		$tmp = '{update} {delete}';
		$buttons = array(
			'update' => array(	'label' => Yii::t('translations', 'Edit'),	'imageUrl' => null,	),'delete' => array(	'label' => Yii::t('translations', 'Delete'),'imageUrl' => null,	),	);	}
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'suppliers-grid','dataProvider'=>$model->search(),	'summaryText' => '',
		'selectableRows'=>1,'pager'=> Utils::getPagerArray(),   'template'=>'{items}{pager}',
		'columns'=>array(
			array('class'=>'CCheckBoxColumn', 'id'=>'checksuppliers','htmlOptions' => array('class' => 'item checkbox_grid_suppliers'),	'selectableRows'=>1,),	
			array('name' => 'name','header' => 'Name',	'value' => '$data->renderNumber()'	),
			array(	'name' => 'idType.codelkup','header' => 'Type',	'value' => 'isset($data->idType->codelkup) ? $data->idType->codelkup : ""'	),
			'main_contact',  'main_phone',
			array(	'class'=>'CCustomButtonColumn',	'template'=>$tmp,'htmlOptions'=>array('class' => 'button-column'),        'buttons'=>$buttons,		),	),)); ?>
<script type="text/javascript">
function chooseActionsSuppliers(){
	if ($('.action_list').css('display')=="none") {
		if ($('#users-list').is(':visible')) {
			$('#users-list').fadeOut(100);
			$('.deletInv').hide();
		} else {	$('.action_list').show();	}	}		
	else {
		$('.action_list').fadeOut(100);		$('.deletInv').hide();	}
	return false; }
function shareCheck(element){
	var action_buttons = {
			        "Ok": {
				        	class: 'ok_button',
				        	click: function() 
					        {
					            $( this ).dialog( "close" );
					        }
			        }
	  		}
		send = $('.checkbox_grid_suppliers input').serialize();
		if(send.length <= 0){
			custom_alert('ERROR MESSAGE', 'Please select one supplier first', action_buttons);
		}else{
			var dialog = $('.popup_list');
			$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('suppliers/createCheck');?>",dataType: "json",data: send,
				success: function(data) {
					if (data) {
						if (dialog.find('.loader').length) {
							$('.loader').hide();
						}
						if (data.status == "failure") {	dialog.html(data.form);
							dialog.addClass('popup_shareby'); dialog.css('top','50%'); dialog.css('left','50%'); dialog.css('position','absolute'); dialog.show();	}	}}});	}}
function shareLetter(element){
var action_buttons = {
			        "Ok": {
				        	class: 'ok_button',
				        	click: function() 
					        {
					            $( this ).dialog( "close" );
					        }
			        }
	  		}
		send = $('.checkbox_grid_suppliers input').serialize();
		if(send.length <= 0){	custom_alert('ERROR MESSAGE', 'Please select one supplier first', action_buttons);
		}else{
			var dialog = $('.popup_list');
			$.ajax({
				type: "POST",
				url: "<?php echo Yii::app()->createAbsoluteUrl('suppliers/createLetter');?>",
				dataType: "json",
				data: send,
				success: function(data) {
					if (data) {
						if (dialog.find('.loader').length) {	$('.loader').hide();	}
						if (data.status == "failure") {	dialog.html(data.form);		dialog.addClass('popup_shareby');	dialog.show();	} 	}}});	} }
</script>