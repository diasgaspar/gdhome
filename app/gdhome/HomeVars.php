<?php
namespace gdhome;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class HomeVars{
    
     /**
     * Home page
     */
    const GDHOME_HOME_PAGE="http://gdhome.atwebpages.com";
    
    const USER_EMAIL_ACCOUNTS= ['gaspar'=>'diasgaspar@gmail.com', 'filipa'=>'filipagmota@gmail.com'];
    
    /**
     * T1 - Fora Vazio inicio
     * format: [H]HMM
     */
    const ENERGY_T2T3_BEGIN = '08';//8H00
    /**
     * T1 - Fora Vazio Fim
     * format:[H]HMM
     */
    const ENERGY_T2T3_END = '22';//22H00
    
    //https://institucional.goldenergy.pt/pt/empresa/precos-de-referencia/
    
    /**
     * KWH cost "fora do vazio"
     */
    const ENERGY_COST_T2T3 = 0.2033;
    /**
     * KWH cost "vazio"
     */
    const ENERGY_COST_T1 = 0.0941;
    
    /**
     * Custo diário da potencia contratada
     */
    const ENERGY_COST_POT = 0.312;

    /**
    *Contribuicao audiovisual
    */
    const ENERGY_COST_CONTR_AUDIOVISUAL = 2.85;
    
    /**
     * Data aconselhavel para comunicação de leitura
     */
    const ENERGY_READING_DAY = 15;
    
    /**
     * start time to perform maintenance tasks: aggregations
     * format: [H]HMM
     */
    const MAINTENANCE_TIME_BEGIN = 200;//02h00
    
    /**
     * end time to perform maintenance tasks: aggregations
     * format: [H]HMM
     */
    const MAINTENANCE_TIME_END = 205;//02h05
    
    /**
     * start time to send daily report via email
     * format: [H]HMM
     */
    const SEND_DAILY_REPORT_TIME_BEGIN=800;
      
    /**
     * end time to send daily report via email
     * format: [H]HMM
     */
    const SEND_DAILY_REPORT_TIME_END=805;
    
    /**
     * begin time to send door events notifications by email
     * format: [H]HMM
     */
    const SEND_DOOR_NOTIFICATION_TIME_BEGIN=830;
    
    /**
     * End time to send door events notifications by email
     * format: [H]HMM
     */
    const SEND_DOOR_NOTIFICATION_TIME_END=1830;
    
    /**
     * Mail host for home agent to send notifications and reports
     */
    const MAIL_HOST="mail.gdome.tk";
    /**
     * Mail address for home agent to send notifications and reports
     */
    const MAIL_FROM_ADDRESS="gdhome@gdome.tk";
    
    
    
    /**
     * Time in seconds to wait before sending new door event notification
     */
    const TIME_TO_WAIT_BEFORE_SEND_NEW_DOOR_NOTIFICATION=30;
    
    /**
     * Time in days to store data from Door data raw. All data older than this are deleted during MAINTENANCE_TIME window
     */
    const PERSISTENCE_DOOR_DATA_RAW=30;
    
    /**
     * Time in days to store data from meas data raw. All data older than this are deleted during MAINTENANCE_TIME window
     */
    const PERSISTENCE_MEAS_DATA_RAW=60;
    

}
