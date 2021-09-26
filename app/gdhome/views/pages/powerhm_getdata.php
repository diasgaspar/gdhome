<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once '../../../start.php';

//use gdhome\Db\Db as Db;
use gdhome\models\HomeMeasHour as HomeMeasHour;

$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$dtAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P70D"));

$homeMeasHour= new HomeMeasHour();
$homeMeasHourList=$homeMeasHour->getRecordsBetweenTwoDates($dtAgo->format("Y-m-d"), $dtNow->format("Y-m-d"));

$obj = new stdClass();
$obj->data = array();

$i=0;
foreach ($homeMeasHourList as $key => $value) {
  $obj->data[$i]=array('timestamp'=>$value->getTimeStamp(),'value'=>array('PM2.5'=>$value->getRxPw()));
  $i++;        
            
}

$string =json_encode($obj);
echo $string;

