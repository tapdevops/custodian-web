<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Checkroll HK
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Checkroll HK
						- save				: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/05/2013
Update Terakhir		:	30/05/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_CheckrollHk
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
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (A.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (A.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   A.BA_CODE,
				   A.EMPLOYEE_STATUS,
				   A.HARI_SETAHUN,
				   A.MINGGU_SETAHUN,
				   A.LIBUR_SETAHUN,
				   A.HK,
				   A.CUTI,
				   A.SAKIT,
				   A.IZIN,
				   A.HAID,
				   A.HKE,
				   A.FLAG_TEMP
			  FROM TM_CHECKROLL_HK A
			  LEFT JOIN TM_ORGANIZATION B
			  ON A.BA_CODE = B.BA_CODE
			 WHERE A.DELETE_USER IS NULL
        ";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER(B.REGION_CODE) LIKE UPPER('%".$this->_siteCode."%')";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER(A.BA_CODE) LIKE UPPER('%".$this->_siteCode."%')";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(A.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(A.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.EMPLOYEE_STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HARI_SETAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.MINGGU_SETAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.LIBUR_SETAHUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HK) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.CUTI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.SAKIT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.IZIN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HAID) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HKE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY A.BA_CODE, A.EMPLOYEE_STATUS
		";		
		return $query;
	}
	
	//menampilkan list Checkroll HK
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
        $result = true;
		$hk = $this->_formula->cal_MasterCheckrollHk_Hk($row);
		$hke = $this->_formula->cal_MasterCheckrollHk_Hke($row);
		
			$sql = "
				UPDATE TM_CHECKROLL_HK
				SET  
					HARI_SETAHUN = REPLACE('".addslashes($row['HARI_SETAHUN'])."', ',', ''),
					MINGGU_SETAHUN = REPLACE('".addslashes($row['MINGGU_SETAHUN'])."', ',', ''),
					LIBUR_SETAHUN = REPLACE('".addslashes($row['LIBUR_SETAHUN'])."', ',', ''),
					HK = REPLACE('".addslashes($hk)."', ',', ''),
					CUTI = REPLACE('".addslashes($row['CUTI'])."', ',', ''),
					SAKIT = REPLACE('".addslashes($row['SAKIT'])."', ',', ''),
					IZIN = REPLACE('".addslashes($row['IZIN'])."', ',', ''),
					HAID = REPLACE('".addslashes($row['HAID'])."', ',', ''),
					HKE = REPLACE('".addslashes($hke)."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP = NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		$hk = $this->_formula->cal_MasterCheckrollHk_Hk($row);
		$hke = $this->_formula->cal_MasterCheckrollHk_Hke($row);
		
			$sql = "
				UPDATE TM_CHECKROLL_HK
				SET  
					HARI_SETAHUN = REPLACE('".addslashes($row['HARI_SETAHUN'])."', ',', ''),
					MINGGU_SETAHUN = REPLACE('".addslashes($row['MINGGU_SETAHUN'])."', ',', ''),
					LIBUR_SETAHUN = REPLACE('".addslashes($row['LIBUR_SETAHUN'])."', ',', ''),
					HK = REPLACE('".addslashes($hk)."', ',', ''),
					CUTI = REPLACE('".addslashes($row['CUTI'])."', ',', ''),
					SAKIT = REPLACE('".addslashes($row['SAKIT'])."', ',', ''),
					IZIN = REPLACE('".addslashes($row['IZIN'])."', ',', ''),
					HAID = REPLACE('".addslashes($row['HAID'])."', ',', ''),
					HKE = REPLACE('".addslashes($hke)."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP = 'Y'
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************* UPDATE RKT CHECKROLL *********************************************
		$param = array();
		$model = new Application_Model_NormaCheckroll();
			
		//set parameter sesuai data yang diupdate
		$param['key_find'] = $row['BA_CODE'];
		$param['employee_status'] = $row['EMPLOYEE_STATUS'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {		
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT CHECKROLL', '', 'UPDATED FROM MASTER CHECKROLL HK');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT CHECKROLL', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }
		// ********************************************* END OF UPDATE RKT CHECKROLL *********************************************
		return $result;
	}
}

