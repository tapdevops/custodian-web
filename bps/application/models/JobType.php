<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Job Type
Function 			:	- getList			: menampilkan list job type
						- save				: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_JobType
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
    }
	
	//menampilkan list Jenis Pekerjaan
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
        
		$query = "
            SELECT ROWIDTOCHAR(ROWID) row_id, rownum, JOB_CODE, GROUP_CHECKROLL_CODE, JOB_TYPE, JOB_DESCRIPTION, STATUS
            FROM TM_JOB_TYPE
			WHERE DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(JOB_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP_CHECKROLL_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(JOB_TYPE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(JOB_DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(STATUS) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY JOB_CODE
		";
		
        $sql = "SELECT COUNT(*) FROM ({$query})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$query} {$end}");
		
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
			
		if ($row['ROW_ID']){
			try {
				$sql = "
					UPDATE TM_JOB_TYPE
					SET  
						GROUP_CHECKROLL_CODE = '".addslashes($row['GROUP_CHECKROLL_CODE'])."',
						JOB_TYPE = '".addslashes($row['JOB_TYPE'])."',
						JOB_DESCRIPTION = '".addslashes($row['JOB_DESCRIPTION'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MASTER JOB TYPE', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MASTER JOB TYPE', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "
					INSERT INTO TM_JOB_TYPE
					(JOB_CODE, GROUP_CHECKROLL_CODE, JOB_TYPE, JOB_DESCRIPTION, INSERT_USER, INSERT_TIME)
					VALUES (
						'".addslashes($row['JOB_CODE'])."',
						'".addslashes($row['GROUP_CHECKROLL_CODE'])."',
						'".addslashes($row['JOB_TYPE'])."',
						'".addslashes($row['JOB_DESCRIPTION'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MASTER JOB TYPE', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MASTER JOB TYPE', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
        return $result;
    }
}

