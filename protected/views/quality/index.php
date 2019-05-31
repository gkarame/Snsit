<?php Yii::app()->clientScript->registerScript('search', "$('.search-form form').submit(function(){	$.fn.yiiGridView.update('qa-grid', {	data: $(this).serialize()	});	return false;});");?>
<div class="search-form" style="overflow: inherit;"> <?php $this->renderPartial('_search',array('model'=>$model, )); 	?></div>
<?php	$cls = 'editable_select'; 	$this->widget('zii.widgets.grid.CGridView', array(	'id'=>'qa-grid',	'dataProvider'=>$model->search(),	'summaryText' => '',
	'selectableRows'=>1,	'pager'=> Utils::getPagerArray(),    'template'=>'{items}{pager}',	'columns'=>array( 
		array('class'=>'CCheckBoxColumn',  'id'=>'checkinvoice','htmlOptions' => array('class' => 'item checkbox_grid_invoice'),'selectableRows'=>2,	),
		array('header' => 'Project','type'=>'html','htmlOptions' => array('class' => ''),'value' => '"<div style=\"width:80px;\">" .Quality::getProjectName($data->id_project, $data->internal_project)."</div>"',),
		array('header' => 'Task','value' => 'Quality::getTaskDesc($data->id_task, $data->id_phase, $data->internal_project)','htmlOptions' => array('class' => 'width70'),'headerHtmlOptions' => array('class' => 'width70'),   ),
		array('name' => 'pm/ops','header' => 'pm/ops','value'=>'Quality::getDescriptionGrid(Quality::getPMOps($data->id_project, $data->internal_project))',
			'type'=>'raw','htmlOptions' => array('style' => 'width: 50px !important;', 'onmouseenter'=>"showToolTip(this);", "onmouseleave"=>"hideToolTip(this);"),'headerHtmlOptions' => array('style' => 'width: 50px !important;'),),
        array('header' => 'res','value' => 'Users::getinitials($data->id_user)'),
		array('header' => 'Complexity','type'=>'raw','value' => 'Quality::gettaskcomplexity($data->id, $data->complexity)',),
		array('name' => 'status','header' => 'status','type'=>'raw','value' => 'Quality::gettaskstatus($data->id, $data->status)',),
		array('header' => 'QA','type'=>'raw','value' =>  'Quality::getQAUsers($data->id, $data->id_resc)',),
		array('name' => 'fbr_delivery','header' => 'FBR ETA','type'=>'raw','value' =>  'Quality::datedispFBR($data->fbr_delivery,$data->id)','headerHtmlOptions' => array('class' => ''),),
		array('name' => 'expected_delivery_date','header' => 'QA ETA','type'=>'raw','value' =>  'Quality::datedispQA($data->expected_delivery_date,$data->id)','headerHtmlOptions' => array('class' => ''),),
		array('header' => 'Result','type'=>'raw','value' => 'Quality::gettaskscore($data->id, $data->score)',),
		array('header' => 'Comment','type'=>'raw','value'=>'(strlen(( Quality::formatNotes($data->notes))) > 10
                                                ? CHtml::tag("span", array("title"=>(Quality::formatNotes($data->notes))), CHtml::encode(substr((Quality::formatNotes($data->notes)), 0, 10)) . "..")
                                                : CHtml::encode((Quality::formatNotes($data->notes))));'),	), )); ?>
<div id="popupavoid"> 
			<div class='titre red-bold'>Comment</div> <div class='closereason'> </div>	<div class='reasoncontainer'>
				<textarea id="solution_message" style="width:270px;height:120px;resize:none;" name="solution_message"></textarea></div> 
			<div class='submitreason'>	<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:10px; margin-top:10px;' ,'onclick' => 'SetNotes();return false;','id'=>'createbut')); ?>	</div></div>
<script>
$(".closereason").click(function() {	$('#popupavoid').hide();	$('#popupavoidsol').hide();		});
function getExcel() {	$('.action_list').hide();		window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('quality/getExcel');?>/?");	}
$(document).ready(function() {		$('#popupavoid').hide();	});
function showdropdown(){		document.getElementById('inv_type').style.visibility="visible";	}
$(".notesQA").hover(function() {   
       var project=$(this).attr('project');       showToolTip(document.getElementById('notesQA'+project));
    },function() { 
      var project=$(this).attr('project');        hideToolTip(document.getElementById('notesQA'+project));    }    );

function showPop(id){		
		$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('quality/updateNotesDisplay');?>",dataType: "json", 	data: {'qa_id':$('.checkbox_grid_invoice input').serialize()},
		  	success: function(data) {
		  		if (data.status == 'success') {	$('#popupavoid').stop().show();		$('#solution_message').val(data.message);
				  		}else{
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
						  } } });	}
function deleteQA(){	
		$.ajax({type: "POST",data: {'qa_id':$('.checkbox_grid_invoice input').serialize()},
 		url: "<?php echo Yii::app()->createAbsoluteUrl('quality/deleteQA');?>",dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {  	if (data.status == 'success' ) {	$.fn.yiiGridView.update('qa-grid');	$('.action_list').hide(); }else{
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
						  }
	}  	}	});	}
function SetNotes(){		
		var msg2 = document.getElementById('solution_message').value;
		$.ajax({type: "POST",data: {'notes':msg2,'qa_id':$('.checkbox_grid_invoice input').serialize()},
 		url: "<?php echo Yii::app()->createAbsoluteUrl('quality/updateNotes');?>",dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {  	if (data.status == 'success' ) {	$('#popupavoid').hide();	$('.search-btn').trigger('click'); 	}	}  	}	});	}
function changeInput(v,id, field){
	if(field=='5' || field=='6'){	v=v.target.value;	}
	$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('quality/update');?>",dataType: "json",data: {'value':v,'id':id,'field':field},
	  	success: function(data) {
		  	if (data) {	if (data.updategrid=='1') {if (data.message.length > 0) {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('NOTIFICATION MESSAGE', data.message, action_buttons);
					}	$.fn.yiiGridView.update('qa-grid');	}  	}	}	}); } 
function sendToQA() {		
		$.ajax({type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('quality/sendToQA');?>",dataType: "json",data: {'qa_id':$('.checkbox_grid_invoice input').serialize()},
		   	success: function(data) {
			  	if (data) {
				  	if (data.message.length > 0) {
				  		var action_buttons = {
						        "Ok": {
									click: function() 
							        {
							            $( this ).dialog( "close" );
							        },
							        class : 'ok_button'
						        }
							}
			  			custom_alert('NOTIFICATION MESSAGE', data.message, action_buttons);
					}
					$.fn.yiiGridView.update('qa-grid');  	} 		}	});	}
</script>
