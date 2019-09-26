<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Periode Budget
Function 			:	- usersAction		: menampilkan list periode budget
						- inputAction		: menampilkan inputan master periode budget
						- rowAction			: menampilkan data periode budget yang ingin diubah
						- saveAction		: action untuk simpan
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
YUL 12/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class HoCompanyController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
		$this->_model = new Application_Model_HoCompany();
    }

    public function indexAction()
    {
        $this->_redirect('/ho-company/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Pengaturan &raquo; Master &raquo; Company';
        $this->_helper->layout->setLayout('detail');
    }
	
	//menampilkan list periode budget
    public function listAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
    //menampilkan inputan master periode budget
	public function inputAction()
    {
        $this->view->title = 'Master Periode Budget';
        $this->_helper->layout->setLayout('detail');

        $table = new Application_Model_SetupMasterBudgetPeriod();
        $this->view->input = $table->getInput();
    }

    //menampilkan data periode budget yang ingin diubah
	public function rowAction()
    {
        $params = $this->_request->getParams();
        $params['rowid'] = rawurldecode($params['rowid']);
		
		$table = new Application_Model_SetupMasterBudgetPeriod();
        $row = $table->getRow($params);
        $result = Zend_Json::encode($row);

        die($result);
    }

    //mapping textfield name terhadap field name di DB
    public function mappingAction(){
        $params = $this->_request->getParams();
        $rows = array();
        
        foreach ($params['text00'] as $key => $val) {
            //if (($key > 0) && ($params['tChange'][$key])) {
            if ($params['tChange'][$key]) {
                $rows[$key]['CHANGE']           = $params['tChange'][$key]; // CHANGE           
                $rows[$key]['ROW_ID']           = $params['text00'][$key]; // ROW ID
                $rows[$key]['PERIOD_BUDGET']    = $params['budgetperiod']; // PERIOD_BUDGET
                $rows[$key]['BA_CODE']          = $params['text02'][$key];
                $rows[$key]['BA_NAME']          = $params['text03'][$key];
                $rows[$key]['CORE']             = $params['text04'][$key];
                $rows[$key]['COMPANY_CODE']     = $params['text05'][$key];
                $rows[$key]['COMPANY_NAME']     = $params['text06'][$key];
                
                //deklarasi var utk inherit module
                //$rows[$key]['key_find']       = $params['text06'][$key]; // BA_CODE
                //$rows[$key]['afd_code']       = $params['text04'][$key]; // AFD
                //$rows[$key]['block_code']         = $params['text05'][$key]; // BLOCK
                //$rows[$key]['src_afd']            = $params['text04'][$key]; // AFD
                //$rows[$key]['src_block']      = $params['text05'][$key]; // BLOCK
            }
        }
        return $rows;
    }
    
    // action untuk simpan
    public function saveAction()
    {
        $rows = $this->mappingAction();

        $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $status = 0;

        if (!empty($rows)) {
            //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
            foreach ($rows as $key => $row) {
                $params['key_find'] = $row['BA_CODE'];
                $lock = $this->_global->checkLockTable($params);
                if($lock['JUMLAH']) {
                    $data['return'] = "locked";
                    $data['module'] = $lock['MODULE'];
                    $data['insert_user'] = $lock['INSERT_USER'];
                    die(json_encode($data));
                }
            }

            // 1. Simpan TEMP RENCANA KERJA
            // generate filename untuk .sh dan .sql
            $filename = $uniq_code_file.'_00_HO_COMP_01_SAVETEMP';
            $this->_global->createBashFile($filename);
            $this->_global->createSqlFile($filename);
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

            foreach ($rows as $key => $row) {
                //print_r ($rows);
                $row['filename'] = $filename;
                $return = $this->_model->save($row);
            }   

            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            //echo "sh ".getcwd()."/tmp_query/".$filename.".sh"; die;
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
        }

        $data = array();
        if($status == 0){
            $data['return'] = "done";
        }else{
            $data['return'] = "donewithexception";
        }
        die(json_encode($data));
    }
    
    //hapus data
    public function deleteAction()
    {
        $params = $this->_request->getParams();

        //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul      
        $lock = $this->_global->checkLockTable($params);        
        if($lock['JUMLAH']){
            $data['return'] = "locked";
            $data['module'] = $lock['MODULE'];
            $data['insert_user'] = $lock['INSERT_USER'];
            die(json_encode($data));
        }
        
        //generate filename untuk .sh dan .sql
        $filename = $this->_global->genFileName();
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
        $par['ROW_ID'] = base64_decode($params['rowid']);
        $par['filename'] = $filename;
        $this->_model->delete($par); //hapus data
        
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
        
        $data['return'] = "done";
        die(json_encode($data));
    }

	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_PERIOD";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
