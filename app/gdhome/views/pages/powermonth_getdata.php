<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once '../../../start.php';

use gdhome\Db\Db as Db;
use gdhome\models\HomeMeasDay as HomeMeasDay;

$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));

$homeMeasDay= new HomeMeasDay();
$homeMeasDayList=$homeMeasDay->getMonthlyRecordsBetweenTwoDates("2012-09-17", $dtNow->format("Y-m-d"));

$arr['cols'][0] = array('id'=>'','label'=>'Mes','pattern'=>'', 'type'=>'string');
$arr['cols'][1] = array('id'=>'','label'=>'Fora Vazio (T2+T3)','pattern'=>'', 'type'=>'number');
$arr['cols'][2] = array('id'=>'','label'=>'Vazio (T1)','pattern'=>'', 'type'=>'number');

$i=0;
foreach ($homeMeasDayList as $key => $value) {
    $arr['rows'][$i]['c'] = array(
      array('v'=>$value->getTimeStamp(), 'f'=>null),
      array('v'=>(int)$value->getEwhF()/1000, 'f'=>null),
      array('v'=>(int)$value->getEwhV()/1000, 'f'=>null)
    );
  $i++;        
            
}

$string =json_encode((object)$arr);
echo $string;