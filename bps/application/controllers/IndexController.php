<?php
class IndexController extends Zend_Controller_Action
{
    private $_auth = null;
    private $_global = null;
	
	

    public function init()
    {
        $this->_auth = Zend_Registry::get('auth');
        $this->_global = new Application_Model_Global();
		$this->_menu = new Application_Model_ActivityMenu();
    }

    public function indexAction()
    {
        $this->_redirect('/index/login');
    }
	

    public function loginAction()
    {
		// use login layout
        $this->_helper->layout->setLayout('login');
		
		$loginSuccess = false;
        $request = $this->getRequest();

		if ($request->isPost()) {
			$userName = strtoupper(str_replace("'","",$request->getPost('username')));
			$userPass = $request->getPost('userpass');
			$userTipe = $request->getPost('usertipe');
			
			// -- is blank?
			if ($userName == '' || $userPass == '') {
				$msg = 'Nama Pengguna dan/atau Kata Sandi Tidak Boleh Kosong.';
			} else {
				// if user already login then clear user's identity
				if ($this->_auth->hasIdentity()) {
					$userName = $this->_auth->getIdentity()->USER_NAME;
					$this->_auth->clearIdentity();
					$table = new Application_Model_DbTable_User();
					$table->logLogout($userName);
				}
				   
				$table = new Application_Model_DbTable_User();
				$loginResult = $table->checkAuth($userName, $userPass, $userTipe);

				if ($loginResult['status'] == '1') {
					$loginSuccess = true;
				} elseif ($loginResult['error'] == 'NO_ACCESS' ) {
					$msg = 'Anda tidak mempunyai akses.';
				} elseif ($loginResult['error'] == 'INVALID_PASSWORD') {
					$msg = 'Nama Pengguna atau Kata Sandi Salah.';
				}

				if ($loginSuccess) {
					$sess = new Zend_Session_Namespace('period');
					$sess->period =  $this->_global->getPeriodBudget();
					
					$mysession = new Zend_Session_Namespace('usertipe');
					$mysession->usertipe = $request->getPost('usertipe');
					
					if ($sess->period) {						
						$this->_redirect('/index/main');
					} else {
						$msg = 'Periode budget belum dimulai.';
					}
				}
				// -- not valid
				else {
					$table->logLoginFailed($userName);
				}
			}
		}        
        $this->view->msg = $msg;
    }

    public function logoutAction()
    {
        // clear user's identity then back to login page
        if ($this->_auth->hasIdentity()) {
            $userName = $this->_auth->getIdentity()->USER_NAME;
            $this->_auth->clearIdentity();
            $table = new Application_Model_DbTable_User();
            $table->logLogout($userName);
        }
		Zend_Session::destroy();
        $this->_redirect('/index/login');
    }

    public function mainAction()
    { 
//print_r ($_SESSION);
		$userName = $this->_auth->getIdentity()->USER_NAME;
		$referenceRole = $this->_auth->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 26/08/2013
		
        $table = new Application_Model_DbTable_User();
        $this->view->referenceRole = $referenceRole;  // TAMBAHAN : Sabrina - 26/08/2013
        $this->view->countDown = strval($table->countdownAuthExpired());
		$this->view->countLoginFailed = strval($table->countLoginFailed($userName));
    }
	
	public function listAktivitasAction()
    {
		
        $this->_helper->viewRenderer->setNoRender(true);
		$params = $this->_request->getParams();
		
		$data = $this->_menu->getList($params);
        die(json_encode($data));
    }
	
	public function outdatedBrowserAction()
    {
        // use login layout
        $this->_helper->layout->setLayout('login');
    }
}
