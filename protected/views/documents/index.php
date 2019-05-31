<?php if (!isset($module_category)){	$module_category = NULL;}
$categories = DocumentsCategories::getCategories($id_model, $model_table, $module_category);?>
<div class="documents_div">
	<div class="tree inline-block">
		<div class="bg">
			<?php foreach ($categories as $category) { ?>
				<div class="rowi sublevel1 <?php echo ($active != null && $active == $category['id']) ? 'active' : '';?>" id="li_<?php echo $category['id'];?>" onclick="getDocuments(this, '<?php echo $category['id'];?>');">
					<span style="text-transform: none;    font-size: 12px;font-weight: bold;"><?php echo $category['name'];?> <span>
					<div class="num"><?php echo $category['count']; ?></div>
				</div>
				<?php if (isset($category['children']) && count($category['children'])) { ?>
						<?php foreach ($category['children'] as $child) { ?>
							<div class="rowi sublevel2 children_<?php echo $category['id'];?>" onclick="getDocuments(this, '<?php echo $child['id'];?>');" id="li_<?php echo $child['id'];?>">
								<?php echo $child['name'];?>
								<div class="file"></div>
								<div class="num"><?php echo $child['count']; ?></div>
							</div>
							<?php if (isset($child['children']) && count($child['children'])) { ?>
								<?php foreach ($child['children'] as $chld) {?>
									<div class="rowi sublevel3 children_<?php echo $child['id'];?>"" onclick="getDocuments(this, '<?php echo $chld['id'];?>');" id="li_<?php echo $chld['id'];?>">
										<?php echo $chld['name'];?>
										<div class="file"></div>
										<div class="num"><?php echo $chld['count'];?></div>
									</div>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</ul>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
	<div class="viewer inline-block">
	<?php if(GroupPermissions::checkPermissions($model_table.'-attachments','write')){	?>
		<div class="niveau1">
			<div class="add" onclick="window.location.href='<?php echo Yii::app()->createAbsoluteUrl('documents/create', array('id_model'=>$id_model, 'tname' => $model_table, 'action' => $action, 'category' => $module_category));?>'"></div>
			<div class="delete" onclick="delete_documents();"></div>
		</div>
	<?php }?>		
		<div class="niveau2 hidden">	</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		if ($('.rowi').hasClass('active')) {	$('.rowi.active').trigger('click');
		} else {	$('.sublevel1:first').trigger('click');		}
	});	
	function deleteDocs() {
		$.ajax({
	 		type: "POST", 		data: $('#docs_delete').serialize(),					
		  	url: '<?php echo Yii::app()->createAbsoluteUrl('documents/delete');?>', dataType: "json",
		  	success: function(data) {
		  		if (data && data.status == 'success') {
			  		if (data.count > 0) {
				  		var oldCount = parseInt($('.tree .active').find('.num').html());
				  		var newCount = ((oldCount - data.count) > 0) ? (oldCount - data.count) : 0;
						$('.tree .active').find('.num').html(newCount);
			  		}
		  		}
		  		$.fn.yiiListView.update("docs_list",{});
	  		}
		});
	}
	function getDocuments(element, id) {
		var $this = $(element);		$('.rowi').removeClass('active');		$this.addClass('active');
		$.ajax({
	 		type: "GET",
	 		data: {'id' : id, 'id_model' : '<?php echo $id_model;?>', 'model_table' : '<?php echo $model_table;?>', 'action' : '<?php echo $action;?>'},					
		  	url: '<?php echo Yii::app()->createAbsoluteUrl('documents/getDocuments');?>', 
		  	dataType: "json",
		  	success: function(data) {
			  	if (data && data.status == 'success') {
				  	if (data.list) {	$('.niveau2').html(data.list);		$('.niveau2').removeClass('hidden');
				  	} else {	$('.niveau2').addClass('hidden');  	}
			  	}
	  		}
		}); 		 
	}
</script>