<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Setup Hak Ases User
Function 			:	- listAction		: menampilkan list Access Right
						- saveAction		: menyimpan data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Doni Romdoni
Dibuat Tanggal		: 	06/05/2013
Update Terakhir		:	06/05/2013
Revisi				:	
=========================================================================================================================
*/
class HoSetupHakAksesController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
    }

    public function indexAction()
    {
        $this->_redirect('/ho-setup-hak-akses/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Pengaturan &raquo; Hak Akses Pengguna';

        $table = new Application_Model_HoSetupHakAkses();
        $this->view->main = $table->getMain();
    }

    public function listAction()
    {
        $params = $this->_request->getParams();

        $table = new Application_Model_HoSetupHakAkses();
        $list = $table->getList($params);
        $result = Zend_Json::encode($list);

        die($result);
    }

    public function saveAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);

        $table = new Application_Model_HoSetupHakAkses();
        $result = $table->save($params);

        die($result);
    }
}
