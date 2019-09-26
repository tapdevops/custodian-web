<!--
=========================================================================================================================
Project				: 	Sinkronisasi Data HRIS & Table MASTER untuk aplikasi-aplikasi PHP (Custodian, PT/PA, dan CSR)
Versi				: 	1.2.0
Deskripsi			: 	Sinkronisasi Divisi, Departemen, Jabatan Karyawan, dan Data Karyawan di database MASTER
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Indrabudhi Lokaadi
Dibuat Tanggal		: 	01/07/2012
UPDATE Terakhir		:	19/11/2012
Revisi				:	
	# Sabrina - 19/11/2012 	: Penyesuaian TABLE MASTER
=========================================================================================================================
-->

<!-- REFRESH PAGE TIAP 1 JAM -->
<meta http-equiv="refresh" content="3600">

<?php

//$vLocation = "C:/xampp/htdocs/imp_emp_dw/pro/custodian";
//$vLocation = "../../dev/custodian";
include($vLocation."class.mysql_connection.php");

include($vLocation."oracle_function.php");
include($vLocation."oracle_connect.php");

$con = connect();
$db = new Database;
/* =================== */
/* UPDATE DATA COMPANY */
/* =================== */
$today = date("D M j G:i:s T Y");

echo "
==============<br />
DATA COMPANY<br />
==============<br />
";
echo "Start Company Import .. $today <br />";
$query_comp = "SELECT * FROM TAP_DW.TM_COMP_HRIS";
$result_comp = select_all_data($con, $query_comp);

$comp_temp_drop = "DROP TABLE IF EXISTS temp_exp_company";
$db->query($comp_temp_drop);

$comp_temp_create = "CREATE TABLE temp_exp_company( `Company_Name` VARCHAR(10) NOT NULL, `Company_Description` VARCHAR(100) NULL )";
$db->query($comp_temp_create);
$count = 0;
foreach ($result_comp as $k => $v) {
	if ($v['KODE_COMPANY'] != "") {
		$comp_temp_insert = "INSERT INTO temp_exp_company (Company_Name, Company_Description) VALUES ('{$v['KODE_COMPANY']}', '{$v['NAMA_COMPANY']}')";
		if ($db->query($comp_temp_insert)) {
			echo '[ Success ] : ' . $comp_temp_insert . '<br />';
		} else {
			echo '[ Failed ] : ' . $comp_temp_insert . '<br />';
		}
		$count += 1;
	}
}
echo "=== Insert Company : There is $count data insert into temp_exp_company ===<br /><br />";

$comp_temp_get = "SELECT * FROM temp_exp_company";
$db->query($comp_temp_get);

$db_exp = new Database;
$count = $count_insert = $count_update = 0;
while ($db->nextRecord()) {
	$comp_ori_get = "SELECT * FROM M_Company WHERE UPPER(Company_Name) = UPPER('{$db->Record['Company_Name']}')";
	$db_exp->query($comp_ori_get);
	$db_exp->singleRecord();
	$comp_name = $db_exp->Record['Company_Name'];

	if (empty($comp_name) || $comp_name == "") {
		echo "### [ Insert Data ] : {$db->Record['Company_Name']} | {$db->Record['Company_Description']} : ";
		$comp_ori_execute = "
			INSERT INTO M_Company (
				Company_Name, Company_Description, Company_RegionCode, Company_Region, Company_Status, Company_InsertUser, Company_InsertTime, Company_UpdateUser, Company_UpdateTime
			) VALUES (
				'{$db->Record['Company_Name']}', '{$db->Record['Company_Description']}', '0', '-', '1', 'auto_syn_hris', sysdate(), 'auto_syn_hris', sysdate()
			)
		";
		$count_insert += 1;
	} else {
		echo "### [ Update Data ] : {$db->Record['Company_Name']} | {$db->Record['Company_Description']} : ";
		$comp_ori_execute = "
			UPDATE M_Company 
			SET Company_Name = '{$db->Record['Company_Name']}',
				Company_Description = '{$db->Record['Company_Description']}',
				Company_UpdateUser = 'auto_syn_hris',
				Company_UpdateTime = sysdate()
			WHERE Company_Name = '{$db->Record['Company_Name']}'
		";
		$count_update += 1;
	}
	if ($db_exp->query($comp_ori_execute)) {
		echo 'Success <br />';
	} else {
		echo 'Failed <br />';
	}

	$count += 1;
}
echo "<br>";
echo "... Export Data Company into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br /><br /><br />";

//inactive company yang terdapat di MASTER tp tidak terdapat di HRIS
/*$sqlqry = "	UPDATE M_Division 
			SET Division_InactiveTime = sysdate(), 
				Division_Status = '0'
			WHERE Division_Code NOT IN (SELECT Div_Code FROM temp_exp_division) 
			AND Division_InactiveTime IS NULL";
$db->query($sqlqry);
*/
//drop temporary table
$sqlqry = "DROP TABLE IF EXISTS temp_exp_company";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Company Import .. $today <BR><BR><BR>";




