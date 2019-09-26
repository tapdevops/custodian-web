<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Generate .sql & sync data ke HO
Function 			:	- allAction					: SID 12/08/2014	: sync data dari site ke HO (norma biaya, norma harga borong, all RKT)
						- compressFileAction		: SID 12/08/2014	: compress file ke .tar.gz
						- uploadFileAction			: SID 12/08/2014	: upload file using FTP
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	12/08/2014
Update Terakhir		:	12/08/2014
Revisi				:	
=========================================================================================================================
*/
class SyncUploadDataController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_model = new Application_Model_SyncUploadData();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//sync data dari site ke HO (norma biaya, norma harga borong, all RKT)
    public function allAction()
    {
		$userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$uniq_name = str_replace('.', '', $userName)."_".date("Y-m-d");
		$urutan = 0;
		
		//RKT OPEX VRA & NON VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_OPEX';
        $this->_model->genRktOpex($param); //generate query insert utk RKT OPEX VRA & NON VRA
		
		//RKT Relation
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_RELATION';
        $this->_model->genRktRelation($param); //generate query insert utk RKT Relation
		
		//norma harga borong
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_HARGA_BORONG';
        $this->_model->genNormaHargaBorong($param); //generate query insert utk norma harga borong
		
		//norma alat kerja panen
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_ALAT_KERJA_PANEN';
        $this->_model->genNormaAlatKerjaPanen($param); //generate query insert utk norma alat kerja panen
		
		//norma checkroll
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_CHECKROLL';
        $this->_model->genNormaCheckroll($param); //generate query insert utk norma checkroll
		
		//RKT CAPEX
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_CAPEX';
        $this->_model->genRktCapex($param); //generate query insert utk RKT CAPEX
		
		//norma WRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_WRA';
        $this->_model->genNormaWra($param); //generate query insert utk norma WRA
		
		//RKT VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_VRA';
        $this->_model->genRktVra($param); //generate query insert utk RKT VRA
		
		//RKT Distribusi VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_DISTRIBUSI_VRA';
        $this->_model->genRktDistribusiVra($param); //generate query insert utk RKT Distribusi VRA
		
		//norma biaya
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_BIAYA';
        $this->_model->genNormaBiaya($param); //generate query insert utk norma biaya
		
		//norma panen OER BJR
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_OER_BJR';
        $this->_model->genNormaPanenOer($param); //generate query insert utk norma panen OER BJR
		
		//norma panen supervisi
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_SUPERVISI';
        $this->_model->genNormaPanenSupervisi($param); //generate query insert utk norma panen supervisi		
		
		//hectare statement -- masih masalah, integrity constraint RKT pupuk
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_HS';
        $this->_model->genHectareStatement($param); //generate query insert utk HS
		
		//master OER BA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MASTER_OER_BA';
        $this->_model->genMasterOerBa($param); //generate query insert utk master OER BA
		
		//perencanaan produksi
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_PERENCANAAN_PRODUKSI';
        $this->_model->genPerencanaanProduksi($param); //generate query insert utk perencanaan produksi
		
		//RKT LC
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_LC';
        $this->_model->genRktLc($param); //generate query insert utk RKT LC
		
		//RKT Rawat, Rawat Opsi, Rawat Infra, Tanam Manual, Tanam Otomatis
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT';
        $this->_model->genRkt($param); //generate query insert utk RKT Rawat, Rawat Opsi, Rawat Infra, Tanam Manual, Tanam Otomatis
		
		//RKT Perkerasan Jalan
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PK';
        $this->_model->genRktPerkerasanJalan($param); //generate query insert utk RKT Perkerasan Jalan
		
		//RKT Panen
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PANEN';
        $this->_model->genRktPanen($param); //generate query insert utk RKT Panen
		
		//RKT Pupuk
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_PUPUK';
        $this->_model->genRktPupuk($param); //generate query insert utk RKT Pupuk
		
		//compress ke .tar.gz
		$this->compressFileAction($uniq_name);
		//$return = $this->uploadFileAction($uniq_name);
		
		die($return);
    }
	
	//compress file ke .tar.gz
	public function compressFileAction($uniq_name) {
		//pindahkan .sql ke folder
		$uploaddir = getcwd()."/tmp_query/".$uniq_name."/";
		if ( ! is_dir($uploaddir)) {
			$oldumask = umask(0);
			mkdir("$uploaddir", 0777, true);
			chmod("/".$uniq_name, 0777);
			umask($oldumask);
		}		
		shell_exec("mv ".getcwd()."/tmp_query/".$uniq_name."*.sql ".getcwd()."/tmp_query/".$uniq_name);
		
		//zip file di folder
		shell_exec("tar -cvzf ".getcwd()."/tmp_query/".$uniq_name.".tar.gz -C ".getcwd()."/tmp_query/".$uniq_name." .");
		
		//delete folder setelah di zip
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$uniq_name."/"); //delete folder yg telah dizip
    }
	
	//upload file using FTP
	public function uploadFileAction($uniq_name) {
		//get DB config from application.ini
		$config = Zend_Controller_Front::getInstance()->getParam('bootstrap');		
		$resources = $config->getOption('resources');
		
		/* VARIABLE TO CONNECT TO FTP SERVER */
		$ftp_server 	= $resources['db']['ftp']['server'];
		$ftp_username	= $resources['db']['ftp']['username'];
		$ftp_password	= $resources['db']['ftp']['password'];
		$ftp_remotedir	= $resources['db']['ftp']['remotedir'];
		
		$conn_id = ftp_connect($ftp_server);
		$login_result = @ftp_login($conn_id, $ftp_username, $ftp_password);
		
		if(!$login_result){
			die("Cannot connect to FTP server at " . $ftp_server);
		}
		
		ftp_pasv($conn_id, true);
		
		//check if directory exists and if not then create it
		if(!@ftp_chdir($conn_id, $ftp_remotedir)) {
			//create diectory
			ftp_mkdir($conn_id, $ftp_remotedir);
			
			//change directory
			ftp_chdir($conn_id, $ftp_remotedir);
		}
		
		$file = $uniq_name.".tar.gz";
		
		/* TRANSFER FILE USING FTP */
		$ret = ftp_nb_put($conn_id, $file, getcwd()."/tmp_query/".$file, FTP_BINARY, FTP_AUTORESUME);
		while(FTP_MOREDATA == $ret) { 
			$ret = ftp_nb_continue($conn_id);
		}
		
		if($ret == FTP_FINISHED) {
			$return = "File '" . $file . "' uploaded successfully.";
		} else {
			$return = "Failed uploading file '" . $file . "'.";
		}
		
		unlink($file);
		
		ftp_close($conn_id);
		
		//delete .tar.gz setelah FTP
		shell_exec("rm -f -r ".getcwd()."/tmp_query/".$file); //delete file yg telah diexecute
		
		return $return;
	}
}
