<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Setting Upload
Function 			:	- normaSettingAction	: menampilkan list norma setting
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	17/04/2013
Update Terakhir		:	17/04/2013
Revisi				:	
=========================================================================================================================
*/
class NormaSettingController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global    = new Application_Model_Global();
    }

    public function indexAction()
    {
        $this->_redirect('/norma-setting/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Prebudgeting &raquo; Norma';
		$table = new Application_Model_NormaSetting();
    }

    //menampilkan list norma setting
	public function normaSettingAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_NormaSetting();
        $list = $table->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }
}
