<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Distribusi VRA - Non Infra
Function 			:	- listAction			: menampilkan list norma distribusi VRA - Non Infra
						- saveAction			: save data
						- deleteAction			: hapus data
						- listInfoVraAction		: SID 15/07/2014	: menampilkan list info VRA
						- updateInheritModule	: YIR 19/07/2014	: update inherit module
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	15/07/2014
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class NormaDistribusiVraNonInfraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaDistribusiVraNonInfra();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period=$sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-distribusi-vra-non-infra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT Distribusi VRA - Non Infra';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole;
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan data Norma Distribusi VRA - Non Infra
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//menampilkan table header Norma Distribusi VRA - Non Infra
    public function listAfdAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getListAfd($params);
        die(json_encode($data));
    }
	
	public function saveTempAction(){
		$params = $this->_request->getParams();
        $rows = array();
		$rowsAfd = array();

        foreach ($params['text00'] as $key => $val) {
			if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['TRX_CODE']        	= $params['text00'][$key]; // TRX_CODE
				$rows[$key]['ACTIVITY_CODE']   	= $params['text02'][$key]; // ACTIVITY_CODE
				$rows[$key]['DESCRIPTION']   	= $params['text03'][$key]; // DESCRIPTION
				$rows[$key]['VRA_CODE']   		= $params['text04'][$key]; // VRA_CODE
				$rows[$key]['BA_CODE']   		= $params['key_find']; // BA_CODE
				$rows[$key]['PERIOD_BUDGET']   	= $params['budgetperiod']; // PERIOD_BUDGET
				for($x=13;$x<63;$x++){
					if(trim($params['text'.$x.'_1'][$key])<>""){
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
				$this->_model->saveTemp($row,$rowsAfd[$key],$params['key_find']);
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
	
	//Hitung hanya yang berubah
	public function saveAction(){		
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $params = $this->_request->getParams();
        $rows = array();
		$rowsAfd = array();
			
        foreach ($params['text00'] as $key => $val) {
			if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['TRX_CODE']        	= $params['text00'][$key]; // TRX_CODE
				$rows[$key]['ACTIVITY_CODE']   	= $params['text02'][$key]; // ACTIVITY_CODE
				$rows[$key]['DESCRIPTION']   	= $params['text03'][$key]; // DESCRIPTION
				$rows[$key]['VRA_CODE']   		= $params['text04'][$key]; // VRA_CODE
				$rows[$key]['BA_CODE']   		= $params['key_find']; // BA_CODE
				$rows[$key]['PERIOD_BUDGET']   	= $params['budgetperiod']; // PERIOD_BUDGET
				for($x=13;$x<63;$x++){
					if(trim($params['text'.$x.'_1'][$key])<>""){
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
            //print_r ($rowsAfd);
        }
		
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
		
		/*
		//ketika dist VRA melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
		if (!empty($rows)) {
			foreach ($rows as $key => $row) {
				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
					$this->_global->insertLockTable($lastBa, 'DISTRIBUSI VRA NON INFRA');
				}
				
				$lastBa = $row['BA_CODE'];			
			}
			$this->_global->insertLockTable($lastBa, 'DISTRIBUSI VRA NON INFRA');
		}
		*/
		
		//1. SIMPAN INPUTAN USER UTK RKT DISTRIBUSI VRA NON INFRA 
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_01_SAVETEMP';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		//print_r ($rows); //die;
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$this->_model->saveTemp($row,$rowsAfd[$key],$params['key_find']);
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
		
		//2. HITUNG RKT DISTRIBUSI VRA NON INFRA
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_02_SAVE';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$params = $this->_request->getParams();
		$params['CHANGE'] = 'Y'; 
		$updated_row = $this->_db->fetchAll("{$this->_model->getChangedData($params)}");
		
		//HITUNG RKT DISTRIBUSI VRA NON INFRA 
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				$record1['filename'] = $filename;
				$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
				$this->_model->updateRecord($record1);
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
		
		//3. HITUNG SUMMARY DIST VRA
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_03_SUMDIST';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//HITUNG SUMMARY DIST VRA
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				$record1['filename'] = $filename;
				$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
				$this->_model->updateSummaryNormaDistribusiVra($record1);
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
		
		//4. HITUNG OPEX VRA
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_04_OPEXVRA';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//HITUNG OPEX VRA
		$params = $this->_request->getParams();
		$par['filename'] = $filename;
		$par['PERIOD_BUDGET'] = $params['budgetperiod'];
		$par['key_find'] = $params['key_find'];
		$this->_model->updateRktOpexVra($par);
		
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
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		//distinct activity & vra
		$uniq_row['ACTIVITY_CODE'] = array();
		$uniq_row['VRA_CODE'] = array();
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				if (in_array($record1['ACTIVITY_CODE'], $uniq_row['ACTIVITY_CODE']) == false) {
					array_push($uniq_row['ACTIVITY_CODE'], $record1['ACTIVITY_CODE']);
				}
				
				if (in_array($record1['VRA_CODE'], $uniq_row['VRA_CODE']) == false) {
					array_push($uniq_row['VRA_CODE'], $record1['VRA_CODE']);
				}
			}
		}
		$uniq_act_code =  implode("','", $uniq_row['ACTIVITY_CODE']);
		$uniq_vra_code =  implode("','", $uniq_row['VRA_CODE']);
		
		
		$idxInherit = 1;			
		//deklarasi var utk inherit module
		$record1['PERIOD_BUDGET'] 		= $params['budgetperiod']; // PERIOD_BUDGET
		$record1['key_find'] 			= $params['key_find']; // BA_CODE
		$record1['activity_code'] 		= $uniq_act_code; // ACTIVITY_CODE
		$record1['vra_code'] 			= $uniq_vra_code; // VRA_CODE
		$record1['uniq_code_file'] 		= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
		$record1['ROW_ID'] = ""; //reset rowid agar tidak jadi filter ketika select data
		//update inherit module
		$this->updateInheritModule($record1);
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		//hapus dari table lock ketika selesai melakukan perhitungan di dist VRA
		if (!empty($rows)) {
			foreach ($rows as $key => $row) {
				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
					$this->_global->deleteLockTable($lastBa, 'DISTRIBUSI VRA NON INFRA');
				}
				
				$lastBa = $row['BA_CODE'];			
			}
			$this->_global->deleteLockTable($lastBa, 'DISTRIBUSI VRA NON INFRA');
		}
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array())	{
		if (!empty($row)) {		
			$urutan = 0;
			// ************************************************ UPDATE RKT LC ************************************************
			$model = new Application_Model_RktLc();
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
			
			//1. SAVE COST ELEMENT RKT LC
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTLCCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTLCTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {					
					//hitung total cost
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
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
			// ************************************************ UPDATE RKT LC ************************************************
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
			$model = new Application_Model_RktManualNonInfra();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL NON INFRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATCE';
			
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1); 
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung total cost
					$record1['filename'] = $filename;
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
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA ***************************************************
			
			//aries 2015-05-29
			// ************************************************ UPDATE RKT RAWAT SISIP ************************************************
			$model = new Application_Model_RktManualSisip();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG ROTASI OTOMATIS
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_SISIP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					if($record1['ACTIVITY_CODE'] == '42700'){
						$model->saveRotation($record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_SISIP_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					if($record1['ACTIVITY_CODE'] == '42700'){
						$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_SISIP_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if($record1['ACTIVITY_CODE'] == '42700'){
						//hitung total cost
						$model->calTotalCost($record1);
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
			// ************************************************ UPDATE RKT RAWAT SISIP ************************************************
			
			
			// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL NON INFRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASICE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASITOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung total cost
					$record1['filename'] = $filename;
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
			// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
			$model = new Application_Model_RktManualNonInfraOpsi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. SAVE RKT MANUAL NON INFRA + OPSI
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATOPSICE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATOPSITOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung total cost
					$record1['filename'] = $filename;
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
			// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
			
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
			$model = new Application_Model_RktManualInfra();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL INFRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATINFRACE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1); 
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTRAWATINFRATOTAL';
			
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {				
					//hitung total cost
					$record1['filename'] = $filename;
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
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
			
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			$model = new Application_Model_RktTanamManual();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. SAVE RKT TANAM MANUAL
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMCE';
			
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMTOTAL';
			
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung total cost
					$record1['filename'] = $filename;
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
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			
			// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
			$model = new Application_Model_RktTanam();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. SAVE RKT TANAM OTOMATIS
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMOTOCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$record1['filename'] = $filename;
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMOTOTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					//hitung total cost
					$record1['filename'] = $filename;
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
			// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
			
			//jika aktivitas TABUR PUPUK, UNTIL PUPUK baru hitung RKT Pupuk
			if ( (strpos($row['activity_code'], '43750')  !== false ) || (strpos($row['activity_code'], '43760') !== false ) ){
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
				$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
				$rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
			
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPUPUKBIAYANORMAL';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;	
						$model->calCostElement('TRANSPORT', $record1);
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
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
				
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
				$model = new Application_Model_RktPupukDistribusiBiayaSisip();	
				$rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPUPUKBIAYASISIP';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;	
						$model->calCostElement('TRANSPORT', $record1);
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
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
				
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNDISTVRANONINFRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPUPUKBIAYA';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//save
				$model = new Application_Model_RktPupukDistribusiBiayaGabungan();	
				$row['filename'] = $filename;	
				$model->calculateAllItem($row);
			
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
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
			}
		}
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
		
		///////////////////////////// DELETE DATA /////////////////////////////
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_01_DELETE';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$par['trxcode'] = $params['trxcode'];
		$par['ba_code'] = $params['BA_CODE'];
		$par['activity_code'] = $params['ACTIVITY_CODE'];
		$par['vra_code'] = $params['VRA_CODE'];
		$par['period_budget'] = $params['PERIOD_BUDGET'];
		$par['filename'] = $filename;
		$this->_model->delete($par); //hapus data
		
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
		///////////////////////////// DELETE DATA /////////////////////////////
		
		///////////////////////////// UPDATE SUMMARY DIST VRA /////////////////////////////
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_02_UPDATE_SUMMARY';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$this->_model->updateSummaryNormaDistribusiVra($params);
		
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
		///////////////////////////// UPDATE SUMMARY DIST VRA /////////////////////////////
		
		///////////////////////////// UPDATE RKT OPEX VRA /////////////////////////////
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NDISTVRANONINFRA_03_UPDATE_OPEX_VRA';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$this->_model->updateRktOpexVra($params);
		
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
		///////////////////////////// UPDATE RKT OPEX VRA /////////////////////////////
		
		///////////////////////////// UPDATE INHERIT MODULE /////////////////////////////
		$idxInherit = 1;
		$params['activity_code'] 		= $params['ACTIVITY_CODE']; // ACTIVITY_CODE
		$params['vra_code'] 			= $params['VRA_CODE']; // VRA_CODE
		$params['uniq_code_file'] 		= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
		$this->updateInheritModule($params);
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//menampilkan list info VRA
    public function listInfoVraAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getListInfoVra($params);
        die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_VRA_DISTRIBUSI";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        //print_r ($params);
		$params['task_name'] = "TR_RKT_VRA_DISTRIBUSI";
		$data = $this->_global->chkEnhLockedSequence($params);
		$data = 1;
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_VRA_DISTRIBUSI";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
