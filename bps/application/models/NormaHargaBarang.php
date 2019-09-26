<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma Harga Barang
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: SID 24/06/2013	: ambil data dari DB
						- getList					: SID 24/06/2013	: menampilkan list norma Pupuk TBM
						- save						: SID 24/06/2013	: simpan data
						- delete					: SID 24/06/2013	: hapus data
						
						- getList					: menampilkan list norma harga barang
						- save						: simpan data
						- updateSummaryHargaBarang	: update summary
						- delete					: hapus data
						- calculateAllItem			: prosedur untuk kalkulasi seluruh item
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	25/04/2013
Update Terakhir		:	25/04/2013
Revisi	||	PIC				||	TANGGAL			||	DESKRIPSI 		
=========================================================================================================================
1			DONI				19/06/2013			MENAMBAHKAN PENGIRMAN DATA REFERENCE_ROLE UNTUK VALIDASI FILTERING
=========================================================================================================================
*/
class Application_Model_NormaHargaBarang
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
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; 
		
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
            SELECT ROWIDTOCHAR(norma_harga_barang.ROWID) row_id, rownum, to_char(norma_harga_barang.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, norma_harga_barang.BA_CODE, 
				   norma_harga_barang.MATERIAL_CODE, material.DESCRIPTION MATERIAL_NAME, material.UOM, material.PRICE HARGA_DASAR, norma_dasar.PERCENT_INCREASE INFLASI, 
				   norma_harga_barang.PRICE NORMA_HARGA,norma_harga_barang.FLAG_TEMP, norma_harga_barang.PRICE PRICE, norma_harga_barang.REGION_CODE, 
					(SELECT PRICE
					 FROM TN_HARGA_BARANG_SUM
					 WHERE MATERIAL_CODE = norma_harga_barang.MATERIAL_CODE
						AND PERIOD_BUDGET = norma_harga_barang.PERIOD_BUDGET
						AND REGION_CODE = norma_harga_barang.REGION_CODE) AVG_REGION,
					(SELECT PRICE
					 FROM TN_HARGA_BARANG_SUM
					 WHERE MATERIAL_CODE = norma_harga_barang.MATERIAL_CODE
						AND PERIOD_BUDGET = norma_harga_barang.PERIOD_BUDGET
						AND REGION_CODE = 'ALL') AVG_PT
            FROM TN_HARGA_BARANG norma_harga_barang
			LEFT JOIN V_MATERIAL_ASSET material
				ON material.CODE = norma_harga_barang.MATERIAL_CODE
				AND material.PERIOD_BUDGET = norma_harga_barang.PERIOD_BUDGET
				AND material.BA_CODE = norma_harga_barang.BA_CODE
			LEFT JOIN TN_BASIC norma_dasar
				ON material.BASIC_NORMA_CODE = norma_dasar.BASIC_NORMA_CODE
				AND material.PERIOD_BUDGET = norma_dasar.PERIOD_BUDGET
				AND material.BA_CODE = norma_dasar.BA_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma_harga_barang.BA_CODE = ORG.BA_CODE
			WHERE norma_harga_barang.DELETE_USER IS NULL
        ";	
	
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma_harga_barang.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(norma_harga_barang.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(norma_harga_barang.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(norma_harga_barang.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(norma_harga_barang.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(norma_harga_barang.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_barang.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_barang.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(material.BASIC_NORMA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_dasar.PERCENT_INCREASE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma_harga_barang.PRICE) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY norma_harga_barang.BA_CODE, material.DESCRIPTION, NVL(norma_harga_barang.PRICE,0), norma_harga_barang.MATERIAL_CODE
		";
		
		//die($query);
		return $query;
	}
	
	//menampilkan list norma harga barang
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
				$row['HARGA_KALKULASI'] = $this->_formula->cal_NormaHargaBarang_HargaInflasi(array('HARGA_DASAR' 	=>	$row['HARGA_DASAR'],
																								   'INFLASI' 		=>	$row['INFLASI']));
                $result['rows'][] = $row;
            }
        }

        return $result;
    }
	
	//simpan data
	public function saveTemp($row = array())
    { 
        $result = true;
		
		// ********************************************** UPDATE NORMA HARGA BARANG **********************************************
		$sql = "";
		if ($row['ROW_ID']){
			$sql.= "UPDATE TN_HARGA_BARANG
					SET  
						PRICE = REPLACE('{$row['PRICE']}',',',''),
						TRIGGER_UPDATE = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						FLAG_TEMP = 'Y'
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					 ";
		}else{
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			$sql.= "INSERT INTO TN_HARGA_BARANG (PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, PRICE, INSERT_USER, INSERT_TIME, FLAG_TEMP)
					VALUES (
							TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
							'".addslashes($region_code)."',
							'".addslashes($row['BA_CODE'])."',
							'".addslashes($row['MATERIAL_CODE'])."',
							REPLACE('".addslashes($row['PRICE'])."',',',''),
							'{$this->_userName}',
							SYSDATE,
							NULL
						);
						";
		}
		// ********************************************** END OF UPDATE NORMA HARGA BARANG **********************************************
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		
		return $result;
    }
	
	public function save($row = array())
    { 
        $result = true;
		
		// ********************************************** UPDATE NORMA HARGA BARANG **********************************************
		$sql = "";
		if ($row['ROW_ID']){
			$sql.= "UPDATE TN_HARGA_BARANG
					SET  
						PRICE = REPLACE('{$row['PRICE']}',',',''),
						TRIGGER_UPDATE = NULL,
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE,
						FLAG_TEMP = NULL
					 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
					 ";
		}else{
			$region_code = $this->_formula->get_RegionCode($row['BA_CODE']);
			$sql.= "INSERT INTO TN_HARGA_BARANG (PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, PRICE, INSERT_USER, INSERT_TIME, FLAG_TEMP)
					VALUES (
							TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
							'".addslashes($region_code)."',
							'".addslashes($row['BA_CODE'])."',
							'".addslashes($row['MATERIAL_CODE'])."',
							REPLACE('".addslashes($row['PRICE'])."',',',''),
							'{$this->_userName}',
							SYSDATE,
							'Y'
						);
						";
		}
		// ********************************************** END OF UPDATE NORMA HARGA BARANG **********************************************
				 
		$this->_global->createSqlFile($row['filename'], $sql);
		
		return $result;
    }
	
	//hapus data
	public function delete($row = array())
    {
		$sql = "UPDATE TN_HARGA_BARANG
				SET  
					DELETE_USER = '{$this->_userName}',
					DELETE_TIME = SYSDATE
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'";
		
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
	}
	
	//kalkulasi seluruh data $this->_period
	public function calculateAllItem($region_code, $filename)
    {
		$result = true;
				$par = array(
                    'V_PERIOD_BUDGET'   => "01-01-{$row['PERIOD_BUDGET']}",
                    'V_REGION_CODE'  	=> $region_code,
					'V_USER'  			=> $this->_userName
                );
				
                $sql = "
                    BEGIN
						PKG_BUDGET.CALC_ALL_AVG_NORMA_HARGA (to_date(:V_PERIOD_BUDGET,'dd-mm-rrrr') ,:V_REGION_CODE, :V_USER );
                    END;
                ";
			
				$statement = new Zend_Db_Statement_Oracle($this->_db, $sql);
                $statement->execute($par);

        return $result;
    }
	
	//hitung avg norma harga barang
	public function updateAvgNormaHargaBarang($params = array())
    {
		//avg region
		$sql = "
			SELECT 	PERIOD_BUDGET,
					REGION_CODE,
					MATERIAL_CODE,
					AVG (PRICE) AS PRICE
            FROM TN_HARGA_BARANG
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
                  AND REGION_CODE = '".$params['REGION_CODE']."'
                  AND DELETE_TIME IS NULL
			GROUP BY PERIOD_BUDGET, REGION_CODE, MATERIAL_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$sql = "
					DELETE FROM TN_HARGA_BARANG_SUM
					WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
						AND MATERIAL_CODE = '".$row['MATERIAL_CODE']."'
						AND REGION_CODE = '".$row['REGION_CODE']."';
						
					INSERT INTO TN_HARGA_BARANG_SUM (PERIOD_BUDGET, REGION_CODE, MATERIAL_CODE, PRICE, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['MATERIAL_CODE']."',
						'".$row['PRICE']."',
						'{$this->_userName}',
						SYSDATE
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
		
		//avg national
		$sql = "
			SELECT 	PERIOD_BUDGET,
					MATERIAL_CODE,
					AVG (PRICE) AS PRICE
            FROM TN_HARGA_BARANG
			WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
                  AND DELETE_TIME IS NULL
			GROUP BY PERIOD_BUDGET, MATERIAL_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
				$sql = "
					DELETE FROM TN_HARGA_BARANG_SUM
					WHERE TO_CHAR(PERIOD_BUDGET, 'RRRR') = '".$params['PERIOD_BUDGET']."'
						AND MATERIAL_CODE = '".$row['MATERIAL_CODE']."'
						AND REGION_CODE = 'ALL';
						
					INSERT INTO TN_HARGA_BARANG_SUM (PERIOD_BUDGET, REGION_CODE, MATERIAL_CODE, PRICE, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."','DD-MM-RRRR'),
						'ALL',
						'".$row['MATERIAL_CODE']."',
						'".$row['PRICE']."',
						'{$this->_userName}',
						SYSDATE
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }

        return true;
    }
}

