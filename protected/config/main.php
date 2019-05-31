<?php

// uncomment the following to define a path alias
Yii::setPathOfAlias('vendors','protected/vendors');

ob_start('My_OB');
function My_OB($str, $flags)
{
    //remove UTF-8 BOM
    $str = preg_replace("/\xef\xbb\xbf/","",$str);
    
    return $str;
}

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
		
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SNS it!',
	'timeZone' => 'Asia/Beirut',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		//'application.vendors.phpexcel.PHPExcel',
	),
	'aliases' => array(
	    'xupload' => 'ext.xupload'
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'sns!t',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1', '192.168.1.*'),
		),
		'reports',
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			//'caseSensitive'=>false,   
			'showScriptName' => false,
			'rules'=>array(
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		/*'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=snsit',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'schemaCachingDuration' => 60,//2592000
		),/*
		'dbCRM'=>array(
  		 // 'class'=>'application.components.MyMsSqlConnection', 
 
   		// old MS PDO + MSSQL 2000:  
  		 //'connectionString' => 'mssql:host=HOSTNAME\SQLEXPRESS;dbname=Client',
 
  		 // new MS PDO + MSSQL 2005 2008
   		'connectionString' => 'sqlsrv:Server=crm\SQLEXPRESS;Database=SNS_MSCRM',
      	'username' => 'sa',
      	'password' => 'sql@2008',
      	'charset' => 'GB2312',
     	 'tablePrefix' => 'tbl_',
		),*/
		'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*array(
					'class'=>'CWebLogRoute',
				),*/
			),
		),
		'ePdf' => array(
        	'class'         => 'ext.yii-pdf.EYiiPdf',
        	'params'        => array(
	            'mpdf'     => array(
	                'librarySourcePath' => 'application.vendors.MPDF54.*',
	                'constants'         => array(
	                    '_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
	                ),
	                'class'=>'mpdf', // the literal class filename to be loaded from the vendors folder
	                /*'defaultParams'     => array( // More info: http://mpdf1.com/manual/index.php?tid=184
	                    'mode'              => '', //  This parameter specifies the mode of the new document.
	                    'format'            => 'A4-L', // format A4, A5, ...
	                    'default_font_size' => 0, // Sets the default document font size in points (pt)
	                    'default_font'      => '', // Sets the default font-family for the new document.
	                    'mgl'               => 15, // margin_left. Sets the page margins for the new document.
	                    'mgr'               => 15, // margin_right
	                    'mgt'               => 16, // margin_top
	                    'mgb'               => 16, // margin_bottom
	                    'mgh'               => 9, // margin_header
	                    'mgf'               => 9, // margin_footer
	                    'orientation'       => 'L', // landscape or portrait orientation
	                )*/
	            ),
	            'HTML2PDF' => array(
	                'librarySourcePath' => 'application.vendors/html2pdf_v4.03.*',
	                'classFile'         => 'html2pdf.class.php', // For adding to Yii::$classMap
	                /*'defaultParams'     => array( // More info: http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:accueil
	                    'orientation' => 'P', // landscape or portrait orientation
	                    'format'      => 'A4', // format A4, A5, ...
	                    'language'    => 'en', // language: fr, en, it ...
	                    'unicode'     => true, // TRUE means clustering the input text IS unicode (default = true)
	                    'encoding'    => 'UTF-8', // charset encoding; Default is UTF-8
	                    'marges'      => array(5, 5, 5, 8), // margins by default, in order (left, top, right, bottom)
	                )*/
	            )
        	),
    	),
    	'mailer' => array(
		 /*    'class' => 'ext.Yii-SwiftMailer-master.SwiftMailer',
		    // For SMTP 
			// Ramy Khattar : Configuring Email Setup 06/08/2014
    		'mailer' => 'smtp',
			'host'=>'10.10.10.8',
			'From' => 'no-reply@sns-emea.com',
			'username'=>'no-reply@sns-emea.com',
			'password'=>'$n$123',
			'port' => '25',	*/

         'class' => 'ext.Yii-SwiftMailer-master.SwiftMailer',
        'mailer' => 'smtp',
      'host'=>'outlook.office365.com',
      'From' => 'no_reply@sns-emea.com',
      'username'=>'no_reply@sns-emea.com',
      'password'=>'Noreply@2018',
       'port' => '587', 
      'security'=>'tls',

      			
		),
		'clientScript' => array(
            'scriptMap' => array(
                'jquery-ui.css' => true,
            ),
        ),   
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'monica.olariu@briskcode.com',
		'start_hour'=>'08:00:00',
		'end_hour'  =>'17:00:00',
	),
);
