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
Dibuat Tanggal    :   16/07/2013
Update Terakhir   : 16/07/2013
Revisi        : 
=========================================================================================================================
*/
class Application_Model_RktPupukKgNormal
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
           rkt.DIS_JAN JAN, 
           rkt.DIS_FEB FEB, 
           rkt.DIS_MAR MAR, 
           rkt.DIS_APR APR, 
           rkt.DIS_MAY MAY, 
           rkt.DIS_JUN JUN, 
           rkt.DIS_JUL JUL, 
           rkt.DIS_AUG AUG, 
           rkt.DIS_SEP SEP, 
           rkt.DIS_OCT OCT, 
           rkt.DIS_NOV NOV, 
           rkt.DIS_DEC DEC, 
           rkt.DIS_TOTAL TOTAL,
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_JAN) as PUPUK_JAN,
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_FEB) as PUPUK_FEB,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_MAR) as PUPUK_MAR,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_APR) as PUPUK_APR,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_MAY) as PUPUK_MAY,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_JUN) as PUPUK_JUN,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_JUL) as PUPUK_JUL,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_AUG) as PUPUK_AUG,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_SEP) as PUPUK_SEP,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_OCT) as PUPUK_OCT,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_NOV) as PUPUK_NOV,    
           (SELECT MATERIAL_NAME FROM TM_MATERIAL WHERE PERIOD_BUDGET = ha_statement.PERIOD_BUDGET AND BA_CODE = ha_statement.BA_CODE AND MATERIAL_CODE = rkt.MATERIAL_CODE_DEC) as PUPUK_DEC  
      FROM TR_RKT_PUPUK_DISTRIBUSI rkt 
      LEFT JOIN TM_HECTARE_STATEMENT ha_statement 
        ON rkt.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET 
        AND rkt.BA_CODE = ha_statement.BA_CODE 
        AND rkt.AFD_CODE = ha_statement.AFD_CODE 
        AND rkt.BLOCK_CODE = ha_statement.BLOCK_CODE 
        AND rkt.TIPE_TRANSAKSI = 'KG_NORMAL' 
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
    $query .= "ORDER BY ha_statement.BA_CODE, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE";
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
  
  //kalkulasi seluruh data RKT Pupuk HA
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
              $sql = "
                SELECT DOSIS, MATERIAL_CODE
                FROM TN_PUPUK_TBM2_LESS
                WHERE DELETE_USER IS NULL
                  AND PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
                  AND BA_CODE = '".$record['BA_CODE']."'
                  AND LAND_TYPE = '".$record['LAND_TYPE']."'
                  -- AND MATURITY_STAGE = '".$maturity_stage."' -- KARENA YANG DILIHAT HANYA UMUR TANAMAN, TIDAK BERDASARKAN MATURITY STAGE
                  AND PALM_AGE = '".$umur_tanaman."'";
              $results = $this->_db->fetchAll($sql);
              
              if (!empty($results)) {
                foreach ($results as $idx => $result) {
                  $pokok_pupuk[$idx][$mBudget] = ($result['DOSIS']) ? $record['POKOK_TANAM'] : 0;           
                  $mcd_mon[$idx][$mBudget] = $result['MATERIAL_CODE'];
                  $dis_mon[$idx][$mBudget] = $result['DOSIS'] * $pokok_pupuk[$idx][$mBudget];
                }
              }           
            }
            
            //perhitungan untuk norma pupuk TBM 2 - TM
            else{
              $sql = "
                SELECT MATERIAL_CODE, NVL(MAX(JUMLAH), 0) JUMLAH, NVL(MAX(POKOK), 0) POKOK
                FROM TN_PUPUK_TBM2_TM
                WHERE DELETE_USER IS NULL
                  AND JENIS_TANAM = 'NORMAL' 
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
            AND TIPE_TRANSAKSI IN ('KG_NORMAL','POKOK_NORMAL');
        ";
        
        if (!empty($pokok_pupuk)) {
          foreach ($pokok_pupuk as $idx => $pokok) {
            //insert distribusi KG normal
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
                || '-' || '".addslashes($record['BLOCK_CODE'])."' || '-' || 'RKT010' || '-' || 'KG_NORMAL' || '".$this->_global->randomString(5)."',
                '".addslashes($record['MATURITY_STAGE_SMS1'])."', '".addslashes($record['MATURITY_STAGE_SMS2'])."',
                REPLACE('".addslashes($dis_mon[$idx][1])."',',',''), REPLACE('".addslashes($dis_mon[$idx][2])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][3])."',',',''), REPLACE('".addslashes($dis_mon[$idx][4])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][5])."',',',''), REPLACE('".addslashes($dis_mon[$idx][6])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][7])."',',',''), REPLACE('".addslashes($dis_mon[$idx][8])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][9])."',',',''), REPLACE('".addslashes($dis_mon[$idx][10])."',',',''),
                REPLACE('".addslashes($dis_mon[$idx][11])."',',',''), REPLACE('".addslashes($dis_mon[$idx][12])."',',',''),REPLACE('".addslashes(array_sum($dis_mon[$idx]))."',',',''),
                '{$this->_userName}',SYSDATE,'KG_NORMAL',
                REPLACE('".addslashes($mcd_mon[$idx][1])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][2])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][3])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][4])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][5])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][6])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][7])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][8])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][9])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][10])."',',',''),
                REPLACE('".addslashes($mcd_mon[$idx][11])."',',',''), REPLACE('".addslashes($mcd_mon[$idx][12])."',',','')
              );
            ";
            
            //insert distribusi pokok normal
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
                || '-' || '".addslashes($record['BLOCK_CODE'])."' || '-' || 'RKT010' || '-' || 'POKOK_NORMAL' || '".$this->_global->randomString(5)."',
                '".addslashes($record['MATURITY_STAGE_SMS1'])."', '".addslashes($record['MATURITY_STAGE_SMS2'])."',
                REPLACE('".addslashes($pokok_pupuk[$idx][1])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][2])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][3])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][4])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][5])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][6])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][7])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][8])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][9])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][10])."',',',''),
                REPLACE('".addslashes($pokok_pupuk[$idx][11])."',',',''), REPLACE('".addslashes($pokok_pupuk[$idx][12])."',',',''),REPLACE('".addslashes(array_sum($pokok_pupuk[$idx]))."',',',''),
                '{$this->_userName}',SYSDATE,'POKOK_NORMAL',
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

  /**
   * yaddi.surahman@tap-agri.co.id --- 2017-08-14
   * Hitung sebaran pemakaian pupuk (Kg) per janis pupuk 
   * Hitung sebaran pokok yang dipupuk 
   */
  public function calculatePokokPupuk($params)
  {
    if (($params['maturity_stage']) && ($params['maturity_stage'] != 'ALL')) {
      $where_1 .= "
        AND (
          UPPER(MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['maturity_stage']."%')
          OR UPPER(MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['maturity_stage']."%')
        )
      ";
    }

    $sql = "DELETE FROM TR_RKT_PUPUK_DISTRIBUSI 
            WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = ".$params['budgetperiod']."
            AND BA_CODE = '".$params['key_find'].$where_1."'
            AND TIPE_TRANSAKSI IN ('KG_NORMAL', 'POKOK_NORMAL');
            ";
    $this->_global->createSqlFile($params['filename'], $sql);

    // HITUNG SEBARAN KG_NORMAL PEMAKAIAN PUPUK UNTUK TM, TBM DAN REKOMENDASI TBM
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
               AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
            )
            UNION 
            SELECT * FROM (
              SELECT TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.BLOCK_DESC
              , HS.MATURITY_STAGE_SMS1, HS.MATURITY_STAGE_SMS2
              , (TBM.DOSIS * HS.POKOK_TANAM) JUMLAH, TBM.MATERIAL_CODE
              , HS.BULAN_PEMUPUKAN
              FROM (
                SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
                FROM (
                  SELECT HCS.*, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM 
                  FROM TM_HECTARE_STATEMENT HCS WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = ".$params['budgetperiod']."
                  AND HCS.BA_CODE = '".$params['key_find']."'
                  AND NOT EXISTS (
                    SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HCS.PERIOD_BUDGET AND TM.BA_CODE = HCS.BA_CODE
                    AND TM.AFD_CODE = HCS.AFD_CODE AND TM.BLOCK_CODE = HCS.BLOCK_CODE
                  )
                  AND HCS.MATURITY_STAGE_SMS1 != 'TM'
                ) T_1
                CROSS JOIN (
                  SELECT LEVEL-1 MONTHTOADD FROM (
                    SELECT 0 MONTH_START, 12 MONTH_END
                    FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
                  ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
                )
              ) HS 
              JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE AND TBM.LAND_TYPE = HS.LAND_TYPE AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
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
        AND RKT.TIPE_TRANSAKSI = 'KG_NORMAL')
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
        'KG_NORMAL', 
        PUPUK.PERIOD_BUDGET||'-'||PUPUK.BA_CODE||'-'||PUPUK.AFD_CODE||'-'||PUPUK.BLOCK_CODE||'-RKT010-KG_NORMAL-'||upper(dbms_random.string('L', 5)), 
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
        NVL(PUPUK.JAN_DIS, 0)+NVL(PUPUK.FEB_DIS, 0)+NVL(PUPUK.MAR_DIS, 0)+NVL(PUPUK.APR_DIS, 0)+NVL(PUPUK.MAY_DIS, 0)+NVL(PUPUK.JUN_DIS, 0)+
        NVL(PUPUK.JUL_DIS, 0)+NVL(PUPUK.AUG_DIS, 0)+NVL(PUPUK.SEP_DIS, 0)+NVL(PUPUK.OCT_DIS, 0)+NVL(PUPUK.NOV_DIS, 0)+NVL(PUPUK.DEC_DIS, 0),
        '".$this->_userName."', CURRENT_TIMESTAMP
      );
    ";
    $this->_global->createSqlFile($params['filename'], $sql);

    // HITUNG SEBARAN POKOK TANAMAN YANG DIPUPUK UNTUK TM, TBM DAN REKOMENDASI TBM
    $sql = "
      MERGE INTO TR_RKT_PUPUK_DISTRIBUSI RKT
      USING (
        SELECT * FROM (
            SELECT * FROM (
              SELECT TM.PERIOD_BUDGET , TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE, HS.BLOCK_DESC
              , HS.MATURITY_STAGE_SMS1, HS.MATURITY_STAGE_SMS2
              , TM.POKOK, TM.MATERIAL_CODE
              , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN
              FROM TN_PUPUK_TBM2_TM TM
              JOIN TM_HECTARE_STATEMENT HS ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
                AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
              JOIN TM_MATERIAL MAT ON MAT.BA_CODE = HS.BA_CODE AND MAT.PERIOD_BUDGET = HS.PERIOD_BUDGET AND MAT.MATERIAL_CODE = TM.MATERIAL_CODE
              WHERE EXTRACT(YEAR FROM TM.PERIOD_BUDGET) = ".$params['budgetperiod']."
               AND TM.BA_CODE = '".$params['key_find'].$where_1."'
               AND TRIM(TM.JENIS_TANAM) = 'NORMAL'
            )
            UNION 
            SELECT * FROM (
              SELECT TBM.PERIOD_BUDGET, TBM.BA_CODE, HS.AFD_CODE, HS.BLOCK_CODE, HS.BLOCK_DESC
              , HS.MATURITY_STAGE_SMS1, HS.MATURITY_STAGE_SMS2
              , (HS.POKOK_TANAM) POKOK, TBM.MATERIAL_CODE
              , HS.BULAN_PEMUPUKAN
              FROM (
                SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
                FROM (
                  SELECT HCS.*, MONTHS_BETWEEN(HCS.PERIOD_BUDGET, HCS.TAHUN_TANAM) USIA_TANAM 
                  FROM TM_HECTARE_STATEMENT HCS WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = ".$params['budgetperiod']."
                  AND HCS.BA_CODE = '".$params['key_find'].$where_1."'
                  AND HCS.MATURITY_STAGE_SMS1 != 'TM' AND HCS.MATURITY_STAGE_SMS2 != 'TM'
                ) T_1
                CROSS JOIN (
                  SELECT LEVEL-1 MONTHTOADD FROM (
                    SELECT 0 MONTH_START, 12 MONTH_END
                    FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
                  ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
                )
              ) HS 
              JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HS.USIA_TANAMAN AND TBM.BA_CODE = HS.BA_CODE AND TBM.LAND_TYPE = HS.LAND_TYPE
                AND TBM.PERIOD_BUDGET = HS.PERIOD_BUDGET
            )
        )
        PIVOT (
          SUM(POKOK) AS DIS, MAX(MATERIAL_CODE) AS MATERIAL_CODE
          FOR BULAN_PEMUPUKAN IN (
            '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
            '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
          )
        )
      ) PUPUK ON (PUPUK.PERIOD_BUDGET = RKT.PERIOD_BUDGET AND PUPUK.BA_CODE = RKT.BA_CODE AND PUPUK.AFD_CODE = RKT.AFD_CODE
        AND PUPUK.MATURITY_STAGE_SMS1 = RKT.MATURITY_STAGE_SMS1 AND PUPUK.MATURITY_STAGE_SMS2 = RKT.MATURITY_STAGE_SMS2
        AND RKT.TIPE_TRANSAKSI = 'POKOK_NORMAL')
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
        'POKOK_NORMAL', 
        PUPUK.PERIOD_BUDGET||'-'||PUPUK.BA_CODE||'-'||PUPUK.AFD_CODE||'-'||PUPUK.BLOCK_CODE||'-RKT010-POKOK_NORMAL-'||upper(dbms_random.string('L', 5)), 
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
        NVL(PUPUK.JAN_DIS, 0)+NVL(PUPUK.FEB_DIS, 0)+NVL(PUPUK.MAR_DIS, 0)+NVL(PUPUK.APR_DIS, 0)+NVL(PUPUK.MAY_DIS, 0)+NVL(PUPUK.JUN_DIS, 0)+
        NVL(PUPUK.JUL_DIS, 0)+NVL(PUPUK.AUG_DIS, 0)+NVL(PUPUK.SEP_DIS, 0)+NVL(PUPUK.OCT_DIS, 0)+NVL(PUPUK.NOV_DIS, 0)+NVL(PUPUK.DEC_DIS, 0),
        '".$this->_userName."', CURRENT_TIMESTAMP
      );
    ";
    $this->_global->createSqlFile($params['filename'], $sql);
  }
}

