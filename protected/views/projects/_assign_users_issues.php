<form id="assigned-users-issue-form" method="post">
<div class="listofactions unassigned"><div class="headli"></div><div class="contentli scroll_div"><div class="title">UNASSIGN USERS</div>
<ul class="cover"><?php foreach ($users as $user) {?><li class="row userAssign">
<input type="checkbox" id="<?php echo $user['id'];?>" value="<?php echo $user['id'];?>" name="checked[]" />
<label for="<?php echo $user['id'];?>"><span class="input"></span>
<?php echo $user['firstname'].' '.$user['lastname'];?></label></li><?php } ?></ul></div><ul class="act"><li class="customBtn">
<a href="javascript:void(0);" class="save customSaveBtn ua" onclick="unassignUsersIssues();"><?php echo Yii::t('translation', 'Save');?></a>
<a href="javascript:void(0);" class="customCancelBtn ua" onclick="$('#users-list-issues').fadeOut(100);"><?php echo Yii::t('translation', 'Cancel');?></a>
</li></ul><div class="ftrli"></div></div></form>