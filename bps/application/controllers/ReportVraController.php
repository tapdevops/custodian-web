<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report VRA
Function 			:	- listAction		: menampilkan list Report VRA
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	02/07/2013
Update Terakhir		:	02/07/2013
Revisi				:	
=========================================================================================================================
*/
class ReportVraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_ReportVra();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/report-vra/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; VRA';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->tunjangan = $this->_model->getTunjangan();
		$this->view->pkumum = $this->_model->getPkUmum();
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Report VRA
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
}
