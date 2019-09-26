<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Mapping Aktivitas di Report
Function 			:	- getList			: menampilkan list MAPPING ACTIVITY
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/08/2014
Update Terakhir		:	08/08/2014
Revisi				:	
=========================================================================================================================
*/
class Application_Model_MappingActivityReport
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
			SELECT *
			FROM (
				SELECT 	STRUKTUR_REPORT.GROUP01,
						(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP01) AS GROUP01_DESC,
						STRUKTUR_REPORT.GROUP02,
						(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP02) AS GROUP02_DESC,
						STRUKTUR_REPORT.GROUP03,
						(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP03) AS GROUP03_DESC,
						STRUKTUR_REPORT.GROUP04,
						(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP04) AS GROUP04_DESC,
						ACT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						MAPP.COST_ELEMENT,
						STRUKTUR_REPORT.TIPE
				FROM (
					SELECT 	CASE
								WHEN INSTR(HIRARKI, '/',1, 2) <> 0
								THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 2)+1, INSTR(HIRARKI, '/',1, 2) - 2) 
								ELSE NULL
							END GROUP01,
							CASE
								WHEN INSTR(HIRARKI, '/',1, 3) <> 0
								THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 3)+1, INSTR(HIRARKI, '/',1, 2) - 2)
								ELSE NULL
							END GROUP02,
							CASE
								WHEN INSTR(HIRARKI, '/',1, 4) <> 0
								THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 4)+1, INSTR(HIRARKI, '/',1, 2) - 2)
								ELSE NULL
							END GROUP03,
							CASE
								WHEN INSTR(HIRARKI, '/',1, 5) <> 0
								THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 5)+1, INSTR(HIRARKI, '/',1, 2) - 2)
								ELSE NULL
							END GROUP04,
							GROUP_CODE,
							TIPE
					FROM (
						SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
								LVL, 
								TO_CHAR(GROUP_CODE) AS GROUP_CODE,
								'DEVELOPMENT COST' AS TIPE
						FROM (
							SELECT 	GROUP_CODE, 
									CONNECT_BY_ISCYCLE \"CYCLE\",
									LEVEL as LVL, 
									SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
							FROM TM_RPT_MAPPING_ACT
							WHERE level > 1
							START WITH GROUP_CODE = '01.00.00.00.00' -- dev cost
							CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
						)
						GROUP BY HIRARKI, LVL, GROUP_CODE
						UNION ALL
						SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
								LVL, 
								TO_CHAR(GROUP_CODE) AS GROUP_CODE,
								'ESTATE COST' AS TIPE
						FROM (
							SELECT 	GROUP_CODE, 
									CONNECT_BY_ISCYCLE \"CYCLE\",
									LEVEL as LVL, 
									SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
							FROM TM_RPT_MAPPING_ACT
							WHERE level > 1
							START WITH GROUP_CODE = '02.00.00.00.00' -- est cost
							CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
						)
						GROUP BY HIRARKI, LVL, GROUP_CODE
					)
				) STRUKTUR_REPORT
				LEFT JOIN TM_RPT_MAPPING_ACT MAPP
					ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
				LEFT JOIN TM_ACTIVITY ACT
					ON MAPP.ACTIVITY_CODE = ACT.ACTIVITY_CODE
			)
			WHERE GROUP02 IS NOT NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(GROUP01) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP01_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP02) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP02_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP03) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP03_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP04) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(GROUP04_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(ACTIVITY_DESC) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(COST_ELEMENT) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(TIPE) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY GROUP01, GROUP02, GROUP03, GROUP04, ACTIVITY_CODE, COST_ELEMENT
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

