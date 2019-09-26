<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Alat Kerja Non Panen
Function 			:	- getList					: menampilkan list norma alat kerja non panen
						- save						: simpan data
						- delete					: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaAlatKerjaNonPanen
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//setting input untuk region dan maturity stage
	public function getInput()
    {
        $result = array();

        $table = new Application_Model_DbOptions();
        $options = array();
		$options['optRegion'] = $table->getRegion();

        // elements
		$result['src_region_code'] = array(
            'type'    => 'select',
            'name'    => 'src_region_code',
            'value'   => '',
            'options' => $options['optRegion'],
            'ext'     => 'onChange=\'$("#src_ba").val("");\'', //src_afd
			'style'   => 'width:200px;background-color: #e6ffc8;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (norma.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (norma.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   norma.BA_CODE,
				   norma.MATERIAL_CODE,
				   norma.ACTIVITY_CODE,
				   norma.UNIT,
				   norma.TOTAL,
				   norma_harga.PRICE HARGA_INFLASI,
				   material.MATERIAL_NAME,
				   material.UOM,
				   act.DESCRIPTION ACTIVITY_DESC
			  FROM TN_ALAT_KERJA_NON_PANEN norma
				   LEFT JOIN TN_HARGA_BARANG norma_harga
					  ON norma.MATERIAL_CODE = norma_harga.MATERIAL_CODE
						 AND norma.BA_CODE = norma_harga.BA_CODE
				   LEFT JOIN TM_MATERIAL material
					  ON norma.MATERIAL_CODE = material.MATERIAL_CODE
						 AND norma.BA_CODE = material.BA_CODE
				   LEFT JOIN TM_ACTIVITY act
					  ON norma.ACTIVITY_CODE = act.ACTIVITY_CODE
				   LEFT JOIN TM_ORGANIZATION B
					  ON norma.BA_CODE = B.BA_CODE
			 WHERE norma.DELETE_USER IS NULL
		 ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND  UPPER('".$this->_siteCode."') LIKE  '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND UPPER(norma.ACTIVITY_CODE) LIKE UPPER('%".$params['activity_code']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}		
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(act.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.UNIT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TOTAL) LIKE UPPER('%".$params['search']."%')					
				)
            ";
        }
		
		//untuk inheritance
		if ($params['sub_cost_element'] != '') {
			$query .= "
                AND UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['sub_cost_element']."%')
            ";
        }
		
		$query .= "
			ORDER BY norma.MATERIAL_CODE
		";
		
		return $query;
	}
	
	//menampilkan list norma alat kerja non panen
    public function getList($params = array())
    {
        $result = array();

        $begin = "
            SELECT * FROM (SELECT ROWNUM MY_ROWNUM, MY_TABLE.*
            FROM (SELECT TEMP.*
            FROM (
        ";
        $min = (intval($params['page_num']) - 1) * intval($params['page_rows']);
        $max = $min + intval($params['page_rows']);
        $end = "
            ) TEMP
            ) MY_TABLE
              WHERE ROWNUM <= {$max}
            ) WHERE MY_ROWNUM > {$min}
        ";
        
		$sql = "SELECT COUNT(*) FROM ({$this->getData($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$this->getData($params)} {$end}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
			SELECT ROWIDTOCHAR(ROWID) ROW_ID 
			FROM TN_ALAT_KERJA_NON_PANEN 
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND MATERIAL_CODE  = '{$params['MATERIAL_CODE']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		
		// ********************************************** UPDATE NORMA ALAT KERJA NON PANEN **********************************************
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$total_rp = $this->_formula->cal_NormaAlatKerjaNonPanen_TotalRupiah($row);
			
		if ($row['ROW_ID']){
			try {
				$sql = "UPDATE TN_ALAT_KERJA_NON_PANEN
						SET UNIT = '".addslashes($row['UNIT'])."',
							PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							TOTAL = REPLACE('".addslashes($total_rp)."',',',''),
							TRIGGER_UPDATE = NULL,
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE,
							DELETE_TIME = NULL,
							DELETE_USER = NULL
						 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA ALAT KERJA NON PANEN', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA ALAT KERJA NON PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
				$sql = "INSERT INTO TN_ALAT_KERJA_NON_PANEN (PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, MATERIAL_CODE, PRICE, UNIT, TOTAL, INSERT_USER, 
						INSERT_TIME)
						VALUES (
								TO_DATE('{$this->_period}','DD-MM-RRRR'),
								'".addslashes($region_code)."',
								'".addslashes($row['BA_CODE'])."',
								'".addslashes($row['ACTIVITY_CODE'])."',
								'".addslashes($row['MATERIAL_CODE'])."',
								REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
								'".addslashes($row['UNIT'])."',
								REPLACE('".addslashes($total_rp)."',',',''),
								'{$this->_userName}',
								SYSDATE
							)";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'NORMA ALAT KERJA NON PANEN', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'NORMA ALAT KERJA NON PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE NORMA ALAT KERJA NON PANEN **********************************************
		
		return $result;
    }
	
	//update summary
	public function updateSummaryNormaAlatKerjaNonPanen($param = array())
	{
		$where = '';
		//select data yg ada di TN_ALAT_KERJA_NON_PANEN
		if ($param['ACTIVITY_CODE']){
			$where .= "AND ACTIVITY_CODE LIKE '%{$param['ACTIVITY_CODE']}%'";
		}
		
		if ($param['sub_cost_element']){
			$where .= "AND MATERIAL_CODE LIKE '%{$param['sub_cost_element']}%'";
		}
		
		if ($param['key_find']){
			$where .= "AND BA_CODE LIKE '%{$param['key_find']}%'";
		}
		
		$sql = "
			SELECT BA_CODE, ACTIVITY_CODE, SUM(TOTAL) TOTAL
			FROM TN_ALAT_KERJA_NON_PANEN
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				AND DELETE_USER IS NULL
				$where
			GROUP BY BA_CODE, ACTIVITY_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		foreach ($rows as $idx => $row) {
			try {
				//hapus data yang ada
				$sqlDelete = "
					DELETE FROM TN_ALAT_KERJA_NON_PANEN_SUM
					WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
						AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				";
				//log file penghapusan data
				$this->_global->deleteDataLogFile($sqlDelete);
				
				$this->_db->query($sqlDelete);
				$this->_db->commit();							
			} catch (Exception $e) {
				
			}
			
			try {
				//insert DB
				$sql = "
					INSERT INTO TN_ALAT_KERJA_NON_PANEN_SUM (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, TOTAL, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".addslashes($row['BA_CODE'])."',
						'".addslashes($row['ACTIVITY_CODE'])."',
						'".addslashes($row['TOTAL'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
						
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'SUMMARY TOTAL HARGA ALAT KERJA NON PANEN', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'SUMMARY TOTAL HARGA ALAT KERJA NON PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
			}
			
			//update RKT yang menggunakan harga tools
			//$this->updateInheritanceData($row);
		}	
	}
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE RKT LC **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktLc();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT LC', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT LC', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT LC **********************************************
		
		// ********************************************** UPDATE RKT MANUAL - NON INFRA **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktManualNonInfra();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - NON INFRA', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT MANUAL - NON INFRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT MANUAL - NON INFRA **********************************************
		
		// ********************************************** UPDATE RKT MANUAL - NON INFRA + OPSI **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktManualNonInfraOpsi();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - NON INFRA + OPSI', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT MANUAL - NON INFRA + OPSI', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT MANUAL - NON INFRA + OPSI **********************************************
		
		// ********************************************** UPDATE RKT MANUAL - INFRA **********************************************		
		//reset param
		$param = array();
		
		$model = new Application_Model_RktManualInfra();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - INFRA', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT MANUAL - INFRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT MANUAL - INFRA **********************************************
		
		// ********************************************** UPDATE RKT TANAM **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktTanam();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT TANAM', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT TANAM', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT TANAM **********************************************
		
		// ********************************************** UPDATE RKT TANAM - MANUAL **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktTanamManual();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT TANAM - MANUAL', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT TANAM - MANUAL', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT TANAM - MANUAL **********************************************
		
		// ********************************************** UPDATE RKT PERKERASAN JALAN **********************************************	
		//reset param
		$param = array();
		
		$model = new Application_Model_RktPerkerasanJalan();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('TOOLS', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PERKERASAN JALAN', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');					
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PERKERASAN JALAN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT PERKERASAN JALAN **********************************************
		
		// ********************************************** UPDATE RKT PUPUK - DIST BIAYA NORMAL **********************************************
		if($row['ACTIVITY_CODE'] == '43750' || $row['ACTIVITY_CODE'] == '43760'){
			//reset param
			$param = array();
			
			$model = new Application_Model_RktPupukDistribusiBiayaNormal();
				
			//set parameter sesuai data yang diupdate
			$param['key_find'] = $row['BA_CODE'];
			
			try {	
				$model->calculateAllItem($param);
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PUPUK - DIST BIAYA NORMAL', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PUPUK - DIST BIAYA NORMAL', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
		// ********************************************** END OF UPDATE RKT PUPUK - DIST BIAYA NORMAL **********************************************
		
		// ********************************************** UPDATE RKT PUPUK - DIST BIAYA SISIP **********************************************
		if($row['ACTIVITY_CODE'] == '43750' || $row['ACTIVITY_CODE'] == '43760'){
			//reset param
			$param = array();
			
			$model = new Application_Model_RktPupukDistribusiBiayaSisip();
				
			//set parameter sesuai data yang diupdate
			$param['key_find'] = $row['BA_CODE'];
			
			try {	
				$model->calculateAllItem($param);
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PUPUK - DIST BIAYA SISIP', '', 'UPDATED FROM NORMA ALAT KERJA - NON PANEN');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PUPUK - DIST BIAYA SISIP', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
		}
		// ********************************************** END OF UPDATE RKT PUPUK - DIST BIAYA SISIP **********************************************
		return $result;
	}
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "UPDATE TN_ALAT_KERJA_NON_PANEN
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'NORMA ALAT KERJA NON PANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'NORMA ALAT KERJA NON PANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
	
	//cari activity_code berdasarkan rowid
	public function getActivityCode($rowid)
    {
		$sql = "SELECT ACTIVITY_CODE
				FROM TN_ALAT_KERJA_NON_PANEN
				WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
		$result = $this->_db->fetchOne($sql);

        return $result;
    }
}

