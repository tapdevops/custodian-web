<?php
/*=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Model Class untuk Global System
Function      : - 11/04/13  : printDebug          : print error
            - 11/04/13  : convertDate         : ubah format tanggal
            - 11/04/13  : insertLog           : insert data ke user log untuk setiap aktivitas DB yang dilakukan
            - 11/04/13  : getPeriodBudget       : mencari periode budget yang aktif
            - 11/04/13  : countBA           : menghitung jumlah BA yang terdapat dalam suatu region
            - 09/06/14  : logFile           : mencatat log file
            - 14/05/13  : errorLogFile          : mencatat error log file
            - 14/05/13  : deleteDataLogFile       : mencatat log penghapusan data
            - 09/06/14  : checkLockTable        : cek apakah ada table master yang sedang diedit, 
                                    jika ada yang sedang diedit, maka data tidak akan muncul
            - ?       getInput            : setting input untuk region dan maturity stage
            - 11/06/14  : createBashFile        : membuat SH File untuk execute file sql
            - 11/06/14  : createSqlFile         : membuat SQL file
            - 11/06/14  : genFileName         : generate file name
            - 18/07/14  : deleteLockTable       : delete t_lock
            - 01/08/14  : checkLockSequence       : check Lock Table untuk Sequence (YIR)
            - 05/08/14  : chkEnhLockedSequence      : check status locked urutan sequence di awal (YIR) 
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   11/04/2013
Update Terakhir   : 14/05/2013
Revisi        : 
=========================================================================================================================*/

class Application_Model_Global extends Zend_Db_Table_Abstract
{ 
  public function __construct()
  {
    $this->_db = Zend_Registry::get('db');
    $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
    $this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    $this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
    
    $sess = new Zend_Session_Namespace('period');
    $this->_period = $sess->period;
  }
  
  //print error
  public function printDebug($debug = null)
  {
    if (is_array($debug)) {
      echo "<pre>"; print_r($debug); echo "</pre>"; die();
    } else {
      echo "<p>\n{$debug}\n</p>"; die();
    }
  }

  //ubah format tanggal
  public function convertDate($date = '', $format = '')
  {
    $result = '';

    if (!empty($date)) {
      $arr = explode('-', $date);
      switch ($format) {
        case 'd-m-Y':
          $y = $arr[0];
          $m = $arr[1];
          $d = $arr[2];
          $result = "{$d}-{$m}-{$y}";
          break;
        case 'Y-m-d':
          $d = $arr[0];
          $m = $arr[1];
          $y = $arr[2];
          $result = "{$y}-{$m}-{$d}";
          break;
      }
    }

    return $result;
  }

  //insert data ke user log untuk setiap aktivitas DB yang dilakukan
  public function insertLog($action='', $module='', $username='', $error_code='') 
  {
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    if(!$username){
      $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    }
    
    if($error_code) {
      $error_code = "ORA-".str_pad($error_code, 5, "0", STR_PAD_LEFT);            
    }
    
    $sql = "
      INSERT INTO T_USER_LOG (INSERT_USER, INSERT_TIME, REMOTE_ADDR, ACTION, MODULE, MESSAGE)
      VALUES ('{$username}', sysdate, '{$remoteAddr}', '{$action}', '{$module}', '{$error_code}')
    ";
    $this->_db->query($sql);
  }
  
  //mencari periode budget yang aktif
  public function getPeriodBudget() 
  {   
    //yus 31/12/2014
    //bypass status biar bs masul login
    $dateSys = date('d-m-Y');
    $sql = "
       SELECT TO_CHAR (PERIOD_BUDGET, 'dd-mm-rrrr') PERIOD_BUDGET 
       FROM TM_PERIOD 
       WHERE  TO_DATE('{$dateSys}', 'DD-MM-RRRR') BETWEEN START_BUDGETING AND END_BUDGETING
       --AND STATUS = 'OPEN'
       ORDER BY PERIOD_BUDGET DESC
    ";
    $periodBudget = $this->_db->fetchOne($sql);
    return $periodBudget;
  }
  
