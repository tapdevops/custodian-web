<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Perkerasan Jalan
Function 			:	- listAction		: menampilkan list norma Perkerasan Jalan
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
YULIUS 23/07/2014	:	- penambahan pengecekan untuk lock table pada listAction
						- add function mappingAction
YULIUS 06/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction							
=========================================================================================================================
*/
class NormaPerkerasanJalanController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaPerkerasanJalan();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-perkerasan-jalan/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Perkerasan Jalan';
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
	
	//menampilkan list norma Perkerasan Jalan
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
            //if ($key > 0) {
			if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']				= $params['text00'][$key]; // ROW_ID
				$rows[$key]['PERIOD_BUDGET']		= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']				= $params['text03'][$key]; // BA_CODE
				$rows[$key]['ACTIVITY_CODE']		= $params['text04'][$key]; // ACTIVITY_CODE
				$rows[$key]['LEBAR']				= $params['text06'][$key]; // LEBAR
				$rows[$key]['PANJANG']        		= $params['text07'][$key]; // PANJANG
				$rows[$key]['TEBAL']        		= $params['text08'][$key]; // TEBAL
				$rows[$key]['VOLUME_MATERIAL']      = $params['text09'][$key]; // VOLUME_MATERIAL
				$rows[$key]['PRICE']        		= $params['text10'][$key]; // PRICE
				$rows[$key]['VRA_CODE_DT']        		= $params['text11'][$key]; // VRA_CODE_DT
				$rows[$key]['RP_KM_DT']        		= $params['text12'][$key]; // RP_KM_DT
				$rows[$key]['KAPASITAS_DT']        	= $params['text13'][$key]; // KAPASITAS_DT
				$rows[$key]['KECEPATAN_DT']  		= $params['text14'][$key]; // KECEPATAN_DT
				$rows[$key]['JAM_KERJA_DT']   		= $params['text15'][$key]; // JAM_KERJA_DT
				$rows[$key]['VRA_CODE_EXCAV']   		= $params['text16'][$key]; // VRA_CODE_EXCAV
				$rows[$key]['RP_HM_EXCAV']   		= $params['text17'][$key]; // RP_HM_EXCAV
				$rows[$key]['KAPASITAS_EXCAV']   	= $params['text18'][$key]; // KAPASITAS_EXCAV
				$rows[$key]['VRA_CODE_COMPACTOR']  			= $params['text19'][$key]; // VRA_CODE_COMPACTOR
				$rows[$key]['RP_HM_COMP']  			= $params['text20'][$key]; // RP_HM_COMP
				$rows[$key]['KAPASITAS_COMPACTOR']  = $params['text21'][$key]; // KAPASITAS_COMPACTOR
				$rows[$key]['VRA_CODE_GRADER']   		= $params['text22'][$key]; // VRA_CODE_GRADER
				$rows[$key]['RP_HM_GRADER']   		= $params['text23'][$key]; // RP_HM_GRADER
				$rows[$key]['KAPASITAS_GRADER']   	= $params['text24'][$key]; // KAPASITAS_GRADER
				
				// param inheritance
				$rows[$key]['key_find']				= $params['text03'][$key]; // BA_CODE
				$rows[$key]['activity_code']		= $params['text04'][$key]; // ACTIVITY_CODE
            }
        }
		return $rows;
	}
	
	//save data
	public function saveTempAction()
    {
       $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA PERKERASAN JALAN TEMP ************************************************
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
			// ************************************************ SAVE NORMA PERKERASAN JALAN TEMP ************************************************
		
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
			
			
			// ************************************************ SAVE NORMA PERKERASAN JALAN TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = "NORMA_PK_SVTEMP_".$this->_global->genFileName();
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
			// ************************************************ SAVE NORMA PERKERASAN JALAN TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA PERKERASAN JALAN ************************************************
		//generate filename untuk .sh dan .sql
		$filename = "NORMA_PK_SV_".$this->_global->genFileName();
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
		// ************************************************ SAVE ALL NORMA PERKERASAN JALAN ************************************************
		
		// ************************************************ SAVE ALL NORMA PERKERASAN JALAN HARGA ************************************************
		//generate filename untuk .sh dan .sql
		$filename = "NORMA_PK_SV_HARGA_".$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

		//hitung distribusi biaya seluruh halaman
		if (!empty($updated_row)) {
			foreach ($updated_row as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//update inherit module
				$this->_model->updTnHarga($record1);
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
		// ************************************************ SAVE ALL NORMA PERKERASAN JALAN HARGA ************************************************
		
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
			
			// ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
			$model = new Application_Model_RktPerkerasanJalan();
			$records1 = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPK_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPK_calCostElement';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
						
			if (!empty($records1)) {
				foreach ($records1 as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('MATERIAL', $record1);
					$model->calCostElement('TRANSPORT', $record1);
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPK_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPK_calTotalCost';
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
			$filename = $row['uniq_code_file'].'_TNPK_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKTPK_saveDist';
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
			
			// ************************************************ END UPDATE RKT PERKERASAN JALAN ************************************************
		}
	}
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PERKERASAN_JALAN";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PERKERASAN_JALAN";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_PERKERASAN_JALAN";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
	
	public function getValueVraAction(){
		$params = $this->_request->getPost();
		$data = $this->_model->getVraValue($params);
		if(empty($data['VALUE'])){
			$data_vra_pinjam = $this->_model->getVraPinjamValue($params);
			die(json_encode($data_vra_pinjam));
		}else{
			die(json_encode($data));
		}
		
	}
}
