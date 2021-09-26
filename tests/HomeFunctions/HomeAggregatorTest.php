<?php
namespace gdhome\Tests\HomeFunctions;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator;
use gdhome\Tests\HomeFunctions\HomeRecorderTest as HomeRecorderTest; 
use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
//use gdhome\HomeVars as HomeVars;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use PDO, PDOException;
use DateTime, DateInterval, DateTimeZone;
use PHPUnit_Extensions_Database_Operation_Factory;

class HomeAggregatorTest extends DatabaseTestCase //\PHPUnit_Extensions_Database_TestCase
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
    	//TRUNCATE the table mentionned in the dataSet, then re-insert the content of the dataset.
    	
			$this->dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
			$this->dtMonthAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
			//$this->dtMonthAgo->sub(new DateInterval("P1M"));
                        $this->dtMonthAgo->sub(new DateInterval("P".(\gdhome\HomeVars::PERSISTENCE_MEAS_DATA_RAW+2)."D"));
			$this->dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
			$this->dtDayAgo->sub(new DateInterval("P1D"));
			
			//echo $dtMonthAgo->format("Y-m-d H:i") ."\n";  
			
        $this->dataSetUpArray= new ArrayDataSet ( array(
            'DoorData' => array(
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:27:14', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:40:19', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'13:02:10', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'08:02:10', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'08:40:59', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'13:00:23', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'17:06:43', 'State' => 1 ),
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
        
        /*$itera=$dataSetUpArray->createIterator();
        foreach ($itera as $key=>$val)
				echo $key.":".$val."\n";
        */
        
        //$arr=$dataSetUpArray->getTable('DoorData');
        //var_dump($arr->getRow(1));    	
    	
    	
    	
   	 return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
	}


    protected function getDataSet()
    {		
        //return $this->createFlatXMLDataSet(__DIR__ . '/DoorData.xml');    
        return  $this->dataSetUpArray ;
    }
    
  
    
    public function testSayHello()
    {	
     	
        $homeAggregator = new HomeAggregator($this->getAdapter());
        $doors = $homeAggregator->getAllDoorRawRecords();
        
        $this->assertEquals(10, count($doors));
        
    }
    
 
    
    public function testDoorDataAggregations(){
    	
    	$homeAggregator = new HomeAggregator($this->getAdapter());
	$doors=$homeAggregator->getAllDoorRawRecords();
    	$this->assertEquals(10, count($doors));    	//6: total of records in DoorData Table before doDoorDataMaintenance
    	
    	$homeAggregator->doDoorDataMaintenance(); //doDoorDataMaintenance aggregates doordataraw into doordatahourly and removes records older than 1 month (3 recs)
    	
    	$doors = $homeAggregator->getAllDoorHourlyRecords();
    	$this->assertEquals(2, count($doors)); //two records in aggregated table
    	$doorRecord = $homeAggregator->fetchDoorHourlyByTimeStamp($this->dtMonthAgo->format("Y-m-d ").'11:00:00');
    	$this->assertEquals(2,$doorRecord->getFreq() );
    	
    	$doors=$homeAggregator->getAllDoorRawRecords();
    	$this->assertEquals(7, count($doors));//3: total of records in DoorData Table after doDoorDataMaintenance
    	
    }
    
    
    public function testRawDataAggregations(){
    	
    	$homeAggregator = new HomeAggregator($this->getAdapter());
		$meas=$homeAggregator->getAllRawDataRecords();
    	$this->assertEquals(36, count($meas));    	//36: total of records in DoorData Table before doDoorDataMaintenance
    	
    	$homeAggregator->doRawDataMaintenance(); //doRawDataMaintenance aggregates rawDAta into Rawdatahourly and removes records older than 1 month 
    	
    	$meas = $homeAggregator->getAllRawDataHourRecords();
    	$this->assertEquals(2, count($meas)); //two records in aggregated table
    	
    	$meas = $homeAggregator->fetchRawHourlyByTimeStamp($this->dtDayAgo->format("Y-m-d ").'22:00:00');
    	$this->assertEquals($this->dataSetUpArray->arrayOperation("RawData","Y-m-d H",$this->dtDayAgo->format("Y-m-d ")."22","Temp")["avg"],$meas->getTemp() );//=22
    	
    	$meas = $homeAggregator->fetchRawHourlyByTimeStamp($this->dtDayAgo->format("Y-m-d ").'09:00:00');
    	$this->assertEquals($this->dataSetUpArray->arrayOperation("RawData","Y-m-d H",$this->dtDayAgo->format("Y-m-d ")."09","Temp")["avg"],$meas->getTemp() );//=21
    	
    	$meas=$homeAggregator->getAllRawDataRecords();
    	$this->assertEquals(24, count($meas));//24: total of records in RawDAta Table after doRawDataMaintenance
    	
		$meas = $homeAggregator->fetchRawDailyByTimeStamp($this->dtDayAgo->format("Y-m-d ").'00:00:00');
		//var_dump($this->arrayOperation("RawData","Y-m-d",$this->dtDayAgo->format("Y-m-d"),"Temp"));
    	$this->assertEquals($this->dataSetUpArray->arrayOperation("RawData","Y-m-d",$this->dtDayAgo->format("Y-m-d"),"Temp")["avg"],$meas->getTemp() );//=34
    	$ewh=
    		$this->dataSetUpArray->arrayOperation("RawData","Y-m-d H",$this->dtDayAgo->format("Y-m-d ")."22","RxPw")["avg"]
    		+$this->dataSetUpArray->arrayOperation("RawData","Y-m-d H",$this->dtDayAgo->format("Y-m-d ")."09","RxPw")["avg"];
    	$this->assertEquals($ewh,$meas->getEwh() );   	
   	
    }

    public function testSendReportEmail(){
        $homeAggregator = new HomeAggregator($this->getAdapter());
        $message=$homeAggregator->sendDailyReport();
        $this->assertEquals(2,$message);  
        
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