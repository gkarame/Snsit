<div class="mytabs expenses_edit">
	
	<div id="expenses_items">
		<div class="theme" style="padding-top:0px; background:none"><b><?php echo Yii::t('translations', ' ');?></b></div>
		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array(
						'id'=>'items-grid-sunday',
						'dataProvider'=>$model->searchSunday(),
						'summaryText' => '',
						'pager'=> Utils::getPagerArray(),
						'template'=>'{items}{pager}',
						'columns'=>array(
							
					array(
						'name' => 'id',
						'header' => 'ID',
						'value' => '$data->id',
						'visible' => false, 
						'htmlOptions' => array('class' => 'column120'), 
						'headerHtmlOptions' => array('class' => 'column120'),
					),
					
					array(
						'name' => 'from_date',
						'header' => 'Date',
						'value' => '$data->from_date',
						'visible' => true, 
						'htmlOptions' => array('class' => 'date-sunday'), 
						'headerHtmlOptions' => array('class' => 'date-sunday'),
					),
					
					array(
						'name' => 'to_date',
						'header' => 'To Date',
						'value' => '$data->to_date',
						'visible' => false, 
						'htmlOptions' => array('class' => 'column0'), 
						'headerHtmlOptions' => array('class' => 'column0'),
					),
					array(
						'name' => 'PrimaryContact.firstname'.'PrimaryContact.lastname',
						'header' => 'Primary Contact',
						'value' => 'isset($data->PrimaryContact) ? $data->PrimaryContact->firstname." ".$data->PrimaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column160'), 
						'headerHtmlOptions' => array('class' => 'column160'),
					),
					
				array(
						'name' => 'SecondaryContact.firstname'.'SecondaryContact.lastname',
						'header' => 'Secondary Contact',
						'value' => 'isset($data->SecondaryContact) ? $data->SecondaryContact->firstname." ".$data->SecondaryContact->lastname : ""',
						'visible' => true, 
						'htmlOptions' => array('class' => 'column120'), 
						'headerHtmlOptions' => array('class' => 'column120'),
					),							array
							(
									'class'=>'CCustomButtonColumn',
									'template'=>' {update} {delete}',
									'htmlOptions'=>array('class' => 'button-column'),
									'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
																// update amounts
											  					$.each(response.amounts, function(i, item) {
											  		    			$("#"+i).html(item);
											  					});
											  					$.fn.yiiGridView.update("terms-grid");
											  				}}',
									'buttons'=>array
						            (
						            	
						            	'update' => array(
											'label' => Yii::t('translations', 'Edit'), 
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("fullsupport/ManageSundayItem", array("id"=>$data->id))',
						            		'options' => array(
						            			'onclick' => 'showItemForm(this, false);return false;'
						            		),
										),
										'delete' => array(
											'label' => Yii::t('translations', 'Delete'),
											'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("fullsupport/deleteSundayItem", array("id"=>$data->id))',  
						                	'options' => array(
						                		'class' => 'delete'
											),
										),
						            ),
								),
							),
						)); ?>
			
			<div class="tache-sunday new_item_sunday">
				<div onclick="showSundayItemForm(this, true);" class="newtask">
					<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u>
				</div>
			</div>
		
		</div>
		
        <?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'sunday-form-details',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array(
				'class' => 'ajax_submit',
				'enctype' => 'multipart/form-data',
				'action' => Yii::app()->createUrl("fullsupport/update", array("id"=>$model->id))
			),
		)); ?>
		
		
		
	
		<?php $this->endWidget(); ?>
	</div>
</div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
	var updateItemSundayUrl = '<?php echo Yii::app()->createUrl("fullsupport/createSundayItem"); ?>';
</script>

		
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>