<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma Pupuk TBM
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: SID 24/06/2013	: ambil data dari DB
						- getList					: SID 24/06/2013	: menampilkan list norma Pupuk TBM
						- save						: SID 24/06/2013	: simpan data
						- delete					: SID 24/06/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	24/06/2013
Update Terakhir		:	01/07/2014
Revisi				:	
	SID 01/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save, delete
=========================================================================================================================
*/
class Application_Model_NormaPupukTbmLess
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
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR(norma.ROWID) row_id, rownum, 
				   to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   norma.BA_CODE, 
				   norma.LAND_TYPE, 
				   norma.PALM_AGE, 
				   norma.MATURITY_STAGE, 
				   norma.MATERIAL_CODE, 
				   norma.ROTASI, 
				   norma.DOSIS, 
				   norma.JUMLAH,
				   norma.PRICE_ROTASI, 
				   norma.PRICE_YEAR,
				   material.MATERIAL_NAME,
				   norma_harga.PRICE,
				   norma.FLAG_TEMP
            FROM TN_PUPUK_TBM2_LESS norma
			LEFT JOIN TM_MATERIAL material
				ON material.MATERIAL_CODE = norma.MATERIAL_CODE
				AND material.PERIOD_BUDGET = norma.PERIOD_BUDGET
				AND material.BA_CODE = norma.BA_CODE
			LEFT JOIN TN_HARGA_BARANG norma_harga
				ON material.PERIOD_BUDGET = norma_harga.PERIOD_BUDGET
				AND material.BA_CODE = norma_harga.BA_CODE
				AND material.MATERIAL_CODE = norma_harga.MATERIAL_CODE
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
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PALM_AGE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MATURITY_STAGE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.DOSIS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JUMLAH) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PRICE_ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.PRICE_YEAR) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		//untuk inheritance
		if ($params['sub_cost_element'] != '') {
			$query .= "
                AND UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['sub_cost_element']."%')
            ";
        }
		
		$query .= "
			ORDER BY norma.PERIOD_BUDGET, norma.BA_CODE, norma.LAND_TYPE, norma.PALM_AGE
		";
		
		return $query;
	}
	
	//menampilkan list norma Pupuk TBM
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
	
	//simpan data
	public function save($row = array())
    { 
		$jumlah = $this->_formula->cal_NormaPupukTbmLess_Jumlah($row);
		$rp_rotasi = $this->_formula->cal_NormaPupukTbmLess_RpRotasi($row);
		$rp_tahun = $this->_formula->cal_NormaPupukTbmLess_RpTahun($row);
		
		$sql = "
			UPDATE TN_PUPUK_TBM2_LESS
			SET ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				DOSIS = REPLACE('{$row['DOSIS']}',',',''),
				JUMLAH = REPLACE('{$jumlah}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				PRICE_ROTASI = REPLACE('{$rp_rotasi}',',',''),
				PRICE_YEAR = REPLACE('{$rp_tahun}',',',''),
				TRIGGER_UPDATE = NULL,
				FLAG_TEMP = NULL,
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
	
	//simpan data temp
	public function saveTemp($row = array())
    { 
		$sql = "
			UPDATE TN_PUPUK_TBM2_LESS
			SET ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				DOSIS = REPLACE('{$row['DOSIS']}',',',''),
				JUMLAH = 0,
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				PRICE_ROTASI = 0,
				PRICE_YEAR = 0,
				TRIGGER_UPDATE = NULL,
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
	
	//hapus data
	public function delete($row)
    {
		$sql = "
			UPDATE TN_PUPUK_TBM2_LESS
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
        $jumlah = $this->_formula->cal_NormaPupukTbmLess_Jumlah($row);
		$rp_rotasi = $this->_formula->cal_NormaPupukTbmLess_RpRotasi($row);
		$rp_tahun = $this->_formula->cal_NormaPupukTbmLess_RpTahun($row);
		
		$sql = "
			UPDATE TN_PUPUK_TBM2_LESS
			SET ROTASI = REPLACE('{$row['ROTASI']}',',',''),
				DOSIS = REPLACE('{$row['DOSIS']}',',',''),
				JUMLAH = REPLACE('{$jumlah}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				PRICE_ROTASI = REPLACE('{$rp_rotasi}',',',''),
				PRICE_YEAR = REPLACE('{$rp_tahun}',',',''),
				TRIGGER_UPDATE = NULL,
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				DELETE_TIME = NULL,
				DELETE_USER = NULL
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
		";
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
}

