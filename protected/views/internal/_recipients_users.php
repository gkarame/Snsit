<form id="unassigned-recipients-form" method="post">
	<div class="listofactions unassigned ">			
		<div class="title">ASSIGN RECIPIENTS</div>	
			<div class="width270 scroll_div" style="height: 230px;">	
				<ul class="cover width270">
				<?php foreach ($users as $user) {?>		
					<li class="row userAssign">
						<input type="checkbox" id="<?php echo $user['id'];?>" value="<?php echo $user['id'];?>" name="checked[]" />
						<label for="<?php echo $user['id'];?>">
						<span class="input"></span>	<?php echo $user['firstname'].' '.$user['lastname'];?>	</label>
					</li>
				<?php } ?>		</ul>
			</div>
		<ul class="act" style=" height:30px; padding-top:20px; width:251px;">	
			<li class="customBtn">
				<a href="javascript:void(0);" class="save customSaveBtn ua" onclick="assignrecipients();"><?php echo Yii::t('translation', 'Save');?></a>
				<a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('#recipients-list').fadeOut(100);"><?php echo Yii::t('translation', 'Cancel');?></a>	
			</li>
		</ul>
	</div>
</form>