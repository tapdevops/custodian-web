<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Mapping Activity - Coa
Function 			:	- getList			: menampilkan list Mapping Activity - Coa
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
class Application_Model_MappingActivityCoa
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
	
	//menampilkan list Mapping Activity - Coa
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
            SELECT ROWIDTOCHAR(mapping.ROWID) row_id, rownum, mapping.ACTIVITY_GROUP, mapping.ACTIVITY_CODE, mapping.COST_ELEMENT, mapping.COA_CODE, 
				   mapping.ACTIVITY_CODE_SAP, act.DESCRIPTION ACTIVITY_DESC, coa.DESCRIPTION COA_DESC
			FROM TM_ACTIVITY_COA mapping
			LEFT JOIN TM_ACTIVITY act
				ON mapping.ACTIVITY_CODE = act.ACTIVITY_CODE
			LEFT JOIN TM_COA coa
				ON mapping.COA_CODE = coa.COA_CODE
			WHERE mapping.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(mapping.ACTIVITY_GROUP) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.COST_ELEMENT) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.COA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.ACTIVITY_CODE_SAP) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(act.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY mapping.ACTIVITY_GROUP, mapping.ACTIVITY_CODE, mapping.COST_ELEMENT
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
			FROM TM_ACTIVITY_COA 
			WHERE ACTIVITY_GROUP = '{$params['ACTIVITY_GROUP']}'
				AND ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND COST_ELEMENT  = '{$params['COST_ELEMENT']}'
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
					UPDATE TM_ACTIVITY_COA
					SET  
						COA_CODE = '".addslashes($row['COA_CODE'])."',
						ACTIVITY_CODE_SAP = '".addslashes($row['ACTIVITY_CODE_SAP'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MAPPING AKTIVITAS - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MAPPING AKTIVITAS - COA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "
					INSERT INTO TM_ACTIVITY_COA
					(ACTIVITY_GROUP, ACTIVITY_CODE, COST_ELEMENT, COA_CODE, ACTIVITY_CODE_SAP, INSERT_USER, INSERT_TIME)
					VALUES (
						'".addslashes($row['ACTIVITY_GROUP'])."',
						'".addslashes($row['ACTIVITY_CODE'])."',
						'".addslashes($row['COST_ELEMENT'])."',
						'".addslashes($row['COA_CODE'])."',
						'".addslashes($row['ACTIVITY_CODE_SAP'])."',
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MAPPING AKTIVITAS - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MAPPING AKTIVITAS - COA', '', $e->getCode());
				
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
			$sql = "UPDATE TM_ACTIVITY_COA
					SET  
						DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MAPPING AKTIVITAS - COA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MAPPING AKTIVITAS - COA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

