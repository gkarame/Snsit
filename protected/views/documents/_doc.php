<div class="fileitem">
	<div class="name">
		<input type="checkbox" name="docs[]" value="<?php echo $data->id;?>" />
		<?php if(GroupPermissions::checkPermissions($data->category->module.'-attachments','write')){?>
			<a href="<?php echo $this->createUrl('documents/update', array('id' => $data->id, 'action' => $action));?>"><?php echo $data->document_title;?></a>
		<?php }else{	echo $data->document_title; }?>
	</div>
	<div class="description">
		<div class="desc inline-block">
			<?php echo $data->description;?>
		</div>
		<div class="author inline-block">
			<span>By </span>
			<?php echo $data->author->fullname;?>
			<br />
			<?php $path = $data->getFile(true); echo Utils::getReadableFileSize(filesize($path));?>
			<span>|</span>
			<?php echo date('d/m/Y', strtotime($data->uploaded));?>
		</div>
		<a href="<?php echo $this->createUrl('site/download', array('file' => $path));?>" class="type inline-block <?php echo $data->getExtension();?>"></a>
		<a href="<?php echo $this->createUrl("site/shareby", array("id"=>$data->id));?>" class="letter inline-block shareby_button" onclick="shareBySubmit(this, 'documents');return false;"></a>
	</div>
</div>