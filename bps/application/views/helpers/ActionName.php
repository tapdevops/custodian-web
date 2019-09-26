<?php
class Zend_View_Helper_ActionName {
	function actionName()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();

		$result = strtolower($request->getActionName());

		return $result;
   }
}
?>