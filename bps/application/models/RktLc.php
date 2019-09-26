<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT LC
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region dan maturity stage
						- getData					: YIR 22/07/2013	: ambil data dari DB
						- getList					: YIR 22/07/2013	: menampilkan list RKT LC
						- getActivityClass			: YIR 22/07/2013	: get activity class dari norma biaya
						- getLandType				: YIR 22/07/2013	: get land type
						- getActivityName			: YIR 22/07/2013	: get activity name
						- getTopography				: YIR 22/07/2013	: get topography
						- getSumberBiaya			: YIR 22/07/2013	: get sumber biaya
						- getDataDownload			: YIR 22/07/2013	: ambil data untuk didownload ke excel dari DB
						- saveTemp					: YIR 22/07/2013	: hapus data di RKT LC & RKT LC COST ELEMENT, insert inputan user di RKT LC
						- calCostElement			: YIR 23/07/2013	: hitung per cost element
						- calTotalCost				: YIR 23/07/2013	: hitung total cost
						- delete					: YIR 23/07/2013	: hapus data
						- getDataInheritance		: YIR 17/07/2014	: get RKT LC per-BA dan Act Code untuk keperluan inheritance Data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	24/06/2014
Revisi				:	
	SID 20/06/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function calCostElement, calTotalCost, saveTemp, delete
						- perubahan query pengambilan data di getData & getDataDownload
