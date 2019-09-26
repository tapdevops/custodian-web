<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Mapping Group CSR dan COA
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Mapping Group CSR dan COA
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/06/2013
Update Terakhir		:	04/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_MappingGroupCsrCoa
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
            SELECT ROWIDTOCHAR(mapping.ROWID) row_id, rownum, mapping.GROUP_CODE, mapping.DESCRIPTION, mapping.COA_CODE, coa.DESCRIPTION COA_DESC,
				   mapping.SUB_GROUP_CODE, mapping.SUB_GROUP_DESC
            FROM TM_GROUP_RELATION mapping
			LEFT JOIN TM_COA coa
				ON mapping.COA_CODE = coa.COA_CODE
			WHERE mapping.DELETE_USER IS NULL
				AND mapping.TYPE = 'CSR'
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
				AND (
					UPPER(mapping.GROUP_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.COA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.SUB_GROUP_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(mapping.SUB_GROUP_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY mapping.GROUP_CODE, mapping.SUB_GROUP_CODE
		";
		
		return $query;
	}
	
	//menampilkan list Mapping Group CSR dan COA
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
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
			SELECT ROWIDTOCHAR(ROWID) ROW_ID 
			FROM TM_GROUP_RELATION 
			WHERE COA_CODE = '{$params['COA_CODE']}'
				AND GROUP_CODE  = '{$params['GROUP_CODE']}'
				AND SUB_GROUP_CODE  = '{$params['SUB_GROUP_CODE']}'
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
					UPDATE TM_GROUP_RELATION
					SET DESCRIPTION = '".addslashes($row['DESCRIPTION'])."',
						COA_CODE = '".addslashes($row['COA_CODE'])."',
						SUB_GROUP_CODE = '".addslashes($row['SUB_GROUP_CODE'])."',
						SUB_GROUP_DESC = '".addslashes($row['SUB_GROUP_DESC'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MAPPING GRUP CSR - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MAPPING GRUP CSR - COA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "INSERT INTO TM_GROUP_RELATION (COA_CODE, GROUP_CODE, SUB_GROUP_CODE, DESCRIPTION, SUB_GROUP_DESC, TYPE, INSERT_USER, INSERT_TIME)
						VALUES (
							'".addslashes($row['COA_CODE'])."',
							'".addslashes($row['GROUP_CODE'])."',
							'".addslashes($row['SUB_GROUP_CODE'])."',
							'".addslashes($row['DESCRIPTION'])."',
							'".addslashes($row['SUB_GROUP_DESC'])."',
							'CSR',
							'{$this->_userName}',
							SYSDATE
						)";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MAPPING GRUP CSR - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MAPPING GRUP CSR - COA', '', $e->getCode());
				
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
			$sql = "UPDATE TM_GROUP_RELATION
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MAPPING GRUP CSR - COA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MAPPING GRUP CSR - COA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

