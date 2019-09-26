<?php
class Application_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // no need to check acl
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
        // -- index page: login/logout, main
        if ($request->getControllerName() == 'index') {
            return true;
        }
        // -- pick page
        if ($request->getControllerName() == 'pick' ||
            $request->getControllerName() == 'upload' ||
			$request->getControllerName() == 'download' || 
            $request->getControllerName() == 'print') {
                return true;
        }

        // check if user can access the controller?
        $table = new Application_Model_DbTable_User;
        if (!$table->checkAcl($request->getControllerName())) {
            $request->setControllerName('error');
            $request->setActionName('denied');
        }
    }
}
