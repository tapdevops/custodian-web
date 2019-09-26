<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Menu Aktivitas RKT
Function 			:	- getList			: menampilkan list menu aktivitas RKT
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	09/07/2013
Update Terakhir		:	09/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ActivityMenu
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
	
	//menampilkan list menu aktivitas RKT
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
            SELECT act.DESCRIPTION ACTIVITY_DESC, param.PARAMETER_VALUE_2 as LINK_RKT, act.ACTIVITY_CODE
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
		
		if ($params['TIPE_AKTIVITAS'] != '') {
			$query .= "
                AND UPPER(mapping.ACTIVITY_GROUP_TYPE_CODE) LIKE UPPER('%".$params['TIPE_AKTIVITAS']."%')
            ";
        }
		
		$query .= "
			ORDER BY act.DESCRIPTION
		";
		
        $sql = "SELECT COUNT(*) FROM ({$query})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
        }
		
        return $result;
    }
}

