<div class="mytabs support_edit">	

	<div id="support_header" class="edit_header">	

		<div class="header_title">	<span class="red_title"><?php echo Yii::t('translations', 'RSR HEADER');?></span>
		 <a class="header_button" style="    color: #8d0719;cursor: pointer;" onclick="showComments('<?php echo $model->sr;?>');return false;" ><?php echo Yii::t('translations', 'Show History');?></a>	
		</div>

<div  id="srshistory"> <div class='titre red-bold'>SRs History</div> <div class='closereason' style="    margin-left: 100%;"> </div>
<div  id="srscommente" style="overflow-y: scroll;
    height: 85%;">
				</div> 
					</div>

		<div class="header_content tache">	<?php $this->renderPartial('_header_content', array('model' => $model));?>		</div>


		<div class="hidden edit_header_content tache new" style="width:97%"></div>	<br clear="all" />	
	 
			<div class="horizontalLine" style="margin:0px !important"></div>

	<div class="supportdesk_comm_table">	<?php $this->renderPartial('_posts_table',array('model'=>$model,	)); ?></div>
	
		<div class="supportdesk_comm margintm20">
			<?php $this->renderPartial('_comment',array('model'=>$model,	)); ?>	</div>	
	
</div></div>	

<script type="text/javascript">
$(document).ready(function() {
$("#srshistory").hide(); 	$(".sep1").hide();	$(".sep2").hide();	$(".sep3").hide();	$( "span.attachFile" ).addClass(" support_file");
	
		});

function postComm(){
	$.ajax({type: "POST",data: $('#supportrequest-details').serialize()  + '&ajax=supportrequest-form',	url: "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/postComment', array('id' => $model->id));?>", 
	  	dataType: "json",
	  	beforeSend: function() {
	  		$("#ajxLoader").fadeIn(); 
        },
        complete: function() {
        	$("#ajxLoader").fadeOut();
        },
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) { 
			  		$('.supportdesk_comm_table').html(data.html);  
			  		$('.supportdesk_comm').html(data.html2); 
			  				document.getElementById('rejected_message').value = "";		
			  			$(".sep2").hide();			
	
			  	} else {
			  		if (data.status == 'no_message') {
			  			$('span#rejected_message_not').html("No message");
			  		}
			  	}
		  	}
  		}
	});
}
 $(".closereason").click(function() {		$("#srshistory").hide();   });	
function showComments(id)
{ 
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/getHistory');?>",  	dataType: "json",
	  	data: {'id':id},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'success') {  		
			  		//	alert(data.table);
			  		$('#srscommente').html(data.table);
	$('#srshistory').stop().show();   	
			  	} 
		}	}  });
}
function changeInput(value,id_support_dask,type){	
	$.ajax({type: "POST", 	url: "<?php echo Yii::app()->createAbsoluteUrl('supportRequest/assigned');?>",  	dataType: "json",
	  	data: {'value':value,'id_rsr':id_support_dask,'type':type},
	  	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved') {  		

			  		if(type == 4 && value !=6 && value !=8)
			  		{
			  			$( "#commentssection" ).removeClass("hidden");
			  				$('.supportdesk_comm_table').html(data.html);  
			  				$('.supportdesk_comm').html(data.html2); 
			  		}else if(type == 4 && value ==6 || value ==8)
			  		{
			  				$('.supportdesk_comm_table').html(data.html);  
			  				$('.supportdesk_comm').html(data.html2); 
			  			$( "#commentssection" ).addClass("hidden");
			  		}	  	
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
		  			custom_alert('ERROR MESSAGE', data.errormessage, action_buttons);

			  	  	}		}	}  });
}


</script>