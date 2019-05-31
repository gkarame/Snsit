<div class="group-view mytabs hidden" data-id="<?php echo $model->id;?>">
	<?php $tabs = array();
	if(GroupPermissions::checkPermissions('groups-users')){
		$tabs[Yii::t('translations', 'Assigned Users')] = $this->renderPartial('_assign_users', array('model'=>$model), true);
	}
	if(GroupPermissions::checkPermissions('groups-permissions')){
		$tabs[Yii::t('translations', 'Permissions')] = $this->renderPartial('permissions', array('model'=>$model), true);
	}
	if(GroupPermissions::checkPermissions('groups-notifications')){
		$tabs[Yii::t('translations', 'E-mail Notifications')] = $this->renderPartial('_email_notifications', array('model'=>$model), true);
	}
	$tabs[Yii::t('translations', 'Default Tasks')] = $this->renderPartial('_default_tasks', array('model'=>$model), true);
	$this->widget('CCustomJuiTabs', array('tabs'=>$tabs,'options'=>array('collapsible'=>false,'active' =>  'js:configJs.current.activeTab'),
	    'headerTemplate'=> '<li><a href="{url}">{title}</a></li>',
	    'html_to_add' => GroupPermissions::checkPermissions('groups-users','write') ? '<div class="wrapper_action">
							<div onclick="chooseActions();" class="action"><u><b>ACTION</b></u></div>
							<div class="action_list">
						    	<div class="headli"></div>
								<div class="contentli">
									<div class="cover">
										<div class="li noborder" onclick="getUsers();"> ASSIGN NEW USER </div>
									</div>
								</div>
								<div class="ftrli"></div>
						    </div>
						    <div id="users-list" style="display:none;"></div>
						  </div>' : ''));	?>
</div>
<script> function checkStatus(){} </script>