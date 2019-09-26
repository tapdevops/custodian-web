<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Perkerasan Jalan
Function 			:	- getList			: menampilkan list norma Perkerasan Jalan
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
						- updTnHarga		: update Tn Harga (YIR 7/15/2014)
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaPerkerasanJalan
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
			SELECT ROWIDTOCHAR (TNPJ.ROWID) ROW_ID,
				   ROWNUM,
				   TO_CHAR (TNPJ.PERIOD_BUDGET, 'RRRR') AS PERIOD_BUDGET,
				   TNPJ.BA_CODE,
				   TNPJ.ACTIVITY_CODE,
				   TMA.DESCRIPTION,
				   TNPJ.LEBAR,
				   TNPJ.PANJANG,
				   TNPJ.TEBAL,
				   TNPJ.MATERIAL_QTY AS VOLUME_MATERIAL,
				   TNPJ.PRICE,
				   TNPJ.VRA_CODE_DT,
				   NVL (
					(( SELECT VALUE
						 FROM TR_RKT_VRA_SUM VRASUM
						WHERE     TNPJ.PERIOD_BUDGET = VRASUM.PERIOD_BUDGET
							  AND TNPJ.BA_CODE = VRASUM.BA_CODE
							  AND VRASUM.VRA_CODE = TNPJ.VRA_CODE_DT)
					UNION
					( SELECT NVRAPINJAM.RP_QTY AS VALUE
						FROM TN_VRA_PINJAM NVRAPINJAM
					   WHERE     NVRAPINJAM.PERIOD_BUDGET = TNPJ.PERIOD_BUDGET
							 AND NVRAPINJAM.REGION_CODE = B.REGION_CODE
							 AND NVRAPINJAM.VRA_CODE = TNPJ.VRA_CODE_DT)),
					  0)
					  AS RP_KM_DT,
				   TNPJ.KAPASITAS_DT,
				   TNPJ.KECEPATAN_DT,
				   TNPJ.JAM_KERJA_DT,
				   TNPJ.VRA_CODE_EXCAV,
				   NVL (
					(( SELECT VALUE
						 FROM TR_RKT_VRA_SUM VRASUM
						WHERE     TNPJ.PERIOD_BUDGET = VRASUM.PERIOD_BUDGET
							  AND TNPJ.BA_CODE = VRASUM.BA_CODE
							  AND VRASUM.VRA_CODE = TNPJ.VRA_CODE_EXCAV)
					UNION
					( SELECT NVRAPINJAM.RP_QTY AS VALUE
						FROM TN_VRA_PINJAM NVRAPINJAM
					   WHERE     NVRAPINJAM.PERIOD_BUDGET = TNPJ.PERIOD_BUDGET
							 AND NVRAPINJAM.REGION_CODE = B.REGION_CODE
							 AND NVRAPINJAM.VRA_CODE = TNPJ.VRA_CODE_EXCAV)),
					  0)
					  AS RP_HM_EXCAV,
				   TNPJ.KAPASITAS_EXCAV,
				   TNPJ.VRA_CODE_COMPACTOR,
				   NVL (
					(( SELECT VALUE
						 FROM TR_RKT_VRA_SUM VRASUM
						WHERE     TNPJ.PERIOD_BUDGET = VRASUM.PERIOD_BUDGET
							  AND TNPJ.BA_CODE = VRASUM.BA_CODE
							  AND VRASUM.VRA_CODE = TNPJ.VRA_CODE_COMPACTOR)
					UNION
					( SELECT NVRAPINJAM.RP_QTY AS VALUE
						FROM TN_VRA_PINJAM NVRAPINJAM
					   WHERE     NVRAPINJAM.PERIOD_BUDGET = TNPJ.PERIOD_BUDGET
							 AND NVRAPINJAM.REGION_CODE = B.REGION_CODE
							 AND NVRAPINJAM.VRA_CODE = TNPJ.VRA_CODE_COMPACTOR)),
					  0)
					  AS RP_HM_COMP,
				   TNPJ.KAPASITAS_COMPACTOR,
				   TNPJ.VRA_CODE_GRADER,
				   NVL (
					(( SELECT VALUE
						 FROM TR_RKT_VRA_SUM VRASUM
						WHERE     TNPJ.PERIOD_BUDGET = VRASUM.PERIOD_BUDGET
							  AND TNPJ.BA_CODE = VRASUM.BA_CODE
							  AND VRASUM.VRA_CODE = TNPJ.VRA_CODE_GRADER)
					UNION
					( SELECT NVRAPINJAM.RP_QTY AS VALUE
						FROM TN_VRA_PINJAM NVRAPINJAM
					   WHERE     NVRAPINJAM.PERIOD_BUDGET = TNPJ.PERIOD_BUDGET
							 AND NVRAPINJAM.REGION_CODE = B.REGION_CODE
							 AND NVRAPINJAM.VRA_CODE = TNPJ.VRA_CODE_GRADER)),
					  0)
					  AS RP_HM_GRADER,
				   TNPJ.KAPASITAS_GRADER,
				   TNPJ.FLAG_TEMP
			  FROM TN_PERKERASAN_JALAN TNPJ
			  LEFT JOIN TM_ACTIVITY TMA
				ON TNPJ.ACTIVITY_CODE = TMA.ACTIVITY_CODE
			  LEFT JOIN TM_ORGANIZATION B
				ON TNPJ.BA_CODE = B.BA_CODE
			  WHERE TNPJ.DELETE_USER IS NULL
		  ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(TNPJ.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(TNPJ.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(TNPJ.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(TNPJ.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(TNPJ.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
		
		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(TNPJ.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
		
		
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(TNPJ.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.ACTIVITY_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TMA.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.VRA_CODE_DT) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.VRA_CODE_EXCAV) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.VRA_CODE_COMPACTOR) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TNPJ.VRA_CODE_GRADER) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }	
		
		$query .= "
			ORDER BY TNPJ.PERIOD_BUDGET, TNPJ.BA_CODE, TNPJ.ACTIVITY_CODE, TNPJ.VRA_CODE_DT, 
					 TNPJ.VRA_CODE_EXCAV, TNPJ.VRA_CODE_COMPACTOR, TNPJ.VRA_CODE_GRADER
		";
		return $query;

	}
	
	//menampilkan list norma Infrastruktur
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
			FROM TN_PERKERASAN_JALAN 
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND ACTIVITY_CODE = '{$params['ACTIVITY_CODE']}'
				AND LEBAR = '{$params['LEBAR']}'
				AND PANJANG = '{$params['PANJANG']}'
				AND TEBAL = '{$params['TEBAL']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function save($row = array())
    { 
		$result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		//if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		//print_r($row);die();
		$price = $this->_formula->get_NormaPerkerasanJalan($row);
		$row['PRICE'] = $price;
		$jumlah_material = $this->_formula->get_NormaPerkerasanJalan_QtyMaterial($row);

		//report perkerasan jalan
		$jarak = $this->_formula->get_NormaPerkerasanJalan_Jarak($row);
		$tripkm = $this->_formula->get_NormaPerkerasanJalan_TripKm($row);
		$harga = $this->_formula->get_NormaPerkerasanJalan_BiayaMaterial($row);
		
		//get jarak pp
		$sql = "
		SELECT ROWIDTOCHAR(ROWID) AS ROW_ID, JARAK_PP
		FROM TN_PERKERASAN_JALAN_HARGA
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
		";
		$roww = $this->_db->fetchAll($sql);
		
		foreach($roww as $id => $value) {	
			//semua dikali
			$tripd = $value['JARAK_PP'] * $jarak;
			$rpdt = $tripkm * $tripd;

			//update tn-harga
			$sql = "
				UPDATE TN_PERKERASAN_JALAN_HARGA 
				SET DT_TRIP = '$tripd', 
					DT_PRICE = '$rpdt' 
				WHERE ROWIDTOCHAR(ROWID) ='". $value['ROW_ID']."';
			";
			 
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		
		$hmexcav = $this->_formula->get_NormaPerkerasanJalan_HmExcav($row);
		$rpexcav = $hmexcav * (float)str_replace(",", "", $row['RP_HM_EXCAV']);
		$hmcompac = $this->_formula->get_NormaPerkerasanJalan_HmCompac($row);
		$rpcompac = $hmcompac * (float)str_replace(",", "", $row['RP_HM_COMP']);
		$hmgrader = $this->_formula->get_NormaPerkerasanJalan_HmGrader($row);
		$rpgrader = $hmgrader * (float)str_replace(",", "", $row['RP_HM_GRADER']);
		//$rpinternal = $harga + $rpdt + $rpexcav + $rpcompac + $rpgrader;
		$percent = $this->_formula->get_NormaPerkerasanJalan_PercentExternal($row);
		//$benefit = $rpinternal * $percent / 100;
		//$external = $rpinternal + $benefit;

		if ($row['ROW_ID']){
			$sql = "
				UPDATE TN_PERKERASAN_JALAN
				SET LEBAR = REPLACE('{$row['LEBAR']}',',',''),
					PANJANG = REPLACE('{$row['PANJANG']}',',',''),
					TEBAL = REPLACE('{$row['TEBAL']}',',',''),
					MATERIAL_CODE = '202090031',
					MATERIAL_QTY = REPLACE('{$jumlah_material}',',',''),
					PRICE = REPLACE('{$price}',',',''),
					VRA_CODE_DT = REPLACE('{$row['VRA_CODE_DT']}',',',''),
					RP_KM_DT = REPLACE('{$row['RP_KM_DT']}',',',''),
					KAPASITAS_DT = REPLACE('{$row['KAPASITAS_DT']}',',',''),
					KECEPATAN_DT = REPLACE('{$row['KECEPATAN_DT']}',',',''),
					JAM_KERJA_DT = REPLACE('{$row['JAM_KERJA_DT']}',',',''),
					VRA_CODE_EXCAV = '{$row['VRA_CODE_EXCAV']}',
					RP_HM_EXCAV = REPLACE('{$row['RP_HM_EXCAV']}',',',''),
					KAPASITAS_EXCAV = REPLACE('{$row['KAPASITAS_EXCAV']}',',',''),
					VRA_CODE_COMPACTOR = REPLACE('{$row['VRA_CODE_COMPACTOR']}',',',''),
					RP_HM_COMPACTOR = REPLACE('{$row['RP_HM_COMP']}',',',''),
					KAPASITAS_COMPACTOR = REPLACE('{$row['KAPASITAS_COMPACTOR']}',',',''),
					VRA_CODE_GRADER = REPLACE('{$row['VRA_CODE_GRADER']}',',',''),
					RP_HM_GRADER = REPLACE('{$row['RP_HM_GRADER']}',',',''),
					KAPASITAS_GRADER = REPLACE('{$row['KAPASITAS_GRADER']}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP = NULL
				WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			$this->_global->createSqlFile($row['filename'], $sql);
		
				
			
			$sql = "
				UPDATE TN_PERKERASAN_JALAN_HARGA
				SET MATERIAL_QTY = REPLACE('{$jumlah_material}',',',''),
					TRIP_MATERIAL = REPLACE('{$tripkm}',',',''),
					BIAYA_MATERIAL = REPLACE('{$harga}',',',''),
					EXCAV_HM = REPLACE('{$hmexcav}',',',''),
					EXCAV_PRICE = REPLACE('{$rpexcav}',',',''),
					COMPACTOR_HM = REPLACE('{$hmcompac}',',',''),
					COMPACTOR_PRICE = REPLACE('{$rpcompac}',',',''),
					GRADER_HM = REPLACE('{$hmgrader}',',',''),
					GRADER_PRICE = REPLACE('{$rpgrader}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."';
			";
				
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		
		return $result;		
	}
	
	public function updTnHarga($row = array()){
		$result = true;
		$percent = $this->_formula->get_NormaPerkerasanJalan_PercentExternal($row);
		if ($row['ROW_ID']){
			$sql = "
				SELECT ROWIDTOCHAR(ROWID) AS ROWWID, BIAYA_MATERIAL, DT_PRICE, EXCAV_PRICE, COMPACTOR_PRICE, GRADER_PRICE
					FROM TN_PERKERASAN_JALAN_HARGA
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
					";

					$rowww = $this->_db->fetchAll($sql);

			foreach($rowww as $id => $nilai) {
				//semua dikali
				$rpinternal = $nilai['BIAYA_MATERIAL'] + $nilai['DT_PRICE'] + $nilai['EXCAV_PRICE'] + $nilai['COMPACTOR_PRICE'] + $nilai['GRADER_PRICE'];
				$benefit = $rpinternal * $percent / 100;
				$external = $rpinternal + $benefit;

				//update tn-harga
				$sql = "
					UPDATE TN_PERKERASAN_JALAN_HARGA
					SET INTERNAL_PRICE = '$rpinternal',
						EXTERNAL_PERCENT = '$percent',
						EXTERNAL_BENEFIT = '$benefit',
						EXTERNAL_PRICE = '$external'
					WHERE  ROWIDTOCHAR(ROWID) ='". $nilai['ROWWID']."';
						
					UPDATE TN_PERKERASAN_JALAN
					SET FLAG_TEMP = NULL
					WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
				";
	 
				$this->_global->createSqlFile($row['filename'], $sql);
			}
		}
		return $result;
	}
	
	//simpan data
	public function saveTemp($row = array())
    { 
		$result = true;
		$price = $this->_formula->get_NormaPerkerasanJalan($row);
		$row['PRICE'] = $price;
		$jumlah_material = $this->_formula->get_NormaPerkerasanJalan_QtyMaterial($row);
		if ($row['ROW_ID']){
			$sql = "
				UPDATE TN_PERKERASAN_JALAN
				SET LEBAR = REPLACE('{$row['LEBAR']}',',',''),
					PANJANG = REPLACE('{$row['PANJANG']}',',',''),
					TEBAL = REPLACE('{$row['TEBAL']}',',',''),
					MATERIAL_CODE = '202090031',
					MATERIAL_QTY = REPLACE('{$jumlah_material}',',',''),
					PRICE = REPLACE('{$price}',',',''),
					VRA_CODE_DT = REPLACE('{$row['VRA_CODE_DT']}',',',''),
					RP_KM_DT = REPLACE('{$row['RP_KM_DT']}',',',''),
					KAPASITAS_DT = REPLACE('{$row['KAPASITAS_DT']}',',',''),
					KECEPATAN_DT = REPLACE('{$row['KECEPATAN_DT']}',',',''),
					JAM_KERJA_DT = REPLACE('{$row['JAM_KERJA_DT']}',',',''),
					VRA_CODE_EXCAV = '{$row['VRA_CODE_EXCAV']}',
					RP_HM_EXCAV = REPLACE('{$row['RP_HM_EXCAV']}',',',''),
					KAPASITAS_EXCAV = REPLACE('{$row['KAPASITAS_EXCAV']}',',',''),
					VRA_CODE_COMPACTOR = REPLACE('{$row['VRA_CODE_COMPACTOR']}',',',''),
					VRA_CODE_GRADER = REPLACE('{$row['VRA_CODE_GRADER']}',',',''),
					RP_HM_COMPACTOR = REPLACE('{$row['RP_HM_COMP']}',',',''),
					KAPASITAS_COMPACTOR = REPLACE('{$row['KAPASITAS_COMPACTOR']}',',',''),
					RP_HM_GRADER = REPLACE('{$row['RP_HM_GRADER']}',',',''),
					KAPASITAS_GRADER = REPLACE('{$row['KAPASITAS_GRADER']}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP = 'Y'
				WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
			
			$this->_global->createSqlFile($row['filename'], $sql);
				

			$sql = "
				UPDATE TN_PERKERASAN_JALAN_HARGA
				SET MATERIAL_QTY = NULL,
					TRIP_MATERIAL = NULL,
					BIAYA_MATERIAL = NULL,
					EXCAV_HM = NULL,
					EXCAV_PRICE = NULL,
					COMPACTOR_HM = NULL,
					COMPACTOR_PRICE = NULL,
					GRADER_HM = NULL,
					GRADER_PRICE = NULL,
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					INTERNAL_PRICE = NULL,
					EXTERNAL_PERCENT = NULL,
					EXTERNAL_BENEFIT = NULL,
					EXTERNAL_PRICE = NULL,
					DT_TRIP = NULL, 
					DT_PRICE = NULL
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."';
			";
				 
			$this->_global->createSqlFile($row['filename'], $sql);
		}
		
		return $result;		
	}
	
	public function updateInheritanceData($row = array())
    { 		
		$result = true;
		
		// ********************************************** UPDATE RKT PERKERASAN JALAN **********************************************
		//reset data
		$param = array();		
				
		$model = new Application_Model_RktPerkerasanJalan();
			
		//set parameter sesuai data yang diupdate
		$param['key_find'] = $row['BA_CODE'];
		
		$records1 = $this->_db->fetchAll("{$model->getData($param)}");
		
		if (!empty($records1)) {
			try {		
				foreach ($records1 as $idx1 => $record1) {
					//hitung cost element
					$model->calCostElement('MATERIAL', $record1);
					$model->calCostElement('TRANSPORT', $record1);
					$model->calCostElement('CONTRACT', $record1);
						
					//hitung total cost
					$model->calTotalCost($record1);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'RKT PERKERASAN JALAN', '', 'UPDATED FROM NORMA PERKERASAN JALAN');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'RKT PERKERASAN JALAN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF UPDATE RKT PERKERASAN JALAN **********************************************
		
		return $result;
	}
	
	//function untuk mengambil value VRA
	public function getVraValue($params = array())
    {
		$query = "
			SELECT VALUE  
			FROM TR_RKT_VRA_SUM
			WHERE 1 = 1 AND VRA_CODE = '".$params['vra_code']."'
			AND BA_CODE = '".$params['ba_code']."'
        ";
		
		if($params['period'] != ''){
			$query .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['period']."' 
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
            ";
		}else{
			$query .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
            ";
		}
		
		
		$result = $this->_db->fetchRow($query); 
		return $result;
	}
	
	
	//simpan data
	public function calculateData($row = array())
    { 
		$result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		//if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
		$price = $this->_formula->get_NormaPerkerasanJalan($row);
		$row['PRICE'] = $price;
		$jumlah_material = $this->_formula->get_NormaPerkerasanJalan_QtyMaterial($row);

		//report perkerasan jalan
		$jarak = $this->_formula->get_NormaPerkerasanJalan_Jarak($row);
		$tripkm = $this->_formula->get_NormaPerkerasanJalan_TripKm($row);
		$harga = $this->_formula->get_NormaPerkerasanJalan_BiayaMaterial($row);
		
		//get jarak pp
		$sql = "
		SELECT ROWIDTOCHAR(ROWID) AS ROW_ID, JARAK_PP
		FROM TN_PERKERASAN_JALAN_HARGA
			WHERE PERIOD_BUDGET = TO_DATE('".$this->_period."','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
		";
		$roww = $this->_db->fetchAll($sql);
		
		foreach($roww as $id => $value) {	
			//semua dikali
			$tripd = $value['JARAK_PP'] * $jarak;
			$rpdt = $tripkm * $tripd;

			//update tn-harga
			$sql = "
				UPDATE TN_PERKERASAN_JALAN_HARGA 
				SET DT_TRIP = '$tripd', 
					DT_PRICE = '$rpdt' 
				WHERE ROWIDTOCHAR(ROWID) ='". $value['ROW_ID']."';
			";
			
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		$hmexcav = $this->_formula->get_NormaPerkerasanJalan_HmExcav($row);
		$rpexcav = $hmexcav * (float)str_replace(",", "", $row['RP_HM_EXCAV']);
		$hmcompac = $this->_formula->get_NormaPerkerasanJalan_HmCompac($row);
		$rpcompac = $hmcompac * (float)str_replace(",", "", $row['RP_HM_COMP']);
		$hmgrader = $this->_formula->get_NormaPerkerasanJalan_HmGrader($row);
		$rpgrader = $hmgrader * (float)str_replace(",", "", $row['RP_HM_GRADER']);
		//$rpinternal = $harga + $rpdt + $rpexcav + $rpcompac + $rpgrader;
		$percent = $this->_formula->get_NormaPerkerasanJalan_PercentExternal($row);
		//$benefit = $rpinternal * $percent / 100;
		//$external = $rpinternal + $benefit;

		if ($row['ROW_ID']){
			$sql = "
				UPDATE TN_PERKERASAN_JALAN
				SET LEBAR = REPLACE('{$row['LEBAR']}',',',''),
					PANJANG = REPLACE('{$row['PANJANG']}',',',''),
					TEBAL = REPLACE('{$row['TEBAL']}',',',''),
					MATERIAL_CODE = '202090031',
					MATERIAL_QTY = REPLACE('{$jumlah_material}',',',''),
					PRICE = REPLACE('{$price}',',',''),
					VRA_CODE_DT = REPLACE('{$row['VRA_CODE_DT']}',',',''),
					RP_KM_DT = REPLACE('{$row['RP_KM_DT']}',',',''),
					KAPASITAS_DT = REPLACE('{$row['KAPASITAS_DT']}',',',''),
					KECEPATAN_DT = REPLACE('{$row['KECEPATAN_DT']}',',',''),
					JAM_KERJA_DT = REPLACE('{$row['JAM_KERJA_DT']}',',',''),
					KAPASITAS_EXCAV = REPLACE('{$row['KAPASITAS_EXCAV']}',',',''),
					VRA_CODE_COMPACTOR = REPLACE('{$row['VRA_CODE_COMPACTOR']}',',',''),
					RP_HM_COMPACTOR = REPLACE('{$row['RP_HM_COMP']}',',',''),
					KAPASITAS_COMPACTOR = REPLACE('{$row['KAPASITAS_COMPACTOR']}',',',''),
					VRA_CODE_GRADER = REPLACE('{$row['VRA_CODE_GRADER']}',',',''),
					RP_HM_GRADER = REPLACE('{$row['RP_HM_GRADER']}',',',''),
					KAPASITAS_GRADER = REPLACE('{$row['KAPASITAS_GRADER']}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP = 'Y'
				WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
			";
			$this->_db->query($sql);
			$this->_db->commit();
			
			$sql = "
				UPDATE TN_PERKERASAN_JALAN_HARGA
				SET MATERIAL_QTY = REPLACE('{$jumlah_material}',',',''),
					TRIP_MATERIAL = REPLACE('{$tripkm}',',',''),
					BIAYA_MATERIAL = REPLACE('{$harga}',',',''),
					EXCAV_HM = REPLACE('{$hmexcav}',',',''),
					EXCAV_PRICE = REPLACE('{$rpexcav}',',',''),
					COMPACTOR_HM = REPLACE('{$hmcompac}',',',''),
					COMPACTOR_PRICE = REPLACE('{$rpcompac}',',',''),
					GRADER_HM = REPLACE('{$hmgrader}',',',''),
					GRADER_PRICE = REPLACE('{$rpgrader}',',',''),
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE
				WHERE PERIOD_BUDGET = TO_DATE('".$this->_period."','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($row['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		return $result;		
	}
}