=========================================================================================================================
*/
class Application_Model_RktLc
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
	
	public function getDataInheritance($params = array())
	{	
		$query = "
			SELECT rkt.*
			FROM TR_RKT_LC rkt
			WHERE DELETE_TIME IS NULL
		";
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND PERIOD_BUDGET = TO_DATE ('01-01-".$params['budgetperiod']."', 'DD-MM-RRRR')
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND PERIOD_BUDGET = TO_DATE ('01-01-".$params['PERIOD_BUDGET']."', 'DD-MM-RRRR')
            ";
		}else{
			$query .= "
                AND PERIOD_BUDGET = TO_DATE ('".$this->_period."', 'DD-MM-RRRR')
            ";
		}
		
		if ( ($params['activity_code'] != '') && ($params['ACT_CODE'] != '') ){
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['activity_code'].",".$params['ACT_CODE']."') 
            ";
		}else{
			if ($params['activity_code'] != '') {
				$query .= "
					AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['activity_code']."') 
				";
			}
			
			//jika diupdate dari RKT VRA, filter berdasarkan activity
			if ($params['ACT_CODE'] != '') {
				$query .= "
					AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."') 
				";
			}
		}
		
		//jika diupdate dari RKT VRA, filter berdasarkan BA
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."') 
            ";
        }
		
		
		
		//jika diupdate dari norma biaya, filter berdasarkan BA
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND rkt.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['TOPOGRAPHY'] != '') {
			$query .= "
                AND rkt.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
		if ($params['ACTIVITY_CLASS'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
		
		return $query;
	}
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
				   ROWNUM,
				   rkt.TRX_RKT_CODE, 
				   TO_CHAR(rkt.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET,
				   ORG.REGION_CODE,
				   rkt.BA_CODE, 
				   rkt.AFD_CODE,
				   rkt.ACTIVITY_CODE, 
				   act.DESCRIPTION as ACTIVITY_DESC,
				   rkt.ACTIVITY_CLASS, 
				   rkt.LAND_TYPE, 
				   rkt.TOPOGRAPHY, 
				   rkt.SUMBER_BIAYA, 
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
				   rkt.PLAN_SETAHUN, 
				   rkt.TOTAL_RP_QTY as TOTALRPQTY, 
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
				   rkt.COST_SETAHUN,
				   rkt.FLAG_TEMP,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
					    AND PARAMETER_VALUE_CODE = rkt.TOPOGRAPHY
				   ) as TOPOGRAPHY_DESC,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'LAND_TYPE' 
					 AND PARAMETER_VALUE_CODE = rkt.LAND_TYPE
				   ) as LAND_TYPE_DESC
			FROM TR_RKT_LC rkt
			LEFT JOIN TM_ORGANIZATION ORG 
				ON rkt.BA_CODE = ORG.BA_CODE 
			LEFT JOIN TM_ACTIVITY act
				ON act.ACTIVITY_CODE = rkt.ACTIVITY_CODE
			WHERE rkt.DELETE_USER IS NULL
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
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['activity_code']."')
            ";
        }
		//jika diupdate dari norma biaya, filter berdasarkan BA
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan tipe tanah
		if ($params['LAND_TYPE'] != '') {
			$query .= "
                AND rkt.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan topografi
		if ($params['TOPOGRAPHY'] != '') {
			$query .= "
                AND rkt.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
        }
		
		//jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
		if ($params['ACTIVITY_CLASS'] != '') {
			$query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.LAND_TYPE, rkt.TOPOGRAPHY, rkt.SUMBER_BIAYA, rkt.ACTIVITY_CLASS";
		
		return $query;
	}
	
	//menampilkan list RKT LC
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

        $rows = $this->_db->fetchAll("{$begin} {$this->getData($params)} {$end}");
		
		//ambil sumber data
		$par['PERIOD_BUDGET'] = $params['budgetperiod'];
		$par['BA_CODE'] = $params['key_find'];
		$par['ACTIVITY_CODE'] = $params['activity_code'];
		$result['biayaExkternal'] = $this->_formula->cekSumberBiayaExternal($par);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
            }
        }

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
					AND ACTIVITY_CODE = '".$params['activity_code']."' 
					AND COST_ELEMENT = 'LABOUR' 
				UNION 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_HARGA_BORONG 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['activity_code']."' 
			)";
		
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
	
	//get land type
    public function getLandType($params = array())
    {
		$value = array();
		$query = "
			SELECT T.PARAMETER_VALUE_CODE,T.PARAMETER_VALUE 
			FROM T_PARAMETER_VALUE T 
			WHERE PARAMETER_CODE LIKE 'LAND_TYPE' 
				AND DELETE_USER IS NULL
				AND PARAMETER_VALUE <> 'ALL'
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
	
	//get activity name
	public function getActivityName($params = array())
    {
		$value = array();
		$query = "
			SELECT DESCRIPTION, UOM 
			FROM TM_ACTIVITY T 
			WHERE ACTIVITY_CODE='".$params['activitycode']."'
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
	
	//get topography
	public function getTopography($params = array())
    {
		$value = array();
		$query = "
			SELECT T.PARAMETER_VALUE_CODE,T.PARAMETER_VALUE 
			FROM T_PARAMETER_VALUE T 
			WHERE PARAMETER_CODE LIKE 'TOPOGRAPHY' 
				AND DELETE_USER IS NULL
				AND PARAMETER_VALUE <> 'ALL'
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
	
	//get sumber biaya
	public function getSumberBiaya($params = array())
    {
		$row['BA_CODE'] = $params['key_find'];
		$row['PERIOD_BUDGET'] = $params['budgetperiod'];
		$row['ACTIVITY_CODE'] = $params['activity_code'];
		
		$return = $this->_formula->cekSumberBiayaExternal($row);
		return $return;
	}
	
	//get Proyeksi
	public function getProyeksi($params = array())
    {
		$row['BA_CODE'] = $params['key_find'];
		$row['PERIOD_BUDGET'] = $params['budgetperiod'];
		$row['ACTIVITY_CODE'] = $params['activity_code'];
		
		$query = "SELECT AFD_CODE 
                    FROM (
                        SELECT DISTINCT HS.AFD_CODE
                        FROM TM_HECTARE_STATEMENT HS 
                        LEFT JOIN (SELECT DISTINCT AFD_CODE,BA_CODE FROM TR_RKT_LC WHERE DELETE_USER IS NULL) a ON
                            a.BA_CODE=HS.BA_CODE
                        WHERE DELETE_USER IS NULL
                            AND UPPER(HS.BA_CODE) = '".$row['BA_CODE']."'
                            AND HS.STATUS = 'PROYEKSI'
                             AND TO_CHAR(HS.PERIOD_BUDGET,'RRRR')= '".$row['PERIOD_BUDGET']."')";

		$sql = "SELECT COUNT(*) FROM ({$query})";
        $value['count'] = $this->_db->fetchOne($sql);
		return $value;
	}
	
	//ambil data untuk didownload ke excel dari DB
    public function getDataDownload($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
				   ROWNUM,
				   rkt.TRX_RKT_CODE, 
				   TO_CHAR(rkt.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET,
				   ORG.REGION_CODE,
				   rkt.BA_CODE, 
				   rkt.AFD_CODE,
				   rkt.ACTIVITY_CODE, 
				   act.DESCRIPTION as ACTIVITY_DESC,
				   rkt.ACTIVITY_CLASS, 
				   rkt.LAND_TYPE, 
				   rkt.TOPOGRAPHY, 
				   rkt.SUMBER_BIAYA, 
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
				   rkt.PLAN_SETAHUN, 
				   rkt.TOTAL_RP_QTY as TOTALRPQTY, 
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
				   rkt.COST_SETAHUN,
				   rkt.FLAG_TEMP,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
					    AND PARAMETER_VALUE_CODE = rkt.TOPOGRAPHY
				   ) as TOPOGRAPHY_DESC,
				   (
					SELECT PARAMETER_VALUE 
					FROM T_PARAMETER_VALUE 
					WHERE PARAMETER_CODE = 'LAND_TYPE' 
					 AND PARAMETER_VALUE_CODE = rkt.LAND_TYPE
				   ) as LAND_TYPE_DESC
			FROM TR_RKT_LC rkt
			LEFT JOIN TM_ORGANIZATION ORG 
				ON rkt.BA_CODE = ORG.BA_CODE 
			LEFT JOIN TM_ACTIVITY act
				ON act.ACTIVITY_CODE = rkt.ACTIVITY_CODE
			WHERE rkt.DELETE_USER IS NULL
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
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['activity_code']."')
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.LAND_TYPE, rkt.TOPOGRAPHY, rkt.SUMBER_BIAYA, rkt.ACTIVITY_CLASS
		";
			
		return $query;
	}
	
	//hapus data di RKT LC & RKT LC COST ELEMENT, insert inputan user di RKT LC
	public function saveTemp($row = array())
    { 
		$result = true;
				
		$delActivity = ($row['ACTIVITY_CODE']) ? $row['ACTIVITY_CODE'] : '--';
		$delActivityClass = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : '--';
		$delAfd = ($row['AFD_CODE']) ? $row['AFD_CODE'] : '--';
		$delLandType = ($row['LAND_TYPE']) ? $row['LAND_TYPE'] : '--';
		$delTopo = ($row['TOPOGRAPHY']) ? $row['TOPOGRAPHY'] : '--';
		$delSumberBiaya = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : '--';
		
		$delOldActivity = ($row['OLD_ACTIVITY_CODE']) ? $row['OLD_ACTIVITY_CODE'] : '--';
		$delOldActivityClass = ($row['OLD_ACTIVITY_CLASS']) ? $row['OLD_ACTIVITY_CLASS'] : '--';
		$delOldAfd = ($row['OLD_AFD_CODE']) ? $row['OLD_AFD_CODE'] : '--';
		$delOldLandType = ($row['OLD_LAND_TYPE']) ? $row['OLD_LAND_TYPE'] : '--';
		$delOldTopo = ($row['OLD_TOPOGRAPHY']) ? $row['OLD_TOPOGRAPHY'] : '--';
		$delOldSumberBiaya = ($row['OLD_SUMBER_BIAYA']) ? $row['OLD_SUMBER_BIAYA'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_LC
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}';
			
			DELETE FROM TR_RKT_LC_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_LC
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}';
			
			DELETE FROM TR_RKT_LC_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}';
		";
		
		//insert data input baru sebagai temp data
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-".
					strtoupper(addslashes($row['AFD_CODE'])) ."-RKT007-".
					addslashes($row['ACTIVITY_CODE']) ."-".
					substr(addslashes($row['ACTIVITY_CLASS']), 0,3) ."-".
					substr(addslashes($row['LAND_TYPE']), 0,3) ."-".
					substr(addslashes($row['TOPOGRAPHY']), 0,3) ."-".
					substr(addslashes($row['SUMBER_BIAYA']), 0,3);
					
		$sql.= "
			INSERT INTO TR_RKT_LC (
				TRX_RKT_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				AFD_CODE, 
				ACTIVITY_CODE, 
				ACTIVITY_CLASS, 
				LAND_TYPE, 
				TOPOGRAPHY, 
				SUMBER_BIAYA, 
				PLAN_JAN, 
				PLAN_FEB, 
				PLAN_MAR, 
				PLAN_APR, 
				PLAN_MAY, 
				PLAN_JUN, 
				PLAN_JUL, 
				PLAN_AUG, 
				PLAN_SEP, 
				PLAN_OCT, 
				PLAN_NOV, 
				PLAN_DEC, 
				PLAN_SETAHUN,
				FLAG_TEMP, 
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".$trx_code."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',
				UPPER('".addslashes($row['AFD_CODE'])."'),	
				'".addslashes($row['ACTIVITY_CODE'])."',
				'".addslashes($row['ACTIVITY_CLASS'])."',
				'".addslashes($row['LAND_TYPE'])."', 
				'".addslashes($row['TOPOGRAPHY'])."', 
				'".addslashes($row['SUMBER_BIAYA'])."', 
				REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SETAHUN'])."',',',''),
				'Y',
				'{$this->_userName}',
				SYSDATE
			);
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//hitung cost element
	public function calCostElement($costElement, $row = array())
    { 
		$delActivity = ($row['ACTIVITY_CODE']) ? $row['ACTIVITY_CODE'] : '--';
		$delActivityClass = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : '--';
		$delAfd = ($row['AFD_CODE']) ? $row['AFD_CODE'] : '--';
		$delLandType = ($row['LAND_TYPE']) ? $row['LAND_TYPE'] : '--';
		$delTopo = ($row['TOPOGRAPHY']) ? $row['TOPOGRAPHY'] : '--';
		$delSumberBiaya = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : '--';
		
		$delOldActivity = ($row['OLD_ACTIVITY_CODE']) ? $row['OLD_ACTIVITY_CODE'] : '--';
		$delOldActivityClass = ($row['OLD_ACTIVITY_CLASS']) ? $row['OLD_ACTIVITY_CLASS'] : '--';
		$delOldAfd = ($row['OLD_AFD_CODE']) ? $row['OLD_AFD_CODE'] : '--';
		$delOldLandType = ($row['OLD_LAND_TYPE']) ? $row['OLD_LAND_TYPE'] : '--';
		$delOldTopo = ($row['OLD_TOPOGRAPHY']) ? $row['OLD_TOPOGRAPHY'] : '--';
		$delOldSumberBiaya = ($row['OLD_SUMBER_BIAYA']) ? $row['OLD_SUMBER_BIAYA'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_LC_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}'
				AND NVL(COST_ELEMENT,'--')  = '{$costElement}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_LC_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}'
				AND NVL(COST_ELEMENT,'--')  = '{$costElement}';
		";
		
        //hitung cost element
		$rp_qty = $this->_formula->cal_RktLc_CostElement($costElement,$row);//tn_biaya/borongan alert
		//echo $rp_qty;die();
		$row['RP_QTY'] = $rp_qty;
		$distribusi = $this->_formula->cal_RktLc_DistribusiTahunBerjalan($row);
		
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-".
					strtoupper(addslashes($row['AFD_CODE'])) ."-RKT007-".
					addslashes($row['ACTIVITY_CODE']) ."-".
					substr(addslashes($row['ACTIVITY_CLASS']), 0,3) ."-".
					substr(addslashes($row['LAND_TYPE']), 0,3) ."-".
					substr(addslashes($row['TOPOGRAPHY']), 0,3) ."-".
					substr(addslashes($row['SUMBER_BIAYA']), 0,3);
		$sql .= "
			INSERT INTO TR_RKT_LC_COST_ELEMENT (
				TRX_RKT_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				AFD_CODE, 
				ACTIVITY_CODE, 
				ACTIVITY_CLASS, 
				LAND_TYPE, 
				TOPOGRAPHY, 
				SUMBER_BIAYA, 
				COST_ELEMENT, 
				PLAN_JAN, 
				PLAN_FEB, 
				PLAN_MAR, 
				PLAN_APR, 
				PLAN_MAY, 
				PLAN_JUN, 
				PLAN_JUL, 
				PLAN_AUG, 
				PLAN_SEP, 
				PLAN_OCT, 
				PLAN_NOV, 
				PLAN_DEC, 
				PLAN_SETAHUN, 
				TOTAL_RP_QTY, 
				DIS_COST_JAN, 
				DIS_COST_FEB, 
				DIS_COST_MAR, 
				DIS_COST_APR, 
				DIS_COST_MAY, 
				DIS_COST_JUN, 
				DIS_COST_JUL, 
				DIS_COST_AUG, 
				DIS_COST_SEP, 
				DIS_COST_OCT, 
				DIS_COST_NOV, 
				DIS_COST_DEC, 
				COST_SMS1, 
				COST_SMS2, 
				DIS_COST_SETAHUN, 
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".addslashes($trx_code)."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',
				UPPER('".addslashes($row['AFD_CODE'])."'),	
				'".addslashes($row['ACTIVITY_CODE'])."',
				'".addslashes($row['ACTIVITY_CLASS'])."',
				'".addslashes($row['LAND_TYPE'])."', 
				'".addslashes($row['TOPOGRAPHY'])."', 
				'".addslashes($row['SUMBER_BIAYA'])."', 
				'".$costElement."',
				REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SETAHUN'])."',',',''),
				{$rp_qty}, 
				(".str_replace(',','',addslashes($distribusi['COST_JAN']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_FEB']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_MAR']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_APR']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_MAY']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_JUN']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_JUL']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_AUG']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_SEP']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_OCT']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_NOV']))."), 
				(".str_replace(',','',addslashes($distribusi['COST_DEC']))."), 
				".str_replace(',','',addslashes($distribusi['COST_SMS1'])).", 
				".str_replace(',','',addslashes($distribusi['COST_SMS2'])).",
				(".str_replace(',','',addslashes($distribusi['TOTAL_COST']))."),
				'{$this->_userName}',
				SYSDATE
			);
		";
		
        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
		$delActivity = ($row['ACTIVITY_CODE']) ? $row['ACTIVITY_CODE'] : '--';
		$delActivityClass = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : '--';
		$delAfd = ($row['AFD_CODE']) ? $row['AFD_CODE'] : '--';
		$delLandType = ($row['LAND_TYPE']) ? $row['LAND_TYPE'] : '--';
		$delTopo = ($row['TOPOGRAPHY']) ? $row['TOPOGRAPHY'] : '--';
		$delSumberBiaya = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : '--';
		
		$delOldActivity = ($row['OLD_ACTIVITY_CODE']) ? $row['OLD_ACTIVITY_CODE'] : '--';
		$delOldActivityClass = ($row['OLD_ACTIVITY_CLASS']) ? $row['OLD_ACTIVITY_CLASS'] : '--';
		$delOldAfd = ($row['OLD_AFD_CODE']) ? $row['OLD_AFD_CODE'] : '--';
		$delOldLandType = ($row['OLD_LAND_TYPE']) ? $row['OLD_LAND_TYPE'] : '--';
		$delOldTopo = ($row['OLD_TOPOGRAPHY']) ? $row['OLD_TOPOGRAPHY'] : '--';
		$delOldSumberBiaya = ($row['OLD_SUMBER_BIAYA']) ? $row['OLD_SUMBER_BIAYA'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_LC
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_LC
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}';
		";
		
		//insert data input baru sebagai temp data
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-".
					strtoupper(addslashes($row['AFD_CODE'])) ."-RKT007-".
					addslashes($row['ACTIVITY_CODE']) ."-".
					substr(addslashes($row['ACTIVITY_CLASS']), 0,3) ."-".
					substr(addslashes($row['LAND_TYPE']), 0,3) ."-".
					substr(addslashes($row['TOPOGRAPHY']), 0,3) ."-".
					substr(addslashes($row['SUMBER_BIAYA']), 0,3);
					
        //cari summary total cost
		$sqlsum = "
			SELECT NVL(SUM(DIS_COST_JAN), 0) COST_JAN, 
				   NVL(SUM(DIS_COST_FEB), 0) COST_FEB, 
				   NVL(SUM(DIS_COST_MAR), 0) COST_MAR, 
				   NVL(SUM(DIS_COST_APR), 0) COST_APR, 
				   NVL(SUM(DIS_COST_MAY), 0) COST_MAY, 
				   NVL(SUM(DIS_COST_JUN), 0) COST_JUN, 
				   NVL(SUM(DIS_COST_JUL), 0) COST_JUL, 
				   NVL(SUM(DIS_COST_AUG), 0) COST_AUG, 
				   NVL(SUM(DIS_COST_SEP), 0) COST_SEP, 
				   NVL(SUM(DIS_COST_OCT), 0) COST_OCT, 
				   NVL(SUM(DIS_COST_NOV), 0) COST_NOV, 
				   NVL(SUM(DIS_COST_DEC), 0) COST_DEC, 
				   NVL(SUM(DIS_COST_SETAHUN), 0) COST_SETAHUN, 
				   NVL(SUM(TOTAL_RP_QTY), 0) TOTAL_RP_QTY, 
				   NVL(SUM(COST_SMS1), 0) COST_SMS1, 
				   NVL(SUM(COST_SMS2), 0) COST_SMS2
			FROM TR_RKT_LC_COST_ELEMENT
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
		";
		$summary = $this->_db->fetchRow($sqlsum);
		
		$sql.= "
			INSERT INTO TR_RKT_LC (
				TRX_RKT_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				AFD_CODE, 
				ACTIVITY_CODE, 
				ACTIVITY_CLASS, 
				LAND_TYPE, 
				TOPOGRAPHY, 
				SUMBER_BIAYA, 
				PLAN_JAN, 
				PLAN_FEB, 
				PLAN_MAR, 
				PLAN_APR, 
				PLAN_MAY, 
				PLAN_JUN, 
				PLAN_JUL, 
				PLAN_AUG, 
				PLAN_SEP, 
				PLAN_OCT, 
				PLAN_NOV, 
				PLAN_DEC, 
				PLAN_SETAHUN,
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
				COST_SETAHUN,
				TOTAL_RP_QTY, 
				COST_SMS1, 
				COST_SMS2,
				FLAG_TEMP,
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".$trx_code."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',
				UPPER('".addslashes($row['AFD_CODE'])."'),	
				'".addslashes($row['ACTIVITY_CODE'])."',
				'".addslashes($row['ACTIVITY_CLASS'])."',
				'".addslashes($row['LAND_TYPE'])."', 
				'".addslashes($row['TOPOGRAPHY'])."', 
				'".addslashes($row['SUMBER_BIAYA'])."', 
				REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
				REPLACE('".addslashes($row['PLAN_SETAHUN'])."',',',''),
				(".str_replace(',','',addslashes($summary['COST_JAN']))."), 
				(".str_replace(',','',addslashes($summary['COST_FEB']))."), 
				(".str_replace(',','',addslashes($summary['COST_MAR']))."), 
				(".str_replace(',','',addslashes($summary['COST_APR']))."), 
				(".str_replace(',','',addslashes($summary['COST_MAY']))."), 
				(".str_replace(',','',addslashes($summary['COST_JUN']))."), 
				(".str_replace(',','',addslashes($summary['COST_JUL']))."), 
				(".str_replace(',','',addslashes($summary['COST_AUG']))."), 
				(".str_replace(',','',addslashes($summary['COST_SEP']))."), 
				(".str_replace(',','',addslashes($summary['COST_OCT']))."), 
				(".str_replace(',','',addslashes($summary['COST_NOV']))."), 
				(".str_replace(',','',addslashes($summary['COST_DEC']))."), 
				(".str_replace(',','',addslashes($summary['COST_SETAHUN']))."),
				REPLACE('{$summary['TOTAL_RP_QTY']}',',',''), 
				".str_replace(',','',addslashes($summary['COST_SMS1'])).", 
				".str_replace(',','',addslashes($summary['COST_SMS2'])).",
				NULL,
				'{$this->_userName}',
				SYSDATE
			);
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	public function delete($row = array())
    {
		$delActivity = ($row['ACTIVITY_CODE']) ? $row['ACTIVITY_CODE'] : '--';
		$delActivityClass = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : '--';
		$delAfd = ($row['AFD_CODE']) ? $row['AFD_CODE'] : '--';
		$delLandType = ($row['LAND_TYPE']) ? $row['LAND_TYPE'] : '--';
		$delTopo = ($row['TOPOGRAPHY']) ? $row['TOPOGRAPHY'] : '--';
		$delSumberBiaya = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : '--';
		
		$delOldActivity = ($row['OLD_ACTIVITY_CODE']) ? $row['OLD_ACTIVITY_CODE'] : '--';
		$delOldActivityClass = ($row['OLD_ACTIVITY_CLASS']) ? $row['OLD_ACTIVITY_CLASS'] : '--';
		$delOldAfd = ($row['OLD_AFD_CODE']) ? $row['OLD_AFD_CODE'] : '--';
		$delOldLandType = ($row['OLD_LAND_TYPE']) ? $row['OLD_LAND_TYPE'] : '--';
		$delOldTopo = ($row['OLD_TOPOGRAPHY']) ? $row['OLD_TOPOGRAPHY'] : '--';
		$delOldSumberBiaya = ($row['OLD_SUMBER_BIAYA']) ? $row['OLD_SUMBER_BIAYA'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		/*
		$sql = "
			UPDATE TR_RKT_LC
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}';
			
			UPDATE TR_RKT_LC_COST_ELEMENT
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delSumberBiaya}';
		";
		*/
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			UPDATE TR_RKT_LC
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}';
			
			UPDATE TR_RKT_LC_COST_ELEMENT
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(ACTIVITY_CODE ,'--') = '{$delOldActivity}' 
				AND NVL(ACTIVITY_CLASS,'--')  = '{$delOldActivityClass}'
				AND NVL(AFD_CODE ,'--') = '{$delOldAfd}' 
				AND NVL(LAND_TYPE,'--')  = '{$delOldLandType}'
				AND NVL(TOPOGRAPHY ,'--') = '{$delOldTopo}' 
				AND NVL(SUMBER_BIAYA,'--')  = '{$delOldSumberBiaya}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
}

