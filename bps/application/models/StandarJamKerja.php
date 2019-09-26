<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Standar Jam Kerja
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Standar Jam Kerja
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_StandarJamKerja
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
			SELECT ROWIDTOCHAR (jamker.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (jamker.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   jamker.BA_CODE,
				   jamker.JAM_KERJA,
				   jamker.FLAG_TEMP
			  FROM TM_STANDART_JAM_KERJA_WRA jamker 
			 LEFT JOIN TM_ORGANIZATION B
					  ON jamker.BA_CODE = B.BA_CODE
			 WHERE jamker.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(JAMKER.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(jamker.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(jamker.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(jamker.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(jamker.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(jamker.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(jamker.JAM_KERJA) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY jamker.PERIOD_BUDGET, jamker.BA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list Standar Jam Kerja
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
				UPDATE TM_STANDART_JAM_KERJA_WRA
				SET JAM_KERJA = REPLACE('".addslashes($row['JAM_KERJA'])."', ',', ''),
					FLAG_TEMP=NULL,
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
		return $result;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		
			$sql = "
				UPDATE TM_STANDART_JAM_KERJA_WRA
				SET JAM_KERJA = REPLACE('".addslashes($row['JAM_KERJA'])."', ',', ''),
					FLAG_TEMP='Y',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
		return $result;
    }
}

