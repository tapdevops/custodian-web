<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Model Class untuk Norma VRA yang Dapat Dipinjam
Function 			:	- getInput					: YIR 24/07/2014	: setting input untuk region
						- getData					: SID 24/07/2014	: ambil data dari DB
						- getList					: SID 24/07/2014	: menampilkan list norma VRA
						- save						: SID 24/07/2014	: simpan data
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	24/07/2014
Update Terakhir		:	24/07/2014
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaSph
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
	
	//setting input untuk region
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
			SELECT ROWIDTOCHAR (ROWID) row_id,
				   ROWNUM,
				   CORE,
				   LAND_TYPE,
				   TOPOGRAPHY,
				   SPH_STANDAR,
				   FLAG_TEMP
			 FROM TN_SPH 
			 WHERE DELETE_USER IS NULL
        ";
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}

		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(CORE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(LAND_TYPE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(TOPOGRAPHY) LIKE UPPER('%".$params['search']."%')
					OR UPPER(SPH_STANDAR) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY CORE, LAND_TYPE, TOPOGRAPHY
		";
		
		return $query;
	}
	
	//menampilkan list norma VRA
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
	
	//simpan data
	public function save($row = array())
    { 
        $sql = "
			UPDATE TN_SPH
			SET SPH_STANDAR =  REPLACE('{$row['SPH_STANDAR']}',',',''),
				FLAG_TEMP = '',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
	
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
	//simpan data sementara
	public function saveTemp($row = array())
    { 
        $sql = "
			UPDATE TN_SPH
			SET SPH_STANDAR =  REPLACE('{$row['SPH_STANDAR']}',',',''),
				FLAG_TEMP = 'Y',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
		";
			 
		$this->_global->createSqlFile($row['filename'], $sql);
		return true;
    }
	
}

