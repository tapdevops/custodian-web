<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma VRA
Function 			:	- getInput					: YIR 23/06/2014	: setting input untuk region
						- getData					: SID 21/06/2013	: ambil data dari DB
						- getList					: SID 21/06/2013	: menampilkan list norma VRA
						- save						: SID 21/06/2013	: simpan data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	21/06/2013
Update Terakhir		:	30/06/2014
Revisi				:	
	SID 30/06/2014	: 	- penambahan filter di getData
						- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save
=========================================================================================================================
*/
class Application_Model_NormaVra
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
			SELECT ROWIDTOCHAR (norma.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (norma.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   norma.REGION_CODE,
				   norma.BA_CODE,
				   norma.VRA_CODE,
				   norma.MIN_YEAR,
				   norma.MAX_YEAR,
				   norma.QTY_DAY,
				   norma.DAY_YEAR_VRA,
				   norma.SUB_RVRA_CODE,
				   norma.MATERIAL_CODE,
				   norma.QTY_UOM,
				   vra.TYPE VRA_TYPE,
				   vra.UOM,
				   rvra.SUB_RVRA_DESCRIPTION,
				   material.MATERIAL_NAME,
				   norma_harga.PRICE,
				   norma.FLAG_TEMP
			 FROM TN_VRA norma
			 LEFT JOIN TM_VRA vra
				ON norma.VRA_CODE = vra.VRA_CODE
			 LEFT JOIN TM_RVRA rvra
				ON norma.SUB_RVRA_CODE = rvra.SUB_RVRA_CODE
			 LEFT JOIN TM_MATERIAL material
				ON norma.PERIOD_BUDGET = material.PERIOD_BUDGET
				AND norma.BA_CODE = material.BA_CODE
				AND norma.MATERIAL_CODE = material.MATERIAL_CODE
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
		
		if ($params['src_rvra_code'] != '') {
			$query .= "
                AND UPPER(norma.SUB_RVRA_CODE) LIKE UPPER('%".$params['src_rvra_code']."%')
            ";
        }
		
		if ($params['min_year'] != '' && $params['max_year'] != '') {
			$query .= "
                AND norma.MIN_YEAR = '".$params['min_year']."' 
				AND norma.MAX_YEAR = '".$params['max_year']."'
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
					OR UPPER(norma.VRA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MIN_YEAR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MAX_YEAR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_DAY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.DAY_YEAR_VRA) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.SUB_RVRA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.QTY_UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(vra.VRA_SUB_CAT_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(vra.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rvra.SUB_RVRA_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		//untuk inheritance
		if ($params['sub_wra_group'] != '') {
			$query .= "
                AND UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['sub_wra_group']."%')
            ";
        }
		
		$query .= "
			ORDER BY norma.VRA_CODE, norma.SUB_RVRA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list norma VRA
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
        $sql = "
			UPDATE TN_VRA
			SET QTY_UOM =  REPLACE('{$row['QTY_UOM']}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				TRIGGER_UPDATE = NULL,
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//simpan data sementara
	public function saveTemp($row = array())
    { 
        $sql = "
			UPDATE TN_VRA
			SET QTY_UOM =  REPLACE('{$row['QTY_UOM']}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				TRIGGER_UPDATE = NULL,
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//kalkulasi data saat upload
	public function calculateData($row = array())
    { 
         $sql = "
			UPDATE TN_VRA
			SET QTY_UOM =  REPLACE('{$row['QTY_UOM']}',',',''),
				PRICE = REPLACE('{$row['PRICE']}',',',''),
				TRIGGER_UPDATE = NULL,
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
		";
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
	
}

