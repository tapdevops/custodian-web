<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Parameter
Function 			:	- initParameterValue: ambil data parameter dari DB
						- getList			: menampilkan list parameter
						- getInput			: mempersiapkan form inputan untuk master parameter
						- getRow			: menampilkan data yang akan diubah
						- saveRecord		: simpan data
						- deleteRecord		: hapus data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2012
Update Terakhir		:	10/04/2012
Revisi				:	
=========================================================================================================================
*/
class Application_Model_SetupMasterParameterValue
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
	
	//ambil data parameter dari DB
	private function initParameterValue($params = array())
    {
        $result = array();

        // where2
        $result['where2'] = '';
        if (isset($params['sSearch']) && $params['sSearch'] != '') {
            $val = $this->_db->quote('%' . strtoupper($params['sSearch']) . '%');
            $result['where2'] .= " AND UPPER(P.PARAMETER_NAME) LIKE {$val}";
        }
        // orderBy
        $sortCol = array(
            'MY_ROWNUM',
            'UPPER(P.PARAMETER_NAME)',
            'UPPER(PARAMETER_VALUE_CODE)',
            'UPPER(PARAMETER_VALUE)',
			'UPPER(PARAMETER_VALUE_2)',
			'UPPER(PARAMETER_VALUE_3)'
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
			SELECT ROWIDTOCHAR(pv.rowid), UPPER(p.PARAMETER_NAME), UPPER(pv.PARAMETER_VALUE_CODE), UPPER(pv.PARAMETER_VALUE),
				   UPPER(pv.PARAMETER_VALUE_2), UPPER(pv.PARAMETER_VALUE_3)
			FROM T_PARAMETER_VALUE pv, T_PARAMETER p
			WHERE pv.PARAMETER_CODE = p.PARAMETER_CODE
			AND pv.BA_CODE = p.BA_CODE
        ";

        return $result;
    }
	
	//menampilkan list parameter
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
        $this->_db->setFetchMode(Zend_Db::FETCH_NUM);
        $rows = $this->_db->fetchAll($sql);

        $edit   = '<input type="button" name="edit[]" id="edit-{id}" value="" title="Edit" class="button_edit" />';
        $delete = '<input type="button" name="delete[]" id="delete-{id}" value="" title="Delete" class="button_delete" />';
        foreach ($rows as $idx => $row) {
            $data = array();
            foreach ($row as $key => $val) {
                if ($key == 0) {
                    continue;
                } else if ($key == 1) {
                    $data[] = str_replace('{id}', $val, $edit) . '&nbsp;' . str_replace('{id}', $val, $delete);
                } else {
                    $data[] = $val;
                }
            }
            $result['aaData'][] = $data;
        }

        return $result;
    }
	
    // mempersiapkan form inputan untuk master parameter
	public function getInput($params = array())
    {
        $result = array();
		
        // elements
        $result['PARAMETER_CODE'] = array(
            'type'  => 'text',
            'name'  => 'PARAMETER_CODE',
            'value' => '',
            'ext'   => 'style="width:150px;" class="required" readonly="readonly"'
        );
        $result['PARAMETER_NAME'] = array(
            'type'  => 'text',
            'name'  => 'PARAMETER_NAME',
            'value' => '',
            'ext'   => 'style="width:150px;" class="required" readonly="readonly"'
        );
        $result['PARAMETER_VALUE_CODE'] = array(
            'type'  => 'text',
            'name'  => 'PARAMETER_VALUE_CODE',
            'value' => '',
            'ext'   => 'style="width:150px;" class="required"'
        );
        if ($params['q1'] == 'edit') {
			$result['PARAMETER_VALUE_CODE']['ext'] .= ' readonly="readonly"';
		}
        $result['PARAMETER_VALUE'] = array(
            'type'  => 'text',
            'name'  => 'PARAMETER_VALUE',
            'value' => '',
            'ext'   => 'style="width:150px;" class="required"'
        );
		$result['PARAMETER_VALUE_2'] = array(
            'type'  => 'text',
            'name'  => 'PARAMETER_VALUE_2',
            'value' => '',
            'ext'   => 'style="width:150px;"'
        );
		$result['PARAMETER_VALUE_3'] = array(
            'type'    => 'text',
            'name'    => 'PARAMETER_VALUE_3',
            'value'   => '',
            'ext'     => 'style="width:65px;" maxlength="10" class="datepicker"'
        );
        return $result;
    }
	
	//menampilkan data yang akan diubah
    public function getRow($params = array())
    {
        $result = array();

        $sql = "
			SELECT pv.PARAMETER_CODE, pv.PARAMETER_VALUE_CODE, pv.PARAMETER_VALUE, p.PARAMETER_NAME,
				   pv.PARAMETER_VALUE_2, pv.PARAMETER_VALUE_3
			FROM T_PARAMETER_VALUE pv, T_PARAMETER p
			WHERE pv.PARAMETER_CODE = p.PARAMETER_CODE
			AND pv.BA_CODE = p.BA_CODE
			AND ROWIDTOCHAR(PV.ROWID) = '{$params['rowid']}'
        ";
        $result = $this->_db->fetchRow($sql);
        
        //$this->_global->printDebug($result);
        return $result;
    }

	//simpan data
    public function saveRecord($params = array())
    {
        $result = '';

        $table = new Application_Model_DbTable_ParameterValueMaster();

        // add or edit?
        if ($params['rowid'] == '') {
			$sql = "
                SELECT COUNT(*)
                FROM T_PARAMETER_VALUE
                WHERE BA_CODE  = '{$this->_siteCode}'
                AND PARAMETER_VALUE_CODE = '{$params['PARAMETER_VALUE_CODE']}'
            ";
            $count = $this->_db->fetchOne($sql);
            // data already exist?
            if ($count > 0) {
                $result = "Data [{$params['PARAMETER_VALUE_CODE']}] telah ada sebelumnya.";
                $status = '';
            } else {
                // -- add
                $data = array(
                    'BA_CODE'      	  		=> $this->_siteCode,
                    'PARAMETER_CODE'   	  	=> $params['PARAMETER_CODE'],
                    'PARAMETER_VALUE_CODE'  => $params['PARAMETER_VALUE_CODE'],
                    'PARAMETER_VALUE'   	=> $params['PARAMETER_VALUE'],
					'PARAMETER_VALUE_2'   	=> $params['PARAMETER_VALUE_2'],
					'PARAMETER_VALUE_3'   	=> $params['PARAMETER_VALUE_3'],
					'INSERT_USER'    	  	=> $this->_userName,
					'INSERT_TIME'      	  	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')"),
					'UPDATE_USER'      	  	=> $this->_userName,
					'UPDATE_TIME'      	  	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                );
               
				try {
					//$this->_global->printDebug($data);
					$res = $table->insert($data);
					
					//log DB
					$this->_global->insertLog('INSERT SUCCESS', 'PARAMETER VALUE', '', '');
				} catch (Exception $e) {
					//log DB
					$this->_global->insertLog('INSERT FAILED', 'PARAMETER VALUE', '', $e->getCode());
					
					//error log file
					$this->_global->errorLogFile($e->getMessage());
				
					//return value
					$result = false;
				}
                $result = 'done';
				
                $sql = "
                    SELECT ROWIDTOCHAR(A.ROWID)
                    FROM T_PARAMETER_VALUE A
                    WHERE A.ROWID IN (SELECT MAX(B.ROWID) FROM T_PARAMETER_VALUE B)
                    AND A.BA_CODE  = '{$this->_siteCode}'
                ";
                $params['rowid'] = $this->_db->fetchOne($sql);
            }
        } else {
            // -- edit
            $data = array(
				'PARAMETER_VALUE' 	=> $params['PARAMETER_VALUE'],
				'PARAMETER_VALUE_2'	=> $params['PARAMETER_VALUE_2'],
				'PARAMETER_VALUE_3' => $params['PARAMETER_VALUE_3'],
				'UPDATE_USER'      	=> $this->_userName,
				'UPDATE_TIME'      	=> new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
            );
			
			try {
				//$this->_global->printDebug($data);
				$res = $table->update($data, "ROWIDTOCHAR(ROWID) = '{$params['rowid']}'");
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'PARAMETER VALUE', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'PARAMETER VALUE', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
            
            $result = ($res > 0) ? 'done' : 'fail';
        }

        return $result;
    }
	
	//hapus data
    public function deleteRecord($params = array())
    {
        $result = '';

        $table = new Application_Model_DbTable_ParameterValueMaster();
		
		try {
			//$this->_global->printDebug($data);
			$res = $table->delete("ROWIDTOCHAR(ROWID) = '{$params['rowid']}'");
			
			//log DB
			$this->_global->insertLog('DELETE SUCCESS', 'PARAMETER VALUE', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DELETE FAILED', 'PARAMETER VALUE', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}
       
        $result = ($res > 0) ? 'done' : 'fail';
		
        return $result;
    }
}

