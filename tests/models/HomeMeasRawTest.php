<?php
namespace gdhome\Tests\models;
use gdhome\models\HomeMeasRaw as HomeMeasRaw;
use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use DateTime, DateInterval, DateTimeZone;

class HomeMeasRawTest extends DatabaseTestCase {

    protected $dataSetUpArray;
    protected $homeMeasRaw;


    protected function getSetUpOperation()
    {		
            
            $this->cleanTables('RawDataHourly');
            $this->cleanTables('RawDataDaily');
            $this->cleanTables('RawData');
            //TRUNCATE the table mentionned in the dataSet, then re-insert the content of the dataset.


            $this->dataSetUpArray= $this->createArrayOfData();
            //$this->homeMeasRaw = new HomeMeasRaw();
            return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }
        
    protected function getDataSet()
    {		
        return  $this->dataSetUpArray ;
    }
        
    public function tearDown() {
       return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }
    
    public function testGetLatestRecord() {
        $homeMeas=new HomeMeasRaw();
        $latestHomeMeas=$homeMeas->getLatestRecord();
        $latestTimeStamp= $this->dataSetUpArray->getLatestRow("RawData")['TimeStamp'];
         $this->assertEquals($latestTimeStamp, $latestHomeMeas->getTimeStamp());
         $this->assertEquals($this->dataSetUpArray->getLatestRow("RawData")['RxI'], $latestHomeMeas->getRxI());
         $this->assertEquals($this->dataSetUpArray->getLatestRow("RawData")['RxPw'], $latestHomeMeas->getRxPw());
    }
    
    public function testOverlay(){
        
        $homeMeas=new HomeMeasRaw();
        
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayAgo->sub(new DateInterval("P1D"));
        $durationDays=1;
        $overlayHomeMeas=$homeMeas->getOverlayed($dtNow->format("Y-m-d"), $dtDayAgo->format("Y-m-d"), $durationDays);
        $this->assertEquals($dtNow->format("Y-m-d ")."00:15:00",$overlayHomeMeas["00:15"]['d1']->getTimeStamp());
        $homeMeasRec= $overlayHomeMeas["00:15"]['d1'];
        //print $overlayHomeMeas["00:15"]['d1']->getTimeStamp();
        //var_dump($overlayHomeMeas);
        /*
        foreach ($overlayHomeMeas as $key => $value) {
       // $arr[3] will be updated with each value from $arr...
            print $key . " ".$value['d1']->getTimeStamp()."\n" ;
                //var_dump($value);
        }*/
       
        
    }
    
    public function testEnergyCalculation(){
        $homeMeas=new HomeMeasRaw();
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayTomorrow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayTomorrow->add(new DateInterval("P1D"));
        $energy=$homeMeas->getEnergySpent($dtNow->format("Y-m-d"), $dtDayTomorrow->format("Y-m-d"));
        //print($energy['Ewh']);
    }
    
    public function testEnergyCostCalculation(){
        $homeMeas=new HomeMeasRaw();
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        
        $dtDayTomorrow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayTomorrow->add(new DateInterval("P1D"));
        
        $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayAgo->sub(new DateInterval("P1D"));
        
        $cost=$homeMeas->getEnergyCostBiHourlyTarif($dtNow->format("Y-m-d"),$dtDayTomorrow->format("Y-m-d"));
        $this->assertEquals(0.0649,number_format($cost['cost'],4));
        $this->assertEquals(1,$cost['numDays']);
        
        $costYesterday=$homeMeas->getEnergyCostBiHourlyTarif($dtDayAgo->format("Y-m-d"),$dtNow->format("Y-m-d"));
        $this->assertEquals(0.0332,number_format($costYesterday['cost'],4));
        $this->assertEquals(1,$costYesterday['numDays']);
        
        
        $cost2Days=$homeMeas->getEnergyCostBiHourlyTarif($dtDayAgo->format("Y-m-d"),$dtDayTomorrow->format("Y-m-d"));
        $this->assertEquals(0.0982,number_format($cost2Days['cost'],4));
        $this->assertEquals(2,$cost2Days['numDays']);
        //var_dump($cost);
       //print  number_format($cost['cost'],4);
               
        
        
        //print $dtMonthAgo->format("Y-m-d");
    }
    
    public function testPreviousDateAtSpecificDayCalculation(){
        $homeMeas=new HomeMeasRaw();
        $dtest= new DateTime('2017-01-1');
        $this->assertEquals('2016-12-15',$homeMeas->calculatePreviousDateAtSpecificDay($dtest, \gdhome\HomeVars::ENERGY_READING_DAY)->format("Y-m-d"));
        $dtest= new DateTime('2017-02-1');
        $this->assertEquals('2017-01-15',$homeMeas->calculatePreviousDateAtSpecificDay($dtest, \gdhome\HomeVars::ENERGY_READING_DAY)->format("Y-m-d"));
        $dtest= new DateTime('2017-04-19');
        $this->assertEquals('2017-04-15',$homeMeas->calculatePreviousDateAtSpecificDay($dtest, \gdhome\HomeVars::ENERGY_READING_DAY)->format("Y-m-d"));
        $dtest= new DateTime('2017-04-10');
        $this->assertEquals('2017-03-15',$homeMeas->calculatePreviousDateAtSpecificDay($dtest, \gdhome\HomeVars::ENERGY_READING_DAY)->format("Y-m-d"));
        $dtest= new DateTime('2017-04-15');
        $this->assertEquals('2017-04-15',$homeMeas->calculatePreviousDateAtSpecificDay($dtest, \gdhome\HomeVars::ENERGY_READING_DAY)->format("Y-m-d"));
 
    }
    
