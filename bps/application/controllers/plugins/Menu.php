<?php
class Application_Controller_Plugin_Menu extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // no need user menu
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
        // -- pick page
        if ($request->getControllerName() == 'pick' || $request->getControllerName() == 'pick1') {
            return true;
        }

        /*if ($request->getControllerName() == 'ho-act-outlook') {
            return true;
        } */       

        // get user menu, then assign to view
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $table = new Application_Model_DbTable_User;
        $view->userMenu = $table->getMenu();
        //echo '<pre>'; print_r ($view->userMenu);
    }
}
