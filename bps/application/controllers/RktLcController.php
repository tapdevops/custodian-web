<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk RKT LC
Function 			:	- getStatusPeriodeAction		: BDJ 22/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 22/07/2013	: menampilkan list RKT LC
						- getActivityClassAction		: YIR 22/07/2013	: drop down activity class
						- getLandTypeAction				: YIR 22/07/2013	: drop down land type
						- getActivityNameAction			: YIR 22/07/2013	: get activity name
						- getTopographyAction			: YIR 22/07/2013	: drop down topografi
						- getSumberBiayaAction			: YIR 22/07/2013	: drop down sumber biaya
						- mappingAction					: YIR 22/07/2013	: mapping textfield name terhadap field name di DB
						- saveTempAction				: YIR 22/07/2013	: simpan data sementara sesuai input user
						- saveAction					: YIR 22/07/2013	: save data
						- deleteAction					: YIR 22/07/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	24/06/2014
Revisi				:	
	SID 24/06/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction, deleteAction
	YUL 08/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction					
=========================================================================================================================
*/
class RktLcController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_RktLc();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-lc/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT LC';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT LC
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
	
	//drop down activity class
	public function getActivityClassAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityClass($params);
		
		die(json_encode($value));
    }
	
	//drop down land type
	public function getLandTypeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getLandType($params);
		
		die(json_encode($value));
    }
	
	//get activity name
	public function getActivityNameAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityName($params);
		
		die(json_encode($value));
    }
	
	//drop down topografi
	public function getTopographyAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getTopography($params);
		
		die(json_encode($value));
    }
	
	//drop down sumber biaya
	public function getSumberBiayaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getSumberBiaya($params);
		
		die(json_encode($value));
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
			if (($key > 0) && ($params['text03'][$key])){
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']      	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['TRX_RKT_CODE']        	= $params['trxrktcode'][$key]; // TRX RKT CODE
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				  $rows[$key]['OLD_BA_CODE']      	  = $params['text003'][$key]; // BA_CODE
				$rows[$key]['ACTIVITY_CODE']      	= $params['text04'][$key]; // ACTIVITY_CODE
				  $rows[$key]['OLD_ACTIVITY_CODE']    = $params['text004'][$key]; // ACTIVITY_CODE
				$rows[$key]['AFD_CODE']  			= $params['text06'][$key]; // AFD_CODE
				  $rows[$key]['OLD_AFD_CODE']  			= $params['text006'][$key]; // AFD_CODE
				$rows[$key]['LAND_TYPE'] 			= $params['text07'][$key]; // LAND_TYPE
				  $rows[$key]['OLD_LAND_TYPE'] 			= $params['text007'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY']        	= $params['text08'][$key]; // TOPOGRAPHY
				  $rows[$key]['OLD_TOPOGRAPHY']        	= $params['text008'][$key]; // TOPOGRAPHY
				$rows[$key]['SUMBER_BIAYA']        	= $params['text09'][$key]; // SUMBER_BIAYA
				  $rows[$key]['OLD_SUMBER_BIAYA']        	= $params['text009'][$key]; // SUMBER_BIAYA
				$rows[$key]['ACTIVITY_CLASS'] 		= $params['text10'][$key]; // ACTIVITY_CLASS
				  $rows[$key]['OLD_ACTIVITY_CLASS'] 	= $params['text010'][$key]; // ACTIVITY_CLASS
				$rows[$key]['PLAN_JAN']      		= $params['text11'][$key]; // PLAN_JAN
				$rows[$key]['PLAN_FEB']      		= $params['text12'][$key]; // PLAN_FEB
				$rows[$key]['PLAN_MAR']      		= $params['text13'][$key]; // PLAN_MAR
				$rows[$key]['PLAN_APR']      		= $params['text14'][$key]; // PLAN_APR
				$rows[$key]['PLAN_MAY']      		= $params['text15'][$key]; // PLAN_MAY
				$rows[$key]['PLAN_JUN']      		= $params['text16'][$key]; // PLAN_JUN
				$rows[$key]['PLAN_JUL']      		= $params['text17'][$key]; // PLAN_JUL
				$rows[$key]['PLAN_AUG']      		= $params['text18'][$key]; // PLAN_AUG
				$rows[$key]['PLAN_SEP']      		= $params['text19'][$key]; // PLAN_SEP
				$rows[$key]['PLAN_OCT']      		= $params['text20'][$key]; // PLAN_OCT
				$rows[$key]['PLAN_NOV']      		= $params['text21'][$key]; // PLAN_NOV
				$rows[$key]['PLAN_DEC']      		= $params['text22'][$key]; // PLAN_DEC
				$rows[$key]['PLAN_SETAHUN']      	= str_replace(',','',$params['text11'][$key])+
													  str_replace(',','',$params['text12'][$key])+
													  str_replace(',','',$params['text13'][$key])+
													  str_replace(',','',$params['text14'][$key])+
													  str_replace(',','',$params['text15'][$key])+
													  str_replace(',','',$params['text16'][$key])+
													  str_replace(',','',$params['text17'][$key])+
													  str_replace(',','',$params['text18'][$key])+
													  str_replace(',','',$params['text19'][$key])+
													  str_replace(',','',$params['text20'][$key])+
													  str_replace(',','',$params['text21'][$key])+
													  str_replace(',','',$params['text22'][$key]);
				
				//BIAYA
				$rows[$key]['TOTALRP']      		= $params['text24'][$key]; // TOTAL RP/QTY
				$rows[$key]['COST_JAN']      		= $params['text25'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      		= $params['text26'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      		= $params['text27'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      		= $params['text28'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      		= $params['text29'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      		= $params['text30'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      		= $params['text31'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      		= $params['text32'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      		= $params['text33'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      		= $params['text34'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      		= $params['text35'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      		= $params['text36'][$key]; // COST_DEC
				$rows[$key]['COST_SETAHUN']      	= $params['text37'][$key]; //COST SETAHUN
            }
        }
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
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
		$filename = 'LC01_'.$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
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
		$filename = 'LC02_'.$this->_global->genFileName();
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
		$filename = 'LC03_'.$this->_global->genFileName();
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
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
		
        //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
		$params['key_find'] = $params['BA_CODE'];
		$lock = $this->_global->checkLockTable($params);		
		if($lock['JUMLAH']){
			$data['return'] = "locked";
			$data['module'] = $lock['MODULE'];
			$data['insert_user'] = $lock['INSERT_USER'];
			die(json_encode($data));
		}
		
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
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
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_LC";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_LC";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_LC";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
	
	public function checkProyeksiAction(){	
		$params = $this->_request->getPost();
		$data = $this->_model->getProyeksi($params);
		die(json_encode($data));
	}
}