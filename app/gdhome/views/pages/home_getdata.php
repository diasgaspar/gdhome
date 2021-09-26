<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once '../../../start.php';

use gdhome\Db\Db as Db;
//use DateTime, DateInterval, DateTimeZone;
use gdhome\models\HomeMeasRaw as HomeMeasRaw;
//echo getcwd() . "\n";


$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$dtDayAgo->sub(new DateInterval("P1D"));

$homeMeasRaw = new HomeMeasRaw();


$durationDays=1;
$homeMeasRawList=$homeMeasRaw->getOverlayed($dtNow->format("Y-m-d"), $dtDayAgo->format("Y-m-d"), $durationDays);

$arr['cols'][0] = array('id'=>'','label'=>'Time','pattern'=>'', 'type'=>'string');
$arr['cols'][1] = array('id'=>'','label'=>'Pw(w)yesterday','pattern'=>'', 'type'=>'number');
$arr['cols'][2] = array('id'=>'','label'=>'Pw(w)today','pattern'=>'', 'type'=>'number');
$i=0;
foreach ($homeMeasRawList as $key => $value) {

  $arr['rows'][$i]['c'] = array(
        array('v'=>$key, 'f'=>null),
        array('v'=>(isset($value['d2'])==1?$value['d2']->getRxPw():0), 'f'=>null),
        array('v'=>(isset($value['d1'])==1?$value['d1']->getRxPw():0), 'f'=>null),
    );
  $i++;        
            
}

$string =json_encode((object)$arr);
echo $string;


