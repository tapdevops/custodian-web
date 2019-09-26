<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Catu
Function 			:	- listAction		: menampilkan list Catu
						- saveAction		: simpan data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	30/05/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class CatuController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_Catu();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/catu/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Catu';
		$this->_helper->layout->setLayout('detail');
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Catu
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
				$rows[$key]['PERIOD_BUDGET']    = $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']        	= $params['text03'][$key]; // BA CODE
				$rows[$key]['RICE_PORTION']   	= $params['text05'][$key]; // RICE_PORTION
				$rows[$key]['PRICE_KG']   		= $params['text06'][$key]; // PRICE_KG
				$rows[$key]['HKE_BULAN']   		= $params['text07'][$key]; // HKE_BULAN
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['employee_status'] 	= $params['text04'][$key]; // employee_status
            }
        }
		return $rows;
	}
	
	//simpan temporary data
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		if (!empty($rows)) {	
			
			$filename = $this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma dasar
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
		}
		die('no_alert');
	}
	
	//save data
	public function saveAction()
    {
		$params = $this->_request->getParams();
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName().'_TMCATU_'; //generate file name - hanya ada 1 file name
		if (!empty($rows)) {
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
			/*foreach ($rows as $key => $row) {
				$params['key_find'] = $row['BA_CODE'];
				$lock = $this->_global->checkLockTable($params);		
				if($lock['JUMLAH']){
					$data['return'] = "locked";
					$data['module'] = $lock['MODULE'];
					$data['insert_user'] = $lock['INSERT_USER'];
					die(json_encode($data));
				}
			}*/
			
			// ************************************************ SAVE MASTER CATU TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_01_SVTEMP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save master catu temp
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
			// ************************************************ SAVE MASTER CATU TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL MASTER CATU ************************************************
		
				// ************************************************ UPDATE CATU ************************************************
				//generate filename untuk .sh dan .sql
				$filename = $uniq_code_file.'01_SAVE';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$uniq_row['BA_CODE'] = array();
				
				$params = $this->_request->getPost();
				$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
				
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						if($record1['FLAG_TEMP'] == 'Y'){
							$record1['filename'] = $filename;
							$this->_model->save($record1);
							
							//ditampung untuk inherit module
							$updated_row[] = $record1;
							
							//distinct BA
							if (in_array($record1['BA_CODE'], $uniq_row['BA_CODE']) == false) {
								array_push($uniq_row['BA_CODE'], $record1['BA_CODE']);
							}
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
			// ************************************************ UPDATE CATU ************************************************
			
			// ************************************************ UPDATE CATU SUM ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'02_SUMMARY';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//update sum catu			
			if (!empty($uniq_row['BA_CODE'])) {
				foreach($uniq_row['BA_CODE'] as $idx => $val) {
					$par['filename'] = $filename;
					$par['PERIOD_BUDGET'] = $params['budgetperiod'];
					$par['BA_CODE'] = $val;
					$this->_model->updateSummaryCatu($par);
				}
			}	
			//}
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			//buka 
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
			// ************************************************ UPDATE CATU SUM ************************************************
			
			// ************************************************ UPDATE INHERIT MODULE ************************************************
			if (!empty($uniq_row['BA_CODE'])) {
				$idxInherit = 1;
				foreach($uniq_row['BA_CODE'] as $idx => $val) {
					//update inherit module
					$record1['uniq_code_file'] 	= $uniq_code_file.'INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT).'_'; // filename				
					$record1['PERIOD_BUDGET'] = $params['budgetperiod'];
					$record1['BA_CODE'] = $val;
					$this->updateInheritModule($record1);
					
					$idxInherit++;
				}
			}
			// ************************************************ UPDATE INHERIT MODULE ************************************************
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		if (!empty($row)) {
			$urutan = 0;
			$check_update_rkt_panen = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Panen
			$check_update_rkt_perkerasan_jalan = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Perkerasan Jalan
			$update_norma_wra = 0;
			$update_norma_vra = 0;
			$update_norma_biaya = 0;
			$update_norma_infra = 0;
			$tmp['VRA_CODE'] = array();
			$tmp['ACT_CODE'] = array();
			
			// ************************************************ NORMA CHECKROLL ************************************************
			$model = new Application_Model_NormaCheckroll();		
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_CHECKROLL';
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
			// ************************************************ NORMA CHECKROLL ************************************************
			
			// ************************************************ RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_CHECKROLL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->updateRktCheckroll($record1);
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
			// ************************************************ RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
			
			// ************************************************ RKT CHECKROLL SUMMARY ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_CHECKROLL_SUMMARY';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if(($lastTr) && ($lastTr <> $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'])){				
						$model->updateRktCheckrollSummary($lastArrTr);
					}
					
					$lastTr = $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'];
					$lastArrTr = $record1;
				}
				$model->updateRktCheckrollSummary($lastArrTr);
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
			// ************************************************ RKT CHECKROLL SUMMARY ************************************************
			
			// ************************************************ RKT CHECKROLL DISTRIBUSI ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_CHECKROLL_DISTRIBUSI';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
						$model->calDistribusiCheckroll($lastArrTr);
					}
					
					$lastBa = $record1['BA_CODE'];
					$lastArrTr = $record1;
				}
				$model->calDistribusiCheckroll($lastArrTr);
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
			// ************************************************ RKT CHECKROLL DISTRIBUSI ************************************************
			
			// ************************************************ NORMA WRA ************************************************
			$model = new Application_Model_NormaWra();		
			$records1 = $this->_db->fetchAll("{$model->getData1($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_WRA';
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
			// ************************************************ NORMA WRA ************************************************
			
			// ************************************************ NORMA WRA SUMMARY ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_WRA_SUM';
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
			// ************************************************ NORMA WRA SUMMARY ************************************************
			
			// ************************************************ RKT VRA ************************************************
			$model = new Application_Model_RktVra();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_VRA';
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
			$row['VRA_CODE'] = (count($tmp['VRA_CODE']) > 1) ? implode("','", $tmp['VRA_CODE']) : $tmp['VRA_CODE'][0];
			
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
			// ************************************************ RKT VRA ************************************************
			
			// ************************************************ RKT VRA SUMMARY ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_VRA_SUM';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			foreach ($tmp['VRA_CODE'] as $idx2 => $record2) {
				$par_sum['filename'] = $filename;
				$par_sum['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
				$par_sum['BA_CODE'] = $row['BA_CODE'];
				$par_sum['VRA_CODE'] = $record2;
				$model->updateSummaryRktVra($par_sum);
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
			// ************************************************ RKT VRA SUMMARY ************************************************
			
			// ************************************************ RKT DISTRIBUSI VRA ************************************************
			$model = new Application_Model_NormaDistribusiVraNonInfra();
			$records1 = $this->_db->fetchAll("{$model->getChangedData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_DIST_VRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					$model->updateRecord($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
						array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$row['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
			
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
			// ************************************************ RKT DISTRIBUSI VRA ************************************************
			
			// ************************************************ RKT DISTRIBUSI VRA SUMMARY ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_DIST_VRA_SUM';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
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
			// ************************************************ RKT DISTRIBUSI VRA SUMMARY ************************************************
			
			// ************************************************ UPDATE OPEX VRA ************************************************
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_OPEX_VRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//update
			$row['filename'] = $filename;
			$model->updateRktOpexVra($row);
			
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
			$check = strpos($row['VRA_CODE'], 'DT010'); //kalo vra nya DUMP TRUCK, baru update cost unit
			if ($check !== false) {	
				$check_update_rkt_panen = 1;
				
				$model = new Application_Model_NormaPanenCostUnit();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_COST_UNIT';
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
			 
			// ************************************************ NORMA PANEN SUPERVISI ************************************************
			$model = new Application_Model_NormaPanenSupervisi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_SUPERVISI';
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
			// ************************************************ NORMA PANEN SUPERVISI ************************************************
			 
			// ************************************************ NORMA PANEN KRANI BUAH BEGIN ************************************************
			$model = new Application_Model_NormaPanenKraniBuah();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_KRANI_BUAH';
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
			// ************************************************ NORMA PANEN KRANI BUAH END ************************************************
			 
			// ************************************************ NORMA PANEN PREMI LANGSIR BEGIN ************************************************
			$model = new Application_Model_NormaPanenPremiLangsir();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_PREMI_LANGSIR';
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
			// ************************************************ NORMA PANEN PREMI LANGSIR END ************************************************
			 
			// ************************************************ NORMA PANEN LOADING BEGIN ************************************************
			$model = new Application_Model_NormaPanenLoading();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_LOADING';
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
			// ************************************************ NORMA PANEN LOADING END ************************************************
			
			// ************************************************ NORMA BIAYA BEGIN ************************************************
			$model = new Application_Model_NormaBiaya();			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_BIAYA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
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
			// ************************************************ NORMA BIAYA END ************************************************
			
			// ************************************************ NORMA INFRASTRUKTUR BEGIN ************************************************
			$model = new Application_Model_NormaInfrastruktur();			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_INFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
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
			
			// ************************************************ NORMA INFRASTRUKTUR END ************************************************
			 
			// ************************************************ NORMA PERKERASAN JALAN BEGIN ************************************************
			$model = new Application_Model_NormaPerkerasanJalan();			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PERK_JALAN';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
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
			// ************************************************ NORMA PERKERASAN JALAN END ************************************************
			
			//distinct activity
			if (!empty($temp_row['activity_code'])) {
				$uniq_arr = array_unique($temp_row['activity_code']);
				$row['activity_code'] =  implode("','", $uniq_arr);
			}
			 
			// ************************************************ RKT LC BEGIN ************************************************
			$model = new Application_Model_RktLc();
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_LC_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_LC_TOTAL';
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
			// ************************************************ RKT LC END ************************************************
			 
			// ************************************************ RKT MANUAL NON INFRA BEGIN ************************************************
			$model = new Application_Model_RktManualNonInfra();	
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_NONINFRA_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
				
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_NONINFRA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
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
			// ************************************************ RKT MANUAL NON INFRA END ************************************************
			
			// ************************************************ RKT KASTRASI + SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();	
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_NONINFRA_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
				
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_NONINFRA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
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
			// ************************************************ RKT KASTRASI + SANITASI ************************************************
			 
			// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL NON INFRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASICE';
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
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASITOTAL';
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
			 
			// ************************************************ RKT MANUAL INFRA BEGIN ************************************************
			$model = new Application_Model_RktManualInfra();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_INFRA_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
							
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
			
			//$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_INFRA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//hitung distribusi biaya seluruh halaman
			$params = $this->_request->getPost();
			$records1 = $this->_db->fetchAll("{$model->getData($params)}");
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
				
					$record1['filename'] = $filename;	
					//hitung total cost
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
		
		
			//3. HITUNG DISTRIBUSI VRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_MANUAL_INFRA_SVDIST';
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			// save Distribusi VRA per AFD
			$arrAfdFixs = array();
			$lastAfd = ""; $totalDistVraManInfra = 0; $totalHrgHMKM=0; $totalHrgInternal=0; $lastActCode="";

			//distinct data
			$tmp = array ();
			foreach ($arrAfdUpds as $rowx) {
				if (!in_array($rowx,$tmp)) array_push($tmp,$rowx);
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
			
			// ************************************************ RKT MANUAL INFRA END ************************************************
			 
			// ************************************************ RKT TANAM MANUAL BEGIN ************************************************
			$model = new Application_Model_RktTanamManual();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_TANAM_MANUAL_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_TANAM_MANUAL_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
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
			// ************************************************ RKT TANAM MANUAL END ************************************************
			 
			// ************************************************ RKT TANAM OTOMATIS BEGIN ************************************************
			$model = new Application_Model_RktTanam();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_TANAM_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_TANAM_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
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
			
			// ************************************************ RKT TANAM OTOMATIS END ************************************************
			 
			// ************************************************ RKT PANEN BEGIN ************************************************
			$model = new Application_Model_RktPanen();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PANEN_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// ************************************************
			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PANEN_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			//hitung distribusi biaya seluruh halaman
			$params = $this->_request->getPost();
			$records1 = $this->_db->fetchAll("{$model->getData($params)}");
			$lastAfd = ""; $lastBA = ""; $lastBiaya = "";
			$arrAfdUpds = array(); // variabel array data afd yang di modified
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$curAfd = $record1['AFD_CODE'];
					$curBA = $record1['BA_CODE'];
					$curBiaya = $record1['SUMBER_BIAYA'];
					$curPeriod = $record1['PERIOD_BUDGET'];
					
					$record1['filename'] = $filename;
					//hitung total cost
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
		
			//3. HITUNG DISTRIBUSI VRA PER AFD
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PANEN_SVDIST';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			foreach ($arrAfdUpds as $key => $arrAfdUpd) {
				$arrAfdUpd['filename'] = $filename;
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
			
			// ************************************************ RKT PANEN END ************************************************
			 
			// ************************************************ RKT PERKERASAN JALAN BEGIN ************************************************
			$model = new Application_Model_RktPerkerasanJalan();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PERK_JALAN_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// 2. calTotalCost ************************************************
			
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PERK_JALAN_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//hitung distribusi biaya seluruh halaman
			$params = $this->_request->getPost();
			$records1 = $this->_db->fetchAll("{$model->getData($params)}");
			$lastAfd = ""; $lastBA = ""; $lastAct = "";
			$arrAfdUpds = array(); // variabel array data afd yang di modified
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$curAfd = $record1['AFD_CODE'];
					$curBA = $record1['BA_CODE'];
					$curAct = $record1['ACTIVITY_CODE'];
					$curPer = $record1['PERIOD_BUDGET'];
					
					$record1['filename'] = $filename;
					//hitung total cost
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
		
		//3. HITUNG DISTRIBUSI VRA PER AFD
		//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PERK_JALAN_SVDIST';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			foreach ($arrAfdUpds as $key => $arrAfdUpd) {
				$arrAfdUpd['filename'] = $filename;
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
			// ************************************************ RKT PERKERASAN JALAN END ************************************************
			 
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA NORMAL BEGIN ************************************************
			$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
			$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PUPUK_BIAYA_NORMAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA NORMAL END ************************************************
			 
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA SISIP BEGIN ************************************************
			$model = new Application_Model_RktPupukDistribusiBiayaSisip();	
			$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");			
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PUPUK_BIAYA_SISIP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
					if ($row['VRA_CODE']) { $model->calCostElement('TRANSPORT', $record1); }
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
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA SISIP END ************************************************
			 
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN BEGIN ************************************************
			$model = new Application_Model_RktPupukDistribusiBiayaGabungan();
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PUPUK_BIAYA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
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
			// ************************************************ RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN END ************************************************
		}
	}
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CATU";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CATU";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CATU";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
