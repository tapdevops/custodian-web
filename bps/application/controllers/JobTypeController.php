<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Job Type
Function 			:	- listAction		: menampilkan list job type
						- saveAction		: save data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	
=========================================================================================================================
*/
class JobTypeController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_JobType();
    }

    public function indexAction()
    {
        $this->_redirect('/job-type/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Job Type';
		$this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list job type
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
				$rows[$key]['CHANGE']        		= $params['tChange'][$key]; // CHANGE			
				$rows[$key]['ROW_ID']        		= $params['text00'][$key]; // ROW ID
				$rows[$key]['JOB_CODE']       		= $params['text02'][$key]; // JOB_CODE
				$rows[$key]['GROUP_CHECKROLL_CODE'] = $params['text03'][$key]; // GROUP_CHECKROLL_CODE
				$rows[$key]['JOB_TYPE']        		= $params['text04'][$key]; // JOB_TYPE
				$rows[$key]['JOB_DESCRIPTION']     	= $params['text05'][$key]; // JOB_DESCRIPTION				
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
}
