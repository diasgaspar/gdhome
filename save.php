<?php
error_reporting( E_ALL | E_STRICT );
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use gdhome\Db\MysqlAdapter as MysqlAdapter;
use gdhome\HomeFunctions\HomeRecorder as HomeRecorder;
use gdhome\models\MeasRecord as MeasRecord;
use gdhome\HomeFunctions\CronExecutor as CronExecutor;

require_once 'app/start.php';

//$temp = $_GET['temp'];
$temp=filter_input(INPUT_GET,'temp',FILTER_SANITIZE_SPECIAL_CHARS);
//$RXI = $_GET['rxi'];
$RXI = filter_input(INPUT_GET,'rxi',FILTER_SANITIZE_SPECIAL_CHARS);

include "db_info.php";

$dbAdapter = new MysqlAdapter( 'mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password );
$dtNow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$homeRecorder = new HomeRecorder( $dbAdapter );


$measRec=new MeasRecord();
$measRec->setTimeStampWith5MinStep($dtNow);
$measRec->setTemp($temp);
$measRec->setRxI($RXI);
$measRec->setRxPw((int)$RXI * 23);

      
$inserted=$homeRecorder->insert($measRec);
//var_dump($inserted);

$homeRecs=$homeRecorder->getLatestRecord();
var_dump($homeRecs);  


$cronexecutor=new CronExecutor($dbAdapter);
$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));

$message = $cronexecutor->runCron($dtNow);

if ($message == 1) {
    print "Time to run Cron\n";
} else {
    print "NO Time to run Cron\n";
}