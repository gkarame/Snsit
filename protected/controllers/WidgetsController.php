<?php
class WidgetsController extends Controller{
	public function filters(){
		return array(
				'accessControl',
				'postOnly + delete + deleteVisa',
		);
	}
	public function accessRules(){
		return array(
				array('allow', 
						'actions'=>array('index','setOrder', 'delete','easBarSort','customerPieSort','TopCustomerPieSort','OverRunSort','ProjectRunOut','OldestInvoices','projectSort','billabilityBarSort','MonthlyAvgSort','monthlyPaymentSort','srBarSortClosed',
						'srBarSortSubmitted','SrVsRsrBarSort','ShowGraph','ShowTable','ShowRangeInvoices','ShowRangeSrs','ShowTable2','MonthlyPaymentByResourceSort', 'MonthlyAgeAvg','ShowGraphMonthBillability','customerSatisfaction','SrsAgingUser','supportPerf','showTrendDown','TestActuals','RsrBarSortPerMon','RsrBarSortCustomer','UnsatBarSortCustomer','srBarSortCustomer','srBarSortSystemShutdown','srBarSortTopCustomer','SrBarSortPriorityCustomer','SrOpenStatusCustomer','SrOpenSeverityCustomer','SubmittedCustomer','getyeardiscount','changeRsrAvgRec','changeSrAvgRec','srBarSortReason','projectBarSortAlerts','srBarSortResource',
						'srTime','MaintProfit','srSupport','PendingPaymentsByMonthSort','CountryPayments','srRate','DSOIndex','getaginginvoices', 'getWidgetsOff', 'CountryRevenues', 'EaTypesRevenues', 'SoldByRevenues','getProjects','rescourceBarNonBillable','rescourceBarBillable','mostActiveProject','ProjectAlerts'),
						'expression'=>'!$user->isGuest',
				),
				array('deny', 
						'users'=>array('*'),
				),
		);
	}	
	public function init(){
		parent::init();
	}	
	public function actionIndex(){
		if(Yii::app()->user->isAdmin){
		if (isset($_GET['Widgets']['id'])){
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
		}else{
		if (isset($_GET['Widgets']['id'])){
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
	public function actionGetWidgetsOff(){
		$data = Widgets::getWidgetsOff((int)$_POST['id'], true);
		$options = '<option value="">Choose widget</option>';
		foreach ($data as $key => $value){
			$options .= "<option value='{$key}'>{$value}</option>";
		}
		echo json_encode(array('options' => $options));
		exit;
	}
	public function actionSetOrder(){
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['id'])){
			$ids = explode(',', $_POST['id']);
			if (count($ids) > 1) {
				$i = 0;
				foreach($ids as $v) {
					$i++;
					$wid = UserWidgets::model()->findByPk($v);
					if ($wid !== null && Yii::app()->user->id == $wid->user_id){
						$wid->order = $i;
						$wid->save();
					}
				}
			}
			echo json_encode(array('status' => 1));
		}
		}else{
			if (isset($_POST['id'])){
			$ids = explode(',', $_POST['id']);
			if (count($ids) > 1) {
				$i = 0;
				foreach($ids as $v) {
					$i++;
					$wid = CustomerWidgets::model()->findByPk($v);
					if ($wid !== null && Yii::app()->user->id == $wid->user_id){
						$wid->order = $i;
						$wid->save();
					}
				}
			}
			echo json_encode(array('status' => 1));
		}
		}
	}
	public function actionDelete(){
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['id'])){
			$wid = UserWidgets::model()->findByPk((int)$_POST['id']);
			if ($wid !== null && Yii::app()->user->id == $wid->user_id){
				echo json_encode(array('status' => (int)$wid->delete()));
			}
		}
		}else{
		if (isset($_POST['id'])){
			$wid = CustomerWidgets::model()->findByPk((int)$_POST['id']);
			if ($wid !== null && Yii::app()->user->id == $wid->user_id){
				echo json_encode(array('status' => (int)$wid->delete()));
			}
		}
		}
	}	
	public function actionEasBarSort(){
		if (isset($_POST['year'])){
			$time = $_POST['year'];
    		$date = array();
    		$date1 = array();
			$now =   date('Y',strtotime("now"));
			$data_chart = array();			
			for($i=0;$i<12;$i++) {
				$inter = date('Y-01',strtotime($now .' -'.$time.' year'));
				$date[] = date('Y-m',strtotime($inter .' + '.$i.' month'));
			}
			$year=(date('Y',strtotime('now')));
			$jan=$year.'-01';
			$Feb=$year.'-02';
			$mar=$year.'-03';
			if (in_array($jan, $date) && in_array($mar, $date) && !in_array($Feb, $date)) {
			   $date[] = $Feb;
			}
			sort($date);
			foreach ($date as $data){
				$month = date('M',strtotime($data));
				$year_data = date('Y',strtotime($data));
				$val = WidgetEas::getAmountMonth($data);

								 array_push($data_chart,array('label' => $month."-".$year_data,
	            	  'value' => $val));
			}
    	}else{
	    	$date = array();
			$month =  date('Y-m');
			$data_chart = array();			
			for ($i=11;$i>=0;$i--) {
				$date[] = date('Y-m',strtotime($month . ' - '.$i.' month'));
			}
			$year=(date('Y',strtotime('now')));
			$jan=$year.'-01';
			$Feb=$year.'-02';
			$mar=$year.'-03';
			if (in_array($jan, $date) && in_array($mar, $date) && !in_array($Feb, $date)) {
			   $date[] = $Feb;
			}
			sort($months);
			foreach ($date as $data){
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
	public function actionTopCustomerPieSort(){
		if (isset($_POST['top'])){
			Yii::app()->session['top'] = $_POST['top'];
		}
		$data_chart = array();
 		$rtrnRes = array();
 			$select ="							
			select id_customer,customers.name as  CustName, sum(net_amount) as am,currency
			from receivables join customers on customers.id = id_customer
			where id_customer not in (239,323) and ((partner = 77 and receivables.status<>'Paid') or (partner != 77 and (partner_status <>'Paid' or partner_status is null)) and partner != 554 )
			GROUP BY id_customer, currency
			order by am desc 
			";
		$results=Yii::app()->db->createCommand($select)->queryAll();		 		 
		foreach ($results as $result){ 	
 			$key = $result['id_customer'];
 			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
					$rate = CurrencyRate::getCurrencyRate($result['currency']);
					if (isset($rate['rate'])){
						$realamount = $result['am'] * $rate['rate'];
					}
				}else{
					$realamount = $result['am'];
				} 
 			if(!isset($rtrnRes[$key])){
 			$rtrnRes[$key]['Name'] = $result['CustName'];// $result['FirstName'].' '.$result['LastName'];
			$rtrnRes[$key]['AmountInDollars'] = (double)round($realamount,2);
			}else{
				$rtrnRes[$key]['AmountInDollars'] += (double)round($realamount,2);
			}
		}
		usort($rtrnRes, Widgets::build_sorter('AmountInDollars'));
		foreach(array_slice($rtrnRes, 0,Yii::app()->session['top']) as $res){
			array_push($data_chart,array('Customer' => $res['Name'],
            	  'value' => $res['AmountInDollars']));
		}		
 		echo json_encode($data_chart);
	}
	public function actionCustomerPieSort(){
		if (!isset(Yii::app()->session['top']))
			Yii::app()->session['top'] = '5';			
		if (!isset(Yii::app()->session['year']))
			Yii::app()->session['year'] = date('Y');				
		if (isset($_POST['top'])){
			Yii::app()->session['top'] = $_POST['top'];
		}		
		if (isset($_POST['year'])){
			Yii::app()->session['year'] = $_POST['year'];
		}		
		$date = array();
		$month =  date('Y-m');		
		for ($i=11;$i>=0;$i--) {
			$date[] = date('Y-m',strtotime($month . ' - '.$i.' month'));
		}		
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $date) && in_array($mar, $date) && !in_array($Feb, $date)) {
		   $date[] = $Feb;
		}
		sort($date);
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
	public function actionShowGraph(){
		if (isset($_POST['id'])){
			$id = $_POST['id'];
			$val= WidgetCustomerProfile::CharChart($id);
			print_r($val); exit;
			echo json_encode($val);
		}		
	}	
	public function actionShowTable2(){
		$time=null;
		if (isset($_POST['cust'])){
			$CustName=preg_replace("/[^A-Za-z ]/", '', $_POST['cust']);
			$pieces = explode("(", $_POST['cust']);
			$x= $pieces[1]; 
		}else{
			$x=1;
		}
		if (isset($_POST['perc'])){
			$perc= $_POST['perc'];
		}
		switch ($x)
		{
				case '2':					
					$time=1;
					break;
				case '3':
					$time=3;
					break;
				case '4':
					$time=6;
					break;
				case '1':
					$time=12;
					break;
		}
		$val= WidgetReasons::CharChart($CustName, $perc, $time);
		print_r($val); exit;
		echo json_encode($val);
	}	
	public function actionShowRangeSrs(){
		if (isset($_POST['range2'])){
			$range=$_POST['range2'];
			$where='';
			$list='<div class="closepopupwidget" style=\' margin-top:15px;    margin-left: 423px;\' onclick="parentNode.classList.add(\'hidden\');"> </div>';		
		      $pos = strpos($range, '>');
		      if ($pos === false) {
		        $limits=explode('-', $range);
		        $limit1= trim($limits[0]);
		        $limit2= trim($limits[1]);
		        if ($limit1 == 1) {
 					$where.= "and DATEDIFF(NOW(),sd.date) >=0 and DATEDIFF(NOW(),sd.date) <=".$limit2." ";
		        }else{
		        $where.= "and DATEDIFF(NOW(),sd.date) >".$limit1." and DATEDIFF(NOW(),sd.date) <=".$limit2." ";}
		      } else {
		        $where.= "and DATEDIFF(NOW(),sd.date) >90 ";
		      }		       
		      if (isset($_POST['userAging']) && $_POST['userAging']!='' && $_POST['userAging'] !=null && $_POST['userAging'] !=1 ){
		      	 $id=Users::getIdByName($_POST['userAging']);
      			 $where.=" and assigned_to=".$id." ";  
		      } 
		      $values=Yii::app()->db->createCommand("select sd.sd_no, sd.id_customer, sd.status, sd.short_description, sd.assigned_to
        		from support_desk sd where   sd.status not in (3,5)  ".$where." ORDER BY sd_no desc")->queryAll();
		       $size= sizeof($values);
			$list.="<div style='font-family:Calibri;font-size: 17px;padding-bottom:15px;margin-top:-40px;'><b>".$range." Days, ".$size."  SR(s):</b><br/></div>";
			$list.="<div style='width:460px;height: 290px;  overflow: auto;margin-left:-30px;'><table style='font-family:Calibri;font-size: 13.5px;>";
		    $list.="<tr style='text-align:center; vertical-align:bottom; font-weight:bold'><td width='20' style='height: 16px; ;border-left:1px solid ; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>SR#</td><td width='70' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Customer</td><td width='20' style='height: 16px; ;border-left:1px solid ; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Status</td><td width='20' style='height: 16px; ;border-left:1px solid ; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Assigned To</td><td width='600' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;' >Description</td> </tr>";	
		    foreach ($values as $value) {
			   	$list.="<tr style='text-align:center; vertical-align:bottom;'><td width='20' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-left:1px solid  ;border-right:1px solid  ; text-align:left;'><a href='".Yii::app()->createAbsoluteUrl('supportDesk/update', array('id'=>$value['sd_no']))."'>".$value['sd_no']."</a></td><td width='70' style='height: 16px; border-top:1.5px solid;border-bottom:1.5px solid ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>".Customers::getNameById($value['id_customer'])."</td><td width='20' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-left:1px solid  ;border-right:1px solid  ; text-align:left;'>".SupportDesk::getStatusLabel($value['status'])."</td><td width='20' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-left:1px solid  ;border-right:1px solid  ; text-align:left;'>".Users::getNameById($value['assigned_to'])."</td><td width='600' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid  ;border-right:1px solid ;padding-left:10px;padding-right:10px;'>".$value['short_description']."</td> </tr>";	
		    }
		    $list.='</table></div></div> ';
    		echo json_encode(array ('srs' =>$list));
		}
	}
	public function actionShowRangeInvoices(){
		if (isset($_POST['range'])){
			$range=$_POST['range']; $where='';
			$list='<div class="closepopupwidget" style=\' margin-top:15px;\' onclick="parentNode.classList.add(\'hidden\');"> </div>';		
		      $pos = strpos($range, '>');
		      if ($pos === false) {
		        $limits=explode('-', $range);
		        $limit1= trim($limits[0]);
		        $limit2= trim($limits[1]);
		        $where.= "and DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) >".$limit1." and DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) <=".$limit2." )";
		      }  else {
		        $where.= "and DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) >365 )";
		      }
		    $values=Yii::app()->db->createCommand("select sum(r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by c.date DESC  limit 1)) as total, id_customer
		        from receivables r
		        where r.id_customer not in (239,323) and (((r.partner = 77 and r.status <> 'Paid') or (r.partner != 77 and (r.partner_status <>'Paid' or  r.partner_status is null) )) and r.partner !=554 
		        ".$where."  GROUP BY id_customer order by total DESC")->queryAll();
		    $list.="<div style='font-family:Calibri;font-size: 17px;padding-bottom:15px;margin-top:-40px;'><b>".$range." Days Invoices:</b><br/></div>";
			$list.="<div style='width:350px;height: 290px;  overflow: auto;margin-left:-30px;'><table style='font-family:Calibri;font-size: 13.5px;>";
		    $list.="<tr style='text-align:center; vertical-align:bottom; font-weight:bold'><td width='650' style='height: 16px; ;border-left:1px solid ; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Customer</td><td width='20' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>Total ($)</td><td width='20' style='height: 16px; border-top:1.5px solid  ;border-bottom:1.5px solid  ;border-right:1px solid  ;padding-left:10px;' >Average Age</td> </tr>";	
		    foreach ($values as $value) {
		    	  $avgAge= Receivables::getavgageNotPaidAll($value['id_customer']);
			   	$list.="<tr style='text-align:center; vertical-align:bottom;'><td width='650' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid ;border-left:1px solid  ;border-right:1px solid  ; text-align:left;'>".Customers::getNameById($value['id_customer'])."</td><td width='20' style='height: 16px; border-top:1.5px solid;border-bottom:1.5px solid ;border-right:1px solid  ;padding-left:10px;padding-right:10px;'>".Utils::formatNumber($value['total'],2)."</td><td width='20' style='height: 16px; border-top:1.5px solid ;border-bottom:1.5px solid  ;border-right:1px solid ;padding-left:10px;padding-right:10px;'>". Utils::formatNumber($avgAge,2)."</td> </tr>";	
		    }
		    $list.='</table></div></div> ';
    		echo json_encode(array ('servs' =>$list));
		}
	}
	public function actionShowTable(){
		$time=6;
		if (isset($_POST['cust'])){
			$CustName=preg_replace("/[^A-Za-z &]/", '', $_POST['cust']);
			$arr = explode("-", $_POST['cust'], 3); $time = $arr[1];
		}
		if (isset($_POST['userunsatpop'])){
			$user= $_POST['userunsatpop'];
		}
		if (isset($_POST['perc'])){
			$perc= $_POST['perc'];
		}
		$val= WidgetReasons::CharChart($CustName, $perc, $time, $user);
		print_r($val); exit;
		echo json_encode($val);
	}
	public function actionShowGraphMonthBillability(){
		if (isset($_POST['id'])){
			$id = $_POST['id'];
		}
		if(isset(Yii::app()->session['resc']) || !empty(Yii::app()->session['resc'])){		  	
		  	 $resc=Yii::app()->session['resc'];		  
		}else {
			$resc=3;
		}
		$val= WidgetMonthBillability::CharChart($id, $resc);
		print_r($val); exit;
		echo json_encode($val);
	}
	public function actionshowTrendDown(){
		$values = array();
		$axis = array();
        $query="SELECT DISTINCT(id_customer) FROM `support_desk` where `status`=5 and rate is not null and rate<>0";  
        $values=Yii::app($query)->db->createCommand($query)->queryAll();
        foreach ($values as $key => $value) {
          $customer_name=Customers::getNameByID($value['id_customer']);
			$Months= Yii::app()->db->createCommand("SELECT count(1), MONTH(rate_date), SUM(rate), rate_date FROM `support_desk` where id_customer ='".$value['id_customer']."' and rate_date >= '2016-01-01 00:00:00' 
      		and  `status`=5 and rate is not null and rate <>0 
      		GROUP BY YEAR(rate_date), MONTH(rate_date) ORDER BY rate_date")->queryAll();
			if(sizeof($Months)>1){
				$compareRate = array_slice($Months, -2);
				$thismonthAvg = $compareRate[1]['SUM(rate)']/ $compareRate[1]['count(1)'];
				$lastMonthAvg = $compareRate[0]['SUM(rate)']/ $compareRate[0]['count(1)'];
			  	if ($thismonthAvg<$lastMonthAvg){
			      	$customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryScalar();
			        if ($customer_issues != 0) {
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
	public function actionSrsAgingUser(){     
	  $where="";
      if (isset($_POST['userAging']) && $_POST['userAging']!='' && $_POST['userAging'] !=null && $_POST['userAging'] !=1 ){
			$id=Users::getIdByName($_POST['userAging']);
      		$flag=1; 
			$where=" and assigned_to=".$id." ";        
		}else{
			$id=0;
			 
		}        
      $values = array();
      $results = array();
      $data_chart = array();      
     $months = array(0,7,15,30,60,90);
for($i = 0;$i<6;$i++){
  $j = $i+1;
  if($i == 0){
	 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where  sd.status not in (3,5)  and DATEDIFF(NOW(),sd.date) >= $months[$i] and DATEDIFF(NOW(),sd.date) <= $months[$j] ".$where."
		")->queryAll();
 
  } else if($i < 5 && $i != 0){
	 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where sd.status not in (3,5)  and DATEDIFF(NOW(),sd.date) > $months[$i] and DATEDIFF(NOW(),sd.date) <= $months[$j] ".$where."
		")->queryAll(); 
  } else { 
 $values = Yii::app()->db->createCommand("select count(1) as total from support_desk sd
        where sd.status not in (3,5)  and  DATEDIFF(NOW(),sd.date) >$months[$i] ".$where." ")->queryAll();
}
	 foreach ($values as $key => $value){
      	 if ($i == 0 ){
        $key = '1-'.$months[$j];
    }else  if ($i < 5){
        $key = $months[$i].'-'.$months[$j];
		}else{
		  $key = '>'.$months[$i];
		}      	
        if(!isset($results[$key])){
          $results[$key]['Aging'] = $key;
          $results[$key]['total'] = $value['total'];
        }             
        }
      }
	  foreach ($results as $res) {
          array_push($data_chart,array('label' => $res['Aging'],
                    'value' => (double)round( $res['total'],2)));
        }
				echo json_encode($data_chart);      
    }
	public function actionsupportPerf(){
      $values = array();
      $axis = array();
      $query='';
      $id=0;
    if((!isset(Yii::app()->session['timev']) || empty(Yii::app()->session['timev']) )&& !isset($_POST['timeval'])){
		Yii::app()->session['timev']= 5;		  
	}else if(isset($_POST['timeval'])){
		Yii::app()->session['timev']= $_POST['timeval'];
	} 
	switch (Yii::app()->session['timev']){
		case '1':
			   $qTime= 'YEAR(s.rate_date)=(YEAR(CURRENT_DATE())-1)';
			break;
		case '5':
				$qTime= '((s.rate_date>= (CURRENT_DATE() - interval 11 MONTH)) or (MONTH(s.rate_date)= MONTH((CURRENT_DATE() - interval 11 MONTH)) AND YEAR(s.rate_date)= YEAR((CURRENT_DATE() - interval 11 MONTH)))) ';
			 
			break;
		case '3':
				$qTime= 'YEAR(s.rate_date)=(YEAR(CURRENT_DATE())-2)';					
			break;
		case '4':
			 $qTime= 'YEAR(s.rate_date)=(YEAR(CURRENT_DATE())-3)';
		    break;
	}
	if (isset($_POST['userPerf']) && $_POST['userPerf']!='' && $_POST['userPerf'] !=null && $_POST['userPerf'] !=1 ){
			$id=Users::getIdByName($_POST['userPerf']);
      		$flag=1;
			$query="SELECT count(1), MONTH(s.rate_date), s.rate_date as rate_date
			FROM `support_desk`  s
			where  ".$qTime." and s.status=5 and s.rate is not null and s.rate not in (0,1,2,3) 
			and (SELECT sd.id_user from support_desk_comments sd where sd.status=3 and s.id=sd.id_support_desk ORDER BY sd.date DESC limit 1)='".$id."'
			GROUP BY MONTH(s.rate_date) 
			ORDER BY s.rate_date";         
		}else{
			$id=0;
			$query="SELECT count(1), MONTH(s.rate_date), s.rate_date as rate_date FROM `support_desk` s where  ".$qTime."  and s.status=5 and s.rate is not null and s.rate not in (0,1,2,3) 
              GROUP BY MONTH(s.rate_date) ORDER BY s.rate_date";  
       
      	}  
       $values=Yii::app()->db->createCommand($query)->queryAll();
       if (!empty($values)){
      foreach ($values as $key => $value)  {
      	if ($id == 0){
        $sum = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` s where YEAR(s.rate_date)=YEAR('".$value['rate_date']."') AND MONTH(s.rate_date) = MONTH('".$value['rate_date']."') and s.status=5 and s.rate is not null")->queryScalar();
       }else {
		$sum = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` s where YEAR(s.rate_date)=YEAR('".$value['rate_date']."') AND MONTH(s.rate_date) = MONTH('".$value['rate_date']."') and s.status=5 and s.rate is not null and (SELECT id_user from support_desk_comments where status=3 and s.id=id_support_desk ORDER BY date DESC limit 1)='".$id."'")->queryScalar();       	
       }
       if ($sum==0)  {
             $avg=0;
          }else  {
              $avg= ($value['count(1)']*100/$sum);
          }         
          $month= date("M",strtotime($value['rate_date']));        
          $axis[$key]['older1']= (double)round($avg,2);
          $axis[$key]['total1']= $month;
        	$axis[$key]['tag1']= $month ;
     	  	$axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$month."</b></span> \n Rate:".(double)round($avg,2)."%";
      }
  	}else{
  		for ($i = 1;$i<=12;$i++){
					$months[] = date('Y-m',strtotime(date('2018-'.$i)));
		}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$dataset = array();
		sort($months);
		foreach ($months as $key => $month){
			$month = date('M', strtotime($month));
	  		$avg=0;
	  		$axis[$key]['older1']= (double)round($avg,2);
	        $axis[$key]['total1']= $month;
	        $axis[$key]['tag1']= $month ;
	       	$axis[$key]['tag1']= "<span style='font-size:15px;'><b>".$month."</b></span> \n Rate:".(double)round($avg,2)."%";
       }
  	}
      echo json_encode($axis);
    }
	public function actioncustomerSatisfaction(){
      $values = array();
      $axis = array();
      if (isset($_POST['cust'])){
			$query="SELECT id as id_customer FROM `customers` where name like '%".($_POST['cust'])."%'";			
		}else{
			$query="SELECT DISTINCT(id_customer) FROM `support_desk` where `status`=5 and rate is not null and rate<>0";
      	}        
      $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
      $avg_rate=0;
      $customer_issues=0;
      $customer_rate=0;
      foreach ($values as $key => $value) {
          $customer_name=Customers::getNameByID($value['id_customer']);
          $customer_data = Yii::app()->db->createCommand("SELECT count(1) as tot, sum(rate) as rate FROM `support_desk` where  id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate<>0")->queryAll();
          $customer_issues = array_sum(array_column($customer_data, 'tot'));
         if ($customer_issues != 0){
            $customer_rate=  array_sum(array_column($customer_data, 'rate'));
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
	public function actionCountryPayments(){
		$amount = 0;
		$month = 1;
		if (isset($_POST['month'])){
			Yii::app()->session['month'] = $_POST['month'];
			$month = $_POST['month'];
		}
		if (isset($_POST['amount'])){
			Yii::app()->session['amount'] = $_POST['amount'];
			$amount=$_POST['amount'];
		}
		switch ($month)
    	{
    		case '1':
    			$data = date('Y-m',strtotime('now - 3 months'));    		
    		break;
    		case '2':
    			$data = date('Y-m',strtotime('now - 6 months'));    			
    		break;
    		case '3':
    			$data = date('Y-m',strtotime('now - 9 months'));
    		break;
        case '4':
          $data = date('Y-m',strtotime('now - 1 year'));
        break;
    		default:
    			$data = date('Y-m',strtotime('now - 3 months'));
    		break;
    	}
    	switch ($amount)
    	{
    		case '1':
    			$bgamount = 0;    			
    		break;
    		case '2':
    			$bgamount = 10000;    			
    		break;
    		case '3':
    			$bgamount = 25000;
    		break;
        case '4':
          $bgamount = 50000;
        break;
    		default:
    			$bgamount = 0;
    		break;
    	}
    	$data_chart = WidgetPendingPaymentsByCountry::getPaymentsByCountry($data,$bgamount);
    	echo json_encode($data_chart);
	}
	public function actionMaintProfit()
	{
		$colorfilter=100;
		$year= date('Y');
		$values = array(); $axis = array(); 

		if (isset($_POST['yearprofit'])){
			Yii::app()->session['yearprofit'] = $_POST['yearprofit'];
		} 
		if (isset($_POST['colorfilterMaint'])){
			$colorfilter=$_POST['colorfilterMaint'];
		} 
		if (isset($_POST['custProfit'])){
			$custProfit=$_POST['custProfit'];
		} 
		 
		 
		if(isset(Yii::app()->session['yearprofit']) && Yii::app()->session['yearprofit']!="" && Yii::app()->session['yearprofit']!=" " && Yii::app()->session['yearprofit']!='100'){
	    	if(Yii::app()->session['yearprofit']=="1"){ $year= $year-1; } else if(Yii::app()->session['yearprofit']=="2"){ $year= $year-2; }
	    }    
		$i=0;
		if(!empty($custProfit))
		{
			$values=Yii::app()->db->createCommand("select distinct(id) as customer from customers where id not in (177, 101) and name like '%".$custProfit."%' and id in (select customer from maintenance)")->queryAll();
		}else{
			$values=Yii::app()->db->createCommand("select distinct(customer) from maintenance where customer not in (177, 101)  ")->queryAll();
		}
	    foreach ($values as $customer)
	    {
	    	$customer_values=Maintenance:: getTotalNetAmountYearPerCustomer($customer['customer'] ,$year);
	    	$Yrevenues= $customer_values[0];
			$cost= $customer_values[1];
	    	if($Yrevenues != 'false' || $cost> 0)
		    {	
		    	$srs= $customer_values[2];
	    		$hours= $customer_values[3];
				$Xprofit= $Yrevenues - $cost;
				$enddate=$customer_values[4];
				if($Xprofit !=0)
				{
					if ($colorfilter == 100 || ($colorfilter == 80 && $Xprofit<0) || ($colorfilter == 60 && $Xprofit==0) || ($colorfilter == 40 && $Xprofit>10000) || ($colorfilter == 60 && $Xprofit>0 && $Xprofit<10000)) {
				        $axis[$i]['tag1']= "<span style='font-size:15px;'><b>".Customers::getNameById($customer['customer'])."</b>
				        </span>Revenues:".Utils::formatNumber($Yrevenues,2)." $\n Cost: ".Utils::formatNumber($cost,2)." $\n # of SRs: ".Utils::formatNumber($srs)."\n # of Hours: ".Utils::formatNumber($hours)."\n Profit: ".Utils::formatNumber($Xprofit, 2)." $\n ".$enddate;
				        $axis[$i]['older1']= (double)round($Xprofit,2);
				      //  $axis[$key]['perc1']= (int)$value['status']; 
				        $axis[$i]['total1']= (double)round($Yrevenues,2); 
				        $i++; 
			    	}
				}
			}
	    }
	    echo json_encode($axis);
	}
	public function actionTestActuals(){
		$values = array();
		$axis = array();
		$budget=0;
		$colorfilter=100;
		if (isset($_POST['type'])){
			Yii::app()->session['type'] = $_POST['type'];
		}		
		if (isset($_POST['status'])){
			Yii::app()->session['status'] = $_POST['status'];
		}
		if (isset($_POST['budget'])){
			Yii::app()->session['budget'] = $_POST['budget'];
			$budget=$_POST['budget'];		
		}
		if (isset($_POST['colorfilter'])){
			$colorfilter=$_POST['colorfilter'];
		} 
     $select = " SELECT p.id , p.status from projects p , eas e ";        
      $where ="WHERE (p.id=e.id_project or p.id=e.id_parent_project) and p.id not in (204,484, 222) and (e.TM<>'1' or e.TM is null) AND p.id_type in ('26' , '27') and e.old='No'  and e.status>=2 ";
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
    if(isset(Yii::app()->session['budget']) && Yii::app()->session['budget']!='100' && Yii::app()->session['budget']!="" && Yii::app()->session['budget']!=" ")
    {
      	$budget= Yii::app()->session['budget'];
     	 if($budget=="lt25"){
     	 	      $where=$where." AND (SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id )) < 25000";
     	 }
     	 if($budget=="lt75"){
     	 	      $where=$where." AND (SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id )) > 25000 AND (SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id )) <75000";
     	 }
     	 if($budget=="mt75") {
     	 	      $where=$where." AND (SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id )) >75000";
     	 }        
    }else{     
         $where=$where." AND (SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id )) > 0";
    }
    if($colorfilter !=100) {
     	$lowAverage= SystemParameters::getCost() * 8;;
		$highAverage= 800;
		$budgeted= Projects::getBudgetedMD(204);
     	if ($colorfilter == 80){
			$where=$where." and ((SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id ))/(SELECT  SUM(u.amount)/8 FROM user_time u
			WHERE (u.status=0 or u.status=1) and id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase
					LEFT JOIN projects pi ON pi.id = pp.id_project WHERE (pi.id = p.id or pi.id_parent=p.id) )))< ".$lowAverage." ";
     
     	}
     	if ($colorfilter == 60){
			$where=$where." and (((SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id ))/(SELECT  SUM(u.amount)/8 FROM user_time u
			WHERE (u.status=0 or u.status=1) and id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase
					LEFT JOIN projects pi ON pi.id = pp.id_project WHERE (pi.id = p.id or pi.id_parent=p.id) )))> ".$lowAverage." and ((SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id ))/(SELECT  SUM(u.amount)/8 FROM user_time u
			WHERE (u.status=0 or u.status=1) and id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase
					LEFT JOIN projects pi ON pi.id = pp.id_project WHERE (pi.id = p.id or pi.id_parent=p.id) )))< ".$highAverage." )";
     
     	}
     	if ($colorfilter == 40)	{
			$where=$where." and ((SELECT sum(e.netamountusd) FROM  eas e  where (e.id_project=p.id or e.id_parent_project=p.id ))/(SELECT  SUM(u.amount)/8 FROM user_time u
			WHERE (u.status=0 or u.status=1) and id_task IN (
			SELECT pt.id FROM projects_tasks pt LEFT JOIN projects_phases pp ON pp.id = pt.id_project_phase
					LEFT JOIN projects pi ON pi.id = pp.id_project WHERE (pi.id = p.id or pi.id_parent=p.id) )))> ".$highAverage." ";     
     	}
     }
     $query=$select." ".$where." ".$orderby ; 
     $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
    foreach ($values as $key => $value) {
      if(Projects::getBudgetedMD($value['id'])<='1'){ $BudgetedMDs='1'; }else{  $BudgetedMDs=Projects::getBudgetedMD($value['id']); };
     
 		$ActualMDs= Projects::getActualMDPerParent($value['id']);
        if($ActualMDs <='1'){ $ActualMDsRound='1'; }else{  $ActualMDsRound= $ActualMDs ;}

      $TotNetAamountUSD=Projects::getNetAmountApproved($value['id']);    
      $RemainingMDS=$BudgetedMDs - $ActualMDs;
            $ActualRate=$TotNetAamountUSD/$ActualMDsRound;     
		$axis[$key]['tag1']= "<span style='font-size:15px;'><b>".Projects::getNameById($value['id'])."</b></span> \n Budgeted Amnt:".Utils::formatNumber($TotNetAamountUSD,2)."$ \n Budgeted MDs: ".Utils::formatNumber($BudgetedMDs)." \n Actual MDs: ".Utils::formatNumber($ActualMDs)." \n Remaining MDs: ".Utils::formatNumber($RemainingMDS)." \n  Budgeted Rate: ".Utils::formatNumber($TotNetAamountUSD/$BudgetedMDs,2)." $ \n  Actual Rate: ".Utils::formatNumber($ActualRate,2)." $";
	    $axis[$key]['older1']= (double)round($ActualRate,2);
	    $axis[$key]['perc1']= (int)$value['status'];
	    $axis[$key]['total1']= (double)round($TotNetAamountUSD,2);        
    }
    echo json_encode($axis); 
    }
	public function actionOverRunSort(){
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
					)  as t2	";
			$where ="WHERE t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) AND t1.id_type not in ('24' , '25' , '28') ";
			$groupby= "GROUP BY  t1.projectid";
			$orderby=" ";
		if (isset($_POST['type'])){
			Yii::app()->session['type'] = $_POST['type'];
		}		
		if (isset($_POST['status'])){
			Yii::app()->session['status'] = $_POST['status'];
		}
		if (isset($_POST['order'])){
			Yii::app()->session['order'] = $_POST['order'];
		}
		if (isset($_POST['state'])){
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
		foreach ($results as $result){
 			$tm=Projects::getTMstatus($result['projectid']); 			
 			if ($tm == 0 ){
 				$result['ActualMDs']=Projects::getActualMD($result['projectid']); 
 				if ($result['ActualMDs']==0){
					$result['ActualMDs']=1;
				}
 				$result['TotalMDs']=Projects::getProjectTotalManDaysByProject($result['projectid']); 
 				if ($result['TotalMDs']==0){
					$result['TotalMDs']=1;
				}
				$result['RemaingMDs']=$result['TotalMDs']-$result['ActualMDs'];
		 		$netamount=Projects::getNetAmount($result['projectid']);
		 		$result['ActualRate']= $netamount/$result['ActualMDs'];
		 		$result['Overrun']= (($result['TotalMDs']-$result['ActualMDs'])/$result['TotalMDs'])*100;		 		
				$result['BudgetedRate']=Projects::getBudgetedRate($result['projectid']);
			}
		} 
		if(isset(Yii::app()->session['state']) && Yii::app()->session['state']!="" && Yii::app()->session['state']!=" "){
			if (Yii::app()->session['state'] =='0') {
				foreach ($results as $key => $result){
					if ($result['Overrun']>0){
						$axis[$key]=$result;
					}
				}
				$results=$axis;
			}else if (Yii::app()->session['state'] =='1') {
			 	foreach ($results as $key => $result){
					if ($result['Overrun']<0){
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
	public function actionOldestInvoices(){
		if (isset($_POST['topcust'])){
			Yii::app()->session['topcustomers'] = $_POST['topcust'];
			$topcust= Yii::app()->session['topcustomers'];
			$results=Yii::app()->db->createCommand("
 			select id_customer,customers.name as CustName, final_invoice_number,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age, net_amount, printed_date,receivables.id_assigned,users.firstname as FN,users.lastname as LN,partner,receivables.status,receivables.partner_status,currency
			from receivables join customers on customers.id = id_customer  join  users on users.id = receivables.id_assigned
			where id_customer not in (239,323) and ((partner = 77 and receivables.status Not in ('Paid')) or (partner != 77 and partner != 554 and (partner_status Not in ('Paid') or partner_status is null)) and partner != 554 )
			order by age desc
 			")->queryAll();
 		$i=0;
 		$customers=array();
 		$rtrnRes = array();
 		foreach ($results as $result){
 			if (!empty($result['printed_date']) && $result['printed_date'] != '0000-00-00' && $i<$topcust){
 			$rtrnRes['customer_name']=$result['CustName'];
 			$rtrnRes['Invoice_num']=$result['final_invoice_number'];
 			if ($result['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
				$rate = CurrencyRate::getCurrencyRate($result['currency']);
				if (isset($rate['rate'])){
					$rtrnRes['amount'] = $result['net_amount'] * $rate['rate'];
				}
			}else{
				$rtrnRes['amount'] = $result['net_amount'];
			} 
			$rtrnRes['age']=$result['age'];
			$rtrnRes['resource'] = $result['FN'].' '.$result['LN'];
			array_push($customers,array('customer_name'=>$rtrnRes['customer_name'], 'Invoice_num' => $rtrnRes['Invoice_num'],'amount'=>$rtrnRes['amount'], 'Age'=> $rtrnRes['age'],'Resource'=> $rtrnRes['resource']));			
			$i = $i+1;
 		}
	}	
		}
				echo json_encode(array(
    		'data'=>$customers,
    		'html' => $this->renderPartial('_list_oldestInvoices', array('customers' => $customers), true, false)));
	}
	public function actionProjectRunOut(){	
		$requiredtype=" ";
		if (isset($_POST['type1'])){
			Yii::app()->session['type1'] = $_POST['type1'];
			$requiredtype= Yii::app()->session['type1'];
		}
		if ($requiredtype!=" " && $requiredtype !=100){
			$results=Yii::app()->db->createCommand("SELECT id, name, customer_id  FROM projects where status=1 and id_type =".$requiredtype." and  ((id_parent in (select ea_number from eas where TM=0)) OR id_parent is null) ")->queryAll();
		}else {
			$results=Yii::app()->db->createCommand("SELECT id, name, customer_id  FROM projects where status=1 and  ((id_parent in (select ea_number from eas where TM=0)) OR id_parent is null)")->queryAll();
		}
 		$projects=array();
 		foreach ($results as $result){ 
				$id=$result['id'];
	 			$customer_id=$result['customer_id'];
	 			$project=$result['name'];
				$customer_name=Customers::getNameByID($result['customer_id']);
				$budget= Projects::getBudgetedMD($result['id']);
				$total_incloff= Projects::getIncludingOffsetMD($result['id']);
				if ($budget < $total_incloff){
					$str=' ';
					$actuals=Projects::getActualMD($result['id']);
					$offset= Projects::getTotalOffsetMD($result['id']);
					$offset_req= Projects::getTotalOffset($result['id']);
					$reasons= Projects::getreasons($result['id']);
					$currency= Projects::getCurrency($result['id']);
					if($budget >0){
						$potential= ($budget-$total_incloff)/$budget;
					}else{
						$potential=0;
					}
					if( empty( $reasons ) ){
					     $str.='Not Available';
					}else{
						foreach($reasons as $reason)
						{
							$str.= "- ".$reason['OFFSET_reason']."<br/>";
						}
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
	public function actionProjectSort(){
			$select ="	SELECT t1.customer_id , t1.projectid ,t1.id_type , t1.pstatus, t1.currency , t1.TotalMDs , t2.ActualMDs  , (t1.TotalMDs - t2.ActualMDs) AS RemaingMDs  ,((t1.TotalMDs - t2.ActualMDs) /IFNULL(t1.TotalMDs,1))*100 AS Overrun , (t1.Netamount/IFNULL(t2.ActualMDs,1))AS ActualRate , t1.Netamount/t1.TotalMDs AS BudgetedRate FROM 
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
					)  as t2	";
			$where ="WHERE t1.id_parent is null AND  t2.id_parent is null AND t1.projectid =t2.projectid  AND (t1.TM<>'1' or t1.TM is null) ";
			$groupby= "GROUP BY  t1.projectid";
			$orderby=" ";
		if (isset($_POST['type'])){
			Yii::app()->session['type'] = $_POST['type'];
		}		
		if (isset($_POST['status'])){
			Yii::app()->session['status'] = $_POST['status'];
		}
		if (isset($_POST['state'])){
			Yii::app()->session['state'] = $_POST['state'];
		}
		if (isset($_POST['order'])){
			Yii::app()->session['order'] = $_POST['order'];
		}
		if (isset($_POST['orderover'])){
			Yii::app()->session['orderover'] = $_POST['orderover'];
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
			if(isset(Yii::app()->session['orderover']) && Yii::app()->session['orderover']!="" && Yii::app()->session['orderover']!=" " && Yii::app()->session['order']!='100'){
				$orderby=$orderby."  ORDER BY ".Yii::app()->session['order'].", ".Yii::app()->session['orderover']." ";	
			}else{
				$orderby=$orderby."  ORDER BY ".Yii::app()->session['order']." ";	
			}					
		}else{
						if(isset(Yii::app()->session['orderover']) && Yii::app()->session['orderover']!="" && Yii::app()->session['orderover']!=" " && Yii::app()->session['order']!='100'){
			Yii::app()->session['order']="" ;
			$orderby=$orderby." ORDER BY  ".Yii::app()->session['orderover'].",t1.TotalMDs DESC ";	
		}else{
			Yii::app()->session['order']="" ;
						Yii::app()->session['orderover']="" ;

			$orderby=$orderby." ORDER BY t1.TotalMDs DESC ";
		}
		}		
		 $query=$select." ".$where." ".$groupby." ".$orderby ;   
		 $results=Yii::app($query)->db->createCommand($query)->queryAll(); 
		 $axis= array();
		foreach ($results as $result){ 			
 			$result['ActualMDs']=Projects::getActualMD($result['projectid']);
 			if ($result['ActualMDs']==0){
				$result['ActualMDs']=1;
			}
			$result['TotalMDs']=Projects::getProjectTotalManDaysByProject($result['projectid']); 
			if ($result['TotalMDs']==0){
				$result['TotalMDs']=1;
			}
			$result['RemaingMDs']=$result['TotalMDs']-$result['ActualMDs'];
	 		$netamount=Projects::getNetAmount($result['projectid']);
	 		$result['ActualRate']= $netamount/$result['ActualMDs'];
	 		$result['Overrun']= (($result['TotalMDs']-$result['ActualMDs'])/$result['TotalMDs'])*100;	 		
			$result['BudgetedRate']=Projects::getBudgetedRate($result['projectid']);			
		}
		if(isset(Yii::app()->session['state']) && Yii::app()->session['state']!="" && Yii::app()->session['state']!=" "){
			if (Yii::app()->session['state'] =='0') {
				foreach ($results as $key => $result){
					if ($result['Overrun']>0){
						$axis[$key]=$result;
					}
				}
				$results=$axis;
			}else if (Yii::app()->session['state'] =='1') {
			 	foreach ($results as $key => $result){
					if ($result['Overrun']<0){
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
	public function actionMonthlyAgeAvg(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					$yr = (intval(date('Y',strtotime('now -1 year'))));
				for ($i = 1;$i<=12;$i++){
					$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
				}
				$time = "( Last Year )";
					break;
			}
			$currentm= date('Y-m',strtotime('now'));
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$dataset = array();
		sort($months);
		foreach ($months as $k => $month){
			$sr = array();				
			    $data = array();
			    $data['month'] = date('M-Y', strtotime($month));
			    $lastday=date('Y-m-t', strtotime($month));
				$starter=date('Y-m-01', strtotime($month));
				$bound=date('Y-m-01', strtotime('2016-10'));
			if ($currentm == $month){
$sr = Yii::app()->db->createCommand(" select DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age,
				IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 365, '>365', 
					IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 180,'180 - 365',
						IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 120,'120 - 180',
							IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 90,'90 - 120', 
								IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 60,'60 - 90', '0 - 60')
							)
						)
					)
				) as textdays,
			case when t.currency=9 
					THEN t.net_amount 
					else t.net_amount*(select c.rate from currency_rate c where c.currency=t.currency  order by date DESC  limit 1) end as net_amount
					from receivables t
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$lastday."' and t.id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (partner_status Not in ('Paid','Cancelled') or partner_status is null or partner_status='' or partner_status=' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$lastday."' and paid_date<>'0000-00-00'))
					and partner != 554  ")->queryAll();
			}else{
				$sr = Yii::app()->db->createCommand(" select DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age,
				IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 365, '>365', 
					IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 180,'180 - 365',
						IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 120,'120 - 180',
							IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 90,'90 - 120', 
								IF (DATEDIFF('".$lastday."', LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) > 60,'60 - 90', '0 - 60')
							)
						)
					)
				) as textdays,
			case when t.currency=9 
					THEN t.net_amount 
					else t.net_amount*(select c.rate from currency_rate c where c.currency=t.currency  order by date DESC  limit 1) end as net_amount
					from receivables t
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$lastday."' and t.id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (partner_status Not in ('Paid','Cancelled') or partner_status is null or partner_status='' or partner_status=' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$lastday."' and paid_date<>'0000-00-00'))
					and partner != 554  ")->queryAll();
				}				
				$totyear=0;$tot360=0; $tot180=0; $tot120=0; $tot90=0; $tot60=0;				
				foreach ($sr as $key => $value) {					
					if ($value['textdays'] == '>365'){
						$totyear= $totyear + $value['net_amount'];
					} else if ($value['textdays'] == '180 - 365'){
						$tot360= $tot360 + $value['net_amount'];
					} else if ($value['textdays'] == '120 - 180'){
						$tot180= $tot180 + $value['net_amount'];
					} else if ($value['textdays'] == '90 - 120'){
						$tot120= $tot120 + $value['net_amount'];
					} else if ($value['textdays'] == '60 - 90'){
						$tot90= $tot90 + $value['net_amount'];
					} else  {
						$tot60= $tot60 + $value['net_amount'];
					} 
				}				
				$data['>365'] = (double)round($totyear,2);	
				$data['180 - 365'] = (double)round($tot360,2);
				$data['120 - 180'] = (double)round($tot180,2);
				$data['90 - 120'] = (double)round($tot120,2);
				$data['60 - 90'] = (double)round($tot90,2);
				$data['0 - 60'] = (double)round($tot60,2);
			    array_push($dataset,$data);
		}
		echo json_encode($dataset);
		}
	}
	public function actionMonthlyPaymentByResourceSort(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					$yr = (intval(date('Y',strtotime('now -1 year'))));
				for ($i = 1;$i<=12;$i++){
					$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
				}
				$time = "( Last Year )";
					break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$dataset = array();
		sort($months);
		foreach ($months as $k => $month){
			$sr = array();				
			    $data = array();
			    $data['month'] = date('M-Y', strtotime($month));
			    $lastday=date('Y-m-t', strtotime($month));
				$starter=date('Y-m-01', strtotime($month));
				$bound=date('Y-m-01', strtotime('2016-10'));
				$sr=Receivables::gettotalNetResPerMonth(date('m', strtotime($month)), $lastday);
				if ($starter>=$bound){
				    foreach ($sr as $key=>$result){ 
				    	if($key == 0)
				    		$data['Not Assigned'] = $result;
				    	else
				    		$data[Users::getUsername($key)] = (double)round($result,2);	
				    }
				}else{
					foreach ($sr as $key=>$result) { 
				    	if($key == 0)
				    		$data['Not Assigned'] = 0;
				    	else
				    		$data[Users::getUsername($key)] = 0;	
				    }

				}
			    array_push($dataset,$data);
		}
		echo json_encode($dataset);
		}
	}
	public function actionMonthlyPaymentSort(){
		if(isset($_POST['val'])){
			$sr = array();
			$data_chart = array();
			Yii::app()->session['ypr']= $_POST['val'];
			switch (Yii::app()->session['ypr']){
				case '1':
				$currmonth = (intval(date('m',strtotime('now'))));
				for ($i = $currmonth-1;$i>-1;$i--){
					$months[] = date('Y-m',strtotime('now - '.$i.' month'));
				}
				break;
				case '5':				
				for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}				
				break;
				case '2':
				$yr = (intval(date('Y',strtotime('now -1 year'))));
				for ($i = 1;$i<=12;$i++){

					$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
				}
				break;
				case '3':
				/*$yr = (intval(date('Y',strtotime('now -2 years'))));
				for ($i =1;$i<=12;$i++){
					$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
				}*/
				for ($i = 0;$i<24;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}	
				break;
				case '4':
				/*$yr = (intval(date('Y',strtotime('now -3 years'))));
				for ($i =1;$i<=12;$i++){
					$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
				}*/
				for ($i = 0;$i<36;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
				break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
		foreach ($months as $month){
				$m=date('Y-m-t', strtotime($month));
				$starter=date('Y-m-01', strtotime($month));
				$bound=date('Y-m-01', strtotime('2016-10'));
				$sr['Payment'] = 0;
				if ($starter>=$bound){
					$values = Yii::app()->db->createCommand("
					select sum(net_amount) as am,currency
					from receivables 
					where id_customer not in (239,323) and ((partner = 77 and status='Paid') or (partner != 77 and partner !=554 and partner_status ='Paid')) and paid_date like '$month%'
					group by currency
					 ")->queryAll();					
					foreach($values as $value){								
						if($value['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
							$rate = CurrencyRate::getCurrencyRate($value['currency']);
							if (isset($rate['rate'])){
								$realamount = $value['am'] * $rate['rate'];
							}
						}else{
							$realamount = $value['am'];
						}
						$sr['Payment'] += (double)round($realamount,2);
					}
				}else{
						$sr['Payment'] = 0;
				}
				$sr['month'] = date('M-y', strtotime($month));
				$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];
				array_push($data_chart,$sr);	
			}  
    		echo json_encode($data_chart);
		}
	}	
	public function actionMonthlyAvgSort(){		
		if(isset($_POST['val']) || isset($_POST['userSR']) ){
			if (isset($_POST['userSR']) && $_POST['userSR']!='' && $_POST['userSR'] !=null && $_POST['userSR'] !='1'){
				Yii::app()->session['userSRsession']= $_POST['userSR'];
				$id=Users::getIdByName($_POST['userSR']);
			}else{
				$id=null;
			}
			if((!isset(Yii::app()->session['yr'])   )&& !isset($_POST['val'])){
		  	  	 Yii::app()->session['yr']=4;		  
		  	}else if(isset($_POST['val'])){
		  		Yii::app()->session['yr']= $_POST['val'];
		  	}
				$sr = array();
				$data_chart = array();
				switch (Yii::app()->session['yr']){
					case '1':
					for ($i = 0;$i<1;$i++){
							$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						}
					break;
					case '5':					
					for ($i = 0;$i<12;$i++)	{
							$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						}					
					break;
					case '3':					
					for ($i = 0;$i<3;$i++){
							$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						}					
					break;
					case '2':
					$yr = (intval(date('Y',strtotime('now -1 year'))));
					for ($i = 1;$i<=12;$i++){
						$months[] = date('Y-m',strtotime(date($yr.'-'.$i)));
					}
					break;					
					case '4':					
					for ($i = 0;$i<6;$i++){
							$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						}					
					break;
				}
			}
			$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			foreach ($months as $month){
				$m=date('m', strtotime($month));
				$starter=date('Y-m-01', strtotime($month));				
				$sr['Payment'] = 0;
				if (!empty($id)){
					$values = Yii::app()->db->createCommand("select DISTINCT(sd_no) as sd from support_desk where MONTH(date)=".$m." and assigned_to=".$id." and date>='".$starter."' and `status` in (3,5) and sd_no in (select id_support_desk from support_desk_comments where status=3 )")->queryAll();
				}else{
					$values = Yii::app()->db->createCommand("select DISTINCT(sd_no) as sd from support_desk where MONTH(date)=".$m." and date>='".$starter."' and `status` in (3,5) and sd_no in (select id_support_desk from support_desk_comments where status=3 )")->queryAll();
				}
				$hours=0;
				if (empty($id)){
					$hours=Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task in ( select id from default_tasks where id_parent='27' ) and MONTH(date)=".$m." and date>='".$starter."'")->queryScalar();
				}else{
					$hours=Yii::app()->db->createCommand("select SUM(amount) from user_time where id_task in ( select id from default_tasks where id_parent='27' ) and MONTH(date)=".$m." and date>='".$starter."' and id_user=".$id."")->queryScalar();
				}
				if (sizeof($values)>0){
					$sr['Payment']= Utils::formatNumber(($hours/ sizeof($values)),2);
				}else{
					$sr['Payment']= 0;
				}				
				$sr['month'] = date('M-y', strtotime($month));
				$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];
				array_push($data_chart,$sr);
			}  
	    echo json_encode($data_chart);
	}
	public function actionPendingPaymentsByMonthSort(){
		if(isset($_POST['val'])){
			$sr = array();
			$data_chart = array();
			Yii::app()->session['yr']= $_POST['val'];
			$year=(date('Y',strtotime('now')));
			$jan=$year.'-01';
			$Feb=$year.'-02';
			$mar=$year.'-03';
			switch (Yii::app()->session['yr']){				
				case '2':
				for ($i = 0;$i<24;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
						   $months[] = $Feb;
						}
					}
				break;
				case '3':
				for ($i = 0;$i<36;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
						   $months[] = $Feb;
						}
					}
				break;			
				case '5':
				for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
						if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
						   $months[] = $Feb;
						}
					}
				break;
			}
			sort($months);
		foreach ($months as $month){
				$sr['Payment'] = 0;
				$m=date('Y-m-t', strtotime($month));			
				$starter=date('Y-m-01', strtotime($month));
				$bound=date('Y-m-01', strtotime('2016-10'));
				$sr['Payment'] = 0;
				if ($starter>=$bound){
					$values=Yii::app()->db->createCommand("select 
					case when r.currency=9 
					THEN r.net_amount 
					else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
					from receivables r
					where LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))<='".$m."' and r.id_customer not in (239,323) and 
					(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and (partner_status Not in ('Paid','Cancelled') or partner_status is null or partner_status='' or partner_status=' ')))
					 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date>'".$m."' and paid_date<>'0000-00-00'))
					and partner != 554 ")->queryAll();			
			 		$sr['Payment'] = round(array_sum(array_column($values,'net_amount')), 2);
					$sr['month'] = date('M-y', strtotime($month));
					$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];
				}else{			
			 		$sr['Payment'] = 0;
					$sr['month'] = date('M-y', strtotime($month));
					$sr['lookupdateval'] = date('Y-m', strtotime($month)).' '.$sr['Payment'];					
				}
				array_push($data_chart,$sr);
			 }  
    		echo json_encode($data_chart);
		}
	}
	public function actionBillabilityBarSort(){
		$sr = array();
		$data_chart = array();
		if (isset($_POST['val'])){
			Yii::app()->session['month']= $_POST['val'];
		}
		if(!isset(Yii::app()->session['month']) || empty(Yii::app()->session['month'])){		  	
		  	 Yii::app()->session['month']=3;		  
		}
		switch (Yii::app()->session['month']){
				case '1':				
						$months[] = date('Y-m',strtotime('now'));					
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<24;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 24 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 12 Months )";
					break;					
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
		$resources="";
  		if (isset($_POST['resc'])) {	
    	  Yii::app()->session['resc'] = $_POST['resc'];			
   		}
		if(!isset(Yii::app()->session['resc']) || empty(Yii::app()->session['resc'])){		  	
		  	 Yii::app()->session['resc']=3;		  
		}
		switch (Yii::app()->session['resc']){
				case '1':
				 $tech=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getTechAll() as $t){
					$tech.=" '".$t['id']."',";
				}
				$tech.=" 0 ) ";
				$resources=$tech;
				break;
				case '2':
				$ops="and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getOpsAll() as $o){
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
				foreach(UserPersonalDetails::getPSAll() as $t){
					$techPS.=" '".$t['id']."',";
				}
				$techPS.=" 0 ) ";
				$resources=$techPS;
				break;
				case '5':
				 $techCS=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getCSAll() as $t){
					$techCS.=" '".$t['id']."',";
				}
				$techCS.=" 0 ) ";
				$resources=$techCS;
				break;
				case '6':
				 $coretech=" and uts.id_user in ("; 			
				foreach(UserPersonalDetails::getCoreTechAll() as $t){
					$coretech.=" '".$t['id']."',";
				}
				$coretech.=" 0 ) ";
				$resources=$coretech;
				break;
   			}
		sort($months);
		foreach ($months as $month){
		$values = Yii::app()->db->createCommand("select  IFNULL(TRUNCATE(bill/tot,2),0) as perc 
		 from (
		 		select sum(r.amount)*100 as bill from (select  sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and uts.id_task=t.id and uts.`default`=0 and t.billable='Yes' and uts.date like '".$month."%'  ".$resources." 
		 		union all
		 		select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and uts.id_task=dt.id and uts.`default`=1  and dt.billable='Yes' and uts.date like '".$month."%'   ".$resources.") as r
			) as billable ,
		 	 (
		 	 	select sum(r.amount) as tot from (select sum(uts.amount) as amount  from  users u , user_time uts , projects_tasks t where u.id=uts.id_user and uts.id_task=t.id and uts.`default`=0 and t.billable IN ('Yes','No') and uts.date like '".$month."%'  ".$resources." 
		 	 	union all
				select sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and uts.id_task=dti.id and uts.`default`=3 and dti.billable in ('Yes','No') and uts.date like '$month%'   
				union all 
		 	    select  sum(uts.amount) as amount  from  users u , user_time uts , default_tasks dt where u.id=uts.id_user and uts.id_task=dt.id and uts.`default`=1   and dt.billable IN ('Yes','No') and  uts.date like '".$month."%'   ".$resources.") as r 
			) as total ")->queryScalar();			
						$sr['billability']= (double)$values ;
						$sr['month'] = date('M-y', strtotime($month));
  						$sr['lookupdate'] = date('Y-m', strtotime($month));
					 array_push($data_chart,$sr);	
			 }  
		echo json_encode($data_chart);
	}	
	public function actionSrBarSortClosed(){
		$sum=0;
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;sort($months);
		}		
			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$values = array();
			$sr = array();
			$dataset = array();
			sort($months);
			foreach($months as $month){
				$sr['PS'][$month] = 0;
				$sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,assigned_to FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val){	
					$id = $val['id'];
					if (Users::getUnit($val['assigned_to']) == 'Tech - CS'){	$sr['CS'][$month]++;	}
					else if (Users::getUnit($val['assigned_to'])== 'Tech - PS') {	$sr['PS'][$month]++;	}
				}
				if($sr != null)	{
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
		    	}
			}			
		}
		}else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$values = array();
			$sr = array();
			$dataset = array();
			sort($months);
			foreach($months as $month){
				$sr['PS'][$month] = 0;
				$sr['CS'][$month] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,assigned_to FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryAll();
				foreach($values as $val){	
					$id = $val['id'];
					if (Users::getUnit($val['assigned_to']) == 'Tech - CS'){	$sr['CS'][$month]++;	}
					else if (Users::getUnit($val['assigned_to'])== 'Tech - PS') {	$sr['PS'][$month]++;	}
				}
				if($sr != null){
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)),'ps'=>$sr['PS'][$month],'cs'=>$sr['CS'][$month], 'value' =>($sr['PS'][$month]+$sr['CS'][$month]))); 
		    	}
			}
		}
		}
		echo json_encode($dataset);
	}
		public function actionSrBarSortSystemShutdown(){
		$sum=0;
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		rsort($months);
			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;		
			$dataset = array();
			foreach($months as $month){			
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes'  and date like '$month%' ")->queryScalar();
				if($values != null &&  $escalate !=null){
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); 
		    	}
			}	
		}
		}else{
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;					
			$dataset = array();
			foreach($months as $month){			
				$values = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and system_down='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				$escalate = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed) and escalate='Yes' and id_customer=".$id_customer." and date like '$month%' ")->queryScalar();
				if($values != null &&  $escalate !=null){
		    		array_push($dataset, array('state'=>date('M-Y', strtotime($month)), 'value' =>(int)$values , 'escalate'=>(int)$escalate)); 
		    	}
			}
		}
		}
		$dataset=array_reverse($dataset);
		echo json_encode($dataset);
	}
	public function actionSrVsRsrBarSort(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$status_closed = 3;
			$status_confirm_closed = 5;
			$data_chart=array();
			foreach($months as $month){
				$sr= Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date like '$month%' ")->queryScalar();
				$srtime= Yii::app()->db->createCommand("select sum(amount) from user_time where `default`=1 and date like '$month%'  and id_task in (select id from default_tasks where id_parent=27)")->queryScalar();
				$rsr=Yii::app()->db->createCommand("SELECT count(1) FROM rsr WHERE adddate like '$month%' ")->queryScalar();
				$rsrtime= Yii::app()->db->createCommand("select sum(amount) from user_time where `default`=1 and date like '$month%'  and id_task in (select id from default_tasks where id_parent=1324)")->queryScalar();
				
				if($sr == 0 || $srtime== null)
				{
					$avgsr= 0;
				}else{
					$avgsr= $srtime/$sr;
				}
				if($rsr == 0 || $rsrtime== null)
				{
					$avgrsr= 0;
				}else{
					$avgrsr= $rsrtime/$rsr;
				}
				array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (double)$avgrsr, 'value2' => (double)$avgsr));
			}
		echo json_encode($data_chart);
		}	
	}
	public function actionSrBarSortSubmitted(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
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
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$status_closed = 3;
			$status_confirm_closed = 5;
			$data_chart=array();
			foreach($months as $month){
				$value= Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE date like '$month%' ")->queryScalar();
				$closed=Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE (status = $status_closed OR status = $status_confirm_closed)  and date like '$month%' ")->queryScalar();
				
				array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (int)$value, 'value2' => (int)$closed));
			}
		echo json_encode($data_chart);
		}	
	}
	public function actionRsrBarSortPerMon()
	{
		$values = array();
      	$data_chart = array();
      	$top5=array();  
      	$limit=''; $w='';

      	if (isset($_POST['MonthRSR']) && $_POST['MonthRSR']!='' && $_POST['MonthRSR'] !=null && $_POST['MonthRSR'] !=1 ){
			$id=Customers::getIdByName($_POST['MonthRSR']);
			$w=" and id_customer=".$id." ";
		} 

      	if (isset($_POST['valrsrmonth']) ){
			Yii::app()->session['timersrmonth']= $_POST['valrsrmonth'];
		}else if (empty(Yii::app()->session['timersrmonth']) && !isset(Yii::app()->session['timersrmonth'])){
			Yii::app()->session['timersrmonth']=3;
		}
		switch (Yii::app()->session['timersrmonth']){
			case '1':
				for($i = 2;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
				break;
			case '2':
				for($i = 5;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
				break;
			case '3':
				for($i = 11;$i>=0;$i--){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 12 Months )";
				break;
		}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
		foreach($months as $month)
		{
			$query="SELECT count(1) as value from rsr where adddate like '$month%' ".$w;  
	  		$value=Yii::app($query)->db->createCommand($query)->queryScalar(); 
	  		//array_push($data_chart,array('category' => date('F', strtotime($month)), 'value' => (int)$value));
	  		array_push($data_chart,array('label' => date('M', strtotime($month)), 'value' => (int)$value));
		}
    	echo json_encode($data_chart);
	}
	public function actionRsrBarSortCustomer(){
		$values = array();
      	$data_chart = array();
      	$top5=array();  
      	$limit=''; $w='';

      	if (isset($_POST['custrsr']) && $_POST['custrsr']!='' && $_POST['custrsr'] !=null && $_POST['custrsr'] !=1 ){
			$id=Customers::getIdByName($_POST['custrsr']);
			$w=" where  id_customer=".$id." ";
		} 

      	if (isset($_POST['valrsr']) ){
			Yii::app()->session['timersr']= $_POST['valrsr'];
		}else if (empty(Yii::app()->session['timersr']) && !isset(Yii::app()->session['timersr'])){
			Yii::app()->session['timersr']=3;
		}
		switch (Yii::app()->session['timersr']){
			case '1':
				$limit.=' LIMIT 10 ';
				break;
			case '2':
				$limit='  LIMIT 20 ';
				break;
			case '3':
				$limit.='';
				break;
		}
	    $query="SELECT DISTINCT(id_customer) as id, count(1) as value from rsr ".$w." GROUP BY id_customer order by count(1)  DESC ".$limit; 
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
	  		   
		usort($values, Widgets::build_sorter('value'));
	 	$total = (double)array_sum(array_column($values,'value'));	 	
	    foreach ($values as $key => $value) {
	    	$customer_name=Customers::getNameByID($value['id']);
	    	array_push($data_chart,array('category' => $customer_name, 'value' => (double)$value['value']));
		}
    	echo json_encode($data_chart);
	}
	public function actionUnsatBarSortCustomer(){
		$values = array();
      	$data_chart = array();
      	$top5=array();  
      	$where='';    	
      	$time='6';
      	if (isset($_POST['userunsat']) && $_POST['userunsat']!='' && $_POST['userunsat'] !=null && $_POST['userunsat'] !=1 ){
			$id=Users::getIdByName($_POST['userunsat']);
			$w=" assigned_to=".$id." and "; 
			$where.=" assigned_to=".$id." and ";
		} else{
			$w='';
		}
      	if (isset($_POST['val']) ){
			Yii::app()->session['timeunsat']= $_POST['val'];
		}else if (empty(Yii::app()->session['timeunsat']) && !isset(Yii::app()->session['timeunsat'])){
			Yii::app()->session['timeunsat']=3;
		}
		switch (Yii::app()->session['timeunsat']){
			case '1':
				$where.=' YEAR(rate_date)=YEAR(CURRENT_DATE()) and MONTH(rate_date)=MONTH(CURRENT_DATE()) ';
				$time='1';
				break;
			case '2':
				$where.=' rate_date>=(CURRENT_DATE()- INTERVAL 3 MONTH) ';
				$time='3';
				break;
			case '3':
				$where.=' rate_date>=(CURRENT_DATE()- INTERVAL 6 MONTH) ';
				$time='6';
				break;
			case '4':
				$where.=' YEAR(rate_date)=(YEAR(CURRENT_DATE())-1) ';
				$time='10';
				break;
		}
	    $query="SELECT DISTINCT(id_customer) FROM `support_desk` where ".$where." AND `status`=5 and rate is not null and rate<>0";  
	    $values=Yii::app($query)->db->createCommand($query)->queryAll(); 
	  	$avg_rate=0;
	    $customer_issues=0;
	    $customer_rate=0;
	    foreach ($values as $key => $value) {	        
	        $customer_issues = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where ".$where." AND id_customer ='".$value['id_customer']."' and `status`=5 and rate is not null and rate not in (0,4,5)  ")->queryScalar();
	 		if ($customer_issues != 0){
	 			array_push($top5, array('id' => $value['id_customer'],'value'=>$customer_issues));
	 		}
	 	}
		usort($top5, Widgets::build_sorter('value'));
	 	$values= array_slice($top5, 0 ,5);
	 	$total = (double)array_sum(array_column($values,'value'));	 	
	    foreach ($values as $key => $value) {
	    	$customer_name=Customers::getNameByID($value['id']);
	    	$customer_totalIssues=Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` where ".$w." id_customer ='".$value['id']."' and `status`=5 and rate is not null and rate <>0  ")->queryScalar();
	        $customer_issues = $value['value'];
			$avg_rate=($customer_issues/$total);
	        $percent = number_format(($avg_rate)*100,0);
			array_push($data_chart,array('category' => $customer_name.' '.$customer_issues.' - '.$time,
	           	  'value' => (double)$percent));
		}
    	echo json_encode($data_chart);
	}
	public function actionSrBarSortCustomer(){
		$months=array();
		if (isset($_POST['val'])){ 
			Yii::app()->session['val'] = $_POST['val'];  
		}
		if (isset($_POST['slice'])){ 
			Yii::app()->session['slice'] = $_POST['slice'];  
		}
	$x=1;
		switch (Yii::app()->session['val'])
		{
				case '2':
					$x=2;
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '3':
					$x=3;
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '4':
					$x=4;
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '1':
					$x=1;
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}		
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$values = array();
			$sr = array();
			foreach($months as $month){
				$values = Yii::app()->db->createCommand("SELECT distinct(id),id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();
				foreach($values as $val){	
					if (!isset($sr[$val['id_customer']][$month]))
						$sr[$val['id_customer']][$month] = 1;
					else
						$sr[$val['id_customer']][$month] ++;			
					if (!isset($sr[$val['id_customer']]['reopen']))
						$sr[$val['id_customer']]['reopen'] = $val['reopen']; 
					else 
						$sr[$val['id_customer']]['reopen'] += $val['reopen']; 
					$issues = Yii::app()->db->createCommand("SELECT count(1) FROM support_desk WHERE id_customer= '".$val['id_customer']."' and date like '$month%'")->queryAll();
				}
			}
			$total=0;
			foreach($sr as $key=>$results){
				$i = 0;
				$sum  = 0;
				foreach ($results as $key_re=>$val){
					if($key_re != 'reopen'){

						$sum += $val;
						$total+=$sum;
					}
				}
			}
			$data_chart = array();
			foreach($sr as $key=>$results){
				$i = 0;
				$sum  = 0;
				$sumtot  = 0;
				foreach ($results as $key_re=>$val){
					if($key_re != 'reopen'){
						$sum += $val;
						$i++;
					}
					$sumtot += $val;
				}
				$percent = number_format(($sum/$total)*100,0);
				 array_push($data_chart,array('category' => Customers::getNameById($key).' '.$sumtot.' - ('.$x,
            	  'value' => $sum));
			}		
			usort($data_chart, Widgets::build_sorter('value'));
			switch (Yii::app()->session['slice']) {
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
	public function actionSrBarSortTopCustomer(){
		if(isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$values = array();
			$sr = array();
			foreach($months as $month){
				$values = Yii::app()->db->createCommand("SELECT id,id_customer,reopen FROM support_desk WHERE date like '$month%'")->queryAll();
				foreach ($values as $val){	
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
			foreach(array_slice($sr, 0, 10) as $results){
	    		array_push($data_chart,array('category' => Customers::getNameById($results['cust']).' ('.$results['reopen'].' R)',
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}	
	public function actionSrBarSortPriorityCustomer(){
		if(isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
			$values = array();
			$sr = array();
			foreach($months as $month){
				$values = Yii::app()->db->createCommand("select severity from support_desk WHERE date like '$month%' and id_customer=".$id_customer." ")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$val['severity']]['val']))
						$sr[$val['severity']]['val'] = 1;
					else
						$sr[$val['severity']]['val'] ++;					
					$sr[$val['severity']]['cust'] = $val['severity']; 				
				}
			}			
			uasort($sr, Widgets::build_sorter('val'));
			$data_chart = array();
			foreach(array_slice($sr, 0, 10) as $results){
	    		array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}	
	public function actionSrOpenStatusCustomer(){
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
				foreach ($values as $val){					
	    		array_push($data_chart,array('category' => $val['status']."(".$val['counts'].")", 'value' => $val['counts']));				
				}
    	echo json_encode($data_chart);
	}
	public function actionSrOpenSeverityCustomer(){
		if(isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
    	$values = array();
			$sr = array();
			foreach($months as $month){
				$values = Yii::app()->db->createCommand("select severity from support_desk WHERE date like '$month%' and id_customer=".$id_customer."  and (status !=3 and status!=5)")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$val['severity']]['val']))
						$sr[$val['severity']]['val'] = 1;
					else
						$sr[$val['severity']]['val'] ++;
					$sr[$val['severity']]['cust'] = $val['severity']; 				
				}
			}			
			uasort($sr, Widgets::build_sorter('val'));
			$data_chart = array();
			foreach(array_slice($sr, 0, 10) as $results){
	    		array_push($data_chart,array('category' => $results['cust']."(".$results['val'].")",
            	  'value' => $results['val']));
			}
		}
		echo json_encode($data_chart);
	}
public function actionGetAgingInvoices(){
		if (isset($_POST['val'])){
	  $values = array();
	  $results = array();
      $data_chart = array();
       switch ($_POST['val']) {
      	case '1':
      		$date = date('Y-m-d',strtotime('now - 1 year'));
      		break;
      	case '2':
      		$onedate = date('Y-m-d',strtotime('now - 1 year'));
      		$date = date('Y-m-d',strtotime(date("Y-m-d", strtotime($onedate)) . " - 6 months "));
      		break;
      	case '3':
      		$date = date('Y-m-d',strtotime('now - 2 year'));
      		break;
		case '4':
      		$date = date('Y-m-d',strtotime('now - 1 month'));
      		break;
		case '5':
      		$date = date('Y-m-d',strtotime('now - 2 months'));
      		break;
		case '6':
      		$date = date('Y-m-d',strtotime('now - 3 months'));
      		break;
 		case '7':
      		$date = date('Y-m-d',strtotime('now - 4 months'));
      		break;    
 		case '8':
      		$date = date('Y-m-d',strtotime('now - 5 months'));
      		break;  		
      	default:
      		$date = date('Y-m-d',strtotime('now - 6 months'));
      		break;
      }
      $months = array(0,60,90,120,180,365);
for($i = 0;$i<6;$i++){
  $j = $i+1;
if($i < 5){
	 $values = Yii::app()->db->createCommand("
 select sum(net_amount) total ,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) as age,r.currency
        from receivables r
        where (((partner = 77 and r.status <>'Paid') or (partner != 77 and (partner_status <>'Paid' or partner_status is null))) and partner !=554 and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) > $months[$i] and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) < $months[$j])
GROUP BY age,r.currency 
ORDER BY age desc
    		")->queryAll();
  } else { 
 $values = Yii::app()->db->createCommand("
 select sum(net_amount) total ,DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01')))  age,r.currency
        from receivables r
        where (((partner = 77 and r.status <> 'Paid') or (partner != 77 and (partner_status <>'Paid' or partner_status is null) )) and partner !=554 and DATEDIFF(NOW(),LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01'))) > $months[$i] )
GROUP BY age,r.currency 
ORDER BY age desc
        ")->queryAll();
  }
	 foreach ($values as $key => $value) {
        if ($i < 5){
        $key = $months[$i].'-'.$months[$j];
}else{
  $key = '>'.$months[$i];
}
      	if ($value['currency'] != CurrencyRate::OFFICIAL_CURRENCY){
					$rate = CurrencyRate::getCurrencyRate($value['currency']);
					if (isset($rate['rate'])){
						$realamount = $value['total'] * $rate['rate'];
					}
				}else{
					$realamount = $value['total'];
				}
        if(!isset($results[$key])){
          $results[$key]['Aging'] = $key;
          $results[$key]['total'] = $realamount;
        } else{
            $results[$key]['total'] += $realamount;
          }      
        }
      }
	  foreach ($results as $res) {
          array_push($data_chart,array('label' => $res['Aging'],
                    'value' => (double)round( $res['total'],2)));
        }
				echo json_encode($data_chart);
      }
}
public function actionGetyeardiscount(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<5;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 5 Years )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 3 Years )";
					break;
				case '3':
					for ($i = 0;$i<10;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 10 Years )";
					break;
				case '4':
					for ($i = 0;$i<1;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( This Year )";
					break;
			}			
			$sr = array();
			$data_chart = array();
			sort($years);
			foreach ($years as $year){
				$discount= 0;
				$value = Yii::app()->db->createCommand("SELECT case when SUM(discount)/count(1) is null then 0 else SUM(discount)/count(1) end  perc,id from eas where status>=2 and discount>0 and YEAR(created)='$year%'")->queryScalar();
				$afterdisc = Yii::app()->db->createCommand("SELECT SUM(netamountusd) from eas where status>=2 and discount>0 and YEAR(created)='$year%'")->queryScalar();
				$beforedisc = Yii::app()->db->createCommand("
							select CASE
							                WHEN e.category= 25
							                THEN
							                    CASE 
							                        WHEN e.currency='9'
							                        THEN (ei.amount*ei.man_days)
							                        ELSE ((ei.amount*ei.man_days)*(select c.rate FROM currency_rate c where c.currency=e.currency  order by c.date DESC  limit 1 ))
							                    END
							                ELSE
							                    CASE 
							                        WHEN e.currency='9'
							                        THEN ei.amount
							                        ELSE (ei.amount*(select c.rate FROM currency_rate c where c.currency=e.currency  order by c.date DESC  limit 1))
							                    END
							            END AS totusd
							from eas e, eas_items ei 
							where e.id=ei.id_ea and discount>0  and YEAR(e.created)='$year%' and e.status>=2
							")->queryAll();
				$tot = array_sum(array_column($beforedisc,'totusd'));
				$discount=(int)$tot-(int)$afterdisc;
			    array_push($data_chart,array('label' => $year."
					(".$discount.'$)',
			            	  'value' => (int)$value));				
			}			
		}
		echo json_encode($data_chart);
	}
	public function actionDSOIndex(){
		if (isset($_POST['valdso'])){
			switch ($_POST['valdso']){
				case '1':
					for ($i = 0;$i<5;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 5 Years )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 3 Years )";
					break;
				case '3':
					for ($i = 0;$i<10;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 10 Years )";
					break;
				case '4':
					for ($i = 0;$i<1;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( This Year )";
					break;
			}
			$sr = array();
			sort($years);
			$data_chart = array();
		foreach ($years as $year){
			$value = Invoices::getnetPendingPerYear($year)/Invoices::getnettotPerYear($year);
		    $finalval = (double)$value*365;
		    array_push($data_chart,array('label' => $year,
	            	  'value' => $finalval));
		}
    	echo json_encode($data_chart);
		}
	}
	public function actionSubmittedCustomer(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<5;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 5 Years )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 3 Years )";
					break;
				case '3':
					for ($i = 0;$i<10;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( Last 10 Years )";
					break;
				case '4':
					for ($i = 0;$i<1;$i++){
						$years[] = date('Y',strtotime('now - '.$i.' year'));
					}
					$time = "( This Year )";
					break;
			}			
			$sr = array();
			sort($years);
			foreach ($years as $year){
				$sr[$year][] = 0;
				$values = Yii::app()->db->createCommand("SELECT id,id_customer FROM support_desk WHERE date like '$year%'")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$year][$val['id_customer']]))
						$sr[$year][$val['id_customer']] = 1;
					else
						$sr[$year][$val['id_customer']] ++;
				}
			}			
			$data_chart = array();
			if ($sr != null){
				foreach($sr as $key => $results){
					$i = -1;
					$sum  = 0;
					if ($results != null){
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
		public function actionprojectBarSortAlerts(){
			$values = array();
			$sr = array();
			sort($months);
			foreach ($months as $month){
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and reason is not NULL")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();
			foreach ($sr as $key=>$results){
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
		echo json_encode($data_chart);
	}
	public function actionchangeRsrAvgRec(){	
		$x=3;
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					$x=1;
					break;
				case '2':
					$x=3;
					break;
				case '3':
					$x=6;
					break;
				case '4':
					$x=12;
					break;
			}
		}
		$data_chart=array();
		    $results = Yii::app()->db->createCommand("SELECT SUM(t.avg) , t.id_user , count(t.id) as count , SUM(t.avg)/count(t.id) as response_time  from 
			(select s.id ,TIMESTAMPDIFF(MINUTE,s.adddate,s.closedate)/1440 as avg, s.assigned_to as id_user from rsr s, users u 
			where s.assigned_to=u.id and s.`status` =6 and (u.id in ( SELECT id_user FROM `user_groups` where id_group=9) 
			and u.id not in(31,20,9)) and s.closedate>=(CURRENT_DATE()- INTERVAL ".$x." MONTH) and s.adddate>=(CURRENT_DATE()- INTERVAL ".$x." MONTH) 
			GROUP BY s.id order by 2 desc ) t group by t.id_user")->queryAll();
	    foreach ($results as $result)  {
	    	$cname=Users::getNameById($result['id_user']);
	    	$avg= number_format((float)$result['response_time'], 2, '.', '');
	    	if ($cname !='' && $cname !='NULL' && $cname !=' ' && $avg >0){
				array_push($data_chart,array('label' => $cname, 'value' =>(float)$avg));
	    	}
	    }
	    usort($data_chart, Widgets::build_sorter('value'));
    	echo json_encode($data_chart);    	
    }
	public function actionchangeSrAvgRec(){	
		$x=3;
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					$x=1;
					break;
				case '2':
					$x=3;
					break;
				case '3':
					$x=6;
					break;
				case '4':
					$x=12;
					break;
			}
		}
		$data_chart=array();
		    $results = Yii::app()->db->createCommand("SELECT SUM(t.avg) , t.id_user , count(t.id) as count , SUM(t.avg)/count(t.id) as response_time  from (select s.id ,TIMESTAMPDIFF(MINUTE,s.date,sdc.date)/1440 as avg, sdc.id_user from support_desk s  , support_desk_comments sdc , users u 
				where sdc.id_user=u.id and s.id =sdc.id_support_desk and s.date<sdc.date and s.date <> '0000-00-00 00:00:00' and sender='SNS' and s.`status` in ('3','5') and s.id_customer in (select id from customers where status=1) and (u.id in ( SELECT id_user FROM `user_groups` where id_group=9) and u.id not in(31,20,9))
				and sdc.date>=(CURRENT_DATE()- INTERVAL ".$x." MONTH) and s.date>=(CURRENT_DATE()- INTERVAL ".$x." MONTH) 
				and not exists ( select 1  from support_desk_comments sd where sd.sender='SNS' and sd.id_user<>sdc.id_user and sd.date<sdc.date and sdc.id_support_desk=sd.id_support_desk )
				 GROUP BY s.id
				order by 2 desc
					) t group by t.id_user")->queryAll();
	    foreach ($results as $result)  {
	    	$cname=Users::getNameById($result['id_user']);
	    	$avg= number_format((float)$result['response_time'], 2, '.', '');
	    	if ($cname !='' && $cname !='NULL' && $cname !=' ' && $avg >0){
				array_push($data_chart,array('label' => $cname, 'value' =>(float)$avg));
	    	}
	    }
	    usort($data_chart, Widgets::build_sorter('value'));
    	echo json_encode($data_chart);    	
    }
	public function actionSrBarSortReason(){
		if(Yii::app()->user->isAdmin){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}

		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$values = array();
			$sr = array();
			sort($months);
			foreach ($months as $month){
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and reason is not NULL")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();	
			$total=array_sum($sr);

			foreach ($sr as $key=>$results){
				$perce=Utils::formatNumber($results*100/$total,2);
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key)." (".$perce."%)",
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
		}
	}else{
		$id_user=Yii::app()->user->id;
		$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();

		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
			$values = array();
			$sr = array();
					$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			foreach ($months as $month){
				$values = Yii::app()->db->createCommand("SELECT id,reason FROM support_desk WHERE date like '$month%' and id_customer=".$id_customer." and reason is not NULL")->queryAll();
				foreach ($values as $val){	
					if (!isset($sr[$val['reason']]))
						$sr[$val['reason']] = 1;
					else
						$sr[$val['reason']] ++;
				
				}
			}
			$data_chart = array();
			foreach ($sr as $key=>$results){
				array_push($data_chart,array('label' => Codelkups::getCodelkup($key),
            	  'value' => $results));
			}
			usort($data_chart, Widgets::build_sorter('value'));
		}		
	}
		echo json_encode($data_chart);
	}
	public function actionmostActiveProject(){
		$order="order by man_days desc limit 5";
		$o=0;
		if (isset($_POST['month'])){ Yii::app()->session['month'] = $_POST['month'];  }
		if (isset($_POST['top'])){  Yii::app()->session['top'] = $_POST['top']; }
			switch (Yii::app()->session['month']){
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
			switch (Yii::app()->session['top']){
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
				foreach ($values as $val){						
						$sr[$val['id_project']]= $val['man_days'] ;
					}	
			$data_chart = array();
			asort($sr);	
			foreach ($sr as $key=>$results){   if($o==1){ 
	$ProjectName = "<span style='font-size:11px'>".substr(Projects::getNameByid($key),0,30)."</span>"; }else{
	$ProjectName =substr(Projects::getNameByid($key),0,30);
	 }
				array_push($data_chart,array('label' =>$ProjectName,
            	  'value' => $results));
			}
    		echo json_encode($data_chart);	
}
	public function actionrescourceBarBillable(){
		$order="order by nbperc desc limit 5";
		$month= date('Y-m-01',strtotime('now - 6 month'));
		$o=0;		
		if (isset($_POST['valbill'])){ Yii::app()->session['monthbill'] = $_POST['valbill'];  }
		if (isset($_POST['topbill'])){  Yii::app()->session['topbill'] = $_POST['topbill']; }
			switch (Yii::app()->session['monthbill']){
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
			switch (Yii::app()->session['topbill']){
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
GROUP BY r.id_user ) t1,
(
select r.id_user , sum(r.amount) as total from (
select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where u.id=uts.id_user and u.active='1' and  uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable IN ('Yes','No') and  uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where u.id=uts.id_user and u.active='1' and  uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable IN ('Yes','No') and  uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where u.id=uts.id_user and u.active='1' and  uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable IN ('Yes','No') and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 
) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user ".$order." ")->queryAll();
				foreach ($values as $val){						
						$sr[$val['id_user']]= (int)$val['nbperc']  ;				
				}			
			$data_chart = array();
			 asort($sr);	
			foreach ($sr as $key=>$results){ 
				 if($o==1){ $name ="<span style='font-size:11px'>".Users::getNameByid($key)."</span>"; }
				 else{ $name=Users::getNameByid($key);  }
				array_push($data_chart,array('label' => $name,
            	  'value' => $results));
			}    	
    		echo json_encode($data_chart);	
}
public function actionrescourceBarNonBillable(){
		$order="order by nbperc desc limit 5";	
		$month= date('Y-m-01',strtotime('now - 6 month'));
		$o=0;
		if (isset($_POST['valnobill'])){ Yii::app()->session['monthnobill'] = $_POST['valnobill'];  }
		if (isset($_POST['topnobill'])){  Yii::app()->session['topnobill'] = $_POST['topnobill']; }
			switch (Yii::app()->session['monthnobill']){
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
			switch (Yii::app()->session['topnobill']){
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
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where  u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable='No' and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u ,  user_time uts , default_tasks dt where   u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable='No' and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 
) as r  
GROUP BY r.id_user ) t1,
(
select r.id_user , sum(r.amount) as total from (
select uts.id , uts.id_user ,t.billable , sum(uts.amount) as amount  from users u , user_time uts , projects_tasks t where  u.id=uts.id_user and u.active='1' and uts.id_task=t.id and uts.`default`=0 and uts.status='1' and t.billable  in ('No','Yes') and uts.date  between '$month' and '$current_date'  GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dti.billable , sum(uts.amount) as amount  from users u , user_time uts , internal_tasks dti where  u.id=uts.id_user and u.active='1' and uts.id_task=dti.id and uts.`default`=3 and uts.status='1' and dti.billable in ('No','Yes') and uts.date between '$month' and '$current_date' GROUP BY uts.id_user 
union all
select uts.id , uts.id_user ,dt.billable , sum(uts.amount) as amount  from users u , user_time uts , default_tasks dt where  u.id=uts.id_user and u.active='1' and uts.id_task=dt.id and uts.`default`=1 and uts.status='1' and dt.billable in ('No','Yes') and uts.date  between '$month' and '$current_date' GROUP BY uts.id_user 
) as r  
GROUP BY r.id_user )t2
where t1.id_user = t2.id_user ".$order." ")->queryAll();
				foreach ($values as $val){						
						$sr[$val['id_user']]= (int)$val['nbperc'] ;				
				}			
			$data_chart = array();
			 asort($sr);	
			foreach ($sr as $key=>$results){ 
				 if($o==1){ $name ="<span style='font-size:11px'>".Users::getNameByid($key)."</span>"; }
				 else{ $name=Users::getNameByid($key);  }
				array_push($data_chart,array('label' => $name,
            	  'value' => $results));
			}    	
    		echo json_encode($data_chart);	
}	
	public function actionSrBarSortResource(){    
		$data_chart = array();
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					$month = date('Y-m', strtotime('now - 11 month'));
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
				$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$status_closed = SupportDesk::STATUS_CLOSED;
			$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
			$srs = Yii::app()->db->createCommand("SELECT * FROM (SELECT sd.id as id, sdc.id as scid, sd.assigned_to, sd.reopen, sdc.date, DATE_FORMAT(sdc.date,'%Y-%m') ym FROM support_desk sd left join support_desk_comments sdc on sd.id=sdc.id_support_desk WHERE ((sd.status = {$status_closed} or sd.status = {$status_confirm_closed}) and (sdc.status = {$status_closed} or sdc.status = {$status_confirm_closed})) and sd.assigned_to IS NOT NULL and sd.assigned_to<>'0' order by sdc.id desc) as tmp_table group by id;")->queryAll();
			$result = array();			
			foreach ($srs as $sr) {
				$month = $sr['ym'];
				if (in_array($month, $months)) {
					$names = explode(" ", trim(preg_replace('/\s+/', ' ', Users::getUsername($sr['assigned_to']))), 2);
					if (!isset($result[$sr['assigned_to']]['category'])) {
						count($names) == 1 ? 
						$result[$sr['assigned_to']]['category'] = $names[0][0] :
						$result[$sr['assigned_to']]['category'] = $names[0][0].$names[1][0];
					}
					$result[$sr['assigned_to']]['fullname'] =Users::getUsername($sr['assigned_to']);
					if (!isset($result[$sr['assigned_to']]['count']))
						$result[$sr['assigned_to']]['count'] = 1;
					else
						$result[$sr['assigned_to']]['count']++;
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
		usort($data_chart, Widgets::build_sorter('value'));
		echo json_encode($data_chart);
	}	
public function actionProjectAlerts(){ 
    		$values = array();
			$sr = array();
				$values = Yii::app()->db->createCommand("select DISTINCT id_project from projects_alerts order by id_project")->queryAll();
				foreach ($values as $val){						
						$sr[$val['id_project']]= ProjectsAlerts::getAlertsCount($val['id_project']);				
				}						
			$data_chart = array();			
			arsort($sr);				
			$i=0;	
				foreach ($sr as $key=>$results){ if($i<10){
						array_push($data_chart,array('category' => Projects::getNameByID($key),
            			  'value' => $results));
						$i++;
            			   }						
					}
    	echo json_encode($data_chart);
	}
	public function actionSrTime(){
		$sr = array(); 
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '2':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '1':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
		$status_close  = SupportDesk::STATUS_CLOSED;
		$status_confirm_closed = SupportDesk::STATUS_CONFIRME_CLOSED;
		foreach ($months as $month){
			$values = Yii::app()->db->createCommand("SELECT id,date FROM support_desk WHERE date like '$month%' and (status = $status_close OR status=$status_confirm_closed) ")->queryAll();
			foreach($values as $val)   {
		    	$id = $val['id'];
		    	$end_date = Yii::app()->db->createCommand("SELECT date FROM support_desk_comments WHERE id_support_desk = {$id} ORDER BY date DESC LIMIT 1")->queryScalar();
		    	$seconds = strtotime($end_date) - strtotime($val['date']);
				$hours   = floor(($seconds) / 3600);
		    		if ($hours < 8){
		    		if (!isset($sr['less 8']))
						$sr['less 8'] = 1;
					else
						$sr['less 8'] ++;
		    	}else if($hours >= 8 && $hours < 16){
		    		if (!isset($sr['8']))
						$sr['8'] = 1;
					else
						$sr['8'] ++;
		    	} else if($hours >= 16 && $hours < 24) {
			    	if(!isset($sr['16']))
						$sr['16'] = 1;
					else
						$sr['16'] ++;
			    } else if($hours >= 24 && $hours<48){
				    if(!isset($sr['24']))
						$sr['24'] = 1;
					else
						$sr['24'] ++;
				} else if($hours >= 48 && $hours<72){
				    	if(!isset($sr['48']))
							$sr['48'] = 1;
						else
							$sr['48'] ++;
				} else if($hours >= 72 && $hours<96){
				    	if(!isset($sr['72']))
							$sr['72'] = 1;
						else
							$sr['72'] ++;
				   }else if($hours >= 96 && $hours<192){
				    		if(!isset($sr['96']))
								$sr['96'] = 1;
							else
								$sr['96'] ++;
				    	}else if($hours>=192){	
					    	if(!isset($sr['192']))
								$sr['192'] = 1;
							else
								$sr['192'] ++;
					    }
		    }	
		}
		$data_chart = array();
		ksort($sr);
		foreach ($sr as $key=>$result){
	    	 array_push($data_chart,array('category' => $result." ".WidgetTime::getLabel($key),
            	  'value' => $result));
	    }
	    usort($data_chart, Widgets::build_sorter('value'));
        echo json_encode($data_chart);
	}
	public function actionSrSupport(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		$values = array();
			$x_axis = array();
			$status_close = SupportDesk::STATUS_CLOSED;
			$id_default_support = WidgetSupport::DEFAULT_SUPPORT;
			$ids_users = array();
			$dataset = array();
			sort($months);
			foreach ($months as $month){
				$values = Yii::app()->db->createCommand("SELECT ut.id_user FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
				foreach($values as $val) {
			    	array_push($ids_users,$val['id_user']);
			    }
			}
			sort($months);
	   		foreach ($months as $month){
				$sr = array();
				$values = Yii::app()->db->createCommand("SELECT ut.amount,ut.id_user,ut.date,df.id_parent FROM user_time ut 
														LEFT JOIN timesheets t ON (t.id = ut.id_timesheet)
														LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														LEFT JOIN user_groups ug ON (ug.id_user=ut.id_user)
														WHERE df.id_parent = $id_default_support AND ut.date like '$month%' and ug.id_group=9 ORDER BY  ut.id_user ASC")->queryAll();	
				foreach ($values as $val)   {					
			    	if (!isset($sr[$val['id_user']]))
					{
			    		$total= self::GetTotalUserPermonth($val['id_user'], $month);
			    		if($total==0) { $total =1; }
						$sr[$val['id_user']] = round((($val['amount']/$total)*100),2);
					}else{
						$total= self::GetTotalUserPermonth($val['id_user'], $month);
						if($total==0) { $total =1; }
						$sr[$val['id_user']]+= round((($val['amount']/$total)*100),2);
					}
			    }
			    foreach ($ids_users as $id_user){
			    	if (!isset($sr[$id_user]))
			    		$sr[$id_user] = 0;
			    }
			    $data = array();
			    $data['month'] = date('M-Y', strtotime($month));
				foreach ($sr as $key=>$result){
			    	 $data[Users::getUsername($key)] = $result;
			    }
			    array_push($dataset,$data);
			}
		}
		echo json_encode($dataset);
	}
	public function GetTotalUserPermonth($user, $month)
    {
    	return Yii::app()->db->createCommand("SELECT SUM(ut.amount) FROM user_time ut LEFT JOIN default_tasks df ON (df.id = ut.id_task)
														WHERE ut.id_user=".$user."  AND (df.id_parent != 2 or df.id_parent is null ) AND ut.date like '$month%' ")->queryScalar();	
    }
public function actionsrRate(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
			$sr = array();		
			$users = Yii::app()->db->createCommand("SELECT id_user FROM `user_groups` WHERE id_group=9")->queryAll();	
			$dataset = array();
	   		sort($months);
	   		foreach ($months as $k => $month){
				$data = array();
			    $data['month'] = date('M-Y', strtotime($month));
				$sr = array();				
				foreach($users as $user){
					$query="SELECT count(1)
					FROM `support_desk`  s
					where  YEAR(s.rate_date)=YEAR(CURRENT_DATE()) and s.status=5 and s.rate is not null and s.rate not in (0,1,2,3) 
					and (SELECT id_user from support_desk_comments where status=3 and s.id=id_support_desk ORDER BY date DESC limit 1)='".$user['id_user']."'
					and  s.rate_date like '$month%'";	  				
	  				$value=Yii::app()->db->createCommand($query)->queryScalar(); 
					$sum = Yii::app()->db->createCommand("SELECT count(1) FROM `support_desk` s where YEAR(s.rate_date)=YEAR(CURRENT_DATE()) AND rate_date like '$month%' and s.status=5 and s.rate is not null and (SELECT id_user from support_desk_comments where status=3 and s.id=id_support_desk ORDER BY date DESC limit 1)='".$user['id_user']."'")->queryScalar();       	
					if ($sum != 0){
						$avg= ($value*100/$sum);	
					if ($avg != 0){	$avg=number_format($avg,2);
		        		$data[Users::getUsername($user['id_user'])] = $avg; }
		        	}
	        	}	 
			    array_push($dataset,$data);
			}			
		}
		echo json_encode($dataset);
	}
	public function actionSrSubmittedResolved(){
		if (isset($_POST['val'])){
			switch ($_POST['val']){
				case '1':
					for ($i = 0;$i<1;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Current Month )";
					break;
				case '2':
					for ($i = 0;$i<3;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 3 Months )";
					break;
				case '3':
					for ($i = 0;$i<6;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last 6 Months )";
					break;
				case '4':
					for ($i = 0;$i<12;$i++){
						$months[] = date('Y-m',strtotime('now - '.$i.' month'));
					}
					$time = "( Last Year )";
					break;
			}
		$year=(date('Y',strtotime('now')));
		$jan=$year.'-01';
		$Feb=$year.'-02';
		$mar=$year.'-03';
		if (in_array($jan, $months) && in_array($mar, $months) && !in_array($Feb, $months)) {
		   $months[] = $Feb;
		}
		sort($months);
			$values = array();
			$x_axis = array();
			$status_close = SupportDesk::STATUS_CLOSED;
			$id_default_support = WidgetSupport::DEFAULT_SUPPORT;
			$ids_users = array();
			$dataset = array();
			$id_user=Yii::app()->user->id;
			$id_customer=Yii::app()->db->createCommand("Select c.id from customers_contacts cc,customers c where c.id=cc.id_customer and cc.id=".$id_user." ")->queryScalar();
		$sr = array();
		foreach ($months as $month){
				$submitted="Submitted";
				$resolved="Resolved";
		    	array_push($ids_users,$submitted);
		    	array_push($ids_users,$resolved);
		}
		 $dataset = array();
   		foreach ($months as $k => $month){
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
	public static function actionCountryRevenues(){
		$year = $_POST['val'];
		switch ($year){
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
	public static function actionEaTypesRevenues(){
		$year = $_POST['val'];
		switch ($year){
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
	public static function actionSoldByRevenues(){
		$year = $_POST['val'];
		switch ($year){
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
	public static function actionGetProjects(){
		$id_customer = (int)$_POST['id'];
		$id_type = (int)$_POST['type'];
		if($id_type == 100)
			$results = Yii::app()->db->createCommand("SELECT * FROM projects WHERE customer_id = '$id_customer'")->queryAll();
 		else
 			$results = Yii::app()->db->createCommand("SELECT * FROM projects WHERE customer_id = '$id_customer' AND id_type =$id_type")->queryAll();
		$data=array();
 		$sum_project = array();
 		foreach ($results as $result){	
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
}?>