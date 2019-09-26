<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Distribusi VRA
Function 			:	- listAction		: menampilkan list norma distribusi VRA
						- saveAction		: save data
						- deleteAction		: hapus data
						- getStatusPeriodeAction
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	03/07/2013
Update Terakhir		:	03/07/2013
Revisi				:	
=========================================================================================================================
*/
class NormaDistribusiVraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaDistribusiVra();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period=$sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-distribusi-vra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; Distribusi VRA - Infra';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole;
    }
	
	//menampilkan list norma alat kerja non panen
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }

  // yaddi.surahman@tap-agri.co.id
  function testAction() {
    $this->_helper->viewRenderer->setNoRender(true);
    $params = $this->_request->getPost();
    $data = $this->_model->getData2($params);
    die(json_encode($data));
  }

	
	//menampilkan list norma alat kerja non panen
    public function listafdAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getListAfd($params);
        die(json_encode($data));
    }
	
	//save data
	public function saveAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		$rowsAfd = array();
		$row_err = array();
		$row_success = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['TRX_CODE']        	= $params['text00'][$key]; // TRX_CODE
				$rows[$key]['ACTIVITY_CODE']   	= $params['text02'][$key]; // ACTIVITY_CODE
				$rows[$key]['VRA_CODE']   		= $params['text04'][$key]; // VRA_CODE
				for($x=13;$x<63;$x++){
					if(trim($params['text'.$x.'_2'][$key])<>""){
						$rowsAfd[$key][$params[('text'.$x.'_1')][$key]] = $params[('text'.$x.'_2')][$key]; 
					}else{
						break;
					}
				}
				$rowsAfd[$key]['BIBITAN']   		= $params['text9_2'][$key];
				$rowsAfd[$key]['BASECAMP']   		= $params['text10_2'][$key];
				$rowsAfd[$key]['UMUM']   			= $params['text11_2'][$key];
				$rowsAfd[$key]['LAIN']   			= $params['text12_2'][$key];
            }
        }
		
		/*foreach ($rows as $key => $row) {
			if($row['CHANGE'] == "Y") {
				$return = $this->_model->save($row,$rowsAfd[$key],$params['key_find']);
				
				if (!$return){
					$row_err[] = $key;
				}else{
					$row_success[] = $key;
				}
			}
        }*/
		
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
			
		// ************************************************ SAVE NORMA DISTRIBUSI VRA ************************************************
		//generate filename untuk .sh dan .sql
		$uniq_code_file = $this->_global->genFileName();
		$filename = $uniq_code_file.'_00_TNDISTRIBUSI_VRA';
		$this->_global->createBashFile($filename); //create bash file
		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
		foreach ($rows as $key => $row) {
			if($row['CHANGE'] == 'Y'){
				$row['filename'] = $filename;
				$this->_model->save($row,$rowsAfd[$key],$params['key_find']);
				
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
		// ************************************************ SAVE NORMA DISTRIBUSI VRA ************************************************
	}	
		//update summary
		//$this->_model->updateSummaryNormaDistribusiVra($params['key_find']);
		//2. HITUNG TOTAL COST
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.'_TNDISTRIBUSIVRA_SUM_';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$this->_model->updateSummaryNormaDistribusiVra($params['key_find']);
			
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
			
		
		/*if ($row_err){
			$data = implode(',',$row_err);
			$data=trim($data,',');
			die("Data ke ".$data." Tidak Berhasil Disimpan.");
		}elseif ($row_success){
			die('done');
		}else{
			die('no_alert');
		}*/
		$data['return'] = "done";
		die(json_encode($data));
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
        $rows = array();
		
		$return = $this->_model->delete(base64_decode($params['trxcode']));
		
		if ($return){
			//update summary
			$this->_model->updateSummaryNormaDistribusiVra($params['key_find']);
						
			die('done');
		}else{
			die("Data Tidak Berhasil Dihapus.");
		}
    }

}
