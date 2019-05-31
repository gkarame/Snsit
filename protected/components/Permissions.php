<?php
class Permissions
{
    private $_id;
    public function getId()
    {     return $this->_id;    }	
	public static function getTabs(){
		$a = self::getItems();
		$permissions = self::getGroupRights(Yii::app()->controller->actionParams['id']);
		$tabs = array( Yii::t('translations', 'General') => Yii::app()->controller->renderPartial('_permissions_tabs', array('user_permissions' => $permissions, 'tab' => 'general', 'menu' => $a), true));
		foreach($a as $key => $menu) {
			$tabs[Yii::t('translations', ucfirst($key))] = Yii::app()->controller->renderPartial('_permissions_tabs', array('user_permissions' => $permissions, 'tab' => $key, 'menu' => $a[$key]['items']), true);
		}
		return $tabs;
	}
	public static function getItems(){
		return 
			array(
				'alerts'=> array(
						'label'=>'',
						'items'=>array(
							'expenses_sheets' => array('label'=>Yii::t('translation', 'Expense Sheets')),
							'issue_tickets' => array('label'=>Yii::t('translation', 'Issue Tickets')),
							'timesheets_new' => array('label'=>Yii::t('translation', 'New Timesheets')),
							'passports_alerts' => array('label'=>Yii::t('translation', 'Passports to Expire')),
							'project_alerts' => array('label'=>Yii::t('translation', 'Project Alerts')),
							'timesheets_submittet' => array('label'=>Yii::t('translation', 'Submitted Timesheets')),
							'system_down' => array('label'=>Yii::t('translation', 'System Down')),
							'birthdays' => array('label'=>Yii::t('translation', 'Users Birthdays')),
							'visas_alerts' => array('label'=>Yii::t('translation', 'Visas to Expire')),	),	),
				'booking'=> array(
						'label'=>'',
						'items'=>array(
								'list' => array('label'=>Yii::t('translation', 'List'))),	),	
				'customers' => array(
						'label'=>'',
						'items'=>array(
							'attachments' => array('label'=>Yii::t('translation', 'Attachments')),
							'connections' => array('label'=>Yii::t('translation', 'Customers Connections')), 
							'eas' => array('label'=>Yii::t('translation', 'Customers Eas')),
							'general_customers' => array('label'=>Yii::t('translation', 'Customers General')),
							'invoices' => array('label'=>Yii::t('translation', 'Customers Invoices')),
							'list' => array('label'=>Yii::t('translation', 'Customers List')),
						),	),
				'dashboard'=> array(
						'label'=>'',
						'items'=>Widgets::getAllWidgetsForPermission(),	),
				'deployments' => array(
						'label'=>'',
						'items'=>array(
							'list' => array('label'=>Yii::t('translation', 'deployments')),	),	),
				'eas' => array(
						'label'=>'',
						'items'=>array(
							'list' => array('label'=>Yii::t('translation', 'EAs List')),	),	),
				'expenses' => array(
						'label'=>'',
						'items'=>array(
							'expenses_approval' => array('label'=>Yii::t('translation', 'Expenses Approval')),	),	),
				'financial' => array(
						'label'=>'',
						'items'=>array(
							'invoices' => array('label'=>Yii::t('translation', 'Invoices')),
							'incomingTransfers' => array('label'=>Yii::t('translation', 'Incoming Transfers')),
							'maintenance' => array('label'=>Yii::t('translation', 'Maintenance')),
							'receivables' => array('label'=>Yii::t('translation', 'Receivables')),	),	),
				'groups'=> array(
						'label'=>'',
						'items'=>array(
							'users' => array('label'=>Yii::t('translation', 'Groups Assigned Users')),
							'notifications' => array('label'=>Yii::t('translation', 'Groups E-mail Notifications')),
							'list' => array('label'=>Yii::t('translation', 'Groups List')),
							'permissions' => array('label'=>Yii::t('translation', 'Groups Permissions')),	),	),
				'internal' => array(
						'label'=>'',
						'items'=>array(
							'list' => array('label'=>Yii::t('translation', 'Internal Projects')),	),	),
				'ir'=> array(
						'label'=>'',
						'items'=>array(
							'assign-installationrequests' => array('label'=>Yii::t('translation', 'IR Assign')),
							'close-installationrequests' => array('label'=>Yii::t('translation', 'IR Close')),
							'general-installationrequests' => array('label'=>Yii::t('translation', 'IR General')),	),	),
				'performance'=> array(
						'label'=>'',
						'items'=>array(
							'customer_satisfaction' => array('label'=>Yii::t('translation', 'Customer Satisfaction')),
							'quality' => array('label'=>Yii::t('translation', 'Quality')),
							'productivity' => array('label'=>Yii::t('translation', 'Productivity')), ),		),	
				'projects' => array(
						'label'=>'',
						'items'=>array(
							'alerts' => array('label'=>Yii::t('translation', 'Alerts')),
							'attachments' => array('label'=>Yii::t('translation', 'Attachments')),
							'projects_general' => array('label'=>Yii::t('translation', 'General')),
							'list' => array('label'=>Yii::t('translation', 'List')),
							'milestones' => array('label'=>Yii::t('translation', 'Milestones')),
							'tasks' => array('label'=>Yii::t('translation', 'Tasks')),	),	),
				'quality'=> array(
						'label'=>'',
						'items'=>array(
								'index' => array('label'=>Yii::t('translation', 'QA Tasks')),	),	),
				'quicklinks'=> array(
						'label'=>'',
						'items'=>Quicklinks::getAllQuicklinksForPermission(),	),
				'settings'=> array(
						'label'=>'',
						'items'=>array(
							'codelists' => array('label'=>Yii::t('translation', 'Settings Code Lists')),
							'general_settings' => array('label'=>Yii::t('translation', 'Settings General')),	),	),
				'rsr'=> array(
						'label'=>'',
						'items'=>array(
							'list' => array('label'=>Yii::t('translation', 'List'))),	),	
				'sma'=> array(
						'label'=>'',
						'items'=>array(
								'index' => array('label'=>Yii::t('translation', 'SMA Desk')),	),	),
				'suppliers'=> array(
						'label'=>'',
						'items'=>array(
								'list' => array('label'=>Yii::t('translation', 'List'))),	),
				'supportdesk'=> array(
						'label'=>'',
						'items'=>array(
							'list' => array('label'=>Yii::t('translation', 'List'))),	),											
				'tandm'=> array(
						'label'=>'',
						'items'=>array(
								'index' => array('label'=>Yii::t('translation', 'T&M')),	),		),				
				'timesheets' => array(
						'label'=>'',
						'items'=>array(
								'timesheets_approval' => array('label'=>Yii::t('translation', 'Time Sheet Approval')),	),		),				
				'trainings'=> array(
						'label'=>'',
						'items'=>array(
								'general-trainings' => array('label'=>Yii::t('translation', 'Trainings')),	),	),
				'travel'=> array(
						'label'=>'',
						'items'=>array(
								'list' => array('label'=>Yii::t('translation', 'List'))),	),				
				'users' => array(
						'label'=>'',
						'items'=>array(
							'attachments' => array('label'=>Yii::t('translation', 'Attachments')),
							'hr' => array('label'=>Yii::t('translation', 'HR Details')),
							'personal' => array('label'=>Yii::t('translation', 'Personal Details')),
							'list' => array('label'=>Yii::t('translation', 'Users List')),
							'visas' => array('label'=>Yii::t('translation', 'Visas')),	),	),	);
	}
	public static function save($post){ 
		$id = (int)$post['id'];
		unset($post['id']);
		self::delete_all($id);			
		$sql = "INSERT INTO permissions (`group_id`, `page`, `read`, `write`) VALUES(:group,:page,:read,:write)";
		$command = Yii::app()->db->createCommand($sql); 	$command->bindParam(":group", $id, PDO::PARAM_INT);
		foreach ($post as $page=>$p){
			$read = 0;	$write = 0;
			foreach ($p as $item){
				switch ($item){
					case 1:
						$read = 1;
						break;
					case 2:
						$write = 1;
						break;
					case is_array($item):
						$read = implode($item);
						break;
				}	}
			$command->bindParam(":page",$page,PDO::PARAM_STR);
			$command->bindParam(":read", $read,PDO::PARAM_STR);
			$command->bindParam(":write", $write,PDO::PARAM_STR);			
			$command->execute();
		}	}
	public static function delete_all($id){
		$id = (int) $id;	$sql="DELETE FROM permissions WHERE group_id = '{$id}'";	Yii::app()->db->createCommand($sql)->execute();  }
	public static function getGroupRights($group_id){
		$group_id = (int)$group_id;	$sql="SELECT * FROM permissions where group_id = '{$group_id}'";	$users = Yii::app()->db->createCommand($sql)->queryAll();	$return = array();		
		foreach($users as $rights){		$return[$rights['page']]['read'] = $rights['read'];		$return[$rights['page']]['write'] = $rights['write'];	}
		return $return;
	}
	public static function hasPermission($id, $type='read'){
		$rights = self::getGroupRights();
		if(isset($rights[$type]) && $rights[$type] == 1){
			return true;
		}
		return false;
	}	
}