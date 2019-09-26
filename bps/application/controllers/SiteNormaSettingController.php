<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Site Norma Setting
Function 			:	- siteNormaSettingAction	: menampilkan list site norma setting
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
=========================================================================================================================
*/
class SiteNormaSettingController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global    = new Application_Model_Global();		
		$this->_model   = new Application_Model_SiteNormaSetting();
    }

    public function indexAction()
    {
        $this->_redirect('/site-norma-setting/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Prebudgeting &raquo; Norma';
    }

    //menampilkan list norma setting
	public function siteNormaSettingAction()
    {
        $params = $this->_request->getParams();
		$list = $this->_model->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }
}
