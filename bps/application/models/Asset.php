<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Asset
Function 			:	- getData			: ambil data dari DB
						- getList			: menampilkan list aset
						- save				: simpan data
						- delete			: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	12/06/2013

Revisi	||	PIC				||	TANGGAL			||	DESKRIPSI 		
1		||	Doni R			||	12-06-2013		||	Perbaikan query untuk menampilkan data berdasarkan region code
2			SABRINA				19/06/2013			MENAMBAHKAN PENGIRMAN DATA REFERENCE_ROLE UNTUK VALIDASI FILTERING
=========================================================================================================================
*/
class Application_Model_Asset
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
			'style'   => 'width:200px;'
        );

        return $result;
    }
	
	//ambil data dari DB
    public function getData($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR(aset.ROWID) row_id, to_char(aset.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, aset.BA_CODE, aset.ASSET_CODE, aset.DESCRIPTION, 
				   aset.COA_CODE, aset.UOM, aset.STATUS, aset.PRICE, aset.BASIC_NORMA_CODE, basic.DESCRIPTION BASIC_NORMA_DESC, 
				   basic.PERCENT_INCREASE, coa.DESCRIPTION COA_DESC, aset.FLAG_TEMP
            FROM TM_ASSET aset
			LEFT JOIN TN_BASIC basic
				ON aset.PERIOD_BUDGET = basic.PERIOD_BUDGET
				AND aset.BA_CODE = basic.BA_CODE
				AND aset.BASIC_NORMA_CODE = basic.BASIC_NORMA_CODE
			LEFT JOIN TM_COA coa
				ON aset.COA_CODE = coa.COA_CODE
			LEFT JOIN TM_ORGANIZATION ORG
				ON aset.BA_CODE = ORG.BA_CODE
			WHERE aset.DELETE_USER IS NULL
        ";
		
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(aset.BA_CODE)||'%'";
		}
		
		if($params['budgetperiod'] != ''){
			$query .= "
                AND to_char(aset.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
		}else{
			$query .= "
                AND to_char(aset.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
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
                AND UPPER(aset.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
		
		if ($params['controller'] == 'download') {
			$params['search'] = ($params['search'] == 'AA==') ? "" : rawurldecode(base64_decode($params['search']));
		}
		
		if ($params['search'] != '') {
			$query .= "
				AND (
					UPPER(aset.PERIOD_BUDGET) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.BA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.ASSET_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.COA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.UOM) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.STATUS) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.PRICE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(aset.BASIC_NORMA_CODE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(basic.PERCENT_INCREASE) LIKE UPPER('%".$params['search']."%')
					OR UPPER(coa.DESCRIPTION) LIKE UPPER('%".$params['search']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY  aset.BA_CODE,aset.ASSET_CODE, aset.COA_CODE
		";
		
		return $query;
	}
	
	//menampilkan list aset
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
	
	//cek deleted data
	public function getDeletedRec($params = array())
    {
        $result = array();
        $sql = "
			SELECT ROWIDTOCHAR(ROWID) ROW_ID 
			FROM TM_ASSET 
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
				AND BA_CODE = '{$params['BA_CODE']}'
				AND ASSET_CODE = '{$params['ASSET_CODE']}'
		";        
        $rows = $this->_db->fetchOne($sql);
        return $rows;
    }
	
	//simpan data
	public function save($row = array())
    { 
        $result = true;
		//cek data tsb sudah pernah ada & dihapus atau benar2 data baru
		if(!$row['ROW_ID']) $row['ROW_ID'] = $this->getDeletedRec($row);
		
			$sql = "
				UPDATE TM_ASSET
				SET  
					DESCRIPTION = '".addslashes($row['DESCRIPTION'])."',
					COA_CODE = '".addslashes($row['COA_CODE'])."',
					UOM = '".addslashes($row['UOM'])."',
					STATUS = '".addslashes($row['STATUS'])."',
					PRICE = REPLACE('".addslashes($row['PRICE'])."', ',', ''),
					BASIC_NORMA_CODE = '".addslashes($row['BASIC_NORMA_CODE'])."',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP=NULL
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return true;
    }
	
	public function saveTemp($row = array())
    { 
        $result = true;
		
			$sql = "
				UPDATE TM_ASSET
				SET  
					DESCRIPTION = '".addslashes($row['DESCRIPTION'])."',
					COA_CODE = '".addslashes($row['COA_CODE'])."',
					UOM = '".addslashes($row['UOM'])."',
					STATUS = '".addslashes($row['STATUS'])."',
					PRICE = REPLACE('".addslashes($row['PRICE'])."', ',', ''),
					BASIC_NORMA_CODE = '".addslashes($row['BASIC_NORMA_CODE'])."',
					UPDATE_USER = '{$this->_userName}',
					UPDATE_TIME = SYSDATE,
					DELETE_TIME = NULL,
					DELETE_USER = NULL,
					FLAG_TEMP='Y'
				 WHERE ROWIDTOCHAR(ROWID) = '{$row['ROW_ID']}';
			";
		$this->_global->createSqlFile($row['filename'], $sql);	
        return true;
    }
	
	//hapus data
	public function delete($rowid)
    {
		$result = true;
		
		try {
			$sql = "UPDATE TM_ASSET
					SET  
						DELETE_USER = '{$this->_userName}',
						DELETE_TIME = SYSDATE
					 WHERE ROWIDTOCHAR(ROWID) = '{$rowid}'";
			$this->_db->query($sql);
			$this->_db->commit();
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'MASTER ASET', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'MASTER ASET', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}

        return $result;
    }
}

