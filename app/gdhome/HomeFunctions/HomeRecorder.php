<?php

namespace gdhome\HomeFunctions;

use gdhome\Db\IDbAdapter as IDbAdapter;
use gdhome\models\MeasRecord as MeasRecord;
use DateInterval, DateTimeZone;
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

class HomeRecorder
{
    /**
     * @var \gdhome\Db\IDbAdapter
     */
    protected $db;

    /**
     * @param IDbAdapter $dbAdapter
     */
    public function __construct( IDbAdapter $dbAdapter )
    {
        $this->db = $dbAdapter;
    }

    

    /**
     * Fetch all meas in the RawData Table
     *
     * @return array
    */
    public function getAllRecords() {

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
     * 
     * @return MeasRecord latest record inserted in raw data
     */
    public function getLatestRecord(){
       $measRecords = $this->db->fetchAll('SELECT TimeStamp, Temp, RxI, RxPw FROM RawData order by TimeStamp desc limit 1');
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
       return $measRec; 
    }



    /**
     * Fetch all meas in the RawData Table
     *
     * @return array
     */
    public function fetchAll()
    {
    
        $measRecords = $this->db->fetchAll( "SELECT TimeStamp, Temp, RxI, RxPw FROM RawData" );

        $meas = array();

        if( count( $measRecords ) > 0 )
        {
            foreach( $measRecords as $measRecRecord )
            {
                $measRec = new MeasRec();
                $measRec->setTimeStamp( $measRecRecord[ 'TimeStamp' ] );
                $measRec->setTemp( $measRecRecord[ 'Temp' ] );
                $measRec->setRxI( $measRecRecord[ 'RxI' ] );
                $measRec->setRxPw( $measRecRecord[ 'RxPw' ] );
                $meas[] = $measRec;
            }
        }

        return $meas;
    }

   
    /**
     * Insert a measRec record into the database
     *
     * @param measRec $measRec
     *
     * @return measRec
     */
    public function insert( MeasRecord $measRec )
    {
        $measRecRecord = [
            'TimeStamp' => $measRec->getTimeStamp(),
            'Temp'      => $measRec->getTemp(),
            'RxI' => $measRec->getRxI(),
            'RxPw'     => $measRec->getRxPw()
        ];

        $this->db->insert( 'RawData', $measRecRecord );

        return $measRec;
    }
    
    
      /**
     * Insert a measRec record into the database
     *
     * @param measRecord $measRec
     *
     * @return measRecord
     */
    public function insertWithAutoTimeStamp( MeasRecord $measRec )
    {
    		$dt = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
			$dif=($dt->format("i")%5);

			
        $measRecRecord = [
            'TimeStamp' => $dt->sub(new DateInterval('PT'.$dif.'M'))->format("Y-m-d H:i"),
            'Temp'      => $measRec->getTemp(),
            'RxI' => $measRec->getRxI(),
            'RxPw'     => $measRec->getRxPw()
        ];

        $this->db->insert( 'RawData', $measRecRecord );

        return $measRec;
    }

    /**
     * Update a measRec record into the database
     *
     * @param measRec $measRec
     *
     * @return measRec
     */
    public function update( measRec $measRec )
    {
        $measRecRecord = [
            'isbn'      => $measRec->getIsbn(),
            'author_id' => $measRec->getAuthorId(),
            'title'     => $measRec->getTitle()
        ];

        $this->db->update( 'meas', $measRecRecord, [ 'id' => $measRec->getId() ] );

        return $measRec;
    }

    public function delete( measRec $measRec )
    {
        $this->db->delete( 'meas', [ 'id' => $measRec->getId() ] );
    }

}