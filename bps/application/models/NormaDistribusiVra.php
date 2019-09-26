<?php
/*
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Model Class untuk Norma Distribusi VRA
Function      : - getList         : menampilkan list norma Distribusi VRA
            - save            : simpan data
            - delete          : hapus data
            - getInput      : setting input untuk region dan maturity stage
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada
Developer     :   Yopie Irawan
Dibuat Tanggal    :   03/07/2013
Update Terakhir   : 03/07/2013
Revisi        : 
=========================================================================================================================
*/
class Application_Model_NormaDistribusiVra
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
  
  public function getAfdSite($budgetperiod, $key_find, $search)
  {
    $query = "
    SELECT 
      location.LOCATION_CODE,DESCRIPTION 
    FROM TM_LOCATION_DIST_VRA location
      LEFT JOIN TM_ORGANIZATION B
      ON location.BA_CODE = B.BA_CODE 
    WHERE location.DELETE_USER IS NULL "; 
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(location.BA_CODE)||'%'";
    }
    
    if($budgetperiod != ''){
      $query .= "
        AND to_char(location.PERIOD_BUDGET,'RRRR') = '".$budgetperiod."'";
    }else{
      $query .= "
        AND to_char(location.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'";
    }
    
    //filter region
    if ($params['src_region_code'] != '') {
      $query .= "
        AND UPPER(B.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
      ";
    }
    
    if ($key_find != '') {
      $query .= "
        AND UPPER(location.BA_CODE) LIKE UPPER('%".$key_find."%')";
    }
    
    if ($params['controller'] == 'download') {
      $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
    }
    
    if ($search != '') {
      $query .= "
        AND (
          UPPER(location.PERIOD_BUDGET) LIKE UPPER('%".$search."%')
          OR UPPER(location.BA_CODE) LIKE UPPER('%".$search."%')        
        )";
    }
      
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
  
  //ambil data dari DB - doni
  public function getDataHeader($params = array())
  {
    $query = "
        SELECT DISTINCT 
           VRA.TRX_CODE,
           TO_CHAR (VRA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           VRA.BA_CODE,
           VRA.ACTIVITY_CODE,
           TA.DESCRIPTION,
           VRA.VRA_CODE,
           TV.VRA_SUB_CAT_DESCRIPTION,
           TV.UOM 
        FROM TR_RKT_VRA_DISTRIBUSI VRA
        LEFT JOIN TM_ACTIVITY TA ON TA.ACTIVITY_CODE = VRA.ACTIVITY_CODE
        LEFT JOIN TM_VRA TV ON TV.VRA_CODE = VRA.VRA_CODE
        LEFT JOIN TM_ORGANIZATION ORG ON ORG.BA_CODE = VRA.BA_CODE 
        LEFT JOIN TR_RKT TR ON TR.PERIOD_BUDGET = VRA.PERIOD_BUDGET AND TR.BA_CODE = VRA.BA_CODE AND TR.ACTIVITY_CODE = VRA.ACTIVITY_CODE
         WHERE  VRA.DELETE_TIME IS NULL     
         AND VRA.TIPE_TRANSAKSI = 'INFRA'
    ";

    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(VRA.BA_CODE)||'%'";
    }
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(VRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
      ";
    }else{
      $query .= "
        AND to_char(VRA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
        AND UPPER(VRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
      ";
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(VRA.LOCATION_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(VRA.HM_KM) LIKE UPPER('%".$params['search']."%')
        )
      ";
    }
    
    $query .= "
      ORDER BY TA.DESCRIPTION,  TV.VRA_SUB_CAT_DESCRIPTION
    ";
    //echo $query;
    
    return $query;
  }
  
  //ambil data dari DB - doni
  public function getDataAfdeling($params = array())
  {
    /*$query = "
         SELECT TRX_CODE,LOCATION_CODE, SUM(HM_KM) HM_KM, SUM(PRICE_HM_KM) PRICE_HM_KM  
          FROM TR_RKT_VRA_DISTRIBUSI VRA 
           WHERE   to_char(VRA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."' 
           AND  UPPER(VRA.BA_CODE) LIKE UPPER('%".$params['key_find']."%') 
           AND TIPE_TRANSAKSI = 'INFRA'
           GROUP BY TRX_CODE,LOCATION_CODE 
        ORDER BY 1
    ";*/

    $query = "
          SELECT DISTINCT
            VHD.BA_CODE, VHD.TRX_CODE, VHD.LOCATION_CODE, NVL(RK.SUMBER_BIAYA, 'INTERNAL'), VHD.HM_KM, VHD.PRICE_HM_KM
          FROM TR_RKT_VRA_DISTRIBUSI VHD
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = VHD.ACTIVITY_CODE
          JOIN TM_VRA VH ON VH.VRA_CODE = VHD.VRA_CODE
          LEFT JOIN TR_RKT RK ON RK.PERIOD_BUDGET = VHD.PERIOD_BUDGET AND RK.BA_CODE = VHD.BA_CODE 
            AND RK.ACTIVITY_CODE = VHD.ACTIVITY_CODE AND RK.AFD_CODE = VHD.LOCATION_CODE
          WHERE EXTRACT(YEAR FROM VHD.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND VHD.BA_CODE = '".$params['key_find']."'
          AND VHD.TIPE_TRANSAKSI = 'INFRA'
          AND VHD.ACTIVITY_CODE NOT IN (
            SELECT DISTINCT ACTIVITY_CODE FROM TR_RKT_PK WHERE 
            EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND BA_CODE = '".$params['key_find']."'
          )
          UNION
          SELECT DISTINCT
            VHD.BA_CODE, VHD.TRX_CODE, VHD.LOCATION_CODE, NVL(RH.SUMBER_BIAYA, 'INTERNAL'), VHD.HM_KM, VHD.PRICE_HM_KM
          FROM TR_RKT_VRA_DISTRIBUSI VHD
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = VHD.ACTIVITY_CODE
          JOIN TM_VRA VH ON VH.VRA_CODE = VHD.VRA_CODE
          JOIN TR_RKT_PK RH ON RH.ACTIVITY_CODE = VHD.ACTIVITY_CODE AND RH.PERIOD_BUDGET = VHD.PERIOD_BUDGET
            AND RH.AFD_CODE = VHD.LOCATION_CODE 
            AND RH.SUMBER_BIAYA = 'INTERNAL'
            AND RH.JENIS_PEKERJAAN = 'PERULANGAN'
            AND RH.BA_CODE = VHD.BA_CODE
          WHERE EXTRACT(YEAR FROM VHD.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND VHD.BA_CODE = '".$params['key_find']."'
          AND VHD.TIPE_TRANSAKSI = 'INFRA'";

    return $query;
  }
  
  //ambil data afd dari TM_LOCATION_DIST_VRA
  public function getAfd($params = array())
  {
    $query = "
      SELECT 
           location.LOCATION_CODE,
           location.DESCRIPTION
        FROM TM_LOCATION_DIST_VRA location
           LEFT JOIN TM_ORGANIZATION B
            ON location.BA_CODE = B.BA_CODE
       WHERE location.DELETE_USER IS NULL 
        AND location.LOCATION_CODE NOT IN ('BIBITAN','BASECAMP','UMUM','LAIN')";
    /*
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER(B.REGION_CODE) LIKE UPPER('%".$this->_siteCode."%')";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER(location.BA_CODE) LIKE UPPER('%".$this->_siteCode."%')";
    }*/
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(location.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
      ";
    }else{
      $query .= "
        AND to_char(location.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
        AND UPPER(location.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
      ";
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(location.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(location.BA_CODE) LIKE UPPER('%".$params['search']."%')          
        )
      ";
    }
    
    $query .= "
      ORDER BY location.LOCATION_CODE
    ";

    //echo $query;

    return $query;
  }
  
  //menampilkan list norma alat kerja non panen
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
    
    

    $rows = $this->_db->fetchAll("{$begin} {$this->getDataHeader($params)} {$end}");
    $sql = "SELECT COUNT(*) FROM ({$this->getDataHeader($params)})";
    $result['countHeader'] = $this->_db->fetchOne($sql);
    
    $rowsAfd = $this->_db->fetchAll("{$this->getDataAfdeling($params)}");
    $sql = "SELECT COUNT(*) FROM ({$this->getDataAfdeling($params)})";
    $result['countData'] = $this->_db->fetchOne($sql);
    
    $tabs = $this->_db->fetchAll("{$this->getAfd($params)}");
    $sql = "SELECT COUNT(*) FROM ({$this->getAfd($params)})";
    $result['countAfd'] = $this->_db->fetchOne($sql);
    
    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $result['rows'][] = $row;
      }
    }
    
    if (!empty($rowsAfd)) {
      foreach ($rowsAfd as $idx => $row) {
        $result['rowsAfd'][$row['TRX_CODE']][] = $row;
      }
    }
    
    if (!empty($tabs)) {
      foreach ($tabs as $idx => $tab) {
        $result['tabs'][] = $tab;
      }
    }

    return $result;
  }
  
  //menampilkan list norma alat kerja non panen
  public function getListAfd($params = array())
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getAfd($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getAfd($params)} {$end}");
    
    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $result['rows'][] = $row;
      }
    }

    return $result;
  }
  
  //simpan data
  public function save($row = array(), $rowAfd = array(), $ba_code)
  { 
    $result = true;
    
    // ********************************************** UPDATE NORMA DISTRIBUSI VRA **********************************************
    $arrKeys=array_keys($rowAfd); 
    if ($row['TRX_CODE']){
    //konfirm by doni 130716, 1 trx_code setiap vra memiliki record walaupun tidak aktif di afd itu maka nilainya 0 di database. 
    //sehingga tidak perlu insert, tidak terkecuali afd bibitan, basecamp, umum dan lain    
        for($x=0;$x<count($rowAfd);$x++){//update afd termasuk bibitan, basecamp, umum, lain
          if ($rowAfd[$arrKeys[$x]]) {
            $sql = "UPDATE TR_RKT_VRA_DISTRIBUSI
                SET HM_KM   = ".addslashes($rowAfd[$arrKeys[$x]]).",
                PRICE_HM_KM = (".addslashes($rowAfd[$arrKeys[$x]])."*PRICE_QTY_VRA),
                UPDATE_USER = '{$this->_userName}',
                UPDATE_TIME = SYSDATE
               WHERE TRX_CODE = '{$row['TRX_CODE']}' AND
                LOCATION_CODE = '{$arrKeys[$x]}'";
            
            $this->_global->createSqlFile($row['filename'], $sql);
          }
        }
        //log DB
        $this->_global->insertLog('UPDATE SUCCESS', 'NORMA DISTRIBUSI VRA', '', '');
      
    }else{
      
        $oldChkVra="";
        $price_qty_vra=$this->getVraPrice($row,$ba_code);
        $rand = substr(md5(microtime()),rand(0,26),5);
        //$trx_code=substr($this->_period,-4).$ba_code.'VRA'.date('Ymd').$rand;
        $trx_code = $this->_formula->gen_TransactionCode(substr($this->_period,-4),$ba_code,'VRA'); //doni
        for($x=0;$x<count($rowAfd);$x++){
          if ($rowAfd[$arrKeys[$x]]){
            $sql = "INSERT INTO TR_RKT_VRA_DISTRIBUSI (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, LOCATION_CODE, HM_KM, PRICE_QTY_VRA, PRICE_HM_KM, 
                  TRX_CODE,TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
                VALUES (
                    TO_DATE('{$this->_period}','DD-MM-RRRR'),
                    '".addslashes($ba_code)."',
                    '".addslashes($row['ACTIVITY_CODE'])."',
                    '".addslashes($row['VRA_CODE'])."',
                    '".$arrKeys[$x]."',
                    ".addslashes($rowAfd[$arrKeys[$x]]).",
                    $price_qty_vra,
                    $price_qty_vra*".addslashes($rowAfd[$arrKeys[$x]]).",
                    '{$trx_code}',
                    'INFRA',
                    '{$this->_userName}',
                    SYSDATE)";
            
            $this->_global->createSqlFile($row['filename'], $sql);
          }
        }
        //log DB
        $this->_global->insertLog('INSERT SUCCESS', 'NORMA DISTRIBUSI VRA', '', '');
      
    }
    // ********************************************** END OF UPDATE NORMA DISTRIBUSI VRA **********************************************
    
    return $result;
  }
  
  public function getVraPrice($row,$ba_code){
    $sql="
      SELECT value 
      FROM TR_RKT_VRA_SUM 
      WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}' AND
        BA_CODE='{$ba_code}' AND
        VRA_CODE='{$row['VRA_CODE']}'";
     $vraPrice = $this->_db->fetchOne($sql);
     if($vraPrice=="") $vraPrice=0;
     return $vraPrice;
  }
  
  //update summary
  public function updateSummaryNormaDistribusiVra($ba_code)
  {
    //select data yg ada di VRA DISTRIBUSI
    $sql = "
      SELECT DISTINCT BA_CODE, ACTIVITY_CODE, VRA_CODE
      FROM TR_RKT_VRA_DISTRIBUSI
      WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
        AND TIPE_TRANSAKSI = 'INFRA'
        AND BA_CODE={$ba_code}";
    $rows = $this->_db->fetchAll($sql);
    
    foreach ($rows as $idx => $row) {
      try {
        //hapus data yang ada
        $sqlDelete = "
          DELETE FROM TR_RKT_VRA_DISTRIBUSI_SUM
          WHERE BA_CODE = '".addslashes($row['BA_CODE'])."' 
            AND ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."' 
            AND VRA_CODE = '".addslashes($row['VRA_CODE'])."'  
            AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
            AND TIPE_TRANSAKSI='INFRA'";
        //log file penghapusan data
        $this->_global->deleteDataLogFile($sqlDelete);
        
        $this->_db->query($sqlDelete);              
      } catch (Exception $e) {
        
      }
      
      ///////////////////////////// SUMMARY TOTAL HARGA ALAT KERJA NON PANEN /////////////////////////////
      $sql = "
        SELECT SUM(HM_KM) TOTAL_HM_KM, SUM(PRICE_HM_KM) TOTAL_PRICE_HM_KM
        FROM TR_RKT_VRA_DISTRIBUSI
        WHERE ACTIVITY_CODE = '".addslashes($row['ACTIVITY_CODE'])."'
          AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
          AND BA_CODE = '".addslashes($row['BA_CODE'])."'
          AND VRA_CODE = '".addslashes($row['VRA_CODE'])."' 
          AND DELETE_USER IS NULL
          AND TIPE_TRANSAKSI='INFRA'";
      $arrValue = $this->_db->fetchAll($sql);
      
        //insert DB
        $sql = "
          INSERT INTO TR_RKT_VRA_DISTRIBUSI_SUM (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, TOTAL_HM_KM, TOTAL_PRICE_HM_KM,TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME)
          VALUES (
            TO_DATE('{$this->_period}','DD-MM-RRRR'),
            '".addslashes($row['BA_CODE'])."',
            '".addslashes($row['ACTIVITY_CODE'])."',
            '".addslashes($row['VRA_CODE'])."',
            '".$arrValue[0]['TOTAL_HM_KM']."',
            '".$arrValue[0]['TOTAL_PRICE_HM_KM']."',
            'INFRA',
            '{$this->_userName}',
            SYSDATE );
          ";
        $this->_global->createSqlFile($row['filename'], $sql);
      
      //$this->updateInheritanceData($row);
    }
  }
  
  //hapus data
  public function delete($trxcode)
  {
    $result = true;
    
      $sql = "UPDATE TR_RKT_VRA_DISTRIBUSI
          SET DELETE_USER = '{$this->_userName}',
            DELETE_TIME = SYSDATE
           WHERE TRX_CODE = '{$trxcode}'";
      $this->_global->createSqlFile($row['filename'], $sql);

    return $result;
  }
  
  public function updateInheritanceData($row = array())
  {     
    $result = true;
    
    // ********************************************** UPDATE RKT PUPUK - DIST BIAYA NORMAL **********************************************
    if($row['ACTIVITY_CODE'] == '43750' || $row['ACTIVITY_CODE'] == '43760'){
      //reset param
      $param = array();
      
      $model = new Application_Model_RktPupukDistribusiBiayaNormal();
        
      //set parameter sesuai data yang diupdate
      $param['key_find'] = $row['BA_CODE'];
      
      try { 
        $model->calculateAllItem($param);
        
        //log DB
        $this->_global->insertLog('UPDATE SUCCESS', 'RKT PUPUK - DIST BIAYA NORMAL', '', 'UPDATED FROM NORMA DIST VRA');
      } catch (Exception $e) {
        //log DB
        $this->_global->insertLog('UPDATE FAILED', 'RKT PUPUK - DIST BIAYA NORMAL', '', $e->getCode());
        
        //error log file
        $this->_global->errorLogFile($e->getMessage());
        
        //return value
        $result = false;
      }   
    }
    // ********************************************** END OF UPDATE RKT PUPUK - DIST BIAYA NORMAL **********************************************
    
    // ********************************************** UPDATE RKT PUPUK - DIST BIAYA SISIP **********************************************
    if($row['ACTIVITY_CODE'] == '43750' || $row['ACTIVITY_CODE'] == '43760'){
      //reset param
      $param = array();
      
      $model = new Application_Model_RktPupukDistribusiBiayaSisip();
        
      //set parameter sesuai data yang diupdate
      $param['key_find'] = $row['BA_CODE'];
      
      try { 
        $model->calculateAllItem($param);
        
        //log DB
        $this->_global->insertLog('UPDATE SUCCESS', 'RKT PUPUK - DIST BIAYA SISIP', '', 'UPDATED FROM NORMA DIST VRA');
      } catch (Exception $e) {
        //log DB
        $this->_global->insertLog('UPDATE FAILED', 'RKT PUPUK - DIST BIAYA SISIP', '', $e->getCode());
        
        //error log file
        $this->_global->errorLogFile($e->getMessage());
        
        //return value
        $result = false;
      }   
    }
    // ********************************************** END OF UPDATE RKT PUPUK - DIST BIAYA SISIP **********************************************
    return $result;
  }

  // yaddi.surahman@tap-agri.co.id
  // query baru untuk report menggantikan query sebelumnya
  function getData2($params) {
    $sql = "SELECT DISTINCT AFD_CODE FROM TM_HECTARE_STATEMENT WHERE EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
      AND BA_CODE = '".$params['key_find']."' ORDER BY AFD_CODE";
    $afd_codes = $this->_db->fetchAll($sql);

    foreach ($afd_codes as $key) {
      $afdelings .= $key['AFD_CODE']."' as \"".$key['AFD_CODE']."\",'";
      # code...
    }
    $afdelings = "'".substr($afdelings, 0, -2);

    $sql = "
      SELECT * FROM (
        SELECT S.*, SUM(S.HM_KM) OVER (PARTITION BY S.PERIOD_BUDGET,S.BA_CODE,S.ACTIVITY_CODE,S.VRA_CODE) SUB_TOTAL
        , F_GET_VRA_COST(S.PERIOD_BUDGET,S.BA_CODE,S.ACTIVITY_CODE,S.VRA_CODE) PRICE_TOTAL 
        FROM (
          SELECT DISTINCT
            VHD.PERIOD_BUDGET
            , VHD.BA_CODE, ACT.ACTIVITY_CODE, ACT.DESCRIPTION
            , VHD.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION, VH.UOM
            , VHD.LOCATION_CODE, VHD.HM_KM
          FROM TR_RKT_VRA_DISTRIBUSI VHD
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = VHD.ACTIVITY_CODE
          JOIN TM_VRA VH ON VH.VRA_CODE = VHD.VRA_CODE
          LEFT JOIN TR_RKT RK ON RK.PERIOD_BUDGET = VHD.PERIOD_BUDGET AND RK.BA_CODE = VHD.BA_CODE 
            AND RK.ACTIVITY_CODE = VHD.ACTIVITY_CODE AND RK.AFD_CODE = VHD.LOCATION_CODE
          WHERE EXTRACT(YEAR FROM VHD.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND VHD.BA_CODE = '".$params['key_find']."'
          AND VHD.TIPE_TRANSAKSI = 'INFRA'
          AND VHD.ACTIVITY_CODE NOT IN (
            SELECT DISTINCT ACTIVITY_CODE FROM TR_RKT_PK WHERE 
            EXTRACT(YEAR FROM PERIOD_BUDGET) = '".$params['budgetperiod']."'
            AND BA_CODE = '".$params['key_find']."'
          )
        ) S
      )
      PIVOT (
        SUM(HM_KM) FOR LOCATION_CODE IN (".$afdelings.")
      )
      UNION
      SELECT * FROM (
        SELECT S.*, SUM(S.HM_KM) OVER (PARTITION BY S.PERIOD_BUDGET,S.BA_CODE,S.ACTIVITY_CODE,S.VRA_CODE) SUB_TOTAL
        , F_GET_VRA_COST(S.PERIOD_BUDGET,S.BA_CODE,S.ACTIVITY_CODE,S.VRA_CODE) PRICE_TOTAL 
        FROM (
          SELECT DISTINCT
            VHD.PERIOD_BUDGET
            , VHD.BA_CODE, ACT.ACTIVITY_CODE, ACT.DESCRIPTION
            , VHD.VRA_CODE, VH.VRA_SUB_CAT_DESCRIPTION, VH.UOM
            , VHD.LOCATION_CODE, VHD.HM_KM
          FROM TR_RKT_VRA_DISTRIBUSI VHD
          JOIN TM_ACTIVITY ACT ON ACT.ACTIVITY_CODE = VHD.ACTIVITY_CODE
          JOIN TM_VRA VH ON VH.VRA_CODE = VHD.VRA_CODE
          LEFT JOIN TR_RKT_PK RH ON RH.ACTIVITY_CODE = VHD.ACTIVITY_CODE AND RH.PERIOD_BUDGET = VHD.PERIOD_BUDGET
            AND RH.AFD_CODE = VHD.LOCATION_CODE 
            AND RH.SUMBER_BIAYA = 'INTERNAL'
            AND RH.JENIS_PEKERJAAN = 'PERULANGAN'
            AND RH.BA_CODE = VHD.BA_CODE
          WHERE EXTRACT(YEAR FROM VHD.PERIOD_BUDGET) = '".$params['budgetperiod']."'
          AND VHD.BA_CODE = '".$params['key_find']."'
          AND VHD.TIPE_TRANSAKSI = 'INFRA'
        ) S
      )
      PIVOT (
        SUM(HM_KM) FOR LOCATION_CODE IN (".$afdelings.")
      )
    ";

    $report = $this->_db->fetchAll($sql);
    return $report;
  }
}

