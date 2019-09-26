<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk RKT Rawat Tanam Manual
Function 			:	- listAction		: menampilkan list RKT Kg Sisip
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	22/07/2013
Revisi				:	
YUL 11/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class RktTanamManualController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_RktTanamManual();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-tanam-manual/main');
    }

		public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT Tanam Manual';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT KG SISIP
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
			//echo "data: ";print_r($params); die();
			die(json_encode($data));
		}
    }
	
	public function getActivityClassAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityClass($params);
		
		die(json_encode($value));
    }
	
	public function getLandTypeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getLandType($params);
		
		die(json_encode($value));
    }
	
	public function getTopographyAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getTopography($params);
		
		die(json_encode($value));
    }
	
	public function getSumberBiayaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getSumberBiaya($params);
		
		die(json_encode($value));
    }
	
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['text03'][$key])){
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['ROW_ID_TEMP']        	= $params['rowidtemp'][$key]; // ROW ID
				$rows[$key]['TRX_RKT_CODE']        	= $params['trxcode'][$key]; // TRX RKT CODE
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['PERIOD_BUDGET']      	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				
				$rows[$key]['AFD_CODE']      		= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE']  			= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['LAND_TYPE'] 			= $params['text06'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY']        	= $params['text07'][$key]; // TOPOGRAPHY
				$rows[$key]['THN_TANAM']        	= $params['text08'][$key]; // THN_TANAM --Aries 25-05-2015
				$rows[$key]['SUMBER_BIAYA']        	= $params['text14'][$key]; // SUMBER_BIAYA
				$rows[$key]['ACTIVITY_CLASS'] 		= $params['text15'][$key]; // ACTIVITY_CLASS
				$rows[$key]['ACTIVITY_CODE']      	= $params['src_activity_code']; 
				$rows[$key]['TIPE_NORMA']  			= $params['text55'][$key]; // TIPE_NORMA
				$rows[$key]['ROTASI_SMS1']  		= $params['text56'][$key]; // ROTASI_SMS1
				$rows[$key]['ROTASI_SMS2']  		= $params['text57'][$key]; // ROTASI_SMS2
				$rows[$key]['MATURITY_STAGE_SMS1']  = $params['text09'][$key];
				$rows[$key]['MATURITY_STAGE_SMS2']  = $params['text10'][$key];
				
				$rows[$key]['PLAN_JAN']      		= $params['text16'][$key]; // PLAN_JAN
				$rows[$key]['PLAN_FEB']      		= $params['text17'][$key]; // PLAN_FEB
				$rows[$key]['PLAN_MAR']      		= $params['text18'][$key]; // PLAN_MAR
				$rows[$key]['PLAN_APR']      		= $params['text19'][$key]; // PLAN_APR
				$rows[$key]['PLAN_MAY']      		= $params['text20'][$key]; // PLAN_MAY
				$rows[$key]['PLAN_JUN']      		= $params['text21'][$key]; // PLAN_JUN
				$rows[$key]['PLAN_JUL']      		= $params['text22'][$key]; // PLAN_JUL
				$rows[$key]['PLAN_AUG']      		= $params['text23'][$key]; // PLAN_AUG
				$rows[$key]['PLAN_SEP']      		= $params['text24'][$key]; // PLAN_SEP
				$rows[$key]['PLAN_OCT']      		= $params['text25'][$key]; // PLAN_OCT
				$rows[$key]['PLAN_NOV']      		= $params['text26'][$key]; // PLAN_NOV
				$rows[$key]['PLAN_DEC']      		= $params['text27'][$key]; // PLAN_DEC
				$rows[$key]['PLAN_SETAHUN']      	= str_replace(',','',$params['text16'][$key])+str_replace(',','',$params['text17'][$key])+str_replace(',','',$params['text18'][$key])+str_replace(',','',$params['text19'][$key])+str_replace(',','',$params['text20'][$key])+str_replace(',','',$params['text21'][$key])+str_replace(',','',$params['text22'][$key])+str_replace(',','',$params['text23'][$key])+str_replace(',','',$params['text24'][$key])+str_replace(',','',$params['text25'][$key])+str_replace(',','',$params['text26'][$key])+str_replace(',','',$params['text27'][$key]);
				
				$rows[$key]['TOTALRPQTY']      		= $params['text29'][$key]; // TOTAL
				$rows[$key]['COST_JAN']      		= $params['text30'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      		= $params['text31'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      		= $params['text32'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      		= $params['text33'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      		= $params['text34'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      		= $params['text35'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      		= $params['text36'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      		= $params['text37'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      		= $params['text38'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      		= $params['text39'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      		= $params['text40'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      		= $params['text41'][$key]; // COST_DEC
            }
        }
		return $rows;
	}
	
	//save data temp
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
       
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
       
		foreach ($rows as $key => $row) {
            if ($row['CHANGE']){
				$row['filename'] = $filename;
				$this->_model->saveRotation($row);
				$this->_model->saveTemp($row);
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
		
		die('no_alert');
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
			if ($row['ACTIVITY_CODE'] == '40300'){
				$row['ACTIVITY_CODE'] = '40100';
				$record= $this->_db->fetchAll("{$this->_model->getData($row)}");
				if (!empty($record)) {			
					foreach ($record as $idx => $rec) {
						$rec['filename'] = $filename;
						$rec['ACTIVITY_CODE'] = '40300';
						$rec['TRX_RKT_CODE'] = $row['TRX_RKT_CODE'];
						$return = $this->_model->saveRotation($rec);
					}
				}	
			}else{
				if ($row['CHANGE']){
					$row['filename'] = $filename;
					$return = $this->_model->saveRotation($row);
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
		
		//2. HITUNG PER COST ELEMENT
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung cost element labour
				$this->_model->calCostElement('LABOUR', $record1);
				//hitung cost element material
				$this->_model->calCostElement('MATERIAL', $record1);
				//hitung cost element tools
				$this->_model->calCostElement('TOOLS', $record1);
				//hitung cost element transport
				$this->_model->calCostElement('TRANSPORT', $record1);
				//hitung cost element contract
				$this->_model->calCostElement('CONTRACT', $record1);
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
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung total cost
				$this->_model->calTotalCost($record1);
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
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_RKT_TANAM_MANUAL";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_RKT_TANAM_MANUAL";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_RKT_TANAM_MANUAL";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
	
	//<!-- TIPE NORMA -->
	//menampilkan list tipe norma
    public function getTipeNormaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getTipeNorma($params);
		
		die(json_encode($value));
    }
}