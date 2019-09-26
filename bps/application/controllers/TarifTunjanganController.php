<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Tarif Tunjangan
Function 			:	- listAction		: menampilkan list Tarif Tunjangan
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	07/06/2013
Update Terakhir		:	07/06/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class TarifTunjanganController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_TarifTunjangan();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/tarif-tunjangan/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Master Tarif Tunjangan';
		$this->_helper->layout->setLayout('detail');
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Tarif Tunjangan
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
				$rows[$key]['PERIOD_BUDGET'] 	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE'] 			= $params['text03'][$key]; // BA_CODE
				$rows[$key]['JOB_CODE']   		= $params['text04'][$key]; // JOB_CODE
				$rows[$key]['EMPLOYEE_STATUS']  = $params['text06'][$key]; // EMPLOYEE_STATUS
				$rows[$key]['VALUE']   			= $params['text08'][$key]; // VALUE
            }
        }
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		if (!empty($rows)) {		
			// ************************************************ SAVE TARIF TUNJANGAN TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = "YUS-".$this->_global->genFileName();
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
			// ************************************************ SAVE TARIF TUNJANGAN TEMP ************************************************
		
		}
		
		die('no_alert');
    }
	
	//save data
	public function saveAction()
    {
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $rows = $this->mappingAction();
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
			
			// ************************************************ SAVE MASTER TARIF TUNJANGAN TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_TARIFTUNJ_01_SVTEMP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma basic temp
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
			// ************************************************ SAVE MASTER TARIF TUNJANGAN TEMP ************************************************
			
			// ************************************************ SAVE MASTER TARIF TUNJANGAN ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_TM_TARIF_TUNJANGAN';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save temp
			$params = $this->_request->getPost();
			$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
			$uniq_row['BA_CODE'] = array();
			$uniq_row['PERIOD_BUDGET'] = array();
			if (!empty($records1)) {
				foreach ($records1 as $key => $record1) {
					if($record1['FLAG_TEMP'] == 'Y'){
						$record1['filename'] = $filename;
						$this->_model->save($record1);
						
						//distinct BA
							if (in_array($record1['BA_CODE'], $uniq_row['BA_CODE']) == false) {
								array_push($uniq_row['BA_CODE'], $record1['BA_CODE']);
							}
							
							if (in_array($record1['PERIOD_BUDGET'], $uniq_row['PERIOD_BUDGET']) == false) {
								array_push($uniq_row['PERIOD_BUDGET'], $record1['PERIOD_BUDGET']);
							}
							
					}
				}
			}			
			$updated_row['BA_CODE'] = (count($uniq_row['BA_CODE']) > 1) ? implode("','", $uniq_row['BA_CODE']) : $uniq_row['BA_CODE'][0];
			$updated_row['PERIOD_BUDGET'] = (count($uniq_row['PERIOD_BUDGET']) > 1) ? implode("','", $uniq_row['PERIOD_BUDGET']) : $uniq_row['PERIOD_BUDGET'][0];
			
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
			// ************************************************ SAVE MASTER TARIF TUNJANGAN ************************************************
			
			// ************************************************ UPDATE INHERIT MODULE ************************************************
		if (!empty($updated_row['BA_CODE'])) {
			$idxInherit = 1;
			//deklarasi var utk inherit module
			$uniq_row['uniq_code_file'] 	= $uniq_code_file.'TRF_TUNJ_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename		
			$uniq_row['key_find'] = $updated_row['BA_CODE'];
			$uniq_row['BA_CODE'] = $updated_row['BA_CODE'];
			$uniq_row['PERIOD_BUDGET'] = $updated_row['PERIOD_BUDGET'];
				
			//update inherit module
			$this->updateInheritModule($uniq_row);
		}
		// ************************************************ UPDATE INHERIT MODULE ************************************************
	}
	
	$data['return'] = "done";
	die(json_encode($data));
}
		//update inherit module
		public function updateInheritModule($row = array()) {
			//####################################### UPDATE INHERITANCE MODULE #######################################
			if (!empty($row)) {
				//$row['key_find'] = (count($row['BA_CODE']) > 1) ? implode("','", $row['BA_CODE']) : $row['BA_CODE'][0];
				//$row['uniq_code_file'] = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
				$urutan=0;
				// ************************************************ UPDATE NORMA CHECKROLL ************************************************
				$model = new Application_Model_NormaCheckroll();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				// ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_01_RKTCHECKROLL';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				// ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
				
				// ************************************************ SAVE RKT CHECKROLL SUMMARY ************************************************
				//generate filename untuk .sh dan .sql
				$filename = $row['uniq_code_file'].'_NCHECKROLL_02_RKTCHECKROLLSUM';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$lastTr = "";
				$lastArrTr = array();
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						
						if(($lastTr) && ($lastTr <> $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'])){				
							$model->updateRktCheckrollSummary($lastArrTr);
						}
						
						$lastTr = $record1['PERIOD_BUDGET'].$record1['BA_CODE'].$record1['JOB_CODE'];
						$lastArrTr = $record1;
					}
				}
				$model->updateRktCheckrollSummary($lastArrTr);
				
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
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'YS_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NCHECKROLL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				$lastBa = "";	
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->save($record1);
						if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
							$model->calDistribusiCheckroll($record1);
						}				
						$lastBa = $record1['BA_CODE'];
					
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
				
				// ************************************************ UPDATE NORMA CHECKROLL ************************************************
				
				// ************************************************ UPDATE SUMMARY NORMA WRA ************************************************
				//generate filename untuk .sh dan .sql
				$model = new Application_Model_NormaWra();			
				$rec = $this->_db->fetchAll("{$model->getData1($row)}");
				$lastBa = "";
			
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NWRASUM';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$lastBa = "";
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
							$uPar['filename'] = $filename;
							$uPar['BA_CODE'] = $lastBa;
							$uPar['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
							$model->updateSummaryNormaWra($uPar);
						}				
						$lastBa = $record1['BA_CODE'];
					}
					$uPar['filename'] = $filename;
					$uPar['BA_CODE'] = $lastBa;
					$uPar['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
					$model->updateSummaryNormaWra($uPar);
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
				
				// ************************************************ UPDATE SUMMARY NORMA WRA ************************************************
				
				// ************************************************ UPDATE RKT VRA ************************************************
				$model = new Application_Model_RktVra();
				$model_dist = new Application_Model_NormaDistribusiVraNonInfra();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$temp_row['vra_code'][] = $record1['VRA_CODE'];
						
						$model->save($record1);
						$model->updateSummaryRktVra($record1);	
					}
					
					//cari aktivitas yang terupdate di dist VRA
					$records1 = $this->_db->fetchAll("{$model_dist->getDataHeader($row)}");
					foreach ($records1 as $idx1 => $record1) {
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
					}
				}
			
				$uniq_arr = array_unique($temp_row['vra_code']);
				$row['vra_code'] =  implode("','", $uniq_arr);
			
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
				
				// ************************************************ UPDATE RKT VRA ************************************************
				
				// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$temp_row['vra_code'][] = $record1['VRA_CODE'];
						// DISTRIBUSI VRA - NON INFRA
						$model_dist->updateRecord($record1);
						$model_dist->updateSummaryNormaDistribusiVra($record1);
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
				
				// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
				
				// ************************************************ UPDATE OPEX RKT VRA ************************************************
				$model = new Application_Model_RktOpexVra();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'OPEXVRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE OPEX RKT VRA ************************************************
				
				// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
				$check = strpos($row['vra_code'], 'DT010');
			if ($check !== false) {
				$model = new Application_Model_NormaPanenCostUnit();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENCOSTUNIT';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				$model = new Application_Model_NormaPanenSupervisi();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENSUPERVISI';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
				
				// ************************************************ UPDATE NORMA KRANI BUAH ************************************************
				$model = new Application_Model_NormaPanenKraniBuah();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENKRANIBUAH';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE NORMA KRANI BUAH ************************************************
				
				// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
				$model = new Application_Model_NormaPanenPremiLangsir();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENPREMILANGSIR';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE NORMA PANEN LOADING ************************************************
				$model = new Application_Model_NormaPanenLoading();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENLOADING';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE NORMA PANEN LOADING ************************************************
				
				// ************************************************ UPDATE NORMA BIAYA ************************************************
				$model = new Application_Model_NormaBiaya();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NBIAYA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
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
				
				// ************************************************ UPDATE NORMA BIAYA ************************************************
				
				// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
				$model = new Application_Model_NormaInfrastruktur();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				$tmp['ACT_CODE'] = array();
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NINFRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
						$model->save($record1);
						
						//distinct activity
						if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
							array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
						}
						
					}
				}
				$row['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
				$row['activity_code'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
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
				$model = new Application_Model_NormaPerkerasanJalan();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPERKJALAN';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
						$model->save($record1);
					}
				}
				
				//distinct activity
				if (!empty($temp_row['activity_code'])) {
					$uniq_arr = array_unique($temp_row['activity_code']);
					$row['activity_code'] =  implode("','", $uniq_arr);
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
				// ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
				
			if(!empty($row['activity_code'])){
				// ************************************************ UPDATE RKT LC ************************************************
				$model = new Application_Model_RktLc();
				$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
						
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
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
				
				// ************************************************ UPDATE RKT LC ************************************************
			}

				
			//distinct activity
			if (!empty($temp_row['activity_code'])) {
				$uniq_arr = array_unique($temp_row['activity_code']);
				$row['activity_code'] =  implode("','", $uniq_arr);
			}
			if(!empty($row['activity_code'])){		
				// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
				$model = new Application_Model_RktManualNonInfra();	
				$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRACE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRATOTAL';
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
				// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
			}

			if(!empty($row['activity_code'])){	
				// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
				$model = new Application_Model_RktKastrasiSanitasi();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
				
				//1. SAVE RKT MANUAL NON INFRA
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASICE';
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASITOTAL';
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
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSICE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSITOTAL';
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
				
				// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************	
				
				// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
				$model = new Application_Model_RktManualInfra();	
				$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRACE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRATOTAL';
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
				// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
				
				// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
				$model = new Application_Model_RktTanamManual();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALTOTAL';
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

				// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
				
				// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
				$model = new Application_Model_RktTanam();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISTOTAL';
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
				
				// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
				
				// ************************************************ UPDATE RKT PANEN ************************************************
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENTOTAL';
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
				
				// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
				$model = new Application_Model_RktPerkerasanJalan();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
				$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANTOTAL';
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
				
				// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
			}	
				//jika aktivitas TABUR PUPUK, UNTIL PUPUK baru hitung RKT Pupuk
				
					// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
					$model = new Application_Model_RktPupukDistribusiBiayaNormal();
					$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
					
					//1. HITUNG PER COST ELEMENT
					//generate filename untuk .sh dan .sql
					$urutan++;
					$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKNORMAL';
					$this->_global->createBashFile($filename); //create bash file			
					$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
								
					if (!empty($records1)) {
						foreach ($records1 as $idx1 => $record1) {
							$record1['filename'] = $filename;
							//hitung cost element
							$model->calCostElement('LABOUR', $record1);
							if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
					$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
					
					//1. HITUNG PER COST ELEMENT
					//generate filename untuk .sh dan .sql
					$urutan++;
					$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKSISIP';
					$this->_global->createBashFile($filename); //create bash file			
					$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
								
					if (!empty($records1)) {
						foreach ($records1 as $idx1 => $record1) {
							$record1['filename'] = $filename;
							//hitung cost element
							$model->calCostElement('LABOUR', $record1);
							if ($row['vra_code']) { $model->calCostElement('TRANSPORT', $record1); }
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
					$filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKTOTAL';
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
			//####################################### UPDATE INHERITANCE MODULE #######################################
		
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_TARIF_TUNJANGAN";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_TARIF_TUNJANGAN";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_TARIF_TUNJANGAN";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
