<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


error_reporting( E_ALL | E_STRICT );
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);


use gdhome\PHPMailer\MailSender as MailSender;
use gdhome\HomeVars as HomeVars;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator;
use gdhome\Db\MysqlAdapter as MysqlAdapter;


require_once 'app/start.php';




        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'08:31:00');
        $minTime=830;//->8h30
        $maxTime=1830;//->18h30
        
            $mailer=new MailSender();
          
      
            
            
    $subject="Door event happened";
    $message= "Hello,
        The door was opened  :".$dtNow->format("Y-m-d ")."
        Time of Event=".$dtNow->format("H:i:s")."
                   
        Daily report in:".HomeVars::GDHOME_HOME_PAGE."/doorLogDay.php
        Power report in:".HomeVars::GDHOME_HOME_PAGE."/powerday.php
        (gdhome agent)";
    $mailer=new MailSender();
    //$mailer->send_mail("diasgaspar@gmail.com", "gaspar", $subject, $message);
   
    
    include "db_info.php";
    $dbAdapter = new MysqlAdapter( 'mysql:host='.$host.';dbname='.$database.';charset=utf8', $username, $password );
    $homeAggregator=new HomeAggregator($dbAdapter);
    $homeAggregator->sendDailyReport();