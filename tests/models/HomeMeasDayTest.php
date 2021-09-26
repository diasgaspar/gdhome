<?php
namespace gdhome\Tests\models;
use gdhome\models\HomeMeasDay as HomeMeasDay;
use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator;
use DateTime, DateInterval, DateTimeZone;

class HomeMeasDayTest extends DatabaseTestCase {

    protected $dataSetUpArray;


    protected function getSetUpOperation()
    {		
            $this->cleanTables('RawDataHourly');
            $this->cleanTables('RawDataDaily');
            $this->cleanTables('RawData');
            //TRUNCATE the table mentionned in the dataSet, then re-insert the content of the dataset.
                  
            $this->dataSetUpArray= $this->createArrayOfData();
            
            return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }
        
    protected function getDataSet()
    {	
        return  $this->dataSetUpArray ;
    }
    
         
   
    
 
    public function testLastMonthEnergyCalculation(){
        $homeAggregator = new HomeAggregator($this->getAdapter());
        $homeAggregator->doRawDataMaintenance(); //doRawDataMaintenance aggregates rawDAta into Rawdatahourly and removes records older than 1 month
 
        $homeMeas=new HomeMeasDay();
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $monthAgoChargingStartDate=$homeMeas->calculateMonthAgoChargingPeriod($dtNow)['beginDay'];
        $monthAgoChargingEndDate=$homeMeas->calculateMonthAgoChargingPeriod($dtNow)['endDay'];
        //print $monthAgoChargingStartDate->format("Y-m-d")."->". $monthAgoChargingEndDate->format("Y-m-d");
        $energy=$homeMeas->getEnergySpent($monthAgoChargingStartDate->format("Y-m-d"), $monthAgoChargingEndDate->format("Y-m-d"));
        $this->assertEquals($this->dataSetUpArray->arrayOperation("RawDataDaily","Y-m-d",$monthAgoChargingStartDate->format("Y-m"),"Ewh")["sum"],$homeMeas->getEwh() );
       
        $expectedCostF=$this->dataSetUpArray->arrayOperation("RawDataDaily","Y-m",$monthAgoChargingStartDate->format("Y-m"),"EwhF")["sum"]*\gdhome\HomeVars::ENERGY_COST_T2T3;
        $expectedCostV=$this->dataSetUpArray->arrayOperation("RawDataDaily","Y-m",$monthAgoChargingStartDate->format("Y-m"),"EwhV")["sum"]*\gdhome\HomeVars::ENERGY_COST_T1;
        
        $energyCost=$homeMeas->getEnergyCostBiHourlyTarif($monthAgoChargingStartDate->format("Y-m-d"), $monthAgoChargingEndDate->format("Y-m-d"));
        $this->assertEquals(($expectedCostF+$expectedCostV)/1000,$energyCost['cost']);
        
        $energyCost=$homeMeas->getEnergyCostBiHourlyTarif($monthAgoChargingStartDate->format("Y-m-d"), $monthAgoChargingEndDate->format("Y-m-d"));
        $this->assertEquals(($expectedCostV)/1000,$energyCost['costV']);
        
        $energyCost=$homeMeas->getEnergyCostBiHourlyTarif($monthAgoChargingStartDate->format("Y-m-d"), $monthAgoChargingEndDate->format("Y-m-d"));
        $this->assertEquals(($expectedCostF)/1000,$energyCost['costF']);
        
        $measList=$homeMeas->getRecordsBetweenTwoDates($monthAgoChargingStartDate->format("Y-m-d"), $monthAgoChargingEndDate->format("Y-m-d"));
        $this->assertEquals($this->dataSetUpArray->arrayOperation("RawDataDaily","Y-m",$monthAgoChargingStartDate->format("Y-m"),"Ewh")["count"], count($measList));
        
        $dt3MonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P3M"));
        $dt2MonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P2M"));
        //print $dt3MonthAgo->format("Y-m-d"). "-->".$dt2MonthAgo->format("Y-m-d");
        $measList=$homeMeas->getMonthlyRecordsBetweenTwoDates($dt3MonthAgo->format("Y-m-d"), $dt2MonthAgo->format("Y-m-d"));
        $this->assertEquals($this->dataSetUpArray->arrayOperation("RawDataDaily","Y-m",$dt2MonthAgo->format("Y-m"),"Ewh")["sum"], $measList[0]->getEwh());
        //print("COUNT=".count($measList));
        //print "PRINT".$measList[0]->getTimeStamp();

     
    }
    

    


    public function tearDown() {
       return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }
    
