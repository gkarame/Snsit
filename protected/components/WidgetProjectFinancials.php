<?php
class WidgetProjectFinancials extends CWidget 
{
	public $widget;	
	public function getId($autoGenerate=false) {
		$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT id FROM widgets WHERE model = '$model'")->queryScalar();
	}	
	public static function getName(){
		$model = __CLASS__;
		return Yii::app()->db->createCommand("SELECT name FROM widgets WHERE model = '$model'")->queryScalar();
	}	
    public function run()
    {
    	$searchArray = isset($_GET['ProjectFinancials']) ? $_GET['ProjectFinancials'] : array('status' => ProjectFinancials::STATUS_ACTIVE, 'name' => '', 'customer_id' => '', 'id_parent' => '');
    	if ($searchArray['status'] == '')
    	{
    		$projects = Yii::app()->db->createCommand()
				    		->select('projects.id, projects.name AS project, customers.name AS customer')
				    		->from('projects')
				    		->join('customers', 'customers.id = projects.customer_id')
				    		->where('customers.name LIKE :customer AND projects.name LIKE :project'.(isset($searchArray['id_parent']) ? ' AND projects.id_parent IS NULL' : ''),
				    				array(':customer' => '%'.$searchArray['customer_id'].'%', ':project' => '%'.$searchArray['name'].'%'))
				    				->queryAll();
    	} else {
    		$projects = Yii::app()->db->createCommand()
				    		->select('projects.id, projects.name AS project, customers.name AS customer')
				    		->from('projects')
				    		->join('customers', 'customers.id = projects.customer_id')
				    		->leftJoin('projects_milestones', 'projects_milestones.id_project = projects.id')
				    		->where('customers.name LIKE :customer AND projects.name LIKE :project'
				    				.(isset($searchArray['id_parent']) ? ' AND projects.id_parent IS NULL' : '')
				    				.($searchArray['status'] == ProjectFinancials::STATUS_ACTIVE ? ' AND projects_milestones.status != "Closed"' : ($searchArray['status'] == ProjectFinancials::STATUS_INACTIVE ? ' AND projects_milestones.status = "Closed"' : '') ),
				    				array(':customer' => '%'.$searchArray['customer_id'].'%', ':project' => '%'.$searchArray['name'].'%'))
				    		->group('projects.id')
				    				->queryAll();
    	}
    	$returnArr = array();
    	foreach ($projects as $key => $project)
    	{
    		$tmpArr = array();
    		$tmpArr['id'] 				= $project['id'];
    		$tmpArr['project'] 			= '<a href="'.Yii::app()->createUrl("projects/view", array("id" => $project["id"])).'">'.$project['project'].'</a>';
    		$tmpArr['customer']			= $project['customer'];
    		$tmpArr['total_amount'] 	= Utils::formatNumber(Projects::getTotalAmount($project['id']));
    		$amount = Projects::getTotalAmount($project['id']);
    		$tmpArr['md']				= Utils::formatNumber(Projects::getProjectTotalManDaysByProject($project['id']));
    		$tmpArr['actual_md']		= Utils::formatNumber(Projects::getProjectActualManDays($project['id']));
    		$tmpArr['remaining_md']		= Utils::formatNumber(Projects::getProjectRemainingManDaysByProject($project['id']));
    		$tmpArr['original_rate']	= ($tmpArr['md'] != 0 ? Utils::formatNumber($tmpArr['total_amount']/$tmpArr['md']) : 0);
    		$tmpArr['actual_rate']		= ($tmpArr['actual_md'] != 0 ? Utils::formatNumber($tmpArr['total_amount']/$tmpArr['actual_md']) : 0);
    		$tmpArr['expenses_balance']	= (Projects::getProjectExpensesBalance($project['id'])=="Actuals")?"Actuals":Utils::formatNumber(Projects::getProjectExpensesBalance($project['id']),1);
    		$tmpArr['cost']				= Utils::formatNumber($tmpArr['actual_md'] * SystemParameters::getCost(),1);
    		$cost = $tmpArr['actual_md'] * SystemParameters::getCost();
    		$tmpArr['profit']			= Utils::formatNumber($tmpArr['total_amount'] - $cost,1);
    		$returnArr[] = $tmpArr;
    	}
    	$dataProvider=new CArrayDataProvider($returnArr, array(
    			'id'=>'id',
    			'sort'=>array(
    					'attributes'=>array(
    						'project', 'customer', 'total_amount', 'md', 'actual_md',
    						'remaining_md', 'original_rate', 'actual_rate', 'expenses_balance',
    						'cost', 'profit'
    					),
    			),
    			'pagination'=>array(
    				'pageSize' => Utils::getPageSize(),
    			),
    	));
    	$model = new ProjectFinancials();  $model->status = $searchArray['status'];    	
    	$this->render('/widgets/projectFinancials', array(
            'model'=>$model,
    		'provider'=>$dataProvider
        ));	
    }
}?>
