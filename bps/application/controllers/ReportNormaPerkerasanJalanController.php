<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report Norma Perkerasan Jalan
Function 			:	- listAction		: menampilkan list report norma Perkerasan Jalan
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	05/07/2013
Update Terakhir		:	05/07/2013
Revisi				:	
YULIUS 06/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class ReportNormaPerkerasanJalanController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_ReportNormaPerkerasanJalan();
		$this->view->input = $this->_model->getInput();
		$this->_db = Zend_Registry::get('db');
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/report-norma-perkerasan-jalan/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Harga Perkerasan Jalan';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list report norma Perkerasan Jalan
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		//print_r($params); exit();
        $data = $this->_model->getList($params);
        die(json_encode($data));
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
	
	//Added by Ardo, 25-08-2016
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
				$rows[$key]['JARAK_RANGE']			= $params['text25'][$key]; // JARAK_RANGE
				$rows[$key]['EXTERNAL_PRICE']		= $params['text23'][$key]; // EXTERNAL PRICE
				
				// param inheritance
				$rows[$key]['key_find']				= $params['text03'][$key]; // BA_CODE
            }
        }
		return $rows;
	}
	
	public function saveAction(){
		
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
		
		//print_r($rows); exit;
		if (!empty($rows)) {		
			//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
			foreach ($rows as $key => $row) {
				$params['key_find'] = $row['BA_CODE'];
				
			}
		}
		//1. UPDATE EXTERNAL_PRICE
		//generate filename untuk .sh dan .sql
		$filename = $uniq_code_file.'_00_RNPJ_01_saveRNPJ';
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		 
		//save plan untuk hitung total rotasi
		foreach ($rows as $key => $row) {
			/* $return_cek = $this->_model->checkData($row);
			if($return_cek['status'] == 1){
				$data['return'] = "empty";
				die(json_encode($data));
			} */
			if ($row['CHANGE']){
				
				$row['filename'] = $filename;	
				
				$return = $this->_model->save_external_price($row);
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
		
		//2. HITUNG PER COST ELEMENT
		//generate filename untuk .sh dan .sql
		
		$params = $this->_request->getPost();
		$temp_act = array();
		foreach($params['text04'] as $par_key => $val_par){
			if(count($temp_act)==0){
				$temp_act[] = $val_par;
			} else {
				if(!in_array($val_par, $temp_act)){
					$temp_act[] = $val_par;
				}
			}
		}
		//print_r($temp_act); exit;
		$model_rkt = new Application_Model_RktPerkerasanJalan();
		$params['src_matstage_code'] = 0;
		
		
		foreach($temp_act as $val_act){
			$params['src_coa_code'] = $val_act;
			$records1 = $this->_db->fetchAll("{$model_rkt->getData($params)}");
			//print_r($records1); exit;
			if (!empty($records1)) {
				
				
				foreach ($records1 as $idx1 => $record1) {
					if($record1['SUMBER_BIAYA']=='EXTERNAL'){
						$uniq_code_file = $this->_global->genFileName();
						$filename = $uniq_code_file.'_00_RNPJ_02_countRNPJ';
						$this->_global->createBashFile($filename); //create bash file		
						$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
						$record1['filename'] = $filename;
						$model_rkt->calCostElement('CONTRACT', $record1);
						
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
				}
				
			}
		}
		
		//3. HITUNG TOTAL COST
		//generate filename untuk .sh dan .sql
		$params = $this->_request->getPost();
		$params['src_matstage_code'] = 0;
		foreach($temp_act as $val_act){
			
			
			//hitung distribusi biaya seluruh halaman
			
			$params['src_coa_code'] = $val_act;
			$records1 = $this->_db->fetchAll("{$model_rkt->getData($params)}");
			
			if (!empty($records1)) {
				
				
				foreach ($records1 as $idx1 => $record1) {
					if($record1['SUMBER_BIAYA']=='EXTERNAL'){
						$uniq_code_file = $this->_global->genFileName();
						$filename = $uniq_code_file.'_00_RNPJ_03_calTotalCost';
						$this->_global->createBashFile($filename); //create bash file		
						$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
						
						$record1['filename'] = $filename;
						//hitung total cost
						$model_rkt->calTotalCost($record1);
						
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
					
				}
				
			}
			
		}
		//exit;
		
		
		
		die(json_encode($data));
	}
	
}