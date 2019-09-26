<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Panen Supervisi
Function 			:	- listAction		: menampilkan list norma Panen Supervisi
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	26/06/2013
Update Terakhir		:	26/06/2013
Revisi				:	
YULIUS 08/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function saveAction & saveTempAction
						- saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
						- penambahan pengecekan untuk lock table pada listAction, saveAction
						- add function mappingAction
YULIUS 10/07/2014	:	- modify module updateInheritModule()		
YULIUS 16/07/2014	:	- Tambah deklarasi variable utk inherit module								
=========================================================================================================================
*/
class NormaPanenSupervisiController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaPanenSupervisi();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-panen-supervisi/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Data &raquo; Norma Panen Supervisi';
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
	
	//menampilkan list norma Panen Supervisi
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
		//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
		$lock = $this->_global->checkLockTable($params);
        
		if($lock['JUMLAH']){
			$data['return'] = "locked";
			$data['module'] = $lock['MODULE'];
			$data['insert_user'] = $lock['INSERT_USER'];
			die(json_encode($data));
		} else { 
			$data = $this->_model->getList($params);
			die(json_encode($data));
		}
    }

  /**
   * yaddi.surahman@tap-agri.co.id
   * 2017-08-09
   * list norma supervisi berdasarkan rule 2018
   */
  public function list2018Action()
  {
    $this->_helper->viewRenderer->setNoRender(true);
    $params = $this->_request->getPost();
    $data = $this->_model->getList_2018($params);
    die(json_encode($data));
  }
	
	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();
        $rows = array();
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']			= $params['text00'][$key]; // ROW_ID
				$rows[$key]['BA_CODE']			= $params['text03'][$key]; // BA_CODE
				$rows[$key]['PERIOD_BUDGET']	= $params['text02'][$key]; // PERIOD_BUDGET
				$rows[$key]['MIN_BJR']	= $params['text04'][$key]; // MIN_BJR
				$rows[$key]['MAX_BJR']	= $params['text05'][$key]; // MAX_BJR
				$rows[$key]['JANJANG_OPERATION']	= $params['text06'][$key]; // JANJANG_OPERATION
				$rows[$key]['BJR_BUDGET']	= $params['text07'][$key]; // BJR_BUDGET
				$rows[$key]['JANJANG_OPERATION']	= $params['text08'][$key]; // JANJANG_OPERATION
				$rows[$key]['OVER_BASIS_JANJANG']	= $params['text09'][$key]; // OVER_BASIS_JANJANG
				$rows[$key]['RP_KG']	= $params['text10'][$key]; // RP_KG
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['activity_code'] 	= $params['text04'][$key]; // ACTIVITY
				$rows[$key]['COST_ELEMENT'] 	= $params['text08'][$key]; // COST ELEMENT
				$rows[$key]['vra_code'] 		= $params['text11'][$key]; // VRA_CODE
				$rows[$key]['MATERIAL_CODE'] 	= $params['text11'][$key]; // MATERIAL_CODE
            }
        }
		return $rows;
	}
	
	//save data
	public function saveAction()
    {
        $rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName();
		$urutan=0;
		if (!empty($rows)) {		
			//cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
			foreach ($rows as $key => $row) {
				$params['key_find'] = $row['BA_CODE'];
				$lock = $this->_global->checkLockTable($params);		
				if($lock['JUMLAH']){
					$data['return'] = "locked";
					$data['module'] = $lock['MODULE'];
					$data['insert_user'] = $lock['INSERT_USER'];
					die(json_encode($data));
				}
			}
						
			// ************************************************ SAVE NORMA PANEN SUPERVISI TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $uniq_code_file.'_00_TNPANENSUPV_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_saveTemp';
			$this->_global->createBashFile($filename); //create bash file
			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
			//save norma infra temp
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
			// ************************************************ SAVE NORMA PANEN SUPERVISI TEMP ************************************************
		}
		
		// ************************************************ SAVE ALL NORMA PANEN SUPERVISI ************************************************
		//generate filename untuk .sh dan .sql
		$filename = $this->_global->genFileName();
		$urutan++;
		$filename = $uniq_code_file.'_00_TNPANENSUPV_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_save';
		$this->_global->createBashFile($filename); //create bash file
		
		$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
			
		//hitung distribusi infra seluruh halaman
		$params = $this->_request->getPost();
		$records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$record1['filename'] = $filename;
				$this->_model->save($record1);
				
				//ditampung untuk inherit module
				$updated_row[] = $record1;
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
		// ************************************************ SAVE ALL NORMA PANEN SUPERVISI ************************************************
		
		
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		if (!empty($updated_row)) {
			$idxInherit = 1;
			foreach ($updated_row as $idx1 => $record1) {
				//update inherit module
				$record1['uniq_code_file'] 	= $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
				$record1['key_find'] 		= $record1['BA_CODE']; // BA_CODE
				$record1['budgetperiod'] 	= $record1['PERIOD_BUDGET']; // PERIOD_BUDGET
				
				$record1['ROW_ID'] = ""; //reset rowid agar tidak jadi filter ketika select data
				$this->updateInheritModule($record1);
				
				$idxInherit++;
			}
		}
		// ************************************************ UPDATE INHERIT MODULE ************************************************
		
		$data['return'] = "done";
		die(json_encode($data));
    }
	
	//update inherit module
	public function updateInheritModule($row = array()) {
		if (!empty($row)) {		
			$urutan = 0;	
			// ************************************************ UPDATE RKT PANEN ************************************************
			$model = new Application_Model_RktPanen();	
			$rec = $this->_db->fetchAll("{$model->getData($row)}");	
			
			//1. HITUNG PER COST ELEMENT
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPANENSUPV_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanen';
			$this->_global->createBashFile($filename); //create bash file			
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
				
			if (!empty($rec)) {
				foreach ($rec as $idx1 => $record1) {
					$record1['filename'] = $filename;
					//hitung cost element
					$model->calCostElement('LABOUR', $record1);
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
			//generate filename untuk .sh dan .sql
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPANENSUPV_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanenTotalCost';
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
			$urutan++;
			$filename = $row['uniq_code_file'].'_TNPANENSUPV_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'RktPanenSaveVRA';
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
			// ************************************************ UPDATE RKT PANEN ************************************************
		}
	}
	
	//simpan data sementara sesuai input user
	public function saveTempAction()
    {
        $rows = $this->mappingAction();
		
		if (!empty($rows)) {		
			// ************************************************ SAVE NORMA PANEN SUPERVISI TEMP ************************************************
			//generate filename untuk .sh dan .sql
			$filename = $this->_global->genFileName();
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
			// ************************************************ SAVE NORMA PANEN SUPERVISI TEMP ************************************************
		
		}
		
		die('no_alert');
    }
}
