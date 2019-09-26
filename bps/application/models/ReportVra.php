<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Report VRA
Function 			:	- getList					: menampilkan list Report VRA
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	02/07/2013
Update Terakhir		:	02/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportVra
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
            SELECT ROWIDTOCHAR(report.ROWID) row_id, rownum, 
				   to_char(report.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   report.BA_CODE, 
				   master_vra.VRA_SUB_CAT_DESCRIPTION,
				   master_vra.VRA_CODE,
				   master_vra.TYPE VRA_TYPE,
				   report.DESCRIPTION_VRA DESCRIPTION_VRA_TYPE,
				   report.JUMLAH_ALAT,
				   report.TAHUN_ALAT,
				   master_vra.UOM,
				   report.QTY_DAY, 
				   report.DAY_YEAR_VRA, 
				   report.QTY_YEAR, 
				   report.TOTAL_BIAYA,
				   report.TOTAL_RP_QTY,
				   report.TOTAL_QTY_TAHUN,
				   report.JUMLAH_OPERATOR, 
				   report.GAJI_OPERATOR, 
				   report.TOTAL_GAJI_OPERATOR, 
				   report.TUNJANGAN_OPERATOR, 
				   report.TOTAL_TUNJANGAN_OPERATOR, 
				   report.TOTAL_GAJI_TUNJANGAN_OPERATOR, 
				   report.RP_QTY_OPERATOR, 
				   report.JUMLAH_HELPER, 
				   report.GAJI_HELPER, 
				   report.TOTAL_GAJI_HELPER, 
				   report.TUNJANGAN_HELPER, 
				   report.TOTAL_TUNJANGAN_HELPER, 
				   report.TOTAL_GAJI_TUNJANGAN_HELPER, 
				   report.RP_QTY_HELPER,
				   report.RVRA1_VALUE1, -- PAJAK
				   report.RVRA1_VALUE2,
				   report.RVRA1_VALUE3,
				   report.RVRA2_VALUE1, -- BAHAN BAKAR
				   report.RVRA2_VALUE2,
				   report.RVRA2_VALUE3,
				   report.RVRA3_VALUE1, -- OLI MESIN
				   report.RVRA3_VALUE2,
				   report.RVRA3_VALUE3,
				   report.RVRA4_VALUE1, -- OLI TRANSMISI
				   report.RVRA4_VALUE2,
				   report.RVRA4_VALUE3,
				   report.RVRA5_VALUE1, -- MINYAK HIDROLIC
				   report.RVRA5_VALUE2,
				   report.RVRA5_VALUE3,
				   report.RVRA6_VALUE1, -- GREASE
				   report.RVRA6_VALUE2,
				   report.RVRA6_VALUE3,
				   report.RVRA7_VALUE1, -- FILTER OLI
				   report.RVRA7_VALUE2,
				   report.RVRA7_VALUE3,
				   report.RVRA8_VALUE1, -- FILTER HIDROLIC
				   report.RVRA8_VALUE2,
				   report.RVRA8_VALUE3,
				   report.RVRA9_VALUE1, -- FILTER SOLAR
				   report.RVRA9_VALUE2,
				   report.RVRA9_VALUE3,
				   report.RVRA10_VALUE1, -- FILTER SOLAR MOISTURE SEPARATOR
				   report.RVRA10_VALUE2,
				   report.RVRA10_VALUE3,
				   report.RVRA11_VALUE1, -- FILTER UDARA
				   report.RVRA11_VALUE2,
				   report.RVRA11_VALUE3,
				   report.RVRA12_VALUE1, -- GANTI SPAREPART
				   report.RVRA12_VALUE2,
				   report.RVRA12_VALUE3,
				   report.RVRA13_VALUE1, --  GANTI BAN LUAR
				   report.RVRA13_VALUE2,
				   report.RVRA13_VALUE3,
				   report.RVRA14_VALUE1, -- GANTI BAN DALAM
				   report.RVRA14_VALUE2,
				   report.RVRA14_VALUE3,
				   report.RVRA15_VALUE1, -- SERVIS WORKSHOP
				   report.RVRA15_VALUE2,
				   report.RVRA15_VALUE3,
				   report.RVRA16_VALUE1, -- OVERHAUL
				   report.RVRA16_VALUE2,
				   report.RVRA16_VALUE3,
				   report.RVRA17_VALUE1, -- RENTAL
				   report.RVRA17_VALUE2,
				   report.RVRA17_VALUE3,
				   report.RVRA18_VALUE1, -- SERVIS BENGKEL LUAR
				   report.RVRA18_VALUE2,
				   report.RVRA18_VALUE3,
				   report.RVRA19_VALUE1,
				   report.RVRA19_VALUE2,
				   report.RVRA19_VALUE3,
				   report.RVRA20_VALUE1,
				   report.RVRA20_VALUE2,
				   report.RVRA20_VALUE3,
				   vra_sum.VALUE RP_QTY_VRA_TYPE
			FROM TR_RKT_VRA report
			LEFT JOIN TM_VRA master_vra
				ON report.VRA_CODE = master_vra.VRA_CODE
			LEFT JOIN TR_RKT_VRA_SUM vra_sum
				ON report.BA_CODE = vra_sum.BA_CODE
				AND report.PERIOD_BUDGET = vra_sum.PERIOD_BUDGET
				AND report.VRA_CODE = vra_sum.VRA_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON report.BA_CODE = ORG.BA_CODE
			WHERE report.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REPORT.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(report.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(report.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(report.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		$query .= "
			ORDER BY master_vra.VRA_SUB_CAT_DESCRIPTION, master_vra.VRA_CODE
		";
		return $query;
	}
	
	//menampilkan list Report Checkroll
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
	
	//menampilkan list tunjangan
    public function getTunjangan()
    {
        $sql = "
			SELECT TUNJANGAN_TYPE
			FROM TM_TUNJANGAN
			WHERE DELETE_USER IS NULL
				AND FLAG_RP_HK = 'YES'
			ORDER BY TUNJANGAN_TYPE
		";
        $result = $this->_db->fetchAll($sql);

        return $result;
    }
	
	//menampilkan list pk umum
    public function getPkUmum()
    {
        $sql = "
			SELECT TUNJANGAN_TYPE
			FROM TM_TUNJANGAN
			WHERE DELETE_USER IS NULL
				AND FLAG_RP_HK = 'NO'
			ORDER BY TUNJANGAN_TYPE
		";
        $result = $this->_db->fetchAll($sql);

        return $result;
    }
}

