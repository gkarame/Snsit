<?php
class TandMController extends Controller{
    public $layout = '//layouts/column1';
    public function filters(){
        return array(
            'accessControl',
            'postOnly + delete'
        );
    }    
    public function init(){
        parent::init();
        $this->setPageTitle(Yii::app()->name . ' - T&M');
    }
    public function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('generateTandM'
                ),
                'users' => array('*'
                )
            ),
            array(
                'allow',
                'actions' => array('index','view','create','update','delete','download','changeStatus','closeInputRate','generateInvoice','printTimesheet','updateHeader','changeInvoiceDate','printOne','print','checkPrint','checkStatus','InputRate','changeInputRate','printReceivables','generateTandM'
                ),
                'users' => array('@'),
                'expression' => '!$user->isGuest && isset($user->isAdmin) AND $user->isAdmin'
            ),
            array(
                'deny',
                'users' => array('*')
            )
        );
    }
    public function actionIndex(){
        if (!GroupPermissions::checkPermissions('general-tandm')) {
            throw new CHttpException(403, 'You don\'t have permission to access this page.');
        }
        $searchArray = isset($_GET['TandM']) ? $_GET['TandM'] : Utils::getSearchSession();        
        $this->action_menu          = array_map("unserialize", array_unique(array_map("serialize", array_merge($this->action_menu, array(
            '/tandM/index' => array(
                'label' => Yii::t('translations', 'T&M'),
                'url' => array('tandM/index'
                ),
                'itemOptions' => array('class' => 'link'
                ),
                'subtab' => -1,
                'order' => Utils::getMenuOrder() + 1,
                'search' => $searchArray
            )
        )))));
        Yii::app()->session['menu'] = $this->action_menu;        
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/scripts/jquery.blockUI.js', CClientScript::POS_END)->registerScriptFile(Yii::app()->baseUrl . '/scripts/jquery.cookie.js', CClientScript::POS_END);
        $model = new TandM('search');
        $model->unsetAttributes();
        if (empty($searchArray['tandm_month']) && !(isset($searchArray['tandm_month']))) {
            $now                        = date("m", strtotime("-1 months"));
            $searchArray['tandm_month'] = $now;
        }
        if (empty($searchArray['tandm_year']) && !(isset($searchArray['tandm_year']))) {
            $now                       = date("Y", strtotime("-1 months"));
            $searchArray['tandm_year'] = $now;
        }
        $model->attributes = $searchArray;        
        $this->render('index', array(
            'model' => $model
        ));
    }    
    public function actionInputRate(){
        if (isset($_POST['checktandm'])) {
            $id_tandms = $_POST['checktandm'];            
            $split      = explode(',', $id_tandms[0], 3);
            $id_project = $split[0];
            $month      = $split[1];
            $year       = $split[2];            
            $distinct_projects = array_unique($id_tandms);            
            if (count($distinct_projects) > 1) {                
                echo json_encode(array("status" => "failure",'message' => "You can't choose more than one record to set the rate."
                ));
                exit;                
            }
            $ids_projects = implode(',', $distinct_projects);            
            $rate_table      = "<table id=\"inputratetable\"> <tr><th>Project</th><th>Resource</th><th>Billable Hours</th><th>Rate</th><th>Add</th></tr>";
            $distinct_tandms = Yii::app()->db->createCommand("SELECT DISTINCT id, id_project , id_user ,ea_rate ,tandm_month ,tandm_year  from tandm where id_project=$id_project and tandm_month=$month and tandm_year=$year and (ea_rate is null or ea_rate=0) and id_user in (SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where  pt.billable='Yes' and pt.id_project_phase=pp.id and pp.id_project='" . $id_project . "') and MONTH(u.date)='" . $month . "' and YEAR(u.date)='" . $year . "'  and u.`default`=0) ")->queryAll();
            foreach ($distinct_tandms as $value) {
                $rate_table .= "<tr><td>" . Projects::getNameById($value['id_project']) . "</td><td>" . Users::getNameById($value['id_user']) . "</td><td><input type=text id=\"MD" . $value['id_user'] . "\" onchange=\"updateInput(this.id,this.value)\" value=\" " . TandM::getHoursByProjectbillablePerRes($value['id_project'], $value['tandm_month'], $value['tandm_year'], $value['id_user']) . "\" pattern=\"[0-9]+([,\.][0-9]+)?\"></td><td>" . TandM::getRateDropdown($value['id'], $value['id_project'], $value['ea_rate']) . "</td><td><input type=submit id=\"cmdAddRow\" value=\"+\" style=\"width:100%\" onclick=\"addrow(this)\"></td></tr>";
                $id_proj = $value['id_project'];
            }
            $rate_table .= "<tr><td><label id='warn_label'></label></td></tr></table>";
            if (!isset($id_proj)) {                
                echo json_encode(array("status" => "failure",'message' => "Rates has been already set for all the resources."
                ));
                exit;                
            }
            echo json_encode(array(
                "status" => "success",
                'rate_table' => $rate_table,
                'id_project' => $id_proj,
                'month' => $month,
                'year' => $year
            ));
            exit;
        } else {            
            echo json_encode(array(
                "status" => "failure"
            ));
            exit;            
        }
    }    
    public function actiongenerateTandM(){        
        $tandms = Yii::app()->db->createCommand("select distinct id_user , pp.id_project , e.ea_number from  user_task u , projects_tasks p , projects_phases pp ,projects po ,eas e where u.id_task=p.id and p.id_project_phase=pp.id and pp.id_project=po.id and po.status<>'2' and po.id=e.id_project and e.TM=1 ")->queryAll();
        foreach ($tandms as $value) {            
            $getexits = Yii::app()->db->createCommand("SELECT Count(*) from tandm where id_user='" . $value['id_user'] . "' and id_project='" . $value['id_project'] . "' and tandm_month= MONTH(CURRENT_DATE()) and tandm_year=YEAR(CURRENT_DATE()) and ea_number='" . $value['ea_number'] . "'")->queryScalar();
            if ($getexits < 1) {
                $insert_tandm = Yii::app()->db->createCommand("Insert into tandm (id_user, id_project , tandm_month , tandm_year , status ,ea_number ) values ('" . $value['id_user'] . "' , '" . $value['id_project'] . "' , MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE()) , 'New' ,'" . $value['ea_number'] . "') ")->execute();
            }
        }        
    }
    public function actioncloseInputRate(){        
        if (isset($_POST['type']) && $_POST['type'] == 1) {
            if (isset($_POST['id_project'])) {                
                $id_project = $_POST['id_project'];
            }
            if (isset($_POST['month'])) {                
                $month = $_POST['month'];
            }
            if (isset($_POST['year'])) {                
                $year = $_POST['year'];
            }
            $countrates = Yii::app()->db->createCommand("SELECT count(1)  from tandm where id_project=$id_project and tandm_month=$month and tandm_year=$year and (ea_rate is null or ea_rate=0)  and id_user in (SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='" . $id_project . "') and MONTH(u.date)='" . $month . "' and YEAR(u.date)='" . $year . "') ")->queryScalar();
            if ($countrates > 0) {
                echo json_encode(array("status" => "failure",'message' => "Please make sure that all the rates has been set otherwise all the changes will be discarded. Are you sure you want to close ?"
                ));
                exit;                
            } else {                
                echo json_encode(array("status" => "success"
                ));
                exit;
            }            
        }        
        if (isset($_POST['type']) && $_POST['type'] == 2) {  
            if (isset($_POST['id_project'])) {                
                $id_project = $_POST['id_project'];
            }
            if (isset($_POST['month'])) {                
                $month = $_POST['month'];
            }
            if (isset($_POST['year'])) {                
                $year = $_POST['year'];
            }            
            $reset_rate = Yii::app()->db->createCommand("update tandm set ea_rate=0 where id_project=$id_project and tandm_month=$month and tandm_year=$year ")->execute();
            echo json_encode(array(
                "status" => "success"                
            ));
            exit;            
        } 
    }
    public function actionChangeInputRate(){
        $warn      = 0;
        $nr        = 0;
        $md        = (float) ($_POST['MD']);
        $md        = Utils::formatNumber((float) ($md / 8), 2);
        $id        = (int) $_POST['id_tandm'];
        $value     = $_POST['value'];
        $currtandM = Yii::app()->db->createCommand("SELECT id_project , id_user ,ea_rate ,tandm_month ,tandm_year  from tandm where id=$id")->queryRow();
        $totalmd   = TandM::getMDsByProjectbillablePerRes($currtandM['id_project'], $currtandM['tandm_month'], $currtandM['tandm_year'], $currtandM['id_user']);
        $currntRate = $currtandM['ea_rate'];
        if (empty($currntRate) || $currntRate == 0) {
            if (!((float) $md > (float) $totalmd)) {
                $nr = Yii::app()->db->createCommand("UPDATE tandm SET ea_rate = '$value' WHERE id ='$id' ")->execute();
            } else {
                $warn = 1;
            }
        } else {
            if (!((float) $md > (float) $totalmd) && ((float) $totalmd - (float) $md) >= 0 && ((float) $totalmd - (float) $md <= $totalmd)) {
                $othermd = (float) $totalmd - (float) $md;
                $x       = $othermd * (float) $currntRate;
                $y       = (float) $md * (float) $value;
                $newRate = ($x + $y) / (float) $totalmd;                
                print_r($newRate . ' the newRate'); 
                $nr = Yii::app()->db->createCommand("UPDATE tandm SET ea_rate = '$newRate' WHERE id ='$id' ")->execute();
            } else {
                $warn = 1;
            }
        }        
        if ($nr != 0) {
            echo json_encode(array_merge(array(
                'status' => 'success'
            )));            
            exit;
        } else if ($warn == 1) {
            echo json_encode(array_merge(array(
                'status' => 'warning'
            )));
            exit;
        } else {
            echo json_encode(array_merge(array(
                'status' => 'fail',
                'message' => 'Error'
            )));
            exit;            
        }        
    }
    public function loadModel($id){
        $model = TandM::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }    
    public function actiongenerateInvoice(){        
        if (isset($_POST['checktandm'])) {
            $id_tandms = $_POST['checktandm'];            
            foreach ($id_tandms as $value) {                
                $split      = explode(',', $value, 3);
                $id_project = $split[0];
                $month      = $split[1];
                $year       = $split[2];                
                $now       = new \DateTime('now');
                $currmonth = $now->format('m');                
                if ((int) $month == (int) $currmonth) {echo json_encode(array(    "status" => "failure",    'message' => "You cannot generate invoice before end of the month."    ));exit;
                }
                if (TandM::checkAllpendingTimesheet($id_project, $month, $year)) {echo json_encode(array(    "status" => "failure",    'message' => "You cannot generate invoice. Resources in red have unsubmitted timesheets."    ));exit;
                }            
                $countrates = Yii::app()->db->createCommand("SELECT count(1)  from tandm where id_project=$id_project and tandm_month=$month and tandm_year=$year and (ea_rate is null or ea_rate=0) and id_user in (SELECT distinct id_user FROM user_time u WHERE u.amount>0 and u.id_task in (select pt.id from projects_tasks pt , projects_phases pp where pt.id_project_phase=pp.id and pp.id_project='" . $id_project . "') and MONTH(u.date)='" . $month . "' and YEAR(u.date)='" . $year . "')  ")->queryScalar();
                if ($countrates > 0) {echo json_encode(array(    "status" => "failure",    'message' => "Please input the rate for all the resources before generating the invoices."    ));exit;
                }
                $customer    = Projects::getCustomerByProject($id_project);
                $projectname = Projects::getNameById($id_project);
                $ea          = Projects::getEAids($id_project);                
                $amount      = TandM::getAmountByProject($id_project, $month, $year);
                $currencyy   = Eas::getIdCurrency($customer, $id_project);
                $currency    = $currencyy['currency'];
                $checkStatus = Yii::app()->db->createCommand("select count(1) from tandm  where status='Invoiced' and id_project=$id_project and tandm_month=$month and tandm_year=$year ")->queryScalar();
                if ($checkStatus == 0) {$changestatus = Yii::app()->db->createCommand("update tandm set status='Invoiced' where id_project=$id_project and tandm_month=$month and tandm_year=$year ")->execute();self::CreateInvoice($customer, $projectname, $id_project, $ea, $amount, $currency, $month, $year);
                } else {echo json_encode(array(    "status" => "failure",    'message' => "Invoice has been already generated for this month."    ));exit;
                }
            }
            echo json_encode(array(
                "status" => "success"
            ));
            exit;
        }
    }
    public function actiondelete(){        
        if (isset($_POST['checktandm'])) {
            $id_tandms = $_POST['checktandm'];            
            foreach ($id_tandms as $value) {                
                $split      = explode(',', $value, 3);
                $id_project = $split[0];
                $month      = $split[1];
                $year       = $split[2];
                $stat       = "OK";                
                $time = TandM::getAmounttimeByProject($id_project, $month, $year);               
                if (isset($time) && $time != 0) {$stat = "NOTOK";echo json_encode(array(    "status" => "failure",    'message' => "You cannot delete record having timesheet submitted against it."    ));exit;
                } else {Yii::app()->db->createCommand("delete from tandm where id_project='" . $id_project . "' and tandm_month='" . $month . "' and tandm_year='" . $year . "' ")->execute();
                }
            }            
            if ($stat == "OK") {                
                echo json_encode(array("status" => "success"
                ));
                exit;
            }
        }
    }
    public function createInvoice($customer, $projectname, $id_project, $ea, $amount, $currency, $month, $year){
        $payment_number = 1;
        $i              = 1;
        $title_name     = "";        
        $title_name            = "";
        $model                 = new Invoices();
        $model->invoice_number = "00000";
        $model->id_customer    = $customer;
        $model->project_name   = $projectname;
        $model->id_project     = $id_project;
        $cardinal_number       = Eas::cardinalNumber($i);        
        $id_assign          = Customers::getIdAssigned($customer);
        $model->id_assigned = $id_assign;        
        if ($payment_number == 1) {
            $title = $projectname . " - 100% Payment for the month of " . Utils::formatDate($month, "!m", "F") . " " . $year;
        }
        $model->invoice_title = $title;
        $model->id_ea         = $ea;
        $model->payment       = '1/1';
        $region               = Yii::app()->db->createCommand("SELECT region from customers where id=" . $customer . " ")->queryScalar();
        $country              = Yii::app()->db->createCommand("SELECT country from customers where id=" . $customer . " ")->queryScalar();
        $category             = Eas::getCategoryById($ea);
        $model->sns_share     = 100;
        if ($region == '59') {
            if ($category == '27' || $category == '28') {
                if ($country == '113' || $country == '115') {$model->partner = Maintenance::PARTNER_SNS;
                } else {$model->partner   = '79';$model->sns_share = 80;
                } 
            } else {
                $model->partner = Maintenance::PARTNER_SNS;                
            }
        } elseif ($region == '63') { 
		        $model->partner   = '201';
		        $model->sns_share = 80;          
        } else {
            $model->partner   = Maintenance::PARTNER_SNSI;
            $model->sns_share = 80;
        }
        $model->payment_procente   = "100";
        if($country=='398' ){ 
			$amountAUST    = TandM::getAmountByProjectPerUser($id_project, $month, $year, '16');
			if( $amountAUST >0)
			{
				$amount= $amount - $amountAUST;
				self::createInvoiceAUST($customer, $projectname, $id_project, $ea, $amountAUST, $currency, $month, $year);
			}
		}
		if($amount>0)
		{
	        $net_amount                = $amount;
	        $model->amount             = $net_amount;
	        $model->net_amount         = $net_amount * ($model->sns_share / 100);
	        $model->gross_amount       = $net_amount;
	        $model->partner_amount     = $net_amount * (1 - 100 / 100);
	        $model->sold_by            = "";
	        $model->type               = "T&M";
	        $model->invoice_date_month = $month;
	        $model->invoice_date_year  = $year;
	        $model->currency           = $currency;
	        $model->status             = INVOICES::STATUS_TO_PRINT;        
	        if ($model->save()) {
	            $model->invoice_number = Utils::paddingCode($model->id);
	            if ($model->invoice_number == "99999")
	                $model->invoice_number = "00000";
	            $model->save();            
	            $notif = EmailNotifications::getNotificationByUniqueName('tandm_generate_invoice');            
	            Yii::app()->mailer->ClearAddresses();
	            if ($notif != NULL) {                
	                $subject    = 'T&M Invoice Generated';
	                $to_replace = array('{inv_number}'
	                );
	                $replace = array($model->invoice_number
	                );                
	                $body = str_replace($to_replace, $replace, $notif['message']);                
	                $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
	                Yii::app()->mailer->ClearAddresses();                
	                foreach ($emails as $email) {if (!empty($email))    Yii::app()->mailer->AddAddress($emails);
	                }
	                Yii::app()->mailer->Subject = $subject;
	                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
	                if (Yii::app()->mailer->Send(true)) {
	                }                
	            }
	        }
	    }
    }   
    public function createInvoiceAUST($customer, $projectname, $id_project, $ea, $amount, $currency, $month, $year){
    	$payment_number = 1;
        $i              = 1;
        $title_name     = "";        
        $title_name            = "";
        $model                 = new Invoices();
        $model->invoice_number = "00000";
        $model->id_customer    = $customer;
        $model->project_name   = $projectname;
        $model->id_project     = $id_project;
        $cardinal_number       = Eas::cardinalNumber($i);        
        $id_assign          = Customers::getIdAssigned($customer);
        $model->id_assigned = $id_assign;        
        if ($payment_number == 1) {
            $title = $projectname . " - 100% Payment for the month of " . Utils::formatDate($month, "!m", "F") . " " . $year;
        }
        $model->invoice_title = $title;
        $model->id_ea         = $ea;
        $model->payment       = '1/1';
        $model->partner   = '1218';
		$model->sns_share = 40;     
        $model->payment_procente   = "100";
        $net_amount                = $amount;
        $model->amount             = $net_amount;
        $model->net_amount         = $net_amount * ($model->sns_share / 100);
        $model->gross_amount       = $net_amount;
        $model->partner_amount     = $net_amount * (1 - 100 / 100);
        $model->sold_by            = "";
        $model->type               = "T&M";
        $model->invoice_date_month = $month;
        $model->invoice_date_year  = $year;
        $model->currency           = $currency;
        $model->status             = INVOICES::STATUS_TO_PRINT;        
        if ($model->save()) {
            $model->invoice_number = Utils::paddingCode($model->id);
            if ($model->invoice_number == "99999")
                $model->invoice_number = "00000";
            $model->save();            
            $notif = EmailNotifications::getNotificationByUniqueName('tandm_generate_invoice');            
            Yii::app()->mailer->ClearAddresses();
            if ($notif != NULL) {                
                $subject    = 'T&M Invoice Generated';
                $to_replace = array('{inv_number}'
                );
                $replace = array($model->invoice_number
                );                
                $body = str_replace($to_replace, $replace, $notif['message']);                
                $emails = EmailNotificationsGroups::getNotificationUsers($notif['id']);
                Yii::app()->mailer->ClearAddresses();                
                foreach ($emails as $email) {if (!empty($email))    Yii::app()->mailer->AddAddress($emails);
                }
                Yii::app()->mailer->Subject = $subject;
                Yii::app()->mailer->MsgHTML("<div style='font-size:11pt;font-family:Calibri;'>" . nl2br($body) . "</div>");
                if (Yii::app()->mailer->Send(true)) {
                }                
            }
        }
    } 
    public function actionprintTimesheet(){ 
        if (isset($_GET['checktandm'])) {
            $id_tandms = $_GET['checktandm'];
            $split      = explode(',', $id_tandms[0], 3);
            $id_project = $split[0];
            $month      = $split[1];
            $year       = $split[2];
            $customer   = Projects::getCustomerByProject($id_project);
            $select = "select
			 p.customer_id, 
			 c.name as customer_name, 
			 p.name as project_name, 
			 CONCAT_WS(' ', firstname, lastname) AS username,
			  pp.id_project ,
			  ut.id_user , 
			  pt.id as description ,
			  ut.comment  ,
			   ut.date , 
			SUM(ut.amount) AS amount ,pt.billable
			 from user_time ut";
            $select .= " LEFT JOIN projects_tasks pt ON ( ut.id_task = pt.id) 
			 LEFT JOIN  projects_phases pp ON ( pt.id_project_phase = pp.id) 
			 LEFT JOIN  projects p ON ( p.id = pp.id_project)
			 LEFT JOIN  eas e ON (p.id=e.id_project)
			 LEFT JOIN customers c ON c.id = p.customer_id
			 LEFT JOIN users u ON u.id = ut.id_user";            
            $where = "WHERE ut.default=0";
            $where .= " AND e.TM=1";
            $where .= " AND p.customer_id = '{$customer}'";
            $where .= " AND p.id ='{$id_project}'";
            $where .= " AND pt.billable='Yes'";
            $where .= " AND ut.date >= '" . $year . "-" . $month . "-01 00:00:00' ";
            $where .= " AND ut.date <= '" . $year . "-" . $month . "-31 00:00:00' ";            
            $groupBy = "GROUP BY p.customer_id, pp.id_project, p.name, ut.id_user, pt.id, ut.comment, ut.date having SUM(ut.amount)>0  ";
            $order   = "ORDER BY ut.date ASC";            
            $timesheetSummary = Yii::app()->db->createCommand($select . " " . $where . " " . $groupBy . "  ORDER BY DATE desc")->queryAll();            
            self::createExcel($timesheetSummary);
        }        
    }    
    public function CreateExcel($resp, $profit = null){
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');
        $objPHPExcel = XPHPExcel::createPHPExcel();
        $objPHPExcel->getProperties()->setCreator("Seve Alex")->setLastModifiedBy("Seve Alex")->setTitle("Office 2007 XLSX Test Document")->setSubject("Office 2007 XLSX Test Document")->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")->setKeywords("office 2007 openxml php")->setCategory("Test result file");
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('PHPExcel logo');
        $objDrawing->setDescription('PHPExcel logo');
        $objDrawing->setPath(dirname(Yii::app()->request->scriptFile) . '/images/logo_pdf.png'); // filesystem reference for the image file
        $objDrawing->setHeight(36); // sets the image height to 36px (overriding the actual image height); 
        $objDrawing->setCoordinates('A1'); // pins the top-left corner of the image to cell D24
        $objDrawing->setOffsetX(10); // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
        $styleArray = array(
            'font' => array(
                'italic' => false,
                'bold' => true
            ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );        
        $styleArray1 = array(
            'font' => array(
                'italic' => false,
                'bold' => false
            ),
            'borders' => array(
                'bottom' => array('color' => array(    'argb' => '11666739')
                ),
                'top' => array('color' => array(    'argb' => '11666739')
                ),
                'right' => array('color' => array(    'argb' => '11666739')
                )
            )
        );        
        $styleLeft = array(
            'font' => array(
                'italic' => false,
                'bold' => true
            ),
            'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '11666739')
                )
            )
        );        
        $sheetId = 0;
        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray);
        $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . '4', 'Customer Name')->setCellValue('B' . '4', 'Project Name')->setCellValue('C' . '4', 'Resource Name')->setCellValue('D' . '4', 'Phase')->setCellValue('E' . '4', 'Task')->setCellValue('F' . '4', 'Description')->setCellValue('G' . '4', 'Date')->setCellValue('H' . '4', 'Hours')->setCellValue('I' . '4', 'Billable');
        $ct = 5;
        $nb = sizeof($resp);          
        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');         
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleArray); 

        foreach ($resp as $key => $tim) {
            $customer_id = $tim['customer_id'];
            $project_id  = $tim['id_project'];
            $user        = $tim['username'];
            $phase       = $tim['customer_id'] == '177' ? $tim['description'] : ProjectsTasks::getPhaseDescByid($tim['description']);
            $task        = $tim['customer_id'] == '177' ? $tim['description'] : ProjectsTasks::getTaskDescByid($tim['description']);
            $comment     = $tim['comment'];
            $date        = $tim['date'];
            $amount      = Utils::formatNumber($tim['amount']);
            $billable    = $tim['billable'];            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $ct . ':I' . $ct)->applyFromArray($styleArray1);            
            $objPHPExcel->setActiveSheetIndex($sheetId)->setCellValue('A' . $ct, Customers::getNameById($customer_id))->setCellValue('B' . $ct, Projects::getNameById($project_id))->setCellValue('C' . $ct, $user)->setCellValue('D' . $ct, $phase)->setCellValue('E' . $ct, $task)->setCellValue('F' . $ct, $comment)->setCellValue('G' . $ct, Utils::formatDate($date))->setCellValue('H' . $ct, $amount)->setCellValue('I' . $ct, $billable);
            $ct = $ct + 1;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Projects Reports');        
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="TimeSheet_Report.xls"');
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
}?>