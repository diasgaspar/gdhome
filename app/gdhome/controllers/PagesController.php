<?php
namespace gdhome\controllers;
use gdhome\views\pages\Error as Error;
use gdhome\models\HomeMeasRaw as HomeMeasRaw;
use gdhome\models\HomeMeasDay as HomeMeasDay;
use DateTime, DateInterval, DateTimeZone;

class PagesController {

    
    public function home() {
      
      $homeMeas=new HomeMeasRaw();
      $homeMeasDay= new HomeMeasDay();
      $latestHomeMeas=$homeMeas->getLatestRecord();
      
      $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
      $dtDayTomorrow=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
      $dtDayTomorrow->add(new DateInterval("P1D"));
      $energySpentToday=$homeMeas->getEnergySpent($dtNow->format("Y-m-d"),$dtDayTomorrow->format("Y-m-d"));
      $energyCostToday=$homeMeas->getEnergyCostBiHourlyTarif($dtNow->format("Y-m-d"),$dtDayTomorrow->format("Y-m-d"));
      $energyCostTodayWithPowerContract=number_format($energyCostToday['cost']+\gdhome\HomeVars::ENERGY_COST_POT,2);
      
      $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
      $dtDayAgo->sub(new DateInterval("P1D"));
      $energySpentYesterday=$homeMeas->getEnergySpent($dtDayAgo->format("Y-m-d"),$dtNow->format("Y-m-d"));
      $costYesterday=$homeMeas->getEnergyCostBiHourlyTarif($dtDayAgo->format("Y-m-d"),$dtNow->format("Y-m-d"));
      $costYesterdayWithPowerContract=$costYesterday['cost']+$costYesterday['numDays']*\gdhome\HomeVars::ENERGY_COST_POT;
      
      $previousDateAtSpecificDayCalculation=$homeMeas->calculatePreviousDateAtSpecificDay($dtNow, \gdhome\HomeVars::ENERGY_READING_DAY);
      $energySpentFromPreviousChargingDay=$homeMeas->getEnergySpent($previousDateAtSpecificDayCalculation->format("Y-m-d"), $dtDayTomorrow->format("Y-m-d"));
      $costFromPreviousChargingDay=$homeMeas->getEnergyCostBiHourlyTarif($previousDateAtSpecificDayCalculation->format("Y-m-d"), $dtDayTomorrow->format("Y-m-d"));
      $costFromPreviousChargingDayWithPowerContract=$costFromPreviousChargingDay['cost']+$costFromPreviousChargingDay['numDays']*\gdhome\HomeVars::ENERGY_COST_POT;
      
      $monthAgoChargingStartDate=$homeMeas->calculateMonthAgoChargingPeriod($dtNow)['beginDay'];
      $monthAgoChargingEndDate=$homeMeas->calculateMonthAgoChargingPeriod($dtNow)['endDay'];
       
      $energySpentMonthAgo=$homeMeasDay->getEnergySpent($monthAgoChargingStartDate->format("Y-m-d"),$monthAgoChargingEndDate->format("Y-m-d"));
      $costFromMonthAgo=$homeMeasDay->getEnergyCostBiHourlyTarif($monthAgoChargingStartDate->format("Y-m-d"),$monthAgoChargingEndDate->format("Y-m-d"));
      $costFromMonthAgoWithPowerContract=$costFromMonthAgo['cost']+$costFromMonthAgo['numDays']*\gdhome\HomeVars::ENERGY_COST_POT;
      
      //values to print in home page
      $energySpentToday_Ewh=number_format($energySpentToday['Ewh'],1);
      $energySpentYesterday_Ewh=number_format($energySpentYesterday['Ewh'],1);
      $energyCostToday_cost=number_format($energyCostToday['cost'],2);
      $energyCostToday_numDays=$energyCostToday['numDays'];
      $costToday_powercontract= number_format($energyCostToday_numDays*\gdhome\HomeVars::ENERGY_COST_POT,2);
      
      $costYesterday_cost=number_format($costYesterday['cost'],2);
      $costYesterday_numDays=$costYesterday['numDays'];
      $costYesterday_powercontract=number_format($costYesterday['numDays']*\gdhome\HomeVars::ENERGY_COST_POT,2);
      $costYesterday_powercontract=number_format($costYesterdayWithPowerContract,2);
      
      $energySpentFromPreviousChargingDay_Kwh=number_format($energySpentFromPreviousChargingDay['Ewh']/1000,0);
      $costFromPreviousChargingDay_cost=number_format($costFromPreviousChargingDay['cost'],2);
      
      require_once 'app/gdhome/views/pages/home.php';
      
    }
    
