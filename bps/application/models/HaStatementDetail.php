<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Ha Statement Detail
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Ha Statement
						- save				: simpan data
						- saveTemp			: YIR 07/07/2014	: simpan data sementara sesuai input user
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	03/06/2013
Update Terakhir		:	03/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_HaStatementDetail
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
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
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
            SELECT ROWIDTOCHAR (A.ROWID) row_id,
				   TO_CHAR (A.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   A.BA_CODE,
				   A.AFD_CODE,
				   A.BLOCK_CODE,
				   C.BLOCK_DESC, 
                   A.LAND_CATEGORY,
				   (SELECT PARAMETER_VALUE
					  FROM T_PARAMETER_VALUE
					 WHERE PARAMETER_CODE = 'LAND_CAT'
						   AND PARAMETER_VALUE_CODE = LAND_CATEGORY)
					  LAND_CATEGORY_DESC,
				   A.HA,
				   A.FLAG_TEMP
			  FROM TM_HECTARE_STATEMENT_DETAIL A
			  LEFT JOIN TM_HECTARE_STATEMENT C
				ON A.PERIOD_BUDGET = C.PERIOD_BUDGET
				AND A.BA_CODE = C.BA_CODE
				AND A.BLOCK_CODE = C.BLOCK_CODE
			  LEFT JOIN TM_ORGANIZATION B
			  ON A.BA_CODE = B.BA_CODE
			 WHERE A.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND  UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE)||'%'";
		}

		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(A.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
				AND UPPER(A.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		if ($params['src_block'] != '') {
			$query .= "
				AND UPPER(A.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%')
            ";
        }
		
		$query .= "
			ORDER BY A.BA_CODE, A.AFD_CODE, A.BLOCK_CODE, A.LAND_CATEGORY
		";
		return $query;
	}
	
	//menampilkan list Ha Statement Detail
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
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$row['TOTAL_HA'] = $this->getTotalHa($params);
                $result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//cari total HA
	public function getTotalHa($params = array())
    {
		$result = $where = '';
		
		if($this->_siteCode <> 'ALL'){
			$where .= "
                AND UPPER(BA_CODE) LIKE UPPER('%".$this->_siteCode."%')
            ";
		}
		
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		if ($params['key_find'] != '') {
			$where .= "
                AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$where .= "
				AND UPPER(AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		if ($params['src_block'] != '') {
			$where .= "
				AND UPPER(BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%')
            ";
        }
		
		$total_query .= "
			SELECT SUM(a.HA) HA
			FROM (
				SELECT SUM(HA_PLANTED) HA
				FROM TM_HECTARE_STATEMENT
				WHERE DELETE_USER IS NULL
					$where
				UNION
				SELECT SUM(HA) HA
				FROM TM_HECTARE_STATEMENT_DETAIL
				WHERE DELETE_USER IS NULL
					$where
			) a
			";
		$result = $this->_db->fetchOne($total_query);

        return $result;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		$sql = "UPDATE TM_HECTARE_STATEMENT_DETAIL
				SET HA = REPLACE('{$row['HA']}', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP = NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				 ";
			 
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//simpan data
	public function saveTemp($row = array())
    { 
        $result = true;
		$sql = "UPDATE TM_HECTARE_STATEMENT_DETAIL
				SET HA = REPLACE('{$row['HA']}', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP = NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				 ";
			 
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
}

