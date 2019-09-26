<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma Biaya
Function 			:	- getInput					: YIR 23/06/2014	: setting input untuk region dan maturity stage
						- getData					: SID 11/06/2013	: ambil data dari DB
						- getList					: SID 11/06/2013	: menampilkan list norma biaya
						- getDeletedRec				: SID 11/06/2013	: cek deleted data
						- save						: SID 11/06/2013	: simpan data
						- saveTemp					: SID 23/06/2014	: simpan data sementara sesuai input user
						- delete					: SID 11/06/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/06/2013
Update Terakhir		:	23/06/2014
Revisi				:	
	SID 23/06/2014	: 	- penambahan filter di getData
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save & delete
						- penambahan fungsi saveTemp
						- calculateAllAction dihilangkan
=========================================================================================================================
*/
class Application_Model_NormaBiaya
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
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : YIR - 08/08/2014
		
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
			SELECT 	ROWIDTOCHAR (norma_biaya.ROWID) row_id,
					ROWNUM,
					TO_CHAR (norma_biaya.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
					norma_biaya.BA_CODE,
					norma_biaya.ACTIVITY_GROUP,
					norma_biaya.ACTIVITY_CODE,
					activity.DESCRIPTION AS ACTIVITY_DESC,
					norma_biaya.ACTIVITY_CLASS,
					norma_biaya.PALM_AGE,
					norma_biaya.LAND_TYPE,
					norma_biaya.TOPOGRAPHY,
					norma_biaya.COST_ELEMENT,
					norma_biaya.SUB_COST_ELEMENT,
					CASE
						WHEN norma_biaya.COST_ELEMENT = 'LABOUR'
						THEN
							(
								SELECT jobtype.JOB_DESCRIPTION
								FROM TM_JOB_TYPE jobtype
								WHERE jobtype.JOB_CODE = norma_biaya.SUB_COST_ELEMENT
							)
						WHEN norma_biaya.COST_ELEMENT = 'CONTRACT'
						THEN
							(
								SELECT ACT.DESCRIPTION
								FROM TM_ACTIVITY ACT
								WHERE ACT.ACTIVITY_CODE = norma_biaya.SUB_COST_ELEMENT
							)
						ELSE
							(
								SELECT material1.MATERIAL_NAME
								FROM TM_MATERIAL material1
								WHERE material1.MATERIAL_CODE = norma_biaya.SUB_COST_ELEMENT
									AND material1.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND material1.BA_CODE = norma_biaya.BA_CODE
							)
					END AS SUB_COST_ELEMENT_DESC,
					CASE
						WHEN norma_biaya.COST_ELEMENT = 'LABOUR'
						THEN 'HK'
						WHEN norma_biaya.COST_ELEMENT = 'CONTRACT'
						THEN
							(
								SELECT ACT.UOM
								FROM TM_ACTIVITY ACT
								WHERE ACT.ACTIVITY_CODE = norma_biaya.SUB_COST_ELEMENT
							)
						ELSE
							(
								SELECT material.UOM
								FROM TM_MATERIAL material
								WHERE material.MATERIAL_CODE = norma_biaya.SUB_COST_ELEMENT
									AND material.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND material.BA_CODE = norma_biaya.BA_CODE
							)
					END AS UOM,
					norma_biaya.QTY,
					norma_biaya.ROTASI,
					norma_biaya.VOLUME,
					norma_biaya.QTY_HA,
					CASE
						WHEN norma_biaya.COST_ELEMENT = 'LABOUR'
						THEN
							(
								SELECT checkroll.RP_HK
								FROM TR_RKT_CHECKROLL_SUM checkroll
								WHERE checkroll.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND checkroll.BA_CODE = norma_biaya.BA_CODE
									AND checkroll.JOB_CODE = norma_biaya.SUB_COST_ELEMENT
							)
						WHEN norma_biaya.COST_ELEMENT = 'CONTRACT'	
						THEN
							(
								SELECT borong.PRICE
								FROM TN_HARGA_BORONG borong
								WHERE borong.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND borong.BA_CODE = norma_biaya.BA_CODE
									AND borong.ACTIVITY_CODE = norma_biaya.SUB_COST_ELEMENT
									AND borong.ACTIVITY_CLASS = norma_biaya.ACTIVITY_CLASS
							)
						ELSE
							(
								SELECT norma_harga.PRICE
								FROM TN_HARGA_BARANG norma_harga
								WHERE norma_harga.MATERIAL_CODE = norma_biaya.SUB_COST_ELEMENT
									AND norma_harga.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND norma_harga.BA_CODE = norma_biaya.BA_CODE
							)
					END AS PRICE,
					norma_biaya.PRICE_HA,
					norma_biaya.PRICE_ROTASI,
					norma_biaya.QTY_SITE,
					norma_biaya.ROTASI_SITE,
					norma_biaya.VOLUME_SITE,
					norma_biaya.QTY_HA_SITE,
					CASE
						WHEN norma_biaya.COST_ELEMENT = 'LABOUR'
						THEN
							(
								SELECT checkroll.RP_HK
								FROM TR_RKT_CHECKROLL_SUM checkroll
								WHERE checkroll.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND checkroll.BA_CODE = norma_biaya.BA_CODE
									AND checkroll.JOB_CODE = norma_biaya.SUB_COST_ELEMENT
							)
						WHEN norma_biaya.COST_ELEMENT = 'CONTRACT'	
						THEN
							(
								SELECT borong.PRICE_SITE
								FROM TN_HARGA_BORONG borong
								WHERE borong.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND borong.BA_CODE = norma_biaya.BA_CODE
									AND borong.ACTIVITY_CODE = norma_biaya.SUB_COST_ELEMENT
									AND borong.ACTIVITY_CLASS = norma_biaya.ACTIVITY_CLASS
							)
						ELSE
							(
								SELECT norma_harga.PRICE
								FROM TN_HARGA_BARANG norma_harga
								WHERE norma_harga.MATERIAL_CODE = norma_biaya.SUB_COST_ELEMENT
									AND norma_harga.PERIOD_BUDGET = norma_biaya.PERIOD_BUDGET
									AND norma_harga.BA_CODE = norma_biaya.BA_CODE
							)
					END AS PRICE_SITE,
					norma_biaya.PRICE_HA_SITE,
					norma_biaya.PRICE_ROTASI_SITE,
					norma_biaya.FLAG_SITE,
					norma_biaya.FLAG_TEMP
			FROM TN_BIAYA norma_biaya 
			LEFT JOIN TM_ACTIVITY activity
				ON norma_biaya.ACTIVITY_CODE = activity.ACTIVITY_CODE
			LEFT JOIN TM_ORGANIZATION B
				ON NORMA_BIAYA.BA_CODE = B.BA_CODE
			WHERE norma_biaya.DELETE_USER IS NULL
        ";
		
		/*if ($this->_userName == 'LEFRAND.HOSANG') {
			$query .= "AND  norma_biaya.ACTIVITY_CODE ='41500' AND norma_biaya.COST_ELEMENT IN ('LABOUR') --AND norma_biaya.TOPOGRAPHY IN ('DATAR','BUKIT')  AND norma_biaya.ACTIVITY_GROUP  IN ( 'TBM0') ";
		}elseif ($this->_userName == 'YUSTINUS.WINTOLO') {
			$query .= "AND norma_biaya.ACTIVITY_CODE ='43500' 
     AND norma_biaya.COST_ELEMENT = 'LABOUR' 
    -- AND norma_biaya.TOPOGRAPHY ='DATAR'  
     AND norma_biaya.ACTIVITY_GROUP = 'TM' -- IN ( 'TBM1','TBM2') --,'TBM3', 'TNM')
     AND TO_CHAR(PERIOD_BUDGET, 'rrrr') = '2015'";
		}
		*/
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND  UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma_biaya.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma_biaya.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma_biaya.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma_biaya.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(norma_biaya.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma_biaya.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		//untuk data yang dihapus, cari data berdasarkan rowid
		if ($params['ROW_ID'] != '') {
			$query .= "
                AND ROWIDTOCHAR(norma_biaya.ROWID) = '{$params['ROW_ID']}'
            ";
        }
		
		//filter sub cost element
		if ($params['sub_cost_element'] != '') {
			$query .= "
                AND UPPER(norma_biaya.SUB_COST_ELEMENT) LIKE UPPER('%".$params['sub_cost_element']."%')
            ";
        }
		
		//jika diupdate dari norma checkroll, filter berdasarkan JOB_CODE
		if ($params['JOB_CODE'] != '') {
			$query .= "
                AND UPPER(norma_biaya.SUB_COST_ELEMENT) IN ('".$params['JOB_CODE']."')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma_biaya.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.ACTIVITY_GROUP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(activity.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.ACTIVITY_CLASS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.COST_ELEMENT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.SUB_COST_ELEMENT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.QTY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.VOLUME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.QTY_HA) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.PRICE_HA) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_biaya.PRICE_ROTASI) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY norma_biaya.PERIOD_BUDGET,norma_biaya.BA_CODE,norma_biaya.ACTIVITY_GROUP,norma_biaya.ACTIVITY_CODE,norma_biaya.ACTIVITY_CLASS, 
					 norma_biaya.LAND_TYPE,norma_biaya.TOPOGRAPHY,norma_biaya.COST_ELEMENT,norma_biaya.SUB_COST_ELEMENT
		";
		return $query;
	}
	
	//menampilkan list norma biaya
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
			FROM TN_BIAYA 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND ACTIVITY_GROUP = '{$params['ACTIVITY_GROUP']}'
				AND ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND ACTIVITY_CLASS  = '{$params['ACTIVITY_CLASS']}'
				AND LAND_TYPE = '{$params['LAND_TYPE']}'
				AND TOPOGRAPHY  = '{$params['TOPOGRAPHY']}'
				AND COST_ELEMENT = '{$params['COST_ELEMENT']}'
				AND SUB_COST_ELEMENT  = '{$params['SUB_COST_ELEMENT']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		//print_r($row);die();
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$qty_ha = $this->_formula->cal_NormaBiaya_QtyHa($row);
		$rp_ha = $this->_formula->cal_NormaBiaya_RpHa($row);
		$rp_ha_rotasi = $this->_formula->cal_NormaBiaya_RpHaRotasi($row);
		
		$qty_ha_site = $this->_formula->cal_NormaBiaya_QtyHaSite($row);
		$rp_ha_site = $this->_formula->cal_NormaBiaya_RpHaSite($row);
		$rp_ha_rotasi_site = $this->_formula->cal_NormaBiaya_RpHaRotasiSite($row);
		

		$sql = "
			UPDATE TN_BIAYA
			SET QTY = REPLACE('{$row['QTY']}',',',''),
				ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				VOLUME = REPLACE('{$row['VOLUME']}',',',''),
				QTY_HA = REPLACE('{$qty_ha}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				PRICE_HA = REPLACE('{$rp_ha}',',',''),
				PRICE_ROTASI = REPLACE('{$rp_ha_rotasi}',',',''),
				QTY_SITE = REPLACE('{$row['QTY_SITE']}',',',''),
				ROTASI_SITE = REPLACE('{$row['ROTASI_SITE']}',',',''), 
				VOLUME_SITE = REPLACE('{$row['VOLUME_SITE']}',',',''),
				QTY_HA_SITE = REPLACE('{$qty_ha_site}',',',''),
				PRICE_SITE = REPLACE('{$row['PRICE_SITE']}',',',''),
				PRICE_HA_SITE = REPLACE('{$rp_ha_site}',',',''),
				PRICE_ROTASI_SITE = REPLACE('{$rp_ha_rotasi_site}',',',''),
				--FLAG_SITE = '{$row['FLAG_SITE']}',
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				DELETE_TIME = NULL,
				DELETE_USER = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//simpan data sementara sesuai input user
	public function saveTemp($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);		

		$sql = "
			UPDATE TN_BIAYA
			SET QTY = REPLACE('{$row['QTY']}',',',''),
				ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				VOLUME = REPLACE('{$row['VOLUME']}',',',''),
				QTY_HA = NULL,
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				PRICE_HA = NULL,
				PRICE_ROTASI = NULL,
				QTY_SITE = REPLACE('{$row['QTY_SITE']}',',',''),
				ROTASI_SITE = REPLACE('{$row['ROTASI_SITE']}',',',''), 
				VOLUME_SITE = REPLACE('{$row['VOLUME_SITE']}',',',''),
				QTY_HA_SITE = NULL,
				PRICE_SITE = REPLACE('{$row['PRICE_SITE']}',',',''),
				PRICE_HA_SITE = NULL,
				PRICE_ROTASI_SITE = NULL,
				--FLAG_SITE = '{$row['FLAG_SITE']}',
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
	
	//hapus data
	public function delete($row = array())
    {
		$sql = "
			UPDATE TN_BIAYA
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";

        $this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//kalkulasi seluruh data $this->_period
	public function calculateAllItem($ba_code)
    {
		$result = true;
		try {
				$par = array(
                    'V_PERIOD_BUDGET'   => $this->_period,
                    'V_BA_CODE'  		=> $ba_code,
					'V_USER'  			=> $this->_userName
                ); 
                $sql = "
                    BEGIN
						PKG_BUDGET.CALC_ALL_AVG_NORMA_BIAYA(to_date(:V_PERIOD_BUDGET,'dd-mm-rrrr'), :V_BA_CODE, :V_USER );
                    END;
                ";
			
				$statement = new Zend_Db_Statement_Oracle($this->_db, $sql);
                $statement->execute($par);
			
			//log DB
			$this->_global->insertLog('UPDATE SUCCESS', 'AVG NORMA BIAYA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('UPDATE FAILED', 'AVG NORMA BIAYA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

