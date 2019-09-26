<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master User
Function 			:	- initUsers			: ambil data user dari DB
						- getList			: menampilkan list user
						- getInput			: mempersiapkan form inputan untuk master user
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
class Application_Model_SetupMasterUsers
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
	
	//ambil data user dari DB
    private function initUsers($params = array())
    {
        $result = array();

        // where2
        $result['where2'] = '';
        if (isset($params['sSearch']) && $params['sSearch'] != '') {
            $val = $this->_db->quote('%' . strtoupper($params['sSearch']) . '%');
            $result['where2'] .= " AND UPPER(USER_NAME) LIKE {$val}";
        }
        // orderBy
        $sortCol = array(
            'MY_ROWNUM',
            'UPPER(USER_NAME)',
            'UPPER(FULL_NAME)',
			'UPPER(BA_CODE)',
            'UPPER(USER_ROLE)',
            'UPPER(REF_ROLE)',
			'UPPER(ACTIVE)',
            'UPPER(HO_DIV_CODE)',
            'UPPER(HO_CC_CODE)',
            'UPPER(HO_USER_ROLE)',
            'UPPER(HO_STATUS_ACTIVE)',
            'UPPER(TIPE_AKSES)'
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
        /*$result['query'] = "
            SELECT ROWIDTOCHAR(ROWID), UPPER(USER_NAME), UPPER(FULL_NAME), UPPER(REFERENCE_ROLE), UPPER(BA_CODE), UPPER(USER_ROLE), UPPER(ACTIVE), UPPER(HO_DIV_CODE), UPPER(HO_USER_ROLE), UPPER(HO_STATUS_ACTIVE), UPPER(TIPE_AKSES)
            FROM TM_USER WHERE 1=1
        ";*/
        $result['query'] = "
            SELECT 
                ROWIDTOCHAR(TU.ROWID), UPPER(TU.USER_NAME), UPPER(TU.FULL_NAME), 
                UPPER(TU.REFERENCE_ROLE), UPPER(TU.BA_CODE), UPPER(TU.USER_ROLE), UPPER(TU.ACTIVE), 
                UPPER(TU.HO_USER_ROLE), UPPER(TU.HO_DIV_CODE), UPPER(TU.HO_CC_CODE), UPPER(TU.HO_STATUS_ACTIVE), UPPER(TPV.PARAMETER_VALUE)
            FROM TM_USER TU 
            LEFT JOIN T_PARAMETER_VALUE TPV ON TPV.PARAMETER_VALUE_CODE = TU.TIPE_AKSES
            WHERE 1=1 AND TPV.PARAMETER_CODE = 'TIPE_AKSES' 
        ";

        return $result;
    }

	//menampilkan list user
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
        foreach ($rows as $idx => $row) {
            $data = array();
            foreach ($row as $key => $val) {
                if ($key == 0) {
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

	//mempersiapkan form inputan untuk master user
    //setting input untuk region dan maturity stage
	public function getInput()
    {
        $result = array();

        $table = new Application_Model_DbOptions();
        $options = array();
        $options['REFERENCE_ROLE'] = $table->getRefRole();
        $options['REFERENCE_ROLE'] = array('' => 'Pilih') + $options['REFERENCE_ROLE'];
		$options['user_role'] = $table->getUserRole();
        $options['user_role'] = array('' => 'Pilih') + $options['user_role'];
		$options['active_status'] = $table->getActiveStatus();
        $options['active_status'] = array('' => 'Pilih') + $options['active_status'];
        $options['ho_div_code'] = $table->getDivCode();
        $options['ho_div_code'] = array('' => 'Pilih', 'ALL' => 'ALL') + $options['ho_div_code'];
        $options['ho_user_role'] = $table->getHoUserRole();
        $options['ho_user_role'] = array('' => 'Pilih') + $options['ho_user_role'];
        $options['ho_active_status'] = $table->getHoActiveStatus();
        $options['ho_active_status'] = array('' => 'Pilih') + $options['ho_active_status'];
        $options['tipe_akses'] = $table->getTipeAkses();
        $options['tipe_akses'] = array('' => 'Pilih') + $options['tipe_akses'];

        // elements
        $result['USER_NAME'] = array(
            'type'  => 'text',
            'name'  => 'USER_NAME',
            'value' => '',
            'ext'   => 'maxlength="50"'
        );
        $result['FULL_NAME'] = array(
            'type'  => 'text',
            'name'  => 'FULL_NAME',
            'value' => '',
            'ext'   => 'maxlength="50"'
        );
		$result['NIK'] = array(
            'type'  => 'text',
            'name'  => 'NIK',
            'value' => '',
            'ext'   => 'maxlength="50"'
        );
        $result['TIPE_AKSES'] = array(
            'type'  => 'select',
            'name'  => 'TIPE_AKSES',
            'value' => '',
            'options' => $options['tipe_akses'],
            'ext'   => ''
        );
        $result['REFERENCE_ROLE'] = array(
            'type'    => 'select',
            'name'    => 'REFERENCE_ROLE',
            'value'   => '',
            'options' => $options['REFERENCE_ROLE'],
            'ext'     => ''
        );	
		$result['BA_CODE'] = array(
            'type'    => 'text',
            'name'    => 'BA_CODE',
            'value'   => '',
            'ext'     => 'maxlength="100"',
            'style'   => 'width="500"'
        );
		$result['USER_ROLE'] = array(
            'type'    => 'select',
            'name'    => 'USER_ROLE',
            'value'   => '',
            'options' => $options['user_role'],
            'ext'     => ''
        );
		$result['ACTIVE'] = array(
            'type'    => 'select',
            'name'    => 'ACTIVE',
            'value'   => '',
            'options' => $options['active_status'],
            'ext'     => ''
        );
        /*$result['HO_DIV_CODE'] = array(
            'type'      => 'select',
            'name'      => 'HO_DIV_CODE',
            'value'     => '',
            'options'   => $options['ho_div_code'],
            'ext'       => ''
        );*/
        $result['HO_DIV_CODE'] = array(
            'type'      => 'text',
            'name'      => 'HO_DIV_CODE',
            'value'     => '',
            'ext'       => 'maxlength="100"',
            'style'     => 'width="500"'
        );
        $result['HO_CC_CODE'] = array(
            'type'      => 'text',
            'name'      => 'HO_CC_CODE',
            'value'     => '',
            'ext'       => 'maxlength="100"',
            'style'     => 'width="500"'
        );
        $result['HO_USER_ROLE'] = array(
            'type'      => 'select',
            'name'      => 'HO_USER_ROLE',
            'value'     => '',
            'options'   => $options['ho_user_role'],
            'ext'       => ''
        );
        $result['HO_STATUS_ACTIVE'] = array(
            'type'      => 'select',
            'name'      => 'HO_STATUS_ACTIVE',
            'value'     => '',
            'options'   => $options['active_status'],
            'ext'       => ''
        );

        return $result;
    }

	//menampilkan data yang akan diubah
    public function getRow($params = array())
    {
        $result = array();

        $sql = "
            SELECT *
            FROM TM_USER
            WHERE ROWIDTOCHAR(ROWID) = '{$params['rowid']}'
        ";

		$result = $this->_db->fetchRow($sql);
        return $result;
    }

    // menampilkan data cost center
    public function getCC($params = array()) {
        $result = array();

        $sql = "
            SELECT * FROM TM_HO_COST_CENTER WHERE HCC_DIVISI = '{$params['div']}' AND DELETE_USER IS NULL
        ";

        $result = $this->_db->fetchAll($sql);
        return $result;
    }
	
	//simpan data
    public function saveRecord($params = array())
    {
        $result = '';

        $table = new Application_Model_DbTable_User();

        if ($params['rowid'] == '') {
            // -- add
            $sql = "
                SELECT COUNT(*)
                FROM TM_USER
                WHERE BA_CODE = '{$params['BA_CODE']}'
                AND USER_NAME = '{$params['USER_NAME']}'
            ";
            $count = $this->_db->fetchOne($sql);

            if ($count == 0) {
                if ($params['TIPE_AKSES'] == 'ALL') {
                    $data = array(
                        'NIK'   => $params['NIK'],
                        'USER_ROLE'     => $params['USER_ROLE'],
                        'USER_NAME'     => $params['USER_NAME'],
                        'FULL_NAME'     => $params['FULL_NAME'],
                        'TIPE_AKSES'          => $params['TIPE_AKSES'],
                        'REFERENCE_ROLE'    => $params['REFERENCE_ROLE'],
                        'BA_CODE'       => $params['BA_CODE'],
                        'ACTIVE'        => $params['ACTIVE'],
                        'HO_DIV_CODE'   => $params['HO_DIV_CODE'],
                        'HO_CC_CODE'    => $params['HO_CC_CODE'],
                        'HO_USER_ROLE'  => $params['HO_USER_ROLE'],
                        'HO_STATUS_ACTIVE' => $params['HO_STATUS_ACTIVE'],
                        'INSERT_USER'   => $this->_userName,
                        'INSERT_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                    );
                } else if ($params['TIPE_AKSES'] == '1') {
                    $data = array(
                        'NIK'   => $params['NIK'],
                        'USER_ROLE'     => $params['USER_ROLE'],
                        'USER_NAME'     => $params['USER_NAME'],
                        'FULL_NAME'     => $params['FULL_NAME'],
                        'TIPE_AKSES'          => $params['TIPE_AKSES'],
                        'REFERENCE_ROLE'    => $params['REFERENCE_ROLE'],
                        'BA_CODE'       => $params['BA_CODE'],
                        'ACTIVE'        => $params['ACTIVE'],
                        'HO_DIV_CODE'   => '',
                        'HO_CC_CODE'    => '',
                        'HO_USER_ROLE'  => '',
                        'HO_STATUS_ACTIVE' => '',
                        'INSERT_USER'   => $this->_userName,
                        'INSERT_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                    );
                } else if ($params['TIPE_AKSES'] == '2') {
                    $data = array(
                        'NIK'   => $params['NIK'],
                        'USER_ROLE'     => $params['USER_ROLE'],
                        'USER_NAME'     => $params['USER_NAME'],
                        'FULL_NAME'     => $params['FULL_NAME'],
                        'TIPE_AKSES'          => $params['TIPE_AKSES'],
                        'REFERENCE_ROLE'    => '',
                        'BA_CODE'       => '',
                        'ACTIVE'        => '',
                        'HO_DIV_CODE'   => $params['HO_DIV_CODE'],
                        'HO_CC_CODE'    => $params['HO_CC_CODe'],
                        'HO_USER_ROLE'  => $params['HO_USER_ROLE'],
                        'HO_STATUS_ACTIVE' => $params['HO_STATUS_ACTIVE'],
                        'INSERT_USER'   => $this->_userName,
                        'INSERT_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                    );
                }
				try {
					//$this->_global->printDebug($data);
					$res = $table->simpan($data);
					
					//log DB
					$this->_global->insertLog('INSERT SUCCESS', 'USER', '', '');
				} catch (Exception $e) {
					//log DB
					$this->_global->insertLog('INSERT FAILED', 'USER', '', $e->getCode());
					
					//error log file
					$this->_global->errorLogFile($e->getMessage());
				
					//return value
					$result = false;
				}
                $result = 'done';
            } else {
                $result = "Data [{$params['USER_NAME']}] telah ada sebelumnya.";
            }
        } else {
			/*$data = array(
				'USER_ROLE' 	=> $params['USER_ROLE'],
				'BA_CODE'    	=> $params['BA_CODE'],
				'ACTIVE'     	=> $params['ACTIVE'],
				'UPDATE_USER'   => $this->_userName,
				'UPDATE_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
			);*/
            if ($params['TIPE_AKSES'] == 'ALL') {
                $data = array(
                        'NIK'   => $params['NIK'],
                        'USER_ROLE'     => $params['USER_ROLE'],
                        'USER_NAME'     => $params['USER_NAME'],
                        'FULL_NAME'     => $params['FULL_NAME'],
                        'REFERENCE_ROLE'    => $params['REFERENCE_ROLE'],
                        'BA_CODE'       => $params['BA_CODE'],
                        'ACTIVE'        => $params['ACTIVE'],
                        'HO_DIV_CODE'   => $params['HO_DIV_CODE'],
                        'HO_CC_CODE'    => $params['HO_CC_CODE'],
                        'HO_USER_ROLE'  => $params['HO_USER_ROLE'],
                        'HO_STATUS_ACTIVE' => $params['HO_STATUS_ACTIVE'],
                        'TIPE_AKSES'          => $params['TIPE_AKSES'],
                        'UPDATE_USER'   => $this->_userName,
                        'UPDATE_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                );
            } else if ($params['TIPE_AKSES'] == '1') {
                $data = array(
                    'NIK'   => $params['NIK'],
                    'USER_ROLE'     => $params['USER_ROLE'],
                    'USER_NAME'     => $params['USER_NAME'],
                    'FULL_NAME'     => $params['FULL_NAME'],
                    'TIPE_AKSES'          => $params['TIPE_AKSES'],
                    'REFERENCE_ROLE'    => $params['REFERENCE_ROLE'],
                    'BA_CODE'       => $params['BA_CODE'],
                    'ACTIVE'        => $params['ACTIVE'],
                    'HO_DIV_CODE'   => '',
                    'HO_CC_CODE'    => '',
                    'HO_USER_ROLE'  => '',
                    'HO_STATUS_ACTIVE' => '',
                    'UPDATE_USER'   => $this->_userName,
                    'UPDATE_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                );
            } else if ($params['TIPE_AKSES'] == '2') {
                $data = array(
                    'NIK'   => $params['NIK'],
                    'USER_ROLE'     => $params['USER_ROLE'],
                    'USER_NAME'     => $params['USER_NAME'],
                    'FULL_NAME'     => $params['FULL_NAME'],
                    'TIPE_AKSES'          => $params['TIPE_AKSES'],
                    'REFERENCE_ROLE'    => '',
                    'BA_CODE'       => '',
                    'ACTIVE'        => '',
                    'HO_DIV_CODE'   => $params['HO_DIV_CODE'],
                    'HO_CC_CODE'    => $params['HO_CC_CODE'],
                    'HO_USER_ROLE'  => $params['HO_USER_ROLE'],
                    'HO_STATUS_ACTIVE' => $params['HO_STATUS_ACTIVE'],
                    'UPDATE_USER'   => $this->_userName,
                    'UPDATE_TIME'   => new Zend_Db_Expr("TO_DATE('" . date('d-m-Y H:i:s') . "', 'DD-MM-YYYY HH24:MI:SS')")
                );
            }
				
            try {
				//$this->_global->printDebug($data);
				$res = $table->update($data, "ROWIDTOCHAR(ROWID) = '{$params['rowid']}'");
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'USER', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'USER', '', $e->getCode());
				
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

