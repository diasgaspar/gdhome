<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace gdhome\HomeFunctions;
use gdhome\Db\IDbAdapter as IDbAdapter;
use gdhome\models\DoorRecord as DoorRecord;
use DateTime, DateInterval, DateTimeZone;
//use PHPMailer;
use gdhome\PHPMailer\PHPMailer as PHPMailer;

/**
 * Description of DoorRecorder
 *
 * @author gaspar
 */
class DoorRecorder {
    
        /**
     * @var \gdhome\Db\IDbAdapter
     */
    protected $db;

    /**
     * @param IDbAdapter $dbAdapter
     */
    public function __construct( IDbAdapter $dbAdapter )
    {
        $this->db = $dbAdapter;
    }

        /**
     * Fetch all meas in the RawData Table
     *
     * @return array
    */
    public function getAllRecords() {

        $doorRecords = $this->db->fetchAll('SELECT TimeStamp, State FROM DoorData');
        $doors = array();

        if( count( $doorRecords ) > 0 )
        {
            foreach( $doorRecords as $doorRecord )
            {
                $doorRec = new DoorRecord();
                $doorRec->setTimeStamp( $doorRecord[ 'TimeStamp' ] );
                $doorRec->setState( $doorRecord[ 'State' ]);
                $doors[] = $doorRecord;
            }
        }

        return $doors;
    }
    /**
     * 
     * @param \gdhome\models\DoorRecord $doorRecord
     * @param type $timeDelayInSeconds this method stores door events whose time interval is greater than $timeDelayInSeconds from the last event introduced in DB
     * @return \gdhome\models\DoorRecord
     */
    public function insertDoorEventTimeFiltered(\gdhome\models\DoorRecord $doorRecord, $timeDelayInSeconds) {
 
        if ($this->getDeltaFromLastDoorEvent()>=$timeDelayInSeconds){
            $doorRecord=$this->insertDoorRecord ($doorRecord);
            return $doorRecord;
        }
        else{
            return null;
        }
        
        
    }
    
    /**
     * 
     * @param \gdhome\models\DoorRecord $doorRecord
     */
    public function insertDoorRecord(\gdhome\models\DoorRecord $doorRecord) {
        
         $doorRecRecord = [
            'TimeStamp' => $doorRecord->getTimeStamp(),
            'State'      => $doorRecord->getState(),
        ];

        $this->db->insert( 'DoorData', $doorRecRecord );

        return $doorRecord;
    }
    /**
     * 
     * @return int delta time in seconds from insert of the last door event
     */
    protected function getDeltaFromLastDoorEvent() {
        $lastDoorRecord=$this->db->fetchOne("Select * from DoorData ORDER BY TimeStamp DESC LIMIT 1");
        $dtNow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        
        $dtLastInsert=new DateTime($lastDoorRecord[ 'TimeStamp' ],new DateTimeZone('EUROPE/Lisbon'));
        
        $deltaTime = $dtNow->getTimeStamp() - $dtLastInsert->getTimeStamp();
        
        return $deltaTime;
    }
    /**
     * 
     * @param type $dtbegin
     * @param type $dtEnd
     * @return type 
     */
    public function getDoorEventsBetweenTimeInterval($dtbegin,$dtEnd){
        
        $dt1=$dtbegin->format("Y-m-d");
        $dt2=$dtEnd->format("Y-m-d");
        $doorEvents = $this->db->fetchAll("SELECT TimeStamp, State FROM DoorData where TimeStamp>='$dt1' and TimeStamp<'$dt2'");
        $doorEventsArray = array();
         if( count( $doorEvents ) > 0 )
        {
            foreach( $doorEvents as $doorRecord )
            {
                $doorRec = new DoorRecord();
                $doorRec->setTimeStamp( $doorRecord[ 'TimeStamp' ] );
                $doorRec->setState( $doorRecord[ 'State' ]);
                $doorEventsArray[] = $doorRec;
            }
        }
        
        return $doorEventsArray;
    }
    /**
     * 
     * @param type $dtNow Date to evaluate
     * @param type $minTime lower time limit (H)HMM ex: 8H30M -> $minTime=830; 21H45M ->$minTime=2145
     * @param type $maxTime upper time limit (H)HMM
     * @return boolean
     */
        
    public function isItTimeToSendNotification($dtNow, $minTime, $maxTime){
        $maxTimeH=($maxTime-$maxTime%100)/100;
        $minTimeH=($minTime-$minTime%100)/100;
        $maxTimeM=$maxTime%100;
        $minTimeM=$minTime%100;
        $timeToNotify1 =new DateTime();
        $timeToNotify2 =new DateTime();
        date_time_set($timeToNotify1, $minTimeH, $minTimeM);
        date_time_set($timeToNotify2, $maxTimeH, $maxTimeM);


        if(($dtNow>=$timeToNotify1)&& ($dtNow<$timeToNotify2)){
            $itstime=true;
        }else{
            $itstime=false;
        } 			

        return $itstime;
    }
    

}
