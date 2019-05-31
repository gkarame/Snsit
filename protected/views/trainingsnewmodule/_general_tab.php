<div class="mytabs training_edit">	<div id="training_header" class="edit_header">	<div class="header_title">	
	<span class="red_title"><?php echo Yii::t('translations', 'Training HEADER');?></span>	<?php if(GroupPermissions::checkPermissions("general-trainings","write")) {	?>	
	<a class="tabs_extra extra_edit2" id="extra_edit_training" onclick="showHeader(this);return false;" href="<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/updateheader', array('id' => $model->idTrainings)).'"  title="'.Yii::t('translations', 'Edit Training')?>">Edit Training</a>
	<?php 	}	?></div>	<div class="header_content tache">		<?php $this->renderPartial('_header_content', array('model' => $model));?>		</div>
		<div class="hidden edit_header_content tache new">	</div>	<br clear="all" />		<br clear="all" />		<br clear="all" />		
	</div>
<div id="budget_record"  class="grid border-grid">
<?php $provider = TrainingEas::getEasProvider($model->idTrainings);
$eas = $provider->getData();
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'budget-record-grid',
	'dataProvider'=>$provider,
	'summaryText' => '',
	'pager'=> Utils::getPagerArray(),
	'template'=>'{items}{pager}',
	'columns'=>array(
		array('header'=>Yii::t('translations', 'EA #'),'value'=>'$data->renderEANumber()','name' => 'ea_number','htmlOptions' => array('class' => 'column50'),'headerHtmlOptions' => array('class' => 'column50'),),
        array('header'=>Yii::t('translations', 'Customer'),	'value'=>'$data->customer->name',	'name' => 'customer.name',	'htmlOptions' => array('class' => 'column100'),	'headerHtmlOptions' => array('class' => 'column100'),	),
        array('name' => 'Net Amount','value' => 'Utils::formatNumber($data->getNetAmount())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'currency','value' => 'Codelkups::getCodelkup($data->currency)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		array('name' => '# Participants','value' => 'Utils::formatNumber($data->getTotalManDays())','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
        array('name' => 'Status','value' => 'Eas::getstatuslabel($data->status)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),     ),	),)); ?></div></div><br clear="all" />
<script type="text/javascript">
	function showHeader(element){
		var url = $(element).attr('href');
		$.ajax({
	 		type: "POST",
		  	url: url,
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
						$('.edit_header_content').html(data.html);
						$('.edit_header_content').removeClass('hidden');
						$('.header_content').addClass('hidden');
				  	}
			  	}
	  		}
		});
	}
	function updateHeader(element) {
		$.ajax({
	 		type: "POST",
	 		data: $('#header_fieldset').serialize()  + '&ajax=trainings-form',					
		  	url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/updateHeader', array('id' => $model->idTrainings));?>", 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'saved' && data.html) {
				  		$('.header_content').html(data.html);
				  		$('.header_content').removeClass('hidden');
				  		$('.edit_header_content').addClass('hidden');
				  	} else {
				  		if (data.status == 'success' && data.html) {
				  			$('.edit_header_content').html(data.html);
				  			showErrors(data.error);
				  		}
				  	}
			  	}
	  		}
		});
	}
</script>