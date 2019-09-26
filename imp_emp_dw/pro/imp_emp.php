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

<?PHP
$vLocation = "C:/xampp/htdocs/imp_emp_dw/pro";
include($vLocation."/class.db_connection.php");

//koneksi ke HRIS
$server="10.20.1.55";
$database = "Human_Capital";
$user = "adminspd";
$password = "adminspd";
$connection = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password);

/* ================== */
/* UPDATE DATA DIVISI */
/* ================== */
$today = date("D M j G:i:s T Y");
echo "Start Division Import .. $today <br>";
$qry = "SELECT * FROM dbo.V_PDMsOrgAddGroup";
$result = odbc_exec($connection,$qry);
echo "$qry<br>";
while ($data[] = odbc_fetch_array($result));
odbc_free_result($result);
$count = 0;
$db = new Database;

// insert data divisi dari HRIS ke temp
$sqlqry = "DROP TABLE IF EXISTS temp_exp_division" ;
$db->query($sqlqry); 

$sqlqry = "CREATE TABLE temp_exp_division ( `Div_Code` VARCHAR(20) NOT NULL,
														   `Div_Name` VARCHAR(55) NULL )";

$db->query($sqlqry);
for ($i=0; $i<count($data); $i++) {
	if ($data[$i]['OrgAdtCode'] != "") {
		$sqlqry = "INSERT INTO temp_exp_division (Div_Code, Div_Name) 
				   VALUES ('". $data[$i]['OrgAdtCode'] ."','". $data[$i]['OrgAdtName'] ."')";
		$db->query($sqlqry);
		$count += 1;
	}
}
echo "There is $count data insert into temp table ... <br>"; 

// sinkronisasi data
$sqlqry = "SELECT * FROM temp_exp_division";
$db->query($sqlqry);
$db_exp = new Database;
$count = 0;
$count_insert = 0;
$count_update = 0;

while($db->nextRecord()) {
	$sqlqry = "SELECT * FROM M_Division WHERE Division_Code = '". $db->Record['Div_Code'] ."'";
	$db_exp->query($sqlqry);
	$db_exp->singleRecord();
	$div_name = $db_exp->Record['Division_Name'];

	// insert data yang tidak terdapat di M_Division
	if ($div_name == "") {
		echo "## Insert Data : ".$db->Record['Div_Code']." | ".$db->Record['Div_Name']."<br>";
		
		$sqlqry = "INSERT INTO M_Division (Division_Code, Division_Name, Division_Status,
					Division_InsertUser, Division_InsertTime, Division_UpdateUser, Division_UpdateTime)
					VALUES ('". $db->Record['Div_Code'] ."',
							'". $db->Record['Div_Name'] ."',
							'1',
							'auto_syn_hris',
							sysdate(),
							'auto_syn_hris',
							sysdate())";
		$count_insert += 1;
	} 
	// update data yang telah terdapat di M_Division
	else {
		$sqlqry = "UPDATE M_Division  
				   SET Division_Name = '". $db->Record['Div_Name'] ."', 
					   Division_UpdateUser = 'auto_syn_hris', 
					   Division_UpdateTime = sysdate() 
				   WHERE Division_Code = '". $db->Record['Div_Code'] ."'";	
		$count_update += 1;
	}
	$db_exp->query($sqlqry);
	echo "## Insert Data : ".$db->Record['Div_Code']." | ".$db->Record['Div_Name']."---".$count."-".$count_update."-".$count_insert."<br>";
	$count += 1;
}
echo "<br>";
echo "... Export Data Division into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br>";

//inactive division yang terdapat di MASTER tp tidak terdapat di HRIS
$sqlqry = "	UPDATE M_Division
			SET Division_InactiveTime = sysdate(), 
				Division_Status = '0'
			WHERE Division_Code NOT IN (SELECT Div_Code FROM temp_exp_division) 
			AND Division_InactiveTime IS NULL";
$db->query($sqlqry);

//drop temporary table
$sqlqry = "DROP TABLE IF EXISTS temp_exp_division";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Division Import .. $today <BR><BR><BR>";

/* ====================== */
/* UPDATE DATA DEPARTEMEN */
/* ====================== */
echo "**********<br>";
$today = date("D M j G:i:s T Y");
echo "Start Departemen Import .. $today <br>";

