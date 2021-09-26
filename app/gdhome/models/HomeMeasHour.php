<?php

namespace gdhome\models;
use gdhome\Db\Db as Db;
use gdhome\HomeVars as HomeVars;
use DateTime, DateInterval, DateTimeZone;

class HomeMeasHour extends HomeMeas{
    
    /**
     *
     * @var type 
     */
    protected $RxPwF;
    
    /**
     *
     * @var type 
     */
    protected $RxPwV;
    
     public function __construct(){
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 0:
                //self::__construct1();
                break;
            case 6:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3],$argv[4],$argv[5] );
         }
    }
    
   /* function __construct1() { 
        
    }*/
    function __construct2($TimeStamp, $Temp, $RxI, $RxPw, $RxPwF, $RxPwV) { 
        parent::setTimeStamp($TimeStamp);
        parent::setTemp($Temp);
        parent::setRxI($RxI);
        parent::setRxPw($RxPw);
        $this->setRxPwF($RxPwF);
        $this->setRxPwV($RxPwV);

        
    }
     /**
     * Calculates the amount of energy spent from $date1 to lastdata entry
     * @param type $startDate
     * @return type array $meas['Ewh']
     */
    public function getEnergySpent($startDate,$endDate){
      $db = Db::getInstance();
      $req = $db->prepare(
              "Select sum(RxPw) as 'Ewh'
                FROM RawDataHourly
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
       
      return false;
    }
    
    
    /**
     * 
     * @param type $startDate
     * @param type $endDate
     * @return \gdhome\models\HomeMeasHour
     */
    public function getRecordsBetweenTwoDates($startDate,$endDate){
       $list = [];
        $db = Db::getInstance();
        $req = $db->prepare(
              "select DATE_FORMAT( TimeStamp, '%Y-%m-%dT%H:%m:%s' ) as 'timestamp', Temp, RxI, RxPw, RxPwF, RxPwV from RawDataHourly 
                where TimeStamp >= :startDate and TimeStamp <=:endDate"
          );
        $req->execute(array('startDate' => $startDate,'endDate' => $endDate));
      
        
        foreach($req->fetchAll() as $meas) {
          //print $meas['timestamp']."\n";
            $list[] = new HomeMeasHour($meas['timestamp'], $meas['Temp'],$meas['RxI'],$meas['RxPw'],$meas['RxPwF'],$meas['RxPwV']);
        }
        //var_dump($list);
      return $list;   
      
    }
    
    public function getLatestRecord() {
      $db = Db::getInstance();
      $req = $db->query('SELECT TimeStamp, Temp, RxI, RxPw, RxPwF, RxPwV
        FROM `RawDataHourly`
        ORDER BY TimeStamp DESC
        LIMIT 1');
      $meas=$req->fetch();
      return new HomeMeasHour($meas['TimeStamp'],$meas['Temp'],$meas['RxI'],$meas['RxPw'], $meas['RxPwF'], $meas['RxPwV']);
    }
    
    
    /**
     * 
     * @return type
     */
    public function getRxPwF(){
        return $this->RxPwF;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setRxPwF($rxPwF){
        $this->RxPwF=$rxPwF;
    }
    
     /**
     * 
     * @return type
     */
    public function getRxPwV(){
        return $this->RxPwV;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setRxPwV($rxPwV){
        $this->RxPwF=$rxPwV;
    }
}

