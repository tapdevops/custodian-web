<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Class untuk User
Function 			:	- checkAuth 			: cek login
						- isAuthExpired 		: cek apakah user tsb telah expired
						- countdownAuthExpired 	: hitung sisa waktu user expired
						- logLogin 				: insert log untuk login yang berhasil
						- logLoginFailed 		: insert log untuk login yang gagal
						- logLogout 			: insert log untuk logout
						- checkAcl 				: cek autentifikasi untuk module
						- getMenu 				: menampilkan menu yang dapat diakses oleh user ybs
						- countLoginFailed		: menghitung kesalahan login dari last login
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	09/04/2013
Update Terakhir		:	09/04/2013
Revisi				:	
Date				PIC			Keterangan
29/07/2013 			Doni		Menambahkan ldapAuth untuk konesi login ke LDAP

=========================================================================================================================
*/

//        use Zend\Session\Container; // We need this when using sessions


class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'TM_USER';
   // protected $_primary = array('BA_CODE', 'USER_NAME');
    protected $_primary = array( 'USER_NAME');

    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_tipe = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    }

    //cek login
	public function checkAuth($userName = '', $userPass = '', $userTipe = '')
    {
        $result = array(); $loginSuccess = false;
		
		//ldap check
		if ($this->ldapAuth($userName, $userPass)) {
			// setup zend_auth_adapter
			$adapter = new Zend_Auth_Adapter_DbTable(
				$this->_db,
				$this->_name,
				'USER_NAME'
			);
			$adapter->setIdentity($userName);
			$adapter->setCredential('1');
			$select = $adapter->getDbSelect();

			// do authentication
			$auth = Zend_Registry::get('auth');
			$data = $this->getUserRole($userName);

            if ($data) {
                if ($data->TIPE_AKSES == $userTipe || $data->TIPE_AKSES == 'ALL') {
                    $loginSuccess = true;
                    $result['error'] = '';
                } else {
                    $loginSuccess = false;
                    $result['error'] = 'NO_ACCESS';
                }

                if ($userTipe == '1') {
                    if ($data->ACTIVE == 'Y') {
                        $loginSuccess = true;
                        $result['error'] = '';
                    } else {
                        $loginSuccess = false;
                        $result['error'] = 'NO_ACCESS';
                    }
                } else if ($userTipe == '2') {
                    if ($data->HO_STATUS_ACTIVE == 'Y') {
                        $loginSuccess = true;
                        $result['error'] = '';
                    } else {
                        $loginSuccess = false;
                        $result['error'] = 'NO_ACCESS';
                    }
                }
            }
		}
		else {
			// setup zend_auth_adapter
			$adapter = new Zend_Auth_Adapter_DbTable(
				$this->_db,
				$this->_name,
				'USER_NAME',
				'PASSWORD',
				'ENCRYPT(?)'
			);
			$adapter->setIdentity($userName);
			$adapter->setCredential($userPass);
			$select = $adapter->getDbSelect();

			// do authentication
			$auth = Zend_Registry::get('auth');
			$login = $auth->authenticate($adapter);
			if ($login->isValid()) $loginSuccess = true;
			else $result['error'] = 'INVALID_PASSWORD';
			$data = $adapter->getResultRowObject(null, 'PASSWORD');		
		}

        $_SESSION['user_tipe'] = $userTipe;

        //$loginSuccess = true;

        if ($loginSuccess) {
            $auth->getStorage()->write($data);
            // -- login success
            $this->logLogin($userName);
            $result['status'] = true;
        }

        return $result;
    }
	
	//cek apakah user tsb telah expired
    public function isAuthExpired()
    {
        $auth = Zend_Registry::get('auth');
        $dateUpdate = $auth->getIdentity()->UPDATE_TIME;

        $sql = "
            SELECT (TO_DATE(SYSDATE, 'DD-MM-RRRR') -
                    TO_DATE('{$dateUpdate}', 'DD-MM-RRRR')) AS JML
            FROM DUAL
        ";
        $jml = $this->_db->fetchRow($sql);
        $result = (intval($jml) > 30);

        return $result;
    }
	
	
	
	//ambil data user Role
    public function getUserRole($userId)
    {
		/*$stmt = $this->_db->query(" SELECT * 
            FROM  ".$this->_name ."
			-- WHERE USER_NAME LIKE '%$userId%' 
			WHERE USER_NAME = '$userId' 
			AND UPPER(ACTIVE) LIKE 'Y'");*/
        $stmt = $this->_db->query("
            SELECT * FROM ".$this->_name."
            WHERE USER_NAME = '$userId' 
            AND (UPPER(ACTIVE) LIKE 'Y' OR UPPER(HO_STATUS_ACTIVE) = 'Y')
        ");
        $result = $stmt->fetchObject();
      

        return $result;
    }
	
	//hitung sisa waktu user expired
    public function countdownAuthExpired()
    {
        $auth = Zend_Registry::get('auth');
        $dateUpdate = $auth->getIdentity()->UPDATE_TIME;

        $sql = "
            SELECT (TO_DATE(SYSDATE, 'DD-MM-RRRR') -
                    TO_DATE('{$dateUpdate}', 'DD-MM-RRRR')) AS JML
            FROM DUAL
        ";
        $jml = $this->_db->fetchOne($sql);
        $result = (30 - intval($jml));

        return $result;
    }
	
	// insert log untuk login yang berhasil
    public function logLogin($userName = '')
    {
        $this->_global->insertLog('LOGIN SUCCESS', 'LOGIN', $userName);
		
		//update LAST_LOGIN
		$sql = "
            UPDATE TM_USER
			SET LAST_LOGIN = SYSDATE
			WHERE USER_NAME = '{$userName}'
        ";
        $this->_db->query($sql);
		$this->_db->commit();
    }
	
	//insert log untuk login yang gagal
	public function logLoginFailed($userName = '')
    {
        $this->_global->insertLog('LOGIN FAILED', 'LOGIN', $userName);
    }
	
	//insert log untuk logout
    public function logLogout($userName = '')
    {
        $this->_global->insertLog('LOGOUT', 'LOGIN', $userName);
    }
	
	//cek autentifikasi untuk module
    public function checkAcl($url = '')
    {
        $result = false;

        $auth = Zend_Registry::get('auth');
        $siteCode     = $auth->getIdentity()->BA_CODE;
        //$accessRights = $auth->getIdentity()->USER_ROLE;

        $usertipe = $_SESSION['usertipe']['usertipe'];
        if ($usertipe == '1') {
            $accessRights = $auth->getIdentity()->USER_ROLE;
        } else if ($usertipe == '2') {
            $accessRights = $auth->getIdentity()->HO_USER_ROLE;
        } else;

        $sql = "
            SELECT M.ITEM_NAME
            FROM T_MODULE M
            WHERE (M.ITEM_NAME IS NOT NULL
                  AND LOWER(M.ITEM_NAME) <> 'menu'
                  AND LOWER(M.ITEM_NAME) <> 'submenu')
              AND CODE IN (
                    SELECT A.MODULE_CODE
                    FROM T_ACCESSRIGHT A
                    WHERE A.USER_ROLE = '{$accessRights}'
                                AND A.AUTHORIZED = 'Y')
        ";
        $rows = $this->_db->fetchAll($sql);

        $urlAllowed = array();
        foreach ($rows as $row) {
            $itemName = explode('/', $row['ITEM_NAME']);
            $urlAllowed[] = $itemName[0];
        }
        // nambah Eko Lesmana Sijabat
        /*array_push($urlAllowed, 'ho-setup-master-user', 'ho-setup-hak-akses', 'ho-prebudget-master', 'ho-prebudget-norma', 'ho-act-outlook', 'ho-summary-outlook', 'ho-rencana-kerja', 'ho-capex', 'ho-opex', 'ho-spd', 'ho-report-summary', 'ho-valid-submit');*/

        $result = in_array($url, $urlAllowed);

        return $result;
    }
	
	//menampilkan menu yang dapat diakses oleh user ybs
    public function getMenu()
    {
        $result = array();
        $usertipe = $_SESSION['usertipe']['usertipe'];

        $auth = Zend_Registry::get('auth');
        $siteCode     = $auth->getIdentity()->BA_CODE;

        if ($usertipe == '1') {
            $accessRights = $auth->getIdentity()->USER_ROLE;
        } else if ($usertipe == '2') {
            $accessRights = $auth->getIdentity()->HO_USER_ROLE;
        } else;

        $codes = "
            SELECT MODULE_CODE
            FROM T_ACCESSRIGHT
            WHERE USER_ROLE = '{$accessRights}'
              AND AUTHORIZED = 'Y'
              AND TIPE = '{$usertipe}'
        ";
        //echo $accessRights;

        $address = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
        $url = "{$address}/" . APPLICATION_URL . "/";

        $sql = "
            SELECT CODE, UPPER(NAME) NAME 
            FROM T_MODULE 
            WHERE CODE IN ({$codes})
                AND ITEM_NAME = 'menu'
                AND STATUS = 'M'
                AND TIPE IN ('".$usertipe."', 'ALL')
                AND DELETE_USER IS NULL
            ORDER BY CODE
        ";
//echo $sql;
        $mainMenu = $this->_db->fetchAll($sql);
//        print_r ($mainMenu);
        $result['HOME'] = $url . 'index/main';
        foreach ($mainMenu as $key => $menu) {
            $result[$menu['NAME']] = array();
            // sub_menu
            $sql = "
                SELECT CODE, UPPER(NAME) NAME, ITEM_NAME
                FROM T_MODULE
                WHERE CODE IN ({$codes})
                  AND PARENT_MODULE = {$menu['CODE']}
                  AND STATUS = 'M'
                  AND TIPE IN ('".$usertipe."', 'ALL')
                  AND DELETE_USER IS NULL
                ORDER BY CODE
            ";
            $subMenu = $this->_db->fetchAll($sql);
            foreach ($subMenu as $key2 => $submenu) {
                if ($submenu['ITEM_NAME'] == 'submenu') {
                    // sub_sub_menu
                    $sql = "
                        SELECT CODE, UPPER(NAME) NAME, ITEM_NAME
                        FROM T_MODULE
                        WHERE CODE IN ({$codes})
                          AND PARENT_MODULE = {$submenu['CODE']}
                          AND STATUS = 'M'
                          AND TIPE IN ('".$usertipe."', 'ALL')
                          AND DELETE_USER IS NULL
                        ORDER BY CODE
                    ";
                    $subSubMenu = $this->_db->fetchAll($sql);
                    foreach ($subSubMenu as $key3 => $subsubmenu) {
                        // item_menu
                        $par = ($menu['NAME'] == 'LAPORAN') ? "?BA_CODE={$siteCode}" : '';
                        $result[$menu['NAME']][$submenu['NAME']][$subsubmenu['NAME']] =
                            $url . $subsubmenu['ITEM_NAME'] . $par;
                    }
                } else {
                    // item_menu
                    $par = ($menu['NAME'] == 'LAPORAN') ? "?BA_CODE={$siteCode}" : '';
                    $result[$menu['NAME']][$submenu['NAME']] = $url . $submenu['ITEM_NAME'] . $par;
                }
            }
        }
        $result['LOGOUT'] = $url . 'index/logout';

        //if ($_SESSION['usertipe']['usertipe'] == '1') {
        /*    $auth = Zend_Registry::get('auth');
            //echo '<pre>'; print_r ($auth->getIdentity());
            $siteCode     = $auth->getIdentity()->BA_CODE;
            $accessRights = $auth->getIdentity()->USER_ROLE;
            //print_r ($accessRights);

            // get authorized module
            $codes = "
                SELECT MODULE_CODE
                FROM T_ACCESSRIGHT
                WHERE USER_ROLE = '{$accessRights}'
                  AND AUTHORIZED = 'Y'
            ";

            $address   = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
            $url         = "{$address}/" . APPLICATION_URL . "/";

            // main_menu
            $sql = "
                SELECT CODE, UPPER(NAME) NAME
                FROM T_MODULE
                WHERE CODE IN ({$codes})
                  AND ITEM_NAME = 'menu'
                  AND STATUS = 'M'
                  AND TIPE IN ('".$_SESSION['usertipe']['usertipe']."', 'ALL')
                  AND DELETE_USER IS NULL
                ORDER BY CODE
            ";
            //echo $sql;
            $mainMenu = $this->_db->fetchAll($sql);
            
            $result['HOME'] = $url . 'index/main';
            foreach ($mainMenu as $key => $menu) {
                $result[$menu['NAME']] = array();
                // sub_menu
                $sql = "
                    SELECT CODE, UPPER(NAME) NAME, ITEM_NAME
                    FROM T_MODULE
                    WHERE CODE IN ({$codes})
                      AND PARENT_MODULE = {$menu['CODE']}
                      AND STATUS = 'M'
                      AND TIPE IN ('".$_SESSION['usertipe']['usertipe']."', 'ALL')
                      AND DELETE_USER IS NULL
                    ORDER BY CODE
                ";
                $subMenu = $this->_db->fetchAll($sql);
                foreach ($subMenu as $key2 => $submenu) {
                    if ($submenu['ITEM_NAME'] == 'submenu') {
                        // sub_sub_menu
                        $sql = "
                            SELECT CODE, UPPER(NAME) NAME, ITEM_NAME
                            FROM T_MODULE
                            WHERE CODE IN ({$codes})
                              AND PARENT_MODULE = {$submenu['CODE']}
                              AND STATUS = 'M'
                              AND TIPE IN ('".$_SESSION['usertipe']['usertipe']."', 'ALL')
                              AND DELETE_USER IS NULL
                            ORDER BY CODE
                        ";
                        $subSubMenu = $this->_db->fetchAll($sql);
                        foreach ($subSubMenu as $key3 => $subsubmenu) {
                            // item_menu
                            $par = ($menu['NAME'] == 'LAPORAN') ? "?BA_CODE={$siteCode}" : '';
                            $result[$menu['NAME']][$submenu['NAME']][$subsubmenu['NAME']] =
                                $url . $subsubmenu['ITEM_NAME'] . $par;
                        }
                    } else {
                        // item_menu
                        $par = ($menu['NAME'] == 'LAPORAN') ? "?BA_CODE={$siteCode}" : '';
                        $result[$menu['NAME']][$submenu['NAME']] = $url . $submenu['ITEM_NAME'] . $par;
                    }
                }
            }
            $result['LOGOUT'] = $url . 'index/logout';*/

        return $result;
    }
	
	//menghitung kesalahan login dari last login
    public function countLoginFailed($userName = '')
    {
        $sql = "
            SELECT COUNT (INSERT_TIME) COUNT_LOGIN_FAILED
			FROM T_USER_LOG
			WHERE ACTION = 'LOGIN FAILED'
				AND INSERT_USER = '{$userName}'
				AND INSERT_TIME BETWEEN (SELECT INSERT_TIME
										 FROM (SELECT s2.INSERT_TIME, ROWNUM rm
											   FROM (SELECT DISTINCT INSERT_TIME
													 FROM T_USER_LOG
													 WHERE ACTION = 'LOGIN SUCCESS'
														AND INSERT_USER = '{$userName}'
													 ORDER BY INSERT_TIME DESC) s2
											   WHERE ROWNUM <= 2)
										 WHERE rm >= 2)
								AND  (SELECT LAST_LOGIN
									  FROM TM_USER
									  WHERE USER_NAME = '{$userName}')					   
        ";
        $result = $this->_db->fetchOne($sql);

        return $result;
    }
	
	//menghitung kesalahan login dari last login
    public function simpan($params = array() )
    { 
		try {
			$sql = "
			INSERT INTO TM_USER  (NIK, USER_NAME,PASSWORD, FULL_NAME, ACTIVE,BA_CODE, USER_ROLE, REFERENCE_ROLE, HO_DIV_CODE, HO_CC_CODE, HO_USER_ROLE, HO_STATUS_ACTIVE, TIPE_AKSES, INSERT_USER, INSERT_TIME) VALUES
			('".$params['NIK']."','".$params['USER_NAME']."','bps321','".$params['FULL_NAME']."','".$params['ACTIVE']."','".$params['BA_CODE']."','".$params['USER_ROLE']."','".$params['REFERENCE_ROLE']."', '".$params['HO_CC_CODE']."', '".$params['HO_DIV_CODE']."', '".$params['HO_USER_ROLE']."', '".$params['HO_STATUS_ACTIVE']."', '".$params['TIPE_AKSES']."', '".$params['INSERT_USER']."',".$params['INSERT_TIME'].")									  		   
			";
			
			$this->_db->query($sql);
			$this->_db->commit();
			//log DB
			$this->_global->insertLog('INSERT SUCCESS', 'USER', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('INSERT FAILED', 'USER', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		
			//return value
			$result = false;
		}
        return $result;
    }
	
	
	public function ldapAuth($username,$password){
		/**************************************************
		  Bind to an Active Directory LDAP server and look
		  something up.
		***************************************************/
		  $SearchFor=$username;       //What string do you want to find?
		  $SearchField="samaccountname";   //In what Active Directory field do you want to search for the string?
		  $ldapport = 389;
		  
		  $LDAPHost = "ldap.tap-agri.com";       //Your LDAP server DNS Name or IP Address
		  $dn = "OU=B.Triputra Agro Persada, DC=tap, DC=corp"; //Put your Base DN here
		  $LDAPUserDomain = "@tap";  //Needs the @, but not always the same as the LDAP server domain		
		  
		  $LDAPUser = strtolower($username); //A valid Active Directory login
		  $LDAPUserPassword = $password;
		  $LDAPFieldsToFind = array("cn", "givenname","company", "samaccountname", "homedirectory", "telephonenumber", "mail");
		   
		  $cnx = ldap_connect($LDAPHost, $ldapport) or  $info = "Koneksi LDAP Gagal";
		  if (  $cnx){
		  @ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);  //Set the LDAP Protocol used by your AD service
		  @ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);         //This was necessary for my AD to do anything
		  @ldap_bind($cnx,$LDAPUser.$LDAPUserDomain,$LDAPUserPassword) or $info ="Username / Password Salah";
		  //error_reporting (E_ALL ^ E_NOTICE);   //Suppress some unnecessary messages
		  $filter="($SearchField=$SearchFor*)"; //Wildcard is * Remove it if you want an exact match
		  $sr=@ldap_search($cnx, $dn, $filter, $LDAPFieldsToFind);
		  $info = @ldap_get_entries($cnx, $sr);
		 }
		  //if ($x==0) { print "Oops, $SearchField $SearchFor was not found. Please try again.\n"; }
		  
		  return $info;
	}
}
