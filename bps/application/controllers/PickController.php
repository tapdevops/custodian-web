<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.1.0
Deskripsi			: 	Controller Class untuk popup LoV
Function 			:	- 30/04	: activityAction							: menampilkan popup LoV master aktivitas
						- 29/05	: activityGroupAction						: menampilkan popup LoV master grup aktivitas
						- 29/05	: costElementAction							: menampilkan popup LoV master cost element
						- 29/05	: coaAction									: menampilkan popup LoV master COA
						- 30/05	: businessAreaAction						: menampilkan popup LoV master business area
						- 30/05	: parameterAction							: menampilkan popup LoV parameter
						- 31/05	: uomAction									: menampilkan popup LoV master UOM
						- 31/05	: assetStatusAction							: menampilkan popup LoV master asset status
						- 31/05	: normaBasicAction							: menampilkan popup LoV norma dasar
						- 31/05	: topographyAction							: menampilkan popup LoV master topografi
						- 31/05	: landTypeAction							: menampilkan popup LoV master land type
						- 31/05	: progenyAction								: menampilkan popup LoV master jenis bibit
						- 31/05	: landSuitabilityAction						: menampilkan popup LoV master LandSuitability
						- 31/05	: statusBlokBudgetAction					: menampilkan popup LoV master status blok budget
						- 03/06	: afdelingAction							: menampilkan popup LoV master afdeling
						- 03/06	: blockAction								: menampilkan popup LoV master blok
						- 04/06	: flagPanenNonPanenAction					: menampilkan popup LoV parameter flag panen / non panen
						- 05/06	: rvraAction								: menampilkan popup LoV master RVRA
						- 05/06	: vraAction									: menampilkan popup LoV master VRA
						- 05/06	: jobTypeAction								: menampilkan popup LoV master job type
						- 05/06	: wraGroupAction							: menampilkan popup LoV master WRA Group
						- 10/06	: activityClassAction						: menampilkan popup LoV master activity class
						- 11/06	: normaHargaAction							: menampilkan popup LoV norma harga + material
						- 19/06	: groupBumAction							: menampilkan popup LoV master group BUM
						- 19/06	: budgetPeriodAction						: menampilkan popup LoV master periode budget
						- 24/06	: coaCapexAction							: menampilkan popup LoV master COA Capex
						- 24/06	: maturityStageAction						: menampilkan popup LoV master maturity stage
						- 25/06	: groupCsrAction							: menampilkan popup LoV master group CSR
						- 25/06	: groupIrAction								: menampilkan popup LoV master group IR
						- 25/06	: groupSheAction							: menampilkan popup LoV master group SHE
						- 04/07	: activityNormaAlatKerjaNonPanenAction		: menampilkan popup LoV master activity untuk norma alat kerja non panen
						- 08/07	: assetAction								: menampilkan popup LoV norma harga + asset
						- 08/07	: urgencyCapexAction						: menampilkan popup LoV master urgency CAPEX
						- 10/07	: coaOpexAction								: menampilkan popup LoV master COA OPEX dari group BUM
						- 10/07	: groupBumCoaAction							: menampilkan popup LoV master group BUM - COA
						- 15/07	: coaRelationAction							: menampilkan popup LoV master COA dari mapping COA - relation
						- 15/07	: groupRelationAction						: menampilkan popup LoV master group relation dari mapping COA - relation
						- 15/07	: subGroupRelationAction					: menampilkan popup LoV master sub group relation dari mapping COA - relation
						- 17/07	: jenisPupukAction							: menampilkan popup LoV jenis pupuk
						- 22/07	: activityRktAction							: menampilkan popup LoV master activity yang digunakan pada RKT
						- 22/07	: sumberBiayaAction							: menampilkan popup LoV master sumber biaya
						- 24/07	: activityPKAction							: menampilkan popup LoV master activity yang digunakan pada RKT PK
						- 26/07	: activityOpsiAction						: menampilkan popup LoV master opsi aktivitas
						- 26/07	: afdelingLcAction							: menampilkan popup LoV master afdeling LC
						- 02/08	: jarakPerkerasanJalanAction				: menampilkan popup LoV jarak perkerasan jalan
						- 06/08	: tipePerkerasanJalanAction					: menampilkan popup LoV jarak perkerasan jalan
						- 06/08	: activityClassTanamAction					: menampilkan popup LoV activity class tanam 
						- 22/08 : activityMappAction						: menampilkan popup LoV activity Mapping 
						- 10/09 : rktAction									: menampilkan popup LoV master RKT 
						- _listAction							: menampilkan list
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2013
Update Terakhir		:	26/07/2013

