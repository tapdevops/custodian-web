<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT OPEX
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: SID 10/07/2013	: ambil data dari DB
						- getDataDownload			: SID 10/07/2013	: ambil data untuk didownload ke excel dari DB
						- getList					: SID 10/07/2013	: menampilkan list RKT OPEX
						- save						: SID 10/07/2013	: simpan data
						- saveTemp					: SID 10/07/2013	: hapus data di RKT OPEX, insert inputan user di RKT OPEX
						- delete					: SID 10/07/2013	: hapus data
Disusun Oleh		: 	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT OPEX
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/07/2013
Update Terakhir		:	24/04/2015
Revisi				:	
	SID 30/06/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save, saveTemp, delete
						- perubahan mekanisme select di getData, getDataDownload
	NBU 27/04/2015 	: 	- Tutup delete data COA yang lama
						- penambahan code trx_code dengan random string
						- delete data sesuai transaksi code
=========================================================================================================================
*/
class Application_Model_RktOpex
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
	
	//setting input untuk region
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
				   rkt.FLAG_TEMP,
				   rkt.TRX_CODE,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   ORG.REGION_CODE,
				   rkt.BA_CODE,
				   ORG.COMPANY_NAME,
				   rkt.COA_CODE, 
				   coa.DESCRIPTION as COA_DESC,
				   rkt.GROUP_BUM_CODE, 
				   param.PARAMETER_VALUE as GROUP_BUM_DESCRIPTION,
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
				   (basic.PERCENT_INCREASE - 100) INFLASI_NASIONAL
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
				AND rkt.TIPE_TRANSAKSI = 'NON_VRA'
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
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(rkt.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.GROUP_BUM_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.GROUP_BUM_DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.KETERANGAN) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.COA_CODE, rkt.GROUP_BUM_CODE, rkt.KETERANGAN
		";
		
		//echo $query;//die();
		return $query;
	}
	
	//ambil data untuk didownload ke excel dari DB
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
				AND rkt.TIPE_TRANSAKSI = 'NON_VRA'
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
	
	//menampilkan list RKT OPEX
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
		
		$antisipasi = $this->_formula->cal_RktOpex_Antisipasi($row);
		$row['ANTISIPASI'] = $antisipasi;
		$total = $this->_formula->cal_RktOpex_Total($row);
		
		$sql = "
			UPDATE TR_RKT_OPEX 
			SET ANTISIPASI = REPLACE('".addslashes($antisipasi)."',',',''), 
				PERSENTASE_INFLASI = REPLACE('".addslashes($total['PERSENTASE_INFLASI'])."',',',''), 
				TOTAL_BIAYA = REPLACE('".addslashes($total['TOTAL_BIAYA'])."',',',''), 
				COST_SMS1 = REPLACE('".addslashes($total['TOTAL_SMS1'])."',',',''), 
				COST_SMS2 = REPLACE('".addslashes($total['TOTAL_SMS2'])."',',',''),
				FLAG_TEMP = NULL,
				KETERANGAN = '".addslashes($row['KETERANGAN'])."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE 
			WHERE TRX_CODE= '".addslashes($row['TRX_CODE'])."';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
        return $result;
    }
	
	//hapus data di RKT OPEX, insert inputan user di RKT OPEX
	public function saveTemp($row = array())
    { 
        $delCoa = ($row['COA_CODE']) ? $row['COA_CODE'] : '--';
		$delGroupBum = ($row['GROUP_BUM_CODE']) ? $row['GROUP_BUM_CODE'] : '--';
		
		$delOldCoa = ($row['OLD_COA_CODE']) ? $row['OLD_COA_CODE'] : '--';
		$delOldGroupBum = ($row['OLD_GROUP_BUM_CODE']) ? $row['OLD_GROUP_BUM_CODE'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_OPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND TIPE_TRANSAKSI = 'NON_VRA'
				AND NVL(COA_CODE ,'--') = '{$delCoa}' 
				AND NVL(GROUP_BUM_CODE,'--')  = '{$delGroupBum}'
				AND NVL(TRX_CODE, '--') = '{$row['TRX_CODE']}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_OPEX
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND TIPE_TRANSAKSI = 'NON_VRA'
				AND NVL(COA_CODE ,'--') = '{$delOldCoa}' 
				AND NVL(GROUP_BUM_CODE,'--')  = '{$delOldGroupBum}'
				AND NVL(TRX_CODE, '--') = '{$row['TRX_CODE']}';
		";
		
		//insert data input baru sebagai temp data
		$trx_code = $row['PERIOD_BUDGET'] ."-".
					addslashes($row['BA_CODE']) ."-RKT017-".
					addslashes($row['COA_CODE']) ."-".
					$this->_global->randomString(addslashes($row['GROUP_BUM_CODE']));
		$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
		
					
		$sql.= "
			INSERT INTO TR_RKT_OPEX (
				TRX_CODE, 
				PERIOD_BUDGET, 
				REGION_CODE, 
				BA_CODE, 
				COA_CODE, 
				GROUP_BUM_CODE, 
				TIPE_TRANSAKSI, 
				ACTUAL, 
				TAKSASI, 
				ANTISIPASI, 
				PERSENTASE_INFLASI, 
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
				COST_SMS1, 
				COST_SMS2, 
				TOTAL_BIAYA,
				KETERANGAN, 
				FLAG_TEMP, 
				INSERT_USER, 
				INSERT_TIME
			)
			VALUES (
				'".$trx_code."',
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
				'".$region_code."',
				'".addslashes($row['BA_CODE'])."',
				'".addslashes($row['COA_CODE'])."',
				'".addslashes($row['GROUP_BUM_CODE'])."',
				'NON_VRA', 
				REPLACE('".addslashes($row['ACTUAL'])."',',',''), 
				REPLACE('".addslashes($row['TAKSASI'])."',',',''), 
				REPLACE('".addslashes($row['ANTISIPASI'])."',',',''), 
				REPLACE('".addslashes($row['PERSENTASE_INFLASI'])."',',',''), 
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
				NULL,
				NULL,
				NULL,
				'".addslashes($row['KETERANGAN'])."', 
				'Y',
				'{$this->_userName}',
				SYSDATE
			);
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	//hapus data
	public function delete($row = array())
    {
		$delCoa = ($row['COA_CODE']) ? $row['COA_CODE'] : '--';
		$delGroupBum = ($row['GROUP_BUM_CODE']) ? $row['GROUP_BUM_CODE'] : '--';
		
		$delOldCoa = ($row['OLD_COA_CODE']) ? $row['OLD_COA_CODE'] : '--';
		$delOldGroupBum = ($row['OLD_GROUP_BUM_CODE']) ? $row['OLD_GROUP_BUM_CODE'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		/*
		$sql = "
			UPDATE TR_RKT_OPEX
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND TIPE_TRANSAKSI = 'NON_VRA'
				AND NVL(COA_CODE ,'--') = '{$delCoa}' 
				AND NVL(GROUP_BUM_CODE,'--')  = '{$delGroupBum}';
		";
		*/
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			UPDATE TR_RKT_OPEX
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND TIPE_TRANSAKSI = 'NON_VRA'
				AND NVL(COA_CODE ,'--') = '{$delOldCoa}' 
				AND NVL(GROUP_BUM_CODE,'--')  = '{$delOldGroupBum}'
				AND NVL(TRX_CODE,'--') = '{$row['TRX_CODE']}';
		";
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
}

