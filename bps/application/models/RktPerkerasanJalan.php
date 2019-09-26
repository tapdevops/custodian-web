<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT Perkerasan Jalan
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT Perkerasan Jalan
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
						- getListInfoVra 	: SID 15/07/2014	: menampilkan list info VRA
						- checkData			: memeriksa RP/HM pada Norma PJ
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	29/07/2013
Update Terakhir		:	30/06/2015
Revisi				:	
YULIUS 07/07/2014	: 	- perubahan validasi filter di getData -> if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] <> 'ALL')) {
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function calCostElement, calTotalCost, saveRotation, saveTemp
						  
YULIUS 08/07/2014   :	- perbaikan query di getData (menghilangkan union ke table Temp)
						- menghapus fungsi yang tidak terpakai.
						
NBU	   30/06/2015   :	- penambahan pemeriksaan data pada norma PJ apakah masih ada yang 0
						
=========================================================================================================================
*/
class Application_Model_RktPerkerasanJalan
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
                AND RKTPK.ACTIVITY_CODE IN ('".$params['src_coa_code']."')
            ";
        }
		
		$query = "
		 SELECT RKTPK.ROW_ID,RKTPK.TRX_RKT_CODE,
				 RKTPK.ROW_ID_TEMP,
				 TO_CHAR (HA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				 HA.BA_CODE,
				 HA.AFD_CODE,
				 HA.BLOCK_CODE, 
				 HA.BLOCK_DESC,
				 HA.LAND_TYPE,
				 HA.TOPOGRAPHY,
				 TO_CHAR (HA.TAHUN_TANAM, 'MM.RRRR') AS TAHUN_TANAM,
				 to_char(HA.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				 to_char(HA.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				 HA.MATURITY_STAGE_SMS1 AS SEMESTER1,
				 HA.MATURITY_STAGE_SMS2 AS SEMESTER2,
				 RKTPK.ACTIVITY_CODE,
				 HA.HA_PLANTED,
				 HA.POKOK_TANAM,
				 HA.SPH,
				 RKTPK.SUMBER_BIAYA,
				 RKTPK.FLAG_TEMP,
				   CASE 
					WHEN RKTPK.TIPE_NORMA IS NULL THEN 'UMUM'
					ELSE RKTPK.TIPE_NORMA
				   END as TIPE_NORMA,
								   
				 RKTPK.JENIS_PEKERJAAN,
				 RKTPK.JARAK,
				 TPV.PARAMETER_VALUE AS RANGE_JARAK,
				 RKTPK.AKTUAL_JALAN,
				 RKTPK.AKTUAL_PERKERASAN_JALAN,
				 RKTPK.PLAN_JAN,
				 RKTPK.PLAN_FEB,
				 RKTPK.PLAN_MAR,
				 RKTPK.PLAN_APR,
				 RKTPK.PLAN_MAY,
				 RKTPK.PLAN_JUN,
				 RKTPK.PLAN_JUL,
				 RKTPK.PLAN_AUG,
				 RKTPK.PLAN_SEP,
				 RKTPK.PLAN_OCT,
				 RKTPK.PLAN_NOV,
				 RKTPK.PLAN_DEC,
				   RKTPK.PLAN_JAN
				 + RKTPK.PLAN_FEB
				 + RKTPK.PLAN_MAR
				 + RKTPK.PLAN_APR
				 + RKTPK.PLAN_MAY
				 + RKTPK.PLAN_JUN
				 + RKTPK.PLAN_JUL
				 + RKTPK.PLAN_AUG
				 + RKTPK.PLAN_SEP
				 + RKTPK.PLAN_OCT
				 + RKTPK.PLAN_NOV
				 + RKTPK.PLAN_DEC
					AS PLAN_SETAHUN,
				 RKTPK.PRICE_QTY,
				 RKTPK.COST_JAN,
				 RKTPK.COST_FEB,
				 RKTPK.COST_MAR,
				 RKTPK.COST_APR,
				 RKTPK.COST_MAY,
				 RKTPK.COST_JUN,
				 RKTPK.COST_JUL,
				 RKTPK.COST_AUG,
				 RKTPK.COST_SEP,
				 RKTPK.COST_OCT,
				 RKTPK.COST_NOV,
				 RKTPK.COST_DEC,
				   RKTPK.COST_JAN
				 + RKTPK.COST_FEB
				 + RKTPK.COST_MAR
				 + RKTPK.COST_APR
				 + RKTPK.COST_MAY
				 + RKTPK.COST_JUN
				 + RKTPK.COST_JUL
				 + RKTPK.COST_AUG
				 + RKTPK.COST_SEP
				 + RKTPK.COST_OCT
				 + RKTPK.COST_NOV
				 + RKTPK.COST_DEC
					AS COST_SETAHUN
			FROM (SELECT ROWIDTOCHAR (PK.ROWID) AS ROW_ID,
								   '' ROW_ID_TEMP,
								   PK.PERIOD_BUDGET,
								   PK.BA_CODE,
								   PK.AFD_CODE,
								   PK.BLOCK_CODE,
								   PK.ACTIVITY_CODE,
								   PK.TRX_RKT_CODE,
								   PK.MATURITY_STAGE_SMS1,
								   PK.MATURITY_STAGE_SMS2,
								   PK.SUMBER_BIAYA,
								   PK.FLAG_TEMP,
								   PK.TIPE_NORMA,
								   PK.JENIS_PEKERJAAN,
								   PK.JARAK,
								   PK.AKTUAL_JALAN,
								   PK.AKTUAL_PERKERASAN_JALAN,
								   PK.PLAN_JAN,
								   PK.PLAN_FEB,
								   PK.PLAN_MAR,
								   PK.PLAN_APR,
								   PK.PLAN_MAY,
								   PK.PLAN_JUN,
								   PK.PLAN_JUL,
								   PK.PLAN_AUG,
								   PK.PLAN_SEP,
								   PK.PLAN_OCT,
								   PK.PLAN_NOV,
								   PK.PLAN_DEC,
								   PK.PLAN_SETAHUN,
								   PK.PRICE_QTY,
								   PK.COST_JAN,
								   PK.COST_FEB,
								   PK.COST_MAR,
								   PK.COST_APR,
								   PK.COST_MAY,
								   PK.COST_JUN,
								   PK.COST_JUL,
								   PK.COST_AUG,
								   PK.COST_SEP,
								   PK.COST_OCT,
								   PK.COST_NOV,
								   PK.COST_DEC,
								   PK.COST_SETAHUN
							  FROM  TR_RKT_PK PK
							  WHERE PK.DELETE_USER IS NULL
							) RKTPK
				 LEFT JOIN TM_HECTARE_STATEMENT HA
					ON     HA.PERIOD_BUDGET = RKTPK.PERIOD_BUDGET
					   AND HA.BA_CODE = RKTPK.BA_CODE
					   AND HA.AFD_CODE = RKTPK.AFD_CODE
					   AND HA.BLOCK_CODE = RKTPK.BLOCK_CODE
					$where
				 LEFT JOIN TM_ORGANIZATION ORG
					ON HA.BA_CODE = ORG.BA_CODE
					LEFT JOIN T_PARAMETER_VALUE TPV
					ON TPV.PARAMETER_VALUE_CODE = RKTPK.JARAK
		   WHERE     1 = 1          
        ";

		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HA.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(HA.PERIOD_BUDGET, 'RRRR')  = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(HA.PERIOD_BUDGET, 'RRRR')  = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(HA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(HA.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }else{
			$query .= "
                AND UPPER(HA.AFD_CODE) LIKE UPPER('%%')
            ";
		}

		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(HA.BA_CODE) IN ('".$params['key_find']."')
            ";
        }

		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(HA.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		if ($params['activity_code'] != '') {
			$query .= "
                AND RKTPK.ACTIVITY_CODE IN ('".$params['activity_code']."')
            ";
        }
		
		if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
			$query .= "
                AND (
					UPPER(HA.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
					OR UPPER(HA.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
				)
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(HA.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(HA.AFD_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(HA.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(HA.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(HA.TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(HA.TAHUN_TANAM) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }

		$query .= "
			ORDER BY HA.BA_CODE, HA.AFD_CODE, HA.BLOCK_CODE, HA.LAND_TYPE, HA.TOPOGRAPHY, HA.TAHUN_TANAM
		";
		//print_r($query); echo"<br>";
		return $query;
	}
	
	//menampilkan list RKT Perkerasan Jalan
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
		if (!empty($rows)){
			foreach ($rows as $idx => $row){
				$ulangBaru = ($row['JENIS_PEKERJAAN']) ? $row['JENIS_PEKERJAAN'] : $this->_formula->cekJenisPekerjaan_RKT_PK($row);
				$row['JENIS_PEKERJAAN'] = $ulangBaru;
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
							FROM (
								SELECT VRA_CODE_DT AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_EXCAV AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_COMPACTOR AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_GRADER AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
							) INFRA
							LEFT JOIN TM_VRA VRA
								ON INFRA.VRA_CODE = VRA.VRA_CODE
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
							FROM (
								SELECT VRA_CODE_DT AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_EXCAV AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_COMPACTOR AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
								UNION
								SELECT VRA_CODE_GRADER AS VRA_CODE
								FROM TN_PERKERASAN_JALAN
								WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
									AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
									AND UPPER(ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
							) INFRA
							LEFT JOIN TM_VRA VRA
								ON INFRA.VRA_CODE = VRA.VRA_CODE
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
	
	//get sumber biaya
    public function getSumberBiaya($params = array())
    {
        $result = array();
        $sql = "SELECT PARAMETER_VALUE NILAI FROM T_PARAMETER_VALUE WHERE PARAMETER_CODE = 'SUMBER_BIAYA' ORDER BY NILAI DESC";
        
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
        $sql = "SELECT ROWIDTOCHAR(ROWID) ROW_ID FROM TR_RKT_PK 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') AND
							BA_CODE = '{$params['BA_CODE']}'AND
							AFD_CODE = '{$params['AFD_CODE']}'AND 
							BLOCK_CODE  = '{$params['BLOCK_CODE']}'AND
							ACTIVITY_CODE  = '{$params['ACTIVITY_CODE']}'
							";        
        $rows = $this->_db->fetchOne ($sql);
        return $rows;
    }

	public function saveDistVra($arrAfdUpd = array()){
		$result=true;
		$lastBa = $lastAfd = $lastActivity = '';
		
		$sql="
			SELECT VRA_CODE_DT, VRA_CODE_EXCAV, VRA_CODE_COMPACTOR, VRA_CODE_GRADER
			FROM TN_PERKERASAN_JALAN 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
		";				
		$rows = $this->_db->fetchAll($sql);
		
		//CEK DISTRIBUSI VRA
		$sql = "
			SELECT COUNT (DISTINCT LOCATION_CODE) 
			FROM TR_RKT_VRA_DISTRIBUSI
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
				AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
				AND TIPE_TRANSAKSI = 'INFRA'
		";
		$distribusi = $this->_db->fetchOne($sql);
		
		//jika sebelumnya blm ada data, buat kombinasi data baru
		if($distribusi == 0){
			//die($sql);
			foreach ($rows as $idx => $roww){
				$arrPKDistVra1 = array( $arrAfdUpd['ACTIVITY_CODE'], $roww['VRA_CODE_DT'], $roww['VRA_CODE_EXCAV'], 
				$roww['VRA_CODE_COMPACTOR'], $roww['VRA_CODE_GRADER'], $arrAfdUpd['BA_CODE']);

				//GET AFDELING PER BA
				$sql = "
					SELECT DISTINCT AFD_CODE AS AFD_CODE 
					FROM TM_HECTARE_STATEMENT
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
				";
				$afdeling = $this->_db->fetchAll($sql);
				
				foreach($afdeling as $idx => $value) {
					for($i=1;$i<5;$i++){
						$arrAfdUpd1['TRX_CODE'] = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$arrPKDistVra1[0].$arrPKDistVra1[$i]);
						$sql = "
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
								'{$arrPKDistVra1[0]}', 
								'{$arrPKDistVra1[$i]}',
								'".addslashes($value['AFD_CODE'])."', 
								'0', 
								'0', 
								'0',
								'{$arrAfdUpd1['TRX_CODE']}', 
								'INFRA', 
								'{$this->_userName}', 
								SYSDATE
							)
						";
						$this->_db->query($sql);
						$this->_db->commit();
					}
				}
			}
		}	
		//print_r($rows);die();
		foreach ($rows as $idx => $row) {
			//GET PLAN STAUN AFD PK JALAN
			// YUS 18/11/2014 
			$sql = "
				SELECT JARAK, SUM(PLAN_JAN
				 + PLAN_FEB
				 + PLAN_MAR
				 + PLAN_APR
				 + PLAN_MAY
				 + PLAN_JUN
				 + PLAN_JUL
				 + PLAN_AUG
				 + PLAN_SEP
				 + PLAN_OCT
				 + PLAN_NOV
				 + PLAN_DEC) PLAN_SETAHUN
				FROM TR_RKT_PK 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
					AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."' 
					AND AFD_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."'
					AND JARAK IS NOT NULL
				GROUP BY JARAK
			";					
				//die($sql);
			$rows1 = $this->_db->fetchAll($sql);
			
			if (!empty($rows1)) {
				foreach ($rows1 as $idx1 => $row1) {
					$arrAfdUpd['RANGE_JARAK'] = $row1['JARAK'];					
					$distVraPK = $this->_formula->get_DistVraPK($arrAfdUpd);  //CARI RP HM ELEMENT VRA
					
					//data DT
					$arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_DT'];
					$vra_code['DT'] = $row['VRA_CODE_DT'];
					$rp_qty['DT'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
					$arrHmKm['DT'] = ($rp_qty['DT']) ? ($distVraPK['DT'] / $rp_qty['DT']) : 0;
					$hm_km['DT'] += ($arrHmKm['DT'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					$price_hm_km['DT'] += ($rp_qty['DT'] * $arrHmKm['DT'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					
					//data EXCAV
					$arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_EXCAV'];
					$vra_code['EXCAV'] = $row['VRA_CODE_EXCAV'];
					$rp_qty['EXCAV'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
					$arrHmKm['EXCAV'] = $distVraPK['EXCAV'];
					$hm_km['EXCAV'] += ($arrHmKm['EXCAV'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					$price_hm_km['EXCAV'] += ($rp_qty['EXCAV'] * $arrHmKm['EXCAV'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					
					//data COMPACTOR
					$arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_COMPACTOR'];
					$vra_code['COMPACTOR'] = $row['VRA_CODE_COMPACTOR'];
					$rp_qty['COMPACTOR'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
					$arrHmKm['COMPACTOR'] = $distVraPK['COMPACTOR'];
					$hm_km['COMPACTOR'] += ($arrHmKm['COMPACTOR'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					$price_hm_km['COMPACTOR'] += ($rp_qty['COMPACTOR'] * $arrHmKm['COMPACTOR'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					
					//data GRADER
					$arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_GRADER'];
					$vra_code['GRADER'] = $row['VRA_CODE_GRADER'];
					$rp_qty['GRADER'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
					$arrHmKm['GRADER'] = $distVraPK['GRADER'];
					$hm_km['GRADER'] += ($arrHmKm['GRADER'] * $row1['PLAN_SETAHUN'] / 1000) ; 
					$price_hm_km['GRADER'] += ($rp_qty['GRADER'] * $arrHmKm['GRADER'] * $row1['PLAN_SETAHUN'] / 1000) ; 
				}
			}
			//print_r($hm_km);
			
			//DELETE RKT VRA DISTRIBUSI
			$sql = "
				DELETE FROM TR_RKT_VRA_DISTRIBUSI
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
					AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
					AND LOCATION_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."';
			";
		
			//insert masing2 vra
			for($i=1;$i<5;$i++){
				switch($i){
					case 1 : $vra = 'DT'; break;
					case 2 : $vra = 'EXCAV'; break;
					case 3 : $vra = 'COMPACTOR'; break;
					case 4 : $vra = 'GRADER'; break;
				}
				
				$trxCode = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$arrAfdUpd['ACTIVITY_CODE'].$vra_code[$vra]);
				
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
						'".addslashes($arrAfdUpd['ACTIVITY_CODE'])."', 
						'".addslashes($vra_code[$vra])."', 
						'".addslashes($arrAfdUpd['AFD_CODE'])."', 
						'".$hm_km[$vra]."', 
						'".$rp_qty[$vra]."', 
						'".$price_hm_km[$vra]."', 
						'".$trxCode."', 
						'INFRA', 
						'{$this->_userName}', 
						SYSDATE
					);
				";
			}
		}
		
		//create sql file
		$this->_global->createSqlFile($arrAfdUpd['filename'], $sql);
		return true;
	}

	//hitung cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		//<!-- TIPE NORMA -->
		//tipe norma
		$row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
		
		//hitung cost element
		$mon = $this->_formula->cal_RktPerkerasanJalan_CostElement($costElement,$row);
		//print_r($mon);
		$row['PRICE_QTY'] = $mon[1];
		$total = $this->_formula->cal_RktPerkerasanJalan_DistribusiTahunBerjalan($row);
		
		//save hasil cost element
		$sql = "
			UPDATE TR_RKT_PK_COST_ELEMENT 
			SET
			MATURITY_STAGE_SMS1 = '".addslashes($row['SEMESTER1'])."',
			MATURITY_STAGE_SMS2 = '".addslashes($row['SEMESTER2'])."',
			TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
			SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
			TOTAL_RP_QTY = REPLACE('".addslashes($mon[1])."',',',''),
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
			UPDATE_USER = '{$this->_userName}', 
			UPDATE_TIME = SYSDATE
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
			AND COST_ELEMENT = '".addslashes($costElement)."';
		";
		//echo $sql."<br>";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//hitung total cost
	public function calTotalCost($row = array())
    { 
        $result = true;
		
		//tipe norma
		$row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
		
		//cari summary total cost
		$sql = "
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
				   SUM (DIS_DEC) DIS_DEC
			FROM TR_RKT_PK_COST_ELEMENT
			WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'";
		$summary = $this->_db->fetchRow($sql);
		
		//simpan total cost
			$biaya = $summary['DIS_JAN'] + $summary['DIS_FEB'] + $summary['DIS_MAR'] + $summary['DIS_APR'] + $summary['DIS_MAY'] + $summary['DIS_JUN'] + $summary['DIS_JUL'] + $summary['DIS_AUG'] + $summary['DIS_SEP'] + $summary['DIS_OCT'] + $summary['DIS_NOV'] + $summary['DIS_DEC'];
      $plan_setahun = $row['PLAN_JAN']+$row['PLAN_FEB']+$row['PLAN_MAR']+$row['PLAN_APR']+
                $row['PLAN_MAY']+$row['PLAN_JUN']+$row['PLAN_JUL']+$row['PLAN_AUG']+
                $row['PLAN_SEP']+$row['PLAN_OCT']+$row['PLAN_NOV']+$row['PLAN_DEC'];
			$sql = "UPDATE TR_RKT_PK 
					SET 
						MATURITY_STAGE_SMS1 = '".addslashes($row['SEMESTER1'])."',
						MATURITY_STAGE_SMS2 = '".addslashes($row['SEMESTER2'])."',
						PRICE_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
						AKTUAL_JALAN = '".addslashes($row['AKTUAL_JALAN'])."',
						AKTUAL_PERKERASAN_JALAN = '".addslashes($row['AKTUAL_PERKERASAN_JALAN'])."',
						JENIS_PEKERJAAN = '".addslashes($row['JENIS_PEKERJAAN'])."',
						JARAK = '".addslashes($row['JARAK'])."',
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
            PLAN_SETAHUN = '".addslashes($plan_setahun)."',
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
						TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
						COST_SETAHUN = '".addslashes($biaya)."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						FLAG_TEMP = NULL
						WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
					";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }

	//simpan temp data
	public function saveTemp($row = array())
    { 

		$sql = "
			UPDATE TR_RKT_PK_COST_ELEMENT 
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['SEMESTER1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['SEMESTER2'])."',
				AKTUAL_JALAN = REPLACE('".addslashes($row['AKTUAL_JALAN'])."',',',''),
				AKTUAL_PERKERASAN_JALAN = REPLACE('".addslashes($row['AKTUAL_PERKERASAN_JALAN'])."',',',''),
				JENIS_PEKERJAAN = '".addslashes($row['JENIS_PEKERJAAN'])."',
				JARAK = REPLACE('".addslashes($row['JARAK'])."',',',''),
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
				PRICE_QTY = NULL,
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
				COST_SETAHUN = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
				WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
    }
	
	//simpan inputan rotasi
	public function saveRotation($row = array())
	{
    $plan_setahun = $row['PLAN_JAN']+$row['PLAN_FEB']+$row['PLAN_MAR']+$row['PLAN_APR']+
          $row['PLAN_MAY']+$row['PLAN_JUN']+$row['PLAN_JUL']+$row['PLAN_AUG']+
          $row['PLAN_SEP']+$row['PLAN_OCT']+$row['PLAN_NOV']+$row['PLAN_DEC'];
		$sql = "
			UPDATE TR_RKT_PK
			SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
				MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
				AKTUAL_JALAN = REPLACE('".addslashes($row['AKTUAL_JALAN'])."',',',''),
				TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
				AKTUAL_PERKERASAN_JALAN = REPLACE('".addslashes($row['AKTUAL_PERKERASAN_JALAN'])."',',',''),
				SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
				JENIS_PEKERJAAN = '".addslashes($row['JENIS_PEKERJAAN'])."',
				JARAK = REPLACE('".addslashes($row['JARAK'])."',',',''),
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
        PLAN_SETAHUN = '".addslashes($plan_setahun)."',
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
				COST_SETAHUN = NULL,
				FLAG_TEMP = 'Y',
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
	
	//get Rp/Rotasi SMS1 & SMS2
	public function getRotation($params = array())
    {
		$return = array();
		
		$rotasi = $this->_formula->get_RktManual_Rotasi($params);
		$return['ROTASI_SMS1'] = $rotasi['SMS1'];
		$return['ROTASI_SMS2'] = $rotasi['SMS2'];
		
		return $return;
	}
	
	//cek apakah ada norma perkerasan jalan yang grader, excavator atau compactor RP/HM nya 0
	public function checkData($params = array())
    {
		$query = "
			SELECT RP_HM_EXCAV, RP_HM_GRADER, RP_HM_COMPACTOR 
			FROM TN_PERKERASAN_JALAN
			WHERE 1 = 1
        ";
		
		if($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
            ";
		}else{
			$query .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
            ";
		}
		
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		if ($params['ACTIVITY_CODE'] != '') {
			$query .= "
                AND ACTIVITY_CODE = '".$params['ACTIVITY_CODE']."' 
            ";
        }
		
		$result = $this->_db->fetchRow($query);
		if($result['RP_HM_EXCAV'] == 0 || $result['RP_HM_GRADER'] == 0 || $result['RP_HM_COMPACTOR'] == 0){
			$result['status'] = 1;
		}else{
			$result['status'] = 0;
		}
		return $result;
	}
}
