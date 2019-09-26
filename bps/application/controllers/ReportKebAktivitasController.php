<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report Kebutuhan Aktivitas Est & Dev Cost
Function 			:	- reportKebAktivitasPerAfdAction		: download data kebutuhan aktivitas est cost per AFD
						- reportKebAktivitasPerBaAction			: download data kebutuhan aktivitas est cost per BA
						- reportKebAktivitasDevPerAfdAction		: download data kebutuhan aktivitas dev cost per AFD
						- reportKebAktivitasDevPerBaAction		: download data kebutuhan aktivitas dev cost per BA
						- generateReportAction					: generate report
						- getLastGenerateAction					: get last generate date
Disusun Oleh		: 	IT Solution - PT Triputra Agro Persada	
Developer			: 	Nicholas Budihardja
Dibuat Tanggal		: 	18/09/2015
Update Terakhir		:	18/09/2015
Revisi				:	
=========================================================================================================================
*/
class ReportKebAktivitasController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_ReportKebAktivitas();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/report-keb-aktivitas/main');
    }

    public function mainAction()
    {
        $this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//download data kebutuhan aktivitas per AFD Aries 16.6.2015
	public function reportKebAktivitasPerAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data kebutuhan aktivitas per BA Aries 16.6.2015
	public function reportKebAktivitasPerBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data kebutuhan aktivitas per AFD Aries 16.6.2015
	public function reportKebAktivitasDevPerAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data kebutuhan aktivitas per BA Aries 16.6.2015
	public function reportKebAktivitasDevPerBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	
	//generate report
	public function generateReportAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
		$params = $this->_request->getParams();
		//generate report
		$this->_model->delTmpRptKebActEstCostBlock($params);		//hapus kebutuhan est cost activitas lama  (ARIES 15.07.2015)
		$this->_model->tmpRptKebActEstCostBlock($params);			//generate kebutuhan activitas est cost per BA (ARIES 15.07.2015)
		$this->_model->delTmpRptKebActDevCostBlock($params);		//hapus kebutuhan cev cost activitas lama  (ARIES 16.07.2015)
		$this->_model->tmpRptKebActDevCostBlock($params);			//generate kebutuhan activitas dev cost per BA (ARIES 16.07.2015)
		
		//get last generate date
		$result = $this->_model->getLastGenerate($params);
		
        $data['return'] = "done";
		$data['last_update_user'] = $result['INSERT_USER'];
		$data['last_update_time'] = $result['INSERT_TIME'];
		die(json_encode($data));
    }
	
	//get last generate date
	public function getLastGenerateAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
		$params = $this->_request->getParams();
		
		//get last generate date
		$result = $this->_model->getLastGenerate($params);
		
        $data['last_update_user'] = $result['INSERT_USER'];
		$data['last_update_time'] = $result['INSERT_TIME'];
		die(json_encode($data));
    }
	
	
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
    //menampilkan list
	private function _listAction()
    {
        $params = $this->_request->getParams();
	$table = new Application_Model_ReportKebAktivitas();
	$this->_helper->layout->disableLayout();
        $this->view->data = $table->initList($params);
        $this->view->last_data = $table->getLastGenerate($params);
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
