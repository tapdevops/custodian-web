<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Kebutuhan Aktivitas Report
Function 			:	- getInput							: YIR 20/06/2014	: setting input untuk region
						- getLastGenerate					: SID 12/08/2014	: get last generate date
						- tmpRptKebActEstCostBlock			: NBU 18/09/2015	: query summary Keb Act Est Cost Block
						- tmpRptKebActDevCostBlock			: NBU 18/09/2015	: query summary Keb Act Dev Cost Block
						- reportKebAktivitasPerBa			: NBU 18/09/2015	: generate Keb Akt estate cost per BA
						- reportKebAktivitasPerAfd			: NBU 18/09/2015	: generate Keb Akt estate cost per AFD
						- reportKebAktivitasDevPerBa		: NBU 18/09/2015	: generate Keb Akt development cost per BA
						- reportKebAktivitasDevPerAfd		: NBU 18/09/2015	: generate Keb Akt development cost per AFD
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Nicholas Budihardja
Dibuat Tanggal		: 	18/09/2015
Update Terakhir		:	18/09/2015
Revisi				:	
=========================================================================================================================
*/
class Application_Model_ReportKebAktivitas
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
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
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
	
		
	//ARIES 15-JUN-2015
	public function tmpRptKebActEstCostBlock($params = array())
	{
		$where = "";
		$where1 = ""; $where2 = ""; $where3 = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where2 .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where3 .= "
                    AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$xwhere .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$twhere .= "
                AND to_char(TTJ.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";  
			$where2 .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where3 .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";			
			$xwhere .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$twhere .= "
                AND to_char(TTJ.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
			$where1 .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
			$where2 .= "
                AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
			$where3 .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
			$xwhere .= "
                AND BA_CODE = '".$params['key_find']."'
            "; 
			$twhere .= "
                AND TTJ.BA_CODE = '".$params['key_find']."'
            ";
        }
		//generate estate cost per BLOCK
		$query = "
			INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
				PERIOD_BUDGET, 
				REGION_CODE, 
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				TIPE_TRANSAKSI, 
				COST_ELEMENT,
                ACTIVITY_CODE,
                ACTIVITY_DESC,
				SUB_COST_ELEMENT,
				SUB_COST_ELEMENT_DESC,
				KETERANGAN,
				UOM, 
				QTY_JAN, 
				QTY_FEB, 
				QTY_MAR, 
				QTY_APR, 
				QTY_MAY, 
				QTY_JUN, 
				QTY_JUL, 
				QTY_AUG, 
				QTY_SEP, 
				QTY_OCT, 
				QTY_NOV, 
				QTY_DEC,
				COST_JAN, 
				COST_FEB, 
				COST_MAR, 
				COST_APR, 
				COST_MAY, 
				COST_JUN, 
				COST_JUL, 
				COST_AUG, 
				COST_SEP, 
				COST_OCT, 
				COST_NOV, 
				COST_DEC,
				QTY_SETAHUN,
				COST_SETAHUN,
				INSERT_USER, 
				INSERT_TIME
			)
		SELECT PERIOD_BUDGET,
		 REGION_CODE,	  
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         ACTIVITY_GROUP,
         COST_ELEMENT,
         ACTIVITY_CODE,
		 ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         '' KETERANGAN,
		 UOM,
         SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
         SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
         SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
         SUM (NVL (QTY_APR, 0)) AS QTY_APR,
         SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
         SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
         SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
         SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
         SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
         SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
         SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
         SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
         SUM (NVL (COST_JAN, 0)) AS COST_JAN,
         SUM (NVL (COST_FEB, 0)) AS COST_FEB,
         SUM (NVL (COST_MAR, 0)) AS COST_MAR,
         SUM (NVL (COST_APR, 0)) AS COST_APR,
         SUM (NVL (COST_MAY, 0)) AS COST_MAY,
         SUM (NVL (COST_JUN, 0)) AS COST_JUN,
         SUM (NVL (COST_JUL, 0)) AS COST_JUL,
         SUM (NVL (COST_AUG, 0)) AS COST_AUG,
         SUM (NVL (COST_SEP, 0)) AS COST_SEP,
         SUM (NVL (COST_OCT, 0)) AS COST_OCT,
         SUM (NVL (COST_NOV, 0)) AS COST_NOV,
         SUM (NVL (COST_DEC, 0)) AS COST_DEC,
		 (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
                              + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0))
                              + SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
                              + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
		 AS QTY_SETAHUN,
		 (SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
                     + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))
                     + SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
                     + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) 
         AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
		 SYSDATE AS INSERT_TIME
    FROM (-- MANUAL_NON_INFRA SM 1
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
									(RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
									(RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
									(RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
									(RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
									(RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
									(RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JAN * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JAN * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_JAN,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_FEB * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_FEB * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_FEB,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAR * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_MAR * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_MAR,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_APR * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_APR * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_APR,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAY * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_MAY * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_MAY,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUN * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JUN * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_JUN,
                                     0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE =
                                              RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE =
                                              RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
                                     $where
          UNION ALL -- MANUAL_NON_INFRA SM 2
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
									 BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,									 		 
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
									 (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
									 (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
									 (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
									 (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
									 (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     CASE
									 WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUL * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JUL * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_AUG,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_AUG * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_AUG * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_AUG,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_SEP * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_SEP * BIAYA.PRICE_ROTASI_SITE
								     END
									 AS COST_SEP,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_OCT * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_OCT * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_OCT,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_NOV * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_NOV * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_NOV,
								     CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_DEC * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_DEC * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE =
                                              RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE =
                                              RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET =
                                              RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE =
                                                 RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET =
                                              BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE =
                                                 BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
									 $where
		  UNION ALL
			-- HITUNG RAWAT SISIP
			SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   RKT.PLAN_JAN / SPH.SPH_STANDAR AS QTY_JAN,
			   RKT.PLAN_FEB / SPH.SPH_STANDAR AS QTY_FEB,
			   RKT.PLAN_MAR / SPH.SPH_STANDAR AS QTY_MAR,
			   RKT.PLAN_APR / SPH.SPH_STANDAR AS QTY_APR,
			   RKT.PLAN_MAY / SPH.SPH_STANDAR AS QTY_MAY,
			   RKT.PLAN_JUN / SPH.SPH_STANDAR AS QTY_JUN,
			   0 AS QTY_JUL,
			   0 AS QTY_AUG,
			   0 AS QTY_SEP,
			   0 AS QTY_OCT,
			   0 AS QTY_NOV,
			   0 AS QTY_DEC,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JAN,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_FEB,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_MAR,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_APR,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_MAY,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JUN,
			   0 AS COST_JUL,
			   0 AS COST_AUG,
			   0 AS COST_SEP,
			   0 AS COST_OCT,
			   0 AS COST_NOV,
			   0 AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_SPH SPH
				  ON SPH.CORE =
						CASE
						   WHEN SUBSTR (RKT.BA_CODE, 3, 1) = 2 THEN 'INTI'
						   ELSE 'PLASMA'
						END
					 AND SPH.LAND_TYPE = TM_HS.LAND_TYPE
					 AND SPH.TOPOGRAPHY = TM_HS.TOPOGRAPHY
			   LEFT JOIN TN_BIAYA BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS1 = 'TM'
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_SISIP'
			   AND RKT.COST_ELEMENT <> 'TRANSPORT'
			   AND RKT.ACTIVITY_CODE = '42700'
			   $where
		UNION ALL 
		-- INI SMESTER UNTUK SMS 2 
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   0 AS QTY_JAN,
			   0 AS QTY_FEB,
			   0 AS QTY_MAR,
			   0 AS QTY_APR,
			   0 AS QTY_MAY,
			   0 AS QTY_JUN,
			   RKT.PLAN_JUL / SPH.SPH_STANDAR AS QTY_JUL,
			   RKT.PLAN_AUG / SPH.SPH_STANDAR AS QTY_AUG,
			   RKT.PLAN_SEP / SPH.SPH_STANDAR AS QTY_SEP,
			   RKT.PLAN_OCT / SPH.SPH_STANDAR AS QTY_OCT,
			   RKT.PLAN_NOV / SPH.SPH_STANDAR AS QTY_NOV,
			   RKT.PLAN_DEC / SPH.SPH_STANDAR AS QTY_DEC,
			   0 COST_JAN,
			   0 COST_FEB,
			   0 COST_MAR,
			   0 COST_APR,
			   0 COST_MAY,
			   0 COST_JUN,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JUL,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_AUG,  
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_SEP,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_OCT,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_NOV,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_SPH SPH
				  ON SPH.CORE =
						CASE
						   WHEN SUBSTR (RKT.BA_CODE, 3, 1) = 2 THEN 'INTI'
						   ELSE 'PLASMA'
						END
					 AND SPH.LAND_TYPE = TM_HS.LAND_TYPE
					 AND SPH.TOPOGRAPHY = TM_HS.TOPOGRAPHY
			   LEFT JOIN TN_BIAYA BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS2 = 'TM'
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_SISIP'
			   AND RKT.COST_ELEMENT <> 'TRANSPORT'
			   AND RKT.ACTIVITY_CODE = '42700'
			   $where							 
          UNION ALL
			-- MANUAL_NON_INFRA_OPSI SMS1
			 SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
									 BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     (RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
									 (RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
									 (RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
									 (RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
									 (RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
									 (RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JAN * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JAN * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_JAN,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_FEB * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_FEB * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_FEB,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAR * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_MAR * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_MAR,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_APR * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_APR * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_APR,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAY * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_MAY * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_MAY,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUN * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JUN * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_JUN,
									 0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET =
                                              BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE =
                                                 BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
									 $where
          UNION ALL                              
		  -- MANUAL_NON_INFRA_OPSI SMS2
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
									 BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
									 (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
									 (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
									 (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
									 (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
									 (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUL * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_JUL * BIAYA.PRICE_ROTASI_SITE
								     END
									 AS COST_JUL,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_AUG * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_AUG * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_AUG,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_SEP * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_SEP * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_SEP,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_OCT * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_OCT * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_OCT,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_NOV * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_NOV * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_NOV,
									 CASE
										WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_DEC * BIAYA.PRICE_ROTASI
										ELSE RKT.PLAN_DEC * BIAYA.PRICE_ROTASI_SITE
									 END
									 AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
									 $where
          UNION ALL
		  -- MANUAL_INFRA SMS1
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
									 BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     (RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
									 (RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
									 (RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
									 (RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
									 (RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
									 (RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     (RKT.PLAN_JAN * BIAYA.RP_HA_INTERNAL) AS COST_JAN,
								     (RKT.PLAN_FEB * BIAYA.RP_HA_INTERNAL) AS COST_FEB,
								     (RKT.PLAN_MAR * BIAYA.RP_HA_INTERNAL) AS COST_MAR,
								     (RKT.PLAN_APR * BIAYA.RP_HA_INTERNAL) AS COST_APR,
								     (RKT.PLAN_MAY * BIAYA.RP_HA_INTERNAL) AS COST_MAY,
								     (RKT.PLAN_JUN * BIAYA.RP_HA_INTERNAL) AS COST_JUN,
                                     0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_INFRASTRUKTUR BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
									 $where
          UNION ALL                                       
          -- MANUAL INFRA SMS2
		  SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
									 BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
									 (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
									 (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
									 (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
									 (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
									 (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     (RKT.PLAN_JUL * BIAYA.RP_HA_INTERNAL) AS COST_JUL,
								     (RKT.PLAN_AUG * BIAYA.RP_HA_INTERNAL) AS COST_AUG,
								     (RKT.PLAN_SEP * BIAYA.RP_HA_INTERNAL) AS COST_SEP,
								     (RKT.PLAN_OCT * BIAYA.RP_HA_INTERNAL) AS COST_OCT,
								     (RKT.PLAN_NOV * BIAYA.RP_HA_INTERNAL) AS COST_NOV,
								     (RKT.PLAN_DEC * BIAYA.RP_HA_INTERNAL) AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_INFRASTRUKTUR BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
										   AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
									 AND RKT.COST_ELEMENT <> 'TRANSPORT'
									 $where
			UNION ALL
			-- HITUNG INFRA UNTUK CONTRACT sms 1
			SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT) AS UOM,
			   RKT.PLAN_JAN AS QTY_JAN,
			   RKT.PLAN_FEB AS QTY_FEB,
			   RKT.PLAN_MAR AS QTY_MAR,
			   RKT.PLAN_APR AS QTY_APR,
			   RKT.PLAN_MAY AS QTY_MAY,
			   RKT.PLAN_JUN AS QTY_JUN,
			   0 QTY_JUL,
			   0 QTY_AUG,
			   0 QTY_SEP,
			   0 QTY_OCT,
			   0 QTY_NOV,
			   0 QTY_DEC,
			   DIS_JAN AS COST_JAN,
			   DIS_FEB AS COST_FEB,
			   DIS_MAR AS COST_MAR,
			   DIS_APR AS COST_APR,
			   DIS_MAY AS COST_MAY,
			   DIS_JUN AS COST_JUN,
			   0 COST_JUL,
			   0 COST_AUG,
			   0 COST_SEP,
			   0 COST_OCT,
			   0 COST_NOV,
			   0 COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_INFRASTRUKTUR BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS1 = 'TM'
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
			   AND RKT.SUMBER_BIAYA = 'EXTERNAL'
			   AND RKT.COST_ELEMENT = 'CONTRACT'
			   $where
		UNION ALL
		--untuk sms 2
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   0 QTY_JAN,
			   0 QTY_FEB,
			   0 QTY_MAR,
			   0 QTY_APR,
			   0 QTY_MAY,
			   0 QTY_JUN,
			   RKT.PLAN_JUL AS QTY_JUL,
			   RKT.PLAN_AUG AS QTY_AUG,
			   RKT.PLAN_SEP AS QTY_SEP,
			   RKT.PLAN_OCT AS QTY_OCT,
			   RKT.PLAN_NOV AS QTY_NOV,
			   RKT.PLAN_DEC AS QTY_DEC,
			   0 COST_JAN,
			   0 COST_FEB,
			   0 COST_MAR,
			   0 COST_APR,
			   0 COST_MAY,
			   0 COST_JUN,
			   DIS_JUL AS COST_JUL,
			   DIS_AUG AS COST_AUG,
			   DIS_SEP AS COST_SEP,
			   DIS_OCT AS COST_OCT,
			   DIS_NOV AS COST_NOV,
			   DIS_DEC AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_INFRASTRUKTUR BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS2 = 'TM'
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
			   AND RKT.COST_ELEMENT = 'CONTRACT'
			   $where
		UNION ALL
		--COST ELEMENT TRANSPORT UNTUK RAWAT 
			--SMS1
					SELECT RKT.PERIOD_BUDGET,
						   ORG.REGION_CODE,
						   RKT.BA_CODE,
						   RKT.AFD_CODE,
						   RKT.BLOCK_CODE,
						   RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
						   RKT.ACTIVITY_CODE,
						   RKT.TIPE_TRANSAKSI,
						   ACT.DESCRIPTION AS ACTIVITY_DESC,
						   RKT.COST_ELEMENT,
						   '' AS ACTIVITY_CLASS,
						   '' AS LAND_TYPE,
						   '' AS TOPOGRAPHY,
						   VRA.VRA_CODE,
						   RKT_DIS.HM_KM,
						   VRA.VRA_SUB_CAT_DESCRIPTION,
						   VRA.UOM,
						   (RKT.PLAN_JAN
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_JAN,
						   (RKT.PLAN_FEB
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_FEB,
						   (RKT.PLAN_MAR
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_MAR,
						   (RKT.PLAN_APR
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_APR,
						   (RKT.PLAN_MAY
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_MAY,
						   (RKT.PLAN_JUN
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_JUN,
						   0 QTY_JUL,
						   0 QTY_AUG,
						   0 QTY_SEP,
						   0 QTY_OCT,
						   0 QTY_NOV,
						   0 QTY_DEC,
						   ( (RKT.PLAN_JAN
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_JAN,
						   ( (RKT.PLAN_FEB
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_FEB,
						   ( (RKT.PLAN_MAR
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_MAR,
						   ( (RKT.PLAN_APR
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_APR,
						   ( (RKT.PLAN_MAY
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_MAY,
						   ( (RKT.PLAN_JUN
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_JUN,
						   0 COST_JUL,
						   0 COST_AUG,
						   0 COST_SEP,
						   0 COST_OCT,
						   0 COST_NOV,
						   0 COST_DEC
					  FROM TR_RKT_COST_ELEMENT RKT
						   LEFT JOIN TR_RKT RKT_INDUK
							  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
						   LEFT JOIN TR_RKT_VRA_DISTRIBUSI RKT_DIS
							  ON     RKT_DIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
								 AND RKT_DIS.BA_CODE = RKT.BA_CODE
								 AND RKT_DIS.LOCATION_CODE = RKT.AFD_CODE
								 AND RKT_DIS.ACTIVITY_CODE = RKT.ACTIVITY_CODE
						   INNER JOIN TR_RKT_VRA_DISTRIBUSI_SUM RKT_DIS_SUM
							  ON     RKT_DIS_SUM.PERIOD_BUDGET = RKT_DIS.PERIOD_BUDGET
								 AND RKT_DIS_SUM.BA_CODE = RKT_DIS.BA_CODE
								 AND RKT_DIS_SUM.ACTIVITY_CODE = RKT_DIS.ACTIVITY_CODE
								 AND RKT_DIS_SUM.VRA_CODE = RKT_DIS.VRA_CODE
						   LEFT JOIN TM_ACTIVITY ACT
							  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
						   LEFT JOIN TM_ORGANIZATION ORG
							  ON ORG.BA_CODE = RKT.BA_CODE
						   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
							  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
								 AND TM_HS.BA_CODE = RKT.BA_CODE
								 AND TM_HS.AFD_CODE = RKT.AFD_CODE
								 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
						   LEFT JOIN TM_VRA VRA
							  ON VRA.VRA_CODE = RKT_DIS_SUM.VRA_CODE
					 WHERE     RKT.DELETE_USER IS NULL
						   AND RKT_INDUK.FLAG_TEMP IS NULL
						   AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						   AND RKT.COST_ELEMENT = 'TRANSPORT'
						   AND RKT.TIPE_TRANSAKSI <> 'MANUAL_INFRA'
						   $where
					UNION ALL
					--SMS2       
					SELECT RKT.PERIOD_BUDGET,
						   ORG.REGION_CODE,
						   RKT.BA_CODE,
						   RKT.AFD_CODE,
						   RKT.BLOCK_CODE,
						   RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
						   RKT.ACTIVITY_CODE,
						   RKT.TIPE_TRANSAKSI,
						   ACT.DESCRIPTION AS ACTIVITY_DESC,
						   RKT.COST_ELEMENT,
						   '' AS ACTIVITY_CLASS,
						   '' AS LAND_TYPE,
						   '' AS TOPOGRAPHY,
						   VRA.VRA_CODE,
						   RKT_DIS.HM_KM,
						   VRA.VRA_SUB_CAT_DESCRIPTION,
						   VRA.UOM,
						   0 QTY_JAN,
						   0 QTY_FEB,
						   0 QTY_MAR,
						   0 QTY_APR,
						   0 QTY_MAY,
						   0 QTY_JUN,
						   (RKT.PLAN_JUL
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_JUL,
						   (RKT.PLAN_AUG
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_AUG,
						   (RKT.PLAN_SEP
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_SEP,
						   (RKT.PLAN_OCT
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_OCT,
						   (RKT.PLAN_NOV
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_NOV,
						   (RKT.PLAN_DEC
							/ NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								 FROM TR_RKT
								WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
									  AND BA_CODE = RKT.BA_CODE
									  AND AFD_CODE = RKT.AFD_CODE
									  AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							* RKT_DIS.HM_KM)
							  AS QTY_DEC,
						   0 COST_JAN,
						   0 COST_FEB,
						   0 COST_MAR,
						   0 COST_APR,
						   0 COST_MAY,
						   0 COST_JUN,
						   ( (RKT.PLAN_JUL
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_JUL,
						   ( (RKT.PLAN_AUG
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_AUG,
						   ( (RKT.PLAN_SEP
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_SEP,
						   ( (RKT.PLAN_OCT
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_OCT,
						   ( (RKT.PLAN_NOV
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_NOV,
						   ( (RKT.PLAN_DEC
							  / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
								   FROM TR_RKT
								  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
										AND BA_CODE = RKT.BA_CODE
										AND AFD_CODE = RKT.AFD_CODE
										AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
							  * RKT_DIS.HM_KM))
						   * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
							  AS COST_DEC
					  FROM TR_RKT_COST_ELEMENT RKT
						   LEFT JOIN TR_RKT RKT_INDUK
							  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
						   LEFT JOIN TR_RKT_VRA_DISTRIBUSI RKT_DIS
							  ON     RKT_DIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
								 AND RKT_DIS.BA_CODE = RKT.BA_CODE
								 AND RKT_DIS.LOCATION_CODE = RKT.AFD_CODE
								 AND RKT_DIS.ACTIVITY_CODE = RKT.ACTIVITY_CODE
						   INNER JOIN TR_RKT_VRA_DISTRIBUSI_SUM RKT_DIS_SUM
							  ON     RKT_DIS_SUM.PERIOD_BUDGET = RKT_DIS.PERIOD_BUDGET
								 AND RKT_DIS_SUM.BA_CODE = RKT_DIS.BA_CODE
								 AND RKT_DIS_SUM.ACTIVITY_CODE = RKT_DIS.ACTIVITY_CODE
								 AND RKT_DIS_SUM.VRA_CODE = RKT_DIS.VRA_CODE
						   LEFT JOIN TM_ACTIVITY ACT
							  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
						   LEFT JOIN TM_ORGANIZATION ORG
							  ON ORG.BA_CODE = RKT.BA_CODE
						   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
							  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
								 AND TM_HS.BA_CODE = RKT.BA_CODE
								 AND TM_HS.AFD_CODE = RKT.AFD_CODE
								 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
						   LEFT JOIN TM_VRA VRA
							  ON VRA.VRA_CODE = RKT_DIS_SUM.VRA_CODE
					 WHERE     RKT.DELETE_USER IS NULL
						   AND RKT_INDUK.FLAG_TEMP IS NULL
						   AND RKT.MATURITY_STAGE_SMS2 = 'TM'
						   AND RKT.COST_ELEMENT = 'TRANSPORT'
						   AND RKT.TIPE_TRANSAKSI <> 'MANUAL_INFRA'
						   $where
					UNION ALL
					SELECT PERIOD_BUDGET,
					   REGION_CODE,
					   BA_CODE,
					   AFD_CODE,
					   BLOCK_CODE,
					   MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
					   ACTIVITY_CODE,
					   TIPE_TRANSAKSI,
					   DESCRIPTION AS ACTIVITY_DESC,
					   COST_ELEMENT,
					   ACTIVITY_CLASS,
					   LAND_TYPE,
					   TOPOGRAPHY,
					   SUB_COST_ELEMENT,
					   QTY_HA,
					   VRA_SUB_CAT_DESCRIPTION,
					   UOM,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JAN / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JAN,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_FEB / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_FEB,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAR / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_MAR,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_APR / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_APR,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAY / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_MAY,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUN / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JUN,
					   0 QTY_JUL,
					   0 QTY_AUG,
					   0 QTY_SEP,
					   0 QTY_OCT,
					   0 QTY_NOV,
					   0 QTY_DEC,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JAN ELSE 0 END
						  AS COST_JAN,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_FEB ELSE 0 END
						  AS COST_FEB,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAR ELSE 0 END
						  AS COST_MAR,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_APR ELSE 0 END
						  AS COST_APR,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAY ELSE 0 END
						  AS COST_MAY,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUN ELSE 0 END
						  AS COST_JUN,
					   0 COST_JUL,
					   0 COST_AUG,
					   0 COST_SEP,
					   0 COST_OCT,
					   0 COST_NOV,
					   0 COST_DEC
				  FROM (SELECT RKT_INDUK.*,
							   RKT.COST_ELEMENT,
							   BIAYA.LAND_TYPE,
							   BIAYA.TOPOGRAPHY,
							   BIAYA.SUB_COST_ELEMENT,
							   BIAYA.QTY_HA,
							   ORG.REGION_CODE,
							   ACT.DESCRIPTION,
							   VRA.VRA_SUB_CAT_DESCRIPTION,
							   VRA.UOM,
							   ( (SELECT RKTVRAS.VALUE
									FROM TR_RKT_VRA_SUM RKTVRAS
								   WHERE     RKTVRAS.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										 AND RKTVRAS.BA_CODE = BIAYA.BA_CODE
										 AND RKTVRAS.VRA_CODE = BIAYA.SUB_COST_ELEMENT)
								UNION
								(SELECT NVRAPINJAM.RP_QTY AS VALUE
								   FROM TN_VRA_PINJAM NVRAPINJAM
								  WHERE     NVRAPINJAM.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										AND NVRAPINJAM.REGION_CODE = BIAYA.REGION_CODE
										AND NVRAPINJAM.VRA_CODE = BIAYA.SUB_COST_ELEMENT))
								  AS HARGA_INTERNAL,
							   (1 / BIAYA.QTY_HA) * RP_HA_EXTERNAL AS HARGA_EXTERNAL
						  FROM TR_RKT_COST_ELEMENT RKT
							   LEFT JOIN TR_RKT RKT_INDUK
								  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
							   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
								  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
									 AND TM_HS.BA_CODE = RKT.BA_CODE
									 AND TM_HS.AFD_CODE = RKT.AFD_CODE
									 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
							   INNER JOIN TN_INFRASTRUKTUR BIAYA
								  ON     RKT.BA_CODE = BIAYA.BA_CODE
									 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
									 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
									 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
									 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
									 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
									 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
							   LEFT JOIN TM_ACTIVITY ACT
								  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
							   LEFT JOIN TM_VRA VRA
								  ON VRA.VRA_CODE = BIAYA.SUB_COST_ELEMENT  
							   LEFT JOIN TM_ORGANIZATION ORG
								  ON ORG.BA_CODE = RKT.BA_CODE
						 WHERE     RKT.DELETE_USER IS NULL
							   AND RKT_INDUK.FLAG_TEMP IS NULL
							   AND RKT.MATURITY_STAGE_SMS1 = 'TM'
							   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
							   AND RKT.COST_ELEMENT = 'TRANSPORT'
							   $where)
				UNION ALL
				SELECT PERIOD_BUDGET,
					   REGION_CODE,
					   BA_CODE,
					   AFD_CODE,
					   BLOCK_CODE,
					   MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
					   ACTIVITY_CODE,
					   TIPE_TRANSAKSI,
					   DESCRIPTION AS ACTIVITY_DESC,
					   COST_ELEMENT,
					   ACTIVITY_CLASS,
					   LAND_TYPE,
					   TOPOGRAPHY,
					   SUB_COST_ELEMENT,
					   QTY_HA,
					   VRA_SUB_CAT_DESCRIPTION,
					   UOM,
					   0 QTY_JAN,
					   0 QTY_FEB,
					   0 QTY_MAR,
					   0 QTY_APR,
					   0 QTY_MAY,
					   0 QTY_JUN,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUL / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JUL,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_AUG / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_AUG,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_SEP / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_SEP,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_OCT / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_OCT,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_NOV / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_NOV,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_DEC / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_DEC,
					   0 COST_JAN,
					   0 COST_FEB,
					   0 COST_MAR,
					   0 COST_APR,
					   0 COST_MAY,
					   0 COST_JUN,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUL ELSE 0 END
						  AS COST_JUL,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_AUG ELSE 0 END
						  AS COST_AUG,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_SEP ELSE 0 END
						  AS COST_SEP,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_OCT ELSE 0 END
						  AS COST_OCT,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_NOV ELSE 0 END
						  AS COST_NOV,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_DEC ELSE 0 END
						  AS COST_DEC
				  FROM (SELECT RKT_INDUK.*,
							   RKT.COST_ELEMENT,
							   BIAYA.LAND_TYPE,
							   BIAYA.TOPOGRAPHY,
							   BIAYA.SUB_COST_ELEMENT,
							   BIAYA.QTY_HA,
							   ORG.REGION_CODE,
							   ACT.DESCRIPTION,
							   VRA.VRA_SUB_CAT_DESCRIPTION,
							   VRA.UOM,
							   ( (SELECT RKTVRAS.VALUE
									FROM TR_RKT_VRA_SUM RKTVRAS
								   WHERE     RKTVRAS.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										 AND RKTVRAS.BA_CODE = BIAYA.BA_CODE
										 AND RKTVRAS.VRA_CODE = BIAYA.SUB_COST_ELEMENT)
								UNION
								(SELECT NVRAPINJAM.RP_QTY AS VALUE
								   FROM TN_VRA_PINJAM NVRAPINJAM
								  WHERE     NVRAPINJAM.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										AND NVRAPINJAM.REGION_CODE = BIAYA.REGION_CODE
										AND NVRAPINJAM.VRA_CODE = BIAYA.SUB_COST_ELEMENT))
								  AS HARGA_INTERNAL,
							   (1 / BIAYA.QTY_HA) * RP_HA_EXTERNAL AS HARGA_EXTERNAL
						  FROM TR_RKT_COST_ELEMENT RKT
							   LEFT JOIN TR_RKT RKT_INDUK
								  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
							   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
								  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
									 AND TM_HS.BA_CODE = RKT.BA_CODE
									 AND TM_HS.AFD_CODE = RKT.AFD_CODE
									 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
							   INNER JOIN TN_INFRASTRUKTUR BIAYA
								  ON     RKT.BA_CODE = BIAYA.BA_CODE
									 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
									 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
									 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
									 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
									 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
									 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
							   LEFT JOIN TM_ACTIVITY ACT
								  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
							   LEFT JOIN TM_VRA VRA
								  ON VRA.VRA_CODE = BIAYA.SUB_COST_ELEMENT	  
							   LEFT JOIN TM_ORGANIZATION ORG
								  ON ORG.BA_CODE = RKT.BA_CODE
						 WHERE     RKT.DELETE_USER IS NULL
							   AND RKT_INDUK.FLAG_TEMP IS NULL
							   AND RKT.MATURITY_STAGE_SMS2 = 'TM'
							   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
							   AND RKT.COST_ELEMENT = 'TRANSPORT'
							   $where)
                        ) REPORT
		GROUP BY PERIOD_BUDGET,
				 REGION_CODE,
				 BA_CODE,
				 AFD_CODE,
				 BLOCK_CODE,
				 ACTIVITY_GROUP,
				 COST_ELEMENT,
				 ACTIVITY_CODE,
				 TIPE_TRANSAKSI,
				 ACTIVITY_DESC,
				 SUB_COST_ELEMENT,
				 MATERIAL_NAME,
				 UOM
		UNION ALL --PERHITUNGAN UNTUK PANEN LABOUR		 
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   'TM' ACTIVITY_GROUP,
			   RKT.COST_ELEMENT,
			   RKT.ACTIVITY_CODE,
			   'BIAYA PEMANEN' ACTIVITY_DESC,
			   '' SUB_COST_ELEMENT,
			   '' MATERIAL_NAME,
			   '' KETERANGAN,
			   'HK' UOM,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_DEC,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_BASIS
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_DEC,
			   RKT_INDUK.BIAYA_PEMANEN_HK AS QTY_SETAHUN,   
			   RKT_INDUK.BIAYA_PEMANEN_RP_BASIS AS COST_SETAHUN,
			   '".$this->_userName."' AS INSERT_USER,
			   SYSDATE AS INSERT_TIME
		  FROM TR_RKT_PANEN_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT_PANEN RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
		WHERE     RKT.COST_ELEMENT = 'LABOUR'
		$where
		UNION ALL
		--INI BUAT PREMI PANEN JANJANG
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   'TM' ACTIVITY_GROUP,
			   RKT.COST_ELEMENT,
			   RKT.ACTIVITY_CODE,
			   'PREMI PANEN JANJANG' ACTIVITY_DESC,
			   '' SUB_COST_ELEMENT,
			   '' MATERIAL_NAME,
			   '' KETERANGAN,
			   'HK' UOM,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_DEC,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_DEC,
			   RKT_INDUK.BIAYA_PEMANEN_HK AS QTY_SETAHUN,   
			   RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG AS COST_SETAHUN,
			   '".$this->_userName."' AS INSERT_USER,
			   SYSDATE AS INSERT_TIME
		  FROM TR_RKT_PANEN_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT_PANEN RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
		WHERE     RKT.COST_ELEMENT = 'LABOUR'
		$where
		UNION ALL
		-- INI UNTUK PREMI PANEN BRD
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   'TM' ACTIVITY_GROUP,
			   RKT.COST_ELEMENT,
			   RKT.ACTIVITY_CODE,
			   'PREMI PANEN BRD' ACTIVITY_DESC,
			   '' SUB_COST_ELEMENT,
			   '' MATERIAL_NAME,
			   '' KETERANGAN,
			   'HK' UOM,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_HK
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS QTY_DEC,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT JAN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JAN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT FEB
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_FEB,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT MAR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT APR
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_APR,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT MAY
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_MAY,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT JUN
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUN,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT JUL
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_JUL,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT AUG
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_AUG,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT SEP
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_SEP,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT OCT
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_OCT,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT NOV
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_NOV,
			   ( (RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_BRD
				  * (SELECT DEC
					   FROM TM_SEBARAN_PRODUKSI
					  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
							AND BA_CODE = RKT.BA_CODE))
				/ 100)
				  AS COST_DEC,
			   RKT_INDUK.BIAYA_PEMANEN_HK AS QTY_SETAHUN,   
			   RKT_INDUK.BIAYA_PEMANEN_RP_PREMI_JANJANG AS COST_SETAHUN,
			   '".$this->_userName."' AS INSERT_USER,
			   SYSDATE AS INSERT_TIME
		  FROM TR_RKT_PANEN_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT_PANEN RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
		WHERE     RKT.COST_ELEMENT = 'LABOUR'
		$where
		UNION ALL
		-- UNTUK PERHITUGAN ALAT PANEN (TOOLS)
		SELECT PERIOD_BUDGET,
               REGION_CODE,
               BA_CODE,
               AFD_CODE,
               BLOCK_CODE,
               'TM' ACTIVITY_GROUP,
               COST_ELEMENT,
               ACTIVITY_CODE,
               'ALAT PANEN' ACTIVITY_DESC,
               SUB_COST_ELEMENT,
               MATERIAL_NAME,
               '' KETERANGAN,
               UOM,
               ((PERSEN_JAN/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_JAN,
               ((PERSEN_FEB/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_FEB,
               ((PERSEN_MAR/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_MAR,
               ((PERSEN_APR/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_APR,
               ((PERSEN_MAY/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_MAY,
               ((PERSEN_JUN/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_JUN,
               ((PERSEN_JUL/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_JUL,
               ((PERSEN_AUG/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_AUG,
               ((PERSEN_SEP/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_SEP,
               ((PERSEN_OCT/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_OCT,
               ((PERSEN_NOV/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_NOV,
               ((PERSEN_DEC/100 * TON * 1000 * RP_KG_ALAT) / PRICE) QTY_DEC,
               (PERSEN_JAN/100 * TON * 1000 * RP_KG_ALAT) AS COST_JAN,
               (PERSEN_FEB/100 * TON * 1000 * RP_KG_ALAT) AS COST_FEB,
               (PERSEN_MAR/100 * TON * 1000 * RP_KG_ALAT) AS COST_MAR,
               (PERSEN_APR/100 * TON * 1000 * RP_KG_ALAT) AS COST_APR,
               (PERSEN_MAY/100 * TON * 1000 * RP_KG_ALAT) AS COST_MAY,
               (PERSEN_JUN/100 * TON * 1000 * RP_KG_ALAT) AS COST_JUN,
               (PERSEN_JUL/100 * TON * 1000 * RP_KG_ALAT) AS COST_JUL,
               (PERSEN_AUG/100 * TON * 1000 * RP_KG_ALAT) AS COST_AUG,
               (PERSEN_SEP/100 * TON * 1000 * RP_KG_ALAT) AS COST_SEP,
               (PERSEN_OCT/100 * TON * 1000 * RP_KG_ALAT) AS COST_OCT,
               (PERSEN_NOV/100 * TON * 1000 * RP_KG_ALAT) AS COST_NOV,
               (PERSEN_DEC/100 * TON * 1000 * RP_KG_ALAT) AS COST_DEC,
               (((PERSEN_JAN/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_FEB/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_MAR/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_APR/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_MAY/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_JUN/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_JUL/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_AUG/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_SEP/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_OCT/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_NOV/100 * TON * 1000 * RP_KG_ALAT) / PRICE) +
               ((PERSEN_DEC/100 * TON * 1000 * RP_KG_ALAT) / PRICE))
               AS QTY_SETAHUN,
               ((PERSEN_JAN/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_FEB/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_MAR/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_APR/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_MAY/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_JUN/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_JUL/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_AUG/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_SEP/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_OCT/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_NOV/100 * TON * 1000 * RP_KG_ALAT) +
               (PERSEN_DEC/100 * TON * 1000 * RP_KG_ALAT) ) AS COST_SETAHUN,
               '".$this->_userName."' AS INSERT_USER,
               SYSDATE AS INSERT_TIME
               FROM (
				SELECT RKT.PERIOD_BUDGET,
				   ORG.REGION_CODE,
				   RKT.BA_CODE,
				   RKT.AFD_CODE,
				   RKT.BLOCK_CODE,
				   'TM' ACTIVITY_GROUP,
				   RKT.ACTIVITY_CODE,
				   RKT.COST_ELEMENT,
				   ALAT.MATERIAL_CODE AS SUB_COST_ELEMENT,
				   MATERIAL.MATERIAL_NAME,
				   '' KETERANGAN,
				   MATERIAL.UOM,
				   ALAT.PRICE,
				   ALAT.TOTAL,
				   RKT_INDUK.TON,
				   RKT_INDUK.BIAYA_ALAT_PANEN_RP_KG,
				   (TOTAL / (SELECT SUM (TOTAL)
					  FROM TN_ALAT_KERJA_PANEN
						WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) * RKT_INDUK.BIAYA_ALAT_PANEN_RP_KG) AS RP_KG_ALAT, 
						    ((ALAT.TOTAL/(SELECT SUM (PRICE) FROM TN_ALAT_KERJA_PANEN WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE)*
						    (SELECT SUM (BIAYA_ALAT_PANEN_RP_TOTAL) FROM TR_RKT_PANEN WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE))/ALAT.PRICE) AS BANYAK_ALAT,
						    (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_JAN,
						    (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_FEB,
						    (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_MAR,
							(SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_APR,
							(SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_MAY,
							(SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_JUN,
							(SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_JUL,
							(SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_AUG,
							(SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_SEP,
							(SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_OCT,
							(SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_NOV,
							(SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET AND BA_CODE = RKT.BA_CODE) AS PERSEN_DEC
						FROM TR_RKT_PANEN_COST_ELEMENT RKT
						   LEFT JOIN TR_RKT_PANEN RKT_INDUK
							  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
						   LEFT JOIN TM_ORGANIZATION ORG
							  ON ORG.BA_CODE = RKT.BA_CODE
						   LEFT JOIN  TN_ALAT_KERJA_PANEN ALAT
							  ON RKT.PERIOD_BUDGET = ALAT.PERIOD_BUDGET
							  AND RKT.BA_CODE = ALAT.BA_CODE
						   LEFT JOIN TM_MATERIAL MATERIAL
							  ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
							  AND MATERIAL.BA_CODE = RKT.BA_CODE
							  AND MATERIAL.MATERIAL_CODE = ALAT.MATERIAL_CODE   
					WHERE     RKT.COST_ELEMENT = 'TOOLS'
					$where
        )
		UNION ALL
		-- INI UNTUK PERHITUNGAN SUPERVISI	5101030701 SUPERVISI PEMANEN LABOUR
		SELECT 
			PERIOD_BUDGET,
			REGION_CODE,
			BA_CODE,
			AFD_CODE,
			BLOCK_CODE,
			ACTIVITY_GROUP,
			COST_ELEMENT,
			ACTIVITY_CODE,
			ACTIVITY_DESC,
			'' SUB_COST_ELEMENT,
			'' MATERIAL_NAME,
			'' KETERANGAN,
			'MPP' UOM,
			(TON/TOTAL_TON * JUMLAH * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JAN,
			(TON/TOTAL_TON * JUMLAH * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_FEB,
			(TON/TOTAL_TON * JUMLAH * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAR,
			(TON/TOTAL_TON * JUMLAH * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_APR,
			(TON/TOTAL_TON * JUMLAH * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAY,
			(TON/TOTAL_TON * JUMLAH * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUN,
			(TON/TOTAL_TON * JUMLAH * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUL,
			(TON/TOTAL_TON * JUMLAH * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_AUG,
			(TON/TOTAL_TON * JUMLAH * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_SEP,
			(TON/TOTAL_TON * JUMLAH * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_OCT,
			(TON/TOTAL_TON * JUMLAH * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_NOV,
			(TON/TOTAL_TON * JUMLAH * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_DEC,
			(BIAYA_SPV_RP_TOTAL * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JAN,
			(BIAYA_SPV_RP_TOTAL * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_FEB,
			(BIAYA_SPV_RP_TOTAL * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAR,
			(BIAYA_SPV_RP_TOTAL * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_APR,
			(BIAYA_SPV_RP_TOTAL * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAY,
			(BIAYA_SPV_RP_TOTAL * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUN,
			(BIAYA_SPV_RP_TOTAL * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUL,
			(BIAYA_SPV_RP_TOTAL * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_AUG,
			(BIAYA_SPV_RP_TOTAL * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_SEP,
			(BIAYA_SPV_RP_TOTAL * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_OCT,
			(BIAYA_SPV_RP_TOTAL * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_NOV,
			(BIAYA_SPV_RP_TOTAL * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_DEC,
			(TON/TOTAL_TON * JUMLAH) AS QTY_SETAHUN,
			BIAYA_SPV_RP_TOTAL AS COST_SETAHUN,
			'".$this->_userName."' AS INSERT_USER,
		   SYSDATE AS INSERT_TIME
		FROM (
	SELECT
		   RKT.PERIOD_BUDGET,
		   ORG.REGION_CODE,
		   RKT.BA_CODE,
		   RKT.AFD_CODE,
		   RKT.BLOCK_CODE,
		   'TM' ACTIVITY_GROUP,
		   RKT.COST_ELEMENT,
		   '5101030701-1' ACTIVITY_CODE,
		   'SUPERVISI PEMANEN' ACTIVITY_DESC,
		   RKT.TON,
		   (SELECT SUM(TON) FROM TR_RKT_PANEN WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE) TOTAL_TON,
		   (SELECT SUM(MPP_PERIOD_BUDGET) FROM TR_RKT_CHECKROLL 
			  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE
				 AND JOB_CODE IN ('FX140', 'FX230')) AS JUMLAH,
		   RKT.BIAYA_SPV_RP_TOTAL
	  FROM TR_RKT_PANEN_COST_ELEMENT RKT
		   LEFT JOIN TR_RKT_PANEN RKT_INDUK
			  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
		   LEFT JOIN TM_ORGANIZATION ORG
			  ON ORG.BA_CODE = RKT.BA_CODE
	 WHERE  RKT.COST_ELEMENT = 'LABOUR'
		$where
		   ) SS 
	UNION ALL
	-- INI UNTUK PERHITUNGAN SUPERVISI	5101030701 KRANI BUAH LABOUR
		   SELECT 
				PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				ACTIVITY_GROUP,
				COST_ELEMENT,
				ACTIVITY_CODE,
				ACTIVITY_DESC,
				'' SUB_COST_ELEMENT,
				'' MATERIAL_NAME,
				'' KETERANGAN,
				'MPP' UOM,
				(TON/TOTAL_TON * JUMLAH * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JAN,
				(TON/TOTAL_TON * JUMLAH * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_FEB,
				(TON/TOTAL_TON * JUMLAH * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAR,
				(TON/TOTAL_TON * JUMLAH * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_APR,
				(TON/TOTAL_TON * JUMLAH * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAY,
				(TON/TOTAL_TON * JUMLAH * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUN,
				(TON/TOTAL_TON * JUMLAH * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUL,
				(TON/TOTAL_TON * JUMLAH * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_AUG,
				(TON/TOTAL_TON * JUMLAH * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_SEP,
				(TON/TOTAL_TON * JUMLAH * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_OCT,
				(TON/TOTAL_TON * JUMLAH * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_NOV,
				(TON/TOTAL_TON * JUMLAH * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_DEC,
				(KRANI_BUAH_TOTAL * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JAN,
				(KRANI_BUAH_TOTAL * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_FEB,
				(KRANI_BUAH_TOTAL * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAR,
				(KRANI_BUAH_TOTAL * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_APR,
				(KRANI_BUAH_TOTAL * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAY,
				(KRANI_BUAH_TOTAL * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUN,
				(KRANI_BUAH_TOTAL * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUL,
				(KRANI_BUAH_TOTAL * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_AUG,
				(KRANI_BUAH_TOTAL * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_SEP,
				(KRANI_BUAH_TOTAL * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_OCT,
				(KRANI_BUAH_TOTAL * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_NOV,
				(KRANI_BUAH_TOTAL * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_DEC,
				(TON/TOTAL_TON * JUMLAH) AS QTY_SETAHUN,
				KRANI_BUAH_TOTAL AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
			   SYSDATE AS INSERT_TIME
		FROM (
	SELECT RKT.PERIOD_BUDGET,
		   ORG.REGION_CODE,
		   RKT.BA_CODE,
		   RKT.AFD_CODE,
		   RKT.BLOCK_CODE,
		   'TM' ACTIVITY_GROUP,
		   RKT.COST_ELEMENT,
		   '5101030701-2' ACTIVITY_CODE,
		   'KRANI BUAH' ACTIVITY_DESC,
		   RKT.TON,
		   (SELECT SUM(TON) FROM TR_RKT_PANEN WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE) TOTAL_TON,
		   (SELECT SUM(MPP_PERIOD_BUDGET) FROM TR_RKT_CHECKROLL 
			  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE
				 AND JOB_CODE IN ('FX160')) AS JUMLAH,
		   RKT.KRANI_BUAH_TOTAL
	  FROM TR_RKT_PANEN_COST_ELEMENT RKT
		   LEFT JOIN TR_RKT_PANEN RKT_INDUK
			  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
		   LEFT JOIN TM_ORGANIZATION ORG
			  ON ORG.BA_CODE = RKT.BA_CODE
	 WHERE RKT.COST_ELEMENT = 'LABOUR'
		$where
		   ) SS      
	UNION ALL
	-- INI UNTUK PERHITUNGAN SUPERVISI	5101030701 BONGKAR MUAT LABOUR
		   SELECT 
				PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				ACTIVITY_GROUP,
				COST_ELEMENT,
				ACTIVITY_CODE,
				ACTIVITY_DESC,
				'' SUB_COST_ELEMENT,
				'' MATERIAL_NAME,
				'' KETERANGAN,
				'MPP' UOM,
				(TON/TOTAL_TON * JUMLAH * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JAN,
				(TON/TOTAL_TON * JUMLAH * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_FEB,
				(TON/TOTAL_TON * JUMLAH * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAR,
				(TON/TOTAL_TON * JUMLAH * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_APR,
				(TON/TOTAL_TON * JUMLAH * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_MAY,
				(TON/TOTAL_TON * JUMLAH * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUN,
				(TON/TOTAL_TON * JUMLAH * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_JUL,
				(TON/TOTAL_TON * JUMLAH * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_AUG,
				(TON/TOTAL_TON * JUMLAH * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_SEP,
				(TON/TOTAL_TON * JUMLAH * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_OCT,
				(TON/TOTAL_TON * JUMLAH * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_NOV,
				(TON/TOTAL_TON * JUMLAH * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS QTY_DEC,
				(TUKANG_MUAT_TOTAL * (SELECT JAN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JAN,
				(TUKANG_MUAT_TOTAL * (SELECT FEB FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_FEB,
				(TUKANG_MUAT_TOTAL * (SELECT MAR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAR,
				(TUKANG_MUAT_TOTAL * (SELECT APR FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_APR,
				(TUKANG_MUAT_TOTAL * (SELECT MAY FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_MAY,
				(TUKANG_MUAT_TOTAL * (SELECT JUN FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUN,
				(TUKANG_MUAT_TOTAL * (SELECT JUL FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_JUL,
				(TUKANG_MUAT_TOTAL * (SELECT AUG FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_AUG,
				(TUKANG_MUAT_TOTAL * (SELECT SEP FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_SEP,
				(TUKANG_MUAT_TOTAL * (SELECT OCT FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_OCT,
				(TUKANG_MUAT_TOTAL * (SELECT NOV FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_NOV,
				(TUKANG_MUAT_TOTAL * (SELECT DEC FROM TM_SEBARAN_PRODUKSI WHERE PERIOD_BUDGET = SS.PERIOD_BUDGET AND BA_CODE = SS.BA_CODE) / 100) AS COST_DEC,
				(TON/TOTAL_TON * JUMLAH) AS QTY_SETAHUN,
				TUKANG_MUAT_TOTAL AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
			   SYSDATE AS INSERT_TIME
		FROM (
	SELECT RKT.PERIOD_BUDGET,
		   ORG.REGION_CODE,
		   RKT.BA_CODE,
		   RKT.AFD_CODE,
		   RKT.BLOCK_CODE,
		   'TM' ACTIVITY_GROUP,
		   RKT.COST_ELEMENT,
		   '5101030404-1' ACTIVITY_CODE,
		   'BONGKAR MUAT' ACTIVITY_DESC,
		   RKT.TON,
		   (SELECT SUM(TON) FROM TR_RKT_PANEN WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE) TOTAL_TON,
		   (SELECT SUM(MPP_PERIOD_BUDGET) FROM TR_RKT_CHECKROLL 
			  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND BA_CODE = RKT.BA_CODE
				 AND JOB_CODE IN ('FW041')) AS JUMLAH,
		   RKT.TUKANG_MUAT_TOTAL
	  FROM TR_RKT_PANEN_COST_ELEMENT RKT
		   LEFT JOIN TR_RKT_PANEN RKT_INDUK
			  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
		   LEFT JOIN TM_ORGANIZATION ORG
			  ON ORG.BA_CODE = RKT.BA_CODE
	 WHERE  RKT.COST_ELEMENT = 'LABOUR'
			$where
		   ) SS		   
		   UNION ALL
		   --untuk BIAYA lain lain supervisi perawatan
		   SELECT RKT.PERIOD_BUDGET,
		   ORG.REGION_CODE,
			RKT.BA_CODE,
				   HS.AFD_CODE,
				   HS.BLOCK_CODE,
				   'TM' TIPE_TRANSAKSI,
					'' COST_ELEMENT,
					RKT.COA_CODE AS ACTIVITY_CODE,
					RKT.COA_DESC AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT,
					'' AS SUB_COST_ELEMENT_DESC,
				   '' AS KETERANGAN,
					'' UOM,
					(HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JAN,
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_FEB,
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAR,
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_APR,
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAY,
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JUN,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_JUL,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_AUG,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SEP,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_OCT,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_NOV,
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_DEC,
				   (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
					  AS COST_JAN,
				   (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
					  AS COST_FEB,
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
						  AS COST_MAR,
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
						  AS COST_APR,
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
						  AS COST_MAY,
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
						  AS COST_JUN,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
						  AS COST_JUL,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
						  AS COST_AUG,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
						  AS COST_SEP,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
						  AS COST_OCT,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
						  AS COST_NOV,
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC)
						  AS COST_DEC,
					(HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) +
                    (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SETAHUN,
						   (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
						  +
					   (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
						  +
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
						  +
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
						  +
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
						  +
				  (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
						  +
				  (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC) AS COST_SETAHUN,
				  '".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
				  FROM V_TOTAL_RELATION_COST RKT
				   LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
									   HS.BA_CODE,
									   HS.AFD_CODE,
									   HS.BLOCK_CODE,
									   SUM (HS.SMS1_TM) SMS1_TM,
									   SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
									   SUM (HS.SMS2_TM) SMS2_TM,
									   SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
								  FROM V_REPORT_SEBARAN_HS_BLOCK HS
							  GROUP BY HS.PERIOD_BUDGET,
									   HS.BA_CODE,
									   HS.AFD_CODE,
									   HS.BLOCK_CODE) HS
					  ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						 AND HS.BA_CODE = RKT.BA_CODE
				   LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
									   HS.BA_CODE,
									   SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
									   SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
								  FROM V_REPORT_SEBARAN_HS_BLOCK HS
							  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS2
					  ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						 AND HS2.BA_CODE = RKT.BA_CODE
						 LEFT JOIN TM_ORGANIZATION ORG
                    ON ORG.BA_CODE = RKT.BA_CODE
					LEFT JOIN (
                     SELECT TRC.PERIOD_BUDGET, TRC.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
                      FROM TR_RKT_CHECKROLL TRC
                     WHERE TRC.JOB_CODE in ('FX130','FX110')
                    GROUP BY  TRC.PERIOD_BUDGET, TRC.BA_CODE 
                     )MPP_ALL 
				 ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
				 AND MPP_ALL.BA_CODE = RKT.BA_CODE
			 WHERE     RKT.COA_CODE = '43800'
				   $where		   
		";

		$this->_db->query($query);
		$this->_db->commit();
		
		//labour pemupukan (majemuk tunggal)
		$xquery = "INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (PERIOD_BUDGET,
                                        REGION_CODE,
                                        BA_CODE,
                                        AFD_CODE,
                                        BLOCK_CODE,
                                        TIPE_TRANSAKSI,
                                        COST_ELEMENT,
                                        ACTIVITY_CODE,
                                        ACTIVITY_DESC,
                                        SUB_COST_ELEMENT,
                                        SUB_COST_ELEMENT_DESC,
                                        KETERANGAN,
                                        UOM,
                                        QTY_JAN,
                                        QTY_FEB,
                                        QTY_MAR,
                                        QTY_APR,
                                        QTY_MAY,
                                        QTY_JUN,
                                        QTY_JUL,
                                        QTY_AUG,
                                        QTY_SEP,
                                        QTY_OCT,
                                        QTY_NOV,
                                        QTY_DEC,
                                        COST_JAN,
                                        COST_FEB,
                                        COST_MAR,
                                        COST_APR,
                                        COST_MAY,
                                        COST_JUN,
                                        COST_JUL,
                                        COST_AUG,
                                        COST_SEP,
                                        COST_OCT,
                                        COST_NOV,
                                        COST_DEC,
                                        QTY_SETAHUN,
                                        COST_SETAHUN,
                                        INSERT_USER,
                                        INSERT_TIME)                
			 SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					TIPE_TRANSAKSI,
					COST_ELEMENT,
					ACTIVITY_CODE,
					ACTIVITY_DESC,
					SUB_COST_ELEMENT,
					MATERIAL_NAME,
					'' KETERANGAN,
					UOM,
					SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
					SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
					SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
					SUM (NVL (QTY_APR, 0)) AS QTY_APR,
					SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
					SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
					SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
					SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
					SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
					SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
					SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
					SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
					SUM (NVL (COST_JAN, 0)) AS COST_JAN,
					SUM (NVL (COST_FEB, 0)) AS COST_FEB,
					SUM (NVL (COST_MAR, 0)) AS COST_MAR,
					SUM (NVL (COST_APR, 0)) AS COST_APR,
					SUM (NVL (COST_MAY, 0)) AS COST_MAY,
					SUM (NVL (COST_JUN, 0)) AS COST_JUN,
					SUM (NVL (COST_JUL, 0)) AS COST_JUL,
					SUM (NVL (COST_AUG, 0)) AS COST_AUG,
					SUM (NVL (COST_SEP, 0)) AS COST_SEP,
					SUM (NVL (COST_OCT, 0)) AS COST_OCT,
					SUM (NVL (COST_NOV, 0)) AS COST_NOV,
					SUM (NVL (COST_DEC, 0)) AS COST_DEC,
					(  SUM (NVL (QTY_JAN, 0))
					 + SUM (NVL (QTY_FEB, 0))
					 + SUM (NVL (QTY_MAR, 0))
					 + SUM (NVL (QTY_APR, 0))
					 + SUM (NVL (QTY_MAY, 0))
					 + SUM (NVL (QTY_JUN, 0))
					 + SUM (NVL (QTY_JUL, 0))
					 + SUM (NVL (QTY_AUG, 0))
					 + SUM (NVL (QTY_SEP, 0))
					 + SUM (NVL (QTY_OCT, 0))
					 + SUM (NVL (QTY_NOV, 0))
					 + SUM (NVL (QTY_DEC, 0)))
					   AS QTY_SETAHUN,
					(  SUM (NVL (COST_JAN, 0))
					 + SUM (NVL (COST_FEB, 0))
					 + SUM (NVL (COST_MAR, 0))
					 + SUM (NVL (COST_APR, 0))
					 + SUM (NVL (COST_MAY, 0))
					 + SUM (NVL (COST_JUN, 0))
					 + SUM (NVL (COST_JUL, 0))
					 + SUM (NVL (COST_AUG, 0))
					 + SUM (NVL (COST_SEP, 0))
					 + SUM (NVL (COST_OCT, 0))
					 + SUM (NVL (COST_NOV, 0))
					 + SUM (NVL (COST_DEC, 0)))
					   AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
			   FROM (
					 -- INI UNTUK PERHITUNGAN PEMUPUKAN (MAJEMUK TUNGGAL)
					 SELECT PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							AFD_CODE,
							BLOCK_CODE,
							ACTIVITY_GROUP AS TIPE_TRANSAKSI,
							COST_ELEMENT,
							ACTIVITY_CODE,
							ACTIVITY_DESC,
							SUB_COST_ELEMENT,
							MATERIAL_NAME,
							'' RANK_Z,
							UOM,
							JAN_QTY AS QTY_JAN,
							FEB_QTY AS QTY_FEB,
							MAR_QTY AS QTY_MAR,
							APR_QTY AS QTY_APR,
							MAY_QTY AS QTY_MAY,
							JUN_QTY AS QTY_JUN,
							JUL_QTY AS QTY_JUL,
							AUG_QTY AS QTY_AUG,
							SEP_QTY AS QTY_SEP,
							OCT_QTY AS QTY_OCT,
							NOV_QTY AS QTY_NOV,
							DEC_QTY AS QTY_DEC,
							JAN_COST AS COST_JAN,
							FEB_COST AS COST_FEB,
							MAR_COST AS COST_MAR,
							APR_COST AS COST_APR,
							MAY_COST AS COST_MAY,
							JUN_COST AS COST_JUN,
							JUL_COST AS COST_JUL,
							AUG_COST AS COST_AUG,
							SEP_COST AS COST_SEP,
							OCT_COST AS COST_OCT,
							NOV_COST AS COST_NOV,
							DEC_COST AS COST_DEC
					   FROM (SELECT *        -- UNTUK PERHITUNGAN SMS 1 PUPUK  MAJEMUK
							   FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS1
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS1,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
														'PUPUK MAJEMUK' DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                                                $where
                                                        )HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020400' -- UNTUK PUPUK  MAJEMUK
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('1' JAN,
                                               '2' FEB,
                                               '3' MAR,
                                               '4' APR,
                                               '5' MAY,
                                               '6' JUN,
                                               '91' JUL,
                                               '92' AUG,
                                               '93' SEP,
                                               '94' OCT,
                                               '95' NOV,
                                               '96' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 2 PUPUK  MAJEMUK
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS2
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS2,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
														'PUPUK MAJEMUK' DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                                                $where
                                                        )HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020400' -- UNTUK PUPUK  MAJEMUK
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('91' JAN,
                                               '92' FEB,
                                               '93' MAR,
                                               '94' APR,
                                               '95' MAY,
                                               '96' JUN,
                                               '7' JUL,
                                               '8' AUG,
                                               '9' SEP,
                                               '10' OCT,
                                               '11' NOV,
                                               '12' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 1 PUPUK  TUNGGAL
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS1
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS1,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
														'PUPUK TUNGGAL' DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                                                $where
                                                        )HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020300' -- UNTUK PUPUK  TUNGGAL
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('1' JAN,
                                               '2' FEB,
                                               '3' MAR,
                                               '4' APR,
                                               '5' MAY,
                                               '6' JUN,
                                               '97' JUL,
                                               '98' AUG,
                                               '99' SEP,
                                               '90' OCT,
                                               '91' NOV,
                                               '92' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 2 PUPUK  TUNGGAL
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS2
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS2,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
														'PUPUK TUNGGAL' DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                                                $where
                                                        )HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020300' -- UNTUK PUPUK  TUNGGAL
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('91' JAN,
                                               '92' FEB,
                                               '93' MAR,
                                               '94' APR,
                                               '95' MAY,
                                               '96' JUN,
                                               '7' JUL,
                                               '8' AUG,
                                               '9' SEP,
                                               '10' OCT,
                                               '11' NOV,
                                               '12' DEC))))
                                               ) REPORT
				   GROUP BY PERIOD_BUDGET,
							REGION_CODE,
							BA_CODE,
							AFD_CODE,
							BLOCK_CODE,
							TIPE_TRANSAKSI,
							COST_ELEMENT,
							ACTIVITY_CODE,
							ACTIVITY_DESC,
							SUB_COST_ELEMENT,
							MATERIAL_NAME,
							UOM
				UNION ALL --PERHITUNGAN PUPUK MAJEMUK & TUNGGAL (MATERIAL)
   SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE TIPE_TRANSAKSI,
          'MATERIAL' AS COST_ELEMENT,
          RKT.COA_CODE AS ACTIVITY_CODE,
          'PUPUK MAJEMUK' AS ACTIVITY_DESC,
          RKT.MATERIAL_CODE AS SUB_COST_ELEMENT,
          RKT.MATERIAL_NAME,
          '' KETERANGAN,
          'KG' UOM,
          RKT.QTY_MAJEMUK_JAN AS QTY_JAN,
          RKT.QTY_MAJEMUK_FEB AS QTY_FEB,
          RKT.QTY_MAJEMUK_MAR AS QTY_MAR,
          RKT.QTY_MAJEMUK_APR AS QTY_APR,
          RKT.QTY_MAJEMUK_MAY AS QTY_MAY,
          RKT.QTY_MAJEMUK_JUN AS QTY_JUN,
          RKT.QTY_MAJEMUK_JUL AS QTY_JUL,
          RKT.QTY_MAJEMUK_AUG AS QTY_AUG,
          RKT.QTY_MAJEMUK_SEP AS QTY_SEP,
          RKT.QTY_MAJEMUK_OCT AS QTY_OCT,
          RKT.QTY_MAJEMUK_NOV AS QTY_NOV,
          RKT.QTY_MAJEMUK_DEC AS QTY_DEC,
          (RKT.QTY_MAJEMUK_JAN * HARGA.PRICE) AS COST_JAN,
          (RKT.QTY_MAJEMUK_FEB * HARGA.PRICE) AS COST_FEB,
          (RKT.QTY_MAJEMUK_MAR * HARGA.PRICE) AS COST_MAR,
          (RKT.QTY_MAJEMUK_APR * HARGA.PRICE) AS COST_APR,
          (RKT.QTY_MAJEMUK_MAY * HARGA.PRICE) AS COST_MAY,
          (RKT.QTY_MAJEMUK_JUN * HARGA.PRICE) AS COST_JUN,
          (RKT.QTY_MAJEMUK_JUL * HARGA.PRICE) AS COST_JUL,
          (RKT.QTY_MAJEMUK_AUG * HARGA.PRICE) AS COST_AUG,
          (RKT.QTY_MAJEMUK_SEP * HARGA.PRICE) AS COST_SEP,
          (RKT.QTY_MAJEMUK_OCT * HARGA.PRICE) AS COST_OCT,
          (RKT.QTY_MAJEMUK_NOV * HARGA.PRICE) AS COST_NOV,
          (RKT.QTY_MAJEMUK_DEC * HARGA.PRICE) AS COST_DEC,
		  (  RKT.QTY_MAJEMUK_JAN
           + RKT.QTY_MAJEMUK_FEB
           + RKT.QTY_MAJEMUK_MAR
           + RKT.QTY_MAJEMUK_APR
           + RKT.QTY_MAJEMUK_MAY
           + RKT.QTY_MAJEMUK_JUN
           + RKT.QTY_MAJEMUK_JUL
           + RKT.QTY_MAJEMUK_AUG
           + RKT.QTY_MAJEMUK_SEP
           + RKT.QTY_MAJEMUK_OCT
           + RKT.QTY_MAJEMUK_NOV
           + RKT.QTY_MAJEMUK_DEC)
             AS QTY_SETAHUN,
          (  (RKT.QTY_MAJEMUK_JAN * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_FEB * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_MAR * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_APR * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_MAY * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_JUN * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_JUL * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_AUG * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_SEP * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_OCT * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_NOV * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_DEC * HARGA.PRICE))
             AS COST_SETAHUN,
          '".$this->_userName."' AS INSERT_USER,
          SYSDATE AS INSERT_TIME
     FROM (  SELECT PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE MATERIAL_CODE,
                    ACTIVITY_NAME MATERIAL_NAME,
                    SUM (QTY_MAJEMUK_JAN) QTY_MAJEMUK_JAN,
                    SUM (QTY_MAJEMUK_FEB) QTY_MAJEMUK_FEB,
                    SUM (QTY_MAJEMUK_MAR) QTY_MAJEMUK_MAR,
                    SUM (QTY_MAJEMUK_APR) QTY_MAJEMUK_APR,
                    SUM (QTY_MAJEMUK_MAY) QTY_MAJEMUK_MAY,
                    SUM (QTY_MAJEMUK_JUN) QTY_MAJEMUK_JUN,
                    SUM (QTY_MAJEMUK_JUL) QTY_MAJEMUK_JUL,
                    SUM (QTY_MAJEMUK_AUG) QTY_MAJEMUK_AUG,
                    SUM (QTY_MAJEMUK_SEP) QTY_MAJEMUK_SEP,
                    SUM (QTY_MAJEMUK_OCT) QTY_MAJEMUK_OCT,
                    SUM (QTY_MAJEMUK_NOV) QTY_MAJEMUK_NOV,
                    SUM (QTY_MAJEMUK_DEC) QTY_MAJEMUK_DEC
               FROM (SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            RKT.DIS_JAN QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JAN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            RKT.DIS_FEB QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_FEB
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            RKT.DIS_MAR QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_MAR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            RKT.DIS_APR QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_APR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            RKT.DIS_MAY QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_MAY
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            RKT.DIS_JUN QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JUN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            RKT.DIS_JUL QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JUL
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            RKT.DIS_AUG QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_AUG
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            RKT.DIS_SEP QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_SEP
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            RKT.DIS_OCT QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_OCT
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            RKT.DIS_NOV QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_NOV
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            RKT.DIS_DEC QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_DEC
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400')
           GROUP BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE,
                    ACTIVITY_NAME
           ORDER BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE) RKT
          LEFT JOIN TN_HARGA_BARANG HARGA
             ON     HARGA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                AND HARGA.BA_CODE = RKT.BA_CODE
                AND HARGA.MATERIAL_CODE = RKT.MATERIAL_CODE
                AND HARGA.DELETE_USER IS NULL
                AND HARGA.FLAG_TEMP IS NULL
          LEFT JOIN TM_ORGANIZATION ORG
             ON ORG.BA_CODE = RKT.BA_CODE
    WHERE     1 = 1
          AND RKT.MATURITY_STAGE = 'TM'
          $where
	UNION ALL
	SELECT     RKT.PERIOD_BUDGET,
                        ORG.REGION_CODE,
                        RKT.BA_CODE,
                        RKT.AFD_CODE,
                        RKT.BLOCK_CODE,
                        RKT.MATURITY_STAGE TIPE_TRANSAKSI,
                        'MATERIAL' AS COST_ELEMENT,
                        RKT.COA_CODE AS ACTIVITY_CODE,
                        'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                        RKT.MATERIAL_CODE AS SUB_COST_ELEMENT,
                        RKT.MATERIAL_NAME,
                        '' KETERANGAN,
                        'KG' UOM,
                        RKT.QTY_TUNGGAL_JAN AS QTY_JAN,
                        RKT.QTY_TUNGGAL_FEB AS QTY_FEB,
                        RKT.QTY_TUNGGAL_MAR AS QTY_MAR,
                        RKT.QTY_TUNGGAL_APR AS QTY_APR,
                        RKT.QTY_TUNGGAL_MAY AS QTY_MAY,
                        RKT.QTY_TUNGGAL_JUN AS QTY_JUN,
                        RKT.QTY_TUNGGAL_JUL AS QTY_JUL,
                        RKT.QTY_TUNGGAL_AUG AS QTY_AUG,
                        RKT.QTY_TUNGGAL_SEP AS  QTY_SEP,
                        RKT.QTY_TUNGGAL_OCT AS QTY_OCT,
                        RKT.QTY_TUNGGAL_NOV AS QTY_NOV,
                        RKT.QTY_TUNGGAL_DEC AS QTY_DEC,
                        (RKT.QTY_TUNGGAL_JAN * HARGA.PRICE) AS COST_JAN,
                        (RKT.QTY_TUNGGAL_FEB * HARGA.PRICE) AS COST_FEB,
                        (RKT.QTY_TUNGGAL_MAR * HARGA.PRICE) AS COST_MAR,
                        (RKT.QTY_TUNGGAL_APR * HARGA.PRICE) AS COST_APR,
                        (RKT.QTY_TUNGGAL_MAY * HARGA.PRICE) AS COST_MAY,
                        (RKT.QTY_TUNGGAL_JUN * HARGA.PRICE) AS COST_JUN,
                        (RKT.QTY_TUNGGAL_JUL * HARGA.PRICE) AS COST_JUL,
                        (RKT.QTY_TUNGGAL_AUG * HARGA.PRICE) AS COST_AUG,
                        (RKT.QTY_TUNGGAL_SEP * HARGA.PRICE) AS COST_SEP,
                        (RKT.QTY_TUNGGAL_OCT * HARGA.PRICE) AS COST_OCT,
                        (RKT.QTY_TUNGGAL_NOV * HARGA.PRICE) AS COST_NOV,
                        (RKT.QTY_TUNGGAL_DEC * HARGA.PRICE) AS COST_DEC,
						(RKT.QTY_TUNGGAL_JAN +
                        RKT.QTY_TUNGGAL_FEB +
                        RKT.QTY_TUNGGAL_MAR +
                        RKT.QTY_TUNGGAL_APR +
                        RKT.QTY_TUNGGAL_MAY +
                        RKT.QTY_TUNGGAL_JUN +
                        RKT.QTY_TUNGGAL_JUL +
                        RKT.QTY_TUNGGAL_AUG +
                        RKT.QTY_TUNGGAL_SEP +
                        RKT.QTY_TUNGGAL_OCT +
                        RKT.QTY_TUNGGAL_NOV +
                        RKT.QTY_TUNGGAL_DEC)
                        AS QTY_SETAHUN,
                        ((RKT.QTY_TUNGGAL_JAN * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_FEB * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_MAR * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_APR * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_MAY * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_JUN * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_JUL * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_AUG * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_SEP * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_OCT * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_NOV * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_DEC * HARGA.PRICE)) 
                        AS COST_SETAHUN,
                        '".$this->_userName."' AS INSERT_USER,
                        SYSDATE AS INSERT_TIME
          FROM (
        SELECT PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE MATERIAL_CODE,
                    ACTIVITY_NAME MATERIAL_NAME,
                    SUM (QTY_TUNGGAL_JAN) QTY_TUNGGAL_JAN,
                    SUM (QTY_TUNGGAL_FEB) QTY_TUNGGAL_FEB,
                    SUM (QTY_TUNGGAL_MAR) QTY_TUNGGAL_MAR,
                    SUM (QTY_TUNGGAL_APR) QTY_TUNGGAL_APR,
                    SUM (QTY_TUNGGAL_MAY) QTY_TUNGGAL_MAY,
                    SUM (QTY_TUNGGAL_JUN) QTY_TUNGGAL_JUN,
                    SUM (QTY_TUNGGAL_JUL) QTY_TUNGGAL_JUL,
                    SUM (QTY_TUNGGAL_AUG) QTY_TUNGGAL_AUG,
                    SUM (QTY_TUNGGAL_SEP) QTY_TUNGGAL_SEP,
                    SUM (QTY_TUNGGAL_OCT) QTY_TUNGGAL_OCT,
                    SUM (QTY_TUNGGAL_NOV) QTY_TUNGGAL_NOV,
                    SUM (QTY_TUNGGAL_DEC) QTY_TUNGGAL_DEC
               FROM (
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            RKT.DIS_JAN QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JAN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            RKT.DIS_FEB QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_FEB
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300' 
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            RKT.DIS_MAR QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_MAR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            RKT.DIS_APR QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_APR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            RKT.DIS_MAY QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_MAY
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            RKT.DIS_JUN QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JUN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            RKT.DIS_JUL QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JUL
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            RKT.DIS_AUG QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_AUG
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            RKT.DIS_SEP QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_SEP
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            RKT.DIS_OCT QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_OCT
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            RKT.DIS_NOV QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_NOV
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            RKT.DIS_DEC QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_DEC
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                            )
           GROUP BY PERIOD_BUDGET,
                    BA_CODE,
                    BLOCK_CODE,
                    AFD_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE,
                    ACTIVITY_NAME
           ORDER BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE
        )RKT
                LEFT JOIN TN_HARGA_BARANG HARGA
                    ON HARGA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                    AND HARGA.BA_CODE = RKT.BA_CODE
                    AND HARGA.MATERIAL_CODE = RKT.MATERIAL_CODE
                    AND HARGA.DELETE_USER IS NULL
                    AND HARGA.FLAG_TEMP IS NULL
                LEFT JOIN TM_ORGANIZATION ORG
                    ON ORG.BA_CODE = RKT.BA_CODE
                WHERE 1 = 1
                    AND RKT.MATURITY_STAGE = 'TM'
                    $where  
		UNION ALL 
		--PERHITUNGAN PUPUK MAJEMUK / TUNGGAL SELAIN COST ELEMENT LABOUR DAN MATERIAL
		   SELECT PERIOD_BUDGET,
			   REGION_CODE,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   TIPE_TRANSAKSI,
			   COST_ELEMENT,
			   ACTIVITY_CODE,
			   ACTIVITY_DESC,
			   SUB_COST_ELEMENT,
			   MATERIAL_NAME,
			   KETERANGAN,
			   UOM,
			   QTY_JAN,
			   QTY_FEB,
			   QTY_MAR,
			   QTY_APR,
			   QTY_MAY,
			   QTY_JUN,
			   QTY_JUL,
			   QTY_AUG,
			   QTY_SEP,
			   QTY_OCT,
			   QTY_NOV,
			   QTY_DEC,
			   COST_JAN,
			   COST_FEB,
			   COST_MAR,
			   COST_APR,
			   COST_MAY,
			   COST_JUN,
			   COST_JUL,
			   COST_AUG,
			   COST_SEP,
			   COST_OCT,
			   COST_NOV,
			   COST_DEC,
			   (  QTY_JAN
				+ QTY_FEB
				+ QTY_MAR
				+ QTY_APR
				+ QTY_MAY
				+ QTY_JUN
				+ QTY_JUL
				+ QTY_AUG
				+ QTY_SEP
				+ QTY_OCT
				+ QTY_NOV
				+ QTY_DEC)
				  AS QTY_SETAHUN,
			   (  COST_JAN
				+ COST_FEB
				+ COST_MAR
				+ COST_APR
				+ COST_MAY
				+ COST_JUN
				+ COST_JUL
				+ COST_AUG
				+ COST_SEP
				+ COST_OCT
				+ COST_NOV
				+ COST_DEC)
				  AS COST_SETAHUN,
				   '".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		  FROM (SELECT COST.PERIOD_BUDGET,
               COST.REGION_CODE,
               COST.BA_CODE,
               COST.AFD_CODE,
               COST.BLOCK_CODE,
               KG_PUPUK.MATURITY_STAGE AS TIPE_TRANSAKSI,
               COST.COST_ELEMENT,
               '5101020300' AS ACTIVITY_CODE,
               'PUPUK TUNGGAL' AS ACTIVITY_DESC,
               '' AS SUB_COST_ELEMENT,
               '' AS MATERIAL_NAME,
               '' AS KETERANGAN,
               'KG' UOM,
               0 QTY_JAN,
               0 QTY_FEB,
               0 QTY_MAR,
               0 QTY_APR,
               0 QTY_MAY,
               0 QTY_JUN,
               0 QTY_JUL,
               0 QTY_AUG,
               0 QTY_SEP,
               0 QTY_OCT,
               0 QTY_NOV,
               0 QTY_DEC,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_JAN
                      / KG_PUPUK.QTY_TOTAL_JAN
                      * COST.DIS_COST_JAN)
                  ELSE
                     0
               END
                  AS COST_JAN,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_FEB
                      / KG_PUPUK.QTY_TOTAL_FEB
                      * COST.DIS_COST_FEB)
                  ELSE
                     0
               END
                  AS COST_FEB,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_MAR
                      / KG_PUPUK.QTY_TOTAL_MAR
                      * COST.DIS_COST_MAR)
                  ELSE
                     0
               END
                  AS COST_MAR,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_APR
                      / KG_PUPUK.QTY_TOTAL_APR
                      * COST.DIS_COST_APR)
                  ELSE
                     0
               END
                  AS COST_APR,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_MAY
                      / KG_PUPUK.QTY_TOTAL_MAY
                      * COST.DIS_COST_MAY)
                  ELSE
                     0
               END
                  AS COST_MAY,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_JUN
                      / KG_PUPUK.QTY_TOTAL_JUN
                      * COST.DIS_COST_JUN)
                  ELSE
                     0
               END
                  AS COST_JUN,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_JUL
                      / KG_PUPUK.QTY_TOTAL_JUL
                      * COST.DIS_COST_JUL)
                  ELSE
                     0
               END
                  AS COST_JUL,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_AUG
                      / KG_PUPUK.QTY_TOTAL_AUG
                      * COST.DIS_COST_AUG)
                  ELSE
                     0
               END
                  AS COST_AUG,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_SEP
                      / KG_PUPUK.QTY_TOTAL_SEP
                      * COST.DIS_COST_SEP)
                  ELSE
                     0
               END
                  AS COST_SEP,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_OCT
                      / KG_PUPUK.QTY_TOTAL_OCT
                      * COST.DIS_COST_OCT)
                  ELSE
                     0
               END
                  AS COST_OCT,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_NOV
                      / KG_PUPUK.QTY_TOTAL_NOV
                      * COST.DIS_COST_NOV)
                  ELSE
                     0
               END
                  AS COST_NOV,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
                  THEN
                     (  KG_PUPUK.QTY_TUNGGAL_DEC
                      / KG_PUPUK.QTY_TOTAL_DEC
                      * COST.DIS_COST_DEC)
                  ELSE
                     0
               END
                  AS COST_DEC
          FROM    (  SELECT PERIOD_BUDGET,
                            REGION_CODE,
                            BA_CODE,
                            AFD_CODE,
                            BLOCK_CODE,
                            COST_ELEMENT,
                            MATURITY_STAGE,
                            SUM (DIS_COST_JAN) DIS_COST_JAN,
                            SUM (DIS_COST_FEB) DIS_COST_FEB,
                            SUM (DIS_COST_MAR) DIS_COST_MAR,
                            SUM (DIS_COST_APR) DIS_COST_APR,
                            SUM (DIS_COST_MAY) DIS_COST_MAY,
                            SUM (DIS_COST_JUN) DIS_COST_JUN,
                            SUM (DIS_COST_JUL) DIS_COST_JUL,
                            SUM (DIS_COST_AUG) DIS_COST_AUG,
                            SUM (DIS_COST_SEP) DIS_COST_SEP,
                            SUM (DIS_COST_OCT) DIS_COST_OCT,
                            SUM (DIS_COST_NOV) DIS_COST_NOV,
                            SUM (DIS_COST_DEC) DIS_COST_DEC,
                            MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
                            MAX (COST_TOOLS_KG) COST_TOOLS_KG,
                            MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
                       FROM (  SELECT RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
                                      SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
                                      SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
                                      SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
                                      SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
                                      SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
                                      SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
                                      0 DIS_COST_JUL,
                                      0 DIS_COST_AUG,
                                      0 DIS_COST_SEP,
                                      0 DIS_COST_OCT,
                                      0 DIS_COST_NOV,
                                      0 DIS_COST_DEC,
                                      MAX (RKT.COST_LABOUR_POKOK)
                                         AS COST_LABOUR_POKOK,
                                      MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                                      MAX (RKT.COST_TRANSPORT_KG)
                                         AS COST_TRANSPORT_KG
                                 FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                                      LEFT JOIN
                                         TM_ORGANIZATION ORG
                                      ON ORG.BA_CODE = RKT.BA_CODE
                                WHERE RKT.DELETE_USER IS NULL
                                      AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                      AND RKT.COST_ELEMENT NOT IN ('MATERIAL', 'TRANSPORT')
                                      $where
                             GROUP BY RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS1
                             UNION ALL
                               SELECT RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                                      0 DIS_COST_JAN,
                                      0 DIS_COST_FEB,
                                      0 DIS_COST_MAR,
                                      0 DIS_COST_APR,
                                      0 DIS_COST_MAY,
                                      0 DIS_COST_JUN,
                                      SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
                                      SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
                                      SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
                                      SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
                                      SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
                                      SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
                                      MAX (RKT.COST_LABOUR_POKOK)
                                         AS COST_LABOUR_POKOK,
                                      MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                                      MAX (RKT.COST_TRANSPORT_KG)
                                         AS COST_TRANSPORT_KG
                                 FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                                      LEFT JOIN
                                         TM_ORGANIZATION ORG
                                      ON ORG.BA_CODE = RKT.BA_CODE
                                WHERE RKT.DELETE_USER IS NULL
                                      AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                      AND RKT.COST_ELEMENT NOT IN ('MATERIAL', 'TRANSPORT')
                                      $where
                             GROUP BY RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS2)
                   GROUP BY PERIOD_BUDGET,
                            REGION_CODE,
                            BA_CODE,
                            AFD_CODE,
                            BLOCK_CODE,
                            COST_ELEMENT,
                            MATURITY_STAGE) COST
               LEFT JOIN
                  V_KG_PUPUK_AFD KG_PUPUK
               ON     COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
                  AND COST.BA_CODE = KG_PUPUK.BA_CODE
                  AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
                  AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
        UNION ALL
        -- RKT PUPUK MAJEMUK SELAIN COST ELEMENT MATERIAL
        SELECT COST.PERIOD_BUDGET,
               COST.REGION_CODE,
               COST.BA_CODE,
               COST.AFD_CODE,
               COST.BLOCK_CODE,
               KG_PUPUK.MATURITY_STAGE AS TIPE_TRANSAKSI,
               COST.COST_ELEMENT,
               '5101020400' AS ACTIVITY_CODE,
               'PUPUK MAJEMUK' AS ACTIVITY_NAME,
               '' AS SUB_COST_ELEMENT,
               '' AS MATERIAL_NAME,
               '' AS KETERANGAN,
               'HK' UOM,
               0 QTY_JAN,
               0 QTY_FEB,
               0 QTY_MAR,
               0 QTY_APR,
               0 QTY_MAY,
               0 QTY_JUN,
               0 QTY_JUL,
               0 QTY_AUG,
               0 QTY_SEP,
               0 QTY_OCT,
               0 QTY_NOV,
               0 QTY_DEC,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_JAN
                      / KG_PUPUK.QTY_TOTAL_JAN
                      * COST.DIS_COST_JAN)
                  ELSE
                     0
               END
                  AS COST_JAN,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_FEB
                      / KG_PUPUK.QTY_TOTAL_FEB
                      * COST.DIS_COST_FEB)
                  ELSE
                     0
               END
                  AS COST_FEB,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_MAR
                      / KG_PUPUK.QTY_TOTAL_MAR
                      * COST.DIS_COST_MAR)
                  ELSE
                     0
               END
                  AS COST_MAR,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_APR
                      / KG_PUPUK.QTY_TOTAL_APR
                      * COST.DIS_COST_APR)
                  ELSE
                     0
               END
                  AS COST_APR,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_MAY
                      / KG_PUPUK.QTY_TOTAL_MAY
                      * COST.DIS_COST_MAY)
                  ELSE
                     0
               END
                  AS COST_MAY,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_JUN
                      / KG_PUPUK.QTY_TOTAL_JUN
                      * COST.DIS_COST_JUN)
                  ELSE
                     0
               END
                  AS COST_JUN,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_JUL
                      / KG_PUPUK.QTY_TOTAL_JUL
                      * COST.DIS_COST_JUL)
                  ELSE
                     0
               END
                  AS COST_JUL,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_AUG
                      / KG_PUPUK.QTY_TOTAL_AUG
                      * COST.DIS_COST_AUG)
                  ELSE
                     0
               END
                  AS COST_AUG,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_SEP
                      / KG_PUPUK.QTY_TOTAL_SEP
                      * COST.DIS_COST_SEP)
                  ELSE
                     0
               END
                  AS COST_SEP,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_OCT
                      / KG_PUPUK.QTY_TOTAL_OCT
                      * COST.DIS_COST_OCT)
                  ELSE
                     0
               END
                  AS COST_OCT,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_NOV
                      / KG_PUPUK.QTY_TOTAL_NOV
                      * COST.DIS_COST_NOV)
                  ELSE
                     0
               END
                  AS COST_NOV,
               CASE
                  WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
                  THEN
                     (  KG_PUPUK.QTY_MAJEMUK_DEC
                      / KG_PUPUK.QTY_TOTAL_DEC
                      * COST.DIS_COST_DEC)
                  ELSE
                     0
               END
                  AS COST_DEC
          FROM    (  SELECT PERIOD_BUDGET,
                            REGION_CODE,
                            BA_CODE,
                            AFD_CODE,
                            BLOCK_CODE,
                            COST_ELEMENT,
                            MATURITY_STAGE,
                            SUM (DIS_COST_JAN) DIS_COST_JAN,
                            SUM (DIS_COST_FEB) DIS_COST_FEB,
                            SUM (DIS_COST_MAR) DIS_COST_MAR,
                            SUM (DIS_COST_APR) DIS_COST_APR,
                            SUM (DIS_COST_MAY) DIS_COST_MAY,
                            SUM (DIS_COST_JUN) DIS_COST_JUN,
                            SUM (DIS_COST_JUL) DIS_COST_JUL,
                            SUM (DIS_COST_AUG) DIS_COST_AUG,
                            SUM (DIS_COST_SEP) DIS_COST_SEP,
                            SUM (DIS_COST_OCT) DIS_COST_OCT,
                            SUM (DIS_COST_NOV) DIS_COST_NOV,
                            SUM (DIS_COST_DEC) DIS_COST_DEC,
                            MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
                            MAX (COST_TOOLS_KG) COST_TOOLS_KG,
                            MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
                       FROM (  SELECT RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
                                      SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
                                      SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
                                      SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
                                      SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
                                      SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
                                      SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
                                      0 DIS_COST_JUL,
                                      0 DIS_COST_AUG,
                                      0 DIS_COST_SEP,
                                      0 DIS_COST_OCT,
                                      0 DIS_COST_NOV,
                                      0 DIS_COST_DEC,
                                      MAX (RKT.COST_LABOUR_POKOK)
                                         AS COST_LABOUR_POKOK,
                                      MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                                      MAX (RKT.COST_TRANSPORT_KG)
                                         AS COST_TRANSPORT_KG
                                 FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                                      LEFT JOIN
                                         TM_ORGANIZATION ORG
                                      ON ORG.BA_CODE = RKT.BA_CODE
                                WHERE RKT.DELETE_USER IS NULL
                                      AND RKT.MATURITY_STAGE_SMS1 = 'TM'
                                      AND RKT.COST_ELEMENT NOT IN('MATERIAL', 'TRANSPORT')
                                      $where
                             GROUP BY RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS1
                             UNION ALL
                               SELECT RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                                      0 DIS_COST_JAN,
                                      0 DIS_COST_FEB,
                                      0 DIS_COST_MAR,
                                      0 DIS_COST_APR,
                                      0 DIS_COST_MAY,
                                      0 DIS_COST_JUN,
                                      SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
                                      SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
                                      SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
                                      SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
                                      SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
                                      SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
                                      MAX (RKT.COST_LABOUR_POKOK)
                                         AS COST_LABOUR_POKOK,
                                      MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                                      MAX (RKT.COST_TRANSPORT_KG)
                                         AS COST_TRANSPORT_KG
                                 FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                                      LEFT JOIN
                                         TM_ORGANIZATION ORG
                                      ON ORG.BA_CODE = RKT.BA_CODE
                                WHERE RKT.DELETE_USER IS NULL
                                      AND RKT.MATURITY_STAGE_SMS2 = 'TM'
                                      AND RKT.COST_ELEMENT NOT IN('MATERIAL', 'TRANSPORT')
                                      $where
                             GROUP BY RKT.PERIOD_BUDGET,
                                      ORG.REGION_CODE,
                                      RKT.BA_CODE,
                                      RKT.AFD_CODE,
                                      RKT.BLOCK_CODE,
                                      RKT.COST_ELEMENT,
                                      RKT.MATURITY_STAGE_SMS2)
                   GROUP BY PERIOD_BUDGET,
                            REGION_CODE,
                            BA_CODE,
                            AFD_CODE,
                            BLOCK_CODE,
                            COST_ELEMENT,
                            MATURITY_STAGE) COST
               LEFT JOIN
                  V_KG_PUPUK_AFD KG_PUPUK
               ON     COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
                  AND COST.BA_CODE = KG_PUPUK.BA_CODE
                  AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
                  AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE)
	UNION ALL
		-- INI UNTUK PERHITUNGAN PUPUK MAJEMUK TRANSPORT
	  SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 (COST_JAN / NULLIF (PRICE_QTY_VRA,0)) AS QTY_JAN,
                 (COST_FEB / NULLIF (PRICE_QTY_VRA,0)) AS QTY_FEB,
                 (COST_MAR / NULLIF (PRICE_QTY_VRA,0)) AS QTY_MAR,
                 (COST_APR / NULLIF (PRICE_QTY_VRA,0)) AS QTY_APR,
                 (COST_MAY / NULLIF (PRICE_QTY_VRA,0)) AS QTY_MAY,
                 (COST_JUN / NULLIF (PRICE_QTY_VRA,0)) AS QTY_JUN,
                 0 QTY_JUL,
                 0 QTY_AUG,
                 0 QTY_SEP,
                 0 QTY_OCT,
                 0 QTY_NOV,
                 0 QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020400' AS ACTIVITY_CODE,
                         'PUPUK MAJEMUK' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JAN) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JAN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JAN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_FEB) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_FEB
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_FEB,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAR) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_MAR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_APR) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_APR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_APR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAY) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_MAY
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAY,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUN) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JUN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUN,
                         0 COST_JUL,
                         0 COST_AUG,
                         0 COST_SEP,
                         0 COST_OCT,
                         0 COST_NOV,
                         0 COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS1,
                                 MATERIAL_CODE_JAN,
                                 DIS_JAN,
                                 MATERIAL_CODE_FEB,
                                 DIS_FEB,
                                 MATERIAL_CODE_MAR,
                                 DIS_MAR,
                                 MATERIAL_CODE_APR,
                                 DIS_APR,
                                 MATERIAL_CODE_MAY,
                                 DIS_MAY,
                                 MATERIAL_CODE_JUN,
                                 DIS_JUN
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS1 = 'TM'
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
										$where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE)
          UNION ALL
          SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 0 QTY_JAN,
                 0 QTY_FEB,
                 0 QTY_MAR,
                 0 QTY_APR,
                 0 QTY_MAY,
                 0 QTY_JUN,
                 (COST_JUL / NULLIF(PRICE_QTY_VRA,0)) AS QTY_JUL,
                 (COST_AUG / NULLIF(PRICE_QTY_VRA,0)) AS QTY_AUG,
                 (COST_SEP / NULLIF(PRICE_QTY_VRA,0)) AS QTY_SEP,
                 (COST_OCT / NULLIF(PRICE_QTY_VRA,0)) AS QTY_OCT,
                 (COST_NOV / NULLIF(PRICE_QTY_VRA,0)) AS QTY_NOV,
                 (COST_DEC / NULLIF(PRICE_QTY_VRA,0)) AS QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020400' AS ACTIVITY_CODE,
                         'PUPUK MAJEMUK' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         0 COST_JAN,
                         0 COST_FEB,
                         0 COST_MAR,
                         0 COST_APR,
                         0 COST_MAY,
                         0 COST_JUN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUL) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JUL
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUL,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_AUG) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_AUG
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_AUG,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_SEP) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_SEP
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_SEP,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_OCT) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_OCT
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_OCT,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_NOV) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_NOV
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_NOV,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_DEC) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_DEC
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS2,
                                 MATERIAL_CODE_JUL,
                                 DIS_JUL,
                                 MATERIAL_CODE_AUG,
                                 DIS_AUG,
                                 MATERIAL_CODE_SEP,
                                 DIS_SEP,
                                 MATERIAL_CODE_OCT,
                                 DIS_OCT,
                                 MATERIAL_CODE_NOV,
                                 DIS_NOV,
                                 MATERIAL_CODE_DEC,
                                 DIS_DEC
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS2 = 'TM'
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
										$where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE))
					GROUP BY PERIOD_BUDGET,
							 REGION_CODE,
							 BA_CODE,
							 AFD_CODE,
							 BLOCK_CODE,
							 TIPE_TRANSAKSI,
							 COST_ELEMENT,
							 ACTIVITY_CODE,
							 ACTIVITY_DESC,
							 SUB_COST_ELEMENT,
							 MATERIAL_NAME,
							 UOM
	UNION ALL
		-- INI UNTUK PERHITUNGAN PUPUK TUNGGAL TRANSPORT
		SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 (COST_JAN / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JAN,
                 (COST_FEB / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_FEB,
                 (COST_MAR / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_MAR,
                 (COST_APR / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_APR,
                 (COST_MAY / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_MAY,
                 (COST_JUN / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JUN,
                 0 QTY_JUL,
                 0 QTY_AUG,
                 0 QTY_SEP,
                 0 QTY_OCT,
                 0 QTY_NOV,
                 0 QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020300' AS ACTIVITY_CODE,
                         'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JAN) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JAN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JAN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_FEB) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_FEB
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_FEB,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAR) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_MAR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_APR) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_APR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_APR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAY) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_MAY
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAY,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUN) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JUN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUN,
                         0 COST_JUL,
                         0 COST_AUG,
                         0 COST_SEP,
                         0 COST_OCT,
                         0 COST_NOV,
                         0 COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS1,
                                 MATERIAL_CODE_JAN,
                                 DIS_JAN,
                                 MATERIAL_CODE_FEB,
                                 DIS_FEB,
                                 MATERIAL_CODE_MAR,
                                 DIS_MAR,
                                 MATERIAL_CODE_APR,
                                 DIS_APR,
                                 MATERIAL_CODE_MAY,
                                 DIS_MAY,
                                 MATERIAL_CODE_JUN,
                                 DIS_JUN
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS1 = 'TM'
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
									   $where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE)
          UNION ALL
          SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 0 QTY_JAN,
                 0 QTY_FEB,
                 0 QTY_MAR,
                 0 QTY_APR,
                 0 QTY_MAY,
                 0 QTY_JUN,
                 (COST_JUL / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JUL,
                 (COST_AUG / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_AUG,
                 (COST_SEP / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_SEP,
                 (COST_OCT / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_OCT,
                 (COST_NOV / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_NOV,
                 (COST_DEC / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020300' AS ACTIVITY_CODE,
                         'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         0 COST_JAN,
                         0 COST_FEB,
                         0 COST_MAR,
                         0 COST_APR,
                         0 COST_MAY,
                         0 COST_JUN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUL) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JUL
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUL,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_AUG) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_AUG
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_AUG,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_SEP) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_SEP
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_SEP,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_OCT) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_OCT
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_OCT,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_NOV) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_NOV
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_NOV,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_DEC) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_DEC
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS2,
                                 MATERIAL_CODE_JUL,
                                 DIS_JUL,
                                 MATERIAL_CODE_AUG,
                                 DIS_AUG,
                                 MATERIAL_CODE_SEP,
                                 DIS_SEP,
                                 MATERIAL_CODE_OCT,
                                 DIS_OCT,
                                 MATERIAL_CODE_NOV,
                                 DIS_NOV,
                                 MATERIAL_CODE_DEC,
                                 DIS_DEC
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS2 = 'TM'
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
									   $where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE))
		GROUP BY PERIOD_BUDGET,
				 REGION_CODE,
				 BA_CODE,
				 AFD_CODE,
				 BLOCK_CODE,
				 TIPE_TRANSAKSI,
				 COST_ELEMENT,
				 ACTIVITY_CODE,
				 ACTIVITY_DESC,
				 SUB_COST_ELEMENT,
				 MATERIAL_NAME,
				 UOM				 
	UNION ALL	
	-- INI UNTUK PERHITUNGAN PUPUK TUNGGAL TOOLS
		SELECT PERIOD_BUDGET,
		 REGION_CODE,
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         TIPE_TRANSAKSI,
         COST_ELEMENT,
         ACTIVITY_CODE,
         ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         KETERANGAN,
         UOM,
         MAX (QTY_JAN) QTY_JAN,
         MAX (QTY_FEB) QTY_FEB,
         MAX (QTY_MAR) QTY_MAR,
         MAX (QTY_APR) QTY_APR,
         MAX (QTY_MAY) QTY_MAY,
         MAX (QTY_JUN) QTY_JUN,
         MAX (QTY_JUL) QTY_JUL,
         MAX (QTY_AUG) QTY_AUG,
         MAX (QTY_SEP) QTY_SEP,
         MAX (QTY_OCT) QTY_OCT,
         MAX (QTY_NOV) QTY_NOV,
         MAX (QTY_DEC) QTY_DEC,
         SUM (COST_JAN) AS COST_JAN,
         SUM (COST_FEB) AS COST_FEB,
         SUM (COST_MAR) AS COST_MAR,
         SUM (COST_APR) AS COST_APR,
         SUM (COST_MAY) AS COST_MAY,
         SUM (COST_JUN) AS COST_JUN,
         SUM (COST_JUL) AS COST_JUL,
         SUM (COST_AUG) AS COST_AUG,
         SUM (COST_SEP) AS COST_SEP,
         SUM (COST_OCT) AS COST_OCT,
         SUM (COST_NOV) AS COST_NOV,
         SUM (COST_DEC) AS COST_DEC,
         MAX (QTY_JAN) + 
         MAX (QTY_FEB) +
         MAX (QTY_MAR) +
         MAX (QTY_APR) +
         MAX (QTY_MAY) +
         MAX (QTY_JUN) +
         MAX (QTY_JUL) +
         MAX (QTY_AUG) +
         MAX (QTY_SEP) +
         MAX (QTY_OCT) +
         MAX (QTY_NOV) +
         MAX (QTY_DEC) QTY_SETAHUN,         
         SUM (COST_JAN) +
         SUM (COST_FEB) +
         SUM (COST_MAR) +
         SUM (COST_APR) +
         SUM (COST_MAY) +
         SUM (COST_JUN) +
         SUM (COST_JUL) +
         SUM (COST_AUG) +
         SUM (COST_SEP) +
         SUM (COST_OCT) +
         SUM (COST_NOV) +
         SUM (COST_DEC) AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
         SYSDATE AS INSERT_TIME
		FROM (SELECT RKT.PERIOD_BUDGET,
					 ORG.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					 'TOOLS' AS COST_ELEMENT,
					 '5101020300' AS ACTIVITY_CODE,
					 'PUPUK TUNGGAL' AS ACTIVITY_DESC,
					 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
					 TM.MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'POKOK' AS UOM,
					 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JAN,
					 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_FEB,
					 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_MAR,
					 NVL (DIS_APR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_APR,
					 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_MAY,
					 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JUN,
					 0 AS QTY_JUL,
					 0 AS QTY_AUG,
					 0 AS QTY_SEP,
					 0 AS QTY_OCT,
					 0 AS QTY_NOV,
					 0 AS QTY_DEC,
					 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL AS COST_JAN,
					 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL AS COST_FEB,
					 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL AS COST_MAR,
					 NVL (DIS_APR, 0) * RP_QTY_INTERNAL AS COST_APR,
					 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL AS COST_MAY,
					 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL AS COST_JUN,
					 0 AS COST_JUL,
					 0 AS COST_AUG,
					 0 AS COST_SEP,
					 0 AS COST_OCT,
					 0 AS COST_NOV,
					 0 AS COST_DEC
				FROM TR_RKT_PUPUK_DISTRIBUSI RKT
					 LEFT JOIN TM_HECTARE_STATEMENT THS
						ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND RKT.BA_CODE = THS.BA_CODE
						   AND RKT.AFD_CODE = THS.AFD_CODE
						   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
					 LEFT JOIN TN_INFRASTRUKTUR TNI
						ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND TNI.BA_CODE = THS.BA_CODE
						   AND ACTIVITY_CODE = '43750'
					 LEFT JOIN TM_MATERIAL TM
						ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND TM.BA_CODE = TNI.BA_CODE
						   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TN_HARGA_BARANG THB
						ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND THB.BA_CODE = TNI.BA_CODE
						   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TM_ORGANIZATION ORG
						ON 	   ORG.BA_CODE = RKT.BA_CODE
			   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
					 AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					 AND TNI.COST_ELEMENT = 'TOOLS'
					 AND TM.COA_CODE = '5101020300'
					 $where3
			  UNION ALL
			  SELECT RKT.PERIOD_BUDGET,
					 ORG.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
					 'TOOLS' AS COST_ELEMENT,
					 '5101020300' AS ACTIVITY_CODE,
					 'PUPUK TUNGGAL' AS ACTIVITY_DESC,
					 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
					 TM.MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'POKOK' AS UOM,
					 0 AS QTY_JAN,
					 0 AS QTY_FEB,
					 0 AS QTY_MAR,
					 0 AS QTY_APR,
					 0 AS QTY_MAY,
					 0 AS QTY_JUN,
					 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JUL,
					 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_AUG,
					 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_SEP,
					 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_OCT,
					 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_NOV,
					 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_DEC,
					 0 AS COST_JAN,
					 0 AS COST_FEB,
					 0 AS COST_MAR,
					 0 AS COST_APR,
					 0 AS COST_MAY,
					 0 AS COST_JUN,
					 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL AS COST_JUL,
					 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL AS COST_AUG,
					 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL AS COST_SEP,
					 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL AS COST_OCT,
					 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL AS COST_NOV,
					 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL AS COST_DEC
				FROM TR_RKT_PUPUK_DISTRIBUSI RKT
					 LEFT JOIN TM_HECTARE_STATEMENT THS
						ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND RKT.BA_CODE = THS.BA_CODE
						   AND RKT.AFD_CODE = THS.AFD_CODE
						   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
					 LEFT JOIN TN_INFRASTRUKTUR TNI
						ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND TNI.BA_CODE = THS.BA_CODE
						   AND ACTIVITY_CODE = '43750'
					 LEFT JOIN TM_MATERIAL TM
						ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND TM.BA_CODE = TNI.BA_CODE
						   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TN_HARGA_BARANG THB
						ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND THB.BA_CODE = TNI.BA_CODE
						   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TM_ORGANIZATION ORG
						ON 	   ORG.BA_CODE = RKT.BA_CODE
			   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
					 AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					 AND TNI.COST_ELEMENT = 'TOOLS'
					 AND TM.COA_CODE = '5101020300'					 
					 $where3)
	GROUP BY PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 KETERANGAN,
			 UOM
	UNION ALL
	-- INI UNTUK PERHITUNGAN PUPUK MAJEMUK TOOLS
	SELECT PERIOD_BUDGET,
		 REGION_CODE,
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         TIPE_TRANSAKSI,
         COST_ELEMENT,
         ACTIVITY_CODE,
         ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         KETERANGAN,
         UOM,
         MAX (QTY_JAN) QTY_JAN,
         MAX (QTY_FEB) QTY_FEB,
         MAX (QTY_MAR) QTY_MAR,
         MAX (QTY_APR) QTY_APR,
         MAX (QTY_MAY) QTY_MAY,
         MAX (QTY_JUN) QTY_JUN,
         MAX (QTY_JUL) QTY_JUL,
         MAX (QTY_AUG) QTY_AUG,
         MAX (QTY_SEP) QTY_SEP,
         MAX (QTY_OCT) QTY_OCT,
         MAX (QTY_NOV) QTY_NOV,
         MAX (QTY_DEC) QTY_DEC,
         SUM (COST_JAN) AS COST_JAN,
         SUM (COST_FEB) AS COST_FEB,
         SUM (COST_MAR) AS COST_MAR,
         SUM (COST_APR) AS COST_APR,
         SUM (COST_MAY) AS COST_MAY,
         SUM (COST_JUN) AS COST_JUN,
         SUM (COST_JUL) AS COST_JUL,
         SUM (COST_AUG) AS COST_AUG,
         SUM (COST_SEP) AS COST_SEP,
         SUM (COST_OCT) AS COST_OCT,
         SUM (COST_NOV) AS COST_NOV,
         SUM (COST_DEC) AS COST_DEC,
         MAX (QTY_JAN) + 
         MAX (QTY_FEB) +
         MAX (QTY_MAR) +
         MAX (QTY_APR) +
         MAX (QTY_MAY) +
         MAX (QTY_JUN) +
         MAX (QTY_JUL) +
         MAX (QTY_AUG) +
         MAX (QTY_SEP) +
         MAX (QTY_OCT) +
         MAX (QTY_NOV) +
         MAX (QTY_DEC) QTY_SETAHUN,         
         SUM (COST_JAN) +
         SUM (COST_FEB) +
         SUM (COST_MAR) +
         SUM (COST_APR) +
         SUM (COST_MAY) +
         SUM (COST_JUN) +
         SUM (COST_JUL) +
         SUM (COST_AUG) +
         SUM (COST_SEP) +
         SUM (COST_OCT) +
         SUM (COST_NOV) +
         SUM (COST_DEC) AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
         SYSDATE AS INSERT_TIME
			FROM (SELECT RKT.PERIOD_BUDGET,
						 ORG.REGION_CODE,
						 RKT.BA_CODE,
						 RKT.AFD_CODE,
						 RKT.BLOCK_CODE,
						 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
						 'TOOLS' AS COST_ELEMENT,
						 '5101020400' AS ACTIVITY_CODE,
						 'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
						 TM.MATERIAL_NAME,
						 '' AS KETERANGAN,
						 'POKOK' AS UOM,
						 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_JAN,
						 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_FEB,
						 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_MAR,
						 NVL (DIS_APR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_APR,
						 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_MAY,
						 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_JUN,
						 0 AS QTY_JUL,
						 0 AS QTY_AUG,
						 0 AS QTY_SEP,
						 0 AS QTY_OCT,
						 0 AS QTY_NOV,
						 0 AS QTY_DEC,
						 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL AS COST_JAN,
						 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL AS COST_FEB,
						 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL AS COST_MAR,
						 NVL (DIS_APR, 0) * RP_QTY_INTERNAL AS COST_APR,
						 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL AS COST_MAY,
						 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL AS COST_JUN,
						 0 AS COST_JUL,
						 0 AS COST_AUG,
						 0 AS COST_SEP,
						 0 AS COST_OCT,
						 0 AS COST_NOV,
						 0 AS COST_DEC
					FROM TR_RKT_PUPUK_DISTRIBUSI RKT
						 LEFT JOIN TM_HECTARE_STATEMENT THS
							ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
							   AND RKT.BA_CODE = THS.BA_CODE
							   AND RKT.AFD_CODE = THS.AFD_CODE
							   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
						 LEFT JOIN TN_INFRASTRUKTUR TNI
							ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
							   AND TNI.BA_CODE = THS.BA_CODE
							   AND ACTIVITY_CODE = '43750'
						 LEFT JOIN TM_MATERIAL TM
							ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
							   AND TM.BA_CODE = TNI.BA_CODE
							   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
						 LEFT JOIN TN_HARGA_BARANG THB
							ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
							   AND THB.BA_CODE = TNI.BA_CODE
							   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
						 LEFT JOIN TM_ORGANIZATION ORG
							ON 	   ORG.BA_CODE = RKT.BA_CODE
				   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
						 AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						 AND TNI.COST_ELEMENT = 'TOOLS'
						 AND TM.COA_CODE = '5101020400'
						 $where3
				  UNION
				  SELECT RKT.PERIOD_BUDGET,
						 ORG.REGION_CODE,
						 RKT.BA_CODE,
						 RKT.AFD_CODE,
						 RKT.BLOCK_CODE,
						 RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
						 'TOOLS' AS COST_ELEMENT,
						 '5101020400' AS ACTIVITY_CODE,
						 'PUPUK MAJEMUK' AS ACTIVITY_DESC,
						 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
						 TM.MATERIAL_NAME,
						 '' AS KETERANGAN,
						 'POKOK' AS UOM,
						 0 AS QTY_JAN,
						 0 AS QTY_FEB,
						 0 AS QTY_MAR,
						 0 AS QTY_APR,
						 0 AS QTY_MAY,
						 0 AS QTY_JUN,
						 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_JUL,
						 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_AUG,
						 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_SEP,
						 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_OCT,
						 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_NOV,
						 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
							AS QTY_DEC,
						 0 AS COST_JAN,
						 0 AS COST_FEB,
						 0 AS COST_MAR,
						 0 AS COST_APR,
						 0 AS COST_MAY,
						 0 AS COST_JUN,
						 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL AS COST_JUL,
						 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL AS COST_AUG,
						 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL AS COST_SEP,
						 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL AS COST_OCT,
						 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL AS COST_NOV,
						 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL AS COST_DEC
					FROM TR_RKT_PUPUK_DISTRIBUSI RKT
						 LEFT JOIN TM_HECTARE_STATEMENT THS
							ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
							   AND RKT.BA_CODE = THS.BA_CODE
							   AND RKT.AFD_CODE = THS.AFD_CODE
							   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
						 LEFT JOIN TN_INFRASTRUKTUR TNI
							ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
							   AND TNI.BA_CODE = THS.BA_CODE
							   AND ACTIVITY_CODE = '43750'
						 LEFT JOIN TM_MATERIAL TM
							ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
							   AND TM.BA_CODE = TNI.BA_CODE
							   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
						 LEFT JOIN TN_HARGA_BARANG THB
							ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
							   AND THB.BA_CODE = TNI.BA_CODE
							   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
						 LEFT JOIN TM_ORGANIZATION ORG
							ON 	   ORG.BA_CODE = RKT.BA_CODE
				   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
						 AND RKT.MATURITY_STAGE_SMS1 = 'TM'
						 AND TNI.COST_ELEMENT = 'TOOLS'
						 AND TM.COA_CODE = '5101020400'                 
						 $where3)
		GROUP BY PERIOD_BUDGET,
				 REGION_CODE,
				 BA_CODE,
				 AFD_CODE,
				 BLOCK_CODE,
				 TIPE_TRANSAKSI,
				 COST_ELEMENT,
				 ACTIVITY_CODE,
				 ACTIVITY_DESC,
				 SUB_COST_ELEMENT,
				 MATERIAL_NAME,
				 KETERANGAN,
				 UOM				 
	UNION ALL
		-- INI UNTUK DISTRIBUSI NON INFRA PENGANKUTAN INTERNAL TBS(5101030504-1)
	SELECT   RKTFIRST.PERIOD_BUDGET,
                          RKTFIRST.REGION_CODE,
                          RKTFIRST.BA_CODE,
                          RKTFIRST.AFD_CODE,
                          RKTFIRST.BLOCK_CODE,
                          'TM' AS TIPE_TRANSAKSI,
                          'TRANSPORT' AS COST_ELEMENT,
                          '5101030504-1' AS ACTIVITY_CODE, 
                          'PENGANGKUTAN INTERNAL TBS - VRA' AS ACTIVITY_DESC,
                          RKTFIRST.VRA_CODE AS SUB_COST_ELEMENT, 
                          RKTFIRST.VRA_CODE||' - '||RKTFIRST.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                          '' KETERANGAN,
                          RKTFIRST.UOM,
                          (RKTFIRST.JAN/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJAN,
                          (RKTFIRST.FEB/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISFEB,
                          (RKTFIRST.MAR/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISMAR,
                          (RKTFIRST.APR/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISAPR,
                          (RKTFIRST.MAY/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISMAY,
                          (RKTFIRST.JUN/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJUN,
                          (RKTFIRST.JUL/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJUL,
                          (RKTFIRST.AUG/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISAUG,
                          (RKTFIRST.SEP/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISSEP,
                          (RKTFIRST.OCT/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISOCT,
                          (RKTFIRST.NOV/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISNOV,
                          (RKTFIRST.DEC/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISDEC,
                          (RKTFIRST.JAN/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTJAN,
                          (RKTFIRST.FEB/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTFEB,
                          (RKTFIRST.MAR/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTMAR,
                          (RKTFIRST.APR/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTAPR,
                          (RKTFIRST.MAY/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTMAY,
                          (RKTFIRST.JUN/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTJUN,
                          (RKTFIRST.JUL/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTJUL,
                          (RKTFIRST.AUG/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTAUG,
                          (RKTFIRST.SEP/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTSEP,
                          (RKTFIRST.OCT/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTOCT,
                          (RKTFIRST.NOV/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTNOV,
                          (RKTFIRST.DEC/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) COSTDEC,
                          ((RKTFIRST.JAN/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.FEB/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.MAR/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.APR/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.MAY/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.JUN/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.JUL/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.AUG/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.SEP/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.OCT/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.NOV/RKTSEC.TOTAL*RKTFIRST.HM_KM) +
                          (RKTFIRST.DEC/RKTSEC.TOTAL*RKTFIRST.HM_KM)) DIS_SETAHUN,
                          ((RKTFIRST.JAN/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.FEB/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.MAR/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.APR/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.MAY/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.JUN/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.JUL/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.AUG/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.SEP/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.OCT/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.NOV/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM) +
                          (RKTFIRST.DEC/RKTSEC.TOTAL*RKTFIRST.PRICE_HM_KM)) COS_SETAHUN,
						  '".$this->_userName."' AS INSERT_USER,
						  SYSDATE AS INSERT_TIME
                    FROM (SELECT RKT.PERIOD_BUDGET,
                          ORG.REGION_CODE,  
                          RKT.BA_CODE,
                          RKT.AFD_CODE,
                          RKT.BLOCK_CODE,
                          TMVRA.VRA_CODE,
                          TMVRA.VRA_SUB_CAT_DESCRIPTION, 
                          TMVRA.UOM,
                          RKT.JAN,
                          RKT.FEB,
                          RKT.MAR,
                          RKT.APR,
                          RKT.MAY,
                          RKT.JUN,
                          RKT.JUL,
                          RKT.AUG,
                          RKT.SEP,
                          RKT.OCT,
                          RKT.NOV,
                          RKT.DEC,
                          VRADIS.HM_KM,
                          VRADIS.PRICE_HM_KM
                     FROM TR_PRODUKSI_PERIODE_BUDGET RKT
                          LEFT JOIN 
                          (select BA_CODE, PERIOD_BUDGET, VRA_CODE, 
                          sum(HM_KM) HM_KM, sum(PRICE_HM_KM) PRICE_HM_KM from TR_RKT_VRA_DISTRIBUSI VRADIS
                             where
                                VRADIS.TIPE_TRANSAKSI = 'NON_INFRA'
                                AND VRADIS.ACTIVITY_CODE = '5101030504'
                                group by BA_CODE, PERIOD_BUDGET, VRA_CODE
                           ) VRADIS
                           ON VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                           AND VRADIS.BA_CODE = RKT.BA_CODE
                         LEFT JOIN TM_VRA TMVRA 
                            ON TMVRA.VRA_CODE = VRADIS.VRA_CODE 
                         LEFT JOIN TM_ORGANIZATION ORG
                             ON ORG.BA_CODE = RKT.BA_CODE   
                    WHERE RKT.DELETE_USER IS NULL
                          $where
                          ) RKTFIRST
            LEFT JOIN (              
            SELECT RKT.PERIOD_BUDGET,
                          RKT.BA_CODE,
                          (SUM (RKT.JAN) +
                          SUM (RKT.FEB) +
                          SUM (RKT.MAR) +
                          SUM (RKT.APR) +
                          SUM (RKT.MAY) +
                          SUM (RKT.JUN) +
                          SUM (RKT.JUL) +
                          SUM (RKT.AUG) +
                          SUM (RKT.SEP) +
                          SUM (RKT.OCT) +
                          SUM (RKT.NOV) +
                          SUM (RKT.DEC) ) TOTAL
                     FROM TR_PRODUKSI_PERIODE_BUDGET RKT
						LEFT JOIN TM_ORGANIZATION ORG
                             ON ORG.BA_CODE = RKT.BA_CODE
                          LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRADIS
                             ON     VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                AND VRADIS.BA_CODE = RKT.BA_CODE
                                AND VRADIS.LOCATION_CODE = RKT.AFD_CODE
                                AND VRADIS.TIPE_TRANSAKSI = 'NON_INFRA'
                                AND VRADIS.ACTIVITY_CODE = '5101030504'
                    WHERE RKT.DELETE_USER IS NULL
                          $where
                    GROUP BY RKT.PERIOD_BUDGET,
                          RKT.BA_CODE
                          ) RKTSEC
           ON RKTFIRST.PERIOD_BUDGET = RKTSEC.PERIOD_BUDGET
           AND RKTFIRST.BA_CODE = RKTSEC.BA_CODE           
	UNION ALL
	-- INI UNTUK PERHITUNGAN LANGSIR TRANSPORT & LABOUR (5101030404-2)
		-- INI UNTUK LANGSIR TRANSPORT
		SELECT RKT.PERIOD_BUDGET,
               ORG.REGION_CODE,
               RKT.BA_CODE,
               RKT.AFD_CODE,
               RKT.BLOCK_CODE,
               'TM' AS TIPE_TRANSAKSI,
               'TRANSPORT' COST_ELEMENT,
               '5101030404-2' ACTIVITY_CODE,
               'LANGSIR' ACTIVITY_DESC,
               '' SUB_COST_ELEMENT,
               '' SUB_COST_ELEMENT_DESC,
               '' KETERANGAN,
               'HM' UOM,
               RQTY.QTY_JAN AS QTY_JAN,
               RQTY.QTY_FEB AS QTY_FEB,
               RQTY.QTY_MAR AS QTY_MAR,
               RQTY.QTY_APR AS QTY_APR,
               RQTY.QTY_MAY AS QTY_MAY,
               RQTY.QTY_JUN AS QTY_JUN,
               RQTY.QTY_JUL AS QTY_JUL,
               RQTY.QTY_AUG AS QTY_AUG,
               RQTY.QTY_SEP AS QTY_SEP,
               RQTY.QTY_OCT AS QTY_OCT,
               RQTY.QTY_NOV AS QTY_NOV,
               RQTY.QTY_DEC AS QTY_DEC,
               (BUD.JAN / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_JAN,
               (BUD.FEB / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_FEB,
               (BUD.MAR / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_MAR,
               (BUD.APR / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_APR,
               (BUD.MAY / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_MAY,
               (BUD.JUN / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_JUN,
               (BUD.JUL / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_JUL,
               (BUD.AUG / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_AUG,
               (BUD.SEP / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_SEP,
               (BUD.OCT / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_OCT,
               (BUD.NOV / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_NOV,
               (BUD.DEC / TTL.TOTAL * RKT.LANGSIR_RP) AS COST_DEC,
               (RQTY.QTY_JAN +
               RQTY.QTY_FEB +
               RQTY.QTY_MAR +
               RQTY.QTY_APR +
               RQTY.QTY_MAY +
               RQTY.QTY_JUN +
               RQTY.QTY_JUL +
               RQTY.QTY_AUG +
               RQTY.QTY_SEP +
               RQTY.QTY_OCT +
               RQTY.QTY_NOV +
               RQTY.QTY_DEC) QTY_SETAHUN,
               ((BUD.JAN / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.FEB / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.MAR / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.APR / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.MAY / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.JUN / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.JUL / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.AUG / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.SEP / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.OCT / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.NOV / TTL.TOTAL * RKT.LANGSIR_RP) +
               (BUD.DEC / TTL.TOTAL * RKT.LANGSIR_RP)) COST_SETAHUN,
               '".$this->_userName."' AS INSERT_USER,
               SYSDATE AS INSERT_TIME
          FROM TR_RKT_PANEN RKT
               LEFT JOIN TR_PRODUKSI_PERIODE_BUDGET BUD
                  ON     RKT.PERIOD_BUDGET = BUD.PERIOD_BUDGET
                     AND RKT.BA_CODE = BUD.BA_CODE
                     AND RKT.AFD_CODE = BUD.AFD_CODE
                     AND RKT.BLOCK_CODE = BUD.BLOCK_CODE
               LEFT JOIN (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 (  JAN
                                  + FEB
                                  + MAR
                                  + APR
                                  + MAY
                                  + JUN
                                  + JUL
                                  + AUG
                                  + SEP
                                  + OCT
                                  + NOV
                                  + DEC)
                                    AS TOTAL
                            FROM TR_PRODUKSI_PERIODE_BUDGET) TTL
                  ON     RKT.PERIOD_BUDGET = TTL.PERIOD_BUDGET
                     AND RKT.BA_CODE = TTL.BA_CODE
                     AND RKT.AFD_CODE = TTL.AFD_CODE
                     AND RKT.BLOCK_CODE = TTL.BLOCK_CODE
                LEFT JOIN (SELECT RKT.PERIOD_BUDGET,
                               RKT.BA_CODE,
                               RKT.AFD_CODE,
                               RKT.BLOCK_CODE,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.JAN) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_JAN,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.FEB) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_FEB,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.MAR) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_MAR,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.APR) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_APR,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.MAY) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_MAY,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.JUN) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_JUN,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.JUL) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_JUL,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.AUG) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_AUG,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.SEP) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_SEP,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.OCT) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_OCT,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.NOV) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_NOV,
                               SUM ((RKT.PERSEN_LANGSIR/100*RKT.DEC) / PREMI.TON_TRIP * PREMI.HM_TRIP) AS QTY_DEC
                          FROM    TR_PRODUKSI_PERIODE_BUDGET RKT
                               LEFT JOIN
                                  TN_PANEN_PREMI_LANGSIR PREMI
                               ON RKT.PERIOD_BUDGET = PREMI.PERIOD_BUDGET
                                  AND RKT.BA_CODE = PREMI.BA_CODE
                         GROUP BY 
                            RKT.PERIOD_BUDGET,
                               RKT.BA_CODE,
                               RKT.AFD_CODE,
                               RKT.BLOCK_CODE)  RQTY    
             ON     RKT.PERIOD_BUDGET = RQTY.PERIOD_BUDGET
             AND RKT.BA_CODE = RQTY.BA_CODE
             AND RKT.AFD_CODE = RQTY.AFD_CODE
             AND RKT.BLOCK_CODE = RQTY.BLOCK_CODE              
               LEFT JOIN TM_ORGANIZATION ORG
                  ON ORG.BA_CODE = RKT.BA_CODE
                WHERE 1=1
                $where
		UNION ALL
        -- INI UNTUK LANGSIR LABOUR
        SELECT RKT.PERIOD_BUDGET,
               ORG.REGION_CODE,
               RKT.BA_CODE,
               RKT.AFD_CODE,
               RKT.BLOCK_CODE,
               'TM' AS TIPE_TRANSAKSI,
               'LABOUR' COST_ELEMENT,
               '5101030404-2' ACTIVITY_CODE,
               'LANGSIR' ACTIVITY_DESC,
               '' SUB_COST_ELEMENT,
               '' SUB_COST_ELEMENT_DESC,
               '' KETERANGAN,
               'MPP' UOM,
               0 QTY_JAN,
               0 QTY_FEB,
               0 QTY_MAR,
               0 QTY_APR,
               0 QTY_MAY,
               0 QTY_JUN,
               0 QTY_JUL,
               0 QTY_AUG,
               0 QTY_SEP,
               0 QTY_OCT,
               0 QTY_NOV,
               0 QTY_DEC,
               (BUD.JAN / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_JAN,
               (BUD.FEB / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_FEB,
               (BUD.MAR / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_MAR,
               (BUD.APR / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_APR,
               (BUD.MAY / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_MAY,
               (BUD.JUN / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_JUN,
               (BUD.JUL / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_JUL,
               (BUD.AUG / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_AUG,
               (BUD.SEP / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_SEP,
               (BUD.OCT / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_OCT,
               (BUD.NOV / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_NOV,
               (BUD.DEC / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) AS COST_DEC,
               0 AS QTY_SETAHUN,
               ((BUD.JAN / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.FEB / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.MAR / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.APR / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.MAY / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.JUN / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.JUL / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.AUG / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.SEP / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.OCT / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.NOV / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT) + 
               (BUD.DEC / TTL.TOTAL * RKT.LANGSIR_TUKANG_MUAT)) AS COST_SETAHUN,
               '".$this->_userName."' AS INSERT_USER,
               SYSDATE AS INSERT_TIME
          FROM TR_RKT_PANEN RKT
               LEFT JOIN TR_PRODUKSI_PERIODE_BUDGET BUD
                  ON     RKT.PERIOD_BUDGET = BUD.PERIOD_BUDGET
                     AND RKT.BA_CODE = BUD.BA_CODE
                     AND RKT.AFD_CODE = BUD.AFD_CODE
                     AND RKT.BLOCK_CODE = BUD.BLOCK_CODE
               LEFT JOIN (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 (  JAN
                                  + FEB
                                  + MAR
                                  + APR
                                  + MAY
                                  + JUN
                                  + JUL
                                  + AUG
                                  + SEP
                                  + OCT
                                  + NOV
                                  + DEC)
                                    AS TOTAL
                            FROM TR_PRODUKSI_PERIODE_BUDGET) TTL
                  ON     RKT.PERIOD_BUDGET = TTL.PERIOD_BUDGET
                     AND RKT.BA_CODE = TTL.BA_CODE
                     AND RKT.AFD_CODE = TTL.AFD_CODE
                     AND RKT.BLOCK_CODE = TTL.BLOCK_CODE
               LEFT JOIN TM_ORGANIZATION ORG
                  ON ORG.BA_CODE = RKT.BA_CODE
            WHERE 1=1
			$where
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (ASTEK)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'ASTEK' AS ACTIVITY_CODE, 
				'ASTEK' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1 
	 $xwhere
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	   $xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
		   $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
					   $twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
	 $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
					   $twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (BONUS)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'BONUS' AS ACTIVITY_CODE, 
				'BONUS' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	   $xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
		   $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
					   $twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	   $xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
		   $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
					   $twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1             
	UNION ALL 
	-- INI UNTUK PERHITUNGAN TUNJANGAN (CATU)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'CATU' AS ACTIVITY_CODE, 
				'CATU' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_CATU TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
	 WHERE  1 = 1
			$twhere 
		   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
						TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_CATU TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
				 WHERE   1=1  
					$twhere 
					   AND TCR.TUNJANGAN_TYPE = 'CATU'
					   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_CATU TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
	 WHERE  1 = 1
		$twhere 
		   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
						TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_CATU TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
				 WHERE  1 = 1
						$twhere 
					   AND TCR.TUNJANGAN_TYPE = 'CATU'
					   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (HHR)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'HHR' AS ACTIVITY_CODE, 
				'HHR' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
			$twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1
	UNION ALL
	-- INI UNTUK PERHITUGAN TUNJANGAN (JABATAN)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'' AS COST_ELEMENT,
				'' AS ACTIVITY_CODE, 
				'JABATAN' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE  1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
		$twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
		$twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (KEHADIRAN)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'KEHADIRAN' AS ACTIVITY_CODE, 
				'KEHADIRAN' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE  1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN LAINNYA
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'LAINNYA' AS ACTIVITY_CODE, 
				'LAINNYA' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE  1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE  1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (OBAT)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'OBAT' AS ACTIVITY_CODE, 
				'OBAT' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (PPH21)
	 SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'PPH_21' AS ACTIVITY_CODE, 
				'PPH_21' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
	 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	 $xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
		 $twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (PPH_21)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'PPH_21' AS ACTIVITY_CODE, 
				'PPH_21' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere 
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
		$twhere 
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
					$twhere 
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere 
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
	   $xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
		   $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
					   $twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 
	UNION ALL
	-- INI UNTUK PERHITUNGAN TUNJANGAN (THR)
	SELECT PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
				'LABOUR' AS COST_ELEMENT,
				'THR' AS ACTIVITY_CODE, 
				'THR' AS ACTIVITY_DESC,
				'' AS SUB_COST_ELEMENT, 
				'' AS MATERIAL_NAME,
				'' KETERANGAN,
				'' UOM,
				SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
				SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
				SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
				SUM(NVL (DIS_APR,0)) AS DIS_APR,
				SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
				SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
				SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
				SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
				SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
				SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
				SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
				SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
				SUM(NVL (COST_JAN,0)) AS COST_JAN,
				SUM(NVL (COST_FEB,0)) AS COST_FEB,
				SUM(NVL (COST_MAR,0)) AS COST_MAR,
				SUM(NVL (COST_APR,0)) AS COST_APR,
				SUM(NVL (COST_MAY,0)) AS COST_MAY,
				SUM(NVL (COST_JUN,0)) AS COST_JUN,
				SUM(NVL (COST_JUL,0)) AS COST_JUL,
				SUM(NVL (COST_AUG,0)) AS COST_AUG,
				SUM(NVL (COST_SEP,0)) AS COST_SEP,
				SUM(NVL (COST_OCT,0)) AS COST_OCT,
				SUM(NVL (COST_NOV,0)) AS COST_NOV,
				SUM(NVL (COST_DEC,0)) AS COST_DEC,
				(SUM(NVL (DIS_JAN,0)) +
				SUM(NVL (DIS_FEB,0)) +
				SUM(NVL (DIS_MAR,0)) +
				SUM(NVL (DIS_APR,0)) +
				SUM(NVL (DIS_MAY,0)) +
				SUM(NVL (DIS_JUN,0)) +
				SUM(NVL (DIS_JUL,0)) +
				SUM(NVL (DIS_AUG,0)) +
				SUM(NVL (DIS_SEP,0)) +
				SUM(NVL (DIS_OCT,0)) +
				SUM(NVL (DIS_NOV,0)) +
				SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
				(SUM(NVL (COST_JAN,0)) +
				SUM(NVL (COST_FEB,0)) +
				SUM(NVL (COST_MAR,0)) +
				SUM(NVL (COST_APR,0)) +
				SUM(NVL (COST_MAY,0)) +
				SUM(NVL (COST_JUN,0)) +
				SUM(NVL (COST_JUL,0)) +
				SUM(NVL (COST_AUG,0)) +
				SUM(NVL (COST_SEP,0)) +
				SUM(NVL (COST_OCT,0)) +
				SUM(NVL (COST_NOV,0)) +
				SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
				'".$this->_userName."' AS INSERT_USER,
				SYSDATE AS INSERT_TIME
	FROM (            
	--HITUNG TUNJANGAN UNTUK SMS1
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS1,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
				0 AS DIS_JUL,
				0 AS DIS_AUG,
				0 AS DIS_SEP,
				0 AS DIS_OCT,
				0 AS DIS_NOV,
				0 AS DIS_DEC,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
				0 AS COST_JUL,
				0 AS COST_AUG,
				0 AS COST_SEP,
				0 AS COST_OCT,
				0 AS COST_NOV,
				0 AS COST_DEC       
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS1,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere
	 AND MATURITY_STAGE_SMS1 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE  1 = 1
	 $xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
	 $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
					$twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	UNION ALL
	--HITUNG TUNJANGAN UNTUK SMS2
	SELECT 
				HA_TM.PERIOD_BUDGET,
				ORG.REGION_CODE,
				HA_TM.BA_CODE,
				HA_TM.AFD_CODE,
				HA_TM.BLOCK_CODE,
				HA_TM.MATURITY_STAGE_SMS2,
				0 AS DIS_JAN,
				0 AS DIS_FEB,
				0 AS DIS_MAR,
				0 AS DIS_APR,
				0 AS DIS_MAY,
				0 AS DIS_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
				0 AS COST_JAN,
				0 AS COST_FEB,
				0 AS COST_MAR,
				0 AS COST_APR,
				0 AS COST_MAY,
				0 AS COST_JUN,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
				(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
	FROM (
	SELECT PERIOD_BUDGET,
		   BA_CODE,
		   AFD_CODE,
		   BLOCK_CODE,
		   MATURITY_STAGE_SMS2,
		   HA_PLANTED
	  FROM TM_HECTARE_STATEMENT
	 WHERE 1 = 1
	 $xwhere
	 AND MATURITY_STAGE_SMS2 = 'TM'
	) HA_TM
	LEFT JOIN (       
	SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
	   FROM TM_HECTARE_STATEMENT
	   WHERE 1 = 1
		$xwhere
	GROUP BY PERIOD_BUDGET, BA_CODE
	) HA_BA
	ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND HA_TM.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN (
	SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
	  FROM    TM_TARIF_TUNJANGAN TTJ
		   LEFT JOIN
			  TR_RKT_CHECKROLL TRC
		   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
			  AND TRC.BA_CODE = TTJ.BA_CODE
			  AND TRC.JOB_CODE = TTJ.JOB_CODE
			  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
	 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
	 $twhere
	GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
	) ALL_MPP
	ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
	AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
	LEFT JOIN TM_ORGANIZATION ORG
	ON ORG.BA_CODE = HA_TM.BA_CODE
	LEFT JOIN (
	SELECT PERIOD_BUDGET,
				BA_CODE,
				SUM(COSTTYPE) AS COST_BA
	FROM (            
				SELECT TTJ.PERIOD_BUDGET,
					   TTJ.BA_CODE,
					   TTJ.JOB_CODE,
					   TCR.TUNJANGAN_TYPE,
					   TCR.JUMLAH,
					   TRC.MPP_PERIOD_BUDGET,
					   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
				  FROM TM_TARIF_TUNJANGAN TTJ
					   LEFT JOIN TR_RKT_CHECKROLL TRC
						  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
							 AND TRC.BA_CODE = TTJ.BA_CODE
							 AND TRC.JOB_CODE = TTJ.JOB_CODE
							 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
					   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
						  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
							 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
				 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
					$twhere
	) GROUP BY PERIOD_BUDGET, BA_CODE
	) TTL_COST
	ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
	AND HA_TM.BA_CODE = TTL_COST.BA_CODE
	)
	GROUP BY PERIOD_BUDGET,
				REGION_CODE,
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				MATURITY_STAGE_SMS1 	
	UNION ALL			
	--PERHITUNGAN UNUTUK KEBUTUHAN UMUM
	  SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 'TM' TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT RKT.PERIOD_BUDGET,
					 OPEX.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					 '' AS COST_ELEMENT,
					 OPEX.COA_CODE AS ACTIVITY_CODE,
					 COA.DESCRIPTION AS ACTIVITY_DESC,
					 '' AS SUB_COST_ELEMENT,
					 '' AS MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'HA' AS UOM,
					 0 QTY_JAN,
					 0 QTY_FEB,
					 0 QTY_MAR,
					 0 QTY_APR,
					 0 QTY_MAY,
					 0 QTY_JUN,
					 0 QTY_JUL,
					 0 QTY_AUG,
					 0 QTY_SEP,
					 0 QTY_OCT,
					 0 QTY_NOV,
					 0 QTY_DEC,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JAN)
						AS COST_JAN,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_FEB)
						AS COST_FEB,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_MAR)
						AS COST_MAR,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_APR)
						AS COST_APR,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_MAY)
						AS COST_MAY,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JUN)
						AS COST_JUN,
					 0 COST_JUL,
					 0 COST_AUG,
					 0 COST_SEP,
					 0 COST_OCT,
					 0 COST_NOV,
					 0 COST_DEC
				FROM TM_HECTARE_STATEMENT RKT
					 LEFT JOIN TR_RKT_OPEX OPEX
						ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						   AND OPEX.BA_CODE = RKT.BA_CODE
					 LEFT JOIN TM_COA COA
						ON COA.COA_CODE = OPEX.COA_CODE
			   WHERE     OPEX.COA_CODE NOT IN ('1212010101', '5101030504')
					 AND RKT.MATURITY_STAGE_SMS1 = 'TM'
					 $where3
			  UNION ALL
			  SELECT RKT.PERIOD_BUDGET,
					 OPEX.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					 '' AS COST_ELEMENT,
					 OPEX.COA_CODE AS ACTIVITY_CODE,
					 COA.DESCRIPTION AS ACTIVITY_DESC,
					 '' AS SUB_COST_ELEMENT,
					 '' AS MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'HA' AS UOM,
					 0 QTY_JAN,
					 0 QTY_FEB,
					 0 QTY_MAR,
					 0 QTY_APR,
					 0 QTY_MAY,
					 0 QTY_JUN,
					 0 QTY_JUL,
					 0 QTY_AUG,
					 0 QTY_SEP,
					 0 QTY_OCT,
					 0 QTY_NOV,
					 0 QTY_DEC,
					 0 COST_JAN,
					 0 COST_FEB,
					 0 COST_MAR,
					 0 COST_APR,
					 0 COST_MAY,
					 0 COST_JUN,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JUL)
						AS COST_JUL,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_AUG)
						AS COST_AUG,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_SEP)
						AS COST_SEP,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_OCT)
						AS COST_OCT,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_NOV)
						AS COST_NOV,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_DEC)
						AS COST_DEC
				FROM TM_HECTARE_STATEMENT RKT
					 LEFT JOIN TR_RKT_OPEX OPEX
						ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						   AND OPEX.BA_CODE = RKT.BA_CODE
					 LEFT JOIN TM_COA COA
						ON COA.COA_CODE = OPEX.COA_CODE
			   WHERE     OPEX.COA_CODE NOT IN ('1212010101', '5101030504')
					 AND RKT.MATURITY_STAGE_SMS2 = 'TM'
					 $where3
					 )
	GROUP BY PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 UOM";
		
		$this->_db->query($xquery);
		$this->_db->commit();
		
		return true;		 
	}		
	
	//hapus temp table untuk kebutuhan activity estate cost
	public function delTmpRptKebActEstCostBlock($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		//hapus estate cost per BLOCK
		$query = "
			DELETE FROM TMP_RPT_KEB_EST_COST_BLOCK 
			WHERE 1 = 1
			$where 
		";
		
		$this->_db->query($query);
		$this->_db->commit();
		
		return true;
	}
	
	
	//ARIES 15-JUN-2015
	public function tmpRptKebActDevCostBlock($params = array())
	{
		$where = "";
		$where1 = "";
		$where2 = "";
		$where3 = "";
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where2 .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$where3 .= "
                AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$xwhere .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$twhere .= "
                AND to_char(TTJ.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$where1 .= "
                AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";  
			$where2 .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            "; 
			$where3 .= "
                AND to_char(RKT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$xwhere .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$twhere .= "
                AND to_char(TTJ.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND ORG.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
			$where1 .= "
                AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
			$where2 .= "
                AND UPPER(BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
			$where3 .= "
                AND RKT.BA_CODE = '".$params['key_find']."'
            ";
			$xwhere .= "
                AND BA_CODE = '".$params['key_find']."'
            "; 
			$twhere .= "
                AND TTJ.BA_CODE = '".$params['key_find']."'
            ";
        }
		//generate dev cost per BLOCK
		$query = "
			INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
				PERIOD_BUDGET, 
				REGION_CODE, 
				BA_CODE,
				AFD_CODE,
				BLOCK_CODE,
				TIPE_TRANSAKSI, 
				COST_ELEMENT,
                ACTIVITY_CODE,
                ACTIVITY_DESC,
				SUB_COST_ELEMENT,
				SUB_COST_ELEMENT_DESC,
				KETERANGAN,
				UOM, 
				QTY_JAN, 
				QTY_FEB, 
				QTY_MAR, 
				QTY_APR, 
				QTY_MAY, 
				QTY_JUN, 
				QTY_JUL, 
				QTY_AUG, 
				QTY_SEP, 
				QTY_OCT, 
				QTY_NOV, 
				QTY_DEC,
				COST_JAN, 
				COST_FEB, 
				COST_MAR, 
				COST_APR, 
				COST_MAY, 
				COST_JUN, 
				COST_JUL, 
				COST_AUG, 
				COST_SEP, 
				COST_OCT, 
				COST_NOV, 
				COST_DEC,
				QTY_SETAHUN,
				COST_SETAHUN,
				INSERT_USER, 
				INSERT_TIME
			)
		SELECT PERIOD_BUDGET,
		 REGION_CODE,	  
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         ACTIVITY_GROUP,
         COST_ELEMENT,
         ACTIVITY_CODE,
		 ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         '' KETERANGAN,
		 UOM,
         SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
         SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
         SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
         SUM (NVL (QTY_APR, 0)) AS QTY_APR,
         SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
         SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
         SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
         SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
         SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
         SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
         SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
         SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
         SUM (NVL (COST_JAN, 0)) AS COST_JAN,
         SUM (NVL (COST_FEB, 0)) AS COST_FEB,
         SUM (NVL (COST_MAR, 0)) AS COST_MAR,
         SUM (NVL (COST_APR, 0)) AS COST_APR,
         SUM (NVL (COST_MAY, 0)) AS COST_MAY,
         SUM (NVL (COST_JUN, 0)) AS COST_JUN,
         SUM (NVL (COST_JUL, 0)) AS COST_JUL,
         SUM (NVL (COST_AUG, 0)) AS COST_AUG,
         SUM (NVL (COST_SEP, 0)) AS COST_SEP,
         SUM (NVL (COST_OCT, 0)) AS COST_OCT,
         SUM (NVL (COST_NOV, 0)) AS COST_NOV,
         SUM (NVL (COST_DEC, 0)) AS COST_DEC,
		 (SUM (NVL (QTY_JAN, 0)) + SUM (NVL (QTY_FEB, 0)) + SUM (NVL (QTY_MAR, 0)) 
                              + SUM (NVL (QTY_APR, 0)) + SUM (NVL (QTY_MAY, 0)) + SUM (NVL (QTY_JUN, 0))
                              + SUM (NVL (QTY_JUL, 0)) + SUM (NVL (QTY_AUG, 0)) + SUM (NVL (QTY_SEP, 0)) 
                              + SUM (NVL (QTY_OCT, 0)) + SUM (NVL (QTY_NOV, 0)) + SUM (NVL (QTY_DEC, 0)))
		 AS QTY_SETAHUN,
		 (SUM (NVL (COST_JAN, 0)) + SUM (NVL (COST_FEB, 0)) + SUM (NVL (COST_MAR, 0))
                     + SUM (NVL (COST_APR, 0)) + SUM (NVL (COST_MAY, 0)) + SUM (NVL (COST_JUN, 0))
                     + SUM (NVL (COST_JUL, 0)) + SUM (NVL (COST_AUG, 0)) + SUM (NVL (COST_SEP, 0))
                     + SUM (NVL (COST_OCT, 0)) + SUM (NVL (COST_NOV, 0)) + SUM (NVL (COST_DEC, 0))) 
         AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
		 SYSDATE AS INSERT_TIME
    FROM (-- MANUAL_NON_INFRA SM 1
			SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                    (RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
                                    (RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
                                    (RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
                                    (RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
                                    (RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
                                    (RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JAN * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JAN * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_JAN,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_FEB * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_FEB * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_FEB,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAR * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_MAR * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_MAR,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_APR * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_APR * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_APR,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAY * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_MAY * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_MAY,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUN * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JUN * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_JUN,
                                     0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE =
                                              RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE =
                                              RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
                                     $where
          UNION ALL -- MANUAL_NON_INFRA SM 2
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
                                     (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
                                     (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
                                     (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
                                     (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
                                     (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     CASE
                                     WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUL * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JUL * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_AUG,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_AUG * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_AUG * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_AUG,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_SEP * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_SEP * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_SEP,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_OCT * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_OCT * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_OCT,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_NOV * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_NOV * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_NOV,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_DEC * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_DEC * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE =
                                              RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE =
                                              RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET =
                                              RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE =
                                                 RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET =
                                              BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE =
                                                 BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
                                     $where
		  UNION ALL
			-- HITUNG RAWAT SISIP
			SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   RKT.PLAN_JAN / SPH.SPH_STANDAR AS QTY_JAN,
			   RKT.PLAN_FEB / SPH.SPH_STANDAR AS QTY_FEB,
			   RKT.PLAN_MAR / SPH.SPH_STANDAR AS QTY_MAR,
			   RKT.PLAN_APR / SPH.SPH_STANDAR AS QTY_APR,
			   RKT.PLAN_MAY / SPH.SPH_STANDAR AS QTY_MAY,
			   RKT.PLAN_JUN / SPH.SPH_STANDAR AS QTY_JUN,
			   0 AS QTY_JUL,
			   0 AS QTY_AUG,
			   0 AS QTY_SEP,
			   0 AS QTY_OCT,
			   0 AS QTY_NOV,
			   0 AS QTY_DEC,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JAN,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_FEB,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_MAR,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_APR,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_MAY,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JUN,
			   0 AS COST_JUL,
			   0 AS COST_AUG,
			   0 AS COST_SEP,
			   0 AS COST_OCT,
			   0 AS COST_NOV,
			   0 AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_SPH SPH
				  ON SPH.CORE =
						CASE
						   WHEN SUBSTR (RKT.BA_CODE, 3, 1) = 2 THEN 'INTI'
						   ELSE 'PLASMA'
						END
					 AND SPH.LAND_TYPE = TM_HS.LAND_TYPE
					 AND SPH.TOPOGRAPHY = TM_HS.TOPOGRAPHY
			   LEFT JOIN TN_BIAYA BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2''TBM3')
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_SISIP'
			   AND RKT.COST_ELEMENT <> 'TRANSPORT'
			   AND RKT.ACTIVITY_CODE = '42700'
			   $where
		UNION ALL
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   0 AS QTY_JAN,
			   0 AS QTY_FEB,
			   0 AS QTY_MAR,
			   0 AS QTY_APR,
			   0 AS QTY_MAY,
			   0 AS QTY_JUN,
			   RKT.PLAN_JUL / SPH.SPH_STANDAR AS QTY_JUL,
			   RKT.PLAN_AUG / SPH.SPH_STANDAR AS QTY_AUG,
			   RKT.PLAN_SEP / SPH.SPH_STANDAR AS QTY_SEP,
			   RKT.PLAN_OCT / SPH.SPH_STANDAR AS QTY_OCT,
			   RKT.PLAN_NOV / SPH.SPH_STANDAR AS QTY_NOV,
			   RKT.PLAN_DEC / SPH.SPH_STANDAR AS QTY_DEC,
			   0 COST_JAN,
			   0 COST_FEB,
			   0 COST_MAR,
			   0 COST_APR,
			   0 COST_MAY,
			   0 COST_JUN,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_JUL,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_AUG,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_SEP,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_OCT,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_NOV,
			   CASE
				  WHEN RKT.TIPE_NORMA = 'UMUM'
				  THEN
					 (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI
				  ELSE
					 (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.PRICE_ROTASI_SITE
			   END
				  AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_SPH SPH
				  ON SPH.CORE =
						CASE
						   WHEN SUBSTR (RKT.BA_CODE, 3, 1) = 2 THEN 'INTI'
						   ELSE 'PLASMA'
						END
					 AND SPH.LAND_TYPE = TM_HS.LAND_TYPE
					 AND SPH.TOPOGRAPHY = TM_HS.TOPOGRAPHY
			   LEFT JOIN TN_BIAYA BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2''TBM3')
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_SISIP'
			   AND RKT.COST_ELEMENT <> 'TRANSPORT'
			   AND RKT.ACTIVITY_CODE = '42700'
			   $where							 
          UNION ALL
            -- MANUAL_NON_INFRA_OPSI SMS1
             SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     (RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
                                     (RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
                                     (RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
                                     (RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
                                     (RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
                                     (RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JAN * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JAN * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_JAN,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_FEB * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_FEB * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_FEB,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAR * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_MAR * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_MAR,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_APR * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_APR * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_APR,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAY * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_MAY * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_MAY,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUN * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JUN * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_JUN,
                                     0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET =
                                              BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE =
                                                 BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
                                     $where       
          UNION ALL                              
          -- MANUAL_NON_INFRA_OPSI SMS2
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
                                     (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
                                     (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
                                     (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
                                     (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
                                     (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUL * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_JUL * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_AUG,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_AUG * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_AUG * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_AUG,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_SEP * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_SEP * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_SEP,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_OCT * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_OCT * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_OCT,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_NOV * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_NOV * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_NOV,
                                     CASE
                                        WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_DEC * BIAYA.PRICE_ROTASI
                                        ELSE RKT.PLAN_DEC * BIAYA.PRICE_ROTASI_SITE
                                     END
                                     AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_BIAYA BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
                                     $where
          UNION ALL
          -- MANUAL_INFRA SMS1
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     (RKT.PLAN_JAN * BIAYA.QTY_HA) AS QTY_JAN,
                                     (RKT.PLAN_FEB * BIAYA.QTY_HA) AS QTY_FEB,
                                     (RKT.PLAN_MAR * BIAYA.QTY_HA) AS QTY_MAR,
                                     (RKT.PLAN_APR * BIAYA.QTY_HA) AS QTY_APR,
                                     (RKT.PLAN_MAY * BIAYA.QTY_HA) AS QTY_MAY,
                                     (RKT.PLAN_JUN * BIAYA.QTY_HA) AS QTY_JUN,
                                     0 QTY_JUL,
                                     0 QTY_AUG,
                                     0 QTY_SEP,
                                     0 QTY_OCT,
                                     0 QTY_NOV,
                                     0 QTY_DEC,
                                     (RKT.PLAN_JAN * BIAYA.RP_HA_INTERNAL) AS COST_JAN,
                                     (RKT.PLAN_FEB * BIAYA.RP_HA_INTERNAL) AS COST_FEB,
                                     (RKT.PLAN_MAR * BIAYA.RP_HA_INTERNAL) AS COST_MAR,
                                     (RKT.PLAN_APR * BIAYA.RP_HA_INTERNAL) AS COST_APR,
                                     (RKT.PLAN_MAY * BIAYA.RP_HA_INTERNAL) AS COST_MAY,
                                     (RKT.PLAN_JUN * BIAYA.RP_HA_INTERNAL) AS COST_JUN,
                                     0 COST_JUL,
                                     0 COST_AUG,
                                     0 COST_SEP,
                                     0 COST_OCT,
                                     0 COST_NOV,
                                     0 COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_INFRASTRUKTUR BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
                                     $where
          UNION ALL                                       
          -- MANUAL INFRA SMS2
          SELECT RKT.PERIOD_BUDGET,
                                     ORG.REGION_CODE,
                                     RKT.BA_CODE,
                                     RKT.AFD_CODE,
                                     RKT.BLOCK_CODE,
                                     RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                                     RKT.ACTIVITY_CODE,
                                     RKT.TIPE_TRANSAKSI,
                                     ACT.DESCRIPTION AS ACTIVITY_DESC,
                                     RKT.COST_ELEMENT,
                                     BIAYA.ACTIVITY_CLASS,
                                     BIAYA.LAND_TYPE,
                                     BIAYA.TOPOGRAPHY,
                                     BIAYA.SUB_COST_ELEMENT,
                                     BIAYA.QTY_HA,
                                     TM_MAT.MATERIAL_NAME,
                                     CASE
                                        WHEN RKT.COST_ELEMENT = 'LABOUR'
                                        THEN
                                           'HK'
                                        WHEN RKT.COST_ELEMENT = 'CONTRACT'
                                        THEN
                                           (SELECT ACT.UOM
                                              FROM TM_ACTIVITY ACT
                                             WHERE ACT.ACTIVITY_CODE =
                                                      BIAYA.SUB_COST_ELEMENT)
                                        ELSE
                                           (SELECT material.UOM
                                              FROM TM_MATERIAL material
                                             WHERE material.MATERIAL_CODE =
                                                      BIAYA.SUB_COST_ELEMENT
                                                   AND material.PERIOD_BUDGET =
                                                         BIAYA.PERIOD_BUDGET
                                                   AND material.BA_CODE =
                                                         BIAYA.BA_CODE)
                                     END
                                        AS UOM,
                                     0 AS QTY_JAN,
                                     0 AS QTY_FEB,
                                     0 AS QTY_MAR,
                                     0 AS QTY_APR,
                                     0 AS QTY_MAY,
                                     0 AS QTY_JUN,
                                     (RKT.PLAN_JUL * BIAYA.QTY_HA) AS QTY_JUL,
                                     (RKT.PLAN_AUG * BIAYA.QTY_HA) AS QTY_AUG,
                                     (RKT.PLAN_SEP * BIAYA.QTY_HA) AS QTY_SEP,
                                     (RKT.PLAN_OCT * BIAYA.QTY_HA) AS QTY_OCT,
                                     (RKT.PLAN_NOV * BIAYA.QTY_HA) AS QTY_NOV,
                                     (RKT.PLAN_DEC * BIAYA.QTY_HA) AS QTY_DEC,
                                     0 COST_JAN,
                                     0 COST_FEB,
                                     0 COST_MAR,
                                     0 COST_APR,
                                     0 COST_MAY,
                                     0 COST_JUN,
                                     (RKT.PLAN_JUL * BIAYA.RP_HA_INTERNAL) AS COST_JUL,
                                     (RKT.PLAN_AUG * BIAYA.RP_HA_INTERNAL) AS COST_AUG,
                                     (RKT.PLAN_SEP * BIAYA.RP_HA_INTERNAL) AS COST_SEP,
                                     (RKT.PLAN_OCT * BIAYA.RP_HA_INTERNAL) AS COST_OCT,
                                     (RKT.PLAN_NOV * BIAYA.RP_HA_INTERNAL) AS COST_NOV,
                                     (RKT.PLAN_DEC * BIAYA.RP_HA_INTERNAL) AS COST_DEC
                                FROM TR_RKT_COST_ELEMENT RKT
                                     LEFT JOIN TR_RKT RKT_INDUK
                                        ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                                     LEFT JOIN TM_ACTIVITY ACT
                                        ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                                     LEFT JOIN TM_ORGANIZATION ORG
                                        ON ORG.BA_CODE = RKT.BA_CODE
                                     LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                                        ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                           AND TM_HS.BA_CODE = RKT.BA_CODE
                                           AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                           AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                                     LEFT JOIN TN_INFRASTRUKTUR BIAYA
                                        ON RKT.BA_CODE = BIAYA.BA_CODE
                                           AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                                           AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                                           AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                                           AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                                           AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
										   AND BIAYA.DELETE_USER IS NULL
                                     LEFT JOIN TM_MATERIAL TM_MAT
                                        ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                                           AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                                           AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                               WHERE     RKT.DELETE_USER IS NULL
                                     AND RKT_INDUK.FLAG_TEMP IS NULL
                                     AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                                     AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
                                     $where
        UNION ALL
		-- HITUNG INFRA UNTUK CONTRACT sms 1
			SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT) AS UOM,
			   RKT.PLAN_JAN AS QTY_JAN,
			   RKT.PLAN_FEB AS QTY_FEB,
			   RKT.PLAN_MAR AS QTY_MAR,
			   RKT.PLAN_APR AS QTY_APR,
			   RKT.PLAN_MAY AS QTY_MAY,
			   RKT.PLAN_JUN AS QTY_JUN,
			   0 QTY_JUL,
			   0 QTY_AUG,
			   0 QTY_SEP,
			   0 QTY_OCT,
			   0 QTY_NOV,
			   0 QTY_DEC,
			   DIS_JAN AS COST_JAN,
			   DIS_FEB AS COST_FEB,
			   DIS_MAR AS COST_MAR,
			   DIS_APR AS COST_APR,
			   DIS_MAY AS COST_MAY,
			   DIS_JUN AS COST_JUN,
			   0 COST_JUL,
			   0 COST_AUG,
			   0 COST_SEP,
			   0 COST_OCT,
			   0 COST_NOV,
			   0 COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_INFRASTRUKTUR BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
			   AND RKT.SUMBER_BIAYA = 'EXTERNAL'
			   AND RKT.COST_ELEMENT = 'CONTRACT'
			   $where
		UNION ALL
		--untuk sms 2
		SELECT RKT.PERIOD_BUDGET,
			   ORG.REGION_CODE,
			   RKT.BA_CODE,
			   RKT.AFD_CODE,
			   RKT.BLOCK_CODE,
			   RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
			   RKT.ACTIVITY_CODE,
			   RKT.TIPE_TRANSAKSI,
			   ACT.DESCRIPTION AS ACTIVITY_DESC,
			   RKT.COST_ELEMENT,
			   BIAYA.ACTIVITY_CLASS,
			   BIAYA.LAND_TYPE,
			   BIAYA.TOPOGRAPHY,
			   BIAYA.SUB_COST_ELEMENT,
			   BIAYA.QTY_HA,
			   TM_MAT.MATERIAL_NAME,
			   CASE
				  WHEN RKT.COST_ELEMENT = 'LABOUR'
				  THEN
					 'HK'
				  WHEN RKT.COST_ELEMENT = 'CONTRACT'
				  THEN
					 (SELECT ACT.UOM
						FROM TM_ACTIVITY ACT
					   WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT)
				  ELSE
					 (SELECT material.UOM
						FROM TM_MATERIAL material
					   WHERE     material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
							 AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
							 AND material.BA_CODE = BIAYA.BA_CODE)
			   END
				  AS UOM,
			   0 QTY_JAN,
			   0 QTY_FEB,
			   0 QTY_MAR,
			   0 QTY_APR,
			   0 QTY_MAY,
			   0 QTY_JUN,
			   RKT.PLAN_JUL AS QTY_JUL,
			   RKT.PLAN_AUG AS QTY_AUG,
			   RKT.PLAN_SEP AS QTY_SEP,
			   RKT.PLAN_OCT AS QTY_OCT,
			   RKT.PLAN_NOV AS QTY_NOV,
			   RKT.PLAN_DEC AS QTY_DEC,
			   0 COST_JAN,
			   0 COST_FEB,
			   0 COST_MAR,
			   0 COST_APR,
			   0 COST_MAY,
			   0 COST_JUN,
			   DIS_JUL AS COST_JUL,
			   DIS_AUG AS COST_AUG,
			   DIS_SEP AS COST_SEP,
			   DIS_OCT AS COST_OCT,
			   DIS_NOV AS COST_NOV,
			   DIS_DEC AS COST_DEC
		  FROM TR_RKT_COST_ELEMENT RKT
			   LEFT JOIN TR_RKT RKT_INDUK
				  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
			   LEFT JOIN TM_ACTIVITY ACT
				  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
			   LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = RKT.BA_CODE
			   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
				  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
					 AND TM_HS.BA_CODE = RKT.BA_CODE
					 AND TM_HS.AFD_CODE = RKT.AFD_CODE
					 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
			   LEFT JOIN TN_INFRASTRUKTUR BIAYA
				  ON     RKT.BA_CODE = BIAYA.BA_CODE
					 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
					 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
					 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
					 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
					 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
					 AND BIAYA.DELETE_USER IS NULL
			   LEFT JOIN TM_MATERIAL TM_MAT
				  ON     TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
					 AND TM_MAT.BA_CODE = BIAYA.BA_CODE
					 AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
		 WHERE     RKT.DELETE_USER IS NULL
			   AND RKT_INDUK.FLAG_TEMP IS NULL
			   AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
			   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
			   AND RKT.COST_ELEMENT = 'CONTRACT'
			   $where
		UNION ALL
        --COST ELEMENT TRANSPORT UNTUK RAWAT
            --SMS1
                    SELECT RKT.PERIOD_BUDGET,
                           ORG.REGION_CODE,
                           RKT.BA_CODE,
                           RKT.AFD_CODE,
                           RKT.BLOCK_CODE,
                           RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
                           RKT.ACTIVITY_CODE,
                           RKT.TIPE_TRANSAKSI,
                           ACT.DESCRIPTION AS ACTIVITY_DESC,
                           RKT.COST_ELEMENT,
                           '' AS ACTIVITY_CLASS,
                           '' AS LAND_TYPE,
                           '' AS TOPOGRAPHY,
                           VRA.VRA_CODE,
                           RKT_DIS.HM_KM,
                           VRA.VRA_SUB_CAT_DESCRIPTION,
                           VRA.UOM,
                           (RKT.PLAN_JAN
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_JAN,
                           (RKT.PLAN_FEB
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_FEB,
                           (RKT.PLAN_MAR
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_MAR,
                           (RKT.PLAN_APR
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_APR,
                           (RKT.PLAN_MAY
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_MAY,
                           (RKT.PLAN_JUN
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_JUN,
                           0 QTY_JUL,
                           0 QTY_AUG,
                           0 QTY_SEP,
                           0 QTY_OCT,
                           0 QTY_NOV,
                           0 QTY_DEC,
                           ( (RKT.PLAN_JAN
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_JAN,
                           ( (RKT.PLAN_FEB
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_FEB,
                           ( (RKT.PLAN_MAR
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_MAR,
                           ( (RKT.PLAN_APR
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_APR,
                           ( (RKT.PLAN_MAY
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_MAY,
                           ( (RKT.PLAN_JUN
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_JUN,
                           0 COST_JUL,
                           0 COST_AUG,
                           0 COST_SEP,
                           0 COST_OCT,
                           0 COST_NOV,
                           0 COST_DEC
                      FROM TR_RKT_COST_ELEMENT RKT
                           LEFT JOIN TR_RKT RKT_INDUK
                              ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                           LEFT JOIN TR_RKT_VRA_DISTRIBUSI RKT_DIS
                              ON     RKT_DIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND RKT_DIS.BA_CODE = RKT.BA_CODE
                                 AND RKT_DIS.LOCATION_CODE = RKT.AFD_CODE
                                 AND RKT_DIS.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                           LEFT JOIN TR_RKT_VRA_DISTRIBUSI_SUM RKT_DIS_SUM
                              ON     RKT_DIS_SUM.PERIOD_BUDGET = RKT_DIS.PERIOD_BUDGET
                                 AND RKT_DIS_SUM.BA_CODE = RKT_DIS.BA_CODE
                                 AND RKT_DIS_SUM.ACTIVITY_CODE = RKT_DIS.ACTIVITY_CODE
                                 AND RKT_DIS_SUM.VRA_CODE = RKT_DIS.VRA_CODE
                           LEFT JOIN TM_ACTIVITY ACT
                              ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                           LEFT JOIN TM_ORGANIZATION ORG
                              ON ORG.BA_CODE = RKT.BA_CODE
                           LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                              ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND TM_HS.BA_CODE = RKT.BA_CODE
                                 AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                           LEFT JOIN TM_VRA VRA
                              ON VRA.VRA_CODE = RKT_DIS_SUM.VRA_CODE
                     WHERE     RKT.DELETE_USER IS NULL
                           AND RKT_INDUK.FLAG_TEMP IS NULL
                           AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                           AND RKT.COST_ELEMENT = 'TRANSPORT'
						   AND RKT.TIPE_TRANSAKSI <> 'MANUAL_INFRA'
                           $where
                    UNION ALL
                    --SMS2       
                    SELECT RKT.PERIOD_BUDGET,
                           ORG.REGION_CODE,
                           RKT.BA_CODE,
                           RKT.AFD_CODE,
                           RKT.BLOCK_CODE,
                           RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
                           RKT.ACTIVITY_CODE,
                           RKT.TIPE_TRANSAKSI,
                           ACT.DESCRIPTION AS ACTIVITY_DESC,
                           RKT.COST_ELEMENT,
                           '' AS ACTIVITY_CLASS,
                           '' AS LAND_TYPE,
                           '' AS TOPOGRAPHY,
                           VRA.VRA_CODE,
                           RKT_DIS.HM_KM,
                           VRA.VRA_SUB_CAT_DESCRIPTION,
                           VRA.UOM,
                           0 QTY_JAN,
                           0 QTY_FEB,
                           0 QTY_MAR,
                           0 QTY_APR,
                           0 QTY_MAY,
                           0 QTY_JUN,
                           (RKT.PLAN_JUL
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_JUL,
                           (RKT.PLAN_AUG
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_AUG,
                           (RKT.PLAN_SEP
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_SEP,
                           (RKT.PLAN_OCT
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_OCT,
                           (RKT.PLAN_NOV
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_NOV,
                           (RKT.PLAN_DEC
                            / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                 FROM TR_RKT
                                WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                      AND BA_CODE = RKT.BA_CODE
                                      AND AFD_CODE = RKT.AFD_CODE
                                      AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                            * RKT_DIS.HM_KM)
                              AS QTY_DEC,
                           0 COST_JAN,
                           0 COST_FEB,
                           0 COST_MAR,
                           0 COST_APR,
                           0 COST_MAY,
                           0 COST_JUN,
                           ( (RKT.PLAN_JUL
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_JUL,
                           ( (RKT.PLAN_AUG
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_AUG,
                           ( (RKT.PLAN_SEP
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_SEP,
                           ( (RKT.PLAN_OCT
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_OCT,
                           ( (RKT.PLAN_NOV
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_NOV,
                           ( (RKT.PLAN_DEC
                              / NULLIF((SELECT SUM (PLAN_SETAHUN) AS TTL
                                   FROM TR_RKT
                                  WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                        AND BA_CODE = RKT.BA_CODE
                                        AND AFD_CODE = RKT.AFD_CODE
                                        AND ACTIVITY_CODE = RKT.ACTIVITY_CODE),0)
                              * RKT_DIS.HM_KM))
                           * (RKT_DIS_SUM.TOTAL_PRICE_HM_KM / NULLIF(RKT_DIS_SUM.TOTAL_HM_KM,0))
                              AS COST_DEC
                      FROM TR_RKT_COST_ELEMENT RKT
                           LEFT JOIN TR_RKT RKT_INDUK
                              ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
                           LEFT JOIN TR_RKT_VRA_DISTRIBUSI RKT_DIS
                              ON     RKT_DIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND RKT_DIS.BA_CODE = RKT.BA_CODE
                                 AND RKT_DIS.LOCATION_CODE = RKT.AFD_CODE
                                 AND RKT_DIS.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                           LEFT JOIN TR_RKT_VRA_DISTRIBUSI_SUM RKT_DIS_SUM
                              ON     RKT_DIS_SUM.PERIOD_BUDGET = RKT_DIS.PERIOD_BUDGET
                                 AND RKT_DIS_SUM.BA_CODE = RKT_DIS.BA_CODE
                                 AND RKT_DIS_SUM.ACTIVITY_CODE = RKT_DIS.ACTIVITY_CODE
                                 AND RKT_DIS_SUM.VRA_CODE = RKT_DIS.VRA_CODE
                           LEFT JOIN TM_ACTIVITY ACT
                              ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
                           LEFT JOIN TM_ORGANIZATION ORG
                              ON ORG.BA_CODE = RKT.BA_CODE
                           LEFT JOIN TM_HECTARE_STATEMENT TM_HS
                              ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND TM_HS.BA_CODE = RKT.BA_CODE
                                 AND TM_HS.AFD_CODE = RKT.AFD_CODE
                                 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
                           LEFT JOIN TM_VRA VRA
                              ON VRA.VRA_CODE = RKT_DIS_SUM.VRA_CODE
                     WHERE     RKT.DELETE_USER IS NULL
                           AND RKT_INDUK.FLAG_TEMP IS NULL
                           AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                           AND RKT.COST_ELEMENT = 'TRANSPORT'
						   AND RKT.TIPE_TRANSAKSI <> 'MANUAL_INFRA'
                           $where
				UNION ALL
					SELECT PERIOD_BUDGET,
					   REGION_CODE,
					   BA_CODE,
					   AFD_CODE,
					   BLOCK_CODE,
					   MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
					   ACTIVITY_CODE,
					   TIPE_TRANSAKSI,
					   DESCRIPTION AS ACTIVITY_DESC,
					   COST_ELEMENT,
					   ACTIVITY_CLASS,
					   LAND_TYPE,
					   TOPOGRAPHY,
					   SUB_COST_ELEMENT,
					   QTY_HA,
					   VRA_SUB_CAT_DESCRIPTION,
					   UOM,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JAN / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JAN,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_FEB / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_FEB,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAR / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_MAR,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_APR / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_APR,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAY / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_MAY,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUN / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JUN,
					   0 QTY_JUL,
					   0 QTY_AUG,
					   0 QTY_SEP,
					   0 QTY_OCT,
					   0 QTY_NOV,
					   0 QTY_DEC,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JAN ELSE 0 END
						  AS COST_JAN,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_FEB ELSE 0 END
						  AS COST_FEB,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAR ELSE 0 END
						  AS COST_MAR,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_APR ELSE 0 END
						  AS COST_APR,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_MAY ELSE 0 END
						  AS COST_MAY,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUN ELSE 0 END
						  AS COST_JUN,
					   0 COST_JUL,
					   0 COST_AUG,
					   0 COST_SEP,
					   0 COST_OCT,
					   0 COST_NOV,
					   0 COST_DEC
				  FROM (SELECT RKT_INDUK.*,
							   RKT.COST_ELEMENT,
							   BIAYA.LAND_TYPE,
							   BIAYA.TOPOGRAPHY,
							   BIAYA.SUB_COST_ELEMENT,
							   BIAYA.QTY_HA,
							   ORG.REGION_CODE,
							   ACT.DESCRIPTION,
							   VRA.VRA_SUB_CAT_DESCRIPTION,
							   VRA.UOM,
							   ( (SELECT RKTVRAS.VALUE
									FROM TR_RKT_VRA_SUM RKTVRAS
								   WHERE     RKTVRAS.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										 AND RKTVRAS.BA_CODE = BIAYA.BA_CODE
										 AND RKTVRAS.VRA_CODE = BIAYA.SUB_COST_ELEMENT)
								UNION
								(SELECT NVRAPINJAM.RP_QTY AS VALUE
								   FROM TN_VRA_PINJAM NVRAPINJAM
								  WHERE     NVRAPINJAM.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										AND NVRAPINJAM.REGION_CODE = BIAYA.REGION_CODE
										AND NVRAPINJAM.VRA_CODE = BIAYA.SUB_COST_ELEMENT))
								  AS HARGA_INTERNAL,
							   (1 / BIAYA.QTY_HA) * RP_HA_EXTERNAL AS HARGA_EXTERNAL
						  FROM TR_RKT_COST_ELEMENT RKT
							   LEFT JOIN TR_RKT RKT_INDUK
								  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
							   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
								  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
									 AND TM_HS.BA_CODE = RKT.BA_CODE
									 AND TM_HS.AFD_CODE = RKT.AFD_CODE
									 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
							   INNER JOIN TN_INFRASTRUKTUR BIAYA
								  ON     RKT.BA_CODE = BIAYA.BA_CODE
									 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
									 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
									 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
									 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
									 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
									 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
									 AND BIAYA.DELETE_USER IS NULL
							   LEFT JOIN TM_ACTIVITY ACT
								  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
							   LEFT JOIN TM_VRA VRA
								  ON VRA.VRA_CODE = BIAYA.SUB_COST_ELEMENT  
							   LEFT JOIN TM_ORGANIZATION ORG
								  ON ORG.BA_CODE = RKT.BA_CODE
						 WHERE     RKT.DELETE_USER IS NULL
							   AND RKT_INDUK.FLAG_TEMP IS NULL
							   AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
							   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
							   AND RKT.COST_ELEMENT = 'TRANSPORT'
							   $where)
				UNION ALL
-- biaya lain lain TBM0 supervisi perawatan
SELECT RKT.PERIOD_BUDGET,
       ORG.REGION_CODE,
       RKT.BA_CODE,
       HS.AFD_CODE,
       HS.BLOCK_CODE,
       'TBM0' TIPE_TRANSAKSI,
		RKT.COA_CODE AS ACTIVITY_CODE,
		'TBM0' TIPE_TRANSAKSI2,
		RKT.COA_DESC AS ACTIVITY_DESC,
		'' COST_ELEMENT,
		'' AS ACTIVITY_CLASS,
			   '' AS LAND_TYPE,
			   '' AS TOPOGRAPHY,
		'' AS SUB_COST_ELEMENT,
		0 AS SUB_COST_ELEMENT_DESC,
		'' AS KETERANGAN,
       '' UOM,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_DEC,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
          AS COST_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
          AS COST_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
          AS COST_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
          AS COST_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
          AS COST_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
          AS COST_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
          AS COST_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
          AS COST_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
          AS COST_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
          AS COST_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
          AS COST_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC)
          AS COST_DEC
  FROM V_TOTAL_RELATION_COST RKT
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE,
                           SUM (HS.SMS1_TBM0) SMS1_TBM,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.SMS2_TBM0) SMS2_TBM,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE) HS
          ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS2
          ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS2.BA_CODE = RKT.BA_CODE
       LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT TRC.PERIOD_BUDGET,
                           TRC.BA_CODE,
                           SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
                      FROM TR_RKT_CHECKROLL TRC
                     WHERE TRC.JOB_CODE IN ('FX130', 'FX110')
                  GROUP BY TRC.PERIOD_BUDGET, TRC.BA_CODE) MPP_ALL
          ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND MPP_ALL.BA_CODE = RKT.BA_CODE
 WHERE     RKT.COA_CODE = '43800'
       $where
UNION ALL
-- biaya lain lain TBM1 supervisi perawatan
SELECT RKT.PERIOD_BUDGET,
       ORG.REGION_CODE,
       RKT.BA_CODE,
       HS.AFD_CODE,
       HS.BLOCK_CODE,
       'TBM1' TIPE_TRANSAKSI,
                    RKT.COA_CODE AS ACTIVITY_CODE,
                    'TBM1' TIPE_TRANSAKSI2,
                    RKT.COA_DESC AS ACTIVITY_DESC,
                    '' COST_ELEMENT,
                    '' AS ACTIVITY_CLASS,
                           '' AS LAND_TYPE,
                           '' AS TOPOGRAPHY,
                    '' AS SUB_COST_ELEMENT,
                    0 AS SUB_COST_ELEMENT_DESC,
                    '' AS KETERANGAN,
       '' UOM,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_DEC,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
          AS COST_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
          AS COST_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
          AS COST_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
          AS COST_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
          AS COST_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
          AS COST_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
          AS COST_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
          AS COST_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
          AS COST_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
          AS COST_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
          AS COST_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC)
          AS COST_DEC
  FROM V_TOTAL_RELATION_COST RKT
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE,
                           SUM (HS.SMS1_TBM1) SMS1_TBM,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.SMS2_TBM1) SMS2_TBM,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE) HS
          ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS2
          ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS2.BA_CODE = RKT.BA_CODE
       LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT TRC.PERIOD_BUDGET,
                           TRC.BA_CODE,
                           SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
                      FROM TR_RKT_CHECKROLL TRC
                     WHERE TRC.JOB_CODE IN ('FX130', 'FX110')
                  GROUP BY TRC.PERIOD_BUDGET, TRC.BA_CODE) MPP_ALL
          ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND MPP_ALL.BA_CODE = RKT.BA_CODE
 WHERE     RKT.COA_CODE = '43800'
       $where
UNION ALL
-- biaya lain lain TBM2 supervisi perawatan
SELECT RKT.PERIOD_BUDGET,
       ORG.REGION_CODE,
       RKT.BA_CODE,
       HS.AFD_CODE,
       HS.BLOCK_CODE,
       'TBM2' TIPE_TRANSAKSI,
		RKT.COA_CODE AS ACTIVITY_CODE,
		'TBM2' TIPE_TRANSAKSI2,
		RKT.COA_DESC AS ACTIVITY_DESC,
		'' COST_ELEMENT,
		'' AS ACTIVITY_CLASS,
			   '' AS LAND_TYPE,
			   '' AS TOPOGRAPHY,
		'' AS SUB_COST_ELEMENT,
		0 AS SUB_COST_ELEMENT_DESC,
		'' AS KETERANGAN,
       '' UOM,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_DEC,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
          AS COST_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
          AS COST_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
          AS COST_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
          AS COST_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
          AS COST_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
          AS COST_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
          AS COST_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
          AS COST_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
          AS COST_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
          AS COST_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
          AS COST_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC)
          AS COST_DEC
  FROM V_TOTAL_RELATION_COST RKT
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE,
                           SUM (HS.SMS1_TBM2) SMS1_TBM,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.SMS2_TBM2) SMS2_TBM,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE) HS
          ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS2
          ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS2.BA_CODE = RKT.BA_CODE
       LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT TRC.PERIOD_BUDGET,
                           TRC.BA_CODE,
                           SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
                      FROM TR_RKT_CHECKROLL TRC
                     WHERE TRC.JOB_CODE IN ('FX130', 'FX110')
                  GROUP BY TRC.PERIOD_BUDGET, TRC.BA_CODE) MPP_ALL
          ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND MPP_ALL.BA_CODE = RKT.BA_CODE
 WHERE     RKT.COA_CODE = '43800'
       $where
UNION ALL
-- biaya lain lain TBM3 supervisi perawatan
SELECT RKT.PERIOD_BUDGET,
       ORG.REGION_CODE,
       RKT.BA_CODE,
       HS.AFD_CODE,
       HS.BLOCK_CODE,
       'TBM3' TIPE_TRANSAKSI,
		RKT.COA_CODE AS ACTIVITY_CODE,
		'TBM3' TIPE_TRANSAKSI2,
		RKT.COA_DESC AS ACTIVITY_DESC,
		'' COST_ELEMENT,
		'' AS ACTIVITY_CLASS,
			   '' AS LAND_TYPE,
			   '' AS TOPOGRAPHY,
		'' AS SUB_COST_ELEMENT,
		0 AS SUB_COST_ELEMENT_DESC,
		'' AS KETERANGAN,
       '' UOM,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * MPP_ALL.MPP) QTY_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * MPP_ALL.MPP) QTY_DEC,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)
          AS COST_JAN,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)
          AS COST_FEB,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)
          AS COST_MAR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)
          AS COST_APR,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)
          AS COST_MAY,
       (HS.SMS1_TBM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)
          AS COST_JUN,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)
          AS COST_JUL,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)
          AS COST_AUG,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)
          AS COST_SEP,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)
          AS COST_OCT,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)
          AS COST_NOV,
       (HS.SMS2_TBM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC)
          AS COST_DEC
  FROM V_TOTAL_RELATION_COST RKT
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE,
                           SUM (HS.SMS1_TBM3) SMS1_TBM,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.SMS2_TBM3) SMS2_TBM,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           HS.AFD_CODE,
                           HS.BLOCK_CODE) HS
          ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT HS.PERIOD_BUDGET,
                           HS.BA_CODE,
                           SUM (HS.TOTAL_HA_SMS1) TOTAL_HA_SMS1,
                           SUM (HS.TOTAL_HA_SMS2) TOTAL_HA_SMS2
                      FROM V_REPORT_SEBARAN_HS_BLOCK HS
                  GROUP BY HS.PERIOD_BUDGET, HS.BA_CODE) HS2
          ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND HS2.BA_CODE = RKT.BA_CODE
       LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
       LEFT JOIN (  SELECT TRC.PERIOD_BUDGET,
                           TRC.BA_CODE,
                           SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
                      FROM TR_RKT_CHECKROLL TRC
                     WHERE TRC.JOB_CODE IN ('FX130', 'FX110')
                  GROUP BY TRC.PERIOD_BUDGET, TRC.BA_CODE) MPP_ALL
          ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
             AND MPP_ALL.BA_CODE = RKT.BA_CODE
 WHERE     RKT.COA_CODE = '43800'
		$where
       UNION ALL
				SELECT PERIOD_BUDGET,
					   REGION_CODE,
					   BA_CODE,
					   AFD_CODE,
					   BLOCK_CODE,
					   MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
					   ACTIVITY_CODE,
					   TIPE_TRANSAKSI,
					   DESCRIPTION AS ACTIVITY_DESC,
					   COST_ELEMENT,
					   ACTIVITY_CLASS,
					   LAND_TYPE,
					   TOPOGRAPHY,
					   SUB_COST_ELEMENT,
					   QTY_HA,
					   VRA_SUB_CAT_DESCRIPTION,
					   UOM,
					   0 QTY_JAN,
					   0 QTY_FEB,
					   0 QTY_MAR,
					   0 QTY_APR,
					   0 QTY_MAY,
					   0 QTY_JUN,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUL / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_JUL,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_AUG / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_AUG,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_SEP / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_SEP,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_OCT / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_OCT,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_NOV / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_NOV,
					   CASE
						  WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_DEC / HARGA_INTERNAL
						  ELSE 0
					   END
						  AS QTY_DEC,
					   0 COST_JAN,
					   0 COST_FEB,
					   0 COST_MAR,
					   0 COST_APR,
					   0 COST_MAY,
					   0 COST_JUN,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_JUL ELSE 0 END
						  AS COST_JUL,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_AUG ELSE 0 END
						  AS COST_AUG,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_SEP ELSE 0 END
						  AS COST_SEP,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_OCT ELSE 0 END
						  AS COST_OCT,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_NOV ELSE 0 END
						  AS COST_NOV,
					   CASE WHEN SUMBER_BIAYA = 'INTERNAL' THEN COST_DEC ELSE 0 END
						  AS COST_DEC
				  FROM (SELECT RKT_INDUK.*,
							   RKT.COST_ELEMENT,
							   BIAYA.LAND_TYPE,
							   BIAYA.TOPOGRAPHY,
							   BIAYA.SUB_COST_ELEMENT,
							   BIAYA.QTY_HA,
							   ORG.REGION_CODE,
							   ACT.DESCRIPTION,
							   VRA.VRA_SUB_CAT_DESCRIPTION,
							   VRA.UOM,
							   ( (SELECT RKTVRAS.VALUE
									FROM TR_RKT_VRA_SUM RKTVRAS
								   WHERE     RKTVRAS.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										 AND RKTVRAS.BA_CODE = BIAYA.BA_CODE
										 AND RKTVRAS.VRA_CODE = BIAYA.SUB_COST_ELEMENT)
								UNION
								(SELECT NVRAPINJAM.RP_QTY AS VALUE
								   FROM TN_VRA_PINJAM NVRAPINJAM
								  WHERE     NVRAPINJAM.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
										AND NVRAPINJAM.REGION_CODE = BIAYA.REGION_CODE
										AND NVRAPINJAM.VRA_CODE = BIAYA.SUB_COST_ELEMENT))
								  AS HARGA_INTERNAL,
							   (1 / BIAYA.QTY_HA) * RP_HA_EXTERNAL AS HARGA_EXTERNAL
						  FROM TR_RKT_COST_ELEMENT RKT
							   LEFT JOIN TR_RKT RKT_INDUK
								  ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
							   LEFT JOIN TM_HECTARE_STATEMENT TM_HS
								  ON     TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
									 AND TM_HS.BA_CODE = RKT.BA_CODE
									 AND TM_HS.AFD_CODE = RKT.AFD_CODE
									 AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
							   INNER JOIN TN_INFRASTRUKTUR BIAYA
								  ON     RKT.BA_CODE = BIAYA.BA_CODE
									 AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
									 AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
									 AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
									 AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
									 AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
									 AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
									 AND BIAYA.DELETE_USER IS NULL
							   LEFT JOIN TM_ACTIVITY ACT
								  ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
							   LEFT JOIN TM_VRA VRA
								  ON VRA.VRA_CODE = BIAYA.SUB_COST_ELEMENT	  
							   LEFT JOIN TM_ORGANIZATION ORG
								  ON ORG.BA_CODE = RKT.BA_CODE
						 WHERE     RKT.DELETE_USER IS NULL
							   AND RKT_INDUK.FLAG_TEMP IS NULL
							   AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
							   AND RKT.TIPE_TRANSAKSI = 'MANUAL_INFRA'
							   AND RKT.COST_ELEMENT = 'TRANSPORT'
							   $where)		   
          ) REPORT
		GROUP BY PERIOD_BUDGET,
				 REGION_CODE,
				 BA_CODE,
				 AFD_CODE,
				 BLOCK_CODE,
				 ACTIVITY_GROUP,
				 COST_ELEMENT,
				 ACTIVITY_CODE,
				 TIPE_TRANSAKSI,
				 ACTIVITY_DESC,
				 SUB_COST_ELEMENT,
				 MATERIAL_NAME,
				 UOM";
		
		$this->_db->query($query);
		$this->_db->commit();
		
		//QUERY INSERT UNTUK PUPUK (LABOUR & MATERIAL)
		$xquery = "INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (PERIOD_BUDGET,
                                        REGION_CODE,
                                        BA_CODE,
                                        AFD_CODE,
                                        BLOCK_CODE,
                                        TIPE_TRANSAKSI,
                                        COST_ELEMENT,
                                        ACTIVITY_CODE,
                                        ACTIVITY_DESC,
                                        SUB_COST_ELEMENT,
                                        SUB_COST_ELEMENT_DESC,
                                        KETERANGAN,
                                        UOM,
                                        QTY_JAN,
                                        QTY_FEB,
                                        QTY_MAR,
                                        QTY_APR,
                                        QTY_MAY,
                                        QTY_JUN,
                                        QTY_JUL,
                                        QTY_AUG,
                                        QTY_SEP,
                                        QTY_OCT,
                                        QTY_NOV,
                                        QTY_DEC,
                                        COST_JAN,
                                        COST_FEB,
                                        COST_MAR,
                                        COST_APR,
                                        COST_MAY,
                                        COST_JUN,
                                        COST_JUL,
                                        COST_AUG,
                                        COST_SEP,
                                        COST_OCT,
                                        COST_NOV,
                                        COST_DEC,
                                        QTY_SETAHUN,
                                        COST_SETAHUN,
                                        INSERT_USER,
                                        INSERT_TIME)
     SELECT PERIOD_BUDGET,
            REGION_CODE,
            BA_CODE,
            AFD_CODE,
            BLOCK_CODE,
            TIPE_TRANSAKSI,
            COST_ELEMENT,
            ACTIVITY_CODE,
            ACTIVITY_DESC,
            SUB_COST_ELEMENT,
            MATERIAL_NAME,
            '' KETERANGAN,
            UOM,
            SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
            SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
            SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
            SUM (NVL (QTY_APR, 0)) AS QTY_APR,
            SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
            SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
            SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
            SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
            SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
            SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
            SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
            SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
            SUM (NVL (COST_JAN, 0)) AS COST_JAN,
            SUM (NVL (COST_FEB, 0)) AS COST_FEB,
            SUM (NVL (COST_MAR, 0)) AS COST_MAR,
            SUM (NVL (COST_APR, 0)) AS COST_APR,
            SUM (NVL (COST_MAY, 0)) AS COST_MAY,
            SUM (NVL (COST_JUN, 0)) AS COST_JUN,
            SUM (NVL (COST_JUL, 0)) AS COST_JUL,
            SUM (NVL (COST_AUG, 0)) AS COST_AUG,
            SUM (NVL (COST_SEP, 0)) AS COST_SEP,
            SUM (NVL (COST_OCT, 0)) AS COST_OCT,
            SUM (NVL (COST_NOV, 0)) AS COST_NOV,
            SUM (NVL (COST_DEC, 0)) AS COST_DEC,
            (  SUM (NVL (QTY_JAN, 0))
             + SUM (NVL (QTY_FEB, 0))
             + SUM (NVL (QTY_MAR, 0))
             + SUM (NVL (QTY_APR, 0))
             + SUM (NVL (QTY_MAY, 0))
             + SUM (NVL (QTY_JUN, 0))
             + SUM (NVL (QTY_JUL, 0))
             + SUM (NVL (QTY_AUG, 0))
             + SUM (NVL (QTY_SEP, 0))
             + SUM (NVL (QTY_OCT, 0))
             + SUM (NVL (QTY_NOV, 0))
             + SUM (NVL (QTY_DEC, 0)))
               AS QTY_SETAHUN,
            (  SUM (NVL (COST_JAN, 0))
             + SUM (NVL (COST_FEB, 0))
             + SUM (NVL (COST_MAR, 0))
             + SUM (NVL (COST_APR, 0))
             + SUM (NVL (COST_MAY, 0))
             + SUM (NVL (COST_JUN, 0))
             + SUM (NVL (COST_JUL, 0))
             + SUM (NVL (COST_AUG, 0))
             + SUM (NVL (COST_SEP, 0))
             + SUM (NVL (COST_OCT, 0))
             + SUM (NVL (COST_NOV, 0))
             + SUM (NVL (COST_DEC, 0)))
               AS COST_SETAHUN,
            '".$this->_userName."' AS INSERT_USER,
            SYSDATE AS INSERT_TIME
       FROM (-- INI UNTUK PERHITUNGAN PEMUPUKAN (MAJEMUK TUNGGAL)
             SELECT PERIOD_BUDGET,
                    REGION_CODE,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    ACTIVITY_GROUP AS TIPE_TRANSAKSI,
                    COST_ELEMENT,
                    ACTIVITY_CODE,
                    ACTIVITY_DESC,
                    SUB_COST_ELEMENT,
                    MATERIAL_NAME,
                    '' RANK_Z,
                    UOM,
                    JAN_QTY AS QTY_JAN,
                    FEB_QTY AS QTY_FEB,
                    MAR_QTY AS QTY_MAR,
                    APR_QTY AS QTY_APR,
                    MAY_QTY AS QTY_MAY,
                    JUN_QTY AS QTY_JUN,
                    JUL_QTY AS QTY_JUL,
                    AUG_QTY AS QTY_AUG,
                    SEP_QTY AS QTY_SEP,
                    OCT_QTY AS QTY_OCT,
                    NOV_QTY AS QTY_NOV,
                    DEC_QTY AS QTY_DEC,
                    JAN_COST AS COST_JAN,
                    FEB_COST AS COST_FEB,
                    MAR_COST AS COST_MAR,
                    APR_COST AS COST_APR,
                    MAY_COST AS COST_MAY,
                    JUN_COST AS COST_JUN,
                    JUL_COST AS COST_JUL,
                    AUG_COST AS COST_AUG,
                    SEP_COST AS COST_SEP,
                    OCT_COST AS COST_OCT,
                    NOV_COST AS COST_NOV,
                    DEC_COST AS COST_DEC
               FROM (SELECT *        -- UNTUK PERHITUNGAN SMS 1 PUPUK  MAJEMUK
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS1
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS1,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
                                                        'PUPUK MAJEMUK'
                                                           DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS1 IN
                                                                         ('TBM0',
                                                                          'TBM1',
                                                                          'TBM2',
                                                                          'TBM3')
                                                                $where
															)
                                                        HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020400' -- UNTUK PUPUK  MAJEMUK
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('1' JAN,
                                               '2' FEB,
                                               '3' MAR,
                                               '4' APR,
                                               '5' MAY,
                                               '6' JUN,
                                               '91' JUL,
                                               '92' AUG,
                                               '93' SEP,
                                               '94' OCT,
                                               '95' NOV,
                                               '96' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 2 PUPUK  MAJEMUK
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS2
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS2,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
                                                        'PUPUK MAJEMUK'
                                                           DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS2 IN
                                                                         ('TBM0',
                                                                          'TBM1',
                                                                          'TBM2',
                                                                          'TBM3')
                                                                $where
															)
                                                        HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020400' -- UNTUK PUPUK  MAJEMUK
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('91' JAN,
                                               '92' FEB,
                                               '93' MAR,
                                               '94' APR,
                                               '95' MAY,
                                               '96' JUN,
                                               '7' JUL,
                                               '8' AUG,
                                               '9' SEP,
                                               '10' OCT,
                                               '11' NOV,
                                               '12' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 1 PUPUK  TUNGGAL
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS1
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS1,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
                                                        'PUPUK TUNGGAL'
                                                           DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS1 IN
                                                                         ('TBM0',
                                                                          'TBM1',
                                                                          'TBM2',
                                                                          'TBM3')
                                                                $where
															)
                                                        HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020300' -- UNTUK PUPUK  TUNGGAL
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('1' JAN,
                                               '2' FEB,
                                               '3' MAR,
                                               '4' APR,
                                               '5' MAY,
                                               '6' JUN,
                                               '97' JUL,
                                               '98' AUG,
                                               '99' SEP,
                                               '90' OCT,
                                               '91' NOV,
                                               '92' DEC)))
                     UNION ALL
                     SELECT *        -- UNTUK PERHITUNGAN SMS 2 PUPUK  TUNGGAL
                       FROM (WITH PIVOT_BLOK
                                     AS (SELECT PERIOD_BUDGET,
                                                REGION_CODE,
                                                BA_CODE,
                                                AFD_CODE,
                                                BLOCK_CODE,
                                                MATURITY_STAGE_SMS2
                                                   AS ACTIVITY_GROUP,
                                                'LABOUR' AS COST_ELEMENT,
                                                COA_CODE AS ACTIVITY_CODE,
                                                DESCRIPTION AS ACTIVITY_DESC,
                                                '' SUB_COST_ELEMENT,
                                                '' MATERIAL_NAME,
                                                UOM,
                                                QTY_BLN,
                                                COST_BLN,
                                                BULAN_PEMUPUKAN AS BLN
                                           FROM (SELECT HS.PERIOD_BUDGET,
                                                        HS.REGION_CODE,
                                                        HS.BA_CODE,
                                                        HS.AFD_CODE,
                                                        HS.BLOCK_CODE,
                                                        HS.HA_PLANTED,
                                                        HS.MATURITY_STAGE_SMS2,
                                                        MATERIAL.FLAG,
                                                        MATERIAL.UOM,
                                                        MATERIAL.COA_CODE,
                                                        'PUPUK TUNGGAL'
                                                           DESCRIPTION,
                                                        HS.TAHUN_TANAM,
                                                        BULAN_PEMUPUKAN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.QTY_HA)
                                                           AS QTY_BLN,
                                                        (HS.HA_PLANTED
                                                         * INFRA.HARGA_INTERNAL)
                                                           AS COST_BLN
                                                   FROM (SELECT RKT.*,
                                                                ORG.REGION_CODE
                                                           FROM    TM_HECTARE_STATEMENT RKT
                                                                LEFT JOIN
                                                                   TM_ORGANIZATION ORG
                                                                ON ORG.BA_CODE =
                                                                      RKT.BA_CODE
                                                          WHERE RKT.DELETE_USER IS NULL
                                                                AND RKT.FLAG_TEMP IS NULL
                                                                AND RKT.MATURITY_STAGE_SMS2 IN
                                                                         ('TBM0',
                                                                          'TBM1',
                                                                          'TBM2',
                                                                          'TBM3')
                                                                $where
															)
                                                        HS
                                                        LEFT JOIN TN_PUPUK_TBM2_TM NORMA_PUPUK
                                                           ON HS.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND HS.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND HS.AFD_CODE =
                                                                    NORMA_PUPUK.AFD_CODE
                                                              AND HS.BLOCK_CODE =
                                                                    NORMA_PUPUK.BLOCK_CODE
                                                              AND NORMA_PUPUK.DELETE_USER IS NULL
                                                        LEFT JOIN TM_MATERIAL MATERIAL
                                                           ON NORMA_PUPUK.PERIOD_BUDGET =
                                                                 MATERIAL.PERIOD_BUDGET
                                                              AND NORMA_PUPUK.BA_CODE =
                                                                    MATERIAL.BA_CODE
                                                              AND NORMA_PUPUK.MATERIAL_CODE =
                                                                    MATERIAL.MATERIAL_CODE
                                                              AND MATERIAL.DELETE_USER IS NULL
                                                        LEFT JOIN TN_INFRASTRUKTUR INFRA
                                                           ON INFRA.PERIOD_BUDGET =
                                                                 NORMA_PUPUK.PERIOD_BUDGET
                                                              AND INFRA.BA_CODE =
                                                                    NORMA_PUPUK.BA_CODE
                                                              AND INFRA.ACTIVITY_CODE =
                                                                    CASE MATERIAL.FLAG
                                                                       WHEN 'MAKRO'
                                                                       THEN
                                                                          43751
                                                                       ELSE
                                                                          43750
                                                                    END
                                                  WHERE MATERIAL.COA_CODE =
                                                           '5101020300' -- UNTUK PUPUK  TUNGGAL
                                                                       ))
                             SELECT *
                               FROM PIVOT_BLOK PIVOT (SUM (NVL (QTY_BLN, 0)) AS QTY,
                                                     SUM (NVL (COST_BLN, 0)) AS COST
                                               FOR BLN
                                               IN ('91' JAN,
                                               '92' FEB,
                                               '93' MAR,
                                               '94' APR,
                                               '95' MAY,
                                               '96' JUN,
                                               '7' JUL,
                                               '8' AUG,
                                               '9' SEP,
                                               '10' OCT,
                                               '11' NOV,
                                               '12' DEC))))) REPORT
   GROUP BY PERIOD_BUDGET,
            REGION_CODE,
            BA_CODE,
            AFD_CODE,
            BLOCK_CODE,
            TIPE_TRANSAKSI,
            COST_ELEMENT,
            ACTIVITY_CODE,
            ACTIVITY_DESC,
            SUB_COST_ELEMENT,
            MATERIAL_NAME,
            UOM   
   UNION ALL
   --ini untuk PUPUK TUNGGAL TOOLS
   SELECT PERIOD_BUDGET,
		 REGION_CODE,
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         TIPE_TRANSAKSI,
         COST_ELEMENT,
         ACTIVITY_CODE,
         ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         KETERANGAN,
         UOM,
         MAX (QTY_JAN) QTY_JAN,
         MAX (QTY_FEB) QTY_FEB,
         MAX (QTY_MAR) QTY_MAR,
         MAX (QTY_APR) QTY_APR,
         MAX (QTY_MAY) QTY_MAY,
         MAX (QTY_JUN) QTY_JUN,
         MAX (QTY_JUL) QTY_JUL,
         MAX (QTY_AUG) QTY_AUG,
         MAX (QTY_SEP) QTY_SEP,
         MAX (QTY_OCT) QTY_OCT,
         MAX (QTY_NOV) QTY_NOV,
         MAX (QTY_DEC) QTY_DEC,
         SUM (COST_JAN) AS COST_JAN,
         SUM (COST_FEB) AS COST_FEB,
         SUM (COST_MAR) AS COST_MAR,
         SUM (COST_APR) AS COST_APR,
         SUM (COST_MAY) AS COST_MAY,
         SUM (COST_JUN) AS COST_JUN,
         SUM (COST_JUL) AS COST_JUL,
         SUM (COST_AUG) AS COST_AUG,
         SUM (COST_SEP) AS COST_SEP,
         SUM (COST_OCT) AS COST_OCT,
         SUM (COST_NOV) AS COST_NOV,
         SUM (COST_DEC) AS COST_DEC,
         MAX (QTY_JAN) + 
         MAX (QTY_FEB) +
         MAX (QTY_MAR) +
         MAX (QTY_APR) +
         MAX (QTY_MAY) +
         MAX (QTY_JUN) +
         MAX (QTY_JUL) +
         MAX (QTY_AUG) +
         MAX (QTY_SEP) +
         MAX (QTY_OCT) +
         MAX (QTY_NOV) +
         MAX (QTY_DEC) QTY_SETAHUN,         
         SUM (COST_JAN) +
         SUM (COST_FEB) +
         SUM (COST_MAR) +
         SUM (COST_APR) +
         SUM (COST_MAY) +
         SUM (COST_JUN) +
         SUM (COST_JUL) +
         SUM (COST_AUG) +
         SUM (COST_SEP) +
         SUM (COST_OCT) +
         SUM (COST_NOV) +
         SUM (COST_DEC) AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
         SYSDATE AS INSERT_TIME
    FROM (    
		SELECT RKT.PERIOD_BUDGET,
					 ORG.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					 'TOOLS' AS COST_ELEMENT,
					 '5101020300' AS ACTIVITY_CODE,
					 'PUPUK TUNGGAL' AS ACTIVITY_DESC,
					 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
					 TM.MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'POKOK' AS UOM,                 
					 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JAN,
					 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_FEB,
					 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_MAR,
					 NVL (DIS_APR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_APR,
					 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_MAY,
					 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JUN,
					 0 AS QTY_JUL,
					 0 AS QTY_AUG,
					 0 AS QTY_SEP,
					 0 AS QTY_OCT,
					 0 AS QTY_NOV,
					 0 AS QTY_DEC,
					 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL AS COST_JAN,
					 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL AS COST_FEB,
					 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL AS COST_MAR,
					 NVL (DIS_APR, 0) * RP_QTY_INTERNAL AS COST_APR,
					 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL AS COST_MAY,
					 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL AS COST_JUN,
					 0 AS COST_JUL,
					 0 AS COST_AUG,
					 0 AS COST_SEP,
					 0 AS COST_OCT,
					 0 AS COST_NOV,
					 0 AS COST_DEC
				FROM TR_RKT_PUPUK_DISTRIBUSI RKT
					 LEFT JOIN TM_HECTARE_STATEMENT THS
						ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND RKT.BA_CODE = THS.BA_CODE
						   AND RKT.AFD_CODE = THS.AFD_CODE
						   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
					 LEFT JOIN TN_INFRASTRUKTUR TNI
						ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND TNI.BA_CODE = THS.BA_CODE
						   AND ACTIVITY_CODE = '43750'
					 LEFT JOIN TM_MATERIAL TM
						ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND TM.BA_CODE = TNI.BA_CODE
						   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TN_HARGA_BARANG THB
						ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND THB.BA_CODE = TNI.BA_CODE
						   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TM_ORGANIZATION ORG
						ON 	   ORG.BA_CODE = RKT.BA_CODE
			   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
					 AND RKT.MATURITY_STAGE_SMS1 <> 'TM'
					 AND TNI.COST_ELEMENT = 'TOOLS'
					 AND TM.COA_CODE = '5101020300'
					 $where3
			  UNION
			  SELECT RKT.PERIOD_BUDGET,
					 ORG.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
					 'TOOLS' AS COST_ELEMENT,
					 '5101020300' AS ACTIVITY_CODE,
					 'PUPUK TUNGGAL' AS ACTIVITY_DESC,
					 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
					 TM.MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'POKOK' AS UOM,                 
					 0 AS QTY_JAN,
					 0 AS QTY_FEB,
					 0 AS QTY_MAR,
					 0 AS QTY_APR,
					 0 AS QTY_MAY,
					 0 AS QTY_JUN,
					 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_JUL,
					 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_AUG,
					 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_SEP,
					 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_OCT,
					 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_NOV,
					 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
						AS QTY_DEC,
					 0 AS COST_JAN,
					 0 AS COST_FEB,
					 0 AS COST_MAR,
					 0 AS COST_APR,
					 0 AS COST_MAY,
					 0 AS COST_JUN,
					 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL AS COST_JUL,
					 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL AS COST_AUG,
					 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL AS COST_SEP,
					 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL AS COST_OCT,
					 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL AS COST_NOV,
					 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL AS COST_DEC
				FROM TR_RKT_PUPUK_DISTRIBUSI RKT
					 LEFT JOIN TM_HECTARE_STATEMENT THS
						ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND RKT.BA_CODE = THS.BA_CODE
						   AND RKT.AFD_CODE = THS.AFD_CODE
						   AND RKT.BLOCK_CODE = THS.BLOCK_CODE
					 LEFT JOIN TN_INFRASTRUKTUR TNI
						ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
						   AND TNI.BA_CODE = THS.BA_CODE
						   AND ACTIVITY_CODE = '43750'
					 LEFT JOIN TM_MATERIAL TM
						ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND TM.BA_CODE = TNI.BA_CODE
						   AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TN_HARGA_BARANG THB
						ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
						   AND THB.BA_CODE = TNI.BA_CODE
						   AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
					 LEFT JOIN TM_ORGANIZATION ORG
						ON     ORG.BA_CODE = RKT.BA_CODE
			   WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
					 AND RKT.MATURITY_STAGE_SMS1 <> 'TM'
					 AND TNI.COST_ELEMENT = 'TOOLS'
					 AND TM.COA_CODE = '5101020300'
					 $where3)
	GROUP BY PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 KETERANGAN,
			 UOM
UNION ALL
   --ini untuk PUPUK MAJEMUK TOOLS
   SELECT PERIOD_BUDGET,
		 REGION_CODE,
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         TIPE_TRANSAKSI,
         COST_ELEMENT,
         ACTIVITY_CODE,
         ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         KETERANGAN,
         UOM,
         MAX (QTY_JAN) QTY_JAN,
         MAX (QTY_FEB) QTY_FEB,
         MAX (QTY_MAR) QTY_MAR,
         MAX (QTY_APR) QTY_APR,
         MAX (QTY_MAY) QTY_MAY,
         MAX (QTY_JUN) QTY_JUN,
         MAX (QTY_JUL) QTY_JUL,
         MAX (QTY_AUG) QTY_AUG,
         MAX (QTY_SEP) QTY_SEP,
         MAX (QTY_OCT) QTY_OCT,
         MAX (QTY_NOV) QTY_NOV,
         MAX (QTY_DEC) QTY_DEC,
         SUM (COST_JAN) AS COST_JAN,
         SUM (COST_FEB) AS COST_FEB,
         SUM (COST_MAR) AS COST_MAR,
         SUM (COST_APR) AS COST_APR,
         SUM (COST_MAY) AS COST_MAY,
         SUM (COST_JUN) AS COST_JUN,
         SUM (COST_JUL) AS COST_JUL,
         SUM (COST_AUG) AS COST_AUG,
         SUM (COST_SEP) AS COST_SEP,
         SUM (COST_OCT) AS COST_OCT,
         SUM (COST_NOV) AS COST_NOV,
         SUM (COST_DEC) AS COST_DEC,
         MAX (QTY_JAN) + 
         MAX (QTY_FEB) +
         MAX (QTY_MAR) +
         MAX (QTY_APR) +
         MAX (QTY_MAY) +
         MAX (QTY_JUN) +
         MAX (QTY_JUL) +
         MAX (QTY_AUG) +
         MAX (QTY_SEP) +
         MAX (QTY_OCT) +
         MAX (QTY_NOV) +
         MAX (QTY_DEC) QTY_SETAHUN,         
         SUM (COST_JAN) +
         SUM (COST_FEB) +
         SUM (COST_MAR) +
         SUM (COST_APR) +
         SUM (COST_MAY) +
         SUM (COST_JUN) +
         SUM (COST_JUL) +
         SUM (COST_AUG) +
         SUM (COST_SEP) +
         SUM (COST_OCT) +
         SUM (COST_NOV) +
         SUM (COST_DEC) AS COST_SETAHUN,
         '".$this->_userName."' AS INSERT_USER,
         SYSDATE AS INSERT_TIME
    FROM (    
    SELECT RKT.PERIOD_BUDGET,
				 ORG.REGION_CODE,
                 RKT.BA_CODE,
                 RKT.AFD_CODE,
                 RKT.BLOCK_CODE,
                 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
                 'TOOLS' AS COST_ELEMENT,
                 '5101020300' AS ACTIVITY_CODE,
                 'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
                 TM.MATERIAL_NAME,
                 '' AS KETERANGAN,
                 'POKOK' AS UOM,                 
                 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_JAN,
                 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_FEB,
                 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_MAR,
                 NVL (DIS_APR, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_APR,
                 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_MAY,
                 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_JUN,
                 0 AS QTY_JUL,
                 0 AS QTY_AUG,
                 0 AS QTY_SEP,
                 0 AS QTY_OCT,
                 0 AS QTY_NOV,
                 0 AS QTY_DEC,
                 NVL (DIS_JAN, 0) * RP_QTY_INTERNAL AS COST_JAN,
                 NVL (DIS_FEB, 0) * RP_QTY_INTERNAL AS COST_FEB,
                 NVL (DIS_MAR, 0) * RP_QTY_INTERNAL AS COST_MAR,
                 NVL (DIS_APR, 0) * RP_QTY_INTERNAL AS COST_APR,
                 NVL (DIS_MAY, 0) * RP_QTY_INTERNAL AS COST_MAY,
                 NVL (DIS_JUN, 0) * RP_QTY_INTERNAL AS COST_JUN,
                 0 AS COST_JUL,
                 0 AS COST_AUG,
                 0 AS COST_SEP,
                 0 AS COST_OCT,
                 0 AS COST_NOV,
                 0 AS COST_DEC
            FROM TR_RKT_PUPUK_DISTRIBUSI RKT
                 LEFT JOIN TM_HECTARE_STATEMENT THS
                    ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
                       AND RKT.BA_CODE = THS.BA_CODE
                       AND RKT.AFD_CODE = THS.AFD_CODE
                       AND RKT.BLOCK_CODE = THS.BLOCK_CODE
                 LEFT JOIN TN_INFRASTRUKTUR TNI
                    ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
                       AND TNI.BA_CODE = THS.BA_CODE
                       AND ACTIVITY_CODE = '43750'
                 LEFT JOIN TM_MATERIAL TM
                    ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
                       AND TM.BA_CODE = TNI.BA_CODE
                       AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
                 LEFT JOIN TN_HARGA_BARANG THB
                    ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
                       AND THB.BA_CODE = TNI.BA_CODE
                       AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
				 LEFT JOIN TM_ORGANIZATION ORG
                    ON 	   ORG.BA_CODE = RKT.BA_CODE
           WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
                 AND RKT.MATURITY_STAGE_SMS1 <> 'TM'
                 AND TNI.COST_ELEMENT = 'TOOLS'
                 AND TM.COA_CODE = '5101020400'                 
                 $where3                 
          UNION
          SELECT RKT.PERIOD_BUDGET,
				 ORG.REGION_CODE,
                 RKT.BA_CODE,
                 RKT.AFD_CODE,
                 RKT.BLOCK_CODE,
                 RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
                 'TOOLS' AS COST_ELEMENT,
                 '5101020400' AS ACTIVITY_CODE,
                 'PUPUK MAJEMUK' AS ACTIVITY_DESC,
                 TNI.SUB_COST_ELEMENT SUB_COST_ELEMENT,
                 TM.MATERIAL_NAME,
                 '' AS KETERANGAN,
                 'POKOK' AS UOM,                 
                 0 AS QTY_JAN,
                 0 AS QTY_FEB,
                 0 AS QTY_MAR,
                 0 AS QTY_APR,
                 0 AS QTY_MAY,
                 0 AS QTY_JUN,
                 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_JUL,
                 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_AUG,
                 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_SEP,
                 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_OCT,
                 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_NOV,
                 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL / NVL (THB.PRICE, 1)
                    AS QTY_DEC,
                 0 AS COST_JAN,
                 0 AS COST_FEB,
                 0 AS COST_MAR,
                 0 AS COST_APR,
                 0 AS COST_MAY,
                 0 AS COST_JUN,
                 NVL (DIS_JUL, 0) * RP_QTY_INTERNAL AS COST_JUL,
                 NVL (DIS_AUG, 0) * RP_QTY_INTERNAL AS COST_AUG,
                 NVL (DIS_SEP, 0) * RP_QTY_INTERNAL AS COST_SEP,
                 NVL (DIS_OCT, 0) * RP_QTY_INTERNAL AS COST_OCT,
                 NVL (DIS_NOV, 0) * RP_QTY_INTERNAL AS COST_NOV,
                 NVL (DIS_DEC, 0) * RP_QTY_INTERNAL AS COST_DEC
            FROM TR_RKT_PUPUK_DISTRIBUSI RKT
                 LEFT JOIN TM_HECTARE_STATEMENT THS
                    ON     RKT.PERIOD_BUDGET = THS.PERIOD_BUDGET
                       AND RKT.BA_CODE = THS.BA_CODE
                       AND RKT.AFD_CODE = THS.AFD_CODE
                       AND RKT.BLOCK_CODE = THS.BLOCK_CODE
                 LEFT JOIN TN_INFRASTRUKTUR TNI
                    ON     TNI.PERIOD_BUDGET = THS.PERIOD_BUDGET
                       AND TNI.BA_CODE = THS.BA_CODE
                       AND ACTIVITY_CODE = '43750'
                 LEFT JOIN TM_MATERIAL TM
                    ON     TM.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
                       AND TM.BA_CODE = TNI.BA_CODE
                       AND TM.PERIOD_BUDGET = TNI.PERIOD_BUDGET
                 LEFT JOIN TN_HARGA_BARANG THB
                    ON     THB.MATERIAL_CODE = TNI.SUB_COST_ELEMENT
                       AND THB.BA_CODE = TNI.BA_CODE
                       AND THB.PERIOD_BUDGET = TNI.PERIOD_BUDGET
				  LEFT JOIN TM_ORGANIZATION ORG
                    ON 	   ORG.BA_CODE = RKT.BA_CODE
           WHERE     RKT.TIPE_TRANSAKSI LIKE '%POKOK%'
                 AND RKT.MATURITY_STAGE_SMS1 <> 'TM'
                 AND TNI.COST_ELEMENT = 'TOOLS'
                 AND TM.COA_CODE = '5101020400'
                 $where3)
GROUP BY PERIOD_BUDGET,
		 REGION_CODE,
         BA_CODE,
         AFD_CODE,
         BLOCK_CODE,
         TIPE_TRANSAKSI,
         COST_ELEMENT,
         ACTIVITY_CODE,
         ACTIVITY_DESC,
         SUB_COST_ELEMENT,
         MATERIAL_NAME,
         KETERANGAN,
         UOM
UNION ALL
   SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE TIPE_TRANSAKSI,
          'MATERIAL' AS COST_ELEMENT,
          RKT.COA_CODE AS ACTIVITY_CODE,
          'PUPUK MAJEMUK' AS ACTIVITY_DESC,
          RKT.MATERIAL_CODE AS SUB_COST_ELEMENT,
          RKT.MATERIAL_NAME,
          '' KETERANGAN,
          'KG' UOM,
          RKT.QTY_MAJEMUK_JAN AS QTY_JAN,
          RKT.QTY_MAJEMUK_FEB AS QTY_FEB,
          RKT.QTY_MAJEMUK_MAR AS QTY_MAR,
          RKT.QTY_MAJEMUK_APR AS QTY_APR,
          RKT.QTY_MAJEMUK_MAY AS QTY_MAY,
          RKT.QTY_MAJEMUK_JUN AS QTY_JUN,
          RKT.QTY_MAJEMUK_JUL AS QTY_JUL,
          RKT.QTY_MAJEMUK_AUG AS QTY_AUG,
          RKT.QTY_MAJEMUK_SEP AS QTY_SEP,
          RKT.QTY_MAJEMUK_OCT AS QTY_OCT,
          RKT.QTY_MAJEMUK_NOV AS QTY_NOV,
          RKT.QTY_MAJEMUK_DEC AS QTY_DEC,
          (RKT.QTY_MAJEMUK_JAN * HARGA.PRICE) AS COST_JAN,
          (RKT.QTY_MAJEMUK_FEB * HARGA.PRICE) AS COST_FEB,
          (RKT.QTY_MAJEMUK_MAR * HARGA.PRICE) AS COST_MAR,
          (RKT.QTY_MAJEMUK_APR * HARGA.PRICE) AS COST_APR,
          (RKT.QTY_MAJEMUK_MAY * HARGA.PRICE) AS COST_MAY,
          (RKT.QTY_MAJEMUK_JUN * HARGA.PRICE) AS COST_JUN,
          (RKT.QTY_MAJEMUK_JUL * HARGA.PRICE) AS COST_JUL,
          (RKT.QTY_MAJEMUK_AUG * HARGA.PRICE) AS COST_AUG,
          (RKT.QTY_MAJEMUK_SEP * HARGA.PRICE) AS COST_SEP,
          (RKT.QTY_MAJEMUK_OCT * HARGA.PRICE) AS COST_OCT,
          (RKT.QTY_MAJEMUK_NOV * HARGA.PRICE) AS COST_NOV,
          (RKT.QTY_MAJEMUK_DEC * HARGA.PRICE) AS COST_DEC,
		  (  RKT.QTY_MAJEMUK_JAN
           + RKT.QTY_MAJEMUK_FEB
           + RKT.QTY_MAJEMUK_MAR
           + RKT.QTY_MAJEMUK_APR
           + RKT.QTY_MAJEMUK_MAY
           + RKT.QTY_MAJEMUK_JUN
           + RKT.QTY_MAJEMUK_JUL
           + RKT.QTY_MAJEMUK_AUG
           + RKT.QTY_MAJEMUK_SEP
           + RKT.QTY_MAJEMUK_OCT
           + RKT.QTY_MAJEMUK_NOV
           + RKT.QTY_MAJEMUK_DEC)
             AS QTY_SETAHUN,
          (  (RKT.QTY_MAJEMUK_JAN * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_FEB * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_MAR * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_APR * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_MAY * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_JUN * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_JUL * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_AUG * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_SEP * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_OCT * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_NOV * HARGA.PRICE)
           + (RKT.QTY_MAJEMUK_DEC * HARGA.PRICE))
             AS COST_SETAHUN,
          '".$this->_userName."' AS INSERT_USER,
          SYSDATE AS INSERT_TIME
     FROM (  SELECT PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE MATERIAL_CODE,
                    ACTIVITY_NAME MATERIAL_NAME,
                    SUM (QTY_MAJEMUK_JAN) QTY_MAJEMUK_JAN,
                    SUM (QTY_MAJEMUK_FEB) QTY_MAJEMUK_FEB,
                    SUM (QTY_MAJEMUK_MAR) QTY_MAJEMUK_MAR,
                    SUM (QTY_MAJEMUK_APR) QTY_MAJEMUK_APR,
                    SUM (QTY_MAJEMUK_MAY) QTY_MAJEMUK_MAY,
                    SUM (QTY_MAJEMUK_JUN) QTY_MAJEMUK_JUN,
                    SUM (QTY_MAJEMUK_JUL) QTY_MAJEMUK_JUL,
                    SUM (QTY_MAJEMUK_AUG) QTY_MAJEMUK_AUG,
                    SUM (QTY_MAJEMUK_SEP) QTY_MAJEMUK_SEP,
                    SUM (QTY_MAJEMUK_OCT) QTY_MAJEMUK_OCT,
                    SUM (QTY_MAJEMUK_NOV) QTY_MAJEMUK_NOV,
                    SUM (QTY_MAJEMUK_DEC) QTY_MAJEMUK_DEC
               FROM (SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            RKT.DIS_JAN QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JAN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            RKT.DIS_FEB QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_FEB
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            RKT.DIS_MAR QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_MAR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            RKT.DIS_APR QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_APR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            RKT.DIS_MAY QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_MAY
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            RKT.DIS_JUN QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JUN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            RKT.DIS_JUL QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_JUL
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            RKT.DIS_AUG QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_AUG
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            RKT.DIS_SEP QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_SEP
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            RKT.DIS_OCT QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_OCT
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            RKT.DIS_NOV QTY_MAJEMUK_NOV,
                            0 QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_NOV
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_MAJEMUK_JAN,
                            0 QTY_MAJEMUK_FEB,
                            0 QTY_MAJEMUK_MAR,
                            0 QTY_MAJEMUK_APR,
                            0 QTY_MAJEMUK_MAY,
                            0 QTY_MAJEMUK_JUN,
                            0 QTY_MAJEMUK_JUL,
                            0 QTY_MAJEMUK_AUG,
                            0 QTY_MAJEMUK_SEP,
                            0 QTY_MAJEMUK_OCT,
                            0 QTY_MAJEMUK_NOV,
                            RKT.DIS_DEC QTY_MAJEMUK_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE =
                                     RKT.MATERIAL_CODE_DEC
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020400')
           GROUP BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE,
                    ACTIVITY_NAME
           ORDER BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE) RKT
          LEFT JOIN TN_HARGA_BARANG HARGA
             ON     HARGA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                AND HARGA.BA_CODE = RKT.BA_CODE
                AND HARGA.MATERIAL_CODE = RKT.MATERIAL_CODE
                AND HARGA.DELETE_USER IS NULL
                AND HARGA.FLAG_TEMP IS NULL
          LEFT JOIN TM_ORGANIZATION ORG
             ON ORG.BA_CODE = RKT.BA_CODE
    WHERE     1 = 1
          AND RKT.MATURITY_STAGE IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
          $where
	UNION ALL
	SELECT     RKT.PERIOD_BUDGET,
                        ORG.REGION_CODE,
                        RKT.BA_CODE,
                        RKT.AFD_CODE,
                        RKT.BLOCK_CODE,
                        RKT.MATURITY_STAGE TIPE_TRANSAKSI,
                        'MATERIAL' AS COST_ELEMENT,
                        RKT.COA_CODE AS ACTIVITY_CODE,
                        'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                        RKT.MATERIAL_CODE AS SUB_COST_ELEMENT,
                        RKT.MATERIAL_NAME,
                        '' KETERANGAN,
                        'KG' UOM,
                        RKT.QTY_TUNGGAL_JAN AS QTY_JAN,
                        RKT.QTY_TUNGGAL_FEB AS QTY_FEB,
                        RKT.QTY_TUNGGAL_MAR AS QTY_MAR,
                        RKT.QTY_TUNGGAL_APR AS QTY_APR,
                        RKT.QTY_TUNGGAL_MAY AS QTY_MAY,
                        RKT.QTY_TUNGGAL_JUN AS QTY_JUN,
                        RKT.QTY_TUNGGAL_JUL AS QTY_JUL,
                        RKT.QTY_TUNGGAL_AUG AS QTY_AUG,
                        RKT.QTY_TUNGGAL_SEP AS  QTY_SEP,
                        RKT.QTY_TUNGGAL_OCT AS QTY_OCT,
                        RKT.QTY_TUNGGAL_NOV AS QTY_NOV,
                        RKT.QTY_TUNGGAL_DEC AS QTY_DEC,
                        (RKT.QTY_TUNGGAL_JAN * HARGA.PRICE) AS COST_JAN,
                        (RKT.QTY_TUNGGAL_FEB * HARGA.PRICE) AS COST_FEB,
                        (RKT.QTY_TUNGGAL_MAR * HARGA.PRICE) AS COST_MAR,
                        (RKT.QTY_TUNGGAL_APR * HARGA.PRICE) AS COST_APR,
                        (RKT.QTY_TUNGGAL_MAY * HARGA.PRICE) AS COST_MAY,
                        (RKT.QTY_TUNGGAL_JUN * HARGA.PRICE) AS COST_JUN,
                        (RKT.QTY_TUNGGAL_JUL * HARGA.PRICE) AS COST_JUL,
                        (RKT.QTY_TUNGGAL_AUG * HARGA.PRICE) AS COST_AUG,
                        (RKT.QTY_TUNGGAL_SEP * HARGA.PRICE) AS COST_SEP,
                        (RKT.QTY_TUNGGAL_OCT * HARGA.PRICE) AS COST_OCT,
                        (RKT.QTY_TUNGGAL_NOV * HARGA.PRICE) AS COST_NOV,
                        (RKT.QTY_TUNGGAL_DEC * HARGA.PRICE) AS COST_DEC,
						(RKT.QTY_TUNGGAL_JAN +
                        RKT.QTY_TUNGGAL_FEB +
                        RKT.QTY_TUNGGAL_MAR +
                        RKT.QTY_TUNGGAL_APR +
                        RKT.QTY_TUNGGAL_MAY +
                        RKT.QTY_TUNGGAL_JUN +
                        RKT.QTY_TUNGGAL_JUL +
                        RKT.QTY_TUNGGAL_AUG +
                        RKT.QTY_TUNGGAL_SEP +
                        RKT.QTY_TUNGGAL_OCT +
                        RKT.QTY_TUNGGAL_NOV +
                        RKT.QTY_TUNGGAL_DEC)
                        AS QTY_SETAHUN,
                        ((RKT.QTY_TUNGGAL_JAN * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_FEB * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_MAR * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_APR * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_MAY * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_JUN * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_JUL * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_AUG * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_SEP * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_OCT * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_NOV * HARGA.PRICE) +
                        (RKT.QTY_TUNGGAL_DEC * HARGA.PRICE)) 
                        AS COST_SETAHUN,
                        '".$this->_userName."' AS INSERT_USER,
                        SYSDATE AS INSERT_TIME
          FROM (
        SELECT PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE MATERIAL_CODE,
                    ACTIVITY_NAME MATERIAL_NAME,
                    SUM (QTY_TUNGGAL_JAN) QTY_TUNGGAL_JAN,
                    SUM (QTY_TUNGGAL_FEB) QTY_TUNGGAL_FEB,
                    SUM (QTY_TUNGGAL_MAR) QTY_TUNGGAL_MAR,
                    SUM (QTY_TUNGGAL_APR) QTY_TUNGGAL_APR,
                    SUM (QTY_TUNGGAL_MAY) QTY_TUNGGAL_MAY,
                    SUM (QTY_TUNGGAL_JUN) QTY_TUNGGAL_JUN,
                    SUM (QTY_TUNGGAL_JUL) QTY_TUNGGAL_JUL,
                    SUM (QTY_TUNGGAL_AUG) QTY_TUNGGAL_AUG,
                    SUM (QTY_TUNGGAL_SEP) QTY_TUNGGAL_SEP,
                    SUM (QTY_TUNGGAL_OCT) QTY_TUNGGAL_OCT,
                    SUM (QTY_TUNGGAL_NOV) QTY_TUNGGAL_NOV,
                    SUM (QTY_TUNGGAL_DEC) QTY_TUNGGAL_DEC
               FROM (
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            RKT.DIS_JAN QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JAN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            RKT.DIS_FEB QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_FEB
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300' 
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            RKT.DIS_MAR QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_MAR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            RKT.DIS_APR QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_APR
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            RKT.DIS_MAY QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_MAY
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '1' SEMESTER,
                            RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            RKT.DIS_JUN QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JUN
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            RKT.DIS_JUL QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_JUL
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            RKT.DIS_AUG QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_AUG
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            RKT.DIS_SEP QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_SEP
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            RKT.DIS_OCT QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_OCT
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            RKT.DIS_NOV QTY_TUNGGAL_NOV,
                            0 QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_NOV
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                     UNION ALL
                     SELECT RKT.PERIOD_BUDGET,
                            RKT.BA_CODE,
                            RKT.AFD_CODE,
                            RKT.BLOCK_CODE,
                            '2' SEMESTER,
                            RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                            MATERIAL.COA_CODE COA_CODE,
                            MATERIAL.MATERIAL_CODE ACTIVITY_CODE,
                            MATERIAL.MATERIAL_NAME ACTIVITY_NAME,
                            0 QTY_TUNGGAL_JAN,
                            0 QTY_TUNGGAL_FEB,
                            0 QTY_TUNGGAL_MAR,
                            0 QTY_TUNGGAL_APR,
                            0 QTY_TUNGGAL_MAY,
                            0 QTY_TUNGGAL_JUN,
                            0 QTY_TUNGGAL_JUL,
                            0 QTY_TUNGGAL_AUG,
                            0 QTY_TUNGGAL_SEP,
                            0 QTY_TUNGGAL_OCT,
                            0 QTY_TUNGGAL_NOV,
                            RKT.DIS_DEC QTY_TUNGGAL_DEC
                       FROM    TR_RKT_PUPUK_DISTRIBUSI RKT
                            LEFT JOIN
                               TM_MATERIAL MATERIAL
                            ON     MATERIAL.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                               AND MATERIAL.BA_CODE = RKT.BA_CODE
                               AND MATERIAL.MATERIAL_CODE = RKT.MATERIAL_CODE_DEC
                      WHERE     RKT.DELETE_USER IS NULL
                            AND RKT.TIPE_TRANSAKSI LIKE '%KG%'
                            AND MATERIAL.COA_CODE = '5101020300'
                            )
           GROUP BY PERIOD_BUDGET,
                    BA_CODE,
                    BLOCK_CODE,
                    AFD_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE,
                    ACTIVITY_NAME
           ORDER BY PERIOD_BUDGET,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    SEMESTER,
                    MATURITY_STAGE,
                    COA_CODE,
                    ACTIVITY_CODE
        )RKT
                LEFT JOIN TN_HARGA_BARANG HARGA
                    ON HARGA.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                    AND HARGA.BA_CODE = RKT.BA_CODE
                    AND HARGA.MATERIAL_CODE = RKT.MATERIAL_CODE
                    AND HARGA.DELETE_USER IS NULL
                    AND HARGA.FLAG_TEMP IS NULL
                LEFT JOIN TM_ORGANIZATION ORG
                    ON ORG.BA_CODE = RKT.BA_CODE
                WHERE 1 = 1
                    AND RKT.MATURITY_STAGE IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                    $where 
				UNION ALL	
				SELECT PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, COST_ELEMENT, ACTIVITY_CODE, ACTIVITY_DESC, SUB_COST_ELEMENT,
      MATERIAL_NAME, KETERANGAN, UOM, QTY_JAN, QTY_FEB, QTY_MAR, QTY_APR, QTY_MAY, QTY_JUN, QTY_JUL, QTY_AUG, QTY_SEP, QTY_OCT, QTY_NOV, QTY_DEC, ( QTY_JAN
      + QTY_FEB + QTY_MAR + QTY_APR + QTY_MAY + QTY_JUN + QTY_JUL + QTY_AUG + QTY_SEP + QTY_OCT + QTY_NOV + QTY_DEC) AS QTY_SETAHUN, COST_JAN, COST_FEB,
      COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, ( COST_JAN + COST_FEB + COST_MAR + COST_APR +
      COST_MAY + COST_JUN + COST_JUL + COST_AUG + COST_SEP + COST_OCT + COST_NOV + COST_DEC) AS COST_SETAHUN, '".$this->_userName."' AS INSERT_USER, SYSDATE
      AS INSERT_TIME
      FROM (
      SELECT COST.PERIOD_BUDGET, COST.REGION_CODE, COST.BA_CODE, COST.AFD_CODE, COST.BLOCK_CODE, KG_PUPUK.MATURITY_STAGE AS TIPE_TRANSAKSI, COST.
      COST_ELEMENT, '5101020300' AS ACTIVITY_CODE, 'PUPUK TUNGGAL' AS ACTIVITY_DESC, '' AS SUB_COST_ELEMENT, '' AS MATERIAL_NAME, '' AS KETERANGAN, 'KG' UOM
      , 0 QTY_JAN, 0 QTY_FEB, 0 QTY_MAR, 0 QTY_APR, 0 QTY_MAY, 0 QTY_JUN, 0 QTY_JUL, 0 QTY_AUG, 0 QTY_SEP, 0 QTY_OCT, 0 QTY_NOV, 0 QTY_DEC,
      CASE WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_JAN / KG_PUPUK.QTY_TOTAL_JAN * COST.DIS_COST_JAN)
      ELSE 0
      END AS COST_JAN,
      CASE WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_FEB / KG_PUPUK.QTY_TOTAL_FEB * COST.DIS_COST_FEB)
      ELSE 0
      END AS COST_FEB,
      CASE WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_MAR / KG_PUPUK.QTY_TOTAL_MAR * COST.DIS_COST_MAR)
      ELSE 0
      END AS COST_MAR,
      CASE WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_APR / KG_PUPUK.QTY_TOTAL_APR * COST.DIS_COST_APR)
      ELSE 0
      END AS COST_APR,
      CASE WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_MAY / KG_PUPUK.QTY_TOTAL_MAY * COST.DIS_COST_MAY)
      ELSE 0
      END AS COST_MAY,
      CASE WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_JUN / KG_PUPUK.QTY_TOTAL_JUN * COST.DIS_COST_JUN)
      ELSE 0
      END AS COST_JUN,
      CASE WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_JUL / KG_PUPUK.QTY_TOTAL_JUL * COST.DIS_COST_JUL)
      ELSE 0
      END AS COST_JUL,
      CASE WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_AUG / KG_PUPUK.QTY_TOTAL_AUG * COST.DIS_COST_AUG)
      ELSE 0
      END AS COST_AUG,
      CASE WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_SEP / KG_PUPUK.QTY_TOTAL_SEP * COST.DIS_COST_SEP)
      ELSE 0
      END AS COST_SEP,
      CASE WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_OCT / KG_PUPUK.QTY_TOTAL_OCT * COST.DIS_COST_OCT)
      ELSE 0
      END AS COST_OCT,
      CASE WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_NOV / KG_PUPUK.QTY_TOTAL_NOV * COST.DIS_COST_NOV)
      ELSE 0
      END AS COST_NOV,
      CASE WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
      THEN ( KG_PUPUK.QTY_TUNGGAL_DEC / KG_PUPUK.QTY_TOTAL_DEC * COST.DIS_COST_DEC)
      ELSE 0
      END AS COST_DEC
      FROM (
      SELECT PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, MATURITY_STAGE, SUM (DIS_COST_JAN) DIS_COST_JAN, SUM (DIS_COST_FEB)
      DIS_COST_FEB, SUM (DIS_COST_MAR) DIS_COST_MAR, SUM (DIS_COST_APR) DIS_COST_APR, SUM (DIS_COST_MAY) DIS_COST_MAY, SUM (DIS_COST_JUN) DIS_COST_JUN, SUM
      (DIS_COST_JUL) DIS_COST_JUL, SUM (DIS_COST_AUG) DIS_COST_AUG, SUM (DIS_COST_SEP) DIS_COST_SEP, SUM (DIS_COST_OCT) DIS_COST_OCT, SUM (DIS_COST_NOV)
      DIS_COST_NOV, SUM (DIS_COST_DEC) DIS_COST_DEC, MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK, MAX (COST_TOOLS_KG) COST_TOOLS_KG, MAX (COST_TRANSPORT_KG)
      COST_TRANSPORT_KG
      FROM (
      SELECT RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.COST_ELEMENT, RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE, SUM
      (RKT.DIS_COST_JAN) AS DIS_COST_JAN, SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB, SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR, SUM (RKT.DIS_COST_APR) AS
      DIS_COST_APR, SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY, SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN, 0 DIS_COST_JUL, 0 DIS_COST_AUG, 0 DIS_COST_SEP, 0
      DIS_COST_OCT, 0 DIS_COST_NOV, 0 DIS_COST_DEC, MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK, MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG, MAX (RKT.
      COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
      FROM TR_RKT_PUPUK_COST_ELEMENT RKT LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      WHERE RKT.DELETE_USER IS NULL
      AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
      AND RKT.COST_ELEMENT <> 'MATERIAL'
      $where
	  GROUP BY RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.COST_ELEMENT, RKT.
      MATURITY_STAGE_SMS1 UNION ALL
      SELECT RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.COST_ELEMENT, RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE, 0
      DIS_COST_JAN, 0 DIS_COST_FEB, 0 DIS_COST_MAR, 0 DIS_COST_APR, 0 DIS_COST_MAY, 0 DIS_COST_JUN, SUM (RKT.DIS_COST_JUL) DIS_COST_JUL, SUM (RKT.
      DIS_COST_AUG) DIS_COST_AUG, SUM (RKT.DIS_COST_SEP) DIS_COST_SEP, SUM (RKT.DIS_COST_OCT) DIS_COST_OCT, SUM (RKT.DIS_COST_NOV) DIS_COST_NOV, SUM (RKT.
      DIS_COST_DEC) DIS_COST_DEC, MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK, MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG, MAX (RKT.COST_TRANSPORT_KG) AS
      COST_TRANSPORT_KG
      FROM TR_RKT_PUPUK_COST_ELEMENT RKT LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      WHERE RKT.DELETE_USER IS NULL
      AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
      AND RKT.COST_ELEMENT <> 'MATERIAL'
      $where
	  GROUP BY RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.COST_ELEMENT, RKT.
      MATURITY_STAGE_SMS2) GROUP BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, MATURITY_STAGE) COST LEFT JOIN V_KG_PUPUK_AFD
      KG_PUPUK ON COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
      AND COST.BA_CODE = KG_PUPUK.BA_CODE
      AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
      AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE 
	  UNION ALL
      -- RKT PUPUK MAJEMUK SELAIN COST ELEMENT MATERIAL
SELECT COST.PERIOD_BUDGET,
       COST.REGION_CODE,
       COST.BA_CODE,
       COST.AFD_CODE,
       COST.BLOCK_CODE,
       KG_PUPUK.MATURITY_STAGE AS TIPE_TRANSAKSI,
       COST.COST_ELEMENT,
       '5101020400' AS ACTIVITY_CODE,
       'PUPUK MAJEMUK' AS ACTIVITY_NAME,
       '' AS SUB_COST_ELEMENT,
       '' AS MATERIAL_NAME,
       '' AS KETERANGAN,
       'KG' UOM,
       0 QTY_JAN,
       0 QTY_FEB,
       0 QTY_MAR,
       0 QTY_APR,
       0 QTY_MAY,
       0 QTY_JUN,
       0 QTY_JUL,
       0 QTY_AUG,
       0 QTY_SEP,
       0 QTY_OCT,
       0 QTY_NOV,
       0 QTY_DEC,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_JAN <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_JAN
              / KG_PUPUK.QTY_TOTAL_JAN
              * COST.DIS_COST_JAN)
          ELSE
             0
       END
          AS COST_JAN,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_FEB <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_FEB
              / KG_PUPUK.QTY_TOTAL_FEB
              * COST.DIS_COST_FEB)
          ELSE
             0
       END
          AS COST_FEB,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_MAR <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_MAR
              / KG_PUPUK.QTY_TOTAL_MAR
              * COST.DIS_COST_MAR)
          ELSE
             0
       END
          AS COST_MAR,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_APR <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_APR
              / KG_PUPUK.QTY_TOTAL_APR
              * COST.DIS_COST_APR)
          ELSE
             0
       END
          AS COST_APR,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_MAY <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_MAY
              / KG_PUPUK.QTY_TOTAL_MAY
              * COST.DIS_COST_MAY)
          ELSE
             0
       END
          AS COST_MAY,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_JUN <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_JUN
              / KG_PUPUK.QTY_TOTAL_JUN
              * COST.DIS_COST_JUN)
          ELSE
             0
       END
          AS COST_JUN,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_JUL <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_JUL
              / KG_PUPUK.QTY_TOTAL_JUL
              * COST.DIS_COST_JUL)
          ELSE
             0
       END
          AS COST_JUL,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_AUG <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_AUG
              / KG_PUPUK.QTY_TOTAL_AUG
              * COST.DIS_COST_AUG)
          ELSE
             0
       END
          AS COST_AUG,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_SEP <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_SEP
              / KG_PUPUK.QTY_TOTAL_SEP
              * COST.DIS_COST_SEP)
          ELSE
             0
       END
          AS COST_SEP,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_OCT <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_OCT
              / KG_PUPUK.QTY_TOTAL_OCT
              * COST.DIS_COST_OCT)
          ELSE
             0
       END
          AS COST_OCT,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_NOV <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_NOV
              / KG_PUPUK.QTY_TOTAL_NOV
              * COST.DIS_COST_NOV)
          ELSE
             0
       END
          AS COST_NOV,
       CASE
          WHEN KG_PUPUK.QTY_TOTAL_DEC <> 0
          THEN
             (  KG_PUPUK.QTY_MAJEMUK_DEC
              / KG_PUPUK.QTY_TOTAL_DEC
              * COST.DIS_COST_DEC)
          ELSE
             0
       END
          AS COST_DEC
  FROM    (  SELECT PERIOD_BUDGET,
                    REGION_CODE,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    COST_ELEMENT,
                    MATURITY_STAGE,
                    SUM (DIS_COST_JAN) DIS_COST_JAN,
                    SUM (DIS_COST_FEB) DIS_COST_FEB,
                    SUM (DIS_COST_MAR) DIS_COST_MAR,
                    SUM (DIS_COST_APR) DIS_COST_APR,
                    SUM (DIS_COST_MAY) DIS_COST_MAY,
                    SUM (DIS_COST_JUN) DIS_COST_JUN,
                    SUM (DIS_COST_JUL) DIS_COST_JUL,
                    SUM (DIS_COST_AUG) DIS_COST_AUG,
                    SUM (DIS_COST_SEP) DIS_COST_SEP,
                    SUM (DIS_COST_OCT) DIS_COST_OCT,
                    SUM (DIS_COST_NOV) DIS_COST_NOV,
                    SUM (DIS_COST_DEC) DIS_COST_DEC,
                    MAX (COST_LABOUR_POKOK) COST_LABOUR_POKOK,
                    MAX (COST_TOOLS_KG) COST_TOOLS_KG,
                    MAX (COST_TRANSPORT_KG) COST_TRANSPORT_KG
               FROM (  SELECT RKT.PERIOD_BUDGET,
                              ORG.REGION_CODE,
                              RKT.BA_CODE,
                              RKT.AFD_CODE,
                              RKT.BLOCK_CODE,
                              RKT.COST_ELEMENT,
                              RKT.MATURITY_STAGE_SMS1 AS MATURITY_STAGE,
                              SUM (RKT.DIS_COST_JAN) AS DIS_COST_JAN,
                              SUM (RKT.DIS_COST_FEB) AS DIS_COST_FEB,
                              SUM (RKT.DIS_COST_MAR) AS DIS_COST_MAR,
                              SUM (RKT.DIS_COST_APR) AS DIS_COST_APR,
                              SUM (RKT.DIS_COST_MAY) AS DIS_COST_MAY,
                              SUM (RKT.DIS_COST_JUN) AS DIS_COST_JUN,
                              0 DIS_COST_JUL,
                              0 DIS_COST_AUG,
                              0 DIS_COST_SEP,
                              0 DIS_COST_OCT,
                              0 DIS_COST_NOV,
                              0 DIS_COST_DEC,
                              MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
                              MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                              MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
                         FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                              LEFT JOIN
                                 TM_ORGANIZATION ORG
                              ON ORG.BA_CODE = RKT.BA_CODE
                        WHERE RKT.DELETE_USER IS NULL
                              AND RKT.MATURITY_STAGE_SMS1 IN
                                       ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                              AND RKT.COST_ELEMENT NOT IN('MATERIAL', 'TRANSPORT')
                              $where
                     GROUP BY RKT.PERIOD_BUDGET,
                              ORG.REGION_CODE,
                              RKT.BA_CODE,
                              RKT.AFD_CODE,
                              RKT.BLOCK_CODE,
                              RKT.COST_ELEMENT,
                              RKT.MATURITY_STAGE_SMS1
                     UNION ALL
                       SELECT RKT.PERIOD_BUDGET,
                              ORG.REGION_CODE,
                              RKT.BA_CODE,
                              RKT.AFD_CODE,
                              RKT.BLOCK_CODE,
                              RKT.COST_ELEMENT,
                              RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
                              0 DIS_COST_JAN,
                              0 DIS_COST_FEB,
                              0 DIS_COST_MAR,
                              0 DIS_COST_APR,
                              0 DIS_COST_MAY,
                              0 DIS_COST_JUN,
                              SUM (RKT.DIS_COST_JUL) DIS_COST_JUL,
                              SUM (RKT.DIS_COST_AUG) DIS_COST_AUG,
                              SUM (RKT.DIS_COST_SEP) DIS_COST_SEP,
                              SUM (RKT.DIS_COST_OCT) DIS_COST_OCT,
                              SUM (RKT.DIS_COST_NOV) DIS_COST_NOV,
                              SUM (RKT.DIS_COST_DEC) DIS_COST_DEC,
                              MAX (RKT.COST_LABOUR_POKOK) AS COST_LABOUR_POKOK,
                              MAX (RKT.COST_TOOLS_KG) AS COST_TOOLS_KG,
                              MAX (RKT.COST_TRANSPORT_KG) AS COST_TRANSPORT_KG
                         FROM    TR_RKT_PUPUK_COST_ELEMENT RKT
                              LEFT JOIN
                                 TM_ORGANIZATION ORG
                              ON ORG.BA_CODE = RKT.BA_CODE
                        WHERE RKT.DELETE_USER IS NULL
                              AND RKT.MATURITY_STAGE_SMS2 IN
                                       ('TBM0', 'TBM1', 'TBM2', 'TBM3')
                              AND RKT.COST_ELEMENT NOT IN('MATERIAL', 'TRANSPORT')
                              $where
                     GROUP BY RKT.PERIOD_BUDGET,
                              ORG.REGION_CODE,
                              RKT.BA_CODE,
                              RKT.AFD_CODE,
                              RKT.BLOCK_CODE,
                              RKT.COST_ELEMENT,
                              RKT.MATURITY_STAGE_SMS2)
           GROUP BY PERIOD_BUDGET,
                    REGION_CODE,
                    BA_CODE,
                    AFD_CODE,
                    BLOCK_CODE,
                    COST_ELEMENT,
                    MATURITY_STAGE) COST
       LEFT JOIN
          V_KG_PUPUK_AFD KG_PUPUK
       ON     COST.PERIOD_BUDGET = KG_PUPUK.PERIOD_BUDGET
          AND COST.BA_CODE = KG_PUPUK.BA_CODE
          AND COST.AFD_CODE = KG_PUPUK.AFD_CODE
          AND COST.MATURITY_STAGE = KG_PUPUK.MATURITY_STAGE
		) 
		UNION ALL
		-- INI UNTUK PERHITUNGAN PUPUK MAJEMUK TRANSPORT
		/* Formatted on 9/7/2015 8:08:37 PM (QP5 v5.136.908.31019) */
	  SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 (COST_JAN / NULLIF (PRICE_QTY_VRA,0)) AS QTY_JAN,
                 (COST_FEB / NULLIF (PRICE_QTY_VRA,0)) AS QTY_FEB,
                 (COST_MAR / NULLIF (PRICE_QTY_VRA,0)) AS QTY_MAR,
                 (COST_APR / NULLIF (PRICE_QTY_VRA,0)) AS QTY_APR,
                 (COST_MAY / NULLIF (PRICE_QTY_VRA,0)) AS QTY_MAY,
                 (COST_JUN / NULLIF (PRICE_QTY_VRA,0)) AS QTY_JUN,
                 0 QTY_JUL,
                 0 QTY_AUG,
                 0 QTY_SEP,
                 0 QTY_OCT,
                 0 QTY_NOV,
                 0 QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020400' AS ACTIVITY_CODE,
                         'PUPUK MAJEMUK' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JAN) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JAN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JAN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_FEB) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_FEB
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_FEB,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAR) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_MAR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_APR) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_APR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_APR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAY) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_MAY
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAY,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUN) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JUN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUN,
                         0 COST_JUL,
                         0 COST_AUG,
                         0 COST_SEP,
                         0 COST_OCT,
                         0 COST_NOV,
                         0 COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS1,
                                 MATERIAL_CODE_JAN,
                                 DIS_JAN,
                                 MATERIAL_CODE_FEB,
                                 DIS_FEB,
                                 MATERIAL_CODE_MAR,
                                 DIS_MAR,
                                 MATERIAL_CODE_APR,
                                 DIS_APR,
                                 MATERIAL_CODE_MAY,
                                 DIS_MAY,
                                 MATERIAL_CODE_JUN,
                                 DIS_JUN
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS1 NOT IN ('TM')
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
										$where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE)
          UNION ALL
          SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 0 QTY_JAN,
                 0 QTY_FEB,
                 0 QTY_MAR,
                 0 QTY_APR,
                 0 QTY_MAY,
                 0 QTY_JUN,
                 (COST_JUL / NULLIF(PRICE_QTY_VRA,0)) AS QTY_JUL,
                 (COST_AUG / NULLIF(PRICE_QTY_VRA,0)) AS QTY_AUG,
                 (COST_SEP / NULLIF(PRICE_QTY_VRA,0)) AS QTY_SEP,
                 (COST_OCT / NULLIF(PRICE_QTY_VRA,0)) AS QTY_OCT,
                 (COST_NOV / NULLIF(PRICE_QTY_VRA,0)) AS QTY_NOV,
                 (COST_DEC / NULLIF(PRICE_QTY_VRA,0)) AS QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020400' AS ACTIVITY_CODE,
                         'PUPUK MAJEMUK' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         0 COST_JAN,
                         0 COST_FEB,
                         0 COST_MAR,
                         0 COST_APR,
                         0 COST_MAY,
                         0 COST_JUN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUL) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_JUL
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUL,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_AUG) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_AUG
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_AUG,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_SEP) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_SEP
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_SEP,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_OCT) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_OCT
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_OCT,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_NOV) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_NOV
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_NOV,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_DEC) =
                                    '5101020400'
                            THEN
                               (RKT.DIS_DEC
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS2,
                                 MATERIAL_CODE_JUL,
                                 DIS_JUL,
                                 MATERIAL_CODE_AUG,
                                 DIS_AUG,
                                 MATERIAL_CODE_SEP,
                                 DIS_SEP,
                                 MATERIAL_CODE_OCT,
                                 DIS_OCT,
                                 MATERIAL_CODE_NOV,
                                 DIS_NOV,
                                 MATERIAL_CODE_DEC,
                                 DIS_DEC
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS2 NOT IN ('TM')
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
										$where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE))
					GROUP BY PERIOD_BUDGET,
							 REGION_CODE,
							 BA_CODE,
							 AFD_CODE,
							 BLOCK_CODE,
							 TIPE_TRANSAKSI,
							 COST_ELEMENT,
							 ACTIVITY_CODE,
							 ACTIVITY_DESC,
							 SUB_COST_ELEMENT,
							 MATERIAL_NAME,
							 UOM
	UNION ALL
		-- INI UNTUK PERHITUNGAN PUPUK TUNGGAL TRANSPORT
		SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 (COST_JAN / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JAN,
                 (COST_FEB / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_FEB,
                 (COST_MAR / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_MAR,
                 (COST_APR / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_APR,
                 (COST_MAY / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_MAY,
                 (COST_JUN / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JUN,
                 0 QTY_JUL,
                 0 QTY_AUG,
                 0 QTY_SEP,
                 0 QTY_OCT,
                 0 QTY_NOV,
                 0 QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020300' AS ACTIVITY_CODE,
                         'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JAN) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JAN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JAN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_FEB) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_FEB
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_FEB,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAR) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_MAR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_APR) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_APR
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_APR,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_MAY) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_MAY
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_MAY,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUN) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JUN
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUN,
                         0 COST_JUL,
                         0 COST_AUG,
                         0 COST_SEP,
                         0 COST_OCT,
                         0 COST_NOV,
                         0 COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS1,
                                 MATERIAL_CODE_JAN,
                                 DIS_JAN,
                                 MATERIAL_CODE_FEB,
                                 DIS_FEB,
                                 MATERIAL_CODE_MAR,
                                 DIS_MAR,
                                 MATERIAL_CODE_APR,
                                 DIS_APR,
                                 MATERIAL_CODE_MAY,
                                 DIS_MAY,
                                 MATERIAL_CODE_JUN,
                                 DIS_JUN
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS1 NOT IN ('TM')
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
									   $where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE)
          UNION ALL
          SELECT PERIOD_BUDGET,
                 REGION_CODE,
                 BA_CODE,
                 AFD_CODE,
                 BLOCK_CODE,
                 TIPE_TRANSAKSI,
                 COST_ELEMENT,
                 ACTIVITY_CODE,
                 ACTIVITY_DESC,
                 SUB_COST_ELEMENT,
                 MATERIAL_NAME,
                 KETERANGAN,
                 UOM,
                 0 QTY_JAN,
                 0 QTY_FEB,
                 0 QTY_MAR,
                 0 QTY_APR,
                 0 QTY_MAY,
                 0 QTY_JUN,
                 (COST_JUL / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_JUL,
                 (COST_AUG / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_AUG,
                 (COST_SEP / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_SEP,
                 (COST_OCT / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_OCT,
                 (COST_NOV / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_NOV,
                 (COST_DEC / NULLIF (PRICE_QTY_VRA, 0)) AS QTY_DEC,
                 COST_JAN,
                 COST_FEB,
                 COST_MAR,
                 COST_APR,
                 COST_MAY,
                 COST_JUN,
                 COST_JUL,
                 COST_AUG,
                 COST_SEP,
                 COST_OCT,
                 COST_NOV,
                 COST_DEC
            FROM (SELECT RKT.PERIOD_BUDGET,
                         ORG.REGION_CODE,
                         RKT.BA_CODE,
                         RKT.AFD_CODE,
                         RKT.BLOCK_CODE,
                         RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
                         'TRANSPORT' AS COST_ELEMENT,
                         '5101020300' AS ACTIVITY_CODE,
                         'PUPUK TUNGGAL' AS ACTIVITY_DESC,
                         VRA.VRA_CODE AS SUB_COST_ELEMENT,
                         TMVRA.VRA_SUB_CAT_DESCRIPTION AS MATERIAL_NAME,
                         '' AS KETERANGAN,
                         TMVRA.UOM,
                         0 COST_JAN,
                         0 COST_FEB,
                         0 COST_MAR,
                         0 COST_APR,
                         0 COST_MAY,
                         0 COST_JUN,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_JUL) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_JUL
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_JUL,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_AUG) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_AUG
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_AUG,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_SEP) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_SEP
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_SEP,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_OCT) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_OCT
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_OCT,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_NOV) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_NOV
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_NOV,
                         CASE
                            WHEN (SELECT COA_CODE
                                    FROM TM_MATERIAL
                                   WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                         AND BA_CODE = RKT.BA_CODE
                                         AND MATERIAL_CODE =
                                               RKT.MATERIAL_CODE_DEC) =
                                    '5101020300'
                            THEN
                               (RKT.DIS_DEC
                                / (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                                     FROM TR_RKT_PUPUK_DISTRIBUSI
                                    WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                          AND BA_CODE = RKT.BA_CODE
                                          AND TIPE_TRANSAKSI = 'KG_NORMAL')
                                * VRA.TOTAL_PRICE)
                            ELSE
                               0
                         END
                            AS COST_DEC,
                         (SELECT SUM (DIS_TOTAL) TOTAL_DIS
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE     PERIOD_BUDGET = RKT.PERIOD_BUDGET
                                 AND BA_CODE = RKT.BA_CODE
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL')
                            AS TOTAL_DIS,
                         VRA.TOTAL_PRICE,
                         VRA.PRICE_QTY_VRA
                    FROM (SELECT PERIOD_BUDGET,
                                 BA_CODE,
                                 AFD_CODE,
                                 BLOCK_CODE,
                                 TIPE_TRANSAKSI,
                                 MATURITY_STAGE_SMS2,
                                 MATERIAL_CODE_JUL,
                                 DIS_JUL,
                                 MATERIAL_CODE_AUG,
                                 DIS_AUG,
                                 MATERIAL_CODE_SEP,
                                 DIS_SEP,
                                 MATERIAL_CODE_OCT,
                                 DIS_OCT,
                                 MATERIAL_CODE_NOV,
                                 DIS_NOV,
                                 MATERIAL_CODE_DEC,
                                 DIS_DEC
                            FROM TR_RKT_PUPUK_DISTRIBUSI
                           WHERE MATURITY_STAGE_SMS2 NOT IN ('TM')
                                 AND TIPE_TRANSAKSI = 'KG_NORMAL'
								 $where2) RKT
                         LEFT JOIN (  SELECT PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA,
                                             SUM (PRICE_HM_KM) TOTAL_PRICE
                                        FROM TR_RKT_VRA_DISTRIBUSI
                                       WHERE ACTIVITY_CODE = '43750'
									   $where2
                                    GROUP BY PERIOD_BUDGET,
                                             BA_CODE,
                                             ACTIVITY_CODE,
                                             VRA_CODE,
                                             PRICE_QTY_VRA) VRA
                            ON RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET
                               AND RKT.BA_CODE = VRA.BA_CODE
                         LEFT JOIN TM_VRA TMVRA
                            ON TMVRA.VRA_CODE = VRA.VRA_CODE
                         LEFT JOIN TM_ORGANIZATION ORG
                            ON ORG.BA_CODE = RKT.BA_CODE))
		GROUP BY PERIOD_BUDGET,
				 REGION_CODE,
				 BA_CODE,
				 AFD_CODE,
				 BLOCK_CODE,
				 TIPE_TRANSAKSI,
				 COST_ELEMENT,
				 ACTIVITY_CODE,
				 ACTIVITY_DESC,
				 SUB_COST_ELEMENT,
				 MATERIAL_NAME,
				 UOM
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (ASTEK)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'ASTEK' AS ACTIVITY_CODE, 
					'ASTEK' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1 
		 $xwhere
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		   $xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
			   $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
						   $twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
		 $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'ASTEK'
						   $twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (BONUS)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'BONUS' AS ACTIVITY_CODE, 
					'BONUS' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		   $xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
			   $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
						   $twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere
		 AND MATURITY_STAGE_SMS2 NOT IN ('TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		   $xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
			   $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'BONUS'
						   $twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1             
		UNION ALL 
		-- INI UNTUK PERHITUNGAN TUNJANGAN (CATU)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'CATU' AS ACTIVITY_CODE, 
					'CATU' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_CATU TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
		 WHERE  1 = 1
				$twhere 
			   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
							TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_CATU TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
					 WHERE   1=1  
						$twhere 
						   AND TCR.TUNJANGAN_TYPE = 'CATU'
						   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_CATU TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
		 WHERE  1 = 1
			$twhere 
			   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
							TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_CATU TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
					 WHERE  1 = 1
							$twhere 
						   AND TCR.TUNJANGAN_TYPE = 'CATU'
						   AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (HHR)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'HHR' AS ACTIVITY_CODE, 
					'HHR' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ('TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
				$twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'HHR'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1
		UNION ALL
		-- INI UNTUK PERHITUGAN TUNJANGAN (JABATAN)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'' AS COST_ELEMENT,
					'' AS ACTIVITY_CODE, 
					'JABATAN' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE  1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
			$twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
			$twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'JABATAN'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (KEHADIRAN)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'KEHADIRAN' AS ACTIVITY_CODE, 
					'KEHADIRAN' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE  1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'KEHADIRAN'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN LAINNYA
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'LAINNYA' AS ACTIVITY_CODE, 
					'LAINNYA' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE  1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE  1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'LAINNYA'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (OBAT)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'OBAT' AS ACTIVITY_CODE, 
					'OBAT' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'OBAT'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (PPH21)
		 SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'PPH_21' AS ACTIVITY_CODE, 
					'PPH_21' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
		 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		 $xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
			 $twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (PPH_21)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'PPH_21' AS ACTIVITY_CODE, 
					'PPH_21' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere 
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
			$twhere 
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
						$twhere 
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere 
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
		   $xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
			   $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'PPH_21'
						   $twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 
		UNION ALL
		-- INI UNTUK PERHITUNGAN TUNJANGAN (THR)
		SELECT PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					'LABOUR' AS COST_ELEMENT,
					'THR' AS ACTIVITY_CODE, 
					'THR' AS ACTIVITY_DESC,
					'' AS SUB_COST_ELEMENT, 
					'' AS MATERIAL_NAME,
					'' KETERANGAN,
					'' UOM,
					SUM(NVL (DIS_JAN,0)) AS DIS_JAN,
					SUM(NVL (DIS_FEB,0)) AS DIS_FEB,
					SUM(NVL (DIS_MAR,0)) AS DIS_MAR,
					SUM(NVL (DIS_APR,0)) AS DIS_APR,
					SUM(NVL (DIS_MAY,0)) AS DIS_MAY,
					SUM(NVL (DIS_JUN,0)) AS DIS_JUN,
					SUM(NVL (DIS_JUL,0)) AS DIS_JUL,
					SUM(NVL (DIS_AUG,0)) AS DIS_AUG,
					SUM(NVL (DIS_SEP,0)) AS DIS_SEP,
					SUM(NVL (DIS_OCT,0)) AS DIS_OCT,
					SUM(NVL (DIS_NOV,0)) AS DIS_NOV,
					SUM(NVL (DIS_DEC,0)) AS DIS_DEC,
					SUM(NVL (COST_JAN,0)) AS COST_JAN,
					SUM(NVL (COST_FEB,0)) AS COST_FEB,
					SUM(NVL (COST_MAR,0)) AS COST_MAR,
					SUM(NVL (COST_APR,0)) AS COST_APR,
					SUM(NVL (COST_MAY,0)) AS COST_MAY,
					SUM(NVL (COST_JUN,0)) AS COST_JUN,
					SUM(NVL (COST_JUL,0)) AS COST_JUL,
					SUM(NVL (COST_AUG,0)) AS COST_AUG,
					SUM(NVL (COST_SEP,0)) AS COST_SEP,
					SUM(NVL (COST_OCT,0)) AS COST_OCT,
					SUM(NVL (COST_NOV,0)) AS COST_NOV,
					SUM(NVL (COST_DEC,0)) AS COST_DEC,
					(SUM(NVL (DIS_JAN,0)) +
					SUM(NVL (DIS_FEB,0)) +
					SUM(NVL (DIS_MAR,0)) +
					SUM(NVL (DIS_APR,0)) +
					SUM(NVL (DIS_MAY,0)) +
					SUM(NVL (DIS_JUN,0)) +
					SUM(NVL (DIS_JUL,0)) +
					SUM(NVL (DIS_AUG,0)) +
					SUM(NVL (DIS_SEP,0)) +
					SUM(NVL (DIS_OCT,0)) +
					SUM(NVL (DIS_NOV,0)) +
					SUM(NVL (DIS_DEC,0))) AS DIS_SETAHUN,
					(SUM(NVL (COST_JAN,0)) +
					SUM(NVL (COST_FEB,0)) +
					SUM(NVL (COST_MAR,0)) +
					SUM(NVL (COST_APR,0)) +
					SUM(NVL (COST_MAY,0)) +
					SUM(NVL (COST_JUN,0)) +
					SUM(NVL (COST_JUL,0)) +
					SUM(NVL (COST_AUG,0)) +
					SUM(NVL (COST_SEP,0)) +
					SUM(NVL (COST_OCT,0)) +
					SUM(NVL (COST_NOV,0)) +
					SUM(NVL (COST_DEC,0))) AS COST_SETAHUN,
					'".$this->_userName."' AS INSERT_USER,
					SYSDATE AS INSERT_TIME
		FROM (            
		--HITUNG TUNJANGAN UNTUK SMS1
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS1,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUN,
					0 AS DIS_JUL,
					0 AS DIS_AUG,
					0 AS DIS_SEP,
					0 AS DIS_OCT,
					0 AS DIS_NOV,
					0 AS DIS_DEC,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JAN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_FEB,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_APR,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_MAY,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUN,
					0 AS COST_JUL,
					0 AS COST_AUG,
					0 AS COST_SEP,
					0 AS COST_OCT,
					0 AS COST_NOV,
					0 AS COST_DEC       
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS1,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere
		 AND MATURITY_STAGE_SMS1 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE  1 = 1
		 $xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
		 $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
						$twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		UNION ALL
		--HITUNG TUNJANGAN UNTUK SMS2
		SELECT 
					HA_TM.PERIOD_BUDGET,
					ORG.REGION_CODE,
					HA_TM.BA_CODE,
					HA_TM.AFD_CODE,
					HA_TM.BLOCK_CODE,
					HA_TM.MATURITY_STAGE_SMS2,
					0 AS DIS_JAN,
					0 AS DIS_FEB,
					0 AS DIS_MAR,
					0 AS DIS_APR,
					0 AS DIS_MAY,
					0 AS DIS_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*ALL_MPP.MPP) AS DIS_DEC,
					0 AS COST_JAN,
					0 AS COST_FEB,
					0 AS COST_MAR,
					0 AS COST_APR,
					0 AS COST_MAY,
					0 AS COST_JUN,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_JUL,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_AUG,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_SEP,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_OCT,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_NOV,
					(HA_TM.HA_PLANTED/HA_BA.HA*TTL_COST.COST_BA) AS COST_DEC
		FROM (
		SELECT PERIOD_BUDGET,
			   BA_CODE,
			   AFD_CODE,
			   BLOCK_CODE,
			   MATURITY_STAGE_SMS2,
			   HA_PLANTED
		  FROM TM_HECTARE_STATEMENT
		 WHERE 1 = 1
		 $xwhere
		 AND MATURITY_STAGE_SMS2 NOT IN ( 'TM')
		) HA_TM
		LEFT JOIN (       
		SELECT PERIOD_BUDGET, BA_CODE, SUM (HA_PLANTED) AS HA
		   FROM TM_HECTARE_STATEMENT
		   WHERE 1 = 1
			$xwhere
		GROUP BY PERIOD_BUDGET, BA_CODE
		) HA_BA
		ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND HA_TM.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN (
		SELECT TTJ.PERIOD_BUDGET, TTJ.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP
		  FROM    TM_TARIF_TUNJANGAN TTJ
			   LEFT JOIN
				  TR_RKT_CHECKROLL TRC
			   ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
				  AND TRC.BA_CODE = TTJ.BA_CODE
				  AND TRC.JOB_CODE = TTJ.JOB_CODE
				  AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
		 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
		 $twhere
		GROUP BY  TTJ.PERIOD_BUDGET, TTJ.BA_CODE 
		) ALL_MPP
		ON ALL_MPP.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
		AND ALL_MPP.BA_CODE = HA_BA.BA_CODE
		LEFT JOIN TM_ORGANIZATION ORG
		ON ORG.BA_CODE = HA_TM.BA_CODE
		LEFT JOIN (
		SELECT PERIOD_BUDGET,
					BA_CODE,
					SUM(COSTTYPE) AS COST_BA
		FROM (            
					SELECT TTJ.PERIOD_BUDGET,
						   TTJ.BA_CODE,
						   TTJ.JOB_CODE,
						   TCR.TUNJANGAN_TYPE,
						   TCR.JUMLAH,
						   TRC.MPP_PERIOD_BUDGET,
						   (TCR.JUMLAH*TRC.MPP_PERIOD_BUDGET/12) COSTTYPE
					  FROM TM_TARIF_TUNJANGAN TTJ
						   LEFT JOIN TR_RKT_CHECKROLL TRC
							  ON     TRC.PERIOD_BUDGET = TTJ.PERIOD_BUDGET
								 AND TRC.BA_CODE = TTJ.BA_CODE
								 AND TRC.JOB_CODE = TTJ.JOB_CODE
								 AND TRC.EMPLOYEE_STATUS = TTJ.EMPLOYEE_STATUS
						   LEFT JOIN TR_RKT_CHECKROLL_DETAIL TCR
							  ON TCR.TRX_CR_CODE = TRC.TRX_CR_CODE
								 AND TCR.TUNJANGAN_TYPE = TTJ.TUNJANGAN_TYPE
					 WHERE     TTJ.TUNJANGAN_TYPE = 'THR'
						$twhere
		) GROUP BY PERIOD_BUDGET, BA_CODE
		) TTL_COST
		ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET
		AND HA_TM.BA_CODE = TTL_COST.BA_CODE
		)
		GROUP BY PERIOD_BUDGET,
					REGION_CODE,
					BA_CODE,
					AFD_CODE,
					BLOCK_CODE,
					MATURITY_STAGE_SMS1
		UNION ALL
		--PERHITUNGAN UNUTUK KEBUTUHAN UMUM
		SELECT PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 '' KETERANGAN,
			 UOM,
			 SUM (NVL (QTY_JAN, 0)) AS QTY_JAN,
			 SUM (NVL (QTY_FEB, 0)) AS QTY_FEB,
			 SUM (NVL (QTY_MAR, 0)) AS QTY_MAR,
			 SUM (NVL (QTY_APR, 0)) AS QTY_APR,
			 SUM (NVL (QTY_MAY, 0)) AS QTY_MAY,
			 SUM (NVL (QTY_JUN, 0)) AS QTY_JUN,
			 SUM (NVL (QTY_JUL, 0)) AS QTY_JUL,
			 SUM (NVL (QTY_AUG, 0)) AS QTY_AUG,
			 SUM (NVL (QTY_SEP, 0)) AS QTY_SEP,
			 SUM (NVL (QTY_OCT, 0)) AS QTY_OCT,
			 SUM (NVL (QTY_NOV, 0)) AS QTY_NOV,
			 SUM (NVL (QTY_DEC, 0)) AS QTY_DEC,
			 SUM (NVL (COST_JAN, 0)) AS COST_JAN,
			 SUM (NVL (COST_FEB, 0)) AS COST_FEB,
			 SUM (NVL (COST_MAR, 0)) AS COST_MAR,
			 SUM (NVL (COST_APR, 0)) AS COST_APR,
			 SUM (NVL (COST_MAY, 0)) AS COST_MAY,
			 SUM (NVL (COST_JUN, 0)) AS COST_JUN,
			 SUM (NVL (COST_JUL, 0)) AS COST_JUL,
			 SUM (NVL (COST_AUG, 0)) AS COST_AUG,
			 SUM (NVL (COST_SEP, 0)) AS COST_SEP,
			 SUM (NVL (COST_OCT, 0)) AS COST_OCT,
			 SUM (NVL (COST_NOV, 0)) AS COST_NOV,
			 SUM (NVL (COST_DEC, 0)) AS COST_DEC,
			 (  SUM (NVL (QTY_JAN, 0))
			  + SUM (NVL (QTY_FEB, 0))
			  + SUM (NVL (QTY_MAR, 0))
			  + SUM (NVL (QTY_APR, 0))
			  + SUM (NVL (QTY_MAY, 0))
			  + SUM (NVL (QTY_JUN, 0))
			  + SUM (NVL (QTY_JUL, 0))
			  + SUM (NVL (QTY_AUG, 0))
			  + SUM (NVL (QTY_SEP, 0))
			  + SUM (NVL (QTY_OCT, 0))
			  + SUM (NVL (QTY_NOV, 0))
			  + SUM (NVL (QTY_DEC, 0)))
				AS QTY_SETAHUN,
			 (  SUM (NVL (COST_JAN, 0))
			  + SUM (NVL (COST_FEB, 0))
			  + SUM (NVL (COST_MAR, 0))
			  + SUM (NVL (COST_APR, 0))
			  + SUM (NVL (COST_MAY, 0))
			  + SUM (NVL (COST_JUN, 0))
			  + SUM (NVL (COST_JUL, 0))
			  + SUM (NVL (COST_AUG, 0))
			  + SUM (NVL (COST_SEP, 0))
			  + SUM (NVL (COST_OCT, 0))
			  + SUM (NVL (COST_NOV, 0))
			  + SUM (NVL (COST_DEC, 0)))
				AS COST_SETAHUN,
			 '".$this->_userName."' AS INSERT_USER,
			 SYSDATE AS INSERT_TIME
		FROM (SELECT RKT.PERIOD_BUDGET,
					 OPEX.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
					 '' AS COST_ELEMENT,
					 OPEX.COA_CODE AS ACTIVITY_CODE,
					 COA.DESCRIPTION AS ACTIVITY_DESC,
					 '' AS SUB_COST_ELEMENT,
					 '' AS MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'HA' AS UOM,
					 0 QTY_JAN,
					 0 QTY_FEB,
					 0 QTY_MAR,
					 0 QTY_APR,
					 0 QTY_MAY,
					 0 QTY_JUN,
					 0 QTY_JUL,
					 0 QTY_AUG,
					 0 QTY_SEP,
					 0 QTY_OCT,
					 0 QTY_NOV,
					 0 QTY_DEC,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JAN)
						AS COST_JAN,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_FEB)
						AS COST_FEB,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_MAR)
						AS COST_MAR,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_APR)
						AS COST_APR,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_MAY)
						AS COST_MAY,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM1
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS1 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JUN)
						AS COST_JUN,
					 0 COST_JUL,
					 0 COST_AUG,
					 0 COST_SEP,
					 0 COST_OCT,
					 0 COST_NOV,
					 0 COST_DEC
				FROM TM_HECTARE_STATEMENT RKT
					 LEFT JOIN TR_RKT_OPEX OPEX
						ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						   AND OPEX.BA_CODE = RKT.BA_CODE
					 LEFT JOIN TM_COA COA
						ON COA.COA_CODE = OPEX.COA_CODE
			   WHERE     OPEX.COA_CODE NOT IN ('1212010101', '5101030504')
					 AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0','TBM1','TBM2','TBM3')
					 $where3
			  UNION ALL
			  SELECT RKT.PERIOD_BUDGET,
					 OPEX.REGION_CODE,
					 RKT.BA_CODE,
					 RKT.AFD_CODE,
					 RKT.BLOCK_CODE,
					 RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
					 '' AS COST_ELEMENT,
					 OPEX.COA_CODE AS ACTIVITY_CODE,
					 COA.DESCRIPTION AS ACTIVITY_DESC,
					 '' AS SUB_COST_ELEMENT,
					 '' AS MATERIAL_NAME,
					 '' AS KETERANGAN,
					 'HA' AS UOM,
					 0 QTY_JAN,
					 0 QTY_FEB,
					 0 QTY_MAR,
					 0 QTY_APR,
					 0 QTY_MAY,
					 0 QTY_JUN,
					 0 QTY_JUL,
					 0 QTY_AUG,
					 0 QTY_SEP,
					 0 QTY_OCT,
					 0 QTY_NOV,
					 0 QTY_DEC,
					 0 COST_JAN,
					 0 COST_FEB,
					 0 COST_MAR,
					 0 COST_APR,
					 0 COST_MAY,
					 0 COST_JUN,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_JUL)
						AS COST_JUL,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_AUG)
						AS COST_AUG,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_SEP)
						AS COST_SEP,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_OCT)
						AS COST_OCT,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_NOV)
						AS COST_NOV,
					 (RKT.HA_PLANTED
					  / (SELECT SUM (HA_PLANTED) TOTAL_SM2
						   FROM TM_HECTARE_STATEMENT
						  WHERE PERIOD_BUDGET = RKT.PERIOD_BUDGET
								AND BA_CODE = RKT.BA_CODE
								AND MATURITY_STAGE_SMS2 IN
										 ('TBM0', 'TBM1', 'TBM2', 'TBM3', 'TM'))
					  * OPEX.DIS_DEC)
						AS COST_DEC
				FROM TM_HECTARE_STATEMENT RKT
					 LEFT JOIN TR_RKT_OPEX OPEX
						ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET
						   AND OPEX.BA_CODE = RKT.BA_CODE
					 LEFT JOIN TM_COA COA
						ON COA.COA_CODE = OPEX.COA_CODE
			   WHERE     OPEX.COA_CODE NOT IN ('1212010101', '5101030504')
					 AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0','TBM1','TBM2','TBM3')
					 $where3
					 )
	GROUP BY PERIOD_BUDGET,
			 REGION_CODE,
			 BA_CODE,
			 AFD_CODE,
			 BLOCK_CODE,
			 TIPE_TRANSAKSI,
			 COST_ELEMENT,
			 ACTIVITY_CODE,
			 ACTIVITY_DESC,
			 SUB_COST_ELEMENT,
			 MATERIAL_NAME,
			 UOM
		  ";
		
		$this->_db->query($xquery);
		$this->_db->commit();
		
		return true;		 
	}		
	
	//hapus temp table untuk kebutuhan activity estate cost
	public function delTmpRptKebActDevCostBlock($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		//hapus estate cost per BLOCK
		$query = "
			DELETE FROM TMP_RPT_KEB_DEV_COST_BLOCK 
			WHERE 1 = 1
			$where 
		";
		
		$this->_db->query($query);
		$this->_db->commit();
		
		return true;
	}
	
	
	//get last generate date
    public function getLastGenerate($params = array())
    {
		$where = "";
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
	
	
		$query = "
			SELECT 	MAX(INSERT_USER) INSERT_USER,
					TO_CHAR( MAX(INSERT_TIME), 'DD-MM-RRRR HH24:MI:SS') INSERT_TIME
			FROM (
				SELECT 	MAX(INSERT_USER) INSERT_USER,
						MAX(INSERT_TIME) INSERT_TIME
				FROM TMP_RPT_KEB_DEV_COST_BLOCK
				WHERE 1 = 1
				$where
				UNION ALL
				SELECT 	MAX(INSERT_USER) INSERT_USER,
						MAX(INSERT_TIME) INSERT_TIME
				FROM TMP_RPT_KEB_EST_COST_BLOCK
				WHERE 1 = 1
				$where
			)
		";
		
		$result = $this->_db->fetchRow("{$query}");
		
		return $result;
	}
	
	//generate report report kebutuhan aktivitas per BA --aries 16.06.2015
    public function reportKebAktivitasPerBa($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel estate cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$result['PERIOD'] = $params['budgetperiod'];
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$result['PERIOD'] = $this->_period;
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME
			FROM (
				SELECT 	CASE
							WHEN INSTR(HIRARKI, '/',1, 2) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 2)+1, INSTR(HIRARKI, '/',1, 2) - 2) 
							ELSE NULL
						END GROUP01,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 3) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 3)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP02,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 4) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 4)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP03,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 5) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 5)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP04,
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN ( SELECT *
                              FROM (   
							  SELECT PERIOD_BUDGET,
							 REGION_CODE,
							 BA_CODE,
							 ACTIVITY_DESC,
							 TIPE_TRANSAKSI,
							 ACTIVITY_CODE,
							 SUB_COST_ELEMENT_DESC,
							 COST_ELEMENT,
							 SUB_COST_ELEMENT,
							 KETERANGAN,
							 UOM,
							 SUM (QTY_JAN) QTY_JAN,
							 SUM (QTY_FEB) QTY_FEB,
							 SUM (QTY_MAR) QTY_MAR,
							 SUM (QTY_APR) QTY_APR,
							 SUM (QTY_MAY) QTY_MAY,
							 SUM (QTY_JUN) QTY_JUN,
							 SUM (QTY_JUL) QTY_JUL,
							 SUM (QTY_AUG) QTY_AUG,
							 SUM (QTY_SEP) QTY_SEP,
							 SUM (QTY_OCT) QTY_OCT,
							 SUM (QTY_NOV) QTY_NOV,
							 SUM (QTY_DEC) QTY_DEC,
							 SUM (COST_JAN) COST_JAN,
							 SUM (COST_FEB) COST_FEB,
							 SUM (COST_MAR) COST_MAR,
							 SUM (COST_APR) COST_APR,
							 SUM (COST_MAY) COST_MAY,
							 SUM (COST_JUN) COST_JUN,
							 SUM (COST_JUL) COST_JUL,
							 SUM (COST_AUG) COST_AUG,
							 SUM (COST_SEP) COST_SEP,
							 SUM (COST_OCT) COST_OCT,
							 SUM (COST_NOV) COST_NOV,
							 SUM (COST_DEC) COST_DEC,
							 SUM (QTY_SETAHUN) QTY_SETAHUN,
							 SUM (COST_SETAHUN) COST_SETAHUN
						FROM TMP_RPT_KEB_EST_COST_BLOCK REPORT
					   GROUP BY PERIOD_BUDGET,
						 REGION_CODE,
						 BA_CODE,
						 ACTIVITY_DESC,
						 TIPE_TRANSAKSI,
						 ACTIVITY_CODE,
						 SUB_COST_ELEMENT_DESC,
						 COST_ELEMENT,
						 SUB_COST_ELEMENT,
						 KETERANGAN,
						 UOM) ALL_ACT
					WHERE 1 = 1
					   $where		
				) REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				   AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				   AND NVL (MAPP.COST_ELEMENT, 'NA') =
						 NVL (REPORT.COST_ELEMENT, 'NA')
				LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = REPORT.BA_CODE
				  WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
			";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel kebutuhan aktivitas estate cost (BA) ################################################### */
		
		return $result;
	}
	
	//generate report report kebutuhan aktivitas per AFD --aries 16.06.2015
    public function reportKebAktivitasPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel estate cost AFD ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			
			$where1 .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			
			$result['PERIOD'] = $params['budgetperiod'];
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			
			$where1 .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			
			$result['PERIOD'] = $this->_period;
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
			
			$where1 .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
			
			$where1 .= "
                AND ALL_ACT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME
			FROM (
				SELECT 	CASE
							WHEN INSTR(HIRARKI, '/',1, 2) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 2)+1, INSTR(HIRARKI, '/',1, 2) - 2) 
							ELSE NULL
						END GROUP01,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 3) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 3)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP02,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 4) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 4)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP03,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 5) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 5)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP04,
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '02.00.00.00.00' -- estate cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM (  SELECT PERIOD_BUDGET,
								 REGION_CODE,
								 BA_CODE,
								 AFD_CODE,
								 ACTIVITY_DESC,
								 TIPE_TRANSAKSI,
								 ACTIVITY_CODE,
								 SUB_COST_ELEMENT_DESC,
								 COST_ELEMENT,
								 SUB_COST_ELEMENT,
								 KETERANGAN,
								 UOM,
								 SUM (QTY_JAN) QTY_JAN,
								 SUM (QTY_FEB) QTY_FEB,
								 SUM (QTY_MAR) QTY_MAR,
								 SUM (QTY_APR) QTY_APR,
								 SUM (QTY_MAY) QTY_MAY,
								 SUM (QTY_JUN) QTY_JUN,
								 SUM (QTY_JUL) QTY_JUL,
								 SUM (QTY_AUG) QTY_AUG,
								 SUM (QTY_SEP) QTY_SEP,
								 SUM (QTY_OCT) QTY_OCT,
								 SUM (QTY_NOV) QTY_NOV,
								 SUM (QTY_DEC) QTY_DEC,
								 SUM (COST_JAN) COST_JAN,
								 SUM (COST_FEB) COST_FEB,
								 SUM (COST_MAR) COST_MAR,
								 SUM (COST_APR) COST_APR,
								 SUM (COST_MAY) COST_MAY,
								 SUM (COST_JUN) COST_JUN,
								 SUM (COST_JUL) COST_JUL,
								 SUM (COST_AUG) COST_AUG,
								 SUM (COST_SEP) COST_SEP,
								 SUM (COST_OCT) COST_OCT,
								 SUM (COST_NOV) COST_NOV,
								 SUM (COST_DEC) COST_DEC,
								 SUM (QTY_SETAHUN) QTY_SETAHUN,
								 SUM (COST_SETAHUN) COST_SETAHUN
							FROM TMP_RPT_KEB_EST_COST_BLOCK REPORT
							GROUP BY PERIOD_BUDGET,
								 REGION_CODE,
								 BA_CODE,
								 AFD_CODE,
								 ACTIVITY_DESC,
								 TIPE_TRANSAKSI,
								 ACTIVITY_CODE,
								 SUB_COST_ELEMENT_DESC,
								 COST_ELEMENT,
								 SUB_COST_ELEMENT,
								 KETERANGAN,
								 UOM
						) ALL_ACT
							WHERE 1 = 1 
							$where1
						)REPORT
							ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
							AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
							AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
						LEFT JOIN TM_ORGANIZATION ORG
							ON ORG.BA_CODE = REPORT.BA_CODE
						WHERE REPORT.ACTIVITY_CODE IS NOT NULL
						ORDER BY REPORT.PERIOD_BUDGET,
								 REPORT.BA_CODE,
								 REPORT.AFD_CODE,
								 $order_group
								 REPORT.ACTIVITY_CODE,
								 REPORT.COST_ELEMENT,
								 REPORT.KETERANGAN
					";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel kebutuhan aktivitas estate cost (BA) ################################################### */
		
		return $result;
	}
	
	//report kebutuhan aktivitas dev cost per BA
	public function reportKebAktivitasDevPerBa($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel development cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			$result['PERIOD'] = $params['budgetperiod'];
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			$result['PERIOD'] = $this->_period;
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME
			FROM (
				SELECT 	CASE
							WHEN INSTR(HIRARKI, '/',1, 2) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 2)+1, INSTR(HIRARKI, '/',1, 2) - 2) 
							ELSE NULL
						END GROUP01,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 3) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 3)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP02,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 4) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 4)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP03,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 5) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 5)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP04,
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN ( SELECT *
                              FROM (              
							 SELECT PERIOD_BUDGET,
							 REGION_CODE,
							 BA_CODE,
							 ACTIVITY_DESC,
							 TIPE_TRANSAKSI,
							 ACTIVITY_CODE,
							 SUB_COST_ELEMENT_DESC,
							 COST_ELEMENT,
							 SUB_COST_ELEMENT,
							 KETERANGAN,
							 UOM,
							 SUM (QTY_JAN) QTY_JAN,
							 SUM (QTY_FEB) QTY_FEB,
							 SUM (QTY_MAR) QTY_MAR,
							 SUM (QTY_APR) QTY_APR,
							 SUM (QTY_MAY) QTY_MAY,
							 SUM (QTY_JUN) QTY_JUN,
							 SUM (QTY_JUL) QTY_JUL,
							 SUM (QTY_AUG) QTY_AUG,
							 SUM (QTY_SEP) QTY_SEP,
							 SUM (QTY_OCT) QTY_OCT,
							 SUM (QTY_NOV) QTY_NOV,
							 SUM (QTY_DEC) QTY_DEC,
							 SUM (COST_JAN) COST_JAN,
							 SUM (COST_FEB) COST_FEB,
							 SUM (COST_MAR) COST_MAR,
							 SUM (COST_APR) COST_APR,
							 SUM (COST_MAY) COST_MAY,
							 SUM (COST_JUN) COST_JUN,
							 SUM (COST_JUL) COST_JUL,
							 SUM (COST_AUG) COST_AUG,
							 SUM (COST_SEP) COST_SEP,
							 SUM (COST_OCT) COST_OCT,
							 SUM (COST_NOV) COST_NOV,
							 SUM (COST_DEC) COST_DEC,
							 SUM (QTY_SETAHUN) QTY_SETAHUN,
							 SUM (COST_SETAHUN) COST_SETAHUN
						FROM TMP_RPT_KEB_DEV_COST_BLOCK REPORT					   
				GROUP BY PERIOD_BUDGET,
						 REGION_CODE,
						 BA_CODE,
						 ACTIVITY_DESC,
						 TIPE_TRANSAKSI,
						 ACTIVITY_CODE,
						 SUB_COST_ELEMENT_DESC,
						 COST_ELEMENT,
						 SUB_COST_ELEMENT,
						 KETERANGAN,
						 UOM) ALL_ACT
					WHERE 1 = 1
					   $where		
				) REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				   AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				   AND NVL (MAPP.COST_ELEMENT, 'NA') =
						 NVL (REPORT.COST_ELEMENT, 'NA')
				LEFT JOIN TM_ORGANIZATION ORG
				  ON ORG.BA_CODE = REPORT.BA_CODE
				  WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
			";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}
		/* ################################################### generate excel kebutuhan aktivitas estate cost (BA) ################################################### */
		
		return $result;
	}
	
	//generate report report kebutuhan aktivitas DEV Cost per AFD --aries 16.06.2015
    public function reportKebAktivitasDevPerAfd($params = array())
    {
		$where = $select_group = $order_group = "";
		$params['uniq_code'] = $this->_global->genFileName();
		
		/* ################################################### generate excel development cost ################################################### */
		//cari jumlah group report
		$query = "
			SELECT MAX(LVL) - 1
			FROM (
				SELECT 	GROUP_CODE, 
					CONNECT_BY_ISCYCLE \"CYCLE\",
					LEVEL as LVL, 
					SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
			FROM TM_RPT_MAPPING_ACT
			WHERE level > 1
			START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
			CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
			)
		";
		$result['max_group'] = $this->_db->fetchOne($query);

		for ($i = 1 ; $i <= $result['max_group'] ; $i++){
			$select_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
				(SELECT DESCRIPTION FROM TM_RPT_GROUP WHERE GROUP_CODE = STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).") AS GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."_DESC,
			";
			$order_group .= "
				STRUKTUR_REPORT.GROUP".str_pad($i,2,'0',STR_PAD_LEFT).",
			";
		}
		
		//filter periode buget
		if($params['budgetperiod'] != ''){
			$where .= "
                AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			
			$where1 .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
			
			$result['PERIOD'] = $params['budgetperiod'];
		}else{
			$where .= "
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			
			$where1 .= "
                AND to_char(ALL_ACT.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
			
			$result['PERIOD'] = $this->_period;
		}
		
		//filter region
		if ($params['src_region_code'] != '') {
			$where .= "
                AND REGION_CODE = '".$params['src_region_code']."'
            ";
			
			$where1 .= "
                AND ALL_ACT.REGION_CODE = '".$params['src_region_code']."'
            ";
        }
		
		//filter BA
		if ($params['key_find'] != '') {
			$where .= "
                AND BA_CODE = '".$params['key_find']."'
            ";
			
			$where1 .= "
                AND ALL_ACT.BA_CODE = '".$params['key_find']."'
            ";
        }
		
		$query = "
			SELECT 	$select_group
					REPORT.*,
					ORG.ESTATE_NAME
			FROM (
				SELECT 	CASE
							WHEN INSTR(HIRARKI, '/',1, 2) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 2)+1, INSTR(HIRARKI, '/',1, 2) - 2) 
							ELSE NULL
						END GROUP01,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 3) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 3)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP02,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 4) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 4)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP03,
						CASE
							WHEN INSTR(HIRARKI, '/',1, 5) <> 0
							THEN SUBSTR(HIRARKI,  INSTR(HIRARKI, '/',1, 5)+1, INSTR(HIRARKI, '/',1, 2) - 2)
							ELSE NULL
						END GROUP04,
						GROUP_CODE
				FROM (
					SELECT 	TO_CHAR(HIRARKI)  AS HIRARKI, 
							LVL, 
							TO_CHAR(GROUP_CODE) AS GROUP_CODE
					FROM (
						SELECT 	GROUP_CODE, 
								CONNECT_BY_ISCYCLE \"CYCLE\",
								LEVEL as LVL, 
								SYS_CONNECT_BY_PATH(GROUP_CODE, '/') \"HIRARKI\"
						FROM TM_RPT_MAPPING_ACT
						WHERE level > 1
						START WITH GROUP_CODE = '01.00.00.00.00' -- development cost
						CONNECT BY NOCYCLE PRIOR GROUP_CODE = PARENT_CODE
					)
					GROUP BY HIRARKI, LVL, GROUP_CODE
					ORDER BY HIRARKI
				)
			) STRUKTUR_REPORT
			LEFT JOIN TM_RPT_MAPPING_ACT MAPP
				ON STRUKTUR_REPORT.GROUP_CODE = MAPP.GROUP_CODE
			LEFT JOIN (
				SELECT *
				FROM (
					SELECT 		PERIOD_BUDGET,
								 REGION_CODE,
								 BA_CODE,
								 AFD_CODE,
								 ACTIVITY_DESC,
								 TIPE_TRANSAKSI,
								 ACTIVITY_CODE,
								 SUB_COST_ELEMENT_DESC,
								 COST_ELEMENT,
								 SUB_COST_ELEMENT,
								 KETERANGAN,
								 UOM,
								 SUM (QTY_JAN) QTY_JAN,
								 SUM (QTY_FEB) QTY_FEB,
								 SUM (QTY_MAR) QTY_MAR,
								 SUM (QTY_APR) QTY_APR,
								 SUM (QTY_MAY) QTY_MAY,
								 SUM (QTY_JUN) QTY_JUN,
								 SUM (QTY_JUL) QTY_JUL,
								 SUM (QTY_AUG) QTY_AUG,
								 SUM (QTY_SEP) QTY_SEP,
								 SUM (QTY_OCT) QTY_OCT,
								 SUM (QTY_NOV) QTY_NOV,
								 SUM (QTY_DEC) QTY_DEC,
								 SUM (COST_JAN) COST_JAN,
								 SUM (COST_FEB) COST_FEB,
								 SUM (COST_MAR) COST_MAR,
								 SUM (COST_APR) COST_APR,
								 SUM (COST_MAY) COST_MAY,
								 SUM (COST_JUN) COST_JUN,
								 SUM (COST_JUL) COST_JUL,
								 SUM (COST_AUG) COST_AUG,
								 SUM (COST_SEP) COST_SEP,
								 SUM (COST_OCT) COST_OCT,
								 SUM (COST_NOV) COST_NOV,
								 SUM (COST_DEC) COST_DEC,
								 SUM (QTY_SETAHUN) QTY_SETAHUN,
								 SUM (COST_SETAHUN) COST_SETAHUN
							FROM TMP_RPT_KEB_DEV_COST_BLOCK REPORT
						GROUP BY PERIOD_BUDGET,
								 REGION_CODE,
								 BA_CODE,
								 AFD_CODE,
								 ACTIVITY_DESC,
								 TIPE_TRANSAKSI,
								 ACTIVITY_CODE,
								 SUB_COST_ELEMENT_DESC,
								 COST_ELEMENT,
								 SUB_COST_ELEMENT,
								 KETERANGAN,
								 UOM
				) ALL_ACT
				WHERE 1 = 1 
				$where1
			)REPORT
				ON MAPP.MATURITY_STAGE = REPORT.TIPE_TRANSAKSI
				AND MAPP.ACTIVITY_CODE = REPORT.ACTIVITY_CODE
				AND NVL(MAPP.COST_ELEMENT, 'NA') = NVL(REPORT.COST_ELEMENT, 'NA')
			LEFT JOIN TM_ORGANIZATION ORG
				ON ORG.BA_CODE = REPORT.BA_CODE
			WHERE REPORT.ACTIVITY_CODE IS NOT NULL
			ORDER BY REPORT.PERIOD_BUDGET,
					 REPORT.BA_CODE,
					 REPORT.AFD_CODE,
					 $order_group
					 REPORT.ACTIVITY_CODE,
					 REPORT.COST_ELEMENT,
					 REPORT.KETERANGAN
		";

		$sql = "SELECT COUNT(*) FROM ({$query})";
		$result['count'] = $this->_db->fetchOne($sql);
		
		$rows = $this->_db->fetchAll("{$query}");
			
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				$result['rows'][] = $row;
			}
		}

		return $result;
	}
	

	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
	//inisialisasi list yang akan ditampilkan
	public function initList($params = array())
    {
		$result = array();
        $initAction = str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));
		$result = $this->$initAction($params);
        return $result;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}

