<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk RKT Manual - Infra
Function 			:	- listAction		: menampilkan list RKT Manual - Infra
						- saveAction		: save data
						- deleteAction		: hapus data
						- listInfoVraAction	: SID 15/07/2014	: menampilkan list info VRA
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/07/2013
Update Terakhir		:	15/07/2014
Revisi				:	
YUL 11/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class RktManualInfraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_RktManualInfra();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-manual-infra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT Manual - Infra';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT Manual - Infra
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//menampilkan list RKT Manual - Infra
    public function getActivityClassAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getActivityClass($params);
		
		die(json_encode($value));
    }
	
	public function getRotationAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getRotation($params);
		
		die(json_encode($value));
    }
	
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//mapping action
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0){
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['ROW_ID_TEMP']        	= $params['rowidtemp'][$key]; // ROW_ID_TEMP
				$rows[$key]['TRX_RKT_CODE']        	= $params['trxcode'][$key]; // KODE TRANSAKSI
				$rows[$key]['PERIOD_BUDGET']      	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['BA_CODE']      		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['AFD_CODE']  			= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE'] 			= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['MATURITY_STAGE_SMS1']  = $params['text09'][$key]; // MATURITY_STAGE_SMS1
				$rows[$key]['MATURITY_STAGE_SMS2']  = $params['text10'][$key]; // MATURITY_STAGE_SMS2
				$rows[$key]['ACTIVITY_CODE']      	= $params['text14'][$key]; // ACTIVITY_CODE
				$rows[$key]['ACTIVITY_CLASS']  		= $params['text16'][$key]; // ACTIVITY_CLASS
				$rows[$key]['SUMBER_BIAYA']  		= $params['text19'][$key]; // SUMBER_BIAYA
				$rows[$key]['PLAN_JAN']      		= $params['text20'][$key]; // PLAN_JAN
				$rows[$key]['PLAN_FEB']      		= $params['text21'][$key]; // PLAN_FEB
				$rows[$key]['PLAN_MAR']      		= $params['text22'][$key]; // PLAN_MAR
				$rows[$key]['PLAN_APR']      		= $params['text23'][$key]; // PLAN_APR
				$rows[$key]['PLAN_MAY']      		= $params['text24'][$key]; // PLAN_MAY
				$rows[$key]['PLAN_JUN']      		= $params['text25'][$key]; // PLAN_JUN
				$rows[$key]['PLAN_JUL']      		= $params['text26'][$key]; // PLAN_JUL
				$rows[$key]['PLAN_AUG']      		= $params['text27'][$key]; // PLAN_AUG
				$rows[$key]['PLAN_SEP']      		= $params['text28'][$key]; // PLAN_SEP
				$rows[$key]['PLAN_OCT']      		= $params['text29'][$key]; // PLAN_OCT
				$rows[$key]['PLAN_NOV']      		= $params['text30'][$key]; // PLAN_NOV
				$rows[$key]['PLAN_DEC']      		= $params['text31'][$key]; // PLAN_DEC
				$rows[$key]['PLAN_SETAHUN']      	= $params['text32'][$key]; // PLAN_SETAHUN
				$rows[$key]['TOTAL_RP_SMS1']      	= $params['text33'][$key]; // TOTAL_RP_SMS1
				$rows[$key]['TOTAL_RP_SMS2']      	= $params['text34'][$key]; // TOTAL_RP_SMS2
				$rows[$key]['COST_JAN']      		= $params['text35'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      		= $params['text36'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      		= $params['text37'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      		= $params['text38'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      		= $params['text39'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      		= $params['text40'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      		= $params['text41'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      		= $params['text42'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      		= $params['text43'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      		= $params['text44'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      		= $params['text45'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      		= $params['text46'][$key]; // COST_DEC
				$rows[$key]['TOTAL_RP_SETAHUN']     = $params['text47'][$key]; // TOTAL_RP_SETAHUN
				$rows[$key]['TIPE_NORMA']     		= $params['text48'][$key]; // TIPE_NORMA //<!-- TIPE NORMA -->
				$rows[$key]['LAND_TYPE'] 			= $params['hidden00'][$key]; // LAND_TYPE
				$rows[$key]['TOPOGRAPHY'] 			= $params['hidden01'][$key]; // TOPOGRAPHY
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction(); //print_r($rows); die();
		
		//die("SAVE!");
		
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
		
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$filename = '1'.$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			$row['filename'] = $filename;
            $this->_model->saveRotation($row);
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
				
		       
		//2. SIMPAN RKT INFRA
		//generate filename untuk .sh dan .sql
		$filename = '2'.$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung cost element labour
				$this->_model->calCostElement('LABOUR', $record1);
				//hitung cost element material
				$this->_model->calCostElement('MATERIAL', $record1);
				//hitung cost element tools
				$this->_model->calCostElement('TOOLS', $record1);
				//hitung cost element transport
				$this->_model->calCostElement('TRANSPORT', $record1);
				//hitung cost element contract
				$this->_model->calCostElement('CONTRACT', $record1);
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
		
		//3. SIMPAN TOTAL COST
		//generate filename untuk .sh dan .sql
		$filename = '3'.$this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
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
				$this->_model->calTotalCost($record1);
				
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
		
		/**
		 * Hitung distribusi pemakaian VRA
		 * deprecated by yaddi.surahman@tap-agri.co.id
		 * 
		// save Distribusi VRA per AFD
		$arrAfdFixs = array();
		$lastAfd = ""; $totalDistVraManInfra = 0; $totalHrgHMKM=0; $totalHrgInternal=0; $lastActCode="";

		//distinct data
		$tmp = array ();
		foreach ($arrAfdUpds as $row) {
			if (!in_array($row,$tmp)) array_push($tmp,$row);
		}
		
		foreach ($tmp as $key => $arrAfdUpd) { //disini harus di-akumulasi hasil perhitungan per-afdeling.
			$arrHitungDistVra = $this->_model->hitungDistVra($arrAfdUpd);
			$curAfd = $arrAfdUpd['AFD_CODE'];
			$curPeriod = $arrAfdUpd['PERIOD_BUDGET'];
			$curBA = $arrAfdUpd['BA_CODE'];
			$curActCode = $arrayrrAfdUpd['ACTIVITY_CODE'];
			
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
			$this->_model->saveDistVra($arrAfdFix);
		}		
		**/
		/**
		 * Hitung distribusi pemakaian VRA
		 * yaddi.surahman@tap-agri.co.id
		 */
		$params['filename'] = $filename;
		$this->_model->calculateDistribusiVRA($params);
		
		//execute transaksi
		$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
		shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
		$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
		
		//pindahkan .sql ke logs
		$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
		if ( ! is_dir($uploaddir)) {
			$oldumask = umask(0);
			mkdir("$uploaddir", 0777, true);
			chmod("/".date("Y-m-d"), 0777);
			umask($oldumask);
		}
		shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//save data temp
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		//1. SIMPAN INPUTAN TEMP
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
            if ($row['CHANGE']){
				$return = $this->_model->saveTemp($row);
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
		
		$data['return'] = "done";
		die(json_encode('data'));
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		
		$return = $this->_model->delete(base64_decode($params['rowid']));
		
		if ($return){			
			die('done');			
		}else{
			die("Data Tidak Berhasil Dihapus.");
		}
    }
	
	//<!-- TIPE NORMA -->
	//menampilkan list tipe norma
    public function getTipeNormaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getTipeNorma($params);
		
		die(json_encode($value));
    }
	
	//menampilkan list info VRA
    public function listInfoVraAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getListInfoVra($params);
        die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_MANUAL_INFRA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_MANUAL_INFRA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_MANUAL_INFRA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
