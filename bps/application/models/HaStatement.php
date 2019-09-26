<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Ha Statement
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Ha Statement
						- save				: simpan data
						- saveTemp			: YIR 07/07/2014	: simpan data sementara sesuai input user
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	

=========================================================================================================================
*/
class Application_Model_HaStatement
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
	
	//ambil data dari DB
    public function getData($params = array())
    {
    	/* change Eko Lesmana Sijabat - 20180528 */
		$query = "
            SELECT ROWIDTOCHAR (A.ROWID) row_id,
				   TO_CHAR (A.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   A.BA_CODE,
				   A.AFD_CODE,
				   A.BLOCK_CODE,
				   A.BLOCK_DESC, 
				   TRIM(TO_CHAR(A.HA_PLANTED, '999999999.9999')) AS HA_PLANTED,
                   --A.HA_PLANTED,
				   A.TOPOGRAPHY,
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'TOPOGRAPHY'
						   AND PARAMETER_VALUE_CODE = A.TOPOGRAPHY)
					  TOPOGRAPHY_DESC,
				   A.LAND_TYPE,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'LAND_TYPE'
						   AND PARAMETER_VALUE_CODE = A.LAND_TYPE)
					  LAND_TYPE_DESC,
				   A.PROGENY,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'PROGENY' AND PARAMETER_VALUE_CODE = PROGENY)
					  PROGENY_DESC,
				   A.LAND_SUITABILITY,
				   A.LAND_SUITABILITY LAND_SUITABILITY_DESC,
				   TO_CHAR (A.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
				   TO_CHAR (A.TAHUN_TANAM, 'MM') TAHUN_TANAM_M,
				   TO_CHAR (A.TAHUN_TANAM, 'RRRR') TAHUN_TANAM_Y,
				   A.POKOK_TANAM,
				   A.SPH,
				   A.STATUS,
				   A.KONVERSI_TBM,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'STATUS_BLOK_BUDGET'
						   AND PARAMETER_VALUE_CODE = STATUS)
					  STATUS_DESC,
					  A.FLAG_TEMP,
					  SPH.SPH_STANDAR,
					  NVL((SPH.SPH_STANDAR - A.SPH),0) SPH_MAX
			  FROM TM_HECTARE_STATEMENT A
			  LEFT JOIN TM_ORGANIZATION B
			  ON A.BA_CODE = B.BA_CODE
			  LEFT JOIN TN_SPH SPH
				ON SPH.CORE =
                  CASE
                     WHEN SUBSTR (A.BA_CODE, 3, 1) = 2 THEN 'INTI'
                     ELSE 'PLASMA'
                  END
               AND SPH.LAND_TYPE = A.LAND_TYPE
               AND SPH.TOPOGRAPHY = A.TOPOGRAPHY
			 WHERE A.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(A.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(A.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.AFD_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.BLOCK_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HA_PLANTED) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.PROGENY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.LAND_SUITABILITY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.TAHUN_TANAM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.POKOK_TANAM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.SPH) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.KONVERSI_TBM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY A.BA_CODE, A.AFD_CODE, A.BLOCK_CODE
		";

		return $query;
	}
	
	//menampilkan list Ha Statement
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
		
        $sql = "SELECT COUNT(*) FROM ({$this->getData($params)})"; //echo $sql;
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$this->getData($params)} {$end}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
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
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);		
		$haplanted = number_format($row['HA_PLANTED'], 4);

		$sql = "
			UPDATE TM_HECTARE_STATEMENT
					SET HA_PLANTED = REPLACE('{$haplanted}', ',', ''),
						TOPOGRAPHY = '{$row['TOPOGRAPHY']}', 
						LAND_TYPE = '{$row['LAND_TYPE']}', 
						PROGENY = '{$row['PROGENY']}', 
						LAND_SUITABILITY = '{$row['LAND_SUITABILITY']}', 
						TAHUN_TANAM = TO_DATE('{$row['TAHUN_TANAM']}','MM.RRRR'), 
						POKOK_TANAM = REPLACE('{$row['POKOK_TANAM']}', ',', ''), 
						SPH = NULL,
						MATURITY_STAGE_SMS1 = NULL,
						MATURITY_STAGE_SMS2 = NULL,
						STATUS = '{$row['STATUS']}',
						KONVERSI_TBM = '{$row['KONVERSI_TBM']}',
						FLAG_TEMP = 'Y',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//simpan data
	public function save($row = array())
    { 
		$result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$tahun_tanam = intval(date('Ymd', strtotime('01.'.$row['TAHUN_TANAM'])));
		$period_budget = intval(date('Ymd', strtotime('31.12.'.$row['PERIOD_BUDGET'])));
		$haplanted = number_format($row['HA_PLANTED'], 4);
		if($period_budget < $tahun_tanam) {
			die("Tahun Tanam Harus Lebih Kecil Daripada Periode Budget.");
		}
		
		//kalkulasi maturity_stage
		$maturity_stage = $this->_formula->get_MaturityStage('01.'.$row['TAHUN_TANAM']);
		$sph = $this->_formula->cal_Upload_Sph($row);
		
			$sql = "UPDATE TM_HECTARE_STATEMENT
					SET HA_PLANTED = REPLACE('{$haplanted}', ',', ''),
						TOPOGRAPHY = '{$row['TOPOGRAPHY']}', 
						LAND_TYPE = '{$row['LAND_TYPE']}', 
						PROGENY = '{$row['PROGENY']}', 
						LAND_SUITABILITY = '{$row['LAND_SUITABILITY']}', 
						TAHUN_TANAM = TO_DATE('{$row['TAHUN_TANAM']}','MM.RRRR'), 
						POKOK_TANAM = REPLACE('{$row['POKOK_TANAM']}', ',', ''), 
						SPH = REPLACE('{$sph}', ',', ''),
						MATURITY_STAGE_SMS1 = '".addslashes($maturity_stage[1])."',
						MATURITY_STAGE_SMS2 = '".addslashes($maturity_stage[2])."',
						STATUS = '{$row['STATUS']}',
						KONVERSI_TBM = '{$row['KONVERSI_TBM']}',
						FLAG_TEMP = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						DELETE_TIME = NULL,
						DELETE_USER = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";

		$this->_global->createSqlFile($row['filename'], $sql);
					 
		$row['MATURITY_STAGE_SMS2'] = $maturity_stage[2];

        return $result;
    }
	
	//hapus data
	public function delete($row = array())
    {
		$result = true;
		
		$sql = "
			UPDATE TM_HECTARE_STATEMENT
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				AND BA_CODE = '".addslashes($row[BA_CODE])."'
				AND AFD_CODE = '".addslashes($row[AFD_CODE])."'
				AND BLOCK_CODE = '".addslashes($row[BLOCK_CODE])."';
		";
		
		$sql .= "
			UPDATE TM_HECTARE_STATEMENT_DETAIL
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE BA_CODE = '".addslashes($rows[BA_CODE])."'
				AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				AND AFD_CODE = '".addslashes($rows[AFD_CODE])."'
				AND BLOCK_CODE = '".addslashes($rows[BLOCK_CODE])."';
		";
		$this->_global->createSqlFile($row['filename'], $sql);

        return $result;
    }
}

