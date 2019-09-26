<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Sebaran Produksi
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Sebaran Produksi
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_SebaranProduksi
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
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
			SELECT ROWIDTOCHAR (SEBARAN.ROWID) ROW_ID,
				   ROWNUM,
				   TO_CHAR (SEBARAN.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   SEBARAN.BA_CODE,
				   SEBARAN.JAN,
				   SEBARAN.FEB,
				   SEBARAN.MAR,
				   SEBARAN.APR,
				   SEBARAN.MAY,
				   SEBARAN.JUN,
				   SEBARAN.JUL,
				   SEBARAN.AUG,
				   SEBARAN.SEP,
				   SEBARAN.OCT,
				   SEBARAN.NOV,
				   SEBARAN.DEC,
				   SEBARAN.FLAG_TEMP
			  FROM TM_SEBARAN_PRODUKSI SEBARAN 
			  LEFT JOIN TM_ORGANIZATION B
					  ON SEBARAN.BA_CODE = B.BA_CODE
			 WHERE SEBARAN.DELETE_USER IS NULL
		 ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(SEBARAN.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(SEBARAN.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(SEBARAN.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(SEBARAN.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(SEBARAN.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.JAN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.FEB) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.MAR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.APR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.MAY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.JUN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.JUL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.AUG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.SEP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.OCT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.NOV) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SEBARAN.DEC) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY SEBARAN.PERIOD_BUDGET, SEBARAN.BA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list Sebaran Produksi
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
		
		$sql = "
			UPDATE TM_SEBARAN_PRODUKSI
			SET JAN = REPLACE('".addslashes($row['JAN'])."', ',', ''),
				FEB = REPLACE('".addslashes($row['FEB'])."', ',', ''),
				MAR = REPLACE('".addslashes($row['MAR'])."', ',', ''),
				APR = REPLACE('".addslashes($row['APR'])."', ',', ''),
				MAY = REPLACE('".addslashes($row['MAY'])."', ',', ''),
				JUN = REPLACE('".addslashes($row['JUN'])."', ',', ''),
				JUL = REPLACE('".addslashes($row['JUL'])."', ',', ''),
				AUG = REPLACE('".addslashes($row['AUG'])."', ',', ''),
				SEP = REPLACE('".addslashes($row['SEP'])."', ',', ''),
				OCT = REPLACE('".addslashes($row['OCT'])."', ',', ''),
				NOV = REPLACE('".addslashes($row['NOV'])."', ',', ''),
				DEC = REPLACE('".addslashes($row['DEC'])."', ',', ''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP=NULL
			 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
	
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		
		$sql = "
			UPDATE TM_SEBARAN_PRODUKSI
			SET JAN = REPLACE('".addslashes($row['JAN'])."', ',', ''),
				FEB = REPLACE('".addslashes($row['FEB'])."', ',', ''),
				MAR = REPLACE('".addslashes($row['MAR'])."', ',', ''),
				APR = REPLACE('".addslashes($row['APR'])."', ',', ''),
				MAY = REPLACE('".addslashes($row['MAY'])."', ',', ''),
				JUN = REPLACE('".addslashes($row['JUN'])."', ',', ''),
				JUL = REPLACE('".addslashes($row['JUL'])."', ',', ''),
				AUG = REPLACE('".addslashes($row['AUG'])."', ',', ''),
				SEP = REPLACE('".addslashes($row['SEP'])."', ',', ''),
				OCT = REPLACE('".addslashes($row['OCT'])."', ',', ''),
				NOV = REPLACE('".addslashes($row['NOV'])."', ',', ''),
				DEC = REPLACE('".addslashes($row['DEC'])."', ',', ''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP = 'Y'
			 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
	
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
    }
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "SELECT * 
					FROM TM_SEBARAN_PRODUKSI
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$row = $this->_db->fetchRow($sql);
			
			$sql = "UPDATE TM_SEBARAN_PRODUKSI
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MASTER SEBARAN PRODUKSI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MASTER SEBARAN PRODUKSI', '', $e->getCode());
			
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
		
		// ********************************************** UPDATE RKT PANEN **********************************************	
		//reset data
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
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM SEBARAN PRODUKSI');
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

