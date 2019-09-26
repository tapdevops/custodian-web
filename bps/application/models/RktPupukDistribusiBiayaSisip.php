<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk RKT Pupuk Distribusi Biaya Sisip
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list RKT Pupuk Pupuk Distribusi Biaya Sisip
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	18/07/2013
Update Terakhir		:	18/07/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_RktPupukDistribusiBiayaSisip
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
		
		$options['optMatStage'] = $table->getMaturityStage();
		$result['src_matstage_code'] = array(
            'type'    => 'select',
            'name'    => 'src_matstage_code',
            'value'   => '',
            'options' => $options['optMatStage'],
            'ext'     => '',
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
            SELECT to_char(ha_statement.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
				   ha_statement.BA_CODE,
				   ORG.COMPANY_NAME,
				   ha_statement.AFD_CODE, 
				   ha_statement.BLOCK_CODE,
				   ha_statement.BLOCK_DESC, 
				   ha_statement.LAND_TYPE,
				   ha_statement.TOPOGRAPHY,
				   to_char(ha_statement.TAHUN_TANAM,'MM.RRRR') TAHUN_TANAM, 
				   to_char(ha_statement.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
				   to_char(ha_statement.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
				   ha_statement.MATURITY_STAGE_SMS1, 
				   ha_statement.MATURITY_STAGE_SMS2, 
				   ha_statement.HA_PLANTED, 
				   ha_statement.POKOK_TANAM, 
				   ha_statement.SPH,
				   SUM(rkt.DIS_COST_JAN) JAN, 
				   SUM(rkt.DIS_COST_FEB) FEB, 
				   SUM(rkt.DIS_COST_MAR) MAR, 
				   SUM(rkt.DIS_COST_APR) APR, 
				   SUM(rkt.DIS_COST_MAY) MAY, 
				   SUM(rkt.DIS_COST_JUN) JUN, 
				   SUM(rkt.DIS_COST_JUL) JUL, 
				   SUM(rkt.DIS_COST_AUG) AUG, 
				   SUM(rkt.DIS_COST_SEP) SEP, 
				   SUM(rkt.DIS_COST_OCT) OCT, 
				   SUM(rkt.DIS_COST_NOV) NOV, 
				   SUM(rkt.DIS_COST_DEC) DEC, 
				   SUM(rkt.DIS_COST_YEAR) SETAHUN,
				   MAX(rkt.COST_TRANSPORT_KG) COST_TRANSPORT_KG, 
				   MAX(rkt.COST_TOOLS_KG) COST_TOOLS_KG, 
				   MAX(rkt.COST_LABOUR_POKOK) COST_LABOUR_POKOK,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_JAN) PUPUK_JAN,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_FEB) PUPUK_FEB,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_MAR) PUPUK_MAR,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_APR) PUPUK_APR,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_MAY) PUPUK_MAY,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_JUN) PUPUK_JUN,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_JUL) PUPUK_JUL,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_AUG) PUPUK_AUG,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_SEP) PUPUK_SEP,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_OCT) PUPUK_OCT,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_NOV) PUPUK_NOV,
				   MAX(NAMA_PUPUK.MATERIAL_NAME_DEC) PUPUK_DEC,
                   MAX(NAMA_PUPUK.MATERIAL_JAN) MATERIAL_CODE_JAN,
                   MAX(NAMA_PUPUK.MATERIAL_FEB) MATERIAL_CODE_FEB,
                   MAX(NAMA_PUPUK.MATERIAL_MAR) MATERIAL_CODE_MAR,
                   MAX(NAMA_PUPUK.MATERIAL_APR) MATERIAL_CODE_APR,
                   MAX(NAMA_PUPUK.MATERIAL_MAY) MATERIAL_CODE_MAY,
                   MAX(NAMA_PUPUK.MATERIAL_JUN) MATERIAL_CODE_JUN,
                   MAX(NAMA_PUPUK.MATERIAL_JUL) MATERIAL_CODE_JUL,
                   MAX(NAMA_PUPUK.MATERIAL_AUG) MATERIAL_CODE_AUG,
                   MAX(NAMA_PUPUK.MATERIAL_SEP) MATERIAL_CODE_SEP,
                   MAX(NAMA_PUPUK.MATERIAL_OCT) MATERIAL_CODE_OCT,
                   MAX(NAMA_PUPUK.MATERIAL_NOV) MATERIAL_CODE_NOV,
                   MAX(NAMA_PUPUK.MATERIAL_DEC) MATERIAL_CODE_DEC
			FROM TR_RKT_PUPUK_COST_ELEMENT rkt
			LEFT JOIN TM_HECTARE_STATEMENT ha_statement
				ON rkt.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET
				AND rkt.BA_CODE = ha_statement.BA_CODE
				AND rkt.AFD_CODE = ha_statement.AFD_CODE
				AND rkt.BLOCK_CODE = ha_statement.BLOCK_CODE
				AND rkt.TIPE_TRANSAKSI = 'SISIP'
			LEFT JOIN (SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE,
						      LISTAGG (MATERIAL_NAME_JAN, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_JAN,
						      LISTAGG (MATERIAL_NAME_FEB, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_FEB,
						      LISTAGG (MATERIAL_NAME_MAR, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_MAR,
						      LISTAGG (MATERIAL_NAME_APR, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_APR,
						      LISTAGG (MATERIAL_NAME_MAY, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_MAY,
						      LISTAGG (MATERIAL_NAME_JUN, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_JUN,
						      LISTAGG (MATERIAL_NAME_JUL, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_JUL,
						      LISTAGG (MATERIAL_NAME_AUG, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_AUG,
						      LISTAGG (MATERIAL_NAME_SEP, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_SEP,
						      LISTAGG (MATERIAL_NAME_OCT, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_OCT,
						      LISTAGG (MATERIAL_NAME_NOV, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_NOV,
						      LISTAGG (MATERIAL_NAME_DEC, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_DEC,
                              LISTAGG (MATERIAL_JAN, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_JAN,
                              LISTAGG (MATERIAL_FEB, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_FEB,
                              LISTAGG (MATERIAL_MAR, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_MAR,
                              LISTAGG (MATERIAL_APR, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_APR,
                              LISTAGG (MATERIAL_MAY, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_MAY,
                              LISTAGG (MATERIAL_JUN, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_JUN,
                              LISTAGG (MATERIAL_JUL, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_JUL,
                              LISTAGG (MATERIAL_AUG, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_AUG,
                              LISTAGG (MATERIAL_SEP, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_SEP,
                              LISTAGG (MATERIAL_OCT, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_OCT,
                              LISTAGG (MATERIAL_NOV, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NOV,
                              LISTAGG (MATERIAL_DEC, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_DEC
					   FROM (SELECT DISTINCT PPK.PERIOD_BUDGET, PPK.BA_CODE, PPK.AFD_CODE, PPK.BLOCK_CODE, 
									TM_JAN.MATERIAL_NAME AS MATERIAL_NAME_JAN,
									TM_FEB.MATERIAL_NAME AS MATERIAL_NAME_FEB,
									TM_MAR.MATERIAL_NAME AS MATERIAL_NAME_MAR,
									TM_APR.MATERIAL_NAME AS MATERIAL_NAME_APR,
									TM_MAY.MATERIAL_NAME AS MATERIAL_NAME_MAY,
									TM_JUN.MATERIAL_NAME AS MATERIAL_NAME_JUN,
									TM_JUL.MATERIAL_NAME AS MATERIAL_NAME_JUL,
									TM_AUG.MATERIAL_NAME AS MATERIAL_NAME_AUG,
									TM_SEP.MATERIAL_NAME AS MATERIAL_NAME_SEP,
									TM_OCT.MATERIAL_NAME AS MATERIAL_NAME_OCT,
									TM_NOV.MATERIAL_NAME AS MATERIAL_NAME_NOV,
									TM_DEC.MATERIAL_NAME AS MATERIAL_NAME_DEC,
                                    PPK.MATERIAL_CODE_JAN AS MATERIAL_JAN,
                                    PPK.MATERIAL_CODE_FEB AS MATERIAL_FEB,
                                    PPK.MATERIAL_CODE_MAR AS MATERIAL_MAR,
                                    PPK.MATERIAL_CODE_APR AS MATERIAL_APR,
                                    PPK.MATERIAL_CODE_MAY AS MATERIAL_MAY,
                                    PPK.MATERIAL_CODE_JUN AS MATERIAL_JUN,
                                    PPK.MATERIAL_CODE_JUL AS MATERIAL_JUL,
                                    PPK.MATERIAL_CODE_AUG AS MATERIAL_AUG,
                                    PPK.MATERIAL_CODE_SEP AS MATERIAL_SEP,
                                    PPK.MATERIAL_CODE_OCT AS MATERIAL_OCT,
                                    PPK.MATERIAL_CODE_NOV AS MATERIAL_NOV,
                                    PPK.MATERIAL_CODE_DEC AS MATERIAL_DEC
                             FROM TR_RKT_PUPUK_DISTRIBUSI PPK
                             LEFT JOIN TM_MATERIAL TM_JAN
								ON TM_JAN.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_JAN.BA_CODE = PPK.BA_CODE
                                AND TM_JAN.MATERIAL_CODE = PPK.MATERIAL_CODE_JAN
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_FEB
                                ON TM_FEB.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_FEB.BA_CODE = PPK.BA_CODE
                                AND TM_FEB.MATERIAL_CODE = PPK.MATERIAL_CODE_FEB
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_MAR
                                ON TM_MAR.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_MAR.BA_CODE = PPK.BA_CODE
                                AND TM_MAR.MATERIAL_CODE = PPK.MATERIAL_CODE_MAR
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_APR
                                ON TM_APR.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_APR.BA_CODE = PPK.BA_CODE
                                AND TM_APR.MATERIAL_CODE = PPK.MATERIAL_CODE_APR
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_MAY
                                ON TM_MAY.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_MAY.BA_CODE = PPK.BA_CODE
                                AND TM_MAY.MATERIAL_CODE = PPK.MATERIAL_CODE_MAY
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_JUN
                                ON TM_JUN.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_JUN.BA_CODE = PPK.BA_CODE
                                AND TM_JUN.MATERIAL_CODE = PPK.MATERIAL_CODE_JUN
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_JUL
                                ON TM_JUL.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_JUL.BA_CODE = PPK.BA_CODE
                                AND TM_JUL.MATERIAL_CODE = PPK.MATERIAL_CODE_JUL
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_AUG
                                ON TM_AUG.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_AUG.BA_CODE = PPK.BA_CODE
                                AND TM_AUG.MATERIAL_CODE = PPK.MATERIAL_CODE_AUG
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_SEP
                                ON TM_SEP.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_SEP.BA_CODE = PPK.BA_CODE
                                AND TM_SEP.MATERIAL_CODE = PPK.MATERIAL_CODE_SEP
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_OCT
                                ON TM_OCT.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_OCT.BA_CODE = PPK.BA_CODE
                                AND TM_OCT.MATERIAL_CODE = PPK.MATERIAL_CODE_OCT
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_NOV
                                ON TM_NOV.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_NOV.BA_CODE = PPK.BA_CODE
                                AND TM_NOV.MATERIAL_CODE = PPK.MATERIAL_CODE_NOV
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP'
                             LEFT JOIN TM_MATERIAL TM_DEC
                                ON TM_DEC.PERIOD_BUDGET = PPK.PERIOD_BUDGET
                                AND TM_DEC.BA_CODE = PPK.BA_CODE
                                AND TM_DEC.MATERIAL_CODE = PPK.MATERIAL_CODE_DEC
                                AND PPK.TIPE_TRANSAKSI = 'KG_SISIP')
					   GROUP BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) NAMA_PUPUK
				ON RKT.BA_CODE = NAMA_PUPUK.BA_CODE 
				AND NAMA_PUPUK.PERIOD_BUDGET = RKT.PERIOD_BUDGET 
				AND NAMA_PUPUK.AFD_CODE = RKT.AFD_CODE 
				AND NAMA_PUPUK.BLOCK_CODE = RKT.BLOCK_CODE	
			LEFT JOIN TM_ORGANIZATION ORG
				ON ha_statement.BA_CODE = ORG.BA_CODE
			WHERE ha_statement.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ha_statement.BA_CODE)||'%'";
		}
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}elseif($params['PERIOD_BUDGET'] != ''){
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
		}else{
			$query .= "
                AND to_char(ha_statement.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$query .= "
                AND UPPER(ha_statement.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }

		//jika diupdate dari norma VRA, filter berdasarkan BA
		if ($params['BA_CODE'] != '') {
			$query .= "
                AND UPPER(ha_statement.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
		
		//filter maturity_stage
		if (($params['src_matstage_code']) && ($params['src_matstage_code'] != 'ALL')) {
			$query .= "
                AND (
					UPPER(ha_statement.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
					OR UPPER(ha_statement.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
				)
            ";
        }
		
		//filter afdeling
		if ($params['src_afd'] != '') {
			$query .= "
                AND UPPER(ha_statement.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
		
		//filter jenis pupuk
		if ($params['src_jenis_pupuk'] != '') {
			$query .= "
                AND (
					UPPER(NAMA_PUPUK.MATERIAL_JAN) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_FEB) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_MAR) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_APR) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_MAY) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_JUN) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_JUL) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_AUG) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_SEP) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_OCT) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_NOV) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
					OR UPPER(NAMA_PUPUK.MATERIAL_DEC) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
				)
            ";
        }
		
		$query .= "
			GROUP BY to_char(ha_statement.PERIOD_BUDGET,'RRRR'), ha_statement.BA_CODE, ORG.COMPANY_NAME, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE, 
					 ha_statement.BLOCK_DESC, ha_statement.LAND_TYPE, ha_statement.TOPOGRAPHY, to_char(ha_statement.TAHUN_TANAM,'MM.RRRR'),
				   to_char(ha_statement.TAHUN_TANAM,'MM') , 
				   to_char(ha_statement.TAHUN_TANAM,'RRRR') ,  ha_statement.MATURITY_STAGE_SMS1, ha_statement.MATURITY_STAGE_SMS2, ha_statement.HA_PLANTED, ha_statement.POKOK_TANAM, ha_statement.SPH
			ORDER BY ha_statement.BA_CODE, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE
		";
		return $query;
	}
	
	//menampilkan list Report Pupuk Distribusi Biaya Normal
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
	
	//hitung cost element
	public function calCostElement($costElement, $row = array())
    { 
        $result = true;
		$arr = array();
		$total_sms1 = $total_sms2 = 0;
		$row['TOTAL_COST_ELEMENT'] = 0;
		
		//hitung cost element
		if($costElement == 'LABOUR'){
			if($row['STATUS'] == 'MAKRO'){
				$act_code = '43750';
			}else if($row['STATUS'] == 'MIKRO'){$act_code = '43751';}
			else if($row['STATUS'] == 'TANKOS'){$act_code = '43751';}
			else {$act_code = '43770';}
			$sql = "
				SELECT TOPOGRAPHY, LAND_TYPE, SUM(RP_QTY_INTERNAL) TOTAL_COST_LABOUR
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".$row['BA_CODE']."'
					AND COST_ELEMENT = 'LABOUR'
					AND ACTIVITY_CODE IN ('".$act_code."') -- AKTIVITAS : TABUR PUPUK, UNTIL PUPUK -- tambahan untuk pupuk maksro mikro (43750, 43751)
					-- AND ACTIVITY_CLASS = 'ALL'
					AND TOPOGRAPHY IN ('ALL', '".$row['TOPOGRAPHY']."')
					AND LAND_TYPE IN ('ALL', '".$row['LAND_TYPE']."')
				GROUP BY TOPOGRAPHY, LAND_TYPE
			";
			$ress = $this->_db->fetchRow($sql);
			$row['TOTAL_COST_ELEMENT'] = $ress['TOTAL_COST_LABOUR'];
			$arr['COST_LABOUR_POKOK'] = $row['TOTAL_COST_ELEMENT'];
			
		}elseif($costElement == 'TOOLS'){
			if($row['STATUS'] == 'MAKRO'){
				$act_code = '43750';
			}else if($row['STATUS'] == 'MIKRO'){$act_code = '43751';}
			else if($row['STATUS'] == 'TANKOS'){$act_code = '43751';}
			else {$act_code = '43770';}
			$sql = "
				SELECT TOPOGRAPHY, LAND_TYPE, SUM(RP_QTY_INTERNAL) TOTAL_COST_TOOLS
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".$row['BA_CODE']."'
					AND COST_ELEMENT = 'TOOLS'
					AND ACTIVITY_CODE IN ('".$act_code."') -- AKTIVITAS : TABUR PUPUK, UNTIL PUPUK
					-- AND ACTIVITY_CLASS = 'ALL'
					AND TOPOGRAPHY IN ('ALL', '".$row['TOPOGRAPHY']."')
					AND LAND_TYPE IN ('ALL', '".$row['LAND_TYPE']."')
				GROUP BY TOPOGRAPHY, LAND_TYPE
			";
			$ress = $this->_db->fetchRow($sql);
			$row['TOTAL_COST_ELEMENT'] = $ress['TOTAL_COST_TOOLS'];
			$arr['COST_TOOLS_KG'] = $row['TOTAL_COST_ELEMENT'];
			
		}elseif($costElement == 'TRANSPORT'){
			$total_kg = $this->_formula->get_RktPupuk_TotalKg($row);
			$sql = "
				SELECT SUM(TOTAL_PRICE_HM_KM) TOTAL_COST_TRANSPORTASI
				FROM TR_RKT_VRA_DISTRIBUSI_SUM
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".$row['BA_CODE']."'
					AND ACTIVITY_CODE IN ('43750', '43760') -- AKTIVITAS : TABUR PUPUK, UNTIL PUPUK
			";
			//die($sql);
			$cost_transport = $this->_db->fetchOne($sql);
			$row['TOTAL_COST_ELEMENT'] = ($total_kg) ? $cost_transport/$total_kg : 0;
			$arr['COST_TRANSPORT_KG'] = $row['TOTAL_COST_ELEMENT'];
			
		}
		
		if($costElement == 'MATERIAL'){
			$total = array();
			$umur_tanaman_jan = $this->_formula->cal_RktPupuk_SelisihBulan($row['TAHUN_TANAM']);
		
			for ($mBudget = 1 ; $mBudget <= 12 ; $mBudget++){
				$umur_tanaman = $umur_tanaman_jan + $mBudget - 1; 
				
				if (($mBudget == 1) || ($mBudget == 7)){
					$maturity_stage = ($mBudget == 1) ? $row['MATURITY_STAGE_SMS1'] : $row['MATURITY_STAGE_SMS2'];
					//cari jenis norma pupuk
					$sql = "
						SELECT PARAMETER_VALUE
						FROM T_PARAMETER_VALUE
						WHERE DELETE_USER IS NULL
							AND PARAMETER_VALUE_CODE = '".$maturity_stage."'
							AND PARAMETER_CODE = 'STATUS_PUPUK'
					";
					$jenis_norma_pupuk = $this->_db->fetchOne($sql);
				}
				
				if($umur_tanaman < 0){
					$total[$mBudget] = 0;
				}else{
					//perhitungan untuk norma pupuk TBM 2 Less
					if($jenis_norma_pupuk == 'TN_PUPUK_TBM2_LESS'){
						$total[$mBudget] = 0;
					}
					
					//perhitungan untuk norma pupuk TBM 2 - TM
					else{
						$sql = "
							SELECT SUM(BIAYA) BIAYA
							FROM TN_PUPUK_TBM2_TM
							WHERE DELETE_USER IS NULL
								AND JENIS_TANAM = 'SISIP' 
								AND PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
								AND BA_CODE = '".$row['BA_CODE']."'
								AND AFD_CODE = '".$row['AFD_CODE']."'
								AND BLOCK_CODE = '".$row['BLOCK_CODE']."'
								AND BULAN_PEMUPUKAN = '".$mBudget."'
						";
						//die($sql);
						$result = $this->_db->fetchOne($sql);
						
						$total[$mBudget] = ( $result ) ? $result : 0;
					}
				}
			}
		}else{
			$row['TIPE_POKOK'] = 'KG_SISIP';
			$jumlah_pokok = $this->_formula->get_RktPupuk_JumlahPokok($row);
			
			$total[1] = $jumlah_pokok['JAN'] * $row['TOTAL_COST_ELEMENT'];
			$total[2] = $jumlah_pokok['FEB'] * $row['TOTAL_COST_ELEMENT'];
			$total[3] = $jumlah_pokok['MAR'] * $row['TOTAL_COST_ELEMENT'];
			$total[4] = $jumlah_pokok['APR'] * $row['TOTAL_COST_ELEMENT'];
			$total[5] = $jumlah_pokok['MAY'] * $row['TOTAL_COST_ELEMENT'];
			$total[6] = $jumlah_pokok['JUN'] * $row['TOTAL_COST_ELEMENT'];
			$total[7] = $jumlah_pokok['JUL'] * $row['TOTAL_COST_ELEMENT'];
			$total[8] = $jumlah_pokok['AUG'] * $row['TOTAL_COST_ELEMENT'];
			$total[9] = $jumlah_pokok['SEP'] * $row['TOTAL_COST_ELEMENT'];
			$total[10] = $jumlah_pokok['OCT'] * $row['TOTAL_COST_ELEMENT'];
			$total[11] = $jumlah_pokok['NOV'] * $row['TOTAL_COST_ELEMENT'];
			$total[12] = $jumlah_pokok['DEC'] * $row['TOTAL_COST_ELEMENT'];
		}
		
		//total/sms
		$total_sms1 += $total[1];
		$total_sms1 += $total[2];
		$total_sms1 += $total[3];
		$total_sms1 += $total[4];
		$total_sms1 += $total[5];
		$total_sms1 += $total[6];
		$total_sms2 += $total[7];
		$total_sms2 += $total[8];
		$total_sms2 += $total[9];
		$total_sms2 += $total[10];
		$total_sms2 += $total[11];
		$total_sms2 += $total[12];
		
		//hapus data lama
		$query = "
			DELETE FROM TR_RKT_PUPUK_COST_ELEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".$row['BA_CODE']."'
				AND AFD_CODE = '".$row['AFD_CODE']."'
				AND BLOCK_CODE = '".$row['BLOCK_CODE']."'
				AND COST_ELEMENT = '".addslashes($costElement)."'	
				AND TIPE_TRANSAKSI = 'SISIP';
		";
				
		//save hasil cost element
		$query .= "
			INSERT INTO TR_RKT_PUPUK_COST_ELEMENT (
				PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE, 
				MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN, 
				DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC, DIS_COST_YEAR, INSERT_USER, INSERT_TIME, 
				COST_TRANSPORT_KG, COST_TOOLS_KG, COST_LABOUR_POKOK, COST_SMS1, COST_SMS2
			)
			VALUES (
				TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
				'".addslashes($row['BA_CODE'])."',
				'".addslashes($row['AFD_CODE'])."',
				'".addslashes($row['BLOCK_CODE'])."',
				'".addslashes($costElement)."',
				'SISIP',
				".$row['PERIOD_BUDGET']." || '-' || '".addslashes($row['BA_CODE'])."' || '-' || '".addslashes($row['AFD_CODE'])."'
					|| '-' || '".addslashes($row['BLOCK_CODE'])."' || '-' || 'RKT011',
				'".addslashes($row['MATURITY_STAGE_SMS1'])."',
				'".addslashes($row['MATURITY_STAGE_SMS2'])."',
				REPLACE('".addslashes($total[1])."',',',''),
				REPLACE('".addslashes($total[2])."',',',''),
				REPLACE('".addslashes($total[3])."',',',''),
				REPLACE('".addslashes($total[4])."',',',''),
				REPLACE('".addslashes($total[5])."',',',''),
				REPLACE('".addslashes($total[6])."',',',''),
				REPLACE('".addslashes($total[7])."',',',''),
				REPLACE('".addslashes($total[8])."',',',''),
				REPLACE('".addslashes($total[9])."',',',''),
				REPLACE('".addslashes($total[10])."',',',''),
				REPLACE('".addslashes($total[11])."',',',''),
				REPLACE('".addslashes($total[12])."',',',''),
				REPLACE('".addslashes(array_sum($total))."',',',''),
				'{$this->_userName}',
				SYSDATE,
				REPLACE('".addslashes($arr['COST_TRANSPORT_KG'])."',',',''),
				REPLACE('".addslashes($arr['COST_TOOLS_KG'])."',',',''),
				REPLACE('".addslashes($arr['COST_LABOUR_POKOK'])."',',',''),
				REPLACE('".addslashes($total_sms1)."',',',''),
				REPLACE('".addslashes($total_sms2)."',',','')
			);
		";
		//die($query);
		//create sql file
		$this->_global->createSqlFile($row['filename'], $query);
					
        return $result;
    }
	
	//kalkulasi seluruh data RKT Pupuk Distribusi Biaya sisip
    public function getInheritData($params = array())
    {
		$result = true;
        //cari data
		$sql = "
			SELECT DISTINCT TO_CHAR(THS.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, THS.BA_CODE, AFD_CODE, BLOCK_CODE, 
				LAND_TYPE, TAHUN_TANAM, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, HA_PLANTED, POKOK_TANAM, TOPOGRAPHY,
                upper(TMAT.FLAG) AS STATUS
			FROM TM_HECTARE_STATEMENT THS
			LEFT JOIN TM_MATERIAL TMAT
				ON TMAT.PERIOD_BUDGET = THS.PERIOD_BUDGET
				AND TMAT.BA_CODE = THS.BA_CODE
			WHERE THS.DELETE_USER IS NULL 
			AND SUBSTR(BLOCK_CODE, 1, 3) <> 'ZZ_'
		";
		if($params['PERIOD_BUDGET']){
			$sql .= "AND THS.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') ";
		}
		if($params['budgetperiod']){
			$sql .= "AND THS.PERIOD_BUDGET = TO_DATE('01-01-{$params['budgetperiod']}','DD-MM-RRRR') ";
		}
		if($params['BA_CODE']){
			$sql .= "AND THS.BA_CODE IN ('".$params['BA_CODE']."') ";
		}
		if($params['key_find']){
			$sql .= "AND THS.BA_CODE IN ('".$params['key_find']."') ";
		}
		if($params['afd_code']){
			$sql .= "AND AFD_CODE = '".$params['afd_code']."' ";
		}
		if($params['block_code']){
			$sql .= "AND BLOCK_CODE = '".$params['block_code']."' ";
		}
		if(($params['land_type']) && ($params['land_type'] <> 'ALL')){
			$sql .= "AND LAND_TYPE = '".$params['land_type']."' ";
		}
		if(($params['topo']) && ($params['topo'] <> 'ALL')){
			$sql .= "AND TOPOGRAPHY = '".$params['topo']."' ";
		}
		//filter maturity_stage
		if (($params['maturity_stage']) && ($params['maturity_stage'] != 'ALL')) {
			$sql .= "
                AND (
					UPPER(MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['maturity_stage']."%')
					OR UPPER(MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['maturity_stage']."%')
				)
            ";
        }
		if($params['MATERIAL_CODE']){
			$sql .= "AND TMAT.MATERIAL_CODE = '".$params['MATERIAL_CODE']."' ";
		}
		return $sql;
    }

  public function calculateToolSisipManual($params) {
    $sql = "MERGE INTO TR_RKT_PUPUK_COST_ELEMENT COST
            USING (
              SELECT TOOLS.*, CE.TRX_RKT_CODE FROM (
                WITH BIAYA AS (
                  SELECT DISTINCT C1.PERIOD_BUDGET, C1.BA_CODE, C1.ACTIVITY_GROUP, C1.ACTIVITY_CODE, C1.SUB_COST_ELEMENT, C1.QTY JUMLAH, C1.PRICE, MAT.MATERIAL_NAME, C1.QTY/C2.QTY DOSIS
                  FROM TN_BIAYA C1
                  JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = C1.PERIOD_BUDGET AND MAT.BA_CODE = C1.BA_CODE 
                    AND MAT.MATERIAL_CODE = C1.SUB_COST_ELEMENT AND MAT.MATERIAL_CODE = '104010075'
                  JOIN TN_BIAYA C2 ON C2.PERIOD_BUDGET = C1.PERIOD_BUDGET AND C2.BA_CODE = C1.BA_CODE
                    AND C2.ACTIVITY_CODE = C1.ACTIVITY_CODE AND C2.SUB_COST_ELEMENT = '102010001'
                  WHERE EXTRACT(YEAR FROM C1.PERIOD_BUDGET) = ".$params['budgetperiod']."
                  AND C1.BA_CODE = '".$params['key_find']."' AND C1.ACTIVITY_CODE = '42700'
                )
                SELECT
                SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2, 
                SUM(PLAN_JAN*CST.DOSIS*CST.PRICE) JAN_COST,
                SUM(PLAN_FEB*CST.DOSIS*CST.PRICE) FEB_COST,
                SUM(PLAN_MAR*CST.DOSIS*CST.PRICE) MAR_COST,
                SUM(PLAN_APR*CST.DOSIS*CST.PRICE) APR_COST,
                SUM(PLAN_MAY*CST.DOSIS*CST.PRICE) MAY_COST,
                SUM(PLAN_JUN*CST.DOSIS*CST.PRICE) JUN_COST,
                SUM(PLAN_JUL*CST.DOSIS*CST.PRICE) JUL_COST,
                SUM(PLAN_AUG*CST.DOSIS*CST.PRICE) AUG_COST,
                SUM(PLAN_SEP*CST.DOSIS*CST.PRICE) SEP_COST,
                SUM(PLAN_OCT*CST.DOSIS*CST.PRICE) OCT_COST,
                SUM(PLAN_NOV*CST.DOSIS*CST.PRICE) NOV_COST,
                SUM(PLAN_DEC*CST.DOSIS*CST.PRICE) DEC_COST
                FROM TR_RKT SSP 
                JOIN BIAYA CST ON CST.PERIOD_BUDGET = SSP.PERIOD_BUDGET AND CST.BA_CODE = SSP.BA_CODE AND CST.ACTIVITY_GROUP = SSP.MATURITY_STAGE_SMS2
                WHERE SSP.ACTIVITY_CODE = '42700'
                AND EXTRACT(YEAR FROM SSP.PERIOD_BUDGET) = ".$params['budgetperiod']."
                AND TOTAL_RP_SETAHUN != 0
                GROUP BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2
              ) TOOLS
              LEFT JOIN TR_RKT_PUPUK_COST_ELEMENT CE ON CE.PERIOD_BUDGET = TOOLS.PERIOD_BUDGET AND CE.BA_CODE = TOOLS.BA_CODE
                AND CE.AFD_CODE = TOOLS.AFD_CODE AND CE.BLOCK_CODE = TOOLS.BLOCK_CODE AND CE.COST_ELEMENT = 'TOOLS'
                AND CE.TIPE_TRANSAKSI = 'SISIP'
            ) RKT ON (
              RKT.TRX_RKT_CODE = COST.TRX_RKT_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.TIPE_TRANSAKSI = 'SISIP'
            )
            WHEN MATCHED THEN UPDATE SET 
              COST.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1,
              COST.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2,
              COST.DIS_COST_JAN = NVL(RKT.JAN_COST,0),
              COST.DIS_COST_FEB = NVL(RKT.FEB_COST,0),
              COST.DIS_COST_MAR = NVL(RKT.MAR_COST,0),
              COST.DIS_COST_APR = NVL(RKT.APR_COST,0),
              COST.DIS_COST_MAY = NVL(RKT.MAY_COST,0),
              COST.DIS_COST_JUN = NVL(RKT.JUN_COST,0),
              COST.DIS_COST_JUL = NVL(RKT.JUL_COST,0),
              COST.DIS_COST_AUG = NVL(RKT.AUG_COST,0),
              COST.DIS_COST_SEP = NVL(RKT.SEP_COST,0),
              COST.DIS_COST_OCT = NVL(RKT.OCT_COST,0),
              COST.DIS_COST_NOV = NVL(RKT.NOV_COST,0),
              COST.DIS_COST_DEC = NVL(RKT.DEC_COST,0),
              COST.COST_SMS1 = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                               NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              COST.COST_SMS2 = NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                               NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.DIS_COST_YEAR = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                                   NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
                                   NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                                   NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.UPDATE_USER = '{$this->_userName}',
              COST.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE,
              MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
              DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN,
              DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC,
              COST_SMS1,COST_SMS2,DIS_COST_YEAR, INSERT_USER, INSERT_TIME
            )
            VALUES (
              RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.AFD_CODE,RKT.BLOCK_CODE,'TOOLS','SISIP',
              EXTRACT(YEAR FROM RKT.PERIOD_BUDGET)||'-'||RKT.BA_CODE||'-'||RKT.AFD_CODE||'-'||RKT.BLOCK_CODE||'-RKT011',
              RKT.MATURITY_STAGE_SMS1,RKT.MATURITY_STAGE_SMS2,
              NVL(RKT.JAN_COST,0),
              NVL(RKT.FEB_COST,0),
              NVL(RKT.MAR_COST,0),
              NVL(RKT.APR_COST,0),
              NVL(RKT.MAY_COST,0),
              NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0),
              NVL(RKT.AUG_COST,0),
              NVL(RKT.SEP_COST,0),
              NVL(RKT.OCT_COST,0),
              NVL(RKT.NOV_COST,0),
              NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              '{$this->_userName}',
              CURRENT_TIMESTAMP
            );
            ";

    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }

  public function calculateMaterialSisipManual($params) {
    $sql = "MERGE INTO TR_RKT_PUPUK_COST_ELEMENT COST
            USING (
              SELECT FZ.*, CE.TRX_RKT_CODE FROM (
                WITH BIAYA AS (
                  SELECT DISTINCT C1.PERIOD_BUDGET, C1.BA_CODE, C1.ACTIVITY_GROUP, C1.ACTIVITY_CODE, C1.SUB_COST_ELEMENT, C1.QTY JUMLAH, C1.PRICE, MAT.MATERIAL_NAME, C1.QTY/C2.QTY DOSIS
                  FROM TN_BIAYA C1
                  JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = C1.PERIOD_BUDGET AND MAT.BA_CODE = C1.BA_CODE 
                    AND MAT.MATERIAL_CODE = C1.SUB_COST_ELEMENT AND MAT.DETAIL_CAT_DESC IN ('PUPUK', 'SEED')
                  JOIN TN_BIAYA C2 ON C2.PERIOD_BUDGET = C1.PERIOD_BUDGET AND C2.BA_CODE = C1.BA_CODE
                    AND C2.ACTIVITY_CODE = C1.ACTIVITY_CODE AND C2.SUB_COST_ELEMENT = '102010001'
                  WHERE EXTRACT(YEAR FROM C1.PERIOD_BUDGET) = ".$params['budgetperiod']."
                  AND C1.BA_CODE = '".$params['key_find']."' AND C1.ACTIVITY_CODE = '42700'
                )
                SELECT
                SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2, 
                SUM(PLAN_JAN*CST.DOSIS*CST.PRICE) JAN_COST,
                SUM(PLAN_FEB*CST.DOSIS*CST.PRICE) FEB_COST,
                SUM(PLAN_MAR*CST.DOSIS*CST.PRICE) MAR_COST,
                SUM(PLAN_APR*CST.DOSIS*CST.PRICE) APR_COST,
                SUM(PLAN_MAY*CST.DOSIS*CST.PRICE) MAY_COST,
                SUM(PLAN_JUN*CST.DOSIS*CST.PRICE) JUN_COST,
                SUM(PLAN_JUL*CST.DOSIS*CST.PRICE) JUL_COST,
                SUM(PLAN_AUG*CST.DOSIS*CST.PRICE) AUG_COST,
                SUM(PLAN_SEP*CST.DOSIS*CST.PRICE) SEP_COST,
                SUM(PLAN_OCT*CST.DOSIS*CST.PRICE) OCT_COST,
                SUM(PLAN_NOV*CST.DOSIS*CST.PRICE) NOV_COST,
                SUM(PLAN_DEC*CST.DOSIS*CST.PRICE) DEC_COST
                FROM TR_RKT SSP 
                JOIN BIAYA CST ON CST.PERIOD_BUDGET = SSP.PERIOD_BUDGET AND CST.BA_CODE = SSP.BA_CODE AND CST.ACTIVITY_GROUP = SSP.MATURITY_STAGE_SMS2
                WHERE SSP.ACTIVITY_CODE = '42700'
                AND EXTRACT(YEAR FROM SSP.PERIOD_BUDGET) = ".$params['budgetperiod']."
                AND TOTAL_RP_SETAHUN != 0
                GROUP BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2
              ) FZ
              LEFT JOIN TR_RKT_PUPUK_COST_ELEMENT CE ON CE.PERIOD_BUDGET = FZ.PERIOD_BUDGET AND CE.BA_CODE = FZ.BA_CODE
                AND CE.AFD_CODE = FZ.AFD_CODE AND CE.BLOCK_CODE = FZ.BLOCK_CODE AND CE.COST_ELEMENT = 'MATERIAL'
                AND CE.TIPE_TRANSAKSI = 'SISIP'
            ) RKT ON (
              RKT.TRX_RKT_CODE = COST.TRX_RKT_CODE AND COST.COST_ELEMENT = 'MATERIAL' AND COST.TIPE_TRANSAKSI = 'SISIP'
            )
            WHEN MATCHED THEN UPDATE SET 
              COST.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1,
              COST.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2,
              COST.DIS_COST_JAN = NVL(RKT.JAN_COST,0),
              COST.DIS_COST_FEB = NVL(RKT.FEB_COST,0),
              COST.DIS_COST_MAR = NVL(RKT.MAR_COST,0),
              COST.DIS_COST_APR = NVL(RKT.APR_COST,0),
              COST.DIS_COST_MAY = NVL(RKT.MAY_COST,0),
              COST.DIS_COST_JUN = NVL(RKT.JUN_COST,0),
              COST.DIS_COST_JUL = NVL(RKT.JUL_COST,0),
              COST.DIS_COST_AUG = NVL(RKT.AUG_COST,0),
              COST.DIS_COST_SEP = NVL(RKT.SEP_COST,0),
              COST.DIS_COST_OCT = NVL(RKT.OCT_COST,0),
              COST.DIS_COST_NOV = NVL(RKT.NOV_COST,0),
              COST.DIS_COST_DEC = NVL(RKT.DEC_COST,0),
              COST.COST_SMS1 = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                               NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              COST.COST_SMS2 = NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                               NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.DIS_COST_YEAR = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                                   NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
                                   NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                                   NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.UPDATE_USER = '{$this->_userName}',
              COST.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE,
              MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
              DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN,
              DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC,
              COST_SMS1,COST_SMS2,DIS_COST_YEAR, INSERT_USER, INSERT_TIME
            )
            VALUES (
              RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.AFD_CODE,RKT.BLOCK_CODE,'MATERIAL','SISIP',
              EXTRACT(YEAR FROM RKT.PERIOD_BUDGET)||'-'||RKT.BA_CODE||'-'||RKT.AFD_CODE||'-'||RKT.BLOCK_CODE||'-RKT011',
              RKT.MATURITY_STAGE_SMS1,RKT.MATURITY_STAGE_SMS2,
              NVL(RKT.JAN_COST,0),
              NVL(RKT.FEB_COST,0),
              NVL(RKT.MAR_COST,0),
              NVL(RKT.APR_COST,0),
              NVL(RKT.MAY_COST,0),
              NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0),
              NVL(RKT.AUG_COST,0),
              NVL(RKT.SEP_COST,0),
              NVL(RKT.OCT_COST,0),
              NVL(RKT.NOV_COST,0),
              NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              '{$this->_userName}',
              CURRENT_TIMESTAMP
            );
            ";

    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }

  public function calculateTransportSisipManual($params) {
    $sql = "MERGE INTO TR_RKT_PUPUK_COST_ELEMENT COST
            USING (
              WITH BIAYA AS (
                SELECT DISTINCT C1.PERIOD_BUDGET, C1.BA_CODE, C1.ACTIVITY_GROUP, C1.ACTIVITY_CODE, C1.SUB_COST_ELEMENT, C1.QTY JUMLAH, C1.PRICE, MAT.MATERIAL_NAME, C1.QTY/C2.QTY DOSIS
                FROM TN_BIAYA C1
                JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = C1.PERIOD_BUDGET AND MAT.BA_CODE = C1.BA_CODE 
                  AND MAT.MATERIAL_CODE = C1.SUB_COST_ELEMENT AND MAT.DETAIL_CAT_DESC IN ('PUPUK')
                JOIN TN_BIAYA C2 ON C2.PERIOD_BUDGET = C1.PERIOD_BUDGET AND C2.BA_CODE = C1.BA_CODE
                  AND C2.ACTIVITY_CODE = C1.ACTIVITY_CODE AND C2.SUB_COST_ELEMENT = '102010001'
                WHERE EXTRACT(YEAR FROM C1.PERIOD_BUDGET) = ".$params['budgetperiod']."
                AND C1.BA_CODE = '".$params['key_find']."' AND C1.ACTIVITY_CODE = '42700'
              )
              SELECT TR.*, CE.TRX_RKT_CODE FROM (
                SELECT
                SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2, 
                (SSP.PLAN_JAN/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM JAN_COST,
                (SSP.PLAN_FEB/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM FEB_COST,
                (SSP.PLAN_MAR/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM MAR_COST,
                (SSP.PLAN_APR/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM APR_COST,
                (SSP.PLAN_MAY/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM MAY_COST,
                (SSP.PLAN_JUN/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM JUN_COST,
                (SSP.PLAN_JUL/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM JUL_COST,
                (SSP.PLAN_AUG/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM AUG_COST,
                (SSP.PLAN_SEP/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM SEP_COST,
                (SSP.PLAN_OCT/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM OCT_COST,
                (SSP.PLAN_NOV/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM NOV_COST,
                (SSP.PLAN_DEC/ SUM(SSP.PLAN_SETAHUN) OVER (PARTITION BY SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE) )*VRA.PRICE_HM_KM DEC_COST
                FROM TR_RKT SSP 
                JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = SSP.PERIOD_BUDGET
                  AND VRA.BA_CODE = SSP.BA_CODE AND VRA.LOCATION_CODE = SSP.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
                WHERE SSP.ACTIVITY_CODE = '42700' AND TOTAL_RP_SETAHUN != 0
                AND EXTRACT(YEAR FROM SSP.PERIOD_BUDGET) = ".$params['budgetperiod']."
              ) TR
              LEFT JOIN TR_RKT_PUPUK_COST_ELEMENT CE ON CE.PERIOD_BUDGET = TR.PERIOD_BUDGET AND CE.BA_CODE = TR.BA_CODE
                AND CE.AFD_CODE = TR.AFD_CODE AND CE.BLOCK_CODE = TR.BLOCK_CODE AND CE.TIPE_TRANSAKSI = 'SISIP' AND CE.COST_ELEMENT = 'TRANSPORT'
            ) RKT ON (
              RKT.TRX_RKT_CODE = COST.TRX_RKT_CODE AND COST.COST_ELEMENT = 'TRANSPORT' AND COST.TIPE_TRANSAKSI = 'SISIP'
            )
            WHEN MATCHED THEN UPDATE SET 
              COST.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1,
              COST.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2,
              COST.DIS_COST_JAN = NVL(RKT.JAN_COST,0),
              COST.DIS_COST_FEB = NVL(RKT.FEB_COST,0),
              COST.DIS_COST_MAR = NVL(RKT.MAR_COST,0),
              COST.DIS_COST_APR = NVL(RKT.APR_COST,0),
              COST.DIS_COST_MAY = NVL(RKT.MAY_COST,0),
              COST.DIS_COST_JUN = NVL(RKT.JUN_COST,0),
              COST.DIS_COST_JUL = NVL(RKT.JUL_COST,0),
              COST.DIS_COST_AUG = NVL(RKT.AUG_COST,0),
              COST.DIS_COST_SEP = NVL(RKT.SEP_COST,0),
              COST.DIS_COST_OCT = NVL(RKT.OCT_COST,0),
              COST.DIS_COST_NOV = NVL(RKT.NOV_COST,0),
              COST.DIS_COST_DEC = NVL(RKT.DEC_COST,0),
              COST.COST_SMS1 = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                               NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              COST.COST_SMS2 = NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                               NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.DIS_COST_YEAR = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                                   NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
                                   NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                                   NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.UPDATE_USER = '{$this->_userName}',
              COST.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE,
              MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
              DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN,
              DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC,
              COST_SMS1,COST_SMS2,DIS_COST_YEAR, INSERT_USER, INSERT_TIME
            )
            VALUES (
              RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.AFD_CODE,RKT.BLOCK_CODE,'TRANSPORT','SISIP',
              EXTRACT(YEAR FROM RKT.PERIOD_BUDGET)||'-'||RKT.BA_CODE||'-'||RKT.AFD_CODE||'-'||RKT.BLOCK_CODE||'-RKT011',
              RKT.MATURITY_STAGE_SMS1,RKT.MATURITY_STAGE_SMS2,
              NVL(RKT.JAN_COST,0),
              NVL(RKT.FEB_COST,0),
              NVL(RKT.MAR_COST,0),
              NVL(RKT.APR_COST,0),
              NVL(RKT.MAY_COST,0),
              NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0),
              NVL(RKT.AUG_COST,0),
              NVL(RKT.SEP_COST,0),
              NVL(RKT.OCT_COST,0),
              NVL(RKT.NOV_COST,0),
              NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              '{$this->_userName}',
              CURRENT_TIMESTAMP
            );
            ";

    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }
  public function calculateLaborSisipManual($params) {
    $sql = "MERGE INTO TR_RKT_PUPUK_COST_ELEMENT COST
            USING (
              WITH BIAYA AS (
                SELECT DISTINCT C1.PERIOD_BUDGET, C1.BA_CODE, C1.ACTIVITY_GROUP, C1.ACTIVITY_CODE, C1.SUB_COST_ELEMENT, 
                C1.QTY JUMLAH, C2.PRICE, MAT.MATERIAL_NAME, C2.QTY/C1.QTY HK
                FROM TN_BIAYA C1
                JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = C1.PERIOD_BUDGET AND MAT.BA_CODE = C1.BA_CODE 
                  AND MAT.MATERIAL_CODE = C1.SUB_COST_ELEMENT AND MAT.DETAIL_CAT_DESC = 'SEED'
                JOIN TN_BIAYA C2 ON C2.PERIOD_BUDGET = C1.PERIOD_BUDGET AND C2.BA_CODE = C1.BA_CODE
                  AND C2.ACTIVITY_CODE = C1.ACTIVITY_CODE AND C2.SUB_COST_ELEMENT = 'FW030'
                WHERE EXTRACT(YEAR FROM C1.PERIOD_BUDGET) = ".$params['budgetperiod']."
                AND C1.BA_CODE = '".$params['key_find']."' AND C1.ACTIVITY_CODE = '42700'
              )
              SELECT LB.*, CE.TRX_RKT_CODE FROM (
                SELECT
                SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                SSP.MATURITY_STAGE_SMS1,SSP.MATURITY_STAGE_SMS2, 
                SUM(PLAN_JAN*CST.PRICE*CST.HK) JAN_COST,
                SUM(PLAN_FEB*CST.PRICE*CST.HK) FEB_COST,
                SUM(PLAN_MAR*CST.PRICE*CST.HK) MAR_COST,
                SUM(PLAN_APR*CST.PRICE*CST.HK) APR_COST,
                SUM(PLAN_MAY*CST.PRICE*CST.HK) MAY_COST,
                SUM(PLAN_JUN*CST.PRICE*CST.HK) JUN_COST,
                SUM(PLAN_JUL*CST.PRICE*CST.HK) JUL_COST,
                SUM(PLAN_AUG*CST.PRICE*CST.HK) AUG_COST,
                SUM(PLAN_SEP*CST.PRICE*CST.HK) SEP_COST,
                SUM(PLAN_OCT*CST.PRICE*CST.HK) OCT_COST,
                SUM(PLAN_NOV*CST.PRICE*CST.HK) NOV_COST,
                SUM(PLAN_DEC*CST.PRICE*CST.HK) DEC_COST
                FROM TR_RKT SSP 
                JOIN TM_HECTARE_STATEMENT HS ON HS.PERIOD_BUDGET = SSP.PERIOD_BUDGET AND HS.BA_CODE = SSP.BA_CODE
                  AND HS.AFD_CODE = SSP.AFD_CODE AND HS.BLOCK_CODE = SSP.BLOCK_CODE 
                JOIN BIAYA CST ON CST.PERIOD_BUDGET = SSP.PERIOD_BUDGET AND CST.BA_CODE = SSP.BA_CODE 
                  AND CST.ACTIVITY_GROUP = SSP.MATURITY_STAGE_SMS2
                WHERE SSP.ACTIVITY_CODE = '42700' AND SSP.PLAN_SETAHUN != 0
                AND EXTRACT(YEAR FROM SSP.PERIOD_BUDGET) = ".$params['budgetperiod']."
                GROUP BY
                SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
                SSP.MATURITY_STAGE_SMS1,SSP.MATURITY_STAGE_SMS2
              ) LB
              LEFT JOIN TR_RKT_PUPUK_COST_ELEMENT CE ON CE.PERIOD_BUDGET = LB.PERIOD_BUDGET AND CE.BA_CODE = LB.BA_CODE
                AND CE.AFD_CODE = LB.AFD_CODE AND CE.BLOCK_CODE = LB.BLOCK_CODE AND CE.TIPE_TRANSAKSI = 'SISIP' AND CE.COST_ELEMENT = 'LABOUR'
            ) RKT ON (
              RKT.TRX_RKT_CODE = COST.TRX_RKT_CODE AND COST.COST_ELEMENT = 'LABOUR' AND COST.TIPE_TRANSAKSI = 'SISIP'
            )
            WHEN MATCHED THEN UPDATE SET 
              COST.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1,
              COST.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2,
              COST.DIS_COST_JAN = NVL(RKT.JAN_COST,0),
              COST.DIS_COST_FEB = NVL(RKT.FEB_COST,0),
              COST.DIS_COST_MAR = NVL(RKT.MAR_COST,0),
              COST.DIS_COST_APR = NVL(RKT.APR_COST,0),
              COST.DIS_COST_MAY = NVL(RKT.MAY_COST,0),
              COST.DIS_COST_JUN = NVL(RKT.JUN_COST,0),
              COST.DIS_COST_JUL = NVL(RKT.JUL_COST,0),
              COST.DIS_COST_AUG = NVL(RKT.AUG_COST,0),
              COST.DIS_COST_SEP = NVL(RKT.SEP_COST,0),
              COST.DIS_COST_OCT = NVL(RKT.OCT_COST,0),
              COST.DIS_COST_NOV = NVL(RKT.NOV_COST,0),
              COST.DIS_COST_DEC = NVL(RKT.DEC_COST,0),
              COST.COST_SMS1 = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                               NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              COST.COST_SMS2 = NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                               NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.DIS_COST_YEAR = NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
                                   NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
                                   NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
                                   NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              COST.UPDATE_USER = '{$this->_userName}',
              COST.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE,
              MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
              DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN,
              DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC,
              COST_SMS1,COST_SMS2,DIS_COST_YEAR, INSERT_USER, INSERT_TIME
            )
            VALUES (
              RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.AFD_CODE,RKT.BLOCK_CODE,'LABOUR','SISIP',
              EXTRACT(YEAR FROM RKT.PERIOD_BUDGET)||'-'||RKT.BA_CODE||'-'||RKT.AFD_CODE||'-'||RKT.BLOCK_CODE||'-RKT011',
              RKT.MATURITY_STAGE_SMS1,RKT.MATURITY_STAGE_SMS2,
              NVL(RKT.JAN_COST,0),
              NVL(RKT.FEB_COST,0),
              NVL(RKT.MAR_COST,0),
              NVL(RKT.APR_COST,0),
              NVL(RKT.MAY_COST,0),
              NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0),
              NVL(RKT.AUG_COST,0),
              NVL(RKT.SEP_COST,0),
              NVL(RKT.OCT_COST,0),
              NVL(RKT.NOV_COST,0),
              NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0),
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              NVL(RKT.JAN_COST,0)+NVL(RKT.FEB_COST,0)+NVL(RKT.MAR_COST,0)+
              NVL(RKT.APR_COST,0)+NVL(RKT.MAY_COST,0)+NVL(RKT.JUN_COST,0)+
              NVL(RKT.JUL_COST,0)+NVL(RKT.AUG_COST,0)+NVL(RKT.SEP_COST,0)+
              NVL(RKT.OCT_COST,0)+NVL(RKT.NOV_COST,0)+NVL(RKT.DEC_COST,0),
              '{$this->_userName}',
              CURRENT_TIMESTAMP
            );
            ";

    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }


}

