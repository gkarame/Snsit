<?php

class WidgetsController extends Controller
{
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete + deleteVisa', // we only allow deletion via POST request
		);
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
						'actions'=>array('index','setOrder', 'delete','easBarSort','customerPieSort','OverRunSort','ProjectRunOut','projectSort','billabilityBarSort','srBarSortClosed',
						'srBarSortSubmitted','ShowGraph','customerSatisfaction','showTrendDown','TestActuals','UnsatBarSortCustomer','srBarSortCustomer','srBarSortSystemShutdown','srBarSortTopCustomer','SrBarSortPriorityCustomer','SrOpenStatusCustomer','SrOpenSeverityCustomer','SubmittedCustomer','changeSrAvgRec','srBarSortReason','projectBarSortAlerts','srBarSortResource',
						'srTime','srSupport', 'getWidgetsOff', 'CountryRevenues', 'EaTypesRevenues', 'SoldByRevenues','getProjects','rescourceBarNonBillable','rescourceBarBillable','mostActiveProject','ProjectAlerts'),
						'expression'=>'!$user->isGuest',
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function init()
	{
		parent::init();
	}
	
	public function actionIndex()
	{
		if(Yii::app()->user->isAdmin){
		if (isset($_GET['Widgets']['id']))
		{
			$lastOrder =  	Yii::app()->db->createCommand('
				SELECT user_widgets.order as ord FROM user_widgets 
				WHERE user_widgets.user_id='. Yii::app()->user->id .' ORDER BY user_widgets.order DESC LIMIT 1 ')->queryRow();
			
			$widadd = new UserWidgets();
			$widadd->order = $lastOrder['ord']+1;
			$widadd->user_id = Yii::app()->user->id;
			$widadd->widget_id = $_GET['Widgets']['id'];
			$widadd->save();
		}
		
		$this->redirect(array('site/index'));
		}
		else{
		if (isset($_GET['Widgets']['id']))
		{
			$lastOrder =  	Yii::app()->db->createCommand('
				SELECT customer_widgets.order as ord FROM customer_widgets 
				WHERE customer_widgets.user_id='. Yii::app()->user->id .' ORDER BY customer_widgets.order DESC LIMIT 1 ')->queryRow();
			
			$widadd = new CustomerWidgets();
			$widadd->order = $lastOrder['ord']+1;
			$widadd->user_id = Yii::app()->user->id;
			$widadd->widget_id = $_GET['Widgets']['id'];
			$widadd->save();
		}
		
		$this->redirect(array('site/index'));
		}
	}
	
	/**
	 * 
	 * Retrieves a list of select options with all the widgets that are not on the selected dashboard
	 * @param int $id id of the dashboard
	 */
	public function actionGetWidgetsOff()
	{
		$data = Widgets::getWidgetsOff((int)$_POST['id'], true);

		$options = '<option value="">Choose widget</option>';
		foreach ($data as $key => $value)
		{
			$options .= "<option value='{$key}'>{$value}</option>";
		}

		echo json_encode(array('options' => $options));
		exit;
	}
	
	/**
	 * 
	 * Saves the order of the widgets on the dashboard for the user 
	 */
	public function actionSetOrder()
	{
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['id']))
		{
			$ids = explode(',', $_POST['id']);
			//dragtable order
			if (count($ids) > 1) 
			{
				$i = 0;
				foreach($ids as $v) 
				{
					$i++;
					$wid = UserWidgets::model()->findByPk($v);
					if ($wid !== null && Yii::app()->user->id == $wid->user_id)
					{
						$wid->order = $i;
						$wid->save();
					}
				}
			}
			echo json_encode(array('status' => 1));
		}
		}
		else{
			if (isset($_POST['id']))
		{
			$ids = explode(',', $_POST['id']);
			//dragtable order
			if (count($ids) > 1) 
			{
				$i = 0;
				foreach($ids as $v) 
				{
					$i++;
					$wid = CustomerWidgets::model()->findByPk($v);
					if ($wid !== null && Yii::app()->user->id == $wid->user_id)
					{
						$wid->order = $i;
						$wid->save();
					}
				}
			}
			echo json_encode(array('status' => 1));
		}
		}
	}

	/**
	 * 
	 * Removes a widget from the logged in user's dashboard
	 */
	public function actionDelete()
	{
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['id']))
		{
			$wid = UserWidgets::model()->findByPk((int)$_POST['id']);
			if ($wid !== null && Yii::app()->user->id == $wid->user_id)
			{
				echo json_encode(array('status' => (int)$wid->delete()));
			}
		}
		}
		else{
		if (isset($_POST['id']))
		{
			$wid = CustomerWidgets::model()->findByPk((int)$_POST['id']);
			if ($wid !== null && Yii::app()->user->id == $wid->user_id)
			{
				echo json_encode(array('status' => (int)$wid->delete()));
			}
		}
		}
	}
	
	public function actionEasBarSort()
	{
		if (isset($_POST['year']))
		{
			$time = $_POST['year'];
    		$date = array();
    		$date1 = array();
			$now =   date('Y',strtotime("now"));
			$data_chart = array();
			
			for($i=0;$i<12;$i++) 
			{
				$inter = date('Y-01',strtotime($now .' -'.$time.' year'));
				$date[] = date('Y-m',strtotime($inter .' + '.$i.' month'));
			}
			
			foreach ($date as $data)
			{
				$month = date('M',strtotime($data));
				$year_data = date('Y',strtotime($data));
				$val = WidgetEas::getAmountMonth($data);

								 array_push($data_chart,array('label' => $month."-".$year_data,
	            	  'value' => $val));
			}

    	}
    	else
    	{
	    	$date = array();
			$month =  date('Y-m');
			$data_chart = array();
			
			for ($i=11;$i>=0;$i--) 
			{
				$date[] = date('Y-m',strtotime($month . ' - '.$i.' month'));
			}
			
			foreach ($date as $data)
			{
				$month = date('M',strtotime($data));
				$year_data = date('Y',strtotime($data));
				$val = WidgetEas::getAmountMonth($data);
				array_push($data_chart,array('label' => $month."-".substr($year_data,2,2), 'value' => $val));
			}
    	}
    	
    	$chart = array( 
		          "xAxisName" => "Months",
		          "yAxisName" => "Total Amount",
		          "numberPrefix" => "$"
		);
		
    	echo json_encode($data_chart);
	}
	
	public function actionCustomerPieSort()
	{
		if (!isset(Yii::app()->session['top']))
			Yii::app()->session['top'] = '5';
			
		if (!isset(Yii::app()->session['year']))
			Yii::app()->session['year'] = date('Y');
				
		if (isset($_POST['top']))
		{
			Yii::app()->session['top'] = $_POST['top'];
		}
		
		if (isset($_POST['year']))
		{
			Yii::app()->session['year'] = $_POST['year'];
		}
		
		$date = array();
		$month =  date('Y-m');
		
		for ($i=11;$i>=0;$i--) 
		{
			$date[] = date('Y-m',strtotime($month . ' - '.$i.' month'));
		}
		
		$data_chart = WidgetCustomers::getCustomer(Yii::app()->session['year'],Yii::app()->session['top']);
    	$chart = array( 
    			"palette" => "2",
		        "animation" => "1",
		        "yaxisname" => "Sales Achieved",
		        "showvalues" => "1",
		        "numberprefix" => "$",
		        "formatnumberscale" => "0",
		        "showpercentintooltip" => "0",
		        "showlabels" => "0",
		        "showlegend" => "1"
		);
		
    	echo json_encode($data_chart);
	}
	public function actionShowGraph()
	{
		if (isset($_POST['id']))
		{
			$id = $_POST['id'];
		}

		$val= WidgetCustomerProfile::CharChart($id);
		print_r($val); exit;
		echo json_encode($val);
	}
	public function actionshowTrendDown()
	{
		$values = array();
		$axis = array();
        $query="SELECT DISTINCT(id_customer) FROM `support_desk` where `status`=5 and rate is not null and rate<>0";  
        $values=Yii::app($query)->db->createCommand($query)->queryAll();
		//print_r($values);exit;
        foreach ($values as $key => $value) 
      	{
          $customer_name=Customers::getNameByID($value['id_customer']);
			//echo $customer_name;exit;
			$Months= Yii::app()->db->createCommand("SELECT count(1), MONTH(rate_date), SUM(rate), rate_date FROM `support_desk` where id_customer ='".$value['id_customer']."' and rate_date >= '2016-01-01 00:00:00' 
      		and  `status`=5 and rate is not null and rate <>0 
            GROUP BY MONTH(rate_date) ORDER BY MONTH(rate_date)")->queryAll();
			if(sizeof($Months)>1)
			{
				$compareRate = array_slice($Months, -2);
				//print_r($compareRate);exit;

				$thismonthAvg = $compareRate[1]['SUM(rate)']/ $compareRate[1]['count(1)'];
				$lastMonthAvg = $compareRate[0]['SUM(rate)']/ $compareRate[0]['count(1)'];

			  	if ($thismonthAvg<$lastMonthAvg)
				{
			      	$customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryScalar();
			        if ($customer_issues != 0)
			        {
			           $customer_rate=Yii::app()->db->createCommand("SELECT sum(rate) FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryScalar();
            			$avg_rate=($customer_rate/$customer_issues);

            			$axis[$key]['older1']= (double)round($avg_rate,2);
    			        $axis[$key]['total1']= (double)$customer_issues;

            			$axis[$key]['tag1']= $value['id_customer']."- <span style='font-size:15px;'><b>".$customer_name."</b></span> \n Rate:".(double)round($avg_rate,2)." \n Number of issues:".(double)round($customer_issues,2)." " ;
          
          			}
	      		}
	      	}
     	}

      usort($axis, Widgets::build_sorter('total1'));
      $axis=array_reverse($axis);

      echo json_encode($axis);


	}
	public function actioncustomerSatisfaction()
	{
      $values = array();
      $axis = array();
      
		if (isset($_POST['cust']))
		{
			$query="SELECT id as id_customer FROM `customers` where name like '%".($_POST['cust'])."%'";
			//print_r($query);exit;
			//Yii::app()->session['year'] = $_POST['year'];
		}
		else
		{
			$query="SELECT DISTINCT(id_customer) FROM `support_desk` where `status`=5 and rate is not null and rate<>0";
      	}  
       
      $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
      $avg_rate=0;
      $customer_issues=0;
      $customer_rate=0;
      foreach ($values as $key => $value) 
      {
          $customer_name=Customers::getNameByID($value['id_customer']);
          $customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryScalar();
          if ($customer_issues != 0)
          {
            $customer_rate=Yii::app()->db->createCommand("SELECT sum(rate) FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryScalar();
            $avg_rate=($customer_rate/$customer_issues);

            $axis[$key]['older1']= (double)round($avg_rate,2);
            $axis[$key]['total1']= (double)$customer_issues;

            $axis[$key]['tag1']= $value['id_customer']."- <span style='font-size:15px;'><b>".$customer_name."</b></span> \n Rate:".(double)round($avg_rate,2)." \n Number of issues:".(double)round($customer_issues,2)." " ;
          }
      }
      usort($axis, Widgets::build_sorter('total1'));
      $axis=array_reverse($axis);

      echo json_encode($axis);
    }

	
	
	public function actionTestActuals()
	{
		
		if (isset($_POST['type']))
		{
			Yii::app()->session['type'] = $_POST['type'];
		}
		
		if (isset($_POST['status']))
		{
			Yii::app()->session['status'] = $_POST['status'];
		}

		if (isset($_POST['budget']))
		{
			Yii::app()->session['budget'] = $_POST['budget'];
			$budget=$_POST['budget'];
		
		}
			$values = array();
			$axis = array();
		
			
		
		  
     $select = " SELECT p.id , p.status from projects p , eas e ";
        
      $where ="WHERE (p.id=e.id_project or p.id=e.id_parent_project) and (e.TM<>'1' or e.TM is null) AND p.id_type in ('26' , '27') and e.old='No'  and e.status>=2 ";
      
      $orderby="order by p.id";


     

    if(isset(Yii::app()->session['status']) && Yii::app()->session['status']!="" && Yii::app()->session['status']!=" " && Yii::app()->session['status']!='100'){
      $where=$where." AND p.status=".Yii::app()->session['status']." "; 
          
    }else{
      Yii::app()->session['status']="" ;
      $where=$where." AND (p.status=0 or p.status=1 or p.status=2) ";

    }

    if(isset(Yii::app()->session['type']) && Yii::app()->session['type']!='100' && Yii::app()->session['type']!="" && Yii::app()->session['type']!=" "){
      $where=$where." AND p.id_type=".Yii::app()->session['type']." ";
        
    }else{
      Yii::app()->session['type']="" ;
      $where=$where." AND (p.id_type=26 or p.id_type=27 ) ";

    }
		/*
      if(isset(Yii::app()->session['budget']) && Yii::app()->session['budget']!='100' && Yii::app()->session['budget']!="" && Yii::app()->session['budget']!=" "){
      $where=$where." ".$budget." ";
        
    }else{
      Yii::app()->session['budget']="" ;
      $where=$where." ";

    }*/

     $query=$select." ".$where." ".$orderby ; 
    
     $values=Yii::app($query)->db->createCommand($query)->queryAll(); 

    foreach ($values as $key => $value) {
     
      if(Projects::getBudgetedMD($value['id'])<='1'){ $BudgetedMDs='1'; }else{  $BudgetedMDs=Projects::getBudgetedMD($value['id']); };
      if(Projects::getActualMD($value['id'])<='1'){ $ActualMDs='1'; }else{  $ActualMDs=Projects::getActualMD($value['id']); };
      $TotNetAamountUSD=Projects::getNetAmount($value['id']);
     	$x=0;
      $RemainingMDS=$BudgetedMDs - $ActualMDs;
      $ActualRate=$TotNetAamountUSD/$ActualMDs;
      if($budget=="lt25"){
      if( $TotNetAamountUSD<25000){
      	$x=$budget;
      }
      }
       if($budget=="lt75"){
      if( $TotNetAamountUSD>25000 && $TotNetAamountUSD<75000){
      	$x=$budget;
      }
      }
       if($budget=="mt75"){
       	
      if( $TotNetAamountUSD>75000){
      	$x=$budget;
      }
      }
      	if($x==$budget){
        $axis[$key]['tag1']= "<span style='font-size:15px;'><b>".Projects::getNameById($value['id'])."</b></span> \n Budgeted Amnt:".Utils::formatNumber($TotNetAamountUSD,2)."$ \n Budgeted MDs: ".Utils::formatNumber($BudgetedMDs)." \n Actual MDs: ".Utils::formatNumber($ActualMDs)." \n Remaining MDs: ".Utils::formatNumber($RemainingMDS)." \n  Budgeted Rate: ".Utils::formatNumber($TotNetAamountUSD/$BudgetedMDs,2)." $ \n  Actual Rate: ".Utils::formatNumber($ActualRate,2)." $";
      	$axis[$key]['older1']= (double)round($ActualRate,2);
        $axis[$key]['perc1']= (int)$value['status'];
        $axis[$key]['total1']= (double)round($TotNetAamountUSD,2);
     	}

     	echo $budget;
        
    }

    echo json_encode($axis);
  

    }
	public function actionOverRunSort()
	{

			$select ="							
				SELECT t1.customer_id , t1.projectid ,t1.id_type , t1.pstatus, t1.currency , t1.TotalMDs , t2.ActualMDs  , (t1.TotalMDs - t2.ActualMDs) AS RemaingMDs  ,((t1.TotalMDs - t2.ActualMDs) /IFNULL(t1.TotalMDs,1))*100 AS Overrun , (t1.Netamount/IFNULL(t2.ActualMDs,1))AS ActualRate , t1.netmandayrateusd AS BudgetedRate FROM 
					(
						SELECT
						sum(ei.man_days)as TotalMDs,
						  e.netamountusd as Netamount ,
						  e.netmandayrateusd ,
						  p.id  as projectid ,
							p.id_parent ,
						e.TM ,
						p.id_type ,
						p.status as pstatus,
						p.customer_id ,
						e.currency
						FROM eas_items ei ,eas e , projects p
						WHERE e.id=ei.id_ea AND (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24'  AND e.category<>'454'   AND e.category<>'496' and e.status>='2'
						GROUP BY p.id 
					) as t1 ,

					(
						SELECT  sum(uti.amount)/8  as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt  , projects_phases pp , projects p ,projects p2
									WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent)
						GROUP BY p2.id
					)  as t2			
			";

			$where ="WHERE t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') ";
			$groupby= "GROUP BY  t1.projectid";
			$orderby=" ";

		if (isset($_POST['type']))
		{
			Yii::app()->session['type'] = $_POST['type'];
		}
		
		if (isset($_POST['status']))
		{
			Yii::app()->session['status'] = $_POST['status'];
		}

		if (isset($_POST['order']))
		{
			Yii::app()->session['order'] = $_POST['order'];
		}

		if (isset($_POST['state']))
		{
			Yii::app()->session['state'] = $_POST['state'];
		}

		if(isset(Yii::app()->session['status']) && Yii::app()->session['status']!="" && Yii::app()->session['status']!=" " && Yii::app()->session['status']!='100'){
			$where=$where." AND t1.pstatus=".Yii::app()->session['status']." ";	
					
		}else{
			Yii::app()->session['status']="" ;
			$where=$where." AND (t1.pstatus=0 or t1.pstatus=1 or t1.pstatus=2) ";

		}

		if(isset(Yii::app()->session['type']) && Yii::app()->session['type']!='100' && Yii::app()->session['type']!="" && Yii::app()->session['type']!=" "){
			$where=$where." AND t1.id_type=".Yii::app()->session['type']." ";
				
		}else{
			Yii::app()->session['type']="" ;
			$where=$where." AND (t1.id_type=26 or t1.id_type=27) ";

		}

		if(isset(Yii::app()->session['order']) && Yii::app()->session['order']!="" && Yii::app()->session['order']!=" " && Yii::app()->session['order']!='100'){
			$orderby=$orderby."  ORDER BY ".Yii::app()->session['order']." ";	
					
		}else{
			Yii::app()->session['order']="" ;
			$orderby=$orderby." ORDER BY t1.TotalMDs DESC ";

		}
			
		 $query=$select." ".$where." ".$groupby." ".$orderby ;   
		 $results=Yii::app($query)->db->createCommand($query)->queryAll();
		 $axis= array();

		foreach ($results as $result) 
 		{
 			$tm=Projects::getTMstatus($result['projectid']);
 			
 			if ($tm == 0 )
 			{
 				$result['ActualMDs']=Projects::getActualMD($result['projectid']); 
 				if ($result['ActualMDs']==0)
				{
					$result['ActualMDs']=1;
				}
 				$result['TotalMDs']=Projects::getProjectTotalManDaysByProject($result['projectid']); 
 				if ($result['TotalMDs']==0)
				{
					$result['TotalMDs']=1;
				}
				$result['RemaingMDs']=$result['TotalMDs']-$result['ActualMDs'];
		 		$netamount=Projects::getNetAmount($result['projectid']);
		 		$result['ActualRate']= $netamount/$result['ActualMDs'];
		 		$result['Overrun']= (($result['TotalMDs']-$result['ActualMDs'])/$result['TotalMDs'])*100;
		 		
				$result['BudgetedRate']=Projects::getBudgetedRate($result['projectid']);
			}
		} 
		 // print_r($results);
		 //exit;
		if(isset(Yii::app()->session['state']) && Yii::app()->session['state']!="" && Yii::app()->session['state']!=" ")
		{
			if (Yii::app()->session['state'] =='0') {
				foreach ($results as $key => $result)
				{
					if ($result['Overrun']>0)
					{
						$axis[$key]=$result;
					}
				}
				$results=$axis;
			}
			else if (Yii::app()->session['state'] =='1') 
			{
			 	foreach ($results as $key => $result)
				{
					if ($result['Overrun']<0)
					{
						$axis[$key]=$result;
					}
				}
			 	$results=$axis;
			 }
		}
		
		echo json_encode(array(
    		'data'=>$results,
			'html' => $this->renderPartial('_list_projects', array('projects' => $results), true, false)
        ));
	}
	public function actionProjectRunOut()
	{	
		$requiredtype=" ";
		if (isset($_POST['type1']))
		{
			Yii::app()->session['type1'] = $_POST['type1'];
			$requiredtype= Yii::app()->session['type1'];
			//echo  $_POST['type']; exit;
		}

		if ($requiredtype!=" " && $requiredtype !=100)
		{
			$results=Yii::app()->db->createCommand("SELECT id, name, customer_id  FROM projects where status=1 and id_type =".$requiredtype." and  ((id_parent in (select ea_number from eas where TM=0)) OR id_parent is null) ")->queryAll();

		}
		else 
		{
			$results=Yii::app()->db->createCommand("SELECT id, name, customer_id  FROM projects where status=1 and  ((id_parent in (select ea_number from eas where TM=0)) OR id_parent is null)")->queryAll();
		}

 		$projects=array();

 		foreach ($results as $result) 
 		{ 
				$id=$result['id'];
	 			$customer_id=$result['customer_id'];
	 			$project=$result['name'];
				$customer_name=Customers::getNameByID($result['customer_id']);
				$budget= Projects::getBudgetedMD($result['id']);
				$total_incloff= Projects::getIncludingOffsetMD($result['id']);

				if ($budget < $total_incloff)
				{
					$str=' ';
					$actuals=Projects::getActualMD($result['id']);
					$offset= Projects::getTotalOffsetMD($result['id']);
					$offset_req= Projects::getTotalOffset($result['id']);
					$reasons= Projects::getreasons($result['id']);
					$currency= Projects::getCurrency($result['id']);
					if($budget >0)
					{
						$potential= ($budget-$total_incloff)/$budget;
					}
					else
					{
						$potential=0;
					}


				//	print_r($reasons); exit;
					if( empty( $reasons ) )
					{
					     $str.='Not Available';
					}
					else
					{

						foreach($reasons as $reason)
						{
							$str.= "- ".$reason['OFFSET_reason']."<br/>";
						}
						//$str= json_encode($reasons);
						
					}
					array_push($projects,array('id'=>$id,'customer_id'=>$customer_id, 'customer' => $customer_name,'project'=>$project, 'budget'=> $budget,'actuals'=> $actuals,'includingoffset'=>$total_incloff,
	 					'offset'=> $offset,'currency'=> $currency , 'requests'=> $offset_req,'reasons' =>$str,'potential'=>$potential));
				
				}



 			
 		}

   		
		echo json_encode(array(
    		'data'=>$projects,
			'html' => $this->renderPartial('_list_projectsBudget', array('projects' => $projects), true, false)
        ));
	}
	public function actionProjectSort()
	{	

			$select ="							
			SELECT t1.customer_id , t1.projectid ,t1.id_type , t1.pstatus, t1.currency , t1.TotalMDs , t2.ActualMDs  , (t1.TotalMDs - t2.ActualMDs) AS RemaingMDs  ,((t1.TotalMDs - t2.ActualMDs) /IFNULL(t1.TotalMDs,1))*100 AS Overrun , (t1.Netamount/IFNULL(t2.ActualMDs,1))AS ActualRate , t1.Netamount/t1.TotalMDs AS BudgetedRate FROM 
					(
						SELECT
						sum((select sum(ei.man_days) from eas_items ei where ei.id_ea=e.id))as TotalMDs,
						sum(e.netamountusd) as Netamount ,
					
						p.id  as projectid ,
						p.id_parent ,
						e.TM ,
						p.id_type ,
						p.status as pstatus,
						p.customer_id ,
						e.currency
						FROM  eas e , projects p
						WHERE  (p.id =e.id_project or p.id=e.id_parent_project) AND e.category<>'25' AND e.category<>'24'  AND e.category<>'454'   AND e.category<>'496' and e.status>='2'
						GROUP BY p.id 
					) as t1 ,

					(
						SELECT  sum(uti.amount)/8  as ActualMDs, p2.id as projectid, p2.id_parent FROM user_time uti , projects_tasks pt  , projects_phases pp , projects p ,projects p2
									WHERE uti.id_task=pt.id AND uti.default='0' AND pt.id_project_phase=pp.id AND pp.id_project=p.id AND (p2.id=p.id or p2.id=p.id_parent)
						GROUP BY p2.id
					)  as t2			
			";

			$where ="WHERE t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') ";
			$groupby= "GROUP BY  t1.projectid";
			$orderby=" ";

		if (isset($_POST['type']))
		{
			Yii::app()->session['type'] = $_POST['type'];
			//echo  $_POST['type']; exit;

		}
		
		if (isset($_POST['status']))
		{
			Yii::app()->session['status'] = $_POST['status'];
		}

		if (isset($_POST['order']))
		{
			Yii::app()->session['order'] = $_POST['order'];
		}

		if(isset(Yii::app()->session['status']) && Yii::app()->session['status']!="" && Yii::app()->session['status']!=" " && Yii::app()->session['status']!='100'){
			$where=$where." AND t1.pstatus=".Yii::app()->session['status']." ";	
					
		}else{
			Yii::app()->session['status']="" ;
			$where=$where." AND (t1.pstatus=0 or t1.pstatus=1 or t1.pstatus=2) ";

		}

		if(isset(Yii::app()->session['type']) && Yii::app()->session['type']!='100' && Yii::app()->session['type']!="" && Yii::app()->session['type']!=" "){
			$where=$where." AND t1.id_type=".Yii::app()->session['type']." ";
				
		}else{
			Yii::app()->session['type']="" ;
			$where=$where." AND (t1.id_type=26 or t1.id_type=27) ";

		}

		if(isset(Yii::app()->session['order']) && Yii::app()->session['order']!="" && Yii::app()->session['order']!=" " && Yii::app()->session['order']!='100'){
			$orderby=$orderby."  ORDER BY ".Yii::app()->session['order']." ";	
					
		}else{
			Yii::app()->session['order']="" ;
			$orderby=$orderby." ORDER BY t1.TotalMDs DESC ";

		}
			
		   $query=$select." ".$where." ".$groupby." ".$orderby ;   
		 $results=Yii::app($query)->db->createCommand($query)->queryAll(); 

		foreach ($results as $result) 
 		{ 			
 			$result['ActualMDs']=Projects::getActualMD($result['projectid']);
 			if ($result['ActualMDs']==0)
			{
				$result['ActualMDs']=1;
			}
			$result['TotalMDs']=Projects::getProjectTotalManDaysByProject($result['projectid']); 
			if ($result['TotalMDs']==0)
			{
				$result['TotalMDs']=1;
			}
			$result['RemaingMDs']=$result['TotalMDs']-$result['ActualMDs'];
	 		$netamount=Projects::getNetAmount($result['projectid']);

	 		$result['ActualRate']= $netamount/$result['ActualMDs'];
	 				 	//	echo $result['ActualRate'];exit;
	 		$result['Overrun']= (($result['TotalMDs']-$result['ActualMDs'])/$result['TotalMDs'])*100;
	 		
			$result['BudgetedRate']=Projects::getBudgetedRate($result['projectid']);

			
		}
		echo json_encode(array(
    		'data'=>$results,
			'html' => $this->renderPartial('_list_projects', array('projects' => $results), true, false)
        ));
	
	}
	
	
	public function actionBillabilityBarSort()
	{
		
		$sr = array();
		$data_chart = array();


		

		if (isset($_POST['val']))
		{
			Yii::app()->session['month']= $_POST['val'];
		}
		if(!isset(Yii::app()->session['month']) || empty(Yii::app()->session['month'])){
		  	
		  	 Yii::app()->session['month']=3;
		  
		  }

		switch (Yii::app()->session['month'])
			{
				case '1':
				
						$months[] = date('Y-m',strtotime('now'));
					
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 12 Months )";
					break;	
				
			}

			$resources="";

  		  if (isset($_POST['resc']))
   		 {	
    		  Yii::app()->session['resc'] = $_POST['resc'];			
   		   
		  }

		  if(!isset(Yii::app()->session['resc']) || empty(Yii::app()->session['resc'])){
		  	
		  	 Yii::app()->session['resc']=3;
		  
		  }

		  	switch (Yii::app()->session['resc'])
			{
				case '1':
				 $tech=" and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getTech() as $t){
					$tech.=" '".$t['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
				break;

				case '2':
				$ops="and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getOps() as $o){
					$ops.=" '".$o['id']."',";
				}
				$ops.=" 0 ) ";
				$resources=$ops;
				break;
				case '3':
				$resources=' ';
				break;

				case '4':
				 $techPS=" and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getPS() as $t){
					$techPS.=" '".$t['id']."',";
				}
				$techPS.=" 0 ) ";
				$resources=$techPS;
				break;

				case '5':
				 $techCS=" and uts.id_user in ("; 
			
				foreach(UserPersonalDetails::getCS() as $t){
					$techCS.=" '".$t['id']."',";
				}
				$techCS.=" 0 ) ";
				$resources=$techCS;
				break;


   			}		

		sort($months);
		foreach ($months as $month)
			{


		$values = Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
		 from (
		 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and uts.date like '".$month."%'  ".$resources." 
		 		union all
		 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and uts.date like '".$month."%'   ".$resources.") as r
			) as billable ,
		 	 (
		 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.date like '".$month."%'  ".$resources." 
		 	 		union all 
		 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  uts.date like '".$month."%'   ".$resources.") as r 
			) as total ")->queryScalar();
				/*	echo "select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
		 from (
		 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and uts.date like '".$month."%'  ".$resources." 
		 		union all
		 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and uts.date like '".$month."%'   ".$resources.") as r
			) as billable ,
		 	 (
		 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and uts.date like '".$month."%'  ".$resources." 
		 	 		union all 
		 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and uts.date like '".$month."%'   ".$resources.") as r 
			) as total ";*/
					
						$sr['billability']= (double)$values ;
						$sr['month'] = date('M-y', strtotime($month));
  					
					 array_push($data_chart,$sr);	
			 }  



		echo json_encode($data_chart);
	}
	
	public function actionSrBarSortClosed()
	{
		$sum=0;
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '3':
					for($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '1':
					for($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$values = array();
			$sr = array();
			$dataset = array();
			sort($months);
			foreach($months as $month)
			{
				$sr['PS'][$month] = 0;
				$sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,responsibility FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val)
				
				{	
					$id = $val['id'];
					if ($val['responsibility'] == 'CS')
					{
						$sr['CS'][$month]++;
					}else if ($val['responsibility'] == 'PS') {
						$sr['PS'][$month]++;
					}
				}
				if($sr != null)
		    	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
		    	}
			}
			
		}
		}
		else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();

		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '3':
					for($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '2':
					for($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$values = array();
			$sr = array();
			$dataset = array();
			sort($months);
			foreach($months as $month)
			{
				$sr['PS'][$month] = 0;
				$sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,responsibility FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val)
				
				{	
					$id = $val['id'];
					if ($val['responsibility'] == 'CS')
					{
						$sr['CS'][$month]++;
					}else if ($val['responsibility'] == 'PS') {
						$sr['PS'][$month]++;
					}
				}
				if($sr != null)
		    	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
		    	}
			}
		}

		}
		echo json_encode($dataset);
	}


		public function actionSrBarSortSystemShutdown()
	{
		$sum=0;
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '3':
					for($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '1':
					for($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			
			
			$dataset = array();
			foreach($months as $month)
			{
			
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes'  and date like '$month%' ")->queryScalar();
				
				if($values != null &&  $escalate !=null)
		    	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); 
		    	}
			}
			
		
		}
		}
		else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();

		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '3':
					for($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '2':
					for($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			
			
			$dataset = array();
			foreach($months as $month)
			{
			
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				
					if($values != null &&  $escalate !=null)
		    	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); 
		    	}
			}
		}

		}
		$dataset=array_reverse($dataset);

		echo json_encode($dataset);
	}

	public function actionSrBarSortSubmitted()
	{
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '3':
					for($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for($i = 2;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '1':
					for($i = 5;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
				for($i = 11;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$status_closed = 3;
			$status_confirm_closed = 5;
			$data_chart=array();
			foreach($months as $month)
			{
				$value= Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date like '$month%' ")->queryScalar();
				$closed=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryScalar();
				
				array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (int)$value, 'value2' => (int)$closed));
			}
		echo json_encode($data_chart);
		}	
	}

	public function actionUnsatBarSortCustomer()
	{
		$values = array();
      	$data_chart = array();
      	$top5=array();

	    $query="SELECT DISTINCT(id_customer) FROM `support_desk` where YEAR(rate_date)=YEAR(CURRENT_DATE()) AND `status`=5 and rate is not null and rate<>0";  
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
	  	//$total= Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where `status`=5 and rate is not null and rate not in (0,4,5)  ")->queryScalar();
  		//echo $total; exit;
	  	$avg_rate=0;
	    $customer_issues=0;
	    $customer_rate=0;
	    foreach ($values as $key => $value) 
	    {
	        
	        $customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where YEAR(rate_date)=YEAR(CURRENT_DATE()) AND id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate not in (0,3,4,5)  ")->queryScalar();
	 		if ($customer_issues != 0)
	 		{
	 			array_push($top5, array('id' => $value['id_customer'],'value'=>$customer_issues));
	 		}
	 	}
		usort($top5, Widgets::build_sorter('value'));
	 	$values= array_slice($top5, 0 ,5);
	 	$total = (double)array_sum(array_column($values,'value'));
	 	//print_r($values); 
	 	//echo $total;
	 	
	    foreach ($values as $key => $value) 
	    {
	    	$customer_name=Customers::getNameByID($value['id']);
	    	$customer_totalIssues=Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where  id_customer ='".$value['id']."' and `status`=5 and rate is not null and rate <>0  ")->queryScalar();

	        $customer_issues = $value['value'];
	       // echo $customer_issues;
	    	//exit;
			$avg_rate=($customer_issues/$total);
	        $percent = number_format(($avg_rate)*100,0);
	       // echo $percent; exit;
			array_push($data_chart,array('category' => $customer_name.' '.$customer_issues.' - ',
	           	  'value' => (double)$percent));
			// print_r($data_chart['category']);exit;
		}
		
		//usort($data_chart, Widgets::build_sorter('value'));
    	echo json_encode($data_chart);
	}

	public function actionSrBarSortCustomer()
	{
		$months=array();
		if (isset($_POST['val']))
		{ 
			Yii::app()->session['val'] = $_POST['val'];  
		}
		if (isset($_POST['slice']))
		{ 
			Yii::app()->session['slice'] = $_POST['slice'];  
		}

		switch (Yii::app()->session['val'])
		
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '3':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '4':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '1':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		

			$values = array();
			$sr = array();
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT distinct(id),id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();

				foreach($values as $val)
				{	
					if (!isset($sr[$val['id_customer']][$month]))
						$sr[$val['id_customer']][$month] = 1;
					else
						$sr[$val['id_customer']][$month] ++;
					
					if (!isset($sr[$val['id_customer']]['reopen']))
						$sr[$val['id_customer']]['reopen'] = $val['reopen']; 
					else 
						$sr[$val['id_customer']]['reopen'] += $val['reopen']; 

					$issues = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE id_customer= '".$val['id_customer']."' and date like '$month%'")->queryAll();
			//		print_r($issues);exit;
				}
			}
			$total=0;
			foreach($sr as $key=>$results)
			{
				$i = 0;
				$sum  = 0;
				foreach ($results as $key_re=>$val)
				{
					if($key_re != 'reopen'){

						$sum += $val;
						$total+=$sum;
					}
				}
			}
			$data_chart = array();
			foreach($sr as $key=>$results)
			{
				$i = 0;
				$sum  = 0;
				$sumtot  = 0;
				foreach ($results as $key_re=>$val)
				{
					if($key_re != 'reopen'){
						$sum += $val;
						$i++;
					}
					$sumtot += $val;
				}
				$percent = number_format(($sum/$total)*100,0);
			
				array_push($data_chart,array('category' => Customers::getNameById($key).' ('.$results['reopen'].' R)'.' Total nb of issues:'.$sumtot.', ',
            	  'value' => $sum));
			}
		
			usort($data_chart, Widgets::build_sorter('value'));
			switch (Yii::app()->session['slice']) 
			{
			 	case '100':
			 		break;

				case '20':
					$data_chart=array_slice($data_chart,0, 20);
			 		break;

			 	case '10':
					$data_chart=array_slice($data_chart,0, 10);
			 		break;	
			 	case '5':
					$data_chart=array_slice($data_chart,0, 5);
			 		break;		 	
			 	
			}
		echo json_encode($data_chart);
		
	}
	public function actionSrBarSortTopCustomer()
	{
		if(isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$values = array();
			$sr = array();
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['id_customer']]['val']))
						$sr[$val['id_customer']]['val'] = 1;
					else
						$sr[$val['id_customer']]['val'] ++;
					
					$sr[$val['id_customer']]['cust'] = $val['id_customer']; 
				
					if (!isset($sr[$val['id_customer']]['reopen']))
						$sr[$val['id_customer']]['reopen'] = $val['reopen']; 
					else 
						$sr[$val['id_customer']]['reopen'] += $val['reopen']; 
				}
			}
			
			uasort($sr, Widgets::build_sorter('val'));
			$data_chart = array();
			foreach(array_slice($sr, 0, 10) as $results)
			{
	    		array_push($data_chart,array('category' => Customers::getNameById($results['cust']).' ('.$results['reopen'].' R)',
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}
	
	public function actionSrBarSortPriorityCustomer()
	{
		if(isset($_POST['val'])){
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
    	
			$values = array();
			$sr = array();
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("select severity from support_desk WHERE date like '$month%' and id_customer=".$id_customer." ")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['severity']]['val']))
						$sr[$val['severity']]['val'] = 1;
					else
						$sr[$val['severity']]['val'] ++;
					
					$sr[$val['severity']]['cust'] = $val['severity']; 
				
				}
			}
			
			uasort($sr, Widgets::build_sorter('val'));
			$data_chart = array();
			foreach(array_slice($sr, 0, 10) as $results)
			{
	    		array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}
	
	public function actionSrOpenStatusCustomer()
	{
		
			
    	$id_user=Yii::app()->user->id;
    	if(!Yii::app()->user->isAdmin){
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
				}
    			$data_chart = array();
			$values = array();
		
				if(!Yii::app()->user->isAdmin){	
				$values = Yii::app()->db->createCommand("select count(1) as counts, case status when '0' then 'New' when '1' then 'In Progress' when '2' then 'Awaiting Customer' when '4' then 'Repoened' END as status from support_desk WHERE  id_customer=".$id_customer." and (status !=3 and status!=5) group by status order by status")->queryAll();
				}else{

						$values = Yii::app()->db->createCommand("select count(1) as counts, case status when '0' then 'New' when '1' then 'In Progress' when '2' then 'Awaiting Customer' when '4' then 'Repoened' END as status  from support_desk WHERE  (status !=3 and status!=5)  group by status order by status")->queryAll();	
				}
				foreach ($values as $val)
				{
					
	    		array_push($data_chart,array('category' => $val['status']."(".$val['counts'].")", 'value' => $val['counts']));
				
				}
			
		
		

    	echo json_encode($data_chart);
	}


	public function actionSrOpenSeverityCustomer()
	{
		if(isset($_POST['val'])){
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
    	
			$values = array();
			$sr = array();
			foreach($months as $month)
			{
				$values = Yii::app()->db->createCommand("select severity from support_desk WHERE date like '$month%' and id_customer=".$id_customer."  and (status !=3 and status!=5)")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['severity']]['val']))
						$sr[$val['severity']]['val'] = 1;
					else
						$sr[$val['severity']]['val'] ++;

					$sr[$val['severity']]['cust'] = $val['severity']; 
				
				}
			}
			
			uasort($sr, Widgets::build_sorter('val'));
			$data_chart = array();
			foreach(array_slice($sr, 0, 10) as $results)
			{
	    		array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}

	public function actionSubmittedCustomer()
	{
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '1':
					for ($i = 0;$i<5;$i++)
					{
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 5 Years )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++)
					{
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 3 Years )";
					break;
				case '3':
					for ($i = 0;$i<10;$i++)
					{
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 10 Years )";
					break;
				case '4':
					for ($i = 0;$i<1;$i++)
					{
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( This Year )";
					break;
			}
			
			$sr = array();
			sort($years);
			foreach ($years as $year)
			{
				$sr[$year][] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,id_customer FROM support_desk WHERE date like '$year%'")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$year][$val['id_customer']]))
						$sr[$year][$val['id_customer']] = 1;
					else
						$sr[$year][$val['id_customer']] ++;
				}
			}
			
			$data_chart = array();
			if ($sr != null)
			{
				foreach($sr as $key => $results)
				{
					$i = -1;
					$sum  = 0;
					if ($results != null)
					{
						foreach ($results as $val)
						{
							$sum += $val;
							$i++;
						}
					}
					array_push($data_chart,array('label' => $key."(".$i.')', 'value' => $sum));
				}
			}

			$chart = array(
					"xAxisName" => "Months",
					"yAxisName" => "SRs",
			);
			
		}
		echo json_encode($data_chart);
	}
		public function actionprojectBarSortAlerts()
	{
		
		
			$values = array();
			$sr = array();
			sort($months);
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%'")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			

		echo json_encode($data_chart);
	}