Revisi	||	PIC				||	TANGGAL			||	FUNCTION		||	DESKRIPSI 		
1		||	Doni R			||	12-06-2013		||	initRegion		||	Penambahan fungsi initRegion untuk menampilkan LOV region 
=========================================================================================================================
*/
class PickController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_global = new Application_Model_Global();
        $this->_helper->layout->setLayout('pick');
    }

    public function indexAction()
    {
        // there is no index
        $this->_helper->layout->setLayout('error');
        $this->_request->setControllerName('error')
                       ->setActionName('no-index');
    }
	
	//menampilkan popup LoV master aktivitas
    public function activityAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master grup aktivitas
    public function activityGroupAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master cost element
    public function costElementAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master COA
    public function coaAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master business area
    public function businessAreaAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV parameter
    public function parameterAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master UOM
    public function uomAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master asset status
    public function assetStatusAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV norma dasar
    public function normaBasicAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master topografi
    public function topographyAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master land type
    public function landTypeAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master jenis bibit
    public function progenyAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master LandSuitability
    public function landSuitabilityAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master status blok budget
    public function statusBlokBudgetAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master afdeling
    public function afdelingAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master afdeling
    public function afdTopoLandAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master afdeling LC
    public function afdelingLcAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master blok
    public function blockAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV parameter flag panen / non panen
    public function flagPanenNonPanenAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master RVRA
    public function rvraAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master VRA
    public function vraAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master job type
    public function jobTypeAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master WRA Group
    public function wraGroupAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master activity class
    public function activityClassAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV norma harga + material
    public function normaHargaAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master Region
    public function regionAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group BUM
    public function groupBumAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master periode budget
    public function budgetPeriodAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master Coa Capex
    public function coaCapexAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master maturity stage
    public function maturityStageAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group CSR
    public function groupCsrAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group IR
    public function groupIrAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group SHE
    public function groupSheAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master aktivitas untuk norma alat kerja non-panen
    public function activityNormaAlatKerjaNonPanenAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV norma harga + asset
    public function assetAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master urgency CAPEX
    public function urgencyCapexAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master COA OPEX dari group BUM
    public function coaOpexAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group BUM - COA
    public function groupBumCoaAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master COA dari mapping COA - relation
    public function coaRelationAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master group relation dari mapping COA - relation
    public function groupRelationAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master sub group relation dari mapping COA - relation
    public function subGroupRelationAction()
    {
        $this->_listAction();
    }
	
	//jenisPupukAction
	public function jenisPupukAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master activity yang digunakan pada RKT
	public function activityRktAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master sumber biaya
	public function sumberBiayaAction()
    {
        $this->_listAction();
    }

	//menampilkan popup LoV master activity yang digunakan pada RKT PK
	public function activityPkAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master aktivitas opsi
	public function activityOpsiAction()
    {
        $this->_listAction();
    }

	//menampilkan popup LoV jarak perkerasan jalan
	public function jarakPerkerasanJalanAction()
    {
        $this->_listAction();
    }
 
	//menampilkan popup LoV tipe perkerasan jalan
	public function perulanganBaruAction()
    {
        $this->_listAction();
    }
	
	//menampilkan popup LoV master activity yang digunakan pada RKT Tanam
	public function activityTanamAction()
    {
        $this->_listAction();
    }

	public function activityClassTanamAction()
    {		
        $this->_listAction();
    }
	
	public function activityMappAction()
    {		
        $this->_listAction();
    }
	
	public function rktAction()
    {		
        $this->_listAction();
    }

    public function costCenterAction() {
        $this->_listAction();
    }
	
    public function hoCoaAction() {
        $this->_listAction();
    }

    public function hoCoreAction() {
        $this->_listAction();
    }

    public function hoCompanyAction() {
        $this->_listAction();
    }

    public function hoNormaSpdAction() {
        $this->_listAction();
    }

    public function hoStandarSpdAction() {
        $this->_listAction();
    }

    public function hoRencanaKerjaAction() {
        $this->_listAction();
    }

    public function hoDivisionAction() {
        $this->_listAction();
    }

    //menampilkan list
	private function _listAction()
    {
        $table = new Application_Model_DbPick();
        $params = $this->_request->getParams(); 
        if (!$this->_request->isXmlHttpRequest()) {
            $this->view->columns = $table->initList($params);
        } else {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_response->appendBody(Zend_Json::encode($table->getList($params)));
        }
    }
}
