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
class Application_Model_HoRencanaKerja
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
        //echo '<pre>'; print_r (Zend_Registry::get('auth')->getIdentity());
        
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
        //$rk_name = str_replace('&', "'||'&'||'", $row['RK_NAME']);
        //$rk_desc = str_replace('&', "'||'&'||'", $row['RK_DESCRIPTION']);

        $div_code = (empty($row['DIV_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['DIV_CODE']);
        $cc_code = (empty($row['CC_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['CC_CODE']);
        $rk_name = (empty($row['RK_NAME'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['RK_NAME']);
        $rk_desc = (empty($row['RK_DESCRIPTION'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['RK_DESCRIPTION']);

        $check = "
            SELECT * FROM TM_HO_RENCANA_KERJA
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
        ";
        $count = $this->_db->fetchOne($check);

        if ($count > 0) {
            $sql = "
                UPDATE TM_HO_RENCANA_KERJA 
                SET 
                    PERIOD_BUDGET = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    DIV_CODE = '".$div_code."',
                    CC_CODE = '".$cc_code."',
                    RK_NAME = '".$rk_name."',
                    RK_DESCRIPTION = '".$rk_desc."',
                    UPDATE_USER = '{$username}',
                    UPDATE_TIME = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
            ";
        } else {
            $sql = "
                INSERT INTO TM_HO_RENCANA_KERJA (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_NAME,
                    RK_DESCRIPTION,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    '".$div_code."',
                    '".$cc_code."',
                    '".$rk_name."',
                    '".$rk_desc."',
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
                ROWIDTOCHAR(THRK.ROWID) row_id,
                TO_CHAR(THRK.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
                THD.DIV_CODE,
                THD.DIV_NAME,
                THCC.HCC_CC,
                THCC.HCC_COST_CENTER,
                THRK.RK_NAME,
                THRK.RK_DESCRIPTION
            FROM TM_HO_RENCANA_KERJA THRK 
            LEFT JOIN TM_HO_COST_CENTER THCC 
                ON THCC.HCC_CC = THRK.CC_CODE
            LEFT JOIN TM_HO_DIVISION THD
                ON THD.DIV_CODE = THRK.DIV_CODE
            WHERE THRK.DELETE_USER IS NULL
                --AND THRK.INSERT_USER = '{$username}'
        ";

        if($params['budgetperiod'] != '') {
            $query .= "
                AND to_char(THRK.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        } else {
            $query .= "
                AND to_char(THRK.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
        }
        
        if ($params['key_find'] != '') {
            $query .= "
                AND UPPER(THRK.DIV_CODE) = '".$params['key_find']."'
            ";
        }

        if ($params['src_cc'] != '') {
            $explode = explode('-', $params['src_cc']);
            $query .= "
                AND UPPER(THRK.CC_CODE) = '".trim($explode[0])."'
            ";
        }

        if ($params['search'] != '') {
            $query .= "
                AND (
                    UPPER(THRK.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THRK.DIV_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THRK.CC_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THRK.RK_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THRK.RK_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
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

    public function getStatusLock($params = array()) {
        $query = "
            SELECT COUNT(*) FROM TR_HO_REPORT_VALID 
            WHERE TO_CHAR(PERIOD_BUDGET, 'YYYY') = '".$params['budgetperiod']."' 
                AND DIV_CODE = '".$params['key_find_div']."' AND CC_CODE = '".$params['key_find_cc']."'
        ";

        $result['locked'] = $this->_db->fetchOne($query);
        
        return $result;
    }

    //hapus data
    public function delete($row = array())
    {
        $sql = "
            UPDATE TM_HO_RENCANA_KERJA
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