public function actionchangeSrAvgRec()
	{	$data_chart=array();
		    $results = Yii::app()->db->createCommand("SELECT SUM(t.avg) , t.id_user , count(t.id) as count , SUM(t.avg)/count(t.id) as response_time  from (select s.id ,TIMESTAMPDIFF(MINUTE,s.date,sdc.date)/1440 as avg, sdc.id_user from support_desk s  , support_desk_comments sdc , users u 
				where sdc.id_user=u.id and s.id =sdc.id_support_desk and s.date<sdc.date and s.date <> '0000-00-00 00:00:00' and sender='SNS' and s.`status` in ('3','5') and s.id_customer in (select id from customers where status=1) and (u.id in ( SELECT id_user FROM `user_groups` where id_group=9) and u.id not in(31,20,9))
				-- and YEAR(sdc.date)='2016' and YEAR(s.date)='2016' 
				and not exists ( select 1  from support_desk_comments sd where sd.sender='SNS' and sd.id_user<>sdc.id_user and sd.date<sdc.date and sdc.id_support_desk=sd.id_support_desk )
				 GROUP BY s.id
				order by 2 desc
					) t group by t.id_user")->queryAll();

	   // print_r($users);exit;
	    foreach ($results as $result) 
	    {
	    	$cname=Users::getNameById($result['id_user']);
	    	//echo $cname; exit;
	    	$avg= number_format((float)$result['response_time'], 2, '.', ''); 
	    	//echo $avg;exit;
	    	if ($cname !='' && $cname !='NULL' && $cname !=' ' && $avg >0)
			
			{
				array_push($data_chart,array('label' => $cname, 'value' =>(float)$avg));
	    	}
	    }



	    usort($data_chart, Widgets::build_sorter('value'));

	   // print_r($data_chart);exit;
    	echo json_encode($data_chart);
    	
    }





	public function actionSrBarSortReason()
	{
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$values = array();
			$sr = array();
			sort($months);
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%'")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
		}
	}
	else{
		$id_user=Yii::app()->user->id;
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();

		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$values = array();
			$sr = array();
			sort($months);
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and id_customer=".$id_customer." ")->queryAll();
				foreach ($values as $val)
				{	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();
			foreach ($sr as $key=>$results)
			{
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
		}		
	}
	
		echo json_encode($data_chart);
	}


	public function actionmostActiveProject()
	{
		$order="order by man_days desc limit 5";
		$o=0;
		if (isset($_POST['month'])){ Yii::app()->session['month'] = $_POST['month'];  }

		if (isset($_POST['top'])){  Yii::app()->session['top'] = $_POST['top']; }

			switch (Yii::app()->session['month'])
			{
				case '3':
					
						$month = date('Y-m-01',strtotime('now'));
					
					$time = "( Current Month )";
					break;
				case '2':
					
						$month = date('Y-m-01',strtotime('now - 3 month'));
					
					$time = "( Last 3 Months )";
					break;
				case '1':
					
						$month= date('Y-m-01',strtotime('now - 6 month'));
					
					$time = "( Last 6 Months )";
					break;
				
			}

			switch (Yii::app()->session['top'])
			{
				case '3':
					$order="order by man_days desc limit 15";	$o=1;
					break;
				case '2':
					$order="order by man_days desc limit 10";						
					break;
				case '1':
					$order="order by man_days desc limit 5";						
					break;
				
			}
			$current_date = date('Y-m-d',strtotime('now'));
	

			$values = array();
			$sr = array();
		 	 $select ="SELECT pp.id_project,round(SUM(uts.amount)/8,3) as man_days FROM user_time uts  LEFT JOIN projects_tasks pt ON uts.id_task=pt.id LEFT JOIN projects_phases pp  ON pt.id_project_phase=pp.id ";
			 $where=" where uts.`default`='0'  and uts.date between '$month' and '$current_date' ";
			 $group="group by pp.id_project ";
			

				$values = Yii::app()->db->createCommand($select." ".$where." ".$group." ".$order."" )->queryAll();

				foreach ($values as $val)
				{	
					
						$sr[$val['id_project']]= $val['man_days'] ;
					}
				
			
			
			$data_chart = array();
			asort($sr);	

			foreach ($sr as $key=>$results)
			{   if($o==1){ 
	$ProjectName = "<span style='font-size:11px'>".substr(Projects::getNameByid($key),0,30)."</span>"; }else{
	$ProjectName =substr(Projects::getNameByid($key),0,30);
	 }
				array_push($data_chart,array('label' =>$ProjectName,
            	  'value' => $results));
			}
	  
    	
    		echo json_encode($data_chart);

	
}

	public function actionrescourceBarBillable()
	{
		$order="order by nbperc desc limit 5";
		$month= date('Y-m-01',strtotime('now - 6 month'));
		$o=0;
		
		if (isset($_POST['valbill'])){ Yii::app()->session['monthbill'] = $_POST['valbill'];  }

		if (isset($_POST['topbill'])){  Yii::app()->session['topbill'] = $_POST['topbill']; }

			switch (Yii::app()->session['monthbill'])
			{
				case '3':
					
						$month = date('Y-m-01',strtotime('now'));
					
					$time = "( Current Month )";
					break;
				case '2':
					
						$month = date('Y-m-01',strtotime('now - 3 month'));
					
					$time = "( Last 3 Months )";
					break;
				case '1':
					
						$month= date('Y-m-01',strtotime('now - 6 month'));
					
					$time = "( Last 6 Months )";
					break;
				
			}

			switch (Yii::app()->session['topbill'])
			{
				case '3':
					$order="order by nbperc desc limit 15";	$o=1;
					break;
				case '2':
					$order="order by nbperc desc limit 10";						
					break;
				case '1':
					$order="order by nbperc desc limit 5";						
					break;
				
			}
			$current_date = date('Y-m-d',strtotime('now'));
	
	


			$values = array();
			$sr = array();
			
			
				$values = Yii::app()->db->createCommand("
select t1.id_user , TRUNCATE(((nonbillable*100)/total) ,2) as nbperc from 
(
select r.id_user , sum(r.amount) as nonbillable from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and  uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='Yes' and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and  uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='Yes' and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user ) t1
,
(
select r.id_user , sum(r.amount) as total from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and  uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable IN ('Yes','No') and  uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and  uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable IN ('Yes','No') and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user ".$order." ")->queryAll();

				foreach ($values as $val)
				{	
					
						$sr[$val['id_user']]= (int)$val['nbperc']  ;
				
				}



			
			$data_chart = array();
			 asort($sr);	
			foreach ($sr as $key=>$results)
			{ 
				 if($o==1){ $name ="<span style='font-size:11px'>".Users::getNameByid($key)."</span>"; }
				 else{ $name=Users::getNameByid($key);  }
				array_push($data_chart,array('label' => $name,
            	  'value' => $results));
			}
	  
    	
    		echo json_encode($data_chart);
	
}
	

	public function actionrescourceBarNonBillable()
	{
		$order="order by nbperc desc limit 5";	
		$month= date('Y-m-01',strtotime('now - 6 month'));
		$o=0;
		if (isset($_POST['valnobill'])){ Yii::app()->session['monthnobill'] = $_POST['valnobill'];  }

		if (isset($_POST['topnobill'])){  Yii::app()->session['topnobill'] = $_POST['topnobill']; }

			switch (Yii::app()->session['monthnobill'])
			{
				case '3':
					
						$month = date('Y-m-01',strtotime('now'));
					
					$time = "( Current Month )";
					break;
				case '2':
					
						$month = date('Y-m-01',strtotime('now - 3 month'));
					
					$time = "( Last 3 Months )";
					break;
				case '1':
					
						$month= date('Y-m-01',strtotime('now - 6 month'));
					
					$time = "( Last 6 Months )";
					break;
				
			}

			switch (Yii::app()->session['topnobill'])
			{
				case '3':
					$order="order by nbperc desc limit 15";	$o=1;
					break;
				case '2':
					$order="order by nbperc desc limit 10";						
					break;
				case '1':
					$order="order by nbperc desc limit 5";						
					break;
				
			}
			$current_date = date('Y-m-d',strtotime('now'));
	

			$values = array();
			$sr = array();
			
			
				$values = Yii::app()->db->createCommand("
select t1.id_user , TRUNCATE(((nonbillable*100)/total) ,2) as nbperc from 
(
select r.id_user , sum(r.amount) as nonbillable from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where  u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable='No' and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where   u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='No' and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user ) t1
,
(
select r.id_user , sum(r.amount) as total from (

select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where  u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable  in ('No','Yes') and uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where  u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable in ('No','Yes') and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 

) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user ".$order." ")->queryAll();

				foreach ($values as $val)
				{	
					
						$sr[$val['id_user']]= (int)$val['nbperc'] ;
				
				}



			
			$data_chart = array();
			 asort($sr);	
			foreach ($sr as $key=>$results)
			{ 
				 if($o==1){ $name ="<span style='font-size:11px'>".Users::getNameByid($key)."</span>"; }
				 else{ $name=Users::getNameByid($key);  }
				array_push($data_chart,array('label' => $name,
            	  'value' => $results));
			}
    	
    		echo json_encode($data_chart);
	
}
	
	public function actionSrBarSortResource()
	{    
		$data_chart = array();
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '1':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					$month = date('Y-m', strtotime('now - 11 month'));
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		
			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$srs = Yii::app()->db->createCommand("SELECT * FROM (SELECT sd.id as id, sdc.id as scid, sd.assigned_to, sd.reopen, sdc.date, DATE_FORMAT(sdc.date,'%Y-%m') ym FROM support_desk sd left join support_desk_comments sdc on sd.id=sdc.id_support_desk WHERE ((sd.status = {$status_closed} or sd.status = {$status_confirm_closed}) and (sdc.status = {$status_closed} or sdc.status = {$status_confirm_closed})) and sd.assigned_to IS NOT NULL and sd.assigned_to<>'0' order by sdc.id desc) as tmp_table group by id;")->queryAll();
			$result = array();
			
			foreach ($srs as $sr) {
				$month = $sr['ym'];
				if (in_array($month, $months)) {
					$names = explode(" ", trim(preg_replace('/\s+/', ' ', Users::getUsername($sr['assigned_to']))), 2);
					// set category name on the chart
					if (!isset($result[$sr['assigned_to']]['category'])) {
						count($names) == 1 ? 
						$result[$sr['assigned_to']]['category'] = $names[0][0] :
						$result[$sr['assigned_to']]['category'] = $names[0][0].$names[1][0];
					}

					$result[$sr['assigned_to']]['fullname'] =Users::getUsername($sr['assigned_to']);

					// set number of srs for the resource
					if (!isset($result[$sr['assigned_to']]['count']))
						$result[$sr['assigned_to']]['count'] = 1;
					else
						$result[$sr['assigned_to']]['count']++;

				
					// number of reopened srs
					if (!isset($result[$sr['assigned_to']]['reopen']))
						$result[$sr['assigned_to']]['reopen'] = $sr['reopen'];
					else
						$result[$sr['assigned_to']]['reopen'] += $sr['reopen'];
				}
			}
			
			foreach ($result as $key => $res) {
					
						array_push($data_chart,array('category' => $res['category'].' ('.$res['reopen'].' R)',
				'value' => $res['count'] , 'label'=>$res['fullname'] ));
							
			}
		}
		echo json_encode($data_chart);
	}
	
public function actionProjectAlerts()
	{    
		

    		$values = array();
			$sr = array();
			
				$values = Yii::app()->db->createCommand("select DISTINCT id_project from projects_alerts order by id_project")->queryAll();
				foreach ($values as $val)
				{	
					
						$sr[$val['id_project']]= ProjectsAlerts::getAlertsCount($val['id_project']);
				
				}

							
			$data_chart = array();
			
			arsort($sr);	
			
			$i=0;	
				foreach ($sr as $key=>$results)
					{ if($i<10){
						array_push($data_chart,array('category' => Projects::getNameByID($key),
            			  'value' => $results));
						$i++;
            			   }
						
					}
				
			

	  
    	echo json_encode($data_chart);
	}

	public function actionSrTime()
	{
		$sr = array(); 
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '2':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		}
		
		$status_close  = SupportDesk::STATUS_CLOSED;
		$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
		foreach ($months as $month)
		{
			$values = Yii::app()->db->createCommand("SELECT id,date FROM support_desk WHERE date like '$month%' and (status = $status_close OR status=$status_confirm_closed) ")->queryAll();
			foreach($values as $val)
		    {
		    	$id = $val['id'];
		    	$end_date = Yii::app()->db->createCommand("SELECT date FROM support_desk_comments WHERE id_support_desk = {$id} ORDER BY date DESC LIMIT 1")->queryScalar();
		    	$seconds = strtotime($end_date) - strtotime($val['date']);
				$hours   = floor(($seconds) / 3600);
		    		if ($hours < 8)
		    	{
		    		if (!isset($sr['less 8']))
						$sr['less 8'] = 1;
					else
						$sr['less 8'] ++;
		    	}
		    	else if($hours >= 8 && $hours < 16)
				{
		    		if (!isset($sr['8']))
						$sr['8'] = 1;
					else
						$sr['8'] ++;
		    	} 		    	
		    	else if($hours >= 16 && $hours < 24)
			    {
			    	if(!isset($sr['16']))
						$sr['16'] = 1;
					else
						$sr['16'] ++;
			    }
			    else if($hours >= 24 && $hours<48)
				{
				    if(!isset($sr['24']))
						$sr['24'] = 1;
					else
						$sr['24'] ++;
				    }
				   else if($hours >= 48 && $hours<72)
				    {
				    	if(!isset($sr['48']))
							$sr['48'] = 1;
						else
							$sr['48'] ++;
				    	}
				    else if($hours >= 72 && $hours<96)
				    {
				    	if(!isset($sr['72']))
							$sr['72'] = 1;
						else
							$sr['72'] ++;
				    	}
				    	else if($hours >= 96 && $hours<192)
				    	{
				    		if(!isset($sr['96']))
								$sr['96'] = 1;
							else
								$sr['96'] ++;
				    	}
				    	 	else if($hours>=192)
					    {	
					    	if(!isset($sr['192']))
								$sr['192'] = 1;
							else
								$sr['192'] ++;
					    }
		    }	
		}

		$data_chart = array();
		ksort($sr);
		foreach ($sr as $key=>$result)
	    {
	    	 array_push($data_chart,array('category' => $result." ".WidgetTime::getLabel($key),
            	  'value' => $result));
	    }
	    usort($data_chart, Widgets::build_sorter('value'));
        echo json_encode($data_chart);
	}
	public function actionSrSupport()
	{
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '1':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$values = array();
			$x_axis = array();
			$status_close = SupportDesk::STATUS_CLOSED;
			$id_default_support = WidgetSupport::DEFAULT_SUPPORT;
			$ids_users = array();
			$dataset = array();
			sort($months);
			foreach ($months as $month)
			{
				$values = Yii::app()->db->createCommand("SELECT ut.id_user FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
				foreach($values as $val)
			    {
			    	array_push($ids_users,$val['id_user']);
			    }
			}
			sort($months);
	   		foreach ($months as $month)
			{
				$sr = array();
				$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
				foreach ($values as $val)
			    {
			    	if (!isset($sr[$val['id_user']]))
						$sr[$val['id_user']] = $val['amount'];
					else
						$sr[$val['id_user']]+= $val['amount'];
			    }
			    foreach ($ids_users as $id_user){
			    	if (!isset($sr[$id_user]))
			    		$sr[$id_user] = 0;
			    }
			    $data = array();
			    $data['month'] = date('M-Y', strtotime($month));
				foreach ($sr as $key=>$result)
			    {
			    	 $data[Users::getUsername($key)] = $result;
			    }
			    array_push($dataset,$data);

			}
		}
		echo json_encode($dataset);
	}


	


	public function actionSrSubmittedResolved()
	{
		if (isset($_POST['val']))
		{
			switch ($_POST['val'])
			{
				case '1':
					for ($i = 0;$i<1;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++)
					{
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

			$values = array();
			$x_axis = array();
			$status_close = SupportDesk::STATUS_CLOSED;
			$id_default_support = WidgetSupport::DEFAULT_SUPPORT;
			$ids_users = array();
			$dataset = array();
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		$sr = array();

		foreach ($months as $month)
		{
				$submitted="Submitted";
				$resolved="Resolved";
		    	array_push($ids_users,$submitted);
		    	array_push($ids_users,$resolved);
		}
		 $dataset = array();
   		foreach ($months as $k => $month)
		{
			$sr = array();
			$values = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where id_customer=".$id_customer." and date like '$month%'")->queryScalar();
			$num_closed=0;
		    	if (!isset($sr[$submitted][$month]))
					$sr[$submitted][$month] = $values;
				else
					$sr[$submitted][$month]+= $values;


					$values_closed =Yii::app()->db->createCommand("select sc.id_support_desk, max(sc.date) from support_desk sd, support_desk_comments sc where sc.id_support_desk=sd.id and (sc.`status`=3 or sc.`status`=5) and id_customer=".$id_customer." and sc.is_admin=1  
						group by sc.id_support_desk 
						having max(sc.date) Like '$month%'")->queryAll();
		  			foreach ($values_closed as $val_closed) {
		  				$num_closed++;
		  			}
		  		if (!isset($sr[$resolved][$month]))
					$sr[$resolved][$month] = $num_closed;
				else
					$sr[$resolved][$month]+= $num_closed;

		    foreach ($ids_users as $id_user){
		    	if (!isset($sr[$id_user][$month]))
		    		$sr[$id_user][$month] = 0;
		    }
		    $data = array();
		    $data['month'] = date('M-Y', strtotime($month));
			foreach ($sr as $key=>$result)
		    {
		    	 $data[Users::$key] = $result;
		    }
			    array_push($dataset,$data);
			}
		}
		echo json_encode($dataset);
	}
	



	/**
     * Sends the json array to generate the chart on filter
     * @param string $year
     * @author Romeo Onisim
     */
	public static function actionCountryRevenues()
	{
		$year = $_POST['val'];
		switch ($year)
		{
			case 'current':
				$data = date('Y');
				$data_chart = WidgetCountryRevenues::getCountryRevenues($data);
				break;
			case 'last':
				$data = date('Y') - 1;
				$data_chart = WidgetCountryRevenues::getCountryRevenues($data);
				break;
			case 'last3':
				$data = date('Y') ;
				$data2 = date('Y') - 1; 
				$data3 = date('Y') - 2;
				$data_chart = WidgetCountryRevenues::getCountryRevenues($data,$data2,$data3);
				break;
			default:
				$data = date('Y');
				break;
		}
		echo json_encode($data_chart);
	}
	
	/**
	 * Sends the json array to generate the chart on filter
	 * @param string $year
	 * @author Romeo Onisim
	 */
	public static function actionEaTypesRevenues()
	{
		$year = $_POST['val'];
		switch ($year)
		{
			case 'current':
				$data = date('Y');
				$data_chart = WidgetEaTypeRevenues::getEaRevenues($data);
				break;
			case 'last':
				$data = date('Y') - 1;
				$data_chart = WidgetEaTypeRevenues::getEaRevenues($data);
				break;
			case 'last3':
				$data = date('Y') ;
				$data2 = date('Y') - 1; 
				$data3 = date('Y') - 2;
				$data_chart = WidgetEaTypeRevenues::getEaRevenues($data,$data2,$data3);
				break;
			default:
				$data = date('Y');
				break;
		}
		echo json_encode($data_chart);
	}
	
	/**
	 * Sends the json array to generate the chart on filter
	 * @param string $year
	 * @author Romeo Onisim
	 */
	public static function actionSoldByRevenues()
	{
		$year = $_POST['val'];
		switch ($year)
		{
			case 'current':
				$data = date('Y');
				$data_chart = WidgetSoldByRevenues::getSoldByRevenues($data);
				break;
			case 'last':
				$data = date('Y') - 1;
				$data_chart = WidgetSoldByRevenues::getSoldByRevenues($data);
				break;
			case 'last3':
				$data = date('Y') ;
				$data2 = date('Y') - 1; 
				$data3 = date('Y') - 2;
				$data_chart = WidgetSoldByRevenues::getSoldByRevenues($data,$data2,$data3);
				break;
			default:
				$data = date('Y');
				break;
		}
		$chart = array('pieRadius'=>'100');
		echo json_encode($data_chart);
	}
	public static function actionGetProjects()
	{
		$id_customer = (int)$_POST['id'];
		$id_type = (int)$_POST['type'];
		if($id_type == 100)
			$results = Yii::app()->db->createCommand("SELECT * FROM projects WHERE customer_id = '$id_customer'")->queryAll();
 		else
 			$results = Yii::app()->db->createCommand("SELECT * FROM projects WHERE customer_id = '$id_customer' AND id_type =$id_type")->queryAll();
		$data=array();
 		$sum_project = array();
 		foreach ($results as $result)
 		{	
 			$id_project = $result['id'];
 			$date = date('Y',strtotime('now'));
   			$eas = Yii::app()->db->createCommand("SELECT id from eas where id_project = $id_project and approved like '$date%'")->queryScalar();
   			if ($eas != false)
   			{
	   			$eas_model = Eas::model()->findByPk($eas);
	   			$sum_project[$result['id']]['totalMDs'] = $eas_model->getTotalManDays();
	   			$sum_project[$result['id']]['actualMDs'] = $eas_model->getActualMD();
	   			$sum_project[$result['id']]['name'] = $result['name'];
	   			$sum_project[$result['id']]['customer_id'] = $result['customer_id'];//echo($id_project);
	   			$sum_project[$result['id']]['milestone'] = Milestones::getMilestoneDescription(Projects::getCurrentMilestone($id_project));
   			}
 		}
		echo json_encode($sum_project);
	}

}