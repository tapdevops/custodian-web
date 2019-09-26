<?php
/*
=========================================================================================================================
Project       :   Budgeting & Planning System
Versi       :   2.0.0
Deskripsi     :   Controller Class untuk Norma Pupuk TM
Function      : - getStatusPeriodeAction    : BDJ 22/07/2013  : cek status periode budget yang dipilih
            - listAction          : SID 24/06/2013  : menampilkan list norma Pupuk TM
            - mappingAction         : SID 24/06/2013  : mapping textfield name terhadap field name di DB
            - saveAllAction         : SID 24/06/2013  : kalkulasi seluruh data
            - saveAction          : SID 24/06/2013  : save data
            - updateInheritModule     : SID 02/07/2014  : update inherit module
            - deleteAction          : SID 24/06/2013  : hapus data
            
            - listAction    : menampilkan list Norma Pupuk TM
            - saveAction    : save data
            - deleteAction    : hapus data
Disusun Oleh    :   IT Enterprise Solution - PT Triputra Agro Persada
yaddi.surahman@tap-agri.co.id --- 2017-08-14
* initial copy from NormaPupukTbmTmController
=========================================================================================================================
*/
class NormaPupukTbmRekomendasiController extends Zend_Controller_Action
{
  private $_global = null;

  public function init()
  {
    $this->_db = Zend_Registry::get('db');
    $this->_global = new Application_Model_Global();
    $this->_formula = new Application_Model_Formula();
    $this->_model = new Application_Model_NormaPupukTbmRekomendasi();
    $this->view->input = $this->_model->getInput();
    
    $sess = new Zend_Session_Namespace('period');
    $this->_period = $sess->period;
  }

  public function indexAction()
  {
    $this->_redirect('/norma-pupuk-tbm-rekomendasi/main');
  }

  public function mainAction()
  {
    $this->view->title = 'Data &raquo; Norma Pupuk TMB Rekomendasi';
    $this->view->period = date("Y", strtotime($this->_period));
    $this->_helper->layout->setLayout('norma');   
    $this->view->referencerole = $this->_model->_referenceRole;
  }
  
  //cek status periode budget yang dipilih
  public function getStatusPeriodeAction()
  {   
    $params = $this->_request->getParams();
    $value = $this->_formula->getStatusPeriode($params);
    die(json_encode($value));
  }
  
  //menampilkan list Norma Pupuk TM
  public function listAction()
  {   
    $this->_helper->viewRenderer->setNoRender(true);

    $params = $this->_request->getPost(); 
    $data = $this->_model->getList($params);
    die(json_encode($data));
  }
  
  //mapping textfield name terhadap field name di DB
  public function mappingAction(){
    $params = $this->_request->getParams();
    $rows = array();
    
    foreach ($params['text00'] as $key => $val) {
      if (($key > 0) && ($params['tChange'][$key])) {
        $rows[$key]['CHANGE']         = $params['tChange'][$key]; // CHANGE
        $rows[$key]['ROW_ID']         = $params['text00'][$key]; // ROW ID
        $rows[$key]['BA_CODE']        = $params['text03'][$key]; // BA_CODE
        $rows[$key]['HA_PUPUK']       = $params['text09'][$key]; // HA_PUPUK
        $rows[$key]['POKOK']        = $params['text11'][$key]; // POKOK
        $rows[$key]['DOSIS']          = $params['text15'][$key]; // DOSIS
        $rows[$key]['PRICE']        = $params['text17'][$key]; // PRICE
        
        //deklarasi var utk inherit module
        $rows[$key]['key_find']     = $params['text03'][$key]; // BA_CODE
      }
    }
    return $rows;
  }
  
  //simpan data sementara sesuai input user
  public function saveTempAction()
  {
    $rows = $this->mappingAction();
    
    if (!empty($rows)) {    
      // ************************************************ SAVE NORMA PUPUK TM TEMP ************************************************
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName();
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save norma biaya temp
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
      // ************************************************ SAVE NORMA PUPUK TM TEMP ************************************************
    }
    
    die('no_alert');
  }
  
