<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Panen Premi Pemanen
Function 			:	- listAction		: menampilkan list norma Panen Premi Pemanen
						- getStatusPeriodeAction
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	28/06/2013
Update Terakhir		:	28/06/2013
Revisi				:	
YULIUS /07/2014	: 		- penambahan pengecekan untuk lock table pada listAction
						
=========================================================================================================================
*/
class NormaPanenPremiPemanenController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaPanenPremiPemanen();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-panen-premi-pemanen/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Panen Premi Pemanen';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->_helper->layout->setLayout('norma');
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma Panen Premi Pemanen
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$data = $this->_model->getList($params);
		die(json_encode($data));
		
    }
}
