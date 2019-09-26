<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT Manual - Non Infra
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region dan maturity stage
						- getActivityClass			: YIR 23/07/2013	: get activity class dari norma biaya
						- getTipeNorma				: SID 18/07/2013	: get tipe norma
						- getRotation				: SID 23/07/2013	: get Rp/Rotasi SMS1 & SMS2
						- getData					: SID 23/07/2013	: ambil data dari DB
						- getDataDownload			: SID 23/07/2013	: ambil data untuk didownload ke excel dari DB
						- getList					: SID 23/07/2013	: menampilkan list RKT Manual - Non Infra
						- calCostElement			: SID 23/07/2013	: hitung per cost element
						- calTotalCost				: SID 23/07/2013	: hitung total cost
						- saveRotation				: SID 23/07/2013	: simpan inputan rotasi
						- saveTemp					: SID 23/07/2013	: reset perhitungan
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	23/07/2013
Update Terakhir		:	20/06/2014
Revisi				:	
	SID 20/06/2014	: 	- penambahan filter & perubahan mekanisme select di getData & getDataDownload
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function calCostElement, calTotalCost, saveRotation, saveTemp
	SID 18/07/2013	: 	- penambahan getTipeNorma
						- perubahan perhitungan cost element berdasarkan tipe norma yang dipilih