unset($data);
$qry = "SELECT * FROM dbo.V_PDMsOrganization";
$result = odbc_exec($connection,$qry);
echo "$qry<br>";
while ($data[] = odbc_fetch_array($result));
odbc_free_result($result);

$sqlqry = "DROP TABLE IF EXISTS temp_exp_dep" ;
$db->query($sqlqry);

$count = 0;
// insert data departemen dari HRIS ke temp
$sqlqry = "CREATE  TABLE temp_exp_dep ( `Dep_Code` VARCHAR(20) NOT NULL,
													   `Dep_Name` VARCHAR(55) NULL,
													   `Dep_Div_Name` VARCHAR(55) NULL,
													   `Dep_Div_Code` VARCHAR(55) NULL )";

$db->query($sqlqry);
for ($i=0; $i<count($data); $i++) { 
	if ($data[$i]['Kode_organization'] != "") {
		// cari kode divisi berdasarkan divisi yang terdapat di MASTER
		$Division_Code = "";
		if ($data[$i]['Organization_Add_Group'] != "") {
			$sqlqry = "SELECT Division_Code FROM M_Division 
						WHERE UPPER(Division_Name) = UPPER('". $data[$i]['Organization_Add_Group'] ."')";
			$db->query($sqlqry);
			$db->singleRecord();
			$Division_Code = $db->Record['Division_Code'];
		}
		if ($Division_Code == "") 
			$Division_Code = "null";
		else
			$Division_Code = "'". $Division_Code ."'";
		
		
			
		$sqlqry = "INSERT INTO temp_exp_dep (Dep_Code, 
															Dep_Name, 
															Dep_Div_Name, 
															Dep_Div_Code) 
					VALUES ('". $data[$i]['Kode_organization'] ."',
							'". $data[$i]['Nama_Organization'] ."',
							'". $data[$i]['Organization_Add_Group'] ."', 
							". $Division_Code .")";
		$db->query($sqlqry);
		$count += 1;
	}
}
echo "There is $count data insert into temp table ... <br>"; 

// sinkronisasi data departemen
$sqlqry = "SELECT * FROM temp_exp_dep";
$db->query($sqlqry);
$db_exp = new Database;
$count = 0;
$count_insert = 0;
$count_update = 0;
while($db->nextRecord()) {
	$sqlqry = "SELECT * FROM M_Department WHERE Department_Code = '". $db->Record['Dep_Code'] ."'";
	$db_exp->query($sqlqry);
	$db_exp->singleRecord();
	$Department_Name = $db_exp->Record['Department_Name'];
	
	//insert data yang tidak terdapat di M_Department
	if ($Department_Name == "") {
		echo "## Insert Data : ".$db->Record['Dep_Code']." | ".$db->Record['Dep_Name']."<br>";
		
		$sqlqry = "INSERT INTO M_Department (Department_Code, 
															Department_Name, 
															Department_DivCode, 
															Department_Status,
															Department_InsertUser, 
															Department_InsertTime, 
															Department_UpdateUser, 
															Department_UpdateTime)
					VALUES ('". $db->Record['Dep_Code'] ."',
							'". $db->Record['Dep_Name'] ."',
							'". $db->Record['Dep_Div_Code'] ."', 
							'1',
							'auto_syn_hris',
							sysdate(),
							'auto_syn_hris',
							sysdate())";
		$count_insert += 1;
	} 
	//update data yang telah terdapat di M_Department
	else {
		$Division_Code = "";
		if ($db->Record['Dep_Div_Code'] == "")
			$Division_Code = "null";
		else
			$Division_Code = "'". $db->Record['Dep_Div_Code'] ."'";
		$sqlqry = "UPDATE M_Department 
				   SET Department_Name = '". $db->Record['Dep_Name'] ."', 
					   Department_DivCode = ". $Division_Code .", 
					   Department_UpdateUser = 'auto_syn_hris', 
					   Department_UpdateTime = sysdate() 
				   WHERE Department_Code = '". $db->Record['Dep_Code'] ."'";	
		$count_update += 1;
	}
	$db_exp->query($sqlqry);
	$count += 1;
}
echo "<br>";
echo "... Export Data Departemen into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br>";

