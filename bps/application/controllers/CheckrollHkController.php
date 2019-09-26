<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Checkroll HK
Function 			:	- listAction		: menampilkan list Checkroll HK
						- saveAction		: simpan data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/05/2013
Update Terakhir		:	30/05/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class CheckrollHkController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_CheckrollHk();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/checkroll-hk/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Checkroll HK';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('detail');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Checkroll HK
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
				$rows[$key]['PERIOD_BUDGET']   	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']   		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['EMPLOYEE_STATUS']  = $params['text13'][$key]; // EMPLOYEE_STATUS
				$rows[$key]['HARI_SETAHUN']   	= $params['text04'][$key]; // HARI_SETAHUN
				$rows[$key]['MINGGU_SETAHUN']   = $params['text05'][$key]; // MINGGU_SETAHUN
				$rows[$key]['LIBUR_SETAHUN'] 	= $params['text06'][$key]; // LIBUR_SETAHUN
				$rows[$key]['CUTI']   			= $params['text08'][$key]; // CUTI
				$rows[$key]['SAKIT']   			= $params['text09'][$key]; // SAKIT
				$rows[$key]['IZIN']   			= $params['text10'][$key]; // IZIN
				$rows[$key]['HAID']   			= $params['text11'][$key]; // HAID
				
				//deklarasi var utk inherit module
				$rows[$key]['budgetperiod']		= $params['text02'][$key]; // budgetperiod
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['employee_status'] 	= $params['text13'][$key]; // employee_status
            }
        }
		return $rows;
	}
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		if (!empty($rows)) {		
			// ************************************************ SAVE CHECKROLL HK TEMP ************************************************
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
			// ************************************************ SAVE CHECKROLL HK TEMP ************************************************
		
		}
		
		die('no_alert');
    }
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {
			$urutan = 0;
			
			$uniq_code_file = $this->_global->genFileName().'_MSCHECKROLLHK_'; //generate file name - hanya ada 1 file name
			
			// ************************************************ SAVE CHECKROLL HK ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_SAVE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			foreach ($rows as $key => $row) {
				$row['filename'] = $filename;
				$return = $this->_model->save($row);
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
			// ************************************************ SAVE CHECKROLL HK ************************************************
			
			//####################################### UPDATE INHERITANCE MODULE #######################################			
			// ************************************************ UPDATE NORMA CHECKROLL ************************************************
			$model = new Application_Model_NormaCheckroll();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NCHECKROLL';
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
			// ************************************************ UPDATE NORMA CHECKROLL ************************************************
			
			// ************************************************ SAVE RKT CHECKROLL & RKT CHECKROLL DETAIL ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTCHECKROLL';
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
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTCHECKROLLSUM';
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
			
			// ************************************************ SAVE RKT CHECKROLL DISTRIBUSI ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTCHECKROLLDIST';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$lastBa = "";
			$lastArrTr = array();
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
						$model->calDistribusiCheckroll($lastArrTr);
					}
					
					$lastBa = $record1['BA_CODE'];
					$lastArrTr = $record1;
				}
			}
			$model->calDistribusiCheckroll($lastArrTr);
			
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
			
			// ************************************************ UPDATE NORMA WRA ************************************************			
			$model = new Application_Model_NormaWra();
			
			//1. NORMA WRA 1
			$records1 = $this->_db->fetchAll("{$model->getData1($row)}");			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMAWRA1';
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
			
			//2. NORMA WRA 2
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMAWRA2';
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
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMAWRASUM';
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$lastBa = $lastPr ="";
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
						$uPar['filename'] = $filename;
						$uPar['BA_CODE'] = $lastBa;
						$uPar['PERIOD_BUDGET'] = $lastPr;
						$model->updateSummaryNormaWra($uPar);
					}
					$lastBa = $record1['BA_CODE'];
					$lastPr = $row['PERIOD_BUDGET'];
				}
			}
			$uPar['filename'] = $filename;
			$uPar['BA_CODE'] = $lastBa;
			$uPar['PERIOD_BUDGET'] = $lastPr;
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
			// ************************************************ END UPDATE NORMA WRA ************************************************
			
			// ************************************************ UPDATE RKT VRA ************************************************
			$model = new Application_Model_RktVra();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTVRA';
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
			// ************************************************ END UPDATE RKT VRA ************************************************
			
			// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTVRASUM';
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$par_sum['filename'] = $filename;
					$par_sum['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
					$par_sum['BA_CODE'] = $record1['BA_CODE'];
					$par_sum['VRA_CODE'] = $record1['VRA_CODE'];
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
			// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
			
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
			$model = new Application_Model_NormaDistribusiVraNonInfra();
			$updated_row = $this->_db->fetchAll("{$model->getChangedData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NDISTVRANONINFRA';
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
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NDISTVRANONINFRASUM';
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
			// ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
			
			// ************************************************ UPDATE OPEX RKT VRA ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTOPEXVRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($updated_row)) {
				foreach ($updated_row as $idx1 => $record1) {
					$par_opex_vra['filename'] = $filename;
					$par_opex_vra['key_find'] = $record1['BA_CODE'];
					$par_opex_vra['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
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
			// ************************************************ END UPDATE OPEX RKT VRA ************************************************
			
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			$model = new Application_Model_NormaPanenCostUnit();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPANENCOSTUNIT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			
			// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
			$model = new Application_Model_NormaPanenSupervisi();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPANENSPV';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			
			// ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
			$model = new Application_Model_NormaPanenKraniBuah();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPANENKRANIBUAH';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			// ************************************************ END UPDATE NORMA PANEN KRANI BUAH ************************************************
			
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			$model = new Application_Model_NormaPanenPremiLangsir();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPANENPREMILANGSIR';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPANENLOADING';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file			
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NBIAYA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
					//hitung total cost
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
			// ************************************************ END UPDATE NORMA BIAYA ************************************************
			
			// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
			$model = new Application_Model_NormaInfrastruktur();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NINFRASTRUKTUR';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
					//hitung total cost
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
			// ************************************************ END UPDATE NORMA INFRASTRUKTUR ************************************************
			
			// ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
			$model = new Application_Model_NormaPerkerasanJalan();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NPERKJALAN';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
					//hitung total cost
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
			// ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
			
			// ************************************************ UPDATE RKT LC ************************************************
			$model = new Application_Model_RktLc();
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
						
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTLC_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTLC_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
						
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
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
			$model = new Application_Model_RktManualNonInfra();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALNONINFRA_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALNONINFRA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
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
			
			// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
			$model = new Application_Model_RktManualNonInfraOpsi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALNONINFRAOPSI_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALNONINFRAOPSI_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
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
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALINFRA_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALINFRA_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			$lastAfd = ""; $lastActClass = ""; $lastActCode = ""; $lastLandType = ""; $lastTopo = ""; $lastBA = ""; $lastBiaya = ""; 
			$arrAfdUpds = array(); // variabel array data afd yang di modified	
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					$curAfd = $record1['AFD_CODE'];
					$curActClass = $record1['ACTIVITY_CLASS'];
					$curActCode = $record1['ACTIVITY_CODE'];
					$curLandType = $record1['LAND_TYPE'];
					$curTopo = $record1['TOPOGRAPHY'];
					$curBA = $record1['BA_CODE']; 
					$curBiaya = $record1['SUMBER_BIAYA']; 
					$curPeriod = $record1['PERIOD_BUDGET']; 
					
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTMANUALINFRA_DISTVRA';
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
				//edit $this->_model jd $model , karena functionnya gak ada
				//$this->_model->saveDistVra($arrAfdFix);
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
			// ************************************************ UPDATE RKT MANUAL INFRA************************************************	
			
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			$model = new Application_Model_RktTanamManual();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMMANUAL_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAMMANUAL_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAM_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTTANAM_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPANEN_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPANEN_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			$lastAfd = ""; $lastBA = ""; $lastBiaya = "";
			$arrAfdUpds = array(); // variabel array data afd yang di modified
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPANEN_DIST';
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
			// ************************************************ UPDATE RKT PANEN ************************************************
			
			// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
			$model = new Application_Model_RktPerkerasanJalan();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPERKJALAN_COSTELEMENT';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPERKJALAN_TOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			$lastAfd = ""; $lastBA = ""; $lastAct = "";
			$arrAfdUpds = array(); // variabel array data afd yang di modified
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
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
				array_push($arrAfdUpds, 
				   array('AFD_CODE'=>$lastAfd,
						 'BA_CODE'=>$lastBA,
						 'ACTIVITY_CODE'=>$lastAct,
						 'PERIOD_BUDGET'=>$lastPer)
				  );
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
						
			//2. HITUNG DISTRIBUSI VRA PER AFD
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPERKJALAN_DISTVRA';
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
			// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
			
			// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
			$model = new Application_Model_RktPupukDistribusiBiayaNormal();
			$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_PUPUK_NORMAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_PUPUK_SISIP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			$model = new Application_Model_RktPupukDistribusiBiayaGabungan();
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.str_pad($urutan,3,"0",STR_PAD_LEFT).'_PUPUK';
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
			
			// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
			
			//####################################### UPDATE INHERITANCE MODULE #######################################
        }
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CHECKROLL_HK";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CHECKROLL_HK";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_CHECKROLL_HK";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