    public function testPreviousMonthChargingDay(){
        $homeMeas=new HomeMeasRaw();
        $dtest=new DateTime('2017-01-1');
        $monthAgoPeriodDates=$homeMeas->calculateMonthAgoChargingPeriod($dtest);
        $this->assertEquals('2016-11-15',$monthAgoPeriodDates['beginDay']->format("Y-m-d"));
        $this->assertEquals('2016-12-14',$monthAgoPeriodDates['endDay']->format("Y-m-d"));
        
        $dtest=new DateTime('2017-04-14');
        $monthAgoPeriodDates=$homeMeas->calculateMonthAgoChargingPeriod($dtest);
        $this->assertEquals('2017-02-15',$monthAgoPeriodDates['beginDay']->format("Y-m-d"));
        $this->assertEquals('2017-03-14',$monthAgoPeriodDates['endDay']->format("Y-m-d"));
       
        
        $dtest=new DateTime('2017-04-15');
        $monthAgoPeriodDates=$homeMeas->calculateMonthAgoChargingPeriod($dtest);
        $this->assertEquals('2017-03-15',$monthAgoPeriodDates['beginDay']->format("Y-m-d"));
        $this->assertEquals('2017-04-14',$monthAgoPeriodDates['endDay']->format("Y-m-d"));
    }
    
    public function testEnergyCostCalculationFromPreviousChargingDay() {
        $homeMeas=new HomeMeasRaw();
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        
        $dtDayTomorrow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayTomorrow->add(new DateInterval("P1D"));
        $previousDateAtSpecificDayCalculation=$homeMeas->calculatePreviousDateAtSpecificDay($dtNow, \gdhome\HomeVars::ENERGY_READING_DAY);
        //print $previousDateAtSpecificDayCalculation->format("Y-m-d")."-> ".$dtDayTomorrow->format("Y-m-d");
        $costFromPreviousChargingDay=$homeMeas->getEnergyCostBiHourlyTarif($previousDateAtSpecificDayCalculation->format("Y-m-d"), $dtDayTomorrow->format("Y-m-d"));
        $this->assertEquals(0.0982,number_format($costFromPreviousChargingDay['cost'],4));
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
            
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:00:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>23 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:05:00', 'Temp' => 20, 'RxI'=>11, 'RxPw'=>21 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:10:00', 'Temp' => 20, 'RxI'=>11, 'RxPw'=>31 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:15:00', 'Temp' => 20, 'RxI'=>12, 'RxPw'=>32 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:20:00', 'Temp' => 20, 'RxI'=>12, 'RxPw'=>22 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:25:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:30:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:35:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>330 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:40:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>430 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:45:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>2630 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:50:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>2660 ),
            	array( 'TimeStamp' => $this->dtMonthAgo->format("Y-m-d ").'07:55:00', 'Temp' => 20, 'RxI'=>10, 'RxPw'=>230 ),
            	
