<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk RKT Perkerasan Jalan
Function 			:	- listAction		: menampilkan list RKT Perkerasan Jalan
						- saveAction		: save data
						- deleteAction		: hapus data
						- saveTempAction	: YIR 25/06/2014	: simpan data sementara sesuai input user, *perlukah krn dia ngga bisa input?
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	29/07/2013
Update Terakhir		:	29/07/2013
Revisi				:	
YULIUS 21/07/2014	: 	- penambahan pengecekan untuk lock table pada listAction, saveAction
YUL 08/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class RktTanamController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_RktTanam();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-tanam/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT TANAM';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT Tanam
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
	
	//mengambil nilai urgency Tanam
    public function getSumberBiayaAction()
    {		
        $params = $this->_request->getPost();
        $data = $this->_model->getSumberBiaya($params);
        die(json_encode($data));
    }

	public function getActivityClassAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityClass($params);
		
		die(json_encode($value));
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['text03'][$key])){
				$rows[$key]['TRX_RKT_CODE']			= $params['trxcode'][$key]; // TRX_RKT_CODE
				$rows[$key]['ACTIVITY_CODE']		= $params['src_coa_code']; // ACTIVITY_CODE
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['ROW_ID_TEMP']        	= $params['rowidtemp'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']      	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['AFD_CODE']      		= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE']      		= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['TAHUN_TANAM_M']  		= $params['bulan'][$key]; // BULAN_TANAM
				$rows[$key]['TAHUN_TANAM_Y']  		= $params['tahun'][$key]; // TAHUN_TANAM
				$rows[$key]['TAHUN_TANAM']  		= $params['text08'][$key]; // TAHUN_TANAM
				$rows[$key]['SEMESTER1']  			= $params['text09'][$key]; // SEMESTER1
				$rows[$key]['SEMESTER2']  			= $params['text10'][$key]; // SEMESTER2
				$rows[$key]['HA_PLANTED']  			= $params['text11'][$key]; // HA_PLANTED
				$rows[$key]['SUMBER_BIAYA'] 		= $params['text14'][$key]; // SUMBER_BIAYA
				$rows[$key]['ACTIVITY_CLASS']      = $params['text15'][$key]; // JENIS_PEKERJAAN
				$rows[$key]['TIPE_NORMA']  			= $params['text61'][$key]; // TIPE_NORMA
				$rows[$key]['ROTASI_SMS1']  		= $params['text17'][$key]; // ROTASI_SMS1
				$rows[$key]['ROTASI_SMS2']  		= $params['text18'][$key]; // ROTASI_SMS2
				$rows[$key]['JARAK'] 				= $params['text16'][$key]; // PRICE
				$rows[$key]['AKTUAL_JALAN']      	= $params['text17'][$key]; // QTY_ACTUAL
				$rows[$key]['AKTUAL_PERKERASAN_JALAN'] = $params['text18'][$key]; // QTY_ACTUAL
				$rows[$key]['PLAN_JAN']      		= $params['text19'][$key]; // DIS_JAN
				$rows[$key]['PLAN_FEB']      		= $params['text20'][$key]; // DIS_FEB
				$rows[$key]['PLAN_MAR']      		= $params['text21'][$key]; // DIS_MAR
				$rows[$key]['PLAN_APR']      		= $params['text22'][$key]; // DIS_APR
				$rows[$key]['PLAN_MAY']      		= $params['text23'][$key]; // DIS_MAY
				$rows[$key]['PLAN_JUN']      		= $params['text24'][$key]; // DIS_JUN
				$rows[$key]['PLAN_JUL']      		= $params['text25'][$key]; // DIS_JUL
				$rows[$key]['PLAN_AUG']      		= $params['text26'][$key]; // DIS_AUG
				$rows[$key]['PLAN_SEP']      		= $params['text27'][$key]; // DIS_SEP
				$rows[$key]['PLAN_OCT']      		= $params['text28'][$key]; // DIS_OCT
				$rows[$key]['PLAN_NOV']      		= $params['text29'][$key]; // DIS_NOV
				$rows[$key]['PLAN_DEC']      		= $params['text30'][$key]; // DIS_DEC
				$rows[$key]['PLAN_SETAHUN']      	= $params['text31'][$key]; // TOTAL_BIAYA
				$rows[$key]['PRICE_QTY']      		= $params['text32'][$key]; // PRICE_QTY
				$rows[$key]['COST_JAN']      		= $params['text33'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      		= $params['text34'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      		= $params['text35'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      		= $params['text36'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      		= $params['text37'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      		= $params['text38'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      		= $params['text39'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      		= $params['text40'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      		= $params['text41'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      		= $params['text42'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      		= $params['text43'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      		= $params['text44'][$key]; // COST_DEC
				$rows[$key]['TOTAL_RP_SETAHUN']      	= $params['text45'][$key]; // COST_SETAHUN
				$rows[$key]['LAND_TYPE']			= $params['text06'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY']			= $params['text07'][$key]; // TOPOGRAPHY
				$rows[$key]['MATURITY_STAGE_SMS1']			= $params['text46'][$key]; // MATURITY_STAGE_SMS1
				$rows[$key]['MATURITY_STAGE_SMS2']			= $params['text47'][$key]; // MATURITY_STAGE_SMS2
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
		$params['task_name'] = "TR_RKT_TANAM";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_TANAM";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_TANAM";
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