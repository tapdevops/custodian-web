<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Panen Premi Langsir
Function 			:	- getList					: menampilkan list norma Panen Premi Langsir
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save dan save temp
YULIUS 16/07/2014	:	- set field FLAG_TEMP pada function getData, save, saveTemp	
=========================================================================================================================
*/
class Application_Model_NormaPanenPremiLangsir
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
				   norma.VRA_CODE, 
				   norma.TON_TRIP, 
				   norma.TRIP_HARI, 
				   norma.TON_HARI, 
				   norma.HM_TRIP, 
				   NVL(vra_sum.VALUE, 0) RP_HM, 
				   norma.RP_TRIP, 
				   norma.RP_KG,
				   norma.FLAG_TEMP,
				   vra.TYPE VRA_TYPE
			FROM TN_PANEN_PREMI_LANGSIR norma
			LEFT JOIN TR_RKT_VRA_SUM vra_sum
				ON norma.PERIOD_BUDGET = vra_sum.PERIOD_BUDGET
				AND norma.BA_CODE = vra_sum.BA_CODE
				AND norma.VRA_CODE = vra_sum.VRA_CODE
			LEFT JOIN TM_VRA vra
				ON norma.VRA_CODE = vra.VRA_CODE
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
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['vra_code'] != '') {
			$query .= "
                AND norma.VRA_CODE IN ('".$params['vra_code']."')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan VRA CODE
		if ($params['VRA_CODE'] != '') {
			$query .= "
                AND UPPER(norma.VRA_CODE) IN ('".$params['VRA_CODE']."')
            ";
        }
		
		
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.VRA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TON_TRIP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TRIP_HARI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TON_HARI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.HM_TRIP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_TRIP) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG) LIKE UPPER('%".$params['search']."%')
					OR UPPER(vra_sum.VALUE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(vra.TYPE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.VRA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list norma Panen Premi Langsir
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
		
		$ton_hari = $this->_formula->cal_NormaPanenPremiLangsir_TonHari($row);
		$row['TON_HARI'] = $ton_hari;
		$rp_trip = $this->_formula->cal_NormaPanenPremiLangsir_RpTrip($row);
		$row['RP_TRIP'] = $rp_trip;
		$rp_kg = $this->_formula->cal_NormaPanenPremiLangsir_RpKg($row);
			
			$sql = "UPDATE TN_PANEN_PREMI_LANGSIR
					SET TON_TRIP = REPLACE('".addslashes($row['TON_TRIP'])."', ',', ''),
						TRIP_HARI = REPLACE('".addslashes($row['TRIP_HARI'])."', ',', ''),
						TON_HARI = REPLACE('".addslashes($ton_hari)."', ',', ''),
						HM_TRIP = REPLACE('".addslashes($row['HM_TRIP'])."', ',', ''),
						RP_HM = REPLACE('".addslashes($row['RP_HM'])."', ',', ''),
						RP_TRIP = REPLACE('".addslashes($rp_trip)."', ',', ''),
						RP_KG = REPLACE('".addslashes($rp_kg)."', ',', ''),
						FLAG_TEMP = NULL,
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
		$ton_hari = $this->_formula->cal_NormaPanenPremiLangsir_TonHari($row);
		$row['TON_HARI'] = $ton_hari;
		$rp_trip = $this->_formula->cal_NormaPanenPremiLangsir_RpTrip($row);
		$row['RP_TRIP'] = $rp_trip;
		$rp_kg = $this->_formula->cal_NormaPanenPremiLangsir_RpKg($row);
		
       	$sql = "UPDATE TN_PANEN_PREMI_LANGSIR
				SET TON_TRIP = REPLACE('".addslashes($row['TON_TRIP'])."', ',', ''),
					TRIP_HARI = REPLACE('".addslashes($row['TRIP_HARI'])."', ',', ''),
					TON_HARI = REPLACE('".addslashes($ton_hari)."', ',', ''),
					HM_TRIP = REPLACE('".addslashes($row['HM_TRIP'])."', ',', ''),
					RP_HM = REPLACE('".addslashes($row['RP_HM'])."', ',', ''),
					RP_TRIP = REPLACE('".addslashes($rp_trip)."', ',', ''),
					RP_KG = REPLACE('".addslashes($rp_kg)."', ',', ''),
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
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - PREMI LANGSIR');
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
        $ton_hari = $this->_formula->cal_NormaPanenPremiLangsir_TonHari($row);
		$row['TON_HARI'] = $ton_hari;
		$rp_trip = $this->_formula->cal_NormaPanenPremiLangsir_RpTrip($row);
		$row['RP_TRIP'] = $rp_trip;
		$rp_kg = $this->_formula->cal_NormaPanenPremiLangsir_RpKg($row);
			
		$sql = "UPDATE TN_PANEN_PREMI_LANGSIR
				SET TON_TRIP = REPLACE('".addslashes($row['TON_TRIP'])."', ',', ''),
					TRIP_HARI = REPLACE('".addslashes($row['TRIP_HARI'])."', ',', ''),
					TON_HARI = REPLACE('".addslashes($ton_hari)."', ',', ''),
					HM_TRIP = REPLACE('".addslashes($row['HM_TRIP'])."', ',', ''),
					RP_HM = REPLACE('".addslashes($row['RP_HM'])."', ',', ''),
					RP_TRIP = REPLACE('".addslashes($rp_trip)."', ',', ''),
					RP_KG = REPLACE('".addslashes($rp_kg)."', ',', ''),
					FLAG_TEMP = NULL,
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
			";
		$this->_db->query($sql);
		$this->_db->commit();
				
        return true;
    }
}

