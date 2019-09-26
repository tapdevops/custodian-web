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
class Application_Model_HoSpd
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

        $div_code = (empty($row['DIV_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['DIV_CODE']);
        $cc_code = (empty($row['CC_CODE'])) ? '' : str_replace(array('.', "'", '&'), array('', "''", '\'||\'&\'||\''), $row['CC_CODE']);

        //$rk_name = str_replace('&', "'||'&'||'", $row['RK_NAME']);
        
        /*$tiket = (empty($row['TIKET'])) ? 0 : str_replace('.', '', addslashes($row['TIKET']));
        $transport_lain = (empty($row['TRANSPORT_LAIN'])) ? 0 : str_replace('.', '', addslashes($row['TRANSPORT_LAIN']));
        $uang_makan = (empty($row['UANG_MAKAN'])) ? 0 : str_replace('.', '', addslashes($row['UANG_MAKAN']));
        $uang_saku = (empty($row['UANG_SAKU'])) ? 0 : str_replace('.', '', addslashes($row['UANG_SAKU']));
        $hotel_jlh_tarif = (empty($row['HOTEL_JLH_TARIF'])) ? 0 : str_replace('.', '', addslashes($row['HOTEL_JLH_TARIF']));
        $others = (empty($row['OTHERS'])) ? 0 : str_replace('.', '', addslashes($row['OTHERS']));
        $total = (empty($row['TOTAL'])) ? 0 : str_replace('.', '', addslashes($row['TOTAL']));
        $sebaran_jan = (empty($row['SEBARAN_JAN'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_JAN']));
        $sebaran_feb = (empty($row['SEBARAN_FEB'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_FEB']));
        $sebaran_mar = (empty($row['SEBARAN_MAR'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_MAR']));
        $sebaran_apr = (empty($row['SEBARAN_APR'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_APR']));
        $sebaran_may = (empty($row['SEBARAN_MAY'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_MAY']));
        $sebaran_jun = (empty($row['SEBARAN_JUN'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_JUN']));
        $sebaran_jul = (empty($row['SEBARAN_JUL'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_JUL']));
        $sebaran_aug = (empty($row['SEBARAN_AUG'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_AUG']));
        $sebaran_sep = (empty($row['SEBARAN_SEP'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_SEP']));
        $sebaran_oct = (empty($row['SEBARAN_OCT'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_OCT']));
        $sebaran_nov = (empty($row['SEBARAN_NOV'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_NOV']));
        $sebaran_dec = (empty($row['SEBARAN_DEC'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_DEC']));
        $sebaran_total = (empty($row['SEBARAN_TOTAL'])) ? 0 : str_replace('.', '', addslashes($row['SEBARAN_TOTAL']));*/

        $check = "
            SELECT * FROM TR_HO_SPD
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
        ";
        $count = $this->_db->fetchOne($check);

        if ($count > 0) {
            $sql = "
                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    DIV_CODE        = '".$div_code."',
                    CC_CODE         = '".$cc_code."',
                    RK_ID           = '{$row['RK_ID']}',
                    SPD_DESCRIPTION = '{$row['SPD_DESCRIPTION']}',
                    COA_CODE        = '{$row['COA_CODE']}',
                    NORMA_SPD_ID    = '{$row['NORMA_SPD_ID']}',
                    CORE_CODE       = '{$row['CORE_CODE']}',
                    COMP_CODE       = '{$row['COMP_CODE']}',
                    BA_CODE         = '{$row['BA_CODE']}',
                    PLAN            = '{$row['PLAN']}',
                    GOLONGAN        = '{$row['GOLONGAN']}',
                    JLH_PRIA        = '{$row['JLH_PRIA']}',
                    JLH_WANITA      = '{$row['JLH_WANITA']}',
                    JLH_HARI        = '{$row['JLH_HARI']}',
                    TIKET           = '{$row['TIKET']}',
                    TRANSPORT_LAIN  = '{$row['TRANSPORT_LAIN']}',
                    UANG_MAKAN      = '{$row['UANG_MAKAN']}',
                    UANG_SAKU       = '{$row['UANG_SAKU']}',
                    HOTEL_JLH_HARI  = '{$row['HOTEL_JLH_HARI']}',
                    HOTEL_JLH_TARIF = '{$row['HOTEL_JLH_TARIF']}',
                    OTHERS          = '{$row['OTHERS']}',
                    TOTAL           = '{$row['TOTAL']}',
                    REMARKS_OTHERS  = '{$row['REMARKS_OTHERS']}',
                    SEBARAN_JAN     = '{$row['SEBARAN_JAN']}',
                    SEBARAN_FEB     = '{$row['SEBARAN_FEB']}',
                    SEBARAN_MAR     = '{$row['SEBARAN_MAR']}',
                    SEBARAN_APR     = '{$row['SEBARAN_APR']}',
                    SEBARAN_MAY     = '{$row['SEBARAN_MAY']}',
                    SEBARAN_JUN     = '{$row['SEBARAN_JUN']}',
                    SEBARAN_JUL     = '{$row['SEBARAN_JUL']}',
                    SEBARAN_AUG     = '{$row['SEBARAN_AUG']}',
                    SEBARAN_SEP     = '{$row['SEBARAN_SEP']}',
                    SEBARAN_OCT     = '{$row['SEBARAN_OCT']}',
                    SEBARAN_NOV     = '{$row['SEBARAN_NOV']}',
                    SEBARAN_DEC     = '{$row['SEBARAN_DEC']}',
                    SEBARAN_TOTAL   = '{$row['SEBARAN_TOTAL']}',
                    TIPE_NORMA      = '{$row['TIPE_NORMA']}',
                    UPDATE_USER     = '{$username}',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
            ";
        } else {
            $sql = "
                INSERT INTO TR_HO_SPD (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_ID,
                    SPD_DESCRIPTION,
                    COA_CODE,
                    NORMA_SPD_ID,
                    CORE_CODE,
                    COMP_CODE,
                    BA_CODE,
                    PLAN,
                    GOLONGAN,
                    JLH_PRIA,
                    JLH_WANITA,
                    JLH_HARI,
                    TIKET,
                    TRANSPORT_LAIN,
                    UANG_MAKAN,
                    UANG_SAKU,
                    HOTEL_JLH_HARI,
                    HOTEL_JLH_TARIF,
                    OTHERS,
                    TOTAL,
                    REMARKS_OTHERS,
                    SEBARAN_JAN,
                    SEBARAN_FEB,
                    SEBARAN_MAR,
                    SEBARAN_APR,
                    SEBARAN_MAY,
                    SEBARAN_JUN,
                    SEBARAN_JUL,
                    SEBARAN_AUG,
                    SEBARAN_SEP,
                    SEBARAN_OCT,
                    SEBARAN_NOV,
                    SEBARAN_DEC,
                    SEBARAN_TOTAL,
                    TIPE_NORMA,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('{$row['PERIOD_BUDGET']}', 'YYYY'),
                    '".$div_code."',
                    '".$cc_code."',
                    '{$row['RK_ID']}',
                    '{$row['SPD_DESCRIPTION']}',
                    '{$row['COA_CODE']}',
                    '{$row['NORMA_SPD_ID']}',
                    '{$row['CORE_CODE']}',
                    '{$row['COMP_CODE']}',
                    '{$row['BA_CODE']}',
                    '{$row['PLAN']}',
                    '{$row['GOLONGAN']}',
                    '{$row['JLH_PRIA']}',
                    '{$row['JLH_WANITA']}',
                    '{$row['JLH_HARI']}',
                    '{$row['TIKET']}',
                    '{$row['TRANSPORT_LAIN']}',
                    '{$row['UANG_MAKAN']}',
                    '{$row['UANG_SAKU']}',
                    '{$row['HOTEL_JLH_HARI']}',
                    '{$row['HOTEL_JLH_TARIF']}',
                    '{$row['OTHERS']}',
                    '{$row['TOTAL']}',
                    '{$row['REMARKS_OTHERS']}',
                    '{$row['SEBARAN_JAN']}',
                    '{$row['SEBARAN_FEB']}',
                    '{$row['SEBARAN_MAR']}',
                    '{$row['SEBARAN_APR']}',
                    '{$row['SEBARAN_MAY']}',
                    '{$row['SEBARAN_JUN']}',
                    '{$row['SEBARAN_JUL']}',
                    '{$row['SEBARAN_AUG']}',
                    '{$row['SEBARAN_SEP']}',
                    '{$row['SEBARAN_OCT']}',
                    '{$row['SEBARAN_NOV']}',
                    '{$row['SEBARAN_DEC']}',
                    '{$row['SEBARAN_TOTAL']}',
                    '{$row['TIPE_NORMA']}',
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
                ROW_ID,
                PERIOD_BUDGET,
                DIV_CODE,
                DIV_NAME,
                CC_CODE,
                HCC_CC,
                HCC_COST_CENTER,
                RK_ID,
                RK_NAME,
                SPD_DESCRIPTION,
                COA_CODE,
                COA_NAME,
                NORMA_SPD_ID,
                RUTE,
                PLANE_N_PRICE,
                PLANE_P_PRICE,
                TAXI_QTY,
                TAXI_N_PRICE,
                CHARTER_QTY,
                CHARTER_N_PRICE,
                WATER_VEH_QTY,
                WATER_VEH_N_PRICE,
                CORE_CODE,
                CORE_NAME,
                COMP_CODE,
                (SELECT COMPANY_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND BA_CODE = B_CODE AND DELETE_USER IS NULL) COMPANY_NAME,
                BA_CODE,
                (SELECT BA_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND BA_CODE = B_CODE AND DELETE_USER IS NULL) BA_NAME,
                PLAN,
                GOLONGAN,
                JLH_PRIA,
                JLH_WANITA,
                JLH_HARI,
                TIKET,
                TRANSPORT_LAIN,
                UANG_MAKAN,
                UANG_SAKU,
                HOTEL_JLH_HARI,
                HOTEL_JLH_TARIF,
                OTHERS,
                TOTAL,
                REMARKS_OTHERS,
                SEBARAN_JAN,
                SEBARAN_FEB,
                SEBARAN_MAR,
                SEBARAN_APR,
                SEBARAN_MAY,
                SEBARAN_JUN,
                SEBARAN_JUL,
                SEBARAN_AUG,
                SEBARAN_SEP,
                SEBARAN_OCT,
                SEBARAN_NOV,
                SEBARAN_DEC,
                SEBARAN_TOTAL,
                TIPE_NORMA
                FROM
            (
                SELECT 
                    ROWIDTOCHAR(SPD.ROWID) row_id,
                    TO_CHAR(SPD.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
                    SPD.DIV_CODE,
                    THD.DIV_NAME,
                    SPD.CC_CODE,
                    HCC.HCC_CC,
                    HCC.HCC_COST_CENTER,
                    SPD.RK_ID,
                    HRK.RK_NAME,
                    SPD.SPD_DESCRIPTION,
                    SPD.COA_CODE,
                    COA.COA_NAME,
                    SPD.NORMA_SPD_ID,
                    NSP.RUTE,
                    NSP.PLANE_N_PRICE,
                    NSP.PLANE_P_PRICE,
                    NSP.TAXI_QTY,
                    NSP.TAXI_N_PRICE,
                    NSP.CHARTER_QTY,
                    NSP.CHARTER_N_PRICE,
                    NSP.WATER_VEH_QTY,
                    NSP.WATER_VEH_N_PRICE,
                    SPD.CORE_CODE,
                    SPD.CORE_CODE AS CR_CODE,
                    COR.CORE_NAME,
                    SPD.COMP_CODE,
                    SPD.COMP_CODE AS C_CODE,
                    --COM.COMPANY_NAME,
                    SPD.BA_CODE,
                    SPD.BA_CODE AS B_CODE,
                    --COM.BA_NAME,
                    SPD.PLAN,
                    SPD.GOLONGAN,
                    SPD.JLH_PRIA,
                    SPD.JLH_WANITA,
                    SPD.JLH_HARI,
                    SPD.TIKET,
                    SPD.TRANSPORT_LAIN,
                    SPD.UANG_MAKAN,
                    SPD.UANG_SAKU,
                    SPD.HOTEL_JLH_HARI,
                    SPD.HOTEL_JLH_TARIF,
                    SPD.OTHERS,
                    SPD.TOTAL,
                    SPD.REMARKS_OTHERS,
                    SPD.SEBARAN_JAN,
                    SPD.SEBARAN_FEB,
                    SPD.SEBARAN_MAR,
                    SPD.SEBARAN_APR,
                    SPD.SEBARAN_MAY,
                    SPD.SEBARAN_JUN,
                    SPD.SEBARAN_JUL,
                    SPD.SEBARAN_AUG,
                    SPD.SEBARAN_SEP,
                    SPD.SEBARAN_OCT,
                    SPD.SEBARAN_NOV,
                    SPD.SEBARAN_DEC,
                    SPD.SEBARAN_TOTAL,
                    SPD.TIPE_NORMA
                FROM TR_HO_SPD SPD
                LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = SPD.DIV_CODE 
                LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
                LEFT JOIN TM_HO_RENCANA_KERJA HRK ON HRK.ID = SPD.RK_ID
                LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
                LEFT JOIN TM_HO_NORMA_SPD NSP ON NSP.ID = SPD.NORMA_SPD_ID
                LEFT JOIN TM_HO_CORE COR ON COR.CORE_CODE = SPD.CORE_CODE
                --LEFT JOIN TM_HO_COMPANY COM ON COM.BA_CODE = SPD.BA_CODE
                WHERE 
                    SPD.DELETE_USER IS NULL
                    --AND SPD.INSERT_USER = '{$username}'
        ";

        if($params['budgetperiod'] != '') {
            $query .= "
                AND to_char(SPD.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        } else {
            $query .= "
                AND to_char(SPD.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
        }
        
        if ($params['key_find'] != '') {
            $query .= "
                AND UPPER(SPD.DIV_CODE) = '".$params['key_find']."'
            ";
        }

        if ($params['src_cc'] != '') {
            $explode = explode('-', $params['src_cc']);
            $query .= "
                AND UPPER(SPD.CC_CODE) = '".trim($explode[0])."'
            ";
        }

        if ($params['search'] != '') {
            $query .= "
                AND (
                    UPPER(SPD.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.DIV_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(THD.DIV_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.CC_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(HCC.HCC_CC) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(HCC.HCC_COST_CENTER) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.RK_ID) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(HRK.RK_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.SPD_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.COA_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(COA.COA_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.NORMA_SPD_ID) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(NSP.RUTE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.CORE_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(COR.CORE_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.COMP_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(COM.COMPANY_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.BA_CODE) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(COM.BA_NAME) LIKE UPPER('%".$params['search']."%')
                    OR UPPER(SPD.TIPE_NORMA) LIKE UPPER('%".$params['search']."%')
                )
            ";
        }

        $query .= " ORDER BY SPD.INSERT_TIME)";

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
            UPDATE TR_HO_SPD
            SET DELETE_USER = '{$this->_userName}',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
        ";

        $this->_global->createSqlFile($row['filename'], $sql);
        return true;
    }
    
}

