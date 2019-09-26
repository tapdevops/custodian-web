<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Panen Cost Unit
Function 			:	- getList					: menampilkan list norma Panen Cost Unit
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	28/06/2013
Update Terakhir		:	28/06/2013
Revisi				:	
YULIUS 08/07/2014		: - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save dan save temp
YULIUS 16/07/2014	:	- set field FLAG_TEMP pada function getData, save, saveTemp						  
=========================================================================================================================
*/
class Application_Model_NormaPanenCostUnit
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
					AND target.PANEN_CODE = 'PRE_COST_UNT_JRK') as JARAK_ANGKUT, 
				   (SELECT target.VALUE
					FROM TN_PANEN_VARIABLE target
					WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
					AND target.BA_CODE = norma.BA_CODE
					AND target.PANEN_CODE = 'PRE_COST_UNT_TARGET_TON') as TARGET, 
				   (SELECT target.VALUE
					FROM TN_PANEN_VARIABLE target
					WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
					AND target.BA_CODE = norma.BA_CODE
					AND target.PANEN_CODE = 'PRE_COST_UNIT_RIT') as RIT, 
				   norma.RP_KG_INTERNAL, 
				   (SELECT ext.VALUE
					FROM TN_PANEN_VARIABLE ext
					WHERE ext.PERIOD_BUDGET = norma.PERIOD_BUDGET
							AND ext.BA_CODE = norma.BA_CODE
							AND PANEN_CODE = 'VRA_DT_EXTERNAL') DEFAULT_RP_KM_EXTERNAL,
				   norma.RP_KM_EXTERNAL, 
				   norma.RP_KG_EXTERNAL,			
					norma.FLAG_TEMP,
				   NVL(vra_sum.VALUE, 0) RP_KM_INTERNAL
			FROM TN_PANEN_PREMI_COST_UNIT norma
			LEFT JOIN TR_RKT_VRA_SUM vra_sum
				ON norma.PERIOD_BUDGET = vra_sum.PERIOD_BUDGET
				AND norma.BA_CODE = vra_sum.BA_CODE
				AND vra_sum.VRA_CODE = 'DT010'
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
		
		//jika diupdate dari norma VRA / RKT VRA, filter berdasarkan BA
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
					OR UPPER(norma.JARAK_ANGKUT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TARGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RIT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_INTERNAL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KM_EXTERNAL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.RP_KG_EXTERNAL) LIKE UPPER('%".$params['search']."%')
					OR UPPER(vra_sum.VALUE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.JARAK_ANGKUT, norma.TARGET, norma.RIT
		";
		return $query;
	}
	
	//menampilkan list norma Panen Cost Unit
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
		$row['RP_KM_INTERNAL'] = ( $row['RP_KM_INTERNAL'] <> "" ) ? $row['RP_KM_INTERNAL'] : $this->_formula->get_NormaPanenCostUnit_RpKmInternal($row);
		$rp_kg_internal = $this->_formula->cal_NormaPanenCostUnit_RpKgInternal($row);
		$rp_kg_external = $this->_formula->cal_NormaPanenCostUnit_RpKgExternal($row);
		
		if ($row['ROW_ID']){	
				$sql = "UPDATE TN_PANEN_PREMI_COST_UNIT
						SET JARAK_ANGKUT = REPLACE('".addslashes($row['JARAK_ANGKUT'])."', ',', ''),
							TARGET = REPLACE('".addslashes($row['TARGET'])."', ',', ''),
							RIT = REPLACE('".addslashes($row['RIT'])."', ',', ''),	   
							RP_KM_INTERNAL = REPLACE('".addslashes($row['RP_KM_INTERNAL'])."', ',', ''),
							RP_KG_INTERNAL = REPLACE('".addslashes($rp_kg_internal)."', ',', ''),
							RP_KM_EXTERNAL = REPLACE('".addslashes($row['RP_KM_EXTERNAL'])."', ',', ''),
							RP_KG_EXTERNAL = REPLACE('".addslashes($rp_kg_external)."', ',', ''),		
							FLAG_TEMP=NULL,
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE
						 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					";
			//create sql file
			$this->_global->createSqlFile($row['filename'], $sql);		
		
		}else{
				$sql = "INSERT INTO TN_PANEN_PREMI_COST_UNIT (PERIOD_BUDGET, BA_CODE, JARAK_ANGKUT, TARGET, RIT, RP_KM_INTERNAL, RP_KG_INTERNAL, RP_KM_EXTERNAL, RP_KG_EXTERNAL, INSERT_USER, INSERT_TIME)
						VALUES (
								TO_DATE('{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($row['BA_CODE'])."',
								REPLACE('".addslashes($row['JARAK_ANGKUT'])."',',',''),
								REPLACE('".addslashes($row['TARGET'])."',',',''),
								REPLACE('".addslashes($row['RIT'])."',',',''),
								REPLACE('".addslashes($row['RP_KM_INTERNAL'])."',',',''),
								REPLACE('".addslashes($rp_kg_internal)."',',',''),
								REPLACE('".addslashes($row['RP_KM_EXTERNAL'])."',',',''),
								REPLACE('".addslashes($rp_kg_external)."',',',''),
								'{$this->_userName}',
								SYSDATE);
						";
				//create sql file
				$this->_global->createSqlFile($row['filename'], $sql);		
		}
		return true;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		$row['RP_KM_INTERNAL'] = ( $row['RP_KM_INTERNAL'] <> "" ) ? $row['RP_KM_INTERNAL'] : $this->_formula->get_NormaPanenCostUnit_RpKmInternal($row);
		$rp_kg_internal = $this->_formula->cal_NormaPanenCostUnit_RpKgInternal($row);
		$rp_kg_external = $this->_formula->cal_NormaPanenCostUnit_RpKgExternal($row);
		
		if ($row['ROW_ID']){	
				$sql = "UPDATE TN_PANEN_PREMI_COST_UNIT
						SET 
							JARAK_ANGKUT = REPLACE('".addslashes($row['JARAK_ANGKUT'])."', ',', ''),
							TARGET = REPLACE('".addslashes($row['TARGET'])."', ',', ''),
							RIT = REPLACE('".addslashes($row['RIT'])."', ',', ''),	   
							RP_KM_INTERNAL = REPLACE('".addslashes($row['RP_KM_INTERNAL'])."', ',', ''),
							RP_KG_INTERNAL = REPLACE('".addslashes($rp_kg_internal)."', ',', ''),
							RP_KM_EXTERNAL = REPLACE('".addslashes($row['RP_KM_EXTERNAL'])."', ',', ''),
							RP_KG_EXTERNAL = REPLACE('".addslashes($rp_kg_external)."', ',', ''),	
							FLAG_TEMP='Y',
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE
						 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					   ";
		}else{
				$sql = "INSERT INTO TN_PANEN_PREMI_COST_UNIT (PERIOD_BUDGET, BA_CODE, JARAK_ANGKUT, TARGET, RIT, RP_KM_INTERNAL, RP_KG_INTERNAL, RP_KM_EXTERNAL, RP_KG_EXTERNAL, INSERT_USER, INSERT_TIME)
						VALUES (
								TO_DATE('{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($row['BA_CODE'])."',
								REPLACE('".addslashes($row['JARAK_ANGKUT'])."',',',''),
								REPLACE('".addslashes($row['TARGET'])."',',',''),
								REPLACE('".addslashes($row['RIT'])."',',',''),
								REPLACE('".addslashes($row['RP_KM_INTERNAL'])."',',',''),
								REPLACE('".addslashes($rp_kg_internal)."',',',''),
								REPLACE('".addslashes($row['RP_KM_EXTERNAL'])."',',',''),
								REPLACE('".addslashes($rp_kg_external)."',',',''),
								'{$this->_userName}',
								SYSDATE);
						";				
		}
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
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - COST UNIT');
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

