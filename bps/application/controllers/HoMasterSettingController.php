<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Setting Upload
Function 			:	- masterSettingAction	: menampilkan list master setting
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class HoMasterSettingController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global    = new Application_Model_Global();
    }

    public function indexAction()
    {
        $this->_redirect('/master-setting/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Prebudgeting &raquo; Master';
		$table = new Application_Model_MasterSetting();
    }

    //menampilkan list master setting
	public function masterSettingAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_MasterSetting();
        $list = $table->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }
}
