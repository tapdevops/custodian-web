<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Harga Borong
Function 			:	- getList					: menampilkan list norma harga borong
						- save						: simpan data
						- updateSummaryHargaBorong	: update summary
						- delete					: hapus data
						- calculateAllItem			: prosedur untuk kalkulasi seluruh item
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	20/06/2013
Revisi				:	

Revisi	||	PIC				||	TANGGAL			||	DESKRIPSI 		
1			Doni R				20-06-2013			Perbaikan query untuk menampilkan data berdasarkan region code
2			DONI R				19/06/2013			MENAMBAHKAN PENGIRMAN DATA REFERENCE_ROLE UNTUK VALIDASI FILTERING
=========================================================================================================================
*/
class Application_Model_NormaHargaBorong
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
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; 
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : YIR - 08/08/2014
		
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
			SELECT ROWIDTOCHAR (norma_harga_borong.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (norma_harga_borong.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   norma_harga_borong.REGION_CODE,
				   norma_harga_borong.BA_CODE,
				   norma_harga_borong.ACTIVITY_CODE,
				   norma_harga_borong.ACTIVITY_CLASS,
				   norma_harga_borong.SPESIFICATION,
				   norma_harga_borong.PRICE,
				   norma_harga_borong.PRICE_SITE,
				   norma_harga_borong.FLAG_TEMP,
				   act.DESCRIPTION ACTIVITY_DESCRIPTION,
				   act.UOM,
				   (SELECT PRICE
					  FROM TN_HARGA_BORONG_SUM
					 WHERE     PERIOD_BUDGET = norma_harga_borong.PERIOD_BUDGET
						   AND REGION_CODE = norma_harga_borong.REGION_CODE
						   AND ACTIVITY_CODE = norma_harga_borong.ACTIVITY_CODE
						   AND ACTIVITY_CLASS = norma_harga_borong.ACTIVITY_CLASS)
					  AVG_REGION,
				   (SELECT PRICE
					  FROM TN_HARGA_BORONG_SUM
					 WHERE     PERIOD_BUDGET = norma_harga_borong.PERIOD_BUDGET
						   AND REGION_CODE = 'ALL'
						   AND ACTIVITY_CODE = norma_harga_borong.ACTIVITY_CODE
						   AND ACTIVITY_CLASS = norma_harga_borong.ACTIVITY_CLASS)
					  AVG_PT,
				   (SELECT PRICE_SITE
					  FROM TN_HARGA_BORONG_SUM
					 WHERE     PERIOD_BUDGET = norma_harga_borong.PERIOD_BUDGET
						   AND REGION_CODE = norma_harga_borong.REGION_CODE
						   AND ACTIVITY_CODE = norma_harga_borong.ACTIVITY_CODE
						   AND ACTIVITY_CLASS = norma_harga_borong.ACTIVITY_CLASS)
					  AVG_REGION_SITE,
				   (SELECT PRICE_SITE
					  FROM TN_HARGA_BORONG_SUM
					 WHERE     PERIOD_BUDGET = norma_harga_borong.PERIOD_BUDGET
						   AND REGION_CODE = 'ALL'
						   AND ACTIVITY_CODE = norma_harga_borong.ACTIVITY_CODE
						   AND ACTIVITY_CLASS = norma_harga_borong.ACTIVITY_CLASS)
					  AVG_PT_SITE
			  FROM TN_HARGA_BORONG norma_harga_borong
				   LEFT JOIN TM_ACTIVITY act
					  ON norma_harga_borong.ACTIVITY_CODE = act.ACTIVITY_CODE
				   LEFT JOIN TM_ORGANIZATION B
					  ON norma_harga_borong.BA_CODE = B.BA_CODE
			 WHERE norma_harga_borong.DELETE_USER IS NULL
        ";
	
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma_harga_borong.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma_harga_borong.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(norma_harga_borong.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(norma_harga_borong.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma_harga_borong.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_borong.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_borong.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_borong.ACTIVITY_CLASS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_borong.SPESIFICATION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_borong.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(act.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(act.UOM) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma_harga_borong.BA_CODE, norma_harga_borong.ACTIVITY_CODE, norma_harga_borong.SPESIFICATION
		";
		
		return $query;
	}
	
	//menampilkan list norma harga barang
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
			FROM TN_WRA 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND ACTIVITY_CLASS  = '{$params['ACTIVITY_CLASS']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		if ($row['ROW_ID']){
			$sql = "
				UPDATE TN_HARGA_BORONG
				SET ACTIVITY_CLASS =  '{$row['ACTIVITY_CLASS']}',
					SPESIFICATION =  '{$row['SPESIFICATION']}',
					PRICE = REPLACE('{$row['PRICE']}',',',''),
					PRICE_SITE = REPLACE('{$row['PRICE_SITE']}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP = NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			$this->_global->createSqlFile($row['filename'], $sql);
		
		}else{
			
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			$sql = "
				INSERT INTO TN_HARGA_BORONG (PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, SPESIFICATION, PRICE, PRICE_SITE, INSERT_USER, INSERT_TIME, FLAG_TEMP)
				VALUES (
					TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') ,
					'".addslashes($region_code)."',
					'".addslashes($row['BA_CODE'])."',
					'".addslashes($row['ACTIVITY_CODE'])."',
					'".addslashes($row['ACTIVITY_CLASS'])."',
					'".addslashes($row['SPESIFICATION'])."',
					REPLACE('".addslashes($row['PRICE'])."',',',''),
					REPLACE('".addslashes($row['PRICE_SITE'])."',',',''),
					'{$this->_userName}',
					SYSDATE,
					NULL
				);
			";
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		return $result;
    }
	
	//simpan data
	public function saveTemp($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		if ($row['ROW_ID']){
			$sql = "
				UPDATE TN_HARGA_BORONG
				SET ACTIVITY_CLASS =  '{$row['ACTIVITY_CLASS']}',
					SPESIFICATION =  '{$row['SPESIFICATION']}',
					PRICE = REPLACE('{$row['PRICE']}',',',''),
					PRICE_SITE = REPLACE('{$row['PRICE_SITE']}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP = 'Y'
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			$this->_global->createSqlFile($row['filename'], $sql);
		
		}else{
			
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			$sql = "
				INSERT INTO TN_HARGA_BORONG (PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, SPESIFICATION, PRICE, PRICE_SITE, INSERT_USER, INSERT_TIME, FLAG_TEMP)
				VALUES (
					TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') ,
					'".addslashes($region_code)."',
					'".addslashes($row['BA_CODE'])."',
					'".addslashes($row['ACTIVITY_CODE'])."',
					'".addslashes($row['ACTIVITY_CLASS'])."',
					'".addslashes($row['SPESIFICATION'])."',
					REPLACE('".addslashes($row['PRICE'])."',',',''),
					REPLACE('".addslashes($row['PRICE_SITE'])."',',',''),
					'{$this->_userName}',
					SYSDATE,
					'Y'
				);
			";
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		return $result;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE RKT LC **********************************************
		//reset data
		$param = array();		
				
		$model = new Application_Model_RktLc();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT LC', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		//reset data
		$param = array();		
			
		$model = new Application_Model_RktManualNonInfra();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - NON INFRA', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		//reset data
		$param = array();		
				
		$model = new Application_Model_RktManualNonInfraOpsi();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - NON INFRA + OPSI', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		//reset data
		$param = array();		
		
		$model = new Application_Model_RktManualInfra();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT MANUAL - INFRA', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		//reset data
		$param = array();		
		
		$model = new Application_Model_RktTanam();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT TANAM', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		//reset data
		$param = array();		
			
		$model = new Application_Model_RktTanamManual();	
		
		$param['key_find'] = $row['BA_CODE'];
		$param['activity_code'] = $row['ACTIVITY_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT TANAM - MANUAL', '', 'UPDATED FROM NORMA HARGA BORONG');					
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
		return $result;
	}
	
	//update summary
	public function updateSummaryHargaBorong($params = array())
	{
		//avg region
		$sql = "
			SELECT 	PERIOD_BUDGET,
					REGION_CODE,
					ACTIVITY_CODE, 
					ACTIVITY_CLASS,
					AVG (PRICE) AS PRICE,
					AVG (PRICE_SITE) AS PRICE_SITE
            FROM TN_HARGA_BORONG
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
                  AND REGION_CODE = '".$params['REGION_CODE']."'
                  AND DELETE_TIME IS NULL
			GROUP BY PERIOD_BUDGET, REGION_CODE, ACTIVITY_CODE, ACTIVITY_CLASS
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$sql = "
					DELETE FROM TN_HARGA_BORONG_SUM
					WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
						AND ACTIVITY_CODE = '".$row['ACTIVITY_CODE']."'
						AND ACTIVITY_CLASS = '".$row['ACTIVITY_CLASS']."'
						AND REGION_CODE = '".$row['REGION_CODE']."';
						
					INSERT INTO TN_HARGA_BORONG_SUM (PERIOD_BUDGET, REGION_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, PRICE, PRICE_SITE, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['ACTIVITY_CODE']."',
						'".$row['ACTIVITY_CLASS']."',
						'".$row['PRICE']."',
						'".$row['PRICE_SITE']."',
						'{$this->_userName}',
						SYSDATE
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
		
		//avg national
		$sql = "
			SELECT 	PERIOD_BUDGET,
					ACTIVITY_CODE, 
					ACTIVITY_CLASS,
					AVG (PRICE) AS PRICE,
					AVG (PRICE_SITE) AS PRICE_SITE
            FROM TN_HARGA_BORONG
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
                  AND DELETE_TIME IS NULL
			GROUP BY PERIOD_BUDGET, ACTIVITY_CODE, ACTIVITY_CLASS
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$sql = "
					DELETE FROM TN_HARGA_BORONG_SUM
					WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
						AND ACTIVITY_CODE = '".$row['ACTIVITY_CODE']."'
						AND ACTIVITY_CLASS = '".$row['ACTIVITY_CLASS']."'
						AND REGION_CODE = 'ALL';
						
					INSERT INTO TN_HARGA_BORONG_SUM (PERIOD_BUDGET, REGION_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, PRICE, PRICE_SITE, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
						'ALL',
						'".$row['ACTIVITY_CODE']."',
						'".$row['ACTIVITY_CLASS']."',
						'".$row['PRICE']."',
						'".$row['PRICE_SITE']."',
						'{$this->_userName}',
						SYSDATE
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }

        return true;
	}
	
	//hapus data
	public function delete($row = array())
    {
		$result = true;
			$sql = "UPDATE TN_HARGA_BORONG
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'";
		
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
	}
	
	//kalkulasi seluruh data $this->_period
	public function calculateAllItem($region_code)
    {
		$result = true;
		try {
				$par = array(
                    'V_PERIOD_BUDGET'   => $this->_period,
                    'V_REGION_CODE'  	=> $region_code,
					'V_USER'  			=> $this->_userName
                ); 
                $sql = "
                    BEGIN
						PKG_BUDGET.CALC_ALL_AVG_NORMA_BORONG(to_date(:V_PERIOD_BUDGET,'dd-mm-rrrr') ,:V_REGION_CODE, :V_USER );
                    END;
                ";
			
				$statement = new Zend_Db_Statement_Oracle($this->_db, $sql);
                $statement->execute($par);
			
			//log DB
			$this->_global->insertLog('UPDATE SUCCESS', 'AVG NORMA HARGA BORONG', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('UPDATE FAILED', 'AVG NORMA HARGA BORONG', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

