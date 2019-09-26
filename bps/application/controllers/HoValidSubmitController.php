<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	Controller Class untuk Master Ha Statement
Function 			:	- getStatusPeriodeAction		: BDJ 23/07/2013	: cek status periode budget yang dipilih
						- listAction					: SID 22/04/2013	: menampilkan list Ha Statement
						- saveTempAction				: SID 22/04/2013	: simpan data sementara sesuai input user
						- saveAction					: SID 22/04/2013	: save data
						- deleteAction					: SID 22/04/2013	: hapus data
						- mappingAction					: SID 22/04/2013	: mapping textfield name terhadap field name di DB
						- updateInheritModule			: SID 15/07/2014	: update inherit module
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	15/07/2014
Revisi				:	
YUL 07/08/2014	: 	- tambah validasi sequence pada function chkEnhLockedSequenceAction dan checkLockedSeqAction	
=========================================================================================================================
*/
class HoValidSubmitController extends Zend_Controller_Action
{
    private $_global = null;

    public function init()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
        $this->_model = new Application_Model_HoValidSubmit();
		//$this->view->input = $this->_model->getInput();
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }

    public function indexAction()
    {
        $this->_redirect('/ho-valid-submit/main');
    }

    public function mainAction()
    {
        $this->view->title = 'Report &raquo; Validation & Submit Budgeting HO';
		$this->view->period = date("Y", strtotime($this->_period));
		$this->view->username = $this->_model->_userName;
		$this->view->referencerole = $this->_model->_referenceRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->userrole = $this->_model->_userRole; // TAMBAHAN : Sabrina - 19/06/2013
		$this->view->divcode = $this->_model->_divCode;
		$this->view->divname = $this->_model->getDivName();
		$this->view->cccode = $this->_model->_ccCode;
		$this->view->ccname = $this->_model->getCcName();
    }
	
	//cek status periode budget yang dipilih
	public function getStatusPeriodeAction()
    {		
        $params = $this->_request->getParams();
		$value = $this->_formula->getStatusPeriode($params);
		die(json_encode($value));
    }
	
	//menampilkan list Ha Statement
    public function listAction()
    {		
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_request->getPost();
        $data = $this->_model->getList($params);
        die(json_encode($data));
    }

	//mapping textfield name terhadap field name di DB
	public function mappingAction(){
		$params = $this->_request->getParams();

        $rows = array();
        $i = 0;
        foreach ($params['output']['group_budget'] as $key => $val) {
        	$rows[$i]['PERIOD_BUDGET'] = $params['budgetperiod'];
        	$rows[$i]['DIV_CODE'] = $params['key_find_div'];
        	$rows[$i]['CC_CODE'] = $params['key_find_cc'];
        	$rows[$i]['GROUP_BUDGET'] = $params['output']['group_budget'][$i];
        	$rows[$i]['OUTLOOK'] = $params['output']['outlook'][$i];
        	$rows[$i]['NEXT_BUDGET'] = $params['output']['next_budget'][$i];
        	$rows[$i]['VAR_SELISIH'] = $params['output']['var_selisih'][$i];
        	$rows[$i]['VAR_PERSEN'] = $params['output']['var_persen'][$i];

        	$i++;
        }

		return $rows;
	}

	public function saveAction() {
		$rows = $this->mappingAction();
		$uniq_code_file = $this->_global->genFileName(); //generate file name - hanya ada 1 file name
        $status = 0;

		//$this->_global->insertLockTable('', 'HO SUBMIT VALIDATION');

		if (!empty($rows)) {
			// 1. Simpan TEMP RENCANA KERJA
			// generate filename untuk .sh dan .sql
			$filename = $uniq_code_file.'_00_HO_VALID_01_SAVETEMP';
			$this->_global->createBashFile($filename);
			$this->_global->createSqlFile($filename);
			$this->_global->createSqlFile($filename, "START : ".date("Y-m-d H:i:s")."\n"); //start create file

			foreach ($rows as $key => $row) {
				//print_r ($rows);
				$row['filename'] = $filename;
				$return = $this->_model->saveTemp($row);
			}

			//execute transaksi
			$this->_global->createSqlFile($filename, "COMMIT;\n"); //add query untuk commit
			//echo "sh ".getcwd()."/tmp_query/".$filename.".sh"; die;
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

		$data = array();
		
		if($status == 0){
			$data['return'] = "done";
		}else{
			$data['return'] = "donewithexception";
		}
		die(json_encode($data));
	}

	public function printAction() {
		require(__DIR__ . '/../../library/tcpdf/tcpdf.php');

		$data = array();

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		$params = $this->_request->getParams();
        $data = $this->_model->getDatas($params);

		$filename = 'VALIDATION_BUDGET_' . date('Y-m-d-H-i-s') . '_' . $params['budgetperiod'] . '_' . $params['key_find_div'] . '_' . $params['key_find_cc'] . '.pdf';

        $table = '
        	<div style="text-align:center; font-size:50px;">
        	VALIDASI BUDGET
        	</div>
        	<hr style="height:1px; margin-top:50px;">
        	<br /><br />
        	<table width="100%" border="1" cellpadding="5" cellspacing="3">
				<thead>
					<tr style="color: black; font-weight: bold;">
						<th style="text-align:center;">PERIODE BUDGET</th>
						<th style="text-align:center;">DIVISI</th>
						<th style="text-align:center;">COST CENTER</th>
					</tr>
				</thead>
				<tbody width="100%" name="data" id="data">
					<tr>
						<td style="text-align:center; font-size:11pt;">'.$params['budgetperiod'].'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['user']['DIVISION'].'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['user']['HCC_COST_CENTER'].'</td>
					</tr>
				</tbody>
			</table>
			<br /><br /><br /><br /><br /><br /><br /><br />
			<div style="font-size:30px;">Berikut kami sampaikan total rencana anggaran untuk tahun '.$params['budgetperiod'].' dengan summary sebagai berikut : </div>
			<br />
        	<table width="100%" border="1" cellpadding="5" cellspacing="1">
				<thead>
					<tr style="background: blue; color: black; font-weight: bold;">
						<td style="text-align:center;">GROUP BUDGET</td>
						<td valign="middle" style="text-align:center;">OUTLOOK</td>
						<td style="text-align:center;">2019</td>
						<td style="text-align:center;">VAR</td>
						<td style="text-align:center;">VAR %</td>
					</tr>
				</thead>
				<tbody width="100%" name="data" id="data">
					<tr>
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][0]['GROUP_BUDGET'].'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][0]['TOTAL_ACTUAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][0]['TOTAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][0]['VAR_SELISIH']).'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][0]['VAR_PERSEN'].'</td>
					</tr>
					<tr>
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][1]['GROUP_BUDGET'].'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][1]['TOTAL_ACTUAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][1]['TOTAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][1]['VAR_SELISIH']).'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][1]['VAR_PERSEN'].'</td>
					</tr>
					<tr style="color: black; font-weight: bold;">
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][2]['GROUP_BUDGET'].'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][2]['TOTAL_ACTUAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][2]['TOTAL']).'</td>
						<td style="text-align:center; font-size:11pt;">'.number_format($data['result']['rows'][2]['VAR_SELISIH']).'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['result']['rows'][2]['VAR_PERSEN'].'</td>
					</tr>
				</tbody>
			</table>
			<br />
			<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
			<div style="text-align:center; font-size:50px;">APPROVAL</div>
			<br />
        	<table width="100%" border="1" cellpadding="1" cellspacing="1">
				<thead>
					<tr style="color: black; font-weight: bold;">
						<th style="width:50%; text-align:center;">DIBUAT OLEH :</th>
						<th style="text-align:center;">DISETUJUI OLEH :</th>
					</tr>
				</thead>
				<tbody width="100%" name="data" id="data">
					<tr>
						<td style="text-align:center; font-size: 11pt;">'.$data['user']['HCC_COST_CENTER_HEAD'].'<br /><br /><br /><br /><br />' .$data['user']['HCC_COST_CENTER'].'</td>
						<td style="text-align:center; font-size:11pt;">'.$data['user']['HCC_DIVISION_HEAD'].'<br /><br /><br /><br /><br />'.$data['user']['DIVISION'].'</td>
					</tr>
				</tbody>
			</table>
        ';

		$pdf->AddPage();

		$pdf->writeHTML($table, true, false, true, false, '');
		$pdf->Output($filename, 'D');


		exit();
		//die();
		//die(json_encode($data));
	}

}
