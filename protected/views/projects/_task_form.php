<div class="tache new " style="    min-height:230px;"><div class="bgtaskk " id="section" style="background-size: 100% 230px;
    height: 230px;
    min-height: 230px;"></div>	<fieldset class="items_fieldset"><?php $id = $model->isNewRecord ? 'new' : $model->id;?>
		
		<div class="textBox  two inline-block  width85" >	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "type *"); ?></div>
			<div class="input_text"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "type",  ProjectsTasks::getTypeList(), array( 'onchange' => 'changeType(this);', 'class'=>'input_text_value width73')); ?>
				</div> 	</div>	<?php echo CCustomHtml::error($model, "type", array('id'=>"type")); ?>	</div>	

		<div class="textBox  inline-block paddingl21 width365 hidden" id="descr">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "description *"); ?></div>
			<div class="input_text width365"><?php echo CHtml::activeTextField($model, "description", array('class'=> 'input_text_value width323')); ?>	</div>
			<?php echo CCustomHtml::error($model, "description", array('id'=>"description"));  ?></div>

		<div class="textBox two inline-block paddingl21  width40" id="fbrnb">	<div class="input_text_desc width51"><?php echo CHtml::activelabelEx($model, "fbr# *"); ?></div>
			<div class="input_text width40"><?php echo CHtml::activeTextField($model, "fbr", array('class'=> 'width30')); ?>	</div>
			<?php echo CCustomHtml::error($model, "fbr", array('id'=>"fbr", 'style' => 'width:70px;'));  ?></div>

		<div class="textBox one inline-block width300 paddingl21" id="titlestr">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "title *"); ?></div>
			<div class="input_text width300"><?php echo CHtml::activeTextField($model, "title", array('class'=> 'input_text_value width280')); ?>	</div>
			<?php echo CCustomHtml::error($model, "title", array('id'=>"title"));  ?></div>

		<div class="textBox   inline-block  paddingl21  width150" id="modulestr">	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "module *"); ?></div>
			<div class="input_text width150"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "module",  Codelkups::getCodelkupsDropDownOriginals('modules'), array('prompt' => "", 'class'=>'input_text_value width146')); ?>
		</div> 	</div>	<?php echo CCustomHtml::error($model, "module", array('id'=>"module")); ?>	</div>	

		<div class="textBox   inline-block  paddingl21   width150" id="bill" >	<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "billable"); ?></div>
			<div class="input_text width150"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "billable",  array('Yes'=>'Yes','No'=>'No'), array('class'=>'input_text_value width146')); ?>
				</div> 	</div>	<?php echo CCustomHtml::error($model, "billable", array('id'=>"billable")); ?>	</div>	


		<div class="textBox   inline-block     width150" id="complex">	<div class="input_text_desc padding_smaller width100"><?php echo CHtml::activelabelEx($model, "complexity *"); ?></div>
			<div class="input_text width150"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "complexity",  ProjectsTasks::getComplexityList(), array( 'class'=>'input_text_value width146')); ?>
		</div> 	</div>	<?php echo CCustomHtml::error($model, "complexity", array('id'=>"complexity")); ?>	</div>	
		

		<div class="textBox  inline-block width473 paddingl21" id="keyw">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "keywords (Enter multiple Keywords) *"); ?></div>
			<div class="input_text width473"><?php echo CHtml::activeTextField($model, "keywords", array('placeholder'=>'Seperate words with commas','class'=> 'input_text_value width450')); ?>	</div>
			<?php echo CCustomHtml::error($model, "keywords", array('id'=>"keywords"));  ?></div>		


		<div class="textBox  inline-block paddingl21 width150 " id="fexists" >	<div class="input_text_desc " style="width: 150px;"><?php echo CHtml::activelabelEx($model, "Previously Done?*"); ?></div>
			<div class="input_text width150"><div class="hdselect">
				<?php echo CHtml::activeDropDownList($model, "existsfbr",  ProjectsTasks::getExistsList(), array('onchange' => 'changeCategory(this);', 'class'=>'input_text_value width146')); ?>
		</div> 	</div>	<?php echo CCustomHtml::error($model, "existsfbr", array('id'=>"existsfbr")); ?>	</div>	


		<div class="textBox  width150  hidden inline-block paddigr20"  id="pfbr">
			<div class="input_text_desc padding_smaller"><?php echo CHtml::activelabelEx($model, "parent_fbr *"); ?></div>			
			<div class="input_text width150">
				<div class="hdselect width140">
					<?php $this->widget('zii.widgets.jui.CJuiAutoComplete',array(
							'model' => $model,'attribute' => "parent_fbr",'source'=>ProjectsTasks::getAllTasksAutocomplete(),
							'options'=>array(
								'minLength'=>'0','showAnim'=>'fold',								
								'select'=>"js:function(event, ui) {	$('#ProjectsTasks_parent_fbr').val(ui.item.id);	}",	
								),
							'htmlOptions'=>array('class' => 'width140','onfocus' => "javascript:$(this).autocomplete('search','');",),
					)); ?>
				</div>
			</div>
			<?php echo CCustomHtml::error($model, "parent_fbr", array('id'=>"parent_fbr")); ?>		
		</div>

		
				<div class="textBox one inline-block width473  " id="notes">	<div class="input_text_desc"><?php echo CHtml::activelabelEx($model, "notes"); ?></div>
			<div class="input_text width473  "><?php echo CHtml::activeTextField($model, "notes", array('class'=> 'input_text_value width450')); ?>	</div>
			<?php echo CCustomHtml::error($model, "notes", array('id'=>"notes"));  ?></div>


		<?php if($id == "new"){?>
			<div style="right:75px;" class="save top185" onclick="createTask(this,<?php echo $id_phase;?>,<?php echo $id_project?>);return false;"><u><b>SAVE</b></u></div>
		<?php }else{?>
			<div style="right:75px; " class="save top185" onclick="updateTask(this, '<?php echo $id;?>',<?php echo $id_phase;?>);return false;"><u><b>SAVE</b></u></div>
		<?php }?>
		<div style="color:#333;" class="save top185" onclick="$(this).parents('.tache.new').siblings('.tache').show();$(this).parents('.tache.new').remove();<?php if (!$model->isNewRecord) {?>$.fn.yiiGridView.update('<?php echo $id_phase;?>-grid');<?php }?>"><u><b>CANCEL</b></u></div>
	</fieldset></div>
