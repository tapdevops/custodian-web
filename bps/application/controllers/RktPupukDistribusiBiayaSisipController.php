<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk RKT Pupuk Distribusi Biaya Sisip
Function 			:	- listAction		: menampilkan list RKT Pupuk Distribusi Biaya Sisip
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	18/07/2013
Update Terakhir		:	18/07/2013
Revisi				:	
=========================================================================================================================
*/
class RktPupukDistribusiBiayaSisipController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_RktPupukDistribusiBiayaSisip();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-pupuk-distribusi-biaya-sisip/main');
    }

	
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; Pupuk - Distribusi Biaya Sisip';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list RKT Pupuk Distribusi Biaya Sisip
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//kalkulasi seluruh data
	public function saveAllAction()
    {
		$params = $this->_request->getParams();
        
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$this->_global->createBashFile($filename); //create bash file		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
		
		//hitung biaya
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getInheritData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				//hitung cost element labour
				// $this->_model->calCostElement('LABOUR', $record1);
				//hitung cost element material
				// $this->_model->calCostElement('MATERIAL', $record1);
				//hitung cost element tools
				// $this->_model->calCostElement('TOOLS', $record1);
				//hitung cost element transport
				// $this->_model->calCostElement('TRANSPORT', $record1);
				//hitung cost element contract
				$this->_model->calCostElement('CONTRACT', $record1);
			}
		}
		
    $params['filename'] = $filename;
    $this->_model->calculateToolSisipManual($params);
    $this->_model->calculateLaborSisipManual($params);
    $this->_model->calculateTransportSisipManual($params);
    $this->_model->calculateMaterialSisipManual($params);
    
		//execute transaksi
		$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
		shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query		
		$this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
		
		//pindahkan .sql ke logs
		$uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
		if ( ! is_dir($uploaddir)) {
			$oldumask = umask(0);
			mkdir("$uploaddir", 0777, true);
			chmod("/".date("Y-m-d"), 0777);
			umask($oldumask);
		}
		shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");

        die('done');
    }
}
