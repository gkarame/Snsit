
<div class="create supportCreate" id="surveys-id-form" >
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'surveys-form',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(
			'class' => 'ajax_submit',
			'enctype' => 'multipart/form-data',
		),
	)); ?>
		<table style="padding:0px; margin:0px;">
		<tr style="background-color:white; "><td  style="padding-bottom:40px;padding-top:40px; padding-left:35px;  font-family:Arial, Helvetica, sans-serif; font-size:22px;"><?php  $p=Surveys::decrypt_url($project); echo "<b>Customer:</b> ".Customers::getNameById(Projects::getCustomerByProject($p))," </br>";  echo "<b>Project:</b> ".Projects::getNameById($p)," </br>"; echo "<b>Project Manager:</b> ".Projects::getProjectManager($p)." <br/>";   $type=Projects::getProjectType($p); ?></td></tr>
		<?php	
			$checkprojectstatus = Projects::getProjectStatus($p);

			if($checkprojectstatus=="2"){				
				$surv_type='close';
			}else{				
				$surv_type='intermediate';
			}
		
			 $result =  Yii::app()->db->createCommand("SELECT id , question  from surveys_questions where type='".$type."' and surv_type='".$surv_type."' order by id asc ")->queryAll(); 
			
			$i=1;
			 foreach($result as $res) 
		{  ?>
			
		<tr style ="background-color: <?php if($i % 2 ==0){ echo 'white';} else {echo '#f9f9f9';} ?> " >
		
		<td  style="padding:5px; padding-left:30px; padding-top:43px;font-size:22px;
	
	font-family: Arial, Helvetica, sans-serif; padding-right:14px;">
			<div>	
			<?php echo $i."- ".$res['question']; ?>
			</div>
				</td>
					</tr>
		<tr style ="background-color: <?php if($i % 2 ==0){ echo 'white';} else {echo '#f9f9f9';} ?> ">
		<?php if($res['id']!='11' && $res['id']!='22') { ?>
				<td style=" padding:15px; padding-left:30px; padding-bottom:23px;">
			
				<?php $this->widget('CStarRating',
									array(
										'name'=>"surv-rate-".$res['id'], 
										'allowEmpty'=>false,
									    'ratingStepSize'=>'1' ,
									     'maxRating'=>'5' , 
									     'titles'=>array('1'=>'Very Unsatisfied','2'=>'Unsatisfied','3'=>'Somewhat Satisfied', '4'=>'Satisfied' , '5'=>'Very Satisfied') ,
									     'callback'=>"function(){ 	
									     							 if($(this).val()==1){ 	
									     						 						$('.rate-status-".$res['id']."').removeClass('awe');
									     						 						$('#very-unsatisfied-".$res['id']."').addClass('awe');
									     						 						 }
									     						 	if($(this).val()==2){ 	
									     						 							$('.rate-status-".$res['id']."').removeClass('awe');
									     						 						$('#unsatisfied-".$res['id']."').addClass('awe');
									     						 						 }
									     						 		if($(this).val()==3){ 	
									     						 						$('.rate-status-".$res['id']."').removeClass('awe');
									     						 						$('#neutral-".$res['id']."').addClass('awe');
									     						 						 }
									     						 			if($(this).val()==4){ 	
									     						 							$('.rate-status-".$res['id']."').removeClass('awe');
									     						 						$('#satisfied-".$res['id']."').addClass('awe');
									     						 						 }
									     						 				if($(this).val()==5){ 	
									     						 							$('.rate-status-".$res['id']."').removeClass('awe');
									     						 						$('#very-satisfied-".$res['id']."').addClass('awe');
									     						 						 }
									     							 		

									     						 }
													" ,
									     'focus'=>"function(){ 
									     						 if($(this).val()==1){ 	
									     						 						$('.rate-status-".$res['id']."').addClass('hidden');
									     						 						$('#very-unsatisfied-".$res['id']."').removeClass('hidden');
									     						 						 }
									     						 	if($(this).val()==2){ 	
									     						 						$('.rate-status-".$res['id']."').addClass('hidden');
									     						 						$('#unsatisfied-".$res['id']."').removeClass('hidden');
									     						 						 }
									     						 		if($(this).val()==3){ 	
									     						 						$('.rate-status-".$res['id']."').addClass('hidden');
									     						 						$('#neutral-".$res['id']."').removeClass('hidden');
									     						 						 }
									     						 			if($(this).val()==4){ 	
									     						 						$('.rate-status-".$res['id']."').addClass('hidden');
									     						 						$('#satisfied-".$res['id']."').removeClass('hidden');
									     						 						 }
									     						 				if($(this).val()==5){ 	
									     						 						$('.rate-status-".$res['id']."').addClass('hidden');
									     						 						$('#very-satisfied-".$res['id']."').removeClass('hidden');
									     						 						 }
									     						 }",
									     'blur'=>"function(){	
									     						 

									    						 			$('.rate-status-".$res['id']."').addClass('hidden');
									    						 		$('.awe').removeClass('hidden')
									    								 
									     						
									     						 }" 					

				)); ?>

				 <div class="rate-surveys-status" style="float:left; padding-left:20px;">
						<div class="rate-status-<?php echo $res['id']; ?> hidden red-rate" id ="very-unsatisfied-<?php echo $res['id']; ?>" >Very Unsatisfied</div>
					<div class="rate-status-<?php echo $res['id']; ?> hidden red-rate" id ="unsatisfied-<?php echo $res['id']; ?>" >Unsatisfied</div>
						<div class="rate-status-<?php echo $res['id']; ?> hidden red-rate" id ="neutral-<?php echo $res['id']; ?>" >Somewhat Satisfied</div>
							<div class="rate-status-<?php echo $res['id']; ?> hidden red-rate" id ="satisfied-<?php echo $res['id']; ?>" >Satisfied</div>
								<div class="rate-status-<?php echo $res['id']; ?> hidden red-rate" id ="very-satisfied-<?php echo $res['id']; ?>" >Very Satisfied</div>
				</div> </td> 


				<?php }else{ ?> 

				<td style=" padding-left:35px; padding-bottom:20px;">
					
					<textarea cols='100' rows='6' style="border-color:#a91616;" name="comment-<?php echo $res['id']; ?>"></textarea>

				</td>
				<?php 	} ?> 
					
		  </tr>

		  	<tr style ="background-color: <?php if($i % 2 ==0){ echo 'white';} else {echo '#f9f9f9';} ?> " ><td >
					<div style="height:8px;"></div>
					 </td></tr>

					 	<?php $i++; ?>
			<?php } ?>
			</table>

			
		
		
		<div class="row buttons" style="background-color:white; padding-top:35px; width:100%; padding-bottom:26px;">
			<?php echo CHtml::submitButton('Submit', array('class'=>'new-submit' , 'style'=>'margin-left:35px;' ,'onclick' => 'submitSurveys();return false;','id'=>'createbut')); ?>
		</div>
		
		<br clear="all" />
		
		
		
	<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {

		$( ".br br" ).remove();
		$('span.btn-success').addClass('margint18'); 
	});


	function submitSurveys(){
			
			var id_project= '<?php echo $p ; ?>';
			var pname = '<?php echo Projects::getNameById($p); ?>;'
			
			$.ajax({
		 		type: "POST",					
		 		url: "<?php echo Yii::app()->createAbsoluteUrl('projects/sendSurvey')?>",
			  	dataType: "json",
			    data: $('#surveys-form').serialize()+'&send=2&id_project='+id_project,
			  	success: function(data) {
			  		if (data) {
			  		
			  					if(data.status=='success'){

			  						document.getElementById('surveys-id-form').innerHTML =" ";
			  						document.getElementById('surveys-id-form').innerHTML ="<div style='background-color:white; width:100%; height:260px; font-size:30px; text-align:center; '><div style='padding-top:80px; font-family: Arial, Helvetica, sans-serif;    color: #A11515;' >THANK YOU <br/> FROM COMPLETING THE SURVEY</div></div> ";
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
			  					}
				  			
				  		}
			  		
		  		},
				
			});
	}


</script>