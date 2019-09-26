<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Panen Krani Buah
Function 			:	- getList					: menampilkan list norma Panen Krani Buah
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save dan save temp
=========================================================================================================================
*/
class Application_Model_NormaPanenKraniBuah
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
				   (SELECT target.VALUE
					FROM TN_PANEN_VARIABLE target
					WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
					AND target.BA_CODE = norma.BA_CODE
					AND target.PANEN_CODE = 'TGT_KRANI_BUAH') as TARGET, 
				   (SELECT target.VALUE
					FROM TN_PANEN_VARIABLE target
					WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
					AND target.BA_CODE = norma.BA_CODE
					AND target.PANEN_CODE = 'BAS_KRANI_BUAH') as BASIS, 
				   (SELECT target.VALUE
					FROM TN_PANEN_VARIABLE target
					WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
					AND target.BA_CODE = norma.BA_CODE
					AND target.PANEN_CODE = 'TRF_OB_KRANI_BUAH') as TARIF_BASIS,
				   norma.SELISIH_OVER_BASIS, 
				   norma.RP_KG_BASIS, 
				   norma.TOTAL_RP_PREMI, 
				   norma.RP_KG_PREMI,
				   NVL(checkroll.RP_HK, 0) RP_HK
			FROM TN_PANEN_KRANI_BUAH norma
			LEFT JOIN TR_RKT_CHECKROLL_SUM checkroll
				ON norma.PERIOD_BUDGET = checkroll.PERIOD_BUDGET
				AND norma.BA_CODE = checkroll.BA_CODE
				AND checkroll.JOB_CODE = 'FX160'
			LEFT JOIN TM_ORGANIZATION ORG
				  ON norma.BA_CODE = ORG.BA_CODE
			WHERE norma.DELETE_USER IS NULL
		 ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%'";
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
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) IN ('".$params['BA_CODE']."')
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
					OR UPPER(norma.TARGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BASIS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TARIF_BASIS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.SELISIH_OVER_BASIS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_BASIS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TOTAL_RP_PREMI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_PREMI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(checkroll.RP_HK) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.TARGET, norma.BASIS
		"; 
		//die($query);
		
		return $query;
	}
	
	//menampilkan list norma Panen Krani Buah
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
		$row['RP_HK'] = ( $row['RP_HK'] <> "" ) ? $row['RP_HK'] : $this->_formula->get_NormaPanenKraniBuah_RpHk($row);
		$selisih_over_basis = $this->_formula->cal_NormaPanenKraniBuah_SelisihOverBasis($row);
		$row['SELISIH_OVER_BASIS'] = $selisih_over_basis;
		$rp_kg_basis = $this->_formula->cal_NormaPanenKraniBuah_RpKgBasis($row);
		$premi = $this->_formula->cal_NormaPanenKraniBuah_TotalPremi($row);
		$row['PREMI'] = $premi;
		$rp_kg_premi = $this->_formula->cal_NormaPanenKraniBuah_RpKgPremi($row);
		
		if ($row['ROW_ID']){	
			
			$sql = "
				UPDATE TN_PANEN_KRANI_BUAH
				SET 
					TARGET = REPLACE('".addslashes($row['TARGET'])."', ',', ''),
					BASIS = REPLACE('".addslashes($row['BASIS'])."', ',', ''),
					TARIF_BASIS = REPLACE('".addslashes($row['TARIF_BASIS'])."', ',', ''),
					SELISIH_OVER_BASIS = REPLACE('".addslashes($selisih_over_basis)."', ',', ''),
					RP_KG_BASIS = REPLACE('".addslashes($rp_kg_basis)."', ',', ''),
					TOTAL_RP_PREMI = REPLACE('".addslashes($premi)."', ',', ''),
					RP_KG_PREMI = REPLACE('".addslashes($rp_kg_premi)."', ',', ''),
					RP_HK = REPLACE('".addslashes($row['RP_HK'])."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);		
	
			
			$sql = "
				UPDATE TN_PANEN_VARIABLE
				SET VALUE = REPLACE('".addslashes($row['BASIS'])."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".addslashes($row['PERIOD_BUDGET'])."'
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND PANEN_CODE = 'BAS_KRANI_BUAH';
			";
			
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);
				
				
		}else{
			
			$sql = "
				INSERT INTO TN_PANEN_KRANI_BUAH (PERIOD_BUDGET, BA_CODE, TARGET, BASIS, TARIF_BASIS, SELISIH_OVER_BASIS, RP_HK, RP_KG_BASIS, TOTAL_RP_PREMI, 
					RP_KG_PREMI, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
					'".addslashes($row['BA_CODE'])."',
					REPLACE('".addslashes($row['TARGET'])."',',',''),
					REPLACE('".addslashes($row['BASIS'])."',',',''),
					REPLACE('".addslashes($row['TARIF_BASIS'])."',',',''),
					REPLACE('".addslashes($selisih_over_basis)."',',',''),
					REPLACE('".addslashes($row['RP_HK'])."',',',''),
					REPLACE('".addslashes($rp_kg_basis)."',',',''),
					REPLACE('".addslashes($premi)."',',',''),
					REPLACE('".addslashes($rp_kg_premi)."',',',''),
					'{$this->_userName}',
					SYSDATE
				);
			";
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);		
		}
		return true;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		$row['RP_HK'] = ( $row['RP_HK'] <> "" ) ? $row['RP_HK'] : $this->_formula->get_NormaPanenKraniBuah_RpHk($row);
		$selisih_over_basis = $this->_formula->cal_NormaPanenKraniBuah_SelisihOverBasis($row);
		$row['SELISIH_OVER_BASIS'] = $selisih_over_basis;
		$rp_kg_basis = $this->_formula->cal_NormaPanenKraniBuah_RpKgBasis($row);
		$premi = $this->_formula->cal_NormaPanenKraniBuah_TotalPremi($row);
		$row['PREMI'] = $premi;
		$rp_kg_premi = $this->_formula->cal_NormaPanenKraniBuah_RpKgPremi($row);
		
		if ($row['ROW_ID']){	
			
			$sql = "
				UPDATE TN_PANEN_KRANI_BUAH
				SET 
					TARGET = REPLACE('".addslashes($row['TARGET'])."', ',', ''),
					BASIS = REPLACE('".addslashes($row['BASIS'])."', ',', ''),
					TARIF_BASIS = REPLACE('".addslashes($row['TARIF_BASIS'])."', ',', ''),
					SELISIH_OVER_BASIS = REPLACE('".addslashes($selisih_over_basis)."', ',', ''),
					RP_KG_BASIS = REPLACE('".addslashes($rp_kg_basis)."', ',', ''),
					TOTAL_RP_PREMI = REPLACE('".addslashes($premi)."', ',', ''),
					RP_KG_PREMI = REPLACE('".addslashes($rp_kg_premi)."', ',', ''),
					RP_HK = REPLACE('".addslashes($row['RP_HK'])."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);		
							
			$sql = "
				UPDATE TN_PANEN_VARIABLE
				SET VALUE = REPLACE('".addslashes($row['BASIS'])."', ',', ''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".addslashes($row['PERIOD_BUDGET'])."'
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND PANEN_CODE = 'BAS_KRANI_BUAH';
			";
			
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);
				
		}else{
			
			$sql = "
				INSERT INTO TN_PANEN_KRANI_BUAH (PERIOD_BUDGET, BA_CODE, TARGET, BASIS, TARIF_BASIS, SELISIH_OVER_BASIS, RP_HK, RP_KG_BASIS, TOTAL_RP_PREMI, 
						RP_KG_PREMI, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
					'".addslashes($row['BA_CODE'])."',
					REPLACE('".addslashes($row['TARGET'])."',',',''),
					REPLACE('".addslashes($row['BASIS'])."',',',''),
					REPLACE('".addslashes($row['TARIF_BASIS'])."',',',''),
					REPLACE('".addslashes($selisih_over_basis)."',',',''),
					REPLACE('".addslashes($row['RP_HK'])."',',',''),
					REPLACE('".addslashes($rp_kg_basis)."',',',''),
					REPLACE('".addslashes($premi)."',',',''),
					REPLACE('".addslashes($rp_kg_premi)."',',',''),
					'{$this->_userName}',
					SYSDATE
				);
			";
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);		
		}
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
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - KRANI BUAH');
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
}

