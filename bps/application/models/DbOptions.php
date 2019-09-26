<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk option yang sering digunakan
Function 			:	- 10/04	: getUserRole				: pilihan user role
						- 10/04	: getBACode					: pilihan BA code
						- 10/04	: getActiveStatus			: pilihan status aktif
						- 06/05	: getUsersAccessrights		: pilihan hak akses user
						- 18/06	: getPeriodBudgetStatus		: pilihan status periode budget
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2012
Update Terakhir		:	18/06/2012
Revisi				:	
=========================================================================================================================
*/
class Application_Model_DbOptions
{
    private $_db = null;
    private $_auth = null;
    private $_global = null;

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_auth = Zend_Registry::get('auth');
        $this->_global = new Application_Model_Global();
        if (!empty($this->_auth->getIdentity()->BA_CODE)) {
            $this->_siteCode = $this->_auth->getIdentity()->BA_CODE;
			$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE;
        }
		//$this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
    }
	
	 public function getCoaRelation($tipe){ //$tipe - she - csr
		$result = array();
		$sql = "
			SELECT DISTINCT master_relation.COA_CODE,
                  coa.DESCRIPTION,
                  master_relation.GROUP_CODE,
                  desc_group.PARAMETER_VALUE
				FROM TM_GROUP_RELATION master_relation
					 LEFT JOIN T_PARAMETER_VALUE desc_group
						ON desc_group.PARAMETER_VALUE_CODE = master_relation.GROUP_CODE
						   AND UPPER (desc_group.PARAMETER_CODE) = UPPER ('GROUP_$tipe')
					 LEFT JOIN TM_COA coa
						ON master_relation.COA_CODE = coa.COA_CODE
			   WHERE master_relation.DELETE_USER IS NULL
					 AND UPPER (master_relation.TYPE) = UPPER ('$tipe')
			ORDER BY COA_CODE ASC"; //die ($sql);
		$rows = $this->_db->fetchAll($sql);
		$result["0"] = "";
        foreach ($rows as $idx => $row) {
            $result["{$row['COA_CODE']} : {$row['GROUP_CODE']}"] = "{$row['COA_CODE']} : {$row['DESCRIPTION']} : {$row['GROUP_CODE']} : {$row['PARAMETER_VALUE']}";
        }
        return $result;
	 }
	
	 public function getRegion()
    {
        $result = array();
        if ($this->_referenceRole == 'REGION_CODE')
			$where1 .= " AND   '".$this->_siteCode."' LIKE  '%'||UPPER(REGION_CODE) ||'%'   ";
		elseif ($this->_referenceRole == 'BA_CODE')
			$where1 .= " AND '".$this->_siteCode."' LIKE  '%'|| UPPER(BA_CODE)  ||'%'   ";
		$sql = "
			SELECT  REGION_CODE, REGION_NAME
			FROM TM_ORGANIZATION
			WHERE BA_CODE <> 'ALL'
				AND DELETE_USER IS NULL
			{$where1}
			GROUP BY REGION_CODE, REGION_NAME
			ORDER BY REGION_CODE
		";
        $rows = $this->_db->fetchAll($sql);
		$result[""] = "";
        foreach ($rows as $idx => $row) {
            $result["{$row['REGION_CODE']}"] = "{$row['REGION_CODE']} - {$row['REGION_NAME']}";
        }
        return $result;
    }
	
	 public function getKastActivity()
    {
        $result = array();
        
		$sql = "
			SELECT AM.ACTIVITY_CODE, TA.DESCRIPTION, TA.UOM
				FROM TM_ACTIVITY_MAPPING AM 
				LEFT JOIN TM_ACTIVITY TA
					ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
				WHERE 1 = 1 AND UPPER(AM.UI_RKT_CODE) IN ('RKT023') 
		";
        $rows = $this->_db->fetchAll($sql);
		$result[""] = "";
        foreach ($rows as $idx => $row) {
            $result["{$row['ACTIVITY_CODE']}"] = "{$row['ACTIVITY_CODE']} - {$row['DESCRIPTION']} ({$row['UOM']})";
        }
        return $result;
    }
	
	/*
	
	*/
	
	 public function getMaturityStage()
    {
        $result = array();
        if ($params['module'] == 'normaPupukTbmTm'){
			$where1 .= " AND PARAMETER_VALUE_CODE IN ('TM', 'ALL')";
		}
				
		$sql = "
			SELECT DISTINCT PARAMETER_VALUE_CODE
			FROM T_PARAMETER_VALUE
			WHERE DELETE_USER IS NULL
				AND PARAMETER_CODE = 'MATURITY_STAGE'
			{$where1}
			ORDER BY PARAMETER_VALUE_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		$result["0"] = "";
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }
        return $result;
    }
	
	 public function getCoaCapex()
    {
        $result = array();
        $sql = "
            SELECT COA_CODE, UPPER(DESCRIPTION) AS DESCRIPTION
				FROM TM_COA
				WHERE DELETE_USER IS NULL
				AND UPPER(FLAG) = 'CAPEX'        
				ORDER BY DESCRIPTION ASC
        ";
        $rows = $this->_db->fetchAll($sql);
		$result["0"] = "";
        foreach ($rows as $idx => $row) {
            $result["{$row['COA_CODE']}"] = "{$row['COA_CODE']} - {$row['DESCRIPTION']}";
        }
        return $result;
    }
	
	 public function getRefRole()
    {
        $result = array();
        $sql = "
            SELECT PARAMETER_VALUE_CODE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'REF_ROLE'
				AND DELETE_USER IS NULL
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }
        return $result;
    }

	//pilihan user role
    public function getUserRole()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'USER_ROLE'
				AND DELETE_USER IS NULL
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }

        return $result;
    }

    //pilihan user role
    public function getHoUserRole()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'HO_USER_ROLE'
                AND DELETE_USER IS NULL
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE']}";
        }

        return $result;
    }

    //pilihan BA code
	public function getBACode()
    {
        $result = array();

        $sql = "
			SELECT * 
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
		";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['BA_CODE']}"] = $row['BA_CODE'];
        }

        return $result;
    }

    //pilihan HO Div Code
    public function getDivCode() {
        $result = array();

        $sql = "
            SELECT * 
            FROM TM_HO_DIVISION
            WHERE DELETE_USER IS NULL
            ORDER BY DIV_NAME
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['DIV_CODE']}"] = $row['DIV_CODE'] . ' - ' . $row['DIV_NAME'];
        }

        return $result;
    }

    // pilihan tipe akses
    public function getTipeAkses() {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE 
            FROM T_PARAMETER_VALUE 
            WHERE PARAMETER_CODE = 'TIPE_AKSES' 
            AND DELETE_USER IS NULL ORDER BY PARAMETER_VALUE DESC
        ";

        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = $row['PARAMETER_VALUE'];
        }

        return $result;
    }
	
	//pilihan status aktif
	public function getActiveStatus()
    {
        $result = array();

        $sql = "
			SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'ACTIVE_STATUS'
				AND DELETE_USER IS NULL
		";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = $row['PARAMETER_VALUE'];
        }

        return $result;
    }
	
    //pilihan status aktif
    public function getHoActiveStatus()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'HO_ACTIVE_STATUS'
                AND DELETE_USER IS NULL
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = $row['PARAMETER_VALUE'];
        }

        return $result;
    }
    
	//pilihan hak akses user
	public function getUsersAccessrights()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'USER_ROLE'
        ";
        $rows = $this->_db->fetchAll($sql);
        $result['-1'] = '';
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }

        return $result;
    }
	
    //pilihan hak akses user
    public function getHoUsersAccessrights()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'HO_USER_ROLE'
        ";
        $rows = $this->_db->fetchAll($sql);
        $result[''] = '';
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }

        return $result;
    }
    
	//pilihan status periode budget
	public function getPeriodBudgetStatus()
    {
        $result = array();

        $sql = "
            SELECT PARAMETER_VALUE_CODE
            FROM T_PARAMETER_VALUE
            WHERE PARAMETER_CODE = 'STATUS_PERIOD_BUDGET'
        ";
        $rows = $this->_db->fetchAll($sql);
        foreach ($rows as $idx => $row) {
            $result["{$row['PARAMETER_VALUE_CODE']}"] = "{$row['PARAMETER_VALUE_CODE']}";
        }

        return $result;
    }
}