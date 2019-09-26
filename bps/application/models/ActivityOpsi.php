<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Activity Opsi
Function 			:	- getList			: menampilkan list Activity Opsi
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2013
Update Terakhir		:	29/05/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ActivityOpsi
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
	
	//menampilkan list Activity Opsi
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
            SELECT ROWIDTOCHAR(opsi.ROWID) row_id, rownum, opsi.ACTIVITY_CODE, opsi.OPTIONAL, opsi.ACTIVITY_OPTIONAL_CODE, act.DESCRIPTION ACTIVITY_DESC
			FROM TM_ACTIVITY_OPSI opsi
			LEFT JOIN TM_ACTIVITY act
				ON opsi.ACTIVITY_CODE = act.ACTIVITY_CODE
			WHERE opsi.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(opsi.ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(opsi.OPTIONAL) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(opsi.ACTIVITY_OPTIONAL_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(act.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY opsi.ACTIVITY_CODE, opsi.ACTIVITY_OPTIONAL_CODE
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
			FROM TM_ACTIVITY_OPSI 
			WHERE ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND OPTIONAL = '{$params['OPTIONAL']}'
				AND ACTIVITY_OPTIONAL_CODE  = '{$params['ACTIVITY_OPTIONAL_CODE']}'
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
					UPDATE TM_ACTIVITY_OPSI
					SET  
						ACTIVITY_OPTIONAL_CODE = '".addslashes($row['ACTIVITY_OPTIONAL_CODE'])."',
						OPTIONAL = '".addslashes($row['OPTIONAL'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'ACTIVITY OPSI', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'ACTIVITY OPSI', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "
					INSERT INTO TM_ACTIVITY_OPSI
					(ACTIVITY_CODE, OPTIONAL, ACTIVITY_OPTIONAL_CODE, INSERT_USER, INSERT_TIME)
					VALUES (
						'".addslashes($row['ACTIVITY_CODE'])."',
						'".addslashes($row['OPTIONAL'])."',
						'".addslashes($row['ACTIVITY_OPTIONAL_CODE'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'ACTIVITY OPSI', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'ACTIVITY OPSI', '', $e->getCode());
				
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
			$sql = "UPDATE TM_ACTIVITY_OPSI
					SET  
						DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'ACTIVITY OPSI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'ACTIVITY OPSI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

