<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Setting
Function 			:	- initParameterValue: ambil data master setting dari DB
						- getList			: menampilkan list master setting
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_HoValidSubmit
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
        $this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
        $this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
        $this->_divCode = Zend_Registry::get('auth')->getIdentity()->HO_DIV_CODE;
        $this->_ccCode = Zend_Registry::get('auth')->getIdentity()->HO_CC_CODE;
        
        $sess = new Zend_Session_Namespace('period');
        $this->_period = $sess->period;
    }
	
	//ambil data master setting dari DB
	private function initMasterSetting($params = array())
    {
        $result = array();

        // where2
        $result['where2'] = '';
        if (isset($params['sSearch']) && $params['sSearch'] != '') {
            $val = $this->_db->quote('%' . strtoupper($params['sSearch']) . '%');
            $result['where2'] .= " AND UPPER(NAME) LIKE {$val}";
        }
        // orderBy
        $sortCol = array(
            'UPPER(NAME)',
			'MY_ROWNUM',
			'LOWER(ITEM_NAME)'
        );
        $result['orderBy'] = '';
        if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
            $orderBy = '';
            for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
            }
            $result['orderBy'] = 'ORDER BY ' . substr_replace($orderBy, '', -2);
        }
        // sql
        $result['query'] = "
			SELECT UPPER(NAME), ROWIDTOCHAR(rowid), LOWER(ITEM_NAME), ICON
			FROM T_MODULE
			WHERE PARENT_MODULE = '901000000'
				AND DELETE_USER IS NULL
				-- AND STATUS = 'F'
        ";

        return $result;
    }
	
    public function getDivName() {
        $div = explode(',', $this->_divCode);
        $newdiv = '';
        foreach ($div as $row) { $newdiv .= "'" . $row . "',"; }
        $newdiv = substr($newdiv, 0, -1);

        if ($this->_divCode != 'ALL') {
            $sql = "SELECT DIV_CODE, DIV_NAME FROM TM_HO_DIVISION WHERE DIV_CODE IN ($newdiv) AND DELETE_USER IS NULL";
            $rows = $this->_db->fetchAll($sql);

            if (!empty($rows)) {
                $result = $rows[0]['DIV_CODE'] . ' - ' . $rows[0]['DIV_NAME'];
            }
        } else {
            $result = 'ALL';
        }

        return $result;
    }

    public function getCcName() {
        if ($this->_ccCode != 'ALL') {
            $sql = "SELECT HCC_CC, HCC_COST_CENTER FROM TM_HO_COST_CENTER WHERE HCC_CC = '{$this->_ccCode}' AND DELETE_USER IS NULL";
            $rows = $this->_db->fetchAll($sql);

            if (!empty($rows)) {
                $result = $rows[0]['HCC_CC'] . ' - ' . $rows[0]['HCC_COST_CENTER'];
            }
        } else {
            $result = 'ALL';
        }

        return $result;
    }

    //cek deleted data
    public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
            SELECT ROWIDTOCHAR(ROWID) ROW_ID 
            FROM TM_HECTARE_STATEMENT 
            WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
                AND BA_CODE = '{$params['BA_CODE']}'
                AND AFD_CODE = '{$params['AFD_CODE']}'
                AND BLOCK_CODE  = '{$params['BLOCK_CODE']}'
        ";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
    
    //simpan data sementara sesuai input user
    public function saveTemp($row = array())
    {
        $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
        $result = true;
        //cek data tsb sudah pernah ada & dihapus atau benar2 data baru
        //if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);

        $div_code = (empty($row['DIV_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['DIV_CODE']);
        $cc_code = (empty($row['CC_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['CC_CODE']);

        $check = "
            SELECT * FROM TR_HO_REPORT_VALID
            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '{$row['PERIOD_BUDGET']}' 
            	AND DIV_CODE = '{$div_code}' 
            	AND CC_CODE = '{$cc_code}'
        ";
        $count = $this->_db->fetchOne($check);

        if ($count > 0) {
        	//return false;
            $sql = "
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    DIV_CODE = '{$div_code}',
                    CC_CODE = '{$cc_code}',
                    GROUP_BUDGET = '{$row['GROUP_BUDGET']}',
                    OUTLOOK = '{$row['OUTLOOK']}',
                    NEXT_BUDGET = '{$row['NEXT_BUDGET']}',
                    VAR_SELISIH = '{$row['VAR_SELISIH']}',
                    VAR_PERSEN = '{$row['VAR_PERSEN']}',
                    INSERT_USER = '{$username}',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY') 
                    AND DIV_CODE = '{$div_code}'
                    AND CC_CODE = '{$cc_code}'
                    AND GROUP_BUDGET = '{$row['GROUP_BUDGET']}'
            ";
        } else {
            $sql = "
                INSERT INTO TR_HO_REPORT_VALID (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    GROUP_BUDGET,
                    OUTLOOK,
                    NEXT_BUDGET,
                    VAR_SELISIH,
                    VAR_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    '".$div_code."',
                    '".$cc_code."',
                    '{$row['GROUP_BUDGET']}',
                    '{$row['OUTLOOK']}',
                    '{$row['NEXT_BUDGET']}',
                    '{$row['VAR_SELISIH']}',
                    '{$row['VAR_PERSEN']}',
                    '{$username}',
                    SYSDATE
                );
            ";
        }

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }

    public function getData($params = array()) {
		if ($params['budgetperiod'] != '') $period = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $cc = $params['key_find_cc'];

        $query = "
        	SELECT
                GROUP_BUDGET, 
                NVL(TOTAL_ACTUAL, 0) TOTAL_ACTUAL, 
                TOTAL, 
                NVL((TOTAL - TOTAL_ACTUAL), 0) VAR_SELISIH, 
                CASE 
                    WHEN TOTAL_ACTUAL = 0 OR TOTAL = 0
                    THEN 0
                    ELSE NVL(TRUNC((TOTAL - TOTAL_ACTUAL) / TOTAL_ACTUAL * 100, 2), 0)
                END AS VAR_PERSEN
            FROM (
                SELECT OLK.GROUP_BUDGET, SUM(OLK.TOTAL_ACTUAL) AS TOTAL_ACTUAL, NVL(CUR.TOTAL, 0) TOTAL FROM (
                    SELECT 'OPEX' GROUP_BUDGET, TOTAL_ACTUAL 
                    FROM TM_HO_ACT_OUTLOOK ACTUAL 
                    WHERE DELETE_USER IS NULL 
                        AND TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                        AND CC_CODE = '".$cc."'
                        AND COA_CODE IN (
                            SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%'
                        )
                ) OLK

                RIGHT JOIN (
                    SELECT 'OPEX' GROUP_BUDGET, SUM(NVL(TOTAL, 0)) TOTAL FROM (
                        SELECT SUM(NVL(CAPEX_TOTAL, 0)) AS TOTAL FROM (
                            SELECT NVL(CAPEX_TOTAL, 0) CAPEX_TOTAL 
                            FROM TM_HO_CAPEX 
                            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                                AND CC_CODE = '".$cc."' AND DELETE_USER IS NULL
                                AND COA_CODE IN (
                                    SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%'
                                )
                        )

                        UNION ALL

                        SELECT SUM(NVL(OPEX_TOTAL, 0)) AS TOTAL FROM (
                            SELECT NVL(OPEX_TOTAL, 0) OPEX_TOTAL 
                            FROM TM_HO_OPEX 
                            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                                AND CC_CODE = '".$cc."' AND DELETE_USER IS NULL
                                AND COA_CODE IN (
                                    SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%'
                                )
                        )

                        UNION ALL

                        SELECT SUM(NVL(SEBARAN_TOTAL, 0)) AS TOTAL FROM (
                            SELECT NVL(SEBARAN_TOTAL, 0) SEBARAN_TOTAL 
                            FROM TR_HO_SPD
                            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."' 
                                AND CC_CODE = '".$cc."' 
                                AND DELETE_USER IS NULL
                                AND COA_CODE IN (
                                    SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%'
                                )
                        )
                    ) GROUP BY 'OPEX'
                ) CUR 
                ON CUR.GROUP_BUDGET = OLK.GROUP_BUDGET 
                GROUP BY OLK.GROUP_BUDGET, OLK.GROUP_BUDGET, CUR.TOTAL

                UNION ALL

                SELECT 'CAPEX' GROUP_BUDGET, SUM(NVL(OLK.TOTAL_ACTUAL, 0)) AS TOTAL_ACTUAL, NVL(CUR.TOTAL, 0) TOTAL FROM (
                    SELECT 'CAPEX' GROUP_BUDGET, TOTAL_ACTUAL 
                    FROM TM_HO_ACT_OUTLOOK ACTUAL 
                    WHERE DELETE_USER IS NULL 
                        AND TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                        AND CC_CODE = '".$cc."'
                        AND COA_CODE IN (
                            SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'CAPEX%'
                        )
                ) OLK

                RIGHT JOIN (
                    SELECT 'CAPEX' GROUP_BUDGET, SUM(NVL(TOTAL, 0)) TOTAL FROM (
                        SELECT SUM(NVL(CAPEX_TOTAL, 0)) AS TOTAL FROM (
                            SELECT NVL(CAPEX_TOTAL, 0) CAPEX_TOTAL 
                            FROM TM_HO_CAPEX 
                            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                            AND CC_CODE = '".$cc."' AND DELETE_USER IS NULL
                            AND COA_CODE IN (
                                SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'CAPEX%'
                            )
                    )

                    UNION ALL

                    SELECT SUM(NVL(OPEX_TOTAL, 0)) AS TOTAL FROM (
                        SELECT NVL(OPEX_TOTAL, 0) OPEX_TOTAL 
                        FROM TM_HO_OPEX 
                        WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."'
                            AND CC_CODE = '".$cc."' AND DELETE_USER IS NULL
                            AND COA_CODE IN (
                                SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'CAPEX%'
                            )
                    )

                    UNION ALL

                    SELECT SUM(NVL(SEBARAN_TOTAL, 0)) AS TOTAL FROM (
                        SELECT NVL(SEBARAN_TOTAL, 0) SEBARAN_TOTAL 
                        FROM TR_HO_SPD 
                        WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$period."' 
                            AND CC_CODE = '".$cc."' 
                            AND DELETE_USER IS NULL
                            AND COA_CODE IN (
                                SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE '%SPD'
                            )
                    )
                ) GROUP BY 'CAPEX'
            ) CUR 
            ON CUR.GROUP_BUDGET = OLK.GROUP_BUDGET 
            GROUP BY OLK.GROUP_BUDGET, OLK.GROUP_BUDGET, CUR.TOTAL
            )
        ";

        return $query;
    }

    public function getList($params = array())
    {
    	$output = array();
        $result = array();
        $user = array();

        $begin = "
            SELECT * FROM ( SELECT MY_TABLE.*
            FROM (
            SELECT ROWNUM MY_ROWNUM, TEMP.*
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

        $ttl = array();
        if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
            $result['rows'][2]['MY_ROWNUM'] = 3;
            $result['rows'][2]['GROUP_BUDGET'] = 'TOTAL';
            $result['rows'][2]['TOTAL_ACTUAL'] = $rows[0]['TOTAL_ACTUAL'] + $rows[1]['TOTAL_ACTUAL'];
            $result['rows'][2]['TOTAL'] = $rows[0]['TOTAL'] + $rows[1]['TOTAL'];
            $result['rows'][2]['VAR_SELISIH'] = $rows[0]['VAR_SELISIH'] + $rows[1]['VAR_SELISIH'];
            $result['rows'][2]['VAR_PERSEN'] = $rows[0]['VAR_PERSEN'] + $rows[1]['VAR_PERSEN'];
        }

        $sql_user = "
         SELECT HCC_COST_CENTER_HEAD, HCC_COST_CENTER, HCC_DIVISION_HEAD, 
                (SELECT DISTINCT DIV_NAME FROM TM_HO_DIVISION WHERE DIV_CODE = '".$params['key_find_div']."' AND DELETE_USER IS NULL) AS DIVISION 
            FROM TM_HO_COST_CENTER 
            WHERE HCC_CC = '".$params['key_find_cc']."' AND HCC_DIVISI = '".$params['key_find_div']."'
        ";
        $output_user = $this->_db->fetchAll($sql_user);

        if (!empty($output_user)) {
        	$user = $output_user[0];
        }

        $output['result'] = $result;
        $output['user'] = $user;

        return $output;
    }

    public function getDatas($params = array()) {
        $rows = $this->_db->fetchAll("{$this->getData($params)}");   

        $ttl = array();
        if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
            $result['rows'][2]['MY_ROWNUM'] = 3;
            $result['rows'][2]['GROUP_BUDGET'] = 'TOTAL';
            $result['rows'][2]['TOTAL_ACTUAL'] = $rows[0]['TOTAL_ACTUAL'] + $rows[1]['TOTAL_ACTUAL'];
            $result['rows'][2]['TOTAL'] = $rows[0]['TOTAL'] + $rows[1]['TOTAL'];
            $result['rows'][2]['VAR_SELISIH'] = $rows[0]['VAR_SELISIH'] + $rows[1]['VAR_SELISIH'];
            $result['rows'][2]['VAR_PERSEN'] = $rows[0]['VAR_PERSEN'] + $rows[1]['VAR_PERSEN'];
        }

        //$sql_user = "SELECT * FROM TM_HO_COST_CENTER THC LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = THC. WHERE HCC_CC = '".$params['key_find_cc']."' AND HCC_DIVISI = '".$params['key_find_div']."'";
        $sql_user = "
            SELECT HCC_COST_CENTER_HEAD, HCC_COST_CENTER, HCC_DIVISION_HEAD, 
                (SELECT DISTINCT DIV_NAME FROM TM_HO_DIVISION WHERE DIV_CODE = '".$params['key_find_div']."' AND DELETE_USER IS NULL) AS DIVISION 
            FROM TM_HO_COST_CENTER 
            WHERE HCC_CC = '".$params['key_find_cc']."' AND HCC_DIVISI = '".$params['key_find_div']."'
        ";
        $output_user = $this->_db->fetchAll($sql_user);

        if (!empty($output_user)) {
            $user = $output_user[0];
        }

        $output['result'] = $result;
        $output['user'] = $user;

        return $output;
    }

    //hapus data
    public function delete($row = array())
    {
        $sql = "
            UPDATE TR_HO_SPD
            SET DELETE_USER = '{$this->_userName}',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
        ";

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }
    
}

