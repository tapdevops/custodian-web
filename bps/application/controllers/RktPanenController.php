<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk RKT Panen
Function 			:	- getStatusPeriodeAction		: BDJ 23/07/2013	: cek status periode budget yang dipilih
						- listAction					: YIR 05/08/2013	: menampilkan list RKT Panen
						- getSumberBiayaAction			: YIR 05/08/2013	: get sumber biaya
						- mappingAction					: YIR 05/08/2013	: mapping textfield name terhadap field name di DB
						- saveAction					: YIR 05/08/2013	: save data
						- saveTempAction				: YIR 05/08/2013	: simpan data sementara sesuai input user
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	05/08/2013
Update Terakhir		:	12/07/2014
Revisi				:	
	SID 12/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction
	YUL 11/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class RktPanenController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_RktPanen();
		$this->_formula = new Application_Model_Formula();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-panen/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT Panen';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT Panen
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//get sumber biaya
	public function getSumberBiayaAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_model->getSumberBiaya($params);
		
		die(json_encode($value));
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//mapping textfield name terhadap field name di DB	
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && (($params['text06'][$key] != "") || ($params['text08'][$key] != "") || ($params['tChange'][$key] != ""))){
				$rows[$key]['PRE_OER']        					= $params['OERvalue']; // CHANGE 
				$rows[$key]['TRX_RKT_CODE']        				= $params['trxcode'][$key]; //TRX_RKT_CODE
				$rows[$key]['CHANGE']        					= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        					= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_CODE']        				= $params['text02'][$key]; // PERIOD_CODE
				$rows[$key]['BA_CODE']      					= $params['text03'][$key]; // BA_CODE
				$rows[$key]['AFD_CODE']      					= $params['text04'][$key]; // AFD_CODE
				$rows[$key]['BLOCK_CODE']  						= $params['text05'][$key]; // BLOCK_CODE
				$rows[$key]['TON'] 								= $params['text06'][$key]; // TON
				$rows[$key]['JANJANG']        					= $params['text07'][$key]; // JANJANG
				$rows[$key]['BJR_AFD'] 							= $params['text08'][$key]; // BJR_AFD
				$rows[$key]['JARAK_PKS']      					= $params['text09'][$key]; // JARAK_PKS
				$rows[$key]['SUMBER_BIAYA']    					= $params['text10'][$key]; // SUMBER_BIAYA
				$rows[$key]['PERSEN_LANGSIR']   				= $params['text11'][$key]; // PERSEN_LANGSIR
				
				$rows[$key]['BIAYA_PEMANEN_HK']					= $params['text12'][$key]; // BIAYA_PEMANEN_HK
				$rows[$key]['BIAYA_PEMANEN_RP_BASIS']  			= $params['text13'][$key]; // BIAYA_PEMANEN_RP_BASIS
				$rows[$key]['BIAYA_PEMANEN_RP_PREMI_JANJANG']   = $params['text14'][$key]; // BIAYA_PEMANEN_RP_PREMI_JANJANG
				$rows[$key]['BIAYA_PEMANEN_RP_PREMI_BRD']   	= $params['text53'][$key]; // BIAYA_PEMANEN_RP_PREMI_BRD
				$rows[$key]['BIAYA_PEMANEN_RP_TOTAL']  			= $params['text15'][$key]; // BIAYA_PEMANEN_RP_TOTAL
				$rows[$key]['BIAYA_PEMANEN_RP_KG']     			= $params['text16'][$key]; // BIAYA_PEMANEN_RP_KG
				$rows[$key]['BIAYA_SPV_RP_BASIS']      			= $params['text17'][$key]; // BIAYA_SPV_RP_BASIS
				$rows[$key]['BIAYA_SPV_RP_PREMI']      			= $params['text18'][$key]; // BIAYA_SPV_RP_PREMI
				$rows[$key]['BIAYA_SPV_RP_TOTAL']      			= $params['text19'][$key]; // BIAYA_SPV_RP_TOTAL
				$rows[$key]['BIAYA_SPV_RP_KG']      			= $params['text20'][$key]; // BIAYA_SPV_RP_KG
				$rows[$key]['BIAYA_ALAT_PANEN_RP_KG']   		= $params['text21'][$key]; // BIAYA_ALAT_PANEN_RP_KG
				$rows[$key]['BIAYA_ALAT_PANEN_RP_TOTAL']		= $params['text22'][$key]; // BIAYA_ALAT_PANEN_RP_TOTAL
				$rows[$key]['TUKANG_MUAT_BASIS']      			= $params['text23'][$key]; // TUKANG_MUAT_BASIS
				$rows[$key]['TUKANG_MUAT_PREMI']      			= $params['text24'][$key]; // TUKANG_MUAT_PREMI
				
				$rows[$key]['TUKANG_MUAT_TOTAL']      			= $params['text25'][$key]; // TUKANG_MUAT_TOTAL
				$rows[$key]['TUKANG_MUAT_RP_KG']      			= $params['text26'][$key]; // TUKANG_MUAT_RP_KG
				$rows[$key]['SUPIR_PREMI']      				= $params['text27'][$key]; // SUPIR_PREMI
				$rows[$key]['SUPIR_RP_KG']      				= $params['text28'][$key]; // SUPIR_RP_KG
				$rows[$key]['ANGKUT_TBS_RP_KG_KM']     		 	= $params['text29'][$key]; // ANGKUT_TBS_RP_KG_KM
				$rows[$key]['ANGKUT_TBS_RP_ANGKUT']   			= $params['text30'][$key]; // ANGKUT_TBS_RP_ANGKUT
				$rows[$key]['ANGKUT_TBS_RP_KG']      			= $params['text31'][$key]; // ANGKUT_TBS_RP_KG
				$rows[$key]['KRANI_BUAH_BASIS']      			= $params['text32'][$key]; // KRANI_BUAH_BASIS
				$rows[$key]['KRANI_BUAH_PREMI']      			= $params['text33'][$key]; // KRANI_BUAH_PREMI
				$rows[$key]['KRANI_BUAH_TOTAL']      			= $params['text34'][$key]; // KRANI_BUAH_TOTAL
				$rows[$key]['KRANI_BUAH_RP_KG']      			= $params['text35'][$key]; // KRANI_BUAH_RP_KG
				$rows[$key]['LANGSIR_TON']      				= $params['text36'][$key]; // LANGSIR_TON
				$rows[$key]['LANGSIR_RP']      					= $params['text37'][$key]; // LANGSIR_RP
				$rows[$key]['LANGSIR_TUKANG_MUAT']      		= $params['text54'][$key]; // LANGSIR_TUKANG_MUAT
				$rows[$key]['LANGSIR_RP_KG']      				= $params['text38'][$key]; // LANGSIR_RP_KG
				$rows[$key]['TOTAL_BIAYA']      				= $params['text39'][$key]; // TOTAL_BIAYA
				
				$rows[$key]['COST_JAN']      					= $params['text40'][$key]; // COST_JAN
				$rows[$key]['COST_FEB']      					= $params['text41'][$key]; // COST_FEB
				$rows[$key]['COST_MAR']      					= $params['text42'][$key]; // COST_MAR
				$rows[$key]['COST_APR']      					= $params['text43'][$key]; // COST_APR
				$rows[$key]['COST_MAY']      					= $params['text44'][$key]; // COST_MAY
				$rows[$key]['COST_JUN']      					= $params['text45'][$key]; // COST_JUN
				$rows[$key]['COST_JUL']      					= $params['text46'][$key]; // COST_JUL
				$rows[$key]['COST_AUG']      					= $params['text47'][$key]; // COST_AUG
				$rows[$key]['COST_SEP']      					= $params['text48'][$key]; // COST_SEP
				$rows[$key]['COST_OCT']      					= $params['text49'][$key]; // COST_OCT
				$rows[$key]['COST_NOV']      					= $params['text50'][$key]; // COST_NOV
				$rows[$key]['COST_DEC']      					= $params['text51'][$key]; // COST_DEC
				$rows[$key]['COST_SETAHUN']     				= $params['text52'][$key]; // COST_SETAHUN
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
		$rows = $this->mappingAction(); // simpan 1 halaman input 
		$uniq_code_file = $this->_global->genFileName();
		
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
		
		//TAMBAHAN SAB : 10/09/2013 - SIMPAN OER YANG DIINPUT
		$params = $this->_request->getParams();
		$arr['BA_CODE'] = $params['key_find'];
		$arr['PRE_OER'] = $params['OERvalue'];
		$arr['PERIOD_BUDGET'] = $params['budgetperiod'];
		$urutan=0;
		//1. SIMPAN INPUTAN USER
		//generate filename untuk .sh dan .sql
		$urutan++;
		$filename = $uniq_code_file.'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveOer_Rotation';
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$arr['filename'] = $filename;
		$this->_model->saveOer($arr);
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$return = $this->_model->saveRotation($row);
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
		$filename = $this->_global->genFileName()."0$urutan_PANEN_calCostElement";
		$filename = $uniq_code_file.'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_calCostElement';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		
		//print_r($records1);die();
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
		
		//3. HITUNG TOTAL COST
		//generate filename untuk .sh dan .sql
		$urutan++;
		$filename = $uniq_code_file.'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_calTotalCost';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung distribusi biaya seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
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
				$this->_model->calTotalCost($record1);
				
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
		
		//4. HITUNG DISTRIBUSI VRA PER AFD
		//generate filename untuk .sh dan .sql
		$urutan++;
		$filename = $uniq_code_file.'_00_RKTPANEN_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveDistVra';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		foreach ($arrAfdUpds as $key => $arrAfdUpd) {
			$arrAfdUpd['filename'] = $filename;
			$return = $this->_model->saveDistVra($arrAfdUpd);
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
		die(json_encode($data));
    }
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
		//TAMBAHAN SAB : 10/09/2013 - SIMPAN OER YANG DIINPUT
		$params = $this->_request->getParams();
		
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$arr['BA_CODE'] = $params['key_find'];
		$arr['PRE_OER'] = $params['OERvalue'];
		$arr['filename'] = $filename;
		$this->_model->saveOer($arr);
		
        $rows = $this->mappingAction();
		$row_err = array();
		$row_success = array();
       
		foreach ($rows as $key => $row) {
            if ($row['CHANGE']){
				$row['filename'] = $filename;
				$this->_model->saveRotation($row);
				$this->_model->saveTemp($row);
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
		
		die('no_alert');
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_PANEN";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_PANEN";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_PANEN";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
