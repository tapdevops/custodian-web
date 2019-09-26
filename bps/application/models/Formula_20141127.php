<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Formula
Function 			:	- 24/04	: cal_NormaHarga_QtyHa						: hitung Qty/HA utk norma harga
						- 24/04	: cal_NormaHargaBarang_HargaInflasi			: hitung inflasi harga dasar
						- 29/04	: get_Semester								: mencari semester dari suatu bulan
						- 29/04	: get_MaturityStage							: mencari maturity stage
						- 30/04	: cal_NormaAlatKerjaPanen_TotalRupiah		: hitung total rupiah alat kerja panen
						- 30/04	: cal_NormaAlatKerjaNonPanen_TotalRupiah	: hitung total rupiah alat kerja non panen
						- 08/05	: cal_NormaInfra_QtyHa						: hitung Qty / Ha untuk norma Infra
						- 10/05 : get_NormaHargaBorong_Harga				: hitung harga berdasarkan harga borong
						- 10/05 : cal_NormaInfra_RpQtyExternal				: hitung Rp / Qty untuk harga external norma Infra
						- 13/05 : cal_NormaInfra_RpHaExternal				: hitung Rp / Ha untuk harga external norma Infra
						- 10/05	: cal_NormaPupukTbmTm_JumlahKg				: hitung jumlah kg untuk norma pupuk TBM + TM
						- 10/05	: cal_NormaPupukTbmTm_Biaya					: hitung total biaya untuk norma pupuk TBM + TM
						- 30/05	: cal_MasterCatu_CatuBulan					: hitung catu beras / bulan
						- 30/05	: cal_MasterCheckrollHk_Hk					: hitung jumlah HK dalam 1 tahun
						- 30/05	: cal_MasterCheckrollHk_Hke					: hitung jumlah HKE dalam 1 tahun
						- 31/05	: cal_Upload_Sph							: hitung SPH per blok
						- 10/06	: get_RegionCode							: mencari region code dari suatu BA
						- 11/06	: cal_NormaBiaya_QtyHa						: hitung Qty / Ha untuk norma biaya
						- 11/06	: cal_NormaBiaya_RpHa						: hitung Rp / Ha untuk norma biaya
						- 11/06	: cal_NormaBiaya_RpHaRotasi					: hitung Rp / Ha / Rotasi untuk norma biaya
						- 13/06	: cal_NormaWra_QtyTahun						: hitung Qty / Tahun untuk norma WRA
						- 13/06	: cal_NormaWra_HargaQtyTahun				: hitung Harga Qty / Tahun untuk norma WRA
						- 13/06	: cal_NormaWra_RpQty						: hitung Rp / Qty untuk norma WRA
						- 13/06	: cal_NormaCheckroll_MppRekrut				: hitung MPP rekrut untuk norma checkroll
						- 14/06	: cal_NormaCheckroll_GpInflasi				: hitung GP inflasi untuk norma checkroll
						- 14/06	: cal_NormaCheckroll_Tunjangan				: hitung tunjangan untuk norma checkroll
						- 14/06	: cal_NormaCheckroll_PkUmum					: hitung tunjangan pekerjaan umum untuk norma checkroll
						- 14/06	: cal_NormaCheckroll_TotalGpMpp				: hitung Total GP MPP untuk norma checkroll
						- 17/06	: cal_NormaCheckroll_RpHk					: hitung Rp/HK untuk norma checkroll
						- 17/06	: cal_NormaCheckroll_TotalTunjanganPkUmum	: hitung total tunjangan PK Umum untuk norma checkroll
						- 17/06	: cal_NormaCheckroll_DisYear				: hitung total tunjangan PK Umum 1 tahun untuk norma checkroll
						- 18/06	: cal_NormaPanenOerBjr_OverBasisJanjang		: hitung over basis janjang untuk norma panen OER BJR
						- 18/06	: cal_NormaPanenOerBjr_JanjangOperation		: hitung janjang operation untuk norma panen OER BJR
						- 18/06	: cal_NormaPanenOerBjr_Nilai				: hitung nilai untuk norma panen OER BJR
						- 24/06	: cal_NormaPupukTbmLess_Jumlah				: hitung jumlah untuk norma pupuk < TBM 2
						- 24/06	: cal_NormaPupukTbmLess_RpRotasi			: hitung Rp/Rotasi untuk norma pupuk < TBM 2
						- 24/06	: cal_NormaPupukTbmLess_RpTahun				: hitung Rp/Tahun untuk norma pupuk < TBM 2
						- 24/06 : cal_NormaInfrastruktur_RpHaEks			: hitung Rp / Ha untuk norma infrastruktur eksternal
						- 24/06 : cal_NormaInfrastruktur_QtyHaEks			: hitung Qty / Ha untuk norma infrastruktur eksternal
						- 25/06 : cal_NormaInfrastruktur_RpQtyInt			: hitung Rp / Ha untuk norma infrastruktur internal
						- 25/06 : cal_NormaInfrastruktur_RpHaInt			: hitung Qty / Ha untuk norma infrastruktur internal
						- 26/06 : cal_NormaPanenLoading_SelisihBasis		: hitung selisih basis untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpBasisTukangMuat	: hitung Rp basis untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpKgBasisTukangMuat	: hitung Rp/Kg basis tukang muat untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpPremiTukangMuat	: hitung Rp premi tukang muat untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpKgPremiTukangMuat	: hitung Rp/Kg premi tukang muat untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpPremiSupir		: hitung premi supir untuk norma panen loading
						- 26/06 : cal_NormaPanenLoading_RpKgPremiSupir		: hitung Rp/Kg premi supir untuk norma panen loading
						- 26/06 : cal_NormaPanenSupervisi_RpKg				: hitung Rp/Kg untuk norma panen supervisi
						- 27/06 : get_NormaPanenKraniBuah_RpHk				: cari Rp/Kg untuk norma panen krani buah
						- 27/06 : cal_NormaPanenKraniBuah_SelisihOverBasis	: hitung selisih over basis untuk norma panen krani buah
						- 27/06 : cal_NormaPanenKraniBuah_RpKgBasis			: hitung Rp/Kg basis untuk norma panen krani buah
						- 27/06 : cal_NormaPanenKraniBuah_TotalPremi		: hitung Rp premi untuk norma panen krani buah
						- 27/06 : cal_NormaPanenKraniBuah_RpKgPremi			: hitung Rp/Kg Premi untuk norma panen krani buah
						- 27/06 : cal_NormaPanenPremiLangsir_TonHari		: hitung ton/hari untuk norma panen premi langsir
						- 27/06 : cal_NormaPanenPremiLangsir_RpTrip			: hitung Rp/Trip untuk norma panen premi langsir
						- 27/06 : cal_NormaPanenPremiLangsir_RpKg			: hitung Rp/Kg untuk norma panen premi langsir
						- 28/06 : get_NormaPerkerasanJalan					: cari harga literit untuk norma perkerasan jalan
						- 28/06 : get_NormaPanenCostUnit_RpKmInternal		: cari Rp/KM internal untuk norma panen cost unit
						- 28/06 : cal_NormaPanenCostUnit_RpKgInternal		: hitung Rp/Kg internal untuk norma panen cost unit
						- 28/06 : cal_NormaPanenCostUnit_RpKgExternal		: hitung Rp/Kg external untuk norma panen cost unit
						- 04/07 : get_NormaPerkerasanJalan_Jarak			: cari jarak rata-rata material untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_TripKm			: hitung trip/km untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_BiayaMaterial 	: hitung biaya material untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_HmExcav 			: hitung hm excav untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_HmCompac 		: hitung hm compactor untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_HmGrader 		: hitung hm grader untuk norma perkerasan jalan harga
						- 04/07 : get_NormaPerkerasanJalan_PercentExternal 	: hitung percent external untuk norma perkerasan jalan harga
						- 08/07 : cal_RktCapex_DistribusiTahunBerjalan		: hitung distribusi jumlah & biaya untuk RKT CAPEX
						- 10/07 : cal_RktOpex_Antisipasi					: hitung jumlah antisipasi untuk RKT OPEX
						- 10/07 : cal_RktOpex_Total							: hitung total biaya & presentase inflasi untuk RKT OPEX
						- 11/07 : get_ReportDistribusiCheckroll_TotalSem1	: cari ha planted semester 1 report distribusi coa
						- 11/07 : get_ReportDistribusiCheckroll_TotalSem2	: cari ha planted semester 2 report distribusi coa
						- 11/07 : cal_RktVra_PerincianStandarJamKerja		: hitung detail perhitungan standar jam kerja dari norma VRA untuk RKT VRA
						- 11/07 : cal_RktVra_PerincianTenagaKerja			: hitung detail perhitungan tenaga kerja dari norma checkroll untuk RKT VRA
						- 11/07 : cal_RktVra_PerincianRvra					: hitung detail per RVRA untuk RKT VRA
						- 15/07 : cal_RktRelation_TotalBiaya				: hitung total biaya untuk RKT CSR / IR / SHE
						- 15/07 : cal_NormaAlatKerjaPanen_RpKg				: hitung Rp/Kg untuk Norma Alat Kerja Panen
						- 17/07 : cal_RktPupuk_SelisihBulan					: hitung selisih bulan untuk RKT Pupuk
						- 24/07 : get_RktManual_Rotasi						: cari rotasi semester 1 & 2 untuk RKT Manual
						- 24/07 : cal_RktManual_CostElement					: hitung cost element untuk RKT Manual - Non Infra
						- 24/07 : cal_RktKastrasiSanitasi_CostElement		: hitung cost element untuk RKT Kastrasi Sanitasi
						- 24/07 : cal_RktManual_CostElementInfra			: hitung cost element RKT Manual - Infra
						- 24/07 : cal_RktManual_CostElementOpsi				: hitung cost element untuk RKT Manual - Non Infra + Opsi
						- 24/07 : cal_RktManual_Total						: hitung total untuk RKT Manual	
						- cal_RktManual_TotalTanam
						- get_RKTPerkerasanJalan_hargaqty
						- cal_RktPerkerasanJalan_CostElement
						- cal_RktPerkerasanJalan_DistribusiTahunBerjalan
						- cal_RktTanam_DistribusiHa
						- cal_RktManual_CostElementTanam
						- cal_costElement_RktTanam
						- cal_Rkt_Total
						- getStatusPeriode
						- cekSumberBiayaExternal
						- cekJenisPekerjaan_RKT_PK
						- cekSumberBiayaExternalManualInfra
						- 02/08 : cal_RktLc_CostElement						: hitung cost element RKT LC
						- 05/08 : gen_TransactionCode						: membuat kode transaksi untuk table
						- 12/08 : get_RktPanen_HSProd						: cari HS Produksi Panen 
						- 12/08 : get_RktPanen_PemanenHK					: cari Pemanen HK 
						- 12/08 : get_RktPanen_PemanenBasis					: cari Pemanen Basis 
						- 12/08 : get_RktPanen_PemanenPremi					: cari Pemanen Premi 
						- 12/08 : get_RktPanen_PemanenTotal					: cari Pemanen Total 
						- 12/08 : get_RktPanen_PemanenKg					: cari Pemanen Rp/Kg
						- 13/08 : get_RktPanen_SpvBasis						: cari Supervisi Basis 
						- 13/08 : get_RktPanen_SpvPremi						: cari Supervisi Premi
						- 13/08 : get_RktPanen_SpvTotal						: cari Supervisi Total
						- 13/08 : get_RktPanen_SpvKg						: cari Supervisi Kg
						- 13/08 : get_RktPanen_ToolsTotal					: cari Alat Panen Total
						- 13/08 : get_RktPanen_ToolsKg						: cari Alat Panen Kg
						- 13/08 : get_RktPanen_TkgBasis						: cari Tukang Muat Basis 
						- 13/08 : get_RktPanen_TkgPremi						: cari Tukang Muat Premi
						- 13/08 : get_RktPanen_TkgTotal						: cari Tukang Muat Total
						- 13/08 : get_RktPanen_TkgKg						: cari Tukang Muat Kg
						- 13/08 : get_RktPanen_SprPremi						: cari Supir Premi
						- 13/08 : get_RktPanen_SprKg						: cari Supir Kg 
						- 13/08 : get_RktPanen_AngkutKGKM					: cari Angkut Kg/Km
						- 13/08 : get_RktPanen_Angkut						: cari Angkut
						- 13/08 : get_RktPanen_AngkutKG						: cari Angkut Kg
						- 13/08 : get_RktPanen_KraniBasis					: cari Kerani Basis 
						- 13/08 : get_RktPanen_KraniPremi					: cari Kerani Premi
						- 13/08 : get_RktPanen_KraniTotal					: cari Kerani Total
						- 13/08 : get_RktPanen_KraniKg						: cari Kerani Kg
						- 13/08 : get_RktPanen_LangsirTon					: cari Kerani Premi
						- 13/08 : get_RktPanen_Langsir						: cari Kerani Total
						- 13/08 : get_RktPanen_LangsirKg				 	: cari Kerani Kg 
						- 13/08 : get_RktPanen_CostPanen				 	: cari Cost Panen
						- 19/08 : gen_TransactionCode						: Generate kode transaksi untuk di database
						- 21/08 : get_StatusPeriode							: cari status periode
						- 26/08 : get_DistVraPanenAngkTBS     				: cari nilai dist vra Panen Angkut TBS.
						- 18/06/14 	: cal_NormaBiaya_QtyHaSite				: hitung Qty / Ha Site untuk norma biaya				(Yopie Irawan)
						- 18/06/14 	: cal_NormaBiaya_RpHaSite				: hitung Rp / Ha Site untuk norma biaya					(Yopie Irawan)
						- 18/06/14 	: cal_NormaBiaya_RpHaRotasiSite			: hitung Rp / Ha / Rotasi Site untuk norma biaya		(Yopie Irawan)
						- 19/06/14 	: getInput								: setting input untuk region dan maturity stage
						- 05/07/14	: cal_SebaranRotasiRawat				: hitung sebaran rotasi untuk RKT Rawat & Rkt Rawat Opsi
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	24/04/2013
Update Terakhir		:	05/07/2014
Revisi				:	
============================================================================================================================================================================
NO			PIC				TANGGAL						KETERANGAN
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
1			DONI R			27-06-2013					MENAMBAHKAN VALIDASI DI cal_NormaCheckroll_Tunjangan JIKA NILAI TARIF TUNJANGAN KOSONG, MAKA TIDAK DIPROSES
2			DONI R			16-07-2013					PERBAIKAN QUERY DI cal_NormaCheckroll_Tunjangan & cal_NormaCheckroll_PkUmum

============================================================================================================================================================================

*/

