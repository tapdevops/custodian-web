<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Distribusi VRA - Non Infra
Function 			:	- getInput					: YIR 12/08/2014	: setting input untuk region dan maturity stage
Function 			:	- getList					: menampilkan list norma Distribusi VRA - Non Infra
						- save						: simpan data
						- delete					: hapus data
						- getListInfoVra 	: SID 15/07/2014	: menampilkan list info VRA
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	15/07/2014
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaDistribusiVraNonInfra
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
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE;
		
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
	
	//ambil data dari DB - doni
    public function getDataHeader($params = array())
    {
/*		$query = "
			SELECT DISTINCT 
				 VRA.TRX_CODE,
				 TO_CHAR (VRA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				 VRA.BA_CODE,
				 VRA.ACTIVITY_CODE,
				 TA.DESCRIPTION,
				 VRA.VRA_CODE,
				 TV.VRA_SUB_CAT_DESCRIPTION,
				 TV.UOM, 
				 mapping.ACTIVITY_GROUP_TYPE_CODE,
				 VRA.FLAG_TEMP
			FROM TR_RKT_VRA_DISTRIBUSI VRA
			LEFT JOIN TM_ACTIVITY TA 
				ON TA.ACTIVITY_CODE = VRA.ACTIVITY_CODE
			LEFT JOIN TM_ACTIVITY_MAPPING mapping
				ON TA.ACTIVITY_CODE = mapping.ACTIVITY_CODE
			LEFT JOIN TM_VRA TV 
				ON TV.VRA_CODE = VRA.VRA_CODE
			LEFT JOIN TM_ORGANIZATION ORG 
				ON ORG.BA_CODE = VRA.BA_CODE 
			WHERE  VRA.DELETE_TIME IS NULL
			AND VRA.TIPE_TRANSAKSI = 'NON_INFRA'
			AND  mapping.ACTIVITY_GROUP_TYPE_CODE <> '-'
		";
*/
		$query  = "SELECT HEADER.*, CEKS.HM_KM_DIGUNAKAN_SENDIRI, CEKS.HM_KM_DIGUNAKAN_PINJAM, CEKS.SELISIH_HM_KM FROM (";
		$query .= "SELECT DISTINCT 
						 VRA.TRX_CODE,
						 TO_CHAR (VRA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
						 VRA.BA_CODE,
						 VRA.ACTIVITY_CODE,
						 TA.DESCRIPTION,
						 VRA.VRA_CODE,
						 TV.VRA_SUB_CAT_DESCRIPTION,
						 TV.UOM, 
						 mapping.ACTIVITY_GROUP_TYPE_CODE,
						 VRA.FLAG_TEMP
					FROM TR_RKT_VRA_DISTRIBUSI VRA
					LEFT JOIN TM_ACTIVITY TA 
						ON TA.ACTIVITY_CODE = VRA.ACTIVITY_CODE
					LEFT JOIN TM_ACTIVITY_MAPPING mapping
						ON TA.ACTIVITY_CODE = mapping.ACTIVITY_CODE
					LEFT JOIN TM_VRA TV 
						ON TV.VRA_CODE = VRA.VRA_CODE
					LEFT JOIN TM_ORGANIZATION ORG 
						ON ORG.BA_CODE = VRA.BA_CODE 
					WHERE  VRA.DELETE_TIME IS NULL
					AND VRA.TIPE_TRANSAKSI = 'NON_INFRA'
					AND  mapping.ACTIVITY_GROUP_TYPE_CODE <> '-'
					";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(VRA.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(VRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(VRA.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(VRA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(VRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(VRA.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TA.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		if ($params['vra_code'] != '') {
			$query .= "
                AND VRA.VRA_CODE IN ('".$params['vra_code']."')
            ";
        }
		
		$query .= "
			ORDER BY TA.DESCRIPTION,  TV.VRA_SUB_CAT_DESCRIPTION
		";
		
		//aries tambahan untuk validasi dari report utilitas vra
		$query .= " )HEADER
                        LEFT JOIN (  SELECT SUM_HM_KM.PERIOD_BUDGET,
                                             SUM_HM_KM.REGION_CODE,
                                             SUM_HM_KM.BA_CODE,
                                             SUM_HM_KM.COMPANY_NAME,
                                             SUM_HM_KM.VRA_CODE,
                                             UPPER (VRA.VRA_SUB_CAT_DESCRIPTION) AS VRA_SUB_CAT_DESCRIPTION,
                                             UPPER (VRA.TYPE) AS TYPE,
                                             NVL (SUM (SUM_HM_KM.JUMLAH_ALAT), 0) AS JUMLAH_ALAT,
                                             UPPER (VRA.UOM) AS UOM,
                                             NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0) AS TOTAL_HM_KM,
                                             NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0)
                                                AS HM_KM_DIGUNAKAN_SENDIRI,
                                             NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_PINJAM), 0) AS HM_KM_DIGUNAKAN_PINJAM,
                                             (NVL (SUM (SUM_HM_KM.TOTAL_QTY_TAHUN), 0)
                                              - NVL (SUM (SUM_HM_KM.TOTAL_HM_KM_SENDIRI), 0))
                                                AS SELISIH_HM_KM
                                        FROM    (-- VRA yang dimiliki oleh BA tsb
                                                 SELECT RKT.PERIOD_BUDGET,
                                                        ORG.REGION_CODE,
                                                        RKT.BA_CODE,
                                                        ORG.COMPANY_NAME,
                                                        RKT.VRA_CODE,
                                                        RKT.JUMLAH_ALAT,
                                                        RKT.TOTAL_QTY_TAHUN,
                                                        0 TOTAL_HM_KM_SENDIRI,
                                                        0 TOTAL_HM_KM_PINJAM
                                                   FROM TR_RKT_VRA RKT LEFT JOIN TM_ORGANIZATION ORG
                                                           ON ORG.BA_CODE = RKT.BA_CODE
                                                  WHERE     RKT.DELETE_USER IS NULL
                                                        AND RKT.FLAG_TEMP IS NULL
                                                        AND TO_CHAR (RKT.PERIOD_BUDGET, 'RRRR') = '".$params['budgetperiod']."'
                                                        AND ORG.REGION_CODE = '".$params['src_region_code']."'
                                                        AND RKT.BA_CODE = '".$params['key_find']."'
                                                 UNION ALL
                                                 -- VRA yg digunakan oleh BA tsb (bisa milik sendiri atau pinjam VRA dummy ZZ_)
                                                 SELECT RKT.PERIOD_BUDGET,
                                                        ORG.REGION_CODE,
                                                        RKT.BA_CODE,
                                                        ORG.COMPANY_NAME,
                                                        CASE
                                                           WHEN SUBSTR (RKT.VRA_CODE, 1, 3) = 'ZZ_'
                                                           THEN
                                                              SUBSTR (RKT.VRA_CODE, 4)
                                                           ELSE
                                                              RKT.VRA_CODE
                                                        END
                                                           AS VRA_CODE,
                                                        0 JUMLAH_ALAT,
                                                        0 TOTAL_QTY_TAHUN,
                                                        CASE
                                                           WHEN SUBSTR (RKT.VRA_CODE, 1, 3) <> 'ZZ_' THEN RKT.HM_KM
                                                           ELSE 0
                                                        END
                                                           AS TOTAL_HM_KM_SENDIRI,
                                                        CASE
                                                           WHEN SUBSTR (RKT.VRA_CODE, 1, 3) = 'ZZ_' THEN RKT.HM_KM
                                                           ELSE 0
                                                        END
                                                           AS TOTAL_HM_KM_PINJAM
                                                   FROM TR_RKT_VRA_DISTRIBUSI RKT LEFT JOIN TM_ORGANIZATION ORG
                                                           ON ORG.BA_CODE = RKT.BA_CODE
                                                  WHERE     RKT.DELETE_USER IS NULL
                                                        AND RKT.FLAG_TEMP IS NULL
                                                        AND TO_CHAR (RKT.PERIOD_BUDGET, 'RRRR') = '".$params['budgetperiod']."'
                                                        AND ORG.REGION_CODE = '".$params['src_region_code']."'
                                                        AND RKT.BA_CODE = '".$params['key_find']."') SUM_HM_KM
                                             LEFT JOIN
                                                TM_VRA VRA
                                             ON SUM_HM_KM.VRA_CODE = VRA.VRA_CODE
                                    GROUP BY SUM_HM_KM.PERIOD_BUDGET,
                                             SUM_HM_KM.REGION_CODE,
                                             SUM_HM_KM.BA_CODE,
                                             SUM_HM_KM.COMPANY_NAME,
                                             SUM_HM_KM.VRA_CODE,
                                             VRA.VRA_SUB_CAT_DESCRIPTION,
                                             VRA.TYPE,
                                             VRA.UOM
                                    ORDER BY SUM_HM_KM.PERIOD_BUDGET, SUM_HM_KM.BA_CODE, SUM_HM_KM.VRA_CODE) CEKS
                                    ON CEKS.BA_CODE = HEADER.BA_CODE
                                    AND CEKS.VRA_CODE = HEADER.VRA_CODE";
		return $query;
	}
	
	//ambil data dari DB - doni
    public function getDataAfdeling($params = array())
    {
		$query = "
			SELECT TRX_CODE,TMVRA.LOCATION_CODE, FLAG_TEMP, SUM(HM_KM) HM_KM, SUM(PRICE_HM_KM) PRICE_HM_KM 
			FROM TR_RKT_VRA_DISTRIBUSI VRA
LEFT JOIN TM_LOCATION_DIST_VRA TMVRA
    ON TMVRA.PERIOD_BUDGET = VRA.PERIOD_BUDGET
    AND TMVRA.BA_CODE = VRA.BA_CODE
    AND TMVRA.LOCATION_CODE = VRA.LOCATION_CODE			
			LEFT JOIN TM_ACTIVITY TA 
				ON TA.ACTIVITY_CODE = VRA.ACTIVITY_CODE 
			WHERE  to_char(VRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
				AND UPPER(VRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
				AND VRA.TIPE_TRANSAKSI = 'NON_INFRA'";
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(VRA.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TA.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)";
        }
		
		$query .= "GROUP BY TRX_CODE, TMVRA.LOCATION_CODE, FLAG_TEMP
			ORDER BY 1"; 
		
		//die($query);		
		return $query;
	}
	
	//ambil data afd dari TM_LOCATION_DIST_VRA
    public function getDataAfd($params = array())
    {
		
		$query = "
			SELECT ROWIDTOCHAR (location.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (location.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   location.BA_CODE,
				   location.LOCATION_CODE,
				   location.DESCRIPTION
			FROM TM_LOCATION_DIST_VRA location
			LEFT JOIN TM_ORGANIZATION B
				ON location.BA_CODE = B.BA_CODE
			WHERE location.DELETE_USER IS NULL 
				AND location.LOCATION_CODE NOT IN ('BIBITAN','BASECAMP','UMUM','LAIN')
		";

		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(location.BA_CODE)||'%'";
		}
		
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(location.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(location.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(location.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(location.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		
		$query .= "
			ORDER BY location.LOCATION_CODE
		"; 
		
		//die($query);
		return $query;
	}
	
	//menampilkan list norma distribusi vra non panen
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
        
		$sql = "SELECT COUNT(*) FROM ({$this->getDataHeader($params)})";
        $result['countHeader'] = $this->_db->fetchOne($sql);
		
		$sql = "SELECT COUNT(*) FROM ({$this->getDataAfdeling($params)})";
        $result['countData'] = $this->_db->fetchOne($sql);
		
		$sql = "SELECT COUNT(*) FROM ({$this->getDataAfd($params)})";
        $result['countAfd'] = $this->_db->fetchOne($sql);
		
        $rows = $this->_db->fetchAll("{$begin} {$this->getDataHeader($params)} {$end}");
		$rowsAfd = $this->_db->fetchAll("{$this->getDataAfdeling($params)}");
		$tabs = $this->_db->fetchAll("{$this->getDataAfd($params)}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
            }
        }		
		
		if (!empty($rowsAfd)) {
            foreach ($rowsAfd as $idx => $row) {
				$result['rowsAfd'][$row['TRX_CODE']][] = $row;
            }
        }
		
		if (!empty($tabs)) {
            foreach ($tabs as $idx => $tab) {
				$result['tabs'][] = $tab;
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
		//die($sql);
        return $result;
    }
	
	//menampilkan list afdeling yang ada
    public function getListAfd($params = array())
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
        
		$sql = "SELECT COUNT(*) FROM ({$this->getDataAfd($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $tabs = $this->_db->fetchAll("{$begin} {$this->getDataAfd($params)} {$end}");
		if (!empty($tabs)) {
            foreach ($tabs as $idx => $row) {
				$result['tabs'][] = $row;
            }
        }

        return $result;
    }
	
	public function checkAktivitasRawat($activity_code){
		$sql = "
			SELECT COUNT(*)
			FROM TM_ACTIVITY_MAPPING
			WHERE ACTIVITY_CODE = '".$activity_code."'
			AND ACTIVITY_GROUP_TYPE LIKE '%DIS_VRA_NON_INFRA%'
            AND DELETE_USER IS NULL
		";
		$result = $this->_db->fetchOne($sql);
		
		return $result;
	}
	
	public function saveTemp($row = array(), $rowAfd = array(), $ba_code)
    { 
		
		//print_r ($row); //die;
		$result = true;
		
		$arrKeys=array_keys($rowAfd);
		//print_r ($arrKeys);
	
		$sql = "";
		IF ($row['ACTIVITY_CODE']){
			//jika aktivitas rawat, maka tidak dapat melakukan pengisian di base camp, bibitan, umum, lainnya
			if($this->checkAktivitasRawat($row['ACTIVITY_CODE']) == 0){
				$rowAfd['BIBITAN'] = 0;
				$rowAfd['BASECAMP'] = 0;
				$rowAfd['UMUM'] = 0;
				$rowAfd['LAIN'] = 0;
			}
			//print_r ($rowAfd); //die;
			for($x=0;$x<count($rowAfd);$x++){
				//insert data input baru sebagai temp data
				$trx_code = $row['PERIOD_BUDGET'] ."-".
							addslashes($ba_code) ."-RKT016-".
							addslashes($row['ACTIVITY_CODE']) ."-".
							addslashes($row['VRA_CODE']);
				$rowAfd[$arrKeys[$x]] = ($rowAfd[$arrKeys[$x]]) ? $rowAfd[$arrKeys[$x]] : '0';
				//print_r($rowAfd);
				//delete data
				$sql.= "
							DELETE FROM TR_RKT_VRA_DISTRIBUSI 
							WHERE 
								PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
								AND BA_CODE='".addslashes($ba_code)."' 
								AND ACTIVITY_CODE ='".addslashes($row['ACTIVITY_CODE'])."' 
								AND VRA_CODE = '".addslashes($row['VRA_CODE'])."' 
								AND LOCATION_CODE ='".$arrKeys[$x]."';
						";
				
				$cek_afd = "SELECT location.LOCATION_CODE
								FROM TM_LOCATION_DIST_VRA location LEFT JOIN TM_ORGANIZATION B
										ON location.BA_CODE = B.BA_CODE
							   WHERE location.DELETE_USER IS NULL
									 AND location.LOCATION_CODE NOT IN
											  ('BIBITAN', 'BASECAMP', 'UMUM', 'LAIN')
									 AND TO_CHAR (location.PERIOD_BUDGET, 'RRRR') = '{$row['PERIOD_BUDGET']}'
									 AND UPPER (location.BA_CODE) LIKE UPPER ('%{$ba_code}%')
							ORDER BY location.LOCATION_CODE";
							
				$row_afd = $this->_db->fetchAll($cek_afd);
				//foreach($row_afd as $rowx){
					//if($rowx['LOCATION_CODE'] == $arrKeys[$x]){
						$sql.= "
							INSERT INTO TR_RKT_VRA_DISTRIBUSI (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, LOCATION_CODE, HM_KM, PRICE_QTY_VRA, 
							PRICE_HM_KM, TRX_CODE, TIPE_TRANSAKSI, FLAG_TEMP, INSERT_USER, INSERT_TIME)
							VALUES (
								TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($ba_code)."',
								'".addslashes($row['ACTIVITY_CODE'])."',
								'".addslashes($row['VRA_CODE'])."',
								REPLACE('".$arrKeys[$x]."',',',''),
								REPLACE('".addslashes($rowAfd[$arrKeys[$x]])."',',',''),
								0,
								0,
								'{$trx_code}',
								'NON_INFRA',
								'Y',
								'{$this->_userName}',
								SYSDATE
							);
						"; //echo $sql; //die;
					//}
				//}
				//die();			
			}
			//die;
		} 
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
	}
	
	//simpan data
	public function save($row = array(), $rowAfd = array(), $ba_code)
    { 
        $result = true;
		//echo(count($rowAfd));die(':die');
		// ********************************************** UPDATE NORMA DISTRIBUSI VRA **********************************************
		$arrKeys=array_keys($rowAfd); //die(count($rowAfd)); //print_r($rowAfd);	die(' :end!');
		$sql = "";
		IF ($row['ACTIVITY_CODE']){
			//jika aktivitas rawat, maka tidak dapat melakukan pengisian di base camp, bibitan, umum, lainnya
			if($this->checkAktivitasRawat($row['ACTIVITY_CODE']) == 0){
				$rowAfd['BIBITAN'] = 0;
				$rowAfd['BASECAMP'] = 0;
				$rowAfd['UMUM'] = 0;
				$rowAfd['LAIN'] = 0;
			}
		
			$oldChkVra="";
			$price_qty_vra=$this->getVraPrice($row,$ba_code);
			$rand = substr(md5(microtime()),rand(0,26),5);
			//$trx_code=substr($this->_period,-4).$ba_code.'VRA'.date('Ymd').$rand;
			//$trx_code = $this->_formula->gen_TransactionCode(substr($this->_period,-4),$ba_code,'VRA'); //doni
			for($x=0;$x<count($rowAfd);$x++){
				//echo(count($rowAfd));die(':die');
				$trx_code = $row['PERIOD_BUDGET'] ."-".
						addslashes($ba_code) ."-RKT016-".
						addslashes($row['ACTIVITY_CODE']) ."-".
						addslashes($row['VRA_CODE']);
				$rowAfd[$arrKeys[$x]] = ($rowAfd[$arrKeys[$x]]) ? $rowAfd[$arrKeys[$x]] : '0';
					
				$sql.= "
					DELETE FROM TR_RKT_VRA_DISTRIBUSI 
					WHERE 
						PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE='".addslashes($ba_code)."' 
						AND ACTIVITY_CODE ='".addslashes($row['ACTIVITY_CODE'])."' 
						AND VRA_CODE = '".addslashes($row['VRA_CODE'])."' 
						AND LOCATION_CODE ='".$arrKeys[$x]."';
						";
				
				
				$sql.= "INSERT INTO 
							TR_RKT_VRA_DISTRIBUSI (
								PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, LOCATION_CODE, HM_KM, PRICE_QTY_VRA, 
								PRICE_HM_KM, TRX_CODE, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
						VALUES (
								TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($ba_code)."',
								'".addslashes($row['ACTIVITY_CODE'])."',
								'".addslashes($row['VRA_CODE'])."',
								REPLACE('".$arrKeys[$x]."',',',''),
								REPLACE('".addslashes($rowAfd[$arrKeys[$x]])."',',',''),
								REPLACE('$price_qty_vra',',',''),
								(REPLACE('$price_qty_vra',',','') * REPLACE('".addslashes($rowAfd[$arrKeys[$x]])."',',','')),
								'{$trx_code}',
								'NON_INFRA',
								'{$this->_userName}',
								SYSDATE);
								";
			}
		}
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
		
	public function getVraPrice($row,$ba_code){
		$sql="
			SELECT value 
			FROM TR_RKT_VRA_SUM 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE='{$ba_code}' 
				AND VRA_CODE='{$row['VRA_CODE']}'";
		 $vraPrice = $this->_db->fetchOne($sql);
		 if($vraPrice=="") $vraPrice=0;
		 return $vraPrice;
	}
	
	//update summary
	public function updateSummaryNormaDistribusiVra($param = array())
	{
		$sql = "
			DELETE FROM TR_RKT_VRA_DISTRIBUSI_SUM
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$param['PERIOD_BUDGET']}'
				AND BA_CODE IN ('".($param['BA_CODE'])."')
				AND VRA_CODE IN ('".($param['VRA_CODE'])."')
				AND ACTIVITY_CODE IN ('".($param['ACTIVITY_CODE'])."') 
				AND TIPE_TRANSAKSI = 'NON_INFRA';
		"; 
			
		$sqlsum = "
			SELECT ACTIVITY_CODE, SUM(HM_KM) TOTAL_HM_KM, SUM(PRICE_HM_KM) TOTAL_PRICE_HM_KM
			FROM TR_RKT_VRA_DISTRIBUSI
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$param['PERIOD_BUDGET']}'
				AND BA_CODE IN ('".($param['BA_CODE'])."')
				AND VRA_CODE IN ('".($param['VRA_CODE'])."')
				AND ACTIVITY_CODE IN ('".($param['ACTIVITY_CODE'])."')
				AND TIPE_TRANSAKSI = 'NON_INFRA'
                AND DELETE_USER IS NULL
			GROUP BY BA_CODE,ACTIVITY_CODE,VRA_CODE
		";
		$rows = $this->_db->fetchAll($sqlsum);
		
		foreach ($rows as $idx => $row) {
			//insert DB
			$sql.= "
				INSERT INTO TR_RKT_VRA_DISTRIBUSI_SUM (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, TOTAL_HM_KM, TOTAL_PRICE_HM_KM, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-{$param['PERIOD_BUDGET']}','DD-MM-RRRR'),
					'".addslashes($param['BA_CODE'])."',
					'".addslashes($param['ACTIVITY_CODE'])."',
					'".addslashes($param['VRA_CODE'])."',
					'".$row['TOTAL_HM_KM']."',
					'".$row['TOTAL_PRICE_HM_KM']."',
					'NON_INFRA',
					'{$this->_userName}',
					SYSDATE
				);
			";
		}
		
		//create sql file
		$this->_global->createSqlFile($param['filename'], $sql);	
	}
	
	//hapus data
	public function delete($row = array())
    {
		
		$sql .= "
			UPDATE TR_RKT_VRA_DISTRIBUSI
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE TRX_CODE = '{$row['trxcode']}'
				AND TIPE_TRANSAKSI = 'NON_INFRA';
		";
		//YUS 18/11/2014 : tambahan delete ke TR_RKT_VRA_DISTRIBUSI_SUM
		$sql .= "
			UPDATE TR_RKT_VRA_DISTRIBUSI_SUM
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE 
				PERIOD_BUDGET = '01-01-{$row['period_budget']}' AND
				BA_CODE = '{$row['ba_code']}' AND
				ACTIVITY_CODE = '{$row['activity_code']}' AND
				VRA_CODE = '{$row['vra_code']}' AND 
				TIPE_TRANSAKSI = 'NON_INFRA';
		";
		
		//YUS 18/11/2014 : tambahan delete ke TR_RKT_opex
		$sql .= "
			UPDATE TR_RKT_OPEX
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE 
				PERIOD_BUDGET='01-01-{$row['period_budget']}' AND BA_CODE || COA_CODE IN (
				SELECT  A.BA_CODE || B.COA_CODE FROM TR_RKT_VRA_DISTRIBUSI_SUM A 
				LEFT JOIN TM_ACTIVITY_COA B ON B.ACTIVITY_CODE = A.ACTIVITY_CODE  
				WHERE A.DELETE_USER='SYSTEM';
		";
					 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//insert ke OPEX VRA
	public function updateRktOpexVra($par = array()){
		//print_r($par);die();
		//select data yg ada di OPEX VRA
		$sql = "
			SELECT DISTINCT COA_CODE
			FROM TM_COA
			WHERE DELETE_USER IS NULL
				AND FLAG IN ('VRA')";
		$rows = $this->_db->fetchAll($sql);
		
		//get region code
		$region_code = $this->_formula->get_RegionCode($par['key_find']);
		$sql="";
		foreach ($rows as $idx => $row) {
			//cek data
			$sqlchk = "
				SELECT COUNT(*) 
				FROM TR_RKT_OPEX
				WHERE BA_CODE = '".addslashes($par['key_find'])."'
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$par['PERIOD_BUDGET']}'
					AND COA_CODE = '".addslashes($row['COA_CODE'])."'
			";
			$count = $this->_db->fetchOne($sqlchk);
			
			if ($count) {
				$sql.= "
					DELETE FROM TR_RKT_OPEX
					WHERE BA_CODE = '".addslashes($par['key_find'])."'
						AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$par['PERIOD_BUDGET']}'
						AND COA_CODE = '".addslashes($row['COA_CODE'])."';				
				";
			}
			//total biaya
			//YUS 02/09/2014 ganti kondisi mapping.COA_CODE menjadi rkt.ACTIVITY_CODE, disesuain sm yg ada di formula function cal_RktOpex_Total() 
			$sqlsum = "
				SELECT NVL(SUM(rkt.TOTAL_PRICE_HM_KM),0 )
				FROM TR_RKT_VRA_DISTRIBUSI_SUM rkt
				LEFT JOIN  
				(SELECT DISTINCT ACTIVITY_CODE, COA_CODE
				 FROM TM_ACTIVITY_COA
				 WHERE DELETE_USER IS NULL) mapping
					ON rkt.ACTIVITY_CODE = mapping.ACTIVITY_CODE
					AND rkt.TIPE_TRANSAKSI = 'NON_INFRA'
				WHERE rkt.BA_CODE = '".addslashes($par['key_find'])."'
					AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$par['PERIOD_BUDGET']}'
					AND rkt.ACTIVITY_CODE = '".addslashes($row['COA_CODE'])."'
					AND rkt.DELETE_USER IS NULL
			";
			
			$total_biaya = $this->_db->fetchOne($sqlsum);
			$distribusi_bulanan = $total_biaya / 12;
			
			$sql.= "
				INSERT INTO TR_RKT_OPEX (PERIOD_BUDGET, BA_CODE, REGION_CODE, COA_CODE, GROUP_BUM_CODE, TOTAL_BIAYA, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-{$par['PERIOD_BUDGET']}','DD-MM-RRRR'),
					'".addslashes($par['key_find'])."',
					'".addslashes($region_code)."',
					'".addslashes($row['COA_CODE'])."',
					'1',
					'".$total_biaya."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'".$distribusi_bulanan."',
					'VRA',
					'{$this->_userName}',
					SYSDATE
				);
			";
		} 
		
		//create sql file
		$this->_global->createSqlFile($par['filename'], $sql);
	}
	
	//update record berdasarkan VRA & BA CODE 
	public function updateRecord($row = array()){
		//cari Rp/Qty : 
		//jika awalan ZZ_ maka VRA pinjaman dan ambil Rp/Qty dari TN_VRA_PINJAM
		//jika tidak ambil dari TR_RKT_VRA_SUM
		if (substr($row['VRA_CODE'], 0, 3) == 'ZZ_'){
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			
			$sql = "
				SELECT RP_QTY
				FROM TN_VRA_PINJAM
				WHERE REGION_CODE='".$region_code."' 
					AND VRA_CODE='".$row['VRA_CODE']."'
					AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}' 
			";
		}else{
			$sql = "
				SELECT VALUE
				FROM TR_RKT_VRA_SUM
				WHERE BA_CODE='".$row['BA_CODE']."' 
					AND VRA_CODE='".$row['VRA_CODE']."'
					AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}' 
			"; 
		}
		//print_r($row);
		//die($sql);
		$rp_qty = $this->_db->fetchOne($sql);
		
			
		if ($row['ACTIVITY_CODE'] != '') {
			$where = " AND ACTIVITY_CODE='".$row['ACTIVITY_CODE']."' ";
        }
		
		$sql="
			UPDATE TR_RKT_VRA_DISTRIBUSI
			SET PRICE_QTY_VRA = $rp_qty,
				PRICE_HM_KM = ( HM_KM * nvl($rp_qty,0)),
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE  TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}' 
				AND BA_CODE='".$row['BA_CODE']."' 
				AND VRA_CODE='".$row['VRA_CODE']."'
				AND TIPE_TRANSAKSI = 'NON_INFRA'
				$where
				AND DELETE_USER IS NULL;
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
	
	//get data yang berubah di dist VRA non infra
	public function getChangedData($row = array()){
		if($row['CHANGE']){
			$where .= "AND FLAG_TEMP = 'Y'";
		}
		
		if($row['VRA_CODE']){
			$where .= " AND VRA_CODE IN ('".$row['VRA_CODE']."') ";
		}
		
		if($row['key_find']){
			$where .= " AND BA_CODE IN ('".$row['key_find']."') ";
			
		}
		
		if($row['budgetperiod']){
			$where .= " AND TO_CHAR (PERIOD_BUDGET, 'RRRR') = '".$row['budgetperiod']."' ";
			
		}elseif($row['PERIOD_BUDGET']){
			$where .= " AND TO_CHAR (PERIOD_BUDGET, 'RRRR') = '".$row['PERIOD_BUDGET']."' ";
			
		}else{
			$where .= " AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '{$this->_period}' ";
		}
		
		$sql="
			SELECT PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, FLAG_TEMP
			FROM TR_RKT_VRA_DISTRIBUSI
			WHERE  TIPE_TRANSAKSI = 'NON_INFRA'
				$where
				AND DELETE_USER IS NULL
			GROUP BY PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, FLAG_TEMP
		";
		//echo $sql;
		return $sql;
	}
}