    public function powerday(){
        $dtNow = new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayAgo=new DateTime("now",new DateTimeZone('EUROPE/Lisbon'));
        $dtDayAgo->sub(new DateInterval("P1D"));
        $homeMeasDay= new HomeMeasDay();
        $monthAgoChargingStartDate=$homeMeasDay->calculateMonthAgoChargingPeriod($dtNow)['beginDay'];
        $monthAgoChargingEndDate=$homeMeasDay->calculateMonthAgoChargingPeriod($dtNow)['endDay'];
        
        $energySpentMonthAgo=$homeMeasDay->getEnergySpent($monthAgoChargingStartDate->format("Y-m-d"),$monthAgoChargingEndDate->format("Y-m-d"));
        $costFromMonthAgo=$homeMeasDay->getEnergyCostBiHourlyTarif($monthAgoChargingStartDate->format("Y-m-d"),$monthAgoChargingEndDate->format("Y-m-d"));
        $costFromMonthAgoWithPowerContract=$costFromMonthAgo['cost']+$costFromMonthAgo['numDays']*\gdhome\HomeVars::ENERGY_COST_POT;
        
        $previousDateAtSpecificDayCalculation=$homeMeasDay->calculatePreviousDateAtSpecificDay($dtNow, \gdhome\HomeVars::ENERGY_READING_DAY);
        $energySpentFromPreviousChargingDay=$homeMeasDay->getEnergySpent($previousDateAtSpecificDayCalculation->format("Y-m-d"), $dtDayAgo->format("Y-m-d"));
        $costFromPreviousChargingDay=$homeMeasDay->getEnergyCostBiHourlyTarif($previousDateAtSpecificDayCalculation->format("Y-m-d"), $dtDayAgo->format("Y-m-d"));
        $costFromPreviousChargingDayWithPowerContract=$costFromPreviousChargingDay['cost']+$costFromPreviousChargingDay['numDays']*\gdhome\HomeVars::ENERGY_COST_POT;
        
        
        //values to print in powerday page
        $energySpentMonthAgo_V_Kwh=number_format($energySpentMonthAgo['EwhV']/1000,1);
        $energySpentMonthAgo_F_Kwh=number_format($energySpentMonthAgo['EwhF']/1000,1);
        $energySpentMonthAgo_Total_Kwh=number_format($energySpentMonthAgo['Ewh']/1000,1);
        $costFromMonthAgo_V=number_format($costFromMonthAgo['costV'],2);
        $costFromMonthAgo_F=number_format($costFromMonthAgo['costF'],2);
        $costFromMonthAgo_Total=number_format($costFromMonthAgo['cost'],2);
        $numDays=$costFromMonthAgo['numDays'];
        $costPowerContract=number_format($numDays*\gdhome\HomeVars::ENERGY_COST_POT,2);
        $totalCost=$costFromMonthAgo_Total + $costPowerContract + \gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL;
        
        $energySpentFromPreviousChargingDay_V_Kwh=number_format($energySpentFromPreviousChargingDay['EwhV']/1000,1);
        $energySpentFromPreviousChargingDay_F_Kwh=number_format($energySpentFromPreviousChargingDay['EwhF']/1000,1);
        $energySpentFromPreviousChargingDay_Total_Kwh=number_format($energySpentFromPreviousChargingDay['Ewh']/1000,1);
        $costFromPreviousChargingDay_V=number_format($costFromPreviousChargingDay['costV'],2);
        $costFromPreviousChargingDay_F=number_format($costFromPreviousChargingDay['costF'],2);
        $costFromPreviousChargingDay_Total=number_format($costFromPreviousChargingDay['cost'],2);
        $numDaysFromPreviousChargingDay=$costFromPreviousChargingDay['numDays'];
        $costPowerContractFromPreviousChargingDay=number_format($numDaysFromPreviousChargingDay*\gdhome\HomeVars::ENERGY_COST_POT,2);
        $totalCostFromPreviousChargingDay=$costFromPreviousChargingDay_Total + $costPowerContractFromPreviousChargingDay + \gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL;

        require_once('app/gdhome/views/pages/powerday.php');
    }
    
    public function powermonth(){
        
        require_once('app/gdhome/views/pages/powermonth.php');
    }
    
    public function powerheatmap(){
        require_once('app/gdhome/views/pages/powerheatmap.php');
    }

    public function error() {
      require_once('app/gdhome/views/pages/error.php');
    }
    
    public function chartRangeFilter(){
        require_once('app/gdhome/views/pages/chartRangeFilter.php');
    }

  }