//inactive department yang terdapat di MASTER tp tidak terdapat di HRIS
$sqlqry = "UPDATE M_Department 
		   SET Department_InactiveTime = sysdate(), 
			   Department_Status='0' 
		   WHERE Department_Code NOT IN (SELECT Dep_Code FROM temp_exp_dep) 
		   AND Department_InactiveTime IS NULL";
$db->query($sqlqry);

//drop temporary table
$sqlqry = "DROP TABLE temp_exp_dep";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Department Import .. $today <BR><BR><BR>";

/* ============================ */
/* UPDATE DATA JABATAN KARYAWAN */
/* ============================ */
echo "**********<br>";
$today = date("D M j G:i:s T Y");
echo "Start Jabatan Import .. $today <br>";
$qry = "SELECT * FROM dbo.V_PDMsJobTitle";
$result = odbc_exec($connection,$qry);
echo "$qry<br>";
while ($data[] = odbc_fetch_array($result));
odbc_free_result($result);
$count = 0;
$db = new Database;

// insert data jabatan karyawan dari HRIS ke temp
$sqlqry = "DROP TABLE IF EXISTS temp_exp_position" ;
$db->query($sqlqry); 

$sqlqry = "CREATE  TABLE temp_exp_position ( `Position_Code` VARCHAR(20) NOT NULL,
															`Position_Name` VARCHAR(55) NULL )";
$db->query($sqlqry);
for ($i=0; $i<count($data); $i++) {
	if ($data[$i]['Nama_JobTitle'] != "") {
		$sqlqry = "INSERT INTO temp_exp_position (Position_Code, Position_Name) 
				   VALUES ('". $data[$i]['Kode_jobTitle'] ."','". $data[$i]['Nama_JobTitle'] ."')";
		$db->query($sqlqry);
		$count += 1;
	}
}
echo "There is $count data insert into temp table ... <br>"; 

// sinkronisasi data
$sqlqry = "SELECT * FROM temp_exp_position";
$db->query($sqlqry);
$db_exp = new Database;
$count = 0;
$count_insert = 0;
$count_update = 0;
while($db->nextRecord()) {
	$sqlqry = "SELECT * FROM M_EmployeePosition WHERE Position_Code = '". $db->Record['Position_Code'] ."'";
	$db_exp->query($sqlqry);
	$db_exp->singleRecord();
	$Position_Code = $db_exp->Record['Position_Code'];
	
	//insert data yang tidak terdapat di M_EmployeePosition
	if ($Position_Code == "") {
		echo "## Insert Data : ".$db->Record['Position_Code']." | ".$db->Record['Position_Name']."<br>";
		
		$sqlqry = "INSERT INTO M_EmployeePosition (Position_Code, 
																  Position_Name, 
																  Position_Status,
																  Position_InsertUser, 
																  Position_InsertTime,
																  Position_UpdateUser, 
																  Position_UpdateTime)
					VALUES ('". $db->Record['Position_Code'] ."',
							'". $db->Record['Position_Name'] ."',
							'1',
							'auto_syn_hris',
							sysdate(),
							'auto_syn_hris',
							sysdate())";
		$count_insert += 1;			
	} 
	//update data yang telah terdapat di M_EmployeePosition
	else {
		$sqlqry = "UPDATE M_EmployeePosition 
				   SET Position_Name = '". $db->Record['Position_Name'] ."', 
				       Position_UpdateUser = 'auto_syn_hris', 
					   Position_UpdateTime = sysdate() 
				   WHERE Position_Code = '". $db->Record['Position_Code'] ."'";	
		$count_update += 1;
	}
	$db_exp->query($sqlqry);
	$count += 1;
}
echo "<br>";
echo "... Export Data Position into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br>";

//inactive jabatan karyawan yang terdapat di MASTER tp tidak terdapat di HRIS
$sqlqry = "UPDATE M_EmployeePosition 
		   SET Position_InactiveTime = sysdate(), 
			   Position_Status = '0'
		   WHERE Position_Code NOT IN (SELECT Position_Code FROM temp_exp_position) 
		   AND Position_InactiveTime IS NULL";
$db->query($sqlqry);

//Drop temporary TABLE
$sqlqry = "DROP TABLE IF EXISTS temp_exp_position";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Position Import .. $today <BR><BR><BR>";


