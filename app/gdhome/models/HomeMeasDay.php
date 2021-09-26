<?php

namespace gdhome\models;
use gdhome\Db\Db as Db;
use gdhome\HomeVars as HomeVars;   


class HomeMeasDay extends HomeMeas{
    
    /**
     *
     * @var type 
     */
    protected $TempMax;
    
    /**
     *
     * @var type 
     */
    protected $TempMin;
    
    /**
     *
     * @var type 
     */
    protected $RxPwMin;
    /**
     *
     * @var type 
     */
    protected $RxPwMax;
    /**
     *
     * @var type 
     */
    protected $Ewh;
    /**
     *
     * @var type 
     */
    protected $EwhF;
    /**
     *
     * @var type 
     */
    protected $EwhV;
    
   
    
    /**
     * 0 Args
     * 10 Args: $TimeStamp, $Temp, $TempMax, $TempMin,$RxI, $RxPwMax, $RxPwMin, $Ewh, $EwhF, $EwhV
     */
     public function __construct(){
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 0:
                //self::__construct1();
                break;
            case 10:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3],$argv[4],$argv[5],$argv[6],$argv[7],$argv[8],$argv[9] );
         }
    }
    
   /* function __construct1() { 
        
    }*/
    function __construct2($TimeStamp, $Temp, $TempMax, $TempMin,$RxI, $RxPwMax, $RxPwMin, $Ewh, $EwhF, $EwhV) { 
        parent::setTimeStamp($TimeStamp);
        parent::setTemp($Temp);
        parent::setRxI($RxI);
        //parent::setRxPw($RxPw);
        $this->setTempMax($TempMax);
        $this->setTempMin($TempMin);
        $this->setRxPwMax($RxPwMax);
        $this->setRxPwMin($RxPwMin);
        $this->setEwh($Ewh);
        $this->setEwhF($EwhF);
        $this->setEwhV($EwhV);
        
    }
     /**
     * Calculates the amount of energy spent from $date1 to lastdata entry
     * @param type $startDate, $endDate
     * @return type array $meas['Ewh','EwhV','EwhF']
     */
    public function getEnergySpent($startDate,$endDate){
      $db = Db::getInstance();
      $req = $db->prepare(
              "Select sum(Ewh) as 'Ewh', sum(EwhV) as 'EwhV', sum(EwhF) as 'EwhF'
                FROM RawDataDaily
                where TimeStamp >= :startDate and TimeStamp<=:endDate" 
          );
      $req->execute(array('startDate' => $startDate, 'endDate'=>$endDate));
      $meas=$req->fetch();
      return $meas;
    }
    
      /**
     * Calculates energy cost from $date1 to lastdata entry
     * @param type $startDate, $endDate
     * @return type $cost['cost', 'costF', 'costV','numDays]
     */
    public function getEnergyCostBiHourlyTarif($startDate,$endDate){
      $db = Db::getInstance();
      $req = $db->prepare(
              "select 
                  count( distinct(DATE_FORMAT( TimeStamp, '%Y-%m-%d' ))) as 'numDays',
                  sum(EwhF)*:T2T3Cost/1000 + sum(EwhV)*:T1Cost/1000 as 'cost',
                  sum(EwhF)*:T2T3Cost/1000 as 'costF',
                  sum(EwhV)*:T1Cost/1000 as 'costV'
                FROM RawDataDaily
                where TimeStamp >= :startDate and TimeStamp <=:endDate"
          );
      //$req->execute(array('startDate' => $startDate, 'T2T3Begin' => '08', 'T2T3End'=> 22, 'T2T3Cost'=> 0.2033, 'T1Cost'=>0.0941));
      $req->execute(array('startDate' => $startDate,'endDate' => $endDate, 'T2T3Cost'=> HomeVars::ENERGY_COST_T2T3, 'T1Cost'=>HomeVars::ENERGY_COST_T1));
      $cost=$req->fetch();
      return $cost;
    }
    
     /**
     * Gets the latest record inserted into meas table
     * @return \gdhome\models\HomeMeasDay
     */
    public function getLatestRecord() {
      $db = Db::getInstance();
      $req = $db->query('SELECT TimeStamp, Temp, RxI, RxPw
        FROM `RawDataDaily`
        ORDER BY TimeStamp DESC
        LIMIT 1');
      $meas=$req->fetch();
      return new HomeMeasDay($meas['TimeStamp'],$meas['Temp'],$meas['RxI'],$meas['RxPw']);
    }
    
    /**
     * 
     * @param type $startDate
     * @param type $endDate
     * @return type list[] of HomeMeasDay
     */
    public function getRecordsBetweenTwoDates($startDate,$endDate){
        $list = [];
        $db = Db::getInstance();
        $req = $db->prepare(
              "select DATE_FORMAT( TimeStamp, '%Y-%m-%d' ) as 'TimeStamp', Temp, TempMax, TempMin, RxI, RxPwMax, RxPwMin, Ewh, EwhF, EwhV from RawDataDaily 
                where TimeStamp >= :startDate and TimeStamp <=:endDate"
          );
        $req->execute(array('startDate' => $startDate,'endDate' => $endDate));
      
        
        foreach($req->fetchAll() as $meas) {
          //print $meas['EwhF']."\n";
            $list[] = new HomeMeasDay($meas['TimeStamp'], $meas['Temp'],$meas['TempMax'],$meas['TempMin'],$meas['RxI'],$meas['RxPwMax'],$meas['RxPwMin'],$meas['Ewh'],$meas['EwhF'],$meas['EwhV']);
            //$TimeStamp, $Temp, $TempMax, $TempMin,$RxI, $RxPwMax, $RxPwMin, $Ewh, $EwhF, $EwhV
        }
      return $list;   
    }
    
    public function getMonthlyRecordsBetweenTwoDates($startDate,$endDate){
        $list = [];
        $db = Db::getInstance();
        $req = $db->prepare(
              "select DATE_FORMAT( TimeStamp, '%Y-%m' ) as 'TimeStamp', 
                  avg(Temp) as 'Temp', 
                  max(TempMax) as 'TempMax', 
                  min(TempMin) as 'TempMin', 
                  avg(RxI) as 'RxI', 
                  max(RxPwMax) as 'RxPwMax', 
                  min(RxPwMin) as 'RxPwMin', 
                  sum(Ewh) as 'Ewh', 
                  sum(EwhF) as 'EwhF', 
                  sum(EwhV) as 'EwhV' 
                from RawDataDaily 
                where TimeStamp >= :startDate and TimeStamp <=:endDate
                GROUP BY DATE_FORMAT( TimeStamp, '%Y-%m' )"
          );
        $req->execute(array('startDate' => $startDate,'endDate' => $endDate));
      
        
        foreach($req->fetchAll() as $meas) {
          //print $meas['EwhF']."\n";
            $list[] = new HomeMeasDay($meas['TimeStamp'], $meas['Temp'],$meas['TempMax'],$meas['TempMin'],$meas['RxI'],$meas['RxPwMax'],$meas['RxPwMin'],$meas['Ewh'],$meas['EwhF'],$meas['EwhV']);
            //$TimeStamp, $Temp, $TempMax, $TempMin,$RxI, $RxPwMax, $RxPwMin, $Ewh, $EwhF, $EwhV
        }
      return $list;   
    }
    
    
    /**
     * 
     * @return type
     */
    public function getTempMax(){
        return $this->TempMax;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setTempMax($TempMax){
        $this->TempMax=$TempMax;
    }
    
      /**
     * 
     * @return type
     */
    public function getTempMin(){
        return $this->TempMin;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setTempMin($TempMin){
        $this->TempMin=$TempMin;
    }
    
    
        /**
     * 
     * @return type
     */
    public function getRxPwMax(){
        return $this->RxPwMax;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setRxPwMax($RxPwMax){
        $this->RxPwMax=$RxPwMax;
    }
     /**
     * 
     * @return type
     */
    public function getRxPwMin(){
        return $this->RxPwMin;
    }
    /**
     * 
     * @param type $rxPwF
     */
    public function setRxPwMin($RxPwMin){
        $this->RxPwMin=$RxPwMin;
    }
    
    /**
     * 
     * @return type
     */
    public function getEwh(){
        return $this->Ewh;
    }
    /**
     * 
     * @param type $Ewh
     */
    public function setEwh($Ewh){
        $this->Ewh=$Ewh;
    }
    /**
     * 
     * @return type
     */
    public function getEwhF(){
        return $this->EwhF;
    }
    /**
     * 
     * @param type $EwhF
     */
    public function setEwhF($EwhF){
        $this->EwhF=$EwhF;
    }
    
       /**
     * 
     * @return type
     */
    public function getEwhV(){
        return $this->EwhV;
    }
    /**
     * 
     * @param type $EwhV
     */
    public function setEwhV($EwhV){
        $this->EwhV=$EwhV;
    }
}