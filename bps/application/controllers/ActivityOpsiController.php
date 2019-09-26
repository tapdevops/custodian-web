<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Activity Opsi
Function 			:	- listAction		: menampilkan list Activity Opsi
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2013
Update Terakhir		:	29/05/2013
Revisi				:	
=========================================================================================================================
*/
class ActivityOpsiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_ActivityOpsi();
    }

    public function indexAction()
    {
        $this->_redirect('/activity-opsi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Aktivitas Opsional';
		$this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list Activity Opsi
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
				$rows[$key]['ROW_ID']        			= $params['text00'][$key]; // ROW ID
				$rows[$key]['ACTIVITY_CODE']   			= $params['text02'][$key]; // ACTIVITY_GROUP
				$rows[$key]['ACTIVITY_OPTIONAL_CODE']   = $params['text04'][$key]; // ACTIVITY_OPTIONAL_CODE
				$rows[$key]['OPTIONAL']   				= $params['text05'][$key]; // OPTIONAL
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
}
