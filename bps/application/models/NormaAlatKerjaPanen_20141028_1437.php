<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Alat Kerja Panen
Function 			:	- getList					: menampilkan list norma alat kerja panen
						- save						: simpan data
						- delete					: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaAlatKerjaPanen
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
				   norma.MATERIAL_CODE,
				   material.MATERIAL_NAME,
				   material.UOM,
				   norma_harga.PRICE HARGA_INFLASI,
				   norma.ROTASI,
				   norma.TOTAL,
				   norma.FLAG_TEMP,
				   (SELECT SUM.PRICE_SUM
					  FROM TN_ALAT_KERJA_PANEN_SUM SUM
					 WHERE SUM.PERIOD_BUDGET = norma.PERIOD_BUDGET
						   AND SUM.BA_CODE = norma.BA_CODE)
					  AS PRICE_SUM,
				   (SELECT sum1.PRICE_ROTASI_SUM
					  FROM TN_ALAT_KERJA_PANEN_SUM sum1
					 WHERE sum1.PERIOD_BUDGET = norma.PERIOD_BUDGET
						   AND sum1.BA_CODE = norma.BA_CODE)
					  AS PRICE_ROTASI_SUM,
				   (SELECT sum2.PRICE_KG
					  FROM TN_ALAT_KERJA_PANEN_SUM sum2
					 WHERE sum2.PERIOD_BUDGET = norma.PERIOD_BUDGET
						   AND sum2.BA_CODE = norma.BA_CODE)
					  AS PRICE_KG
			  FROM TN_ALAT_KERJA_PANEN norma
				   LEFT JOIN TN_HARGA_BARANG norma_harga
					  ON norma.MATERIAL_CODE = norma_harga.MATERIAL_CODE
						 AND norma.BA_CODE = norma_harga.BA_CODE
						 AND norma.PERIOD_BUDGET = norma_harga.PERIOD_BUDGET
				   LEFT JOIN TM_MATERIAL material
					  ON norma.MATERIAL_CODE = material.MATERIAL_CODE
						 AND norma.BA_CODE = material.BA_CODE
						 AND norma.PERIOD_BUDGET = material.PERIOD_BUDGET
				   LEFT JOIN TM_ORGANIZATION B
					  ON norma.BA_CODE = B.BA_CODE
			 WHERE norma.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
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
                AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
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
					OR UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.ROTASI) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.TOTAL) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		//untuk inheritance
		if ($params['sub_cost_element'] != '') {
			$query .= "
                AND UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['sub_cost_element']."%')
            ";
        }
		
		$query .= "
			ORDER BY norma.MATERIAL_CODE
		";
		
		return $query;
	}
	
	//menampilkan list norma alat kerja panen
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
			FROM TN_ALAT_KERJA_PANEN 
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '{$params['PERIOD_BUDGET']}'
				AND BA_CODE = '{$params['BA_CODE']}'
				AND MATERIAL_CODE = '{$params['MATERIAL_CODE']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function saveTemp($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
			
		if ($row['ROW_ID']){
				$sql = "UPDATE TN_ALAT_KERJA_PANEN
						SET ROTASI = REPLACE('".addslashes($row['ROTASI'])."',',',''),
							PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							TOTAL = NULL,
							TRIGGER_UPDATE = NULL,
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE,
							DELETE_TIME = NULL,
							DELETE_USER = NULL,
							FLAG_TEMP = 'Y'
						 WHERE ROWID = '{$row['ROW_ID']}';
						 ";
		}else{
				$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
				$sql = "INSERT INTO TN_ALAT_KERJA_PANEN (PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, 
							ROTASI, PRICE, TOTAL, INSERT_USER, INSERT_TIME, FLAG_TEMP)
						VALUES (
								TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($region_code)."',
								'".addslashes($row['BA_CODE'])."',
								'".addslashes($row['MATERIAL_CODE'])."',
								REPLACE('".addslashes($row['ROTASI'])."',',',''),
								REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
								NULL,
								'{$this->_userName}',
								SYSDATE,
								'Y'
							);
						";
		}
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$total_rp = $this->_formula->cal_NormaAlatKerjaPanen_TotalRupiah($row);
			
		if ($row['ROW_ID']){
				$sql = "UPDATE TN_ALAT_KERJA_PANEN
						SET ROTASI = REPLACE('".addslashes($row['ROTASI'])."',',',''),
							PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							TOTAL = REPLACE('".addslashes($total_rp)."',',',''),
							TRIGGER_UPDATE = NULL,
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE,
							DELETE_TIME = NULL,
							DELETE_USER = NULL,
							FLAG_TEMP = NULL
						 WHERE ROWID = '{$row['ROW_ID']}';
						 ";
		}else{
				$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
				$sql = "INSERT INTO TN_ALAT_KERJA_PANEN (PERIOD_BUDGET, REGION_CODE, BA_CODE, 
							MATERIAL_CODE, ROTASI, PRICE, TOTAL, INSERT_USER, INSERT_TIME, FLAG_TEMP)
						VALUES (
								TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
								'".addslashes($region_code)."',
								'".addslashes($row['BA_CODE'])."',
								'".addslashes($row['MATERIAL_CODE'])."',
								REPLACE('".addslashes($row['ROTASI'])."',',',''),
								REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
								REPLACE('".addslashes($total_rp)."',',',''),
								'{$this->_userName}',
								SYSDATE,
								NULL
							);
							";
		}
					
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);	
        return $result;
    }
	
	//update summary
	public function updateSummaryNormaAlatKerjaPanen($param = array())
	{
		$sql = "
			SELECT PERIOD_BUDGET, BA_CODE, SUM(TOTAL) TOTAL, SUM(PRICE) PRICE
			FROM TN_ALAT_KERJA_PANEN
			WHERE to_char(PERIOD_BUDGET,'RRRR') = '{$param['PERIOD_BUDGET']}'
                AND BA_CODE = '".$param['BA_CODE']."'
				AND DELETE_USER IS NULL
			GROUP BY PERIOD_BUDGET, BA_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		foreach ($rows as $idx => $row) {
			//hapus data yang ada
			$sql = "
				DELETE FROM TN_ALAT_KERJA_PANEN_SUM
				WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND to_char(PERIOD_BUDGET,'RRRR') = '{$param['PERIOD_BUDGET']}';
			";
			
			///////////////////////////// SUMMARY TOTAL HARGA ALAT KERJA PANEN /////////////////////////////
			$row['PRICE_ROTASI_SUM'] = $row['TOTAL'];
			$rp_kg = $this->_formula->cal_NormaAlatKerjaPanen_RpKg($row);
			
			//insert DB
			$sql.= "
				INSERT INTO TN_ALAT_KERJA_PANEN_SUM (PERIOD_BUDGET, BA_CODE, PRICE_SUM, PRICE_ROTASI_SUM, PRICE_KG, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('01-01-{$param['PERIOD_BUDGET']}','DD-MM-RRRR'),
					'".addslashes($row['BA_CODE'])."',
					'".addslashes($row['TOTAL'])."',
					'".addslashes($row['PRICE'])."',
					'".addslashes($rp_kg)."',
					'{$this->_userName}',
					SYSDATE
				);
			";
			//create sql file
			$this->_global->createSqlFile($param['filename'], $sql);	
		}	
	}
	
	//hapus data
	public function delete($row = array())
    {
		$result = true;
		
		try {
			$sql = "UPDATE TN_ALAT_KERJA_PANEN
					SET DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWID = '".$row['ROW_ID']."'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'NORMA ALAT KERJA PANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'NORMA ALAT KERJA PANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE RKT PANEN **********************************************
		//reset data
		$param = array();		
		
		$model = new Application_Model_RktPanen();	
		
		$param['key_find'] = $row['BA_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {
				foreach ($records1 as $idx1 => $record1) {
					$model->save($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA ALAT KERJA - PANEN');					
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
	}
	
	//kalkulasi data saat upload
	public function calculateData($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$total_rp = $this->_formula->cal_NormaAlatKerjaPanen_TotalRupiah($row);
			
		if ($row['ROW_ID']){
				$sql = "UPDATE TN_ALAT_KERJA_PANEN
						SET ROTASI = REPLACE('".addslashes($row['ROTASI'])."',',',''),
							PRICE = REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
							TOTAL = REPLACE('".addslashes($total_rp)."',',',''),
							TRIGGER_UPDATE = NULL,
							UPDATE_USER = '{$this->_userName}',
							UPDATE_TIME = SYSDATE,
							DELETE_TIME = NULL,
							DELETE_USER = NULL,
							FLAG_TEMP = NULL
						 WHERE ROWID = '{$row['ROW_ID']}'
						 ";
		}else{
				$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
				$sql = "INSERT INTO TN_ALAT_KERJA_PANEN (PERIOD_BUDGET, REGION_CODE, BA_CODE, 
							MATERIAL_CODE, ROTASI, PRICE, TOTAL, INSERT_USER, INSERT_TIME, FLAG_TEMP)
						VALUES (
								TO_DATE('".$this->_period."','DD-MM-RRRR'),
								'".addslashes($region_code)."',
								'".addslashes($row['BA_CODE'])."',
								'".addslashes($row['MATERIAL_CODE'])."',
								REPLACE('".addslashes($row['ROTASI'])."',',',''),
								REPLACE('".addslashes($row['HARGA_INFLASI'])."',',',''),
								REPLACE('".addslashes($total_rp)."',',',''),
								'{$this->_userName}',
								SYSDATE,
								NULL
							)
							";
		}
		$this->_db->query($sql);
		$this->_db->commit();
		
		
		//summary
		$sql = "
			SELECT PERIOD_BUDGET, BA_CODE, SUM(TOTAL) TOTAL, SUM(PRICE) PRICE
			FROM TN_ALAT_KERJA_PANEN
			WHERE PERIOD_BUDGET = TO_DATE('".$this->_period."','DD-MM-RRRR')
                AND BA_CODE = '".$param['BA_CODE']."'
				AND DELETE_USER IS NULL
			GROUP BY PERIOD_BUDGET, BA_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		foreach ($rows as $idx => $row) {
			//hapus data yang ada
			$sql = "
				DELETE FROM TN_ALAT_KERJA_PANEN_SUM
				WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND PERIOD_BUDGET = TO_DATE('".$this->_period."','DD-MM-RRRR')
			";
			$this->_db->query($sql);
			$this->_db->commit();
			
			///////////////////////////// SUMMARY TOTAL HARGA ALAT KERJA PANEN /////////////////////////////
			$row['PRICE_ROTASI_SUM'] = $row['TOTAL'];
			$rp_kg = $this->_formula->cal_NormaAlatKerjaPanen_RpKg($row);
			
			//insert DB
			$sql = "
				INSERT INTO TN_ALAT_KERJA_PANEN_SUM (PERIOD_BUDGET, BA_CODE, PRICE_SUM, PRICE_ROTASI_SUM, PRICE_KG, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('".$this->_period."','DD-MM-RRRR'),
					'".addslashes($row['BA_CODE'])."',
					'".addslashes($row['TOTAL'])."',
					'".addslashes($row['PRICE'])."',
					'".addslashes($rp_kg)."',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
				
        return true;
    }
}

