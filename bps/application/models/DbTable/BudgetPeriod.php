<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Class untuk Budget Period
Function 			:	
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	18/06/2012
Update Terakhir		:	18/06/2012
Revisi				:	
=========================================================================================================================
*/
class Application_Model_DbTable_BudgetPeriod extends Zend_Db_Table_Abstract
{
    protected $_name = 'TM_PERIOD';
    protected $_primary = array('PERIOD_BUDGET');
}