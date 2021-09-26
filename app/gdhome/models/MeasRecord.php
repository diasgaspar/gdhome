<?php

namespace gdhome\models;

use DateInterval;

class MeasRecord
{
    /**
     * @var string
     */
    protected $TimeStamp;

    /**
     * @var string
     */
    protected $Temp;

    /**
     * @var string
     */
    protected $RxI;

    /**
     * @var string
     */
    protected $RxPw;


	   /**
     * @var string
     */
    protected $RxPwF;
    
       /**
     * @var string
     */
    protected $RxPwV;

	  /**
     * @var string
     */
    protected $TempMax;
    
    /**
     * @var string
     */
    protected $TempMin;
    
    /**
     * @var string
     */
    protected $RxPwMax;
    
    /**
     * @var string
     */
    protected $RxPwMin;
    
    
     /**
     * @var string
     */
    protected $Ewh;
    
    /**
     * @var string
     */
    protected $EwhF;
        /**
     * @var string
     */
    protected $EwhV;

   

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
     * @param DateTime $TimeStamp
     */
    public function setTimeStampWith5MinStep( $TimeStamp )
    {
        //$dt = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
	$dif=($TimeStamp->format("i")%5);
        $TimeStamp->sub(new DateInterval('PT'.$dif.'M'));
        $this->TimeStamp = $TimeStamp->format("Y-m-d H:i");
    }

    /**
     * @return string
     */
    public function getTemp()
    {
        return $this->Temp;
    }

    /**
     * @param string $Temp
     */
    public function setTemp( $Temp )
    {
        $this->Temp = $Temp;
    }

    /**
     * @return string
     */
    public function getRxI()
    {
        return $this->RxI;
    }

    /**
     * @param string $RxI
     */
    public function setRxI( $RxI )
    {
        $this->RxI = $RxI;
    }

    /**
     * @return string
     */
    public function getRxPw()
    {
        return $this->RxPw;
    }

    /**
     * @param string $RxPw
     */
    public function setRxPw( $RxPw )
    {
        $this->RxPw = $RxPw;
    }
    
       /**
     * @return string
     */
    public function getRxPwF()
    {
        return $this->RxPwF;
    }

    /**
     * @param string $RxPwF
     */
    public function setRxPwF( $RxPwF )
    {
        $this->RxPwF = $RxPwF;
    }


	      /**
     * @return string
     */
    public function getRxPwV()
    {
        return $this->RxPwV;
    }

    /**
     * @param string $RxPwV
     */
    public function setRxPwV( $RxPwV )
    {
        $this->RxPwV = $RxPwV;
    }
  
	/**
     * @return string
     */
    public function getTempMax()
    {
        return $this->TempMax;
    }

    /**
     * @param string $TempMax
     */
    public function setTempMax( $TempMax )
    {
        $this->TempMax = $TempMax;
    } 
    
    
    	/**
     * @return string
     */
    public function getTempMin()
    {
        return $this->TempMin;
    }

    /**
     * @param string $TempMin
     */
    public function setTempMin( $TempMin )
    {
        $this->TempMin = $TempMin;
    }   
    
    
       	/**
     * @return string
     */
    public function getRxPwMax()
    {
        return $this->RxPwMax;
    }

    /**
     * @param string $RxPwMax
     */
    public function setRxPwMax( $RxPwMax )
    {
        $this->RxPwMax = $RxPwMax;
    }   
    
    
          	/**
     * @return string
     */
    public function getRxPwMin()
    {
        return $this->RxPwMin;
    }

    /**
     * @param string $RxPwMin
     */
    public function setRxPwMin( $RxPwMin )
    {
        $this->RxPwMin = $RxPwMin;
    }   
  
	         	/**
     * @return string
     */
    public function getEwh()
    {
        return $this->Ewh;
    }

    /**
     * @param string $Ewh
     */
    public function setEwh( $Ewh )
    {
        $this->Ewh = $Ewh;
    }     
    
    
             	/**
     * @return string
     */
    public function getEwhF()
    {
        return $this->EwhF;
    }

    /**
     * @param string $EwhF
     */
    public function setEwhF( $EwhF )
    {
        $this->EwhF = $EwhF;
    }   
    
                	/**
     * @return string
     */
    public function getEwhV()
    {
        return $this->EwhV;
    }

    /**
     * @param string $EwhV
     */
    public function setEwhV( $EwhV )
    {
        $this->EwhV = $EwhV;
    }       
  
}