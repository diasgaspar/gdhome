<?php
namespace gdhome\models;
use gdhome\Db\Db as Db;
use gdhome\HomeVars as HomeVars;
use DateTime, DateInterval, DateTimeZone;
class HomeMeasRaw extends HomeMeas{

    
    
    public function __construct(){
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 0:
                //self::__construct1();
                break;
            case 4:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3] );
         }
    }
    
   /* function __construct1() { 
        
    }*/
    function __construct2($TimeStamp, $Temp, $RxI, $RxPw) { 
        parent::setTimeStamp($TimeStamp);
        parent::setTemp($Temp);
        parent::setRxI($RxI);
        parent::setRxPw($RxPw);

        
    }
    
    public static function setHomeMeas($TimeStamp, $Temp, $RxI, $RxPw){
        parent::setTimeStamp($TimeStamp);
        parent::setTemp($Temp);
        parent::setRxI($RxI);
        parent::setRxPw($RxPw);
    }

    public function getRecordsBetweenTwoDates($startDate,$endDate){
        
     return false;   
    }
    
  
    /**
     * Calculates the amount of energy spent from $date1 to lastdata entry
     * @param type $startDate
     * @return type array $meas['Ewh']
     */
    public function getEnergySpent($startDate,$endDate){
      $db = Db::getInstance();
      $req = $db->prepare(
              "Select sum(RxPw)/12 as 'Ewh'
                FROM RawData
                where TimeStamp >= :startDate and TimeStamp<:endDate" 
          );
      $req->execute(array('startDate' => $startDate, 'endDate'=>$endDate));
      $meas=$req->fetch();
      return $meas;
    }
    /**
     * Calculates energy cost from $date1 to lastdata entry
     * @param type $startDate, $endDate
     * @return type $cost['cost'|'numDays]
     */
    public function getEnergyCostBiHourlyTarif($startDate,$endDate){
         $db = Db::getInstance();
      $req = $db->prepare(
              "select 
                  count( distinct(DATE_FORMAT( TimeStamp, '%Y-%m-%d' ))) as 'numDays',
                  sum((CASE WHEN DATE_FORMAT( TimeStamp, '%HH' )>=:T2T3Begin and DATE_FORMAT( TimeStamp, '%HH' )<:T2T3End THEN (RxPw*:T2T3Cost) ELSE (RxPw*:T1Cost) END))/12000 as 'cost'
                FROM RawData
                where TimeStamp >= :startDate and TimeStamp <:endDate"
          );
      //$req->execute(array('startDate' => $startDate, 'T2T3Begin' => '08', 'T2T3End'=> 22, 'T2T3Cost'=> 0.2033, 'T1Cost'=>0.0941));
      $req->execute(array('startDate' => $startDate,'endDate' => $endDate, 'T2T3Begin' => HomeVars::ENERGY_T2T3_BEGIN, 'T2T3End'=> HomeVars::ENERGY_T2T3_END, 'T2T3Cost'=> HomeVars::ENERGY_COST_T2T3, 'T1Cost'=>HomeVars::ENERGY_COST_T1));
      $cost=$req->fetch();
      return $cost;
    }
    
    /**
     * Gets the latest record inserted into meas table
     * @return \gdhome\models\HomeMeasRaw
     */
    public function getLatestRecord() {
      $db = Db::getInstance();
      $req = $db->query('SELECT TimeStamp, Temp, RxI, RxPw
        FROM `RawData`
        ORDER BY TimeStamp DESC
        LIMIT 1');
      $meas=$req->fetch();
      return new HomeMeasRaw($meas['TimeStamp'],$meas['Temp'],$meas['RxI'],$meas['RxPw']);
    }
    
    /**
     * 
     * @param type $date1 
     * @param type $date2
     * @param type $durationDays
     * @return list of [HH:MM][d1..d2] of \gdhome\models\HomeMeasRaw
     */
      public function getOverlayed($date1,$date2,$durationDays) {
      $list = [];
      $db = Db::getInstance();
      
      //day 1 records
      $req = $db->prepare(
              "SELECT DATE_FORMAT( TimeStamp, '%H:%i' ) as 'Time',TimeStamp, Temp, RxI, RxPw FROM RawData 
                WHERE TimeStamp >= :date1 and TimeStamp < (DATE(:date1)+ INTERVAL :durationDays DAY)" 
                
          );
      $req->execute(array('date1' => $date1, 'durationDays'=>$durationDays));
      // we create a list of Post objects from the database results
      foreach($req->fetchAll() as $meas) {
          //print $meas['RxPw']."\n";
        $list[$meas['Time']]['d1'] = new HomeMeasRaw($meas['TimeStamp'], $meas['Temp'],$meas['RxI'], $meas['RxPw']);
        
      }
      //day 2 records
      $req2 = $db->prepare(
              "SELECT DATE_FORMAT( TimeStamp, '%H:%i' ) as 'Time',TimeStamp, Temp, RxI, RxPw FROM RawData 
                WHERE TimeStamp >= :date2 and TimeStamp < (DATE(:date2)+ INTERVAL :durationDays DAY)" 
                
          );
      $req2->execute(array('date2' => $date2, 'durationDays'=>$durationDays));
      // we create a list of Post objects from the database results
      foreach($req2->fetchAll() as $meas) {
          //print $meas['RxPw']."\n";
        $list[$meas['Time']]['d2'] = new HomeMeasRaw($meas['TimeStamp'], $meas['Temp'],$meas['RxI'], $meas['RxPw']);
        
      }
      //var_dump($list);
      return $list;
    }
}

 