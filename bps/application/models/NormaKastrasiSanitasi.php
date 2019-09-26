<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Norma Kastrasi Sanitasi
Function 			:	- getList					: menampilkan list norma Kastrasi Sanitasi
						- save						: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	28/06/2013
Update Terakhir		:	28/06/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_NormaKastrasiSanitasi
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
    }
		
	//menampilkan list norma Kastrasi Sanitasi
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
        
		$query = "
            SELECT ROWIDTOCHAR (TKS.ROWID) row_id,
					ROWNUM, TKS.ACTIVITY_CODE,
					activity.DESCRIPTION AS ACTIVITY_DESC,
					TKS.LAND_SUITABILITY, 
					TKS.UMUR,
					TO_CHAR(TKS.PERIOD_BUDGET,'RRRR') AS PERIOD_BUDGET
			FROM TN_KASTRASI_SANITASI TKS
			LEFT JOIN TM_ACTIVITY activity
				ON TKS.ACTIVITY_CODE = activity.ACTIVITY_CODE
			WHERE TKS.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(TKS.ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(activity.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		if ($params['budgetperiod'] != '') {
			$query .= "
                AND TO_CHAR(PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        }
		
		$query .= "
			ORDER BY ACTIVITY_CODE
		";
		
        $sql = "SELECT COUNT(*) FROM ({$query})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$query} {$end}");
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $result['rows'][] = $row;
            }
        }
        return $result;
    }
	
	//untuk export data
	public function getData($params = array())
    {
		$query = "
            SELECT ROWIDTOCHAR (TKS.ROWID) row_id,
					ROWNUM, TKS.ACTIVITY_CODE,
					activity.DESCRIPTION AS ACTIVITY_DESC,
					TKS.LAND_SUITABILITY, 
					TKS.UMUR,
					TKS.PERIOD_BUDGET
			FROM TN_KASTRASI_SANITASI TKS
			LEFT JOIN TM_ACTIVITY activity
				ON TKS.ACTIVITY_CODE = activity.ACTIVITY_CODE
			WHERE TKS.DELETE_USER IS NULL
        ";
		
		if ($params['key_find'] != '') {
			$query .= "
                AND (
					UPPER(TKS.ACTIVITY_CODE) LIKE UPPER('%".$params['key_find']."%')
					OR UPPER(activity.DESCRIPTION) LIKE UPPER('%".$params['key_find']."%')
				)
            ";
        }
		
		$query .= "
			ORDER BY ACTIVITY_CODE
		";
		return $query;
	}	
	
	public function updateNormaKastrasiSanitasi($row = array())
    {
		$sql = "
			UPDATE TN_KASTRASI_SANITASI
			SET UMUR = '".addslashes($row['UMUR'])."',
				UPDATE_USER = '{$this->_userName}',
				UPDATE_TIME = SYSDATE
			WHERE ROWID = '".addslashes($row['ROW_ID'])."'	
		";
		
		$this->_db->query($sql);
		$this->_db->commit();
	}
	
}

