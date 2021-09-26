<?php
namespace gdhome\Tests\models;
use gdhome\models\HomeMeasHour as HomeMeasHour;
use gdhome\Tests\Persistence\DatabaseTestCase as DatabaseTestCase; 
use PHPUnit_Extensions_Database_Operation_Factory;
use gdhome\Tests\Persistence\ArrayDataSet as ArrayDataSet;
use gdhome\HomeFunctions\HomeAggregator as HomeAggregator;
use DateTime, DateInterval, DateTimeZone;

class HomeMeasHourTest extends DatabaseTestCase {

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
    
         
   
    
 
    public function testHourlyMeas(){
 
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P70D"));

        $homeMeasHour= new HomeMeasHour();
        $homeMeasHourList=$homeMeasHour->getRecordsBetweenTwoDates($dtAgo->format("Y-m-d"), $dtNow->format("Y-m-d"));
        
        //$this->assertEquals($this->dataSetUpArray->arrayOperation("RawDataHourly","Y-m-d",$dtAgo->format("Y-m-d"),"RxPw")["count"], $homeMeasHourList[0]->getRxPw());
        //print("COUNT=".count($measList));
        //print "PRINT".$measList[0]->getTimeStamp();

     
    }
    

    


    public function tearDown() {
       return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }
    
