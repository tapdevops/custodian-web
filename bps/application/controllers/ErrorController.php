<?php
class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('error');
    }

    public function indexAction()
    {
        //...
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $code = 404;
                $message = 'Page not found';
                break;
            default:
                $code = 500;
                $message = 'Application error';
                break;
        }

        if ($this->_request->isXmlHttpRequest()) {
            $msg = "Error!\n" .
                   "$message\n" .
                   "Exception message:\n" .
                   $errors->exception->getMessage();
            die($msg);
        }

        $this->getResponse()->setHttpResponseCode($code);
        $this->view->message = $message;
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
            // -- krisna
            // get sql that make error
            $sqlError = '';
            $traces = $errors->exception->getTrace();
            foreach ($traces as $trace) {
                if ($trace['class'] == 'Zend_Db_Adapter_Pdo_Abstract') {
                    $sqlError = str_replace('"', '', $trace['args'][0]);
                }
            }
            $this->view->sqlError = $sqlError;
        }

        $this->view->request = $errors->request;
    }

    public function deniedAction()
    {
        //...
    }

    public function noIndexAction()
    {
        //...
    }
}