  //menghitung jumlah BA yang terdapat dalam suatu region
  public function countBA($region_code = 0)
  {
    $where = "";
    if ($region_code){
      $where = "
        AND REGION_CODE = '".$region_code."'
      ";
    }
    
    $sql = "
      SELECT COUNT(BA_CODE) JUMLAH
      FROM TM_ORGANIZATION
      WHERE DELETE_USER IS NULL
      $where
    ";
    $cBA = $this->_db->fetchOne($sql);
    return $cBA;
  }
  
  //mencatat log file
  public function logFile($action='', $module='', $record='', $message='') 
  {
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    
    $uploaddir = "logs/".date("Y-m-d")."/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
      chmod("/".date("Y-m-d"), 0777);
      umask($oldumask);
    }
    
    $logFile = "logs/".date("Y-m-d")."/".$username.".log";
    $data  = "[".date("Y-m-d H:i:s")."]\t";
    $data .= "[".$remoteAddr."]\t";
    $data .= "[".$action."]\t";
    $data .= "[".$module."]\t";
    $data .= "[".$record."]\t";
    
    if($message){
      $data .= "[".$message."]\n";
    } else {
      $data .= "\n";
    }
    file_put_contents($logFile, $data, FILE_APPEND | LOCK_EX);
  }
  
  //mencatat error log file
  public function errorLogFile($message='') 
  {
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    
    $logFile = "logs/error_log_".date("Y-m-d").".log";
    $data  = "[".date("Y-m-d H:i:s")."]\t";
    $data .= "[".$username."]\t";
    $data .= "[".$remoteAddr."]\n";
    $data .= $message."\n";
    file_put_contents($logFile, $data, FILE_APPEND | LOCK_EX);
  }
  
  //mencatat log penghapusan data
  public function deleteDataLogFile($query='') 
  {
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    
    $logFile = "logs/delete_data_log_".date("Y-m-d").".log";
    $data  = "[".date("Y-m-d H:i:s")."]\t";
    $data .= "[".$username."]\t";
    $data .= "[".$remoteAddr."]\n";
    $data .= $query."\n";
    file_put_contents($logFile, $data, FILE_APPEND | LOCK_EX);
  }
  
  // get browser
  public function getBrowser()
  {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
      $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
      $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
      $platform = 'windows';
    }

    // Next get the name of the useragent yes separately and for good reason.
    if (preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
      $bname = 'Internet Explorer';
      $ub = "MSIE";
    }
    elseif (preg_match('/Firefox/i',$u_agent))
    {
      $bname = 'Mozilla Firefox';
      $ub = "Firefox";
    }
    elseif (preg_match('/Chrome/i',$u_agent))
    {
      $bname = 'Google Chrome';
      $ub = "Chrome";
    }
    elseif (preg_match('/Safari/i',$u_agent))
    {
      $bname = 'Apple Safari';
      $ub = "Safari";
    }
    elseif (preg_match('/Opera/i',$u_agent))
    {
      $bname = 'Opera';
      $ub = "Opera";
    }
    elseif (preg_match('/Netscape/i',$u_agent))
    {
      $bname = 'Netscape';
      $ub = "Netscape";
    }

    // Finally get the correct version number.
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
      // we have no matching number just continue
    }

    // See how many we have.
    $i = count($matches['browser']);
    if ($i != 1) {
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
        $version= $matches['version'][0];
      }
      else {
        $version= $matches['version'][1];
      }
    }
    else {
      $version= $matches['version'][0];
    }

    // Check if we have a number.
    if ($version==null || $version=="") {$version="?";}

    return array(
      'userAgent' => $u_agent,
      'name'      => $bname,
      'version'   => $version,
      'platform'  => $platform,
      'pattern'    => $pattern
    );
  }
  
  //insert lock
  public function insertLockTable($ba_code='', $module='') 
  {
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    $sql = "
      INSERT INTO T_LOCK (PERIOD_BUDGET, BA_CODE, MODULE, INSERT_USER, INSERT_TIME)
      VALUES (
        TO_DATE('{$this->_period}','DD-MM-RRRR'),
        '{$ba_code}',
        '{$module}',
        '{$username}',
        SYSDATE
      )
    ";
    //$this->_db->query($sql);
    //$this->_db->commit();
  }
  
  //delete lock
  public function deleteLockTable($ba_code='', $module='') 
  {
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    $sql = "
      DELETE FROM T_LOCK
      WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
        AND BA_CODE = '{$ba_code}'
        AND MODULE = '{$module}'
        AND INSERT_USER = '{$username}'
    ";
    $this->_db->query($sql);
    $this->_db->commit();
  }
  
  public function updLockedSeqStatus($params = array()){
    $query = "UPDATE T_SEQ_CHECK 
      SET STATUS='".$params['status']."',
      UPDATE_USER = '{$username}',
      UPDATE_TIME = SYSDATE
      WHERE 1=1 AND TASK_NAME = '".$params['task_name']."' ";
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
    }else{
      $query .= "
        AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
    }
    if ($params['task_name'] != "TN_KASTRASI_SANITASI"){
      if ($params['key_find'] != '') {
        $query .= "
          AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%') ";
      }
    }
    
    $this->_db->query($query);
    $this->_db->commit();
    
    return true;
  }
  
  public function chkEnhLockedSequence($params = array()){
    $arrEnhLocked=$this->getEnhLockedSequence($params);
    
    if($arrEnhLocked){ //error, masih ada data yang sebelumnya belum di locked
      //$return = 0; //print_r($arrEnhLocked);
      $return = $arrEnhLocked;
    }else{ //jika urutan sudah valid
      $return = 1;
    }
    return $return;
  }
  
  //SELECT COUNT(MODULE) JUMLAH, MODULE, INSERT_USER 
  //function untuk check apakah enheritance norma/rkt (task_name) saat ini statusnya locked atau unlocked
  public function getEnhLockedSequence($params = array())
  {
    $result  ='';
    //************************** 1. Cek urutan berapa task 
    $query = "
      SELECT SEQ_NUM 
      FROM T_SEQ
      WHERE TASK_NAME = '".$params['task_name']."'
    ";
    $rowSeq = $this->_db->fetchOne($query); 
    
    //jika null, maka ubah jadi 0
    $rowSeq = ($rowSeq) ? $rowSeq : 0;
    
    //************************** 2. Cek apakah urutan yang lebih kecil dari urutannya sudah di-LOCK
    $query = "
      SELECT INITCAP(REMARKS) REMARKS
      FROM T_SEQ_CHECK A 
        LEFT JOIN T_SEQ B 
        ON A.TASK_NAME=B.TASK_NAME
      WHERE 
        B.SEQ_NUM < $rowSeq 
        AND A.STATUS IS NULL ";
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
    }else{
      $query .= "
        AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
    }
    if ($params['task_name'] != "TN_KASTRASI_SANITASI"){
      if ($params['key_find'] != '') {
        $query .= "
          AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%') ";
      }
    }
    
    $query .= " ORDER BY SEQ_NUM, A.TASK_NAME"; 
    $rowData = $this->_db->fetchAll($query);
    
    if(!empty($rowData)){
      $curRemarks="";$oldRemarks="";
      foreach ($rowData as $key => $row) {
        $oldRemarks=$curRemarks;
        if($curRemarks<>$row['REMARKS'])
          $result .= $row['REMARKS'].", ";
        $curRemarks=$row['REMARKS'];
      }
      
      if($curRemarks<>$oldRemarks)
        $result .= $curRemarks.", ";
      
      $result = substr($result, 0, -2) . ".";
    }
    return $result;
  }
  
  //function untuk check apakah curent norma/rkt (task_name) saat ini statusnya locked atau NULL
  public function checkLockSequence($params = array())
  {
    $query = "
      SELECT STATUS  
      FROM T_SEQ_CHECK 
      WHERE 1=1 AND TASK_NAME = '".$params['task_name']."'
    ";
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }else{
      $query .= "
        AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
      ";
    }
    if ($params['task_name'] != "TN_KASTRASI_SANITASI"){
      if ($params['key_find'] != '') {
        $query .= "
          AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
        ";
      }
    }
    
    $result = $this->_db->fetchRow($query); 
    return $result;
  }
  
  //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
  public function checkLockTable($params = array())
  {
    $query = "
      SELECT COUNT(MODULE) JUMLAH, MODULE, INSERT_USER 
      FROM T_LOCK 
      WHERE 1=1
    ";
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }else{
      $query .= "
        AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
      ";
    }
    
    if ($params['key_find'] != '') {
      $query .= "
        AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
      ";
    }
    
    $query .= "
      GROUP BY MODULE, INSERT_USER
    ";
    
    $result = $this->_db->fetchRow($query);
    return $result;
  }
  
  //membuat SH File untuk execute file sql
  public function createBashFile($filename)
  {
    //get DB config from application.ini
    $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');    
    $resources = $config->getOption('resources');
    
    $uploaddir = "tmp_query/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
      umask($oldumask);
    }
    
    $logFile = "tmp_query/".$filename.".sh";
    $data  = "
    #!/bin/bash
    host='".$resources['db']['params']['host']."';
    user='".$resources['db']['params']['username']."';
    pass='".$resources['db']['params']['password']."';
    dbname='".$resources['db']['params']['dbname']."';
    
    export ORACLE_HOME=/home/oracle/app/oracle/product/12.1.0/dbhome_1
    export ORACLE_SID=".chr(36)."dbname
    export LD_LIBRARY_PATH=".chr(36)."ORACLE_HOME/lib:/usr/lib
    export PATH=".chr(36)."PATH:".chr(36)."ORACLE_HOME/bin
    
    /home/oracle/app/oracle/product/12.1.0/dbhome_1/bin/sqlplus -s ".chr(36)."user/".chr(36)."pass@".chr(36)."host:1521/".chr(36)."dbname < ".getcwd()."/tmp_query/".$filename.".sql";
    
    /* ORIGINAL CODE - Edited by Sabrina 07/01/2015
    $data  = "
    #!/bin/bash
    host='".$resources['db']['params']['host']."';
    user='".$resources['db']['params']['username']."';
    pass='".$resources['db']['params']['password']."';
    dbname='".$resources['db']['params']['dbname']."';
    
    export ORACLE_HOME=/usr/lib/oracle/12.1
    export ORACLE_SID=".chr(36)."dbname
    export LD_LIBRARY_PATH=".chr(36)."ORACLE_HOME/client64/lib:/usr/lib
    export PATH=".chr(36)."PATH:".chr(36)."ORACLE_HOME/bin

    /usr/lib/oracle/12.1/client64/bin/sqlplus -s ".chr(36)."user/".chr(36)."pass@".chr(36)."host:1521/".chr(36)."dbname < tmp_query/".$filename.".sql";
    */

    file_put_contents($logFile, $data);
  }
  
  /*
  //SETTING UNTUK PRODUCTION
  //membuat SH File untuk execute file sql
  public function createBashFile($filename)
  {
    //get DB config from application.ini
    $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');    
    $resources = $config->getOption('resources');
    
    $uploaddir = "tmp_query/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
      umask($oldumask);
    }
    
    $logFile = "tmp_query/".$filename.".sh";
    $data  = "
    #!/bin/bash
    host='".$resources['db']['params']['host']."';
    user='".$resources['db']['params']['username']."';
    pass='".$resources['db']['params']['password']."';
    dbname='".$resources['db']['params']['dbname']."';
    
    export ORACLE_HOME=/home/oracle/app/oracle/product/12.1.0/dbhome_1
    export ORACLE_SID=".chr(36)."dbname
    export LD_LIBRARY_PATH=".chr(36)."ORACLE_HOME/lib
    export PATH=".chr(36)."PATH:".chr(36)."ORACLE_HOME/bin

    ".chr(36)."ORACLE_HOME/bin/sqlplus -s ".chr(36)."user/".chr(36)."pass@".chr(36)."host:1521/".chr(36)."dbname < tmp_query/".$filename.".sql";

    file_put_contents($logFile, $data);
  }
  */
  
  //membuat SQL file
  public function createSqlFile($filename, $data)
  {
    $sub_dir =  date('Y').'/'.date('m');
    $uploaddir = "tmp_query/".$sub_dir;
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
      umask($oldumask);
    }
    
    $logFile = "tmp_query/".$filename.".sql"; //echo $logFile."\n";
    file_put_contents($logFile, $data, FILE_APPEND | LOCK_EX);
  }
  
  function randomString($lchar) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i <= $lchar; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }
  
  //generate file name
  public function genFileName()
  {
    $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    
    $return = str_replace('.', '', $username)."_".date("YmdHis")."_".$this->randomString(10);
    
    return $return;
  }
}
