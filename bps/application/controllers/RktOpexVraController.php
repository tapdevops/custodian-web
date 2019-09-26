<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk RKT OPEX VRA
Function 			:	- listAction		: menampilkan list RKT OPEX VRA
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	22/07/2013
Revisi				:	
YUL 11/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class RktOpexVraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_RktOpexVra();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-opex-vra/main');
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT OPEX - VRA';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT OPEX VRA
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//mapping action
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0){
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['ROW_ID_TEMP']        	= $params['rowidtemp'][$key]; // ROW_ID_TEMP
				$rows[$key]['TRX_RKT_CODE']        	= $params['trxcode'][$key]; // ROW_ID_TEMP
				$rows[$key]['PERIOD_BUDGET']    	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['COA_CODE']      		= $params['text04'][$key]; // COA_CODE
				$rows[$key]['GROUP_BUM_CODE']  		= $params['text06'][$key]; // GROUP_BUM_CODE
				$rows[$key]['ACTUAL'] 				= $params['text08'][$key]; // ACTUAL
				$rows[$key]['TAKSASI']        		= $params['text09'][$key]; // TAKSASI
				$rows[$key]['ANTISIPASI']        	= $params['text10'][$key]; // ANTISIPASI
				$rows[$key]['PERSENTASE_INFLASI']   = $params['text11'][$key]; // PERSENTASE_INFLASI
				$rows[$key]['TOTAL_BIAYA']        	= $params['text12'][$key]; // TOTAL_BIAYA
				$rows[$key]['DIS_JAN']      		= $params['text13'][$key]; // DIS_JAN
				$rows[$key]['DIS_FEB']      		= $params['text14'][$key]; // DIS_FEB
				$rows[$key]['DIS_MAR']      		= $params['text15'][$key]; // DIS_MAR
				$rows[$key]['DIS_APR']      		= $params['text16'][$key]; // DIS_APR
				$rows[$key]['DIS_MAY']      		= $params['text17'][$key]; // DIS_MAY
				$rows[$key]['DIS_JUN']      		= $params['text18'][$key]; // DIS_JUN
				$rows[$key]['DIS_JUL']      		= $params['text19'][$key]; // DIS_JUL
				$rows[$key]['DIS_AUG']      		= $params['text20'][$key]; // DIS_AUG
				$rows[$key]['DIS_SEP']      		= $params['text21'][$key]; // DIS_SEP
				$rows[$key]['DIS_OCT']      		= $params['text22'][$key]; // DIS_OCT
				$rows[$key]['DIS_NOV']      		= $params['text23'][$key]; // DIS_NOV
				$rows[$key]['DIS_DEC']      		= $params['text24'][$key]; // DIS_DEC
				$rows[$key]['KETERANGAN']      		= $params['text25'][$key]; // KETERANGAN
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
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
		
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) { 
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
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
		
		//2. HITUNG RKT OPEX VRA
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}"); 
		if (!empty($records1)) { //print_r($records1); die('DIE');
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung total cost
				$this->_model->save($record1);
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
	
	//save data temp
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
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
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		
		$return = $this->_model->delete(base64_decode($params['rowid']),base64_decode($params['rowidtemp']));
		
		if ($return){			
			die('done');			
		}else{
			die("Data Tidak Berhasil Dihapus.");
		}
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_OPEX_VRA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_OPEX_VRA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_OPEX_VRA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
