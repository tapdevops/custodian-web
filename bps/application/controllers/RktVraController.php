<?php
/*
=========================================================================================================================
Project             :     Budgeting & Planning System
Versi               :     2.0.0
Deskripsi           :     Controller Class untuk RKT VRA
Function            :   - getStatusPeriodeAction        : BDJ 22/07/2013    : cek status periode budget yang dipilih
                        - listAction                    : SID 02/07/2013    : menampilkan list RKT VRA
                        - mappingAction                 : SID 02/07/2013    : mapping textfield name terhadap field name di DB
                        - saveTempAction                : SID 02/07/2013    : simpan data sementara sesuai input user
                        - saveAction                    : SID 02/07/2013    : save data
                        - updateInheritModule           : SID 01/07/2014    : update inherit module
                        - deleteAction                  : SID 02/07/2013    : hapus data
Disusun Oleh        :     IT Enterprise Solution - PT Triputra Agro Persada    
Developer           :     Sabrina Ingrid Davita
Dibuat Tanggal      :     02/07/2013
Update Terakhir     :     30/04/2015
Revisi              :    
    SID 01/07/2014  :   - perubahan mekanisme penyimpananan data -> ditampung di .sql terlebih dahulu
                          pada function saveAction & saveTempAction
                        - saveAction menghitung seluruh data yang mengalami perubahan berdasarkan filter yang dipilih
                        - penambahan pengecekan untuk lock table pada listAction, saveAction, deleteAction
    YUL 11/08/2014  :   - tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction
    NBU 30/04/2015  :   - penambahan fungsi inherit untuk update Perkerasan Jalan Harga (line 576)
=========================================================================================================================
*/
class RktVraController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
        $this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_RktVra();
        $this->view->input = $this->_model->getInput();
        $sess = new Zend_Session_Namespace('period');
        $this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/rkt-vra/main');
    }
    
    //cek status periode budget yang dipilih
    public function getStatusPeriodeAction()
    {        
        $params = $this->_request->getParams();
        $value = $this->_formula->getStatusPeriode($params);
        echo json_encode($value);
        die();
    }

    public function mainAction()
    {
        $this->view->title = 'Budgeting Tahap 2 &raquo; RKT VRA';
        $this->view->period = date("Y", strtotime($this->_period));
        $this->view->tunjangan = $this->_model->getTunjangan();
        $this->view->pkumum = $this->_model->getPkUmum();
        $this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
    }
    
    //menampilkan list RKT VRA
    public function listAction()
    {        
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        
        //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
        $lock = $this->_global->checkLockTable($params);
        
        if($lock['JUMLAH']){
            $data['return'] = "locked";
            $data['module'] = $lock['MODULE'];
            $data['insert_user'] = $lock['INSERT_USER'];
            die(json_encode($data));
        } else {        
            $data = $this->_model->getList($params);
            die(json_encode($data));
        }
    }
    
    //mapping textfield name terhadap field name di DB
    public function mappingAction(){
        $params = $this->_request->getParams();
        $rows = array();
        
        foreach ($params['text00'] as $key => $val) {
            if (($key > 0) && ($params['text03'][$key]) && ($params['tChange'][$key])){
                $rows[$key]['CHANGE']               = $params['tChange'][$key]; // CHANGE
                $rows[$key]['ROW_ID']               = $params['text00'][$key]; // ROW ID
                $rows[$key]['TRX_RKT_VRA_CODE']     = $params['trxrktcode'][$key]; // TRX_RKT_VRA_CODE
                $rows[$key]['PERIOD_BUDGET']        = $params['text02'][$key]; // PERIOD_BUDGET
                $rows[$key]['BA_CODE']              = $params['text03'][$key]; // BA_CODE
                $rows[$key]['VRA_CODE']             = $params['text05'][$key]; // VRA_CODE
                $rows[$key]['DESCRIPTION_VRA']      = $params['text07'][$key]; // DESCRIPTION_VRA
                $rows[$key]['JUMLAH_ALAT']          = $params['text08'][$key]; // JUMLAH_ALAT
                $rows[$key]['TAHUN_ALAT']           = $params['text09'][$key]; // TAHUN_ALAT
                $rows[$key]['QTY_DAY']              = $params['text11'][$key]; // QTY_DAY
                $rows[$key]['DAY_YEAR_VRA']         = $params['text12'][$key]; // DAY_YEAR_VRA
                $rows[$key]['JUMLAH_OPERATOR']      = $params['text15'][$key]; // JUMLAH_OPERATOR
                $rows[$key]['JUMLAH_HELPER']        = $params['text16'][$key]; // JUMLAH_HELPER
                $rows[$key]['RVRA1_VALUE2']         = $params['text17'][$key]; // HARGA PAJAK
                $rows[$key]['RVRA17_VALUE1']        = $params['text18'][$key]; // QTY/SAT RENTAL
                $rows[$key]['RVRA17_VALUE2']        = $params['text19'][$key]; // HARGA RENTAL
                $rows[$key]['RVRA12_VALUE2']        = $params['text20'][$key]; // HARGA GANTI SPAREPART
                $rows[$key]['RVRA16_VALUE2']        = $params['text21'][$key]; // HARGA OVERHAUL
                $rows[$key]['RVRA15_VALUE1']        = $params['text22'][$key]; // JAM KERJA WORKSHOP
                $rows[$key]['RVRA18_VALUE2']        = $params['text23'][$key]; // HARGA SERVIS BENGKEL LUAR
                $rows[$key]['INTERNAL_ORDER']       = $params['text24'][$key]; // INTERNAL ORDER --Aries 05-052015
                $rows[$key]['KOMPARISON_OUT_HM_KM'] = $params['text25'][$key]; // KOMPARISON_OUT_HM_KM --Aries 05-052015
                $rows[$key]['RP_QTY_BULAN_BUDGET']  = $params['text26'][$key]; // RP_QTY_BULAN_BUDGET --Aries 05-052015
                
                //inharitance variable                
                $rows[$key]['key_find']             = $params['text03'][$key]; // BA_CODE
                $rows[$key]['vra_code']             = $params['text05'][$key]; // VRA_CODE
                $rows[$key]['sub_cost_element']     = $params['text05'][$key]; // VRA_CODE - norma biaya
                
                //old data
                $rows[$key]['OLD_VRA_CODE']         = $params['text005'][$key]; // OLD_VRA_CODE
                $rows[$key]['OLD_DESCRIPTION_VRA']  = $params['text007'][$key]; // OLD_DESCRIPTION_VRA
                $rows[$key]['OLD_INTERNAL_ORDER']   = $params['text024'][$key]; // OLD_INTERNAL_ORDER --Aries 05-052015
                $rows[$key]['OLD_TAHUN_ALAT']       = $params['text009'][$key]; // OLD_TAHUN_ALAT --Aries 05-052015
                
                if ($rows[$key]['OLD_INTERNAL_ORDER'] == ""){
                    $rows[$key]['OLD_INTERNAL_ORDER'] = "-";
                }
                if ($rows[$key]['INTERNAL_ORDER'] == ""){
                    $rows[$key]['INTERNAL_ORDER'] = "-";
                }
            }
        }

        return $rows;
    }
    
    //save data
    public function saveAction()
    {
        $rows = $this->mappingAction();
        $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        
        if (empty($rows)) {
            $data['return'] = "kosong";
            die(json_encode($data));
        }else{
            //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
            /*foreach ($rows as $key => $row) {
                $params['key_find'] = $row['BA_CODE'];
                $lock = $this->_global->checkLockTable($params);        
                if($lock['JUMLAH']){
                    $data['return'] = "locked";
                    $data['module'] = $lock['MODULE'];
                    $data['insert_user'] = $lock['INSERT_USER'];
                    die(json_encode($data));
                }
            }*/
            
            
            //ketika RKT VRA melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
            foreach ($rows as $key => $row) {
                if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
                    $this->_global->insertLockTable($lastBa, 'RKT VRA');
                }
                
                $lastBa = $row['BA_CODE'];            
            }
            $this->_global->insertLockTable($lastBa, 'RKT VRA');
            
            
            // ************************************************ SAVE RKT VRA TEMP ************************************************
            //generate filename untuk .sh dan .sql
            $filename = $uniq_code_file.'_00_RKTVRA_01_SAVETEMP';
            $this->_global->createBashFile($filename); //create bash file
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            //save norma biaya temp
            foreach ($rows as $key => $row) {
                $row['filename'] = $filename;
                $return = $this->_model->saveTemp($row); 
            }    
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ SAVE RKT VRA TEMP ************************************************
        }
        
        // ************************************************ SAVE ALL RKT VRA ************************************************
        //generate filename untuk .sh dan .sql
        $filename = $uniq_code_file.'_00_RKTVRA_02_SAVE';
        //echo $filename;
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
        //hitung distribusi biaya seluruh halaman
        $params = $this->_request->getPost();
        //print_r ($params);
        $records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");
        //print_r ($records1);
            
        if (!empty($records1)) {
            foreach ($records1 as $idx1 => $record1) {
                //if($record1['FLAG_TEMP'] == 'Y'){
                if ($record1['ROW_ID'] == 'AAAptsAAIAAAxnoAAC') {
                    $record1['filename'] = $filename;
                    $this->_model->save($record1);
                    //ditampung untuk inherit module
                    $updated_row[] = $record1;
                }
            }
        }

        //execute transaksi
        $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
        shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
        $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
        shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
        
        //pindahkan .sql ke logs
        $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
        if ( ! is_dir($uploaddir)) {
            $oldumask = umask(0);
            mkdir("$uploaddir", 0777, true);
            chmod("/".date("Y-m-d"), 0777);
            umask($oldumask);
        }
        shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
        // ************************************************ SAVE ALL RKT VRA ************************************************
        
        
        // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
        //distinct VRA CODE & BA CODE
        $uniq_row['VRA_CODE'] = array();
        $uniq_row['BA_CODE'] = array();
        if (!empty($updated_row)) {
            foreach ($updated_row as $idx1 => $record1) {
                if (in_array($record1['VRA_CODE'], $uniq_row['VRA_CODE']) == false) {
                    array_push($uniq_row['VRA_CODE'], $record1['VRA_CODE']);
                }
                
                if (in_array($record1['BA_CODE'], $uniq_row['BA_CODE']) == false) {
                    array_push($uniq_row['BA_CODE'], $record1['BA_CODE']);
                }
            }
        }        

        
        $updated_row['BA_CODE'] = (count($uniq_row['BA_CODE']) > 1) ? implode("','", $uniq_row['BA_CODE']) : $uniq_row['BA_CODE'][0];
        $updated_row['VRA_CODE'] = (count($uniq_row['VRA_CODE']) > 1) ? implode("','", $uniq_row['VRA_CODE']) : $uniq_row['VRA_CODE'][0];
        
        print_r ($uniq_row);
        //generate filename untuk .sh dan .sql
        $filename = $uniq_code_file.'_00_RKTVRA_03_SUMRKTVRA';
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
        if (!empty($uniq_row['VRA_CODE']) && !empty($uniq_row['BA_CODE'])) {
            foreach ($uniq_row['BA_CODE'] as $idx1 => $record1) {
                foreach ($uniq_row['VRA_CODE'] as $idx2 => $record2) {
                    $par['filename'] = $filename;
                    $par['BA_CODE'] = $record1;
                    $par['VRA_CODE'] = $record2;
                    $par['PERIOD_BUDGET'] = $params['budgetperiod'];
                    echo 'test';
                    $this->_model->updateSummaryRktVra($par);
                }
            }
        }
        
        //execute transaksi
        $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
        shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
        $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
        shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
        
        //pindahkan .sql ke logs
        $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
        if ( ! is_dir($uploaddir)) {
            $oldumask = umask(0);
            mkdir("$uploaddir", 0777, true);
            chmod("/".date("Y-m-d"), 0777);
            umask($oldumask);
        }
        shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
        // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
        
        // ************************************************ UPDATE INHERIT MODULE ************************************************
        if (!empty($updated_row['BA_CODE'])) {
            $idxInherit = 1;            
            //deklarasi var utk inherit module
            $uniq_row['uniq_code_file']     = "YUS-".$uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename    
            $uniq_row['PERIOD_BUDGET']         = $params['budgetperiod'];
            $uniq_row['budgetperiod']         = $params['budgetperiod'];
            $uniq_row['key_find'] = $updated_row['BA_CODE'];
            $uniq_row['BA_CODE']         = $updated_row['BA_CODE'];
            $uniq_row['VRA_CODE']         = $updated_row['VRA_CODE'];
            
            //update inherit module
            $this->updateInheritModule($uniq_row);
            // ************************************************ UPDATE INHERIT MODULE ************************************************
        }
        
        //hapus dari table lock ketika selesai melakukan perhitungan di RKT VRA
        if (!empty($rows)) {
            foreach ($rows as $key => $row) {
                if(($lastBa) && ($lastBa <> $row['BA_CODE'])){
                    $this->_global->deleteLockTable($lastBa, 'RKT VRA');
                }
                $lastBa = $row['BA_CODE'];            
            }
            $this->_global->deleteLockTable($lastBa, 'RKT VRA');
        }

        $data['return'] = "done";
        die(json_encode($data));
    }
    
    //update inherit module
    public function updateInheritModule($row = array()) {
        $check_update_rkt_panen = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Panen
        $check_update_rkt_perkerasan_jalan = 0; //untuk check jika bernilai true, akan menjalankan perhitungan RKT Perkerasan Jalan
        print_r($row);//die();
        if (!empty($row)) {    
            $par['PERIOD_BUDGET'] = $row['PERIOD_BUDGET'];
            $par['budgetperiod'] = $row['budgetperiod'];
            //implode
            $par['VRA_CODE'] = (count($row['VRA_CODE']) > 1) ? implode("','", $row['VRA_CODE']) : $row['VRA_CODE'];
            $par['key_find'] = (count($row['BA_CODE']) > 1) ? implode("','", $row['BA_CODE']) : $row['BA_CODE'];
            $tmp['ACT_CODE'] = array();
        
        // ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************            
            $model = new Application_Model_NormaDistribusiVraNonInfra();
            $updated_row = $this->_db->fetchAll("{$model->getChangedData($row)}");
            //print_r ($updated_row); die;
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_01_DISTVRANONINFRA';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($updated_row)) {
                foreach ($updated_row as $idx1 => $record1) {
                    $record1['filename'] = $filename;
                    $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
                    $model->updateRecord($record1);
                    
                    //distinct activity
                    if (in_array($record1['ACTIVITY_CODE'], $tmp['ACT_CODE']) == false) {
                        array_push($tmp['ACT_CODE'], $record1['ACTIVITY_CODE']);
                    }
                }
            }
            $row['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
            $row['activity_code'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
              
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE DISTRIBUSI VRA - NON INFRA ************************************************
            if(!empty($row['activity_code'])){
                // ************************************************ UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA ************************************************
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_02_SUMDISTVRANONINFRA';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($updated_row)) {
                    foreach ($updated_row as $idx1 => $record1) {
                        $record1['filename'] = $filename;
                        $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET']));
                        $model->updateSummaryNormaDistribusiVra($record1);
                    }
                    
                    //cari aktivitas yang terupdate di dist VRA
                    $records1 = $this->_db->fetchAll("{$model->getDataHeader($row)}");
                    foreach ($records1 as $idx1 => $record1) {
                        $temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
                    }
                    
                }
                $uniq_arr = array_unique($temp_row['vra_code']);
                $row['vra_code'] =  implode("','", $uniq_arr);
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                // ************************************************ UPDATE SUMMARY DISTRIBUSI VRA - NON INFRA ************************************************
                }
            /*
            // ************************************************ UPDATE OPEX VRA ************************************************
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_03_OPEXVRA';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($row['BA_CODE'])) {
                foreach ($row['BA_CODE'] as $idx2 => $record2) {
                    $par_opex_vra['filename'] = $filename;
                    $par_opex_vra['key_find'] = $record2;
                    $par_opex_vra['PERIOD_BUDGET'] = $par['PERIOD_BUDGET'];
                    $model_dist->updateRktOpexVra($par_opex_vra);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE OPEX VRA ************************************************
            */
            
            // ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
            $check = strpos($par['VRA_CODE'], 'DT010'); //kalo vra nya DUMP TRUCK, baru update cost unit
            if ($check !== false) {    
                $check_update_rkt_panen = 1;
                
                $model = new Application_Model_NormaPanenCostUnit();
                $records1 = $this->_db->fetchAll("{$model->getData($row)}");
            
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_04_NPANENCOSTUNIT';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        $record1['filename'] = $filename;
                        $model->save($record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
            }
            // ************************************************ UPDATE NORMA PANEN COST UNIT ************************************************
            
            // ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
            $model = new Application_Model_NormaPanenPremiLangsir();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");
        
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_04_NPANENPREMILANGSIR';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                $check_update_rkt_panen = 1;
                
                foreach ($records1 as $idx1 => $record1) {
                    $record1['filename'] = $filename;
                    $model->save($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE NORMA PANEN PREMI LANGSIR ************************************************
            
            // ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
            $model = new Application_Model_NormaInfrastruktur();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");
        
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_05_NINFRA';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    $record1['filename'] = $filename;
                    $model->save($record1);
                }                    
            }
            $par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE NORMA INFRASTRUKTUR ************************************************
            
            // ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
            $check = strpos($par['VRA_CODE'], 'EX010'); //kalo vra nya EXCAV, baru update
            if (!$check) $check = strpos($par['VRA_CODE'], 'VC010'); //kalo vra nya COMPACTOR, baru update
            if (!$check)  $check = strpos($par['VRA_CODE'], 'GD010'); //kalo vra nya GRADER, baru update
            if (!$check)  $check = strpos($par['VRA_CODE'], 'DT010'); //kalo vra nya DUMPTRUCK, baru update
            
            if ($check !== false) {    
                $model = new Application_Model_NormaPerkerasanJalan();
                $records1 = $this->_db->fetchAll("{$model->getData($row)}");
                
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_06_NPERKERASANJALAN';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        $temp_row['activity_code'][] = $record1['ACTIVITY_CODE'];
                        $record1['filename'] = $filename;
                        $model->save($record1);
                        $model->updTnHarga($record1);
                    }
                }
                $par['ACT_CODE'] = (count($tmp['ACT_CODE']) > 1) ? implode("','", $tmp['ACT_CODE']) : $tmp['ACT_CODE'][0];
                //distinct activity
                if (!empty($temp_row['activity_code'])) {
                    $uniq_arr = array_unique($temp_row['activity_code']);
                    $row['activity_code'] =  implode("','", $uniq_arr);
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
            }
            // ************************************************ UPDATE NORMA PERKERASAN JALAN ************************************************
            
            if(!empty($row['activity_code'])){
                // ************************************************ UPDATE RKT LC ************************************************
                $model = new Application_Model_RktLc();
                $records1 = $this->_db->fetchAll("{$model->getDataInheritance($row)}");
                
                //1. SAVE COST ELEMENT RKT LC
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_07_RKTLCCE';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                    
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        //hitung cost element
                        $record1['filename'] = $filename;
                        $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET'])); //format date period budget
                        $model->calCostElement('TRANSPORT', $record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
                //2. HITUNG TOTAL COST
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_08_RKTLCTOTAL';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                    
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {                    
                        //hitung total cost
                        $record1['filename'] = $filename;
                        $record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET'])); //format date period budget
                        $model->calTotalCost($record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                // ************************************************ UPDATE RKT LC ************************************************
            }
            
        if(!empty($row['activity_code'])){
            // ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
            $model = new Application_Model_RktManualNonInfra();
            $model_dist = new Application_Model_NormaDistribusiVraNonInfra();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");    
            
            //1. SAVE RKT MANUAL NON INFRA
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_09_RKTRAWATCE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_10_RKTRAWATTOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT MANUAL NON INFRA ************************************************
            
            // ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
            $model = new Application_Model_RktKastrasiSanitasi();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");    
            
            //1. SAVE RKT MANUAL NON INFRA
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_10A_RKTKASTRASICE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_10A_RKTKASTRASITOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT KASTRASI - SANITASI ************************************************
            
            // ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
            $model = new Application_Model_RktManualNonInfraOpsi();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");
            
            //1. SAVE RKT MANUAL NON INFRA + OPSI
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_11_RKTRAWATOPSICE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_12_RKTRAWATOPSITOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT MANUAL NON INFRA + OPSI ************************************************
            
            // ************************************************ UPDATE RKT MANUAL INFRA ************************************************
            $model = new Application_Model_RktManualInfra();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");    
            
            //1. SAVE RKT MANUAL INFRA
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_13_RKTRAWATINFRACE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1); 
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_14_RKTRAWATINFRATOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            $rec = $this->_db->fetchAll("{$model->getData($row)}");    
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {                
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT MANUAL INFRA ************************************************
        
            // ************************************************ UPDATE RKT TANAM MANUAL ************************************************
            $model = new Application_Model_RktTanamManual();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");
            
            //1. SAVE RKT TANAM MANUAL
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_15_RKTTANAMCE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_16_RKTTANAMTOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT TANAM MANUAL ************************************************
            
            // ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
            $model = new Application_Model_RktTanam();
            $records1 = $this->_db->fetchAll("{$model->getData($row)}");
            
            //1. SAVE RKT TANAM OTOMATIS
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_17_RKTTANAMOTOCE';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung cost element
                    $record1['filename'] = $filename;
                    $model->calCostElement('TRANSPORT', $record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            
            //2. HITUNG TOTAL COST
            //generate filename untuk .sh dan .sql
            $filename = $row['uniq_code_file'].'_RKTVRA_18_RKTTANAMOTOTOTAL';
            $this->_global->createBashFile($filename); //create bash file            
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            if (!empty($records1)) {
                foreach ($records1 as $idx1 => $record1) {
                    //hitung total cost
                    $record1['filename'] = $filename;
                    $model->calTotalCost($record1);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            // ************************************************ UPDATE RKT TANAM OTOMATIS ************************************************
        
            // ************************************************ UPDATE RKT PANEN ************************************************            
            if ($check_update_rkt_panen == 1){
                $model = new Application_Model_RktPanen();
                $records1 = $this->_db->fetchAll("{$model->getData($row)}");
                
                //1. SAVE RKT TANAM OTOMATIS
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_19_RKTPANENCE';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                    
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        //hitung cost element
                        $record1['filename'] = $filename;
                        $model->calCostElement('TRANSPORT', $record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
                //2. HITUNG TOTAL COST
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_20_RKTPANENTOTAL';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        //hitung total cost
                        $record1['filename'] = $filename;
                        $model->calTotalCost($record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            }
            // ************************************************ UPDATE RKT PANEN ************************************************
            
            // ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************            
            if ($check_update_rkt_perkerasan_jalan == 1){
                $model = new Application_Model_RktPerkerasanJalan();
                $records1 = $this->_db->fetchAll("{$model->getData($row)}");
                
                //1. SAVE RKT TANAM OTOMATIS
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_21_RKTPERKJALANCE';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                    
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        //hitung cost element
                        $record1['filename'] = $filename;
                        $model->calCostElement('TRANSPORT', $record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
                //2. HITUNG TOTAL COST
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_22_RKTPERKJALANTOTAL';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($records1)) {
                    foreach ($records1 as $idx1 => $record1) {
                        //hitung total cost
                        $record1['filename'] = $filename;
                        $model->calTotalCost($record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
            }
            // ************************************************ UPDATE RKT PERKERASAN JALAN ************************************************
        }    
            //jika aktivitas TABUR PUPUK, UNTIL PUPUK baru hitung RKT Pupuk
            if ( (strpos($par['ACT_CODE'], '43750')  !== false ) || (strpos($par['ACT_CODE'], '43760') !== false ) ){
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
                $model = new Application_Model_RktPupukDistribusiBiayaNormal();    
                $row['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET'])); //format date period budget
                $rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
            
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_23_RKTPUPUKBIAYANORMAL';
                $this->_global->createBashFile($filename); //create bash file        
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                if (!empty($rec)) {
                    foreach ($rec as $idx1 => $record1) {
                        $record1['filename'] = $filename;    //$record1['PERIOD_BUDGET'] = date('Y',strtotime($record1['PERIOD_BUDGET'])); //format date period budget
                        
                        $model->calCostElement('TRANSPORT', $record1);
                    }
                }
            
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA NORMAL ************************************************
                
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
                $model = new Application_Model_RktPupukDistribusiBiayaSisip();    
                $rec = $this->_db->fetchAll("{$model->getInheritData($row)}");
                
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_24_RKTPUPUKBIAYASISIP';
                $this->_global->createBashFile($filename); //create bash file        
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                if (!empty($rec)) {
                    foreach ($rec as $idx1 => $record1) {
                        $record1['filename'] = $filename;    
                        $model->calCostElement('TRANSPORT', $record1);
                    }
                }
            
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA SISIP ************************************************
                
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
                //generate filename untuk .sh dan .sql
                $filename = $row['uniq_code_file'].'_RKTVRA_25_RKTPUPUKBIAYA';
                $this->_global->createBashFile($filename); //create bash file        
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
                
                //save
                $model = new Application_Model_RktPupukDistribusiBiayaGabungan();    
                $par['filename'] = $filename;    
                $model->calculateAllItem($row);
            
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                // ************************************************ UPDATE RKT PUPUK - DISTRIBUSI BIAYA GABUNGAN ************************************************
                
                // ************************************************ UPDATE OPEX RKT VRA ************************************************
                $model = new Application_Model_RktOpexVra();
                $rec = $this->_db->fetchAll("{$model->getData($row)}");
                
                //generate filename untuk .sh dan .sql
                $urutan++;
                $filename = $row['uniq_code_file'].'_TARIFTUNJ_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'OPEXVRA';
                $this->_global->createBashFile($filename); //create bash file            
                $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

                if (!empty($rec)) {
                    foreach ($rec as $idx1 => $record1) {
                        $record1['filename'] = $filename;
                        $model->save($record1);
                    }
                }
                
                //execute transaksi
                $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
                shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
                $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
                shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
                
                //pindahkan .sql ke logs
                $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
                if ( ! is_dir($uploaddir)) {
                    $oldumask = umask(0);
                    mkdir("$uploaddir", 0777, true);
                    chmod("/".date("Y-m-d"), 0777);
                    umask($oldumask);
                }
                shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
                
                // ************************************************ UPDATE OPEX RKT VRA ************************************************
                
            }
        }
    }
    
    //save data temp
    public function saveTempAction()
    {
        $rows = $this->mappingAction();
        
        if (!empty($rows)) {
            //generate filename untuk .sh dan .sql
            $filename = $this->_global->genFileName();
            $this->_global->createBashFile($filename); //create bash file        
            $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
            //save inputan user
            foreach ($rows as $key => $row) {
                // Kalau ada perubahan di rownya, baru disimpan
                if($row['CHANGE'] == 'Y') {
                    $row['filename'] = $filename;
                    $this->_model->saveTemp($row);
                }
            }
            
            //execute transaksi
            $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
            shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
            $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
            shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
            
            //pindahkan .sql ke logs
            $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
            if ( ! is_dir($uploaddir)) {
                $oldumask = umask(0);
                mkdir("$uploaddir", 0777, true);
                chmod("/".date("Y-m-d"), 0777);
                umask($oldumask);
            }
            shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
        }
        
        die('no_alert');
    }
    
    //hapus data
    public function deleteAction()
    {
        $uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $params = $this->_request->getParams();
       
        //cek apakah ada table master yang sedang diedit, jika ada yang sedang diedit, maka data tidak akan muncul
        $params['key_find'] = $params['BA_CODE'];
        $lock = $this->_global->checkLockTable($params);        
        if($lock['JUMLAH']){
            $data['return'] = "locked";
            $data['module'] = $lock['MODULE'];
            $data['insert_user'] = $lock['INSERT_USER'];
            die(json_encode($data));
        }
        
        //ketika RKT VRA melakukan perhitungan, maka RKT turunan tidak dapat melakukan perhitungan sampai proses disini selesai
        $this->_global->insertLockTable($params['BA_CODE'], 'RKT VRA');
        
        // ************************************************ UPDATE RKT VRA ************************************************
        //generate filename untuk .sh dan .sql
        $filename = $uniq_code_file.'_00_RKTVRA_01_DELETE';
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
        
        $params['filename'] = $filename;
        
        $this->_model->delete($params);
        
        //execute transaksi
        $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
        shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
        $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
        shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
        
        //pindahkan .sql ke logs
        $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
        if ( ! is_dir($uploaddir)) {
            $oldumask = umask(0);
            mkdir("$uploaddir", 0777, true);
            chmod("/".date("Y-m-d"), 0777);
            umask($oldumask);
        }
        shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
        // ************************************************ UPDATE RKT VRA ************************************************
        
        
        // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
        //generate filename untuk .sh dan .sql
        $filename = $uniq_code_file.'_00_RKTVRA_02_SUMRKTVRA';
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file
            
        $params['filename'] = $filename;
        $this->_model->updateSummaryRktVra($params);
        
        //execute transaksi
        $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
        shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
        $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
        shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
        
        //pindahkan .sql ke logs
        $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
        if ( ! is_dir($uploaddir)) {
            $oldumask = umask(0);
            mkdir("$uploaddir", 0777, true);
            chmod("/".date("Y-m-d"), 0777);
            umask($oldumask);
        }
        shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");
        // ************************************************ UPDATE SUMMARY RKT VRA ************************************************
        
        // ************************************************ UPDATE INHERIT MODULE ************************************************
        $idxInherit = 1;            
        //deklarasi var utk inherit module
        $uniq_row['VRA_CODE']             = $params['OLD_VRA_CODE'];    
        $uniq_row['BA_CODE']            = $params['BA_CODE'];    
        $uniq_row['uniq_code_file']     = $uniq_code_file.'_INH_'.str_pad($idxInherit,3,"0",STR_PAD_LEFT); // filename
        $uniq_row['PERIOD_BUDGET']         = $params['budgetperiod'];
        $uniq_row['budgetperiod']         = $params['budgetperiod'];
        
        //update inherit module
        $this->updateInheritModule($uniq_row);
        // ************************************************ UPDATE INHERIT MODULE ************************************************
        
        //hapus dari table lock ketika selesai melakukan perhitungan di RKT VRA
        $this->_global->deleteLockTable($params['BA_CODE'], 'RKT VRA');
        
        $data['return'] = "done";
        die(json_encode($data));
    }
    
    public function updLockedSeqStatusAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $params['task_name'] = "TR_RKT_VRA";
        $data = $this->_global->updLockedSeqStatus($params);
        die(json_encode($data));
    }
    
    public function chkEnhLockedSequenceAction(){
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $params['task_name'] = "TR_RKT_VRA";
        $data = $this->_global->chkEnhLockedSequence($params);
        die(json_encode($data));
    }
    
    public function checkLockedSeqAction(){    
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $params['task_name'] = "TR_RKT_VRA";
        $data = $this->_global->checkLockSequence($params);
        die(json_encode($data));
    }

    public function recalculateAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $filename = $this->_global->genFileName().'_00_RKTVRA_02_SAVE';
        $this->_global->createBashFile($filename); //create bash file
        $this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

        $params = $this->_request->getPost();
        $records1 = $this->_db->fetchAll("{$this->_model->getData($params)}");

        foreach ($records1 as $idx1 => $record1) {
            $record1['filename'] = $filename;
            $vra = $this->_model->save($record1);
        }

        //execute transaksi
        $this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
        shell_exec("sh ".getcwd()."/tmp_query/".$filename.".sh"); //execute query        
        $this->_global->createSqlFile($filename, "END : ".date("Y-m-d H:i:s")."\n"); //end execute
        // shell_exec("rm -f -r ".getcwd()."/tmp_query/".$filename.".sh"); //delete file yg telah diexecute
        
        //pindahkan .sql ke logs
        $uploaddir = getcwd()."/logs/".date("Y-m-d")."/";
        if ( ! is_dir($uploaddir)) {
            $oldumask = umask(0);
            mkdir("$uploaddir", 0777, true);
            chmod("/".date("Y-m-d"), 0777);
            umask($oldumask);
        }
        shell_exec("mv ".getcwd()."/tmp_query/".$filename.".sql ".getcwd()."/logs/".date("Y-m-d")."/".$filename.".sql");

        die();
    }
}
