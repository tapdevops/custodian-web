<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master User
Function 			:	- usersAction		: menampilkan list user
						- inputAction		: menampilkan inputan master user
						- rowAction			: menampilkan data user yang ingin diubah
						- saveAction		: action untuk simpan
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class SetupMasterUsersController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
    }

    public function indexAction()
    {
        $this->_redirect('/setup-master-users/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Pengaturan &raquo; Pengguna';
    }
	
	//menampilkan list user
    public function usersAction()
    {
        $params = $this->_request->getParams();
        $table = new Application_Model_SetupMasterUsers();
        $list = $table->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }

    //menampilkan inputan master user
	public function inputAction()
    {
        $this->view->title = 'Pengguna';
        $this->_helper->layout->setLayout('detail');

        $table = new Application_Model_SetupMasterUsers();
        $this->view->input = $table->getInput();
    }

    public function ccAction() {
        $params = $this->_request->getParams();
        //print_r ($params);

        $table = new Application_Model_SetupMasterUsers();
        $row = $table->getCC($params);
        $result = Zend_Json::encode($row);
        die($result);
    }

    //menampilkan data user yang ingin diubah
	public function rowAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);
		
		$table = new Application_Model_SetupMasterUsers();
        $row = $table->getRow($params);
        $result = Zend_Json::encode($row);

        die($result);
    }

	// action untuk simpan
    public function saveAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);

        $table = new Application_Model_SetupMasterUsers();
        $result = $table->saveRecord($params);

        die($result);
    }
}
