<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk RKT Manual - Infra
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT Manual - Infra
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
						- saveRotation(YIR)	: simpan data sementara
						- saveTemp(YIR)	: simpan data sementara
						- getListInfoVra 	: SID 15/07/2014	: menampilkan list info VRA
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/07/2013
Update Terakhir		:	15/07/2014
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktManualInfra
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
	
	public function getRotation($params = array())
    {
		$return = array();
		$params['TIPE_RKT_MANUAL'] = 'INFRA';
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
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')";
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
				   CASE 
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
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
			AND rkt.TIPE_TRANSAKSI = 'MANUAL_INFRA' 
			$where
		";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(hs.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(hs.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//jika diupdate dari norma infra, filter berdasarkan kelas aktivitas
		if ($params['ACTIVITY_CLASS'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
		
		//jika diupdate dari norma infra, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
		
		//jika diupdate dari norma infra, filter berdasarkan topografi
		if ($params['TOPOGRAPHY'] != '') {
			$query .= "
                AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
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
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(hs.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		if ($params['src_block'] != '') {
			$query .= "
                AND UPPER(hs.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%')
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
			ORDER BY hs.BA_CODE, hs.AFD_CODE, hs.BLOCK_CODE
		";
		
		//echo $query;die();
		return $query;
	}
	
	//ambil data untuk didownload dari DB
    public function getDataDownload($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR (rkt.ROWID) row_id,
				   TO_CHAR (hs.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   hs.BA_CODE,
				   ORG.COMPANY_NAME,
				   hs.AFD_CODE,
				   CONCAT(CONCAT(hs.BLOCK_CODE, ' - '), hs.BLOCK_DESC) as BLOCK_CODE,
				   TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
				   to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   hs.TOPOGRAPHY,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'TOPOGRAPHY'
						   AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY)
					  TOPOGRAPHY_DESC,
				   hs.LAND_TYPE,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'LAND_TYPE'
						   AND PARAMETER_VALUE_CODE = hs.LAND_TYPE)
					  LAND_TYPE_DESC,
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
				   activity.DESCRIPTION ACTIVITY_DESC
			FROM TR_RKT rkt
			LEFT JOIN TM_HECTARE_STATEMENT hs
				ON rkt.PERIOD_BUDGET = hs.PERIOD_BUDGET
				AND rkt.BA_CODE = hs.BA_CODE
				AND rkt.AFD_CODE = hs.AFD_CODE
				AND rkt.BLOCK_CODE = hs.BLOCK_CODE
				AND rkt.TIPE_TRANSAKSI = 'MANUAL_INFRA'
			LEFT JOIN TM_ACTIVITY activity
				ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON hs.BA_CODE = ORG.BA_CODE
			WHERE hs.DELETE_USER IS NULL
		";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HS.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(hs.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(hs.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(hs.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		if ($params['src_activity_code'] != '') {
			$query .= "
                --AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
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
			ORDER BY hs.BA_CODE, hs.AFD_CODE, hs.BLOCK_CODE
		";
		return $query;
	}
	
	//menampilkan list RKT Manual - Infra
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
			$paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_activity_code'];
		}
		
		//edited by doni
		if (!empty($rows)) {
			$sumberBiaya = $this->_formula->cekSumberBiayaExternalManualInfra($paramsSumberBiaya);
            foreach ($rows as $idx => $row) {
				$row['SUMBER_BIAYA'] = $row['SUMBER_BIAYA'] ? $row['SUMBER_BIAYA'] : $sumberBiaya;
				$row['TIPE_RKT_MANUAL'] = 'INFRA';
				$rotasi = $this->_formula->get_RktManual_Rotasi($row);
				$row['ROTASI_SMS1'] = $rotasi['SMS1'];
				$row['ROTASI_SMS2'] = $rotasi['SMS2'];
                $result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//menampilkan list info VRA
    public function getListInfoVra($params = array())
    {
        $result = array();

        $query = "
			SELECT ROWNUM, 
				   A.*
			FROM (
				SELECT *
				FROM (
					SELECT VRA_SUB_CAT_DESCRIPTION, VALUE
					FROM V_INFO_WINDOW_VRA
					WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
						AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
						AND VRA_SUB_CAT_CODE IN (
							SELECT VRA_SUB_CAT_CODE
							FROM TN_INFRASTRUKTUR INFRA
							LEFT JOIN TM_VRA VRA
								ON INFRA.SUB_COST_ELEMENT = VRA.VRA_CODE
								AND INFRA.COST_ELEMENT = 'TRANSPORT'
							WHERE to_char(INFRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
								AND UPPER(INFRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
								AND UPPER(INFRA.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
						)
					UNION ALL
					SELECT 'ANTAR BA - ' || VRA.VRA_SUB_CAT_DESCRIPTION, VRA_PINJAM.RP_QTY as VALUE
                    FROM TN_VRA_PINJAM VRA_PINJAM
                    LEFT JOIN TM_ORGANIZATION ORG
						ON VRA_PINJAM.REGION_CODE = ORG.REGION_CODE
                    LEFT JOIN TM_VRA VRA
                        ON VRA.VRA_CODE = VRA_PINJAM.VRA_CODE
                    WHERE to_char(VRA_PINJAM.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
						AND UPPER(ORG.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
						AND VRA_PINJAM.DELETE_TIME IS NULL
						AND VRA.VRA_SUB_CAT_CODE IN (
							SELECT VRA_SUB_CAT_CODE
							FROM TN_INFRASTRUKTUR INFRA
							LEFT JOIN TM_VRA VRA
								ON INFRA.SUB_COST_ELEMENT = VRA.VRA_CODE
								AND INFRA.COST_ELEMENT = 'TRANSPORT'
							WHERE to_char(INFRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
								AND UPPER(INFRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
								AND UPPER(INFRA.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
						)
				)
				ORDER BY VRA_SUB_CAT_DESCRIPTION
			) A
		";
		
        $sql = "SELECT COUNT(*) FROM ({$query})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll($query);
		
		//edited by doni
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
            }
        }
        return $result;
    }
	
	public function saveDistVra($arrAfdFix = array()){
		$result = true;

		//CEK DISTRIBUSI VRA
		$sql = "
			SELECT COUNT (DISTINCT LOCATION_CODE) FROM TR_RKT_VRA_DISTRIBUSI
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdFix['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdFix['BA_CODE'])."' 
				AND ACTIVITY_CODE = '".addslashes($arrAfdFix['ACTIVITY_CODE'])."'
				";
		$distribusi = $this->_db->fetchOne($sql);
		$sql = "";
		if($distribusi == 0){
			//GET AFDELING PER BA
			$sqlafd = "
				SELECT DISTINCT AFD_CODE AS AFD_CODE 
				FROM TM_HECTARE_STATEMENT
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdFix['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($arrAfdFix['BA_CODE'])."'
			";
			$afdeling = $this->_db->fetchAll($sqlafd);
			
			foreach($afdeling as $idx => $value){						
				$arrAfdFix['TRX_CODE'] = ($arrAfdFix['PERIOD_BUDGET']."-".$arrAfdFix['BA_CODE']."-RKT015-".$arrAfdFix['ACTIVITY_CODE']."-".$arrAfdFix['vraCode']);
				$sql.= "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI(
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, 
						LOCATION_CODE, HM_KM, PRICE_QTY_VRA, PRICE_HM_KM, 
						TRX_CODE, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
					VALUES(
						TO_DATE('01-01-{$arrAfdFix['PERIOD_BUDGET']}','DD-MM-RRRR'),
						'".addslashes($arrAfdFix['BA_CODE'])."', '{$arrAfdFix['ACTIVITY_CODE']}', '{$arrAfdFix['vraCode']}',
						'".addslashes($value['AFD_CODE'])."', '0', 
						'0', '0',
						'{$arrAfdFix['TRX_CODE']}', 'INFRA', '{$this->_userName}', SYSDATE);
						";
			}
		}
	
		// DELETE TR_RKT_VRA_DISTRIBUSI selain activity langsir dan angkut
		$sql.= "
			DELETE FROM TR_RKT_VRA_DISTRIBUSI
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdFix['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdFix['BA_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($arrAfdFix['ACTIVITY_CODE'])."'
				AND LOCATION_CODE = '".addslashes($arrAfdFix['AFD_CODE'])."';
				";
		
		$arrAfdFix['TRX_CODE'] = ($arrAfdFix['PERIOD_BUDGET']."-".$arrAfdFix['BA_CODE']."-RKT015-".$arrAfdFix['ACTIVITY_CODE']."-".$arrAfdFix['vraCode']);
		
		$sql.= "
			INSERT INTO TR_RKT_VRA_DISTRIBUSI(
				PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, 
				LOCATION_CODE, HM_KM, PRICE_QTY_VRA, PRICE_HM_KM, 
				TRX_CODE, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
			VALUES(
				TO_DATE('01-01-{$arrAfdFix['PERIOD_BUDGET']}','DD-MM-RRRR'),
				'".addslashes($arrAfdFix['BA_CODE'])."', '{$arrAfdFix['ACTIVITY_CODE']}', '{$arrAfdFix['vraCode']}',
				'".addslashes($arrAfdFix['AFD_CODE'])."', '{$arrAfdFix['totalDistVraManInfra']}', 
				'{$arrAfdFix['totalHrgInternal']}', '{$arrAfdFix['totalHrgHMKM']}',
				'{$arrAfdFix['TRX_CODE']}', 'INFRA', '{$this->_userName}', SYSDATE);
				";

		//create sql file
		$this->_global->createSqlFile($arrAfdFix['filename'], $sql);
			
        return $result;
	}
	
	public function hitungDistVra($arrAfdUpd = array()){ //diperuntukan untuk spesifik act class, act code, land type, dan topo
		$sql = "
			SELECT SUB_COST_ELEMENT, NVL(QTY_ALAT,0) QTY_ALAT, NVL(HARGA_INTERNAL,0) HARGA_INTERNAL 
			FROM TN_INFRASTRUKTUR 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
				AND COST_ELEMENT = 'TRANSPORT'
				AND ACTIVITY_CLASS IN ('ALL', '".addslashes($arrAfdUpd['ACTIVITY_CLASS'])."')
				AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."' 
				AND LAND_TYPE IN ('ALL', '".addslashes($arrAfdUpd['LAND_TYPE'])."')
				AND TOPOGRAPHY IN ('ALL', '".addslashes($arrAfdUpd['TOPOGRAPHY'])."')
				AND DELETE_USER IS NULL
			ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
		";	
		
		$row = $this->_db->fetchRow($sql);
		$totalDistVraManInfra = $this->_formula->get_DistVraManInfra($arrAfdUpd,$row);
		$totalHrgInternal = $row['HARGA_INTERNAL'];
		$totalHrgHMKM = $totalDistVraManInfra*$row['HARGA_INTERNAL'];
		$vraCode = $row['SUB_COST_ELEMENT'];
		
		return array('totalDistVraManInfra'=>$totalDistVraManInfra,
				'totalHrgHMKM'=>$totalHrgHMKM,
				'totalHrgInternal'=>$totalHrgInternal,
				'vraCode'=>$vraCode);
	}
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		
		//hitung cost element labour
		$this->calCostElement('LABOUR', $row);
		//hitung cost element material
		$this->calCostElement('MATERIAL', $row);
		//hitung cost element tools
		$this->calCostElement('TOOLS', $row);
		//hitung cost element transport
		$this->calCostElement('TRANSPORT', $row);
		//hitung cost element contract
		$this->calCostElement('CONTRACT', $row);
		//hitung total cost
		$this->calTotalCost($row);
		
        return $result;
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
	
	//hitung cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		
		//jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
		$row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
		
		//hitung cost element
		$row['TIPE_RKT_MANUAL'] = 'INFRA';
		$rotasi = $this->_formula->get_RktManual_Rotasi($row);
		
		$mon = $this->_formula->cal_RktManual_CostElementInfra($costElement,$row);
		$row['TOTAL_RP_SMS1'] = $mon[1];
		$row['TOTAL_RP_SMS2'] = $mon[2];
		$row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
		$total = $this->_formula->cal_RktManual_Total($row);
		
		//save hasil cost element
			$sql= "
				UPDATE TR_RKT_COST_ELEMENT
					SET 
						AFD_CODE='".addslashes($row['AFD_CODE'])."', 
						BLOCK_CODE='".addslashes($row['BLOCK_CODE'])."', 
						SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', 
						ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
						ROTASI_SMS1 = REPLACE('".addslashes($rotasi['SMS1'])."',',',''), 
						ROTASI_SMS2 = REPLACE('".addslashes($rotasi['SMS2'])."',',',''), 
						TOTAL_RP_QTY = REPLACE('".$row['BASIC_TOTAL_RP_QTY']."',',',''), 
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
						PLAN_SMS1 = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SMS1'])."',',',''), 
						PLAN_SMS2 = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SMS2'])."',',',''), 
						PLAN_SETAHUN = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SETAHUN'])."',',',''), 
						DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''), DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''), 
						DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''), DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''), 
						DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''), DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''), 
						DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''), DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''), 
						DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''), DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''), 
						DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''), DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''), 
						DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''), 
						COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''), MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', 
						MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
						RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''), 
						RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
						TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."' -- //<!-- TIPE NORMA -->
					WHERE 
						TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
					AND COST_ELEMENT = '".addslashes($costElement)."';
				";
				
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
        $result = true;
		
		//jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
		$row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
		
		//cari summary total cost
		$sql = "
			SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, SUM(DIS_JAN) DIS_JAN, SUM(DIS_FEB) DIS_FEB, SUM(DIS_MAR) 
				   DIS_MAR, SUM(DIS_APR) DIS_APR, SUM(DIS_MAY) DIS_MAY, SUM(DIS_JUN) DIS_JUN, SUM(DIS_JUL) DIS_JUL, SUM(DIS_AUG) DIS_AUG, SUM(DIS_SEP) DIS_SEP, 
				   SUM(DIS_OCT) DIS_OCT, SUM(DIS_NOV) DIS_NOV, SUM(DIS_DEC) DIS_DEC, SUM(DIS_SETAHUN) DIS_SETAHUN, MAX(ROTASI_SMS1) ROTASI_SMS1, 
				   MAX(ROTASI_SMS2) ROTASI_SMS2, MAX(PLAN_SETAHUN) PLAN_SETAHUN, SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, SUM(COST_SMS1) COST_SMS1, SUM(COST_SMS2) COST_SMS2,
				   MAX(PLAN_SMS1) PLAN_SMS1, MAX(PLAN_SMS2) PLAN_SMS2
			FROM TR_RKT_COST_ELEMENT
			WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$row['PERIOD_BUDGET']."'
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND AFD_CODE = '".addslashes($row['AFD_CODE'])."'
				AND BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
				AND TIPE_TRANSAKSI = 'MANUAL_INFRA'
		";
		$summary = $this->_db->fetchRow($sql);
		
		//simpan total cost
		$sql = "UPDATE TR_RKT
				SET 
					AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
					SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
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
					PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''), 
					PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''), 
					PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''), 
					TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''), FLAG_TEMP = NULL,
					COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''), COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''), 
					COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''), COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''), 
					COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''), COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''), 
					COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''), COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''), 
					COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''), COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''), 
					COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''), COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''), 
					TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''), 
					COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''), 
					MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
					ROTASI_SMS1 = '".addslashes($summary['ROTASI_SMS1'])."', ROTASI_SMS2 = '".addslashes($summary['ROTASI_SMS2'])."', 
					TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''), TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
					UPDATE_USER = '{$this->_userName}', UPDATE_TIME = SYSDATE,
					TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."' -- //<!-- TIPE NORMA -->
				WHERE
					TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
				";

		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
			
        return $result;
    }
	
	
	public function saveRotation($row = array())
	{
		//jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
		$row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
		
		$sql = "
			UPDATE TR_RKT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				TIPE_TRANSAKSI = 'MANUAL_INFRA',
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
				ROTASI_SMS1 = NULL, 
				ROTASI_SMS2 = NULL, 
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
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
	//simpan temp data
	public function saveTemp($row = array())
    { 
        $result = true;		
		
		//jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
		$row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
		
		$sql = "
			UPDATE TR_RKT_COST_ELEMENT
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				RP_ROTASI_SMS1 = NULL,
				RP_ROTASI_SMS2 = NULL,
				TOTAL_RP_QTY = NULL,
				ROTASI_SMS1 = NULL,
				ROTASI_SMS2 = NULL,
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
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "
				UPDATE TR_RKT
				SET DELETE_USER = '{$this->_userName}',
					DELETE_TIME = SYSDATE
				WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'
			";
			$this->_db->query($sql);
			$this->_db->commit();			
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'RKT MANUAL - INFRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'RKT MANUAL - INFRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

