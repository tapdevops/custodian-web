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
class Application_Model_HoActOutlook
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
        if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);

        $div_code = str_replace('&', "'||'&'||'", $row['DIV_CODE']);
        $cc_code = str_replace('&', "'||'&'||'", $row['CC_CODE']);
        $trans_desc = str_replace('&', "'||'&'||'", $row['TRANSACTION_DESC']);

        $check = "
            SELECT * FROM TM_HO_ACT_OUTLOOK
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
        ";
        $count = $this->_db->fetchOne($check);

        if ($count > 0) {
            $sql = "
                UPDATE TM_HO_ACT_OUTLOOK 
                SET 
                    PERIOD_BUDGET = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    CC_CODE = '{$row['CC_CODE']}',
                    COA_CODE = '{$row['COA_CODE']}',
                    TRANSACTION_DESC = '{$trans_desc}',
                    CORE = '{$row['CORE_CODE']}',
                    COMP_CODE = '{$row['COMPANY_CODE']}',
                    ACT_JAN = '{$row['ACT_JAN']}',
                    ACT_FEB = '{$row['ACT_FEB']}',
                    ACT_MAR = '{$row['ACT_MAR']}',
                    ACT_APR = '{$row['ACT_APR']}',
                    ACT_MAY = '{$row['ACT_MAY']}',
                    ACT_JUN = '{$row['ACT_JUN']}',
                    ACT_JUL = '{$row['ACT_JUL']}',
                    ACT_AUG = '{$row['ACT_AUG']}',
                    OUTLOOK_SEP = '{$row['OUTLOOK_SEP']}',
                    OUTLOOK_OCT = '{$row['OUTLOOK_OCT']}',
                    OUTLOOK_NOV = '{$row['OUTLOOK_NOV']}',
                    OUTLOOK_DEC = '{$row['OUTLOOK_DEC']}',
                    YTD_ACTUAL = '{$row['YTD_ACTUAL']}',
                    ADJ = '{$row['ADJ']}',
                    OUTLOOK = '{$row['OUTLOOK']}',
                    TOTAL_ACTUAL = '{$row['TOTAL_ACTUAL']}',
                    UPDATE_USER = '{$username}',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
            ";
        } else {
            $sql = "
                INSERT INTO TM_HO_ACT_OUTLOOK (
                    PERIOD_BUDGET,
                    CC_CODE,
                    COA_CODE,
                    TRANSACTION_DESC,
                    CORE,
                    COMP_CODE,
                    ACT_JAN,
                    ACT_FEB,
                    ACT_MAR,
                    ACT_APR,
                    ACT_MAY,
                    ACT_JUN,
                    ACT_JUL,
                    ACT_AUG,
                    OUTLOOK_SEP,
                    OUTLOOK_OCT,
                    OUTLOOK_NOV,
                    OUTLOOK_DEC,
                    YTD_ACTUAL,
                    ADJ,
                    OUTLOOK,
                    TOTAL_ACTUAL,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    '{$row['CC_CODE']}',
                    '{$row['COA_CODE']}',
                    '{$trans_desc}',
                    '{$row['CORE_CODE']}',
                    '{$row['COMPANY_CODE']}',
                    '{$row['ACT_JAN']}',
                    '{$row['ACT_FEB']}',
                    '{$row['ACT_MAR']}',
                    '{$row['ACT_APR']}',
                    '{$row['ACT_MAY']}',
                    '{$row['ACT_JUN']}',
                    '{$row['ACT_JUL']}',
                    '{$row['ACT_AUG']}',
                    '{$row['OUTLOOK_SEP']}',
                    '{$row['OUTLOOK_OCT']}',
                    '{$row['OUTLOOK_NOV']}',
                    '{$row['OUTLOOK_DEC']}',
                    '{$row['YTD_ACTUAL']}',
                    '{$row['ADJ']}',
                    '{$row['OUTLOOK']}',
                    '{$row['TOTAL_ACTUAL']}',
                    '{$username}',
                    SYSDATE
                );
            ";
        }

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }

    //simpan data sementara sesuai input user
    public function saveTempSummary($row = array())
    { 
        $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
        $result = true;

        $check = "
            SELECT * FROM TR_HO_SUMMARY_OUTLOOK 
            WHERE EXTRACT (YEAR FROM PERIOD_BUDGET) = '".$row['period_budget']."' 
            AND CC_CODE = '".$row['cc']."'
            AND COA_CODE = '".$row['coa']."'
        ";
        $rows = $this->_db->fetchAll("{$check}");

        $calculate = "
            SELECT 
                PERIOD_BUDGET, CC_CODE, HCC_CC, COA_CODE, COA_NAME,
                ACT_JAN, ACT_FEB, ACT_MAR, ACT_APR, ACT_MAY, ACT_JUN, ACT_JUL, ACT_AUG,
                OUTLOOK_SEP, OUTLOOK_OCT, OUTLOOK_NOV, OUTLOOK_DEC,
                YTD_ACTUAL, ADJ, OUTLOOK, TTL_ACTUAL,
                YTD_ACTUAL + ADJ AS YTD_ACTUAL_ADJ,
                YTD_ACTUAL + ADJ + OUTLOOK AS LAST_YEAR,
                (YTD_ACTUAL + ADJ) / 8 * 12 AS ANNUAL_YTD,
                (YTD_ACTUAL + OUTLOOK) - (YTD_ACTUAL / 8 * 12) AS VARIANCE_RP,
                CASE WHEN
                    YTD_ACTUAL = 0
                THEN 0
                ELSE 
                    ROUND(((YTD_ACTUAL + OUTLOOK) - (YTD_ACTUAL / 8 * 12)) / (YTD_ACTUAL / 8 * 12) * 100, 2) 
                END AS VARIANCE_PERSEN
            FROM (
                SELECT 
                    --ROWIDTOCHAR(THA.ROWID) ROW_ID,
                    EXTRACT (YEAR FROM PERIOD_BUDGET) PERIOD_BUDGET,
                    THA.CC_CODE, HCC.HCC_CC,
                    THA.COA_CODE, HCO.COA_NAME,
                    SUM(ACT_JAN) ACT_JAN, 
                    SUM(ACT_FEB) ACT_FEB, 
                    SUM(ACT_MAR) ACT_MAR,
                    SUM(ACT_APR) ACT_APR,
                    SUM(ACT_MAY) ACT_MAY,
                    SUM(ACT_JUN) ACT_JUN,
                    SUM(ACT_JUL) ACT_JUL,
                    SUM(ACT_AUG) ACT_AUG,
                    SUM(OUTLOOK_SEP) OUTLOOK_SEP,
                    SUM(OUTLOOK_OCT) OUTLOOK_OCT,
                    SUM(OUTLOOK_NOV) OUTLOOK_NOV,
                    SUM(OUTLOOK_DEC) OUTLOOK_DEC,
                    SUM(YTD_ACTUAL) YTD_ACTUAL,
                    SUM(ADJ) ADJ,
                    SUM(OUTLOOK) OUTLOOK,
                    SUM(TOTAL_ACTUAL) TTL_ACTUAL
                FROM TM_HO_ACT_OUTLOOK THA
                LEFT JOIN TM_HO_COST_CENTER HCC
                    ON HCC.HCC_CC = THA.CC_CODE
                LEFT JOIN TM_HO_COA HCO
                    ON HCO.COA_CODE = THA.COA_CODE
                WHERE THA.DELETE_USER IS NULL
                    AND to_char(THA.PERIOD_BUDGET,'YYYY') = '".$row['period_budget']."'
                    AND UPPER(THA.CC_CODE) = '".$row['cc']."'
                    AND UPPER(THA.COA_CODE) = '".$row['coa']."'
                GROUP BY THA.PERIOD_BUDGET, THA.CC_CODE, HCC.HCC_CC, THA.COA_CODE, HCO.COA_NAME
            )
        ";

        $calc = $this->_db->fetchAll("{$calculate}");

        if (count($rows) > 0) {
            $sql = "
                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('{$calc[0]['PERIOD_BUDGET']}', 'YYYY'),
                    CC_CODE = '{$calc[0]['CC_CODE']}',
                    COA_CODE = '{$calc[0]['COA_CODE']}',
                    ACT_JAN = '{$calc[0]['ACT_JAN']}',
                    ACT_FEB = '{$calc[0]['ACT_FEB']}',
                    ACT_MAR = '{$calc[0]['ACT_MAR']}',
                    ACT_APR = '{$calc[0]['ACT_APR']}',
                    ACT_MAY = '{$calc[0]['ACT_MAY']}',
                    ACT_JUN = '{$calc[0]['ACT_JUN']}',
                    ACT_JUL = '{$calc[0]['ACT_JUL']}',
                    ACT_AUG = '{$calc[0]['ACT_AUG']}',
                    OUTLOOK_SEP = '{$calc[0]['OUTLOOK_SEP']}',
                    OUTLOOK_OCT = '{$calc[0]['OUTLOOK_OCT']}',
                    OUTLOOK_NOV = '{$calc[0]['OUTLOOK_NOV']}',
                    OUTLOOK_DEC = '{$calc[0]['OUTLOOK_DEC']}',
                    ADJ = '{$calc[0]['ADJ']}',
                    YTD_ACTUAL_ADJ = '{$calc[0]['YTD_ACTUAL_ADJ']}',
                    OUTLOOK = '{$calc[0]['OUTLOOK']}',
                    LATEST_PERIOD = '{$calc[0]['LAST_YEAR']}',
                    ANNUALIZED_YTD = '{$calc[0]['ANNUAL_YTD']}',
                    VARIANCE_RP = '{$calc[0]['VARIANCE_RP']}',
                    VARIANCE_PERSEN = '{$calc[0]['VARIANCE_PERSEN']}',
                    UPDATE_USER = '{$username}',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('{$row['period_budget']}', 'YYYY')
                    AND CC_CODE = '{$row['cc']}' 
                    AND COA_CODE = '{$row['coa']}'
                ;
            ";
        } else {
            $sql = "
                INSERT INTO TR_HO_SUMMARY_OUTLOOK (
                    PERIOD_BUDGET,
                    CC_CODE,
                    COA_CODE,
                    ACT_JAN,
                    ACT_FEB,
                    ACT_MAR,
                    ACT_APR,
                    ACT_MAY,
                    ACT_JUN,
                    ACT_JUL,
                    ACT_AUG,
                    OUTLOOK_SEP,
                    OUTLOOK_OCT,
                    OUTLOOK_NOV,
                    OUTLOOK_DEC,
                    ADJ,
                    YTD_ACTUAL_ADJ,
                    OUTLOOK,
                    LATEST_PERIOD,
                    ANNUALIZED_YTD,
                    VARIANCE_RP,
                    VARIANCE_PERSEN,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$calc[0]['PERIOD_BUDGET']}', 'YYYY'),
                    '{$calc[0]['CC_CODE']}',
                    '{$calc[0]['COA_CODE']}',
                    '{$calc[0]['ACT_JAN']}',
                    '{$calc[0]['ACT_FEB']}',
                    '{$calc[0]['ACT_MAR']}',
                    '{$calc[0]['ACT_APR']}',
                    '{$calc[0]['ACT_MAY']}',
                    '{$calc[0]['ACT_JUN']}',
                    '{$calc[0]['ACT_JUL']}',
                    '{$calc[0]['ACT_AUG']}',
                    '{$calc[0]['OUTLOOK_SEP']}',
                    '{$calc[0]['OUTLOOK_OCT']}',
                    '{$calc[0]['OUTLOOK_NOV']}',
                    '{$calc[0]['OUTLOOK_DEC']}',
                    '{$calc[0]['ADJ']}',
                    '{$calc[0]['YTD_ACTUAL_ADJ']}',
                    '{$calc[0]['OUTLOOK']}',
                    '{$calc[0]['LAST_YEAR']}',
                    '{$calc[0]['ANNUAL_YTD']}',
                    '{$calc[0]['VARIANCE_RP']}',
                    '{$calc[0]['VARIANCE_PERSEN']}',
                    '{$username}',
                    CURRENT_TIMESTAMP
                );
            ";
        }

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }

    public function getData($params = array()) {
        $username = Zend_Registry::get('auth')->getIdentity()->USER_NAME;

        $query = "
            SELECT 
                row_id,
                PERIOD_BUDGET,
                HCC_CC, HCC_COST_CENTER,
                COA_CODE, COA_NAME,
                TRANSACTION_DESC,
                CORE_CODE, CORE_NAME,
                COMP_CODE AS COMPANY_CODE,
                (SELECT DISTINCT COMPANY_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = COMP_CODE) COMPANY_NAME,
                ACT_JAN,
                ACT_FEB,
                ACT_MAR,
                ACT_APR,
                ACT_MAY,
                ACT_JUN,
                ACT_JUL,
                ACT_AUG,
                OUTLOOK_SEP,
                OUTLOOK_OCT,
                OUTLOOK_NOV,
                OUTLOOK_DEC,
                YTD_ACTUAL,
                ADJ,
                OUTLOOK,
                TOTAL_ACTUAL
            FROM (
                SELECT
                ROWIDTOCHAR(TAO.ROWID) row_id,
                TO_CHAR(TAO.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
                THCC.HCC_CC, THCC.HCC_COST_CENTER,
                THC.COA_CODE, THC.COA_NAME,
                TAO.TRANSACTION_DESC,
                THC.CORE_CODE, THC.CORE_NAME,
                TAO.COMP_CODE,
                TAO.ACT_JAN,
                TAO.ACT_FEB,
                TAO.ACT_MAR,
                TAO.ACT_APR,
                TAO.ACT_MAY,
                TAO.ACT_JUN,
                TAO.ACT_JUL,
                TAO.ACT_AUG,
                TAO.OUTLOOK_SEP,
                TAO.OUTLOOK_OCT,
                TAO.OUTLOOK_NOV,
                TAO.OUTLOOK_DEC,
                TAO.YTD_ACTUAL,
                TAO.ADJ,
                TAO.OUTLOOK,
                TAO.TOTAL_ACTUAL
            FROM TM_HO_ACT_OUTLOOK TAO
            LEFT JOIN TM_HO_COST_CENTER THCC
                ON THCC.HCC_CC = TAO.CC_CODE
            LEFT JOIN TM_HO_COA THC
                ON THC.COA_CODE = TAO.COA_CODE
            LEFT JOIN TM_HO_CORE THC
                ON THC.CORE_CODE = TAO.CORE
            WHERE TAO.DELETE_USER IS NULL
                --AND TAO.INSERT_USER = '{$username}'
        ";

        if($params['budgetperiod'] != '') {
            $query .= "
                AND to_char(TAO.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        } else {
            $query .= "
                AND to_char(TAO.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
        }
        
        /*if ($params['key_find'] != '') {
            $query .= "
                AND UPPER(THRK.DIV_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }*/

        if ($params['controller'] == 'download') {
            $query .= "
                AND UPPER(TAO.CC_CODE) = '".$params['key_find_cc']."' 
            ";

            $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
        } else {
            if ($params['src_cc'] != '') {
                $explode = explode('-', $params['src_cc']);
                $query .= "
                    AND UPPER(TAO.CC_CODE) = '".trim($explode[0])."'
                ";
            }
        }

        if ($params['search'] != '') {
            $query .= "
                AND (
                    UPPER(TAO.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THCC.HCC_CC) LIKE UPPER('%".$params['search']."%')  
                    OR UPPER(THCC.HCC_COST_CENTER) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(THC.COA_CODE) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(THC.COA_NAME) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(TAO.TRANSACTION_DESC) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(THC.CORE_CODE) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(THC.CORE_NAME) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(TC.COMPANY_CODE) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(TC.COMPANY_NAME) LIKE UPPER('%".$params['search']."%')
                )
            ";

        }
        $query .= ')';

        return $query;
    }

    public function getList($params = array())
    {
        $result = array();

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
        
        if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
        }

        return $result;
    }

    //hapus data
    public function delete($row = array())
    {
        $sql = "
            UPDATE TM_HO_ACT_OUTLOOK
            SET DELETE_USER = '{$this->_userName}',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
        ";

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }
    
	//menampilkan list parameter
	/*public function getList($params = array())
    {
        $result = array();

        $result['sEcho'] = intval($params['sEcho']);
        $result['iTotalRecords'] = 0;
        $result['iTotalDisplayRecords'] = 0;
        $result['aaData'] = array();

        $min = strval($params['iDisplayStart']);
        $max = strval(intval($params['iDisplayStart']) + intval($params['iDisplayLength']));
        $begin = "
            SELECT * FROM (SELECT ROWNUM MY_ROWNUM, MY_TABLE.*
            FROM (SELECT TEMP.*
            FROM (
        ";
        $end = "
            ) TEMP
            ) MY_TABLE
              WHERE ROWNUM <= {$max}
            ) WHERE MY_ROWNUM > {$min}
        ";

        $initAction = 'init' . str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));
        $init = $this->$initAction($params);

        // -- rows count (all)
        $sql = "SELECT COUNT(*) FROM ({$init['query']})";
        $result['iTotalRecords'] = $this->_db->fetchOne($sql);
        // -- rows count (filter)
        $sql = "SELECT COUNT(*) FROM ({$init['query']} {$init['where2']})";
        $result['iTotalDisplayRecords'] = $this->_db->fetchOne($sql);
        // -- rows
        $sql = "{$begin} {$init['query']} {$init['where2']} {$init['orderBy']} {$end}";
        $this->_db->setFetchMode(Zend_Db::FETCH_NUM);
        $rows = $this->_db->fetchAll($sql);
		
		$cRow = 1;
        
        foreach ($rows as $idx => $row) {
            $data = array();
			$d_upload = $d_view = $d_download = "";
			//$d_download = "disabled";
			
			switch ($row[4]){
				case 'VD' :
					$d_upload = "disabled";
					break;
				case 'UD' :
					$d_view = "disabled";
					break;
				case 'V' :
					$d_upload = "disabled";
					$d_download = "disabled";
					break;	
				case 'UV' :
					$d_download = "disabled";
					break;
			}
			
			$upload = '<input type="button" name="upload[]" id="upload_'.$cRow.'_{item_name}" value="Upload" title="Upload" class="button" '.$d_upload.'/>';
			$view_list = '<input type="button" name="list[]" id="list_'.$cRow.'_{item_name}" value="Lihat Data" title="Lihat Data" class="button" '.$d_view.'/>';
			//$download = '<input type="button" name="download[]" id="download_'.$cRow.'_{item_name}" value="Export to CSV" title="Export to CSV" class="button" '.$d_download.'/>';
			
            foreach ($row as $key => $val) {
                if ($key == 0 || $key == 2 || $key == 4) {
                    continue;
                } else if ($key == 3) {
                    $data[] = str_replace('{item_name}', $val, $upload) . 
								'&nbsp;' . str_replace('{item_name}', $val, $view_list) . 
								'&nbsp;' . str_replace('{item_name}', $val, $download);
                } else {
                    $data[] = $val;
                }
            }
            $result['aaData'][] = $data;
        }

        return $result;
    }*/
}

