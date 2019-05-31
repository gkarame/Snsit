<?php $comments = $model->getComments();  if (!empty($comments)) {	?>
<table class="posts_table">
	<thead>		<tr>	<td class="tduser">USER</td>	<td class="tdcomm">COMMENT</td>		<td class="tdstatus">STATUS</td>	<td class="tddate">DATE</td>
			<td class="tdattachment">ATTACHMENT</td></tr></thead>	<tbody>	
			<?php foreach ($comments as $comment) { ?>	<tr>
				<td class="tduser"><?php echo Users::getUsername($comment['id_user']);?></td>
				<td class="tdcomm"><?php echo $comment['comment'];?></td>	
				<td class="tdstatus"><?php echo SupportRequest::getStatusLabel($comment['status']);?></td>
				<td class="tddate"><?php echo date("d/m/Y H:i:s",strtotime($comment['date'])) ?> </td>	
				<td class="tdattachment">	
				<div class="files exppage" data-toggle="modal-gallery" data-target="#modal-gallery" style="float:left;">
					<?php $files = $model->getFiles($comment['id']);	
							foreach ($files as $file) {	
								$path_parts = pathinfo($file['path']);	?>
								<div class=" template-download fade" style="float:left;margin-right:5px" >	<div class="title">
									<a href="<?php echo $this->createUrl('site/download', array('file' => $file['path']));?>" title="<?php echo $path_parts['basename'];?>" rel="gallery" download="<?php echo $path_parts['basename'];?>"><?php echo $path_parts['basename'];?></a>
								</div>	</div>	
							<?php } ?>	</div>	
			</td>	</tr>	
			<?php } ?>
</tbody> </table>
<?php } ?>
<script type="text/javascript">	
</script>