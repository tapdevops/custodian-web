<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Perencanaan Produksi
Function 			:	- getStatusPeriodeAction		: BDJ 23/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 04/07/2013	: menampilkan list perencanaan produksi
						- mappingAction					: SID 07/07/2014	: mapping textfield name terhadap field name di DB
						- saveAction					: SID 04/07/2013	: save data
						- updateInheritModule			: SID 07/07/2014	: update inherit module
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/07/2013
Update Terakhir		:	07/07/2014
Revisi				:	
	SID 07/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction
						- saveAction menghitung seluruh data berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction
	YUL 08/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class PerencanaanProduksiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_PerencanaanProduksi();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/perencanaan-produksi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 1 &raquo; Perencanaan Produksi';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list perencanaan produksi
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
            if (($key > 0)) {
				$rows[$key]['CHANGE']        			= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        			= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']        	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']     				= $params['text03'][$key]; // BA_CODE
				$rows[$key]['AFD_CODE']    				= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE']     			= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['JARAK_PKS']     			= $params['text52'][$key]; // JARAK_PKS
				$rows[$key]['PERSEN_LANGSIR']     		= $params['text53'][$key]; // PERSEN_LANGSIR
				$rows[$key]['HA_PANEN']        			= $params['text06'][$key]; // HA_PANEN
				$rows[$key]['POKOK_PRODUKTIF']  		= $params['text07'][$key]; // POKOK_PRODUKTIF
				$rows[$key]['SPH_PRODUKTIF']    		= $params['text08'][$key]; // SPH_PRODUKTIF
				$rows[$key]['TON_AKTUAL']     			= $params['text09'][$key]; // TON_AKTUAL
				$rows[$key]['JANJANG_AKTUAL']   		= $params['text10'][$key]; // JANJANG_AKTUAL
				$rows[$key]['BJR_AKTUAL']     			= $params['text11'][$key]; // BJR_AKTUAL
				$rows[$key]['YPH_AKTUAL']    			= $params['text12'][$key]; // YPH_AKTUAL
				$rows[$key]['TON_TAKSASI']     			= $params['text13'][$key]; // TON_TAKSASI
				$rows[$key]['JANJANG_TAKSASI']  		= $params['text14'][$key]; // JANJANG_TAKSASI
				$rows[$key]['BJR_TAKSASI']     			= $params['text15'][$key]; // BJR_TAKSASI
				$rows[$key]['YPH_TAKSASI']    			= $params['text16'][$key]; // YPH_TAKSASI
				$rows[$key]['TON_ANTISIPASI']   		= $params['text17'][$key]; // TON_ANTISIPASI
				$rows[$key]['JANJANG_ANTISIPASI']       = $params['text18'][$key]; // JANJANG_ANTISIPASI
				$rows[$key]['BJR_ANTISIPASI']     		= $params['text19'][$key]; // BJR_ANTISIPASI
				$rows[$key]['YPH_ANTISIPASI']    		= $params['text20'][$key]; // YPH_ANTISIPASI
				$rows[$key]['TON_BUDGET_TAHUN_BERJALAN']= $params['text21'][$key]; // TON_BUDGET_TAHUN_BERJALAN
				$rows[$key]['YPH_BUDGET_TAHUN_BERJALAN']= $params['text22'][$key]; // YPH_BUDGET_TAHUN_BERJALAN
				$rows[$key]['VAR_YPH']     				= $params['text23'][$key]; // VAR_YPH
				$rows[$key]['HA_SMS1']    				= $params['text24'][$key]; // HA_SMS1
				$rows[$key]['POKOK_SMS1']     			= $params['text25'][$key]; // POKOK_SMS1
				$rows[$key]['SPH_SMS1']        			= $params['text26'][$key]; // ROW SPH_SMS1
				$rows[$key]['HA_SMS2']     				= $params['text27'][$key]; // HA_SMS2
				$rows[$key]['POKOK_SMS2']    			= $params['text28'][$key]; // POKOK_SMS2
				$rows[$key]['SPH_SMS2']     			= $params['text29'][$key]; // SPH_SMS2
				$rows[$key]['YPH_PROFILE']        		= $params['text30'][$key]; // YPH_PROFILE
				$rows[$key]['TON_PROFILE']     			= $params['text31'][$key]; // TON_PROFILE
				$rows[$key]['YPH_PROPORTION']        	= $params['text32'][$key]; // YPH_PROPORTION
				$rows[$key]['TON_PROPORTION']     		= $params['text33'][$key]; // TON_PROPORTION
				$rows[$key]['JANJANG_BUDGET']    		= $params['text34'][$key]; // JANJANG_BUDGET
				$rows[$key]['BJR_BUDGET']     			= $params['text35'][$key]; // BJR_BUDGET
				$rows[$key]['TON_BUDGET']        		= $params['text36'][$key]; // TON_BUDGET
				$rows[$key]['YPH_BUDGET']     			= $params['text37'][$key]; // YPH_BUDGET
				$rows[$key]['JAN']    					= $params['text38'][$key]; // JAN
				$rows[$key]['FEB']     					= $params['text39'][$key]; // FEB
				$rows[$key]['MAR']        				= $params['text40'][$key]; // MAR
				$rows[$key]['APR']     					= $params['text41'][$key]; // APR
				$rows[$key]['MAY']        				= $params['text42'][$key]; // MAY
				$rows[$key]['JUN']     					= $params['text43'][$key]; // JUN
				$rows[$key]['JUL']    					= $params['text44'][$key]; // JUL
				$rows[$key]['AUG']     					= $params['text45'][$key]; // AUG
				$rows[$key]['SEP']        				= $params['text46'][$key]; // SEP
				$rows[$key]['OCT']     					= $params['text47'][$key]; // OCT
				$rows[$key]['NOV']    					= $params['text48'][$key]; // NOV
				$rows[$key]['DEC']     					= $params['text49'][$key]; // DEC
				$rows[$key]['SMS1']        				= $params['text50'][$key]; // SMS1
				$rows[$key]['SMS2']     				= $params['text51'][$key]; // SMS2
				
				$rows[$key]['key_find'] 		= $params['text03'][$key];
            }
        }
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE PERENCANAAN PRODUKSI TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName()."TEMP_PP";
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
			// ************************************************ SAVE PERENCANAAN PRODUKSI TEMP ************************************************
		
		}
		
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
		
		// ************************************************ SAVE PERENCANAAN PRODUKSI TEMP ************************************************
			$model = new Application_Model_RktPanen();
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_NINFRA_01_SAVETEMP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma infra temp
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
			// ************************************************ SAVE PERENCANAAN PRODUKSI TEMP ************************************************
		
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$j = 0;
		$seb_prod = array();
		
		//save plan untuk hitung sebaran produksi
		foreach ($rows as $key => $row) {
			//if ($row['CHANGE']){
				//print_r($row);die();
				$row['filename'] = $filename;
				$return = $this->_model->save($row);
				$model->saveProduksi($row);
				//ditampung untuk inherit module
				$updated_row[] = $row;
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
		// ************************************************ END SAVE PERENCANAAN PRODUKSI TEMP ***********************************
		
		// ************************************************ SAVE SEBARAN PRODUKSI ************************************************
		$model1 = new Application_Model_RktPanen();
			
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName()."SEBARANPRODUKSI";
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		if (!empty($updated_row)) {
			$updated_row['filename'] = $filename;
			$model1->saveSebaran($updated_row);
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
		// ********************************************** END SAVE SEBARAN PRODUKSI **********************************************
		
		
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				//update inherit module
				$this->updateInheritModule($record1);
			}
		}
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		// ************************************************ UPDATE RKT PANEN ************************************************
		$model = new Application_Model_RktPanen();
		
		//set parameter sesuai data yang diupdate
		$row['key_find'] = $row['BA_CODE'];
		$row['src_afd'] = $row['AFD_CODE'];
		$row['src_block'] = $row['BLOCK_CODE'];
		
		$rec = $this->_db->fetchAll("{$model->getData($row)}");
		
		//1. HITUNG PER COST ELEMENT
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName()."PER_COST_ELEMENT";
		$this->_global->createBashFile($filename); //create bash file			
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		if (!empty($rec)) {
			foreach ($rec as $idx1 => $record1) {
				$record1['filename'] = $filename;
		
			//hitung cost element
				$model->calCostElement('LABOUR', $record1);
				$model->calCostElement('TRANSPORT', $record1);
				$model->calCostElement('TOOLS', $record1);
				$model->calCostElement('CONTRACT', $record1);
				$model->calCostElement('MATERIAL', $record1);
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
		
		//2. HITUNG TOTAL COST
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName().'TOTAL_COST_RKT_PANEN';
		$this->_global->createBashFile($filename); //create bash file			
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
		if (!empty($rec)) {
			foreach ($rec as $idx1 => $record1) {
				$record1['filename'] = $filename;
				
				//hitung total cost
				$model->calTotalCost($record1);
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
		// ************************************************ UPDATE RKT PANEN ************************************************	
	}
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_PRODUKSI_PERIODE_BUDGET";
		$update = $this->_model->finalizeRktPanenLangsir($params);
		print_r($update);
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_PRODUKSI_PERIODE_BUDGET";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_PRODUKSI_PERIODE_BUDGET";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
