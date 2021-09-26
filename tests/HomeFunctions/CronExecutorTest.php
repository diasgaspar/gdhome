<?php
namespace gdhome\Tests\HomeFunctions;

use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use gdhome\HomeFunctions\CronExecutor as CronExecutor;
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use DateTime, DateInterval, DateTimeZone;

class CronExecutorTest extends DatabaseTestCase 
{
	 protected $db;
	 
	 protected $dtMonthAgo,$dtNow;
	 
	 protected $dataSetUpArray;
        
   /* protected function getConnection()
    {
        $this->db = new PDO('sqlite:' . __DIR__ . '/../whome.db');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->createDefaultDBConnection($this->db, 'testdb');
    }*/

/**
 * Method executed before each test
 *
 * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
 */
	protected function getSetUpOperation()
	{		
		$this->cleanTables('DoorDataHourly');
		$this->cleanTables('RawDataHourly');
		$this->cleanTables('RawDataDaily');
    	
    	
		$this->dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
		$this->dtMonthAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
		//$this->dtMonthAgo->sub(new DateInterval("P1M"));
                $this->dtMonthAgo->sub(new DateInterval("P".(\gdhome\HomeVars::PERSISTENCE_MEAS_DATA_RAW+2)."D"));
		$this->dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
		$this->dtDayAgo->sub(new DateInterval("P1D"));
			
	  
		
		$this->dataSetUpArray= new ArrayDataSet ( array(
         'DoorData' => array(
             array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:27:14', 'State' => 1 ),
             array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:40:19', 'State' => 1 ),
             array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'13:02:10', 'State' => 1 ),
             array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'11:27:14', 'State' => 1 ),
             array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'12:20:19', 'State' => 1 ),
             array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'13:02:10', 'State' => 1 )
         ),
         'RawData'=> array(
         
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:00:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:05:00', 'Temp' => 20, 'RxI'=>11, 'RxPw'=>231 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:10:00', 'Temp' => 20, 'RxI'=>11, 'RxPw'=>231 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:15:00', 'Temp' => 20, 'RxI'=>12, 'RxPw'=>232 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:20:00', 'Temp' => 20, 'RxI'=>12, 'RxPw'=>232 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:25:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:30:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:35:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:40:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:45:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:50:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:55:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
         	
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:00:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:05:00', 'Temp' => 21, 'RxI'=>11, 'RxPw'=>231 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:10:00', 'Temp' => 21, 'RxI'=>11, 'RxPw'=>231 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:15:00', 'Temp' => 21, 'RxI'=>12, 'RxPw'=>232 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:20:00', 'Temp' => 21, 'RxI'=>12, 'RxPw'=>232 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:25:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:30:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:35:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:40:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:45:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:50:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'09:55:00', 'Temp' => 21, 'RxI'=>10, 'RxPw'=>230 ),
         	
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:00:00', 'Temp' => 22, 'RxI'=>15, 'RxPw'=>235 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:05:00', 'Temp' => 22, 'RxI'=>15, 'RxPw'=>235 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:10:00', 'Temp' => 22, 'RxI'=>15, 'RxPw'=>235 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:15:00', 'Temp' => 22, 'RxI'=>15, 'RxPw'=>235 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:20:00', 'Temp' => 22, 'RxI'=>15, 'RxPw'=>235 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:25:00', 'Temp' => 22, 'RxI'=>16, 'RxPw'=>236 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:30:00', 'Temp' => 22, 'RxI'=>17, 'RxPw'=>237 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:35:00', 'Temp' => 22, 'RxI'=>17, 'RxPw'=>237 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:40:00', 'Temp' => 22, 'RxI'=>17, 'RxPw'=>237 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:45:00', 'Temp' => 122, 'RxI'=>17, 'RxPw'=>237 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:50:00', 'Temp' => 22, 'RxI'=>18, 'RxPw'=>238 ),
         	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'22:55:00', 'Temp' => 220, 'RxI'=>18, 'RxPw'=>238 )
         )
         
     ));
     
 
    	
    	
   	 return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
	}


    protected function getDataSet()
    {		
        //return $this->createFlatXMLDataSet(__DIR__ . '/DoorData.xml');    
        return  $this->dataSetUpArray ;
    }
    
  
    
    public function testDailyCronChecker()
    {	
     	
        $cronExecutor = new CronExecutor($this->getAdapter());
        
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        
	$minTime=200;
        $maxTime=205;
        $dt1=new DateTime($dtNow->format("Y-m-d ").'02:00:00');
        $this->assertEquals(true, $cronExecutor->isItTimeToRunTasks($dt1, $minTime,$maxTime));
        $dt2=new DateTime($dtNow->format("Y-m-d ").'02:04:59');
        $this->assertEquals(true, $cronExecutor->isItTimeToRunTasks($dt2,$minTime, $maxTime));
        $dt3=new DateTime($dtNow->format("Y-m-d ").'02:05:00');
        $this->assertEquals(false, $cronExecutor->isItTimeToRunTasks($dt3,$minTime, $maxTime));
        
       
        
    }
    
    public function testCronRunner(){
    
    	$cronExecutor = new CronExecutor($this->getAdapter());  
       
      $meas=$cronExecutor->gethomeAggregator()->getAllRawDataRecords();
    	$this->assertEquals(36, count($meas));    	//36: total of records in DoorData Table before doDoorDataMaintenance
      
		$doors=$cronExecutor->getHomeAggregator()->getAllDoorRawRecords();
    	$this->assertEquals(6, count($doors));    	//6: total of records in DoorData Table before doDoorDataMaintenance

        //-------Run cron-----------------

		$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
		$cronExecutor->runCron(new DateTime($dtNow->format("Y-m-d ").'02:04:59'));    	
    	//--------------------------------
    	
    	$doorRecord = $cronExecutor->getHomeAggregator()->fetchDoorHourlyByTimeStamp($this->dtMonthAgo->format("Y-m-d ").'11:00:00');
    	$this->assertEquals(2,$doorRecord->getFreq() );
    	
    	$doors=$cronExecutor->getHomeAggregator()->getAllDoorRawRecords();
    	$this->assertEquals(3, count($doors));//3: total of records in DoorData Table after doDoorDataMaintenance
    	
    	$meas = $cronExecutor->getHomeAggregator()->fetchRawHourlyByTimeStamp($this->dtDayAgo->format("Y-m-d ").'09:00:00');
    	$this->assertEquals($this->dataSetUpArray->arrayOperation("RawData","Y-m-d H",$this->dtDayAgo->format("Y-m-d ")."09","Temp")["avg"],$meas->getTemp() );//=21
    	
    	$meas=$cronExecutor->getHomeAggregator()->getAllRawDataRecords();
    	$this->assertEquals(24, count($meas));//24: total of records in RawDAta Table after doRawDataMaintenance
        
        
        //-------Run cron to test report sender by email-----------------

		$dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
		$message=$cronExecutor->runCron(new DateTime($dtNow->format("Y-m-d ").'08:03:59'));    	
                $this->assertEquals(2, $message);
                
                //not supposed to send daily report outside the time window
                $message=$cronExecutor->runCron(new DateTime($dtNow->format("Y-m-d ").'08:53:59'));    	
                $this->assertEquals(0, $message);
    	//--------------------------------
  
    }
    
 
    
    
 /**
 * Method executed after each test
 *
 * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
 */
	protected function getTearDownOperation()
	{
		
    	//return PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE();
    	return PHPUnit_Extensions_Database_Operation_Factory::NONE();
	}

    
   /* public static function tearDownAfterClass()
    {
        $this->conn === null;
    }*/
}