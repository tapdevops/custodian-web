<?php
class Application_Model_DbTable_ParameterValueMaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'T_PARAMETER_VALUE';
    protected $_primary = array('BA_CODE', 'PARAMETER_CODE', 'PARAMETER_VALUE_CODE');
}
