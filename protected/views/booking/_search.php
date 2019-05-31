<div class="wide search" id="search-travel">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); ?>
	
	<div class="row author ">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'traveler'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'traveler',		
						'source'=>Users::getAllAutocomplete(true),
						// additional javascript options for the autocomplete plugin
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
							'class'	  => "width141",
						),
				));
				?>
			</div>
		</div>

		
    <div class="row" >
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'id_customer'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'id_customer',		
					'source'=>Customers::getAllAutocomplete(),
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold',
						'select'	=>"js: function(event, ui){ refreshProjectListsProjects(ui.item.id); }"
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width141',	),
			));
			?>
		</div>
		<?php echo $form->error($model, 'id_customer'); ?> 
	</div>
		<div class="row ">
			<div class="selectBg_search">
				<?php echo $form->labelEx($model,'id_project'); ?>
				<span class="spliter"></span>
				<div class="select_container">
					<?php echo $form->dropDownList($model, 'id_project', Projects::getAllProjectsSelect(), array('prompt' => Yii::t('translations', 'Choose project'), 'disabled' => true)); ?>
				</div>
			</div>
			<?php echo $form->error($model,'id_project'); ?>
		</div>	
		
 
	<div class="row margint10" >
		<div class="inputBg_txt">
			<?php echo $form->label($model, 'destination'); ?>
			<span class="spliter"></span>
			<?php 
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
					'model' => $model,
					'attribute' => 'destination',		
					'source'=>Booking::getAllCitiesAutocomplete(),
					'options'=>array(
						'minLength'	=>'0',
						'showAnim'	=>'fold'
					),
					'htmlOptions'	=>array(
						'onfocus' 	=> "javascript:$(this).autocomplete('search','');",
						'class'=>'width141',
					),
			));
			?>
		</div>
		<?php echo $form->error($model, 'destination'); ?> 
	</div>

		<div class="row  margint10">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'status'); ?>
				<span class="spliter"></span>
				<div class="select_container">
					<?php echo $form->dropDownList($model, 'status', Booking::getStatusList(), array('prompt'=>'')); ?>
				</div>
			</div>
		</div>

		<div class="row  margint10">
			<div class="selectBg_search">
				<?php echo $form->label($model, 'billable'); ?>
				<span class="spliter"></span>
				<div class="select_container">
					<?php echo $form->dropDownList($model, 'billable', Booking::getBillableList(), array('prompt'=>'')); ?>
				</div>
			</div>
		</div>
		
		<div class="btn" style="margin-bottom: 2%;">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>		
<?php if(GroupPermissions::checkPermissions('booking-list','write'))	{?>
			<div class="wrapper_action" id="action_tabs_right">
						<div onclick="chooseActionsSuppliers();" class="action triggerAction"><u><b>ACTION</b></u></div>
					<div class="action_list actionPanel " style="margin-top:-3%;z-index: 1000 !important; ">
				    	<div class="headli"></div>
						<div class="contentli">
							<div class="cover">
								<div class="li noborder"><a class="special_edit_header" href="<?php echo Yii::app()->createAbsoluteUrl('booking/create');?>"><?php echo Yii::t('translations', 'New Travel Request');?></a></div>
							</div>							
						</div>
						<div class="ftrli"></div>
				    </div>
				    <div id="users-list" style="display:none;"></div>
				 </div>	
			<?php  } ?>
		</div>	<div class="horizontalLine search-margin"></div>		
	<?php $this->endWidget(); ?>	
</div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
</script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/travel.js"></script>
<script>
function chooseActionsSuppliers(){
	if ($('.action_list').css('display')=="none") {
		if ($('#users-list').is(':visible')) {
			$('#users-list').fadeOut(100);
			$('.deletInv').hide();
		} else {	$('.action_list').show();	}	}		
	else {
		$('.action_list').fadeOut(100);		$('.deletInv').hide();	}
	return false; }
	function refreshProjectListsProjects(id) {
	var id_project_ts = "<?php echo $model->id_project;?>";
	if (!id) {
		id = $('#Booking_id_customer').val();
	}
	console.log(id);	
	if (id)
	{
		$('#Booking_id_project').removeAttr('disabled');
		$.ajax({
 			type: "GET",
 			data: {id : id},					
 			url: getProjectsByClientUrl, 
 			dataType: "json",
 			success: function(data) {
			  	if (data) {
			  		var selectOptions = '<option value=""></option>';
			  		var index = 1;
			  		$.each(data,function(id,name){
			  			var selected = (id == id_project_ts) ? 'selected="selected"' : ''; 
			  			selectOptions += '<option value="' + id+'"' + selected + '>'+name+'</option>';
			  		});
				    $('#Booking_id_project').html(selectOptions);
			  	}
	  		}
		});
	} else {
		$('#Booking_id_project').html('');
		$('#Booking_id_project').attr('disabled', 'disabled');
	}
}
</script>