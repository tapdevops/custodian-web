<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Norma SPH
Function 			:	- getStatusPeriodeAction		: BDJ 24/07/2014	: cek status periode budget yang dipilih
						- listAction					: SID 24/07/2014	: menampilkan list norma VRA
						- mappingAction					: SID 24/07/2014	: mapping textfield name terhadap field name di DB
						- saveTempActionAction			: SID 24/07/2014	: save data temp
						- saveAction					: SID 24/07/2014	: save data
						- updateInheritModule			: SID 24/07/2014	: update inherit module
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	24/07/2014
Update Terakhir		:	24/07/2014
Revisi				:	
YUL 08/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class NormaSphController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaSph();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-sph/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma SPH';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole;
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma VRA
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost(); 
        
		$data = $this->_model->getList($params);
		die(json_encode($data));
    }
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
           if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['CORE']			   	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['LAND_TYPE']     	= $params['text03'][$key]; // REGION_CODE
				$rows[$key]['TOPOGRAPHY']      	= $params['text04'][$key]; // VRA_CODE
				$rows[$key]['SPH_STANDAR']    	= $params['text05'][$key]; // RP_QTY
            }
        }
		return $rows;
	}
	
	//save data temp
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		$params = $this->_request->getParams();
		if (!empty($rows)) {
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save inputan user
			foreach ($rows as $key => $row) {
				$row['filename'] = $filename;
				$this->_model->saveTemp($row);
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
		
		die('no_alert');
    }
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
		if (!empty($rows)) {		
			//ketika NORMA VRA melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
			/*foreach ($rows as $key => $row) {
				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
					$this->_global->insertLockTable($lastBa, 'NORMA VRA (PINJAM)');
				}
				
				$lastBa = $row['BA_CODE'];			
			}
			$this->_global->insertLockTable($lastBa, 'NORMA VRA (PINJAM)');
			*/
			// ************************************************ SAVE NORMA VRA TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_NSPH_01_SAVETEMP';
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma biaya temp
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
			// ************************************************ SAVE NORMA VRA TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA VRA ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NSPH_02_SAVE';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		if (!empty($records1)) {	
			foreach ($records1 as $idx1 => $record1) {
				if($record1['FLAG_TEMP'] == 'Y'){
					$record1['filename'] = $filename;
					$this->_model->save($record1);
					
					//ditampung untuk inherit module
					$updated_row[] = $record1;
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
		// ************************************************ SAVE ALL NORMA VRA ************************************************
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		//distinct VRA CODE & BA CODE
		$uniq_row['CORE'] = array();
		$uniq_row['TOPOGRAPHY'] = array();
		$uniq_row['LAND_TYPE'] = array();
		
/*		//cari BA yg diupdate
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				$sql = "
					SELECT BA_CODE
					FROM TM_ORGANIZATION
					WHERE DELETE_USER IS NULL
						AND REGION_CODE = '".addslashes($record1['REGION_CODE'])."'
				";					
				$rows1 = $this->_db->fetchAll($sql);
				foreach ($rows1 as $id1 => $row1) {
					if (in_array($row1['BA_CODE'], $uniq_row['BA_CODE']) == false) {
						array_push($uniq_row['BA_CODE'], $row1['BA_CODE']);
					}
				}
			}
		}		
*/		
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				array_push($uniq_row['CORE'], $record1['CORE']);
				array_push($uniq_row['TOPOGRAPHY'], $record1['TOPOGRAPHY']);
				array_push($uniq_row['LAND_TYPE'], $record1['LAND_TYPE']);
			}
		}	
		
		$idxInherit = 1;			
		//deklarasi var utk inherit module
		$uniq_row['uniq_code_file'] = $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename		
		
		//update inherit module
		$this->updateInheritModule($uniq_row);
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		
		//hapus dari table lock ketika selesai melakukan perhitungan di norma VRA
//		if (!empty($rows)) {
//			foreach ($rows as $key => $row) {
//				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
//					$this->_global->deleteLockTable($lastBa, 'NORMA VRA (PINJAM)');
//				}
//				
//				$lastBa = $row['BA_CODE'];			
//			}
//			$this->_global->deleteLockTable($lastBa, 'NORMA VRA (PINJAM)');
//		}
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		// ************************************************ UPDATE RKT MANUAL SISIP ************************************************
		$model = new Application_Model_RktManualSisip();
		
		for ($x = 0; $x <= count($row['CORE']); $x++) {
			if ($row['CORE'][$x] != ""){
				if ($row['CORE'][$x] == 'INTI'){
					$par['CORE'] .= 2;
				}else{
					$par['CORE'] .= 3;
				}
				$par['TOPOGRAPHY'] = $row['TOPOGRAPHY'][$x];
				$par['LAND_TYPE'] =  $row['LAND_TYPE'][$x];
				$par['ACT_CODE'] = '42700';
				
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM MANUAL
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NSPH_01_RKTMANUALSISIP';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						//hitung cost element labour
						$model->calCostElement('LABOUR', $record1);
						//hitung cost element material
						$model->calCostElement('MATERIAL', $record1);
						//hitung cost element tools
						$model->calCostElement('TOOLS', $record1);
						//hitung cost element transport
						$model->calCostElement('TRANSPORT', $record1);
						//hitung cost element contract
						$model->calCostElement('CONTRACT', $record1);
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
				$filename = $row['uniq_code_file'].'_NSPH_17_RKTMANUALSISIPTOTAL';
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
				
				
			}
		}

				
						
				
				
				// ************************************************ UPDATE RKT MANUAL SISIP ************************************************
	
	
	/*
		$check_update_rkt_panen = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Panen
		$check_update_rkt_perkerasan_jalan = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Perkerasan Jalan
		$check_update_rkt_rawat = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Rawat
		
		if (!empty($row)) {	
			//implode
			$par['VRA_CODE'] = (count($row['VRA_CODE']) > 1) ? implode("','", $row['VRA_CODE']) : $row['VRA_CODE'][0];
			$par['key_find'] = (count($row['BA_CODE']) > 1) ? implode("','", $row['BA_CODE']) : $row['BA_CODE'][0];
			$tmp['ACT_CODE'] = array();
			
			
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************			
			$model = new Application_Model_NormaDistribusiVraNonInfra();
			$updated_row = $this->_db->fetchAll("{$model->getChangedData($par)}");
			
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_01_DISTVRANONINFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($updated_row)) {
				$check_update_rkt_rawat = 1;
				
				foreach ($updated_row as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					$model->updateRecord($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
						array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
			
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
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
			
			// ************************************************ UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_02_SUMDISTVRANONINFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($updated_row)) {
				foreach ($updated_row as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					$model->updateSummaryNormaDistribusiVra($record1);
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
			// ************************************************ UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA ************************************************
			
			// ************************************************ UPDATE OPEX VRA ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_03_OPEXVRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($row['BA_CODE'])) {
				foreach ($row['BA_CODE'] as $idx2 => $record2) {
					$par_opex_vra['filename'] = $filename;
					$par_opex_vra['key_find'] = $record2;
					$model->updateRktOpexVra($par_opex_vra);
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
			// ************************************************ UPDATE OPEX VRA ************************************************
			
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			$check = strpos($par['VRA_CODE'], 'ZZ_DT010'); //kalo vra nya DUMP TRUCK, baru update cost unit
			if ($check !== false) {	
				$check_update_rkt_panen = 1;
				
				$model = new Application_Model_NormaPanenCostUnit();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_04_NPANENCOSTUNIT';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->save($record1);
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
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			$model = new Application_Model_NormaPanenPremiLangsir();
			$records1 = $this->_db->fetchAll("{$model->getData($par)}");
		
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_05_NPANENPREMILANGSIR';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				$check_update_rkt_panen = 1;
				
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->save($record1);
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
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			
			// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
			$model = new Application_Model_NormaInfrastruktur();
			$records1 = $this->_db->fetchAll("{$model->getData($par)}");
		
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_06_NINFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->save($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
						array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
			
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
			// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
			
			// ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
			$check = strpos($par['VRA_CODE'], 'ZZ_EX010'); //kalo vra nya EXCAV, baru update
			if (!$check) $check = strpos($par['VRA_CODE'], 'ZZ_VC010'); //kalo vra nya COMPACTOR, baru update
			if (!$check)  $check = strpos($par['VRA_CODE'], 'ZZ_GD010'); //kalo vra nya GRADER, baru update
			
			if ($check !== false) {	
				$check_update_rkt_perkerasan_jalan = 1;
				
				$model = new Application_Model_NormaPerkerasanJalan();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_07_NPERKERASANJALAN';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->save($record1);
						
						//distinct activity
						if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
							array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
						}
					}
				}
				$par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
				
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
			// ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
			
			// ************************************************ UPDATE RKT LC ************************************************
			$model = new Application_Model_RktLc();
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($par)}");
			
			//1. SAVE COST ELEMENT RKT LC
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NVRA_08_RKTLCCE';
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
			$filename = $row['uniq_code_file'].'_NVRA_09_RKTLCTOTAL';
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
			
			if ($check_update_rkt_rawat == 1){
				// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
				$model = new Application_Model_RktManualNonInfra();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");	
				
				//1. SAVE RKT MANUAL NON INFRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_10_RKTRAWATCE';
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
				$filename = $row['uniq_code_file'].'_NVRA_11_RKTRAWATTOTAL';
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
				// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
				
				// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
				$model = new Application_Model_RktKastrasiSanitasi();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
				
				//1. SAVE RKT MANUAL NON INFRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_11A_RKTKASTRASICE';
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
				$filename = $row['uniq_code_file'].'_NVRA_11A_RKTKASTRASITOTAL';
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
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT MANUAL NON INFRA + OPSI
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_12_RKTRAWATOPSICE';
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
				$filename = $row['uniq_code_file'].'_NVRA_13_RKTRAWATOPSITOTAL';
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
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");	
				
				//1. SAVE RKT MANUAL INFRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_14_RKTRAWATINFRACE';
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
				$filename = $row['uniq_code_file'].'_NVRA_15_RKTRAWATINFRATOTAL';
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
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM MANUAL
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_16_RKTTANAMCE';
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
				$filename = $row['uniq_code_file'].'_NVRA_17_RKTTANAMTOTAL';
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
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM OTOMATIS
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_18_RKTTANAMOTOCE';
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
				$filename = $row['uniq_code_file'].'_NVRA_19_RKTTANAMOTOTOTAL';
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
			}
			
			
			// ************************************************ UPDATE RKT PANEN ************************************************			
			if ($check_update_rkt_panen == 1){
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM OTOMATIS
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_20_RKTPANENCE';
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
				$filename = $row['uniq_code_file'].'_NVRA_21_RKTPANENTOTAL';
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
			}
			// ************************************************ UPDATE RKT PANEN ************************************************
			
			// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************			
			if ($check_update_rkt_perkerasan_jalan == 1){
				$model = new Application_Model_RktPerkerasanJalan();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM OTOMATIS
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_22_RKTPERKJALANCE';
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
				$filename = $row['uniq_code_file'].'_NVRA_23_RKTPERKJALANTOTAL';
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
			}
			// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
			
			//jika aktivitas TABUR PUPUK, UNTIL PUPUK baru hitung RKT Pupuk
			if ( (strpos($par['ACT_CODE'], '43750')  !== false ) || (strpos($par['ACT_CODE'], '43760') !== false ) ){
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
				$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
				$rec = $this->_db->fetchAll("{$model->getInheritData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_24_RKTPUPUKBIAYANORMAL';
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
				$rec = $this->_db->fetchAll("{$model->getInheritData($par)}");
				
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NVRA_25_RKTPUPUKBIAYASISIP';
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
				$filename = $row['uniq_code_file'].'_NVRA_26_RKTPUPUKBIAYA';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//save
				$model = new Application_Model_RktPupukDistribusiBiayaGabungan();	
				$par['filename'] = $filename;	
				$par['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
				$model->calculateAllItem($par);
			
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
		*/
	}
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_SISIP";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_SISIP";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_SISIP";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