$db = new Database;
/* ==================== */
/* UPDATE DATA DIVISION */
/* ==================== */
echo "
=============<br />
DATA DIVISION<br />
=============<br />
";
echo "Start Division Import .. $today <br />";
$query_div = "SELECT * FROM TAP_DW.TM_ORGADDGROUP_HRIS ORDER BY ORGADTNAME ASC";
$result_div = select_all_data($con, $query_div);

$div_temp_drop = "DROP TABLE IF EXISTS temp_exp_div";
$db->query($div_temp_drop);

$div_temp_create = "CREATE TABLE temp_exp_div( `Division_Name` VARCHAR(100) NOT NULL, `Division_Code_Hcis` VARCHAR(15) NULL )";
$db->query($div_temp_create);
$count = 0;
foreach ($result_div as $k => $v) {
	if ($v['ORGADTCODE'] != "") {
		$div_trim = trim($v['ORGADTCODE']);
		$div_temp_insert = "INSERT INTO temp_exp_div (Division_Name, Division_Code_Hcis) VALUES ('{$v['ORGADTNAME']}', '{$div_trim}')";
		if ($db->query($div_temp_insert)) {
			echo '[ Success ] : ' . $div_temp_insert . '<br />';
		} else {
			echo '[ Failed ] : ' . $div_temp_insert . '<br />';
		}
		$count += 1;
	}
}
echo "=== Insert Division : There is $count data insert into temp_exp_div ===<br /><br />";

$div_temp_get = "SELECT * FROM temp_exp_div";
$db->query($div_temp_get);

$db_exp = new Database;
$count = $count_insert = $count_update = 0;
while ($db->nextRecord()) {
	//$div_ori_get = "SELECT * FROM M_Division WHERE Division_Name = '{$db->Record['Division_Name']}'";
	//$div_ori_get = "SELECT Division_Code FROM M_Division WHERE UPPER(Division_Name) = UPPER('{$db->Record['Division_Name']}')";
	$div_ori_get = "SELECT Division_Code FROM M_Division WHERE Division_Code = '{$db->Record['Division_Code_Hcis']}'";
	$db_exp->query($div_ori_get);
	$db_exp->singleRecord();
	$div_code = $db_exp->Record['Division_Code'];

	if (empty($div_code) || $div_code == "") {
		echo "### [ Insert Data ] : {$db->Record['Division_Name']} | {$db->Record['Division_Code_Hcis']} : ";
		/*$div_ori_execute = "
			INSERT INTO M_Division (
				Division_Code, Division_Name, Division_Status, Division_Code_Hcis, Division_InsertUser, Division_InsertTime, Division_UpdateUser, Division_UpdateTime, Division_InactiveTime
			) VALUES (
				'{$db->Record['Division_Code_Hcis']}', '{$db->Record['Division_Name']}', '0', '{$db->Record['Division_Code_Hcis']}', 'auto_syn_hris', sysdate(), 'auto_syn_hris', sysdate(), sysdate()
			)
		";*/
		$div_ori_execute = "
			INSERT INTO M_Division 
				SET Division_Code = '{$db->Record['Division_Code_Hcis']}',
					Division_Code_Hcis = '{$db->Record['Division_Code_Hcis']}',
					Division_Name = '{$db->Record['Division_Name']}',
					Division_Status = '0',
					Division_InsertUser = 'auto_syn_hris',
					Division_InsertTime = sysdate(),
					Division_UpdateUser = 'auto_syn_hris',
					Division_UpdateTime = sysdate(),
					Division_InactiveTime = sysdate()";
		$count_insert += 1;
	} else {
		echo "### [ Update Data ] : {$db->Record['Division_Name']} | {$db->Record['Division_Code_Hcis']} : ";
		$div_ori_execute = "
			UPDATE M_Division
			SET Division_Name = '{$db->Record['Division_Name']}',
				Division_Code_Hcis = '{$db->Record['Division_Code_Hcis']}',
				Division_Status = '0',
				Division_UpdateUser = 'auto_syn_hris',
				Division_UpdateTime = sysdate()
			WHERE Division_Name = '{$db->Record['Division_Name']}'
		";
		$count_update += 1;
	}
	
	if ($db_exp->query($div_ori_execute)) {
		echo 'Success <br />';
	} else {
		echo 'Failed <br />';
	}

	$count += 1;
}
echo "<br>";
echo "... Export Data Division into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br /><br /><br />";

//inactive division yang terdapat di MASTER tp tidak terdapat di HRIS
$sqlqry = "	UPDATE M_Division 
			SET Division_InactiveTime = sysdate(), 
				Division_Status = '0'
			WHERE Division_Code NOT IN (SELECT Division_Code_Hcis FROM temp_exp_div) 
			AND Division_InactiveTime IS NULL";
$db->query($sqlqry);