    protected function createArrayOfData(){
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon')); 
        $dtDayAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P1D"));
        $dt2DayAgo=(new DateTime("now",new DateTimeZone('EUROPE/Lisbon')))->sub(new DateInterval("P2D"));

        $dataSetUpArray= new ArrayDataSet ( array(

            'RawDataHourly'=> array(
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'00:00:00','Temp'=>20, 'RxI'=>10, 'RxPw'=>100,'RxPwF'=>0,'RxPwV'=>100),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'01:00:00','Temp'=>19, 'RxI'=>2, 'RxPw'=>20,'RxPwF'=>0,'RxPwV'=>20),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'02:00:00','Temp'=>20, 'RxI'=>5, 'RxPw'=>50,'RxPwF'=>0,'RxPwV'=>50),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'03:00:00','Temp'=>22, 'RxI'=>9, 'RxPw'=>90,'RxPwF'=>0,'RxPwV'=>90),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'04:00:00','Temp'=>21, 'RxI'=>3, 'RxPw'=>30,'RxPwF'=>0,'RxPwV'=>30),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'05:00:00','Temp'=>21, 'RxI'=>8, 'RxPw'=>80,'RxPwF'=>0,'RxPwV'=>80),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'06:00:00','Temp'=>22, 'RxI'=>14, 'RxPw'=>140,'RxPwF'=>0,'RxPwV'=>140),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'07:00:00','Temp'=>21, 'RxI'=>8, 'RxPw'=>80,'RxPwF'=>0,'RxPwV'=>80),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'08:00:00','Temp'=>22, 'RxI'=>4, 'RxPw'=>40,'RxPwF'=>40,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'09:00:00','Temp'=>22, 'RxI'=>13, 'RxPw'=>130,'RxPwF'=>130,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'10:00:00','Temp'=>22, 'RxI'=>11, 'RxPw'=>110,'RxPwF'=>110,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'11:00:00','Temp'=>23, 'RxI'=>16, 'RxPw'=>160,'RxPwF'=>160,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'12:00:00','Temp'=>22, 'RxI'=>12, 'RxPw'=>120,'RxPwF'=>120,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'13:00:00','Temp'=>22, 'RxI'=>3, 'RxPw'=>30,'RxPwF'=>30,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'14:00:00','Temp'=>24, 'RxI'=>12, 'RxPw'=>120,'RxPwF'=>120,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'15:00:00','Temp'=>25, 'RxI'=>9, 'RxPw'=>90,'RxPwF'=>90,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'16:00:00','Temp'=>26, 'RxI'=>11, 'RxPw'=>110,'RxPwF'=>110,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'17:00:00','Temp'=>25, 'RxI'=>5, 'RxPw'=>50,'RxPwF'=>50,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'18:00:00','Temp'=>27, 'RxI'=>0, 'RxPw'=>0,'RxPwF'=>0,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'19:00:00','Temp'=>29, 'RxI'=>8, 'RxPw'=>80,'RxPwF'=>80,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'20:00:00','Temp'=>26, 'RxI'=>7, 'RxPw'=>70,'RxPwF'=>70,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'21:00:00','Temp'=>27, 'RxI'=>10, 'RxPw'=>100,'RxPwF'=>100,'RxPwV'=>0),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'22:00:00','Temp'=>27, 'RxI'=>18, 'RxPw'=>180,'RxPwF'=>180,'RxPwV'=>180),
                    array( 'TimeStamp' => $dtDayAgo->format("Y-m-d ").'23:00:00','Temp'=>27, 'RxI'=>27, 'RxPw'=>270,'RxPwF'=>270,'RxPwV'=>270),

                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'00:00:00','Temp'=>20, 'RxI'=>2, 'RxPw'=>20,'RxPwF'=>0,'RxPwV'=>20),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'01:00:00','Temp'=>20, 'RxI'=>0, 'RxPw'=>0,'RxPwF'=>0,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'02:00:00','Temp'=>20, 'RxI'=>2, 'RxPw'=>20,'RxPwF'=>0,'RxPwV'=>20),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'03:00:00','Temp'=>19, 'RxI'=>11, 'RxPw'=>110,'RxPwF'=>0,'RxPwV'=>110),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'04:00:00','Temp'=>18, 'RxI'=>5, 'RxPw'=>50,'RxPwF'=>0,'RxPwV'=>50),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'05:00:00','Temp'=>21, 'RxI'=>9, 'RxPw'=>90,'RxPwF'=>0,'RxPwV'=>90),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'06:00:00','Temp'=>23, 'RxI'=>9, 'RxPw'=>90,'RxPwF'=>0,'RxPwV'=>90),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'07:00:00','Temp'=>21, 'RxI'=>3, 'RxPw'=>30,'RxPwF'=>0,'RxPwV'=>30),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'08:00:00','Temp'=>22, 'RxI'=>12, 'RxPw'=>120,'RxPwF'=>120,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'09:00:00','Temp'=>21, 'RxI'=>6, 'RxPw'=>60,'RxPwF'=>60,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'10:00:00','Temp'=>22, 'RxI'=>0, 'RxPw'=>0,'RxPwF'=>0,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'11:00:00','Temp'=>23, 'RxI'=>10, 'RxPw'=>100,'RxPwF'=>100,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'12:00:00','Temp'=>25, 'RxI'=>9, 'RxPw'=>90,'RxPwF'=>90,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'13:00:00','Temp'=>25, 'RxI'=>15, 'RxPw'=>150,'RxPwF'=>150,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'14:00:00','Temp'=>24, 'RxI'=>15, 'RxPw'=>150,'RxPwF'=>150,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'15:00:00','Temp'=>23, 'RxI'=>15, 'RxPw'=>150,'RxPwF'=>150,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'16:00:00','Temp'=>21, 'RxI'=>25, 'RxPw'=>250,'RxPwF'=>250,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'17:00:00','Temp'=>25, 'RxI'=>24, 'RxPw'=>240,'RxPwF'=>240,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'18:00:00','Temp'=>24, 'RxI'=>22, 'RxPw'=>220,'RxPwF'=>220,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'19:00:00','Temp'=>25, 'RxI'=>26, 'RxPw'=>260,'RxPwF'=>260,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'20:00:00','Temp'=>26, 'RxI'=>19, 'RxPw'=>190,'RxPwF'=>190,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'21:00:00','Temp'=>26, 'RxI'=>21, 'RxPw'=>210,'RxPwF'=>210,'RxPwV'=>0),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'22:00:00','Temp'=>26, 'RxI'=>16, 'RxPw'=>160,'RxPwF'=>160,'RxPwV'=>160),
                    array( 'TimeStamp' => $dt2DayAgo->format("Y-m-d ").'23:00:00','Temp'=>27, 'RxI'=>8, 'RxPw'=>80,'RxPwF'=>80,'RxPwV'=>80)



            	
            	
                
            )
            
            
        ));
        
        return $dataSetUpArray;
        
    }
    
}
