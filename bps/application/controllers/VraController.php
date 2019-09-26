<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master VRA
Function 			:	- listAction		: menampilkan list aktivitas
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	03/06/2013
Update Terakhir		:	03/06/2013
Revisi				:	
YUL 12/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction

=========================================================================================================================
*/
class VraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_Vra();
    }

    public function indexAction()
    {
        $this->_redirect('/vra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Master VRA';
		$this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list VRA
    public function listAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_VRA";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_VRA";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_VRA";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