/* ==================== */
/* UPDATE DATA KARYAWAN */
/* ==================== */
echo "**********<br>";
$today = date("D M j G:i:s T Y");
echo "Start Karyawan Import .. $today <br>";
unset($data);
$qry = "SELECT * FROM dbo.V_PDMsKaryawan";
$result = odbc_exec($connection,$qry);
echo "$qry<br>";
while ($data[] = odbc_fetch_array($result));
odbc_free_result($result);

$sqlqry = "DROP TABLE IF EXISTS temp_exp_emp" ;
$db->query($sqlqry);

$count = 0;
// insert data karyawan dari HRIS ke temp
$sqlqry = "CREATE  TABLE temp_exp_emp ( `NIK` VARCHAR(20) NOT NULL,
													   `Nama_Karyawan` VARCHAR(150) NULL,
													   `Gender` VARCHAR(20) NULL,
													   `Agama` VARCHAR(20) NULL ,
													   `Tanggal_Lahir` VARCHAR(50) NULL,
													   `Kode_Bank` VARCHAR(50) NULL,
													   `Nama_Bank` VARCHAR(50) NULL,
													   `Rekening_Bank` VARCHAR(50) NULL,
													   `Nama_Rekening` VARCHAR(50) NULL,
													   `Kode_Jabatan` VARCHAR(55) NULL,
													   `Jabatan` VARCHAR(55) NULL,
													   `Kode_Pangkat` VARCHAR(55) NULL,
													   `Nama_Pangkat` VARCHAR(55) NULL,
													   `Golongan` VARCHAR(10) NULL,	
													   `Kode_Departemen` VARCHAR(55) NULL,
													   `Departemen` VARCHAR(55) NULL,	
													   `Kode_Divisi` VARCHAR(55) NULL,
													   `Divisi` VARCHAR(55) NULL,
													   `Company` VARCHAR(55) NULL,
													   `Kode_Lokasi` VARCHAR(55) NULL,
													   `Lokasi` VARCHAR(55) NULL,
													   `Email_Address` VARCHAR(150) NULL,
													   `Atasan_Langsung` VARCHAR(150) NULL,
													   `Spv_ID` VARCHAR(10) NULL,
													   `Tanggal_Masuk` VARCHAR(30) NULL,
													   `Tanggal_Keluar` VARCHAR(30) NULL)"; 
$db->query($sqlqry);
for ($i=0; $i<count($data); $i++) { 
	if ($data[$i]['NIK'] != "") {
		$sqlqry = "INSERT INTO temp_exp_emp (NIK, 
											Nama_Karyawan, 
											Gender, 
											Agama, 
											Tanggal_Lahir, 
											Kode_Bank, 
											Nama_Bank,
											Rekening_Bank,
											Nama_Rekening, 
											Kode_Jabatan, 
											Jabatan, 
											Kode_Pangkat, 
											Nama_Pangkat, 
											Golongan, 
											Kode_Departemen, 
											Departemen, 
											Kode_Divisi, 
											Divisi, 
											Company,
											Kode_Lokasi, 
											Lokasi, 
											Email_Address, 
											Atasan_Langsung, 
											Spv_ID, 
											Tanggal_Masuk, 
											Tanggal_Keluar) 
					VALUES ('". $data[$i]['NIK'] ."',
							'". str_replace("'", "", $data[$i]['Nama_Karyawan']) ."',
							'". $data[$i]['Gender'] ."',
							'". $data[$i]['Agama'] ."', 
							'". $data[$i]['Tanggal_Lahir'] ."',
							'". $data[$i]['Kode_Bank'] ."',
							'". $data[$i]['Nama_Bank'] ."',
							'". $data[$i]['Rekening_Bank'] ."', 
							'". str_replace("'", "", $data[$i]['Nama_Rekening']) ."',
							'". $data[$i]['Kode_Jabatan'] ."',
							'". $data[$i]['Jabatan'] ."',
							'". $data[$i]['Kode_Pangkat'] ."',
							'". $data[$i]['Nama_Pangkat'] ."', 
							'". $data[$i]['Golongan'] ."',
							'". $data[$i]['Kode_Departemen'] ."',
							'". $data[$i]['Departemen'] ."',
							'". $data[$i]['Kode_Divisi'] ."',
							'". $data[$i]['Divisi'] ."',
							'". $data[$i]['Company'] ."', 
							'". $data[$i]['Kode_Lokasi'] ."', 
							'". $data[$i]['Lokasi'] ."',
							'". str_replace("'", "", $data[$i]['Email_Address']) ."',
							'". str_replace("'", "", $data[$i]['Atasan_Langsung'])."', 
							'". $data[$i]['NIK_Atasa_Langsung'] ."',
							'". $data[$i]['Tanggal_Masuk'] ."',	
							'". $data[$i]['Tanggal_Keluar'] ."')";
		$db->query($sqlqry);
		$count += 1;
	}
}
echo "There is $count data insert into temp table ... <br>"; 