=========================================================================================================================
*/
class Application_Model_RktManualSisip
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
		
		$options['optMatStage'] = $table->getMaturityStage();
		$result['src_matstage_code'] = array(
            'type'    => 'select',
            'name'    => 'src_matstage_code',
            'value'   => '',
            'options' => $options['optMatStage'],
            'ext'     => '',
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//get activity class dari norma biaya
    public function getActivityClass($params = array())
    {
		$value = array();
		$query = "
			SELECT DISTINCT NILAI
			FROM (
				SELECT ACTIVITY_CLASS NILAI
				FROM TN_BIAYA
				WHERE DELETE_USER IS NULL
					AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
					AND BA_CODE = '".$params['key_find']."'
					AND ACTIVITY_CODE = '".$params['src_activity_code']."'
					AND COST_ELEMENT = 'LABOUR'
				UNION
				SELECT ACTIVITY_CLASS NILAI
				FROM TN_HARGA_BORONG
				WHERE DELETE_USER IS NULL
					AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
					AND BA_CODE = '".$params['key_find']."'
					AND ACTIVITY_CODE = '".$params['src_activity_code']."'
			)				
		";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
        $value['count'] = $this->_db->fetchOne($sql);
			   
		$rows = $this->_db->fetchAll($query);
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				if ($row['NILAI'] == 'ALL') {
					$value['rows'] = '';
					$value['rows'][] = $row;
					break;
				}else{
					$value['rows'][] = $row;
				}
			}
        }
		return $value;
	}
	
	//<!-- TIPE NORMA -->
	//get tipe norma
    public function getTipeNorma($params = array())
    {
		$value = array();
		$query = "
			SELECT PARAMETER_VALUE_CODE as  NILAI
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'TIPE_NORMA'
			AND DELETE_USER IS NULL
			ORDER BY 1 ASC
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
	
	//get Rp/Rotasi SMS1 & SMS2
	public function getRotation($params = array())
    {
		$return = array();
		
		$rotasi = $this->_formula->get_RktManual_Rotasi($params);
		$return['ROTASI_SMS1'] = $rotasi['SMS1'];
		$return['ROTASI_SMS2'] = $rotasi['SMS2'];
		
		return $return;
	}
	
	//ambil data dari DB
    public function getData($params = array())
    {
		if ($params['src_activity_code'] != '') {
			$where = "
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
            ";
        }
		
		$query = "
            SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
				   rkt.FLAG_TEMP,
				   rkt.TRX_RKT_CODE,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   rkt.BA_CODE,
				   rkt.AFD_CODE,
				   rkt.BLOCK_CODE,
				   hs.BLOCK_DESC,
				   TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
				   to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   hs.TOPOGRAPHY,
				   ( 
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
					AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
				   ) as TOPOGRAPHY_DESC,
				   hs.LAND_TYPE,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'LAND_TYPE' 
					AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
				   )as LAND_TYPE_DESC,
				   hs.MATURITY_STAGE_SMS1,
				   hs.MATURITY_STAGE_SMS2,
				   hs.HA_PLANTED,
				   hs.POKOK_TANAM,
				   hs.SPH,
				   rkt.ACTIVITY_CODE,
				   rkt.ACTIVITY_CLASS,
				   rkt.ROTASI_SMS1,
				   rkt.ROTASI_SMS2,
				   rkt.PLAN_SETAHUN,
				   rkt.SUMBER_BIAYA,
				   rkt.TOTAL_RP_SMS1,
				   rkt.TOTAL_RP_SMS2,
				   rkt.TOTAL_RP_SETAHUN,
				   rkt.PLAN_JAN,
				   rkt.PLAN_FEB,
				   rkt.PLAN_MAR,
				   rkt.PLAN_APR,
				   rkt.PLAN_MAY,
				   rkt.PLAN_JUN,
				   rkt.PLAN_JUL,
				   rkt.PLAN_AUG,
				   rkt.PLAN_SEP,
				   rkt.PLAN_OCT,
				   rkt.PLAN_NOV,
				   rkt.PLAN_DEC,
				   rkt.COST_JAN,
				   rkt.COST_FEB,
				   rkt.COST_MAR,
				   rkt.COST_APR,
				   rkt.COST_MAY,
				   rkt.COST_JUN,
				   rkt.COST_JUL,
				   rkt.COST_AUG,
				   rkt.COST_SEP,
				   rkt.COST_OCT,
				   rkt.COST_NOV,
				   rkt.COST_DEC,
				   rkt.TIPE_TRANSAKSI,
				   CASE 
					WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
					ELSE rkt.AWAL_ROTASI
				   END as AWAL_ROTASI,
				   CASE 
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
				   activity.DESCRIPTION as ACTIVITY_DESC,
				   NVL((SPH.SPH_STANDAR * hs.HA_PLANTED - hs.POKOK_TANAM),0) SPH_MAX
			FROM TR_RKT rkt 
			LEFT JOIN TM_HECTARE_STATEMENT hs 
				ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
				AND hs.BA_CODE = rkt.BA_CODE 
				AND hs.AFD_CODE = rkt.AFD_CODE 
				AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
			LEFT JOIN TM_ACTIVITY activity 
				ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
			LEFT JOIN TM_ORGANIZATION ORG 
				ON rkt.BA_CODE = ORG.BA_CODE
			LEFT JOIN TN_SPH SPH
				 ON SPH.CORE = CASE WHEN SUBSTR(rkt.BA_CODE,3,1) = 2 THEN 'INTI' ELSE 'PLASMA' END
				 AND SPH.LAND_TYPE = hs.LAND_TYPE
				 AND SPH.TOPOGRAPHY = hs.TOPOGRAPHY		
			WHERE rkt.DELETE_USER IS NULL 
			AND rkt.TIPE_TRANSAKSI = 'MANUAL_SISIP' 
			$where
		";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
            ";
		}else{
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%') 
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."') 
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
            ";
        }
		
		if ($params['src_block'] != '') {
			$query .= "AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
        }
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
            ";
        }
		
		//jika diupdate dari RKT VRA, filter berdasarkan kode activity
		if ($params['ACT_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan BA
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['CORE'] != '') {
			$query .= "
                AND SUBSTR(rkt.BA_CODE,3,1) = '".$params['CORE']."' 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['TOPOGRAPHY'] != '') {
			$query .= "
                AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
		if ($params['ACTIVITY_CLASS'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan maturity status
		if ($params['MATURITY_STATUS'] != '') {
			$query .= "
                AND (
					hs.MATURITY_STAGE_SMS1 IN ('".$params['MATURITY_STATUS']."') 
					OR hs.MATURITY_STAGE_SMS2 IN ('".$params['MATURITY_STATUS']."') 
				)
            ";
        }
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
			$query .= "
                AND (
					UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
					OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
		";
		
		return $query; 
	}
	
	//ambil data dari DB
    public function cekSph($params = array())
    {
		if ($params['src_activity_code'] != '') {
			$where = "
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
            ";
        }
		
		$query = "
            SELECT TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				 rkt.BA_CODE,
				 rkt.AFD_CODE,
				 rkt.BLOCK_CODE,
				 hs.BLOCK_DESC,
				 hs.MATURITY_STAGE_SMS1,
				 hs.MATURITY_STAGE_SMS2,
				 NVL(rkt.PLAN_JAN + rkt.PLAN_FEB + rkt.PLAN_MAR +
				 rkt.PLAN_APR + rkt.PLAN_MAY + rkt.PLAN_JUN + 
				 rkt.PLAN_JUL + rkt.PLAN_AUG +
				 rkt.PLAN_SEP + rkt.PLAN_OCT +
				 rkt.PLAN_NOV + rkt.PLAN_DEC,0) TOTAL_POKOK,
				 NVL ( (SPH.SPH_STANDAR * hs.ha_planted)-hs.pokok_tanam, 0) SPH_MAX,
                 NVL(NVL ( (SPH.SPH_STANDAR * hs.ha_planted)-hs.pokok_tanam, 0) -
                 NVL(rkt.PLAN_JAN + rkt.PLAN_FEB + rkt.PLAN_MAR +
                 rkt.PLAN_APR + rkt.PLAN_MAY + rkt.PLAN_JUN + 
                 rkt.PLAN_JUL + rkt.PLAN_AUG +
                 rkt.PLAN_SEP + rkt.PLAN_OCT +
                 rkt.PLAN_NOV + rkt.PLAN_DEC,0),0) SISA_POKOK
			FROM TR_RKT rkt 
			LEFT JOIN TM_HECTARE_STATEMENT hs 
				ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
				AND hs.BA_CODE = rkt.BA_CODE 
				AND hs.AFD_CODE = rkt.AFD_CODE 
				AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
			LEFT JOIN TM_ACTIVITY activity 
				ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
			LEFT JOIN TM_ORGANIZATION ORG 
				ON rkt.BA_CODE = ORG.BA_CODE
			LEFT JOIN TN_SPH SPH
				 ON SPH.CORE = CASE WHEN SUBSTR(rkt.BA_CODE,3,1) = 2 THEN 'INTI' ELSE 'PLASMA' END
				 AND SPH.LAND_TYPE = hs.LAND_TYPE
				 AND SPH.TOPOGRAPHY = hs.TOPOGRAPHY		
			WHERE rkt.DELETE_USER IS NULL 
			AND rkt.TIPE_TRANSAKSI = 'MANUAL_SISIP' 
			$where
		";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
            ";
		}else{
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%') 
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."') 
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
            ";
        }
		
		if ($params['src_block'] != '') {
			$query .= "AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
        }
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
            ";
        }
		
		//jika diupdate dari RKT VRA, filter berdasarkan kode activity
		if ($params['ACT_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan BA
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['CORE'] != '') {
			$query .= "
                AND SUBSTR(rkt.BA_CODE,3,1) = '".$params['CORE']."' 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['TOPOGRAPHY'] != '') {
			$query .= "
                AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
		if ($params['ACTIVITY_CLASS'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan maturity status
		if ($params['MATURITY_STATUS'] != '') {
			$query .= "
                AND (
					hs.MATURITY_STAGE_SMS1 IN ('".$params['MATURITY_STATUS']."') 
					OR hs.MATURITY_STAGE_SMS2 IN ('".$params['MATURITY_STATUS']."') 
				)
            ";
        }
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
			$query .= "
                AND (
					UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
					OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
		";
		
		return $query; 
	}
	
	//ambil data untuk didownload ke excel dari DB
    public function getDataDownload($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
				   rkt.TRX_RKT_CODE,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   rkt.BA_CODE,
				   ORG.COMPANY_NAME,
				   rkt.AFD_CODE,
				   CONCAT (CONCAT (rkt.BLOCK_CODE, ' - '), hs.BLOCK_DESC) BLOCK_CODE,
				   TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
				   to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   hs.TOPOGRAPHY,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
					AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
				   ) as TOPOGRAPHY_DESC,
				   hs.LAND_TYPE,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'LAND_TYPE' 
					AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
				   ) as LAND_TYPE_DESC,
				   hs.MATURITY_STAGE_SMS1,
				   hs.MATURITY_STAGE_SMS2,
				   hs.HA_PLANTED,
				   hs.POKOK_TANAM,
				   hs.SPH,
				   rkt.ACTIVITY_CODE,
				   rkt.ACTIVITY_CLASS,
				   rkt.ROTASI_SMS1,
				   rkt.ROTASI_SMS2,
				   rkt.PLAN_SETAHUN,
				   rkt.SUMBER_BIAYA,
				   rkt.TOTAL_RP_SMS1,
				   rkt.TOTAL_RP_SMS2,
				   rkt.TOTAL_RP_SETAHUN,
				   rkt.PLAN_JAN,
				   rkt.PLAN_FEB,
				   rkt.PLAN_MAR,
				   rkt.PLAN_APR,
				   rkt.PLAN_MAY,
				   rkt.PLAN_JUN,
				   rkt.PLAN_JUL,
				   rkt.PLAN_AUG,
				   rkt.PLAN_SEP,
				   rkt.PLAN_OCT,
				   rkt.PLAN_NOV,
				   rkt.PLAN_DEC,
				   rkt.COST_JAN,
				   rkt.COST_FEB,
				   rkt.COST_MAR,
				   rkt.COST_APR,
				   rkt.COST_MAY,
				   rkt.COST_JUN,
				   rkt.COST_JUL,
				   rkt.COST_AUG,
				   rkt.COST_SEP,
				   rkt.COST_OCT,
				   rkt.COST_NOV,
				   rkt.COST_DEC,
				   rkt.TIPE_TRANSAKSI,
				   activity.DESCRIPTION ACTIVITY_DESC,
				   CASE 
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
				   CASE 
					WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
					ELSE rkt.AWAL_ROTASI
				   END as AWAL_ROTASI
			FROM TR_RKT rkt 
			LEFT JOIN TM_HECTARE_STATEMENT hs 
				ON rkt.PERIOD_BUDGET = hs.PERIOD_BUDGET 
				AND rkt.BA_CODE = hs.BA_CODE 
				AND rkt.AFD_CODE = hs.AFD_CODE 
				AND rkt.BLOCK_CODE = hs.BLOCK_CODE 
				AND rkt.TIPE_TRANSAKSI = 'MANUAL_SISIP' 
				AND rkt.ACTIVITY_CODE = '".$params['src_activity_code']."' 
			LEFT JOIN TM_ACTIVITY activity 
				ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
			LEFT JOIN TM_ORGANIZATION ORG 
				ON hs.BA_CODE = ORG.BA_CODE 
			WHERE rkt.DELETE_USER IS NULL 
			AND rkt.TIPE_TRANSAKSI = 'MANUAL_SISIP' 
		";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
            ";
		}else{
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%') 
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
            ";
        }
		
		if ($params['src_activity_code'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%') 
            ";
        }
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
			$query .= "
                AND (
					UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
					OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
		";
		
		return $query;
	}
	
	//menampilkan list RKT Manual - Non Infra
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
		
		foreach($params as $idx => $paramsSumberBiaya)
		{
			$paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
			$paramsSumberBiaya['BA_CODE'] = $params['key_find'];
			$paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
		}
		
		if (!empty($rows)) {
			$sumberBiaya = $this->_formula->cekSumberBiayaExternal($paramsSumberBiaya);
            foreach ($rows as $idx => $row) {
				$row['SUMBER_BIAYA'] = $sumberBiaya;
				$rotasi = $this->_formula->get_RktManual_Rotasi($row);
				$row['ROTASI_SMS1'] = $rotasi['SMS1'];
				$row['ROTASI_SMS2'] = $rotasi['SMS2'];
                $result['rows'][] = $row;
            }
        }
		return $result;
    }
	
	//hitung per cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		//hitung sumber biaya 
		$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
		
		//<!-- TIPE NORMA -->
		//tipe norma
		$row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
		
		//hitung rotasi
		$rotasi = $this->_formula->get_RktManual_Rotasi($row);
		$row['ROTASI_SMS1'] = $rotasi['SMS1'];
		$row['ROTASI_SMS2'] = $rotasi['SMS2'];
		
		//hitung sebaran rotasi
		//$sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
		//$row['PLAN_JAN'] = $sebaran_rotasi['PLAN_JAN'];
		//$row['PLAN_FEB'] = $sebaran_rotasi['PLAN_FEB'];
		//$row['PLAN_MAR'] = $sebaran_rotasi['PLAN_MAR'];
		//$row['PLAN_APR'] = $sebaran_rotasi['PLAN_APR'];
		//$row['PLAN_MAY'] = $sebaran_rotasi['PLAN_MAY'];
		//$row['PLAN_JUN'] = $sebaran_rotasi['PLAN_JUN'];
		//$row['PLAN_JUL'] = $sebaran_rotasi['PLAN_JUL'];
		//$row['PLAN_AUG'] = $sebaran_rotasi['PLAN_AUG'];
		//$row['PLAN_SEP'] = $sebaran_rotasi['PLAN_SEP'];
		//$row['PLAN_OCT'] = $sebaran_rotasi['PLAN_OCT'];
		//$row['PLAN_NOV'] = $sebaran_rotasi['PLAN_NOV'];
		//$row['PLAN_DEC'] = $sebaran_rotasi['PLAN_DEC'];
		$row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
		$row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
		$row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
		
		//hitung cost element
		$mon = $this->_formula->cal_RktManualSisip_CostElement($costElement,$row);
		$konversi_pokok_ha = $this->_formula->cal_KonversiPokokHa($row);
	
		$row['TOTAL_RP_SMS1'] = ($rotasi['SMS1']) ? ($mon[1]/$konversi_pokok_ha['SPH_STANDAR']) : (0);
		$row['TOTAL_RP_SMS2'] = ($rotasi['SMS2']) ? ($mon[2]/$konversi_pokok_ha['SPH_STANDAR']) : (0);
		$row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
		$total = $this->_formula->cal_RktManual_Total($row);
		
		//save hasil cost element
		$sql = "
			UPDATE TR_RKT_COST_ELEMENT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
				RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''),
				RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
				TOTAL_RP_QTY = REPLACE('".addslashes($row['TOTAL_RP_SMS1_SMS2'])."',',',''),
				ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
				ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
				PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
				PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
				PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
				PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
				PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
				PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
				PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
				PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
				PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
				PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
				PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
				PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
				PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
				PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
				BULAN_PENGERJAAN = NULL, 
				ATRIBUT = NULL, 
				DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
				DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
				DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''),
				DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''),
				DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''),
				DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''),
				DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''),
				DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''),
				DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''),
				DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''),
				DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''),
				DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''),
				COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''),
				COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''),
				DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
			AND COST_ELEMENT = '".addslashes($costElement)."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
        $result = true;
		
		//hitung sebaran rotasi
		//$sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
		
		//cari summary total cost
		$sql = "
			SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, 
				   SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, 
				   SUM(DIS_JAN) DIS_JAN, 
				   SUM(DIS_FEB) DIS_FEB, 
				   SUM(DIS_MAR) DIS_MAR, 
				   SUM(DIS_APR) DIS_APR, 
				   SUM(DIS_MAY) DIS_MAY, 
				   SUM(DIS_JUN) DIS_JUN, 
				   SUM(DIS_JUL) DIS_JUL, 
				   SUM(DIS_AUG) DIS_AUG, 
				   SUM(DIS_SEP) DIS_SEP, 
				   SUM(DIS_OCT) DIS_OCT, 
				   SUM(DIS_NOV) DIS_NOV, 
				   SUM(DIS_DEC) DIS_DEC, 
				   SUM(DIS_SETAHUN) DIS_SETAHUN, 
				   MAX(ROTASI_SMS1) ROTASI_SMS1, 
				   MAX(ROTASI_SMS2) ROTASI_SMS2,  
				   MAX(PLAN_SMS1) PLAN_SMS1,  
				   MAX(PLAN_SMS2) PLAN_SMS2, 
				   MAX(PLAN_SETAHUN) PLAN_SETAHUN, 
				   SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, 
				   SUM(COST_SMS1) COST_SMS1, 
				   SUM(COST_SMS2) COST_SMS2
			FROM TR_RKT_COST_ELEMENT
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
		";
		$summary = $this->_db->fetchRow($sql);
		
		//simpan total cost
		$sql = "
			UPDATE TR_RKT
			SET AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
				PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
				PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
				PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
				PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
				PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
				PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
				PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
				PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
				PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
				PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
				PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
				ROTASI_SMS1 = REPLACE('".addslashes($summary['ROTASI_SMS1'])."',',',''),
				ROTASI_SMS2 = REPLACE('".addslashes($summary['ROTASI_SMS2'])."',',',''),
				TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
				TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
				TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
				PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''),
				PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''),
				PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''),
				COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
				COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
				COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
				COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
				COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
				COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
				COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
				COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
				COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
				COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
				COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
				COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
				COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''),
				COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''),
				TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''),
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";

        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		return $result;
    }
	
	//simpan inputan rotasi
	public function saveRotation($row = array())
	{
		//CEK TRX CODE di TR_RKT_COST_ELEMENT
		/*$sql_cek = "SELECT * FROM TR_RKT_COST_ELEMENT WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."'";
		$ada = $this->_db->fetchOne($sql_cek);
		$sql="";
		if(empty($ada)){
		$costElement = array("LABOUR","MATERIAL","TOOLS","TRANSPORT","CONTRACT");
			foreach($costElement as $ce){
				$sql.= "INSERT INTO TR_RKT_COST_ELEMENT (
						PERIOD_BUDGET,
						BA_CODE,
						AFD_CODE,
						BLOCK_CODE,
						TIPE_TRANSAKSI,
						ACTIVITY_CODE,
						COST_ELEMENT,
						TRX_RKT_CODE,
						INSERT_USER,
						INSERT_TIME)
						VALUES(
						TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
						'".addslashes($row['BA_CODE'])."',
						'".addslashes($row['AFD_CODE'])."',
						'".addslashes($row['BLOCK_CODE'])."',
						'MANUAL_NON_INFRA',
						'".addslashes($row['ACTIVITY_CODE'])."',
						'".$ce."',
						'".addslashes($row['TRX_RKT_CODE'])."',
						'{$this->_userName}', 
						SYSDATE);
				";
			}
		}*/
		
	
		//hitung rotasi
		$rotasi = $this->_formula->get_RktManual_Rotasi($row);
		$row['ROTASI_SMS1'] = $rotasi['SMS1'];
		$row['ROTASI_SMS2'] = $rotasi['SMS2'];
		
		//hitung sebaran rotasi
//		$sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
		
		
		$sql = "
			UPDATE TR_RKT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
				ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
				AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
				PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
				PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
				PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
				PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
				PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
				PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
				PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
				PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
				PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
				PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
				PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
				BULAN_PENGERJAAN = NULL, 
				ATRIBUT = NULL, 
				TOTAL_RP_SMS1 = NULL, 
				TOTAL_RP_SMS2 = NULL, 
				TOTAL_RP_QTY = NULL,
				PLAN_SMS1 = NULL, 
				PLAN_SMS2 = NULL, 
				PLAN_SETAHUN = NULL, 
				COST_JAN = NULL, 
				COST_FEB = NULL, 
				COST_MAR = NULL, 
				COST_APR = NULL, 
				COST_MAY = NULL, 
				COST_JUN = NULL, 
				COST_JUL = NULL, 
				COST_AUG = NULL, 
				COST_SEP = NULL, 
				COST_OCT = NULL, 
				COST_NOV = NULL, 
				COST_DEC = NULL, 
				COST_SMS1 = NULL, 
				COST_SMS2 = NULL, 
				TOTAL_RP_SETAHUN = NULL,
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
	}
	
	//reset perhitungan
	public function saveTemp($row = array())
    { 
		//$sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
		
        $sql.= "
			UPDATE TR_RKT_COST_ELEMENT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
				RP_ROTASI_SMS1 = NULL,
				RP_ROTASI_SMS2 = NULL,
				TOTAL_RP_QTY = NULL,
				PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
				PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
				PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
				PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
				PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
				PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
				PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
				PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
				PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
				PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
				PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
				PLAN_SMS1 = NULL,
				PLAN_SMS2 = NULL,
				PLAN_SETAHUN = NULL,
				BULAN_PENGERJAAN = NULL, 
				ATRIBUT = NULL, 
				DIS_JAN = NULL,
				DIS_FEB = NULL,
				DIS_MAR = NULL,
				DIS_APR = NULL,
				DIS_MAY = NULL,
				DIS_JUN = NULL,
				DIS_JUL = NULL,
				DIS_AUG = NULL,
				DIS_SEP = NULL,
				DIS_OCT = NULL,
				DIS_NOV = NULL,
				DIS_DEC = NULL,
				COST_SMS1 = NULL,
				COST_SMS2 = NULL,
				DIS_SETAHUN = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
}

