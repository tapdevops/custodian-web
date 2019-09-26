<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk RKT CAPEX
Function 			:	- getStatusPeriodeAction		: BDJ 22/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 08/07/2013	: menampilkan list RKT CAPEX
						- getUrgencyCapexAction			: SID 08/07/2013	: mengambil nilai urgency CAPEX
						- mappingAction					: SID 08/07/2013	: mapping textfield name terhadap field name di DB
						- saveTempAction				: SID 08/07/2013	: simpan data sementara sesuai input user
						- saveAction					: SID 08/07/2013	: save data
						- deleteAction					: SID 08/07/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/07/2013
Update Terakhir		:	11/07/2014
Revisi				:	
YUL 08/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class RktCapexController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_RktCapex();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-capex/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 1 &raquo; RKT CAPEX';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->minPriceCapex = $this->_model->getMinPriceCapex(); // min price capex
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT CAPEX
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//mengambil nilai urgency CAPEX
    public function getUrgencyCapexAction()
    {		
        $params = $this->_request->getPost();
        $data = $this->_model->getUrgencyCapex($params);
        die(json_encode($data));
    }
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
			if (($key > 0) && ($params['text03'][$key])){
				$rows[$key]['CHANGE']        			= $params['tChange'][$key]; // ROW ID
				$rows[$key]['ROW_ID']        			= $params['text00'][$key]; // ROW ID
				$rows[$key]['PERIOD_BUDGET']        	= $params['budgetperiod']; // ROW ID
				$rows[$key]['TRX_CODE']        			= $params['trxrktcode'][$key]; // ROW ID
				$rows[$key]['BA_CODE']      			= $params['text03'][$key]; // BA_CODE
				$rows[$key]['COA_CODE']      			= $params['text04'][$key]; // COA_CODE
				$rows[$key]['ASSET_CODE']  				= $params['text06'][$key]; // ASSET_CODE
				$rows[$key]['DETAIL_SPESIFICATION'] 	= $params['text08'][$key]; // DETAIL_SPESIFICATION
				$rows[$key]['URGENCY_CAPEX']        	= $params['text09'][$key]; // URGENCY_CAPEX
				$rows[$key]['PRICE'] 					= $params['text10'][$key]; // PRICE
				$rows[$key]['QTY_ACTUAL']      			= $params['text12'][$key]; // QTY_ACTUAL
				$rows[$key]['DIS_TAHUN_BERJALAN']   	= $params['text13'][$key]; // DIS_TAHUN_BERJALAN
				$rows[$key]['DIS_JAN']      			= $params['text14'][$key]; // DIS_JAN
				$rows[$key]['DIS_FEB']      			= $params['text15'][$key]; // DIS_FEB
				$rows[$key]['DIS_MAR']      			= $params['text16'][$key]; // DIS_MAR
				$rows[$key]['DIS_APR']      			= $params['text17'][$key]; // DIS_APR
				$rows[$key]['DIS_MAY']      			= $params['text18'][$key]; // DIS_MAY
				$rows[$key]['DIS_JUN']      			= $params['text19'][$key]; // DIS_JUN
				$rows[$key]['DIS_JUL']      			= $params['text20'][$key]; // DIS_JUL
				$rows[$key]['DIS_AUG']      			= $params['text21'][$key]; // DIS_AUG
				$rows[$key]['DIS_SEP']      			= $params['text22'][$key]; // DIS_SEP
				$rows[$key]['DIS_OCT']      			= $params['text23'][$key]; // DIS_OCT
				$rows[$key]['DIS_NOV']      			= $params['text24'][$key]; // DIS_NOV
				$rows[$key]['DIS_DEC']      			= $params['text25'][$key]; // DIS_DEC
				//BIAYA
				$rows[$key]['TOTAL_BIAYA']      		= $params['text26'][$key]; // TOTAL_BIAYA
				$rows[$key]['DIS_BIAYA_JAN']      		= $params['text27'][$key]; // DIS_BIAYA_JAN
				$rows[$key]['DIS_BIAYA_FEB']      		= $params['text28'][$key]; // DIS_BIAYA_FEB
				$rows[$key]['DIS_BIAYA_MAR']      		= $params['text29'][$key]; // DIS_BIAYA_MAR
				$rows[$key]['DIS_BIAYA_APR']      		= $params['text30'][$key]; // DIS_BIAYA_APR
				$rows[$key]['DIS_BIAYA_MAY']      		= $params['text31'][$key]; // DIS_BIAYA_MAY
				$rows[$key]['DIS_BIAYA_JUN']      		= $params['text32'][$key]; // DIS_BIAYA_JUN
				$rows[$key]['DIS_BIAYA_JUL']      		= $params['text33'][$key]; // DIS_BIAYA_JUL
				$rows[$key]['DIS_BIAYA_AUG']      		= $params['text34'][$key]; // DIS_BIAYA_AUG
				$rows[$key]['DIS_BIAYA_SEP']      		= $params['text35'][$key]; // DIS_BIAYA_SEP
				$rows[$key]['DIS_BIAYA_OCT']      		= $params['text36'][$key]; // DIS_BIAYA_OCT
				$rows[$key]['DIS_BIAYA_NOV']      		= $params['text37'][$key]; // DIS_BIAYA_NOV
				$rows[$key]['DIS_BIAYA_DEC']      		= $params['text38'][$key]; // DIS_BIAYA_DEC
				
				//old data
				$rows[$key]['OLD_COA_CODE']      		= $params['text004'][$key]; // OLD_COA_CODE
				$rows[$key]['OLD_ASSET_CODE']  			= $params['text006'][$key]; // OLD_ASSET_CODE
				$rows[$key]['OLD_DETAIL_SPESIFICATION'] = $params['text008'][$key]; // OLD_DETAIL_SPESIFICATION
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
       
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			if ($row['CHANGE']){
				$row['filename'] = $filename;
				$return = $this->_model->save($row);
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
		die(json_encode($data));
    }
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		$row_err = array();
		$row_success = array();
       
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
       
		foreach ($rows as $key => $row) {
            if ($row['CHANGE']){
				$row['filename'] = $filename;
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
		
		die('no_alert');
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		$params['filename'] = $filename;
		$return = $this->_model->delete($params);
		
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
		
		$data['return'] = 'done';
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_CAPEX";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_CAPEX";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TR_RKT_CAPEX";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
