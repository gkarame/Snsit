<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
/*
 * Author: Mike
 * Date: 18.06.19
 * Change console settings
 */
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
/*		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=snsit',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
        'mailer' => array(
            'class' => 'ext.Yii-SwiftMailer-master.SwiftMailer',
            'mailer' => 'smtp',
            'host'=>'outlook.office365.com',
            'From' => 'no_reply@sns-emea.com',
            'username'=>'no_reply@sns-emea.com',
            'password'=>'Noreply@2018',
            'port' => '587',
            'security'=>'tls',
        ),
	),
);