<div class="headli"></div>
	<div class="contentli spMove scroll-pane">	
		<ul class="cover">
		<?php foreach ($groups as $group) {?>
			<li>	<a href="javascript:void(0);" onclick="moveUser(this, '<?php echo $group->id;?>', '<?php echo $id_usergroup;?>')"><?php echo $group->name;?></a> </li>
		<?php } ?>
		</ul>
	</div>			
<div class="ftrli"></div>
