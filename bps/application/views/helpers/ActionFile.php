<?php
class Zend_View_Helper_ActionFile {
    function actionFile()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $result = strtolower($request->getControllerName()) . '/' .
                  strtolower($request->getActionName()) . '.phtml';

        return $result;
   }
}
