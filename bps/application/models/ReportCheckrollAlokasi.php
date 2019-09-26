<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk report alokasi checkroll
Function 			:	- getList	: menampilkan list report alokasi checkroll
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	09/07/2013
Update Terakhir		:	09/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportCheckrollAlokasi
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
	SELECT ROWIDTOCHAR (RCA.ROWID) ROW_ID,
       ROWNUM,
	   TO_CHAR (RCA.PERIOD_BUDGET, 'RRRR') AS PERIOD_BUDGET,
       RCA.BA_CODE,
       RCA.MATURITY_STAGE,
       RCA.TUNJANGAN_TYPE,
	   RCA.TOTAL_BIAYA,
       RCA.DIS_JAN,
       RCA.DIS_FEB,
       RCA.DIS_MAR,
       RCA.DIS_APR,
       RCA.DIS_MAY,
       RCA.DIS_JUN,
       RCA.DIS_JUL,
       RCA.DIS_AUG,
       RCA.DIS_SEP,
       RCA.DIS_OCT,
       RCA.DIS_NOV,
       RCA.DIS_DEC
	FROM TR_RPT_DISTRIBUSI_COA RCA
	LEFT JOIN TM_ORGANIZATION B
	ON RCA.BA_CODE = B.BA_CODE
	WHERE REPORT_TYPE = 'CR_ALOKASI'
		  ";
		  
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RCA.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(RCA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(RCA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(RCA.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(RCA.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(RCA.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(RCA.MATURITY_STAGE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(RCA.TUNJANGAN_TYPE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }	
		
		$query .= "
			ORDER BY RCA.PERIOD_BUDGET, RCA.BA_CODE, RCA.MATURITY_STAGE, 
					 RCA.TUNJANGAN_TYPE
		";
		return $query;

	}
	
	//menampilkan list report alokasi checkroll
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
}

