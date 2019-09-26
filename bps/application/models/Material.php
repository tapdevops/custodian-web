<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Material
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Material
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_Material
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
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
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (material.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (material.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   material.BA_CODE,
				   material.MATERIAL_CODE,
				   material.MATERIAL_NAME,
				   material.UOM,
				   material.VALUATION_CLASS,
				   material.COA_CODE,
				   material.PRICE,
				   material.BASIC_NORMA_CODE,
				   material.FLAG,
				   material.DETAIL_CAT_CODE,
				   material.DETAIL_CAT_DESC,
				   material.FLAG_TEMP,
				   basic.PERCENT_INCREASE,
				   basic.DESCRIPTION NORMA_DESCRIPTION,
				   coa.DESCRIPTION COA_DESC
			  FROM TM_MATERIAL material
				   LEFT JOIN TN_BASIC basic
					  ON     material.PERIOD_BUDGET = basic.PERIOD_BUDGET
						 AND material.BA_CODE = basic.BA_CODE
						 AND material.BASIC_NORMA_CODE = basic.BASIC_NORMA_CODE
				   LEFT JOIN TM_COA coa
					  ON material.COA_CODE = coa.COA_CODE
							 LEFT JOIN TM_ORGANIZATION B
					  ON material.BA_CODE = B.BA_CODE
			 WHERE material.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."')  LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(material.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(material.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(material.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(material.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(material.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.VALUATION_CLASS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.BASIC_NORMA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.FLAG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.DETAIL_CAT_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.DETAIL_CAT_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(basic.PERCENT_INCREASE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY material.MATERIAL_CODE, material.COA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list material
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
        $result = true;
		
			$sql = "
				UPDATE TM_MATERIAL
				SET  
					MATERIAL_NAME = '".addslashes($row['MATERIAL_NAME'])."',
					UOM = '".addslashes($row['UOM'])."',
					VALUATION_CLASS = '".addslashes($row['VALUATION_CLASS'])."',
					COA_CODE = '".addslashes($row['COA_CODE'])."',
					PRICE = REPLACE('".addslashes($row['PRICE'])."', ',', ''),
					BASIC_NORMA_CODE = '".addslashes($row['BASIC_NORMA_CODE'])."',
					FLAG = '".addslashes($row['FLAG'])."',
					DETAIL_CAT_CODE = '".addslashes($row['DETAIL_CAT_CODE'])."',
					DETAIL_CAT_DESC = '".addslashes($row['DETAIL_CAT_DESC'])."',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP=NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		
			$sql = "
				UPDATE TM_MATERIAL
				SET  
					MATERIAL_NAME = '".addslashes($row['MATERIAL_NAME'])."',
					UOM = '".addslashes($row['UOM'])."',
					VALUATION_CLASS = '".addslashes($row['VALUATION_CLASS'])."',
					COA_CODE = '".addslashes($row['COA_CODE'])."',
					PRICE = REPLACE('".addslashes($row['PRICE'])."', ',', ''),
					BASIC_NORMA_CODE = '".addslashes($row['BASIC_NORMA_CODE'])."',
					FLAG = '".addslashes($row['FLAG'])."',
					DETAIL_CAT_CODE = '".addslashes($row['DETAIL_CAT_CODE'])."',
					DETAIL_CAT_DESC = '".addslashes($row['DETAIL_CAT_DESC'])."',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP = 'Y'
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "UPDATE TM_MATERIAL
					SET  
						DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MASTER MATERIAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MASTER MATERIAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

