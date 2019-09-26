<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk Norma Alat Kerja Non Panen
Function 			:	- listAction		: menampilkan list norma alat kerja non panen
						- saveAction		: save data
						- deleteAction		: hapus data
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
=========================================================================================================================
*/
class NormaAlatKerjaNonPanenController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		$this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_NormaAlatKerjaNonPanen();
		$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/norma-alat-kerja-non-panen/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; Norma Alat Kerja - Non Panen';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }

	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list norma alat kerja non panen
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }
	
	//save data
	public function saveAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		$row_err = array();
		$row_success = array();
		$lastBaAct = $lastBa = $lastAct = "";
		
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['tChange'][$key])) {
				$rows[$key]['CHANGE']        	= $params['tChange'][$key]; // CHANGE
				$rows[$key]['ROW_ID']        	= $params['text00'][$key]; // ROW ID
				$rows[$key]['BA_CODE']     		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['ACTIVITY_CODE']   	= $params['text04'][$key]; // ACTIVITY_CODE
				$rows[$key]['MATERIAL_CODE']   	= $params['text06'][$key]; // MATERIAL_CODE
				$rows[$key]['HARGA_INFLASI']   	= $params['text09'][$key]; // HARGA_INFLASI
				$rows[$key]['UNIT']    			= $params['text10'][$key]; // UNIT
				
				//deklarasi var utk inherit module
				$rows[$key]['key_find'] 		= $params['text03'][$key]; // BA_CODE
				$rows[$key]['activity_code'] 	= $params['text04'][$key]; // ACTIVITY_CODE
            }
        }
       
		foreach ($rows as $key => $row) {
			$return = $this->_model->save($row);
			
			if(($lastBaAct) && ($lastBaAct <> $row['BA_CODE'].$row['ACTIVITY_CODE'])){
				//update summary
				$this->_model->updateSummaryNormaAlatKerjaNonPanen($row);
				
				//####################################### UPDATE INHERITANCE MODULE #######################################
				// NORMA PANEN COST UNIT				
				$model = new Application_Model_NormaPanenCostUnit();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$model->save($record1);
					}
				}
				
				// NORMA PANEN PREMI LANGSIR
				$model = new Application_Model_NormaPanenPremiLangsir();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						$model->save($record1);
					}
				}
				
				// RKT LC
				$model = new Application_Model_RktLc();
				$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT MANUAL NON INFRA
				$model = new Application_Model_RktManualNonInfra();	
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT MANUAL NON INFRA + OPSI
				$model = new Application_Model_RktManualNonInfraOpsi();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT MANUAL INFRA
				$model = new Application_Model_RktManualInfra();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT TANAM MANUAL
				$model = new Application_Model_RktTanamManual();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT TANAM OTOMATIS
				$model = new Application_Model_RktTanam();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT PANEN
				$model = new Application_Model_RktPanen();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT PERKERASAN JALAN
				$model = new Application_Model_RktPerkerasanJalan();
				$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
							
						//hitung total cost
						$model->calTotalCost($record1);
					}
				}
				
				// RKT PUPUK - DISTRIBUSI BIAYA NORMAL
				$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
				$records1 = $this->_db->fetchAll("{$model->getInheritanceData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
					}
				}
				
				// RKT PUPUK - DISTRIBUSI BIAYA SISIP
				$model = new Application_Model_RktPupukDistribusiBiayaSisip();	
				$records1 = $this->_db->fetchAll("{$model->getInheritanceData($row)}");			
				if (!empty($records1)) {
					foreach ($records1 as $idx1 => $record1) {
						//hitung cost element
						$model->calCostElement('TOOLS', $record1);
					}
				}
				
				// RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN
				$model = new Application_Model_RktPupukDistribusiBiayaGabungan();	
				$model->calculateAllItem($row);
				//####################################### UPDATE INHERITANCE MODULE #######################################
			}				
			$lastBaAct = $row['BA_CODE'].$row['ACTIVITY_CODE'];
			$lastBa = $row['BA_CODE'];
			$lastAct = $row['ACTIVITY_CODE'];
			
			if (!$return){
				$row_err[] = $key;
			}else{
				$row_success[] = $key;
			}
        }
		//update summary
		$row['BA_CODE'] = $lastBa;
		$row['ACTIVITY_CODE'] = $lastAct;
		$this->_model->updateSummaryNormaAlatKerjaNonPanen($row);
			
		//####################################### UPDATE INHERITANCE MODULE #######################################
		// NORMA PANEN COST UNIT				
		$model = new Application_Model_NormaPanenCostUnit();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$model->save($record1);
			}
		}
		
		// NORMA PANEN PREMI LANGSIR
		$model = new Application_Model_NormaPanenPremiLangsir();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				$model->save($record1);
			}
		}
		
		// RKT LC
		$model = new Application_Model_RktLc();
		$records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT MANUAL NON INFRA
		$model = new Application_Model_RktManualNonInfra();	
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT MANUAL NON INFRA + OPSI
		$model = new Application_Model_RktManualNonInfraOpsi();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT MANUAL INFRA
		$model = new Application_Model_RktManualInfra();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT TANAM MANUAL
		$model = new Application_Model_RktTanamManual();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT TANAM OTOMATIS
		$model = new Application_Model_RktTanam();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT PANEN
		$model = new Application_Model_RktPanen();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT PERKERASAN JALAN
		$model = new Application_Model_RktPerkerasanJalan();
		$records1 = $this->_db->fetchAll("{$model->getData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
					
				//hitung total cost
				$model->calTotalCost($record1);
			}
		}
		
		// RKT PUPUK - DISTRIBUSI BIAYA NORMAL
		$model = new Application_Model_RktPupukDistribusiBiayaNormal();	
		$records1 = $this->_db->fetchAll("{$model->getInheritanceData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
			}
		}
		
		// RKT PUPUK - DISTRIBUSI BIAYA SISIP
		$model = new Application_Model_RktPupukDistribusiBiayaSisip();	
		$records1 = $this->_db->fetchAll("{$model->getInheritanceData($row)}");			
		if (!empty($records1)) {
			foreach ($records1 as $idx1 => $record1) {
				//hitung cost element
				$model->calCostElement('TOOLS', $record1);
			}
		}
		
		// RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN
		$model = new Application_Model_RktPupukDistribusiBiayaGabungan();	
		$model->calculateAllItem($row);
		//####################################### UPDATE INHERITANCE MODULE #######################################
		
		if ($row_err){
			$data = implode(',',$row_err);
			$data=trim($data,',');
			die("Data ke ".$data." Tidak Berhasil Disimpan.");
		}elseif ($row_success){
			die('done');
		}else{
			die('no_alert');
		}
    }
	
	//hapus data
	public function deleteAction()
    {
        $params = $this->_request->getParams();
        $rows = array();
		
		$return = $this->_model->delete(base64_decode($params['rowid']));
		
		if ($return){
			//update summary
			$act_code = $this->_model->getActivityCode(base64_decode($params['rowid']));
			$this->_model->updateSummaryNormaAlatKerjaNonPanen($act_code);
						
			die('done');
			
		}else{
			die("Data Tidak Berhasil Dihapus.");
		}
    }
}
