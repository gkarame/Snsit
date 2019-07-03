<?php
class Receivables extends CActiveRecord{
	const AGE_365 = 365;	const AGE_270 = 270;	const AGE_180 = 180;	const AGE_90 = 90;	const AGE_30 = 30;	const AGE_0	 = -30;	const GROUP_HR = 6;
	const GROUP_OFFICE_ASSISTANTS = 7;	const GROUP_recruitment = 15;	const GROUP_office_admin = 11;	const PARTNER_STATUS_PAID = 'Paid';	const PARTNER_STATUS_NOT_PAID = 'Not Paid';
	public $age, $textdays;
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return 'receivables';
	}
	public function rules(){
		return array(
			array('id_customer, project_name, status, currency, sold_by, paid_date, id_assigned', 'required'),
			array('id, id_customer, id_ea, currency, partner, sns_share, invoice_date_month, invoice_date_year, sold_by, id_assigned', 'numerical', 'integerOnly'=>true),
			array('net_amount, gross_amount, partner_amount, payment_procente, amount, age', 'numerical'),
			array('final_invoice_number, partner_inv, span_partner_inv,snsapj_partner_inv','length', 'max'=>7),
			array('project_name,type, textdays', 'length', 'max'=>255),
			array('status', 'length', 'max'=>9),
			array('old', 'length', 'max'=>3),
			array('partner_status', 'length', 'max'=>8),
			array('invoice_number, invoice_title, payment, printed_date, id_expenses, notes, remarks', 'safe'),
			array('id, id_customer, invoice_number, final_invoice_number, invoice_title, project_name, id_ea, payment, payment_procente, status, currency, partner, sns_share, invoice_date_month, invoice_date_year, sold_by, old, printed_date, partner_status, partner_inv, span_partner_inv, snapj_partner_inv, net_amount, gross_amount, partner_amount, amount, id_expenses, paid_date, notes, remarks, id_assigned, age, textdays', 'safe', 'on'=>'search'),
		);
	}
	public function relations(){
		return array(
			'customer' => array(self::BELONGS_TO, 'Customers', 'id_customer'),
			'assignedTo' => array(self::BELONGS_TO, 'Users', 'id_assigned'),
			'project' => array(self::BELONGS_TO, 'Projects', '', 'on'=>'project.name=t.project_name'),
			'rCurrency' => array(self::BELONGS_TO, 'Codelkups', 'currency'),
			'unit' => array(self::BELONGS_TO, 'Codelkups', 'sold_by'),
			'rPartner' => array(self::BELONGS_TO, 'Codelkups', 'partner'),
			'ea' => array(self::BELONGS_TO, 'Eas', 'id_ea'),
		);
	}
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'id_customer' => 'Customer',
			'invoice_number' => 'Invoice #',
			'final_invoice_number' => 'Final Invoice #',
			'invoice_title' => 'Invoice Title',
			'project_name' => 'Project Name',
			'id_ea' => 'EA #',
			'payment' => 'Payment #',
			'payment_procente' => 'Payment %',
			'status' => 'Status',
			'currency' => 'Currency',
			'partner' => 'Partner',
			'sns_share' => 'Sns Share',
			'invoice_date_month' => 'Invoice Date Month',
			'invoice_date_year' => 'Invoice Date Year',
			'sold_by' => 'Sold By',
			'old' => 'Old',
			'printed_date' => 'Printed Date',
			'partner_status' => 'Partner Status',
			'partner_inv' => 'Partner Invoice',
			'span_partner_inv' => 'Span Inv',
			'snsapj_partner_inv' => 'SNSAPJ Inv',
			'net_amount' => 'Net Amount',
			'gross_amount' => 'Gross Amount',
			'partner_amount' => 'Partner Amount',
			'amount' => 'Amount',
			'id_expenses' => 'Expenses',
			'paid_date' => 'Paid Date',
			'notes' => 'Notes',
			'remarks' => 'Remarks',
			'id_assigned' => 'Assigned To',
			'age' => 'Age',
			'textdays' => 'Age in hours'
		);
	}
	public function search(){
		$criteria=new CDbCriteria;	$criteria->compare('id', $this->id);	$criteria->compare('id_customer', $this->id_customer);
		$criteria->compare('invoice_number', $this->invoice_number,true);	$criteria->compare('final_invoice_number', $this->final_invoice_number,true);
		$criteria->compare('invoice_title', $this->invoice_title,true);	$criteria->compare('project_name', $this->project_name,true);
		$criteria->compare('id_ea', $this->id_ea,true);	$criteria->compare('payment', $this->payment,true);
		$criteria->compare('payment_procente', $this->payment_procente,true);	$criteria->compare('status', $this->status,true);
		$criteria->compare('currency', $this->currency);
		if (isset($this->partner) &&  $this->partner!='' &&  $this->partner!=' '){
        	$criteria->compare('partner', $this->partner,true);
        }else{
        //	$criteria->addCondition("partner !='554' ");
        }	
        if (isset($this->partner_status) &&  $this->partner_status!='' &&  $this->partner_status!=' '){
        	$criteria->addCondition(" ( (partner_status ='".$this->partner_status."' and partner !=77) or partner = 77 ) ");	
        }
        	
		$criteria->compare('sns_share', $this->sns_share);	$criteria->compare('invoice_date_month', $this->invoice_date_month);
		$criteria->compare('invoice_date_year', $this->invoice_date_year);	$criteria->compare('sold_by', $this->sold_by,true);
		$criteria->compare('old',$this->old,true);	$criteria->compare('printed_date', $this->printed_date,true);
		$criteria->compare('partner_inv', $this->partner_inv,true);
		$criteria->compare('span_partner_inv', $this->span_partner_inv,true);	$criteria->compare('snsapj_partner_inv', $this->snsapj_partner_inv,true);
		$criteria->compare('net_amount', $this->net_amount);	$criteria->compare('gross_amount', $this->gross_amount);
		$criteria->compare('partner_amount', $this->partner_amount);	$criteria->compare('amount', $this->amount,true);
		$criteria->compare('id_expenses', $this->id_expenses,true);	$criteria->compare('paid_date', $this->paid_date,true);
		$criteria->compare('notes', $this->notes,true);	$criteria->compare('remarks', $this->remarks,true);
		$criteria->compare('id_assigned', $this->id_assigned);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function getType($type){
		$str="";
		$arr= explode(',', $type);
		$i=0;
		foreach ($arr as $key => $a) {
			//print_r($a);exit;
			if ($a == 'TandM'){
				$str.= "T&M";
			}else if($a == 'Expenses'){
				$str.= "Expense Sheet";
			}
			else{
				$str.= $a;
			}
			if($key< (count($arr) -1))
			{
				$str .= ", ";
			}
			//print_r($str);
		}
		return $str;	
	}	
	public static function getAgeOptions(){
		return array(
			self::AGE_365 => 'More than  365',
			self::AGE_270 => 'More than  270',
			self::AGE_180 => 'More than  180',
			self::AGE_90 => 'More than  90',
			self::AGE_30 => 'More than  30',
			self::AGE_0	 => 'Less than 30' 
		);
	}	
	public static function getAgeList($age){
		$age = (float)$age;
		switch(true){
			case ($age >= 365):
				$res = 'More than 365';
				break;
			case ($age >= 270):
				$res = 'More than 270';
			break;
			case ($age >= 180):
				$res = 'More than 180';
				break;
			case ($age >= 90):
				$res = 'More than 90';
				break;
			case ($age >= 30):
				$res = 'More than 30';
				break;
			default:
				$res = 'Less than 30';
		}
		return $res;
	}	
	public function getTextdays($age = 10){	return $this->getAgeList($age);	}	
	public function getAge(){
		$days = 0;	$invoice_date_month=$this->invoice_date_month; 	$invoice_date_year= $this->invoice_date_year; 		
		if (!empty($invoice_date_month) && !empty($invoice_date_year) )	{
			$now = time();
			$days= Yii::app()->db->createCommand("select DATEDIFF(NOW(),LAST_DAY(CONCAT('".$invoice_date_year."','-','".$invoice_date_month."','-01')))")->queryScalar();
		}
		return $days;
	}
	public static function getpartinvBox($finalinv, $partner, $old, $partner_inv){		
		if($partner == 'SNS AUST' )
		{
			return $partner_inv;
		}else if ($old=='No'){ $where="where final_invoice_number= '".$finalinv."' ";}
		else{ $where="where old_sns_inv= '".$finalinv."' ";}
		$str='';
		if ($partner == 'SNS' || $partner == 'SNSI' ||  $partner == 'LOG CUBES'){
			$str= Yii::app()->db->createCommand("SELECT partner_inv FROM invoices ".$where."")->queryScalar();
		}else if ($partner=='SPAN'){
			$str=Yii::app()->db->createCommand("SELECT span_partner_inv  FROM invoices ".$where."")->queryScalar();			
		} else if ($partner =='SNS APJ' || $partner == 'APJ'){
			$str= Yii::app()->db->createCommand("SELECT snsapj_partner_inv  FROM invoices  ".$where."")->queryScalar();
		}else{
			$str= Yii::app()->db->createCommand("SELECT old_sns_inv  FROM invoices  ".$where."")->queryScalar();
		}
		if ($str==''){
			$str= Yii::app()->db->createCommand("SELECT partner_inv  FROM invoices  ".$where."")->queryScalar();
		}	
		

		 if ($partner=='SPAN' && GroupPermissions::checkPermissions('financial-invoices','write') ) {
		 	return '<input type="text" required name="partner_inv" value="'.$str.'" onchange="UpdatePartner(event,\''.$finalinv.'\',\''.$partner.'\',\''.$old.'\');" style="font-size: 0.73rem;width:65px;">';
		 }else{
		 	return $str;
		 }
	}	
	public static function getpartinv($finalinv, $partner, $old, $partner_inv){		
		if($partner == 'SNS AUST' )
		{
			return $partner_inv;
		}else if ($old=='No'){ $where="where final_invoice_number= '".$finalinv."' ";}
		else{ $where="where old_sns_inv= '".$finalinv."' ";}
		$str='';
		if ($partner == 'SNS' || $partner == 'SNSI' || $partner == 'SNS AUST'  || $partner == 'LOG CUBES'){
			$str= Yii::app()->db->createCommand("SELECT partner_inv FROM invoices ".$where."")->queryScalar();
		}else if ($partner=='SPAN'){
			$str=Yii::app()->db->createCommand("SELECT span_partner_inv  FROM invoices ".$where."")->queryScalar();			
		} else if ($partner =='SNS APJ' || $partner == 'APJ'){
			$str= Yii::app()->db->createCommand("SELECT snsapj_partner_inv  FROM invoices  ".$where."")->queryScalar();
		}else{
			$str= Yii::app()->db->createCommand("SELECT old_sns_inv  FROM invoices  ".$where."")->queryScalar();
		}
		if ($str==''){
			$str= Yii::app()->db->createCommand("SELECT partner_inv  FROM invoices  ".$where."")->queryScalar();
		}	
		return $str;
	}
	public static function getavgageNotPaidAll($cust){
		$values=Yii::app()->db->createCommand("select DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) as age
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."'")->queryAll();
		if(!empty($values)){ $ct = array_sum(array_column($values,'age'))/count($values); } else{	$ct=0; }
		return $ct;
	}
	public static function getavgageNotPaid($cust){
		$values=Yii::app()->db->createCommand("select DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) as age
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' and r.old<>'No' ")->queryAll();
		 $ct = array_sum(array_column($values,'age'))/count($values);
		return $ct;
	}
	public static function getmaxAgePending($cust){
		$value=Yii::app()->db->createCommand("select MAX(DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01')))) as maxage
			from receivables r
			where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' and r.old<>'No' ")->queryScalar();
		return $value;
	}
	public static function getmaxAgePendingAll($cust){
		$value=Yii::app()->db->createCommand("select MAX(DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01')))) as maxage
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' ")->queryScalar();
		return $value;
	}
	public static function gettotalnotpaidandAllGross($cust){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.gross_amount 
			else r.gross_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as gross_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' ")->queryAll();
		 $ct = array_sum(array_column($values,'gross_amount'));
		return $ct;
	}
	public static function getMonthlyAvg($finish_date){
		$values=Yii::app()->db->createCommand("select YEAR(paid_date),MONTH(paid_date),sum(receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency order by c.date desc limit 1)) as tot
		from receivables where ((partner =77 and status ='Paid') OR (partner !=77 and partner_status='Paid')) and partner!=554 and paid_date>'2016-09-30' and paid_date<= '".$finish_date."'
		GROUP BY YEAR(paid_date), MONTH(paid_date) ORDER BY tot DESC")->queryAll();		
		$crows= count($values);	$ct = array_sum(array_column($values,'tot'));	return ($ct/$crows);		
	}
	public static function gettotalNetpaiddates($start,$end){
		$values=Yii::app()->db->createCommand("select case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency   order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status ='Paid') OR (r.partner !=77 and r.partner_status='Paid'))  and r.paid_date>='".$start."' and r.paid_date<='".$end."' ")->queryAll();
		$ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function gettotalNetResPerMonth($Month, $lastday){
		$ct='';	$ids= Yii::app()->db->createCommand("select distinct(id_assigned) as user from receivables where id_customer not in (239,323) and ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and partner !=554")->queryAll();
		$usrs=array();		
		foreach ($ids as  $id){
		  	$values=Yii::app()->db->createCommand("select id_assigned as Resource_ID, users.firstname as FirstName ,users.lastname as LastName,
 			case when receivables.currency=9 
			THEN receivables.net_amount 
			else receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC limit 1)  end as val
			from receivables JOIN users on receivables.id_assigned = users.id where id_customer not in (239,323) and ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and id_assigned=".$id['user']."  and partner !=554 and MONTH(paid_date)=".$Month." and  YEAR(paid_date)=YEAR('".$lastday."') ")->queryAll();
			$points=  array_sum(array_column($values,'val')); 	$usrs[$id['user']]=$points;		 		 
		}
		arsort($usrs);		return $usrs;
	}
	public static function gettotalNetRes($start,$end){
		$ct='';
		$ids= Yii::app()->db->createCommand("select distinct(id_assigned) as user from receivables where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and partner !=554 and paid_date>='".$start."' and paid_date<='".$end."' ")->queryAll();
		$usrs=array();	$i=0;		
		foreach ($ids as  $id){
		  	$values=Yii::app()->db->createCommand("	select id_assigned as Resource_ID, users.firstname as FirstName ,users.lastname as LastName,
 			case when receivables.currency=9 
			THEN receivables.net_amount 
			else receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC limit 1)  end as val
			from receivables JOIN users on receivables.id_assigned = users.id where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and id_assigned=".$id['user']."  and partner !=554 and paid_date>='".$start."' and paid_date<='".$end."' ")->queryAll();
			$points=  array_sum(array_column($values,'val')); 	$usrs[$i]=array( 'name' => $id['user'], 'pts' => $points ); 	$i++;		 
		}
		uasort($usrs, Widgets::build_sorter('pts'));
		foreach ($usrs as $usr) {	$ct.='- '.Users::getNameById($usr['name']).': '.Utils::formatNumber($usr['pts'],2).'<br/>';	}
		return $ct;
	}
	public static function getPending($start){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from receivables r	where 
			(((partner = 77 and status Not in ('Paid','Cancelled')) or (partner != 77 and partner_status Not in ('Paid','Cancelled')))
			 or (((partner = 77 and status='Paid') or (partner != 77 and partner_status='Paid')) and paid_date<".$start." ))
			and partner != 554 ")->queryAll();		
		$ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function gettopYearPerf($start){
		$ct='';
		$ids= Yii::app()->db->createCommand("select distinct(id_assigned) as user from receivables where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and partner !=554 and YEAR(paid_date)=Year('".$start."') and paid_date>'2016-09-30' ")->queryAll();
		$usrs=array();	$i=0;
		$yearpend= Yii::app()->db->createCommand("Select amount  from yearly_sales	where id_codelkup = (select id from codelkups where id_codelist = 31 and codelkup = (Year('".$start."')-1)  order by id desc limit 1)")->queryScalar();
		if (!empty($yearpend) && $yearpend!=0){
		foreach ($ids as  $id){
		  	$values=Yii::app()->db->createCommand("	select id_customer,id_assigned as Resource_ID, users.firstname as FirstName ,users.lastname as LastName,DATEDIFF(CURDATE(), 
			LAST_DAY(CONCAT(receivables.invoice_date_year,'-',receivables.invoice_date_month,'-01'))) as age,
 			case when receivables.currency=9 
			THEN receivables.net_amount
			else receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC limit 1) 
			end as val
			from receivables JOIN users on receivables.id_assigned = users.id where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid'))
			and partner !=554 and id_assigned=".$id['user']." and YEAR(paid_date)=Year('".$start."') and paid_date>'2016-09-30' ORDER BY Resource_ID,val DESC")->queryAll();
			$points=0;
		  	foreach ($values as $value){
		  		$flag= Customers::checkSensitive($value['id_customer']);
		  		if ($flag>0){	$points= $points+ ((($value['val']*$value['age'])/$yearpend)*1.5);
		  		}else{	$points= $points+ (($value['val']*$value['age'])/$yearpend); }		  		
		  	}		 	
		 	$usrs[$i]=array( 'name' => $value['Resource_ID'], 'pts' => $points ); 	$i++;		 
		}
		uasort($usrs, Widgets::build_sorter('pts'));
		foreach ($usrs as $usr){	$ct.='- '.Users::getNameById($usr['name']).': '.Utils::formatNumber($usr['pts'],2).'<br/>';	}		
		}else{	$ct.='- Yearly sales for previous year is not set yet<br/>'; }
		return $ct;
	}
	public static function gettopMonthPerf($start,$end){
		$ct='';
		$ids= Yii::app()->db->createCommand("select distinct(id_assigned) as user from receivables where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid')) and partner !=554 and paid_date>='".$start."' and paid_date<='".$end."' and id_assigned!=0")->queryAll();
		$usrs=array();	$i=0;	$monthpend=self::getPending($start);
		foreach ($ids as  $id){
		  	$values=Yii::app()->db->createCommand("	select id_customer,id_assigned as Resource_ID, users.firstname as FirstName ,users.lastname as LastName,DATEDIFF(CURDATE(), 
			LAST_DAY(CONCAT(receivables.invoice_date_year,'-',receivables.invoice_date_month,'-01'))) as age,
 			case when receivables.currency=9 
			THEN receivables.net_amount
			else receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC limit 1) 
			end as val
			from receivables JOIN users on receivables.id_assigned = users.id where ((partner=77 and status='Paid') OR (partner !=77 and partner_status='Paid'))
			and partner !=554 and id_assigned=".$id['user']." and paid_date>='".$start."' and paid_date<='".$end."'  ORDER BY Resource_ID,val DESC")->queryAll();
			$points=0;
		  	foreach ($values as $value){
		  		$flag=Customers::checkSensitive($value['id_customer']);
		  		if ($flag>0){	$points= $points+ ((($value['val']*$value['age'])/$monthpend)*1.5);
		  		}else{	$points= $points+ (($value['val']*$value['age'])/$monthpend);		}		  		
		  	}		 	
		 	$usrs[$i]=array( 'name' => $value['Resource_ID'], 'pts' => $points ); 	$i++;		 
		}
		uasort($usrs, Widgets::build_sorter('pts'));
		foreach ($usrs as $usr) {	$ct.='- '.Users::getNameById($usr['name']).': '.Utils::formatNumber($usr['pts'],2).'<br/>';	}
		return $ct;
	}
	public static function getImprovFactor($start_date, $finish_date){		
		$curr= self::gettotalNetpaiddates($start_date,$finish_date);	$last_start = date("Y-m-1", strtotime("-2 month") ) ; 
		$last_end = date("Y-m-t", strtotime("-2 month") );	$last= self::gettotalNetpaiddates($last_start,$last_end);
		$ct = ((($curr/$last)-1)*100);
		return $ct;
	}
	public static function getAgeFactor($end){
		$values=Yii::app()->db->createCommand("select DATEDIFF(NOW(),LAST_DAY(CONCAT(r.invoice_date_year,'-',r.invoice_date_month,'-01'))) as age
			from receivables r	where 
			(((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  
				OR
			(((r.partner =77 and r.status ='Paid') OR (r.partner !=77 and r.partner_status ='Paid')) and paid_date>'".$end."'))")->queryAll();		
		$ct = array_sum(array_column($values,'age'))/count($values);	$ct=Utils::formatNumber($ct);
		return $ct;
	}
	public static function getAgeImprovFactor($finish_date){		
		$avgcurr= self::getAgeFactor($finish_date);	$last_end = date("Y-m-t", strtotime("-2 month"));
		$avglast= self::getAgeFactor($last_end);	$ct = ((($avgcurr/$avglast)-1)*100)*(-1);
		return $ct;	
	}
	public static function gettotalnotpaidandAllInv(){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' '))) ")->queryAll();
		$ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function gettotalnotpaidandAll($cust){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function gettotalnotpaid($cust){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency   order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' and r.old<>'No' ")->queryAll();
		$ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function getReportData(){
		$ct=Yii::app()->db->createCommand("select id_customer, id_assigned, MAX(DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01')))) AS maxage, AVG(DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01')))) AS avgage,
			sum(receivables.net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC  limit 1)) as tot
			from receivables where id_customer not in (239,323) and  ((partner =77 and status Not in ('Paid','Cancelled')) OR (partner !=77 and status Not in ('Paid','Cancelled') and partner_status Not in ('Paid','Canceled'))) and partner!=554 and old<>'Yes' GROUP BY id_customer ORDER BY tot DESC")->queryAll();
		return $ct;
	}
	public static function gettotalnotpaidosns($cust){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_customer='".$cust."' and r.old='Yes' ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
public static function getInvPerAssigned($id){
		$values=Yii::app()->db->createCommand("select 
			case when r.currency=9 
			THEN r.net_amount 
			else r.net_amount*(select c.rate from currency_rate c where c.currency=r.currency  order by date DESC  limit 1) end as net_amount
			from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77  and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and  r.id_assigned='".$id."' ")->queryAll();
		 $ct = array_sum(array_column($values,'net_amount'));
		return $ct;
	}
	public static function getCountInv($id){
	$value=Yii::app()->db->createCommand("select count(*) from receivables r	where ((r.partner =77 and r.status Not in ('Paid','Cancelled')) OR (r.partner !=77 and r.status Not in ('Paid','Cancelled') and (r.partner_status Not in ('Paid','Cancelled') or r.partner_status is null or r.partner_status ='' or r.partner_status =' ')))  and r.id_assigned='".$id."' ")->queryScalar();
		return $value;
	}
	public static function getReportDataosns(){
		$ct=Yii::app()->db->createCommand("select id_customer, id_assigned,MAX(DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01')))) AS maxage, AVG(DATEDIFF(CURDATE(), LAST_DAY(CONCAT(invoice_date_year,'-',invoice_date_month,'-01')))) AS avgage,SUM(net_amount*(select c.rate from currency_rate c where c.currency=receivables.currency  order by date DESC  limit 1)) AS tot from receivables where id_customer not in (239,323) and ((partner =77 and status Not in ('Paid','Cancelled')) OR (partner !=77 and status Not in ('Paid','Cancelled') and partner_status Not in ('Paid','Cancelled'))) and old='Yes' GROUP BY id_customer ORDER BY tot DESC")->queryAll();
		return $ct;
	}
	public static function getAmountUsd($curr, $amount){
		if ($curr !=9){
			$ct=Yii::app()->db->createCommand("select rate from currency_rate  where currency='".$curr."'  order by date DESC limit 1")->queryScalar();
			return ($amount*(double)$ct);
		}else{
			return $amount;
		}
	}
	public static function getCustomerperInv($id){
		return Yii::app()->db->createCommand("select id_customer from receivables  where invoice_number='".$id."' ")->queryScalar();
			
	}
	public function getAll($group = NULL, $export = false){	
		$criteria = new CDbCriteria;	
		$criteria->with = array('customer','ea');
		$criteria->select = array(
				"t.*",
				"DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age",
				"IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 365, 'More than 365', 
					IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 270,'More than 270',
						IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 180,'More than 180',
							IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 90,'More than 90', 
								IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 30,'More than 30', 'Less than 30')
							)
						)
					)
				) as textdays"	
		);
		$criteria->addCondition(" t.id_customer not in (239,323) ");
		$dataProvider = new CActiveDataProvider('Receivables', array(
				'criteria' => $criteria,
				'pagination'=>($group != null || $export) ? false : array(
						'pageSize' => Utils::getPageSize(),
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => $group ? $group : ($export ? 'customer.name ASC' : 't.final_invoice_number ASC'),
           		 ),
		));
		return $dataProvider; 
	}
	public function searchReceivablesGr($group = NULL, $export = false){	
		$criteria = new CDbCriteria;
		$criteria->with = array('customer','ea');
		$criteria->select = array(
				"t.*",
				"DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) as age",
				"IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 365, 'More than 365', 
					IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 270,'More than 270',
						IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 180,'More than 180',
							IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 90,'More than 90', 
								IF (DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >= 30,'More than 30', 'Less than 30')
							)
						)
					)
				) as textdays"	
		);		
		$criteria->compare('final_invoice_number', $this->final_invoice_number, true);	$criteria->compare('customer.name', $this->id_customer, true);
		if(isset($this->old) && $this->old !='')
		{
			$criteria->compare('t.old', $this->old);
		}
		$criteria->compare('ea.ea_number', $this->id_ea, true);		 
		if (isset($this->age) && $this->age != ''){
			if ($this->age == -30)	{	$criteria->addCondition("DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) < 30");
			}else {	$criteria->addCondition("DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >=". $this->age);
			}
		}
		if (isset($this->type) && $this->type[0]!='' &&  $this->type!=' ' && !empty($this->type)){         	
			$types=$this->type;		$inv_type="";
        	foreach ($types as $value) {	$inv_type.=" t.type like '%".rtrim(ltrim($value," ")," ")."%' or ";	}        	
        	$criteria->addCondition("(  ".$inv_type."  t.type ='NON' ) ");        
        }

       // print_r($criteria);exit;
		if (isset($this->project_name) && $this->project_name != ""){	$criteria->compare('t.project_name', $this->project_name, true);	}
		if (isset($this->status) && $this->status != ""){	$criteria->compare('t.status', $this->status);	}
		else {	$criteria->addCondition('t.status IN ("'.Invoices::STATUS_PRINTED.'", "'.Invoices::STATUS_PAID.'", "'.Invoices::STATUS_CANCELLED.'")');	}		
		if (isset($this->partner_status) && $this->partner_status != ""){
			if ($this->partner_status== 'Not Paid' && (!(isset($this->status)) ||  $this->status == "")){
				$criteria->addCondition('t.partner_status="'.Receivables::PARTNER_STATUS_NOT_PAID.'" ');
			}
			if ($this->partner_status== 'Not Paid'){
				$criteria->addCondition("(t.partner_status is null or t.partner_status=' ' or t.partner_status='".Receivables::PARTNER_STATUS_NOT_PAID."') ");
			}
			else if (isset($this->status) && $this->status == "Paid"){
				$criteria->addCondition("(t.partner_status ='".$this->partner_status."'or t.partner_status=' ' or t.partner_status is null ) ");
			} else	{
				$criteria->compare('t.partner_status', $this->partner_status);
			}
		}
		if (isset($this->partner) && $this->partner != ""){
			if($this->partner == '202')	{		$criteria->compare('t.partner', '79');		
			}else{	$criteria->compare('t.partner', $this->partner); }
		}else{
        	$criteria->addCondition(" t.partner !='554' ");
        }

        if (isset($this->partner_inv) && $this->partner_inv != ""){
        	$criteria->addCondition("   ((( (t.old ='Yes' and t.partner='77') or  (t.old='No' and t.partner in ('77','78','1218','1336' ))  )  and t.partner_inv like '%".$this->partner_inv."%')  or (t.partner= '79' and t.span_partner_inv like '%".$this->partner_inv."%')  or  (  t.partner in ('201','554') and  t.snsapj_partner_inv like '%".$this->partner_inv."%'))");

        }


if (isset($this->invoice_date_year) && $this->invoice_date_year != ""){
        $criteria->compare('invoice_date_year', $this->invoice_date_year);
}
if (isset($this->invoice_date_month) && $this->invoice_date_month != ""){
        $criteria->compare('invoice_date_month', $this->invoice_date_month);
}
		if (isset($this->id_assigned) && $this->id_assigned != ""){	$criteria->compare('t.id_assigned', $this->id_assigned); }		
		$dataProvider = new CActiveDataProvider('Receivables', array(
				'criteria' => $criteria,
				'pagination'=>($group != null || $export) ? false : array(
						'pageSize' => 50,
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => $group ? 't.invoice_date_year DESC,t.invoice_date_month DESC, '.$group : ($export ? 'customer.name ASC' : 't.final_invoice_number ASC'),
           		 ),
		));
		return $dataProvider;
	}
	public function searchById($final_invoices_ids){
		$criteria = new CDbCriteria;	$criteria->with = array('customer','ea');	$criteria->addInCondition('final_invoice_number', $final_invoices_ids);
		return new CActiveDataProvider('Receivables', array(
				'criteria' => $criteria,
				'sort'=>array(
					'defaultOrder'=>'customer.name ASC ',
				),
		));
	}	
	public function primaryKey(){    return 'id'; }	
	public static function getAllUsserToAssign($list = false){
		$users = Yii::app()->db->createCommand("SELECT DISTINCT users.id, users.firstname, users.lastname 
			FROM users LEFT JOIN user_groups ON (user_groups.id_user = users.id) 
			WHERE users.active = 1 AND (user_groups.id_group = '".self::GROUP_recruitment."' OR user_groups.id_group = '".self::GROUP_office_admin."')")->queryAll();
		if ($list){
			$arr = array();
			foreach ($users as $k => $v){	$arr[$v['id']] = $v['firstname'].' '.$v['lastname'];	}
			$users = $arr;
		}
		return $users;
	}	
	public static function getPartnerStatus(){	return array('Paid' => 'Paid', 'Not Paid' => 'Not Paid'); }	
	public static function getAssignedto(){	return array(null=>'','11'=>'Claudine Daaboul', '40' => 'Irene Rabbah', '19'=>'Micheline Daaboul', '23' => 'Nadine Abboud');
	}
	public static function getPartner(){ return array('1336' => 'LOG CUBES','77' => 'SNS', '201' =>'SNS APJ', '1218' => 'SNS AUST', '78' => 'SNSI', '79' => 'SPAN', '202'=>'OSNS'); }
	public function getstatus(){	if ($this->status == 'Printed'){	return 'Not Paid';	}else{	return $this->status;	}	}
	public function getinvdate(){	return Yii::app()->db->createCommand('select  LAST_DAY(CONCAT('.$this->invoice_date_year.',"-",'.$this->invoice_date_month.',"-01"))')->queryScalar();	}
	public static function getEASerInvoice($eas)
	{
		if(!empty($eas))
		{
			$trs= explode(', ', $eas);
			$str='';
			foreach ($trs as $tr) {
				$str.=' <a class="show_link" href="'.Yii::app()->createUrl("eas/update", array("id" => $tr)).'"> '.Utils::paddingCode($tr).'</a>,';
			}
			$str= substr($str, 0, -1);
			return $str;
		}else{
			return '';
		}
		
	}
	public static function getlinkperInvoice($ids)
	{
		$trs= explode(', ', $ids);
		$str='';
		foreach ($trs as $tr) {
			$str.=' <a class="show_link" href="'.Yii::app()->createUrl("invoices/view", array("id" => $tr)).'"> '.Utils::paddingCode($tr).'</a>,';
		}
		$str= substr($str, 0, -1);
		return $str;
	}
}?>