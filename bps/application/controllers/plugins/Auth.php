<?php
class Application_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    { 
        // no need to check auth
        // -- not found page
        if (Zend_Controller_Front::getInstance()->getDispatcher()->isDispatchable($request) === false) {
            return true;
        }
        // -- error page
        if ($request->getControllerName() == 'error') {
            return true;
        }
        // -- sync data
        if ($request->getControllerName() == 'sync-upload-data') {
            return true;
        }
        if ($request->getControllerName() == 'sync-download-data') {
            return true;
        }
        // -- login/logout page
        if (($request->getControllerName()) == 'index' && !($request->getActionName() == 'main' || $request->getActionName() == 'outdated-browser')) {
            return true;
        }
		
		// check if user have been login?
		$auth = Zend_Registry::get('auth');
		if ($auth->hasIdentity()) {
			// assign userInfo to view
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
			$viewRenderer->initView();
			$view = $viewRenderer->view;
			$usertipe = $_SESSION['user_tipe'];

			if ($usertipe == '1') {
				$grupname = $auth->getIdentity()->USER_ROLE;
			} else if ($usertipe == '2') {
				$grupname = $auth->getIdentity()->HO_USER_ROLE;
			}
			$view->userInfo = array(
				'username' 			=> $auth->getIdentity()->USER_NAME,
				'grupname' 			=> $grupname
			);
		} else {				
			// ajax request?
			if ($request->isXmlHttpRequest()) {
				die('session is expired');
			}
			
			// redirect to login page
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
			$redirector->gotoUrl('/index/login');
			
        }
    }
	
	
}
