<?php $tpl = '';	$buttons = array();
if(GroupPermissions::checkPermissions('groups-users','write')){
	$tpl = '{update}{delete}';
	$buttons = array(
				'update' => array('label' => Yii::t('translations', 'Move User'),'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("groups/moveUser", array("id"=>$data->id))',
	                	'options' => array('onclick' => 'getGroups(this);return false;',)
				),'delete' => array('label' => Yii::t('translations', 'Delete User'),'imageUrl' => null,
						'url' => 'Yii::app()->createUrl("groups/deleteUser", array("id"=>$data->id))',  
	                	'options' => array('class' => 'delete',)),	); }
	$this->widget('zii.widgets.grid.CGridView', array('id'=>'users-group-grid','dataProvider'=>$model->getMembersProvider(),
	'summaryText' => '','emptyText' => Yii::t('translation', 'No users assigned'),'pager'=> Utils::getPagerArray(),
	'columns'=>array(
		array('header' => 'Username','value' => '$data->user->username',),
		array('header' => 'First Name','value' => '$data->user->firstname',),
		array('header' => 'Last Name','value' => '$data->user->lastname',),
		array('header' => 'Job Title','value' => '$data->user->userPersonalDetails && $data->user->userPersonalDetails->job_title ? $data->user->userPersonalDetails->job_title : ""',),
		array('class'=>'CCustomButtonColumn','template'=>$tpl,'htmlOptions'=>array('class' => 'button-column'),'buttons'=>$buttons),	), )); ?> 
<br clear="all" />
<script type="text/javascript">
function getUsers(){
	var id_group = $('.group-view').attr('data-id');
	if (!$('#users-list').is(':visible')){
		$.ajax({
	 		type: "POST",url: "<?php echo Yii::app()->createAbsoluteUrl('groups/getUnssignedUsers');?>", 
		  	dataType: "json",data: {id:id_group},
		  	success: function(data) {
		  		if (data.status == "success") {
			  		$('#users-list').html(data.div); $('.action_list').hide(); $('#users-list').show(); $('#users-list').find('.scroll_div').mCustomScrollbar();
		  		} } });
	} else {	$('#users-list').fadeOut(100);	}
}
function moveUser(element, id_group, id_usergroup) {
	$.ajax({
 		type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('groups/moveUser');?>", dataType: "json",
	  	data: {'new_group':id_group, 'id_usergroup':id_usergroup},
	  	success: function(data) {
	  		if (data.status == "success") {
	  			$.fn.yiiGridView.update('users-group-grid', { data: $(this).serialize() }); } }	});
}
function getGroups(element) {
	var groupList = $(element).siblings('.groups-list');	var isNew = false;
	if (groupList.length == 0){		isNew = true;	}
	if (isNew || !groupList.is(':visible')){
		var parent	= $(element).parent();	var id_user = $(element).attr('href').split('/').slice(-1)[0];
		$.ajax({
	 		type: "POST",  	url: "<?php echo Yii::app()->createAbsoluteUrl('groups/getOtherGroups', array('id'=>Yii::app()->controller->actionParams['id']));?>", 
		  	dataType: "json",  	data: {'id_usergroup':id_user},
		  	success: function(data) {
		  		if (data.status == "success") {
			  		if (isNew) { parent.append("<div class='groups-list listofactions'>" + data.div + "</div>"); parent.find('.scroll-pane').jScrollPane();
			  		} else {
			  			groupList.html(data.div); groupList.show(); groupList.find('.scroll-pane').jScrollPane();
			  		}		  		
		  		}
	  		} });
	}else{
		if (!isNew){ groupList.fadeOut(100); }	} }
<?php if(GroupPermissions::checkPermissions('groups-users','write')){ ?>
	function assignUsers() {
		if ($('#unassigned-users-form').serialize() != '') {
			$.ajax({
		 		type: "POST", url: "<?php echo Yii::app()->createAbsoluteUrl('groups/assignUsers', array('id'=>$model->id));?>", 
			  	dataType: "json", data: $('#unassigned-users-form').serialize(),
			  	success: function(data) {
			  		if (data.status == "success") {
			  			$.fn.yiiGridView.update('users-group-grid', { data: $(this).serialize() }); $('#users-list').fadeOut(100);
			  		} } });
		} else { alert("<?php echo Yii::t('translation', 'You have to select at least one user in order to save!')?>"); }	}
<?php } ?>
</script>