    protected function createArrayOfData(){
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $dtDayAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P1D"));;
	//$dtDayAgo->sub(new DateInterval("P1D"));
	$dtMonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P1M"));
	//$dtMonthAgo->sub(new DateInterval("P1M"));
	$dt2MonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P2M"));
        $dt3MonthAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P3M"));
        $dtMonthAgop1Day=(new DateTime())->setDate($dtMonthAgo->format("Y"),$dtMonthAgo->format("m"), $dtMonthAgo->format("d"))->add(new DateInterval("P1D"));
        //$dtMonthAgop1Day->setDate($dtMonthAgo->format("Y"),$dtMonthAgo->format("m"), $dtMonthAgo->format("d"));
	//$dtMonthAgop1Day->add(new DateInterval("P1D"));
        $dtMonthAgop2Day=(new DateTime())->setDate($dtMonthAgo->format("Y"),$dtMonthAgo->format("m"), $dtMonthAgo->format("d"))->add(new DateInterval("P2D"));
        $dtMonthAgop3Day=(new DateTime())->setDate($dtMonthAgo->format("Y"),$dtMonthAgo->format("m"), $dtMonthAgo->format("d"))->add(new DateInterval("P3D"));
        $dataSetUpArray= new ArrayDataSet ( array(
            'RawDataDaily'=> array(
                array( 'TimeStamp' => $dt3MonthAgo->format("Y-m").'-01 00:00:00', 'Temp' => 18, 'TempMax'=>21, 'TempMin'=>19, 'RxI'=>13, 'Ewh'=>13000, 'RxPwMax'=>2600, 'RxPwMin'=>140, 'EwhF'=>7000, 'EwhV'=>5000 ),
                array( 'TimeStamp' => $dt3MonthAgo->format("Y-m").'-02 00:00:00', 'Temp' => 18, 'TempMax'=>21, 'TempMin'=>19, 'RxI'=>13, 'Ewh'=>13000, 'RxPwMax'=>2600, 'RxPwMin'=>140, 'EwhF'=>7000, 'EwhV'=>5000 ),
                array( 'TimeStamp' => $dt2MonthAgo->format("Y-m-d ").'00:00:00', 'Temp' => 19, 'TempMax'=>22, 'TempMin'=>20, 'RxI'=>12, 'Ewh'=>12000, 'RxPwMax'=>2700, 'RxPwMin'=>160, 'EwhF'=>6500, 'EwhV'=>4500 ),
            	array( 'TimeStamp' => $dtMonthAgo->format("Y-m-d ").'00:00:00', 'Temp' => 20, 'TempMax'=>23, 'TempMin'=>21, 'RxI'=>10, 'Ewh'=>10000, 'RxPwMax'=>2500, 'RxPwMin'=>150, 'EwhF'=>6000, 'EwhV'=>4000 ),
                array( 'TimeStamp' => $dtMonthAgop1Day->format("Y-m-d ").'00:00:00', 'Temp' => 21, 'TempMax'=>24, 'TempMin'=>22, 'RxI'=>11, 'Ewh'=>11010, 'RxPwMax'=>3502, 'RxPwMin'=>160, 'EwhF'=>6000, 'EwhV'=>5000 ),
                array( 'TimeStamp' => $dtMonthAgop2Day->format("Y-m-d ").'00:00:00', 'Temp' => 22, 'TempMax'=>25, 'TempMin'=>23, 'RxI'=>12, 'Ewh'=>12020, 'RxPwMax'=>4503, 'RxPwMin'=>260, 'EwhF'=>7000, 'EwhV'=>6000 ),
                array( 'TimeStamp' => $dtMonthAgop3Day->format("Y-m-d ").'00:00:00', 'Temp' => 10, 'TempMax'=>22, 'TempMin'=>20, 'RxI'=>9, 'Ewh'=>9090, 'RxPwMax'=>1504, 'RxPwMin'=>60, 'EwhF'=>5000, 'EwhV'=>3000 )
     

            ),
            'RawData'=> array(
            
            	
            	
            	array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:00:00','Temp'=>17, 'RxI'=>13, 'RxPw'=>130),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:05:00','Temp'=>17, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:10:00','Temp'=>17, 'RxI'=>15, 'RxPw'=>150),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:15:00','Temp'=>16, 'RxI'=>18, 'RxPw'=>180),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:20:00','Temp'=>17, 'RxI'=>20, 'RxPw'=>200),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:25:00','Temp'=>17, 'RxI'=>28, 'RxPw'=>280),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:30:00','Temp'=>16, 'RxI'=>28, 'RxPw'=>280),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:35:00','Temp'=>16, 'RxI'=>23, 'RxPw'=>230),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:40:00','Temp'=>15, 'RxI'=>18, 'RxPw'=>180),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:45:00','Temp'=>13, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:50:00','Temp'=>11, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:55:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:00:00','Temp'=>12, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:05:00','Temp'=>13, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:10:00','Temp'=>15, 'RxI'=>7, 'RxPw'=>70),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:15:00','Temp'=>15, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:20:00','Temp'=>14, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:25:00','Temp'=>16, 'RxI'=>10, 'RxPw'=>100),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:30:00','Temp'=>15, 'RxI'=>17, 'RxPw'=>170),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:35:00','Temp'=>16, 'RxI'=>13, 'RxPw'=>130),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:40:00','Temp'=>15, 'RxI'=>20, 'RxPw'=>200),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:45:00','Temp'=>17, 'RxI'=>13, 'RxPw'=>130),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:50:00','Temp'=>16, 'RxI'=>22, 'RxPw'=>220),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:55:00','Temp'=>14, 'RxI'=>18, 'RxPw'=>180),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:00:00','Temp'=>14, 'RxI'=>14, 'RxPw'=>140),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:05:00','Temp'=>13, 'RxI'=>22, 'RxPw'=>220),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:10:00','Temp'=>11, 'RxI'=>13, 'RxPw'=>130),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:15:00','Temp'=>13, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:20:00','Temp'=>12, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:25:00','Temp'=>11, 'RxI'=>14, 'RxPw'=>140),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:30:00','Temp'=>10, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:35:00','Temp'=>10, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:40:00','Temp'=>11, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:45:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:50:00','Temp'=>12, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:55:00','Temp'=>13, 'RxI'=>1, 'RxPw'=>10),



                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:00:00','Temp'=>20, 'RxI'=>16, 'RxPw'=>160),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:05:00','Temp'=>21, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:10:00','Temp'=>20, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:15:00','Temp'=>21, 'RxI'=>7, 'RxPw'=>70),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:20:00','Temp'=>21, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:25:00','Temp'=>20, 'RxI'=>12, 'RxPw'=>120),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:30:00','Temp'=>21, 'RxI'=>16, 'RxPw'=>160),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:35:00','Temp'=>19, 'RxI'=>25, 'RxPw'=>250),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:40:00','Temp'=>20, 'RxI'=>19, 'RxPw'=>190),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:45:00','Temp'=>21, 'RxI'=>20, 'RxPw'=>200),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:50:00','Temp'=>21, 'RxI'=>27, 'RxPw'=>270),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'00:55:00','Temp'=>23, 'RxI'=>32, 'RxPw'=>320),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:00:00','Temp'=>22, 'RxI'=>34, 'RxPw'=>340),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:05:00','Temp'=>21, 'RxI'=>24, 'RxPw'=>240),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:10:00','Temp'=>19, 'RxI'=>17, 'RxPw'=>170),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:15:00','Temp'=>21, 'RxI'=>11, 'RxPw'=>110),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:20:00','Temp'=>22, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:25:00','Temp'=>22, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:30:00','Temp'=>21, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:35:00','Temp'=>23, 'RxI'=>12, 'RxPw'=>120),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:40:00','Temp'=>25, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:45:00','Temp'=>24, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:50:00','Temp'=>23, 'RxI'=>12, 'RxPw'=>120),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'01:55:00','Temp'=>24, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:00:00','Temp'=>25, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:05:00','Temp'=>27, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:10:00','Temp'=>27, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:15:00','Temp'=>26, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:20:00','Temp'=>25, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:25:00','Temp'=>24, 'RxI'=>11, 'RxPw'=>110),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:30:00','Temp'=>23, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:35:00','Temp'=>23, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:40:00','Temp'=>23, 'RxI'=>14, 'RxPw'=>140),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:45:00','Temp'=>22, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:50:00','Temp'=>21, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'02:55:00','Temp'=>21, 'RxI'=>12, 'RxPw'=>120),

                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:00:00','Temp'=>15, 'RxI'=>9, 'RxPw'=>90),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:05:00','Temp'=>14, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:10:00','Temp'=>13, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:15:00','Temp'=>11, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:20:00','Temp'=>11, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:25:00','Temp'=>12, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:30:00','Temp'=>12, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:35:00','Temp'=>10, 'RxI'=>7, 'RxPw'=>70),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:45:00','Temp'=>9, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:50:00','Temp'=>8, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'08:55:00','Temp'=>10, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:00:00','Temp'=>10, 'RxI'=>15, 'RxPw'=>150),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:05:00','Temp'=>8, 'RxI'=>7, 'RxPw'=>70),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:10:00','Temp'=>8, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:15:00','Temp'=>7, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:20:00','Temp'=>7, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:25:00','Temp'=>6, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:30:00','Temp'=>7, 'RxI'=>7, 'RxPw'=>70),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:35:00','Temp'=>8, 'RxI'=>14, 'RxPw'=>140),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:40:00','Temp'=>10, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:45:00','Temp'=>10, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:50:00','Temp'=>9, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'09:55:00','Temp'=>9, 'RxI'=>8, 'RxPw'=>80),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:00:00','Temp'=>7, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:05:00','Temp'=>8, 'RxI'=>11, 'RxPw'=>110),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:10:00','Temp'=>8, 'RxI'=>1, 'RxPw'=>10),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:15:00','Temp'=>6, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:20:00','Temp'=>5, 'RxI'=>2, 'RxPw'=>20),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:25:00','Temp'=>6, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:30:00','Temp'=>5, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:35:00','Temp'=>7, 'RxI'=>4, 'RxPw'=>40),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:40:00','Temp'=>8, 'RxI'=>6, 'RxPw'=>60),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:45:00','Temp'=>8, 'RxI'=>3, 'RxPw'=>30),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:50:00','Temp'=>6, 'RxI'=>5, 'RxPw'=>50),
                array( 'TimeStamp' => $dtNow->format("Y-m-d ").'10:55:00','Temp'=>6, 'RxI'=>1, 'RxPw'=>10)
                
            )
            
            
        ));
        
        return $dataSetUpArray;
        
    }
    
}
