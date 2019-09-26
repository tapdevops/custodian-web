<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT CAPEX
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region dan COA
						- getData					: SID 08/07/2013	: ambil data dari DB
						- getList					: SID 08/07/2013	: menampilkan list RKT CAPEX
						- getDataDownload			: SID 08/07/2013	: ambil data untuk didownload ke excel dari DB
						- getUrgencyCapex			: SID 08/07/2013	: get curency capex
						- save						: SID 08/07/2013	: simpan data
						- saveTemp					: SID 08/07/2013	: simpan inputan user
						- delete					: SID 08/07/2013	: hapus data
						- getMinPriceCapex			: SID 08/07/2013	: get min price capex
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/07/2013
Update Terakhir		:	11/07/2014
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktCapex
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
	
	//setting input untuk region dan COA
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
		
		$options['optCoaCapex'] = $table->getCoaCapex();
		$result['src_coa_code'] = array(
            'type'    => 'select',
            'name'    => 'src_coa_code',
            'value'   => '',
            'options' => $options['optCoaCapex'],
            'ext'     => '',
			'style'   => 'width:200px;background-color: #e6ffc8;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
				SELECT *
				FROM (
					SELECT 	ROWNUM,
							ROWIDTOCHAR (rkt.ROWID) row_id,
							RKT.TRX_CODE,
							TO_CHAR (RKT.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
							ORG.REGION_CODE,
							rkt.BA_CODE,
							rkt.COA_CODE,
							coa.DESCRIPTION AS COA_DESC,
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
							rkt.DIS_BIAYA_TOTAL,
							rkt.FLAG_TEMP
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
				) RKT
				WHERE 1 = 1
          
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND  UPPER('".$this->_siteCode."')  LIKE '%'||UPPER(RKT.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE   '%'||UPPER(rkt.BA_CODE)||'%' ";
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
                AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(RKT.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
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
			$params['search'] = ($params['search'] == 'AA==') ? "" : base64_decode($params['search']);
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(rkt.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.ASSET_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.ASSET_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.DETAIL_SPESIFICATION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.UOM) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.PERIOD_BUDGET, rkt.BA_CODE, rkt.COA_CODE, rkt.ASSET_CODE, rkt.DETAIL_SPESIFICATION
		";
		
		//die($query);
		return $query;
	}
	
	//ambil data yang dapat didownload dari DB
    public function getDataDownload($params = array())
    {
		$query = "
			  SELECT ROWNUM, 
					 ROWIDTOCHAR (rkt.ROWID) row_id,
					 TO_CHAR (RKT.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
					 ORG.REGION_CODE,
					 rkt.BA_CODE,
					 ORG.COMPANY_NAME,
					 rkt.COA_CODE,
					 coa.DESCRIPTION AS COA_DESC,
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
          
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RKT.REGION_CODE)||'%'";
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
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(RKT.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_coa_code'] != '' && $params['src_coa_code'] != '0') {
			$query .= "
                AND UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : base64_decode($params['search']);
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
			ORDER BY rkt.PERIOD_BUDGET, rkt.BA_CODE, rkt.COA_CODE, rkt.ASSET_CODE, rkt.DETAIL_SPESIFICATION
		";
		return $query;
	}
	
	//menampilkan list RKT CAPEX
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
	
	//get curency capex
    public function getUrgencyCapex($params = array())
    {
        $result = array();

        $sql = "SELECT PARAMETER_VALUE_CODE KODE, PARAMETER_VALUE NILAI FROM T_PARAMETER_VALUE WHERE PARAMETER_CODE = 'CAPEX_URGENCY'";
        
        $rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {				
					$result['rows'][] = $row;
			}
        }
		$result['count'] = count($rows);
		

        return $result;
    }
	
	//simpan data
	public function save($row = array())
    { 
		$delCoa = ($row['COA_CODE']) ? $row['COA_CODE'] : '--';
		$delAsset = ($row['ASSET_CODE']) ? $row['ASSET_CODE'] : '--';
		$delDetailSpec = ($row['DETAIL_SPESIFICATION']) ? $row['DETAIL_SPESIFICATION'] : '--';
		
		$delOldCoa = ($row['OLD_COA_CODE']) ? $row['OLD_COA_CODE'] : '--';
		$delOldAsset = ($row['OLD_ASSET_CODE']) ? $row['OLD_ASSET_CODE'] : '--';
		$delOldDetailSpec = ($row['OLD_DETAIL_SPESIFICATION']) ? $row['OLD_DETAIL_SPESIFICATION'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_CAPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(COA_CODE ,'--') = '{$delCoa}' 
				AND NVL(ASSET_CODE,'--')  = '{$delAsset}'
				AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delDetailSpec}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_CAPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(COA_CODE ,'--') = '{$delOldCoa}' 
				AND NVL(ASSET_CODE,'--')  = '{$delOldAsset}'
				AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delOldDetailSpec}';
		";
		
		//insert data input baru
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-RKT022-".
					addslashes($row['COA_CODE']) ."-".
					addslashes($row['ASSET_CODE']) ."-".
					$this->_global->randomString(10);
		$distribusi = $this->_formula->cal_RktCapex_DistribusiTahunBerjalan($row);		
		$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
					
		$sql.= "
			INSERT INTO TR_RKT_CAPEX (
				TRX_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				REGION_CODE, 
				COA_CODE, 
				ASSET_CODE, 
				DETAIL_SPESIFICATION, 
				URGENCY_CAPEX, 
				PRICE, 
				QTY_ACTUAL, 
				DIS_TAHUN_BERJALAN, 
				DIS_JAN, 
				DIS_FEB, 
				DIS_MAR, 
				DIS_APR, 
				DIS_MAY, 
				DIS_JUN, 
				DIS_JUL, 
				DIS_AUG, 
				DIS_SEP, 
				DIS_OCT, 
				DIS_NOV, 
				DIS_DEC, 
				DIS_BIAYA_JAN, 
				DIS_BIAYA_FEB, 
				DIS_BIAYA_MAR, 
				DIS_BIAYA_APR, 
				DIS_BIAYA_MAY, 
				DIS_BIAYA_JUN, 
				DIS_BIAYA_JUL, 
				DIS_BIAYA_AUG, 
				DIS_BIAYA_SEP, 
				DIS_BIAYA_OCT, 
				DIS_BIAYA_NOV, 
				DIS_BIAYA_DEC, 
				DIS_BIAYA_TOTAL, 
				COST_SMS1, 
				COST_SMS2, 
				FLAG_TEMP,
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".$trx_code."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',		
				'".addslashes($region_code)."',
				'".addslashes($row['COA_CODE'])."',
				'".addslashes($row['ASSET_CODE'])."',
				'".addslashes($row['DETAIL_SPESIFICATION'])."', 
				'".addslashes($row['URGENCY_CAPEX'])."', 
				REPLACE('".addslashes($row['PRICE'])."',',',''), 
				REPLACE('".addslashes($row['QTY_ACTUAL'])."',',',''), 
				REPLACE('".addslashes($distribusi['TOTAL_DISTRIBUSI'])."',',',''), 
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
				REPLACE('".addslashes($distribusi['DIS_JAN'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_FEB'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_MAR'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_APR'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_MAY'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_JUN'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_JUL'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_AUG'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_SEP'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_OCT'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_NOV'])."',',',''), 
				REPLACE('".addslashes($distribusi['DIS_DEC'])."',',',''), 
				REPLACE('".addslashes($distribusi['TOTAL_BIAYA'])."',',',''), 
				REPLACE('".addslashes($distribusi['TOTAL_SMS1'])."',',',''), 
				REPLACE('".addslashes($distribusi['TOTAL_SMS2'])."',',',''),
				NULL,
				'{$this->_userName}',
				SYSDATE
			);
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//simpan inputan user
	public function saveTemp($row = array())
    { 
        $delCoa = ($row['COA_CODE']) ? $row['COA_CODE'] : '--';
		$delAsset = ($row['ASSET_CODE']) ? $row['ASSET_CODE'] : '--';
		$delDetailSpec = ($row['DETAIL_SPESIFICATION']) ? $row['DETAIL_SPESIFICATION'] : '--';
		
		$delOldCoa = ($row['OLD_COA_CODE']) ? $row['OLD_COA_CODE'] : '--';
		$delOldAsset = ($row['OLD_ASSET_CODE']) ? $row['OLD_ASSET_CODE'] : '--';
		$delOldDetailSpec = ($row['OLD_DETAIL_SPESIFICATION']) ? $row['OLD_DETAIL_SPESIFICATION'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_CAPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(COA_CODE ,'--') = '{$delCoa}' 
				AND NVL(ASSET_CODE,'--')  = '{$delAsset}'
				AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delDetailSpec}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_CAPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(COA_CODE ,'--') = '{$delOldCoa}' 
				AND NVL(ASSET_CODE,'--')  = '{$delOldAsset}'
				AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delOldDetailSpec}';
		";
		
		//insert data input baru
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-RKT022-".
					addslashes($row['COA_CODE']) ."-".
					addslashes($row['ASSET_CODE']) ."-".
					$this->_global->randomString(10);
		$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
					
		$sql.= "
			INSERT INTO TR_RKT_CAPEX (
				TRX_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				REGION_CODE, 
				COA_CODE, 
				ASSET_CODE, 
				DETAIL_SPESIFICATION, 
				URGENCY_CAPEX, 
				PRICE, 
				QTY_ACTUAL, 
				DIS_JAN, 
				DIS_FEB, 
				DIS_MAR, 
				DIS_APR, 
				DIS_MAY, 
				DIS_JUN, 
				DIS_JUL, 
				DIS_AUG, 
				DIS_SEP, 
				DIS_OCT, 
				DIS_NOV, 
				DIS_DEC, 
				FLAG_TEMP,
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".$trx_code."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',		
				'".addslashes($region_code)."',
				'".addslashes($row['COA_CODE'])."',
				'".addslashes($row['ASSET_CODE'])."',
				'".addslashes($row['DETAIL_SPESIFICATION'])."', 
				'".addslashes($row['URGENCY_CAPEX'])."', 
				REPLACE('".addslashes($row['PRICE'])."',',',''), 
				REPLACE('".addslashes($row['QTY_ACTUAL'])."',',',''), 
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
				'Y',
				'{$this->_userName}',
				SYSDATE
			);
		";
						
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);

        return $result;
    }
	
	//hapus data
	public function delete($row = array())
    {
		$delCoa = ($row['COA_CODE']) ? $row['COA_CODE'] : '--';
		$delAsset = ($row['ASSET_CODE']) ? $row['ASSET_CODE'] : '--';
		$delDetailSpec = ($row['DETAIL_SPESIFICATION']) ? $row['DETAIL_SPESIFICATION'] : '--';
		
		$delOldCoa = ($row['OLD_COA_CODE']) ? $row['OLD_COA_CODE'] : '--';
		$delOldAsset = ($row['OLD_ASSET_CODE']) ? $row['OLD_ASSET_CODE'] : '--';
		$delOldDetailSpec = ($row['OLD_DETAIL_SPESIFICATION']) ? $row['OLD_DETAIL_SPESIFICATION'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		/*
		$sql = "
			UPDATE TR_RKT_CAPEX
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND NVL(COA_CODE ,'--') = '{$delCoa}' 
				AND NVL(ASSET_CODE,'--')  = '{$delAsset}'
				AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delDetailSpec}';
		";
		*/
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql= "
			UPDATE TR_RKT_CAPEX
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND TRX_CODE = '{$row['TRX_CODE']}'
				--AND NVL(COA_CODE ,'--') = '{$delOldCoa}' 
				--AND NVL(ASSET_CODE,'--')  = '{$delOldAsset}'
				--AND NVL(DETAIL_SPESIFICATION ,'--') = '{$delOldDetailSpec}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//get min price capex
	public function getMinPriceCapex()
    {
        $sql = "
			SELECT PARAMETER_VALUE
			FROM T_PARAMETER_VALUE
			WHERE DELETE_USER IS NULL
				AND PARAMETER_VALUE_CODE = 'MIN_CAPEX'
				AND PARAMETER_CODE = 'MAX_PRICE'
		";
		$result = $this->_db->fetchOne($sql);
        return $result;
    }
}

