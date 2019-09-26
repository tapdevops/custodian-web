<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk RKT Perkerasan Jalan
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT Perkerasan Jalan
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
						- saveRotation		: YIR 25/06/2014	: simpan inputan rotasi
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	29/07/2013
Update Terakhir		:	29/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktTanam
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
	
	//ambil data dari DB
    public function getData($params = array())
    {
		if ($params['src_coa_code'] != '') {
			$where = "
                AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
            ";
        }
		
		$query = "
			SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
				   rkt.FLAG_SITE,
				   rkt.FLAG_TEMP,
				   rkt.TRX_RKT_CODE,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   rkt.BA_CODE,
				   rkt.AFD_CODE,
				   rkt.BLOCK_CODE,
				   hs.BLOCK_DESC,
				   rkt.TOTAL_RP_QTY,
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
					WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE rkt.TIPE_NORMA
				   END as TIPE_NORMA,
				   activity.DESCRIPTION ACTIVITY_DESC 
			FROM 
				TR_RKT rkt 
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
			AND rkt.TIPE_TRANSAKSI = 'TANAM' 
			AND hs.STATUS = 'PROYEKSI'
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
			$query .= "
                AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%') 
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
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL') && ($params['src_matstage_code'] <> 'undefined')) {
			$query .= "
                AND (
					UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
					OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
		";//die("query: ".$query);
		return $query;
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
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
					AND COST_ELEMENT = 'LABOUR' 
				UNION 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_HARGA_BORONG 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
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
	
	//menampilkan list RKT Tanam
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
		
		foreach ($params as $idx => $acti)
		{
			$sumberbiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
			$sumberbiaya['BA_CODE'] = $params['key_find'];
			$sumberbiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
		}
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($sumberbiaya);
				$result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//get sumber biaya
    public function getSumberBiaya($params = array())
    {
        $result = array();
        $sql = "SELECT PARAMETER_VALUE_CODE KODE, PARAMETER_VALUE NILAI FROM T_PARAMETER_VALUE WHERE PARAMETER_CODE = 'SUMBER_BIAYA'";
        
        $rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
			}
        }
		$result['count'] = count($rows);
		

        return $result;
    }
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "SELECT ROWIDTOCHAR(ROWID) ROW_ID FROM TR_RKT 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') AND
							BA_CODE = '{$params['BA_CODE']}'AND
							AFD_CODE = '{$params['AFD_CODE']}'AND 
							BLOCK_CODE  = '{$params['BLOCK_CODE']}'AND
							ACTIVITY_CODE  = '{$params['ACTIVITY_CODE']}'
							";        
        $rows = $this->_db->fetchOne ($sql);
        return $rows;
    }

	//hitung data
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
	
	//hitung cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		$cost_sms1 = $cost_sms2 = 0;
		
		//get sumber biaya
		$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
		
		//<!-- TIPE NORMA -->
		//tipe norma
		$row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
		
		//hitung rotasi
		$rotasi = $this->_formula->get_RktManual_Rotasi($row);
		$row['ROTASI_SMS1'] = $rotasi['SMS1'];
		$row['ROTASI_SMS2'] = $rotasi['SMS2'];
		
		//hitung cost element
		$dis = $this->_formula->cal_RktTanam_DistribusiHa($row);
		
		$mon = $this->_formula->cal_costElement_RktTanam($costElement, $row);
		
		//save hasil cost element
			$row['COST_TOTAL_RP_QTY'] = $mon[1];
			
			$jan = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JAN']; $cost_sms1 += $jan;
			$feb = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_FEB']; $cost_sms1 += $feb;
			$mar = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_MAR']; $cost_sms1 += $mar;
			$apr = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_APR']; $cost_sms1 += $apr;
			$may = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_MAY']; $cost_sms1 += $may;
			$jun = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JUN']; $cost_sms1 += $jun;
			$jul = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JUL']; $cost_sms2 += $jul;
			$aug = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_AUG']; $cost_sms2 += $aug;
			$sep = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_SEP']; $cost_sms2 += $sep;
			$oct = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_OCT']; $cost_sms2 += $oct;
			$nov = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_NOV']; $cost_sms2 += $nov;
			$dec = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_DEC']; $cost_sms2 += $dec;
			
			$tahun = $dis['PLAN_JAN'] + $dis['PLAN_FEB'] + $dis['PLAN_MAR'] + $dis['PLAN_APR'] + $dis['PLAN_MAY'] + $dis['PLAN_JUN'] + $dis['PLAN_JUL'] + $dis['PLAN_AUG'] + $dis['PLAN_SEP'] + $dis['PLAN_OCT'] + $dis['PLAN_NOV'] + $dis['PLAN_DEC'];
			$biaya = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $dec;
			
			$sql = "
				UPDATE TR_RKT_COST_ELEMENT 
				SET 
					AFD_CODE='".addslashes($row['AFD_CODE'])."', 
					BLOCK_CODE='".addslashes($row['BLOCK_CODE'])."', 
					SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', 
					TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
					ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
					ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
					ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
					TOTAL_RP_QTY = REPLACE('".$row['COST_TOTAL_RP_QTY']."',',',''), 
					PLAN_JAN = REPLACE('".addslashes($dis['PLAN_JAN'])."',',',''), 
					PLAN_FEB = REPLACE('".addslashes($dis['PLAN_FEB'])."',',',''), 
					PLAN_MAR = REPLACE('".addslashes($dis['PLAN_MAR'])."',',',''), 
					PLAN_APR = REPLACE('".addslashes($dis['PLAN_APR'])."',',',''), 
					PLAN_MAY = REPLACE('".addslashes($dis['PLAN_MAY'])."',',',''), 
					PLAN_JUN = REPLACE('".addslashes($dis['PLAN_JUN'])."',',',''), 
					PLAN_JUL = REPLACE('".addslashes($dis['PLAN_JUL'])."',',',''), 
					PLAN_AUG = REPLACE('".addslashes($dis['PLAN_AUG'])."',',',''), 
					PLAN_SEP = REPLACE('".addslashes($dis['PLAN_SEP'])."',',',''), 
					PLAN_OCT = REPLACE('".addslashes($dis['PLAN_OCT'])."',',',''), 
					PLAN_NOV = REPLACE('".addslashes($dis['PLAN_NOV'])."',',',''), 
					PLAN_DEC = REPLACE('".addslashes($dis['PLAN_DEC'])."',',',''), 
					PLAN_SETAHUN = REPLACE('".addslashes($tahun)."',',',''), 
					DIS_JAN = REPLACE('".addslashes($jan)."',',',''), DIS_FEB = REPLACE('".addslashes($feb)."',',',''), 
					DIS_MAR = REPLACE('".addslashes($mar)."',',',''), DIS_APR = REPLACE('".addslashes($apr)."',',',''), 
					DIS_MAY = REPLACE('".addslashes($may)."',',',''), DIS_JUN = REPLACE('".addslashes($jun)."',',',''), 
					DIS_JUL = REPLACE('".addslashes($jul)."',',',''), DIS_AUG = REPLACE('".addslashes($aug)."',',',''), 
					DIS_SEP = REPLACE('".addslashes($sep)."',',',''), DIS_OCT = REPLACE('".addslashes($oct)."',',',''), 
					DIS_NOV = REPLACE('".addslashes($nov)."',',',''), DIS_DEC = REPLACE('".addslashes($dec)."',',',''), 
					DIS_SETAHUN = REPLACE('".addslashes($biaya)."',',',''), COST_SMS1 = REPLACE('".addslashes($cost_sms1)."',',',''), 
					COST_SMS2 = REPLACE('".addslashes($cost_sms2)."',',',''), 
					MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', 
					MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."'
				WHERE 
					TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
					AND COST_ELEMENT = '".addslashes($costElement)."';
				";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
        $result = true;
		
		//get sumber biaya
		$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
			
		//cari summary total cost
		$sqlsum = "
			SELECT SUM (TOTAL_RP_QTY) TOTAL_RP_QTY,
				   SUM (DIS_JAN) DIS_JAN,
				   SUM (DIS_FEB) DIS_FEB,
				   SUM (DIS_MAR) DIS_MAR,
				   SUM (DIS_APR) DIS_APR,
				   SUM (DIS_MAY) DIS_MAY,
				   SUM (DIS_JUN) DIS_JUN,
				   SUM (DIS_JUL) DIS_JUL,
				   SUM (DIS_AUG) DIS_AUG,
				   SUM (DIS_SEP) DIS_SEP,
				   SUM (DIS_OCT) DIS_OCT,
				   SUM (DIS_NOV) DIS_NOV,
				   SUM (DIS_DEC) DIS_DEC,
				   SUM (DIS_SETAHUN) DIS_SETAHUN,
				   SUM (COST_SMS1) COST_SMS1,
				   SUM (COST_SMS2) COST_SMS2,
				   MAX (PLAN_JAN) PLAN_JAN, 
				   MAX (PLAN_FEB) PLAN_FEB, 
				   MAX (PLAN_MAR) PLAN_MAR, 
				   MAX (PLAN_APR) PLAN_APR, 
				   MAX (PLAN_MAY) PLAN_MAY, 
				   MAX (PLAN_JUN) PLAN_JUN, 
				   MAX (PLAN_JUL) PLAN_JUL, 					
				   MAX (PLAN_AUG) PLAN_AUG, 
				   MAX (PLAN_SEP) PLAN_SEP, 
				   MAX (PLAN_OCT) PLAN_OCT, 
				   MAX (PLAN_NOV) PLAN_NOV, 
				   MAX (PLAN_DEC) PLAN_DEC, 
				   MAX (PLAN_SETAHUN) PLAN_SETAHUN
			  FROM TR_RKT_COST_ELEMENT
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'";
		$summary = $this->_db->fetchRow($sqlsum);
		
		//simpan total cost
		$sql = "UPDATE TR_RKT
					SET 
						AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
						SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
						PLAN_JAN = REPLACE('".addslashes($summary['PLAN_JAN'])."',',',''), 
						PLAN_FEB = REPLACE('".addslashes($summary['PLAN_FEB'])."',',',''), 
						PLAN_MAR = REPLACE('".addslashes($summary['PLAN_MAR'])."',',',''), 
						PLAN_APR = REPLACE('".addslashes($summary['PLAN_APR'])."',',',''), 
						PLAN_MAY = REPLACE('".addslashes($summary['PLAN_MAY'])."',',',''), 
						PLAN_JUN = REPLACE('".addslashes($summary['PLAN_JUN'])."',',',''), 
						PLAN_JUL = REPLACE('".addslashes($summary['PLAN_JUL'])."',',',''), 
						PLAN_AUG = REPLACE('".addslashes($summary['PLAN_AUG'])."',',',''), 
						PLAN_SEP = REPLACE('".addslashes($summary['PLAN_SEP'])."',',',''), 
						PLAN_OCT = REPLACE('".addslashes($summary['PLAN_OCT'])."',',',''), 
						PLAN_NOV = REPLACE('".addslashes($summary['PLAN_NOV'])."',',',''), 
						PLAN_DEC = REPLACE('".addslashes($summary['PLAN_DEC'])."',',',''), 
						PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''), 
						TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''), 
						COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''), COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''), 
						COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''), COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''), 
						COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''), COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''), 
						COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''), COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''), 
						COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''), COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''), 
						COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''), COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''), 
						TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''), 
						COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''), 
						MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
						FLAG_TEMP = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					WHERE
						TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
					";

        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//ambil data untuk didownload dari DB
    public function getDataDownload($params = array())
    {
		$query = "
SELECT ROWIDTOCHAR (HS.ROWID) AS ROW_ID,
       ROWNUM,
       TO_CHAR (HS.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
       HS.BA_CODE,
	   ORG.COMPANY_NAME,
       HS.AFD_CODE,
       HS.BLOCK_CODE,
       HS.LAND_TYPE,
       HS.TOPOGRAPHY,
       TO_CHAR (HS.TAHUN_TANAM, 'MM') AS BULAN_TANAM,
       TO_CHAR (HS.TAHUN_TANAM, 'MM-RRRR') AS TAHUN_TANAM,
       HS.HA_PLANTED,
       HS.POKOK_TANAM,
       HS.SPH,
       RKT.ACTIVITY_CODE,	   
	   TMA.DESCRIPTION,
       RKT.ACTIVITY_CLASS,
       RKT.SUMBER_BIAYA,
       RKT.TOTAL_RP_SETAHUN,
       RKT.TOTAL_RP_QTY,
       RKT.PLAN_JAN,
       RKT.PLAN_FEB,
       RKT.PLAN_MAR,
       RKT.PLAN_APR,
       RKT.PLAN_MAY,
       RKT.PLAN_JUN,
       RKT.PLAN_JUL,
       RKT.PLAN_AUG,
       RKT.PLAN_SEP,
       RKT.PLAN_OCT,
       RKT.PLAN_NOV,
       RKT.PLAN_DEC,
       RKT.PLAN_SETAHUN,
       RKT.COST_JAN,
       RKT.COST_FEB,
       RKT.COST_MAR,
       RKT.COST_APR,
       RKT.COST_MAY,
       RKT.COST_JUN,
       RKT.COST_JUL,
       RKT.COST_AUG,
       RKT.COST_SEP,
       RKT.COST_OCT,
       RKT.COST_NOV,
       RKT.COST_DEC,
       RKT.TRX_RKT_CODE       
  FROM TM_HECTARE_STATEMENT HS
  LEFT JOIN 
  (SELECT RKT.PERIOD_BUDGET,
       RKT.BA_CODE,
       RKT.ACTIVITY_CODE,
       RKT.AFD_CODE,
       RKT.BLOCK_CODE,
       RKT.TIPE_TRANSAKSI,
       RKT.ACTIVITY_CLASS,
       RKT.SUMBER_BIAYA,
       RKT.TOTAL_RP_SETAHUN,
       RKT.TOTAL_RP_QTY,
       RKT.PLAN_JAN,
       RKT.PLAN_FEB,
       RKT.PLAN_MAR,
       RKT.PLAN_APR,
       RKT.PLAN_MAY,
       RKT.PLAN_JUN,
       RKT.PLAN_JUL,
       RKT.PLAN_AUG,
       RKT.PLAN_SEP,
       RKT.PLAN_OCT,
       RKT.PLAN_NOV,
       RKT.PLAN_DEC,
       RKT.PLAN_SETAHUN,
       RKT.COST_JAN,
       RKT.COST_FEB,
       RKT.COST_MAR,
       RKT.COST_APR,
       RKT.COST_MAY,
       RKT.COST_JUN,
       RKT.COST_JUL,
       RKT.COST_AUG,
       RKT.COST_SEP,
       RKT.COST_OCT,
       RKT.COST_NOV,
       RKT.COST_DEC,
       RKT.TRX_RKT_CODE,
	   RKT.TIPE_NORMA
  FROM TR_RKT RKT
  WHERE TO_CHAR (RKT.PERIOD_BUDGET, 'RRRR') = '".$params['budgetperiod']."'
  AND RKT.BA_CODE = '".$params['key_find']."'
  AND RKT.ACTIVITY_CODE = '".$params['src_coa_code']."'
  AND RKT.TIPE_TRANSAKSI = 'TANAM') RKT
  ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
  AND HS.BA_CODE = RKT.BA_CODE
  AND HS.AFD_CODE = RKT.AFD_CODE
  AND HS.BLOCK_CODE = RKT.BLOCK_CODE
  LEFT JOIN TM_ACTIVITY_MAPPING AM
  ON RKT.ACTIVITY_CODE = AM.ACTIVITY_CODE
  AND AM.ACTIVITY_CODE = '".$params['src_coa_code']."'
LEFT JOIN TM_ORGANIZATION ORG
ON HS.BA_CODE = ORG.BA_CODE
LEFT JOIN TM_ACTIVITY TMA
ON RKT.ACTIVITY_CODE = TMA.ACTIVITY_CODE
 WHERE     HS.STATUS = 'PROYEKSI'
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
                AND UPPER(HS.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }else{
			$query .= "
                AND UPPER(HS.AFD_CODE) LIKE UPPER('%%')
            ";
		}
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] <> 'ALL')) {
			$query .= "
                AND (
					UPPER(HS.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
					OR UPPER(HS.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY hs.BA_CODE, hs.AFD_CODE, hs.BLOCK_CODE
		";
		return $query;
	}

	//simpan inputan rotasi
	public function saveRotation($row = array())
	{
		//get sumber biaya
		$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
		
		$sql = "
			UPDATE TR_RKT
				SET 
					AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
					SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
					TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
					TIPE_TRANSAKSI = 'TANAM',
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
					PLAN_SETAHUN = NULL, 
					TOTAL_RP_QTY = NULL, 
					COST_JAN = NULL, COST_FEB = NULL, 
					COST_MAR = NULL, COST_APR = NULL, 
					COST_MAY = NULL, COST_JUN = NULL, 
					COST_JUL = NULL, COST_AUG = NULL, 
					COST_SEP = NULL, COST_OCT = NULL, 
					COST_NOV = NULL, COST_DEC = NULL, 
					TOTAL_RP_SETAHUN = NULL, COST_SMS1 = NULL, 
					COST_SMS2 = NULL, 
					MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
					FLAG_TEMP = 'Y',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				WHERE
					TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
	//reset perhitungan
	public function saveTemp($row = array())
    { 
		//get sumber biaya
		$row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
		
        $sql = "
			UPDATE TR_RKT_COST_ELEMENT 
				SET 
					AFD_CODE='".addslashes($row['AFD_CODE'])."', 
					BLOCK_CODE='".addslashes($row['BLOCK_CODE'])."', 
					SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', 
					TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
					ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
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
					PLAN_SETAHUN = NULL, 
					DIS_JAN = NULL, DIS_FEB = NULL, 
					DIS_MAR = NULL, DIS_APR = NULL, 
					DIS_MAY = NULL, DIS_JUN = NULL, 
					DIS_JUL = NULL, DIS_AUG = NULL, 
					DIS_SEP = NULL, DIS_OCT = NULL, 
					DIS_NOV = NULL, DIS_DEC = NULL, 
					DIS_SETAHUN = NULL, COST_SMS1 = NULL, 
					COST_SMS2 = NULL, MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', 
					MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS1'])."'
				WHERE 
					TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }	
	
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
}