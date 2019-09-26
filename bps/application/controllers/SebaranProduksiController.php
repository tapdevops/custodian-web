<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Master Sebaran Produksi
Function 			:	- listAction		: menampilkan list Sebaran Produksi
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
Revisi				:	
YUL 13/08/2014		: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
=========================================================================================================================
*/
class SebaranProduksiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_model = new Application_Model_SebaranProduksi();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/sebaran-produksi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Master Sebaran Produksi';
		$this->_helper->layout->setLayout('detail');
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
	
	//menampilkan list Sebaran Produksi
    public function listAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	public function mappingAction(){
		$params = $this->_request->getParams();
		 $rows = array();
		 foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['JAN']   			= $params['text04'][$key]; // JAN
				$rows[$key]['FEB']   			= $params['text05'][$key]; // FEB
				$rows[$key]['MAR']   			= $params['text06'][$key]; // MAR
				$rows[$key]['APR']   			= $params['text07'][$key]; // APR
				$rows[$key]['MAY']   			= $params['text08'][$key]; // MAY
				$rows[$key]['JUN']   			= $params['text09'][$key]; // JUN
				$rows[$key]['JUL']   			= $params['text10'][$key]; // JUL
				$rows[$key]['AUG']   			= $params['text11'][$key]; // AUG
				$rows[$key]['SEP']   			= $params['text12'][$key]; // SEP
				$rows[$key]['OCT']   			= $params['text13'][$key]; // OCT
				$rows[$key]['NOV']   			= $params['text14'][$key]; // NOV
				$rows[$key]['DEC']   			= $params['text15'][$key]; // DEC
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE			
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $rows = $this->mappingAction();
       
		if(!empty($rows)){
		
		// ************************************************ SAVE SEBARAN PRODUKSI TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = "YUS-".$this->_global->genFileName();
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma biaya temp
			foreach ($rows as $key => $row) {
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
			}
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
			// ************************************************ SAVE SEBARAN PRODUKSI TEMP ************************************************
		}
				// ************************************************ SAVE SEBARAN PRODUKSI BEGIN ************************************************
				//generate filename untuk .sh dan .sql
				$filename = $uniq_code_file.'_00_TMSEBARANPRODUKSI_01_SAVE';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				$params = $this->_request->getPost();
				$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
				if (!empty($records1)) {
					foreach ($records1 as $key => $record1) {
						if($record1['FLAG_TEMP'] == 'Y'){
							$record1['filename'] = $filename;
							$this->_model->save($record1);
							
							//ditampung untuk inherit module
							$updated_row[] = $record1;
						}
					}
				}	
				$updated_row['BA_CODE'] = (count($uniq_row['BA_CODE']) > 1) ? implode("','", $uniq_row['BA_CODE']) : $uniq_row['BA_CODE'][0];
				
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
				// ************************************************ SAVE SEBARAN PRODUKSI END ************************************************
				
				if (!empty($updated_row['BA_CODE'])) {
				
				//####################################### UPDATE INHERITANCE MODULE #######################################
				
				// RKT PANEN - COST ELEMENT
				$filename = $uniq_code_file.'_00_TMSEBARANPRODUKSI_02_RKTPANEN_CE';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$record1['filename'] = $filename;
						//hitung cost element
						$model->calCostElement('LABOUR', $record1);
						$model->calCostElement('TRANSPORT', $record1);
						$model->calCostElement('TOOLS', $record1);
						$model->calCostElement('CONTRACT', $record1);
						$model->calCostElement('MATERIAL', $record1);
					}
				}
			
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
				
				//2. HITUNG TOTAL COST
				$filename = $uniq_code_file.'_00_TMSEBARANPRODUKSI_03_RKTPANEN_TOTAL';
				$this->_global->createBashFile($filename); //create bash file			
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
				//hitung distribusi biaya seluruh halaman
				$params = $this->_request->getPost();
				$rec = $this->_db->fetchAll("{$model->getData($params)}");
				$lastAfd = ""; $lastBA = ""; $lastBiaya = "";
				$arrAfdUpds = array(); // variabel array data afd yang di modified			
				if (!empty($rec)) {
					foreach ($rec as $idx1 => $record1) {
						$curAfd = $record1['AFD_CODE'];
						$curBA = $record1['BA_CODE'];
						$curBiaya = $record1['SUMBER_BIAYA'];
						$curPeriod = $record1['PERIOD_BUDGET'];
						
						$record1['filename'] = $filename;
						
						//hitung total cost
						$model->calTotalCost($record1);
					
						if(($lastAfd) && ($lastAfd!=$curAfd)){
							array_push($arrAfdUpds, 
									   array('AFD_CODE'=>$lastAfd,
											 'BA_CODE'=>$lastBA,
											 'SUMBER_BIAYA'=>$lastBiaya,
											 'PERIOD_BUDGET'=>$lastPeriod)
									  );
						}
						$lastAfd = $curAfd; 
						$lastBA = $curBA; 
						$lastBiaya = $curBiaya;
						$lastPeriod = $curPeriod;
					}
				}
				array_push($arrAfdUpds, 
						   array('AFD_CODE'=>$lastAfd,
								 'BA_CODE'=>$lastBA,
								 'SUMBER_BIAYA'=>$lastBiaya,
								 'PERIOD_BUDGET'=>$lastPeriod)
						  );
				
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
				
				//3. HITUNG DISTRIBUSI VRA PER AFD
				//generate filename untuk .sh dan .sql
				$filename = $uniq_code_file.'_00_TMSEBARANPRODUKSI_04_DIST_VRA';
				$this->_global->createBashFile($filename); //create bash file		
				$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				foreach ($arrAfdUpds as $key => $arrAfdUpd) {
					$arrAfdUpd['filename'] = $filename;
					$return = $model->saveDistVra($arrAfdUpd);
				}
				
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
				//####################################### UPDATE INHERITANCE MODULE #######################################	
			}
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		if (!empty($rows)) {		
			// ************************************************ SAVE SEBARAN PRODUKSI TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = "YUS-".$this->_global->genFileName()."oke";
			$this->_global->createBashFile($filename); //create bash file
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma biaya temp
			foreach ($rows as $key => $row) {
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
			}
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
			// ************************************************ SAVE SEBARAN PRODUKSI TEMP ************************************************		
		}
		
		die('no_alert');
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		
		$return = $this->_model->delete(base64_decode($params['rowid']));
		
		if ($return){
			die('done');
			
		}else{
			die("Data Tidak Berhasil Dihapus.");
		}
    }
	
	public function updLockedSeqStatusAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_SEBARAN_PRODUKSI";
		$data = $this->_global->updLockedSeqStatus($params);
		die(json_encode($data));
	}
	
	public function chkEnhLockedSequenceAction(){
		$this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_SEBARAN_PRODUKSI";
		$data = $this->_global->chkEnhLockedSequence($params);
		die(json_encode($data));
	}
	
	public function checkLockedSeqAction(){	
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		$params['task_name'] = "TM_SEBARAN_PRODUKSI";
		$data = $this->_global->checkLockSequence($params);
		die(json_encode($data));
	}
}
