<?php 
class Utils{
	public static function getReadableFileSize($size, $retstring = null){
        $sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($retstring === null) { $retstring = '%01.2f %s'; }
        $lastsizestring = end($sizes);
        foreach ($sizes as $sizestring) {
	        if ($size < 1024) {  break; }
    	    if ($sizestring != $lastsizestring){   $size /= 1024; }
        }
        if ($sizestring == $sizes[0]) {	$retstring = '%01d %s'; } 
        return sprintf($retstring, $size, $sizestring);
	}        
	public static function addAttachments($statename, $path) {		
	    if (Yii::app( )->user->hasState( $statename )) {	    	
	        $attachments = Yii::app( )->user->getState( $statename );
	        if( !is_dir( $path ) ) {  mkdir( $path , 0777, true); chmod( $path, 0777 );	 }
	        foreach ($attachments as $attach ) {
	            if (is_file( $attach["path"] ) ) {
	                if (rename( $attach["path"], $path.$attach["name"] )) { chmod( $path.$attach["name"], 0777 );  }
	            } 
	        }
	    	Yii::app( )->user->setState( $statename, null );
	    }
	}	
	public static function emptyDirectory($dirname,$self_delete=false) {
		if (is_dir($dirname)){	$dir_handle = opendir($dirname);	}
		if (!isset($dir_handle) || $dir_handle == false){	return false;	}
		while($file = readdir($dir_handle)){
			if ($file != "." && $file != ".."){
				if (!is_dir($dirname."/".$file)){
					@unlink($dirname."/".$file);
				}else{
					Utils::emptyDirectory($dirname.'/'.$file, true);
				}
			}
		}
		closedir($dir_handle);
		if ($self_delete){	@rmdir($dirname);	}
		return true;
	}	
	public static function deleteSearchFile($dirname, $name){
		if (is_dir($dirname)){	$dir_handle = opendir($dirname);	}
		if (!isset($dir_handle) || $dir_handle == false){	return false;	}
		while($file = readdir($dir_handle)){	
			echo pathinfo($dirname."/".$file, PATHINFO_FILENAME);
			if ($file != "." && $file != ".."){
				if (!is_dir($dirname."/".$file) && pathinfo($dirname."/".$file, PATHINFO_FILENAME) == $name){
					echo $dirname."/".$file;
					@unlink($dirname."/".$file);
					return true;
				}
			}
		}
		closedir($dir_handle);
		return false;
	}
	public static function deleteFile($file){
		if (file_exists($file) && is_file($file)){
			if ($file != "." && $file != "..") {
				@unlink($file);
				return true;
			}
		}
		return false;
	}
	public static function getPagerArray(){
		return array(
			'header'         => '',
	        'firstPageLabel' => '',
	        'prevPageLabel'  => '',
	        'nextPageLabel'  => '',
	        'lastPageLabel'  => '',
			'cssFile'		 => false,
			'maxButtonCount' => 5,	
		);
	}	
	public static function getPageSize(){
		$query = 'SELECT value from system_parameters where system_parameter = "page_size"';
		return Yii::app()->db->createCommand($query)->queryScalar();
	}
  public static function dateDiff($time1, $time2, $precision = 6) 
  {	  	date_default_timezone_set("UTC");
	  	if (!is_int($time1)) {	$time1 = strtotime($time1);    }
	    if (!is_int($time2)) {	$time2 = strtotime($time2);    }
	    if ($time1 > $time2) {	$ttime = $time1;	$time1 = $time2;	$time2 = $ttime;   }
	    $intervals = array('year','month','day','hour');    $diffs = array();
	    foreach ($intervals as $interval) {
	      	$diffs[$interval] = 0;     	$ttime = strtotime("+1 " . $interval, $time1);
	      	while ($time2 >= $ttime){
				$time1 = $ttime;	$diffs[$interval]++;
				$ttime = strtotime("+1 " . $interval, $time1);
	      	}
	    }	 
	    $count = 0;    $times = array();
	    foreach ($diffs as $interval => $value) {
	      	if ($count >= $precision){	break;  }
	      	if ($value > 0){
				if ($value != 1){  $interval .= "s";	}
				$times[] = $value . " " . $interval;	$count++;
	      	}
	    }
	    return implode(", ", $times);
	}
	public static function getShortText($text, $max_chars = 18){
        $txt = strip_tags($text);      $len = strlen($txt); 
        if ($len <= $max_chars){
            return array('text' => $txt, 'shortened' => false, 'left' => '');
        }
        return array('text' => substr($txt, 0, $max_chars), 'shortened' => true, 'left' => substr($txt, -($len-$max_chars))); 
 	}
 	public static function getRelativeUrl($url){
 		$baseurl = Yii::app()->request->getBaseUrl(true);	$baseUrl = str_replace(Yii::app()->request->getHostInfo(), "", $baseurl);
		$url_arr = explode('?', str_replace($baseUrl, "", $url));
		return $url_arr[0];
 	} 	
 	public static function getSearchSession(){
		$baseurl = Yii::app()->request->getBaseUrl(true);
		$baseUrl = str_replace(Yii::app()->request->getHostInfo(), "", $baseurl);
		$url = str_replace($baseUrl, "", Yii::app()->request->url);		
		if (@isset(Yii::app()->session['menu'][$url]['search'])){
			return Yii::app()->session['menu'][$url]['search'];
		}
		return array();
 	}	
 	public static function getMenuOrder($tab = false){
		if (isset(Yii::app()->session['menu'])){
			$arr = Yii::app()->session['menu'];
			uasort($arr, function($a, $b) {
				if (isset($b['order'], $a['order']))
			    	return $b['order'] - $a['order'];
			    return 0;
			});
			$first = reset($arr);
			if ($tab){
				return key($arr);
			}else{
				$last_order = $first['order'];
				return $last_order;
			}
		}
		if ($tab){
			return Yii::app()->createUrl('site/index');	
		}else{
			return 0;
		}
	}	
 	public static function closeTab($url, $params = false){
 		if (isset(Yii::app()->session['menu']) && !empty($url)){
			$menu = Yii::app()->session['menu'];
			$keys = array_keys($menu);
			if(!$params){
				$url = Utils::getRelativeUrl($url);
			}else{
				$baseurl = Yii::app()->request->getBaseUrl(true);
				$baseUrl = str_replace(Yii::app()->request->getHostInfo(), "", $baseurl);
				$url = str_replace($baseUrl, "", $url);
			}
			unset($menu[$url]);
			Yii::app()->session['menu'] = $menu;
		}
		return array(
			'status' => 'success',
			'tab' => Utils::getMenuOrder(true)
		);
 	}
 	public static function formatNumber($number, $decimal_no = 3, $decimal_char = ".", $thousand_char = ","){
 		$number = (float) $number;	$decimal_no = (int) $decimal_no;
 		return  rtrim(rtrim(number_format($number, $decimal_no, $decimal_char, $thousand_char), '0'), $decimal_char);
 	} 	
 	public static function paddingCode($number){
 		return str_pad($number, 5, "0", STR_PAD_LEFT);
 	}
 	public static function paddingCodeSmall($number){
 		return str_pad($number, 3, "0", STR_PAD_LEFT);
 	}
 	public static function convert_number_to_words($number,$currency = null){
 		if($currency == null)
	    	$decimal     = 'US Dollars';
	    else
	   		$decimal     = $currency;
	   	
	   	$result = self::recursive_convert_number_to_words($number);
	   	$position = strpos($result, "point"); 
	   	if ($position !== FALSE){
	   		return str_replace("point", $decimal.' and ', $result). ' Cents Only';
	   	}else{
	   		return $result.' '.$decimal.' and No Cents Only';
	   	}
 	}
	public static function recursive_convert_number_to_words($number) {
	    $hyphen      = ' ';    $conjunction = ' ';	    $separator   = ', ';    $negative    = 'negative ';
	    $decimal	 = " point ";
	    $dictionary  = array(
	        0                   => 'Zero',
	        1                   => 'One',
	        2                   => 'Two',
	        3                   => 'Three',
	        4                   => 'Four',
	        5                   => 'Five',
	        6                   => 'Six',
	        7                   => 'Seven',
	        8                   => 'Eight',
	        9                   => 'Nine',
	        10                  => 'Ten',
	        11                  => 'Eleven',
	        12                  => 'Twelve',
	        13                  => 'Thirteen',
	        14                  => 'Fourteen',
	        15                  => 'Fifteen',
	        16                  => 'Sixteen',
	        17                  => 'Seventeen',
	        18                  => 'Eighteen',
	        19                  => 'Nineteen',
	        20                  => 'Twenty',
	        30                  => 'Thirty',
	        40                  => 'Fourty',
	        50                  => 'Fifty',
	        60                  => 'Sixty',
	        70                  => 'Seventy',
	        80                  => 'Eighty',
	        90                  => 'Ninety',
	        100                 => 'Hundred',
	        1000                => 'Thousand',
	        1000000             => 'Million',
	        1000000000          => 'Billion',
	        1000000000000       => 'Trillion',
	        1000000000000000    => 'Quadrillion',
	        1000000000000000000 => 'Quintillion'
	    );	   
	    if (!is_numeric($number)) {  return false;   }	   
	    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
	        trigger_error(
	            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
	            E_USER_WARNING
	        );
	        return false;
	    }	
	    if ($number < 0) {  return $negative . self::recursive_convert_number_to_words(abs($number));  }	   
	    $string = $fraction = null;	   
	    if (strpos($number, '.') !== false) {
	        list($number, $fraction) = explode('.', $number);	      
	        if(substr($fraction,0,1)=='0'){
	        	$fraction=substr($fraction,1,2);
	        	 $fraction; 
	        }
	    }	   
	    switch (true) {
	        case $number < 21:
	            $string = $dictionary[$number];
	            break;
	        case $number < 100:
	            $tens   = ((int) ($number / 10)) * 10;
	            $units  = $number % 10;
	            $string = $dictionary[$tens];
	            if ($units) {
	                $string .= $hyphen . $dictionary[$units];
	            }
	            break;
	        case $number < 1000:
	            $hundreds  = $number / 100;
	             $remainder = $number % 100;
	            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
	            if ($remainder) {
	                $string .= $conjunction . self::recursive_convert_number_to_words($remainder) ;
	            }
	            break;
	        default:
	            $baseUnit = pow(1000, floor(log($number, 1000)));
	            $numBaseUnits = (int) ($number / $baseUnit);
	            $remainder = $number % $baseUnit;
	            $string = self::recursive_convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
	            if ($remainder) {
	                $string .= $remainder < 100 ? $conjunction : $separator;
	                $string .= self::recursive_convert_number_to_words($remainder);
	            }
	            break;
	    }	    
	    if (null !== $fraction && is_numeric($fraction)) {
	    	$string .= $decimal;   $string .= self::recursive_convert_number_to_words($fraction);
	    }	   
	    return $string;
	}	
	public static function createInvNumber(){
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 && $dated<16){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');	}
		$number = '';
		$result = Yii::app()->db->createCommand("SELECT final_invoice_number FROM invoices WHERE final_invoice_number LIKE '____/{$actual_year}' AND  final_invoice_number != '' ORDER BY final_invoice_number desc limit 1")->queryScalar();
		if ($result === false)
			$result = 0000;
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);
		return $calculate_number.'/'.$actual_year;
	}
	public static function createOldInvNumber(){
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 ){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');	}
		$number = '';
		$result = Yii::app()->db->createCommand("select old_sns_inv FROM invoices where old_sns_inv like '____/{$actual_year}' AND  old_sns_inv != '' Order By old_sns_inv desc  limit 1")->queryScalar();
		if ($result === false)
			$result = 0000;
		$number = substr($result, 0, 4);		$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);		
		return $calculate_number.'/'.$actual_year;
	}		
	public static function createTransferNumberPartner($partnerid){		
		$number = '';
		$result = Yii::app()->db->createCommand("Select transfer_number FROM invoices where partner=".$partnerid."  Order By transfer_number desc  limit 1")->queryScalar();
		if (empty($result))
			$result = 0000;
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);		
		return $calculate_number;
	}
	public static function adjustSeqPart($model, $partner, $old, $partner_inv, $final)
	{
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 ){
			$actual_year = date('y');	
			$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');	}
		if ($old == "Yes")
		{ 
			$partner_inv = Yii::app()->db->createCommand("select old_sns_inv, invoice_date_month, invoice_date_year FROM invoices where old_sns_inv like '____/{$actual_year}' AND  old_sns_inv != '' Order By old_sns_inv desc  limit 1")->queryRow();
		}else if ($partner == Maintenance::PARTNER_SNSI)
		{
			$partner_inv = Yii::app()->db->createCommand("Select partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner=78 and partner_inv like '____/{$actual_year}' AND  partner_inv != '' Order By partner_inv desc  limit 1")->queryRow();							
		}
		else if ($partner == Maintenance::PARTNER_SNSAPJ)
		{
			$partner_inv = Yii::app()->db->createCommand("Select snsapj_partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryRow();
		}
		else if ($partner == Maintenance::PARTNER_APJ)
		{
			$partner_inv = Yii::app()->db->createCommand("Select snsapj_partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryRow();
		}	
		else if ($partner == Maintenance::PARTNER_AUST)
		{
			$partner_inv =  Yii::app()->db->createCommand("Select partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner=1218 and partner_inv like '____/{$actual_year}' AND  partner_inv != '' Order By partner_inv desc  limit 1")->queryRow();
		}	
		else
		{ 
			$partner_inv = Yii::app()->db->createCommand("SELECT final_invoice_number, invoice_date_month, invoice_date_year FROM invoices WHERE final_invoice_number LIKE '____/{$actual_year}' AND  final_invoice_number != '' ORDER BY final_invoice_number desc limit 1")->queryRow();
		}
		$number = substr($partner_inv, 0, 4);
		if($model->invoice_date_month== $partner_inv['invoice_date_month'] && $model->invoice_date_year== $partner_inv['invoice_date_year'] ){
			if ($old == "Yes")
			{ 
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET old_sns_inv = '$partner_inv', status='To Print' WHERE old_sns_inv ='$number' ")->execute();
			}else if ($partner == Maintenance::PARTNER_SNSI)
			{
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET partner_inv = '$partner_inv', final_invoice_number='$final', status='To Print' WHERE partner_inv ='$number' and partner=78")->execute();
			}
			else if ($partner == Maintenance::PARTNER_SNSAPJ)
			{
				$nr = Yii::app()->db->createCommand("UPDATE invoices SET snsapj_partner_inv = '$partner_inv', final_invoice_number='$final', status='To Print' WHERE snsapj_partner_inv ='$number' and partner=".Maintenance::PARTNER_SNSAPJ)->execute();
				$partner_inv = Yii::app()->db->createCommand("Select snsapj_partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryRow();
			}
			else if ($partner == Maintenance::PARTNER_APJ)
			{
				$partner_inv = Yii::app()->db->createCommand("Select snsapj_partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryRow();
			}	
			else if ($partner == Maintenance::PARTNER_AUST)
			{
				$partner_inv =  Yii::app()->db->createCommand("Select partner_inv, invoice_date_month, invoice_date_year FROM invoices where partner=1218 and partner_inv like '____/{$actual_year}' AND  partner_inv != '' Order By partner_inv desc  limit 1")->queryRow();
			}	
			else
			{ 
				$partner_inv = Yii::app()->db->createCommand("SELECT final_invoice_number, invoice_date_month, invoice_date_year FROM invoices WHERE final_invoice_number LIKE '____/{$actual_year}' AND  final_invoice_number != '' ORDER BY final_invoice_number desc limit 1")->queryRow();
			}
		}

	}
	public static function createInvNumberPartner(){
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 && $dated<16){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');		}
		$number = '';
		$result = Yii::app()->db->createCommand("Select partner_inv FROM invoices where partner=78 and partner_inv like '____/{$actual_year}' AND  partner_inv != '' Order By partner_inv desc  limit 1")->queryScalar();
		if ($result === false)
			$result = 0000;
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);		
		return $calculate_number.'/'.$actual_year;
	}
	public static function createInvNumberPartnerAust(){
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 && $dated<16){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');		}
		$number = '';
		$result = Yii::app()->db->createCommand("Select partner_inv FROM invoices where partner=1218 and partner_inv like '____/{$actual_year}' AND  partner_inv != '' Order By partner_inv desc  limit 1")->queryScalar();
		if ($result === false || $result == null)
		{	$result = 0000; }
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);		
		return $calculate_number.'/'.$actual_year;
	}
	public static function createInvNumberPartnerSNSAPJ(){		
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 && $dated<16){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');	}	
		$number = '';
		$result = Yii::app()->db->createCommand("Select snsapj_partner_inv FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryScalar();
		if ($result === false)
			$result = 0000;
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);	
		return $calculate_number.'/'.$actual_year;
	}
	public static function createInvNumberPartnerAPJ(){	
		$datem= date('m');	$dated= date('d');
		if ($datem == 1 && $dated<16){
			$actual_year = date('y');	$actual_year = $actual_year - 1 ;
		}else{	$actual_year = date('y');	}
		$number = '';
		$result = Yii::app()->db->createCommand("Select snsapj_partner_inv FROM invoices where partner in (201,554) and snsapj_partner_inv like '____/{$actual_year}' AND  snsapj_partner_inv != '' Order By snsapj_partner_inv desc  limit 1")->queryScalar();
		if ($result === false)
			$result = 0000;
		$number = substr($result, 0, 4);	$calculate_number = str_pad($number+1, 4, "0", STR_PAD_LEFT);		
		return $calculate_number.'/'.$actual_year;
	}	
	public static function formatDate($data, $from = 'Y-m-d', $to = 'd/m/Y') {
		return DateTime::createFromFormat($from, $data)->format($to);
	}	
	public static function prettyDate($data){
		$timestamp = strtotime($data);	$diff = date('d', $timestamp)-date("d");	$day = date('d', $timestamp);	$month = date('m',$timestamp);
		$time = strtotime(date('Y').'-'.$month.'-'.$day);
		switch($diff){
			case 0:
				return 'Today';	
			break;			
			case 1:
				return 'Tomorrow';
			break;			
			default:
				return  date('l', $time);
			break;
		}
	}	
	public static function getFileReport($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR.'REPORTS.pdf';
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR.'REPORTS.pdf';		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFileexpenses($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.'expenses.pdf';
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.'expenses.pdf';		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFileexpensesBankTr($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.'bankTransfer.pdf';
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'expenses'.DIRECTORY_SEPARATOR.'bankTransfer.pdf';		
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getDirPathReports(){
		$path = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR;
	 	if (!is_dir( $path ) ) {   mkdir( $path, 0777, true); chmod( $path, 0777 );  }
		return $path; 
	}
	public static function getFileExcel($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'excel'.DIRECTORY_SEPARATOR.'export.xls';
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'excel'.DIRECTORY_SEPARATOR.'export.xls';
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}	
	public static function getFileExcelDep($path = false){
		$filePath = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'excel'.DIRECTORY_SEPARATOR.'deployments.xls';
		$fileName = Yii::app()->getBaseUrl(true).DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'excel'.DIRECTORY_SEPARATOR.'deployments.xls';
		if (file_exists($filePath) && is_file($filePath)){
			return $path ? $filePath : $fileName;
		}
		return null;
	}
 }
?>