<?php

error_reporting( E_ALL | E_STRICT );
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

use gdhome\HomeFunctions\DoorRecorder as DoorRecorder;
use gdhome\models\DoorRecord as DoorRecord;
use gdhome\PHPMailer\MailSender as MailSender;
use gdhome\Db\MysqlAdapter as MysqlAdapter;
use gdhome\HomeVars as HomeVars;

require_once 'app/start.php';

//$doorEvent = $_GET['state'];
$doorEvent = filter_input(INPUT_GET,'state',FILTER_SANITIZE_SPECIAL_CHARS);

include "db_info.php";

$dbAdapter = new MysqlAdapter( 'mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password );

$doorRecorder = new DoorRecorder( $dbAdapter );

$dtNow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));

$doorRec= new DoorRecord();
$doorRec->setTimeStamp($dtNow->format("Y-m-d H:i:s"));
$doorRec->setState($doorEvent);

$inserted=$doorRecorder->insertDoorEventTimeFiltered($doorRec, HomeVars::TIME_TO_WAIT_BEFORE_SEND_NEW_DOOR_NOTIFICATION);

var_dump($inserted);

if($doorRecorder->isItTimeToSendNotification($dtNow, HomeVars::SEND_DOOR_NOTIFICATION_TIME_BEGIN, HomeVars::SEND_DOOR_NOTIFICATION_TIME_END)){
    $subject="Door event happened";
    $message= "Hello,
        The door was opened: ".$dtNow->format("Y-m-d ")."
        Time of Event: ".$dtNow->format("H:i:s")."
       
        Daily report in:".HomeVars::GDHOME_HOME_PAGE."/doorLogDay.php
        Power report in:".HomeVars::GDHOME_HOME_PAGE."/powerday.php
        (gdhome agent)";
    $mailer=new MailSender();
    foreach(HomeVars::USER_EMAIL_ACCOUNTS as $name => $email) {  
        $mailer->send_mail($email, $name, $subject, $message);
    }
    
    //$mailer->send_mail("diasgaspar@gmail.com", "gaspar", $subject, $message);
    //$mailer->send_mail("filipagmota@gmail.com", "filipa", $subject,$message);
    
}

