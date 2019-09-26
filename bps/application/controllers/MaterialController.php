<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Material
Function 			:	- listAction		: menampilkan list Material
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class MaterialController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_Material();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/material/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Master Material';
		$this->_helper->layout->setLayout('detail');
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Material
    public function listAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	public function mappingAction(){
		$params = $this->_request->getParams();
		 $rows = array();
		 foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['MATERIAL_NAME']   	= $params['text05'][$key]; // MATERIAL_NAME
				$rows[$key]['UOM']   			= $params['text06'][$key]; // UOM
				$rows[$key]['VALUATION_CLASS']  = $params['text07'][$key]; // VALUATION_CLASS
				$rows[$key]['COA_CODE']  		= $params['text08'][$key]; // COA_CODE
				$rows[$key]['PRICE']			= $params['text10'][$key]; // PRICE
				$rows[$key]['BASIC_NORMA_CODE']	= $params['text11'][$key]; // BASIC_NORMA_CODE	
				$rows[$key]['FLAG']				= $params['text13'][$key]; // FLAG
				$rows[$key]['DETAIL_CAT_CODE']	= $params['text14'][$key]; // DETAIL_CAT_CODE
				$rows[$key]['DETAIL_CAT_DESC']	= $params['text15'][$key]; // DETAIL_CAT_DESC			
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		if(!empty($rows)){
		
			// ************************************************ SAVE MASTER MATERIAL TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_TNMATERIAL_01_SVTEMP';
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
			// ************************************************ SAVE MASTER MATERIAL TEMP ************************************************
			
			// ************************************************ SAVE MASTER MATERIAL ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_TM_ASSET';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			$params = $this->_request->getPost();
			$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
			if (!empty($records1)) {
				foreach ($records1 as $key => $record1) {
					if($record1['FLAG_TEMP'] == 'Y'){
						$record1['filename'] = $filename;
						$this->_model->save($record1);
						
						//ditampung untuk inherit module
						$updated_row[] = $record1;
					}
				}
			}	
			$updated_row['BA_CODE'] = (count($uniq_row['BA_CODE']) > 1) ? implode("','", $uniq_row['BA_CODE']) : $uniq_row['BA_CODE'][0];
			
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
			// ************************************************ SAVE MASTER MATERIAL ************************************************
			
			/*foreach ($rows as $key => $row) {
				if($row['CHANGE'] == "Y") {
					$return = $this->_model->save($row);
					
					if (!$return){
						$row_err[] = $key;
					}else{
						$row_success[] = $key;
					}
				}
			}*/
		
		}
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		if (!empty($rows)) {		
			// ************************************************ SAVE MATERIAL TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = "YUS-".$this->_global->genFileName()."oke";
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
			// ************************************************ SAVE MATERIAL TEMP ************************************************		
		}
		
		die('no_alert');
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
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_MATERIAL";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_MATERIAL";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_MATERIAL";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
