<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master VRA
Function 			:	- getList			: menampilkan list VRA
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	03/06/2013
Update Terakhir		:	03/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_Vra
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
	
	//menampilkan list VRA
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
            SELECT ROWIDTOCHAR(ROWID) row_id, rownum, VRA_CODE, VRA_CAT_CODE, VRA_CAT_DESCRIPTION, VRA_SUB_CAT_CODE, VRA_SUB_CAT_DESCRIPTION, UOM, TYPE
            FROM TM_VRA
			WHERE DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(VRA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(VRA_CAT_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(VRA_CAT_DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(VRA_SUB_CAT_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(VRA_SUB_CAT_DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(UOM) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(TYPE) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY VRA_CODE
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
}

