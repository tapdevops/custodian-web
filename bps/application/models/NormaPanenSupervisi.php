<?php
/*
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Model Class untuk Norma Panen Supervisi
Function      : - getList         : menampilkan list norma Panen Supervisi
            - save            : simpan data
            - getInput      : setting input untuk region dan maturity stage
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   26/06/2013
Update Terakhir   : 26/06/2013
Revisi        : 
YULIUS 08/07/2014   : - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
              pada function save dan save temp
YULIUS 16/07/2014 : - set field FLAG_TEMP pada function getData, save, saveTemp             
=========================================================================================================================
*/
class Application_Model_NormaPanenSupervisi
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
      SELECT ROWIDTOCHAR (norma.ROWID) row_id,
           ROWNUM,
           TO_CHAR (norma.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
           norma.BA_CODE,
           ORG.COMPANY_NAME,
           norma.MIN_BJR, 
           norma.MAX_BJR, 
           (SELECT MAX(janjang.JANJANG_BASIS_MANDOR) JANJANG_BASIS_MANDOR
          FROM TN_PANEN_OER_BJR janjang
          WHERE janjang.PERIOD_BUDGET = norma.PERIOD_BUDGET
          AND janjang.BA_CODE = norma.BA_CODE
          AND janjang.BJR_MIN = norma.MIN_BJR
          AND janjang.BJR_MAX = norma.MAX_BJR) as JANJANG_BASIS_MANDOR,  
           (SELECT MAX(janjang.BJR_BUDGET) BJR_BUDGET
          FROM TN_PANEN_OER_BJR janjang
          WHERE janjang.PERIOD_BUDGET = norma.PERIOD_BUDGET
          AND janjang.BA_CODE = norma.BA_CODE
          AND janjang.BJR_MIN = norma.MIN_BJR
          AND janjang.BJR_MAX = norma.MAX_BJR) as BJR_BUDGET,  
           norma.OVER_BASIS_JANJANG,  
           norma.JANJANG_OPERATION,  
           norma.RP_KG,
           norma.FLAG_TEMP,
           var.VALUE ASUMSI_OVER_BASIS,
           (SELECT AVG(checkroll.RP_HK) RP_HK
          FROM TR_RKT_CHECKROLL_SUM checkroll
          WHERE norma.PERIOD_BUDGET = checkroll.PERIOD_BUDGET
          AND norma.BA_CODE = checkroll.BA_CODE
          AND checkroll.JOB_CODE IN ('FX140', 'FX230')) AVG_MANDOR,
           (SELECT target.VALUE
          FROM TN_PANEN_VARIABLE target
          WHERE target.PERIOD_BUDGET = norma.PERIOD_BUDGET
          AND target.BA_CODE = norma.BA_CODE
          AND target.PANEN_CODE = 'PRES_RATIO_PER_PEMANEN') as RATIO_PEMANEN
      FROM TN_PANEN_SUPERVISI norma
      LEFT JOIN TN_PANEN_VARIABLE var
        ON norma.PERIOD_BUDGET = var.PERIOD_BUDGET
        AND norma.BA_CODE = var.BA_CODE
        AND var.PANEN_CODE = 'ASUM_OVR_BASIS'
      LEFT JOIN TM_ORGANIZATION ORG
          ON norma.BA_CODE = ORG.BA_CODE
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
        AND UPPER(norma.BA_CODE) IN ('".$params['key_find']."')
      ";
    }
    
    if ($params['controller'] == 'download') {
      $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
    }
    
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
      ";
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.MIN_BJR) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.MAX_BJR) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.JANJANG_BASIS) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BJR_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.OVER_BASIS_JANJANG) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.JANJANG_OPERATION) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.RP_KG) LIKE UPPER('%".$params['search']."%')
          OR UPPER(var.VALUE) LIKE UPPER('%".$params['search']."%')
        )
      ";
    }
    
    $query .= "
      ORDER BY norma.MIN_BJR, norma.MAX_BJR
    ";
    return $query;
  }
  
  //menampilkan list norma Panen Supervisi
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

  /**
   * yaddi.surahman@tap-agri.co.id
   * 2017-08-09
   */
  public function getList_2018($params)
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
    
    $sql = "SELECT COUNT(*) FROM ({$this->getData_2018($params)})";
    $result['count'] = $this->_db->fetchOne($sql);

    $rows = $this->_db->fetchAll("{$begin} {$this->getData_2018($params)} {$end}");

    if (!empty($rows)) {
      foreach ($rows as $idx => $row) {
        $result['rows'][] = $row;
      }
    }
    return $result;
  }
  
  /**
   * yaddi.surahman@tap-agri.co.id
   * 2017-08-09
   */
  public function getData_2018($params = array())
  {
    $query = "SELECT ROWIDTOCHAR (NORMA.ROWID) row_id, ROWNUM, TO_CHAR (NORMA.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
              NORMA.BA_CODE, NORMA.BJR_MIN,  NORMA.BJR_MAX, NORMA.PREMI_MANDOR_PANEN, NORMA.PREMI_MANDOR_1
              FROM TN_PANEN_SUPERVISI_2017 NORMA 
              LEFT JOIN TM_ORGANIZATION ORG ON NORMA.BA_CODE = ORG.BA_CODE
              WHERE NORMA.DELETE_USER IS NULL ";
     
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(NORMA.BA_CODE)||'%'";
    }
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(NORMA.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(NORMA.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
      ";
    }else{
      $query .= "
        AND to_char(NORMA.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
        AND UPPER(NORMA.BA_CODE) IN ('".$params['key_find']."')
      ";
    }
    
    if ($params['controller'] == 'download') {
      $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
    }
    
    //jika diupdate dari norma VRA, filter berdasarkan BA
    if ($params['BA_CODE'] != '') {
      $query .= "
        AND UPPER(NORMA.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
      ";
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(NORMA.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(NORMA.BA_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(NORMA.BJR_MIN) LIKE UPPER('%".$params['search']."%')
          OR UPPER(NORMA.BJR_MAX) LIKE UPPER('%".$params['search']."%')
          OR UPPER(NORMA.PREMI_MANDOR_PANEN) LIKE UPPER('%".$params['search']."%')
          OR UPPER(NORMA.PREMI_MANDOR_1) LIKE UPPER('%".$params['search']."%')
        )
      ";
    }
    
    $query .= "
      ORDER BY NORMA.BJR_MIN, NORMA.BJR_MAX
    ";

    return $query;
  }

  //simpan data
  public function save($row = array())
  { 
    $result = true;
    
    $over_basis_janjang = $this->_formula->cal_NormaPanenOerBjr_OverBasisJanjang($row);
    $row['OVER_BASIS_JANJANG'] = $over_basis_janjang;
    $janjang_operation = $this->_formula->cal_NormaPanenOerBjr_JanjangOperation($row);
    $row['JANJANG_OPERATION'] = $janjang_operation;
    $rp_kg = $this->_formula->cal_NormaPanenSupervisi_RpKg($row);
      
      $sql = "UPDATE TN_PANEN_SUPERVISI
          SET BJR_BUDGET = REPLACE('".addslashes($row['BJR_BUDGET'])."', ',', ''),
            JANJANG_BASIS = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR'])."', ',', ''),
            JANJANG_OPERATION = '".addslashes($janjang_operation)."',
            OVER_BASIS_JANJANG = '".addslashes($over_basis_janjang)."',
            RP_KG  = '".addslashes($rp_kg)."',
            FLAG_TEMP = NULL,
            UPDATE_USER = '{$this->_userName}',
            UPDATE_TIME = SYSDATE
           WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
        ";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);    
    return true;
  }
  
  public function saveTemp($row = array())
  { 
    $result = true;
    
    $over_basis_janjang = $this->_formula->cal_NormaPanenOerBjr_OverBasisJanjang($row);
    $row['OVER_BASIS_JANJANG'] = $over_basis_janjang;
    $janjang_operation = $this->_formula->cal_NormaPanenOerBjr_JanjangOperation($row);
    $row['JANJANG_OPERATION'] = $janjang_operation;
    $rp_kg = $this->_formula->cal_NormaPanenSupervisi_RpKg($row);
      
      $sql = "UPDATE TN_PANEN_SUPERVISI
          SET BJR_BUDGET = REPLACE('".addslashes($row['BJR_BUDGET'])."', ',', ''),
            JANJANG_BASIS = REPLACE('".addslashes($row['JANJANG_BASIS_MANDOR'])."', ',', ''),
            JANJANG_OPERATION = '".addslashes($janjang_operation)."',
            OVER_BASIS_JANJANG = '".addslashes($over_basis_janjang)."',
            RP_KG  = '".addslashes($rp_kg)."',
            FLAG_TEMP = 'Y',
            UPDATE_USER = '{$this->_userName}',
            UPDATE_TIME = SYSDATE
           WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
        ";
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);    
    return true;
  }
  
  public function calculateAll($params = array())
  { 
    $result = true;
    $records1 = $this->_db->fetchAll("{$this->getData($params)}");
          
    foreach ($records1 as $idx1 => $record1) {
      $this->save($record1);
    }
    
    return $result;
  }
  
  public function updateInheritanceData($row = array())
  {     
    $result = true;
    
    // ********************************************** UPDATE RKT PANEN **********************************************
    //reset param
    $param = array();
    
    $model = new Application_Model_RktPanen();
      
    //set parameter sesuai data yang diupdate
    $param['key_find'] = $row['BA_CODE'];
    
    $records1 = $this->_db->fetchAll("{$model->getData($param)}");
    
    if (!empty($records1)) {
      try {   
        foreach ($records1 as $idx1 => $record1) {
          $model->save($record1);
        }
        
        //log DB
        $this->_global->insertLog('UPDATE SUCCESS', 'RKT PANEN', '', 'UPDATED FROM NORMA PANEN - SUPERVISI');
      } catch (Exception $e) {
        //log DB
        $this->_global->insertLog('UPDATE FAILED', 'RKT PANEN', '', $e->getCode());
        
        //error log file
        $this->_global->errorLogFile($e->getMessage());
        
        //return value
        $result = false;
      } 
    }   
    // ********************************************** END OF UPDATE RKT PANEN **********************************************
    
    return $result;
  }
}

