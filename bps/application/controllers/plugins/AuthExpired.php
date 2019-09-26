<?php
class Application_Controller_Plugin_AuthExpired extends Zend_Controller_Plugin_Abstract
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
        // -- login/logout page
        if (($request->getControllerName()) == 'index' && !($request->getActionName() == 'main' || $request->getActionName() == 'outdated-browser')) {
            return true;
        }

        // check if user password has expired?
        $table = new Application_Model_DbTable_User();
        if ($table->isAuthExpired()) {
            // send flash msg
            $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
            $flashMessenger->addMessage('PASSWORD ANDA HARUS DIUBAH DULU!');
            if ($request->getControllerName() == 'setup-master-ubah-password') {
                //...
            } else {
                // force to change password
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrl('/setup-master-ubah-password/main');
            }
        }
    }
}
