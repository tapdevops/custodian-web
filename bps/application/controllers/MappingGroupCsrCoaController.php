<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Mapping Group CSR - COA
Function 			:	- listAction		: menampilkan list Mapping Group CSR - COA
						- saveAction		: simpan data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/06/2013
Update Terakhir		:	04/06/2013
Revisi				:	
YUL 12/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction

=========================================================================================================================
*/
class MappingGroupCsrCoaController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_MappingGroupCsrCoa();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/mapping-group-csr-coa/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Mapping Group CSR - COA';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list Mapping Group CSR - COA
    public function listAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//save data
	public function saveAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		$row_err = array();
		$row_success = array();
		
        foreach ($params['text00'] as $key => $val) {
            if ($key > 0) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['GROUP_CODE']   	= $params['text02'][$key]; // GROUP_CODE
				$rows[$key]['DESCRIPTION']   	= $params['text03'][$key]; // DESCRIPTION
				$rows[$key]['COA_CODE']   		= $params['text04'][$key]; // COA_CODE
				$rows[$key]['SUB_GROUP_CODE']   = $params['text06'][$key]; // SUB_GROUP_CODE
				$rows[$key]['SUB_GROUP_DESC']   = $params['text07'][$key]; // SUB_GROUP_DESC
            }
        }
       
		foreach ($rows as $key => $row) {
			if($row['CHANGE'] == "Y") {
				$return = $this->_model->save($row);
				
				if (!$return){
					$row_err[] = $key;
				}else{
					$row_success[] = $key;
				}
			}
        }
		
		if ($row_err){
			$data = implode(',',$row_err);
			$data=trim($data,',');
			die("Data ke ".$data." Tidak Berhasil Disimpan.");
		}elseif ($row_success){
			die('done');
		}else{
			die('no_alert');
		}
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
		$params['task_name'] = "TM_GROUP_RELATION_CSR";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_GROUP_RELATION_CSR";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_GROUP_RELATION_CSR";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
