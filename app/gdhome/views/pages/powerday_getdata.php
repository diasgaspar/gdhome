<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once '../../../start.php';

use gdhome\Db\Db as Db;
use gdhome\models\HomeMeasDay as HomeMeasDay;

$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$dt2MonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P1M"));

$homeMeasDay= new HomeMeasDay();
$homeMeasDayList=$homeMeasDay->getRecordsBetweenTwoDates($dt2MonthAgo->format("Y-m-d"), $dtNow->format("Y-m-d"));

$arr['cols'][0] = array('id'=>'','label'=>'Day','pattern'=>'', 'type'=>'string');
$arr['cols'][1] = array('id'=>'','label'=>'Fora Vazio (T2+T3)','pattern'=>'', 'type'=>'number');
$arr['cols'][2] = array('id'=>'','label'=>'Vazio(T1)','pattern'=>'', 'type'=>'number');
$arr['cols'][3] = array("type"=>"string", "role"=>"style");

// {"id":"","label":"","pattern":"","type":"number","p":{"role":"interval"}},

//var_dump($homeMeasDayList);

$i=0;
foreach ($homeMeasDayList as $key => $value) {
    $weekendColor=($value->isTimeStampWeekEnd()?'gold':'');
  $arr['rows'][$i]['c'] = array(
      array('v'=>$value->getTimeStamp(), 'f'=>null),
      array('v'=>(int)$value->getEwhF(), 'f'=>null),
      array('v'=>(int)$value->getEwhV(), 'f'=>null),
      array('v'=>$weekendColor, 'f'=>null)
    );
  $i++;        
            
}

$string =json_encode((object)$arr);
echo $string;
