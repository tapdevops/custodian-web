<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Estate Organization
Function 			:	- getList			: menampilkan list estate organization
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/04/2013
Update Terakhir		:	29/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_EstateOrganization
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
	
	//menampilkan list estate organization
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
            SELECT ROWIDTOCHAR(ROWID) row_id, rownum, BA_CODE, COMPANY_CODE, COMPANY_NAME, ESTATE_NAME, REGION_CODE, REGION_NAME, BA_TYPE, ACTIVE
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(COMPANY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(COMPANY_NAME) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(ESTATE_NAME) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(REGION_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(REGION_NAME) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(BA_TYPE) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY BA_CODE
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

