<div class="mytabs expenses_edit">	
	<div id="expenses_items">
		<div class="theme" style="padding-top:0px; background:none"><b><?php echo Yii::t('translations', ' ');?></b></div>
		<div id="expenses_items_content"  class="grid border-grid">
			<?php  $this->widget('zii.widgets.grid.CGridView', array('id'=>'items-grid-after','dataProvider'=>$model->searchAfterHours(),
						'summaryText' => '','pager'=> Utils::getPagerArray(),'template'=>'{items}{pager}',
						'columns'=>array(					
					array('name' => 'id','header' => 'ID','value' => '$data->id','visible' => false, 
						'htmlOptions' => array('class' => 'column70'),'headerHtmlOptions' => array('class' => 'column70'),),					
					array('name' => 'from_date','header' => 'From Date','value' => '$data->from_date',
						'visible' => true,'htmlOptions' => array('class' => 'date-after'),'headerHtmlOptions' => array('class' => 'date-after'),),					
					array('name' => 'to_date','header' => 'To Date','value' => '$data->to_date','visible' => true, 
						'htmlOptions' => array('class' => 'column200'),'headerHtmlOptions' => array('class' => 'column200'),),
					array('name' => 'PrimaryContact.firstname'.'PrimaryContact.lastname','header' => 'Primary Contact',
						'value' => 'isset($data->PrimaryContact) ? $data->PrimaryContact->firstname." ".$data->PrimaryContact->lastname : ""',
						'visible' => true,'htmlOptions' => array('class' => 'column200'),'headerHtmlOptions' => array('class' => 'column200'),),					
				array('name' => 'SecondaryContact.firstname'.'SecondaryContact.lastname','header' => 'Secondary Contact',
						'value' => 'isset($data->SecondaryContact) ? $data->SecondaryContact->firstname." ".$data->SecondaryContact->lastname : ""',
						'visible' => true,'htmlOptions' => array('class' => 'column200'),'headerHtmlOptions' => array('class' => 'column200'),
					),array(		'class'=>'CCustomButtonColumn',
									'template'=>' {update} {delete}',
									'htmlOptions'=>array('class' => 'button-column'),
									'afterDelete'=>'function(link,success,data){ 
															if (success) {
																var response = jQuery.parseJSON(data); 
											  					$.each(response.amounts, function(i, item) { $("#"+i).html(item); });
											  					$.fn.yiiGridView.update("terms-grid");}}',
									'buttons'=>array(						            	
						            	'update' => array(
											'label' => Yii::t('translations', 'Edit'),'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("fullsupport/ManageAfterItem", array("id"=>$data->id))',
						            		'options' => array('onclick' => 'showAfterItemForm(this, false);return false;'),),
										'delete' => array('label' => Yii::t('translations', 'Delete'),'imageUrl' => null,
											'url' => 'Yii::app()->createUrl("fullsupport/deleteAfterItem", array("id"=>$data->id))',  
						                	'options' => array('class' => 'delete'),),
						            ),),),)); ?>			
			<div class="tache-after new_item_after">
				<div onclick="showAfterItemForm(this, true);" class="newtask">
					<u><b>+ <?php echo Yii::t('translations', 'NEW ITEM');?></b></u>
				</div>
			</div>		
		</div>		
        <?php $form=$this->beginWidget('CActiveForm', array('id'=>'after-form-details','enableAjaxValidation'=>false,
			'htmlOptions' => array('class' => 'ajax_submit','enctype' => 'multipart/form-data','action' => Yii::app()->createUrl("fullsupport/update", array("id"=>$model->id))),)); ?>		
		<?php $this->endWidget(); ?>
	</div>
</div>
<script> 
	var getProjectsByClientUrl = '<?php echo Yii::app()->createAbsoluteUrl('projects/getProjectsByClient');?>'; 
	var modelId = '<?php echo $model->id;?>';
	var updateItemAfterUrl = '<?php echo Yii::app()->createUrl("fullsupport/createAfterItem"); ?>';
</script>		
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/scripts/common.js"></script>