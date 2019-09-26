<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Download
Function 			:	////////////////////////////////////////////////// DOWNLOAD TEMPLATE //////////////////////////////////////////////////
					    - 04/07	: initTemplatePerencanaanProduksi			: download template perencanaan produksi
						
						////////////////////////////////////////////////// DOWNLOAD DATA //////////////////////////////////////////////////
					    - 06/05	: initDataAsset								: download data casset
						- 06/05	: initDataCatu								: download data catu
						- 06/05	: initDataHaStatement						: download data ha statement
						- 06/05	: initDataMaterial							: download data material
						- 06/05	: initDataTarifTunjangan					: download data tarif tunjangan
						- 07/05	: initDataActualPanen						: download data panen aktual
						- 15/05	: initDataActivityMapping					: download data mapping aktivitas
						- 03/06	: initDataHaStatementDetail					: download data ha statement detail
						- 07/06	: initDataNormaBasic						: download data norma dasar
						- 10/06	: initDataNormaHargaBarang					: download data norma harga barang
						- 10/06	: initDataNormaHargaBorong					: download data norma harga borong
						- 11/06	: initDataNormaBiaya						: download data norma biaya
						- 11/06	: initDataNormaAlatKerjaNonPanen			: download data norma alat kerja non panen
						- 11/06	: initDataNormaAlatKerjaPanen				: download data norma alat kerja panen
						- 13/06	: initDataNormaWra							: download data norma WRA
						- 13/06	: initDataNormaCheckroll					: download data norma Checkroll
						- 17/06	: initDataReportCheckroll					: download data report Checkroll
						- 18/06	: initDataNormaPanenOerBjr					: download data norma panen OER BJR
						- 21/06	: initDataNormaVra							: download data norma VRA
						- 24/06	: initDataNormaPupukTbmLess					: download data norma pupuk < TBM 2
						- 24/06	: initDataNormaPupukTbmTm					: download data norma pupuk TBM 2 - TM
						- 25/06	: initDataNormaPanenPremiMandor				: download data norma panen premi mandor
						- 25/06	: initDataNormaPanenVariabel				: download data norma panen variabel
						- 25/06	: initDataNormaInfrastruktur				: download data norma infrastruktur
						- 26/06	: initDataNormaPanenLoading					: download data norma panen loading
						- 26/06	: initDataNormaPanenSupervisi				: download data norma panen supervisi
						- 27/06	: initDataNormaPanenKraniBuah				: download data norma panen krani buah
						- 27/06	: initDataNormaPanenPremiLangsir			: download data norma panen premi langsir
						- 28/06	: initDataNormaPerkerasanJalan				: download data norma perkerasan jalan
						- 28/06	: initDataNormaPanenCostUnit				: download data norma panen cost unit
						- 28/06	: initDataNormaPanenPremiPemanen			: download data norma panen premi pemanen
						- 03/07	: initDataReportVra							: download data report VRA
						- 04/07	: initDataPerencanaanProduksi				: download data perencanaan produksi
						- 08/07	: initDataRktCapex							: download data RKT CAPEX
						- 08/07	: initDataReportNormaPerkerasanJalan		: download data report perkerasan jalan
						- 09/07	: initDataReportRktCapex					: download data report RKT CAPEX
						- 10/07	: initDataRktOpex							: download data RKT OPEX
						- 11/07	: initDataRktVra							: download data RKT VRA
						- 15/07	: initDataRktCsr							: download data RKT CSR
						- 15/07	: initDataRktInternalRelation				: download data RKT Internal Relation
						- 15/07	: initDataRktShe							: download data RKT SHE
						- 15/07	: initDataReportCheckrollAlokasi			: download data checkroll alokasi
						- 18/07	: initDataRktPupukHa						: download data RKT Pupuk HA
						- 18/07	: initDataRktPupukDistribusiBiayaNormal		: download data RKT Pupuk distribusi biaya normal
						- 18/07	: initDataRktPupukDistribusiBiayaSisip		: download data RKT Pupuk distribusi biaya sisip
						- 18/07	: initDataRktPupukDistribusiBiayaGabungan	: download data RKT Pupuk distribusi biaya gabungan
						- 22/07	: initDataRktPupukKgNormal					: download data RKT Pupuk kg normal
						- 22/07	: initDataRktOpexVra						: download data RKT OPEX VRA
						- 22/07	: initDataNormaDistribusiVraNonInfra		: download data norma distribusi VRA non infra
						- 22/07	: initDataRktPupukKgSisip					: download data RKT Pupuk kg sisip
						- 22/07	: initDataNormaDistribusiVra  				: download data norma distribusi VRA
						- 24/07	: initDataRktManualNonInfra					: download data RKT Manual Non Infra
						- 25/07	: initDataRktManualInfra					: download data RKT Manual Infra
						- 26/07	: initDataRktManualNonInfraOpsi				: download data RKT Manual Non Infra + Opsi
						- 02/08	: initDataRktLc								: download data RKT LC 
						- 14/08	: initDataRktPanen							: download data RKT Panen 
						- 16/08	: initDataRktTanamManual					: download data RKT Tanam Manual 
						- 16/08	: initDataRktTanam							: download data RKT Tanam 
						- 16/08	: initDataRktPerkerasanJalan				: download data RKT Perkerasan Jalan
						- 05/08	: initDataNormaPanenPremiTopography			: download data norma panen premi topography
						- 06/08	: initDataNormaPanenProduktifitasPemanen	: download data norma panen produktifitas pemanen
						- 23/08	: initDataNormaPanenProduktifitasPemanen	: download data norma panen produktifitas pemanen
						- 08/07	: initTemplateReportNormaPerkerasanJalan	: template data report perkerasan jalan
						- initList											: inisialisasi list yang akan ditampilkan
						- getList											: menampilkan list
						- getInput			: setting input untuk region dan maturity stage
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	02/05/2013
Update Terakhir		:	23/08/2016
Revisi				:	