//drop temporary table
$sqlqry = "DROP TABLE IF EXISTS temp_exp_div";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Division Import .. $today <BR><BR><BR>";



$db = new Database;
/* ====================== */
/* UPDATE DATA DEPARTMENT */
/* ====================== */
echo "
=================<br />
DATA DEPARTMENT<br />
=================<br />
";
echo "Start Department Import .. $today <br />";
$query_dept = "
	SELECT 
		TOH.KODE_ORGANIZATION AS DEPARTMENT_CODE, 
		TOH.NAMA_ORGANIZATION AS DEPARTMENT_NAME, 
		TODH.ORGADTCODE AS DEPARTMENT_DIVCODE, 
		TODH.ORGADTNAME AS DEPARTMENT_DIVNAME 
	FROM TAP_DW.TM_ORGANIZATION_HRIS TOH 
	LEFT JOIN TAP_DW.TM_ORGADDGROUP_HRIS TODH ON TOH.ORGANIZATION_ADD_GROUP = TODH.ORGADTNAME
	ORDER BY NAMA_ORGANIZATION ASC
";
$result_dept = select_all_data($con, $query_dept);

//echo '<pre>'; print_r ($result_dept); echo '</pre>';
//die;
$dept_temp_drop = "DROP TABLE IF EXISTS temp_exp_dept";
$db->query($dept_temp_drop);

$dept_temp_create = "CREATE TABLE temp_exp_dept( `Department_Code_Hcis` VARCHAR(15) NOT NULL, `Department_Name` VARCHAR(100) NULL, `Department_DivCode_Hcis` VARCHAR(15) NULL )";
$db->query($dept_temp_create);
$count = 0;
foreach ($result_dept as $k => $v) {
	if ($v['DEPARTMENT_CODE'] != "") {
		$dept_trim = trim($v['DEPARTMENT_CODE']);
		$dept_div_trim = trim($v['DEPARTMENT_DIVCODE']);
		$dept_temp_insert = "INSERT INTO temp_exp_dept (Department_Code_Hcis, Department_Name, Department_DivCode_Hcis) VALUES ('{$dept_trim}', '{$v['DEPARTMENT_NAME']}', '{$dept_div_trim}')";
		if ($db->query($dept_temp_insert)) {
			echo '[ Success ] : ' . $dept_temp_insert . '<br />';
		} else {
			echo '[ Failed ] : ' . $dept_temp_insert . '<br />';
		}
		$count += 1;
	}
}
echo "=== Insert Department : There is $count data insert into temp_exp_dept ===<br /><br />";

$dept_temp_get = "SELECT * FROM temp_exp_dept";
$db->query($dept_temp_get);

$db_exp = new Database;
$count = $count_insert = $count_update = 0;
while ($db->nextRecord()) {
	$dept_ori_get = "SELECT * FROM M_Department WHERE UPPER(Department_Name) = UPPER('{$db->Record['Department_Name']}')";
	$db_exp->query($dept_ori_get);
	$db_exp->singleRecord();
	$dept_name = $db_exp->Record['Department_Name'];

	if (empty($dept_name) || $dept_name == "") {
		echo "### [ Insert Data ] : {$db->Record['Department_Code_Hcis']} | {$db->Record['Department_Name']} | {$db->Record['Department_DivCode_Hcis']} : ";
		$dept_ori_execute = "
			INSERT IGNORE INTO M_Department (
				Department_Code, Department_Name, 
				Department_DivCode, Department_DivCode_Hcis, Department_Status, 
				Department_InsertUser, Department_InsertTime, 
				Department_UpdateUser, Department_UpdateTime, 
				Department_InactiveTime, Department_Code_Hcis
			) VALUES (
				'{$db->Record['Department_Code_Hcis']}', '{$db->Record['Department_Name']}', 
				'{$db->Record['Department_DivCode_Hcis']}', '{$db->Record['Department_DivCode_Hcis']}', '0', 
				'auto_syn_hris', sysdate(), 
				'auto_syn_hris', sysdate(), 
				sysdate(), '{$db->Record['Department_Code_Hcis']}'
			)
		";
		$count_insert += 1;
	} else {
		echo "### [ Update Data ] : {$db->Record['Department_Code_Hcis']} | {$db->Record['Department_Name']} | {$db->Record['Department_DivCode_Hcis']}: ";
		$dept_ori_execute = "
			UPDATE M_Department 
			SET Department_Name = '{$db->Record['Department_Name']}',
				Department_DivCode_Hcis = '{$db->Record['Department_DivCode_Hcis']}',
				Department_Status = '0',
				Department_Code_Hcis = '{$db->Record['Department_Code_Hcis']}',
				Department_UpdateUser = 'auto_syn_hris',
				Department_UpdateTime = sysdate()
			WHERE Department_Name = '{$db->Record['Department_Name']}'
		";
		$count_update += 1;
	}
	
	if ($db_exp->query($dept_ori_execute)) {
		echo 'Success <br />';
	} else {
		echo 'Failed <br />';
	}

	$count += 1;
}
echo "<br>";
echo "... Export Data Department into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br /><br /><br />";

