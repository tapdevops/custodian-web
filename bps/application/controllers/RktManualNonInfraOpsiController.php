<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk RKT Manual - Non Infra + Opsi
Function 			:	- getStatusPeriodeAction		: BDJ 26/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 26/07/2013	: menampilkan list RKT Manual - Non Infra + Opsi
						- getActivityClassAction		: SID 26/07/2013	: menampilkan list activity class
						- getActivityOpsiAction			: SID 26/07/2013	: menampilkan opsi aktivitas
						- getRotationAction				: SID 26/07/2013	: get Rp/Rotasi SMS1 & SMS2
						- mappingAction					: SID 26/07/2013	: mapping textfield name terhadap field name di DB
						- saveAction					: SID 26/07/2013	: save data
						- saveTempAction				: SID 26/07/2013	: simpan data sementara sesuai input user
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	26/07/2013
Update Terakhir		:	25/06/2014
Revisi				:	
	SID 25/06/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction
	YUL 11/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class RktManualNonInfraOpsiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_RktManualNonInfraOpsi();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
    public function indexAction()
    {
        $this->_redirect('/rkt-manual-non-infra-opsi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT RAWAT + Opsi';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT Manual - Non Infra + Opsi
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
	
	//menampilkan list activity class
    public function getActivityClassAction()
    {		
		$params = $this->_request->getParams();
		$value = $this->_model->getActivityClass($params);
		
		die(json_encode($value));
    }

	//menampilkan opsi aktivitas
    public function getActivityOpsiAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityOpsi($params);
		
		die(json_encode($value));
    }
	
	//<!-- TIPE NORMA -->
	//menampilkan list tipe norma
    public function getTipeNormaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getTipeNorma($params);
		
		die(json_encode($value));
    }
	
	//get Rp/Rotasi SMS1 & SMS2
	public function getRotationAction()
    {		
        $params = $this->_request->getParams();
		$params['TIPE_RKT_MANUAL'] = 'NON_INFRA_OPSI';
		$value = $this->_model->getRotation($params);
		
		die(json_encode($value));
    }
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0){
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['TRX_RKT_CODE']        	= $params['trxcode'][$key]; // KODE TRANSAKSI
				$rows[$key]['PERIOD_BUDGET']   		= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['AFD_CODE']  			= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE'] 			= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['MATURITY_STAGE_SMS1']  = $params['text09'][$key]; // MATURITY_STAGE_SMS1
				$rows[$key]['MATURITY_STAGE_SMS2']  = $params['text10'][$key]; // MATURITY_STAGE_SMS2
				$rows[$key]['HA_PLANTED']  			= $params['text11'][$key]; // HA_PLANTED
				$rows[$key]['ACTIVITY_CODE']      	= $params['text14'][$key]; // ACTIVITY_CODE
				$rows[$key]['ACTIVITY_CLASS']  		= $params['text16'][$key]; // ACTIVITY_CLASS
				$rows[$key]['ROTASI_SMS1']  		= $params['text17'][$key]; // ROTASI_SMS1
				$rows[$key]['ROTASI_SMS2']  		= $params['text18'][$key]; // ROTASI_SMS2
				$rows[$key]['ATRIBUT']  			= $params['text19'][$key]; // ATRIBUT
				$rows[$key]['PLAN_JAN']      		= $params['text20'][$key]; // PLAN_JAN
				$rows[$key]['PLAN_FEB']      		= $params['text21'][$key]; // PLAN_FEB
				$rows[$key]['PLAN_MAR']      		= $params['text22'][$key]; // PLAN_MAR
				$rows[$key]['PLAN_APR']      		= $params['text23'][$key]; // PLAN_APR
				$rows[$key]['PLAN_MAY']      		= $params['text24'][$key]; // PLAN_MAY
				$rows[$key]['PLAN_JUN']      		= $params['text25'][$key]; // PLAN_JUN
				$rows[$key]['PLAN_JUL']      		= $params['text26'][$key]; // PLAN_JUL
				$rows[$key]['PLAN_AUG']      		= $params['text27'][$key]; // PLAN_AUG
				$rows[$key]['PLAN_SEP']      		= $params['text28'][$key]; // PLAN_SEP
				$rows[$key]['PLAN_OCT']      		= $params['text29'][$key]; // PLAN_OCT
				$rows[$key]['PLAN_NOV']      		= $params['text30'][$key]; // PLAN_NOV
				$rows[$key]['PLAN_DEC']      		= $params['text31'][$key]; // PLAN_DEC
				$rows[$key]['PLAN_SETAHUN']      	= $params['text32'][$key]; // PLAN_SETAHUN
				$rows[$key]['TOTAL_RP_SMS1']      	= $params['text33'][$key]; // TOTAL_RP_SMS1
				$rows[$key]['TOTAL_RP_SMS2']      	= $params['text34'][$key]; // TOTAL_RP_SMS2
				$rows[$key]['COST_JAN']      		= $params['text35'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      		= $params['text36'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      		= $params['text37'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      		= $params['text38'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      		= $params['text39'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      		= $params['text40'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      		= $params['text41'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      		= $params['text42'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      		= $params['text43'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      		= $params['text44'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      		= $params['text45'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      		= $params['text46'][$key]; // COST_DEC
				$rows[$key]['TOTAL_RP_SETAHUN']     = $params['text47'][$key]; // TOTAL_RP_SETAHUN
				$rows[$key]['TIPE_NORMA']     		= $params['text60'][$key]; // TIPE_NORMA //<!-- TIPE NORMA -->
				$rows[$key]['LAND_TYPE'] 			= $params['hidden00'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY'] 			= $params['hidden01'][$key]; // TOPOGRAPHY
				$rows[$key]['SUMBER_BIAYA']     	= $params['text49'][$key]; // SUMBER_BIAYA
				$rows[$key]['AWAL_ROTASI']     		= $params['text50'][$key]; // AWAL_ROTASI
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
		$filename = 'RawatOpsi01_'.$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			//if ($row['CHANGE']){
				$row['filename'] = $filename;
				$return = $this->_model->saveRotation($row);
			//}
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
		$filename = "RawatOpsi02_".$this->_global->genFileName();
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
		$filename = 'RawatOpsi03_'.$this->_global->genFileName();
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
	
	//simpan data sementara sesuai input user
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
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_RAWAT_OPSI";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_RAWAT_OPSI";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_RAWAT_OPSI";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
