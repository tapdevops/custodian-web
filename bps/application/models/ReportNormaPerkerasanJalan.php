<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Perkerasan Jalan
Function 			:	- getList			: menampilkan list norma Perkerasan Jalan
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	05/07/2013
Update Terakhir		:	05/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportNormaPerkerasanJalan
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
		//Edited by Ardo, 25-08-2016 : Harga Perkerasan Jalan - Penambahan JARAK_RANGE
		$query = "
	SELECT ROWIDTOCHAR (PJH.ROWID) ROW_ID,
       ROWNUM,
       TO_CHAR (PJH.PERIOD_BUDGET, 'RRRR') AS PERIOD_BUDGET,
       PJH.BA_CODE,
       PJH.ACTIVITY_CODE,
       TMA.DESCRIPTION,
       PV.PARAMETER_VALUE,
	   PJH.JARAK_RANGE,
       PJH.JARAK_AVG,
       PJH.JARAK_PP,
       PJH.MATERIAL_QTY,
       PJH.TRIP_MATERIAL,
       PJH.BIAYA_MATERIAL,
       PJH.DT_TRIP,
       PJH.DT_PRICE,
       PJH.EXCAV_HM,
       PJH.EXCAV_PRICE,
       PJH.COMPACTOR_HM,
       PJH.COMPACTOR_PRICE,
       PJH.GRADER_HM,
       PJH.GRADER_PRICE,
       PJH.INTERNAL_PRICE,
       PJH.EXTERNAL_PERCENT,
       PJH.EXTERNAL_BENEFIT,
       PJH.EXTERNAL_PRICE
	FROM TN_PERKERASAN_JALAN_HARGA PJH
       LEFT JOIN TM_ACTIVITY TMA
          ON PJH.ACTIVITY_CODE = TMA.ACTIVITY_CODE
       LEFT JOIN TM_ORGANIZATION B
          ON PJH.BA_CODE = B.BA_CODE
       LEFT JOIN T_PARAMETER_VALUE PV
           ON PJH.JARAK_RANGE = PV.PARAMETER_VALUE_CODE
		   WHERE 1=1
		  ";
		 
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(PJH.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(PJH.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(PJH.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(PJH.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(PJH.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(PJH.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(PJH.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TMA.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(PV.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(PJH.JARAK_AVG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(PJH.JARAK_PP) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }	
		
		$query .= "
			ORDER BY PJH.PERIOD_BUDGET, PJH.BA_CODE, PJH.ACTIVITY_CODE, 
					 PV.PARAMETER_VALUE
		";

		return $query;

	}
	
	//menampilkan list norma Infrastruktur
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
	
	//simpan external_price
	public function save_external_price($row = array())
	{
		$sql = "
			UPDATE TN_PERKERASAN_JALAN_HARGA
			SET EXTERNAL_PRICE = '".preg_replace('/[,]/', '', $row['EXTERNAL_PRICE'])."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = to_date('1/1/".$row['PERIOD_BUDGET']."', 'MM/DD/YYYY') AND
				BA_CODE = '".$row['BA_CODE']."' AND
				ACTIVITY_CODE = '".$row['ACTIVITY_CODE']."' AND
				JARAK_RANGE = '".$row['JARAK_RANGE']."';
		";
		
		//create sql file
		
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
}