ARW	23/08/2016		:	-	Perubahan proses perhitungan pada perkerasan jalan yang menggunakan pilihan external
=========================================================================================================================
*/
class Application_Model_Download
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
	
	////////////////////////////////////////////////// DOWNLOAD TEMPLATE //////////////////////////////////////////////////

	//download template perencanaan produksi
    public function initTemplatePerencanaanProduksi($params = array())
    {
        $data = new Application_Model_PerencanaanProduksi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getTemplateData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getTemplateData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('DOWNLOAD TEMPLATE SUCCESS', 'TEMPLATE PERENCANAAN PRODUKSI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('DOWNLOAD TEMPLATE FAILED', 'TEMPLATE PERENCANAAN PRODUKSI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	////////////////////////////////////////////////// DOWNLOAD DATA //////////////////////////////////////////////////

	//download data asset
    public function initDataAsset($params = array())
    {
        $data = new Application_Model_Asset();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER ASET', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER ASET', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data catu
    public function initDataCatu($params = array())
    {
        $data = new Application_Model_Catu();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER CATU', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER CATU', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data ha statement
    public function initDataHaStatement($params = array())
    {
        $data = new Application_Model_HaStatement();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER HA STATEMENT', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER HA STATEMENT', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data material
    public function initDataMaterial($params = array())
    {
        $data = new Application_Model_Material();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER MATERIAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER MATERIAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data tarif tunjangan
    public function initDataTarifTunjangan($params = array())
    {
        $data = new Application_Model_TarifTunjangan();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER TARIF TUNJANGAN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER TARIF TUNJANGAN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data panen aktual
    public function initDataActualPanen($params = array())
    {
        $data = new Application_Model_ActualPanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER PANEN AKTUAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER PANEN AKTUAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data mapping aktivitas
    public function initDataActivityMapping($params = array())
    {
        $data = new Application_Model_ActivityMapping();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER MAPPING AKTIVITAS', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER MAPPING AKTIVITAS', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data ha statement detail
    public function initDataHaStatementDetail($params = array())
    {
        $data = new Application_Model_HaStatementDetail();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'MASTER HA STATEMENT DETAIL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'MASTER HA STATEMENT DETAIL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma basic
    public function initDataNormaBasic($params = array())
    {
        $data = new Application_Model_NormaBasic();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA DASAR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA DASAR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma harga barang
    public function initDataNormaHargaBarang($params = array())
    {
        $data = new Application_Model_NormaHargaBarang();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA HARGA BARANG', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA HARGA BARANG', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma harga borong
    public function initDataNormaHargaBorong($params = array())
    {
        $data = new Application_Model_NormaHargaBorong();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA HARGA BORONG', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA HARGA BORONG', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma biaya
    public function initDataNormaBiaya($params = array())
    {
        $data = new Application_Model_NormaBiaya();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA BIAYA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA BIAYA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma kastrasi sanitasi
    public function initDataNormaKastrasiSanitasi($params = array())
    {
        $data = new Application_Model_NormaKastrasiSanitasi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA BIAYA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA BIAYA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen premi topography
    public function initDataNormaPanenPremiTopography($params = array())
    {
        $data = new Application_Model_NormaPanenPremiTopography();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN PREMI TOPOGRAPHY', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN PREMI TOPOGRAPHY', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen produktifitas pemanen
    public function initDataNormaPanenProduktifitasPemanen($params = array())
    {
        $data = new Application_Model_NormaPanenProduktifitasPemanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN PRODUKTIFITAS PEMANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN PRODUKTIFITAS PEMANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma alat kerja non panen
    public function initDataNormaAlatKerjaNonPanen($params = array())
    {
        $data = new Application_Model_NormaAlatKerjaNonPanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA ALAT KERJA NON PANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA ALAT KERJA NON PANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma alat kerja panen
    public function initDataNormaAlatKerjaPanen($params = array())
    {
        $data = new Application_Model_NormaAlatKerjaPanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA ALAT KERJA PANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA ALAT KERJA PANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma WRA
    public function initDataNormaWra($params = array())
    {
        $data = new Application_Model_NormaWra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData1($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData1($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA WRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA WRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma Checkroll
    public function initDataNormaCheckroll($params = array())
    {
        $data = new Application_Model_NormaCheckroll();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA CHECKROLL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA CHECKROLL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data report Checkroll
    public function initDataReportCheckroll($params = array())
    {
        $data = new Application_Model_ReportCheckroll();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'REPORT CHECKROLL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'REPORT CHECKROLL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen OER BJR
    public function initDataNormaPanenOerBjr($params = array())
    {
        $data = new Application_Model_NormaPanenOerBjr();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN OER BJR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN OER BJR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma VRA
    public function initDataNormaVra($params = array())
    {
        $data = new Application_Model_NormaVra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma pupuk < TBM 2
    public function initDataNormaPupukTbmLess($params = array())
    {
        $data = new Application_Model_NormaPupukTbmLess();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PUPUK TBM2 LESS', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PUPUK TBM2 LESS', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma pupuk TBM 2 - TM
    public function initDataNormaPupukTbmTm($params = array())
    {
        $data = new Application_Model_NormaPupukTbmTm();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PUPUK TBM2 - TM', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PUPUK TBM2 - TM', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen premi mandor
    public function initDataNormaPanenPremiMandor($params = array())
    {
        $data = new Application_Model_NormaPanenPremiMandor();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN PREMI MANDOR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN PREMI MANDOR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	//download data norma infrastruktur
    public function initDataNormaInfrastruktur($params = array())
    {
        $data = new Application_Model_NormaInfrastruktur();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA INFRASTRUKTUR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA INFRASTRUKTUR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen variabel
    public function initDataNormaPanenVariabel($params = array())
    {
        $data = new Application_Model_NormaPanenVariabel();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN VARIABEL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN VARIABEL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen loading
    public function initDataNormaPanenLoading($params = array())
    {
        $data = new Application_Model_NormaPanenLoading();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN LOADING', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN LOADING', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen supervisi
    public function initDataNormaPanenSupervisi($params = array())
    {
        $data = new Application_Model_NormaPanenSupervisi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN SUPERVISI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN SUPERVISI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen krani buah
    public function initDataNormaPanenKraniBuah($params = array())
    {
        $data = new Application_Model_NormaPanenKraniBuah();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN KRANI BUAH', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN KRANI BUAH', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen premi langsir
    public function initDataNormaPanenPremiLangsir($params = array())
    {
        $data = new Application_Model_NormaPanenPremiLangsir();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN PREMI LANGSIR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN PREMI LANGSIR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }

	//download data norma perkerasan jalan
    public function initDataNormaPerkerasanJalan($params = array())
    {
		$data = new Application_Model_NormaPerkerasanJalan();
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PERKERASAN JALAN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PERKERASAN JALAN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen cost unit
    public function initDataNormaPanenCostUnit($params = array())
    {
        $data = new Application_Model_NormaPanenCostUnit();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN COST UNIT', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN COST UNIT', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma panen premi pemanen
    public function initDataNormaPanenPremiPemanen($params = array())
    {
        $data = new Application_Model_NormaPanenPremiPemanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PANEN PREMI PEMANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PANEN PREMI PEMANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma SPH
    public function initDataNormaSph($params = array())
    {
        $data = new Application_Model_NormaSph();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA SPH', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA SPH', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data report VRA
    public function initDataReportVra($params = array())
    {
        $data = new Application_Model_ReportVra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'REPORT VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'REPORT VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data perencanaan produksi
    public function initDataPerencanaanProduksi($params = array())
    {
        $data = new Application_Model_PerencanaanProduksi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'PERENCANAAN PRODUKSI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'PERENCANAAN PRODUKSI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT CAPEX
    public function initDataRktCapex($params = array())
    {
        $data = new Application_Model_RktCapex();
		$sumArray = array();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//sub total
					if (($lastPk)&&($lastPk <> $row['PERIOD_BUDGET'].$row['BA_CODE'].$row['COA_CODE'])){
						$result['rows'][] = $sumArray;
						$sumArray = array();
					}	
					if (($lastBa)&&($lastBa <> $row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET REGION_CODE BA_CODE COMPANY_NAME COA_CODE COA_DESC';
					$descKolom = 'ASSET_CODE ASSET_DESC DETAIL_SPESIFICATION UOM URGENCY_CAPEX PRICE';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$descKolom) == 1 ){// set value for sub total & total kolom
							$sumArray[$subId]= 'Sub Total';
							$grandSumArray[$subId]='Total';
						}else if(preg_match("/$subId/",$pkKolom) == 1 ){						
							$sumArray[$subId] =$value;
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$sumArray[$subId]+=$value;
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastPk = $row['PERIOD_BUDGET'].$row['BA_CODE'].$row['COA_CODE'];
					$lastBa = $row['BA_CODE'];
				}
				$result['rows'][] = $sumArray;
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT CAPEX', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT CAPEX', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma perkerasan jalan
    public function initDataReportNormaPerkerasanJalan($params = array())
    {
        $data = new Application_Model_ReportNormaPerkerasanJalan();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'REPORT NORMA PERKERASAN JALAN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'REPORT NORMA PERKERASAN JALAN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//Added by Ardo 23/08/2016 :
	//template data norma perkerasan jalan
    public function initTemplateReportNormaPerkerasanJalan($params = array())
    {
		$result['params'] = $params;
        $data = new Application_Model_ReportNormaPerkerasanJalan();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			
		} catch (Exception $e) {
			
		}
		
        return $result;
    }
	
	//download data report RKT CAPEX
    public function initDataReportRktCapex($params = array())
    {
        $data = new Application_Model_ReportRktCapex();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'REPORT RKT CAPEX', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'REPORT RKT CAPEX', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT OPEX
    public function initDataRktOpex($params = array())
    {
        $data = new Application_Model_RktOpex();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME COA_CODE COA_DESC GROUP_BUM_DESCRIPTION';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT OPEX', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT OPEX', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT VRA
    public function initDataRktVra($params = array())
    {
        $data = new Application_Model_RktVra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME VRA_SUB_CAT_DESCRIPTION VRA_CODE VRA_TYPE DESCRIPTION_VRA_TYPE';
					$blKolom = 'TAHUN_ALAT  UOM';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}elseif(preg_match("/$subId/",$blKolom) == 1 ){
							$grandSumArray[$subId] ='';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT CSR
    public function initDataRktCsr($params = array())
    {
        $data = new Application_Model_RktCsr();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME COA_CODE COA_DESC GROUP_CODE GROUP_DESC SUB_GROUP_CODE SUB_GROUP_DESC ACTIVITY_DETAIL';
					$blKolom = 'KETERANGAN';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}elseif(preg_match("/$subId/",$blKolom) == 1 ){
							$grandSumArray[$subId] ='';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT CSR', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT CSR', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Internal Relation
    public function initDataRktInternalRelation($params = array())
    {
        $data = new Application_Model_RktInternalRelation();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME COA_CODE COA_DESC GROUP_CODE GROUP_DESC SUB_GROUP_CODE SUB_GROUP_DESC ACTIVITY_DETAIL';
					$blKolom = 'KETERANGAN';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}elseif(preg_match("/$subId/",$blKolom) == 1 ){
							$grandSumArray[$subId] ='';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT INTERNAL RELATION', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT INTERNAL RELATION', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT SHE
    public function initDataRktShe($params = array())
    {
        $data = new Application_Model_RktShe();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME COA_CODE COA_DESC GROUP_CODE GROUP_DESC SUB_GROUP_CODE SUB_GROUP_DESC ACTIVITY_DETAIL';
					$blKolom = 'KETERANGAN';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}elseif(preg_match("/$subId/",$blKolom) == 1 ){
							$grandSumArray[$subId] ='';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT SHE', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT SHE', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data checkroll alokasi
    public function initDataReportCheckrollAlokasi($params = array())
    {
        $data = new Application_Model_ReportCheckrollAlokasi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'CHECKROLL ALOKASI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'CHECKROLL ALOKASI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk HA
    public function initDataRktPupukHa($params = array())
    {
        $data = new Application_Model_RktPupukHa();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK HA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK HA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk distribusi biaya normal
    public function initDataRktPupukDistribusiBiayaNormal($params = array())
    {
        $data = new Application_Model_RktPupukDistribusiBiayaNormal();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK DISTRIBUSI BIAYA NORMAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK DISTRIBUSI BIAYA NORMAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk distribusi biaya normal
    public function initDataRktPupukKgNormal($params = array())
    {
        $data = new Application_Model_RktPupukKgNormal();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK KG NORMAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK KG NORMAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk kg sisip
    public function initDataRktPupukKgSisip($params = array())
    {
		$data = new Application_Model_RktPupukKgSisip();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK KG SISIP', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK KG SISIP', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk distribusi biaya sisip
    public function initDataRktPupukDistribusiBiayaSisip($params = array())
    {
        $data = new Application_Model_RktPupukDistribusiBiayaSisip();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK DISTRIBUSI BIAYA SISIP', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK DISTRIBUSI BIAYA SISIP', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT pupuk distribusi biaya gabungan
    public function initDataRktPupukDistribusiBiayaGabungan($params = array())
    {
        $data = new Application_Model_RktPupukDistribusiBiayaGabungan();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE TOPOGRAPHY TAHUN_TANAM_M  TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PUPUK DISTRIBUSI BIAYA GABUNGAN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PUPUK DISTRIBUSI BIAYA GABUNGAN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT OPEX VRA
    public function initDataRktOpexVra($params = array())
    {
        $data = new Application_Model_RktOpexVra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME COA_CODE COA_DESC GROUP_BUM_DESCRIPTION';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT OPEX - VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT OPEX - VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma distribusi VRA non infra
    public function initDataNormaDistribusiVraNonInfra($params = array())
    {
        $data = new Application_Model_NormaDistribusiVraNonInfra();
		
		try {
			$result = array();
			
			$sql = "SELECT COUNT(*) FROM ({$data->getDataHeader($params)})";
			$result['count'] = $this->_db->fetchOne($sql);

			$rows = $this->_db->fetchAll("{$data->getDataHeader($params)}");
			$rowsAfd = $this->_db->fetchAll("{$data->getDataAfdeling($params)}");
			$tabs = $this->_db->fetchAll("{$data->getDataAfd($params)}");
			
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}		
			
			if (!empty($rowsAfd)) {
				foreach ($rowsAfd as $idx => $row) {
					$result['rowsAfd'][$row['TRX_CODE']][] = $row;
				}
			}
			
			if (!empty($tabs)) {
				foreach ($tabs as $idx => $tab) {
					$result['tabs'][] = $tab;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA DISTRIBUSI VRA - NON INFRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA DISTRIBUSI VRA - NON INFRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data norma distribusi VRA
    public function initDataNormaDistribusiVra($params = array())
    {
        $data = new Application_Model_NormaDistribusiVra();
		
		try {

			// yaddi.surahman@tap-agri.co.id
			// $sql = "SELECT COUNT(*) FROM ({$data->getDataHeader($params)})";
			// $result['count'] = $this->_db->fetchOne($sql);
			
			// $rows = $this->_db->fetchAll("{$data->getDataHeader($params)}");
			// $rowsAfd = $this->_db->fetchAll("{$data->getDataAfdeling($params)}");

			$tabs = $this->_db->fetchAll("{$data->getAfd($params)}");
			
			// if (!empty($rows)) {
			// 	foreach ($rows as $idx => $row) {
			// 		$result['rows'][] = $row;
			// 	}
			// }		
			
			// if (!empty($rowsAfd)) {
			// 	foreach ($rowsAfd as $idx => $row) {
			// 		$result['rowsAfd'][$row['TRX_CODE']][] = $row;
			// 	}
			// }
			
			if (!empty($tabs)) {
				foreach ($tabs as $idx => $tab) {
					$result['tabs'][] = $tab;
				}
			} 
			
			// yaddi.surahman@tap-agri.co.id
			$rows = $data->getData2($params);

			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}

			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA DISTRIBUSI VRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA DISTRIBUSI VRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}

        return $result;
    }
	
	//download data RKT Manual - Non Infra
    public function initDataRktManualNonInfra($params = array())
    {
        $data = new Application_Model_RktManualNonInfra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGE BA_CODE COMPANY_NAME ACTIVITY_CODE ACTIVITY_DESC AFD_CODE BLOCK_CODE TAHUN_TANAM_M TAHUN_TANAM_Y TOPOGRAPHY_DESC LAND_TYPE_DESC MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log file
			$this->_global->logFile('EXPORT SUCCESS', 'RKT MANUAL - NON INFRA');
		} catch (Exception $e) {
			//log file
			$this->_global->logFile('EXPORT FAILED', 'RKT MANUAL - NON INFRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Manual Sisip
    public function initDataRktManualSisip($params = array())
    {
        $data = new Application_Model_RktManualSisip();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGE BA_CODE COMPANY_NAME ACTIVITY_CODE ACTIVITY_DESC AFD_CODE BLOCK_CODE TAHUN_TANAM_M TAHUN_TANAM_Y TOPOGRAPHY_DESC LAND_TYPE_DESC MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log file
			$this->_global->logFile('EXPORT SUCCESS', 'RKT MANUAL SISIP');
		} catch (Exception $e) {
			//log file
			$this->_global->logFile('EXPORT FAILED', 'RKT MANUAL SISIP', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Kastrasi Sanitasi
    public function initDataRktKastrasiSanitasi($params = array())
    {
        $data = new Application_Model_RktKastrasiSanitasi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGE BA_CODE COMPANY_NAME ACTIVITY_CODE ACTIVITY_DESC AFD_CODE BLOCK_CODE TAHUN_TANAM_M TAHUN_TANAM_Y TOPOGRAPHY_DESC LAND_TYPE_DESC MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log file
			$this->_global->logFile('EXPORT SUCCESS', 'RKT KASTRASI SANITASI');
		} catch (Exception $e) {
			//log file
			$this->_global->logFile('EXPORT FAILED', 'RKT KASTRASI SANITASI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT LC
    public function initDataRktLc($params = array())
    {
		$data = new Application_Model_RktLc();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT LC', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT LC', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data Tanam Manual
    public function initDataRktTanamManual($params = array())
    {
		$data = new Application_Model_RktTanamManual();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll($data->getDataDownload($params)); 
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT TANAM MANUAL', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT TANAM MANUAL', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }

	//download data Tanam
    public function initDataRktTanam($params = array())
    {
		$data = new Application_Model_RktTanam();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll($data->getData($params)); 
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT TANAM', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT TANAM', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }

	//download data Perkerasan Jalan
    public function initDataRktPerkerasanJalan($params = array())
    {
		$data = new Application_Model_RktPerkerasanJalan();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PERKERASAN JALAN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PERKERASAN JALAN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Panen
    public function initDataRktPanen($params = array())
    {
		$data = new Application_Model_RktPanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT PANEN', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT PANEN', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Manual - Infra
    public function initDataRktManualInfra($params = array())
    {
		$data = new Application_Model_RktManualInfra();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE_DESC TOPOGRAPHY_DESC TAHUN_TANAM_M TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT MANUAL - INFRA', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT MANUAL - INFRA', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }
	
	//download data RKT Manual - Non Infra + Opsi
    public function initDataRktManualNonInfraOpsi($params = array())
    {
		$data = new Application_Model_RktManualNonInfraOpsi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getDataDownload($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getDataDownload($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					//grand total
					if (($lastBa)&&($lastBa <> $row['PERIOD_BUDGET'].$row['BA_CODE'])){
						$result['rows'][] = $grandSumArray;
						$grandSumArray = array();
					}						
					//cetak data
					$result['rows'][] = $row;
					
					//jika kolom2 ini, value tidak di cetak
					$pkKolom = 'PERIOD_BUDGET BA_CODE COMPANY_NAME AFD_CODE BLOCK_CODE LAND_TYPE_DESC TOPOGRAPHY_DESC TAHUN_TANAM_M TAHUN_TANAM_Y MATURITY_STAGE_SMS1 MATURITY_STAGE_SMS2';
					foreach ($row as $subId=>$value) {
						if(preg_match("/$subId/",$pkKolom) == 1 ){
							$grandSumArray[$subId] ='Total';
						}else{ //count total and subtotal
							$grandSumArray[$subId]+=$value;
						}
					}
					
					//last data for comparison
					$lastBa = $row['PERIOD_BUDGET'].$row['BA_CODE'];
				}
				$result['rows'][] = $grandSumArray;
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'RKT MANUAL - NON INFRA + OPSI', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'RKT MANUAL - NON INFRA + OPSI', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }	



	//download data norma pupuk TBM 2 - TM
    public function initDataNormaPupukTbmRekomendasi($params = array())
    {
        $data = new Application_Model_NormaPupukTbmRekomendasi();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PUPUK TBM2 - TM', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PUPUK TBM2 - TM', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }

	//download data norma pupuk TBM 2 - TM
    public function initDataNormaInsentivePanen($params = array())
    {
        $data = new Application_Model_NormaInsentivePanen();
		
		try {
			$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
			$result['count'] = $this->_db->fetchOne($sql);
			
			$rows = $this->_db->fetchAll("{$data->getData($params)}");
			
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) {
					$result['rows'][] = $row;
				}
			}
			
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'NORMA PUPUK TBM2 - TM', '', '');
		} catch (Exception $e) {
			//log DB
			$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'NORMA PUPUK TBM2 - TM', '', $e->getCode());
			
			//error log file
			$this->_global->errorLogFile($e->getMessage());
		}
		
        return $result;
    }

    //download data HO Act Outlook
    public function initDataHoActOutlook($params = array()) {
    	$data = new Application_Model_HoActOutlook();

    	try {
    		echo $data->getData($params);
    		$sql = "SELECT COUNT(*) FROM ({$data->getData($params)})";
    		$result['count'] = $this->_db->fetchOne($sql);

    		$rows = $this->_db->fetchAll("{$data->getData($params)}");

    		if (!empty($rows)) {
    			foreach ($rows as $idx => $row) {
    				$result['rows'][] = $row;
    			}
    		}

    		$this->_global->insertLog('EXPORT DATA TO CSV SUCCESS', 'HO ACT OUTLOOK', '', '');
    	} catch (Exception $e) {
    		$this->_global->insertLog('EXPORT DATA TO CSV FAILED', 'HO ACT OUTLOOK', '', $e->getCode());
    		$this->_global->errorLogFile($e->getMessage());
    	}

    	return $result;
    }

	
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
	//inisialisasi list yang akan ditampilkan
	public function initList($params = array())
    {
        $result = array();

        $initAction = 'init' . str_replace(' ', '', ucwords(str_replace('-', ' ', $params['action'])));
        $result = $this->$initAction($params);

        return $result;
    }
	////////////////////////////////////////////////// JANGAN DIUBAH //////////////////////////////////////////////////
}
