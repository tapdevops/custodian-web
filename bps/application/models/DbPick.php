<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.1.0
Deskripsi			: 	Model Class untuk popup LoV
Function 			:	- 29/05	: initActivityGroup						: ambil data master activity group
						- 29/05	: initActivity							: ambil data master activity
						- 29/05	: initCostElement						: ambil data master cost element
						- 29/05	: initCoa								: ambil data master COA
						- 30/05	: initBusinessArea						: ambil data master business area
						- 30/05	: initParameter							: ambil data parameter
						- 31/05	: initUom								: ambil data master UOM
						- 31/05	: initAssetStatus						: ambil data master asset status
						- 31/05	: initNormaBasic						: ambil data norma dasar
						- 31/05	: initTopography						: ambil data master topografi
						- 31/05	: initLandType							: ambil data master tipe tanah
						- 31/05	: initProgeny							: ambil data master jenis bibit
						- 31/05	: initLandSuitability					: ambil data master Land Suitability
						- 31/05	: initStatusBlokBudget					: ambil data master status blok budget
						- 03/06	: initAfdeling							: ambil data master afdeling
						- 03/06	: initBlock								: ambil data master blok
						- 04/06	: initFlagPanenNonPanen					: ambil data parameter flag panen / non panen
						- 05/06	: initRvra								: ambil data master RVRA
						- 05/06	: initVra								: ambil data master VRA
						- 05/06	: initJobType							: ambil data master Job Type
						- 05/06	: initWraGroup							: ambil data master WRA Group
						- 10/06	: initActivityClass						: ambil data master activity class
						- 11/06	: initNormaHarga						: ambil data norma harga + material
						- 11/06	: initRegion							: ambil data master region
						- 19/06	: initGroupBum							: ambil data master group BUM
						- 19/06	: initBudgetPeriod						: ambil data master periode budget
						- 24/06	: initCoaCapex							: ambil data master COA dengan type CAPEX untuk kebutuhan master aset
						- 24/06	: initMaturityStage						: ambil data master maturity stage
						- 25/06	: initGroupCsr							: ambil data master group CSR
						- 25/06	: initGroupIr							: ambil data master group IR
						- 25/06	: initGroupShe							: ambil data master group SHE
						- 04/07	: initActivityNormaAlatKerjaNonPanen	: ambil data master activity untuk norma alat kerja non panen
						- 08/07	: initAsset								: ambil data norma harga + asset
						- 08/07	: initUrgencyCapex						: ambil data master urgency capex
						- 10/07	: initCoaOpex							: ambil data master COA OPEX dari group BUM
						- 10/07	: initGroupBumCoa						: ambil data master group BUM - COA
						- 15/07	: initCoaRelation						: ambil data master COA dari mapping COA - relation
						- 15/07	: initGroupRelation						: ambil data master group relation dari mapping COA - relation
						- 15/07	: initSubGroupRelation					: ambil data master sub group relation dari mapping COA - relation
						- 17/07 : initJenisPupuk						: ambil data jenis pupuk
						- 22/07 : initActivityRkt						: ambil data master activity yang digunakan pada RKT
						- 22/07 : initSumberBiaya						: ambil data master sumber biaya
						- 26/07 : initActivityOpsi						: ambil data master opsi aktivitas
						- 26/07	: initAfdelingLc						: ambil data master afdeling LC
						- 02/08	: initJarakPerkerasanJalan				: ambil data jarak perkerasan jalan
						- 06/08	: initTipePerkerasanJalan				: ambil data tipe perkerasan jalan
						- 06/08	: initActivityClassTanam				: ambil data master activity class tanam
						- 10/09	: initRkt								: ambil data master jenis RKT
						- initList										: inisialisasi list yang akan ditampilkan
						- getList										: menampilkan list					
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2013
Update Terakhir		:	05/05/2015

Revisi	||	PIC				||	TANGGAL			||	FUNCTION			||	DESKRIPSI 		
1		||	Doni R			||	12-06-2013		||	initBusinessArea	||	Perbaikan query untuk menampilkan data berdasarkan region code
2		||	Doni R			||	24-06-2013		||	initWraGroup		||	Perbaikan query untuk menampilkan data gaji dan tunjangan saja
													initRvraGroup		||	Perbaikan query untuk menampilkan data gaji dan tunjangan saja
													initCoaCapex		||	menampilkan data coa dengan type capex
