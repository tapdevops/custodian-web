<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Norma Infrastruktur
Function 			:	- getStatusPeriodeAction		: BDJ 22/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 11/06/2013	: menampilkan list norma Infrastruktur
						- mappingAction					: SID 11/06/2013	: mapping textfield name terhadap field name di DB
						- saveTempAction				: SID 11/06/2013	: simpan data sementara sesuai input user
						- saveAction					: SID 11/06/2013	: save data
						- updateInheritModule			: SID 01/07/2014	: update inherit module
						- deleteAction					: SID 11/06/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/06/2013
Update Terakhir		:	01/07/2014
Revisi				:	
	SID 01/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction, deleteAction
	YUL 06/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class NormaInfrastrukturController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaInfrastruktur();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-infrastruktur/main');
    }
	
	public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Infrastruktur';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma Infrastruktur
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
				$rows[$key]['PERIOD_BUDGET']	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']			= $params['text03'][$key]; // BA_CODE
				$rows[$key]['QTY_INFRA']		= $params['text14'][$key]; // QTY_INFRA
				$rows[$key]['QTY']        		= $params['text15'][$key]; // QTY
				$rows[$key]['ROTASI']        	= $params['text16'][$key]; // ROTASI
				$rows[$key]['VOLUME']        	= $params['text17'][$key]; // VOLUME
				$rows[$key]['QTY_HA']        	= $params['text18'][$key]; // QTY_HA
				$rows[$key]['PRICE']  			= $params['text19'][$key]; // PRICE
				$rows[$key]['RP_HA_EXTERNAL']   = $params['text20'][$key]; // RP_HA_EXTERNAL
				$rows[$key]['HARGA_INTERNAL']   = $params['text21'][$key]; // HARGA_INTERNAL
				$rows[$key]['RP_QTY_INTERNAL']  = $params['text22'][$key]; // RP_QTY_INTERNAL
				$rows[$key]['RP_HA_INTERNAL']   = $params['text23'][$key]; // RP_HA_INTERNAL
				
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
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA INFRASTRUKTUR TEMP ************************************************
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
			// ************************************************ SAVE NORMA INFRASTRUKTUR TEMP ************************************************
		
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
						
			// ************************************************ SAVE NORMA INFRASTRUKTUR TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_NINFRA_01_SAVETEMP';
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
			// ************************************************ SAVE NORMA INFRASTRUKTUR TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA INFRASTRUKTUR ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NINFRA_02_SAVE';
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
		// ************************************************ SAVE ALL NORMA INFRASTRUKTUR ************************************************
		
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		if (!empty($updated_row)) {
			$idxInherit = 1;
			foreach ($updated_row as $idx1 => $record1) {
				//deklarasi var utk inherit module
				$record1['key_find'] 			= $record1['BA_CODE']; // BA_CODE
				$record1['activity_code'] 		= $record1['ACTIVITY_CODE']; // ACTIVITY_CODE
				$record1['ACTIVITY_CLASS'] 		= ($record1['ACTIVITY_CLASS'] == 'ALL') ? "" : $record1['ACTIVITY_CLASS']; 	// ACTIVITY_CLASS
				$record1['LAND_TYPE'] 			= ($record1['LAND_TYPE'] == 'ALL') ? "" : $record1['LAND_TYPE']; //LAND_TYPE
				$record1['TOPOGRAPHY'] 			= ($record1['TOPOGRAPHY'] == 'ALL') ? "" : $record1['TOPOGRAPHY'];	//TOPOGRAPHY
				$record1['land_type'] 			= ($record1['LAND_TYPE'] == 'ALL') ? "" : $record1['LAND_TYPE']; //LAND_TYPE
				$record1['topo'] 				= ($record1['TOPOGRAPHY'] == 'ALL') ? "" : $record1['TOPOGRAPHY'];	//TOPOGRAPHY
				$record1['uniq_code_file'] 		= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename		
				
				//update inherit module
				$this->updateInheritModule($record1);
					
				$idxInherit++;
			}
		}
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
		$params['key_find'] = $params['BA_CODE'];
		$lock = $this->_global->checkLockTable($params);		
		if($lock['JUMLAH']){
			$data['return'] = "locked";
			$data['module'] = $lock['MODULE'];
			$data['insert_user'] = $lock['INSERT_USER'];
			die(json_encode($data));
		}
		
		// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_NINFRA_01_DELETE';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
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
		// ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		$params['filename'] = $uniq_code_file.'_00_NINFRA_02_DELETE_INHERIT_DATA';
		$params['key_find'] 			= $params['BA_CODE']; // BA_CODE
		$params['activity_code'] 		= $params['ACTIVITY_CODE']; // ACTIVITY_CODE
		$params['land_type'] 			= $params['LAND_TYPE']; //LAND_TYPE
		$params['topo'] 				= $params['TOPOGRAPHY'];	//TOPOGRAPHY
		$this->updateInheritModule($params);
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		if (!empty($row)) {		
			// ************************************************ UPDATE RKT MANUAL INFRA ************************************************
			$model = new Application_Model_RktManualInfra();
			$rec = $this->_db->fetchAll("{$model->getData($row)}");
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NINFRA_01_RKTMANUALINFRACE';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					
					//hitung cost element
					if($row['COST_ELEMENT'] == 'LABOUR'){
						$model->calCostElement('LABOUR', $record1);
					}elseif($row['COST_ELEMENT'] == 'MATERIAL'){
						$model->calCostElement('MATERIAL', $record1);
						$model->calCostElement('TOOLS', $record1);
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
			$filename = $row['uniq_code_file'].'_NINFRA_02_RKTMANUALINFRATOTAL';
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
		
		
			//3. HITUNG DISTRIBUSI VRA
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NINFRA_03_RKTMANUALINFRASVDIST';
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
			
			// ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
			$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
			$rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
			
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NINFRA_04_RKTPUPUKBIAYANORMAL';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->calCostElement('LABOUR', $record1);
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
			$rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
			
			//generate filename untuk .sh dan .sql
			$filename = $row['uniq_code_file'].'_NINFRA_05_RKTPUPUKBIAYASISIP';
			$this->_global->createBashFile($filename); //create bash file		
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					$model->calCostElement('LABOUR', $record1);
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
			$filename = $row['uniq_code_file'].'_NINFRA_06_RKTPUPUKBIAYA';
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
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_INFRASTRUKTUR";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_INFRASTRUKTUR";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TN_INFRASTRUKTUR";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
