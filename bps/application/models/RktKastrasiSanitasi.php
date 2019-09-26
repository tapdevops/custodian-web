<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT Manual - Non Infra
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region dan maturity stage
						- getActivityClass			: YIR 23/07/2013	: get activity class dari norma biaya
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
Dibuat Tanggal		: 	14/07/2014
Update Terakhir		:	14/07/2014
Revisi				:	
	
=========================================================================================================================
*/
class Application_Model_RktKastrasiSanitasi
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
		$options['optActivity'] = $table->getKastActivity();

        // elements
		$result['src_region_code'] = array(
            'type'    => 'select',
            'name'    => 'src_region_code',
            'value'   => '',
            'options' => $options['optRegion'],
            'ext'     => 'onChange=\'$("#src_ba").val("");\'', //src_afd
			'style'   => 'width:200px;background-color: #e6ffc8;'
        );
		
		$result['src_activity_code'] = array(
            'type'    => 'select',
            'name'    => 'src_activity_code',
            'value'   => '',
            'options' => $options['optActivity'],
            'ext'     => '', //src_afd
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
				   hs.TAHUN_TANAM AS TAHUN_TANAM_MULAI,
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
				   hs.LAND_SUITABILITY,
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
				   rkt.ATRIBUT,
				   CASE 
					WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
					ELSE rkt.AWAL_ROTASI
				   END as AWAL_ROTASI,
				   CASE 
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
				   activity.DESCRIPTION ACTIVITY_DESC 
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
			WHERE rkt.DELETE_USER IS NULL 
			AND rkt.TIPE_TRANSAKSI = 'KASTRASI_SANITASI' 
			AND (
				hs.MATURITY_STAGE_SMS1 LIKE 'TBM%'
				OR hs.MATURITY_STAGE_SMS2 LIKE 'TBM%'
			)
			$where
		";
		/*
			
		*/
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
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
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
		
			//AND rkt.BLOCK_CODE = 'B58'
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
				   CONCAT(CONCAT(rkt.BLOCK_CODE, ' - '), hs.BLOCK_DESC) BLOCK_CODE,
				   TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
				   to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   hs.TOPOGRAPHY,
				   hs.LAND_SUITABILITY,
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
				   rkt.COST_SMS1,
				   rkt.COST_SMS2,
				   CASE 
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
				   rkt.TIPE_TRANSAKSI,
				   activity.DESCRIPTION ACTIVITY_DESC 
			FROM TR_RKT rkt 
			LEFT JOIN TM_HECTARE_STATEMENT hs 
				ON rkt.PERIOD_BUDGET = hs.PERIOD_BUDGET 
				AND rkt.BA_CODE = hs.BA_CODE 
				AND rkt.AFD_CODE = hs.AFD_CODE 
				AND rkt.BLOCK_CODE = hs.BLOCK_CODE 
				AND rkt.TIPE_TRANSAKSI = 'KASTRASI_SANITASI' 
				AND rkt.ACTIVITY_CODE = '".$params['src_activity_code']."' 
			LEFT JOIN TM_ACTIVITY activity 
				ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
			LEFT JOIN TM_ORGANIZATION ORG 
				ON hs.BA_CODE = ORG.BA_CODE 
			WHERE rkt.DELETE_USER IS NULL 
			AND rkt.TIPE_TRANSAKSI = 'KASTRASI_SANITASI' 
			AND (
				hs.MATURITY_STAGE_SMS1 LIKE 'TBM%'
				OR hs.MATURITY_STAGE_SMS2 LIKE 'TBM%'
			)
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
		
		if ($params['src_activity_code'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%') 
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
				
				$kelas = $row['LAND_SUITABILITY'];
			}
			
        }
		
        return $result;
    }
	
		//menampilkan list RKT Manual - Non Infra (untuk hitungan di norma)
    public function getNormaList($params = array())
    {
        $result = array();
		
        $sql = "SELECT COUNT(*) FROM ({$this->getData($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$this->getData($params)}");
		
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
				
				$kelas = $row['LAND_SUITABILITY'];
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
		
		//hitung rotasi
		$sebaran_rotasi = $this->updateTempKastrasi($row);		
		$row['PLAN_JAN'] = $sebaran_rotasi[1];
		$row['PLAN_FEB'] = $sebaran_rotasi[2];
		$row['PLAN_MAR'] = $sebaran_rotasi[3];
		$row['PLAN_APR'] = $sebaran_rotasi[4];
		$row['PLAN_MAY'] = $sebaran_rotasi[5];
		$row['PLAN_JUN'] = $sebaran_rotasi[6];
		$row['PLAN_JUL'] = $sebaran_rotasi[7];
		$row['PLAN_AUG'] = $sebaran_rotasi[8];
		$row['PLAN_SEP'] = $sebaran_rotasi[9];
		$row['PLAN_OCT'] = $sebaran_rotasi[10];
		$row['PLAN_NOV'] = $sebaran_rotasi[11];
		$row['PLAN_DEC'] = $sebaran_rotasi[12];
		$row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
		$row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
		$row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
		
		//hitung cost element
		$mon = $this->_formula->cal_RktKastrasiSanitasi_CostElement($costElement,$row); 
		$row['TOTAL_RP_SMS1'] = ($row['TOTAL_PLAN_SMS1']) ? $mon[1] : 0;
		$row['TOTAL_RP_SMS2'] = ($row['TOTAL_PLAN_SMS2']) ? $mon[2] : 0;
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
		
		//hitung rotasi
		$sebaran_rotasi = $this->updateTempKastrasi($row);		
		$row['PLAN_JAN'] = $sebaran_rotasi[1];
		$row['PLAN_FEB'] = $sebaran_rotasi[2];
		$row['PLAN_MAR'] = $sebaran_rotasi[3];
		$row['PLAN_APR'] = $sebaran_rotasi[4];
		$row['PLAN_MAY'] = $sebaran_rotasi[5];
		$row['PLAN_JUN'] = $sebaran_rotasi[6];
		$row['PLAN_JUL'] = $sebaran_rotasi[7];
		$row['PLAN_AUG'] = $sebaran_rotasi[8];
		$row['PLAN_SEP'] = $sebaran_rotasi[9];
		$row['PLAN_OCT'] = $sebaran_rotasi[10];
		$row['PLAN_NOV'] = $sebaran_rotasi[11];
		$row['PLAN_DEC'] = $sebaran_rotasi[12];
		$row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
		$row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
		$row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
		
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
			SET PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
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
				ROTASI_SMS1 = REPLACE('".addslashes($summary['ROTASI_SMS1'])."',',',''),
				ROTASI_SMS2 = REPLACE('".addslashes($summary['ROTASI_SMS2'])."',',',''),
				TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
				TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
				TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
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
    }
	
	public function updateTempKastrasi($row = array()){
		//get norma kastrasi
		$norma = $this->_formula->getNormaKastrasiSanitasi($row);
		
		for ($i = 1 ; $i <= 12 ; $i++){
			//hitung selisih umur
			$date1_y = substr($row['TAHUN_TANAM'], -4);
			$date2_y = $row['PERIOD_BUDGET'];
			$selisih_tahun = (((int)($date2_y) - (int)($date1_y)) * 12);
			
			$date1_m = substr($row['TAHUN_TANAM'], 0, 2);
			$selisih_bulan = (int)($i) - (int)($date1_m);
			
			$total_selisih_umur = $selisih_tahun + $selisih_bulan;
			if (in_array($total_selisih_umur, $norma)) {
				$return[$i] = $row['HA_PLANTED'];
			}else{
				$return[$i] = 0;
			}
		}
		
		return($return);
	}
	
	//simpan inputan rotasi
	public function saveRotation($row = array())
	{
		//hitung rotasi
		$sebaran_rotasi = $this->updateTempKastrasi($row);		
		$row['PLAN_JAN'] = $sebaran_rotasi[1];
		$row['PLAN_FEB'] = $sebaran_rotasi[2];
		$row['PLAN_MAR'] = $sebaran_rotasi[3];
		$row['PLAN_APR'] = $sebaran_rotasi[4];
		$row['PLAN_MAY'] = $sebaran_rotasi[5];
		$row['PLAN_JUN'] = $sebaran_rotasi[6];
		$row['PLAN_JUL'] = $sebaran_rotasi[7];
		$row['PLAN_AUG'] = $sebaran_rotasi[8];
		$row['PLAN_SEP'] = $sebaran_rotasi[9];
		$row['PLAN_OCT'] = $sebaran_rotasi[10];
		$row['PLAN_NOV'] = $sebaran_rotasi[11];
		$row['PLAN_DEC'] = $sebaran_rotasi[12];
		$row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
		$row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
		$row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
		
		$sql = "
			UPDATE TR_RKT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
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
				TOTAL_RP_QTY = NULL,
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
	}
	
	//reset perhitungan
	public function saveTemp($row = array())
    { 
		//hitung rotasi
		$sebaran_rotasi = $this->updateTempKastrasi($row);		
		$row['PLAN_JAN'] = $sebaran_rotasi[1];
		$row['PLAN_FEB'] = $sebaran_rotasi[2];
		$row['PLAN_MAR'] = $sebaran_rotasi[3];
		$row['PLAN_APR'] = $sebaran_rotasi[4];
		$row['PLAN_MAY'] = $sebaran_rotasi[5];
		$row['PLAN_JUN'] = $sebaran_rotasi[6];
		$row['PLAN_JUL'] = $sebaran_rotasi[7];
		$row['PLAN_AUG'] = $sebaran_rotasi[8];
		$row['PLAN_SEP'] = $sebaran_rotasi[9];
		$row['PLAN_OCT'] = $sebaran_rotasi[10];
		$row['PLAN_NOV'] = $sebaran_rotasi[11];
		$row['PLAN_DEC'] = $sebaran_rotasi[12];
		$row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
		$row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
		$row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
		
        $sql = "
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
				PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
				PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
				PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
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
	
	//reset perhitungan untuk turunan inheritance
	public function EmptyKastrasi($row = array())
    { 
	    $sql = "
			UPDATE TR_RKT_COST_ELEMENT
			SET DIS_JAN = NULL,
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
				DIS_SETAHUN = NULL
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
}

