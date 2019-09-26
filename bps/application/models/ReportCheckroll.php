<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Report Checkroll
Function 			:	- getList					: menampilkan list Report Checkroll
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	13/06/2013
Update Terakhir		:	13/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportCheckroll
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
				   report.JOB_CODE, report.EMPLOYEE_STATUS,
				   report.GP_INFLASI, 
				   report.MPP_PERIOD_BUDGET, 
				   report.TOTAL_GP_MPP, 
				   report.TOTAL_GAJI_TUNJANGAN, 
				   report.RP_HK_PERBULAN, 
				   report.TOTAL_TUNJANGAN_PK_UMUM, 
				   report.DIS_YEAR, 
				   report.DIS_JAN, 
				   report.DIS_FEB, 
				   report.DIS_MAR, 
				   report.DIS_APR, 
				   report.DIS_MAY, 
				   report.DIS_JUN, 
				   report.DIS_JUL, 
				   report.DIS_AUG, 
				   report.DIS_SEP, 
				   report.DIS_OCT, 
				   report.DIS_NOV, 
				   report.DIS_DEC,  
                   job.JOB_DESCRIPTION, grupjob.PARAMETER_VALUE GROUP_CHECKROLL_DESC, (basic.PERCENT_INCREASE - 100) PERCENT_INCREASE,
				   (SELECT summary.RP_HK 
					FROM TR_RKT_CHECKROLL_SUM summary 
					WHERE summary.PERIOD_BUDGET = report.PERIOD_BUDGET
						AND summary.BA_CODE = report.BA_CODE
						AND summary.JOB_CODE = report.JOB_CODE) as SUMMARY_RP_HK,
                   detail.*
			FROM TR_RKT_CHECKROLL report
			LEFT JOIN (
				SELECT detail_report.TRX_CR_CODE, 
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'PPH_21',detail_report.JUMLAH)) PPH_21,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'ASTEK', detail_report.JUMLAH)) ASTEK,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'JABATAN', detail_report.JUMLAH)) JABATAN,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'KEHADIRAN', detail_report.JUMLAH)) KEHADIRAN,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'LAINNYA', detail_report.JUMLAH)) LAINNYA,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'CATU', detail_report.JUMLAH)) CATU,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'OBAT', detail_report.JUMLAH)) OBAT,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'THR', detail_report.JUMLAH)) THR,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'HHR', detail_report.JUMLAH)) HHR,
						SUM(DECODE(detail_report.TUNJANGAN_TYPE, 'BONUS', detail_report.JUMLAH)) BONUS
				FROM TR_RKT_CHECKROLL_DETAIL detail_report
				GROUP BY detail_report.TRX_CR_CODE
			) detail
				ON detail.TRX_CR_CODE = report.TRX_CR_CODE
			LEFT JOIN TM_JOB_TYPE job
				ON report.JOB_CODE = job.JOB_CODE
			LEFT JOIN T_PARAMETER_VALUE grupjob
				ON grupjob.PARAMETER_VALUE_CODE = job.GROUP_CHECKROLL_CODE
				AND grupjob.PARAMETER_CODE = 'GROUP_JOB'
			LEFT JOIN TN_BASIC basic
				ON report.BA_CODE = basic.BA_CODE
				AND report.PERIOD_BUDGET = basic.PERIOD_BUDGET
				AND BASIC_NORMA_CODE = 'NC001'
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
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(report.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
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
		//---CHECK---
		/*if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(report.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(report.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(report.MATURITY_STAGE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(report.TUNJANGAN_TYPE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }*/
		
		$query .= "
			ORDER BY report.BA_CODE, report.JOB_CODE, report.EMPLOYEE_STATUS
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

