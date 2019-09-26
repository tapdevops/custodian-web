<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Parameter
Function 			:	- parameterValueAction	: menampilkan list parameter
						- inputAction			: menampilkan inputan master parameter
						- getrowAction			: menampilkan data parameter yang ingin diubah
						- saveAction			: action untuk simpan
						- deleteAction			: action untuk hapus
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class SetupMasterParameterValueController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global    = new Application_Model_Global();
    }

    public function indexAction()
    {
        $this->_redirect('/setup-master-parameter-value/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Pengaturan &raquo; Parameter';
		$table = new Application_Model_SetupMasterParameterValue();
    }

    //menampilkan list parameter
	public function parameterValueAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_SetupMasterParameterValue();
        $list = $table->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }

	//menampilkan inputan master parameter
    public function inputAction()
    {
		$params = $this->_request->getParams();
		
        $this->view->title = 'Parameter';
        $this->_helper->layout->setLayout('detail');

        $table = new Application_Model_SetupMasterParameterValue();
        $this->view->input = $table->getInput($params);
    }
	
	//menampilkan data parameter yang ingin diubah
    public function getRowAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_SetupMasterParameterValue();
        $row = $table->getRow($params);
        $result = Zend_Json::encode($row);

        die($result);
    }

	//action untuk simpan
    public function saveAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_SetupMasterParameterValue();
        $result = $table->saveRecord($params);

        die($result);
    }
	
	//action untuk hapus
    public function deleteAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_SetupMasterParameterValue();
        $result = $table->deleteRecord($params);

        die($result);
    }
}
