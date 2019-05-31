<fieldset id="assign-users-form" class="unassigned_receivables"><div class="receivables_assigned listofactions unassigned">	<div class="headli"></div>
			<div class="contentli scroll_div">	<div class="title">ASSIGN NEW USERS</div>	<ul class="cover">	<?php foreach ($users as $user) {?>
						<li class="row userAssign">	<input type="radio" id="<?php echo $user['id'];?>" value="<?php echo $user['id'];?>" name="checked" />
							<label for="<?php echo $user['id'];?>">	<span class="input"></span>	<?php echo $user['firstname'].' '.$user['lastname'];?>	</label> </li>	<?php } ?>	</ul>	</div>
			<ul class="act"><li class="customBtn"><a href="javascript:void(0);" class="save customSaveBtn ua" onclick="assignUsers();"><?php echo Yii::t('translation', 'Save');?></a>					
					<a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('#users-list').fadeOut(100);"><?php echo Yii::t('translation', 'Cancel');?></a>	</li></ul>	<div class="ftrli"></div></div></fieldset>