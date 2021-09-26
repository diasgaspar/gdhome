<?php

namespace gdhome\HomeFunctions;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator;
use gdhome\Db\IDbAdapter as IDbAdapter;
use gdhome\HomeVars as HomeVars;
use DateTime;
use PDOException;

class CronExecutor
{
	
	/**
     * @var \gdhome\Db\IDbAdapter
   */
    protected $db;

		/**
     * @var gdhome\HomeFunctions\HomeAggregator
   */
    protected $homeAggregator;

	
   /**
     * @param IDbAdapter $dbAdapter
     */
    public function __construct( IDbAdapter $dbAdapter )
    {
        $this->db = $dbAdapter;
        $this->homeAggregator = new HomeAggregator($this->db);
    }
    
    /**
     * 
     * @param type $dtNow time stamp under evaluation (now)
     * @return int 0: no time to run cron; 1: time to run cron
     */
    public function runCron($dtNow){
    	$message=0;
        if($this->isItTimeToRunTasks($dtNow, HomeVars::MAINTENANCE_TIME_BEGIN, HomeVars::MAINTENANCE_TIME_END)){

            $this->homeAggregator->doDoorDataMaintenance();
            $this->homeAggregator->doRawDataMaintenance();
            $message=1; 
                    
    	}

        if($this->isItTimeToRunTasks($dtNow, HomeVars::SEND_DAILY_REPORT_TIME_BEGIN, HomeVars::SEND_DAILY_REPORT_TIME_END)){ 
           $message=$this->homeAggregator->sendDailyReport();
        }
       return $message; 

    }
    /**
     * 
     * @param type $dtNow Date to evaluate
     * @param type $minTime lower time limit (H)HMM ex: 8H30M -> $minTime=830; 21H45M ->$minTime=2145
     * @param type $maxTime upper time limit (H)HMM
     * @return boolean
     */
    public function isItTimeToRunTasks ($dtNow,$minTime, $maxTime) {
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
    /**
     * @return gdhome\HomeFunctions\HomeAggregator
     */
    public function getHomeAggregator(){
    
		return $this->homeAggregator;  
    }
    
    

}