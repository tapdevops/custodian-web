<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Mapping Aktivitas di Report
Function 			:	- listAction		: menampilkan list Activity Opsi
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/08/2014
Update Terakhir		:	08/08/2014
Revisi				:	
=========================================================================================================================
*/
class MappingActivityReportController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_MappingActivityReport();
    }

    public function indexAction()
    {
        $this->_redirect('/activity-mapping/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Mapping Aktivitas Untuk Report';
		$this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list Activity mapping
    public function listAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
}
