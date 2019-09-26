<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Standar Jam Kerja
Function 			:	- listAction		: menampilkan list Standar Jam Kerja
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class StandarJamKerjaController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_StandarJamKerja();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/standar-jam-kerja/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Master Standar Jam Kerja';
		$this->_helper->layout->setLayout('detail');
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Standar Jam Kerja
    public function listAction()
    {
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//simpan temporary data
	public function saveTempAction()
    {
        //$rows = $this->mappingAction();
		$params = $this->_request->getParams();
        $rows = array();
		$row_err = array();
		$row_success = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']    = $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']        	= $params['text03'][$key]; // BA_CODE
				$rows[$key]['JAM_KERJA']   		= $params['text04'][$key]; // JAM_KERJA
				
				//deklarasi var utk inherit module
				$rows[$key]['budgetperiod'] 	= $params['text02'][$key]; // BA_CODE
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
            }
        }
		
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
		$uniq_code_file = $this->_global->genFileName().'_TMSTD_JAMKER_'; //generate file name - hanya ada 1 file name
        $params = $this->_request->getParams();
        $rows = array();
		$row_err = array();
		$row_success = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']    = $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']        	= $params['text03'][$key]; // BA_CODE
				$rows[$key]['JAM_KERJA']   		= $params['text04'][$key]; // JAM_KERJA
				
				//deklarasi var utk inherit module
				$rows[$key]['budgetperiod'] 	= $params['text02'][$key]; // BA_CODE
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
            }
        }
		
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
			
			// ************************************************ SAVE MASTER STANDAR JAM KERJA TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_01_SVTEMP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save master standar jam kerja temp
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
			// ************************************************ SAVE MASTER STANDAR JAM KERJA TEMP ************************************************
		}
		
		// ************************************************ UPDATE STANDAR JAM KERJA ************************************************
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
			// ************************************************ UPDATE STANDAR JAM KERJA ************************************************
			
       
		//foreach ($rows as $key => $row) {
			//$return = $this->_model->save($row);
			
			//if (!$return){
				//$row_err[] = $key;
			//}else{
				//$row_success[] = $key;
			
			$row = array();
			if(!empty($updated_row)){
			$row['BA_CODE'] = $updated_row[0]['BA_CODE'];
			$row['PERIOD_BUDGET'] = $updated_row[0]['PERIOD_BUDGET'];
				//####################################### UPDATE INHERITANCE MODULE #######################################
				$row['uniq_code_file'] = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
				$urutan=0;
				// ************************************************ UPDATE NORMA WRA ************************************************
				$model = new Application_Model_NormaWra();
				$rec = $this->_db->fetchAll("{$model->getData1($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NWRA';
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
				
				// ************************************************ UPDATE NORMA WRA ************************************************
				
				// ************************************************ UPDATE SUMMARY NORMA WRA ************************************************
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NWRASUM';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$lastBa = "";
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						if(($lastBa) && ($lastBa <> $record1['BA_CODE'])){
							$uPar['filename'] = $filename;
							$uPar['BA_CODE'] = $lastBa;
							$uPar['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
							$model->updateSummaryNormaWra($uPar);
						}				
						$lastBa = $record1['BA_CODE'];
					}
					/*$uPar['filename'] = $filename;
					$uPar['BA_CODE'] = $lastBa;
					$uPar['PERIOD_BUDGET'] = $record1['PERIOD_BUDGET'];
					$model->updateSummaryNormaWra($uPar);*/
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
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
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
				
				// ************************************************ UPDATE SUMMARY NORMA DISTRIBUSI VRA ************************************************
				$model = new Application_Model_RktVra();
				$model_dist = new Application_Model_NormaDistribusiVraNonInfra();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						// DISTRIBUSI VRA - NON INFRA
						$model_dist->updateRecord($record1);
						$model_dist->updateSummaryNormaDistribusiVra($record1);
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
				
				// ************************************************ UPDATE SUMMARY NORMA DISTRIBUSI VRA ************************************************
				
				// ************************************************ UPDATE SUMMARY RKT VRA ************************************************
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTVRASUM';
				$this->_global->createBashFile($filename); //create bash file
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						$model->updateSummaryRktVra($record1);
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'OPEXVRA';
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
				
				$check = strpos($row['vra_code'], 'DT010');
				if ($check !== false) {
				// ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
					$model = new Application_Model_NormaPanenCostUnit();
					$rec = $this->_db->fetchAll("{$model->getData($row)}");
					
					//generate filename untuk .sh dan .sql
					$urutan++;
					$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENCOSTUNIT';
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
				}
				
				// ************************************************ UPDATE NORMA PANEN SUPERVISI ************************************************
				$model = new Application_Model_NormaPanenSupervisi();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENSUPERVISI';
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENKRANIBUAH';
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPANENPREMILANGSIR';
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NBIAYA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
						$record1['filename'] = $filename;
						$model->save($record1);
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NINFRA';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
						$record1['filename'] = $filename;
						$model->save($record1);
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
				$model = new Application_Model_NormaPerkerasanJalan();
				$rec = $this->_db->fetchAll("{$model->getData($row)}");
				
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'NPERKJALAN';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
						$record1['filename'] = $filename;
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
				
				// ************************************************ UPDATE RKT LC ************************************************
				$model = new Application_Model_RktLc();
				$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
						$record1['filename'] = $filename;
						
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTLCTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRACE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRATOTAL';
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
				
				// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
				$model = new Application_Model_RktManualNonInfraOpsi();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSICE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANNONINFRAOPSITOTAL';
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
				
				// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************	
				
				// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
				$model = new Application_Model_RktManualInfra();	
				$rec = $this->_db->fetchAll("{$model->getData($row)}");	
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRACE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$record1['filename'] = $filename;
						
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTMANINFRATOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//hitung distribusi biaya seluruh halaman
				$params = $this->_request->getPost();
				$records1 = $this->_db->fetchAll("{$model->getData($params)}");
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
				
				//execute transaksi
				$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
				shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
				$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
				shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
				
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMMANUALTOTAL';
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

				// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
				
				// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
				$model = new Application_Model_RktTanam();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTTANAMOTOMATISTOTAL';
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
				
				// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
				
				// ************************************************ UPDATE RKT PANEN ************************************************
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				
				//1. HITUNG PER COST ELEMENT
				//generate filename untuk .sh dan .sql
				$urutan++;
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPANENTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				//hitung distribusi biaya seluruh halaman
				$params = $this->_request->getPost();
				$rec = $this->_db->fetchAll("{$model->getData($params)}");
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'DIST_VRA';
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANCE';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
							
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANTOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
					
				//hitung distribusi biaya seluruh halaman
				$params = $this->_request->getPost();
				$rec = $this->_db->fetchAll("{$model->getData($params)}");
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
				$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTPERKJALANTOTALSVDIST';
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
					$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKNORMAL';
					$this->_global->createBashFile($filename); //create bash file			
					$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
								
					if (!empty($records1)) {
						foreach ($records1 as $idx1 => $record1) {
							$record1['filename'] = $filename;
							//hitung cost element
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
					$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKSISIP';
					$this->_global->createBashFile($filename); //create bash file			
					$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
								
					if (!empty($records1)) {
						foreach ($records1 as $idx1 => $record1) {
							$record1['filename'] = $filename;
							//hitung cost element
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
					$filename = $row['uniq_code_file'].'_STDJAMKERJA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKTBIAYAPUPUKTOTAL';
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
				
				//####################################### UPDATE INHERITANCE MODULE #######################################
			}
        
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_STANDART_JAM_KERJA_WRA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_STANDART_JAM_KERJA_WRA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_STANDART_JAM_KERJA_WRA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
