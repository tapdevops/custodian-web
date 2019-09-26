<?php
/*
=========================================================================================================================
Project                :     Budgeting & Planning System
Versi                :     2.0.0
Deskripsi            :     Model Class untuk RKT VRA
Function             :    - getInput                    : YIR 20/06/2014    : setting input untuk region
                        - getData                    : SID 11/07/2013    : ambil data dari DB
                        - getList                    : SID 11/07/2013    : menampilkan list RKT VRA
                        - getDataDownload            : SID 11/07/2013    : ambil data untuk didownload ke excel dari DB
                        - getTunjangan                : SID 11/07/2013    : menampilkan list tunjangan
                        - getPkUmum                    : SID 11/07/2013    : menampilkan list pk umum
                        - save                        : SID 11/07/2013    : simpan data
                        - saveTemp                    : SID 11/07/2013    : hapus data di RKT VRA, insert inputan user di RKT VRA
                        - delete                    : SID 11/07/2013    : hapus data
                        - updateSummaryRktVra        : SID 11/07/2013    : update summary RKT VRA
Disusun Oleh        :     IT Enterprise Solution - PT Triputra Agro Persada
Developer            :     Sabrina Ingrid Davita
Dibuat Tanggal        :     11/07/2013
Update Terakhir        :    01/07/2014
Revisi                :    
    SID 01/07/2014    :     - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
                          pada function save, saveTemp, delete
                        - perubahan query pengambilan data di getData & getDataDownload
=========================================================================================================================
*/
class Application_Model_RktVra
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_formula = new Application_Model_Formula();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
        $this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
        $this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
        
        $sess = new Zend_Session_Namespace('period');
        $this->_period = $sess->period;
    }
    
    //setting input untuk region
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
    
    //ambil data dari DB
    public function getData($params = array())
    {
    
        $query = "
            SELECT ROWNUM, RKT.*, 
                 standar_jam_kerja.JAM_KERJA as STANDAR_JAM_KERJA_WRA,
                (SELECT SUM(CR.MPP_PERIOD_BUDGET) SUM_MPP
                 FROM TR_RKT_CHECKROLL CR
                 WHERE CR.JOB_CODE IN ( SELECT DISTINCT JOB_CODE FROM TM_MAPPING_JOB_TYPE_VRA WHERE RVRA_CODE = 'R550101010101')
                 AND to_char(CR.PERIOD_BUDGET,'RRRR') = RKT.PERIOD_BUDGET
                 AND CR.BA_CODE = RKT.BA_CODE) as TOTAL_MPP_OPERATOR,
                (SELECT SUM(CR.MPP_PERIOD_BUDGET) SUM_MPP
                 FROM TR_RKT_CHECKROLL CR
                 WHERE CR.JOB_CODE IN ( SELECT DISTINCT JOB_CODE FROM TM_MAPPING_JOB_TYPE_VRA WHERE RVRA_CODE = 'R550101010102')
                 AND to_char(CR.PERIOD_BUDGET,'RRRR') = RKT.PERIOD_BUDGET
                 AND CR.BA_CODE = RKT.BA_CODE) as TOTAL_MPP_HELPER            
                FROM (
                    SELECT ROWIDTOCHAR (report.ROWID) row_id,
                           to_char(report.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
                           report.INTERNAL_ORDER,
                           report.KOMPARISON_OUT_HM_KM,
                           report.RP_QTY_BULAN_BUDGET,
                           report.TRX_RKT_VRA_CODE,
                           ORG.REGION_CODE,
                           report.BA_CODE, 
                           master_vra.VRA_SUB_CAT_DESCRIPTION,
                           master_vra.VRA_CODE,
                           master_vra.TYPE VRA_TYPE,
                           report.DESCRIPTION_VRA DESCRIPTION_VRA,
                           report.JUMLAH_ALAT,
                           report.TAHUN_ALAT,
                           master_vra.UOM,
                           report.QTY_DAY, 
                           report.DAY_YEAR_VRA, 
                           report.QTY_YEAR, 
                           report.TOTAL_BIAYA,
                           report.TOTAL_RP_QTY,
                           report.TOTAL_QTY_TAHUN,
                           report.JUMLAH_OPERATOR, 
                           report.GAJI_OPERATOR, 
                           report.TOTAL_GAJI_OPERATOR, 
                           report.TUNJANGAN_OPERATOR, 
                           report.TOTAL_TUNJANGAN_OPERATOR, 
                           report.TOTAL_GAJI_TUNJANGAN_OPERATOR, 
                           report.RP_QTY_OPERATOR, 
                           report.JUMLAH_HELPER, 
                           report.GAJI_HELPER, 
                           report.TOTAL_GAJI_HELPER, 
                           report.TUNJANGAN_HELPER, 
                           report.TOTAL_TUNJANGAN_HELPER, 
                           report.TOTAL_GAJI_TUNJANGAN_HELPER, 
                           report.RP_QTY_HELPER,
                           report.RVRA1_VALUE1, -- PAJAK
                           report.RVRA1_VALUE2,
                           report.RVRA1_VALUE3,
                           report.RVRA2_VALUE1, -- BAHAN BAKAR
                           report.RVRA2_VALUE2,
                           report.RVRA2_VALUE3,
                           report.RVRA3_VALUE1, -- OLI MESIN
                           report.RVRA3_VALUE2,
                           report.RVRA3_VALUE3,
                           report.RVRA4_VALUE1, -- OLI TRANSMISI
                           report.RVRA4_VALUE2,
                           report.RVRA4_VALUE3,
                           report.RVRA5_VALUE1, -- MINYAK HIDROLIC
                           report.RVRA5_VALUE2,
                           report.RVRA5_VALUE3,
                           report.RVRA6_VALUE1, -- GREASE
                           report.RVRA6_VALUE2,
                           report.RVRA6_VALUE3,
                           report.RVRA7_VALUE1, -- FILTER OLI
                           report.RVRA7_VALUE2,
                           report.RVRA7_VALUE3,
                           report.RVRA8_VALUE1, -- FILTER HIDROLIC
                           report.RVRA8_VALUE2,
                           report.RVRA8_VALUE3,
                           report.RVRA9_VALUE1, -- FILTER SOLAR
                           report.RVRA9_VALUE2,
                           report.RVRA9_VALUE3,
                           report.RVRA10_VALUE1, -- FILTER SOLAR MOISTURE SEPARATOR
                           report.RVRA10_VALUE2,
                           report.RVRA10_VALUE3,
                           report.RVRA11_VALUE1, -- FILTER UDARA
                           report.RVRA11_VALUE2,
                           report.RVRA11_VALUE3,
                           report.RVRA12_VALUE1, -- GANTI SPAREPART
                           report.RVRA12_VALUE2,
                           report.RVRA12_VALUE3,
                           report.RVRA13_VALUE1, --  GANTI BAN LUAR
                           report.RVRA13_VALUE2,
                           report.RVRA13_VALUE3,
                           report.RVRA14_VALUE1, -- GANTI BAN DALAM
                           report.RVRA14_VALUE2,
                           report.RVRA14_VALUE3,
                           report.RVRA15_VALUE1, -- SERVIS WORKSHOP
                           report.RVRA15_VALUE2,
                           report.RVRA15_VALUE3,
                           report.RVRA16_VALUE1, -- OVERHAUL
                           report.RVRA16_VALUE2,
                           report.RVRA16_VALUE3,
                           report.RVRA17_VALUE1, -- RENTAL
                           report.RVRA17_VALUE2,
                           report.RVRA17_VALUE3,
                           report.RVRA18_VALUE1, -- SERVIS BENGKEL LUAR
                           report.RVRA18_VALUE2,
                           report.RVRA18_VALUE3,
                           report.RVRA19_VALUE1,
                           report.RVRA19_VALUE2,
                           report.RVRA19_VALUE3,
                           report.RVRA20_VALUE1,
                           report.RVRA20_VALUE2,
                           report.RVRA20_VALUE3,
                           vra_sum.VALUE RP_QTY_VRA_TYPE,
                           report.FLAG_TEMP
                    FROM TR_RKT_VRA report
                    LEFT JOIN TM_VRA master_vra
                        ON report.VRA_CODE = master_vra.VRA_CODE
                    LEFT JOIN TR_RKT_VRA_SUM vra_sum
                        ON report.BA_CODE = vra_sum.BA_CODE
                        AND report.PERIOD_BUDGET = vra_sum.PERIOD_BUDGET
                        AND report.VRA_CODE = vra_sum.VRA_CODE
                    LEFT JOIN TM_ORGANIZATION ORG
                        ON report.BA_CODE = ORG.BA_CODE
                    WHERE report.DELETE_USER IS NULL
                ) RKT
            LEFT JOIN TM_STANDART_JAM_KERJA_WRA standar_jam_kerja
                ON to_char(standar_jam_kerja.PERIOD_BUDGET,'RRRR') = RKT.PERIOD_BUDGET
                AND standar_jam_kerja.BA_CODE = RKT.BA_CODE
            WHERE     1 = 1
        ";
        
        if($this->_siteCode <> 'ALL'){
            if ($this->_referenceRole == 'REGION_CODE')
                $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RKT.REGION_CODE)||'%'";
            elseif ($this->_referenceRole == 'BA_CODE')
                $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(RKT.BA_CODE)||'%'";
        }
        if($params['budgetperiod'] != ''){
            $query .= "
                AND rkt.PERIOD_BUDGET = '".$params['budgetperiod']."'
            ";
        }elseif($params['PERIOD_BUDGET'] != ''){
            $query .= "
                AND rkt.PERIOD_BUDGET = '".$params['PERIOD_BUDGET']."'
            ";
        }else{
            $query .= "
                AND rkt.PERIOD_BUDGET = TO_CHAR(TO_DATE ('".$this->_period."', 'DD-MM-RRRR'),'RRRR')
            ";
        }
        
        //filter region
        if ($params['src_region_code'] != '') {
            $query .= "
                AND UPPER(RKT.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
        
        if ($params['key_find'] != '') { 
            $query .= "
                AND UPPER(RKT.BA_CODE) IN ('".$params['key_find']."')
            ";
        }
        
        if ($params['vra_code'] != '') {
            $query .= "
                AND rkt.VRA_CODE IN ('".$params['vra_code']."')
            ";
        }
        
        if ($params['min_year'] != '' && $params['max_year'] != '') {
            $query .= "
                AND (TO_CHAR(TO_DATE ('01-01-{$params['PERIOD_BUDGET']}', 'DD-MM-RRRR'),'RRRR') - RKT.TAHUN_ALAT) BETWEEN '".$params['min_year']."' AND '".$params['max_year']."'
            ";
        }
        
        if ($params['ba_code'] != '') {
            $query .= "
                AND UPPER(rkt.BA_CODE) IN ('".$params['ba_code']."')
            ";
        }
        
        //jika diupdate dari norma VRA, filter berdasarkan BA
        if ($params['BA_CODE'] != '') {
            $query .= "
                AND UPPER(rkt.BA_CODE) LIKE UPPER('%".$params['BA_CODE']."%') 
            ";
        }
        
        //jika diupdate dari norma VRA, filter berdasarkan VRA CODE
        if ($params['VRA_CODE'] != '') {
            $query .= "
                AND UPPER(rkt.VRA_CODE) IN ('".$params['VRA_CODE']."')
            ";
        }
        
        $query .= "
            ORDER BY rkt.BA_CODE, RKT.VRA_SUB_CAT_DESCRIPTION, RKT.VRA_CODE
        ";

        return $query;
    }
    
    //ambil data untuk didownload ke excel dari DB
    public function getDataDownload($params = array())
    {
        $query = "
            SELECT ROWIDTOCHAR(report.ROWID) row_id, rownum, 
                   to_char(report.PERIOD_BUDGET,'RRRR') PERIOD_BUDGET, 
                   report.BA_CODE,
                   report.INTERNAL_ORDER,
                   report.KOMPARISON_OUT_HM_KM,
                   report.RP_QTY_BULAN_BUDGET,
                   ORG.COMPANY_NAME,
                   master_vra.VRA_SUB_CAT_DESCRIPTION,
                   master_vra.VRA_CODE,
                   master_vra.TYPE VRA_TYPE,
                   report.DESCRIPTION_VRA DESCRIPTION_VRA,
                   report.JUMLAH_ALAT,
                   report.TAHUN_ALAT,
                   master_vra.UOM,
                   report.QTY_DAY, 
                   report.DAY_YEAR_VRA, 
                   report.QTY_YEAR, 
                   report.TOTAL_BIAYA,
                   report.TOTAL_RP_QTY,
                   report.TOTAL_QTY_TAHUN,
                   report.JUMLAH_OPERATOR, 
                   report.GAJI_OPERATOR, 
                   report.TOTAL_GAJI_OPERATOR, 
                   report.TUNJANGAN_OPERATOR, 
                   report.TOTAL_TUNJANGAN_OPERATOR, 
                   report.TOTAL_GAJI_TUNJANGAN_OPERATOR, 
                   report.RP_QTY_OPERATOR, 
                   report.JUMLAH_HELPER, 
                   report.GAJI_HELPER, 
                   report.TOTAL_GAJI_HELPER, 
                   report.TUNJANGAN_HELPER, 
                   report.TOTAL_TUNJANGAN_HELPER, 
                   report.TOTAL_GAJI_TUNJANGAN_HELPER, 
                   report.RP_QTY_HELPER,
                   report.RVRA1_VALUE1, -- PAJAK
                   report.RVRA1_VALUE2,
                   report.RVRA1_VALUE3,
                   report.RVRA2_VALUE1, -- BAHAN BAKAR
                   report.RVRA2_VALUE2,
                   report.RVRA2_VALUE3,
                   report.RVRA3_VALUE1, -- OLI MESIN
                   report.RVRA3_VALUE2,
                   report.RVRA3_VALUE3,
                   report.RVRA4_VALUE1, -- OLI TRANSMISI
                   report.RVRA4_VALUE2,
                   report.RVRA4_VALUE3,
                   report.RVRA5_VALUE1, -- MINYAK HIDROLIC
                   report.RVRA5_VALUE2,
                   report.RVRA5_VALUE3,
                   report.RVRA6_VALUE1, -- GREASE
                   report.RVRA6_VALUE2,
                   report.RVRA6_VALUE3,
                   report.RVRA7_VALUE1, -- FILTER OLI
                   report.RVRA7_VALUE2,
                   report.RVRA7_VALUE3,
                   report.RVRA8_VALUE1, -- FILTER HIDROLIC
                   report.RVRA8_VALUE2,
                   report.RVRA8_VALUE3,
                   report.RVRA9_VALUE1, -- FILTER SOLAR
                   report.RVRA9_VALUE2,
                   report.RVRA9_VALUE3,
                   report.RVRA10_VALUE1, -- FILTER SOLAR MOISTURE SEPARATOR
                   report.RVRA10_VALUE2,
                   report.RVRA10_VALUE3,
                   report.RVRA11_VALUE1, -- FILTER UDARA
                   report.RVRA11_VALUE2,
                   report.RVRA11_VALUE3,
                   report.RVRA12_VALUE1, -- GANTI SPAREPART
                   report.RVRA12_VALUE2,
                   report.RVRA12_VALUE3,
                   report.RVRA13_VALUE1, --  GANTI BAN LUAR
                   report.RVRA13_VALUE2,
                   report.RVRA13_VALUE3,
                   report.RVRA14_VALUE1, -- GANTI BAN DALAM
                   report.RVRA14_VALUE2,
                   report.RVRA14_VALUE3,
                   report.RVRA15_VALUE1, -- SERVIS WORKSHOP
                   report.RVRA15_VALUE2,
                   report.RVRA15_VALUE3,
                   report.RVRA16_VALUE1, -- OVERHAUL
                   report.RVRA16_VALUE2,
                   report.RVRA16_VALUE3,
                   report.RVRA17_VALUE1, -- RENTAL
                   report.RVRA17_VALUE2,
                   report.RVRA17_VALUE3,
                   report.RVRA18_VALUE1, -- SERVIS BENGKEL LUAR
                   report.RVRA18_VALUE2,
                   report.RVRA18_VALUE3,
                   report.RVRA19_VALUE1,
                   report.RVRA19_VALUE2,
                   report.RVRA19_VALUE3,
                   report.RVRA20_VALUE1,
                   report.RVRA20_VALUE2,
                   report.RVRA20_VALUE3,
                   vra_sum.VALUE RP_QTY_VRA_TYPE
            FROM TR_RKT_VRA report
            LEFT JOIN TM_VRA master_vra
                ON report.VRA_CODE = master_vra.VRA_CODE
            LEFT JOIN TR_RKT_VRA_SUM vra_sum
                ON report.BA_CODE = vra_sum.BA_CODE
                AND report.PERIOD_BUDGET = vra_sum.PERIOD_BUDGET
                AND report.VRA_CODE = vra_sum.VRA_CODE
            LEFT JOIN TM_ORGANIZATION ORG
                ON report.BA_CODE = ORG.BA_CODE
            WHERE report.DELETE_USER IS NULL
        ";
        
        if($this->_siteCode <> 'ALL'){
            if ($this->_referenceRole == 'REGION_CODE')
                $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(ORG.REGION_CODE)||'%'";
            elseif ($this->_referenceRole == 'BA_CODE')
                $query .= "AND UPPER('".$this->_siteCode."') LIKE '%'||UPPER(REPORT.BA_CODE)||'%'";
        }
        if($params['budgetperiod'] != ''){
            $query .= "
                AND to_char(report.PERIOD_BUDGET,'RRRR') = '".$params['budgetperiod']."'
            ";
        }elseif($params['PERIOD_BUDGET'] != ''){
            $query .= "
                AND to_char(report.PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
            ";
        }else{
            $query .= "
                AND to_char(report.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
            ";
        }
        
        //filter region
        if ($params['src_region_code'] != '') {
            $query .= "
                AND UPPER(ORG.REGION_CODE) LIKE UPPER('%".$params['src_region_code']."%')
            ";
        }
        
        if ($params['key_find'] != '') {
            $query .= "
                AND UPPER(report.BA_CODE) LIKE UPPER('%".$params['key_find']."%')
            ";
        }
        
        if ($params['vra_code'] != '') {
            $query .= "
                AND UPPER(report.VRA_CODE) LIKE UPPER('%".$params['vra_code']."%')
            ";
        }
        
        $query .= "
            ORDER BY master_vra.VRA_SUB_CAT_DESCRIPTION, master_vra.VRA_CODE
        ";
        
        return $query;
    }
    
    //menampilkan list RKT VRA
    public function getList($params = array())
    {
      $getParams = $this->getData($params);

        $result = array();

        $begin = "
            SELECT * FROM (SELECT ROWNUM MY_ROWNUM, MY_TABLE.*
            FROM (SELECT TEMP.*
            FROM (
        ";
        $min = (intval($params['page_num']) - 1) * intval($params['page_rows']);
        $max = $min + intval($params['page_rows']);
        $end = "
            ) TEMP
            ) MY_TABLE
              WHERE ROWNUM <= {$max}
            ) WHERE MY_ROWNUM > {$min}
        ";
        
        $sql = "SELECT COUNT(*) FROM ({$getParams})";
        $result['count'] = $this->_db->fetchOne($sql);

        $rows = $this->_db->fetchAll("{$begin} {$getParams} {$end}");
    
    if (!empty($rows)) {
            foreach ($rows as $idx => $row) {
        $result['rows'][] = $row;
            }
        }

        return $result;
    }
    
    //menampilkan list tunjangan
    public function getTunjangan()
    {
        $sql = "
            SELECT TUNJANGAN_TYPE
            FROM TM_TUNJANGAN
            WHERE DELETE_USER IS NULL
                AND FLAG_RP_HK = 'YES'
            ORDER BY TUNJANGAN_TYPE
        ";
        $result = $this->_db->fetchAll($sql);

        return $result;
    }
    
    //menampilkan list pk umum
    public function getPkUmum()
    {
        $sql = "
            SELECT TUNJANGAN_TYPE
            FROM TM_TUNJANGAN
            WHERE DELETE_USER IS NULL
                AND FLAG_RP_HK = 'NO'
            ORDER BY TUNJANGAN_TYPE
        ";
        $result = $this->_db->fetchAll($sql);

        return $result;
    }
    
    //simpan data
    public function save($row = array())
    {
        $standar_jam_kerja = $this->_formula->cal_RktVra_PerincianStandarJamKerja($row);
        $row['TOTAL_QTY_TAHUN'] = $standar_jam_kerja['TOTAL_QTY_TAHUN'];
        $tenaga_kerja = $this->_formula->cal_RktVra_PerincianTenagaKerja($row);
        $row['RP_QTY_OPERATOR'] = $tenaga_kerja['RP_QTY_OPERATOR'];
        $row['RP_QTY_HELPER'] = $tenaga_kerja['RP_QTY_HELPER'];
        $row['TOTAL_GAJI_TUNJANGAN_OPERATOR'] = $tenaga_kerja['TOTAL_GAJI_TUNJANGAN_OPERATOR'];
        $row['TOTAL_GAJI_TUNJANGAN_HELPER'] = $tenaga_kerja['TOTAL_GAJI_TUNJANGAN_HELPER'];
        $rvra = $this->_formula->cal_RktVra_PerincianRvra($row);


        $sql = "
            UPDATE TR_RKT_VRA
            SET QTY_YEAR = REPLACE('{$standar_jam_kerja['QTY_YEAR']}', ',', ''),
                TOTAL_QTY_TAHUN = REPLACE('{$standar_jam_kerja['TOTAL_QTY_TAHUN']}', ',', ''),
                TOTAL_BIAYA = REPLACE('{$rvra['TOTAL_BIAYA']}', ',', ''), 
                TOTAL_RP_QTY = REPLACE('{$rvra['TOTAL_RP_QTY']}', ',', ''), 
                COST_SETAHUN = REPLACE('{$rvra['TOTAL_BIAYA']}', ',', ''), 
                GAJI_OPERATOR = REPLACE('{$tenaga_kerja['GAJI_OPERATOR']}', ',', ''), 
                TOTAL_GAJI_OPERATOR = REPLACE('{$tenaga_kerja['TOTAL_GAJI_OPERATOR']}', ',', ''), 
                TUNJANGAN_OPERATOR = REPLACE('{$tenaga_kerja['TUNJANGAN_OPERATOR']}', ',', ''), 
                TOTAL_TUNJANGAN_OPERATOR = REPLACE('{$tenaga_kerja['TOTAL_TUNJANGAN_OPERATOR']}', ',', ''), 
                TOTAL_GAJI_TUNJANGAN_OPERATOR = REPLACE('{$tenaga_kerja['TOTAL_GAJI_TUNJANGAN_OPERATOR']}', ',', ''), 
                RP_QTY_OPERATOR = REPLACE('{$tenaga_kerja['RP_QTY_OPERATOR']}', ',', ''),
                GAJI_HELPER = REPLACE('{$tenaga_kerja['GAJI_HELPER']}', ',', ''),  
                TOTAL_GAJI_HELPER = REPLACE('{$tenaga_kerja['TOTAL_GAJI_HELPER']}', ',', ''), 
                TUNJANGAN_HELPER = REPLACE('{$tenaga_kerja['TUNJANGAN_HELPER']}', ',', ''), 
                TOTAL_TUNJANGAN_HELPER = REPLACE('{$tenaga_kerja['TOTAL_TUNJANGAN_HELPER']}', ',', ''), 
                TOTAL_GAJI_TUNJANGAN_HELPER = REPLACE('{$tenaga_kerja['TOTAL_GAJI_TUNJANGAN_HELPER']}', ',', ''), 
                RP_QTY_HELPER = REPLACE('{$tenaga_kerja['RP_QTY_HELPER']}', ',', ''),     
                RVRA1_CODE = '{$rvra['RVRA1_CODE']}', 
                RVRA1_VALUE1 = REPLACE('{$rvra['RVRA1_VALUE1']}', ',', ''), 
                RVRA1_VALUE3 = REPLACE('{$rvra['RVRA1_VALUE3']}', ',', ''), 
                RVRA2_CODE = '{$rvra['RVRA2_CODE']}', 
                RVRA2_VALUE1 = REPLACE('{$rvra['RVRA2_VALUE1']}', ',', ''), 
                RVRA2_VALUE2 = REPLACE('{$rvra['RVRA2_VALUE2']}', ',', ''), 
                RVRA2_VALUE3 = REPLACE('{$rvra['RVRA2_VALUE3']}', ',', ''), 
                RVRA3_CODE = '{$rvra['RVRA3_CODE']}', 
                RVRA3_VALUE1 = REPLACE('{$rvra['RVRA3_VALUE1']}', ',', ''), 
                RVRA3_VALUE2 = REPLACE('{$rvra['RVRA3_VALUE2']}', ',', ''), 
                RVRA3_VALUE3 = REPLACE('{$rvra['RVRA3_VALUE3']}', ',', ''), 
                RVRA4_CODE = '{$rvra['RVRA4_CODE']}', 
                RVRA4_VALUE1 = REPLACE('{$rvra['RVRA4_VALUE1']}', ',', ''), 
                RVRA4_VALUE2 = REPLACE('{$rvra['RVRA4_VALUE2']}', ',', ''), 
                RVRA4_VALUE3 = REPLACE('{$rvra['RVRA4_VALUE3']}', ',', ''), 
                RVRA5_CODE = '{$rvra['RVRA5_CODE']}', 
                RVRA5_VALUE1 = REPLACE('{$rvra['RVRA5_VALUE1']}', ',', ''), 
                RVRA5_VALUE2 = REPLACE('{$rvra['RVRA5_VALUE2']}', ',', ''), 
                RVRA5_VALUE3 = REPLACE('{$rvra['RVRA5_VALUE3']}', ',', ''), 
                RVRA6_CODE = '{$rvra['RVRA6_CODE']}', 
                RVRA6_VALUE1 = REPLACE('{$rvra['RVRA6_VALUE1']}', ',', ''), 
                RVRA6_VALUE2 = REPLACE('{$rvra['RVRA6_VALUE2']}', ',', ''), 
                RVRA6_VALUE3 = REPLACE('{$rvra['RVRA6_VALUE3']}', ',', ''), 
                RVRA7_CODE = '{$rvra['RVRA7_CODE']}', 
                RVRA7_VALUE1 = REPLACE('{$rvra['RVRA7_VALUE1']}', ',', ''), 
                RVRA7_VALUE2 = REPLACE('{$rvra['RVRA7_VALUE2']}', ',', ''), 
                RVRA7_VALUE3 = REPLACE('{$rvra['RVRA7_VALUE3']}', ',', ''), 
                RVRA8_CODE = '{$rvra['RVRA8_CODE']}', 
                RVRA8_VALUE1 = REPLACE('{$rvra['RVRA8_VALUE1']}', ',', ''), 
                RVRA8_VALUE2 = REPLACE('{$rvra['RVRA8_VALUE2']}', ',', ''), 
                RVRA8_VALUE3 = REPLACE('{$rvra['RVRA8_VALUE3']}', ',', ''), 
                RVRA9_CODE = '{$rvra['RVRA9_CODE']}', 
                RVRA9_VALUE1 = REPLACE('{$rvra['RVRA9_VALUE1']}', ',', ''), 
                RVRA9_VALUE2 = REPLACE('{$rvra['RVRA9_VALUE2']}', ',', ''), 
                RVRA9_VALUE3 = REPLACE('{$rvra['RVRA9_VALUE3']}', ',', ''), 
                RVRA10_CODE = '{$rvra['RVRA10_CODE']}', 
                RVRA10_VALUE1 = REPLACE('{$rvra['RVRA10_VALUE1']}', ',', ''), 
                RVRA10_VALUE2 = REPLACE('{$rvra['RVRA10_VALUE2']}', ',', ''), 
                RVRA10_VALUE3 = REPLACE('{$rvra['RVRA10_VALUE3']}', ',', ''), 
                RVRA11_CODE = '{$rvra['RVRA11_CODE']}', 
                RVRA11_VALUE1 = REPLACE('{$rvra['RVRA11_VALUE1']}', ',', ''), 
                RVRA11_VALUE2 = REPLACE('{$rvra['RVRA11_VALUE2']}', ',', ''), 
                RVRA11_VALUE3 = REPLACE('{$rvra['RVRA11_VALUE3']}', ',', ''), 
                RVRA12_CODE = '{$rvra['RVRA12_CODE']}', 
                RVRA12_VALUE1 = REPLACE('{$rvra['RVRA12_VALUE1']}', ',', ''), 
                RVRA12_VALUE3 = REPLACE('{$rvra['RVRA12_VALUE3']}', ',', ''), 
                RVRA13_CODE = '{$rvra['RVRA13_CODE']}', 
                RVRA13_VALUE1 = REPLACE('{$rvra['RVRA13_VALUE1']}', ',', ''), 
                RVRA13_VALUE2 = REPLACE('{$rvra['RVRA13_VALUE2']}', ',', ''), 
                RVRA13_VALUE3 = REPLACE('{$rvra['RVRA13_VALUE3']}', ',', ''), 
                RVRA14_CODE = '{$rvra['RVRA14_CODE']}', 
                RVRA14_VALUE1 = REPLACE('{$rvra['RVRA14_VALUE1']}', ',', ''), 
                RVRA14_VALUE2 = REPLACE('{$rvra['RVRA14_VALUE2']}', ',', ''), 
                RVRA14_VALUE3 = REPLACE('{$rvra['RVRA14_VALUE3']}', ',', ''), 
                RVRA15_CODE = '{$rvra['RVRA15_CODE']}', 
                RVRA15_VALUE2 = REPLACE('{$rvra['RVRA15_VALUE2']}', ',', ''), 
                RVRA15_VALUE3 = REPLACE('{$rvra['RVRA15_VALUE3']}', ',', ''), 
                RVRA16_CODE = '{$rvra['RVRA16_CODE']}', 
                RVRA16_VALUE1 = REPLACE('{$rvra['RVRA16_VALUE1']}', ',', ''), 
                RVRA16_VALUE3 = REPLACE('{$rvra['RVRA16_VALUE3']}', ',', ''), 
                RVRA17_CODE = '{$rvra['RVRA17_CODE']}', 
                RVRA17_VALUE3 = REPLACE('{$rvra['RVRA17_VALUE3']}', ',', ''),
                RVRA18_CODE = '{$rvra['RVRA18_CODE']}', 
                RVRA18_VALUE1 = REPLACE('{$rvra['RVRA18_VALUE1']}', ',', ''), 
                RVRA18_VALUE3 = REPLACE('{$rvra['RVRA18_VALUE3']}', ',', ''), 
                RVRA19_CODE = '{$rvra['RVRA19_CODE']}', 
                RVRA19_VALUE1 = REPLACE('{$rvra['RVRA19_VALUE1']}', ',', ''), 
                RVRA19_VALUE2 = REPLACE('{$rvra['RVRA19_VALUE2']}', ',', ''), 
                RVRA19_VALUE3 = REPLACE('{$rvra['RVRA19_VALUE3']}', ',', ''), 
                RVRA20_CODE = '{$rvra['RVRA20_CODE']}',  
                RVRA20_VALUE1 = REPLACE('{$rvra['RVRA20_VALUE1']}', ',', ''), 
                RVRA20_VALUE2 = REPLACE('{$rvra['RVRA20_VALUE2']}', ',', ''), 
                RVRA20_VALUE3 = REPLACE('{$rvra['RVRA20_VALUE3']}', ',', ''), 
                FLAG_TEMP = NULL , 
                UPDATE_USER = '{$this->_userName}',
                UPDATE_TIME = SYSDATE,
                INTERNAL_ORDER = '".addslashes($row['INTERNAL_ORDER'])."',
                KOMPARISON_OUT_HM_KM = '".addslashes($row['KOMPARISON_OUT_HM_KM'])."',
                RP_QTY_BULAN_BUDGET = '".addslashes($row['RP_QTY_BULAN_BUDGET'])."'
            WHERE TRX_RKT_VRA_CODE = '".addslashes($row['TRX_RKT_VRA_CODE'])."';
        ";

        $this->_global->createSqlFile($row['filename'], $sql);        
        return true;
    }
    
    //hapus data di RKT VRA, insert inputan user di RKT VRA
    public function saveTemp($row = array())
    { 

        $result = true;        
        
        $delVra = ($row['VRA_CODE']!='') ? $row['VRA_CODE'] : '--';
        $delVraType = ($row['DESCRIPTION_VRA']!='') ? $row['DESCRIPTION_VRA'] : '--';
        //$delVraType = addslashes($delVraType);
        $delInternalOrder = ($row['INTERNAL_ORDER']!='') ? $row['INTERNAL_ORDER'] : '--';
        
        $delOldVra = ($row['OLD_VRA_CODE']!='') ? $row['OLD_VRA_CODE'] : '--';
        $delOldVraType = ($row['OLD_DESCRIPTION_VRA']!='') ? $row['OLD_DESCRIPTION_VRA'] : '--';
        $delOldInternalOrder = ($row['OLD_INTERNAL_ORDER']!='') ? $row['OLD_INTERNAL_ORDER'] : '--';
        
        //delete data berdasarkan kombinasi PK yg baru
        $sql = "
            DELETE FROM TR_RKT_VRA
            WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
                AND BA_CODE = '{$row['BA_CODE']}'
                AND NVL(VRA_CODE ,'--') = '{$delVra}' 
                AND NVL(DESCRIPTION_VRA,'--')  = '{$delVraType}'
                AND NVL(INTERNAL_ORDER,'--')  = '{$delInternalOrder}'
                AND TAHUN_ALAT  = '{$row['TAHUN_ALAT']}';
        ";
        
        //delete data berdasarkan kombinasi PK yg lama
        $sql.= "
            DELETE FROM TR_RKT_VRA
            WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
                AND BA_CODE = '{$row['BA_CODE']}'
                AND NVL(VRA_CODE ,'--') = '{$delOldVra}' 
                AND NVL(DESCRIPTION_VRA,'--')  = '{$delOldVraType}'
                AND NVL(INTERNAL_ORDER,'--')  = '{$delOldInternalOrder}'
                AND TAHUN_ALAT = '{$row['OLD_TAHUN_ALAT']}';
        ";
        
        //insert data input baru sebagai temp data
        $trx_code = $row['PERIOD_BUDGET'] ."-".
                    addslashes($row['BA_CODE']) ."-RKT021-".
                    addslashes($row['VRA_CODE']) ."-".
                    $this->_global->randomString(10);
                    
        $sql.= "
            INSERT INTO TR_RKT_VRA (
                TRX_RKT_VRA_CODE, 
                PERIOD_BUDGET, 
                BA_CODE, 
                VRA_CODE, 
                DESCRIPTION_VRA, 
                JUMLAH_ALAT, 
                TAHUN_ALAT, 
                QTY_DAY, 
                DAY_YEAR_VRA, 
                JUMLAH_OPERATOR,
                JUMLAH_HELPER, 
                RVRA1_VALUE2, 
                RVRA17_VALUE1, 
                RVRA17_VALUE2, 
                RVRA12_VALUE2, 
                RVRA16_VALUE2, 
                RVRA15_VALUE1, 
                RVRA18_VALUE2, 
                FLAG_TEMP, 
                INSERT_USER, 
                INSERT_TIME,
                INTERNAL_ORDER,
                KOMPARISON_OUT_HM_KM,
                RP_QTY_BULAN_BUDGET
            )
            VALUES (
                '".$trx_code."',
                TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),        
                '".addslashes($row['BA_CODE'])."',
                '".addslashes($row['VRA_CODE'])."',
                '".addslashes($row['DESCRIPTION_VRA'])."',
                REPLACE('".addslashes($row['JUMLAH_ALAT'])."',',',''), 
                REPLACE('".addslashes($row['TAHUN_ALAT'])."',',',''), 
                REPLACE('".addslashes($row['QTY_DAY'])."',',',''), 
                REPLACE('".addslashes($row['DAY_YEAR_VRA'])."',',',''), 
                REPLACE('".addslashes($row['JUMLAH_OPERATOR'])."',',',''), 
                REPLACE('".addslashes($row['JUMLAH_HELPER'])."',',',''), 
                REPLACE('".addslashes($row['RVRA1_VALUE2'])."',',',''), 
                REPLACE('".addslashes($row['RVRA17_VALUE1'])."',',',''), 
                REPLACE('".addslashes($row['RVRA17_VALUE2'])."',',',''), 
                REPLACE('".addslashes($row['RVRA12_VALUE2'])."',',',''), 
                REPLACE('".addslashes($row['RVRA16_VALUE2'])."',',',''), 
                REPLACE('".addslashes($row['RVRA15_VALUE1'])."',',',''), 
                REPLACE('".addslashes($row['RVRA18_VALUE2'])."',',',''),
                REPLACE('".addslashes($row['CHANGE'])."',',',''),
                '{$this->_userName}',
                SYSDATE,
                '".addslashes($row['INTERNAL_ORDER'])."',
                REPLACE('".addslashes($row['KOMPARISON_OUT_HM_KM'])."',',',''),
                REPLACE('".addslashes($row['RP_QTY_BULAN_BUDGET'])."',',','')
            );
        ";
                    
        //create sql file
        $this->_global->createSqlFile($row['filename'], $sql);        
        return true;
    }
    
    //hapus data
    public function delete($row = array())
    {
         $result = true;        
        
        $delVra = ($row['VRA_CODE']!='') ? $row['VRA_CODE'] : '--';
        $delVraType = ($row['DESCRIPTION_VRA']!='') ? $row['DESCRIPTION_VRA'] : '--';
        $delInternalOrder = ($row['INTERNAL_ORDER']!='') ? $row['INTERNAL_ORDER'] : '--';
        
        $delOldVra = ($row['OLD_VRA_CODE']!='') ? $row['OLD_VRA_CODE'] : '--';
        $delOldVraType = ($row['OLD_DESCRIPTION_VRA']!='') ? $row['OLD_DESCRIPTION_VRA'] : '--';
        $delOldInternalOrder = ($row['OLD_INTERNAL_ORDER']!='') ? $row['OLD_INTERNAL_ORDER'] : '--';
        
        //delete data berdasarkan kombinasi PK yg lama
        $sql = "
            UPDATE TR_RKT_VRA
            SET DELETE_USER = '{$this->_userName}',
                DELETE_TIME = SYSDATE
            WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
                AND BA_CODE = '{$row['BA_CODE']}'
                AND NVL(VRA_CODE ,'--') = '{$delOldVra}' 
                AND NVL(DESCRIPTION_VRA,'--')  = '{$delOldVraType}'
                AND NVL(INTERNAL_ORDER,'--')  = '{$delOldInternalOrder}'
                AND TAHUN_ALAT = '{$row['TAHUN_TANAM']}';
        ";
        
        /*
        //delete data berdasarkan kombinasi PK yg baru
        $sql .= "
            UPDATE TR_RKT_VRA
            SET DELETE_USER = '{$this->_userName}',
                DELETE_TIME = SYSDATE
            WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
                AND BA_CODE = '{$row['BA_CODE']}'
                AND NVL(VRA_CODE ,'--') = '{$delVra}' 
                AND NVL(DESCRIPTION_VRA,'--')  = '{$delVraType}';
        ";
        */
        
        //create sql file
        $this->_global->createSqlFile($row['filename'], $sql);        
        return true;
    }
    
    //update summary RKT VRA
    public function updateSummaryRktVra($row = array())
    {
        //delete data lama
        $sql = "
            DELETE FROM TR_RKT_VRA_SUM
            WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
                AND BA_CODE = '{$row['BA_CODE']}'
                AND VRA_CODE = '".addslashes($row['VRA_CODE'])."';
        ";
        
        ///////////////////////////// SUMMARY RKT VRA /////////////////////////////
        $query = "
            SELECT SUM(TOTAL_BIAYA) TOTAL_BIAYA, SUM(TOTAL_QTY_TAHUN) TOTAL_QTY_TAHUN
            FROM TR_RKT_VRA
            WHERE VRA_CODE = '".addslashes($row['VRA_CODE'])."'
                AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$row['PERIOD_BUDGET']}'
                AND BA_CODE = '".addslashes($row['BA_CODE'])."'
                AND DELETE_USER IS NULL
                AND FLAG_TEMP IS NULL
        ";
        $hasil = $this->_db->fetchRow($query);
        
        //rp/qty
        $rp_qty = ($hasil['TOTAL_QTY_TAHUN']) ? $hasil['TOTAL_BIAYA'] / $hasil['TOTAL_QTY_TAHUN'] : 0;
        $row['RP_QTY'] = $rp_qty;
        
        $sql .= "
            INSERT INTO TR_RKT_VRA_SUM (PERIOD_BUDGET, BA_CODE, VRA_CODE, VALUE, INSERT_USER, INSERT_TIME)
            VALUES (
                TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR'),
                '".addslashes($row['BA_CODE'])."',
                '".addslashes($row['VRA_CODE'])."',
                '".$rp_qty."',
                '{$this->_userName}',
                SYSDATE
            );
        ";
        
        //create sql file
        $this->_global->createSqlFile($row['filename'], $sql);        
        return true;    
    }
}

