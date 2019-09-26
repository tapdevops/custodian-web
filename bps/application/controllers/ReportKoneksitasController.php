<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report Estate Cost & Development Cost
Function 			:	- reportProduksiAction					: download data report produksi
						- reportSummaryDevelopmentCostAction	: download data report summary development cost
						- reportSebaranHaAction					: download data report sebaran HA
						- reportCapexAction						: download data report CAPEX
						- reportEstateCostAction				: download data report estate cost
						- reportSummaryEstateCostAction			: download data report summary estate cost
						- reportVraUtilisasiAction				: download data report vra utilisasi per BA
						- reportVraUtilisasiRegionAction		: download data report vra utilisasi per region
						- modReviewDevelopmentCostPerBaAction	: download data module review development cost per BA
						- modReviewDevelopmentCostPerAfdAction	: download data module review development cost per AFD
						- modReviewEstateCostPerBaAction		: download data module review estate cost per BA
						- modReviewEstateCostPerAfdAction		: download data module review estate cost per AFD
						- modReviewProduksiPerRegionAction		: download data module review produksi per region
						- modReviewProduksiPerBaAction			: download data module review produksi per BA
						- modReviewProduksiPerAfdAction			: download data module review produksi per AFD
						- generateReportAction					: generate report
						- getLastGenerateAction					: get last generate date
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	23/08/2013
Update Terakhir		:	23/08/2013
Revisi				:	
=========================================================================================================================
*/
class ReportKoneksitasController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_Report();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/report/main');
    }

    public function mainAction()
    {
        $this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//download data report produksi
    public function reportProduksiAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report HS
    public function reportHsAction()
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
		$this->_model->delTmpRptDevCost($params);		//hapus dev cost lama
		$this->_model->tmpRptDevCost($params);			//generate dev cost per BA
		$this->_model->delTmpRptEstCost($params);		//hapus est cost lama
		$this->_model->tmpRptEstCost($params);			//generate est cost per BA
		
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
		$table = new Application_Model_ReportKoneksitas();
		$this->_helper->layout->disableLayout();
        $this->view->data = $table->initList($params);
        //$this->view->last_data = $table->getLastGenerate($params);
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
