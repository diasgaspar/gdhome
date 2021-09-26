<?php

use gdhome\Db\MysqlAdapter as MysqlAdapter;
use gdhome\HomeFunctions\HomeRecorder as HomeRecorder;
use gdhome\models\MeasRecord as MeasRecord;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator; 
error_reporting( E_ALL | E_STRICT );
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require_once 'app/start.php';



$dt = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
$dif=($dt->format("i")%5);
echo "\n".$dif."\n";
print $dt->sub(new DateInterval('PT'.$dif.'M'))->format("Y-m-d H:i");

include "db_info.php";


$dbAdapter = new MysqlAdapter( 'mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password );

$homeRecorder = new HomeRecorder( $dbAdapter );

$meas=$homeRecorder->getLatestRecord();

$recordList = $meas;

//include "app/gdhome/views/recorList.php";

var_dump( $meas );


$measRec=new MeasRecord();
$measRec->setTemp(21);
$measRec->setRxI(9);
$measRec->setRxPw(123);

$measRecord = [
            'TimeStamp' => $measRec->getTimeStamp(),
            'Temp'      => $measRec->getTemp(),
            'RxI' => $measRec->getRxI(),
            'RxPw'     => $measRec->getRxPw()
        ];
        
   
//$homeAggregator=new HomeAggregator('mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password);
//echo $homeAggregator->doHourlyOperations();