  //save data
  public function saveAction()
  {
    $rows = $this->mappingAction();
    $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
    
    if (!empty($rows)) {
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_NPUPUKTM_01_SAVETEMP';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
    
      foreach ($rows as $key => $row) {
        $row['filename'] = $filename;
        $this->_model->saveTemp($row);
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
    }
    
    // ************************************************ SAVE ALL NORMA PUPUK TM ************************************************
    //generate filename untuk .sh dan .sql
    $filename = $uniq_code_file.'_00_NPUPUKTM_02_SAVE';
    $this->_global->createBashFile($filename); //create bash file   
    $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
    //hitung seluruh halaman
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
    // ************************************************ SAVE ALL NORMA PUPUK TM ************************************************
    
    
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    if (!empty($updated_row)) {
      $idxInherit = 1;
      foreach ($updated_row as $idx1 => $record1) {
        //deklarasi var utk inherit module
        $record1['key_find']      = $record1['BA_CODE']; // BA_CODE
        $record1['afd_code']      = $record1['AFD_CODE']; // AFD_CODE 
        $record1['block_code']      = $record1['BLOCK_CODE']; // BLOCK_CODE   
        $record1['uniq_code_file']    = $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename    
        
        //update inherit module
        $this->updateInheritModule($record1);
          
        $idxInherit++;
      }
    }
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    
    $data['return'] = "done";
    die(json_encode($data));
  }
  
  //hapus data
  public function deleteAction()
  {
    $params = $this->_request->getParams();
    $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
    
    // ************************************************ UPDATE NORMA PUPUK ************************************************
    //generate filename untuk .sh dan .sql
    $filename = $uniq_code_file.'_00_NPUPUKTM_01_DELETE';
    $this->_global->createBashFile($filename); //create bash file
    $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
    
    $params['filename'] = $filename;
    $this->_model->delete($params);
    
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
    // ************************************************ UPDATE NORMA PUPUK ************************************************
    
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    $params['filename']   = $uniq_code_file.'_00_NPUPUKTM_02_DELETE_INHERIT_DATA';
    $params['afd_code']   = $params['AFD_CODE']; // AFD_CODE  
    $params['block_code']   = $params['BLOCK_CODE']; // BLOCK_CODE  
    
    $this->updateInheritModule($params);
    // ************************************************ UPDATE INHERIT MODULE ************************************************
    
    $data['return'] = "done";
    die(json_encode($data));
  }
  
  //update inherit module
  public function updateInheritModule($row = array()) {
    if (!empty($row)) {   
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI HA ************************************************
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_01_RKTPUPUKHA';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      //print_r($row);die();
      //save
      $model = new Application_Model_RktPupukHa();
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
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI HA ************************************************
            
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI KG NORMAL ************************************************
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_02_RKTPUPUKKGNORMAL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save
      $model = new Application_Model_RktPupukKgNormal();  
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
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI KG NORMAL ************************************************
            
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI KG SISIP ************************************************
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_03_RKTPUPUKKGSISIP';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save
      $model = new Application_Model_RktPupukKgSisip(); 
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
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI KG SISIP ************************************************
      
      // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
      $model = new Application_Model_RktPupukDistribusiBiayaNormal(); 
      $rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
      
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_04_RKTPUPUKBIAYANORMAL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;         
          $model->calCostElement('MATERIAL', $record1);
          $model->calCostElement('LABOUR', $record1);
          $model->calCostElement('TOOLS', $record1);
          $model->calCostElement('TRANSPORT', $record1);
          $model->calCostElement('CONTRACT', $record1);
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
      $rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
      
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_05_RKTPUPUKBIAYASISIP';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      if (!empty($rec)) {
        foreach ($rec as $idx1 => $record1) {
          $record1['filename'] = $filename;         
          $model->calCostElement('MATERIAL', $record1);
          $model->calCostElement('LABOUR', $record1);
          $model->calCostElement('TOOLS', $record1);
          $model->calCostElement('TRANSPORT', $record1);
          $model->calCostElement('CONTRACT', $record1);
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
      //generate filename untuk .sh dan .sql
      $filename = $row['uniq_code_file'].'_NPUPUKTM_06_RKTPUPUKBIAYA';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save
      $model = new Application_Model_RktPupukDistribusiBiayaGabungan(); 
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
  
  public function updLockedSeqStatusAction(){
    $this->_helper->viewRenderer->setNoRender(true);

    $params = $this->_request->getPost();
    $params['task_name'] = "TN_PUPUK_TMBM2_TM";
    $data = $this->_global->updLockedSeqStatus($params);
    die(json_encode($data));
  }
  
  public function chkEnhLockedSequenceAction(){
    $this->_helper->viewRenderer->setNoRender(true);

    $params = $this->_request->getPost();
    $params['task_name'] = "TN_PUPUK_TMBM2_TM";
    $data = $this->_global->chkEnhLockedSequence($params);
    die(json_encode($data));
  }
  
  public function checkLockedSeqAction(){ 
    $this->_helper->viewRenderer->setNoRender(true);

    $params = $this->_request->getPost();
    $params['task_name'] = "TN_PUPUK_TMBM2_TM";
    $data = $this->_global->checkLockSequence($params);
    die(json_encode($data));
  }
}
