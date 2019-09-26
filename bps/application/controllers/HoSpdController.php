<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Master Ha Statement
Function 			:	- getStatusPeriodeAction		: BDJ 23/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 22/04/2013	: menampilkan list Ha Statement
						- saveTempAction				: SID 22/04/2013	: simpan data sementara sesuai input user
						- saveAction					: SID 22/04/2013	: save data
						- deleteAction					: SID 22/04/2013	: hapus data
						- mappingAction					: SID 22/04/2013	: mapping textfield name terhadap field name di DB
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	15/07/2014
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class HoSpdController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_HoSpd();
		//$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/ha-statement/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting HO &raquo; SPD HO';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->username = $this->_model->_userName;
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->divcode = $this->_model->_divCode;
		$this->view->divname = $this->_model->getDivName();
		$this->view->cccode = $this->_model->_ccCode;
		$this->view->ccname = $this->_model->getCcName();
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list Ha Statement
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }

    public function parse_number($number, $dec_point=null) {
    	if (empty($dec_point)) {
    		$locale = localeconv();
    		$dec_point = $locale['decimal_point'];
    	}

    	return floatval(str_replace($dec_point, '.', preg_replace('/[^\d'.preg_quote($dec_point).']/', '', $number)));
    }
		
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
        	//echo $this->parse_number($params['text23'][$key]);
            //if (($key > 0) && ($params['tChange'][$key])) {
        	if ($params['tChange'][$key]) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE			
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; 	// ROW ID
				$rows[$key]['PERIOD_BUDGET']    = $params['text02'][$key]; 	// PERIOD_BUDGET
				$rows[$key]['DIV_CODE']    		= $params['key_find']; 		// BA_CODE
				$rows[$key]['CC_CODE']       	= $params['key_find_cc']; 	// HA_PLANTED

				$rows[$key]['RK_ID']			= $params['text04'][$key];
				$rows[$key]['SPD_DESCRIPTION']	= str_replace('&', "'||'&'||'", $params['text06'][$key]);
				$rows[$key]['COA_CODE']			= $params['text07'][$key];
				$rows[$key]['NORMA_SPD_ID']		= $params['text09'][$key];
				$rows[$key]['CORE_CODE']		= $params['text12'][$key];
				$rows[$key]['COMP_CODE']		= $params['text13'][$key];
				$rows[$key]['BA_CODE']			= $params['text15'][$key];
				$rows[$key]['PLAN']				= $params['text17'][$key];
				$rows[$key]['GOLONGAN']			= $params['text18'][$key];
				$rows[$key]['JLH_PRIA']			= $params['text19'][$key];
				$rows[$key]['JLH_WANITA']		= $params['text20'][$key];
				$rows[$key]['JLH_HARI']			= $params['text21'][$key];
				$rows[$key]['TIKET']			= (empty($params['text23'][$key])) ? 0 : $this->parse_number($params['text23'][$key]);
				$rows[$key]['TRANSPORT_LAIN']	= (empty($params['text25'][$key])) ? 0 : $this->parse_number($params['text25'][$key]);
				$rows[$key]['UANG_MAKAN']		= (empty($params['text27'][$key])) ? 0 : $this->parse_number($params['text27'][$key]);
				$rows[$key]['UANG_SAKU']		= (empty($params['text29'][$key])) ? 0 : $this->parse_number($params['text29'][$key]);
				$rows[$key]['HOTEL_JLH_HARI']	= $params['text30'][$key];
				$rows[$key]['HOTEL_JLH_TARIF']	= (empty($params['text32'][$key])) ? 0 : $this->parse_number($params['text32'][$key]);
				$rows[$key]['OTHERS']			= (empty($params['text33'][$key])) ? 0 : $this->parse_number($params['text33'][$key]);
				$rows[$key]['TOTAL']			= (empty($params['text34'][$key])) ? 0 : $this->parse_number($params['text34'][$key]);
				$rows[$key]['REMARKS_OTHERS']	= str_replace('&', "'||'&'||'", $params['text35'][$key]);
				$rows[$key]['SEBARAN_JAN']		= (empty($params['text36'][$key])) ? 0 : $this->parse_number($params['text36'][$key]);
				$rows[$key]['SEBARAN_FEB']		= (empty($params['text37'][$key])) ? 0 : $this->parse_number($params['text37'][$key]);
				$rows[$key]['SEBARAN_MAR']		= (empty($params['text38'][$key])) ? 0 : $this->parse_number($params['text38'][$key]);
				$rows[$key]['SEBARAN_APR']		= (empty($params['text39'][$key])) ? 0 : $this->parse_number($params['text39'][$key]);
				$rows[$key]['SEBARAN_MAY']		= (empty($params['text40'][$key])) ? 0 : $this->parse_number($params['text40'][$key]);
				$rows[$key]['SEBARAN_JUN']		= (empty($params['text41'][$key])) ? 0 : $this->parse_number($params['text41'][$key]);
				$rows[$key]['SEBARAN_JUL']		= (empty($params['text42'][$key])) ? 0 : $this->parse_number($params['text42'][$key]);
				$rows[$key]['SEBARAN_AUG']		= (empty($params['text43'][$key])) ? 0 : $this->parse_number($params['text43'][$key]);
				$rows[$key]['SEBARAN_SEP']		= (empty($params['text44'][$key])) ? 0 : $this->parse_number($params['text44'][$key]);
				$rows[$key]['SEBARAN_OCT']		= (empty($params['text45'][$key])) ? 0 : $this->parse_number($params['text45'][$key]);
				$rows[$key]['SEBARAN_NOV']		= (empty($params['text46'][$key])) ? 0 : $this->parse_number($params['text46'][$key]);
				$rows[$key]['SEBARAN_DEC']		= (empty($params['text47'][$key])) ? 0 : $this->parse_number($params['text47'][$key]);
				$rows[$key]['SEBARAN_TOTAL']	= (empty($params['text48'][$key])) ? 0 : $this->parse_number($params['text48'][$key]);
				$rows[$key]['TIPE_NORMA'] 		= $params['text50'][$key];
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['afd_code'] 		= $params['text04'][$key]; // AFD
				$rows[$key]['block_code'] 		= $params['text05'][$key]; // BLOCK
				$rows[$key]['src_afd'] 			= $params['text04'][$key]; // AFD
				$rows[$key]['src_block'] 		= $params['text05'][$key]; // BLOCK
            }
        }
		return $rows;
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save Ha Statement temp
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
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $status = 0;

		if (!empty($rows)) {
			//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
			foreach ($rows as $key => $row) {
				$params['key_find'] = $row['BA_CODE'];
				$lock = $this->_global->checkLockTable($params);
				if($lock['JUMLAH']) {
					$data['return'] = "locked";
					$data['module'] = $lock['MODULE'];
					$data['insert_user'] = $lock['INSERT_USER'];
					die(json_encode($data));
				}
			}

			// 1. Simpan TEMP RENCANA KERJA
			// generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_HO_SPD_01_SAVETEMP';
			$this->_global->createBashFile($filename);
			$this->_global->createSqlFile($filename);
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

			foreach ($rows as $key => $row) {
				//print_r ($rows);
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
			}	

			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			//echo "sh ".getcwd()."/tmp_query/".$filename.".sh"; die;
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

		$data = array();
		if($status == 0){
			$data['return'] = "done";
		}else{
			$data['return'] = "donewithexception";
		}
		die(json_encode($data));
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
		//ketika HS melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
		//$this->_global->insertLockTable($params['BA_CODE'], 'HECTARE STATEMENT');
		
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_HSPD_01_DELETE';
		$this->_global->createBashFile($filename); //create bash file			
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hapus data
		$params['ROW_ID'] = base64_decode($params['rowid']);
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
		
		//hapus dari table lock ketika selesai melakukan perhitungan di norma CHECKROLL
		//$this->_global->deleteLockTable($params['BA_CODE'], 'HECTARE STATEMENT');
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_HECTARE_STATEMENT";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_HECTARE_STATEMENT";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_HECTARE_STATEMENT";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