//drop temporary table
$sqlqry = "DROP TABLE temp_exp_dept";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Department Import .. $today <BR><BR><BR>";




$db = new Database;
/* ============================= */
/* UPDATE DATA EMPLOYEE POSITION */
/* ============================= */
echo "
=======================<br />
DATA EMPLOYEE POSITION<br />
=======================<br />
";
echo "Start Employee Position Import .. $today <br />";
$query_job = "SELECT * FROM TAP_DW.TM_JOB";
$result_job = select_all_data($con, $query_job);

//echo '<pre>'; print_r ($result_job); echo '</pre>';
//die;
$job_temp_drop = "DROP TABLE IF EXISTS temp_exp_job";
$db->query($job_temp_drop);

$job_temp_create = "CREATE TABLE temp_exp_job( `Position_Code` VARCHAR(11) NOT NULL, `Position_Name` VARCHAR(100) NULL, `Position_Code_Hcis` VARCHAR(11) NULL )";
$db->query($job_temp_create);
$count = 0;
foreach ($result_job as $k => $v) {
	if ($v['JOB_CODE'] != "") {
		$job_trim = (!empty($v['JOB_TYPE'])) ? trim($v['JOB_TYPE']) : '';
		$job_trim_hcis = (!empty($v['JOB_TYPE_HCIS'])) ? trim($v['JOB_TYPE_HCIS']) : '';
		$job_temp_insert = "INSERT INTO temp_exp_job (Position_Code, Position_Name, Position_Code_Hcis) VALUES ('{$job_trim}', '{$v['JOB_CODE']}', '{$job_trim_hcis}')";
		if ($db->query($job_temp_insert)) {
			echo '[ Success ] : ' . $job_temp_insert . '<br />';
		} else {
			echo '[ Failed ] : ' . $job_temp_insert . '<br />';
		}
		$count += 1;
	}
}
echo "=== Insert Employee Position : There is $count data insert into temp_exp_job ===<br /><br />";

$job_temp_get = "SELECT * FROM temp_exp_job";
$db->query($job_temp_get);

$db_exp = new Database;
$count = $count_insert = $count_update = 0;
while ($db->nextRecord()) {
	$job_ori_get = "SELECT * FROM M_EmployeePosition WHERE UPPER(Position_Name) = UPPER('{$db->Record['Position_Name']}')";
	$db_exp->query($job_ori_get);
	$db_exp->singleRecord();
	$job_name = $db_exp->Record['Position_Name'];

	if (empty($job_name) || $job_name == "") {
		echo "### [ Insert Data ] : {$db->Record['Position_Code']} | {$db->Record['Position_Name']} : ";
		$job_ori_execute = "
			INSERT IGNORE INTO M_EmployeePosition (
				Position_Code, Position_Name, Position_Code_Hcis,
				Position_Status, Position_InsertUser, Position_InsertTime, 
				Position_UpdateUser, Position_UpdateTime, Position_InactiveTime
			) VALUES (
				'{$db->Record['Position_Code_Hcis']}', '{$db->Record['Position_Name']}', '{$db->Record['Position_Code_Hcis']}',
				'0', 'auto_syn_hris', sysdate(),
				'auto_syn_hris', sysdate(), sysdate()
			)
		";
		$count_insert += 1;
	} else {
		echo "### [ Update Data ] : {$db->Record['Position_Code']} | {$db->Record['Position_Name']} : ";
		$job_ori_execute = "
			UPDATE M_EmployeePosition 
			SET Position_Name = '{$db->Record['Position_Name']}',
				Position_Code_Hcis = '{$db->Record['Position_Code_Hcis']}',
				Position_Status = '0',
				Position_UpdateTime = sysdate(),
				Position_UpdateUser = 'auto_syn_hris'
			WHERE Position_Name = '{$db->Record['Position_Name']}'
		";
		$count_update += 1;
	}
	
	if ($db_exp->query($job_ori_execute)) {
		echo 'Success <br />';
	} else {
		echo 'Failed <br />';
	}

	$count += 1;
}
echo "<br>";
echo "... Export Data Employee Position into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br /><br /><br />";

//drop temporary table
$sqlqry = "DROP TABLE temp_exp_job";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Employee Position Import .. $today <BR><BR><BR>";



$db = new Database;
/* ============================= */
/* UPDATE DATA EMPLOYEE POSITION */
/* ============================= */
echo "
===============<br />
DATA EMPLOYEE<br />
===============<br />
";
echo "Start Employee Import .. $today <br />";
$query_emp = "SELECT * FROM TAP_DW.TM_EMPLOYEE_HRIS";
$result_emp = select_all_data($con, $query_emp);

$emp_temp_drop = "DROP TABLE IF EXISTS temp_exp_emp";
$db->query($emp_temp_drop);

