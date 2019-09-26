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
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   16/07/2013
Update Terakhir   : 16/07/2013
Revisi        : 
=========================================================================================================================
*/
class Application_Model_RktPupukHa
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
      SELECT ROWIDTOCHAR(rkt.ROWID) row_id, 
           to_char(ha_statement.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
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
           rkt.TRX_RKT_CODE, 
           ha_statement.MATURITY_STAGE_SMS1, 
           ha_statement.MATURITY_STAGE_SMS2, 
           ha_statement.HA_PLANTED, 
           ha_statement.POKOK_TANAM, 
           ha_statement.SPH,
           rkt.TIPE_TRANSAKSI,
           rkt.JAN, 
           rkt.FEB, 
           rkt.MAR, 
           rkt.APR, 
           rkt.MAY, 
           rkt.JUN, 
           rkt.JUL, 
           rkt.AUG, 
           rkt.SEP, 
           rkt.OCT, 
           rkt.NOV, 
           rkt.DEC, 
           rkt.SETAHUN
      FROM TM_HECTARE_STATEMENT ha_statement
      LEFT JOIN TR_RKT_PUPUK rkt
        ON rkt.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET
        AND rkt.BA_CODE = ha_statement.BA_CODE
        AND rkt.AFD_CODE = ha_statement.AFD_CODE
        AND rkt.BLOCK_CODE = ha_statement.BLOCK_CODE
        AND rkt.TIPE_TRANSAKSI = 'HA'
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
    
    $query .= "
      ORDER BY ha_statement.BA_CODE, ha_statement.AFD_CODE, ha_statement.BLOCK_CODE
    ";
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
    //filter maturity_stage
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
          //perhitungan untuk norma pupuk TBM 2 Less
          if($jenis_norma_pupuk == 'TN_PUPUK_TBM2_LESS'){
            $sql = "
              SELECT DOSIS
              FROM TN_PUPUK_TBM2_LESS
              WHERE DELETE_USER IS NULL
                AND PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
                AND BA_CODE = '".$record['BA_CODE']."'
                AND LAND_TYPE = '".$record['LAND_TYPE']."'
                -- AND MATURITY_STAGE = '".$maturity_stage."'
                AND PALM_AGE = '".$umur_tanaman."'
            ";
                
            $dosis = $this->_db->fetchOne($sql);
            $ha_pupuk[$mBudget] = ($dosis) ? $record['HA_PLANTED'] : 0;
            $pokok_pupuk[$mBudget] = ($dosis) ? $record['POKOK_TANAM'] : 0;
          }
          //perhitungan untuk norma pupuk TBM 2 - TM
          else{
            $sql = "
              SELECT NVL(MAX(HA_PUPUK), 0) HA_PUPUK, NVL(MAX(POKOK), 0) POKOK
              FROM TN_PUPUK_TBM2_TM
              WHERE DELETE_USER IS NULL
                AND PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
                AND BA_CODE = '".$record['BA_CODE']."'
                AND AFD_CODE = '".$record['AFD_CODE']."'
                AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
                AND BULAN_PEMUPUKAN = '".$mBudget."'
            ";
            $res = $this->_db->fetchRow($sql);
            $ha_pupuk[$mBudget] = $res['HA_PUPUK'];
            $pokok_pupuk[$mBudget] = $res['POKOK'];
          } 
        } 
        
        //update RKT Pupuk
        $sql = "
          UPDATE TR_RKT_PUPUK
          SET MATURITY_STAGE_SMS1 = '".addslashes($record['MATURITY_STAGE_SMS1'])."',
            MATURITY_STAGE_SMS2 = '".addslashes($record['MATURITY_STAGE_SMS2'])."',
            JAN = REPLACE('".addslashes($ha_pupuk[1])."',',',''),
            FEB = REPLACE('".addslashes($ha_pupuk[2])."',',',''), 
            MAR = REPLACE('".addslashes($ha_pupuk[3])."',',',''),
            APR = REPLACE('".addslashes($ha_pupuk[4])."',',',''),
            MAY = REPLACE('".addslashes($ha_pupuk[5])."',',',''),
            JUN = REPLACE('".addslashes($ha_pupuk[6])."',',',''),
            JUL = REPLACE('".addslashes($ha_pupuk[7])."',',',''),
            AUG = REPLACE('".addslashes($ha_pupuk[8])."',',',''),
            SEP = REPLACE('".addslashes($ha_pupuk[9])."',',',''),
            OCT = REPLACE('".addslashes($ha_pupuk[10])."',',',''),
            NOV = REPLACE('".addslashes($ha_pupuk[11])."',',',''),
            DEC = REPLACE('".addslashes($ha_pupuk[12])."',',',''),
            SETAHUN = REPLACE('".addslashes(array_sum($ha_pupuk))."',',',''),
            UPDATE_USER = '{$this->_userName}',
            UPDATE_TIME = SYSDATE
          WHERE PERIOD_BUDGET = TO_DATE('01-01-{$budget}','DD-MM-RRRR')
            AND BA_CODE = '".$record['BA_CODE']."'
            AND AFD_CODE = '".$record['AFD_CODE']."'
            AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
            AND TIPE_TRANSAKSI = 'HA';
        ";
        //var_dump($sql);
        //create sql file
        $this->_global->createSqlFile($params['filename'], $sql);
      }
    }

    return $result;
  }

  /**
   * Karena penambahan rekomendasi TBM yang mendapat dosis TM, 
   * maka untuk memudahkan, kalkulasi HA pupuk tidak berdasarkan data dari hectare statement lagi,
   * tapi langsung union dari data yang di upload 
   * yaddi.surahman@tap-agri.co.id --- 2018-08-14
   */
  public function calculateHaPupuk($params)
  {
    $where_1 = "EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = ".$params['budgetperiod'];
    if($params['key_find']){
      $where_1 .= " AND HS.BA_CODE = '".$params['key_find']."' ";
    }
    if (($params['maturity_stage']) && ($params['maturity_stage'] != 'ALL')) {
      $where_1 .= "
        AND (
          UPPER(MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['maturity_stage']."%')
          OR UPPER(MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['maturity_stage']."%')
        )
      ";
    }
    $where_2 = $where_1." AND MATURITY_STAGE_SMS1 != 'TM' AND MATURITY_STAGE_SMS2 != 'TM' ";

    $sql = "MERGE INTO TR_RKT_PUPUK RKT
            USING (
              SELECT * FROM (
                SELECT * FROM (
                  SELECT TM.PERIOD_BUDGET, TM.BA_CODE, TM.AFD_CODE, TM.BLOCK_CODE, HS.BLOCK_DESC
                    , HS.MATURITY_STAGE_SMS1, HS.MATURITY_STAGE_SMS2, 'HA' TIPE_TRANSAKSI
                    , CAST(TM.BULAN_PEMUPUKAN AS INT) BULAN_PEMUPUKAN, TM.HA_PUPUK
                  FROM TN_PUPUK_TBM2_TM TM
                  JOIN TM_HECTARE_STATEMENT HS ON TM.AFD_CODE = HS.AFD_CODE AND TM.BA_CODE = HS.BA_CODE
                    AND TM.BLOCK_CODE = HS.BLOCK_CODE AND TM.PERIOD_BUDGET = HS.PERIOD_BUDGET
                  WHERE ".$where_1."
                )
                UNION
                SELECT * FROM (
                  SELECT TBM.PERIOD_BUDGET, TBM.BA_CODE, HCS.AFD_CODE, HCS.BLOCK_CODE, HCS.BLOCK_DESC, HCS.MATURITY_STAGE_SMS1, HCS.MATURITY_STAGE_SMS2
                  , 'HA' TIPE_TRANSAKSI, HCS.BULAN_PEMUPUKAN, HCS.HA_PLANTED HA_PUPUK
                  FROM (
                    SELECT MONTHTOADD, (T_1.USIA_TANAM + MONTHTOADD) USIA_TANAMAN, MONTHTOADD + 1 AS BULAN_PEMUPUKAN, T_1.*
                    FROM (
                      SELECT HS.*, MONTHS_BETWEEN(HS.PERIOD_BUDGET, HS.TAHUN_TANAM) USIA_TANAM FROM TM_HECTARE_STATEMENT HS 
                      WHERE EXTRACT(YEAR FROM HS.PERIOD_BUDGET) = ".$params['budgetperiod']."
                      AND NOT EXISTS (
                        SELECT 1 FROM TN_PUPUK_TBM2_TM TM WHERE TM.PERIOD_BUDGET = HS.PERIOD_BUDGET AND TM.BA_CODE = HS.BA_CODE
                        AND TM.AFD_CODE = HS.AFD_CODE AND TM.BLOCK_CODE = HS.BLOCK_CODE
                      )
                    ) T_1
                    CROSS JOIN (
                      SELECT LEVEL-1 MONTHTOADD FROM (
                        SELECT 0 MONTH_START, 12 MONTH_END FROM (SELECT SYSDATE AS TANGGAL FROM DUAL)
                      ) CONNECT BY LEVEL <= (MONTH_END - MONTH_START)
                    )
                  ) HCS 
                  JOIN TN_PUPUK_TBM2_LESS TBM ON TBM.PALM_AGE = HCS.USIA_TANAMAN AND TBM.BA_CODE = HCS.BA_CODE AND TBM.LAND_TYPE = HCS.LAND_TYPE
                    AND TBM.PERIOD_BUDGET = HCS.PERIOD_BUDGET
                  ORDER BY 2,3 ASC
                )
              )
               PIVOT (
                MAX(HA_PUPUK) FOR BULAN_PEMUPUKAN IN (
                  '1' AS JAN, '2' AS FEB, '3' AS MAR, '4' AS APR, '5' AS MAY, '6' AS JUN,
                  '7' AS JUL, '8' AS AUG, '9' AS SEP, '10' AS OCT, '11' AS NOV, '12' AS DEC
                )
              )
            ) NORMA ON (RKT.PERIOD_BUDGET = NORMA.PERIOD_BUDGET AND RKT.AFD_CODE = NORMA.AFD_CODE
                        AND RKT.BA_CODE = NORMA.BA_CODE AND RKT.BLOCK_CODE = NORMA.BLOCK_CODE 
                        AND RKT.TIPE_TRANSAKSI = NORMA.TIPE_TRANSAKSI
                        AND RKT.MATURITY_STAGE_SMS1 = NORMA.MATURITY_STAGE_SMS1 
                        AND RKT.MATURITY_STAGE_SMS2 = NORMA.MATURITY_STAGE_SMS2)
            WHEN MATCHED THEN UPDATE SET
              RKT.JAN = NORMA.JAN,
              RKT.FEB = NORMA.FEB,
              RKT.MAR = NORMA.MAR,
              RKT.APR = NORMA.APR,
              RKT.MAY = NORMA.MAY,
              RKT.JUN = NORMA.JUN,
              RKT.JUL = NORMA.JUL,
              RKT.AUG = NORMA.AUG,
              RKT.SEP = NORMA.SEP,
              RKT.OCT = NORMA.OCT,
              RKT.NOV = NORMA.NOV,
              RKT.DEC = NORMA.DEC,
              RKT.SETAHUN = NVL(NORMA.JAN, 0)+NVL(NORMA.FEB, 0)+NVL(NORMA.MAR, 0)+NVL(NORMA.APR, 0)+NVL(NORMA.MAY, 0)+NVL(NORMA.JUN, 0)+NVL(NORMA.JUL, 0)+NVL(NORMA.AUG, 0)+NVL(NORMA.SEP, 0)+NVL(NORMA.OCT, 0)+NVL(NORMA.NOV, 0)+NVL(NORMA.DEC, 0),
              RKT.UPDATE_USER = '".$this->_userName."',
              RKT.UPDATE_TIME = CURRENT_TIMESTAMP
            WHEN NOT MATCHED THEN INSERT (
              PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE,TRX_RKT_CODE,
              MATURITY_STAGE_SMS1,MATURITY_STAGE_SMS2,TIPE_TRANSAKSI,
              JAN,FEB,MAR,APR,MAY,JUN,JUL,AUG,SEP,OCT,NOV,DEC,SETAHUN,
              INSERT_USER,INSERT_TIME
            ) VALUES (
              NORMA.PERIOD_BUDGET, NORMA.BA_CODE, NORMA.AFD_CODE, NORMA.BLOCK_CODE, 
              EXTRACT(YEAR FROM NORMA.PERIOD_BUDGET)||'-'||NORMA.BA_CODE||'-'||NORMA.AFD_CODE||'-'||NORMA.BLOCK_CODE,
              NORMA.MATURITY_STAGE_SMS1, NORMA.MATURITY_STAGE_SMS2, NORMA.TIPE_TRANSAKSI,
              NORMA.JAN,NORMA.FEB,NORMA.MAR,NORMA.APR,NORMA.MAY,NORMA.JUN,NORMA.JUL,NORMA.AUG,NORMA.SEP,NORMA.OCT,NORMA.NOV,NORMA.DEC,
              NVL(NORMA.JAN, 0)+NVL(NORMA.FEB, 0)+NVL(NORMA.MAR, 0)+NVL(NORMA.APR, 0)+NVL(NORMA.MAY, 0)+NVL(NORMA.JUN, 0)+NVL(NORMA.JUL, 0)+NVL(NORMA.AUG, 0)+NVL(NORMA.SEP, 0)+NVL(NORMA.OCT, 0)+NVL(NORMA.NOV, 0)+NVL(NORMA.DEC, 0),
              '".$this->_userName."', CURRENT_TIMESTAMP
            );
            ";
    $this->_global->createSqlFile($params['filename'], $sql);
    return true;
  }
}

