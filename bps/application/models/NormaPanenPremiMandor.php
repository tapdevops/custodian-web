<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Panen Premi Mandor
Function 			:	- getList					: menampilkan list norma Panen Premi Mandor
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/06/2013
Update Terakhir		:	25/06/2013
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save dan save temp
YULIUS 16/07/2014	:	- set field FLAG_TEMP pada function getData, save, saveTemp
=========================================================================================================================
*/
class Application_Model_NormaPanenPremiMandor
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
				   norma.PREMI_MANDOR_CODE, 
				   norma.DESCRIPTION, 
				   norma.MIN_YIELD, 
				   norma.MAX_YIELD, 
				   norma.MIN_OER, 
				   norma.MAX_OER, 
				   norma.VALUE,
				   norma.FLAG_TEMP,
		           (YB.TON_BUDGET / HS.HA_PLANTED / 12) YIELD
			FROM TN_PANEN_PREMI_MANDOR norma
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			LEFT JOIN (SELECT PPB.PERIOD_BUDGET, PPB.BA_CODE, SUM(PPB.TON_BUDGET) TON_BUDGET
					   FROM TR_PRODUKSI_PERIODE_BUDGET PPB
					   GROUP BY PPB.PERIOD_BUDGET, PPB.BA_CODE) YB
				ON YB.PERIOD_BUDGET = norma.PERIOD_BUDGET
				AND YB.BA_CODE = norma.BA_CODE
		   LEFT JOIN (SELECT HS.PERIOD_BUDGET, HS.BA_CODE, SUM(HS.HA_PLANTED) HA_PLANTED
					  FROM TM_HECTARE_STATEMENT HS
					  WHERE HS.MATURITY_STAGE_SMS2 = 'TM'
					  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS
				ON HS.PERIOD_BUDGET = norma.PERIOD_BUDGET
				AND HS.BA_CODE = norma.BA_CODE
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
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PREMI_MANDOR_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MIN_YIELD) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MAX_YIELD) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MIN_OER) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MAX_OER) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.VALUE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.BA_CODE, norma.PREMI_MANDOR_CODE
		";
		return $query;
	}
	
	//menampilkan list norma Panen Premi Mandor
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
	
	//simpan data
	public function save($row = array())
    { 
        $sql = "UPDATE TN_PANEN_PREMI_MANDOR
					SET VALUE = REPLACE('".addslashes($row['VALUE'])."', ',', ''),
						FLAG_TEMP = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	public function saveTemp($row = array())
    { 
		
		$sql = "UPDATE TN_PANEN_PREMI_MANDOR
					SET VALUE = REPLACE('".addslashes($row['VALUE'])."', ',', ''),
						FLAG_TEMP = 'Y',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
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
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - PREMI MANDOR');
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
}

