<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Panen Loading
Function 			:	- getList					: menampilkan list norma Panen Loading
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/06/2013
Update Terakhir		:	25/06/2013
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save dan save temp
YULIUS 16/07/2014	:	- set field FLAG_TEMP pada function getData, save, saveTemp	
=========================================================================================================================
*/
class Application_Model_NormaPanenLoading
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
			SELECT ROWIDTOCHAR (norma.ROWID) row_id,
				   ROWNUM,
				   TO_CHAR (norma.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
				   norma.BA_CODE,
				   norma.JARAK_PKS_MIN, 
				   norma.JARAK_PKS_MAX, 
				   norma.TARGET_ANGKUT_TM_SUPIR, 
				   norma.BASIS_TM_SUPIR, 
				   norma.JUMLAH_TM, 
				   norma.SELISIH_TM, 
				   norma.TARIF_TM, 
				   NVL(checkroll.RP_HK, 0) as RP_HK_TM, 
				   norma.RP_BASIS_TM,
				   norma.RP_KG_BASIS_TM, 
				   norma.RP_PREMI_TM, 
				   norma.RP_KG_PREMI_TM, 
				   norma.TARIF_SUPIR, 
				   norma.RP_PREMI_SUPIR, 
				   norma.RP_KG_PREMI_SUPIR,
				   norma.FLAG_TEMP
			FROM TN_PANEN_LOADING norma
			LEFT JOIN TR_RKT_CHECKROLL_SUM checkroll
				ON norma.PERIOD_BUDGET = checkroll.PERIOD_BUDGET
				AND norma.BA_CODE = checkroll.BA_CODE
				AND checkroll.JOB_CODE = 'FW041'
			LEFT JOIN TM_ORGANIZATION ORG
				  ON norma.BA_CODE = ORG.BA_CODE
			WHERE norma.DELETE_USER IS NULL
		 ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$this->_siteCode."%')";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER(norma.BA_CODE) LIKE UPPER('%".$this->_siteCode."%')";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(norma.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JARAK_PKS_MIN) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JARAK_PKS_MAX) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TARGET_ANGKUT_TM_SUPIR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BASIS_TM_SUPIR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.JUMLAH_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.SELISIH_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TARIF_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_HK_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_BASIS_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_BASIS_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_PREMI_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_PREMI_TM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TARIF_SUPIR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_PREMI_SUPIR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_PREMI_SUPIR) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.JARAK_PKS_MIN, norma.JARAK_PKS_MAX
		";
		
		return $query;
	}
	
	//menampilkan list norma Panen Loading
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
		
		$selisih_tm = $this->_formula->cal_NormaPanenLoading_SelisihBasis($row);
		$row['SELISIH_BASIS'] = $selisih_tm;
		$rp_basis_tm = $this->_formula->cal_NormaPanenLoading_RpBasisTukangMuat($row);
		$row['RUPIAH_BASIS_TM'] = $rp_basis_tm;
		$rp_kg_basis_tm = $this->_formula->cal_NormaPanenLoading_RpKgBasisTukangMuat($row);
		$rp_premi_tm = $this->_formula->cal_NormaPanenLoading_RpPremiTukangMuat($row);
		$row['RP_PREMI_TM'] = $rp_premi_tm;
		$rp_kg_premi_tm = $this->_formula->cal_NormaPanenLoading_RpKgPremiTukangMuat($row);
		$rp_premi_supir = $this->_formula->cal_NormaPanenLoading_RpPremiSupir($row);
		$row['RP_PREMI_SUPIR'] = $rp_premi_supir;
		$rp_kg_premi_supir = $this->_formula->cal_NormaPanenLoading_RpKgPremiSupir($row);
			
		
			$sql = "UPDATE TN_PANEN_LOADING
					SET TARGET_ANGKUT_TM_SUPIR = REPLACE('".addslashes($row['TARGET_ANGKUT_TM_SUPIR'])."', ',', ''),
						BASIS_TM_SUPIR = REPLACE('".addslashes($row['BASIS_TM_SUPIR'])."', ',', ''),
						JUMLAH_TM = REPLACE('".addslashes($row['JUMLAH_TM'])."', ',', ''),
						SELISIH_TM = REPLACE('".addslashes($selisih_tm)."', ',', ''),
						TARIF_TM = REPLACE('".addslashes($row['TARIF_TM'])."', ',', ''),
						RP_HK_TM = REPLACE('".addslashes($row['RP_HK_TM'])."', ',', ''),
						RP_BASIS_TM = REPLACE('".addslashes($rp_basis_tm)."', ',', ''),
						RP_KG_BASIS_TM = REPLACE('".addslashes($rp_kg_basis_tm)."', ',', ''),
						RP_PREMI_TM = REPLACE('".addslashes($rp_premi_tm)."', ',', ''),
						RP_KG_PREMI_TM = REPLACE('".addslashes($rp_kg_premi_tm)."', ',', ''),
						TARIF_SUPIR = REPLACE('".addslashes($row['TARIF_SUPIR'])."', ',', ''),
						RP_PREMI_SUPIR = REPLACE('".addslashes($rp_premi_supir)."', ',', ''),
						RP_KG_PREMI_SUPIR = REPLACE('".addslashes($rp_kg_premi_supir)."', ',', ''),
						FLAG_TEMP=NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					 ";
					 
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	public function saveTemp($row = array())
    { 
		$selisih_tm = $this->_formula->cal_NormaPanenLoading_SelisihBasis($row);
		$row['SELISIH_BASIS'] = $selisih_tm;
		$rp_basis_tm = $this->_formula->cal_NormaPanenLoading_RpBasisTukangMuat($row);
		$row['RUPIAH_BASIS_TM'] = $rp_basis_tm;
		$rp_kg_basis_tm = $this->_formula->cal_NormaPanenLoading_RpKgBasisTukangMuat($row);
		$rp_premi_tm = $this->_formula->cal_NormaPanenLoading_RpPremiTukangMuat($row);
		$row['RP_PREMI_TM'] = $rp_premi_tm;
		$rp_kg_premi_tm = $this->_formula->cal_NormaPanenLoading_RpKgPremiTukangMuat($row);
		$rp_premi_supir = $this->_formula->cal_NormaPanenLoading_RpPremiSupir($row);
		$row['RP_PREMI_SUPIR'] = $rp_premi_supir;
		$rp_kg_premi_supir = $this->_formula->cal_NormaPanenLoading_RpKgPremiSupir($row);
		
		$sql = "UPDATE TN_PANEN_LOADING
					SET TARGET_ANGKUT_TM_SUPIR = REPLACE('".addslashes($row['TARGET_ANGKUT_TM_SUPIR'])."', ',', ''),
						BASIS_TM_SUPIR = REPLACE('".addslashes($row['BASIS_TM_SUPIR'])."', ',', ''),
						JUMLAH_TM = REPLACE('".addslashes($row['JUMLAH_TM'])."', ',', ''),
						SELISIH_TM = REPLACE('".addslashes($selisih_tm)."', ',', ''),
						TARIF_TM = REPLACE('".addslashes($row['TARIF_TM'])."', ',', ''),
						RP_HK_TM = REPLACE('".addslashes($row['RP_HK_TM'])."', ',', ''),
						RP_BASIS_TM = REPLACE('".addslashes($rp_basis_tm)."', ',', ''),
						RP_KG_BASIS_TM = REPLACE('".addslashes($rp_kg_basis_tm)."', ',', ''),
						RP_PREMI_TM = REPLACE('".addslashes($rp_premi_tm)."', ',', ''),
						RP_KG_PREMI_TM = REPLACE('".addslashes($rp_kg_premi_tm)."', ',', ''),
						TARIF_SUPIR = REPLACE('".addslashes($row['TARIF_SUPIR'])."', ',', ''),
						RP_PREMI_SUPIR = REPLACE('".addslashes($rp_premi_supir)."', ',', ''),
						RP_KG_PREMI_SUPIR = REPLACE('".addslashes($rp_kg_premi_supir)."', ',', ''),
						FLAG_TEMP = 'Y',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					 ";
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);		
        return true;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE RKT PANEN **********************************************
		//reset param
		$param = array();
		
		$model = new Application_Model_RktPanen();
			
		//set parameter sesuai data yang diupdate
		$param['key_find'] = $row['BA_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {		
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - LOADING');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF UPDATE RKT PANEN **********************************************
		
		return $result;
	}
	
	//kalkulasi data saat upload
	public function calculateData($row = array())
    { 
        $selisih_tm = $this->_formula->cal_NormaPanenLoading_SelisihBasis($row);
		$row['SELISIH_BASIS'] = $selisih_tm;
		$rp_basis_tm = $this->_formula->cal_NormaPanenLoading_RpBasisTukangMuat($row);
		$row['RUPIAH_BASIS_TM'] = $rp_basis_tm;
		$rp_kg_basis_tm = $this->_formula->cal_NormaPanenLoading_RpKgBasisTukangMuat($row);
		$rp_premi_tm = $this->_formula->cal_NormaPanenLoading_RpPremiTukangMuat($row);
		$row['RP_PREMI_TM'] = $rp_premi_tm;
		$rp_kg_premi_tm = $this->_formula->cal_NormaPanenLoading_RpKgPremiTukangMuat($row);
		$rp_premi_supir = $this->_formula->cal_NormaPanenLoading_RpPremiSupir($row);
		$row['RP_PREMI_SUPIR'] = $rp_premi_supir;
		$rp_kg_premi_supir = $this->_formula->cal_NormaPanenLoading_RpKgPremiSupir($row);
			
		
		$sql = "
			UPDATE TN_PANEN_LOADING
			SET TARGET_ANGKUT_TM_SUPIR = REPLACE('".addslashes($row['TARGET_ANGKUT_TM_SUPIR'])."', ',', ''),
				BASIS_TM_SUPIR = REPLACE('".addslashes($row['BASIS_TM_SUPIR'])."', ',', ''),
				JUMLAH_TM = REPLACE('".addslashes($row['JUMLAH_TM'])."', ',', ''),
				SELISIH_TM = REPLACE('".addslashes($selisih_tm)."', ',', ''),
				TARIF_TM = REPLACE('".addslashes($row['TARIF_TM'])."', ',', ''),
				RP_HK_TM = REPLACE('".addslashes($row['RP_HK_TM'])."', ',', ''),
				RP_BASIS_TM = REPLACE('".addslashes($rp_basis_tm)."', ',', ''),
				RP_KG_BASIS_TM = REPLACE('".addslashes($rp_kg_basis_tm)."', ',', ''),
				RP_PREMI_TM = REPLACE('".addslashes($rp_premi_tm)."', ',', ''),
				RP_KG_PREMI_TM = REPLACE('".addslashes($rp_kg_premi_tm)."', ',', ''),
				TARIF_SUPIR = REPLACE('".addslashes($row['TARIF_SUPIR'])."', ',', ''),
				RP_PREMI_SUPIR = REPLACE('".addslashes($rp_premi_supir)."', ',', ''),
				RP_KG_PREMI_SUPIR = REPLACE('".addslashes($rp_kg_premi_supir)."', ',', ''),
				FLAG_TEMP=NULL,
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
		";
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
}

