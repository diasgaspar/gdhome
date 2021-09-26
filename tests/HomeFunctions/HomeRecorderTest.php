<?php

namespace gdhome\tests\HomeFunctions;

use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use gdhome\HomeFunctions\HomeRecorder as HomeRecorder;
use gdhome\models\MeasRecord as MeasRecord;
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use DateTime, DateInterval, DateTimeZone;

class HomeRecorderTest extends DatabaseTestCase
{
        protected $dataSetUpArray;
        protected $homeRecorder;
    
	protected function getSetUpOperation()
        {		
            
            $this->cleanTables('RawDataHourly');
            $this->cleanTables('RawDataDaily');
            //TRUNCATE the table mentionned in the dataSet, then re-insert the content of the dataset.


            $this->dataSetUpArray= $this->createArrayOfData();
            $this->homeRecorder = new HomeRecorder($this->getAdapter());
            return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
        }
        
        protected function getDataSet()
        {		
            return  $this->dataSetUpArray ;
        }
        
        public function tearDown() {
            return PHPUnit_Extensions_Database_Operation_Factory::NONE();
        }
        
        public function testInsertHomeRecord() {
            $dtNow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
            $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
            $dtDayAgo->sub(new DateInterval("P1D"));
            $homeRecs=$this->homeRecorder->getAllRecords();
            //$this->assertEquals($this->dataSetUpArray->arrayOperation("RawData","Y-m-d",$this->dtDayAgo->format("Y-m-d"),"Temp")["count"], count($homeRecs));
            $this->assertEquals(36, count($homeRecs));
            
            $rxi=15;
            $measRec=new MeasRecord();
            $measRec->setTimeStampWith5MinStep($dtNow);
            $measRec->setTemp(23);
            $measRec->setRxI($rxi);
            $measRec->setRxPw((int)$rxi * 23);   
            $measRec=$this->homeRecorder->insert($measRec);
            $this->assertEquals(37, count($this->homeRecorder->getAllRecords()));
            
            
            $dif=($dtNow->format("i")%5);
            $dtNow->sub(new DateInterval('PT'.$dif.'M'));
            
            $measRec=$this->homeRecorder->getLatestRecord();
            $this->assertEquals($dtNow->format("Y-m-d H:i:00"), $measRec->getTimeStamp());
            
            
        }
        
        
        protected function createArrayOfData(){
        
    	$this->dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
	$this->dtMonthAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$this->dtMonthAgo->sub(new DateInterval("P1M"));
	$this->dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$this->dtDayAgo->sub(new DateInterval("P1D"));
        $dt31SecAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dt31SecAgo->sub(new DateInterval("PT00H00M31S"));
        //print "\ndate now\t".$this->dtNow->format("Y-m-d H:i:s");
	//print "\ninserted\t".$dt15SecAgo->format("Y-m-d H:i:s")	;
         $deltaTime=$this->dtNow->getTimeStamp()-$dt31SecAgo->getTimeStamp();
        //print "\nDelta\t".$deltaTime;
			
        $dataSetUpArray= new ArrayDataSet ( array(
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
        
        return $dataSetUpArray;
        
    }

}