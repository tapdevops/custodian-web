<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Mapping Group BUM dan COA
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Mapping Group BUM dan COA
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/05/2013
Update Terakhir		:	30/05/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_MappingGroupBumCoa
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
            SELECT ROWIDTOCHAR(bum.ROWID) row_id, rownum, bum.GROUP_BUM_CODE, param.PARAMETER_VALUE DESCRIPTION, bum.COA_CODE, coa.DESCRIPTION COA_DESC
            FROM TM_GROUP_BUM_COA bum
			LEFT JOIN T_PARAMETER_VALUE param
				ON bum.GROUP_BUM_CODE = param.PARAMETER_VALUE_CODE
				AND param.PARAMETER_CODE = 'GROUP_BUM'
			LEFT JOIN TM_COA coa
				ON bum.COA_CODE = coa.COA_CODE
			WHERE bum.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
				AND (
					UPPER(bum.GROUP_BUM_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(bum.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(bum.COA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY bum.GROUP_BUM_CODE, bum.DESCRIPTION
		";
		
		return $query;
	}
	
	//menampilkan list Mapping Group BUM dan COA
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
			FROM TM_GROUP_BUM_COA 
			WHERE GROUP_BUM_CODE = '{$params['GROUP_BUM_CODE']}'
				AND COA_CODE  = '{$params['COA_CODE']}'
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
					UPDATE TM_GROUP_BUM_COA
					SET GROUP_BUM_CODE = '".addslashes($row['GROUP_BUM_CODE'])."',
						DESCRIPTION = '".addslashes($row['DESCRIPTION'])."',
						COA_CODE = '".addslashes($row['COA_CODE'])."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
				";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MAPPING GRUP BUM - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MAPPING GRUP BUM - COA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}else{
			try {
				$sql = "INSERT INTO TM_GROUP_BUM_COA (GROUP_BUM_CODE, DESCRIPTION, COA_CODE, INSERT_USER, INSERT_TIME)
						VALUES (
							'".addslashes($row['GROUP_BUM_CODE'])."',
							'".addslashes($row['DESCRIPTION'])."',
							'".addslashes($row['COA_CODE'])."',
							'{$this->_userName}',
							SYSDATE
						)";
				$this->_db->query($sql);
				$this->_db->commit();
				//log DB
				$this->_global->insertLog('INSERT SUCCESS', 'MAPPING GRUP BUM - COA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('INSERT FAILED', 'MAPPING GRUP BUM - COA', '', $e->getCode());
				
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
			$sql = "UPDATE TM_GROUP_BUM_COA
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MAPPING GRUP BUM - COA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MAPPING GRUP BUM - COA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

