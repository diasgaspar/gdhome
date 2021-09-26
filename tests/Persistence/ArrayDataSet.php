<?php

namespace gdhome\Tests\Persistence;
use DateTime, DateInterval, DateTimeZone;
class ArrayDataSet extends \PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = array();

    /**
     * @param array $data
     */
    public function __construct( array $data )
    {
        foreach( $data AS $tableName => $rows )
        {
            $columns = array();
            if( isset( $rows[ 0 ] ) )
            {
                $columns = array_keys( $rows[ 0 ] );
            }

            $metaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData( $tableName, $columns );
            $table    = new \PHPUnit_Extensions_Database_DataSet_DefaultTable( $metaData );

            foreach( $rows AS $row )
            {
                $table->addRow( $row );
            }
            $this->tables[ $tableName ] = $table;
        }
    }

    public function createIterator( $reverse = FALSE )
    {
        return new \PHPUnit_Extensions_Database_DataSet_DefaultTableIterator( $this->tables, $reverse );
    }

    public function getTable( $tableName )
    {
        if( !isset( $this->tables[ $tableName ] ) )
        {
            throw new \InvalidArgumentException( "$tableName is not a table in the current database." );
        }

        return $this->tables[ $tableName ];
    }
    
    
    			   /**
     * Function to perform aggregation in arraydataset
     * @param       $table Table inside array (RawData, DoorData)
     * @param  		$timemask  - date format maks to apply (ex: Y-m-d H)
     * @param  		$timeinput  - date time to perform the task (ex: 2017-01-01 22)
     * @param  		$column  - Column to perfrom the agg 
     * @return array keys are sum, avg, min, max, count
    */  
    
    public function arrayOperation($table, $timemask,$timeinput, $column){
    	$table=$this->getTable($table);
		$resulti=0;
		$result=0; 
		$resultMin=$table->getRow(0)[$column];
		$resultMax=0;
		$countValid=0;
		   	
    	for ($i = 0; $i < $table->getRowCount(); ++$i) {
        			//$arr2=$arr->getRow($i);
        			$timeStamp = new DateTime( $table->getRow($i)['TimeStamp']);
        			if ($timeStamp->format($timemask) == $timeinput ){
        				$countValid++;
						//print $timeStamp->format($timemask)." -> ".$table->getRow($i)[$column]."\n";
						$resulti=$table->getRow($i)[$column];
						$result=$result+$resulti;
						if($resulti>$resultMax)$resultMax=$resulti;
						if($resulti<$resultMin)$resultMin=$resulti;
						
        			}
        			
   	}	
   		
   		$results = array(
			    "sum" => $result,
			    "avg" => ($countValid==0? null: round($result/$countValid)),
			    "min" => $resultMin,
			    "max" => $resultMax,
			    "count" => $countValid
			);
	    return $results;
    }
    
    public function getLatestRow($table){
        $table=$this->getTable($table);
        //$table->getRow(0)[$column];
        
        
       /* $dt1=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $doorEvent=1;
        $doorRec= new DoorRecord();
        $doorRec->setTimeStamp($dt1->format("Y-m-d H:i:s"));
        $doorRec->setState($doorEvent);
        */
        $d1 = new DateTime('1900-01-01 10:00:00');
        $row=null;
        for ($i = 0; $i < $table->getRowCount(); ++$i) {
            $timeStamp = new DateTime( $table->getRow($i)['TimeStamp']);
            if ($timeStamp >  $d1){
                   $row=$table->getRow($i);
                   $d1=$timeStamp;
            }
        			
   	}
        
        return $row;
    }
}