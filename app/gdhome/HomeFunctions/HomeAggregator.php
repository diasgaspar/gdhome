<?php

namespace gdhome\HomeFunctions;
use gdhome\Db\IDbAdapter as IDbAdapter;
use gdhome\HomeFunctions\DoorRecorder as DoorRecorder;
use gdhome\models\DoorRecord;
use gdhome\models\MeasRecord;
use gdhome\PHPMailer\MailSender as MailSender;
use gdhome\HomeVars as HomeVars;
use PDO;
use DateTime, DateInterval, DateTimeZone;

class HomeAggregator
{

	 /**
     * @var \gdhome\Db\IDbAdapter
     */
    protected $db;
    
    //Persistence for data in DoorData (RAW)
    protected $DoorDataRawPersistence;
    
     //Persistence for data in RawData (RAW)
    protected $RawDataPersistence;

    /**
     * @param IDbAdapter $dbAdapter
     */
    public function __construct( IDbAdapter $dbAdapter )
    {
        $this->db = $dbAdapter;
        $this->DoorDataRawPersistence= HomeVars::PERSISTENCE_DOOR_DATA_RAW;
        $this->RawDataPersistence= HomeVars::PERSISTENCE_MEAS_DATA_RAW;
    }
    
   /**
     * Aggregates DoorData Raw Table into DoorDataHourly table and deletes old data from DoorData Raw
     * @param       $pdo 
     * 
    */
	public function doDoorDataMaintenance(){
		$pdo=$this->db->getConnection();
		try {  
		  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		  $pdo->beginTransaction();
				$this->aggregateDoorDataHour($pdo);
				$this->deleteOldDoorDataRawRecords($pdo);
		  $pdo->commit();
		  
		} catch (Exception $e) {
		  $pdo->rollBack();
		  echo "Failed: " . $e->getMessage();
		}		
		
	} 
	
	
	  /**
     * Aggregates RawData Table into RawDataHourly table and deletes old data from RawData Raw
     * @param       $pdo 
     * 
    */
	public function doRawDataMaintenance(){
            
		$pdo=$this->db->getConnection();
		try {  
		  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		  $pdo->beginTransaction();
				$this->aggregateRawDataHour($pdo);
				$this->aggregateRawDataDaily($pdo);
				$this->deleteOldRawDataRecords($pdo);
		  $pdo->commit();
		  
		} catch (Exception $e) {
		  $pdo->rollBack();
		  echo "Failed: " . $e->getMessage();
		}		
		
	} 
       /**
        * sends daily report with door events 
        * @return int
        */     
       public function sendDailyReport(){
        $subject="gdhome daily report";
        $doorRecorder= new DoorRecorder($this->db);
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$dtDayAgo->sub(new DateInterval("P1D"));
        
        $doorEventsArray=$doorRecorder->getDoorEventsBetweenTimeInterval($dtDayAgo, $dtNow);
        $eventsMessage="\n";
        //var_dump($doorEventsArray);
         if( count( $doorEventsArray ) > 0 )
        {
            foreach( $doorEventsArray as $doorRecord )
            {
                $eventsMessage=$eventsMessage.$doorRecord->getTimeStamp()."\n";

            }
        }   
        $message= "Good Morning,
          This is the daily report for yesterday's door events:"
          .$eventsMessage.

         "(gdhome agent)";
        $mailer=new MailSender();
        
        
        foreach(HomeVars::USER_EMAIL_ACCOUNTS as $name => $email) {
            //print "\nKey=" . $name . ", Value=" . $email;
            $mailer->send_mail($email, $name, $subject, $message);
            
        }
        //$mailer->send_mail("diasgaspar@gmail.com", "gaspar", $subject, $message);
        //print "\n".$message;
        $message=2;
        
        return $message;
       }     
 
