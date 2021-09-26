<?php
namespace gdhome\Tests\HomeFunctions;
use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use gdhome\HomeFunctions\DoorRecorder as DoorRecorder;
use gdhome\models\DoorRecord as DoorRecord;
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use DateTime, DateInterval, DateTimeZone;
use gdhome\HomeVars as HomeVars;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DoorRecorderTest
 *
 * @author gaspar
 */
class DoorRecorderTest extends DatabaseTestCase {

    protected $dataSetUpArray;
    protected $doorRecorder;

/*    public function setUp() {
        $this->cleanTables('DoorDataHourly');
	$this->cleanTables('RawDataHourly');
	$this->cleanTables('RawDataDaily');
       // $this->createArrayOfData();
        
   	return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }
  */  
    	
    protected function getSetUpOperation()
    {		
        $this->cleanTables('DoorDataHourly');
        $this->cleanTables('RawDataHourly');
        $this->cleanTables('RawDataDaily');
    	//TRUNCATE the table mentionned in the dataSet, then re-insert the content of the dataset.
    	
			
        $this->dataSetUpArray= $this->createArrayOfData();
        $this->doorRecorder = new DoorRecorder($this->getAdapter());
   	return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }
    
    protected function getDataSet()
    {		
        //return $this->createFlatXMLDataSet(__DIR__ . '/DoorData.xml');    
        return  $this->dataSetUpArray ;
    }

    public function tearDown() {
        return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }


    public function testSimple() {
        
	$doorEvents=$this->doorRecorder->getAllRecords();
    	$this->assertEquals(11, count($doorEvents));
    }
    /**
     * The first door event will be stored because more that 30 secs have passed since last door event in DB
     * The second will not because 30 secs haven't passed yet
     */
    public function testInsertDoorEventTimeFiltered() {
        //$this->doorRecorder = new DoorRecorder($this->getAdapter());
        $dt1=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $doorEvent=1;
        $doorRec= new DoorRecord();
        $doorRec->setTimeStamp($dt1->format("Y-m-d H:i:s"));
        $doorRec->setState($doorEvent);
        $doorEvent = $this->doorRecorder->insertDoorEventTimeFiltered($doorRec, HomeVars::TIME_TO_WAIT_BEFORE_SEND_NEW_DOOR_NOTIFICATION);
        //print "\ndelta=\t".$delta;
        $doorRecordsAfterFirstEvent=$this->doorRecorder->getAllRecords();
    	$this->assertEquals(12, count($doorRecordsAfterFirstEvent));
        
        $dt2=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $doorRec2= new DoorRecord();
        $doorRec2->setTimeStamp($dt2->format("Y-m-d H:i:s"));
        $doorRec2->setState($doorEvent);
        $doorEvent2 = $this->doorRecorder->insertDoorEventTimeFiltered($doorRec2,HomeVars::TIME_TO_WAIT_BEFORE_SEND_NEW_DOOR_NOTIFICATION);
        //print "\ndelta=\t".$delta;
        $doorRecordsAfterSecondEvent=$this->doorRecorder->getAllRecords();
    	$this->assertEquals(12, count($doorRecordsAfterSecondEvent));//they must be the same as in previous check
        $message = "Line 1\r\nLine 2\r\nLine 3";
        //$this->doorRecorder->send_mail("diasgaspar@gmail.com", "gaspar", "Door event information");
        
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'08:29:00');
        $this->assertEquals(false, $this->doorRecorder->isItTimeToSendNotification($dtTest,830,1830));
        
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'08:30:00');
        $this->assertEquals(true, $this->doorRecorder->isItTimeToSendNotification($dtTest,830,1830));
        
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'18:29:00');
        $this->assertEquals(true, $this->doorRecorder->isItTimeToSendNotification($dtTest,830,1830));
        
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'18:30:00');
        $this->assertEquals(false, $this->doorRecorder->isItTimeToSendNotification($dtTest,830,1830));
        
        $dtTest=new DateTime($dtNow->format("Y-m-d ").'12:22:00');
        $this->assertEquals(true, $this->doorRecorder->isItTimeToSendNotification($dtTest,830,1830));
       // $this->doorRecorder->isItTimeToSendNotification($dtNow, $minTime, $maxTime)
       
    }
    
    public function testGetDoorEventsFromATimeInterval(){
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        //$dtbegin=new DateTime($dtNow->format("Y-m-d ").'08:31:00');
        
        $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$dtDayAgo->sub(new DateInterval("P1D"));
        $dt2DaysAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$dt2DaysAgo->sub(new DateInterval("P2D"));
        $doorEventsArray=$this->doorRecorder->getDoorEventsBetweenTimeInterval($dtDayAgo, $dtNow);
        $this->assertEquals(4,count($doorEventsArray));
        //var_dump($doorEventsArray);  
        
    }
   
    protected function createArrayOfData(){
        
    	$this->dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
	$this->dtMonthAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$this->dtMonthAgo->sub(new DateInterval("P1M"));
	$this->dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$this->dtDayAgo->sub(new DateInterval("P1D"));
        $dt31SecAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dt31SecAgo->sub(new DateInterval("PT00H00M31S"));
        
        $dt1HourAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dt1HourAgo->sub(new DateInterval("PT00H01M12S"));
        $dt2HourAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dt2HourAgo->sub(new DateInterval("PT00H02M21S"));
        $dt4HourAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dt4HourAgo->sub(new DateInterval("PT00H04M12S"));
        //print "\ndate now\t".$this->dtNow->format("Y-m-d H:i:s");
	//print "\ninserted\t".$dt15SecAgo->format("Y-m-d H:i:s")	;
         $deltaTime=$this->dtNow->getTimeStamp()-$dt31SecAgo->getTimeStamp();
        //print "\nDelta\t".$deltaTime;
			
        $dataSetUpArray= new ArrayDataSet ( array(
            'DoorData' => array(
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:27:14', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'11:40:19', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'13:02:10', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'08:02:10', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'08:40:59', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'13:00:23', 'State' => 1 ),
                array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'17:06:43', 'State' => 1 ),
                array( 'TimeStamp' => $dt1HourAgo->format("Y-m-d H:i:s"), 'State' => 1 ),
                array( 'TimeStamp' => $dt2HourAgo->format("Y-m-d H:i:s"), 'State' => 1 ),
                array( 'TimeStamp' => $dt4HourAgo->format("Y-m-d H:i:s"), 'State' => 1 ),
                array( 'TimeStamp' => $dt31SecAgo->format("Y-m-d H:i:s"), 'State' => 1 )
            )
            
        ));
        
        return $dataSetUpArray;
        
    }

}
