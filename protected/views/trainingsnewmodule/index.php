<?php   Yii::app()->clientScript->registerScript('search', "$('.search-button').click(function(){	$('.search-form').toggle();	return false;});
$('.search-form form').submit(function(){	$.fn.yiiGridView.update('trainings-grid', {		data: $(this).serialize()	});	return false;  }); ");  ?>
<div class="search-form" style="overflow: inherit;"><?php $this->renderPartial('_search',array(	'model'=>$model,)); ?></div>
<div id="popupfreeinvite" style="width:500px"> 	<div class='titre red-bold'>Free Invite</div> 	<div class='closefreeinvite'> </div>			 
			<div class='freeinvitecontainer'>	<div class=" selectBg_create">
				<?php echo CHtml::DropDownlist("freeInvite_training","",TrainingsNewModule::getTrainingSelectDDL(),array('prompt' => Yii::t('translations', 'Choose Training'),'class'=>'width250' ));?>
			</div><div class="selectBg_create margint10">
				<?php echo CHtml::DropDownlist("freeInvite_customer","",Customers::getAllCustomersSelect(),array('prompt' => Yii::t('translations', 'Choose Customer') ,'class'=>'width250' ));?>
			</div>	</div> 	<div class='submitfreeinvite margint20'>
			<?php echo CHtml::submitButton('Submit', array('class'=>'submit' , 'style'=>'margin-left:135px;' ,'onclick' => 'submitInvite();return false;','id'=>'createbut')); ?>
			</div>	</div>
<?php $this->widget('zii.widgets.grid.CGridView', array('id'=>'trainings-grid','dataProvider'=>$model->search(),'summaryText' => '','pager'=> Utils::getPagerArray(),
    'template'=>'{items}{pager}','columns'=>array(
			array('class'=>'CCheckBoxColumn','id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_training'),'selectableRows'=>2,),
		array('header'=>Yii::t('translations', 'TRN #'),'value'=>'$data->renderTrainingNumber()','name' => 'training_number','htmlOptions' => array('class' => 'column70'), 'headerHtmlOptions' => array('class' => 'column70'),),
        array('header'=>Yii::t('translations', 'Course'),'value'=>'$data->eCourse->codelkup','name' => 'eCourse.codelkup','htmlOptions' => array('class' => 'column120'),'headerHtmlOptions' => array('class' => 'column120'),),
        array('header'=>Yii::t('translations', 'City'),'value'=>'$data->city','name' => 'city','htmlOptions' => array('class' => 'column50'), 'headerHtmlOptions' => array('class' => 'column50'),       ),
         array('header'=>Yii::t('translations', 'Country'),'value'=>'$data->eCountry->codelkup','name' => 'eCountry.codelkup','htmlOptions' => array('class' => 'column50'), 'headerHtmlOptions' => array('class' => 'column50'),),
		array('header'=>Yii::t('translations', 'Start Date'),'name' => 'start_date','value' => 'date(\'d/m/Y\',strtotime($data->start_date))',	'htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),
		array('header'=>Yii::t('translations', 'End Date'),'name' => 'end_date','value' => 'date(\'d/m/Y\',strtotime($data->end_date))','htmlOptions' => array('class' => 'column110'),'headerHtmlOptions' => array('class' => 'column110'),),
		array('name' => 'eInstructor.fullname','header' => Yii::t('translations', 'Instructor'),'value' => '$data->eInstructor->fullname','htmlOptions' => array('class' => 'column100'),'headerHtmlOptions' => array('class' => 'column100'),),
		array('name' => 'location','value'=>'$data->location','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		array('name' => 'type','value'=>'$data->getTypeLabel($data->type)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		array('name' => 'status','value'=>'$data->getStatusLabel($data->status)','htmlOptions' => array('class' => 'column65'),'headerHtmlOptions' => array('class' => 'column65'),),
		array('class'=>'CCustomButtonColumn','template'=>'{edit}','htmlOptions'=>array('class' => 'button-column'),
            'buttons'=>array (
				'edit' => array('label' => Yii::t('translations', 'Edit'), 
					'imageUrl' => null,'url' => 'Yii::app()->createUrl("trainingsnewmodule/view", array("id"=>$data->idTrainings))',
					'visible' => '(GroupPermissions::checkPermissions("general-trainings","write"))',	),          ),		),	),)); ?>
<script>
$(document).ready(function(){	$('#popupfreeinvite').hide();	});
 $(".closefreeinvite").click(function() {	$("#popupfreeinvite").hide();		});
	function freeInvite(){		$('#popupfreeinvite').stop().show();	}
	function getExcel() {	$('.action_list').hide();
			window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/getExcel');?>/?");	}
	function submitInvite(){
		$.ajax({	type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('trainingsnewmodule/submitFreeInvitation');?>", 	dataType: "json",  	data: {'invite_customer':$("#freeInvite_customer").val(),'invite_training':$("#freeInvite_training").val()},
		  	success: function(data) {
			  	if (data) {
				  	if (data.status == 'success') {
				  		$('.action_list').hide();
				  		$('#popupfreeinvite').hide();
				  	} else {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('ERROR MESSAGE', data.message, action_buttons);
							$('.action_list').hide();
					}
			  	}
	  		}
		});	}
</script>
