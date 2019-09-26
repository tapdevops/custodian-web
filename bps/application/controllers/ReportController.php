<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Report Estate Cost & Development Cost
Function 			:	- reportDevelopmentCostAction			: download data report development cost
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
class ReportController extends Zend_Controller_Action
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
	
	//download data report development cost
    public function reportDevelopmentCostAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    } 

	//download data report development cost per AFD
	public function reportDevelopmentCostAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    } 
	
	//download data report summary development cost
    public function reportSummaryDevelopmentCostAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report summary development cost AFD
    public function reportSummaryDevelopmentCostAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report sebaran HA
    public function reportSebaranHaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report CAPEX
    public function reportCapexAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report estate cost
    public function reportEstateCostAction()
    { 
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report estate cost afd
    public function reportEstateCostAfdAction()
    { 
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report summary estate cost
    public function reportSummaryEstateCostAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report summary estate cost AFD
    public function reportSummaryEstateCostAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report vra utilisasi per BA
    public function reportVraUtilisasiAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report vra utilisasi per region
    public function reportVraUtilisasiRegionAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review development cost per BA
    public function modReviewDevelopmentCostPerBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review development cost per AFD
    public function modReviewDevelopmentCostPerAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review estate cost per BA
    public function modReviewEstateCostPerBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review estate cost per AFD
    public function modReviewEstateCostPerAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review produksi per region
    public function modReviewProduksiPerRegionAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review produksi per BA
    public function modReviewProduksiPerBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data module review produksi per AFD
    public function modReviewProduksiPerAfdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data Report HK Development Cost
    public function reportHkDevelopmentCostAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data Report HK Estate Cost
    public function reportHkEstateCostAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data Report Norma Pupuk TBM NBU 30.07.2015
    public function reportNormaPupukTbmAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data Report Status Hitung All NBU 04.08.2015
    public function reportStatusHitungAllAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data Report Norma Pupuk TM NBU 30.07.2015
    public function reportNormaPupukTmAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
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
	
	//download data HK VS MPP BA Aries 18.6.2015
	public function reportHkMppBaAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data HK VS MPP AFD Aries 18.6.2015
	public function reportHkMppAfdAction()
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
		$this->_model->delTmpRptDevCost($params);					//hapus dev cost lama
		$this->_model->tmpRptDevCost($params);						//generate dev cost per BA
		$this->_model->delTmpRptEstCost($params);					//hapus est cost lama
		$this->_model->tmpRptEstCost($params);						//generate est cost per BA
		//$this->_model->delTmpRptKebActEstCostBlock($params);		//hapus kebutuhan est cost activitas lama  (ARIES 15.07.2015)
		//$this->_model->tmpRptKebActEstCostBlock($params);			//generate kebutuhan activitas est cost per BA (ARIES 15.07.2015)
		//$this->_model->delTmpRptKebActDevCostBlock($params);		//hapus kebutuhan cev cost activitas lama  (ARIES 16.07.2015)
		//$this->_model->tmpRptKebActDevCostBlock($params);			//generate kebutuhan activitas dev cost per BA (ARIES 16.07.2015)
		
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
		//print_r ($params);
		
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

		$table = new Application_Model_Report();
		$this->_helper->layout->disableLayout();
        $this->view->data = $table->initList($params);
        //echo '<pre>'; print_r ($this->view->data); echo '</pre>';
        $this->view->last_data = $table->getLastGenerate($params);
        //echo '<pre>'; print_r ($this->view->last_data);
        //die;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
