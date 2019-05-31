<?php
// change the following paths if necessary
//Alexandre
//$yii = dirname(__FILE__).'/../framework/yiilite.php';
$yii = dirname(__FILE__).'/../framework/yii.php';


if (($yii_application_env = getenv('YII_APPLICATION_ENV')) === false)
{
	$config=dirname(__FILE__).'/protected/config/main.php';
}
else
{
	$config=dirname(__FILE__).'/protected/config/main-' . $yii_application_env . '.php';
}
// remove the following lines when in production mode
//Alexandre
defined('YII_DEBUG') or define('YII_DEBUG',false);
//defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once($yii);
$app = Yii::createWebApplication($config);
$app->run();

