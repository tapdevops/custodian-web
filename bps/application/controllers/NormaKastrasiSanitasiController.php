<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Kastrasi Sanitasi
Function 			:	- listAction		: menampilkan list norma Kastrasi Sanitasi
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	28/06/2013
Update Terakhir		:	28/06/2013
Revisi				:						
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class NormaKastrasiSanitasiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaKastrasiSanitasi();
        $this->_trmodel = new Application_Model_RktKastrasiSanitasi();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-kastrasi-sanitasi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Kastrasi Sanitasi';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list norma Kastrasi Sanitasi
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_KASTRASI_SANITASI";
        $data = $this->_model->getList($params);
		$data['cek'] = $this->_global->checkLockSequence($params);
        die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_KASTRASI_SANITASI";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_KASTRASI_SANITASI";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_KASTRASI_SANITASI";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
	
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0){
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']      	= $params['text06'][$key]; // PERIOD_BUDGET
				$rows[$key]['ACTIVITY_CODE']      	= $params['text02'][$key]; // BA_CODE
				$rows[$key]['LAND_SUITABILITY']  	= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['UMUR']  				= $params['text05'][$key]; // AFD_CODE
			}
		}
		
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
		//update umur tanam
		$rows = $this->mappingAction();
		$params = $this->_request->getPost();
		foreach ($rows as $key => $row) {
			$this->_model->updateNormaKastrasiSanitasi($row);
		}
		
		//====================================================================
		//$rows = $this->_trmodel->getNormaList($params);
		$rows = $this->_db->fetchAll("{$this->_trmodel->getData($params)}");
		
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
		foreach ($rows as $key => $row) {
			//update plan
			$row['filename'] = $filename;
			$this->_trmodel->updateTempKastrasi($row);
				
			$params['key_find'] = $row['BA_CODE'];
			$lock = $this->_global->checkLockTable($params);		
			if($lock['JUMLAH']){
				$data['return'] = "locked";
				$data['module'] = $lock['MODULE'];
				$data['insert_user'] = $lock['INSERT_USER'];
				die(json_encode($data));
			}
		}
		
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$this->_trmodel->saveRotation($row);
				$this->_trmodel->saveTemp($row);
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
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_trmodel->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				
				//hitung cost element labour
				$this->_trmodel->calCostElement('LABOUR', $record1);
				//hitung cost element material
				$this->_trmodel->calCostElement('MATERIAL', $record1);
				//hitung cost element tools
				$this->_trmodel->calCostElement('TOOLS', $record1);
				//hitung cost element transport
				$this->_trmodel->calCostElement('TRANSPORT', $record1);
				//hitung cost element contract
				$this->_trmodel->calCostElement('CONTRACT', $record1);
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
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_trmodel->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung total cost
				$this->_trmodel->calTotalCost($record1);
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
		
		$data['return'] = "done";
		die(json_encode($data));
    }
}
