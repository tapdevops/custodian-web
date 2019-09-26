<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Generate .sql & sync data dari HO
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
class SyncDownloadDataController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_model = new Application_Model_SyncDownloadData();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//sync data dari site ke HO (norma biaya, norma harga borong, all RKT)
    public function allAction()
    {
		$userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$uniq_name = str_replace('.', '', $userName)."_".date("Y-m-d");
		$urutan = 0;
		
		//maturity stage
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_MATURITY_STAGE';
        $this->_model->genMasterMaturityStage($param); //generate query insert utk maturity stage
		
		//rotasi otomatis
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_ROTASI_OTOMATIS';
        $this->_model->genMasterRotasiOtomatis($param); //generate query insert utk rotasi otomatis
		
		//master user
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_USER';
        $this->_model->genMasterUser($param); //generate query insert utk master user
		
		//module
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_MODULE';
        $this->_model->genMasterModule($param); //generate query insert utk module
		
		//access right
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_ACCESSRIGHT';
        $this->_model->genMasterAccessRight($param); //generate query insert utk access right
		
		//parameter
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_PARAMETER';
        $this->_model->genParameter($param); //generate query insert utk parameter
		
		//parameter value
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_PARAMETER_VALUE';
        $this->_model->genParameterValue($param); //generate query insert utk parameter value
		
		//parameter value range
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_PARAMETER_VALUE_RANGE';
        $this->_model->genParameterValueRange($param); //generate query insert utk parameter value range
		
		//sequence
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_SEQ';
        $this->_model->genSequence($param); //generate query insert utk sequence
		
		//sequence check
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_T_SEQ_CHECK';
        $this->_model->genSequenceCheck($param); //generate query insert utk sequence check
		
		//struktur organisasi
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_ORGANIZATION';
        $this->_model->genMasterStrukturOrganisasi($param); //generate query insert utk struktur organisasi
		
		//aktivitas
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_AKTIVITAS';
        $this->_model->genMasterAktivitas($param); //generate query insert utk aktivitas
		
		//COA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_COA';
        $this->_model->genMasterCoa($param); //generate query insert utk COA
		
		//Mapping Aktivitas COA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_ACTIVITY_COA';
        $this->_model->genMasterMappingAktivitasCoa($param); //generate query insert utk Mapping Aktivitas COA
		
		//MAPPING AKTIVITAS UNTUK PENGGUNAAN RKT
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_ACTIVITY_RKT';
        $this->_model->genMasterMappingAktivitasMapping($param); //generate query insert utk MAPPING AKTIVITAS UNTUK PENGGUNAAN RKT
		
		//Master Job Type
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_JOB_TYPE';
        $this->_model->genMasterJobType($param); //generate query insert utk Master Job Type
		
		//Master Tunjangan
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_TUNJANGAN';
        $this->_model->genMasterTunjangan($param); //generate query insert utk Master Tunjangan
		
		//Master RVRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_RVRA';
        $this->_model->genMasterRvra($param); //generate query insert utk Master RVRA
		
		//Mapping Job Type Vra
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_JOBTYPE_VRA';
        $this->_model->genMasterMappingJobTypeVra($param); //generate query insert utk Mapping Job Type Vra
		
		//Master Mapping Job Type Wra
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_JOBTYPE_WRA';
        $this->_model->genMasterMappingJobTypeWra($param); //generate query insert utk Master Mapping Job Type Wra
		
		//Master Mapping Group BUM - COA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_GROUPBUM_COA';
        $this->_model->genMasterMappingGroupBumCoa($param); //generate query insert utk Master Mapping Group BUM - COA
		
		//Master Mapping Group Relation - COA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MAPPING_GROUPRELATION_COA';
        $this->_model->genMasterMappingGroupRelation($param); //generate query insert utk Master Mapping Group Relation - COA
		
		//master group report
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_RPT_GROUP';
        $this->_model->genMasterRptGroup($param); //generate query insert utk master group report
		
		//mapping group report - activity
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_RPT_MAPPING_ACT';
        $this->_model->genMasterRptMappingAct($param); //generate query insert utk mapping group report - activity
		
		//Master VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_VRA';
        $this->_model->genMasterVra($param); //generate query insert utk Master VRA
		
		//Master Period Budget
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_PERIOD_BUDGET';
        $this->_model->genMasterPeriodeBudget($param); //generate query insert utk Master Period Budget
		
		//sebaran produksi
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_SEBARAN_PRODUKSI';
        $this->_model->genMasterSebaranProduksi($param); //generate query insert utk Master sebaran produksi
		
		//catu
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_CATU';
        $this->_model->genMasterCatu($param); //generate query insert utk catu
		
		//checkroll HK
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_CHECKROLL_HK';
        $this->_model->genCheckrollHk($param); //generate query insert utk checkroll HK
		
		//tarif tunjangan
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_TARIF_TUNJANGAN';
        $this->_model->genTarifTunjangan($param); //generate query insert utk tarif tunjangan
		
		//asset
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_ASSET';
        $this->_model->genAsset($param); //generate query insert utk asset
		
		//material
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_MATERIAL';
        $this->_model->genMaterial($param); //generate query insert utk material
		
		//standar jam kerja
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_STANDART_JAM_KERJA_WRA';
        $this->_model->genStandarJamKerja($param); //generate query insert utk standar jam kerja
		
		//norma basic
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_BASIC';
        $this->_model->genNormaBasic($param); //generate query insert utk norma basic
		
		//norma kastrasi
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_KASTRASI_SANITASI';
        $this->_model->genNormaKastrasiSanitasi($param); //generate query insert utk norma kastrasi
		
		//norma panen variable
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_VARIABLE';
        $this->_model->genNormaPanenVariabel($param); //generate query insert utk norma panen variable		
		
		//RKT OPEX
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_OPEX';
        $this->_model->genRktOpex($param); //generate query insert utk RKT OPEX
		
		//RKT CSR / IR / SHE
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_RELATION';
        $this->_model->genRktRelation($param); //generate query insert utk RKT CSR / IR / SHE
		
		//norma harga barang
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_HARGA_BARANG';
        $this->_model->genNormaHargaBarang($param); //generate query insert utk norma harga barang
		
		//norma harga borong
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_HARGA_BORONG';
        $this->_model->genNormaHargaBorong($param); //generate query insert utk norma harga borong
		
		//norma VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_VRA';
        $this->_model->genNormaVra($param); //generate query insert utk norma VRA
		
		//norma VRA pinjam
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_VRA_PINJAM';
        $this->_model->genNormaVraPinjam($param); //generate query insert utk norma VRA pinjam
		
		//norma alat kerja panen
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_ALAT_KERJA_PANEN';
        $this->_model->genNormaAlatKerjaPanen($param); //generate query insert utk norma alat kerja panen
		
		//norma checkroll
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_CHECKROLL';
        $this->_model->genNormaCheckroll($param); //generate query insert utk norma checkroll
		
		//norma pupuk tbm
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'TN_PUPUK_TBM';
        $this->_model->genNormaPupukTbm($param); //generate query insert utk norma pupuk tbm
		
		//norma pupuk tm
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'TN_PUPUK_TM';
        $this->_model->genNormaPupukTm($param); //generate query insert utk norma pupuk tm
		
		//RKT CAPEX
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_CAPEX';
        $this->_model->genRktCapex($param); //generate query insert utk RKT CAPEX
		
		//NORMA WRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_WRA';
        $this->_model->genNormaWra($param); //generate query insert utk NORMA WRA
		
		//RKT VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_VRA';
        $this->_model->genRktVra($param); //generate query insert utk RKT VRA
		
		//RKT DIST VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_RKT_DIST_VRA';
        $this->_model->genRktDistribusiVra($param); //generate query insert utk RKT DIST VRA
		
		//NORMA BIAYA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_BIAYA';
        $this->_model->genNormaBiaya($param); //generate query insert utk NORMA BIAYA
		
		//NORMA PERK JALAN
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PERKERASAN_JALAN';
        $this->_model->genNormaPerkerasanJalan($param); //generate query insert utk NORMA PERK JALAN
		
		//NORMA PERK JALAN HARGA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PERKERASAN_JALAN_HARGA';
        $this->_model->genNormaPerkerasanJalanHarga($param); //generate query insert utk NORMA PERK JALAN HARGA
		
		//NORMA INFRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_INFRASTRUKTUR';
        $this->_model->genNormaInfrastruktur($param); //generate query insert utk NORMA INFRA
		
		//NORMA PANEN COST UNIT
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_PREMI_COST_UNIT';
        $this->_model->genNormaPanenPremiCostUnit($param); //generate query insert utk NORMA PANEN COST UNIT
		
		//NORMA PANEN KRANI BUAH
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_KRANI_BUAH';
        $this->_model->genNormaPanenKraniBuah($param); //generate query insert utk NORMA PANEN KRANI BUAH
		
		//NORMA PANEN LOADING
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_LOADING';
        $this->_model->genNormaPanenLoading($param); //generate query insert utk NORMA PANEN LOADING		
		
		//NORMA PANEN OER BJR
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_OER_BJR';
        $this->_model->genNormaPanenOer($param); //generate query insert utk NORMA PANEN OER BJR		
		
		//NORMA PANEN LANGSIR
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_LANGSIR';
        $this->_model->genNormaPanenPremiLangsir($param); //generate query insert utk NORMA PANEN LANGSIR
		
		//NORMA PANEN PREMI MANDOR
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_PREMI_MANDOR';
        $this->_model->genNormaPanenPremiMandor($param); //generate query insert utk NORMA PANEN PREMI MANDOR
		
		//NORMA PANEN SUPERVISI
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_NORMA_PANEN_SUPERVISI';
        $this->_model->genNormaPanenSupervisi($param); //generate query insert utk NORMA PANEN SUPERVISI
		
		//HS
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_HECTARE_STATEMENT';
        $this->_model->genHectareStatement($param); //generate query insert utk HS
		
		//LOCATION DIST VRA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TM_LOCATION_DIST_VRA';
        $this->_model->genLocationDistVra($param); //generate query insert utk LOCATION DIST VRA
		
		//MASTER OER BA
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_OER_BA';
        $this->_model->genMasterOerBa($param); //generate query insert utk MASTER OER BA
		
		//MASTER PERENCANAAN PRODUKSI
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_MS_PERENCANAAN_PRODUKSI';
        $this->_model->genPerencanaanProduksi($param); //generate query insert utk MASTER PERENCANAAN PRODUKSI
		
		//NORMA PANEN PREMI TOPO
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_PREMI_TOPOGRAPHY';
        $this->_model->genNormaPanenPremiTopography($param); //generate query insert utk NORMA PANEN PREMI TOPO
		
		//NORMA PANEN PROD PEMANEN
		$urutan++;
		$param['filename'] = $uniq_name.'_'.str_pad($urutan,3,"0",STR_PAD_LEFT).'_TN_PANEN_PROD_PEMANEN';
        $this->_model->genNormaPanenProdPermanen($param); //generate query insert utk NORMA PANEN PROD PEMANEN
		
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
		
		//pindahkan file ke 1 folder
		$this->moveFileAction($uniq_name);
		//$this->importSql($uniq_name);
		die($return);
    }
	
	//pindahkan file ke 1 folder
	public function moveFileAction($uniq_name) {
		//pindahkan .sql ke folder
		$uploaddir = getcwd()."/tmp_query/".$uniq_name."/";
		if ( ! is_dir($uploaddir)) {
			$oldumask = umask(0);
			mkdir("$uploaddir", 0777, true);
			chmod("/".$uniq_name, 0777);
			umask($oldumask);
		}		
		shell_exec("mv ".getcwd()."/tmp_query/".$uniq_name."*.sql ".getcwd()."/tmp_query/".$uniq_name);
    }
	
	//generate file .sql utk dijalankan ke database local
	public function importSql($uniq_name) {
		
		
		return $return;
	}
}
