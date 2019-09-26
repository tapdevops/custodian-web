<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Norma Biaya
Function 			:	- getStatusPeriodeAction		: BDJ 11/06/2013	: cek status periode budget yang dipilih
						- listAction					: SID 11/06/2013	: menampilkan list norma Biaya
						- mappingAction					: SID 11/06/2013	: mapping textfield name terhadap field name di DB
						- saveTempAction				: SID 23/06/2014	: simpan data sementara sesuai input user
						- saveAction					: SID 11/06/2013	: save data
						- updateInheritModule			: SID 23/06/2014	: update inherit module
						- deleteAction					: SID 11/06/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/06/2013
Update Terakhir		:	23/06/2014
Revisi				:	
	SID 23/06/2014	: 	- penambahan fungsi saveTempAction
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function deleteAction, saveAction & updateInheritModule
						- saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
						- calculateAllAction dihilangkan
						- penambahan pengecekan untuk lock table pada listAction, saveAction, deleteAction
	YUL 06/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction					
=========================================================================================================================
*/
class NormaBiayaController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaBiaya();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-biaya/main'); //$this->_redirect('/norma-harga-barang/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Biaya';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : YIR - 08/08/2014
    }

	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma Biaya
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
				$rows[$key]['BA_CODE']     		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['ACTIVITY_GROUP']   = $params['text04'][$key]; // ACTIVITY_GROUP
				$rows[$key]['ACTIVITY_CODE']    = $params['text05'][$key]; // ACTIVITY_CODE
				$rows[$key]['ACTIVITY_CLASS']   = $params['text07'][$key]; // ACTIVITY_CLASS
				$rows[$key]['LAND_TYPE']        = $params['text08'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY']       = $params['text09'][$key]; // TOPOGRAPHY
				$rows[$key]['COST_ELEMENT']     = $params['text10'][$key]; // COST_ELEMENT
				$rows[$key]['SUB_COST_ELEMENT'] = $params['text11'][$key]; // SUB_COST_ELEMENT
				$rows[$key]['QTY']        		= $params['text13'][$key]; // QTY
				$rows[$key]['ROTASI']        	= $params['text14'][$key]; // ROTASI
				$rows[$key]['VOLUME']        	= $params['text15'][$key]; // VOLUME
				$rows[$key]['PRICE']        	= $params['text17'][$key]; // PRICE
				
				$rows[$key]['QTY_SITE']        		= $params['text21'][$key]; // QTY
				$rows[$key]['ROTASI_SITE']        	= $params['text22'][$key]; // ROTASI
				$rows[$key]['VOLUME_SITE']        	= $params['text23'][$key]; // VOLUME
				$rows[$key]['PRICE_SITE']        	= $params['text25'][$key]; // PRICE
				
				$rows[$key]['PALM_AGE']        	= $params['text28'][$key]; // PALM
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['activity_code'] 	= $params['text05'][$key]; // BA_CODE
            }
        }
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA BIAYA TEMP ************************************************
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
			// ************************************************ SAVE NORMA BIAYA TEMP ************************************************
		
		}
		
		die('no_alert');
    }
	
	//save data
	public function saveAction()
    {
		$rows = $this->mappingAction();
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
			
			
			// ************************************************ SAVE NORMA BIAYA TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_TNBIAYA_01_SAVETEMP';
			$this->_global->createBashFile($filename); //create bash file
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
			// ************************************************ SAVE NORMA BIAYA TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA BIAYA ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_TNBIAYA_02_SV';
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
		// ************************************************ SAVE ALL NORMA BIAYA ************************************************
		
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
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		if (!empty($row)) {	
			$urutan = 0;	
			// LIST VARIABLE UNTUK KEBUTUHAN GETDATA INHERITANCE SESUAI DENGAN SPESIFIKASI KRITERIA PERHITUNGAN COST ELEMENT DI FORMULA TERKAIT TN_BIAYA
			$row['activity_code'] = $row['ACTIVITY_CODE']; //activity yg dihitung ulang hanya yg mengalami perubahan
			$row['LAND_TYPE'] = ($row['LAND_TYPE'] == 'ALL') ? "" : $row['LAND_TYPE'];
			$row['TOPOGRAPHY'] = ($row['TOPOGRAPHY'] == 'ALL') ? "" : $row['TOPOGRAPHY'];
			$row['ACTIVITY_CLASS'] = ($row['ACTIVITY_CLASS'] == 'ALL') ? "" : $row['ACTIVITY_CLASS']; 
			$row['MATURITY_STATUS'] = ($row['ACTIVITY_GROUP'] == 'ALL') ? "" : $row['ACTIVITY_GROUP'];
			
			// ************************************************ UPDATE RKT LC ************************************************
			$model = new Application_Model_RktLc();
			$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'LCCE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
					
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'LCCE_TotalCost';
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
			
			//1. HITUNG ROTASI OTOMATIS
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_ROTASI';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					$model->saveRotation($record1);
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			
			
			//3. HITUNG TOTAL COST
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_TOTAL';
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
			
			// ************************************************ UPDATE RKT KASTRASI SANITASI ************************************************
			$model = new Application_Model_RktKastrasiSanitasi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_KASTRASI_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_KASTRASI_TOTAL';
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
			
			// ************************************************ UPDATE RKT KASTRASI SANITASI ************************************************	
		
			// ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
			//untuk ambil aktivitas induk untuk RKT Manual Non Infra + Opsi
			$row['ACTIVITY_CODE_OPSI'] = $row['ACTIVITY_CODE']."','".substr($row['ACTIVITY_CODE'], 0, 5);
			
			$model = new Application_Model_RktManualNonInfraOpsi();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG ROTASI OTOMATIS
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_OPSI_ROTASI';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					$model->saveRotation($record1);
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_OPSI_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			
			
			//3. HITUNG TOTAL COST
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_NON_INFRA_OPSI_TOTAL';
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

			// ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
			$model = new Application_Model_RktTanam();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanam';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanam_TotalCost';
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
			
			// ************************************************ UPDATE RKT TANAM MANUAL ************************************************
			//mencegah filter maturity status ketika edit record di norma biaya. 
			//karena menghilangkan filter ini, RKT Tanam Manual hrs diletakkan paling bwh. JANGAN DITUKAR!
			$row['MATURITY_STATUS'] = ""; 
			
			$model = new Application_Model_RktTanamManual();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanamManual';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktTanamManual_TotalCost';
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
			
			//aries 2015-05-28
			// ************************************************ UPDATE RKT RAWAT SISIP ************************************************
			$model = new Application_Model_RktManualSisip();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG ROTASI OTOMATIS
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_SISIP';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					$model->saveRotation($record1);
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
			$filename = $row['uniq_code_file'].'_TNBIAYA_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RKT_MANUAL_SISIP_CE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						// jika yang diupdate norma labour, maka hitung seluruh cost element karena rotasi RKT mengikuti rotasi norma labour
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'TOOLS'){
						$model->calCostElement('TOOLS', $record1);
					}elseif($row['COST_ELEMENT'] == 'CONTRACT'){
						$model->calCostElement('CONTRACT', $record1);
					}
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
			// ************************************************ UPDATE RKT RAWAT SISIP ************************************************
		}
	}
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
		
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul		
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
		
		$par['ROW_ID'] = base64_decode($params['rowid']);
		$par['filename'] = $filename;
		$this->_model->delete($par); //hapus data
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
			foreach ($records1 as $idx1 => $record1) {
				//update inherit module
				$this->updateInheritModule($record1);
			}
		}		
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_BIAYA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_BIAYA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_BIAYA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
