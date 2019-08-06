<?php
class ReceivablesController extends Controller{
    public $layout = '//layouts/column1';
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete'
        );
    }    
    public function init(){
        parent::init();
        $this->setPageTitle(Yii::app()->name . ' - Receivables');
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('sendPatnerPaid','sendPrinted','sendReceivablesMonthlySummary','SendReceivablesMonthlySummaryAdmins'),
                'users' => array('*')
            ),
            array(
                'allow',
                'actions' => array('index','view','update','updatePartnerInv','changeStatusPaid','changeStatusSNSPaid','getExcel','getReport','updateHeader','share','getSoA','getUsers','assignUsers'),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny', 
                'users' => array('*'
                )
            )
        );
    }    
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('financial-receivables')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $searchArray = isset($_GET['Receivables']) ? $_GET['Receivables'] : Utils::getSearchSession();        
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/receivables/index' => array(
                'label' => Yii::t('translations', 'Receivables'),
                'url' => array('receivables/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        $model = new Receivables('searchReceivablesGr');        
        $model->unsetAttributes();
        $model->attributes = $searchArray;
        $this->render('index', array(
            'model' => $model
        ));
    }    
    public function actionView($id){
        if (!GroupPermissions::checkPermissions('financial-receivables')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $model = $this->loadModel($id);        
        $title = 'Invoice #' . $model->final_invoice_number . ' - ' . Customers::getNameById($model->id_customer);
        $arr   = Utils::getShortText($title);        
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/receivables/view/' . $id => array(
                'label' => $arr['text'],
                'url' => array('receivables/view','id' => $id
                ),
                'itemOptions' => array('class' => 'link','title' => $arr['shortened'] ? $title : ''
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;
        $this->render('view', array(
            'model' => $model
        ));
    }
    public function actionchangeStatusSNSPaid(){
        if (!isset($_POST['checkinvoice'])) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => ' You have to select at least one invoice!'
            ));
            exit;
        }
        $invoices_ids = $_POST['checkinvoice'];
        $valid_ids    = array();
        if (empty($invoices_ids)) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select SNS invoices!'
            ));
            exit;
            
        }
        $ids_invoices = '';
        foreach ($invoices_ids as $id) {            
            $ids_invoices .= $id . ',';
        }
        $ids_invoices .= '0';
        $criteria            = new CDbCriteria;
        $criteria->condition = "status != '" . Invoices::STATUS_PAID . "' and invoice_number in (" . $ids_invoices . ") ";
        $criteria->order     = "invoice_number ASC";
        $invoices            = Receivables::model()->findAll($criteria);
        if (empty($invoices)) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select Not Paid SNS invoices!'
            ));
            exit;           
        }        
        $criteria            = new CDbCriteria;
        $criteria->condition = "(id_assigned is null or id_assigned =0) and invoice_number in (" . $ids_invoices . ") ";
        $criteria->order     = "invoice_number ASC";
        $checkassigned       = Receivables::model()->findAll($criteria);
        if (!empty($checkassigned)) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to set Assigned To!'
            ));
            exit;            
        }
        $toSendEmail = array();
        foreach ($invoices as $key => $inv) {
            $valid_ids[]   = $inv->final_invoice_number;
            $toSendEmail[] = array(
                'final_invoice_number' => $inv->final_invoice_number,
                'gross_amount' => $inv->gross_amount,
                'currency' => $inv->rCurrency->codelkup,
                'customer' => $inv->customer->name
            );
        }        
        if (!empty($valid_ids)) {
            Yii::app()->db->createCommand("UPDATE `invoices`
				SET status = '" . Invoices::STATUS_PAID . "' , paid_date= CURRENT_DATE()
				WHERE partner=77 and final_invoice_number IN (\"" . implode("\",\"", $valid_ids) . "\"); ")->execute();
            Yii::app()->db->createCommand("UPDATE `invoices`
				SET status = '" . Invoices::STATUS_PAID . "' , sns_paid_date= CURRENT_DATE()
				WHERE partner!=77 and partner_status='" . Invoices::STATUS_PAID . "'  and final_invoice_number IN (\"" . implode("\",\"", $valid_ids) . "\"); ")->execute();
            echo json_encode(array(
                'status' => 'success',
                'paid_date' => date('d/m/Y')
            ));
            exit;
        }        
        echo json_encode(array(
            'status' => 'success',
            'no_change' => true
        ));
        exit;
    }
    public function actionupdatePartnerInv()
    {
    	$value = $_POST['value'];
    	$finalinv = $_POST['final'];
    	$partner = $_POST['partner'];
    	$old = $_POST['old'];
    	 if(isset($finalinv) && !empty($finalinv))
    	{
    		if ($old=='No'){ $where="where final_invoice_number= '".$finalinv."' ";}
			else{ $where="where old_sns_inv= '".$finalinv."' ";}
			$str='';
			 $str=Yii::app()->db->createCommand("update invoices set span_partner_inv='".$value."' ".$where."")->execute();	
			  echo CJSON::encode(array(
                'status' => 'success',
            ));
            exit;
    	}
    }
    public function actionChangeStatusPaid(){
        if (!isset($_POST['checkinvoice'])) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => ' You have to select at least one invoice!'
            ));
            exit;
        }        
        $invoices_ids = $_POST['checkinvoice'];
        if(count($invoices_ids ) == 0)
        {
        	echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select invoices!'
            ));
            exit;
        }
        $error=false;
        foreach ($invoices_ids as $value) {
        	$criteria            = new CDbCriteria;
	        $criteria->condition = "partner_status != '" . Invoices::STATUS_PAID . "' and partner!=77 and invoice_number in (" . $value . ") and (id_assigned is null or id_assigned =0) and invoice_number in (" . $value . ") ";
	        $checkassigned   = Receivables::model()->findAll($criteria);
       		if(empty($checkassigned))
       		{
       			$nr= Yii::app()->db->createCommand("UPDATE `invoices`
				SET partner_status = '" . Invoices::STATUS_PAID . "', paid_date=CURRENT_DATE()
				WHERE partner !=77 and partner_status != '" . Invoices::STATUS_PAID . "'  and  invoice_number in (" . $value . ") ;")->execute();
       		}else{
       			$error=true;
       		}
        }
		if ($error) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to set Assigned To!'
            ));
            exit;            
        }else if ($nr > 0) {
        	 echo json_encode(array(
                'status' => 'success',
                'paid_date' => date('d/m/Y')
            ));
            exit;
        }else{
        	echo CJSON::encode(array(
                'status' => 'fail',
                'message' => 'You have to select not paid partner invoices!'
            ));
            exit;
        }
    }
    public function actionSendReceivablesMonthlySummary(){
        $monday = strtotime('first day of last month');
        $sunday = strtotime('last day of last month');
        $start_datetime  = date('Y-m-d 00:00:00', $monday);
        $finish_datetime = date('Y-m-d 23:59:59', $sunday);        
        $start_date  = date('Y-m-d', $monday);
        $finish_date = date('Y-m-d', $sunday);
        $list1 = '<b>Total Amount Collected:</b> ' . Utils::formatNumber(Receivables::gettotalNetpaiddates($start_date, $finish_date), 2) . '$<br/><br/> <b>Monthly Payments Average:</b> ' . Utils::formatNumber(Receivables::getMonthlyAvg($finish_date), 2) . '$';
        $list2 = ' <br/><b>Total Amount Per Resource:</b> <br/>' . Receivables::gettotalNetRes($start_date, $finish_date);
        $list3 = '<b>Top Monthly Performer:</b> <br/>' . Receivables::gettopMonthPerf($start_date, $finish_date);
        $list4 = '<b>Top Yearly Performer:</b> <br/>' . Receivables::gettopYearPerf($start_date) . '<br/>';
        $notif = EmailNotifications::getNotificationByUniqueName('receivables_weekly_performance');
        if ($notif != NULL) {
            $subject = $notif['name'];            
            $to_replace = array(
                '{start_date}',
                '{end_date}',
                '{list1}',
                '{list2}',
                '{list3}',
                '{list4}'
            );
            $replace    = array(
                date('l jS \o\f F', strtotime($start_datetime)),
                date('l jS \o\f F', strtotime($finish_datetime)),
                $list1,
                $list2,
                $list3,
                $list4
            );
            $body       = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL))Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
        }        
        echo $body;
    }    
    public function actionSendReceivablesMonthlySummaryAdmins(){
         $monday = strtotime('first day of last month');
        $sunday = strtotime('last day of last month');
        $start_datetime  = date('Y-m-d 00:00:00', $monday);
        $finish_datetime = date('Y-m-d 23:59:59', $sunday);
        $start_date  = date('Y-m-d', $monday);
        $finish_date = date('Y-m-d', $sunday);

        $start_year  =  date("Y-m-d", strtotime('first day of January '.date('Y') ));
        $finish_year = date('Y-m-d', strtotime('last day of december this year'));

        $mon = date('M', $monday);
        $list1 = '<b>Total Amount Collected this Month:</b> ' . Utils::formatNumber(Receivables::gettotalNetpaiddates($start_date, $finish_date), 2) . '$ <br/><br/><b>Total Amount Collected this Year:</b> ' . Utils::formatNumber(Receivables::gettotalNetpaiddates($start_year, $finish_year), 2) . '$<br/><br/> <b>Monthly Payments Average:</b> ' . Utils::formatNumber(Receivables::getMonthlyAvg($finish_date), 2) . '$';
        $list2 = '<br/><b>Total Pending Amount:</b> ' . Utils::formatNumber(Receivables::gettotalnotpaidandAllInv(), 2) . '$<br/><br/> <b>' . $mon . ' Average Age:</b> ' . Utils::formatNumber(Receivables::getAgeFactor($finish_date), 2) . ' Days';
        $list3 = '<br/><b>Improvement Amount %:</b> ' . Utils::formatNumber(Receivables::getImprovFactor($start_date, $finish_date), 2) . ' %';
        $list4 = '<br/><b>Improvement Age %:</b> ' . Utils::formatNumber(Receivables::getAgeImprovFactor($finish_date), 2) . ' %<br/>';
        $notif = EmailNotifications::getNotificationByUniqueName('receivables_weekly_performance_admins');
        if ($notif != NULL) {
            $subject = $notif['name'];            
            $to_replace = array(
                '{start_date}',
                '{end_date}',
                '{list1}',
                '{list2}',
                '{list3}',
                '{list4}'
            );
            $replace    = array(
                date('l jS \o\f F', strtotime($start_datetime)),
                date('l jS \o\f F', strtotime($finish_datetime)),
                $list1,
                $list2,
                $list3,
                $list4
            );
            $body       = str_replace($to_replace, $replace, $notif['message']);            
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL))
                {	Yii::app()->mailer->AddAddress($email); 
				}
            }
		//	Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com"); 
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);
        }        
        echo $body;
    }
    public function actionsendPrinted()
    {
    	$part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('past_invoices_printed');
        if ($notif != NULL) 
        {
            $users= Yii::app()->db->createCommand("select distinct(id_assigned) as id_assigned from receivables WHERE printed_date>= (CURRENT_DATE() - INTERVAL 1 MONTH) and id_assigned is not null")->queryAll();
            if(!empty($users))
            {
            	
	            foreach ($users as $user) {
						$to_replace = array(
					'{group_description}',
					'{body}'
					);
					$invoices   = 'Dear '.Users::getfirstNameById($user['id_assigned']).', <br/ > <br/ > ';
					$i          = 1;
					$str        = '';
				  //  $total      = '';
					$total2     = '';
					
	            	 $models     = Yii::app()->db->createCommand("select old, case when final_invoice_number is null then (case when (old_sns_inv is null or old_sns_inv='') then snsapj_partner_inv else old_sns_inv end ) else final_invoice_number end final_invoice_number,
						case when partner in  ('201','554','79') then (case when partner='79' then span_partner_inv else snsapj_partner_inv end ) else partner_inv end partner_inv, partner,  (select name from customers where id=id_customer) customer, 	invoice_title
						from invoices where printed_date>= (CURRENT_DATE() - INTERVAL 1 MONTH) and id_assigned =".$user['id_assigned']." and old = 'No' and final_invoice_number is not null and final_invoice_number!='' group by final_invoice_number
					UNION 
						select  old,case when final_invoice_number is null then (case when (old_sns_inv is null or old_sns_inv='') then snsapj_partner_inv else old_sns_inv end ) else final_invoice_number end final_invoice_number,
						case when partner in  ('201','554','79') then (case when partner='79' then span_partner_inv else snsapj_partner_inv end ) else partner_inv end partner_inv, partner, (select name from customers where id=id_customer) customer, 	invoice_title
						from invoices where printed_date>= (CURRENT_DATE() - INTERVAL 1 MONTH) and id_assigned =".$user['id_assigned']." and old = 'Yes' group by old_sns_inv
					UNION
						select old, case when final_invoice_number is null then partner_inv else final_invoice_number end final_invoice_number, partner_inv, partner,  (select name from customers where id=id_customer) customer, 	invoice_title
						from invoices where printed_date>= (CURRENT_DATE() - INTERVAL 1 MONTH) and id_assigned =".$user['id_assigned']." and partner=1218 and old = 'No' and (final_invoice_number is null or final_invoice_number='') and partner_inv is not null group by partner_inv")->queryAll();
		            if (empty($models)) {
		                $invoices .= 'No invoices were printed in the past month.<br>';
		            } else {
		                $str = '(';
		                $invoices .= 'Please find below the invoices printed in the past month:<br><br><table border="1"  style="font-family:Calibri;border-collapse: collapse;" ><tr><th>  Customer  </th><th>  Final Invoice #  </th><th>  Partner  </th><th>  Partner Invoice #  </th><th style="width:40px;">  Old  </th><th>  Title  </th></tr>';
		                foreach ($models as $model) {
		                	$invoices .= '<tr><td>' . $model['customer'] . '</td><td>' . $model['final_invoice_number'] . '</td><td>' . Codelkups::getCodelkup($model['partner']) . '</td><td>' . $model['partner_inv'] . '</td><td>' . $model['old'] . '</td><td>' . $model['invoice_title'] . '</td></tr>';
		                }
		                $invoices .= '</table>';
		            }
		            $invoices .= '<br/> Best Regards, <br/ > SNSit';
		            $emailuser= Users::getEmailbyID($user['id_assigned']); //$invoices .= $emailuser;
		            $subject = $notif['name'];
		            $replace = array(
		                EmailNotificationsGroups::getGroupDescription($notif['id']),
		                $invoices
		                
		            );
		            $body    = str_replace($to_replace, $replace, $notif['message']);
		            print_r($body);
		            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
		            Yii::app()->mailer->ClearAddresses();
		           // Yii::app()->mailer->AddAddress("houda.nasser@sns-emea.com");
		            Yii::app()->mailer->AddAddress($emailuser);
		            foreach ($emails as $email) {
		                Yii::app()->mailer->AddAddress($email);
		            }
		            Yii::app()->mailer->Subject = $subject;
		            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
		            Yii::app()->mailer->Send(true);  
	            }
	        }                     
        }
    }
    public function actionsendPatnerPaid(){        
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('invoices_paid');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $invoices   = '';
            $i          = 1;
            $str        = '';
          //  $total      = '';
            $total2     = '';
            $models     = Yii::app()->db->createCommand("
select case when final_invoice_number is null then (case when (old_sns_inv is null or old_sns_inv='') then snsapj_partner_inv else old_sns_inv end ) else final_invoice_number end final_invoice_number,partner_status, invoice_number, id_customer, sum(gross_amount) as gross_amount,currency, send_email from invoices where  old= 'No' and (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77)) and paid_date=CURRENT_DATE() and final_invoice_number is not null and final_invoice_number !='' group by final_invoice_number
union select old_sns_inv as final_invoice_number ,partner_status, invoice_number, id_customer, sum(gross_amount) as gross_amount,currency, send_email from invoices where old= 'Yes' and (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77)) and paid_date=CURRENT_DATE() group by old_sns_inv
union select case when final_invoice_number is null then partner_inv else final_invoice_number end final_invoice_number,partner_status, invoice_number, id_customer, sum(gross_amount) as gross_amount,currency, send_email from invoices where  old= 'No' and   partner_status ='Paid' and Partner=1218 and (final_invoice_number is null or final_invoice_number='') and paid_date=CURRENT_DATE() group by partner_inv
")->queryAll();
            $currencies = Yii::app()->db->createCommand("select distinct(currency) from invoices 
				where (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77)) and paid_date=CURRENT_DATE()")->queryAll();
            if (empty($models)) {
                $invoices .= 'No invoices were paid today.<br>';
            } else {
                $str = '(';
                $invoices .= 'The invoices below are paid:<br><br>';
                foreach ($models as $model) {$customer = Customers::getNameById($model['id_customer']);$currency = Codelkups::getCodelkup($model['currency']);$invoices .= $i++ . '- Invoice #' . $model['final_invoice_number'] . ' - ' . $customer;$invoices .= ' - ' . Utils::formatNumber($model['gross_amount']) . ' ' . $currency;$invoices .= '<br>';$str .= " '" . $model['invoice_number'] . "', ";
                }
                $str .= "'0')";
               // foreach ($currencies as $currencie) {$sum  = Yii::app()->db->createCommand("select SUM(net_amount) from invoices where currency='" . $currencie['currency'] . "' and (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77))  and paid_date=CURRENT_DATE()")->queryRow();$sum1 = number_format($sum['SUM(net_amount)'], 2);$total .= '- ' . $sum1 . ' ' . Codelkups::getCodelkup($currencie['currency']) . '<br />';
               // }
                foreach ($currencies as $currencie) {$sum  = Yii::app()->db->createCommand("select SUM(gross_amount) from invoices where currency='" . $currencie['currency'] . "' and (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77))  and paid_date=CURRENT_DATE()")->queryRow();$sum1 = number_format($sum['SUM(gross_amount)'], 2);$total2 .= '- ' . $sum1 . ' ' . Codelkups::getCodelkup($currencie['currency']) . '<br />';
                }
               // $invoices .= '<br /> <b>Total Net Amount:<br /> ' . $total . '</b>';
                $invoices .= '<br /> <b>Total Amount:<br /> ' . $total2 . '<br /></b>';
                $run = Yii::app()->db->createCommand("UPDATE invoices SET send_email ='" . date('Y-m-d') . "' WHERE (( partner_status ='Paid' and Partner!=77) OR (Status='Paid' and Partner=77))  and paid_date=CURRENT_DATE()")->execute();
            }
            $subject = $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $invoices
                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
            print_r($body);
            $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            Yii::app()->mailer->Send(true);            
        }
    }
    public function sendNotificationEmail($models){
        $part  = array();
        $notif = EmailNotifications::getNotificationByUniqueName('invoices_paid');
        if ($notif != NULL) {
            $to_replace = array(
                '{group_description}',
                '{body}'
            );
            $invoices = '';
            $i        = 1;
            $invoices .= 'The invoices below are paid:<br>';
            foreach ($models as $model) {
                $invoices .= $i++ . '- Invoice #' . $model['final_invoice_number'] . ' - ' . $model['customer'];
                $invoices .= ' - ' . Utils::formatNumber($model['gross_amount']) . ' ' . $model['currency'];
                $invoices .= '<br>';
            }
            $subject = $notif['name'];
            $replace = array(
                EmailNotificationsGroups::getGroupDescription($notif['id']),
                $invoices
                
            );
            $body    = str_replace($to_replace, $replace, $notif['message']);
            $emails  = EmailNotificationsGroups::getNotificationUsers($notif['id']);
            Yii::app()->mailer->ClearAddresses();
            foreach ($emails as $email) {
                Yii::app()->mailer->AddAddress($email);
            }
            Yii::app()->mailer->Subject = $subject;
            Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
            if (Yii::app()->mailer->Send(true)) {
                return true;
            }
        }
        return false;
    }
    public function actionGetUsers(){
        $users = Receivables::getAllUsserToAssign();
        echo CJSON::encode(array(
            'status' => 'success',
            'div' => $this->renderPartial('_assign_users', array(
                'users' => $users
            ), true)
        ));
        exit;
    }    
    public function actionAssignUsers(){
        if (!GroupPermissions::checkPermissions('financial-receivables', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        if (!isset($_POST['checked'], $_POST['checkinvoice'])) {
            exit;
        }
        $id       = (int) $_POST['checked']; 
        $invoices = $_POST['checkinvoice'];
        if ($id > 0 && ($count = count($invoices)) > 0 && ($username = Users::getUsername($id)) != '') {
            if ($count > 1) {
                foreach ($invoices as  $value) {
                //	print_r("UPDATE invoices SET id_assigned = '$id' WHERE invoice_number IN (" . $value . ")");exit;
                $nr   =	Yii::app()->db->createCommand("UPDATE invoices SET id_assigned = '$id' WHERE invoice_number IN (" . $value . ")")->execute();
                } 
            } else {
                $inv_numbers = $invoices[0];
                $nr          = Yii::app()->db->createCommand("UPDATE invoices SET id_assigned = '$id' WHERE invoice_number in (" . $inv_numbers . ") ")->execute();
            }
            if ($nr > 0) {
                echo CJSON::encode(array('status' => 'success','message' => 'The user ' . Users::getUsername($id) . ' have been assigned!'
                ));
                exit;
            }
        }        
        echo CJSON::encode(array(
            'status' => 'error',
            'message' => 'An error has occured or the user is already assigned. Please try again later!'
        ));
        exit;
    }
    public function actiongetReport(){ 
        $data = Receivables::getReportData();        
        $totalaging    = 0;
        $totalagingavg = 0;
        $totalamount   = 0;        
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('Error PHP Excel extension');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")->setLastModifiedBy("http://www.sns-emea.com")->setTitle("SNS Invoices Export");
        $sheetId = 0;

        $nb = sizeof($data);         
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

       $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray); 

       


        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A1', 'Customer')->setCellValue('B1', 'Sum of Remaining($)')->setCellValue('C1', 'Max of Age')->setCellValue('D1', 'Average of Age')->setCellValue('E1', 'Action')->setCellValue('F1', 'Assigned');
        $i = 1;
        foreach ($data as $d => $row) {
            $total = $row['tot'];
            $totalamount += $total;
            $total = number_format($total, 2);
            $totalaging += $row['maxage'];
            $totalagingavg += $row['avgage'];
            $management = Users::getNameById(Customers::getIdAssigned($row['id_customer']));            
            $i++;
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $i, isset($row['id_customer']) ? Customers::getNameById($row['id_customer']) : '')->setCellValue('B' . $i, $total)->setCellValue('C' . $i, $row['maxage'])->setCellValue('D' . $i, $row['avgage'])->setCellValue('E' . $i, '')->setCellValue('F' . $i, $management);
        }
        $countro     = count($data);
        $totalamount = number_format($totalamount, 2);
        $i++;
        $i++;
        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('B' . $i, $totalamount)->setCellValue('C' . $i, number_format(($totalaging / $countro), 2))->setCellValue('D' . $i, number_format(($totalagingavg / $countro), 2));
        $objPHPExcel->getActiveSheet()->setTitle('Report # - ' . date("d m Y"));
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->createSheet();        
        $data = Receivables::getReportDataosns();        
        $sheetId = 1;
         $objPHPExcel->setActiveSheetIndex(1);
        $nb = sizeof($data);         
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

       $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray); 

        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A1', 'Customer')->setCellValue('B1', 'Sum of Remaining')->setCellValue('C1', 'Max of Age')->setCellValue('D1', 'Average of Age')->setCellValue('E1', 'Action')->setCellValue('F1', 'Assigned');
        $i = 1;
        foreach ($data as $d => $row) {
            $total = Receivables::gettotalnotpaidosns($row['id_customer']);
            $totalamount += $total;
            $total = number_format($total, 2);
            $totalaging += $row['maxage'];
            $totalagingavg += $row['avgage'];
            $i++;
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $i, isset($row['id_customer']) ? Customers::getNameById($row['id_customer']) : '')->setCellValue('B' . $i, $total)->setCellValue('C' . $i, $row['maxage'])->setCellValue('D' . $i, $row['avgage'])->setCellValue('E' . $i, '')->setCellValue('F' . $i, Users::getNameById($row['id_assigned']));
        }
        $totalamount = number_format($totalamount, 2);
        $i++;
        $i++;
        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('B' . $i, $totalamount)->setCellValue('C' . $i, $totalaging)->setCellValue('D' . $i, $totalagingavg);
        $objPHPExcel->getActiveSheet()->setTitle('Report OSNS# - ' . date("d m Y"));
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Report_' . date("d_m_Y") . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
        header('Cache-Control: cache, must-revalidate'); 
        header('Pragma: public');         
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }    
    public function actionGetExcel(){
      //  $model = new Receivables('getAll');
        //$data  = $model->getAll(null, true)->getData();

//print_r($_POST['Receivables']);exit;
$group = NULL; $export = false;
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



		if(!empty($_POST['Receivables']['final_invoice_number']) && $_POST['Receivables']['final_invoice_number']!='' &&  $_POST['Receivables']['final_invoice_number']!=' ')
		{
			$criteria->compare('final_invoice_number',$_POST['Receivables']['final_invoice_number'], true);
		}
		if(!empty($_POST['Receivables']['id_customer']) && $_POST['Receivables']['id_customer']!='' &&  $_POST['Receivables']['id_customer']!=' ')
		{
			$criteria->compare('customer.name', $_POST['Receivables']['id_customer'], true);
		}
		if(!empty($_POST['Receivables']['old']) && $_POST['Receivables']['old']!='' &&  $_POST['Receivables']['old']!=' ')
		{
			$criteria->compare('t.old', $_POST['Receivables']['old']);
		}
		if(!empty($_POST['Receivables']['id_ea']) && $_POST['Receivables']['id_ea']!='' &&  $_POST['Receivables']['id_ea']!=' ')
		{
			$criteria->compare('ea.ea_number', $_POST['Receivables']['id_ea'], true);
		}		 
		if(!empty($_POST['Receivables']['age']) && $_POST['Receivables']['age']!='' &&  $_POST['Receivables']['age']!=' ')
		{	if ($_POST['Receivables']['age'] == -30)	{	$criteria->addCondition("DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) < 30");
			}else {	$criteria->addCondition("DATEDIFF(CURDATE(), LAST_DAY(CONCAT(t.invoice_date_year,'-',t.invoice_date_month,'-01'))) >=". $_POST['Receivables']['age']);
			}
		}
		if (isset($_POST['Receivables']['type']) && !empty($_POST['Receivables']['type']) && $_POST['Receivables']['type']!=' ' ){         	
			$types=$_POST['Receivables']['type'];		$inv_type="";
        	foreach ($types as $value) {	$inv_type.="'%".rtrim(ltrim($value," ")," ")."%' or ";	}        	
        	$criteria->addCondition("( t.type like ".$inv_type."  t.type ='NON' ) ");        
        }
		if (isset($_POST['Receivables']['project_name']) && $_POST['Receivables']['project_name'] != ""){	
			$criteria->compare('t.project_name', $_POST['Receivables']['project_name'], true);	
		}
		if (isset($_POST['Receivables']['status']) && $_POST['Receivables']['status'] != ""){	
			$criteria->compare('t.status', $_POST['Receivables']['status']);	}
		else {	$criteria->addCondition('t.status IN ("'.Invoices::STATUS_PRINTED.'", "'.Invoices::STATUS_PAID.'", "'.Invoices::STATUS_CANCELLED.'")');	}		
		
		if (isset($_POST['Receivables']['partner_status']) && $_POST['Receivables']['partner_status'] != ""){
			if ($_POST['Receivables']['partner_status'] == 'Not Paid' && (!(isset($_POST['Receivables']['status'])) ||  $_POST['Receivables']['status'] == "")){
				$criteria->addCondition('t.partner_status="'.Receivables::PARTNER_STATUS_NOT_PAID.'" ');
			}
			if ($_POST['Receivables']['partner_status'] == 'Not Paid'){
				$criteria->addCondition("(t.partner_status is null or t.partner_status=' ' or t.partner_status='".Receivables::PARTNER_STATUS_NOT_PAID."') ");
			}
			else if (isset($_POST['Receivables']['status']) && $_POST['Receivables']['status'] == "Paid"){
				$criteria->addCondition("(t.partner_status ='".$_POST['Receivables']['partner_status']."'or t.partner_status=' ' or t.partner_status is null ) ");
			} else	{
				$criteria->compare('t.partner_status', $_POST['Receivables']['partner_status']);
			}
		}
		if (isset($_POST['Receivables']['partner']) && $_POST['Receivables']['partner'] != ""){
			if($_POST['Receivables']['partner'] == '202')	{		$criteria->compare('t.partner', '79');		
			}else{	$criteria->compare('t.partner', $_POST['Receivables']['partner']); }
		}else{
        	$criteria->addCondition(" t.partner !='554' ");
        }

        if (isset($_POST['Receivables']['partner_inv']) && $_POST['Receivables']['partner_inv'] != ""){
        	$criteria->addCondition("   ((( (t.old ='Yes' and t.partner='77') or  (t.old='No' and t.partner in ('77','78','1218','1336' ))  )  and t.partner_inv like '%".$_POST['Receivables']['partner_inv']."%')  or (t.partner= '79' and t.span_partner_inv like '%".$_POST['Receivables']['partner_inv']."%')  or  (  t.partner in ('201','554') and  t.snsapj_partner_inv like '%".$_POST['Receivables']['partner_inv']."%'))");

        }


if (isset($_POST['Receivables']['invoice_date_year']) && $_POST['Receivables']['invoice_date_year'] != ""){
        $criteria->compare('invoice_date_year', $_POST['Receivables']['invoice_date_year']);
}
if (isset($_POST['Receivables']['invoice_date_month']) &&$_POST['Receivables']['invoice_date_month'] != ""){
        $criteria->compare('invoice_date_month', $_POST['Receivables']['invoice_date_month']);
}
		if (isset($_POST['Receivables']['id_assigned']) && $_POST['Receivables']['id_assigned'] != ""){	$criteria->compare('t.id_assigned',$_POST['Receivables']['id_assigned']); }		
		$dataProvider = new CActiveDataProvider('Receivables', array(
				'criteria' => $criteria,
				'pagination'=>($group != null || $export) ? false : array(
						'pageSize' => 50000,
				),
				'sort'=>array( 
               		'attributes' => array(
						$group,  
					),
               		'defaultOrder' => $group ? 't.invoice_date_year DESC,t.invoice_date_month DESC, '.$group : ($export ? 'customer.name ASC' : 't.final_invoice_number ASC'),
           		 ),
		));


 $data  = $dataProvider->getData();

        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('Error PHP Excel extension');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("http://www.sns-emea.com")->setLastModifiedBy("http://www.sns-emea.com")->setTitle("SNS Invoices Export");
        $sheetId = 0;

        $nb = sizeof($data);         
        $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            )); 

       $objPHPExcel->getActiveSheet()->getStyle('A1:X1')->applyFromArray($styleArray); 


        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A1', 'Customer')->setCellValue('B1', 'Invoice')->setCellValue('C1', 'EA #')->setCellValue('D1', 'Invoice Title')->setCellValue('E1', 'Partner')->setCellValue('F1', 'Partner Inv')->setCellValue('G1', 'Currency')->setCellValue('H1', 'Foreign Currency')->setCellValue('I1', 'Gross Amount')->setCellValue('J1', 'Foreign Gross Amount')->setCellValue('K1', 'Net Amount')->setCellValue('L1', 'Foreign Net Amount')->setCellValue('M1', 'Partner Status')->setCellValue('N1', 'Status')->setCellValue('O1', 'Age')->setCellValue('P1', 'Invoice Date')->setCellValue('Q1', 'Paid Date')->setCellValue('R1', 'Old')->setCellValue('S1', 'Payment')->setCellValue('T1', 'Percent')->setCellValue('U1', 'SNS Share')->setCellValue('V1', 'Remarks')->setCellValue('W1', 'Notes')->setCellValue('X1', 'Assigned To');
        $i = 1;
        foreach ($data as $d => $row) {
            $net   = Receivables::getAmountUsd($row->rCurrency['id'], $row->net_amount);
            if(!empty($net))
            {
            	$net= round($net, 2);
            }
            $gross = Receivables::getAmountUsd($row->rCurrency['id'], $row->gross_amount);
            if(!empty($gross))
            {
            	$gross=round($gross,2);
            }
            $final = $row->final_invoice_number;
            if ($row->partner == '554') {
                $final = $row->snsapj_partner_inv;
            }
            if ($row->partner == '79' && isset($row->span_partner_inv)) {
                $pinv = $row->span_partner_inv;
            } else if($row->partner == '201' && !isset($row->partner_inv)) {
                $pinv = $row->snsapj_partner_inv;
            }else{
            	$pinv = $row->partner_inv;
            }

            $i++;
            $objPHPExcel->getActiveSheet()->getStyle('I' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->getStyle('J' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->getStyle('K' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$objPHPExcel->getActiveSheet()->getStyle('L' . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $i, isset($row->customer->name) ? $row->customer->name : '')
                     ->setCellValue('B' . $i, $final)->setCellValue('C' . $i, isset($row->ea->ea_number) ? $row->ea->ea_number : "")
                     ->setCellValue('D' . $i, $row->invoice_title)->setCellValue('E' . $i, $row->partner ? $row->rPartner->codelkup : '')->setCellValue('F' . $i, $pinv)
					->setCellValue('G' . $i, 'USD')->setCellValue('H' . $i, ($row->currency!=9)? Codelkups::getCodelkup($row->currency): "")
					->setCellValue('I' . $i, $gross)->setCellValue('J' . $i, ($row->currency!=9)? round($row->gross_amount,2): "")
					->setCellValue('K' . $i, $net)->setCellValue('L' . $i, ($row->currency!=9)? round($row->net_amount,2): "")
					->setCellValue('M' . $i, $row->partner != '77' ? $row->partner_status : '')->setCellValue('N' . $i, ($row->status == 'Printed') ? "Not Paid" : $row->status)
					->setCellValue('O' . $i, $row->age)->setCellValue('P' . $i, date("d/m/Y", strtotime($row->getinvdate())))
					->setCellValue('Q' . $i, ($row->paid_date != '0000-00-00') ? date("d/m/Y", strtotime($row->paid_date)) : '')
					->setCellValue('R' . $i, $row->old)->setCellValue('S' . $i, $row->payment)->setCellValue('T' . $i, $row->payment_procente . "%")
					->setCellValue('U' . $i, $row->sns_share . ' %')->setCellValue('V' . $i, $row->remarks)->setCellValue('W' . $i, $row->notes)
					->setCellValue('X' . $i, (Users::getUsername($row->id_assigned)));
        }
		$objPHPExcel->getActiveSheet()->setTitle('Invoices # - ' . date("d m Y"));
        $objPHPExcel->setActiveSheetIndex(0);
         header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SupportDesk.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
		header ('Cache-Control: cache, must-revalidate'); 
		header ('Pragma: public');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$path = dirname(Yii::app()->request->scriptFile)."/uploads/excel/export.xls";
		$objWriter->save($path);
		echo json_encode(array ('success' =>'success'));
		exit;
    }
    public function actionUpdateHeader($id){
        if (!GroupPermissions::checkPermissions('financial-receivables', 'write')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }        
        $model = $this->loadModel($id);  
        if (isset($_POST['Receivables'])) {
            $str = $model->partner_status;
            if($model->partner_status ='Not Paid' && $_POST['Receivables']['status'] == 'Paid'  && $model->partner !='77'  && $model->partner !='SNS'){
				if ($_POST['Receivables']['partner_status'] == 'Not Paid' ){
					echo json_encode(array(
					'status'=>'failure',				
					));
					Yii::app()->end();
				}
			}
            $set = array();
            $f   = 0;
            if (($model->partner == '77'))
                {
                $model->status = Yii::app()->db->createCommand("SELECT status from receivables where id=" . $id . " ")->queryScalar();                
            } else {
                $model->partner_status = Yii::app()->db->createCommand("SELECT partner_status from receivables where id=" . $id . " ")->queryScalar();
            }
            if (((!isset($_POST['Receivables']['id_assigned']) || empty($_POST['Receivables']['id_assigned'])) && ($model->id_assigned == 0 || empty($model->id_assigned))) && ((($_POST['Receivables']['status'] == Invoices::STATUS_PAID || $model->Status == Invoices::STATUS_PAID) && ($model->partner == '77')) || (($_POST['Receivables']['partner_status'] == Invoices::STATUS_PAID || $model->partner_status == Invoices::STATUS_PAID || $f == 1) && ($model->partner != '77')))) {
               echo json_encode(array('status' => 'failureAssigned'//'html'=>$this->renderPartial('_edit_header_content', array('model'=> $model), true, true)
                ));
                Yii::app()->end();
            }
            if ($_POST['Receivables']['partner_status'] == Invoices::STATUS_PAID && $model->partner != '77' && $model->partner != 'SNS') {
                if ($str == 'Not Paid') {$toSendEmail[] = array(    'final_invoice_number' => $model->final_invoice_number,    'gross_amount' => $model->gross_amount,    'currency' => $model->rCurrency->codelkup,    'customer' => $model->customer->name);//$this->sendNotificationEmail($toSendEmail);
                }                
            }
            if ($_POST['Receivables']['status'] == Invoices::STATUS_PAID && ($model->partner == '77' || $model->partner == 'SNS')) {
                $toSendEmail[] = array('final_invoice_number' => $model->final_invoice_number,'gross_amount' => $model->gross_amount,'currency' => $model->rCurrency->codelkup,'customer' => $model->customer->name
                );
            } 
            $d     = new DateTime("now", new DateTimeZone("Asia/Beirut"));
            $today = $d->format('Y-m-d');
            if ($model->partner_status == 1 && (empty($model->paid_date) || $model->paid_date == '0000-00-00')) {
                $f = 1;                
            }
            foreach ($_POST['Receivables'] as $key => $value) {
                $set[] = $key . ' = "' . $value . '"';
            }            
            if ($_POST['Receivables']['status'] == Invoices::STATUS_PAID && ($model->partner == '77') && $model->status != Invoices::STATUS_PAID) {
                $set[] = "paid_date='" . $today . "' ";
            } else if ($_POST['Receivables']['status'] == Invoices::STATUS_PAID && ($model->partner != '77') && $model->status != Invoices::STATUS_PAID) {
                $set[] = "sns_paid_date='" . $today . "' ";
            } else if ($_POST['Receivables']['partner_status'] == Invoices::STATUS_PAID && ($model->partner != '77') && ($model->partner_status != Invoices::STATUS_PAID || $f == 1)) {
                $set[] = "paid_date='" . $today . "' ";
            }            
            if (!empty($set)) {
                $set_items = implode(',', $set);
            }
            $assignHr = explode('id_assigned = "', $set_items);
            $assignHr = explode('"', $assignHr[1]);
            $assignHr = $assignHr[0];
            if ($model->partner == '554') {
                $where = " WHERE invoice_number = '" . $model->invoice_number . "' ";
            } else if ($model->partner == '1218') {
                $where = " WHERE partner= 1218 and partner_inv = '" . $model->partner_inv . "' ";
            } else if ($model->old == 'No' && !empty($model->final_invoice_number)) {
                $where = " WHERE final_invoice_number = '" . $model->final_invoice_number . "' ";
            } else if ($model->old == 'No') {
                $where = " WHERE invoice_number = '" . $model->invoice_number . "' ";
            } else {
                $where = " WHERE old_sns_inv = '" . $model->final_invoice_number . "' ";
            }
			//print_r($model->partner."UPDATE `invoices` SET {$set_items} " . $where . " ");exit;
            Yii::app()->db->createCommand("UPDATE `invoices` SET {$set_items} " . $where . " ")->execute(); 

          /*  if($_POST['Receivables']['status'] == 'Cancelled' and !empty($model->id_ea))
			{
				$getremaininginv= Yii::app()->db->createCommand("SELECT count(1) from invoices where id_ea =".$model->id_ea." and status!= 'Cancelled' ")->queryScalar();
				if($getremaininginv == 0)
				{
					Yii::app()->db->createCommand("UPDATE eas SET status=0  WHERE id =".$model->id_ea." ")->execute();
				}
			}*/


            $nr             = Yii::app()->db->createCommand("UPDATE `invoices` SET id_assigned='" . $assignHr . "' WHERE id_customer='" . $model['id_customer'] . "' ")->execute();
            $updatecustomer = Yii::app()->db->createCommand("UPDATE `customers` SET id_assigned='" . $assignHr . "' WHERE id='" . $model['id_customer'] . "' ")->execute();
            $model->refresh();
            echo json_encode(array(
                'status' => 'saved',
                'html' => $this->renderPartial('_header_content', array('model' => $model
                ), true, true)
            ));
            Yii::app()->end();
        }        
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
            'jquery-ui.min.js' => false
        );
        echo json_encode(array(
            'status' => 'success',
            'html' => $this->renderPartial('_edit_header_content', array(
                'model' => $model
            ), true, true)
        ));
        Yii::app()->end();
    }    
    public function actionShare()
    {
        if (!isset($_POST['checkinvoice'])) {
            echo CJSON::encode(array(
                'status' => 'fail',
                'message' => ' You have to select at least one invoice!'
            ));
            exit;
        } else {            
            $model     = new ShareByForm;
            $ids       = $_POST['checkinvoice'];
            $items     = array();
            $files     = array();
            $filenames = array();            
            if (is_array($ids)) {
                $cust         = Yii::app()->db->createCommand("SELECT id_customer from invoices where invoice_number in (" . $ids[0] . ")")->queryScalar();
                $bill         = Yii::app()->db->createCommand("SELECT bill_to_contact_email, bill_to_contact_person,id_assigned FROM `customers` WHERE id='" . $cust . "'")->queryRow();

                $billemail    = $bill['bill_to_contact_email'];
               
                $name         = explode(" ", $bill['bill_to_contact_person']);
                $billname     = "Dear " . $name[0] . ",";
                $criteria     = new CDbCriteria;
                $ids_invoices = '';
                foreach ($ids as $id) {$ids_invoices .= $id . ','; }
                $ids_invoices .= '0';
                $criteria->condition = "invoice_number in (" . $ids_invoices . ") ";
                $items               = Invoices::model()->findAll($criteria);                
                foreach ($items as $item) {$inv = $item->getFilePrinted2(true);if ($inv != null && !in_array($inv, $files)) {    array_push($files, $inv);    array_push($filenames, basename($inv));}
                }
                if (isset($_POST['ShareByForm'])) {
                	$model->attributes = $_POST['ShareByForm'];
                	if ($model->validate() && !empty($files)) {  
                		$model->to  = "Micheline.Daaboul@sns-emea.com,".$model->to;
                		$emailrep= Users::getEmailbyID($bill['id_assigned']);
		                if(!empty($emailrep))
		                {
		                	 $model->to  = $emailrep.",".$model->to;
		                }
                		$emails_to      = explode(',', $model->to);   
                		$emails_invalid = array();    $emails_valid   = array();    
                		$validator      = new CEmailValidator;   
                		foreach ($emails_to as $em) {        
                			$email = '';       
                			 if (trim($em)) {         
                			 	   $arr = explode('<', $em);         
                			 	     if (count($arr) == 2) {            
                			 	         $to                         = array();          
                			 	         $to[substr($arr[1], 0, -1)] = $arr[0];               
                			 	        if ($validator->validateValue(substr($arr[1], 0, -1))) {                    $email = $to;                }            } else {                if ($validator->validateValue($em)) {                    $email = $em;                }            }            if (!empty($email)) {                $emails_valid[] = $email;            } else {                $emails_invalid[] = $em;            }        }    }        if (!empty($emails_valid)) {        Yii::app()->mailer->ClearAddresses();        foreach ($emails_valid as $email) {            Yii::app()->mailer->AddAddress($email);        }                Yii::app()->mailer->Subject = 'Pending Invoice(s)';        $msj                        = $model->header . '<br/><br/>' . $model->body . '<br/><br/>' . $model->footer;        Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($msj) . "</div>");                foreach ($files as $file) {            Yii::app()->mailer->AddFile($file);        }                if (count($model->soa) > 0) {            foreach ($items as $item) {                $id_customer = $item->id_customer;                $SoAFile     = Documents::model()->findByAttributes(array(                    'id_model' => $id_customer,                    'model_table' => 'customers',                    'id_category' => 5                ), array(                    'order' => 'id DESC',                    'limit' => 1                ));                                if (is_array($SoAFile) && !empty($SoAFile)) {                    $soa = $SoAFile->getFile(true);                    if (!is_null($soa)) {                        Yii::app()->mailer->AddFile($soa);                    }                }            }        }                $sent = Yii::app()->mailer->Send(true);                echo json_encode(array(            "status" => "success",            'sent' => $sent,            'not_sent_to' => implode(',', $emails_invalid)        ));        exit;    }}
                }                
                $form = $this->renderPartial('share', array('model' => $model,'filenames' => $filenames,'billemail' => $billemail,'billname' => $billname
                ), true, true);
                echo json_encode(array("status" => "failure","form" => $form,"file_found" => (isset($_POST['ShareByForm']) && empty($files)) ? 0 : 1
                ));
                exit;
            }
        } 
    }    
    public function actionGetSoA(){
        if (isset($_POST['checkinvoice'])) {
            $criteria     = new CDbCriteria;
            $ids          = $_POST['checkinvoice'];
            $ids_invoices = '';
            foreach ($ids as $id) {
                $ids_invoices .= $id . ',';
            }
            $ids_invoices .= '0';
            $criteria->condition = "invoice_number in (" . $ids_invoices . ") ";
            $invoices  = Invoices::model()->findAll($criteria);
            $files     = '';
            $ids_files = array();
            $customers = array();
            if (!empty($invoices)) {
                foreach ($invoices as $invoice) {if (isset($invoice->id_customer) && !in_array($invoice->id_customer, $customers)) {    $customers[] = $invoice->id_customer;    $lastFile    = Documents::model()->findByAttributes(array(        'id_model' => $invoice->id_customer,        'model_table' => 'customers',        'id_category' => 5    ), array(        'order' => 'id DESC',        'limit' => 1    ));    if ($lastFile !== null && file_exists($lastFile->getFile(true))) {        $ids_files[] = $lastFile->id;        $files .= '<span class="attachm soaFile" data-id="' . $lastFile->id . '">' . $lastFile->file . '</span>';    }}
                }
                echo json_encode(array('file' => $files,'idfile' => $ids_files
                ));
            }
        }
        exit;
    }
    public function loadModel($id){
        $model = Receivables::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
}?>