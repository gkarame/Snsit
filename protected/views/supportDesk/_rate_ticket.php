<form id="rating-ticket-form" method="post" >	<div id="rateheight" class="listofactions unassigned ratePop ratePopheight-small" >	<div class="close" onclick="closerateTicket()"></div>
<div class="titre" >PLEASE RATE THE SOLUTION PROVIDED</div>	<div class="rating_stars">
				<?php $this->widget('CStarRating',
									array('name'=>'rating', 'allowEmpty'=>false,'ratingStepSize'=>'1' ,'maxRating'=>'5' , 'titles'=>array('1'=>'Very Unsatisfied','2'=>'Unsatisfied','3'=>'Somewhat Satisfied', '4'=>'Satisfied' , '5'=>'Very Satisfied') ,
									     'callback'=>"function(){ 	
									     							 if($(this).val()<=3) {
									     							 	$('#comment-text').removeClass('hidden'); $('#rateheight').removeClass('ratePopheight-small');
									     							 	$('#rateheight').addClass('ratePopheight-large');	
									     							 }else{
									     							 	$('#comment-text').addClass('hidden'); $('#rateheight').removeClass('ratePopheight-large');
									     							 	$('#rateheight').addClass('ratePopheight-small');
									     							 } } " ,
									     'focus'=>"function(){ 
									     						 if($(this).val()==1){ 	
									     						 		$('.rate-status').addClass('hidden');
									     						 		$('#very-unsatisfied').removeClass('hidden');
									     						 	}
									     						 	if($(this).val()==2){ 	
									     						 		$('.rate-status').addClass('hidden');
									     						 		$('#unsatisfied').removeClass('hidden');
									     						 	}
									     						 	if($(this).val()==3){ 	
									     						 		$('.rate-status').addClass('hidden');
									     						 		$('#neutral').removeClass('hidden');
									     						 	}
									     						 	if($(this).val()==4){ 	
									     						 		$('.rate-status').addClass('hidden');
									     						 		$('#satisfied').removeClass('hidden');
									     						 	}
									     						 	if($(this).val()==5){ 	
									     						 		$('.rate-status').addClass('hidden');
									     						 		$('#very-satisfied').removeClass('hidden');
									     						 	}	 }",
									     'blur'=>"function(){	$('.rate-status').addClass('hidden');	 }"	)); ?>
				</div>	<div class="rate-ticket-status"><div class="rate-status hidden red-bold" id ="very-unsatisfied" >Very Unsatisfied</div>
					<div class="rate-status hidden red-bold" id ="unsatisfied" >Unsatisfied</div>		<div class="rate-status hidden red-bold" id ="neutral" >Somewhat Satisfied</div>
							<div class="rate-status hidden red-bold" id ="satisfied" >Satisfied</div>	<div class="rate-status hidden red-bold" id ="very-satisfied" >Very Satisfied</div>
			</div>	<div id="comment-text" class="hidden" > <div style="padding-top:5px; " class="label">WHAT ARE WE MISSING ? </div>
				<textarea  name="rating-text" id="rating-reason" class="ratingcomment" ></textarea>		</div>		
				 <a href="javascript:void(0);" class="send  ua" onclick="rateTicket()"><?php echo Yii::t('translation', '');?></a>					
				<div id="remark-comment" class="remark hidden">Review is required for 3 stars and below</div>		</div></form>