3		||	NBU 			||	05-05-2015		||	initAfdeling		||	Perbaikan query agar tidak missing right parenthesis
===========================================================================================================================================
*/
class Application_Model_DbPick
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
        $this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE;
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//setting input untuk region dan maturity stage
	public function getInput()
    {
        $result = array();

        $table = new Application_Model_DbOptions();
        $options = array();
		$options['optRegion'] = $table->getRegion();

        // elements
		$result['src_region_code'] = array(
            'type'    => 'select',
            'name'    => 'src_region_code',
            'value'   => '',
            'options' => $options['optRegion'],
            'ext'     => 'onChange=\'$("#src_ba").val("");\'', //src_afd
			'style'   => 'width:200px;background-color: #e6ffc8;'
        );

        return $result;
    }
	
	//ambil data master activity group dari DB
    public function initActivityGroup($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ACTIVITY_GROUP_CODE',
				'ACTIVITY_GROUP_DESC'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE ACTIVITY_GROUP_CODE, PARAMETER_VALUE ACTIVITY_GROUP_DESC
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'ACTIVITY_GROUP'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master activity dari DB
    public function initActivity($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ACTIVITY_CODE',
				'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
			if($params['module']=='normaDistribusiVraNonInfra'){
				$where2 .= " AND ACTIVITY_PARENT_CODE IS NULL ";
			}
            // sql
            $result['query'] = "
                SELECT ACTIVITY_CODE, DESCRIPTION
				FROM TM_ACTIVITY
				WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master cost element dari DB
    public function initCostElement($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'COST_ELEMENT'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left'
            );
            // sorts
            $result['sorts'] = array(
                'COST_ELEMENT' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'COST_ELEMENT'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master COA dari DB
    public function initCoa($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'DESKRIPSI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'COA_CODE',
				'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT COA_CODE, DESCRIPTION
				FROM TM_COA
				WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	public function initJenisPupuk($params = null)
	{
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(MATERIAL_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(MATERIAL_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'MATERIAL_CODE',
				'MATERIAL_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			$result['query'] = "
                SELECT MATERIAL_CODE, MATERIAL_NAME
				FROM TM_MATERIAL
				WHERE DELETE_USER IS NULL
					AND MATERIAL_NAME LIKE '%PUPUK%' 
					AND UPPER(BA_CODE) = '".$params['bacode']."'
				{$where2}
                {$orderBy}
            ";
        } 
        return $result;
    }
	
	//ambil data master business area dari DB
    public function initBusinessArea($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'BA',
                'ESTATE',
				'KODE_REGION',
				'DESKRIPSI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'BA' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(BA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ESTATE_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(REGION_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(REGION_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'BA_CODE',
                'ESTATE_NAME',
				'REGION_CODE',
				'REGION_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
			if($this->_siteCode <> 'ALL'){
				if ($this->_referenceRole == 'REGION_CODE')
					$where1 .= " AND '".$this->_siteCode."' LIKE '%'||UPPER(REGION_CODE)||'%' ";
				elseif ($this->_referenceRole == 'BA_CODE')
					$where1 .= " AND  '".$this->_siteCode."' LIKE '%'||UPPER(BA_CODE)||'%' ";				
			}
			if ($params['regioncode']) {
				$where1 .= " AND UPPER(REGION_CODE) = '".$params['regioncode']."' ";
			}
			
            // sql
            $result['query'] = "
                SELECT BA_CODE, ESTATE_NAME, REGION_CODE, REGION_NAME
				FROM TM_ORGANIZATION
				WHERE BA_CODE <> 'ALL'
					AND DELETE_USER IS NULL
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data parameter dari DB
    public function initParameter($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KETERANGAN',
                'KODE'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KETERANGAN' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_NAME',
                'PARAMETER_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_NAME, PARAMETER_CODE 
				FROM T_PARAMETER
                WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master UOM dari DB
    public function initUom($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'UOM',
                'DESKRIPSI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'UOM' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(UOM) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'UOM',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT UOM, UOM_DESCRIPTION DESCRIPTION 
				FROM TM_UOM
                WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master asset status dari DB
    public function initAssetStatus($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'STATUS'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left'
            );
            // sorts
            $result['sorts'] = array(
                'STATUS' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'ASSET_STATUS'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data norma dasar dari DB
    public function initNormaBasic($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN',
				'INFLASI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
				'left',
				'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(BASIC_NORMA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PERCENT_INCREASE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'BASIC_NORMA_CODE',
				'DESCRIPTION',
				'PERCENT_INCREASE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			
			if($this->_siteCode <> 'ALL'){
				$where1 .= " AND '".$this->_siteCode."' LIKE '%'||UPPER(BA_CODE)||'%'   ";
			}
			
			if($params['bacode']){
				$where1 .= " AND UPPER(BA_CODE) = '".$params['bacode']."' ";
			}
			
			
            $result['query'] = "
                SELECT BASIC_NORMA_CODE, DESCRIPTION, PERCENT_INCREASE
				FROM TN_BASIC
				WHERE DELETE_USER IS NULL
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master topografi dari DB
    public function initTopography($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KETERANGAN) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'KODE',
                'KETERANGAN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE as KODE, PARAMETER_VALUE as KETERANGAN
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'TOPOGRAPHY'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master tipe tanah dari DB
    public function initLandType($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KETERANGAN) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'KODE',
                'KETERANGAN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE as KODE, PARAMETER_VALUE as KETERANGAN
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'LAND_TYPE'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master jenis bibit dari DB
    public function initProgeny($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KETERANGAN) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'KODE',
                'KETERANGAN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE as KODE, PARAMETER_VALUE as KETERANGAN
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'PROGENY'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master LandSuitability dari DB
    public function initLandSuitability($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KETERANGAN) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'KODE',
                'KETERANGAN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE as KODE, PARAMETER_VALUE as KETERANGAN
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'LAND_SUIT'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master status blok budget dari DB
    public function initStatusBlokBudget($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(KETERANGAN) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'KODE',
                'KETERANGAN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE as KODE, PARAMETER_VALUE as KETERANGAN
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'STATUS_BLOK_BUDGET'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master afdeling dari DB
    public function initAfdeling($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'AFDELING'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'AFDELING' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(AFD_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'AFD_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			$result['query'] = "
                SELECT DISTINCT AFD_CODE
				FROM TM_HECTARE_STATEMENT
				WHERE DELETE_USER IS NULL
					AND UPPER(BA_CODE) = '".$params['bacode']."'
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master afdeling dari DB
    public function initAfdTopoLand($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'AFDELING',
				'TOPOGRAFI',
				'TIPE_TANAH'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'AFDELING' => 'ASC', 
				'TOPOGRAFI' => 'ASC', 
				'TIPE_TANAH' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(AFD_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TOPOGRAPHY) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(LAND_TYPE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'AFD_CODE', 
				'TOPOGRAPHY', 
				'LAND_TYPE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			$result['query'] = "
                SELECT DISTINCT AFD_CODE, TOPOGRAPHY, LAND_TYPE
				FROM TM_HECTARE_STATEMENT
				WHERE DELETE_USER IS NULL
					AND UPPER(BA_CODE) = '".$params['bacode']."'
				{$where2}
                {$orderBy}";
        }
        return $result;
    }
	
	//ambil data master afdeling dari DB
    public function initAfdelingLc($params = null)
    {
        $result = array();
		
        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'AFDELING'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'AFDELING' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = 'WHERE 1=1 ';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(AFD_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'AFD_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			/*$result['query'] = "
				SELECT AFD_CODE
				FROM (
					SELECT AFD_CODE 
					FROM (
						SELECT DISTINCT HS.AFD_CODE
						FROM TM_HECTARE_STATEMENT HS 
						LEFT JOIN (SELECT DISTINCT AFD_CODE,BA_CODE FROM TR_RKT_LC WHERE DELETE_USER IS NULL) a ON
							a.BA_CODE=HS.BA_CODE
						WHERE DELETE_USER IS NULL
							AND UPPER(HS.BA_CODE) = '".$params['bacode']."'
							AND HS.STATUS = 'PROYEKSI') 
					UNION (
						SELECT AFD_CODE 
						FROM (
							SELECT DISTINCT AFD_CODE, BA_CODE, TO_CHAR(PERIOD_BUDGET,'RRRR') PERIOD_BUDGET 
							FROM TR_RKT_LC 
							WHERE DELETE_USER IS NULL
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
						) AFD_GABUNGAN 
						WHERE AFD_CODE NOT IN (
							SELECT DISTINCT AFD_CODE 
							FROM TM_HECTARE_STATEMENT 
							WHERE BA_CODE=AFD_GABUNGAN.BA_CODE
								AND TO_CHAR(PERIOD_BUDGET,'RRRR')=AFD_GABUNGAN.PERIOD_BUDGET
								AND DELETE_USER IS NULL 
						)
						AND UPPER(AFD_GABUNGAN.BA_CODE) = '".$params['bacode']."') 
				)
				{$where2}
                {$orderBy}
            ";*/
			
			//aries hanya ambil sa statement untuk afdeling nya
			$result['query'] = "
						SELECT DISTINCT AFD_CODE
						FROM TM_HECTARE_STATEMENT
                        WHERE BA_CODE = '".$params['bacode']."'
                            AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
                            AND DELETE_USER IS NULL
                            AND STATUS = 'PROYEKSI'
                            ORDER BY AFD_CODE ASC";
        }
				
        return $result;
    }
	
	//ambil data master blok dari DB
    public function initBlock($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'BLOK'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'BLOK' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(BLOCK_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'BLOCK_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			$result['query'] = "
                SELECT DISTINCT BLOCK_CODE
				FROM TM_HECTARE_STATEMENT
				WHERE DELETE_USER IS NULL
					AND UPPER(BA_CODE) = '".$params['bacode']."'
					AND UPPER(AFD_CODE) = '".$params['afd']."'
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data parameter flag panen / non panen dari DB
    public function initFlagPanenNonPanen($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'FLAG'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'FLAG' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'FLAG_PANEN_NON_PANEN'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master RVRA dari DB
    public function initRvra($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(SUB_RVRA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(SUB_RVRA_DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'SUB_RVRA_CODE',
                'SUB_RVRA_DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
			if($params['module']=='mappingJobTypeVra'){
				$where2 .= " AND UPPER(SUB_RVRA_CODE) IN ('R550101010101','R550101010102') ";
			}
            // sql
            $result['query'] = "
                SELECT SUB_RVRA_CODE, SUB_RVRA_DESCRIPTION
				FROM TM_RVRA
				WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master VRA dari DB
    public function initVra($params = null)
    {
        $result = array();
        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN',
				'UOM',
                'SUB_KATEGORI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(VRA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TYPE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(UOM) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(VRA_SUB_CAT_DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'VRA_CODE',
                'TYPE',
				'VRA_SUB_CAT_DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
			
			if($params['module']=='normaDistribusiVraNonInfra'){
				$where2 .= " 
					AND VRA_CODE IN (
						SELECT VRA_CODE 
						FROM TR_RKT_VRA 
						WHERE BA_CODE = '{$params['bacode']}'
							AND DELETE_USER IS NULL
							AND FLAG_TEMP IS NULL
						GROUP BY VRA_CODE
					)
					
					UNION
							SELECT TN_VRA_PINJAM.VRA_CODE, TM.TYPE, TM.UOM, TM.VRA_SUB_CAT_DESCRIPTION
						  FROM TN_VRA_PINJAM
						  INNER JOIN TM_VRA TM ON TM.VRA_CODE = substr(TN_VRA_PINJAM.VRA_CODE,4,8) 
						 WHERE     TM.DELETE_USER IS NULL
							   AND TN_VRA_PINJAM.FLAG_TEMP IS NULL
							   AND TN_VRA_PINJAM.REGION_CODE = (SELECT REGION_CODE
													FROM TM_ORGANIZATION
												   WHERE BA_CODE = '{$params['bacode']}')  
				";
				
				/*$where2 .= " 
					AND VRA_CODE IN (
						SELECT VRA_CODE 
						FROM TR_RKT_VRA 
						WHERE BA_CODE = '{$params['bacode']}'
							AND DELETE_USER IS NULL
							AND FLAG_TEMP IS NULL
						GROUP BY VRA_CODE
						UNION
						SELECT VRA_CODE
						FROM TN_VRA_PINJAM
						WHERE DELETE_USER IS NULL
							AND FLAG_TEMP IS NULL
							AND REGION_CODE = (
								SELECT REGION_CODE
								FROM TM_ORGANIZATION
								WHERE BA_CODE = '{$params['bacode']}'
							)
							AND VRA_CODE LIKE 'ZZ_{$params['vracd']}%' 
					)
				";*/
			}
			elseif($params['module']=='rktVra'){
				$where2 .= " AND SUBSTR(VRA_CODE, 1, 3) <> 'ZZ_'";
			}elseif($params['module']=='normaPerkerasanJalan'){
				$where2 .= " 
					AND VRA_CODE IN (
						SELECT VRA_CODE 
						FROM TR_RKT_VRA_SUM 
						WHERE BA_CODE = '{$params['bacode']}'
							AND DELETE_USER IS NULL
							AND VRA_CODE LIKE '{$params['vracd']}%' 
						GROUP BY VRA_CODE
						)
						UNION
						SELECT TN_VRA_PINJAM.VRA_CODE,
							   TM.TYPE,
							   TM.UOM,
							   TM.VRA_SUB_CAT_DESCRIPTION
						  FROM TN_VRA_PINJAM INNER JOIN TM_VRA TM
								  ON TM.VRA_CODE = SUBSTR (TN_VRA_PINJAM.VRA_CODE, 4, 8)
						 WHERE TM.DELETE_USER IS NULL AND TN_VRA_PINJAM.FLAG_TEMP IS NULL
							   AND TN_VRA_PINJAM.REGION_CODE =
									 (SELECT REGION_CODE
										FROM TM_ORGANIZATION
									   WHERE BA_CODE = '{$params['bacode']}')
								AND TN_VRA_PINJAM.VRA_CODE LIKE 'ZZ_{$params['vracd']}%'       
				";
			}

            // sql
            $result['query'] = "
                SELECT VRA_CODE, TYPE, UOM, VRA_SUB_CAT_DESCRIPTION
				FROM TM_VRA
				WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master Job Type dari DB
    public function initJobType($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(JOB_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(JOB_DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'JOB_CODE',
                'JOB_DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['module']=='mappingJobTypeVra'){
				$where1 .= " AND UPPER(GROUP_CHECKROLL_CODE) = 'HR' ";
			}
			elseif($params['module']=='mappingJobTypeWra'){
				$where1 .= " AND UPPER(GROUP_CHECKROLL_CODE) = 'KR' ";
			}
            $result['query'] = "
                SELECT JOB_CODE, JOB_DESCRIPTION
				FROM TM_JOB_TYPE
				WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master WRA Group dari DB
	//EDITED BY DONI  24-06-2014 : FILTER PARAMETER GAJI DAN TUNJANGAN SAJA YANG TAMPIL
    public function initWraGroup($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE',
                'JOB_DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'WRA_GROUPING'
					AND (UPPER(PARAMETER_VALUE)  LIKE '%GAJI%' OR UPPER(PARAMETER_VALUE)  LIKE '%TUNJANGAN%')
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master activity class dari DB
    public function initActivityClass($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KELAS'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left'
            );
            // sorts
            $result['sorts'] = array(
                'KELAS' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'ACTIVITY_CLASS'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data norma harga + material dari DB
    public function initNormaHarga($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'DESKRIPSI',
				'HARGA',
				'UOM'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
				'left',
				'right',
				'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(norma.MATERIAL_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(material.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(norma.PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
							case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(material.UOM) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'MATERIAL_CODE',
				'MATERIAL_NAME',
				'PRICE',
				'UOM'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
			if(STRTOUPPER($this->_siteCode) <> 'ALL'){
				if ($this->_referenceRole == 'REGION_CODE')
					$where1 .= "AND UPPER('%".$this->_siteCode."%')  LIKE '%'||ORG.REGION_CODE||'%'";
				elseif ($this->_referenceRole == 'BA_CODE')
					$where1 .= "AND UPPER('%".$this->_siteCode."%') LIKE '%'||norma.BA_CODE||'%'";
			}
			if($params['bacode']){
				$where1 .= " AND UPPER(norma.BA_CODE) = '".$params['bacode']."' ";
			}
			
			if($params['tipeNormaHarga'] == 'alatKerjaNonPanen'){
				$where1 .= " AND UPPER(FLAG) LIKE 'NON_PANEN' ";
			}elseif($params['tipeNormaHarga'] == 'alatKerja'){
				$where1 .= " AND UPPER(FLAG) LIKE 'PANEN' AND UPPER(TYPE) LIKE 'MATERIAL' ";
				$result['query'] = "
					SELECT DISTINCT norma.MATERIAL_CODE, 
						   material.DESCRIPTION MATERIAL_NAME,
						   TO_CHAR(norma.PRICE, '999G999G999G999D90')  PRICE, 
						   material.UOM
					FROM TN_HARGA_BARANG norma
					LEFT JOIN V_MATERIAL_ASSET material
						ON norma.MATERIAL_CODE = material.CODE 
						AND MATERIAL.BA_CODE = NORMA.BA_CODE	
						AND norma.PERIOD_BUDGET = material.PERIOD_BUDGET						
					LEFT JOIN TM_ORGANIZATION ORG
						ON norma.BA_CODE = ORG.BA_CODE
					WHERE norma.DELETE_USER IS NULL			
					AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'					
					{$where1}
					{$where2}
					{$orderBy}
				";
					
			}				
			elseif($params['tipeNormaHarga'] == 'wra'){
				$where1 .= " 
					AND norma.PRICE < (
						SELECT PARAMETER_VALUE
						FROM T_PARAMETER_VALUE
						WHERE DELETE_USER IS NULL
							AND PARAMETER_CODE = 'MAX_PRICE'
							AND PARAMETER_VALUE_CODE = 'MAX_WRA'
					)
					AND material.FLAG='WRA'
				";
			
				$result['query'] = "
					SELECT norma.MATERIAL_CODE, 
						   material.DESCRIPTION MATERIAL_NAME,
						   TO_CHAR(norma.PRICE, '999G999G999G999D90') PRICE, 
						   material.UOM
					FROM TN_HARGA_BARANG norma
					LEFT JOIN V_MATERIAL_ASSET material
						ON norma.MATERIAL_CODE = material.CODE 
						AND MATERIAL.BA_CODE = NORMA.BA_CODE
						AND MATERIAL.PERIOD_BUDGET = NORMA.PERIOD_BUDGET
					LEFT JOIN TM_ORGANIZATION ORG
						ON norma.BA_CODE = ORG.BA_CODE
					WHERE norma.DELETE_USER IS NULL	
						AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
					{$where1}
					{$where2}
					{$orderBy}
				";
			}
        }
        return $result;
    }
	
	//ambil data master region dari DB
    public function initRegion($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'DESKRIPSI'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(REGION_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(REGION_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;							
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'REGION_CODE',
				'REGION_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
				if ($this->_referenceRole == 'REGION_CODE')
					$where1 .= " AND   '".$this->_siteCode."' LIKE  '%'||UPPER(REGION_CODE) ||'%'   ";
				elseif ($this->_referenceRole == 'BA_CODE')
					$where1 .= " AND '".$this->_siteCode."' LIKE  '%'|| UPPER(BA_CODE)  ||'%'   ";
            // sql
            $result['query'] = "
                SELECT  REGION_CODE, REGION_NAME
				FROM TM_ORGANIZATION
				WHERE BA_CODE <> 'ALL'
					AND DELETE_USER IS NULL
                {$where1}
				{$where2}
				GROUP BY REGION_CODE, REGION_NAME
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master group BUM dari DB
    public function initGroupBum($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;							
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PARAMETER_VALUE_CODE',
				'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
            // sql
            $result['query'] = "
                SELECT TO_NUMBER(PARAMETER_VALUE_CODE) PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'GROUP_BUM'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master periode budget dari DB
    public function initBudgetPeriod($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'PERIODE',
                'START',
				'END'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PERIOD_BUDGET) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(START_BUDGETING) LIKE ' . $this->_db->quote("%{$val}%");
                                break;	
							case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(END_BUDGETING) LIKE ' . $this->_db->quote("%{$val}%");
                                break;	
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PERIOD_BUDGET',
				'START_BUDGETING',
				'END_BUDGETING'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
            // sql
            $result['query'] = "
                SELECT to_char(PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, START_BUDGETING, END_BUDGETING
				FROM TM_PERIOD
				WHERE DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	
	//ambil data master COA dari DB
    public function initCoaCapex($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'DESKRIPSI'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'DESC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }

        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'COA_CODE',
				'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT COA_CODE, DESCRIPTION
				FROM TM_COA
				WHERE DELETE_USER IS NULL
				AND UPPER(FLAG) = 'CAPEX'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master maturity stage dari DB
    public function initMaturityStage($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'STATUS'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'STATUS' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if ($params['module'] == 'normaPupukTbmTm'){
				$where1 .= " AND PARAMETER_VALUE_CODE IN ('TM', 'ALL')";
			}
					
            $result['query'] = "
                SELECT DISTINCT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'MATURITY_STAGE'
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master group CSR dari DB
    public function initGroupCsr($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;							
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PARAMETER_VALUE_CODE',
				'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'GROUP_CSR'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master group IR dari DB
    public function initGroupIr($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;							
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PARAMETER_VALUE_CODE',
				'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'GROUP_IR'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master group SHE dari DB
    public function initGroupShe($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;							
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'PARAMETER_VALUE_CODE',
				'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE = 'GROUP_SHE'
					AND DELETE_USER IS NULL
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master activity untuk norma alat kerja non panen dari DB
    public function initActivityNormaAlatKerjaNonPanen($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN',
				'TOTAL'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left',
				'right'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(act.ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(act.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(sum_norma.TOTAL) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ACTIVITY_CODE',
				'DESCRIPTION',
				'TOTAL'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT act.ACTIVITY_CODE, 
					   act.DESCRIPTION,
					   TO_CHAR(sum_norma.TOTAL, '999G999G999G999D90')  TOTAL
				FROM TM_ACTIVITY act
				LEFT JOIN TN_ALAT_KERJA_NON_PANEN_SUM sum_norma
					ON act.ACTIVITY_CODE = sum_norma.ACTIVITY_CODE				
					AND sum_norma.BA_CODE = '".$params['bacode']."' 
					AND to_char(sum_norma.PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				LEFT JOIN TM_ACTIVITY_MAPPING mapping
					ON act.ACTIVITY_CODE = mapping.ACTIVITY_CODE
				WHERE act.DELETE_USER IS NULL	
				AND act.ACTIVITY_PARENT_CODE IS NULL
				AND mapping.UI_RKT_CODE IN ('RKT007', 'RKT006', 'RKT001', 'RKT002','RKT008')
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data norma harga + asset dari DB
    public function initAsset($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN',
				'HARGA',
				'UOM'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left',
				'right',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }
		
		if (is_array($params)) {
            // where2
			$where1 = '';
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(norma.MATERIAL_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(asset.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(norma.PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(asset.UOM) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'MATERIAL_CODE',
				'DESCRIPTION',
				'PRICE',
				'UOM'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            if($this->_siteCode <> 'ALL'){
				if ($this->_referenceRole == 'REGION_CODE')
					$where1 .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
				elseif ($this->_referenceRole == 'BA_CODE')
					$where1 .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(norma.BA_CODE)||'%' ";
			}
			if($params['bacode']){
				$where1 .= " AND UPPER(norma.BA_CODE) = '".$params['bacode']."' ";
			}
			if($params['coa']){
				$where1 .= " AND UPPER(asset.COA_CODE) = '".$params['coa']."' ";
			}
            $result['query'] = "
                SELECT norma.MATERIAL_CODE, 
					   asset.DESCRIPTION,
					   TO_CHAR(norma.PRICE, '999G999G999G999D90') PRICE,
					   asset.UOM
				FROM TN_HARGA_BARANG norma
				LEFT JOIN TM_ASSET asset
					ON norma.MATERIAL_CODE = asset.ASSET_CODE
					AND norma.BA_CODE = asset.BA_CODE
					AND norma.PERIOD_BUDGET = asset.PERIOD_BUDGET
				LEFT JOIN TM_ORGANIZATION ORG
					ON norma.BA_CODE = ORG.BA_CODE
				WHERE norma.DELETE_USER IS NULL	
					AND norma.STATUS = 'ASSET'
					AND to_char(norma.PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
		return $result;
    }
	
	//ambil data norma urgency CAPEX dari DB
    public function initUrgencyCapex($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KETERANGAN' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL	
					AND PARAMETER_CODE = 'CAPEX_URGENCY'
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data coa OPEX dari DB / table GROUP BUM
    public function initCoaOpex($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'COA',
				'KETERANGAN',
				'GRUP',
				'DESKRIPSI_GRUP'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'COA' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(grup.COA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(coa.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(grup.GROUP_BUM_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(desc_group.PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'COA_CODE',
                'DESCRIPTION',
                'GROUP_BUM_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT DISTINCT grup.COA_CODE, coa.DESCRIPTION, grup.GROUP_BUM_CODE, desc_group.PARAMETER_VALUE
				FROM TM_ACTIVITY_MAPPING mapping
				LEFT JOIN TM_GROUP_BUM_COA grup
					ON mapping.ACTIVITY_CODE = grup.COA_CODE
				LEFT JOIN T_PARAMETER_VALUE desc_group
					ON desc_group.PARAMETER_VALUE_CODE = grup.GROUP_BUM_CODE
					AND desc_group.PARAMETER_CODE = 'GROUP_BUM'
				LEFT JOIN TM_COA coa
					ON grup.COA_CODE = coa.COA_CODE
				WHERE mapping.DELETE_USER IS NULL
				AND grup.DELETE_USER IS NULL
				AND (coa.FLAG = 'OPEX'
				OR coa.FLAG2 = 'OPEXX')
                {$where1}
                {$where2}
                {$orderBy}
            ";
			
			//die($result['query']);
        }
        return $result;
    }
	
	//ambil data group BUM - COA dari DB
    public function initGroupBumCoa($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(grup.GROUP_BUM_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(param.PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'GROUP_BUM_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['coa']){
				$where1 .= " AND grup.COA_CODE = '".$params['coa']."' ";
			}
			
            $result['query'] = "
                SELECT DISTINCT grup.GROUP_BUM_CODE, param.PARAMETER_VALUE
				FROM TM_GROUP_BUM_COA grup
				LEFT JOIN T_PARAMETER_VALUE param
					ON grup.GROUP_BUM_CODE = param.PARAMETER_VALUE_CODE
					AND param.PARAMETER_CODE = 'GROUP_BUM'
				WHERE grup.DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master COA dari mapping COA - relation dari DB
    public function initCoaRelation($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'COA',
				'KETERANGAN',
				'GRUP',
				'DESKRIPSI_GRUP'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'COA' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(master_relation.COA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(coa.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(master_relation.GROUP_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(desc_group.PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'COA_CODE',
                'DESCRIPTION',
                'GROUP_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['type']){
				$where3 .= " AND UPPER(desc_group.PARAMETER_CODE) = UPPER('GROUP_".$params['type']."') ";
				$where1 .= " AND UPPER(master_relation.TYPE) = UPPER('".$params['type']."') ";
			}
			
            $result['query'] = "
                SELECT DISTINCT master_relation.COA_CODE, coa.DESCRIPTION, master_relation.GROUP_CODE, desc_group.PARAMETER_VALUE
				FROM TM_GROUP_RELATION master_relation
				LEFT JOIN T_PARAMETER_VALUE desc_group
					ON desc_group.PARAMETER_VALUE_CODE = master_relation.GROUP_CODE
					{$where3}
				LEFT JOIN TM_COA coa
					ON master_relation.COA_CODE = coa.COA_CODE
				WHERE master_relation.DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
			/*die($result['query']);
			SELECT DISTINCT master_relation.COA_CODE,
							  coa.DESCRIPTION,
							  master_relation.GROUP_CODE,
							  desc_group.PARAMETER_VALUE
				FROM TM_GROUP_RELATION master_relation
					 LEFT JOIN T_PARAMETER_VALUE desc_group
						ON desc_group.PARAMETER_VALUE_CODE = master_relation.GROUP_CODE
						   AND UPPER (desc_group.PARAMETER_CODE) = UPPER ('GROUP_csr') --
					 LEFT JOIN TM_COA coa
						ON master_relation.COA_CODE = coa.COA_CODE
			   WHERE master_relation.DELETE_USER IS NULL
					 AND UPPER (master_relation.TYPE) = UPPER ('csr') --
			ORDER BY COA_CODE ASC --
			
			SELECT DISTINCT master_relation.COA_CODE,
							  coa.DESCRIPTION,
							  master_relation.GROUP_CODE,
							  desc_group.PARAMETER_VALUE
				FROM TM_GROUP_RELATION master_relation
					 LEFT JOIN T_PARAMETER_VALUE desc_group
						ON desc_group.PARAMETER_VALUE_CODE = master_relation.GROUP_CODE
						   AND UPPER (desc_group.PARAMETER_CODE) = UPPER ('GROUP_she')
					 LEFT JOIN TM_COA coa
						ON master_relation.COA_CODE = coa.COA_CODE
			   WHERE master_relation.DELETE_USER IS NULL
					 AND UPPER (master_relation.TYPE) = UPPER ('she')
			ORDER BY COA_CODE ASC*/
        }
        return $result;
    }
	
	//ambil data master group relation dari mapping COA - relation dari DB
    public function initGroupRelation($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(master_relation.GROUP_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(param.PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'GROUP_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['type']){
				$where1 .= " AND UPPER(master_relation.TYPE) = UPPER('".$params['type']."') ";
			}
			if($params['coa']){
				$where1 .= " AND UPPER(master_relation.COA_CODE) = UPPER('".$params['coa']."') ";
			}
			
            $result['query'] = "
                SELECT DISTINCT master_relation.GROUP_CODE, param.PARAMETER_VALUE
				FROM TM_GROUP_RELATION master_relation
				LEFT JOIN T_PARAMETER_VALUE param
					ON master_relation.GROUP_CODE = param.PARAMETER_VALUE_CODE
				WHERE master_relation.DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master sub group relation dari mapping COA - relation dari DB
    public function initSubGroupRelation($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(master_relation.SUB_GROUP_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(master_relation.SUB_GROUP_DESC) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'SUB_GROUP_CODE',
                'SUB_GROUP_DESC'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['type']){
				$where1 .= " AND UPPER(master_relation.TYPE) = UPPER('".$params['type']."') ";
			}
			if($params['coa']){
				$where1 .= " AND UPPER(master_relation.COA_CODE) = UPPER('".$params['coa']."') ";
			}
			if($params['group']){
				$where1 .= " AND UPPER(master_relation.GROUP_CODE) = UPPER('".$params['group']."') ";
			}
			
            $result['query'] = "
                SELECT DISTINCT master_relation.SUB_GROUP_CODE, master_relation.SUB_GROUP_DESC
				FROM TM_GROUP_RELATION master_relation
				WHERE master_relation.DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master activity yang digunakan pada RKT
    public function initActivityRkt($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'ACTIVITY_CODE',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			$result['query'] = "
                SELECT DISTINCT ACTIVITY_CODE, DESCRIPTION
				FROM TM_ACTIVITY
				WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master sumber biaya
    public function initSumberBiaya($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'SUMBER_BIAYA'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'SUMBER_BIAYA' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // remark by doni 2013-09-04
			/*if($params['module'] == 'rktManualInfra'){
				$sql = "
					SELECT SUM(BIAYA) BIAYA, SUM(BORONG) BORONG
					FROM (
						SELECT COUNT(*) BIAYA, 0 BORONG
						FROM TN_BIAYA
						WHERE BA_CODE = '".$params['bacode']."'
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".$params['activity']."'
							AND ACTIVITY_CLASS = '".$params['class']."'
						UNION
						SELECT 0 BIAYA, COUNT(*) BORONG
						FROM TN_HARGA_BORONG
						WHERE BA_CODE = '".$params['bacode']."'
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".$params['activity']."'
							AND ACTIVITY_CLASS = '".$params['class']."'
					)
				";
				
				
				$count = $this->_db->fetchRow($sql);
				
				if (($count['BIAYA'] == 0) &&  ($count['BORONG'] == 1)){
					$where1 = " AND PARAMETER_VALUE_CODE = 'EXTERNAL'";
				} else if (($count['BIAYA'] == 1) &&  ($count['BORONG'] == 0)){
					$where1 = " AND PARAMETER_VALUE_CODE = 'INTERNAL'";
				} else if (($count['BIAYA'] == 1) &&  ($count['BORONG'] == 1)){
					$where1 = "";
				}
			}else 
			*/
			
			//kalau manual infra ada pilihan untuk sumber biaya
			if($params['module'] == 'rktManualInfra'){
				$sql = "
					SELECT SUM(BIAYA) BIAYA, SUM(BORONG) BORONG
					FROM (
					  SELECT COUNT(*) BIAYA, 0 BORONG
                        FROM TN_INFRASTRUKTUR
						WHERE BA_CODE = '".$params['bacode']."'
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".$params['activity']."'
                            AND COST_ELEMENT IN ('LABOUR','TRANSPORT') 
						UNION
						SELECT 0 BIAYA, COUNT(*) BORONG
						FROM TN_HARGA_BORONG
						WHERE BA_CODE = '".$params['bacode']."'
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".$params['activity']."'
							AND ACTIVITY_CLASS = 'ALL'
					)
				";
				
				$count = $this->_db->fetchRow($sql);
				
				if (($count['BIAYA'] == 0) &&  ($count['BORONG'] > 0)){
					$where1 = " AND PARAMETER_VALUE_CODE = 'EXTERNAL'";
				} else if (($count['BIAYA'] > 0) &&  ($count['BORONG'] == 0)){
					$where1 = " AND PARAMETER_VALUE_CODE = 'INTERNAL'";
				} else if (($count['BIAYA'] > 0) &&  ($count['BORONG'] > 0)){
					$where1 = "";
				}
			}
			
			$result['query'] = "
                SELECT PARAMETER_VALUE_CODE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'SUMBER_BIAYA'
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }

	//ambil data master activity yang digunakan pada RKT Perkerasan Jalan
	/* remark by doni 2013-09-04
    public function initActivityPK($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'ACTIVITY_CODE',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }

			// sql
			if($params['src_region_code']){
                $where1 = "  AND UPPER(PK.ACTIVITY_CODE) LIKE UPPER "'.$params['src_activity_code']."'";
			}

			$result['query'] = "
                SELECT AM.ACTIVITY_CODE, TA.DESCRIPTION
				FROM TM_ACTIVITY_MAPPING AM LEFT JOIN TM_ACTIVITY TA
				ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
				WHERE AM.UI_RKT_CODE = 'RKT008'
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	*/
	//ambil data master activity opsi
    public function initActivityOpsi($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'OPSI',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'OPSI' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ACTIVITY_CODE',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['activity']){
				$where1 = " AND ACTIVITY_PARENT_CODE = '".$params['activity']."'";
			}
			
			$result['query'] = "
                SELECT ACTIVITY_CODE, DESCRIPTION
				FROM TM_ACTIVITY
				WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }

	//ambil data jarak perkerasan jalan
    public function initJarakPerkerasanJalan($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if($params['JARAK']){
				$where1 = " AND PARAMETER_VALUE = '".$params['JARAK']."'";
			}
			
			$result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE LIKE '%JARAK_PERKERASAN_JALAN%'
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data tipe perkerasan jalan
    public function initPerulanganBaru($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE',
                'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
			if((($params['semester1'])  != 'TM') && (($params['semester2'])  != 'TM')) {
				$where1 = " AND PARAMETER_VALUE_CODE LIKE '%BARU%'";
			}else{
				$where1 = " AND PARAMETER_VALUE_CODE LIKE '%%'";
			}
			
			$result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE LIKE '%JENIS_PERKERASAN_JALAN%'
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	
	//ambil data master Activity dari DB activityMappAction
    public function initActivityMapp($params = null)
    {
        $result = array();
        $where1 = '';

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
                'KETERANGAN',
                'UOM'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
			$where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(AM.ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TA.DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TA.UOM) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
			
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'ACTIVITY_CODE',
                'DESCRIPTION',
                'UOM'
            );
			
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
			
			/* data per 2013-09-04
			RKT001	RKT MANUAL NON INFRA + CLASS								
			RKT002	RKT MANUAL - NON INFRA + OPSI								
			RKT003	RKT MANUAL INFRA + CLASS								
			RKT004	RKT PERKERASAN JALAN								
			RKT005	RKT TANAM 								
			RKT006	RKT TANAM MANUAL								
			RKT007	RKT LC								
			RPT008	RPT PUPUK HA (RKT)								
			RKT009	RPT PUPUK KG SISIP (RKT)								
			RKT010	RPT PUPUK KG NORMAL (RKT)								
			RKT011	RKT PUPUK DISTRIBUSI BIAYA SISIP (RKT)								
			RKT012	RKT PUPUK DISTRIBUSI BIAYA NORMAL (RKT)								
			RKT013	RPT PUPUK DISTRIBUSI BIAYA GABUNGAN (RKT)								
			RKT014	RKT PANEN								
			RKT015	RKT DISTRIBUSI VRA INFRA								
			RKT016	RKT DISTRIBUSI VRA NON INFRA								
			RKT017	RKT OPEX								
			RKT018	RKT INTERNAL RELATION								
			RKT019	RKT CSR								
			RKT020	RKT SHE								
			RKT021	RKT VRA								
			RKT022	RKT CAPEX								
			*/

			if($params['module']=='rktManualNonInfra'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT001') ";
			}else if($params['module']=='rktManualNonInfraOpsi'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT002') ";
			}else  if($params['module']=='rktManualInfra'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT003') ";
			}else if($params['module']=='rktPerkerasanJalan'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT004') ";
			}else  if($params['module']=='rktTanam'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT005') ";
			}else if($params['module']=='rktTanamManual'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT006') ";
			}else if($params['module']=='rktLc'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT007') ";
			}else if($params['module']=='rktPupukHa'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT008') ";
			}else if($params['module']=='rktPupukKgSisip'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT009') ";
			}else if($params['module']=='rktPupukKgNormal'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT010') ";
			}else if($params['module']=='rktPupukDisBiayaSisip'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT011') ";
			}else if($params['module']=='rktPupukDisBiayaNormal'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT012') ";
			}else if($params['module']=='rktPupukDisBiayaGabungan'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT013') ";
			}else if($params['module']=='rktPanen'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT014') ";
			}else if($params['module']=='rktDisVraInfra'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT015') ";
			}else if($params['module']=='normaDistribusiVraNonInfra'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT016') ";
			}else if($params['module']=='rktOpex'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT017') ";
			}else if($params['module']=='rktInternalRelation'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT018') ";
			}else if($params['module']=='rktCsr'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT019') ";
			}else if($params['module']=='rktShe'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT020') ";
			}else if($params['module']=='rktVra'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT021') ";
			}else if($params['module']=='rktCapex'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT022') ";
			}else if($params['module']=='rktKastrasiSanitasi'){
				$where2 .= " AND UPPER(AM.UI_RKT_CODE) IN ('RKT023') ";
			}else if($params['module']=='rktManualSisip'){
				$where2 .= " AND UPPER(AM.ACTIVITY_CODE) IN ('42700') ";
			} 	
			
            //sql
            $result['query'] = "
                SELECT AM.ACTIVITY_CODE, TA.DESCRIPTION, TA.UOM
				FROM TM_ACTIVITY_MAPPING AM 
				LEFT JOIN TM_ACTIVITY TA
					ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
				WHERE TA.DELETE_USER IS NULL
                {$where1}
				{$where2}
				GROUP BY AM.ACTIVITY_CODE, TA.DESCRIPTION, TA.UOM
                {$orderBy}
				"; 
        }
		//die($result['query']);
        return $result;
    }
	
	//ambil data master activity yang digunakan pada RKT tanam activityTanamAction
	/*
    public function initActivityTanam($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'ACTIVITY_CODE',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }

			// sql
			//if($params['src_region_code']){
            //    $where1 = "  AND UPPER(PK.ACTIVITY_CODE) LIKE UPPER "'.$params['src_activity_code']."'";
			}

			$result['query'] = "
                SELECT AM.ACTIVITY_CODE, TA.DESCRIPTION
				FROM TM_ACTIVITY_MAPPING AM LEFT JOIN TM_ACTIVITY TA
				ON AM.ACTIVITY_CODE = TA.ACTIVITY_CODE
				WHERE AM.UI_RKT_CODE = 'RKT005'
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
	*/
	
	//ambil data master activity yang digunakan pada RKT tanam
    public function initActivityTanam1($params = null)
    {
        $result = array();
        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
				'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ACTIVITY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
				'ACTIVITY_CODE',
                'DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }

			$result['query'] = "
			SELECT DISTINCT NILAI 
			FROM ( 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_BIAYA 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
					AND COST_ELEMENT = 'LABOUR' 
				UNION 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_HARGA_BORONG 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
                {$where1}
				{$where2}
                {$orderBy}
            ";
        }
        return $result;
    }

	//get activity class dari norma biaya
    public function initActivityClassTanam($params = array())
    {
		$value = array();
		$query = "
			SELECT DISTINCT NILAI 
			FROM ( 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_BIAYA 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
					AND COST_ELEMENT = 'LABOUR' 
				UNION 
				SELECT ACTIVITY_CLASS NILAI 
				FROM TN_HARGA_BORONG 
				WHERE DELETE_USER IS NULL 
					AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."' 
					AND BA_CODE = '".$params['key_find']."' 
					AND ACTIVITY_CODE = '".$params['src_coa_code']."' 
			)
			ORDER BY NILAI";
		
		$sql = "SELECT COUNT(*) FROM ({$query})";
        $value['count'] = $this->_db->fetchOne($sql);
		$rows = $this->_db->fetchAll($query);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				if ($row['ACTIVITY_CLASS'] == 'ALL') {
					$value['rows'] = '';
					$value['rows'][] = $row;
					break;
				}else{
					$value['rows'][] = $row;
				}
			}
        }
		return $value;
	}

	//ambil data master jenis RKT
    public function initRkt($params = null)
    {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'KODE',
				'KETERANGAN'
            );
            // aligns
            $result['aligns'] = array (
                'center',
				'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'KODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PARAMETER_VALUE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'PARAMETER_VALUE_CODE',
				'PARAMETER_VALUE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            // sql
            $result['query'] = "
                SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_USER IS NULL
					AND PARAMETER_CODE = 'UI_RKT'
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }

    public function initCostCenter($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'COST CENTER CODE',
                'COST CENTER NAME',
                'COST CENTER HEAD'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = $where3 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(HCC_CC) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(HCC_COST_CENTER) LIKE ' . $this->_db->quote("%{$val}%");
                                break;  
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(HCC_COST_CENTER_HEAD) LIKE ' . $this->_db->quote("%{$val}%");
                                break;  
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'HCC_CC',
                'HCC_COST_CENTER',
                'HCC_COST_CENTER_HEAD'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['division']) {
                $where1 .= " AND UPPER(HCC_DIVISI) = '".$params['division']."' ";
            }
            
            if (isset($params['ac']) && !empty($params['ac']) && $params['ac'] != 'ALL') {
                if (isset($params['us']) && !empty($params['us'])) {
                    $sqls = "SELECT HO_CC_CODE FROM TM_USER WHERE USER_NAME = '{$params['us']}'";
                    $ress = $this->_db->fetchOne($sqls);

                    if (!empty($ress)) {
                        $ress = "'" . str_replace(',', "','", $ress) . "'";
                    }

                    $q = "SELECT HCC_CC FROM TM_HO_COST_CENTER WHERE HCC_CC IN ($ress) AND HCC_DIVISI = '{$params['division']}'";
                    $qres = $this->_db->fetchAll($q);
                    $rows = '';
                    if (!empty($qres)) {
                        foreach ($qres as $row) {
                            $rows .= "'" . $row['HCC_CC'] . "',";
                        }
                    }
                    $rows = substr($rows, 0, -1);
                    //echo strlen($rows); die;
                    $where3 .= (strlen($rows) > 0) ? " AND HCC_CC IN ($rows)" : " AND HCC_CC IN ('')";
                }
            }
            
            // sql
            $result['query'] = "
                SELECT HCC_CC, HCC_COST_CENTER, HCC_COST_CENTER_HEAD
                FROM TM_HO_COST_CENTER
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$where3}
                {$orderBy}
            ";
        }
        return $result;
    }
	
    public function initHoCoa($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'COA CODE',
                'COA NAME'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COA_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COA_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'COA_CODE',
                'COA_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }

            if (isset($params['module']) && $params['module'] == 'hoActOutlook') {
                $where1 .= " AND (COA_CODE LIKE '52%' OR COA_CODE LIKE '54%' OR COA_CODE LIKE '62%') ";
            }
            if (isset($params['module']) && $params['module'] == 'hoCapex') {
                $where1 .= " AND COA_GROUP LIKE 'CAPEX%' ";
            }
            if (isset($params['module']) && $params['module'] == 'hoOpex') {
                $where1 .= " AND COA_GROUP LIKE 'OPEX%' ";
                if (isset($params['core']) && $params['core'] == 'SITE') {
                    $where1 .= " AND COA_CODE LIKE '52%'";
                } else if (isset($params['core']) && $params['core'] == 'HO') {
                    $where1 .= " AND (COA_CODE LIKE '62%' OR COA_CODE LIKE '71%' OR COA_CODE LIKE '61%')";
                } else if (isset($params['core']) && $params['core'] == 'MILL') {
                    $where1 .= " AND COA_CODE LIKE '54%'";
                } else if (isset($params['core']) && $params['core'] == 'PLASMA') {
                    $where1 .= " AND COA_CODE = '1205010101'";
                } else {
                    $where1 .= "";
                }
            }
            
            /*if ($params['division']) {
                $where1 .= " AND UPPER(HCC_DIVISI) = '".$params['division']."' ";
            }*/
            
            // sql
            $result['query'] = "
                SELECT COA_CODE, COA_NAME
                FROM TM_HO_COA
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
            //echo $result['query']; die;
        }
        return $result;
    }
    
    public function initHoCore($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'CORE CODE',
                'CORE NAME'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(CORE_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(CORE_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'CORE_CODE',
                'CORE_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            /*if ($params['division']) {
                $where1 .= " AND UPPER(HCC_DIVISI) = '".$params['division']."' ";
            }*/
            
            // sql
            $result['query'] = "
                SELECT CORE_CODE, CORE_NAME
                FROM TM_HO_CORE
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
 
    public function initHoCompany($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'COMPANY CODE',
                'COMPANY NAME',
                'BA CODE',
                'BA NAME'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COMPANY_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COMPANY_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'COMPANY_CODE',
                'COMPANY_NAME',
                'BA_CODE',
                'BA_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['core']) {
                $where1 .= " AND UPPER(CORE) = '".$params['core']."' ";
            }
            
            // sql
            $result['query'] = "
                SELECT COMPANY_CODE, COMPANY_NAME, BA_CODE, BA_NAME
                FROM TM_HO_COMPANY
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
 
    public function initHoNormaSpd($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'ID',
                'RUTE',
                'TIKET PESAWAT (HARGA N)', 
                'TIKET PESAWAT (HARGA P)',
                'TAXI (QTY)', 
                'TAXI (HARGA)', 
                'CHARTER (QTY)', 
                'CHARTER (HARGA)', 
                'KENDARAAN AIR (QTY)', 
                'KENDARAAN AIR (HARGA)'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ID) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(RUTE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PLANE_N_PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 4:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(PLANE_P_PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 5:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TAXI_QTY) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 6:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(TAXI_N_PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 7:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(CHARTER_QTY) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 8:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(CHARTER_N_PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 9:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(WATER_VEH_QTY) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 10:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(WATER_VEH_N_PRICE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ID',
                'RUTE',
                'TOTAL_PRICE'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['core']) {
                $where1 .= " AND UPPER(CORE) = '".$params['core']."' ";
            }
            
            // sql
            $result['query'] = "
                SELECT ID, RUTE, PLANE_N_PRICE, PLANE_P_PRICE, TAXI_QTY, TAXI_N_PRICE, CHARTER_QTY, CHARTER_N_PRICE, WATER_VEH_QTY, WATER_VEH_N_PRICE
                FROM TM_HO_NORMA_SPD
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
            //echo $result['query'];
        }
        return $result;
    }

    public function initHoStandarSpd($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'GOLONGAN',
                'HOTEL',
                'UANG MAKAN',
                'UANG SAKU',
                'TRANSPORT LAIN-LAIN'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(RUTE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(COMPANY_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'GOLONGAN',
                'HOTEL',
                'UANG_MAKAN',
                'UANG_SAKU',
                'TRANSPORT_LAIN'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['core']) {
                $where1 .= " AND UPPER(CORE) = '".$params['core']."' ";
            }
            
            // sql
            $result['query'] = "
                SELECT GOLONGAN, HOTEL, UANG_MAKAN, UANG_SAKU, TRANSPORT_LAIN
                FROM TM_HO_STANDAR_SPD
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }

    public function initHoRencanaKerja($params = null) {
        $result = array();

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'ID',
                'RENCANA KERJA',
                'DESCRIPTION'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(ID) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(RK_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 3:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(RK_DESCRIPTION) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'ID',
                'RK_NAME',
                'RK_DESCRIPTION'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['cc']) {
                $where1 .= " AND UPPER(CC_CODE) = '".$params['cc']."' ";
            }
            
            // sql
            $result['query'] = "
                SELECT ID, RK_NAME, RK_DESCRIPTION
                FROM TM_HO_RENCANA_KERJA
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$orderBy}
            ";
        }
        return $result;
    }
 
    public function initHoDivision($params = null) {
        $result = array();
        //print_r ($params['us']);

        if (is_null($params))
        {
            // headers
            $result['headers'] = array (
                '#',
                'DIV CODE',
                'DIVISION NAME'
            );
            // aligns
            $result['aligns'] = array (
                'left',
                'left',
                'left'
            );
            // sorts
            $result['sorts'] = array(
                'PERIODE' => 'ASC'
            );
            // error?
            if(count($result['headers']) != count($result['aligns']))
            {
                die('Error: array count is not match!');
            }
        }

        if (is_array($params)) {
            // where2
            $where1 = $where2 = $where3 = '';
            if (isset($params['sSearch']) && $params['sSearch'] != '') {
                $arr = explode('~', $params['sSearch']);
                foreach ($arr as $key => $val) {
                    if ($val != '') {
                        switch ($key) {
                            case 0:
                                $where2 .= ' AND MY_ROWNUM = ' . intval($val);
                                break;
                            case 1:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DIV_CODE) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                            case 2:
                                $val = strtoupper($val);
                                $where2 .= ' AND UPPER(DIV_NAME) LIKE ' . $this->_db->quote("%{$val}%");
                                break;
                        }
                    }
                }
            }
            // orderBy
            $sortCol = array(
                'MY_ROWNUM',
                'DIV_CODE',
                'DIV_NAME'
            );
            $orderBy = '';
            if (isset($params['iSortCol_0']) && $params['iSortCol_0'] != '') {
                for ($i=0;$i<intval($params['iSortingCols']);$i++) {
                    $orderBy .= $sortCol[intval($params['iSortCol_' . $i])] . ' ' . $params['sSortDir_' . $i] . ', ';
                }
                $orderBy = 'ORDER BY ' . substr_replace($orderBy, '', -2);
            }
            
            if ($params['core']) {
                $where1 .= " AND UPPER(CORE) = '".$params['core']."' ";
            }

            if (isset($params['ac']) && !empty($params['ac']) && $params['ac'] != 'ALL') {
                if (isset($params['us']) && !empty($params['us'])) {
                    $sqls = "SELECT HO_DIV_CODE FROM TM_USER WHERE USER_NAME = '{$params['us']}'";
                    $ress = $this->_db->fetchOne($sqls);

                    if (!empty($ress)) {
                        $ress = "'" . str_replace(',', "','", $ress) . "'";
                    }
                    $where3 .= " AND DIV_CODE IN ($ress)";
                }
            }
            
            // sql
            $result['query'] = "
                SELECT DIV_CODE, DIV_NAME
                FROM TM_HO_DIVISION
                WHERE DELETE_USER IS NULL
                {$where1}
                {$where2}
                {$where3}
                {$orderBy}
            "; //echo $result['query']; die;
        }
        return $result;
    }

 	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
	//inisialisasi list yang akan ditampilkan
	public function initList($params = array())
    {
        $result = array();

        $initAction = 'init' . str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));

        $result = $this->$initAction();
        return $result;
    }

    //menampilkan list
	public function getList($params = array())
    {
        $result = array(); //print_r($params); die();

        $result['sEcho'] = intval($params['sEcho']);
        $result['iTotalRecords'] = 0;
        $result['iTotalDisplayRecords'] = 0;
        $result['aaData'] = array();

        $min = $params['iDisplayStart'];
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
        //$this->_global->printDebug($init['query']);

        // -- rows count (all)
        $sql = "SELECT COUNT(*) FROM ({$init['query']})";
        $result['iTotalRecords'] = $this->_db->fetchOne($sql);
        // -- rows count (filter)
        //$sql = "SELECT COUNT(*) FROM ({$init['query']})";
        //$result['iTotalDisplayRecords'] = $this->_db->fetchOne($sql);
        $result['iTotalDisplayRecords'] = $result['iTotalRecords'];
        // -- rows
        $sql = "{$begin} {$init['query']} {$end}";
        $this->_db->setFetchMode(Zend_Db::FETCH_NUM);
        $rows = $this->_db->fetchAll($sql);

        $pick = '<input type="button" name="pick-{id}" id="pick-{id}" value="Pilih" class="button" />';
        foreach ($rows as $idx => $row) {
            $row[0] = str_replace('{id}', $row[0], $pick);
            $result['aaData'][] = $row;
        }

        return $result;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
