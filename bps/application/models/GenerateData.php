<?php
/*=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	1.0.0
Deskripsi			: 	Model Class untuk Generate Data
Function 			:	- 26/07/14	: genHectareStatementDetail		: generate data detail HS
						- 26/07/14	: genLocationDistribusiVra		: generate data lokasi distribusi VRA
						- 26/07/14	: genRkt						: generate data RKT (RKT Rawat + Rawat Opsi + Rawat Infra + Sanitasi + Tanam)
						- 26/07/14	: genRktPanen					: generate data RKT Panen
						- 26/07/14	: genRktPerkerasanJalan			: generate data RKT Perkerasan Jalan
						- 22/08/14	: genRktPerkerasanJalanNegara	: generate data RKT Perkerasan Jalan Negara
						- 26/07/14	: genRktPupukHa					: generate data RKT Pupuk HA
						- 26/07/14	: genRktCheckroll				: generate data RKT Checkroll
						- 18/08/14	: genNormaPanenSupervisi		: generate data norma panen supervisi
						- 18/08/14	: genNormaHargaBarang			: generate data norma harga barang
						- 18/08/14	: genNormaWraTriggerMappingWra	: generate data norma WRA ketika upload mapping WRA
						- 18/08/14	: genNormaWraTriggerOrganization: generate data norma WRA ketika upload organization
						- 18/08/14	: genNormaWraTriggerPeriodBudget: generate data norma WRA ketika upload period budget
						- 18/08/14	: genNormaHargaPerkerasanJalan	: generate data norma harga perkerasan jalan
						- 21/08/14	: genNormaPanenCostUnit			: generate data norma panen cost unit
						- 22/08/14	: genStandarJamKerja			: generate data standar jam kerja
						- 22/08/14	: genCheckrollHk				: generate data checkroll HK
						- 02/09/14	: genSequence					: generate data sequence per BA
						- 02/09/14	: genCatuSum					: generate data summary Catu
						- 13/09/14	: genNormaPanenKraniBuah		: generate data norma panen krani buah
						- 16/09/14	: genMasterOerBa				: generate data master Oer BA
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	26/07/2014
Update Terakhir		:	04/05/2015
Revisi				:	
	SID 27/04/2015	:	- Penambahan query untuk gaji line 683
	NBU 04/05/2015	:	- Penambahan query untuk OER line 106

=========================================================================================================================*/

class Application_Model_GenerateData extends Zend_Db_Table_Abstract
{	
	public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_siteCode = Zend_Registry::get('auth')->getIdentity()->BA_CODE;
        $this->_global = new Application_Model_Global();
		$this->_userName = Zend_Registry::get('auth')->getIdentity()->USER_NAME;
		$this->_referenceRole = Zend_Registry::get('auth')->getIdentity()->REFERENCE_ROLE; // TAMBAHAN : Sabrina - 19/06/2013
		
