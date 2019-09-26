<?php
/*
=========================================================================================================================
Project       :   Budgeting & Planning System
Versi         :   2.0.0
Deskripsi     :   Model Class untuk Kebutuhan Aktivitas Report
Function      : - getInput                      : YIR 20/06/2014  : setting input untuk region
                - getLastGenerate               : SID 12/08/2014  : get last generate date
                - tmpRptKebActEstCostBlock      : NBU 18/09/2015  : query summary Keb Act Est Cost Block
                - tmpRptKebActDevCostBlock      : NBU 18/09/2015  : query summary Keb Act Dev Cost Block
                - reportKebAktivitasPerBa       : NBU 18/09/2015  : generate Keb Akt estate cost per BA
                - reportKebAktivitasPerAfd      : NBU 18/09/2015  : generate Keb Akt estate cost per AFD
                - reportKebAktivitasDevPerBa    : NBU 18/09/2015  : generate Keb Akt development cost per BA
                - reportKebAktivitasDevPerAfd   : NBU 18/09/2015  : generate Keb Akt development cost per AFD
Disusun Oleh  :   IT Enterprise Solution - PT Triputra Agro Persada
Developer     :   Nicholas Budihardja
Dibuat Tanggal    :   18/09/2015
Update Terakhir   : 18/09/2015
Revisi        : 

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
    $ba_code = ""; $period_budget = "";

    //filter periode buget
    if($params['budgetperiod'] != ''){
      $period_budget = $params['budgetperiod'];
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
      $cwhere .= "
                AND to_char(CR.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
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
      $cwhere .= "
                AND to_char(CR.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
      $ba_code = $params['key_find'];
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
      $cwhere .= "
                AND CR.BA_CODE = '".$params['key_find']."'
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
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_JAN * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_JAN * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_JAN,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_FEB * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_FEB * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_FEB,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_MAR * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_MAR * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_MAR,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_APR * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_APR * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_APR,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_MAY * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_MAY * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_MAY,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_JUN * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE
                      (RKT.PLAN_JUN * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE
                   END AS QTY_JUN,
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
                     -- AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
                     AND RKT.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
                     AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
                     AND BIAYA.LAND_TYPE IN ('ALL', TM_HS.LAND_TYPE)
                     AND BIAYA.TOPOGRAPHY IN ('ALL', TM_HS.TOPOGRAPHY)
               LEFT JOIN TM_MATERIAL TM_MAT
                  ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                     AND TM_MAT.BA_CODE = BIAYA.BA_CODE
                     AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
              LEFT JOIN TR_RKT_CHECKROLL_SUM RATE_HK ON RATE_HK.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                   AND RATE_HK.BA_CODE = RKT.BA_CODE AND RATE_HK.JOB_CODE = BIAYA.SUB_COST_ELEMENT
         WHERE     RKT.DELETE_USER IS NULL
               AND RKT_INDUK.FLAG_TEMP IS NULL
               AND RKT.MATURITY_STAGE_SMS1 = 'TM'
               AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
               AND RKT_INDUK.SUMBER_BIAYA = 'INTERNAL'
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
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_JUL * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_JUL * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY_JUL,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_AUG * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_AUG * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY_AUG,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_SEP * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_SEP * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY_SEP,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_OCT * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_OCT * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY__OCT,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_NOV * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_NOV * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY_NOV,
                   CASE WHEN RKT.COST_ELEMENT = 'LABOUR' THEN
                      (RKT.PLAN_DEC * BIAYA.PRICE_ROTASI)/RATE_HK.RP_HK
                   ELSE (RKT.PLAN_DEC * BIAYA.PRICE_ROTASI)/TM_MAT.PRICE END AS QTY_DEC,
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
                     -- AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
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
              LEFT JOIN TR_RKT_CHECKROLL_SUM RATE_HK ON RATE_HK.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                   AND RATE_HK.BA_CODE = RKT.BA_CODE AND RATE_HK.JOB_CODE = BIAYA.SUB_COST_ELEMENT
              WHERE RKT.DELETE_USER IS NULL
               AND RKT_INDUK.FLAG_TEMP IS NULL
               AND RKT.MATURITY_STAGE_SMS2 = 'TM'
               AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
               AND RKT_INDUK.SUMBER_BIAYA = 'INTERNAL'
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
         CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JAN,    
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_FEB,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_MAR,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_APR,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_MAY,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JUN,   
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
         CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JUL,  
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_AUG,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_SEP,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_OCT,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_NOV,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_DEC,
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
                                           ACT.UOM
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
    UNION ALL 
    --PERHITUNGAN UNTUK PANEN LABOUR     
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
          '5101030101' AS ACTIVITY_CODE,
          'BIAYA PEMANEN' AS ACTIVITY_DETAIL,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          '' AS UOM,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_JAN,
          (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_FEB,
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_MAR,
          (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_APR,
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_MAY,
          (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_JUN,
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_JUL,
          (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_AUG,
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_SEP,
          (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_OCT,
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_NOV,
          (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK AS QTY_DEC,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JAN,
          (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_FEB,
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAR,
          (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_APR,
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_MAY,
          (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUN,
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_JUL,
          (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_AUG,
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_SEP,
          (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_OCT,
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_NOV,
          (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_BASIS) AS COST_DEC,
          ((SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+
          ((SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+
          ((SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+
          ((SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+
          ((SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+
          ((SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK)+((SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_BASIS)/CHK.RP_HK),
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_BASIS)+
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_BASIS)+
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_BASIS)+
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_BASIS)+
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_BASIS)+
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_BASIS)+(SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_BASIS),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE
                  AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY
                  norma.PERIOD_BUDGET,
                  norma.BA_CODE,
                  norma.AFD_CODE,
                  norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN TR_RKT_CHECKROLL_SUM CHK ON CHK.PERIOD_BUDGET = RKT.PERIOD_BUDGET
            AND CHK.BA_CODE = RKT.BA_CODE AND CHK.JOB_CODE = 'FW040'
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where
    UNION ALL
    --INI BUAT PREMI PANEN JANJANG
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
          '5101030201' AS ACTIVITY_CODE,
          'PREMI PANEN JANJANG' AS ACTIVITY_DETAIL,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          '' AS UOM,
          0 AS QTY_JAN,
          0 AS QTY_FEB,
          0 AS QTY_MAR,
          0 AS QTY_APR,
          0 AS QTY_MAY,
          0 AS QTY_JUN,
          0 AS QTY_JUL,
          0 AS QTY_AUG,
          0 AS QTY_SEP,
          0 AS QTY_OCT,
          0 AS QTY_NOV,
          0 AS QTY_DEC,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_JAN,
          (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_FEB,
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_MAR,
          (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_APR,
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_MAY,
          (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_JUN,
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_JUL,
          (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_AUG,
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_SEP,
          (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_OCT,
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_NOV,
          (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG) AS COST_DEC,
          0,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG)+(SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_JANJANG),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY
                  norma.PERIOD_BUDGET,
                  norma.BA_CODE,
                  norma.AFD_CODE,
                  norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where
    UNION ALL
    --INI BUAT PREMI INSENTIf PANEN JANJANG -- 2017-09-03 INSENTIF PANEN 2018
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
          '5101030201-1' AS ACTIVITY_CODE,
          'PREMI PANEN INCENTIVE' AS ACTIVITY_DETAIL,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          '' AS UOM,
          0 AS QTY_JAN,
          0 AS QTY_FEB,
          0 AS QTY_MAR,
          0 AS QTY_APR,
          0 AS QTY_MAY,
          0 AS QTY_JUN,
          0 AS QTY_JUL,
          0 AS QTY_AUG,
          0 AS QTY_SEP,
          0 AS QTY_OCT,
          0 AS QTY_NOV,
          0 AS QTY_DEC,
          (SEBARAN.JAN * RKT.INCENTIVE) AS COST_JAN,
          (SEBARAN.FEB * RKT.INCENTIVE) AS COST_FEB,
          (SEBARAN.MAR * RKT.INCENTIVE) AS COST_MAR,
          (SEBARAN.APR * RKT.INCENTIVE) AS COST_APR,
          (SEBARAN.MAY * RKT.INCENTIVE) AS COST_MAY,
          (SEBARAN.JUN * RKT.INCENTIVE) AS COST_JUN,
          (SEBARAN.JUL * RKT.INCENTIVE) AS COST_JUL,
          (SEBARAN.AUG * RKT.INCENTIVE) AS COST_AUG,
          (SEBARAN.SEP * RKT.INCENTIVE) AS COST_SEP,
          (SEBARAN.OCT * RKT.INCENTIVE) AS COST_OCT,
          (SEBARAN.NOV * RKT.INCENTIVE) AS COST_NOV,
          (SEBARAN.DEC * RKT.INCENTIVE) AS COST_DEC,
          0,
          (SEBARAN.JAN * RKT.INCENTIVE)+(SEBARAN.FEB * RKT.INCENTIVE)+
          (SEBARAN.MAR * RKT.INCENTIVE)+(SEBARAN.APR * RKT.INCENTIVE)+
          (SEBARAN.MAY * RKT.INCENTIVE)+(SEBARAN.JUN * RKT.INCENTIVE)+
          (SEBARAN.JUL * RKT.INCENTIVE)+(SEBARAN.AUG * RKT.INCENTIVE)+
          (SEBARAN.SEP * RKT.INCENTIVE)+(SEBARAN.OCT * RKT.INCENTIVE)+
          (SEBARAN.NOV * RKT.INCENTIVE)+(SEBARAN.DEC * RKT.INCENTIVE),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE
                  AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY
                  norma.PERIOD_BUDGET,
                  norma.BA_CODE,
                  norma.AFD_CODE,
                  norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where
    UNION ALL
    -- INI UNTUK PREMI PANEN BRD
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
          '5101030301' AS ACTIVITY_CODE,
          'PREMI PANEN BRD' AS ACTIVITY_DETAIL,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          '' AS UOM,
          0 AS QTY_JAN,
          0 AS QTY_FEB,
          0 AS QTY_MAR,
          0 AS QTY_APR,
          0 AS QTY_MAY,
          0 AS QTY_JUN,
          0 AS QTY_JUL,
          0 AS QTY_AUG,
          0 AS QTY_SEP,
          0 AS QTY_OCT,
          0 AS QTY_NOV,
          0 AS QTY_DEC,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JAN,
          (SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_FEB,
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAR,
          (SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_APR,
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_MAY,
          (SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUN,
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_JUL,
          (SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_AUG,
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_SEP,
          (SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_OCT,
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_NOV,
          (SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_BRD) AS COST_DEC,
          0,
          (SEBARAN.JAN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.FEB * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+
          (SEBARAN.MAR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.APR * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+
          (SEBARAN.MAY * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.JUN * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+
          (SEBARAN.JUL * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.AUG * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+
          (SEBARAN.SEP * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.OCT * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+
          (SEBARAN.NOV * RKT.BIAYA_PEMANEN_RP_PREMI_BRD)+(SEBARAN.DEC * RKT.BIAYA_PEMANEN_RP_PREMI_BRD),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE
                  AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY
                  norma.PERIOD_BUDGET,
                  norma.BA_CODE,
                  norma.AFD_CODE,
                  norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where

    -- UNTUK PERHITUGAN ALAT PANEN (TOOLS)
    -- yaddi.surahman@tap-agri.co.id
    -- saya pindahin ke bagian bawah

    UNION ALL
    -- INI UNTUK PERHITUNGAN SUPERVISI  5101030701 SUPERVISI PEMANEN LABOUR
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
          '5101030701-1' ACTIVITY_CODE,
          'SUPERVISI PEMANEN' ACTIVITY_DESC,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          'HK' AS UOM,
          (SEBARAN.JAN * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_JAN,
          (SEBARAN.FEB * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_FEB,
          (SEBARAN.MAR * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_MAR,
          (SEBARAN.APR * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_APR,
          (SEBARAN.MAY * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_MAY,
          (SEBARAN.JUN * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_JUN,
          (SEBARAN.JUL * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_JUL,
          (SEBARAN.AUG * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_AUG,
          (SEBARAN.SEP * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_SEP,
          (SEBARAN.OCT * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_OCT,
          (SEBARAN.NOV * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_NOV,
          (SEBARAN.DEC * RKT.BIAYA_SPV_RP_BASIS)/CR.RP_HK AS QTY_DEC,
          (SEBARAN.JAN * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JAN,
          (SEBARAN.FEB * RKT.BIAYA_SPV_RP_TOTAL) AS COST_FEB,
          (SEBARAN.MAR * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAR,
          (SEBARAN.APR * RKT.BIAYA_SPV_RP_TOTAL) AS COST_APR,
          (SEBARAN.MAY * RKT.BIAYA_SPV_RP_TOTAL) AS COST_MAY,
          (SEBARAN.JUN * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUN,
          (SEBARAN.JUL * RKT.BIAYA_SPV_RP_TOTAL) AS COST_JUL,
          (SEBARAN.AUG * RKT.BIAYA_SPV_RP_TOTAL) AS COST_AUG,
          (SEBARAN.SEP * RKT.BIAYA_SPV_RP_TOTAL) AS COST_SEP,
          (SEBARAN.OCT * RKT.BIAYA_SPV_RP_TOTAL) AS COST_OCT,
          (SEBARAN.NOV * RKT.BIAYA_SPV_RP_TOTAL) AS COST_NOV,
          (SEBARAN.DEC * RKT.BIAYA_SPV_RP_TOTAL) AS COST_DEC,
          ROUND(RKT.BIAYA_SPV_RP_BASIS/CR.RP_HK,0),
          (SEBARAN.JAN * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.FEB * RKT.BIAYA_SPV_RP_TOTAL)+
          (SEBARAN.MAR * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.APR * RKT.BIAYA_SPV_RP_TOTAL)+
          (SEBARAN.MAY * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.JUN * RKT.BIAYA_SPV_RP_TOTAL)+
          (SEBARAN.JUL * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.AUG * RKT.BIAYA_SPV_RP_TOTAL)+
          (SEBARAN.SEP * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.OCT * RKT.BIAYA_SPV_RP_TOTAL)+
          (SEBARAN.NOV * RKT.BIAYA_SPV_RP_TOTAL)+(SEBARAN.DEC * RKT.BIAYA_SPV_RP_TOTAL),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE, norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN (
          SELECT AVG(RP_HK) RP_HK, PERIOD_BUDGET, BA_CODE FROM TR_RKT_CHECKROLL_SUM 
          WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND BA_CODE = '".$params['key_find']."'
          AND JOB_CODE IN ('FX140') GROUP BY PERIOD_BUDGET, BA_CODE
        ) CHK ON CHK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CHK.BA_CODE = RKT.BA_CODE
        LEFT JOIN TR_RKT_CHECKROLL_SUM CR ON CR.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CR.BA_CODE = RKT.BA_CODE
            AND CR.JOB_CODE IN ('FX140')
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where
  UNION ALL
  -- INI UNTUK PERHITUNGAN SUPERVISI  5101030701 KRANI BUAH LABOUR
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '' AS COST_ELEMENT,
         '5101030701-2' ACTIVITY_CODE,
         'KRANI BUAH' ACTIVITY_DESC,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          'HK' AS UOM,
          (SEBARAN.JAN * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_JAN,
          (SEBARAN.FEB * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_FEB,
          (SEBARAN.MAR * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_MAR,
          (SEBARAN.APR * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_APR,
          (SEBARAN.MAY * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_MAY,
          (SEBARAN.JUN * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_JUN,
          (SEBARAN.JUL * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_JUL,
          (SEBARAN.AUG * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_AUG,
          (SEBARAN.SEP * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_SEP,
          (SEBARAN.OCT * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_OCT,
          (SEBARAN.NOV * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_NOV,
          (SEBARAN.DEC * RKT.KRANI_BUAH_BASIS)/CR.RP_HK AS QTY_DEC,
          (SEBARAN.JAN * RKT.KRANI_BUAH_TOTAL) AS COST_JAN,
          (SEBARAN.FEB * RKT.KRANI_BUAH_TOTAL) AS COST_FEB,
          (SEBARAN.MAR * RKT.KRANI_BUAH_TOTAL) AS COST_MAR,
          (SEBARAN.APR * RKT.KRANI_BUAH_TOTAL) AS COST_APR,
          (SEBARAN.MAY * RKT.KRANI_BUAH_TOTAL) AS COST_MAY,
          (SEBARAN.JUN * RKT.KRANI_BUAH_TOTAL) AS COST_JUN,
          (SEBARAN.JUL * RKT.KRANI_BUAH_TOTAL) AS COST_JUL,
          (SEBARAN.AUG * RKT.KRANI_BUAH_TOTAL) AS COST_AUG,
          (SEBARAN.SEP * RKT.KRANI_BUAH_TOTAL) AS COST_SEP,
          (SEBARAN.OCT * RKT.KRANI_BUAH_TOTAL) AS COST_OCT,
          (SEBARAN.NOV * RKT.KRANI_BUAH_TOTAL) AS COST_NOV,
          (SEBARAN.DEC * RKT.KRANI_BUAH_TOTAL) AS COST_DEC,
          ROUND(RKT.KRANI_BUAH_BASIS/CR.RP_HK,0),
          (SEBARAN.JAN * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.FEB * RKT.KRANI_BUAH_TOTAL)+
          (SEBARAN.MAR * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.APR * RKT.KRANI_BUAH_TOTAL)+
          (SEBARAN.MAY * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.JUN * RKT.KRANI_BUAH_TOTAL)+
          (SEBARAN.JUL * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.AUG * RKT.KRANI_BUAH_TOTAL)+
          (SEBARAN.SEP * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.OCT * RKT.KRANI_BUAH_TOTAL)+
          (SEBARAN.NOV * RKT.KRANI_BUAH_TOTAL)+(SEBARAN.DEC * RKT.KRANI_BUAH_TOTAL),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE, norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN (
          SELECT AVG(RP_HK) RP_HK, PERIOD_BUDGET, BA_CODE FROM TR_RKT_CHECKROLL_SUM 
          WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND BA_CODE = '".$params['key_find']."'
          AND JOB_CODE IN ('FX160') GROUP BY PERIOD_BUDGET, BA_CODE
        ) CHK ON CHK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CHK.BA_CODE = RKT.BA_CODE
        LEFT JOIN TR_RKT_CHECKROLL_SUM CR ON CR.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CR.BA_CODE = RKT.BA_CODE
            AND CR.JOB_CODE IN ('FX160')
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where
  UNION ALL
  -- INI UNTUK PERHITUNGAN SUPERVISI  5101030701 BONGKAR MUAT LABOUR
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          'LABOUR' AS COST_ELEMENT,
          '5101030404-1' ACTIVITY_CODE,
          'BONGKAR MUAT' ACTIVITY_DESC,
          '' AS SUB_COST_ELEMENT,
          '' MATERIAL_NAME,
          '' KETERANGAN,
          'HK' AS UOM,
          (SEBARAN.JAN * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_JAN,
          (SEBARAN.FEB * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_FEB,
          (SEBARAN.MAR * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_MAR,
          (SEBARAN.APR * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_APR,
          (SEBARAN.MAY * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_MAY,
          (SEBARAN.JUN * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_JUN,
          (SEBARAN.JUL * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_JUL,
          (SEBARAN.AUG * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_AUG,
          (SEBARAN.SEP * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_SEP,
          (SEBARAN.OCT * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_OCT,
          (SEBARAN.NOV * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_NOV,
          (SEBARAN.DEC * RKT.TUKANG_MUAT_BASIS)/CR.RP_HK AS QTY_DEC,
          (SEBARAN.JAN * RKT.TUKANG_MUAT_TOTAL) AS COST_JAN,
          (SEBARAN.FEB * RKT.TUKANG_MUAT_TOTAL) AS COST_FEB,
          (SEBARAN.MAR * RKT.TUKANG_MUAT_TOTAL) AS COST_MAR,
          (SEBARAN.APR * RKT.TUKANG_MUAT_TOTAL) AS COST_APR,
          (SEBARAN.MAY * RKT.TUKANG_MUAT_TOTAL) AS COST_MAY,
          (SEBARAN.JUN * RKT.TUKANG_MUAT_TOTAL) AS COST_JUN,
          (SEBARAN.JUL * RKT.TUKANG_MUAT_TOTAL) AS COST_JUL,
          (SEBARAN.AUG * RKT.TUKANG_MUAT_TOTAL) AS COST_AUG,
          (SEBARAN.SEP * RKT.TUKANG_MUAT_TOTAL) AS COST_SEP,
          (SEBARAN.OCT * RKT.TUKANG_MUAT_TOTAL) AS COST_OCT,
          (SEBARAN.NOV * RKT.TUKANG_MUAT_TOTAL) AS COST_NOV,
          (SEBARAN.DEC * RKT.TUKANG_MUAT_TOTAL) AS COST_DEC,
          ROUND(RKT.TUKANG_MUAT_BASIS/CR.RP_HK,0),
          (SEBARAN.JAN * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.FEB * RKT.TUKANG_MUAT_TOTAL)+
          (SEBARAN.MAR * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.APR * RKT.TUKANG_MUAT_TOTAL)+
          (SEBARAN.MAY * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.JUN * RKT.TUKANG_MUAT_TOTAL)+
          (SEBARAN.JUL * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.AUG * RKT.TUKANG_MUAT_TOTAL)+
          (SEBARAN.SEP * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.OCT * RKT.TUKANG_MUAT_TOTAL)+
          (SEBARAN.NOV * RKT.TUKANG_MUAT_TOTAL)+(SEBARAN.DEC * RKT.TUKANG_MUAT_TOTAL),
          '".$this->_userName."' AS INSERT_USER, CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN, FEB / TOTAL AS FEB, MAR / TOTAL AS MAR,
              APR / TOTAL AS APR, MAY / TOTAL AS MAY, JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL, AUG / TOTAL AS AUG, SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT, NOV / TOTAL AS NOV, DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN, SUM( norma.FEB ) FEB, SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR, SUM( norma.MAY ) MAY, SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL, SUM( norma.AUG ) AUG, SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT, SUM( norma.NOV ) NOV, SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total
                FROM TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE norma.DELETE_USER IS NULL $where1
                GROUP BY norma.PERIOD_BUDGET, norma.BA_CODE, norma.AFD_CODE, norma.BLOCK_CODE
              )
          ) SEBARAN ON sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN (
          SELECT AVG(RP_HK) RP_HK, PERIOD_BUDGET, BA_CODE FROM TR_RKT_CHECKROLL_SUM 
          WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND BA_CODE = '".$params['key_find']."'
          AND JOB_CODE IN ('FW041') GROUP BY PERIOD_BUDGET, BA_CODE
        ) CHK ON CHK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CHK.BA_CODE = RKT.BA_CODE
        LEFT JOIN TR_RKT_CHECKROLL_SUM CR ON CR.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CR.BA_CODE = RKT.BA_CODE
            AND CR.JOB_CODE IN ('FW041')
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL $where 
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
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN) AS COST_JAN,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB) AS COST_FEB,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR) AS COST_MAR,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR) AS COST_APR,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY) AS COST_MAY,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN) AS COST_JUN,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL) AS COST_JUL,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG) AS COST_AUG,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP) AS COST_SEP,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT) AS COST_OCT,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV) AS COST_NOV,
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_DEC) AS COST_DEC,
          0 QTY_SETAHUN,
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JAN)+
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_FEB)+
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAR)+
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_APR)+
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_MAY)+
          (HS.SMS1_TM / NULLIF (HS2.TOTAL_HA_SMS1, 0) * RKT.COST_JUN)+
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_JUL)+
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_AUG)+
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_SEP)+
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_OCT)+
          (HS.SMS2_TM / NULLIF (HS2.TOTAL_HA_SMS2, 0) * RKT.COST_NOV)+
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
            ON HS2.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND HS2.BA_CODE = RKT.BA_CODE
          LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
          LEFT JOIN (
            SELECT TRC.PERIOD_BUDGET, TRC.BA_CODE, SUM (TRC.MPP_PERIOD_BUDGET) AS MPP FROM TR_RKT_CHECKROLL TRC
            WHERE TRC.JOB_CODE in ('FX130','FX110') AND EXTRACT(YEAR FROM TRC.PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND TRC.BA_CODE = '".$params['key_find']."'
            GROUP BY  TRC.PERIOD_BUDGET, TRC.BA_CODE
          )MPP_ALL ON MPP_ALL.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND MPP_ALL.BA_CODE = RKT.BA_CODE
          LEFT JOIN (
            SELECT DISTINCT PERIOD_BUDGET, BA_CODE, MAX(HKE) OVER(PARTITION BY PERIOD_BUDGET, BA_CODE) HKE
            FROM TM_CHECKROLL_HK HKE WHERE EXTRACT(YEAR FROM HKE.PERIOD_BUDGET) = '".$params['budgetperiod']."' 
            AND HKE.BA_CODE = '".$params['key_find']."'
          ) HK ON HK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND HK.BA_CODE = RKT.BA_CODE
        WHERE RKT.COA_CODE = '43800' $where      
    ";

    $this->_db->query($query);
    $this->_db->commit();
    
    // yaddi.surahman@tap-agri.co.id
    // 2017-08-30
    // KEBUTUHAN AKTIFITAS PER BA
    
    $components['rawat_opsi'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK(
          PERIOD_BUDGET,
          REGION_CODE,
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI,
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          SUB_COST_ELEMENT,
          SUB_COST_ELEMENT_DESC,
          UOM,
          QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
          COST_JAN,COST_FEB,COST_MAR,COST_APR,COST_MAY,COST_JUN,COST_JUL,COST_AUG,COST_SEP,COST_OCT,COST_NOV,COST_DEC,
          INSERT_USER,INSERT_TIME
        )
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS1 AS ACTIVITY_GROUP,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          RKT.COST_ELEMENT,
          BIAYA.SUB_COST_ELEMENT,
          CASE
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN A1.DESCRIPTION
            ELSE TM_MAT.MATERIAL_NAME
          END SUB_COST_ELEMENT_DESC,
          CASE
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN 'HK'
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN(
              SELECT ACT.UOM FROM TM_ACTIVITY ACT
              WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT
            )
            ELSE(
              SELECT material.UOM FROM TM_MATERIAL material
              WHERE material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
                AND material.BA_CODE = BIAYA.BA_CODE
            )
          END AS UOM,
          CASE
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_JAN * BIAYA.PRICE_ROTASI )/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_JAN
            ELSE(RKT.PLAN_JAN * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_JAN,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_FEB * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_FEB
            ELSE(RKT.PLAN_FEB * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_FEB,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_MAR * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_MAR
            ELSE(RKT.PLAN_MAR * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_MAR,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_APR * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_APR
            ELSE(RKT.PLAN_APR * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_APR,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_MAY * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_MAY
            ELSE(RKT.PLAN_MAY * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_MAY,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_JUN * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_JUN
            ELSE(RKT.PLAN_JUN * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_JUN,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_JUL, 0) QTY_JUL,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_AUG, 0) QTY_AUG,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_SEP, 0) QTY_SEP,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_OCT, 0) QTY_OCT,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_NOV, 0) QTY_NOV,
          DECODE(RKT.COST_ELEMENT, 'CONTRACT', RKT.PLAN_DEC, 0) QTY_DEC,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JAN * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_JAN * BIAYA.PRICE_ROTASI_SITE
          END AS COST_JAN,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_FEB * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_FEB * BIAYA.PRICE_ROTASI_SITE
          END AS COST_FEB,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAR * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_MAR * BIAYA.PRICE_ROTASI_SITE
          END AS COST_MAR,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_APR * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_APR * BIAYA.PRICE_ROTASI_SITE
          END AS COST_APR,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_MAY * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_MAY * BIAYA.PRICE_ROTASI_SITE
          END AS COST_MAY,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUN * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_JUN * BIAYA.PRICE_ROTASI_SITE
          END AS COST_JUN,
          0 COST_JUL,
          0 COST_AUG,
          0 COST_SEP,
          0 COST_OCT,
          0 COST_NOV,
          0 COST_DEC, '".$this->_userName."', CURRENT_TIMESTAMP
        FROM
          TR_RKT_COST_ELEMENT RKT
        LEFT JOIN TR_RKT RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
        LEFT JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN TM_HECTARE_STATEMENT TM_HS ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND TM_HS.BA_CODE = RKT.BA_CODE AND TM_HS.AFD_CODE = RKT.AFD_CODE
          AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TN_BIAYA BIAYA ON RKT.BA_CODE = BIAYA.BA_CODE
          AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
          AND RKT.MATURITY_STAGE_SMS1 = BIAYA.ACTIVITY_GROUP
          AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
          AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
          AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
          AND BIAYA.LAND_TYPE IN('ALL',TM_HS.LAND_TYPE)
          AND BIAYA.TOPOGRAPHY IN('ALL',TM_HS.TOPOGRAPHY)
        LEFT JOIN TM_ACTIVITY A1 ON A1.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
        LEFT JOIN TM_MATERIAL TM_MAT ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
          AND TM_MAT.BA_CODE = BIAYA.BA_CODE AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
        LEFT JOIN TR_RKT_CHECKROLL_SUM RATE_HK ON RATE_HK.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND RATE_HK.BA_CODE = RKT.BA_CODE AND RATE_HK.JOB_CODE = BIAYA.SUB_COST_ELEMENT
        WHERE
          RKT.DELETE_USER IS NULL
          AND RKT_INDUK.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS1 = 'TM'
          AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
          AND RKT.COST_ELEMENT <> 'TRANSPORT' $where
        UNION ALL SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS2 AS ACTIVITY_GROUP,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          RKT.COST_ELEMENT,
          BIAYA.SUB_COST_ELEMENT,
          CASE
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN A1.DESCRIPTION
            ELSE TM_MAT.MATERIAL_NAME
          END SUB_COST_ELEMENT_DESC,
          CASE
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN 'HK'
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN(
              SELECT ACT.UOM FROM TM_ACTIVITY ACT
              WHERE ACT.ACTIVITY_CODE = BIAYA.SUB_COST_ELEMENT
            )
            ELSE(
              SELECT material.UOM
              FROM TM_MATERIAL material
              WHERE material.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
                AND material.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET AND material.BA_CODE = BIAYA.BA_CODE
            )
          END AS UOM,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_JAN, 0) AS QTY_JAN,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_FEB, 0) AS QTY_FEB,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_MAR, 0) AS QTY_MAR,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_APR, 0) AS QTY_APR,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_MAY, 0) AS QTY_MAY,
          DECODE(RKT.COST_ELEMENT,'CONTRACT', RKT.PLAN_JUN, 0) AS QTY_JUN,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_JUL * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_JUL
            ELSE (RKT.PLAN_JUL * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_JUL,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_AUG * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_AUG
            ELSE (RKT.PLAN_AUG * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_AUG,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_SEP * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_SEP
            ELSE (RKT.PLAN_SEP * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_SEP,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_OCT * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_OCT
            ELSE (RKT.PLAN_OCT * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_OCT,
          CASE 
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_NOV * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_NOV
            ELSE (RKT.PLAN_NOV * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_NOV,
          CASE
            WHEN RKT.COST_ELEMENT = 'LABOUR' THEN(RKT.PLAN_DEC * BIAYA.PRICE_ROTASI)/ RATE_HK.RP_HK
            WHEN RKT.COST_ELEMENT = 'CONTRACT' THEN RKT.PLAN_DEC
            ELSE(RKT.PLAN_DEC * BIAYA.PRICE_ROTASI)/ TM_MAT.PRICE
          END AS QTY_DEC,
          0 COST_JAN,
          0 COST_FEB,
          0 COST_MAR,
          0 COST_APR,
          0 COST_MAY,
          0 COST_JUN,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_JUL * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_JUL * BIAYA.PRICE_ROTASI_SITE
          END AS COST_JUL,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_AUG * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_AUG * BIAYA.PRICE_ROTASI_SITE
          END AS COST_AUG,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_SEP * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_SEP * BIAYA.PRICE_ROTASI_SITE
          END AS COST_SEP,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_OCT * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_OCT * BIAYA.PRICE_ROTASI_SITE
          END AS COST_OCT,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_NOV * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_NOV * BIAYA.PRICE_ROTASI_SITE
          END AS COST_NOV,
          CASE
            WHEN RKT.TIPE_NORMA = 'UMUM' THEN RKT.PLAN_DEC * BIAYA.PRICE_ROTASI
            ELSE RKT.PLAN_DEC * BIAYA.PRICE_ROTASI_SITE
          END AS COST_DEC, '".$this->_userName."', CURRENT_TIMESTAMP
        FROM TR_RKT_COST_ELEMENT RKT
        LEFT JOIN TR_RKT RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
        LEFT JOIN TM_ACTIVITY ACT ON         ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN TM_HECTARE_STATEMENT TM_HS ON TM_HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND TM_HS.BA_CODE = RKT.BA_CODE
          AND TM_HS.AFD_CODE = RKT.AFD_CODE
          AND TM_HS.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TN_BIAYA BIAYA ON RKT.BA_CODE = BIAYA.BA_CODE
          AND RKT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
          AND RKT.MATURITY_STAGE_SMS2 = BIAYA.ACTIVITY_GROUP
          AND RKT_INDUK.ACTIVITY_CLASS = BIAYA.ACTIVITY_CLASS
          AND RKT.ATRIBUT = BIAYA.ACTIVITY_CODE
          AND RKT.COST_ELEMENT = BIAYA.COST_ELEMENT
          AND BIAYA.LAND_TYPE IN( 'ALL', TM_HS.LAND_TYPE )
          AND BIAYA.TOPOGRAPHY IN('ALL',TM_HS.TOPOGRAPHY)
        LEFT JOIN TM_ACTIVITY A1 ON A1.ACTIVITY_CODE = BIAYA.ACTIVITY_CODE
        LEFT JOIN TM_MATERIAL TM_MAT ON TM_MAT.PERIOD_BUDGET = BIAYA.PERIOD_BUDGET
          AND TM_MAT.BA_CODE = BIAYA.BA_CODE
          AND TM_MAT.MATERIAL_CODE = BIAYA.SUB_COST_ELEMENT
        LEFT JOIN TR_RKT_CHECKROLL_SUM RATE_HK ON RATE_HK.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND RATE_HK.BA_CODE = RKT.BA_CODE AND RATE_HK.JOB_CODE = BIAYA.SUB_COST_ELEMENT
        WHERE
          RKT.DELETE_USER IS NULL
          AND RKT_INDUK.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS2 = 'TM'
          AND RKT.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
          AND RKT.COST_ELEMENT <> 'TRANSPORT' $where
    ";

    $components['cost_qty_setahun_of_rawat_opsi'] = "
       UPDATE TMP_RPT_KEB_EST_COST_BLOCK SET 
          QTY_SETAHUN = QTY_JAN+QTY_FEB+QTY_MAR+QTY_APR+QTY_MAY+QTY_JUN+QTY_JUL+QTY_AUG+QTY_SEP+QTY_OCT+QTY_NOV+QTY_DEC,
          COST_SETAHUN = COST_JAN+COST_FEB+COST_MAR+COST_APR+COST_MAY+COST_JUN+COST_JUL+COST_AUG+COST_SEP+COST_OCT+COST_NOV+COST_DEC
       WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
         AND BA_CODE = '".$params['key_find']."'
         AND QTY_SETAHUN IS NULL
         AND COST_SETAHUN IS NULL
    ";

    // PUPUK, PUPUK TUNGGAL, PUPUK MAJEMUK
    $components['labor_pupuk_tunggal'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
          PERIOD_BUDGET, 
          REGION_CODE, 
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI, 
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          UOM, 
          INSERT_USER, INSERT_TIME,
          KETERANGAN,
          QTY_JAN, COST_JAN,
          QTY_FEB, COST_FEB,
          QTY_MAR, COST_MAR,
          QTY_APR, COST_APR,
          QTY_MAY, COST_MAY,
          QTY_JUN, COST_JUN,
          QTY_JUL, COST_JUL,
          QTY_AUG, COST_AUG,
          QTY_SEP, COST_SEP,
          QTY_OCT, COST_OCT,
          QTY_NOV, COST_NOV,
          QTY_DEC, COST_DEC,
          QTY_SETAHUN, COST_SETAHUN
        )
        SELECT PIV.*, 
            NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
            NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
            NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
            NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
        FROM (
            SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
            , RKT.AFD_CODE, RKT.BLOCK_CODE
            , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
            , 'PUPUK TUNGGAL' ACTIVITY_DESC
            , 'LABOUR' COST_ELEMENT
            , 'HK' UOM
            , '".$this->_userName."', CURRENT_TIMESTAMP, NULL
            , RKT.BULAN_PEMUPUKAN
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL / CR.RP_HK HK
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL COST
            FROM (
                SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
                , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
                , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
                , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
                FROM TM_HECTARE_STATEMENT HS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
                JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
                AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
                JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
                AND MAT.BA_CODE = FZ.BA_CODE
                JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
                WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
                AND FZ.BA_CODE = '".$params['key_find']."'
                AND HS.MATURITY_STAGE_SMS1 = 'TM'
                AND COA.COA_CODE = '5101020300'
                AND FZ.JENIS_TANAM = 'NORMAL'
            ) RKT
            LEFT JOIN TN_INFRASTRUKTUR COST ON COST.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'LABOUR' 
                AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
            LEFT JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = RKT.BA_CODE AND CR.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
            )
            PIVOT (
                SUM(HK) AS DIS, SUM(COST) AS COST
                FOR BULAN_PEMUPUKAN IN (
                    '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
                    '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
                )
            )
        ) PIV
    ";

    $components['labor_pupuk_majemuk'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
          PERIOD_BUDGET, 
          REGION_CODE, 
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI, 
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          UOM, 
          INSERT_USER, INSERT_TIME,
          KETERANGAN,
          QTY_JAN, COST_JAN,
          QTY_FEB, COST_FEB,
          QTY_MAR, COST_MAR,
          QTY_APR, COST_APR,
          QTY_MAY, COST_MAY,
          QTY_JUN, COST_JUN,
          QTY_JUL, COST_JUL,
          QTY_AUG, COST_AUG,
          QTY_SEP, COST_SEP,
          QTY_OCT, COST_OCT,
          QTY_NOV, COST_NOV,
          QTY_DEC, COST_DEC,
          QTY_SETAHUN, COST_SETAHUN
        )
        SELECT PIV.*, 
            NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
            NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
            NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
            NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
        FROM (
            SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
            , RKT.AFD_CODE, RKT.BLOCK_CODE
            , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
            , 'PUPUK MAJEMUK' ACTIVITY_DESC
            , 'LABOUR' COST_ELEMENT
            , 'HK' UOM
            , '".$this->_userName."', CURRENT_TIMESTAMP, NULL
            , RKT.BULAN_PEMUPUKAN
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL / CR.RP_HK HK
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL COST
            FROM (
                SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
                , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
                , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
                , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
                FROM TM_HECTARE_STATEMENT HS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
                JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
                AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
                JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
                AND MAT.BA_CODE = FZ.BA_CODE
                JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
                WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
                AND FZ.BA_CODE = '".$params['key_find']."'
                AND HS.MATURITY_STAGE_SMS1 = 'TM'
                AND COA.COA_CODE = '5101020400'
                AND FZ.JENIS_TANAM = 'NORMAL'
            ) RKT
            LEFT JOIN TN_INFRASTRUKTUR COST ON COST.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET
                AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'LABOUR' 
                AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
            LEFT JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = RKT.BA_CODE AND CR.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
            )
            PIVOT (
                SUM(HK) AS DIS, SUM(COST) AS COST
                FOR BULAN_PEMUPUKAN IN (
                    '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
                    '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
                )
            )
        ) PIV
    ";

    $components['material_pupuk_tunggal'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
          PERIOD_BUDGET, 
          REGION_CODE, 
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI, 
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          UOM, 
          SUB_COST_ELEMENT,
          INSERT_USER, INSERT_TIME,
          SUB_COST_ELEMENT_DESC,
          QTY_JAN, COST_JAN,
          QTY_FEB, COST_FEB,
          QTY_MAR, COST_MAR,
          QTY_APR, COST_APR,
          QTY_MAY, COST_MAY,
          QTY_JUN, COST_JUN,
          QTY_JUL, COST_JUL,
          QTY_AUG, COST_AUG,
          QTY_SEP, COST_SEP,
          QTY_OCT, COST_OCT,
          QTY_NOV, COST_NOV,
          QTY_DEC, COST_DEC,
          QTY_SETAHUN, COST_SETAHUN
        )
        SELECT PIV.*, 
            NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
            NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
            NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
            NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
          FROM (
            SELECT * FROM (
              SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
              , RKT.AFD_CODE, RKT.BLOCK_CODE
              , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
              , 'PUPUK TUNGGAL' ACTIVITY_DESC
              , 'MATERIAL' COST_ELEMENT
              , 'KG' UOM
              , RKT.MATERIAL_NAME
              , '".$this->_userName."', CURRENT_TIMESTAMP
              , RKT.MATERIAL_NAME DESCRIPTION
              , RKT.BULAN_PEMUPUKAN
              , JUMLAH
              , PRICE
            FROM (
              SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
                , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
                , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
                , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
                , MAT.PRICE, FZ.JUMLAH
              FROM TM_HECTARE_STATEMENT HS
              JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
              JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
                AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
              JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
                AND MAT.BA_CODE = FZ.BA_CODE
              JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
              WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
              AND FZ.BA_CODE = '".$params['key_find']."'
              AND HS.MATURITY_STAGE_SMS1 = 'TM'
              AND COA.COA_CODE = '5101020300'
              AND FZ.JENIS_TANAM = 'NORMAL'
            ) RKT
          )
          PIVOT (
            SUM(JUMLAH) AS DIS, SUM(JUMLAH * PRICE) AS COST
            FOR BULAN_PEMUPUKAN IN (
              '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
              '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
            )
          )
        ) PIV
    ";

    $components['material_pupuk_majemuk'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
          PERIOD_BUDGET, 
          REGION_CODE, 
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI, 
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          UOM, 
          SUB_COST_ELEMENT,
          INSERT_USER, INSERT_TIME,
          SUB_COST_ELEMENT_DESC,
          QTY_JAN, COST_JAN,
          QTY_FEB, COST_FEB,
          QTY_MAR, COST_MAR,
          QTY_APR, COST_APR,
          QTY_MAY, COST_MAY,
          QTY_JUN, COST_JUN,
          QTY_JUL, COST_JUL,
          QTY_AUG, COST_AUG,
          QTY_SEP, COST_SEP,
          QTY_OCT, COST_OCT,
          QTY_NOV, COST_NOV,
          QTY_DEC, COST_DEC,
          QTY_SETAHUN, COST_SETAHUN
        )
        SELECT PIV.*, 
            NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
            NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
            NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
            NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
          FROM (
            SELECT * FROM (
              SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
              , RKT.AFD_CODE, RKT.BLOCK_CODE
              , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
              , 'PUPUK MAJEMUK' ACTIVITY_DESC
              , 'MATERIAL' COST_ELEMENT
              , 'KG' UOM
              , RKT.MATERIAL_NAME
              , '".$this->_userName."', CURRENT_TIMESTAMP
              , RKT.MATERIAL_NAME DESCRIPTION
              , RKT.BULAN_PEMUPUKAN
              , JUMLAH
              , PRICE
            FROM (
              SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
                , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
                , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
                , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
                , MAT.PRICE, FZ.JUMLAH
              FROM TM_HECTARE_STATEMENT HS
              JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
              JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
                AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
              JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
                AND MAT.BA_CODE = FZ.BA_CODE
              JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
              WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
              AND FZ.BA_CODE = '".$params['key_find']."'
              AND HS.MATURITY_STAGE_SMS1 = 'TM'
              AND COA.COA_CODE = '5101020400'
              AND FZ.JENIS_TANAM = 'NORMAL'
            ) RKT
          )
          PIVOT (
            SUM(JUMLAH) AS DIS, SUM(JUMLAH * PRICE) AS COST
            FOR BULAN_PEMUPUKAN IN (
              '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
              '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
            )
          )
        ) PIV
    ";

    $components['tools_pupuk_tunggal'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, 
        -- SUB_COST_ELEMENT,
        INSERT_USER, INSERT_TIME,
        SUB_COST_ELEMENT_DESC,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN
      )
      SELECT PIV.*, 
          NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
          NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
          NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
          NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
        FROM (
          SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
            , RKT.AFD_CODE, RKT.BLOCK_CODE
            , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
            , 'PUPUK TUNGGAL' ACTIVITY_DESC
            , 'TOOLS' COST_ELEMENT
            , M1.UOM
            -- , RKT.MATERIAL_NAME
            , '".$this->_userName."', CURRENT_TIMESTAMP
            , M1.MATERIAL_NAME DESCRIPTION
            , RKT.BULAN_PEMUPUKAN
            , COST.QTY_ALAT * RKT.POKOK_TANAM QTY_ALAT
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL COST
          FROM (
            SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
              , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
              , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
              , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
              , MAT.PRICE, FZ.JUMLAH
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
              AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
            JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
              AND MAT.BA_CODE = FZ.BA_CODE
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
            WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND FZ.BA_CODE = '".$params['key_find']."'
            AND HS.MATURITY_STAGE_SMS1 = 'TM'
            AND COA.COA_CODE = '5101020300'
            AND FZ.JENIS_TANAM = 'NORMAL'
          ) RKT
          LEFT JOIN TN_INFRASTRUKTUR COST ON COST.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET
            AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' 
            AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
          LEFT JOIN TM_MATERIAL M1 ON M1.PERIOD_BUDGET = COST.PERIOD_BUDGET AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
            AND M1.BA_CODE = COST.BA_CODE
          WHERE COST.TOPOGRAPHY = (
            SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
            AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
            AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
          )
        )
        PIVOT (
          SUM(QTY_ALAT) DIS, SUM(COST) AS COST
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PIV
    ";

    $components['tools_pupuk_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, 
        -- SUB_COST_ELEMENT,
        INSERT_USER, INSERT_TIME,
        SUB_COST_ELEMENT_DESC,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN
      )
      SELECT PIV.*, 
          NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
          NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
          NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
          NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN
        FROM (
          SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE
            , RKT.AFD_CODE, RKT.BLOCK_CODE
            , 'TM' TIPE_TRANSAKSI, RKT.COA_CODE ACTIVITY_CODE
            , 'PUPUK MAJEMUK' ACTIVITY_DESC
            , 'TOOLS' COST_ELEMENT
            , M1.UOM
            -- , RKT.MATERIAL_NAME
            , '".$this->_userName."', CURRENT_TIMESTAMP
            , M1.MATERIAL_NAME DESCRIPTION
            , RKT.BULAN_PEMUPUKAN
            , COST.QTY_ALAT * RKT.POKOK_TANAM QTY_ALAT
            , RKT.POKOK_TANAM * COST.RP_QTY_INTERNAL COST
          FROM (
            SELECT DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770') ACTIVITY_CODE
              , HS.LAND_TYPE, HS.TOPOGRAPHY, ORG.REGION_CODE, HS.BA_CODE
              , HS.AFD_CODE, HS.BLOCK_CODE, HS.PERIOD_BUDGET, HS.HA_PLANTED, HS.POKOK_TANAM
              , FZ.BULAN_PEMUPUKAN, MAT.FLAG, MAT.MATERIAL_NAME, COA.COA_CODE
              , MAT.PRICE, FZ.JUMLAH
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TN_PUPUK_TBM2_TM FZ ON FZ.PERIOD_BUDGET = HS.PERIOD_BUDGET AND FZ.BA_CODE = HS.BA_CODE
              AND FZ.AFD_CODE = HS.AFD_CODE AND FZ.BLOCK_CODE = HS.BLOCK_CODE
            JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = FZ.MATERIAL_CODE AND MAT.PERIOD_BUDGET = FZ.PERIOD_BUDGET
              AND MAT.BA_CODE = FZ.BA_CODE
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
            WHERE EXTRACT(YEAR FROM FZ.PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND FZ.BA_CODE = '".$params['key_find']."'
            AND HS.MATURITY_STAGE_SMS1 = 'TM'
            AND COA.COA_CODE = '5101020400'
            AND FZ.JENIS_TANAM = 'NORMAL'
          ) RKT
          LEFT JOIN TN_INFRASTRUKTUR COST ON COST.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET
            AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' 
            AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
          LEFT JOIN TM_MATERIAL M1 ON M1.PERIOD_BUDGET = COST.PERIOD_BUDGET AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
            AND M1.BA_CODE = COST.BA_CODE
          WHERE COST.TOPOGRAPHY = (
            SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
            AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
            AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
          )
        )
        PIVOT (
          SUM(QTY_ALAT) DIS, SUM(COST) AS COST
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PIV
    ";

    $components['transport_pupuk_tunggal'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
      PERIOD_BUDGET,
      REGION_CODE,
      BA_CODE,
      AFD_CODE,
      BLOCK_CODE,
      TIPE_TRANSAKSI,
      ACTIVITY_CODE,
      ACTIVITY_DESC,
      COST_ELEMENT,
      UOM,
      SUB_COST_ELEMENT_DESC,
      INSERT_USER,INSERT_TIME, KETERANGAN,
      QTY_SETAHUN, COST_SETAHUN,
      QTY_JAN, COST_JAN,
      QTY_FEB, COST_FEB,
      QTY_MAR, COST_MAR,
      QTY_APR, COST_APR,
      QTY_MAY, COST_MAY,
      QTY_JUN, COST_JUN,
      QTY_JUL, COST_JUL,
      QTY_AUG, COST_AUG,
      QTY_SEP, COST_SEP,
      QTY_OCT, COST_OCT,
      QTY_NOV, COST_NOV,
      QTY_DEC, COST_DEC
      )
      SELECT * FROM (
        SELECT S.*, SUM(S.JUMLAH/S.KG_AFDELING*S.HM_KM) OVER (PARTITION BY PERIOD_BUDGET, BA_CODE, BLOCK_CODE, AFD_CODE, VRA_SUB_CAT_DESCRIPTION) QTY_TOTAL
          , SUM(S.JUMLAH/S.KG_AFDELING*S.PRICE_HM_KM) OVER (PARTITION BY PERIOD_BUDGET, BA_CODE, BLOCK_CODE, AFD_CODE, VRA_SUB_CAT_DESCRIPTION) COST_TOTAL 
        FROM (
          SELECT TM.PERIOD_BUDGET, ORG.REGION_CODE, TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE
          , HS.MATURITY_STAGE_SMS1
          , COA.COA_CODE
          , 'PUPUK TUNGGAL', 'TRANSPORT', VH.UOM
          , VH.VRA_SUB_CAT_DESCRIPTION
          , '".$this->_userName."', CURRENT_TIMESTAMP, NULL
          , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN, VRA.PRICE_HM_KM, VRA.HM_KM
          , F_GET_PUPUK_KG_AFDELING(TM.PERIOD_BUDGET, TM.BA_CODE, TM.AFD_CODE) KG_AFDELING
          , TM.JUMLAH
          FROM TM_HECTARE_STATEMENT HS
          JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
          JOIN TN_PUPUK_TBM2_TM TM ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
            AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
          JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
          JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
          LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = TM.PERIOD_BUDGET
            AND VRA.BA_CODE = TM.BA_CODE AND VRA.LOCATION_CODE = TM.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
          JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
          WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = '".$params['budgetperiod']."'
           AND TM.BA_CODE = '".$params['key_find']."'
           AND HS.MATURITY_STAGE_SMS1 = 'TM'
          AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
          AND COA.COA_CODE = '5101020300' -- TUNGGAL
        ) S
      )
      PIVOT (
        SUM(JUMLAH/KG_AFDELING*HM_KM) DIS, SUM(JUMLAH/KG_AFDELING*PRICE_HM_KM) COST
        FOR BULAN_PEMUPUKAN IN (
        '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
        '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
        )
      )
    ";

    $components['transport_pupuk_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
      PERIOD_BUDGET,
      REGION_CODE,
      BA_CODE,
      AFD_CODE,
      BLOCK_CODE,
      TIPE_TRANSAKSI,
      ACTIVITY_CODE,
      ACTIVITY_DESC,
      COST_ELEMENT,
      UOM,
      SUB_COST_ELEMENT_DESC,
      INSERT_USER,INSERT_TIME, KETERANGAN,
      QTY_SETAHUN, COST_SETAHUN,
      QTY_JAN, COST_JAN,
      QTY_FEB, COST_FEB,
      QTY_MAR, COST_MAR,
      QTY_APR, COST_APR,
      QTY_MAY, COST_MAY,
      QTY_JUN, COST_JUN,
      QTY_JUL, COST_JUL,
      QTY_AUG, COST_AUG,
      QTY_SEP, COST_SEP,
      QTY_OCT, COST_OCT,
      QTY_NOV, COST_NOV,
      QTY_DEC, COST_DEC
      )
      SELECT * FROM (
        SELECT S.*, SUM(S.JUMLAH/S.KG_AFDELING*S.HM_KM) OVER (PARTITION BY PERIOD_BUDGET, BA_CODE, BLOCK_CODE, AFD_CODE, VRA_SUB_CAT_DESCRIPTION) QTY_TOTAL
          , SUM(S.JUMLAH/S.KG_AFDELING*S.PRICE_HM_KM) OVER (PARTITION BY PERIOD_BUDGET, BA_CODE, BLOCK_CODE, AFD_CODE, VRA_SUB_CAT_DESCRIPTION) COST_TOTAL 
        FROM (
          SELECT TM.PERIOD_BUDGET, ORG.REGION_CODE, TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE
          , HS.MATURITY_STAGE_SMS1
          , COA.COA_CODE
          , 'PUPUK MAJEMUK', 'TRANSPORT', VH.UOM
          , VH.VRA_SUB_CAT_DESCRIPTION
          , '".$this->_userName."', CURRENT_TIMESTAMP, NULL
          , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN, VRA.PRICE_HM_KM, VRA.HM_KM
          , F_GET_PUPUK_KG_AFDELING(TM.PERIOD_BUDGET, TM.BA_CODE, TM.AFD_CODE) KG_AFDELING
          , TM.JUMLAH
          FROM TM_HECTARE_STATEMENT HS
          JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
          JOIN TN_PUPUK_TBM2_TM TM ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
            AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
          JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
          JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
          LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = TM.PERIOD_BUDGET
            AND VRA.BA_CODE = TM.BA_CODE AND VRA.LOCATION_CODE = TM.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
          JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
          WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = '".$params['budgetperiod']."'
           AND TM.BA_CODE = '".$params['key_find']."'
           AND HS.MATURITY_STAGE_SMS1 = 'TM'
          AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
          AND COA.COA_CODE = '5101020400' -- MAJEMUK
        ) S
      )
      PIVOT (
        SUM(JUMLAH/KG_AFDELING*HM_KM) DIS, SUM(JUMLAH/KG_AFDELING*PRICE_HM_KM) COST
        FOR BULAN_PEMUPUKAN IN (
        '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
        '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
        )
      )
    ";

    // LANGSIR
    $components['langsir_labor'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, 
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
        SELECT RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,
            RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE,
           'TM' AS TIPE_TRANSAKSI,
           '5101030404-2' ACTIVITY_CODE,
           'LANGSIR' ACTIVITY_DESC,
           'LABOUR' COST_ELEMENT,
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
            (SEBARAN.JAN / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_JAN,
            (SEBARAN.FEB / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_FEB,
            (SEBARAN.MAR / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_MAR,
            (SEBARAN.APR / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_APR,
            (SEBARAN.MAY / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_MAY,
            (SEBARAN.JUN / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_JUN,
            (SEBARAN.JUL / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_JUL,
            (SEBARAN.AUG / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_AUG,
            (SEBARAN.SEP / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_SEP,
            (SEBARAN.OCT / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_OCT,
            (SEBARAN.NOV / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_NOV,
            (SEBARAN.DEC / 100 * RKT.LANGSIR_TUKANG_MUAT) AS COST_DEC,
            0 QTY_SETAHUN,
            (SEBARAN.JAN / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.FEB / 100 * RKT.LANGSIR_TUKANG_MUAT) +
            (SEBARAN.MAR / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.APR / 100 * RKT.LANGSIR_TUKANG_MUAT) +
            (SEBARAN.MAY / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.JUN / 100 * RKT.LANGSIR_TUKANG_MUAT) +
            (SEBARAN.JUL / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.AUG / 100 * RKT.LANGSIR_TUKANG_MUAT) +
            (SEBARAN.SEP / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.OCT / 100 * RKT.LANGSIR_TUKANG_MUAT) +
            (SEBARAN.NOV / 100 * RKT.LANGSIR_TUKANG_MUAT) + (SEBARAN.DEC / 100 * RKT.LANGSIR_TUKANG_MUAT)  COST_SETAHUN,
            '".$this->_userName."', CURRENT_TIMESTAMP
        FROM TR_RKT_PANEN RKT
        LEFT JOIN TM_SEBARAN_PRODUKSI SEBARAN ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET
          AND RKT.BA_CODE = SEBARAN.BA_CODE AND SEBARAN.DELETE_USER IS NULL
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    $components['langsir_transport'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, SUB_COST_ELEMENT_DESC,
        QTY_JAN, QTY_FEB, QTY_MAR, QTY_APR, QTY_MAY, QTY_JUN, QTY_JUL, QTY_AUG, QTY_SEP, QTY_OCT, QTY_NOV, QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE,
         'TM' AS TIPE_TRANSAKSI,
         '5101030404-2' ACTIVITY_CODE,
         'LANGSIR' ACTIVITY_DESC,
         'TRANSPORT' COST_ELEMENT,
          NULL, VH.VRA_SUB_CAT_DESCRIPTION,
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
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.JAN/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_JAN,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.FEB/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_FEB,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.MAR/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_MAR,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.APR/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_APR,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.MAY/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_MAY,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.JUN/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_JUN,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.JUL/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_JUL,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.AUG/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_AUG,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.SEP/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_SEP,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.OCT/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_OCT,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.NOV/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_NOV,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*(DIS.DEC/DIS.TOTAL)*VN.RP_KG)*1000,0) COST_DEC,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*VN.RP_KG)*1000/VN.RP_HM,0) QTY_SETAHUN,
          NVL((RKT.TON*(RKT.PERSEN_LANGSIR/100)*VN.RP_KG)*1000 ,0) COST_SETAHUN,
          '".$this->_userName."', CURRENT_TIMESTAMP
      FROM TR_RKT_PANEN RKT
      LEFT JOIN (
        SELECT BG.PERIOD_BUDGET, ORG.REGION_CODE, BG.BA_CODE,BG.AFD_CODE,BG.BLOCK_CODE,
          SUM(BG.JAN) JAN, SUM(BG.FEB) FEB, SUM(BG.MAR) MAR, SUM(BG.APR) APR, SUM(BG.MAY) MAY, SUM(BG.JUN) JUN, 
          SUM(BG.JUL) JUL, SUM(BG.AUG) AUG, SUM(BG.SEP) SEP, SUM(BG.OCT) OCT, SUM(BG.NOV) NOV, SUM(BG.DEC) DEC, 
          SUM(BG.JAN)+SUM(BG.FEB)+SUM(BG.MAR)+SUM(BG.APR)+SUM(BG.MAY)+SUM(BG.JUN)+
          SUM(BG.JUL)+SUM(BG.AUG)+SUM(BG.SEP)+SUM(BG.OCT)+SUM(BG.NOV)+SUM(BG.DEC) TOTAL
        FROM TR_PRODUKSI_PERIODE_BUDGET BG
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = BG.BA_CODE   
        WHERE BG.DELETE_USER IS NULL AND to_char(BG.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
        AND BG.BA_CODE = '".$params['key_find']."' AND BG.DELETE_USER IS NULL
        GROUP BY BG.PERIOD_BUDGET, ORG.REGION_CODE, BG.BA_CODE,BG.AFD_CODE,BG.BLOCK_CODE
      ) DIS ON RKT.PERIOD_BUDGET = DIS.PERIOD_BUDGET AND RKT.BA_CODE = DIS.BA_CODE 
        AND RKT.AFD_CODE = DIS.AFD_CODE AND RKT.BLOCK_CODE = DIS.BLOCK_CODE
      LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      LEFT JOIN TN_PANEN_PREMI_LANGSIR VN ON VN.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND VN.BA_CODE = RKT.BA_CODE
        AND VN.RP_HM > 0 AND VN.RP_KG > 0
      JOIN TM_VRA VH ON VH.VRA_CODE = VN.VRA_CODE
      WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL
        AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
        AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    $components['langsir_transport_vra'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK(
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM,
        SUB_COST_ELEMENT_DESC,
        INSERT_USER, INSERT_TIME,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN
      )
      SELECT 
      PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, 
      'TM', '5101030404-3', 'LANGSIR - VRA', 'TRANSPORT', UOM, TYPE,
      '".$this->_userName."', CURRENT_TIMESTAMP,
      JAN/TOTAL*HM_KM,
      FEB/TOTAL*HM_KM,
      MAR/TOTAL*HM_KM,
      APR/TOTAL*HM_KM,
      MAY/TOTAL*HM_KM,
      JUN/TOTAL*HM_KM,
      JUL/TOTAL*HM_KM,
      AUG/TOTAL*HM_KM,
      SEP/TOTAL*HM_KM,
      OCT/TOTAL*HM_KM,
      NOV/TOTAL*HM_KM,
      DEC/TOTAL*HM_KM,
      JAN/TOTAL*PRICE_HM_KM,
      FEB/TOTAL*PRICE_HM_KM,
      MAR/TOTAL*PRICE_HM_KM,
      APR/TOTAL*PRICE_HM_KM,
      MAY/TOTAL*PRICE_HM_KM,
      JUN/TOTAL*PRICE_HM_KM,
      JUL/TOTAL*PRICE_HM_KM,
      AUG/TOTAL*PRICE_HM_KM,
      SEP/TOTAL*PRICE_HM_KM,
      OCT/TOTAL*PRICE_HM_KM,
      NOV/TOTAL*PRICE_HM_KM,
      DEC/TOTAL*PRICE_HM_KM,
      HM_KM, PRICE_HM_KM
      FROM (
        SELECT DIS.*,  VRADIS.HM_KM, VRADIS.PRICE_HM_KM, VRADIS.VRA_CODE, VRADIS.TYPE,VRADIS.UOM
        FROM (
          SELECT RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE,RKT.AFD_CODE,
              SUM(RKT.JAN) JAN, SUM(RKT.FEB) FEB, SUM(RKT.MAR) MAR, SUM(RKT.APR) APR, SUM(RKT.MAY) MAY, SUM(RKT.JUN) JUN, 
              SUM(RKT.JUL) JUL, SUM(RKT.AUG) AUG, SUM(RKT.SEP) SEP, SUM(RKT.OCT) OCT, SUM(RKT.NOV) NOV, SUM(RKT.DEC) DEC, 
              SUM(RKT.JAN)+SUM(RKT.FEB)+SUM(RKT.MAR)+SUM(RKT.APR)+SUM(RKT.MAY)+SUM(RKT.JUN)+
              SUM(RKT.JUL)+SUM(RKT.AUG)+SUM(RKT.SEP)+SUM(RKT.OCT)+SUM(RKT.NOV)+SUM(RKT.DEC) TOTAL
          FROM TR_PRODUKSI_PERIODE_BUDGET RKT
          JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE   
          WHERE RKT.DELETE_USER IS NULL AND to_char(RKT.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
          GROUP BY RKT.PERIOD_BUDGET, ORG.REGION_CODE, RKT.BA_CODE,RKT.AFD_CODE
        ) DIS
        JOIN (
            SELECT BA_CODE, PERIOD_BUDGET, SUM(HM_KM) HM_KM, SUM(PRICE_HM_KM) PRICE_HM_KM, LOCATION_CODE
            , VRADIS.VRA_CODE, V.TYPE, V.UOM
             FROM TR_RKT_VRA_DISTRIBUSI VRADIS  
             JOIN TM_VRA V ON V.VRA_CODE = VRADIS.VRA_CODE
             WHERE VRADIS.TIPE_TRANSAKSI = 'NON_INFRA' 
             AND VRADIS.ACTIVITY_CODE = '51600' AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
             AND VRADIS.BA_CODE = '".$params['key_find']."'
            GROUP BY BA_CODE, PERIOD_BUDGET, LOCATION_CODE,VRADIS.VRA_CODE, V.TYPE,V.UOM
        ) VRADIS ON VRADIS.PERIOD_BUDGET = DIS.PERIOD_BUDGET AND VRADIS.BA_CODE = DIS.BA_CODE
          AND VRADIS.LOCATION_CODE = DIS.AFD_CODE
        ORDER BY DIS.BA_CODE, DIS.AFD_CODE
      )
      ORDER BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, VRA_CODE
    ";

    $components['perkerasan_jalan_total_setahun'] = "
      UPDATE BPS_PROD.TR_RKT_PK
      SET PLAN_SETAHUN = PLAN_JAN+PLAN_FEB+PLAN_MAR+PLAN_APR+PLAN_MAY+PLAN_JUN+PLAN_JUL+PLAN_AUG+PLAN_SEP+PLAN_OCT+PLAN_NOV+PLAN_DEC
      WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$period_budget."' and BA_CODE = '".$ba_code."'
    ";

    // PERKERASAN JALAN
    $components['perkerasan_jalan_material'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, SUB_COST_ELEMENT_DESC,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
        SELECT RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,
            RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
            'TM' AS TIPE_TRANSAKSI,
            RKT.ACTIVITY_CODE,
            ACT.DESCRIPTION AS ACTIVITY_DESC,
            RKT.COST_ELEMENT,
            MAT.UOM, MAT.MATERIAL_NAME,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JAN QTY_JAN,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_FEB QTY_FEB,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_MAR QTY_MAR,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_APR QTY_APR,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_MAY QTY_MAY,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JUN QTY_JUN,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JUL QTY_JUL,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_AUG QTY_AUG,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_SEP QTY_SEP,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_OCT QTY_OCT,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_NOV QTY_NOV,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_DEC QTY_DEC,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JAN*RH.PRICE COST_JAN,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_FEB*RH.PRICE COST_FEB,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_MAR*RH.PRICE COST_MAR,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_APR*RH.PRICE COST_APR,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_MAY*RH.PRICE COST_MAY,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JUN*RH.PRICE COST_JUN,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_JUL*RH.PRICE COST_JUL,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_AUG*RH.PRICE COST_AUG,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_SEP*RH.PRICE COST_SEP,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_OCT*RH.PRICE COST_OCT,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_NOV*RH.PRICE COST_NOV,
            RHC.MATERIAL_QTY/1000*RKT.PLAN_DEC*RH.PRICE COST_DEC,
            (RHC.MATERIAL_QTY/1000) * (
              RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+
              RKT.PLAN_MAY+RKT.PLAN_JUN+RKT.PLAN_JUL+RKT.PLAN_AUG+
              RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC
            ) QTY_SETAHUN,
            (RHC.MATERIAL_QTY/1000) * (
              RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+
              RKT.PLAN_MAY+RKT.PLAN_JUN+RKT.PLAN_JUL+RKT.PLAN_AUG+
              RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC
            ) * RH.PRICE COST_SETAHUN,
            '".$this->_userName."', CURRENT_TIMESTAMP
        FROM TR_RKT_PK_COST_ELEMENT RKT
        LEFT JOIN TR_RKT_PK RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
        LEFT JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT_INDUK.PERIOD_BUDGET
          AND RH.ACTIVITY_CODE = RKT_INDUK.ACTIVITY_CODE
          AND RKT_INDUK.BA_CODE = RH.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
          AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT_INDUK.JARAK
          AND RKT_INDUK.BA_CODE = RHC.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT_INDUK.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS1 = 'TM' AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN' 
          AND RKT.SUMBER_BIAYA = 'INTERNAL' AND RKT.COST_ELEMENT = 'MATERIAL'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    $components['perkerasan_jalan_contract'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT RKT.PERIOD_BUDGET,
        ORG.REGION_CODE,
        RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
        'TM' AS TIPE_TRANSAKSI,
        RKT.ACTIVITY_CODE,
        ACT.DESCRIPTION AS ACTIVITY_DESC,
        RKT.COST_ELEMENT,
        ACT.UOM,
        RKT_INDUK.PLAN_JAN QTY_JAN,RKT_INDUK.PLAN_FEB QTY_FEB,RKT_INDUK.PLAN_MAR QTY_MAR,
        RKT_INDUK.PLAN_APR QTY_APR,RKT_INDUK.PLAN_MAY QTY_MAY,RKT_INDUK.PLAN_JUN QTY_JUN,
        RKT_INDUK.PLAN_JUL QTY_JUL,RKT_INDUK.PLAN_AUG QTY_AUG,RKT_INDUK.PLAN_SEP QTY_SEP,
        RKT_INDUK.PLAN_OCT QTY_OCT,RKT_INDUK.PLAN_NOV QTY_NOV,RKT_INDUK.PLAN_DEC QTY_DEC,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JAN COST_JAN,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_FEB COST_FEB,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_MAR COST_MAR,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_APR COST_APR,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_MAY COST_MAY,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JUN COST_JUN,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JUL COST_JUL,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_AUG COST_AUG,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_SEP COST_SEP,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_OCT COST_OCT,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_NOV COST_NOV,
        RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_DEC COST_DEC,
        RKT_INDUK.PLAN_JAN+RKT_INDUK.PLAN_FEB+RKT_INDUK.PLAN_MAR+RKT_INDUK.PLAN_APR+
        RKT_INDUK.PLAN_MAY+RKT_INDUK.PLAN_JUN+RKT_INDUK.PLAN_JUL+RKT_INDUK.PLAN_AUG+
        RKT_INDUK.PLAN_SEP+RKT_INDUK.PLAN_OCT+RKT_INDUK.PLAN_NOV+RKT_INDUK.PLAN_DEC QTY_SETAHUN,
        (RHC.EXTERNAL_PRICE/1000) * (RKT_INDUK.PLAN_JAN+RKT_INDUK.PLAN_FEB+RKT_INDUK.PLAN_MAR+RKT_INDUK.PLAN_APR+
        RKT_INDUK.PLAN_MAY+RKT_INDUK.PLAN_JUN+RKT_INDUK.PLAN_JUL+RKT_INDUK.PLAN_AUG+
        RKT_INDUK.PLAN_SEP+RKT_INDUK.PLAN_OCT+RKT_INDUK.PLAN_NOV+RKT_INDUK.PLAN_DEC) COST_SETAHUN,
        '".$this->_userName."', CURRENT_TIMESTAMP
      FROM TR_RKT_PK_COST_ELEMENT RKT
      JOIN TR_RKT_PK RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
      JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
      JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT_INDUK.PERIOD_BUDGET
        AND RH.ACTIVITY_CODE = RKT_INDUK.ACTIVITY_CODE AND RH.BA_CODE = RKT.BA_CODE
      JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
        AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
      JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT_INDUK.JARAK 
        AND RHC.BA_CODE = RKT.BA_CODE
      WHERE RKT.DELETE_USER IS NULL AND RKT_INDUK.FLAG_TEMP IS NULL
        AND RKT.MATURITY_STAGE_SMS1 = 'TM' AND RKT_INDUK.JENIS_PEKERJAAN = 'PERULANGAN' 
        AND RKT.SUMBER_BIAYA = 'EXTERNAL' AND RKT.COST_ELEMENT = 'CONTRACT'
        AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
        AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    $components['perkerasan_jalan_transport'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM,SUB_COST_ELEMENT_DESC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        COST_SETAHUN,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        QTY_SETAHUN, 
        INSERT_USER, INSERT_TIME
      )
      SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          'TRANSPORT',
          COALESCE(V1.UOM,V2.UOM,V3.UOM,V4.UOM) UOM,
          COALESCE(V1.VRA_SUB_CAT_DESCRIPTION,V2.VRA_SUB_CAT_DESCRIPTION,V3.VRA_SUB_CAT_DESCRIPTION,V4.VRA_SUB_CAT_DESCRIPTION) VRA_NAME,
          RKT.PLAN_JAN*RH.RATE/1000 JAN, RKT.PLAN_FEB*RH.RATE/1000 FEB, RKT.PLAN_MAR*RH.RATE/1000 MAR, 
          RKT.PLAN_APR*RH.RATE/1000 APR, RKT.PLAN_MAY*RH.RATE/1000 MAY, RKT.PLAN_JUN*RH.RATE/1000 JUN, 
          RKT.PLAN_JUL*RH.RATE/1000 JUL, RKT.PLAN_AUG*RH.RATE/1000 AUG, RKT.PLAN_SEP*RH.RATE/1000 SEP, 
          RKT.PLAN_OCT*RH.RATE/1000 OCT, RKT.PLAN_NOV*RH.RATE/1000 NOV, RKT.PLAN_DEC*RH.RATE/1000 DEC, 
          RKT.PLAN_SETAHUN*RH.RATE/1000,
          RKT.PLAN_JAN/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_FEB/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_MAR/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_APR/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_MAY/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_JUN/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_JUL/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_AUG/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_SEP/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_OCT/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_NOV/RKT.PLAN_SETAHUN*RH.HM_KM,
          RKT.PLAN_DEC/RKT.PLAN_SETAHUN*RH.HM_KM,
          RH.HM_KM,
          '".$this->_userName."', CURRENT_TIMESTAMP
      FROM TR_RKT_PK RKT
      LEFT JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
      LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      JOIN (
        SELECT PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, JARAK_RANGE
        , CASE
            WHEN COMPONENT LIKE '%DT_PRICE%' THEN 'DT010'
            WHEN COMPONENT LIKE '%EXCAV_PRICE%' THEN 'EX011'
            WHEN COMPONENT LIKE '%COMPACTOR_PRICE%' THEN 'VC010'
            ELSE 'GD010'
          END VRA_CODE
        , HM_KM
        , RATE FROM (
          SELECT PERIOD_BUDGET,BA_CODE,ACTIVITY_CODE,JARAK_RANGE,DT_PRICE,EXCAV_PRICE,COMPACTOR_PRICE,GRADER_PRICE
          ,DT_TRIP,EXCAV_HM,COMPACTOR_HM,GRADER_HM
          FROM TN_PERKERASAN_JALAN_HARGA WHERE ACTIVITY_CODE =  '20311'
          AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."' 
          AND BA_CODE = '".$params['key_find']."'
        ) A
        UNPIVOT
        (
          (HM_KM, RATE) FOR COMPONENT IN (
            (DT_TRIP, DT_PRICE),
            (EXCAV_HM, EXCAV_PRICE),
            (COMPACTOR_HM, COMPACTOR_PRICE),
            (GRADER_HM, GRADER_PRICE)
          )
        )
      ) RH ON RH.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RH.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        AND RH.JARAK_RANGE = RKT.JARAK
      JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT.JARAK
      JOIN TM_VRA V1 ON V1.VRA_CODE = RH.VRA_CODE
      JOIN TM_VRA V2 ON V2.VRA_CODE = RH.VRA_CODE
      JOIN TM_VRA V3 ON V3.VRA_CODE = RH.VRA_CODE
      JOIN TM_VRA V4 ON V4.VRA_CODE = RH.VRA_CODE
      WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL
        AND RKT.MATURITY_STAGE_SMS1 = 'TM' AND RKT.JENIS_PEKERJAAN = 'PERULANGAN' 
        AND RKT.SUMBER_BIAYA = 'INTERNAL'
        AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
        AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    // PENGANGKUTAN INTERNAL TBS - VRA
    $components['5101030504-1'] ="
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        SUB_COST_ELEMENT_DESC, KETERANGAN,
        UOM,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT RKTFIRST.PERIOD_BUDGET,
        RKTFIRST.REGION_CODE,
        RKTFIRST.BA_CODE,
        RKTFIRST.AFD_CODE,
        RKTFIRST.BLOCK_CODE,
        'TM' AS TIPE_TRANSAKSI,
        '5101030504-1' AS ACTIVITY_CODE, 
        'PENGANGKUTAN INTERNAL TBS - VRA' AS ACTIVITY_DESC,
        'TRANSPORT',
        RKTFIRST.VRA_CODE AS SUB_COST_ELEMENT,
        '' KETERANGAN,
        RKTFIRST.UOM,
        (RKTFIRST.JAN/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJAN, (RKTFIRST.FEB/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISFEB,
        (RKTFIRST.MAR/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISMAR, (RKTFIRST.APR/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISAPR,
        (RKTFIRST.MAY/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISMAY, (RKTFIRST.JUN/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJUN,
        (RKTFIRST.JUL/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISJUL, (RKTFIRST.AUG/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISAUG,
        (RKTFIRST.SEP/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISSEP, (RKTFIRST.OCT/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISOCT,
        (RKTFIRST.NOV/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISNOV, (RKTFIRST.DEC/RKTSEC.TOTAL*RKTFIRST.HM_KM) DISDEC,
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
        RKTFIRST.HM_KM DIS_SETAHUN,
        RKTFIRST.PRICE_HM_KM COS_SETAHUN,
        '".$this->_userName."' AS INSERT_USER, SYSDATE AS INSERT_TIME
      FROM (
        SELECT RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,  
            RKT.BA_CODE,
            RKT.AFD_CODE,
            RKT.BLOCK_CODE,
            TMVRA.VRA_CODE,
            TMVRA.VRA_SUB_CAT_DESCRIPTION, 
            TMVRA.UOM,
            RKT.JAN,RKT.FEB,RKT.MAR,RKT.APR,RKT.MAY,RKT.JUN,RKT.JUL,RKT.AUG,RKT.SEP,RKT.OCT,RKT.NOV,RKT.DEC,
            VRADIS.HM_KM,
            VRADIS.PRICE_HM_KM
        FROM TR_PRODUKSI_PERIODE_BUDGET RKT
        LEFT JOIN (
            SELECT BA_CODE, PERIOD_BUDGET, VRA_CODE, SUM(HM_KM) HM_KM, SUM(PRICE_HM_KM) PRICE_HM_KM 
             FROM TR_RKT_VRA_DISTRIBUSI VRADIS  WHERE VRADIS.TIPE_TRANSAKSI = 'NON_INFRA' 
             AND VRADIS.ACTIVITY_CODE = '5101030504' AND EXTRACT(YEAR FROM VRADIS.PERIOD_BUDGET) = '".$params['budgetperiod']."'
            GROUP BY BA_CODE, PERIOD_BUDGET, VRA_CODE
        ) VRADIS ON VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND VRADIS.BA_CODE = RKT.BA_CODE
        LEFT JOIN TM_VRA TMVRA ON TMVRA.VRA_CODE = VRADIS.VRA_CODE 
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE   
        WHERE RKT.DELETE_USER IS NULL $where 
      ) RKTFIRST
      LEFT JOIN (              
        SELECT RKT.PERIOD_BUDGET,
        RKT.BA_CODE,
        (
          SUM(RKT.JAN)+SUM(RKT.FEB)+SUM(RKT.MAR)+SUM(RKT.APR)+SUM(RKT.MAY)+SUM(RKT.JUN)+
          SUM(RKT.JUL)+SUM(RKT.AUG)+SUM(RKT.SEP)+SUM(RKT.OCT)+SUM(RKT.NOV)+SUM (RKT.DEC) 
        ) TOTAL
        FROM TR_PRODUKSI_PERIODE_BUDGET RKT
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRADIS ON VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
            AND VRADIS.BA_CODE = RKT.BA_CODE AND VRADIS.LOCATION_CODE = RKT.AFD_CODE
            AND VRADIS.TIPE_TRANSAKSI = 'NON_INFRA' AND VRADIS.ACTIVITY_CODE = '5101030504'
        WHERE RKT.DELETE_USER IS NULL
        $where
        GROUP BY RKT.PERIOD_BUDGET, RKT.BA_CODE
      ) RKTSEC ON RKTFIRST.PERIOD_BUDGET = RKTSEC.PERIOD_BUDGET AND RKTFIRST.BA_CODE = RKTSEC.BA_CODE
    ";

    // PANEN - ANGKUT - TRANSPORT TBS INTERNAL
    $components['5101030504-2'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
        SELECT  RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,
            RKT.BA_CODE,RKT.AFD_CODE, RKT.BA_CODE,
            'TM' AS TIPE_TRANSAKSI,
            '5101030504-2' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
            'TRANSPORT TBS INTERNAL - PANEN' AS ACTIVITY_DETAIL,
            'TRANSPORT',
            0,0,0,0,0,0,0,0,0,0,0,0,
            (SEBARAN.JAN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
            (SEBARAN.FEB / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
            (SEBARAN.MAR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
            (SEBARAN.APR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
            (SEBARAN.MAY / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
            (SEBARAN.JUN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
            (SEBARAN.JUL / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
            (SEBARAN.AUG / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
            (SEBARAN.SEP / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
            (SEBARAN.OCT / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
            (SEBARAN.NOV / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
            (SEBARAN.DEC / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
            0 ,
            (SEBARAN.JAN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.FEB / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.MAR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.APR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.MAY / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.JUN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.JUL / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.AUG / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.SEP / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.OCT / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.NOV / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.DEC / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT),
            '".$this->_userName."' AS INSERT_USER, SYSDATE AS INSERT_TIME
        FROM TR_RKT_PANEN RKT
        LEFT JOIN (              
          SELECT RKT.PERIOD_BUDGET,
          RKT.BA_CODE,
          SUM(RKT.JAN) JAN, SUM(RKT.FEB) FEB, SUM(RKT.MAR) MAR,
          SUM(RKT.APR) APR, SUM(RKT.MAY) MAY, SUM(RKT.JUN) JUN,
          SUM(RKT.JUL) JUL, SUM(RKT.AUG) AUG, SUM(RKT.SEP) SEP,
          SUM(RKT.OCT) OCT, SUM(RKT.NOV) NOV, SUM(RKT.DEC) DEC,
          (
            SUM(RKT.JAN)+SUM(RKT.FEB)+SUM(RKT.MAR)+SUM(RKT.APR)+SUM(RKT.MAY)+SUM(RKT.JUN)+
            SUM(RKT.JUL)+SUM(RKT.AUG)+SUM(RKT.SEP)+SUM(RKT.OCT)+SUM(RKT.NOV)+SUM (RKT.DEC) 
          ) TOTAL
          FROM TR_PRODUKSI_PERIODE_BUDGET RKT
          LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
          LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRADIS ON VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
              AND VRADIS.BA_CODE = RKT.BA_CODE AND VRADIS.LOCATION_CODE = RKT.AFD_CODE
              AND VRADIS.TIPE_TRANSAKSI = 'NON_INFRA' AND VRADIS.ACTIVITY_CODE = '5101030504'
          WHERE RKT.DELETE_USER IS NULL
          $where
          GROUP BY RKT.PERIOD_BUDGET, RKT.BA_CODE
        ) SEBARAN ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET AND RKT.BA_CODE = SEBARAN.BA_CODE
        LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL
          AND RKT.FLAG_TEMP IS NULL
          AND RKT.SUMBER_BIAYA_UNIT = 'INTERNAL'
        $where
    ";

    // PANEN - ANGKUT - TRANSPORT TBS EKSTERNAL
    $components['5101030605'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
        SELECT  RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,
            RKT.BA_CODE,RKT.AFD_CODE, RKT.BA_CODE,
            'TM' AS TIPE_TRANSAKSI,
            '5101030605' AS ACTIVITY_CODE, -- BY ADI 16/11/2014
            'TRANSPORT TBS EKSTERNAL - PANEN' AS ACTIVITY_DETAIL,
            0,0,0,0,0,0,0,0,0,0,0,0,
            (SEBARAN.JAN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JAN,
            (SEBARAN.FEB / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_FEB,
            (SEBARAN.MAR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAR,
            (SEBARAN.APR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_APR,
            (SEBARAN.MAY / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_MAY,
            (SEBARAN.JUN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUN,
            (SEBARAN.JUL / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_JUL,
            (SEBARAN.AUG / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_AUG,
            (SEBARAN.SEP / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_SEP,
            (SEBARAN.OCT / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_OCT,
            (SEBARAN.NOV / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_NOV,
            (SEBARAN.DEC / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT) AS COST_DEC,
            0 ,
            (SEBARAN.JAN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.FEB / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.MAR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.APR / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.MAY / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.JUN / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.JUL / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.AUG / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.SEP / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.OCT / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+
            (SEBARAN.NOV / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT)+(SEBARAN.DEC / SEBARAN.TOTAL * RKT.ANGKUT_TBS_RP_ANGKUT),
            '".$this->_userName."' AS INSERT_USER, SYSDATE AS INSERT_TIME
        FROM TR_RKT_PANEN RKT
        LEFT JOIN (              
          SELECT RKT.PERIOD_BUDGET,
          RKT.BA_CODE,
          SUM(RKT.JAN) JAN, SUM(RKT.FEB) FEB, SUM(RKT.MAR) MAR,
          SUM(RKT.APR) APR, SUM(RKT.MAY) MAY, SUM(RKT.JUN) JUN,
          SUM(RKT.JUL) JUL, SUM(RKT.AUG) AUG, SUM(RKT.SEP) SEP,
          SUM(RKT.OCT) OCT, SUM(RKT.NOV) NOV, SUM(RKT.DEC) DEC,
          (
            SUM(RKT.JAN)+SUM(RKT.FEB)+SUM(RKT.MAR)+SUM(RKT.APR)+SUM(RKT.MAY)+SUM(RKT.JUN)+
            SUM(RKT.JUL)+SUM(RKT.AUG)+SUM(RKT.SEP)+SUM(RKT.OCT)+SUM(RKT.NOV)+SUM (RKT.DEC) 
          ) TOTAL
          FROM TR_PRODUKSI_PERIODE_BUDGET RKT
          LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
          LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRADIS ON VRADIS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
              AND VRADIS.BA_CODE = RKT.BA_CODE AND VRADIS.LOCATION_CODE = RKT.AFD_CODE
              AND VRADIS.TIPE_TRANSAKSI = 'NON_INFRA'
          WHERE RKT.DELETE_USER IS NULL
          $where
          GROUP BY RKT.PERIOD_BUDGET, RKT.BA_CODE
        ) SEBARAN ON RKT.PERIOD_BUDGET = SEBARAN.PERIOD_BUDGET AND RKT.BA_CODE = SEBARAN.BA_CODE
        LEFT JOIN TM_ORGANIZATION ORG
          ON ORG.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL
          AND RKT.FLAG_TEMP IS NULL
          AND RKT.SUMBER_BIAYA_UNIT = 'EXTERNAL'
        $where
    ";

    // PREMI SUPIR
    $components['5101030504-3'] = "
      INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
        SELECT
          RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE,
          RKT.AFD_CODE,
          RKT.BLOCK_CODE,
          'TM' AS TIPE_TRANSAKSI,
          '5101030504-3' AS ACTIVITY_CODE,-- BY ADI 16/11/2014
          'PREMI SUPIR' AS ACTIVITY_DETAIL,
          0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
          (SEBARAN.JAN * RKT.SUPIR_PREMI) AS COST_JAN,
          (SEBARAN.FEB * RKT.SUPIR_PREMI) AS COST_FEB,
          (SEBARAN.MAR * RKT.SUPIR_PREMI) AS COST_MAR,
          (SEBARAN.APR * RKT.SUPIR_PREMI) AS COST_APR,
          (SEBARAN.MAY * RKT.SUPIR_PREMI) AS COST_MAY,
          (SEBARAN.JUN * RKT.SUPIR_PREMI) AS COST_JUN,
          (SEBARAN.JUL * RKT.SUPIR_PREMI) AS COST_JUL,
          (SEBARAN.AUG * RKT.SUPIR_PREMI) AS COST_AUG,
          (SEBARAN.SEP * RKT.SUPIR_PREMI) AS COST_SEP,
          (SEBARAN.OCT * RKT.SUPIR_PREMI) AS COST_OCT,
          (SEBARAN.NOV * RKT.SUPIR_PREMI) AS COST_NOV,
          (SEBARAN.DEC * RKT.SUPIR_PREMI) AS COST_DEC,
          0 , 
          (SEBARAN.JAN * RKT.SUPIR_PREMI)+(SEBARAN.FEB * RKT.SUPIR_PREMI)+(SEBARAN.MAR * RKT.SUPIR_PREMI)+
          (SEBARAN.APR * RKT.SUPIR_PREMI)+(SEBARAN.MAY * RKT.SUPIR_PREMI)+(SEBARAN.JUN * RKT.SUPIR_PREMI)+
          (SEBARAN.JUL * RKT.SUPIR_PREMI)+(SEBARAN.AUG * RKT.SUPIR_PREMI)+(SEBARAN.SEP * RKT.SUPIR_PREMI)+
          (SEBARAN.OCT * RKT.SUPIR_PREMI)+(SEBARAN.NOV * RKT.SUPIR_PREMI)+(SEBARAN.DEC * RKT.SUPIR_PREMI),
          '".$this->_userName."', CURRENT_TIMESTAMP
        FROM
          TR_RKT_PANEN RKT
        LEFT JOIN(
            SELECT
              PERIOD_BUDGET,
              BA_CODE,
              AFD_CODE,
              BLOCK_CODE,
              JAN / TOTAL AS JAN,
              FEB / TOTAL AS FEB,
              MAR / TOTAL AS MAR,
              APR / TOTAL AS APR,
              MAY / TOTAL AS MAY,
              JUN / TOTAL AS JUN,
              JUL / TOTAL AS JUL,
              AUG / TOTAL AS AUG,
              SEP / TOTAL AS SEP,
              OCT / TOTAL AS OCT,
              NOV / TOTAL AS NOV,
              DEC / TOTAL AS DEC,
              TOTAL
            FROM
              (
                SELECT
                  norma.PERIOD_BUDGET PERIOD_BUDGET,
                  norma.BA_CODE BA_CODE,
                  norma.AFD_CODE AFD_CODE,
                  norma.BLOCK_CODE BLOCK_CODE,
                  SUM( norma.JAN ) JAN,
                  SUM( norma.FEB ) FEB,
                  SUM( norma.MAR ) MAR,
                  SUM( norma.APR ) APR,
                  SUM( norma.MAY ) MAY,
                  SUM( norma.JUN ) JUN,
                  SUM( norma.JUL ) JUL,
                  SUM( norma.AUG ) AUG,
                  SUM( norma.SEP ) SEP,
                  SUM( norma.OCT ) OCT,
                  SUM( norma.NOV ) NOV,
                  SUM( norma.DEC ) DEC,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total,
                  SUM(( norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN )) total_sms1,
                  SUM(( norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC )) total_sms2
                FROM
                  TR_PRODUKSI_PERIODE_BUDGET norma
                LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON
                  norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                  AND norma.BA_CODE = thn_berjalan.BA_CODE
                  AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                  AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
                WHERE
                  norma.DELETE_USER IS NULL
                  AND TO_CHAR( norma.PERIOD_BUDGET, 'RRRR')= '".$params['budgetperiod']."'
                  AND norma.BA_CODE = '".$params['key_find']."'
                GROUP BY
                  norma.PERIOD_BUDGET,
                  norma.BA_CODE,
                  norma.AFD_CODE,
                  norma.BLOCK_CODE
              )
          ) SEBARAN ON
          sebaran.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND sebaran.BA_CODE = RKT.BA_CODE
          AND sebaran.AFD_CODE = RKT.AFD_CODE
          AND sebaran.BLOCK_CODE = RKT.BLOCK_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON
          ORG.BA_CODE = RKT.BA_CODE
        WHERE
          RKT.DELETE_USER IS NULL
          AND RKT.FLAG_TEMP IS NULL
          AND TO_CHAR( RKT.PERIOD_BUDGET, 'RRRR')= '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
    ";

    $components['alat_panen'] = "
        INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK(
          PERIOD_BUDGET,
          REGION_CODE,
          BA_CODE,
          AFD_CODE,
          BLOCK_CODE,
          TIPE_TRANSAKSI,
          ACTIVITY_CODE,
          ACTIVITY_DESC,
          COST_ELEMENT,
          SUB_COST_ELEMENT,
          SUB_COST_ELEMENT_DESC,
          UOM,
          QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
          COST_JAN,COST_FEB,COST_MAR,COST_APR,COST_MAY,COST_JUN,COST_JUL,COST_AUG,COST_SEP,COST_OCT,COST_NOV,COST_DEC,
          QTY_SETAHUN, COST_SETAHUN,
          INSERT_USER,INSERT_TIME
        )
        SELECT 
          MPP.PERIOD_BUDGET, ORG.REGION_CODE, MPP.BA_CODE, MPP.AFD_CODE, MPP.BLOCK_CODE, 'TM'
          , '5101030103', 'ALAT PANEN', NULL, NULL, MAT.MATERIAL_NAME, MAT.UOM
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.JAN QTY_JAN
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.FEB QTY_FEB
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.MAR QTY_MAR
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.APR QTY_APR
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.MAY QTY_MAY
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.JUN QTY_JUN
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.JUL QTY_JUL
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.AUG QTY_AUG
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.SEP QTY_SEP
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.OCT QTY_OCT
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.NOV QTY_NOV
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN)*DIS.DEC QTY_DEC
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.JAN COST_JAN
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.FEB COST_FEB
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.MAR COST_MAR
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.APR COST_APR
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.MAY COST_MAY
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.JUN COST_JUN
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.JUL COST_JUL
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.AUG COST_AUG
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.SEP COST_SEP
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.OCT COST_OCT
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.NOV COST_NOV
          , HVT.TOTAL*MPP.MPP_PANEN*DIS.DEC COST_DEC
          , (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.JAN)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.FEB)
          + (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.MAR)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.APR)
          + (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.MAY)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.JUN)
          + (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.JUL)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.AUG)
          + (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.SEP)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.OCT)
          + (HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.NOV)+(HVT.TOTAL/HVT.PRICE*MPP.MPP_PANEN*DIS.DEC) QTY_SETAHUN
          , (HVT.TOTAL*MPP.MPP_PANEN*DIS.JAN) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.FEB)
          + (HVT.TOTAL*MPP.MPP_PANEN*DIS.MAR) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.APR)
          + (HVT.TOTAL*MPP.MPP_PANEN*DIS.MAY) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.JUN)
          + (HVT.TOTAL*MPP.MPP_PANEN*DIS.JUL) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.AUG)
          + (HVT.TOTAL*MPP.MPP_PANEN*DIS.SEP) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.OCT)
          + (HVT.TOTAL*MPP.MPP_PANEN*DIS.NOV) + (HVT.TOTAL*MPP.MPP_PANEN*DIS.DEC) COST_SETAHUN
          , '".$this->_userName."', CURRENT_TIMESTAMP
        FROM
        (
          SELECT
           HV.PERIOD_BUDGET, HV.BA_CODE, HV.AFD_CODE, HV.BLOCK_CODE
           -- , NVL(SUM(HV.BIAYA_PEMANEN_HK)/HKE.HKE,0) MPP_PANEN
           -- , NVL(HVTS.PRICE_SUM*SUM(HV.BIAYA_PEMANEN_HK)/HKE.HKE,0) TOTAL_BIAYA_ALAT_PANEN
           , SUM(HV.BIAYA_ALAT_PANEN_RP_TOTAL)/HVTS.PRICE_SUM MPP_PANEN
           , SUM(HV.BIAYA_ALAT_PANEN_RP_TOTAL) TOTAL_BIAYA_ALAT_PANEN
          FROM TR_RKT_PANEN HV
          JOIN (
            SELECT DISTINCT PERIOD_BUDGET, BA_CODE, MAX(HKE) OVER(PARTITION BY PERIOD_BUDGET, BA_CODE) HKE
            FROM TM_CHECKROLL_HK HKE 
            WHERE EXTRACT(YEAR FROM HKE.PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND HKE.BA_CODE = '".$params['key_find']."'
          ) HKE ON HKE.PERIOD_BUDGET = HV.PERIOD_BUDGET AND HKE.BA_CODE = HV.BA_CODE
          JOIN TN_ALAT_KERJA_PANEN_SUM HVTS ON HVTS.PERIOD_BUDGET = HV.PERIOD_BUDGET AND HVTS.BA_CODE = HV.BA_CODE
          WHERE EXTRACT(YEAR FROM HV.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND HV.BA_CODE = '".$params['key_find']."'
          GROUP BY HKE.BA_CODE, HKE.HKE, HVTS.PRICE_SUM, HV.PERIOD_BUDGET, HV.BA_CODE, HV.AFD_CODE, HV.BLOCK_CODE 
        ) MPP
        JOIN (
          SELECT
            PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE,
            JAN/TOTAL AS JAN, FEB/TOTAL AS FEB, MAR/TOTAL AS MAR,APR/TOTAL AS APR, MAY/TOTAL AS MAY, JUN/TOTAL AS JUN,
            JUL/TOTAL AS JUL, AUG/TOTAL AS AUG, SEP/TOTAL AS SEP,OCT/TOTAL AS OCT, NOV/TOTAL AS NOV, DEC/TOTAL AS DEC,
            TOTAL
          FROM
            (
              SELECT
                norma.PERIOD_BUDGET PERIOD_BUDGET,
                norma.BA_CODE BA_CODE, NORMA.AFD_CODE, NORMA.BLOCK_CODE,
                SUM(norma.JAN) JAN, SUM(norma.FEB) FEB, SUM(norma.MAR) MAR,
                SUM(norma.APR) APR, SUM(norma.MAY) MAY, SUM(norma.JUN) JUN,
                SUM(norma.JUL) JUL, SUM(norma.AUG) AUG, SUM(norma.SEP) SEP,
                SUM(norma.OCT) OCT, SUM(norma.NOV) NOV, SUM(norma.DEC) DEC,
                SUM( 
                    norma.JAN + norma.FEB + norma.MAR + norma.APR + norma.MAY + norma.JUN + 
                    norma.JUL + norma.AUG + norma.SEP + norma.OCT + norma.NOV + norma.DEC 
                  ) total
              FROM TR_PRODUKSI_PERIODE_BUDGET norma
              LEFT JOIN TR_PRODUKSI_TAHUN_BERJALAN thn_berjalan ON norma.PERIOD_BUDGET = thn_berjalan.PERIOD_BUDGET
                AND norma.BA_CODE = thn_berjalan.BA_CODE AND norma.AFD_CODE = thn_berjalan.AFD_CODE
                AND norma.BLOCK_CODE = thn_berjalan.BLOCK_CODE
              WHERE norma.DELETE_USER IS NULL 
              AND extract(YEAR FROM norma.PERIOD_BUDGET) = '".$params['budgetperiod']."' AND norma.ba_code = '".$params['key_find']."'
            GROUP BY norma.PERIOD_BUDGET, norma.BA_CODE, NORMA.AFD_CODE, NORMA.BLOCK_CODE
          )
        ) DIS ON DIS.PERIOD_BUDGET = MPP.PERIOD_BUDGET AND DIS.BA_CODE = MPP.BA_CODE
          AND DIS.AFD_CODE = MPP.AFD_CODE AND DIS.BLOCK_CODE = MPP.BLOCK_CODE
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = MPP.BA_CODE
        JOIN TN_ALAT_KERJA_PANEN HVT ON HVT.PERIOD_BUDGET = MPP.PERIOD_BUDGET AND HVT.BA_CODE = MPP.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = HVT.MATERIAL_CODE AND MAT.PERIOD_BUDGET = HVT.PERIOD_BUDGET
          AND MAT.BA_CODE = HVT.BA_CODE
        ORDER BY MPP.BA_CODE, MPP.AFD_CODE, MPP.BLOCK_CODE, MAT.MATERIAL_NAME
    ";

  // transport berdasarkan vra distribusi
  // 'MANUAL_INFRA', 'MANUAL_NON_INFRA', 'MANUAL_NON_INFRA_OPSI'
  // sumber biaya internal
  $components['vra_infra'] = "
    INSERT INTO TMP_RPT_KEB_EST_COST_BLOCK (
      PERIOD_BUDGET,
      REGION_CODE,
      BA_CODE,
      AFD_CODE,
      BLOCK_CODE,
      TIPE_TRANSAKSI,
      ACTIVITY_CODE,
      ACTIVITY_DESC,
      COST_ELEMENT,
      SUB_COST_ELEMENT_DESC,
      UOM,
      QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
      COST_JAN,COST_FEB,COST_MAR,COST_APR,COST_MAY,COST_JUN,COST_JUL,COST_AUG,COST_SEP,COST_OCT,COST_NOV,COST_DEC,
      QTY_SETAHUN, COST_SETAHUN,
      INSERT_USER,INSERT_TIME
    )
    SELECT 
      PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE
      , 'TM', ACTIVITY_CODE, ACTIVITY_NAME||' - VRA'
      , 'TRANSPORT'
      , VH_NAME, UOM
      , SUM((PC.PLAN_JAN/PC.PLAN_AFD)*HM_KM) PLAN_JAN
      , SUM((PC.PLAN_FEB/PC.PLAN_AFD)*HM_KM) PLAN_FEB
      , SUM((PC.PLAN_MAR/PC.PLAN_AFD)*HM_KM) PLAN_MAR
      , SUM((PC.PLAN_APR/PC.PLAN_AFD)*HM_KM) PLAN_APR
      , SUM((PC.PLAN_MAY/PC.PLAN_AFD)*HM_KM) PLAN_MAY
      , SUM((PC.PLAN_JUN/PC.PLAN_AFD)*HM_KM) PLAN_JUN
      , SUM((PC.PLAN_JUL/PC.PLAN_AFD)*HM_KM) PLAN_JUL
      , SUM((PC.PLAN_AUG/PC.PLAN_AFD)*HM_KM) PLAN_AUG
      , SUM((PC.PLAN_SEP/PC.PLAN_AFD)*HM_KM) PLAN_SEP
      , SUM((PC.PLAN_OCT/PC.PLAN_AFD)*HM_KM) PLAN_OCT
      , SUM((PC.PLAN_NOV/PC.PLAN_AFD)*HM_KM) PLAN_NOV
      , SUM((PC.PLAN_DEC/PC.PLAN_AFD)*HM_KM) PLAN_DEC
      , SUM((PC.PLAN_JAN/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_JAN, SUM((PC.PLAN_FEB/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_FEB
      , SUM((PC.PLAN_MAR/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_MAR, SUM((PC.PLAN_APR/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_APR
      , SUM((PC.PLAN_MAY/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_MAY, SUM((PC.PLAN_JUN/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_JUN
      , SUM((PC.PLAN_JUL/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_JUL, SUM((PC.PLAN_AUG/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_AUG
      , SUM((PC.PLAN_SEP/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_SEP, SUM((PC.PLAN_OCT/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_OCT
      , SUM((PC.PLAN_NOV/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_NOV, SUM((PC.PLAN_DEC/PC.PLAN_AFD)*PC.PRICE_HM_KM) COST_DEC
      , SUM(((PLAN_JAN+PLAN_FEB+PLAN_MAR+PLAN_APR+PLAN_MAY+PLAN_JUN+PLAN_JUL+PLAN_AUG+PLAN_SEP+PLAN_OCT+PLAN_NOV+PLAN_DEC)/PC.PLAN_AFD)*HM_KM)
      , SUM(((PLAN_JAN+PLAN_FEB+PLAN_MAR+PLAN_APR+PLAN_MAY+PLAN_JUN+PLAN_JUL+PLAN_AUG+PLAN_SEP+PLAN_OCT+PLAN_NOV+PLAN_DEC)/PC.PLAN_AFD)*PC.PRICE_HM_KM)
      , '".$this->_userName."', CURRENT_TIMESTAMP
    FROM (
      SELECT RKT.*
      , F_GET_RKT_PLAN_AFDELING(RKT.PERIOD_BUDGET, RKT.BA_CODE, RKT.AFD_CODE, RKT.ACTIVITY_CODE,RKT.SUMBER_BIAYA) PLAN_AFD
      , VRA.HM_KM, VRA.PRICE_HM_KM, VRA.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VH_NAME
      , ACT.DESCRIPTION ACTIVITY_NAME, VH.UOM
      , ORG.REGION_CODE
      FROM TR_RKT RKT
      JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
      JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
      JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        AND RKT.PERIOD_BUDGET = VRA.PERIOD_BUDGET AND RKT.BA_CODE = VRA.BA_CODE
        AND RKT.AFD_CODE = VRA.LOCATION_CODE
      JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
      WHERE EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = ".$params['budgetperiod']."
      AND RKT.BA_CODE = '".$params['key_find']."'
      AND RKT.TIPE_TRANSAKSI IN ('MANUAL_INFRA')
      AND RKT.MATURITY_STAGE_SMS1 = 'TM'
      AND RKT.PLAN_SETAHUN != 0
      AND RKT.ACTIVITY_CODE NOT IN ('51600','42700') -- EXCLUDE SISIP DAN LANGSIR
    ) PC
    GROUP BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE
    , PC.ACTIVITY_CODE, ACTIVITY_NAME, VH_NAME, UOM
  ";

  foreach ($components as $key => $sql_string) {
    // print_r($sql_string);
    $this->_db->query($sql_string);
    $this->_db->commit();
  }
    // die();

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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'ASTEK'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE   
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'ASTEK'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE                  
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
       -- AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'CATU'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE   
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
       -- AND TTJ.EMPLOYEE_STATUS = 'K/1' -- INI NANTI DIHAPUS UNTUK DATA 2016 HANYA PERLU BA UNTUK PENGAMBILAN DATANYA
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'CATU'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE   
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
        'LABOUR' AS COST_ELEMENT,
        'JABATAN' AS ACTIVITY_CODE, 
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
     WHERE  1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'JABATAN'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'JABATAN'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'KEHADIRAN'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE  1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'KEHADIRAN'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE  1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'LAINNYA'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE  1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
        SELECT CR.PERIOD_BUDGET,CR.BA_CODE, CRD.TUNJANGAN_TYPE ,  SUM  (MPP_PERIOD_BUDGET * JUMLAH )  COSTTYPE -- TUNJANGAN OT
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR
                    ON JT.JOB_CODE = CR.JOB_CODE
                LEFT JOIN TR_RKT_CHECKROLL_DETAIL CRD
                    ON CRD.TRX_CR_CODE = CR.TRX_CR_CODE
                WHERE JOB_TYPE = 'OT'
                    AND TUNJANGAN_TYPE IN (
                        SELECT TUNJANGAN_TYPE
                        FROM TM_TUNJANGAN 
                        WHERE DELETE_TIME IS NULL 
                        AND FLAG_RP_HK = 'YES'
                    )
                    AND CRD.TUNJANGAN_TYPE = 'LAINNYA'
                    $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND CRD.DELETE_TIME IS NULL
                GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE, CRD.TUNJANGAN_TYPE 
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
        'GAJI' AS ACTIVITY_CODE, 
        'GAJI' AS ACTIVITY_DESC,
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
    $xwhere 
  GROUP BY PERIOD_BUDGET, BA_CODE
  ) HA_BA
  ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
  AND HA_TM.BA_CODE = HA_BA.BA_CODE
  LEFT JOIN (
    SELECT CR.PERIOD_BUDGET, CR.BA_CODE, SUM (CR.MPP_PERIOD_BUDGET) AS MPP
    FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR 
                    ON CR.JOB_CODE = JT.JOB_CODE
                WHERE 1=1 
          $cwhere
                    AND CR.DELETE_TIME IS NULL
                    AND JT.JOB_TYPE = 'OT'
                    GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE 
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
        SELECT CR.PERIOD_BUDGET, CR.BA_CODE, 'GAJI', SUM(GP_INFLASI * MPP_PERIOD_BUDGET ) COSTTYPE
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR 
                    ON CR.JOB_CODE = JT.JOB_CODE
                WHERE CR.DELETE_TIME IS NULL
                    $cwhere
                    AND JT.JOB_TYPE = 'OT'
                    GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
     $xwhere
  GROUP BY PERIOD_BUDGET, BA_CODE
  ) HA_BA
  ON HA_TM.PERIOD_BUDGET = HA_BA.PERIOD_BUDGET
  AND HA_TM.BA_CODE = HA_BA.BA_CODE
  LEFT JOIN (
  SELECT CR.PERIOD_BUDGET, CR.BA_CODE, SUM (CR.MPP_PERIOD_BUDGET) AS MPP
    FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR 
                    ON CR.JOB_CODE = JT.JOB_CODE
                WHERE 1=1 
        $cwhere 
        AND CR.DELETE_TIME IS NULL
        AND JT.JOB_TYPE = 'OT'
        GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE 
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
        SELECT CR.PERIOD_BUDGET, CR.BA_CODE, 'GAJI', SUM(GP_INFLASI * MPP_PERIOD_BUDGET ) COSTTYPE
                FROM TM_JOB_TYPE JT
                LEFT JOIN TR_RKT_CHECKROLL CR 
                    ON CR.JOB_CODE = JT.JOB_CODE
                WHERE CR.DELETE_TIME IS NULL
                    $cwhere
                    AND JT.JOB_TYPE = 'OT'
                    GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE
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
     WHERE  1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
     WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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

    $ba_code = ""; $period_budget = "";
    //filter periode buget
    if($params['budgetperiod'] != ''){
      $period_budget = $params['budgetperiod'];
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
      $cwhere .= "
                AND to_char(CR.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
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
      $cwhere .= "
                AND to_char(CR.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
      $ba_code = $params['key_find'];
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
      $cwhere .= "
                AND CR.BA_CODE = '".$params['key_find']."'
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
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JAN / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JAN,    
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_FEB / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_FEB,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_MAR / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_MAR,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_APR / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_APR,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_MAY / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_MAY,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JUN / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JUN,
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
         AND RKT.MATURITY_STAGE_SMS1 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
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
         CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_JUL / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_JUL,  
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_AUG / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_AUG,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_SEP / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_SEP,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_OCT / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_OCT,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_NOV / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_NOV,
               CASE
                  WHEN RKT.TIPE_NORMA = 'UMUM'
                  THEN
                     (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.QTY_HA
                  ELSE
                     (RKT.PLAN_DEC / SPH.SPH_STANDAR) * BIAYA.QTY_HA_SITE
               END
                  AS QTY_DEC,
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
         AND RKT.MATURITY_STAGE_SMS2 IN ('TBM0', 'TBM1', 'TBM2', 'TBM3')
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
   --ini untuk PUPUK TUNGGAL TOOLS
   --ini untuk PUPUK MAJEMUK TOOLS

    --PERHITUNGAN PUPUK MAJEMUK & TUNGGAL (MATERIAL)

    -- RKT PUPUK MAJEMUK SELAIN COST ELEMENT MATERIAL & LABOUR

    -- PUPUK TUNGGAL LABOUR             
                              
    -- PUPUK MAJEMUK LABOUR
      
    -- INI UNTUK PERHITUNGAN PUPUK TUNGGAL TRANSPORT
    
    -- INI UNTUK PERHITUNGAN TUNJANGAN (ASTEK)

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

    -- INI UNTUK PERHITUNGAN TUNJANGAN (JABATAN)
    -- INI UNTUK PERHITUNGAN TUNJANGAN (KEHADIRAN)
    -- INI UNTUK PERHITUNGAN TUNJANGAN (LAINNYA)

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
       WHERE 1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
       WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
    -- INI UNTUK PERHITUNGAN TUNJANGAN (GAJI)
    
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
       WHERE  1 = 1 AND MATURITY_STAGE_SMS1 IS NOT NULL
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
       WHERE 1 = 1 AND MATURITY_STAGE_SMS2 IS NOT NULL
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
    ";
    $this->_db->query($xquery);
    $this->_db->commit();
    

    // DEV COST KASTRASI DAN SANITASI 
    $devcost_components['kastrasi_sanitasi'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI,
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        SUB_COST_ELEMENT,
        SUB_COST_ELEMENT_DESC,
        UOM,
        QTY_SETAHUN, COST_SETAHUN,INSERT_USER, INSERT_TIME,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC
      )
      SELECT * FROM (
        SELECT P.*
          , SUM(P.QTY*P.HA_PLANTED) OVER (PARTITION BY P.PERIOD_BUDGET, P.BA_CODE, P.AFD_CODE, P.BLOCK_CODE, P.MATURITY_STAGE, P.ACTIVITY_CODE, P.SUB_COST_ELEMENT) QTY_TOTAL
          , SUM(P.QTY*P.PRICE*P.HA_PLANTED) OVER (PARTITION BY P.PERIOD_BUDGET, P.BA_CODE, P.AFD_CODE, P.BLOCK_CODE, P.MATURITY_STAGE, P.ACTIVITY_CODE, P.SUB_COST_ELEMENT) COST_TOTAL
          , '".$this->_userName."', CURRENT_TIMESTAMP
        FROM (
          SELECT DS.PERIOD_BUDGET, DS.REGION_CODE, DS.BA_CODE, DS.AFD_CODE, DS.BLOCK_CODE, DS.MATURITY_STAGE_SMS1 MATURITY_STAGE
          , ACT.ACTIVITY_CODE, ACT.DESCRIPTION ACTIVITY_NAME
          , COST.COST_ELEMENT, COST.SUB_COST_ELEMENT
          , COALESCE(JT.JOB_DESCRIPTION, MAT.MATERIAL_NAME) SUB_COST_ELEMENT_DESC
          , COALESCE(MAT.UOM, 'HK') UOM
          , DS.BULAN_ROTASI
          , COST.QTY, COST.PRICE, DS.HA_PLANTED
          FROM (
            SELECT HS.*, ORG.REGION_CODE, TRUNC(MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM)) USIA_TANAMAN
            , MONTHTOADD , (TRUNC(MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM)) + MONTHTOADD) USIA, MONTHTOADD + 1 AS BULAN_ROTASI
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            CROSS JOIN (
              SELECT LEVEL-1 MONTHTOADD FROM (
                SELECT 0 MONTH_START, 6 MONTH_END
                FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
              ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
            )
            WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
            AND HS.BA_CODE = '".$ba_code."'
          ) DS
          JOIN TN_KASTRASI_SANITASI KS ON KS.PERIOD_BUDGET = DS.PERIOD_BUDGET AND DS.LAND_SUITABILITY = KS.LAND_SUITABILITY
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = KS.ACTIVITY_CODE
          JOIN TN_BIAYA COST ON COST.PERIOD_BUDGET = DS.PERIOD_BUDGET AND COST.ACTIVITY_CODE = KS.ACTIVITY_CODE
            AND COST.BA_CODE = DS.BA_CODE AND COST.ACTIVITY_GROUP = DS.MATURITY_STAGE_SMS1
          LEFT JOIN TM_JOB_TYPE JT ON JT.JOB_CODE = COST.SUB_COST_ELEMENT
          LEFT JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = COST.SUB_COST_ELEMENT AND MAT.PERIOD_BUDGET = DS.PERIOD_BUDGET
            AND MAT.BA_CODE = DS.BA_CODE
          WHERE DS.USIA = KS.UMUR
          UNION ALL
          SELECT DS.PERIOD_BUDGET, DS.REGION_CODE, DS.BA_CODE, DS.AFD_CODE, DS.BLOCK_CODE, DS.MATURITY_STAGE_SMS2 MATURITY_STAGE
          , ACT.ACTIVITY_CODE, ACT.DESCRIPTION ACTIVITY_NAME
          , COST.COST_ELEMENT, COST.SUB_COST_ELEMENT
          , COALESCE(JT.JOB_DESCRIPTION, MAT.MATERIAL_NAME) SUB_COST_ELEMENT_DESC
          , COALESCE(MAT.UOM, 'HK') UOM
          , DS.BULAN_ROTASI
          , COST.QTY, COST.PRICE, DS.HA_PLANTED
          FROM (
            SELECT HS.*, ORG.REGION_CODE, TRUNC(MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM)) USIA_TANAMAN
            , MONTHTOADD , (TRUNC(MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM)) + MONTHTOADD) USIA, MONTHTOADD + 1 AS BULAN_ROTASI
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            CROSS JOIN (
              SELECT LEVEL+5 MONTHTOADD FROM (
                SELECT 0 MONTH_START, 6 MONTH_END
                FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
              ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
            )
            WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
            AND HS.BA_CODE = '".$ba_code."'
          ) DS
          JOIN TN_KASTRASI_SANITASI KS ON KS.PERIOD_BUDGET = DS.PERIOD_BUDGET AND DS.LAND_SUITABILITY = KS.LAND_SUITABILITY
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = KS.ACTIVITY_CODE
          JOIN TN_BIAYA COST ON COST.PERIOD_BUDGET = DS.PERIOD_BUDGET AND COST.ACTIVITY_CODE = KS.ACTIVITY_CODE
            AND COST.BA_CODE = DS.BA_CODE AND COST.ACTIVITY_GROUP = DS.MATURITY_STAGE_SMS2
          LEFT JOIN TM_JOB_TYPE JT ON JT.JOB_CODE = COST.SUB_COST_ELEMENT
          LEFT JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = COST.SUB_COST_ELEMENT AND MAT.PERIOD_BUDGET = DS.PERIOD_BUDGET
            AND MAT.BA_CODE = DS.BA_CODE
          WHERE DS.USIA = KS.UMUR
        --  ORDER BY 
        ) P
      )
      PIVOT (
        SUM(QTY*P.HA_PLANTED) AS DIS, SUM(QTY*PRICE*HA_PLANTED) AS COST
        FOR BULAN_ROTASI IN (
          '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
          '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
        )
      )
    ";

    // PEMUPUKAN
    // PUPUK TBM HARUS DITAMBAHKAN DENGAN 
    // SUB BLOK TBM YANG MENDAPAT REKOMENDASI DOSIS PEMUPUKAN TM
    $devcost_components['material_pupuk_tunggal_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI,
        COST_ELEMENT,
        SUB_COST_ELEMENT,
        SUB_COST_ELEMENT_DESC,
        UOM,
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,INSERT_USER, INSERT_TIME
      )
      SELECT PIV.*,
        NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
        NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) QTY_SETAHUN,
        NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
        NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN,
        '".$this->_userName."', CURRENT_TIMESTAMP
      FROM (
        SELECT * FROM (
          -- REKOMENDASI PUPUK TBM SEMESTER 1
          SELECT * FROM (
            SELECT TM.PERIOD_BUDGET, ORG.REGION_CODE, TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE
            , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE, 'MATERIAL'
            , TM.JUMLAH, TM.MATERIAL_CODE
            , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN
            , MAT.PRICE, MAT.MATERIAL_NAME, MAT.UOM
            , COA.COA_CODE ACTIVITY_CODE
            , DECODE(COA.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC
            FROM TN_PUPUK_TBM2_TM TM
            JOIN TM_HECTARE_STATEMENT HS ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
              AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
            WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = '".$period_budget."'
             AND TM.BA_CODE = '".$ba_code."' AND HS.MATURITY_STAGE_SMS1 != 'TM'
             AND CAST(TM.BULAN_PEMUPUKAN AS INT) < 7
             AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
          )
          UNION 
          -- PUPUK TBM SEMSTER 1
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
            , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE, 'MATERIAL'
            , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH, TBM.MATERIAL_CODE
            , HS.BULAN_PEMUPUKAN
            , MAT.PRICE
            , MAT.MATERIAL_NAME, MAT.UOM
            , COA.COA_CODE ACTIVITY_CODE
            , DECODE(COA.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."' AND HCS.BA_CODE = '".$ba_code."'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS1 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL-1 MONTHTOADD FROM (
                  SELECT 0 MONTH_START, 6 MONTH_END
                  FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
                ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN BPS_PROD.TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.MATERIAL_CODE = TBM.MATERIAL_CODE AND MAT.BA_CODE = TBM.BA_CODE AND MAT.PERIOD_BUDGET = TBM.PERIOD_BUDGET
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
          )
          UNION
          -- REKOMENDASI PUPUK TBM SEMESTER 2
          SELECT * FROM (
            SELECT TM.PERIOD_BUDGET, ORG.REGION_CODE, TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE
            , HS.MATURITY_STAGE_SMS2 MATURITY_STAGE, 'MATERIAL'
            , TM.JUMLAH, TM.MATERIAL_CODE
            , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN
            , MAT.PRICE, MAT.MATERIAL_NAME, MAT.UOM
            , COA.COA_CODE ACTIVITY_CODE
            , DECODE(COA.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC
            FROM TN_PUPUK_TBM2_TM TM
            JOIN TM_HECTARE_STATEMENT HS ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
              AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE 
            WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = '".$period_budget."'
             AND TM.BA_CODE = '".$ba_code."' AND HS.MATURITY_STAGE_SMS2 != 'TM'
             AND CAST(TM.BULAN_PEMUPUKAN AS INT) > 6
             AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
          )
          UNION 
          -- PUPUK TBM SEMESTER 2
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
            , HS.MATURITY_STAGE_SMS2 MATURITY_STAGE, 'MATERIAL'
            , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH, TBM.MATERIAL_CODE
            , HS.BULAN_PEMUPUKAN
            , MAT.PRICE
            , MAT.MATERIAL_NAME, MAT.UOM
            , COA.COA_CODE ACTIVITY_CODE
            , DECODE(COA.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."' AND HCS.BA_CODE = '".$ba_code."'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS2 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL+5 MONTHTOADD FROM (
                  SELECT 0 MONTH_START, 6 MONTH_END
                  FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
                ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN BPS_PROD.TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.MATERIAL_CODE = TBM.MATERIAL_CODE AND MAT.BA_CODE = TBM.BA_CODE AND MAT.PERIOD_BUDGET = TBM.PERIOD_BUDGET
            JOIN TM_COA COA ON COA.COA_CODE = MAT.COA_CODE
          )
        )
        PIVOT (
          SUM(JUMLAH) AS DIS, SUM(JUMLAH*PRICE) AS COST
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PIV
    ";

    $devcost_components['labour_pupuk_tunggal_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI,
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        SUB_COST_ELEMENT, -- kode jabatan
        SUB_COST_ELEMENT_DESC,  -- nama jabatan
        UOM,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,INSERT_USER, INSERT_TIME
      )
      SELECT PIV.*, 
        NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
        NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) DIS_SETAHUN,
        NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
        NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN,
        '".$this->_userName."', CURRENT_TIMESTAMP
      FROM (
        SELECT * FROM (
          SELECT HS.PERIOD_BUDGET, HS.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.MATURITY_STAGE_SMS1 MATURITY_STAGE,
          MAT.COA_CODE, DECODE(MAT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK'),
          'LABOUR' COST_ELEMENT, HS.BULAN_PEMUPUKAN,
          CR.JOB_CODE, 'PEKERJA RAWAT', 'HK' UOM,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL COST,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL/CR.RP_HK HK
          FROM (
            SELECT HS.*, MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE 
             , MONTHTOADD, (MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            CROSS JOIN (
              SELECT LEVEL-1 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
              CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
            )
            WHERE  EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
            AND HS.BA_CODE = '".$ba_code."'
            AND HS.MATURITY_STAGE_SMS1 != 'TM'
            AND NOT EXISTS (
              SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HS.PERIOD_BUDGET AND TM.BA_CODE = HS.BA_CODE
              AND TM.AFD_CODE = HS.AFD_CODE AND TM.BLOCK_CODE = HS.BLOCK_CODE
            )
          ) HS
          JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
            AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
          JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
            AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
          JOIN TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = HS.PERIOD_BUDGET AND COST.BA_CODE = HS.BA_CODE AND COST.COST_ELEMENT = 'LABOUR'
          AND COST.ACTIVITY_CODE = DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
          JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = HS.BA_CODE AND CR.PERIOD_BUDGET = HS.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
          UNION ALL
          SELECT HS.PERIOD_BUDGET, HS.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.MATURITY_STAGE_SMS2 MATURITY_STAGE,
          MAT.COA_CODE, DECODE(MAT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK'),
          'LABOUR' COST_ELEMENT, HS.BULAN_PEMUPUKAN,
          CR.JOB_CODE, 'PEKERJA RAWAT', 'HK' UOM,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL COST,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL/CR.RP_HK HK
          FROM (
            SELECT HS.*, MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE 
             , MONTHTOADD, (MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            CROSS JOIN (
              SELECT LEVEL+5 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
              CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
            )
            WHERE  EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
            AND HS.BA_CODE = '".$ba_code."'
            AND HS.MATURITY_STAGE_SMS2 != 'TM'
            AND NOT EXISTS (
              SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HS.PERIOD_BUDGET AND TM.BA_CODE = HS.BA_CODE
              AND TM.AFD_CODE = HS.AFD_CODE AND TM.BLOCK_CODE = HS.BLOCK_CODE
            )
          ) HS
          JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
            AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
          JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
            AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
          JOIN TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = HS.PERIOD_BUDGET AND COST.BA_CODE = HS.BA_CODE AND COST.COST_ELEMENT = 'LABOUR'
          AND COST.ACTIVITY_CODE = DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
          JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = HS.BA_CODE AND CR.PERIOD_BUDGET = HS.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
          UNION ALL
          SELECT HS.PERIOD_BUDGET, ORG.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.MATURITY_STAGE_SMS1 MATURITY_STAGE,
          MAT.COA_CODE, DECODE(MAT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK'),
          'LABOUR' COST_ELEMENT, CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN,
          CR.JOB_CODE, 'PEKERJA RAWAT', 'HK' UOM,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL COST,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL/CR.RP_HK HK
          FROM TM_HECTARE_STATEMENT HS
          JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
          JOIN TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
            AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE 
          JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
            AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
          JOIN TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = HS.PERIOD_BUDGET AND COST.BA_CODE = HS.BA_CODE AND COST.COST_ELEMENT = 'LABOUR'
          AND COST.ACTIVITY_CODE = DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
          JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = HS.BA_CODE AND CR.PERIOD_BUDGET = HS.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
          WHERE HS.MATURITY_STAGE_SMS1 != 'TM'
          AND EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
          AND HS.BA_CODE = '".$ba_code."'
          AND TM.BULAN_PEMUPUKAN < 7
          UNION ALL 
          SELECT HS.PERIOD_BUDGET, ORG.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.MATURITY_STAGE_SMS2 MATURITY_STAGE,
          MAT.COA_CODE, DECODE(MAT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK'),
          'LABOUR' COST_ELEMENT, CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN,
          CR.JOB_CODE, 'PEKERJA RAWAT', 'HK' UOM,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL COST,
          HS.POKOK_TANAM * COST.RP_QTY_INTERNAL/CR.RP_HK HK
          FROM TM_HECTARE_STATEMENT HS
          JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
          JOIN TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
            AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE 
          JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
            AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
          JOIN TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = HS.PERIOD_BUDGET AND COST.BA_CODE = HS.BA_CODE AND COST.COST_ELEMENT = 'LABOUR'
          AND COST.ACTIVITY_CODE = DECODE(UPPER(MAT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
          JOIN TR_RKT_CHECKROLL_SUM CR ON CR.BA_CODE = HS.BA_CODE AND CR.PERIOD_BUDGET = HS.PERIOD_BUDGET AND CR.JOB_CODE = 'FW030'
          WHERE HS.MATURITY_STAGE_SMS2 != 'TM'
          AND EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
          AND HS.BA_CODE = '".$ba_code."'
          AND TM.BULAN_PEMUPUKAN > 6
        )
        PIVOT (
          SUM(HK) AS DIS, SUM(COST) AS COST
          FOR BULAN_PEMUPUKAN IN (
          '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
          '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PIV
    ";

    $devcost_components['tools_pupuk_tunggal_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI,
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM,
        SUB_COST_ELEMENT_DESC,  -- nama jabatan
        SUB_COST_ELEMENT, -- kode jabatan
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN, INSERT_USER, INSERT_TIME
      )
      SELECT PIV.*, 
        NVL(PIV.JAN_DIS,0)+NVL(PIV.FEB_DIS,0)+NVL(PIV.MAR_DIS,0)+NVL(PIV.APR_DIS,0)+NVL(PIV.MAY_DIS,0)+NVL(PIV.JUN_DIS,0)+
        NVL(PIV.JUL_DIS,0)+NVL(PIV.AUG_DIS,0)+NVL(PIV.SEP_DIS,0)+NVL(PIV.OCT_DIS,0)+NVL(PIV.NOV_DIS,0)+NVL(PIV.DEC_DIS,0) DIS_SETAHUN,
        NVL(PIV.JAN_COST,0)+NVL(PIV.FEB_COST,0)+NVL(PIV.MAR_COST,0)+NVL(PIV.APR_COST,0)+NVL(PIV.MAY_COST,0)+NVL(PIV.JUN_COST,0)+
        NVL(PIV.JUL_COST,0)+NVL(PIV.AUG_COST,0)+NVL(PIV.SEP_COST,0)+NVL(PIV.OCT_COST,0)+NVL(PIV.NOV_COST,0)+NVL(PIV.DEC_COST,0) COST_SETAHUN,
        '".$this->_userName."', CURRENT_TIMESTAMP
      FROM (
        SELECT * FROM (
          SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
            RKT.COA_CODE ACTIVITY_CODE, 
            DECODE(RKT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC,
            COST.COST_ELEMENT, RKT.BULAN_PEMUPUKAN,
            M1.UOM, M1.MATERIAL_NAME, M1.MATERIAL_CODE,
            COST.QTY_ALAT * RKT.POKOK_TANAM QTY,
            COST.RP_QTY_INTERNAL * RKT.POKOK_TANAM COST
            FROM (
              SELECT HS.*, MAT.MATERIAL_CODE, MAT.FLAG, MAT.COA_CODE FROM (
                SELECT HS.*, MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE 
                 , MONTHTOADD, (MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN
                FROM BPS_PROD.TM_HECTARE_STATEMENT HS
                JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
                CROSS JOIN (
                  SELECT LEVEL-1 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                  CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
                )
                WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
                AND HS.BA_CODE = '".$ba_code."'
                AND HS.MATURITY_STAGE_SMS1 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM BPS_PROD.TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HS.PERIOD_BUDGET AND TM.BA_CODE = HS.BA_CODE
                  AND TM.AFD_CODE = HS.AFD_CODE AND TM.BLOCK_CODE = HS.BLOCK_CODE
                )
              ) HS
              JOIN BPS_PROD.TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
                AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
              JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
                AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            ) RKT
            JOIN BPS_PROD.TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS'
              AND COST.ACTIVITY_CODE = DECODE(UPPER(RKT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
              AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
            JOIN BPS_PROD.TM_MATERIAL M1 ON M1.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND M1.BA_CODE = RKT.BA_CODE
              AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
            WHERE COST.TOPOGRAPHY = (
              SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM BPS_PROD.TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
              AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
              AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
            )
          )
          UNION ALL
          SELECT * FROM (
            SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
            RKT.COA_CODE ACTIVITY_CODE, 
            DECODE(RKT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC,
            COST.COST_ELEMENT, RKT.BULAN_PEMUPUKAN,
            M1.UOM, M1.MATERIAL_NAME, M1.MATERIAL_CODE,
            COST.QTY_ALAT * RKT.POKOK_TANAM QTY,
            COST.RP_QTY_INTERNAL * RKT.POKOK_TANAM COST
            FROM (
              SELECT HS.*, MAT.MATERIAL_CODE, MAT.FLAG, MAT.COA_CODE FROM (
                SELECT HS.*, MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE 
                 , MONTHTOADD, (MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN
                FROM BPS_PROD.TM_HECTARE_STATEMENT HS
                JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
                CROSS JOIN (
                  SELECT LEVEL+5 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                  CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
                )
                WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
                AND HS.BA_CODE = '".$ba_code."'
                AND HS.MATURITY_STAGE_SMS2 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM BPS_PROD.TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HS.PERIOD_BUDGET AND TM.BA_CODE = HS.BA_CODE
                  AND TM.AFD_CODE = HS.AFD_CODE AND TM.BLOCK_CODE = HS.BLOCK_CODE
                )
              ) HS
              JOIN BPS_PROD.TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
                AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
              JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
                AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            ) RKT
            JOIN BPS_PROD.TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS'
              AND COST.ACTIVITY_CODE = DECODE(UPPER(RKT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
              AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
            JOIN BPS_PROD.TM_MATERIAL M1 ON M1.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND M1.BA_CODE = RKT.BA_CODE
              AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
            WHERE COST.TOPOGRAPHY = (
              SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM BPS_PROD.TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
              AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
              AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
            )
          )
          UNION ALL
          -- REKOMENDASI SUB BLOK TBM
          SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.MATURITY_STAGE_SMS1 MATURITY_STAGE,
            RKT.COA_CODE ACTIVITY_CODE, 
            DECODE(RKT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC,
            COST.COST_ELEMENT, CAST(RKT.BULAN_PEMUPUKAN AS INTEGER) BULAN_PEMUPUKAN,
            M1.UOM, M1.MATERIAL_NAME, M1.MATERIAL_CODE,
            COST.QTY_ALAT * RKT.POKOK_TANAM QTY,
            COST.RP_QTY_INTERNAL * RKT.POKOK_TANAM COST
          FROM (
              SELECT HS.*, ORG.REGION_CODE, TM.BULAN_PEMUPUKAN, MAT.MATERIAL_CODE, MAT.FLAG, MAT.COA_CODE
              FROM BPS_PROD.TM_HECTARE_STATEMENT HS
              JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
              JOIN BPS_PROD.TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
                AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE 
              JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
                AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
              WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
              AND HS.BA_CODE = '".$ba_code."'
              AND HS.MATURITY_STAGE_SMS1 != 'TM'
              AND TM.BULAN_PEMUPUKAN < 7
          ) RKT
          JOIN BPS_PROD.TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS'
            AND COST.ACTIVITY_CODE = DECODE(UPPER(RKT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
            AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
          JOIN BPS_PROD.TM_MATERIAL M1 ON M1.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND M1.BA_CODE = RKT.BA_CODE
            AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
          WHERE COST.TOPOGRAPHY = (
            SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM BPS_PROD.TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
            AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
            AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
          )
          UNION ALL
          SELECT RKT.PERIOD_BUDGET, RKT.REGION_CODE, RKT.BA_CODE, RKT.AFD_CODE, RKT.BLOCK_CODE, RKT.MATURITY_STAGE_SMS2 MATURITY_STAGE,
            RKT.COA_CODE ACTIVITY_CODE, 
            DECODE(RKT.COA_CODE, '5101020300', 'PUPUK TUNGGAL', '5101020400', 'PUPUK MAJEMUK') ACTIVITY_DESC,
            COST.COST_ELEMENT, CAST(RKT.BULAN_PEMUPUKAN AS INTEGER) BULAN_PEMUPUKAN,
            M1.UOM, M1.MATERIAL_NAME, M1.MATERIAL_CODE,
            COST.QTY_ALAT * RKT.POKOK_TANAM QTY,
            COST.RP_QTY_INTERNAL * RKT.POKOK_TANAM COST
          FROM (
              SELECT HS.*, ORG.REGION_CODE, TM.BULAN_PEMUPUKAN, MAT.MATERIAL_CODE, MAT.FLAG, MAT.COA_CODE
              FROM BPS_PROD.TM_HECTARE_STATEMENT HS
              JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
              JOIN BPS_PROD.TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
                AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE 
              JOIN BPS_PROD.TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
                AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
              WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
              AND HS.BA_CODE = '".$ba_code."'
              AND HS.MATURITY_STAGE_SMS2 != 'TM'
              AND TM.BULAN_PEMUPUKAN > 6
          ) RKT
          JOIN BPS_PROD.TN_INFRASTRUKTUR COST ON COST.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND COST.BA_CODE = RKT.BA_CODE AND COST.COST_ELEMENT = 'TOOLS'
            AND COST.ACTIVITY_CODE = DECODE(UPPER(RKT.FLAG), 'MAKRO', '43750', 'MIKRO', '43751', 'TANKOS', '43500', '43770')
            AND RKT.TOPOGRAPHY = COST.TOPOGRAPHY
          JOIN BPS_PROD.TM_MATERIAL M1 ON M1.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND M1.BA_CODE = RKT.BA_CODE
            AND M1.MATERIAL_CODE = COST.SUB_COST_ELEMENT
          WHERE COST.TOPOGRAPHY = (
            SELECT DISTINCT NVL(B1.TOPOGRAPHY, 'ALL') FROM BPS_PROD.TN_INFRASTRUKTUR B1 WHERE B1.TOPOGRAPHY = RKT.TOPOGRAPHY 
            AND COST.ACTIVITY_CODE = B1.ACTIVITY_CODE AND COST.PERIOD_BUDGET = B1.PERIOD_BUDGET
            AND COST.BA_CODE = B1.BA_CODE AND COST.COST_ELEMENT = 'TOOLS' AND COST.SUB_COST_ELEMENT = B1.SUB_COST_ELEMENT
          )
        )
        PIVOT (
          SUM(QTY) AS DIS, SUM(COST) AS COST
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PIV
    ";

    $devcost_components['transport_pupuk_tunggal_majemuk'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI,
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        SUB_COST_ELEMENT,
        SUB_COST_ELEMENT_DESC,
        UOM,
        QTY_SETAHUN, COST_SETAHUN,INSERT_USER, INSERT_TIME,
        QTY_JAN, COST_JAN,
        QTY_FEB, COST_FEB,
        QTY_MAR, COST_MAR,
        QTY_APR, COST_APR,
        QTY_MAY, COST_MAY,
        QTY_JUN, COST_JUN,
        QTY_JUL, COST_JUL,
        QTY_AUG, COST_AUG,
        QTY_SEP, COST_SEP,
        QTY_OCT, COST_OCT,
        QTY_NOV, COST_NOV,
        QTY_DEC, COST_DEC
      )
      SELECT * FROM (
        SELECT P.*
          , SUM(JUMLAH/KG_AFDELING*HM_KM) OVER(PARTITION BY P.PERIOD_BUDGET, P.BA_CODE, P.AFD_CODE, P.BLOCK_CODE, P.MATURITY_STAGE, P.COA_CODE) QTY_TOTAL
          , SUM(JUMLAH/KG_AFDELING*PRICE_HM_KM) OVER(PARTITION BY P.PERIOD_BUDGET, P.BA_CODE, P.AFD_CODE, P.BLOCK_CODE, P.MATURITY_STAGE, P.COA_CODE) COST_TOTAL
          , '".$this->_userName."', CURRENT_TIMESTAMP
        FROM (
          -- SEMESTER 1
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK TUNGGAL' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH
              , HS.BULAN_PEMUPUKAN
              , HS.VRA_CODE, HS.VRA_NAME, HS.UOM
              , HS.PRICE_HM_KM, HS.HM_KM
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, VRA.PRICE_HM_KM, VRA.HM_KM, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                  , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HCS.PERIOD_BUDGET
                  AND VRA.BA_CODE = HCS.BA_CODE AND VRA.LOCATION_CODE = HCS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
                JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."'
                AND HCS.BA_CODE = '".$ba_code."'
                AND HCS.MATURITY_STAGE_SMS1 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS1 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL-1 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
              AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            WHERE MAT.COA_CODE = '5101020300' -- TUNGGAL
          )
          UNION ALL
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK MAJEMUK' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH
              , HS.BULAN_PEMUPUKAN
              , HS.VRA_CODE, HS.VRA_NAME, HS.UOM
              , HS.PRICE_HM_KM, HS.HM_KM
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, VRA.PRICE_HM_KM, VRA.HM_KM, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                  , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HCS.PERIOD_BUDGET
                  AND VRA.BA_CODE = HCS.BA_CODE AND VRA.LOCATION_CODE = HCS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
                JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."'
                AND HCS.BA_CODE = '".$ba_code."'
                AND HCS.MATURITY_STAGE_SMS1 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS1 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL-1 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
              AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            WHERE MAT.COA_CODE = '5101020400' -- MAJEMUK
          )
          UNION ALL
          -- SEMESTER 2
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS2 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK TUNGGAL' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH
              , HS.BULAN_PEMUPUKAN
              , HS.VRA_CODE, HS.VRA_NAME, HS.UOM
              , HS.PRICE_HM_KM, HS.HM_KM
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, VRA.PRICE_HM_KM, VRA.HM_KM, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                  , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HCS.PERIOD_BUDGET
                  AND VRA.BA_CODE = HCS.BA_CODE AND VRA.LOCATION_CODE = HCS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
                JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."'
                AND HCS.BA_CODE = '".$ba_code."'
                AND HCS.MATURITY_STAGE_SMS2 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS2 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL+5 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
              AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            WHERE MAT.COA_CODE = '5101020300' -- TUNGGAL
          )
          UNION ALL
          SELECT * FROM (
            SELECT TBM.PERIOD_BUDGET, HS.REGION_CODE, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS2 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK MAJEMUK' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH
              , HS.BULAN_PEMUPUKAN
              , HS.VRA_CODE, HS.VRA_NAME, HS.UOM
              , HS.PRICE_HM_KM, HS.HM_KM
            FROM (
              SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
              FROM (
                SELECT HCS.*, VRA.PRICE_HM_KM, VRA.HM_KM, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM, ORG.REGION_CODE
                  , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
                FROM TM_HECTARE_STATEMENT HCS
                JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HCS.BA_CODE
                LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HCS.PERIOD_BUDGET
                  AND VRA.BA_CODE = HCS.BA_CODE AND VRA.LOCATION_CODE = HCS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
                JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
                WHERE EXTRACT(YEAR FROM HCS.PERIOD_BUDGET) = '".$period_budget."'
                AND HCS.BA_CODE = '".$ba_code."'
                AND HCS.MATURITY_STAGE_SMS2 != 'TM'
                AND NOT EXISTS (
                  SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                  AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                )
                AND HCS.MATURITY_STAGE_SMS2 != 'TM'
              ) T_1
              CROSS JOIN (
                SELECT LEVEL+5 MONTHTOADD FROM (SELECT 0 MONTH_START, 6 MONTH_END FROM DUAL) 
                CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
              )
            ) HS 
            JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE 
              AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TBM.MATERIAL_CODE
            WHERE MAT.COA_CODE = '5101020400' -- MAJEMUK
          )
          UNION ALL
          -- SUB BLOK TBM DENGAN REKOMENDASI TM
          SELECT * FROM (
            SELECT HS.PERIOD_BUDGET, ORG.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(HS.PERIOD_BUDGET, HS.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK TUNGGAL' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , TM.JUMLAH
              , CAST(TM.BULAN_PEMUPUKAN AS INTEGER) BULAN_PEMUPUKAN
              , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
              , VRA.PRICE_HM_KM, VRA.HM_KM
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
              AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE
            LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HS.PERIOD_BUDGET
              AND VRA.BA_CODE = HS.BA_CODE AND VRA.LOCATION_CODE = HS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
            JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
            WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
              AND HS.BA_CODE = '".$ba_code."' AND HS.MATURITY_STAGE_SMS1 != 'TM'
              AND MAT.COA_CODE = '5101020300' -- TUNGGAL
          )
          UNION ALL
          SELECT * FROM (
            SELECT HS.PERIOD_BUDGET, ORG.REGION_CODE, HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE
              , F_GET_PUPUK_KG_AFDELING(HS.PERIOD_BUDGET, HS.BA_CODE, HS.AFD_CODE) KG_AFDELING
              , HS.MATURITY_STAGE_SMS1 MATURITY_STAGE
              , MAT.COA_CODE
              , 'PUPUK MAJEMUK' ACTIVITY_DESC, 'TRANSPORT' COST_ELEMENT
              , TM.JUMLAH
              , CAST(TM.BULAN_PEMUPUKAN AS INTEGER) BULAN_PEMUPUKAN
              , VH.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION VRA_NAME, VH.UOM
              , VRA.PRICE_HM_KM, VRA.HM_KM
            FROM TM_HECTARE_STATEMENT HS
            JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HS.BA_CODE
            JOIN TN_PUPUK_TBM2_TM TM ON HS.BA_CODE  = TM.BA_CODE AND HS.PERIOD_BUDGET = TM.PERIOD_BUDGET
              AND HS.AFD_CODE = TM.AFD_CODE AND HS.BLOCK_CODE = TM.BLOCK_CODE
            LEFT JOIN TR_RKT_VRA_DISTRIBUSI VRA ON VRA.PERIOD_BUDGET = HS.PERIOD_BUDGET
              AND VRA.BA_CODE = HS.BA_CODE AND VRA.LOCATION_CODE = HS.AFD_CODE AND VRA.ACTIVITY_CODE IN ('43750', '43760')
            JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.BA_CODE = HS.BA_CODE
              AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
            JOIN TM_VRA VH ON VH.VRA_CODE = VRA.VRA_CODE
            WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = '".$period_budget."'
              AND HS.BA_CODE = '".$ba_code."' AND HS.MATURITY_STAGE_SMS1 != 'TM'
              AND MAT.COA_CODE = '5101020400' -- MAJEMUK
          )
        ) P
      )
      PIVOT (
        SUM(JUMLAH/KG_AFDELING*HM_KM) AS DIS, SUM(JUMLAH/KG_AFDELING*PRICE_HM_KM) AS COST
        FOR BULAN_PEMUPUKAN IN (
        '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
        '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
        )
      )
    ";


    // PERKERASAN JALAN
    $devcost_components['perkerasan_jalan_contract'] = "
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
        UOM,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
        'CONTRACT', ACTIVITY_CODE, ACTIVITY_DESC, UOM,
        SUM(QTY_JAN), SUM(QTY_FEB), SUM(QTY_MAR), SUM(QTY_APR), SUM(QTY_MAY), SUM(QTY_JUN),
        SUM(QTY_JUL), SUM(QTY_AUG), SUM(QTY_SEP), SUM(QTY_OCT), SUM(QTY_NOV), SUM(QTY_DEC),
        SUM(COST_JAN), SUM(COST_FEB), SUM(COST_MAR), SUM(COST_APR), SUM(COST_MAY), SUM(COST_JUN),
        SUM(COST_JUL), SUM(COST_AUG), SUM(COST_SEP), SUM(COST_OCT), SUM(COST_NOV), SUM(COST_DEC),
        SUM(QTY_SMS), SUM(COST_SMS), '".$this->_userName."', CURRENT_TIMESTAMP
      FROM (
        -- SEMESTER 1
        SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
          RKT.COST_ELEMENT,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          ACT.UOM,
          RKT_INDUK.PLAN_JAN QTY_JAN,RKT_INDUK.PLAN_FEB QTY_FEB,RKT_INDUK.PLAN_MAR QTY_MAR,
          RKT_INDUK.PLAN_APR QTY_APR,RKT_INDUK.PLAN_MAY QTY_MAY,RKT_INDUK.PLAN_JUN QTY_JUN,
          0 QTY_JUL, 0 QTY_AUG, 0 QTY_SEP, 0 QTY_OCT, 0 QTY_NOV, 0 QTY_DEC,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JAN COST_JAN,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_FEB COST_FEB,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_MAR COST_MAR,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_APR COST_APR,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_MAY COST_MAY,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JUN COST_JUN,
          0 COST_JUL, 0 COST_AUG, 0 COST_SEP, 0 COST_OCT, 0 COST_NOV, 0 COST_DEC,
          RKT_INDUK.PLAN_JAN+RKT_INDUK.PLAN_FEB+RKT_INDUK.PLAN_MAR+RKT_INDUK.PLAN_APR+RKT_INDUK.PLAN_MAY+RKT_INDUK.PLAN_JUN QTY_SMS,
          (RHC.EXTERNAL_PRICE/1000) * (
            RKT_INDUK.PLAN_JAN+RKT_INDUK.PLAN_FEB+RKT_INDUK.PLAN_MAR+
            RKT_INDUK.PLAN_APR+RKT_INDUK.PLAN_MAY+RKT_INDUK.PLAN_JUN
          ) COST_SMS
        FROM TR_RKT_PK_COST_ELEMENT RKT
        JOIN TR_RKT_PK RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
        JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT_INDUK.PERIOD_BUDGET
          AND RH.ACTIVITY_CODE = RKT_INDUK.ACTIVITY_CODE AND RH.BA_CODE = RKT.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
          AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT_INDUK.JARAK 
          AND RHC.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT_INDUK.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS1 != 'TM'
          AND RKT.SUMBER_BIAYA = 'EXTERNAL' AND RKT.COST_ELEMENT = 'CONTRACT'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
        -- SEMESTER 2
        UNION ALL
        SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
          RKT.COST_ELEMENT,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          ACT.UOM,
          0 QTY_JAN, 0 QTY_FEB, 0 QTY_MAR, 0 QTY_APR, 0 QTY_MAY, 0 QTY_JUN,
          RKT_INDUK.PLAN_JUL QTY_JUL,RKT_INDUK.PLAN_AUG QTY_AUG,RKT_INDUK.PLAN_SEP QTY_SEP,
          RKT_INDUK.PLAN_OCT QTY_OCT,RKT_INDUK.PLAN_NOV QTY_NOV,RKT_INDUK.PLAN_DEC QTY_DEC,
          0 COST_JAN, 0 COST_FEB, 0 COST_MAR, 0 COST_APR, 0 COST_MAY, 0 COST_JUN,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_JUL COST_JUL,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_AUG COST_AUG,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_SEP COST_SEP,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_OCT COST_OCT,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_NOV COST_NOV,
          RHC.EXTERNAL_PRICE/1000*RKT_INDUK.PLAN_DEC COST_DEC,
          RKT_INDUK.PLAN_JUL+RKT_INDUK.PLAN_AUG+RKT_INDUK.PLAN_SEP+RKT_INDUK.PLAN_OCT+RKT_INDUK.PLAN_NOV+RKT_INDUK.PLAN_DEC QTY_SMS,
          (RHC.EXTERNAL_PRICE/1000) * (
            RKT_INDUK.PLAN_JUL+RKT_INDUK.PLAN_AUG+RKT_INDUK.PLAN_SEP+
            RKT_INDUK.PLAN_OCT+RKT_INDUK.PLAN_NOV+RKT_INDUK.PLAN_DEC
          ) COST_SMS
        FROM TR_RKT_PK_COST_ELEMENT RKT
        JOIN TR_RKT_PK RKT_INDUK ON RKT_INDUK.TRX_RKT_CODE = RKT.TRX_RKT_CODE
        JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT_INDUK.PERIOD_BUDGET
          AND RH.ACTIVITY_CODE = RKT_INDUK.ACTIVITY_CODE AND RH.BA_CODE = RKT.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
          AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT_INDUK.JARAK 
          AND RHC.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT_INDUK.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS2 != 'TM'
          AND RKT.SUMBER_BIAYA = 'EXTERNAL' AND RKT.COST_ELEMENT = 'CONTRACT'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
      )
      GROUP BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, ACTIVITY_DESC, UOM
    ";

    $devcost_components['perkerasan_jalan_material'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM, SUB_COST_ELEMENT_DESC,
        QTY_JAN,QTY_FEB,QTY_MAR,QTY_APR,QTY_MAY,QTY_JUN,QTY_JUL,QTY_AUG,QTY_SEP,QTY_OCT,QTY_NOV,QTY_DEC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
        ACTIVITY_CODE, ACTIVITY_DESC, 'MATERIAL', UOM, MATERIAL_NAME,
        SUM(QTY_JAN), SUM(QTY_FEB), SUM(QTY_MAR), SUM(QTY_APR), SUM(QTY_MAY), SUM(QTY_JUN),
        SUM(QTY_JUL), SUM(QTY_AUG), SUM(QTY_SEP), SUM(QTY_OCT), SUM(QTY_NOV), SUM(QTY_DEC),
        SUM(COST_JAN), SUM(COST_FEB), SUM(COST_MAR), SUM(COST_APR), SUM(COST_MAY), SUM(COST_JUN),
        SUM(COST_JUL), SUM(COST_AUG), SUM(COST_SEP), SUM(COST_OCT), SUM(COST_NOV), SUM(COST_DEC),
        SUM(QTY_SMS), SUM(COST_SMS), '".$this->_userName."', CURRENT_TIMESTAMP
      FROM (
        SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          MAT.UOM, MAT.MATERIAL_NAME,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JAN QTY_JAN,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_FEB QTY_FEB,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_MAR QTY_MAR,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_APR QTY_APR,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_MAY QTY_MAY,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JUN QTY_JUN,
          0 QTY_JUL, 0 QTY_AUG, 0 QTY_SEP, 0 QTY_OCT, 0 QTY_NOV, 0 QTY_DEC,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JAN*RH.PRICE COST_JAN,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_FEB*RH.PRICE COST_FEB,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_MAR*RH.PRICE COST_MAR,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_APR*RH.PRICE COST_APR,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_MAY*RH.PRICE COST_MAY,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JUN*RH.PRICE COST_JUN,
          0 COST_JUL, 0 COST_AUG, 0 COST_SEP, 0 COST_OCT, 0 COST_NOV, 0 COST_DEC,
          (RHC.MATERIAL_QTY/1000) * (RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+RKT.PLAN_MAY+RKT.PLAN_JUN) QTY_SMS,
          (RHC.MATERIAL_QTY/1000) * (RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+RKT.PLAN_MAY+RKT.PLAN_JUN) * RH.PRICE COST_SMS
        FROM TR_RKT_PK RKT
        JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND RH.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND RH.BA_CODE = RKT.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
          AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT.JARAK
          AND RHC.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL
          AND RKT.MATURITY_STAGE_SMS1 != 'TM' AND RKT.JENIS_PEKERJAAN = 'PERULANGAN' 
          AND RKT.SUMBER_BIAYA = 'INTERNAL'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."' 
          AND RKT.BA_CODE = '".$params['key_find']."'
        UNION ALL
        SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          MAT.UOM, MAT.MATERIAL_NAME,
          0 QTY_JAN, 0 QTY_FEB, 0 QTY_MAR, 0 QTY_APR, 0 QTY_MAY, 0 QTY_JUN,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JUL QTY_JUL,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_AUG QTY_AUG,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_SEP QTY_SEP,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_OCT QTY_OCT,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_NOV QTY_NOV,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_DEC QTY_DEC,
          0 COST_JAN, 0 COST_FEB, 0 COST_MAR, 0 COST_APR, 0 COST_MAY, 0 COST_JUN,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_JUL*RH.PRICE COST_JUL,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_AUG*RH.PRICE COST_AUG,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_SEP*RH.PRICE COST_SEP,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_OCT*RH.PRICE COST_OCT,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_NOV*RH.PRICE COST_NOV,
          RHC.MATERIAL_QTY/1000*RKT.PLAN_DEC*RH.PRICE COST_DEC,
          (RHC.MATERIAL_QTY/1000) * (RKT.PLAN_JUL+RKT.PLAN_AUG+RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC) QTY_SMS,
          (RHC.MATERIAL_QTY/1000) * (RKT.PLAN_JUL+RKT.PLAN_AUG+RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC) * RH.PRICE COST_SMS
        FROM TR_RKT_PK RKT
        JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN TN_PERKERASAN_JALAN RH ON RH.PERIOD_BUDGET = RKT.PERIOD_BUDGET
          AND RH.ACTIVITY_CODE = RKT.ACTIVITY_CODE AND RH.BA_CODE = RKT.BA_CODE
        JOIN TM_MATERIAL MAT ON MAT.MATERIAL_CODE = RH.MATERIAL_CODE
          AND MAT.PERIOD_BUDGET = RH.PERIOD_BUDGET AND MAT.BA_CODE = RH.BA_CODE
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT.JARAK
          AND RHC.BA_CODE = RKT.BA_CODE
        WHERE RKT.DELETE_USER IS NULL
          AND RKT.MATURITY_STAGE_SMS2 != 'TM' AND RKT.JENIS_PEKERJAAN = 'PERULANGAN' 
          AND RKT.SUMBER_BIAYA = 'INTERNAL'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."' 
          AND RKT.BA_CODE = '".$params['key_find']."'
      )
      GROUP BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, ACTIVITY_DESC, UOM, MATERIAL_NAME
    ";

    $devcost_components['perkerasan_jalan_transport'] = "
      INSERT INTO TMP_RPT_KEB_DEV_COST_BLOCK (
        PERIOD_BUDGET, 
        REGION_CODE, 
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        TIPE_TRANSAKSI, 
        ACTIVITY_CODE,
        ACTIVITY_DESC,
        COST_ELEMENT,
        UOM,SUB_COST_ELEMENT_DESC,
        COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC,
        COST_SETAHUN,
        QTY_JAN, QTY_FEB, QTY_MAR, QTY_APR, QTY_MAY, QTY_JUN, QTY_JUL, QTY_AUG, QTY_SEP, QTY_OCT, QTY_NOV, QTY_DEC,
        QTY_SETAHUN, 
        INSERT_USER, INSERT_TIME
      )
      SELECT 
        PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
        ACTIVITY_CODE, ACTIVITY_DESC, 'TRANSPORT', UOM, VRA_NAME,
        SUM(QTY_JAN), SUM(QTY_FEB), SUM(QTY_MAR), SUM(QTY_APR), SUM(QTY_MAY), SUM(QTY_JUN),
        SUM(QTY_JUL), SUM(QTY_AUG), SUM(QTY_SEP), SUM(QTY_OCT), SUM(QTY_NOV), SUM(QTY_DEC),
        SUM(COST_JAN), SUM(COST_FEB), SUM(COST_MAR), SUM(COST_APR), SUM(COST_MAY), SUM(COST_JUN),
        SUM(COST_JUL), SUM(COST_AUG), SUM(COST_SEP), SUM(COST_OCT), SUM(COST_NOV), SUM(COST_DEC),
        SUM(QTY_SMS), SUM(COST_SMS), '".$this->_userName."', CURRENT_TIMESTAMP
      FROM 
      (
        SELECT RKT.PERIOD_BUDGET,
          ORG.REGION_CODE,
          RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
          RKT.MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
          RKT.ACTIVITY_CODE,
          ACT.DESCRIPTION AS ACTIVITY_DESC,
          COALESCE(V1.UOM,V2.UOM,V3.UOM,V4.UOM) UOM,
          COALESCE(V1.VRA_SUB_CAT_DESCRIPTION,V2.VRA_SUB_CAT_DESCRIPTION,V3.VRA_SUB_CAT_DESCRIPTION,V4.VRA_SUB_CAT_DESCRIPTION) VRA_NAME,
          RKT.PLAN_JAN*RH.RATE/1000 QTY_JAN, RKT.PLAN_FEB*RH.RATE/1000 QTY_FEB, RKT.PLAN_MAR*RH.RATE/1000 QTY_MAR, 
          RKT.PLAN_APR*RH.RATE/1000 QTY_APR, RKT.PLAN_MAY*RH.RATE/1000 QTY_MAY, RKT.PLAN_JUN*RH.RATE/1000 QTY_JUN, 
          0 QTY_JUL, 0 QTY_AUG, 0 QTY_SEP, 0 QTY_OCT, 0 QTY_NOV, 0 QTY_DEC,
          RKT.PLAN_JAN/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_JAN,
          RKT.PLAN_FEB/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_FEB,
          RKT.PLAN_MAR/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_MAR,
          RKT.PLAN_APR/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_APR,
          RKT.PLAN_MAY/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_MAY,
          RKT.PLAN_JUN/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_JUN,
          0 COST_JUL, 0 COST_AUG, 0 COST_SEP, 0 COST_OCT, 0 COST_NOV, 0 COST_DEC,
          (RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+RKT.PLAN_MAY+RKT.PLAN_JUN) * (RH.RATE/1000) QTY_SMS,
          (RKT.PLAN_JAN+RKT.PLAN_FEB+RKT.PLAN_MAR+RKT.PLAN_APR+RKT.PLAN_MAY+RKT.PLAN_JUN) * (NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM) COST_SMS, 
          '".$this->_userName."', CURRENT_TIMESTAMP
        FROM TR_RKT_PK RKT
        LEFT JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN (
          SELECT PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, JARAK_RANGE
          , CASE
              WHEN COMPONENT LIKE '%DT_PRICE%' THEN 'DT010'
              WHEN COMPONENT LIKE '%EXCAV_PRICE%' THEN 'EX011'
              WHEN COMPONENT LIKE '%COMPACTOR_PRICE%' THEN 'VC010'
              ELSE 'GD010'
            END VRA_CODE
          , HM_KM
          , RATE FROM (
            SELECT PERIOD_BUDGET,BA_CODE,ACTIVITY_CODE,JARAK_RANGE,DT_PRICE,EXCAV_PRICE,COMPACTOR_PRICE,GRADER_PRICE
            ,DT_TRIP,EXCAV_HM,COMPACTOR_HM,GRADER_HM
            FROM TN_PERKERASAN_JALAN_HARGA WHERE ACTIVITY_CODE =  '20311'
            AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."' 
            AND BA_CODE = '".$params['key_find']."'
          ) A
          UNPIVOT
          (
            (HM_KM, RATE) FOR COMPONENT IN (
              (DT_TRIP, DT_PRICE),
              (EXCAV_HM, EXCAV_PRICE),
              (COMPACTOR_HM, COMPACTOR_PRICE),
              (GRADER_HM, GRADER_PRICE)
            )
          )
        ) RH ON RH.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RH.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RH.JARAK_RANGE = RKT.JARAK
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT.JARAK
        JOIN TM_VRA V1 ON V1.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V2 ON V2.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V3 ON V3.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V4 ON V4.VRA_CODE = RH.VRA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS1 != 'TM' AND RKT.JENIS_PEKERJAAN = 'PERULANGAN' 
          AND RKT.SUMBER_BIAYA = 'INTERNAL'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
        UNION ALL
        SELECT RKT.PERIOD_BUDGET,
            ORG.REGION_CODE,
            RKT.BA_CODE, RKT.AFD_CODE,RKT.BLOCK_CODE,
            RKT.MATURITY_STAGE_SMS2 AS TIPE_TRANSAKSI,
            RKT.ACTIVITY_CODE,
            ACT.DESCRIPTION AS ACTIVITY_DESC,
            COALESCE(V1.UOM,V2.UOM,V3.UOM,V4.UOM) UOM,
            COALESCE(V1.VRA_SUB_CAT_DESCRIPTION,V2.VRA_SUB_CAT_DESCRIPTION,V3.VRA_SUB_CAT_DESCRIPTION,V4.VRA_SUB_CAT_DESCRIPTION) VRA_NAME,
            0 JAN, 0 FEB, 0 MAR, 0 APR, 0 MAY, 0 JUN, 
            RKT.PLAN_JUL*RH.RATE/1000 JUL, RKT.PLAN_AUG*RH.RATE/1000 AUG, RKT.PLAN_SEP*RH.RATE/1000 SEP, 
            RKT.PLAN_OCT*RH.RATE/1000 OCT, RKT.PLAN_NOV*RH.RATE/1000 NOV, RKT.PLAN_DEC*RH.RATE/1000 DEC, 
            0COST_JAN , 0COST_FEB , 0 COST_MAR, 0 COST_APR, 0 COST_MAY, 0 COST_JUN,
            RKT.PLAN_JUL/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_JUL,
            RKT.PLAN_AUG/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_AUG,
            RKT.PLAN_SEP/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_SEP,
            RKT.PLAN_OCT/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_OCT,
            RKT.PLAN_NOV/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_NOV,
            RKT.PLAN_DEC/NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM COST_DEC,
            (RKT.PLAN_JUL+RKT.PLAN_AUG+RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC) * (RH.RATE/1000) QTY_SMS,
            (RKT.PLAN_JUL+RKT.PLAN_AUG+RKT.PLAN_SEP+RKT.PLAN_OCT+RKT.PLAN_NOV+RKT.PLAN_DEC) * (NULLIF(RKT.PLAN_SETAHUN,0)*RH.HM_KM) COST_SMS,
            '".$this->_userName."', CURRENT_TIMESTAMP
        FROM TR_RKT_PK RKT
        LEFT JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = RKT.ACTIVITY_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = RKT.BA_CODE
        JOIN (
          SELECT PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, JARAK_RANGE
          , CASE
              WHEN COMPONENT LIKE '%DT_PRICE%' THEN 'DT010'
              WHEN COMPONENT LIKE '%EXCAV_PRICE%' THEN 'EX011'
              WHEN COMPONENT LIKE '%COMPACTOR_PRICE%' THEN 'VC010'
              ELSE 'GD010'
            END VRA_CODE
          , HM_KM
          , RATE FROM (
            SELECT PERIOD_BUDGET,BA_CODE,ACTIVITY_CODE,JARAK_RANGE,DT_PRICE,EXCAV_PRICE,COMPACTOR_PRICE,GRADER_PRICE
            ,DT_TRIP,EXCAV_HM,COMPACTOR_HM,GRADER_HM
            FROM TN_PERKERASAN_JALAN_HARGA WHERE ACTIVITY_CODE =  '20311'
            AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."' 
            AND BA_CODE = '".$params['key_find']."'
          ) A
          UNPIVOT
          (
            (HM_KM, RATE) FOR COMPONENT IN (
              (DT_TRIP, DT_PRICE),
              (EXCAV_HM, EXCAV_PRICE),
              (COMPACTOR_HM, COMPACTOR_PRICE),
              (GRADER_HM, GRADER_PRICE)
            )
          )
        ) RH ON RH.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RH.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RH.JARAK_RANGE = RKT.JARAK
        JOIN TN_PERKERASAN_JALAN_HARGA RHC ON RHC.ACTIVITY_CODE = RKT.ACTIVITY_CODE
          AND RHC.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND RHC.JARAK_RANGE = RKT.JARAK
        JOIN TM_VRA V1 ON V1.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V2 ON V2.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V3 ON V3.VRA_CODE = RH.VRA_CODE
        JOIN TM_VRA V4 ON V4.VRA_CODE = RH.VRA_CODE
        WHERE RKT.DELETE_USER IS NULL AND RKT.FLAG_TEMP IS NULL
          AND RKT.MATURITY_STAGE_SMS2 != 'TM' AND RKT.JENIS_PEKERJAAN = 'PERULANGAN' 
          AND RKT.SUMBER_BIAYA = 'INTERNAL'
          AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND RKT.BA_CODE = '".$params['key_find']."'
      )
      GROUP BY PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, ACTIVITY_DESC, UOM, VRA_NAME
    ";

    $devcost_components['kebutuhan_umum'] = "
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
        QTY_JAN,  QTY_FEB,  QTY_MAR,  QTY_APR,  QTY_MAY,  QTY_JUN,  QTY_JUL,  QTY_AUG,  QTY_SEP,  QTY_OCT,  QTY_NOV,  QTY_DEC,
        COST_JAN,  COST_FEB,  COST_MAR,  COST_APR,  COST_MAY,  COST_JUN,  COST_JUL,  COST_AUG,  COST_SEP,  COST_OCT,  COST_NOV,  COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
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
        SELECT
          RKT.PERIOD_BUDGET,
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
          ".$this->freeColumnQty('sm1').", 
          ".$this->freeColumnQty('sm2').",
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_JAN) AS COST_JAN,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_FEB) AS COST_FEB,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_MAR) AS COST_MAR,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_APR) AS COST_APR,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_MAY) AS COST_MAY,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM1(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_JUN) AS COST_JUN,
          ".$this->freeColumnCost('sm2')."
        FROM TM_HECTARE_STATEMENT RKT
        LEFT JOIN TR_RKT_OPEX OPEX ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND OPEX.BA_CODE = RKT.BA_CODE
        LEFT JOIN TM_COA COA ON COA.COA_CODE = OPEX.COA_CODE
        WHERE OPEX.COA_CODE NOT IN('1212010101','5101030504')
          AND RKT.MATURITY_STAGE_SMS1 IN('TBM0','TBM1','TBM2','TBM3') 
          AND RKT.BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$period_budget."'
        UNION ALL 
        SELECT
          RKT.PERIOD_BUDGET,
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
          ".$this->freeColumnQty('sm1').", 
          ".$this->freeColumnQty('sm2').",
          ".$this->freeColumnCost('sm1').",
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_JUL) AS COST_JUL,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_AUG) AS COST_AUG,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_SEP) AS COST_SEP,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_OCT) AS COST_OCT,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_NOV) AS COST_NOV,
          (RKT.HA_PLANTED /(F_GET_HA_PLANTED_SUMMARY_SM2(RKT.PERIOD_BUDGET, RKT.BA_CODE))* OPEX.DIS_DEC) AS COST_DEC
        FROM TM_HECTARE_STATEMENT RKT
        LEFT JOIN TR_RKT_OPEX OPEX ON OPEX.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND OPEX.BA_CODE = RKT.BA_CODE
        LEFT JOIN TM_COA COA ON COA.COA_CODE = OPEX.COA_CODE
        WHERE OPEX.COA_CODE NOT IN('1212010101','5101030504')
          AND RKT.MATURITY_STAGE_SMS2 IN('TBM0','TBM1','TBM2','TBM3') 
          AND RKT.BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM RKT.PERIOD_BUDGET) = '".$period_budget."'
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

    $devcost_components['GAJI'] = "
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
        QTY_JAN,  QTY_FEB,  QTY_MAR,  QTY_APR,  QTY_MAY,  QTY_JUN,  QTY_JUL,  QTY_AUG,  QTY_SEP,  QTY_OCT,  QTY_NOV,  QTY_DEC,
        COST_JAN,  COST_FEB,  COST_MAR,  COST_APR,  COST_MAY,  COST_JUN,  COST_JUL,  COST_AUG,  COST_SEP,  COST_OCT,  COST_NOV,  COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT 
        PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
        'LABOUR' AS COST_ELEMENT,
        'GAJI' AS ACTIVITY_CODE, 
        'GAJI' AS ACTIVITY_DESC,
        '' AS SUB_COST_ELEMENT, 
        '' AS MATERIAL_NAME,
        '' KETERANGAN,
        '' UOM,
        SUM(NVL (QTY_JAN,0)) AS QTY_JAN,
        SUM(NVL (QTY_FEB,0)) AS QTY_FEB,
        SUM(NVL (QTY_MAR,0)) AS QTY_MAR,
        SUM(NVL (QTY_APR,0)) AS QTY_APR,
        SUM(NVL (QTY_MAY,0)) AS QTY_MAY,
        SUM(NVL (QTY_JUN,0)) AS QTY_JUN,
        SUM(NVL (QTY_JUL,0)) AS QTY_JUL,
        SUM(NVL (QTY_AUG,0)) AS QTY_AUG,
        SUM(NVL (QTY_SEP,0)) AS QTY_SEP,
        SUM(NVL (QTY_OCT,0)) AS QTY_OCT,
        SUM(NVL (QTY_NOV,0)) AS QTY_NOV,
        SUM(NVL (QTY_DEC,0)) AS QTY_DEC,
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
        (SUM(NVL (QTY_JAN,0)) +
        SUM(NVL (QTY_FEB,0)) +
        SUM(NVL (QTY_MAR,0)) +
        SUM(NVL (QTY_APR,0)) +
        SUM(NVL (QTY_MAY,0)) +
        SUM(NVL (QTY_JUN,0)) +
        SUM(NVL (QTY_JUL,0)) +
        SUM(NVL (QTY_AUG,0)) +
        SUM(NVL (QTY_SEP,0)) +
        SUM(NVL (QTY_OCT,0)) +
        SUM(NVL (QTY_NOV,0)) +
        SUM(NVL (QTY_DEC,0))) AS QTY_SETAHUN,
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
        SELECT
          HA_TM.PERIOD_BUDGET,
          ORG.REGION_CODE,
          HA_TM.BA_CODE,
          HA_TM.AFD_CODE,
          HA_TM.BLOCK_CODE,
          HA_TM.MATURITY_STAGE_SMS1,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JAN,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_FEB,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_MAR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_APR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_MAY,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JUN,
          ".$this->freeColumnQty('sm2').", 
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_JAN,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_FEB,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_MAR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_APR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_MAY,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_JUN,
          ".$this->freeColumnCost('sm2')."
        FROM (
          SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, MATURITY_STAGE_SMS1, HA_PLANTED
          , BPS_PROD.F_GET_HA_PLANTED_SUMMARY_SM1(PERIOD_BUDGET, BA_CODE) HA
          FROM TM_HECTARE_STATEMENT
          WHERE MATURITY_STAGE_SMS1 != 'TM'
          AND BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$period_budget."'
        ) HA_TM
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HA_TM.BA_CODE
        LEFT JOIN(
          SELECT CR.PERIOD_BUDGET, CR.BA_CODE, SUM(CR.TOTAL_GP_MPP) GP_MPP, SUM(CR.MPP_PERIOD_BUDGET) MPP
          FROM TM_JOB_TYPE JT
          JOIN TR_RKT_CHECKROLL CR ON CR.JOB_CODE = JT.JOB_CODE AND JT.JOB_TYPE = 'OT'
          WHERE CR.DELETE_TIME IS NULL AND CR.BA_CODE = '".$ba_code."'
          AND EXTRACT(YEAR FROM CR.PERIOD_BUDGET) = '".$period_budget."'
          GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE
        ) TTL_COST ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET AND HA_TM.BA_CODE = TTL_COST.BA_CODE
        UNION ALL --HITUNG TUNJANGAN UNTUK SMS2
        SELECT
          HA_TM.PERIOD_BUDGET,
          ORG.REGION_CODE,
          HA_TM.BA_CODE,
          HA_TM.AFD_CODE,
          HA_TM.BLOCK_CODE,
          HA_TM.MATURITY_STAGE_SMS2,
          ".$this->freeColumnQty('sm1').", 
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JUL,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_AUG,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_SEP,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_OCT,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_NOV,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_DEC,
          ".$this->freeColumnCost('sm1').",
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_JUL,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_AUG,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_SEP,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_OCT,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_NOV,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.GP_MPP) AS COST_DEC
        FROM (
          SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, MATURITY_STAGE_SMS2, HA_PLANTED
          , BPS_PROD.F_GET_HA_PLANTED_SUMMARY_SM2(PERIOD_BUDGET, BA_CODE) HA
          FROM TM_HECTARE_STATEMENT
          WHERE MATURITY_STAGE_SMS2 != 'TM'
          AND BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$period_budget."'
        ) HA_TM
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = HA_TM.BA_CODE
        LEFT JOIN (
          SELECT CR.PERIOD_BUDGET, CR.BA_CODE, SUM(CR.TOTAL_GP_MPP) GP_MPP, SUM(CR.MPP_PERIOD_BUDGET) MPP
          FROM TM_JOB_TYPE JT
          JOIN TR_RKT_CHECKROLL CR ON CR.JOB_CODE = JT.JOB_CODE AND JT.JOB_TYPE = 'OT'
          WHERE CR.DELETE_TIME IS NULL AND CR.BA_CODE = '".$ba_code."'
          AND EXTRACT(YEAR FROM CR.PERIOD_BUDGET) = '".$period_budget."'
          GROUP BY CR.PERIOD_BUDGET, CR.BA_CODE
        ) TTL_COST ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET AND HA_TM.BA_CODE = TTL_COST.BA_CODE
      )
      GROUP BY PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        MATURITY_STAGE_SMS1
    ";
    foreach ($devcost_components as $key => $sql_string) {
      $this->_db->query($sql_string);
      $this->_db->commit();
    }

    // KOMPONEN GAJI YANG MASUK KE BIAYA UMUM
    // ASTEK, CATU, JABATAN, KEHADIRAN, LAINNYA
    $salary_components = array('ASTEK', 'CATU', 'JABATAN', 'KEHADIRAN', 'LAINNYA');
    $devcost_sallary_components = "
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
        QTY_JAN,  QTY_FEB,  QTY_MAR,  QTY_APR,  QTY_MAY,  QTY_JUN,  QTY_JUL,  QTY_AUG,  QTY_SEP,  QTY_OCT,  QTY_NOV,  QTY_DEC,
        COST_JAN,  COST_FEB,  COST_MAR,  COST_APR,  COST_MAY,  COST_JUN,  COST_JUL,  COST_AUG,  COST_SEP,  COST_OCT,  COST_NOV,  COST_DEC,
        QTY_SETAHUN, COST_SETAHUN,
        INSERT_USER, INSERT_TIME
      )
      SELECT PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        MATURITY_STAGE_SMS1 AS TIPE_TRANSAKSI,
        'LABOUR' AS COST_ELEMENT,
        '{SALARY_COMPONENT}' AS ACTIVITY_CODE, 
        '{SALARY_COMPONENT}' AS ACTIVITY_DESC,
        '' AS SUB_COST_ELEMENT, 
        '' AS MATERIAL_NAME,
        '' KETERANGAN,
        '' UOM,
        SUM(NVL (QTY_JAN,0)) AS QTY_JAN,
        SUM(NVL (QTY_FEB,0)) AS QTY_FEB,
        SUM(NVL (QTY_MAR,0)) AS QTY_MAR,
        SUM(NVL (QTY_APR,0)) AS QTY_APR,
        SUM(NVL (QTY_MAY,0)) AS QTY_MAY,
        SUM(NVL (QTY_JUN,0)) AS QTY_JUN,
        SUM(NVL (QTY_JUL,0)) AS QTY_JUL,
        SUM(NVL (QTY_AUG,0)) AS QTY_AUG,
        SUM(NVL (QTY_SEP,0)) AS QTY_SEP,
        SUM(NVL (QTY_OCT,0)) AS QTY_OCT,
        SUM(NVL (QTY_NOV,0)) AS QTY_NOV,
        SUM(NVL (QTY_DEC,0)) AS QTY_DEC,
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
        (SUM(NVL (QTY_JAN,0)) +
        SUM(NVL (QTY_FEB,0)) +
        SUM(NVL (QTY_MAR,0)) +
        SUM(NVL (QTY_APR,0)) +
        SUM(NVL (QTY_MAY,0)) +
        SUM(NVL (QTY_JUN,0)) +
        SUM(NVL (QTY_JUL,0)) +
        SUM(NVL (QTY_AUG,0)) +
        SUM(NVL (QTY_SEP,0)) +
        SUM(NVL (QTY_OCT,0)) +
        SUM(NVL (QTY_NOV,0)) +
        SUM(NVL (QTY_DEC,0))) AS QTY_SETAHUN,
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
        CURRENT_TIMESTAMP AS INSERT_TIME
      FROM (
        SELECT
          HA_TM.PERIOD_BUDGET,
          ORG.REGION_CODE,
          HA_TM.BA_CODE,
          HA_TM.AFD_CODE,
          HA_TM.BLOCK_CODE,
          HA_TM.MATURITY_STAGE_SMS1,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JAN,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_FEB,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_MAR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_APR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_MAY,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JUN,
          ".$this->freeColumnQty('sm2').",
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_JAN,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_FEB,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_MAR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_APR,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_MAY,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_JUN,
          ".$this->freeColumnCost('sm2')."
        FROM (
          SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, MATURITY_STAGE_SMS1, HA_PLANTED
          , BPS_PROD.F_GET_HA_PLANTED_SUMMARY_SM1(PERIOD_BUDGET, BA_CODE) HA
          FROM BPS_PROD.TM_HECTARE_STATEMENT
          WHERE MATURITY_STAGE_SMS1 != 'TM'
          AND BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$period_budget."'
        ) HA_TM
        LEFT JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HA_TM.BA_CODE
        LEFT JOIN(
          SELECT S.PERIOD_BUDGET, S.BA_CODE, S.TUNJANGAN_TYPE
          , SUM(S.TOTAL_GP_MPP) GP_ALL
          , SUM(S.JUMLAH * S.MPP_PERIOD_BUDGET) COST_BA, S.MPP 
          FROM (
            SELECT CR.PERIOD_BUDGET, CR.BA_CODE
              , CD.TUNJANGAN_TYPE
              , JT.JOB_TYPE
              , CR.TOTAL_GP_MPP
              , CD.JUMLAH
              , CR.MPP_PERIOD_BUDGET
              , SUM(CR.MPP_PERIOD_BUDGET) OVER (PARTITION BY CR.PERIOD_BUDGET, CR.BA_CODE) MPP
            FROM BPS_PROD.TM_JOB_TYPE JT
            JOIN BPS_PROD.TR_RKT_CHECKROLL CR ON CR.JOB_CODE = JT.JOB_CODE
            JOIN BPS_PROD.TR_RKT_CHECKROLL_DETAIL CD ON CD.TRX_CR_CODE = CR.TRX_CR_CODE
            WHERE CR.DELETE_TIME IS NULL AND CR.BA_CODE = '".$ba_code."'
            AND EXTRACT(YEAR FROM CR.PERIOD_BUDGET) = '".$period_budget."'
            AND CD.TUNJANGAN_TYPE = '{SALARY_COMPONENT}'
            ORDER BY 2
          ) S WHERE JOB_TYPE = 'OT'
          GROUP BY S.PERIOD_BUDGET, S.BA_CODE, S.TUNJANGAN_TYPE,S.MPP 
        ) TTL_COST ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET AND HA_TM.BA_CODE = TTL_COST.BA_CODE
        UNION ALL --HITUNG TUNJANGAN UNTUK SMS2
        SELECT
          HA_TM.PERIOD_BUDGET,
          ORG.REGION_CODE,
          HA_TM.BA_CODE,
          HA_TM.AFD_CODE,
          HA_TM.BLOCK_CODE,
          HA_TM.MATURITY_STAGE_SMS2,
          ".$this->freeColumnQty('sm1').", 
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_JUL,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_AUG,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_SEP,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_OCT,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_NOV,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.MPP) AS QTY_DEC,
          ".$this->freeColumnCost('sm1').",
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_JUL,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_AUG,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_SEP,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_OCT,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_NOV,
          (HA_TM.HA_PLANTED / HA_TM.HA*TTL_COST.COST_BA) AS COST_DEC
        FROM (
          SELECT PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, MATURITY_STAGE_SMS2, HA_PLANTED
          , BPS_PROD.F_GET_HA_PLANTED_SUMMARY_SM2(PERIOD_BUDGET, BA_CODE) HA
          FROM BPS_PROD.TM_HECTARE_STATEMENT
          WHERE MATURITY_STAGE_SMS2 != 'TM'
          AND BA_CODE = '".$ba_code."' AND EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$period_budget."'
        ) HA_TM
        LEFT JOIN BPS_PROD.TM_ORGANIZATION ORG ON ORG.BA_CODE = HA_TM.BA_CODE
        LEFT JOIN (
          SELECT S.PERIOD_BUDGET, S.BA_CODE, S.TUNJANGAN_TYPE
          , SUM(S.TOTAL_GP_MPP) GP_ALL
          , SUM(S.JUMLAH * S.MPP_PERIOD_BUDGET) COST_BA, S.MPP 
          FROM (
            SELECT CR.PERIOD_BUDGET, CR.BA_CODE
              , CD.TUNJANGAN_TYPE
              , JT.JOB_TYPE
              , CR.TOTAL_GP_MPP
              , CD.JUMLAH
              , CR.MPP_PERIOD_BUDGET
              , SUM(CR.MPP_PERIOD_BUDGET) OVER (PARTITION BY CR.PERIOD_BUDGET, CR.BA_CODE) MPP
            FROM BPS_PROD.TM_JOB_TYPE JT
            JOIN BPS_PROD.TR_RKT_CHECKROLL CR ON CR.JOB_CODE = JT.JOB_CODE --AND JT.JOB_TYPE = 'OT'
            JOIN BPS_PROD.TR_RKT_CHECKROLL_DETAIL CD ON CD.TRX_CR_CODE = CR.TRX_CR_CODE
            WHERE CR.DELETE_TIME IS NULL AND CR.BA_CODE = '".$ba_code."'
            AND EXTRACT(YEAR FROM CR.PERIOD_BUDGET) = '".$period_budget."'
            AND CD.TUNJANGAN_TYPE = '{SALARY_COMPONENT}'
            ORDER BY 2
          ) S WHERE JOB_TYPE = 'OT'
          GROUP BY S.PERIOD_BUDGET, S.BA_CODE, S.TUNJANGAN_TYPE,S.MPP 
        ) TTL_COST ON HA_TM.PERIOD_BUDGET = TTL_COST.PERIOD_BUDGET AND HA_TM.BA_CODE = TTL_COST.BA_CODE
      )
      GROUP BY PERIOD_BUDGET,
        REGION_CODE,
        BA_CODE,
        AFD_CODE,
        BLOCK_CODE,
        MATURITY_STAGE_SMS1
    ";
    foreach ($salary_components as $key) {
      $sql_string = str_replace('{SALARY_COMPONENT}', $key, $devcost_sallary_components);
      $this->_db->query($sql_string);
      $this->_db->commit();
    }

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
      SELECT  MAX(INSERT_USER) INSERT_USER,
          TO_CHAR( MAX(INSERT_TIME), 'DD-MM-RRRR HH24:MI:SS') INSERT_TIME
      FROM (
        SELECT  MAX(INSERT_USER) INSERT_USER,
            MAX(INSERT_TIME) INSERT_TIME
        FROM TMP_RPT_KEB_DEV_COST_BLOCK
        WHERE 1 = 1
        $where
        UNION ALL
        SELECT  MAX(INSERT_USER) INSERT_USER,
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
        SELECT  GROUP_CODE, 
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
      SELECT  $select_group
          REPORT.*,
          ORG.ESTATE_NAME
      FROM (
        SELECT  CASE
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
          SELECT  TO_CHAR(HIRARKI)  AS HIRARKI, 
              LVL, 
              TO_CHAR(GROUP_CODE) AS GROUP_CODE
          FROM (
            SELECT  GROUP_CODE, 
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
           REPORT.SUB_COST_ELEMENT_DESC,
           REPORT.KETERANGAN
      ";
    // print_r($query);die();
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
        SELECT  GROUP_CODE, 
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
      SELECT  $select_group
          REPORT.*,
          ORG.ESTATE_NAME
      FROM (
        SELECT  CASE
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
          SELECT  TO_CHAR(HIRARKI)  AS HIRARKI, 
              LVL, 
              TO_CHAR(GROUP_CODE) AS GROUP_CODE
          FROM (
            SELECT  GROUP_CODE, 
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
        SELECT  GROUP_CODE, 
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
      SELECT  $select_group
          REPORT.*,
          ORG.ESTATE_NAME
      FROM (
        SELECT  CASE
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
          SELECT  TO_CHAR(HIRARKI)  AS HIRARKI, 
              LVL, 
              TO_CHAR(GROUP_CODE) AS GROUP_CODE
          FROM (
            SELECT  GROUP_CODE, 
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
  public function reportKebAktivitasDevPerAfd($params = array()) {
    $where = $select_group = $order_group = "";
    $params['uniq_code'] = $this->_global->genFileName();
    
    /* ################################################### generate excel development cost ################################################### */
    //cari jumlah group report
    $query = "
      SELECT MAX(LVL) - 1
      FROM (
        SELECT  GROUP_CODE, 
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
      SELECT  $select_group
          REPORT.*,
          ORG.ESTATE_NAME
      FROM (
        SELECT  CASE
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
          SELECT  TO_CHAR(HIRARKI)  AS HIRARKI, 
              LVL, 
              TO_CHAR(GROUP_CODE) AS GROUP_CODE
          FROM (
            SELECT  GROUP_CODE, 
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
          SELECT PERIOD_BUDGET,
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
  
  public function freeColumnCost($params)
  {
    if($params == 'sm1') {
      $free_column = "0 as COST_JAN, 0 as COST_FEB, 0 as COST_MAR, 0 as COST_APR, 0 as COST_MAY, 0 as COST_JUN";
    } else {
      $free_column = "0 as COST_JUL, 0 as COST_AUG, 0 as COST_SEP, 0 as COST_OCT, 0 as COST_NOV, 0 as COST_DEC";
    }
    return $free_column;
  }

  public function freeColumnQty($params)
  {
    if($params == 'sm1') {
      $free_column = "0 as QTY_JAN, 0 as QTY_FEB, 0 as QTY_MAR, 0 as QTY_APR, 0 as QTY_MAY, 0 as QTY_JUN";
    } else {
      $free_column = "0 as QTY_JUL, 0 as QTY_AUG, 0 as QTY_SEP, 0 as QTY_OCT, 0 as QTY_NOV, 0 as QTY_DEC";
    }
    return $free_column;
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

