<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Periode Budget
Function 			:	- usersAction		: menampilkan list periode budget
						- inputAction		: menampilkan inputan master periode budget
						- rowAction			: menampilkan data periode budget yang ingin diubah
						- saveAction		: action untuk simpan
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
YUL 12/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class SetupMasterBudgetPeriodController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_model = new Application_Model_SetupMasterBudgetPeriod();
    }

    public function indexAction()
    {
        $this->_redirect('/setup-master-budget-period/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Pengaturan &raquo; Master &raquo; Periode Budget';
    }
	
	//menampilkan list periode budget
    public function listAction()
    {
        $params = $this->_request->getParams();

        $list = $this->_model->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }
	
    //menampilkan inputan master periode budget
	public function inputAction()
    {
        $this->view->title = 'Master Periode Budget';
        $this->_helper->layout->setLayout('detail');

        $table = new Application_Model_SetupMasterBudgetPeriod();
        $this->view->input = $table->getInput();
    }

    //menampilkan data periode budget yang ingin diubah
	public function rowAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);
		
		$table = new Application_Model_SetupMasterBudgetPeriod();
        $row = $table->getRow($params);
        $result = Zend_Json::encode($row);

        die($result);
    }

	// action untuk simpan
    public function saveAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);

        $table = new Application_Model_SetupMasterBudgetPeriod();
        $result = $table->saveRecord($params);

        die($result);
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