// sinkronisasi data
$sqlqry = "SELECT * FROM temp_exp_emp";
$db->query($sqlqry);
$db_exp = new Database;
$count = 0;
$count_insert = 0;
$count_update = 0;
while($db->nextRecord()) {
	//set inactive date
	$inactive_date = "";
	if ($db->Record['Tanggal_Keluar'])
		$inactive_date = "'". date('Y-m-d', strtotime($db->Record['Tanggal_Keluar'])) ."'";
	else
		$inactive_date = "null";
	
	$sqlqry = "SELECT * 
			   FROM M_Employee 
			   WHERE Employee_NIK = '". $db->Record['NIK'] ."'";
	$db_exp->query($sqlqry);
	$db_exp->singleRecord();
	$emp_nik = $db_exp->Record['Employee_NIK'];	
	//echo $sqlqry." | ".$emp_nik."<BR>";
	
	//insert data yang tidak terdapat di M_Employee
	if ($emp_nik=="") {
		//set username (default adalah email)
		$get_email=explode("@", $db->Record['Email_Address']);
		$username_karyawan=$get_email[0];
		
		echo "## Insert Data : ".$db->Record['NIK']." | ".$db->Record['Nama_Karyawan']."<br>";
		
		$sqlqry = "INSERT INTO M_Employee (Employee_NIK, 
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
														  Employee_Position,
														  Employee_GradeCode, 
														  Employee_Grade, 
														  Employee_Level, 
														  Employee_DeptCode, 
														  Employee_Department,
														  Employee_DivCode, 
														  Employee_Division, 
														  Employee_CompanyCode, 
														  Employee_LocationCode, 
														  Employee_Location,
														  Employee_Email, 
														  Employee_SpvNIK, 
														  Employee_Spv, 
														  Employee_JoinDate, 
														  Employee_ResignDate,
														  Employee_InsertUser, 
														  Employee_InsertTime, 
														  Employee_UpdateUser, 
														  Employee_UpdateTime)
					VALUES ('". $db->Record['NIK'] ."',
							'".$username_karyawan."',
							'". $db->Record['Nama_Karyawan'] ."', 
							'". $db->Record['Gender'] ."',
							'". $db->Record['Agama'] ."', 
							'". $db->Record['Tanggal_Lahir'] ."', 
							'". $db->Record['Kode_Bank'] ."',
							'". $db->Record['Nama_Bank'] ."', 
							'". $db->Record['Rekening_Bank'] ."', 
							'". $db->Record['Nama_Rekening'] ."',							
							'". $db->Record['Kode_Jabatan'] ."',
							'". $db->Record['Jabatan'] ."', 
							'". $db->Record['Kode_Pangkat'] ."',
							'". $db->Record['Nama_Pangkat'] ."',
							'". $db->Record['Golongan'] ."', 
							'". $db->Record['Kode_Departemen'] ."',
							'". $db->Record['Departemen'] ."',
							'". $db->Record['Kode_Divisi'] ."', 
							'". $db->Record['Divisi'] ."',						
							'". $db->Record['Company'] ."',
							'". $db->Record['Kode_Lokasi'] ."',
							'". $db->Record['Lokasi'] ."',
							'". $db->Record['Email_Address'] ."',							
							'". $db->Record['Spv_ID'] ."',
							'". $db->Record['Atasan_Langsung'] ."',
							'". $db->Record['Tanggal_Masuk'] ."',
							". $inactive_date .", 
							'auto_syn_hris',
							sysdate(),
							'auto_syn_hris',
							sysdate())";
		$count_insert += 1;
	} 
	//update data yang telah terdapat di M_Employee
	else {
		$sqlqry = "UPDATE M_Employee 
				   SET Employee_FullName = '".$db->Record['Nama_Karyawan']."', 
					   Employee_Gender = '". $db->Record['Gender'] ."',
					   Employee_Religion = '". $db->Record['Agama'] ."',
					   Employee_Birthday = '". $db->Record['Tanggal_Lahir'] ."',
					   Employee_BankCode = '". $db->Record['Kode_Bank'] ."',
					   Employee_BankName = '". $db->Record['Nama_Bank'] ."',
					   Employee_BankAccount = '". $db->Record['Rekening_Bank'] ."',
					   Employee_AccBankName = '". $db->Record['Nama_Rekening'] ."',
					   Employee_PositionCode = '". $db->Record['Kode_Jabatan'] ."',
					   Employee_Position = '". $db->Record['Jabatan'] ."',
					   Employee_GradeCode = '". $db->Record['Kode_Pangkat'] ."',
					   Employee_Grade = '". $db->Record['Nama_Pangkat'] ."',
					   Employee_Level = '". $db->Record['Golongan'] ."',
					   Employee_DeptCode = '". $db->Record['Kode_Departemen'] ."',
					   Employee_Department = '". $db->Record['Departemen'] ."',
					   Employee_DivCode = '". $db->Record['Kode_Divisi'] ."',
					   Employee_Division = '". $db->Record['Divisi'] ."',
					   Employee_CompanyCode = '". $db->Record['Company'] ."',
					   Employee_LocationCode = '". $db->Record['Kode_Lokasi'] ."',
					   Employee_Location = '". $db->Record['Lokasi'] ."',
					   Employee_Spv = '". $db->Record['Atasan_Langsung'] ."',
					   Employee_SpvNIK = '". $db->Record['Spv_ID'] ."',
					   Employee_JoinDate = '". $db->Record['Tanggal_Masuk'] ."',
					   Employee_ResignDate = ". $inactive_date .", 
					   Employee_UpdateUser = 'auto_syn_hris',
					   Employee_UpdateTime = sysdate() 
				   WHERE Employee_NIK = '". $db->Record['NIK'] ."'";	
		$count_update += 1;
	}	
	$db_exp->query($sqlqry); 
	$count += 1;
}
echo "<br>";
echo "... Export Data Employee into MySQL Master Table Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br>";