		$sess = new Zend_Session_Namespace('period');
		$this->_period = $sess->period;
    }
	
	//generate data detail HS
	public function genHectareStatementDetail($param = array())
    {
		$sql = "
			SELECT PARAMETER_VALUE_CODE AS LAND_CAT
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'LAND_CAT'
			AND DELETE_USER IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$sql = "
					INSERT INTO TM_HECTARE_STATEMENT_DETAIL (PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, LAND_CATEGORY, HA, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',
						'".$param['AFD_CODE']."',
						'".$param['BLOCK_CODE']."',
						'".$row['LAND_CAT']."',
						0,
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
        }		
    }
	
	public function genMasterOerBa($param = array()){
		$sql = "
			SELECT COUNT (1)
			FROM TM_OER_BA
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND BA_CODE = '".$param['BA_CODE']."'
		";
		$jml = $this->_db->fetchOne($sql);
		
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_OER_BA (
					PERIOD_BUDGET, BA_CODE, OER, INSERT_USER, INSERT_TIME) 
				VALUES ( 
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'0',						
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}else{
			$sql = "UPDATE TM_OER_BA
					SET OER = '".$param['OER']."',
						UPDATE_USER = '{$this->_userName}',
						UPDATE_TIME = SYSDATE
					 WHERE BA_CODE = '".$param['BA_CODE']."'
					 AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')";
			//echo $sql;die();
			$this->_db->query($sql);
			$this->_db->commit();
		}
	}
	
	//generate data lokasi distribusi VRA
	public function genLocationDistribusiVra($param = array())
    {
		$sql = "
			SELECT COUNT (LOCATION_CODE)
			FROM TM_LOCATION_DIST_VRA LD
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND LD.BA_CODE = '".$param['BA_CODE']."'
			AND LOCATION_CODE = '".$param['AFD_CODE']."'
		";
		$jml = $this->_db->fetchOne($sql);
		
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_LOCATION_DIST_VRA (PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'".$param['AFD_CODE']."',
					'AFDELING ".$param['AFD_CODE']."',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
        }	

		//kombinasi untuk lokasi bibitan / basecamep / umum / lainnya
		//1. bibitan
		$sql = "
			SELECT COUNT (LOCATION_CODE)
			FROM TM_LOCATION_DIST_VRA LD
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND LD.BA_CODE = '".$param['BA_CODE']."'
			AND LOCATION_CODE = 'BIBITAN'
		";
		$jml = $this->_db->fetchOne($sql);
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_LOCATION_DIST_VRA (PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'BIBITAN',
					'BIBITAN',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		//2. basecamp
		$sql = "
			SELECT COUNT (LOCATION_CODE)
			FROM TM_LOCATION_DIST_VRA LD
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND LD.BA_CODE = '".$param['BA_CODE']."'
			AND LOCATION_CODE = 'BASECAMP'
		";
		$jml = $this->_db->fetchOne($sql);
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_LOCATION_DIST_VRA (PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'BASECAMP',
					'BASECAMP',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		//3. umum
		$sql = "
			SELECT COUNT (LOCATION_CODE)
			FROM TM_LOCATION_DIST_VRA LD
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND LD.BA_CODE = '".$param['BA_CODE']."'
			AND LOCATION_CODE = 'UMUM'
		";
		$jml = $this->_db->fetchOne($sql);
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_LOCATION_DIST_VRA (PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'UMUM',
					'UMUM',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		//4. lain
		$sql = "
			SELECT COUNT (LOCATION_CODE)
			FROM TM_LOCATION_DIST_VRA LD
			WHERE PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
			AND LD.BA_CODE = '".$param['BA_CODE']."'
			AND LOCATION_CODE = 'LAIN'
		";
		$jml = $this->_db->fetchOne($sql);
		if ($jml == 0) {
			$sql = "
				INSERT INTO TM_LOCATION_DIST_VRA (PERIOD_BUDGET, BA_CODE, LOCATION_CODE, DESCRIPTION, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'LAIN',
					'LAIN',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
    }
	
	//generate data RKT (RKT Rawat + Rawat Opsi + Rawat Infra + Sanitasi + Tanam)
	public function genRkt($param = array())
    {
		//cari tipe transaksi
		switch ($param['UI_RKT_CODE']) {
			case 'RKT001': 
				$tipe_transaksi = 'MANUAL_NON_INFRA';
				break;
			case 'RKT002': 
				$tipe_transaksi = 'MANUAL_NON_INFRA_OPSI';
				break;	
			case 'RKT003': 
				$tipe_transaksi = 'MANUAL_INFRA';
				break;	
			case 'RKT005': 
				$tipe_transaksi = 'TANAM';
				break;	
			case 'RKT006': 
				$tipe_transaksi = 'TANAM_MANUAL';
				break;	
			case 'RKT023': 
				$tipe_transaksi = 'KASTRASI_SANITASI';
				break;	
			case 'RKT024': 
				$tipe_transaksi = 'MANUAL_SISIP';
				break;		
		}
		
		//get list activity
		$sql = "
			SELECT ACTIVITY_CODE
			FROM TM_ACTIVITY_MAPPING
			WHERE UI_RKT_CODE = '".$param['UI_RKT_CODE']."'
			AND DELETE_USER IS NULL
			GROUP BY ACTIVITY_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		//get list cost element
		$sql = "
			SELECT PARAMETER_VALUE_CODE as COST_ELEMENT
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'COST_ELEMENT'
				AND PARAMETER_VALUE_CODE <> 'ALL'
				AND DELETE_USER IS NULL
			GROUP BY PARAMETER_VALUE_CODE
		";
		$cost_elements = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$trx_code = substr($this->_period, -4) ."-".
							addslashes($param['BA_CODE']) ."-".
							strtoupper(addslashes($param['AFD_CODE'])) ."-".
							addslashes($param['BLOCK_CODE']) ."-".
							addslashes($param['UI_RKT_CODE']) ."-".
							addslashes($row['ACTIVITY_CODE']);
					
				//RKT	
				$sql = "
					INSERT INTO TR_RKT (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
										ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME)
					VALUES (
						'".$trx_code."',
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',
						'".$param['AFD_CODE']."',
						'".$param['BLOCK_CODE']."',
						'".$tipe_transaksi."',
						'".$row['ACTIVITY_CODE']."',
						'".$param['MATURITY_STAGE_SMS1']."',
						'".$param['MATURITY_STAGE_SMS2']."',						
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				
				if (!empty($cost_elements)) {			
					foreach ($cost_elements as $idx1 => $row1) {
						//RKT COST ELEMENT
						$sql = "
							INSERT INTO TR_RKT_COST_ELEMENT (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
															 ACTIVITY_CODE, MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME, COST_ELEMENT)
							VALUES (
								'".$trx_code."',
								TO_DATE('{$this->_period}','DD-MM-RRRR'),
								'".$param['BA_CODE']."',
								'".$param['AFD_CODE']."',
								'".$param['BLOCK_CODE']."',
								'".$tipe_transaksi."',
								'".$row['ACTIVITY_CODE']."',
								'".$param['MATURITY_STAGE_SMS1']."',
								'".$param['MATURITY_STAGE_SMS2']."',						
								'{$this->_userName}',
								SYSDATE,
								'".$row1['COST_ELEMENT']."'
							)
						";
						$this->_db->query($sql);
						$this->_db->commit();
					}
				}
			}
		}
    }
	
	//generate data RKT Panen
	public function genRktPanen($param = array())
    {
		//get list cost element
		$sql = "
			SELECT PARAMETER_VALUE_CODE as COST_ELEMENT
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'COST_ELEMENT'
				AND PARAMETER_VALUE_CODE <> 'ALL'
				AND DELETE_USER IS NULL
			GROUP BY PARAMETER_VALUE_CODE
		";
		$cost_elements = $this->_db->fetchAll($sql);
		
		$trx_code = substr($this->_period, -4) ."-".
					addslashes($param['BA_CODE']) ."-".
					strtoupper(addslashes($param['AFD_CODE'])) ."-".
					addslashes($param['BLOCK_CODE']) ."-".
					'RKT014' ."-".
					'51100';
			
		//RKT	
		$sql = "
			INSERT INTO TR_RKT_PANEN (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
									  MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME)
			VALUES (
				'".$trx_code."',
				TO_DATE('{$this->_period}','DD-MM-RRRR'),
				'".$param['BA_CODE']."',
				'".$param['AFD_CODE']."',
				'".$param['BLOCK_CODE']."',
				'51100',
				'".$param['MATURITY_STAGE_SMS1']."',
				'".$param['MATURITY_STAGE_SMS2']."',						
				'{$this->_userName}',
				SYSDATE
			)
		";
		$this->_db->query($sql);
		$this->_db->commit();
		
		if (!empty($cost_elements)) {			
			foreach ($cost_elements as $idx1 => $row1) {
				//RKT COST ELEMENT
				$sql = "
					INSERT INTO TR_RKT_PANEN_COST_ELEMENT (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
														   MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME, COST_ELEMENT)
					VALUES (
						'".$trx_code."',
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',
						'".$param['AFD_CODE']."',
						'".$param['BLOCK_CODE']."',
						'51100',
						'".$param['MATURITY_STAGE_SMS1']."',
						'".$param['MATURITY_STAGE_SMS2']."',						
						'{$this->_userName}',
						SYSDATE,
						'".$row1['COST_ELEMENT']."'
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
		}
	}
	
	//generate data RKT Perkerasan Jalan
	public function genRktPerkerasanJalan($param = array())
    {
		//get list activity
		$sql = "
			SELECT ACTIVITY_CODE
			FROM TM_ACTIVITY_MAPPING
			WHERE UI_RKT_CODE = '".$param['UI_RKT_CODE']."'
			AND DELETE_USER IS NULL
			GROUP BY ACTIVITY_CODE
		";
		$rows = $this->_db->fetchAll($sql);
		
		//get list cost element
		$sql = "
			SELECT PARAMETER_VALUE_CODE as COST_ELEMENT
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'COST_ELEMENT'
				AND PARAMETER_VALUE_CODE <> 'ALL'
				AND DELETE_USER IS NULL
			GROUP BY PARAMETER_VALUE_CODE
		";
		$cost_elements = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$trx_code = substr($this->_period, -4) ."-".
							addslashes($param['BA_CODE']) ."-".
							strtoupper(addslashes($param['AFD_CODE'])) ."-".
							addslashes($param['BLOCK_CODE']) ."-".
							addslashes($param['UI_RKT_CODE']) ."-".
							addslashes($row['ACTIVITY_CODE']);
					
				//RKT	
				$sql = "
					INSERT INTO TR_RKT_PK (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
										   MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME)
					VALUES (
						'".$trx_code."',
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',
						'".$param['AFD_CODE']."',
						'".$param['BLOCK_CODE']."',
						'".$row['ACTIVITY_CODE']."',
						'".$param['MATURITY_STAGE_SMS1']."',
						'".$param['MATURITY_STAGE_SMS2']."',						
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
				
				if (!empty($cost_elements)) {			
					foreach ($cost_elements as $idx1 => $row1) {
						//RKT COST ELEMENT
						$sql = "
							INSERT INTO TR_RKT_PK_COST_ELEMENT (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
																MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME, COST_ELEMENT)
							VALUES (
								'".$trx_code."',
								TO_DATE('{$this->_period}','DD-MM-RRRR'),
								'".$param['BA_CODE']."',
								'".$param['AFD_CODE']."',
								'".$param['BLOCK_CODE']."',
								'".$row['ACTIVITY_CODE']."',
								'".$param['MATURITY_STAGE_SMS1']."',
								'".$param['MATURITY_STAGE_SMS2']."',						
								'{$this->_userName}',
								SYSDATE,
								'".$row1['COST_ELEMENT']."'
							)
						";
						$this->_db->query($sql);
						$this->_db->commit();
					}
				}
			}
        }
    }
	
	//generate data RKT Perkerasan Jalan Negara
	public function genRktPerkerasanJalanNegara($param = array())
    {
		$row['ACTIVITY_CODE'] = "10470"; //kode aktivitas perkerasan jalan negara
		
		//get list cost element
		$sql = "
			SELECT PARAMETER_VALUE_CODE as COST_ELEMENT
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'COST_ELEMENT'
				AND PARAMETER_VALUE_CODE <> 'ALL'
				AND DELETE_USER IS NULL
			GROUP BY PARAMETER_VALUE_CODE
		";
		$cost_elements = $this->_db->fetchAll($sql);
		
		$trx_code = substr($this->_period, -4) ."-".
					addslashes($param['BA_CODE']) ."-".
					strtoupper(addslashes($param['AFD_CODE'])) ."-".
					addslashes($param['BLOCK_CODE']) ."-".
					addslashes($param['UI_RKT_CODE']) ."-".
					addslashes($row['ACTIVITY_CODE']);
			
		//RKT	
		$sql = "
			INSERT INTO TR_RKT_PK (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
								   MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME)
			VALUES (
				'".$trx_code."',
				TO_DATE('{$this->_period}','DD-MM-RRRR'),
				'".$param['BA_CODE']."',
				'".$param['AFD_CODE']."',
				'".$param['BLOCK_CODE']."',
				'".$row['ACTIVITY_CODE']."',
				'".$param['MATURITY_STAGE_SMS1']."',
				'".$param['MATURITY_STAGE_SMS2']."',						
				'{$this->_userName}',
				SYSDATE
			)
		";
		$this->_db->query($sql);
		$this->_db->commit();
		
		if (!empty($cost_elements)) {			
			foreach ($cost_elements as $idx1 => $row1) {
				//RKT COST ELEMENT
				$sql = "
					INSERT INTO TR_RKT_PK_COST_ELEMENT (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, ACTIVITY_CODE, 
														MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME, COST_ELEMENT)
					VALUES (
						'".$trx_code."',
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',
						'".$param['AFD_CODE']."',
						'".$param['BLOCK_CODE']."',
						'".$row['ACTIVITY_CODE']."',
						'".$param['MATURITY_STAGE_SMS1']."',
						'".$param['MATURITY_STAGE_SMS2']."',						
						'{$this->_userName}',
						SYSDATE,
						'".$row1['COST_ELEMENT']."'
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
		}
    }
	
	//generate data RKT Pupuk HA
	public function genRktPupukHa($param = array())
    {
		$trx_code = substr($this->_period, -4) ."-".
					addslashes($param['BA_CODE']) ."-".
					strtoupper(addslashes($param['AFD_CODE'])) ."-".
					addslashes($param['BLOCK_CODE']) ."-".
					'RKT008';
			
		$sql = "
			INSERT INTO TR_RKT_PUPUK (TRX_RKT_CODE, PERIOD_BUDGET, BA_CODE, AFD_CODE, BLOCK_CODE, TIPE_TRANSAKSI, 
									  MATURITY_STAGE_SMS1, MATURITY_STAGE_SMS2, INSERT_USER, INSERT_TIME)
			VALUES (
				'".$trx_code."',
				TO_DATE('{$this->_period}','DD-MM-RRRR'),
				'".$param['BA_CODE']."',
				'".$param['AFD_CODE']."',
				'".$param['BLOCK_CODE']."',
				'HA',
				'".$param['MATURITY_STAGE_SMS1']."',
				'".$param['MATURITY_STAGE_SMS2']."',						
				'{$this->_userName}',
				SYSDATE
			)
		";
		$this->_db->query($sql);
		$this->_db->commit();
    }
	
	//generate data RKT Checkroll
	public function genRktCheckroll($param = array())
    {
		$trx_code = substr($this->_period, -4) ."-".
					addslashes($param['BA_CODE']) ."-CR-".
					$this->_global->randomString(15);
			
		//get list tunjangan
		$sql = "
			SELECT TUNJANGAN_TYPE
			FROM TM_TUNJANGAN
			WHERE DELETE_TIME IS NULL
			AND FLAG_EMPLOYEE_STATUS = '".$param['EMPLOYEE_STATUS']."'
		";
		$rows = $this->_db->fetchAll($sql);
		
		$sql = "
			INSERT INTO TR_RKT_CHECKROLL (TRX_CR_CODE, PERIOD_BUDGET, BA_CODE, JOB_CODE, EMPLOYEE_STATUS, GP_INFLASI,
										  INSERT_USER, INSERT_TIME)
			VALUES (
				'".$trx_code."',
				TO_DATE('{$this->_period}','DD-MM-RRRR'),
				'".$param['BA_CODE']."',
				'".$param['JOB_CODE']."',
				'".$param['EMPLOYEE_STATUS']."',
				F_CALC_PRICE_INFLASI (TO_DATE('{$this->_period}','DD-MM-RRRR'), '".$param['BA_CODE']."', 'NC001', REPLACE('".addslashes($param['GP'])."', ',', '')),						
				'{$this->_userName}',
				SYSDATE
			)
		";
		$this->_db->query($sql);
		$this->_db->commit();
		
		//insert RKT Checkroll SUM
		$sql = "
			SELECT COUNT(BA_CODE)
			FROM TR_RKT_CHECKROLL_SUM
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
				AND JOB_CODE = '".$param['JOB_CODE']."'
		";
		$cRow = $this->_db->fetchOne($sql);
		
		if($cRow == 0){
			$sql = "
				INSERT INTO TR_RKT_CHECKROLL_SUM (PERIOD_BUDGET, BA_CODE, JOB_CODE, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'".$param['JOB_CODE']."',					
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
		
		//insert RKT checkroll detail
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$sql = "
					INSERT INTO TR_RKT_CHECKROLL_DETAIL (TRX_CR_CODE, TUNJANGAN_TYPE, INSERT_USER, INSERT_TIME, PERIOD_BUDGET, BA_CODE)
					VALUES (
						'".$trx_code."',
						'".$row['TUNJANGAN_TYPE']."',						
						'{$this->_userName}',
						SYSDATE,
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."'
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
        }
		
		
		//insert RKT checkroll distribusi
		$sql = "
			SELECT COUNT(BA_CODE)
			FROM TR_RPT_DISTRIBUSI_COA
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
		";
		$cRow = $this->_db->fetchOne($sql);
		
		if($cRow == 0){	
			//get list tunjangan + gaji pokok
			$sql = "
				SELECT PARAMETER_VALUE_CODE as TUNJANGAN_TYPE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_TIME IS NULL
					AND PARAMETER_CODE = 'TUNJANGAN'
				UNION ALL
				SELECT 'GAJI' as TUNJANGAN_TYPE
				FROM DUAL
			";
			$rows = $this->_db->fetchAll($sql);
		
			//get list maturity status
			$sql = "
				SELECT PARAMETER_VALUE_CODE as MATURITY_STAGE
				FROM T_PARAMETER_VALUE
				WHERE DELETE_TIME IS NULL
					AND PARAMETER_CODE = 'MATURITY_STAGE'
					AND PARAMETER_VALUE_CODE <> 'ALL'
			";
			$rows1 = $this->_db->fetchAll($sql);
			
			if (!empty($rows)) {			
				foreach ($rows as $idx => $row) {
					if (!empty($rows1)) {			
						foreach ($rows1 as $idx1 => $row1) {
							$sql = "
								INSERT INTO TR_RPT_DISTRIBUSI_COA (PERIOD_BUDGET, BA_CODE, MATURITY_STAGE, TUNJANGAN_TYPE, REPORT_TYPE, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('{$this->_period}','DD-MM-RRRR'),
									'".$param['BA_CODE']."',
									'".$row1['MATURITY_STAGE']."',
									'".$row['TUNJANGAN_TYPE']."',	
									'CR_ALOKASI',
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
						}
					}
				}
			}
		}
    }
	
	//generate data norma panen supervisi
	public function genNormaPanenSupervisi($param = array())
    {
		$sql = "
			SELECT COUNT(MIN_BJR)
			FROM TN_PANEN_SUPERVISI
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
				AND DELETE_TIME IS NULL
				AND MIN_BJR = '".$param['BJR_MIN']."'
				AND MAX_BJR = '".$param['BJR_MAX']."'
		";
		$cData = $this->_db->fetchOne($sql);
		
		if ($cData == 0){			
			$sql = "
				INSERT INTO TN_PANEN_SUPERVISI (PERIOD_BUDGET, BA_CODE, MIN_BJR, MAX_BJR, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',
					'".$param['BJR_MIN']."',
					'".$param['BJR_MAX']."',						
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
    }
	
	//generate data norma harga barang
	public function genNormaHargaBarang($param = array())
    {
		$sql = "
			DELETE FROM TN_HARGA_BARANG
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
				AND MATERIAL_CODE = '".$param['MATERIAL_CODE']."'
				AND STATUS = '".$param['STATUS']."'
		";
		$this->_db->query($sql);
		$this->_db->commit();
		
		$sql = "
			INSERT INTO TN_HARGA_BARANG (PERIOD_BUDGET, REGION_CODE, BA_CODE, MATERIAL_CODE, PRICE, STATUS, INSERT_USER, INSERT_TIME)
			VALUES (
				TO_DATE('{$this->_period}','DD-MM-RRRR'),
				F_GET_REGION ('".$param['BA_CODE']."'),
				'".$param['BA_CODE']."',
				'".$param['MATERIAL_CODE']."',
				F_CALC_PRICE_INFLASI (TO_DATE('{$this->_period}','DD-MM-RRRR'), '".$param['BA_CODE']."', '".$param['BASIC_NORMA_CODE']."', '".$param['PRICE']."'),
				'".$param['STATUS']."',						
				'{$this->_userName}',
				SYSDATE
			)
		";
		$this->_db->query($sql);
		$this->_db->commit();
    }
	
	public function genSequence($param = array()){
		//get BA
		$sql = "
			SELECT TASK_NAME
			FROM T_SEQ
			WHERE DELETE_TIME IS NULL
			";
		$rows = $this->_db->fetchAll($sql);
		$arrtask = array();
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				array_push($arrtask, $row['TASK_NAME']);
			}
		}
		
		$sql = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {
			foreach ($rows as $idx => $row) {
				for($x=0;$x<count($arrtask);$x++){
					$sql = "
						SELECT COUNT(1)
						FROM T_SEQ_CHECK
						WHERE PERIOD_BUDGET  = TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR')
						AND BA_CODE = '".$row['BA_CODE']."'
						AND TASK_NAME = '".$arrtask[$x]."'
					";
					$count_row = $this->_db->fetchOne($sql);
					
					if($count_row == 0){
						$sql = "
							INSERT INTO 
								T_SEQ_CHECK(PERIOD_BUDGET, BA_CODE, TASK_NAME, INSERT_USER, INSERT_TIME)
							VALUES(
								TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR'),
								'".$row['BA_CODE']."',
								'".$arrtask[$x]."',
								'{$this->_userName}',
								SYSDATE
							)";
						$this->_db->query($sql);
						$this->_db->commit();
					}
				}
			}
		}
	}
	
	//generate data norma WRA ketika upload mapping WRA
	public function genNormaWraTriggerMappingWra($param = array())
    {
		//get BA
		$sql = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				//cek group wra 1
				$sql = "
					SELECT COUNT(BA_CODE)
					FROM TN_WRA
					WHERE DELETE_TIME IS NULL
						AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
						AND BA_CODE = '".$row['BA_CODE']."'
						AND GROUP_WRA_CODE = '1'
				";
				$cRow = $this->_db->fetchOne($sql);
				
				if($cRow == 0){
					$sql = "
						SELECT JAM_KERJA
						FROM TM_STANDART_JAM_KERJA_WRA
						WHERE DELETE_TIME IS NULL
							AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
							AND BA_CODE = '".$row['BA_CODE']."'
					";
					$jam_kerja = $this->_db->fetchOne($sql);
					
					$sql = "
						INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, INSERT_USER, INSERT_TIME)
						VALUES (
							TO_DATE('{$this->_period}','DD-MM-RRRR'),
							'".$row['BA_CODE']."',	
							'1',		
							'JAM KERJA',
							'".$jam_kerja."',	
							'12',
							'".($jam_kerja * 12)."',
							'{$this->_userName}',
							SYSDATE
						)
					";
					$this->_db->query($sql);
					$this->_db->commit();
				}		
		
				$sql = "
					INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$row['BA_CODE']."',	
						'".$param['WRA_GROUP_CODE']."',		
						'".$param['JOB_CODE']."',		
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
        }
    }
	
	//generate data norma WRA ketika upload ORG
	public function genNormaWraTriggerOrganization($param = array())
    {
		//cek group wra 1
		$sql = "
			SELECT COUNT(BA_CODE)
			FROM TN_WRA
			WHERE DELETE_TIME IS NULL
				AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
				AND BA_CODE = '".$param['BA_CODE']."'
				AND GROUP_WRA_CODE = '1'
		";
		$cRow = $this->_db->fetchOne($sql);
		
		if($cRow == 0){
			$sql = "
				SELECT JAM_KERJA
				FROM TM_STANDART_JAM_KERJA_WRA
				WHERE DELETE_TIME IS NULL
					AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
					AND BA_CODE = '".$param['BA_CODE']."'
			";
			$jam_kerja = $this->_db->fetchOne($sql);
			
			$sql = "
				INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',	
					'1',		
					'JAM KERJA',
					'".$jam_kerja."',	
					'12',
					'".($jam_kerja * 12)."',
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}	
				
		//get mapping WRA
		$sql = "
			SELECT WRA_GROUP_CODE, JOB_CODE
			FROM TM_MAPPING_JOB_TYPE_WRA
			WHERE DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$sql = "
					INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',	
						'".$row['WRA_GROUP_CODE']."',		
						'".$row['JOB_CODE']."',		
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
        }
    }
	
	//generate data norma WRA ketika upload period budget
	public function genNormaWraTriggerPeriodBudget($param = array())
    {
		//get mapping WRA
		$sql = "
			SELECT WRA_GROUP_CODE, JOB_CODE
			FROM TM_MAPPING_JOB_TYPE_WRA
			WHERE DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		//get BA
		$sql = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_TIME IS NULL
		";
		$rows1 = $this->_db->fetchAll($sql);
		
		if (!empty($rows1)) {			
			foreach ($rows1 as $idx1 => $row1) {
				//cek group wra 1
				$sql = "
					SELECT COUNT(BA_CODE)
					FROM TN_WRA
					WHERE DELETE_TIME IS NULL
						AND PERIOD_BUDGET = TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR')
						AND BA_CODE = '".$row1['BA_CODE']."'
						AND GROUP_WRA_CODE = '1'
				";
				$cRow = $this->_db->fetchOne($sql);
				
				if($cRow == 0){
					$sql = "
						SELECT JAM_KERJA
						FROM TM_STANDART_JAM_KERJA_WRA
						WHERE DELETE_TIME IS NULL
							AND PERIOD_BUDGET = TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR')
							AND BA_CODE = '".$row1['BA_CODE']."'
					";
					$jam_kerja = $this->_db->fetchOne($sql);
					
					$sql = "
						INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, QTY_ROTASI, ROTASI_TAHUN, QTY_TAHUN, INSERT_USER, INSERT_TIME)
						VALUES (
							TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR'),
							'".$row1['BA_CODE']."',	
							'1',		
							'JAM KERJA',
							'".$jam_kerja."',	
							'12',
							'".($jam_kerja * 12)."',
							'{$this->_userName}',
							SYSDATE
						)
					";
					$this->_db->query($sql);
					$this->_db->commit();
				}	
			
			
				if (!empty($rows)) {			
					foreach ($rows as $idx => $row) {
						$sql = "
							INSERT INTO TN_WRA (PERIOD_BUDGET, BA_CODE, GROUP_WRA_CODE, SUB_WRA_GROUP, INSERT_USER, INSERT_TIME)
							VALUES (
								TO_DATE('".$param['PERIOD_BUDGET']."','DD-MM-RRRR'),
								'".$row1['BA_CODE']."',	
								'".$row['WRA_GROUP_CODE']."',		
								'".$row['JOB_CODE']."',		
								'{$this->_userName}',
								SYSDATE
							)
						";
						$this->_db->query($sql);
						$this->_db->commit();
					}
				}
			}
        }
    }
	
	//generate data norma harga perkerasan jalan
	public function genNormaHargaPerkerasanJalan($param = array())
    {
		//get range
		$sql = "
			SELECT 	PARAMETER_VALUE_CODE JARAK_RANGE,  
					PARAMETER_VALUE JARAK_DESC,
					PARAMETER_VALUE_2 JARAK_AVG,
					PARAMETER_VALUE_2 * 2 JARAK_PP
			FROM T_PARAMETER_VALUE
			WHERE PARAMETER_CODE = 'JARAK_PERKERASAN_JALAN'
				AND DELETE_TIME IS NULL
		";
		$rows = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				$sql = "
					INSERT INTO TN_PERKERASAN_JALAN_HARGA (PERIOD_BUDGET, BA_CODE, ACTIVITY_CODE, JARAK_RANGE, JARAK_AVG, JARAK_PP, INSERT_USER, INSERT_TIME)
					VALUES (
						TO_DATE('{$this->_period}','DD-MM-RRRR'),
						'".$param['BA_CODE']."',	
						'".$param['ACTIVITY_CODE']."',		
						'".$row['JARAK_RANGE']."',		
						'".$row['JARAK_AVG']."',		
						'".$row['JARAK_PP']."',		
						'{$this->_userName}',
						SYSDATE
					)
				";
				$this->_db->query($sql);
				$this->_db->commit();
			}
        }
    }
	
	//generate data norma panen krani buah
	public function genNormaPanenKraniBuah($param = array())
    {
		$sql = "
			SELECT COUNT(BA_CODE)
			FROM TN_PANEN_KRANI_BUAH
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
		";
		$cRow = $this->_db->fetchOne($sql);
		
		if($cRow == 0){
			// RP_HK, RP_KG_BASIS, TOTAL_RP_PREMI, RP_KG_PREMI
			$param['SELISIH_OVER_BASIS'] = $param['TARGET']-$param['BASIS'];
			$sql = "
				SELECT AVG(RP_HK) 
					FROM TR_RKT_CHECKROLL_SUM 
					WHERE JOB_CODE = 'FX160'
						AND PERIOD_BUDGET = TO_DATE('{$this->_period}','DD-MM-RRRR')
						AND BA_CODE = '".$param['BA_CODE']."'";
			$rpHk = $this->_db->fetchOne($sql);
			$rpKgBasis = $rpHk/$param['TARGET']/1000;
			$totRpPremi = $param['SELISIH_OVER_BASIS']*$param['TARIF_BASIS'];
			$rpKgPremi = $totRpPremi/$param['TARGET']/1000;
			
			$sql = "
				INSERT INTO TN_PANEN_KRANI_BUAH (
					PERIOD_BUDGET, BA_CODE, TARGET, BASIS, TARIF_BASIS, SELISIH_OVER_BASIS, 
					RP_HK, RP_KG_BASIS, TOTAL_RP_PREMI, RP_KG_PREMI, INSERT_USER, INSERT_TIME) 
				  VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."', '".$param['TARGET']."', '".$param['BASIS']."', '".$param['TARIF_BASIS']."', '".$param['SELISIH_OVER_BASIS']."',				
					'$rpHk', '$rpKgBasis', '$totRpPremi', '$rpKgPremi','{$this->_userName}', SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
    }
	
	//generate data norma panen cost unit
	public function genNormaPanenCostUnit($param = array())
    {
		$sql = "
			SELECT COUNT(BA_CODE)
			FROM TN_PANEN_PREMI_COST_UNIT
			WHERE to_char(PERIOD_BUDGET,'DD-MM-RRRR')= '{$this->_period}'
				AND BA_CODE = '".$param['BA_CODE']."'
		";
		$cRow = $this->_db->fetchOne($sql);
		
		if($cRow == 0){
			$sql = "
				INSERT INTO TN_PANEN_PREMI_COST_UNIT (PERIOD_BUDGET, BA_CODE, INSERT_USER, INSERT_TIME)
				VALUES (
					TO_DATE('{$this->_period}','DD-MM-RRRR'),
					'".$param['BA_CODE']."',				
					'{$this->_userName}',
					SYSDATE
				)
			";
			$this->_db->query($sql);
			$this->_db->commit();
		}
    }
	
	//generate data standar jam kerja
	public function genStandarJamKerja()
    {
		//get all BA
		$sql = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
		";
		$rows = $this->_db->fetchAll($sql);		
		
		//get all period budget
		$sql = "
			SELECT PERIOD_BUDGET
			FROM TM_PERIOD
			WHERE DELETE_USER IS NULL
				AND STATUS = 'OPEN'
		";
		$rows1 = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				if (!empty($rows1)) {			
					foreach ($rows1 as $idx1 => $row1) {
						$sql = "
							SELECT COUNT(BA_CODE)
							FROM TM_STANDART_JAM_KERJA_WRA
							WHERE PERIOD_BUDGET = TO_DATE('".$row1['PERIOD_BUDGET']."','DD-MM-RRRR')
								AND BA_CODE = '".$row['BA_CODE']."'
						";
						$cRow = $this->_db->fetchOne($sql);
						
						if($cRow == 0){
							$sql = "
								INSERT INTO TM_STANDART_JAM_KERJA_WRA (PERIOD_BUDGET, BA_CODE, JAM_KERJA, INSERT_USER, INSERT_TIME)
								VALUES (
									TO_DATE('".$row1['PERIOD_BUDGET']."','DD-MM-RRRR'),
									'".$row['BA_CODE']."',		
									'3000', -- default awal
									'{$this->_userName}',
									SYSDATE
								)
							";
							$this->_db->query($sql);
							$this->_db->commit();
						}
					}
				}
			}
		}
    }
	
	public function genCatuSum($row = array()){
		$sql = "
			DELETE FROM TM_CATU_SUM
			WHERE BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND PERIOD_BUDGET = TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR')";
		$this->_db->query($sql);
		$this->_db->commit();	
		
		$sql = "
			SELECT AVG(CATU_BERAS) CATU_BERAS
			FROM TM_CATU
			WHERE PERIOD_BUDGET = TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR')
				AND BA_CODE = '".addslashes($row['BA_CODE'])."'
				AND DELETE_USER IS NULL
		";
		$sum_price = $this->_db->fetchOne($sql);
		
		$sql = "
			INSERT INTO TM_CATU_SUM (PERIOD_BUDGET, BA_CODE, CATU_BERAS_SUM, INSERT_USER, INSERT_TIME)
			VALUES (
				TO_DATE('01-01-".$row['PERIOD_BUDGET']."', 'DD-MM-RRRR'),
				'".addslashes($row['BA_CODE'])."',
				'".addslashes(number_format($sum_price, 2, '.', ''))."',
				'{$this->_userName}',
				SYSDATE
			)";
		$this->_db->query($sql);
		$this->_db->commit();
	}
	
	//generate data checkroll HK
	public function genCheckrollHk()
    {
		//get all BA
		$sql = "
			SELECT BA_CODE
			FROM TM_ORGANIZATION
			WHERE DELETE_USER IS NULL
		";
		$rows = $this->_db->fetchAll($sql);		
		
		//get all period budget
		$sql = "
			SELECT PERIOD_BUDGET
			FROM TM_PERIOD
			WHERE DELETE_USER IS NULL
				AND STATUS = 'OPEN'
		";
		$rows1 = $this->_db->fetchAll($sql);
		
		//get all job status
		$sql = "
			SELECT PARAMETER_VALUE_CODE as EMPLOYEE_STATUS
			FROM T_PARAMETER_VALUE
			WHERE DELETE_USER IS NULL
				AND PARAMETER_CODE = 'JOB_STATUS'
		";
		$rows2 = $this->_db->fetchAll($sql);
		
		if (!empty($rows)) {			
			foreach ($rows as $idx => $row) {
				if (!empty($rows1)) {			
					foreach ($rows1 as $idx1 => $row1) {
						if (!empty($rows2)) {			
							foreach ($rows2 as $idx2 => $row2) {
								$sql = "
									SELECT COUNT(BA_CODE)
									FROM TM_CHECKROLL_HK
									WHERE PERIOD_BUDGET = TO_DATE('".$row1['PERIOD_BUDGET']."','DD-MM-RRRR')
										AND BA_CODE = '".$row['BA_CODE']."'
										AND EMPLOYEE_STATUS = '".$row2['EMPLOYEE_STATUS']."'
								";
								$cRow = $this->_db->fetchOne($sql);
								
								if($cRow == 0){
									$sql = "
										INSERT INTO TM_CHECKROLL_HK (PERIOD_BUDGET, BA_CODE, EMPLOYEE_STATUS, INSERT_USER, INSERT_TIME)
										VALUES (
											TO_DATE('".$row1['PERIOD_BUDGET']."','DD-MM-RRRR'),
											'".$row['BA_CODE']."',	
											'".$row2['EMPLOYEE_STATUS']."',
											'{$this->_userName}',
											SYSDATE
										)
									";
									$this->_db->query($sql);
									$this->_db->commit();
								}
							}
						}
					}
				}
			}
		}
    }
}
