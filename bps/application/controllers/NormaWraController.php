<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma WRA
Function 			:	- listAction		: menampilkan list norma WRA
						- saveAction		: save data
						- saveTempAction	: save temporary data (YIR - 140710)
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	12/06/2013
Update Terakhir		:	12/06/2013
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction		
=========================================================================================================================
*/
class NormaWraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaWra();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-wra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; Norma WRA';
		$this->view->period = date("Y", strtotime($this->_period));
		
		$grup = $this->_model->getWraGroup();
		$this->view->grupwracode = $grup['PARAMETER_VALUE_CODE'];
		$this->view->grupwra = $grup['PARAMETER_VALUE'];
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma WRA
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//menampilkan list norma WRA
    public function list1Action()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList1($params);
        die(json_encode($data));
    }
	
	//save data
	public function saveTempAction()
    {
		$params = $this->_request->getParams();
        $rows = array();
		$lastBa = "";
		
        foreach ($params['text100'] as $key => $val) {
            if (($key > 0) && ($params['tChange1'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange1'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text100'][$key]; // ROW ID
				$rows[$key]['BA_CODE']     		= $params['text103'][$key]; // BA_CODE
				$rows[$key]['GROUP_WRA_CODE']   = $params['text104'][$key]; // GROUP_WRA_CODE
				$rows[$key]['SUB_WRA_GROUP']   	= $params['text106'][$key]; // SUB_WRA_GROUP
				$rows[$key]['QTY_ROTASI']   	= $params['text108'][$key]; // QTY_ROTASI
				$rows[$key]['ROTASI_TAHUN']   	= $params['text109'][$key]; // ROTASI_TAHUN
				$rows[$key]['HARGA_INFLASI']   	= $params['text111'][$key]; // HARGA_INFLASI
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$total_row = $key;
            }
        }
		
		foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$new_key = $total_row + $key;
				$rows[$new_key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$new_key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$new_key]['BA_CODE']     		= $params['text03'][$key]; // BA_CODE
				$rows[$new_key]['GROUP_WRA_CODE']   = $params['text04'][$key]; // GROUP_WRA_CODE
				$rows[$new_key]['SUB_WRA_GROUP']   	= $params['text06'][$key]; // SUB_WRA_GROUP
				$rows[$new_key]['QTY_ROTASI']   	= $params['text08'][$key]; // QTY_ROTASI
				$rows[$new_key]['ROTASI_TAHUN']   	= $params['text09'][$key]; // ROTASI_TAHUN
				$rows[$new_key]['HARGA_INFLASI']   	= $params['text11'][$key]; // HARGA_INFLASI
				
				//deklarasi var utk inherit module
				$rows[$new_key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
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
		
		// 1. SIMPAN TEMP TN_WRA
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
	
	//save data
	public function saveAction()
    {
        $params = $this->_request->getParams();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $rows = array();
		$lastBa = "";
		
        foreach ($params['text100'] as $key => $val) {
            if (($key > 0) && ($params['tChange1'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange1'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text100'][$key]; // ROW ID
				$rows[$key]['BA_CODE']     		= $params['text103'][$key]; // BA_CODE
				$rows[$key]['GROUP_WRA_CODE']   = $params['text104'][$key]; // GROUP_WRA_CODE
				$rows[$key]['SUB_WRA_GROUP']   	= $params['text106'][$key]; // SUB_WRA_GROUP
				$rows[$key]['QTY_ROTASI']   	= $params['text108'][$key]; // QTY_ROTASI
				$rows[$key]['ROTASI_TAHUN']   	= $params['text109'][$key]; // ROTASI_TAHUN
				$rows[$key]['HARGA_INFLASI']   	= $params['text111'][$key]; // HARGA_INFLASI
				$rows[$key]['PERIOD_BUDGET']   	= $params['text102'][$key]; // PERIOD_BUDGET
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$total_row = $key;
            }
        }
		
		foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$new_key = $total_row + $key;
				$rows[$new_key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$new_key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$new_key]['BA_CODE']     		= $params['text03'][$key]; // BA_CODE
				$rows[$new_key]['GROUP_WRA_CODE']   = $params['text04'][$key]; // GROUP_WRA_CODE
				$rows[$new_key]['SUB_WRA_GROUP']   	= $params['text06'][$key]; // SUB_WRA_GROUP
				$rows[$new_key]['QTY_ROTASI']   	= $params['text08'][$key]; // QTY_ROTASI
				$rows[$new_key]['ROTASI_TAHUN']   	= $params['text09'][$key]; // ROTASI_TAHUN
				$rows[$new_key]['HARGA_INFLASI']   	= $params['text11'][$key]; // HARGA_INFLASI
				$rows[$new_key]['PERIOD_BUDGET']   	= $params['text02'][$key]; // PERIOD_BUDGET
				
				//deklarasi var utk inherit module
				$rows[$new_key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
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
		
		// 1. SIMPAN TEMPORARY TN_WRA
		$filename = $uniq_code_file.'_00_TNWRA_01_SAVETEMP';
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
		
		// 2. SIMPAN TN_WRA
		$filename = $uniq_code_file.'_00_TNWRA_02_SAVE';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost(); 
		
		//sub group 4
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				if($record1['FLAG_TEMP'] == 'Y'){
					$record1['filename'] = $filename;
					$this->_model->save($record1);
					
					$jml_vra['JML_VRA'] = 0;
					$jml_vra = $this->_model->checkVra($record1);
					if($jml_vra['JML_VRA'] > 0){
						//ditampung untuk inherit module
						$updated_row[] = $record1;
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
		
		$filename = $uniq_code_file.'_00_TNWRA_03_SAVE';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost(); 
		
		//sub group 1 - 3
		$records1 = $this->_db->fetchAll("{$this->_model->getData1($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				if($record1['FLAG_TEMP'] == 'Y'){
					$record1['filename'] = $filename;
					$this->_model->save($record1);
					
					$jml_vra['JML_VRA'] = 0;
					$jml_vra = $this->_model->checkVra($record1);
					if($jml_vra['JML_VRA'] > 0){
						//ditampung untuk inherit module
						$updated_row[] = $record1;
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
		
		//3. UPDATE SUMMARY WRA
		$filename = $uniq_code_file.'_00_TNWRA_03_SUM';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save norma dasar
		foreach ($rows as $key => $row) {			
			if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
				$uPar['filename'] = $filename;
				$uPar['BA_CODE'] = $lastBa;
				$uPar['PERIOD_BUDGET'] = $lastPr;
				$this->_model->updateSummaryNormaWra($uPar);	
			}
			$lastBa = $row['BA_CODE'];
			$lastPr = $row['PERIOD_BUDGET'];
		}
		$uPar['filename'] = $filename;
		$uPar['BA_CODE'] = $lastBa;		
		$uPar['PERIOD_BUDGET'] = $lastPr;
		$this->_model->updateSummaryNormaWra($uPar);
		
		//execute transaksi
		$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
		shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
		$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
		
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
		if (!empty($updated_row)) {
			$idxInherit = 1;
			foreach ($updated_row as $idx1 => $record1) {
				//update inherit module
				$record1['uniq_code_file'] 	= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
				
				$record1['ROW_ID'] = ""; //reset rowid agar tidak jadi filter ketika select data
				$this->updateInheritModule($record1);
				
				$idxInherit++;
			}
		}
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		if (!empty($row)) {	
			$urutan = 0;
			$row['key_find'] = $row['BA_CODE'];
			$row['budgetperiod'] = $row['PERIOD_BUDGET'];
			
			// ************************************************ UPDATE NORMA VRA ************************************************
			$model = new Application_Model_NormaVra();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");//die ($model->getData($row));
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NVRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$uniq_row['VRA_CODE'] = array();
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->save($record1);
					
					if (in_array($record1['VRA_CODE'], $uniq_row['VRA_CODE']) == false) {
						array_push($uniq_row['VRA_CODE'], $record1['VRA_CODE']);
					}
				}
			}
			$row['VRA_CODE'] = (count($uniq_row['VRA_CODE']) > 1) ? implode("','", $uniq_row['VRA_CODE']) : $uniq_row['VRA_CODE'][0];
			
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
			//pindahkan .sql ke logs
			$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
			if ( ! is_dir($uploaddir)) {
				$oldumask = umask(0);
				mkdir("$uploaddir", 0777, true);
				chmod("/".date("Y-m-d"), 0777);
				umask($oldumask);
			}
			shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
			
			// ************************************************ UPDATE NORMA VRA ************************************************
			
			// ************************************************ UPDATE RKT VRA ************************************************
			$model = new Application_Model_RktVra();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
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
			
			// ************************************************ UPDATE RKT VRA ************************************************
			
			// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRASUM';
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($uniq_row['VRA_CODE'])) {
				foreach ($uniq_row['VRA_CODE'] as $idx1 => $record1) {
					$par_sum['filename'] = $filename;
					$par_sum['BA_CODE'] = $row['BA_CODE'];
					$par_sum['VRA_CODE'] = $record1;
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'DISTVRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$uniq_row['ACT_CODE'] = array();
			if (!empty($updated_row)) {
				foreach ($updated_row as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					$model->updateRecord($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $uniq_row['ACT_CODE']) == false) {
						array_push($uniq_row['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$row['ACT_CODE'] = (count($uniq_row['ACT_CODE']) > 1) ? implode("','", $uniq_row['ACT_CODE']) : $uniq_row['ACT_CODE'][0];
			
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'DISTVRASUM';
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
			
			// ************************************************ UPDATE OPEX RKT VRA ************************************************
			$model = new Application_Model_RktOpexVra();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'OPEXVRA';
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
			$model = new Application_Model_NormaPanenCostUnit();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENCOSTUNIT';
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
			
			// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
			
			// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
			$model = new Application_Model_NormaPanenSupervisi();			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENSPV';
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
			
			// ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
			$model = new Application_Model_NormaPanenKraniBuah();			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENKRANIBUAH';
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
			
			// ************************************************ UPDATE NORMA PANEN KRANI BUAH ************************************************
			
			// ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
			$model = new Application_Model_NormaPanenPremiLangsir();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENPREMILANGSIR';
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
			
			// ************************************************ UPDATE NORMA BIAYA ************************************************
			$model = new Application_Model_NormaBiaya();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NBIAYA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->save($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $uniq_row['ACT_CODE']) == false) {
						array_push($uniq_row['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$row['ACT_CODE'] = (count($uniq_row['ACT_CODE']) > 1) ? implode("','", $uniq_row['ACT_CODE']) : $uniq_row['ACT_CODE'][0];
			
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
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
			
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NINFRA';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->save($record1);
					
					//distinct activity
					if (in_array($record1['ACTIVITY_CODE'], $uniq_row['ACT_CODE']) == false) {
						array_push($uniq_row['ACT_CODE'], $record1['ACTIVITY_CODE']);
					}
				}
			}
			$row['ACT_CODE'] = (count($uniq_row['ACT_CODE']) > 1) ? implode("','", $uniq_row['ACT_CODE']) : $uniq_row['ACT_CODE'][0];
			
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
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
			if($row['MATERIAL_CODE'] == '202090031'){
				$model = new Application_Model_NormaPerkerasanJalan();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPERKJALAN';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->save($record1);
						
						//distinct activity
						if (in_array($record1['ACTIVITY_CODE'], $uniq_row['ACT_CODE']) == false) {
							array_push($uniq_row['ACT_CODE'], $record1['ACTIVITY_CODE']);
						}
					}
				}
				$row['ACT_CODE'] = (count($uniq_row['ACT_CODE']) > 1) ? implode("','", $uniq_row['ACT_CODE']) : $uniq_row['ACT_CODE'][0];
				
				//execute transaksi
				$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
				shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
				$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
				shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
				
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
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung total cost
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
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRACE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRATOTAL';
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
			
			// ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. SAVE RKT MANUAL NON INFRA
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASICE';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTKASTRASITOTAL';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSICE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSITOTAL';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRACE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRATOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			//hitung distribusi biaya seluruh halaman
			$params = $this->_request->getPost();
			$rec = $this->_db->fetchAll("{$model->getData($params)}");
			$lastAfd = ""; $lastActClass = ""; $lastActCode = ""; $lastLandType = ""; $lastTopo = ""; $lastBA = ""; $lastBiaya = ""; 
			$arrAfdUpds = array(); // variabel array data afd yang di modified
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
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
		
		
		//4. HITUNG DISTRIBUSI VRA
		//generate filename untuk .sh dan .sql
		$filename = '4'.$this->_global->genFileName();
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
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************

			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			$model = new Application_Model_RktTanamManual();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALTOTAL';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISTOTAL';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//hitung distribusi biaya seluruh halaman
			$params = $this->_request->getPost();
			$rec = $this->_db->fetchAll("{$model->getData($params)}");
			$lastAfd = ""; $lastBA = ""; $lastAct = "";
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'DIST_VRA';
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANTOTAL';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			//hitung distribusi biaya seluruh halaman
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
			$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPK_saveDist';
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
			
			//jika aktivitas TABUR PUPUK, UNTIL PUPUK baru hitung RKT Pupuk
			if ( (strpos($row['ACT_CODE'], '43750')  !== false ) || (strpos($row['ACT_CODE'], '43760') !== false ) ){
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
				$model = new Application_Model_RktPupukDistribusiBiayaNormal();
				$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKNORMAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
				
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
				$model = new Application_Model_RktPupukDistribusiBiayaSisip();
				$records1 = $this->_db->fetchAll("{$model->getInheritData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKSISIP';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
				
				// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_TNWRA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKTOTAL';
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
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul		
		$lock = $this->_global->checkLockTable($params);		
		if($lock['JUMLAH']){
			$data['return'] = "locked";
			$data['module'] = $lock['MODULE'];
			$data['insert_user'] = $lock['INSERT_USER'];
			die(json_encode($data));
		}
		
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_TNWRA_01_DELETE';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$par['ROW_ID'] = base64_decode($params['rowid']);
		$par['filename'] = $filename;
		$par['BA_CODE'] = base64_decode($params['bacode']);
		$return = $this->_model->delete($par); //hapus data
		
		if ($return){
			$this->_model->updateSummaryNormaWra($par);
		}
		
		//get data untuk dihapus
		$records1 = $this->_db->fetchAll("{$this->_model->getData($par)}");
		
		//execute transaksi
		$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
		shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
		$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
		
		//pindahkan .sql ke logs
		$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
		if ( ! is_dir($uploaddir)) {
			$oldumask = umask(0);
			mkdir("$uploaddir", 0777, true);
			chmod("/".date("Y-m-d"), 0777);
			umask($oldumask);
		}
		shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
		
		
		if (!empty($records1)) {
			$idxInherit = 1;
			foreach ($records1 as $idx1 => $record1) {
				//update inherit module
				$record1['uniq_code_file'] 	= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
				
				$record1['ROW_ID'] = ""; //reset rowid agar tidak jadi filter ketika select data
				$this->updateInheritModule($record1);
				
				$idxInherit++;
			}
		}
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_WRA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_WRA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_WRA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
