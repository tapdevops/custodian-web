<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Summary Report
Function 			:	- getInput							: YIR 20/06/2014	: setting input untuk region
						- tmpRptDevCost						: SID 04/08/2014	: generate temp table untuk dev cost
						- delTmpRptDevCost					: SID 04/08/2014	: hapus temp table untuk dev cost
						- reportDevelopmentCost				: SID 04/08/2014	: generate report development cost
						- reportSummaryDevelopmentCost		: SID 04/08/2014	: generate report summary development cost
						- tmpRptEstCost						: SID 05/08/2014	: generate temp table untuk estate cost
						- delTmpRptEstCost					: SID 05/08/2014	: hapus temp table untuk estate cost
						- reportEstateCost					: SID 05/08/2014	: generate report estate cost
						- reportSummaryEstateCost			: SID 05/08/2014	: generate report summary estate cost
						- reportCapex						: SID 05/08/2014	: generate report CAPEX
						- reportSebaranHa					: SID 23/08/2013	: generate report sebaran HA
						- reportVraUtilisasi				: SID 06/08/2014	: generate report vra utilisasi per BA
						- reportVraUtilisasiRegion			: SID 22/08/2014	: generate report vra utilisasi per region
						- getLastGenerate					: SID 12/08/2014	: get last generate date
						- querySummaryDevelopmentCostPerBa	: SID 25/08/2014	: query summary development cost per BA
						- querySummaryDevelopmentCostPerAfd	: SID 28/08/2014	: query summary development cost per AFD
						- querySummaryEstateCostPerBa		: SID 25/08/2014	: query summary estate cost per BA
						- querySummaryEstateCostPerAfd		: SID 28/08/2014	: query summary estate cost per AFD
						- modReviewDevelopmentCostPerBa		: SID 25/08/2014	: generate module review development cost per BA
						- modReviewDevelopmentCostPerAfd	: SID 28/08/2014	: generate module review development cost per AFD
						- modReviewEstateCostPerBa			: SID 25/08/2014	: generate module review estate cost per BA
						- modReviewEstateCostPerAfd			: SID 28/08/2014	: generate module review estate cost per AFD
						- modReviewProduksiPerAfd			: SID 28/08/2014	: generate module review produksi per AFD
						- modReviewProduksiPerBa			: SID 28/08/2014	: generate module review produksi per BA
						- modReviewProduksiPerRegion		: SID 28/08/2014	: generate module review produksi per region
						- reportHkDevelopmentCost			: YUS 09/09/2014	: generate report HK development cost
						- reportHkEstateCost				: YUS 10/09/2014	: generate report HK estate cost
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	23/08/2013
Update Terakhir		:	05/08/2014
Revisi				:	
	NBU	18/05/2015	: - Ubah perhitungan sebaran di estate cost (Line 6054)
