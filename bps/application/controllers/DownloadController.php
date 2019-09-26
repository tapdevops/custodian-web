<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Controller Class untuk popup LoV
Function 			:	////////////////////////////////////////////////// DOWNLOAD TEMPLATE //////////////////////////////////////////////////
						- 04/07	: templatePerencanaanProduksiAction			: download template perencanaan produksi
						
						////////////////////////////////////////////////// DOWNLOAD DATA //////////////////////////////////////////////////
					    - 06/05	: dataAssetAction							: download data asset
						- 06/05	: dataCatuAction							: download data catu
						- 06/05	: dataHaStatementAction						: download data Ha Statement
						- 06/05	: dataMaterialAction						: download data material
						- 06/05	: dataTarifTunjanganAction					: download data tarif tunjangan
						- 07/05	: dataActualPanenAction						: download data panen aktual
						- 15/05	: dataActivityMappingAction					: download data mapping aktivitas
						- 03/06	: dataHaStatementDetailAction				: download data Ha Statement Detail
						- 07/06	: dataNormaBasicAction						: download data norma dasar
						- 10/06	: dataNormaHargaBarangAction				: download data norma harga barang
						- 10/06	: dataNormaHargaBorongAction				: download data norma harga borong
						- 11/06	: dataNormaBiayaAction						: download data norma biaya
						- 11/06	: dataNormaAlatKerjaNonPanenAction			: download data norma alat kerja non panen
						- 11/06	: dataNormaAlatKerjaPanenAction				: download data norma alat kerja panen
						- 13/06	: dataNormaWraAction						: download data norma WRA
						- 13/06	: dataNormaCheckrollAction					: download data norma Checkroll
						- 17/06	: dataReportCheckrollAction					: download data report Checkroll
						- 18/06	: dataNormaPanenOerBjrAction				: download data norma panen OER BJR
						- 21/06	: dataNormaVraAction						: download data norma VRA
						- 24/06	: dataNormaPupukTbmLessAction				: download data norma pupuk < TBM 2
						- 24/06	: dataNormaPupukTbmTmAction					: download data norma pupuk TBM 2 - TM
						- 25/06	: dataNormaPanenPremiMandorAction			: download data norma panen premi mandor
						- 25/06	: dataNormaPanenVariabelAction				: download data norma panen variabel
						- 25/06	: dataNormaInfrastrukturAction				: download data norma Infrastruktur
						- 26/06	: dataNormaPanenLoadingAction				: download data norma panen loading
						- 26/06	: dataNormaPanenSupervisiAction				: download data norma panen supervisi
						- 27/06	: dataNormaPanenKraniBuahAction				: download data norma panen krani buah
						- 27/06	: dataNormaPanenPremiLangsirAction			: download data norma panen premi langsir
						- 28/06	: dataNormaPerkerasanJalanAction			: download data norma perkerasan jalan
						- 28/06	: dataNormaPanenCostUnitAction				: download data norma panen cost unit
						- 28/06	: dataNormaPanenPremiPemanenAction			: download data norma panen premi pemanen
						- 03/07	: dataReportVraAction						: download data report VRA
						- 04/07	: dataPerencanaanProduksiAction				: download data perencanaan produksi
						- 08/07	: dataRktCapexAction						: download data RKT CAPEX
						- 08/07	: dataReportNormaPerkerasanJalanAction		: download data report norma perkerasan jalan
						- 09/07	: dataReportRktCapexAction					: download data report RKT CAPEX
						- 10/07	: dataRktOpexAction							: download data RKT OPEX
						- 11/07	: dataRktVraAction							: download data RKT VRA
						- 15/07	: dataRktCsrAction							: download data RKT CSR
						- 15/07	: dataRktInternalRelationAction				: download data RKT Internal Relation
						- 15/07	: dataRktSheAction							: download data RKT SHE
						- 15/07	: dataReportCheckrollAlokasiAction			: download data report alokasi Checkroll
						- 18/07	: dataRktPupukHaAction						: download data RKT Pupuk HA
						- 18/07	: dataRktPupukDistribusiBiayaNormalAction	: download data RKT Pupuk distribusi biaya normal
						- 18/07	: dataRktPupukDistribusiBiayaSisipAction	: download data RKT Pupuk distribusi biaya sisip
						- 18/07	: dataRktPupukDistribusiBiayaGabunganAction	: download data RKT Pupuk distribusi biaya gabungan
						- 22/07	: dataRktPupukKgNormalAction				: download data RKT Pupuk kg normal
						- 22/07	: dataRktOpexVraAction						: download data RKT OPEX VRA
						- 22/07	: dataNormaDistribusiVraNonInfraAction		: download data norma distribusi VRA non infra
						- 22/07	: dataRktPupukKgSisipAction					: download data RKT Pupuk sisip normal
						- 22/07 : dataNormaDistribusiVraAction 				: download data norma distribusi VRA
						- 24/07	: dataRktManualNonInfraAction				: download data RKT Manual - Non Infra
						- 25/07	: dataRktManualInfraAction					: download data RKT Manual - Infra
						- 26/07	: dataRktManualNonInfraOpsiAction			: download data RKT Manual - Non Infra + Opsi
						- 02/08 : dataRktLcAction							: download data RKT LC 
						- 14/08 : dataRktPanenAction						: download data RKT Panen  
						- 18/08 : dataRktTanamManualAction					: download data RKT Tanam Manual  
						- 21/08 : dataRktTanamAction						: download data RKT Tanam
						- 23/08 : templateReportNormaPerkerasanJalanAction	: template data norma perkerasan jalan
						- _listAction										: menampilkan list
						- 05/08	: dataNormaPanenPremiTopographyAction		: download data norma panen premi topography
						- 06/08	: dataNormaPanenProduktifitasPemanenAction	: download data norma panen produktifitas pemanen
						
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	02/05/2013
Update Terakhir		:	23/08/2016
Revisi				:	
YUL	13/08/2014		:	-	Tambah Function Download Norma Kastrasi Sanitasi
ARW	23/08/2016		:	-	Perubahan proses perhitungan pada perkerasan jalan yang menggunakan pilihan external
=========================================================================================================================
*/
class DownloadController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
    }

    public function indexAction()
    {
        // there is no index
        $this->_helper->layout->setLayout('error');
        $this->_request->setControllerName('error')
                       ->setActionName('no-index');
    }
	
	////////////////////////////////////////////////// DOWNLOAD TEMPLATE //////////////////////////////////////////////////
	
	//download template perencanaan produksi
    public function templatePerencanaanProduksiAction()
    {
        $this->_listAction();
    }
	
	////////////////////////////////////////////////// DOWNLOAD DATA //////////////////////////////////////////////////
	//download data asset
    public function dataAssetAction()
    {
        $this->_listAction();
    }
	
	//download data catu
    public function dataCatuAction()
    {
        $this->_listAction();
    }
	
	//download data Ha Statement
    public function dataHaStatementAction()
    {
        $this->_listAction();
    }
	
	//download data material
    public function dataMaterialAction()
    {
        $this->_listAction();
    }
	
	//download data tarif tunjangan
    public function dataTarifTunjanganAction()
    {
        $this->_listAction();
    }
	
	//download data panen aktual
    public function dataActualPanenAction()
    {
        $this->_listAction();
    }
	
	//download data mapping aktivitas
    public function dataActivityMappingAction()
    {
        $this->_listAction();
    }
	
	//download data Ha Statement Detail
    public function dataHaStatementDetailAction()
    {
        $this->_listAction();
    }
	
	//download data norma dasar
    public function dataNormaBasicAction()
    {
        $this->_listAction();
    }
	
	//download data norma harga barang
    public function dataNormaHargaBarangAction()
    {
        $this->_listAction();
    }
	
	//download data norma harga borong
    public function dataNormaHargaBorongAction()
    {
        $this->_listAction();
    }
	
	//download data norma biaya
    public function dataNormaBiayaAction()
    {
        $this->_listAction();
    }
	
	//download data norma kastrasi sanitasi
    public function dataNormaKastrasiSanitasiAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen premi topo
    public function dataNormaPanenPremiTopographyAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen produktifitas pemanen
    public function dataNormaPanenProduktifitasPemanenAction()
    {
        $this->_listAction();
    }
	
	//download data norma alat kerja non panen
    public function dataNormaAlatKerjaNonPanenAction()
    {
        $this->_listAction();
    }
	
	//download data norma alat kerja panen
    public function dataNormaAlatKerjaPanenAction()
    {
        $this->_listAction();
    }
	
	//download data norma WRA
    public function dataNormaWraAction()
    {
        $this->_listAction();
    }
	
    //download data norma Checkroll
    public function dataNormaCheckrollAction()
    {
        $this->_listAction();
    }
	
	//download data report Checkroll
    public function dataReportCheckrollAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen OER BJR
    public function dataNormaPanenOerBjrAction()
    {
        $this->_listAction();
    }
	
	//download data norma VRA
    public function dataNormaVraAction()
    {
        $this->_listAction();
    }
	
	//download data norma pupuk < TBM 2
    public function dataNormaPupukTbmLessAction()
    {
        $this->_listAction();
    }
	
	//download data norma pupuk TBM 2 - TM
    public function dataNormaPupukTbmTmAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen premi mandor
    public function dataNormaPanenPremiMandorAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen variabel
    public function dataNormaPanenVariabelAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen loading
    public function dataNormaPanenLoadingAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen supervisi
    public function dataNormaPanenSupervisiAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen krani buah
    public function dataNormaPanenKraniBuahAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen premi langsir
    public function dataNormaPanenPremiLangsirAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen cost unit
    public function dataNormaPanenCostUnitAction()
    {
        $this->_listAction();
    }
	
	//download data norma panen premi pemanen
    public function dataNormaPanenPremiPemanenAction()
    {
        $this->_listAction();
    }
	
	//download data norma infrastruktur
    public function dataNormaInfrastrukturAction()
    {
        $this->_listAction();
    }
	
	//download data norma perkerasan jalan
    public function dataNormaPerkerasanJalanAction()
    {
        $this->_listAction();
    }
	
	//download data norma SPH
    public function dataNormaSphAction()
    {
        $this->_listAction();
    }
	
	//download data report VRA
    public function dataReportVraAction()
    {
        $this->_listAction();
    }
	
	//download data perencanaan produksi
    public function dataPerencanaanProduksiAction()
    {
        $this->_listAction();
    }
	
	//download data RKT	CAPEX
    public function dataRktCapexAction()
    {
        $this->_listAction();
    }
	
	//download data report norma perkerasan jalan
    public function dataReportNormaPerkerasanJalanAction()
    {
        $this->_listAction();
    }
	
	//Added by Ardo 23/08/2016 - tempalte norma perkerasan jalan
	//download data report norma perkerasan jalan
    public function templateReportNormaPerkerasanJalanAction()
    {
        $this->_listAction();
    }
	
	//download data report RKT CAPEX
    public function dataReportRktCapexAction()
    {
        $this->_listAction();
    }
	
	//download data RKT OPEX
    public function dataRktOpexAction()
    {
        $this->_listAction();
    }
	
	//download data RKT VRA
    public function dataRktVraAction()
    {
        $this->_listAction();
    }
	
	//download data RKT CSR
    public function dataRktCsrAction()
    {
        $this->_listAction();
    }
	
	//download data RKT Internal Relation
    public function dataRktInternalRelationAction()
    {
        $this->_listAction();
    }
	
	//download data RKT SHE
    public function dataRktSheAction()
    {
        $this->_listAction();
    }

	//download data report alokasi Checkroll
    public function dataReportCheckrollAlokasiAction()
    {
        $this->_listAction();
    }

	//download data RKT Pupuk HA
    public function dataRktPupukHaAction()
    {
        $this->_listAction();
    }

	//download data RKT Pupuk distribusi biaya normal
    public function dataRktPupukDistribusiBiayaNormalAction()
    {
        $this->_listAction();
    }
	
	public function dataRktPupukKgNormalAction()
    {
        $this->_listAction();
    }

	//download data RKT Pupuk distribusi biaya sisip
    public function dataRktPupukDistribusiBiayaSisipAction()
    {
        $this->_listAction();
    }

	//download data RKT Pupuk distribusi biaya gabungan
    public function dataRktPupukDistribusiBiayaGabunganAction()
    {
        $this->_listAction();
    }

	//download data RKT OPEX VRA
    public function dataRktOpexVraAction()
    {
        $this->_listAction();
    }

	//download data norma distribusi VRA non infra
    public function dataNormaDistribusiVraNonInfraAction()
    {
        $this->_listAction();
    }
	
	//download data norma distribusi VRA
    public function dataNormaDistribusiVraAction()
    {
        $this->_listAction();
    }
	
	//download data pupuk Kg Sisipan
    public function dataRktPupukKgSisipAction()
    {
        $this->_listAction();
    }
	
	//download data RKT Manual Non Infra
    public function dataRktManualNonInfraAction()
    {
        $this->_listAction();
    }
	
	//download data RKT Manual SISIP
    public function dataRktManualSisipAction()
    {
        $this->_listAction();
    }
	
	//download data RKT Kastrasi Sanitasi
    public function dataRktKastrasiSanitasiAction()
    {
        $this->_listAction();
    }
	
	//download data RKT Manual Infra
    public function dataRktManualInfraAction()
    {
		$this->_listAction();
    }
	
	//download data RKT LC
	public function dataRktLcAction(){
		$this->_listAction();
	}
	
	//download data RKT LC
	public function dataRktPanenAction(){
		$this->_listAction();
	}
	
	//download data RKT LC
	public function dataRktTanamManualAction(){
		$this->_listAction();
	}

	//download data RKT TANAM
	public function dataRktTanamAction(){
		$this->_listAction();
	}

	//download data RKT Perkerasan Jalan
	public function dataRktPerkerasanJalanAction(){
		$this->_listAction($params);
	}
    
    //download data RKT Manual Non Infra + Opsi
    public function dataRktManualNonInfraOpsiAction()
    {
        $this->_listAction();
    }

    // yaddi.surahman@tap-agri.co.id --- 2017-08-21
    //download data Norma Pupuk TBM Rekomendasi
    public function dataNormaPupukTbmRekomendasiAction()
    {
        $this->_listAction();
    }
    // yaddi.surahman@tap-agri.co.id --- 2017-08-21
	//download data Norma Pupuk TBM Rekomendasi
    public function dataNormaInsentivePanenAction()
    {
        $this->_listAction();
    }

    //download data HO Actual Outlook
    public function dataHoActOutlookAction() {
        $this->_listAction();
    }
	
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
    //menampilkan list
	private function _listAction()
    {
        $table = new Application_Model_Download();
        $params = $this->_request->getParams();
		$this->_helper->layout->disableLayout();
		$this->view->data = $table->initList($params);
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
