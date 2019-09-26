<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Perencanaan Produksi
Function 			:	- getInput					: YIR 20/06/2014	: setting input untuk region
						- getData					: SID 04/07/2013	: ambil data dari DB
						- getDataDownload			: SID 04/07/2013	: ambil data untuk didownload ke excel dari DB
						- getList					: SID 04/07/2013	: menampilkan list Perencanaan Produksi
						- save						: SID 04/07/2013	: simpan data
						- getTemplateData			: SID 04/07/2013	: ambil data ha statement yang dibutuhkan dari DB untuk perencanaan produksi
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/07/2013
Update Terakhir		:	07/07/2014
Revisi				:	
	SID 07/07/2014	: 	- perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
						  pada function save
=========================================================================================================================
*/
class Application_Model_PerencanaanProduksi
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
            SELECT ROWIDTOCHAR(norma.ROWID) row_id, 
				   to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   norma.BA_CODE, 
				   norma.AFD_CODE,  
				   norma.BLOCK_CODE,  
				   hs.BLOCK_DESC,
				   norma.JARAK_PKS,
                   norma.PERSEN_LANGSIR,
				   norma.HA_SMS1,  
				   norma.POKOK_SMS1,  
				   norma.SPH_SMS1,  
				   norma.HA_SMS2,  
				   norma.POKOK_SMS2,  
				   norma.SPH_SMS2,  
				   norma.YPH_PROFILE,  
				   norma.TON_PROFILE,  
				   norma.YPH_PROPORTION,  
				   norma.TON_PROPORTION,  
				   norma.JANJANG_BUDGET,  
				   norma.BJR_BUDGET,  
				   norma.TON_BUDGET,  
				   norma.YPH_BUDGET,  
				   norma.JAN,  
				   norma.FEB,  
				   norma.MAR,  
				   norma.APR,  
				   norma.MAY,  
				   norma.JUN,  
				   norma.JUL,  
				   norma.AUG,  
				   norma.SEP,  
				   norma.OCT,  
				   norma.NOV,  
				   norma.DEC,  
				   norma.SMS1,  
				   norma.SMS2,
				   thn_berjalan.HA_PANEN, 
				   thn_berjalan.POKOK_PRODUKTIF, 
				   thn_berjalan.SPH_PRODUKTIF, 
				   thn_berjalan.TON_AKTUAL, 
				   thn_berjalan.JANJANG_AKTUAL, 
				   thn_berjalan.BJR_AKTUAL, 
				   thn_berjalan.YPH_AKTUAL, 
				   thn_berjalan.TON_TAKSASI, 
				   thn_berjalan.JANJANG_TAKSASI, 
				   thn_berjalan.TON_ANTISIPASI, 
				   thn_berjalan.JANJANG_ANTISIPASI, 
				   thn_berjalan.BJR_ANTISIPASI, 
				   thn_berjalan.YPH_ANTISIPASI, 
				   thn_berjalan.BJR_TAKSASI, 
				   thn_berjalan.YPH_TAKSASI, 
				   thn_berjalan.TON_BUDGET TON_BUDGET_TAHUN_BERJALAN, 
				   thn_berjalan.YPH_BUDGET YPH_BUDGET_TAHUN_BERJALAN, 
				   thn_berjalan.VAR_YPH
			FROM TR_PRODUKSI_PERIODE_BUDGET norma
			LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan
				ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
				AND norma.BA_CODE = thn_berjalan.BA_CODE
				AND norma.AFD_CODE = thn_berjalan.AFD_CODE
				AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON norma.BA_CODE = ORG.BA_CODE
			LEFT JOIN TM_HECTARE_STATEMENT hs
				ON norma.PERIOD_BUDGET = hs.PERIOD_BUDGET
               AND norma.BA_CODE = hs.BA_CODE
               AND norma.AFD_CODE = hs.AFD_CODE
               AND norma.BLOCK_CODE = hs.BLOCK_CODE
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
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(norma.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
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
					OR UPPER(norma.AFD_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(norma.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY norma.BA_CODE, norma.AFD_CODE, norma.BLOCK_CODE
		";
		//die($query);
		return $query;
	}
	
	//menampilkan list perencanaan produksi
    public function getList($params = array())
    {
        $result = array();

        $begin = "
            SELECT * FROM ( SELECT MY_TABLE.*
            FROM (
            SELECT ROWNUM MY_ROWNUM, TEMP.*
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
        $sql = "
			UPDATE TR_PRODUKSI_PERIODE_BUDGET
			SET JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				HA_SMS1 = REPLACE('{$row['HA_SMS1']}',',',''),
				POKOK_SMS1 = REPLACE('{$row['POKOK_SMS1']}',',',''),
				SPH_SMS1 = REPLACE('{$row['SPH_SMS1']}',',',''),
				HA_SMS2 = REPLACE('{$row['HA_SMS2']}',',',''),
				POKOK_SMS2 = REPLACE('{$row['POKOK_SMS2']}',',',''),
				SPH_SMS2 = REPLACE('{$row['SPH_SMS2']}',',',''),
				YPH_PROFILE = REPLACE('{$row['YPH_PROFILE']}',',',''),
				TON_PROFILE = REPLACE('{$row['TON_PROFILE']}',',',''),
				YPH_PROPORTION = REPLACE('{$row['YPH_PROPORTION']}',',',''),
				TON_PROPORTION = REPLACE('{$row['TON_PROPORTION']}',',',''),
				JANJANG_BUDGET = REPLACE('{$row['JANJANG_BUDGET']}',',',''),
				BJR_BUDGET = REPLACE('{$row['BJR_BUDGET']}',',',''),
				TON_BUDGET = REPLACE('{$row['TON_BUDGET']}',',',''),
				YPH_BUDGET = REPLACE('{$row['YPH_BUDGET']}',',',''),
				JAN = REPLACE('{$row['JAN']}',',',''),
				FEB = REPLACE('{$row['FEB']}',',',''),
				MAR = REPLACE('{$row['MAR']}',',',''),
				APR = REPLACE('{$row['APR']}',',',''),
				MAY = REPLACE('{$row['MAY']}',',',''),
				JUN = REPLACE('{$row['JUN']}',',',''),
				JUL = REPLACE('{$row['JUL']}',',',''),
				AUG = REPLACE('{$row['AUG']}',',',''),
				SEP = REPLACE('{$row['SEP']}',',',''),
				OCT = REPLACE('{$row['OCT']}',',',''),
				NOV = REPLACE('{$row['NOV']}',',',''),
				DEC = REPLACE('{$row['DEC']}',',',''),
				SMS1 = REPLACE('{$row['SMS1']}',',',''),
				SMS2 = REPLACE('{$row['SMS2']}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			 
			UPDATE TR_PRODUKSI_TAHUN_BERJALAN
			SET HA_PANEN = REPLACE('{$row['HA_PANEN']}',',',''),
				POKOK_PRODUKTIF = REPLACE('{$row['POKOK_PRODUKTIF']}',',',''), 
				SPH_PRODUKTIF = REPLACE('{$row['SPH_PRODUKTIF']}',',',''), 
				TON_AKTUAL = REPLACE('{$row['TON_AKTUAL']}',',',''), 
				JANJANG_AKTUAL = REPLACE('{$row['JANJANG_AKTUAL']}',',',''), 
				BJR_AKTUAL = REPLACE('{$row['BJR_AKTUAL']}',',',''), 
				YPH_AKTUAL = REPLACE('{$row['YPH_AKTUAL']}',',',''), 
				TON_TAKSASI = REPLACE('{$row['TON_TAKSASI']}',',',''), 
				JANJANG_TAKSASI = REPLACE('{$row['JANJANG_TAKSASI']}',',',''), 
				BJR_TAKSASI = REPLACE('{$row['BJR_TAKSASI']}',',',''), 
				YPH_TAKSASI = REPLACE('{$row['YPH_TAKSASI']}',',',''), 
				TON_BUDGET = REPLACE('{$row['TON_BUDGET_TAHUN_BERJALAN']}',',',''), 
				YPH_BUDGET = REPLACE('{$row['YPH_BUDGET_TAHUN_BERJALAN']}',',',''), 
				VAR_YPH = REPLACE('{$row['VAR_YPH']}',',',''),
				TON_ANTISIPASI = REPLACE('{$row['TON_ANTISIPASI']}',',',''),
				JANJANG_ANTISIPASI = REPLACE('{$row['JANJANG_ANTISIPASI']}',',',''),
				BJR_ANTISIPASI = REPLACE('{$row['BJR_ANTISIPASI']}',',',''),
				YPH_ANTISIPASI = REPLACE('{$row['YPH_ANTISIPASI']}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE to_char(PERIOD_BUDGET,'RRRR') = '{$row['PERIOD_BUDGET']}'
				AND BA_CODE = '{$row['BA_CODE']}'
				AND AFD_CODE = '{$row['AFD_CODE']}'
				AND BLOCK_CODE = '{$row['BLOCK_CODE']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
        return true;
    }
	
	//simpan data
	public function saveTemp($row = array())
    { 
        $sql = "
			UPDATE TR_PRODUKSI_PERIODE_BUDGET
			SET JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
				PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
				HA_SMS1 = REPLACE('{$row['HA_SMS1']}',',',''),
				POKOK_SMS1 = REPLACE('{$row['POKOK_SMS1']}',',',''),
				SPH_SMS1 = REPLACE('{$row['SPH_SMS1']}',',',''),
				HA_SMS2 = REPLACE('{$row['HA_SMS2']}',',',''),
				POKOK_SMS2 = REPLACE('{$row['POKOK_SMS2']}',',',''),
				SPH_SMS2 = REPLACE('{$row['SPH_SMS2']}',',',''),
				YPH_PROFILE = REPLACE('{$row['YPH_PROFILE']}',',',''),
				TON_PROFILE = REPLACE('{$row['TON_PROFILE']}',',',''),
				YPH_PROPORTION = REPLACE('{$row['YPH_PROPORTION']}',',',''),
				TON_PROPORTION = REPLACE('{$row['TON_PROPORTION']}',',',''),
				JANJANG_BUDGET = REPLACE('{$row['JANJANG_BUDGET']}',',',''),
				BJR_BUDGET = REPLACE('{$row['BJR_BUDGET']}',',',''),
				TON_BUDGET = REPLACE('{$row['TON_BUDGET']}',',',''),
				YPH_BUDGET = REPLACE('{$row['YPH_BUDGET']}',',',''),
				JAN = REPLACE('{$row['JAN']}',',',''),
				FEB = REPLACE('{$row['FEB']}',',',''),
				MAR = REPLACE('{$row['MAR']}',',',''),
				APR = REPLACE('{$row['APR']}',',',''),
				MAY = REPLACE('{$row['MAY']}',',',''),
				JUN = REPLACE('{$row['JUN']}',',',''),
				JUL = REPLACE('{$row['JUL']}',',',''),
				AUG = REPLACE('{$row['AUG']}',',',''),
				SEP = REPLACE('{$row['SEP']}',',',''),
				OCT = REPLACE('{$row['OCT']}',',',''),
				NOV = REPLACE('{$row['NOV']}',',',''),
				DEC = REPLACE('{$row['DEC']}',',',''),
				SMS1 = REPLACE('{$row['SMS1']}',',',''),
				SMS2 = REPLACE('{$row['SMS2']}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			 
			UPDATE TR_PRODUKSI_TAHUN_BERJALAN
			SET HA_PANEN = REPLACE('{$row['HA_PANEN']}',',',''),
				POKOK_PRODUKTIF = REPLACE('{$row['POKOK_PRODUKTIF']}',',',''), 
				SPH_PRODUKTIF = REPLACE('{$row['SPH_PRODUKTIF']}',',',''), 
				TON_AKTUAL = REPLACE('{$row['TON_AKTUAL']}',',',''), 
				JANJANG_AKTUAL = REPLACE('{$row['JANJANG_AKTUAL']}',',',''), 
				BJR_AKTUAL = REPLACE('{$row['BJR_AKTUAL']}',',',''), 
				YPH_AKTUAL = REPLACE('{$row['YPH_AKTUAL']}',',',''), 
				TON_TAKSASI = REPLACE('{$row['TON_TAKSASI']}',',',''), 
				JANJANG_TAKSASI = REPLACE('{$row['JANJANG_TAKSASI']}',',',''), 
				BJR_TAKSASI = REPLACE('{$row['BJR_TAKSASI']}',',',''), 
				YPH_TAKSASI = REPLACE('{$row['YPH_TAKSASI']}',',',''), 
				TON_BUDGET = REPLACE('{$row['TON_BUDGET_TAHUN_BERJALAN']}',',',''), 
				YPH_BUDGET = REPLACE('{$row['YPH_BUDGET_TAHUN_BERJALAN']}',',',''), 
				VAR_YPH = REPLACE('{$row['VAR_YPH']}',',',''),
				TON_ANTISIPASI = REPLACE('{$row['TON_ANTISIPASI']}',',',''),
				JANJANG_ANTISIPASI = REPLACE('{$row['JANJANG_ANTISIPASI']}',',',''),
				BJR_ANTISIPASI = REPLACE('{$row['BJR_ANTISIPASI']}',',',''),
				YPH_ANTISIPASI = REPLACE('{$row['YPH_ANTISIPASI']}',',',''),
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE to_char(PERIOD_BUDGET,'RRRR') = '{$row['PERIOD_BUDGET']}'
				AND BA_CODE = '{$row['BA_CODE']}'
				AND AFD_CODE = '{$row['AFD_CODE']}'
				AND BLOCK_CODE = '{$row['BLOCK_CODE']}';
		";
		
		//create sql file
		$this->_global->createSqlFile($row['filename'], $sql);
		
        return true;
    }
	
	//ambil data ha statement yang dibutuhkan dari DB untuk perencanaan produksi
    public function getTemplateData($params = array())
    {
		$query = "
            SELECT ha.BA_CODE, 
				   ha.AFD_CODE, 
				   ha.BLOCK_CODE, 
				   ha.BLOCK_DESC, 
				   ha.LAND_TYPE, 
				   ha.PROGENY, 
				   ha.LAND_SUITABILITY, 
				   to_char(ha.TAHUN_TANAM,'RRRR') YEAR_PLANT,  
				   to_char(ha.TAHUN_TANAM,'MON') MONTH_PLANT, 
				   org.REGION_CODE,
				   ha.POKOK_TANAM,
				   ha.SPH,
				   ha.HA_PLANTED,
				   tppb.JARAK_PKS,
				   tppb.PERSEN_LANGSIR
            FROM TM_HECTARE_STATEMENT ha
			LEFT JOIN TM_ORGANIZATION org
				ON ha.BA_CODE = org.BA_CODE
			LEFT JOIN TR_PRODUKSI_PERIODE_BUDGET tppb
				ON ha.BA_CODE = tppb.BA_CODE AND ha.AFD_CODE = tppb.AFD_CODE
				AND ha.BLOCK_CODE = tppb.BLOCK_CODE AND ha.PERIOD_BUDGET = tppb.PERIOD_BUDGET
			WHERE ha.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HA.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(ha.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(ha.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(ha.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(ha.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(ha.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(ha.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(ha.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(ha.AFD_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(ha.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
					
				)
            ";
        }
		
		$query .= "
			ORDER BY ha.BA_CODE, ha.AFD_CODE, ha.BLOCK_CODE
		";
		
		return $query;
	}

  public function finalizeRktPanenLangsir($params)
  {
    $sql = "UPDATE (
              SELECT RKT.JARAK_PKS, RKT.PERSEN_LANGSIR, BP.JARAK_PKS JARAK, BP.PERSEN_LANGSIR LANGSIR
              FROM TR_RKT_PANEN RKT
              LEFT JOIN TR_PRODUKSI_PERIODE_BUDGET BP ON BP.AFD_CODE = RKT.AFD_CODE AND BP.BA_CODE = RKT.BA_CODE
              AND BP.BLOCK_CODE = RKT.BLOCK_CODE AND BP.PERIOD_BUDGET = RKT.PERIOD_BUDGET
              WHERE EXTRACT(YEAR FROM RKT.period_budget) = ".$params['budgetperiod']."
              AND RKT.BA_CODE = '".$params['key_find']."'
            ) SET JARAK_PKS = JARAK, PERSEN_LANGSIR = LANGSIR";

    $this->_db->beginTransaction();
    $this->_db->query($sql);
    $this->_db->commit();

  }
}

