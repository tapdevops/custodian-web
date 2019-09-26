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
class Application_Model_HoCapex
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

        //$div_code = str_replace('&', "'||'&'||'", $row['DIV_CODE']);
        //$cc_code = str_replace('&', "'||'&'||'", $row['CC_CODE']);
        //$capex_desc = str_replace('&', "'||'&'||'", $row['CAPEX_DESCRIPTION']);

        $div_code = (empty($row['DIV_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['DIV_CODE']);
        $cc_code = (empty($row['CC_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['CC_CODE']);
        $capex_desc = (empty($row['CAPEX_DESCRIPTION'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['CAPEX_DESCRIPTION']);

        $check = "
            SELECT * FROM TM_HO_CAPEX
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
        ";
        $count = $this->_db->fetchOne($check);

        if ($count > 0) {
            $sql = "
                UPDATE TM_HO_CAPEX 
                SET 
                    PERIOD_BUDGET = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    CC_CODE = '".$cc_code."',
                    RK_ID = '{$row['RK_ID']}',
                    CAPEX_DESCRIPTION = '".$capex_desc."',
                    CORE_CODE = '{$row['CORE_CODE']}',
                    COMP_CODE = '{$row['COMP_CODE']}',
                    BA_CODE = '{$row['BA_CODE']}',
                    COA_CODE = '{$row['COA_CODE']}',
                    CAPEX_JAN = '{$row['CAPEX_JAN']}',
                    CAPEX_FEB = '{$row['CAPEX_FEB']}',
                    CAPEX_MAR = '{$row['CAPEX_MAR']}',
                    CAPEX_APR = '{$row['CAPEX_APR']}',
                    CAPEX_MAY = '{$row['CAPEX_MAY']}',
                    CAPEX_JUN = '{$row['CAPEX_JUN']}',
                    CAPEX_JUL = '{$row['CAPEX_JUL']}',
                    CAPEX_AUG = '{$row['CAPEX_AUG']}',
                    CAPEX_SEP = '{$row['CAPEX_SEP']}',
                    CAPEX_OCT = '{$row['CAPEX_OCT']}',
                    CAPEX_NOV = '{$row['CAPEX_NOV']}',
                    CAPEX_DEC = '{$row['CAPEX_DEC']}',
                    CAPEX_TOTAL = '{$row['CAPEX_TOTAL']}',
                    UPDATE_USER = '{$username}',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
            ";
        } else {
            $sql = "
                INSERT INTO TM_HO_CAPEX (
                    PERIOD_BUDGET,
                    CC_CODE,
                    RK_ID,
                    CAPEX_DESCRIPTION,
                    CORE_CODE,
                    COMP_CODE,
                    BA_CODE,
                    COA_CODE,
                    CAPEX_JAN,
                    CAPEX_FEB,
                    CAPEX_MAR,
                    CAPEX_APR,
                    CAPEX_MAY,
                    CAPEX_JUN,
                    CAPEX_JUL,
                    CAPEX_AUG,
                    CAPEX_SEP,
                    CAPEX_OCT,
                    CAPEX_NOV,
                    CAPEX_DEC,
                    CAPEX_TOTAL,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    '".$cc_code."',
                    '{$row['RK_ID']}',
                    '".$capex_desc."',
                    '{$row['CORE_CODE']}',
                    '{$row['COMP_CODE']}',
                    '{$row['BA_CODE']}',
                    '{$row['COA_CODE']}',
                    '{$row['CAPEX_JAN']}',
                    '{$row['CAPEX_FEB']}',
                    '{$row['CAPEX_MAR']}',
                    '{$row['CAPEX_APR']}',
                    '{$row['CAPEX_MAY']}',
                    '{$row['CAPEX_JUN']}',
                    '{$row['CAPEX_JUL']}',
                    '{$row['CAPEX_AUG']}',
                    '{$row['CAPEX_SEP']}',
                    '{$row['CAPEX_OCT']}',
                    '{$row['CAPEX_NOV']}',
                    '{$row['CAPEX_DEC']}',
                    '{$row['CAPEX_TOTAL']}',
                    '{$username}',
                    SYSDATE
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
                ROWIDTOCHAR(CPX.ROWID) row_id,
                TO_CHAR(CPX.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
                CPX.CC_CODE,
                CC.HCC_COST_CENTER,
                RK.ID AS RK_ID,
                RK.RK_NAME,
                CPX.CAPEX_DESCRIPTION,
                CPX.CORE_CODE,
                CPX.COMP_CODE,
                HC.COMPANY_NAME,
                CPX.BA_CODE,
                HC.BA_NAME,
                CPX.COA_CODE,
                COA.COA_NAME,
                CPX.CAPEX_JAN,
                CPX.CAPEX_FEB,
                CPX.CAPEX_MAR,
                CPX.CAPEX_APR,
                CPX.CAPEX_MAY,
                CPX.CAPEX_JUN,
                CPX.CAPEX_JUL,
                CPX.CAPEX_AUG,
                CPX.CAPEX_SEP,
                CPX.CAPEX_OCT,
                CPX.CAPEX_NOV,
                CPX.CAPEX_DEC,
                CPX.CAPEX_TOTAL
            FROM TM_HO_CAPEX CPX 
            LEFT JOIN TM_HO_COST_CENTER CC
                ON CC.HCC_CC = CPX.CC_CODE
            LEFT JOIN TM_HO_RENCANA_KERJA RK 
                ON RK.ID = CPX.RK_ID
            LEFT JOIN TM_HO_COMPANY HC
                ON HC.BA_CODE = CPX.BA_CODE
            LEFT JOIN TM_HO_COA COA
                ON COA.COA_CODE = CPX.COA_CODE
            WHERE CPX.DELETE_USER IS NULL
                --AND CPX.INSERT_USER = '{$username}'
        ";

        if($params['budgetperiod'] != '') {
            $query .= "
                AND to_char(CPX.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        } else {
            $query .= "
                AND to_char(CPX.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
        }
        
        /*if ($params['key_find'] != '') {
            $query .= "
                AND UPPER(THRK.DIV_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }*/

        if ($params['src_cc'] != '') {
            $explode = explode('-', $params['src_cc']);
            $query .= "
                AND UPPER(CPX.CC_CODE) = '".trim($explode[0])."'
            ";
        }

        if ($params['search'] != '') {
            $query .= "
                AND (
                    UPPER(CPX.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.CC_CODE) LIKE UPPER('%".$params['search']."%') 
                    OR UPPER(CC.HCC_COST_CENTER) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(RK.ID) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(RK.RK_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.CAPEX_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.CORE_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.COMP_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(HC.COMPANY_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.BA_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(HC.BA_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(CPX.COA_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(COA.COA_NAME) LIKE UPPER('%".$params['search']."%')
                )
            ";
        }

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
            UPDATE TM_HO_CAPEX
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

