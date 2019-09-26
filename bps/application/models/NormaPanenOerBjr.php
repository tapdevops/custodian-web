<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma Panen OER BJR
Function 			:	- getList					: menampilkan list norma Panen OER BJR
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	17/06/2013
Update Terakhir		:	08/07/2014
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save, saveTemp
YULIUS 16/07/2014	:	- set field FLAG_TEMP pada function getData, save, saveTemp						  
						 
=========================================================================================================================
*/
class Application_Model_NormaPanenOerBjr
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
				   norma.BJR_MIN,
				   norma.BJR_MAX,
           norma.JANJANG_BASIS_MANDOR,
				   norma.JANJANG_BASIS_MANDOR_JUMAT,
				   norma.BJR_BUDGET,
				   norma.JANJANG_OPERATION,
				   norma.OVER_BASIS_JANJANG,
				   norma.OER_MIN,
				   norma.OER_MAX,
				   norma.PREMI_PANEN,
				   norma.NILAI,
				   norma.FLAG_TEMP,
				   oer_ba.OER,
				   var.VALUE ASUMSI_OVER_BASIS
			 FROM TN_PANEN_OER_BJR norma
			 LEFT JOIN TM_OER_BA oer_ba
				ON norma.PERIOD_BUDGET = oer_ba.PERIOD_BUDGET
				AND norma.BA_CODE = oer_ba.BA_CODE
			 LEFT JOIN TN_PANEN_VARIABLE var
				ON norma.PERIOD_BUDGET = var.PERIOD_BUDGET
				AND norma.BA_CODE = var.BA_CODE
				AND var.PANEN_CODE = 'ASUM_OVR_BASIS'
			 LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			 WHERE norma.DELETE_USER IS NULL
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
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BJR_MIN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BJR_MAX) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.OER_MIN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.OER_MAX) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PREMI_PANEN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BJR_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JANJANG_BASIS_MANDOR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JANJANG_OPERATION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.OVER_BASIS_JANJANG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.OER_BA) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.NILAI) LIKE UPPER('%".$params['search']."%')	
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.BJR_MIN, norma.BJR_MAX
		";
		
		return $query;
	}
	
	//menampilkan list norma Panen OER BJR
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
	
	//hapus data di norma infrastruktur, insert inputan user di norma infrastruktur
	public function saveTemp($row = array())
    { 
		$over_basis_janjang = $this->_formula->cal_NormaPanenOerBjr_OverBasisJanjang($row);
		//$row['OVER_BASIS_JANJANG'] = $over_basis_janjang;
		$janjang_operation = $this->_formula->cal_NormaPanenOerBjr_JanjangOperation($row);
		$row['JANJANG_OPERATION'] = $janjang_operation;
		$nilai = $this->_formula->cal_NormaPanenOerBjr_Nilai($row);
		//die($row['OVER_BASIS_JANJANG']." - ".$row['JANJANG_OPERATION']);
		
		$sql = "UPDATE TN_PANEN_OER_BJR
					SET BJR_BUDGET = REPLACE('".addslashes($row['BJR_BUDGET'])."', ',', ''),
            JANJANG_BASIS_MANDOR = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR'])."', ',', ''),
						JANJANG_BASIS_MANDOR_JUMAT = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR_JUMAT'])."', ',', ''),
						JANJANG_OPERATION = REPLACE('".addslashes($janjang_operation)."', ',', ''),
						PREMI_PANEN = REPLACE('".addslashes($row['PREMI_PANEN'])."', ',', ''),  
						OER_BA = '".addslashes($row['OER'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						FLAG_TEMP = 'Y',
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $over_basis_janjang = $this->_formula->cal_NormaPanenOerBjr_OverBasisJanjang($row);
		$row['OVER_BASIS_JANJANG'] = $over_basis_janjang;
		$janjang_operation = $this->_formula->cal_NormaPanenOerBjr_JanjangOperation($row);
		$row['JANJANG_OPERATION'] = $janjang_operation;
		$nilai = $this->_formula->cal_NormaPanenOerBjr_Nilai($row);
		//die($nilai);
			$sql = "UPDATE TN_PANEN_OER_BJR
					SET BJR_BUDGET = REPLACE('".addslashes($row['BJR_BUDGET'])."', ',', ''),
            JANJANG_BASIS_MANDOR = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR'])."', ',', ''),
						JANJANG_BASIS_MANDOR_JUMAT = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR_JUMAT'])."', ',', ''),
						JANJANG_OPERATION = REPLACE('".addslashes($janjang_operation)."', ',', ''),
						PREMI_PANEN = REPLACE('".addslashes($row['PREMI_PANEN'])."', ',', ''),  
						OER_BA = '".addslashes($row['OER'])."',
						NILAI  = '".addslashes($nilai)."',
						OVER_BASIS_JANJANG = REPLACE('".addslashes($over_basis_janjang)."', ',', ''),
						FLAG_TEMP = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//update asumsi over basis
	public function updateAsumsiOverBasis($row = array())
    { 
        $result = true;
		
		try{
			//update asumsi over budget
			$sql = "UPDATE TN_PANEN_VARIABLE
					SET VALUE = '".addslashes($row['ASUMSI_OVER_BASIS'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE BA_CODE = '{$row['BA_CODE']}'
						AND to_char(PERIOD_BUDGET,'RRRR') = '{$row['PERIOD_BUDGET']}'
						AND PANEN_CODE = 'ASUM_OVR_BASIS'
			";
			$this->_db->query($sql);
			
			//log DB
			$this->_global->insertLog('UPDATE SUCCESS', 'ASUMSI OVER BASIS', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('UPDATE FAILED', 'ASUMSI OVER BASIS', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
			
			//return value
			$result = false;
		}
        return $result;
    }
	
	//simpan data
	public function saveAll($row = array())
    { 
        $result = true;
		
		try{
			$params['budgetperiod'] = $row['PERIOD_BUDGET'];
			$params['key_find'] = $row['BA_CODE'];
			
			$records = $this->_db->fetchAll($this->getData($row));
						
			foreach ($records as $idx => $record) {
				$record['ASUMSI_OVER_BASIS'] = $row['ASUMSI_OVER_BASIS'];
				$this->save($record);
			}
			
			//log DB
			$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PANEN OER BJR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('UPDATE FAILED', 'NORMA PANEN OER BJR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
			
			//return value
			$result = false;
		}
		
		//if($result == true) $this->updateInheritanceData($row);
		
        return $result;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE NORMA PANEN - SUPERVISI **********************************************		
		//reset param
		$param = array();
		
		$model = new Application_Model_NormaPanenSupervisi();
			
		//set parameter sesuai data yang diupdate
		$param['key_find'] = $row['BA_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {		
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PANEN - SUPERVISI', '', 'UPDATED FROM NORMA PANEN - OER BJR');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PANEN - SUPERVISI', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF UPDATE NORMA PANEN - SUPERVISI **********************************************
		
		// ********************************************** UPDATE RKT PANEN **********************************************
		//reset param
		$param = array();
		
		$model = new Application_Model_RktPanen();
			
		//set parameter sesuai data yang diupdate
		$param['key_find'] = $row['BA_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {		
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - OER BJR');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF UPDATE RKT PANEN **********************************************
		
		return $result;
	}
	
	//kalkulasi data saat upload
	public function calculateData($row = array())
    { 
        $over_basis_janjang = $this->_formula->cal_NormaPanenOerBjr_OverBasisJanjang($row);
		$row['OVER_BASIS_JANJANG'] = $over_basis_janjang;
		$janjang_operation = $this->_formula->cal_NormaPanenOerBjr_JanjangOperation($row);
		$row['JANJANG_OPERATION'] = $janjang_operation;
		$nilai = $this->_formula->cal_NormaPanenOerBjr_Nilai($row);
			
		$sql = "UPDATE TN_PANEN_OER_BJR
				SET BJR_BUDGET = REPLACE('".addslashes($row['BJR_BUDGET'])."', ',', ''),
					JANJANG_BASIS_MANDOR = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR'])."', ',', ''),
					JANJANG_OPERATION = REPLACE('".addslashes($janjang_operation)."', ',', ''),
					PREMI_PANEN = REPLACE('".addslashes($row['PREMI_PANEN'])."', ',', ''),  
					OER_BA = '".addslashes($row['OER'])."',
					NILAI  = '".addslashes($nilai)."',
					OVER_BASIS_JANJANG = REPLACE('".addslashes($over_basis_janjang)."', ',', ''),
					FLAG_TEMP = NULL,
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
			";
			
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
}

