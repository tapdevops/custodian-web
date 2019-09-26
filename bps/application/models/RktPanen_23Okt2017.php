<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT Panen
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: YIR 05/08/2013	: ambil data dari DB
						- getSumberBiaya			: YIR 05/08/2013	: get sumber biaya
						- getDataDownload			: YIR 05/08/2013	: ambil data untuk didownload ke excel dari DB
						- getList					: YIR 05/08/2013	: menampilkan list RKT Panen
						- getTotalAngkutAfd			: YIR 05/08/2013	: get total cost angkut TBS per AFD
						- getTotalLangsirAfd		: YIR 05/08/2013	: get total cost langsir TBS per AFD
						- saveDistVra				: YIR 05/08/2013	: hitung dist VRA Infra
						- calCostElement			: YIR 05/08/2013	: hitung per cost element
						- calTotalCost				: YIR 05/08/2013	: hitung total cost
						- saveRotation				: YIR 05/08/2013	: simpan inputan rotasi
						- saveTemp					: YIR 05/08/2013	: reset perhitungan
						- saveOer					: YIR 05/08/2013	: simpan perubahan OER dari input user
						- saveProduksi				: NBU 04/05/2015	: simpan perubahan Jarak PKS dan %Langsir
						- saveSebaran				: NBU 15/05/2015	: simpan perubahan sebaran per BA
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	05/08/2013
Update Terakhir		:	18/05/2015
Revisi				:	 
	SID 20/06/2014	: 	- penambahan filter & perubahan mekanisme select di getData & getDataDownload
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function calCostElement, calTotalCost, saveRotation, saveTemp
	NBU 04/05/2015	: 	- penambahan function saveProduksi
	NBU 15/05/2015	: 	- penambahan function saveSebaran
