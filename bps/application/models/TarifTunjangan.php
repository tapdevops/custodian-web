<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Tarif Tunjangan
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list Tarif Tunjangan
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	07/06/2013
Update Terakhir		:	07/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_TarifTunjangan
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
			SELECT ROWIDTOCHAR (tarif.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (tarif.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   tarif.BA_CODE,
				   tarif.JOB_CODE,
				   tarif.EMPLOYEE_STATUS,
				   tarif.TUNJANGAN_TYPE,
				   tarif.VALUE,
				   tarif.FLAG_TEMP,
				   job.JOB_DESCRIPTION
			  FROM TM_TARIF_TUNJANGAN tarif
				   INNER JOIN TM_JOB_TYPE job
					  ON tarif.JOB_CODE = job.JOB_CODE
				   LEFT JOIN TM_ORGANIZATION B
					  ON tarif.BA_CODE = B.BA_CODE
			 WHERE tarif.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(TARIF.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(tarif.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
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
                AND UPPER(tarif.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(tarif.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(tarif.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(tarif.JOB_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(tarif.EMPLOYEE_STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(tarif.TUNJANGAN_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(tarif.VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(job.JOB_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY tarif.PERIOD_BUDGET, tarif.BA_CODE, tarif.JOB_CODE
		";
		
		return $query;
	}
	
	//menampilkan list Tarif Tunjangan
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
				UPDATE TM_TARIF_TUNJANGAN
				SET VALUE = REPLACE('".addslashes($row['VALUE'])."', ',', ''),
					FLAG_TEMP=NULL,
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return true;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		
			$sql = "
				UPDATE TM_TARIF_TUNJANGAN
				SET VALUE = REPLACE('".addslashes($row['VALUE'])."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					FLAG_TEMP='Y'
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return true;
    }
}

