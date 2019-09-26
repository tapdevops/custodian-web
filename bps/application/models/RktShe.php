<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk RKT SHE
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region dan COA
						- getData					: SID 15/07/2013	: ambil data dari DB
						- getDataDownload			: SID 15/07/2013	: ambil data untuk didownload ke excel dari DB
						- getList					: SID 15/07/2013	: menampilkan list RKT SHE
						- save						: SID 15/07/2013	: simpan data
						- saveTemp					: SID 15/07/2013	: hapus data di RKT SHE, insert inputan user di RKT SHE
						- delete					: SID 15/07/2013	: hapus data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	15/07/2013
Update Terakhir		:	24/06/2014
Revisi				:	
	SID 24/06/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save, delete
						- perubahan mekanisme select di getData, getDataDownload
=========================================================================================================================
*/
class Application_Model_RktShe
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
		
		$options['optCoa'] = $table->getCoaRelation('she');

        // elements
		$result['src_coa_code_pre'] = array(
            'type'    => 'select',
            'name'    => 'src_coa_code_pre',
            'value'   => '',
            'options' => $options['optCoa'],
            'ext'     => 'onChange=\'var myarr = $("#src_coa_code_pre option:selected").text().split(" : "); 
						  $("#src_coa_code").val(myarr[0]); 
						  $("#src_coa").val(myarr[1]);  
						  $("#src_group_code").val(myarr[2]);
						  $("#src_group_desc").val(myarr[3]);\'',
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
				   TRX_CODE,
				   TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   ORG.REGION_CODE,
				   rkt.BA_CODE,
				   rkt.COA_CODE, 
				   coa.DESCRIPTION as COA_DESC,
				   rkt.GROUP_CODE, 
				   param.PARAMETER_VALUE GROUP_DESC,
				   rkt.SUB_GROUP_CODE, 
				   master_relation.SUB_GROUP_DESC,
				   rkt.ACTIVITY_DETAIL, 
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
				   rkt.FLAG_TEMP
			FROM TR_RKT_RELATION rkt
			LEFT JOIN TM_COA coa
				ON rkt.COA_CODE = coa.COA_CODE
			LEFT JOIN T_PARAMETER_VALUE param
				ON rkt.GROUP_CODE = param.PARAMETER_VALUE_CODE
				AND param.PARAMETER_CODE = 'GROUP_SHE'
			LEFT JOIN TM_GROUP_RELATION master_relation
				ON rkt.COA_CODE = master_relation.COA_CODE
				AND rkt.GROUP_CODE = master_relation.GROUP_CODE
				AND rkt.SUB_GROUP_CODE = master_relation.SUB_GROUP_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON rkt.BA_CODE = ORG.BA_CODE
			WHERE rkt.DELETE_USER IS NULL
				AND rkt.REPORT_TYPE = 'SHE'
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
		
		if ($params['src_coa_code'] != '') {
			$query .= "
                AND UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
            ";
        }
		
		if ($params['src_group_code'] != '') {
			$query .= "
                AND UPPER(rkt.GROUP_CODE) LIKE UPPER('%".$params['src_group_code']."%')
            ";
        }
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(rkt.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.GROUP_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(param.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.SUB_GROUP_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(master_relation.SUB_GROUP_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.ACTIVITY_DETAIL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.KETERANGAN) LIKE UPPER('%".$params['search']."%')
				)";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.GROUP_CODE, rkt.COA_CODE, rkt.SUB_GROUP_CODE
		";
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
				   rkt.GROUP_CODE, 
				   param.PARAMETER_VALUE GROUP_DESC,
				   rkt.SUB_GROUP_CODE, 
				   master_relation.SUB_GROUP_DESC,
				   rkt.ACTIVITY_DETAIL, 
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
			FROM TR_RKT_RELATION rkt
			LEFT JOIN TM_COA coa
				ON rkt.COA_CODE = coa.COA_CODE
			LEFT JOIN T_PARAMETER_VALUE param
				ON rkt.GROUP_CODE = param.PARAMETER_VALUE_CODE
				AND param.PARAMETER_CODE = 'GROUP_SHE'
			LEFT JOIN TM_GROUP_RELATION master_relation
				ON rkt.COA_CODE = master_relation.COA_CODE
				AND rkt.GROUP_CODE = master_relation.GROUP_CODE
				AND rkt.SUB_GROUP_CODE = master_relation.SUB_GROUP_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON rkt.BA_CODE = ORG.BA_CODE
			WHERE rkt.DELETE_USER IS NULL
				AND rkt.REPORT_TYPE = 'SHE'
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
		
		if ($params['src_coa_code'] != '') {
			$query .= "
                AND UPPER(rkt.COA_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
            ";
        }
		
		if ($params['src_group_code'] != '') {
			$query .= "
                AND UPPER(rkt.GROUP_CODE) LIKE UPPER('%".$params['src_group_code']."%')
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
					OR UPPER(rkt.GROUP_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(param.PARAMETER_VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.SUB_GROUP_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(master_relation.SUB_GROUP_DESC) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.ACTIVITY_DETAIL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(rkt.KETERANGAN) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY rkt.BA_CODE, rkt.GROUP_CODE, rkt.COA_CODE, rkt.SUB_GROUP_CODE
		";
		
		return $query;
	}
	
	//menampilkan list RKT SHE
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
        $total_biaya = $this->_formula->cal_RktRelation_TotalBiaya($row);
					
		$sql.= "
			UPDATE TR_RKT_RELATION
			SET COST_SMS1 = REPLACE('".addslashes($total_biaya['TOTAL_SMS1'])."',',',''), 
				COST_SMS2 = REPLACE('".addslashes($total_biaya['TOTAL_SMS2'])."',',',''), 
				TOTAL_BIAYA = REPLACE('".addslashes($total_biaya['TOTAL_BIAYA'])."',',',''),
				FLAG_TEMP = NULL, 
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE TRX_CODE = '".addslashes($row['TRX_CODE'])."';
		";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	
	//simpan temp data
	public function saveTemp($row = array())
    { 
        $delSubGroup = ($row['SUB_GROUP_CODE']) ? $row['SUB_GROUP_CODE'] : '--';
		$delActivity = ($row['ACTIVITY_DETAIL']) ? $row['ACTIVITY_DETAIL'] : '--';
		
		$delOldSubGroup = ($row['OLD_SUB_GROUP_CODE']) ? $row['OLD_SUB_GROUP_CODE'] : '--';
		$delOldActivity = ($row['OLD_ACTIVITY_DETAIL']) ? $row['OLD_ACTIVITY_DETAIL'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		$sql = "
			DELETE FROM TR_RKT_RELATION
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND COA_CODE = '{$row['COA_CODE']}'
				AND GROUP_CODE = '{$row['GROUP_CODE']}'
				AND NVL(SUB_GROUP_CODE ,'--') = '{$delSubGroup}' 
				AND NVL(ACTIVITY_DETAIL,'--')  = '{$delActivity}';
		";
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			DELETE FROM TR_RKT_RELATION
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND COA_CODE = '{$row['COA_CODE']}'
				AND GROUP_CODE = '{$row['GROUP_CODE']}'
				AND NVL(SUB_GROUP_CODE ,'--') = '{$delOldSubGroup}' 
				AND NVL(ACTIVITY_DETAIL,'--')  = '{$delOldActivity}';
		";
		
		//insert data input baru sebagai temp data
		$trx_code = substr($this->_period, -4) ."-".
					addslashes($row['BA_CODE']) ."-RKT020-".
					addslashes($row['COA_CODE']) ."-".
					addslashes($row['GROUP_CODE']) ."-".
					addslashes($row['SUB_GROUP_CODE']) ."-".
					$this->_global->randomString(5);
					
		$sql.= "
			INSERT INTO TR_RKT_RELATION (
				TRX_CODE, 
				PERIOD_BUDGET, 
				BA_CODE, 
				REPORT_TYPE, 
				COA_CODE, 
				GROUP_CODE, 
				SUB_GROUP_CODE, 
				ACTIVITY_DETAIL, 
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
				TO_DATE('{$this->_period}','DD-MM-RRRR'),		
				'".addslashes($row['BA_CODE'])."',
				UPPER('SHE'),	
				'".addslashes($row['COA_CODE'])."',
				'".addslashes($row['GROUP_CODE'])."',
				'".addslashes($row['SUB_GROUP_CODE'])."', 
				'".addslashes($row['ACTIVITY_DETAIL'])."', 
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
		$delSubGroup = ($row['SUB_GROUP_CODE']) ? $row['SUB_GROUP_CODE'] : '--';
		$delActivity = ($row['ACTIVITY_DETAIL']) ? $row['ACTIVITY_DETAIL'] : '--';
		
		$delOldSubGroup = ($row['OLD_SUB_GROUP_CODE']) ? $row['OLD_SUB_GROUP_CODE'] : '--';
		$delOldActivity = ($row['OLD_ACTIVITY_DETAIL']) ? $row['OLD_ACTIVITY_DETAIL'] : '--';
		
		//delete data berdasarkan kombinasi PK yg baru
		/*
		$sql = "
			UPDATE TR_RKT_RELATION
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND COA_CODE = '{$row['COA_CODE']}'
				AND GROUP_CODE = '{$row['GROUP_CODE']}'
				AND NVL(SUB_GROUP_CODE ,'--') = '{$delSubGroup}' 
				AND NVL(ACTIVITY_DETAIL,'--')  = '{$delActivity}';
		";
		*/
		
		//delete data berdasarkan kombinasi PK yg lama
		$sql.= "
			UPDATE TR_RKT_RELATION
			SET DELETE_USER = '{$this->_userName}',
				DELETE_TIME = SYSDATE
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$row['BA_CODE']}'
				AND COA_CODE = '{$row['COA_CODE']}'
				AND GROUP_CODE = '{$row['GROUP_CODE']}'
				AND NVL(SUB_GROUP_CODE ,'--') = '{$delOldSubGroup}' 
				AND NVL(ACTIVITY_DETAIL,'--')  = '{$delOldActivity}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
}

