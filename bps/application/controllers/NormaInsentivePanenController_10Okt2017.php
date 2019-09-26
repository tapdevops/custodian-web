<?php
/* 
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Controller Class untuk Norma Dasar
Function      : - listAction    : menampilkan list norma dasar
            - saveAction    : save data
            - deleteAction    : hapus data
            - getStatusPeriodeAction
            - checkLockedSeqAction  : checked validasi sequence YIR (140801)
            - chkEnhLockedSequenceAction : check status locked urutan sequence di awal
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada 
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   26/04/2013
Update Terakhir   : 20/05/2014
Revisi        : 
=========================================================================================================================
*/
class NormaInsentivePanenController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
      $this->_db = Zend_Registry::get('db');
      $this->_global = new Application_Model_Global();
      $this->_formula = new Application_Model_Formula();
      $this->_model = new Application_Model_NormaInsentivePanen();
      $this->view->input = $this->_model->getInput();
    
      $sess = new Zend_Session_Namespace('period');
      $this->_period = $sess->period;
    }

    public function indexAction()
    {
      $this->_redirect('/norma-insentive-panen/main');
    }

    public function mainAction()
    {
      $this->view->title = 'Data &raquo; Norma Insentive Panen';
      $this->view->legend_title = "NORMA INSENTIVE PANEN";
      $this->view->period = date("Y", strtotime($this->_period));
      $this->_helper->layout->setLayout('norma');
      $this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
  

    public function listAction()
    {   
      $this->_helper->viewRenderer->setNoRender(true);

      $params = $this->_request->getPost();
      //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
      $lock = $this->_global->checkLockTable($params);
    
      if($lock['JUMLAH']){
        $data['return'] = "locked";
        $data['module'] = $lock['MODULE'];
        $data['insert_user'] = $lock['INSERT_USER'];
        die(json_encode($data));
      } else {
        $data = $this->_model->getList($params);
        die(json_encode($data));
      }
    }
  
  public function updLockedSeqStatusAction(){
    $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
    $params['task_name'] = "TN_INSENTIVE_PANEN";
    $data = $this->_global->updLockedSeqStatus($params);
    die(json_encode($data));
  }
  
  public function chkEnhLockedSequenceAction(){
    $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
    $params['task_name'] = "TN_INSENTIVE_PANEN";
    //$params['tipe_transaksi'] = "MANUAL_INFRA";
    $data = $this->_global->chkEnhLockedSequence($params);
    die(json_encode($data));
  }
  
  public function checkLockedSeqAction(){ 
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
    $params['task_name'] = "TN_INSENTIVE_PANEN";
    $data = $this->_global->checkLockSequence($params);
    die(json_encode($data));
  }
  
  //simpan temporary data
  public function saveTempAction()
    {
        $rows = $this->mappingAction();
    
    //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
    foreach ($rows as $key => $row) {
      $params['key_find'] = $row['BA_CODE'];
      $lock = $this->_global->checkLockTable($params);    
      if($lock['JUMLAH']){
        $data['return'] = "locked";
        $data['module'] = $lock['MODULE'];
        $data['insert_user'] = $lock['INSERT_USER'];
        die(json_encode($data));
      }
    }
    
    $filename = $this->_global->genFileName();
    $this->_global->createBashFile($filename); //create bash file
    
    $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
    
    //save norma dasar
    foreach ($rows as $key => $row) {
      $row['filename'] = $filename;
      $return = $this->_model->saveTemp($row);
    } 
    
    //execute transaksi
    $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
    shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
    $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
    shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
    
    //pindahkan .sql ke logs
    $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true);
      chmod("/".date("Y-m-d"), 0777);
      umask($oldumask);
    }
    shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
    die('no_alert');
  }
  
  //mapping textfield name terhadap field name di DB
  public function mappingAction(){
    $params = $this->_request->getParams();
        $rows = array();
    
        foreach ($params['text00'] as $key => $val) {
      if (($key > 0) && ($params['tChange'][$key])) {
        $rows[$key]['CHANGE']         = $params['tChange'][$key]; // CHANGE
        $rows[$key]['ROW_ID']         = $params['text00'][$key]; // ROW ID
        $rows[$key]['PERIOD_BUDGET']        = $params['text02'][$key]; // PERIOD_BUDGET
        $rows[$key]['BA_CODE']        = $params['text03'][$key]; // BA_CODE
        $rows[$key]['BASIC_NORMA_CODE']   = $params['text04'][$key]; // BASIC_NORMA_CODE
        $rows[$key]['DESCRIPTION']    = $params['text05'][$key]; // DESCRIPTION
        $rows[$key]['PERCENT_INCREASE']   = $params['text06'][$key]; // PERCENT_INCREASE
        
        //deklarasi var utk inherit module
        $rows[$key]['key_find']     = $params['text03'][$key]; // BA_CODE
        $rows[$key]['activity_code']  = $params['text05'][$key]; // BA_CODE
            }
        }
    return $rows;
  }
  
  //save data
  public function saveAction()
    {
    $rows = $this->mappingAction();
    $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
    
    if (!empty($rows)) {    
      //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
      foreach ($rows as $key => $row) {
        $params['key_find'] = $row['BA_CODE'];
        $lock = $this->_global->checkLockTable($params);    
        if($lock['JUMLAH']){
          $data['return'] = "locked";
          $data['module'] = $lock['MODULE'];
          $data['insert_user'] = $lock['INSERT_USER'];
          die(json_encode($data));
        }
      }
      
      
      // ************************************************ SAVE NORMA BASIC TEMP ************************************************
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_TNBASIC_01_SVTEMP';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save norma basic temp
      foreach ($rows as $key => $row) {
        $row['filename'] = $filename;
        $return = $this->_model->saveTemp($row);
      } 
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ SAVE NORMA BASIC TEMP ************************************************
    }
    
    // ************************************************ SAVE ALL NORMA BASIC ************************************************
    //generate filename untuk .sh dan .sql
    $filename = $uniq_code_file.'_00_TNBASIC_02_SV';
    $this->_global->createBashFile($filename); //create bash file   
    $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
    $params = $this->_request->getPost();
    $records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
    if (!empty($records1)) {
      foreach ($records1 as $idx1 => $record1) {
        if($record1['FLAG_TEMP'] == 'Y'){
          $record1['filename'] = $filename;
          $this->_model->save($record1);
          
          //ditampung untuk inherit module
          $updated_row[] = $record1;
        }
      }
    }
    //execute transaksi
    $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
    shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
    $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
    shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
    
    //pindahkan .sql ke logs
    $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true);
      chmod("/".date("Y-m-d"), 0777);
      umask($oldumask);
    }
    shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
    // ************************************************ SAVE ALL NORMA BASIC ************************************************
    
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    if (!empty($updated_row)) {
      $idxInherit = 1;
      foreach ($updated_row as $idx1 => $record1) {
        //update inherit module
        $record1['uniq_code_file']  = $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
        
        $record1['ROW_ID'] = ""; //reset rowid agar tidak jadi filter ketika select data
        $this->updateInheritModule($record1);
        
        $idxInherit++;
      }
    }
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    
    $data['return'] = "done";
    die(json_encode($data));
    }

  //update inherit module
  public function updateInheritModule($row = array()) {
    if (!empty($row)) {   
      $urutan = 0;
      
      // ************************************************ UPDATE NORMA CHECKROLL ************************************************
      $model = new Application_Model_NormaCheckroll();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NCHECKROLL';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;         
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA CHECKROLL ************************************************
      
      // ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTCHECKROLL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $model->updateRktCheckroll($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
      
      // ************************************************ SAVE RKT CHECKROLL SUMMARY ************************************************
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTCHECKROLLSUM';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $lastTr = "";
      $lastArrTr = array();
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          if(($lastTr) && ($lastTr <> $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'])){       
            $model->updateRktCheckrollSummary($lastArrTr);
          }
          
          $lastTr = $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'];
          $lastArrTr = $record1;
        }
      }
      $model->updateRktCheckrollSummary($lastArrTr);
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ SAVE RKT CHECKROLL SUMMARY ************************************************
      
      // ************************************************ SAVE RKT CHECKROLL DISTRIBUSI ************************************************
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTCHECKROLLDIST';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $lastBa = "";
      $lastArrTr = array();
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
            $model->calDistribusiCheckroll($lastArrTr);
          }
          
          $lastBa = $record1['BA_CODE'];
          $lastArrTr = $record1;
        }
      }
      $model->calDistribusiCheckroll($lastArrTr);
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ SAVE RKT CHECKROLL DISTRIBUSI ************************************************
      
      // ************************************************ UPDATE NORMA WRA ************************************************     
      $model = new Application_Model_NormaWra();
      
      //1. NORMA WRA 1
      $records1 = $this->_db->fetchAll("{$model->getData1($row)}");     
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NORMAWRA1';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      //2. NORMA WRA 2
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NORMAWRA1';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      //2. SAVE NORMA WRA SUMMARY
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NORMAWRASUM';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $lastBa = $lastPr ="";
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
            $uPar['filename'] = $filename;
            $uPar['BA_CODE'] = $lastBa;
            $uPar['PERIOD_BUDGET'] = $lastPr;
            $model->updateSummaryNormaWra($uPar);
          }
          $lastBa = $record1['BA_CODE'];
          $lastPr = $row['PERIOD_BUDGET'];
        }
      }
      $uPar['filename'] = $filename;
      $uPar['BA_CODE'] = $lastBa;
      $uPar['PERIOD_BUDGET'] = $lastPr;
      $model->updateSummaryNormaWra($uPar);
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ END UPDATE NORMA WRA ************************************************
      
      // ************************************************ UPDATE RKT VRA ************************************************
      $model = new Application_Model_RktVra();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $model->save($record1);
          
          //distinct VRA
          if (in_array($record1['VRA_CODE'], $tmp['VRA_CODE']) == false) {
            array_push($tmp['VRA_CODE'], $record1['VRA_CODE']);
          }
        }
      }
      $par['VRA_CODE'] = (count($tmp['VRA_CODE']) > 1) ? implode("','", $tmp['VRA_CODE']) : $tmp['VRA_CODE'][0];
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ END UPDATE RKT VRA ************************************************
      
      // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRASUM';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
    
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $par_sum['filename'] = $filename;
          $par_sum['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
          $par_sum['BA_CODE'] = $record1['BA_CODE'];
          $par_sum['VRA_CODE'] = $record1['VRA_CODE'];
          $model->updateSummaryRktVra($par_sum);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
      
      // ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
      $model = new Application_Model_NormaDistribusiVraNonInfra();
      $updated_row = $this->_db->fetchAll("{$model->getChangedData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NDISTVRANONINFRA';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //UPDATE DISTRIBUSI VRA - NON INFRA
      if (!empty($updated_row)) {
        foreach ($updated_row as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
          $model->updateRecord($record1);
          
          //distinct activity
          if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
            array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
          }
        }
      }
      $par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NDISTVRANONINFRASUM';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA
      if (!empty($updated_row)) {
        foreach ($updated_row as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
          $model->updateSummaryNormaDistribusiVra($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
      
      // ************************************************ UPDATE OPEX RKT VRA ************************************************
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktOpexVra';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($updated_row)) {
        foreach ($updated_row as $idx1 => $record1) {
          $par_opex_vra['filename'] = $filename;
          $par_opex_vra['key_find'] = $record1['BA_CODE'];
          $par_opex_vra['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
          $model->updateRktOpexVra($par_opex_vra);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ END UPDATE OPEX RKT VRA ************************************************
      
      // ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
      $model = new Application_Model_NormaPanenCostUnit();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPanenCostUnit';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
      
      // ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
      $model = new Application_Model_NormaPanenSupervisi();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPanenSupervisi';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
      
      // ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
      $model = new Application_Model_NormaPanenKraniBuah();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPanenKraniBuah';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ END UPDATE NORMA PANEN KRANI BUAH ************************************************
      
      // ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
      $model = new Application_Model_NormaPanenPremiLangsir();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPanenPremiLangsir';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
      
      // ************************************************ UPDATE NORMA PANEN LOADING ************************************************
      $model = new Application_Model_NormaPanenLoading();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPanenLoading';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file      
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA PANEN LOADING ************************************************
      
      // ************************************************ UPDATE NORMA BIAYA ************************************************
      $model = new Application_Model_NormaBiaya();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaBiaya';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ END UPDATE NORMA BIAYA ************************************************
      
      // ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
      $model = new Application_Model_NormaInfrastruktur();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaInfrastruktur';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ END UPDATE NORMA INFRASTRUKTUR ************************************************
      
      // ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
      $model = new Application_Model_NormaPerkerasanJalan();
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NormaPerkerasanJalan';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
          //hitung total cost
          $model->save($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
      
      // ************************************************ UPDATE RKT LC ************************************************
      $model = new Application_Model_RktLc();
      $records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
            
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktLc';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
          
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
          
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktLc_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
            
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
          //hitung total cost
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");      
      // ************************************************ UPDATE RKT LC ************************************************
      
      // ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
      $model = new Application_Model_RktManualNonInfra(); 
      $rec = $this->_db->fetchAll("{$model->getData($row)}"); 
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualNonInfra';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualNonInfra_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}"); 
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
      
      // ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
      $model = new Application_Model_RktKastrasiSanitasi();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");  
      
      //1. SAVE RKT MANUAL NON INFRA
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASICE';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          //hitung cost element
          $record1['filename'] = $filename;
          $model->calCostElement('TRANSPORT', $record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASITOTAL';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          //hitung total cost
          $record1['filename'] = $filename;
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
    
      // ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
      $model = new Application_Model_RktManualNonInfraOpsi();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualNonInfraOpsi';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualNonInfraOpsi_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");  
      // ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************ 
      
      // ************************************************ UPDATE RKT MANUAL INFRA ************************************************
      $model = new Application_Model_RktManualInfra();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualInfra';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualInfra_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
      $lastAfd = ""; $lastActClass = ""; $lastActCode = ""; $lastLandType = ""; $lastTopo = ""; $lastBA = ""; $lastBiaya = ""; 
      $arrAfdUpds = array(); // variabel array data afd yang di modified
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          $curAfd = $record1['AFD_CODE'];
          $curActClass = $record1['ACTIVITY_CLASS'];
          $curActCode = $record1['ACTIVITY_CODE'];
          $curLandType = $record1['LAND_TYPE'];
          $curTopo = $record1['TOPOGRAPHY'];
          $curBA = $record1['BA_CODE']; 
          $curBiaya = $record1['SUMBER_BIAYA']; 
          $curPeriod = $record1['PERIOD_BUDGET']; 
          
          //hitung total cost
          $model->calTotalCost($record1);
          
          if(($lastAfd != "") && (($lastAfd!=$curAfd)||($lastActClass!=$curActClass)||($lastActCode!=$curActCode)||($lastLandType!=$curLandType)||($lastTopo!=$curTopo))){
              array_push($arrAfdUpds, 
                     array('AFD_CODE'=>$lastAfd, 
                       'BA_CODE'=>$curBA, 
                       'SUMBER_BIAYA'=>$curBiaya, 
                       'TRX_CODE'=>$trxCode,
                       'ACTIVITY_CLASS'=>$lastActClass, 
                       'ACTIVITY_CODE'=>$lastActCode, 
                       'LAND_TYPE'=>$lastLandType, 
                       'TOPOGRAPHY'=>$lastTopo,
                       'PERIOD_BUDGET'=>$curPeriod
                      )
                     );
          }
          $lastAfd=$curAfd; 
          $lastActClass=$curActClass; 
          $lastActCode=$curActCode; 
          $lastLandType=$curLandType; 
          $lastTopo=$curTopo;
          $lastBA=$curBA; 
          $lastBiaya=$curBiaya;   
        }
        array_push($arrAfdUpds, 
           array('AFD_CODE'=>$lastAfd, 
             'BA_CODE'=>$curBA, 
             'SUMBER_BIAYA'=>$curBiaya, 
             'TRX_CODE'=>$trxCode,
             'ACTIVITY_CLASS'=>$lastActClass, 
             'ACTIVITY_CODE'=>$lastActCode, 
             'LAND_TYPE'=>$lastLandType, 
             'TOPOGRAPHY'=>$lastTopo,
             'PERIOD_BUDGET'=>$curPeriod
            )
           ); 
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");  

      //3. HITUNG DISTRIBUSI VRA
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktManualInfra_DistVra';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      // save Distribusi VRA per AFD
      $arrAfdFixs = array();
      $lastAfd = ""; $totalDistVraManInfra = 0; $totalHrgHMKM=0; $totalHrgInternal=0; $lastActCode="";

      //distinct data
      $tmp = array ();
      foreach ($arrAfdUpds as $row) {
        if (!in_array($row,$tmp)) array_push($tmp,$row);
      }
      
      foreach ($tmp as $key => $arrAfdUpd) { //disini harus di-akumulasi hasil perhitungan per-afdeling.
        $arrHitungDistVra = $model->hitungDistVra($arrAfdUpd);
        $curAfd = $arrAfdUpd['AFD_CODE'];
        $curPeriod = $arrAfdUpd['PERIOD_BUDGET'];
        $curBA = $arrAfdUpd['BA_CODE'];
        $curActCode = $arrAfdUpd['ACTIVITY_CODE'];
        
        //kelompokan per-afdeling
        if(($vraCode<>"") && ($lastAfd!="") && ($lastAfd!=$curAfd)){
          array_push($arrAfdFixs, 
            array('AFD_CODE'=>$lastAfd, 'totalDistVraManInfra'=>$tempDistVraManInfra, 'totalHrgHMKM'=>$tempHrgHMKM, 
            'totalHrgInternal'=>$tempHrgInternal, 'vraCode'=>$tempVraCode, 'BA_CODE'=>$lastBa, 'TRX_CODE'=>$trxCode, 
            'ACTIVITY_CODE'=>$tempActCode, 'PERIOD_BUDGET'=>$lastPeriod));
          
          //reset total hitungan disini
          $totalDistVraManInfra=0; $totalHrgHMKM=0; $totalHrgInternal=0; 
        }     
        
        $totalDistVraManInfra += $arrHitungDistVra['totalDistVraManInfra'];
        $totalHrgHMKM += $arrHitungDistVra['totalHrgHMKM'];
        $totalHrgInternal += $arrHitungDistVra['totalHrgInternal'];
        $vraCode = $arrHitungDistVra['vraCode'];
        
        $lastBa=$curBA; 
        $lastAfd=$curAfd;
        $lastPeriod=$curPeriod;       
        
        //digunakan untuk simpan record hitungan terakhir
        $tempDistVraManInfra=$totalDistVraManInfra; 
        $tempHrgHMKM=$totalHrgHMKM; 
        $tempHrgInternal=$totalHrgInternal; 
        $tempAfd=$curAfd; 
        $tempPeriod=$curPeriod; 
        $tempActCode=$curActCode;
        $tempVraCode=$vraCode;  
      }
      
      if($vraCode<>""){
        array_push($arrAfdFixs, 
          array('AFD_CODE'=>$tempAfd, 'totalDistVraManInfra'=>$tempDistVraManInfra, 'totalHrgHMKM'=>$tempHrgHMKM, 
          'totalHrgInternal'=>$tempHrgInternal, 'vraCode'=>$tempVraCode, 'BA_CODE'=>$lastBa, 'TRX_CODE'=>$trxCode, 
          'ACTIVITY_CODE'=>$tempActCode, 'PERIOD_BUDGET'=>$tempPeriod));
      }
        
      foreach ($arrAfdFixs as $key => $arrAfdFix) {
        $arrAfdFix['filename'] = $filename;
        $model->saveDistVra($arrAfdFix);
      }   
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT MANUAL INFRA************************************************ 

      // ************************************************ UPDATE RKT TANAM MANUAL ************************************************
      $model = new Application_Model_RktTanamManual();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanamManual';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanamManual_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT TANAM MANUAL ************************************************
      
      // ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
      $model = new Application_Model_RktTanam();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanam';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanam_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
      
      // ************************************************ UPDATE RKT PANEN ************************************************
      $model = new Application_Model_RktPanen();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanen';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
            
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanen_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $curAfd = $record1['AFD_CODE'];
          $curBA = $record1['BA_CODE'];
          $curBiaya = $record1['SUMBER_BIAYA'];
          $curPeriod = $record1['PERIOD_BUDGET'];
          
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
        
          if(($lastAfd) && ($lastAfd!=$curAfd)){
            array_push($arrAfdUpds, 
                   array('AFD_CODE'=>$lastAfd,
                     'BA_CODE'=>$lastBA,
                     'SUMBER_BIAYA'=>$lastBiaya,
                     'PERIOD_BUDGET'=>$lastPeriod)
                  );
          }
          $lastAfd = $curAfd; 
          $lastBA = $curBA; 
          $lastBiaya = $curBiaya;
          $lastPeriod = $curPeriod;
        }
      }
      array_push($arrAfdUpds, 
             array('AFD_CODE'=>$lastAfd,
               'BA_CODE'=>$lastBA,
               'SUMBER_BIAYA'=>$lastBiaya,
               'PERIOD_BUDGET'=>$lastPeriod)
            );
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      //3. HITUNG DISTRIBUSI VRA PER AFD
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'DIST_VRA';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      foreach ($arrAfdUpds as $key => $arrAfdUpd) {
        $arrAfdUpd['filename'] = $filename;
        $return = $model->saveDistVra($arrAfdUpd);
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT PANEN ************************************************
      
      // ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
      $model = new Application_Model_RktPerkerasanJalan();
      $records1 = $this->_db->fetchAll("{$model->getData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPerkerasanJalan';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
            
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      
      //2. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPerkerasanJalan_TotalCost';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $rec = $this->_db->fetchAll("{$model->getData($row)}");
        
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $curAfd = $record1['AFD_CODE'];
          $curBA = $record1['BA_CODE'];
          $curAct = $record1['ACTIVITY_CODE'];
          $curPer = $record1['PERIOD_BUDGET'];
        
          $record1['filename'] = $filename;
          
          //hitung total cost
          $model->calTotalCost($record1);
          $lastAfd = ""; $lastBA = ""; $lastBiaya = "";
          $arrAfdUpds = array(); // variabel array data afd yang di modified      
          
          if(($lastAfd) && ($lastAfd!=$curAfd)){
            array_push($arrAfdUpds, 
                   array('AFD_CODE'=>$lastAfd,
                     'BA_CODE'=>$lastBA,
                     'ACTIVITY_CODE'=>$lastAct,
                     'PERIOD_BUDGET'=>$lastPer)
                  );
          }
          $lastAfd = $curAfd; 
          $lastBA = $curBA; 
          $lastAct = $curAct;
          $lastPer = $curPer;
        }
        array_push($arrAfdUpds, 
           array('AFD_CODE'=>$lastAfd,
             'BA_CODE'=>$lastBA,
             'ACTIVITY_CODE'=>$lastAct,
             'PERIOD_BUDGET'=>$lastPer)
          );
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
      //3. HITUNG DISTRIBUSI VRA PER AFD
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPerkerasanJalan_DistVRA';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      foreach ($arrAfdUpds as $key => $arrAfdUpd) {
        $arrAfdUpd['filename'] = $filename;
        $return = $model->saveDistVra($arrAfdUpd);
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      // ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
      $model = new Application_Model_RktPupukDistribusiBiayaNormal();
      $records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPupukDistribusiBiayaNormal';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
            
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
      $model = new Application_Model_RktPupukDistribusiBiayaSisip();
      $records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPupukDistribusiBiayaSisip';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
            
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element
          $model->calCostElement('LABOUR', $record1);
          if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
        }
      }
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
      $model = new Application_Model_RktPupukDistribusiBiayaGabungan();
      
      //1. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $row['uniq_code_file'].'_TNBASIC_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPupukDistribusiBiayaGabungan';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
      $row['filename'] = $filename; 
      $model->calculateAllItem($row);
      
      //execute transaksi
      $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
      shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
      $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
      shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
      
      //pindahkan .sql ke logs
      $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
      if ( ! is_dir($uploaddir)) {
        $oldumask = umask(0);
        mkdir("$uploaddir", 0777, true);
        chmod("/".date("Y-m-d"), 0777);
        umask($oldumask);
      }
      shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
    }
  }
  
  public function getStatusPeriodeAction()
    {   
        $params = $this->_request->getParams();
    $value = $this->_formula->getStatusPeriode($params);
    die(json_encode($value));
    }
  
  //hapus data
  public function deleteAction()
    {
      $params = $this->_request->getParams();
    $uniq_code_file = $this->_global->genFileName();
    
    //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul    
    $lock = $this->_global->checkLockTable($params);    
    if($lock['JUMLAH']){
      $data['return'] = "locked";
      $data['module'] = $lock['MODULE'];
      $data['insert_user'] = $lock['INSERT_USER'];
      die(json_encode($data));
    }
    
    //generate filename untuk .sh dan .sql
    $filename = $uniq_code_file.'_00_TNBASIC_01_DELETE';
    $this->_global->createBashFile($filename); //create bash file
    $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
    
    $par['ROW_ID'] = base64_decode($params['rowid']);
    $par['filename'] = $filename;
    $this->_model->delete($par); //hapus data
    //get data untuk dihapus
    $records1 = $this->_db->fetchAll("{$this->_model->getData($par)}");
    
    //execute transaksi
    $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
    shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query   
    $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
    shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
    
    //pindahkan .sql ke logs
    $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
    if ( ! is_dir($uploaddir)) {
      $oldumask = umask(0);
      mkdir("$uploaddir", 0777, true);
      chmod("/".date("Y-m-d"), 0777);
      umask($oldumask);
    }
    shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
    
    
    if (!empty($records1)) {
      $idxInherit = 1;
      foreach ($records1 as $idx1 => $record1) {
        $record1['uniq_code_file']  = $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
        //update inherit module
        $this->updateInheritModule($record1);
      }
    }   
    
    $data['return'] = "done";
    die(json_encode($data));
  
  }

}
