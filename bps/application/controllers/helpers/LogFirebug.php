<?php
class Application_Controller_Helper_LogFirebug extends Zend_Controller_Action_Helper_Abstract
{
    protected $_log = null;

    public function __construct()
    {
        $log = new Zend_Log();
        $writer = new Zend_Log_Writer_Firebug();

        if (APPLICATION_ENV == 'production') {
            $writer->setEnabled(false);
        }
        $log->addWriter($writer);

        $this->_log = $log;
    }

    public function writeLog($logMsg = '')
    {
        $this->_log->log($logMsg, Zend_Log::INFO);
    }

    public function direct($msg)
    {
        return $this->writeLog($msg);
    }
}
