<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma Infrastruktur
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: SID 11/06/2013	: ambil data dari DB
						- getList					: SID 11/06/2013	: menampilkan list norma Infrastruktur
						- save						: SID 11/06/2013	: simpan data
						- saveTemp					: SID 11/06/2013	: hapus data di norma infrastruktur, insert inputan user di norma infrastruktur
						- delete					: SID 11/06/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/06/2013
Update Terakhir		:	01/07/2014
Revisi				:	
	SID 01/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save, saveTemp, delete
						- perubahan query pengambilan data di getData & getDataDownload
=========================================================================================================================
*/
class Application_Model_NormaInfrastruktur
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
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (TNI.ROWID) ROW_ID,
				   ROWNUM,
				   TO_CHAR (TNI.PERIOD_BUDGET, 'RRRR') AS PERIOD_BUDGET,
				   TNI.BA_CODE,
				   TMA.ACTIVITY_CODE,
				   TMA.DESCRIPTION AS ACTIVITY_DESC,
				   TNI.LAND_TYPE,
				   TNI.ACTIVITY_CLASS,
				   TNI.COST_ELEMENT,
				   TNI.TOPOGRAPHY,
				   TMA.UOM AS UOM_ACTIVITY,
				   TNI.SUB_COST_ELEMENT,
				   CASE
					  WHEN TNI.COST_ELEMENT = 'MATERIAL' OR TNI.COST_ELEMENT = 'TOOLS'
					  THEN
						 (SELECT TMM.MATERIAL_NAME
							FROM TM_MATERIAL TMM
						   WHERE     TMM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
								 AND TMM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
								 AND TMM.BA_CODE = TNI.BA_CODE)
					  WHEN TNI.COST_ELEMENT = 'LABOUR'
					  THEN
						 (SELECT JOBTYPE.JOB_DESCRIPTION
							FROM TM_JOB_TYPE JOBTYPE
						   WHERE JOBTYPE.JOB_CODE = TNI.SUB_COST_ELEMENT)
					  ELSE
						 (SELECT TMV.TYPE
							FROM TM_VRA TMV
						   WHERE TNI.SUB_COST_ELEMENT = TMV.VRA_CODE)
				   END
					  AS SUB_COST_ELEMENT_DESC,
				   CASE
					  WHEN TNI.COST_ELEMENT = 'MATERIAL' OR TNI.COST_ELEMENT = 'TOOLS'
                      THEN
                         (SELECT MATERIAL.UOM
                            FROM TM_MATERIAL MATERIAL
                           WHERE     MATERIAL.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
                                 AND MATERIAL.PERIOD_BUDGET = TNI.PERIOD_BUDGET
                                 AND MATERIAL.BA_CODE = TNI.BA_CODE)
                     WHEN TNI.COST_ELEMENT = 'TRANSPORT'
                      THEN
                         (SELECT VRA.UOM
                            FROM TM_VRA VRA
                           WHERE     VRA.VRA_CODE = TNI.SUB_COST_ELEMENT)
                      ELSE
                         'HK'
				   END
					  AS UOM,
				   TNI.QTY_INFRA,
				   TNI.QTY_ALAT AS QTY,
				   TNI.ROTASI,
				   TNI.VOLUME,
				   TNI.QTY_HA AS QTY_HA,
				   NVL (
					  (SELECT PRICE
						 FROM TN_HARGA_BORONG TNHB
						WHERE     TNHB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
							  AND TNHB.BA_CODE = TNI.BA_CODE
							  AND TNHB.ACTIVITY_CODE = TNI.ACTIVITY_CODE
							  AND TNHB.ACTIVITY_CLASS = TNI.ACTIVITY_CLASS),
					  0) AS PRICE,
				   TNI.RP_HA_EXTERNAL,
				   CASE
					  WHEN TNI.COST_ELEMENT = 'MATERIAL' OR TNI.COST_ELEMENT = 'TOOLS'
					  THEN
						 (SELECT NORMA_HARGA.PRICE
							FROM TN_HARGA_BARANG NORMA_HARGA
						   WHERE     NORMA_HARGA.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
								 AND NORMA_HARGA.PERIOD_BUDGET = TNI.PERIOD_BUDGET
								 AND NORMA_HARGA.BA_CODE = TNI.BA_CODE)
					  WHEN TNI.COST_ELEMENT = 'TRANSPORT'
					  THEN
						(( SELECT RKTVRAS.VALUE
							FROM TR_RKT_VRA_SUM RKTVRAS
						   WHERE     RKTVRAS.PERIOD_BUDGET = TNI.PERIOD_BUDGET
								 AND RKTVRAS.BA_CODE = TNI.BA_CODE
								 AND RKTVRAS.VRA_CODE = TNI.SUB_COST_ELEMENT)
						UNION
						( SELECT NVRAPINJAM.RP_QTY AS VALUE
							FROM TN_VRA_PINJAM NVRAPINJAM
						   WHERE     NVRAPINJAM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
								 AND NVRAPINJAM.REGION_CODE = TNI.REGION_CODE
								 AND NVRAPINJAM.VRA_CODE = TNI.SUB_COST_ELEMENT))
					  ELSE
						 (SELECT CHECKROLL.RP_HK
							FROM TR_RKT_CHECKROLL_SUM CHECKROLL
						   WHERE     CHECKROLL.PERIOD_BUDGET = TNI.PERIOD_BUDGET
								 AND CHECKROLL.BA_CODE = TNI.BA_CODE
								 AND CHECKROLL.JOB_CODE = TNI.SUB_COST_ELEMENT)
				   END
					  AS HARGA_INTERNAL,
				   TNI.RP_QTY_INTERNAL,
				   TNI.RP_HA_INTERNAL,
				   TNI.FLAG_TEMP
			  FROM TN_INFRASTRUKTUR TNI
			  LEFT JOIN TM_ACTIVITY TMA
				  ON TNI.ACTIVITY_CODE = TMA.ACTIVITY_CODE
			  LEFT JOIN TM_ORGANIZATION B
				  ON TNI.BA_CODE = B.BA_CODE
			 WHERE TNI.DELETE_USER IS NULL
		  ";
		  
				
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(TNI.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(TNI.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(TNI.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(TNI.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(TNI.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(TNI.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		//jika diupdate dari RKT VRA, filter berdasarkan kode VRA
		if ($params['VRA_CODE'] != '') {
			$query .= "
                AND UPPER(TNI.SUB_COST_ELEMENT) IN ('".$params['VRA_CODE']."')
				AND TNI.COST_ELEMENT = 'TRANSPORT'
            ";
        }
		
		//jika diupdate dari norma checkroll, filter berdasarkan JOB_CODE
		if ($params['JOB_CODE'] != '') {
			$query .= "
                AND UPPER(TNI.SUB_COST_ELEMENT) IN ('".$params['JOB_CODE']."')
				AND TNI.COST_ELEMENT = 'LABOUR'
            ";
        }
		
		if ($params['sub_cost_element'] != '') {
			$query .= "
                AND UPPER(TNI.SUB_COST_ELEMENT) IN ('".$params['sub_cost_element']."')
            ";
        }
		
		if ($params['activity_code'] != '') {
			$query .= "AND TNI.ACTIVITY_CODE IN ('".$params['activity_code']."')";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
				
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(TNI.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TMA.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.ACTIVITY_CLASS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.COST_ELEMENT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.SUB_COST_ELEMENT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.QTY_INFRA) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.VOLUME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNI.QTY_HA) LIKE UPPER('%".$params['search']."%')					
				)
            ";
        }
		
		$query .= "
			ORDER BY TNI.PERIOD_BUDGET, TNI.BA_CODE, TNI.ACTIVITY_CODE, TNI.ACTIVITY_CLASS, 
					 TNI.LAND_TYPE, TNI.TOPOGRAPHY, TNI.COST_ELEMENT, TNI.SUB_COST_ELEMENT
		";		  
		
		return $query;

	}
	
	//menampilkan list norma Infrastruktur
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
	
	//hapus data di norma infrastruktur, insert inputan user di norma infrastruktur
	public function saveTemp($row = array())
    { 
		$sql = "
			UPDATE TN_INFRASTRUKTUR
			SET QTY_ALAT = REPLACE('{$row['QTY']}',',',''),
				ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				VOLUME = REPLACE('{$row['VOLUME']}',',',''),
				RP_QTY_EXTERNAL = REPLACE('{$row['PRICE']}',',',''),
				HARGA_INTERNAL = REPLACE('{$row['HARGA_INTERNAL']}',',',''),
				QTY_HA = NULL,
				RP_HA_EXTERNAL = NULL,
				RP_QTY_INTERNAL = NULL,
				RP_HA_INTERNAL = NULL,
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				DELETE_TIME = NULL,
				DELETE_USER = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $qty_ha = $this->_formula->cal_NormaInfrastruktur_QtyHaEks($row);
		$row['QTY_HA'] = $qty_ha;
		$rp_ha_rotasi = $this->_formula->cal_NormaInfrastruktur_RpHaEks($row);
		$rp_qt_int = $this->_formula->cal_NormaInfrastruktur_RpQtyint($row);
		$rp_ha_int = $this->_formula->cal_NormaInfrastruktur_RpHaInt($row);

		$sql = "
			UPDATE TN_INFRASTRUKTUR
			SET QTY_HA = REPLACE('{$qty_ha}',',',''),
				RP_HA_EXTERNAL = REPLACE('{$rp_ha_rotasi}',',',''),
				RP_QTY_INTERNAL = REPLACE('{$rp_qt_int}',',',''),
				RP_HA_INTERNAL = REPLACE('{$rp_ha_int}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//hapus data
	public function delete($row = array())
    {
		$sql = "
			UPDATE TN_INFRASTRUKTUR
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//kalkulasi data saat upload
	public function calculateData($row = array())
    { 
        $qty_ha = $this->_formula->cal_NormaInfrastruktur_QtyHaEks($row);
		$row['QTY_HA'] = $qty_ha;
		$rp_ha_rotasi = $this->_formula->cal_NormaInfrastruktur_RpHaEks($row);
		$rp_qt_int = $this->_formula->cal_NormaInfrastruktur_RpQtyint($row);
		$rp_ha_int = $this->_formula->cal_NormaInfrastruktur_RpHaInt($row);

		$sql = "
			UPDATE TN_INFRASTRUKTUR
			SET QTY_HA = REPLACE('{$qty_ha}',',',''),
				RP_HA_EXTERNAL = REPLACE('{$rp_ha_rotasi}',',',''),
				RP_QTY_INTERNAL = REPLACE('{$rp_qt_int}',',',''),
				RP_HA_INTERNAL = REPLACE('{$rp_ha_int}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
		";
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
}