=========================================================================================================================
*/
class Application_Model_ReportKoneksitas
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
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//setting input untuk region dan maturity stage
	public function getInput()
    {
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
	
	//generate temp table untuk dev cost
	public function tmpRptDevCost($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		return true;
	}
	
	//hapus temp table untuk dev cost
	public function delTmpRptDevCost($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		//hapus dev cost per BA
		$query = "
			DELETE FROM TMP_RPT_DEV_COST 
			WHERE 1 = 1
			$where 
		";
		$this->_db->query($query);
		$this->_db->commit();
		
		//hapus dev cost per afd
		$query = "
			DELETE FROM TMP_RPT_DEV_COST_AFD 
			WHERE 1 = 1
			$where 
		";
		$this->_db->query($query);
		$this->_db->commit();
		
		return true;
	}
	
	//generate report produksi
    public function reportProduksi($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		/* ################################################### generate excel development cost ################################################### */
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                to_char(TRPPB.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(TRPPB.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		/*//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }*/
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND TRPPB.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			select TRPPB.period_budget, TRPPB.BA_CODE, sum(ton_budget) as ton_p_prod, sum(janjang_budget) Janjang_p_prod, sum(jan) + sum(feb) + sum(mar) + sum(apr) + sum(may) + sum(jun) + 
                    sum(jul) + sum(aug) + sum(sep) + sum(oct) + sum(nov) + sum(dec) as total_sebaran_ha, sum(TON) as TON_PANEN, sum(JANJANG) as JANJANG_PANEN, (sum(ton_budget) - sum(TON)) as selisih_ton,
                    (sum(janjang_budget) - sum(JANJANG)) as selisih_janjang, sum(ha_sms2) total_ha_p_prod, VRSP_HA.QTY_SETAHUN ttl_ha_p_sebaran, (sum(ha_sms2) - VRSP_HA.QTY_SETAHUN) as selisih_ha_panen,
                    round((VRSP_TON.QTY_JAN / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_jan, round((VRSP_TON.QTY_FEB / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_feb, 
                    round((VRSP_TON.QTY_MAR / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_mar, round((VRSP_TON.QTY_APR / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_apr, 
                    round((VRSP_TON.QTY_MAY / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_may, round((VRSP_TON.QTY_JUN / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_jun, 
                    round((VRSP_TON.QTY_JUL / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_jul, round((VRSP_TON.QTY_AUG / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_aug, 
                    round((VRSP_TON.QTY_SEP / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_sep, round((VRSP_TON.QTY_OCT / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_oct, 
                    round((VRSP_TON.QTY_NOV / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_nov, round((VRSP_TON.QTY_DEC / VRSP_TON.QTY_SETAHUN) * 100) persen_p_prod_dec,
                    round((sum(cost_jan) / sum(cost_setahun) * 100)) persen_biaya_jan, round((sum(cost_feb) / sum(cost_setahun) * 100)) persen_biaya_feb, round((sum(cost_mar) / sum(cost_setahun) * 100)) persen_biaya_mar,
                    round((sum(cost_apr) / sum(cost_setahun) * 100)) persen_biaya_apr, round((sum(cost_may) / sum(cost_setahun) * 100)) persen_biaya_may, round((sum(cost_jun) / sum(cost_setahun) * 100)) persen_biaya_jun,
                    round((sum(cost_jul) / sum(cost_setahun) * 100)) persen_biaya_jul, round((sum(cost_aug) / sum(cost_setahun) * 100)) persen_biaya_aug, round((sum(cost_sep) / sum(cost_setahun) * 100)) persen_biaya_sep, 
                    round((sum(cost_oct) / sum(cost_setahun) * 100)) persen_biaya_oct, round((sum(cost_nov) / sum(cost_setahun) * 100)) persen_biaya_nov, round((sum(cost_dec) / sum(cost_setahun) * 100)) persen_biaya_dec,
                    (round((VRSP_TON.QTY_JAN / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_jan) / sum(cost_setahun) * 100))) selisih_jan, 
                    (round((VRSP_TON.QTY_FEB / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_feb) / sum(cost_setahun) * 100))) selisih_feb, 
                    (round((VRSP_TON.QTY_MAR / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_mar) / sum(cost_setahun) * 100))) selisih_mar, 
                    (round((VRSP_TON.QTY_APR / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_apr) / sum(cost_setahun) * 100))) selisih_apr, 
                    (round((VRSP_TON.QTY_MAY / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_may) / sum(cost_setahun) * 100))) selisih_may, 
                    (round((VRSP_TON.QTY_JUN / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_jun) / sum(cost_setahun) * 100))) selisih_jun, 
                    (round((VRSP_TON.QTY_JUL / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_jul) / sum(cost_setahun) * 100))) selisih_jul, 
                    (round((VRSP_TON.QTY_AUG / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_aug) / sum(cost_setahun) * 100))) selisih_aug, 
                    (round((VRSP_TON.QTY_SEP / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_sep) / sum(cost_setahun) * 100))) selisih_sep, 
                    (round((VRSP_TON.QTY_OCT / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_oct) / sum(cost_setahun) * 100))) selisih_oct, 
                    (round((VRSP_TON.QTY_NOV / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_nov) / sum(cost_setahun) * 100))) selisih_nov, 
                    (round((VRSP_TON.QTY_DEC / VRSP_TON.QTY_SETAHUN) * 100) - round((sum(cost_dec) / sum(cost_setahun) * 100))) selisih_dec,
					count(TRPPB.block_code) jml_blck_p_prod,
                    SUM(CASE WHEN TON is NOT null THEN 1 ELSE 0 END) AS jml_blck_panen,
                    (count(TRPPB.block_code) - SUM(CASE WHEN TON is NOT null THEN 1 ELSE 0 END)) selisih_jml_blck
					 from TR_PRODUKSI_PERIODE_BUDGET TRPPB
                     left join TR_RKT_PANEN TRP on TRP.period_budget = TRPPB.period_budget and TRP.BA_CODE = TRPPB.BA_CODE and TRP.AFD_CODE = TRPPB.AFD_CODE and TRP.BLOCK_CODE = TRPPB.BLOCK_CODE
                     left join V_REPORT_SEBARAN_PRODUKSI VRSP_HA on VRSP_HA.period_budget = TRPPB.period_budget and VRSP_HA.BA_CODE = TRPPB.BA_CODE and VRSP_HA.TIPE_TRANSAKSI = '02_HA_PANEN'
                     left join V_REPORT_SEBARAN_PRODUKSI VRSP_TON on VRSP_TON.period_budget = TRPPB.period_budget and VRSP_TON.BA_CODE = TRPPB.BA_CODE and VRSP_TON.TIPE_TRANSAKSI = '03_TBS_PANEN'
                      where $where
                    group by TRPPB.period_budget, TRPPB.BA_CODE, VRSP_HA.QTY_SETAHUN, VRSP_TON.QTY_JAN,
                    VRSP_TON.QTY_FEB, VRSP_TON.QTY_MAR, VRSP_TON.QTY_APR, VRSP_TON.QTY_MAY, VRSP_TON.QTY_JUN, VRSP_TON.QTY_JUL, VRSP_TON.QTY_AUG, VRSP_TON.QTY_SEP,
                    VRSP_TON.QTY_OCT, VRSP_TON.QTY_NOV, VRSP_TON.QTY_DEC, VRSP_TON.QTY_SETAHUN
		";
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}		
		/* ################################################### generate excel koneksitas produksi ################################################### */
		
		return $result;
	}
	
	//generate report hs
    public function reportHs($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		/* ################################################### generate excel development cost ################################################### */
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                to_char(THS.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where1 .= "
                to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(THS.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where1 .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		/*//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }*/
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND THS.BA_CODE = '".$params['key_find']."'
            ";
			$where1 .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT PERIOD_BUDGET, BA_CODE,
					 ALL_HA.ACTIVITY_CODE,
					 TA.DESCRIPTION,
					 SUM (TBM0_HS_HA) AS TBM0_HS_HA,
					 SUM (TBM1_HS_HA) AS TBM1_HS_HA,
					 SUM (TBM2_HS_HA) AS TBM2_HS_HA,
					 SUM (TBM3_HS_HA) AS TBM3_HS_HA,
					 SUM (TM_HS_HA) AS TM_HS_HA,
					 SUM (TOTAL_HS_HA) AS TOTAL_HS_HA,
					 SUM (TBM0_RKT_HA) AS TBM0_RKT_HA,
					 SUM (TBM1_RKT_HA) AS TBM1_RKT_HA,
					 SUM (TBM2_RKT_HA) AS TBM2_RKT_HA,
					 SUM (TBM3_RKT_HA) AS TBM3_RKT_HA,
					 SUM (TM_RKT_HA) AS TM_RKT_HA,
					 SUM (TOTAL_RKT_HA) AS TOTAL_RKT_HA,
					 SUM (SELISIH_TBM0_HA) AS SELISIH_TBM0_HA,
					 SUM (SELISIH_TBM1_HA) AS SELISIH_TBM1_HA,
					 SUM (SELISIH_TBM2_HA) AS SELISIH_TBM2_HA,
					 SUM (SELISIH_TBM3_HA) AS SELISIH_TBM3_HA,
					 SUM (SELISIH_TM_HA) AS SELISIH_TM_HA,
					 SUM (SELISIH_TOTAL_HA) AS SELISIH_TOTAL_HA,
					 SUM (TBM0_HS_BLCK) AS TBM0_HS_BLCK,
					 SUM (TBM1_HS_BLCK) TBM1_HS_BLCK,
					 SUM (TBM2_HS_BLCK) TBM2_HS_BLCK,
					 SUM (TBM3_HS_BLCK) TBM3_HS_BLCK,
					 SUM (TM_HS_BLCK) TM_HS_BLCK,
					 SUM (TOTAL_HS_BLCK) TOTAL_HS_BLCK,
					 SUM (TBM0_RKT_BLCK) TBM0_RKT_BLCK,
					 SUM (TBM1_RKT_BLCK) TBM1_RKT_BLCK,
					 SUM (TBM2_RKT_BLCK) TBM2_RKT_BLCK,
					 SUM (TBM3_RKT_BLCK) TBM3_RKT_BLCK,
					 SUM (TM_RKT_BLCK) TM_RKT_BLCK,
					 SUM (TOTAL_RKT_BLCK) TOTAL_RKT_BLCK,
					 SUM (SELISIH_TBM0_BLCK) SELISIH_TBM0_BLCK,
					 SUM (SELISIH_TBM1_BLCK) SELISIH_TBM1_BLCK,
					 SUM (SELISIH_TBM2_BLCK) SELISIH_TBM2_BLCK,
					 SUM (SELISIH_TBM3_BLCK) SELISIH_TBM3_BLCK,
					 SUM (SELISIH_TM_BLCK) SELISIH_TM_BLCK,
					 SUM (SELISIH_BLCK) SELISIH_BLCK,
					 SUM (MINERAL_HS) MINERAL_HS,
					 SUM (PASIR_HS) PASIR_HS,
					 SUM (GAMBUT_HS) GAMBUT_HS,
					 SUM (TOTAL_HS_LT) TOTAL_HS_LT,
					 SUM (MINERAL_RKT) MINERAL_RKT,
					 SUM (PASIR_RKT) PASIR_RKT,
					 SUM (GAMBUT_RKT) GAMBUT_RKT,
					 SUM (TOTAL_RKT_LT) TOTAL_RKT_LT,
					 SUM (SELISIH_MINERAL_HA) SELISIH_MINERAL_HA,
					 SUM (SELISIH_PASIR_HA) SELISIH_PASIR_HA,
					 SUM (SELISIH_GAMBUT_HA) SELISIH_GAMBUT_HA,
					 SUM (SELISIH_TOTAL_HA_LT) SELISIH_TOTAL_HA_LT,
					 SUM (MINERAL_HS_BLCK) MINERAL_HS_BLCK,
					 SUM (PASIR_HS_BLCK) PASIR_HS_BLCK,
					 SUM (GAMBUT_HS_BLCK) GAMBUT_HS_BLCK,
					 SUM (TOTAL_HS_LT_BLCK) TOTAL_HS_LT_BLCK,
					 SUM (MINERAL_RKT_BLCK) MINERAL_RKT_BLCK,
					 SUM (PASIR_RKT_BLCK) PASIR_RKT_BLCK,
					 SUM (GAMBUT_RKT_BLCK) GAMBUT_RKT_BLCK,
					 SUM (TOTAL_RKT_LT_BLCK) TOTAL_RKT_LT_BLCK,
					 SUM (SELISIH_MINERAL_BLCK) SELISIH_MINERAL_BLCK,
					 SUM (SELISIH_PASIR_BLCK) SELISIH_PASIR_BLCK,
					 SUM (SELISIH_GAMBUT_BLCK) SELISIH_GAMBUT_BLCK,
					 SUM (SELISIH_TOTAL_BLCK_LT) SELISIH_TOTAL_BLCK_LT,
					 SUM (DATAR_HS) DATAR_HS,
					 SUM (BUKIT_HS) BUKIT_HS,
					 SUM (TOTAL_HS_TOP) TOTAL_HS_TOP,
					 SUM (DATAR_RKT) DATAR_RKT,
					 SUM (BUKIT_RKT) BUKIT_RKT,
					 SUM (TOTAL_RKT_TOP) TOTAL_RKT_TOP,
					 SUM (SELISIH_DATAR_HA) SELISIH_DATAR_HA,
					 SUM (SELISIH_BUKIT_HA) SELISIH_BUKIT_HA,
					 SUM (SELISIH_TOTAL_HA_TOP) SELISIH_TOTAL_HA_TOP,
					 SUM (DATAR_HS_BLCK) DATAR_HS_BLCK,
					 SUM (BUKIT_HS_BLCK) BUKIT_HS_BLCK,
					 SUM (TOTAL_HS_TOP_BLCK) TOTAL_HS_TOP_BLCK,
					 SUM (DATAR_RKT_BLCK) DATAR_RKT_BLCK,
					 SUM (BUKIT_RKT_BLCK) BUKIT_RKT_BLCK,
					 SUM (TOTAL_RKT_TOP_BLCK) TOTAL_RKT_TOP_BLCK,
					 SUM (SELISIH_DATAR_BLCK) SELISIH_DATAR_BLCK,
					 SUM (SELISIH_BUKIT_BLCK) SELISIH_BUKIT_BLCK,
					 SUM (SELISIH_TOTAL_BLCK_TOP) SELISIH_TOTAL_BLCK_TOP
				FROM    (  SELECT THS.PERIOD_BUDGET, THS.BA_CODE,
								  TRKT.ACTIVITY_CODE,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0'
										 THEN
											HA_PLANTED
										 ELSE
											0
									  END)
									 AS TBM0_HS_HA,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1'
										 THEN
											HA_PLANTED
										 ELSE
											0
									  END)
									 AS TBM1_HS_HA,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2'
										 THEN
											HA_PLANTED
										 ELSE
											0
									  END)
									 AS TBM2_HS_HA,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3'
										 THEN
											HA_PLANTED
										 ELSE
											0
									  END)
									 AS TBM3_HS_HA,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS TM_HS_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0'
										  THEN
											 HA_PLANTED
										  ELSE
											 0
									   END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1'
											THEN
											   HA_PLANTED
											ELSE
											   0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2'
											THEN
											   HA_PLANTED
											ELSE
											   0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3'
											THEN
											   HA_PLANTED
											ELSE
											   0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TM'
											THEN
											   HA_PLANTED
											ELSE
											   0
										 END))
									 AS TOTAL_HS_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN AVG_HA
										 ELSE 0
									  END)
									 TBM0_RKT_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN AVG_HA
										 ELSE 0
									  END)
									 TBM1_RKT_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN AVG_HA
										 ELSE 0
									  END)
									 TBM2_RKT_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN AVG_HA
										 ELSE 0
									  END)
									 TBM3_RKT_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN AVG_HA
										 ELSE 0
									  END)
									 TM_RKT_HA,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN AVG_HA
										 ELSE 0
									  END)
								  + SUM(CASE
										   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN AVG_HA
										   ELSE 0
										END)
								  + SUM(CASE
										   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN AVG_HA
										   ELSE 0
										END)
								  + SUM(CASE
										   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN AVG_HA
										   ELSE 0
										END)
								  + SUM(CASE
										   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN AVG_HA
										   ELSE 0
										END)
									 TOTAL_RKT_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0'
										  THEN
											 HA_PLANTED
										  ELSE
											 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0'
											THEN
											   AVG_HA
											ELSE
											   0
										 END))
									 AS SELISIH_TBM0_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1'
										  THEN
											 HA_PLANTED
										  ELSE
											 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1'
											THEN
											   AVG_HA
											ELSE
											   0
										 END))
									 AS SELISIH_TBM1_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2'
										  THEN
											 HA_PLANTED
										  ELSE
											 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2'
											THEN
											   AVG_HA
											ELSE
											   0
										 END))
									 AS SELISIH_TBM2_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3'
										  THEN
											 HA_PLANTED
										  ELSE
											 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3'
											THEN
											   AVG_HA
											ELSE
											   0
										 END))
									 AS SELISIH_TBM3_HA,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN AVG_HA
											ELSE 0
										 END))
									 SELISIH_TM_HA,
								  ( (SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0'
											THEN
											   HA_PLANTED
											ELSE
											   0
										 END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1'
											  THEN
												 HA_PLANTED
											  ELSE
												 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2'
											  THEN
												 HA_PLANTED
											  ELSE
												 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3'
											  THEN
												 HA_PLANTED
											  ELSE
												 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TM'
											  THEN
												 HA_PLANTED
											  ELSE
												 0
										   END))
								   - (SUM(CASE
											 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0'
											 THEN
												AVG_HA
											 ELSE
												0
										  END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1'
											   THEN
												  AVG_HA
											   ELSE
												  0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2'
											   THEN
												  AVG_HA
											   ELSE
												  0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3'
											   THEN
												  AVG_HA
											   ELSE
												  0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM'
											   THEN
												  AVG_HA
											   ELSE
												  0
											END)))
									 AS SELISIH_TOTAL_HA,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
										 ELSE 0
									  END)
									 AS TBM0_HS_BLCK,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
										 ELSE 0
									  END)
									 AS TBM1_HS_BLCK,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
										 ELSE 0
									  END)
									 AS TBM2_HS_BLCK,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
										 ELSE 0
									  END)
									 AS TBM3_HS_BLCK,
								  SUM(CASE
										 WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN 1
										 ELSE 0
									  END)
									 AS TM_HS_BLCK,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN 1
											ELSE 0
										 END))
									 AS TOTAL_HS_BLCK,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
										 ELSE 0
									  END)
									 AS TBM0_RKT_BLCK,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
										 ELSE 0
									  END)
									 AS TBM1_RKT_BLCK,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
										 ELSE 0
									  END)
									 AS TBM2_RKT_BLCK,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
										 ELSE 0
									  END)
									 AS TBM3_RKT_BLCK,
								  SUM(CASE
										 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN 1
										 ELSE 0
									  END)
									 AS TM_RKT_BLCK,
								  (SUM(CASE
										  WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN 1
											ELSE 0
										 END))
									 AS TOTAL_RKT_BLCK,
									 (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
											ELSE 0
										 END))
									 SELISIH_TBM0_BLCK,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
											ELSE 0
										 END))
									 SELISIH_TBM1_BLCK,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
											ELSE 0
										 END))
									 SELISIH_TBM2_BLCK,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
											ELSE 0
										 END))
									 SELISIH_TBM3_BLCK,
								  (SUM(CASE
										  WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN 1
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN 1
											ELSE 0
										 END))
									 SELISIH_TM_BLCK,
								  ( (SUM(CASE
											WHEN THS.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
											ELSE 0
										 END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
											  ELSE 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
											  ELSE 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
											  ELSE 0
										   END)
									 + SUM(CASE
											  WHEN THS.MATURITY_STAGE_SMS2 = 'TM' THEN 1
											  ELSE 0
										   END))
								   - (SUM(CASE
											 WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM0' THEN 1
											 ELSE 0
										  END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM1' THEN 1
											   ELSE 0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM2' THEN 1
											   ELSE 0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TBM3' THEN 1
											   ELSE 0
											END)
									  + SUM(CASE
											   WHEN TRKT.MATURITY_STAGE_SMS2 = 'TM' THEN 1
											   ELSE 0
											END)))
									 AS SELISIH_BLCK,
								  0 AS MINERAL_HS,
								  0 AS PASIR_HS,
								  0 AS GAMBUT_HS,
								  0 AS TOTAL_HS_LT,
								  0 AS MINERAL_RKT,
								  0 AS PASIR_RKT,
								  0 AS GAMBUT_RKT,
								  0 AS TOTAL_RKT_LT,
								  0 AS SELISIH_MINERAL_HA,
								  0 AS SELISIH_PASIR_HA,
								  0 AS SELISIH_GAMBUT_HA,
								  0 AS SELISIH_TOTAL_HA_LT,
								  0 AS MINERAL_HS_BLCK,
								  0 AS PASIR_HS_BLCK,
								  0 AS GAMBUT_HS_BLCK,
								  0 AS TOTAL_HS_LT_BLCK,
								  0 AS MINERAL_RKT_BLCK,
								  0 AS PASIR_RKT_BLCK,
								  0 AS GAMBUT_RKT_BLCK,
								  0 AS TOTAL_RKT_LT_BLCK,
								  0 AS SELISIH_MINERAL_BLCK,
								  0 AS SELISIH_PASIR_BLCK,
								  0 AS SELISIH_GAMBUT_BLCK,
								  0 AS SELISIH_TOTAL_BLCK_LT,
								  0 AS DATAR_HS,
								  0 AS BUKIT_HS,
								  0 AS TOTAL_HS_TOP,
								  0 AS DATAR_RKT,
								  0 AS BUKIT_RKT,
								  0 AS TOTAL_RKT_TOP,
								  0 AS SELISIH_DATAR_HA,
								  0 AS SELISIH_BUKIT_HA,
								  0 AS SELISIH_TOTAL_HA_TOP,
								  0 AS DATAR_HS_BLCK,
								  0 AS BUKIT_HS_BLCK,
								  0 AS TOTAL_HS_TOP_BLCK,
								  0 AS DATAR_RKT_BLCK,
								  0 AS BUKIT_RKT_BLCK,
								  0 AS TOTAL_RKT_TOP_BLCK,
								  0 AS SELISIH_DATAR_BLCK,
								  0 AS SELISIH_BUKIT_BLCK,
								  0 AS SELISIH_TOTAL_BLCK_TOP
							 FROM    TM_HECTARE_STATEMENT THS
								  LEFT JOIN
									 (  SELECT PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN,
											   (CASE
												   WHEN (SUM(CASE
																WHEN plan_jan > 0 THEN 1
																ELSE 0
															 END)
														 + SUM(CASE
																  WHEN plan_feb > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_mar > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_apr > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_may > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jun > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jul > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_aug > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_sep > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_oct > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_nov > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_dec > 0 THEN 1
																  ELSE 0
															   END)) = 0
												   THEN
													  1
												   ELSE
													  (SUM(CASE
															  WHEN plan_jan > 0 THEN 1
															  ELSE 0
														   END)
													   + SUM(CASE
																WHEN plan_feb > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_mar > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_apr > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_may > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jun > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jul > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_aug > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_sep > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_oct > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_nov > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_dec > 0 THEN 1
																ELSE 0
															 END))
												END)
												  count_data,
											   (PLAN_SETAHUN
												/ (CASE
													  WHEN (SUM(CASE
																   WHEN plan_jan > 0 THEN 1
																   ELSE 0
																END)
															+ SUM(CASE
																	 WHEN plan_feb > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_mar > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_apr > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_may > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jun > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jul > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_aug > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_sep > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_oct > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_nov > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_dec > 0 THEN 1
																	 ELSE 0
																  END)) = 0
													  THEN
														 1
													  ELSE
														 (SUM(CASE
																 WHEN plan_jan > 0 THEN 1
																 ELSE 0
															  END)
														  + SUM(CASE
																   WHEN plan_feb > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_mar > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_apr > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_may > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jun > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jul > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_aug > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_sep > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_oct > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_nov > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_dec > 0 THEN 1
																   ELSE 0
																END))
												   END))
												  avg_ha
										  FROM TR_RKT
										 WHERE $where1
									  GROUP BY PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN) TRKT
								  ON     THS.period_budget = TRKT.period_budget
									 AND THS.BA_CODE = TRKT.BA_CODE
									 AND THS.AFD_CODE = TRKT.AFD_CODE
									 AND THS.BLOCK_CODE = TRKT.BLOCK_CODE
									 AND THS.MATURITY_STAGE_SMS2 = TRKT.MATURITY_STAGE_SMS2
							WHERE $where
						 GROUP BY THS.PERIOD_BUDGET, THS.BA_CODE, activity_code
						 UNION ALL
						   SELECT THS.PERIOD_BUDGET, THS.BA_CODE,
								  ACTIVITY_CODE,
								  0 AS TBM0_HS_HA,
								  0 AS TBM1_HS_HA,
								  0 AS TBM2_HS_HA,
								  0 AS TBM3_HS_HA,
								  0 AS TM_HS_HA,
								  0 AS TOTAL_HS_HA,
								  0 AS TBM0_RKT_HA,
								  0 AS TBM1_RKT_HA,
								  0 AS TBM2_RKT_HA,
								  0 AS TBM3_RKT_HA,
								  0 AS TM_RKT_HA,
								  0 AS TOTAL_RKT_HA,
								  0 AS SELISIH_TBM0_HA,
								  0 AS SELISIH_TBM1_HA,
								  0 AS SELISIH_TBM2_HA,
								  0 AS SELISIH_TBM3_HA,
								  0 AS SELISIH_TM_HA,
								  0 AS SELISIH_TOTAL_HA,
								  0 AS TBM0_HS_BLCK,
								  0 AS TBM1_HS_BLCK,
								  0 AS TBM2_HS_BLCK,
								  0 AS TBM3_HS_BLCK,
								  0 AS TM_HS_BLCK,
								  0 AS TOTAL_HS_BLCK,
								  0 AS TBM0_RKT_BLCK,
								  0 AS TBM1_RKT_BLCK,
								  0 AS TBM2_RKT_BLCK,
								  0 AS TBM3_RKT_BLCK,
								  0 AS TM_RKT_BLCK,
								  0 AS TOTAL_RKT_BLCK,
								  0 AS SELISIH_TBM0_BLCK,
								  0 AS SELISIH_TBM1_BLCK,
								  0 AS SELISIH_TBM2_BLCK,
								  0 AS SELISIH_TBM3_BLCK,
								  0 AS SELISIH_TM_BLCK,
								  0 AS SELISIH_BLCK,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'MINERAL' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS MINERAL_HS,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'PASIR' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS PASIR_HS,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'GAMBUT' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS GAMBUT_HS,
								  (SUM(CASE
										  WHEN THS.LAND_TYPE = 'MINERAL' THEN HA_PLANTED
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN THS.LAND_TYPE = 'PASIR' THEN HA_PLANTED
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN THS.LAND_TYPE = 'GAMBUT' THEN HA_PLANTED
											ELSE 0
										 END))
									 AS TOTAL_HS_LT,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'MINERAL' THEN AVG_HA
										 ELSE 0
									  END)
									 AS MINERAL_RKT,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'PASIR' THEN AVG_HA
										 ELSE 0
									  END)
									 AS PASIR_RKT,
								  SUM(CASE
										 WHEN THS.LAND_TYPE = 'GAMBUT' THEN AVG_HA
										 ELSE 0
									  END)
									 AS GAMBUT_RKT,
								  (SUM(CASE
										  WHEN THS.LAND_TYPE = 'MINERAL' THEN AVG_HA
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN THS.LAND_TYPE = 'PASIR' THEN AVG_HA
											ELSE 0
										 END)
								   + SUM(CASE
											WHEN THS.LAND_TYPE = 'GAMBUT' THEN AVG_HA
											ELSE 0
										 END))
									 AS TOTAL_RKT_LT,
								  (SUM(CASE
										  WHEN THS.LAND_TYPE = 'MINERAL' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN THS.LAND_TYPE = 'MINERAL' THEN AVG_HA
											ELSE 0
										 END))
									 AS SELISIH_MINERAL_HA,
								  (SUM(CASE
										  WHEN THS.LAND_TYPE = 'PASIR' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN THS.LAND_TYPE = 'PASIR' THEN AVG_HA
											ELSE 0
										 END))
									 AS SELISIH_PASIR_HA,
								  (SUM(CASE
										  WHEN THS.LAND_TYPE = 'GAMBUT' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN THS.LAND_TYPE = 'GAMBUT' THEN AVG_HA
											ELSE 0
										 END))
									 AS SELISIH_GAMBUT_HA,
								  ( (SUM(CASE
											WHEN THS.LAND_TYPE = 'MINERAL' THEN HA_PLANTED
											ELSE 0
										 END)
									 + SUM(CASE
											  WHEN THS.LAND_TYPE = 'PASIR' THEN HA_PLANTED
											  ELSE 0
										   END)
									 + SUM(CASE
											  WHEN THS.LAND_TYPE = 'GAMBUT' THEN HA_PLANTED
											  ELSE 0
										   END))
								   - (SUM(CASE
											 WHEN THS.LAND_TYPE = 'MINERAL' THEN AVG_HA
											 ELSE 0
										  END)
									  + SUM(CASE
											   WHEN THS.LAND_TYPE = 'PASIR' THEN AVG_HA
											   ELSE 0
											END)
									  + SUM(CASE
											   WHEN THS.LAND_TYPE = 'GAMBUT' THEN AVG_HA
											   ELSE 0
											END)))
									 AS SELISIH_TOTAL_HA_LT,
								  SUM (CASE WHEN THS.LAND_TYPE = 'MINERAL' THEN 1 ELSE 0 END)
									 AS MINERAL_HS_BLCK,
								  SUM (CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END)
									 AS PASIR_HS_BLCK,
								  SUM (CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END)
									 AS GAMBUT_HS_BLCK,
								  (SUM (
									  CASE WHEN THS.LAND_TYPE = 'MINERAL' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END))
									 AS TOTAL_HS_LT_BLCK,
								  SUM (CASE WHEN THS.LAND_TYPE = 'MINERAL' THEN 1 ELSE 0 END)
									 AS MINERAL_RKT_BLCK,
								  SUM (CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END)
									 AS PASIR_RKT_BLCK,
								  SUM (CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END)
									 AS GAMBUT_RKT_BLCK,
								  (SUM (
									  CASE WHEN THS.LAND_TYPE = 'MINERAL' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END))
									 AS TOTAL_RKT_LT_BLCK,
								  (SUM (
									  CASE WHEN THS.LAND_TYPE = 'MINERAL' THEN 1 ELSE 0 END)
								   - SUM(CASE
											WHEN THS.LAND_TYPE = 'MINERAL' THEN 1
											ELSE 0
										 END))
									 AS SELISIH_MINERAL_BLCK,
								  (SUM (CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END)
								   - SUM (
										CASE WHEN THS.LAND_TYPE = 'PASIR' THEN 1 ELSE 0 END))
									 AS SELISIH_PASIR_BLCK,
								  (SUM (CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END)
								   - SUM (
										CASE WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1 ELSE 0 END))
									 AS SELISIH_GAMBUT_BLCK,
								  ( (SUM(CASE
											WHEN THS.LAND_TYPE = 'MINERAL' THEN 1
											ELSE 0
										 END)
									 + SUM(CASE
											  WHEN THS.LAND_TYPE = 'PASIR' THEN 1
											  ELSE 0
										   END)
									 + SUM(CASE
											  WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1
											  ELSE 0
										   END))
								   - (SUM(CASE
											 WHEN THS.LAND_TYPE = 'MINERAL' THEN 1
											 ELSE 0
										  END)
									  + SUM(CASE
											   WHEN THS.LAND_TYPE = 'PASIR' THEN 1
											   ELSE 0
											END)
									  + SUM(CASE
											   WHEN THS.LAND_TYPE = 'GAMBUT' THEN 1
											   ELSE 0
											END)))
									 AS SELISIH_TOTAL_BLCK_LT,
								  0 AS DATAR_HS,
								  0 AS BUKIT_HS,
								  0 AS TOTAL_HS_TOP,
								  0 AS DATAR_RKT,
								  0 AS BUKIT_RKT,
								  0 AS TOTAL_RKT_TOP,
								  0 AS SELISIH_DATAR_HA,
								  0 AS SELISIH_BUKIT_HA,
								  0 AS SELISIH_TOTAL_HA_TOP,
								  0 AS DATAR_HS_BLCK,
								  0 AS BUKIT_HS_BLCK,
								  0 AS TOTAL_HS_TOP_BLCK,
								  0 AS DATAR_RKT_BLCK,
								  0 AS BUKIT_RKT_BLCK,
								  0 AS TOTAL_RKT_TOP_BLCK,
								  0 AS SELISIH_DATAR_BLCK,
								  0 AS SELISIH_BUKIT_BLCK,
								  0 AS SELISIH_TOTAL_BLCK_TOP
							 FROM    TM_HECTARE_STATEMENT THS
								  LEFT JOIN
									 (  SELECT PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN,
											   (CASE
												   WHEN (SUM(CASE
																WHEN plan_jan > 0 THEN 1
																ELSE 0
															 END)
														 + SUM(CASE
																  WHEN plan_feb > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_mar > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_apr > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_may > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jun > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jul > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_aug > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_sep > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_oct > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_nov > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_dec > 0 THEN 1
																  ELSE 0
															   END)) = 0
												   THEN
													  1
												   ELSE
													  (SUM(CASE
															  WHEN plan_jan > 0 THEN 1
															  ELSE 0
														   END)
													   + SUM(CASE
																WHEN plan_feb > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_mar > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_apr > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_may > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jun > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jul > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_aug > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_sep > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_oct > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_nov > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_dec > 0 THEN 1
																ELSE 0
															 END))
												END)
												  count_data,
											   (PLAN_SETAHUN
												/ (CASE
													  WHEN (SUM(CASE
																   WHEN plan_jan > 0 THEN 1
																   ELSE 0
																END)
															+ SUM(CASE
																	 WHEN plan_feb > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_mar > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_apr > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_may > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jun > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jul > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_aug > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_sep > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_oct > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_nov > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_dec > 0 THEN 1
																	 ELSE 0
																  END)) = 0
													  THEN
														 1
													  ELSE
														 (SUM(CASE
																 WHEN plan_jan > 0 THEN 1
																 ELSE 0
															  END)
														  + SUM(CASE
																   WHEN plan_feb > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_mar > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_apr > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_may > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jun > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jul > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_aug > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_sep > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_oct > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_nov > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_dec > 0 THEN 1
																   ELSE 0
																END))
												   END))
												  avg_ha
										  FROM TR_RKT
										 WHERE $where1
									  GROUP BY PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN) TRKT
								  ON     THS.period_budget = TRKT.period_budget
									 AND THS.BA_CODE = TRKT.BA_CODE
									 AND THS.AFD_CODE = TRKT.AFD_CODE
									 AND THS.BLOCK_CODE = TRKT.BLOCK_CODE
									 AND THS.MATURITY_STAGE_SMS2 = TRKT.MATURITY_STAGE_SMS2
							WHERE $where
						 GROUP BY THS.PERIOD_BUDGET, THS.BA_CODE, ACTIVITY_CODE
						 UNION ALL
						   SELECT THS.PERIOD_BUDGET, THS.BA_CODE,
								  ACTIVITY_CODE,
								  0 AS TBM0_HS_HA,
								  0 AS TBM1_HS_HA,
								  0 AS TBM2_HS_HA,
								  0 AS TBM3_HS_HA,
								  0 AS TM_HS_HA,
								  0 AS TOTAL_HS_HA,
								  0 AS TBM0_RKT_HA,
								  0 AS TBM1_RKT_HA,
								  0 AS TBM2_RKT_HA,
								  0 AS TBM3_RKT_HA,
								  0 AS TM_RKT_HA,
								  0 AS TOTAL_RKT_HA,
								  0 AS SELISIH_TBM0_HA,
								  0 AS SELISIH_TBM1_HA,
								  0 AS SELISIH_TBM2_HA,
								  0 AS SELISIH_TBM3_HA,
								  0 AS SELISIH_TM_HA,
								  0 AS SELISIH_TOTAL_HA,
								  0 AS TBM0_HS_BLCK,
								  0 AS TBM1_HS_BLCK,
								  0 AS TBM2_HS_BLCK,
								  0 AS TBM3_HS_BLCK,
								  0 AS TM_HS_BLCK,
								  0 AS TOTAL_HS_BLCK,
								  0 AS TBM0_RKT_BLCK,
								  0 AS TBM1_RKT_BLCK,
								  0 AS TBM2_RKT_BLCK,
								  0 AS TBM3_RKT_BLCK,
								  0 AS TM_RKT_BLCK,
								  0 AS TOTAL_RKT_BLCK,
								  0 AS SELISIH_TBM0_BLCK,
								  0 AS SELISIH_TBM1_BLCK,
								  0 AS SELISIH_TBM2_BLCK,
								  0 AS SELISIH_TBM3_BLCK,
								  0 AS SELISIH_TM_BLCK,
								  0 AS SELISIH_BLCK,
								  0 AS MINERAL_HS,
								  0 AS PASIR_HS,
								  0 AS GAMBUT_HS,
								  0 AS TOTAL_HS_LT,
								  0 AS MINERAL_RKT,
								  0 AS PASIR_RKT,
								  0 AS GAMBUT_RKT,
								  0 AS TOTAL_RKT_LT,
								  0 AS SELISIH_MINERAL_HA,
								  0 AS SELISIH_PASIR_HA,
								  0 AS SELISIH_GAMBUT_HA,
								  0 AS SELISIH_TOTAL_HA_LT,
								  0 AS MINERAL_HS_BLCK,
								  0 AS PASIR_HS_BLCK,
								  0 AS GAMBUT_HS_BLCK,
								  0 AS TOTAL_HS_LT_BLCK,
								  0 AS MINERAL_RKT_BLCK,
								  0 AS PASIR_RKT_BLCK,
								  0 AS GAMBUT_RKT_BLCK,
								  0 AS TOTAL_RKT_LT_BLCK,
								  0 AS SELISIH_MINERAL_BLCK,
								  0 AS SELISIH_PASIR_BLCK,
								  0 AS SELISIH_GAMBUT_BLCK,
								  0 AS SELISIH_TOTAL_BLCK_LT,
								  SUM(CASE
										 WHEN THS.TOPOGRAPHY = 'DATAR' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS DATAR_HS,
								  SUM(CASE
										 WHEN THS.TOPOGRAPHY = 'BUKIT' THEN HA_PLANTED
										 ELSE 0
									  END)
									 AS BUKIT_HS,
								  (SUM(CASE
										  WHEN THS.TOPOGRAPHY = 'DATAR' THEN HA_PLANTED
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN THS.TOPOGRAPHY = 'BUKIT' THEN HA_PLANTED
											ELSE 0
										 END))
									 AS TOTAL_HS_TOP,
								  SUM(CASE
										 WHEN THS.TOPOGRAPHY = 'DATAR' THEN AVG_HA
										 ELSE 0
									  END)
									 AS DATAR_RKT,
								  SUM(CASE
										 WHEN THS.TOPOGRAPHY = 'BUKIT' THEN AVG_HA
										 ELSE 0
									  END)
									 AS BUKIT_RKT,
								  (SUM(CASE
										  WHEN THS.TOPOGRAPHY = 'DATAR' THEN AVG_HA
										  ELSE 0
									   END)
								   + SUM(CASE
											WHEN THS.TOPOGRAPHY = 'BUKIT' THEN AVG_HA
											ELSE 0
										 END))
									 AS TOTAL_RKT_TOP,
								  (SUM(CASE
										  WHEN THS.TOPOGRAPHY = 'DATAR' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN THS.TOPOGRAPHY = 'DATAR' THEN AVG_HA
											ELSE 0
										 END))
									 AS SELISIH_DATAR_HA,
								  (SUM(CASE
										  WHEN THS.TOPOGRAPHY = 'BUKIT' THEN HA_PLANTED
										  ELSE 0
									   END)
								   - SUM(CASE
											WHEN THS.TOPOGRAPHY = 'BUKIT' THEN AVG_HA
											ELSE 0
										 END))
									 AS SELISIH_BUKIT_HA,
								  ( (SUM(CASE
											WHEN THS.TOPOGRAPHY = 'DATAR' THEN HA_PLANTED
											ELSE 0
										 END)
									 + SUM(CASE
											  WHEN THS.TOPOGRAPHY = 'BUKIT' THEN HA_PLANTED
											  ELSE 0
										   END))
								   - (SUM(CASE
											 WHEN THS.TOPOGRAPHY = 'DATAR' THEN AVG_HA
											 ELSE 0
										  END)
									  + SUM(CASE
											   WHEN THS.TOPOGRAPHY = 'BUKIT' THEN AVG_HA
											   ELSE 0
											END)))
									 AS SELISIH_TOTAL_HA_TOP,
								  SUM (CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
									 AS DATAR_HS_BLCK,
								  SUM (CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END)
									 AS BUKIT_HS_BLCK,
								  (SUM (CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END))
									 AS TOTAL_HS_TOP_BLCK,
								  SUM (CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
									 AS DATAR_RKT_BLCK,
								  SUM (CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END)
									 AS BUKIT_RKT_BLCK,
								  (SUM (CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
								   + SUM (
										CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END))
									 AS TOTAL_RKT_TOP_BLCK,
								  (SUM (CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
								   - SUM (
										CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END))
									 AS SELISIH_DATAR_BLCK,
								  (SUM (CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END)
								   - SUM (
										CASE WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1 ELSE 0 END))
									 AS SELISIH_BUKIT_BLCK,
								  ( (SUM (
										CASE WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1 ELSE 0 END)
									 + SUM(CASE
											  WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1
											  ELSE 0
										   END))
								   - (SUM(CASE
											 WHEN THS.TOPOGRAPHY = 'DATAR' THEN 1
											 ELSE 0
										  END)
									  + SUM(CASE
											   WHEN THS.TOPOGRAPHY = 'BUKIT' THEN 1
											   ELSE 0
											END)))
									 AS SELISIH_TOTAL_BLCK_TOP
							 FROM    TM_HECTARE_STATEMENT THS
								  LEFT JOIN
									 (  SELECT PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN,
											   (CASE
												   WHEN (SUM(CASE
																WHEN plan_jan > 0 THEN 1
																ELSE 0
															 END)
														 + SUM(CASE
																  WHEN plan_feb > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_mar > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_apr > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_may > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jun > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_jul > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_aug > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_sep > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_oct > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_nov > 0 THEN 1
																  ELSE 0
															   END)
														 + SUM(CASE
																  WHEN plan_dec > 0 THEN 1
																  ELSE 0
															   END)) = 0
												   THEN
													  1
												   ELSE
													  (SUM(CASE
															  WHEN plan_jan > 0 THEN 1
															  ELSE 0
														   END)
													   + SUM(CASE
																WHEN plan_feb > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_mar > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_apr > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_may > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jun > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_jul > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_aug > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_sep > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_oct > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_nov > 0 THEN 1
																ELSE 0
															 END)
													   + SUM(CASE
																WHEN plan_dec > 0 THEN 1
																ELSE 0
															 END))
												END)
												  count_data,
											   (PLAN_SETAHUN
												/ (CASE
													  WHEN (SUM(CASE
																   WHEN plan_jan > 0 THEN 1
																   ELSE 0
																END)
															+ SUM(CASE
																	 WHEN plan_feb > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_mar > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_apr > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_may > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jun > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_jul > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_aug > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_sep > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_oct > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_nov > 0 THEN 1
																	 ELSE 0
																  END)
															+ SUM(CASE
																	 WHEN plan_dec > 0 THEN 1
																	 ELSE 0
																  END)) = 0
													  THEN
														 1
													  ELSE
														 (SUM(CASE
																 WHEN plan_jan > 0 THEN 1
																 ELSE 0
															  END)
														  + SUM(CASE
																   WHEN plan_feb > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_mar > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_apr > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_may > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jun > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_jul > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_aug > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_sep > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_oct > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_nov > 0 THEN 1
																   ELSE 0
																END)
														  + SUM(CASE
																   WHEN plan_dec > 0 THEN 1
																   ELSE 0
																END))
												   END))
												  avg_ha
										  FROM TR_RKT
										 WHERE $where1
									  GROUP BY PERIOD_BUDGET,
											   BA_CODE,
											   ACTIVITY_CODE,
											   AFD_CODE,
											   BLOCK_CODE,
											   MATURITY_STAGE_SMS2,
											   PLAN_SETAHUN) TRKT
								  ON     THS.period_budget = TRKT.period_budget
									 AND THS.BA_CODE = TRKT.BA_CODE
									 AND THS.AFD_CODE = TRKT.AFD_CODE
									 AND THS.BLOCK_CODE = TRKT.BLOCK_CODE
									 AND THS.MATURITY_STAGE_SMS2 = TRKT.MATURITY_STAGE_SMS2
							WHERE $where
						 GROUP BY THS.PERIOD_BUDGET, THS.BA_CODE, ACTIVITY_CODE) ALL_HA
					 LEFT JOIN
						TM_ACTIVITY TA
					 ON TA.ACTIVITY_CODE = ALL_HA.ACTIVITY_CODE
			GROUP BY PERIOD_BUDGET, BA_CODE, ALL_HA.ACTIVITY_CODE, TA.DESCRIPTION
			ORDER BY BA_CODE, ALL_HA.ACTIVITY_CODE
		";
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}		
		/* ################################################### generate excel koneksitas hs ################################################### */
		
		return $result;
	}
	
	//generate report summary development cost
    public function reportSummaryDevelopmentCost($params = array())
    {
		$where = $select_group = $order_group = "";
		
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);
		
		/* ################################################### generate excel development cost ################################################### */
		$query = $this->querySummaryDevelopmentCostPerBa($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}		
		/* ################################################### generate excel development cost ################################################### */
				
		return $result;
	}
	
	//generate module review development cost per BA
    public function modReviewDevelopmentCostPerBa($params = array())
    {
		$where = $select_group = $order_group = "";
		
		// ############################################# get all BA #############################################
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		$query = "
			SELECT REGION_NAME, BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
			$where
			ORDER BY REGION_NAME, BA_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['BA_CODE'] = array(); // distinct BA
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct BA_CODE
				if (in_array($row['BA_CODE'], $result['BA_CODE']) == false) {
					array_push($result['BA_CODE'], $row['BA_CODE']);
				}
				
				$result['REGION_NAME'] = $row['REGION_NAME'];
			}
		}
		
		// ############################################# get all group + activity #############################################
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		$query = "
			SELECT 	$select_group
					MAPP.ACTIVITY_CODE,
					REPORT.ACTIVITY_DESC
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_DEV_COST ALL_ACT
				WHERE 1 = 1
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			WHERE MAPP.ACTIVITY_CODE IS NOT NULL
				AND REPORT.ACTIVITY_DESC IS NOT NULL
			ORDER BY $order_group
					 MAPP.ACTIVITY_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['GROUP_ACTIVITY'] = array(); // distinct GROUP - ACTIVITY_CODE
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct GROUP_ACTIVITY
				if (in_array($row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE'], $result['GROUP_ACTIVITY']) == false) {
					array_push($result['GROUP_ACTIVITY'], $row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']);
					
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP01_DESC'] = $row['GROUP01_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP02_DESC'] = $row['GROUP02_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_CODE'] = $row['ACTIVITY_CODE'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_DESC'] = $row['ACTIVITY_DESC'];
				}
			}
		}
		
		/* ################################################### generate excel module review development cost ################################################### */
		$query = $this->querySummaryDevelopmentCostPerBa($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['BA_CODE']]['rp_ha'] = $row['RP_HA_SETAHUN'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['BA_CODE']]['NORMA'] = $row['NORMA'];
				
				$result['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			}
		}		
		/* ################################################### generate excel module review development cost ################################################### */

		return $result;
	}
	
	//query summary development cost per AFD
    public function querySummaryDevelopmentCostPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND ALL_ACT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					CASE WHEN --TAMBAHAN ARIES NAMBAH NORMA BIAYA/BORONGAN 04-06-2015
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) IS NULL
                    THEN    
                        (SELECT PRICE FROM TN_HARGA_BORONG 
                        WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                        AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                        AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) 
                    ELSE
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET)
                    END
                    AS NORMA,
					STRUKTUR_REPORT.PERIOD_BUDGET, 
					STRUKTUR_REPORT.REGION_CODE, 
					STRUKTUR_REPORT.BA_CODE, 
					STRUKTUR_REPORT.ESTATE_NAME, 
					STRUKTUR_REPORT.AFD_CODE, 
					STRUKTUR_REPORT.ACTIVITY_CODE, 
					STRUKTUR_REPORT.ACTIVITY_DESC, 
					STRUKTUR_REPORT.UOM, 
					STRUKTUR_REPORT.QTY_SMS1, 
					STRUKTUR_REPORT.QTY_SMS2, 
					STRUKTUR_REPORT.COST_SMS1, 
					STRUKTUR_REPORT.COST_SMS2, 
					STRUKTUR_REPORT.COST_SETAHUN,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS1 > 0
						THEN (STRUKTUR_REPORT.COST_SMS1 / STRUKTUR_REPORT.QTY_SMS1)
						ELSE 0
					END as RP_HA_SMS1,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN (STRUKTUR_REPORT.COST_SMS2 / STRUKTUR_REPORT.QTY_SMS2)
						ELSE 0
					END as RP_HA_SMS2,
					CASE
						WHEN STRUKTUR_REPORT.ACTIVITY_CODE IN ('10100', '10200', '10300', '10400', '10500', '10600', '40400', '40500', '40600') 
							 AND (STRUKTUR_REPORT.QTY_SMS1 + STRUKTUR_REPORT.QTY_SMS2) > 0
						THEN STRUKTUR_REPORT.COST_SETAHUN / (STRUKTUR_REPORT.QTY_SMS1 + STRUKTUR_REPORT.QTY_SMS2)
						WHEN STRUKTUR_REPORT.ACTIVITY_CODE IN ('10100', '10200', '10300', '10400', '10500', '10600', '40400', '40500', '40600') 
							 AND (STRUKTUR_REPORT.QTY_SMS1 + STRUKTUR_REPORT.QTY_SMS2) = 0
						THEN 0
						WHEN STRUKTUR_REPORT.ACTIVITY_CODE NOT IN ('10100', '10200', '10300', '10400', '10500', '10600', '40400', '40500', '40600') 
							 AND STRUKTUR_REPORT.QTY_SMS1 = 0 
							 AND STRUKTUR_REPORT.QTY_SMS2 = 0
						THEN 0
						WHEN STRUKTUR_REPORT.ACTIVITY_CODE NOT IN ('10100', '10200', '10300', '10400', '10500', '10600', '40400', '40500', '40600') 
							 AND STRUKTUR_REPORT.QTY_SMS1 > 0 
							 AND STRUKTUR_REPORT.QTY_SMS2 = 0
						THEN STRUKTUR_REPORT.COST_SMS1 / STRUKTUR_REPORT.QTY_SMS1
						WHEN STRUKTUR_REPORT.ACTIVITY_CODE NOT IN ('10100', '10200', '10300', '10400', '10500', '10600', '40400', '40500', '40600') 
							 AND STRUKTUR_REPORT.QTY_SMS1 = 0 
							 AND STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN STRUKTUR_REPORT.COST_SMS2 / STRUKTUR_REPORT.QTY_SMS2
						ELSE
							(STRUKTUR_REPORT.COST_SMS1 / STRUKTUR_REPORT.QTY_SMS1) + (STRUKTUR_REPORT.COST_SMS2 / STRUKTUR_REPORT.QTY_SMS2)
					END as RP_HA_SETAHUN
			FROM (
				SELECT 	$select_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.AFD_CODE, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM, 
						CASE
							WHEN REPORT.TIPE_TRANSAKSI = 'LC' THEN MAX(SEM1)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM0' THEN MAX(SMS1_TBM0)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM1' THEN MAX(SMS1_TBM1)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM2' THEN MAX(SMS1_TBM2)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM3' THEN MAX(SMS1_TBM3)
							ELSE 0
						END AS QTY_SMS1,
						CASE
							WHEN REPORT.TIPE_TRANSAKSI = 'LC' THEN MAX(SEM2)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM0' THEN MAX(SMS2_TBM0)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM1' THEN MAX(SMS2_TBM1)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM2' THEN MAX(SMS2_TBM2)
							WHEN REPORT.TIPE_TRANSAKSI = 'TBM3' THEN MAX(SMS2_TBM3)
							ELSE 0
						END AS QTY_SMS2,
						SUM (NVL(COST_SMS1,0)) as COST_SMS1, 
						SUM (NVL(COST_SMS2,0)) as COST_SMS2, 
						SUM (NVL(COST_SETAHUN,0)) as COST_SETAHUN
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
							GROUP_CODE
					FROM (
						SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
								LVL, 
								TO_CHAR(GROUP_CODE) AS GROUP_CODE
						FROM (
							SELECT 	GROUP_CODE, 
									CONNECT_BY_ISCYCLE \"CYCLE\",
									LEVEL as LVL, 
									SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
							FROM TM_RPT_MAPPING_ACT
							WHERE level > 1
							START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
							CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
						)
						GROUP BY HIRARKI, LVL, GROUP_CODE
						ORDER BY HIRARKI
					)
				) STRUKTUR_REPORT
				LEFT JOIN TM_RPT_MAPPING_ACT MAPP
					ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
				LEFT JOIN (
					SELECT *
					FROM TMP_RPT_DEV_COST_AFD ALL_ACT
					WHERE 1 = 1
					$where
				)REPORT
					ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
					AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
					AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = REPORT.BA_CODE
				LEFT JOIN (  
					SELECT 	PERIOD_BUDGET,
							BA_CODE,
							AFD_CODE,
							SMS1_TBM0,
							SMS1_TBM1,
							SMS1_TBM2,
							SMS1_TBM3,
							SMS2_TBM0,
							SMS2_TBM1,
							SMS2_TBM2,
							SMS2_TBM3
					FROM V_REPORT_SEBARAN_HS
				) TOTAL_SEBARAN_HA
					ON TOTAL_SEBARAN_HA.PERIOD_BUDGET = REPORT.PERIOD_BUDGET
					AND TOTAL_SEBARAN_HA.BA_CODE = REPORT.BA_CODE
					AND TOTAL_SEBARAN_HA.AFD_CODE = REPORT.AFD_CODE
				LEFT JOIN (  
					SELECT 	PERIOD_BUDGET,
							BA_CODE,
							AFD_CODE,
							ACTIVITY_CODE,
							(SUM (PLAN_JAN) + SUM (PLAN_FEB) + SUM (PLAN_MAR)
							 + SUM (PLAN_APR) + SUM (PLAN_MAY) + SUM (PLAN_JUN)) AS SEM1,
							(SUM (PLAN_JUL) + SUM (PLAN_AUG) + SUM (PLAN_SEP)
							 + SUM (PLAN_OCT) + SUM (PLAN_NOV) + SUM (PLAN_DEC)) AS SEM2
					FROM TR_RKT_LC
					WHERE DELETE_TIME IS NULL
						AND FLAG_TEMP IS NULL
					GROUP BY PERIOD_BUDGET, 
						BA_CODE, 
						AFD_CODE,
						ACTIVITY_CODE
				) LC
					ON LC.PERIOD_BUDGET = REPORT.PERIOD_BUDGET
					AND LC.BA_CODE = REPORT.BA_CODE
					AND LC.AFD_CODE = REPORT.AFD_CODE
					AND LC.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				WHERE REPORT.ACTIVITY_CODE IS NOT NULL
				GROUP BY $order_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						REPORT.AFD_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM,
						REPORT.TIPE_TRANSAKSI
			) STRUKTUR_REPORT
			ORDER BY STRUKTUR_REPORT.PERIOD_BUDGET,
					 STRUKTUR_REPORT.BA_CODE,
					 STRUKTUR_REPORT.AFD_CODE,
					 $order_group
					 STRUKTUR_REPORT.ACTIVITY_CODE
		";
		
		return $query;
	}
	
	//generate module review development cost per AFD
    public function modReviewDevelopmentCostPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		
		// ############################################# get all BA #############################################
		//filter period budget
		if ($params['budgetperiod'] != '') {
			$where .= "
                AND TO_CHAR(A.PERIOD_BUDGET, 'RRRR') = '".$params['budgetperiod']."'
            ";
        }
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND B.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND A.BA_CODE = '".$params['key_find']."'
            ";
        }
		$query = "
			SELECT *
			FROM (
				SELECT B.REGION_NAME, B.COMPANY_NAME, B.ESTATE_NAME, A.AFD_CODE
				FROM TM_HECTARE_STATEMENT A
				LEFT JOIN TM_ORGANIZATION B
					ON A.BA_CODE = B.BA_CODE
				WHERE A.DELETE_USER IS NULL
				$where
				UNION
				SELECT B.REGION_NAME, B.COMPANY_NAME, B.ESTATE_NAME, A.AFD_CODE
				FROM TR_RKT_LC A
				LEFT JOIN TM_ORGANIZATION B
					ON A.BA_CODE = B.BA_CODE
				WHERE A.DELETE_USER IS NULL
				$where
			)	
			GROUP BY REGION_NAME, COMPANY_NAME, ESTATE_NAME, AFD_CODE
			ORDER BY REGION_NAME, COMPANY_NAME, ESTATE_NAME, AFD_CODE
		";
		
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['AFD_CODE'] = array(); // distinct BA
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct AFD_CODE
				if (in_array($row['AFD_CODE'], $result['AFD_CODE']) == false) {
					array_push($result['AFD_CODE'], $row['AFD_CODE']);
				}
				
				$result['REGION_NAME'] = $row['REGION_NAME'];
				$result['COMPANY_NAME'] = $row['COMPANY_NAME'];
				$result['ESTATE_NAME'] = $row['ESTATE_NAME'];
			}
		}
		
		// ############################################# get all group + activity #############################################
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		$query = "
			SELECT 	$select_group
					MAPP.ACTIVITY_CODE,
					REPORT.ACTIVITY_DESC
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_DEV_COST ALL_ACT
				WHERE 1 = 1
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			WHERE MAPP.ACTIVITY_CODE IS NOT NULL
				AND REPORT.ACTIVITY_DESC IS NOT NULL
			ORDER BY $order_group
					 MAPP.ACTIVITY_CODE
		";
		
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['GROUP_ACTIVITY'] = array(); // distinct GROUP - ACTIVITY_CODE
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct GROUP_ACTIVITY
				if (in_array($row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE'], $result['GROUP_ACTIVITY']) == false) {
					array_push($result['GROUP_ACTIVITY'], $row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']);
					
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP01_DESC'] = $row['GROUP01_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP02_DESC'] = $row['GROUP02_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_CODE'] = $row['ACTIVITY_CODE'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_DESC'] = $row['ACTIVITY_DESC'];
				}
			}
		}
		
		/* ################################################### generate excel module review development cost ################################################### */
		$query = $this->querySummaryDevelopmentCostPerAfd($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['AFD_CODE']]['rp_ha'] = $row['RP_HA_SETAHUN'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['NORMA'] = $row['NORMA'];
				
				$result['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			}
		}		
		/* ################################################### generate excel module review development cost ################################################### */

		return $result;
	}
	
	//generate temp table untuk estate cost
	public function tmpRptEstCost($params = array())
    {
		$where = "";
		$where1 = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";  
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
			$where1 .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		//generate estate cost per BA
		$query = "
			INSERT INTO TMP_RPT_EST_COST (
				PERIOD_BUDGET, 
				REGION_CODE, 
				BA_CODE, 
				TIPE_TRANSAKSI, 
				ACTIVITY_CODE, 
				ACTIVITY_DESC, 
				COST_ELEMENT, 
				KETERANGAN,
				UOM, 
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
				QTY_SMS1, 
				QTY_SMS2, 
				QTY_SETAHUN, 
				COST_JAN, 
				COST_FEB, 
				COST_MAR, 
				COST_APR, 
				COST_MAY, 
				COST_JUN, 
				COST_JUL, 
				COST_AUG, 
				COST_SEP, 
				COST_OCT, 
				COST_NOV, 
				COST_DEC, 
				COST_SMS1, 
				COST_SMS2, 
				COST_SETAHUN, 
				RP_ROTASI_SMS1, 
				RP_ROTASI_SMS2, 
				RP_ROTASI_TOTAL,
				INSERT_USER, 
				INSERT_TIME
			)
			SELECT 	PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					TIPE_TRANSAKSI,
					ACTIVITY_CODE,
					ACTIVITY_DESC,
					COST_ELEMENT,
					KETERANGAN,
					UOM,
					SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
					SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
					SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
					SUM (NVL (QTY_APR, 0)) AS QTY_APR,
					SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
					SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
					SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
					SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
					SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
					SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
					SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
					SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN SUM (QTY_JAN)
						ELSE (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
							  + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0)))
					END AS QTY_SMS1,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN SUM (QTY_JUL)
						ELSE (SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
							  + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
					END AS QTY_SMS2,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN 0
						ELSE (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
							  + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0))
							  + SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
							  + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
					END AS QTY_SETAHUN,
					SUM (NVL (COST_JAN, 0)) COST_JAN,
					SUM (NVL (COST_FEB, 0)) COST_FEB,
					SUM (NVL (COST_MAR, 0)) COST_MAR,
					SUM (NVL (COST_APR, 0)) COST_APR,
					SUM (NVL (COST_MAY, 0)) COST_MAY,
					SUM (NVL (COST_JUN, 0)) COST_JUN,
					SUM (NVL (COST_JUL, 0)) COST_JUL,
					SUM (NVL (COST_AUG, 0)) COST_AUG,
					SUM (NVL (COST_SEP, 0)) COST_SEP,
					SUM (NVL (COST_OCT, 0)) COST_OCT,
					SUM (NVL (COST_NOV, 0)) COST_NOV,
					SUM (NVL (COST_DEC, 0)) COST_DEC,
					(SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
					 + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))) AS COST_SMS1,
					(SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
					 + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) AS COST_SMS2,
					(SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
					 + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))
					 + SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
					 + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) AS COST_SETAHUN,
					SUM (NVL (RP_ROTASI_SMS1, 0)) AS RP_ROTASI_SMS1,
					SUM (NVL (RP_ROTASI_SMS2, 0)) AS RP_ROTASI_SMS2,
					CASE
						WHEN (SUM (NVL (RP_ROTASI_SMS1, 0))) = (SUM (NVL (RP_ROTASI_SMS2, 0)))
						THEN SUM (NVL (RP_ROTASI_SMS1, 0))
						ELSE (SUM (NVL (RP_ROTASI_SMS1, 0)) + SUM (NVL (RP_ROTASI_SMS2, 0)))
					END AS RP_ROTASI_TOTAL,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
			FROM (
				-- HECTARE TANAM TM - SMS 1
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'HA_TM' ACTIVITY_CODE,
						'HEKTAR TM' AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'HA' AS UOM,
						RKT.HA_PLANTED AS QTY_JAN,
						RKT.HA_PLANTED AS QTY_FEB,
						RKT.HA_PLANTED AS QTY_MAR,
						RKT.HA_PLANTED AS QTY_APR,
						RKT.HA_PLANTED AS QTY_MAY,
						RKT.HA_PLANTED AS QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TM_HECTARE_STATEMENT RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL 
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					$where
				UNION ALL	
				-- HECTARE TANAM TM - SMS 2
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'HA_TM' ACTIVITY_CODE,
						'HEKTAR TM' AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'HA' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.HA_PLANTED AS QTY_JUL,
						RKT.HA_PLANTED AS QTY_AUG,
						RKT.HA_PLANTED AS QTY_SEP,
						RKT.HA_PLANTED AS QTY_OCT,
						RKT.HA_PLANTED AS QTY_NOV,
						RKT.HA_PLANTED AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TM_HECTARE_STATEMENT RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL 
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					$where
				UNION ALL
				-- PRODUKSI TBS	
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' TIPE_TRANSAKSI,
						'PRODUKSI_TBS' ACTIVITY_CODE,
						'PRODUKSI TBS' ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'TON' UOM,
						RKT.JAN QTY_JAN,
						RKT.FEB QTY_FEB,
						RKT.MAR QTY_MAR,
						RKT.APR QTY_APR,
						RKT.MAY QTY_MAY,
						RKT.JUN QTY_JUN,
						RKT.JUL QTY_JUL,
						RKT.AUG QTY_AUG,
						RKT.SEP QTY_SEP,
						RKT.OCT QTY_OCT,
						RKT.NOV QTY_NOV,
						RKT.DEC QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_PRODUKSI_PERIODE_BUDGET RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					$where
				UNION ALL	
				-- BIAYA PRODUKSI UNTUK RKT RAWAT : SMS 1 TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						RKT.PLAN_JAN AS QTY_JAN,
						RKT.PLAN_FEB AS QTY_FEB,
						RKT.PLAN_MAR AS QTY_MAR,
						RKT.PLAN_APR AS QTY_APR,
						RKT.PLAN_MAY AS QTY_MAY,
						RKT.PLAN_JUN AS QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.DIS_JAN AS COST_JAN,
						RKT.DIS_FEB AS COST_FEB,
						RKT.DIS_MAR AS COST_MAR,
						RKT.DIS_APR AS COST_APR,
						RKT.DIS_MAY AS COST_MAY,
						RKT.DIS_JUN AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						RKT.RP_ROTASI_SMS1 AS RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_COST_ELEMENT RKT
				LEFT JOIN TR_RKT RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE 
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.TIPE_TRANSAKSI IN ('MANUAL_INFRA', 'MANUAL_NON_INFRA', 'MANUAL_NON_INFRA_OPSI')
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					$where
				UNION ALL	
				-- BIAYA PRODUKSI UNTUK RKT RAWAT : SMS 2 TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.PLAN_JUL AS QTY_JUL,
						RKT.PLAN_AUG AS QTY_AUG,
						RKT.PLAN_SEP AS QTY_SEP,
						RKT.PLAN_OCT AS QTY_OCT,
						RKT.PLAN_NOV AS QTY_NOV,
						RKT.PLAN_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						RKT.DIS_JUL AS COST_JUL,
						RKT.DIS_AUG AS COST_AUG,
						RKT.DIS_SEP AS COST_SEP,
						RKT.DIS_OCT AS COST_OCT,
						RKT.DIS_NOV AS COST_NOV,
						RKT.DIS_DEC AS COST_DEC,
						0 RP_ROTASI_SMS1,
						RKT.RP_ROTASI_SMS2 AS RP_ROTASI_SMS2
				FROM TR_RKT_COST_ELEMENT RKT
				LEFT JOIN TR_RKT RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE 
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.TIPE_TRANSAKSI IN ('MANUAL_INFRA', 'MANUAL_NON_INFRA', 'MANUAL_NON_INFRA_OPSI')
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					$where
				UNION ALL	
				-- BIAYA UMUM + RELATION (SELAIN COA 1212010101, 5101030504) UNTUK TM
				SELECT	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.COA_CODE AS ACTIVITY_CODE,
						RKT.COA_DESC AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'' UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_JAN) AS COST_JAN,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_FEB) AS COST_FEB,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_MAR) AS COST_MAR,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_APR) AS COST_APR,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_MAY) AS COST_MAY,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_JUN) AS COST_JUN,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_JUL) AS COST_JUL,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_AUG) AS COST_AUG,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_SEP) AS COST_SEP,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_OCT) AS COST_OCT,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_NOV) AS COST_NOV,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_DEC) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM V_TOTAL_RELATION_COST RKT
				LEFT JOIN (
					SELECT 	HS.PERIOD_BUDGET,
							HS.BA_CODE,
							SUM (HS.SMS1_TM) SMS1_TM,
							SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
							SUM (HS.SMS2_TM) SMS2_TM,
							SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
					FROM V_REPORT_SEBARAN_HS HS
					GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE
				) HS
					ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					AND HS.BA_CODE = RKT.BA_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.COA_CODE NOT IN ( '1212010101',  '5101030504')
					$where
				UNION ALL	
				-- RKT PUPUK MAJEMUK COST ELEMENT MATERIAL : MATURITY STAGE SMS1 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '1' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JAN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '2' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_FEB,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '3' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '4' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_APR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '5' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAY,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '6' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (1, 2, 3, 4, 5, 6) -- SMS1
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020400' -- UNTUK PUPUK  MAJEMUK
				UNION ALL	
				-- RKT PUPUK MAJEMUK COST ELEMENT MATERIAL : MATURITY STAGE SMS2 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '7' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUL,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '8' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_AUG,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '9' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_SEP,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '10' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_OCT,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '11' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_NOV,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '12' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS2 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (7, 8, 9, 10, 11, 12) -- SMS2
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020400' -- UNTUK PUPUK  MAJEMUK
				UNION ALL	
				-- RKT PUPUK TUNGGAL COST ELEMENT MATERIAL : MATURITY STAGE SMS1 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '1' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JAN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '2' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_FEB,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '3' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '4' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_APR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '5' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAY,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '6' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (1, 2, 3, 4, 5, 6) -- SMS1
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020300' -- UNTUK PUPUK  TUNGGAL
				UNION ALL	
				-- RKT PUPUK TUNGGAL COST ELEMENT MATERIAL : MATURITY STAGE SMS2 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '7' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUL,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '8' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_AUG,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '9' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_SEP,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '10' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_OCT,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '11' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_NOV,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '12' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS2 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (7, 8, 9, 10, 11, 12) -- SMS2
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020300' -- UNTUK PUPUK  TUNGGAL
				UNION ALL	
				-- QTY KG PUPUK MAJEMUK - TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						RKT.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						RKT.QTY_MAJEMUK_JAN AS QTY_JAN,
						RKT.QTY_MAJEMUK_FEB AS QTY_FEB,
						RKT.QTY_MAJEMUK_MAR AS QTY_MAR,
						RKT.QTY_MAJEMUK_APR AS QTY_APR,
						RKT.QTY_MAJEMUK_MAY AS QTY_MAY,
						RKT.QTY_MAJEMUK_JUN AS QTY_JUN,
						RKT.QTY_MAJEMUK_JUL AS QTY_JUL,
						RKT.QTY_MAJEMUK_AUG AS QTY_AUG,
						RKT.QTY_MAJEMUK_SEP AS QTY_SEP,
						RKT.QTY_MAJEMUK_OCT AS QTY_OCT,
						RKT.QTY_MAJEMUK_NOV AS QTY_NOV,
						RKT.QTY_MAJEMUK_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM V_TOTAL_KG_PUPUK_PER_MATERIAL RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.MATURITY_STAGE = 'TM' 
					AND RKT.COA_CODE = '5101020400' -- UNTUK PUPUK MAJEMUK
					$where
				UNION ALL	
				-- QTY KG PUPUK TUNGGAL - TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						RKT.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						RKT.QTY_TUNGGAL_JAN AS QTY_JAN,
						RKT.QTY_TUNGGAL_FEB AS QTY_FEB,
						RKT.QTY_TUNGGAL_MAR AS QTY_MAR,
						RKT.QTY_TUNGGAL_APR AS QTY_APR,
						RKT.QTY_TUNGGAL_MAY AS QTY_MAY,
						RKT.QTY_TUNGGAL_JUN AS QTY_JUN,
						RKT.QTY_TUNGGAL_JUL AS QTY_JUL,
						RKT.QTY_TUNGGAL_AUG AS QTY_AUG,
						RKT.QTY_TUNGGAL_SEP AS QTY_SEP,
						RKT.QTY_TUNGGAL_OCT AS QTY_OCT,
						RKT.QTY_TUNGGAL_NOV AS QTY_NOV,
						RKT.QTY_TUNGGAL_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM V_TOTAL_KG_PUPUK_PER_MATERIAL RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.MATURITY_STAGE = 'TM' 
					AND RKT.COA_CODE = '5101020300' -- UNTUK PUPUK TUNGGAL
					$where
				UNION ALL	
				-- RKT PUPUK TUNGGAL SELAIN COST ELEMENT MATERIAL
				SELECT 	COST.PERIOD_BUDGET,
						COST.REGION_CODE,
						COST.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						COST.COST_ELEMENT,
						'' AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JAN / KG_PUPUK.QTY_TOTAL_JAN * COST.DIS_COST_JAN)
							ELSE 0
						END AS COST_JAN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_FEB / KG_PUPUK.QTY_TOTAL_FEB * COST.DIS_COST_FEB)
							ELSE 0
						END AS COST_FEB,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_MAR / KG_PUPUK.QTY_TOTAL_MAR * COST.DIS_COST_MAR)
							ELSE 0
						END AS COST_MAR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_APR / KG_PUPUK.QTY_TOTAL_APR * COST.DIS_COST_APR)
							ELSE 0
						END AS COST_APR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_MAY / KG_PUPUK.QTY_TOTAL_MAY * COST.DIS_COST_MAY)
							ELSE 0
						END AS COST_MAY,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JUN / KG_PUPUK.QTY_TOTAL_JUN * COST.DIS_COST_JUN)
							ELSE 0
						END AS COST_JUN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JUL / KG_PUPUK.QTY_TOTAL_JUL * COST.DIS_COST_JUL)
							ELSE 0
						END AS COST_JUL,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_AUG / KG_PUPUK.QTY_TOTAL_AUG * COST.DIS_COST_AUG)
							ELSE 0
						END AS COST_AUG,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_SEP / KG_PUPUK.QTY_TOTAL_SEP * COST.DIS_COST_SEP)
							ELSE 0
						END AS COST_SEP,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_OCT / KG_PUPUK.QTY_TOTAL_OCT * COST.DIS_COST_OCT)
							ELSE 0
						END AS COST_OCT,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_NOV / KG_PUPUK.QTY_TOTAL_NOV * COST.DIS_COST_NOV)
							ELSE 0
						END AS COST_NOV,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_DEC / KG_PUPUK.QTY_TOTAL_DEC * COST.DIS_COST_DEC)
							ELSE 0
						END AS COST_DEC,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS1,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS2
				FROM (  
					SELECT 	PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							COST_ELEMENT,
							MATURITY_STAGE,
							SUM (DIS_COST_JAN) DIS_COST_JAN,
							SUM (DIS_COST_FEB) DIS_COST_FEB,
							SUM (DIS_COST_MAR) DIS_COST_MAR,
							SUM (DIS_COST_APR) DIS_COST_APR,
							SUM (DIS_COST_MAY) DIS_COST_MAY,
							SUM (DIS_COST_JUN) DIS_COST_JUN,
							SUM (DIS_COST_JUL) DIS_COST_JUL,
							SUM (DIS_COST_AUG) DIS_COST_AUG,
							SUM (DIS_COST_SEP) DIS_COST_SEP,
							SUM (DIS_COST_OCT) DIS_COST_OCT,
							SUM (DIS_COST_NOV) DIS_COST_NOV,
							SUM (DIS_COST_DEC) DIS_COST_DEC,
							MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
							MAX (COST_TOOLS_KG) COST_TOOLS_KG,
							MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
					FROM (  
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
								SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
								SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
								SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
								SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
								SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
								SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
								0 DIS_COST_JUL,
								0 DIS_COST_AUG,
								0 DIS_COST_SEP,
								0 DIS_COST_OCT,
								0 DIS_COST_NOV,
								0 DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS1 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS1
						UNION ALL
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS2 AS MATURITY_STAGE,
								0 DIS_COST_JAN,
								0 DIS_COST_FEB,
								0 DIS_COST_MAR,
								0 DIS_COST_APR,
								0 DIS_COST_MAY,
								0 DIS_COST_JUN,
								SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
								SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
								SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
								SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
								SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
								SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS2 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS2
					)
					GROUP BY PERIOD_BUDGET,
						REGION_CODE,
						BA_CODE,
						COST_ELEMENT,
						MATURITY_STAGE
				) COST
				LEFT JOIN V_TOTAL_KG_PUPUK KG_PUPUK
					ON COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
					AND COST.BA_CODE = KG_PUPUK.BA_CODE
					AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
				UNION ALL	
				-- RKT PUPUK MAJEMUK SELAIN COST ELEMENT MATERIAL
				SELECT 	COST.PERIOD_BUDGET,
						COST.REGION_CODE,
						COST.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						COST.COST_ELEMENT,
						'' AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JAN / KG_PUPUK.QTY_TOTAL_JAN * COST.DIS_COST_JAN)
							ELSE 0
						END AS COST_JAN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_FEB / KG_PUPUK.QTY_TOTAL_FEB * COST.DIS_COST_FEB)
							ELSE 0
						END AS COST_FEB,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_MAR / KG_PUPUK.QTY_TOTAL_MAR * COST.DIS_COST_MAR)
							ELSE 0
						END AS COST_MAR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_APR / KG_PUPUK.QTY_TOTAL_APR * COST.DIS_COST_APR)
							ELSE 0
						END AS COST_APR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_MAY / KG_PUPUK.QTY_TOTAL_MAY * COST.DIS_COST_MAY)
							ELSE 0
						END AS COST_MAY,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JUN / KG_PUPUK.QTY_TOTAL_JUN * COST.DIS_COST_JUN)
							ELSE 0
						END AS COST_JUN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JUL / KG_PUPUK.QTY_TOTAL_JUL * COST.DIS_COST_JUL)
							ELSE 0
						END AS COST_JUL,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_AUG / KG_PUPUK.QTY_TOTAL_AUG * COST.DIS_COST_AUG)
							ELSE 0
						END AS COST_AUG,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_SEP / KG_PUPUK.QTY_TOTAL_SEP * COST.DIS_COST_SEP)
							ELSE 0
						END AS COST_SEP,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_OCT / KG_PUPUK.QTY_TOTAL_OCT * COST.DIS_COST_OCT)
							ELSE 0
						END AS COST_OCT,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_NOV / KG_PUPUK.QTY_TOTAL_NOV * COST.DIS_COST_NOV)
							ELSE 0
						END AS COST_NOV,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_DEC / KG_PUPUK.QTY_TOTAL_DEC * COST.DIS_COST_DEC)
							ELSE 0
						END AS COST_DEC,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS1,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS2
				FROM (  
					SELECT 	PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							COST_ELEMENT,
							MATURITY_STAGE,
							SUM (DIS_COST_JAN) DIS_COST_JAN,
							SUM (DIS_COST_FEB) DIS_COST_FEB,
							SUM (DIS_COST_MAR) DIS_COST_MAR,
							SUM (DIS_COST_APR) DIS_COST_APR,
							SUM (DIS_COST_MAY) DIS_COST_MAY,
							SUM (DIS_COST_JUN) DIS_COST_JUN,
							SUM (DIS_COST_JUL) DIS_COST_JUL,
							SUM (DIS_COST_AUG) DIS_COST_AUG,
							SUM (DIS_COST_SEP) DIS_COST_SEP,
							SUM (DIS_COST_OCT) DIS_COST_OCT,
							SUM (DIS_COST_NOV) DIS_COST_NOV,
							SUM (DIS_COST_DEC) DIS_COST_DEC,
							MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
							MAX (COST_TOOLS_KG) COST_TOOLS_KG,
							MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
					FROM (  
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
								SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
								SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
								SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
								SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
								SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
								SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
								0 DIS_COST_JUL,
								0 DIS_COST_AUG,
								0 DIS_COST_SEP,
								0 DIS_COST_OCT,
								0 DIS_COST_NOV,
								0 DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS1 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS1
						UNION ALL
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS2 AS MATURITY_STAGE,
								0 DIS_COST_JAN,
								0 DIS_COST_FEB,
								0 DIS_COST_MAR,
								0 DIS_COST_APR,
								0 DIS_COST_MAY,
								0 DIS_COST_JUN,
								SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
								SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
								SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
								SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
								SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
								SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS2 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS2
					)
					GROUP BY PERIOD_BUDGET,
						REGION_CODE,
						BA_CODE,
						COST_ELEMENT,
						MATURITY_STAGE
				) COST
				LEFT JOIN V_TOTAL_KG_PUPUK KG_PUPUK
					ON COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
					AND COST.BA_CODE = KG_PUPUK.BA_CODE
					AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
				UNION ALL					
				-- PANEN - BIAYA PEMANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030101' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'BIAYA PEMANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - PREMI PANEN JANJANG
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030201' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'PREMI PANEN JANJANG' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_JAN,
                        (SEBARAN.FEB / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_FEB,
                        (SEBARAN.MAR / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_MAR, 
                        (SEBARAN.APR / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_APR,
                        (SEBARAN.MAY / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_MAY,
                        (SEBARAN.JUN / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_JUN,
                        (SEBARAN.JUL / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_JUL,
                        (SEBARAN.AUG / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_AUG,
                        (SEBARAN.SEP / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_SEP,
                        (SEBARAN.OCT / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_OCT,
                        (SEBARAN.NOV / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_NOV,
                        (SEBARAN.DEC / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL
				-- PANEN - PREMI PANEN BRD
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030301' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'PREMI PANEN BRD' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUN,
						(SEBARAN.AUG / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_AUG,
						(SEBARAN.JUL / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUL,
						(SEBARAN.SEP / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - KRANI BUAH
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030701' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'KRANI BUAH' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.KRANI_BUAH_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ALAT PANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030103' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'ALAT PANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - SUPERVISI PEMANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030701' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'SUPERVISI PEMANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.BIAYA_SPV_RP_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - PREMI SUPIR
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-3' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'PREMI SUPIR' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.SUPIR_PREMI) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.SUPIR_PREMI) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.SUPIR_PREMI) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.SUPIR_PREMI) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.SUPIR_PREMI) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.SUPIR_PREMI) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.SUPIR_PREMI) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.SUPIR_PREMI) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.SUPIR_PREMI) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.SUPIR_PREMI) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.SUPIR_PREMI) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.SUPIR_PREMI) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ANGKUT - LANGSIR
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030404-2' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'LANGSIR' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.LANGSIR_RP) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.LANGSIR_RP) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.LANGSIR_RP) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.LANGSIR_RP) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.LANGSIR_RP) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.LANGSIR_RP) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.LANGSIR_RP) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.LANGSIR_RP) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.LANGSIR_RP) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.LANGSIR_RP) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.LANGSIR_RP) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.LANGSIR_RP) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - TRANSPORT TBS - BONGKAR MUAT
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030404-1' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'BONGKAR MUAT' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.TUKANG_MUAT_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ANGKUT - TRANSPORT TBS INTERNAL
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-2' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'TRANSPORT TBS INTERNAL - PANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.SUMBER_BIAYA_UNIT = 'INTERNAL'
					$where
				UNION ALL	
				-- PANEN - ANGKUT - TRANSPORT TBS EKSTERNAL
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030605' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'TRANSPORT TBS EKSTERNAL' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
						(SEBARAN.FEB / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
						(SEBARAN.MAR / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
						(SEBARAN.APR / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
						(SEBARAN.MAY / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
						(SEBARAN.JUN / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
						(SEBARAN.JUL / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
						(SEBARAN.AUG / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
						(SEBARAN.SEP / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
						(SEBARAN.OCT / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
						(SEBARAN.NOV / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
						(SEBARAN.DEC / 100 * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND RKT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.SUMBER_BIAYA_UNIT = 'EXTERNAL'
					$where
				UNION ALL	
				-- PANEN : PENGANGKUTAN TBS INTERNAL (COA : 5101030504)
				SELECT 	ANGKUT.PERIOD_BUDGET,
						ANGKUT.REGION_CODE,
						ANGKUT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-1' AS ACTIVITY_CODE,  -- BY ADI 16/11/2014
						'PENGANGKUTAN INTERNAL TBS - VRA' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN / 100 * ANGKUT.TOTAL) COST_JAN,
						(SEBARAN.FEB / 100 * ANGKUT.TOTAL) COST_FEB,
						(SEBARAN.MAR / 100 * ANGKUT.TOTAL) COST_MAR,
						(SEBARAN.APR / 100 * ANGKUT.TOTAL) COST_APR,
						(SEBARAN.MAY / 100 * ANGKUT.TOTAL) COST_MAY,
						(SEBARAN.JUN / 100 * ANGKUT.TOTAL) COST_JUN,
						(SEBARAN.JUL / 100 * ANGKUT.TOTAL) COST_JUL,
						(SEBARAN.AUG / 100 * ANGKUT.TOTAL) COST_AUG,
						(SEBARAN.SEP / 100 * ANGKUT.TOTAL) COST_SEP,
						(SEBARAN.OCT / 100 * ANGKUT.TOTAL) COST_OCT,
						(SEBARAN.NOV / 100 * ANGKUT.TOTAL) COST_NOV,
						(SEBARAN.DEC / 100 * ANGKUT.TOTAL) COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT 	RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							(NVL (RKT.COST_JAN, 0) + NVL (RKT.COST_FEB, 0) + NVL (RKT.COST_MAR, 0)
							 + NVL (RKT.COST_APR, 0) + NVL (RKT.COST_MAY, 0) + NVL (RKT.COST_JUN, 0)
							 + NVL (RKT.COST_JUL, 0) + NVL (RKT.COST_AUG, 0) + NVL (RKT.COST_SEP, 0)
							 + NVL (RKT.COST_OCT, 0) + NVL (RKT.COST_NOV, 0) + NVL (RKT.COST_DEC, 0)) AS TOTAL
					FROM V_TOTAL_RELATION_COST RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.COA_CODE = '5101030504'
						$where
				) ANGKUT
				LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN
					ON ANGKUT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
					AND ANGKUT.BA_CODE = SEBARAN.BA_CODE
					AND SEBARAN.DELETE_USER IS NULL
				UNION ALL	
				-- ALOKASI CHECKROLL
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.TUNJANGAN_TYPE AS ACTIVITY_CODE,
						RKT.TUNJANGAN_TYPE AS ACTIVITY_DESC,
						'LABOUR' AS COST_ELEMENT,
						'' KETERANGAN,
						'' UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.DIS_JAN,
						RKT.DIS_FEB,
						RKT.DIS_MAR,
						RKT.DIS_APR,
						RKT.DIS_MAY,
						RKT.DIS_JUN,
						RKT.DIS_JUL,
						RKT.DIS_AUG,
						RKT.DIS_SEP,
						RKT.DIS_OCT,
						RKT.DIS_NOV,
						RKT.DIS_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RPT_DISTRIBUSI_COA RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.REPORT_TYPE = 'CR_ALOKASI'
					AND RKT.DELETE_USER IS NULL
					AND RKT.MATURITY_STAGE = 'TM'
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 1 = TM & JENIS = PERULANGAN (DIKATEGORIKAN SBG BIAYA RAWAT TM)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						RKT.PLAN_JAN QTY_JAN,
						RKT.PLAN_FEB QTY_FEB,
						RKT.PLAN_MAR QTY_MAR,
						RKT.PLAN_APR QTY_APR,
						RKT.PLAN_MAY QTY_MAY,
						RKT.PLAN_JUN QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.DIS_JAN COST_JAN,
						RKT.DIS_FEB COST_FEB,
						RKT.DIS_MAR COST_MAR,
						RKT.DIS_APR COST_APR,
						RKT.DIS_MAY COST_MAY,
						RKT.DIS_JUN COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						RKT.TOTAL_RP_QTY RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PK_COST_ELEMENT RKT
				LEFT JOIN TR_RKT_PK RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN'
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 2 = TM & JENIS = PERULANGAN (DIKATEGORIKAN SBG BIAYA RAWAT TM)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.PLAN_JUL QTY_JUL,
						RKT.PLAN_AUG QTY_AUG,
						RKT.PLAN_SEP QTY_SEP,
						RKT.PLAN_OCT QTY_OCT,
						RKT.PLAN_NOV QTY_NOV,
						RKT.PLAN_DEC QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						RKT.DIS_JUL COST_JUL,
						RKT.DIS_AUG COST_AUG,
						RKT.DIS_SEP COST_SEP,
						RKT.DIS_OCT COST_OCT,
						RKT.DIS_NOV COST_NOV,
						RKT.DIS_DEC COST_DEC,
						0 RP_ROTASI_SMS1,
						RKT.TOTAL_RP_QTY RP_ROTASI_SMS2
				FROM TR_RKT_PK_COST_ELEMENT RKT
				LEFT JOIN TR_RKT_PK RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN'
					$where
			) REPORT
			GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				TIPE_TRANSAKSI,
				ACTIVITY_CODE,
				ACTIVITY_DESC,
				COST_ELEMENT,
				KETERANGAN,
				UOM
		";
		//die($query);
		$this->_db->query($query);
		$this->_db->commit();
		
		//generate estate cost per AFD
		$query = "
			INSERT INTO TMP_RPT_EST_COST_AFD (
				PERIOD_BUDGET, 
				REGION_CODE, 
				BA_CODE, 
				AFD_CODE, 
				TIPE_TRANSAKSI, 
				ACTIVITY_CODE, 
				ACTIVITY_DESC, 
				COST_ELEMENT, 
				KETERANGAN,
				UOM, 
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
				QTY_SMS1, 
				QTY_SMS2, 
				QTY_SETAHUN, 
				COST_JAN, 
				COST_FEB, 
				COST_MAR, 
				COST_APR, 
				COST_MAY, 
				COST_JUN, 
				COST_JUL, 
				COST_AUG, 
				COST_SEP, 
				COST_OCT, 
				COST_NOV, 
				COST_DEC, 
				COST_SMS1, 
				COST_SMS2, 
				COST_SETAHUN, 
				RP_ROTASI_SMS1, 
				RP_ROTASI_SMS2, 
				RP_ROTASI_TOTAL,
				INSERT_USER, 
				INSERT_TIME
			)
			SELECT 	PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					TIPE_TRANSAKSI,
					ACTIVITY_CODE,
					ACTIVITY_DESC,
					COST_ELEMENT,
					KETERANGAN,
					UOM,
					SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
					SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
					SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
					SUM (NVL (QTY_APR, 0)) AS QTY_APR,
					SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
					SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
					SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
					SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
					SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
					SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
					SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
					SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN SUM (QTY_JAN)
						ELSE (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
							  + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0)))
					END AS QTY_SMS1,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN SUM (QTY_JUL)
						ELSE (SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
							  + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
					END AS QTY_SMS2,
					CASE
						WHEN ACTIVITY_CODE = 'HA_TM'
						THEN 0
						ELSE (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
							  + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0))
							  + SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
							  + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
					END AS QTY_SETAHUN,
					SUM (NVL (COST_JAN, 0)) COST_JAN,
					SUM (NVL (COST_FEB, 0)) COST_FEB,
					SUM (NVL (COST_MAR, 0)) COST_MAR,
					SUM (NVL (COST_APR, 0)) COST_APR,
					SUM (NVL (COST_MAY, 0)) COST_MAY,
					SUM (NVL (COST_JUN, 0)) COST_JUN,
					SUM (NVL (COST_JUL, 0)) COST_JUL,
					SUM (NVL (COST_AUG, 0)) COST_AUG,
					SUM (NVL (COST_SEP, 0)) COST_SEP,
					SUM (NVL (COST_OCT, 0)) COST_OCT,
					SUM (NVL (COST_NOV, 0)) COST_NOV,
					SUM (NVL (COST_DEC, 0)) COST_DEC,
					(SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
					 + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))) AS COST_SMS1,
					(SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
					 + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) AS COST_SMS2,
					(SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
					 + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))
					 + SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
					 + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) AS COST_SETAHUN,
					SUM (NVL (RP_ROTASI_SMS1, 0)) AS RP_ROTASI_SMS1,
					SUM (NVL (RP_ROTASI_SMS2, 0)) AS RP_ROTASI_SMS2,
					CASE
						WHEN (SUM (NVL (RP_ROTASI_SMS1, 0))) = (SUM (NVL (RP_ROTASI_SMS2, 0)))
						THEN SUM (NVL (RP_ROTASI_SMS1, 0))
						ELSE (SUM (NVL (RP_ROTASI_SMS1, 0)) + SUM (NVL (RP_ROTASI_SMS2, 0)))
					END AS RP_ROTASI_TOTAL,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
			FROM (
				-- HECTARE TANAM TM - SMS 1
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'HA_TM' ACTIVITY_CODE,
						'HEKTAR TM' AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'HA' AS UOM,
						RKT.HA_PLANTED AS QTY_JAN,
						RKT.HA_PLANTED AS QTY_FEB,
						RKT.HA_PLANTED AS QTY_MAR,
						RKT.HA_PLANTED AS QTY_APR,
						RKT.HA_PLANTED AS QTY_MAY,
						RKT.HA_PLANTED AS QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TM_HECTARE_STATEMENT RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL 
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					$where
				UNION ALL	
				-- HECTARE TANAM TM - SMS 2
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'HA_TM' ACTIVITY_CODE,
						'HEKTAR TM' AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'HA' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.HA_PLANTED AS QTY_JUL,
						RKT.HA_PLANTED AS QTY_AUG,
						RKT.HA_PLANTED AS QTY_SEP,
						RKT.HA_PLANTED AS QTY_OCT,
						RKT.HA_PLANTED AS QTY_NOV,
						RKT.HA_PLANTED AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TM_HECTARE_STATEMENT RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL 
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					$where
				UNION ALL
				-- PRODUKSI TBS	
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' TIPE_TRANSAKSI,
						'PRODUKSI_TBS' ACTIVITY_CODE,
						'PRODUKSI TBS' ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'TON' UOM,
						RKT.JAN QTY_JAN,
						RKT.FEB QTY_FEB,
						RKT.MAR QTY_MAR,
						RKT.APR QTY_APR,
						RKT.MAY QTY_MAY,
						RKT.JUN QTY_JUN,
						RKT.JUL QTY_JUL,
						RKT.AUG QTY_AUG,
						RKT.SEP QTY_SEP,
						RKT.OCT QTY_OCT,
						RKT.NOV QTY_NOV,
						RKT.DEC QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_PRODUKSI_PERIODE_BUDGET RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					$where					
				UNION ALL	
				-- BIAYA PRODUKSI UNTUK RKT RAWAT : SMS 1 TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						RKT.PLAN_JAN AS QTY_JAN,
						RKT.PLAN_FEB AS QTY_FEB,
						RKT.PLAN_MAR AS QTY_MAR,
						RKT.PLAN_APR AS QTY_APR,
						RKT.PLAN_MAY AS QTY_MAY,
						RKT.PLAN_JUN AS QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.DIS_JAN AS COST_JAN,
						RKT.DIS_FEB AS COST_FEB,
						RKT.DIS_MAR AS COST_MAR,
						RKT.DIS_APR AS COST_APR,
						RKT.DIS_MAY AS COST_MAY,
						RKT.DIS_JUN AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						RKT.RP_ROTASI_SMS1 AS RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_COST_ELEMENT RKT
				LEFT JOIN TR_RKT RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE 
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.TIPE_TRANSAKSI IN ('MANUAL_INFRA', 'MANUAL_NON_INFRA', 'MANUAL_NON_INFRA_OPSI')
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					$where
				UNION ALL	
				-- BIAYA PRODUKSI UNTUK RKT RAWAT : SMS 2 TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.PLAN_JUL AS QTY_JUL,
						RKT.PLAN_AUG AS QTY_AUG,
						RKT.PLAN_SEP AS QTY_SEP,
						RKT.PLAN_OCT AS QTY_OCT,
						RKT.PLAN_NOV AS QTY_NOV,
						RKT.PLAN_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						RKT.DIS_JUL AS COST_JUL,
						RKT.DIS_AUG AS COST_AUG,
						RKT.DIS_SEP AS COST_SEP,
						RKT.DIS_OCT AS COST_OCT,
						RKT.DIS_NOV AS COST_NOV,
						RKT.DIS_DEC AS COST_DEC,
						0 RP_ROTASI_SMS1,
						RKT.RP_ROTASI_SMS2 AS RP_ROTASI_SMS2
				FROM TR_RKT_COST_ELEMENT RKT
				LEFT JOIN TR_RKT RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE 
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.TIPE_TRANSAKSI IN ('MANUAL_INFRA', 'MANUAL_NON_INFRA', 'MANUAL_NON_INFRA_OPSI')
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					$where
				UNION ALL	
				-- BIAYA UMUM + RELATION (SELAIN COA 1212010101, 5101030504) UNTUK TM
				SELECT	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.COA_CODE AS ACTIVITY_CODE,
						RKT.COA_DESC AS ACTIVITY_DESC,
						'' COST_ELEMENT,
						'' KETERANGAN,
						'' UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_JAN) AS COST_JAN,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_FEB) AS COST_FEB,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_MAR) AS COST_MAR,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_APR) AS COST_APR,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_MAY) AS COST_MAY,
						(HS.SMS1_TM / NULLIF(HS.TOTAL_HA_SMS1, 0) * RKT.COST_JUN) AS COST_JUN,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_JUL) AS COST_JUL,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_AUG) AS COST_AUG,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_SEP) AS COST_SEP,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_OCT) AS COST_OCT,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_NOV) AS COST_NOV,
						(HS.SMS2_TM / NULLIF(HS.TOTAL_HA_SMS2, 0) * RKT.COST_DEC) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT 	SEBARAN_HA.PERIOD_BUDGET,
							SEBARAN_HA.BA_CODE,
							SEBARAN_HA.AFD_CODE,
							RKT.REPORT_TYPE,
							RKT.COA_CODE,
							RKT.COA_DESC,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JAN) COST_JAN,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_FEB) COST_FEB,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_MAR) COST_MAR,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_APR) COST_APR,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_MAY) COST_MAY,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JUN) COST_JUN,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JUL) COST_JUL,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_AUG) COST_AUG,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_SEP) COST_SEP,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_OCT) COST_OCT,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_NOV) COST_NOV,
							(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_DEC) COST_DEC
					FROM (
						SELECT SUMHA_AFD.PERIOD_BUDGET,
									SUMHA_AFD.BA_CODE,
									SUMHA_AFD.AFD_CODE,
									SUM(SUMHA_AFD.TOTAL_HA_SMS1) as HA_AFD,
									SUMHA_BA.HA_BA
						FROM V_REPORT_SEBARAN_HS SUMHA_AFD
						LEFT JOIN (
							SELECT PERIOD_BUDGET,
										BA_CODE,
										SUM(TOTAL_HA_SMS1) HA_BA
							FROM V_REPORT_SEBARAN_HS 
							GROUP BY PERIOD_BUDGET,
										BA_CODE
						) SUMHA_BA
							ON SUMHA_BA.PERIOD_BUDGET = SUMHA_AFD.PERIOD_BUDGET
							AND SUMHA_BA.BA_CODE = SUMHA_AFD.BA_CODE
						GROUP BY SUMHA_AFD.PERIOD_BUDGET,
									SUMHA_AFD.BA_CODE,
									SUMHA_AFD.AFD_CODE,
									SUMHA_BA.HA_BA
					) SEBARAN_HA
					LEFT JOIN V_TOTAL_RELATION_COST RKT
						ON SEBARAN_HA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						AND SEBARAN_HA.BA_CODE = RKT.BA_CODE
				) RKT
				LEFT JOIN (
					SELECT 	HS.PERIOD_BUDGET,
							HS.BA_CODE,
							HS.AFD_CODE,
							HS.SMS1_TM,
							HS.TOTAL_HA_SMS1,
							HS.SMS2_TM,
							HS.TOTAL_HA_SMS2
					FROM V_REPORT_SEBARAN_HS HS
				) HS
					ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					AND HS.BA_CODE = RKT.BA_CODE
					AND HS.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.COA_CODE NOT IN ( '1212010101',  '5101030504')
					$where
				UNION ALL	
				-- RKT PUPUK MAJEMUK COST ELEMENT MATERIAL : MATURITY STAGE SMS1 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						HS.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '1' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JAN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '2' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_FEB,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '3' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '4' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_APR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '5' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAY,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '6' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (1, 2, 3, 4, 5, 6) -- SMS1
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020400' -- UNTUK PUPUK  MAJEMUK
				UNION ALL	
				-- RKT PUPUK MAJEMUK COST ELEMENT MATERIAL : MATURITY STAGE SMS2 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						HS.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '7' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUL,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '8' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_AUG,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '9' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_SEP,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '10' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_OCT,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '11' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_NOV,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '12' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS2 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (7, 8, 9, 10, 11, 12) -- SMS2
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020400' -- UNTUK PUPUK  MAJEMUK
				UNION ALL	
				-- RKT PUPUK TUNGGAL COST ELEMENT MATERIAL : MATURITY STAGE SMS1 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						HS.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '1' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JAN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '2' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_FEB,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '3' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '4' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_APR,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '5' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_MAY,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '6' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (1, 2, 3, 4, 5, 6) -- SMS1
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020300' -- UNTUK PUPUK  TUNGGAL
				UNION ALL	
				-- RKT PUPUK TUNGGAL COST ELEMENT MATERIAL : MATURITY STAGE SMS2 = TM
				SELECT 	HS.PERIOD_BUDGET,
						HS.REGION_CODE,
						HS.BA_CODE,
						HS.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						MATERIAL.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '7' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_JUL,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '8' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_AUG,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '9' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_SEP,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '10' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_OCT,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '11' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_NOV,
						CASE NORMA_PUPUK.BULAN_PEMUPUKAN
						   WHEN '12' THEN NORMA_PUPUK.BIAYA
						   ELSE 0
						END AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT RKT.*, ORG.REGION_CODE
					FROM TM_HECTARE_STATEMENT RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.DELETE_USER IS NULL
						AND RKT.FLAG_TEMP IS NULL
						AND RKT.MATURITY_STAGE_SMS2 = 'TM'
						$where
				)HS
				LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
					ON HS.PERIOD_BUDGET = NORMA_PUPUK.PERIOD_BUDGET
					AND HS.BA_CODE = NORMA_PUPUK.BA_CODE
					AND HS.AFD_CODE = NORMA_PUPUK.AFD_CODE
					AND HS.BLOCK_CODE = NORMA_PUPUK.BLOCK_CODE
					AND NORMA_PUPUK.DELETE_USER IS NULL
					AND NORMA_PUPUK.BULAN_PEMUPUKAN IN (7, 8, 9, 10, 11, 12) -- SMS2
				LEFT JOIN TM_MATERIAL MATERIAL
					ON NORMA_PUPUK.PERIOD_BUDGET = MATERIAL.PERIOD_BUDGET
					AND NORMA_PUPUK.BA_CODE = MATERIAL.BA_CODE
					AND NORMA_PUPUK.MATERIAL_CODE = MATERIAL.MATERIAL_CODE
					AND MATERIAL.DELETE_USER IS NULL
				WHERE MATERIAL.COA_CODE = '5101020300' -- UNTUK PUPUK  TUNGGAL
				UNION ALL	
				-- QTY KG PUPUK MAJEMUK - TM				
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						RKT.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						RKT.QTY_MAJEMUK_JAN AS QTY_JAN,
						RKT.QTY_MAJEMUK_FEB AS QTY_FEB,
						RKT.QTY_MAJEMUK_MAR AS QTY_MAR,
						RKT.QTY_MAJEMUK_APR AS QTY_APR,
						RKT.QTY_MAJEMUK_MAY AS QTY_MAY,
						RKT.QTY_MAJEMUK_JUN AS QTY_JUN,
						RKT.QTY_MAJEMUK_JUL AS QTY_JUL,
						RKT.QTY_MAJEMUK_AUG AS QTY_AUG,
						RKT.QTY_MAJEMUK_SEP AS QTY_SEP,
						RKT.QTY_MAJEMUK_OCT AS QTY_OCT,
						RKT.QTY_MAJEMUK_NOV AS QTY_NOV,
						RKT.QTY_MAJEMUK_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM V_KG_PUPUK_PER_MATERIAL_AFD RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.MATURITY_STAGE = 'TM' 
					AND RKT.COA_CODE = '5101020400' -- UNTUK PUPUK MAJEMUK
					$where					
				UNION ALL	
				-- QTY KG PUPUK TUNGGAL - TM
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						'MATERIAL' AS COST_ELEMENT,
						RKT.MATERIAL_NAME AS  KETERANGAN,
						'KG' AS UOM,
						RKT.QTY_TUNGGAL_JAN AS QTY_JAN,
						RKT.QTY_TUNGGAL_FEB AS QTY_FEB,
						RKT.QTY_TUNGGAL_MAR AS QTY_MAR,
						RKT.QTY_TUNGGAL_APR AS QTY_APR,
						RKT.QTY_TUNGGAL_MAY AS QTY_MAY,
						RKT.QTY_TUNGGAL_JUN AS QTY_JUN,
						RKT.QTY_TUNGGAL_JUL AS QTY_JUL,
						RKT.QTY_TUNGGAL_AUG AS QTY_AUG,
						RKT.QTY_TUNGGAL_SEP AS QTY_SEP,
						RKT.QTY_TUNGGAL_OCT AS QTY_OCT,
						RKT.QTY_TUNGGAL_NOV AS QTY_NOV,
						RKT.QTY_TUNGGAL_DEC AS QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM V_KG_PUPUK_PER_MATERIAL_AFD RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.MATURITY_STAGE = 'TM' 
					AND RKT.COA_CODE = '5101020300' -- UNTUK PUPUK TUNGGAL
					$where
				UNION ALL	
				-- RKT PUPUK TUNGGAL SELAIN COST ELEMENT MATERIAL
				SELECT 	COST.PERIOD_BUDGET,
						COST.REGION_CODE,
						COST.BA_CODE,
						COST.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020300' AS ACTIVITY_CODE,
						'PUPUK TUNGGAL' AS ACTIVITY_DESC,
						COST.COST_ELEMENT,
						'' AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JAN / KG_PUPUK.QTY_TOTAL_JAN * COST.DIS_COST_JAN)
							ELSE 0
						END AS COST_JAN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_FEB / KG_PUPUK.QTY_TOTAL_FEB * COST.DIS_COST_FEB)
							ELSE 0
						END AS COST_FEB,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_MAR / KG_PUPUK.QTY_TOTAL_MAR * COST.DIS_COST_MAR)
							ELSE 0
						END AS COST_MAR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_APR / KG_PUPUK.QTY_TOTAL_APR * COST.DIS_COST_APR)
							ELSE 0
						END AS COST_APR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_MAY / KG_PUPUK.QTY_TOTAL_MAY * COST.DIS_COST_MAY)
							ELSE 0
						END AS COST_MAY,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JUN / KG_PUPUK.QTY_TOTAL_JUN * COST.DIS_COST_JUN)
							ELSE 0
						END AS COST_JUN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_JUL / KG_PUPUK.QTY_TOTAL_JUL * COST.DIS_COST_JUL)
							ELSE 0
						END AS COST_JUL,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_AUG / KG_PUPUK.QTY_TOTAL_AUG * COST.DIS_COST_AUG)
							ELSE 0
						END AS COST_AUG,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_SEP / KG_PUPUK.QTY_TOTAL_SEP * COST.DIS_COST_SEP)
							ELSE 0
						END AS COST_SEP,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_OCT / KG_PUPUK.QTY_TOTAL_OCT * COST.DIS_COST_OCT)
							ELSE 0
						END AS COST_OCT,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_NOV / KG_PUPUK.QTY_TOTAL_NOV * COST.DIS_COST_NOV)
							ELSE 0
						END AS COST_NOV,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
							THEN (KG_PUPUK.QTY_TUNGGAL_DEC / KG_PUPUK.QTY_TOTAL_DEC * COST.DIS_COST_DEC)
							ELSE 0
						END AS COST_DEC,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS1,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS2
				FROM (  
					SELECT 	PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							AFD_CODE,
							COST_ELEMENT,
							MATURITY_STAGE,
							SUM (DIS_COST_JAN) DIS_COST_JAN,
							SUM (DIS_COST_FEB) DIS_COST_FEB,
							SUM (DIS_COST_MAR) DIS_COST_MAR,
							SUM (DIS_COST_APR) DIS_COST_APR,
							SUM (DIS_COST_MAY) DIS_COST_MAY,
							SUM (DIS_COST_JUN) DIS_COST_JUN,
							SUM (DIS_COST_JUL) DIS_COST_JUL,
							SUM (DIS_COST_AUG) DIS_COST_AUG,
							SUM (DIS_COST_SEP) DIS_COST_SEP,
							SUM (DIS_COST_OCT) DIS_COST_OCT,
							SUM (DIS_COST_NOV) DIS_COST_NOV,
							SUM (DIS_COST_DEC) DIS_COST_DEC,
							MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
							MAX (COST_TOOLS_KG) COST_TOOLS_KG,
							MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
					FROM (  
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.AFD_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
								SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
								SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
								SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
								SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
								SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
								SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
								0 DIS_COST_JUL,
								0 DIS_COST_AUG,
								0 DIS_COST_SEP,
								0 DIS_COST_OCT,
								0 DIS_COST_NOV,
								0 DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS1 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.AFD_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS1
						UNION ALL
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.AFD_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS2 AS MATURITY_STAGE,
								0 DIS_COST_JAN,
								0 DIS_COST_FEB,
								0 DIS_COST_MAR,
								0 DIS_COST_APR,
								0 DIS_COST_MAY,
								0 DIS_COST_JUN,
								SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
								SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
								SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
								SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
								SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
								SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS2 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.AFD_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS2
					)
					GROUP BY PERIOD_BUDGET,
						REGION_CODE,
						BA_CODE,
						AFD_CODE,
						COST_ELEMENT,
						MATURITY_STAGE
				) COST
				LEFT JOIN V_KG_PUPUK_AFD KG_PUPUK
					ON COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
					AND COST.BA_CODE = KG_PUPUK.BA_CODE
					AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
					AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
				UNION ALL	
				-- RKT PUPUK MAJEMUK SELAIN COST ELEMENT MATERIAL
				SELECT 	COST.PERIOD_BUDGET,
						COST.REGION_CODE,
						COST.BA_CODE,
						COST.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101020400' AS ACTIVITY_CODE,
						'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						COST.COST_ELEMENT,
						'' AS  KETERANGAN,
						'KG' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JAN / KG_PUPUK.QTY_TOTAL_JAN * COST.DIS_COST_JAN)
							ELSE 0
						END AS COST_JAN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_FEB / KG_PUPUK.QTY_TOTAL_FEB * COST.DIS_COST_FEB)
							ELSE 0
						END AS COST_FEB,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_MAR / KG_PUPUK.QTY_TOTAL_MAR * COST.DIS_COST_MAR)
							ELSE 0
						END AS COST_MAR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_APR / KG_PUPUK.QTY_TOTAL_APR * COST.DIS_COST_APR)
							ELSE 0
						END AS COST_APR,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_MAY / KG_PUPUK.QTY_TOTAL_MAY * COST.DIS_COST_MAY)
							ELSE 0
						END AS COST_MAY,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JUN / KG_PUPUK.QTY_TOTAL_JUN * COST.DIS_COST_JUN)
							ELSE 0
						END AS COST_JUN,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_JUL / KG_PUPUK.QTY_TOTAL_JUL * COST.DIS_COST_JUL)
							ELSE 0
						END AS COST_JUL,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_AUG / KG_PUPUK.QTY_TOTAL_AUG * COST.DIS_COST_AUG)
							ELSE 0
						END AS COST_AUG,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_SEP / KG_PUPUK.QTY_TOTAL_SEP * COST.DIS_COST_SEP)
							ELSE 0
						END AS COST_SEP,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_OCT / KG_PUPUK.QTY_TOTAL_OCT * COST.DIS_COST_OCT)
							ELSE 0
						END AS COST_OCT,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_NOV / KG_PUPUK.QTY_TOTAL_NOV * COST.DIS_COST_NOV)
							ELSE 0
						END AS COST_NOV,
						CASE
							WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
							THEN (KG_PUPUK.QTY_MAJEMUK_DEC / KG_PUPUK.QTY_TOTAL_DEC * COST.DIS_COST_DEC)
							ELSE 0
						END AS COST_DEC,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS1,
						CASE
							WHEN COST.COST_ELEMENT = 'LABOUR'
							THEN COST.COST_LABOUR_POKOK
							WHEN COST.COST_ELEMENT = 'TOOLS'
							THEN COST.COST_TOOLS_KG
							WHEN COST.COST_ELEMENT = 'TRANSPORT'
							THEN COST.COST_TRANSPORT_KG
							ELSE 0
						END AS RP_ROTASI_SMS2
				FROM (  
					SELECT 	PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							AFD_CODE,
							COST_ELEMENT,
							MATURITY_STAGE,
							SUM (DIS_COST_JAN) DIS_COST_JAN,
							SUM (DIS_COST_FEB) DIS_COST_FEB,
							SUM (DIS_COST_MAR) DIS_COST_MAR,
							SUM (DIS_COST_APR) DIS_COST_APR,
							SUM (DIS_COST_MAY) DIS_COST_MAY,
							SUM (DIS_COST_JUN) DIS_COST_JUN,
							SUM (DIS_COST_JUL) DIS_COST_JUL,
							SUM (DIS_COST_AUG) DIS_COST_AUG,
							SUM (DIS_COST_SEP) DIS_COST_SEP,
							SUM (DIS_COST_OCT) DIS_COST_OCT,
							SUM (DIS_COST_NOV) DIS_COST_NOV,
							SUM (DIS_COST_DEC) DIS_COST_DEC,
							MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
							MAX (COST_TOOLS_KG) COST_TOOLS_KG,
							MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
					FROM (  
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.AFD_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
								SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
								SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
								SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
								SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
								SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
								SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
								0 DIS_COST_JUL,
								0 DIS_COST_AUG,
								0 DIS_COST_SEP,
								0 DIS_COST_OCT,
								0 DIS_COST_NOV,
								0 DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS1 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.AFD_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS1
						UNION ALL
						SELECT 	RKT.PERIOD_BUDGET,
								ORG.REGION_CODE,
								RKT.BA_CODE,
								RKT.AFD_CODE,
								RKT.COST_ELEMENT,
								RKT.MATURITY_STAGE_SMS2 AS MATURITY_STAGE,
								0 DIS_COST_JAN,
								0 DIS_COST_FEB,
								0 DIS_COST_MAR,
								0 DIS_COST_APR,
								0 DIS_COST_MAY,
								0 DIS_COST_JUN,
								SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
								SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
								SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
								SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
								SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
								SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
								MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
								MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
								MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
						FROM TR_RKT_PUPUK_COST_ELEMENT RKT
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = RKT.BA_CODE
						WHERE RKT.DELETE_USER IS NULL
							AND RKT.MATURITY_STAGE_SMS2 = 'TM'
							AND RKT.COST_ELEMENT <> 'MATERIAL'
							$where
						GROUP BY RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.AFD_CODE,
							RKT.COST_ELEMENT,
							RKT.MATURITY_STAGE_SMS2
					)
					GROUP BY PERIOD_BUDGET,
						REGION_CODE,
						BA_CODE,
						AFD_CODE,
						COST_ELEMENT,
						MATURITY_STAGE
				) COST
				LEFT JOIN V_KG_PUPUK_AFD KG_PUPUK
					ON COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
					AND COST.BA_CODE = KG_PUPUK.BA_CODE
					AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
					AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
				UNION ALL					
				-- PANEN - BIAYA PEMANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030101' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'BIAYA PEMANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JAN,
						(SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_FEB,
						(SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAR,
						(SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_APR,
						(SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAY,
						(SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUN,
						(SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUL,
						(SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_AUG,
						(SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_SEP,
						(SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_OCT,
						(SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_NOV,
						(SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
						SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
							JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (
						SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                norma.BA_CODE BA_CODE,
                                norma.AFD_CODE AFD_CODE,
                                sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                   sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                  sum((norma.JAN + norma.FEB+ norma.MAR +
                               norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                               norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                            FROM TR_PRODUKSI_PERIODE_BUDGET norma
                            LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                AND norma.BA_CODE = thn_berjalan.BA_CODE
                                AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                            WHERE norma.DELETE_USER IS NULL
                                $where1
                            group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - PREMI PANEN JANJANG
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030201' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'PREMI PANEN JANJANG' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						COST_JAN,
                        (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_FEB,
                        (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_MAR,
                        (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_APR,
                        (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_MAY,
                        (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_JUN,
                        (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_JUL,
                        (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_AUG,
                        (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_SEP,
                        (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_OCT,
                        (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_NOV,
                        (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
						SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
							JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (
						SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                norma.BA_CODE BA_CODE,
                                norma.AFD_CODE AFD_CODE,
                                sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                   sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                  sum((norma.JAN + norma.FEB+ norma.MAR +
                               norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                               norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                            FROM TR_PRODUKSI_PERIODE_BUDGET norma
                            LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                AND norma.BA_CODE = thn_berjalan.BA_CODE
                                AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                            WHERE norma.DELETE_USER IS NULL
                                $where1
                            group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL
					-- PANEN - PREMI PANEN BRD
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030301' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'PREMI PANEN BRD' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JAN,
                        (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_FEB,
                        (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAR,
                        (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_APR,
                        (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAY,
                        (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUN,
                        (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUL,
                        (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_AUG,
                        (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_SEP,
                        (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_OCT,
                        (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_NOV,
                        (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - KRANI BUAH
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030701' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'KRANI BUAH' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.KRANI_BUAH_TOTAL) AS COST_JAN,
						(SEBARAN.FEB * RKT.KRANI_BUAH_TOTAL) AS COST_FEB,
						(SEBARAN.MAR * RKT.KRANI_BUAH_TOTAL) AS COST_MAR,
						(SEBARAN.APR * RKT.KRANI_BUAH_TOTAL) AS COST_APR,
						(SEBARAN.MAY * RKT.KRANI_BUAH_TOTAL) AS COST_MAY,
						(SEBARAN.JUN * RKT.KRANI_BUAH_TOTAL) AS COST_JUN,
						(SEBARAN.JUL * RKT.KRANI_BUAH_TOTAL) AS COST_JUL,
						(SEBARAN.AUG * RKT.KRANI_BUAH_TOTAL) AS COST_AUG,
						(SEBARAN.SEP * RKT.KRANI_BUAH_TOTAL) AS COST_SEP,
						(SEBARAN.OCT * RKT.KRANI_BUAH_TOTAL) AS COST_OCT,
						(SEBARAN.NOV * RKT.KRANI_BUAH_TOTAL) AS COST_NOV,
						(SEBARAN.DEC * RKT.KRANI_BUAH_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ALAT PANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030103' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'ALAT PANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JAN,
						(SEBARAN.FEB * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_FEB,
						(SEBARAN.MAR * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_MAR,
						(SEBARAN.APR * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_APR,
						(SEBARAN.MAY * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_MAY,
						(SEBARAN.JUN * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JUN,
						(SEBARAN.JUL * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_JUL,
						(SEBARAN.AUG * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_AUG,
						(SEBARAN.SEP * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_SEP,
						(SEBARAN.OCT * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_OCT,
						(SEBARAN.NOV * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_NOV,
						(SEBARAN.DEC * RKT.BIAYA_ALAT_PANEN_RP_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - SUPERVISI PEMANEN
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030701' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'SUPERVISI PEMANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JAN,
						(SEBARAN.FEB * RKT.BIAYA_SPV_RP_TOTAL) AS COST_FEB,
						(SEBARAN.MAR * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAR,
						(SEBARAN.APR * RKT.BIAYA_SPV_RP_TOTAL) AS COST_APR,
						(SEBARAN.MAY * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAY,
						(SEBARAN.JUN * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUN,
						(SEBARAN.JUL * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUL,
						(SEBARAN.AUG * RKT.BIAYA_SPV_RP_TOTAL) AS COST_AUG,
						(SEBARAN.SEP * RKT.BIAYA_SPV_RP_TOTAL) AS COST_SEP,
						(SEBARAN.OCT * RKT.BIAYA_SPV_RP_TOTAL) AS COST_OCT,
						(SEBARAN.NOV * RKT.BIAYA_SPV_RP_TOTAL) AS COST_NOV,
						(SEBARAN.DEC * RKT.BIAYA_SPV_RP_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - PREMI SUPIR
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-3' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'PREMI SUPIR' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.SUPIR_PREMI) AS COST_JAN,
						(SEBARAN.FEB * RKT.SUPIR_PREMI) AS COST_FEB,
						(SEBARAN.MAR * RKT.SUPIR_PREMI) AS COST_MAR,
						(SEBARAN.APR * RKT.SUPIR_PREMI) AS COST_APR,
						(SEBARAN.MAY * RKT.SUPIR_PREMI) AS COST_MAY,
						(SEBARAN.JUN * RKT.SUPIR_PREMI) AS COST_JUN,
						(SEBARAN.JUL * RKT.SUPIR_PREMI) AS COST_JUL,
						(SEBARAN.AUG * RKT.SUPIR_PREMI) AS COST_AUG,
						(SEBARAN.SEP * RKT.SUPIR_PREMI) AS COST_SEP,
						(SEBARAN.OCT * RKT.SUPIR_PREMI) AS COST_OCT,
						(SEBARAN.NOV * RKT.SUPIR_PREMI) AS COST_NOV,
						(SEBARAN.DEC * RKT.SUPIR_PREMI) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ANGKUT - LANGSIR
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030404-2' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'LANGSIR' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.LANGSIR_RP) AS COST_JAN,
						(SEBARAN.FEB * RKT.LANGSIR_RP) AS COST_FEB,
						(SEBARAN.MAR * RKT.LANGSIR_RP) AS COST_MAR,
						(SEBARAN.APR * RKT.LANGSIR_RP) AS COST_APR,
						(SEBARAN.MAY * RKT.LANGSIR_RP) AS COST_MAY,
						(SEBARAN.JUN * RKT.LANGSIR_RP) AS COST_JUN,
						(SEBARAN.JUL * RKT.LANGSIR_RP) AS COST_JUL,
						(SEBARAN.AUG * RKT.LANGSIR_RP) AS COST_AUG,
						(SEBARAN.SEP * RKT.LANGSIR_RP) AS COST_SEP,
						(SEBARAN.OCT * RKT.LANGSIR_RP) AS COST_OCT,
						(SEBARAN.NOV * RKT.LANGSIR_RP) AS COST_NOV,
						(SEBARAN.DEC * RKT.LANGSIR_RP) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - TRANSPORT TBS - BONGKAR MUAT
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030404-1' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'BONGKAR MUAT' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.TUKANG_MUAT_TOTAL) AS COST_JAN,
						(SEBARAN.FEB * RKT.TUKANG_MUAT_TOTAL) AS COST_FEB,
						(SEBARAN.MAR * RKT.TUKANG_MUAT_TOTAL) AS COST_MAR,
						(SEBARAN.APR * RKT.TUKANG_MUAT_TOTAL) AS COST_APR,
						(SEBARAN.MAY * RKT.TUKANG_MUAT_TOTAL) AS COST_MAY,
						(SEBARAN.JUN * RKT.TUKANG_MUAT_TOTAL) AS COST_JUN,
						(SEBARAN.JUL * RKT.TUKANG_MUAT_TOTAL) AS COST_JUL,
						(SEBARAN.AUG * RKT.TUKANG_MUAT_TOTAL) AS COST_AUG,
						(SEBARAN.SEP * RKT.TUKANG_MUAT_TOTAL) AS COST_SEP,
						(SEBARAN.OCT * RKT.TUKANG_MUAT_TOTAL) AS COST_OCT,
						(SEBARAN.NOV * RKT.TUKANG_MUAT_TOTAL) AS COST_NOV,
						(SEBARAN.DEC * RKT.TUKANG_MUAT_TOTAL) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL	
				-- PANEN - ANGKUT - TRANSPORT TBS INTERNAL
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-2' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
						'TRANSPORT TBS INTERNAL - PANEN' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
						(SEBARAN.FEB * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
						(SEBARAN.MAR * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
						(SEBARAN.APR * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
						(SEBARAN.MAY * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
						(SEBARAN.JUN * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
						(SEBARAN.JUL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
						(SEBARAN.AUG * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
						(SEBARAN.SEP * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
						(SEBARAN.OCT * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
						(SEBARAN.NOV * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
						(SEBARAN.DEC * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.SUMBER_BIAYA_UNIT = 'INTERNAL'
					$where
				UNION ALL	
				-- PANEN - ANGKUT - TRANSPORT TBS EKSTERNAL
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030605' AS ACTIVITY_CODE, -- BY ADI 30/08/2014
						'TRANSPORT TBS EKSTERNAL' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
						(SEBARAN.FEB * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
						(SEBARAN.MAR * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
						(SEBARAN.APR * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
						(SEBARAN.MAY * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
						(SEBARAN.JUN * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
						(SEBARAN.JUL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
						(SEBARAN.AUG * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
						(SEBARAN.SEP * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
						(SEBARAN.OCT * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
						(SEBARAN.NOV * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
						(SEBARAN.DEC * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PANEN RKT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET and sebaran.BA_CODE = RKT.BA_CODE and sebaran.AFD_CODE = RKT.AFD_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.SUMBER_BIAYA_UNIT = 'EXTERNAL'
					$where
				UNION ALL	
				-- PANEN : PENGANGKUTAN TBS INTERNAL (COA : 5101030504)
				SELECT 	ANGKUT.PERIOD_BUDGET,
						ANGKUT.REGION_CODE,
						ANGKUT.BA_CODE,
						ANGKUT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						'5101030504-1' AS ACTIVITY_CODE,  -- BY ADI 16/11/2014
						'PENGANGKUTAN INTERNAL TBS - VRA' AS ACTIVITY_DETAIL,
						'' AS COST_ELEMENT,
						'' KETERANGAN,
						'' AS UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN.JAN * ANGKUT.TOTAL) COST_JAN,
						(SEBARAN.FEB * ANGKUT.TOTAL) COST_FEB,
						(SEBARAN.MAR * ANGKUT.TOTAL) COST_MAR,
						(SEBARAN.APR * ANGKUT.TOTAL) COST_APR,
						(SEBARAN.MAY * ANGKUT.TOTAL) COST_MAY,
						(SEBARAN.JUN * ANGKUT.TOTAL) COST_JUN,
						(SEBARAN.JUL * ANGKUT.TOTAL) COST_JUL,
						(SEBARAN.AUG * ANGKUT.TOTAL) COST_AUG,
						(SEBARAN.SEP * ANGKUT.TOTAL) COST_SEP,
						(SEBARAN.OCT * ANGKUT.TOTAL) COST_OCT,
						(SEBARAN.NOV * ANGKUT.TOTAL) COST_NOV,
						(SEBARAN.DEC * ANGKUT.TOTAL) COST_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT 	RKT.PERIOD_BUDGET,
							ORG.REGION_CODE,
							RKT.BA_CODE,
							RKT.AFD_CODE,
							(NVL (RKT.COST_JAN, 0) + NVL (RKT.COST_FEB, 0) + NVL (RKT.COST_MAR, 0)
							 + NVL (RKT.COST_APR, 0) + NVL (RKT.COST_MAY, 0) + NVL (RKT.COST_JUN, 0)
							 + NVL (RKT.COST_JUL, 0) + NVL (RKT.COST_AUG, 0) + NVL (RKT.COST_SEP, 0)
							 + NVL (RKT.COST_OCT, 0) + NVL (RKT.COST_NOV, 0) + NVL (RKT.COST_DEC, 0)) AS TOTAL
					FROM (
						SELECT 	SEBARAN_HA.PERIOD_BUDGET,
								SEBARAN_HA.BA_CODE,
								SEBARAN_HA.AFD_CODE,
								RKT.REPORT_TYPE,
								RKT.COA_CODE,
								RKT.COA_DESC,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JAN) COST_JAN,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_FEB) COST_FEB,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_MAR) COST_MAR,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_APR) COST_APR,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_MAY) COST_MAY,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JUN) COST_JUN,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_JUL) COST_JUL,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_AUG) COST_AUG,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_SEP) COST_SEP,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_OCT) COST_OCT,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_NOV) COST_NOV,
								(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.COST_DEC) COST_DEC
						FROM (
							SELECT SUMHA_AFD.PERIOD_BUDGET,
										SUMHA_AFD.BA_CODE,
										SUMHA_AFD.AFD_CODE,
										SUM(SUMHA_AFD.TOTAL_HA_SMS1) as HA_AFD,
										SUMHA_BA.HA_BA
							FROM V_REPORT_SEBARAN_HS SUMHA_AFD
							LEFT JOIN (
								SELECT PERIOD_BUDGET,
											BA_CODE,
											SUM(TOTAL_HA_SMS1) HA_BA
								FROM V_REPORT_SEBARAN_HS 
								GROUP BY PERIOD_BUDGET,
											BA_CODE
							) SUMHA_BA
								ON SUMHA_BA.PERIOD_BUDGET = SUMHA_AFD.PERIOD_BUDGET
								AND SUMHA_BA.BA_CODE = SUMHA_AFD.BA_CODE
							GROUP BY SUMHA_AFD.PERIOD_BUDGET,
										SUMHA_AFD.BA_CODE,
										SUMHA_AFD.AFD_CODE,
										SUMHA_BA.HA_BA
						) SEBARAN_HA
						LEFT JOIN V_TOTAL_RELATION_COST RKT
							ON SEBARAN_HA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND SEBARAN_HA.BA_CODE = RKT.BA_CODE
					) RKT
					LEFT JOIN TM_ORGANIZATION ORG
						ON ORG.BA_CODE = RKT.BA_CODE
					WHERE RKT.COA_CODE = '5101030504'
						$where
				) ANGKUT
				LEFT JOIN (
                        SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR, APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN, 
                                JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP, OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC, TOTAL FROM (          
                            SELECT norma.PERIOD_BUDGET PERIOD_BUDGET,
                                    norma.BA_CODE BA_CODE,
                                    norma.AFD_CODE AFD_CODE,
                                    sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                                       sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                                      sum((norma.JAN + norma.FEB+ norma.MAR +
                                   norma.APR+ norma.MAY+  norma.JUN+ norma.JUL+  
                                   norma.AUG+ norma.SEP+  norma.OCT+ norma.NOV+  norma.DEC)) total
                                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                                WHERE norma.DELETE_USER IS NULL
                                    $where1
                                group by norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE )
                    )  SEBARAN on sebaran.PERIOD_BUDGET = ANGKUT.PERIOD_BUDGET and sebaran.BA_CODE = ANGKUT.BA_CODE and sebaran.AFD_CODE = ANGKUT.AFD_CODE
				UNION ALL
				-- ALOKASI CHECKROLL
				SELECT 	SEBARAN_HA.PERIOD_BUDGET,
						ORG.REGION_CODE,
						SEBARAN_HA.BA_CODE,
						SEBARAN_HA.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.TUNJANGAN_TYPE AS ACTIVITY_CODE,
						RKT.TUNJANGAN_TYPE AS ACTIVITY_DESC,
						'LABOUR' AS COST_ELEMENT,
						'' KETERANGAN,
						'' UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_JAN) DIS_JAN,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_FEB) DIS_FEB,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_MAR) DIS_MAR,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_APR) DIS_APR,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_MAY) DIS_MAY,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_JUN) DIS_JUN,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_JUL) DIS_JUL,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_AUG) DIS_AUG,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_SEP) DIS_SEP,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_OCT) DIS_OCT,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_NOV) DIS_NOV,
						(SEBARAN_HA.HA_AFD / SEBARAN_HA.HA_BA * RKT.DIS_DEC) DIS_DEC,
						0 RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM (
					SELECT 	SUMHA_AFD.PERIOD_BUDGET,
							SUMHA_AFD.BA_CODE,
							SUMHA_AFD.AFD_CODE,
							SUM(SUMHA_AFD.TOTAL_HA_SMS1) as HA_AFD,
							SUMHA_BA.HA_BA
					FROM V_REPORT_SEBARAN_HS SUMHA_AFD
					LEFT JOIN (
						SELECT 	PERIOD_BUDGET,
								BA_CODE,
								SUM(TOTAL_HA_SMS1) HA_BA
						FROM V_REPORT_SEBARAN_HS 
						GROUP BY PERIOD_BUDGET, BA_CODE
					) SUMHA_BA
						ON SUMHA_BA.PERIOD_BUDGET = SUMHA_AFD.PERIOD_BUDGET
						AND SUMHA_BA.BA_CODE = SUMHA_AFD.BA_CODE
					GROUP BY SUMHA_AFD.PERIOD_BUDGET,
						SUMHA_AFD.BA_CODE,
						SUMHA_AFD.AFD_CODE,
						SUMHA_BA.HA_BA
				) SEBARAN_HA
				LEFT JOIN TR_RPT_DISTRIBUSI_COA RKT
					ON SEBARAN_HA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					AND SEBARAN_HA.BA_CODE = RKT.BA_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.REPORT_TYPE = 'CR_ALOKASI'
					AND RKT.DELETE_USER IS NULL
					AND RKT.MATURITY_STAGE = 'TM'
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 1 = TM & JENIS = PERULANGAN (DIKATEGORIKAN SBG BIAYA RAWAT TM)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						RKT.PLAN_JAN QTY_JAN,
						RKT.PLAN_FEB QTY_FEB,
						RKT.PLAN_MAR QTY_MAR,
						RKT.PLAN_APR QTY_APR,
						RKT.PLAN_MAY QTY_MAY,
						RKT.PLAN_JUN QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.DIS_JAN COST_JAN,
						RKT.DIS_FEB COST_FEB,
						RKT.DIS_MAR COST_MAR,
						RKT.DIS_APR COST_APR,
						RKT.DIS_MAY COST_MAY,
						RKT.DIS_JUN COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC,
						RKT.TOTAL_RP_QTY RP_ROTASI_SMS1,
						0 RP_ROTASI_SMS2
				FROM TR_RKT_PK_COST_ELEMENT RKT
				LEFT JOIN TR_RKT_PK RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN'
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 2 = TM & JENIS = PERULANGAN (DIKATEGORIKAN SBG BIAYA RAWAT TM)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.AFD_CODE,
						'TM' AS TIPE_TRANSAKSI,
						RKT.ACTIVITY_CODE,
						ACT.DESCRIPTION AS ACTIVITY_DESC,
						RKT.COST_ELEMENT,
						'' KETERANGAN,
						ACT.UOM,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.PLAN_JUL QTY_JUL,
						RKT.PLAN_AUG QTY_AUG,
						RKT.PLAN_SEP QTY_SEP,
						RKT.PLAN_OCT QTY_OCT,
						RKT.PLAN_NOV QTY_NOV,
						RKT.PLAN_DEC QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						RKT.DIS_JUL COST_JUL,
						RKT.DIS_AUG COST_AUG,
						RKT.DIS_SEP COST_SEP,
						RKT.DIS_OCT COST_OCT,
						RKT.DIS_NOV COST_NOV,
						RKT.DIS_DEC COST_DEC,
						0 RP_ROTASI_SMS1,
						RKT.TOTAL_RP_QTY RP_ROTASI_SMS2
				FROM TR_RKT_PK_COST_ELEMENT RKT
				LEFT JOIN TR_RKT_PK RKT_INDUK
					ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT_INDUK.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN'
					$where
			) REPORT
			GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				TIPE_TRANSAKSI,
				ACTIVITY_CODE,
				ACTIVITY_DESC,
				COST_ELEMENT,
				KETERANGAN,
				UOM
		";
		//die($query);
		$this->_db->query($query);
		$this->_db->commit();
		
		return true;
	}
	
	//hapus temp table untuk estate cost
	public function delTmpRptEstCost($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		//hapus estate cost per BA
		$query = "
			DELETE FROM TMP_RPT_EST_COST 
			WHERE 1 = 1
			$where 
		";
		$this->_db->query($query);
		$this->_db->commit();
		
		//hapus estate cost per afd
		$query = "
			DELETE FROM TMP_RPT_EST_COST_AFD 
			WHERE 1 = 1
			$where 
		";
		//die($query);
		$this->_db->query($query);
		$this->_db->commit();
		
		return true;
	}
	
	//generate report estate cost
    public function reportEstateCost($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel estate cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_EST_COST ALL_ACT
				WHERE 1 = 1
				$where
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			LEFT JOIN TM_ORGANIZATION ORG
				ON ORG.BA_CODE = REPORT.BA_CODE
			WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel estate cost ################################################### */
		
		return $result;
	}
	
	//query summary estate cost per BA
    public function querySummaryEstateCostPerBa($params = array())
    {
		$where = $select_group = $order_group = "";
		
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					CASE WHEN --TAMBAHAN ARIES NAMBAH NORMA BIAYA/BORONGAN 04-06-2015
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) IS NULL
                    THEN    
                        (SELECT PRICE FROM TN_HARGA_BORONG 
                        WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                        AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                        AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) 
                    ELSE
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET)
                    END
                    AS NORMA,
					STRUKTUR_REPORT.PERIOD_BUDGET, 
					STRUKTUR_REPORT.REGION_CODE, 
					STRUKTUR_REPORT.BA_CODE, 
					STRUKTUR_REPORT.ESTATE_NAME, 
					STRUKTUR_REPORT.ACTIVITY_CODE, 
					STRUKTUR_REPORT.ACTIVITY_DESC, 
					STRUKTUR_REPORT.UOM, 
					STRUKTUR_REPORT.QTY_SMS1, 
					STRUKTUR_REPORT.QTY_SMS2, 
					STRUKTUR_REPORT.COST_SMS1, 
					STRUKTUR_REPORT.COST_SMS2, 
					STRUKTUR_REPORT.COST_SETAHUN,
					STRUKTUR_REPORT.TOTAL_SEBARAN_KG,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS1 > 0
						THEN (STRUKTUR_REPORT.COST_SMS1 / STRUKTUR_REPORT.QTY_SMS1)
						ELSE 0
					END as RP_HA_SMS1,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN (STRUKTUR_REPORT.COST_SMS2 / STRUKTUR_REPORT.QTY_SMS2)
						ELSE 0
					END as RP_HA_SMS2,
					CASE
						WHEN STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN (STRUKTUR_REPORT.COST_SETAHUN / STRUKTUR_REPORT.QTY_SMS2)
						ELSE 0
					END as RP_HA_SETAHUN,
					CASE
						WHEN STRUKTUR_REPORT.TOTAL_SEBARAN_KG > 0
						THEN (STRUKTUR_REPORT.COST_SETAHUN / (STRUKTUR_REPORT.TOTAL_SEBARAN_KG * 1000))
						ELSE 0
					END as RP_KG
			FROM (
				SELECT 	$select_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM, 
						MAX(SMS1_TM) AS QTY_SMS1,
						MAX(SMS2_TM) AS QTY_SMS2,
						SUM (NVL(COST_SMS1,0)) as COST_SMS1, 
						SUM (NVL(COST_SMS2,0)) as COST_SMS2, 
						SUM (NVL(COST_SETAHUN,0)) as COST_SETAHUN,
						MAX (sebaran_prod.QTY_SETAHUN) TOTAL_SEBARAN_KG
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
							GROUP_CODE
					FROM (
						SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
								LVL, 
								TO_CHAR(GROUP_CODE) AS GROUP_CODE
						FROM (
							SELECT 	GROUP_CODE, 
									CONNECT_BY_ISCYCLE \"CYCLE\",
									LEVEL as LVL, 
									SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
							FROM TM_RPT_MAPPING_ACT
							WHERE level > 1
							START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
							CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
						)
						GROUP BY HIRARKI, LVL, GROUP_CODE
						ORDER BY HIRARKI
					)
				) STRUKTUR_REPORT
				LEFT JOIN TM_RPT_MAPPING_ACT MAPP
					ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
				LEFT JOIN (
					SELECT *
					FROM TMP_RPT_EST_COST ALL_ACT
					WHERE 1 = 1 
					$where
				)REPORT
					ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
					AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
					AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = REPORT.BA_CODE
				LEFT JOIN (
					SELECT 	PERIOD_BUDGET, 
							BA_CODE, 
							SUM(SMS1_TM) SMS1_TM, 
							SUM(SMS2_TM) SMS2_TM
					FROM V_REPORT_SEBARAN_HS
					GROUP BY PERIOD_BUDGET, BA_CODE
				) TOTAL_SEBARAN_HA
					ON TOTAL_SEBARAN_HA.PERIOD_BUDGET = REPORT.PERIOD_BUDGET
					AND TOTAL_SEBARAN_HA.BA_CODE = REPORT.BA_CODE
				LEFT JOIN V_REPORT_SEBARAN_PRODUKSI sebaran_prod
					ON REPORT.PERIOD_BUDGET = sebaran_prod.PERIOD_BUDGET
					AND REPORT.BA_CODE = sebaran_prod.BA_CODE
					AND sebaran_prod.TIPE_TRANSAKSI = '03_TBS_PANEN'
				WHERE REPORT.ACTIVITY_CODE IS NOT NULL
				GROUP BY $order_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM,
						REPORT.TIPE_TRANSAKSI
			) STRUKTUR_REPORT
			ORDER BY STRUKTUR_REPORT.PERIOD_BUDGET,
					 STRUKTUR_REPORT.BA_CODE,
					 $order_group
					 STRUKTUR_REPORT.ACTIVITY_CODE
		";
		
		return $query;
	}
	
	//query summary estate cost per AFD
    public function querySummaryEstateCostPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					CASE WHEN --TAMBAHAN ARIES NAMBAH NORMA BIAYA/BORONGAN 04-06-2015
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) IS NULL
                    THEN    
                        (SELECT PRICE FROM TN_HARGA_BORONG 
                        WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                        AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                        AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET) 
                    ELSE
                        (SELECT SUM(PRICE_ROTASI) FROM TN_BIAYA 
                            WHERE ACTIVITY_CODE = STRUKTUR_REPORT.ACTIVITY_CODE 
                            AND ACTIVITY_GROUP = STRUKTUR_REPORT.GROUP01_DESC
                            AND BA_CODE = STRUKTUR_REPORT.BA_CODE
                            AND PERIOD_BUDGET = STRUKTUR_REPORT.PERIOD_BUDGET)
                    END
                    AS NORMA,
					STRUKTUR_REPORT.PERIOD_BUDGET, 
					STRUKTUR_REPORT.REGION_CODE, 
					STRUKTUR_REPORT.BA_CODE, 
					STRUKTUR_REPORT.ESTATE_NAME, 
					STRUKTUR_REPORT.AFD_CODE, 
					STRUKTUR_REPORT.ACTIVITY_CODE, 
					STRUKTUR_REPORT.ACTIVITY_DESC, 
					STRUKTUR_REPORT.UOM, 
					STRUKTUR_REPORT.QTY_SMS1, 
					STRUKTUR_REPORT.QTY_SMS2, 
					STRUKTUR_REPORT.COST_SMS1, 
					STRUKTUR_REPORT.COST_SMS2, 
					STRUKTUR_REPORT.COST_SETAHUN,
					STRUKTUR_REPORT.TOTAL_SEBARAN_KG,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS1 > 0
						THEN (STRUKTUR_REPORT.COST_SMS1 / STRUKTUR_REPORT.QTY_SMS1)
						ELSE 0
					END as RP_HA_SMS1,
					CASE 
						WHEN STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN (STRUKTUR_REPORT.COST_SMS2 / STRUKTUR_REPORT.QTY_SMS2)
						ELSE 0
					END as RP_HA_SMS2,
					CASE
						WHEN STRUKTUR_REPORT.QTY_SMS2 > 0
						THEN (STRUKTUR_REPORT.COST_SETAHUN / STRUKTUR_REPORT.QTY_SMS2)
						ELSE 0
					END as RP_HA_SETAHUN,
					CASE
						WHEN STRUKTUR_REPORT.TOTAL_SEBARAN_KG > 0
						THEN (STRUKTUR_REPORT.COST_SETAHUN / (STRUKTUR_REPORT.TOTAL_SEBARAN_KG * 1000))
						ELSE 0
					END as RP_KG
			FROM (
				SELECT 	$select_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.AFD_CODE, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM, 
						MAX(SMS1_TM) AS QTY_SMS1,
						MAX(SMS2_TM) AS QTY_SMS2,
						SUM (NVL(COST_SMS1,0)) as COST_SMS1, 
						SUM (NVL(COST_SMS2,0)) as COST_SMS2, 
						SUM (NVL(COST_SETAHUN,0)) as COST_SETAHUN,
						MAX (sebaran_prod.QTY_SETAHUN) TOTAL_SEBARAN_KG
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
							GROUP_CODE
					FROM (
						SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
								LVL, 
								TO_CHAR(GROUP_CODE) AS GROUP_CODE
						FROM (
							SELECT 	GROUP_CODE, 
									CONNECT_BY_ISCYCLE \"CYCLE\",
									LEVEL as LVL, 
									SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
							FROM TM_RPT_MAPPING_ACT
							WHERE level > 1
							START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
							CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
						)
						GROUP BY HIRARKI, LVL, GROUP_CODE
						ORDER BY HIRARKI
					)
				) STRUKTUR_REPORT
				LEFT JOIN TM_RPT_MAPPING_ACT MAPP
					ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
				LEFT JOIN (
					SELECT *
					FROM TMP_RPT_EST_COST_AFD ALL_ACT
					WHERE 1 = 1
					$where
				)REPORT
					ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
					AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
					AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = REPORT.BA_CODE
				LEFT JOIN (
					SELECT 	PERIOD_BUDGET, 
							BA_CODE, 
							AFD_CODE,
							SMS1_TM, 
							SMS2_TM
					FROM V_REPORT_SEBARAN_HS
				) TOTAL_SEBARAN_HA
					ON TOTAL_SEBARAN_HA.PERIOD_BUDGET = REPORT.PERIOD_BUDGET
					AND TOTAL_SEBARAN_HA.BA_CODE = REPORT.BA_CODE
					AND TOTAL_SEBARAN_HA.AFD_CODE = REPORT.AFD_CODE
				LEFT JOIN (
					SELECT 	PERIOD_BUDGET,
							BA_CODE,
							AFD_CODE,
							NVL(SUM(JAN),0) QTY_JAN,
							NVL(SUM(FEB),0) QTY_FEB,
							NVL(SUM(MAR),0) QTY_MAR,
							NVL(SUM(APR),0) QTY_APR,
							NVL(SUM(MAY),0) QTY_MAY,
							NVL(SUM(JUN),0) QTY_JUN,
							NVL(SUM(JUL),0) QTY_JUL,
							NVL(SUM(AUG),0) QTY_AUG,
							NVL(SUM(SEP),0) QTY_SEP,
							NVL(SUM(OCT),0) QTY_OCT,
							NVL(SUM(NOV),0) QTY_NOV,
							NVL(SUM(DEC),0) QTY_DEC,
							(  NVL(SUM(JAN),0)
							 + NVL(SUM(FEB),0)
							 + NVL(SUM(MAR),0)
							 + NVL(SUM(APR),0)
							 + NVL(SUM(MAY),0)
							 + NVL(SUM(JUN),0)
							 + NVL(SUM(JUL),0)
							 + NVL(SUM(AUG),0)
							 + NVL(SUM(SEP),0)
							 + NVL(SUM(OCT),0)
							 + NVL(SUM(NOV),0)
							 + NVL(SUM(DEC),0)
							 ) AS QTY_SETAHUN
					FROM TR_PRODUKSI_PERIODE_BUDGET
					WHERE DELETE_USER IS NULL
					GROUP BY PERIOD_BUDGET,
							BA_CODE,
							AFD_CODE
				) sebaran_prod
					ON REPORT.PERIOD_BUDGET = sebaran_prod.PERIOD_BUDGET
					AND REPORT.BA_CODE = sebaran_prod.BA_CODE
					AND REPORT.AFD_CODE = sebaran_prod.AFD_CODE
				WHERE REPORT.ACTIVITY_CODE IS NOT NULL
				GROUP BY $order_group
						REPORT.PERIOD_BUDGET, 
						REPORT.REGION_CODE, 
						REPORT.BA_CODE, 
						ORG.ESTATE_NAME, 
						REPORT.AFD_CODE, 
						REPORT.ACTIVITY_CODE, 
						REPORT.ACTIVITY_DESC, 
						REPORT.UOM,
						REPORT.TIPE_TRANSAKSI
			) STRUKTUR_REPORT
			ORDER BY STRUKTUR_REPORT.PERIOD_BUDGET,
					 STRUKTUR_REPORT.BA_CODE,
					 STRUKTUR_REPORT.AFD_CODE, 
					 $order_group
					 STRUKTUR_REPORT.ACTIVITY_CODE
		";
		
		return $query;
	}
	
	//generate report summary estate cost
    public function reportSummaryEstateCost($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);
		
		/* ################################################### generate excel estate cost ################################################### */
		$query = $this->querySummaryEstateCostPerBa($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel summary estate cost ################################################### */
		
		return $result;
	}
	
	//generate module review estate cost per BA
    public function modReviewEstateCostPerBa($params = array())
    {
		$where = $select_group = $order_group = "";
		
		// ############################################# get all BA #############################################
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		$query = "
			SELECT REGION_NAME, BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
			$where
			ORDER BY REGION_NAME, BA_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['BA_CODE'] = array(); // distinct BA
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct BA_CODE
				if (in_array($row['BA_CODE'], $result['BA_CODE']) == false) {
					array_push($result['BA_CODE'], $row['BA_CODE']);
				}
				
				$result['REGION_NAME'] = $row['REGION_NAME'];
			}
		}
		
		// ############################################# get all group + activity #############################################
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		$query = "
			SELECT 	$select_group
					MAPP.ACTIVITY_CODE,
					REPORT.ACTIVITY_DESC
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_EST_COST ALL_ACT
				WHERE 1 = 1
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			WHERE MAPP.ACTIVITY_CODE IS NOT NULL
				AND REPORT.ACTIVITY_DESC IS NOT NULL
			ORDER BY $order_group
					 MAPP.ACTIVITY_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['GROUP_ACTIVITY'] = array(); // distinct GROUP - ACTIVITY_CODE
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct GROUP_ACTIVITY
				if (in_array($row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE'], $result['GROUP_ACTIVITY']) == false) {
					array_push($result['GROUP_ACTIVITY'], $row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']);
					
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP01_DESC'] = $row['GROUP01_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP02_DESC'] = $row['GROUP02_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_CODE'] = $row['ACTIVITY_CODE'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_DESC'] = $row['ACTIVITY_DESC'];
				}
			}
		}
		
		/* ################################################### generate excel module review estate cost ################################################### */
		$query = $this->querySummaryEstateCostPerBa($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['BA_CODE']]['rp_ha'] = $row['RP_HA_SETAHUN'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['BA_CODE']]['rp_kg'] = $row['RP_KG'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['BA_CODE']]['NORMA'] = $row['NORMA'];
				
				$result['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			}
		}		
		/* ################################################### generate excel module review estate cost ################################################### */
		
		return $result;
	}
	
	//generate module review estate cost per AFD
    public function modReviewEstateCostPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		
		// ############################################# get all AFD #############################################
		//filter period budget
		if ($params['budgetperiod'] != '') {
			$where .= "
                AND TO_CHAR(A.PERIOD_BUDGET, 'RRRR') = '".$params['budgetperiod']."'
            ";
        }
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND B.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND A.BA_CODE = '".$params['key_find']."'
            ";
        }
		$query = "
			SELECT *
			FROM (
				SELECT B.REGION_NAME, B.COMPANY_NAME, B.ESTATE_NAME, A.AFD_CODE
				FROM TM_HECTARE_STATEMENT A
				LEFT JOIN TM_ORGANIZATION B
					ON A.BA_CODE = B.BA_CODE
				WHERE A.DELETE_USER IS NULL
				$where
			)	
			GROUP BY REGION_NAME, COMPANY_NAME, ESTATE_NAME, AFD_CODE
			ORDER BY REGION_NAME, COMPANY_NAME, ESTATE_NAME, AFD_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['AFD_CODE'] = array(); // distinct BA
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct AFD_CODE
				if (in_array($row['AFD_CODE'], $result['AFD_CODE']) == false) {
					array_push($result['AFD_CODE'], $row['AFD_CODE']);
				}
				
				$result['REGION_NAME'] = $row['REGION_NAME'];
				$result['COMPANY_NAME'] = $row['COMPANY_NAME'];
				$result['ESTATE_NAME'] = $row['ESTATE_NAME'];
			}
		}
		
		// ############################################# get all group + activity #############################################
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		$query = "
			SELECT 	$select_group
					MAPP.ACTIVITY_CODE,
					REPORT.ACTIVITY_DESC
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_EST_COST ALL_ACT
				WHERE 1 = 1
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			WHERE MAPP.ACTIVITY_CODE IS NOT NULL
				AND REPORT.ACTIVITY_DESC IS NOT NULL
			ORDER BY $order_group
					 MAPP.ACTIVITY_CODE
		";
		
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['GROUP_ACTIVITY'] = array(); // distinct GROUP - ACTIVITY_CODE
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct GROUP_ACTIVITY
				if (in_array($row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE'], $result['GROUP_ACTIVITY']) == false) {
					array_push($result['GROUP_ACTIVITY'], $row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']);
					
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP01_DESC'] = $row['GROUP01_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['GROUP02_DESC'] = $row['GROUP02_DESC'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_CODE'] = $row['ACTIVITY_CODE'];
					$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['ACTIVITY_DESC'] = $row['ACTIVITY_DESC'];
				}
			}
		}
		
		/* ################################################### generate excel module review estate cost ################################################### */
		$query = $this->querySummaryEstateCostPerAfd($params);
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['AFD_CODE']]['rp_ha'] = $row['RP_HA_SETAHUN'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']][$row['AFD_CODE']]['rp_kg'] = $row['RP_KG'];
				$result['data'][$row['GROUP01'].$row['GROUP02'].$row['ACTIVITY_CODE']]['NORMA'] = $row['NORMA'];
				
				$result['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			}
		}		
		/* ################################################### generate excel module review estate cost ################################################### */
		
		return $result;
	}
	
	//generate report CAPEX
    public function reportCapex($params = array())
    {
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel report CAPEX ################################################### */
		$where = $where1 = $where2 = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where1 .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where1 .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where2 .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where2 .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
					rkt.BA_CODE,
					rkt.COA_CODE,
					rkt.COA_DESC,
					aset.DESCRIPTION AS ASSET_DESC,
					(SUM(rkt.DIS_JAN) + SUM(rkt.DIS_FEB) + SUM(rkt.DIS_MAR) +
					 SUM(rkt.DIS_APR) + SUM(rkt.DIS_MAY) + SUM(rkt.DIS_JUN) +
					 SUM(rkt.DIS_JUL) + SUM(rkt.DIS_AUG) + SUM(rkt.DIS_SEP) +
					 SUM(rkt.DIS_OCT) + SUM(rkt.DIS_NOV) + SUM(rkt.DIS_DEC)) AS DIS_TAHUN_BERJALAN,
					SUM(rkt.DIS_JAN) DIS_JAN,
					SUM(rkt.DIS_FEB) DIS_FEB,
					SUM(rkt.DIS_MAR) DIS_MAR,
					SUM(rkt.DIS_APR) DIS_APR,
					SUM(rkt.DIS_MAY) DIS_MAY,
					SUM(rkt.DIS_JUN) DIS_JUN,
					SUM(rkt.DIS_JUL) DIS_JUL,
					SUM(rkt.DIS_AUG) DIS_AUG,
					SUM(rkt.DIS_SEP) DIS_SEP,
					SUM(rkt.DIS_OCT) DIS_OCT,
					SUM(rkt.DIS_NOV) DIS_NOV,
					SUM(rkt.DIS_DEC) DIS_DEC,
					(SUM(NVL(rkt.DIS_BIAYA_JAN,0)) + SUM(NVL(rkt.DIS_BIAYA_FEB,0)) + SUM(NVL(rkt.DIS_BIAYA_MAR,0)) +
					 SUM(NVL(rkt.DIS_BIAYA_APR,0)) + SUM(NVL(rkt.DIS_BIAYA_MAY,0)) + SUM(NVL(rkt.DIS_BIAYA_JUN,0)) +
					 SUM(NVL(rkt.DIS_BIAYA_JUL,0)) + SUM(NVL(rkt.DIS_BIAYA_AUG,0)) + SUM(NVL(rkt.DIS_BIAYA_SEP,0)) +
					 SUM(NVL(rkt.DIS_BIAYA_OCT,0)) + SUM(NVL(rkt.DIS_BIAYA_NOV,0)) + SUM(NVL(rkt.DIS_BIAYA_DEC,0))) AS DIS_BIAYA_TOTAL,
				    SUM(NVL(rkt.DIS_BIAYA_JAN,0)) DIS_BIAYA_JAN,
				    SUM(NVL(rkt.DIS_BIAYA_FEB,0)) DIS_BIAYA_FEB,
					SUM(NVL(rkt.DIS_BIAYA_MAR,0)) DIS_BIAYA_MAR,
					SUM(NVL(rkt.DIS_BIAYA_APR,0)) DIS_BIAYA_APR,
					SUM(NVL(rkt.DIS_BIAYA_MAY,0)) DIS_BIAYA_MAY,
					SUM(NVL(rkt.DIS_BIAYA_JUN,0)) DIS_BIAYA_JUN,
					SUM(NVL(rkt.DIS_BIAYA_JUL,0)) DIS_BIAYA_JUL,
					SUM(NVL(rkt.DIS_BIAYA_AUG,0)) DIS_BIAYA_AUG,
					SUM(NVL(rkt.DIS_BIAYA_SEP,0)) DIS_BIAYA_SEP,
					SUM(NVL(rkt.DIS_BIAYA_OCT,0)) DIS_BIAYA_OCT,
					SUM(NVL(rkt.DIS_BIAYA_NOV,0)) DIS_BIAYA_NOV,
					SUM(NVL(rkt.DIS_BIAYA_DEC,0)) DIS_BIAYA_DEC
			FROM (  
				-- DARI RKT CAPEX
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.COA_CODE,
						UPPER(coa.DESCRIPTION) COA_DESC,
						RKT.ASSET_CODE,
						SUM (RKT.DIS_JAN) DIS_JAN,
						SUM (RKT.DIS_FEB) DIS_FEB,
						SUM (RKT.DIS_MAR) DIS_MAR,
						SUM (RKT.DIS_APR) DIS_APR,
						SUM (RKT.DIS_MAY) DIS_MAY,
						SUM (RKT.DIS_JUN) DIS_JUN,
						SUM (RKT.DIS_JUL) DIS_JUL,
						SUM (RKT.DIS_AUG) DIS_AUG,
						SUM (RKT.DIS_SEP) DIS_SEP,
						SUM (RKT.DIS_OCT) DIS_OCT,
						SUM (RKT.DIS_NOV) DIS_NOV,
						SUM (RKT.DIS_DEC) DIS_DEC,
						SUM (RKT.DIS_BIAYA_JAN) DIS_BIAYA_JAN,
						SUM (RKT.DIS_BIAYA_FEB) DIS_BIAYA_FEB,
						SUM (RKT.DIS_BIAYA_MAR) DIS_BIAYA_MAR,
						SUM (RKT.DIS_BIAYA_APR) DIS_BIAYA_APR,
						SUM (RKT.DIS_BIAYA_MAY) DIS_BIAYA_MAY,
						SUM (RKT.DIS_BIAYA_JUN) DIS_BIAYA_JUN,
						SUM (RKT.DIS_BIAYA_JUL) DIS_BIAYA_JUL,
						SUM (RKT.DIS_BIAYA_AUG) DIS_BIAYA_AUG,
						SUM (RKT.DIS_BIAYA_SEP) DIS_BIAYA_SEP,
						SUM (RKT.DIS_BIAYA_OCT) DIS_BIAYA_OCT,
						SUM (RKT.DIS_BIAYA_NOV) DIS_BIAYA_NOV,
						SUM (RKT.DIS_BIAYA_DEC) DIS_BIAYA_DEC
				FROM TR_RKT_CAPEX RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				LEFT JOIN TM_COA coa
					ON RKT.COA_CODE = coa.COA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				GROUP BY RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.COA_CODE,
						UPPER(coa.DESCRIPTION),
						RKT.ASSET_CODE
				UNION ALL
				-- BIAYA UMUM UNTUK COA 1212010101
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.COA_CODE,
						UPPER(coa.DESCRIPTION) COA_DESC,
						'' ASSET_CODE,
						0 DIS_JAN,
						0 DIS_FEB,
						0 DIS_MAR,
						0 DIS_APR,
						0 DIS_MAY,
						0 DIS_JUN,
						0 DIS_JUL,
						0 DIS_AUG,
						0 DIS_SEP,
						0 DIS_OCT,
						0 DIS_NOV,
						0 DIS_DEC,
						SUM (RKT.COST_JAN) DIS_BIAYA_JAN,
						SUM (RKT.COST_FEB) DIS_BIAYA_FEB,
						SUM (RKT.COST_MAR) DIS_BIAYA_MAR,
						SUM (RKT.COST_APR) DIS_BIAYA_APR,
						SUM (RKT.COST_MAY) DIS_BIAYA_MAY,
						SUM (RKT.COST_JUN) DIS_BIAYA_JUN,
						SUM (RKT.COST_JUL) DIS_BIAYA_JUL,
						SUM (RKT.COST_AUG) DIS_BIAYA_AUG,
						SUM (RKT.COST_SEP) DIS_BIAYA_SEP,
						SUM (RKT.COST_OCT) DIS_BIAYA_OCT,
						SUM (RKT.COST_NOV) DIS_BIAYA_NOV,
						SUM (RKT.COST_DEC) DIS_BIAYA_DEC
				FROM V_TOTAL_RELATION_COST RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				LEFT JOIN TM_COA coa
					ON RKT.COA_CODE = coa.COA_CODE	
				WHERE RKT.COA_CODE = '1212010101'
					$where
				GROUP BY RKT.PERIOD_BUDGET,
					ORG.REGION_CODE,
					RKT.BA_CODE,
					RKT.COA_CODE,
					UPPER(coa.DESCRIPTION)
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 1 = TM & JENIS = BARU & BUKAN PERKERASAN JALAN NEGARA
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.ACTIVITY_CODE COA_CODE,
						ACT.DESCRIPTION COA_DESC,
						'' ASSET_CODE,
						RKT.PLAN_JAN QTY_JAN,
						RKT.PLAN_FEB QTY_FEB,
						RKT.PLAN_MAR QTY_MAR,
						RKT.PLAN_APR QTY_APR,
						RKT.PLAN_MAY QTY_MAY,
						RKT.PLAN_JUN QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.COST_JAN,
						RKT.COST_FEB,
						RKT.COST_MAR,
						RKT.COST_APR,
						RKT.COST_MAY,
						RKT.COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC
				FROM TR_RKT_PK RKT
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					AND RKT.JENIS_PEKERJAAN = 'BARU'
					AND RKT.ACTIVITY_CODE NOT IN ('10470')
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : SMS 2 = TM & JENIS = BARU & BUKAN PERKERASAN JALAN NEGARA
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.ACTIVITY_CODE COA_CODE,
						ACT.DESCRIPTION COA_DESC,
						'' ASSET_CODE,
						0 QTY_JAN,
						0 QTY_FEB,
						0 QTY_MAR,
						0 QTY_APR,
						0 QTY_MAY,
						0 QTY_JUN,
						RKT.PLAN_JUL QTY_JUL,
						RKT.PLAN_AUG QTY_AUG,
						RKT.PLAN_SEP QTY_SEP,
						RKT.PLAN_OCT QTY_OCT,
						RKT.PLAN_NOV QTY_NOV,
						RKT.PLAN_DEC QTY_DEC,
						0 COST_JAN,
						0 COST_FEB,
						0 COST_MAR,
						0 COST_APR,
						0 COST_MAY,
						0 COST_JUN,
						RKT.COST_JUL,
						RKT.COST_AUG,
						RKT.COST_SEP,
						RKT.COST_OCT,
						RKT.COST_NOV,
						RKT.COST_DEC
				FROM TR_RKT_PK RKT
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					AND RKT.JENIS_PEKERJAAN = 'BARU'
					AND RKT.ACTIVITY_CODE NOT IN ('10470')
					$where
				UNION ALL
				-- RKT PERKERASAN JALAN : PERKERASAN JALAN NEGARA
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						RKT.ACTIVITY_CODE COA_CODE,
						ACT.DESCRIPTION COA_DESC,
						'' ASSET_CODE,
						RKT.PLAN_JAN QTY_JAN,
						RKT.PLAN_FEB QTY_FEB,
						RKT.PLAN_MAR QTY_MAR,
						RKT.PLAN_APR QTY_APR,
						RKT.PLAN_MAY QTY_MAY,
						RKT.PLAN_JUN QTY_JUN,
						0 QTY_JUL,
						0 QTY_AUG,
						0 QTY_SEP,
						0 QTY_OCT,
						0 QTY_NOV,
						0 QTY_DEC,
						RKT.COST_JAN,
						RKT.COST_FEB,
						RKT.COST_MAR,
						RKT.COST_APR,
						RKT.COST_MAY,
						RKT.COST_JUN,
						0 COST_JUL,
						0 COST_AUG,
						0 COST_SEP,
						0 COST_OCT,
						0 COST_NOV,
						0 COST_DEC
				FROM TR_RKT_PK RKT
				LEFT JOIN TM_ACTIVITY ACT
					ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					AND RKT.ACTIVITY_CODE IN ('10470')
					$where
				UNION ALL 
				-- UNTUK RKT001, RKT002, RKT003, RKT004 YG INFRASTRUKTUR
				SELECT 	ALL_ACT.PERIOD_BUDGET,
						ALL_ACT.REGION_CODE,
                        ALL_ACT.BA_CODE,
                        ALL_ACT.ACTIVITY_CODE COA_CODE,
                        ALL_ACT.ACTIVITY_DESC COA_DESC,
                        '' ASSET_CODE,
                        MAX(NVL (report.QTY_JAN, 0)) QTY_JAN,
                        MAX(NVL (report.QTY_FEB, 0)) QTY_FEB,
                        MAX(NVL (report.QTY_MAR, 0)) QTY_MAR,
                        MAX(NVL (report.QTY_APR, 0)) QTY_APR,
                        MAX(NVL (report.QTY_MAY, 0)) QTY_MAY,
                        MAX(NVL (report.QTY_JUN, 0)) QTY_JUN,
                        MAX(NVL (report.QTY_JUL, 0)) QTY_JUL,
                        MAX(NVL (report.QTY_AUG, 0)) QTY_AUG,
                        MAX(NVL (report.QTY_SEP, 0)) QTY_SEP,
                        MAX(NVL (report.QTY_OCT, 0)) QTY_OCT,
                        MAX(NVL (report.QTY_NOV, 0)) QTY_NOV,
                        MAX(NVL (report.QTY_DEC, 0)) QTY_DEC,
                        SUM(NVL (report.COST_JAN, 0)) COST_JAN,
                        SUM(NVL (report.COST_FEB, 0)) COST_FEB,
                        SUM(NVL (report.COST_MAR, 0)) COST_MAR,
                        SUM(NVL (report.COST_APR, 0)) COST_APR,
                        SUM(NVL (report.COST_MAY, 0)) COST_MAY,
                        SUM(NVL (report.COST_JUN, 0)) COST_JUN,
                        SUM(NVL (report.COST_JUL, 0)) COST_JUL,
                        SUM(NVL (report.COST_AUG, 0)) COST_AUG,
                        SUM(NVL (report.COST_SEP, 0)) COST_SEP,
                        SUM(NVL (report.COST_OCT, 0)) COST_OCT,
                        SUM(NVL (report.COST_NOV, 0)) COST_NOV,
                        SUM(NVL (report.COST_DEC, 0)) COST_DEC
				FROM (
					SELECT 	PERIOD.PERIOD_BUDGET,
							ORG.REGION_CODE,
							ORG.BA_CODE,
							ACT.ACTIVITY_CODE,
							ACT.DESCRIPTION AS ACTIVITY_DESC
					FROM (
						SELECT PERIOD_BUDGET
						FROM TM_PERIOD 
						WHERE 1 = 1
						$where1
					) PERIOD
					LEFT JOIN (
						SELECT REGION_CODE, BA_CODE, COMPANY_NAME
						FROM TM_ORGANIZATION
						WHERE 1 = 1
						$where2
					) ORG
						ON PERIOD.PERIOD_BUDGET IS NOT NULL
					LEFT JOIN (
						SELECT ACTIVITY_CODE, ACTIVITY_GROUP_TYPE_CODE, ACTIVITY_GROUP_TYPE, UI_RKT_CODE
						FROM TM_ACTIVITY_MAPPING
						WHERE UI_RKT_CODE IN ('RKT001', 'RKT002', 'RKT003', 'RKT004')
							AND ACTIVITY_GROUP_TYPE_CODE = 'INFRASTRUKTUR'
							AND ACTIVITY_CODE NOT IN ('42800', '42900', '43000', '43001', '43002', '43003')
					) MAPPING_ACT
						ON MAPPING_ACT.ACTIVITY_CODE IS NOT NULL
					LEFT JOIN TM_ACTIVITY ACT
						ON MAPPING_ACT.ACTIVITY_CODE = ACT.ACTIVITY_CODE
				) ALL_ACT
                LEFT JOIN TMP_RPT_EST_COST report
                    ON ALL_ACT.PERIOD_BUDGET = report.PERIOD_BUDGET
                    AND ALL_ACT.BA_CODE = report.BA_CODE
                    AND ALL_ACT.ACTIVITY_CODE = report.ACTIVITY_CODE
                    AND report.TIPE_TRANSAKSI = '02_BIAYA_RAWAT_TM'
                GROUP BY ALL_ACT.PERIOD_BUDGET,
					ALL_ACT.REGION_CODE,
					ALL_ACT.BA_CODE,
					ALL_ACT.ACTIVITY_CODE,
					ALL_ACT.ACTIVITY_DESC
			) rkt
			LEFT JOIN TM_ASSET aset
				ON rkt.PERIOD_BUDGET = aset.PERIOD_BUDGET
				AND rkt.BA_CODE = aset.BA_CODE
				AND rkt.ASSET_CODE = aset.ASSET_CODE
			GROUP BY TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR'),
				rkt.BA_CODE,
				rkt.COA_CODE,
				rkt.COA_DESC,
				aset.DESCRIPTION
			ORDER BY TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR'),
				rkt.BA_CODE,
				rkt.COA_CODE,
				rkt.COA_DESC,
				aset.DESCRIPTION
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel report CAPEX ################################################### */
		
		return $result;
	}
		
	//generate report sebaran HA
    public function reportSebaranHa($params = array())
    {
		$query = "
			SELECT REPORT.*, ORG.COMPANY_NAME
			  FROM    (SELECT PERIOD_BUDGET,
							  BA_CODE,
							  TIPE_TRANSAKSI,
							  AFD_CODE,
							  SMS1_TBM0,
							  SMS1_TBM1,
							  SMS1_TBM2,
							  SMS1_TBM3,
							  SMS1_TM,
							  TOTAL_HA_SMS1,
							  SMS2_TBM0,
							  SMS2_TBM1,
							  SMS2_TBM2,
							  SMS2_TBM3,
							  SMS2_TM,
							  TOTAL_HA_SMS2,
							  (TOTAL_HA_SMS1 + TOTAL_HA_SMS2) TOTAL_HA
						 FROM V_REPORT_SEBARAN_HS report
					   UNION
					   SELECT PERIOD_BUDGET,
							  BA_CODE,
							  TIPE_TRANSAKSI,
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
							  QTY_SETAHUN
						 FROM V_REPORT_SEBARAN_PRODUKSI) REPORT
				   LEFT JOIN
					  TM_ORGANIZATION ORG
				   ON ORG.BA_CODE = REPORT.BA_CODE
			WHERE 1 = 1 ";
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(report.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(report.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(report.BA_CODE)||'%'";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) = UPPER('".$params['src_region_code']."')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(report.BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
            ";
        }
		
		$query .= " ORDER BY report.PERIOD_BUDGET, report.BA_CODE, report.TIPE_TRANSAKSI";
		
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
		
	//generate report vra per BA
    public function reportVraUtilisasi($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
        }
	
	
		$query = "
			SELECT 	SUM_HM_KM.PERIOD_BUDGET,
					SUM_HM_KM.REGION_CODE,
					SUM_HM_KM.BA_CODE,
					SUM_HM_KM.COMPANY_NAME,
					SUM_HM_KM.VRA_CODE,
					UPPER (VRA.VRA_SUB_CAT_DESCRIPTION) AS VRA_SUB_CAT_DESCRIPTION,
					UPPER (VRA.TYPE) AS TYPE,
					NVL (SUM (SUM_HM_KM.JUMLAH_ALAT), 0) AS JUMLAH_ALAT,
					UPPER (VRA.UOM) AS UOM,
					NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0) AS TOTAL_HM_KM,
					NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0) AS HM_KM_DIGUNAKAN_SENDIRI,
					NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_PINJAM), 0) AS HM_KM_DIGUNAKAN_PINJAM,
					(NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0) - NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0)) AS SELISIH_HM_KM
			FROM (
				-- VRA yang dimiliki oleh BA tsb
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						ORG.COMPANY_NAME,
						RKT.VRA_CODE,
						RKT.JUMLAH_ALAT,
						RKT.TOTAL_QTY_TAHUN,
						0 TOTAL_HM_KM_SENDIRI,
						0 TOTAL_HM_KM_PINJAM
				FROM TR_RKT_VRA RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL
				-- VRA yg digunakan oleh BA tsb (bisa milik sendiri atau pinjam VRA dummy ZZ_)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.BA_CODE,
						ORG.COMPANY_NAME,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) = 'ZZ_'
							THEN SUBSTR(RKT.VRA_CODE,4)
							ELSE RKT.VRA_CODE
						END AS VRA_CODE,
						0 JUMLAH_ALAT,
						0 TOTAL_QTY_TAHUN,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) <> 'ZZ_'
							THEN RKT.HM_KM
							ELSE 0
						END AS TOTAL_HM_KM_SENDIRI,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) = 'ZZ_'
							THEN RKT.HM_KM
							ELSE 0
						END AS TOTAL_HM_KM_PINJAM
				FROM TR_RKT_VRA_DISTRIBUSI RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
			) SUM_HM_KM 
			LEFT JOIN TM_VRA VRA
				ON SUM_HM_KM.VRA_CODE = VRA.VRA_CODE
			GROUP BY SUM_HM_KM.PERIOD_BUDGET,
				SUM_HM_KM.REGION_CODE,
				SUM_HM_KM.BA_CODE,
				SUM_HM_KM.COMPANY_NAME,
				SUM_HM_KM.VRA_CODE,
				VRA.VRA_SUB_CAT_DESCRIPTION,
				VRA.TYPE,
				VRA.UOM
			ORDER BY SUM_HM_KM.PERIOD_BUDGET, SUM_HM_KM.BA_CODE, SUM_HM_KM.VRA_CODE
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
		
	//generate report vra per region
    public function reportVraUtilisasiRegion($params = array())
    {
		// ############################################# get all BA #############################################
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		$query = "
			SELECT REGION_NAME, BA_CODE
			FROM TM_ORGANIZATION ORG
			WHERE DELETE_USER IS NULL
			$where
			ORDER BY REGION_NAME, BA_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['BA_CODE'] = array(); // distinct BA
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct BA_CODE
				if (in_array($row['BA_CODE'], $result['BA_CODE']) == false) {
					array_push($result['BA_CODE'], $row['BA_CODE']);
				}
				
				$result['REGION_NAME'] = $row['REGION_NAME'];
			}
		}
		
		// ############################################# get all VRA #############################################
		$query = "
			SELECT 	SUM_HM_KM.VRA_CODE,
					UPPER (VRA.TYPE) AS TYPE,
					NVRA.RP_QTY NORMA
			FROM (
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						RKT.VRA_CODE
				FROM TR_RKT_VRA RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
				$where
				UNION ALL
				-- VRA yg digunakan oleh BA tsb (bisa milik sendiri atau pinjam VRA dummy ZZ_)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) = 'ZZ_'
							THEN SUBSTR(RKT.VRA_CODE,4)
							ELSE RKT.VRA_CODE
						END AS VRA_CODE
				FROM TR_RKT_VRA_DISTRIBUSI RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
			) SUM_HM_KM
			LEFT JOIN TM_VRA VRA
				ON SUM_HM_KM.VRA_CODE = VRA.VRA_CODE
			LEFT JOIN TN_VRA_PINJAM NVRA
				ON SUM_HM_KM.PERIOD_BUDGET = NVRA.PERIOD_BUDGET
				AND SUM_HM_KM.REGION_CODE = NVRA.REGION_CODE
				AND SUM_HM_KM.VRA_CODE = SUBSTR(NVRA.VRA_CODE,4)
			GROUP BY SUM_HM_KM.VRA_CODE,
					UPPER (VRA.TYPE),
					NVRA.RP_QTY
			ORDER BY SUM_HM_KM.VRA_CODE
		";
		$rows = $this->_db->fetchAll("{$query}");
		
		$result['VRA_CODE'] = array(); // distinct VRA_CODE
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				//distinct VRA_CODE
				if (in_array($row['VRA_CODE'], $result['VRA_CODE']) == false) {
					array_push($result['VRA_CODE'], $row['VRA_CODE']);
					
					$result['data'][$row['VRA_CODE']]['VRA_CODE'] = $row['VRA_CODE'];
					$result['data'][$row['VRA_CODE']]['TYPE'] = $row['TYPE'];
					$result['data'][$row['VRA_CODE']]['NORMA'] = $row['NORMA'];
				}
			}
		}
		
		/* ################################################### generate excel ################################################### */
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
	
	
		$query = "
			SELECT 	SUM_HM_KM.PERIOD_BUDGET,
					SUM_HM_KM.REGION_CODE,
					SUM_HM_KM.REGION_NAME,
					SUM_HM_KM.BA_CODE,
					SUM_HM_KM.COMPANY_NAME,
					SUM_HM_KM.VRA_CODE,
					NVRA.RP_QTY NORMA,
					RPQTY.VALUE RP_QTY,
					UPPER (VRA.VRA_SUB_CAT_DESCRIPTION) AS VRA_SUB_CAT_DESCRIPTION,
					UPPER (VRA.TYPE) AS TYPE,
					NVL (SUM (SUM_HM_KM.JUMLAH_ALAT), 0) AS JUMLAH_ALAT,
					UPPER (VRA.UOM) AS UOM,
					NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0) AS TOTAL_HM_KM,
					NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0) AS HM_KM_DIGUNAKAN_SENDIRI,
					NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_PINJAM), 0) AS HM_KM_DIGUNAKAN_PINJAM,
					(NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0) - NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0)) AS SELISIH_HM_KM
			FROM (
				-- VRA yang dimiliki oleh BA tsb
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						ORG.REGION_NAME,
						RKT.BA_CODE,
						ORG.COMPANY_NAME,
						RKT.VRA_CODE,
						RKT.JUMLAH_ALAT,
						RKT.TOTAL_QTY_TAHUN,
						0 TOTAL_HM_KM_SENDIRI,
						0 TOTAL_HM_KM_PINJAM
				FROM TR_RKT_VRA RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
				UNION ALL
				-- VRA yg digunakan oleh BA tsb (bisa milik sendiri atau pinjam VRA dummy ZZ_)
				SELECT 	RKT.PERIOD_BUDGET,
						ORG.REGION_CODE,
						ORG.REGION_NAME,
						RKT.BA_CODE,
						ORG.COMPANY_NAME,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) = 'ZZ_'
							THEN SUBSTR(RKT.VRA_CODE,4)
							ELSE RKT.VRA_CODE
						END AS VRA_CODE,
						0 JUMLAH_ALAT,
						0 TOTAL_QTY_TAHUN,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) <> 'ZZ_'
							THEN RKT.HM_KM
							ELSE 0
						END AS TOTAL_HM_KM_SENDIRI,
						CASE
							WHEN SUBSTR(RKT.VRA_CODE,1,3) = 'ZZ_'
							THEN RKT.HM_KM
							ELSE 0
						END AS TOTAL_HM_KM_PINJAM
				FROM TR_RKT_VRA_DISTRIBUSI RKT
				LEFT JOIN TM_ORGANIZATION ORG
					ON ORG.BA_CODE = RKT.BA_CODE
				WHERE RKT.DELETE_USER IS NULL
					AND RKT.FLAG_TEMP IS NULL
					$where
			) SUM_HM_KM 
			LEFT JOIN TM_VRA VRA
				ON SUM_HM_KM.VRA_CODE = VRA.VRA_CODE
			LEFT JOIN TR_RKT_VRA_SUM RPQTY
				ON SUM_HM_KM.PERIOD_BUDGET = RPQTY.PERIOD_BUDGET
				AND SUM_HM_KM.BA_CODE = RPQTY.BA_CODE
				AND SUM_HM_KM.VRA_CODE = RPQTY.VRA_CODE
			LEFT JOIN TN_VRA_PINJAM NVRA
				ON SUM_HM_KM.PERIOD_BUDGET = NVRA.PERIOD_BUDGET
				AND SUM_HM_KM.REGION_CODE = NVRA.REGION_CODE
				AND SUM_HM_KM.VRA_CODE = SUBSTR(NVRA.VRA_CODE,4)
			GROUP BY SUM_HM_KM.PERIOD_BUDGET,
				SUM_HM_KM.REGION_CODE,
				SUM_HM_KM.REGION_NAME,
				SUM_HM_KM.BA_CODE,
				SUM_HM_KM.COMPANY_NAME,
				SUM_HM_KM.VRA_CODE,
				NVRA.RP_QTY,
				RPQTY.VALUE,
				VRA.VRA_SUB_CAT_DESCRIPTION,
				VRA.TYPE,
				VRA.UOM
			ORDER BY SUM_HM_KM.PERIOD_BUDGET, SUM_HM_KM.BA_CODE, SUM_HM_KM.VRA_CODE
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}"); 
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['data'][$row['VRA_CODE']][$row['BA_CODE']]['rp_qty'] = $row['RP_QTY'];
				$result['data'][$row['VRA_CODE']][$row['BA_CODE']]['selisih'] = $row['SELISIH_HM_KM'];
				
				$result['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
			}
		}
		/* ################################################### generate excel ################################################### */
		
		return $result;
	}
		
	//generate module review produksi per AFD
    public function modReviewProduksiPerAfd($params = array())
    {
		/* ################################################### generate excel ################################################### */
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}
		
		//filter region
		if($params['src_region_code'] != ''){
			$where .= "
                AND B.REGION_CODE = '".$params['src_region_code']."'
            ";
		}
		
		//filter BA
		if($params['key_find'] != ''){
			$where .= "
                AND A.BA_CODE = '".$params['key_find']."'
            ";
		}
		
		$query = "
			SELECT 	A.PERIOD_BUDGET,
					B.REGION_CODE,
					B.REGION_NAME,
					B.COMPANY_CODE,
					B.COMPANY_NAME,
					A.BA_CODE,
					B.ESTATE_NAME,
					A.AFD_CODE,
					SUM(A.HA_SMS2) as HA_PANEN,
					SUM(A.POKOK_SMS2) as POKOK_PANEN,
					CASE
						WHEN NVL(SUM(A.HA_SMS2),0) = 0
						THEN 0
						ELSE ROUND( SUM(A.POKOK_SMS2) / SUM(A.HA_SMS2) )
					END as SPH_PANEN,
					SUM(A.YPH_PROFILE) as YIELD_PROFILE_YPH,
					SUM(A.TON_PROFILE) as YIELD_PROFILE_TON,
					SUM(A.YPH_PROPORTION) as POTENSI_YPH,
					SUM(A.TON_PROPORTION) as POTENSI_TON,
					SUM(A.YPH_BUDGET) as BUDGET_YPH,
					SUM(A.TON_BUDGET) as BUDGET_TON,
					CASE
						WHEN NVL(SUM(A.YPH_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.YPH_BUDGET) / SUM(A.YPH_PROPORTION) * 100
					END VAR_YPH,
					CASE
						WHEN NVL(SUM(A.TON_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.TON_BUDGET) / SUM(A.TON_PROPORTION) * 100
					END VAR_TON
			FROM TR_PRODUKSI_PERIODE_BUDGET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE A.DELETE_TIME IS NULL
				$where
			GROUP BY A.PERIOD_BUDGET,
				B.REGION_CODE,
				B.REGION_NAME,
				B.COMPANY_CODE,
				B.COMPANY_NAME,
				A.BA_CODE,
				B.ESTATE_NAME,
				A.AFD_CODE
			ORDER BY 1,2,4,6,8
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}"); 
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel ################################################### */
		
		return $result;
	}
		
	//generate module review produksi per BA
    public function modReviewProduksiPerBa($params = array())
    {
		/* ################################################### generate excel ################################################### */
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}
		
		//filter region
		if($params['src_region_code'] != ''){
			$where .= "
                AND B.REGION_CODE = '".$params['src_region_code']."'
            ";
		}
		
		$query = "
			SELECT 	A.PERIOD_BUDGET,
					B.REGION_CODE,
					B.REGION_NAME,
					B.COMPANY_CODE,
					B.COMPANY_NAME,
					A.BA_CODE,
					B.ESTATE_NAME,
					SUM(A.HA_SMS2) as HA_PANEN,
					SUM(A.POKOK_SMS2) as POKOK_PANEN,
					CASE
						WHEN NVL(SUM(A.HA_SMS2),0) = 0
						THEN 0
						ELSE ROUND( SUM(A.POKOK_SMS2) / SUM(A.HA_SMS2) )
					END as SPH_PANEN,
					SUM(A.YPH_PROFILE) as YIELD_PROFILE_YPH,
					SUM(A.TON_PROFILE) as YIELD_PROFILE_TON,
					SUM(A.YPH_PROPORTION) as POTENSI_YPH,
					SUM(A.TON_PROPORTION) as POTENSI_TON,
					SUM(A.YPH_BUDGET) as BUDGET_YPH,
					SUM(A.TON_BUDGET) as BUDGET_TON,
					CASE
						WHEN NVL(SUM(A.YPH_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.YPH_BUDGET) / SUM(A.YPH_PROPORTION) * 100
					END VAR_YPH,
					CASE
						WHEN NVL(SUM(A.TON_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.TON_BUDGET) / SUM(A.TON_PROPORTION) * 100
					END VAR_TON
			FROM TR_PRODUKSI_PERIODE_BUDGET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE A.DELETE_TIME IS NULL
				$where
			GROUP BY A.PERIOD_BUDGET,
				B.REGION_CODE,
				B.REGION_NAME,
				B.COMPANY_CODE,
				B.COMPANY_NAME,
				A.BA_CODE,
				B.ESTATE_NAME
			ORDER BY 1,2,4,6
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}"); 
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel ################################################### */
		
		return $result;
	}
		
	//generate module review produksi per region
    public function modReviewProduksiPerRegion($params = array())
    {
		/* ################################################### generate excel ################################################### */
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}
		
		$query = "
			SELECT 	A.PERIOD_BUDGET,
					B.REGION_CODE,
					B.REGION_NAME,
					SUM(A.HA_SMS2) as HA_PANEN,
					SUM(A.POKOK_SMS2) as POKOK_PANEN,
					CASE
						WHEN NVL(SUM(A.HA_SMS2),0) = 0
						THEN 0
						ELSE ROUND( SUM(A.POKOK_SMS2) / SUM(A.HA_SMS2) )
					END as SPH_PANEN,
					SUM(A.YPH_PROFILE) as YIELD_PROFILE_YPH,
					SUM(A.TON_PROFILE) as YIELD_PROFILE_TON,
					SUM(A.YPH_PROPORTION) as POTENSI_YPH,
					SUM(A.TON_PROPORTION) as POTENSI_TON,
					SUM(A.YPH_BUDGET) as BUDGET_YPH,
					SUM(A.TON_BUDGET) as BUDGET_TON,
					CASE
						WHEN NVL(SUM(A.YPH_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.YPH_BUDGET) / SUM(A.YPH_PROPORTION) * 100
					END VAR_YPH,
					CASE
						WHEN NVL(SUM(A.TON_PROPORTION),0) = 0
						THEN 0
						ELSE SUM(A.TON_BUDGET) / SUM(A.TON_PROPORTION) * 100
					END VAR_TON
			FROM TR_PRODUKSI_PERIODE_BUDGET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE A.DELETE_TIME IS NULL
				$where
			GROUP BY A.PERIOD_BUDGET,
				B.REGION_CODE,
				B.REGION_NAME
			ORDER BY 1,2
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}"); 
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel ################################################### */
		
		return $result;
	}
	
	//get last generate date
    public function getLastGenerate($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
	
	
		$query = "
			SELECT 	MAX(INSERT_USER) INSERT_USER,
					TO_CHAR( MAX(INSERT_TIME), 'DD-MM-RRRR HH24:MI:SS') INSERT_TIME
			FROM (
				SELECT 	MAX(INSERT_USER) INSERT_USER,
						MAX(INSERT_TIME) INSERT_TIME
				FROM TMP_RPT_DEV_COST
				WHERE 1 = 1
				$where
				UNION ALL
				SELECT 	MAX(INSERT_USER) INSERT_USER,
						MAX(INSERT_TIME) INSERT_TIME
				FROM TMP_RPT_EST_COST
				WHERE 1 = 1
				$where
			)
		";
		
		$result = $this->_db->fetchRow("{$query}");
		
		return $result;
	}
	
	//generate report development cost
    public function reportHkDevelopmentCost($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel report hk development cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND ALL_ACT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME, 
					(SELECT SUM(CEK.RP_HK) 
						FROM TR_RKT_CHECKROLL_SUM CEK 
						LEFT JOIN TN_BIAYA B ON B.SUB_COST_ELEMENT = CEK.JOB_CODE
						WHERE B.COST_ELEMENT='LABOUR' AND 
						B.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND B.BA_CODE = REPORT.BA_CODE AND
						CEK.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND CEK.BA_CODE = REPORT.BA_CODE 
						AND B.ACTIVITY_CODE=REPORT.ACTIVITY_CODE
						AND B.ACTIVITY_GROUP = REPORT.TIPE_TRANSAKSI
					) RP_HK ,
					
					( SELECT HKE
						FROM TM_CHECKROLL_HK CEK
						WHERE CEK.EMPLOYEE_STATUS='KT' AND 
						CEK.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND CEK.BA_CODE = REPORT.BA_CODE
					) HKE
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_DEV_COST ALL_ACT
				WHERE 1 = 1 
				$where
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			LEFT JOIN TM_ORGANIZATION ORG
				ON ORG.BA_CODE = REPORT.BA_CODE
			WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
		";
		//die($query);
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}		
		/* ################################################### generate excel report hk development cost ################################################### */
		
		return $result;
	}
	
	//generate report HK Estate cost
    public function reportHkEstateCost($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel hk estate cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME, 
					-- get all rkt_panen
					(
						SELECT SUM(BIAYA_PEMANEN_HK)
							FROM TR_RKT_PANEN panen
						LEFT JOIN TM_ACTIVITY activity 
							ON panen.ACTIVITY_CODE = activity.ACTIVITY_CODE
						WHERE panen.DELETE_USER IS NULL AND
						panen.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						panen.BA_CODE = REPORT.BA_CODE 
					) HK_PANEN,
					-- get sebaran perencaan produksi
					(
						SELECT 
						(
							SUM(JAN) + SUM(FEB) + SUM(MAR) + SUM(APR) + SUM(MAY) +
							SUM(JUN) + SUM(JUL) + SUM(AUG) + SUM(SEP) + SUM(OCT) + 
							SUM (NOV) + SUM(DEC)
						)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) TOTAL_COST_PERTAHUN,
						
					(
						SELECT SUM(JAN)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_JAN,
					(
						SELECT SUM(FEB)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_FEB,
					(
						SELECT SUM(MAR)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_MAR,
					(
						SELECT SUM(APR)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_APR,
					(
						SELECT SUM(MAY)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_MAY,
					(
						SELECT SUM(JUN)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_JUN,
					(
						SELECT SUM(JUL)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_JUL,
					(
						SELECT SUM(AUG)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_AUG,
					(
						SELECT SUM(SEP)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_SEP,
					(
						SELECT SUM(OCT)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_OCT,
					(
						SELECT SUM(NOV)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_NOV,
					(
						SELECT SUM(DEC)
						FROM TR_PRODUKSI_PERIODE_BUDGET norma
						LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
							ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
							AND norma.BA_CODE = thn_berjalan.BA_CODE
							AND norma.AFD_CODE = thn_berjalan.AFD_CODE
							AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
						LEFT JOIN TM_ORGANIZATION ORG
							ON norma.BA_CODE = ORG.BA_CODE
						WHERE norma.DELETE_USER IS NULL AND
						norma.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND 
						norma.BA_CODE = REPORT.BA_CODE 
					) PEREN_COST_DEC,
					
					(
					SELECT SUM(CEK.RP_HK) 
						FROM TR_RKT_CHECKROLL_SUM CEK 
						LEFT JOIN TN_BIAYA B ON B.SUB_COST_ELEMENT = CEK.JOB_CODE
						WHERE B.COST_ELEMENT='LABOUR' AND 
						B.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND B.BA_CODE = REPORT.BA_CODE AND
						CEK.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND CEK.BA_CODE = REPORT.BA_CODE 
						AND B.ACTIVITY_CODE=REPORT.ACTIVITY_CODE
						AND B.ACTIVITY_GROUP = REPORT.TIPE_TRANSAKSI
					) RP_HK ,
					
					( SELECT SUM(HKE)/2
						FROM TM_CHECKROLL_HK CEK
						WHERE  
						CEK.PERIOD_BUDGET = REPORT.PERIOD_BUDGET AND CEK.BA_CODE = REPORT.BA_CODE
					) HKE
					
					
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
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM TMP_RPT_EST_COST ALL_ACT
				WHERE 1 = 1
				$where
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			LEFT JOIN TM_ORGANIZATION ORG
				ON ORG.BA_CODE = REPORT.BA_CODE
			WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel hk estate cost ################################################### */
		
		return $result;
	}
	
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
	//inisialisasi list yang akan ditampilkan
	public function initList($params = array())
    {
		$result = array();
        $initAction = str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));
		$result = $this->$initAction($params);
        return $result;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
