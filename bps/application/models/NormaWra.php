<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma WRA
Function 			:	- getList					: menampilkan list norma WRA
						- save						: simpan data
						- updateSummaryNormaWra		: summary norma WRA
						- delete					: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	12/06/2013
Update Terakhir		:	12/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaWra
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
            SELECT ROWIDTOCHAR(norma.ROWID) row_id, rownum, 
				   to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   norma.BA_CODE, 
				   norma.FLAG_TEMP,
				   norma.GROUP_WRA_CODE, 
				   norma.QTY_ROTASI, 
				   norma.ROTASI_TAHUN, 
				   norma.QTY_TAHUN, 
				   norma.PRICE_QTY_TAHUN, 
				   norma.RP_QTY,
				   (SELECT par.PARAMETER_VALUE
					FROM T_PARAMETER_VALUE par
					WHERE par.PARAMETER_VALUE_CODE = norma.GROUP_WRA_CODE
						AND par.PARAMETER_CODE = 'WRA_GROUPING'
					) as GROUP_WRA_DESC,
					norma.SUB_WRA_GROUP, 
					material.UOM, 
					material.MATERIAL_NAME SUB_WRA_GROUP_DESC, 
					norma_harga.PRICE HARGA_INFLASI,
					(SELECT sum.TOTAL_RP_QTY
					FROM TN_WRA_SUM sum
					WHERE sum.PERIOD_BUDGET = norma.PERIOD_BUDGET
						AND sum.BA_CODE = norma.BA_CODE
					) as TOTAL_RP_QTY
			FROM TN_WRA norma
			LEFT JOIN TN_HARGA_BARANG norma_harga
				ON norma.SUB_WRA_GROUP = norma_harga.MATERIAL_CODE
				AND norma.BA_CODE = norma_harga.BA_CODE
				AND norma.PERIOD_BUDGET = norma_harga.PERIOD_BUDGET
			LEFT JOIN TM_MATERIAL material
				ON norma.SUB_WRA_GROUP = material.MATERIAL_CODE
				AND norma.BA_CODE = material.BA_CODE
				AND norma.PERIOD_BUDGET = material.PERIOD_BUDGET
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			WHERE norma.DELETE_USER IS NULL
				AND norma.GROUP_WRA_CODE = '4'
        ";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
				
		//filter grup wra
		if ($params['wra_group_code'] != '') {
			$query .= "
                AND UPPER(norma.GROUP_WRA_CODE) LIKE UPPER('%".$params['wra_group_code']."%')
            ";
        }
		
		//filter job type
		if ($params['sub_wra_group'] != '') {
			$query .= "
                AND UPPER(norma.SUB_WRA_GROUP) LIKE UPPER('%".$params['sub_wra_group']."%')
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
					OR UPPER(norma.SUB_WRA_GROUP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.ROTASI_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PRICE_QTY_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_QTY) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.SUB_WRA_GROUP
		";//echo '::XXXXX::'.$query;
		
		return $query;
	}
	
	//menampilkan list norma WRA
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
	
	//ambil data dari DB
    public function getData1($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR(norma.ROWID) row_id, rownum, 
					to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
					norma.BA_CODE, 
					param.PARAMETER_VALUE_CODE as GROUP_WRA_CODE,
					param.PARAMETER_VALUE as GROUP_WRA_DESC,
					norma.SUB_WRA_GROUP, 
					norma.FLAG_TEMP,
					jobtype.JOB_DESCRIPTION as SUB_WRA_GROUP_DESC,
					CASE
						WHEN norma.GROUP_WRA_CODE = '1'
							THEN (SELECT jamkerja.JAM_KERJA
									  FROM TM_STANDART_JAM_KERJA_WRA jamkerja
									  WHERE norma.PERIOD_BUDGET = jamkerja.PERIOD_BUDGET
									  AND norma.BA_CODE = jamkerja.BA_CODE)
						WHEN (norma.GROUP_WRA_CODE = '2' or norma.GROUP_WRA_CODE = '3') 
							THEN (SELECT rkt_cr.MPP_PERIOD_BUDGET
									  FROM TR_RKT_CHECKROLL rkt_cr
									  WHERE norma.SUB_WRA_GROUP = rkt_cr.JOB_CODE
									  AND norma.PERIOD_BUDGET = rkt_cr.PERIOD_BUDGET
									  AND norma.BA_CODE = rkt_cr.BA_CODE
									  and rkt_cr.EMPLOYEE_STATUS = 'KT')
					END AS QTY_ROTASI, 
					NVL(norma.ROTASI_TAHUN, 12) ROTASI_TAHUN, 
					norma.QTY_TAHUN, 
					CASE
						WHEN norma.GROUP_WRA_CODE = '2'
							THEN (SELECT rkt_cr.GP_INFLASI HARGA_INFLASI
									  FROM TR_RKT_CHECKROLL rkt_cr
									  WHERE norma.SUB_WRA_GROUP = rkt_cr.JOB_CODE
									  AND norma.PERIOD_BUDGET = rkt_cr.PERIOD_BUDGET
									  AND norma.BA_CODE = rkt_cr.BA_CODE
									  and rkt_cr.EMPLOYEE_STATUS = 'KT')
						WHEN norma.GROUP_WRA_CODE = '3'
							THEN (SELECT rkt_cr.TOTAL_TUNJANGAN_WRA HARGA_INFLASI
									  FROM TR_RKT_CHECKROLL rkt_cr
									  WHERE norma.SUB_WRA_GROUP = rkt_cr.JOB_CODE
									  AND norma.PERIOD_BUDGET = rkt_cr.PERIOD_BUDGET
									  AND norma.BA_CODE = rkt_cr.BA_CODE
									  and rkt_cr.EMPLOYEE_STATUS = 'KT')
					END AS HARGA_INFLASI, 
					norma.PRICE_QTY_TAHUN, 
					norma.RP_QTY
			FROM TN_WRA norma
			LEFT JOIN TM_JOB_TYPE jobtype
				ON norma.SUB_WRA_GROUP = jobtype.JOB_CODE
			LEFT JOIN T_PARAMETER_VALUE param
				ON param.PARAMETER_CODE = 'WRA_GROUPING'
				AND param.PARAMETER_VALUE_CODE = norma.GROUP_WRA_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			WHERE norma.GROUP_WRA_CODE <> '4'
				AND norma.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		if ($params['JOB_CODE'] != '') {
			$query .= "
                AND UPPER(norma.SUB_WRA_GROUP) IN ('".$params['JOB_CODE']."')
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
					OR UPPER(norma.SUB_WRA_GROUP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(jobtype.JOB_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.ROTASI_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PRICE_QTY_TAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_QTY) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.GROUP_WRA_CODE, norma.SUB_WRA_GROUP
		";
		
		return $query;
	}
	
	//menampilkan list norma WRA
    public function getList1($params = array())
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
        
		$sql = "SELECT COUNT(*) FROM ({$this->getData1($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$this->getData1($params)} {$end}");
		
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
				AND GROUP_WRA_CODE = '{$params['GROUP_WRA_CODE']}'
				AND SUB_WRA_GROUP  = '{$params['SUB_WRA_GROUP']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan temporary data
	public function saveTemp($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
			
		if ($row['ROW_ID']){
			$sql = "UPDATE TN_WRA
					SET SUB_WRA_GROUP = '".addslashes($row['SUB_WRA_GROUP'])."',
						QTY_ROTASI = REPLACE('".addslashes($row['QTY_ROTASI'])."',',',''),
						ROTASI_TAHUN = REPLACE('".addslashes($row['ROTASI_TAHUN'])."',',',''),
						QTY_TAHUN = NULL,
						PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
						PRICE_QTY_TAHUN = NULL,
						RP_QTY = NULL,
						TRIGGER_UPDATE = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL,
						FLAG_TEMP = 'Y'
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					";
		}else{
			$sql = "INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, PRICE, PRICE_QTY_TAHUN, RP_QTY, 
					INSERT_USER, INSERT_TIME, FLAG_TEMP)
					VALUES (
							TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
							'".addslashes($row['BA_CODE'])."',
							'".addslashes($row['GROUP_WRA_CODE'])."',
							'".addslashes($row['SUB_WRA_GROUP'])."',
							REPLACE('".addslashes($row['QTY_ROTASI'])."',',',''),
							REPLACE('".addslashes($row['ROTASI_TAHUN'])."',',',''),
							NULL,
							REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							NULL,
							NULL,
							'{$this->_userName}',
							SYSDATE,
							'Y'
						);
					";
		}
				 
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//simpan data
	public function save($row = array())
    {
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$qty_tahun = $this->_formula->cal_NormaWra_QtyTahun($row);
		$harga_qty_tahun = $this->_formula->cal_NormaWra_HargaQtyTahun($row);
		$rp_qty = $this->_formula->cal_NormaWra_RpQty($row);
			
		if ($row['ROW_ID']){
			$sql = "UPDATE TN_WRA
					SET SUB_WRA_GROUP = '".addslashes($row['SUB_WRA_GROUP'])."',
						QTY_ROTASI = REPLACE('".addslashes($row['QTY_ROTASI'])."',',',''),
						ROTASI_TAHUN = REPLACE('".addslashes($row['ROTASI_TAHUN'])."',',',''),
						QTY_TAHUN = REPLACE('".addslashes($qty_tahun)."',',',''),
						PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
						PRICE_QTY_TAHUN = REPLACE('".addslashes($harga_qty_tahun)."',',',''),
						RP_QTY = REPLACE('".addslashes($rp_qty)."',',',''),
						TRIGGER_UPDATE = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL,
						FLAG_TEMP = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					";
		}else{
			$sql = "INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, PRICE, PRICE_QTY_TAHUN, RP_QTY, 
					INSERT_USER, INSERT_TIME, FLAG_TEMP)
					VALUES (
							TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
							'".addslashes($row['BA_CODE'])."',
							'".addslashes($row['GROUP_WRA_CODE'])."',
							'".addslashes($row['SUB_WRA_GROUP'])."',
							REPLACE('".addslashes($row['QTY_ROTASI'])."',',',''),
							REPLACE('".addslashes($row['ROTASI_TAHUN'])."',',',''),
							REPLACE('".addslashes($qty_tahun)."',',',''),
							REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							REPLACE('".addslashes($harga_qty_tahun)."',',',''),
							REPLACE('".addslashes($rp_qty)."',',',''),
							'{$this->_userName}',
							SYSDATE,
							NULL
						);
					";
		} 
		//die($sql);
				 
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//update summary
	public function updateSummaryNormaWra($row = array())
	{
		$result = true;
		$sql = "
			DELETE FROM TN_WRA_SUM
			WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}';
		";
		
		///////////////////////////// SUMMARY TOTAL HARGA WRA /////////////////////////////
		$sqlsum = "
			SELECT SUM(RP_QTY) TOTAL
			FROM TN_WRA
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}'
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND DELETE_USER IS NULL
		";
		$sum_price = $this->_db->fetchOne($sqlsum);
		
		//insert DB
		$sql .= "
			INSERT INTO TN_WRA_SUM (PERIOD_BUDGET, BA_CODE, TOTAL_RP_QTY, INSERT_USER, INSERT_TIME)
			VALUES (
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
				'".addslashes($row['BA_CODE'])."',
				'".addslashes($sum_price)."',
				'{$this->_userName}',
				SYSDATE
			);
		";
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		//$this->updateInheritanceData($row);
		return $result;		
	}
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************* UPDATE RKT VRA *********************************************
		//reset data
		$param = array();		
		
		$model = new Application_Model_RktVra();
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {				
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				$model->updateSummaryRktVra();
			
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT VRA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT VRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
		}
		// ********************************************* END OF UPDATE RKT CHECKROLL *********************************************
		return $result;
	}
	
	//hapus data
	public function delete($row = array())
    {
		$result = true;
		$sql = "UPDATE TN_WRA
				SET DELETE_USER = '{$this->_userName}',
					DELETE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'";
		
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
	}
	
	//wra group code
	public function getWraGroup()
	{
		$sql = "
			SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'WRA_GROUPING'
				AND PARAMETER_VALUE_CODE = '4'
		";
		$result = $this->_db->fetchRow($sql);
		
		return $result;
	}
	
	//check VRA
	public function checkVra($row = array())
	{
		$sql = "
			SELECT COUNT ( * ) jml_vra
			FROM TR_RKT_VRA
			WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}'";
		$result = $this->_db->fetchRow($sql);
		
		return $result;
	}
}

