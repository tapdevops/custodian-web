<?php
/*
=========================================================================================================================
Project       :   Budgeting & Planning System
Versi       :   2.0.0
Deskripsi     :   Model Class untuk Norma Pupuk TM
Function      : - getInput          : YIR 20/06/2014  : setting input untuk region dan maturity stage
            - getData         : SID 24/06/2013  : ambil data dari DB
            - getList         : SID 24/06/2013  : menampilkan list norma Pupuk TM
            - save            : SID 24/06/2013  : simpan data
            - delete          : SID 24/06/2013  : hapus data
Disusun Oleh    :   IT Enterprise Solution - PT Triputra Agro Persada
Developer     :   Sabrina Ingrid Davita
Dibuat Tanggal    :   24/06/2013
Update Terakhir   : 02/07/2014
Revisi        : 
  SID 02/07/2014  :   - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
              pada function save, delete
=========================================================================================================================
*/
class Application_Model_NormaPupukTbmRekomendasi
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
      SELECT ROWIDTOCHAR(norma.ROWID) row_id, 
           to_char(norma.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
           norma.BA_CODE, 
           norma.AFD_CODE,  
           norma.BLOCK_CODE,
           ha_statement.BLOCK_DESC,  
           norma.JENIS_TANAM,  
           norma.POKOK,  
           norma.BULAN_PEMUPUKAN,  
           norma.MATERIAL_CODE,  
           norma.DOSIS,  
           norma.JUMLAH,  
           norma.BIAYA,
           norma.HA_PUPUK,
           material.MATERIAL_NAME,
           norma_harga.PRICE,
           ha_statement.HA_PLANTED,
           to_char(ha_statement.TAHUN_TANAM,'MM.RRRR') TAHUN_TANAM,
           to_char(ha_statement.TAHUN_TANAM,'RRRR') TAHUN_TANAM_Y,
           to_char(ha_statement.TAHUN_TANAM,'MM') TAHUN_TANAM_M,
           ha_statement.MATURITY_STAGE_SMS1,
           ha_statement.MATURITY_STAGE_SMS2,
           norma.FLAG_TEMP
      FROM TN_PUPUK_TBM2_TM norma
      LEFT JOIN TM_MATERIAL material
        ON material.MATERIAL_CODE = norma.MATERIAL_CODE
        AND material.PERIOD_BUDGET = norma.PERIOD_BUDGET
        AND material.BA_CODE = norma.BA_CODE
      LEFT JOIN TN_HARGA_BARANG norma_harga
        ON material.PERIOD_BUDGET = norma_harga.PERIOD_BUDGET
        AND material.BA_CODE = norma_harga.BA_CODE
        AND material.MATERIAL_CODE = norma_harga.MATERIAL_CODE
      LEFT JOIN TM_HECTARE_STATEMENT ha_statement
        ON norma.BA_CODE  = ha_statement.BA_CODE
        AND norma.PERIOD_BUDGET = ha_statement.PERIOD_BUDGET
        AND norma.AFD_CODE = ha_statement.AFD_CODE
        AND norma.BLOCK_CODE = ha_statement.BLOCK_CODE
      LEFT JOIN TM_ORGANIZATION ORG
        ON norma.BA_CODE = ORG.BA_CODE
      WHERE norma.DELETE_USER IS NULL
    AND (HA_STATEMENT.MATURITY_STAGE_SMS1 != 'TM' AND HA_STATEMENT.MATURITY_STAGE_SMS1 != 'TM')
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
        AND UPPER(norma.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
      ";
    }
    
    if (($params['src_matstage_code']) && ($params['src_matstage_code'] != '0')) {
      $query .= "
        AND (
          UPPER(ha_statement.MATURITY_STAGE_SMS1) LIKE UPPER('%".$params['src_matstage_code']."%')
          OR UPPER(ha_statement.MATURITY_STAGE_SMS2) LIKE UPPER('%".$params['src_matstage_code']."%')
        )
      ";
    }
    
    if ($params['controller'] == 'download') {
      $params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
    }
    
    if ($params['search'] != '') {
      $query .= "
        AND (
          UPPER(norma.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BA_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(material.MATERIAL_NAME) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma_harga.PRICE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.AFD_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BLOCK_CODE) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.JENIS_TANAM) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.POKOK) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BULAN_PEMUPUKAN) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.DOSIS) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.JUMLAH) LIKE UPPER('%".$params['search']."%')
          OR UPPER(norma.BIAYA) LIKE UPPER('%".$params['search']."%')
        )
      ";
    }
    
    //untuk inheritance
    if ($params['sub_cost_element'] != '') {
      $query .= "
        AND UPPER(norma.MATERIAL_CODE) LIKE UPPER('%".$params['sub_cost_element']."%')
      ";
    }
    
    $query .= "
      ORDER BY norma.PERIOD_BUDGET, norma.BA_CODE, norma.JENIS_TANAM, norma.BULAN_PEMUPUKAN
    ";
    return $query;
  }
  
  //menampilkan list norma Pupuk TM
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
  
  //simpan data
  public function save($row = array())
  { 
    $jumlah = $this->_formula->cal_NormaPupukTbmTm_JumlahKg($row);
    $biaya = $this->_formula->cal_NormaPupukTbmTm_Biaya($row);
    
    $sql = "
      UPDATE TN_PUPUK_TBM2_TM
      SET HA_PUPUK = REPLACE('{$row['HA_PUPUK']}',',',''),
        POKOK = REPLACE('{$row['POKOK']}',',',''),
        DOSIS = REPLACE('{$row['DOSIS']}',',',''),
        JUMLAH = REPLACE('{$jumlah}',',',''),
        HARGA = REPLACE('{$row['PRICE']}',',',''),
        BIAYA = REPLACE('{$biaya}',',',''),
        FLAG_TEMP = NULL,
        TRIGGER_UPDATE = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);    
    return true;
  }
  
  //simpan data temp
  public function saveTemp($row = array())
  { 
    $sql = "
      UPDATE TN_PUPUK_TBM2_TM
      SET HA_PUPUK = REPLACE('{$row['HA_PUPUK']}',',',''),
        POKOK = REPLACE('{$row['POKOK']}',',',''),
        DOSIS = REPLACE('{$row['DOSIS']}',',',''),
        JUMLAH = 0,
        HARGA = REPLACE('{$row['PRICE']}',',',''),
        BIAYA = 0,
        FLAG_TEMP = 'Y',
        TRIGGER_UPDATE = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);    
    return true;
  }
  
  //hapus data
  public function delete($row = array())
  {
    $sql = "
      UPDATE TN_PUPUK_TBM2_TM
      SET DELETE_USER = '{$this->_userName}',
        DELETE_TIME = SYSDATE
      WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
    ";
    
    //create sql file
    $this->_global->createSqlFile($row['filename'], $sql);    
    return true;
  }
  
  //kalkulasi data saat upload
  public function calculateData($row = array())
  { 
    $jumlah = $this->_formula->cal_NormaPupukTbmTm_JumlahKg($row);
    $biaya = $this->_formula->cal_NormaPupukTbmTm_Biaya($row);
    
    $sql = "
      UPDATE TN_PUPUK_TBM2_TM
      SET HA_PUPUK = REPLACE('{$row['HA_PUPUK']}',',',''),
        POKOK = REPLACE('{$row['POKOK']}',',',''),
        DOSIS = REPLACE('{$row['DOSIS']}',',',''),
        JUMLAH = REPLACE('{$jumlah}',',',''),
        HARGA = REPLACE('{$row['PRICE']}',',',''),
        BIAYA = REPLACE('{$biaya}',',',''),
        FLAG_TEMP = NULL,
        TRIGGER_UPDATE = NULL,
        UPDATE_USER = '{$this->_userName}',
        UPDATE_TIME = SYSDATE
      WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}'
    ";
    $this->_db->query($sql);
    $this->_db->commit();
        
    return true;
  }
}

