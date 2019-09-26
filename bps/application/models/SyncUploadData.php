<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Generate .sql & sync data ke HO
Function 			:	- genNormaBiaya				: SID 12/08/2014	: generate norma biaya
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	12/08/2014
Update Terakhir		:	12/08/2014
Revisi				:	

=========================================================================================================================
*/
class Application_Model_SyncUploadData
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db'); //setting database local
        $this->_global = new Application_Model_Global();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//generate norma biaya
	public function genNormaBiaya($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_BIAYA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
		
		
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_GROUP, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.COST_ELEMENT, 
				   A.SUB_COST_ELEMENT, 
				   A.QTY, 
				   A.ROTASI, 
				   A.VOLUME, 
				   A.QTY_HA, 
				   A.PRICE, 
				   A.PRICE_HA, 
				   A.PRICE_ROTASI, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   A.QTY_SITE, 
				   A.ROTASI_SITE, 
				   A.VOLUME_SITE, 
				   A.QTY_HA_SITE, 
				   A.PRICE_SITE, 
				   A.PRICE_HA_SITE, 
				   A.PRICE_ROTASI_SITE, 
				   A.FLAG_SITE, 
				   A.FLAG_TEMP
			FROM TN_BIAYA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_BIAYA (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_GROUP, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, COST_ELEMENT, SUB_COST_ELEMENT, QTY, ROTASI, VOLUME, QTY_HA, PRICE, PRICE_HA, PRICE_ROTASI, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, QTY_SITE, ROTASI_SITE, VOLUME_SITE, QTY_HA_SITE, PRICE_SITE, PRICE_HA_SITE, PRICE_ROTASI_SITE, FLAG_SITE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_GROUP']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['SUB_COST_ELEMENT']."',
					    '".$row['QTY']."',
					    '".$row['ROTASI']."',
					    '".$row['VOLUME']."',
					    '".$row['QTY_HA']."',
					    '".$row['PRICE']."',
					    '".$row['PRICE_HA']."',
					    '".$row['PRICE_ROTASI']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['QTY_SITE']."',
					    '".$row['ROTASI_SITE']."',
					    '".$row['VOLUME_SITE']."',
					    '".$row['QTY_HA_SITE']."',
					    '".$row['PRICE_SITE']."',
					    '".$row['PRICE_HA_SITE']."',
					    '".$row['PRICE_ROTASI_SITE']."',
					    '".$row['FLAG_SITE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
			
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit	
        return true;
    }
	
	//generate norma harga borong
	public function genNormaHargaBorong($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_HARGA_BORONG
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_HARGA_BORONG_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND REGION_CODE = '".$row['REGION_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_HARGA_BORONG
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.SPESIFICATION, 
				   A.PRICE, 
				   A.PRICE_SITE, 
				   A.FLAG_SITE, 
				   A.FLAG_TEMP,				   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_HARGA_BORONG A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BORONG (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, SPESIFICATION, PRICE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, PRICE_SITE, FLAG_SITE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['SPESIFICATION']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['PRICE_SITE']."',
					    '".$row['FLAG_SITE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_HARGA_BORONG_SUM
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND A.REGION_CODE IN (
					SELECT REGION_CODE
					FROM TM_ORGANIZATION
					WHERE UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'
					GROUP BY REGION_CODE
				)";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.PRICE, 
				   A.FLAG_SITE, 
				   A.PRICE_SITE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_HARGA_BORONG_SUM A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BORONG_SUM (
						PERIOD_BUDGET, REGION_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, PRICE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_SITE, PRICE_SITE
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_SITE']."',
					    '".$row['PRICE_SITE']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate norma checkroll
	public function genNormaCheckroll($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_CHECKROLL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL_DETAIL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RPT_DISTRIBUSI_COA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_CHECKROLL
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.GP, 
				   A.MPP_AKTUAL, 
				   A.MPP_PERIOD_BUDGET, 
				   A.MPP_REKRUT, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_CHECKROLL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_CHECKROLL (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, GP, MPP_AKTUAL, MPP_PERIOD_BUDGET, MPP_REKRUT, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['GP']."',
					    '".$row['MPP_AKTUAL']."',
					    '".$row['MPP_PERIOD_BUDGET']."',
					    '".$row['MPP_REKRUT']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_CHECKROLL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.TRX_CR_CODE, 
				   A.GP_INFLASI, 
				   A.MPP_PERIOD_BUDGET, 
				   A.TOTAL_GP_MPP, 
				   A.TOTAL_GAJI_TUNJANGAN, 
				   A.RP_HK_PERBULAN, 
				   A.TOTAL_TUNJANGAN_PK_UMUM, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.DIS_YEAR, 
				   A.TOTAL_TUNJANGAN_WRA, 
				   A.TOTAL_TUNJANGAN_VRA,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, TRX_CR_CODE, GP_INFLASI, MPP_PERIOD_BUDGET, TOTAL_GP_MPP, TOTAL_GAJI_TUNJANGAN, RP_HK_PERBULAN, TOTAL_TUNJANGAN_PK_UMUM, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, DIS_YEAR, TOTAL_TUNJANGAN_WRA, TOTAL_TUNJANGAN_VRA
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['TRX_CR_CODE']."',
					    '".$row['GP_INFLASI']."',
					    '".$row['MPP_PERIOD_BUDGET']."',
					    '".$row['TOTAL_GP_MPP']."',
					    '".$row['TOTAL_GAJI_TUNJANGAN']."',
					    '".$row['RP_HK_PERBULAN']."',
					    '".$row['TOTAL_TUNJANGAN_PK_UMUM']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DIS_YEAR']."',
					    '".$row['TOTAL_TUNJANGAN_WRA']."',
					    '".$row['TOTAL_TUNJANGAN_VRA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
		
		//get new data - TR_RKT_CHECKROLL_DETAIL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_CR_CODE, 
				   A.TUNJANGAN_TYPE, 
				   A.JUMLAH,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL_DETAIL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL_DETAIL (
						PERIOD_BUDGET, BA_CODE, TRX_CR_CODE, TUNJANGAN_TYPE, JUMLAH, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TRX_CR_CODE']."',
					    '".$row['TUNJANGAN_TYPE']."',
					    '".$row['JUMLAH']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_CHECKROLL_SUM		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.RP_HK,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL_SUM (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, RP_HK, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['RP_HK']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }

		//get new data - TR_RPT_DISTRIBUSI_COA		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.MATURITY_STAGE, 
				   A.TUNJANGAN_TYPE, 
				   A.TOTAL_BIAYA, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.REPORT_TYPE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RPT_DISTRIBUSI_COA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RPT_DISTRIBUSI_COA (
						PERIOD_BUDGET, BA_CODE, MATURITY_STAGE, TUNJANGAN_TYPE, TOTAL_BIAYA, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, REPORT_TYPE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['MATURITY_STAGE']."',
					    '".$row['TUNJANGAN_TYPE']."',
					    '".$row['TOTAL_BIAYA']."',
						'".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['REPORT_TYPE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate norma WRA
	public function genNormaWra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_WRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_WRA_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_WRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.GROUP_WRA_CODE, 
				   A.SUB_WRA_GROUP, 
				   A.QTY_ROTASI, 
				   A.ROTASI_TAHUN, 
				   A.QTY_TAHUN, 
				   A.PRICE, 
				   A.PRICE_QTY_TAHUN, 
				   A.RP_QTY,	
				   A.TRIGGER_UPDATE, 
				   A.FLAG_TEMP,			   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_WRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_WRA (
						PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, PRICE, PRICE_QTY_TAHUN, RP_QTY, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['GROUP_WRA_CODE']."',
					    '".$row['SUB_WRA_GROUP']."',
					    '".$row['QTY_ROTASI']."',
					    '".$row['ROTASI_TAHUN']."',
					    '".$row['QTY_TAHUN']."',
					    '".$row['PRICE']."',
					    '".$row['PRICE_QTY_TAHUN']."',
					    '".$row['RP_QTY']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_WRA_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TOTAL_RP_QTY, 
				   A.TRIGGER_UPDATE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_WRA_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_WRA_SUM (
						PERIOD_BUDGET, BA_CODE, TOTAL_RP_QTY, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate norma alat kerja panen
	public function genNormaAlatKerjaPanen($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_ALAT_KERJA_PANEN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_ALAT_KERJA_PANEN_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_ALAT_KERJA_PANEN
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.MATERIAL_CODE, 
				   A.ROTASI, 
				   A.PRICE, 
				   A.TOTAL,
				   A.TRIGGER_UPDATE, 
				   A.FLAG_TEMP,			   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_ALAT_KERJA_PANEN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_ALAT_KERJA_PANEN (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, ROTASI, PRICE, TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['ROTASI']."',
					    '".$row['PRICE']."',
					    '".$row['TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_ALAT_KERJA_PANEN_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.PRICE_SUM, 
				   A.PRICE_ROTASI_SUM, 
				   A.PRICE_KG, 
				   A.TRIGGER_UPDATE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_ALAT_KERJA_PANEN_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_ALAT_KERJA_PANEN_SUM (
						PERIOD_BUDGET, BA_CODE, PRICE_SUM, PRICE_ROTASI_SUM, PRICE_KG, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['PRICE_SUM']."',
					    '".$row['PRICE_ROTASI_SUM']."',
					    '".$row['PRICE_KG']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate hectare statement
	public function genHectareStatement($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "						
					DELETE FROM TM_HECTARE_STATEMENT_DETAIL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TM_HECTARE_STATEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TM_HECTARE_STATEMENT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_PLANTED, 
				   A.TOPOGRAPHY, 
				   A.LAND_TYPE, 
				   A.PROGENY, 
				   A.LAND_SUITABILITY, 
				   A.TAHUN_TANAM, 
				   A.POKOK_TANAM, 
				   A.SPH, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.STATUS,	
				   A.KONVERSI_TBM, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_HECTARE_STATEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_HECTARE_STATEMENT (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PLANTED, TOPOGRAPHY, LAND_TYPE, PROGENY, LAND_SUITABILITY, TAHUN_TANAM, POKOK_TANAM, SPH, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, KONVERSI_TBM, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_PLANTED']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['PROGENY']."',
					    '".$row['LAND_SUITABILITY']."',
					    '".$row['TAHUN_TANAM']."',
					    '".$row['POKOK_TANAM']."',
					    '".$row['SPH']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['KONVERSI_TBM']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TM_HECTARE_STATEMENT_DETAIL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.LAND_CATEGORY, 
				   A.HA, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_HECTARE_STATEMENT_DETAIL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_HECTARE_STATEMENT_DETAIL (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, LAND_CATEGORY, HA, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['LAND_CATEGORY']."',
					    '".$row['HA']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate perencanaan produksi
	public function genPerencanaanProduksi($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_PRODUKSI_PERIODE_BUDGET
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_PRODUKSI_TAHUN_BERJALAN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_PRODUKSI_PERIODE_BUDGET
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_SMS1, 
				   A.POKOK_SMS1, 
				   A.SPH_SMS1, 
				   A.HA_SMS2, 
				   A.POKOK_SMS2, 
				   A.SPH_SMS2, 
				   A.YPH_PROFILE, 
				   A.TON_PROFILE, 
				   A.YPH_PROPORTION, 
				   A.TON_PROPORTION, 
				   A.JANJANG_BUDGET, 
				   A.BJR_BUDGET, 
				   A.TON_BUDGET, 
				   A.YPH_BUDGET, 
				   A.JAN, A.FEB, A.MAR, 
				   A.APR, A.MAY, A.JUN, 
				   A.JUL, A.AUG, A.SEP, 
				   A.OCT, A.NOV, A.DEC, 
				   A.SMS1, A.SMS2, 
				   A.TRX_CODE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_PRODUKSI_PERIODE_BUDGET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_PRODUKSI_PERIODE_BUDGET (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_SMS1, POKOK_SMS1, SPH_SMS1, HA_SMS2, POKOK_SMS2, SPH_SMS2, YPH_PROFILE, TON_PROFILE, YPH_PROPORTION, TON_PROPORTION, JANJANG_BUDGET, BJR_BUDGET, TON_BUDGET, YPH_BUDGET, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SMS1, SMS2, TRX_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_SMS1']."',
					    '".$row['POKOK_SMS1']."',
					    '".$row['SPH_SMS1']."',
					    '".$row['HA_SMS2']."',
					    '".$row['POKOK_SMS2']."',
					    '".$row['SPH_SMS2']."',
					    '".$row['YPH_PROFILE']."',
					    '".$row['TON_PROFILE']."',
					    '".$row['YPH_PROPORTION']."',
					    '".$row['TON_PROPORTION']."',
					    '".$row['JANJANG_BUDGET']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['TON_BUDGET']."',
					    '".$row['YPH_BUDGET']."',
					    '".$row['JAN']."', '".$row['FEB']."', '".$row['MAR']."',
					    '".$row['APR']."', '".$row['MAY']."', '".$row['JUN']."',
					    '".$row['JUL']."', '".$row['AUG']."', '".$row['SEP']."',
					    '".$row['OCT']."', '".$row['NOV']."', '".$row['DEC']."',
						'".$row['SMS1']."', '".$row['SMS2']."',
						'".$row['TRX_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_PRODUKSI_TAHUN_BERJALAN
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_PANEN, 
				   A.POKOK_PRODUKTIF, 
				   A.SPH_PRODUKTIF, 
				   A.TON_AKTUAL, 
				   A.JANJANG_AKTUAL, 
				   A.BJR_AKTUAL, 
				   A.YPH_AKTUAL, 
				   A.TON_TAKSASI, 
				   A.JANJANG_TAKSASI, 
				   A.BJR_TAKSASI, 
				   A.YPH_TAKSASI, 
				   A.TON_BUDGET, 
				   A.YPH_BUDGET, 
				   A.VAR_YPH, 
				   A.TRX_CODE, 
				   A.TON_ANTISIPASI, 
				   A.JANJANG_ANTISIPASI, 
				   A.BJR_ANTISIPASI, 
				   A.YPH_ANTISIPASI,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_PRODUKSI_TAHUN_BERJALAN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_PRODUKSI_TAHUN_BERJALAN (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PANEN, POKOK_PRODUKTIF, SPH_PRODUKTIF, TON_AKTUAL, JANJANG_AKTUAL, BJR_AKTUAL, YPH_AKTUAL, TON_TAKSASI, JANJANG_TAKSASI, BJR_TAKSASI, YPH_TAKSASI, TON_BUDGET, YPH_BUDGET, VAR_YPH, TRX_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TON_ANTISIPASI, JANJANG_ANTISIPASI, BJR_ANTISIPASI, YPH_ANTISIPASI
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_PANEN']."',
					    '".$row['POKOK_PRODUKTIF']."',
					    '".$row['SPH_PRODUKTIF']."',
					    '".$row['TON_AKTUAL']."',
					    '".$row['JANJANG_AKTUAL']."',
					    '".$row['BJR_AKTUAL']."',
					    '".$row['YPH_AKTUAL']."',
					    '".$row['TON_TAKSASI']."',
					    '".$row['JANJANG_TAKSASI']."',
					    '".$row['BJR_TAKSASI']."',
					    '".$row['YPH_TAKSASI']."',
					    '".$row['TON_BUDGET']."',
					    '".$row['YPH_BUDGET']."',
					    '".$row['VAR_YPH']."',
					    '".$row['TRX_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TON_ANTISIPASI']."',
					    '".$row['JANJANG_ANTISIPASI']."',
					    '".$row['BJR_ANTISIPASI']."',
					    '".$row['YPH_ANTISIPASI']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT CAPEX
	public function genRktCapex($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_CAPEX
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_CAPEX
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_CODE,
				   A.REGION_CODE, 
				   A.COA_CODE, 
				   A.ASSET_CODE, 
				   A.DETAIL_SPESIFICATION, 
				   A.URGENCY_CAPEX, 
				   A.PRICE, 
				   A.QTY_ACTUAL, 
				   A.DIS_TAHUN_BERJALAN, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.DIS_BIAYA_JAN, A.DIS_BIAYA_FEB, A.DIS_BIAYA_MAR, 
				   A.DIS_BIAYA_APR, A.DIS_BIAYA_MAY, A.DIS_BIAYA_JUN, 
				   A.DIS_BIAYA_JUL, A.DIS_BIAYA_AUG, A.DIS_BIAYA_SEP, 
				   A.DIS_BIAYA_OCT, A.DIS_BIAYA_NOV, A.DIS_BIAYA_DEC, 
				   A.DIS_BIAYA_TOTAL,
				   A.COST_SMS1, A.COST_SMS2, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CAPEX A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CAPEX (
						TRX_CODE, PERIOD_BUDGET, BA_CODE, REGION_CODE, COA_CODE, ASSET_CODE, DETAIL_SPESIFICATION, URGENCY_CAPEX, PRICE, QTY_ACTUAL, DIS_TAHUN_BERJALAN, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, DIS_BIAYA_JAN, DIS_BIAYA_FEB, DIS_BIAYA_MAR, DIS_BIAYA_APR, DIS_BIAYA_MAY, DIS_BIAYA_JUN, DIS_BIAYA_JUL, DIS_BIAYA_AUG, DIS_BIAYA_SEP, DIS_BIAYA_OCT, DIS_BIAYA_NOV, DIS_BIAYA_DEC, DIS_BIAYA_TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, FLAG_TEMP
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['REGION_CODE']."',
					    '".$row['COA_CODE']."',
					    '".$row['ASSET_CODE']."',
					    '".$row['DETAIL_SPESIFICATION']."',
					    '".$row['URGENCY_CAPEX']."',
					    '".$row['PRICE']."',
					    '".$row['QTY_ACTUAL']."',
					    '".$row['DIS_TAHUN_BERJALAN']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['DIS_BIAYA_JAN']."', '".$row['DIS_BIAYA_FEB']."', '".$row['DIS_BIAYA_MAR']."',
					    '".$row['DIS_BIAYA_APR']."', '".$row['DIS_BIAYA_MAY']."', '".$row['DIS_BIAYA_JUN']."',
					    '".$row['DIS_BIAYA_JUL']."', '".$row['DIS_BIAYA_AUG']."', '".$row['DIS_BIAYA_SEP']."',
					    '".$row['DIS_BIAYA_OCT']."', '".$row['DIS_BIAYA_NOV']."', '".$row['DIS_BIAYA_DEC']."',
					    '".$row['DIS_BIAYA_TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT LC
	public function genRktLc($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_LC
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_LC
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.SUMBER_BIAYA, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, 
				   A.TOTAL_RP_QTY, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.COST_SETAHUN, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_LC A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_LC (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, SUMBER_BIAYA, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, TOTAL_RP_QTY, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SMS1, COST_SMS2, COST_SETAHUN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_LC_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.SUMBER_BIAYA, 
				   A.COST_ELEMENT, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, 
				   A.TOTAL_RP_QTY, 
				   A.DIS_COST_JAN, A.DIS_COST_FEB, A.DIS_COST_MAR, 
				   A.DIS_COST_APR, A.DIS_COST_MAY, A.DIS_COST_JUN, 
				   A.DIS_COST_JUL, A.DIS_COST_AUG, A.DIS_COST_SEP, 
				   A.DIS_COST_OCT, A.DIS_COST_NOV, A.DIS_COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.DIS_COST_SETAHUN,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_LC_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_LC_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, SUMBER_BIAYA, COST_ELEMENT, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, TOTAL_RP_QTY, DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC, COST_SMS1, COST_SMS2, DIS_COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['DIS_COST_JAN']."', '".$row['DIS_COST_FEB']."', '".$row['DIS_COST_MAR']."',
					    '".$row['DIS_COST_APR']."', '".$row['DIS_COST_MAY']."', '".$row['DIS_COST_JUN']."',
					    '".$row['DIS_COST_JUL']."', '".$row['DIS_COST_AUG']."', '".$row['DIS_COST_SEP']."',
					    '".$row['DIS_COST_OCT']."', '".$row['DIS_COST_NOV']."', '".$row['DIS_COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['DIS_COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT Rawat, Rawat Opsi, Rawat Infra, Tanam Manual, Tanam Otomatis
	public function genRkt($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTIVITY_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.BULAN_PENGERJAAN, 
				   A.ACTIVITY_CLASS, 
				   A.ATRIBUT, 
				   A.SUMBER_BIAYA, 
				   A.ROTASI_SMS1, 
				   A.ROTASI_SMS2, 
				   A.TOTAL_RP_SMS1, 
				   A.TOTAL_RP_SMS2, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SMS1, A.PLAN_SMS2, A.PLAN_SETAHUN, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.TOTAL_RP_SETAHUN, 
				   A.FLAG_TEMP, 
				   A.AWAL_ROTASI, 
				   A.TIPE_NORMA, 
				   A.FLAG_SITE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, BULAN_PENGERJAAN, ACTIVITY_CLASS, ATRIBUT, SUMBER_BIAYA, ROTASI_SMS1, ROTASI_SMS2, TOTAL_RP_SMS1, TOTAL_RP_SMS2, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SMS1, PLAN_SMS2, PLAN_SETAHUN, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SMS1, COST_SMS2, TOTAL_RP_SETAHUN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, AWAL_ROTASI, TIPE_NORMA, FLAG_SITE
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['BULAN_PENGERJAAN']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['ATRIBUT']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['ROTASI_SMS1']."',
					    '".$row['ROTASI_SMS2']."',
					    '".$row['TOTAL_RP_SMS1']."',
					    '".$row['TOTAL_RP_SMS2']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SMS1']."', '".$row['PLAN_SMS2']."', '".$row['PLAN_SETAHUN']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_RP_SETAHUN']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['AWAL_ROTASI']."',
					    '".$row['TIPE_NORMA']."',
					    '".$row['FLAG_SITE']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTIVITY_CODE, 
				   A.COST_ELEMENT, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.BULAN_PENGERJAAN, 
				   A.ACTIVITY_CLASS, 
				   A.ATRIBUT, 
				   A.SUMBER_BIAYA, 
				   A.ROTASI_SMS1, 
				   A.ROTASI_SMS2, 
				   A.RP_ROTASI_SMS1, 
				   A.RP_ROTASI_SMS2, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SMS1, A.PLAN_SMS2, A.PLAN_SETAHUN, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.DIS_SETAHUN, 
				   A.AWAL_ROTASI, 
				   A.TIPE_NORMA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, COST_ELEMENT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, BULAN_PENGERJAAN, ACTIVITY_CLASS, ATRIBUT, SUMBER_BIAYA, ROTASI_SMS1, ROTASI_SMS2, RP_ROTASI_SMS1, RP_ROTASI_SMS2, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SMS1, PLAN_SMS2, PLAN_SETAHUN, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, DIS_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, AWAL_ROTASI, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['BULAN_PENGERJAAN']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['ATRIBUT']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['ROTASI_SMS1']."',
					    '".$row['ROTASI_SMS2']."',
					    '".$row['RP_ROTASI_SMS1']."',
					    '".$row['RP_ROTASI_SMS2']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SMS1']."', '".$row['PLAN_SMS2']."', '".$row['PLAN_SETAHUN']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['DIS_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['AWAL_ROTASI']."',
					    '".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT Perkerasan Jalan
	public function genRktPerkerasanJalan($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PK_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.SUMBER_BIAYA, 
				   A.JENIS_PEKERJAAN, 
				   A.JARAK, 
				   A.AKTUAL_JALAN, 
				   A.AKTUAL_PERKERASAN_JALAN, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, A.PRICE_QTY, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.FLAG_TEMP, A.TIPE_NORMA,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PK (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, SUMBER_BIAYA, JENIS_PEKERJAAN, JARAK, AKTUAL_JALAN, AKTUAL_PERKERASAN_JALAN, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, PRICE_QTY, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, FLAG_TEMP, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['JENIS_PEKERJAAN']."',
					    '".$row['JARAK']."',
					    '".$row['AKTUAL_JALAN']."',
					    '".$row['AKTUAL_PERKERASAN_JALAN']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."', '".$row['PRICE_QTY']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['FLAG_TEMP']."',
						'".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_PK_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.COST_ELEMENT, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.SUMBER_BIAYA, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.COST_SETAHUN, A.TIPE_NORMA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PK_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PK_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, COST_ELEMENT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, SUMBER_BIAYA, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, COST_SETAHUN, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT Panen
	public function genRktPanen($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PANEN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PANEN_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.TRX_RKT_CODE, 
				   A.TON, 
				   A.JANJANG, 
				   A.BJR_AFD, 
				   A.JARAK_PKS, 
				   A.SUMBER_BIAYA_UNIT, 
				   A.PERSEN_LANGSIR, 
				   A.BIAYA_PEMANEN_HK, 
				   A.BIAYA_PEMANEN_RP_BASIS, 
				   A.BIAYA_PEMANEN_RP_PREMI, 
				   A.BIAYA_PEMANEN_RP_TOTAL, 
				   A.BIAYA_PEMANEN_RP_KG, 
				   A.BIAYA_SPV_RP_BASIS, 
				   A.BIAYA_SPV_RP_PREMI, 
				   A.BIAYA_SPV_RP_TOTAL, 
				   A.BIAYA_SPV_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_TOTAL, 
				   A.TUKANG_MUAT_BASIS, 
				   A.TUKANG_MUAT_PREMI, 
				   A.TUKANG_MUAT_TOTAL, 
				   A.TUKANG_MUAT_RP_KG, 
				   A.SUPIR_PREMI, 
				   A.SUPIR_RP_KG, 
				   A.ANGKUT_TBS_RP_KG_KM, 
				   A.ANGKUT_TBS_RP_ANGKUT, 
				   A.ANGKUT_TBS_RP_KG, 
				   A.KRANI_BUAH_BASIS, 
				   A.KRANI_BUAH_PREMI, 
				   A.KRANI_BUAH_TOTAL, 
				   A.KRANI_BUAH_RP_KG, 
				   A.LANGSIR_TON, 
				   A.LANGSIR_RP, 
				   A.LANGSIR_RP_KG, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, A.COST_SMS1, A.COST_SMS2, 
				   A.MATURITY_STAGE_SMS1, A.MATURITY_STAGE_SMS2, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PANEN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PANEN (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, TON, JANJANG, BJR_AFD, JARAK_PKS, SUMBER_BIAYA_UNIT, PERSEN_LANGSIR, BIAYA_PEMANEN_HK, BIAYA_PEMANEN_RP_BASIS, BIAYA_PEMANEN_RP_PREMI, BIAYA_PEMANEN_RP_TOTAL, BIAYA_PEMANEN_RP_KG, BIAYA_SPV_RP_BASIS, BIAYA_SPV_RP_PREMI, BIAYA_SPV_RP_TOTAL, BIAYA_SPV_RP_KG, BIAYA_ALAT_PANEN_RP_KG, BIAYA_ALAT_PANEN_RP_TOTAL, TUKANG_MUAT_BASIS, TUKANG_MUAT_PREMI, TUKANG_MUAT_TOTAL, TUKANG_MUAT_RP_KG, SUPIR_PREMI, SUPIR_RP_KG, ANGKUT_TBS_RP_KG_KM, ANGKUT_TBS_RP_ANGKUT, ANGKUT_TBS_RP_KG, KRANI_BUAH_BASIS, KRANI_BUAH_PREMI, KRANI_BUAH_TOTAL, KRANI_BUAH_RP_KG, LANGSIR_TON, LANGSIR_RP, LANGSIR_RP_KG, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, FLAG_TEMP
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['TON']."', 
						'".$row['JANJANG']."', 
						'".$row['BJR_AFD']."',
						'".$row['JARAK_PKS']."',
					    '".$row['SUMBER_BIAYA_UNIT']."',
					    '".$row['PERSEN_LANGSIR']."',
					    '".$row['BIAYA_PEMANEN_HK']."',
					    '".$row['BIAYA_PEMANEN_RP_BASIS']."',
					    '".$row['BIAYA_PEMANEN_RP_PREMI']."',
					    '".$row['BIAYA_PEMANEN_RP_TOTAL']."',
					    '".$row['BIAYA_PEMANEN_RP_KG']."',
					    '".$row['BIAYA_SPV_RP_BASIS']."',
					    '".$row['BIAYA_SPV_RP_PREMI']."',
					    '".$row['BIAYA_SPV_RP_TOTAL']."',
					    '".$row['BIAYA_SPV_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_TOTAL']."',
					    '".$row['TUKANG_MUAT_BASIS']."',
					    '".$row['TUKANG_MUAT_PREMI']."',
					    '".$row['TUKANG_MUAT_TOTAL']."',
					    '".$row['TUKANG_MUAT_RP_KG']."',
					    '".$row['SUPIR_PREMI']."',
					    '".$row['SUPIR_RP_KG']."',
					    '".$row['ANGKUT_TBS_RP_KG_KM']."',
					    '".$row['ANGKUT_TBS_RP_ANGKUT']."',
					    '".$row['ANGKUT_TBS_RP_KG']."',
					    '".$row['KRANI_BUAH_BASIS']."',
					    '".$row['KRANI_BUAH_PREMI']."',
					    '".$row['KRANI_BUAH_TOTAL']."',
					    '".$row['KRANI_BUAH_RP_KG']."',
					    '".$row['LANGSIR_TON']."',
					    '".$row['LANGSIR_RP']."',
					    '".$row['LANGSIR_RP_KG']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
						'".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_PANEN_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.COST_ELEMENT, 
				   A.TRX_RKT_CODE, 
				   A.SUMBER_BIAYA_UNIT, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.TON, 
				   A.JANJANG, 
				   A.BJR_AFD, 
				   A.JARAK_PKS, 
				   A.PERSEN_LANGSIR, 
				   A.BIAYA_PEMANEN_HK, 
				   A.BIAYA_PEMANEN_RP_BASIS, 
				   A.BIAYA_PEMANEN_RP_PREMI, 
				   A.BIAYA_PEMANEN_RP_TOTAL, 
				   A.BIAYA_PEMANEN_RP_KG, 
				   A.BIAYA_SPV_RP_BASIS, 
				   A.BIAYA_SPV_RP_PREMI, 
				   A.BIAYA_SPV_RP_TOTAL, 
				   A.BIAYA_SPV_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_TOTAL, 
				   A.TUKANG_MUAT_BASIS, 
				   A.TUKANG_MUAT_PREMI, 
				   A.TUKANG_MUAT_TOTAL, 
				   A.TUKANG_MUAT_RP_KG, 
				   A.SUPIR_PREMI, 
				   A.SUPIR_RP_KG, 
				   A.ANGKUT_TBS_RP_KG_KM, 
				   A.ANGKUT_TBS_RP_ANGKUT, 
				   A.ANGKUT_TBS_RP_KG, 
				   A.KRANI_BUAH_BASIS, 
				   A.KRANI_BUAH_PREMI, 
				   A.KRANI_BUAH_TOTAL, 
				   A.KRANI_BUAH_RP_KG, 
				   A.LANGSIR_TON, 
				   A.LANGSIR_RP, 
				   A.LANGSIR_RP_KG, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, A.COST_SMS1, A.COST_SMS2,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PANEN_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PANEN_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, COST_ELEMENT, SUMBER_BIAYA_UNIT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, TON, JANJANG, BJR_AFD, JARAK_PKS, PERSEN_LANGSIR, BIAYA_PEMANEN_HK, BIAYA_PEMANEN_RP_BASIS, BIAYA_PEMANEN_RP_PREMI, BIAYA_PEMANEN_RP_TOTAL, BIAYA_PEMANEN_RP_KG, BIAYA_SPV_RP_BASIS, BIAYA_SPV_RP_PREMI, BIAYA_SPV_RP_TOTAL, BIAYA_SPV_RP_KG, BIAYA_ALAT_PANEN_RP_KG, BIAYA_ALAT_PANEN_RP_TOTAL, TUKANG_MUAT_BASIS, TUKANG_MUAT_PREMI, TUKANG_MUAT_TOTAL, TUKANG_MUAT_RP_KG, SUPIR_PREMI, SUPIR_RP_KG, ANGKUT_TBS_RP_KG_KM, ANGKUT_TBS_RP_ANGKUT, ANGKUT_TBS_RP_KG, KRANI_BUAH_BASIS, KRANI_BUAH_PREMI, KRANI_BUAH_TOTAL, KRANI_BUAH_RP_KG, LANGSIR_TON, LANGSIR_RP, LANGSIR_RP_KG, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, COST_SMS1, COST_SMS2, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['SUMBER_BIAYA_UNIT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['TON']."', 
						'".$row['JANJANG']."', 
						'".$row['BJR_AFD']."',
						'".$row['JARAK_PKS']."',
					    '".$row['PERSEN_LANGSIR']."',
					    '".$row['BIAYA_PEMANEN_HK']."',
					    '".$row['BIAYA_PEMANEN_RP_BASIS']."',
					    '".$row['BIAYA_PEMANEN_RP_PREMI']."',
					    '".$row['BIAYA_PEMANEN_RP_TOTAL']."',
					    '".$row['BIAYA_PEMANEN_RP_KG']."',
					    '".$row['BIAYA_SPV_RP_BASIS']."',
					    '".$row['BIAYA_SPV_RP_PREMI']."',
					    '".$row['BIAYA_SPV_RP_TOTAL']."',
					    '".$row['BIAYA_SPV_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_TOTAL']."',
					    '".$row['TUKANG_MUAT_BASIS']."',
					    '".$row['TUKANG_MUAT_PREMI']."',
					    '".$row['TUKANG_MUAT_TOTAL']."',
					    '".$row['TUKANG_MUAT_RP_KG']."',
					    '".$row['SUPIR_PREMI']."',
					    '".$row['SUPIR_RP_KG']."',
					    '".$row['ANGKUT_TBS_RP_KG_KM']."',
					    '".$row['ANGKUT_TBS_RP_ANGKUT']."',
					    '".$row['ANGKUT_TBS_RP_KG']."',
					    '".$row['KRANI_BUAH_BASIS']."',
					    '".$row['KRANI_BUAH_PREMI']."',
					    '".$row['KRANI_BUAH_TOTAL']."',
					    '".$row['KRANI_BUAH_RP_KG']."',
					    '".$row['LANGSIR_TON']."',
					    '".$row['LANGSIR_RP']."',
					    '".$row['LANGSIR_RP_KG']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT OPEX VRA & NON VRA
	public function genRktOpex($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_OPEX
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_OPEX
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_CODE, 
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.COA_CODE, 
				   A.GROUP_BUM_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTUAL, 
				   A.TAKSASI, 
				   A.ANTISIPASI, 
				   A.PERSENTASE_INFLASI, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.TOTAL_BIAYA, 
				   A.KETERANGAN, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_OPEX A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_OPEX (
						TRX_CODE, PERIOD_BUDGET, REGION_CODE, BA_CODE, COA_CODE, GROUP_BUM_CODE, TIPE_TRANSAKSI, ACTUAL, TAKSASI, ANTISIPASI, PERSENTASE_INFLASI, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, TOTAL_BIAYA, KETERANGAN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['COA_CODE']."',
					    '".$row['GROUP_BUM_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTUAL']."', 
						'".$row['TAKSASI']."', 
						'".$row['ANTISIPASI']."',
						'".$row['PERSENTASE_INFLASI']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['KETERANGAN']."',
						'".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT CSR / IR / SHE
	public function genRktRelation($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_RELATION
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_RELATION
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_CODE, 
				   A.BA_CODE, 
				   A.REPORT_TYPE, 
				   A.COA_CODE, 
				   A.GROUP_CODE, 
				   A.SUB_GROUP_CODE, 
				   A.ACTIVITY_DETAIL, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.TOTAL_BIAYA, 
				   A.KETERANGAN, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_RELATION A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_RELATION (
						TRX_CODE, PERIOD_BUDGET, BA_CODE, REPORT_TYPE, COA_CODE, GROUP_CODE, SUB_GROUP_CODE, ACTIVITY_DETAIL, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, TOTAL_BIAYA, KETERANGAN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['REPORT_TYPE']."',
					    '".$row['COA_CODE']."',
					    '".$row['GROUP_CODE']."',
					    '".$row['SUB_GROUP_CODE']."',
					    '".$row['ACTIVITY_DETAIL']."', 
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['KETERANGAN']."',
						'".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT VRA
	public function genRktVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_VRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_VRA_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_VRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_RKT_VRA_CODE, 
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.DESCRIPTION_VRA, 
				   A.JUMLAH_ALAT, 
				   A.TAHUN_ALAT, 
				   A.QTY_DAY, 
				   A.DAY_YEAR_VRA, 
				   A.QTY_YEAR, 
				   A.TOTAL_QTY_TAHUN, 
				   A.TOTAL_BIAYA, 
				   A.TOTAL_RP_QTY, 
				   A.JUMLAH_OPERATOR, A.GAJI_OPERATOR, 
				   A.JUMLAH_HELPER, A.GAJI_HELPER, 
				   A.TOTAL_GAJI_OPERATOR, A.TUNJANGAN_OPERATOR, A.TOTAL_TUNJANGAN_OPERATOR, A.TOTAL_GAJI_TUNJANGAN_OPERATOR, A.RP_QTY_OPERATOR, 
				   A.TOTAL_GAJI_HELPER, A.TUNJANGAN_HELPER, A.TOTAL_TUNJANGAN_HELPER, A.TOTAL_GAJI_TUNJANGAN_HELPER, A.RP_QTY_HELPER, 
				   A.COST_SETAHUN, 
				   A.RVRA1_CODE, A.RVRA1_VALUE1, A.RVRA1_VALUE2, A.RVRA1_VALUE3, 
				   A.RVRA2_CODE, A.RVRA2_VALUE1, A.RVRA2_VALUE2, A.RVRA2_VALUE3, 
				   A.RVRA3_CODE, A.RVRA3_VALUE1, A.RVRA3_VALUE2, A.RVRA3_VALUE3, 
				   A.RVRA4_CODE, A.RVRA4_VALUE1, A.RVRA4_VALUE2, A.RVRA4_VALUE3, 
				   A.RVRA5_CODE, A.RVRA5_VALUE1, A.RVRA5_VALUE2, A.RVRA5_VALUE3, 
				   A.RVRA6_CODE, A.RVRA6_VALUE1, A.RVRA6_VALUE2, A.RVRA6_VALUE3, 
				   A.RVRA7_CODE, A.RVRA7_VALUE1, A.RVRA7_VALUE2, A.RVRA7_VALUE3, 
				   A.RVRA8_CODE, A.RVRA8_VALUE1, A.RVRA8_VALUE2, A.RVRA8_VALUE3, 
				   A.RVRA9_CODE, A.RVRA9_VALUE1, A.RVRA9_VALUE2, A.RVRA9_VALUE3, 
				   A.RVRA10_CODE, A.RVRA10_VALUE1, A.RVRA10_VALUE2, A.RVRA10_VALUE3, 
				   A.RVRA11_CODE, A.RVRA11_VALUE1, A.RVRA11_VALUE2, A.RVRA11_VALUE3, 
				   A.RVRA12_CODE, A.RVRA12_VALUE1, A.RVRA12_VALUE2, A.RVRA12_VALUE3, 
				   A.RVRA13_CODE, A.RVRA13_VALUE1, A.RVRA13_VALUE2, A.RVRA13_VALUE3, 
				   A.RVRA14_CODE, A.RVRA14_VALUE1, A.RVRA14_VALUE2, A.RVRA14_VALUE3, 
				   A.RVRA15_CODE, A.RVRA15_VALUE1, A.RVRA15_VALUE2, A.RVRA15_VALUE3, 
				   A.RVRA16_CODE, A.RVRA16_VALUE1, A.RVRA16_VALUE2, A.RVRA16_VALUE3, 
				   A.RVRA17_CODE, A.RVRA17_VALUE1, A.RVRA17_VALUE2, A.RVRA17_VALUE3, 
				   A.RVRA18_CODE, A.RVRA18_VALUE1, A.RVRA18_VALUE2, A.RVRA18_VALUE3, 
				   A.RVRA19_CODE, A.RVRA19_VALUE1, A.RVRA19_VALUE2, A.RVRA19_VALUE3, 
				   A.RVRA20_CODE, A.RVRA20_VALUE1, A.RVRA20_VALUE2, A.RVRA20_VALUE3, 
				   A.FLAG_TEMP, A.TRIGGER_UPDATE,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA (
						TRX_RKT_VRA_CODE, PERIOD_BUDGET, BA_CODE, VRA_CODE, DESCRIPTION_VRA, JUMLAH_ALAT, TAHUN_ALAT, QTY_DAY, DAY_YEAR_VRA, QTY_YEAR, TOTAL_QTY_TAHUN, TOTAL_BIAYA, TOTAL_RP_QTY, JUMLAH_OPERATOR, GAJI_OPERATOR, JUMLAH_HELPER, GAJI_HELPER, TOTAL_GAJI_OPERATOR, TUNJANGAN_OPERATOR, TOTAL_TUNJANGAN_OPERATOR, TOTAL_GAJI_TUNJANGAN_OPERATOR, RP_QTY_OPERATOR, TOTAL_GAJI_HELPER, TUNJANGAN_HELPER, TOTAL_TUNJANGAN_HELPER, TOTAL_GAJI_TUNJANGAN_HELPER, RP_QTY_HELPER, COST_SETAHUN, RVRA1_CODE, RVRA1_VALUE1, RVRA1_VALUE2, RVRA1_VALUE3, RVRA2_CODE, RVRA2_VALUE1, RVRA2_VALUE2, RVRA2_VALUE3, RVRA3_CODE, RVRA3_VALUE1, RVRA3_VALUE2, RVRA3_VALUE3, RVRA4_CODE, RVRA4_VALUE1, RVRA4_VALUE2, RVRA4_VALUE3, RVRA5_CODE, RVRA5_VALUE1, RVRA5_VALUE2, RVRA5_VALUE3, RVRA6_CODE, RVRA6_VALUE1, RVRA6_VALUE2, RVRA6_VALUE3, RVRA7_CODE, RVRA7_VALUE1, RVRA7_VALUE2, RVRA7_VALUE3, RVRA8_CODE, RVRA8_VALUE1, RVRA8_VALUE2, RVRA8_VALUE3, RVRA9_CODE, RVRA9_VALUE1, RVRA9_VALUE2, RVRA9_VALUE3, RVRA10_CODE, RVRA10_VALUE1, RVRA10_VALUE2, RVRA10_VALUE3, RVRA11_CODE, RVRA11_VALUE1, RVRA11_VALUE2, RVRA11_VALUE3, RVRA12_CODE, RVRA12_VALUE1, RVRA12_VALUE2, RVRA12_VALUE3, RVRA13_CODE, RVRA13_VALUE1, RVRA13_VALUE2, RVRA13_VALUE3, RVRA14_CODE, RVRA14_VALUE1, RVRA14_VALUE2, RVRA14_VALUE3, RVRA15_CODE, RVRA15_VALUE1, RVRA15_VALUE2, RVRA15_VALUE3, RVRA16_CODE, RVRA16_VALUE1, RVRA16_VALUE2, RVRA16_VALUE3, RVRA17_CODE, RVRA17_VALUE1, RVRA17_VALUE2, RVRA17_VALUE3, RVRA18_CODE, RVRA18_VALUE1, RVRA18_VALUE2, RVRA18_VALUE3, RVRA19_CODE, RVRA19_VALUE1, RVRA19_VALUE2, RVRA19_VALUE3, RVRA20_CODE, RVRA20_VALUE1, RVRA20_VALUE2, RVRA20_VALUE3, FLAG_TEMP, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_VRA_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['DESCRIPTION_VRA']."',
					    '".$row['JUMLAH_ALAT']."',
					    '".$row['TAHUN_ALAT']."',
					    '".$row['QTY_DAY']."',
					    '".$row['DAY_YEAR_VRA']."',
					    '".$row['QTY_YEAR']."',
					    '".$row['TOTAL_QTY_TAHUN']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['JUMLAH_OPERATOR']."', '".$row['GAJI_OPERATOR']."', '".$row['JUMLAH_HELPER']."', 
						'".$row['GAJI_HELPER']."', '".$row['TOTAL_GAJI_OPERATOR']."',
						'".$row['TUNJANGAN_OPERATOR']."', '".$row['TOTAL_TUNJANGAN_OPERATOR']."',
						'".$row['TOTAL_GAJI_TUNJANGAN_OPERATOR']."', '".$row['RP_QTY_OPERATOR']."', 
						'".$row['TOTAL_GAJI_HELPER']."', '".$row['TUNJANGAN_HELPER']."', 
						'".$row['TOTAL_TUNJANGAN_HELPER']."', '".$row['TOTAL_GAJI_TUNJANGAN_HELPER']."', 
						'".$row['RP_QTY_HELPER']."', 
						'".$row['COST_SETAHUN']."', 
						'".$row['RVRA1_CODE']."', '".$row['RVRA1_VALUE1']."', '".$row['RVRA1_VALUE2']."', '".$row['RVRA1_VALUE3']."', 
						'".$row['RVRA2_CODE']."', '".$row['RVRA2_VALUE1']."', '".$row['RVRA2_VALUE2']."', '".$row['RVRA2_VALUE3']."', 
						'".$row['RVRA3_CODE']."', '".$row['RVRA3_VALUE1']."', '".$row['RVRA3_VALUE2']."', '".$row['RVRA3_VALUE3']."', 
						'".$row['RVRA4_CODE']."', '".$row['RVRA4_VALUE1']."', '".$row['RVRA4_VALUE2']."', '".$row['RVRA4_VALUE3']."', 
						'".$row['RVRA5_CODE']."', '".$row['RVRA5_VALUE1']."', '".$row['RVRA5_VALUE2']."', '".$row['RVRA5_VALUE3']."', 
						'".$row['RVRA6_CODE']."', '".$row['RVRA6_VALUE1']."', '".$row['RVRA6_VALUE2']."', '".$row['RVRA6_VALUE3']."', 
						'".$row['RVRA7_CODE']."', '".$row['RVRA7_VALUE1']."', '".$row['RVRA7_VALUE2']."', '".$row['RVRA7_VALUE3']."', 
						'".$row['RVRA8_CODE']."', '".$row['RVRA8_VALUE1']."', '".$row['RVRA8_VALUE2']."', '".$row['RVRA8_VALUE3']."', 
						'".$row['RVRA9_CODE']."', '".$row['RVRA9_VALUE1']."', '".$row['RVRA9_VALUE2']."', '".$row['RVRA9_VALUE3']."', 
						'".$row['RVRA10_CODE']."', '".$row['RVRA10_VALUE1']."', '".$row['RVRA10_VALUE2']."', '".$row['RVRA10_VALUE3']."', 
						'".$row['RVRA11_CODE']."', '".$row['RVRA11_VALUE1']."', '".$row['RVRA11_VALUE2']."', '".$row['RVRA11_VALUE3']."', 
						'".$row['RVRA12_CODE']."', '".$row['RVRA12_VALUE1']."', '".$row['RVRA12_VALUE2']."', '".$row['RVRA12_VALUE3']."', 
						'".$row['RVRA13_CODE']."', '".$row['RVRA13_VALUE1']."', '".$row['RVRA13_VALUE2']."', '".$row['RVRA13_VALUE3']."', 
						'".$row['RVRA14_CODE']."', '".$row['RVRA14_VALUE1']."', '".$row['RVRA14_VALUE2']."', '".$row['RVRA14_VALUE3']."', 
						'".$row['RVRA15_CODE']."', '".$row['RVRA15_VALUE1']."', '".$row['RVRA15_VALUE2']."', '".$row['RVRA15_VALUE3']."', 
						'".$row['RVRA16_CODE']."', '".$row['RVRA16_VALUE1']."', '".$row['RVRA16_VALUE2']."', '".$row['RVRA16_VALUE3']."', 
						'".$row['RVRA17_CODE']."', '".$row['RVRA17_VALUE1']."', '".$row['RVRA17_VALUE2']."', '".$row['RVRA17_VALUE3']."', 
						'".$row['RVRA18_CODE']."', '".$row['RVRA18_VALUE1']."', '".$row['RVRA18_VALUE2']."', '".$row['RVRA18_VALUE3']."', 
						'".$row['RVRA19_CODE']."', '".$row['RVRA19_VALUE1']."', '".$row['RVRA19_VALUE2']."', '".$row['RVRA19_VALUE3']."', 
						'".$row['RVRA20_CODE']."', '".$row['RVRA20_VALUE1']."', '".$row['RVRA20_VALUE2']."', '".$row['RVRA20_VALUE3']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_VRA_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.VALUE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_SUM (
						PERIOD_BUDGET, BA_CODE, VRA_CODE, VALUE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT Distribusi VRA
	public function genRktDistribusiVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_VRA_DISTRIBUSI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_VRA_DISTRIBUSI_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_VRA_DISTRIBUSI
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.VRA_CODE, 
				   A.LOCATION_CODE, 
				   A.HM_KM, 
				   A.PRICE_QTY_VRA, 
				   A.PRICE_HM_KM, 
				   A.TRX_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_DISTRIBUSI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, LOCATION_CODE, HM_KM, PRICE_QTY_VRA, PRICE_HM_KM, TRX_CODE, TIPE_TRANSAKSI, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['LOCATION_CODE']."',
					    '".$row['HM_KM']."',
					    '".$row['PRICE_QTY_VRA']."',
					    '".$row['PRICE_HM_KM']."',
					    '".$row['TRX_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_VRA_DISTRIBUSI_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.VRA_CODE, 
				   A.TOTAL_HM_KM, 
				   A.TOTAL_PRICE_HM_KM, 
				   A.TIPE_TRANSAKSI,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_DISTRIBUSI_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI_SUM (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, TOTAL_HM_KM, TOTAL_PRICE_HM_KM, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['TOTAL_HM_KM']."',
					    '".$row['TOTAL_PRICE_HM_KM']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate norma panen OER BJR
	public function genNormaPanenOer($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_OER_BJR
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_PANEN_OER_BJR
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.BJR_MIN, 
				   A.BJR_MAX, 
				   A.OER_MIN, 
				   A.OER_MAX, 
				   A.PREMI_PANEN, 
				   A.BJR_BUDGET, 
				   A.JANJANG_BASIS_MANDOR, 
				   A.JANJANG_OPERATION, 
				   A.OVER_BASIS_JANJANG, 
				   A.OER_BA, 
				   A.NILAI, 
				   A.VAR1, 
				   A.VAR2,
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PANEN_OER_BJR A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_OER_BJR (
						PERIOD_BUDGET, BA_CODE, BJR_MIN, BJR_MAX, OER_MIN, OER_MAX, PREMI_PANEN, BJR_BUDGET, JANJANG_BASIS_MANDOR, JANJANG_OPERATION, OVER_BASIS_JANJANG, OER_BA, NILAI, VAR1, VAR2, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['BJR_MIN']."',
					    '".$row['BJR_MAX']."',
					    '".$row['OER_MIN']."',
					    '".$row['OER_MAX']."',
					    '".$row['PREMI_PANEN']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['JANJANG_BASIS_MANDOR']."',
					    '".$row['JANJANG_OPERATION']."',
					    '".$row['OVER_BASIS_JANJANG']."',
					    '".$row['OER_BA']."',
					    '".$row['NILAI']."',
					    '".$row['VAR1']."',
					    '".$row['VAR2']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate norma panen supervisi
	public function genNormaPanenSupervisi($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_SUPERVISI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_PANEN_SUPERVISI
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.MIN_BJR, 
				   A.MAX_BJR, 
				   A.JANJANG_BASIS, 
				   A.BJR_BUDGET, 
				   A.OVER_BASIS_JANJANG, 
				   A.JANJANG_OPERATION, 
				   A.RP_KG, 
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PANEN_SUPERVISI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_SUPERVISI (
						PERIOD_BUDGET, BA_CODE, MIN_BJR, MAX_BJR, JANJANG_BASIS, BJR_BUDGET, OVER_BASIS_JANJANG, JANJANG_OPERATION, RP_KG, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['MIN_BJR']."',
					    '".$row['MAX_BJR']."',
					    '".$row['JANJANG_BASIS']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['OVER_BASIS_JANJANG']."',
					    '".$row['JANJANG_OPERATION']."',
					    '".$row['RP_KG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate master OER BA
	public function genMasterOerBa($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_OER_BA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TM_OER_BA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.OER,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_OER_BA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_OER_BA (
						PERIOD_BUDGET, BA_CODE, OER, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['OER']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
	
	//generate RKT Pupuk
	public function genRktPupuk($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PUPUK_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PUPUK_DISTRIBUSI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PUPUK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_PUPUK
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.TIPE_TRANSAKSI, 
				   A.JAN, A.FEB, A.MAR, 
				   A.APR, A.MAY, A.JUN, 
				   A.JUL, A.AUG, A.SEP, 
				   A.OCT, A.NOV, A.DEC, 
				   A.SETAHUN, A.COST_SMS1, A.COST_SMS2,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, TIPE_TRANSAKSI, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['TRX_RKT_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
						'".$row['TIPE_TRANSAKSI']."',
					    '".$row['JAN']."', '".$row['FEB']."', '".$row['MAR']."',
						'".$row['APR']."', '".$row['MAY']."', '".$row['JUN']."', 
						'".$row['JUL']."', '".$row['AUG']."', '".$row['SEP']."', 
						'".$row['OCT']."', '".$row['NOV']."', '".$row['DEC']."',
					    '".$row['SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_PUPUK_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.COST_ELEMENT, 
				   A.TIPE_TRANSAKSI, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.COST_TRANSPORT_KG, 
				   A.COST_TOOLS_KG, 
				   A.COST_LABOUR_POKOK, 
				   A.DIS_COST_JAN, A.DIS_COST_FEB, A.DIS_COST_MAR, 
				   A.DIS_COST_APR, A.DIS_COST_MAY, A.DIS_COST_JUN, 
				   A.DIS_COST_JUL, A.DIS_COST_AUG, A.DIS_COST_SEP, 
				   A.DIS_COST_OCT, A.DIS_COST_NOV, A.DIS_COST_DEC, 
				   A.DIS_COST_YEAR, A.COST_SMS1, A.COST_SMS2,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK_COST_ELEMENT (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, COST_TRANSPORT_KG, COST_TOOLS_KG, COST_LABOUR_POKOK, DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC, DIS_COST_YEAR, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['COST_ELEMENT']."', 
						'".$row['TIPE_TRANSAKSI']."', 
						'".$row['TRX_RKT_CODE']."', 
						'".$row['MATURITY_STAGE_SMS1']."', 
						'".$row['MATURITY_STAGE_SMS2']."', 
						'".$row['COST_TRANSPORT_KG']."', 
						'".$row['COST_TOOLS_KG']."', 
						'".$row['COST_LABOUR_POKOK']."', 
						'".$row['DIS_COST_JAN']."', '".$row['DIS_COST_FEB']."', '".$row['DIS_COST_MAR']."', 
						'".$row['DIS_COST_APR']."', '".$row['DIS_COST_MAY']."', '".$row['DIS_COST_JUN']."', 
						'".$row['DIS_COST_JUL']."', '".$row['DIS_COST_AUG']."', '".$row['DIS_COST_SEP']."', 
						'".$row['DIS_COST_OCT']."', '".$row['DIS_COST_NOV']."', '".$row['DIS_COST_DEC']."', 
						'".$row['DIS_COST_YEAR']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_PUPUK_DISTRIBUSI
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.DIS_JAN, A.MATERIAL_CODE_JAN, 
				   A.DIS_FEB, A.MATERIAL_CODE_FEB, 
				   A.DIS_MAR, A.MATERIAL_CODE_MAR, 
				   A.DIS_APR, A.MATERIAL_CODE_APR, 
				   A.DIS_MAY, A.MATERIAL_CODE_MAY, 
				   A.DIS_JUN, A.MATERIAL_CODE_JUN, 
				   A.DIS_JUL, A.MATERIAL_CODE_JUL, 
				   A.DIS_AUG, A.MATERIAL_CODE_AUG, 
				   A.DIS_SEP, A.MATERIAL_CODE_SEP, 
				   A.DIS_OCT, A.MATERIAL_CODE_OCT, 
				   A.DIS_NOV, A.MATERIAL_CODE_NOV, 
				   A.DIS_DEC, A.MATERIAL_CODE_DEC, 
				   A.DIS_TOTAL, A.MATERIAL_TOTAL, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK_DISTRIBUSI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK_DISTRIBUSI (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, DIS_JAN, MATERIAL_CODE_JAN, DIS_FEB, MATERIAL_CODE_FEB, DIS_MAR, MATERIAL_CODE_MAR, DIS_APR, MATERIAL_CODE_APR, DIS_MAY, MATERIAL_CODE_MAY, DIS_JUN, MATERIAL_CODE_JUN, DIS_JUL, MATERIAL_CODE_JUL, DIS_AUG, MATERIAL_CODE_AUG, DIS_SEP, MATERIAL_CODE_SEP, DIS_OCT, MATERIAL_CODE_OCT, DIS_NOV, MATERIAL_CODE_NOV, DIS_DEC, MATERIAL_CODE_DEC, DIS_TOTAL, MATERIAL_TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['TIPE_TRANSAKSI']."', 
						'".$row['TRX_RKT_CODE']."', 
						'".$row['MATURITY_STAGE_SMS1']."', 
						'".$row['MATURITY_STAGE_SMS2']."', 
						'".$row['DIS_JAN']."', '".$row['MATERIAL_CODE_JAN']."', 
						'".$row['DIS_FEB']."', '".$row['MATERIAL_CODE_FEB']."', 
						'".$row['DIS_MAR']."', '".$row['MATERIAL_CODE_MAR']."', 
						'".$row['DIS_APR']."', '".$row['MATERIAL_CODE_APR']."', 
						'".$row['DIS_MAY']."', '".$row['MATERIAL_CODE_MAY']."', 
						'".$row['DIS_JUN']."', '".$row['MATERIAL_CODE_JUN']."', 
						'".$row['DIS_JUL']."', '".$row['MATERIAL_CODE_JUL']."', 
						'".$row['DIS_AUG']."', '".$row['MATERIAL_CODE_AUG']."', 
						'".$row['DIS_SEP']."', '".$row['MATERIAL_CODE_SEP']."', 
						'".$row['DIS_OCT']."', '".$row['MATERIAL_CODE_OCT']."', 
						'".$row['DIS_NOV']."', '".$row['MATERIAL_CODE_NOV']."', 
						'".$row['DIS_DEC']."', '".$row['MATERIAL_CODE_DEC']."', 
						'".$row['DIS_TOTAL']."', '".$row['MATERIAL_TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		$this->_global->createSqlFile($params['filename'], "EXIT"); //exit
        return true;
    }
}