            	array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:00:00','Temp'=>17, 'RxI'=>13, 'RxPw'=>130),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:05:00','Temp'=>17, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:10:00','Temp'=>17, 'RxI'=>15, 'RxPw'=>150),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:15:00','Temp'=>16, 'RxI'=>18, 'RxPw'=>180),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:20:00','Temp'=>17, 'RxI'=>20, 'RxPw'=>200),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:25:00','Temp'=>17, 'RxI'=>28, 'RxPw'=>280),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:30:00','Temp'=>16, 'RxI'=>28, 'RxPw'=>280),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:35:00','Temp'=>16, 'RxI'=>23, 'RxPw'=>230),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:40:00','Temp'=>15, 'RxI'=>18, 'RxPw'=>180),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:45:00','Temp'=>13, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:50:00','Temp'=>11, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'00:55:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:00:00','Temp'=>12, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:05:00','Temp'=>13, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:10:00','Temp'=>15, 'RxI'=>7, 'RxPw'=>70),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:15:00','Temp'=>15, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:20:00','Temp'=>14, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:25:00','Temp'=>16, 'RxI'=>10, 'RxPw'=>100),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:30:00','Temp'=>15, 'RxI'=>17, 'RxPw'=>170),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:35:00','Temp'=>16, 'RxI'=>13, 'RxPw'=>130),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:40:00','Temp'=>15, 'RxI'=>20, 'RxPw'=>200),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:45:00','Temp'=>17, 'RxI'=>13, 'RxPw'=>130),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:50:00','Temp'=>16, 'RxI'=>22, 'RxPw'=>220),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'01:55:00','Temp'=>14, 'RxI'=>18, 'RxPw'=>180),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:00:00','Temp'=>14, 'RxI'=>14, 'RxPw'=>140),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:05:00','Temp'=>13, 'RxI'=>22, 'RxPw'=>220),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:10:00','Temp'=>11, 'RxI'=>13, 'RxPw'=>130),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:15:00','Temp'=>13, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:20:00','Temp'=>12, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:25:00','Temp'=>11, 'RxI'=>14, 'RxPw'=>140),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:30:00','Temp'=>10, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:35:00','Temp'=>10, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:40:00','Temp'=>11, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:45:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:50:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtDayAgo->format("Y-m-d ").'02:55:00','Temp'=>13, 'RxI'=>1, 'RxPw'=>10),

                
                              
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:00:00','Temp'=>20, 'RxI'=>16, 'RxPw'=>160),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:05:00','Temp'=>21, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:10:00','Temp'=>20, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:15:00','Temp'=>21, 'RxI'=>7, 'RxPw'=>70),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:20:00','Temp'=>21, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:25:00','Temp'=>20, 'RxI'=>12, 'RxPw'=>120),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:30:00','Temp'=>21, 'RxI'=>16, 'RxPw'=>160),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:35:00','Temp'=>19, 'RxI'=>25, 'RxPw'=>250),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:40:00','Temp'=>20, 'RxI'=>19, 'RxPw'=>190),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:45:00','Temp'=>21, 'RxI'=>20, 'RxPw'=>200),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:50:00','Temp'=>21, 'RxI'=>27, 'RxPw'=>270),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'00:55:00','Temp'=>23, 'RxI'=>32, 'RxPw'=>320),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:00:00','Temp'=>22, 'RxI'=>34, 'RxPw'=>340),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:05:00','Temp'=>21, 'RxI'=>24, 'RxPw'=>240),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:10:00','Temp'=>19, 'RxI'=>17, 'RxPw'=>170),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:15:00','Temp'=>21, 'RxI'=>11, 'RxPw'=>110),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:20:00','Temp'=>22, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:25:00','Temp'=>22, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:30:00','Temp'=>21, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:35:00','Temp'=>23, 'RxI'=>12, 'RxPw'=>120),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:40:00','Temp'=>25, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:45:00','Temp'=>24, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:50:00','Temp'=>23, 'RxI'=>12, 'RxPw'=>120),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'01:55:00','Temp'=>24, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:00:00','Temp'=>25, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:05:00','Temp'=>27, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:10:00','Temp'=>27, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:15:00','Temp'=>26, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:20:00','Temp'=>25, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:25:00','Temp'=>24, 'RxI'=>11, 'RxPw'=>110),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:30:00','Temp'=>23, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:35:00','Temp'=>23, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:40:00','Temp'=>23, 'RxI'=>14, 'RxPw'=>140),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:45:00','Temp'=>22, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:50:00','Temp'=>21, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'02:55:00','Temp'=>21, 'RxI'=>12, 'RxPw'=>120),

array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:00:00','Temp'=>15, 'RxI'=>9, 'RxPw'=>90),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:05:00','Temp'=>14, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:10:00','Temp'=>13, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:15:00','Temp'=>11, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:20:00','Temp'=>11, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:25:00','Temp'=>12, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:30:00','Temp'=>12, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:35:00','Temp'=>10, 'RxI'=>7, 'RxPw'=>70),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:45:00','Temp'=>9, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:50:00','Temp'=>8, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'08:55:00','Temp'=>10, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:00:00','Temp'=>10, 'RxI'=>15, 'RxPw'=>150),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:05:00','Temp'=>8, 'RxI'=>7, 'RxPw'=>70),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:10:00','Temp'=>8, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:15:00','Temp'=>7, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:20:00','Temp'=>7, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:25:00','Temp'=>6, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:30:00','Temp'=>7, 'RxI'=>7, 'RxPw'=>70),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:35:00','Temp'=>8, 'RxI'=>14, 'RxPw'=>140),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:40:00','Temp'=>10, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:45:00','Temp'=>10, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:50:00','Temp'=>9, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'09:55:00','Temp'=>9, 'RxI'=>8, 'RxPw'=>80),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:00:00','Temp'=>7, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:05:00','Temp'=>8, 'RxI'=>11, 'RxPw'=>110),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:10:00','Temp'=>8, 'RxI'=>1, 'RxPw'=>10),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:15:00','Temp'=>6, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:20:00','Temp'=>5, 'RxI'=>2, 'RxPw'=>20),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:25:00','Temp'=>6, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:30:00','Temp'=>5, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:35:00','Temp'=>7, 'RxI'=>4, 'RxPw'=>40),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:40:00','Temp'=>8, 'RxI'=>6, 'RxPw'=>60),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:45:00','Temp'=>8, 'RxI'=>3, 'RxPw'=>30),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:50:00','Temp'=>6, 'RxI'=>5, 'RxPw'=>50),
array( 'TimeStamp' => $this->dtNow->format("Y-m-d ").'10:55:00','Temp'=>6, 'RxI'=>1, 'RxPw'=>10)


                

    
                
            )
            
        ));
        
        return $dataSetUpArray;
        
    }
    
}
