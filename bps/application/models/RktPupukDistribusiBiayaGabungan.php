<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk RKT Pupuk Distribusi Biaya Gabungan
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT Pupuk Pupuk Distribusi Biaya Gabungan
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	18/07/2013
Update Terakhir		:	18/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktPupukDistribusiBiayaGabungan
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
		
		$options['optMatStage'] = $table->getMaturityStage();
		$result['src_matstage_code'] = array(
            'type'    => 'select',
            'name'    => 'src_matstage_code',
            'value'   => '',
            'options' => $options['optMatStage'],
            'ext'     => '',
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
            SELECT to_char(ha_statement.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   ha_statement.BA_CODE,
				   ORG.COMPANY_NAME,
				   ha_statement.AFD_CODE, 
				   ha_statement.BLOCK_CODE,
				   ha_statement.BLOCK_DESC, 
				   ha_statement.LAND_TYPE,
				   ha_statement.TOPOGRAPHY,
				   to_char(ha_statement.TAHUN_TANAM,'MM.RRRR') TAHUN_TANAM, 
				   to_char(ha_statement.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(ha_statement.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   ha_statement.MATURITY_STAGE_SMS1, 
				   ha_statement.MATURITY_STAGE_SMS2, 
				   ha_statement.HA_PLANTED, 
				   ha_statement.POKOK_TANAM, 
				   ha_statement.SPH,
				   rkt.JAN, 
				   rkt.FEB, 
				   rkt.MAR, 
				   rkt.APR, 
				   rkt.MAY, 
				   rkt.JUN, 
				   rkt.JUL, 
				   rkt.AUG, 
				   rkt.SEP, 
				   rkt.OCT, 
				   rkt.NOV, 
				   rkt.DEC, 
				   rkt.SETAHUN
			FROM TR_RKT_PUPUK rkt
			LEFT JOIN TM_HECTARE_STATEMENT ha_statement
				ON rkt.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET
				AND rkt.BA_CODE = ha_statement.BA_CODE
				AND rkt.AFD_CODE = ha_statement.AFD_CODE
				AND rkt.BLOCK_CODE = ha_statement.BLOCK_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON ha_statement.BA_CODE = ORG.BA_CODE
			WHERE ha_statement.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ha_statement.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(ha_statement.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }

		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(ha_statement.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//filter maturity_stage
		if (($params['src_matstage_code']) && ($params['src_matstage_code'] != 'ALL')) {
			$query .= "
                AND (
					UPPER(ha_statement.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
					OR UPPER(ha_statement.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
				)
            ";
        }
		
		//filter afdeling
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(ha_statement.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		$query .= "
			ORDER BY ha_statement.BA_CODE, ORG.COMPANY_NAME, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE
		";
		return $query;
	}
	
	//menampilkan list Report Pupuk Distribusi Biaya Gabungan
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
	
	//kalkulasi seluruh data RKT Pupuk Distribusi Biaya Normal
    public function calculateAllItem($params = array())
    {
        $result = true;

        //cari data
		$sql = "
			SELECT DISTINCT BA_CODE, AFD_CODE, BLOCK_CODE, LAND_TYPE, TAHUN_TANAM, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, HA_PLANTED, POKOK_TANAM
			FROM TM_HECTARE_STATEMENT
			WHERE DELETE_USER IS NULL 
			AND SUBSTR(BLOCK_CODE, 1, 3) <> 'ZZ_'
		";
		if($params['PERIOD_BUDGET']){
			$sql .= "AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') ";
			$budget = $params['PERIOD_BUDGET'];
		}
		if($params['budgetperiod']){
			$sql .= "AND PERIOD_BUDGET = TO_DATE('01-01-{$params['budgetperiod']}','DD-MM-RRRR') ";
			$budget = $params['budgetperiod'];
		}
		if($params['key_find']){
			$sql .= "AND BA_CODE IN ('".$params['key_find']."') ";
		}
		if($params['afd_code']){
			$sql .= "AND AFD_CODE = '".$params['afd_code']."' ";
		}
		if($params['block_code']){
			$sql .= "AND BLOCK_CODE = '".$params['block_code']."' ";
		}
		if(($params['land_type']) && ($params['land_type'] <> 'ALL')){
			$sql .= "AND LAND_TYPE = '".$params['land_type']."' ";
		}
		if(($params['topo']) && ($params['topo'] <> 'ALL')){
			$sql .= "AND TOPOGRAPHY = '".$params['topo']."' ";
		}
		
		if($params['BA_CODE']){
			$sql .= "AND BA_CODE IN ('".$params['BA_CODE']."') ";
		}
		//filter maturity_stage
		if (($params['maturity_stage']) && ($params['maturity_stage'] != 'ALL')) {
			$sql .= "
                AND (
					UPPER(MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['maturity_stage']."%')
					OR UPPER(MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['maturity_stage']."%')
				)
            ";
        }
		$records = $this->_db->fetchAll($sql);
		
		if (!empty($records)) {
            foreach ($records as $index => $record) {
				//cari summary data
				$sql = "
					SELECT SUM(DIS_COST_JAN) DIS_COST_JAN,
						   SUM(DIS_COST_FEB) DIS_COST_FEB, 
						   SUM(DIS_COST_MAR) DIS_COST_MAR,
						   SUM(DIS_COST_APR) DIS_COST_APR, 
						   SUM(DIS_COST_MAY) DIS_COST_MAY, 
						   SUM(DIS_COST_JUN) DIS_COST_JUN, 
						   SUM(DIS_COST_JUL) DIS_COST_JUL, 
						   SUM(DIS_COST_AUG) DIS_COST_AUG, 
						   SUM(DIS_COST_SEP) DIS_COST_SEP, 	
						   SUM(DIS_COST_OCT) DIS_COST_OCT, 
						   SUM(DIS_COST_NOV) DIS_COST_NOV, 
						   SUM(DIS_COST_DEC) DIS_COST_DEC, 
						   SUM(DIS_COST_YEAR) DIS_COST_YEAR,
						   SUM(COST_SMS1) COST_SMS1,
						   SUM(COST_SMS2) COST_SMS2
					FROM TR_RKT_PUPUK_COST_ELEMENT
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
						AND BA_CODE = '".$record['BA_CODE']."'
						AND AFD_CODE = '".$record['AFD_CODE']."'
						AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
				";
				$summary_cost = $this->_db->fetchRow($sql);
				
				//hapus data lama
				$sql = "
					DELETE FROM TR_RKT_PUPUK
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
						AND BA_CODE = '".$record['BA_CODE']."'
						AND AFD_CODE = '".$record['AFD_CODE']."'
						AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
						AND TIPE_TRANSAKSI = 'COST';
				";
				
				$sql .= "
					INSERT INTO TR_RKT_PUPUK (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TRX_RKT_CODE, MATURITY_STAGE_SMS1, 
						MATURITY_STAGE_SMS2, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SETAHUN, 
						INSERT_USER, INSERT_TIME, TIPE_TRANSAKSI, COST_SMS1, COST_SMS2
					)
					VALUES (
						TO_DATE('01-01-{$budget}','DD-MM-RRRR'),
						'".addslashes($record['BA_CODE'])."',
						'".addslashes($record['AFD_CODE'])."',
						'".addslashes($record['BLOCK_CODE'])."',
						".$params['PERIOD_BUDGET']." || '-' || '".addslashes($record['BA_CODE'])."' || '-' || '".addslashes($record['AFD_CODE'])."'
					|| '-' || '".addslashes($record['BLOCK_CODE'])."' || '-' || 'RKT013',
						'".addslashes($record['MATURITY_STAGE_SMS1'])."',
						'".addslashes($record['MATURITY_STAGE_SMS2'])."',
						REPLACE('".addslashes($summary_cost['DIS_COST_JAN'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_FEB'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_MAR'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_APR'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_MAY'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_JUN'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_JUL'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_AUG'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_SEP'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_OCT'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_NOV'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_DEC'])."',',',''),
						REPLACE('".addslashes($summary_cost['DIS_COST_YEAR'])."',',',''),
						'{$this->_userName}',
						SYSDATE,
						'COST',
						REPLACE('".addslashes($summary_cost['COST_SMS1'])."',',',''),
						REPLACE('".addslashes($summary_cost['COST_SMS2'])."',',','')
					);
				";
				
				//create sql file
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }

        return $result;
    }
}

