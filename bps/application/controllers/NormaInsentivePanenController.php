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
  

  public function mappingAction(){
    $params = $this->_request->getParams();
    $rows = array();

    foreach ($params['text00'] as $key => $val) {
      if (($key > 0) && ($params['tChange'][$key])) {
        $rows[$key]['CHANGE']                  = $params['tChange'][$key];
        $rows[$key]['ROW_ID']                  = $params['text00'][$key];
        $rows[$key]['PERIOD_BUDGET']           = $params['text02'][$key];
        $rows[$key]['BA_CODE']                 = $params['text03'][$key];
        $rows[$key]['PERCENTAGE_INCENTIVE_1']  = $params['text04'][$key];
        $rows[$key]['INCENTIVE_1']             = $params['text05'][$key];
        $rows[$key]['PERCENTAGE_INCENTIVE_2']  = $params['text06'][$key];
        $rows[$key]['INCENTIVE_2']             = $params['text07'][$key];
        $rows[$key]['PERCENTAGE_INCENTIVE_3']  = $params['text08'][$key];
        $rows[$key]['INCENTIVE_3']             = $params['text09'][$key];
      }
    }
    return $rows;
  }

  public function saveAction(){
    $rows = $this->mappingAction();

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

      $filename = $this->_global->genFileName().'_00_TN_INSENTIVE_PANEN';
      $this->_global->createBashFile($filename); //create bash file     
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file


      foreach ($rows as $key => $row) {
        $row['filename'] = $filename;
        $return = $this->_model->save($row);
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

    $data['return'] = "done";
    die(json_encode($data));
  }

  public function getStatusPeriodeAction() {   
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
    
    $params['ROW_ID'] = base64_decode($params['rowid']);
    $this->_model->delete($params);
    
    $data['return'] = "done";
    die(json_encode($data));
  
  }

}
