<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Checkroll
Function 			:	- listAction		: menampilkan list norma Checkroll
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	13/06/2013
Update Terakhir		:	13/06/2013
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class NormaCheckrollController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaCheckroll();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-checkroll/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT Checkroll (MPP)';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma Checkroll
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
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']   	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']     		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['JOB_CODE']			= $params['text04'][$key]; // JOB_CODE
				$rows[$key]['EMPLOYEE_STATUS'] 	= $params['text07'][$key]; // EMPLOYEE_STATUS
				$rows[$key]['GP']   			= $params['text08'][$key]; // GP
				$rows[$key]['MPP_AKTUAL']   	= $params['text09'][$key]; // MPP_AKTUAL
				$rows[$key]['MPP_PERIOD_BUDGET']= $params['text10'][$key]; // MPP_PERIOD_BUDGET
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
            }
        }
		
		
		//cek norma pendukung
		if (!empty($rows)) {
			foreach ($rows as $key => $row) {
				//cek tarif tunjangan
				$sql = "
					SELECT TT.TUNJANGAN_TYPE, NVL(TT.VALUE,0) as NILAI
					FROM TM_TARIF_TUNJANGAN TT LEFT JOIN TM_TUNJANGAN TJ ON TT.TUNJANGAN_TYPE = TJ.TUNJANGAN_TYPE 
					WHERE TT.PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
						AND TT.BA_CODE like '".addslashes($row['BA_CODE'])."'
						AND TT.JOB_CODE like '".addslashes($row['JOB_CODE'])."'
						AND TT.EMPLOYEE_STATUS like '".addslashes($row['EMPLOYEE_STATUS'])."'
						AND TJ.FLAG_EMPLOYEE_STATUS  like '".addslashes($row['EMPLOYEE_STATUS'])."'
						";
				$rows1 = $this->_db->fetchAll($sql);
				if (empty($rows1)) {
					$data['return'] = 'Tarif tunjangan untuk Pekerja '.$row['JOB_CODE'].' - '.$row['EMPLOYEE_STATUS'] .' tidak tersedia';
					die(json_encode($data));
				}			
				foreach ($rows1 as $idx1 => $row1) {
					if ($row1['NILAI'] == NULL ) {
						$data['return'] = 'Lengkapi Terlebih Dahulu Tarif Tunjangan  '.$row1['TUNJANGAN_TYPE'].' untuk Pekerja '.$row['JOB_CODE'].' - '.$row['EMPLOYEE_STATUS'];
						die(json_encode($data));
					}
				}
				
				//cek catu
				$sql = "
					SELECT CATU_BERAS_SUM
					FROM TM_CATU_SUM
					WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
						AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
				";		
				$TARIF_CATU = $this->_db->fetchOne($sql);
				if($TARIF_CATU == '') {
					$data['return'] = 'Tarif Catu Tidak Ditemukan.';
					die(json_encode($data));
				}
				
				//cek HKE
				$sql = "
					SELECT HKE
					FROM TM_CHECKROLL_HK
					WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
						AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
						AND EMPLOYEE_STATUS = '".addslashes($row['EMPLOYEE_STATUS'])."'
				";		
				$hke = $this->_db->fetchOne($sql);
				
				if(!$hke) { 
					$data['return'] = "HKE untuk BA ".addslashes($params['BA_CODE'])." - ".$params['EMPLOYEE_STATUS']." kosong, Harap proses HKE terlebih dahulu"; 
					die(json_encode($data));
				}
			}
		}
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		$params = $this->_request->getParams();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA CHECKROLL TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
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
			// ************************************************ SAVE NORMA CHECKROLL TEMP ************************************************
		
		}
		
		die('no_alert');
    }
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		$params = $this->_request->getParams();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
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
			
			//ketika checkroll melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
			foreach ($rows as $key => $row) {
				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
					$this->_global->insertLockTable($row['BA_CODE'], 'RKT CHECKROLL');
				}
				
				$lastBa = $row['BA_CODE'];			
			}
			$this->_global->insertLockTable($lastBa, 'RKT CHECKROLL');
			
			// ************************************************ SAVE TEMP NORMA CHECKROLL ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_NCHECKROLL_01_SAVETEMP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
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
			// ************************************************ SAVE TEMP NORMA CHECKROLL ************************************************
		}
		
		// ************************************************ SAVE NORMA CHECKROLL ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NCHECKROLL_02_SAVE';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
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
		// ************************************************ SAVE NORMA CHECKROLL ************************************************
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		//distinct JOB CODE & BA CODE
		$uniq_row['UPDATED_ROW'] = $updated_row;
		$uniq_row['JOB_CODE'] = array();
		$uniq_row['BA_CODE'] = array();
		$uniq_row['PERIOD_BUDGET'] = $params['budgetperiod'];
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				if (in_array($record1['JOB_CODE'], $uniq_row['JOB_CODE']) == false) {
					array_push($uniq_row['JOB_CODE'], $record1['JOB_CODE']);
				}
				
				if (in_array($record1['BA_CODE'], $uniq_row['BA_CODE']) == false) {
					array_push($uniq_row['BA_CODE'], $record1['BA_CODE']);
				}
			}
		}	
		
		$idxInherit = 1;			
		//deklarasi var utk inherit module
		$uniq_row['uniq_code_file'] 	= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename		
		
		//update inherit module
		$this->updateInheritModule($uniq_row);
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		//hapus dari table lock ketika selesai melakukan perhitungan di norma CHECKROLL
		if (!empty($rows)) {
			foreach ($rows as $key => $row) {
				if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
					$this->_global->deleteLockTable($row['BA_CODE'], 'RKT CHECKROLL');
				}
				
				$lastBa = $row['BA_CODE'];			
			}
			$this->_global->deleteLockTable($lastBa, 'RKT CHECKROLL');
		}
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		$check_update_rkt_panen = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Panen
		$check_update_rkt_perkerasan_jalan = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Perkerasan Jalan
		$update_norma_wra = 0;
		$update_norma_vra = 0;
		$update_norma_biaya = 0;
		$update_norma_infra = 0;
		
		if (!empty($row)) {
			//implode
			$par['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			$par['JOB_CODE'] = (count($row['JOB_CODE']) > 1) ? implode("','", $row['JOB_CODE']) : $row['JOB_CODE'][0];
			$par['key_find'] = (count($row['BA_CODE']) > 1) ? implode("','", $row['BA_CODE']) : $row['BA_CODE'][0];
			$tmp['VRA_CODE'] = array();
			$tmp['ACT_CODE'] = array();
			
			// ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_01_RKTCHECKROLL';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($row['UPDATED_ROW'])) {
				foreach ($row['UPDATED_ROW'] as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$this->_model->updateRktCheckroll($record1);
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
			// ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
			
			// ************************************************ SAVE RKT CHECKROLL SUMMARY ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_02_RKTCHECKROLLSUM';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$lastTr = "";
			$lastArrTr = array();
			if (!empty($row['UPDATED_ROW'])) {
				foreach ($row['UPDATED_ROW'] as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if(($lastTr) && ($lastTr <> $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'])){				
						$this->_model->updateRktCheckrollSummary($lastArrTr);
					}
					
					$lastTr = $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'];
					$lastArrTr = $record1;
				}
			}
			$this->_model->updateRktCheckrollSummary($lastArrTr);
			
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
			// ************************************************ SAVE RKT CHECKROLL SUMMARY ************************************************
			
			// ************************************************ SAVE RKT CHECKROLL DISTRIBUSI ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_03_RKTCHECKROLLDIST';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$lastBa = "";
			$lastArrTr = array();
			if (!empty($row['UPDATED_ROW'])) {
				foreach ($row['UPDATED_ROW'] as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
						$this->_model->calDistribusiCheckroll($lastArrTr);
					}
					
					$lastBa = $record1['BA_CODE'];
					$lastArrTr = $record1;
				}
			}
			$this->_model->calDistribusiCheckroll($lastArrTr);
			
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
			// ************************************************ SAVE RKT CHECKROLL DISTRIBUSI ************************************************
			
			// ************************************************ UPDATE RKT WRA ************************************************
			//cari job code yang berhubungan dengan RKT WRA di TM_MAPPING_JOB_TYPE_WRA
			$query = "
				SELECT JOB_CODE
				FROM TM_MAPPING_JOB_TYPE_WRA
				WHERE DELETE_USER IS NULL 
				GROUP BY JOB_CODE
			";
			$check_job_code = $this->_db->fetchAll($query);
			
			$tmp['CHECK_JOB_CODE'] = array();
			if (!empty($check_job_code)) {
				foreach ($check_job_code as $idx1 => $record1) {
					array_push($tmp['CHECK_JOB_CODE'], $record1['JOB_CODE']);
				}
			}
			
			if (!empty($row['JOB_CODE'])) {
				foreach ($row['JOB_CODE'] as $idx1 => $record1) {
					if (in_array($record1, $tmp['CHECK_JOB_CODE']) == true) {
						$update_norma_wra = 1;
					}
				}
			}
			
			if ($update_norma_wra == 1) {
				$model = new Application_Model_NormaWra();
				$records1 = $this->_db->fetchAll("{$model->getData1($par)}");
				
				//1. SAVE NORMA WRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_04_RKTWRA';
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
				
				//2. SAVE NORMA WRA SUMMARY
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_05_RKTWRASUM';
				$this->_global->createBashFile($filename); //create bash file
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$lastBa = "";
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
							$uPar['filename'] = $filename;
							$uPar['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
							$uPar['BA_CODE'] = $lastBa;
							$model->updateSummaryNormaWra($uPar);
						}
						$lastBa = $record1['BA_CODE'];
					}
				}
				$uPar['filename'] = $filename;
				$uPar['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
				$uPar['BA_CODE'] = $lastBa;
				$model->updateSummaryNormaWra($uPar);
				
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
			// ************************************************ UPDATE RKT WRA ************************************************
			
			// ************************************************ UPDATE RKT VRA ************************************************
			//cari job code yang berhubungan dengan RKT VRA di TM_MAPPING_JOB_TYPE_VRA
			$query = "
				SELECT JOB_CODE, VRA_CODE
				FROM TM_MAPPING_JOB_TYPE_VRA
				WHERE DELETE_USER IS NULL 
				GROUP BY JOB_CODE, VRA_CODE
			";
			$check_job_code = $this->_db->fetchAll($query);
			
			$tmp['CHECK_JOB_CODE'] = array();
			if (!empty($check_job_code)) {
				foreach ($check_job_code as $idx1 => $record1) {
					array_push($tmp['CHECK_JOB_CODE'], $record1['JOB_CODE']);
				}
			}
			
			if (!empty($row['JOB_CODE'])) {
				foreach ($row['JOB_CODE'] as $idx1 => $record1) {
					if (in_array($record1, $tmp['CHECK_JOB_CODE']) == true) {
						$update_norma_vra = 1;
					}
				}
			}
			
			if ($update_norma_vra == 1) {
				$model = new Application_Model_RktVra();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_06_RKTVRA';
				$this->_global->createBashFile($filename); //create bash file
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->save($record1);
						
						//distinct VRA
						if (in_array($record1['VRA_CODE'], $tmp['VRA_CODE']) == false) {
							array_push($tmp['VRA_CODE'], $record1['VRA_CODE']);
						}
					}
				}
				$par['VRA_CODE'] = (count($tmp['VRA_CODE']) > 1) ? implode("','", $tmp['VRA_CODE']) : $tmp['VRA_CODE'][0];
				
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
			// ************************************************ UPDATE RKT VRA ************************************************
			
			// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
			if (!empty($tmp['VRA_CODE']) && !empty($row['BA_CODE'])) {
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_07_RKTVRASUM';
				$this->_global->createBashFile($filename); //create bash file
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
				foreach ($row['BA_CODE'] as $idx1 => $record1) {
					foreach ($tmp['VRA_CODE'] as $idx2 => $record2) {
						$par_sum['filename'] = $filename;
						$par_sum['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
						$par_sum['BA_CODE'] = $record1;
						$par_sum['VRA_CODE'] = $record2;
						$model->updateSummaryRktVra($par_sum);
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
			// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
			
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
			if ($update_norma_vra == 1) {
				$model = new Application_Model_NormaDistribusiVraNonInfra();
				$updated_row = $this->_db->fetchAll("{$model->getChangedData($par)}");
				
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_08_DISTVRANONINFRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//UPDATE DISTRIBUSI VRA - NON INFRA
				if (!empty($updated_row)) {
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
				
				
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_09_SUMDISTVRANONINFRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA
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
			}
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
			
			// ************************************************ UPDATE OPEX VRA ************************************************
			if ($update_norma_vra == 1) {
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_10_OPEXVRA';
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
			}
			// ************************************************ UPDATE OPEX VRA ************************************************
			
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			$check = strpos($par['VRA_CODE'], 'DT010'); //kalo vra nya DUMP TRUCK, baru update cost unit
			if ($check !== false) {	
				$check_update_rkt_panen = 1;
				
				$model = new Application_Model_NormaPanenCostUnit();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_11_NPANENCOSTUNIT';
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
			
			// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
			$check = strpos($par['JOB_CODE'], 'FX140'); //kalo job code mandor, baru update
			if (!$check) $check = strpos($par['JOB_CODE'], 'FX230'); //kalo job code mandor, baru update
			
			if ($check !== false) {	
				$model = new Application_Model_NormaPanenSupervisi();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_12_NPANENSUPERVISI';
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
			}
			// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
			
			// ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
			$check = strpos($par['JOB_CODE'], 'FX160'); //kalo job code krani buah, baru update
			
			if ($check !== false) {	
				$model = new Application_Model_NormaPanenKraniBuah();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_13_NPANENKRANIBUAH';
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
			}
			// ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
			
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			if ($update_norma_vra == 1){
				$model = new Application_Model_NormaPanenPremiLangsir();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_14_NPANENPREMILANGSIR';
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
			}
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			
			// ************************************************ UPDATE NORMA PANEN LOADING ************************************************
			$check = strpos($par['JOB_CODE'], 'FW041'); //kalo job code loading, baru update
			
			if ($check !== false) {	
				$model = new Application_Model_NormaPanenLoading();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_15_NPANENLOADING';
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
			}
			// ************************************************ UPDATE NORMA PANEN LOADING ************************************************
			
			// ************************************************ UPDATE NORMA BIAYA ************************************************			
			$model = new Application_Model_NormaBiaya();
			$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_16_NBIAYA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				$update_norma_biaya = 1;
				
				
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
			// ************************************************ UPDATE NORMA BIAYA ************************************************
			
			// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
			if(!empty($par['VRA_CODE']) && !empty($par['JOB_CODE'])){
				$par1['key_find'] = $par['key_find'];
				$par1['sub_cost_element'] = $par['VRA_CODE'] . "', '" . $par['JOB_CODE'];
			}else{
				$par1 = $par;
			}
			
			$model = new Application_Model_NormaInfrastruktur();
			$records1 = $this->_db->fetchAll("{$model->getData($par1)}");
		
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_17_NINFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				$update_norma_infra = 1;
				
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
			$check = strpos($par['VRA_CODE'], 'EX010'); //kalo vra nya EXCAV, baru update
			if (!$check) $check = strpos($par['VRA_CODE'], 'VC010'); //kalo vra nya COMPACTOR, baru update
			if (!$check)  $check = strpos($par['VRA_CODE'], 'GD010'); //kalo vra nya GRADER, baru update
			
			if ($check !== false) {	
				$check_update_rkt_perkerasan_jalan = 1;
				
				$model = new Application_Model_NormaPerkerasanJalan();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
			
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_18_NPERKERASANJALAN';
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
			if ($update_norma_biaya == 1 || $update_norma_vra == 1){
				$model = new Application_Model_RktLc();
				$records1 = $this->_db->fetchAll("{$model->getDataInheritance($par)}");
				
				//1. SAVE COST ELEMENT RKT LC
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_19_RKTLCCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_20_RKTLCTOTAL';
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
			}
			// ************************************************ UPDATE RKT LC ************************************************
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
			if ($update_norma_biaya == 1 || $update_norma_vra == 1){
				$model = new Application_Model_RktManualNonInfra();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");	
				
				//1. SAVE RKT MANUAL NON INFRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_21_RKTRAWATCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_22_RKTRAWATTOTAL';
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
			// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
			
			// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL NON INFRA
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NCHECKROLL_23A_RKTKASTRASICE';
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
			$filename = $row['uniq_code_file'].'_NCHECKROLL_23A_RKTKASTRASITOTAL';
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
			if ($update_norma_biaya == 1 || $update_norma_vra == 1){
				$model = new Application_Model_RktManualNonInfraOpsi();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT MANUAL NON INFRA + OPSI
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_23_RKTRAWATOPSICE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_24_RKTRAWATOPSITOTAL';
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
			// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
			
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
			if($update_norma_infra == 1){
				$model = new Application_Model_RktManualInfra();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");	
				
				//1. SAVE RKT MANUAL INFRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_25_RKTRAWATINFRACE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_26_RKTRAWATINFRATOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				$lastAfd = ""; $lastActClass = ""; $lastActCode = ""; $lastLandType = ""; $lastTopo = ""; $lastBA = ""; $lastBiaya = ""; 
				$arrAfdUpds = array(); // variabel array data afd yang di modified
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {	
						$curAfd = $record1['AFD_CODE'];
						$curActClass = $record1['ACTIVITY_CLASS'];
						$curActCode = $record1['ACTIVITY_CODE'];
						$curLandType = $record1['LAND_TYPE'];
						$curTopo = $record1['TOPOGRAPHY'];
						$curBA = $record1['BA_CODE']; 
						$curBiaya = $record1['SUMBER_BIAYA']; 
						$curPeriod = $record1['PERIOD_BUDGET']; 	
						
						//hitung total cost
						$record1['filename'] = $filename;
						$model->calTotalCost($record1);
				
						if(($lastAfd != "") && (($lastAfd!=$curAfd)||($lastActClass!=$curActClass)||($lastActCode!=$curActCode)||($lastLandType!=$curLandType)||($lastTopo!=$curTopo))){
								array_push($arrAfdUpds, 
										   array('AFD_CODE'=>$lastAfd, 
												 'BA_CODE'=>$curBA, 
												 'SUMBER_BIAYA'=>$curBiaya, 
												 'TRX_CODE'=>$trxCode,
												 'ACTIVITY_CLASS'=>$lastActClass, 
												 'ACTIVITY_CODE'=>$lastActCode, 
												 'LAND_TYPE'=>$lastLandType, 
												 'TOPOGRAPHY'=>$lastTopo,
												 'PERIOD_BUDGET'=>$curPeriod
												)
										   );
						}
						$lastAfd=$curAfd; 
						$lastActClass=$curActClass; 
						$lastActCode=$curActCode; 
						$lastLandType=$curLandType; 
						$lastTopo=$curTopo;
						$lastBA=$curBA; 
						$lastBiaya=$curBiaya;
					}
				}
				array_push($arrAfdUpds, 
						   array('AFD_CODE'=>$lastAfd, 
								 'BA_CODE'=>$curBA, 
								 'SUMBER_BIAYA'=>$curBiaya, 
								 'TRX_CODE'=>$trxCode,
								 'ACTIVITY_CLASS'=>$lastActClass, 
								 'ACTIVITY_CODE'=>$lastActCode, 
								 'LAND_TYPE'=>$lastLandType, 
								 'TOPOGRAPHY'=>$lastTopo,
								 'PERIOD_BUDGET'=>$curPeriod
								)
						   );	
				
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
				
				//4. HITUNG DISTRIBUSI VRA
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_26_RKTRAWATINFRASVDIST';
				$this->_global->createBashFile($filename); //create bash file
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				// save Distribusi VRA per AFD
				$arrAfdFixs = array();
				$lastAfd = ""; $totalDistVraManInfra = 0; $totalHrgHMKM=0; $totalHrgInternal=0; $lastActCode="";

				//distinct data
				$tmp = array ();
				foreach ($arrAfdUpds as $row) {
					if (!in_array($row,$tmp)) array_push($tmp,$row);
				}
				
				foreach ($tmp as $key => $arrAfdUpd) { //disini harus di-akumulasi hasil perhitungan per-afdeling.
					$arrHitungDistVra = $model->hitungDistVra($arrAfdUpd);
					$curAfd = $arrAfdUpd['AFD_CODE'];
					$curPeriod = $arrAfdUpd['PERIOD_BUDGET'];
					$curBA = $arrAfdUpd['BA_CODE'];
					$curActCode = $arrAfdUpd['ACTIVITY_CODE'];
					
					//kelompokan per-afdeling
					if(($vraCode<>"") && ($lastAfd!="") && ($lastAfd!=$curAfd)){
						array_push($arrAfdFixs, 
							array('AFD_CODE'=>$lastAfd, 'totalDistVraManInfra'=>$tempDistVraManInfra, 'totalHrgHMKM'=>$tempHrgHMKM, 
							'totalHrgInternal'=>$tempHrgInternal, 'vraCode'=>$tempVraCode, 'BA_CODE'=>$lastBa, 'TRX_CODE'=>$trxCode, 
							'ACTIVITY_CODE'=>$tempActCode, 'PERIOD_BUDGET'=>$lastPeriod));
						
						//reset total hitungan disini
						$totalDistVraManInfra=0; $totalHrgHMKM=0; $totalHrgInternal=0; 
					} 		
					
					$totalDistVraManInfra += $arrHitungDistVra['totalDistVraManInfra'];
					$totalHrgHMKM += $arrHitungDistVra['totalHrgHMKM'];
					$totalHrgInternal += $arrHitungDistVra['totalHrgInternal'];
					$vraCode = $arrHitungDistVra['vraCode'];
					
					$lastBa=$curBA; 
					$lastAfd=$curAfd;
					$lastPeriod=$curPeriod; 			
					
					//digunakan untuk simpan record hitungan terakhir
					$tempDistVraManInfra=$totalDistVraManInfra; 
					$tempHrgHMKM=$totalHrgHMKM; 
					$tempHrgInternal=$totalHrgInternal; 
					$tempAfd=$curAfd; 
					$tempPeriod=$curPeriod; 
					$tempActCode=$curActCode;
					$tempVraCode=$vraCode;	
				}
				
				if($vraCode<>""){
					array_push($arrAfdFixs, 
						array('AFD_CODE'=>$tempAfd, 'totalDistVraManInfra'=>$tempDistVraManInfra, 'totalHrgHMKM'=>$tempHrgHMKM, 
						'totalHrgInternal'=>$tempHrgInternal, 'vraCode'=>$tempVraCode, 'BA_CODE'=>$lastBa, 'TRX_CODE'=>$trxCode, 
						'ACTIVITY_CODE'=>$tempActCode, 'PERIOD_BUDGET'=>$tempPeriod));
				}
					
				foreach ($arrAfdFixs as $key => $arrAfdFix) {
					$arrAfdFix['filename'] = $filename;
					// ganti variable $this->_model menjadi $model 
					//karena $this->_model gak ketemu functionnya
					//by YUS 20/11-2014
					$model->saveDistVra($arrAfdFix);
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
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
			
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			if ($update_norma_biaya == 1 || $update_norma_vra == 1){
				$model = new Application_Model_RktTanamManual();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM MANUAL
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_27_RKTTANAMCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_28_RKTTANAMTOTAL';
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
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			
			// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
			if ($update_norma_biaya == 1 || $update_norma_vra == 1){
				$model = new Application_Model_RktTanam();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM OTOMATIS
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_29_RKTTANAMOTOCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_30_RKTTANAMOTOTOTAL';
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
			// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
			
			// ************************************************ UPDATE RKT PANEN ************************************************			
			if ($check_update_rkt_panen == 1){
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($par)}");
				
				//1. SAVE RKT TANAM OTOMATIS
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_31_RKTPANENCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_32_RKTPANENTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$params = $this->_request->getPost();
				$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
				$lastAfd = ""; $lastBA = ""; $lastBiaya = "";
				$arrAfdUpds = array(); // variabel array data afd yang di modified
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$curAfd = $record1['AFD_CODE'];
						$curBA = $record1['BA_CODE'];
						$curBiaya = $record1['SUMBER_BIAYA'];
						$curPeriod = $record1['PERIOD_BUDGET'];
						
						$record1['filename'] = $filename;
						$model->calTotalCost($record1);
				
						if(($lastAfd) && ($lastAfd!=$curAfd)){
							array_push($arrAfdUpds, 
									   array('AFD_CODE'=>$lastAfd,
											 'BA_CODE'=>$lastBA,
											 'SUMBER_BIAYA'=>$lastBiaya,
											 'PERIOD_BUDGET'=>$lastPeriod)
									  );
						}
						$lastAfd = $curAfd; 
						$lastBA = $curBA; 
						$lastBiaya = $curBiaya;
						$lastPeriod = $curPeriod;
					}
				}
				array_push($arrAfdUpds, 
						   array('AFD_CODE'=>$lastAfd,
								 'BA_CODE'=>$lastBA,
								 'SUMBER_BIAYA'=>$lastBiaya,
								 'PERIOD_BUDGET'=>$lastPeriod)
						  );
				
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
		
				//4. HITUNG DISTRIBUSI VRA PER AFD
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $uniq_code_file.'_00_TNCHECKROLL_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveDistVra';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				foreach ($arrAfdUpds as $key => $arrAfdUpd) {
					$arrAfdUpd['filename'] = $filename;
					// ganti variable $this->_model menjadi $model 
					//karena $this->_model gak ketemu functionnya
					//by YUS 20/11-2014
					$return = $model->saveDistVra($arrAfdUpd);
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_33_RKTPERKJALANCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$record1['filename'] = $filename;
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_34_RKTPERKJALANTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//hitung distribusi biaya seluruh halaman
				$params = $this->_request->getPost();
				$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
				$lastAfd = ""; $lastBA = ""; $lastAct = "";
				$arrAfdUpds = array(); // variabel array data afd yang di modified
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$curAfd = $record1['AFD_CODE'];
						$curBA = $record1['BA_CODE'];
						$curAct = $record1['ACTIVITY_CODE'];
						$curPer = $record1['PERIOD_BUDGET'];
						
						//hitung total cost
						$record1['filename'] = $filename;
						$model->calTotalCost($record1);
				
						if(($lastAfd) && ($lastAfd!=$curAfd)){
							array_push($arrAfdUpds, 
									   array('AFD_CODE'=>$lastAfd,
											 'BA_CODE'=>$lastBA,
											 'ACTIVITY_CODE'=>$lastAct,
											 'PERIOD_BUDGET'=>$lastPer)
									  );
						}
						$lastAfd = $curAfd; 
						$lastBA = $curBA; 
						$lastAct = $curAct;
						$lastPer = $curPer;
					}
				}
				array_push($arrAfdUpds, 
						   array('AFD_CODE'=>$lastAfd,
								 'BA_CODE'=>$lastBA,
								 'ACTIVITY_CODE'=>$lastAct,
								 'PERIOD_BUDGET'=>$lastPer)
						  );
				
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
		
				//4. HITUNG DISTRIBUSI VRA PER AFD
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_34_RKTPERKJALANSVDIST';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				foreach ($arrAfdUpds as $key => $arrAfdUpd) {
					$arrAfdUpd['filename'] = $filename;
					// ganti variable $this->_model menjadi $model 
					//karena $this->_model gak ketemu functionnya
					//by YUS 20/11-2014
					$return = $model->saveDistVra($arrAfdUpd);
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_35_RKTPUPUKBIAYANORMAL';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;	
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_36_RKTPUPUKBIAYASISIP';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;	
						$model->calCostElement('LABOUR', $record1);
						if ($update_norma_vra == 1) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_NCHECKROLL_37_RKTPUPUKBIAYA';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//save
				$model = new Application_Model_RktPupukDistribusiBiayaGabungan();	
				$par['filename'] = $filename;	
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
	}
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul		
		$lock = $this->_global->checkLockTable($params);		
		if($lock['JUMLAH']){
			$data['return'] = "locked";
			$data['module'] = $lock['MODULE'];
			$data['insert_user'] = $lock['INSERT_USER'];
			die(json_encode($data));
		}
		
		//ketika checkroll melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
		$this->_global->insertLockTable($params['BA_CODE'], 'RKT CHECKROLL');
		
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NCHECKROLL_01_DELETE';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hapus data
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
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		//distinct JOB CODE & BA CODE
		$uniq_row['UPDATED_ROW'][0] = $params;
		$uniq_row['JOB_CODE'] = $params['JOB_CODE'];
		$uniq_row['BA_CODE'] = $params['BA_CODE'];
		
		$idxInherit = 1;			
		//deklarasi var utk inherit module
		$uniq_row['uniq_code_file'] 	= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename		
		
		//update inherit module
		$this->updateInheritModule($uniq_row);
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		//hapus dari table lock ketika selesai melakukan perhitungan di norma CHECKROLL
		$this->_global->deleteLockTable($params['BA_CODE'], 'RKT CHECKROLL');
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_CHECKROLL";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_CHECKROLL";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_CHECKROLL";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
