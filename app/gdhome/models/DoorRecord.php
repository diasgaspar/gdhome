<?php

namespace gdhome\models;

class DoorRecord
{
    /**
     * @var string
     */
    protected $TimeStamp;

    /**
     * @var int
     */
    protected $State;

   /**
     * @var freq
     */
    protected $Freq;
      

    /**
     * @return DateTime
     */
    public function getTimeStamp()
    {
        return $this->TimeStamp;
    }

    /**
     * @param string $TimeStamp
     */
    public function setTimeStamp( $TimeStamp )
    {
        $this->TimeStamp = $TimeStamp;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->State;
    }

    /**
     * @param int $State
     */
    public function setState( $State )
    {
        $this->State = $State;
    }

	    /**
     * @return int
     */
    public function getFreq()
    {
        return $this->Freq;
    }

    /**
     * @param int $Freq
     */
    public function setFreq( $Freq )
    {
        $this->Freq = $Freq;
    }
   
  
}