$emp_temp_create = "
	CREATE TABLE temp_exp_emp( 
		`Employee_NIK` VARCHAR(20), 
		`Employee_UserName` VARCHAR(50) NULL, 
		`Employee_FullName` VARCHAR(100) NULL, 
		`Employee_Gender` VARCHAR(1) NULL, 
		`Employee_Religion` VARCHAR(15) NULL, 
		`Employee_Birthday` DATE NULL, 
		`Employee_BankCode` VARCHAR(11), 
		`Employee_BankName` VARCHAR(100) NULL, 
		`Employee_BankAccount` VARCHAR(25), 
		`Employee_AccBankName` VARCHAR(100) NULL, 
		`Employee_PositionCode` VARCHAR(11) NULL, 
		`Employee_PositionCode_Hcis` VARCHAR(11) NULL, 
		`Employee_Position` VARCHAR(100) NULL, 
		`Employee_GradeCode` VARCHAR(11) NULL, 
		`Employee_Grade` VARCHAR(100) NULL, 
		`Employee_Level` VARCHAR(5) NULL, 
		`Employee_DeptCode` VARCHAR(15) NULL, 
		`Employee_DeptCode_Hcis` VARCHAR(15) NULL, 
		`Employee_Department` VARCHAR(100) NULL, 
		`Employee_DivCode` VARCHAR(11) NULL, 
		`Employee_DivCode_Hcis` VARCHAR(11) NULL, 
		`Employee_Division` VARCHAR(100) NULL, 
		`Employee_CompanyCode` VARCHAR(11) NULL, 
		`Employee_LocationCode` VARCHAR(15) NULL, 
		`Employee_LocationCode_Hcis` VARCHAR(15) NULL, 
		`Employee_Location` VARCHAR(50) NULL, 
		`Employee_Email` VARCHAR(100) NULL, 
		`Employee_SpvNIK` VARCHAR(10) NULL, 
		`Employee_Spv` VARCHAR(100) NULL, 
		`Employee_JoinDate` DATE NULL, 
		`Employee_ResignDate` DATE NULL, 
		`Employee_InsertUser` VARCHAR(25) NULL, 
		`Employee_InsertTime` DATETIME NULL, 
		`Employee_UpdateUser` VARCHAR(25) NULL, 
		`Employee_UpdateTime` DATETIME NULL
	)
";
$db->query($emp_temp_create);
$count = 0;

$mon = array('JAN' => '01' , 'FEB' => '02', 'MAR' => '03', 'APR' => '04', 'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12');