<script>
function updateTask(element, id,id_phase) {
	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/updateTasks');?>";
	if (id != 'new') {	url += '/'+parseInt(id); }
	$.ajax({type: "POST",	data: $(element).parents('.items_fieldset').serialize() ,  	url: url, 	dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
		  			$(element).parents('.tache.new').remove(); 		$.fn.yiiGridView.update(id_phase+'-grid'); } else {	var error = data.error;  		$.each( error, function( key, value ) {		$("#" + key).text(value); 		});		} }	} }); }
function createTask(element,id_phase,id_project) {
	$(".errorMessage").text("");	var url = "<?php echo Yii::app()->createAbsoluteUrl('projects/createTasks');?>";
	$.ajax({type: "POST",		data: $(element).parents('.items_fieldset').serialize()+'& id_phase='+id_phase+'& id_project='+id_project,  	url: url, dataType: "json",
	  	success: function(data) {
		  	if (data) {
		  		if (data.status == 'saved') {
				  	$(element).parents('.tache.new').remove();  	$('.new_item').show();	$.fn.yiiGridView.update(id_phase+'-grid',{url:"<?php echo Yii::app()->createAbsoluteUrl('projects/view');?>/"+id_project});
			  		$('#man_day_rate-'+id_phase).html(data.totalDays);	
			  	} else {	var error = data.error;  		$.each( error, function( key, value ) {		$("#" + key).text(value); 		});		}		  	}  		}	}); }

</script>			