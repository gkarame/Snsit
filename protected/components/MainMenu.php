<?php Yii::import('zii.widgets.CMenu');
class MainMenu extends CMenu{
	protected function renderMenu($items){
		if(count($items)){
			echo CHtml::openTag('ul',$this->htmlOptions)."\n";
			$this->renderMenuRecursive($items, 1);
			echo CHtml::closeTag('ul');
		}	}
	protected function renderMenuRecursive($items, $parent = 0){
		$count=0;	$n=count($items);
		foreach($items as $item){
			$count++;
			$options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
			$class=array();
			if($item['active'] && $this->activeCssClass!='')
				$class[]=$this->activeCssClass;
			if($count===1 && $this->firstItemCssClass!==null)
				$class[]=$this->firstItemCssClass;
			if($count===$n && $this->lastItemCssClass!==null)
				$class[]=$this->lastItemCssClass;
			if($this->itemCssClass!==null)
				$class[]=$this->itemCssClass;
			if($class!==array()){
				if(empty($options['class']))
				$options['class']=implode(' ',$class);
				else
				$options['class'].=' '.implode(' ',$class);
			}			
			echo CHtml::openTag('li', $options); $menu=$this->renderMenuItem($item);
			if(isset($this->itemTemplate) || isset($item['template'])){
				$template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template,array('{menu}'=>$menu));
			}
			else
				echo $menu;

			if (isset($item['items']) && count($item['items'])){
				echo "\n<div class='menuDiv'><span class='top'></span>";
				echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
				$this->renderMenuRecursive($item['items']);
				echo CHtml::closeTag('ul')."\n";
				echo "<span class='bottom'></span></div>\n";
			}		
			echo CHtml::closeTag('li')."\n";
			if ($parent == 1 && $count != $n) echo "<li class='shadow'></li>\n";
		}	}
	public static function getCustomerItems(){
		return array('supportDeskCustomerView' => array(
						'label'=>'','itemOptions'=>array('class' =>'supportdeskcustomerview  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array(
								'incidents' => array('label'=>Yii::t('translation', 'Incidents'), 'url'=>array('/supportDesk/index'),'visible' => SupportDesk::isCustomer()),
								'statistics' => array('label'=>Yii::t('translation', 'Analytics'), 'url'=>array('site/index'),'visible' => SupportDesk::isCustomer()),	),	));	}
	public static function getItems(){
		return 
			array(
				'security' => array(
						'label'=>'','itemOptions'=>array('class'=>'security menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('users' => array('label'=>Yii::t('translation', 'Users'), 'url'=>array('/users/index'), 'visible' => GroupPermissions::checkPermissions('general-users')),
								'groups' => array('label'=>Yii::t('translation', 'Groups'), 'url'=>array('/groups/index'), 'visible' => GroupPermissions::checkPermissions('general-groups')),
								'settings' => array('label'=>Yii::t('translation', 'Settings'), 'url'=>array('/settings/index'), 'visible' => GroupPermissions::checkPermissions('general-settings')),
						),		),
				'setup' => array(
						'label'=>'','itemOptions'=>array('class'=>'setup  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('customers' => array('label'=>Yii::t('translation', 'Customers'), 'url'=>array('/customers/index'), 'visible' => GroupPermissions::checkPermissions('general-customers')),
								'eas' => array('label'=>Yii::t('translation', 'EAs'), 'url'=>array('/eas/index'), 'visible' => GroupPermissions::checkPermissions('general-eas')),
								'deployments' => array('label'=>Yii::t('translation', 'Deployments'), 'url'=>array('deployments/index'), 'visible' => GroupPermissions::checkPermissions('general-deployments')),
								'tandM' => array('label'=>Yii::t('translation', 'T&M'), 'url'=>array('/tandM/index'), 'visible' => GroupPermissions::checkPermissions('general-tandM')),
								'projects' => array('label'=>Yii::t('translation', 'Projects'), 'url'=>array('/projects/index'), 'visible' => GroupPermissions::checkPermissions('general-projects')),
								'internal' => array('label'=>Yii::t('translation', 'Internal Projects'), 'url'=>array('internal/index'), 'visible' => GroupPermissions::checkPermissions('general-internal')),
								'supportDesk' => array('label'=>Yii::t('translation', 'SRs'), 'url'=>array('/supportDesk/index'), 'visible' => GroupPermissions::checkPermissions('general-supportDesk')),
								'supportRequest' => array('label'=>Yii::t('translation', 'RSRs'), 'url'=>array('/supportRequest/index'), 'visible' => GroupPermissions::checkPermissions('general-rsr')),
								'24over7' => array('label'=>Yii::t('translation', 'Support Schedule'), 'url'=>array('/fullsupport/index'), 'visible' => GroupPermissions::checkPermissions('general-supportDesk')),
								'performance' => array('label'=>Yii::t('translation', 'Resource Performance'), 'url'=>array('/performance/index'), 'visible' => GroupPermissions::checkPermissions('general-performance')),
								'quality' => array('label'=>Yii::t('translation', 'QA Management'), 'url'=>array('/quality/index'), 'visible' => GroupPermissions::checkPermissions('general-quality')),
								'trainings' => array('label'=>Yii::t('translation', 'Trainings'), 'url'=>array('/trainingsnewmodule/index'), 'visible' => GroupPermissions::checkPermissions('general-trainings')),
								'installationrequests' => array('label'=>Yii::t('translation', 'Installation Requests'), 'url'=>array('/installationrequests/index'), 'visible' => GroupPermissions::checkPermissions('ir-general-installationrequests')),
								'sma' => array('label'=>Yii::t('translation', 'SMA Desk'), 'url'=>array('/sma/index'), 'visible' => GroupPermissions::checkPermissions('general-sma')),
								'Travel Requests' => array('label'=>Yii::t('translation', 'Travel Requests'), 'url'=>array('/booking/index'), 'visible' => GroupPermissions::checkPermissions('general-booking')),
						),	),
				'expense' => array('label'=>'','itemOptions'=>array('class'=>'expense  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('userList' => array('label'=>Yii::t('translation', 'My Expenses'), 'url'=>array('/expenses/index')),
								'travel' => array('label'=>Yii::t('translation', 'Travel Expenses'), 'url'=>array('/travel/index'), 'visible' => GroupPermissions::checkPermissions('general-travel')),
								'managerList' => array('label'=>Yii::t('translation', 'Expense Approval'), 'url'=>array('/expenses/approval')),
						),	),
				'timesheet' => array('label'=>'','itemOptions'=>array('class' =>'timesheet  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('userCurrent' => array('label'=>Yii::t('translation', 'Current Time Sheet'), 'url'=>array('/timesheets/current')),
								'userList' => array('label'=>Yii::t('translation', 'My Time Sheets'), 'url'=>array('/timesheets/index')),
								'approval' => array('label'=>Yii::t('translation', 'Time Sheet Approval'), 'url'=>array('/timesheets/approval'), 'visible' => GroupPermissions::checkPermissions('timesheets-timesheets_approval','read',Yii::app()->user->id,1)),
						),	),
				'financial' => array('label'=>'','itemOptions'=>array('class' =>'financial  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('suppliers' => array('label'=>Yii::t('translation', 'Suppliers'), 'url'=>array('/suppliers/index'), 'visible' => GroupPermissions::checkPermissions('general-suppliers')),
								'maintenance' => array('label'=>Yii::t('translation', 'Services Contracts'), 'url'=>array('maintenance/index'), 'visible' => GroupPermissions::checkPermissions('financial-maintenance','read',Yii::app()->user->id,1)),
								'invoices' => array('label'=>Yii::t('translation', 'Invoices'), 'url'=>array('invoices/index'), 'visible' => GroupPermissions::checkPermissions('financial-invoices','read',Yii::app()->user->id,1)),
                                'incomingTransfers' => array('label'=>Yii::t('translation', 'Incoming Transfers'), 'url'=>array('incomingTransfers/index'), 'visible' => GroupPermissions::checkPermissions('financial-incomingTransfers','read',Yii::app()->user->id,1)),
								'receivables' => array('label'=>Yii::t('translation', 'Receivables'), 'url'=>array('receivables/index'), 'visible' => GroupPermissions::checkPermissions('financial-receivables','read', Yii::app()->user->id, 1)),
						),	),
				'hr' => array('label'=>'','itemOptions'=>array('class' =>'hr  menuItem'),	'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array('leaveRequests' => array('label'=>Yii::t('translation', 'Leave Requests'), 'url'=>array('/requests')),			
						),		),
				'report' => array('label'=>'','itemOptions'=>array('class' =>'report  menuItem'),'submenuOptions'=> array('class'=>'dropMenu'),
						'items'=>array(
								'CSD' => array('label'=>Yii::t('translation', 'Customer Solution Details'), 'url'=>array('/reports/CSD')),
								'CustomerRating' => array('label'=>Yii::t('translation', 'Customer Rating'), 'url'=>array('/reports/customerRating')),
								'CustomerPlan' => array('label'=>Yii::t('translation', 'Customer Support Plan'), 'url'=>array('/reports/CustomerPlan')),
								'leaveSummary' => array('label'=>Yii::t('translation', 'Detailed Leaves Report'), 'url'=>array('/reports/leaveSummary')),
								'eaSummary' => array('label'=>Yii::t('translation', 'EA License Report'), 'url'=>array('/reports/eaSummary')),
								'expenseSummary' => array('label'=>Yii::t('translation', 'Expense Summary'), 'url'=>array('/reports/expenseSummary')),
								'fbrsList' => array('label'=>Yii::t('translation', 'FBRs List'), 'url'=>array('/reports/FbrsList')),
								'Licensing' => array('label'=>Yii::t('translation', 'License Audit Report'), 'url'=>array('/reports/Licensing')),
								'pendingTimesheet' => array('label'=>Yii::t('translation', 'Pending Timesheet Report'), 'url'=>array('/reports/pendingTimesheet')),
								'projectSummary' => array('label'=>Yii::t('translation', 'Project Profitability'), 'url'=>array('/reports/projectSummary')),
								'timesheetSnapshot' => array('label'=>Yii::t('translation', 'Timesheet Snapshot'), 'url'=>array('/reports/timesheetSnapshot')),
								'timesheetSummary' => array('label'=>Yii::t('translation', 'Timesheet Summary'), 'url'=>array('/reports/timesheetSummary')),
								'vacationAudit' => array('label'=>Yii::t('translation', 'Vacation Audit'), 'url'=>array('/reports/vacationAudit')),
								'vacationSummary' => array('label'=>Yii::t('translation', 'Vacation Balance Report'), 'url'=>array('/reports/vacationSummary')),
                                'TravelRequests' => array('label'=>Yii::t('translation', 'Travel Requests Report'), 'url'=>array('/reports/travelRequests')),
				),	),	);	} }