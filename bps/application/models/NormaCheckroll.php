<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Checkroll
Function 			:	- getList					: menampilkan list norma Checkroll
						- save						: simpan data
						- updateRktCheckroll		: update RKT Checkroll
						- updateRktCheckrollSummary	: update RKT Checkroll Summary
						- delete					: hapus data
						- getInput					: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	13/06/2013
Update Terakhir		:	13/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaCheckroll
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

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR(norma.ROWID) row_id, rownum, 
				   to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, norma.BA_CODE, 
				   norma.JOB_CODE, norma.EMPLOYEE_STATUS, norma.GP, norma.MPP_AKTUAL, norma.MPP_PERIOD_BUDGET, norma.MPP_REKRUT, 
				   job.JOB_DESCRIPTION, grupjob.PARAMETER_VALUE GROUP_CHECKROLL_DESC, norma.FLAG_TEMP
			FROM TN_CHECKROLL norma
			LEFT JOIN TM_JOB_TYPE job
				ON norma.JOB_CODE = job.JOB_CODE
			LEFT JOIN T_PARAMETER_VALUE grupjob
				ON grupjob.PARAMETER_VALUE_CODE = job.GROUP_CHECKROLL_CODE
				AND grupjob.PARAMETER_CODE = 'GROUP_JOB'
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			WHERE norma.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		//filter employee status
		if ($params['employee_status'] != '') {
			$query .= "
                AND UPPER(norma.EMPLOYEE_STATUS) LIKE UPPER('%".$params['employee_status']."%')
            ";
        }
		
		//filter employee status
		if ($params['job_code'] != '') {
			$query .= "
                AND UPPER(norma.JOB_CODE) LIKE UPPER('%".$params['job_code']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
				
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JOB_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.EMPLOYEE_STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.GP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MPP_AKTUAL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MPP_PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MPP_REKRUT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(job.JOB_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(grupjob.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.JOB_CODE
		";
		return $query;
	}
	
	//menampilkan list norma CHECKROLL
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
			FROM TN_CHECKROLL 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND JOB_CODE = '{$params['JOB_CODE']}'
				AND EMPLOYEE_STATUS = '{$params['EMPLOYEE_STATUS']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data sementara
	public function saveTemp($row = array())
    { 
        $result = true;
		
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		//$mpp_rekrut = $this->_formula->cal_NormaCheckroll_MppRekrut($row);
				
		$sql = "
			UPDATE TN_CHECKROLL
			SET GP = REPLACE('".addslashes($row['GP'])."',',',''),
				MPP_AKTUAL = REPLACE('".addslashes($row['MPP_AKTUAL'])."',',',''),
				MPP_PERIOD_BUDGET = REPLACE('".addslashes($row['MPP_PERIOD_BUDGET'])."',',',''),
				MPP_REKRUT = NULL,
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				DELETE_TIME = NULL,
				DELETE_USER = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
				 
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);	
		
        return $result;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$mpp_rekrut = $this->_formula->cal_NormaCheckroll_MppRekrut($row);
				
		$sql = "
			UPDATE TN_CHECKROLL
			SET GP = REPLACE('".addslashes($row['GP'])."',',',''),
				MPP_AKTUAL = REPLACE('".addslashes($row['MPP_AKTUAL'])."',',',''),
				MPP_PERIOD_BUDGET = REPLACE('".addslashes($row['MPP_PERIOD_BUDGET'])."',',',''),
				MPP_REKRUT = REPLACE('".addslashes($mpp_rekrut)."',',',''),
				UPDATE_USER = '{$this->_userName}',
				FLAG_TEMP = NULL,
				UPDATE_TIME = SYSDATE,
				DELETE_TIME = NULL,
				DELETE_USER = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);	
		
        return $result;
    }
	
	public function updateRktCheckroll($row = array())
    { 		
		$result = true;		
		
		//report checkroll
		$gp_inflasi = $this->_formula->cal_NormaCheckroll_GpInflasi($row);
		$row['GP_INFLASI'] = $gp_inflasi;
		$tunjangan = $this->_formula->cal_NormaCheckroll_Tunjangan($row);
		$pk_umum = $this->_formula->cal_NormaCheckroll_PkUmum($row);
		$total_gp_mpp = $this->_formula->cal_NormaCheckroll_TotalGpMpp($row);
		$total_gaji_tunjangan = $row['MPP_PERIOD_BUDGET'] ? ($gp_inflasi + array_sum($tunjangan)) : 0;
		$row['TOTAL_GAJI_TUNJANGAN'] = $total_gaji_tunjangan;
		$rp_hk = $this->_formula->cal_NormaCheckroll_RpHk($row);
		$total_tunjangan_pk_umum = $this->_formula->cal_NormaCheckroll_TotalTunjanganPkUmum($pk_umum);
		$row['TOTAL_TUNJANGAN_PK_UMUM'] = $total_tunjangan_pk_umum;
		$dis_year = $this->_formula->cal_NormaCheckroll_DisYear($row);
		$dis_bulanan = $dis_year / 12;
		$total_tunjangan_wra = array_sum($tunjangan);
		
		/*if ((addslashes($row['BA_CODE']) == '5521') && (addslashes($row['JOB_CODE']) == 'FW010') && (addslashes($row['EMPLOYEE_STATUS']) == 'KL')){
			print_r ($tunjangan);
			echo "<br/>";
			print_r ($pk_umum);
			die();
		}*/

		// ********************************************** UPDATE RKT CHECKROLL **********************************************
		$sql = "
			UPDATE TR_RKT_CHECKROLL
			SET GP_INFLASI = REPLACE('".addslashes($gp_inflasi)."',',',''),
				MPP_PERIOD_BUDGET = REPLACE('".addslashes($row['MPP_PERIOD_BUDGET'])."',',',''),
				TOTAL_GP_MPP = REPLACE('".addslashes($total_gp_mpp)."',',',''),
				TOTAL_GAJI_TUNJANGAN = REPLACE('".addslashes($total_gaji_tunjangan)."',',',''),
				RP_HK_PERBULAN = REPLACE('".addslashes($rp_hk)."',',',''),
				TOTAL_TUNJANGAN_PK_UMUM = REPLACE('".addslashes($total_tunjangan_pk_umum)."',',',''),
				DIS_YEAR = REPLACE('".addslashes($dis_year)."',',',''),
				DIS_JAN = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_FEB = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_MAR = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_APR = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_MAY = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_JUN = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_JUL = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_AUG = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_SEP = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_OCT = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_NOV = REPLACE('".addslashes($dis_bulanan)."',',',''),
				DIS_DEC = REPLACE('".addslashes($dis_bulanan)."',',',''),
				TOTAL_TUNJANGAN_WRA = REPLACE('".addslashes($total_tunjangan_wra)."',',',''),
				TOTAL_TUNJANGAN_VRA = REPLACE('".addslashes(array_sum($tunjangan))."',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND JOB_CODE = '".addslashes($row['JOB_CODE'])."'
				AND EMPLOYEE_STATUS = '".addslashes($row['EMPLOYEE_STATUS'])."';
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
		// ********************************************** UPDATE RKT CHECKROLL **********************************************
		
		// ********************************************** UPDATE RKT CHECKROLL - DETAIL **********************************************		
		//get transaction code
		$sql = "
			SELECT TRX_CR_CODE
			FROM TR_RKT_CHECKROLL
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND JOB_CODE = '".addslashes($row['JOB_CODE'])."'
				AND EMPLOYEE_STATUS = '".addslashes($row['EMPLOYEE_STATUS'])."'
		";		
		$transaction_code = $this->_db->fetchOne($sql);
			
		//update detail report checkroll
		if ($tunjangan) {
			foreach ($tunjangan as $idx => $val) {
				$sql = "
					UPDATE TR_RKT_CHECKROLL_DETAIL
					SET JUMLAH = REPLACE('".addslashes($tunjangan[$idx])."',',',''),
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					WHERE TRX_CR_CODE = '".addslashes($transaction_code)."'
						AND TUNJANGAN_TYPE = '".$idx."';
				";
				
				$sqlx .= "
					UPDATE TR_RKT_CHECKROLL_DETAIL
					SET JUMLAH = REPLACE('".addslashes($tunjangan[$idx])."',',',''),
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					WHERE TRX_CR_CODE = '".addslashes($transaction_code)."'
						AND TUNJANGAN_TYPE = '".$idx."' ||;
				";
				//create sql file
				$this->_global->createSqlFile($row['filename'], $sql);
			}
			
			//if ((addslashes($row['BA_CODE']) == '5521') && (addslashes($row['JOB_CODE']) == 'FW010') && (addslashes($row['EMPLOYEE_STATUS']) == 'KL')){
			//	echo $sqlx;
			//	die();
			//}
		}
			
		if($pk_umum){
			foreach ($pk_umum as $idx => $val) {
				$sql = "
					UPDATE TR_RKT_CHECKROLL_DETAIL
					SET JUMLAH = REPLACE('".addslashes($pk_umum[$idx])."',',',''),
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					WHERE TRX_CR_CODE = '".addslashes($transaction_code)."'
						AND TUNJANGAN_TYPE = '".$idx."';
				";
				//create sql file
				$this->_global->createSqlFile($row['filename'], $sql);
			}
		}
		// ********************************************** UPDATE RKT CHECKROLL - DETAIL **********************************************
		
		return $result;
    }
	
	public function updateRktCheckrollSummary($row = array())
    { 		
		$result = true;		
		// ********************************************** UPDATE RKT CHECKROLL SUM **********************************************	
		// update TR_RKT_CHECKROLL_SUM
		$sql = "
			SELECT EMPLOYEE_STATUS, NVL(SUM(RP_HK_PERBULAN),0) RP_HK, NVL(SUM(MPP_PERIOD_BUDGET),0) MPP
			FROM TR_RKT_CHECKROLL
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND JOB_CODE = '".addslashes($row['JOB_CODE'])."'
			GROUP BY EMPLOYEE_STATUS
		";
		$arr_data = $this->_db->fetchAll($sql);
		
		$total_rp_hk = $total_mpp = 0;
		if (!empty($arr_data)) {
			foreach ($arr_data as $index => $data) {
				$total_rp_hk += ($data['RP_HK'] * $data['MPP']);
				$total_mpp += $data['MPP'];
			}
		}
		
		$rp_hk_sum = ($total_mpp) ? ($total_rp_hk/$total_mpp) : 0 ;
		
		$sql = "
			UPDATE TR_RKT_CHECKROLL_SUM 
			SET RP_HK = '".addslashes($rp_hk_sum)."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND JOB_CODE = '".addslashes($row['JOB_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
		// ********************************************** UPDATE RKT CHECKROLL SUM **********************************************
		return $result;
	}
	
	// update distribusi checkroll
	public function calDistribusiCheckroll($row = array())
	{
		$result = true;
		
		//hitung persentase ha planted per semester
		$sem1 = $this->_formula->get_ReportDistribusiCheckroll_TotalSem1($row);
		$sem2 = $this->_formula->get_ReportDistribusiCheckroll_TotalSem2($row);
		
		$sql = "
			SELECT MATURITY_STAGE,
				   TUNJANGAN_TYPE,
				   NVL((PERSEN_SMS1 * JUMLAH), 0)  AS NILAI_SMS1_PERBULAN,
				   NVL((PERSEN_SMS2 * JUMLAH), 0) AS NILAI_SMS2_PERBULAN
			FROM (
				SELECT 	PV.PARAMETER_VALUE MATURITY_STAGE,
						NVL(PERSEN_SMS1,0) PERSEN_SMS1,
						NVL(PERSEN_SMS2,0) PERSEN_SMS2
				FROM T_PARAMETER_VALUE PV
				LEFT JOIN (  
					SELECT 	HS.MATURITY_STAGE_SMS1 MATURITY_STAGE,
							CASE
								WHEN '{$sem1}' = 0 THEN 0
								ELSE (SUM (HS.HA_PLANTED) / '{$sem1}')
							END	as PERSEN_SMS1
					FROM TM_HECTARE_STATEMENT HS
					WHERE HS.DELETE_TIME IS NULL
						AND HS.PERIOD_BUDGET = TO_DATE ('01-01-{$row['PERIOD_BUDGET']}', 'DD-MM-RRRR')
						AND HS.BA_CODE = '".addslashes($row['BA_CODE'])."'
						AND HS.MATURITY_STAGE_SMS1 IS NOT NULL
					GROUP BY HS.MATURITY_STAGE_SMS1
				) SMS1
				ON PV.PARAMETER_VALUE = SMS1.MATURITY_STAGE
				LEFT JOIN (  
					SELECT 	HS.MATURITY_STAGE_SMS2 MATURITY_STAGE,
							CASE
								WHEN '{$sem2}' = 0 THEN 0
								ELSE (SUM (HS.HA_PLANTED) / '{$sem2}') 
							END as PERSEN_SMS2
					FROM TM_HECTARE_STATEMENT HS
					WHERE HS.DELETE_TIME IS NULL
						AND HS.PERIOD_BUDGET = TO_DATE ('01-01-{$row['PERIOD_BUDGET']}', 'DD-MM-RRRR')
						AND HS.BA_CODE = '".addslashes($row['BA_CODE'])."'
						AND HS.MATURITY_STAGE_SMS2 IS NOT NULL
					GROUP BY HS.MATURITY_STAGE_SMS2
				) SMS2
				ON PV.PARAMETER_VALUE = SMS2.MATURITY_STAGE
				WHERE PV.PARAMETER_CODE = 'MATURITY_STAGE'
					AND PARAMETER_VALUE <> 'ALL'
			) PRESENTASE,		
			(
				SELECT 	CRD.TUNJANGAN_TYPE, SUM (MPP_PERIOD_BUDGET * JUMLAH / 12) AS JUMLAH -- PK UMUM
				FROM TR_RKT_CHECKROLL CR
				LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
					ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
				WHERE CRD.DELETE_TIME IS NULL
					AND CR.PERIOD_BUDGET = TO_DATE ('01-01-{$row['PERIOD_BUDGET']}', 'DD-MM-RRRR')
					AND CR.BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND CR.DELETE_TIME IS NULL
				GROUP BY CRD.TUNJANGAN_TYPE
				UNION ALL 
				SELECT CRD.TUNJANGAN_TYPE, SUM  (MPP_PERIOD_BUDGET * JUMLAH )  JUMLAH -- TUNJANGAN OT
				FROM TM_JOB_TYPE JT
				LEFT JOIN TR_RKT_CHECKROLL CR
					ON JT.JOB_CODE = CR.JOB_CODE
				LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
					ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
				WHERE JOB_TYPE = 'OT'
					AND TUNJANGAN_TYPE IN (
						SELECT TUNJANGAN_TYPE
						FROM TM_TUNJANGAN 
						WHERE DELETE_TIME IS NULL 
						AND FLAG_RP_HK = 'YES'
					)
					AND CR.PERIOD_BUDGET = TO_DATE ('01-01-{$row['PERIOD_BUDGET']}', 'DD-MM-RRRR')
					AND CR.BA_CODE =  '".addslashes($row['BA_CODE'])."'
					AND CR.DELETE_TIME IS NULL
					AND CRD.DELETE_TIME IS NULL
				GROUP BY CRD.TUNJANGAN_TYPE
				UNION ALL			 
				SELECT  'GAJI', SUM(GP_INFLASI * MPP_PERIOD_BUDGET )JUMLAH
				FROM TM_JOB_TYPE JT
				LEFT JOIN TR_RKT_CHECKROLL CR 
					ON CR.JOB_CODE = JT.JOB_CODE
				WHERE CR.BA_CODE =  '".addslashes($row['BA_CODE'])."' 
					AND CR.PERIOD_BUDGET = TO_DATE ('01-01-{$row['PERIOD_BUDGET']}', 'DD-MM-RRRR')
					AND CR.DELETE_TIME IS NULL
					AND JT.JOB_TYPE = 'OT'
			) NILAI
		";
		$distribusi = $this->_db->fetchAll($sql);
		
		foreach($distribusi as $id => $value) {
			$value['TOTAL_SETAHUN'] = (( $value['NILAI_SMS1_PERBULAN'] * 6 ) + ( $value['NILAI_SMS2_PERBULAN'] * 6 ));
			$sql = "
				UPDATE TR_RPT_DISTRIBUSI_COA 
				SET DIS_JAN = '". $value['NILAI_SMS1_PERBULAN']."', 
					DIS_FEB = '". $value['NILAI_SMS1_PERBULAN']."',
					DIS_MAR = '". $value['NILAI_SMS1_PERBULAN']."',
					DIS_APR = '". $value['NILAI_SMS1_PERBULAN']."',
					DIS_MAY = '". $value['NILAI_SMS1_PERBULAN']."',
					DIS_JUN = '". $value['NILAI_SMS1_PERBULAN']."',
					DIS_JUL = '". $value['NILAI_SMS2_PERBULAN']."', 
					DIS_AUG = '". $value['NILAI_SMS2_PERBULAN']."',
					DIS_SEP = '". $value['NILAI_SMS2_PERBULAN']."',
					DIS_OCT = '". $value['NILAI_SMS2_PERBULAN']."',
					DIS_NOV = '". $value['NILAI_SMS2_PERBULAN']."',
					DIS_DEC = '". $value['NILAI_SMS2_PERBULAN']."',
					TOTAL_BIAYA = '". $value['TOTAL_SETAHUN']."',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND MATURITY_STAGE = '".$value['MATURITY_STAGE']."'
					AND TUNJANGAN_TYPE = '".$value['TUNJANGAN_TYPE']."';
			";

			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		
		return $result;
	}
	
	//hapus data
	public function delete($row = array())
    {
		$sql = "
			UPDATE TN_CHECKROLL
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;		 
    }
}