=========================================================================================================================
*/
class Application_Model_RktPanen
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
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//setting input untuk region
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
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$whrcrt="";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE'){
				$whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			}elseif ($this->_referenceRole == 'BA_CODE'){
				$whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HS.BA_CODE)||'%'";
			}
		}
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND RKT_GABUNGAN.ACTIVITY_CODE IN ('".$params['activity_code']."') 
            ";
        }
		
		if($params['budgetperiod'] != ''){
			$whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['budgetperiod']."'";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['PERIOD_BUDGET']."'";
		}else{
			$whrcrt .= "AND HS.PERIOD_BUDGET = TO_CHAR(TO_DATE('".$this->_period."', 'DD-MM-RRRR'), 'RRRR')";
		}
		
		if ($params['src_region_code'] != '') {
			$whrcrt .= "AND UPPER(REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')";
        }
		
		if ($params['key_find'] != '') {
			$whrcrt .= "AND UPPER(HS.BA_CODE) IN ('".$params['key_find']."')";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$whrcrt .= "
                AND UPPER(RKT_GABUNGAN.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		if ($params['src_afd'] != '') {
			$whrcrt .= "AND UPPER(HS.AFD_CODE) LIKE UPPER('".$params['src_afd']."')";
        }
		
		if ($params['src_block'] != '') {
			$whrcrt .= "AND UPPER(HS.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
        }
		
		$query="SELECT 
						hs.PERIOD_BUDGET PERIOD_BUDGETHS, hs.BA_CODE BA_CODEHS, hs.AFD_CODE AFD_CODEHS, hs.BLOCK_CODE BLOCK_CODEHS, ths.BLOCK_DESC BLOCK_DESCHS,
						RKT_GABUNGAN.*,
						activity.DESCRIPTION ACTIVITY_DESC,ORG.REGION_CODE, oer_ba.OER as OER_BA, oer_ba.OER as PRE_OER
				FROM 
				(
					SELECT DISTINCT TO_CHAR(PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, DELETE_USER 
					FROM TR_PRODUKSI_PERIODE_BUDGET WHERE DELETE_USER IS NULL AND TON_BUDGET>0
				) HS 
				LEFT JOIN 
				(
					SELECT ROWIDTOCHAR (PANEN1.ROWID) row_id, '' ROW_ID_TEMP, PANEN1.FLAG_TEMP, PANEN1.TRX_RKT_CODE, TO_CHAR(PANEN1.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
					PANEN1.BA_CODE, PANEN1.AFD_CODE, PANEN1.BLOCK_CODE, THS1.BLOCK_DESC, 
            EXTRACT(MONTH FROM THS1.TAHUN_TANAM) BULAN_TANAM,
            EXTRACT(YEAR FROM THS1.TAHUN_TANAM) TAHUN_TANAM,
            THS1.HA_PLANTED, THS1.POKOK_TANAM, THS1.SPH, THS1.TOPOGRAPHY, THS1.LAND_TYPE,
            PANEN1.ACTIVITY_CODE, PANEN1.TON, PANEN1.JANJANG, 
					PANEN1.BJR_AFD, PANEN1.JARAK_PKS, PANEN1.SUMBER_BIAYA_UNIT SUMBER_BIAYA, PANEN1.PERSEN_LANGSIR, 
					PANEN1.BIAYA_PEMANEN_HK, PANEN1.BIAYA_PEMANEN_RP_BASIS, PANEN1.BIAYA_PEMANEN_RP_PREMI_JANJANG, 
          PANEN1.INCENTIVE,
          PANEN1.BIAYA_PEMANEN_RP_PREMI_BRD, PANEN1.BIAYA_PEMANEN_RP_TOTAL, 
					PANEN1.BIAYA_PEMANEN_RP_KG, PANEN1.BIAYA_SPV_RP_BASIS, PANEN1.BIAYA_SPV_RP_PREMI, PANEN1.BIAYA_SPV_RP_TOTAL, PANEN1.BIAYA_SPV_RP_KG, 
					PANEN1.BIAYA_ALAT_PANEN_RP_KG, PANEN1.BIAYA_ALAT_PANEN_RP_TOTAL, PANEN1.TUKANG_MUAT_BASIS, PANEN1.TUKANG_MUAT_PREMI, PANEN1.TUKANG_MUAT_TOTAL, 
					PANEN1.TUKANG_MUAT_RP_KG, PANEN1.SUPIR_PREMI, PANEN1.SUPIR_RP_KG, PANEN1.ANGKUT_TBS_RP_KG_KM, PANEN1.ANGKUT_TBS_RP_ANGKUT, PANEN1.ANGKUT_TBS_RP_KG, 
					PANEN1.KRANI_BUAH_BASIS, PANEN1.KRANI_BUAH_PREMI, PANEN1.KRANI_BUAH_TOTAL, PANEN1.KRANI_BUAH_RP_KG, PANEN1.LANGSIR_TON, PANEN1.LANGSIR_RP, PANEN1.LANGSIR_TUKANG_MUAT,
					PANEN1.LANGSIR_RP_KG, PANEN1.COST_JAN, PANEN1.COST_FEB, PANEN1.COST_MAR, PANEN1.COST_APR, PANEN1.COST_MAY, PANEN1.COST_JUN, PANEN1.COST_JUL, 
					PANEN1.COST_AUG, PANEN1.COST_SEP, PANEN1.COST_OCT, PANEN1.COST_NOV, PANEN1.COST_DEC, PANEN1.COST_SETAHUN 
					FROM TR_RKT_PANEN PANEN1 
					LEFT JOIN TM_HECTARE_STATEMENT THS1
                    ON     PANEN1.PERIOD_BUDGET = THS1.PERIOD_BUDGET
                       AND PANEN1.BA_CODE = THS1.BA_CODE
                       AND PANEN1.AFD_CODE = THS1.AFD_CODE
                       AND PANEN1.BLOCK_CODE = THS1.BLOCK_CODE
                             WHERE PANEN1.DELETE_USER IS NULL
				) RKT_GABUNGAN 
					ON HS.PERIOD_BUDGET = RKT_GABUNGAN.PERIOD_BUDGET  
						AND HS.BA_CODE = RKT_GABUNGAN.BA_CODE 
						AND HS.AFD_CODE = RKT_GABUNGAN.AFD_CODE 
						AND HS.BLOCK_CODE = RKT_GABUNGAN.BLOCK_CODE  
				LEFT JOIN TM_ACTIVITY activity 
					ON RKT_GABUNGAN.ACTIVITY_CODE = activity.ACTIVITY_CODE
				LEFT JOIN TM_OER_BA oer_ba
					ON TO_CHAR(oer_ba.PERIOD_BUDGET,'RRRR') = hs.PERIOD_BUDGET
						AND oer_ba.BA_CODE = hs.BA_CODE
				LEFT JOIN TM_ORGANIZATION ORG 
					ON HS.BA_CODE = ORG.BA_CODE
				LEFT JOIN TM_HECTARE_STATEMENT THS
					ON HS.PERIOD_BUDGET = to_char(THS.PERIOD_BUDGET, 'RRRR') 
						AND HS.BA_CODE = THS.BA_CODE 
						AND HS.AFD_CODE = THS.AFD_CODE 
						AND HS.BLOCK_CODE = THS.BLOCK_CODE  
				WHERE 1=1 $whrcrt ";
		$query .= "
				ORDER BY HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE"; //print_r($params);echo "<br><br><br>";die(':DIE:');

		return $query;
	}
	
	//get sumber biaya
	public function getSumberBiaya($params = array())
    {
		$value = array();
		$query = "
			SELECT 
				   T.PARAMETER_VALUE_CODE,T.PARAMETER_VALUE 
				FROM T_PARAMETER_VALUE T 
				Where 
				PARAMETER_CODE LIKE 'SUMBER_BIAYA' AND 
				DELETE_USER IS NULL
				ORDER BY T.PARAMETER_VALUE_CODE DESC
			";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
        $value['count'] = $this->_db->fetchOne($sql);
			   
		$rows = $this->_db->fetchAll($query);
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$value['rows'][] = $row;
			}
        }
		return $value;
	}
	
	//ambil data untuk didownload ke excel dari DB
    public function getDataDownload($params = array())
    {
		$whrcrt="";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE'){
				$whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			}elseif ($this->_referenceRole == 'BA_CODE'){
				$whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HS.BA_CODE)||'%'";
			}
		}
		
		if($params['budgetperiod'] != ''){
			$whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['budgetperiod']."'";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['PERIOD_BUDGET']."'";
		}else{
			$whrcrt .= "AND HS.PERIOD_BUDGET = TO_CHAR(TO_DATE('".$this->_period."', 'DD-MM-RRRR'), 'RRRR')";
		}
		
		if ($params['src_region_code'] != '') {
			$whrcrt .= "AND UPPER(REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')";
        }
		
		if ($params['key_find'] != '') {
			$whrcrt .= "AND UPPER(HS.BA_CODE) LIKE UPPER('%".$params['key_find']."%')";
        }
		
		if ($params['src_afd'] != '') {
			$whrcrt .= "AND UPPER(HS.AFD_CODE) LIKE UPPER('".$params['src_afd']."')";
        }
		
		$query="SELECT 
						ROWNUM,hs.PERIOD_BUDGET PERIOD_BUDGETHS, hs.BA_CODE BA_CODEHS, hs.AFD_CODE AFD_CODEHS, hs.BLOCK_CODE BLOCK_CODEHS,
						RKT_GABUNGAN.*,
						activity.DESCRIPTION ACTIVITY_DESC,ORG.REGION_CODE, oer_ba.OER as OER_BA,
						(YB.TON_BUDGET / HS1.HA_PLANTED / 12) YIELD
				FROM 
				
				(SELECT DISTINCT TO_CHAR(PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, DELETE_USER FROM TR_PRODUKSI_PERIODE_BUDGET WHERE DELETE_USER IS NULL AND TON_BUDGET>0) HS 
				LEFT JOIN 
				(SELECT ROWIDTOCHAR (PANEN1.ROWID) row_id, '' ROW_ID_TEMP, PANEN1.TRX_RKT_CODE, TO_CHAR(PANEN1.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, PANEN1.BA_CODE, PANEN1.AFD_CODE, PANEN1.BLOCK_CODE, PANEN1.ACTIVITY_CODE, PANEN1.TON, PANEN1.JANJANG, PANEN1.BJR_AFD, PANEN1.JARAK_PKS, PANEN1.SUMBER_BIAYA_UNIT SUMBER_BIAYA, PANEN1.PERSEN_LANGSIR, PANEN1.BIAYA_PEMANEN_HK, PANEN1.BIAYA_PEMANEN_RP_BASIS, PANEN1.BIAYA_PEMANEN_RP_PREMI_JANJANG, PANEN1.BIAYA_PEMANEN_RP_PREMI_BRD, PANEN1.BIAYA_PEMANEN_RP_TOTAL, PANEN1.BIAYA_PEMANEN_RP_KG, PANEN1.BIAYA_SPV_RP_BASIS, PANEN1.BIAYA_SPV_RP_PREMI, PANEN1.BIAYA_SPV_RP_TOTAL, PANEN1.BIAYA_SPV_RP_KG, PANEN1.BIAYA_ALAT_PANEN_RP_KG, PANEN1.BIAYA_ALAT_PANEN_RP_TOTAL, PANEN1.TUKANG_MUAT_BASIS, PANEN1.TUKANG_MUAT_PREMI, PANEN1.TUKANG_MUAT_TOTAL, PANEN1.TUKANG_MUAT_RP_KG, PANEN1.SUPIR_PREMI, PANEN1.SUPIR_RP_KG, PANEN1.ANGKUT_TBS_RP_KG_KM, PANEN1.ANGKUT_TBS_RP_ANGKUT, PANEN1.ANGKUT_TBS_RP_KG, PANEN1.KRANI_BUAH_BASIS, PANEN1.KRANI_BUAH_PREMI, PANEN1.KRANI_BUAH_TOTAL, PANEN1.KRANI_BUAH_RP_KG, PANEN1.LANGSIR_TON, PANEN1.LANGSIR_RP, PANEN1.LANGSIR_TUKANG_MUAT, PANEN1.LANGSIR_RP_KG, PANEN1.COST_JAN, PANEN1.COST_FEB, PANEN1.COST_MAR, PANEN1.COST_APR, PANEN1.COST_MAY, PANEN1.COST_JUN, PANEN1.COST_JUL, PANEN1.COST_AUG, PANEN1.COST_SEP, PANEN1.COST_OCT, PANEN1.COST_NOV, PANEN1.COST_DEC, PANEN1.COST_SETAHUN FROM TR_RKT_PANEN PANEN1 WHERE DELETE_USER IS NULL 
				UNION 
				SELECT '' row_id, ROWIDTOCHAR (PANEN2.ROWID) ROW_ID_TEMP, PANEN2.TRX_RKT_CODE, TO_CHAR(PANEN2.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, PANEN2.BA_CODE, PANEN2.AFD_CODE, PANEN2.BLOCK_CODE, PANEN2.ACTIVITY_CODE, PANEN2.TON, PANEN2.JANJANG, PANEN2.BJR_AFD, PANEN2.JARAK_PKS, PANEN2.SUMBER_BIAYA_UNIT SUMBER_BIAYA, PANEN2.PERSEN_LANGSIR, PANEN2.BIAYA_PEMANEN_HK, PANEN2.BIAYA_PEMANEN_RP_BASIS, PANEN2.BIAYA_PEMANEN_RP_PREMI_JANJANG, PANEN2.BIAYA_PEMANEN_RP_PREMI_BRD, PANEN2.BIAYA_PEMANEN_RP_TOTAL, PANEN2.BIAYA_PEMANEN_RP_KG, PANEN2.BIAYA_SPV_RP_BASIS, PANEN2.BIAYA_SPV_RP_PREMI, PANEN2.BIAYA_SPV_RP_TOTAL, PANEN2.BIAYA_SPV_RP_KG, PANEN2.BIAYA_ALAT_PANEN_RP_KG, PANEN2.BIAYA_ALAT_PANEN_RP_TOTAL, PANEN2.TUKANG_MUAT_BASIS, PANEN2.TUKANG_MUAT_PREMI, PANEN2.TUKANG_MUAT_TOTAL, PANEN2.TUKANG_MUAT_RP_KG, PANEN2.SUPIR_PREMI, PANEN2.SUPIR_RP_KG, PANEN2.ANGKUT_TBS_RP_KG_KM, PANEN2.ANGKUT_TBS_RP_ANGKUT, PANEN2.ANGKUT_TBS_RP_KG, PANEN2.KRANI_BUAH_BASIS, PANEN2.KRANI_BUAH_PREMI, PANEN2.KRANI_BUAH_TOTAL, PANEN2.KRANI_BUAH_RP_KG, PANEN2.LANGSIR_TON, PANEN2.LANGSIR_RP, PANEN2.LANGSIR_TUKANG_MUAT, PANEN2.LANGSIR_RP_KG, PANEN2.COST_JAN, PANEN2.COST_FEB, PANEN2.COST_MAR, PANEN2.COST_APR, PANEN2.COST_MAY, PANEN2.COST_JUN, PANEN2.COST_JUL, PANEN2.COST_AUG, PANEN2.COST_SEP, PANEN2.COST_OCT, PANEN2.COST_NOV, PANEN2.COST_DEC, PANEN2.COST_SETAHUN FROM TR_RKT_PANEN_TEMP PANEN2 WHERE DELETE_USER IS NULL ) RKT_GABUNGAN 
				ON HS.PERIOD_BUDGET = RKT_GABUNGAN.PERIOD_BUDGET  
				  AND HS.BA_CODE = RKT_GABUNGAN.BA_CODE 
					  AND HS.AFD_CODE = RKT_GABUNGAN.AFD_CODE 
					  AND HS.BLOCK_CODE = RKT_GABUNGAN.BLOCK_CODE  
				LEFT JOIN (SELECT PPB.PERIOD_BUDGET, PPB.BA_CODE, SUM(PPB.TON_BUDGET) TON_BUDGET
					   FROM TR_PRODUKSI_PERIODE_BUDGET PPB
					   GROUP BY PPB.PERIOD_BUDGET, PPB.BA_CODE) YB
					ON TO_CHAR(YB.PERIOD_BUDGET,'RRRR') = HS.PERIOD_BUDGET
					AND YB.BA_CODE = HS.BA_CODE
				LEFT JOIN (SELECT HS.PERIOD_BUDGET, HS.BA_CODE, SUM(HS.HA_PLANTED) HA_PLANTED
					  FROM TM_HECTARE_STATEMENT HS
					  WHERE HS.MATURITY_STAGE_SMS2 = 'TM'
					  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS1
					ON TO_CHAR(HS1.PERIOD_BUDGET,'RRRR') = HS.PERIOD_BUDGET
					AND HS1.BA_CODE = HS.BA_CODE
				LEFT JOIN TM_ACTIVITY activity 
					ON RKT_GABUNGAN.ACTIVITY_CODE = activity.ACTIVITY_CODE 
				LEFT JOIN TM_ORGANIZATION ORG 
					ON HS.BA_CODE = ORG.BA_CODE  
				LEFT JOIN TM_OER_BA oer_ba
					ON TO_CHAR(oer_ba.PERIOD_BUDGET,'RRRR') = hs.PERIOD_BUDGET
					AND oer_ba.BA_CODE = hs.BA_CODE
				WHERE 1=1 $whrcrt ";
		
		$query .= "
			ORDER BY HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE";
		return $query;
	}
	
	//menampilkan list RKT Panen
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
		
        $sql = "SELECT COUNT(*) FROM ({$this->getData($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$this->getData($params)}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//get total cost angkut TBS per AFD
	public function getTotalAngkutAfd($params = array()){
		$sql="
			SELECT SUM(ANGKUT_TBS_RP_ANGKUT) 
			FROM TR_RKT_PANEN
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND AFD_CODE = '{$params['AFD_CODE']}' 
				AND SUMBER_BIAYA_UNIT = 'INTERNAL'
		";
		$result = $this->_db->fetchOne($sql);
		
		return ($result) ? $result : 0;
	}
	
	//get total cost langsir TBS per AFD
	public function getTotalLangsirAfd($params = array()){
		$sql="
			SELECT SUM(LANGSIR_TON)
			FROM TR_RKT_PANEN
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND AFD_CODE = '{$params['AFD_CODE']}' 
				AND SUMBER_BIAYA_UNIT = 'INTERNAL'
		";
		$result = $this->_db->fetchOne($sql);
		return ($result) ? $result : 0;
	}
	
	//hitung dist VRA Infra
	public function saveDistVra($arrAfdUpd = array()){
		$arrAfdUpd['SUM_ANGKUT'] = $this->getTotalAngkutAfd($arrAfdUpd);
		$arrAfdUpd['SUM_LANGSIR'] = $this->getTotalLangsirAfd($arrAfdUpd);
		$distVraPanenAngkTBS = $this->_formula->get_DistVraPanenAngkTBS($arrAfdUpd);
		$act_code = 51800; //angkut internal saja
		$trxCodeAngkutTbs = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$act_code.'DT010');
		
		$sql="
			SELECT 	VRA_CODE, 
					NVL(TON_TRIP,0) AS TONTRIP, 
					NVL(HM_TRIP,0) AS HMTRIP, 
					NVL(RP_HM,0) AS RP_HM
			FROM TN_PANEN_PREMI_LANGSIR
			WHERE PERIOD_BUDGET = TO_DATE ('01-01-{$arrAfdUpd['PERIOD_BUDGET']}', 'DD-MM-RRRR')
				AND BA_CODE = '{$arrAfdUpd['BA_CODE']}' 
				AND DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		//CEK DISTRIBUSI VRA
		$sql = "
			SELECT COUNT (DISTINCT LOCATION_CODE) 
			FROM TR_RKT_VRA_DISTRIBUSI
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
				AND ACTIVITY_CODE IN ('{$act_code}','51600')
				AND TIPE_TRANSAKSI = 'INFRA'
		";
		$distribusi = $this->_db->fetchOne($sql);
		
		
		//jika sebelumnya blm ada data, buat kombinasi data baru
		if($distribusi == 0){
			//GET AFDELING PER BA
			$sql = "
				SELECT DISTINCT AFD_CODE AS AFD_CODE 
				FROM TM_HECTARE_STATEMENT
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
			";
			$afdeling = $this->_db->fetchAll($sql);
			$sql = "";
			foreach($afdeling as $idx => $nilai){
				//INSERT KOMBINASI ANGKUT TBS
				$sql .= "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI(
						PERIOD_BUDGET, 
						BA_CODE, 
						ACTIVITY_CODE, 
						VRA_CODE, 
						LOCATION_CODE, 
						HM_KM, 
						PRICE_QTY_VRA, 
						PRICE_HM_KM, 
						TRX_CODE, 
						TIPE_TRANSAKSI, 
						INSERT_USER, 
						INSERT_TIME
					)
					VALUES(
						TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
						'".addslashes($arrAfdUpd['BA_CODE'])."', 
						'{$act_code}', 
						'DT010',
						'".addslashes($nilai['AFD_CODE'])."', 
						'0', 
						'0', 
						'0',
						'{$trxCodeAngkutTbs}', 
						'INFRA', 
						'{$this->_userName}', 
						SYSDATE
					);
				";
				//$this->_db->query($sql);
				//$this->_db->commit();
				}
			
			foreach ($rows as $idx => $roww){				
				foreach($afdeling as $idx => $value){						
					$trxLangsir = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].'51600'.$roww['VRA_CODE']);
					
					//INSERT KOMBINASI LANGSIR TBS
					$sql .= "
						INSERT INTO TR_RKT_VRA_DISTRIBUSI(
							PERIOD_BUDGET, 
							BA_CODE, 
							ACTIVITY_CODE, 
							VRA_CODE, 
							LOCATION_CODE, 
							HM_KM, 
							PRICE_QTY_VRA, 
							PRICE_HM_KM, 
							TRX_CODE, 
							TIPE_TRANSAKSI, 
							INSERT_USER, 
							INSERT_TIME)
						VALUES(
							TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
							'".addslashes($arrAfdUpd['BA_CODE'])."', 
							'51600', 
							'{$roww['VRA_CODE']}',
							'".addslashes($value['AFD_CODE'])."', 
							'0', 
							'0', 
							'0',
							'{$trxLangsir}', 
							'INFRA', 
							'{$this->_userName}', 
							SYSDATE
						);
					"; 
							
					//$this->_db->query($sql);
					//$this->_db->commit();
				}
			}
		}
		
		//DELETE RKT VRA DISTRIBUSI
		$sql = "
			DELETE FROM TR_RKT_VRA_DISTRIBUSI
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
				AND ACTIVITY_CODE IN ('{$act_code}','51600')
				AND LOCATION_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."';
		";
		
		//INSERT ANGKUT TBS
		$sql .= "
			INSERT INTO TR_RKT_VRA_DISTRIBUSI(
				PERIOD_BUDGET, 
				BA_CODE, 
				ACTIVITY_CODE, 
				VRA_CODE, 
				LOCATION_CODE, 
				HM_KM, 
				PRICE_QTY_VRA, 
				PRICE_HM_KM, 
				TRX_CODE, 
				TIPE_TRANSAKSI, 
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES(
				TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
				'".addslashes($arrAfdUpd['BA_CODE'])."', 
				'{$act_code}', 
				'DT010',
				'".addslashes($arrAfdUpd['AFD_CODE'])."', 
				{$distVraPanenAngkTBS[0]}, 
				{$distVraPanenAngkTBS[1]}, 
				{$arrAfdUpd['SUM_ANGKUT']},
				'{$trxCodeAngkutTbs}', 
				'INFRA', 
				'{$this->_userName}', 
				SYSDATE
			);
		";
		
		//INSERT LANGSIR TBS
		foreach ($rows as $idx => $rowx) {
			$harga = $rowx['RP_HM'];
			$distVraPanenLangsir = $this->_formula->get_DistVraPanenLangsir($arrAfdUpd['SUM_LANGSIR'],$rowx);
			$jumlah = $harga * $distVraPanenLangsir[0];
			$trxCodeLangsir = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].'51600'.$rowx['VRA_CODE']);
				
			$sql .= "
				INSERT INTO TR_RKT_VRA_DISTRIBUSI(
					PERIOD_BUDGET, 
					BA_CODE, 
					ACTIVITY_CODE, 
					VRA_CODE, 
					LOCATION_CODE, 
					HM_KM, 
					PRICE_QTY_VRA, 
					PRICE_HM_KM, 
					TRX_CODE, 
					TIPE_TRANSAKSI, 
					INSERT_USER, 
					INSERT_TIME
				)
				VALUES(
					TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
					'".addslashes($arrAfdUpd['BA_CODE'])."', 
					'51600', 
					'{$rowx['VRA_CODE']}',
					'".addslashes($arrAfdUpd['AFD_CODE'])."', 
					{$distVraPanenLangsir[0]}, 
					{$harga}, 
					{$jumlah},
					'{$trxCodeLangsir}', 
					'INFRA', 
					'{$this->_userName}', 
					SYSDATE
				);
			";
			
		}
		
		//create sql file
		$this->_global->createSqlFile($arrAfdUpd['filename'], $sql);
		return true;
	}
	
	//simpan inputan rotasi
	public function saveRotation($row = array())
	{		
		$sql = "
			UPDATE TR_RKT_PANEN
			SET	TON = REPLACE('".addslashes($row['TON'])."',',',''), 
				JANJANG = REPLACE('".addslashes($row['JANJANG'])."',',',''), 
				BJR_AFD = REPLACE('".addslashes($row['BJR_AFD'])."',',',''), 
				JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				BIAYA_PEMANEN_HK = REPLACE('".addslashes($row['BIAYA_PEMANEN_HK'])."',',',''), 
				BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
				BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
				BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_KG'])."',',',''), 
				BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($row['BIAYA_SPV_RP_BASIS'])."',',',''), 
				BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($row['BIAYA_SPV_RP_PREMI'])."',',',''), 
				BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_SPV_RP_TOTAL'])."',',',''), 
				BIAYA_SPV_RP_KG = REPLACE('".addslashes($row['BIAYA_SPV_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
				TUKANG_MUAT_BASIS = REPLACE('".addslashes($row['TUKANG_MUAT_BASIS'])."',',',''), 
				TUKANG_MUAT_PREMI = REPLACE('".addslashes($row['TUKANG_MUAT_PREMI'])."',',',''), 
				TUKANG_MUAT_TOTAL = REPLACE('".addslashes($row['TUKANG_MUAT_TOTAL'])."',',',''), 
				TUKANG_MUAT_RP_KG = REPLACE('".addslashes($row['TUKANG_MUAT_RP_KG'])."',',',''), 
				SUPIR_PREMI = REPLACE('".addslashes($row['SUPIR_PREMI'])."',',',''), 
				SUPIR_RP_KG = REPLACE('".addslashes($row['SUPIR_RP_KG'])."',',',''), 
				ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
				ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($row['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
				ANGKUT_TBS_RP_KG = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG'])."',',',''), 
				KRANI_BUAH_BASIS = REPLACE('".addslashes($row['KRANI_BUAH_BASIS'])."',',',''), 
				KRANI_BUAH_PREMI = REPLACE('".addslashes($row['KRANI_BUAH_PREMI'])."',',',''), 
				KRANI_BUAH_TOTAL = REPLACE('".addslashes($row['KRANI_BUAH_TOTAL'])."',',',''), 
				KRANI_BUAH_RP_KG = REPLACE('".addslashes($row['KRANI_BUAH_RP_KG'])."',',',''), 
				LANGSIR_TON = REPLACE('".addslashes($row['LANGSIR_TON'])."',',',''), 
				LANGSIR_RP = REPLACE('".addslashes($row['LANGSIR_RP'])."',',',''), 
				LANGSIR_TUKANG_MUAT = REPLACE('".addslashes($row['LANGSIR_TUKANG_MUAT'])."',',',''), 
				LANGSIR_RP_KG = REPLACE('".addslashes($row['LANGSIR_RP_KG'])."',',',''), 
				COST_JAN = REPLACE('".addslashes($row['COST_JAN'])."',',',''), 
				COST_FEB = REPLACE('".addslashes($row['COST_FEB'])."',',',''), 
				COST_MAR = REPLACE('".addslashes($row['COST_MAR'])."',',',''), 
				COST_APR = REPLACE('".addslashes($row['COST_APR'])."',',',''), 
				COST_MAY = REPLACE('".addslashes($row['COST_MAY'])."',',',''), 
				COST_JUN = REPLACE('".addslashes($row['COST_JUN'])."',',',''), 
				COST_JUL = REPLACE('".addslashes($row['COST_JUL'])."',',',''), 
				COST_AUG = REPLACE('".addslashes($row['COST_AUG'])."',',',''), 
				COST_SEP = REPLACE('".addslashes($row['COST_SEP'])."',',',''), 
				COST_OCT = REPLACE('".addslashes($row['COST_OCT'])."',',',''), 
				COST_NOV = REPLACE('".addslashes($row['COST_NOV'])."',',',''), 
				COST_DEC = REPLACE('".addslashes($row['COST_DEC'])."',',',''), 
				COST_SETAHUN = REPLACE('".addslashes($row['COST_SETAHUN'])."',',',''),
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
		
	//hitung per cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		
		$row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : 'INTERNAL';
		
		//hitung cost element
		$arrHSP = $this->_formula->get_RktPanen_HSProd($row);
		$row['TON'] = $arrHSP['TON'];
		$row['JANJANG'] = $arrHSP['JANJANG'];
		$row['BJR_AFD'] = $arrHSP['BJR_AFD'];
		$row['TOTAL_COST_ELEMENT'] = 0;
		
		//reset data
		$row['BIAYA_PEMANEN_HK'] = 0;
		$row['BIAYA_PEMANEN_RP_BASIS'] = 0;
		//$row['BIAYA_PEMANEN_RP_PREMI'] = 0; remarked by NBU 08.05.2015
		$row['BIAYA_PEMANEN_RP_PREMI_JANJANG'] = 0;
		$row['BIAYA_PEMANEN_RP_PREMI_BRD'] = 0;
		$row['BIAYA_PEMANEN_RP_TOTAL'] = 0;
		$row['BIAYA_PEMANEN_RP_KG'] = 0;
		$row['BIAYA_SPV_RP_BASIS'] = 0;
		$row['BIAYA_SPV_RP_PREMI'] = 0;
		$row['BIAYA_SPV_RP_TOTAL'] = 0;
		$row['BIAYA_SPV_RP_KG'] = 0;
		$row['TUKANG_MUAT_BASIS'] = 0;
		$row['TUKANG_MUAT_PREMI'] = 0;
		$row['TUKANG_MUAT_TOTAL'] = 0;
		$row['TUKANG_MUAT_RP_KG'] = 0;
		$row['SUPIR_RP_KG'] = 0;
		$row['SUPIR_PREMI'] = 0;
		$row['KRANI_BUAH_BASIS'] = 0;
		$row['KRANI_BUAH_PREMI'] = 0;
		$row['KRANI_BUAH_TOTAL'] = 0;
		$row['KRANI_BUAH_RP_KG'] = 0;
		$row['BIAYA_ALAT_PANEN_RP_KG'] = 0;
		$row['BIAYA_ALAT_PANEN_RP_TOTAL'] = 0;
		$row['ANGKUT_TBS_RP_KG_KM'] = 0;
		$row['ANGKUT_TBS_RP_ANGKUT'] = 0;
		$row['ANGKUT_TBS_RP_KG'] = 0;
		$row['LANGSIR_TON'] = 0;
		$row['LANGSIR_RP'] = 0;
		$row['LANGSIR_TUKANG_MUAT'] = 0;
		$row['LANGSIR_RP_KG'] = 0;
                $row['INCENTIVE'] = 0;

		if($costElement == 'LABOUR'){
			$row['BIAYA_PEMANEN_HK'] = $this->_formula->get_RktPanen_PemanenHK($row);
			$row['BIAYA_PEMANEN_RP_BASIS'] = $this->_formula->get_RktPanen_PemanenBasis($row);
			//$row['BIAYA_PEMANEN_RP_PREMI'] = $this->_formula->get_RktPanen_PemanenPremi($row); remarked by NBU 08.05.2015
			// $premi_baru = $this->_formula->get_RktPanen_PemanenPremi($row);

      $premi_2018 = $this->_formula->get_RktPanen_Premi2018($row);

      $row['INCENTIVE'] = $this->_formula->get_RktPanen_Incentive2018($row);
			$row['BIAYA_PEMANEN_RP_PREMI_JANJANG'] = $premi_2018['premi_2018'];
			$row['BIAYA_PEMANEN_RP_PREMI_BRD'] = $premi_2018['premi_brondolan'];
			$row['BIAYA_PEMANEN_RP_TOTAL'] = $this->_formula->get_RktPanen_PemanenTotal($row); //total
			$row['BIAYA_PEMANEN_RP_KG'] = $this->_formula->get_RktPanen_PemanenKg($row);
			$row['BIAYA_SPV_RP_BASIS'] = $this->_formula->get_RktPanen_SpvBasis($row);
			$row['BIAYA_SPV_RP_PREMI'] = $this->_formula->get_SpvPremi_2018($row);
			$row['BIAYA_SPV_RP_TOTAL'] = $this->_formula->get_RktPanen_SpvTotal($row); //total
			$row['BIAYA_SPV_RP_KG'] = $this->_formula->get_RktPanen_SpvKg($row);
			$row['TUKANG_MUAT_BASIS'] = $this->_formula->get_RktPanen_TkgBasis($row);
			$row['TUKANG_MUAT_PREMI'] = $this->_formula->get_RktPanen_TkgPremi($row);
			$row['TUKANG_MUAT_TOTAL'] = $this->_formula->get_RktPanen_TkgTotal($row); //total
			$row['TUKANG_MUAT_RP_KG'] = $this->_formula->get_RktPanen_TkgKg($row); 
			$row['SUPIR_RP_KG'] = $this->_formula->get_RktPanen_SprKg($row);
			$row['SUPIR_PREMI'] = $this->_formula->get_RktPanen_SprPremi($row); //total
			$row['KRANI_BUAH_BASIS'] = $this->_formula->get_RktPanen_KraniBasis($row);
			$row['KRANI_BUAH_PREMI'] = $this->_formula->get_RktPanen_KraniPremi($row);
			$row['KRANI_BUAH_TOTAL'] = $this->_formula->get_RktPanen_KraniTotal($row); //total
			$row['KRANI_BUAH_RP_KG'] = $this->_formula->get_RktPanen_KraniKg($row);
			
			//total biaya
			$row['TOTAL_COST_ELEMENT'] += $row['BIAYA_PEMANEN_RP_TOTAL'];
			$row['TOTAL_COST_ELEMENT'] += $row['BIAYA_SPV_RP_TOTAL'];
			$row['TOTAL_COST_ELEMENT'] += $row['TUKANG_MUAT_TOTAL'];
			$row['TOTAL_COST_ELEMENT'] += $row['SUPIR_PREMI'];
			$row['TOTAL_COST_ELEMENT'] += $row['KRANI_BUAH_TOTAL'];
		}elseif($costElement == 'TOOLS'){
			$row['BIAYA_ALAT_PANEN_RP_KG'] = $this->_formula->get_RktPanen_ToolsKg($row);
			$row['BIAYA_ALAT_PANEN_RP_TOTAL'] = $this->_formula->get_RktPanen_ToolsTotal($row); //total
			
			//total biaya
			$row['TOTAL_COST_ELEMENT'] += $row['BIAYA_ALAT_PANEN_RP_TOTAL'];
		}elseif($costElement == 'TRANSPORT'){
			$row['ANGKUT_TBS_RP_KG_KM'] = $this->_formula->get_RktPanen_AngkutKGKM($row);
			$row['ANGKUT_TBS_RP_ANGKUT'] = $this->_formula->get_RktPanen_Angkut($row); //total
			$row['ANGKUT_TBS_RP_KG'] = $this->_formula->get_RktPanen_AngkutKG($row); 

			$row['LANGSIR_TON'] = $this->_formula->get_RktPanen_LangsirTon($row);
			$row['LANGSIR_RP'] = $this->_formula->get_RktPanen_Langsir($row); //total
			$row['LANGSIR_TUKANG_MUAT'] = $this->_formula->get_RktLangsir_TkgMuat($row);
			$row['LANGSIR_RP_KG'] = $this->_formula->get_RktPanen_LangsirKg($row);
		
			//total biaya
			$row['TOTAL_COST_ELEMENT'] += $row['ANGKUT_TBS_RP_ANGKUT'];
			$row['TOTAL_COST_ELEMENT'] += $row['LANGSIR_RP'];
			$row['TOTAL_COST_ELEMENT'] += $row['LANGSIR_TUKANG_MUAT'];
		}
		$distribusi = $this->_formula->cal_RktPanen_Distribusi($row);
		
		//save hasil cost element
		$sql = "
			UPDATE TR_RKT_PANEN_COST_ELEMENT
			SET	SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
				TON = REPLACE('".addslashes($row['TON'])."',',',''), 
				JANJANG = REPLACE('".addslashes($row['JANJANG'])."',',',''), 
				BJR_AFD = REPLACE('".addslashes($row['BJR_AFD'])."',',',''), 
				JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				BIAYA_PEMANEN_HK = REPLACE('".addslashes($row['BIAYA_PEMANEN_HK'])."',',',''), 
				BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
				BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
				BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_KG'])."',',',''), 
				BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($row['BIAYA_SPV_RP_BASIS'])."',',',''), 
				BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($row['BIAYA_SPV_RP_PREMI'])."',',',''), 
				BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_SPV_RP_TOTAL'])."',',',''), 
				BIAYA_SPV_RP_KG = REPLACE('".addslashes($row['BIAYA_SPV_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
				TUKANG_MUAT_BASIS = REPLACE('".addslashes($row['TUKANG_MUAT_BASIS'])."',',',''), 
				TUKANG_MUAT_PREMI = REPLACE('".addslashes($row['TUKANG_MUAT_PREMI'])."',',',''), 
				TUKANG_MUAT_TOTAL = REPLACE('".addslashes($row['TUKANG_MUAT_TOTAL'])."',',',''), 
				TUKANG_MUAT_RP_KG = REPLACE('".addslashes($row['TUKANG_MUAT_RP_KG'])."',',',''), 
				SUPIR_PREMI = REPLACE('".addslashes($row['SUPIR_PREMI'])."',',',''), 
				SUPIR_RP_KG = REPLACE('".addslashes($row['SUPIR_RP_KG'])."',',',''), 
				ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
				ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($row['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
				ANGKUT_TBS_RP_KG = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG'])."',',',''), 
				KRANI_BUAH_BASIS = REPLACE('".addslashes($row['KRANI_BUAH_BASIS'])."',',',''), 
				KRANI_BUAH_PREMI = REPLACE('".addslashes($row['KRANI_BUAH_PREMI'])."',',',''), 
				KRANI_BUAH_TOTAL = REPLACE('".addslashes($row['KRANI_BUAH_TOTAL'])."',',',''), 
				KRANI_BUAH_RP_KG = REPLACE('".addslashes($row['KRANI_BUAH_RP_KG'])."',',',''), 
				LANGSIR_TON = REPLACE('".addslashes($row['LANGSIR_TON'])."',',',''), 
				LANGSIR_RP = REPLACE('".addslashes($row['LANGSIR_RP'])."',',',''), 
				LANGSIR_TUKANG_MUAT = REPLACE('".addslashes($row['LANGSIR_TUKANG_MUAT'])."',',',''), 
				LANGSIR_RP_KG = REPLACE('".addslashes($row['LANGSIR_RP_KG'])."',',',''), 
				COST_JAN = REPLACE('".$distribusi['COST_JAN']."',',',''), 
				COST_FEB = REPLACE('".$distribusi['COST_FEB']."',',',''), 
				COST_MAR = REPLACE('".$distribusi['COST_MAR']."',',',''), 
				COST_APR = REPLACE('".$distribusi['COST_APR']."',',',''), 
				COST_MAY = REPLACE('".$distribusi['COST_MAY']."',',',''), 
				COST_JUN = REPLACE('".$distribusi['COST_JUN']."',',',''), 
				COST_JUL = REPLACE('".$distribusi['COST_JUL']."',',',''), 
				COST_AUG = REPLACE('".$distribusi['COST_AUG']."',',',''), 
				COST_SEP = REPLACE('".$distribusi['COST_SEP']."',',',''), 
				COST_OCT = REPLACE('".$distribusi['COST_OCT']."',',',''), 
				COST_NOV = REPLACE('".$distribusi['COST_NOV']."',',',''), 
				COST_DEC = REPLACE('".$distribusi['COST_DEC']."',',',''), 
				COST_SETAHUN = REPLACE('".$distribusi['TOTAL_COST']."',',',''), 
				COST_SMS1 = REPLACE('".$distribusi['TOTAL_COST_SMS1']."',',',''), 
				COST_SMS2 = REPLACE('".$distribusi['TOTAL_COST_SMS2']."',',',''),
				INCENTIVE = REPLACE('".addslashes($row['INCENTIVE'])."',',',''), 
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."' 
				AND COST_ELEMENT='$costElement';
		";

        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
        $result = true;
		
		$row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : 'INTERNAL';
		
		//cari summary total cost
		$sql = "
			SELECT 	SUM(BIAYA_PEMANEN_HK) BIAYA_PEMANEN_HK, 
					SUM(BIAYA_PEMANEN_RP_BASIS) BIAYA_PEMANEN_RP_BASIS, 
					SUM(BIAYA_PEMANEN_RP_PREMI_JANJANG) BIAYA_PEMANEN_RP_PREMI_JANJANG,
					SUM(BIAYA_PEMANEN_RP_PREMI_BRD) BIAYA_PEMANEN_RP_PREMI_BRD,
					SUM(BIAYA_PEMANEN_RP_TOTAL) BIAYA_PEMANEN_RP_TOTAL, 
					SUM(BIAYA_PEMANEN_RP_KG) BIAYA_PEMANEN_RP_KG, 
					SUM(BIAYA_SPV_RP_BASIS) BIAYA_SPV_RP_BASIS, 
					SUM(BIAYA_SPV_RP_PREMI) BIAYA_SPV_RP_PREMI, 
					SUM(BIAYA_SPV_RP_TOTAL) BIAYA_SPV_RP_TOTAL, 
					SUM(BIAYA_SPV_RP_KG) BIAYA_SPV_RP_KG, 
					SUM(BIAYA_ALAT_PANEN_RP_KG) BIAYA_ALAT_PANEN_RP_KG, 
					SUM(BIAYA_ALAT_PANEN_RP_TOTAL) BIAYA_ALAT_PANEN_RP_TOTAL, 
					SUM(TUKANG_MUAT_BASIS) TUKANG_MUAT_BASIS, 
					SUM(TUKANG_MUAT_PREMI) TUKANG_MUAT_PREMI, 
					SUM(TUKANG_MUAT_TOTAL) TUKANG_MUAT_TOTAL, 
					SUM(TUKANG_MUAT_RP_KG) TUKANG_MUAT_RP_KG, 
					SUM(SUPIR_PREMI) SUPIR_PREMI, 
					SUM(SUPIR_RP_KG) SUPIR_RP_KG, 
					SUM(ANGKUT_TBS_RP_KG_KM) ANGKUT_TBS_RP_KG_KM, 
					SUM(ANGKUT_TBS_RP_ANGKUT) ANGKUT_TBS_RP_ANGKUT, 
					SUM(ANGKUT_TBS_RP_KG) ANGKUT_TBS_RP_KG, 
					SUM(KRANI_BUAH_BASIS) KRANI_BUAH_BASIS, 
					SUM(KRANI_BUAH_PREMI) KRANI_BUAH_PREMI, 
					SUM(KRANI_BUAH_TOTAL) KRANI_BUAH_TOTAL, 
					SUM(KRANI_BUAH_RP_KG) KRANI_BUAH_RP_KG, 
					SUM(LANGSIR_TON) LANGSIR_TON, 
					SUM(LANGSIR_RP) LANGSIR_RP, 
					SUM(LANGSIR_TUKANG_MUAT) LANGSIR_TUKANG_MUAT, 
					SUM(LANGSIR_RP_KG) LANGSIR_RP_KG, 
					SUM(COST_JAN) COST_JAN, 
					SUM(COST_FEB) COST_FEB, 
					SUM(COST_MAR) COST_MAR, 
					SUM(COST_APR) COST_APR, 
					SUM(COST_MAY) COST_MAY, 
					SUM(COST_JUN) COST_JUN, 
					SUM(COST_JUL) COST_JUL, 
					SUM(COST_AUG) COST_AUG, 
					SUM(COST_SEP) COST_SEP, 
					SUM(COST_OCT) COST_OCT, 
					SUM(COST_NOV) COST_NOV, 
					SUM(COST_DEC) COST_DEC, 
					SUM(COST_SETAHUN) COST_SETAHUN, 
					SUM(COST_SMS1) COST_SMS1, 
					SUM(COST_SMS2) COST_SMS2, 
          SUM(INCENTIVE) INCENTIVE,
					MAX(TON) TON, 
					MAX(JANJANG) JANJANG, 
					MAX(BJR_AFD) BJR_AFD, 
					MAX(JARAK_PKS) JARAK_PKS, 
					MAX(PERSEN_LANGSIR) PERSEN_LANGSIR
			FROM TR_RKT_PANEN_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND AFD_CODE = '".addslashes($row['AFD_CODE'])."'
				AND BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."'
		";
		$summary = $this->_db->fetchRow($sql);
		
		//simpan total cost
		$sql = "
			UPDATE TR_RKT_PANEN 
			SET SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
				TON = REPLACE('".addslashes($summary['TON'])."',',',''), 
				JANJANG = REPLACE('".addslashes($summary['JANJANG'])."',',',''), 
				BJR_AFD = REPLACE('".addslashes($summary['BJR_AFD'])."',',',''), 
				JARAK_PKS = REPLACE('".addslashes($summary['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($summary['PERSEN_LANGSIR'])."',',',''), 
				BIAYA_PEMANEN_HK = REPLACE('".addslashes($summary['BIAYA_PEMANEN_HK'])."',',',''), 
				BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
				BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
				BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_KG'])."',',',''), 
				BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($summary['BIAYA_SPV_RP_BASIS'])."',',',''), 
				BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($summary['BIAYA_SPV_RP_PREMI'])."',',',''), 
				BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_SPV_RP_TOTAL'])."',',',''), 
				BIAYA_SPV_RP_KG = REPLACE('".addslashes($summary['BIAYA_SPV_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($summary['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
				TUKANG_MUAT_BASIS = REPLACE('".addslashes($summary['TUKANG_MUAT_BASIS'])."',',',''), 
				TUKANG_MUAT_PREMI = REPLACE('".addslashes($summary['TUKANG_MUAT_PREMI'])."',',',''), 
				TUKANG_MUAT_TOTAL = REPLACE('".addslashes($summary['TUKANG_MUAT_TOTAL'])."',',',''), 
				TUKANG_MUAT_RP_KG = REPLACE('".addslashes($summary['TUKANG_MUAT_RP_KG'])."',',',''), 
				SUPIR_PREMI = REPLACE('".addslashes($summary['SUPIR_PREMI'])."',',',''), 
				SUPIR_RP_KG = REPLACE('".addslashes($summary['SUPIR_RP_KG'])."',',',''), 
				ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
				ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
				ANGKUT_TBS_RP_KG = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_KG'])."',',',''), 
				KRANI_BUAH_BASIS = REPLACE('".addslashes($summary['KRANI_BUAH_BASIS'])."',',',''), 
				KRANI_BUAH_PREMI = REPLACE('".addslashes($summary['KRANI_BUAH_PREMI'])."',',',''), 
				KRANI_BUAH_TOTAL = REPLACE('".addslashes($summary['KRANI_BUAH_TOTAL'])."',',',''), 
				KRANI_BUAH_RP_KG = REPLACE('".addslashes($summary['KRANI_BUAH_RP_KG'])."',',',''), 
				LANGSIR_TON = REPLACE('".addslashes($summary['LANGSIR_TON'])."',',',''), 
				LANGSIR_RP = REPLACE('".addslashes($summary['LANGSIR_RP'])."',',',''), 
				LANGSIR_TUKANG_MUAT = REPLACE('".addslashes($summary['LANGSIR_TUKANG_MUAT'])."',',',''), 
				LANGSIR_RP_KG = REPLACE('".addslashes($summary['LANGSIR_RP_KG'])."',',',''), 
				COST_JAN = REPLACE('".addslashes($summary['COST_JAN'])."',',',''), 
				COST_FEB = REPLACE('".addslashes($summary['COST_FEB'])."',',',''), 
				COST_MAR = REPLACE('".addslashes($summary['COST_MAR'])."',',',''), 
				COST_APR = REPLACE('".addslashes($summary['COST_APR'])."',',',''), 
				COST_MAY = REPLACE('".addslashes($summary['COST_MAY'])."',',',''), 
				COST_JUN = REPLACE('".addslashes($summary['COST_JUN'])."',',',''), 
				COST_JUL = REPLACE('".addslashes($summary['COST_JUL'])."',',',''), 
				COST_AUG = REPLACE('".addslashes($summary['COST_AUG'])."',',',''), 
				COST_SEP = REPLACE('".addslashes($summary['COST_SEP'])."',',',''), 
				COST_OCT = REPLACE('".addslashes($summary['COST_OCT'])."',',',''), 
				COST_NOV = REPLACE('".addslashes($summary['COST_NOV'])."',',',''), 
				COST_DEC = REPLACE('".addslashes($summary['COST_DEC'])."',',',''), 
				COST_SETAHUN = REPLACE('".addslashes($summary['COST_SETAHUN'])."',',',''),
        INCENTIVE = REPLACE('".addslashes($summary['INCENTIVE'])."',',',''), 
				FLAG_TEMP = NULL, 
				MATURITY_STAGE_SMS1='TM', 
				MATURITY_STAGE_SMS2='TM',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."';
		";

        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//simpan temp data
	public function saveTemp($row = array())
    { 
        $result = true;
		$row['TON'] = 0;
		$row['JANJANG'] = 0;
		$row['BJR_AFD'] = 0;
		$row['TOTAL_COST_ELEMENT'] = 0;
		
		//reset data
		$row['BIAYA_PEMANEN_HK'] = 0;
		$row['BIAYA_PEMANEN_RP_BASIS'] = 0;
		//$row['BIAYA_PEMANEN_RP_PREMI'] = 0;
		$row['BIAYA_PEMANEN_RP_PREMI_JANJANG'] = 0;
		$row['BIAYA_PEMANEN_RP_PREMI_BRD'] = 0;
		$row['BIAYA_PEMANEN_RP_TOTAL'] = 0;
		$row['BIAYA_PEMANEN_RP_KG'] = 0;
		$row['BIAYA_SPV_RP_BASIS'] = 0;
		$row['BIAYA_SPV_RP_PREMI'] = 0;
		$row['BIAYA_SPV_RP_TOTAL'] = 0;
		$row['BIAYA_SPV_RP_KG'] = 0;
		$row['TUKANG_MUAT_BASIS'] = 0;
		$row['TUKANG_MUAT_PREMI'] = 0;
		$row['TUKANG_MUAT_TOTAL'] = 0;
		$row['TUKANG_MUAT_RP_KG'] = 0;
		$row['SUPIR_RP_KG'] = 0;
		$row['SUPIR_PREMI'] = 0;
		$row['KRANI_BUAH_BASIS'] = 0;
		$row['KRANI_BUAH_PREMI'] = 0;
		$row['KRANI_BUAH_TOTAL'] = 0;
		$row['KRANI_BUAH_RP_KG'] = 0;
		$row['BIAYA_ALAT_PANEN_RP_KG'] = 0;
		$row['BIAYA_ALAT_PANEN_RP_TOTAL'] = 0;
		$row['ANGKUT_TBS_RP_KG_KM'] = 0;
		$row['ANGKUT_TBS_RP_ANGKUT'] = 0;
		$row['ANGKUT_TBS_RP_KG'] = 0;
		$row['LANGSIR_TON'] = 0;
		$row['LANGSIR_RP'] = 0;
		$row['LANGSIR_TUKANG_MUAT'] = 0;
		$row['LANGSIR_RP_KG'] = 0;
		
		$sql = "
			UPDATE TR_RKT_PANEN_COST_ELEMENT
			SET SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
				TON = REPLACE('".addslashes($row['TON'])."',',',''), 
				JANJANG = REPLACE('".addslashes($row['JANJANG'])."',',',''), 
				BJR_AFD = REPLACE('".addslashes($row['BJR_AFD'])."',',',''), 
				JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				BIAYA_PEMANEN_HK = REPLACE('".addslashes($row['BIAYA_PEMANEN_HK'])."',',',''), 
				BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
				BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
				BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
				BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_KG'])."',',',''), 
				BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($row['BIAYA_SPV_RP_BASIS'])."',',',''), 
				BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($row['BIAYA_SPV_RP_PREMI'])."',',',''), 
				BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_SPV_RP_TOTAL'])."',',',''), 
				BIAYA_SPV_RP_KG = REPLACE('".addslashes($row['BIAYA_SPV_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
				BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
				TUKANG_MUAT_BASIS = REPLACE('".addslashes($row['TUKANG_MUAT_BASIS'])."',',',''), 
				TUKANG_MUAT_PREMI = REPLACE('".addslashes($row['TUKANG_MUAT_PREMI'])."',',',''), 
				TUKANG_MUAT_TOTAL = REPLACE('".addslashes($row['TUKANG_MUAT_TOTAL'])."',',',''), 
				TUKANG_MUAT_RP_KG = REPLACE('".addslashes($row['TUKANG_MUAT_RP_KG'])."',',',''), 
				SUPIR_PREMI = REPLACE('".addslashes($row['SUPIR_PREMI'])."',',',''), 
				SUPIR_RP_KG = REPLACE('".addslashes($row['SUPIR_RP_KG'])."',',',''), 
				ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
				ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($row['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
				ANGKUT_TBS_RP_KG = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG'])."',',',''), 
				KRANI_BUAH_BASIS = REPLACE('".addslashes($row['KRANI_BUAH_BASIS'])."',',',''), 
				KRANI_BUAH_PREMI = REPLACE('".addslashes($row['KRANI_BUAH_PREMI'])."',',',''), 
				KRANI_BUAH_TOTAL = REPLACE('".addslashes($row['KRANI_BUAH_TOTAL'])."',',',''), 
				KRANI_BUAH_RP_KG = REPLACE('".addslashes($row['KRANI_BUAH_RP_KG'])."',',',''), 
				LANGSIR_TON = REPLACE('".addslashes($row['LANGSIR_TON'])."',',',''), 
				LANGSIR_RP = REPLACE('".addslashes($row['LANGSIR_RP'])."',',',''), 
				LANGSIR_TUKANG_MUAT = REPLACE('".addslashes($row['LANGSIR_TUKANG_MUAT'])."',',',''), 
				LANGSIR_RP_KG = REPLACE('".addslashes($row['LANGSIR_RP_KG'])."',',',''), 
				COST_JAN = 0, COST_FEB = 0, 
				COST_MAR = 0, COST_APR = 0, 
				COST_MAY = 0, COST_JUN = 0, 
				COST_JUL = 0, COST_AUG = 0, 
				COST_SEP = 0, COST_OCT = 0, 
				COST_NOV = 0, COST_DEC = 0, 
				COST_SETAHUN = 0, COST_SMS1 = 0, 
				COST_SMS2 = 0,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//simpan perubahan OER dari input user
	public function saveOer($row = array())
	{
		$sql = "
			UPDATE TM_OER_BA
			SET OER = REPLACE('".addslashes($row['PRE_OER'])."',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE  PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
			
        return $result;
	}
	
	//simpan inputan jarak PKS dan % Langsir dari Perencanaan Produksi
	public function saveProduksi($row = array())
	{		
		$sql = "
			UPDATE TR_RKT_PANEN
			SET	JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE  PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND AFD_CODE = '{$row['AFD_CODE']}'
				AND BLOCK_CODE = '{$row['BLOCK_CODE']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
		$sql = "
			UPDATE TR_RKT_PANEN_COST_ELEMENT
			SET	JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE  PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND AFD_CODE = '{$row['AFD_CODE']}'
				AND BLOCK_CODE = '{$row['BLOCK_CODE']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
	//simpan perubahan sebaran produksi dari input user
	public function saveSebaran($row = array())
	{            
		$afdeling = ""; 
		$blok = "";
		$period_budget = "";
		$totalcost['JAN'] = 0;	$totalcost['FEB'] = 0;	$totalcost['MAR'] = 0;	$totalcost['APR'] = 0;	$totalcost['MAY'] = 0;	$totalcost['JUN'] = 0;
		$totalcost['JUL'] = 0;	$totalcost['AUG'] = 0;	$totalcost['SEP'] = 0;	$totalcost['OCT'] = 0;	$totalcost['NOV'] = 0;	$totalcost['DEC'] = 0;
		$totalcost['TOTAL'] = 0;
		
		for($i = 0; $i < count($row)-1; $i++){
			$afdeling .= "'" . $row[$i]['AFD_CODE'] . "'" . ",";
			$blok .= "'" . $row[$i]['BLOCK_CODE'] . "'" . ",";
			/*$totalcost['JAN'] += $row[$i]['JAN'];
			$totalcost['FEB'] += $row[$i]['FEB'];
			$totalcost['MAR'] += $row[$i]['MAR'];
			$totalcost['APR'] += $row[$i]['APR'];
			$totalcost['MAY'] += $row[$i]['MAY'];
			$totalcost['JUN'] += $row[$i]['JUN'];
			$totalcost['JUL'] += $row[$i]['JUL'];
			$totalcost['AUG'] += $row[$i]['AUG'];
			$totalcost['SEP'] += $row[$i]['SEP'];
			$totalcost['OCT'] += $row[$i]['OCT'];
			$totalcost['NOV'] += $row[$i]['NOV'];
			$totalcost['DEC'] += $row[$i]['DEC'];
			$totalcost['TOTAL'] += $row[$i]['JAN']+$row[$i]['FEB']+$row[$i]['MAR']+$row[$i]['APR']+$row[$i]['MAY']+$row[$i]['JUN']
								+$row[$i]['JUL']+$row[$i]['AUG']+$row[$i]['SEP']+$row[$i]['OCT']+$row[$i]['NOV']+$row[$i]['DEC'];*/
			$ba_code = $row[$i]['BA_CODE'];					
			$period_budget = $row[$i]['PERIOD_BUDGET'];
		}
		
		$afdeling = substr($afdeling, 0, -1);
		$blok = substr($blok, 0, -1);
		
		$sql1 = "SELECT  sum(norma.JAN) JAN, sum(norma.FEB) FEB, sum(norma.MAR) MAR, sum(norma.APR) APR, sum(norma.MAY) MAY, sum(norma.JUN) JUN,  
                         sum(norma.JUL) JUL, sum(norma.AUG) AUG, sum(norma.SEP) SEP, sum(norma.OCT) OCT, sum(norma.NOV) NOV, sum(norma.DEC) DEC,  
                        (sum(norma.JAN) + sum(norma.FEB)+ sum(norma.MAR) +
						 sum(norma.APR)+ sum(norma.MAY)+  sum(norma.JUN)+ sum(norma.JUL)+  
						 sum(norma.AUG)+ sum(norma.SEP)+  sum(norma.OCT)+ sum(norma.NOV)+  sum(norma.DEC)) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
                    ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                    AND norma.BA_CODE = thn_berjalan.BA_CODE
                    AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                    AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL
                    AND to_char(norma.PERIOD_BUDGET,'RRRR') = '" . $period_budget . "'            
                    AND UPPER(norma.BA_CODE) LIKE UPPER('%" . $ba_code . "%')
                    --AND norma.BLOCK_CODE not in (" . $blok . ")";
		
		$total_sum = $this->_db->fetchRow($sql1);
		
		$totalcost['JAN'] += $total_sum['JAN'];
		$totalcost['FEB'] += $total_sum['FEB'];
		$totalcost['MAR'] += $total_sum['MAR'];
		$totalcost['APR'] += $total_sum['APR'];
		$totalcost['MAY'] += $total_sum['MAY'];
		$totalcost['JUN'] += $total_sum['JUN'];
		$totalcost['JUL'] += $total_sum['JUL'];
		$totalcost['AUG'] += $total_sum['AUG'];
		$totalcost['SEP'] += $total_sum['SEP'];
		$totalcost['OCT'] += $total_sum['OCT'];
		$totalcost['NOV'] += $total_sum['NOV'];
		$totalcost['DEC'] += $total_sum['DEC'];
		$totalcost['TOTAL'] += $total_sum['TOTAL'];
		
		$sebaran['JAN'] = round($totalcost['JAN'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['FEB'] = round($totalcost['FEB'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['MAR'] = round($totalcost['MAR'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['APR'] = round($totalcost['APR'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['MAY'] = round($totalcost['MAY'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['JUN'] = round($totalcost['JUN'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['JUL'] = round($totalcost['JUL'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['AUG'] = round($totalcost['AUG'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['SEP'] = round($totalcost['SEP'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['OCT'] = round($totalcost['OCT'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['NOV'] = round($totalcost['NOV'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		$sebaran['DEC'] = round($totalcost['DEC'] / $totalcost['TOTAL'] * 100,4,PHP_ROUND_HALF_DOWN); 
		
		//print_r($sebaran);die();
		$sql1 = "SELECT COUNT(*) COUNT_SEB from TM_SEBARAN_PRODUKSI
                 WHERE to_char(PERIOD_BUDGET,'RRRR') = '" . $period_budget . "' 
				AND UPPER(BA_CODE) LIKE UPPER('%" . $ba_code . "%')";
		
		$count_seb = $this->_db->fetchRow($sql1);
		
		if($count_seb['COUNT_SEB'] > 0){
		$sql = "UPDATE TM_SEBARAN_PRODUKSI set 
				JAN = '" . $sebaran['JAN'] . "', FEB = '" . $sebaran['FEB'] . "', MAR = '" . $sebaran['MAR'] . "', APR = '" . $sebaran['APR'] . "', MAY = '" . $sebaran['MAY'] . "', 
				JUN = '" . $sebaran['JUN'] . "', 
				JUL = '" . $sebaran['JUL'] . "', AUG = '" . $sebaran['AUG'] . "', SEP = '" . $sebaran['SEP'] . "', OCT = '" . $sebaran['OCT'] . "', NOV = '" . $sebaran['NOV'] . "', 
				DEC = '" . $sebaran['DEC'] . "',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
				WHERE to_char(PERIOD_BUDGET,'RRRR') = '" . $period_budget . "' 
				AND UPPER(BA_CODE) LIKE UPPER('%" . $ba_code . "%');
				";                
		}else{
			$sql .= "
				INSERT INTO TM_SEBARAN_PRODUKSI(
					PERIOD_BUDGET, 
					BA_CODE, 
					JAN, 
					FEB, 
					MAR, 
					APR, 
					MAY, 
					JUN, 
					JUL, 
					AUG,
					SEP,
					OCT,
					NOV,
					DEC,
					INSERT_USER, 
					INSERT_TIME
				)
				VALUES(
					TO_DATE('01-01-{$period_budget}','DD-MM-RRRR'),
					'".addslashes($ba_code)."', 
				    '" . $sebaran['JAN'] . "',
					'" . $sebaran['FEB'] . "',
					'" . $sebaran['MAR'] . "',
					'" . $sebaran['APR'] . "',
					'" . $sebaran['MAY'] . "', 
					'" . $sebaran['JUN'] . "', 
					'" . $sebaran['JUL'] . "', 
					'" . $sebaran['AUG'] . "', 
					'" . $sebaran['SEP'] . "', 
					'" . $sebaran['OCT'] . "', 
					'" . $sebaran['NOV'] . "', 
				    '" . $sebaran['DEC'] . "',
					'{$this->_userName}', 
					SYSDATE
				);
			";
			//echo $sql;die();
		}
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
}

