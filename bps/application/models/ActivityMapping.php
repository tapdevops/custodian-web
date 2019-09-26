<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Mapping Aktivitas Untuk Penggunaan RKT
Function 			:	- getList			: menampilkan list MAPPING ACTIVITY
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/09/2013
Update Terakhir		:	10/09/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ActivityMapping
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
	
	//menampilkan list MAPPING ACTIVITY
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
            SELECT ROWIDTOCHAR(mapping.ROWID) row_id, rownum, mapping.ACTIVITY_CODE, mapping.ACTIVITY_GROUP_TYPE_CODE, mapping.ACTIVITY_GROUP_TYPE,
				   act.DESCRIPTION ACTIVITY_DESC, mapping.UI_RKT_CODE, param.PARAMETER_VALUE UI_RKT
			FROM TM_ACTIVITY_MAPPING mapping
			LEFT JOIN TM_ACTIVITY act
				ON mapping.ACTIVITY_CODE = act.ACTIVITY_CODE
			LEFT JOIN T_PARAMETER_VALUE param
				ON param.PARAMETER_VALUE_CODE = mapping.UI_RKT_CODE
				AND param.PARAMETER_CODE = 'UI_RKT'
			WHERE mapping.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(mapping.ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.ACTIVITY_GROUP_TYPE_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.ACTIVITY_GROUP_TYPE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(act.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.UI_RKT_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(param.PARAMETER_VALUE) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY mapping.UI_RKT_CODE, mapping.ACTIVITY_CODE, mapping.ACTIVITY_GROUP_TYPE_CODE
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
			FROM TM_ACTIVITY_MAPPING 
			WHERE ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND ACTIVITY_GROUP_TYPE_CODE = '{$params['ACTIVITY_GROUP_TYPE_CODE']}'
				AND ACTIVITY_GROUP_TYPE = '{$params['ACTIVITY_GROUP_TYPE']}'
				AND UI_RKT_CODE  = '{$params['UI_RKT_CODE']}'
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
					UPDATE TM_ACTIVITY_MAPPING
					SET  
						ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."',
						ACTIVITY_GROUP_TYPE_CODE = '".addslashes($row['ACTIVITY_GROUP_TYPE_CODE'])."',
						ACTIVITY_GROUP_TYPE = '".addslashes($row['ACTIVITY_GROUP_TYPE'])."',
						UI_RKT_CODE = '".addslashes($row['UI_RKT_CODE'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit(); 
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MAPPING ACTIVITY', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MAPPING ACTIVITY', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "
					INSERT INTO TM_ACTIVITY_MAPPING
					(ACTIVITY_CODE, ACTIVITY_GROUP_TYPE_CODE, ACTIVITY_GROUP_TYPE, UI_RKT_CODE, INSERT_USER, INSERT_TIME)
					VALUES (
						'".addslashes($row['ACTIVITY_CODE'])."',
						'".addslashes($row['ACTIVITY_GROUP_TYPE_CODE'])."',
						'".addslashes($row['ACTIVITY_GROUP_TYPE'])."',
						'".addslashes($row['UI_RKT_CODE'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MAPPING ACTIVITY', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MAPPING ACTIVITY', '', $e->getCode());
				
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
			$sql = "UPDATE TM_ACTIVITY_MAPPING
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MAPPING ACTIVITY', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MAPPING ACTIVITY', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

