<?php
namespace gdhome\models;
use DateTime, DateInterval, DateTimeZone;
abstract class HomeMeas{

 /**
     * @var string
     */
    protected $TimeStamp;

    /**
     * @var string
     */
    protected $Temp;

    /**
     * @var string
     */
    protected $RxI;

    /**
     * @var string
     */
    protected $RxPw;
    
    
    abstract public function getRecordsBetweenTwoDates($startDate,$endDate);
    abstract public function getLatestRecord();
    
      /**
     * Returns the start date corresponding to the charging period of $entryDate
     * @param type $entryDate
     * @param type $specificDay - day[1..31]
     * @return DateTime
     */    
    public function calculatePreviousDateAtSpecificDay($entryDate,$specificDay){
        $previousMonthDateAtSpecificDay=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        
        $thisYear=$entryDate->format("Y");
        $thisMonth=$entryDate->format("m");
        $thisDay=$entryDate->format("d");
        
        if($thisDay >= $specificDay){
          $previousMonthDateAtSpecificDay->setDate($thisYear, $thisMonth, $specificDay);
          //print $previousMonthDateAtSpecificDay->format("Y-m-d");
        }
        else{
             $previousMonthDateAtSpecificDay->setDate($thisYear, $thisMonth-1, $specificDay);
         // print $previousMonthDateAtSpecificDay->format("Y-m-d");
              
        }
        return $previousMonthDateAtSpecificDay;
        
    }
    /**
     * Returns array['beginDay','endDay'] from the month ago charging period
     * @param type $entryDate
     * @return type array['beginDay','endDay'] of DateTime
     */
    public function calculateMonthAgoChargingPeriod($entryDate){
        $homeMeas=new HomeMeasRaw();
                
        $previousDateAtSpecificDayCalculation=$homeMeas->calculatePreviousDateAtSpecificDay($entryDate, \gdhome\HomeVars::ENERGY_READING_DAY);
        $monthAgoChargingStartDate=new DateTime($previousDateAtSpecificDayCalculation->format("Y-m-d"));
        $monthAgoChargingStartDate->sub(new DateInterval("P1M"));
        
        $monthAgoChargingEndDate=new DateTime($previousDateAtSpecificDayCalculation->format("Y-m-d"));
        $monthAgoChargingEndDate->sub(new DateInterval("P1D"));
        
        return ['beginDay'=>$monthAgoChargingStartDate,'endDay'=>$monthAgoChargingEndDate];
    }
    /**
     * checks if home meas timestamp is weekend day (sat:6, sun:0) or not (mon:1..thu:5)
     * @return boolean
     */
    public function isTimeStampWeekEnd(){
        $dt=new DateTime($this->TimeStamp);
        
        if($dt->format("w")==0 || $dt->format("w")==6){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * @return DateTime
     */
    public function getTimeStamp()
    {
        return $this->TimeStamp;
    }

    /**
     * @param string $TimeStamp
     */
    public function setTimeStamp( $TimeStamp )
    {
        $this->TimeStamp = $TimeStamp;
    }
    
        /**
     * @return string
     */
    public function getTemp()
    {
        return $this->Temp;
    }

    /**
     * @param string $Temp
     */
    public function setTemp( $Temp )
    {
        $this->Temp = $Temp;
    }

    /**
     * @return string
     */
    public function getRxI()
    {
        return $this->RxI;
    }

    /**
     * @param string $RxI
     */
    public function setRxI( $RxI )
    {
        $this->RxI = $RxI;
    }

    /**
     * @return string
     */
    public function getRxPw()
    {
        return $this->RxPw;
    }

    /**
     * @param string $RxPw
     */
    public function setRxPw( $RxPw )
    {
        $this->RxPw = $RxPw;
    }
    
    
}

