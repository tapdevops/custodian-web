<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk report RKT CAPEX
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list report RKT CAPEX
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/07/2013
Update Terakhir		:	08/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportRktCapex
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
            SELECT ROWIDTOCHAR (rkt.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   rkt.BA_CODE,
				   rkt.COA_CODE, 
				   coa.DESCRIPTION as COA_DESC,
				   rkt.ASSET_CODE, 
				   aset.DESCRIPTION ASSET_DESC,
				   rkt.DETAIL_SPESIFICATION, 
				   aset.UOM,
				   rkt.URGENCY_CAPEX, 
				   rkt.PRICE, 
				   rkt.QTY_ACTUAL, 
				   rkt.DIS_TAHUN_BERJALAN, 
				   rkt.DIS_JAN, 
				   rkt.DIS_FEB, 
				   rkt.DIS_MAR, 
				   rkt.DIS_APR, 
				   rkt.DIS_MAY, 
				   rkt.DIS_JUN, 
				   rkt.DIS_JUL, 
				   rkt.DIS_AUG, 
				   rkt.DIS_SEP, 
				   rkt.DIS_OCT, 
				   rkt.DIS_NOV, 
				   rkt.DIS_DEC, 
				   rkt.DIS_BIAYA_JAN, 
				   rkt.DIS_BIAYA_FEB, 
				   rkt.DIS_BIAYA_MAR, 
				   rkt.DIS_BIAYA_APR, 
				   rkt.DIS_BIAYA_MAY, 
				   rkt.DIS_BIAYA_JUN, 
				   rkt.DIS_BIAYA_JUL, 
				   rkt.DIS_BIAYA_AUG, 
				   rkt.DIS_BIAYA_SEP, 
				   rkt.DIS_BIAYA_OCT, 
				   rkt.DIS_BIAYA_NOV, 
				   rkt.DIS_BIAYA_DEC, 
				   rkt.DIS_BIAYA_TOTAL
			FROM TR_RKT_CAPEX rkt
			LEFT JOIN TM_COA coa
				ON rkt.COA_CODE = coa.COA_CODE
			LEFT JOIN TM_ASSET aset
				ON rkt.PERIOD_BUDGET = aset.PERIOD_BUDGET
				AND rkt.BA_CODE = aset.BA_CODE
				AND rkt.ASSET_CODE = aset.ASSET_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON rkt.BA_CODE = ORG.BA_CODE
			WHERE rkt.DELETE_USER IS NULL
			AND rkt.FLAG_TEMP IS NULL
        ";
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RKT.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(rkt.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.ASSET_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.DETAIL_SPESIFICATION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.UOM) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.COA_CODE, rkt.ASSET_CODE, rkt.DETAIL_SPESIFICATION
		";
		return $query;
	}
	
	//menampilkan list report RKT CAPEX
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

