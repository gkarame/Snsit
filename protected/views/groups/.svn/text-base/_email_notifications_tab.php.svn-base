<input type="hidden" name="verify" value="1" />
<?php foreach ($notifs as $notif) {?>
	<div class="row chk" onclick="CheckOrUncheckInput(this)">
		<div class="input"></div>
		<input type="checkbox" name="checked[]" value="<?php echo $notif->id;?>" 
		<?php echo EmailNotificationsGroups::isActivated($id_group, $notif->id) ? 'checked' : '';?> />
		<label><?php echo $notif->name;?></label>
	</div>
<?php } ?>
