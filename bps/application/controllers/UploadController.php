<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.1.0
Deskripsi			: 	Controller Class untuk Upload
Function 			:	- 11/04	: mainAction					: menampilkan form upload file
						//////////////////////////////////// MASTER ////////////////////////////////////
						- 29/05	: estateOrganizationAction		: upload master organization
						- 29/05	: activityAction				: upload master activity
						- 29/05	: coaAction						: upload master COA
						- 29/05	: mappingActivityCoaAction		: upload mapping aktivitas - COA
						- 30/05	: catuAction					: upload master catu
						- 30/05 : assetAction					: upload master asset
						- 31/05 : haStatementAction				: upload master HA statement
						- 03/06 : haStatementDetailAction		: upload master HA statement detail
						- 03/06 : vraAction						: upload master VRA
						- 03/06 : rvraAction					: upload master RVRA
						- 04/06 : materialAction				: upload master material
						- 05/06 : mappingJobTypeVraAction		: upload mapping job type - VRA
						- 05/06 : mappingJobTypeWraAction		: upload mapping job type - WRA
						- 05/06 : sebaranProduksiAction			: upload master sebaran produksi
						- 07/06 : tunjanganAction				: upload master tunjangan
						- 07/06 : tarifTunjanganAction			: upload master tarif tunjangan
						- 19/06 : mappingGroupBumCoaAction		: upload mapping group BUM - COA
						//////////////////////////////////// NORMA ////////////////////////////////////
						- 30/05	: normaBasicAction				: upload norma dasar
						- 10/06	: normaHargaBorongAction		: upload norma harga borong
						- 11/06	: normaBiayaAction				: upload norma biaya
						- 11/06	: normaAlatKerjaPanenAction		: upload norma alat kerja panen
						- 13/06	: normaCheckrollAction			: upload norma checkroll
						- 17/06	: normaPanenOerBjrAction		: upload norma panen OER BJR
						- 21/06	: normaVraAction				: upload norma VRA
						- 24/07	: normaVraPinjamAction			: upload norma VRA pinjam
						- 21/06	: normaInfrastrukturAction		: upload norma infrastruktur
						- 21/06	: normaPupukTbmLessAction		: upload norma pupuk < TBM 2
						- 24/06	: normaPupukTbmTmAction			: upload norma pupuk > TBM 2
						- 25/06	: normaPanenPremiMandorAction	: upload norma panen premi mandor
						- 25/06	: normaPanenVariabelAction		: upload norma panen variabel
						- 25/06	: normaPanenLoadingAction		: upload norma panen loading
						- 26/06	: normaPerkerasanJalanAction	: upload norma perkerasan jalan
						- 27/06	: normaPanenPremiLangsirAction	: upload norma panen premi langsir
						- 09/06 : normaSphAction				: upload norma SPH (aries)
						- 07/09 : reportNormaPerkerasanJalanAction : upload harga perkerasan jalan (ardo)
						//////////////////////////////////// BUDGETING TAHAP 1 ////////////////////////////////////
						- 04/07	: perencanaanProduksiAction		: upload perencanaan produksi
						- 11/07	: normaKastrasiSanitasiAction	: upload norma kastrasi sanitasi
						- 05/08	: normaPanenPremiTopographyAction	: upload norma panen premi topography YIR
						- 12/08 : normaPanenProduktifitasPemanenAction : upload norma panen produktifitas pemanen YIR
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2013
Update Terakhir		:	09/07/2016
Revisi				:	
2017/07/31 - yaddi.surahan@tap-agri.co.id
normaInsentivePanenAction
=========================================================================================================================
*/
class UploadController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
		  $this->_global = new Application_Model_Global();
		  $this->_model  = new Application_Model_Upload();
      $this->_helper->layout->setLayout('detail');
    }

    //menampilkan form upload file
    public function mainAction()
    {
        $this->view->title = 'Upload File';
    }
	
	//////////////////////////////////////////////////// MASTER ////////////////////////////////////////////////////
	
	//upload master period budget
	public function setupMasterBudgetPeriodAction()
    {
        $params = $this->_request->getParams();
		$result = $this->_model->uploadPeriodBudget($params);

        $this->resultAction($result);
    }
	
	//upload master estate organization
	public function estateOrganizationAction()
    {
        $params = $this->_request->getParams();
		$result = $this->_model->uploadEstateOrganization($params);

        $this->resultAction($result);
    }
	
	//upload master activity
	public function activityAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadActivity($params);

        $this->resultAction($result);
    }
	
	//upload master coa
	public function coaAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadCoa($params);

        $this->resultAction($result);
    }
	
	//upload mapping aktivitas - COA
	public function mappingActivityCoaAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMappingActivityCoa($params);

        $this->resultAction($result);
    }
	
	//upload master catu
	public function catuAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadCatu($params);

        $this->resultAction($result);
    }
	
	//upload master asset
	public function assetAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadAsset($params);

        $this->resultAction($result);
    }
	
	//upload master HA statement
	public function haStatementAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHaStatement($params);

        $this->resultAction($result);
    }
	
	//upload master HA statement Detail
	public function haStatementDetailAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHaStatementDetail($params);

        $this->resultAction($result);
    }
	
	//upload master VRA
	public function vraAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadVra($params);

        $this->resultAction($result);
    }
	
	//upload master RVRA
	public function rvraAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadRvra($params);

        $this->resultAction($result);
    }
	
	//upload master material
	public function materialAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMaterial($params);

        $this->resultAction($result);
    }
	
	//upload mapping job type - VRA
	public function mappingJobTypeVraAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMappingJobTypeVra($params);

        $this->resultAction($result);
    }
	
	//upload mapping job type - WRA
	public function mappingJobTypeWraAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMappingJobTypeWra($params);

        $this->resultAction($result);
    }
	
	//upload master sebaran produksi
	public function sebaranProduksiAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadSebaranProduksi($params);

        $this->resultAction($result);
    }
	
	//upload master tunjangan
	public function tunjanganAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadTunjangan($params);

        $this->resultAction($result);
    }
	
	//upload master tarif tunjangan
	public function tarifTunjanganAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadTarifTunjangan($params);

        $this->resultAction($result);
    }
	
	//upload mapping group BUM - COA
	public function mappingGroupBumCoaAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMappingGroupBumCoa($params);

        $this->resultAction($result);
    }
	
	
	//////////////////////////////////////////////////// NORMA ////////////////////////////////////////////////////
	//upload norma dasar
	public function normaBasicAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaBasic($params);

        $this->resultAction($result);
    }
	
	//upload norma harga borong
	public function normaHargaBorongAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaHargaBorong($params);

        $this->resultAction($result);
    }
	
	//upload norma biaya
	public function normaBiayaAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaBiaya($params);

        $this->resultAction($result);
    }
	
	//upload norma alat kerja panen
	public function normaAlatKerjaPanenAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaAlatKerjaPanen($params);

        $this->resultAction($result);
    }
	
	//upload norma alat kerja panen
	public function normaCheckrollAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaCheckroll($params);

        $this->resultAction($result);
    }
	
	//upload norma panen OER BJR
	public function normaPanenOerBjrAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenOerBjr($params);

        $this->resultAction($result);
    }
	
	//upload norma VRA
	public function normaVraAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaVra($params);

        $this->resultAction($result);
    }
	
	//upload norma VRA pinjam
	public function normaVraPinjamAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaVraPinjam($params);

        $this->resultAction($result);
    }
	
	//upload norma Infrastruktur
	public function normaInfrastrukturAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaInfrastruktur($params);

        $this->resultAction($result);
    }	
	
	//upload norma pupuk < TBM 2
	public function normaPupukTbmLessAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPupukTbmLess($params);

        $this->resultAction($result);
    }	

    /**
     * yaddi.surahman@tap-agri.co.id
     * 2017-08-11
     * Upload data pupuk untuk sub blok TBM yang mendapat rekomendasi pemupukan menggunakan rule blok induk yang TM
     */
    public function normaPupukTbmRekomendasiAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPupukTbmRekomendasi($params);

        $this->resultAction($result);
    }

	//upload norma pupuk > TBM 2
	public function normaPupukTbmTmAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPupukTbmTm($params);

        $this->resultAction($result);
    }

	//upload norma panen premi mandor
	public function normaPanenPremiMandorAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenPremiMandor($params);

        $this->resultAction($result);
    }	
	
	//upload norma panen variabel
	public function normaPanenVariabelAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenVariabel($params);

        $this->resultAction($result);
    }
	
	//upload norma panen loading
	public function normaPanenLoadingAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenLoading($params);

        $this->resultAction($result);
    }
	
	//upload norma Kastrasi Sanitasi
	public function normaKastrasiSanitasiAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadKastrasiSanitasi($params);

        $this->resultAction($result);
    }
	
	//upload norma pengerasan jalan
	public function normaPerkerasanJalanAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPerkerasanJalan($params);

        $this->resultAction($result);
    }	
	
	//upload norma panen premi langsir
	public function normaPanenPremiLangsirAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenPremiLangsir($params);

        $this->resultAction($result);
    }
	
	//upload norma panen premi topography
	public function normaPanenPremiTopographyAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenPremiTopography($params);

        $this->resultAction($result);
    }
  
  //normaPanenProduktifitasPemanenAction
  public function normaPanenProduktifitasPemanenAction()
    {
    $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaPanenProduktifitasPemanen($params);

        $this->resultAction($result);
    }

  /**
   * 2017-07-31 - yaddi.surahman@tap-agri.co.id
   */
  public function normaInsentivePanenAction() {
    $params = $this->_request->getParams();
    $result = $this->_model->uploadNormaInsentivePanen($params);
    $this->resultAction($result);
  }

  /**
   * 2017-08-08 - yaddi.surahman@tap-agri.co.id
   */
  public function normaPanenSupervisiAction() {
    $params = $this->_request->getParams();
    $result = $this->_model->uploadNormaPanenSupervisi($params);

    $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
    foreach ($result as $msg) {
      $message .= '<div>'.$msg.'</div>';
    }
    $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
    $message .= '</div>';

    echo $message;
    die();
  }
	
	//upload mapping Norma Pupuk -> aries 
	/*public function mappingNormaPupukAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadMappingNormaPupuk($params);

        $this->resultAction($result);
    }*/
	
	//upload norma SPH -> aries 26-05-2015
	public function normaSphAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadNormaSph($params);

        $this->resultAction($result);
    }
	
	//Added by Ardo 06092016 : upload harga perkerasan jalan
	public function reportNormaPerkerasanJalanAction(){
		$params = $this->_request->getParams();
        $result = $this->_model->uploadHargaPerkerasanJalan($params);

        $this->resultAction($result);
	}
	
	//////////////////////////////////////////////////// BUDGETING TAHAP 1 ////////////////////////////////////////////////////
	
	//upload perencanaan produksi
	public function perencanaanProduksiAction()
    {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadPerencanaanProduksiPeriodeBerjalan($params);
        $result = $this->_model->uploadPerencanaanProduksiSebaranProduksi($params);
        $this->resultAction($result);
    }
	
	public function resultAction($result = array())
	{
		if ($result['status'] == 'done')
			$msg = "Data berhasil disimpan.";
		else
			$msg = "Data gagal disimpan.";
		
		if($result['task_err']){
			$return = "
				<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>
					$msg
					<br>Anda tidak dapat melakukan upload data karena data yang anda upload pada BA <b>".$result['ba_notfound']."</b> belum tersedia di <b>".$result['task_err']."</b>.
					<br>Atau data di <b>".$result['task_err']."</b> belum terkunci, atau masih open.
					<br>Harap melengkapi data tersebut terlebih dahulu sebelum melakukan proses upload kembali.";
		}else if($result['task_err_vra']){
			$return = "
				<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>
					$msg
					<br>Anda tidak dapat melakukan upload data karena data yang anda upload untuk VRA = <b>".$result['vra_notfound']."</b> belum tersedia di <b>Master VRA</b>.
					<br>Harap melengkapi data tersebut terlebih dahulu sebelum melakukan proses upload kembali.";
		}else{
			$return = "
				<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>
					$msg
					<br>Diproses ".$result['total']." baris. Ditambahkan ".$result['inserted']." baris. Diubah ".$result['updated']." baris.";
		}
		if($result['data_failed']){
			$return .= "
				<i>
					<br><br>Data yang tidak diproses baris ke- :
					<br>".implode(", ", $result['data_failed']).".
				</i>
			";
		}
		if($result['line_err']){
			$return .= "
				<i>
					<br><br>Data baris ke- :
					<br>".implode(", ", $result['line_err'])." Jarak PKS dan Persen Langsir Data tidak boleh kosong 
					<br> atau Jarak PKS harus lebih besar dari 0 atau masih ada sebaran budget yang kosong.
				</i>
			";
		}if($result['empty_blck']){
			$return .= "
				<i>
					<br><br>Data baris ke- :
					<br>".implode(", ", $result['empty_blck'])." bloknya tidak ada di Hectare Statement.
				</i>
			";
		}if($result['jml_eq']){
			$return .= "
				<i>
					<br><br>Data baris ke- :
					<br>".implode(", ", $result['jml_eq'])." ton budgetnya tidak sama dengan total sebaran ton.
				</i>
			";
		}
		$return .= "
				<br><br><a href=\"JavaScript:window.close()\">[Tutup]</a>
			</div>		
		";		
		die($return);
	}

    //upload master HA statement
    public function rktVraAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadRktVra($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
            $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoCostCenterAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoCostCenter($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoDivisionAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoDivision($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoCompanyAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoCompany($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoCoreAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoCore($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoCoaAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoCoa($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoHaStatementAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoHaStatement($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoNormaSpdAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoNormaSpd($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoStandarSpdAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoStandarSpd($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }

    public function hoActOutlookAction() {
        $params = $this->_request->getParams();
        $result = $this->_model->uploadHoActOutlook($params);

        $message = "<div style = 'margin:10px; background:#CCC; font-family:verdana; font-size:12px; padding:10px; min-height:100px; text-align:center;'>";
        foreach ($result as $msg) {
          $message .= '<div>'.$msg.'</div>';
        }
        $message .= "<br><br><a href='#' onclick='window.close()'>[Tutup]</a>";
        $message .= '</div>';

        echo $message;
        die();
    }
}
