<?php
/*
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   3.0.0
Deskripsi     :   Controller Class untuk Calculate RKT
Function      : 
Disusun Oleh    :   IT Solution - PT Triputra Agro Persada  
Developer     :   Nicholas Budihardja
Dibuat Tanggal    :   06/05/2015
Update Terakhir   : 06/05/2015
Revisi        : 
=========================================================================================================================
*/
class CalculateRktController extends Zend_Controller_Action
{
  private $_global = null;

  public function init()
  {
    $this->_db = Zend_Registry::get('db');
    $this->_global = new Application_Model_Global();
    $this->_model = new Application_Model_CalculateRkt();
    $this->_formula = new Application_Model_Formula();
    $this->view->input = $this->_model->getInput();
    $sess = new Zend_Session_Namespace('period');
    $this->_period = $sess->period;
  }

  public function indexAction()
  {
    $this->_redirect('/report/main');
  }

  public function mainAction()
  {
    $this->view->period = date("Y", strtotime($this->_period));
    $this->view->referencerole = $this->_model->_referenceRole;
    $this->view->userrole = $this->_model->_userRole;
  }
  
  

  //calculate RKT
  public function calcRktAction()
  { 
    $this->view->period = date("Y", strtotime($this->_period));
    $params = $this->_request->getParams();
    
    //get data RKT
    ////////////////////////////////////////////////////////////RKT RAWAT//////////////////////////////////////////////////////////////////////
    if($params['jenis_report'] == "rawat"){
      $rows = $this->_model->getListRawat($params);
    }
    ////////////////////////////////////////////////////////////END RKT RAWAT//////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////RKT RAWAT + OPSI/////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "rawat_opsi"){
      $rows = $this->_model->getListRawatOpsi($params);
    }
    //////////////////////////////////////////////////////////END RKT RAWAT + OPSI/////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////RKT RAWAT TANAM////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "tanam_otomatis"){
      $rows = $this->_model->getListTanamAuto($params);
    }
    //////////////////////////////////////////////////////////END RKT RAWAT TANAM//////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////RKT PANEN//////////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "panen"){
      $rows = $this->_model->getListPanen($params);
    }
    //////////////////////////////////////////////////////////END RKT PANEN///////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////RKT KASTRASI SANITASI///////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "kastrasi_sanitasi"){
      $rows = $this->_model->getListKasSan($params);
    }
    /////////////////////////////////////////////////////END RKT KASTRASI SANITASI/////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////RKT RAWAT SISIP//////////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "rawat_sisip"){
      $rows = $this->_model->getListRawatSisip($params);
    }
    //////////////////////////////////////////////////////////END RKT RAWAT SISIP//////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////RKT MANUAL INFRA//////////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == 'calc_infra'){
      //$rows = $this->_model->getListManualInfra($params);
      $rows_jalan = $this->_model->getListPerkerasanJalan($params);
    }
    
    //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
    foreach ($rows['rows'] as $key => $row) {
      $params['key_find'] = $row['BA_CODE'];
      $lock = $this->_global->checkLockTable($params);    
      if($lock['JUMLAH']){
        $data['return'] = "locked";
        $data['module'] = $lock['MODULE'];
        $data['insert_user'] = $lock['INSERT_USER'];
        die(json_encode($data));
      }
    }
    
    //calculate RKT
    ////////////////////////////////////////////////////////////RKT RAWAT//////////////////////////////////////////////////////////////////////
    if ($params['jenis_report'] == 'calc_infra') {

      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = '1'.$this->_global->genFileName();
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
        $row['filename'] = $filename;
        $this->_model->saveRotationInfra($row);
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

      //2. SIMPAN RKT INFRA
      //generate filename untuk .sh dan .sql
      $filename = '2'.$this->_global->genFileName();
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

      //hitung distribusi biaya seluruh halaman
      $params = $this->_request->getPost();
      $records1 = $this->_db->fetchAll("{$this->_model->getDataInfra($params)}");
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element labour
          $this->_model->calCostElementManualInfra('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementManualInfra('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementManualInfra('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementManualInfra('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementManualInfra('CONTRACT', $record1);
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

      //3. SIMPAN TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = '3'.$this->_global->genFileName();
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

      //hitung distribusi biaya seluruh halaman
      $params = $this->_request->getPost();
      $records1 = $this->_db->fetchAll("{$this->_model->getDataInfra($params)}");
      $lastAfd = ""; $lastActClass = ""; $lastActCode = ""; $lastLandType = ""; $lastTopo = ""; $lastBA = ""; $lastBiaya = ""; 
      $arrAfdUpds = array(); // variabel array data afd yang di modified
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $curAfd = $record1['AFD_CODE'];
          $curActClass = $record1['ACTIVITY_CLASS'];
          $curActCode = $record1['ACTIVITY_CODE'];
          $curLandType = $record1['LAND_TYPE'];
          $curTopo = $record1['TOPOGRAPHY'];
          $curBA = $record1['BA_CODE'];
          $curBiaya = $record1['SUMBER_BIAYA'];
          $curPeriod = $record1['PERIOD_BUDGET'];

          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostManualInfra($record1);

          if(($lastAfd != "") && (($lastAfd!=$curAfd)||($lastActClass!=$curActClass)||($lastActCode!=$curActCode)||($lastLandType!=$curLandType)||($lastTopo!=$curTopo))){
            array_push($arrAfdUpds, 
              array(
                'AFD_CODE'=>$lastAfd, 
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

      //4. HITUNG DISTRIBUSI VRA
      //generate filename untuk .sh dan .sql
      $filename = '4'.$this->_global->genFileName();
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

      $params['filename'] = $filename;
      $this->_model->calculateDistribusiVRA($params);

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



      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_RKTPK_01_saveRotation';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save plan untuk hitung total rotasi
      foreach ($rows_jalan['rows'] as $key => $row) {
        $return_cek = $this->_model->checkDataPerkerasanJalan($row);
        if($return_cek['status'] == 1) {
          $data['return'] = "empty";
          die(json_encode($data));
        }
        if ($row['CHANGE']) {
          $row['filename'] = $filename;
          $return = $this->_model->saveRotationPerkerasanJalan($row);
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_RKTPK_02_CostElement';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      $params = $this->_request->getPost();
      
      $records1 = $this->_db->fetchAll("{$this->_model->getDataPerkerasanJalan($params)}");
      //print_r($params); exit;
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element labour
          $this->_model->calCostElementPerkerasanJalan('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementPerkerasanJalan('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementPerkerasanJalan('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementPerkerasanJalan('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementPerkerasanJalan('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_RKTPK_03_calTotalCost';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      $params = $this->_request->getPost();
      $records1 = $this->_db->fetchAll("{$this->_model->getDataPerkerasanJalan($params)}");
      $lastAfd = ""; $lastBA = ""; $lastAct = "";
      $arrAfdUpds = array(); // variabel array data afd yang di modified
      if (!empty($records1)) {
        foreach ($records1 as $idx1 => $record1) {
          $curAfd = $record1['AFD_CODE'];
          $curBA = $record1['BA_CODE'];
          $curAct = $record1['ACTIVITY_CODE'];
          $curPer = $record1['PERIOD_BUDGET'];
          
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostPerkerasanJalan($record1);
          
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
      }
      array_push($arrAfdUpds, 
             array('AFD_CODE'=>$lastAfd,
               'BA_CODE'=>$lastBA,
               'ACTIVITY_CODE'=>$lastAct,
               'PERIOD_BUDGET'=>$lastPer)
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
      
      //4. HITUNG DISTRIBUSI VRA PER AFD
      //generate filename untuk .sh dan .sql
      $filename = $uniq_code_file.'_00_RKTPK_04_saveDistVra';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      foreach ($arrAfdUpds as $key => $arrAfdUpd) {
        $arrAfdUpd['filename'] = $filename;
        $return = $this->_model->saveDistVraPerkerasanJalan($arrAfdUpd);
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
      
    ////////////////////////////////////////////////////////////END RKT MANUAL INFRA///////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////// RKT RAWAT ////////////////////////////////////////////////////////////////////
    } else if($params['jenis_report'] == "rawat"){
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName()."SAVEROTATIONCALCALL";
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
          $row['filename'] = $filename;
          if($row['ACTIVITY_CODE'] != '42700'){
            $return = $this->_model->saveRotationRawat($row);
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName()."COSTELEMENTCALCALL";
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      
      if (!empty($rows['rows'])) {
        foreach ($rows['rows']  as $idx1 => $record1) {
          $record1['filename'] = $filename;
          if($record1['ACTIVITY_CODE'] != '42700'){
            //hitung cost element labour
            $this->_model->calCostElementRawat('LABOUR', $record1);
            //hitung cost element material
            $this->_model->calCostElementRawat('MATERIAL', $record1);
            //hitung cost element tools
            $this->_model->calCostElementRawat('TOOLS', $record1);
            //hitung cost element transport
            $this->_model->calCostElementRawat('TRANSPORT', $record1);
            //hitung cost element contract
            $this->_model->calCostElementRawat('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName()."TOTALCOSTCALCALL";
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          if($record1['ACTIVITY_CODE'] != '42700'){
            $record1['filename'] = $filename;
            //hitung total cost
            $this->_model->calTotalCostRawat($record1);
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
      
    }
    ////////////////////////////////////////////////////////////END RKT RAWAT//////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////// RKT RAWAT + OPSI ////////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "rawat_opsi"){
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = 'RawatOpsi01_'.$this->_global->genFileName().'SAVEROTATIONCALCALL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
          $row['filename'] = $filename;
          $return = $this->_model->saveRotationRawatOpsi($row);
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = "RawatOpsi02_".$this->_global->genFileName().'COSTELEMENTCALCALL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung cost element labour
          $this->_model->calCostElementRawatOpsi('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementRawatOpsi('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementRawatOpsi('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementRawatOpsi('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementRawatOpsi('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = 'RawatOpsi03_'.$this->_global->genFileName().'TOTALCOSTCALCALL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostRawatOpsi($record1);
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
      
    }
    ////////////////////////////////////////////////////////END RKT RAWAT + OPSI///////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////// RKT TANAM OTOMATIS ////////////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "tanam_otomatis"){
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = '01.RKTTANAMOTOMATIS'.$this->_global->genFileName().'CALCALL';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
        $row['filename'] = $filename;
        $this->_model->saveRotationTanam($row);
        $this->_model->saveTempTanam($row);
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
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = '02.RKTTANAMOTOMATISCOSTELEMENT'.$this->_global->genFileName().'CALCALL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element labour
          $this->_model->calCostElementTanamAuto('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementTanamAuto('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementTanamAuto('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementTanamAuto('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementTanamAuto('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = '03.RKTTANAMOTOMATISTOTALCOST'.$this->_global->genFileName().'CALCALL';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostTanamAuto($record1);
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
    }
    ////////////////////////////////////////////////////////END RKT TANAM OTOMATIS///////////////////////////////////////////////////////////////////
    
    /**
     * 2017-08-01
     * yaddi.surahman@tap-agri.co.id
     * Hitung RKT Panen, penambahan premi panen dan perubahan formula premi janjang lebih
     */
    else if($params['jenis_report'] == "panen"){
      $arr['BA_CODE'] = $params['key_find'];
      $arr['PRE_OER'] = $rows['rows']['OER_BA'];
      $arr['PERIOD_BUDGET'] = $params['budgetperiod'];
      $urutan=0;
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $this->_global->genFileName().'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveRotationCalcAll';
      $this->_global->createBashFile($filename); //create bash file
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      $arr['filename'] = $filename;
      //$this->_model->saveOer($arr);
      
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $idx1 => $row) {
        $row['filename'] = $filename;
        $return = $this->_model->saveRotationPanen($row);
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $this->_global->genFileName()."0$urutan_PANEN_calCostElementCalcAll";
      $filename = $this->_global->genFileName().'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_calCostElement';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung cost element labour
          $this->_model->calCostElementPanen('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementPanen('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementPanen('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementPanen('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementPanen('CONTRACT', $record1);
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
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $this->_global->genFileName().'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_calTotalCostCalcAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      $lastAfd = ""; $lastBA = ""; $lastBiaya = "";
      $arrAfdUpds = array(); // variabel array data afd yang di modified
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $curAfd = $record1['AFD_CODE'];
          $curBA = $record1['BA_CODE'];
          $curBiaya = $record1['SUMBER_BIAYA'];
          $curPeriod = $record1['PERIOD_BUDGET'];
          
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostPanen($record1);
          
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
      
      //4. HITUNG DISTRIBUSI VRA PER AFD
      //generate filename untuk .sh dan .sql
      $urutan++;
      $filename = $this->_global->genFileName().'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveDistVraCalcAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      foreach ($arrAfdUpds as $key => $arrAfdUpd) {
        $arrAfdUpd['filename'] = $filename;
        $return = $this->_model->saveDistVra($arrAfdUpd);
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
    ////////////////////////////////////////////////////////END RKT PANEN///////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////// RKT KASTRASI SANITASI /////////////////////////////////////////////////////////////
    else if($params['jenis_report'] == "kastrasi_sanitasi"){
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_01_RKTKastrasiSanitasi_saveRotationCalcAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
          $row['filename'] = $filename;
          $this->_model->saveRotationKasSan($row);
          $this->_model->saveTempKasSan($row);
        
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_02_RKTKastrasiSanitasi__calCostElement';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman     
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung cost element labour
          $this->_model->calCostElementKasSan('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementKasSan('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementKasSan('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementKasSan('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementKasSan('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_03_RKTKastrasiSanitasi_calTotalCostCalcAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostKasSan($record1);
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
      
    }
    ///////////////////////////////////////////END RKT KASTRASI SANITASI////////////////////////////////////////////////////////
    /////////////////////////////////////////// RKT RAWAT SISIP //////////////////////////////////////////////////////////
    // else if($params['jenis_report'] == "rawat_sisip"){
    // yaddi.surahman@tap-agri.co.id 
    // jika jenis report rawat, maka hitung juga data rawat sisip
    
    if($params['jenis_report'] == "rawat"){
      $rows = $this->_model->getListRawatSisip($params);
    }

    foreach ($rows['rows'] as $key => $row) {
      $params['key_find'] = $row['BA_CODE'];
      $lock = $this->_global->checkLockTable($params);    
      if($lock['JUMLAH']){
        $data['return'] = "locked";
        $data['module'] = $lock['MODULE'];
        $data['insert_user'] = $lock['INSERT_USER'];
        die(json_encode($data));
      }
    }
    
    if($params['jenis_report'] == "rawat_sisip"){
      //1. SIMPAN INPUTAN USER
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_01_RKTRawatSisip_saveRotationCalcAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
          
      //save plan untuk hitung total rotasi
      foreach ($rows['rows'] as $key => $row) {
          $row['filename'] = $filename;
          $return = $this->_model->saveRotationRawatSisip($row);
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
      
      
      //2. HITUNG PER COST ELEMENT
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_02_RKTRawatSisip_CalcCOSTELEMENT';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          
          //hitung cost element labour
          $this->_model->calCostElementRawatSisip('LABOUR', $record1);
          //hitung cost element material
          $this->_model->calCostElementRawatSisip('MATERIAL', $record1);
          //hitung cost element tools
          $this->_model->calCostElementRawatSisip('TOOLS', $record1);
          //hitung cost element transport
          $this->_model->calCostElementRawatSisip('TRANSPORT', $record1);
          //hitung cost element contract
          $this->_model->calCostElementRawatSisip('CONTRACT', $record1);
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
      
      
      //3. HITUNG TOTAL COST
      //generate filename untuk .sh dan .sql
      $filename = $this->_global->genFileName().'_03_RKTRawatSisip_CalcCOSTELEMENTAll';
      $this->_global->createBashFile($filename); //create bash file   
      $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
      
      //hitung distribusi biaya seluruh halaman
      if (!empty($rows['rows'])) {
        foreach ($rows['rows'] as $idx1 => $record1) {
          $record1['filename'] = $filename;
          //hitung total cost
          $this->_model->calTotalCostRawatSisip($record1);
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
      
    }
    ////////////////////////////////////////////END RKT RAWAT SISIP////////////////////////////////////////////////////////
    
    //get last generate date
    $result = $this->_model->getLastCalculate($params);
    
    $data['return'] = "done";
    $data['last_update_user'] = $result['INSERT_USER'];
    $data['last_update_time'] = $result['INSERT_TIME'];
    die(json_encode($data));
  }
  
  //get last generate date
  public function getLastCalculateAction()
  {
    $this->view->period = date("Y", strtotime($this->_period));
    $params = $this->_request->getParams();
    
    //get last generate date
    $result = $this->_model->getLastCalculate($params);
    
    $data['last_update_user'] = $result['INSERT_USER'];
    if($result['INSERT_TIME'] <> '')
    $data['last_update_time'] = date("d-m-Y H:i:s", strtotime($result['INSERT_TIME']));
    else $data['last_update_time'] = $result['INSERT_TIME'];
    die(json_encode($data));
  }
  
  public function chkEnhLockedSequenceAction(){
    $this->_helper->viewRenderer->setNoRender(true);

    $params = $this->_request->getPost();
    if($params['jenis_report'] == 'rawat'){
      $params['task_name'] = "TR_RKT_RAWAT";
    }else if($params['jenis_report'] == 'rawat_opsi'){
      $params['task_name'] = "TR_RKT_RAWAT_OPSI";
    }else if($params['jenis_report'] == 'tanam_otomatis'){
      $params['task_name'] = "TR_RKT_TANAM";
    }else if($params['jenis_report'] == 'panen'){
      $params['task_name'] = "TR_RKT_PANEN";
    }else if($params['jenis_report'] == 'kastrasi_sanitasi'){
      $params['task_name'] = "TR_RKT_KASTRASI_SANITASI";
    }else if($params['jenis_report'] == 'rawat_sisip'){
      $params['task_name'] = "TR_RKT_RAWAT_OPSI";
    }
      
    $data = $this->_global->chkEnhLockedSequence($params);
    die(json_encode($data));
  }
  
  ////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
  //menampilkan list
  private function _listAction()
  {
    $params = $this->_request->getParams();
    
    $table = new Application_Model_Report();
    $this->_helper->layout->disableLayout();
    $this->view->data = $table->initList($params);
    $this->view->last_data = $table->getLastCalculate($params);
  }
  ////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
