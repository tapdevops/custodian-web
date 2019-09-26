<?php

/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.1.0
Deskripsi			: 	Model Class untuk Upload
Function 			:	//////////////////////////////////// MASTER ////////////////////////////////////
						- 29/05	: uploadPeriodBudget					: upload master period budget
						- 29/05	: uploadEstateOrganization				: upload master organization
						- 29/05	: uploadActivity						: upload master activity
						- 29/05 : uploadCoa								: upload master COA
						- 29/05 : uploadMappingActivityCoa				: upload mapping aktivitas - COA
						- 30/05 : uploadCatu							: upload master catu
						- 30/05 : uploadAsset							: upload master asset
						- 31/05 : uploadHaStatement						: upload master HA statement
						- 03/06 : uploadHaStatementDetail				: upload master HA statement detail
						- 03/06 : uploadVra								: upload master VRA
						- 03/06 : uploadRvra							: upload master RVRA59
						- 04/06 : uploadMaterial						: upload master material
						- 05/06 : uploadMappingJobTypeVra				: upload mapping job type - VRA
						- 05/06 : uploadMappingJobTypeWra				: upload mapping job type - WRA
						- 05/06 : uploadSebaranProduksi					: upload master sebaran produksi
						- 07/06 : uploadTunjangan						: upload master tunjangan
						- 07/06 : uploadTarifTunjangan					: upload master tarif tunjangan
						- 19/06 : uploadMappingGroupBumCoa				: upload mapping group BUM - COA
						//////////////////////////////////// NORMA ////////////////////////////////////
						- 30/05 : uploadNormaBasic						: upload norma dasar
						- 10/06 : uploadNormaHargaBorong				: upload norma harga borong				#ada lgsg hitung#
						- 11/06 : uploadNormaBiaya						: upload norma biaya					#ada lgsg hitung#
						- 11/06 : uploadNormaAlatKerjaPanen				: upload norma alat kerja panen			#ada lgsg hitung#
						- 13/06 : uploadNormaCheckroll					: upload norma checkroll
						- 17/06 : uploadNormaPanenOerBjr				: upload norma panen OER BJR			#ada lgsg hitung#
						- 21/06 : uploadNormaInfrastruktur				: upload norma infrastruktur			#ada lgsg hitung#
						- 21/06 : uploadNormaVra						: upload norma VRA						#ada lgsg hitung#
						- 24/07 : uploadNormaVraPinjam					: upload norma VRA pinjam				
						- 21/06 : uploadNormaPupukTbmLess				: upload norma pupuk < TBM 2			#ada lgsg hitung#
						- 24/06 : uploadNormaPupukTbmTm					: upload norma pupuk > TBM 2			#ada lgsg hitung#
						- 25/06 : uploadNormaPanenPremiMandor			: upload norma panen premi mandor
						- 25/06 : uploadNormaPanenVariabel				: upload norma panen variabel
						- 25/06 : uploadNormaPanenLoading				: upload norma panen loading			#ada lgsg hitung#
						- 26/06 : uploadNormaPerkerasanJalan			: upload norma perkerasan jalan			#ada lgsg hitung#
						- 27/06 : uploadNormaPanenPremiLangsir			: upload norma panen premi langsir		#ada lgsg hitung#
						//////////////////////////////////// BUDGETING TAHAP 1 ////////////////////////////////////
						- 04/07	: uploadPerencanaanProduksi				: upload perencanaan produksi
						- 11/07	: uploadKastrasiSanitasi				: upload norma kastrasi sanitasi
						- getInput										: setting input untuk region dan maturity stage
						- 05/08 : uploadNormaPanenPremiTopography 		: upload norma panen premi topography
						- 12/08 : uploadNormaPanenProduktifitasPemanen 	: upload norma panen produktifitas pemanen
						
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	29/05/2012
Update Terakhir		:	23/08/2012
Revisi				:	
Remarks				:	Upload hanya untuk periode aktif saja $this->_period, jadi tidak perlu diubah dgn pilihan period
=========================================================================================================================
*/
class Application_Model_Upload
{
    private $_db = null;
    private $_global = null;
    private $_siteCode = '';	
	private $_period = null;
  

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_global = new Application_Model_Global();
		$this->_formula = new Application_Model_Formula();
		$this->_generateData = new Application_Model_GenerateData();
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;				
    }
	
	//////////////////////////////////////////////////// MASTER ////////////////////////////////////////////////////
	
	//upload period budget
	public function uploadPeriodBudget($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_PERIOD
						WHERE PERIOD_BUDGET = TO_DATE('".addslashes($data[0])."', 'DD-MM-RRRR')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_PERIOD (PERIOD_BUDGET, START_BUDGETING, END_BUDGETING, STATUS, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('".addslashes($data[0])."', 'DD-MM-RRRR'),
									TO_DATE('".addslashes($data[1])."', 'DD-MM-RRRR'),
									TO_DATE('".addslashes($data[2])."', 'DD-MM-RRRR'),
									'".addslashes($data[3])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate turunan : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['PERIOD_BUDGET'] = addslashes($data[0]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER PERIOD BUDGET', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER PERIOD BUDGET', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_PERIOD
							WHERE PERIOD_BUDGET = TO_DATE('".addslashes($data[0])."', 'DD-MM-RRRR')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_PERIOD
									SET START_BUDGETING = TO_DATE('".addslashes($data[1])."', 'DD-MM-RRRR'),
										END_BUDGETING = TO_DATE('".addslashes($data[2])."', 'DD-MM-RRRR'),
										STATUS = '".addslashes($data[3])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE PERIOD_BUDGET = TO_DATE('".addslashes($data[0])."', 'DD-MM-RRRR')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER PERIOD BUDGET', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER PERIOD BUDGET', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate turunan : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genStandarJamKerja(); //trigger baru di BPS II
				$this->_generateData->genCheckrollHk(); //trigger baru di BPS II
				$this->_generateData->genNormaWraTriggerPeriodBudget($row); //menggantikan trigger yang ada di DB saat BPS I
				$this->_generateData->genSequence($row); //trigger baru di BPS II
			}
        }
		
		return $return;
    }
	
	//upload master organization
	public function uploadEstateOrganization($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_ORGANIZATION
						WHERE BA_CODE = '".addslashes($data[0])."'
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_ORGANIZATION (BA_CODE, COMPANY_CODE, COMPANY_NAME, ESTATE_NAME, REGION_CODE,
															 REGION_NAME, BA_TYPE, ACTIVE, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'Y',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate turunan : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['REGION_CODE'] = addslashes($data[4]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ESTATE ORGANIZATION', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ESTATE ORGANIZATION', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_ORGANIZATION
							WHERE BA_CODE = '".addslashes($data[0])."'
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_ORGANIZATION
									SET COMPANY_CODE = TRIM('".addslashes($data[1])."'),
										COMPANY_NAME = '".addslashes($data[2])."',
										ESTATE_NAME = '".addslashes($data[3])."',
										REGION_CODE = '".addslashes($data[4])."',
										REGION_NAME = '".addslashes($data[5])."',
										BA_TYPE = '".addslashes($data[6])."',
										ACTIVE = 'Y',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ESTATE ORGANIZATION', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ESTATE ORGANIZATION', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate turunan : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaWraTriggerOrganization($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload master activity
	public function uploadActivity($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_ACTIVITY
						WHERE ACTIVITY_CODE = '".addslashes($data[0])."'
					";
					$rec_count = $this->_db->fetchOne($sql);
					if ($rec_count == 0) {
						try {
							$sql = "
								INSERT INTO TM_ACTIVITY
								(ACTIVITY_CODE, DESCRIPTION, ACTIVITY_PARENT_CODE, UOM, FLAG, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ACTIVITY', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ACTIVITY', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_ACTIVITY
							WHERE ACTIVITY_CODE = '".addslashes($data[0])."'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_ACTIVITY
									SET DESCRIPTION = '".addslashes($data[1])."',
										ACTIVITY_PARENT_CODE = TRIM('".addslashes($data[2])."'),
										UOM = TRIM('".addslashes($data[3])."'),
										FLAG = TRIM('".addslashes($data[4])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE ACTIVITY_CODE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ACTIVITY', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ACTIVITY', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master COA
	public function uploadCoa($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_COA
						WHERE COA_CODE = '".addslashes($data[0])."'
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_COA
								(COA_CODE, DESCRIPTION, COA_PARENT, FLAG, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER COA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER COA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_COA
							WHERE COA_CODE = TRIM('".addslashes($data[0])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_COA
									SET DESCRIPTION = '".addslashes($data[1])."',
										COA_PARENT = TRIM('".addslashes($data[2])."'),
										FLAG = TRIM('".addslashes($data[3])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE COA_CODE = '".addslashes($data[0])."'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER COA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER COA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );			
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload mapping aktivitas - COA
	public function uploadMappingActivityCoa($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_ACTIVITY_COA
						WHERE ACTIVITY_GROUP = TRIM('".addslashes($data[0])."')
							AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
							AND COST_ELEMENT = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_ACTIVITY_COA
								(ACTIVITY_GROUP, ACTIVITY_CODE, COST_ELEMENT, COA_CODE, ACTIVITY_CODE_SAP, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING AKTIVITAS - COA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING AKTIVITAS - COA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_ACTIVITY_COA
							WHERE ACTIVITY_GROUP = TRIM('".addslashes($data[0])."')
								AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
								AND COST_ELEMENT = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_ACTIVITY_COA
									SET COA_CODE = TRIM('".addslashes($data[3])."'),
										ACTIVITY_CODE_SAP = TRIM('".addslashes($data[4])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE ACTIVITY_GROUP = TRIM('".addslashes($data[0])."')
										AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
										AND COST_ELEMENT = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING AKTIVITAS - COA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING AKTIVITAS - COA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );			
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master catu
	public function uploadCatu($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$total_rec = $ins_rec = $update_rec = $lastBaCode = 0;
		
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_CATU'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_CATU 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";					
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
						*/
						
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_CATU
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							AND EMPLOYEE_STATUS = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
							INSERT INTO TM_CATU (PERIOD_BUDGET, BA_CODE, EMPLOYEE_STATUS, RICE_PORTION, PRICE_KG, CATU_BERAS, HKE_BULAN, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									REPLACE('".addslashes($data[2])."', ',', ''),
									REPLACE('".addslashes($data[3])."', ',', ''),
									REPLACE('".addslashes($data[4])."', ',', ''),
									REPLACE('".addslashes($data[5])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)						
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER CATU', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER CATU', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_CATU
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND EMPLOYEE_STATUS = TRIM('".addslashes($data[1])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_CATU
									SET RICE_PORTION = REPLACE('".addslashes($data[2])."', ',', ''),
										PRICE_KG = REPLACE('".addslashes($data[3])."', ',', ''),
										CATU_BERAS = REPLACE('".addslashes($data[4])."', ',', ''),
										HKE_BULAN = REPLACE('".addslashes($data[5])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND EMPLOYEE_STATUS = TRIM('".addslashes($data[1])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER CATU', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER CATU', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SUMMARY **********************************************
		//print_r($newdata_ba);die();
		/*if (!empty($newdata_ba)) {
			foreach ($newdata_ba as $idx => $ba_code) {
				$this->_generateData->genCatuSum($ba_code); //trigger baru di BPS II
			}	
        }*/
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_Catu();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['PERIOD_BUDGET'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					$params['BA_CODE'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$this->_generateData->genCatuSum($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'MASTER CATU', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'MASTER CATU', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}
		}	
		// Sabrina / 2014-09-03
		
				
		// ********************************************** END OF HITUNG SUMMARY **********************************************
		
		return $return;
    }
	
	//upload master asset
	public function uploadAsset($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = $lastBaCode = 0;
		
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_ASSET'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_ASSET 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";				
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_ASSET
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							AND ASSET_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TM_ASSET (PERIOD_BUDGET, BA_CODE, ASSET_CODE, DESCRIPTION, COA_CODE, UOM, STATUS, PRICE, BASIC_NORMA_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									TRIM('".addslashes($data[7])."'),
									'{$this->_userName}',
									SYSDATE
								)						
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate norma harga barang : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['STATUS'] = 'ASSET';
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['MATERIAL_CODE'] = addslashes($data[1]);
							$gen_inherit[$ins_rec]['PRICE'] = addslashes($data[6]);
							$gen_inherit[$ins_rec]['BASIC_NORMA_CODE'] = addslashes($data[7]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ASSET', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ASSET', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_ASSET
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND ASSET_CODE = TRIM('".addslashes($data[1])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_ASSET
									SET DESCRIPTION = '".addslashes($data[2])."',
										COA_CODE = TRIM('".addslashes($data[3])."'),
										UOM = TRIM('".addslashes($data[4])."'),
										STATUS = '".addslashes($data[5])."',
										PRICE = '".addslashes($data[6])."',
										BASIC_NORMA_CODE = TRIM('".addslashes($data[7])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND ASSET_CODE = TRIM('".addslashes($data[1])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ASSET', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ASSET', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate norma harga barang : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaHargaBarang($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload master ha statement
	public function uploadHaStatement($params = array())
    {
		$data = array();
		$datachk = array();
		$total_recchk = $total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_HECTARE_STATEMENT'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				
				if (($data[0])&&($total_rec > 1)) {
					/* if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_HECTARE_STATEMENT 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					} */
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_HECTARE_STATEMENT
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[1])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if (($count == 0) && (($this->_siteCode == 'ALL') || (addslashes($data[10]) == 'PROYEKSI'))) {
						try {
							//kalkulasi maturity_stage
							$sql = "
								SELECT TO_DATE('".addslashes($data[8])."','DD-MM-RRRR') FROM DUAL
							";
							$date = $this->_db->fetchOne($sql);
							$date = date('d-m-Y', strtotime($date));
							$maturity_stage = $this->_formula->get_MaturityStage($date);
							$array = array(
										"POKOK_TANAM" 	=> $data[9],
										"HA_PLANTED" 	=> $data[3]
									 );
							$sph = $this->_formula->cal_Upload_Sph($array);
							
							//insert data
							$sql = "
								INSERT INTO TM_HECTARE_STATEMENT 
								(PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PLANTED, TOPOGRAPHY, LAND_TYPE, PROGENY, LAND_SUITABILITY, TAHUN_TANAM, POKOK_TANAM, SPH, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, STATUS, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									REPLACE('".addslashes($data[3])."', ',', ''),
									TRIM('".addslashes($data[4])."'),
									TRIM('".addslashes($data[5])."'),
									TRIM('".addslashes($data[6])."'),
									TRIM('".addslashes($data[7])."'),
									TO_DATE('".addslashes($data[8])."','DD-MM-RRRR'),
									REPLACE('".addslashes($data[9])."', ',', ''),
									REPLACE('".addslashes($sph)."', ',', ''),
									'".addslashes($maturity_stage[1])."',
									'".addslashes($maturity_stage[2])."',
									'".addslashes($data[10])."',
									'{$this->_userName}',
									SYSDATE
								)						
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate RKT turunan ketika upload HS : SABRINA 26/07/2014
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['AFD_CODE'] = addslashes($data[1]);
							$gen_inherit[$ins_rec]['BLOCK_CODE'] = addslashes($data[2]);
							$gen_inherit[$ins_rec]['STATUS'] = addslashes($data[10]);
							$gen_inherit[$ins_rec]['MATURITY_STAGE_SMS1'] = addslashes($maturity_stage[1]);
							$gen_inherit[$ins_rec]['MATURITY_STAGE_SMS2'] = addslashes($maturity_stage[2]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER HA STATEMENT', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER HA STATEMENT', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_HECTARE_STATEMENT
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND AFD_CODE = TRIM('".addslashes($data[1])."')
								AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if (($count == 0) && (($this->_siteCode == 'ALL') || (addslashes($data[10]) == 'PROYEKSI'))) {
							//kalkulasi maturity_stage
							$sql = "
								SELECT TO_DATE('".addslashes($data[8])."','DD-MM-RRRR') FROM DUAL
							";
							$date = $this->_db->fetchOne($sql);
							$date = date('d-m-Y', strtotime($date));
							$maturity_stage = $this->_formula->get_MaturityStage($date);
							$array = array(
										"POKOK_TANAM" 	=> $data[9],
										"HA_PLANTED" 	=> $data[3]
									 );
							$sph = $this->_formula->cal_Upload_Sph($array);
							
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_HECTARE_STATEMENT
									SET HA_PLANTED = REPLACE('".addslashes($data[3])."', ',', ''),
										TOPOGRAPHY = TRIM('".addslashes($data[4])."'), 
										LAND_TYPE = TRIM('".addslashes($data[5])."'), 
										PROGENY = TRIM('".addslashes($data[6])."'), 
										LAND_SUITABILITY = TRIM('".addslashes($data[7])."', 
										TAHUN_TANAM = TO_DATE('".addslashes($data[8])."','DD-MM-RRRR'),
										POKOK_TANAM = REPLACE('".addslashes($data[9])."', ',', ''),
										SPH = REPLACE('".addslashes($sph)."', ',', ''),
										MATURITY_STAGE_SMS1 = '".addslashes($maturity_stage[1])."',
										MATURITY_STAGE_SMS2 = '".addslashes($maturity_stage[2])."',
										STATUS = TRIM('".addslashes($data[10])."'), 
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND AFD_CODE = TRIM('".addslashes($data[1])."')
										AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								
								$sql = "
									UPDATE TM_HECTARE_STATEMENT_DETAIL
									SET UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND AFD_CODE = TRIM('".addslashes($data[1])."')
										AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER HA STATEMENT', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER HA STATEMENT', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//Generate data turunan ketika upload HS : SABRINA 26/07/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genMasterOerBa($row); //trigger baru di BPS II YIR-input Master OER BA 
				$this->_generateData->genHectareStatementDetail($row); //menggantikan trigger yang ada di DB saat BPS I
				
				//jika kode AFD mengandung ZZ_ maka TIDAK AKAN generate lokasi distribusi VRA non infra
				if (substr($row['AFD_CODE'], 0, 3) <> 'ZZ_'){
					$this->_generateData->genLocationDistribusiVra($row); //menggantikan trigger yang ada di DB saat BPS I
				}
				
				// jika kode blok mengandung ZZ_ maka hanya generate perkerasan jalan negara
				if (substr($row['BLOCK_CODE'], 0, 3) == 'ZZ_'){
					$row['UI_RKT_CODE'] = 'RKT004'; $this->_generateData->genRktPerkerasanJalanNegara($row); //trigger baru di BPS II
				}else{
					$row['UI_RKT_CODE'] = 'RKT001'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT002'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT003'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT005'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT006'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT023'; $this->_generateData->genRkt($row); //trigger baru di BPS II
					$this->_generateData->genRktPanen($row); //trigger baru di BPS II
					$row['UI_RKT_CODE'] = 'RKT004'; $this->_generateData->genRktPerkerasanJalan($row); //trigger baru di BPS II
					$this->_generateData->genRktPupukHa($row); //trigger baru di BPS II
					$this->_generateData->genNormaPanenCostUnit($row); //trigger baru di BPS II
				}
			}
        }
		
		return $return;
    }
	
	//upload master HA statement detail
	public function uploadHaStatementDetail($params = array())
    {
		$data = array();
		$datachk = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
		//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_HECTARE_STATEMENT_DETAIL'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {					
					try {
						$sql = "
							UPDATE TM_HECTARE_STATEMENT_DETAIL
							SET HA = '".addslashes($data[4])."',
								UPDATE_USER = '{$this->_userName}',
								UPDATE_TIME = SYSDATE,
								DELETE_USER = NULL,
								DELETE_TIME = NULL
							WHERE BA_CODE = '".addslashes($data[0])."'
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND AFD_CODE = TRIM('".addslashes($data[1])."')
								AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
								AND LAND_CATEGORY = TRIM('".addslashes($data[3])."')
						";
						$this->_db->query($sql);
						$this->_db->commit();
						$update_rec++;
						
						//log DB
						$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER HA STATEMENT DETAIL', '', '');
					} catch (Exception $e) {
						//menampilkan data yang tidak ditambahkan
						$data_error[] = $total_rec;
						
						//log DB
						$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER HA STATEMENT DETAIL', '', $e->getCode());
						
						//error log file
						$this->_global->errorLogFile($e->getMessage());
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master VRA
	public function uploadVra($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_VRA
						WHERE VRA_CODE = TRIM('".addslashes($data[0])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_VRA (VRA_CODE, VRA_CAT_CODE, VRA_CAT_DESCRIPTION, VRA_SUB_CAT_CODE, VRA_SUB_CAT_DESCRIPTION, UOM, TYPE, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									TRIM('".addslashes($data[3])."'),
									'".addslashes($data[4])."',
									TRIM('".addslashes($data[5])."'),
									'".addslashes($data[6])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER VRA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER VRA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_VRA
							WHERE VRA_CODE = TRIM('".addslashes($data[0])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_VRA
									SET VRA_CAT_CODE = TRIM('".addslashes($data[1])."'),
										VRA_CAT_DESCRIPTION = '".addslashes($data[2])."',
										VRA_SUB_CAT_CODE = TRIM('".addslashes($data[3])."'),
										VRA_SUB_CAT_DESCRIPTION = '".addslashes($data[4])."',
										UOM = TRIM('".addslashes($data[5])."'),
										TYPE = '".addslashes($data[6])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE VRA_CODE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER VRA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER VRA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master RVRA
	public function uploadRvra($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_RVRA
						WHERE SUB_RVRA_CODE = TRIM('".addslashes($data[0])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_RVRA (SUB_RVRA_CODE, SUB_RVRA_DESCRIPTION, COA_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									TRIM('".addslashes($data[2])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER RVRA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER RVRA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_RVRA
							WHERE SUB_RVRA_CODE = TRIM('".addslashes($data[0])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_RVRA
									SET SUB_RVRA_DESCRIPTION = '".addslashes($data[1])."',
										COA_CODE = TRIM('".addslashes($data[2])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE SUB_RVRA_CODE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER RVRA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER RVRA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master material
	public function uploadMaterial($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_MATERIAL'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_MATERIAL 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_MATERIAL
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TM_MATERIAL 
								(PERIOD_BUDGET, BA_CODE, MATERIAL_CODE, MATERIAL_NAME, UOM, VALUATION_CLASS, COA_CODE, PRICE, BASIC_NORMA_CODE, FLAG, DETAIL_CAT_CODE, DETAIL_CAT_DESC, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									TRIM('".addslashes($data[5])."'),
									REPLACE('".addslashes($data[6])."', ',', ''),
									TRIM('".addslashes($data[7])."'),
									TRIM('".addslashes($data[8])."'),
									TRIM('".addslashes($data[9])."'),
									'".addslashes($data[10])."',
									'{$this->_userName}',
									SYSDATE
								)						
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate norma harga barang : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['STATUS'] = 'MATERIAL';
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['MATERIAL_CODE'] = addslashes($data[1]);
							$gen_inherit[$ins_rec]['PRICE'] = addslashes($data[6]);
							$gen_inherit[$ins_rec]['BASIC_NORMA_CODE'] = addslashes($data[7]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER MATERIAL', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER MATERIAL', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_MATERIAL
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_MATERIAL
									SET MATERIAL_NAME = '".addslashes($data[2])."',
										UOM = TRIM('".addslashes($data[3])."'), 
										VALUATION_CLASS = TRIM('".addslashes($data[4])."'), 
										COA_CODE = TRIM('".addslashes($data[5])."'), 
										PRICE = REPLACE('".addslashes($data[6])."', ',', ''),
										BASIC_NORMA_CODE = TRIM('".addslashes($data[7])."'), 
										FLAG = TRIM('".addslashes($data[8])."'), 
										DETAIL_CAT_CODE = TRIM('".addslashes($data[9])."'), 
										DETAIL_CAT_DESC = '".addslashes($data[10])."', 
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER MATERIAL', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER MATERIAL', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate norma harga barang : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaHargaBarang($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload mapping job type - VRA
	public function uploadMappingJobTypeVra($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_MAPPING_JOB_TYPE_VRA
						WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
							AND RVRA_CODE = TRIM('".addslashes($data[1])."')
							AND VRA_CODE = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_MAPPING_JOB_TYPE_VRA (JOB_CODE, RVRA_CODE, VRA_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING JOB TYPE - VRA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING JOB TYPE - VRA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_MAPPING_JOB_TYPE_VRA
							WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
								AND RVRA_CODE = TRIM('".addslashes($data[1])."')
								AND VRA_CODE = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_MAPPING_JOB_TYPE_VRA
									SET UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
										AND RVRA_CODE = TRIM('".addslashes($data[1])."')
										AND VRA_CODE = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING JOB TYPE - VRA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING JOB TYPE - VRA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload mapping job type - WRA
	public function uploadMappingJobTypeWra($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_MAPPING_JOB_TYPE_WRA
						WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
							AND WRA_GROUP_CODE = TRIM('".addslashes($data[1])."')
							AND WRA_FLAG = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_MAPPING_JOB_TYPE_WRA (JOB_CODE, WRA_GROUP_CODE, WRA_FLAG, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate norma WRA : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['JOB_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['WRA_GROUP_CODE'] = addslashes($data[1]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING JOB TYPE - WRA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING JOB TYPE - WRA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_MAPPING_JOB_TYPE_WRA
							WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
								AND WRA_GROUP_CODE = TRIM('".addslashes($data[1])."')
								AND WRA_FLAG = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_MAPPING_JOB_TYPE_WRA
									SET UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE JOB_CODE = TRIM('".addslashes($data[0])."')
										AND WRA_GROUP_CODE = TRIM('".addslashes($data[1])."')
										AND WRA_FLAG = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING JOB TYPE - WRA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING JOB TYPE - WRA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate norma WRA : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaWraTriggerMappingWra($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload master sebaran produksi
	public function uploadSebaranProduksi($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 0)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_SEBARAN_PRODUKSI 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_SEBARAN_PRODUKSI
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TM_SEBARAN_PRODUKSI 
								(PERIOD_BUDGET, BA_CODE, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									REPLACE('".addslashes($data[1])."', ',', ''),
									REPLACE('".addslashes($data[2])."', ',', ''),
									REPLACE('".addslashes($data[3])."', ',', ''),
									REPLACE('".addslashes($data[4])."', ',', ''),
									REPLACE('".addslashes($data[5])."', ',', ''),
									REPLACE('".addslashes($data[6])."', ',', ''),
									REPLACE('".addslashes($data[7])."', ',', ''),
									REPLACE('".addslashes($data[8])."', ',', ''),
									REPLACE('".addslashes($data[9])."', ',', ''),
									REPLACE('".addslashes($data[10])."', ',', ''),
									REPLACE('".addslashes($data[11])."', ',', ''),
									REPLACE('".addslashes($data[12])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)						
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER SEBARAN PRODUKSI', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER SEBARAN PRODUKSI', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_SEBARAN_PRODUKSI
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_SEBARAN_PRODUKSI
									SET JAN = REPLACE('".addslashes($data[1])."', ',', ''),
										FEB = REPLACE('".addslashes($data[2])."', ',', ''),
										MAR = REPLACE('".addslashes($data[3])."', ',', ''),
										APR = REPLACE('".addslashes($data[4])."', ',', ''),
										MAY = REPLACE('".addslashes($data[5])."', ',', ''),
										JUN = REPLACE('".addslashes($data[6])."', ',', ''),
										JUL = REPLACE('".addslashes($data[7])."', ',', ''),
										AUG = REPLACE('".addslashes($data[8])."', ',', ''),
										SEP = REPLACE('".addslashes($data[9])."', ',', ''),
										OCT = REPLACE('".addslashes($data[10])."', ',', ''),
										NOV = REPLACE('".addslashes($data[11])."', ',', ''),
										DEC = REPLACE('".addslashes($data[12])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER SEBARAN PRODUKSI', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER SEBARAN PRODUKSI', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master tunjangan
	public function uploadTunjangan($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_TUNJANGAN
						WHERE TUNJANGAN_TYPE = TRIM('".addslashes($data[0])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							$sql = "
								INSERT INTO TM_TUNJANGAN (TUNJANGAN_TYPE, DESCRIPTION, UOM, FLAG_RP_HK, FLAG_EMPLOYEE_STATUS, RUMUS, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									TRIM('".addslashes($data[2])."'),
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".addslashes($data[5])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER TUNJANGAN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER TUNJANGAN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_TUNJANGAN
							WHERE TUNJANGAN_TYPE = '".addslashes($data[0])."'
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_TUNJANGAN
									SET DESCRIPTION = '".addslashes($data[1])."',
										UOM = TRIM('".addslashes($data[2])."'),
										FLAG_RP_HK = '".addslashes($data[3])."',
										FLAG_EMPLOYEE_STATUS = '".addslashes($data[4])."',
										--RUMUS = '".addslashes($data[5])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE TUNJANGAN_TYPE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER TUNJANGAN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER TUNJANGAN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload master tarif tunjangan
	public function uploadTarifTunjangan($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TM_TARIF_TUNJANGAN'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TM_TARIF_TUNJANGAN 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_TARIF_TUNJANGAN
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
							AND JOB_CODE = TRIM('".addslashes($data[1])."')
							AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
							AND TUNJANGAN_TYPE = TRIM('".addslashes($data[3])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TM_TARIF_TUNJANGAN 
								(PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, TUNJANGAN_TYPE, VALUE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									REPLACE('".addslashes($data[4])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER TARIF TUNJANGAN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER TARIF TUNJANGAN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_TARIF_TUNJANGAN
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
								AND JOB_CODE = TRIM('".addslashes($data[1])."')
								AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
								AND TUNJANGAN_TYPE = TRIM('".addslashes($data[3])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_TARIF_TUNJANGAN
									SET VALUE = REPLACE('".addslashes($data[4])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR') = '{$this->_period}'
										AND JOB_CODE = TRIM('".addslashes($data[1])."')
										AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
										AND TUNJANGAN_TYPE = TRIM('".addslashes($data[3])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER TARIF TUNJANGAN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER TARIF TUNJANGAN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload mapping grup BUM - COA
	public function uploadMappingGroupBumCoa($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TM_GROUP_BUM_COA
						WHERE GROUP_BUM_CODE = TRIM('".addslashes($data[0])."')
							AND COA_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TM_GROUP_BUM_COA (GROUP_BUM_CODE, COA_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING GROUP BUM - COA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING GROUP BUM - COA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TM_GROUP_BUM_COA
							WHERE GROUP_BUM_CODE = TRIM('".addslashes($data[0])."')
								AND COA_CODE = TRIM('".addslashes($data[1])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TM_GROUP_BUM_COA
									SET UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE GROUP_BUM_CODE = TRIM('".addslashes($data[0])."')
										AND COA_CODE = TRIM('".addslashes($data[1])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MAPPING GROUP BUM - COA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MAPPING GROUP BUM - COA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//////////////////////////////////////////////////// NORMA ////////////////////////////////////////////////////
	//upload norma dasar
	public function uploadNormaBasic($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_BASIC 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);
							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_BASIC
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND BASIC_NORMA_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_BASIC
								(PERIOD_BUDGET, BA_CODE, BASIC_NORMA_CODE, DESCRIPTION, PERCENT_INCREASE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									REPLACE('".addslashes($data[3])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA BASIC', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA BASIC', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_BASIC
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND BASIC_NORMA_CODE = TRIM('".addslashes($data[1])."') 
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_BASIC
									SET PERCENT_INCREASE = REPLACE('".addslashes($data[3])."', ',', ''),
										DESCRIPTION = '".addslashes($data[2])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND BASIC_NORMA_CODE = TRIM('".addslashes($data[1])."') 
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA BASIC', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA BASIC', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload norma harga borong
	public function uploadNormaHargaBorong($params = array())
    {
		$data = array();
		$datachk = array();
		$newdata_region = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_HARGA_BORONG'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_HARGA_BORONG 
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);							
						} catch (Exception $e) {
							
						}*/
						//get region code
						$region_code = $this->_formula->get_RegionCode(addslashes($data[0]));
						
						$newdata_region[] = $region_code;
					}				
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_HARGA_BORONG
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
							AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_HARGA_BORONG
								(PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, SPESIFICATION, PRICE, INSERT_USER, INSERT_TIME, PRICE_SITE)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									'".addslashes($data[3])."',
									REPLACE('".addslashes($data[4])."', ',', ''),
									'{$this->_userName}',
									SYSDATE,
									REPLACE('".addslashes($data[5])."', ',', '')
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA HARGA BORONG', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA HARGA BORONG', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_HARGA_BORONG
							WHERE BA_CODE = TRIM('".addslashes($data[0])."' )
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
								AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_HARGA_BORONG
									SET PRICE = REPLACE('".addslashes($data[4])."', ',', ''),
										PRICE_SITE = REPLACE('".addslashes($data[5])."', ',', ''),
										SPESIFICATION = '".addslashes($data[3])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
										AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();								
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA HARGA BORONG', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA HARGA BORONG', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-23
		
		if (!empty($newdata_region)) {
			try {
				$auto_calc = new Application_Model_NormaHargaBorong();
				
				foreach ($newdata_region as $idx => $region_code) {
					$auto_calc->calculateAllItem($region_code);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA HARGA BORONG', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA HARGA BORONG', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma biaya
	public function uploadNormaBiaya($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_BIAYA'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
		
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_BIAYA
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_BIAYA
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_GROUP = TRIM('".addslashes($data[1])."')
							AND ACTIVITY_CODE = TRIM('".addslashes($data[2])."')
							AND ACTIVITY_CLASS = TRIM('".addslashes($data[3])."')
							AND LAND_TYPE =TRIM( '".addslashes($data[4])."')
							AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
							AND COST_ELEMENT = TRIM('".addslashes($data[6])."')
							AND SUB_COST_ELEMENT = TRIM('".addslashes($data[7])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_BIAYA
								(PERIOD_BUDGET, BA_CODE, ACTIVITY_GROUP, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, TOPOGRAPHY, COST_ELEMENT, 
									SUB_COST_ELEMENT, QTY, ROTASI, VOLUME, INSERT_USER, INSERT_TIME, QTY_SITE, ROTASI_SITE, VOLUME_SITE)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									TRIM('".addslashes($data[5])."'),
									TRIM('".addslashes($data[6])."'),
									TRIM('".addslashes($data[7])."'),
									REPLACE('".addslashes($data[8])."', ',', ''),
									REPLACE('".addslashes($data[9])."', ',', ''),
									REPLACE('".addslashes($data[10])."', ',', ''),
									'{$this->_userName}',
									SYSDATE,
									REPLACE('".addslashes($data[11])."', ',', ''),
									REPLACE('".addslashes($data[12])."', ',', ''),
									REPLACE('".addslashes($data[13])."', ',', '')
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA BIAYA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA BIAYA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_BIAYA
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND ACTIVITY_GROUP = TRIM('".addslashes($data[1])."')
								AND ACTIVITY_CODE = TRIM('".addslashes($data[2])."')
								AND ACTIVITY_CLASS = TRIM('".addslashes($data[3])."')
								AND LAND_TYPE = TRIM('".addslashes($data[4])."')
								AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
								AND COST_ELEMENT = TRIM('".addslashes($data[6])."')
								AND SUB_COST_ELEMENT = TRIM('".addslashes($data[7])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_BIAYA
									SET QTY = REPLACE('".addslashes($data[8])."', ',', ''),
										ROTASI = REPLACE('".addslashes($data[9])."', ',', ''),
										VOLUME = REPLACE('".addslashes($data[10])."', ',', ''),
										QTY_SITE = REPLACE('".addslashes($data[11])."', ',', ''),
										ROTASI_SITE = REPLACE('".addslashes($data[12])."', ',', ''),
										VOLUME_SITE = REPLACE('".addslashes($data[13])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND ACTIVITY_GROUP = TRIM('".addslashes($data[1])."')
										AND ACTIVITY_CODE = TRIM('".addslashes($data[2])."')
										AND ACTIVITY_CLASS = TRIM('".addslashes($data[3])."')
										AND LAND_TYPE = TRIM('".addslashes($data[4])."')
										AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
										AND COST_ELEMENT = TRIM('".addslashes($data[6])."')
										AND SUB_COST_ELEMENT =TRIM('".addslashes($data[7])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA BIAYA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA BIAYA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaBiaya();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$auto_calc->calculateAllItem($ba_code);
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA BIAYA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA BIAYA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma alat kerja panen
	public function uploadNormaAlatKerjaPanen($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_ALAT_KERJA_PANEN'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_ALAT_KERJA_PANEN
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//get region code
					$region_code = $this->_formula->get_RegionCode($data[0]);
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_ALAT_KERJA_PANEN
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_ALAT_KERJA_PANEN
								(PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, ROTASI, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									REPLACE('".addslashes($data[2])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA ALAT KERJA PANEN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA ALAT KERJA PANEN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_ALAT_KERJA_PANEN
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_ALAT_KERJA_PANEN
									SET ROTASI = REPLACE('".addslashes($data[2])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND MATERIAL_CODE = TRIM('".addslashes($data[1])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA ALAT KERJA PANEN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA ALAT KERJA PANEN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		$auto_calc = new Application_Model_NormaAlatKerjaPanen();
				
		if (!empty($newdata_ba)) {
			try {
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA ALAT KERJA PANEN', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA ALAT KERJA PANEN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }
		
		//update summary
		$auto_calc->updateSummaryNormaAlatKerjaPanen();
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma checkroll
	public function uploadNormaCheckroll($params = array())
    {
		$data = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_CHECKROLL'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_CHECKROLL
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_CHECKROLL
						WHERE BA_CODE = ('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND JOB_CODE = ('".addslashes($data[1])."')
							AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_CHECKROLL
								(PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, GP, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									REPLACE('".addslashes($data[3])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate RKT CR turunan ketika upload Norma CR : SABRINA 26/07/2014
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['JOB_CODE'] = addslashes($data[1]);
							$gen_inherit[$ins_rec]['EMPLOYEE_STATUS'] = addslashes($data[2]);
							$gen_inherit[$ins_rec]['GP'] = addslashes($data[3]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA CHECKROLL', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA CHECKROLL', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_CHECKROLL
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND JOB_CODE = TRIM('".addslashes($data[1])."')
								AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_CHECKROLL
									SET GP = REPLACE('".addslashes($data[3])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND JOB_CODE = TRIM('".addslashes($data[1])."')
										AND EMPLOYEE_STATUS = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA CHECKROLL', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA CHECKROLL', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//Generate data turunan ketika upload Norma CR : SABRINA 26/07/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genRktCheckroll($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload norma panen OER BJR
	public function uploadNormaPanenOerBjr($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		$datachk = array();
		$lastBjrMin = $lastBjrMax = "";
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PANEN_OER_BJR'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PANEN_OER_BJR
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {							
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					if (($lastBjrMin <> addslashes($data[1])) && ($lastBjrMax <> addslashes($data[2]))){
						$premi_panen = addslashes($data[5]);
						$bjr_budget = addslashes($data[6]);
						$janjang_basis_mandor = addslashes($data[7]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_OER_BJR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND BJR_MIN = '".addslashes($data[1])."'
							AND BJR_MAX = '".addslashes($data[2])."'
							AND OER_MIN = '".addslashes($data[3])."'
							AND OER_MAX = '".addslashes($data[4])."'
					";
							
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
						
						//hitung data otomatis masuk ke tabel
						$query = "
							SELECT var.VALUE ASUMSI_OVER_BASIS
							 FROM TN_PANEN_VARIABLE var
							 WHERE var.PANEN_CODE = 'ASUM_OVR_BASIS'
							 AND to_char(var.PERIOD_BUDGET,'DD-MM-RRRR') = '".$this->_period."'
								AND UPPER(var.BA_CODE) IN ('".$data[0]."')";
						$execute = $this->_db->fetchOne($query);
						$execute_asumsi = ($execute) ? $execute : 0;	
						
						$ASUMSI_OVER_BASIS = (float)str_replace(",", "", $execute_asumsi);
						$JANJANG_BASIS_MANDOR = (float)str_replace(",", "", $janjang_basis_mandor);
						
						$over_basis_janjang = $JANJANG_BASIS_MANDOR * ( $ASUMSI_OVER_BASIS / 100 );
						$janjang_operation = $JANJANG_BASIS_MANDOR + $over_basis_janjang;
						
						$OVER_BASIS_JANJANG = (float)str_replace(",", "", $over_basis_janjang);
						$PREMI_PANEN = (float)str_replace(",", "", $premi_panen);
						$JANJANG_OPERATION = (float)str_replace(",", "", $janjang_operation);
						$BJR_BUDGET = (float)str_replace(",", "", $bjr_budget);
						
						$nilai = ( $OVER_BASIS_JANJANG * $PREMI_PANEN ) / ( $JANJANG_OPERATION * $BJR_BUDGET );
		
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_OER_BJR (PERIOD_BUDGET, 
								BA_CODE, 
								BJR_MIN, 
								BJR_MAX, 
								OER_MIN, 
								OER_MAX, 
								PREMI_PANEN, 
								BJR_BUDGET, 
								JANJANG_BASIS_MANDOR, 
								INSERT_USER, 
								INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".$premi_panen."',
									'".$bjr_budget."',
									'".$janjang_basis_mandor."',
									'{$this->_userName}',
									SYSDATE
								)
							";
						
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate panen supervisi : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['BJR_MIN'] = addslashes($data[1]);
							$gen_inherit[$ins_rec]['BJR_MAX'] = addslashes($data[2]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN OER BJR', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN OER BJR', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_OER_BJR
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND BJR_MIN = '".addslashes($data[1])."'
								AND BJR_MAX = '".addslashes($data[2])."'
								AND OER_MIN = '".addslashes($data[3])."'
								AND OER_MAX = '".addslashes($data[4])."'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
							
								$sql = "
									UPDATE TN_PANEN_OER_BJR
									SET PREMI_PANEN = '".$premi_panen."',
										BJR_BUDGET = '".$bjr_budget."',
										JANJANG_BASIS_MANDOR = '".$janjang_basis_mandor."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND BJR_MIN = '".addslashes($data[1])."'
										AND BJR_MAX = '".addslashes($data[2])."'
										AND OER_MIN = '".addslashes($data[3])."'
										AND OER_MAX = '".addslashes($data[4])."'
								";
								
						
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN OER BJR', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN OER BJR', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
					$lastBjrMin = addslashes($data[1]);
					$lastBjrMax = addslashes($data[2]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-28
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPanenOerBjr();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PANEN OER BJR', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PANEN OER BJR', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		//generate panen supervisi : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaPanenSupervisi($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }

	//upload norma infrastruktur
	public function uploadNormaInfrastruktur($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_INFRASTRUKTUR'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) 
				{	
					if ($lastBaCode <> addslashes($data[0]))
					{
						/*
						try 
						{
							//remove data
							$sqlDelete = "
								DELETE FROM TN_INFRASTRUKTUR
								WHERE BA_CODE = TRIM('".addslashes($data[0])."') 
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
						}	
						*/
						//get region code
						$region_code = $this->_formula->get_RegionCode($data[0]);					
						
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_INFRASTRUKTUR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
							AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
							AND LAND_TYPE = TRIM('".addslashes($data[3])."')
							AND COST_ELEMENT = TRIM('".addslashes($data[4])."')
							AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
							AND SUB_COST_ELEMENT = TRIM('".addslashes($data[6])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_INFRASTRUKTUR
								(PERIOD_BUDGET, REGION_CODE, BA_CODE, ACTIVITY_CODE, ACTIVITY_CLASS, LAND_TYPE, COST_ELEMENT, TOPOGRAPHY, SUB_COST_ELEMENT, QTY_INFRA, QTY_ALAT, ROTASI, VOLUME, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									TRIM('".addslashes($data[5])."'),
									TRIM('".addslashes($data[6])."'),
									TRIM('".addslashes($data[7])."'),
									'".addslashes($data[8])."',
									'".addslashes($data[9])."',
									'".addslashes($data[10])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA INFRASTRUKTUR', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA INFRASTRUKTUR', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
						SELECT COUNT(*) 
						FROM TN_INFRASTRUKTUR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."') 
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
							AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
							AND LAND_TYPE = TRIM('".addslashes($data[3])."')
							AND COST_ELEMENT = TRIM('".addslashes($data[4])."')
							AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
							AND SUB_COST_ELEMENT = TRIM('".addslashes($data[6])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_INFRASTRUKTUR
									SET QTY_INFRA = REPLACE('".addslashes($data[7])."', ',', ''),
										QTY_ALAT = REPLACE('".addslashes($data[8])."', ',', ''),
										ROTASI = REPLACE('".addslashes($data[9])."', ',', ''),
										VOLUME = REPLACE('".addslashes($data[10])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND ACTIVITY_CODE = TRIM('".addslashes($data[1])."')
										AND ACTIVITY_CLASS = TRIM('".addslashes($data[2])."')
										AND LAND_TYPE = TRIM('".addslashes($data[3])."')
										AND COST_ELEMENT = TRIM('".addslashes($data[4])."')
										AND TOPOGRAPHY = TRIM('".addslashes($data[5])."')
										AND SUB_COST_ELEMENT = TRIM('".addslashes($data[6])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA INFRASTRUKTUR', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA INFRASTRUKTUR', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-23
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaInfrastruktur();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA INFRASTRUKTUR', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA INFRASTRUKTUR', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		return $return;
    }
	
	//upload norma VRA
	public function uploadNormaVra($params = array())
    {
		$data = array();
		$datachk = array();
		$newdata_ba = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		$min_umur = $max_umur = $qty_hari = $hari_vra_tahun = $vraCode = "";
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_VRA'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0]>0)&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_VRA
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						//get region code
						
						$newdata_ba[] = addslashes($data[0]);
					}
					$region_code = $this->_formula->get_RegionCode($data[0]);					
										
					$min_umur = addslashes($data[2]);
					$max_umur = addslashes($data[3]);
					$qty_hari = addslashes($data[4]);
					$hari_vra_tahun = addslashes($data[5]);
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_VRA
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND VRA_CODE = TRIM('".addslashes($data[1])."')
							AND SUB_RVRA_CODE = TRIM('".addslashes($data[7])."')
							AND MIN_YEAR = '$min_umur'
							AND MAX_YEAR = '$max_umur'
					";
							
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_VRA (PERIOD_BUDGET, REGION_CODE, BA_CODE, VRA_CODE, MIN_YEAR, MAX_YEAR, QTY_DAY, DAY_YEAR_VRA, MATERIAL_CODE, SUB_RVRA_CODE, QTY_UOM, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($min_umur)."',
									'".addslashes($max_umur)."',
									'".addslashes($qty_hari)."',
									'".addslashes($hari_vra_tahun)."',
									'".addslashes($data[6])."',
									'".addslashes($data[7])."',
									'".addslashes($data[8])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA VRA', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA VRA', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_VRA
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND VRA_CODE = TRIM('".addslashes($data[1])."')
								AND SUB_RVRA_CODE = TRIM('".addslashes($data[7])."')
								AND MIN_YEAR = '$min_umur'
								AND MAX_YEAR = '$max_umur'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_VRA
									SET QTY_DAY = '".addslashes($qty_hari)."',
										DAY_YEAR_VRA = '".addslashes($hari_vra_tahun)."',
										MATERIAL_CODE = '".addslashes($data[6])."',
										SUB_RVRA_CODE = '".addslashes($data[7])."',
										QTY_UOM = '".addslashes($data[8])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND VRA_CODE = TRIM('".addslashes($data[1])."')
										AND SUB_RVRA_CODE = TRIM('".addslashes($data[7])."')
										AND MIN_YEAR = '$min_umur'
										AND MAX_YEAR = '$max_umur'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA VRA', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA VRA', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
					$vraCode = addslashes($data[1]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-23
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaVra();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA VRA', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA VRA', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma VRA Pinjam
	public function uploadNormaVraPinjam($params = array())
    {
		$data = array();
		$newdata_reg = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastRegCode = $update_rec = 0;
		$min_umur = $max_umur = $qty_hari = $hari_vra_tahun = $vraCode = "";
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_VRA_PINJAM'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//hanya yg huruf depannya ZZ_ baru bisa upload = menunjukkan VRA dummy
					if (substr($data[1], 0, 3) == 'ZZ_') {					
						if ($lastRegCode <> addslashes($data[0])){
							$newdata_reg[] = addslashes($data[0]);
						}
											
						//cek data
						$sql = "
							SELECT COUNT(*) 
							FROM TN_VRA_PINJAM
							WHERE REGION_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND VRA_CODE = TRIM('".addslashes($data[1])."')
						";
								
						$count = $this->_db->fetchOne($sql);
						if ($count == 0) {
							try {
								//insert data
								$sql = "
									INSERT INTO TN_VRA_PINJAM (PERIOD_BUDGET, REGION_CODE, VRA_CODE, RP_QTY, INSERT_USER, INSERT_TIME)
									VALUES (
										TO_DATE('{$this->_period}','DD-MM-RRRR'),
										TRIM('".addslashes($data[0])."'),
										TRIM('".addslashes($data[1])."'),
										'".addslashes($data[2])."',
										'{$this->_userName}',
										SYSDATE
									)
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$ins_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA VRA PINJAM', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA VRA PINJAM', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							// cek apakah data non-aktif
							$sql = "
								SELECT COUNT(*) 
								FROM TN_VRA_PINJAM
								WHERE REGION_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
									AND VRA_CODE = TRIM('".addslashes($data[1])."')
									AND DELETE_USER IS NULL
							";
							$count = $this->_db->fetchOne($sql);
							
							if ($count == 0) {
								// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
								try {
									$sql = "
										UPDATE TN_VRA_PINJAM
										SET RP_QTY = '".addslashes($data[2])."',
											UPDATE_USER = '{$this->_userName}',
											UPDATE_TIME = SYSDATE,
											FLAG_TEMP = NULL,
											DELETE_USER = NULL,
											DELETE_TIME = NULL
										WHERE REGION_CODE = TRIM('".addslashes($data[0])."')
											AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
											AND VRA_CODE = TRIM('".addslashes($data[1])."')
									";
									$this->_db->query($sql);
									$this->_db->commit();
									$update_rec++;
									
									//log DB
									$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA VRA PINJAM', '', '');
								} catch (Exception $e) {
									//menampilkan data yang tidak ditambahkan
									$data_error[] = $total_rec;
									
									//log DB
									$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA VRA PINJAM', '', $e->getCode());
									
									//error log file
									$this->_global->errorLogFile($e->getMessage());
								}
							}else{
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
							}
						}
						$lastRegCode = addslashes($data[0]);
						$vraCode = addslashes($data[1]);
					}else{
						//menampilkan data yang tidak ditambahkan
						$data_error[] = $total_rec;
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload norma pupuk < TBM 2
	public function uploadNormaPupukTbmLess($params = array())
    {
		$data = array(); $newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PUPUK_TMBM2_LESS'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PUPUK_TBM2_LESS
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//get region code
					$region_code = $this->_formula->get_RegionCode($data[0]);
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PUPUK_TBM2_LESS
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND LAND_TYPE = TRIM('".addslashes($data[1])."')
							AND PALM_AGE = '".addslashes($data[2])."'
							AND MATURITY_STAGE = TRIM('".addslashes($data[3])."')
							AND MATERIAL_CODE = TRIM('".addslashes($data[4])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							
							//insert data
							$sql = "
								INSERT INTO TN_PUPUK_TBM2_LESS (PERIOD_BUDGET, REGION_CODE, BA_CODE, 
								LAND_TYPE, PALM_AGE, MATURITY_STAGE, MATERIAL_CODE, ROTASI, DOSIS, 
								INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									TRIM('".addslashes($data[3])."'),
									TRIM('".addslashes($data[4])."'),
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PUPUK TBM 2 LESS', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PUPUK TBM 2 LESS', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PUPUK_TBM2_LESS
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND LAND_TYPE = TRIM('".addslashes($data[1])."')
								AND PALM_AGE = '".addslashes($data[2])."'
								AND MATURITY_STAGE = TRIM('".addslashes($data[3])."')
								AND MATERIAL_CODE = TRIM('".addslashes($data[4])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								
								$sql = "
									UPDATE TN_PUPUK_TBM2_LESS
									SET ROTASI = '".addslashes($data[5])."',
										DOSIS = '".addslashes($data[6])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND LAND_TYPE = TRIM('".addslashes($data[1])."')
										AND PALM_AGE = '".addslashes($data[2])."'
										AND MATURITY_STAGE = TRIM('".addslashes($data[3])."')
										AND MATERIAL_CODE = TRIM('".addslashes($data[4])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PUPUK TBM 2 LESS', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PUPUK TBM 2 LESS', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
					$vraCode = addslashes($data[1]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPupukTbmLess();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PUPUK TBM2 LESS', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PUPUK TBM2 LESS', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma pupuk > TBM 2
	public function uploadNormaPupukTbmTm($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PUPUK_TMBM2_TM'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PUPUK_TBM2_TM
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						//get region code
						$region_code = $this->_formula->get_RegionCode($data[0]);	
						
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PUPUK_TBM2_TM
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[1])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
							AND JENIS_TANAM = TRIM('".addslashes($data[3])."')
							AND BULAN_PEMUPUKAN = TRIM('".addslashes($data[5])."')
							AND MATERIAL_CODE = TRIM('".addslashes($data[6])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PUPUK_TBM2_TM (PERIOD_BUDGET, REGION_CODE, BA_CODE, AFD_CODE, BLOCK_CODE, 
								JENIS_TANAM, POKOK, BULAN_PEMUPUKAN, MATERIAL_CODE, DOSIS, HA_PUPUK, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$region_code."',
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									'".addslashes($data[4])."',
									TRIM('".addslashes($data[5])."'),
									TRIM('".addslashes($data[6])."'),
									'".addslashes($data[7])."',
									'".addslashes($data[8])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PUPUK TBM 2 - TM', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PUPUK TBM 2 - TM', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PUPUK_TBM2_TM
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND AFD_CODE = TRIM('".addslashes($data[1])."')
								AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
								AND JENIS_TANAM = TRIM('".addslashes($data[3])."')
								AND BULAN_PEMUPUKAN = TRIM('".addslashes($data[5])."')
								AND MATERIAL_CODE = TRIM('".addslashes($data[6])."')
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
							
								$sql = "
									UPDATE TN_PUPUK_TBM2_TM
									SET POKOK = '".addslashes($data[4])."',
										DOSIS = '".addslashes($data[7])."',
										HA_PUPUK = '".addslashes($data[8])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND AFD_CODE = TRIM('".addslashes($data[1])."')
										AND BLOCK_CODE = TRIM('".addslashes($data[2])."')
										AND JENIS_TANAM = TRIM('".addslashes($data[3])."')
										AND BULAN_PEMUPUKAN = TRIM('".addslashes($data[5])."')
										AND MATERIAL_CODE = TRIM('".addslashes($data[6])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PUPUK TBM 2 - TM', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PUPUK TBM 2 - TM', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
					$vraCode = addslashes($data[1]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPupukTbmTm();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PUPUK TBM 2 - TM', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PUPUK TBM 2 - TM', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma panen premi mandor
	public function uploadNormaPanenPremiMandor($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		$datachk = array();
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PANEN_PREMI_MANDOR'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PANEN_PREMI_MANDOR
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_PREMI_MANDOR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND PREMI_MANDOR_CODE = '".addslashes($data[1])."'
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_PREMI_MANDOR (PERIOD_BUDGET, BA_CODE, PREMI_MANDOR_CODE, DESCRIPTION, MIN_YIELD, MAX_YIELD, MIN_OER, MAX_OER, VALUE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'".addslashes($data[7])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI MANDOR', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI MANDOR', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_PREMI_MANDOR
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND PREMI_MANDOR_CODE = '".addslashes($data[1])."'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_PANEN_PREMI_MANDOR
									SET DESCRIPTION = '".addslashes($data[2])."',
										MIN_YIELD = '".addslashes($data[3])."',
										MAX_YIELD = '".addslashes($data[4])."',
										MIN_OER = '".addslashes($data[5])."',
										MAX_OER = '".addslashes($data[6])."',
										VALUE = '".addslashes($data[7])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND PREMI_MANDOR_CODE = '".addslashes($data[1])."'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI MANDOR', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI MANDOR', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload norma panen variabel
	public function uploadNormaPanenVariabel($params = array())
    {
		$data = array();
		$datachk = array();
		$total_rec = $ins_rec = $irec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PANEN_VARIABLE'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			$curBa = $oldBa = "";
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[0])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PANEN_VARIABLE
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_VARIABLE
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND PANEN_CODE = '".addslashes($data[1])."'";
					$curBa = addslashes($data[0]);		
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_VARIABLE (PERIOD_BUDGET, BA_CODE, PANEN_CODE, DESCRIPTION, VALUE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate panen supervisi : SABRINA 19/08/2014
							
							if($curBa<>$oldBa){
								$irec++;
								$gen_inherit[$irec]['BA_CODE'] = addslashes($data[3]);
							}
							if(addslashes($data[1])=="TGT_KRANI_BUAH"){
								$gen_inherit[$irec]['TARGET'] = addslashes($data[3]);
							}
							if(addslashes($data[1])=="BAS_KRANI_BUAH"){
								$gen_inherit[$irec]['BASIS'] = addslashes($data[3]);
							}
							if(addslashes($data[1])=="TRF_OB_KRANI_BUAH"){
								$gen_inherit[$irec]['TARIF_BASIS'] = addslashes($data[3]);
							}
							if(addslashes($data[1])=="ASUM_OVR_BASIS"){
								$gen_inherit[$irec]['SELISIH_OVER_BASIS'] = addslashes($data[3]);
							}
							/*TGT_KRANI_BUAH
							BAS_KRANI_BUAH
							TRF_OB_KRANI_BUAH
							ASUM_OVR_BASIS
							*/
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN VARIABEL', '', '');
							
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN VARIABEL', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_VARIABLE
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND PANEN_CODE = '".addslashes($data[1])."'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_PANEN_VARIABLE
									SET DESCRIPTION = '".addslashes($data[2])."',
										VALUE = '".addslashes($data[3])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND PANEN_CODE = '".addslashes($data[1])."'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN VARIABEL', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN VARIABEL', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		//generate panen supervisi : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaPanenKraniBuah($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload norma panen loading
	public function uploadNormaPanenLoading($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PANEN_LOADING'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PANEN_LOADING
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
							
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_LOADING
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND JARAK_PKS_MIN = '".addslashes($data[1])."'
							AND JARAK_PKS_MAX = '".addslashes($data[2])."'
					";
					$count = $this->_db->fetchOne($sql);
					
					if ($count == 0) {
						try {
							
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_LOADING (PERIOD_BUDGET, 
										BA_CODE, 
										JARAK_PKS_MIN, 
										JARAK_PKS_MAX, 
										TARGET_ANGKUT_TM_SUPIR, 
										BASIS_TM_SUPIR, 
										JUMLAH_TM, 
										TARIF_TM, 
										TARIF_SUPIR, 
										INSERT_USER, 
										INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'".addslashes($data[7])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN LOADING', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN LOADING', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_LOADING
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND JARAK_PKS_MIN = '".addslashes($data[1])."'
								AND JARAK_PKS_MAX = '".addslashes($data[2])."'
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
							
								$sql = "
									UPDATE TN_PANEN_LOADING
									SET TARGET_ANGKUT_TM_SUPIR = '".addslashes($data[3])."',
										BASIS_TM_SUPIR = '".addslashes($data[4])."',
										JUMLAH_TM = '".addslashes($data[5])."',
										TARIF_TM = '".addslashes($data[6])."',
										TARIF_SUPIR = '".addslashes($data[7])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND JARAK_PKS_MIN = '".addslashes($data[1])."'
										AND JARAK_PKS_MAX = '".addslashes($data[2])."'
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN LOADING', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN LOADING', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPanenLoading();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PANEN LOADING', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PANEN LOADING', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }

	//upload norma perkerasan jalan
	public function uploadNormaPerkerasanJalan($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PERKERASAN_JALAN'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PERKERASAN_JALAN
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*)
						FROM TN_PERKERASAN_JALAN
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".addslashes($data[1])."'
							AND LEBAR = '".addslashes($data[2])."'
							AND PANJANG = '".addslashes($data[3])."'
							AND TEBAL = '".addslashes($data[4])."'
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PERKERASAN_JALAN (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, LEBAR, PANJANG, TEBAL, VRA_CODE_DT, KAPASITAS_DT, KECEPATAN_DT, JAM_KERJA_DT, VRA_CODE_EXCAV, KAPASITAS_EXCAV, VRA_CODE_COMPACTOR, KAPASITAS_COMPACTOR, VRA_CODE_GRADER, KAPASITAS_GRADER, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'".addslashes($data[5])."',
									'".addslashes($data[6])."',
									'".addslashes($data[7])."',
									'".addslashes($data[8])."',
									'".addslashes($data[9])."',
									'".addslashes($data[10])."',
									'".addslashes($data[11])."',
									'".addslashes($data[12])."',
									'".addslashes($data[13])."',
									'".addslashes($data[14])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//TAMBAHAN untuk otomatis generate norma harga perkerasan jalan : SABRINA 19/08/2014
							$gen_inherit[$ins_rec]['BA_CODE'] = addslashes($data[0]);
							$gen_inherit[$ins_rec]['ACTIVITY_CODE'] = addslashes($data[1]);
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PERKERASAN JALAN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PERKERASAN JALAN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
						SELECT COUNT(*)
						FROM TN_PERKERASAN_JALAN
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND ACTIVITY_CODE = '".addslashes($data[1])."'
							AND LEBAR = '".addslashes($data[2])."'
							AND PANJANG = '".addslashes($data[3])."'
							AND TEBAL = '".addslashes($data[4])."'
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_PERKERASAN_JALAN
									SET VRA_CODE_DT = '".addslashes($data[5])."',
										KAPASITAS_DT = '".addslashes($data[6])."',
										KECEPATAN_DT = '".addslashes($data[7])."',
										JAM_KERJA_DT = '".addslashes($data[8])."',
										VRA_CODE_EXCAV = '".addslashes($data[9])."',
										KAPASITAS_EXCAV = '".addslashes($data[10])."',
										VRA_CODE_COMPACTOR = '".addslashes($data[11])."',
										KAPASITAS_COMPACTOR = '".addslashes($data[12])."',
										VRA_CODE_GRADER = '".addslashes($data[13])."',
										KAPASITAS_GRADER = '".addslashes($data[14])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND ACTIVITY_CODE = '".addslashes($data[1])."'
										AND LEBAR = '".addslashes($data[2])."'
										AND PANJANG = '".addslashes($data[3])."'
										AND TEBAL = '".addslashes($data[4])."'
							
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PERKERASAN JALAN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PERKERASAN JALAN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPerkerasanJalan();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				$records2 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
				foreach ($records2 as $idx2 => $record2) {
						$auto_calc->calculateData($record2);
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PERKERASAN JALAN', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PERKERASAN JALAN', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		//generate norma harga perkerasan jalan : SABRINA 19/08/2014
		if (!empty($gen_inherit)) {			
			foreach ($gen_inherit as $idx => $row) {
				$this->_generateData->genNormaHargaPerkerasanJalan($row); //menggantikan trigger yang ada di DB saat BPS I
			}
        }
		
		return $return;
    }
	
	//upload norma panen premi langsir
	public function uploadNormaPanenPremiLangsir($params = array())
    {
		$data = array();
		$newdata_ba = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_PANEN_PREMI_LANGSIR'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					
					if ($lastBaCode <> addslashes($data[0])){
						/*try {
							//remove data
							$sqlDelete = "
								DELETE FROM TN_PANEN_PREMI_LANGSIR
								WHERE BA_CODE = TRIM('".addslashes($data[0])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
						}*/
						$newdata_ba[] = addslashes($data[0]);
					}
					
					//cek data
					$sql = "
						SELECT COUNT(*)
						FROM TN_PANEN_PREMI_LANGSIR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND VRA_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_PREMI_LANGSIR (PERIOD_BUDGET, 
								BA_CODE,
								VRA_CODE, 
								TON_TRIP, 
								TRIP_HARI, 
								HM_TRIP, 
								INSERT_USER, 
								INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									'".addslashes($data[2])."',
									'".addslashes($data[3])."',
									'".addslashes($data[4])."',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI LANGSIR', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI LANGSIR', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
						SELECT COUNT(*)
						FROM TN_PANEN_PREMI_LANGSIR
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND VRA_CODE = TRIM('".addslashes($data[1])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
							
								$sql = "
									UPDATE TN_PANEN_PREMI_LANGSIR
									SET TON_TRIP = '".addslashes($data[2])."',
										TRIP_HARI = '".addslashes($data[3])."',
										HM_TRIP = '".addslashes($data[4])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND VRA_CODE = TRIM('".addslashes($data[1])."')
							
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI LANGSIR', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI LANGSIR', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		// ********************************************** HITUNG SEMUA HASIL UPLOAD **********************************************
		// Sabrina / 2013-08-22
		
		if (!empty($newdata_ba)) {
			try {
				$auto_calc = new Application_Model_NormaPanenPremiLangsir();
				
				foreach ($newdata_ba as $idx => $ba_code) {
					$params['budgetperiod'] = date("Y", strtotime($this->_period));
					$params['key_find'] = $ba_code;
					
					$records1 = $this->_db->fetchAll("{$auto_calc->getData($params)}");
					
					foreach ($records1 as $idx1 => $record1) {
						$auto_calc->calculateData($record1);
					}
				}
				//log DB
				$this->_global->insertLog('UPDATE SUCCESS', 'NORMA PANEN PREMI LANGSIR', '', '');
			} catch (Exception $e) {
				//log DB
				$this->_global->insertLog('UPDATE FAILED', 'NORMA PANEN PREMI LANGSIR', '', $e->getCode());
				
				//error log file
				$this->_global->errorLogFile($e->getMessage());
				
				//return value
				$result = false;
			}	
        }		
		// ********************************************** END OF HITUNG SEMUA HASIL UPLOAD **********************************************
		
		return $return;
    }
	
	//upload norma panen premi topography
	public function uploadNormaPanenPremiTopography($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_PREMI_TOPOGRAPHY
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[1])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_PREMI_TOPOGRAPHY
								(PERIOD_BUDGET, BA_CODE, AFD_CODE, PERCENTAGE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									'".addslashes($data[1])."',
									REPLACE('".addslashes($data[2])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI TOPOGRAPHY', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI TOPOGRAPHY', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_PREMI_TOPOGRAPHY
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND AFD_CODE = TRIM('".addslashes($data[1])."') 
								AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_PANEN_PREMI_TOPOGRAPHY
									SET PERCENTAGE = REPLACE('".addslashes($data[2])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND Afd_CODE = TRIM('".addslashes($data[1])."') 
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PREMI TOPOGRAPHY', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PREMI TOPOGRAPHY', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload norma panen premi topography
	public function uploadNormaPanenProduktifitasPemanen($params = array())
    {
		
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_PANEN_PROD_PEMANEN
						WHERE BA_CODE = TRIM('".addslashes($data[0])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND TOPOGRAPHY = TRIM('".addslashes($data[1])."')
							AND UMUR = TRIM('".addslashes($data[2])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TN_PANEN_PROD_PEMANEN
								(PERIOD_BUDGET, BA_CODE, UMUR, TOPOGRAPHY, VALUE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[0])."'),
									REPLACE('".addslashes($data[2])."', ',', ''),
									'".addslashes($data[1])."',
									REPLACE('".addslashes($data[3])."', ',', ''),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PRODUKTIFITAS PEMANEN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PRODUKTIFITAS PEMANEN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_PANEN_PROD_PEMANEN
							WHERE BA_CODE = TRIM('".addslashes($data[0])."')
								AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
								AND TOPOGRAPHY = TRIM('".addslashes($data[1])."') 
								AND UMUR = TRIM('".addslashes($data[2])."')
								AND DELETE_USER IS NULL";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_PANEN_PROD_PEMANEN
									SET VALUE = REPLACE('".addslashes($data[3])."', ',', ''),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[0])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND TOPOGRAPHY = TRIM('".addslashes($data[1])."') 
										AND UMUR = TRIM('".addslashes($data[2])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA PANEN PRODUKTIFITAS PEMAMEN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA PANEN PRODUKTIFITAS PEMAMEN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[0]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//////////////////////////////////////////////////// BUDGETING TAHAP 1 ////////////////////////////////////////////////////
	//upload perencanaan produksi - periode berjalan
	public function uploadPerencanaanProduksiPeriodeBerjalan($params = array())
    {
		$data = array();
		$datachk = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TR_PRODUKSI_PERIODE_BUDGET'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[1])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TR_PRODUKSI_TAHUN_BERJALAN
								WHERE BA_CODE = TRIM('".addslashes($data[1])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*)
						FROM TR_PRODUKSI_TAHUN_BERJALAN
						WHERE BA_CODE = TRIM('".addslashes($data[1])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[2])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
					";
						
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TR_PRODUKSI_TAHUN_BERJALAN (PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_PANEN, POKOK_PRODUKTIF, SPH_PRODUKTIF, TON_AKTUAL, JANJANG_AKTUAL, BJR_AKTUAL, YPH_AKTUAL, TON_TAKSASI, JANJANG_TAKSASI, BJR_TAKSASI, YPH_TAKSASI, TON_ANTISIPASI, JANJANG_ANTISIPASI, BJR_ANTISIPASI, YPH_ANTISIPASI, TON_BUDGET, YPH_BUDGET, VAR_YPH, TRX_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									'".addslashes($data[12])."',
									'".addslashes($data[13])."',
									'".addslashes($data[14])."',
									'".addslashes($data[15])."',
									'".addslashes($data[16])."',
									'".addslashes($data[17])."',
									'".addslashes($data[18])."',
									'".addslashes($data[19])."',
									'".addslashes($data[20])."',
									'".addslashes($data[21])."',
									'".addslashes($data[22])."',
									'".addslashes($data[23])."',
									'".addslashes($data[24])."',
									'".addslashes($data[25])."',
									'".addslashes($data[26])."',
									'".addslashes($data[27])."',
									'".addslashes($data[28])."',
									'".addslashes($data[29])."',
									F_GEN_TRANSACTION_CODE(TO_DATE('{$this->_period}','DD-MM-RRRR'),TRIM('".addslashes($data[1])."'),'PP'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'PERENCANAAN PRODUKSI - PERIODE BERJALAN', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'PERENCANAAN PRODUKSI - PERIODE BERJALAN', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
						SELECT COUNT(*)
						FROM TR_PRODUKSI_TAHUN_BERJALAN
						WHERE BA_CODE = TRIM('".addslashes($data[1])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[2])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TR_PRODUKSI_TAHUN_BERJALAN
									SET HA_PANEN = '".addslashes($data[12])."',
										POKOK_PRODUKTIF = '".addslashes($data[13])."',
										SPH_PRODUKTIF = '".addslashes($data[14])."',
										TON_AKTUAL = '".addslashes($data[15])."',
										JANJANG_AKTUAL = '".addslashes($data[16])."',
										BJR_AKTUAL = '".addslashes($data[17])."',
										YPH_AKTUAL = '".addslashes($data[18])."',
										TON_TAKSASI = '".addslashes($data[19])."',
										JANJANG_TAKSASI = '".addslashes($data[20])."',
										BJR_TAKSASI = '".addslashes($data[21])."',
										YPH_TAKSASI = '".addslashes($data[22])."',
										TON_ANTISIPASI = '".addslashes($data[23])."',
										JANJANG_ANTISIPASI = '".addslashes($data[24])."',
										BJR_ANTISIPASI = '".addslashes($data[25])."',
										YPH_ANTISIPASI = '".addslashes($data[26])."',
										TON_BUDGET = '".addslashes($data[27])."',
										YPH_BUDGET = '".addslashes($data[28])."',
										VAR_YPH = '".addslashes($data[29])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[1])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND AFD_CODE = TRIM('".addslashes($data[2])."')
										AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
							
								";
								
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'PERENCANAAN PRODUKSI - PERIODE BERJALAN', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'PERENCANAAN PRODUKSI - PERIODE BERJALAN', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[1]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload perencanaan produksi - sebaran produksi
	public function uploadPerencanaanProduksiSebaranProduksi($params = array())
    {
		$data = array();
		$total_rec = $ins_rec = $lastBaCode = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {

			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			//loop through the csv file and insert into database
			do {
				if (($data[0])&&($total_rec > 1)) {
					/*
					if ($lastBaCode <> addslashes($data[1])){
						try {
							//remove data
							$sqlDelete = "
								DELETE FROM TR_PRODUKSI_PERIODE_BUDGET
								WHERE BA_CODE = TRIM('".addslashes($data[1])."')
									AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							";
							//log file penghapusan data
							$this->_global->deleteDataLogFile($sqlDelete);							
							$this->_db->query($sqlDelete);
						} catch (Exception $e) {
						}
					}
					*/
					//cek data
					$sql = "
						SELECT COUNT(*)
						FROM TR_PRODUKSI_PERIODE_BUDGET
						WHERE BA_CODE = TRIM('".addslashes($data[1])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[2])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
					";
					$count = $this->_db->fetchOne($sql);
					if ($count == 0) {
						try {
							//insert data
							$sql = "
								INSERT INTO TR_PRODUKSI_PERIODE_BUDGET (PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, HA_SMS1, POKOK_SMS1, SPH_SMS1, HA_SMS2, POKOK_SMS2, SPH_SMS2, YPH_PROFILE, TON_PROFILE, YPH_PROPORTION, TON_PROPORTION, JANJANG_BUDGET, BJR_BUDGET, TON_BUDGET, YPH_BUDGET, JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV, DEC, SMS1, SMS2, TRX_CODE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									TRIM('".addslashes($data[3])."'),
									'".addslashes($data[30])."',
									'".addslashes($data[31])."',
									'".addslashes($data[32])."',
									'".addslashes($data[33])."',
									'".addslashes($data[34])."',
									'".addslashes($data[35])."',
									'".addslashes($data[36])."',
									'".addslashes($data[37])."',
									'".addslashes($data[38])."',
									'".addslashes($data[39])."',
									'".addslashes($data[40])."',
									'".addslashes($data[41])."',
									'".addslashes($data[42])."',
									'".addslashes($data[43])."',
									'".addslashes($data[44])."',
									'".addslashes($data[45])."',
									'".addslashes($data[46])."',
									'".addslashes($data[47])."',
									'".addslashes($data[48])."',
									'".addslashes($data[49])."',
									'".addslashes($data[50])."',
									'".addslashes($data[51])."',
									'".addslashes($data[52])."',
									'".addslashes($data[53])."',
									'".addslashes($data[54])."',
									'".addslashes($data[55])."',
									'".addslashes($data[56])."',
									'".addslashes($data[57])."',
									F_GEN_TRANSACTION_CODE(TO_DATE('{$this->_period}','DD-MM-RRRR'),TRIM('".addslashes($data[1])."'),'PP'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'PERENCANAAN PRODUKSI - SEBARAN PRODUKSI', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'PERENCANAAN PRODUKSI - SEBARAN PRODUKSI', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
						SELECT COUNT(*)
						FROM TR_PRODUKSI_PERIODE_BUDGET
						WHERE BA_CODE = TRIM('".addslashes($data[1])."')
							AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
							AND AFD_CODE = TRIM('".addslashes($data[2])."')
							AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TR_PRODUKSI_PERIODE_BUDGET
									SET HA_SMS1 = '".addslashes($data[30])."',
										POKOK_SMS1 = '".addslashes($data[31])."',
										SPH_SMS1 = '".addslashes($data[32])."',
										HA_SMS2 = '".addslashes($data[33])."',
										POKOK_SMS2 = '".addslashes($data[34])."',
										SPH_SMS2 = '".addslashes($data[35])."',
										YPH_PROFILE = '".addslashes($data[36])."',
										TON_PROFILE = '".addslashes($data[37])."',
										YPH_PROPORTION = '".addslashes($data[38])."',
										TON_PROPORTION = '".addslashes($data[39])."',
										JANJANG_BUDGET = '".addslashes($data[40])."',
										BJR_BUDGET = '".addslashes($data[41])."',
										TON_BUDGET = '".addslashes($data[42])."',
										YPH_BUDGET = '".addslashes($data[43])."',
										JAN = '".addslashes($data[44])."',
										FEB = '".addslashes($data[45])."',
										MAR = '".addslashes($data[46])."',
										APR = '".addslashes($data[47])."',
										MAY = '".addslashes($data[48])."',
										JUN = '".addslashes($data[49])."',
										JUL = '".addslashes($data[50])."',
										AUG = '".addslashes($data[51])."',
										SEP = '".addslashes($data[52])."',
										OCT = '".addslashes($data[53])."',
										NOV = '".addslashes($data[54])."',
										DEC = '".addslashes($data[55])."',
										SMS1 = '".addslashes($data[56])."',
										SMS2 = '".addslashes($data[57])."',
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE BA_CODE = TRIM('".addslashes($data[1])."')
										AND to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
										AND AFD_CODE = TRIM('".addslashes($data[2])."')
										AND BLOCK_CODE = TRIM('".addslashes($data[3])."')
							
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'PERENCANAAN PRODUKSI - SEBARAN PRODUKSI', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'PERENCANAAN PRODUKSI - SEBARAN PRODUKSI', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
					$lastBaCode = addslashes($data[1]);
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
	
	//upload norma kastrasi sanitasi
	public function uploadKastrasiSanitasi($params = array())
    {
		$data = array();
		$datachk = array();
		$total_rec = $ins_rec = $update_rec = 0;
		
		if ($_FILES[file][size] > 0) {
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************
			//1. ****************** Check BA dari CSV FILE
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			$arrba = array();
			do {
				if (($datachk[0])&&($total_rec > 1)) {
					array_push($arrba,$datachk[1]);
				}
				$total_rec++;
			} while($datachk = fgetcsv($handle,1000,";","'"));
			$arrbadis = array_values(array_unique($arrba, SORT_REGULAR)); //variabel array list BA pada CSV
			
			//2. ****************** Baca Urutan Upload dari T_SEQ
			$sqlCurTask="SELECT SEQ_NUM FROM T_SEQ WHERE TABLE_NAME='TN_KASTRASI_SANITASI'";
			$seqNum = $this->_db->fetchOne($sqlCurTask);
			$sqlSeq="SELECT * FROM T_SEQ WHERE SEQ_NUM < ($seqNum) ORDER BY SEQ_NUM, TASK_NAME";
			$rows = $this->_db->fetchAll($sqlSeq);
			if (!empty($rows)) {
				foreach ($rows as $idx => $row) { 
					$tablechk = $row['REMARKS'];
					//2.1 ****************** get BA CODE yang sudah dimasukan pada table di awal sequence
					$sqlba = "SELECT DISTINCT BA_CODE FROM ".$row['TABLE_NAME'];
					$arrnormaba = array();
					$arrlockba = array();
					$rowsba = $this->_db->fetchAll($sqlba);
					foreach ($rowsba as $idxba => $rowba) {
						array_push($arrnormaba,$rowba['BA_CODE']); //variabel array list BA pada masing2 norma
						//2.2 ****************** get STATUS LOCK masing2 BA CODE di T_SEQ
						$sqlba1 = "SELECT STATUS FROM T_SEQ_CHECK WHERE BA_CODE='".$rowba['BA_CODE']."' AND TASK_NAME='".$row['TABLE_NAME']."'";
						
						$arrlockba[$rowba['BA_CODE']] = $this->_db->fetchOne($sqlba1);
					}		
					$arrNotFound=array();
					for($x=0;$x<count($arrbadis);$x++){
						if(!in_array($arrbadis[$x],$arrnormaba)){ //check apakah data ba csv ada di data ba norma?
							$arrNotFound[]=$arrbadis[$x];
						}elseif($arrlockba[$arrbadis[$x]]=="LOCKED"){
							$arrNotFound[]=$arrbadis[$x];
						}
					}
					if($arrNotFound) break;
				}
			}
			$arrNotFound =array();
			if($arrNotFound){
				$return = array(
					'status'		=> 'failed',
					'total'			=> $total_rec - 2,
					'ba_notfound'	=> implode(",",$arrNotFound),
					'task_err'		=> $tablechk
				  );
				return $return;
			}
			//********************************* VALIDASI SEQUENCE UPLOAD DATA BUDGET ***************************************//
			
			//get the csv file
			$file = $_FILES[file][tmp_name];
			$handle = fopen($file,"r");
			
			$total_rec = 0;
			
			//loop through the csv file and insert into database
			do {
				if (($data[0]>0)&&($total_rec > 1)) {
					//cek data
					$sql = "
						SELECT COUNT(*) 
						FROM TN_KASTRASI_SANITASI
						WHERE ACTIVITY_CODE = '".addslashes($data[0])."' AND
						LAND_SUITABILITY = '".addslashes($data[1])."' AND
						UMUR = '".addslashes($data[2])."'
					"; 
					
					$rec_count = $this->_db->fetchOne($sql);
					if ($rec_count == 0) {
						try {
							$sql = "
								INSERT INTO TN_KASTRASI_SANITASI
								(ACTIVITY_CODE, LAND_SUITABILITY, UMUR, INSERT_USER, INSERT_TIME)
								VALUES (
									TRIM('".addslashes($data[0])."'),
									TRIM('".addslashes($data[1])."'),
									TRIM('".addslashes($data[2])."'),
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
							$ins_rec++;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA SUCCESS', 'NORMA KASTRASI SANITASI', '', '');
						} catch (Exception $e) {
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
							
							//log DB
							$this->_global->insertLog('UPLOAD DATA FAILED', 'NORMA KASTRASI SANITASI', '', $e->getCode());
							
							//error log file
							$this->_global->errorLogFile($e->getMessage());
						}
					}else{
						// cek apakah data non-aktif
						$sql = "
							SELECT COUNT(*) 
							FROM TN_KASTRASI_SANITASI
							WHERE ACTIVITY_CODE = '".addslashes($data[0])."' AND
							LAND_SUITABILITY = '".addslashes($data[1])."' AND
							UMUR = '".addslashes($data[2])."'
							AND DELETE_USER IS NULL
						";
						$count = $this->_db->fetchOne($sql);
						
						if ($count == 0) {
							// update data menjadi aktif jika sebelumnya telah dinon-aktifkan
							try {
								$sql = "
									UPDATE TN_KASTRASI_SANITASI
									SET LAND_SUITABILITY = TRIM('".addslashes($data[1])."'),
										UMUR = TRIM('".addslashes($data[2])."'),
										UPDATE_USER = '{$this->_userName}',
										UPDATE_TIME = SYSDATE,
										DELETE_USER = NULL,
										DELETE_TIME = NULL
									WHERE ACTIVITY_CODE = TRIM('".addslashes($data[0])."')
								";
								$this->_db->query($sql);
								$this->_db->commit();
								$update_rec++;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA SUCCESS', 'MASTER ACTIVITY', '', '');
							} catch (Exception $e) {
								//menampilkan data yang tidak ditambahkan
								$data_error[] = $total_rec;
								
								//log DB
								$this->_global->insertLog('UPLOAD DATA FAILED', 'MASTER ACTIVITY', '', $e->getCode());
								
								//error log file
								$this->_global->errorLogFile($e->getMessage());
							}
						}else{
							//menampilkan data yang tidak ditambahkan
							$data_error[] = $total_rec;
						}
					}
				}
				$total_rec++;
			} while ($data = fgetcsv($handle,1000,",","'"));
			//
			$return = array(
						'status'		=> 'done',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}else{
			$return = array(
						'status'		=> 'failed',
						'total'			=> $total_rec - 2,
						'inserted'		=> $ins_rec,
						'updated'		=> $update_rec,
						'data_failed'	=> $data_error
					  );
		}
		
		return $return;
    }
}
