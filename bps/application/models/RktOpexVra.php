<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk RKT OPEX VRA
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT OPEX VRA
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	22/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktOpexVra
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
		$query ="
			SELECT ROWNUM, RKT.*,
                ORG.COMPANY_NAME,
                coa.DESCRIPTION AS COA_DESC,
                param.PARAMETER_VALUE GROUP_BUM_DESCRIPTION,
				(basic.PERCENT_INCREASE - 100) INFLASI_NASIONAL
			FROM (
        SELECT ROWIDTOCHAR (rkt.ROWID) row_id,
               '' ROW_ID_TEMP,
			   rkt.FLAG_TEMP,
			   rkt.TRX_CODE,
               TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
               rkt.BA_CODE,
               rkt.COA_CODE,
               rkt.GROUP_BUM_CODE,
               rkt.ACTUAL,
               rkt.TAKSASI,
               rkt.ANTISIPASI,
               rkt.PERSENTASE_INFLASI,
               rkt.TOTAL_BIAYA,
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
               rkt.KETERANGAN
          FROM TR_RKT_OPEX rkt
         WHERE rkt.DELETE_USER IS NULL AND rkt.TIPE_TRANSAKSI = 'VRA') RKT
                         LEFT JOIN TM_COA coa
                            ON rkt.COA_CODE = coa.COA_CODE
                         LEFT JOIN T_PARAMETER_VALUE param
                            ON rkt.GROUP_BUM_CODE = param.PARAMETER_VALUE_CODE
                               AND param.PARAMETER_CODE = 'GROUP_BUM'
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON rkt.BA_CODE = ORG.BA_CODE
                         LEFT JOIN TN_BASIC basic
                            ON     RKT.PERIOD_BUDGET = TO_CHAR(basic.PERIOD_BUDGET,'RRRR')
                               AND RKT.BA_CODE = basic.BA_CODE
                               AND basic.BASIC_NORMA_CODE = 'NC021' 
		WHERE 1=1 ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RKT.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND rkt.PERIOD_BUDGET  = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND rkt.PERIOD_BUDGET  = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND rkt.PERIOD_BUDGET = to_char(to_date('".$this->_period."','DD-MM-RRRR'), 'RRRR')
            ";
		}
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_coa_code'] != '') {
			$query .= "
                AND UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(rkt.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.GROUP_BUM_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(param.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.KETERANGAN) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.COA_CODE, rkt.GROUP_BUM_CODE, rkt.KETERANGAN
		"; 
		
		//echo $query;
		//die();
		
		return $query;
	}
	
	//ambil data yang dapat didownload dari DB
    public function getDataDownload($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR (rkt.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   rkt.BA_CODE,
				   ORG.COMPANY_NAME,
				   rkt.COA_CODE, 
				   coa.DESCRIPTION as COA_DESC,
				   rkt.GROUP_BUM_CODE, 
				   param.PARAMETER_VALUE GROUP_BUM_DESCRIPTION,
				   rkt.ACTUAL, 
				   rkt.TAKSASI, 
				   rkt.ANTISIPASI,
				   rkt.PERSENTASE_INFLASI, 
				   rkt.TOTAL_BIAYA, 
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
				   rkt.KETERANGAN,
				   basic.PERCENT_INCREASE INFLASI_NASIONAL
			FROM TR_RKT_OPEX rkt
			LEFT JOIN TM_COA coa
				ON rkt.COA_CODE = coa.COA_CODE
			LEFT JOIN T_PARAMETER_VALUE param
				ON rkt.GROUP_BUM_CODE = param.PARAMETER_VALUE_CODE
				AND param.PARAMETER_CODE = 'GROUP_BUM'
			LEFT JOIN TM_ORGANIZATION ORG
				ON rkt.BA_CODE = ORG.BA_CODE
			LEFT JOIN TN_BASIC basic
				ON rkt.PERIOD_BUDGET = basic.PERIOD_BUDGET
				AND rkt.BA_CODE = basic.BA_CODE
				AND basic.BASIC_NORMA_CODE = 'NC021'
			WHERE rkt.DELETE_USER IS NULL
				AND rkt.TIPE_TRANSAKSI = 'VRA'
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
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_coa_code'] != '') {
			$query .= "
                AND UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
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
					OR UPPER(rkt.GROUP_BUM_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(param.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.KETERANGAN) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.COA_CODE, rkt.GROUP_BUM_CODE, rkt.KETERANGAN
		";
		
		return $query;
	}
	
	//menampilkan list RKT OPEX VRA
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
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
			SELECT ROWIDTOCHAR(ROWID) ROW_ID 
			FROM TR_RKT_OPEX 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND COA_CODE = '{$params['COA_CODE']}'
				AND TIPE_TRANSAKSI = 'VRA'
		";        
        $rows = $this->_db->fetchOne ($sql);
        return $rows;
    }
	
	//simpan temp data-NEW YIR
	public function saveTemp($row = array())
	{
		$sql = "
			UPDATE TR_RKT_OPEX
			SET ACTUAL = REPLACE('".addslashes($row['ACTUAL'])."',',',''), 
				TAKSASI = REPLACE('".addslashes($row['TAKSASI'])."',',',''),
				ANTISIPASI = NULL, 
				PERSENTASE_INFLASI = NULL, 
				TOTAL_BIAYA = NULL, 
				DIS_JAN = NULL, DIS_FEB = NULL, 
				DIS_MAR = NULL, DIS_APR = NULL, 
				DIS_MAY = NULL, DIS_JUN = NULL, 
				DIS_JUL = NULL, DIS_AUG = NULL, 
				DIS_SEP = NULL, DIS_OCT = NULL, 
				DIS_NOV = NULL, DIS_DEC = NULL,
				COST_SMS1 = NULL, COST_SMS2 = NULL,
				KETERANGAN = '".addslashes($row['KETERANGAN'])."',
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".addslashes($row['PERIOD_BUDGET'])."'
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND COA_CODE = '".addslashes($row['COA_CODE'])."'
				AND GROUP_BUM_CODE = '".addslashes($row['GROUP_BUM_CODE'])."'
				AND TIPE_TRANSAKSI = 'VRA';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
	}
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$antisipasi = $this->_formula->cal_RktOpex_Antisipasi($row);
		$row['ANTISIPASI'] = $antisipasi;
		$row['TIPE_TOTAL_BIAYA'] = 'OPEX_VRA';
		$total = $this->_formula->cal_RktOpex_Total($row);
		$distribusi_bulanan = $total['TOTAL_BIAYA'] / 12;
		$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
		
		$sql = "
			UPDATE TR_RKT_OPEX
			SET ACTUAL = REPLACE('".addslashes($row['ACTUAL'])."',',',''), 
				TAKSASI = REPLACE('".addslashes($row['TAKSASI'])."',',',''),
				ANTISIPASI = REPLACE('".addslashes($antisipasi)."',',',''), 
				PERSENTASE_INFLASI = REPLACE('".addslashes($total['PERSENTASE_INFLASI'])."',',',''), 
				TOTAL_BIAYA = REPLACE('".addslashes($total['TOTAL_BIAYA'])."',',',''), 
				DIS_JAN = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_FEB = REPLACE('".addslashes($distribusi_bulanan)."',',',''), 
				DIS_MAR = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_APR = REPLACE('".addslashes($distribusi_bulanan)."',',',''), 
				DIS_MAY = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_JUN = REPLACE('".addslashes($distribusi_bulanan)."',',',''), 
				DIS_JUL = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_AUG = REPLACE('".addslashes($distribusi_bulanan)."',',',''), 
				DIS_SEP = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_OCT = REPLACE('".addslashes($distribusi_bulanan)."',',',''), 
				DIS_NOV = REPLACE('".addslashes($distribusi_bulanan)."',',',''), DIS_DEC = REPLACE('".addslashes($distribusi_bulanan)."',',',''),
				COST_SMS1 = REPLACE('".addslashes($total['TOTAL_SMS1'])."',',',''), COST_SMS2 = REPLACE('".addslashes($total['TOTAL_SMS2'])."',',',''),
				KETERANGAN = '".addslashes($row['KETERANGAN'])."',
				FLAG_TEMP = NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".addslashes($row['PERIOD_BUDGET'])."'
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND COA_CODE = '".addslashes($row['COA_CODE'])."'
				AND GROUP_BUM_CODE = '".addslashes($row['GROUP_BUM_CODE'])."'
				AND TIPE_TRANSAKSI = 'VRA';
		";
        //create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
			
        return $result;
    }
	
	/*//simpan temp data-OLD
	public function saveTemp($row = array())
    { 
        $result = true;		
		try {
			$sql = "
				DELETE FROM TR_RKT_OPEX_TEMP
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND COA_CODE = '".addslashes($row['COA_CODE'])."'
					AND TIPE_TRANSAKSI = 'VRA'
			";
			$this->_db->query($sql);
			$this->_db->commit();			
			
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			$sql = "INSERT INTO TR_RKT_OPEX_TEMP (PERIOD_BUDGET, BA_CODE, REGION_CODE, COA_CODE, GROUP_BUM_CODE, ACTUAL, TAKSASI, ANTISIPASI, PERSENTASE_INFLASI, TOTAL_BIAYA, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, TIPE_TRANSAKSI, KETERANGAN, INSERT_USER, INSERT_TIME)
					VALUES (
							TO_DATE('{$this->_period}','DD-MM-RRRR'),
							'".addslashes($row['BA_CODE'])."',
							'".addslashes($region_code)."',
							'".addslashes($row['COA_CODE'])."',
							'".addslashes($row['GROUP_BUM_CODE'])."',
							REPLACE('".addslashes($row['ACTUAL'])."',',',''),
							REPLACE('".addslashes($row['TAKSASI'])."',',',''),
							REPLACE('".addslashes($row['ANTISIPASI'])."',',',''),
							REPLACE('".addslashes($row['PERSENTASE_INFLASI'])."',',',''),
							REPLACE('".addslashes($row['TOTAL_BIAYA'])."',',',''),
							REPLACE('".addslashes($row['DIS_JAN'])."',',',''),
							REPLACE('".addslashes($row['DIS_FEB'])."',',',''),
							REPLACE('".addslashes($row['DIS_MAR'])."',',',''),
							REPLACE('".addslashes($row['DIS_APR'])."',',',''),
							REPLACE('".addslashes($row['DIS_MAY'])."',',',''),
							REPLACE('".addslashes($row['DIS_JUN'])."',',',''),
							REPLACE('".addslashes($row['DIS_JUL'])."',',',''),
							REPLACE('".addslashes($row['DIS_AUG'])."',',',''),
							REPLACE('".addslashes($row['DIS_SEP'])."',',',''),
							REPLACE('".addslashes($row['DIS_OCT'])."',',',''),
							REPLACE('".addslashes($row['DIS_NOV'])."',',',''),
							REPLACE('".addslashes($row['DIS_DEC'])."',',',''),
							'VRA',
							'".addslashes($row['KETERANGAN'])."',
							'{$this->_userName}',
							SYSDATE
						)";				
			$this->_db->query($sql);
			$this->_db->commit();
			
			//DELETE DATA ORIGINAL 
			$sql = "
				DELETE FROM TR_RKT_OPEX
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND COA_CODE = '".addslashes($row['COA_CODE'])."'
					AND TIPE_TRANSAKSI = 'VRA'
			";
			$this->_db->query($sql);
			$this->_db->commit();
			
			
			//log DB
			$this->_global->insertLog('INSERT SUCCESS', 'TEMP - RKT OPEX VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('INSERT FAILED', 'TEMP - RKT OPEX VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}
        return $result;
    }*/
}

