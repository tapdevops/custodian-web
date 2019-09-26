<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report report alokasi checkroll
Function 			:	- listAction		: menampilkan list report alokasi checkroll
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	05/07/2013
Update Terakhir		:	05/07/2013
Revisi				:	
=========================================================================================================================
*/
class ReportCheckrollAlokasiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_ReportCheckrollAlokasi();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/report-checkroll-alokasi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; Alokasi Checkroll';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list report checkroll alokasi
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
}