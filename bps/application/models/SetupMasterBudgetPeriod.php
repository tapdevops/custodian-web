<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Periode Budget
Function 			:	- initUsers			: ambil data periode budget dari DB
						- getList			: menampilkan list periode budget
						- getInput			: mempersiapkan form inputan untuk master periode budget
						- getRow			: menampilkan data yang akan diubah
						- saveRecord		: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_SetupMasterBudgetPeriod
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
    }
	
	//ambil data periode budget dari DB
    private function initList($params = array())
    {
        $result = array();

        // where2
        $result['where2'] = '';
        if (isset($params['sSearch']) && $params['sSearch'] != '') {
            $val = $this->_db->quote('%' . strtoupper($params['sSearch']) . '%');
            $result['where2'] .= " AND to_char(PERIOD_BUDGET,'RRRR') LIKE {$val}";
        }
        // orderBy
        $sortCol = array(
            'MY_ROWNUM',
            'PERIOD_BUDGET',
            'START_BUDGETING',
            'END_BUDGETING',
			'UPPER(STATUS)'
        );
        $result['orderBy'] = '';
        if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
            $orderBy = '';
            for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
            }
            $result['orderBy'] = 'ORDER BY ' . substr_replace($orderBy, '', -2);
        }
        // sql
        $result['query'] = "
            SELECT ROWIDTOCHAR(ROWID), to_char(PERIOD_BUDGET,'YYYY-MM-DD') PERIOD_BUDGET, to_char(PERIOD_BUDGET,'RRRR') YEAR_PERIOD_BUDGET, to_char(START_BUDGETING,'YYYY-MM-DD') START_BUDGETING, to_char(END_BUDGETING,'YYYY-MM-DD') END_BUDGETING, UPPER(STATUS)
            FROM TM_PERIOD
			WHERE DELETE_USER IS NULL
        ";

        return $result;
    }

	//menampilkan list periode budget
    public function getList($params = array())
    {
        $result = array();

        $result['sEcho'] = intval($params['sEcho']);
        $result['iTotalRecords'] = 0;
        $result['iTotalDisplayRecords'] = 0;
        $result['aaData'] = array();

        $min = strval($params['iDisplayStart']);
        $max = strval(intval($params['iDisplayStart']) + intval($params['iDisplayLength']));
        $begin = "
            SELECT * FROM (SELECT ROWNUM MY_ROWNUM, MY_TABLE.*
            FROM (SELECT TEMP.*
            FROM (
        ";
        $end = "
            ) TEMP
            ) MY_TABLE
              WHERE ROWNUM <= {$max}
            ) WHERE MY_ROWNUM > {$min}
        ";

        $initAction = 'init' . str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));
		$init = $this->$initAction($params);

        // -- rows count (all)
        $sql = "SELECT COUNT(*) FROM ({$init['query']})";
        $result['iTotalRecords'] = $this->_db->fetchOne($sql);
        // -- rows count (filter)
        $sql = "SELECT COUNT(*) FROM ({$init['query']} {$init['where2']})";
        $result['iTotalDisplayRecords'] = $this->_db->fetchOne($sql);
        // -- rows
        $sql = "{$begin} {$init['query']} {$init['where2']} {$init['orderBy']} {$end}";
        //echo $sql;
        $this->_db->setFetchMode(Zend_Db::FETCH_NUM);
        $rows = $this->_db->fetchAll($sql);

        $edit   = '<input type="button" name="edit[]" id="edit-{id}" value="" title="Edit" class="button_edit" />';
        foreach ($rows as $idx => $row) {
            $data = array();
            foreach ($row as $key => $val) {
                if ($key == 0 || $key == 2) {
                    continue;
                } else if ($key == 1) {
                    $data[] = str_replace('{id}', $val, $edit);
                } else {
                    $data[] = $val;
                }
            }
            $result['aaData'][] = $data;
        }

        return $result;
    }
	
	//mempersiapkan form inputan untuk master periode budget
    //setting input untuk region dan maturity stage
	public function getInput()
    {
        $result = array();
		
		$table = new Application_Model_DbOptions();
        $options = array();
        $options['status'] = $table->getPeriodBudgetStatus();

        // elements
        $result['PERIOD_BUDGET'] = array(
            'type'  => 'text',
            'name'  => 'PERIOD_BUDGET',
            'value' => '',
            'ext'   => 'maxlength="50"'
        );
        $result['START_BUDGETING'] = array(
            'type'  => 'text',
            'name'  => 'START_BUDGETING',
            'value' => '',
            'ext'   => 'maxlength="50"'
        );
        $result['END_BUDGETING'] = array(
            'type'    => 'text',
            'name'    => 'END_BUDGETING',
            'value'   => '',
            'ext'     => 'maxlength="50"'
        );
		$result['STATUS'] = array(
            'type'    => 'select',
            'name'    => 'STATUS',
            'value'   => '',
            'options' => $options['status'],
            'ext'     => ''
        );

        return $result;
    }

	//menampilkan data yang akan diubah
    public function getRow($params = array())
    {
        $result = array();

        $sql = "
            SELECT to_char(PERIOD_BUDGET,'YYYY-MM-DD') PERIOD_BUDGET, to_char(START_BUDGETING,'YYYY-MM-DD') START_BUDGETING, to_char(END_BUDGETING,'YYYY-MM-DD') END_BUDGETING, UPPER(STATUS)
            FROM TM_PERIOD
            WHERE ROWIDTOCHAR(ROWID) = '{$params['rowid']}'
        ";
		$result = $this->_db->fetchRow($sql);
        return $result;
    }
	
	//simpan data
    public function saveRecord($params = array())
    {
        $result = '';

        $table = new Application_Model_DbTable_BudgetPeriod();

        if ($params['rowid'] == '') {
            // -- add
            $sql = "
                SELECT COUNT(*)
                FROM TM_PERIOD
                WHERE PERIOD_BUDGET = TO_DATE('" .$params['PERIOD_BUDGET'] . "', 'YYYY-MM-DD')
            ";
            $count = $this->_db->fetchOne($sql);
            if ($count == 0) {
				$data = array(
					'PERIOD_BUDGET' 	=> new Zend_Db_Expr("TO_DATE('" .$params['PERIOD_BUDGET'] . "', 'YYYY-MM-DD')"),
					'START_BUDGETING'   => new Zend_Db_Expr("TO_DATE('" .$params['START_BUDGETING'] . "', 'YYYY-MM-DD')"),
					'END_BUDGETING'   	=> new Zend_Db_Expr("TO_DATE('" .$params['END_BUDGETING'] . "', 'YYYY-MM-DD')"),
					'STATUS'     		=> $params['STATUS'],
					'INSERT_USER'   	=> $this->_userName,
					'INSERT_TIME'   	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')"),
					'UPDATE_USER'   	=> $this->_userName,
					'UPDATE_TIME'   	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
				);
				try {
					//$this->_global->printDebug($data);
					$res = $table->insert($data);
					
					//log DB
					$this->_global->insertLog('INSERT SUCCESS', 'PERIODE BUDGET', '', '');
				} catch (Exception $e) {
					//log DB
					$this->_global->insertLog('INSERT FAILED', 'PERIODE BUDGET', '', $e->getCode());
					
					//error log file
					$this->_global->errorLogFile($e->getMessage());
				
					//return value
					$result = false;
				}
                $result = 'done';
            } else {
                $result = "Data [{$params['PERIOD_BUDGET']}] telah ada sebelumnya.";
            }
        } else {
			$data = array(
				'START_BUDGETING'   => new Zend_Db_Expr("TO_DATE('" .$params['START_BUDGETING'] . "', 'YYYY-MM-DD')"),
				'END_BUDGETING'   	=> new Zend_Db_Expr("TO_DATE('" .$params['END_BUDGETING'] . "', 'YYYY-MM-DD')"),
				'STATUS'     		=> $params['STATUS'],
				'UPDATE_USER'   	=> $this->_userName,
				'UPDATE_TIME'   	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
			);
				
            try {
				//$this->_global->printDebug($data);
				$res = $table->update($data, "ROWIDTOCHAR(ROWID) = '{$params['rowid']}'");
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'PERIODE BUDGET', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'PERIODE BUDGET', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
            $result = 'done';
        }

        return $result;
    }
}