    /**
     * Aggregates DoorData Raw Table into DoorDataHourly table
     * @param       $pdo 
     * 
    */
    public function aggregateDoorDataHour($pdo){
    
    		$doorRecords = $pdo->prepare("   			
					insert into DoorDataHourly (TimeStamp,Freq)
                    SELECT DATE_FORMAT( TimeStamp, '%Y-%m-%d %H:00' ) as 'TimeStampb', count( State ) AS 'Freq'
					FROM DoorData
					WHERE TimeStamp < (CURDATE()- INTERVAL ".$this->DoorDataRawPersistence." DAY)
					Group by TimeStampb;
					");
		    $doorRecords->execute();
			//$result = $doorRecords->fetchAll();
			//print_r($result);
        			    
		    
    }
  /**
     * Deletes old DoorData  from Raw Table 
     * @param       $pdo 
     * 
    */
 	public function deleteOldDoorDataRawRecords($pdo){
 		
 			$doorRecords = $pdo->prepare("   			
					Delete from DoorData WHERE TimeStamp < (CURDATE()- INTERVAL ".$this->DoorDataRawPersistence." DAY)
					");
		    $doorRecords->execute();
 	
 	}
 
 
   /**
     * Aggregates RawData Table into RawDataHourly table
     * @param       $pdo 
     * 
    */
    public function aggregateRawDataHour($pdo){
        
    		$measRecords = $pdo->prepare("   			
					insert into RawDataHourly (TimeStamp,Temp, RxI, RxPw, RxPwF,RxPwV)
						SELECT
						  DATE_FORMAT( TimeStamp, '%y-%m-%d %H:00:00' ) AS TimeStampb, 
						  avg( Temp ) AS 'Temp', 
						  avg( RxI ) AS 'RxI', 
						  avg( RxPw ) AS 'RxPw',
						  avg(CASE WHEN DATE_FORMAT( TimeStamp, '%H' )>=8 and DATE_FORMAT( TimeStamp, '%H' )<22 THEN RxPw ELSE 0 END) AS 'RxPwF',
						  avg(CASE WHEN DATE_FORMAT( TimeStamp, '%H' )>=8 and DATE_FORMAT( TimeStamp, '%H' )<22 THEN 0 ELSE RxPw END) AS 'RxPwV'
						FROM RawData
						WHERE TimeStamp < CURDATE( )
						AND TimeStamp > (CURDATE( ) - INTERVAL 1 DAY)
						GROUP BY TimeStampb;
					");
		    $measRecords->execute();
		    
    } 
    
    
       /**
     * Aggregates RawDataHourly Table into RawDataDaily table
     * @param       $pdo 
     * 
    */
    public function aggregateRawDataDaily($pdo){
    
    		$measRecords = $pdo->prepare("   			
					insert into RawDataDaily (TimeStamp,Temp,TempMax,TempMin, RxI, RxPwMax,RxPwMin,Ewh,EwhF,EwhV)
						SELECT DATE_FORMAT( TimeStamp, '%Y-%m-%d' ) AS 'TimeStamp',
						avg(Temp) as Temp,
						max(Temp) as TempMax,
						min(Temp) as TempMin,
						avg(RxI) as RxI,
						max(RxPw) as RxPwMax,
						min(RxPw) as RxPwMin,
						sum(RxPw) as 'Ewh',
						sum(RxPwF) As 'EwhF',
						sum(RxPwV) as 'EwhV'
						FROM RawDataHourly
						WHERE TimeStamp < CURDATE( )
						AND TimeStamp > (CURDATE( ) - INTERVAL 1 DAY)
						GROUP BY DATE_FORMAT( TimeStamp, '%Y-%m-%d' )
					");
		    $measRecords->execute();
		    
    } 
 
  /**
     * Deletes old RAwData  from Raw Table 
     * @param       $pdo 
     * 
    */
 	public function deleteOldRawDataRecords($pdo){
 		
 			$doorRecords = $pdo->prepare("   			
					Delete from RawData WHERE TimeStamp < (CURDATE()- INTERVAL ".($this->RawDataPersistence-1)." DAY)
					");
		    $doorRecords->execute();
 	
 	}
 
    /**
     * Fetch all meas in the DoorData Table
     *
     * @return array
    */
    public function getAllDoorRawRecords() {

        $doorRecords = $this->db->fetchAll("SELECT TimeStamp, State FROM DoorData");
        $doors = array();

        if( count( $doorRecords ) > 0 )
        {
            foreach( $doorRecords as $doorRecord )
            {
                $doorRec = new DoorRecord();
                $doorRec->setTimeStamp( $doorRecord[ 'TimeStamp' ] );
                $doorRec->setState( $doorRecord[ 'State' ] );
                $doors[] = $doorRecord;
            }
        }

        return $doors;
    }
    
       /**
     * Fetch all meas in the DoorDataDaily Table
     *
     * @return array
    */
    public function getAllDoorHourlyRecords() {

        $doorRecords = $this->db->fetchAll("SELECT TimeStamp, Freq FROM DoorDataHourly");
        $doors = array();

        if( count( $doorRecords ) > 0 )
        {
            foreach( $doorRecords as $doorRecord )
            {
                $doorRec = new DoorRecord();
                $doorRec->setTimeStamp( $doorRecord[ 'TimeStamp' ] );
                $doorRec->setFreq( $doorRecord[ 'Freq' ] );
                $doors[] = $doorRecord;
            }
        }

        return $doors;
    }
    
    public function fetchDoorHourlyByTimeStamp($dateYMDH00){
    	$doorRecord = $this->db->fetchOne("SELECT TimeStamp, Freq FROM DoorDataHourly where TimeStamp='".$dateYMDH00."'");
    	$doorRec = new DoorRecord();
      $doorRec->setTimeStamp( $doorRecord[ 'TimeStamp' ] );
      $doorRec->setFreq( $doorRecord[ 'Freq' ] );
      return $doorRec;
    }
    
    
    public function fetchRawHourlyByTimeStamp($dateYMDH00){
    	$measRecord = $this->db->fetchOne("SELECT TimeStamp, Temp, RxI, RxPw, RxPwF, RxPwV FROM RawDataHourly where TimeStamp='".$dateYMDH00."'");
    	$measRec = new MeasRecord();
      $measRec->setTimeStamp( $measRecord[ 'TimeStamp' ] );
      $measRec->setTemp( $measRecord[ 'Temp' ] );
      $measRec->setRxI( $measRecord[ 'RxI' ] );
      $measRec->setRxPw( $measRecord[ 'RxPw' ] );
      $measRec->setRxPwF( $measRecord[ 'RxPwF' ] );
      $measRec->setRxPwV( $measRecord[ 'RxPwV' ] );
      return $measRec;
    }
    
    public function fetchRawDailyByTimeStamp($dateYMDH00){
    	$measRecord = $this->db->fetchOne("SELECT TimeStamp,Temp,TempMax,TempMin, RxI, RxPwMax,RxPwMin,Ewh,EwhF,EwhV FROM RawDataDaily where TimeStamp='".$dateYMDH00."'");
    	$measRec = new MeasRecord();
      $measRec->setTimeStamp( $measRecord[ 'TimeStamp' ] );
      $measRec->setTemp( $measRecord[ 'Temp' ] );
      $measRec->setTempMax( $measRecord[ 'TempMax' ] );
      $measRec->setTempMin( $measRecord[ 'TempMin' ] );
      $measRec->setRxI( $measRecord[ 'RxI' ] );
      $measRec->setRxPwMax( $measRecord[ 'RxPwMax' ] );
      $measRec->setRxPwMin( $measRecord[ 'RxPwMin' ] );
      $measRec->setEwh( $measRecord[ 'Ewh' ] );
      $measRec->setEwhF( $measRecord[ 'EwhF' ] );
      $measRec->setEwhV( $measRecord[ 'EwhV' ] );
      
      return $measRec;
    }
    
 	   /**
     * Fetch all meas in the RawData Table
     *
     * @return array
    */
    public function getAllRawDataRecords() {

        $measRecords = $this->db->fetchAll('SELECT TimeStamp, Temp, RxI, RxPw FROM RawData');
        $meas = array();

        if( count( $measRecords ) > 0 )
        {
            foreach( $measRecords as $measRecord )
            {
                $measRec = new MeasRecord();
                $measRec->setTimeStamp( $measRecord[ 'TimeStamp' ] );
                $measRec->setTemp( $measRecord[ 'Temp' ] );
                $measRec->setRxI( $measRecord[ 'RxI' ] );
                $measRec->setRxPw( $measRecord[ 'RxPw' ] );
                $meas[] = $measRecord;
            }
        }

        return $meas;
    }
    
    
     	   /**
     * Fetch all meas in the RawDataHourly Table
     *
     * @return array
    */
    public function getAllRawDataHourRecords() {

        $measRecords = $this->db->fetchAll('SELECT TimeStamp, Temp, RxI, RxPw, RxPwF, RxPwV FROM RawDataHourly');
        $meas = array();

        if( count( $measRecords ) > 0 )
        {
            foreach( $measRecords as $measRecord )
            {
                $measRec = new MeasRecord();
                $measRec->setTimeStamp( $measRecord[ 'TimeStamp' ] );
                $measRec->setTemp( $measRecord[ 'Temp' ] );
                $measRec->setRxI( $measRecord[ 'RxI' ] );
                $measRec->setRxPw( $measRecord[ 'RxPw' ] );
                $measRec->setRxPwF( $measRecord[ 'RxPwF' ] );
                $measRec->setRxPwV( $measRecord[ 'RxPwV' ] );
                $meas[] = $measRecord;
            }
        }

        return $meas;
    }

    
    
}