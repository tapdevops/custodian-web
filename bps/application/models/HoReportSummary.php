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
class Application_Model_HoReportSummary {
	private $_db = null;
	private $_global = null;
	private $_siteCode = '';

	public function __construct() {
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
	private function initMasterSetting($params = array()) {
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

	//setting input untuk region dan maturity stage
	public function getInput() {
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

	public function getLastGenerate($params = array()) {
		$where = "";

		if ($params['budgetperiod'] != '') $where .= " AND TO_CHAR(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'";
		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."'";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."'";

		switch ($params['jenis_report']) {
			case "ho_summary_budget" :
					$table = "TMP_HO_SUM_BUDGET";
				break;
			case "ho_budget" :
					$table = "TMP_HO_BUDGET";
				break;
			case "ho_opex" :
					$table = "TMP_HO_OPEX";
				break;
			case "ho_capex" :
					$table = "TMP_HO_CAPEX";
				break;
			case "ho_spd" :
					$table = "TMP_HO_SPD";
				break;
			case "ho_profit_loss" :
					$table = "TMP_HO_PROFIT_LOSS";
				break;
		}
		
		$query = "
			SELECT  
				MAX(INSERT_USER) INSERT_USER,
				TO_CHAR( MAX(INSERT_TIME), 'DD-MM-RRRR HH24:MI:SS') INSERT_TIME
			FROM (
				SELECT  
					MAX(INSERT_USER) INSERT_USER,
					MAX(INSERT_TIME) INSERT_TIME
				FROM ".$table."
				WHERE 1 = 1
				$where
			)
		";

		$result = $this->_db->fetchRow("{$query}");

		return $result;
	}

	public function delTmpReportHo($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		switch ($params['jenis_report']) {
			case "ho_summary_budget" :
					$table = "TMP_HO_SUM_BUDGET";
				break;
			case "ho_budget" :
					$table = "TMP_HO_BUDGET";
				break;
			case "ho_opex" :
					$table = "TMP_HO_OPEX";
				break;
			case "ho_capex" :
					$table = "TMP_HO_CAPEX";
				break;
			case "ho_spd" :
					$table = "TMP_HO_SPD";
				break;
			case "ho_profit_loss" :
					$table = "TMP_HO_PROFIT_LOSS";
				break;
		}

		$query = "
			DELETE FROM ".$table." WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '" . $period_budget . "' AND CC_CODE = '".$find_cc."' AND DIV_CODE = '".$find_div."'
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function tmpHoSummaryBudget($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		$query = "
			INSERT INTO TMP_HO_SUM_BUDGET (
				PERIOD_BUDGET, DIV_CODE, DIV_NAME, CC_CODE, CC_NAME, 
				GROUP01, COA_CODE, COA_DESC, 
				DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN,
				DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC,
				INSERT_USER, INSERT_TIME, OUTLOOK
			)
			SELECT 
				PERIOD_BUDGET, DIV_CODE, DIV_NAME, CC_CODE, COST_CENTER_NAME,
				GROUP_01,
				COA, KETERANGAN_COA,
				SUM(DIST_JAN) DIST_JAN,
				SUM(DIST_FEB) DIST_FEB,
				SUM(DIST_MAR) DIST_MAR,
				SUM(DIST_APR) DIST_APR,
				SUM(DIST_MAY) DIST_MAY,
				SUM(DIST_JUN) DIST_JUN,
				SUM(DIST_JUL) DIST_JUL,
				SUM(DIST_AUG) DIST_AUG,
				SUM(DIST_SEP) DIST_SEP,
				SUM(DIST_OCT) DIST_OCT,
				SUM(DIST_NOV) DIST_NOV,
				SUM(DIST_DEC) DIST_DEC,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME,
				SUM(OUTLOOK) OUTLOOK
			FROM (
				SELECT 
					THC.PERIOD_BUDGET, THD.DIV_CODE, THD.DIV_NAME, 
					TCC.HCC_CC CC_CODE, TCC.HCC_COST_CENTER COST_CENTER_NAME,
					'CAPEX' GROUP_01,
					COA.COA_CODE COA, COA.COA_NAME KETERANGAN_COA,
					CAPEX_JAN DIST_JAN, CAPEX_FEB DIST_FEB, CAPEX_MAR DIST_MAR, CAPEX_APR DIST_APR, CAPEX_MAY DIST_MAY, CAPEX_JUN DIST_JUN, 
					CAPEX_JUL DIST_JUL, CAPEX_AUG DIST_AUG, CAPEX_SEP DIST_SEP, CAPEX_OCT DIST_OCT, CAPEX_NOV DIST_NOV, CAPEX_DEC DIST_DEC,
					0 OUTLOOK
				FROM TM_HO_CAPEX THC
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = THC.COA_CODE
				LEFT JOIN TM_HO_COST_CENTER TCC ON TCC.HCC_CC = THC.CC_CODE
				LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = TCC.HCC_DIVISI
				WHERE THC.CC_CODE = '".$find_cc."' AND TO_CHAR(THC.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' AND THC.DELETE_USER IS NULL
				AND THD.DELETE_USER IS NULL

				UNION ALL

				SELECT 
					THO.PERIOD_BUDGET, THD.DIV_CODE, THD.DIV_NAME, 
					TCC.HCC_CC CC_CODE, TCC.HCC_COST_CENTER COST_CENTER_NAME,
					'OPEX' GROUP_01,
					COA.COA_CODE COA, COA.COA_NAME KETERANGAN_COA,
					OPEX_JAN DIST_JAN, OPEX_FEB DIST_FEB, OPEX_MAR DIST_MAR, OPEX_APR DIST_APR, OPEX_MAY DIST_MAY, OPEX_JUN DIST_JUN, 
					OPEX_JUL DIST_JUL, OPEX_AUG DIST_AUG, OPEX_SEP DIST_SEP, OPEX_OCT DIST_OCT, OPEX_NOV DIST_NOV, OPEX_DEC DIST_DEC, 
					0 OUTLOOK
				FROM TM_HO_OPEX THO 
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = THO.COA_CODE
				LEFT JOIN TM_HO_COST_CENTER TCC ON TCC.HCC_CC = THO.CC_CODE
				LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = TCC.HCC_DIVISI
				WHERE THO.CC_CODE = '".$find_cc."' AND TO_CHAR(THO.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' AND THO.DELETE_USER IS NULL
				AND THD.DELETE_USER IS NULL

				UNION ALL

				SELECT 
					SPD.PERIOD_BUDGET, THD.DIV_CODE, THD.DIV_NAME, 
					TCC.HCC_CC CC_CODE, TCC.HCC_COST_CENTER COST_CENTER_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE COA, COA.COA_NAME KETERANGAN_COA,
					SEBARAN_JAN DIST_JAN, SEBARAN_FEB DIST_FEB, SEBARAN_MAR DIST_MAR, SEBARAN_APR DIST_APR, SEBARAN_MAY DIST_MAY, SEBARAN_JUN DIST_JUN, 
					SEBARAN_JUL DIST_JUL, SEBARAN_AUG DIST_AUG, SEBARAN_SEP DIST_SEP, SEBARAN_OCT DIST_OCT, SEBARAN_NOV DIST_NOV, SEBARAN_DEC DIST_DEC, 
					0 OUTLOOK
				FROM TR_HO_SPD SPD
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_COST_CENTER TCC ON TCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = TCC.HCC_DIVISI
				WHERE SPD.CC_CODE = '".$find_cc."' AND TO_CHAR(SPD.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' AND SPD.DELETE_USER IS NULL
				AND THD.DELETE_USER IS NULL

				UNION ALL

				SELECT 
					HAO.PERIOD_BUDGET, THD.DIV_CODE, THD.DIV_NAME, 
					TCC.HCC_CC CC_CODE, TCC.HCC_COST_CENTER COST_CENTER_NAME,
					'OUTLOOK' GROUP_01,
					COA.COA_CODE COA, COA.COA_NAME KETERANGAN_COA,
					ACT_JAN DIST_JAN, ACT_FEB DIST_FEB, ACT_MAR DIST_MAR, ACT_APR DIST_APR, ACT_MAY DIST_MAY, ACT_JUN DIST_JUN,
					ACT_JUL DIST_JUL, ACT_AUG DIST_AUG, OUTLOOK_SEP DIST_SEP, OUTLOOK_OCT DIST_OCT, OUTLOOK_NOV DIST_NOV, OUTLOOK_DEC DIST_DEC, 
					OUTLOOK
				FROM TM_HO_ACT_OUTLOOK HAO 
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = HAO.COA_CODE
				LEFT JOIN TM_HO_COST_CENTER TCC ON TCC.HCC_CC = HAO.CC_CODE
				LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = TCC.HCC_DIVISI
				WHERE HAO.CC_CODE = '".$find_cc."' AND TO_CHAR(HAO.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' AND HAO.DELETE_USER IS NULL
				AND THD.DELETE_USER IS NULL
			) 
			GROUP BY PERIOD_BUDGET, DIV_CODE, DIV_NAME, CC_CODE, COST_CENTER_NAME, GROUP_01, COA, KETERANGAN_COA 
			ORDER BY GROUP_01
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function tmpHoBudget($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		$query = "
			INSERT INTO TMP_HO_BUDGET (
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				DIS_JAN,
				DIS_FEB,
				DIS_MAR,
				DIS_APR,
				DIS_MAY,
				DIS_JUN,
				DIS_JUL,
				DIS_AUG,
				DIS_SEP,
				DIS_OCT,
				DIS_NOV,
				DIS_DEC,
				DIS_TOTAL,
				INSERT_USER,
				INSERT_TIME
			)
			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					CAPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'CAPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					CAPEX.CAPEX_JAN DIS_JAN,
					CAPEX.CAPEX_FEB DIS_FEB,
					CAPEX.CAPEX_MAR DIS_MAR,
					CAPEX.CAPEX_APR DIS_APR,
					CAPEX.CAPEX_MAY DIS_MAY,
					CAPEX.CAPEX_JUN DIS_JUN,
					CAPEX.CAPEX_JUL DIS_JUL,
					CAPEX.CAPEX_AUG DIS_AUG,
					CAPEX.CAPEX_SEP DIS_SEP,
					CAPEX.CAPEX_OCT DIS_OCT,
					CAPEX.CAPEX_NOV DIS_NOV,
					CAPEX.CAPEX_DEC DIS_DEC,
					CAPEX.CAPEX_TOTAL DIS_TOTAL
				FROM TM_HO_CAPEX CAPEX
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = CAPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = CAPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = CAPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = CAPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = CAPEX.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(CAPEX.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND CAPEX.CC_CODE = '".$find_cc."'
					AND CAPEX.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					OPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'OPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					OPEX.OPEX_JAN DIS_JAN,
					OPEX.OPEX_FEB DIS_FEB,
					OPEX.OPEX_MAR DIS_MAR,
					OPEX.OPEX_APR DIS_APR,
					OPEX.OPEX_MAY DIS_MAY,
					OPEX.OPEX_JUN DIS_JUN,
					OPEX.OPEX_JUL DIS_JUL,
					OPEX.OPEX_AUG DIS_AUG,
					OPEX.OPEX_SEP DIS_SEP,
					OPEX.OPEX_OCT DIS_OCT,
					OPEX.OPEX_NOV DIS_NOV,
					OPEX.OPEX_DEC DIS_DEC,
					OPEX.OPEX_TOTAL DIS_TOTAL
				FROM TM_HO_OPEX OPEX
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = OPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = OPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = OPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = OPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = OPEX.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(OPEX.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND OPEX.CC_CODE = '".$find_cc."'
					AND OPEX.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					SPD.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					SPD.SEBARAN_JAN DIS_JAN,
					SPD.SEBARAN_FEB DIS_FEB,
					SPD.SEBARAN_MAR DIS_MAR,
					SPD.SEBARAN_APR DIS_APR,
					SPD.SEBARAN_MAY DIS_MAY,
					SPD.SEBARAN_JUN DIS_JUN,
					SPD.SEBARAN_JUL DIS_JUL,
					SPD.SEBARAN_AUG DIS_AUG,
					SPD.SEBARAN_SEP DIS_SEP,
					SPD.SEBARAN_OCT DIS_OCT,
					SPD.SEBARAN_NOV DIS_NOV,
					SPD.SEBARAN_DEC DIS_DEC,
					SPD.SEBARAN_TOTAL DIS_TOTAL
				FROM TR_HO_SPD SPD
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = SPD.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = SPD.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = SPD.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(SPD.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND SPD.CC_CODE = '".$find_cc."'
					AND SPD.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function tmpHoOpex($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		$query = "
			INSERT INTO TMP_HO_OPEX (
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				DIS_JAN,
				DIS_FEB,
				DIS_MAR,
				DIS_APR,
				DIS_MAY,
				DIS_JUN,
				DIS_JUL,
				DIS_AUG,
				DIS_SEP,
				DIS_OCT,
				DIS_NOV,
				DIS_DEC,
				DIS_TOTAL,
				ADJ,
				OUTLOOK,
				INSERT_USER,
				INSERT_TIME
			)
			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					OPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'OPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					OPEX.OPEX_JAN DIS_JAN,
					OPEX.OPEX_FEB DIS_FEB,
					OPEX.OPEX_MAR DIS_MAR,
					OPEX.OPEX_APR DIS_APR,
					OPEX.OPEX_MAY DIS_MAY,
					OPEX.OPEX_JUN DIS_JUN,
					OPEX.OPEX_JUL DIS_JUL,
					OPEX.OPEX_AUG DIS_AUG,
					OPEX.OPEX_SEP DIS_SEP,
					OPEX.OPEX_OCT DIS_OCT,
					OPEX.OPEX_NOV DIS_NOV,
					OPEX.OPEX_DEC DIS_DEC,
					OPEX.OPEX_TOTAL DIS_TOTAL,
					0 ADJ,
					0 OUTLOOK
				FROM TM_HO_OPEX OPEX 
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = OPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = OPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = OPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = OPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = OPEX.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(OPEX.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' 
					AND OPEX.CC_CODE = '".$find_cc."'
					AND OPEX.DELETE_USER IS NULL
                    AND DIV.DELETE_USER IS NULL
			) GROUP BY
			PERIOD_BUDGET,
			DIV_CODE,
			DIV_NAME,
			CC_CODE,
			CC_NAME,
			GROUP_01,
			COA_CODE,
			COA_DESC,
			RK_NAME,
			RK_DESCRIPTION,
			CORE_CODE,
			CORE_NAME,
			COMP_CODE,
			COMP_NAME,
			BA_CODE,
			BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					SPD.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					SPD.SEBARAN_JAN DIS_JAN,
					SPD.SEBARAN_FEB DIS_FEB,
					SPD.SEBARAN_MAR DIS_MAR,
					SPD.SEBARAN_APR DIS_APR,
					SPD.SEBARAN_MAY DIS_MAY,
					SPD.SEBARAN_JUN DIS_JUN,
					SPD.SEBARAN_JUL DIS_JUL,
					SPD.SEBARAN_AUG DIS_AUG,
					SPD.SEBARAN_SEP DIS_SEP,
					SPD.SEBARAN_OCT DIS_OCT,
					SPD.SEBARAN_NOV DIS_NOV,
					SPD.SEBARAN_DEC DIS_DEC,
					SPD.SEBARAN_TOTAL DIS_TOTAL,
					0 ADJ,
					0 OUTLOOK
				FROM TR_HO_SPD SPD
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = SPD.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = SPD.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = SPD.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(SPD.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND SPD.CC_CODE = '".$find_cc."'
					AND SPD.DELETE_USER IS NULL
                    AND DIV.DELETE_USER IS NULL
					AND COA.COA_CODE IN (SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%')
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (

				SELECT 
					PERIOD_BUDGET,
					(SELECT DISTINCT DIV_CODE FROM TM_HO_COST_CENTER THC LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = THC.HCC_DIVISI WHERE THC.HCC_CC = CC_CODE AND THD.DELETE_USER IS NULL) DIV_CODE,
					(SELECT DISTINCT DIV_NAME FROM TM_HO_COST_CENTER THC LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = THC.HCC_DIVISI WHERE THC.HCC_CC = CC_CODE AND THD.DELETE_USER IS NULL) DIV_NAME,
					CC_CODE,
					CC_NAME,
					GROUP_01,
					COA_CODE,
					COA_DESC,
					RK_NAME,
					RK_DESCRIPTION,
					CORE_CODE,
					CORE_NAME,
					COMP_CODE,
                    (SELECT DISTINCT COMPANY_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND DELETE_USER IS NULL) COMP_NAME,
                    (SELECT DISTINCT BA_CODE FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND DELETE_USER IS NULL) BA_CODE,
                    (SELECT DISTINCT BA_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND DELETE_USER IS NULL) BA_NAME,
					DIS_JAN,
					DIS_FEB,
					DIS_MAR,
					DIS_APR,
					DIS_MAY,
					DIS_JUN,
					DIS_JUL,
					DIS_AUG,
					DIS_SEP,
					DIS_OCT,
					DIS_NOV,
					DIS_DEC,
					DIS_TOTAL,
					ADJ,
					OUTLOOK
				FROM (
					SELECT 
						ACT.PERIOD_BUDGET,
						HCC.HCC_CC CC_CODE,
						HCC.HCC_COST_CENTER CC_NAME,
						'OUTLOOK' GROUP_01,
						COA.COA_CODE,
						COA.COA_NAME COA_DESC,
						'OUTLOOK 2018' RK_NAME,
						ACT.TRANSACTION_DESC RK_DESCRIPTION,
						CORE.CORE_CODE,
						CORE.CORE_CODE AS CR_CODE,
						CORE.CORE_NAME,
	                    ACT.COMP_CODE,
	                    ACT.COMP_CODE AS C_CODE,
						ACT.ACT_JAN DIS_JAN,
						ACT.ACT_FEB DIS_FEB,
						ACT.ACT_MAR DIS_MAR,
						ACT.ACT_APR DIS_APR,
						ACT.ACT_MAY DIS_MAY,
						ACT.ACT_JUN DIS_JUN,
						ACT.ACT_JUL DIS_JUL,
						ACT.ACT_AUG DIS_AUG,
						ACT.OUTLOOK_SEP DIS_SEP,
						ACT.OUTLOOK_OCT DIS_OCT,
						ACT.OUTLOOK_NOV DIS_NOV,
						ACT.OUTLOOK_DEC DIS_DEC,
						0 DIS_TOTAL,
						0 ADJ,
						ACT.TOTAL_ACTUAL OUTLOOK
					FROM TM_HO_ACT_OUTLOOK ACT
					LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = ACT.CC_CODE
					LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = ACT.COA_CODE
					LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = ACT.CORE
					WHERE TO_CHAR(ACT.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
						AND ACT.CC_CODE = '".$find_cc."'
						AND ACT.DELETE_USER IS NULL
						AND COA.COA_CODE IN (SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'OPEX%')
				)
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function tmpHoCapex($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		$query = "
			INSERT INTO TMP_HO_CAPEX (
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				DIS_JAN,
				DIS_FEB,
				DIS_MAR,
				DIS_APR,
				DIS_MAY,
				DIS_JUN,
				DIS_JUL,
				DIS_AUG,
				DIS_SEP,
				DIS_OCT,
				DIS_NOV,
				DIS_DEC,
				DIS_TOTAL,
				ADJ,
				OUTLOOK,
				INSERT_USER,
				INSERT_TIME
			)
			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					CAPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'CAPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					CAPEX.CAPEX_JAN DIS_JAN,
					CAPEX.CAPEX_FEB DIS_FEB,
					CAPEX.CAPEX_MAR DIS_MAR,
					CAPEX.CAPEX_APR DIS_APR,
					CAPEX.CAPEX_MAY DIS_MAY,
					CAPEX.CAPEX_JUN DIS_JUN,
					CAPEX.CAPEX_JUL DIS_JUL,
					CAPEX.CAPEX_AUG DIS_AUG,
					CAPEX.CAPEX_SEP DIS_SEP,
					CAPEX.CAPEX_OCT DIS_OCT,
					CAPEX.CAPEX_NOV DIS_NOV,
					CAPEX.CAPEX_DEC DIS_DEC,
					CAPEX.CAPEX_TOTAL DIS_TOTAL,
					0 ADJ,
					0 OUTLOOK
				FROM TM_HO_CAPEX CAPEX 
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = CAPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = CAPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = CAPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = CAPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = CAPEX.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(CAPEX.PERIOD_BUDGET, 'RRRR') = '".$period_budget."' 
					AND CAPEX.CC_CODE = '".$find_cc."'
					AND CAPEX.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
			) GROUP BY
			PERIOD_BUDGET,
			DIV_CODE,
			DIV_NAME,
			CC_CODE,
			CC_NAME,
			GROUP_01,
			COA_CODE,
			COA_DESC,
			RK_NAME,
			RK_DESCRIPTION,
			CORE_CODE,
			CORE_NAME,
			COMP_CODE,
			COMP_NAME,
			BA_CODE,
			BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					SPD.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					SPD.SEBARAN_JAN DIS_JAN,
					SPD.SEBARAN_FEB DIS_FEB,
					SPD.SEBARAN_MAR DIS_MAR,
					SPD.SEBARAN_APR DIS_APR,
					SPD.SEBARAN_MAY DIS_MAY,
					SPD.SEBARAN_JUN DIS_JUN,
					SPD.SEBARAN_JUL DIS_JUL,
					SPD.SEBARAN_AUG DIS_AUG,
					SPD.SEBARAN_SEP DIS_SEP,
					SPD.SEBARAN_OCT DIS_OCT,
					SPD.SEBARAN_NOV DIS_NOV,
					SPD.SEBARAN_DEC DIS_DEC,
					SPD.SEBARAN_TOTAL DIS_TOTAL,
					0 ADJ,
					0 OUTLOOK
				FROM TR_HO_SPD SPD
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = SPD.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = SPD.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = SPD.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(SPD.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND SPD.CC_CODE = '".$find_cc."'
					AND SPD.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
					AND COA.COA_CODE IN (SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'CAPEX%')
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(NVL(DIS_JAN, 0)) DIS_JAN,
				SUM(NVL(DIS_FEB, 0)) DIS_FEB,
				SUM(NVL(DIS_MAR, 0)) DIS_MAR,
				SUM(NVL(DIS_APR, 0)) DIS_APR,
				SUM(NVL(DIS_MAY, 0)) DIS_MAY,
				SUM(NVL(DIS_JUN, 0)) DIS_JUN,
				SUM(NVL(DIS_JUL, 0)) DIS_JUL,
				SUM(NVL(DIS_AUG, 0)) DIS_AUG,
				SUM(NVL(DIS_SEP, 0)) DIS_SEP,
				SUM(NVL(DIS_OCT, 0)) DIS_OCT,
				SUM(NVL(DIS_NOV, 0)) DIS_NOV,
				SUM(NVL(DIS_DEC, 0)) DIS_DEC,
				SUM(NVL(DIS_TOTAL, 0)) DIS_TOTAL,
				SUM(NVL(ADJ, 0)) ADJ,
				SUM(NVL(OUTLOOK, 0)) OUTLOOK,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (

				SELECT 
					PERIOD_BUDGET,
					(SELECT DISTINCT DIV_CODE FROM TM_HO_COST_CENTER THC LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = THC.HCC_DIVISI WHERE THC.HCC_CC = CC_CODE) DIV_CODE,
					(SELECT DISTINCT DIV_NAME FROM TM_HO_COST_CENTER THC LEFT JOIN TM_HO_DIVISION THD ON THD.DIV_CODE = THC.HCC_DIVISI WHERE THC.HCC_CC = CC_CODE) DIV_NAME,
					CC_CODE,
					CC_NAME,
					GROUP_01,
					COA_CODE,
					COA_DESC,
					RK_NAME,
					RK_DESCRIPTION,
					CORE_CODE,
					CORE_NAME,
					COMP_CODE,
                    (SELECT DISTINCT COMPANY_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND DELETE_USER IS NULL) COMP_NAME,
                    (SELECT DISTINCT BA_CODE FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND DELETE_USER IS NULL) BA_CODE,
                    (SELECT DISTINCT BA_NAME FROM TM_HO_COMPANY WHERE COMPANY_CODE = C_CODE AND CORE = CR_CODE AND DELETE_USER IS NULL) BA_NAME,
					DIS_JAN,
					DIS_FEB,
					DIS_MAR,
					DIS_APR,
					DIS_MAY,
					DIS_JUN,
					DIS_JUL,
					DIS_AUG,
					DIS_SEP,
					DIS_OCT,
					DIS_NOV,
					DIS_DEC,
					DIS_TOTAL,
					ADJ,
					OUTLOOK
				FROM (
					SELECT 
						ACT.PERIOD_BUDGET,
						HCC.HCC_CC CC_CODE,
						HCC.HCC_COST_CENTER CC_NAME,
						'OUTLOOK' GROUP_01,
						COA.COA_CODE,
						COA.COA_NAME COA_DESC,
						'OUTLOOK 2018' RK_NAME,
						ACT.TRANSACTION_DESC RK_DESCRIPTION,
						CORE.CORE_CODE,
						CORE.CORE_CODE AS CR_CODE,
						CORE.CORE_NAME,
	                    ACT.COMP_CODE,
	                    ACT.COMP_CODE AS C_CODE,
						ACT.ACT_JAN DIS_JAN,
						ACT.ACT_FEB DIS_FEB,
						ACT.ACT_MAR DIS_MAR,
						ACT.ACT_APR DIS_APR,
						ACT.ACT_MAY DIS_MAY,
						ACT.ACT_JUN DIS_JUN,
						ACT.ACT_JUL DIS_JUL,
						ACT.ACT_AUG DIS_AUG,
						ACT.OUTLOOK_SEP DIS_SEP,
						ACT.OUTLOOK_OCT DIS_OCT,
						ACT.OUTLOOK_NOV DIS_NOV,
						ACT.OUTLOOK_DEC DIS_DEC,
						0 DIS_TOTAL,
						0 ADJ,
						ACT.TOTAL_ACTUAL OUTLOOK
					FROM TM_HO_ACT_OUTLOOK ACT
					LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = ACT.CC_CODE
					LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = ACT.COA_CODE
					LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = ACT.CORE
					WHERE TO_CHAR(ACT.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
						AND ACT.CC_CODE = '".$find_cc."'
						AND ACT.DELETE_USER IS NULL
						AND COA.COA_CODE IN (SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE 'CAPEX%')
				)
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function tmpHoSpd($params = array()) {
		$period_budget = $find_div = $find_cc = '';

		if ($params['budgetperiod'] != '') $period_budget = $params['budgetperiod'];
		if ($params['key_find_div'] != '') $find_div = $params['key_find_div'];
		if ($params['key_find_cc'] != '') $find_cc = $params['key_find_cc'];

		/*$query = "
			INSERT INTO TMP_HO_SPD (
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				DIS_JAN,
				DIS_FEB,
				DIS_MAR,
				DIS_APR,
				DIS_MAY,
				DIS_JUN,
				DIS_JUL,
				DIS_AUG,
				DIS_SEP,
				DIS_OCT,
				DIS_NOV,
				DIS_DEC,
				DIS_TOTAL,
				SATUAN,
				QTY_JAN,
				QTY_FEB,
				QTY_MAR,
				QTY_APR,
				QTY_MAY,
				QTY_JUN,
				QTY_JUL,
				QTY_AUG,
				QTY_SEP,
				QTY_OCT,
				QTY_NOV,
				QTY_DEC,
				QTY_TOTAL,
				INSERT_USER,
				INSERT_TIME
			)
			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					SPD.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					SPD.SEBARAN_JAN DIS_JAN,
					SPD.SEBARAN_FEB DIS_FEB,
					SPD.SEBARAN_MAR DIS_MAR,
					SPD.SEBARAN_APR DIS_APR,
					SPD.SEBARAN_MAY DIS_MAY,
					SPD.SEBARAN_JUN DIS_JUN,
					SPD.SEBARAN_JUL DIS_JUL,
					SPD.SEBARAN_AUG DIS_AUG,
					SPD.SEBARAN_SEP DIS_SEP,
					SPD.SEBARAN_OCT DIS_OCT,
					SPD.SEBARAN_NOV DIS_NOV,
					SPD.SEBARAN_DEC DIS_DEC,
					SPD.SEBARAN_TOTAL DIS_TOTAL,
					CASE WHEN SPD.PLAN = '1' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JAN,
					CASE WHEN SPD.PLAN = '2' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_FEB,
					CASE WHEN SPD.PLAN = '3' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_MAR,
					CASE WHEN SPD.PLAN = '4' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_APR,
					CASE WHEN SPD.PLAN = '5' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_MAY,
					CASE WHEN SPD.PLAN = '6' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JUN,
					CASE WHEN SPD.PLAN = '7' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JUL,
					CASE WHEN SPD.PLAN = '8' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_AUG,
					CASE WHEN SPD.PLAN = '9' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_SEP,
					CASE WHEN SPD.PLAN = '10' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_OCT,
					CASE WHEN SPD.PLAN = '11' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_NOV,
					CASE WHEN SPD.PLAN = '12' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_DEC
				FROM TR_HO_SPD SPD 
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = SPD.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = SPD.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.COMPANY_CODE = SPD.COMP_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(SPD.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND SPD.CC_CODE = '".$find_cc."'
					AND SPD.DELETE_USER IS NULL
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT
					ACT.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'OUTLOOK' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					'OULOOK' RK_NAME,
					'' RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					ACT.ACT_JAN DIS_JAN,
					ACT.ACT_FEB DIS_FEB,
					ACT.ACT_MAR DIS_MAR,
					ACT.ACT_APR DIS_APR,
					ACT.ACT_MAY DIS_MAY,
					ACT.ACT_JUN DIS_JUN,
					ACT.ACT_JUL DIS_JUL,
					ACT.ACT_AUG DIS_AUG,
					ACT.OUTLOOK_SEP DIS_SEP,
					ACT.OUTLOOK_OCT DIS_OCT,
					ACT.OUTLOOK_NOV DIS_NOV,
					ACT.OUTLOOK_DEC DIS_DEC,
					ACT.TOTAL_ACTUAL DIS_TOTAL,
					NULL QTY_JAN,
					NULL QTY_FEB,
					NULL QTY_MAR,
					NULL QTY_APR,
					NULL QTY_MAY,
					NULL QTY_JUN,
					NULL QTY_JUL,
					NULL QTY_AUG,
					NULL QTY_SEP,
					NULL QTY_OCT,
					NULL QTY_NOV,
					NULL QTY_DEC
				FROM TM_HO_ACT_OUTLOOK ACT
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = ACT.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = ACT.COA_CODE
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = ACT.CORE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.COMPANY_CODE = ACT.COMP_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(ACT.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND ACT.CC_CODE = '".$find_cc."'
					AND ACT.DELETE_USER IS NULL
					AND ACT.COA_CODE LIKE '62%'
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT
					CAPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'CAPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					CAPEX.CAPEX_JAN DIS_JAN,
					CAPEX.CAPEX_FEB DIS_FEB,
					CAPEX.CAPEX_MAR DIS_MAR,
					CAPEX.CAPEX_APR DIS_APR,
					CAPEX.CAPEX_MAY DIS_MAY,
					CAPEX.CAPEX_JUN DIS_JUN,
					CAPEX.CAPEX_JUL DIS_JUL,
					CAPEX.CAPEX_AUG DIS_AUG,
					CAPEX.CAPEX_SEP DIS_SEP,
					CAPEX.CAPEX_OCT DIS_OCT,
					CAPEX.CAPEX_NOV DIS_NOV,
					CAPEX.CAPEX_DEC DIS_DEC,
					CAPEX.CAPEX_TOTAL DIS_TOTAL,
					NULL QTY_JAN,
					NULL QTY_FEB,
					NULL QTY_MAR,
					NULL QTY_APR,
					NULL QTY_MAY,
					NULL QTY_JUN,
					NULL QTY_JUL,
					NULL QTY_AUG,
					NULL QTY_SEP,
					NULL QTY_OCT,
					NULL QTY_NOV,
					NULL QTY_DEC
				FROM TM_HO_CAPEX CAPEX
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = CAPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = CAPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = CAPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = CAPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.COMPANY_CODE = CAPEX.COMP_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(CAPEX.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND CAPEX.CC_CODE = '".$find_cc."'
					AND CAPEX.DELETE_USER IS NULL
					AND CAPEX.COA_CODE LIKE '62%'
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT
					OPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'OPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					OPEX.OPEX_JAN DIS_JAN,
					OPEX.OPEX_FEB DIS_FEB,
					OPEX.OPEX_MAR DIS_MAR,
					OPEX.OPEX_APR DIS_APR,
					OPEX.OPEX_MAY DIS_MAY,
					OPEX.OPEX_JUN DIS_JUN,
					OPEX.OPEX_JUL DIS_JUL,
					OPEX.OPEX_AUG DIS_AUG,
					OPEX.OPEX_SEP DIS_SEP,
					OPEX.OPEX_OCT DIS_OCT,
					OPEX.OPEX_NOV DIS_NOV,
					OPEX.OPEX_DEC DIS_DEC,
					OPEX.OPEX_TOTAL DIS_TOTAL,
					NULL QTY_JAN,
					NULL QTY_FEB,
					NULL QTY_MAR,
					NULL QTY_APR,
					NULL QTY_MAY,
					NULL QTY_JUN,
					NULL QTY_JUL,
					NULL QTY_AUG,
					NULL QTY_SEP,
					NULL QTY_OCT,
					NULL QTY_NOV,
					NULL QTY_DEC
				FROM TM_HO_OPEX OPEX
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = OPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = OPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = OPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = OPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.COMPANY_CODE = OPEX.COMP_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(OPEX.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND OPEX.CC_CODE = '".$find_cc."'
					AND OPEX.DELETE_USER IS NULL
					AND OPEX.COA_CODE LIKE '62%'
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME
		";*/

		$query = "
			INSERT INTO TMP_HO_SPD (
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				DIS_JAN,
				DIS_FEB,
				DIS_MAR,
				DIS_APR,
				DIS_MAY,
				DIS_JUN,
				DIS_JUL,
				DIS_AUG,
				DIS_SEP,
				DIS_OCT,
				DIS_NOV,
				DIS_DEC,
				DIS_TOTAL,
				SATUAN,
				QTY_JAN,
				QTY_FEB,
				QTY_MAR,
				QTY_APR,
				QTY_MAY,
				QTY_JUN,
				QTY_JUL,
				QTY_AUG,
				QTY_SEP,
				QTY_OCT,
				QTY_NOV,
				QTY_DEC,
				QTY_TOTAL,
				INSERT_USER,
				INSERT_TIME
			)
			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT 
					SPD.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'SPD' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					SPD.SEBARAN_JAN DIS_JAN,
					SPD.SEBARAN_FEB DIS_FEB,
					SPD.SEBARAN_MAR DIS_MAR,
					SPD.SEBARAN_APR DIS_APR,
					SPD.SEBARAN_MAY DIS_MAY,
					SPD.SEBARAN_JUN DIS_JUN,
					SPD.SEBARAN_JUL DIS_JUL,
					SPD.SEBARAN_AUG DIS_AUG,
					SPD.SEBARAN_SEP DIS_SEP,
					SPD.SEBARAN_OCT DIS_OCT,
					SPD.SEBARAN_NOV DIS_NOV,
					SPD.SEBARAN_DEC DIS_DEC,
					SPD.SEBARAN_TOTAL DIS_TOTAL,
					CASE WHEN SPD.PLAN = '1' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JAN,
					CASE WHEN SPD.PLAN = '2' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_FEB,
					CASE WHEN SPD.PLAN = '3' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_MAR,
					CASE WHEN SPD.PLAN = '4' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_APR,
					CASE WHEN SPD.PLAN = '5' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_MAY,
					CASE WHEN SPD.PLAN = '6' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JUN,
					CASE WHEN SPD.PLAN = '7' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_JUL,
					CASE WHEN SPD.PLAN = '8' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_AUG,
					CASE WHEN SPD.PLAN = '9' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_SEP,
					CASE WHEN SPD.PLAN = '10' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_OCT,
					CASE WHEN SPD.PLAN = '11' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_NOV,
					CASE WHEN SPD.PLAN = '12' THEN NVL(SPD.JLH_PRIA, 0) + NVL(SPD.JLH_WANITA, 0) END AS QTY_DEC
				FROM TR_HO_SPD SPD 
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = SPD.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = SPD.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = SPD.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = SPD.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = SPD.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(SPD.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND SPD.CC_CODE = '".$find_cc."'
					AND SPD.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME

			UNION ALL

			SELECT 
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME,
				SUM(DIS_JAN) AS DIS_JAN,
				SUM(DIS_FEB) AS DIS_FEB,
				SUM(DIS_MAR) AS DIS_MAR,
				SUM(DIS_APR) AS DIS_APR,
				SUM(DIS_MAY) AS DIS_MAY,
				SUM(DIS_JUN) AS DIS_JUN,
				SUM(DIS_JUL) AS DIS_JUL,
				SUM(DIS_AUG) AS DIS_AUG,
				SUM(DIS_SEP) AS DIS_SEP,
				SUM(DIS_OCT) AS DIS_OCT,
				SUM(DIS_NOV) AS DIS_NOV,
				SUM(DIS_DEC) AS DIS_DEC,
				SUM(DIS_TOTAL) AS DIS_TOTAL,
				'ORG' SATUAN,
				SUM(QTY_JAN) AS QTY_JAN,
				SUM(QTY_FEB) AS QTY_FEB,
				SUM(QTY_MAR) AS QTY_MAR,
				SUM(QTY_APR) AS QTY_APR,
				SUM(QTY_MAY) AS QTY_MAY,
				SUM(QTY_JUN) AS QTY_JUN,
				SUM(QTY_JUL) AS QTY_JUL,
				SUM(QTY_AUG) AS QTY_AUG,
				SUM(QTY_SEP) AS QTY_SEP,
				SUM(QTY_OCT) AS QTY_OCT,
				SUM(QTY_NOV) AS QTY_NOV,
				SUM(QTY_DEC) AS QTY_DEC,
				SUM(
					NVL(QTY_JAN, 0) + NVL(QTY_FEB, 0) + NVL(QTY_MAR, 0) + NVL(QTY_APR, 0) + NVL(QTY_MAY, 0) + NVL(QTY_JUN, 0) + 
					NVL(QTY_JUL, 0) + NVL(QTY_AUG, 0) + NVL(QTY_SEP, 0) + NVL(QTY_OCT, 0) + NVL(QTY_NOV, 0) + NVL(QTY_DEC, 0)
				) AS QTY_TOTAL,
				'".$this->_userName."' INSERT_USER,
				SYSDATE INSERT_TIME
			FROM (
				SELECT
					OPEX.PERIOD_BUDGET,
					DIV.DIV_CODE,
					DIV.DIV_NAME,
					HCC.HCC_CC CC_CODE,
					HCC.HCC_COST_CENTER CC_NAME,
					'OPEX' GROUP_01,
					COA.COA_CODE,
					COA.COA_NAME COA_DESC,
					RK.RK_NAME,
					RK.RK_DESCRIPTION,
					CORE.CORE_CODE,
					CORE.CORE_NAME,
					COMP.COMPANY_CODE COMP_CODE,
					COMP.COMPANY_NAME COMP_NAME,
					COMP.BA_CODE,
					COMP.BA_NAME,
					OPEX.OPEX_JAN DIS_JAN,
					OPEX.OPEX_FEB DIS_FEB,
					OPEX.OPEX_MAR DIS_MAR,
					OPEX.OPEX_APR DIS_APR,
					OPEX.OPEX_MAY DIS_MAY,
					OPEX.OPEX_JUN DIS_JUN,
					OPEX.OPEX_JUL DIS_JUL,
					OPEX.OPEX_AUG DIS_AUG,
					OPEX.OPEX_SEP DIS_SEP,
					OPEX.OPEX_OCT DIS_OCT,
					OPEX.OPEX_NOV DIS_NOV,
					OPEX.OPEX_DEC DIS_DEC,
					OPEX.OPEX_TOTAL DIS_TOTAL,
					NULL QTY_JAN,
					NULL QTY_FEB,
					NULL QTY_MAR,
					NULL QTY_APR,
					NULL QTY_MAY,
					NULL QTY_JUN,
					NULL QTY_JUL,
					NULL QTY_AUG,
					NULL QTY_SEP,
					NULL QTY_OCT,
					NULL QTY_NOV,
					NULL QTY_DEC
				FROM TM_HO_OPEX OPEX
				LEFT JOIN TM_HO_COST_CENTER HCC ON HCC.HCC_CC = OPEX.CC_CODE
				LEFT JOIN TM_HO_COA COA ON COA.COA_CODE = OPEX.COA_CODE
				LEFT JOIN TM_HO_RENCANA_KERJA RK ON RK.ID = OPEX.RK_ID
				LEFT JOIN TM_HO_CORE CORE ON CORE.CORE_CODE = OPEX.CORE_CODE
				LEFT JOIN TM_HO_COMPANY COMP ON COMP.BA_CODE = OPEX.BA_CODE
				LEFT JOIN TM_HO_DIVISION DIV ON DIV.DIV_CODE = HCC.HCC_DIVISI
				WHERE TO_CHAR(OPEX.PERIOD_BUDGET, 'YYYY') = '".$period_budget."' 
					AND OPEX.CC_CODE = '".$find_cc."'
					AND OPEX.DELETE_USER IS NULL
					AND DIV.DELETE_USER IS NULL
                    AND COA.COA_CODE IN (SELECT COA_CODE FROM TM_HO_COA WHERE COA_GROUP LIKE '%SPD')
			) GROUP BY
				PERIOD_BUDGET,
				DIV_CODE,
				DIV_NAME,
				CC_CODE,
				CC_NAME,
				GROUP_01,
				COA_CODE,
				COA_DESC,
				RK_NAME,
				RK_DESCRIPTION,
				CORE_CODE,
				CORE_NAME,
				COMP_CODE,
				COMP_NAME,
				BA_CODE,
				BA_NAME
		";

		$this->_db->query($query);
		if ($this->_db->commit()) {
			return true;
		} else {
			return false;
		}
	}

	public function ReportHoSummaryBudget($params = array()) {
		if($params['budgetperiod'] != '') {
			$where .= " AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
			$result['PERIOD'] = $params['budgetperiod'];
		} else {
			$where .= " AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
			$result['PERIOD'] = $this->_period;
		}

		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."' ";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."' ";

		$query = "
			SELECT * FROM TMP_HO_SUM_BUDGET 
			WHERE 1 = 1
			$where
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		//echo $sql;
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");

		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}

	public function ReportHoBudget($params = array()) {
		if($params['budgetperiod'] != '') {
			$where .= " AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
			$result['PERIOD'] = $params['budgetperiod'];
		} else {
			$where .= " AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
			$result['PERIOD'] = $this->_period;
		}

		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."' ";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."' ";

		$query = "
			SELECT * FROM TMP_HO_BUDGET 
			WHERE 1 = 1
			$where
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		//echo $sql;
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");

		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}

	public function ReportHoOpex($params = array()) {
		if($params['budgetperiod'] != '') {
			$where .= " AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
			$result['PERIOD'] = $params['budgetperiod'];
		} else {
			$where .= " AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
			$result['PERIOD'] = $this->_period;
		}

		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."' ";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."' ";

		$query = "
			SELECT * FROM TMP_HO_OPEX 
			WHERE 1 = 1
			$where
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		//echo $sql;
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");

		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}

	public function ReportHoCapex($params = array()) {
		if($params['budgetperiod'] != '') {
			$where .= " AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
			$result['PERIOD'] = $params['budgetperiod'];
		} else {
			$where .= " AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
			$result['PERIOD'] = $this->_period;
		}

		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."' ";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."' ";

		$query = "
			SELECT * FROM TMP_HO_CAPEX 
			WHERE 1 = 1
			$where
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		//echo $sql;
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");

		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}

	public function ReportHoSpd($params = array()) {
		if($params['budgetperiod'] != '') {
			$where .= " AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' ";
			$result['PERIOD'] = $params['budgetperiod'];
		} else {
			$where .= " AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' ";
			$result['PERIOD'] = $this->_period;
		}

		if ($params['key_find_div'] != '') $where .= " AND DIV_CODE = '".$params['key_find_div']."' ";
		if ($params['key_find_cc'] != '') $where .= " AND CC_CODE = '".$params['key_find_cc']."' ";

		$query = "
			SELECT * FROM TMP_HO_SPD 
			WHERE 1 = 1
			$where
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		//echo $sql;
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");

		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}

	public function ReportHoProfitLoss() {
		
	}

	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
	//inisialisasi list yang akan ditampilkan
	public function initList($params = array()) {
		$result = array();
		$initAction = str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));

		$result = $this->$initAction($params);

		return $result;
	}
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}

