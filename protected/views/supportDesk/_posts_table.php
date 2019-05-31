<?php $comments = $model->getComments();  if (!empty($comments)) {	?>
<table class="posts_table">	<thead>		<tr>	<td class="tduser">USER</td>	<td class="tdcomm">COMMENT</td>		<td class="tdstatus">STATUS</td>	<td class="tddate">DATE</td>
			<td class="tdattachment">ATTACHMENT</td></tr></thead>	<tbody>	<?php foreach ($comments as $comment) { ?>	<tr>
			<td class="tduser"><?php echo $comment['is_admin'] == 1 ? Users::getUsername($comment['id_user']) : CustomersContacts::getNameById($comment['id_user'])/*CustomersContacts::getNameBySR($comment['id_support_desk'])*/;?></td>
			<td class="tdcomm"><?php echo $comment['comment'];?></td>	<td class="tdstatus"><?php echo SupportDesk::getStatusLabel($comment['status']);?></td>
			<td class="tddate"><?php /*$offset = timezone_offset_get( new DateTimeZone(Customers::getTimeZonebyID($model->id_customer)), new DateTime() ) ;
			 $offset = timezone_offset_get( new DateTimeZone(Customers::getTimeZonebyID($model->id_customer)), new DateTime() ) ;
			$str_time = SupportDesk::timezone_offset_string($offset); 	sscanf($str_time, "%d:", $hours);
			$hours=$hours-3;		 $time_seconds = $hours * 3600 ;	$timestamp = strtotime($comment['date'])+ $time_seconds; $time = date('H:i', $timestamp) ;
			$customer_time=date("d/m/Y",strtotime($comment['date']))." ".$time;	*/
			
			$timeoff= Yii::app()->db->createCommand("select time_offset from support_desk where id= ". $comment['id_support_desk'])->queryScalar();
			$customer_time=Yii::app()->db->createCommand("select ('".$comment['date']."' - INTERVAL ".$timeoff." HOUR) + INTERVAL (select t.time_offset from time_zone t where time_zone=(select c.time_zone from customers c where id ='".$model->id_customer."')) HOUR")->queryScalar();
			echo  Yii::app()->user->isAdmin == 1 ?  date("d/m/Y H:i:s",strtotime($comment['date'])) :  date("d/m/Y H:i:s",strtotime($customer_time)) ; ?> 
			</td>	<td class="tdattachment">	<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery" style="float:left;">
				<?php $files = $model->getFiles($comment['id']);	foreach ($files as $file) {	$path_parts = pathinfo($file['path']);	?>
					<div class=" template-download fade" style="float:left;margin-right:5px" >	<div class="title">
							<a href="<?php echo $this->createUrl('site/download', array('file' => $file['path']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
						</div>	</div>	<?php } ?>	</div>	</td>	</tr>	<?php } ?>	</tbody> </table>
<?php } if( $model->status == SupportDesk::STATUS_CONFIRME_CLOSED) { ?>
<div class="submit nobackground" style="display:block"><?php echo CHtml::Button(Yii::t('translations','Reopened'), array('class'=>'reopen','onClick'=>'SpecifyReason();')); ?></div> <?php } ?>
<script type="text/javascript">																												// SpecifyReason() chage the status of incident after specifying the reason		
function deleteAtt(id_comm,filename,customer) {
	$.ajax({type: "POST",	data: {'id_comm':id_user,'customer':customer,'filename':filename},				
	  	url: "<?php echo Yii::app()->createAbsoluteUrl('supportDesk/deleteUpload', array('id' => $model->id));?>",
	  	dataType: "json", 	success: function(data) {
		  	if (data) {
			  	if (data.status == 'saved' && data.html) {
			  		$('.header_content').html(data.html); 		$('.header_content').removeClass('hidden');  		$('.edit_header_content').addClass('hidden');
			  	} else {
			  		if (data.status == 'success' && data.html) {	$('.edit_header_content').html(data.html);		}
			  	}
			  	showErrors(data.error);
		  	}	}	});  }
</script>