foreach ($result_emp as $k => $v) {
	$username = (!empty($v['EMPLOYEE_USERNAME'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_USERNAME'])."'" : "null";
	$fullname = (!empty($v['EMPLOYEE_FULLNAME'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_FULLNAME'])."'" : "null";
	$gender = (!empty($v['EMPLOYEE_GENDER'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_GENDER'])."'" : "null";
	$religion = (!empty($v['EMPLOYEE_RELIGION'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_RELIGION'])."'" : "null";

	if (!empty($v['EMPLOYEE_BIRTHDAY'])) {
		$exp_birth = explode('-', $v['EMPLOYEE_BIRTHDAY']);

		$result_birth = substr($v['EMPLOYEE_BIRTHDAY'], -2, 2);
		$year_birth = '20' . $result_birth;
		if ($year_birth > date('Y')) {
			$year_birth = $year_birth - 100;
		}
		$birth = "'" . $year_birth . '-' . $mon[$exp_birth[1]] . '-' . $exp_birth[0] . "'";
	} else {
		$birth = "null";
	}

	$bankcode = (!empty($v['EMPLOYEE_BANKCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_BANKCODE'])."'" : "''";
	$bankname = (!empty($v['EMPLOYEE_BANKNAME'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_BANKNAME'])."'" : "null";
	$bankaccount = (!empty($v['EMPLOYEE_BANKACCOUNT'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_BANKACCOUNT'])."'" : "''";
	$accbank = (!empty($v['EMPLOYEE_ACCBANKNAME'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_ACCBANKNAME'])."'" : "null";
	$poscode = (!empty($v['EMPLOYEE_POSITIONCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_POSITIONCODE'])."'" : "null";
	$position = (!empty($v['EMPLOYEE_POSITION'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_POSITION'])."'" : "null";
	$gradecode = (!empty($v['EMPLOYEE_GRADECODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_GRADECODE'])."'" : "null";
	$grade = (!empty($v['EMPLOYEE_GRADE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_GRADE'])."'" : "null";
	$level = (!empty($v['EMPLOYEE_LEVEL'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_LEVEL'])."'" : "null";
	$deptcode = (!empty($v['EMPLOYEE_DEPTCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DEPTCODE'])."'" : "null";
	$dept = (!empty($v['EMPLOYEE_DEPARTMENT'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DEPARTMENT'])."'" : "null";
	$divcode = (!empty($v['EMPLOYEE_DIVCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DIVCODE'])."'" : "null";
	$div = (!empty($v['EMPLOYEE_DIVISION'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DIVISION'])."'" : "null";
	$comp = (!empty($v['EMPLOYEE_COMPANYCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_COMPANYCODE'])."'" : "null";
	$loccode = (!empty($v['EMPLOYEE_LOCATIONCODE'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_LOCATIONCODE'])."'" : "null";
	$location = (!empty($v['EMPLOYEE_LOCATION'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_LOCATION'])."'" : "null";
	//$email = (!empty($v['EMPLOYEE_EMAIL'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_EMAIL'])."'" : "null";
	$spvnik = (!empty($v['EMPLOYEE_SPVNIK'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_SPVNIK'])."'" : "null";
	$spv = (!empty($v['EMPLOYEE_SPV'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_SPV'])."'" : "null";
	$email = "'arief.fahrizon@tap-agri.co.id'";
	
	if (!empty($v['EMPLOYEE_JOINDATE'])) {
		$exp_join = explode('-', $v['EMPLOYEE_JOINDATE']);

		$result_join = substr($v['EMPLOYEE_JOINDATE'], -2, 2);
		$year_join = '20' . $result_join;
		if ($year_join > date('Y')) {
			$year_join = $year_join - 100;
		}
		$join = "'" . $year_join . '-' . $mon[$exp_join[1]] . '-' . $exp_join[0] . "'";
	} else {
		$join = "null";
	}

	if (!empty($v['EMPLOYEE_RESIGNDATE'])) {
		$exp_resign = explode('-', $v['EMPLOYEE_RESIGNDATE']);

		$result_resign = substr($v['EMPLOYEE_RESIGNDATE'], -2, 2);
		$year_resign = '20' . $result_resign;
		if ($year_resign > date('Y')) {
			$year_resign = $year_resign - 100;
		}
		$resign = "'" . $year_resign . '-' . $mon[$exp_resign[1]] . '-' . $exp_resign[0] ."'";
	} else {
		$resign = "null";
	}

	$poscode_hcis = (!empty($v['EMPLOYEE_POSITIONCODE_HCIS'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_POSITIONCODE_HCIS'])."'" : "null";
	$deptcode_hcis = (!empty($v['EMPLOYEE_DEPTCODE_HCIS'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DEPTCODE_HCIS'])."'" : "null";
	$divcode_hcis = (!empty($v['EMPLOYEE_DIVCODE_HCIS'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_DIVCODE_HCIS'])."'" : "null";
	$loccode_hcis = (!empty($v['EMPLOYEE_LOCATIONCODE_HCIS'])) ? "'".str_replace("'", "\'", $v['EMPLOYEE_LOCATIONCODE_HCIS'])."'" : "null";

	$emp_temp_insert = "INSERT INTO temp_exp_emp (
		Employee_NIK, 
		Employee_UserName, 
		Employee_FullName, 
		Employee_Gender, 
		Employee_Religion, 
		Employee_Birthday, 
		Employee_BankCode, 
		Employee_BankName, 
		Employee_BankAccount, 
		Employee_AccBankName, 
		Employee_PositionCode, 
		Employee_PositionCode_Hcis,
		Employee_Position, 
		Employee_GradeCode, 
		Employee_Grade, 
		Employee_Level, 
		Employee_DeptCode, 
		Employee_DeptCode_Hcis,
		Employee_Department, 
		Employee_DivCode, 
		Employee_DivCode_Hcis, 
		Employee_Division, 
		Employee_CompanyCode, 
		Employee_LocationCode, 
		Employee_LocationCode_Hcis, 
		Employee_Location, 
		Employee_Email, 
		Employee_SpvNIK, 
		Employee_Spv, 
		Employee_JoinDate, 
		Employee_ResignDate, 
		Employee_InsertUser, 
		Employee_InsertTime, 
		Employee_UpdateUser, 
		Employee_UpdateTime
	) VALUES (
		'{$v['EMPLOYEE_NIK']}', 
        {$username},
        {$fullname},
        {$gender},
        {$religion},
        {$birth},
        {$bankcode},
        {$bankname},
        {$bankaccount},
        {$accbank},
        {$poscode},
        {$poscode_hcis},
        {$position},
        {$gradecode},
        {$grade},
        {$level},
        {$deptcode},
        {$deptcode_hcis},
        {$dept},
        {$divcode},
        {$divcode_hcis},
        {$div},
        {$comp},
        {$loccode},
        {$loccode_hcis},
        {$location},
        {$email},
        {$spvnik},
        {$spv},
        {$join},
        {$resign},
        'auto_syn_hris',
        sysdate(),
        'auto_syn_hris',
        sysdate()
	)";

	if ($db->query($emp_temp_insert)) {
		echo '[ Success ] : ' . $emp_temp_insert . '<br />';
	} else {
		echo '[ Failed ] : ' . $emp_temp_insert . '<br />';
	}
	$count += 1;
}
echo "=== Insert Employee : There is $count data insert into temp_exp_emp ===<br /><br />";

$emp_temp_get = "SELECT * FROM temp_exp_emp WHERE Employee_NIK NOT IN ('00009991', '00009992', '00009993') AND Employee_NIK NOT LIKE 'PTPA%' ORDER BY Employee_NIK ASC";
$db->query($emp_temp_get);

$db_exp = new Database;
$count = $count_insert = $count_update = 0;

while ($db->nextRecord()) {
	$nik = (!empty($db->Record['Employee_NIK'])) ? "'".str_replace("'", "\'", $db->Record['Employee_NIK'])."'" : "null";
	$username = (!empty($db->Record['Employee_UserName'])) ? "'".str_replace("'", "\'", $db->Record['Employee_UserName'])."'" : "null";
	$fullname = (!empty($db->Record['Employee_FullName'])) ? "'".str_replace("'", "\'", $db->Record['Employee_FullName'])."'" : "null";
	$gender = (!empty($db->Record['Employee_Gender'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Gender'])."'" : "null";
	$religion = (!empty($db->Record['Employee_Religion'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Religion'])."'" : "null";
	$birth = (!empty($db->Record['Employee_Birthday'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Birthday'])."'" : "null";
	$bankcode = (!empty($db->Record['Employee_BankCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_BankCode'])."'" : "''";
	$bankname = (!empty($db->Record['Employee_BankName'])) ? "'".str_replace("'", "\'", $db->Record['Employee_BankName'])."'" : "null";
	$bankaccount = (!empty($db->Record['Employee_BankAccount'])) ? "'".str_replace("'", "\'", $db->Record['Employee_BankAccount'])."'" : "''";
	$accbank = (!empty($db->Record['Employee_AccBankName'])) ? "'".str_replace("'", "\'", $db->Record['Employee_AccBankName'])."'" : "null";
	$poscode = (!empty($db->Record['Employee_PositionCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_PositionCode'])."'" : "null";
	$poscode_hcis = (!empty($db->Record['Employee_PositionCode_Hcis'])) ? "'".str_replace("'", "\'", $db->Record['Employee_PositionCode_Hcis'])."'" : "null";
	$position = (!empty($db->Record['Employee_Position'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Position'])."'" : "null";
	$gradecode = (!empty($db->Record['Employee_GradeCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_GradeCode'])."'" : "null";
	$grade = (!empty($db->Record['Employee_Grade'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Grade'])."'" : "null";
	$level = (!empty($db->Record['Employee_Level'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Level'])."'" : "null";
	$deptcode = (!empty($db->Record['Employee_DeptCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_DeptCode'])."'" : "null";
	$deptcode_hcis = (!empty($db->Record['Employee_DeptCode_Hcis'])) ? "'".str_replace("'", "\'", $db->Record['Employee_DeptCode_Hcis'])."'" : "null";
	$dept = (!empty($db->Record['Employee_Department'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Department'])."'" : "null";
	$divcode = (!empty($db->Record['Employee_DivCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_DivCode'])."'" : "null";
	$divcode_hcis = (!empty($db->Record['Employee_DivCode_Hcis'])) ? "'".str_replace("'", "\'", $db->Record['Employee_DivCode_Hcis'])."'" : "null";
	$div = (!empty($db->Record['Employee_Division'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Division'])."'" : "null";
	$comp = (!empty($db->Record['Employee_CompanyCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_CompanyCode'])."'" : "null";
	$loccode = (!empty($db->Record['Employee_LocationCode'])) ? "'".str_replace("'", "\'", $db->Record['Employee_LocationCode'])."'" : "null";
	$loccode_hcis = (!empty($db->Record['Employee_LocationCode_Hcis'])) ? "'".str_replace("'", "\'", $db->Record['Employee_LocationCode_Hcis'])."'" : "null";
	$location = (!empty($db->Record['Employee_Location'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Location'])."'" : "null";
	//$email = (!empty($db->Record['Employee_Email'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Email'])."'" : "null";
	$spvnik = (!empty($db->Record['Employee_SpvNIK'])) ? "'".str_replace("'", "\'", $db->Record['Employee_SpvNIK'])."'" : "null";
	$spv = (!empty($db->Record['Employee_Spv'])) ? "'".str_replace("'", "\'", $db->Record['Employee_Spv'])."'" : "null";
	$join  = (!empty($db->Record['Employee_JoinDate'])) ? "'".str_replace("'", "\'", $db->Record['Employee_JoinDate'])."'" : "null";
	$resign = (!empty($db->Record['Employee_ResignDate'])) ? "'".str_replace("'", "\'", $db->Record['Employee_ResignDate'])."'" : "null";
	$email = "'arief.fahrizon@tap-agri.co.id'";
	
	
	$emp_ori_get = "SELECT * FROM M_Employee WHERE Employee_NIK = {$nik}";
	$db_exp->query($emp_ori_get);
	$db_exp->singleRecord();
	$emp_nik = $db_exp->Record['Employee_NIK'];

	if (empty($emp_nik) || $emp_nik == "") {
		echo "### [ Insert Data ] : {$nik} | {$username} | {$fullname} : ";
		$emp_ori_execute = "INSERT INTO M_Employee (
			Employee_NIK, 
			Employee_UserName, 
			Employee_FullName, 
			Employee_Gender, 
			Employee_Religion, 
			Employee_Birthday, 
			Employee_BankCode, 
			Employee_BankName, 
			Employee_BankAccount, 
			Employee_AccBankName, 
			Employee_PositionCode, 
			Employee_PositionCode_Hcis,
			Employee_Position, 
			Employee_GradeCode, 
			Employee_Grade, 
			Employee_Level, 
			Employee_DeptCode, 
			Employee_DeptCode_Hcis,
			Employee_Department, 
			Employee_DivCode, 
			Employee_DivCode_Hcis,
			Employee_Division, 
			Employee_CompanyCode, 
			Employee_LocationCode, 
			Employee_LocationCode_Hcis,
			Employee_Location, 
			Employee_Email, 
			Employee_SpvNIK, 
			Employee_Spv, 
			Employee_JoinDate, 
			Employee_ResignDate, 
			Employee_InsertUser, 
			Employee_InsertTime, 
			Employee_UpdateUser, 
			Employee_UpdateTime
		) VALUES (
			{$nik}, 
	        {$username},
	        {$fullname},
	        {$gender},
	        {$religion},
	        {$birth},
	        {$bankcode},
	        {$bankname},
	        {$bankaccount},
	        {$accbank},
	        {$poscode},
	        {$poscode_hcis},
	        {$position},
	        {$gradecode},
	        {$grade},
	        {$level},
	        {$deptcode},
	        {$deptcode_hcis},
	        {$dept},
	        {$divcode},
	        {$divcode_hcis},
	        {$div},
	        {$comp},
	        {$loccode},
	        {$loccode_hcis},
	        {$location},
	        {$email},
	        {$spvnik},
	        {$spv},
	        {$join},
	        {$resign},
	        'auto_syn_hris',
	        sysdate(),
	        'auto_syn_hris',
	        sysdate()
		)";
		$count_insert += 1;
	} else {
		echo "### [ Update Data ] : {$nik} | {$username} | {$fullname} : ";
		$emp_ori_execute = "
			UPDATE M_Employee 
			SET Employee_NIK = {$nik},
				Employee_UserName = {$username},
				Employee_FullName = {$fullname},
				Employee_Gender = {$gender},
				Employee_Religion = {$religion},
				Employee_Birthday = {$birth},
				Employee_BankCode = {$bankcode},
				Employee_BankName = {$bankname},
				Employee_BankAccount = {$bankaccount},
				Employee_AccBankName = {$accbank},
				Employee_PositionCode = {$poscode},
				Employee_PositionCode_Hcis = {$poscode_hcis},
				Employee_Position = {$position},
				Employee_GradeCode = {$gradecode},
				Employee_Grade = {$grade},
				Employee_Level = {$level},
				Employee_DeptCode = {$deptcode},
				Employee_DeptCode_Hcis = {$deptcode_hcis},
				Employee_Department = {$dept},
				Employee_DivCode = {$divcode},
				Employee_DivCode_Hcis = {$divcode_hcis},
				Employee_Division = {$div},
				Employee_CompanyCode = {$comp},
				Employee_LocationCode = {$loccode},
				Employee_LocationCode_Hcis = {$loccode_hcis},
				Employee_Location = {$location},
				Employee_Email = {$email},
				Employee_SpvNIK = {$spvnik},
				Employee_Spv = {$spv},
				Employee_JoinDate = {$join},
				Employee_ResignDate = {$resign},
				Employee_UpdateUser = 'auto_syn_hris',
				Employee_UpdateTime = sysdate()
			WHERE Employee_NIK = {$nik}
		";
		$count_update += 1;
	}
	
	if ($db_exp->query($emp_ori_execute)) {
		echo 'Success <br />';
	} else {
		echo 'Failed <br />';
	}

	$count += 1;
}
echo "<br>";
echo "... Export Data Employee into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br /><br /><br />";


//drop temporary table
$sqlqry = "DROP TABLE temp_exp_emp";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Employee Import .. $today <BR><BR><BR>";

echo "**********<br>";
echo "Sinkronisasi Divisi, Departemen, Jabatan, Karyawan DONE.. $today <br><BR><BR>";

//UPDATE DATA APPROVER PAK YUKY KE PAK DONO
/*echo "Start Update Approval Custodian .. $today <BR>";
$sqlqry = "UPDATE custodian.M_User
		   SET User_SPV1 = '00000144'
		   WHERE User_SPV1 = '00000016'";
$db->query($sqlqry);

$today = date("D M j G:i:s T Y");
echo "Update Approval Custodian DONE .. $today <BR><BR><BR>";

// Close MS SQL Connection
odbc_close($connection);*/
?>