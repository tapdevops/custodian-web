<?php
/*
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Model Class untuk RKT Pupuk HA
Function      : - getData     : ambil data dari DB
            - getList     : menampilkan list RKT Pupuk HA
            - save        : simpan data
            - delete      : hapus data
            - getInput      : setting input untuk region dan maturity stage
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada
Developer     :   Yopie Irawan
Dibuat Tanggal    :   22/07/2013
Update Terakhir   : 22/07/2013
Revisi        : 
=========================================================================================================================
*/
class Application_Model_RktPupukKgSisip
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
           SUM(rkt.DIS_JAN) JAN, 
           SUM(rkt.DIS_FEB) FEB, 
           SUM(rkt.DIS_MAR) MAR, 
           SUM(rkt.DIS_APR) APR, 
           SUM(rkt.DIS_MAY) MAY, 
           SUM(rkt.DIS_JUN) JUN, 
           SUM(rkt.DIS_JUL) JUL, 
           SUM(rkt.DIS_AUG) AUG, 
           SUM(rkt.DIS_SEP) SEP, 
           SUM(rkt.DIS_OCT) OCT, 
           SUM(rkt.DIS_NOV) NOV, 
           SUM(rkt.DIS_DEC) DEC, 
           SUM(rkt.DIS_TOTAL) TOTAL,
           MAX(MATERIAL_NAME_JAN) PUPUK_JAN,
           MAX(MATERIAL_NAME_FEB) PUPUK_FEB,
           MAX(MATERIAL_NAME_MAR) PUPUK_MAR,
           MAX(MATERIAL_NAME_APR) PUPUK_APR,
           MAX(MATERIAL_NAME_MAY) PUPUK_MAY,
           MAX(MATERIAL_NAME_JUN) PUPUK_JUN,
           MAX(MATERIAL_NAME_JUL) PUPUK_JUL,
           MAX(MATERIAL_NAME_AUG) PUPUK_AUG,
           MAX(MATERIAL_NAME_SEP) PUPUK_SEP,
           MAX(MATERIAL_NAME_OCT) PUPUK_OCT,
           MAX(MATERIAL_NAME_NOV) PUPUK_NOV,
           MAX(MATERIAL_NAME_DEC) PUPUK_DEC 
      FROM TR_RKT_PUPUK_DISTRIBUSI rkt 
      LEFT JOIN TM_HECTARE_STATEMENT ha_statement 
        ON rkt.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET 
        AND rkt.BA_CODE = ha_statement.BA_CODE 
        AND rkt.AFD_CODE = ha_statement.AFD_CODE 
        AND rkt.BLOCK_CODE = ha_statement.BLOCK_CODE 
        -- AND (rkt.TIPE_TRANSAKSI = 'POKOK_SISIP' OR rkt.TIPE_TRANSAKSI = 'KG_SISIP')
        AND rkt.TIPE_TRANSAKSI = 'KG_SISIP'
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
                LISTAGG (MATERIAL_NAME_DEC, ' + ') WITHIN GROUP (ORDER BY PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE) AS MATERIAL_NAME_DEC
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
                  TM_DEC.MATERIAL_NAME AS MATERIAL_NAME_DEC 
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
          UPPER(rkt.MATERIAL_CODE_JAN) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_FEB) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_MAR) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_APR) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_MAY) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_JUN) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_JUL) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_AUG) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_SEP) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_OCT) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_NOV) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
          OR UPPER(rkt.MATERIAL_CODE_DEC) LIKE UPPER('%".$params['src_jenis_pupuk']."%')
        )
      ";
    }
    
    $query .= "
      GROUP BY to_char(ha_statement.PERIOD_BUDGET,'RRRR'), ha_statement.BA_CODE, ORG.COMPANY_NAME, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE,    
           ha_statement.BLOCK_DESC, ha_statement.LAND_TYPE, ha_statement.TOPOGRAPHY, to_char(ha_statement.TAHUN_TANAM,'MM.RRRR'),
           to_char(ha_statement.TAHUN_TANAM,'MM') , 
           to_char(ha_statement.TAHUN_TANAM,'RRRR') ,  ha_statement.MATURITY_STAGE_SMS1, ha_statement.MATURITY_STAGE_SMS2, ha_statement.HA_PLANTED, ha_statement.POKOK_TANAM, ha_statement.SPH 
      ORDER BY ha_statement.BA_CODE, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE";

    return $query;
  }
  
  //menampilkan list Report Pupuk HA
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
  
  //kalkulasi seluruh data RKT Pupuk KG Sisip
  public function calculateAllItem($params = array())
  {
    $result = true;

    //cari data
    $sql = "
      SELECT DISTINCT BA_CODE, AFD_CODE, BLOCK_CODE, LAND_TYPE, TAHUN_TANAM, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, HA_PLANTED, POKOK_TANAM
      FROM TM_HECTARE_STATEMENT
      WHERE DELETE_USER IS NULL       
    ";
    if($params['PERIOD_BUDGET']){
      $sql .= "AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') ";
      $budget = $params['PERIOD_BUDGET'];
    }
    if($params['budgetperiod']){
      $sql .= "AND PERIOD_BUDGET = TO_DATE('01-01-{$params['budgetperiod']}','DD-MM-RRRR') ";
      $budget = $params['budgetperiod'];
    }
    if($params['key_find']){
      $sql .= "AND BA_CODE = '".$params['key_find']."' ";
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
    if (($params['maturity_stage']) && ($params['maturity_stage'] != 'ALL')) {
      $sql .= "
        AND (
          UPPER(MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['maturity_stage']."%')
          OR UPPER(MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['maturity_stage']."%')
        )
      ";
    }
    $records = $this->_db->fetchAll($sql);
    
    if (!empty($records)) {
      foreach ($records as $index => $record) { 
        $pokok_pupuk = array();
        $mcd_mon = array();
        $dis_mon = array();
        $umur_tanaman_jan = $this->_formula->cal_RktPupuk_SelisihBulan($record['TAHUN_TANAM']);
        
        for ($mBudget = 1 ; $mBudget <= 12 ; $mBudget++){
          $umur_tanaman = $umur_tanaman_jan + $mBudget - 1;
          
          if (($mBudget == 1) || ($mBudget == 7)){
            $maturity_stage = ($mBudget == 1) ? $record['MATURITY_STAGE_SMS1'] : $record['MATURITY_STAGE_SMS2'];
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
            $pokok_pupuk[0][$mBudget] = 0;
            $mcd_mon[0][$mBudget] = '';
            $dis_mon[0][$mBudget] = 0;
          }else{
            //perhitungan untuk norma pupuk TBM 2 Less
            if($jenis_norma_pupuk == 'TN_PUPUK_TBM2_LESS'){
              $pokok_pupuk[0][$mBudget] = 0;
              $mcd_mon[0][$mBudget] = '';
              $dis_mon[0][$mBudget] = 0;
            }
            //perhitungan untuk norma pupuk TBM 2 - TM
            else{
              $sql = "
                SELECT MATERIAL_CODE, NVL(MAX(JUMLAH), 0) JUMLAH, NVL(MAX(POKOK), 0) POKOK
                FROM TN_PUPUK_TBM2_TM
                WHERE DELETE_USER IS NULL
                  AND JENIS_TANAM = 'SISIP' 
                  AND PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
                  AND BA_CODE = '".$record['BA_CODE']."'
                  AND AFD_CODE = '".$record['AFD_CODE']."'
                  AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
                  AND BULAN_PEMUPUKAN = '".$mBudget."'
                GROUP BY MATERIAL_CODE"; 
              $results = $this->_db->fetchAll($sql);
              
              if (!empty($results)) {
                foreach ($results as $idx => $result) {           
                  $pokok_pupuk[$idx][$mBudget] = $result['POKOK'];
                  $mcd_mon[$idx][$mBudget] = $result['MATERIAL_CODE'];
                  $dis_mon[$idx][$mBudget] = $result['JUMLAH'];
                }
              }
            }
          }
        }
        
        //insert ke TR_RKT_PUPUK_DISTRIBUSI
        //hapus data lama
        $sql = "
          DELETE FROM TR_RKT_PUPUK_DISTRIBUSI
          WHERE PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
            AND BA_CODE = '".$record['BA_CODE']."'
            AND AFD_CODE = '".$record['AFD_CODE']."'
            AND BLOCK_CODE = '".$record['BLOCK_CODE']."'  
            AND TIPE_TRANSAKSI IN ('KG_SISIP','POKOK_SISIP');
        ";
        
        if (!empty($pokok_pupuk)) {
          foreach ($pokok_pupuk as $idx => $pokok) {
            //insert distribusi KG sisip
            $sql .= "
              INSERT INTO TR_RKT_PUPUK_DISTRIBUSI (PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TRX_RKT_CODE,  
                MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
                DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, 
                DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, DIS_TOTAL, 
                INSERT_USER, INSERT_TIME, TIPE_TRANSAKSI,
                MATERIAL_CODE_JAN, MATERIAL_CODE_FEB, MATERIAL_CODE_MAR, MATERIAL_CODE_APR, MATERIAL_CODE_MAY, MATERIAL_CODE_JUN, 
                MATERIAL_CODE_JUL, MATERIAL_CODE_AUG, MATERIAL_CODE_SEP, MATERIAL_CODE_OCT, MATERIAL_CODE_NOV, MATERIAL_CODE_DEC
                )
              VALUES (
                TO_DATE('01-01-{$budget}','DD-MM-RRRR'),
                '".addslashes($record['BA_CODE'])."',
                '".addslashes($record['AFD_CODE'])."',
                '".addslashes($record['BLOCK_CODE'])."',
                ".$params['PERIOD_BUDGET']." || '-' || '".addslashes($record['BA_CODE'])."' || '-' || '".addslashes($record['AFD_CODE'])."'
                || '-' || '".addslashes($record['BLOCK_CODE'])."' || '-' || 'RKT009' || '-' || 'KG_SISIP' || '".$this->_global->randomString(5)."',
                '".addslashes($record['MATURITY_STAGE_SMS1'])."', '".addslashes($record['MATURITY_STAGE_SMS2'])."',
                REPLACE('".addslashes($dis_mon[$idx][1])."',',',''), REPLACE('".addslashes($dis_mon[$idx][2])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][3])."',',',''), REPLACE('".addslashes($dis_mon[$idx][4])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][5])."',',',''), REPLACE('".addslashes($dis_mon[$idx][6])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][7])."',',',''), REPLACE('".addslashes($dis_mon[$idx][8])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][9])."',',',''), REPLACE('".addslashes($dis_mon[$idx][10])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][11])."',',',''), REPLACE('".addslashes($dis_mon[$idx][12])."',',',''),REPLACE('".addslashes(array_sum($dis_mon[$idx]))."',',',''),
                '{$this->_userName}',SYSDATE,'KG_SISIP',
                REPLACE('".addslashes($mcd_mon[$idx][1])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][2])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][3])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][4])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][5])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][6])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][7])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][8])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][9])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][10])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][11])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][12])."',',','')
              );
            ";
            
            //insert distribusi pokok sisip
            $sql .= "
              INSERT INTO TR_RKT_PUPUK_DISTRIBUSI (PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TRX_RKT_CODE,  
                MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, 
                DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, 
                DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, DIS_TOTAL, 
                INSERT_USER, INSERT_TIME, TIPE_TRANSAKSI,
                MATERIAL_CODE_JAN, MATERIAL_CODE_FEB, MATERIAL_CODE_MAR, MATERIAL_CODE_APR, MATERIAL_CODE_MAY, MATERIAL_CODE_JUN, 
                MATERIAL_CODE_JUL, MATERIAL_CODE_AUG, MATERIAL_CODE_SEP, MATERIAL_CODE_OCT, MATERIAL_CODE_NOV, MATERIAL_CODE_DEC
                )
              VALUES (
                TO_DATE('01-01-{$budget}','DD-MM-RRRR'),
                '".addslashes($record['BA_CODE'])."',
                '".addslashes($record['AFD_CODE'])."',
                '".addslashes($record['BLOCK_CODE'])."',
                ".$params['PERIOD_BUDGET']." || '-' || '".addslashes($record['BA_CODE'])."' || '-' || '".addslashes($record['AFD_CODE'])."'
                || '-' || '".addslashes($record['BLOCK_CODE'])."' || '-' || 'RKT009' || '-' || 'POKOK_SISIP' || '".$this->_global->randomString(5)."',
                '".addslashes($record['MATURITY_STAGE_SMS1'])."', '".addslashes($record['MATURITY_STAGE_SMS2'])."',
                REPLACE('".addslashes($pokok_pupuk[$idx][1])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][2])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][3])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][4])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][5])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][6])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][7])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][8])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][9])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][10])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][11])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][12])."',',',''),REPLACE('".addslashes(array_sum($pokok_pupuk[$idx]))."',',',''),
                '{$this->_userName}',SYSDATE,'POKOK_SISIP',
                REPLACE('".addslashes($mcd_mon[$idx][1])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][2])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][3])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][4])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][5])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][6])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][7])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][8])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][9])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][10])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][11])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][12])."',',','')
              );
            ";
          }
        }
        //create sql file
        $this->_global->createSqlFile($params['filename'], $sql);
      }
    }
    return $result;
  }

  public function calculateKgSisip($params)
  {
    $this->calculateKgSisipUpload($params);
    $this->calculateKgSisipManual($params);
  }

  public function calculateKgSisipUpload($params)
  {
    $sql = "
      MERGE INTO TR_RKT_PUPUK_DISTRIBUSI RKT
      USING (
        SELECT * FROM (
          SELECT * FROM (
            SELECT TM.PERIOD_BUDGET , TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE, HS.BLOCK_DESC
            , HS.MATURITY_STAGE_SMS1, HS.MATURITY_STAGE_SMS2
            , TM.JUMLAH, TM.MATERIAL_CODE
            , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN
            FROM TN_PUPUK_TBM2_TM TM
            JOIN TM_HECTARE_STATEMENT HS ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
              AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
            WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = ".$params['budgetperiod']."
             AND TM.BA_CODE = '".$params['key_find'].$where_1."'
             AND TRIM(TM.JENIS_TANAM) = 'SISIP'
          )
        )
        PIVOT (
          SUM(JUMLAH) AS DIS, MAX(MATERIAL_CODE) AS MATERIAL_CODE
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PUPUK ON (PUPUK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND PUPUK.BA_CODE = RKT.BA_CODE AND PUPUK.AFD_CODE = RKT.AFD_CODE
        AND PUPUK.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1 AND PUPUK.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2
        AND RKT.TIPE_TRANSAKSI = 'KG_SISIP')
      WHEN MATCHED THEN UPDATE SET
        RKT.TRX_RKT_CODE = PUPUK.PERIOD_BUDGET||'-'||PUPUK.BA_CODE||'-'||PUPUK.AFD_CODE||'-'||PUPUK.BLOCK_CODE||'-RKT009-KG_SISIP-'||upper(dbms_random.string('L', 5)),
        RKT.DIS_JAN = PUPUK.JAN_DIS, RKT.MATERIAL_CODE_JAN = PUPUK.JAN_MATERIAL_CODE,
        RKT.DIS_FEB = PUPUK.FEB_DIS, RKT.MATERIAL_CODE_FEB = PUPUK.FEB_MATERIAL_CODE,
        RKT.DIS_MAR = PUPUK.MAR_DIS, RKT.MATERIAL_CODE_MAR = PUPUK.MAR_MATERIAL_CODE,
        RKT.DIS_APR = PUPUK.APR_DIS, RKT.MATERIAL_CODE_APR = PUPUK.APR_MATERIAL_CODE,
        RKT.DIS_MAY = PUPUK.MAY_DIS, RKT.MATERIAL_CODE_MAY = PUPUK.MAY_MATERIAL_CODE,
        RKT.DIS_JUN = PUPUK.JUN_DIS, RKT.MATERIAL_CODE_JUN = PUPUK.JUN_MATERIAL_CODE,
        RKT.DIS_JUL = PUPUK.JUL_DIS, RKT.MATERIAL_CODE_JUL = PUPUK.JUL_MATERIAL_CODE,
        RKT.DIS_AUG = PUPUK.AUG_DIS, RKT.MATERIAL_CODE_AUG = PUPUK.AUG_MATERIAL_CODE,
        RKT.DIS_SEP = PUPUK.SEP_DIS, RKT.MATERIAL_CODE_SEP = PUPUK.SEP_MATERIAL_CODE,
        RKT.DIS_OCT = PUPUK.OCT_DIS, RKT.MATERIAL_CODE_OCT = PUPUK.OCT_MATERIAL_CODE,
        RKT.DIS_NOV = PUPUK.NOV_DIS, RKT.MATERIAL_CODE_NOV = PUPUK.NOV_MATERIAL_CODE,
        RKT.DIS_DEC = PUPUK.DEC_DIS, RKT.MATERIAL_CODE_DEC = PUPUK.DEC_MATERIAL_CODE,
        RKT.DIS_TOTAL = NVL(PUPUK.JAN_DIS, 0)+NVL(PUPUK.FEB_DIS, 0)+NVL(PUPUK.MAR_DIS, 0)+NVL(PUPUK.APR_DIS, 0)+
                        NVL(PUPUK.MAY_DIS, 0)+NVL(PUPUK.JUN_DIS, 0)+NVL(PUPUK.JUL_DIS, 0)+NVL(PUPUK.AUG_DIS, 0)+
                        NVL(PUPUK.SEP_DIS, 0)+NVL(PUPUK.OCT_DIS, 0)+NVL(PUPUK.NOV_DIS, 0)+NVL(PUPUK.DEC_DIS, 0),
        RKT.UPDATE_USER = '".$this->_userName."',
        RKT.UPDATE_TIME = CURRENT_TIMESTAMP
      WHEN NOT MATCHED THEN INSERT (
        PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2,
        DIS_JAN, MATERIAL_CODE_JAN,
        DIS_FEB, MATERIAL_CODE_FEB,
        DIS_MAR, MATERIAL_CODE_MAR,
        DIS_APR, MATERIAL_CODE_APR,
        DIS_MAY, MATERIAL_CODE_MAY,
        DIS_JUN, MATERIAL_CODE_JUN,
        DIS_JUL, MATERIAL_CODE_JUL,
        DIS_AUG, MATERIAL_CODE_AUG,
        DIS_SEP, MATERIAL_CODE_SEP,
        DIS_OCT, MATERIAL_CODE_OCT,
        DIS_NOV, MATERIAL_CODE_NOV,
        DIS_DEC, MATERIAL_CODE_DEC,
        DIS_TOTAL,INSERT_USER,INSERT_TIME
      ) VALUES (
        PUPUK.PERIOD_BUDGET, 
        PUPUK.BA_CODE, 
        PUPUK.AFD_CODE, 
        PUPUK.BLOCK_CODE, 
        'KG_SISIP', 
        EXTRACT(YEAR FROM PUPUK.PERIOD_BUDGET)||'-'||PUPUK.BA_CODE||'-'||PUPUK.AFD_CODE||'-'||PUPUK.BLOCK_CODE||'-RKT009-KG_SISIP-'||upper(dbms_random.string('L', 5)), 
        PUPUK.MATURITY_STAGE_SMS1, 
        PUPUK.MATURITY_STAGE_SMS2,
        PUPUK.JAN_DIS, PUPUK.JAN_MATERIAL_CODE,
        PUPUK.FEB_DIS, PUPUK.FEB_MATERIAL_CODE,
        PUPUK.MAR_DIS, PUPUK.MAR_MATERIAL_CODE,
        PUPUK.APR_DIS, PUPUK.APR_MATERIAL_CODE,
        PUPUK.MAY_DIS, PUPUK.MAY_MATERIAL_CODE,
        PUPUK.JUN_DIS, PUPUK.JUN_MATERIAL_CODE,
        PUPUK.JUL_DIS, PUPUK.JUL_MATERIAL_CODE,
        PUPUK.AUG_DIS, PUPUK.AUG_MATERIAL_CODE,
        PUPUK.SEP_DIS, PUPUK.SEP_MATERIAL_CODE,
        PUPUK.OCT_DIS, PUPUK.OCT_MATERIAL_CODE,
        PUPUK.NOV_DIS, PUPUK.NOV_MATERIAL_CODE,
        PUPUK.DEC_DIS, PUPUK.DEC_MATERIAL_CODE,
        NVL(PUPUK.JAN_DIS, 0)+NVL(PUPUK.FEB_DIS, 0)+NVL(PUPUK.MAR_DIS, 0)+NVL(PUPUK.APR_DIS, 0)+
        NVL(PUPUK.MAY_DIS, 0)+NVL(PUPUK.JUN_DIS, 0)+NVL(PUPUK.JUL_DIS, 0)+NVL(PUPUK.AUG_DIS, 0)+
        NVL(PUPUK.SEP_DIS, 0)+NVL(PUPUK.OCT_DIS, 0)+NVL(PUPUK.NOV_DIS, 0)+NVL(PUPUK.DEC_DIS, 0),
        '".$this->_userName."', CURRENT_TIMESTAMP
      );
    ";
    $this->_global->createSqlFile($params['filename'], $sql);

  }

  /**
   * yaddi.surahman@tap-agri.co.id -- 2017-08-15
   * calculate kg pupuk untuk rkt sisip manual
   */
  public function calculateKgSisipManual($params)
  {
    // data dari rkt sisip manual
    $sql = "MERGE INTO TR_RKT_PUPUK_DISTRIBUSI RKT
            USING (
              WITH BIAYA AS (
                SELECT DISTINCT C1.PERIOD_BUDGET, C1.BA_CODE, C1.ACTIVITY_GROUP, C1.ACTIVITY_CODE, C1.SUB_COST_ELEMENT, C1.QTY JUMLAH, C1.PRICE, MAT.MATERIAL_NAME, C1.QTY/C2.QTY DOSIS
                FROM TN_BIAYA C1
                JOIN TM_MATERIAL MAT ON MAT.PERIOD_BUDGET = C1.PERIOD_BUDGET AND MAT.BA_CODE = C1.BA_CODE 
                  AND MAT.MATERIAL_CODE = C1.SUB_COST_ELEMENT AND MAT.DETAIL_CAT_DESC = 'PUPUK'
                JOIN TN_BIAYA C2 ON C2.PERIOD_BUDGET = C1.PERIOD_BUDGET AND C2.BA_CODE = C1.BA_CODE
                  AND C2.ACTIVITY_CODE = C1.ACTIVITY_CODE AND C2.SUB_COST_ELEMENT = '102010001'
                WHERE EXTRACT(YEAR FROM C1.PERIOD_BUDGET) = ".$params['budgetperiod']."
                AND C1.BA_CODE = '".$params['key_find']."' AND C1.ACTIVITY_CODE = '42700'
              )
              SELECT
              SSP.PERIOD_BUDGET,SSP.BA_CODE,SSP.AFD_CODE,SSP.BLOCK_CODE,SSP.TIPE_TRANSAKSI,SSP.ACTIVITY_CODE,
              MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2,
              PLAN_JAN, PLAN_JAN*CST.DOSIS KG_JAN, DECODE(PLAN_JAN, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_JAN,
              PLAN_FEB, PLAN_FEB*CST.DOSIS KG_FEB, DECODE(PLAN_FEB, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_FEB,
              PLAN_MAR, PLAN_MAR*CST.DOSIS KG_MAR, DECODE(PLAN_MAR, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_MAR,
              PLAN_APR, PLAN_APR*CST.DOSIS KG_APR, DECODE(PLAN_APR, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_APR,
              PLAN_MAY, PLAN_MAY*CST.DOSIS KG_MAY, DECODE(PLAN_MAY, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_MAY,
              PLAN_JUN, PLAN_JUN*CST.DOSIS KG_JUN, DECODE(PLAN_JUN, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_JUN,
              PLAN_JUL, PLAN_JUL*CST.DOSIS KG_JUL, DECODE(PLAN_JUL, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_JUL,
              PLAN_AUG, PLAN_AUG*CST.DOSIS KG_AUG, DECODE(PLAN_AUG, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_AUG,
              PLAN_SEP, PLAN_SEP*CST.DOSIS KG_SEP, DECODE(PLAN_SEP, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_SEP,
              PLAN_OCT, PLAN_OCT*CST.DOSIS KG_OCT, DECODE(PLAN_OCT, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_OCT,
              PLAN_NOV, PLAN_NOV*CST.DOSIS KG_NOV, DECODE(PLAN_NOV, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_NOV,
              PLAN_DEC, PLAN_DEC*CST.DOSIS KG_DEC, DECODE(PLAN_DEC, 0, '-', CST.SUB_COST_ELEMENT) MATERIAL_CODE_DEC
              FROM TR_RKT SSP 
              JOIN BIAYA CST ON CST.PERIOD_BUDGET = SSP.PERIOD_BUDGET AND CST.BA_CODE = SSP.BA_CODE AND CST.ACTIVITY_GROUP = SSP.MATURITY_STAGE_SMS2
              WHERE SSP.ACTIVITY_CODE = '42700'
              AND EXTRACT(YEAR FROM SSP.PERIOD_BUDGET) = ".$params['budgetperiod']."
              AND TOTAL_RP_SETAHUN != 0
            ) SISIP ON (
              SISIP.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND SISIP.BA_CODE = RKT.BA_CODE 
              AND SISIP.AFD_CODE = RKT.AFD_CODE AND SISIP.BLOCK_CODE = RKT.BLOCK_CODE
              AND SISIP.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1 AND SISIP.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2
              AND RKT.TIPE_TRANSAKSI = 'KG_SISIP' AND RKT.MATERIAL_CODE_JAN = SISIP.MATERIAL_CODE_JAN
            )
            WHEN MATCHED THEN UPDATE SET 
              RKT.DIS_JAN = SISIP.KG_JAN, -- RKT.MATERIAL_CODE_JAN = SISIP.MATERIAL_CODE_JAN,
              RKT.DIS_FEB = SISIP.KG_FEB, RKT.MATERIAL_CODE_FEB = SISIP.MATERIAL_CODE_FEB,
              RKT.DIS_MAR = SISIP.KG_MAR, RKT.MATERIAL_CODE_MAR = SISIP.MATERIAL_CODE_MAR,
              RKT.DIS_APR = SISIP.KG_APR, RKT.MATERIAL_CODE_APR = SISIP.MATERIAL_CODE_APR,
              RKT.DIS_MAY = SISIP.KG_MAY, RKT.MATERIAL_CODE_MAY = SISIP.MATERIAL_CODE_MAY,
              RKT.DIS_JUN = SISIP.KG_JUN, RKT.MATERIAL_CODE_JUN = SISIP.MATERIAL_CODE_JUN,
              RKT.DIS_JUL = SISIP.KG_JUL, RKT.MATERIAL_CODE_JUL = SISIP.MATERIAL_CODE_JUL,
              RKT.DIS_AUG = SISIP.KG_AUG, RKT.MATERIAL_CODE_AUG = SISIP.MATERIAL_CODE_AUG,
              RKT.DIS_SEP = SISIP.KG_SEP, RKT.MATERIAL_CODE_SEP = SISIP.MATERIAL_CODE_SEP,
              RKT.DIS_OCT = SISIP.KG_OCT, RKT.MATERIAL_CODE_OCT = SISIP.MATERIAL_CODE_OCT,
              RKT.DIS_NOV = SISIP.KG_NOV, RKT.MATERIAL_CODE_NOV = SISIP.MATERIAL_CODE_NOV,
              RKT.DIS_DEC = SISIP.KG_DEC, RKT.MATERIAL_CODE_DEC = SISIP.MATERIAL_CODE_DEC,
              RKT.DIS_TOTAL = NVL(SISIP.KG_JAN,0)+NVL(SISIP.KG_FEB,0)+NVL(SISIP.KG_MAR,0)+NVL(SISIP.KG_APR,0)+
                              NVL(SISIP.KG_MAY,0)+NVL(SISIP.KG_JUN,0)+NVL(SISIP.KG_JUL,0)+NVL(SISIP.KG_AUG,0)+
                              NVL(SISIP.KG_SEP,0)+NVL(SISIP.KG_OCT,0)+NVL(SISIP.KG_NOV,0)+NVL(SISIP.KG_DEC,0),
              RKT.UPDATE_USER = '".$this->_userName."',
              RKT.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET,BA_CODE,AFD_CODE,BLOCK_CODE,TIPE_TRANSAKSI,TRX_RKT_CODE,
              MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2,
              DIS_JAN, MATERIAL_CODE_JAN,
              DIS_FEB, MATERIAL_CODE_FEB,
              DIS_MAR, MATERIAL_CODE_MAR,
              DIS_APR, MATERIAL_CODE_APR,
              DIS_MAY, MATERIAL_CODE_MAY,
              DIS_JUN, MATERIAL_CODE_JUN,
              DIS_JUL, MATERIAL_CODE_JUL,
              DIS_AUG, MATERIAL_CODE_AUG,
              DIS_SEP, MATERIAL_CODE_SEP,
              DIS_OCT, MATERIAL_CODE_OCT,
              DIS_NOV, MATERIAL_CODE_NOV,
              DIS_DEC, MATERIAL_CODE_DEC,
              DIS_TOTAL, INSERT_USER, INSERT_TIME
            ) VALUES (
              SISIP.PERIOD_BUDGET, SISIP.BA_CODE, SISIP.AFD_CODE, SISIP.BLOCK_CODE,
              'KG_SISIP', 
              EXTRACT(YEAR FROM SISIP.PERIOD_BUDGET)||'-'||SISIP.BA_CODE||'-'||SISIP.AFD_CODE||'-'||SISIP.BLOCK_CODE||'-RKT009-KG_SISIP-'||upper(dbms_random.string('L', 5)),
              SISIP.MATURITY_STAGE_SMS1,SISIP.MATURITY_STAGE_SMS2,
              SISIP.KG_JAN, SISIP.MATERIAL_CODE_JAN,
              SISIP.KG_FEB, SISIP.MATERIAL_CODE_FEB,
              SISIP.KG_MAR, SISIP.MATERIAL_CODE_MAR,
              SISIP.KG_APR, SISIP.MATERIAL_CODE_APR,
              SISIP.KG_MAY, SISIP.MATERIAL_CODE_MAY,
              SISIP.KG_JUN, SISIP.MATERIAL_CODE_JUN,
              SISIP.KG_JUL, SISIP.MATERIAL_CODE_JUL,
              SISIP.KG_AUG, SISIP.MATERIAL_CODE_AUG,
              SISIP.KG_SEP, SISIP.MATERIAL_CODE_SEP,
              SISIP.KG_OCT, SISIP.MATERIAL_CODE_OCT,
              SISIP.KG_NOV, SISIP.MATERIAL_CODE_NOV,
              SISIP.KG_DEC, SISIP.MATERIAL_CODE_DEC,
              NVL(SISIP.KG_JAN,0)+NVL(SISIP.KG_FEB,0)+NVL(SISIP.KG_MAR,0)+NVL(SISIP.KG_APR,0)+
              NVL(SISIP.KG_MAY,0)+NVL(SISIP.KG_JUN,0)+NVL(SISIP.KG_JUL,0)+NVL(SISIP.KG_AUG,0)+
              NVL(SISIP.KG_SEP,0)+NVL(SISIP.KG_OCT,0)+NVL(SISIP.KG_NOV,0)+NVL(SISIP.KG_DEC,0),
              '".$this->_userName."', CURRENT_TIMESTAMP
            );
    ";
    $this->_global->createSqlFile($params['filename'], $sql);

  }
}

