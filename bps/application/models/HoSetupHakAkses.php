<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Master Hak Akses User
Function 			:	- initList			: ambil data hak akses user dari DB
						- getList			: menampilkan list hak akses user
						- save				: simpan data
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/04/2013
Update Terakhir		:	10/04/2013
Revisi				:	
=========================================================================================================================
*/
class Application_Model_HoSetupHakAkses
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
    }

    public function getMain()
    {
        $result = array();

        $table = new Application_Model_DbOptions();
        $options = array();
        $options['accessright'] = $table->getHoUsersAccessrights();

        // elements
        $result['accessright'] = array(
            'type'    => 'select',
            'name'    => 'accessright',
            'value'   => '',
            'options' => $options['accessright'],
            'ext'     => ''
        );
        $result['module'] = array(
            'type'    => 'text',
            'name'    => 'module',
            'value'   => '',
            'ext'     => 'style="width:200px;"'
        );

        return $result;
    }

    private function initList($params = array())
    {
        $result = array();

        // where2
        $result['where2'] = '';
        if (isset($params['sSearch']) && $params['sSearch'] != '') {
            $search = explode('~', $params['sSearch']);
            if ($search[0] != '') {
                $val = $this->_db->quote(strtoupper($search[0]));
                $result['where2'] .= " AND UPPER(T1.USER_ROLE) = {$val}";
            }
            if ($search[1] != '') {
                $val = $this->_db->quote('%' . strtoupper($search[1]) . '%');
                $result['where2'] .= " AND UPPER(T2.NAME) LIKE {$val}";
            }
        }
        // orderBy
        $sortCol = array(
            'UPPER(T1.USER_ROLE)',
            'UPPER(T2.NAME)',
            'T1.AUTHORIZED'
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
            SELECT ROWIDTOCHAR(T1.ROWID),
                   UPPER(T1.USER_ROLE),
                   UPPER(T2.NAME),
                   T1.AUTHORIZED
            FROM T_ACCESSRIGHT T1, T_MODULE T2
            WHERE T2.CODE = T1.MODULE_CODE
              AND T1.TIPE = '2'
              AND T2.TIPE = '2'
              AND T2.DELETE_USER IS NULL
        ";

        return $result;
    }

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

        $checkbox = '<input type="checkbox" name="authorized[]" id="authorized-{id}" value="Y" title="Yes/No" ' .
                    ' {checked} class="checkbox" />';
        foreach ($rows as $idx => $row) {
            $data = array();
            foreach ($row as $key => $val) {
                switch ($key) {
                    case 0:
                    case 1:
                        continue;
                        break;
                    case 4:
                        $checked = ($val == 'Y') ? 'checked="checked"' : '';
                        $data[] = str_replace('{checked}', $checked, str_replace('{id}', $rows[$idx][1], $checkbox));
                        break;
                    default:
                        $data[] = $val;
                }
            }
            $result['aaData'][] = $data;
        }

        return $result;
    }

    public function save($params = array())
    {
        $result = '';
		
		try {
			$sql = "
				UPDATE T_ACCESSRIGHT
				SET AUTHORIZED = '{$params['authorized']}'
				WHERE ROWIDTOCHAR(ROWID) = '{$params['rowid']}'
			";
			$this->_db->query($sql);
			
			//log DB
			$this->_global->insertLog('UPDATE SUCCESS', 'HAK AKSES PENGGUNA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('UPDATE FAILED', 'HAK AKSES PENGGUNA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
				
			//return value
			$result = false;
		}
		
        $result = 'done';

        return $result;
    }
}

