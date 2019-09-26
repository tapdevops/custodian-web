<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Master Ha Statement
Function 			:	- reportSummaryBudgetHo					: download data report summary development cost
						- reportBudgetHo						: download data report summary development cost
						- reportOpexHo							: download data report sebaran HA
						- reportCapexHo							: download data report CAPEX
						- reportSpdHo							: download data report estate cost
						- reportProfitLossHo					: download data report summary estate cost
						- generateReportAction					: generate report
						- getLastGenerateAction					: get last generate date
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	15/07/2014
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class HoReportSummaryController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_HoReportSummary();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/ho-report-summary/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; Report Summary';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->username = $this->_model->_userName;
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->divcode = $this->_model->_divCode;
		$this->view->divname = $this->_model->getDivName();
		$this->view->cccode = $this->_model->_ccCode;
		$this->view->ccname = $this->_model->getCcName();
    }
	
	//download data report Ho Summary Budget
    public function reportHoSummaryBudgetAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    } 

	//download data report HO Budget
	public function reportHoBudgetAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    } 
	
	//download data report HO Opex
	public function reportHoOpexAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report HO Capex
	public function reportHoCapexAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report HO SPD
	public function reportHoSpdAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	//download data report HO Profit Loss
	public function reportHoProfitLossAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
        $this->_listAction();
    }
	
	
	//generate report
	public function generateReportAction()
    {
		$this->view->period = date("Y", strtotime($this->_period));
		$params = $this->_request->getParams();
		$result = $this->_model->getLastGenerate($params);
		//print_r ($params); die;
		//generate report
		//$this->_model->delTmpRptDevCost($params);
		//$this->_model->tmpRptDevCost($params);
		//$this->_model->delTmpRptEstCost($params);
		//$this->_model->tmpRptEstCost($params);

		$this->_model->delTmpReportHo($params);

		switch ($params['jenis_report']) {
			case "ho_summary_budget" :
					$this->_model->tmpHoSummaryBudget($params);
				break;
			case "ho_budget" :
					$this->_model->tmpHoBudget($params);
				break;
			case "ho_opex" :
					$this->_model->tmpHoOpex($params);
				break;
			case "ho_capex" :
					$this->_model->tmpHoCapex($params);
				break;
			case "ho_spd" :
					$this->_model->tmpHoSpd($params);
				break;
			case "ho_profit_loss" :
					$this->_model->tmpHoProfitLoss($params);
				break;
		}
		
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

		$table = new Application_Model_HoReportSummary();
		$this->_helper->layout->disableLayout();
        $this->view->data = $table->initList($params);
        //echo '<pre>'; print_r ($this->view->data); echo '</pre>';
        $this->view->last_data = $table->getLastGenerate($params);
        //echo '<pre>'; print_r ($this->view->last_data);
        //die;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
