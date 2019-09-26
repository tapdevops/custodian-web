<?php
/*
=========================================================================================================================
Project       :   Estate Budget Preparation System
Versi       :   1.0.0
Deskripsi     :   Model Class untuk Norma Dasar
Function      : - getList     : menampilkan list norma dasar
            - save        : simpan data
            - delete      : hapus data
            - getInput      : setting input untuk region dan maturity stage
            - saveTemp
Disusun Oleh    :   IT Support Application - PT Triputra Agro Persada
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   26/04/2013
Update Terakhir   : 20/05/2014
Revisi        : 
- YULIUS 23/07/2014   : - TAMBAH FIELD FLAG TEMP
=========================================================================================================================
*/
class Application_Model_NormaInsentivePanen
{
  private $_db = null;
  private $_global = null;
  private $_siteCode = '';

  public function __construct()
  {
    $this->_db = Zend_Registry::get('db');
    $this->_global = new Application_Model_Global();
    $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
    $this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    $this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
    $this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : YIR - 08/08/2014

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
        SELECT ROWIDTOCHAR (A.ROWID) row_id,
             ROWNUM,
             TO_CHAR (A.PERIOD_BUDGET, 'RRRR') PERIOD_BUDGET,
             A.BA_CODE,
             A.PERCENTAGE_INCENTIVE_1,
             A.INCENTIVE_1,
             A.PERCENTAGE_INCENTIVE_2,
             A.INCENTIVE_2,
             A.PERCENTAGE_INCENTIVE_3,
             A.INCENTIVE_3,
             A.FLAG_TEMP
          FROM TN_INSENTIVE_PANEN A
          LEFT JOIN TM_ORGANIZATION B
              ON A.BA_CODE = B.BA_CODE
         WHERE A.DELETE_USER IS NULL
        ";
    
    if($this->_siteCode <> 'ALL'){
      if ($this->_referenceRole == 'REGION_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'  ";
      elseif ($this->_referenceRole == 'BA_CODE')
        $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE)||'%'";
    }
    
    if($params['budgetperiod'] != ''){
      $query .= "
        AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
      ";
    }elseif($params['PERIOD_BUDGET'] != ''){
      $query .= "
        AND to_char(A.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
      ";
    }else{
      $query .= "
        AND to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
        AND UPPER(A.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
      ";
    }
    
    if ($params['controller'] == 'download') {
      $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(A.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(A.BA_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(A.BASIC_NORMA_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(A.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
          OR UPPER(A.PERCENT_INCREASE) LIKE UPPER('%".$params['search']."%')
        )
      ";
    }
    
    $query .= "
      ORDER BY A.BA_CODE
    ";
    
    return $query;
  }
  
  //menampilkan list norma dasar
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
  
  //cek deleted data
  public function getDeletedRec($params = array())
  {
    $result = array();
    $sql = "
      SELECT ROWIDTOCHAR(ROWID) ROW_ID 
      FROM TN_INSENTIVE_PANEN 
      WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
        AND BA_CODE = '{$params['BA_CODE']}'
    ";        
    $rows = $this->_db->fetchOne($sql);
    return $rows;
  }
  
  //simpan data
  public function saveTemp($row = array())
  { 
    $result = true;
    //cek data tsb sudah pernah ada & dihapus atau benar2 data baru
    if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
    
    $sql = "
      UPDATE TN_INSENTIVE_PANEN
      SET  
        PERCENTAGE_INCENTIVE_1 = '".$row['']."',
        INCENTIVE_1 = '".$row['']."',
        PERCENTAGE_INCENTIVE_2 = '".$row['']."',
        INCENTIVE_2 = '".$row['']."',
        PERCENTAGE_INCENTIVE_3 = '".$row['']."',
        INCENTIVE_3 = '".$row['']."',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE,
        DELETE_TIME = NULL,
        DELETE_USER = NULL,
        FLAG_TEMP = 'Y'
       WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
    ";
         
    $this->_global->createSqlFile($row['filename'], $sql);
    return $result;
  }
  
  //simpan data
  public function save($row = array())
  { 
    $result = true;
    //cek data tsb sudah pernah ada & dihapus atau benar2 data baru
    if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
    
    $sql = "
      UPDATE TN_INSENTIVE_PANEN
      SET  
        PERCENTAGE_INCENTIVE_1 = '".$row['']."',
        INCENTIVE_1 = '".$row['']."',
        PERCENTAGE_INCENTIVE_2 = '".$row['']."',
        INCENTIVE_2 = '".$row['']."',
        PERCENTAGE_INCENTIVE_3 = '".$row['']."',
        INCENTIVE_3 = '".$row['']."',
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE,
        DELETE_TIME = NULL,
        DELETE_USER = NULL,
        FLAG_TEMP = NULL
       WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
    ";
         
    $this->_global->createSqlFile($row['filename'], $sql);
    return $result;
  }

  //hapus data
  public function delete($row = array())
  {
    $sql = "UPDATE TN_INSENTIVE_PANEN
        SET  
          DELETE_USER = '{$this->_userName}',
          DELETE_TIME = SYSDATE
         WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'";
    //$this->_db->query($sql);
      
    $this->_global->createSqlFile($row['filename'], $sql);
    return true;
  }
}

