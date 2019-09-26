<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Panen Variabel
Function 			:	- listAction		: menampilkan list norma Panen Variabel
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/06/2013
Update Terakhir		:	04/05/2015
Revisi				:	
YULIUS 08/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction
						- add function mappingAction
						
YULIUS 10/07/2014	:	- modify module updateInheritModule()			
YULIUS 16/07/2014	:	- Tambah deklarasi variable utk inherit module		
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction						
NBU 04/05/2015		:	- Tambah update OER di line	219 & 312

=========================================================================================================================
*/ 
class NormaPanenVariabelController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaPanenVariabel();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-panen-variabel/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Panen Variabel';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma Panen Variabel
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
				$rows[$key]['ROW_ID']			= $params['text00'][$key]; // ROW_ID
				$rows[$key]['BA_CODE']			= $params['text03'][$key]; // BA_CODE
				$rows[$key]['PERIOD_BUDGET']	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['VALUE']        = $params['text06'][$key]; // VALUE
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['activity_code'] 	= $params['text04'][$key]; // ACTIVITY
				$rows[$key]['COST_ELEMENT'] 	= $params['text08'][$key]; // COST ELEMENT
				$rows[$key]['vra_code'] 		= $params['text11'][$key]; // VRA_CODE
				$rows[$key]['MATERIAL_CODE'] 	= $params['text11'][$key]; // MATERIAL_CODE
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName();
		$urutan=0;
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
						
			// ************************************************ SAVE NORMA PANEN VARIABLE TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.'_00_TNPANENVARI_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveTemp';
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
			// ************************************************ SAVE NORMA PANEN VARIABLE TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA PANEN VARIABLE ************************************************
		//generate filename untuk .sh dan .sql
		$urutan++;
		$filename = $uniq_code_file.'_00_TNPANENVARI_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_save';
		$this->_global->createBashFile($filename); //create bash file
		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
		//hitung distribusi infra seluruh halaman
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
		// ************************************************ SAVE ALL NORMA PANEN VARIABLE ************************************************
		
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		if (!empty($updated_row)) {
			$idxInherit = 1;
			foreach ($updated_row as $idx1 => $record1) {
				//update inherit module
				$record1['uniq_code_file'] 		= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
				$record1['key_find'] 			= $record1['BA_CODE']; // BA_CODE
				
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
		$updated_row_oer = $row;
		$updated_row_oer['PRE_OER'] = $row['VALUE'];
		if (!empty($row)) {			
			$urutan = 0;	
			
			// ************************************************ SAVE NORMA KRANI BUAH ************************************************
			$model = new Application_Model_NormaPanenKraniBuah();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file
			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			foreach ($rec as $key => $row) {
				$row['filename'] = $filename;
				$return = $model->save($row);
			}
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
			//pindahkan .sql ke logs
			$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
			if ( ! is_dir($uploaddir)) {
				$oldumask = umask(0);
				mkdir("$uploaddir", 0777, true);
				chmod("/".date("Y-m-d"), 0777);
				umask($oldumask);
			}
			shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
			// ************************************************ SAVE NORMA KRANI BUAH ************************************************
			
			// ************************************************ SAVE NORMA PANEN COST UNIT ************************************************
			$model = new Application_Model_NormaPanenCostUnit();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file
			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			foreach ($rec as $key => $row) {
				$row['filename'] = $filename;
				$return = $model->save($row);
			}
			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
			$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
			shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
			
			//pindahkan .sql ke logs
			$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
			if ( ! is_dir($uploaddir)) {
				$oldumask = umask(0);
				mkdir("$uploaddir", 0777, true);
				chmod("/".date("Y-m-d"), 0777);
				umask($oldumask);
			}
			shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
			// ************************************************ SAVE NORMA PANEN COST UNIT ************************************************
			
			// ************************************************ UPDATE RKT PANEN ************************************************
			$model = new Application_Model_RktPanen();	
			
			/*$row['activity_code'] = $row['ACTIVITY_CODE']; //activity yg dihitung ulang hanya yg mengalami perubahan
			$row['LAND_TYPE'] = ($row['LAND_TYPE'] == 'ALL') ? "" : $row['LAND_TYPE'];
			$row['TOPOGRAPHY'] = ($row['TOPOGRAPHY'] == 'ALL') ? "" : $row['TOPOGRAPHY'];
			$row['ACTIVITY_CLASS'] = ($row['ACTIVITY_CLASS'] == 'ALL') ? "" : $row['ACTIVITY_CLASS']; 
			$row['MATURITY_STATUS'] = ($row['ACTIVITY_GROUP'] == 'ALL') ? "" : $row['ACTIVITY_GROUP'];*/
			
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPANENVARI_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanen';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($rec)) {			
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
					$model->calCostElement('LABOUR', $record1);
					// YUS 01/09/2014 nambah cost element transport untuk update turunan ANGKUT_TBS_RP_KG_KM dari norma panen cost unit
					$model->calCostElement('TRANSPORT', $record1);
					
					$updated_row_oer['filename'] = $filename;
					//update master OER
					//tambahan aries 10.11.2016 (update hanya untuk PRE OER saja)
					if ($updated_row_oer['PANEN_CODE'] == 'OER_PANEN'){
						$model->saveOer($updated_row_oer);
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
			
			
			//2. HITUNG TOTAL COST
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPANENVARI_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanenTotalCost';
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
			$filename = $row['uniq_code_file'].'_TNPANENVARI_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanenSaveVRA';
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
		}
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA PANEN VARIABLE TEMP ************************************************
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
			// ************************************************ SAVE NORMA PANEN VARIABLE TEMP ************************************************
		
		}
		
		die('no_alert');
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PANEN_VARIABLE";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PANEN_VARIABLE";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PANEN_VARIABLE";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
