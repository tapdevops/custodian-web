<?php
/*
=========================================================================================================================
Project       :   Budgeting & Planning System
Versi       :   3.0.0
Deskripsi     :   Model Class untuk Calculate RKT
Function      : - getInput              : YIR 20/06/2014  : setting input untuk region
            - tmpRptDevCost           : SID 04/08/2014  : generate temp table untuk dev cost
            - delTmpRptDevCost          : SID 04/08/2014  : hapus temp table untuk dev cost
            - reportDevelopmentCost       : SID 04/08/2014  : generate report development cost
            - reportSummaryDevelopmentCost    : SID 04/08/2014  : generate report summary development cost
            - tmpRptEstCost           : SID 05/08/2014  : generate temp table untuk estate cost
            - delTmpRptEstCost          : SID 05/08/2014  : hapus temp table untuk estate cost
            - reportEstateCost          : SID 05/08/2014  : generate report estate cost
            - reportSummaryEstateCost     : SID 05/08/2014  : generate report summary estate cost
            - reportCapex           : SID 05/08/2014  : generate report CAPEX
            - reportSebaranHa         : SID 23/08/2013  : generate report sebaran HA
            - reportVraUtilisasi        : SID 06/08/2014  : generate report vra utilisasi per BA
            - reportVraUtilisasiRegion      : SID 22/08/2014  : generate report vra utilisasi per region
            - getLastGenerate         : SID 12/08/2014  : get last generate date
            - querySummaryDevelopmentCostPerBa  : SID 25/08/2014  : query summary development cost per BA
            - querySummaryDevelopmentCostPerAfd : SID 28/08/2014  : query summary development cost per AFD
            - querySummaryEstateCostPerBa   : SID 25/08/2014  : query summary estate cost per BA
            - querySummaryEstateCostPerAfd    : SID 28/08/2014  : query summary estate cost per AFD
            - modReviewDevelopmentCostPerBa   : SID 25/08/2014  : generate module review development cost per BA
            - modReviewDevelopmentCostPerAfd  : SID 28/08/2014  : generate module review development cost per AFD
            - modReviewEstateCostPerBa      : SID 25/08/2014  : generate module review estate cost per BA
            - modReviewEstateCostPerAfd     : SID 28/08/2014  : generate module review estate cost per AFD
            - modReviewProduksiPerAfd     : SID 28/08/2014  : generate module review produksi per AFD
            - modReviewProduksiPerBa      : SID 28/08/2014  : generate module review produksi per BA
            - modReviewProduksiPerRegion    : SID 28/08/2014  : generate module review produksi per region
            - reportHkDevelopmentCost     : YUS 09/09/2014  : generate report HK development cost
            - reportHkEstateCost        : YUS 10/09/2014  : generate report HK estate cost
Disusun Oleh    :   IT Solution - PT Triputra Agro Persada
Developer     :   Nicholas Budihardja
Dibuat Tanggal    :   06/05/2015
Update Terakhir   : 06/05/2015
Revisi        : 
=========================================================================================================================
*/
class Application_Model_CalculateRkt
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
    
    
  
  //get last Calculate date
  public function getLastCalculate($params = array())
  {
    $where = "";
    $table1 = "";
    $table2 = "";
    
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
    
    //filter RKT
    if ($params['jenis_report'] == 'calc_infra') {
      $where .= "
        AND TIPE_TRANSAKSI = 'MANUAL_INFRA'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'rawat') {
      $where .= "
        AND TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'rawat_opsi') {
      $where .= "
        AND TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'tanam_otomatis') {
      $where .= "
        AND TIPE_TRANSAKSI = 'TANAM'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'panen') {
      //$where .= "
        //  AND TIPE_TRANSAKSI = 'TANAM'
      //";
      $table1 = 'TR_RKT_PANEN';
      $table2 = 'TR_RKT_PANEN_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'kastrasi_sanitasi') {
      $where .= "
        AND TIPE_TRANSAKSI = 'KASTRASI_SANITASI'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    if ($params['jenis_report'] == 'rawat_sisip') {
      $where .= "
        AND TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
      ";
      $table1 = 'TR_RKT';
      $table2 = 'TR_RKT_COST_ELEMENT';
    }
    
    //filter BA
    if ($params['key_find'] != '') {
      $where .= "
        AND BA_CODE = '".$params['key_find']."'
      ";
    }
  
  
    $query = "
      SELECT  INSERT_USER, to_char(MAX(INSERT_TIME), 'RRRR-MM-DD HH24:MI:SS') as INSERT_TIME FROM (
       SELECT     CASE 
        WHEN UPDATE_USER IS NULL THEN INSERT_USER
        ELSE UPDATE_USER
        END AS INSERT_USER,
        CASE 
        WHEN MAX(UPDATE_TIME) IS NULL THEN MAX(INSERT_TIME)
        ELSE MAX(UPDATE_TIME)
        END AS INSERT_TIME
                FROM $table1
        WHERE 1 = 1                
        $where
       GROUP BY UPDATE_USER, INSERT_USER
        UNION ALL
        SELECT     CASE 
        WHEN UPDATE_USER IS NULL THEN INSERT_USER
        ELSE UPDATE_USER
        END AS INSERT_USER,
        CASE 
        WHEN MAX(UPDATE_TIME) IS NULL THEN MAX(INSERT_TIME)
        ELSE MAX(UPDATE_TIME)
        END AS INSERT_TIME
                FROM $table2
        WHERE 1 = 1                
        $where
        GROUP BY UPDATE_USER, INSERT_USER
        )GROUP BY INSERT_USER
        ORDER BY INSERT_TIME DESC
    ";
    
    $result = $this->_db->fetchRow("{$query}");
    
    return $result;
  }
  
  //ambil data dari DB
  public function getDataRawat($params = array())
  {
    if ($params['src_activity_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
      ";
    }
    
    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
           ROWNUM,
           rkt.FLAG_TEMP,
           rkt.TRX_RKT_CODE,
           TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           rkt.BA_CODE,
           rkt.AFD_CODE,
           rkt.BLOCK_CODE,
           TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
           to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
           to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
           hs.TOPOGRAPHY,
           ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
           ) as TOPOGRAPHY_DESC,
           hs.LAND_TYPE,
           (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
           )as LAND_TYPE_DESC,
           hs.MATURITY_STAGE_SMS1,
           hs.MATURITY_STAGE_SMS2,
           hs.HA_PLANTED,
           hs.POKOK_TANAM,
           hs.SPH,
           rkt.ACTIVITY_CODE,
           rkt.ACTIVITY_CLASS,
           rkt.ROTASI_SMS1,
           rkt.ROTASI_SMS2,
           rkt.PLAN_SETAHUN,
           rkt.SUMBER_BIAYA,
           rkt.TOTAL_RP_SMS1,
           rkt.TOTAL_RP_SMS2,
           rkt.TOTAL_RP_SETAHUN,
           rkt.PLAN_JAN,
           rkt.PLAN_FEB,
           rkt.PLAN_MAR,
           rkt.PLAN_APR,
           rkt.PLAN_MAY,
           rkt.PLAN_JUN,
           rkt.PLAN_JUL,
           rkt.PLAN_AUG,
           rkt.PLAN_SEP,
           rkt.PLAN_OCT,
           rkt.PLAN_NOV,
           rkt.PLAN_DEC,
           rkt.COST_JAN,
           rkt.COST_FEB,
           rkt.COST_MAR,
           rkt.COST_APR,
           rkt.COST_MAY,
           rkt.COST_JUN,
           rkt.COST_JUL,
           rkt.COST_AUG,
           rkt.COST_SEP,
           rkt.COST_OCT,
           rkt.COST_NOV,
           rkt.COST_DEC,
           rkt.TIPE_TRANSAKSI,
           rkt.AWAL_ROTASI,
       -- CASE WHEN rkt.AWAL_ROTASI IS NULL THEN 1 ELSE rkt.AWAL_ROTASI END as AWAL_ROTASI,
           CASE WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM' ELSE rkt.TIPE_NORMA END as TIPE_NORMA,
           activity.DESCRIPTION as ACTIVITY_DESC
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA' 
      $where
    ";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
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
        AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."') 
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['src_block'] != '') {
      $query .= "AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }

    $query .= "
      ORDER BY rkt.ACTIVITY_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
    ";
    
    return $query; 
  }
  
  //ambil data dari DB
  public function getDataRawatOpsi($params = array())
  {
    if ($params['src_activity_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
      ";
    }
    
    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
           ROWNUM,
           rkt.FLAG_TEMP,
           rkt.TRX_RKT_CODE,
           TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           rkt.BA_CODE,
           rkt.AFD_CODE,
           rkt.BLOCK_CODE,
           TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
           to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
           to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
           hs.TOPOGRAPHY,
           ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
           ) as TOPOGRAPHY_DESC,
           hs.LAND_TYPE,
           (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
           )as LAND_TYPE_DESC,
           hs.MATURITY_STAGE_SMS1,
           hs.MATURITY_STAGE_SMS2,
           hs.HA_PLANTED,
           hs.POKOK_TANAM,
           hs.SPH,
           rkt.ACTIVITY_CODE,
           rkt.ACTIVITY_CLASS,
           rkt.ATRIBUT,
           (
          SELECT DESCRIPTION
          FROM TM_ACTIVITY
          WHERE ACTIVITY_CODE = rkt.ATRIBUT
           ) as ATRIBUT_DESC,
           rkt.ROTASI_SMS1,
           rkt.ROTASI_SMS2,
           rkt.PLAN_SETAHUN,
           rkt.SUMBER_BIAYA,
           rkt.TOTAL_RP_SMS1,
           rkt.TOTAL_RP_SMS2,
           rkt.TOTAL_RP_SETAHUN,
           rkt.PLAN_JAN,
           rkt.PLAN_FEB,
           rkt.PLAN_MAR,
           rkt.PLAN_APR,
           rkt.PLAN_MAY,
           rkt.PLAN_JUN,
           rkt.PLAN_JUL,
           rkt.PLAN_AUG,
           rkt.PLAN_SEP,
           rkt.PLAN_OCT,
           rkt.PLAN_NOV,
           rkt.PLAN_DEC,
           rkt.COST_JAN,
           rkt.COST_FEB,
           rkt.COST_MAR,
           rkt.COST_APR,
           rkt.COST_MAY,
           rkt.COST_JUN,
           rkt.COST_JUL,
           rkt.COST_AUG,
           rkt.COST_SEP,
           rkt.COST_OCT,
           rkt.COST_NOV,
           rkt.COST_DEC,
           rkt.TIPE_TRANSAKSI,
           CASE 
          WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
          ELSE rkt.AWAL_ROTASI
           END as AWAL_ROTASI,
           CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
           END as TIPE_NORMA,
           activity.DESCRIPTION ACTIVITY_DESC 
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
      $where
    ";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
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
        AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."')
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['src_block'] != '') {
      $query .= "
        AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%') 
      ";
    }
    
    $query .= "
      ORDER BY rkt.ACTIVITY_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
    ";
    return $query;
  }
  
  //ambil data dari DB
  public function getDataTanamAuto($params = array())
  {
    if ($params['src_coa_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_coa_code']."%')
      ";
    }
    
    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
           ROWNUM,
           rkt.FLAG_SITE,
           rkt.FLAG_TEMP,
           rkt.TRX_RKT_CODE,
           TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           rkt.BA_CODE,
           rkt.AFD_CODE,
           rkt.BLOCK_CODE,
           rkt.TOTAL_RP_QTY,
           TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
           to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
           to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
           hs.TOPOGRAPHY,
           ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
           ) as TOPOGRAPHY_DESC,
           hs.LAND_TYPE,
           (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
           )as LAND_TYPE_DESC,
           hs.MATURITY_STAGE_SMS1,
           hs.MATURITY_STAGE_SMS2,
           hs.HA_PLANTED,
           hs.POKOK_TANAM,
           hs.SPH,
           rkt.ACTIVITY_CODE,
           rkt.ACTIVITY_CLASS,
           rkt.ROTASI_SMS1,
           rkt.ROTASI_SMS2,
           rkt.PLAN_SETAHUN,
           rkt.SUMBER_BIAYA,
           rkt.TOTAL_RP_SMS1,
           rkt.TOTAL_RP_SMS2,
           rkt.TOTAL_RP_SETAHUN,
           rkt.PLAN_JAN,
           rkt.PLAN_FEB,
           rkt.PLAN_MAR,
           rkt.PLAN_APR,
           rkt.PLAN_MAY,
           rkt.PLAN_JUN,
           rkt.PLAN_JUL,
           rkt.PLAN_AUG,
           rkt.PLAN_SEP,
           rkt.PLAN_OCT,
           rkt.PLAN_NOV,
           rkt.PLAN_DEC,
           rkt.COST_JAN,
           rkt.COST_FEB,
           rkt.COST_MAR,
           rkt.COST_APR,
           rkt.COST_MAY,
           rkt.COST_JUN,
           rkt.COST_JUL,
           rkt.COST_AUG,
           rkt.COST_SEP,
           rkt.COST_OCT,
           rkt.COST_NOV,
           rkt.COST_DEC,
           rkt.TIPE_TRANSAKSI,
           CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
           END as TIPE_NORMA,
           activity.DESCRIPTION ACTIVITY_DESC 
      FROM 
        TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'TANAM' 
      AND hs.STATUS = 'PROYEKSI'
      $where
    ";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan BA
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan tipe tanah
    if ($params['LAND_TYPE'] != '') {
      $query .= "
        AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['TOPOGRAPHY'] != '') {
      $query .= "
        AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
    if ($params['ACTIVITY_CLASS'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
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
        AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."')
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['src_block'] != '') {
      $query .= "
        AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%') 
      ";
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }
    
    //jika diupdate dari RKT VRA, filter berdasarkan kode activity
    if ($params['ACT_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
      ";
    }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL') && ($params['src_matstage_code'] <> 'undefined')) {
      $query .= "
        AND (
          UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
          OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
        )
      ";
    }
    
    $query .= "
      ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
    ";//die("query: ".$query);
    return $query;
  }
  
  //ambil data dari DB
  public function getDataPanen($params = array())
  {
    $whrcrt="";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE'){
        $whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
      }elseif ($this->_referenceRole == 'BA_CODE'){
        $whrcrt .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HS.BA_CODE)||'%'";
      }
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND RKT_GABUNGAN.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }
    
    if($params['budgetperiod'] != ''){
      $whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['budgetperiod']."'";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $whrcrt .= "AND HS.PERIOD_BUDGET = '".$params['PERIOD_BUDGET']."'";
    }else{
      $whrcrt .= "AND HS.PERIOD_BUDGET = TO_CHAR(TO_DATE('".$this->_period."', 'DD-MM-RRRR'), 'RRRR')";
    }
    
    if ($params['src_region_code'] != '') {
      $whrcrt .= "AND UPPER(REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')";
    }
    
    if ($params['key_find'] != '') {
      $whrcrt .= "AND UPPER(HS.BA_CODE) IN ('".$params['key_find']."')";
    }
    
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $whrcrt .= "
        AND UPPER(RKT_GABUNGAN.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
      ";
    }
    
    if ($params['src_afd'] != '') {
      $whrcrt .= "AND UPPER(HS.AFD_CODE) LIKE UPPER('".$params['src_afd']."')";
    }
    
    if ($params['src_block'] != '') {
      $whrcrt .= "AND UPPER(HS.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
    }
    
    $query="SELECT 
            ROWNUM,hs.PERIOD_BUDGET PERIOD_BUDGETHS, hs.BA_CODE BA_CODEHS, hs.AFD_CODE AFD_CODEHS, hs.BLOCK_CODE BLOCK_CODEHS,
            RKT_GABUNGAN.*,
            activity.DESCRIPTION ACTIVITY_DESC,ORG.REGION_CODE, oer_ba.OER as OER_BA, oer_ba.OER as PRE_OER
        FROM 
        (
          SELECT DISTINCT TO_CHAR(PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, DELETE_USER 
          FROM TR_PRODUKSI_PERIODE_BUDGET WHERE DELETE_USER IS NULL AND TON_BUDGET>0
        ) HS 
        LEFT JOIN 
        (
          SELECT ROWIDTOCHAR (PANEN1.ROWID) row_id, '' ROW_ID_TEMP, FLAG_TEMP, PANEN1.TRX_RKT_CODE, TO_CHAR(PANEN1.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
          PANEN1.BA_CODE, PANEN1.AFD_CODE, PANEN1.BLOCK_CODE, PANEN1.ACTIVITY_CODE, PANEN1.TON, PANEN1.JANJANG, 
          PANEN1.BJR_AFD, PANEN1.JARAK_PKS, PANEN1.SUMBER_BIAYA_UNIT SUMBER_BIAYA, PANEN1.PERSEN_LANGSIR, 
          PANEN1.BIAYA_PEMANEN_HK, PANEN1.BIAYA_PEMANEN_RP_BASIS, PANEN1.BIAYA_PEMANEN_RP_PREMI_JANJANG, PANEN1.BIAYA_PEMANEN_RP_PREMI_BRD, PANEN1.BIAYA_PEMANEN_RP_TOTAL, 
          PANEN1.BIAYA_PEMANEN_RP_KG, PANEN1.BIAYA_SPV_RP_BASIS, PANEN1.BIAYA_SPV_RP_PREMI, PANEN1.BIAYA_SPV_RP_TOTAL, PANEN1.BIAYA_SPV_RP_KG, 
          PANEN1.BIAYA_ALAT_PANEN_RP_KG, PANEN1.BIAYA_ALAT_PANEN_RP_TOTAL, PANEN1.TUKANG_MUAT_BASIS, PANEN1.TUKANG_MUAT_PREMI, PANEN1.TUKANG_MUAT_TOTAL, 
          PANEN1.TUKANG_MUAT_RP_KG, PANEN1.SUPIR_PREMI, PANEN1.SUPIR_RP_KG, PANEN1.ANGKUT_TBS_RP_KG_KM, PANEN1.ANGKUT_TBS_RP_ANGKUT, PANEN1.ANGKUT_TBS_RP_KG, 
          PANEN1.KRANI_BUAH_BASIS, PANEN1.KRANI_BUAH_PREMI, PANEN1.KRANI_BUAH_TOTAL, PANEN1.KRANI_BUAH_RP_KG, PANEN1.LANGSIR_TON, PANEN1.LANGSIR_RP, 
          PANEN1.LANGSIR_RP_KG, PANEN1.COST_JAN, PANEN1.COST_FEB, PANEN1.COST_MAR, PANEN1.COST_APR, PANEN1.COST_MAY, PANEN1.COST_JUN, PANEN1.COST_JUL, 
          PANEN1.COST_AUG, PANEN1.COST_SEP, PANEN1.COST_OCT, PANEN1.COST_NOV, PANEN1.COST_DEC, PANEN1.COST_SETAHUN 
          FROM TR_RKT_PANEN PANEN1 WHERE DELETE_USER IS NULL 
        ) RKT_GABUNGAN 
          ON HS.PERIOD_BUDGET = RKT_GABUNGAN.PERIOD_BUDGET  
            AND HS.BA_CODE = RKT_GABUNGAN.BA_CODE 
            AND HS.AFD_CODE = RKT_GABUNGAN.AFD_CODE 
            AND HS.BLOCK_CODE = RKT_GABUNGAN.BLOCK_CODE  
        LEFT JOIN TM_ACTIVITY activity 
          ON RKT_GABUNGAN.ACTIVITY_CODE = activity.ACTIVITY_CODE
        LEFT JOIN TM_OER_BA oer_ba
          ON TO_CHAR(oer_ba.PERIOD_BUDGET,'RRRR') = hs.PERIOD_BUDGET
            AND oer_ba.BA_CODE = hs.BA_CODE
        LEFT JOIN TM_ORGANIZATION ORG 
          ON HS.BA_CODE = ORG.BA_CODE  
        WHERE 1=1 $whrcrt ";
    $query .= "
        ORDER BY HS.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE"; //print_r($params);echo "<br><br><br>";die(':DIE:');
      
    return $query;
  }
  
  //ambil data dari DB
  public function getDataKasSan($params = array())
  {
    if ($params['src_activity_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
      ";
    }
    
    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
           ROWNUM,
           rkt.FLAG_TEMP,
           rkt.TRX_RKT_CODE,
           TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           rkt.BA_CODE,
           rkt.AFD_CODE,
           CONCAT(CONCAT(rkt.BLOCK_CODE, ' - '), hs.BLOCK_DESC) BLOCK_CODE,
           hs.TAHUN_TANAM AS TAHUN_TANAM_MULAI,
           TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
           to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
           to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
           hs.TOPOGRAPHY,
           ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
           ) as TOPOGRAPHY_DESC,
           hs.LAND_TYPE,
           (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
           )as LAND_TYPE_DESC,
           hs.MATURITY_STAGE_SMS1,
           hs.MATURITY_STAGE_SMS2,
           hs.HA_PLANTED,
           hs.POKOK_TANAM,
           hs.SPH,
           hs.LAND_SUITABILITY,
           rkt.ACTIVITY_CODE,
           rkt.ACTIVITY_CLASS,
           rkt.ROTASI_SMS1,
           rkt.ROTASI_SMS2,
           rkt.PLAN_SETAHUN,
           rkt.SUMBER_BIAYA,
           rkt.TOTAL_RP_SMS1,
           rkt.TOTAL_RP_SMS2,
           rkt.TOTAL_RP_SETAHUN,
           rkt.PLAN_JAN,
           rkt.PLAN_FEB,
           rkt.PLAN_MAR,
           rkt.PLAN_APR,
           rkt.PLAN_MAY,
           rkt.PLAN_JUN,
           rkt.PLAN_JUL,
           rkt.PLAN_AUG,
           rkt.PLAN_SEP,
           rkt.PLAN_OCT,
           rkt.PLAN_NOV,
           rkt.PLAN_DEC,
           rkt.COST_JAN,
           rkt.COST_FEB,
           rkt.COST_MAR,
           rkt.COST_APR,
           rkt.COST_MAY,
           rkt.COST_JUN,
           rkt.COST_JUL,
           rkt.COST_AUG,
           rkt.COST_SEP,
           rkt.COST_OCT,
           rkt.COST_NOV,
           rkt.COST_DEC,
           rkt.TIPE_TRANSAKSI,
           rkt.ATRIBUT,
           CASE 
          WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
          ELSE rkt.AWAL_ROTASI
           END as AWAL_ROTASI,
           CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
           END as TIPE_NORMA,
           activity.DESCRIPTION ACTIVITY_DESC 
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'KASTRASI_SANITASI' 
      AND (
        hs.MATURITY_STAGE_SMS1 LIKE 'TBM%'
        OR hs.MATURITY_STAGE_SMS2 LIKE 'TBM%'
      )
      $where
    ";
    /*
      
    */
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
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
        AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan tipe tanah
    if ($params['LAND_TYPE'] != '') {
      $query .= "
        AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['TOPOGRAPHY'] != '') {
      $query .= "
        AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
    if ($params['ACTIVITY_CLASS'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan maturity status
    if ($params['MATURITY_STATUS'] != '') {
      $query .= "
        AND (
          hs.MATURITY_STAGE_SMS1 IN ('".$params['MATURITY_STATUS']."') 
          OR hs.MATURITY_STAGE_SMS2 IN ('".$params['MATURITY_STATUS']."') 
        )
      ";
    }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
      $query .= "
        AND (
          UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
          OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
        )
      ";
    }
    
      //AND rkt.BLOCK_CODE = 'B58'
    $query .= "
      ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
    ";
    
    return $query; 
  }
  
  //ambil data dari DB
  public function getDataRawatSisip($params = array())
  {
    if ($params['src_activity_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
      ";
    }
    
    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
           ROWNUM,
           rkt.FLAG_TEMP,
           rkt.TRX_RKT_CODE,
           TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           rkt.BA_CODE,
           rkt.AFD_CODE,
           CONCAT (CONCAT (rkt.BLOCK_CODE, ' - '), hs.BLOCK_DESC) BLOCK_CODE,
           TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
           to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
           to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
           hs.TOPOGRAPHY,
           ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
           ) as TOPOGRAPHY_DESC,
           hs.LAND_TYPE,
           (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
           )as LAND_TYPE_DESC,
           hs.MATURITY_STAGE_SMS1,
           hs.MATURITY_STAGE_SMS2,
           hs.HA_PLANTED,
           hs.POKOK_TANAM,
           hs.SPH,
           rkt.ACTIVITY_CODE,
           rkt.ACTIVITY_CLASS,
           rkt.ROTASI_SMS1,
           rkt.ROTASI_SMS2,
           rkt.PLAN_SETAHUN,
           rkt.SUMBER_BIAYA,
           rkt.TOTAL_RP_SMS1,
           rkt.TOTAL_RP_SMS2,
           rkt.TOTAL_RP_SETAHUN,
           rkt.PLAN_JAN,
           rkt.PLAN_FEB,
           rkt.PLAN_MAR,
           rkt.PLAN_APR,
           rkt.PLAN_MAY,
           rkt.PLAN_JUN,
           rkt.PLAN_JUL,
           rkt.PLAN_AUG,
           rkt.PLAN_SEP,
           rkt.PLAN_OCT,
           rkt.PLAN_NOV,
           rkt.PLAN_DEC,
           rkt.COST_JAN,
           rkt.COST_FEB,
           rkt.COST_MAR,
           rkt.COST_APR,
           rkt.COST_MAY,
           rkt.COST_JUN,
           rkt.COST_JUL,
           rkt.COST_AUG,
           rkt.COST_SEP,
           rkt.COST_OCT,
           rkt.COST_NOV,
           rkt.COST_DEC,
           rkt.TIPE_TRANSAKSI,
           CASE 
          WHEN rkt.AWAL_ROTASI IS NULL THEN 1 
          ELSE rkt.AWAL_ROTASI
           END as AWAL_ROTASI,
           CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
           END as TIPE_NORMA,
           activity.DESCRIPTION as ACTIVITY_DESC,
           (SPH.SPH_STANDAR - hs.SPH)SPH_MAX
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE
      LEFT JOIN TN_SPH SPH
         ON SPH.CORE = CASE WHEN SUBSTR(rkt.BA_CODE,3,1) = 2 THEN 'INTI' ELSE 'PLASMA' END
         AND SPH.LAND_TYPE = hs.LAND_TYPE
         AND SPH.TOPOGRAPHY = hs.TOPOGRAPHY   
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'MANUAL_NON_INFRA' 
      $where
    ";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(rkt.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
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
        AND UPPER(rkt.BA_CODE) IN ('".$params['key_find']."') 
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(rkt.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['src_block'] != '') {
      $query .= "AND UPPER(rkt.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }
    
    //jika diupdate dari RKT VRA, filter berdasarkan kode activity
    if ($params['ACT_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan BA
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.BA_CODE) IN ('".$params['BA_CODE']."')
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan tipe tanah
    if ($params['LAND_TYPE'] != '') {
      $query .= "
        AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['CORE'] != '') {
      $query .= "
        AND SUBSTR(rkt.BA_CODE,3,1) = '".$params['CORE']."' 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['TOPOGRAPHY'] != '') {
      $query .= "
        AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
    if ($params['ACTIVITY_CLASS'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan maturity status
    if ($params['MATURITY_STATUS'] != '') {
      $query .= "
        AND (
          hs.MATURITY_STAGE_SMS1 IN ('".$params['MATURITY_STATUS']."') 
          OR hs.MATURITY_STAGE_SMS2 IN ('".$params['MATURITY_STATUS']."') 
        )
      ";
    }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
      $query .= "
        AND (
          UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
          OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
        )
      ";
    }
    
    $query .= "
      ORDER BY rkt.BA_CODE, rkt.AFD_CODE, rkt.BLOCK_CODE
    ";
    
    return $query; 
  }

  public function getDataManualInfra($params = array()) {
    if ($params['src_activity_code'] != '') {
      $where = "
        AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')
      ";
    }

    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
        rkt.FLAG_TEMP,
        rkt.TRX_RKT_CODE,
        TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
        rkt.BA_CODE,
        rkt.AFD_CODE,
        rkt.BLOCK_CODE,
        hs.BLOCK_DESC,
        TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
        to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
        to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
        hs.TOPOGRAPHY,
        ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
        ) as TOPOGRAPHY_DESC,
        CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
        END as TIPE_NORMA,
        hs.LAND_TYPE,
        (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
        ) as LAND_TYPE_DESC,
        hs.MATURITY_STAGE_SMS1,
        hs.MATURITY_STAGE_SMS2,
        hs.HA_PLANTED,
        hs.POKOK_TANAM,
        hs.SPH,
        rkt.ACTIVITY_CODE,
        rkt.ACTIVITY_CLASS,
        rkt.ROTASI_SMS1,
        rkt.ROTASI_SMS2,
        rkt.PLAN_SETAHUN,
        rkt.SUMBER_BIAYA,
        rkt.TOTAL_RP_SMS1,
        rkt.TOTAL_RP_SMS2,
        rkt.TOTAL_RP_SETAHUN,
        rkt.PLAN_JAN,
        rkt.PLAN_FEB,
        rkt.PLAN_MAR,
        rkt.PLAN_APR,
        rkt.PLAN_MAY,
        rkt.PLAN_JUN,
        rkt.PLAN_JUL,
        rkt.PLAN_AUG,
        rkt.PLAN_SEP,
        rkt.PLAN_OCT,
        rkt.PLAN_NOV,
        rkt.PLAN_DEC,
        rkt.COST_JAN,
        rkt.COST_FEB,
        rkt.COST_MAR,
        rkt.COST_APR,
        rkt.COST_MAY,
        rkt.COST_JUN,
        rkt.COST_JUL,
        rkt.COST_AUG,
        rkt.COST_SEP,
        rkt.COST_OCT,
        rkt.COST_NOV,
        rkt.COST_DEC,
        rkt.TIPE_TRANSAKSI,
        activity.DESCRIPTION ACTIVITY_DESC 
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
        AND rkt.TIPE_TRANSAKSI = 'MANUAL_INFRA' 
        AND UPPER(rkt.ACTIVITY_CODE) IN (
          SELECT AM.ACTIVITY_CODE
          FROM TM_ACTIVITY_MAPPING AM 
          LEFT JOIN TM_ACTIVITY TA
            ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
          WHERE TA.DELETE_USER IS NULL
            AND UPPER(AM.UI_RKT_CODE) IN ('RKT003')
          GROUP BY AM.ACTIVITY_CODE, TA.DESCRIPTION, TA.UOM
        )
        $where
    ";

    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%' ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(hs.BA_CODE)||'%' ";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
      ";
    }else{
      $query .= "
        AND to_char(hs.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
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
        AND UPPER(hs.BA_CODE) IN ('".$params['key_find']."') 
      ";
    }
    
    if ($params['src_afd'] != '') {
      $query .= "
        AND UPPER(hs.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%') 
      ";
    }
    
    if ($params['src_block'] != '') {
      $query .= "AND UPPER(hs.BLOCK_CODE) LIKE UPPER('".$params['src_block']."')";
    }
    
    if ($params['activity_code'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."') 
      ";
    }
    
    //jika diupdate dari RKT VRA, filter berdasarkan kode activity
    if ($params['ACT_CODE'] != '') {
      $query .= "
        AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan BA
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(hs.BA_CODE) IN ('".$params['BA_CODE']."')
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan tipe tanah
    if ($params['LAND_TYPE'] != '') {
      $query .= "
        AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['CORE'] != '') {
      $query .= "
        AND SUBSTR(hs.BA_CODE,3,1) = '".$params['CORE']."' 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan topografi
    if ($params['TOPOGRAPHY'] != '') {
      $query .= "
        AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan kelas aktivitas
    if ($params['ACTIVITY_CLASS'] != '') {
      $query .= "
        AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
      ";
    }
    
    //jika diupdate dari norma biaya, filter berdasarkan maturity status
    if ($params['MATURITY_STATUS'] != '') {
      $query .= "
        AND (
          hs.MATURITY_STAGE_SMS1 IN ('".$params['MATURITY_STATUS']."') 
          OR hs.MATURITY_STAGE_SMS2 IN ('".$params['MATURITY_STATUS']."') 
        )
      ";
    }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
      $query .= "
        AND (
          UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%') 
          OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%') 
        )
      ";
    }
    
    $query .= "
      ORDER BY hs.BA_CODE, hs.AFD_CODE, hs.BLOCK_CODE
    ";

    return $query; 
  }
  
  
  //menampilkan data RKT Perkerasan Jalan
  public function getDataPerkerasanJalan($params = array()) {
    if ($params['src_coa_code'] != '') {
      $where = "
                AND RKTPK.ACTIVITY_CODE IN ('".$params['src_coa_code']."')
            ";
        }
    
    $query = "
     SELECT RKTPK.ROW_ID,RKTPK.TRX_RKT_CODE,
         RKTPK.ROW_ID_TEMP,
         TO_CHAR (HA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
         HA.BA_CODE,
         HA.AFD_CODE,
         HA.BLOCK_CODE, 
         HA.BLOCK_DESC,
         HA.LAND_TYPE,
         HA.TOPOGRAPHY,
         TO_CHAR (HA.TAHUN_TANAM, 'MM.RRRR') AS TAHUN_TANAM,
         to_char(HA.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
         to_char(HA.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
         HA.MATURITY_STAGE_SMS1 AS SEMESTER1,
         HA.MATURITY_STAGE_SMS2 AS SEMESTER2,
         RKTPK.ACTIVITY_CODE,
         HA.HA_PLANTED,
         HA.POKOK_TANAM,
         HA.SPH,
         RKTPK.SUMBER_BIAYA,
         RKTPK.FLAG_TEMP,
           CASE 
          WHEN RKTPK.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE RKTPK.TIPE_NORMA
           END as TIPE_NORMA,
                   
         RKTPK.JENIS_PEKERJAAN,
         RKTPK.JARAK,
         TPV.PARAMETER_VALUE AS RANGE_JARAK,
         RKTPK.AKTUAL_JALAN,
         RKTPK.AKTUAL_PERKERASAN_JALAN,
         RKTPK.PLAN_JAN,
         RKTPK.PLAN_FEB,
         RKTPK.PLAN_MAR,
         RKTPK.PLAN_APR,
         RKTPK.PLAN_MAY,
         RKTPK.PLAN_JUN,
         RKTPK.PLAN_JUL,
         RKTPK.PLAN_AUG,
         RKTPK.PLAN_SEP,
         RKTPK.PLAN_OCT,
         RKTPK.PLAN_NOV,
         RKTPK.PLAN_DEC,
           RKTPK.PLAN_JAN
         + RKTPK.PLAN_FEB
         + RKTPK.PLAN_MAR
         + RKTPK.PLAN_APR
         + RKTPK.PLAN_MAY
         + RKTPK.PLAN_JUN
         + RKTPK.PLAN_JUL
         + RKTPK.PLAN_AUG
         + RKTPK.PLAN_SEP
         + RKTPK.PLAN_OCT
         + RKTPK.PLAN_NOV
         + RKTPK.PLAN_DEC
          AS PLAN_SETAHUN,
         RKTPK.PRICE_QTY,
         RKTPK.COST_JAN,
         RKTPK.COST_FEB,
         RKTPK.COST_MAR,
         RKTPK.COST_APR,
         RKTPK.COST_MAY,
         RKTPK.COST_JUN,
         RKTPK.COST_JUL,
         RKTPK.COST_AUG,
         RKTPK.COST_SEP,
         RKTPK.COST_OCT,
         RKTPK.COST_NOV,
         RKTPK.COST_DEC,
           RKTPK.COST_JAN
         + RKTPK.COST_FEB
         + RKTPK.COST_MAR
         + RKTPK.COST_APR
         + RKTPK.COST_MAY
         + RKTPK.COST_JUN
         + RKTPK.COST_JUL
         + RKTPK.COST_AUG
         + RKTPK.COST_SEP
         + RKTPK.COST_OCT
         + RKTPK.COST_NOV
         + RKTPK.COST_DEC
          AS COST_SETAHUN
      FROM (SELECT ROWIDTOCHAR (PK.ROWID) AS ROW_ID,
                   '' ROW_ID_TEMP,
                   PK.PERIOD_BUDGET,
                   PK.BA_CODE,
                   PK.AFD_CODE,
                   PK.BLOCK_CODE,
                   PK.ACTIVITY_CODE,
                   PK.TRX_RKT_CODE,
                   PK.MATURITY_STAGE_SMS1,
                   PK.MATURITY_STAGE_SMS2,
                   PK.SUMBER_BIAYA,
                   PK.FLAG_TEMP,
                   PK.TIPE_NORMA,
                   PK.JENIS_PEKERJAAN,
                   PK.JARAK,
                   PK.AKTUAL_JALAN,
                   PK.AKTUAL_PERKERASAN_JALAN,
                   PK.PLAN_JAN,
                   PK.PLAN_FEB,
                   PK.PLAN_MAR,
                   PK.PLAN_APR,
                   PK.PLAN_MAY,
                   PK.PLAN_JUN,
                   PK.PLAN_JUL,
                   PK.PLAN_AUG,
                   PK.PLAN_SEP,
                   PK.PLAN_OCT,
                   PK.PLAN_NOV,
                   PK.PLAN_DEC,
                   PK.PLAN_SETAHUN,
                   PK.PRICE_QTY,
                   PK.COST_JAN,
                   PK.COST_FEB,
                   PK.COST_MAR,
                   PK.COST_APR,
                   PK.COST_MAY,
                   PK.COST_JUN,
                   PK.COST_JUL,
                   PK.COST_AUG,
                   PK.COST_SEP,
                   PK.COST_OCT,
                   PK.COST_NOV,
                   PK.COST_DEC,
                   PK.COST_SETAHUN
                FROM  TR_RKT_PK PK
                WHERE PK.DELETE_USER IS NULL
              ) RKTPK
         LEFT JOIN TM_HECTARE_STATEMENT HA
          ON     HA.PERIOD_BUDGET = RKTPK.PERIOD_BUDGET
             AND HA.BA_CODE = RKTPK.BA_CODE
             AND HA.AFD_CODE = RKTPK.AFD_CODE
             AND HA.BLOCK_CODE = RKTPK.BLOCK_CODE
          $where
         LEFT JOIN TM_ORGANIZATION ORG
          ON HA.BA_CODE = ORG.BA_CODE
          LEFT JOIN T_PARAMETER_VALUE TPV
          ON TPV.PARAMETER_VALUE_CODE = RKTPK.JARAK
       WHERE     1 = 1          
          AND UPPER(RKTPK.ACTIVITY_CODE) IN (
            SELECT AM.ACTIVITY_CODE
            FROM TM_ACTIVITY_MAPPING AM 
            LEFT JOIN TM_ACTIVITY TA
              ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
            WHERE TA.DELETE_USER IS NULL
              AND UPPER(AM.UI_RKT_CODE) IN ('RKT004')
            GROUP BY AM.ACTIVITY_CODE, TA.DESCRIPTION, TA.UOM
          ) 
        ";

    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(HA.BA_CODE)||'%'";
    }
    
    if($params['budgetperiod'] != ''){
      $query .= "
                AND to_char(HA.PERIOD_BUDGET, 'RRRR')  = '".$params['budgetperiod']."'
            ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
                AND to_char(HA.PERIOD_BUDGET, 'RRRR')  = '".$params['PERIOD_BUDGET']."'
            ";
    }else{
      $query .= "
                AND to_char(HA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
    }
    
    //filter region
    if ($params['src_region_code'] != '') {
      $query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
    
    /*if ($params['src_afd'] != '') {
      $query .= "
                AND UPPER(HA.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }else{
      $query .= "
                AND UPPER(HA.AFD_CODE) LIKE UPPER('%%')
            ";
    }*/

    if ($params['key_find'] != '') {
      $query .= "
                AND UPPER(HA.BA_CODE) IN ('".$params['key_find']."')
            ";
        }

    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
                AND UPPER(HA.BA_CODE) IN ('".$params['BA_CODE']."')
            ";
        }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
      $query .= "
                AND (
          UPPER(HA.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
          OR UPPER(HA.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
        )
            ";
        }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(HA.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(HA.AFD_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(HA.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(HA.LAND_TYPE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(HA.TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
          OR UPPER(HA.TAHUN_TANAM) LIKE UPPER('%".$params['search']."%')
        )
            ";
        }

    $query .= "
      ORDER BY HA.BA_CODE, HA.AFD_CODE, HA.BLOCK_CODE, HA.LAND_TYPE, HA.TOPOGRAPHY, HA.TAHUN_TANAM
    ";
    //print_r($query); echo"<br>"; die;
    return $query;
  }

  //menampilkan list RKT Manual - Non Infra
  public function getListRawat($params = array())
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

    /*$sql = "SELECT COUNT(*) FROM ({$this->getDataRawat($params)})";
    $result['count'] = $this->_db->fetchOne($sql);
    */
    $rows = $this->_db->fetchAll("{$begin} {$this->getDataRawat($params)} {$end}");
    
    foreach($params as $idx => $paramsSumberBiaya)
    {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
    }
    
    if (!empty($rows)) {
      $sumberBiaya = $this->_formula->cekSumberBiayaExternal($paramsSumberBiaya);
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $sumberBiaya;
        $rotasi = $this->_formula->get_RktManual_Rotasi($row);
        $row['ROTASI_SMS1'] = $rotasi['SMS1'];
        $row['ROTASI_SMS2'] = $rotasi['SMS2'];
        $result['rows'][] = $row;
      }
    }
    return $result;
  }
  
  //menampilkan list RKT Manual - Non Infra + Opsi
  public function getListRawatOpsi($params = array())
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataRawatOpsi($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataRawatOpsi($params)} {$end}");
    
    foreach($params as $idx => $paramsSumberBiaya)
    {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_activity_code'];
    }
    
    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($paramsSumberBiaya);
        $row['TIPE_RKT_MANUAL'] = 'NON_INFRA_OPSI';
        $rotasi = $this->_formula->get_RktManual_Rotasi($row);
        $row['ROTASI_SMS1'] = $rotasi['SMS1'];
        $row['ROTASI_SMS2'] = $rotasi['SMS2'];
        $result['rows'][] = $row;
      }
    }

    return $result;
  }
  
  //menampilkan list RKT Tanam
  public function getListTanamAuto($params = array())
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataTanamAuto($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataTanamAuto($params)} {$end}");
    
    foreach ($params as $idx => $acti)
    {
      $sumberbiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $sumberbiaya['BA_CODE'] = $params['key_find'];
      $sumberbiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
    }
    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($sumberbiaya);
        $result['rows'][] = $row;
      }
    }

    return $result;
  }
  
  //menampilkan list RKT Panen
  public function getListPanen($params = array())
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataPanen($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$this->getDataPanen($params)}");
    
    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $result['rows'][] = $row;
      }
    }

    return $result;
  }
  
  //menampilkan list RKT Kastrasi Sanitasi
  public function getListKasSan($params = array())
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataKasSan($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataKasSan($params)} {$end}");
    
    foreach($params as $idx => $paramsSumberBiaya)
    {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
    }
    if (!empty($rows)) {
      $sumberBiaya = $this->_formula->cekSumberBiayaExternal($paramsSumberBiaya);
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $sumberBiaya;
        $rotasi = $this->_formula->get_RktManual_Rotasi($row);
        $row['ROTASI_SMS1'] = $rotasi['SMS1'];
        $row['ROTASI_SMS2'] = $rotasi['SMS2'];
        $result['rows'][] = $row;
        
        $kelas = $row['LAND_SUITABILITY'];
      }
      
    }
    
    return $result;
  }
  
  //menampilkan list RKT Rawat Sisip
  public function getListRawatSisip($params = array())
  {
    $result = array();
    
    $begin = "
      SELECT ROWNUM, NEW_TABLE.* FROM (SELECT MY_TABLE.*
      FROM (SELECT TEMP.*
      FROM (
    ";
    $min = (intval($params['page_num']) - 1) * intval($params['page_rows']);
    $max = $min + intval($params['page_rows']);
    $end = "
      ) TEMP
      ) MY_TABLE
        WHERE ROWNUM BETWEEN {$min} AND {$max}
      ) NEW_TABLE
    ";
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataRawatSisip($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataRawatSisip($params)} {$end}");
    
    foreach($params as $idx => $paramsSumberBiaya)
    {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
    }
    
    if (!empty($rows)) {
      $sumberBiaya = $this->_formula->cekSumberBiayaExternal($paramsSumberBiaya);
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $sumberBiaya;
        $rotasi = $this->_formula->get_RktManual_Rotasi($row);
        $row['ROTASI_SMS1'] = $rotasi['SMS1'];
        $row['ROTASI_SMS2'] = $rotasi['SMS2'];
        $result['rows'][] = $row;
      }
    }
    return $result;
  }

  //menampilkan list RKT Manual Infra
  public function getListManualInfra($params = array()) {
    $result = array();

    $begin = "
      SELECT ROWNUM, NEW_TABLE.* FROM (SELECT MY_TABLE.*
      FROM (SELECT TEMP.*
      FROM (
    ";
    $min = (intval($params['page_num']) - 1) * intval($params['page_rows']);
    $max = $min + intval($params['page_rows']);
    $end = "
      ) TEMP
      ) MY_TABLE
        WHERE ROWNUM BETWEEN {$min} AND {$max}
      ) NEW_TABLE
    ";
    
    $sql = "SELECT COUNT(*) FROM ({$this->getDataManualInfra($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataManualInfra($params)} {$end}");

    foreach($params as $idx => $paramsSumberBiaya) {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_activity_code'];
    }

    if (!empty($rows)) {
      $sumberBiaya = $this->_formula->cekSumberBiayaExternalManualInfra($paramsSumberBiaya);
      foreach ($rows as $idx => $row) {
        $row['SUMBER_BIAYA'] = $row['SUMBER_BIAYA'] ? $row['SUMBER_BIAYA'] : $sumberBiaya;
        $row['TIPE_RKT_MANUAL'] = 'INFRA';
        $rotasi = $this->_formula->get_RktManual_Rotasi($row);
        $row['ROTASI_SMS1'] = $rotasi['SMS1'];
        $row['ROTASI_SMS2'] = $rotasi['SMS2'];
        $result['rows'][] = $row;
      }
    }

    return $result;
  }
  
    //menampilkan list RKT Perkerasan Jalan
    public function getListPerkerasanJalan($params = array())
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
    
        $sql = "SELECT COUNT(*) FROM ({$this->getDataPerkerasanJalan($params)})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$this->getDataPerkerasanJalan($params)} {$end}");
    
    foreach($params as $idx => $paramsSumberBiaya)
    {
      $paramsSumberBiaya['PERIOD_BUDGET'] = $params['budgetperiod'];
      $paramsSumberBiaya['BA_CODE'] = $params['key_find'];
      $paramsSumberBiaya['ACTIVITY_CODE'] = $params['src_coa_code'];
    }
    if (!empty($rows)){
      foreach ($rows as $idx => $row){
        $ulangBaru = ($row['JENIS_PEKERJAAN']) ? $row['JENIS_PEKERJAAN'] : $this->_formula->cekJenisPekerjaan_RKT_PK($row);
        $row['JENIS_PEKERJAAN'] = $ulangBaru;
                $result['rows'][] = $row;
            }
        }
        return $result;
    }


    //cek apakah ada norma perkerasan jalan yang grader, excavator atau compactor RP/HM nya 0
    public function checkDataPerkerasanJalan($params = array())
      {
      $query = "
        SELECT RP_HM_EXCAV, RP_HM_GRADER, RP_HM_COMPACTOR 
        FROM TN_PERKERASAN_JALAN
        WHERE 1 = 1
          ";
      
      if($params['PERIOD_BUDGET'] != ''){
        $query .= "
                  AND to_char(PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."' 
              ";
      }else{
        $query .= "
                  AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
              ";
      }
      
      if ($params['BA_CODE'] != '') {
        $query .= "
                  AND UPPER(BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
              ";
          }
      
      if ($params['ACTIVITY_CODE'] != '') {
        $query .= "
                  AND ACTIVITY_CODE = '".$params['ACTIVITY_CODE']."' 
              ";
          }
      
      $result = $this->_db->fetchRow($query);
      if($result['RP_HM_EXCAV'] == 0 || $result['RP_HM_GRADER'] == 0 || $result['RP_HM_COMPACTOR'] == 0){
        $result['status'] = 1;
      }else{
        $result['status'] = 0;
      }
      return $result;
    }
  
  //simpan inputan rotasi
  public function saveRotationRawat($row = array())
  {
    //CEK TRX CODE di TR_RKT_COST_ELEMENT
    /*$sql_cek = "SELECT * FROM TR_RKT_COST_ELEMENT WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."'";
    $ada = $this->_db->fetchOne($sql_cek);
    $sql="";
    if(empty($ada)){
    $costElement = array("LABOUR","MATERIAL","TOOLS","TRANSPORT","CONTRACT");
      foreach($costElement as $ce){
        $sql.= "INSERT INTO TR_RKT_COST_ELEMENT (
            PERIOD_BUDGET,
            BA_CODE,
            AFD_CODE,
            BLOCK_CODE,
            TIPE_TRANSAKSI,
            ACTIVITY_CODE,
            COST_ELEMENT,
            TRX_RKT_CODE,
            INSERT_USER,
            INSERT_TIME)
            VALUES(
            TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
            '".addslashes($row['BA_CODE'])."',
            '".addslashes($row['AFD_CODE'])."',
            '".addslashes($row['BLOCK_CODE'])."',
            'MANUAL_NON_INFRA',
            '".addslashes($row['ACTIVITY_CODE'])."',
            '".$ce."',
            '".addslashes($row['TRX_RKT_CODE'])."',
            '{$this->_userName}', 
            SYSDATE);
        ";
      }
    }*/
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    
    $sql = "
      UPDATE TR_RKT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        PLAN_JAN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($sebaran_rotasi['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($sebaran_rotasi['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($sebaran_rotasi['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($sebaran_rotasi['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($sebaran_rotasi['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($sebaran_rotasi['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($sebaran_rotasi['PLAN_DEC'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        TOTAL_RP_SMS1 = NULL, 
        TOTAL_RP_SMS2 = NULL, 
        TOTAL_RP_QTY = NULL,
        PLAN_SMS1 = NULL, 
        PLAN_SMS2 = NULL, 
        PLAN_SETAHUN = NULL, 
        COST_JAN = NULL, 
        COST_FEB = NULL, 
        COST_MAR = NULL, 
        COST_APR = NULL, 
        COST_MAY = NULL, 
        COST_JUN = NULL, 
        COST_JUL = NULL, 
        COST_AUG = NULL, 
        COST_SEP = NULL, 
        COST_OCT = NULL, 
        COST_NOV = NULL, 
        COST_DEC = NULL, 
        COST_SMS1 = NULL, 
        COST_SMS2 = NULL, 
        TOTAL_RP_SETAHUN = NULL,
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return true;
  }
  
  //simpan inputan rotasi
  public function saveRotationRawatOpsi($row = array())
  {
    //hitung rotasi
    $row['TIPE_RKT_MANUAL'] = 'NON_INFRA_OPSI';
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    
    $sql = "
      UPDATE TR_RKT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($sebaran_rotasi['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($sebaran_rotasi['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($sebaran_rotasi['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($sebaran_rotasi['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($sebaran_rotasi['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($sebaran_rotasi['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($sebaran_rotasi['PLAN_DEC'])."',',',''),
        ATRIBUT = '".addslashes($row['ATRIBUT'])."', 
        BULAN_PENGERJAAN = NULL, 
        TOTAL_RP_SMS1 = NULL, 
        TOTAL_RP_SMS2 = NULL, 
        TOTAL_RP_QTY = NULL,
        PLAN_SMS1 = NULL, 
        PLAN_SMS2 = NULL, 
        PLAN_SETAHUN = NULL, 
        COST_JAN = NULL, 
        COST_FEB = NULL, 
        COST_MAR = NULL, 
        COST_APR = NULL, 
        COST_MAY = NULL, 
        COST_JUN = NULL, 
        COST_JUL = NULL, 
        COST_AUG = NULL, 
        COST_SEP = NULL, 
        COST_OCT = NULL, 
        COST_NOV = NULL, 
        COST_DEC = NULL, 
        COST_SMS1 = NULL, 
        COST_SMS2 = NULL, 
        TOTAL_RP_SETAHUN = NULL,
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //simpan inputan rotasi
  public function saveRotationTanam($row = array())
  {
    //get sumber biaya
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
    
    $sql = "
      UPDATE TR_RKT
        SET 
          AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
          SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
          TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
          TIPE_TRANSAKSI = 'TANAM',
          PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
          PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
          PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
          PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
          PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
          PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
          PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
          PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
          PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
          PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
          PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
          PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
          PLAN_SETAHUN = NULL, 
          TOTAL_RP_QTY = NULL, 
          COST_JAN = NULL, COST_FEB = NULL, 
          COST_MAR = NULL, COST_APR = NULL, 
          COST_MAY = NULL, COST_JUN = NULL, 
          COST_JUL = NULL, COST_AUG = NULL, 
          COST_SEP = NULL, COST_OCT = NULL, 
          COST_NOV = NULL, COST_DEC = NULL, 
          TOTAL_RP_SETAHUN = NULL, COST_SMS1 = NULL, 
          COST_SMS2 = NULL, 
          MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
          FLAG_TEMP = 'Y',
          UPDATE_USER = '{$this->_userName}',
          UPDATE_TIME = SYSDATE
        WHERE
          TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //simpan inputan rotasi
  public function saveRotationPanen($row = array())
  {   
    $sql = "
      UPDATE TR_RKT_PANEN
      SET TON = REPLACE('".addslashes($row['TON'])."',',',''), 
        JANJANG = REPLACE('".addslashes($row['JANJANG'])."',',',''), 
        BJR_AFD = REPLACE('".addslashes($row['BJR_AFD'])."',',',''), 
        JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
        SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
        PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
        BIAYA_PEMANEN_HK = REPLACE('".addslashes($row['BIAYA_PEMANEN_HK'])."',',',''), 
        BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
        BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
        BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_KG'])."',',',''), 
        BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($row['BIAYA_SPV_RP_BASIS'])."',',',''), 
        BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($row['BIAYA_SPV_RP_PREMI'])."',',',''), 
        BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_SPV_RP_TOTAL'])."',',',''), 
        BIAYA_SPV_RP_KG = REPLACE('".addslashes($row['BIAYA_SPV_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
        TUKANG_MUAT_BASIS = REPLACE('".addslashes($row['TUKANG_MUAT_BASIS'])."',',',''), 
        TUKANG_MUAT_PREMI = REPLACE('".addslashes($row['TUKANG_MUAT_PREMI'])."',',',''), 
        TUKANG_MUAT_TOTAL = REPLACE('".addslashes($row['TUKANG_MUAT_TOTAL'])."',',',''), 
        TUKANG_MUAT_RP_KG = REPLACE('".addslashes($row['TUKANG_MUAT_RP_KG'])."',',',''), 
        SUPIR_PREMI = REPLACE('".addslashes($row['SUPIR_PREMI'])."',',',''), 
        SUPIR_RP_KG = REPLACE('".addslashes($row['SUPIR_RP_KG'])."',',',''), 
        ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
        ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($row['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
        ANGKUT_TBS_RP_KG = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG'])."',',',''), 
        KRANI_BUAH_BASIS = REPLACE('".addslashes($row['KRANI_BUAH_BASIS'])."',',',''), 
        KRANI_BUAH_PREMI = REPLACE('".addslashes($row['KRANI_BUAH_PREMI'])."',',',''), 
        KRANI_BUAH_TOTAL = REPLACE('".addslashes($row['KRANI_BUAH_TOTAL'])."',',',''), 
        KRANI_BUAH_RP_KG = REPLACE('".addslashes($row['KRANI_BUAH_RP_KG'])."',',',''), 
        LANGSIR_TON = REPLACE('".addslashes($row['LANGSIR_TON'])."',',',''), 
        LANGSIR_RP = REPLACE('".addslashes($row['LANGSIR_RP'])."',',',''), 
        LANGSIR_RP_KG = REPLACE('".addslashes($row['LANGSIR_RP_KG'])."',',',''), 
        COST_JAN = REPLACE('".addslashes($row['COST_JAN'])."',',',''), 
        COST_FEB = REPLACE('".addslashes($row['COST_FEB'])."',',',''), 
        COST_MAR = REPLACE('".addslashes($row['COST_MAR'])."',',',''), 
        COST_APR = REPLACE('".addslashes($row['COST_APR'])."',',',''), 
        COST_MAY = REPLACE('".addslashes($row['COST_MAY'])."',',',''), 
        COST_JUN = REPLACE('".addslashes($row['COST_JUN'])."',',',''), 
        COST_JUL = REPLACE('".addslashes($row['COST_JUL'])."',',',''), 
        COST_AUG = REPLACE('".addslashes($row['COST_AUG'])."',',',''), 
        COST_SEP = REPLACE('".addslashes($row['COST_SEP'])."',',',''), 
        COST_OCT = REPLACE('".addslashes($row['COST_OCT'])."',',',''), 
        COST_NOV = REPLACE('".addslashes($row['COST_NOV'])."',',',''), 
        COST_DEC = REPLACE('".addslashes($row['COST_DEC'])."',',',''), 
        COST_SETAHUN = REPLACE('".addslashes($row['COST_SETAHUN'])."',',',''),
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //simpan inputan rotasi
  public function saveRotationKasSan($row = array())
  {   
    //hitung rotasi
    $sebaran_rotasi = $this->updateTempKastrasi($row);    
    $row['PLAN_JAN'] = $sebaran_rotasi[1];
    $row['PLAN_FEB'] = $sebaran_rotasi[2];
    $row['PLAN_MAR'] = $sebaran_rotasi[3];
    $row['PLAN_APR'] = $sebaran_rotasi[4];
    $row['PLAN_MAY'] = $sebaran_rotasi[5];
    $row['PLAN_JUN'] = $sebaran_rotasi[6];
    $row['PLAN_JUL'] = $sebaran_rotasi[7];
    $row['PLAN_AUG'] = $sebaran_rotasi[8];
    $row['PLAN_SEP'] = $sebaran_rotasi[9];
    $row['PLAN_OCT'] = $sebaran_rotasi[10];
    $row['PLAN_NOV'] = $sebaran_rotasi[11];
    $row['PLAN_DEC'] = $sebaran_rotasi[12];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    $sql = "
      UPDATE TR_RKT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''),
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        TOTAL_RP_QTY = NULL,
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //simpan inputan rotasi
  public function saveRotationRawatSisip($row = array())
  {   
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];    
    
    $sql = "
      UPDATE TR_RKT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        TOTAL_RP_SMS1 = NULL, 
        TOTAL_RP_SMS2 = NULL, 
        TOTAL_RP_QTY = NULL,
        PLAN_SMS1 = NULL, 
        PLAN_SMS2 = NULL, 
        PLAN_SETAHUN = NULL, 
        COST_JAN = NULL, 
        COST_FEB = NULL, 
        COST_MAR = NULL, 
        COST_APR = NULL, 
        COST_MAY = NULL, 
        COST_JUN = NULL, 
        COST_JUL = NULL, 
        COST_AUG = NULL, 
        COST_SEP = NULL, 
        COST_OCT = NULL, 
        COST_NOV = NULL, 
        COST_DEC = NULL, 
        COST_SMS1 = NULL, 
        COST_SMS2 = NULL, 
        TOTAL_RP_SETAHUN = NULL,
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return true;
  }

  //simpan inputan rotasi
  public function saveRotationInfra($row = array()) {
    $row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
    
    $sql = "
      UPDATE TR_RKT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_TRANSAKSI = 'MANUAL_INFRA',
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        ROTASI_SMS1 = NULL, 
        ROTASI_SMS2 = NULL, 
        TOTAL_RP_SMS1 = NULL, 
        TOTAL_RP_SMS2 = NULL, 
        TOTAL_RP_QTY = NULL,
        PLAN_SMS1 = NULL, 
        PLAN_SMS2 = NULL, 
        PLAN_SETAHUN = NULL, 
        COST_JAN = NULL, 
        COST_FEB = NULL, 
        COST_MAR = NULL, 
        COST_APR = NULL, 
        COST_MAY = NULL, 
        COST_JUN = NULL, 
        COST_JUL = NULL, 
        COST_AUG = NULL, 
        COST_SEP = NULL, 
        COST_OCT = NULL, 
        COST_NOV = NULL, 
        COST_DEC = NULL, 
        COST_SMS1 = NULL, 
        COST_SMS2 = NULL, 
        TOTAL_RP_SETAHUN = NULL,
        FLAG_TEMP = 'Y',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return true;
  }

  //simpan inputan rotasi
  public function saveRotationPerkerasanJalan($row = array())
  {
    $plan_setahun = $row['PLAN_JAN']+$row['PLAN_FEB']+$row['PLAN_MAR']+$row['PLAN_APR']+
          $row['PLAN_MAY']+$row['PLAN_JUN']+$row['PLAN_JUL']+$row['PLAN_AUG']+
          $row['PLAN_SEP']+$row['PLAN_OCT']+$row['PLAN_NOV']+$row['PLAN_DEC'];
    $sql = "
      UPDATE TR_RKT_PK
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AKTUAL_JALAN = REPLACE('".addslashes($row['AKTUAL_JALAN'])."',',',''),
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        AKTUAL_PERKERASAN_JALAN = REPLACE('".addslashes($row['AKTUAL_PERKERASAN_JALAN'])."',',',''),
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        JENIS_PEKERJAAN = '".addslashes($row['JENIS_PEKERJAAN'])."',
        JARAK = REPLACE('".addslashes($row['JARAK'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SETAHUN = '".addslashes($plan_setahun)."',
        COST_JAN = NULL, 
        COST_FEB = NULL, 
        COST_MAR = NULL, 
        COST_APR = NULL, 
        COST_MAY = NULL, 
        COST_JUN = NULL, 
        COST_JUL = NULL, 
        COST_AUG = NULL, 
        COST_SEP = NULL, 
        COST_OCT = NULL, 
        COST_NOV = NULL, 
        COST_DEC = NULL, 
        COST_SMS1 = NULL, 
        COST_SMS2 = NULL, 
        COST_SETAHUN = NULL,
        FLAG_TEMP = 'Y',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }

  public function getDataInfra($params = array()) {
    if ($params['src_activity_code'] != '') {
      $where = " AND UPPER(rkt.ACTIVITY_CODE) LIKE UPPER('%".$params['src_activity_code']."%')";
    }

    $query = "
      SELECT ROWIDTOCHAR (rkt.ROWID) ROW_ID,
        rkt.FLAG_TEMP,
        rkt.TRX_RKT_CODE,
        TO_CHAR (rkt.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
        rkt.BA_CODE,
        rkt.AFD_CODE,
        rkt.BLOCK_CODE,
        hs.BLOCK_DESC,
        TO_CHAR (hs.TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM,
        to_char(hs.TAHUN_TANAM,'MM') TAHUN_TANAM_M, 
        to_char(hs.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y, 
        hs.TOPOGRAPHY,
        ( 
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'TOPOGRAPHY' 
          AND PARAMETER_VALUE_CODE = hs.TOPOGRAPHY 
        ) as TOPOGRAPHY_DESC,
        CASE 
          WHEN rkt.TIPE_NORMA IS NULL THEN 'UMUM'
          ELSE rkt.TIPE_NORMA
        END as TIPE_NORMA,
        hs.LAND_TYPE,
        (
          SELECT PARAMETER_VALUE 
          FROM T_PARAMETER_VALUE 
          WHERE PARAMETER_CODE = 'LAND_TYPE' 
          AND PARAMETER_VALUE_CODE = hs.LAND_TYPE 
        ) as LAND_TYPE_DESC,
        hs.MATURITY_STAGE_SMS1,
        hs.MATURITY_STAGE_SMS2,
        hs.HA_PLANTED,
        hs.POKOK_TANAM,
        hs.SPH,
        rkt.ACTIVITY_CODE,
        rkt.ACTIVITY_CLASS,
        rkt.ROTASI_SMS1,
        rkt.ROTASI_SMS2,
        rkt.PLAN_SETAHUN,
        rkt.SUMBER_BIAYA,
        rkt.TOTAL_RP_SMS1,
        rkt.TOTAL_RP_SMS2,
        rkt.TOTAL_RP_SETAHUN,
        rkt.PLAN_JAN,
        rkt.PLAN_FEB,
        rkt.PLAN_MAR,
        rkt.PLAN_APR,
        rkt.PLAN_MAY,
        rkt.PLAN_JUN,
        rkt.PLAN_JUL,
        rkt.PLAN_AUG,
        rkt.PLAN_SEP,
        rkt.PLAN_OCT,
        rkt.PLAN_NOV,
        rkt.PLAN_DEC,
        rkt.COST_JAN,
        rkt.COST_FEB,
        rkt.COST_MAR,
        rkt.COST_APR,
        rkt.COST_MAY,
        rkt.COST_JUN,
        rkt.COST_JUL,
        rkt.COST_AUG,
        rkt.COST_SEP,
        rkt.COST_OCT,
        rkt.COST_NOV,
        rkt.COST_DEC,
        rkt.TIPE_TRANSAKSI,
        activity.DESCRIPTION ACTIVITY_DESC 
      FROM TR_RKT rkt 
      LEFT JOIN TM_HECTARE_STATEMENT hs 
        ON hs.PERIOD_BUDGET = rkt.PERIOD_BUDGET 
        AND hs.BA_CODE = rkt.BA_CODE 
        AND hs.AFD_CODE = rkt.AFD_CODE 
        AND hs.BLOCK_CODE = rkt.BLOCK_CODE 
      LEFT JOIN TM_ACTIVITY activity 
        ON rkt.ACTIVITY_CODE = activity.ACTIVITY_CODE 
      LEFT JOIN TM_ORGANIZATION ORG 
        ON rkt.BA_CODE = ORG.BA_CODE 
      WHERE rkt.DELETE_USER IS NULL 
      AND rkt.TIPE_TRANSAKSI = 'MANUAL_INFRA' 
      $where
    ";
    
    if($this->_siteCode <> 'ALL') {
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(hs.BA_CODE)||'%'";
    }
    if($params['budgetperiod'] != ''){
      $query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
                AND to_char(hs.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
    }else{
      $query .= "
                AND to_char(hs.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(hs.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
    
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
    
    //jika diupdate dari norma infra, filter berdasarkan kelas aktivitas
    if ($params['ACTIVITY_CLASS'] != '') {
      $query .= "
                AND rkt.ACTIVITY_CLASS IN ('".$params['ACTIVITY_CLASS']."') 
            ";
        }
    
    //jika diupdate dari norma infra, filter berdasarkan tipe tanah
    if ($params['LAND_TYPE'] != '') {
      $query .= "
                AND hs.LAND_TYPE IN ('".$params['LAND_TYPE']."') 
            ";
        }
    
    //jika diupdate dari norma infra, filter berdasarkan topografi
    if ($params['TOPOGRAPHY'] != '') {
      $query .= "
                AND hs.TOPOGRAPHY IN ('".$params['TOPOGRAPHY']."') 
            ";
        }
    
    if ($params['activity_code'] != '') {
      $query .= "
                AND rkt.ACTIVITY_CODE IN ('".$params['activity_code']."')
            ";
        }
    
    //jika diupdate dari RKT VRA, filter berdasarkan kode activity
    if ($params['ACT_CODE'] != '') {
      $query .= "
                AND UPPER(rkt.ACTIVITY_CODE) IN ('".$params['ACT_CODE']."')
            ";
        }
    
    if ($params['src_afd'] != '') {
      $query .= "
                AND UPPER(hs.AFD_CODE) LIKE UPPER('%".$params['src_afd']."%')
            ";
        }
    
    if ($params['src_block'] != '') {
      $query .= "
                AND UPPER(hs.BLOCK_CODE) LIKE UPPER('%".$params['src_block']."%')
            ";
        }
    
    if (($params['src_matstage_code'] != '') && ($params['src_matstage_code'] != '0') && ($params['src_matstage_code'] <> 'ALL')) {
      $query .= "
                AND (
          UPPER(hs.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
          OR UPPER(hs.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
        )
            ";
        }
    
    $query .= "
      ORDER BY hs.BA_CODE, hs.AFD_CODE, hs.BLOCK_CODE
    ";
    
    return $query;
  }

  public function calculateDistribusiVRA($params = array()) {
    $trx_code = ($arrAfdFix['PERIOD_BUDGET']."-".$arrAfdFix['BA_CODE']."-RKT015-".$arrAfdFix['ACTIVITY_CODE']."-".$arrAfdFix['vraCode']);

    $sql = "
      MERGE INTO TR_RKT_VRA_DISTRIBUSI VD 
      USING (
        SELECT
        R.PERIOD_BUDGET, HS.BA_CODE, R.ACTIVITY_CODE, I.SUB_COST_ELEMENT VRA_CODE, HS.AFD_CODE,
        SUM(R.TOTAL_RP_SETAHUN/VS.VALUE) HM_KM,
        MAX(VS.VALUE) PRICE_QTY_VRA, 
        SUM(R.TOTAL_RP_SETAHUN) PRICE_HM_KM, 
        EXTRACT(YEAR FROM R.PERIOD_BUDGET)||'-'||HS.BA_CODE||'-RKT015-'||R.ACTIVITY_CODE||'-'||I.SUB_COST_ELEMENT TRX_CODE,
        'INFRA' TIPE_TRANSAKSI
        FROM TR_RKT R
        JOIN TM_HECTARE_STATEMENT HS ON HS.BA_CODE = R.BA_CODE AND HS.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND HS.AFD_CODE = R.AFD_CODE AND HS.BLOCK_CODE = R.BLOCK_CODE
        JOIN TN_INFRASTRUKTUR I ON I.ACTIVITY_CODE = R.ACTIVITY_CODE AND I.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND I.BA_CODE = R.BA_CODE AND I.LAND_TYPE = HS.LAND_TYPE AND I.TOPOGRAPHY = HS.TOPOGRAPHY
        JOIN TR_RKT_VRA_SUM VS ON VS.BA_CODE = R.BA_CODE AND VS.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND VS.VRA_CODE = I.SUB_COST_ELEMENT
        WHERE R.BA_CODE = '".$params['key_find']."' AND R.PLAN_SETAHUN > 0
        AND R.ACTIVITY_CODE = '".$params['src_activity_code']."' 
        AND EXTRACT(YEAR FROM R.PERIOD_BUDGET) = '".$params['budgetperiod']."'
        AND R.SUMBER_BIAYA = 'INTERNAL'
        GROUP BY R.PERIOD_BUDGET, HS.BA_CODE, R.ACTIVITY_CODE, I.SUB_COST_ELEMENT, HS.AFD_CODE
      ) RKT
      ON (
        RKT.PERIOD_BUDGET = VD.PERIOD_BUDGET AND RKT.BA_CODE = VD.BA_CODE AND RKT.ACTIVITY_CODE = VD.ACTIVITY_CODE
        AND RKT.VRA_CODE = VD.VRA_CODE AND RKT.AFD_CODE = VD.LOCATION_CODE
      )
      WHEN MATCHED THEN UPDATE SET
        VD.PERIOD_BUDGET = RKT.PERIOD_BUDGET,
        VD.BA_CODE = RKT.BA_CODE,
        VD.ACTIVITY_CODE = RKT.ACTIVITY_CODE,
        VD.VRA_CODE = RKT.VRA_CODE,
        VD.LOCATION_CODE = RKT.AFD_CODE,
        VD.HM_KM = RKT.HM_KM,
        VD.PRICE_QTY_VRA = RKT.PRICE_QTY_VRA,
        VD.PRICE_HM_KM = RKT.PRICE_HM_KM,
        VD.TRX_CODE = RKT.TRX_CODE,
        VD.TIPE_TRANSAKSI = RKT.TIPE_TRANSAKSI,
        VD.UPDATE_USER = '".$this->_userName."',
        VD.UPDATE_TIME = CURRENT_TIMESTAMP
      WHEN NOT MATCHED THEN INSERT (
        PERIOD_BUDGET,BA_CODE,ACTIVITY_CODE,VRA_CODE,LOCATION_CODE,HM_KM,PRICE_QTY_VRA,
        PRICE_HM_KM,TRX_CODE,TIPE_TRANSAKSI,INSERT_USER,INSERT_TIME
      )
      VALUES (
        RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.ACTIVITY_CODE,RKT.VRA_CODE,RKT.AFD_CODE,RKT.HM_KM,RKT.PRICE_QTY_VRA,
        RKT.PRICE_HM_KM,RKT.TRX_CODE,RKT.TIPE_TRANSAKSI,'".$this->_userName."',CURRENT_TIMESTAMP
      )
    ";
    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }

  
  //reset perhitungan
  public function saveTempKasSan($row = array())
  { 
    //hitung rotasi
    $sebaran_rotasi = $this->updateTempKastrasi($row);    
    $row['PLAN_JAN'] = $sebaran_rotasi[1];
    $row['PLAN_FEB'] = $sebaran_rotasi[2];
    $row['PLAN_MAR'] = $sebaran_rotasi[3];
    $row['PLAN_APR'] = $sebaran_rotasi[4];
    $row['PLAN_MAY'] = $sebaran_rotasi[5];
    $row['PLAN_JUN'] = $sebaran_rotasi[6];
    $row['PLAN_JUL'] = $sebaran_rotasi[7];
    $row['PLAN_AUG'] = $sebaran_rotasi[8];
    $row['PLAN_SEP'] = $sebaran_rotasi[9];
    $row['PLAN_OCT'] = $sebaran_rotasi[10];
    $row['PLAN_NOV'] = $sebaran_rotasi[11];
    $row['PLAN_DEC'] = $sebaran_rotasi[12];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = NULL,
        RP_ROTASI_SMS2 = NULL,
        TOTAL_RP_QTY = NULL,
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''),
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        DIS_JAN = NULL,
        DIS_FEB = NULL,
        DIS_MAR = NULL,
        DIS_APR = NULL,
        DIS_MAY = NULL,
        DIS_JUN = NULL,
        DIS_JUL = NULL,
        DIS_AUG = NULL,
        DIS_SEP = NULL,
        DIS_OCT = NULL,
        DIS_NOV = NULL,
        DIS_DEC = NULL,
        COST_SMS1 = NULL,
        COST_SMS2 = NULL,
        DIS_SETAHUN = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  public function updateTempKastrasi($row = array()){
    //get norma kastrasi
    $norma = $this->_formula->getNormaKastrasiSanitasi($row);
    
    for ($i = 1 ; $i <= 12 ; $i++){
      //hitung selisih umur
      $date1_y = substr($row['TAHUN_TANAM'], -4);
      $date2_y = $row['PERIOD_BUDGET'];
      $selisih_tahun = (((int)($date2_y) - (int)($date1_y)) * 12);
      
      $date1_m = substr($row['TAHUN_TANAM'], 0, 2);
      $selisih_bulan = (int)($i) - (int)($date1_m);
      
      $total_selisih_umur = $selisih_tahun + $selisih_bulan;
      if (in_array($total_selisih_umur, $norma)) {
        $return[$i] = $row['HA_PLANTED'];
      }else{
        $return[$i] = 0;
      }
    }
    
    return($return);
  }
  
  //hitung per cost element
  public function calCostElementRawat($costElement, $row = array())
  { 
    $result = true;
    //hitung sumber biaya 
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    $row['PLAN_JAN'] = $sebaran_rotasi['PLAN_JAN'];
    $row['PLAN_FEB'] = $sebaran_rotasi['PLAN_FEB'];
    $row['PLAN_MAR'] = $sebaran_rotasi['PLAN_MAR'];
    $row['PLAN_APR'] = $sebaran_rotasi['PLAN_APR'];
    $row['PLAN_MAY'] = $sebaran_rotasi['PLAN_MAY'];
    $row['PLAN_JUN'] = $sebaran_rotasi['PLAN_JUN'];
    $row['PLAN_JUL'] = $sebaran_rotasi['PLAN_JUL'];
    $row['PLAN_AUG'] = $sebaran_rotasi['PLAN_AUG'];
    $row['PLAN_SEP'] = $sebaran_rotasi['PLAN_SEP'];
    $row['PLAN_OCT'] = $sebaran_rotasi['PLAN_OCT'];
    $row['PLAN_NOV'] = $sebaran_rotasi['PLAN_NOV'];
    $row['PLAN_DEC'] = $sebaran_rotasi['PLAN_DEC'];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    //hitung cost element
    $mon = $this->_formula->cal_RktManual_CostElement($costElement,$row);
    $row['TOTAL_RP_SMS1'] = ($rotasi['SMS1']) ? $mon[1] : 0;
    $row['TOTAL_RP_SMS2'] = ($rotasi['SMS2']) ? $mon[2] : 0;
    $row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
    $total = $this->_formula->cal_RktManual_Total($row);

    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''),
        RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($row['TOTAL_RP_SMS1_SMS2'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
        DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
        DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''),
        DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''),
        DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''),
        DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''),
        DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''),
        DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''),
        DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''),
        DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''),
        DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''),
        DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''),
        DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''),
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
      AND COST_ELEMENT = '".addslashes($costElement)."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung per cost element
  public function calCostElementRawatOpsi($costElement, $row = array())
  { 
    //hitung cost element
    $row['TIPE_RKT_MANUAL'] = 'NON_INFRA_OPSI';
    
    //hitung sumber biaya 
    $row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //get default attribut / activity opsi
    $params['src_activity_code'] = $row['ACTIVITY_CODE'];
    $params['budgetperiod'] = $row['PERIOD_BUDGET'];
    $params['key_find'] = $row['BA_CODE'];
    $opt_activity_opsi = $this->getActivityOpsi($params);
    $row['ATRIBUT'] = ($row['ATRIBUT']) ? $row['ATRIBUT'] : $opt_activity_opsi['rows'][0]['KODE']; 
    
    //default activity class
    $opt_activity_class = $this->getActivityClass($params);
    $row['ACTIVITY_CLASS'] = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : $opt_activity_class['rows'][0]['NILAI']; 
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row); 
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    $row['PLAN_JAN'] = $sebaran_rotasi['PLAN_JAN'];
    $row['PLAN_FEB'] = $sebaran_rotasi['PLAN_FEB'];
    $row['PLAN_MAR'] = $sebaran_rotasi['PLAN_MAR'];
    $row['PLAN_APR'] = $sebaran_rotasi['PLAN_APR'];
    $row['PLAN_MAY'] = $sebaran_rotasi['PLAN_MAY'];
    $row['PLAN_JUN'] = $sebaran_rotasi['PLAN_JUN'];
    $row['PLAN_JUL'] = $sebaran_rotasi['PLAN_JUL'];
    $row['PLAN_AUG'] = $sebaran_rotasi['PLAN_AUG'];
    $row['PLAN_SEP'] = $sebaran_rotasi['PLAN_SEP'];
    $row['PLAN_OCT'] = $sebaran_rotasi['PLAN_OCT'];
    $row['PLAN_NOV'] = $sebaran_rotasi['PLAN_NOV'];
    $row['PLAN_DEC'] = $sebaran_rotasi['PLAN_DEC'];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    //hitung cost element
    $mon = $this->_formula->cal_RktManual_CostElementOpsi($costElement,$row);
    $row['TOTAL_RP_SMS1'] = ($rotasi['SMS1']) ? $mon[1] : 0;
    $row['TOTAL_RP_SMS2'] = ($rotasi['SMS2']) ? $mon[2] : 0;
    $row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
    $total = $this->_formula->cal_RktManual_Total($row);
    
    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''),
        RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($row['TOTAL_RP_SMS1_SMS2'])."',',',''),
        ROTASI_SMS1 = '".addslashes($rotasi['SMS1'])."',
        ROTASI_SMS2 = '".addslashes($rotasi['SMS2'])."',
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        ATRIBUT = '".addslashes($row['ATRIBUT'])."', 
        DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
        DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
        DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''),
        DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''),
        DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''),
        DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''),
        DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''),
        DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''),
        DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''),
        DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''),
        DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''),
        DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''),
        DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''),
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
      AND COST_ELEMENT = '".addslashes($costElement)."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung cost element
  public function calCostElementTanamAuto($costElement, $row = array())
  { 
    $result = true;
    $cost_sms1 = $cost_sms2 = 0;
    
    //get sumber biaya
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung cost element
    $dis = $this->_formula->cal_RktTanam_DistribusiHa($row);
    $mon = $this->_formula->cal_costElement_RktTanam($costElement, $row);
    
    //save hasil cost element
      $row['COST_TOTAL_RP_QTY'] = $mon[1];
      
      $jan = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JAN']; $cost_sms1 += $jan;
      $feb = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_FEB']; $cost_sms1 += $feb;
      $mar = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_MAR']; $cost_sms1 += $mar;
      $apr = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_APR']; $cost_sms1 += $apr;
      $may = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_MAY']; $cost_sms1 += $may;
      $jun = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JUN']; $cost_sms1 += $jun;
      $jul = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_JUL']; $cost_sms2 += $jul;
      $aug = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_AUG']; $cost_sms2 += $aug;
      $sep = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_SEP']; $cost_sms2 += $sep;
      $oct = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_OCT']; $cost_sms2 += $oct;
      $nov = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_NOV']; $cost_sms2 += $nov;
      $dec = $row['COST_TOTAL_RP_QTY'] * $dis['PLAN_DEC']; $cost_sms2 += $dec;
      
      $tahun = $dis['PLAN_JAN'] + $dis['PLAN_FEB'] + $dis['PLAN_MAR'] + $dis['PLAN_APR'] + $dis['PLAN_MAY'] + $dis['PLAN_JUN'] + $dis['PLAN_JUL'] + $dis['PLAN_AUG'] + $dis['PLAN_SEP'] + $dis['PLAN_OCT'] + $dis['PLAN_NOV'] + $dis['PLAN_DEC'];
      $biaya = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $dec;
      
      $sql = "
        UPDATE TR_RKT_COST_ELEMENT 
        SET 
          AFD_CODE='".addslashes($row['AFD_CODE'])."', 
          BLOCK_CODE='".addslashes($row['BLOCK_CODE'])."', 
          SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', 
          TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
          ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
          ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
          ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
          TOTAL_RP_QTY = REPLACE('".$row['COST_TOTAL_RP_QTY']."',',',''), 
          PLAN_JAN = REPLACE('".addslashes($dis['PLAN_JAN'])."',',',''), 
          PLAN_FEB = REPLACE('".addslashes($dis['PLAN_FEB'])."',',',''), 
          PLAN_MAR = REPLACE('".addslashes($dis['PLAN_MAR'])."',',',''), 
          PLAN_APR = REPLACE('".addslashes($dis['PLAN_APR'])."',',',''), 
          PLAN_MAY = REPLACE('".addslashes($dis['PLAN_MAY'])."',',',''), 
          PLAN_JUN = REPLACE('".addslashes($dis['PLAN_JUN'])."',',',''), 
          PLAN_JUL = REPLACE('".addslashes($dis['PLAN_JUL'])."',',',''), 
          PLAN_AUG = REPLACE('".addslashes($dis['PLAN_AUG'])."',',',''), 
          PLAN_SEP = REPLACE('".addslashes($dis['PLAN_SEP'])."',',',''), 
          PLAN_OCT = REPLACE('".addslashes($dis['PLAN_OCT'])."',',',''), 
          PLAN_NOV = REPLACE('".addslashes($dis['PLAN_NOV'])."',',',''), 
          PLAN_DEC = REPLACE('".addslashes($dis['PLAN_DEC'])."',',',''), 
          PLAN_SETAHUN = REPLACE('".addslashes($tahun)."',',',''), 
          DIS_JAN = REPLACE('".addslashes($jan)."',',',''), DIS_FEB = REPLACE('".addslashes($feb)."',',',''), 
          DIS_MAR = REPLACE('".addslashes($mar)."',',',''), DIS_APR = REPLACE('".addslashes($apr)."',',',''), 
          DIS_MAY = REPLACE('".addslashes($may)."',',',''), DIS_JUN = REPLACE('".addslashes($jun)."',',',''), 
          DIS_JUL = REPLACE('".addslashes($jul)."',',',''), DIS_AUG = REPLACE('".addslashes($aug)."',',',''), 
          DIS_SEP = REPLACE('".addslashes($sep)."',',',''), DIS_OCT = REPLACE('".addslashes($oct)."',',',''), 
          DIS_NOV = REPLACE('".addslashes($nov)."',',',''), DIS_DEC = REPLACE('".addslashes($dec)."',',',''), 
          DIS_SETAHUN = REPLACE('".addslashes($biaya)."',',',''), COST_SMS1 = REPLACE('".addslashes($cost_sms1)."',',',''), 
          COST_SMS2 = REPLACE('".addslashes($cost_sms2)."',',',''), 
          MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', 
          MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."'
        WHERE 
          TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
          AND COST_ELEMENT = '".addslashes($costElement)."';
        ";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung per cost element
  public function calCostElementPanen($costElement, $row = array())
  { 
    $result = true;
    
    $row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : 'INTERNAL';
    
    //hitung cost element
    $arrHSP = $this->_formula->get_RktPanen_HSProd($row);
    $row['TON'] = $arrHSP['TON'];
    $row['JANJANG'] = $arrHSP['JANJANG'];
    $row['BJR_AFD'] = $arrHSP['BJR_AFD'];
    $row['TOTAL_COST_ELEMENT'] = 0;
    
    //reset data
    $row['BIAYA_PEMANEN_HK'] = 0;
    $row['BIAYA_PEMANEN_RP_BASIS'] = 0;
    //$row['BIAYA_PEMANEN_RP_PREMI'] = 0; remarked by NBU 08.05.2015
    $row['BIAYA_PEMANEN_RP_PREMI_JANJANG'] = 0;
    $row['BIAYA_PEMANEN_RP_PREMI_BRD'] = 0;
    $row['BIAYA_PEMANEN_RP_TOTAL'] = 0;
    $row['BIAYA_PEMANEN_RP_KG'] = 0;
    $row['BIAYA_SPV_RP_BASIS'] = 0;
    $row['BIAYA_SPV_RP_PREMI'] = 0;
    $row['BIAYA_SPV_RP_TOTAL'] = 0;
    $row['BIAYA_SPV_RP_KG'] = 0;
    $row['TUKANG_MUAT_BASIS'] = 0;
    $row['TUKANG_MUAT_PREMI'] = 0;
    $row['TUKANG_MUAT_TOTAL'] = 0;
    $row['TUKANG_MUAT_RP_KG'] = 0;
    $row['SUPIR_RP_KG'] = 0;
    $row['SUPIR_PREMI'] = 0;
    $row['KRANI_BUAH_BASIS'] = 0;
    $row['KRANI_BUAH_PREMI'] = 0;
    $row['KRANI_BUAH_TOTAL'] = 0;
    $row['KRANI_BUAH_RP_KG'] = 0;
    $row['BIAYA_ALAT_PANEN_RP_KG'] = 0;
    $row['BIAYA_ALAT_PANEN_RP_TOTAL'] = 0;
    $row['ANGKUT_TBS_RP_KG_KM'] = 0;
    $row['ANGKUT_TBS_RP_ANGKUT'] = 0;
    $row['ANGKUT_TBS_RP_KG'] = 0;
    $row['LANGSIR_TON'] = 0;
    $row['LANGSIR_RP'] = 0;
    $row['LANGSIR_RP_KG'] = 0;
    
    if($costElement == 'LABOUR'){
      $row['BIAYA_PEMANEN_HK'] = $this->_formula->get_RktPanen_PemanenHK($row);
      $row['BIAYA_PEMANEN_RP_BASIS'] = $this->_formula->get_RktPanen_PemanenBasis($row);
      //$row['BIAYA_PEMANEN_RP_PREMI'] = $this->_formula->get_RktPanen_PemanenPremi($row); remarked by NBU 08.05.2015
      // $premi_baru = $this->_formula->get_RktPanen_PemanenPremi($row);

      $premi_2018 = $this->_formula->get_RktPanen_Premi2018($row);
      $row['INCENTIVE'] = $this->_formula->get_RktPanen_Incentive2018($row);
      $row['BIAYA_PEMANEN_RP_PREMI_JANJANG'] = $premi_2018['premi_2018'];
      $row['BIAYA_PEMANEN_RP_PREMI_BRD'] = $premi_2018['premi_brondolan'];
      $row['BIAYA_PEMANEN_RP_TOTAL'] = $this->_formula->get_RktPanen_PemanenTotal($row); //total
      $row['BIAYA_PEMANEN_RP_KG'] = $this->_formula->get_RktPanen_PemanenKg($row);
      $row['BIAYA_SPV_RP_BASIS'] = $this->_formula->get_RktPanen_SpvBasis($row);
      $row['BIAYA_SPV_RP_PREMI'] = $this->_formula->get_SpvPremi_2018($row);
      $row['BIAYA_SPV_RP_TOTAL'] = $this->_formula->get_RktPanen_SpvTotal($row); //total
      $row['BIAYA_SPV_RP_KG'] = $this->_formula->get_RktPanen_SpvKg($row);
      $row['TUKANG_MUAT_BASIS'] = $this->_formula->get_RktPanen_TkgBasis($row);
      $row['TUKANG_MUAT_PREMI'] = $this->_formula->get_RktPanen_TkgPremi($row);
      $row['TUKANG_MUAT_TOTAL'] = $this->_formula->get_RktPanen_TkgTotal($row); //total
      $row['TUKANG_MUAT_RP_KG'] = $this->_formula->get_RktPanen_TkgKg($row); 
      $row['SUPIR_RP_KG'] = $this->_formula->get_RktPanen_SprKg($row);
      $row['SUPIR_PREMI'] = $this->_formula->get_RktPanen_SprPremi($row); //total
      $row['KRANI_BUAH_BASIS'] = $this->_formula->get_RktPanen_KraniBasis($row);
      $row['KRANI_BUAH_PREMI'] = $this->_formula->get_RktPanen_KraniPremi($row);
      $row['KRANI_BUAH_TOTAL'] = $this->_formula->get_RktPanen_KraniTotal($row); //total
      $row['KRANI_BUAH_RP_KG'] = $this->_formula->get_RktPanen_KraniKg($row);
      
      //total biaya
      $row['TOTAL_COST_ELEMENT'] += $row['BIAYA_PEMANEN_RP_TOTAL'];
      $row['TOTAL_COST_ELEMENT'] += $row['BIAYA_SPV_RP_TOTAL'];
      $row['TOTAL_COST_ELEMENT'] += $row['TUKANG_MUAT_TOTAL'];
      $row['TOTAL_COST_ELEMENT'] += $row['SUPIR_PREMI'];
      $row['TOTAL_COST_ELEMENT'] += $row['KRANI_BUAH_TOTAL'];
      $row['TOTAL_COST_ELEMENT'] += $row['INCENTIVE'];
    }elseif($costElement == 'TOOLS'){
      $row['BIAYA_ALAT_PANEN_RP_KG'] = $this->_formula->get_RktPanen_ToolsKg($row);
      $row['BIAYA_ALAT_PANEN_RP_TOTAL'] = $this->_formula->get_RktPanen_ToolsTotal($row); //total
      
      //total biaya
      $row['TOTAL_COST_ELEMENT'] += $row['BIAYA_ALAT_PANEN_RP_TOTAL'];
    }elseif($costElement == 'TRANSPORT'){
      $row['ANGKUT_TBS_RP_KG_KM'] = $this->_formula->get_RktPanen_AngkutKGKM($row);
      $row['ANGKUT_TBS_RP_ANGKUT'] = $this->_formula->get_RktPanen_Angkut($row); //total
      $row['ANGKUT_TBS_RP_KG'] = $this->_formula->get_RktPanen_AngkutKG($row);  
      $row['LANGSIR_TON'] = $this->_formula->get_RktPanen_LangsirTon($row);
      $row['LANGSIR_RP'] = $this->_formula->get_RktPanen_Langsir($row); //total
      $row['LANGSIR_RP_KG'] = $this->_formula->get_RktPanen_LangsirKg($row);
    
      //total biaya
      $row['TOTAL_COST_ELEMENT'] += $row['ANGKUT_TBS_RP_ANGKUT'];
      $row['TOTAL_COST_ELEMENT'] += $row['LANGSIR_RP'];
    }
    
    $distribusi = $this->_formula->cal_RktPanen_Distribusi($row);
    
    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_PANEN_COST_ELEMENT
      SET SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
        TON = REPLACE('".addslashes($row['TON'])."',',',''), 
        JANJANG = REPLACE('".addslashes($row['JANJANG'])."',',',''), 
        BJR_AFD = REPLACE('".addslashes($row['BJR_AFD'])."',',',''), 
        JARAK_PKS = REPLACE('".addslashes($row['JARAK_PKS'])."',',',''), 
        PERSEN_LANGSIR = REPLACE('".addslashes($row['PERSEN_LANGSIR'])."',',',''), 
        BIAYA_PEMANEN_HK = REPLACE('".addslashes($row['BIAYA_PEMANEN_HK'])."',',',''), 
        BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
        INCENTIVE = REPLACE('".addslashes($row['INCENTIVE'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
        BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
        BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_PEMANEN_RP_KG'])."',',',''), 
        BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($row['BIAYA_SPV_RP_BASIS'])."',',',''), 
        BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($row['BIAYA_SPV_RP_PREMI'])."',',',''), 
        BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_SPV_RP_TOTAL'])."',',',''), 
        BIAYA_SPV_RP_KG = REPLACE('".addslashes($row['BIAYA_SPV_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($row['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
        TUKANG_MUAT_BASIS = REPLACE('".addslashes($row['TUKANG_MUAT_BASIS'])."',',',''), 
        TUKANG_MUAT_PREMI = REPLACE('".addslashes($row['TUKANG_MUAT_PREMI'])."',',',''), 
        TUKANG_MUAT_TOTAL = REPLACE('".addslashes($row['TUKANG_MUAT_TOTAL'])."',',',''), 
        TUKANG_MUAT_RP_KG = REPLACE('".addslashes($row['TUKANG_MUAT_RP_KG'])."',',',''), 
        SUPIR_PREMI = REPLACE('".addslashes($row['SUPIR_PREMI'])."',',',''), 
        SUPIR_RP_KG = REPLACE('".addslashes($row['SUPIR_RP_KG'])."',',',''), 
        ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
        ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($row['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
        ANGKUT_TBS_RP_KG = REPLACE('".addslashes($row['ANGKUT_TBS_RP_KG'])."',',',''), 
        KRANI_BUAH_BASIS = REPLACE('".addslashes($row['KRANI_BUAH_BASIS'])."',',',''), 
        KRANI_BUAH_PREMI = REPLACE('".addslashes($row['KRANI_BUAH_PREMI'])."',',',''), 
        KRANI_BUAH_TOTAL = REPLACE('".addslashes($row['KRANI_BUAH_TOTAL'])."',',',''), 
        KRANI_BUAH_RP_KG = REPLACE('".addslashes($row['KRANI_BUAH_RP_KG'])."',',',''), 
        LANGSIR_TON = REPLACE('".addslashes($row['LANGSIR_TON'])."',',',''), 
        LANGSIR_RP = REPLACE('".addslashes($row['LANGSIR_RP'])."',',',''), 
        LANGSIR_RP_KG = REPLACE('".addslashes($row['LANGSIR_RP_KG'])."',',',''), 
        COST_JAN = REPLACE('".$distribusi['COST_JAN']."',',',''), 
        COST_FEB = REPLACE('".$distribusi['COST_FEB']."',',',''), 
        COST_MAR = REPLACE('".$distribusi['COST_MAR']."',',',''), 
        COST_APR = REPLACE('".$distribusi['COST_APR']."',',',''), 
        COST_MAY = REPLACE('".$distribusi['COST_MAY']."',',',''), 
        COST_JUN = REPLACE('".$distribusi['COST_JUN']."',',',''), 
        COST_JUL = REPLACE('".$distribusi['COST_JUL']."',',',''), 
        COST_AUG = REPLACE('".$distribusi['COST_AUG']."',',',''), 
        COST_SEP = REPLACE('".$distribusi['COST_SEP']."',',',''), 
        COST_OCT = REPLACE('".$distribusi['COST_OCT']."',',',''), 
        COST_NOV = REPLACE('".$distribusi['COST_NOV']."',',',''), 
        COST_DEC = REPLACE('".$distribusi['COST_DEC']."',',',''), 
        COST_SETAHUN = REPLACE('".$distribusi['TOTAL_COST']."',',',''), 
        COST_SMS1 = REPLACE('".$distribusi['TOTAL_COST_SMS1']."',',',''), 
        COST_SMS2 = REPLACE('".$distribusi['TOTAL_COST_SMS2']."',',',''),
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."' 
        AND COST_ELEMENT='$costElement';
    ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung per cost element
  public function calCostElementKasSan($costElement, $row = array())
  { 
    $result = true;
    //hitung sumber biaya 
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung rotasi
    $sebaran_rotasi = $this->updateTempKastrasi($row);    
    $row['PLAN_JAN'] = $sebaran_rotasi[1];
    $row['PLAN_FEB'] = $sebaran_rotasi[2];
    $row['PLAN_MAR'] = $sebaran_rotasi[3];
    $row['PLAN_APR'] = $sebaran_rotasi[4];
    $row['PLAN_MAY'] = $sebaran_rotasi[5];
    $row['PLAN_JUN'] = $sebaran_rotasi[6];
    $row['PLAN_JUL'] = $sebaran_rotasi[7];
    $row['PLAN_AUG'] = $sebaran_rotasi[8];
    $row['PLAN_SEP'] = $sebaran_rotasi[9];
    $row['PLAN_OCT'] = $sebaran_rotasi[10];
    $row['PLAN_NOV'] = $sebaran_rotasi[11];
    $row['PLAN_DEC'] = $sebaran_rotasi[12];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    //hitung cost element
    $mon = $this->_formula->cal_RktKastrasiSanitasi_CostElement($costElement,$row); 
    $row['TOTAL_RP_SMS1'] = ($row['TOTAL_PLAN_SMS1']) ? $mon[1] : 0;
    $row['TOTAL_RP_SMS2'] = ($row['TOTAL_PLAN_SMS2']) ? $mon[2] : 0;
    $row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
    $total = $this->_formula->cal_RktManual_Total($row);
    
    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''),
        RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($row['TOTAL_RP_SMS1_SMS2'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
        DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
        DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''),
        DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''),
        DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''),
        DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''),
        DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''),
        DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''),
        DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''),
        DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''),
        DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''),
        DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''),
        DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''),
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
      AND COST_ELEMENT = '".addslashes($costElement)."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung per cost element
  public function calCostElementRawatSisip($costElement, $row = array())
  { 
    $result = true;
    //hitung sumber biaya 
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //hitung rotasi
    $rotasi = $this->_formula->get_RktManual_Rotasi($row);
    $row['ROTASI_SMS1'] = $rotasi['SMS1'];
    $row['ROTASI_SMS2'] = $rotasi['SMS2'];
    
    //hitung sebaran rotasi
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    //hitung cost element
    $mon = $this->_formula->cal_RktManual_CostElement($costElement,$row);
    $konversi_pokok_ha = $this->_formula->cal_KonversiPokokHa($row);
  
    $row['TOTAL_RP_SMS1'] = ($rotasi['SMS1']) ? ($mon[1]/$konversi_pokok_ha['SPH_STANDAR']) : (0);
    $row['TOTAL_RP_SMS2'] = ($rotasi['SMS2']) ? ($mon[2]/$konversi_pokok_ha['SPH_STANDAR']) : (0);
    $row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
    $total = $this->_formula->cal_RktManual_Total($row);
    
    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''),
        RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($row['TOTAL_RP_SMS1_SMS2'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($row['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($row['ROTASI_SMS2'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        BULAN_PENGERJAAN = NULL, 
        ATRIBUT = NULL, 
        DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
        DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
        DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''),
        DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''),
        DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''),
        DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''),
        DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''),
        DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''),
        DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''),
        DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''),
        DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''),
        DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''),
        DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''),
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
      AND COST_ELEMENT = '".addslashes($costElement)."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung cost element
  public function calCostElementManualInfra($costElement, $row = array())
  { 
      $result = true;
  
  //jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
  $row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
  
  //hitung cost element
  $row['TIPE_RKT_MANUAL'] = 'INFRA';
  $rotasi = $this->_formula->get_RktManual_Rotasi($row);
  
  $mon = $this->_formula->cal_RktManual_CostElementInfra($costElement,$row);
  $row['TOTAL_RP_SMS1'] = $mon[1];
  $row['TOTAL_RP_SMS2'] = $mon[2];
  $row['TOTAL_RP_SMS1_SMS2'] = $row['TOTAL_RP_SMS1'] + $row['TOTAL_RP_SMS2'];
  $total = $this->_formula->cal_RktManual_Total($row);
  
  //save hasil cost element
    $sql= "
      UPDATE TR_RKT_COST_ELEMENT
        SET 
          AFD_CODE='".addslashes($row['AFD_CODE'])."', 
          BLOCK_CODE='".addslashes($row['BLOCK_CODE'])."', 
          SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', 
          ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
          ROTASI_SMS1 = REPLACE('".addslashes($rotasi['SMS1'])."',',',''), 
          ROTASI_SMS2 = REPLACE('".addslashes($rotasi['SMS2'])."',',',''), 
          TOTAL_RP_QTY = REPLACE('".$row['BASIC_TOTAL_RP_QTY']."',',',''), 
          PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
          PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
          PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
          PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
          PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
          PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
          PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
          PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
          PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
          PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
          PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
          PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
          PLAN_SMS1 = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SMS1'])."',',',''), 
          PLAN_SMS2 = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SMS2'])."',',',''), 
          PLAN_SETAHUN = REPLACE('".addslashes($rotasi['TOTAL_PLAN_SETAHUN'])."',',',''), 
          DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''), DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''), 
          DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''), DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''), 
          DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''), DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''), 
          DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''), DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''), 
          DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''), DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''), 
          DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''), DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''), 
          DIS_SETAHUN = REPLACE('".addslashes($total['TOTAL_RP_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($total['COST_SMS1'])."',',',''), 
          COST_SMS2 = REPLACE('".addslashes($total['COST_SMS2'])."',',',''), MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', 
          MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
          RP_ROTASI_SMS1 = REPLACE('".addslashes($row['TOTAL_RP_SMS1'])."',',',''), 
          RP_ROTASI_SMS2 = REPLACE('".addslashes($row['TOTAL_RP_SMS2'])."',',',''),
          TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."' -- //<!-- TIPE NORMA -->
        WHERE 
          TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
        AND COST_ELEMENT = '".addslashes($costElement)."';
      ";
      
  //create sql file
  $this->_global->createSqlFile($row['filename'], $sql);
      return $result;
  }

  //hitung cost element
  public function calCostElementPerkerasanJalan($costElement, $row = array())
  { 
        $result = true;
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //hitung cost element
    $mon = $this->_formula->cal_RktPerkerasanJalan_CostElement($costElement,$row);
    //print_r($mon);
    $row['PRICE_QTY'] = $mon[1];
    $total = $this->_formula->cal_RktPerkerasanJalan_DistribusiTahunBerjalan($row);
    
    //save hasil cost element
    $sql = "
      UPDATE TR_RKT_PK_COST_ELEMENT 
      SET
      MATURITY_STAGE_SMS1 = '".addslashes($row['SEMESTER1'])."',
      MATURITY_STAGE_SMS2 = '".addslashes($row['SEMESTER2'])."',
      TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
      SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
      TOTAL_RP_QTY = REPLACE('".addslashes($mon[1])."',',',''),
      PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
      PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
      PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
      PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
      PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
      PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
      PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
      PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
      PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
      PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
      PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
      PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),  
      DIS_JAN = REPLACE('".addslashes($total['COST_JAN'])."',',',''),
      DIS_FEB = REPLACE('".addslashes($total['COST_FEB'])."',',',''),
      DIS_MAR = REPLACE('".addslashes($total['COST_MAR'])."',',',''), 
      DIS_APR = REPLACE('".addslashes($total['COST_APR'])."',',',''), 
      DIS_MAY = REPLACE('".addslashes($total['COST_MAY'])."',',',''), 
      DIS_JUN = REPLACE('".addslashes($total['COST_JUN'])."',',',''), 
      DIS_JUL = REPLACE('".addslashes($total['COST_JUL'])."',',',''), 
      DIS_AUG = REPLACE('".addslashes($total['COST_AUG'])."',',',''), 
      DIS_SEP = REPLACE('".addslashes($total['COST_SEP'])."',',',''), 
      DIS_OCT = REPLACE('".addslashes($total['COST_OCT'])."',',',''), 
      DIS_NOV = REPLACE('".addslashes($total['COST_NOV'])."',',',''), 
      DIS_DEC = REPLACE('".addslashes($total['COST_DEC'])."',',',''), 
      UPDATE_USER = '{$this->_userName}', 
      UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
      AND COST_ELEMENT = '".addslashes($costElement)."';
    ";
    //echo $sql."<br>";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  


  //hitung total cost
  public function calTotalCostRawat($row = array())
  { 
    $result = true;
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    
    //cari summary total cost
    $sql = "
      SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, 
           SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, 
           SUM(DIS_JAN) DIS_JAN, 
           SUM(DIS_FEB) DIS_FEB, 
           SUM(DIS_MAR) DIS_MAR, 
           SUM(DIS_APR) DIS_APR, 
           SUM(DIS_MAY) DIS_MAY, 
           SUM(DIS_JUN) DIS_JUN, 
           SUM(DIS_JUL) DIS_JUL, 
           SUM(DIS_AUG) DIS_AUG, 
           SUM(DIS_SEP) DIS_SEP, 
           SUM(DIS_OCT) DIS_OCT, 
           SUM(DIS_NOV) DIS_NOV, 
           SUM(DIS_DEC) DIS_DEC, 
           SUM(DIS_SETAHUN) DIS_SETAHUN, 
           MAX(ROTASI_SMS1) ROTASI_SMS1, 
           MAX(ROTASI_SMS2) ROTASI_SMS2,  
           MAX(PLAN_SMS1) PLAN_SMS1,  
           MAX(PLAN_SMS2) PLAN_SMS2, 
           MAX(PLAN_SETAHUN) PLAN_SETAHUN, 
           SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, 
           SUM(COST_SMS1) COST_SMS1, 
           SUM(COST_SMS2) COST_SMS2
      FROM TR_RKT_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
    ";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
    $sql = "
      UPDATE TR_RKT
      SET AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($sebaran_rotasi['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($sebaran_rotasi['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($sebaran_rotasi['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($sebaran_rotasi['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($sebaran_rotasi['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($sebaran_rotasi['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($sebaran_rotasi['PLAN_DEC'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($summary['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($summary['ROTASI_SMS2'])."',',',''),
        TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
        TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''),
        COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
        COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
        COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
        COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
        COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
        COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
        COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
        COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
        COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
        COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
        COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
        COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''),
        TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''),
        FLAG_TEMP = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return $result;
  }
  
  //hitung total cost
  public function calTotalCostRawatOpsi($row = array())
  { 
    $result = true;
    
    //hitung cost element
    $row['TIPE_RKT_MANUAL'] = 'NON_INFRA_OPSI';
    
    //hitung sumber biaya 
    $row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : $this->_formula->cekSumberBiayaExternal($row);
    
    //<!-- TIPE NORMA -->
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //get default attribut / activity opsi
    $params['src_activity_code'] = $row['ACTIVITY_CODE'];
    $params['budgetperiod'] = $row['PERIOD_BUDGET'];
    $params['key_find'] = $row['BA_CODE'];
    $opt_activity_opsi = $this->getActivityOpsi($params);
    $row['ATRIBUT'] = ($row['ATRIBUT']) ? $row['ATRIBUT'] : $opt_activity_opsi['rows'][0]['KODE']; 
    
    //default activity class
    $opt_activity_class = $this->getActivityClass($params);
    $row['ACTIVITY_CLASS'] = ($row['ACTIVITY_CLASS']) ? $row['ACTIVITY_CLASS'] : $opt_activity_class['rows'][0]['NILAI'];
    
    //hitung sebaran rotasi
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    
    //cari summary total cost
    $sql = "
      SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, 
           SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, 
           SUM(DIS_JAN) DIS_JAN, 
           SUM(DIS_FEB) DIS_FEB, 
           SUM(DIS_MAR) DIS_MAR, 
           SUM(DIS_APR) DIS_APR, 
           SUM(DIS_MAY) DIS_MAY, 
           SUM(DIS_JUN) DIS_JUN, 
           SUM(DIS_JUL) DIS_JUL, 
           SUM(DIS_AUG) DIS_AUG, 
           SUM(DIS_SEP) DIS_SEP, 
           SUM(DIS_OCT) DIS_OCT, 
           SUM(DIS_NOV) DIS_NOV, 
           SUM(DIS_DEC) DIS_DEC, 
           SUM(DIS_SETAHUN) DIS_SETAHUN, 
           MAX(ROTASI_SMS1) ROTASI_SMS1, 
           MAX(ROTASI_SMS2) ROTASI_SMS2, 
           MAX(PLAN_SETAHUN) PLAN_SETAHUN, 
           SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, 
           SUM(COST_SMS1) COST_SMS1, 
           SUM(COST_SMS2) COST_SMS2
      FROM TR_RKT_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
    ";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
    $sql = "
      UPDATE TR_RKT
      SET AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($sebaran_rotasi['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($sebaran_rotasi['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($sebaran_rotasi['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($sebaran_rotasi['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($sebaran_rotasi['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($sebaran_rotasi['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($sebaran_rotasi['PLAN_DEC'])."',',',''),
        ROTASI_SMS1 = '".addslashes($summary['ROTASI_SMS1'])."',
        ROTASI_SMS2 = '".addslashes($summary['ROTASI_SMS2'])."',
        TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
        TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''),
        COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
        COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
        COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
        COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
        COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
        COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
        COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
        COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
        COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
        COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
        COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
        COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''),
        TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''),
        SUMBER_BIAYA = REPLACE('".addslashes($row['SUMBER_BIAYA'])."',',',''),
        TIPE_NORMA = REPLACE('".addslashes($row['TIPE_NORMA'])."',',',''),
        ATRIBUT = REPLACE('".addslashes($row['ATRIBUT'])."',',',''),
        ACTIVITY_CLASS = REPLACE('".addslashes($row['ACTIVITY_CLASS'])."',',',''),
        FLAG_TEMP = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung total cost
  public function calTotalCostTanamAuto($row = array())
  { 
    $result = true;
    
    //get sumber biaya
    $row['SUMBER_BIAYA'] = $this->_formula->cekSumberBiayaExternal($row);
      
    //cari summary total cost
    $sqlsum = "
      SELECT SUM (TOTAL_RP_QTY) TOTAL_RP_QTY,
           SUM (DIS_JAN) DIS_JAN,
           SUM (DIS_FEB) DIS_FEB,
           SUM (DIS_MAR) DIS_MAR,
           SUM (DIS_APR) DIS_APR,
           SUM (DIS_MAY) DIS_MAY,
           SUM (DIS_JUN) DIS_JUN,
           SUM (DIS_JUL) DIS_JUL,
           SUM (DIS_AUG) DIS_AUG,
           SUM (DIS_SEP) DIS_SEP,
           SUM (DIS_OCT) DIS_OCT,
           SUM (DIS_NOV) DIS_NOV,
           SUM (DIS_DEC) DIS_DEC,
           SUM (DIS_SETAHUN) DIS_SETAHUN,
           SUM (COST_SMS1) COST_SMS1,
           SUM (COST_SMS2) COST_SMS2,
           MAX (PLAN_JAN) PLAN_JAN, 
           MAX (PLAN_FEB) PLAN_FEB, 
           MAX (PLAN_MAR) PLAN_MAR, 
           MAX (PLAN_APR) PLAN_APR, 
           MAX (PLAN_MAY) PLAN_MAY, 
           MAX (PLAN_JUN) PLAN_JUN, 
           MAX (PLAN_JUL) PLAN_JUL,           
           MAX (PLAN_AUG) PLAN_AUG, 
           MAX (PLAN_SEP) PLAN_SEP, 
           MAX (PLAN_OCT) PLAN_OCT, 
           MAX (PLAN_NOV) PLAN_NOV, 
           MAX (PLAN_DEC) PLAN_DEC, 
           MAX (PLAN_SETAHUN) PLAN_SETAHUN
        FROM TR_RKT_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'";
    $summary = $this->_db->fetchRow($sqlsum);
    
    //simpan total cost
    $sql = "UPDATE TR_RKT
          SET 
            AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
            SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
            PLAN_JAN = REPLACE('".addslashes($summary['PLAN_JAN'])."',',',''), 
            PLAN_FEB = REPLACE('".addslashes($summary['PLAN_FEB'])."',',',''), 
            PLAN_MAR = REPLACE('".addslashes($summary['PLAN_MAR'])."',',',''), 
            PLAN_APR = REPLACE('".addslashes($summary['PLAN_APR'])."',',',''), 
            PLAN_MAY = REPLACE('".addslashes($summary['PLAN_MAY'])."',',',''), 
            PLAN_JUN = REPLACE('".addslashes($summary['PLAN_JUN'])."',',',''), 
            PLAN_JUL = REPLACE('".addslashes($summary['PLAN_JUL'])."',',',''), 
            PLAN_AUG = REPLACE('".addslashes($summary['PLAN_AUG'])."',',',''), 
            PLAN_SEP = REPLACE('".addslashes($summary['PLAN_SEP'])."',',',''), 
            PLAN_OCT = REPLACE('".addslashes($summary['PLAN_OCT'])."',',',''), 
            PLAN_NOV = REPLACE('".addslashes($summary['PLAN_NOV'])."',',',''), 
            PLAN_DEC = REPLACE('".addslashes($summary['PLAN_DEC'])."',',',''), 
            PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''), 
            TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''), 
            COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''), COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''), 
            COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''), COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''), 
            COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''), COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''), 
            COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''), COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''), 
            COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''), COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''), 
            COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''), COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''), 
            TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''), 
            COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''), 
            MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
            FLAG_TEMP = NULL,
            UPDATE_USER = '{$this->_userName}',
            UPDATE_TIME = SYSDATE
          WHERE
            TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
          ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return $result;
  }
  
  //hitung total cost
  public function calTotalCostPanen($row = array())
  {
    $result = true;

    $row['SUMBER_BIAYA'] = ($row['SUMBER_BIAYA']) ? $row['SUMBER_BIAYA'] : 'INTERNAL';
    
    //cari summary total cost
    $sql = "
      SELECT  SUM(BIAYA_PEMANEN_HK) BIAYA_PEMANEN_HK, 
          SUM(BIAYA_PEMANEN_RP_BASIS) BIAYA_PEMANEN_RP_BASIS, 
          SUM(BIAYA_PEMANEN_RP_PREMI_JANJANG) BIAYA_PEMANEN_RP_PREMI_JANJANG,
          SUM(INCENTIVE) INCENTIVE,
          SUM(BIAYA_PEMANEN_RP_PREMI_BRD) BIAYA_PEMANEN_RP_PREMI_BRD,
          SUM(BIAYA_PEMANEN_RP_TOTAL) BIAYA_PEMANEN_RP_TOTAL, 
          SUM(BIAYA_PEMANEN_RP_KG) BIAYA_PEMANEN_RP_KG, 
          SUM(BIAYA_SPV_RP_BASIS) BIAYA_SPV_RP_BASIS, 
          SUM(BIAYA_SPV_RP_PREMI) BIAYA_SPV_RP_PREMI, 
          SUM(BIAYA_SPV_RP_TOTAL) BIAYA_SPV_RP_TOTAL, 
          SUM(BIAYA_SPV_RP_KG) BIAYA_SPV_RP_KG, 
          SUM(BIAYA_ALAT_PANEN_RP_KG) BIAYA_ALAT_PANEN_RP_KG, 
          SUM(BIAYA_ALAT_PANEN_RP_TOTAL) BIAYA_ALAT_PANEN_RP_TOTAL, 
          SUM(TUKANG_MUAT_BASIS) TUKANG_MUAT_BASIS, 
          SUM(TUKANG_MUAT_PREMI) TUKANG_MUAT_PREMI, 
          SUM(TUKANG_MUAT_TOTAL) TUKANG_MUAT_TOTAL, 
          SUM(TUKANG_MUAT_RP_KG) TUKANG_MUAT_RP_KG, 
          SUM(SUPIR_PREMI) SUPIR_PREMI, 
          SUM(SUPIR_RP_KG) SUPIR_RP_KG, 
          SUM(ANGKUT_TBS_RP_KG_KM) ANGKUT_TBS_RP_KG_KM, 
          SUM(ANGKUT_TBS_RP_ANGKUT) ANGKUT_TBS_RP_ANGKUT, 
          SUM(ANGKUT_TBS_RP_KG) ANGKUT_TBS_RP_KG, 
          SUM(KRANI_BUAH_BASIS) KRANI_BUAH_BASIS, 
          SUM(KRANI_BUAH_PREMI) KRANI_BUAH_PREMI, 
          SUM(KRANI_BUAH_TOTAL) KRANI_BUAH_TOTAL, 
          SUM(KRANI_BUAH_RP_KG) KRANI_BUAH_RP_KG, 
          SUM(LANGSIR_TON) LANGSIR_TON, 
          SUM(LANGSIR_RP) LANGSIR_RP, 
          SUM(LANGSIR_RP_KG) LANGSIR_RP_KG, 
          SUM(COST_JAN) COST_JAN, 
          SUM(COST_FEB) COST_FEB, 
          SUM(COST_MAR) COST_MAR, 
          SUM(COST_APR) COST_APR, 
          SUM(COST_MAY) COST_MAY, 
          SUM(COST_JUN) COST_JUN, 
          SUM(COST_JUL) COST_JUL, 
          SUM(COST_AUG) COST_AUG, 
          SUM(COST_SEP) COST_SEP, 
          SUM(COST_OCT) COST_OCT, 
          SUM(COST_NOV) COST_NOV, 
          SUM(COST_DEC) COST_DEC, 
          SUM(COST_SETAHUN) COST_SETAHUN, 
          SUM(COST_SMS1) COST_SMS1, 
          SUM(COST_SMS2) COST_SMS2, 
          MAX(TON) TON, 
          MAX(JANJANG) JANJANG, 
          MAX(BJR_AFD) BJR_AFD, 
          MAX(JARAK_PKS) JARAK_PKS, 
          MAX(PERSEN_LANGSIR) PERSEN_LANGSIR
      FROM TR_RKT_PANEN_COST_ELEMENT
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR')
        AND BA_CODE = '".addslashes($row['BA_CODE'])."'
        AND AFD_CODE = '".addslashes($row['AFD_CODE'])."'
        AND BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."'
    ";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
    $sql = "
      UPDATE TR_RKT_PANEN 
      SET SUMBER_BIAYA_UNIT = '".addslashes($row['SUMBER_BIAYA'])."', 
        TON = REPLACE('".addslashes($summary['TON'])."',',',''), 
        JANJANG = REPLACE('".addslashes($summary['JANJANG'])."',',',''), 
        BJR_AFD = REPLACE('".addslashes($summary['BJR_AFD'])."',',',''), 
        JARAK_PKS = REPLACE('".addslashes($summary['JARAK_PKS'])."',',',''), 
        PERSEN_LANGSIR = REPLACE('".addslashes($summary['PERSEN_LANGSIR'])."',',',''), 
        BIAYA_PEMANEN_HK = REPLACE('".addslashes($summary['BIAYA_PEMANEN_HK'])."',',',''), 
        BIAYA_PEMANEN_RP_BASIS = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_BASIS'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_JANJANG = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_JANJANG'])."',',',''), 
        INCENTIVE = REPLACE('".addslashes($summary['INCENTIVE'])."',',',''), 
        BIAYA_PEMANEN_RP_PREMI_BRD = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_PREMI_BRD'])."',',',''), 
        BIAYA_PEMANEN_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_TOTAL'])."',',',''), 
        BIAYA_PEMANEN_RP_KG = REPLACE('".addslashes($summary['BIAYA_PEMANEN_RP_KG'])."',',',''), 
        BIAYA_SPV_RP_BASIS = REPLACE('".addslashes($summary['BIAYA_SPV_RP_BASIS'])."',',',''), 
        BIAYA_SPV_RP_PREMI = REPLACE('".addslashes($summary['BIAYA_SPV_RP_PREMI'])."',',',''), 
        BIAYA_SPV_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_SPV_RP_TOTAL'])."',',',''), 
        BIAYA_SPV_RP_KG = REPLACE('".addslashes($summary['BIAYA_SPV_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_KG = REPLACE('".addslashes($summary['BIAYA_ALAT_PANEN_RP_KG'])."',',',''), 
        BIAYA_ALAT_PANEN_RP_TOTAL = REPLACE('".addslashes($summary['BIAYA_ALAT_PANEN_RP_TOTAL'])."',',',''), 
        TUKANG_MUAT_BASIS = REPLACE('".addslashes($summary['TUKANG_MUAT_BASIS'])."',',',''), 
        TUKANG_MUAT_PREMI = REPLACE('".addslashes($summary['TUKANG_MUAT_PREMI'])."',',',''), 
        TUKANG_MUAT_TOTAL = REPLACE('".addslashes($summary['TUKANG_MUAT_TOTAL'])."',',',''), 
        TUKANG_MUAT_RP_KG = REPLACE('".addslashes($summary['TUKANG_MUAT_RP_KG'])."',',',''), 
        SUPIR_PREMI = REPLACE('".addslashes($summary['SUPIR_PREMI'])."',',',''), 
        SUPIR_RP_KG = REPLACE('".addslashes($summary['SUPIR_RP_KG'])."',',',''), 
        ANGKUT_TBS_RP_KG_KM = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_KG_KM'])."',',',''), 
        ANGKUT_TBS_RP_ANGKUT = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_ANGKUT'])."',',',''), 
        ANGKUT_TBS_RP_KG = REPLACE('".addslashes($summary['ANGKUT_TBS_RP_KG'])."',',',''), 
        KRANI_BUAH_BASIS = REPLACE('".addslashes($summary['KRANI_BUAH_BASIS'])."',',',''), 
        KRANI_BUAH_PREMI = REPLACE('".addslashes($summary['KRANI_BUAH_PREMI'])."',',',''), 
        KRANI_BUAH_TOTAL = REPLACE('".addslashes($summary['KRANI_BUAH_TOTAL'])."',',',''), 
        KRANI_BUAH_RP_KG = REPLACE('".addslashes($summary['KRANI_BUAH_RP_KG'])."',',',''), 
        LANGSIR_TON = REPLACE('".addslashes($summary['LANGSIR_TON'])."',',',''), 
        LANGSIR_RP = REPLACE('".addslashes($summary['LANGSIR_RP'])."',',',''), 
        LANGSIR_RP_KG = REPLACE('".addslashes($summary['LANGSIR_RP_KG'])."',',',''), 
        COST_JAN = REPLACE('".addslashes($summary['COST_JAN'])."',',',''), 
        COST_FEB = REPLACE('".addslashes($summary['COST_FEB'])."',',',''), 
        COST_MAR = REPLACE('".addslashes($summary['COST_MAR'])."',',',''), 
        COST_APR = REPLACE('".addslashes($summary['COST_APR'])."',',',''), 
        COST_MAY = REPLACE('".addslashes($summary['COST_MAY'])."',',',''), 
        COST_JUN = REPLACE('".addslashes($summary['COST_JUN'])."',',',''), 
        COST_JUL = REPLACE('".addslashes($summary['COST_JUL'])."',',',''), 
        COST_AUG = REPLACE('".addslashes($summary['COST_AUG'])."',',',''), 
        COST_SEP = REPLACE('".addslashes($summary['COST_SEP'])."',',',''), 
        COST_OCT = REPLACE('".addslashes($summary['COST_OCT'])."',',',''), 
        COST_NOV = REPLACE('".addslashes($summary['COST_NOV'])."',',',''), 
        COST_DEC = REPLACE('".addslashes($summary['COST_DEC'])."',',',''), 
        COST_SETAHUN = REPLACE('".addslashes($summary['COST_SETAHUN'])."',',',''),
        FLAG_TEMP = NULL, 
        MATURITY_STAGE_SMS1='TM', 
        MATURITY_STAGE_SMS2='TM',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE='".addslashes($row['TRX_RKT_CODE'])."';
    ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung total cost
  public function calTotalCostKasSan($row = array())
  { 
    $result = true;
    
    //hitung rotasi
    $sebaran_rotasi = $this->updateTempKastrasi($row);    
    $row['PLAN_JAN'] = $sebaran_rotasi[1];
    $row['PLAN_FEB'] = $sebaran_rotasi[2];
    $row['PLAN_MAR'] = $sebaran_rotasi[3];
    $row['PLAN_APR'] = $sebaran_rotasi[4];
    $row['PLAN_MAY'] = $sebaran_rotasi[5];
    $row['PLAN_JUN'] = $sebaran_rotasi[6];
    $row['PLAN_JUL'] = $sebaran_rotasi[7];
    $row['PLAN_AUG'] = $sebaran_rotasi[8];
    $row['PLAN_SEP'] = $sebaran_rotasi[9];
    $row['PLAN_OCT'] = $sebaran_rotasi[10];
    $row['PLAN_NOV'] = $sebaran_rotasi[11];
    $row['PLAN_DEC'] = $sebaran_rotasi[12];
    $row['TOTAL_PLAN_SMS1'] = $row['PLAN_JAN'] + $row['PLAN_FEB'] + $row['PLAN_MAR'] + $row['PLAN_APR'] + $row['PLAN_MAY'] + $row['PLAN_JUN'];
    $row['TOTAL_PLAN_SMS2'] = $row['PLAN_JUL'] + $row['PLAN_AUG'] + $row['PLAN_SEP'] + $row['PLAN_OCT'] + $row['PLAN_NOV'] + $row['PLAN_DEC'];
    $row['TOTAL_PLAN_SETAHUN'] = $row['TOTAL_PLAN_SMS1'] + $row['TOTAL_PLAN_SMS2'];
    
    //cari summary total cost
    $sql = "
      SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, 
           SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, 
           SUM(DIS_JAN) DIS_JAN, 
           SUM(DIS_FEB) DIS_FEB, 
           SUM(DIS_MAR) DIS_MAR, 
           SUM(DIS_APR) DIS_APR, 
           SUM(DIS_MAY) DIS_MAY, 
           SUM(DIS_JUN) DIS_JUN, 
           SUM(DIS_JUL) DIS_JUL, 
           SUM(DIS_AUG) DIS_AUG, 
           SUM(DIS_SEP) DIS_SEP, 
           SUM(DIS_OCT) DIS_OCT, 
           SUM(DIS_NOV) DIS_NOV, 
           SUM(DIS_DEC) DIS_DEC, 
           SUM(DIS_SETAHUN) DIS_SETAHUN, 
           MAX(ROTASI_SMS1) ROTASI_SMS1, 
           MAX(ROTASI_SMS2) ROTASI_SMS2,  
           MAX(PLAN_SMS1) PLAN_SMS1,  
           MAX(PLAN_SMS2) PLAN_SMS2, 
           MAX(PLAN_SETAHUN) PLAN_SETAHUN, 
           SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, 
           SUM(COST_SMS1) COST_SMS1, 
           SUM(COST_SMS2) COST_SMS2
      FROM TR_RKT_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
    ";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
    $sql = "
      UPDATE TR_RKT
      SET PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($row['TOTAL_PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($row['TOTAL_PLAN_SETAHUN'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($summary['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($summary['ROTASI_SMS2'])."',',',''),
        TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
        TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
        COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
        COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
        COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
        COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
        COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
        COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
        COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
        COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
        COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
        COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
        COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
        COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''),
        TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''),
        FLAG_TEMP = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung total cost
  public function calTotalCostRawatSisip($row = array())
  { 
    $result = true;
    
    //cari summary total cost
    $sql = "
      SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, 
           SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, 
           SUM(DIS_JAN) DIS_JAN, 
           SUM(DIS_FEB) DIS_FEB, 
           SUM(DIS_MAR) DIS_MAR, 
           SUM(DIS_APR) DIS_APR, 
           SUM(DIS_MAY) DIS_MAY, 
           SUM(DIS_JUN) DIS_JUN, 
           SUM(DIS_JUL) DIS_JUL, 
           SUM(DIS_AUG) DIS_AUG, 
           SUM(DIS_SEP) DIS_SEP, 
           SUM(DIS_OCT) DIS_OCT, 
           SUM(DIS_NOV) DIS_NOV, 
           SUM(DIS_DEC) DIS_DEC, 
           SUM(DIS_SETAHUN) DIS_SETAHUN, 
           MAX(ROTASI_SMS1) ROTASI_SMS1, 
           MAX(ROTASI_SMS2) ROTASI_SMS2,  
           MAX(PLAN_SMS1) PLAN_SMS1,  
           MAX(PLAN_SMS2) PLAN_SMS2, 
           MAX(PLAN_SETAHUN) PLAN_SETAHUN, 
           SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, 
           SUM(COST_SMS1) COST_SMS1, 
           SUM(COST_SMS2) COST_SMS2
      FROM TR_RKT_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'
    ";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
    $sql = "
      UPDATE TR_RKT
      SET AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),
        ROTASI_SMS1 = REPLACE('".addslashes($summary['ROTASI_SMS1'])."',',',''),
        ROTASI_SMS2 = REPLACE('".addslashes($summary['ROTASI_SMS2'])."',',',''),
        TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''),
        TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
        TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
        PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''),
        PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''),
        PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''),
        COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
        COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
        COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
        COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
        COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
        COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
        COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
        COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
        COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
        COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
        COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
        COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
        COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''),
        COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''),
        TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''),
        FLAG_TEMP = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";

    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    return $result;
  }

  //hitung total cost
  public function calTotalCostManualInfra($row = array())
  { 
      $result = true;
  
  //jika sumber biaya internal, maka tipe norma umum - SABRINA 30/08/2014
  $row['TIPE_NORMA'] = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? 'UMUM' : $row['TIPE_NORMA'];
  
  //cari summary total cost
  $sql = "
    SELECT SUM(RP_ROTASI_SMS1) RP_ROTASI_SMS1, SUM(RP_ROTASI_SMS2) RP_ROTASI_SMS2, SUM(DIS_JAN) DIS_JAN, SUM(DIS_FEB) DIS_FEB, SUM(DIS_MAR) 
         DIS_MAR, SUM(DIS_APR) DIS_APR, SUM(DIS_MAY) DIS_MAY, SUM(DIS_JUN) DIS_JUN, SUM(DIS_JUL) DIS_JUL, SUM(DIS_AUG) DIS_AUG, SUM(DIS_SEP) DIS_SEP, 
         SUM(DIS_OCT) DIS_OCT, SUM(DIS_NOV) DIS_NOV, SUM(DIS_DEC) DIS_DEC, SUM(DIS_SETAHUN) DIS_SETAHUN, MAX(ROTASI_SMS1) ROTASI_SMS1, 
         MAX(ROTASI_SMS2) ROTASI_SMS2, MAX(PLAN_SETAHUN) PLAN_SETAHUN, SUM(TOTAL_RP_QTY) TOTAL_RP_QTY, SUM(COST_SMS1) COST_SMS1, SUM(COST_SMS2) COST_SMS2,
         MAX(PLAN_SMS1) PLAN_SMS1, MAX(PLAN_SMS2) PLAN_SMS2
    FROM TR_RKT_COST_ELEMENT
    WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$row['PERIOD_BUDGET']."'
      AND BA_CODE = '".addslashes($row['BA_CODE'])."'
      AND AFD_CODE = '".addslashes($row['AFD_CODE'])."'
      AND BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."'
      AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
      AND TIPE_TRANSAKSI = 'MANUAL_INFRA'
  ";
  $summary = $this->_db->fetchRow($sql);
  
  //simpan total cost
  $sql = "UPDATE TR_RKT
      SET 
        AFD_CODE = '".addslashes($row['AFD_CODE'])."', BLOCK_CODE = '".addslashes($row['BLOCK_CODE'])."', 
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."', ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."', 
        PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''), 
        PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''), 
        PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''), 
        PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''), 
        PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''), 
        PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''), 
        PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''), 
        PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''), 
        PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''), 
        PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''), 
        PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''), 
        PLAN_SMS1 = REPLACE('".addslashes($summary['PLAN_SMS1'])."',',',''), 
        PLAN_SMS2 = REPLACE('".addslashes($summary['PLAN_SMS2'])."',',',''), 
        PLAN_SETAHUN = REPLACE('".addslashes($summary['PLAN_SETAHUN'])."',',',''), 
        TOTAL_RP_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''), FLAG_TEMP = NULL,
        COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''), COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''), 
        COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''), COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''), 
        COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''), COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''), 
        COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''), COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''), 
        COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''), COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''), 
        COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''), COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''), 
        TOTAL_RP_SETAHUN = REPLACE('".addslashes($summary['DIS_SETAHUN'])."',',',''), COST_SMS1 = REPLACE('".addslashes($summary['COST_SMS1'])."',',',''), 
        COST_SMS2 = REPLACE('".addslashes($summary['COST_SMS2'])."',',',''), 
        MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."', MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        ROTASI_SMS1 = '".addslashes($summary['ROTASI_SMS1'])."', ROTASI_SMS2 = '".addslashes($summary['ROTASI_SMS2'])."', 
        TOTAL_RP_SMS1 = REPLACE('".addslashes($summary['RP_ROTASI_SMS1'])."',',',''), TOTAL_RP_SMS2 = REPLACE('".addslashes($summary['RP_ROTASI_SMS2'])."',',',''),
        UPDATE_USER = '{$this->_userName}', UPDATE_TIME = SYSDATE,
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."' -- //<!-- TIPE NORMA -->
      WHERE
        TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
      ";

  //create sql file
  $this->_global->createSqlFile($row['filename'], $sql);
    
      return $result;
  }
  
  //hitung total cost
  public function calTotalCostPerkerasanJalan($row = array())
    { 
        $result = true;
    
    //tipe norma
    $row['TIPE_NORMA'] = ($row['TIPE_NORMA']) ? $row['TIPE_NORMA'] : 'UMUM'; 
    
    //cari summary total cost
    $sql = "
      SELECT SUM (TOTAL_RP_QTY) TOTAL_RP_QTY,
           SUM (DIS_JAN) DIS_JAN,
           SUM (DIS_FEB) DIS_FEB,
           SUM (DIS_MAR) DIS_MAR,
           SUM (DIS_APR) DIS_APR,
           SUM (DIS_MAY) DIS_MAY,
           SUM (DIS_JUN) DIS_JUN,
           SUM (DIS_JUL) DIS_JUL,
           SUM (DIS_AUG) DIS_AUG,
           SUM (DIS_SEP) DIS_SEP,
           SUM (DIS_OCT) DIS_OCT,
           SUM (DIS_NOV) DIS_NOV,
           SUM (DIS_DEC) DIS_DEC
      FROM TR_RKT_PK_COST_ELEMENT
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."'";
    $summary = $this->_db->fetchRow($sql);
    
    //simpan total cost
      $biaya = $summary['DIS_JAN'] + $summary['DIS_FEB'] + $summary['DIS_MAR'] + $summary['DIS_APR'] + $summary['DIS_MAY'] + $summary['DIS_JUN'] + $summary['DIS_JUL'] + $summary['DIS_AUG'] + $summary['DIS_SEP'] + $summary['DIS_OCT'] + $summary['DIS_NOV'] + $summary['DIS_DEC'];
      $plan_setahun = $row['PLAN_JAN']+$row['PLAN_FEB']+$row['PLAN_MAR']+$row['PLAN_APR']+
                $row['PLAN_MAY']+$row['PLAN_JUN']+$row['PLAN_JUL']+$row['PLAN_AUG']+
                $row['PLAN_SEP']+$row['PLAN_OCT']+$row['PLAN_NOV']+$row['PLAN_DEC'];
      $sql = "UPDATE TR_RKT_PK 
          SET 
            MATURITY_STAGE_SMS1 = '".addslashes($row['SEMESTER1'])."',
            MATURITY_STAGE_SMS2 = '".addslashes($row['SEMESTER2'])."',
            PRICE_QTY = REPLACE('".addslashes($summary['TOTAL_RP_QTY'])."',',',''),
            AKTUAL_JALAN = '".addslashes($row['AKTUAL_JALAN'])."',
            AKTUAL_PERKERASAN_JALAN = '".addslashes($row['AKTUAL_PERKERASAN_JALAN'])."',
            JENIS_PEKERJAAN = '".addslashes($row['JENIS_PEKERJAAN'])."',
            JARAK = '".addslashes($row['JARAK'])."',
            PLAN_JAN = REPLACE('".addslashes($row['PLAN_JAN'])."',',',''),
            PLAN_FEB = REPLACE('".addslashes($row['PLAN_FEB'])."',',',''),
            PLAN_MAR = REPLACE('".addslashes($row['PLAN_MAR'])."',',',''),
            PLAN_APR = REPLACE('".addslashes($row['PLAN_APR'])."',',',''),
            PLAN_MAY = REPLACE('".addslashes($row['PLAN_MAY'])."',',',''),
            PLAN_JUN = REPLACE('".addslashes($row['PLAN_JUN'])."',',',''),
            PLAN_JUL = REPLACE('".addslashes($row['PLAN_JUL'])."',',',''),
            PLAN_AUG = REPLACE('".addslashes($row['PLAN_AUG'])."',',',''),
            PLAN_SEP = REPLACE('".addslashes($row['PLAN_SEP'])."',',',''), 
            PLAN_OCT = REPLACE('".addslashes($row['PLAN_OCT'])."',',',''),
            PLAN_NOV = REPLACE('".addslashes($row['PLAN_NOV'])."',',',''),
            PLAN_DEC = REPLACE('".addslashes($row['PLAN_DEC'])."',',',''),        
            PLAN_SETAHUN = '".addslashes($plan_setahun)."',
            COST_JAN = REPLACE('".addslashes($summary['DIS_JAN'])."',',',''),
            COST_FEB = REPLACE('".addslashes($summary['DIS_FEB'])."',',',''),
            COST_MAR = REPLACE('".addslashes($summary['DIS_MAR'])."',',',''),
            COST_APR = REPLACE('".addslashes($summary['DIS_APR'])."',',',''),
            COST_MAY = REPLACE('".addslashes($summary['DIS_MAY'])."',',',''),
            COST_JUN = REPLACE('".addslashes($summary['DIS_JUN'])."',',',''),
            COST_JUL = REPLACE('".addslashes($summary['DIS_JUL'])."',',',''),
            COST_AUG = REPLACE('".addslashes($summary['DIS_AUG'])."',',',''),
            COST_SEP = REPLACE('".addslashes($summary['DIS_SEP'])."',',',''),
            COST_OCT = REPLACE('".addslashes($summary['DIS_OCT'])."',',',''),
            COST_NOV = REPLACE('".addslashes($summary['DIS_NOV'])."',',',''),
            COST_DEC = REPLACE('".addslashes($summary['DIS_DEC'])."',',',''),
            TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
            COST_SETAHUN = '".addslashes($biaya)."',
            UPDATE_USER = '{$this->_userName}',
            UPDATE_TIME = SYSDATE,
            FLAG_TEMP = NULL
            WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
          ";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
    }


  //get activity class dari norma biaya
  public function getActivityClass($params = array())
  {
    $value = array();
    $query = "
      SELECT DISTINCT NILAI
      FROM (
        SELECT ACTIVITY_CLASS NILAI
        FROM TN_BIAYA
        WHERE DELETE_USER IS NULL
          AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$params['budgetperiod']}'
          AND BA_CODE = '".$params['key_find']."'
          AND ACTIVITY_CODE LIKE '%".$params['src_activity_code']."%'
          AND COST_ELEMENT = 'LABOUR'
        UNION
        SELECT ACTIVITY_CLASS NILAI
        FROM TN_HARGA_BORONG
        WHERE DELETE_USER IS NULL
          AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$params['budgetperiod']}'
          AND BA_CODE = '".$params['key_find']."'
          AND ACTIVITY_CODE LIKE '%".$params['src_activity_code']."%'
      )       
    ";
    $sql = "SELECT COUNT(*) FROM ({$query})";
    $value['count'] = $this->_db->fetchOne($sql);
         
    $rows = $this->_db->fetchAll($query);
    if (!empty($rows)) {      
      foreach ($rows as $idx => $row) {
        if ($row['NILAI'] == 'ALL') {
          $value['rows'] = '';
          $value['rows'][] = $row;
          break;
        }else{
          $value['rows'][] = $row;
        }
      }
    }
    return $value;
  }
  
  //get activity opsi
  public function getActivityOpsi($params = array())
  {
    $result = array();
    $sql = "SELECT ACTIVITY_CODE KODE, DESCRIPTION NILAI FROM TM_ACTIVITY WHERE DELETE_USER IS NULL AND ACTIVITY_PARENT_CODE = '".$params['src_activity_code']."'";
    $rows = $this->_db->fetchAll($sql);
    
    if (!empty($rows)) {      
      foreach ($rows as $idx => $row) {
          $result['rows'][] = $row;
      }
    }
    $result['count'] = count($rows);
    return $result;
  }
  
  //reset perhitungan
  public function saveTempTanam($row = array())
  { 
    $sebaran_rotasi = $this->_formula->cal_SebaranRotasiRawat($row);
    
    $sql = "
      UPDATE TR_RKT_COST_ELEMENT
      SET MATURITY_STAGE_SMS1 = '".addslashes($row['MATURITY_STAGE_SMS1'])."',
        MATURITY_STAGE_SMS2 = '".addslashes($row['MATURITY_STAGE_SMS2'])."',
        AWAL_ROTASI = REPLACE('".addslashes($row['AWAL_ROTASI'])."',',',''),
        ACTIVITY_CLASS = '".addslashes($row['ACTIVITY_CLASS'])."',
        SUMBER_BIAYA = '".addslashes($row['SUMBER_BIAYA'])."',
        TIPE_NORMA = '".addslashes($row['TIPE_NORMA'])."', -- //<!-- TIPE NORMA -->
        RP_ROTASI_SMS1 = NULL,
        RP_ROTASI_SMS2 = NULL,
        TOTAL_RP_QTY = NULL,
        ROTASI_SMS1 = NULL,
        ROTASI_SMS2 = NULL,
        PLAN_JAN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JAN'])."',',',''),
        PLAN_FEB = REPLACE('".addslashes($sebaran_rotasi['PLAN_FEB'])."',',',''),
        PLAN_MAR = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAR'])."',',',''),
        PLAN_APR = REPLACE('".addslashes($sebaran_rotasi['PLAN_APR'])."',',',''),
        PLAN_MAY = REPLACE('".addslashes($sebaran_rotasi['PLAN_MAY'])."',',',''),
        PLAN_JUN = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUN'])."',',',''),
        PLAN_JUL = REPLACE('".addslashes($sebaran_rotasi['PLAN_JUL'])."',',',''),
        PLAN_AUG = REPLACE('".addslashes($sebaran_rotasi['PLAN_AUG'])."',',',''),
        PLAN_SEP = REPLACE('".addslashes($sebaran_rotasi['PLAN_SEP'])."',',',''), 
        PLAN_OCT = REPLACE('".addslashes($sebaran_rotasi['PLAN_OCT'])."',',',''),
        PLAN_NOV = REPLACE('".addslashes($sebaran_rotasi['PLAN_NOV'])."',',',''),
        PLAN_DEC = REPLACE('".addslashes($sebaran_rotasi['PLAN_DEC'])."',',',''),
        ATRIBUT = '".addslashes($row['ATRIBUT'])."', 
        PLAN_SMS1 = NULL,
        PLAN_SMS2 = NULL,
        PLAN_SETAHUN = NULL,
        BULAN_PENGERJAAN = NULL, 
        DIS_JAN = NULL,
        DIS_FEB = NULL,
        DIS_MAR = NULL,
        DIS_APR = NULL,
        DIS_MAY = NULL,
        DIS_JUN = NULL,
        DIS_JUL = NULL,
        DIS_AUG = NULL,
        DIS_SEP = NULL,
        DIS_OCT = NULL,
        DIS_NOV = NULL,
        DIS_DEC = NULL,
        COST_SMS1 = NULL,
        COST_SMS2 = NULL,
        DIS_SETAHUN = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE TRX_RKT_CODE = '".addslashes($row['TRX_RKT_CODE'])."';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);
  }
  
  //hitung dist VRA Infra
  public function saveDistVra($arrAfdUpd = array()){
    $arrAfdUpd['SUM_ANGKUT'] = $this->getTotalAngkutAfd($arrAfdUpd);
    $arrAfdUpd['SUM_LANGSIR'] = $this->getTotalLangsirAfd($arrAfdUpd);
    $distVraPanenAngkTBS = $this->_formula->get_DistVraPanenAngkTBS($arrAfdUpd);
    $act_code = 51800; //angkut internal saja
    $trxCodeAngkutTbs = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$act_code.'DT010');
    
    $sql="
      SELECT  VRA_CODE, 
          NVL(TON_TRIP,0) AS TONTRIP, 
          NVL(HM_TRIP,0) AS HMTRIP, 
          NVL(RP_HM,0) AS RP_HM
      FROM TN_PANEN_PREMI_LANGSIR
      WHERE PERIOD_BUDGET = TO_DATE ('01-01-{$arrAfdUpd['PERIOD_BUDGET']}', 'DD-MM-RRRR')
        AND BA_CODE = '{$arrAfdUpd['BA_CODE']}' 
        AND DELETE_TIME IS NULL
    ";
    $rows = $this->_db->fetchAll($sql);
    
    //CEK DISTRIBUSI VRA
    $sql = "
      SELECT COUNT (DISTINCT LOCATION_CODE) 
      FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
        AND ACTIVITY_CODE IN ('{$act_code}','51600')
        AND TIPE_TRANSAKSI = 'INFRA'
    ";
    $distribusi = $this->_db->fetchOne($sql);
    
    
    //jika sebelumnya blm ada data, buat kombinasi data baru
    if($distribusi == 0){
      //GET AFDELING PER BA
      $sql = "
        SELECT DISTINCT AFD_CODE AS AFD_CODE 
        FROM TM_HECTARE_STATEMENT
        WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
          AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
      ";
      $afdeling = $this->_db->fetchAll($sql);
      $sql = "";
      foreach($afdeling as $idx => $nilai){
        //INSERT KOMBINASI ANGKUT TBS
        $sql .= "
          INSERT INTO TR_RKT_VRA_DISTRIBUSI(
            PERIOD_BUDGET, 
            BA_CODE, 
            ACTIVITY_CODE, 
            VRA_CODE, 
            LOCATION_CODE, 
            HM_KM, 
            PRICE_QTY_VRA, 
            PRICE_HM_KM, 
            TRX_CODE, 
            TIPE_TRANSAKSI, 
            INSERT_USER, 
            INSERT_TIME
          )
          VALUES(
            TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
            '".addslashes($arrAfdUpd['BA_CODE'])."', 
            '{$act_code}', 
            'DT010',
            '".addslashes($nilai['AFD_CODE'])."', 
            '0', 
            '0', 
            '0',
            '{$trxCodeAngkutTbs}', 
            'INFRA', 
            '{$this->_userName}', 
            SYSDATE
          );
        ";
        //$this->_db->query($sql);
        //$this->_db->commit();
        }
      
      foreach ($rows as $idx => $roww){       
        foreach($afdeling as $idx => $value){           
          $trxLangsir = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].'51600'.$roww['VRA_CODE']);
          
          //INSERT KOMBINASI LANGSIR TBS
          $sql .= "
            INSERT INTO TR_RKT_VRA_DISTRIBUSI(
              PERIOD_BUDGET, 
              BA_CODE, 
              ACTIVITY_CODE, 
              VRA_CODE, 
              LOCATION_CODE, 
              HM_KM, 
              PRICE_QTY_VRA, 
              PRICE_HM_KM, 
              TRX_CODE, 
              TIPE_TRANSAKSI, 
              INSERT_USER, 
              INSERT_TIME)
            VALUES(
              TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
              '".addslashes($arrAfdUpd['BA_CODE'])."', 
              '51600', 
              '{$roww['VRA_CODE']}',
              '".addslashes($value['AFD_CODE'])."', 
              '0', 
              '0', 
              '0',
              '{$trxLangsir}', 
              'INFRA', 
              '{$this->_userName}', 
              SYSDATE
            );
          "; 
              
          //$this->_db->query($sql);
          //$this->_db->commit();
        }
      }
    }
    
    //DELETE RKT VRA DISTRIBUSI
    $sql = "
      DELETE FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
        AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
        AND ACTIVITY_CODE IN ('{$act_code}','51600')
        AND LOCATION_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."';
    ";
    
    //INSERT ANGKUT TBS
    $sql .= "
      INSERT INTO TR_RKT_VRA_DISTRIBUSI(
        PERIOD_BUDGET, 
        BA_CODE, 
        ACTIVITY_CODE, 
        VRA_CODE, 
        LOCATION_CODE, 
        HM_KM, 
        PRICE_QTY_VRA, 
        PRICE_HM_KM, 
        TRX_CODE, 
        TIPE_TRANSAKSI, 
        INSERT_USER, 
        INSERT_TIME
      )
      VALUES(
        TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
        '".addslashes($arrAfdUpd['BA_CODE'])."', 
        '{$act_code}', 
        'DT010',
        '".addslashes($arrAfdUpd['AFD_CODE'])."', 
        {$distVraPanenAngkTBS[0]}, 
        {$distVraPanenAngkTBS[1]}, 
        {$arrAfdUpd['SUM_ANGKUT']},
        '{$trxCodeAngkutTbs}', 
        'INFRA', 
        '{$this->_userName}', 
        SYSDATE
      );
    ";
    
    //INSERT LANGSIR TBS
    foreach ($rows as $idx => $rowx) {
      $harga = $rowx['RP_HM'];
      $distVraPanenLangsir = $this->_formula->get_DistVraPanenLangsir($arrAfdUpd['SUM_LANGSIR'],$rowx);
      $jumlah = $harga * $distVraPanenLangsir[0];
      $trxCodeLangsir = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].'51600'.$rowx['VRA_CODE']);
        
      $sql .= "
        INSERT INTO TR_RKT_VRA_DISTRIBUSI(
          PERIOD_BUDGET, 
          BA_CODE, 
          ACTIVITY_CODE, 
          VRA_CODE, 
          LOCATION_CODE, 
          HM_KM, 
          PRICE_QTY_VRA, 
          PRICE_HM_KM, 
          TRX_CODE, 
          TIPE_TRANSAKSI, 
          INSERT_USER, 
          INSERT_TIME
        )
        VALUES(
          TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
          '".addslashes($arrAfdUpd['BA_CODE'])."', 
          '51600', 
          '{$rowx['VRA_CODE']}',
          '".addslashes($arrAfdUpd['AFD_CODE'])."', 
          {$distVraPanenLangsir[0]}, 
          {$harga}, 
          {$jumlah},
          '{$trxCodeLangsir}', 
          'INFRA', 
          '{$this->_userName}', 
          SYSDATE
        );
      ";
      
    }
    
    //create sql file
    $this->_global->createSqlFile($arrAfdUpd['filename'], $sql);
    return true;
  }
  
  public function saveDistVraPerkerasanJalan($arrAfdUpd = array()){
    $result=true;
    $lastBa = $lastAfd = $lastActivity = '';
    
    $sql="
      SELECT VRA_CODE_DT, VRA_CODE_EXCAV, VRA_CODE_COMPACTOR, VRA_CODE_GRADER
      FROM TN_PERKERASAN_JALAN 
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
        AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
    ";        
    $rows = $this->_db->fetchAll($sql);
    
    //CEK DISTRIBUSI VRA
    $sql = "
      SELECT COUNT (DISTINCT LOCATION_CODE) 
      FROM TR_RKT_VRA_DISTRIBUSI
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
        AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
        AND TIPE_TRANSAKSI = 'INFRA'
    ";
    $distribusi = $this->_db->fetchOne($sql);
    
    //jika sebelumnya blm ada data, buat kombinasi data baru
    if($distribusi == 0){
      //die($sql);
      foreach ($rows as $idx => $roww){
        $arrPKDistVra1 = array( $arrAfdUpd['ACTIVITY_CODE'], $roww['VRA_CODE_DT'], $roww['VRA_CODE_EXCAV'], 
        $roww['VRA_CODE_COMPACTOR'], $roww['VRA_CODE_GRADER'], $arrAfdUpd['BA_CODE']);

        //GET AFDELING PER BA
        $sql = "
          SELECT DISTINCT AFD_CODE AS AFD_CODE 
          FROM TM_HECTARE_STATEMENT
          WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
            AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."'
        ";
        $afdeling = $this->_db->fetchAll($sql);
        
        foreach($afdeling as $idx => $value) {
          for($i=1;$i<5;$i++){
            $arrAfdUpd1['TRX_CODE'] = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$arrPKDistVra1[0].$arrPKDistVra1[$i]);
            $sql = "
              INSERT INTO TR_RKT_VRA_DISTRIBUSI(
                PERIOD_BUDGET, 
                BA_CODE, 
                ACTIVITY_CODE, 
                VRA_CODE, 
                LOCATION_CODE, 
                HM_KM, 
                PRICE_QTY_VRA, 
                PRICE_HM_KM, 
                TRX_CODE, 
                TIPE_TRANSAKSI, 
                INSERT_USER, 
                INSERT_TIME
              )
              VALUES(
                TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
                '".addslashes($arrAfdUpd['BA_CODE'])."', 
                '{$arrPKDistVra1[0]}', 
                '{$arrPKDistVra1[$i]}',
                '".addslashes($value['AFD_CODE'])."', 
                '0', 
                '0', 
                '0',
                '{$arrAfdUpd1['TRX_CODE']}', 
                'INFRA', 
                '{$this->_userName}', 
                SYSDATE
              )
            ";
            $this->_db->query($sql);
            $this->_db->commit();
          }
        }
      }
    } 
    //print_r($rows);die();
    foreach ($rows as $idx => $row) {
      //GET PLAN STAUN AFD PK JALAN
      // YUS 18/11/2014 
      $sql = "
        SELECT JARAK, SUM(PLAN_JAN
         + PLAN_FEB
         + PLAN_MAR
         + PLAN_APR
         + PLAN_MAY
         + PLAN_JUN
         + PLAN_JUL
         + PLAN_AUG
         + PLAN_SEP
         + PLAN_OCT
         + PLAN_NOV
         + PLAN_DEC) PLAN_SETAHUN
        FROM TR_RKT_PK 
        WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
          AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
          AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."' 
          AND AFD_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."'
          AND JARAK IS NOT NULL
        GROUP BY JARAK
      ";          
        //die($sql);
      $rows1 = $this->_db->fetchAll($sql);
      
      if (!empty($rows1)) {
        foreach ($rows1 as $idx1 => $row1) {
          $arrAfdUpd['RANGE_JARAK'] = $row1['JARAK'];         
          $distVraPK = $this->_formula->get_DistVraPK($arrAfdUpd);  //CARI RP HM ELEMENT VRA
          
          //data DT
          $arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_DT'];
          $vra_code['DT'] = $row['VRA_CODE_DT'];
          $rp_qty['DT'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
          $arrHmKm['DT'] = ($rp_qty['DT']) ? ($distVraPK['DT'] / $rp_qty['DT']) : 0;
          $hm_km['DT'] += ($arrHmKm['DT'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          $price_hm_km['DT'] += ($rp_qty['DT'] * $arrHmKm['DT'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          
          //data EXCAV
          $arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_EXCAV'];
          $vra_code['EXCAV'] = $row['VRA_CODE_EXCAV'];
          $rp_qty['EXCAV'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
          $arrHmKm['EXCAV'] = $distVraPK['EXCAV'];
          $hm_km['EXCAV'] += ($arrHmKm['EXCAV'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          $price_hm_km['EXCAV'] += ($rp_qty['EXCAV'] * $arrHmKm['EXCAV'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          
          //data COMPACTOR
          $arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_COMPACTOR'];
          $vra_code['COMPACTOR'] = $row['VRA_CODE_COMPACTOR'];
          $rp_qty['COMPACTOR'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
          $arrHmKm['COMPACTOR'] = $distVraPK['COMPACTOR'];
          $hm_km['COMPACTOR'] += ($arrHmKm['COMPACTOR'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          $price_hm_km['COMPACTOR'] += ($rp_qty['COMPACTOR'] * $arrHmKm['COMPACTOR'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          
          //data GRADER
          $arrAfdUpd['VRA_CODE'] = $row['VRA_CODE_GRADER'];
          $vra_code['GRADER'] = $row['VRA_CODE_GRADER'];
          $rp_qty['GRADER'] = $this->_formula->get_ValueVraPK($arrAfdUpd);
          $arrHmKm['GRADER'] = $distVraPK['GRADER'];
          $hm_km['GRADER'] += ($arrHmKm['GRADER'] * $row1['PLAN_SETAHUN'] / 1000) ; 
          $price_hm_km['GRADER'] += ($rp_qty['GRADER'] * $arrHmKm['GRADER'] * $row1['PLAN_SETAHUN'] / 1000) ; 
        }
      }
      //print_r($hm_km);
      
      //DELETE RKT VRA DISTRIBUSI
      $sql = "
        DELETE FROM TR_RKT_VRA_DISTRIBUSI
        WHERE PERIOD_BUDGET = TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR')
          AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
          AND ACTIVITY_CODE = '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."'
          AND LOCATION_CODE = '".addslashes($arrAfdUpd['AFD_CODE'])."';
      ";
    
      //insert masing2 vra
      for($i=1;$i<5;$i++){
        switch($i){
          case 1 : $vra = 'DT'; break;
          case 2 : $vra = 'EXCAV'; break;
          case 3 : $vra = 'COMPACTOR'; break;
          case 4 : $vra = 'GRADER'; break;
        }
        
        $trxCode = ($arrAfdUpd['PERIOD_BUDGET'].$arrAfdUpd['BA_CODE'].$arrAfdUpd['ACTIVITY_CODE'].$vra_code[$vra]);
        
        $sql .= "
          INSERT INTO TR_RKT_VRA_DISTRIBUSI(
            PERIOD_BUDGET, 
            BA_CODE, 
            ACTIVITY_CODE, 
            VRA_CODE, 
            LOCATION_CODE, 
            HM_KM, 
            PRICE_QTY_VRA, 
            PRICE_HM_KM, 
            TRX_CODE, 
            TIPE_TRANSAKSI, 
            INSERT_USER, 
            INSERT_TIME)
          VALUES(
            TO_DATE('01-01-{$arrAfdUpd['PERIOD_BUDGET']}','DD-MM-RRRR'),
            '".addslashes($arrAfdUpd['BA_CODE'])."', 
            '".addslashes($arrAfdUpd['ACTIVITY_CODE'])."', 
            '".addslashes($vra_code[$vra])."', 
            '".addslashes($arrAfdUpd['AFD_CODE'])."', 
            '".$hm_km[$vra]."', 
            '".$rp_qty[$vra]."', 
            '".$price_hm_km[$vra]."', 
            '".$trxCode."', 
            'INFRA', 
            '{$this->_userName}', 
            SYSDATE
          );
        ";
      }
    }
    
    //create sql file
    $this->_global->createSqlFile($arrAfdUpd['filename'], $sql);
    return true;
  }

  //get total cost angkut TBS per AFD
  public function getTotalAngkutAfd($params = array()){
    $sql="
      SELECT SUM(ANGKUT_TBS_RP_ANGKUT) 
      FROM TR_RKT_PANEN
      WHERE DELETE_USER IS NULL
        AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '{$params['BA_CODE']}'
        AND AFD_CODE = '{$params['AFD_CODE']}' 
        AND SUMBER_BIAYA_UNIT = 'INTERNAL'
    ";
    $result = $this->_db->fetchOne($sql);
    
    return ($result) ? $result : 0;
  }
  
  //get total cost langsir TBS per AFD
  public function getTotalLangsirAfd($params = array()){
    $sql="
      SELECT SUM(LANGSIR_TON)
      FROM TR_RKT_PANEN
      WHERE DELETE_USER IS NULL
        AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '{$params['BA_CODE']}'
        AND AFD_CODE = '{$params['AFD_CODE']}' 
        AND SUMBER_BIAYA_UNIT = 'INTERNAL'
    ";
    $result = $this->_db->fetchOne($sql);
    return ($result) ? $result : 0;
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