//drop temporary table
$sqlqry = "DROP TABLE temp_exp_emp";
$db->query($sqlqry);
echo "TEMP TABLE has been DROPPED... <br>"; 

$today = date("D M j G:i:s T Y");
echo "End of Employee Import .. $today <BR><BR><BR>";

echo "**********<br>";
echo "Sinkronisasi Divisi, Departemen, Jabatan, Karyawan DONE.. $today <br><BR><BR>";

//UPDATE DATA APPROVER PAK YUKY KE PAK DONO
echo "Start Update Approval Custodian .. $today <BR>";
$sqlqry = "UPDATE custodian.M_User
		   SET User_SPV1 = '00000144'
		   WHERE User_SPV1 = '00000016'
		   AND User_ID <> '00000144'";
$db->query($sqlqry);

$today = date("D M j G:i:s T Y");
echo "Update Approval Custodian DONE .. $today <BR><BR><BR>";

//UPDATE DATA APPROVER FUNDING DIVISION KE YUDHA PRAYUDHI WIBHAWA H
echo "Start Update Approval FUNDING DIVISION .. $today <BR>";
$sqlqry = "UPDATE custodian.M_User
		   SET User_SPV1 = '00000111'
		   WHERE User_SPV1 = '00001191'
		   AND User_ID <> '00000111'";
$db->query($sqlqry);

$today = date("D M j G:i:s T Y");
echo "Update Approval FUNDING DIVISION DONE .. $today <BR><BR><BR>";

//UPDATE NON AKTIF DATE TAN TIAN SANG
echo "Start Update NON AKTIF DATE TAN TIAN SANG .. $today <BR>";
$sqlqry = "UPDATE db_master.M_Employee
		   SET Employee_ResignDate = '2013-12-31'
		   WHERE Employee_NIK IN ('00000948', '00001371')";
$db->query($sqlqry);

$today = date("D M j G:i:s T Y");
echo "Update NON AKTIF DATE TAN TIAN SANG DONE .. $today <BR><BR><BR>";

// Close MS SQL Connection
odbc_close($connection);
?>