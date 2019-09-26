<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Catu
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Catu
						- save				: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	30/05/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_Catu
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
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
			SELECT ROWIDTOCHAR (A.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (A.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   A.BA_CODE,
				   A.EMPLOYEE_STATUS,
				   A.RICE_PORTION,
				   A.PRICE_KG,
				   A.CATU_BERAS,
				   A.HKE_BULAN,
				   B.REGION_CODE,
				   A.FLAG_TEMP
			  FROM TM_CATU A
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
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(A.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(A.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.EMPLOYEE_STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.RICE_PORTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.PRICE_KG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.CATU_BERAS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(A.HKE_BULAN) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY A.BA_CODE, A.EMPLOYEE_STATUS
		";
		
		return $query;
	}
	
	//menampilkan list Catu
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
		$catu_bulan = $this->_formula->cal_MasterCatu_CatuBulan($row);
		
		$sql = "
			UPDATE TM_CATU
			SET  
				RICE_PORTION = REPLACE('".addslashes($row['RICE_PORTION'])."', ',', ''),
				PRICE_KG = REPLACE('".addslashes($row['PRICE_KG'])."', ',', ''),
				CATU_BERAS = REPLACE('".addslashes($catu_bulan)."', ',', ''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP = NULL
			 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
        return $result;
    }
	
	//simpan data
	public function savetemp($row = array())
    { 
        $result = true;
		$catu_bulan = $this->_formula->cal_MasterCatu_CatuBulan($row);
		
		$sql = "
			UPDATE TM_CATU
			SET  
				RICE_PORTION = REPLACE('".addslashes($row['RICE_PORTION'])."', ',', ''),
				PRICE_KG = REPLACE('".addslashes($row['PRICE_KG'])."', ',', ''),
				CATU_BERAS = REPLACE('".addslashes($catu_bulan)."', ',', ''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE,
				FLAG_TEMP = 'Y'
			 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";

		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
        return $result;
    }
	
	//update summary
	public function updateSummaryCatu($row = array())
	{
		$result = true;
		$sql = "
			DELETE FROM TM_CATU_SUM
			WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR');
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
		///////////////////////////// SUMMARY TOTAL HARGA CATU /////////////////////////////
		$sql = "
			SELECT AVG(CATU_BERAS) CATU_BERAS
			FROM TM_CATU
			WHERE PERIOD_BUDGET = TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND DELETE_USER IS NULL
		";
		$sum_price = $this->_db->fetchOne($sql); 
		
		$sql = "
			INSERT INTO TM_CATU_SUM (PERIOD_BUDGET, BA_CODE, CATU_BERAS_SUM, INSERT_USER, INSERT_TIME)
			VALUES (
				TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
				'".addslashes($row['BA_CODE'])."',
				'".addslashes(number_format($sum_price, 2, '.', ''))."',
				'{$this->_userName}',
				SYSDATE
			);
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
		return true;
	}
}

