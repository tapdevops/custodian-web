<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Generate .sql & sync data dari / ke HO
Function 			:	- genNormaBiaya				: SID 12/08/2014	: generate norma biaya
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	12/08/2014
Update Terakhir		:	12/08/2014
Revisi				:	

=========================================================================================================================
*/
class Application_Model_SyncDownloadData
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db_ho'); //setting database HO
        $this->_global = new Application_Model_Global();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		$this->_userRole = Zend_Registry::get('auth')->getIdentity()->USER_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//generate struktur organisasi
	public function genMasterStrukturOrganisasi($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.BA_CODE, 
				   A.COMPANY_CODE, 
				   A.COMPANY_NAME, 
				   A.ESTATE_NAME, 
				   A.REGION_CODE, 
				   A.REGION_NAME, 
				   A.BA_TYPE, 
				   A.ACTIVE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ORGANIZATION A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ORGANIZATION (
						BA_CODE, COMPANY_CODE, COMPANY_NAME, ESTATE_NAME, REGION_CODE, REGION_NAME, BA_TYPE, ACTIVE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['BA_CODE']."',
					    '".$row['COMPANY_CODE']."',
					    '".$row['COMPANY_NAME']."',
					    '".$row['ESTATE_NAME']."',
					    '".$row['REGION_CODE']."',
					    '".$row['REGION_NAME']."',
					    '".$row['BA_TYPE']."',
					    '".$row['ACTIVE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master aktivitas
	public function genMasterAktivitas($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.ACTIVITY_CODE, 
				   A.DESCRIPTION, 
				   A.ACTIVITY_PARENT_CODE, 
				   A.UOM, 
				   A.FLAG, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ACTIVITY A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ACTIVITY (
						ACTIVITY_CODE, DESCRIPTION, ACTIVITY_PARENT_CODE, UOM, FLAG, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['ACTIVITY_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['ACTIVITY_PARENT_CODE']."',
					    '".$row['UOM']."',
					    '".$row['FLAG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master COA
	public function genMasterCoa($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.COA_CODE, 
				   A.DESCRIPTION, 
				   A.COA_PARENT, 
				   A.FLAG, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_COA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_COA (
						COA_CODE, DESCRIPTION, COA_PARENT, FLAG, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['COA_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['COA_PARENT']."',
					    '".$row['FLAG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Mapping Aktivitas COA
	public function genMasterMappingAktivitasCoa($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.ACTIVITY_GROUP, 
				   A.ACTIVITY_CODE, 
				   A.COST_ELEMENT, 
				   A.COA_CODE, 
				   A.ACTIVITY_CODE_SAP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ACTIVITY_COA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ACTIVITY_COA (
						ACTIVITY_GROUP, ACTIVITY_CODE, COST_ELEMENT, COA_CODE, ACTIVITY_CODE_SAP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['ACTIVITY_GROUP']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['COA_CODE']."',
					    '".$row['ACTIVITY_CODE_SAP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate MASTER MAPPING AKTIVITAS UNTUK PENGGUNAAN RKT
	public function genMasterMappingAktivitasMapping($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_GROUP_TYPE_CODE, 
				   A.ACTIVITY_GROUP_TYPE, 
				   A.UI_RKT_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ACTIVITY_MAPPING A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ACTIVITY_MAPPING (
						ACTIVITY_CODE, ACTIVITY_GROUP_TYPE_CODE, ACTIVITY_GROUP_TYPE, UI_RKT_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_GROUP_TYPE_CODE']."',
					    '".$row['ACTIVITY_GROUP_TYPE']."',
					    '".$row['UI_RKT_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
		
	//generate Master Job Type
	public function genMasterJobType($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.JOB_CODE, 
				   A.GROUP_CHECKROLL_CODE, 
				   A.JOB_TYPE, 
				   A.JOB_DESCRIPTION, 
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_JOB_TYPE A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_JOB_TYPE (
						JOB_CODE, GROUP_CHECKROLL_CODE, JOB_TYPE, JOB_DESCRIPTION,STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['JOB_CODE']."',
					    '".$row['GROUP_CHECKROLL_CODE']."',
					    '".$row['JOB_TYPE']."',
					    '".$row['JOB_DESCRIPTION']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master tunjangan
	public function genMasterTunjangan($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.TUNJANGAN_TYPE, 
				   A.DESCRIPTION, 
				   A.UOM, 
				   A.FLAG_RP_HK, 
				   A.FLAG_EMPLOYEE_STATUS, 
				   A.RUMUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_TUNJANGAN A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_TUNJANGAN (
						TUNJANGAN_TYPE, DESCRIPTION, UOM, FLAG_RP_HK,FLAG_EMPLOYEE_STATUS, RUMUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TUNJANGAN_TYPE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['UOM']."',
					    '".$row['FLAG_RP_HK']."',
					    '".$row['FLAG_EMPLOYEE_STATUS']."',
					    '".$row['RUMUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master RVRA
	public function genMasterRvra($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.SUB_RVRA_CODE, 
				   A.SUB_RVRA_DESCRIPTION, 
				   A.COA_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_RVRA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_RVRA (
						SUB_RVRA_CODE, SUB_RVRA_DESCRIPTION, COA_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['SUB_RVRA_CODE']."',
					    '".$row['SUB_RVRA_DESCRIPTION']."',
					    '".$row['COA_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Mapping Job Type Vra
	public function genMasterMappingJobTypeVra($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.JOB_CODE, 
				   A.RVRA_CODE, 
				   A.VRA_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_MAPPING_JOB_TYPE_VRA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_MAPPING_JOB_TYPE_VRA (
						JOB_CODE, RVRA_CODE, VRA_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['JOB_CODE']."',
					    '".$row['RVRA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Mapping Job Type Wra
	public function genMasterMappingJobTypeWra($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.JOB_CODE, 
				   A.WRA_GROUP_CODE, 
				   A.WRA_FLAG, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_MAPPING_JOB_TYPE_WRA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_MAPPING_JOB_TYPE_WRA (
						JOB_CODE, WRA_GROUP_CODE, WRA_FLAG, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['JOB_CODE']."',
					    '".$row['WRA_GROUP_CODE']."',
					    '".$row['WRA_FLAG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Mapping Group BUM - COA
	public function genMasterMappingGroupBumCoa($params = array())
    {
		$result = true;
		
		//get new data		
		$query = "
			SELECT 
				   A.GROUP_BUM_CODE, 
				   A.DESCRIPTION, 
				   A.COA_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_GROUP_BUM_COA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_GROUP_BUM_COA (
						GROUP_BUM_CODE, DESCRIPTION, COA_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['GROUP_BUM_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['COA_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Mapping Group Relation - COA
	public function genMasterMappingGroupRelation($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.COA_CODE, 
				   A.GROUP_CODE, 
				   A.SUB_GROUP_CODE, 
				   A.DESCRIPTION, 
				   A.SUB_GROUP_DESC, 
				   A.TYPE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_GROUP_RELATION A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_GROUP_RELATION (
						COA_CODE, GROUP_CODE, SUB_GROUP_CODE, DESCRIPTION, SUB_GROUP_DESC, TYPE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['COA_CODE']."',
					    '".$row['GROUP_CODE']."',
					    '".$row['SUB_GROUP_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['SUB_GROUP_DESC']."',
					    '".$row['TYPE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
		
	//generate Master VRA
	public function genMasterVra($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.VRA_CODE, 
				   A.VRA_CAT_CODE, 
				   A.VRA_CAT_DESCRIPTION, 
				   A.VRA_SUB_CAT_CODE, 
				   A.VRA_SUB_CAT_DESCRIPTION, 
				   A.UOM, 
				   A.TYPE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_VRA A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_VRA (
						VRA_CODE, VRA_CAT_CODE, VRA_CAT_DESCRIPTION, VRA_SUB_CAT_CODE, VRA_SUB_CAT_DESCRIPTION, UOM, TYPE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['VRA_CODE']."',
					    '".$row['VRA_CAT_CODE']."',
					    '".$row['VRA_CAT_DESCRIPTION']."',
					    '".$row['VRA_SUB_CAT_CODE']."',
					    '".$row['VRA_SUB_CAT_DESCRIPTION']."',
					    '".$row['UOM']."',
					    '".$row['TYPE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master PERIODE BUDGET 
	public function genMasterPeriodeBudget($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   TO_DATE(A.START_BUDGETING, 'DD-MM-RRRR') AS START_BUDGETING,
				   TO_DATE(A.END_BUDGETING, 'DD-MM-RRRR') AS END_BUDGETING,
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_PERIOD A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_PERIOD (
						PERIOD_BUDGET, START_BUDGETING, END_BUDGETING, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						TO_DATE('".$row['START_BUDGETING']."', 'DD-MM-RRRR'),
						TO_DATE('".$row['END_BUDGETING']."', 'DD-MM-RRRR'),
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate master sebaran produksi
	public function genMasterSebaranProduksi($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_SEBARAN_PRODUKSI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
		
		
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JAN, 
				   A.FEB, 
				   A.MAR, 
				   A.APR, 
				   A.MAY, 
				   A.JUN, 
				   A.JUL, 
				   A.AUG, 
				   A.SEP, 
				   A.OCT, 
				   A.NOV, 
				   A.DEC, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME			
			FROM TM_SEBARAN_PRODUKSI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_SEBARAN_PRODUKSI (
						PERIOD_BUDGET, BA_CODE, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JAN']."',
					    '".$row['FEB']."',
					    '".$row['MAR']."',
					    '".$row['APR']."',
					    '".$row['MAY']."',
					    '".$row['JUN']."',
					    '".$row['JUL']."',
					    '".$row['AUG']."',
					    '".$row['SEP']."',
					    '".$row['OCT']."',
					    '".$row['NOV']."',
					    '".$row['DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate RKT OPEX VRA & NON VRA
	public function genRktOpex($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_OPEX
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_OPEX
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_CODE, 
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.COA_CODE, 
				   A.GROUP_BUM_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTUAL, 
				   A.TAKSASI, 
				   A.ANTISIPASI, 
				   A.PERSENTASE_INFLASI, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.TOTAL_BIAYA, 
				   A.KETERANGAN, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_OPEX A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_OPEX (
						TRX_CODE, PERIOD_BUDGET, REGION_CODE, BA_CODE, COA_CODE, GROUP_BUM_CODE, TIPE_TRANSAKSI, ACTUAL, TAKSASI, ANTISIPASI, PERSENTASE_INFLASI, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, TOTAL_BIAYA, KETERANGAN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['COA_CODE']."',
					    '".$row['GROUP_BUM_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTUAL']."', 
						'".$row['TAKSASI']."', 
						'".$row['ANTISIPASI']."',
						'".$row['PERSENTASE_INFLASI']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['KETERANGAN']."',
						'".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
        return true;
    }
	
	//generate RKT CSR / IR / SHE
	public function genRktRelation($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_RELATION
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_RELATION
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_CODE, 
				   A.BA_CODE, 
				   A.REPORT_TYPE, 
				   A.COA_CODE, 
				   A.GROUP_CODE, 
				   A.SUB_GROUP_CODE, 
				   A.ACTIVITY_DETAIL, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.TOTAL_BIAYA, 
				   A.KETERANGAN, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_RELATION A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_RELATION (
						TRX_CODE, PERIOD_BUDGET, BA_CODE, REPORT_TYPE, COA_CODE, GROUP_CODE, SUB_GROUP_CODE, ACTIVITY_DETAIL, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, TOTAL_BIAYA, KETERANGAN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['REPORT_TYPE']."',
					    '".$row['COA_CODE']."',
					    '".$row['GROUP_CODE']."',
					    '".$row['SUB_GROUP_CODE']."',
					    '".$row['ACTIVITY_DETAIL']."', 
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['KETERANGAN']."',
						'".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
        return true;
    }
	
	//generate norma harga borong
	public function genNormaHargaBorong($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_HARGA_BORONG
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_HARGA_BORONG_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND REGION_CODE = '".$row['REGION_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_HARGA_BORONG
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.SPESIFICATION, 
				   A.PRICE, 
				   A.PRICE_SITE, 
				   A.FLAG_SITE, 
				   A.FLAG_TEMP,				   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_HARGA_BORONG A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BORONG (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, SPESIFICATION, PRICE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, PRICE_SITE, FLAG_SITE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['SPESIFICATION']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['PRICE_SITE']."',
					    '".$row['FLAG_SITE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_HARGA_BORONG_SUM
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND A.REGION_CODE IN (
					SELECT REGION_CODE
					FROM TM_ORGANIZATION
					WHERE UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'
					GROUP BY REGION_CODE
				)";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.PRICE, 
				   A.FLAG_SITE, 
				   A.PRICE_SITE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_HARGA_BORONG_SUM A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BORONG_SUM (
						PERIOD_BUDGET, REGION_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, PRICE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_SITE, PRICE_SITE
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_SITE']."',
					    '".$row['PRICE_SITE']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma alat kerja panen
	public function genNormaAlatKerjaPanen($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_ALAT_KERJA_PANEN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_ALAT_KERJA_PANEN_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_ALAT_KERJA_PANEN
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.MATERIAL_CODE, 
				   A.ROTASI, 
				   A.PRICE, 
				   A.TOTAL,
				   A.TRIGGER_UPDATE, 
				   A.FLAG_TEMP,			   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_ALAT_KERJA_PANEN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_ALAT_KERJA_PANEN (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, ROTASI, PRICE, TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['ROTASI']."',
					    '".$row['PRICE']."',
					    '".$row['TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_ALAT_KERJA_PANEN_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.PRICE_SUM, 
				   A.PRICE_ROTASI_SUM, 
				   A.PRICE_KG, 
				   A.TRIGGER_UPDATE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_ALAT_KERJA_PANEN_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_ALAT_KERJA_PANEN_SUM (
						PERIOD_BUDGET, BA_CODE, PRICE_SUM, PRICE_ROTASI_SUM, PRICE_KG, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['PRICE_SUM']."',
					    '".$row['PRICE_ROTASI_SUM']."',
					    '".$row['PRICE_KG']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma checkroll
	public function genNormaCheckroll($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_CHECKROLL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL_DETAIL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_CHECKROLL_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RPT_DISTRIBUSI_COA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_CHECKROLL
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.GP, 
				   A.MPP_AKTUAL, 
				   A.MPP_PERIOD_BUDGET, 
				   A.MPP_REKRUT, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_CHECKROLL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_CHECKROLL (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, GP, MPP_AKTUAL, MPP_PERIOD_BUDGET, MPP_REKRUT, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['GP']."',
					    '".$row['MPP_AKTUAL']."',
					    '".$row['MPP_PERIOD_BUDGET']."',
					    '".$row['MPP_REKRUT']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_CHECKROLL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.TRX_CR_CODE, 
				   A.GP_INFLASI, 
				   A.MPP_PERIOD_BUDGET, 
				   A.TOTAL_GP_MPP, 
				   A.TOTAL_GAJI_TUNJANGAN, 
				   A.RP_HK_PERBULAN, 
				   A.TOTAL_TUNJANGAN_PK_UMUM, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.DIS_YEAR, 
				   A.TOTAL_TUNJANGAN_WRA, 
				   A.TOTAL_TUNJANGAN_VRA,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, TRX_CR_CODE, GP_INFLASI, MPP_PERIOD_BUDGET, TOTAL_GP_MPP, TOTAL_GAJI_TUNJANGAN, RP_HK_PERBULAN, TOTAL_TUNJANGAN_PK_UMUM, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, DIS_YEAR, TOTAL_TUNJANGAN_WRA, TOTAL_TUNJANGAN_VRA
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['TRX_CR_CODE']."',
					    '".$row['GP_INFLASI']."',
					    '".$row['MPP_PERIOD_BUDGET']."',
					    '".$row['TOTAL_GP_MPP']."',
					    '".$row['TOTAL_GAJI_TUNJANGAN']."',
					    '".$row['RP_HK_PERBULAN']."',
					    '".$row['TOTAL_TUNJANGAN_PK_UMUM']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DIS_YEAR']."',
					    '".$row['TOTAL_TUNJANGAN_WRA']."',
					    '".$row['TOTAL_TUNJANGAN_VRA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }
		
		//get new data - TR_RKT_CHECKROLL_DETAIL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_CR_CODE, 
				   A.TUNJANGAN_TYPE, 
				   A.JUMLAH,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL_DETAIL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL_DETAIL (
						PERIOD_BUDGET, BA_CODE, TRX_CR_CODE, TUNJANGAN_TYPE, JUMLAH, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TRX_CR_CODE']."',
					    '".$row['TUNJANGAN_TYPE']."',
					    '".$row['JUMLAH']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_CHECKROLL_SUM		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.RP_HK,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CHECKROLL_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CHECKROLL_SUM (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, RP_HK, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['RP_HK']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }

		//get new data - TR_RPT_DISTRIBUSI_COA		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.MATURITY_STAGE, 
				   A.TUNJANGAN_TYPE, 
				   A.TOTAL_BIAYA, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.REPORT_TYPE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RPT_DISTRIBUSI_COA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RPT_DISTRIBUSI_COA (
						PERIOD_BUDGET, BA_CODE, MATURITY_STAGE, TUNJANGAN_TYPE, TOTAL_BIAYA, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, REPORT_TYPE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['MATURITY_STAGE']."',
					    '".$row['TUNJANGAN_TYPE']."',
					    '".$row['TOTAL_BIAYA']."',
						'".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['REPORT_TYPE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT CAPEX
	public function genRktCapex($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_CAPEX
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_CAPEX
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_CODE,
				   A.REGION_CODE, 
				   A.COA_CODE, 
				   A.ASSET_CODE, 
				   A.DETAIL_SPESIFICATION, 
				   A.URGENCY_CAPEX, 
				   A.PRICE, 
				   A.QTY_ACTUAL, 
				   A.DIS_TAHUN_BERJALAN, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.DIS_BIAYA_JAN, A.DIS_BIAYA_FEB, A.DIS_BIAYA_MAR, 
				   A.DIS_BIAYA_APR, A.DIS_BIAYA_MAY, A.DIS_BIAYA_JUN, 
				   A.DIS_BIAYA_JUL, A.DIS_BIAYA_AUG, A.DIS_BIAYA_SEP, 
				   A.DIS_BIAYA_OCT, A.DIS_BIAYA_NOV, A.DIS_BIAYA_DEC, 
				   A.DIS_BIAYA_TOTAL,
				   A.COST_SMS1, A.COST_SMS2, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_CAPEX A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_CAPEX (
						TRX_CODE, PERIOD_BUDGET, BA_CODE, REGION_CODE, COA_CODE, ASSET_CODE, DETAIL_SPESIFICATION, URGENCY_CAPEX, PRICE, QTY_ACTUAL, DIS_TAHUN_BERJALAN, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, DIS_BIAYA_JAN, DIS_BIAYA_FEB, DIS_BIAYA_MAR, DIS_BIAYA_APR, DIS_BIAYA_MAY, DIS_BIAYA_JUN, DIS_BIAYA_JUL, DIS_BIAYA_AUG, DIS_BIAYA_SEP, DIS_BIAYA_OCT, DIS_BIAYA_NOV, DIS_BIAYA_DEC, DIS_BIAYA_TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, FLAG_TEMP
					) VALUES (
						'".$row['TRX_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['REGION_CODE']."',
					    '".$row['COA_CODE']."',
					    '".$row['ASSET_CODE']."',
					    '".$row['DETAIL_SPESIFICATION']."',
					    '".$row['URGENCY_CAPEX']."',
					    '".$row['PRICE']."',
					    '".$row['QTY_ACTUAL']."',
					    '".$row['DIS_TAHUN_BERJALAN']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['DIS_BIAYA_JAN']."', '".$row['DIS_BIAYA_FEB']."', '".$row['DIS_BIAYA_MAR']."',
					    '".$row['DIS_BIAYA_APR']."', '".$row['DIS_BIAYA_MAY']."', '".$row['DIS_BIAYA_JUN']."',
					    '".$row['DIS_BIAYA_JUL']."', '".$row['DIS_BIAYA_AUG']."', '".$row['DIS_BIAYA_SEP']."',
					    '".$row['DIS_BIAYA_OCT']."', '".$row['DIS_BIAYA_NOV']."', '".$row['DIS_BIAYA_DEC']."',
					    '".$row['DIS_BIAYA_TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma WRA
	public function genNormaWra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_WRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_WRA_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_WRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.GROUP_WRA_CODE, 
				   A.SUB_WRA_GROUP, 
				   A.QTY_ROTASI, 
				   A.ROTASI_TAHUN, 
				   A.QTY_TAHUN, 
				   A.PRICE, 
				   A.PRICE_QTY_TAHUN, 
				   A.RP_QTY,	
				   A.TRIGGER_UPDATE, 
				   A.FLAG_TEMP,			   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_WRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_WRA (
						PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, PRICE, PRICE_QTY_TAHUN, RP_QTY, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['GROUP_WRA_CODE']."',
					    '".$row['SUB_WRA_GROUP']."',
					    '".$row['QTY_ROTASI']."',
					    '".$row['ROTASI_TAHUN']."',
					    '".$row['QTY_TAHUN']."',
					    '".$row['PRICE']."',
					    '".$row['PRICE_QTY_TAHUN']."',
					    '".$row['RP_QTY']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_WRA_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TOTAL_RP_QTY, 
				   A.TRIGGER_UPDATE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_WRA_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_WRA_SUM (
						PERIOD_BUDGET, BA_CODE, TOTAL_RP_QTY, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT VRA
	public function genRktVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_VRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_VRA_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_VRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.TRX_RKT_VRA_CODE, 
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.DESCRIPTION_VRA, 
				   A.JUMLAH_ALAT, 
				   A.TAHUN_ALAT, 
				   A.QTY_DAY, 
				   A.DAY_YEAR_VRA, 
				   A.QTY_YEAR, 
				   A.TOTAL_QTY_TAHUN, 
				   A.TOTAL_BIAYA, 
				   A.TOTAL_RP_QTY, 
				   A.JUMLAH_OPERATOR, A.GAJI_OPERATOR, 
				   A.JUMLAH_HELPER, A.GAJI_HELPER, 
				   A.TOTAL_GAJI_OPERATOR, A.TUNJANGAN_OPERATOR, A.TOTAL_TUNJANGAN_OPERATOR, A.TOTAL_GAJI_TUNJANGAN_OPERATOR, A.RP_QTY_OPERATOR, 
				   A.TOTAL_GAJI_HELPER, A.TUNJANGAN_HELPER, A.TOTAL_TUNJANGAN_HELPER, A.TOTAL_GAJI_TUNJANGAN_HELPER, A.RP_QTY_HELPER, 
				   A.COST_SETAHUN, 
				   A.RVRA1_CODE, A.RVRA1_VALUE1, A.RVRA1_VALUE2, A.RVRA1_VALUE3, 
				   A.RVRA2_CODE, A.RVRA2_VALUE1, A.RVRA2_VALUE2, A.RVRA2_VALUE3, 
				   A.RVRA3_CODE, A.RVRA3_VALUE1, A.RVRA3_VALUE2, A.RVRA3_VALUE3, 
				   A.RVRA4_CODE, A.RVRA4_VALUE1, A.RVRA4_VALUE2, A.RVRA4_VALUE3, 
				   A.RVRA5_CODE, A.RVRA5_VALUE1, A.RVRA5_VALUE2, A.RVRA5_VALUE3, 
				   A.RVRA6_CODE, A.RVRA6_VALUE1, A.RVRA6_VALUE2, A.RVRA6_VALUE3, 
				   A.RVRA7_CODE, A.RVRA7_VALUE1, A.RVRA7_VALUE2, A.RVRA7_VALUE3, 
				   A.RVRA8_CODE, A.RVRA8_VALUE1, A.RVRA8_VALUE2, A.RVRA8_VALUE3, 
				   A.RVRA9_CODE, A.RVRA9_VALUE1, A.RVRA9_VALUE2, A.RVRA9_VALUE3, 
				   A.RVRA10_CODE, A.RVRA10_VALUE1, A.RVRA10_VALUE2, A.RVRA10_VALUE3, 
				   A.RVRA11_CODE, A.RVRA11_VALUE1, A.RVRA11_VALUE2, A.RVRA11_VALUE3, 
				   A.RVRA12_CODE, A.RVRA12_VALUE1, A.RVRA12_VALUE2, A.RVRA12_VALUE3, 
				   A.RVRA13_CODE, A.RVRA13_VALUE1, A.RVRA13_VALUE2, A.RVRA13_VALUE3, 
				   A.RVRA14_CODE, A.RVRA14_VALUE1, A.RVRA14_VALUE2, A.RVRA14_VALUE3, 
				   A.RVRA15_CODE, A.RVRA15_VALUE1, A.RVRA15_VALUE2, A.RVRA15_VALUE3, 
				   A.RVRA16_CODE, A.RVRA16_VALUE1, A.RVRA16_VALUE2, A.RVRA16_VALUE3, 
				   A.RVRA17_CODE, A.RVRA17_VALUE1, A.RVRA17_VALUE2, A.RVRA17_VALUE3, 
				   A.RVRA18_CODE, A.RVRA18_VALUE1, A.RVRA18_VALUE2, A.RVRA18_VALUE3, 
				   A.RVRA19_CODE, A.RVRA19_VALUE1, A.RVRA19_VALUE2, A.RVRA19_VALUE3, 
				   A.RVRA20_CODE, A.RVRA20_VALUE1, A.RVRA20_VALUE2, A.RVRA20_VALUE3, 
				   A.FLAG_TEMP, A.TRIGGER_UPDATE,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA (
						TRX_RKT_VRA_CODE, PERIOD_BUDGET, BA_CODE, VRA_CODE, DESCRIPTION_VRA, JUMLAH_ALAT, TAHUN_ALAT, QTY_DAY, DAY_YEAR_VRA, QTY_YEAR, TOTAL_QTY_TAHUN, TOTAL_BIAYA, TOTAL_RP_QTY, JUMLAH_OPERATOR, GAJI_OPERATOR, JUMLAH_HELPER, GAJI_HELPER, TOTAL_GAJI_OPERATOR, TUNJANGAN_OPERATOR, TOTAL_TUNJANGAN_OPERATOR, TOTAL_GAJI_TUNJANGAN_OPERATOR, RP_QTY_OPERATOR, TOTAL_GAJI_HELPER, TUNJANGAN_HELPER, TOTAL_TUNJANGAN_HELPER, TOTAL_GAJI_TUNJANGAN_HELPER, RP_QTY_HELPER, COST_SETAHUN, RVRA1_CODE, RVRA1_VALUE1, RVRA1_VALUE2, RVRA1_VALUE3, RVRA2_CODE, RVRA2_VALUE1, RVRA2_VALUE2, RVRA2_VALUE3, RVRA3_CODE, RVRA3_VALUE1, RVRA3_VALUE2, RVRA3_VALUE3, RVRA4_CODE, RVRA4_VALUE1, RVRA4_VALUE2, RVRA4_VALUE3, RVRA5_CODE, RVRA5_VALUE1, RVRA5_VALUE2, RVRA5_VALUE3, RVRA6_CODE, RVRA6_VALUE1, RVRA6_VALUE2, RVRA6_VALUE3, RVRA7_CODE, RVRA7_VALUE1, RVRA7_VALUE2, RVRA7_VALUE3, RVRA8_CODE, RVRA8_VALUE1, RVRA8_VALUE2, RVRA8_VALUE3, RVRA9_CODE, RVRA9_VALUE1, RVRA9_VALUE2, RVRA9_VALUE3, RVRA10_CODE, RVRA10_VALUE1, RVRA10_VALUE2, RVRA10_VALUE3, RVRA11_CODE, RVRA11_VALUE1, RVRA11_VALUE2, RVRA11_VALUE3, RVRA12_CODE, RVRA12_VALUE1, RVRA12_VALUE2, RVRA12_VALUE3, RVRA13_CODE, RVRA13_VALUE1, RVRA13_VALUE2, RVRA13_VALUE3, RVRA14_CODE, RVRA14_VALUE1, RVRA14_VALUE2, RVRA14_VALUE3, RVRA15_CODE, RVRA15_VALUE1, RVRA15_VALUE2, RVRA15_VALUE3, RVRA16_CODE, RVRA16_VALUE1, RVRA16_VALUE2, RVRA16_VALUE3, RVRA17_CODE, RVRA17_VALUE1, RVRA17_VALUE2, RVRA17_VALUE3, RVRA18_CODE, RVRA18_VALUE1, RVRA18_VALUE2, RVRA18_VALUE3, RVRA19_CODE, RVRA19_VALUE1, RVRA19_VALUE2, RVRA19_VALUE3, RVRA20_CODE, RVRA20_VALUE1, RVRA20_VALUE2, RVRA20_VALUE3, FLAG_TEMP, TRIGGER_UPDATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_VRA_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['DESCRIPTION_VRA']."',
					    '".$row['JUMLAH_ALAT']."',
					    '".$row['TAHUN_ALAT']."',
					    '".$row['QTY_DAY']."',
					    '".$row['DAY_YEAR_VRA']."',
					    '".$row['QTY_YEAR']."',
					    '".$row['TOTAL_QTY_TAHUN']."',
					    '".$row['TOTAL_BIAYA']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['JUMLAH_OPERATOR']."', '".$row['GAJI_OPERATOR']."', '".$row['JUMLAH_HELPER']."', 
						'".$row['GAJI_HELPER']."', '".$row['TOTAL_GAJI_OPERATOR']."',
						'".$row['TUNJANGAN_OPERATOR']."', '".$row['TOTAL_TUNJANGAN_OPERATOR']."',
						'".$row['TOTAL_GAJI_TUNJANGAN_OPERATOR']."', '".$row['RP_QTY_OPERATOR']."', 
						'".$row['TOTAL_GAJI_HELPER']."', '".$row['TUNJANGAN_HELPER']."', 
						'".$row['TOTAL_TUNJANGAN_HELPER']."', '".$row['TOTAL_GAJI_TUNJANGAN_HELPER']."', 
						'".$row['RP_QTY_HELPER']."', 
						'".$row['COST_SETAHUN']."', 
						'".$row['RVRA1_CODE']."', '".$row['RVRA1_VALUE1']."', '".$row['RVRA1_VALUE2']."', '".$row['RVRA1_VALUE3']."', 
						'".$row['RVRA2_CODE']."', '".$row['RVRA2_VALUE1']."', '".$row['RVRA2_VALUE2']."', '".$row['RVRA2_VALUE3']."', 
						'".$row['RVRA3_CODE']."', '".$row['RVRA3_VALUE1']."', '".$row['RVRA3_VALUE2']."', '".$row['RVRA3_VALUE3']."', 
						'".$row['RVRA4_CODE']."', '".$row['RVRA4_VALUE1']."', '".$row['RVRA4_VALUE2']."', '".$row['RVRA4_VALUE3']."', 
						'".$row['RVRA5_CODE']."', '".$row['RVRA5_VALUE1']."', '".$row['RVRA5_VALUE2']."', '".$row['RVRA5_VALUE3']."', 
						'".$row['RVRA6_CODE']."', '".$row['RVRA6_VALUE1']."', '".$row['RVRA6_VALUE2']."', '".$row['RVRA6_VALUE3']."', 
						'".$row['RVRA7_CODE']."', '".$row['RVRA7_VALUE1']."', '".$row['RVRA7_VALUE2']."', '".$row['RVRA7_VALUE3']."', 
						'".$row['RVRA8_CODE']."', '".$row['RVRA8_VALUE1']."', '".$row['RVRA8_VALUE2']."', '".$row['RVRA8_VALUE3']."', 
						'".$row['RVRA9_CODE']."', '".$row['RVRA9_VALUE1']."', '".$row['RVRA9_VALUE2']."', '".$row['RVRA9_VALUE3']."', 
						'".$row['RVRA10_CODE']."', '".$row['RVRA10_VALUE1']."', '".$row['RVRA10_VALUE2']."', '".$row['RVRA10_VALUE3']."', 
						'".$row['RVRA11_CODE']."', '".$row['RVRA11_VALUE1']."', '".$row['RVRA11_VALUE2']."', '".$row['RVRA11_VALUE3']."', 
						'".$row['RVRA12_CODE']."', '".$row['RVRA12_VALUE1']."', '".$row['RVRA12_VALUE2']."', '".$row['RVRA12_VALUE3']."', 
						'".$row['RVRA13_CODE']."', '".$row['RVRA13_VALUE1']."', '".$row['RVRA13_VALUE2']."', '".$row['RVRA13_VALUE3']."', 
						'".$row['RVRA14_CODE']."', '".$row['RVRA14_VALUE1']."', '".$row['RVRA14_VALUE2']."', '".$row['RVRA14_VALUE3']."', 
						'".$row['RVRA15_CODE']."', '".$row['RVRA15_VALUE1']."', '".$row['RVRA15_VALUE2']."', '".$row['RVRA15_VALUE3']."', 
						'".$row['RVRA16_CODE']."', '".$row['RVRA16_VALUE1']."', '".$row['RVRA16_VALUE2']."', '".$row['RVRA16_VALUE3']."', 
						'".$row['RVRA17_CODE']."', '".$row['RVRA17_VALUE1']."', '".$row['RVRA17_VALUE2']."', '".$row['RVRA17_VALUE3']."', 
						'".$row['RVRA18_CODE']."', '".$row['RVRA18_VALUE1']."', '".$row['RVRA18_VALUE2']."', '".$row['RVRA18_VALUE3']."', 
						'".$row['RVRA19_CODE']."', '".$row['RVRA19_VALUE1']."', '".$row['RVRA19_VALUE2']."', '".$row['RVRA19_VALUE3']."', 
						'".$row['RVRA20_CODE']."', '".$row['RVRA20_VALUE1']."', '".$row['RVRA20_VALUE2']."', '".$row['RVRA20_VALUE3']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_VRA_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.VALUE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_SUM (
						PERIOD_BUDGET, BA_CODE, VRA_CODE, VALUE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT Distribusi VRA
	public function genRktDistribusiVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_VRA_DISTRIBUSI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_VRA_DISTRIBUSI_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_VRA_DISTRIBUSI
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.VRA_CODE, 
				   A.LOCATION_CODE, 
				   A.HM_KM, 
				   A.PRICE_QTY_VRA, 
				   A.PRICE_HM_KM, 
				   A.TRX_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_DISTRIBUSI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, LOCATION_CODE, HM_KM, PRICE_QTY_VRA, PRICE_HM_KM, TRX_CODE, TIPE_TRANSAKSI, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['LOCATION_CODE']."',
					    '".$row['HM_KM']."',
					    '".$row['PRICE_QTY_VRA']."',
					    '".$row['PRICE_HM_KM']."',
					    '".$row['TRX_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_RKT_VRA_DISTRIBUSI_SUM
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.VRA_CODE, 
				   A.TOTAL_HM_KM, 
				   A.TOTAL_PRICE_HM_KM, 
				   A.TIPE_TRANSAKSI,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_VRA_DISTRIBUSI_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_VRA_DISTRIBUSI_SUM (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, VRA_CODE, TOTAL_HM_KM, TOTAL_PRICE_HM_KM, TIPE_TRANSAKSI, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['TOTAL_HM_KM']."',
					    '".$row['TOTAL_PRICE_HM_KM']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma biaya
	public function genNormaBiaya($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_BIAYA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
		
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_GROUP, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.COST_ELEMENT, 
				   A.SUB_COST_ELEMENT, 
				   A.QTY, 
				   A.ROTASI, 
				   A.VOLUME, 
				   A.QTY_HA, 
				   A.PRICE, 
				   A.PRICE_HA, 
				   A.PRICE_ROTASI, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   A.QTY_SITE, 
				   A.ROTASI_SITE, 
				   A.VOLUME_SITE, 
				   A.QTY_HA_SITE, 
				   A.PRICE_SITE, 
				   A.PRICE_HA_SITE, 
				   A.PRICE_ROTASI_SITE, 
				   A.FLAG_SITE, 
				   A.FLAG_TEMP
			FROM TN_BIAYA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_BIAYA (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_GROUP, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, COST_ELEMENT, SUB_COST_ELEMENT, QTY, ROTASI, VOLUME, QTY_HA, PRICE, PRICE_HA, PRICE_ROTASI, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, QTY_SITE, ROTASI_SITE, VOLUME_SITE, QTY_HA_SITE, PRICE_SITE, PRICE_HA_SITE, PRICE_ROTASI_SITE, FLAG_SITE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_GROUP']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['SUB_COST_ELEMENT']."',
					    '".$row['QTY']."',
					    '".$row['ROTASI']."',
					    '".$row['VOLUME']."',
					    '".$row['QTY_HA']."',
					    '".$row['PRICE']."',
					    '".$row['PRICE_HA']."',
					    '".$row['PRICE_ROTASI']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['QTY_SITE']."',
					    '".$row['ROTASI_SITE']."',
					    '".$row['VOLUME_SITE']."',
					    '".$row['QTY_HA_SITE']."',
					    '".$row['PRICE_SITE']."',
					    '".$row['PRICE_HA_SITE']."',
					    '".$row['PRICE_ROTASI_SITE']."',
					    '".$row['FLAG_SITE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate norma panen OER BJR
	public function genNormaPanenOer($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_OER_BJR
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_PANEN_OER_BJR
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.BJR_MIN, 
				   A.BJR_MAX, 
				   A.OER_MIN, 
				   A.OER_MAX, 
				   A.PREMI_PANEN, 
				   A.BJR_BUDGET, 
				   A.JANJANG_BASIS_MANDOR, 
				   A.JANJANG_OPERATION, 
				   A.OVER_BASIS_JANJANG, 
				   A.OER_BA, 
				   A.NILAI, 
				   A.VAR1, 
				   A.VAR2,
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PANEN_OER_BJR A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_OER_BJR (
						PERIOD_BUDGET, BA_CODE, BJR_MIN, BJR_MAX, OER_MIN, OER_MAX, PREMI_PANEN, BJR_BUDGET, JANJANG_BASIS_MANDOR, JANJANG_OPERATION, OVER_BASIS_JANJANG, OER_BA, NILAI, VAR1, VAR2, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['BJR_MIN']."',
					    '".$row['BJR_MAX']."',
					    '".$row['OER_MIN']."',
					    '".$row['OER_MAX']."',
					    '".$row['PREMI_PANEN']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['JANJANG_BASIS_MANDOR']."',
					    '".$row['JANJANG_OPERATION']."',
					    '".$row['OVER_BASIS_JANJANG']."',
					    '".$row['OER_BA']."',
					    '".$row['NILAI']."',
					    '".$row['VAR1']."',
					    '".$row['VAR2']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen supervisi
	public function genNormaPanenSupervisi($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_SUPERVISI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_PANEN_SUPERVISI
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.MIN_BJR, 
				   A.MAX_BJR, 
				   A.JANJANG_BASIS, 
				   A.BJR_BUDGET, 
				   A.OVER_BASIS_JANJANG, 
				   A.JANJANG_OPERATION, 
				   A.RP_KG, 
				   A.FLAG_TEMP,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PANEN_SUPERVISI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_SUPERVISI (
						PERIOD_BUDGET, BA_CODE, MIN_BJR, MAX_BJR, JANJANG_BASIS, BJR_BUDGET, OVER_BASIS_JANJANG, JANJANG_OPERATION, RP_KG, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['MIN_BJR']."',
					    '".$row['MAX_BJR']."',
					    '".$row['JANJANG_BASIS']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['OVER_BASIS_JANJANG']."',
					    '".$row['JANJANG_OPERATION']."',
					    '".$row['RP_KG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate hectare statement
	public function genHectareStatement($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "						
					DELETE FROM TM_HECTARE_STATEMENT_DETAIL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TM_HECTARE_STATEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TM_HECTARE_STATEMENT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_PLANTED, 
				   A.TOPOGRAPHY, 
				   A.LAND_TYPE, 
				   A.PROGENY, 
				   A.LAND_SUITABILITY, 
				   A.TAHUN_TANAM, 
				   A.POKOK_TANAM, 
				   A.SPH, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.STATUS,	
				   A.KONVERSI_TBM, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_HECTARE_STATEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_HECTARE_STATEMENT (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PLANTED, TOPOGRAPHY, LAND_TYPE, PROGENY, LAND_SUITABILITY, TAHUN_TANAM, POKOK_TANAM, SPH, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, KONVERSI_TBM, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_PLANTED']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['PROGENY']."',
					    '".$row['LAND_SUITABILITY']."',
					    '".$row['TAHUN_TANAM']."',
					    '".$row['POKOK_TANAM']."',
					    '".$row['SPH']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['KONVERSI_TBM']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TM_HECTARE_STATEMENT_DETAIL
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.LAND_CATEGORY, 
				   A.HA, 
				   A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_HECTARE_STATEMENT_DETAIL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_HECTARE_STATEMENT_DETAIL (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, LAND_CATEGORY, HA, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['LAND_CATEGORY']."',
					    '".$row['HA']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate master OER BA
	public function genMasterOerBa($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_OER_BA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TM_OER_BA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.OER,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_OER_BA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_OER_BA (
						PERIOD_BUDGET, BA_CODE, OER, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['OER']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate perencanaan produksi
	public function genPerencanaanProduksi($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_PRODUKSI_PERIODE_BUDGET
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_PRODUKSI_TAHUN_BERJALAN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_PRODUKSI_PERIODE_BUDGET
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_SMS1, 
				   A.POKOK_SMS1, 
				   A.SPH_SMS1, 
				   A.HA_SMS2, 
				   A.POKOK_SMS2, 
				   A.SPH_SMS2, 
				   A.YPH_PROFILE, 
				   A.TON_PROFILE, 
				   A.YPH_PROPORTION, 
				   A.TON_PROPORTION, 
				   A.JANJANG_BUDGET, 
				   A.BJR_BUDGET, 
				   A.TON_BUDGET, 
				   A.YPH_BUDGET, 
				   A.JAN, A.FEB, A.MAR, 
				   A.APR, A.MAY, A.JUN, 
				   A.JUL, A.AUG, A.SEP, 
				   A.OCT, A.NOV, A.DEC, 
				   A.SMS1, A.SMS2, 
				   A.TRX_CODE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_PRODUKSI_PERIODE_BUDGET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_PRODUKSI_PERIODE_BUDGET (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_SMS1, POKOK_SMS1, SPH_SMS1, HA_SMS2, POKOK_SMS2, SPH_SMS2, YPH_PROFILE, TON_PROFILE, YPH_PROPORTION, TON_PROPORTION, JANJANG_BUDGET, BJR_BUDGET, TON_BUDGET, YPH_BUDGET, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SMS1, SMS2, TRX_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_SMS1']."',
					    '".$row['POKOK_SMS1']."',
					    '".$row['SPH_SMS1']."',
					    '".$row['HA_SMS2']."',
					    '".$row['POKOK_SMS2']."',
					    '".$row['SPH_SMS2']."',
					    '".$row['YPH_PROFILE']."',
					    '".$row['TON_PROFILE']."',
					    '".$row['YPH_PROPORTION']."',
					    '".$row['TON_PROPORTION']."',
					    '".$row['JANJANG_BUDGET']."',
					    '".$row['BJR_BUDGET']."',
					    '".$row['TON_BUDGET']."',
					    '".$row['YPH_BUDGET']."',
					    '".$row['JAN']."', '".$row['FEB']."', '".$row['MAR']."',
					    '".$row['APR']."', '".$row['MAY']."', '".$row['JUN']."',
					    '".$row['JUL']."', '".$row['AUG']."', '".$row['SEP']."',
					    '".$row['OCT']."', '".$row['NOV']."', '".$row['DEC']."',
						'".$row['SMS1']."', '".$row['SMS2']."',
						'".$row['TRX_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TR_PRODUKSI_TAHUN_BERJALAN
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.HA_PANEN, 
				   A.POKOK_PRODUKTIF, 
				   A.SPH_PRODUKTIF, 
				   A.TON_AKTUAL, 
				   A.JANJANG_AKTUAL, 
				   A.BJR_AKTUAL, 
				   A.YPH_AKTUAL, 
				   A.TON_TAKSASI, 
				   A.JANJANG_TAKSASI, 
				   A.BJR_TAKSASI, 
				   A.YPH_TAKSASI, 
				   A.TON_BUDGET, 
				   A.YPH_BUDGET, 
				   A.VAR_YPH, 
				   A.TRX_CODE, 
				   A.TON_ANTISIPASI, 
				   A.JANJANG_ANTISIPASI, 
				   A.BJR_ANTISIPASI, 
				   A.YPH_ANTISIPASI,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_PRODUKSI_TAHUN_BERJALAN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_PRODUKSI_TAHUN_BERJALAN (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PANEN, POKOK_PRODUKTIF, SPH_PRODUKTIF, TON_AKTUAL, JANJANG_AKTUAL, BJR_AKTUAL, YPH_AKTUAL, TON_TAKSASI, JANJANG_TAKSASI, BJR_TAKSASI, YPH_TAKSASI, TON_BUDGET, YPH_BUDGET, VAR_YPH, TRX_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TON_ANTISIPASI, JANJANG_ANTISIPASI, BJR_ANTISIPASI, YPH_ANTISIPASI
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['HA_PANEN']."',
					    '".$row['POKOK_PRODUKTIF']."',
					    '".$row['SPH_PRODUKTIF']."',
					    '".$row['TON_AKTUAL']."',
					    '".$row['JANJANG_AKTUAL']."',
					    '".$row['BJR_AKTUAL']."',
					    '".$row['YPH_AKTUAL']."',
					    '".$row['TON_TAKSASI']."',
					    '".$row['JANJANG_TAKSASI']."',
					    '".$row['BJR_TAKSASI']."',
					    '".$row['YPH_TAKSASI']."',
					    '".$row['TON_BUDGET']."',
					    '".$row['YPH_BUDGET']."',
					    '".$row['VAR_YPH']."',
					    '".$row['TRX_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TON_ANTISIPASI']."',
					    '".$row['JANJANG_ANTISIPASI']."',
					    '".$row['BJR_ANTISIPASI']."',
					    '".$row['YPH_ANTISIPASI']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT LC
	public function genRktLc($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_LC
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_LC_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_LC
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.SUMBER_BIAYA, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, 
				   A.TOTAL_RP_QTY, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.COST_SETAHUN, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_LC A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_LC (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, SUMBER_BIAYA, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, TOTAL_RP_QTY, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SMS1, COST_SMS2, COST_SETAHUN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_LC_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.SUMBER_BIAYA, 
				   A.COST_ELEMENT, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, 
				   A.TOTAL_RP_QTY, 
				   A.DIS_COST_JAN, A.DIS_COST_FEB, A.DIS_COST_MAR, 
				   A.DIS_COST_APR, A.DIS_COST_MAY, A.DIS_COST_JUN, 
				   A.DIS_COST_JUL, A.DIS_COST_AUG, A.DIS_COST_SEP, 
				   A.DIS_COST_OCT, A.DIS_COST_NOV, A.DIS_COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.DIS_COST_SETAHUN,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_LC_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_LC_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, SUMBER_BIAYA, COST_ELEMENT, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, TOTAL_RP_QTY, DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC, COST_SMS1, COST_SMS2, DIS_COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['DIS_COST_JAN']."', '".$row['DIS_COST_FEB']."', '".$row['DIS_COST_MAR']."',
					    '".$row['DIS_COST_APR']."', '".$row['DIS_COST_MAY']."', '".$row['DIS_COST_JUN']."',
					    '".$row['DIS_COST_JUL']."', '".$row['DIS_COST_AUG']."', '".$row['DIS_COST_SEP']."',
					    '".$row['DIS_COST_OCT']."', '".$row['DIS_COST_NOV']."', '".$row['DIS_COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['DIS_COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT Rawat, Rawat Opsi, Rawat Infra, Tanam Manual, Tanam Otomatis
	public function genRkt($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTIVITY_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.BULAN_PENGERJAAN, 
				   A.ACTIVITY_CLASS, 
				   A.ATRIBUT, 
				   A.SUMBER_BIAYA, 
				   A.ROTASI_SMS1, 
				   A.ROTASI_SMS2, 
				   A.TOTAL_RP_SMS1, 
				   A.TOTAL_RP_SMS2, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SMS1, A.PLAN_SMS2, A.PLAN_SETAHUN, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.TOTAL_RP_SETAHUN, 
				   A.FLAG_TEMP, 
				   A.AWAL_ROTASI, 
				   A.TIPE_NORMA, 
				   A.FLAG_SITE,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, BULAN_PENGERJAAN, ACTIVITY_CLASS, ATRIBUT, SUMBER_BIAYA, ROTASI_SMS1, ROTASI_SMS2, TOTAL_RP_SMS1, TOTAL_RP_SMS2, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SMS1, PLAN_SMS2, PLAN_SETAHUN, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SMS1, COST_SMS2, TOTAL_RP_SETAHUN, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, AWAL_ROTASI, TIPE_NORMA, FLAG_SITE
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['BULAN_PENGERJAAN']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['ATRIBUT']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['ROTASI_SMS1']."',
					    '".$row['ROTASI_SMS2']."',
					    '".$row['TOTAL_RP_SMS1']."',
					    '".$row['TOTAL_RP_SMS2']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SMS1']."', '".$row['PLAN_SMS2']."', '".$row['PLAN_SETAHUN']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['TOTAL_RP_SETAHUN']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['AWAL_ROTASI']."',
					    '".$row['TIPE_NORMA']."',
					    '".$row['FLAG_SITE']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TRX_RKT_CODE,
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.ACTIVITY_CODE, 
				   A.COST_ELEMENT, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.BULAN_PENGERJAAN, 
				   A.ACTIVITY_CLASS, 
				   A.ATRIBUT, 
				   A.SUMBER_BIAYA, 
				   A.ROTASI_SMS1, 
				   A.ROTASI_SMS2, 
				   A.RP_ROTASI_SMS1, 
				   A.RP_ROTASI_SMS2, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SMS1, A.PLAN_SMS2, A.PLAN_SETAHUN, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.DIS_SETAHUN, 
				   A.AWAL_ROTASI, 
				   A.TIPE_NORMA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, ACTIVITY_CODE, COST_ELEMENT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, BULAN_PENGERJAAN, ACTIVITY_CLASS, ATRIBUT, SUMBER_BIAYA, ROTASI_SMS1, ROTASI_SMS2, RP_ROTASI_SMS1, RP_ROTASI_SMS2, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SMS1, PLAN_SMS2, PLAN_SETAHUN, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, COST_SMS1, COST_SMS2, DIS_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, AWAL_ROTASI, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['TIPE_TRANSAKSI']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['BULAN_PENGERJAAN']."',
					    '".$row['ACTIVITY_CLASS']."',
					    '".$row['ATRIBUT']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['ROTASI_SMS1']."',
					    '".$row['ROTASI_SMS2']."',
					    '".$row['RP_ROTASI_SMS1']."',
					    '".$row['RP_ROTASI_SMS2']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SMS1']."', '".$row['PLAN_SMS2']."', '".$row['PLAN_SETAHUN']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['DIS_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['AWAL_ROTASI']."',
					    '".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT Perkerasan Jalan
	public function genRktPerkerasanJalan($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PK_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.SUMBER_BIAYA, 
				   A.JENIS_PEKERJAAN, 
				   A.JARAK, 
				   A.AKTUAL_JALAN, 
				   A.AKTUAL_PERKERASAN_JALAN, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.PLAN_SETAHUN, A.PRICE_QTY, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, 
				   A.COST_SMS1, A.COST_SMS2, 
				   A.FLAG_TEMP, A.TIPE_NORMA,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PK (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, SUMBER_BIAYA, JENIS_PEKERJAAN, JARAK, AKTUAL_JALAN, AKTUAL_PERKERASAN_JALAN, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, PLAN_SETAHUN, PRICE_QTY, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, FLAG_TEMP, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['JENIS_PEKERJAAN']."',
					    '".$row['JARAK']."',
					    '".$row['AKTUAL_JALAN']."',
					    '".$row['AKTUAL_PERKERASAN_JALAN']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['PLAN_SETAHUN']."', '".$row['PRICE_QTY']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['FLAG_TEMP']."',
						'".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_PK_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.COST_ELEMENT, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.SUMBER_BIAYA, 
				   A.TOTAL_RP_QTY, 
				   A.PLAN_JAN, A.PLAN_FEB, A.PLAN_MAR, 
				   A.PLAN_APR, A.PLAN_MAY, A.PLAN_JUN, 
				   A.PLAN_JUL, A.PLAN_AUG, A.PLAN_SEP, 
				   A.PLAN_OCT, A.PLAN_NOV, A.PLAN_DEC, 
				   A.DIS_JAN, A.DIS_FEB, A.DIS_MAR, 
				   A.DIS_APR, A.DIS_MAY, A.DIS_JUN, 
				   A.DIS_JUL, A.DIS_AUG, A.DIS_SEP, 
				   A.DIS_OCT, A.DIS_NOV, A.DIS_DEC, 
				   A.COST_SMS1, A.COST_SMS2, A.COST_SETAHUN, A.TIPE_NORMA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PK_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PK_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, COST_ELEMENT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, SUMBER_BIAYA, TOTAL_RP_QTY, PLAN_JAN, PLAN_FEB, PLAN_MAR, PLAN_APR, PLAN_MAY, PLAN_JUN, PLAN_JUL, PLAN_AUG, PLAN_SEP, PLAN_OCT, PLAN_NOV, PLAN_DEC, DIS_JAN, DIS_FEB, DIS_MAR, DIS_APR, DIS_MAY, DIS_JUN, DIS_JUL, DIS_AUG, DIS_SEP, DIS_OCT, DIS_NOV, DIS_DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, COST_SETAHUN, TIPE_NORMA
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['SUMBER_BIAYA']."',
					    '".$row['TOTAL_RP_QTY']."',
					    '".$row['PLAN_JAN']."', '".$row['PLAN_FEB']."', '".$row['PLAN_MAR']."',
					    '".$row['PLAN_APR']."', '".$row['PLAN_MAY']."', '".$row['PLAN_JUN']."',
					    '".$row['PLAN_JUL']."', '".$row['PLAN_AUG']."', '".$row['PLAN_SEP']."',
					    '".$row['PLAN_OCT']."', '".$row['PLAN_NOV']."', '".$row['PLAN_DEC']."',
					    '".$row['DIS_JAN']."', '".$row['DIS_FEB']."', '".$row['DIS_MAR']."',
					    '".$row['DIS_APR']."', '".$row['DIS_MAY']."', '".$row['DIS_JUN']."',
					    '".$row['DIS_JUL']."', '".$row['DIS_AUG']."', '".$row['DIS_SEP']."',
					    '".$row['DIS_OCT']."', '".$row['DIS_NOV']."', '".$row['DIS_DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['TIPE_NORMA']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT Panen
	public function genRktPanen($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PANEN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PANEN_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.TRX_RKT_CODE, 
				   A.TON, 
				   A.JANJANG, 
				   A.BJR_AFD, 
				   A.JARAK_PKS, 
				   A.SUMBER_BIAYA_UNIT, 
				   A.PERSEN_LANGSIR, 
				   A.BIAYA_PEMANEN_HK, 
				   A.BIAYA_PEMANEN_RP_BASIS, 
				   A.BIAYA_PEMANEN_RP_PREMI, 
				   A.BIAYA_PEMANEN_RP_TOTAL, 
				   A.BIAYA_PEMANEN_RP_KG, 
				   A.BIAYA_SPV_RP_BASIS, 
				   A.BIAYA_SPV_RP_PREMI, 
				   A.BIAYA_SPV_RP_TOTAL, 
				   A.BIAYA_SPV_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_TOTAL, 
				   A.TUKANG_MUAT_BASIS, 
				   A.TUKANG_MUAT_PREMI, 
				   A.TUKANG_MUAT_TOTAL, 
				   A.TUKANG_MUAT_RP_KG, 
				   A.SUPIR_PREMI, 
				   A.SUPIR_RP_KG, 
				   A.ANGKUT_TBS_RP_KG_KM, 
				   A.ANGKUT_TBS_RP_ANGKUT, 
				   A.ANGKUT_TBS_RP_KG, 
				   A.KRANI_BUAH_BASIS, 
				   A.KRANI_BUAH_PREMI, 
				   A.KRANI_BUAH_TOTAL, 
				   A.KRANI_BUAH_RP_KG, 
				   A.LANGSIR_TON, 
				   A.LANGSIR_RP, 
				   A.LANGSIR_RP_KG, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, A.COST_SMS1, A.COST_SMS2, 
				   A.MATURITY_STAGE_SMS1, A.MATURITY_STAGE_SMS2, A.FLAG_TEMP,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PANEN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PANEN (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, TON, JANJANG, BJR_AFD, JARAK_PKS, SUMBER_BIAYA_UNIT, PERSEN_LANGSIR, BIAYA_PEMANEN_HK, BIAYA_PEMANEN_RP_BASIS, BIAYA_PEMANEN_RP_PREMI, BIAYA_PEMANEN_RP_TOTAL, BIAYA_PEMANEN_RP_KG, BIAYA_SPV_RP_BASIS, BIAYA_SPV_RP_PREMI, BIAYA_SPV_RP_TOTAL, BIAYA_SPV_RP_KG, BIAYA_ALAT_PANEN_RP_KG, BIAYA_ALAT_PANEN_RP_TOTAL, TUKANG_MUAT_BASIS, TUKANG_MUAT_PREMI, TUKANG_MUAT_TOTAL, TUKANG_MUAT_RP_KG, SUPIR_PREMI, SUPIR_RP_KG, ANGKUT_TBS_RP_KG_KM, ANGKUT_TBS_RP_ANGKUT, ANGKUT_TBS_RP_KG, KRANI_BUAH_BASIS, KRANI_BUAH_PREMI, KRANI_BUAH_TOTAL, KRANI_BUAH_RP_KG, LANGSIR_TON, LANGSIR_RP, LANGSIR_RP_KG, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, FLAG_TEMP
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['TON']."', 
						'".$row['JANJANG']."', 
						'".$row['BJR_AFD']."',
						'".$row['JARAK_PKS']."',
					    '".$row['SUMBER_BIAYA_UNIT']."',
					    '".$row['PERSEN_LANGSIR']."',
					    '".$row['BIAYA_PEMANEN_HK']."',
					    '".$row['BIAYA_PEMANEN_RP_BASIS']."',
					    '".$row['BIAYA_PEMANEN_RP_PREMI']."',
					    '".$row['BIAYA_PEMANEN_RP_TOTAL']."',
					    '".$row['BIAYA_PEMANEN_RP_KG']."',
					    '".$row['BIAYA_SPV_RP_BASIS']."',
					    '".$row['BIAYA_SPV_RP_PREMI']."',
					    '".$row['BIAYA_SPV_RP_TOTAL']."',
					    '".$row['BIAYA_SPV_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_TOTAL']."',
					    '".$row['TUKANG_MUAT_BASIS']."',
					    '".$row['TUKANG_MUAT_PREMI']."',
					    '".$row['TUKANG_MUAT_TOTAL']."',
					    '".$row['TUKANG_MUAT_RP_KG']."',
					    '".$row['SUPIR_PREMI']."',
					    '".$row['SUPIR_RP_KG']."',
					    '".$row['ANGKUT_TBS_RP_KG_KM']."',
					    '".$row['ANGKUT_TBS_RP_ANGKUT']."',
					    '".$row['ANGKUT_TBS_RP_KG']."',
					    '".$row['KRANI_BUAH_BASIS']."',
					    '".$row['KRANI_BUAH_PREMI']."',
					    '".$row['KRANI_BUAH_TOTAL']."',
					    '".$row['KRANI_BUAH_RP_KG']."',
					    '".$row['LANGSIR_TON']."',
					    '".$row['LANGSIR_RP']."',
					    '".$row['LANGSIR_RP_KG']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
						'".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
				
		//get new data - TR_RKT_PANEN_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.ACTIVITY_CODE, 
				   A.COST_ELEMENT, 
				   A.TRX_RKT_CODE, 
				   A.SUMBER_BIAYA_UNIT, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.TON, 
				   A.JANJANG, 
				   A.BJR_AFD, 
				   A.JARAK_PKS, 
				   A.PERSEN_LANGSIR, 
				   A.BIAYA_PEMANEN_HK, 
				   A.BIAYA_PEMANEN_RP_BASIS, 
				   A.BIAYA_PEMANEN_RP_PREMI, 
				   A.BIAYA_PEMANEN_RP_TOTAL, 
				   A.BIAYA_PEMANEN_RP_KG, 
				   A.BIAYA_SPV_RP_BASIS, 
				   A.BIAYA_SPV_RP_PREMI, 
				   A.BIAYA_SPV_RP_TOTAL, 
				   A.BIAYA_SPV_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_KG, 
				   A.BIAYA_ALAT_PANEN_RP_TOTAL, 
				   A.TUKANG_MUAT_BASIS, 
				   A.TUKANG_MUAT_PREMI, 
				   A.TUKANG_MUAT_TOTAL, 
				   A.TUKANG_MUAT_RP_KG, 
				   A.SUPIR_PREMI, 
				   A.SUPIR_RP_KG, 
				   A.ANGKUT_TBS_RP_KG_KM, 
				   A.ANGKUT_TBS_RP_ANGKUT, 
				   A.ANGKUT_TBS_RP_KG, 
				   A.KRANI_BUAH_BASIS, 
				   A.KRANI_BUAH_PREMI, 
				   A.KRANI_BUAH_TOTAL, 
				   A.KRANI_BUAH_RP_KG, 
				   A.LANGSIR_TON, 
				   A.LANGSIR_RP, 
				   A.LANGSIR_RP_KG, 
				   A.COST_JAN, A.COST_FEB, A.COST_MAR, 
				   A.COST_APR, A.COST_MAY, A.COST_JUN, 
				   A.COST_JUL, A.COST_AUG, A.COST_SEP, 
				   A.COST_OCT, A.COST_NOV, A.COST_DEC, 
				   A.COST_SETAHUN, A.COST_SMS1, A.COST_SMS2,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PANEN_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PANEN_COST_ELEMENT (
						TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, COST_ELEMENT, SUMBER_BIAYA_UNIT, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, TON, JANJANG, BJR_AFD, JARAK_PKS, PERSEN_LANGSIR, BIAYA_PEMANEN_HK, BIAYA_PEMANEN_RP_BASIS, BIAYA_PEMANEN_RP_PREMI, BIAYA_PEMANEN_RP_TOTAL, BIAYA_PEMANEN_RP_KG, BIAYA_SPV_RP_BASIS, BIAYA_SPV_RP_PREMI, BIAYA_SPV_RP_TOTAL, BIAYA_SPV_RP_KG, BIAYA_ALAT_PANEN_RP_KG, BIAYA_ALAT_PANEN_RP_TOTAL, TUKANG_MUAT_BASIS, TUKANG_MUAT_PREMI, TUKANG_MUAT_TOTAL, TUKANG_MUAT_RP_KG, SUPIR_PREMI, SUPIR_RP_KG, ANGKUT_TBS_RP_KG_KM, ANGKUT_TBS_RP_ANGKUT, ANGKUT_TBS_RP_KG, KRANI_BUAH_BASIS, KRANI_BUAH_PREMI, KRANI_BUAH_TOTAL, KRANI_BUAH_RP_KG, LANGSIR_TON, LANGSIR_RP, LANGSIR_RP_KG, COST_JAN, COST_FEB, COST_MAR, COST_APR, COST_MAY, COST_JUN, COST_JUL, COST_AUG, COST_SEP, COST_OCT, COST_NOV, COST_DEC, COST_SETAHUN, COST_SMS1, COST_SMS2, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['TRX_RKT_CODE']."',
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['SUMBER_BIAYA_UNIT']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
					    '".$row['TON']."', 
						'".$row['JANJANG']."', 
						'".$row['BJR_AFD']."',
						'".$row['JARAK_PKS']."',
					    '".$row['PERSEN_LANGSIR']."',
					    '".$row['BIAYA_PEMANEN_HK']."',
					    '".$row['BIAYA_PEMANEN_RP_BASIS']."',
					    '".$row['BIAYA_PEMANEN_RP_PREMI']."',
					    '".$row['BIAYA_PEMANEN_RP_TOTAL']."',
					    '".$row['BIAYA_PEMANEN_RP_KG']."',
					    '".$row['BIAYA_SPV_RP_BASIS']."',
					    '".$row['BIAYA_SPV_RP_PREMI']."',
					    '".$row['BIAYA_SPV_RP_TOTAL']."',
					    '".$row['BIAYA_SPV_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_KG']."',
					    '".$row['BIAYA_ALAT_PANEN_RP_TOTAL']."',
					    '".$row['TUKANG_MUAT_BASIS']."',
					    '".$row['TUKANG_MUAT_PREMI']."',
					    '".$row['TUKANG_MUAT_TOTAL']."',
					    '".$row['TUKANG_MUAT_RP_KG']."',
					    '".$row['SUPIR_PREMI']."',
					    '".$row['SUPIR_RP_KG']."',
					    '".$row['ANGKUT_TBS_RP_KG_KM']."',
					    '".$row['ANGKUT_TBS_RP_ANGKUT']."',
					    '".$row['ANGKUT_TBS_RP_KG']."',
					    '".$row['KRANI_BUAH_BASIS']."',
					    '".$row['KRANI_BUAH_PREMI']."',
					    '".$row['KRANI_BUAH_TOTAL']."',
					    '".$row['KRANI_BUAH_RP_KG']."',
					    '".$row['LANGSIR_TON']."',
					    '".$row['LANGSIR_RP']."',
					    '".$row['LANGSIR_RP_KG']."',
					    '".$row['COST_JAN']."', '".$row['COST_FEB']."', '".$row['COST_MAR']."',
					    '".$row['COST_APR']."', '".$row['COST_MAY']."', '".$row['COST_JUN']."',
					    '".$row['COST_JUL']."', '".$row['COST_AUG']."', '".$row['COST_SEP']."',
					    '".$row['COST_OCT']."', '".$row['COST_NOV']."', '".$row['COST_DEC']."',
					    '".$row['COST_SETAHUN']."',
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate RKT Pupuk
	public function genRktPupuk($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TR_RKT_PUPUK_COST_ELEMENT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PUPUK_DISTRIBUSI
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TR_RKT_PUPUK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TR_RKT_PUPUK
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.TIPE_TRANSAKSI, 
				   A.JAN, A.FEB, A.MAR, 
				   A.APR, A.MAY, A.JUN, 
				   A.JUL, A.AUG, A.SEP, 
				   A.OCT, A.NOV, A.DEC, 
				   A.SETAHUN, A.COST_SMS1, A.COST_SMS2,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, TIPE_TRANSAKSI, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SETAHUN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['TRX_RKT_CODE']."',
					    '".$row['MATURITY_STAGE_SMS1']."',
					    '".$row['MATURITY_STAGE_SMS2']."',
						'".$row['TIPE_TRANSAKSI']."',
					    '".$row['JAN']."', '".$row['FEB']."', '".$row['MAR']."',
						'".$row['APR']."', '".$row['MAY']."', '".$row['JUN']."', 
						'".$row['JUL']."', '".$row['AUG']."', '".$row['SEP']."', 
						'".$row['OCT']."', '".$row['NOV']."', '".$row['DEC']."',
					    '".$row['SETAHUN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_PUPUK_COST_ELEMENT
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.COST_ELEMENT, 
				   A.TIPE_TRANSAKSI, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.COST_TRANSPORT_KG, 
				   A.COST_TOOLS_KG, 
				   A.COST_LABOUR_POKOK, 
				   A.DIS_COST_JAN, A.DIS_COST_FEB, A.DIS_COST_MAR, 
				   A.DIS_COST_APR, A.DIS_COST_MAY, A.DIS_COST_JUN, 
				   A.DIS_COST_JUL, A.DIS_COST_AUG, A.DIS_COST_SEP, 
				   A.DIS_COST_OCT, A.DIS_COST_NOV, A.DIS_COST_DEC, 
				   A.DIS_COST_YEAR, A.COST_SMS1, A.COST_SMS2,	   
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK_COST_ELEMENT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK_COST_ELEMENT (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, COST_ELEMENT, TIPE_TRANSAKSI, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, COST_TRANSPORT_KG, COST_TOOLS_KG, COST_LABOUR_POKOK, DIS_COST_JAN, DIS_COST_FEB, DIS_COST_MAR, DIS_COST_APR, DIS_COST_MAY, DIS_COST_JUN, DIS_COST_JUL, DIS_COST_AUG, DIS_COST_SEP, DIS_COST_OCT, DIS_COST_NOV, DIS_COST_DEC, DIS_COST_YEAR, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, COST_SMS1, COST_SMS2
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['COST_ELEMENT']."', 
						'".$row['TIPE_TRANSAKSI']."', 
						'".$row['TRX_RKT_CODE']."', 
						'".$row['MATURITY_STAGE_SMS1']."', 
						'".$row['MATURITY_STAGE_SMS2']."', 
						'".$row['COST_TRANSPORT_KG']."', 
						'".$row['COST_TOOLS_KG']."', 
						'".$row['COST_LABOUR_POKOK']."', 
						'".$row['DIS_COST_JAN']."', '".$row['DIS_COST_FEB']."', '".$row['DIS_COST_MAR']."', 
						'".$row['DIS_COST_APR']."', '".$row['DIS_COST_MAY']."', '".$row['DIS_COST_JUN']."', 
						'".$row['DIS_COST_JUL']."', '".$row['DIS_COST_AUG']."', '".$row['DIS_COST_SEP']."', 
						'".$row['DIS_COST_OCT']."', '".$row['DIS_COST_NOV']."', '".$row['DIS_COST_DEC']."', 
						'".$row['DIS_COST_YEAR']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['COST_SMS1']."',
					    '".$row['COST_SMS2']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
		
		//get new data - TR_RKT_PUPUK_DISTRIBUSI
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.TIPE_TRANSAKSI, 
				   A.TRX_RKT_CODE, 
				   A.MATURITY_STAGE_SMS1, 
				   A.MATURITY_STAGE_SMS2, 
				   A.DIS_JAN, A.MATERIAL_CODE_JAN, 
				   A.DIS_FEB, A.MATERIAL_CODE_FEB, 
				   A.DIS_MAR, A.MATERIAL_CODE_MAR, 
				   A.DIS_APR, A.MATERIAL_CODE_APR, 
				   A.DIS_MAY, A.MATERIAL_CODE_MAY, 
				   A.DIS_JUN, A.MATERIAL_CODE_JUN, 
				   A.DIS_JUL, A.MATERIAL_CODE_JUL, 
				   A.DIS_AUG, A.MATERIAL_CODE_AUG, 
				   A.DIS_SEP, A.MATERIAL_CODE_SEP, 
				   A.DIS_OCT, A.MATERIAL_CODE_OCT, 
				   A.DIS_NOV, A.MATERIAL_CODE_NOV, 
				   A.DIS_DEC, A.MATERIAL_CODE_DEC, 
				   A.DIS_TOTAL, A.MATERIAL_TOTAL, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TR_RKT_PUPUK_DISTRIBUSI A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TR_RKT_PUPUK_DISTRIBUSI (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, TRX_RKT_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, DIS_JAN, MATERIAL_CODE_JAN, DIS_FEB, MATERIAL_CODE_FEB, DIS_MAR, MATERIAL_CODE_MAR, DIS_APR, MATERIAL_CODE_APR, DIS_MAY, MATERIAL_CODE_MAY, DIS_JUN, MATERIAL_CODE_JUN, DIS_JUL, MATERIAL_CODE_JUL, DIS_AUG, MATERIAL_CODE_AUG, DIS_SEP, MATERIAL_CODE_SEP, DIS_OCT, MATERIAL_CODE_OCT, DIS_NOV, MATERIAL_CODE_NOV, DIS_DEC, MATERIAL_CODE_DEC, DIS_TOTAL, MATERIAL_TOTAL, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
						'".$row['TIPE_TRANSAKSI']."', 
						'".$row['TRX_RKT_CODE']."', 
						'".$row['MATURITY_STAGE_SMS1']."', 
						'".$row['MATURITY_STAGE_SMS2']."', 
						'".$row['DIS_JAN']."', '".$row['MATERIAL_CODE_JAN']."', 
						'".$row['DIS_FEB']."', '".$row['MATERIAL_CODE_FEB']."', 
						'".$row['DIS_MAR']."', '".$row['MATERIAL_CODE_MAR']."', 
						'".$row['DIS_APR']."', '".$row['MATERIAL_CODE_APR']."', 
						'".$row['DIS_MAY']."', '".$row['MATERIAL_CODE_MAY']."', 
						'".$row['DIS_JUN']."', '".$row['MATERIAL_CODE_JUN']."', 
						'".$row['DIS_JUL']."', '".$row['MATERIAL_CODE_JUL']."', 
						'".$row['DIS_AUG']."', '".$row['MATERIAL_CODE_AUG']."', 
						'".$row['DIS_SEP']."', '".$row['MATERIAL_CODE_SEP']."', 
						'".$row['DIS_OCT']."', '".$row['MATERIAL_CODE_OCT']."', 
						'".$row['DIS_NOV']."', '".$row['MATERIAL_CODE_NOV']."', 
						'".$row['DIS_DEC']."', '".$row['MATERIAL_CODE_DEC']."', 
						'".$row['DIS_TOTAL']."', '".$row['MATERIAL_TOTAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate Master Maturity Stage
	public function genMasterMaturityStage($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.SMS_TANAM, 
				   A.SMS_VIEW, 
				   A.UMUR_TANAM, 
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_MATURITY_STAGE A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_MATURITY_STAGE (
						SMS_TANAM, SMS_VIEW, UMUR_TANAM, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['SMS_TANAM']."',
					    '".$row['SMS_VIEW']."',
					    '".$row['UMUR_TANAM']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Rotasi Otomatis
	public function genMasterRotasiOtomatis($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.ROTASI_SMS1, 
				   A.ROTASI_SMS2, 
				   A.BULAN, 
				   A.JAN, 
				   A.FEB, 
				   A.MAR, 
				   A.APR, 
				   A.MAY, 
				   A.JUN, 
				   A.JUL, 
				   A.AUG, 
				   A.SEP, 
				   A.OCT, 
				   A.NOV, 
				   A.DEC,  
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ROTASI_OTOMATIS A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ROTASI_OTOMATIS (
						ROTASI_SMS1, ROTASI_SMS2, BULAN, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['ROTASI_SMS1']."',
					    '".$row['ROTASI_SMS2']."',
					    '".$row['BULAN']."',
					    '".$row['JAN']."',
					    '".$row['FEB']."',
					    '".$row['MAR']."',
					    '".$row['APR']."',
					    '".$row['MAY']."',
					    '".$row['JUN']."',
					    '".$row['JUL']."',
					    '".$row['AUG']."',
					    '".$row['SEP']."',
					    '".$row['OCT']."',
					    '".$row['NOV']."',
					    '".$row['DEC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master User
	public function genMasterUser($params = array())
    {
		$result = true;
		//get new data
		$query = "
			SELECT 
				   A.NIK, 
				   A.USER_NAME, 
				   A.FULL_NAME, 
				   A.PASSWORD, 
				   A.POSITION_CODE, 
				   A.POSITION, 
				   A.GRADE_CODE, 
				   A.GRADE, 
				   A.USER_LEVEL, 
				   A.DEPARTMENT_CODE, 
				   A.DEPARTMENT, 
				   A.DIVISION_CODE, 
				   A.DIVISION, 
				   A.COMPANY_CODE, 
				   A.LOCATION_CODE,  
				   A.LOCATION,  
				   A.EMAIL,  
				   A.SPV_NIK,  
				   A.SPV,  
				   A.ACTIVE,  
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   TO_DATE(A.LAST_LOGIN, 'DD-MM-RRRR') AS LAST_LOGIN,
				   BA_CODE,
				   USER_ROLE,
				   REFERENCE_ROLE
			FROM TM_USER A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_USER (
						NIK, USER_NAME, FULL_NAME, PASSWORD, POSITION_CODE, POSITION, GRADE_CODE, GRADE, USER_LEVEL, DEPARTMENT_CODE, DEPARTMENT, DIVISION_CODE, 
						DIVISION, COMPANY_CODE, LOCATION_CODE, LOCATION, EMAIL, SPV_NIK, SPV, ACTIVE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, 
						LAST_LOGIN, BA_CODE, USER_ROLE, REFERENCE_ROLE
					) VALUES (
						'".$row['NIK']."',
					    '".$row['USER_NAME']."',
					    '".$row['FULL_NAME']."',
					    '".$row['PASSWORD']."',
					    '".$row['POSITION_CODE']."',
					    '".$row['POSITION']."',
					    '".$row['GRADE_CODE']."',
					    '".$row['GRADE']."',
					    '".$row['USER_LEVEL']."',
					    '".$row['DEPARTMENT_CODE']."',
					    '".$row['DEPARTMENT']."',
					    '".$row['DIVISION_CODE']."',
					    '".$row['DIVISION']."',
					    '".$row['COMPANY_CODE']."',
					    '".$row['LOCATION_CODE']."',
					    '".$row['LOCATION']."',
					    '".$row['EMAIL']."',
					    '".$row['SPV_NIK']."',
					    '".$row['SPV']."',
					    '".$row['ACTIVE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
						TO_DATE('".$row['LAST_LOGIN']."', 'DD-MM-RRRR'),
						BA_CODE,
						USER_ROLE,
						REFERENCE_ROLE
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Module
	public function genMasterModule($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.CODE, 
				   A.NAME, 
				   A.PARENT_MODULE, 
				   A.ITEM_NAME, 
				   A.ICON, 
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_MODULE A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_MODULE (
						CODE, NAME, PARENT_MODULE, ITEM_NAME, ICON, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['CODE']."',
					    '".$row['NAME']."',
					    '".$row['PARENT_MODULE']."',
					    '".$row['ITEM_NAME']."',
					    '".$row['ICON']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master Access Right
	public function genMasterAccessRight($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.USER_ROLE, 
				   A.MODULE_CODE, 
				   A.AUTHORIZED, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME
			FROM T_ACCESSRIGHT A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_ACCESSRIGHT (
						USER_ROLE, MODULE_CODE, AUTHORIZED, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME
					) VALUES (
						'".$row['USER_ROLE']."',
					    '".$row['MODULE_CODE']."',
					    '".$row['AUTHORIZED']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Parameter
	public function genParameter($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM T_PARAMETER
					WHERE BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT 
				   A.BA_CODE, 
				   A.PARAMETER_CODE, 
				   A.PARAMETER_NAME, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_PARAMETER A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_PARAMETER (
						BA_CODE, PARAMETER_CODE, PARAMETER_NAME, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['BA_CODE']."',
					    '".$row['PARAMETER_CODE']."',
					    '".$row['PARAMETER_NAME']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Parameter Value
	public function genParameterValue($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM T_PARAMETER_VALUE
					WHERE BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT 
				   A.BA_CODE, 
				   A.PARAMETER_CODE, 
				   A.PARAMETER_VALUE_CODE, 
				   A.PARAMETER_VALUE, 
				   A.PARAMETER_VALUE_2, 
				   A.PARAMETER_VALUE_3, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_PARAMETER_VALUE A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_PARAMETER_VALUE (
						BA_CODE, PARAMETER_CODE, PARAMETER_VALUE_CODE, PARAMETER_VALUE, PARAMETER_VALUE_2, PARAMETER_VALUE_3, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['BA_CODE']."',
					    '".$row['PARAMETER_CODE']."',
					    '".$row['PARAMETER_VALUE_CODE']."',
					    '".$row['PARAMETER_VALUE']."',
					    '".$row['PARAMETER_VALUE_2']."',
					    '".$row['PARAMETER_VALUE_3']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Parameter Value Range
	public function genParameterValueRange($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM T_PARAMETER_VALUE_RANGE
					WHERE BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT 
				   A.BA_CODE, 
				   A.PARAMETER_CODE, 
				   A.PARAMETER_VALUE_CODE, 
				   A.MIN_VALUE, 
				   A.MAX_VALUE, 
				   A.RATE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_PARAMETER_VALUE_RANGE A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_PARAMETER_VALUE_RANGE (
						BA_CODE, PARAMETER_CODE, PARAMETER_VALUE_CODE, MIN_VALUE, MAX_VALUE, RATE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['BA_CODE']."',
					    '".$row['PARAMETER_CODE']."',
					    '".$row['PARAMETER_VALUE_CODE']."',
					    '".$row['MIN_VALUE']."',
					    '".$row['MAX_VALUE']."',
					    '".$row['RATE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Sequence
	public function genSequence($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.SEQ_NUM, 
				   A.TASK_NAME, 
				   A.REMARKS, 
				   A.TABLE_NAME, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_SEQ A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_SEQ (
						SEQ_NUM, TASK_NAME, REMARKS, TABLE_NAME, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['SEQ_NUM']."',
					    '".$row['TASK_NAME']."',
					    '".$row['REMARKS']."',
					    '".$row['TABLE_NAME']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Sequence Check
	public function genSequenceCheck($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM T_SEQ_CHECK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TASK_NAME, 
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM T_SEQ_CHECK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE 1 = 1
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO T_SEQ_CHECK (
						PERIOD_BUDGET, BA_CODE, TASK_NAME, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TASK_NAME']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master RPT Group
	public function genMasterRptGroup($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.GROUP_CODE, 
				   A.DESCRIPTION, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_RPT_GROUP A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_RPT_GROUP (
						GROUP_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['GROUP_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Master RPT Mapping Act
	public function genMasterRptMappingAct($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.GROUP_CODE, 
				   A.PARENT_CODE, 
				   A.MATURITY_STAGE, 
				   A.ACTIVITY_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_RPT_MAPPING_ACT A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_RPT_MAPPING_ACT (
						GROUP_CODE, PARENT_CODE, MATURITY_STAGE, ACTIVITY_CODE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['GROUP_CODE']."',
					    '".$row['PARENT_CODE']."',
					    '".$row['MATURITY_STAGE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate master catu
	public function genMasterCatu($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_CATU
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TM_CATU_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND REGION_CODE = '".$row['REGION_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TM_CATU
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.RICE_PORTION, 
				   A.PRICE_KG, 
				   A.CATU_BERAS, 
				   A.HKE_BULAN, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_CATU A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_CATU (
						PERIOD_BUDGET, BA_CODE, EMPLOYEE_STATUS, RICE_PORTION, PRICE_KG, CATU_BERAS, HKE_BULAN, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['RICE_PORTION']."',
					    '".$row['PRICE_KG']."',
					    '".$row['CATU_BERAS']."',
					    '".$row['HKE_BULAN']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TM_CATU_SUM
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.CATU_BERAS_SUM, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_CATU_SUM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_CATU_SUM (
						PERIOD_BUDGET, BA_CODE, CATU_BERAS_SUM, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['CATU_BERAS_SUM']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate Checkroll HK
	public function genCheckrollHk($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_CHECKROLL_HK
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.HARI_SETAHUN, 
				   A.MINGGU_SETAHUN, 
				   A.LIBUR_SETAHUN, 
				   A.HK, 
				   A.CUTI, 
				   A.SAKIT, 
				   A.IZIN, 
				   A.HAID, 
				   A.HKE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_CHECKROLL_HK A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_CHECKROLL_HK (
						PERIOD_BUDGET, BA_CODE, EMPLOYEE_STATUS, HARI_SETAHUN, MINGGU_SETAHUN, LIBUR_SETAHUN, HK, CUTI, SAKIT, IZIN, HAID, HKE,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['HARI_SETAHUN']."',
					    '".$row['MINGGU_SETAHUN']."',
					    '".$row['LIBUR_SETAHUN']."',
					    '".$row['HK']."',
					    '".$row['CUTI']."',
					    '".$row['SAKIT']."',
					    '".$row['HAID']."',
					    '".$row['IZIN']."',
					    '".$row['HKE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Tarif Tunjangan
	public function genTarifTunjangan($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_TARIF_TUNJANGAN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JOB_CODE, 
				   A.EMPLOYEE_STATUS, 
				   A.TUNJANGAN_TYPE, 
				   A.VALUE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_TARIF_TUNJANGAN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_TARIF_TUNJANGAN (
						PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, TUNJANGAN_TYPE, VALUE,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JOB_CODE']."',
					    '".$row['EMPLOYEE_STATUS']."',
					    '".$row['TUNJANGAN_TYPE']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Asset
	public function genAsset($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_ASSET
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ASSET_CODE, 
				   A.DESCRIPTION, 
				   A.COA_CODE, 
				   A.UOM, 
				   A.STATUS, 
				   A.PRICE, 
				   A.BASIC_NORMA_CODE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_ASSET A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_ASSET (
						PERIOD_BUDGET, BA_CODE, ASSET_CODE, DESCRIPTION, COA_CODE, UOM, STATUS, PRICE, BASIC_NORMA_CODE, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ASSET_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['COA_CODE']."',
					    '".$row['UOM']."',
					    '".$row['STATUS']."',
					    '".$row['PRICE']."',
					    '".$row['BASIC_NORMA_CODE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Material
	public function genMaterial($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_MATERIAL
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.MATERIAL_CODE, 
				   A.MATERIAL_NAME, 
				   A.UOM, 
				   A.VALUATION_CLASS, 
				   A.COA_CODE, 
				   A.PRICE, 
				   A.BASIC_NORMA_CODE, 
				   A.FLAG,
				   A.DETAIL_CAT_CODE,
				   A.DETAIL_CAT_DESC,
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_MATERIAL A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_MATERIAL (
						PERIOD_BUDGET, BA_CODE, MATERIAL_CODE, MATERIAL_NAME, UOM, VALUATION_CLASS, COA_CODE, PRICE, BASIC_NORMA_CODE, FLAG, DETAIL_CAT_CODE, DETAIL_CAT_DESC, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['MATERIAL_NAME']."',
					    '".$row['UOM']."',
					    '".$row['VALUATION_CLASS']."',
					    '".$row['COA_CODE']."',
					    '".$row['PRICE']."',
					    '".$row['BASIC_NORMA_CODE']."',
					    '".$row['FLAG']."',
					    '".$row['DETAIL_CAT_CODE']."',
					    '".$row['DETAIL_CAT_DESC']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Standar Jam Kerja
	public function genStandarJamKerja($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_STANDART_JAM_KERJA_WRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JAM_KERJA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_STANDART_JAM_KERJA_WRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_STANDART_JAM_KERJA_WRA (
						PERIOD_BUDGET, BA_CODE, JAM_KERJA,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JAM_KERJA']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Norma Basic
	public function genNormaBasic($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_BASIC
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.BASIC_NORMA_CODE, 
				   A.DESCRIPTION, 
				   A.PERCENT_INCREASE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   A.FLAG_TEMP
			FROM TN_BASIC A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_BASIC (
						PERIOD_BUDGET, BA_CODE, BASIC_NORMA_CODE, DESCRIPTION, PERCENT_INCREASE, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['BASIC_NORMA_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['PERCENT_INCREASE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Norma Kastrasi Sanitasi
	public function genNormaKastrasiSanitasi($params = array())
    {
		$result = true;
		
		//get new data
		$query = "
			SELECT 
				   A.ACTIVITY_CODE, 
				   A.LAND_SUITABILITY, 
				   A.UMUR, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_KASTRASI_SANITASI A
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_KASTRASI_SANITASI (
						ACTIVITY_CODE, LAND_SUITABILITY, UMUR, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						'".$row['ACTIVITY_CODE']."',
					    '".$row['LAND_SUITABILITY']."',
					    '".$row['UMUR']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate Norma Panen Variabel
	public function genNormaPanenVariabel($params = array())
    {
		$result = true;
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_VARIABLE
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					AND BA_CODE = '".$row['BA_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		//get new data
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.PANEN_CODE, 
				   A.DESCRIPTION, 
				   A.VALUE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   A.FLAG_TEMP
			FROM TN_PANEN_VARIABLE A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_VARIABLE (
						PERIOD_BUDGET, BA_CODE, PANEN_CODE, DESCRIPTION, VALUE, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['PANEN_CODE']."',
					    '".$row['DESCRIPTION']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }		
        return true;
    }
	
	//generate norma harga barang
	public function genNormaHargaBarang($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_HARGA_BARANG
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
						
					DELETE FROM TN_HARGA_BARANG_SUM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND REGION_CODE = '".$row['REGION_CODE']."';
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_HARGA_BARANG
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.MATERIAL_CODE, 
				   A.PRICE, 
				   A.STATUS, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   TRIGGER_UPDATE,
				   FLAG_TEMP
			FROM TN_HARGA_BARANG A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BARANG (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, PRICE, STATUS, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['PRICE']."',
					    '".$row['STATUS']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	

		//get new data - TN_HARGA_BARANG_SUM
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND A.REGION_CODE IN (
					SELECT REGION_CODE
					FROM TM_ORGANIZATION
					WHERE UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'
					GROUP BY REGION_CODE
				)";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.MATERIAL_CODE, 
				   A.PRICE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   TRIGGER_UPDATE
			FROM TN_HARGA_BARANG_SUM A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_HARGA_BARANG_SUM (
						PERIOD_BUDGET, REGION_CODE, MATERIAL_CODE, PRICE, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma vra
	public function genNormaVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_VRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_VRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.MIN_YEAR, 
				   A.MAX_YEAR, 
				   A.QTY_DAY, 
				   A.DAY_YEAR_VRA, 
				   A.SUB_RVRA_CODE, 
				   A.MATERIAL_CODE, 
				   A.QTY_UOM, 
				   A.PRICE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   TRIGGER_UPDATE,
				   FLAG_TEMP
			FROM TN_VRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_VRA (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, VRA_CODE, MIN_YEAR, MAX_YEAR, QTY_DAY, DAY_YEAR_VRA, SUB_RVRA_CODE, MATERIAL_CODE, QTY_UOM, PRICE, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['MIN_YEAR']."',
					    '".$row['MAX_YEAR']."',
					    '".$row['QTY_DAY']."',
					    '".$row['DAY_YEAR_VRA']."',
					    '".$row['SUB_RVRA_CODE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['QTY_UOM']."',
					    '".$row['PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma vra pinjam
	public function genNormaVraPinjam($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_VRA_PINJAM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND REGION_CODE = '".$row['REGION_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_VRA_PINJAM
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND A.REGION_CODE IN (
					SELECT REGION_CODE
					FROM TM_ORGANIZATION
					WHERE UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'
					GROUP BY REGION_CODE
				)";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.VRA_CODE, 
				   A.RP_QTY, 
				   A.FLAG_TEMP, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_VRA_PINJAM A
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_VRA_PINJAM (
						PERIOD_BUDGET, REGION_CODE, VRA_CODE, RP_QTY, FLAG_TEMP, INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['RP_QTY']."',
					    '".$row['FLAG_TEMP']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma pupuk tbm
	public function genNormaPupukTbm($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PUPUK_TBM2_LESS
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_VRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.LAND_TYPE, 
				   A.PALM_AGE, 
				   A.MATURITY_STAGE, 
				   A.MATERIAL_CODE, 
				   A.ROTASI, 
				   A.DOSIS, 
				   A.JUMLAH, 
				   A.PRICE, 
				   A.PRICE_ROTASI, 
				   A.PRICE_YEAR, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   TRIGGER_UPDATE,
				   FLAG_TEMP
			FROM TN_PUPUK_TBM2_LESS A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PUPUK_TBM2_LESS (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, LAND_TYPE, PALM_AGE, MATURITY_STAGE, MATERIAL_CODE, ROTASI, DOSIS, 
						JUMLAH, PRICE, PRICE_ROTASI, PRICE_YEAR, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['PALM_AGE']."',
					    '".$row['MATURITY_STAGE']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['ROTASI']."',
					    '".$row['DOSIS']."',
					    '".$row['JUMLAH']."',
					    '".$row['PRICE']."',
					    '".$row['PRICE_ROTASI']."',
					    '".$row['PRICE_YEAR']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma pupuk tm
	public function genNormaPupukTm($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PUPUK_TBM2_TM
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_VRA
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE, 
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.BLOCK_CODE, 
				   A.JENIS_TANAM, 
				   A.POKOK, 
				   A.BULAN_PEMUPUKAN, 
				   A.MATERIAL_CODE, 
				   A.DOSIS, 
				   A.JUMLAH, 
				   A.HARGA, 
				   A.BIAYA, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   TRIGGER_UPDATE,
				   HA_PUPUK,
				   FLAG_TEMP
			FROM TN_PUPUK_TBM2_TM A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PUPUK_TBM2_TM (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, JENIS_TANAM, POKOK, BULAN_PEMUPUKAN, MATERIAL_CODE, 
						DOSIS, JUMLAH, HARGA, BIAYA, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, TRIGGER_UPDATE, HA_PUPUK, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['BLOCK_CODE']."',
					    '".$row['JENIS_TANAM']."',
					    '".$row['POKOK']."',
					    '".$row['BULAN_PEMUPUKAN']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['DOSIS']."',
					    '".$row['JUMLAH']."',
					    '".$row['HARGA']."',
					    '".$row['BIAYA']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['TRIGGER_UPDATE']."',
					    '".$row['HA_PUPUK']."',
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma perkerasan jalan
	public function genNormaPerkerasanJalan($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PERKERASAN_JALAN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.LEBAR, 
				   A.PANJANG, 
				   A.TEBAL, 
				   A.MATERIAL_CODE, 
				   A.MATERIAL_QTY, 
				   A.PRICE, 
				   A.VRA_CODE_DT, 
				   A.RP_KM_DT, 
				   A.KAPASITAS_DT, 
				   A.KECEPATAN_DT, 
				   A.JAM_KERJA_DT, 
				   A.VRA_CODE_EXCAV, 
				   A.RP_HM_EXCAV, 
				   A.KAPASITAS_EXCAV, 
				   A.VRA_CODE_COMPACTOR, 
				   A.RP_HM_COMPACTOR, 
				   A.KAPASITAS_COMPACTOR, 
				   A.VRA_CODE_GRADER, 
				   A.RP_HM_GRADER, 
				   A.KAPASITAS_GRADER, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PERKERASAN_JALAN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PERKERASAN_JALAN (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, LEBAR, PANJANG, TEBAL, MATERIAL_CODE, MATERIAL_QTY, PRICE, 
						VRA_CODE_DT, RP_KM_DT, KAPASITAS_DT, KECEPATAN_DT, JAM_KERJA_DT, VRA_CODE_EXCAV, RP_HM_EXCAV, KAPASITAS_EXCAV, 
						VRA_CODE_COMPACTOR, RP_HM_COMPACTOR, KAPASITAS_COMPACTOR, VRA_CODE_GRADER, RP_HM_GRADER, KAPASITAS_GRADER, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
					    '".$row['LEBAR']."',
						'".$row['PANJANG']."',
					    '".$row['TEBAL']."',
					    '".$row['MATERIAL_CODE']."',
					    '".$row['MATERIAL_QTY']."',
					    '".$row['PRICE']."',
					    '".$row['VRA_CODE_DT']."',
					    '".$row['RP_KM_DT']."',
					    '".$row['KAPASITAS_DT']."',
					    '".$row['KECEPATAN_DT']."',
					    '".$row['JAM_KERJA_DT']."',
					    '".$row['VRA_CODE_EXCAV']."',
					    '".$row['RP_HM_EXCAV']."',
					    '".$row['KAPASITAS_EXCAV']."',
					    '".$row['VRA_CODE_COMPACTOR']."',
					    '".$row['RP_HM_COMPACTOR']."',
					    '".$row['KAPASITAS_COMPACTOR']."',
					    '".$row['VRA_CODE_GRADER']."',
					    '".$row['RP_HM_GRADER']."',
					    '".$row['KAPASITAS_GRADER']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma perkerasan jalan harga
	public function genNormaPerkerasanJalanHarga($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PERKERASAN_JALAN_HARGA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.JARAK_RANGE, 
				   A.JARAK_AVG, 
				   A.JARAK_PP, 
				   A.MATERIAL_QTY, 
				   A.TRIP_MATERIAL, 
				   A.BIAYA_MATERIAL, 
				   A.DT_TRIP, 
				   A.DT_PRICE, 
				   A.EXCAV_HM, 
				   A.EXCAV_PRICE, 
				   A.COMPACTOR_HM, 
				   A.COMPACTOR_PRICE, 
				   A.GRADER_HM, 
				   A.GRADER_PRICE, 
				   A.INTERNAL_PRICE, 
				   A.EXTERNAL_PERCENT, 
				   A.EXTERNAL_BENEFIT, 
				   A.EXTERNAL_PRICE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PERKERASAN_JALAN_HARGA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PERKERASAN_JALAN_HARGA (
						PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, JARAK_RANGE, JARAK_AVG, JARAK_PP, MATERIAL_QTY, TRIP_MATERIAL, 
						BIAYA_MATERIAL, DT_TRIP, DT_PRICE, EXCAV_HM, EXCAV_PRICE, COMPACTOR_HM, COMPACTOR_PRICE, GRADER_HM, 
						GRADER_PRICE, INTERNAL_PRICE, EXTERNAL_PERCENT, EXTERNAL_BENEFIT, EXTERNAL_PRICE, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
						'".$row['JARAK_RANGE']."',
					    '".$row['JARAK_AVG']."',
					    '".$row['JARAK_PP']."',
					    '".$row['MATERIAL_QTY']."',
					    '".$row['TRIP_MATERIAL']."',
					    '".$row['BIAYA_MATERIAL']."',
					    '".$row['DT_TRIP']."',
					    '".$row['DT_PRICE']."',
					    '".$row['EXCAV_HM']."',
					    '".$row['EXCAV_PRICE']."',
					    '".$row['COMPACTOR_HM']."',
					    '".$row['COMPACTOR_PRICE']."',
					    '".$row['GRADER_HM']."',
					    '".$row['GRADER_PRICE']."',
					    '".$row['INTERNAL_PRICE']."',
					    '".$row['EXTERNAL_PERCENT']."',
					    '".$row['EXTERNAL_BENEFIT']."',
					    '".$row['EXTERNAL_PRICE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    } 
	
	//generate norma infrastruktur
	public function genNormaInfrastruktur($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_INFRASTRUKTUR
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data - TN_INFRASTRUKTUR
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.REGION_CODE,
				   A.BA_CODE, 
				   A.ACTIVITY_CODE, 
				   A.ACTIVITY_CLASS, 
				   A.LAND_TYPE, 
				   A.TOPOGRAPHY, 
				   A.COST_ELEMENT, 
				   A.SUB_COST_ELEMENT, 
				   A.QTY_INFRA, 
				   A.QTY_ALAT, 
				   A.ROTASI, 
				   A.VOLUME, 
				   A.QTY_HA, 
				   A.RP_QTY_EXTERNAL, 
				   A.RP_HA_EXTERNAL, 
				   A.HARGA_INTERNAL, 
				   A.RP_QTY_INTERNAL, 
				   A.RP_HA_INTERNAL, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_INFRASTRUKTUR A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_INFRASTRUKTUR (
						PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, COST_ELEMENT, SUB_COST_ELEMENT, 
						QTY_INFRA, QTY_ALAT, ROTASI, VOLUME, QTY_HA, RP_QTY_EXTERNAL, RP_HA_EXTERNAL, HARGA_INTERNAL, 
						RP_QTY_INTERNAL, RP_HA_INTERNAL,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['REGION_CODE']."',
						'".$row['BA_CODE']."',
					    '".$row['ACTIVITY_CODE']."',
						'".$row['ACTIVITY_CLASS']."',
					    '".$row['LAND_TYPE']."',
					    '".$row['TOPOGRAPHY']."',
					    '".$row['COST_ELEMENT']."',
					    '".$row['SUB_COST_ELEMENT']."',
					    '".$row['QTY_INFRA']."',
					    '".$row['QTY_ALAT']."',
					    '".$row['ROTASI']."',
					    '".$row['VOLUME']."',
					    '".$row['QTY_HA']."',
					    '".$row['RP_QTY_EXTERNAL']."',
					    '".$row['RP_HA_EXTERNAL']."',
					    '".$row['HARGA_INTERNAL']."',
					    '".$row['RP_QTY_INTERNAL']."',
					    '".$row['RP_HA_INTERNAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
						FLAG_TEMP
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    } 
	
	//generate norma panen premi cost unit
	public function genNormaPanenPremiCostUnit($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_PREMI_COST_UNIT
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JARAK_ANGKUT, 
				   A.TARGET, 
				   A.RIT, 
				   A.RP_KM_INTERNAL, 
				   A.RP_KG_INTERNAL, 
				   A.RP_KM_EXTERNAL, 
				   A.RP_KG_EXTERNAL, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_PREMI_COST_UNIT A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_PREMI_COST_UNIT (
						PERIOD_BUDGET, BA_CODE, JARAK_ANGKUT, TARGET, RIT, RP_KM_INTERNAL, RP_KG_INTERNAL, RP_KM_EXTERNAL, RP_KG_EXTERNAL, 
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JARAK_ANGKUT']."',
					    '".$row['TARGET']."',
						'".$row['RIT']."',
					    '".$row['RP_KM_INTERNAL']."',
					    '".$row['RP_KG_INTERNAL']."',
					    '".$row['RP_KM_EXTERNAL']."',
					    '".$row['RP_KG_EXTERNAL']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen krani buah
	public function genNormaPanenKraniBuah($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_KRANI_BUAH
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.TARGET, 
				   A.BASIS, 
				   A.TARIF_BASIS, 
				   A.SELISIH_OVER_BASIS, 
				   A.RP_HK, 
				   A.RP_KG_BASIS, 
				   A.TOTAL_RP_PREMI, 
				   A.RP_KG_PREMI, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TN_PANEN_KRANI_BUAH A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_KRANI_BUAH (
						PERIOD_BUDGET, BA_CODE, TARGET, BASIS, TARIF_BASIS, SELISIH_OVER_BASIS, RP_HK, RP_KG_BASIS, TOTAL_RP_PREMI, RP_KG_PREMI,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['TARGET']."',
					    '".$row['BASIS']."',
						'".$row['TARIF_BASIS']."',
					    '".$row['SELISIH_OVER_BASIS']."',
					    '".$row['RP_HK']."',
					    '".$row['RP_KG_BASIS']."',
					    '".$row['TOTAL_RP_PREMI']."',
					    '".$row['RP_KG_PREMI']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen loading
	public function genNormaPanenLoading($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_LOADING
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.JARAK_PKS_MIN, 
				   A.JARAK_PKS_MAX, 
				   A.TARGET_ANGKUT_TM_SUPIR, 
				   A.JUMLAH_TM, 
				   A.SELISIH_TM, 
				   A.TARIF_TM, 
				   A.RP_HK_TM, 
				   A.RP_BASIS_TM, 
				   A.RP_KG_BASIS_TM, 
				   A.RP_PREMI_TM, 
				   A.RP_KG_PREMI_TM, 
				   A.TARIF_SUPIR, 
				   A.RP_PREMI_SUPIR, 
				   A.RP_KG_PREMI_SUPIR, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_LOADING A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_LOADING (
						PERIOD_BUDGET, BA_CODE, JARAK_PKS_MIN, JARAK_PKS_MAX, TARGET_ANGKUT_TM_SUPIR, JUMLAH_TM, SELISIH_TM, TARIF_TM, RP_HK_TM, RP_BASIS_TM,
						RP_KG_BASIS_TM, RP_PREMI_TM, RP_KG_PREMI_TM, TARIF_SUPIR, RP_PREMI_SUPIR, RP_KG_PREMI_SUPIR,   
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['JARAK_PKS_MIN']."',
					    '".$row['JARAK_PKS_MAX']."',
						'".$row['TARGET_ANGKUT_TM_SUPIR']."',
					    '".$row['JUMLAH_TM']."',
					    '".$row['SELISIH_TM']."',
					    '".$row['TARIF_TM']."',
					    '".$row['RP_HK_TM']."',
					    '".$row['RP_BASIS_TM']."',
					    '".$row['RP_KG_BASIS_TM']."',
					    '".$row['RP_PREMI_TM']."',
					    '".$row['RP_KG_PREMI_TM']."',
					    '".$row['TARIF_SUPIR']."',
					    '".$row['RP_PREMI_SUPIR']."',
					    '".$row['RP_KG_PREMI_SUPIR']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen premi langsir
	public function genNormaPanenPremiLangsir($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_PREMI_LANGSIR
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.VRA_CODE, 
				   A.TON_TRIP, 
				   A.TRIP_HARI, 
				   A.TON_HARI, 
				   A.HM_TRIP, 
				   A.RP_HM, 
				   A.RP_TRIP, 
				   A.RP_KG, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_PREMI_LANGSIR A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_PREMI_LANGSIR (
						PERIOD_BUDGET, BA_CODE, VRA_CODE, TON_TRIP, TRIP_HARI, TON_HARI, HM_TRIP, RP_HM, RP_TRIP, RP_KG,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['VRA_CODE']."',
					    '".$row['TON_TRIP']."',
						'".$row['TRIP_HARI']."',
					    '".$row['TON_HARI']."',
					    '".$row['HM_TRIP']."',
					    '".$row['RP_HM']."',
					    '".$row['RP_TRIP']."',
					    '".$row['RP_KG']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen premi mandor
	public function genNormaPanenPremiMandor($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_PREMI_MANDOR
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.PREMI_MANDOR_CODE, 
				   A.DESCRIPTION, 
				   A.MIN_YIELD, 
				   A.MAX_YIELD, 
				   A.MIN_OER, 
				   A.MAX_OER, 
				   A.VALUE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_PREMI_MANDOR A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_PREMI_MANDOR (
						PERIOD_BUDGET, BA_CODE, PREMI_MANDOR_CODE, DESCRIPTION, MIN_YIELD, MAX_YIELD, MIN_OER, MAX_OER, VALUE,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['PREMI_MANDOR_CODE']."',
					    '".$row['DESCRIPTION']."',
						'".$row['MIN_YIELD']."',
					    '".$row['MAX_YIELD']."',
					    '".$row['MIN_OER']."',
					    '".$row['MAX_OER']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate location distribusi VRA
	public function genLocationDistVra($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TM_LOCATION_DIST_VRA
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.LOCATION_CODE, 
				   A.DESCRIPTION, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME
			FROM TM_LOCATION_DIST_VRA A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TM_LOCATION_DIST_VRA (
						PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['LOCATION_CODE']."',
					    '".$row['DESCRIPTION']."',
						'".$row['MIN_YIELD']."',
					    '".$row['MAX_YIELD']."',
					    '".$row['MIN_OER']."',
					    '".$row['MAX_OER']."',
					    '".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR')
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen premi topography
	public function genNormaPanenPremiTopography($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_PREMI_TOPOGRAPHY
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.AFD_CODE, 
				   A.PERCENTAGE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_PREMI_TOPOGRAPHY A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_PREMI_TOPOGRAPHY (
						PERIOD_BUDGET, BA_CODE, AFD_CODE, PERCENTAGE,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['AFD_CODE']."',
					    '".$row['PERCENTAGE']."',
						'".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
	
	//generate norma panen prod permanen
	public function genNormaPanenProdPermanen($params = array())
    {
		$result = true;
		
		//generate string untuk delete old data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT REGION_CODE, BA_CODE
			FROM TM_ORGANIZATION
			WHERE 1 = 1
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					DELETE FROM TN_PANEN_PROD_PEMANEN
					WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
						AND BA_CODE = '".$row['BA_CODE']."';
					";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
        }
				
		//get new data
		if($this->_siteCode <> 'ALL'){
			if ($this->_referenceRole == 'REGION_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(B.REGION_CODE)||'%'";
			elseif ($this->_referenceRole == 'BA_CODE')
				$where = "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(A.BA_CODE) ||'%'";
		}
		
		$query = "
			SELECT TO_DATE(A.PERIOD_BUDGET, 'DD-MM-RRRR') AS PERIOD_BUDGET,
				   A.BA_CODE, 
				   A.UMUR, 
				   A.TOPOGRAPHY, 
				   A.VALUE, 
				   A.INSERT_USER, 
				   TO_DATE(A.INSERT_TIME, 'DD-MM-RRRR') AS INSERT_TIME,
				   A.UPDATE_USER, 
				   TO_DATE(A.UPDATE_TIME, 'DD-MM-RRRR') AS UPDATE_TIME,
				   A.DELETE_USER, 
				   TO_DATE(A.DELETE_TIME, 'DD-MM-RRRR') AS DELETE_TIME,
				   FLAG_TEMP
			FROM TN_PANEN_PROD_PEMANEN A
			LEFT JOIN TM_ORGANIZATION B
				ON A.BA_CODE = B.BA_CODE
			WHERE to_char(A.PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
				$where
		";
		$rows = $this->_db->fetchAll($query);
		
		//generate string untuk insert new data
		if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
                $sql = "
					INSERT INTO TN_PANEN_PROD_PEMANEN (
						PERIOD_BUDGET, BA_CODE, UMUR, TOPOGRAPHY, VALUE,
						INSERT_USER, INSERT_TIME, UPDATE_USER, UPDATE_TIME, DELETE_USER, DELETE_TIME, FLAG_TEMP
					) VALUES (
						TO_DATE('".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
						'".$row['BA_CODE']."',
					    '".$row['UMUR']."',
					    '".$row['TOPOGRAPHY']."',
						'".$row['VALUE']."',
					    '".$row['INSERT_USER']."',
						TO_DATE('".$row['INSERT_TIME']."', 'DD-MM-RRRR'),
					    '".$row['UPDATE_USER']."',
						TO_DATE('".$row['UPDATE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['DELETE_USER']."',
						TO_DATE('".$row['DELETE_TIME']."', 'DD-MM-RRRR'),
					    '".$row['FLAG_TEMP']."'
					);
				";
				$this->_global->createSqlFile($params['filename'], $sql);
            }
			
			$this->_global->createSqlFile($params['filename'], "COMMIT;\n"); //add query untuk commit
        }	
        return true;
    }
}

