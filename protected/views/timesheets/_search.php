<div class="wide search">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); ?>
		<div class="row customer">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'id_customer'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'customer_name',	
						'source'=>Customers::getAllAutocomplete(),
						'options'=>array(
							'minLength'=>'0',
							'showAnim'=>'fold',
							'select'=>"js:function(event, ui) {
		                    				$('#SearchApprovalForm_id_customer').val(ui.item.id);
		                    				refreshProjectsListAll();
                   					}"
						),
						'htmlOptions'=>array(
							'onfocus' => "javascript:$(this).autocomplete('search','');",
							'onblur' => 'blurAutocomplete(event, this, "#SearchApprovalForm_id_customer", refreshProjectsListAll);',
							'class'	=>	'width111',
							'id' => 'SearchApprovalForm_customer_name'
						)
				));
				?>
				<?php echo $form->hiddenField($model, 'id_customer'); ?> 
			</div>
		</div>
	
		<div class="row project_name">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'id_project'); ?>
				<span class="spliter"></span>
				<div class="select_container width111">
					<?php echo $form->dropDownList($model, 'id_project', array(), array('disabled' => true)); ?>
				</div>
			</div>
		</div>

		<div class="row author">
			<div class="inputBg_txt">
				<?php echo $form->label($model,'id_user'); ?>
				<span class="spliter"></span>
				<?php 
				$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
						'model' => $model,
						'attribute' => 'user_name',		
						'source'=>Users::getAllAutocomplete(true),
						'options'=>array(
								'minLength'=>'0',
								'showAnim'=>'fold',
								'select'=>"js:function(event, ui) {
								                    				$('#SearchApprovalForm_id_user').val(ui.item.id);
								                    			}",
						),
						'htmlOptions'=>array(
								'onfocus' => "javascript:$(this).autocomplete('search','');",
								'onblur' => 'blurAutocomplete(event, this, "#SearchApprovalForm_id_user");',
								'class'	=>	'width111',
								'id' => 'SearchApprovalForm_user_name',
						),
				));
				?>
				<?php echo $form->hiddenField($model, 'id_user'); ?>
			</div>
		</div>
		
		<div class="row dateRow margint10">
			<div class="dateSearch inputBg_txt">
				<?php echo $form->label($model,'from'); ?>
				<span class="spliter"></span>
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 
			        'attribute'=>'from', 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy'
			    	),
			    	'htmlOptions'=>array('class'=>'width111'),
			    	
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
		</div>
		<div class="row dateRow margint10">
			<div class="dateSearch inputBg_txt">
				<?php echo $form->label($model,'to'); ?>
				<span class="spliter"></span>
				<?php 
			    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
			        'model'=>$model, 
			        'attribute'=>'to', 
			    	'cssFile' => false,
			        'options'=>array(
			    		'dateFormat'=>'dd/mm/yy'
			    	),
			    	'htmlOptions'=>array('class'=>'width111'),
			    ));
				?>
				<span class="calendar calfrom"></span>
			</div>
		</div>
		
		<div class="btn">
			<?php echo CHtml::submitButton(Yii::t('translations', 'Search'), array('class'=>'search-btn')); ?>
		</div>
		<div class="horizontalLine search-margin"></div>
		
	
	<?php $this->endWidget(); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			refreshProjectsListAll();
		});
		function refreshProjectsListAll(id)
		{
			var id_project_ts = "<?php echo $model->id_project;?>";
			if (!id) {
				id = $('#SearchApprovalForm_id_customer').val();
			}
				
			if (id)
			{
				console.log("if");
				$.ajax({
		 			type: "GET",
		 			data: {id : id},					
		 			url: '<?php echo Yii::app()->createAbsoluteUrl("projects/GetProjectsAndInternalByClient");?>', 
		 			dataType: "json",
		 			success: function(data) {
					  	if (data) {
						  	
					  		var arr = [];

					  		for (var key in data) {
					  		    if (data.hasOwnProperty(key)) {
					  		        arr.push({'id': key, 'label': data[key]});
					  		    }
					  		}
					  		
					  		 var sorted = arr.sort(function (a, b) {
				    				if (a.label > b.label) {
				      					return 1;
				      				}
				    				if (a.label < b.label) {
				     					 return -1;
				     				}

				    				return 0;
							 });
					  		
					  		$('#SearchApprovalForm_id_project').removeAttr('disabled');
					  		var selectOptions = '<option value=""></option>';
					  		$.each(sorted,function(index, val){
						  		var selected = (val.id == id_project_ts) ? 'selected="selected"' : ''; 
						        selectOptions += '<option value="' + val.id+'"' + selected + '>'+val.label+'</option>';
						    });
						    $('#SearchApprovalForm_id_project').html(selectOptions);
					  	}
			  		}
				});
			}
			else
			{
				console.log('eles');
				$('#SearchApprovalForm_id_project').html('');
				$('#SearchApprovalForm_id_project').attr('disabled', 'disabled');
			}
		}
	</script>
</div><!-- search-form -->