//floatval(str_replace(",", "", [nama_var])) => penulisan baru : (float)str_replace(",", "", [nama_var])
class Application_Model_Formula
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
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;	
    }

	
	//////////////////////////////////////////////////////////////////// JANGAN DIUBAH ////////////////////////////////////////////////////////////////////
	
	//FUNCTION UNTUK MENGUBAH STRING MENJADI RUMUS PERHITUNGAN
	public function calculate_string( $mathString ) {
		$mathString = trim($mathString);     // trim white spaces
		$mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators
	 
		$compute = create_function("", "return (" . $mathString . ");" );
		return 0 + $compute();
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	//hitung Qty/HA utk norma harga
    public function cal_NormaHarga_QtyHa($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		
		$result = $qty * $rotasi * $volume;
        return $result;
    }
	
	//hitung inflasi harga dasar
	public function cal_NormaHargaBarang_HargaInflasi($params = array())
    {
		$harga_dasar = (float)str_replace(",", "", $params['HARGA_DASAR']);
		$inflasi = (float)str_replace(",", "", $params['INFLASI']);
		
		$result = $inflasi / 100 * $harga_dasar;
        return $result;
    }
	
	//mencari semester dari suatu bulan
	public function get_Semester($month)
    {
		switch ($month){
			case "01" : $result = 1; break;
			case "02" : $result = 1; break;
			case "03" : $result = 1; break;
			case "04" : $result = 1; break;
			case "05" : $result = 1; break;
			case "06" : $result = 1; break;
			case "07" : $result = 2; break;
			case "08" : $result = 2; break;
			case "09" : $result = 2; break;
			case "10" : $result = 2; break;
			case "11" : $result = 2; break;
			case "12" : $result = 2; break;
		}
        return $result;
    }
	
	//mencari maturity stage
	public function get_MaturityStage($tahun_tanam)
    {
		$date1_y = date("Y", strtotime($tahun_tanam));
		$date2_y = date("Y", strtotime($this->_period));
		$selisih_tahun = (((int)($date2_y) - (int)($date1_y)) * 12);
		
		$date1_m = date("m", strtotime($tahun_tanam));
		//$date2_m = date("m", strtotime($this->_period));
		$selisih_bulan = 12 - (int)($date1_m);
		
		$total = $selisih_tahun + $selisih_bulan;
		
		//cari umur
		$tahun_umur = 0;
		if ($total>0){
			while($total >= 12){
				$tahun_umur++;
				$total = $total - 12;
			}
		}
		$sms_tanam = $this->get_Semester(date("m", strtotime($tahun_tanam)));
		
		for($sms_view=1 ; $sms_view<=2 ; $sms_view++){
			if($tahun_umur <= 3){
				$sql = "
					SELECT STATUS
					FROM TM_MATURITY_STAGE
					WHERE SMS_TANAM = '".$sms_tanam."'
						AND SMS_VIEW = '".$sms_view."'
						AND UMUR_TANAM = '".$tahun_umur."'
				";
				$maturity_stage = $this->_db->fetchOne($sql);
			} else {
				$maturity_stage = 'TM';
			}			
			
			$result[$sms_view] = $maturity_stage;
		}	
		
        return $result;
    }
	
	//hitung total rupiah alat kerja panen
	public function cal_NormaAlatKerjaPanen_TotalRupiah($params = array())
    {
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$harga_inflasi = (float)str_replace(",", "", $params['HARGA_INFLASI']);
		
		$result = $rotasi * $harga_inflasi;
        return $result;
    }
	
	//hitung total rupiah alat kerja non panen
	public function cal_NormaAlatKerjaNonPanen_TotalRupiah($params = array())
    {
		$unit = (float)str_replace(",", "", $params['UNIT']);
		$harga_inflasi = (float)str_replace(",", "", $params['HARGA_INFLASI']);
		
		$result = $unit * $harga_inflasi;
        return $result;
    }
	
	//hitung Qty / Ha untuk norma Infra
	public function cal_NormaInfra_QtyHa($params = array())
    {
		$qty_alat = (float)str_replace(",", "", $params['QTY_ALAT']);
		$qty_infra = (float)str_replace(",", "", $params['QTY_INFRA']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		
		$result = $qty_alat * $qty_infra * $rotasi * ($volume / 100);
        return $result;
    }
	
	//hitung harga berdasarkan harga borong
	public function get_NormaHargaBorong_Harga($params = array())
    {
		$sql = "
			SELECT PRICE
			FROM TN_HARGA_BORONG
			WHERE to_char(PERIOD_BUDGET,'RRRR') = '".$params['PERIOD_BUDGET']."'
				AND BA_CODE = '".$params['BA_CODE']."'
				AND ACTIVITY_CODE = '".$params['ACTIVITY_CODE']."'
				AND ACTIVITY_CLASS = '".$params['ACTIVITY_CLASS']."'
		";
		
		$result = $this->_db->fetchOne($sql);
        return $result;
    }
	
	//hitung Rp / Qty untuk harga external norma Infra
	public function cal_NormaInfra_RpQtyExternal($params = array())
    {
		$harga_external = (float)str_replace(",", "", $params['HARGA_EXTERNAL']);
		$qty = (float)str_replace(",", "", $params['QTY_ALAT']);
		
		$result = $harga_external * $qty;
        return $result;
    }
	
	//hitung Rp / Ha untuk harga external norma Infra
	public function cal_NormaInfra_RpHaExternal($params = array())
    {
		$harga_external = (float)str_replace(",", "", $params['HARGA_EXTERNAL']);
		$qty = (float)str_replace(",", "", $params['QTY_HA']);
		
		$result = $harga_external * $qty;
        return $result;
    }
	
	//hitung jumlah kg untuk norma pupuk TBM + TM
	public function cal_NormaPupukTbmTm_JumlahKg($params = array())
    {
		$pokok = (float)str_replace(",", "", $params['POKOK']);
		$dosis = (float)str_replace(",", "", $params['DOSIS']);
		
		$result = $pokok * $dosis;
        return $result;
    }
	
	//hitung total biaya untuk norma pupuk TBM + TM
	public function cal_NormaPupukTbmTm_Biaya($params = array())
    {
		$pokok = (float)str_replace(",", "", $params['POKOK']);
		$dosis = (float)str_replace(",", "", $params['DOSIS']);
		$harga = (float)str_replace(",", "", $params['PRICE']);
		
		$result = $pokok * $dosis * $harga;
        return $result;
    }
	
	//hitung catu beras / bulan
	public function cal_MasterCatu_CatuBulan($params = array())
    {
		$rice_portion = (float)str_replace(",", "", $params['RICE_PORTION']);
		$price_kg = (float)str_replace(",", "", $params['PRICE_KG']);
		$hke_bulan = (float)str_replace(",", "", $params['HKE_BULAN']);
		
		$result = $rice_portion * $price_kg * $hke_bulan;
        return $result;
    }
	
	//hitung jumlah HK dalam 1 tahun
	public function cal_MasterCheckrollHk_Hk($params = array())
    {
		$total_hari = (float)str_replace(",", "", $params['HARI_SETAHUN']);
		$total_minggu = (float)str_replace(",", "", $params['MINGGU_SETAHUN']);
		$total_libur = (float)str_replace(",", "", $params['LIBUR_SETAHUN']);
		
		$result = $total_hari - $total_minggu - $total_libur;
        return $result;
    }
	
	//hitung jumlah HKE dalam 1 tahun
	public function cal_MasterCheckrollHk_Hke($params = array())
    {
		$total_hari = (float)str_replace(",", "", $params['HARI_SETAHUN']);
		$total_minggu = (float)str_replace(",", "", $params['MINGGU_SETAHUN']);
		$total_libur = (float)str_replace(",", "", $params['LIBUR_SETAHUN']);
		$cuti = (float)str_replace(",", "", $params['CUTI']);
		$sakit = (float)str_replace(",", "", $params['SAKIT']);
		$ijin = (float)str_replace(",", "", $params['IZIN']);
		$haid = (float)str_replace(",", "", $params['HAID']);
		
		$result = $total_hari - $total_minggu - $total_libur - $cuti - $sakit - $ijin - $haid;
        return $result;
    }
	
	//hitung SPH per blok
	public function cal_Upload_Sph($params = array())
    {
		$jumlah_pokok = (float)str_replace(",", "", $params['POKOK_TANAM']);
		$total_ha = (float)str_replace(",", "", $params['HA_PLANTED']);
		
		$result = ($jumlah_pokok == 0 || $total_ha == 0) ? 0 : ($jumlah_pokok / $total_ha);
        return $result;
    }
	
	//mencari region code dari suatu BA
	public function get_RegionCode($ba_code)
    {
		$sql = "
			SELECT REGION_CODE
			FROM TM_ORGANIZATION
			WHERE BA_CODE = '".addslashes($ba_code)."'
		"; 
		//echo $sql;
		
		$result = $this->_db->fetchOne($sql);
		
        return $result;
    }
	
	//hitung Qty / Ha untuk norma biaya
	public function cal_NormaBiaya_QtyHa($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		
		$result = $qty * $rotasi * ($volume / 100);
        return $result;
    }
	
	//hitung Qty / Ha Site untuk norma biaya
	public function cal_NormaBiaya_QtyHaSite($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY_SITE']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI_SITE']);
		$volume = (float)str_replace(",", "", $params['VOLUME_SITE']);
		
		$result = $qty * $rotasi * ($volume / 100);
        return $result;
    }
	
	//hitung Rp / Ha untuk norma biaya
	public function cal_NormaBiaya_RpHa($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		$price = (float)str_replace(",", "", $params['PRICE']);
		
		//echo "cal_NormaBiaya_RpHa result: $qty * $rotasi * ($volume / 100) * $price <br><br>";
		
		$result = $qty * $rotasi * ($volume / 100) * $price;
        return $result;
    }
	
	//hitung Rp / Ha untuk norma biaya
	public function cal_NormaBiaya_RpHaSite($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY_SITE']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI_SITE']);
		$volume = (float)str_replace(",", "", $params['VOLUME_SITE']);
		$price = (float)str_replace(",", "", $params['PRICE_SITE']);
		
		//echo "cal_NormaBiaya_RpHaSite result = $qty * $rotasi * ($volume / 100) * $price <br><br>";
		
		$result = $qty * $rotasi * ($volume / 100) * $price;
        return $result;
    }
	
	//hitung Rp / Ha / Rotasi untuk norma biaya
	public function cal_NormaBiaya_RpHaRotasi($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		$price = (float)str_replace(",", "", $params['PRICE']);
		
		//echo "cal_NormaBiaya_RpHaRotasi result = $qty * ($volume / 100) * $price <br><br>";
		
		$result = $qty * ($volume / 100) * $price;
        return $result;
    }
	
	//hitung Rp / Ha / Rotasi untuk norma biaya
	public function cal_NormaBiaya_RpHaRotasiSite($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY_SITE']);
		$volume = (float)str_replace(",", "", $params['VOLUME_SITE']);
		$price = (float)str_replace(",", "", $params['PRICE_SITE']);
		
		//echo "cal_NormaBiaya_RpHaRotasiSite result = $qty * ($volume / 100) * $price <br><br>";
		
		$result = $qty * ($volume / 100) * $price;
        return $result;
    }
	
	//hitung Qty / Tahun untuk norma WRA
	public function cal_NormaWra_QtyTahun($params = array())
    {
		$qty_rotasi = (float)str_replace(",", "", $params['QTY_ROTASI']);
		$rotasi_tahun = (float)str_replace(",", "", $params['ROTASI_TAHUN']);
		
		$result = $qty_rotasi * $rotasi_tahun;
        return $result;
    }
	
	//hitung Harga Qty / Tahun untuk norma WRA
	public function cal_NormaWra_HargaQtyTahun($params = array())
    {
		$qty_rotasi = (float)str_replace(",", "", $params['QTY_ROTASI']);
		$rotasi_tahun = (float)str_replace(",", "", $params['ROTASI_TAHUN']);
		$price = (float)str_replace(",", "", $params['HARGA_INFLASI']);
		
		$result = $qty_rotasi * $rotasi_tahun * $price;
        return $result;
    }
	
	//hitung Rp / Qty untuk norma WRA
	public function cal_NormaWra_RpQty($params = array())
    {
		$qty_rotasi = (float)str_replace(",", "", $params['QTY_ROTASI']);
		$rotasi_tahun = (float)str_replace(",", "", $params['ROTASI_TAHUN']);
		$price = (float)str_replace(",", "", $params['HARGA_INFLASI']);
		
		//get standar jam kerja
		$sql = "
			SELECT JAM_KERJA
			FROM TM_STANDART_JAM_KERJA_WRA
			WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
		";		
		$standar_jam_kerja = $this->_db->fetchOne($sql);
		
		$result = ($qty_rotasi * $rotasi_tahun * $price) / $standar_jam_kerja;
        return $result;
    }
	
	//hitung MPP rekrut untuk norma checkroll
	public function cal_NormaCheckroll_MppRekrut($params = array())
    {
		$mpp_period_budget = (int)str_replace(",", "", $params['MPP_PERIOD_BUDGET']);
		$mpp_aktual = (int)str_replace(",", "", $params['MPP_AKTUAL']);
		
		$mpp_rekrut = $mpp_period_budget - $mpp_aktual;
		
		$result = ($mpp_rekrut < 0) ? 0 : $mpp_rekrut;
        return $result;
    }
	
	//hitung GP inflasi untuk norma checkroll
	public function cal_NormaCheckroll_GpInflasi($params = array())
    {
		$gp = (float)str_replace(",", "", $params['GP']);
		
		//get standar jam kerja
		$sql = "
			SELECT PERCENT_INCREASE
			FROM TN_BASIC
			WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BASIC_NORMA_CODE = 'NC001'
		";		
		$persentase = $this->_db->fetchOne($sql);
		
		$result = $gp * ( $persentase / 100 );
        return $result;
    }
	
	//hitung tunjangan untuk norma checkroll
	public function cal_NormaCheckroll_Tunjangan($params = array())
    {
		$result['PPH21'] = 0;
		//rumus
		$sql = "
			SELECT TUNJANGAN_TYPE, RUMUS
			FROM TM_TUNJANGAN
			WHERE DELETE_USER IS NULL
				AND FLAG_RP_HK LIKE 'YES'
				AND FLAG_EMPLOYEE_STATUS LIKE '".$params['EMPLOYEE_STATUS']."'
		";		
		$rows = $this->_db->fetchAll($sql);	
		
		if (count($rows) > 0) {
			foreach ($rows as $idx => $row) {
				$rumus[$row['TUNJANGAN_TYPE']] = $row['RUMUS'];
			}
			//get tarif tunjangan
			/*remark doni 2013-07-16
			$sql = "
				SELECT TUNJANGAN_TYPE, VALUE as NILAI
				FROM TM_TARIF_TUNJANGAN
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND JOB_CODE = '".addslashes($params['JOB_CODE'])."'
					AND EMPLOYEE_STATUS = '".addslashes($params['EMPLOYEE_STATUS'])."'
			";		
			*/
			
			$sql = "
				SELECT TT.TUNJANGAN_TYPE, NVL(TT.VALUE,0) as NILAI
                FROM TM_TARIF_TUNJANGAN TT LEFT JOIN TM_TUNJANGAN TJ ON TT.TUNJANGAN_TYPE = TJ.TUNJANGAN_TYPE 
                WHERE TT.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
                    AND TT.BA_CODE like '".addslashes($params['BA_CODE'])."'
                    AND TT.JOB_CODE like '".addslashes($params['JOB_CODE'])."'
                    AND TT.EMPLOYEE_STATUS like '".addslashes($params['EMPLOYEE_STATUS'])."'
                    AND TJ.FLAG_EMPLOYEE_STATUS  like '".addslashes($params['EMPLOYEE_STATUS'])."'
					";
			$rows = $this->_db->fetchAll($sql);
			if (empty($rows)) {
				die('Tarif tunjangan untuk Pekerja '.$params['JOB_CODE'].' - '.$params['EMPLOYEE_STATUS'] .' tidak tersedia');
			}			
			foreach ($rows as $idx => $row) {
				IF ($row['NILAI'] == NULL ) die('GAGAL Hitung Tunjangan! Lengkapi Terlebih Dahulu Tarif Tunjangan  '.$row['TUNJANGAN_TYPE'].' untuk Pekerja '.$params['JOB_CODE'].' - '.$params['EMPLOYEE_STATUS']);
				$valTarifTunjangan[$row['TUNJANGAN_TYPE']] = $row['NILAI'];
			}
			
			$GP_BULAN = (float)str_replace(",", "", $params['GP_INFLASI']);
			$TARIF_PPH21 = $valTarifTunjangan['PPH_21'];
			$TARIF_ASTEK = $valTarifTunjangan['ASTEK'];
			$TARIF_JABATAN = $valTarifTunjangan['JABATAN'];
			$TARIF_KEHADIRAN = $valTarifTunjangan['KEHADIRAN'];
			$TARIF_LAINNYA = $valTarifTunjangan['LAINNYA'];
			
			//get catu
			$sql = "
				SELECT CATU_BERAS_SUM
				FROM TM_CATU_SUM
				WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
			";		
			$TARIF_CATU = $this->_db->fetchOne($sql);
			//if($TARIF_CATU == '') {die('Tarif Catu Tidak ditemukan');}
			foreach ($rumus as $idx => $rumus_hitung) {
				eval("\$formula_perhitungan = \"$rumus_hitung\";");
				$result[$idx] = $params['MPP_PERIOD_BUDGET'] ? $this->calculate_string($formula_perhitungan) : 0;
			}
		} //end if kalo ada datanya
		return $result;
    }
	
	//hitung tunjangan pekerjaan umum untuk norma checkroll
	public function cal_NormaCheckroll_PkUmum($params = array())
    {
		//rumus
		$sql = "
			SELECT TUNJANGAN_TYPE, RUMUS
			FROM TM_TUNJANGAN
			WHERE DELETE_USER IS NULL
				AND FLAG_RP_HK = 'NO'
				AND FLAG_EMPLOYEE_STATUS LIKE '".$params['EMPLOYEE_STATUS']."'
		";		
		$rows = $this->_db->fetchAll($sql);	 
			foreach ($rows as $idx => $row) {				
				$rumus[$row['TUNJANGAN_TYPE']] = $row['RUMUS'];
			}
			
			//get tarif tunjangan
			/* REMARK DONI - 2013-07-16
			$sql = "
				SELECT TUNJANGAN_TYPE, VALUE as NILAI
				FROM TM_TARIF_TUNJANGAN
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND JOB_CODE = '".addslashes($params['JOB_CODE'])."'
					AND EMPLOYEE_STATUS = '".addslashes($params['EMPLOYEE_STATUS'])."'
			";		
			*/
			
			$sql = "
				SELECT TT.TUNJANGAN_TYPE, NVL(TT.VALUE,0) as NILAI
                FROM TM_TARIF_TUNJANGAN TT LEFT JOIN TM_TUNJANGAN TJ ON TT.TUNJANGAN_TYPE = TJ.TUNJANGAN_TYPE 
                WHERE TT.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
                    AND TT.BA_CODE like '".addslashes($params['BA_CODE'])."'
                    AND TT.JOB_CODE like '".addslashes($params['JOB_CODE'])."'
                    AND TT.EMPLOYEE_STATUS like '".addslashes($params['EMPLOYEE_STATUS'])."'
                    AND TJ.FLAG_EMPLOYEE_STATUS  like '".addslashes($params['EMPLOYEE_STATUS'])."'
					";
			
			
			$rows = $this->_db->fetchAll($sql); 
			if (empty($rows)) {
				die('Tarif tunjangan untuk Pekerja '.$params['JOB_CODE'].' - '.$params['EMPLOYEE_STATUS'] .' tidak tersedia');
			}	
			if (count($rows) > 0) {
				foreach ($rows as $idx => $row) {
					IF ($row['NILAI'] == NULL ) die('GAGAL Hitung PK Umum! Lengkapi Dahulu Tarif Tunjangan '.$row['TUNJANGAN_TYPE'].' untuk Pekerja '.$params['JOB_CODE'].' - '.$params['EMPLOYEE_STATUS']);
					$valTarifTunjangan[$row['TUNJANGAN_TYPE']] = $row['NILAI'];
				}  
				$GP_BULAN = (float)str_replace(",", "", $params['GP_INFLASI']);
				$TARIF_THR = $valTarifTunjangan['THR'];
				$TARIF_HHR = $valTarifTunjangan['HHR'];
				$TARIF_BONUS = $valTarifTunjangan['BONUS'];
				$TARIF_OBAT = $valTarifTunjangan['OBAT'];
				
				foreach ($rumus as $idx => $rumus_hitung) {
					eval("\$formula_perhitungan = \"$rumus_hitung\";");
					
					$result[$idx] = $params['MPP_PERIOD_BUDGET'] ? $this->calculate_string($formula_perhitungan) : 0;
					
					if ( ( $idx == 'THR' || $idx == 'BONUS' ) && ( $params['EMPLOYEE_STATUS'] <> 'KT') ) {
						$result[$idx] = 0;
					} elseif ( ( $idx == 'HHR' ) && ( $params['EMPLOYEE_STATUS'] <> 'KL') ) {
						$result[$idx] = 0;
					}
				}
			}
		return $result;
    }
	
	//hitung Total GP MPP untuk norma checkroll
	public function cal_NormaCheckroll_TotalGpMpp($params = array())
    {
		$gp_inflasi = (float)str_replace(",", "", $params['GP_INFLASI']);
		$mpp_budget = (float)str_replace(",", "", $params['MPP_PERIOD_BUDGET']);
		
		$result = $params['MPP_PERIOD_BUDGET'] ? ($gp_inflasi * $mpp_budget) : 0;
        return $result;
    }
	
	//hitung Rp/HK untuk norma checkroll
	public function cal_NormaCheckroll_RpHk($params = array())
    {
		$total_gaji_tunjangan = (float)str_replace(",", "", $params['TOTAL_GAJI_TUNJANGAN']);
		
		//get HKE
		$sql = "
			SELECT HKE
			FROM TM_CHECKROLL_HK
			WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND EMPLOYEE_STATUS = '".addslashes($params['EMPLOYEE_STATUS'])."'
		";		
		$hke = $this->_db->fetchOne($sql);
		
		if(!$hke) { die("HKE untuk BA ".addslashes($params['BA_CODE'])." - ".$params['EMPLOYEE_STATUS']." kosong, Harap proses HKE terlebih dahulu"); }
		
		$result = $total_gaji_tunjangan / ( $hke / 12 );
        return $result;
    }
	
	//hitung total tunjangan PK Umum untuk norma checkroll
	public function cal_NormaCheckroll_TotalTunjanganPkUmum($params = array())
    {
		$OBAT = (float)str_replace(",", "", $params['OBAT']);
		$THR = (float)str_replace(",", "", $params['THR']);
		$HHR = (float)str_replace(",", "", $params['HHR']);
		$BONUS = (float)str_replace(",", "", $params['BONUS']);
		
		$result = $THR + $HHR + $BONUS + $OBAT;
        return $result;
    }
	
	//hitung total tunjangan PK Umum 1 tahun untuk norma checkroll
	public function cal_NormaCheckroll_DisYear($params = array())
    {		
		$TOTAL_TUNJANGAN_PK_UMUM = (float)str_replace(",", "", $params['TOTAL_TUNJANGAN_PK_UMUM']);
		$TOTAL_GAJI_TUNJANGAN = (float)str_replace(",", "", $params['TOTAL_GAJI_TUNJANGAN']);
		$MPP_PERIOD_BUDGET = (float)str_replace(",", "", $params['MPP_PERIOD_BUDGET']);
		/* 
		**************************** PERHITUNGAN ORIGINAL ****************************		
		$result = $TOTAL_TUNJANGAN_PK_UMUM * $MPP_PERIOD_BUDGET;
        return $result;
		*/
		
		//edited : SABRINA 09/07/2013
		// get jobgroup
		$sql = "
			SELECT JOB_TYPE
			FROM TM_JOB_TYPE
			WHERE JOB_CODE = '".$params['JOB_CODE']."'
		";		
		$jobtype = $this->_db->fetchOne($sql);
		
		if($jobtype == 'OT') {
			$result = ($TOTAL_TUNJANGAN_PK_UMUM * $MPP_PERIOD_BUDGET) + ($TOTAL_GAJI_TUNJANGAN * $MPP_PERIOD_BUDGET * 12);
		}else{
			$result = $TOTAL_TUNJANGAN_PK_UMUM * $MPP_PERIOD_BUDGET;
		}
        return $result;
    }
	
	//hitung over basis janjang untuk norma panen OER BJR
	public function cal_NormaPanenOerBjr_OverBasisJanjang($params = array())
    {
		$ASUMSI_OVER_BASIS = (float)str_replace(",", "", $params['ASUMSI_OVER_BASIS']);
		$JANJANG_BASIS_MANDOR = (float)str_replace(",", "", $params['JANJANG_BASIS_MANDOR']);
		$result = $JANJANG_BASIS_MANDOR * ( $ASUMSI_OVER_BASIS / 100 );
        return $result;
    }
	
	//hitung janjang operation untuk norma panen OER BJR
	public function cal_NormaPanenOerBjr_JanjangOperation($params = array())
    {
		$OVER_BASIS_JANJANG = (float)str_replace(",", "", $params['OVER_BASIS_JANJANG']);
		$JANJANG_BASIS_MANDOR = (float)str_replace(",", "", $params['JANJANG_BASIS_MANDOR']);
		
		$result = $JANJANG_BASIS_MANDOR + $OVER_BASIS_JANJANG;
        return $result;
    }
	
	//hitung nilai untuk norma panen OER BJR
	public function cal_NormaPanenOerBjr_Nilai($params = array())
    {
		$OVER_BASIS_JANJANG = (float)str_replace(",", "", $params['OVER_BASIS_JANJANG']);
		$PREMI_PANEN = (float)str_replace(",", "", $params['PREMI_PANEN']);
		$JANJANG_OPERATION = (float)str_replace(",", "", $params['JANJANG_OPERATION']);
		$BJR_BUDGET = (float)str_replace(",", "", $params['BJR_BUDGET']);
		
		$result = ($OVER_BASIS_JANJANG && $JANJANG_OPERATION && $BJR_BUDGET) ? ( $OVER_BASIS_JANJANG * $PREMI_PANEN ) / ( $JANJANG_OPERATION * $BJR_BUDGET ) : 0;
        
		return $result;
    }
	
	//hitung jumlah untuk norma pupuk < TBM 2
	public function cal_NormaPupukTbmLess_Jumlah($params = array())
    {
		$ROTASI = (float)str_replace(",", "", $params['ROTASI']);
		$DOSIS = (float)str_replace(",", "", $params['DOSIS']);
		
		$result = $ROTASI * $DOSIS;
        return $result;
    }
	
	//hitung Rp/Rotasi untuk norma pupuk < TBM 2
	public function cal_NormaPupukTbmLess_RpRotasi($params = array())
    {
		$DOSIS = (float)str_replace(",", "", $params['DOSIS']);
		$PRICE = (float)str_replace(",", "", $params['PRICE']);
		
		$result = $PRICE * $DOSIS;
        return $result;
    }
	
	//hitung Rp/Tahun untuk norma pupuk < TBM 2
	public function cal_NormaPupukTbmLess_RpTahun($params = array())
    {
		$ROTASI = (float)str_replace(",", "", $params['ROTASI']);
		$DOSIS = (float)str_replace(",", "", $params['DOSIS']);
		$PRICE = (float)str_replace(",", "", $params['PRICE']);
		
		$result = $ROTASI * $DOSIS * $PRICE;
        return $result;
    }
	
	//hitung Qty / Ha untuk norma infrastruktur
	public function cal_NormaInfrastruktur_QtyHaEks($params = array())
    {
		$qty_inf = (float)str_replace(",", "", $params['QTY_INFRA']);
		$qty = (float)str_replace(",", "", $params['QTY']);
		$rotasi = (float)str_replace(",", "", $params['ROTASI']);
		$volume = (float)str_replace(",", "", $params['VOLUME']);
		
		$result = $qty_inf * $qty * $rotasi * ($volume / 100);
        return $result;
    }	
	
	//hitung Rp / Ha untuk norma infrastruktur eksternal
	public function cal_NormaInfrastruktur_RpHaEks($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY_INFRA']);
		$price = (float)str_replace(",", "", $params['PRICE']);
		
		$result = $qty * $price;
        return $result;
    }
	
	//hitung Rp / Qty untuk norma infrastruktur internal
	public function cal_NormaInfrastruktur_RpQtyInt($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY']);
		$harga = (float)str_replace(",", "", $params['HARGA_INTERNAL']);
		
		$result = $qty * $harga;
        return $result;
    }
	
	//hitung Rp / Ha untuk norma infrastruktur internal
	public function cal_NormaInfrastruktur_RpHaInt($params = array())
    {
		$qty = (float)str_replace(",", "", $params['QTY_HA']);
		$harga = (float)str_replace(",", "", $params['HARGA_INTERNAL']);
		
		$result = $qty * $harga;
        return $result;
    }
	
	//hitung selisih basis untuk norma panen loading
	public function cal_NormaPanenLoading_SelisihBasis($params = array())
    {
		$target = (float)str_replace(",", "", $params['TARGET_ANGKUT_TM_SUPIR']);
		$basis = (float)str_replace(",", "", $params['BASIS_TM_SUPIR']);
		
		$result = $target - $basis;
        return $result;
    }
	
	//hitung Rp Basis tukang muat untuk norma panen loading
	public function cal_NormaPanenLoading_RpBasisTukangMuat($params = array())
    {
		$rp_hk = (float)str_replace(",", "", $params['RP_HK_TM']);
		$hk = (float)str_replace(",", "", $params['JUMLAH_TM']);
		
		$result = $rp_hk * $hk;
        return $result;
    }
	
	//hitung Rp/Kg Basis tukang muat untuk norma panen loading
	public function cal_NormaPanenLoading_RpKgBasisTukangMuat($params = array())
    {
		$rupiah_basis_tm = (float)str_replace(",", "", $params['RUPIAH_BASIS_TM']);
		$target = (float)str_replace(",", "", $params['TARGET_ANGKUT_TM_SUPIR']);
		
		$result = $rupiah_basis_tm / ($target * 1000);
        return $result;
    }
	
	//hitung Rp Premi tukang muat untuk norma panen loading
	public function cal_NormaPanenLoading_RpPremiTukangMuat($params = array())
    {
		$selisih_basis = (float)str_replace(",", "", $params['SELISIH_BASIS']);
		$tarif = (float)str_replace(",", "", $params['TARIF_TM']);
		
		$result = $selisih_basis * $tarif;
        return $result;
    }
	
	//hitung Rp/Kg Premi tukang muat untuk norma panen loading
	public function cal_NormaPanenLoading_RpKgPremiTukangMuat($params = array())
    {
		$rp_premi_tm = (float)str_replace(",", "", $params['RP_PREMI_TM']);
		$target = (float)str_replace(",", "", $params['TARGET_ANGKUT_TM_SUPIR']);
		
		$result = $rp_premi_tm / ($target * 1000);
        return $result;
    }
	
	//hitung Rp Premi supir untuk norma panen loading
	public function cal_NormaPanenLoading_RpPremiSupir($params = array())
    {
		$selisih_basis = (float)str_replace(",", "", $params['SELISIH_BASIS']);
		$tarif = (float)str_replace(",", "", $params['TARIF_SUPIR']);
		
		$result = $selisih_basis * $tarif;
        return $result;
    }
	
	//hitung Rp/Kg Premi supir untuk norma panen loading
	public function cal_NormaPanenLoading_RpKgPremiSupir($params = array())
    {
		$rp_premi_supir = (float)str_replace(",", "", $params['RP_PREMI_SUPIR']);
		$target = (float)str_replace(",", "", $params['TARGET_ANGKUT_TM_SUPIR']);
		
		$result = $rp_premi_supir / ($target * 1000);
        return $result;
    }
	
	//hitung Rp/Kg untuk norma panen supervisi
	public function cal_NormaPanenSupervisi_RpKg($params = array())
    {
		$AVG_MANDOR = (float)str_replace(",", "", $params['AVG_MANDOR']);
		$JANJANG_OPERATION = (float)str_replace(",", "", $params['JANJANG_OPERATION']);
		$BJR_BUDGET = (float)str_replace(",", "", $params['BJR_BUDGET']);
		$RATIO_PEMANEN = (float)str_replace(",", "", $params['RATIO_PEMANEN']);
		
		$result = ($JANJANG_OPERATION && $BJR_BUDGET) ? ($AVG_MANDOR / ($JANJANG_OPERATION * $BJR_BUDGET) * ($RATIO_PEMANEN / 100)) : 0;
        return $result;
    }
	
	//cari Rp/Kg untuk norma panen krani buah
	public function get_NormaPanenKraniBuah_RpHk($params = array())
    {
		$sql = "
			SELECT RP_HK
			FROM TR_RKT_CHECKROLL_SUM
			WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND JOB_CODE = 'FX160'
		";		
		$rp_hk = $this->_db->fetchOne($sql);
		$result = ($rp_hk) ? $rp_hk : 0;
        return $result;
    }
	
	//hitung selisih over basis untuk norma panen krani buah
	public function cal_NormaPanenKraniBuah_SelisihOverBasis($params = array())
    {
		$TARGET = (float)str_replace(",", "", $params['TARGET']);
		$BASIS = (float)str_replace(",", "", $params['BASIS']);
		$result = $TARGET - $BASIS;
        return $result;
    }
	
	//hitung Rp/Kg basis untuk norma panen krani buah
	public function cal_NormaPanenKraniBuah_RpKgBasis($params = array())
    {
		$RP_HK = (float)str_replace(",", "", $params['RP_HK']);
		$TARGET = (float)str_replace(",", "", $params['TARGET']);
		
		$result = ($RP_HK != 0) && ($TARGET != 0) ? ($RP_HK / $TARGET) / 1000 : 0;
        return $result;
    }
	
	//hitung total premi untuk norma panen krani buah
	public function cal_NormaPanenKraniBuah_TotalPremi($params = array())
    {
		$SELISIH_OVER_BASIS = (float)str_replace(",", "", $params['SELISIH_OVER_BASIS']);
		$TARIF_BASIS = (float)str_replace(",", "", $params['TARIF_BASIS']);
		
		$result = $SELISIH_OVER_BASIS * $TARIF_BASIS;
        return $result;
    }
	
	//hitung Rp/Kg premi untuk norma panen krani buah
	public function cal_NormaPanenKraniBuah_RpKgPremi($params = array())
    {
		$PREMI = (float)str_replace(",", "", $params['PREMI']);
		$TARGET = (float)str_replace(",", "", $params['TARGET']);
		
		$result = ($PREMI != 0) ? ($PREMI / $TARGET) / 1000 : 0;
        return $result;
    }
	
	//hitung ton/hari untuk norma panen premi langsir
	public function cal_NormaPanenPremiLangsir_TonHari($params = array())
    {
		$TON_TRIP = (float)str_replace(",", "", $params['TON_TRIP']);
		$TRIP_HARI = (float)str_replace(",", "", $params['TRIP_HARI']);
		
		$result = $TON_TRIP * $TRIP_HARI;
        return $result;
    }

	//hitung rp/trip untuk norma panen premi langsir
	public function cal_NormaPanenPremiLangsir_RpTrip($params = array())
    {
		$HM_TRIP = (float)str_replace(",", "", $params['HM_TRIP']);
		$RP_HM = (float)str_replace(",", "", $params['RP_HM']);
		
		$result = $HM_TRIP * $RP_HM;
        return $result;
    }
	
	//hitung rp/kg untuk norma panen premi langsir
	public function cal_NormaPanenPremiLangsir_RpKg($params = array())
    {
		$RP_TRIP = (float)str_replace(",", "", $params['RP_TRIP']);
		$TON_HARI = (float)str_replace(",", "", $params['TON_HARI']);
		
		$result = $RP_TRIP / ($TON_HARI * 1000);
        return $result;
    }
	
	//cari Rp/KM internal untuk norma panen cost unit
	public function get_NormaPanenCostUnit_RpKmInternal($params = array())
    {
		$sql = "
			SELECT VALUE
			FROM TR_RKT_VRA_SUM
			WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND VRA_CODE = 'DT010'
		";		
		$rp_km = $this->_db->fetchOne($sql);
		$result = ($rp_km) ? $rp_km : 0;
        return $result;
    }
	
	//hitung rp/kg internal untuk norma panen cost unit
	public function cal_NormaPanenCostUnit_RpKgInternal($params = array())
    {
		$JARAK_ANGKUT = (float)str_replace(",", "", $params['JARAK_ANGKUT']);
		$RIT = (float)str_replace(",", "", $params['RIT']);
		$RP_KM_INTERNAL = (float)str_replace(",", "", $params['RP_KM_INTERNAL']);
		$TARGET = (float)str_replace(",", "", $params['TARGET']);
		
		if(($TARGET==0)||($JARAK_ANGKUT==0))
			$result = 0;
		else
			$result = (( $JARAK_ANGKUT * 2 * $RIT * $RP_KM_INTERNAL ) / ( $TARGET * 1000 )) / $JARAK_ANGKUT;
        return $result;
    }
	
	//hitung rp/kg external untuk norma panen cost unit
	public function cal_NormaPanenCostUnit_RpKgExternal($params = array())
    {
		$JARAK_ANGKUT = (float)str_replace(",", "", $params['JARAK_ANGKUT']);
		$RIT = (float)str_replace(",", "", $params['RIT']);
		$RP_KM_EXTERNAL = (float)str_replace(",", "", $params['RP_KM_EXTERNAL']);
		$TARGET = (float)str_replace(",", "", $params['TARGET']);
		
		if(($TARGET==0)||($JARAK_ANGKUT==0))
			$result = 0;
		else
			$result = (( $JARAK_ANGKUT * 2 * $RIT * $RP_KM_EXTERNAL ) / ( $TARGET * 1000 )) / $JARAK_ANGKUT;
        return $result;
    }
	
	//cari jumlah literit untuk norma perkerasan jalan
	public function get_NormaPerkerasanJalan_QtyMaterial($params = array())
    {
		//print_r($params);die();
		$LEBAR = (float)str_replace(",", "", $params['LEBAR']);
		$PANJANG = (float)str_replace(",", "", $params['PANJANG']);
		$TEBAL = (float)str_replace(",", "", $params['TEBAL']);

		$result = $LEBAR * $PANJANG * $TEBAL * 1.666;
		return $result;
    }	
	
	//cari harga literit untuk norma perkerasan jalan
	public function get_NormaPerkerasanJalan($params = array())
    {
		$sql = "
		SELECT PRICE FROM TN_HARGA_BARANG
          WHERE BA_CODE = '".addslashes($params['BA_CODE'])."'
			AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
             AND MATERIAL_CODE = '202090031'
		";		
		$result = $this->_db->fetchOne($sql);
		return $result;
    }	
	
	//cari jarak rata-rata material untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_Jarak($params = array())
    {
		$sql = "
		SELECT RP_KM_DT
			FROM TN_PERKERASAN_JALAN
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
				";
		$result = $this->_db->fetchOne($sql);
		return $result;
    }	
	
	//hitung trip/km untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_TripKm($params = array())
    {
		$MATERIAL_M3 = (float)str_replace(",", "", $params['VOLUME_MATERIAL']);
		$KAPASITAS = (float)str_replace(",", "", $params['KAPASITAS_DT']);
		
		$result = ($MATERIAL_M3!=0) ? ($MATERIAL_M3 / $KAPASITAS) : 0;
		return $result;
    }

	//hitung biaya material untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_BiayaMaterial($params = array())
    {
		$MATERIAL_M3 = (float)str_replace(",", "", $params['VOLUME_MATERIAL']);
		$HARGA = (float)str_replace(",", "", $params['PRICE']);
		
		$result = ($MATERIAL_M3!=0) ? ($MATERIAL_M3 * $HARGA) :0;
		return $result;
    }
	
	//hitung hm excav untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_HmExcav($params = array())
    {
		$MATERIAL_M3 = (float)str_replace(",", "", $params['VOLUME_MATERIAL']);
		$KAPASITAS = (float)str_replace(",", "", $params['KAPASITAS_EXCAV']);
		
		$result = ($MATERIAL_M3!=0)?($MATERIAL_M3 / $KAPASITAS):0;
		return $result;
    }
	
	//hitung hm compactor untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_HmCompac($params = array())
    {
		$MATERIAL_M3 = (float)str_replace(",", "", $params['VOLUME_MATERIAL']);
		$KAPASITAS = (float)str_replace(",", "", $params['KAPASITAS_COMPACTOR']);
		
		$result = ($MATERIAL_M3!=0) ? ($MATERIAL_M3 / $KAPASITAS) :0;
		return $result;
    }
	
	//hitung hm grader untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_HmGrader($params = array())
    {
		$MATERIAL_M3 = (float)str_replace(",", "", $params['VOLUME_MATERIAL']);
		$KAPASITAS = (float)str_replace(",", "", $params['KAPASITAS_GRADER']);
		
		$result = ($MATERIAL_M3!=0) ? ($MATERIAL_M3 / $KAPASITAS) :0;
		return $result;
    }
	
	//hitung percent external untuk norma perkerasan jalan harga
	public function get_NormaPerkerasanJalan_PercentExternal($params = array())
    {
		$sql = "
		SELECT PERCENT_INCREASE - 100 AS PERCENT_INCREASE
			FROM TN_BASIC
			WHERE BASIC_NORMA_CODE = 'NC024'
			";
		$result = $this->_db->fetchOne($sql);
		return $result;
    }
	
	//hitung distribusi jumlah & biaya untuk RKT CAPEX
	public function cal_RktCapex_DistribusiTahunBerjalan($params = array())
    {
		$total_distribusi = $total_biaya = $total_sms1 = $total_sms2 = 0;
		$DIS_JAN = (float)str_replace(",", "", $params['DIS_JAN']);
		$DIS_FEB = (float)str_replace(",", "", $params['DIS_FEB']);
		$DIS_MAR = (float)str_replace(",", "", $params['DIS_MAR']);
		$DIS_APR = (float)str_replace(",", "", $params['DIS_APR']);
		$DIS_MAY = (float)str_replace(",", "", $params['DIS_MAY']);
		$DIS_JUN = (float)str_replace(",", "", $params['DIS_JUN']);
		$DIS_JUL = (float)str_replace(",", "", $params['DIS_JUL']);
		$DIS_AUG = (float)str_replace(",", "", $params['DIS_AUG']);
		$DIS_SEP = (float)str_replace(",", "", $params['DIS_SEP']);
		$DIS_OCT = (float)str_replace(",", "", $params['DIS_OCT']);
		$DIS_NOV = (float)str_replace(",", "", $params['DIS_NOV']);
		$DIS_DEC = (float)str_replace(",", "", $params['DIS_DEC']);
		$PRICE = (float)str_replace(",", "", $params['PRICE']);
		
		$result['DIS_JAN'] = $PRICE * $DIS_JAN; $total_distribusi += $DIS_JAN; $total_sms1 += $result['DIS_JAN'];
		$result['DIS_FEB'] = $PRICE * $DIS_FEB; $total_distribusi += $DIS_FEB; $total_sms1 += $result['DIS_FEB'];
		$result['DIS_MAR'] = $PRICE * $DIS_MAR; $total_distribusi += $DIS_MAR; $total_sms1 += $result['DIS_MAR'];
		$result['DIS_APR'] = $PRICE * $DIS_APR; $total_distribusi += $DIS_APR; $total_sms1 += $result['DIS_APR'];
		$result['DIS_MAY'] = $PRICE * $DIS_MAY; $total_distribusi += $DIS_MAY; $total_sms1 += $result['DIS_MAY'];
		$result['DIS_JUN'] = $PRICE * $DIS_JUN; $total_distribusi += $DIS_JUN; $total_sms1 += $result['DIS_JUN'];
		$result['DIS_JUL'] = $PRICE * $DIS_JUL; $total_distribusi += $DIS_JUL; $total_sms2 += $result['DIS_JUL'];
		$result['DIS_AUG'] = $PRICE * $DIS_AUG; $total_distribusi += $DIS_AUG; $total_sms2 += $result['DIS_AUG'];
		$result['DIS_SEP'] = $PRICE * $DIS_SEP; $total_distribusi += $DIS_SEP; $total_sms2 += $result['DIS_SEP'];
		$result['DIS_OCT'] = $PRICE * $DIS_OCT; $total_distribusi += $DIS_OCT; $total_sms2 += $result['DIS_OCT'];
		$result['DIS_NOV'] = $PRICE * $DIS_NOV; $total_distribusi += $DIS_NOV; $total_sms2 += $result['DIS_NOV'];
		$result['DIS_DEC'] = $PRICE * $DIS_DEC; $total_distribusi += $DIS_DEC; $total_sms2 += $result['DIS_DEC'];
		$total_biaya = array_sum($result);
		$result['TOTAL_BIAYA'] = $total_biaya;
		$result['TOTAL_SMS1'] = $total_sms1;
		$result['TOTAL_SMS2'] = $total_sms2;
		$result['TOTAL_DISTRIBUSI'] = $total_distribusi;		
		
        return $result;
    }
	
	//hitung distribusi jumlah & biaya untuk RKT CAPEX
	public function cal_RktLc_DistribusiTahunBerjalan($params = array())
    {
		$total_distribusi = $total_biaya = 0;
		$DIS_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$DIS_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$DIS_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$DIS_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$DIS_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$DIS_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$DIS_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$DIS_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$DIS_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$DIS_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$DIS_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$DIS_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);
		$PRICE   = (float)str_replace(",", "", $params['RP_QTY']);
		//print_r($PRICE);
		//die();
		$result['COST_JAN'] = $PRICE * $DIS_JAN; $total_distribusi += $DIS_JAN;
		$result['COST_FEB'] = $PRICE * $DIS_FEB; $total_distribusi += $DIS_FEB;
		$result['COST_MAR'] = $PRICE * $DIS_MAR; $total_distribusi += $DIS_MAR;
		$result['COST_APR'] = $PRICE * $DIS_APR; $total_distribusi += $DIS_APR;
		$result['COST_MAY'] = $PRICE * $DIS_MAY; $total_distribusi += $DIS_MAY;
		$result['COST_JUN'] = $PRICE * $DIS_JUN; $total_distribusi += $DIS_JUN;
		$result['COST_JUL'] = $PRICE * $DIS_JUL; $total_distribusi += $DIS_JUL;
		$result['COST_AUG'] = $PRICE * $DIS_AUG; $total_distribusi += $DIS_AUG;
		$result['COST_SEP'] = $PRICE * $DIS_SEP; $total_distribusi += $DIS_SEP;
		$result['COST_OCT'] = $PRICE * $DIS_OCT; $total_distribusi += $DIS_OCT;
		$result['COST_NOV'] = $PRICE * $DIS_NOV; $total_distribusi += $DIS_NOV;
		$result['COST_DEC'] = $PRICE * $DIS_DEC; $total_distribusi += $DIS_DEC;
		//$total_biaya = array_sum($result);
		//$total_distribusi = $DIS_JAN + $DIS_FEB + $DIS_MAR+ $DIS_APR+ $DIS_MAY+ $DIS_JUN+ $DIS_JUL+ $DIS_AUG+ $DIS_SEP+ $DIS_OCT+ $DIS_NOV+ $DIS_DEC;
		$total_biaya = $PRICE * $total_distribusi;
		$result['TOTAL_COST'] = $total_biaya;
		$result['COST_SMS1'] = $result['COST_JAN'] + $result['COST_FEB']+ $result['COST_MAR']+ $result['COST_APR']+ $result['COST_MAY']+ $result['COST_JUN'];
		$result['COST_SMS2'] = $total_biaya - $result['COST_SMS1'];
		$result['TOTAL_PLAN'] = $total_distribusi;		
		
        return $result;
    }
	
	//hitung jumlah antisipasi untuk RKT OPEX
	public function cal_RktOpex_Antisipasi($params = array())
    {
		$ACTUAL = (float)str_replace(",", "", $params['ACTUAL']);
		$TAKSASI = (float)str_replace(",", "", $params['TAKSASI']);
		
		$result = $ACTUAL + $TAKSASI;
		return $result;
    }
	
	
	
	//hitung total biaya & presentase inflasi untuk RKT OPEX
	public function cal_RktOpex_Total($params = array())
    {
		$total_biaya = $total_sms1 = $total_sms2 = 0;
		$ANTISIPASI = (float)str_replace(",", "", $params['ANTISIPASI']);	
		
		if ($params['TIPE_TOTAL_BIAYA'] == 'OPEX_VRA'){
			// total biaya RKT OPEX - VRA
			$sql = "
				SELECT NVL(SUM(rkt.TOTAL_PRICE_HM_KM),0 )
				FROM TR_RKT_VRA_DISTRIBUSI_SUM rkt
				WHERE rkt.BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND to_char(rkt.PERIOD_BUDGET,'DD-MM-RRRR') = '01-01-{$params['PERIOD_BUDGET']}'
					AND rkt.ACTIVITY_CODE = '".addslashes($params['COA_CODE'])."'
		   ";
			$total_biaya = $this->_db->fetchOne($sql);
			$total_sms1 = ($total_biaya) ? ($total_biaya/2) : 0;
			$total_sms2 = ($total_biaya) ? ($total_biaya/2) : 0;
		}else{
			$DIS_JAN = (float)str_replace(",", "", $params['DIS_JAN']);
			$DIS_FEB = (float)str_replace(",", "", $params['DIS_FEB']);
			$DIS_MAR = (float)str_replace(",", "", $params['DIS_MAR']);
			$DIS_APR = (float)str_replace(",", "", $params['DIS_APR']);
			$DIS_MAY = (float)str_replace(",", "", $params['DIS_MAY']);
			$DIS_JUN = (float)str_replace(",", "", $params['DIS_JUN']);
			$DIS_JUL = (float)str_replace(",", "", $params['DIS_JUL']);
			$DIS_AUG = (float)str_replace(",", "", $params['DIS_AUG']);
			$DIS_SEP = (float)str_replace(",", "", $params['DIS_SEP']);
			$DIS_OCT = (float)str_replace(",", "", $params['DIS_OCT']);
			$DIS_NOV = (float)str_replace(",", "", $params['DIS_NOV']);
			$DIS_DEC = (float)str_replace(",", "", $params['DIS_DEC']);
			
			$total_biaya += $DIS_JAN; $total_sms1 += $DIS_JAN;
			$total_biaya += $DIS_FEB; $total_sms1 += $DIS_FEB;
			$total_biaya += $DIS_MAR; $total_sms1 += $DIS_MAR;
			$total_biaya += $DIS_APR; $total_sms1 += $DIS_APR;
			$total_biaya += $DIS_MAY; $total_sms1 += $DIS_MAY;
			$total_biaya += $DIS_JUN; $total_sms1 += $DIS_JUN;
			$total_biaya += $DIS_JUL; $total_sms2 += $DIS_JUL;
			$total_biaya += $DIS_AUG; $total_sms2 += $DIS_AUG;
			$total_biaya += $DIS_SEP; $total_sms2 += $DIS_SEP;
			$total_biaya += $DIS_OCT; $total_sms2 += $DIS_OCT;
			$total_biaya += $DIS_NOV; $total_sms2 += $DIS_NOV;
			$total_biaya += $DIS_DEC; $total_sms2 += $DIS_DEC;
		}	
		
		$result['TOTAL_SMS1'] = $total_sms1;
		$result['TOTAL_SMS2'] = $total_sms2;
		$result['TOTAL_BIAYA'] = $total_biaya;
		$result['PERSENTASE_INFLASI'] = ($total_biaya && $ANTISIPASI) ? ((($total_biaya/$ANTISIPASI) - 1) * 100) : 0;
		
        return $result;
    }
	
	//cari total ha planted semester 1 report distribusi coa
	public function get_ReportDistribusiCheckroll_TotalSem1($params = array())
    {
		$sql = "
		SELECT SUM(HA_PLANTED) AS TOTAL_SEM
		FROM TM_HECTARE_STATEMENT
		WHERE DELETE_TIME IS NULL
			AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
			AND BA_CODE = '".addslashes($params['BA_CODE'])."'
			AND MATURITY_STAGE_SMS1 IS NOT NULL
	   ";
		$result = $this->_db->fetchOne($sql);
		return $result;
    }
	
	//cari total tunjangan semester 1 report distribusi coa
	public function get_ReportDistribusiCheckroll_TotalTunjanganSem1($params = array())
    {
		$sql = "
		SELECT SUM(HA_PLANTED) AS TOTAL_SEM
		FROM TM_HECTARE_STATEMENT
		WHERE DELETE_TIME IS NULL
			AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
			AND BA_CODE = '".addslashes($params['BA_CODE'])."'
			AND MATURITY_STAGE_SMS1 IS NOT NULL
	   ";
		$result = $this->_db->fetchOne($sql);
		return $result;
    }

	//cari total ha planted semester 2 report distribusi coa
	public function get_ReportDistribusiCheckroll_TotalSem2($params = array())
    {
		$sql = "
		SELECT SUM(HA_PLANTED)
		FROM TM_HECTARE_STATEMENT
		WHERE
			DELETE_TIME IS NULL
			AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
			AND BA_CODE = '".addslashes($params['BA_CODE'])."'
			AND MATURITY_STAGE_SMS2 IS NOT NULL
	   ";
		$result = $this->_db->fetchOne($sql);
		return $result;
    }

	//hitung detail perhitungan standar jam kerja dari norma VRA untuk RKT VRA
	public function cal_RktVra_PerincianStandarJamKerja($params = array())
    {
		$jumlah_alat = (int)str_replace(",", "", $params['JUMLAH_ALAT']);
		
		//perhitungan usia alat
		$tahun_alat = (int)str_replace(",", "", $params['TAHUN_ALAT']);
		$period_budget = date("Y", strtotime("01-01-{$params['PERIOD_BUDGET']}"));
		$usia_alat = $period_budget - $tahun_alat;
		
		//cari dari norma VRA
		$sql = "
			SELECT DISTINCT QTY_DAY, DAY_YEAR_VRA
			FROM TN_VRA
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND VRA_CODE =  '".addslashes($params['VRA_CODE'])."'
				AND '".addslashes($usia_alat)."' BETWEEN MIN_YEAR AND MAX_YEAR
	   ";
		$row = $this->_db->fetchRow($sql);
		
		$qty_day = $params['QTY_DAY'] ? ((int)str_replace(",", "", $params['QTY_DAY'])) : $row['QTY_DAY'];
		$day_year_vra = $params['DAY_YEAR_VRA'] ? ((int)str_replace(",", "", $params['DAY_YEAR_VRA'])) : $row['DAY_YEAR_VRA'];
		
		$result['QTY_DAY'] = $qty_day;
		$result['DAY_YEAR_VRA'] = $day_year_vra;
		$result['QTY_YEAR'] = $qty_day * $day_year_vra;
		$result['TOTAL_QTY_TAHUN'] = $jumlah_alat * $result['QTY_YEAR'];
		
		return $result;
    }

	//hitung detail perhitungan tenaga kerja dari norma checkroll untuk RKT VRA
	public function cal_RktVra_PerincianTenagaKerja($params = array())
    {
		$jumlah_operator = (int)str_replace(",", "", $params['JUMLAH_OPERATOR']);
		$jumlah_helper = (int)str_replace(",", "", $params['JUMLAH_HELPER']);
		$total_qty_tahun = (int)str_replace(",", "", $params['TOTAL_QTY_TAHUN']);
		
		//cari GP dari report checkroll + TM_MAPPING_JOB_TYPE_VRA untuk OPERATOR
		$sql = "
			SELECT cr.GP_INFLASI, cr.MPP_PERIOD_BUDGET, cr.TOTAL_GAJI_TUNJANGAN, cr.TOTAL_TUNJANGAN_PK_UMUM, cr.TOTAL_TUNJANGAN_VRA
			FROM TR_RKT_CHECKROLL cr
			LEFT JOIN TM_MAPPING_JOB_TYPE_VRA mapping
				ON cr.JOB_CODE = mapping.JOB_CODE
				AND cr.EMPLOYEE_STATUS = 'KT'
			WHERE cr.DELETE_USER IS NULL
				AND cr.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND cr.BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND mapping.VRA_CODE =  '".addslashes($params['VRA_CODE'])."'
				AND mapping.RVRA_CODE = 'R550101010101'
	   ";
		$row = $this->_db->fetchRow($sql);
		$result['GAJI_OPERATOR'] = ($jumlah_operator) ? $row['GP_INFLASI'] : 0;
		$result['TOTAL_GAJI_OPERATOR'] = ($jumlah_operator) ? ($jumlah_operator * $result['GAJI_OPERATOR']) : 0;
		$result['TUNJANGAN_OPERATOR'] = ($jumlah_operator) ? $row['TOTAL_TUNJANGAN_VRA'] : 0;
		$result['TOTAL_TUNJANGAN_OPERATOR'] = ($jumlah_operator) ? ($jumlah_operator * $result['TUNJANGAN_OPERATOR']) : 0;
		$result['TOTAL_GAJI_TUNJANGAN_OPERATOR'] = $result['TOTAL_GAJI_OPERATOR'] + $result['TOTAL_TUNJANGAN_OPERATOR'];
		$result['RP_QTY_OPERATOR'] = ($total_qty_tahun) ? $result['TOTAL_GAJI_TUNJANGAN_OPERATOR'] * 12 / $total_qty_tahun : 0;
		
		//cari GP dari report checkroll + TM_MAPPING_JOB_TYPE_VRA untuk HELPER
		$sql = "
			SELECT cr.GP_INFLASI, cr.MPP_PERIOD_BUDGET, cr.TOTAL_GAJI_TUNJANGAN, cr.TOTAL_TUNJANGAN_PK_UMUM, cr.TOTAL_TUNJANGAN_VRA
			FROM TR_RKT_CHECKROLL cr
			LEFT JOIN TM_MAPPING_JOB_TYPE_VRA mapping
				ON cr.JOB_CODE = mapping.JOB_CODE
				AND cr.EMPLOYEE_STATUS = 'KT'
			WHERE cr.DELETE_USER IS NULL
				AND cr.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND cr.BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND mapping.VRA_CODE =  '".addslashes($params['VRA_CODE'])."'
				AND mapping.RVRA_CODE = 'R550101010102'
	   ";
		$row = $this->_db->fetchRow($sql);
		$result['GAJI_HELPER'] = ($jumlah_helper) ? $row['GP_INFLASI'] : 0;
		$result['TOTAL_GAJI_HELPER'] = ($jumlah_helper) ? ($jumlah_helper * $result['GAJI_HELPER']) : 0;
		$result['TUNJANGAN_HELPER'] = ($jumlah_helper) ? $row['TOTAL_TUNJANGAN_VRA'] : 0;
		$result['TOTAL_TUNJANGAN_HELPER'] = ($jumlah_helper) ? ($jumlah_helper * $result['TUNJANGAN_HELPER']) : 0;
		$result['TOTAL_GAJI_TUNJANGAN_HELPER'] = $result['TOTAL_GAJI_HELPER'] + $result['TOTAL_TUNJANGAN_HELPER'];
		$result['RP_QTY_HELPER'] = ($total_qty_tahun) ? $result['TOTAL_GAJI_TUNJANGAN_HELPER'] * 12 / $total_qty_tahun : 0;
		
		return $result;
    }

	//hitung detail per RVRA untuk RKT VRA
	public function cal_RktVra_PerincianRvra($params = array())
    {
		$total_qty_tahun = (float)str_replace(",", "", $params['TOTAL_QTY_TAHUN']);
		$total_rp_qty = $params['RP_QTY_OPERATOR'] + $params['RP_QTY_HELPER'];
		$total_biaya2 = ($params['TOTAL_GAJI_TUNJANGAN_OPERATOR'] * 12) + ($params['TOTAL_GAJI_TUNJANGAN_HELPER'] * 12);
		//perhitungan usia alat
		$tahun_alat = (int)str_replace(",", "", $params['TAHUN_ALAT']);
		$period_budget = date("Y", strtotime("01-01-{$params['PERIOD_BUDGET']}"));
		$usia_alat = $period_budget - $tahun_alat;
		
		//cari material code + qty/sat + harga material dari norma VRA
		$sql = "
			SELECT DISTINCT norma_vra.SUB_RVRA_CODE, norma_vra.MATERIAL_CODE, norma_vra.QTY_UOM, norma_harga.PRICE
			FROM TN_VRA norma_vra
			LEFT JOIN TN_HARGA_BARANG norma_harga
                ON norma_vra.PERIOD_BUDGET = norma_harga.PERIOD_BUDGET
                AND norma_vra.BA_CODE = norma_harga.BA_CODE
                AND norma_vra.MATERIAL_CODE = norma_harga.MATERIAL_CODE
			WHERE norma_vra.DELETE_USER IS NULL
				AND norma_vra.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND norma_vra.BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND norma_vra.VRA_CODE =  '".addslashes($params['VRA_CODE'])."'
				AND '".addslashes($usia_alat)."' BETWEEN norma_vra.MIN_YEAR AND norma_vra.MAX_YEAR
				AND norma_vra.SUB_RVRA_CODE NOT IN ('R550101010101', 'R550101010102', 'R550101070101', 'R550101020101')
	   ";
	   $rows = $this->_db->fetchAll($sql);
		
		foreach ($rows as $idx => $row) {
			$rvra['harga'][$row['SUB_RVRA_CODE']] = $row['PRICE'];
			$rvra['qty_uom'][$row['SUB_RVRA_CODE']] = $row['QTY_UOM'];
		}
		
		// definisi RVRA
		$result['RVRA1_CODE'] = 'R550101070101';	//PAJAK & PERIJINAN
		$result['RVRA2_CODE'] = 'R550101030101'; //FUEL / BAHAN BAKAR
		$result['RVRA3_CODE'] = 'R550101040101'; //OLI MESIN
		$result['RVRA4_CODE'] = 'R550101040102'; //OLI TRANSMISI
		$result['RVRA5_CODE'] = 'R550101040103'; //MINYAK HYDROLIC
		$result['RVRA6_CODE'] = 'R550101040104'; //GREASE
		$result['RVRA7_CODE'] = 'R550101050101'; //FILTER OLI
		$result['RVRA8_CODE'] = 'R550101050102'; //FILTER HYDROLIC
		$result['RVRA9_CODE'] = 'R550101050103'; //FILTER SOLAR
		$result['RVRA10_CODE'] = 'R550101050104'; //FILTER SOLAR MOISTURE SEPARATOR
		$result['RVRA11_CODE'] = 'R550101050105'; //FILTER UDARA
		$result['RVRA12_CODE'] = 'R550101050106'; //GANTI SPAREPART
		$result['RVRA13_CODE'] = 'R550101060101'; //GANTI BAN LUAR
		$result['RVRA14_CODE'] = 'R550101060102'; //GANTI BAN DALAM
		$result['RVRA15_CODE'] = 'R550101080101'; //SERVIS WORKSHOP
		$result['RVRA16_CODE'] = 'R550101090101'; //OVERHAUL
		$result['RVRA17_CODE'] = 'R550101020101'; //RENTAL
		$result['RVRA18_CODE'] = 'R550101090102'; //SERVIS BENGKEL LUAR
		
		//PAJAK PERIJINAN
		$result['RVRA1_VALUE1'] = (int)str_replace(",", "", $params['JUMLAH_ALAT']);
		$result['RVRA1_VALUE2'] = $params['JUMLAH_ALAT'] ? (float)str_replace(",", "", $params['RVRA1_VALUE2']) : 0;
		$result['RVRA1_VALUE3'] = ($total_qty_tahun && $params['JUMLAH_ALAT'] ) ? ($result['RVRA1_VALUE1'] * $result['RVRA1_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA1_VALUE3'];
		$total_biaya2 += ($result['RVRA1_VALUE1'] * $result['RVRA1_VALUE2']);
		
		//RENTAL
		$result['RVRA17_VALUE1'] = (float)str_replace(",", "", $params['RVRA17_VALUE1']);
		$result['RVRA17_VALUE2'] = ($params['RVRA17_VALUE1']) ? (float)str_replace(",", "", $params['RVRA17_VALUE2']) : 0;
		$result['RVRA17_VALUE3'] = ($total_qty_tahun && $params['RVRA17_VALUE1']) ? ($result['RVRA17_VALUE1'] * $result['RVRA17_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA17_VALUE3'];
		$total_biaya2 += ($result['RVRA17_VALUE1'] * $result['RVRA17_VALUE2'] );
		
		
		//FUEL / BAHAN BAKAR
		$result['RVRA2_VALUE1'] = $rvra['qty_uom']['R550101030101'];
		$result['RVRA2_VALUE2'] = ($rvra['qty_uom']['R550101030101']) ? $rvra['harga']['R550101030101'] : 0;
		$result['RVRA2_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101030101']) ? ($result['RVRA2_VALUE1'] * $result['RVRA2_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA2_VALUE3'];
		$total_biaya2 += ($result['RVRA2_VALUE1'] * $result['RVRA2_VALUE2'] * $total_qty_tahun );
		
		//OLI MESIN
		$result['RVRA3_VALUE1'] = $rvra['qty_uom']['R550101040101'];
		$result['RVRA3_VALUE2'] = ($rvra['qty_uom']['R550101040101']) ? $rvra['harga']['R550101040101'] : 0;
		$result['RVRA3_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101040101']) ? ($result['RVRA3_VALUE1'] * $result['RVRA3_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA3_VALUE3'];
		$total_biaya2 += ($result['RVRA3_VALUE1'] * $result['RVRA3_VALUE2']* $total_qty_tahun );
		
		//OLI TRANSMISI
		$result['RVRA4_VALUE1'] = $rvra['qty_uom']['R550101040102'];
		$result['RVRA4_VALUE2'] = ($rvra['qty_uom']['R550101040102']) ? $rvra['harga']['R550101040102'] : 0;
		$result['RVRA4_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101040102']) ? ($result['RVRA4_VALUE1'] * $result['RVRA4_VALUE2'])  : 0;
		$total_rp_qty += $result['RVRA4_VALUE3'];
		$total_biaya2 += ($result['RVRA4_VALUE1'] * $result['RVRA4_VALUE2']* $total_qty_tahun );
		
		//MINYAK HYDROLIC
		$result['RVRA5_VALUE1'] = $rvra['qty_uom']['R550101040103'];
		$result['RVRA5_VALUE2'] = ($rvra['qty_uom']['R550101040103']) ? $rvra['harga']['R550101040103'] : 0;
		$result['RVRA5_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101040103']) ? ($result['RVRA5_VALUE1'] * $result['RVRA5_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA5_VALUE3'];
		$total_biaya2 += ($result['RVRA5_VALUE1'] * $result['RVRA5_VALUE2']* $total_qty_tahun );
		
		//GREASE
		$result['RVRA6_VALUE1'] = $rvra['qty_uom']['R550101040104'];
		$result['RVRA6_VALUE2'] = ($rvra['qty_uom']['R550101040104']) ? $rvra['harga']['R550101040104'] : 0;
		$result['RVRA6_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101040104']) ? ($result['RVRA6_VALUE1'] * $result['RVRA6_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA6_VALUE3'];
		$total_biaya2 += ($result['RVRA6_VALUE1'] * $result['RVRA6_VALUE2']* $total_qty_tahun );
		
		//FILTER OLI
		$result['RVRA7_VALUE1'] = $rvra['qty_uom']['R550101050101'];
		$result['RVRA7_VALUE2'] = ($rvra['qty_uom']['R550101050101']) ? $rvra['harga']['R550101050101'] : 0;
		$result['RVRA7_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101050101']) ? ($result['RVRA7_VALUE1'] * $result['RVRA7_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA7_VALUE3'];
		$total_biaya2 += ($result['RVRA7_VALUE1'] * $result['RVRA7_VALUE2']* $total_qty_tahun );
		
		//FILTER HYDROLIC
		$result['RVRA8_VALUE1'] = $rvra['qty_uom']['R550101050102'];
		$result['RVRA8_VALUE2'] = ($rvra['qty_uom']['R550101050102']) ? $rvra['harga']['R550101050102'] : 0;
		$result['RVRA8_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101050102']) ? ($result['RVRA8_VALUE1'] * $result['RVRA8_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA8_VALUE3'];
		$total_biaya2 += ($result['RVRA8_VALUE1'] * $result['RVRA8_VALUE2']* $total_qty_tahun );
		
		//FILTER SOLAR
		$result['RVRA9_VALUE1'] = $rvra['qty_uom']['R550101050103'];
		$result['RVRA9_VALUE2'] = ($rvra['qty_uom']['R550101050103']) ? $rvra['harga']['R550101050103'] : 0;
		$result['RVRA9_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101050103']) ? ($result['RVRA9_VALUE1'] * $result['RVRA9_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA9_VALUE3'];
		$total_biaya2 += ($result['RVRA9_VALUE1'] * $result['RVRA9_VALUE2']* $total_qty_tahun );
		
		//FILTER SOLAR MOISTURE SEPARATOR
		$result['RVRA10_VALUE1'] = $rvra['qty_uom']['R550101050104'];
		$result['RVRA10_VALUE2'] = ($rvra['qty_uom']['R550101050104']) ? $rvra['harga']['R550101050104'] : 0;
		$result['RVRA10_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101050104']) ? ($result['RVRA10_VALUE1'] * $result['RVRA10_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA10_VALUE3'];
		$total_biaya2 += ($result['RVRA10_VALUE1'] * $result['RVRA10_VALUE2']* $total_qty_tahun );
		
		//FILTER UDARA
		$result['RVRA11_VALUE1'] = $rvra['qty_uom']['R550101050105'];
		$result['RVRA11_VALUE2'] = ($rvra['qty_uom']['R550101050105']) ? $rvra['harga']['R550101050105'] : 0;
		$result['RVRA11_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101050105']) ? ($result['RVRA11_VALUE1'] * $result['RVRA11_VALUE2']) : 0;
		$total_rp_qty += $result['RVRA11_VALUE3'];
		$total_biaya2 += ($result['RVRA11_VALUE1'] * $result['RVRA11_VALUE2']* $total_qty_tahun );
		
		//GANTI SPAREPART
		$result['RVRA12_VALUE1'] = 1;
		$result['RVRA12_VALUE2'] = (float)str_replace(",", "", $params['RVRA12_VALUE2']);
		$result['RVRA12_VALUE3'] = ($total_qty_tahun && $result['RVRA12_VALUE2']) ? ($result['RVRA12_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA12_VALUE3'];
		$total_biaya2 += ($result['RVRA12_VALUE1'] * $result['RVRA12_VALUE2']);
		
		//GANTI BAN LUAR
		$result['RVRA13_VALUE1'] = $rvra['qty_uom']['R550101060101'];
		$result['RVRA13_VALUE2'] = ($rvra['qty_uom']['R550101060101']) ? $rvra['harga']['R550101060101'] : 0;
		$result['RVRA13_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101060101']) ? ($result['RVRA13_VALUE1'] * $result['RVRA13_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA13_VALUE3'];
		$total_biaya2 += ($result['RVRA13_VALUE1'] * $result['RVRA13_VALUE2']);
		
		//GANTI BAN DALAM
		$result['RVRA14_VALUE1'] = $rvra['qty_uom']['R550101060102'];
		$result['RVRA14_VALUE2'] = ($rvra['qty_uom']['R550101060102']) ? $rvra['harga']['R550101060102'] : 0;
		$result['RVRA14_VALUE3'] = ($total_qty_tahun && $rvra['qty_uom']['R550101060102']) ? ($result['RVRA14_VALUE1'] * $result['RVRA14_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA14_VALUE3'];
		$total_biaya2 += ($result['RVRA14_VALUE1'] * $result['RVRA14_VALUE2']);
		
		//SERVIS WORKSHOP
		$result['RVRA15_VALUE1'] = (float)str_replace(",", "", $params['RVRA15_VALUE1']);
		$sql = "
			SELECT TOTAL_RP_QTY
			FROM TN_WRA_SUM
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
	   ";
		$result['RVRA15_VALUE2'] = ($params['RVRA15_VALUE1']) ? $this->_db->fetchOne($sql) : 0;
		$result['RVRA15_VALUE3'] = ($total_qty_tahun && $params['RVRA15_VALUE1']) ? ($result['RVRA15_VALUE1'] * $result['RVRA15_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA15_VALUE3'];
		$total_biaya2 += ($result['RVRA15_VALUE1'] * $result['RVRA15_VALUE2']);
		
		//OVERHAUL
		$result['RVRA16_VALUE1'] = 1;
		$result['RVRA16_VALUE2'] = (float)str_replace(",", "", $params['RVRA16_VALUE2']);
		$result['RVRA16_VALUE3'] = ($total_qty_tahun && $result['RVRA16_VALUE2']) ?  ($result['RVRA16_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA16_VALUE3'];
		$total_biaya2 += ($result['RVRA16_VALUE1'] * $result['RVRA16_VALUE2']);
		
		
		//SERVIS BENGKEL LUAR
		$result['RVRA18_VALUE1'] = 1;
		$result['RVRA18_VALUE2'] = (float)str_replace(",", "", $params['RVRA18_VALUE2']);
		$result['RVRA18_VALUE3'] = ($total_qty_tahun && $result['RVRA18_VALUE2']) ? ($result['RVRA18_VALUE2'] / $total_qty_tahun) : 0;
		$total_rp_qty += $result['RVRA18_VALUE3'];
		$total_biaya2 += ($result['RVRA18_VALUE1'] * $result['RVRA18_VALUE2']);
		
		//TOTAL
		$result['TOTAL_RP_QTY'] = $total_rp_qty;
		$result['TOTAL_BIAYA'] = $total_biaya2;
		
		return $result;
    }

	//total biaya untuk RKT CSR / IR / SHE
	public function cal_RktRelation_TotalBiaya($params = array())
    {
		$total_biaya = $total_sms1 = $total_sms2 = 0;
		
		$DIS_JAN = (float)str_replace(",", "", $params['DIS_JAN']);
		$DIS_FEB = (float)str_replace(",", "", $params['DIS_FEB']);
		$DIS_MAR = (float)str_replace(",", "", $params['DIS_MAR']);
		$DIS_APR = (float)str_replace(",", "", $params['DIS_APR']);
		$DIS_MAY = (float)str_replace(",", "", $params['DIS_MAY']);
		$DIS_JUN = (float)str_replace(",", "", $params['DIS_JUN']);
		$DIS_JUL = (float)str_replace(",", "", $params['DIS_JUL']);
		$DIS_AUG = (float)str_replace(",", "", $params['DIS_AUG']);
		$DIS_SEP = (float)str_replace(",", "", $params['DIS_SEP']);
		$DIS_OCT = (float)str_replace(",", "", $params['DIS_OCT']);
		$DIS_NOV = (float)str_replace(",", "", $params['DIS_NOV']);
		$DIS_DEC = (float)str_replace(",", "", $params['DIS_DEC']);
		
		$total_biaya += $DIS_JAN; $total_sms1 += $DIS_JAN;
		$total_biaya += $DIS_FEB; $total_sms1 += $DIS_FEB;
		$total_biaya += $DIS_MAR; $total_sms1 += $DIS_MAR;
		$total_biaya += $DIS_APR; $total_sms1 += $DIS_APR;
		$total_biaya += $DIS_MAY; $total_sms1 += $DIS_MAY;
		$total_biaya += $DIS_JUN; $total_sms1 += $DIS_JUN;
		$total_biaya += $DIS_JUL; $total_sms2 += $DIS_JUL;
		$total_biaya += $DIS_AUG; $total_sms2 += $DIS_AUG;
		$total_biaya += $DIS_SEP; $total_sms2 += $DIS_SEP;
		$total_biaya += $DIS_OCT; $total_sms2 += $DIS_OCT;
		$total_biaya += $DIS_NOV; $total_sms2 += $DIS_NOV;
		$total_biaya += $DIS_DEC; $total_sms2 += $DIS_DEC;
		
		$result['TOTAL_SMS1'] = $total_sms1;
		$result['TOTAL_SMS2'] = $total_sms2;
		$result['TOTAL_BIAYA'] = $total_biaya;
		
        return $result;
    }

	//hitung Rp/Kg untuk norma alat kerja panen
	public function cal_NormaAlatKerjaPanen_RpKg($params = array())
    {
		//TON / BA
		$sql = "
			SELECT SUM(TON_BUDGET)
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$total_ton = $this->_db->fetchOne($sql);
		
		//PRODUKTIVITAS
		$sql = "
			SELECT VALUE
			FROM TN_PANEN_VARIABLE
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND PANEN_CODE = 'PRO_PEMANEN'
		";
		$produktivitas = $this->_db->fetchOne($sql);
		$prod = ($produktivitas) ? $produktivitas : 1000; //jika tidak ada nilai nya, maka default 1000 -- SABRINA 21/08/2014
		
		//HKE KT
		$sql = "
			SELECT HKE
			FROM TM_CHECKROLL_HK
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND EMPLOYEE_STATUS = 'KT'
		";
		$hke = $this->_db->fetchOne($sql);
		
		if ( ($total_ton) && ($prod) && ($hke) && ($params['PRICE_ROTASI_SUM']) )
			$result = ((($total_ton/($prod/1000)/$hke)*$params['PRICE_ROTASI_SUM'])/$total_ton)/1000;
		else
			$result = 0;
			
        return $result;
    }

	//hitung selisih bulan untuk RKT Pupuk
	public function cal_RktPupuk_SelisihBulan($tahun_tanam)
    {
		/*
		=========== ADA MASALAH SELISIH 05.2014 - 01.2014 = 3 ===========
		date_default_timezone_set('Asia/Jakarta');  // you are required to set a timezone

		$date1 = new DateTime($tahun_tanam);
		$date2 = new DateTime($this->_period);

		$diff = $date1->diff($date2);

		$result = (($diff->format('%y') * 12) + $diff->format('%m'));
		
		$year1 = mktime(0, 0, 0, date("m", strtotime($tahun_tanam)), 0, date("Y", strtotime($tahun_tanam)));
		$year2 = mktime(0,0,0,date("m", strtotime($this->_period)), 0, date("Y", strtotime($this->_period)));
		
		$result = ($year2 < $year1) ? ($result * (-1)) : $result;
		*/
		
		// DATE2 = PERIODE BUDGET, DATE1 = TAHUN TANAM
		$date1_y = date("Y", strtotime($tahun_tanam));
		$date2_y = date("Y", strtotime($this->_period));
		$selisih_tahun = (((int)($date2_y) - (int)($date1_y)) * 12);
		
		$date1_m = date("m", strtotime($tahun_tanam));
		$date2_m = date("m", strtotime($this->_period));
		$selisih_bulan = (int)($date2_m) - (int)($date1_m);
		
		$result = $selisih_tahun + $selisih_bulan;
		
		return $result;
    }
	
	public function get_RktPanen_HSProd($params){
		$sql = "
			SELECT NVL(TON_BUDGET, 0) TON, NVL(JANJANG_BUDGET, 0) JANJANG
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND AFD_CODE = '".addslashes($params['AFD_CODE'])."'
					AND BLOCK_CODE = '".addslashes($params['BLOCK_CODE'])."'";
		$result = $this->_db->fetchRow($sql);
		
		$sql = "
			SELECT SUM(NVL(TON_BUDGET, 0)) TON_BUDGET, SUM(NVL(JANJANG_BUDGET, 0)) JANJANG_BUDGET
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND AFD_CODE = '".addslashes($params['AFD_CODE'])."'";
		$res = $this->_db->fetchRow($sql);
		
		/*
		if($res['JANJANG_BUDGET']){
			$result['BJR_AFD'] = ( $res['TON_BUDGET'] / $res['JANJANG_BUDGET'] * 1000 ) ? 0;
		}else{
			die('Nilai Janjang Budget Untuk BA : '.$params['BA_CODE'].' AFD : '.$params['AFD_CODE'].' Belum Terdapat di Perencanaan Produksi.');
		}*/
		
		$result['BJR_AFD'] = ($res['JANJANG_BUDGET']) ? ( $res['TON_BUDGET'] / $res['JANJANG_BUDGET'] * 1000 ) : 0;
		
		return $result;
	}
	
	public function get_RktPanen_PemanenHK($params){
		/* OLD
		$sql = "SELECT NVL(VALUE,1) FROM TN_PANEN_VARIABLE 
				WHERE PANEN_CODE='PRO_PEMANEN' 
				AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$prdPanen = $this->_db->fetchOne($sql);
		*/
		
		//PERUBAHAN FORMULA : SABRINA 05/08/2014
		//cari tahun tanam
		$sql = "
			SELECT TO_CHAR (TAHUN_TANAM, 'MM.RRRR') TAHUN_TANAM, TOPOGRAPHY
			FROM TM_HECTARE_STATEMENT
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND AFD_CODE = '".addslashes($params['AFD_CODE'])."' 
				AND BLOCK_CODE = '".addslashes($params['BLOCK_CODE'])."' 
		";					
		$res = $this->_db->fetchRow($sql);
		
		//hitung umur 
		$date1_y = substr($res['TAHUN_TANAM'], -4);
		$date2_y = $params['PERIOD_BUDGET'];
		$selisih_tahun = (((int)($date2_y) - (int)($date1_y)) * 12);
		
		$date1_m = substr($res['TAHUN_TANAM'], 0, 2);
		$selisih_bulan = 1 - (int)($date1_m);
		
		$total_selisih_umur = $selisih_tahun + $selisih_bulan;
		$tahun_umur = $total_selisih_umur / 12;
		
		$umur_tanaman = round($tahun_umur, 0, PHP_ROUND_HALF_DOWN);
		
		$sql = "
			SELECT NVL(VALUE,0)
			FROM TN_PANEN_PROD_PEMANEN
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND UMUR = '".addslashes($umur_tanaman)."' 
				AND TOPOGRAPHY = '".addslashes($res['TOPOGRAPHY'])."'
		";					
		$prdPanen = $this->_db->fetchOne($sql);
		
		return ($prdPanen) ? (($params['TON']/$prdPanen)*1000) : 0;
	}
	
	public function get_RktPanen_PemanenBasis($params){
		$sql = "SELECT NVL(RP_HK,0) FROM TR_RKT_CHECKROLL_SUM 
				WHERE JOB_CODE='FW040' 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$crPemanen = $this->_db->fetchOne($sql);
		
		return $params['BIAYA_PEMANEN_HK']*$crPemanen;
	}
	
	public function get_RktPanen_PemanenPremi($params){
		//PERSEN JANJANG
		$sql = "SELECT NVL(VALUE,0) FROM TN_PANEN_VARIABLE 
				WHERE PANEN_CODE='PRES_JANJANG' 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";					
		$presJjg = $this->_db->fetchOne($sql);
		
		//JJG RP/KG, TN_PANEN_OER_BJR - Edited : Sab 10/09/2013
		$pre_oer = ((float)str_replace(",", "", $params['PRE_OER'])) / 100;
		$bjr = (float)str_replace(",", "", $params['BJR_AFD']);
		$sql = "SELECT 	NVL(JANJANG_BASIS_MANDOR,0) as JANJANG_BASIS_MANDOR,
						NVL(PREMI_PANEN,0) as PREMI_PANEN
				FROM TN_PANEN_OER_BJR 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$pre_oer."' BETWEEN OER_MIN AND OER_MAX
					AND '".$bjr."' BETWEEN BJR_MIN AND BJR_MAX
					AND DELETE_USER IS NULL";
		$oer = $this->_db->fetchRow($sql);
		
		//PERSEN BRONDOLAN
		$sql = "SELECT NVL(VALUE,0) FROM TN_PANEN_VARIABLE 
				WHERE PANEN_CODE='PRES_BRONDOLAN' 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$presBrd = $this->_db->fetchOne($sql);
		
		//PRE_MAN_BRD
		$sql = "SELECT NVL(VALUE,0) FROM TN_PANEN_VARIABLE 
				WHERE PANEN_CODE='PRE_MAN_BRD' 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";					
		$panenBrd = $this->_db->fetchOne($sql);
		
		//cek penambahan presentase premi bukit
		$sql = "
			SELECT NVL(PERCENTAGE,0) as PRESENTASE
			FROM TN_PANEN_PREMI_TOPOGRAPHY
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND AFD_CODE = '".addslashes($params['AFD_CODE'])."' 
		";					
		$presentase = $this->_db->fetchOne($sql);
		
		//PERUBAHAN FORMULA : SABRINA 21/08/2014
		$premi_pemanen = (($params['TON']*1000/$bjr) - ($oer['JANJANG_BASIS_MANDOR'] * $params['BIAYA_PEMANEN_HK'])) * $oer['PREMI_PANEN'];
		$premi_pemanen_tambahan = $premi_pemanen + ($presentase / 100 * $premi_pemanen);
		
		$premi_brondolan = ($presBrd/100*$params['TON']*1000)*$panenBrd;
		
		$premi = $premi_pemanen_tambahan + $premi_brondolan;
		
		$return = ($premi < 0) ? 0 : $premi;
		/*echo "TON : ". $params['TON']."<br />";
		echo "BJR[AFD] : ". $bjr."<br />";
		echo "JANJANG BASIS MANDOR : ". $oer['BIAYA_PEMANEN_HK']."<br />";
		echo "BIAYA PEMANEN HK : ". $params['TON']."<br />";
		echo "PREMI_PANEN : ". $oer['PREMI_PANEN']."<br />";
		echo "-------------------------------------<br />";
		echo "PREMI PEMANEN = ((TON*1000)/BJR[AFD]) - (JANJANG BASIS MANDOR * BIAYA PEMANEN HK) * PREMI PANEN";
		echo "-------------------------------------<br />";
		
		echo "PRESENTASE (PREMI TOPOGRAPHY) : ". $presentase."<br />";
		
		echo "-------------------------------------<br />";
		echo "PREMI PEMANEN TAMBAHAN = PREMI PEMANEN + (PRESENTASE/100*PREMI PEMANEN)";
		echo "-------------------------------------<br />";
		
		echo "PERSEN BRONDOLAN : ". $presBrd."<br />";
		echo "PANEN BRONDOLAN: ". $panenBrd."<br />";
		
		echo "-------------------------------------<br />";
		echo "PREMI BRONDOLAN = (PERSEN BRONDOLAN/100*TON*1000)*PANEN BRONDOLAN";
		echo "-------------------------------------<br />";
		
		echo "HASIL AKHIR : -------------------------------------<br />";
		echo "PREMI = PREMI PANEN TAMBAHAN+PREMI BRONDOLAN";
		echo "-------------------------------------<br />";
		
		die();*/
		
		return $return;
	}
	
	public function get_RktPanen_PemanenTotal($row){
		return $row['BIAYA_PEMANEN_RP_PREMI']+$row['BIAYA_PEMANEN_RP_BASIS'];
	}
	
	public function get_RktPanen_PemanenKg($row){
		return ($row['TON']!=0)? ($row['BIAYA_PEMANEN_RP_TOTAL']/($row['TON']*1000)):0;
	}
	
	public function get_RktPanen_SpvBasis($params){		
		/*
		//JJG RP/KG, TN_PANEN_SUPERVISI
		$bjr = (float)str_replace(",", "", $params['BJR_AFD']);
		$sql = "SELECT NVL(RP_KG,0) 
				FROM TN_PANEN_SUPERVISI 
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$bjr."' BETWEEN MIN_BJR AND MAX_BJR
					AND DELETE_USER IS NULL";
					
		$jjgValue = $this->_db->fetchOne($sql);
		
		return ($params['TON']*1000)*$jjgValue;
		*/
		
		//PERUBAHAN FORMULA : SABRINA 21/08/2014
		$sql = "
			SELECT TOTAL_GAJI_TUNJANGAN, MPP_PERIOD_BUDGET
			FROM TR_RKT_CHECKROLL
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND JOB_CODE = 'FX140'
				AND EMPLOYEE_STATUS = 'KT'
				AND DELETE_USER IS NULL
		";
		$res = $this->_db->fetchRow($sql);
		
		$sql = "
			SELECT SUM(NVL(TON_BUDGET, 0)) TON_BUDGET
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$total_ton_ba = $this->_db->fetchOne($sql);
		
		$return = ($res['TOTAL_GAJI_TUNJANGAN'] * $res['MPP_PERIOD_BUDGET'] * 12 * $params['TON'] / $total_ton_ba);
		
		return $return;
	}
	
	public function get_RktPanen_SpvPremi($params){
		//TN_PANEN_PREMI_MANDOR
		$pre_oer = ((float)str_replace(",", "", $params['PRE_OER'])) / 100;
		
		$sql = "
			SELECT SUM(PPB.TON_BUDGET) TON_BUDGET
			FROM TR_PRODUKSI_PERIODE_BUDGET PPB
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$ton = $this->_db->fetchOne($sql);
		
		$sql = "
			SELECT SUM(HS.HA_PLANTED) HA_PLANTED
			FROM TM_HECTARE_STATEMENT HS
			WHERE HS.MATURITY_STAGE_SMS2 = 'TM'
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$ha_planted = $this->_db->fetchOne($sql);
		
		$yield_ba = ($ton && $ha_planted) ? ($ton / $ha_planted / 12) : 0;
		
		$sql = "SELECT NVL(VALUE,0) VALUE
				FROM TN_PANEN_PREMI_MANDOR 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$pre_oer."' BETWEEN MIN_OER AND MAX_OER
					AND '".$yield_ba."' BETWEEN MIN_YIELD AND MAX_YIELD
					AND DELETE_USER IS NULL";			
		$preManValue = $this->_db->fetchOne($sql);
		//PRE MAN
		$sql = "SELECT NVL(VALUE,1) 
				FROM TN_PANEN_VARIABLE 
				WHERE PANEN_CODE='PRE_MAN1' 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$preManVar = $this->_db->fetchOne($sql);
		
		return ($params['TON']*1000)*($preManValue+$preManVar);
	}
	
	public function get_RktPanen_SpvTotal($row){
		return $row['BIAYA_SPV_RP_BASIS'] + $row['BIAYA_SPV_RP_PREMI'];
	}
	
	public function get_RktPanen_SpvKg($row){
		return ($row['TON']!=0)? ($row['BIAYA_SPV_RP_TOTAL']/($row['TON']*1000)):0;
	}
	
	public function get_RktPanen_ToolsKg($params){
		//PRICE KG
		$sql = "SELECT NVL(PRICE_KG,0) FROM TN_ALAT_KERJA_PANEN_SUM 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
		
		$priceTools = $this->_db->fetchOne($sql);
		$priceTools = ($priceTools )?$priceTools :0;
		
		return $priceTools;
	}
	
	public function get_RktPanen_ToolsTotal($row){
		return $row['BIAYA_ALAT_PANEN_RP_KG']*($row['TON']*1000);
	}
	
	public function get_RktPanen_TkgBasis($params){
		/*
		$jarak_pks = (float)str_replace(",", "", $params['JARAK_PKS']);
		
		//RP/KG, TN_PANEN_LOADING
		$sql = "SELECT NVL(RP_KG_BASIS_TM,0) FROM TN_PANEN_LOADING 
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$jarak_pks."' BETWEEN JARAK_PKS_MIN AND JARAK_PKS_MAX
					AND DELETE_USER IS NULL";
					
		$basisRp = $this->_db->fetchOne($sql);
		
		return ($params['TON']*1000)*$basisRp;
		*/
		
		//PERUBAHAN FORMULA : SABRINA 21/08/2014
		$sql = "
			SELECT EMPLOYEE_STATUS, TOTAL_GAJI_TUNJANGAN, MPP_PERIOD_BUDGET
			FROM TR_RKT_CHECKROLL
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND JOB_CODE = 'FW041'
				AND DELETE_USER IS NULL
		";
		$res = $this->_db->fetchAll($sql);
		$total_gaji = 0;
		$total_mpp = 0;
		$total = 0;
		if (!empty($res)) {
            foreach ($res as $idx => $row) {
                $total += $row['TOTAL_GAJI_TUNJANGAN'] * $row['MPP_PERIOD_BUDGET'] * 12;
		    }
	    }
		
		//echo "total : ". $total; die();
		/*$sql = "
			SELECT sum(TOTAL_GAJI_TUNJANGAN) AS total_gaji, sum(MPP_PERIOD_BUDGET) as total_mpp
			FROM TR_RKT_CHECKROLL
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND JOB_CODE = 'FW041'
				AND DELETE_USER IS NULL
			GROUP BY JOB_CODE	
		";
		$res = $this->_db->fetchAll($sql);*/
		//$total_gaji = 0;
		//$total_mpp = 0;
		//$total = 0;
		/*if (!empty($res)) {
			$total_gaji = $res[0]['TOTAL_GAJI'];
			$total_mpp = $res[0]['TOTAL_MPP'];
			$total = $total_gaji*$total_mpp*12;
		}*/
		//die();
		$sql = "
			SELECT SUM(NVL(TON_BUDGET, 0)) TON_BUDGET
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$total_ton_ba = $this->_db->fetchOne($sql);
		
		$return = ($total * $params['TON'] / $total_ton_ba);
		
		return $return;		
	}
	
	public function get_RktPanen_TkgPremi($params){
		$jarak_pks = (float)str_replace(",", "", $params['JARAK_PKS']);
		
		//RP/KG, TN_PANEN_LOADING
		$sql = "SELECT NVL(RP_KG_PREMI_TM,0) FROM TN_PANEN_LOADING 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$jarak_pks."' BETWEEN JARAK_PKS_MIN AND JARAK_PKS_MAX
					AND DELETE_USER IS NULL";					
		$premiRp = $this->_db->fetchOne($sql);
		
		//FORMULA PREMI YG LAMA
		$premi = ($params['TON']*1000)*($premiRp); 
		
		//PERUBAHAN FORMULA : SABRINA 21/08/2014
		$return = $premi + ($params['PERSEN_LANGSIR'] / 100 * $premi);
		
		return $return;
	}
	
	public function get_RktPanen_TkgTotal($row){
		return $row['TUKANG_MUAT_BASIS']+$row['TUKANG_MUAT_PREMI'];
	}
	
	public function get_RktPanen_TkgKg($row){
		$return = ($row['TON']!=0) ? ($row['TUKANG_MUAT_TOTAL']/($row['TON']*1000)) : 0;
		
		return $return;
	}
	
	public function get_RktPanen_SprKg($params){
		$jarak_pks = (float)str_replace(",", "", $params['JARAK_PKS']);
		
		//RP/KG, TN_PANEN_LOADING
		$sql = "SELECT NVL(RP_KG_PREMI_SUPIR,0) FROM TN_PANEN_LOADING 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND '".$jarak_pks."' BETWEEN JARAK_PKS_MIN AND JARAK_PKS_MAX
					AND DELETE_USER IS NULL";
					
		$premiRp = ($this->_db->fetchOne($sql)) ? $this->_db->fetchOne($sql) : 0;
		
		return $premiRp;
	}
	
	public function get_RktPanen_SprPremi($row){
		return $row['SUPIR_RP_KG']*($row['TON']*1000);
	}
	
	public function get_RktPanen_AngkutKGKM($params){
		//TN_PANEN_PREMI_COST_UNIT
		$sql = "SELECT NVL(RP_KG_INTERNAL,0) RP_KG_INTERNAL, NVL(RP_KG_EXTERNAL,0) RP_KG_EXTERNAL
				FROM TN_PANEN_PREMI_COST_UNIT 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
		$res = $this->_db->fetchRow($sql);
		$return = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['RP_KG_INTERNAL'] : $res['RP_KG_EXTERNAL'];
		
		return $return;
	}
	
	public function get_RktPanen_Angkut($params){
		return $params['ANGKUT_TBS_RP_KG_KM']*($params['TON']*1000)*$params['JARAK_PKS'];
	}
	
	public function get_RktPanen_AngkutKG($row){
		return ($row['TON']!=0)? ($row['ANGKUT_TBS_RP_ANGKUT']/($row['TON']*1000)):0;
	}
	
	public function get_RktPanen_KraniBasis($params){
		/*
		//JJG RP/KG, TN_PANEN_KRANI_BUAH
		$sql = "SELECT NVL(RP_KG_BASIS,0) FROM TN_PANEN_KRANI_BUAH 
				WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$keraniBasis = $this->_db->fetchOne($sql);
		
		return ($params['TON']*1000)*$keraniBasis;
		*/
		
		//PERUBAHAN FORMULA : SABRINA 21/08/2014
		$sql = "
			SELECT TOTAL_GAJI_TUNJANGAN, MPP_PERIOD_BUDGET
			FROM TR_RKT_CHECKROLL
			WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
				AND JOB_CODE = 'FX160'
				AND EMPLOYEE_STATUS = 'KT'
				AND DELETE_USER IS NULL
		";
		$res = $this->_db->fetchRow($sql);
		
		$sql = "
			SELECT SUM(NVL(TON_BUDGET, 0)) TON_BUDGET
			FROM TR_PRODUKSI_PERIODE_BUDGET
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$total_ton_ba = $this->_db->fetchOne($sql);
		
		$return = ($res['TOTAL_GAJI_TUNJANGAN'] * $res['MPP_PERIOD_BUDGET'] * 12 * $params['TON'] / $total_ton_ba);
		
		return $return;
	}
	
	public function get_RktPanen_KraniPremi($params){
		//JJG RP/KG, TN_PANEN_KRANI_BUAH
		$sql = "SELECT NVL(RP_KG_PREMI,0) FROM TN_PANEN_KRANI_BUAH 
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND DELETE_USER IS NULL";
					
		$keraniPremi = $this->_db->fetchOne($sql);
		
		return ($params['TON']*1000)*$keraniPremi;
	}
	
	public function get_RktPanen_KraniTotal($row){
		return $row['KRANI_BUAH_BASIS']+$row['KRANI_BUAH_PREMI'];
	}
	
	public function get_RktPanen_KraniKg($row){
		return ($row['TON']!=0)? ($row['KRANI_BUAH_TOTAL']/($row['TON']*1000)):0;
	}
	
	//get_RktPanen_LangsirTon
	public function get_RktPanen_LangsirTon($params){
		$return = ($params['TON']!=0) ? (($params['PERSEN_LANGSIR']/100)*$params['TON']) : 0;
		return $return;
	}
	
	public function get_RktPanen_Langsir($params)
	{
		$sql = "
			SELECT SUM(RP_KG)
			FROM TN_PANEN_PREMI_LANGSIR
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'";
				
		$langsirVal = $this->_db->fetchOne($sql);
		
		return ($params['LANGSIR_TON']*1000)*$langsirVal;
	}
	
	public function get_RktPanen_LangsirKg($row){
		return ($row['TON']!=0)? ($row['LANGSIR_RP']/($row['TON']*1000)):0;
	}
	
	public function get_RktPanen_PercentPanen($params){
		$sql = "
			SELECT JAN,FEB,MAR,APR,MAY,JUN,JUL,AUG,SEP,OCT,NOV,DEC 
			FROM TM_SEBARAN_PRODUKSI 
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'"; 
				
		$monthVal = $this->_db->fetchAll($sql); 
		$mv 	  = $monthVal[0]; 
		$arrmon	  = array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
		
		for($i=0;$i<12;$i++){
			$result[$i]=($mv[$arrmon[$i]]/100);
		}
		return $result;
	}
	
	public function get_RktPanen_CostPanen($totalBiaya,$params){
		$sql = "
			SELECT JAN,FEB,MAR,APR,MAY,JUN,JUL,AUG,SEP,OCT,NOV,DEC 
			FROM TM_SEBARAN_PRODUKSI 
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'";
				
		$monthVal = $this->_db->fetchAll($sql);
		$mv 	  = $monthVal[0];
		$arrmon	  = array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
		
		$totalcost=0;
		for($i=0;$i<12;$i++){
			$result[$i]=($mv[$arrmon[$i]]/100)*$totalBiaya;
			$totalcost=$totalcost+$result[$i];
		}
		$result[12]=$totalcost;
		return $result;
	}
	
	public function cal_RktPanen_Distribusi($params){
		$sebaran = array();
		$result = array();
		$total = 0; $total_sms1 = 0; $total_sms2 = 0;
		
		$sql = "
			SELECT JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC 
			FROM TM_SEBARAN_PRODUKSI 
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
		";
		$sebaran = $this->_db->fetchRow($sql);
		
		$result['COST_JAN'] = $sebaran['JAN'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_JAN']; $total_sms1 += $result['COST_JAN'];
		$result['COST_FEB'] = $sebaran['FEB'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_FEB']; $total_sms1 += $result['COST_FEB'];
		$result['COST_MAR'] = $sebaran['MAR'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_MAR']; $total_sms1 += $result['COST_MAR'];
		$result['COST_APR'] = $sebaran['APR'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_APR']; $total_sms1 += $result['COST_APR'];
		$result['COST_MAY'] = $sebaran['MAY'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_MAY']; $total_sms1 += $result['COST_MAY'];
		$result['COST_JUN'] = $sebaran['JUN'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_JUN']; $total_sms1 += $result['COST_JUN'];
		$result['COST_JUL'] = $sebaran['JUL'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_JUL']; $total_sms2 += $result['COST_JUL'];
		$result['COST_AUG'] = $sebaran['AUG'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_AUG']; $total_sms2 += $result['COST_AUG'];
		$result['COST_SEP'] = $sebaran['SEP'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_SEP']; $total_sms2 += $result['COST_SEP'];
		$result['COST_OCT'] = $sebaran['OCT'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_OCT']; $total_sms2 += $result['COST_OCT'];
		$result['COST_NOV'] = $sebaran['NOV'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_NOV']; $total_sms2 += $result['COST_NOV'];
		$result['COST_DEC'] = $sebaran['DEC'] / 100 * $params['TOTAL_COST_ELEMENT']; $total += $result['COST_DEC']; $total_sms2 += $result['COST_DEC'];
		$result['TOTAL_COST_SMS1'] = $total_sms1;
		$result['TOTAL_COST_SMS2'] = $total_sms2;
		$result['TOTAL_COST'] = $total;
		
		return $result;
	}
	
	public function get_ValueVraPK($arrAfdUpd){
		$sql = "
			SELECT VALUE 
			FROM TR_RKT_VRA_SUM
			WHERE DELETE_USER IS NULL 
				AND BA_CODE = '".addslashes($arrAfdUpd['BA_CODE'])."' 
				AND VRA_CODE = '".addslashes($arrAfdUpd['VRA_CODE'])."' 
		";
		$res = $this->_db->fetchOne($sql);
			
		return $res;
	}
	
	public function get_DistVraPK($params){
		//CARI RP HM ELEMENT VRA
		$sql = "
			SELECT DT_PRICE DT, EXCAV_HM EXCAV, COMPACTOR_HM COMPACTOR, GRADER_HM GRADER
				FROM TN_PERKERASAN_JALAN_HARGA
				WHERE DELETE_USER IS NULL 
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."' 
					AND JARAK_RANGE = '".addslashes($params['RANGE_JARAK'])."' 
			";
					
		$res = $this->_db->fetchRow($sql);
		
		return $res;
	}
	
	public function get_DistVraPanenAngkTBS($params){
		$sql = "
			SELECT NVL(RP_KM_INTERNAL,0) 
			FROM TN_PANEN_PREMI_COST_UNIT 
			WHERE DELETE_USER IS NULL 
				AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND BA_CODE = '".addslashes($params['BA_CODE'])."' 
		";
		$rpKmInt = $this->_db->fetchOne($sql); 
		
		//HM_KM, PRICE_QTY_VRA
		return ($rpKmInt==0) ? (array(0,0)) : (array($params['SUM_ANGKUT']/$rpKmInt,$rpKmInt));
	}
	
	public function get_DistVraPanenLangsir($langsirTon,$params){ 
		if (($params['TONTRIP']*$params['HMTRIP'])==0){
			$result = array(0,0);
		}else{
			$result[0] = $langsirTon/$params['TONTRIP']*$params['HMTRIP']/10*2;
			$result[1] = $params['TONTRIP']*$params['HMTRIP'];
		}
		return $result;
	}
	
	public function get_DistVraManInfra($params,$row){
		$sql="SELECT SUM(NVL(RKT.PLAN_SETAHUN,0)) SUM_PLAN_SETAHUN 
			  FROM TM_HECTARE_STATEMENT HS
			  LEFT JOIN TR_RKT RKT
				ON HS.PERIOD_BUDGET = RKT.PERIOD_BUDGET
				AND HS.BA_CODE = RKT.BA_CODE
				AND HS.AFD_CODE = RKT.AFD_CODE
				AND HS.BLOCK_CODE = RKT.BLOCK_CODE
				AND RKT.TIPE_TRANSAKSI='MANUAL_INFRA' 
				AND RKT.DELETE_USER IS NULL
			  WHERE RKT.ACTIVITY_CLASS = '".addslashes($params['ACTIVITY_CLASS'])."'
				AND RKT.ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."' 
				AND HS.PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
				AND HS.AFD_CODE='{$params['AFD_CODE']}'
				AND HS.BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND HS.LAND_TYPE = '".addslashes($params['LAND_TYPE'])."'
				AND HS.TOPOGRAPHY = '".addslashes($params['TOPOGRAPHY'])."'
				AND HS.DELETE_USER IS NULL";
		$sum_plan_setahun = $this->_db->fetchOne($sql); 
		return $sum_plan_setahun*$row['QTY_ALAT'];
	}

	//cari rotasi semester 1 & 2 untuk RKT Manual
	public function get_RktManual_Rotasi($params = array())
    {
		if ($params['TIPE_RKT_MANUAL'] == 'INFRA'){
			$sql = "
				SELECT ACTIVITY_CLASS,LAND_TYPE,TOPOGRAPHY,NVL (ROTASI, 0) ROTASI
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
				 AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				 AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				 AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
				 AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
				 AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
				 AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
				 AND COST_ELEMENT IN ('LABOUR')
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
			";
			
			$res = $this->_db->fetchRow($sql);
			$result['SMS1'] = $res['ROTASI'];
			$result['SMS2'] = $res['ROTASI'];
		}
		elseif ($params['TIPE_RKT_MANUAL'] == 'NON_INFRA_OPSI'){
			
			if ($params['TIPE_NORMA'] == 'UMUM'){
				//SEMESTER 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI, 0) ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS1'] = $res['ROTASI'];
			
				//SEMESTER 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI, 0) ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS2'] = $res['ROTASI'];
			}
			//<!-- TIPE NORMA -->
			elseif ($params['TIPE_NORMA'] == 'KHUSUS'){
				//SEMESTER 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI_SITE, 0) ROTASI_SITE
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS1'] = $res['ROTASI_SITE'];
			
				//SEMESTER 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI_SITE, 0) ROTASI_SITE
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS2'] = $res['ROTASI_SITE'];
			}
		}
		else{
			if ($params['TIPE_NORMA'] == 'UMUM'){
				//SEMESTER 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI, 0) ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS1'] = $res['ROTASI'];
			
				//SEMESTER 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI, 0) ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS2'] = $res['ROTASI'];
			}
			//<!-- TIPE NORMA -->
			elseif ($params['TIPE_NORMA'] == 'KHUSUS'){
				//SEMESTER 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI_SITE, 0) ROTASI_SITE
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS1'] = $res['ROTASI_SITE'];
			
				//SEMESTER 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(ROTASI_SITE, 0) ROTASI_SITE
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result['SMS2'] = $res['ROTASI_SITE'];
			}
		}
		
		$total_rotasi = $total_rotasi_sms1 = $total_rotasi_sms2 = 0;
		$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);
		
		$total_rotasi += $PLAN_JAN; $total_rotasi_sms1 += $PLAN_JAN;
		$total_rotasi += $PLAN_FEB; $total_rotasi_sms1 += $PLAN_FEB;
		$total_rotasi += $PLAN_MAR; $total_rotasi_sms1 += $PLAN_MAR;
		$total_rotasi += $PLAN_APR; $total_rotasi_sms1 += $PLAN_APR;
		$total_rotasi += $PLAN_MAY; $total_rotasi_sms1 += $PLAN_MAY;
		$total_rotasi += $PLAN_JUN; $total_rotasi_sms1 += $PLAN_JUN;
		$total_rotasi += $PLAN_JUL; $total_rotasi_sms2 += $PLAN_JUL;
		$total_rotasi += $PLAN_AUG; $total_rotasi_sms2 += $PLAN_AUG;
		$total_rotasi += $PLAN_SEP; $total_rotasi_sms2 += $PLAN_SEP;
		$total_rotasi += $PLAN_OCT; $total_rotasi_sms2 += $PLAN_OCT;
		$total_rotasi += $PLAN_NOV; $total_rotasi_sms2 += $PLAN_NOV;
		$total_rotasi += $PLAN_DEC; $total_rotasi_sms2 += $PLAN_DEC;
		
		$result['TOTAL_PLAN_SETAHUN'] = $total_rotasi; // PLAN SETAHUN PER BLOK
		$result['TOTAL_PLAN_SMS1'] = $total_rotasi_sms1; // PLAN SMS 1 PER BLOK
		$result['TOTAL_PLAN_SMS2'] = $total_rotasi_sms2; // PLAN SMS 2 PER BLOK
			
        return $result;
    }

	public function cal_RktLc_CostElement($element, $row = array()){
		if($element=="CONTRACT"){
			$sql="SELECT ACTIVITY_CLASS, NVL(PRICE,0) PRICE
				  FROM TN_HARGA_BORONG 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND DELETE_USER IS NULL
			ORDER BY ACTIVITY_CLASS";
			$res = $this->_db->fetchRow($sql);
			//$price = ($res['PRICE']) ? $res['PRICE'] : 0;
			$price = ($row['SUMBER_BIAYA'] == 'EXTERNAL') ? $res['PRICE'] : 0;
		}else if($element=="LABOUR"){
			$sql="SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
				  FROM TN_BIAYA 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($row['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($row['TOPOGRAPHY'])."')
					AND  COST_ELEMENT = 'LABOUR' 
					AND DELETE_USER IS NULL
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY";
			$res = $this->_db->fetchRow($sql);
			//$price = ($res['PRICE_ROTASI']) ? $res['PRICE_ROTASI'] : 0;
			$price = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
		}else if($element=="MATERIAL"){			
			$sql="SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
				  FROM TN_BIAYA 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($row['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($row['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'MATERIAL' 
					AND DELETE_USER IS NULL 
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY";
			$res = $this->_db->fetchRow($sql);
			//$price = ($res['PRICE_ROTASI']) ? $res['PRICE_ROTASI'] : 0;
			$price = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
		}else if($element=="TRANSPORT"){ 
			/*$sql="SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, SUM(NVL(PLAN_SETAHUN,0)) PLAN_SETAHUN
				  FROM TR_RKT_LC 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($row['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($row['TOPOGRAPHY'])."')
					AND DELETE_USER IS NULL 
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY";*/
				
			$sql="SELECT SUM(NVL(PLAN_SETAHUN,0)) PLAN_SETAHUN
				  FROM TR_RKT_LC 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND DELETE_USER IS NULL 
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY";
				
			//die($sql);				
			$res = $this->_db->fetchRow($sql);
			$planTotalYear = ($res['PLAN_SETAHUN']) ? $res['PLAN_SETAHUN'] : 0;
			
			if ($planTotalYear && $planTotalYear<>0){
				$sql="SELECT SUM(NVL(TOTAL_PRICE_HM_KM,0))/{$planTotalYear} 
					  FROM TR_RKT_VRA_DISTRIBUSI_SUM 
					  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
						AND BA_CODE = '{$row['BA_CODE']}' 
						AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
						AND TIPE_TRANSAKSI = 'NON_INFRA' 
						AND DELETE_USER IS NULL"; //TOTAL BIAYA SELURUH AFDELING
				$price = ($this->_db->fetchOne($sql)) ? $this->_db->fetchOne($sql) : 0;
			}else{
				$price = 0;
			}
		}else if($element=="TOOLS"){
			$sql="SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
				  FROM TN_BIAYA 
				  WHERE PERIOD_BUDGET = TO_DATE('01-01-{$row['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '{$row['BA_CODE']}' 
					AND ACTIVITY_CODE = '{$row['ACTIVITY_CODE']}' 
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($row['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($row['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($row['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'TOOLS' 
					AND DELETE_USER IS NULL 
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY";
			$res = $this->_db->fetchRow($sql);
			//$price = ($res['PRICE_ROTASI']) ? $res['PRICE_ROTASI'] : 0;
			$price = ($row['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
		}
		
		return $price;
	}
	
	//cost total RKT Manual
	public function cal_RktManual_CostElement($costElement, $params = array())
    {
		//<!-- TIPE NORMA -->
		if($costElement == 'LABOUR'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}
		}
		elseif($costElement == 'MATERIAL'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		// COST ELEMENT TOOLS DIAMBIL DARI NORMA BIAYA (BPS 1, AMBIL DR NORMA ALAT KERJA NON PANEN)
		elseif($costElement == 'TOOLS'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TRANSPORT'){
			$sql = "
				SELECT (SUM(PLAN_JAN) + SUM(PLAN_FEB) + SUM(PLAN_MAR) + SUM(PLAN_APR) + SUM(PLAN_MAY) + SUM(PLAN_JUN) + SUM(PLAN_JUL) + SUM(PLAN_AUG) + SUM(PLAN_SEP) + SUM(PLAN_OCT) + SUM(PLAN_NOV) + SUM(PLAN_DEC)) TOTAL_ROTASI
				FROM TR_RKT
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND AFD_CODE = '".addslashes($params['AFD_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND TIPE_TRANSAKSI = 'MANUAL_NON_INFRA'
			";
			$total_rotasi_per_afd = $this->_db->fetchOne($sql);
			
			if($total_rotasi_per_afd){
				$sql = "
					SELECT VRA_CODE, HM_KM
					FROM TR_RKT_VRA_DISTRIBUSI
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND LOCATION_CODE = '".addslashes($params['AFD_CODE'])."'
						AND TIPE_TRANSAKSI = 'NON_INFRA'
						AND DELETE_USER IS NULL
				";
				$rows = $this->_db->fetchAll($sql);
				if (!empty($rows)) {
					$region_code = $this->get_RegionCode($params['BA_CODE']);
					foreach ($rows as $idx => $row) {
						//cari Rp/Qty : 
						//jika awalan ZZ_ maka VRA pinjaman dan ambil Rp/Qty dari TN_VRA_PINJAM
						//jika tidak ambil dari TR_RKT_VRA_SUM
						
						if (substr($row['VRA_CODE'], 0, 3) == 'ZZ_'){
							
							$query = "
								SELECT RP_QTY
								FROM TN_VRA_PINJAM
								WHERE REGION_CODE='".$region_code."' 
									AND VRA_CODE='".$row['VRA_CODE']."'
									AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$params['PERIOD_BUDGET']}' 
									AND DELETE_USER IS NULL
									AND FLAG_TEMP IS NULL
							";
						}else{
							$query = "
								SELECT VALUE
								FROM TR_RKT_VRA_SUM
								WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
									AND BA_CODE = '".addslashes($params['BA_CODE'])."'
									AND VRA_CODE = '".addslashes($row['VRA_CODE'])."'
									AND DELETE_USER IS NULL
							";
						}
						
						$rp_qty = $this->_db->fetchOne($query);
						$cost_transport[$row['VRA_CODE']] = $rp_qty * $row['HM_KM'];
					}
				}
				$result[1] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
				$result[2] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
			}else{
				$result[1] = 0;
				$result[2] =0;
			}
		}
		// COST ELEMENT CONTRACT DIAMBIL DARI NORMA BIAYA (BPS 1, AMBIL DR NORMA HARGA BORONG)
		elseif($costElement == 'CONTRACT'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		
		return $result;
    }
	
	//cost total RKT Manual Kastrasi - Sanitasi
	public function cal_RktKastrasiSanitasi_CostElement($costElement, $params = array())
    {
		//<!-- TIPE NORMA -->
		if($costElement == 'LABOUR'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}
		}
		elseif($costElement == 'MATERIAL'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		// COST ELEMENT TOOLS DIAMBIL DARI NORMA BIAYA (BPS 1, AMBIL DR NORMA ALAT KERJA NON PANEN)
		elseif($costElement == 'TOOLS'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TRANSPORT'){
			$sql = "
				SELECT (SUM(PLAN_JAN) + SUM(PLAN_FEB) + SUM(PLAN_MAR) + SUM(PLAN_APR) + SUM(PLAN_MAY) + SUM(PLAN_JUN) + SUM(PLAN_JUL) + SUM(PLAN_AUG) + SUM(PLAN_SEP) + SUM(PLAN_OCT) + SUM(PLAN_NOV) + SUM(PLAN_DEC)) TOTAL_ROTASI
				FROM TR_RKT
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND AFD_CODE = '".addslashes($params['AFD_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND TIPE_TRANSAKSI = 'KASTRASI_SANITASI'
			";
			$total_rotasi_per_afd = $this->_db->fetchOne($sql);
			
			if($total_rotasi_per_afd){
				$sql = "
					SELECT VRA_CODE, HM_KM
					FROM TR_RKT_VRA_DISTRIBUSI
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND LOCATION_CODE = '".addslashes($params['AFD_CODE'])."'
						AND TIPE_TRANSAKSI = 'NON_INFRA'
						AND DELETE_USER IS NULL
				";
				$rows = $this->_db->fetchAll($sql);
				if (!empty($rows)) {
					$region_code = $this->get_RegionCode($params['BA_CODE']);
					foreach ($rows as $idx => $row) {
						//cari Rp/Qty : 
						//jika awalan ZZ_ maka VRA pinjaman dan ambil Rp/Qty dari TN_VRA_PINJAM
						//jika tidak ambil dari TR_RKT_VRA_SUM
						
						if (substr($row['VRA_CODE'], 0, 3) == 'ZZ_'){
							
							$query = "
								SELECT RP_QTY
								FROM TN_VRA_PINJAM
								WHERE REGION_CODE='".$region_code."' 
									AND VRA_CODE='".$row['VRA_CODE']."'
									AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$params['PERIOD_BUDGET']}' 
									AND DELETE_USER IS NULL
									AND FLAG_TEMP IS NULL
							";
						}else{
							$query = "
								SELECT VALUE
								FROM TR_RKT_VRA_SUM
								WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
									AND BA_CODE = '".addslashes($params['BA_CODE'])."'
									AND VRA_CODE = '".addslashes($row['VRA_CODE'])."'
									AND DELETE_USER IS NULL
							";
						}
						
						$rp_qty = $this->_db->fetchOne($query);
						$cost_transport[$row['VRA_CODE']] = $rp_qty * $row['HM_KM'];
					}
				}
				$result[1] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
				$result[2] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
			}else{
				$result[1] = 0;
				$result[2] =0;
			}
		}
		// COST ELEMENT CONTRACT DIAMBIL DARI NORMA BIAYA (BPS 1, AMBIL DR NORMA HARGA BORONG)
		elseif($costElement == 'CONTRACT'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		
		return $result;
    }

	//cost total RKT Manual Non Infra - Opsi
	public function cal_RktManual_CostElementOpsi($costElement, $params = array())
    {
		if($costElement == 'LABOUR'){
			//LABOUR SMS 1
			if($params['TIPE_NORMA'] == 'UMUM'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}
		}
		elseif($costElement == 'MATERIAL'){
			//MATERIAL SMS 1
			if($params['TIPE_NORMA'] == 'UMUM'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TOOLS'){
			if($params['TIPE_NORMA'] == 'UMUM'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TRANSPORT'){
			$sql = "
				SELECT (SUM(PLAN_JAN) + SUM(PLAN_FEB) + SUM(PLAN_MAR) + SUM(PLAN_APR) + SUM(PLAN_MAY) + SUM(PLAN_JUN) + SUM(PLAN_JUL) + SUM(PLAN_AUG) + SUM(PLAN_SEP) + SUM(PLAN_OCT) + SUM(PLAN_NOV) + SUM(PLAN_DEC)) TOTAL_ROTASI
				FROM TR_RKT
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND AFD_CODE = '".addslashes($params['AFD_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND TIPE_TRANSAKSI = 'MANUAL_NON_INFRA_OPSI'
			";
			$total_rotasi_per_afd = $this->_db->fetchOne($sql);
			
			if($total_rotasi_per_afd){
				$sql = "
					SELECT VRA_CODE, HM_KM
					FROM TR_RKT_VRA_DISTRIBUSI
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND LOCATION_CODE = '".addslashes($params['AFD_CODE'])."'
						AND TIPE_TRANSAKSI = 'NON_INFRA'
						AND DELETE_USER IS NULL
				";
				$rows = $this->_db->fetchAll($sql);
				if (!empty($rows)) {
					$region_code = $this->get_RegionCode($params['BA_CODE']);
					foreach ($rows as $idx => $row) {
						//cari Rp/Qty : 
						//jika awalan ZZ_ maka VRA pinjaman dan ambil Rp/Qty dari TN_VRA_PINJAM
						//jika tidak ambil dari TR_RKT_VRA_SUM
						
						if (substr($row['VRA_CODE'], 0, 3) == 'ZZ_'){
							
							$query = "
								SELECT RP_QTY
								FROM TN_VRA_PINJAM
								WHERE REGION_CODE='".$region_code."' 
									AND VRA_CODE='".$row['VRA_CODE']."'
									AND TO_CHAR (PERIOD_BUDGET, 'DD-MM-RRRR') = '01-01-{$params['PERIOD_BUDGET']}' 
									AND DELETE_USER IS NULL
									AND FLAG_TEMP IS NULL
							";
						}else{
							$query = "
								SELECT VALUE
								FROM TR_RKT_VRA_SUM
								WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
									AND BA_CODE = '".addslashes($params['BA_CODE'])."'
									AND VRA_CODE = '".addslashes($row['VRA_CODE'])."'
									AND DELETE_USER IS NULL
							";
						}
						$rp_qty = $this->_db->fetchOne($query);
						$cost_transport[$row['VRA_CODE']] = $rp_qty * $row['HM_KM'];
					}
				}
				$result[1] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
				$result[2] = (!empty($cost_transport) && $total_rotasi_per_afd>0) ? ((array_sum($cost_transport)) / $total_rotasi_per_afd) : 0;
			}else{
				$result[1] = 0;
				$result[2] = 0;
			}
		}	
		elseif($costElement == 'CONTRACT'){
			//CONTRACT
			if($params['TIPE_NORMA'] == 'UMUM'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS1'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_GROUP = '".addslashes($params['MATURITY_STAGE_SMS2'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ATRIBUT'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
        return $result;
    }

	//cost total RKT Manual - Infra
	public function cal_RktManual_CostElementInfra($costElement, $params = array())
    {
		if($costElement == 'LABOUR'){
			//LABOUR 
			$sql = "
				SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(RP_QTY_INTERNAL), 0) PRICE_ROTASI
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'LABOUR'
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
			";
			$res = $this->_db->fetchRow($sql);
			$cost_labour = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
			$result[1] = $cost_labour;
			$result[2] = $cost_labour;
		}
		elseif($costElement == 'MATERIAL'){
			//MATERIAL
			$sql = "
				SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(RP_QTY_INTERNAL), 0) PRICE_ROTASI
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'MATERIAL'
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
			";
			$res = $this->_db->fetchRow($sql);
			$cost_material = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
			$result[1] = $cost_material;
			$result[2] = $cost_material;
		}
		elseif($costElement == 'TOOLS'){
			//TOOLS
			$sql = "
				SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(RP_QTY_INTERNAL), 0) PRICE_ROTASI
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'TOOLS'
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
			";
			$res = $this->_db->fetchRow($sql);
			$cost_tools = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
			$result[1] = $cost_tools;
			$result[2] = $cost_tools;
		}
		elseif($costElement == 'TRANSPORT'){
			//TRANSPORT
			//eksternal tidak ambil transport dari norma infra (konfirmasi : LEF 09/09/2013)
			if($params['SUMBER_BIAYA'] == 'INTERNAL'){
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(RP_QTY_INTERNAL), 0) PRICE_ROTASI
					FROM TN_INFRASTRUKTUR
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TRANSPORT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				//$cost_transport = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				//$cost_transport = $res['PRICE_ROTASI'];
				$result[1] = $res['PRICE_ROTASI'];
				$result[2] = $res['PRICE_ROTASI'];
			}else{
				$result[1] = 0;
				$result[2] = 0;
			}		
		}
		elseif($costElement == 'CONTRACT'){
			//CONTRACT
			if($params['SUMBER_BIAYA'] == 'INTERNAL'){
				$result[1] = 0;
				$result[2] = 0;
			}else{
				if($params['TIPE_NORMA'] == 'UMUM'){
					//CONTRACT
					$sql = "
						SELECT ACTIVITY_CLASS, NVL(SUM(PRICE), 0) PRICE_ROTASI
						FROM TN_HARGA_BORONG
						WHERE DELETE_USER IS NULL
							AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
							AND BA_CODE = '".addslashes($params['BA_CODE'])."'
							AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
							AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						GROUP BY ACTIVITY_CLASS
						ORDER BY ACTIVITY_CLASS
					";
					$res = $this->_db->fetchRow($sql);
					$cost_contract = $res['PRICE_ROTASI'];
					$result[1] = $cost_contract;
					$result[2] = $cost_contract;
				}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
					//CONTRACT
					$sql = "
						SELECT ACTIVITY_CLASS, NVL(SUM(PRICE_SITE), 0) PRICE_ROTASI
						FROM TN_HARGA_BORONG
						WHERE DELETE_USER IS NULL
							AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
							AND BA_CODE = '".addslashes($params['BA_CODE'])."'
							AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
							AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						GROUP BY ACTIVITY_CLASS
						ORDER BY ACTIVITY_CLASS
					";
					$res = $this->_db->fetchRow($sql);
					$cost_contract = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
					$result[1] = $cost_contract;
					$result[2] = $cost_contract;
				}
			}
		}
        return $result;
    }
	
	//total RKT
	public function cal_RktManual_Total($params = array())
    {
		$total_cost = $cost_sms1 = $cost_sms2 = 0;
		$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);
		
		$cost_jan = $PLAN_JAN * $params['TOTAL_RP_SMS1']; $result['COST_JAN'] = $cost_jan; $total_cost += $cost_jan; $cost_sms1 += $cost_jan;
		$cost_feb = $PLAN_FEB * $params['TOTAL_RP_SMS1']; $result['COST_FEB'] = $cost_feb; $total_cost += $cost_feb; $cost_sms1 += $cost_feb;
		$cost_mar = $PLAN_MAR * $params['TOTAL_RP_SMS1']; $result['COST_MAR'] = $cost_mar; $total_cost += $cost_mar; $cost_sms1 += $cost_mar;
		$cost_apr = $PLAN_APR * $params['TOTAL_RP_SMS1']; $result['COST_APR'] = $cost_apr; $total_cost += $cost_apr; $cost_sms1 += $cost_apr;
		$cost_may = $PLAN_MAY * $params['TOTAL_RP_SMS1']; $result['COST_MAY'] = $cost_may; $total_cost += $cost_may; $cost_sms1 += $cost_may;
		$cost_jun = $PLAN_JUN * $params['TOTAL_RP_SMS1']; $result['COST_JUN'] = $cost_jun; $total_cost += $cost_jun; $cost_sms1 += $cost_jun;
		$cost_jul = $PLAN_JUL * $params['TOTAL_RP_SMS2']; $result['COST_JUL'] = $cost_jul; $total_cost += $cost_jul; $cost_sms2 += $cost_jul;
		$cost_aug = $PLAN_AUG * $params['TOTAL_RP_SMS2']; $result['COST_AUG'] = $cost_aug; $total_cost += $cost_aug; $cost_sms2 += $cost_aug;
		$cost_sep = $PLAN_SEP * $params['TOTAL_RP_SMS2']; $result['COST_SEP'] = $cost_sep; $total_cost += $cost_sep; $cost_sms2 += $cost_sep;
		$cost_oct = $PLAN_OCT * $params['TOTAL_RP_SMS2']; $result['COST_OCT'] = $cost_oct; $total_cost += $cost_oct; $cost_sms2 += $cost_oct;
		$cost_nov = $PLAN_NOV * $params['TOTAL_RP_SMS2']; $result['COST_NOV'] = $cost_nov; $total_cost += $cost_nov; $cost_sms2 += $cost_nov;
		$cost_dec = $PLAN_DEC * $params['TOTAL_RP_SMS2']; $result['COST_DEC'] = $cost_dec; $total_cost += $cost_dec; $cost_sms2 += $cost_dec;
		
		$result['COST_SMS1'] = $cost_sms1;
		$result['COST_SMS2'] = $cost_sms2;
		$result['TOTAL_RP_SETAHUN'] = $total_cost;
		
        return $result;
    }

	//total RKT
	public function cal_RktManual_TotalTanam($params = array())
    {
		$total_cost = $cost_sms1 = $cost_sms2 = 0;
		$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);
		
		$cost_jan = $PLAN_JAN * $params['BASIC_TOTAL_RP_QTY']; $result['COST_JAN'] = $cost_jan;	$total_cost += $cost_jan; $cost_sms1 += $cost_jan;
		$cost_feb = $PLAN_FEB * $params['BASIC_TOTAL_RP_QTY']; $result['COST_FEB'] = $cost_feb;	$total_cost += $cost_feb; $cost_sms1 += $cost_feb;
		$cost_mar = $PLAN_MAR * $params['BASIC_TOTAL_RP_QTY']; $result['COST_MAR'] = $cost_mar;	$total_cost += $cost_mar; $cost_sms1 += $cost_mar;
		$cost_apr = $PLAN_APR * $params['BASIC_TOTAL_RP_QTY']; $result['COST_APR'] = $cost_apr;	$total_cost += $cost_apr; $cost_sms1 += $cost_apr;
		$cost_may = $PLAN_MAY * $params['BASIC_TOTAL_RP_QTY']; $result['COST_MAY'] = $cost_may;	$total_cost += $cost_may; $cost_sms1 += $cost_may;
		$cost_jun = $PLAN_JUN * $params['BASIC_TOTAL_RP_QTY']; $result['COST_JUN'] = $cost_jun;	$total_cost += $cost_jun; $cost_sms1 += $cost_jun;
		$cost_jul = $PLAN_JUL * $params['BASIC_TOTAL_RP_QTY']; $result['COST_JUL'] = $cost_jul;	$total_cost += $cost_jul; $cost_sms2 += $cost_jul;
		$cost_aug = $PLAN_AUG * $params['BASIC_TOTAL_RP_QTY']; $result['COST_AUG'] = $cost_aug;	$total_cost += $cost_aug; $cost_sms2 += $cost_aug;
		$cost_sep = $PLAN_SEP * $params['BASIC_TOTAL_RP_QTY']; $result['COST_SEP'] = $cost_sep;	$total_cost += $cost_sep; $cost_sms2 += $cost_sep;
		$cost_oct = $PLAN_OCT * $params['BASIC_TOTAL_RP_QTY']; $result['COST_OCT'] = $cost_oct;	$total_cost += $cost_oct; $cost_sms2 += $cost_oct;
		$cost_nov = $PLAN_NOV * $params['BASIC_TOTAL_RP_QTY']; $result['COST_NOV'] = $cost_nov;	$total_cost += $cost_nov; $cost_sms2 += $cost_nov;
		$cost_dec = $PLAN_DEC * $params['BASIC_TOTAL_RP_QTY']; $result['COST_DEC'] = $cost_dec;	$total_cost += $cost_dec; $cost_sms2 += $cost_dec;
		
		$result['COST_SMS1'] = $cost_sms1;
		$result['COST_SMS2'] = $cost_sms2;
		$result['TOTAL_RP_SETAHUN'] = $total_cost;
		
        return $result;
    }
	
	//cari harga / qty RKT perkerasan jalan
	public function get_RKTPerkerasanJalan_hargaqty($params = array())
    {
   		if ($params['SUMBER_BIAYA'] == 'INTERNAL'){
		$sql = "
			SELECT INTERNAL_PRICE AS PRICE
			  FROM TN_PERKERASAN_JALAN_HARGA
			 WHERE     PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				   AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				   AND ACTIVITY_CODE = '".addslashes($params['src_coa'])."'
				   AND JARAK_RANGE = '".addslashes($params['JARAK'])."'
			";
			$result = $this->_db->fetchOne($sql);
	   }else{
		$sql = "
			SELECT EXTERNAL_PRICE AS PRICE
			  FROM TN_PERKERASAN_JALAN_HARGA
			 WHERE     PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				   AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				   AND ACTIVITY_CODE = '".addslashes($params['src_coa'])."'
				   AND JARAK_RANGE = '".addslashes($params['JARAK'])."'
			";
			$result = $this->_db->fetchOne($sql);
		}
		return $result;

	}

	//cost total RKT Perkerasan Jalan
	public function cal_RktPerkerasanJalan_CostElement($costElement, $params = array())
    {
		if($costElement == 'LABOUR'){
			//LABOUR - PERKERASAN JALAN cost element labour uda masuk di VRA - SABRINA 30/08/2014
			$result[1] = 0;
			$result[2] = 0;
		}
		elseif($costElement == 'MATERIAL'){
			//MATERIAL
			$sql = "
					SELECT BIAYA_MATERIAL
					FROM TN_PERKERASAN_JALAN_HARGA
					WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND JARAK_RANGE = '".addslashes($params['JARAK'])."'
			";
			
			$costmaterial = $this->_db->fetchOne($sql);
			$cost_material = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $costmaterial : 0;
			$material = $cost_material / 1000;
			$result[1] = $material;
		}
		elseif($costElement == 'TOOLS'){
			//TOOLS
			$sql = "
				SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(RP_QTY_INTERNAL), 0) PRICE_ROTASI
				FROM TN_INFRASTRUKTUR
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
					AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
					AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
					AND COST_ELEMENT = 'TOOLS'
				GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
			";
			$res = $this->_db->fetchRow($sql);
			$cost_tools = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
			$result[1] = $cost_tools;
			$result[2] = $cost_tools;
		}
		elseif($costElement == 'TRANSPORT'){
			//TRANSPORT
			$sql = "
				SELECT NVL((INTERNAL_PRICE - BIAYA_MATERIAL), 0) AS BIAYA_KENDARAAN
				FROM TN_PERKERASAN_JALAN_HARGA
				WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
				AND JARAK_RANGE = '".addslashes($params['JARAK'])."'
			";
			
			$costtransport = $this->_db->fetchOne($sql);
			$cost_transport = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $costtransport : 0;
			$transport = $cost_transport / 1000;
			$result[1] = $transport;
		}
		elseif($costElement == 'CONTRACT'){
			//CONTRACT
			$sql = "
				SELECT EXTERNAL_PRICE AS PRICE
				  FROM TN_PERKERASAN_JALAN_HARGA
				 WHERE     PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					   AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					   AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					   AND JARAK_RANGE = '".addslashes($params['JARAK'])."'
			";
			
			$cost_contract = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $this->_db->fetchOne($sql);
			$contract = $cost_contract / 1000;
			$result[1] = $contract;
		}
        return $result;
    }
	
	//hitung distribusi jumlah & biaya untuk RKT perkerasan jalan
	public function cal_RktPerkerasanJalan_DistribusiTahunBerjalan($params = array())
    {
		$total_cost = 0;
		$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);

		$cost_jan = $PLAN_JAN * $params['PRICE_QTY'];	$result['COST_JAN'] = $cost_jan;	$total_cost += $cost_jan;
		$cost_feb = $PLAN_FEB * $params['PRICE_QTY'];	$result['COST_FEB'] = $cost_feb;	$total_cost += $cost_feb;
		$cost_mar = $PLAN_MAR * $params['PRICE_QTY'];	$result['COST_MAR'] = $cost_mar;	$total_cost += $cost_mar;
		$cost_apr = $PLAN_APR * $params['PRICE_QTY'];	$result['COST_APR'] = $cost_apr;	$total_cost += $cost_apr;
		$cost_may = $PLAN_MAY * $params['PRICE_QTY'];	$result['COST_MAY'] = $cost_may;	$total_cost += $cost_may;
		$cost_jun = $PLAN_JUN * $params['PRICE_QTY'];	$result['COST_JUN'] = $cost_jun;	$total_cost += $cost_jun;
		$cost_jul = $PLAN_JUL * $params['PRICE_QTY'];	$result['COST_JUL'] = $cost_jul;	$total_cost += $cost_jul;
		$cost_aug = $PLAN_AUG * $params['PRICE_QTY'];	$result['COST_AUG'] = $cost_aug;	$total_cost += $cost_aug;
		$cost_sep = $PLAN_SEP * $params['PRICE_QTY'];	$result['COST_SEP'] = $cost_sep;	$total_cost += $cost_sep;
		$cost_oct = $PLAN_OCT * $params['PRICE_QTY'];	$result['COST_OCT'] = $cost_oct;	$total_cost += $cost_oct;
		$cost_nov = $PLAN_NOV * $params['PRICE_QTY'];	$result['COST_NOV'] = $cost_nov;	$total_cost += $cost_nov;
		$cost_dec = $PLAN_DEC * $params['PRICE_QTY'];	$result['COST_DEC'] = $cost_dec;	$total_cost += $cost_dec;
		
		$result['TOTAL_RP_SETAHUN'] = $total_cost;
		
        return $result;
    }		
	
	//hitung distribusi jumlah & biaya untuk RKT tanam
	public function cal_RktTanam_DistribusiHa($params = array())
    {
		$total_cost = 0;
		/*$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);*/
		$PLAN_JAN = 0;
		$PLAN_FEB = 0;
		$PLAN_MAR = 0;
		$PLAN_APR = 0;
		$PLAN_MAY = 0;
		$PLAN_JUN = 0;
		$PLAN_JUL = 0;
		$PLAN_AUG = 0;
		$PLAN_SEP = 0;
		$PLAN_OCT = 0;
		$PLAN_NOV = 0;
		$PLAN_DEC = 0;

		//print_r($params);die();	
		if($params['TAHUN_TANAM_Y']==$params['PERIOD_BUDGET']){
			if ($params['TAHUN_TANAM_M'] == '01'){
				$PLAN_JAN = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '02'){
				$PLAN_FEB = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '03'){
				$PLAN_MAR = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '04'){
				$PLAN_APR = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '05'){
				$PLAN_MAY = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '06'){
				$PLAN_JUN = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '07'){
				$PLAN_JUL = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '08'){
				$PLAN_AUG = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '09'){
				$PLAN_SEP = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '10'){
				$PLAN_OCT = $params['HA_PLANTED'];
			} else if ($params['TAHUN_TANAM_M'] == '11'){
				$PLAN_NOV = $params['HA_PLANTED'];
			} else if($params['TAHUN_TANAM_M'] == '12'){
				$PLAN_DEC = $params['HA_PLANTED'];
			}
		}
		
		$result['PLAN_JAN'] = $PLAN_JAN;
		$result['PLAN_FEB'] = $PLAN_FEB;
		$result['PLAN_MAR'] = $PLAN_MAR;
		$result['PLAN_APR'] = $PLAN_APR;
		$result['PLAN_MAY'] = $PLAN_MAY;
		$result['PLAN_JUN'] = $PLAN_JUN;
		$result['PLAN_JUL'] = $PLAN_JUL;
		$result['PLAN_AUG'] = $PLAN_AUG;
		$result['PLAN_SEP'] = $PLAN_SEP;
		$result['PLAN_OCT'] = $PLAN_OCT;
		$result['PLAN_NOV'] = $PLAN_NOV;
		$result['PLAN_DEC'] = $PLAN_DEC;
		
		$cost_jan = $PLAN_JAN * $params['PRICE_QTY'];	$result['COST_JAN'] = $cost_jan;	$total_cost += $cost_jan;
		$cost_feb = $PLAN_FEB * $params['PRICE_QTY'];	$result['COST_FEB'] = $cost_feb;	$total_cost += $cost_feb;
		$cost_mar = $PLAN_MAR * $params['PRICE_QTY'];	$result['COST_MAR'] = $cost_mar;	$total_cost += $cost_mar;
		$cost_apr = $PLAN_APR * $params['PRICE_QTY'];	$result['COST_APR'] = $cost_apr;	$total_cost += $cost_apr;
		$cost_may = $PLAN_MAY * $params['PRICE_QTY'];	$result['COST_MAY'] = $cost_may;	$total_cost += $cost_may;
		$cost_jun = $PLAN_JUN * $params['PRICE_QTY'];	$result['COST_JUN'] = $cost_jun;	$total_cost += $cost_jun;
		$cost_jul = $PLAN_JUL * $params['PRICE_QTY'];	$result['COST_JUL'] = $cost_jul;	$total_cost += $cost_jul;
		$cost_aug = $PLAN_AUG * $params['PRICE_QTY'];	$result['COST_AUG'] = $cost_aug;	$total_cost += $cost_aug;
		$cost_sep = $PLAN_SEP * $params['PRICE_QTY'];	$result['COST_SEP'] = $cost_sep;	$total_cost += $cost_sep;
		$cost_oct = $PLAN_OCT * $params['PRICE_QTY'];	$result['COST_OCT'] = $cost_oct;	$total_cost += $cost_oct;
		$cost_nov = $PLAN_NOV * $params['PRICE_QTY'];	$result['COST_NOV'] = $cost_nov;	$total_cost += $cost_nov;
		$cost_dec = $PLAN_DEC * $params['PRICE_QTY'];	$result['COST_DEC'] = $cost_dec;	$total_cost += $cost_dec;
		
		$result['TOTAL_RP_SETAHUN'] = $total_cost;
        return $result;
    }	
	
	//cost total RKT Manual - Infra
	public function cal_RktManual_CostElementTanam($costElement, $params = array())
    {
		$total_rotasi = $params['TOTAL_ROTASI'];
		$planTotalYear=0;
		if($costElement == 'TOOLS' || $costElement == ' TRANSPORT'){
			$sql="
				SELECT SUM(NVL(PLAN_SETAHUN,0)) FROM TR_RKT 
				WHERE TIPE_TRANSAKSI = 'TANAM_MANUAL' 
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND DELETE_USER IS NULL
			"; 
			$planTotalYear = $this->_db->fetchOne($sql);
			
		}
		if($costElement == 'LABOUR'){
			//LABOUR, NORMA_BIAYA
			if($params['TIPE_NORMA'] == 'UMUM'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}
		}
		elseif($costElement == 'MATERIAL'){
			//MATERIAL
			if($params['TIPE_NORMA'] == 'UMUM'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TOOLS'){
			//TOOLS
			if($params['TIPE_NORMA'] == 'UMUM'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TRANSPORT'){
			//TRANSPORT
			/* CHECKING 130903 FIX NILAI TOTAL_PRICE_HM_KM DARI TR_RKT_VRA_DISTRIBUSI_SUM.*/
			$sql="
				SELECT SUM(NVL(PLAN_SETAHUN,0)) FROM TR_RKT 
				WHERE TIPE_TRANSAKSI = 'TANAM_MANUAL' 
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR') 
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND DELETE_USER IS NULL
			"; 
			$planTotalYear = $this->_db->fetchOne($sql);
			
			if($planTotalYear>0) {
				$sql = "
					SELECT NVL(SUM(TOTAL_PRICE_HM_KM), 0)
					FROM TR_RKT_VRA_DISTRIBUSI_SUM
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'";
				$cost_trperhm = ($planTotalYear>0) ? ($this->_db->fetchOne($sql)/$planTotalYear) : 0;
				$cost_transport = ($planTotalYear>0) ? (($this->_db->fetchOne($sql)/$planTotalYear)*$total_rotasi) : 0;
				$result[1] = $cost_transport;
				$result[2] = $cost_trperhm;
			}else{
				$result[1] = 0;
				$result[2] = 0;
			}
		}
		elseif($costElement == 'CONTRACT'){
			//CONTRACT
			if($params['TIPE_NORMA'] == 'UMUM'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
			}
		}
        return $result;
    }
	
	//cost total RKT Manual - Infra
	public function cal_costElement_RktTanam($costElement, $params = array())
    {
		if($costElement == 'LABOUR') {
			//LABOUR, NORMA_BIAYA
			if($params['TIPE_NORMA'] == 'UMUM'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//LABOUR SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms1 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[1] = $cost_labour_sms1;
				
				//LABOUR SMS 2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'LABOUR'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$cost_labour_sms2 = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? $res['PRICE_ROTASI'] : 0;
				$result[2] = $cost_labour_sms2;
			}
		}
		elseif($costElement == 'MATERIAL') {
			//MATERIAL
			if($params['TIPE_NORMA'] == 'UMUM'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//MATERIAL SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//MATERIAL SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'MATERIAL'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TOOLS') {
			//TOOLS
			if($params['TIPE_NORMA'] == 'UMUM'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//TOOLS SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = $res['PRICE_ROTASI'];
				
				//TOOLS SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'TOOLS'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = $res['PRICE_ROTASI'];
			}
		}
		elseif($costElement == 'TRANSPORT') {
			//TRANSPORT
			$sql = "
				SELECT SUM(HA_PLANTED)
				  FROM TM_HECTARE_STATEMENT
				 WHERE PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
				 AND BA_CODE = '".addslashes($params['BA_CODE'])."'
				 AND STATUS = 'PROYEKSI'
			";
			$total_rotasi = $this->_db->fetchOne($sql);
			
			/* CHECKING 130903 FIX NILAI TOTAL_PRICE_HM_KM DARI TR_RKT_VRA_DISTRIBUSI_SUM.*/
			$sql = "
				SELECT NVL(SUM(TOTAL_PRICE_HM_KM), 0)
				FROM TR_RKT_VRA_DISTRIBUSI_SUM
				WHERE DELETE_USER IS NULL
					AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
					AND BA_CODE = '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
			";
			$transport = $this->_db->fetchOne($sql);

			$cost_trperhm = ($total_rotasi > 0) ? ($transport / $total_rotasi) : 0;
			$cost_transport = ($total_rotasi > 0) ? (($transport / $total_rotasi) * $params['HA_PLANTED']) : 0;
			//echo $cost_trperhm;die();
			$result[1] = $cost_trperhm;
			$result[2] = $cost_transport;
		}
		elseif($costElement == 'CONTRACT') {
			if($params['TIPE_NORMA'] == 'UMUM'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
			}elseif($params['TIPE_NORMA'] == 'KHUSUS'){
				//CONTRACT SMS 1
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[1] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
				
				//CONTRACT SMS2
				$sql = "
					SELECT ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, NVL(SUM(PRICE_ROTASI_SITE), 0) PRICE_ROTASI
					FROM TN_BIAYA
					WHERE DELETE_USER IS NULL
						AND PERIOD_BUDGET = TO_DATE('01-01-{$params['PERIOD_BUDGET']}','DD-MM-RRRR')
						AND BA_CODE = '".addslashes($params['BA_CODE'])."'
						AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
						AND ACTIVITY_CLASS IN ('ALL', '".addslashes($params['ACTIVITY_CLASS'])."')
						AND LAND_TYPE IN ('ALL', '".addslashes($params['LAND_TYPE'])."')
						AND TOPOGRAPHY IN ('ALL', '".addslashes($params['TOPOGRAPHY'])."')
						AND COST_ELEMENT = 'CONTRACT'
					GROUP BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
					ORDER BY ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY
				";
				$res = $this->_db->fetchRow($sql);
				$result[2] = ($params['SUMBER_BIAYA'] == 'INTERNAL') ? 0 : $res['PRICE_ROTASI'];
			}
		}
        return $result;
    }
	
	public function cal_Rkt_Total($params = array())
    {
		$total_cost = 0;
		$PLAN_JAN = (float)str_replace(",", "", $params['PLAN_JAN']);
		$PLAN_FEB = (float)str_replace(",", "", $params['PLAN_FEB']);
		$PLAN_MAR = (float)str_replace(",", "", $params['PLAN_MAR']);
		$PLAN_APR = (float)str_replace(",", "", $params['PLAN_APR']);
		$PLAN_MAY = (float)str_replace(",", "", $params['PLAN_MAY']);
		$PLAN_JUN = (float)str_replace(",", "", $params['PLAN_JUN']);
		$PLAN_JUL = (float)str_replace(",", "", $params['PLAN_JUL']);
		$PLAN_AUG = (float)str_replace(",", "", $params['PLAN_AUG']);
		$PLAN_SEP = (float)str_replace(",", "", $params['PLAN_SEP']);
		$PLAN_OCT = (float)str_replace(",", "", $params['PLAN_OCT']);
		$PLAN_NOV = (float)str_replace(",", "", $params['PLAN_NOV']);
		$PLAN_DEC = (float)str_replace(",", "", $params['PLAN_DEC']);
		
		$cost_jan = $PLAN_JAN * $params['COST_TOTAL_RP_QTY'];	$result['COST_JAN'] = $cost_jan;	$total_cost += $cost_jan;
		$cost_feb = $PLAN_FEB * $params['COST_TOTAL_RP_QTY'];	$result['COST_FEB'] = $cost_feb;	$total_cost += $cost_feb;
		$cost_mar = $PLAN_MAR * $params['COST_TOTAL_RP_QTY'];	$result['COST_MAR'] = $cost_mar;	$total_cost += $cost_mar;
		$cost_apr = $PLAN_APR * $params['COST_TOTAL_RP_QTY'];	$result['COST_APR'] = $cost_apr;	$total_cost += $cost_apr;
		$cost_may = $PLAN_MAY * $params['COST_TOTAL_RP_QTY'];	$result['COST_MAY'] = $cost_may;	$total_cost += $cost_may;
		$cost_jun = $PLAN_JUN * $params['COST_TOTAL_RP_QTY'];	$result['COST_JUN'] = $cost_jun;	$total_cost += $cost_jun;
		$cost_jul = $PLAN_JUL * $params['COST_TOTAL_RP_QTY'];	$result['COST_JUL'] = $cost_jul;	$total_cost += $cost_jul;
		$cost_aug = $PLAN_AUG * $params['COST_TOTAL_RP_QTY'];	$result['COST_AUG'] = $cost_aug;	$total_cost += $cost_aug;
		$cost_sep = $PLAN_SEP * $params['COST_TOTAL_RP_QTY'];	$result['COST_SEP'] = $cost_sep;	$total_cost += $cost_sep;
		$cost_oct = $PLAN_OCT * $params['COST_TOTAL_RP_QTY'];	$result['COST_OCT'] = $cost_oct;	$total_cost += $cost_oct;
		$cost_nov = $PLAN_NOV * $params['COST_TOTAL_RP_QTY'];	$result['COST_NOV'] = $cost_nov;	$total_cost += $cost_nov;
		$cost_dec = $PLAN_DEC * $params['COST_TOTAL_RP_QTY'];	$result['COST_DEC'] = $cost_dec;	$total_cost += $cost_dec;
		$result['TOTAL_RP_SETAHUN'] = $total_cost;
		
        return $result;
    }

	public function getStatusPeriode($params = array())
	{
		$sql = "SELECT STATUS
			FROM TM_PERIOD
			WHERE PERIOD_BUDGET = TO_DATE('01-01-".addslashes($params['budgetperiod'])."','DD-MM-RRRR')
		";
		
		$result = $this->_db->fetchOne($sql);
		return $result;
	}
	
	public function cekSumberBiayaExternal($params = array())
	{
		$sql = "SELECT COUNT(ACTIVITY_CODE)
				FROM TN_HARGA_BORONG
				WHERE DELETE_USER IS NULL
				   AND to_char(PERIOD_BUDGET,'RRRR') = '".addslashes($params['PERIOD_BUDGET'])."'
				   AND BA_CODE LIKE '".addslashes($params['BA_CODE'])."'
				   AND ACTIVITY_CODE LIKE '".addslashes($params['ACTIVITY_CODE'])."'
		";
		$res = $this->_db->fetchOne($sql);
		$result = ($res) ? 'EXTERNAL' : 'INTERNAL';
		return $result;
	}

	public function cekJenisPekerjaan_RKT_PK($params = array())
	{
		$sql = "SELECT PARAMETER_VALUE_CODE, PARAMETER_VALUE
				FROM T_PARAMETER_VALUE
				WHERE PARAMETER_CODE LIKE '%JENIS_PERKERASAN_JALAN%'
		";
		
		$res = $this->_db->fetchOne($sql);
		$result = ($res) ? 'BARU' : 'BARU';
		return $result;
	}
	
	//khusus untuk RKT Manual Infra
	public function cekSumberBiayaExternalManualInfra($params = array())
	{
		$sql = "
			SELECT SUM(BIAYA) BIAYA, SUM(BORONG) BORONG
			FROM (
			  SELECT COUNT(*) BIAYA, 0 BORONG
				FROM TN_INFRASTRUKTUR
				WHERE to_char(PERIOD_BUDGET,'RRRR')= '".addslashes($params['PERIOD_BUDGET'])."' AND
					BA_CODE LIKE '".addslashes($params['BA_CODE'])."'
					AND ACTIVITY_CODE LIKE '".addslashes($params['ACTIVITY_CODE'])."'
					AND COST_ELEMENT IN ('LABOUR','TRANSPORT') 
				UNION
				SELECT 0 BIAYA, COUNT(*) BORONG
				FROM TN_HARGA_BORONG
				WHERE BA_CODE ='".addslashes($params['BA_CODE'])."'
					AND  to_char(PERIOD_BUDGET,'RRRR')= '".addslashes($params['PERIOD_BUDGET'])."'
					AND ACTIVITY_CODE = '".addslashes($params['ACTIVITY_CODE'])."'
					AND ACTIVITY_CLASS = 'ALL'
			)
		";
		$res = $this->_db->fetchRow($sql);
		if ($res['BIAYA'])  $result ='INTERNAL';
		else if ($res['BORONG'])  $result ='EXTERNAL';
		return $result;
	}
	
	public function gen_TransactionCode($period, $baCode, $rktCode)
	{
		//20142121CR20130626AB123
		//FORMAT : = BA_CODE + RKT_CODE + DATE INSERT + 6 DIGIT RANDOM CODE
		
		$rand = substr(md5(microtime()),rand(0,26),6);
		$trxCode=$baCode.$rktCode.date('ymdis').$rand;
		
		return $trxCode;
	}
	
	//doni added 2013-11-22 , gara2 error di dist vra
	public function gen_TransactionCodeVra($period, $baCode, $activityCode,$vraCode)
	{
		//20142121DT01041000
		$trxCode=$period. $baCode. $vraCode. $activityCode;
		
		return $trxCode;
	}
	
	public function get_RktPupuk_JumlahPokok($record = array()){
		$return = array();
		
		//cari data pokok
		$sql = "
			SELECT MAX(DIS_JAN) JAN, MAX(DIS_FEB) FEB, MAX(DIS_MAR) MAR, MAX(DIS_APR) APR, MAX(DIS_MAY) MAY, MAX(DIS_JUN) JUN, 
				   MAX(DIS_JUL) JUL, MAX(DIS_AUG) AUG, MAX(DIS_SEP) SEP, MAX(DIS_OCT) OCT, MAX(DIS_NOV) NOV, MAX(DIS_DEC) DEC, MAX(DIS_TOTAL) SETAHUN
			FROM TR_RKT_PUPUK_DISTRIBUSI
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$record['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".$record['BA_CODE']."'
				AND AFD_CODE = '".$record['AFD_CODE']."'
				AND BLOCK_CODE = '".$record['BLOCK_CODE']."'
				AND TIPE_TRANSAKSI = '".$record['TIPE_POKOK']."'
		";
		//die($sql);
		$return = $this->_db->fetchRow($sql);
		
		return $return;
	}
	
	public function get_RktPupuk_TotalKg($record = array()){
		$return = array();
		
		//cari data pokok
		$sql = "
			SELECT NVL(SUM(DIS_TOTAL), 0) TOTAL_KG
			FROM TR_RKT_PUPUK_DISTRIBUSI
			WHERE DELETE_USER IS NULL
				AND PERIOD_BUDGET = TO_DATE('01-01-{$record['PERIOD_BUDGET']}','DD-MM-RRRR')
				AND BA_CODE = '".$record['BA_CODE']."'
				AND TIPE_TRANSAKSI IN ('KG_NORMAL', 'KG_SISIP')
		";
		$return = $this->_db->fetchOne($sql);
		
		return $return;
	}
	
	//hitung sebaran rotasi untuk RKT Rawat & Rkt Rawat Opsi -- BELOM JADI!!!!!!
	public function cal_SebaranRotasiRawat($record = array()){
		
		$return = array();
		//deklarasi variable
		$rotasi_sms1 = (int)str_replace(",", "", $record['ROTASI_SMS1']);
		$rotasi_sms2 = (int)str_replace(",", "", $record['ROTASI_SMS2']);
		$awal_rotasi = (int)str_replace(",", "", $record['AWAL_ROTASI']);
		$ha = $record['HA_PLANTED'];
		
		$sql = "
			SELECT 	CASE WHEN JAN = '1' THEN '$ha' ELSE '0' END as PLAN_JAN,
					CASE WHEN FEB = '1' THEN '$ha' ELSE '0' END as PLAN_FEB,
					CASE WHEN MAR = '1' THEN '$ha' ELSE '0' END as PLAN_MAR,
					CASE WHEN APR = '1' THEN '$ha' ELSE '0' END as PLAN_APR,
					CASE WHEN MAY = '1' THEN '$ha' ELSE '0' END as PLAN_MAY,
					CASE WHEN JUN = '1' THEN '$ha' ELSE '0' END as PLAN_JUN,
					CASE WHEN JUL = '1' THEN '$ha' ELSE '0' END as PLAN_JUL,
					CASE WHEN AUG = '1' THEN '$ha' ELSE '0' END as PLAN_AUG,
					CASE WHEN SEP = '1' THEN '$ha' ELSE '0' END as PLAN_SEP,
					CASE WHEN OCT = '1' THEN '$ha' ELSE '0' END as PLAN_OCT,
					CASE WHEN NOV = '1' THEN '$ha' ELSE '0' END as PLAN_NOV,
					CASE WHEN DEC = '1' THEN '$ha' ELSE '0' END as PLAN_DEC
			FROM TM_ROTASI_OTOMATIS
			WHERE DELETE_USER IS NULL
				AND ROTASI_SMS1 = '".$rotasi_sms1."'
				AND ROTASI_SMS2 = '".$rotasi_sms2."'
				AND BULAN = '".$awal_rotasi."'
		";
		$return = $this->_db->fetchRow($sql);
		
		return $return;
	}
	
	public function getNormaKastrasiSanitasi($row = array()){
		$sql = "
			SELECT DISTINCT UMUR
			FROM TN_KASTRASI_SANITASI
			WHERE DELETE_USER IS NULL 
				AND ACTIVITY_CODE = '".$row['ACTIVITY_CODE']."'
				AND LAND_SUITABILITY = '".$row['LAND_SUITABILITY']."'
			ORDER BY UMUR
		";
		$rows = $this->_db->fetchAll($sql);	
		
		foreach ($rows as $idx => $row) {
			$result[] = $row['UMUR'];
		}
		
		return $result;
	}
}