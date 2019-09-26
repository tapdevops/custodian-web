<?php
class Zend_View_Helper_ControllerName {
	function controllerName()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();

		$result = strtolower($request->getControllerName());

		return $result;
   }
}
