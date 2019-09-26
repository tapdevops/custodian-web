<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Mapping Job Type - VRA
Function 			:	- getList			: menampilkan list Mapping Job Type - VRA
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
class Application_Model_MappingJobTypeVra
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
	
	//menampilkan list Mapping Job Type - VRA
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
            SELECT ROWIDTOCHAR(mapping.ROWID) row_id, rownum, mapping.JOB_CODE, mapping.RVRA_CODE, mapping.VRA_CODE, job.JOB_DESCRIPTION, rvra.SUB_RVRA_DESCRIPTION,
				   vra.TYPE
			FROM TM_MAPPING_JOB_TYPE_VRA mapping
			LEFT JOIN TM_JOB_TYPE job
				ON mapping.JOB_CODE = job.JOB_CODE
			LEFT JOIN TM_RVRA rvra
				ON mapping.RVRA_CODE = rvra.SUB_RVRA_CODE
			LEFT JOIN TM_VRA vra
				ON mapping.VRA_CODE = vra.VRA_CODE
			WHERE mapping.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(mapping.JOB_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.RVRA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.VRA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(job.JOB_DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(rvra.SUB_RVRA_DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(vra.TYPE) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY mapping.JOB_CODE, mapping.RVRA_CODE, mapping.VRA_CODE
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
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
			SELECT ROWIDTOCHAR(ROWID) ROW_ID 
			FROM TM_MAPPING_JOB_TYPE_VRA 
			WHERE JOB_CODE = '{$params['JOB_CODE']}'
				AND RVRA_CODE  = '{$params['RVRA_CODE']}'
				AND VRA_CODE  = '{$params['VRA_CODE']}'
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
			try {
				$sql = "
					UPDATE TM_MAPPING_JOB_TYPE_VRA
					SET JOB_CODE = '".addslashes($row['JOB_CODE'])."', 
						RVRA_CODE = '".addslashes($row['RVRA_CODE'])."',
						VRA_CODE = '".addslashes($row['VRA_CODE'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MAPPING JOB TYPE - VRA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MAPPING JOB TYPE - VRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "
					INSERT INTO TM_MAPPING_JOB_TYPE_VRA
					(JOB_CODE, RVRA_CODE, VRA_CODE, INSERT_USER, INSERT_TIME)
					VALUES (
						'".addslashes($row['JOB_CODE'])."',
						'".addslashes($row['RVRA_CODE'])."',
						'".addslashes($row['VRA_CODE'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MAPPING JOB TYPE - VRA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MAPPING JOB TYPE - VRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}
        return $result;
    }
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "UPDATE TM_MAPPING_JOB_TYPE_VRA
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MAPPING JOB TYPE - VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MAPPING JOB TYPE - VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}
		
        return $result;
    }
}

