<!--
=========================================================================================================================
Project				: 	Sinkronisasi Data HRIS & Table MASTER untuk aplikasi-aplikasi SPD
Versi				: 	1.2.0
Deskripsi			: 	Sinkronisasi Divisi, Departemen, Jabatan Karyawan, dan Data Karyawan di database MASTER
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Indrabudhi Lokaadi
Dibuat Tanggal		: 	01/07/2012
UPDATE Terakhir		:	01/02/2013
Revisi				:	
	# Sabrina - 19/11/2012 	: Penyesuaian TABLE MASTER
	# Nicholas - 01/02/2013	: Penyesuaian Table Master di SPD
=========================================================================================================================
-->

<!-- REFRESH PAGE TIAP 1 JAM -->
<meta http-equiv="refresh" content="3600">

<?PHP
include("class.mysql_connection.php");

$username = 'DBLINKADM';
$password = 'dbl1nk4dm';
$dbname = '10.20.1.196/HCPROD';
//$password = 'DBLINKADM123';
//$dbname = '10.0.99.150/HRDEV';
$c = oci_connect($username, $password, $dbname);
if (!$c) {
	//echo 'Koneksi ke server database gagal dilakukan';
	//exit();
	    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);

} else {
	echo 'Koneksi ke server database sukses';
}
echo '<br />';
//die();
/* ================== */
/* UPDATE DATA DIVISI */
/* ================== */
$today = date('D M j G:i:s T Y');
echo "=== Start Division Import .. $today ===<br />";
$query = oci_parse($c, "SELECT ORGADTCODE, ORGADTNAME FROM SYSADM.V_PDMSORGADDGROUP");
oci_execute($query);
while ($data[] = oci_fetch_array($query, OCI_ASSOC));

$count = 0;
$db = new Database;

$divisi_drop = "DROP TABLE IF EXISTS temp_exp_division";
$db->query($divisi_drop);

$divisi_create = "CREATE TABLE temp_exp_division (`Div_Code` VARCHAR(20) NOT NULL, `Div_Name` VARCHAR(55) NULL )";
$db->query($divisi_create);

for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['ORGADTCODE'] != '') {
		$divisi_insert = "INSERT INTO temp_exp_division (Div_Code, Div_Name) VALUES ('{$data[$i]['ORGADTCODE']}', '{$data[$i]['ORGADTNAME']}')";
		$db->query($divisi_insert);
		$count += 1;
	}
}
echo "=== Insert Division : There is $count data insert into temp_exp_division ===<br />";

/* ========================== */
/* SINKRONISASI KE M_DIVISION */
/* ========================== */
$divisi_select = "SELECT * FROM temp_exp_division";
$db->query($divisi_select);

$db_exp = new Database;

$count = 0;
$count_insert = 0;
$count_update = 0;


while ($db->nextRecord()) {
	$divname = strtoupper($db->Record['Div_Name']);
	$m_div_select_name = "SELECT * FROM M_Division WHERE Division_Name = '{$divname}'";
	$db_exp->query($m_div_select_name);
	$db_exp->singleRecord();
	$select_by_name = $db_exp->Record['Division_Name'];

	$divcode = $db->Record['Div_Code'];
	$m_div_select_code = "SELECT * FROM M_Division WHERE Division_Code = '{$divcode}'";
	$db_exp->query($m_div_select_code);
	$db_exp->singleRecord();
	$select_by_code = $db_exp->Record['Division_Code'];

	if ($select_by_name == '' && $select_by_code == '') {
		echo "*** INSERT DATA DIVISION : '{$db->Record['Div_Code']}' | '{$db->Record['Div_Name']}' ***<br />";

		$m_div_query = "
			INSERT INTO M_Division (
				Division_Code, Division_Name, Division_InsertUser, Division_InsertTime, Division_UpdateUser, Division_UpdateTime
			) VALUES (
				'{$db->Record['Div_Code']}', '{$db->Record['Div_Name']}', 'auto_syn_hris', sysdate(), 'auto_syn_hris', sysdate()
			)
		";
		$count_insert += 1;
		$db_exp->query($m_div_query);
	} else if ($select_by_name == '' && $select_by_code != '') {
		echo "*** UPDATE DATA DIVISION BY CODE : '{$db->Record['Div_Code']}' | '{$db->Record['Div_Name']}' ***<br />";

		$m_div_query = "
			UPDATE M_Division
			SET Division_Name = '{$db->Record['Div_Name']}',
			Division_Code = '{$db->Record['Div_Code']}',
			Division_UpdateUser = 'auto_syn_hris',
			Division_UpdateTime = sysdate(),
			Division_InactiveTime = NULL
			WHERE Division_Code = '{$db->Record['Div_Code']}'
		";
		$count_update += 1;
		$db_exp->query($m_div_query);
	} else if ($select_by_name != '' && $select_by_code == '') {
		echo "*** UPDATE DATA DIVISION BY NAME : '{$db->Record['Div_Code']}' | '{$db->Record['Div_Name']}' ***<br />";

		$m_div_query = "
			UPDATE M_Division
			SET Division_Name = '{$db->Record['Div_Name']}',
			Division_Code = '{$db->Record['Div_Code']}',
			Division_UpdateUser = 'auto_syn_hris',
			Division_UpdateTime = sysdate(),
			Division_InactiveTime = NULL
			WHERE Division_Name = '{$db->Record['Div_Name']}'
		";
		$count_update += 1;
		$db_exp->query($m_div_query);
	} else if ($select_by_name != '' && $select_by_code != '') {
		echo "*** UPDATE DATA DIVISION BY NAME : '{$db->Record['Div_Code']}' | '{$db->Record['Div_Name']}' ***<br />";

		$m_div_query = "
			UPDATE M_Division
			SET Division_Name = '{$db->Record['Div_Name']}',
			Division_Code = '{$db->Record['Div_Code']}',
			Division_UpdateUser = 'auto_syn_hris',
			Division_UpdateTime = sysdate(),
			Division_InactiveTime = NULL
			WHERE Division_Code = '{$db->Record['Div_Code']}'
		";
		$count_update += 1;
		$db_exp->query($m_div_query);
	}
	$count += 1;

}
echo "=== Export Data Division to MySQL Master Table Division Complete! ===<br />";
echo "=== There is $count total data check, with $count_insert inserted into table and $count_update data updated ===<br />";

/* INACTIVE DIVISION YANG ADA DI MASTER TAPI TIDAK TERDAPAT DI HRIS */
$m_div_inactive = "UPDATE M_Division 
	SET Division_InactiveTime = sysdate() 
	WHERE Division_Code NOT IN (SELECT Div_Code FROM temp_exp_division) 
	AND (Division_Code LIKE 'D%' OR Division_Code LIKE 'T%') 
	AND Division_InactiveTime IS NULL";
$db->query($m_div_inactive);

/* DROP TEMPORARY TABLE DIVISION */
$divisi_drop = "DROP TABLE IF EXISTS temp_exp_division";
$db->query($divisi_drop);
echo "=== TEMP TABLE DIVISION HAS BEEN DROPPED ===<br />";

$today = date('D M j G:i:s T Y');
echo "=== End of Division Import $today ===<br /><br /><br />";

unset($data);


/* ====================== */
/* UPDATE DATA DEPARTMENT */
/* ====================== */
$today = date('D M j G:i:s T Y');
echo "=== Start Department Import .. $today ===<br />";
$sql = "
	SELECT DISTINCT(KODE_ORGANIZATION) KODE_ORGANIZATION, NAMA_ORGANIZATION, ORGANIZATION_ADD_GROUP FROM SYSADM.V_PDMSORGANIZATION 
	JOIN SYSADM.V_PDMSKARYAWAN ON KODE_DEPARTEMEN = KODE_ORGANIZATION 
	AND TANGGAL_KELUAR IS NULL 
	AND LOKASI LIKE '%HEAD %'
";
$query = oci_parse($c, $sql);
oci_execute($query);
while ($data[] = oci_fetch_array($query, OCI_ASSOC));

$count = 0;

$depart_drop = "DROP TABLE IF EXISTS temp_exp_dep";
$db->query($depart_drop);

$depart_create = "
	CREATE TABLE temp_exp_dep (
		`Dep_Code` VARCHAR(20) NOT NULL, 
		`Dep_Name` VARCHAR(55) NULL, 
		`Dep_Div_Name` VARCHAR(55) NULL, 
		`Dep_Div_Code` VARCHAR(55) NULL
	)";
$db->query($depart_create);

for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['KODE_ORGANIZATION'] != '') {
		// cari kode divisi berdasarkan divisi yang ada di Master
		$Division_Code = '';
		if ($data[$i]['ORGANIZATION_ADD_GROUP'] != '') {
			$depart_select = "SELECT Division_ID FROM M_Division WHERE UPPER(Division_Name) = UPPER('{$data[$i]['ORGANIZATION_ADD_GROUP']}')";
			$db->query($depart_select);
			$db->singleRecord();
			$Division_Code = $db->Record['Division_ID'];
		}
		$Division_Code = ($Division_Code == '') ? "null" : $Division_Code;

		$depart_insert = "
			INSERT INTO temp_exp_dep (
				Dep_Code, Dep_Name, Dep_Div_Name, Dep_Div_Code
			) VALUES (
				'{$data[$i]['KODE_ORGANIZATION']}', '{$data[$i]['NAMA_ORGANIZATION']}', '{$data[$i]['ORGANIZATION_ADD_GROUP']}', '{$Division_Code}'
			)
		";
		$db->query($depart_insert);
		$count += 1;
	}
}
echo "=== There is $count data insert into temp table Department ===<br />";


/* SINKRONISASI DATA DEPARTMENT */
$dep_select = "SELECT * FROM temp_exp_dep";
$db->query($dep_select);

$db_exp = new Database;

$count = 0;
$count_insert = 0;
$count_update = 0;

while ($db->nextRecord()) {
	$depname = strtoupper($db->Record['Dep_Name']);
	$m_dep_select_name = "SELECT * FROM M_Departemen WHERE Dep_Name = '{$depname}'";
	$db_exp->query($m_dep_select_name);
	$db_exp->singleRecord();
	$select_by_name = $db_exp->Record['Dep_Name'];

	$depcode = $db->Record['Dep_Code'];
	$m_dep_select_code = "SELECT * FROM M_Departemen WHERE Dep_Code = '{$depcode}'";
	$db_exp->query($m_dep_select_code);
	$db_exp->singleRecord();
	$select_by_code = $db_exp->Record['Dep_Code'];

	if ($select_by_name == '' && $select_by_code == '') {
		echo "*** INSERT DATA DEPARTEMEN : '{$db->Record['Dep_Code']}' | '{$db->Record['Dep_Name']}' ***<br />";

		$m_dep_query = "
			INSERT INTO M_Departemen (Dep_Code, Dep_Name, Dep_DivisionID, Dep_InsertUser, Dep_InsertTime, Dep_UpdateUser, Dep_UpdateTime) VALUES (
				'{$db->Record['Dep_Code']}', '{$db->Record['Dep_Name']}', '{$db->Record['Dep_Div_Code']}', 
				'auto_syn_hris', sysdate(), 'auto_syn_hris', sysdate()
			)
		";
		$count_insert += 1;
		$db_exp->query($m_dep_query);
	} else if ($select_by_name == '' && $select_by_code != '') {
		$Division_Code = '';
		$Division_Code = ($db->Record['Dep_Div_Code'] == '') ? "null" : $db->Record['Dep_Div_Code'];
		echo "*** UPDATE DATA DEPARTEMEN BY CODE : '{$Division_Code}' | '{$db->Record['Dep_Name']}' ***<br />";
		echo $Division_Code; echo '<br />';

		$m_dep_query = "
			UPDATE M_Departemen
			SET Dep_Name = '{$db->Record['Dep_Name']}',
			Dep_Code = '{$db->Record['Dep_Code']}',
			Dep_DivisionID = '{$Division_Code}',
			Dep_UpdateUser = 'auto_syn_hris',
			Dep_UpdateTime = sysdate(),
			Dep_InactiveTime = NULL
			WHERE Dep_Code = '{$db->Record['Dep_Code']}'
		";
		$count_update += 1;
		$db_exp->query($m_dep_query);
	} else if ($select_by_name != '' && $select_by_code == '') {
		$Division_Code = '';
		$Division_Code = ($db->Record['Dep_Div_Code'] == '') ? "null" : $db->Record['Dep_Div_Code'];
		echo "*** UPDATE DATA DEPARTEMEN BY NAME : '{$Division_Code}' | '{$db->Record['Dep_Name']}' ***<br />";

		$m_dep_query = "
			UPDATE M_Departemen
			SET Dep_Name = '{$db->Record['Dep_Name']}',
			Dep_Code = '{$db->Record['Dep_Code']}',
			Dep_DivisionID = '{$Division_Code}',
			Dep_UpdateUser = 'auto_syn_hris',
			Dep_UpdateTime = sysdate(),
			Dep_InactiveTime = NULL
			WHERE Dep_Name = '{$db->Record['Dep_Name']}'
		";
		$count_update += 1;
		$db_exp->query($m_dep_query);
	} else if ($select_by_name != '' && $select_by_name != '') {
		$Division_Code = '';
		$Division_Code = ($db->Record['Dep_Div_Code'] == '') ? "null" : $db->Record['Dep_Div_Code'];
		echo "*** UPDATE DATA DEPARTEMEN BY NAME : '{$Division_Code}' | '{$db->Record['Dep_Name']}' ***<br />";

		$m_dep_query = "
			UPDATE M_Departemen
			SET Dep_Name = '{$db->Record['Dep_Name']}',
			Dep_Code = '{$db->Record['Dep_Code']}',
			Dep_DivisionID = '{$Division_Code}',
			Dep_UpdateUser = 'auto_syn_hris',
			Dep_UpdateTime = sysdate(),
			Dep_InactiveTime = NULL
			WHERE Dep_Code = '{$db->Record['Dep_Code']}'
		";
		$count_update += 1;
		$db_exp->query($m_dep_query);
	}
	$count += 1;
}
echo "=== Export Data Department into MySQL Master Table Department Complete! ===<br />";
echo "=== There is $count total data check, with $count_insert inserted into table and $count_update data updated ===<br />";

/* INACTIVE DEPARTMENT YANG ADA DI MASTER TAPI TIDAK ADA DI HRIS */
$m_dep_update = "
	UPDATE M_Departemen 
	SET Dep_InactiveTime = sysdate()
	WHERE Dep_Code NOT IN (SELECT Dep_Code FROM temp_exp_dep) 
	AND Dep_Code LIKE 'D%' 
	AND Dep_InactiveTime IS NULL
";
$db->query($m_dep_update);

/* DROP TEMPORARY TABLE DEPARTMENT */
$m_dep_drop = "DROP TABLE temp_exp_dep";
$db->query($m_dep_drop);
echo "=== TEMP TABLE DEPARTMENT HAS BEEN DROPPED ===<br />";

$today = date('D M j G:i:s T Y');
echo "=== End of Department Import .. $today ===<br /><br /><br />";

unset($data);


/* ============================ */
/* UPDATE DATA JABATAN KARYAWAN */
/* ============================ */
$today = date('D M j G:i:s T Y');
echo "=== Start Jabatan Import .. $today ===<br />";
$query = oci_parse($c, "SELECT * FROM SYSADM.V_PDMSJOBTITLE");
oci_execute($query);
while ($data[] = oci_fetch_array($query, OCI_ASSOC));

$count = 0;

$db = new Database;

$jabatan_drop = "DROP TABLE IF EXISTS temp_exp_position";
$db->query($jabatan_drop);

$jabatan_create = "CREATE TABLE temp_exp_position(`Position_Code` VARCHAR(20) NOT NULL, `Position_Name` VARCHAR(55) NULL)";
$db->query($jabatan_create);

for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['NAMA_JOBTITLE'] != '') {
		$jabatan_insert = "INSERT INTO temp_exp_position (Position_Code, Position_Name) VALUES ('{$data[$i]['KODE_JOBTITLE']}', '{$data[$i]['NAMA_JOBTITLE']}')";
		$db->query($jabatan_insert);
		$count += 1;
	}
}
echo "=== There is $count data insert into temp table jabatan ===<br />";

/* SINKRONISASI DATA */
$jabatan_select = "SELECT * FROM temp_exp_position";
$db->query($jabatan_select);
$db_exp = new Database;

$count = 0;
$count_insert = 0;
$count_update = 0;

while ($db->nextRecord()) {
	$titlename = strtoupper($db->Record['Position_Name']);
	$m_title_select_name = "SELECT * FROM M_Title WHERE Title_Name = '{$titlename}'";
	$db_exp->query($m_title_select_name);
	$db_exp->singleRecord();
	$select_by_name = $db_exp->Record['Title_Name'];

	$titlecode = $db->Record['Position_Code'];
	$m_title_select_code = "SELECT * FROM M_Title WHERE Title_Code = '{$titlecode}'";
	$db_exp->query($m_title_select_code);
	$db_exp->singleRecord();
	$select_by_code = $db_exp->Record['Title_Code'];

	if ($select_by_name == '' && $select_by_code == '') {
		echo "*** INSERT DATA POSITION : '{$db->Record['Position_Code']}' | '{$db->Record['Position_Name']}' ***<br />";

		$m_title_query = "
			INSERT INTO M_Title (Title_Code, Title_Name, Title_InsertUser, Title_InsertTime, Title_UpdateUser, Title_UpdateTime) VALUES (
				'{$db->Record['Position_Code']}', '{$db->Record['Position_Name']}', 'auto_syn_hris', sysdate(), 'auto_syn_hris', sysdate()
			)
		";
		$count_insert += 1;
		$db_exp->query($m_title_query);
	} else if ($select_by_name == '' && $select_by_code != '') {
		echo "*** UPDATE DATA POSITION BY CODE : '{$db->Record['Position_Code']}' | '{$db->Record['Position_Name']}' ***<br />";

		$m_title_query = "
			UPDATE M_Title 
			SET Title_Name = '{$db->Record['Position_Name']}', 
			Title_Code = '{$db->Record['Position_Code']}',
			Title_UpdateUser = 'auto_syn_hris',
			Title_UpdateTime = sysdate(),
			Title_InactiveTime = NULL 
			WHERE Title_Code = '{$db->Record['Position_Code']}'
		";
		$count_update += 1;
		$db_exp->query($m_title_query);
	} else if ($select_by_name != '' && $select_by_code == '') {
		echo "*** UPDATE DATA POSITION BY NAME : '{$db->Record['Position_Code']}' | '{$db->Record['Position_Name']}' ***<br />";

		$m_title_query = "
			UPDATE M_Title 
			SET Title_Name = '{$db->Record['Position_Name']}', 
			Title_Code = '{$db->Record['Position_Code']}',
			Title_UpdateUser = 'auto_syn_hris',
			Title_UpdateTime = sysdate(),
			Title_InactiveTime = NULL 
			WHERE Title_Name = '{$db->Record['Position_Name']}'
		";
		$count_update += 1;
		$db_exp->query($m_title_query);
	}

	$count += 1;
}
echo "=== Export Data Position into MySQL Master Table Title Complete! ===<br />";
echo "=== There is $count total data check, with $count_insert inserted into table and $count_update data updated ===<br />";

/* INACTIVE JABATAN KARYAWAN YANG ADA DI MASTER TAPI TIDAK ADA DI HRIS */
$m_title_inactive = "
	UPDATE M_Title 
	SET Title_InactiveTime = sysdate()
	WHERE Title_Code NOT IN (SELECT Position_Code FROM temp_exp_position) 
	-- AND Title_Code LIKE 'P%' 
	AND Title_InactiveTime IS NULL
";
$db->query($m_title_inactive);

/* DROP TEMPORARY TABLE TITLE */
$title_drop = "DROP TABLE IF EXISTS temp_exp_position";
$db->query($title_drop);
echo "=== TEMP TABLE POSITION HAS BEEN DROPPED ===<br />";

$today = date('D M j G:i:s T Y');
echo "=== End of Position Import .. $today ===<br /><br /><br />";

unset($data);

/* ==================== */
/* UPDATE DATA KARYAWAN */
/* ==================== */
$today = date('D M j G:i:s T Y');
echo "=== Start Karyawan Import .. $today ===<br />";

$query = oci_parse($c, "SELECT * FROM SYSADM.V_PDMSKARYAWAN");
oci_execute($query);
while ($data[] = oci_fetch_array($query, OCI_ASSOC));

$count = 0;

$db = new Database;

$emp_drop = "DROP TABLE IF EXISTS temp_exp_emp";
$db->query($emp_drop);

$emp_create = "
	CREATE TABLE temp_exp_emp (
		`NIK` VARCHAR(20) NOT NULL,
		`Nama_Karyawan` VARCHAR(150) NULL,
		`Gender` VARCHAR(20) NULL,
		`Agama` VARCHAR(20) NULL,
		`Tanggal_Lahir` VARCHAR(50) NULL,
		`Kode_Bank` VARCHAR(50) NULL,
		`Nama_Bank` VARCHAR(50) NULL,
		`Rekening_Bank` VARCHAR(50) NULL,
		`Nama_Rekening` VARCHAR(50) NULL,
		`Kode_Jabatan` VARCHAR(55) NULL,
		`Jabatan` VARCHAR(55) NULL,
		`Kode_Parent_Jabatan` VARCHAR(10) NULL,
		`Nama_Parent_Jabatan` VARCHAR(40) NULL,
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
		`Tanggal_Masuk` VARCHAR(30) DEFAULT NULL,
		`Tanggal_Keluar` VARCHAR(30) DEFAULT NULL,
		`No_Hp` VARCHAR(30) NULL
	)
";
$db->query($emp_create);

for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['NIK'] != '') {
			$nik = (!empty($data[$i]['NIK'])) ? $data[$i]['NIK'] : '';
			$namakaryawan = (!empty($data[$i]['NAMA_KARYAWAN'])) ? str_replace("'", "", $data[$i]['NAMA_KARYAWAN']) : '';
			$gender = (!empty($data[$i]['GENDER'])) ? $data[$i]['GENDER'] : '';
			$agama = (!empty($data[$i]['AGAMA'])) ? $data[$i]['AGAMA'] : '';
			$tgllahir = (!empty($data[$i]['TANGGAL_LAHIR'])) ? $data[$i]['TANGGAL_LAHIR'] : '';
			$kodebank = (!empty($data[$i]['KODE_BANK'])) ? $data[$i]['KODE_BANK'] : '';
			$namabank = (!empty($data[$i]['NAMA_BANK'])) ? $data[$i]['NAMA_BANK'] : '';
			$rekbank = (!empty($data[$i]['REKENING_BANK'])) ? $data[$i]['REKENING_BANK'] : '';
			$namarekening = (!empty($data[$i]['NAMA_REKENING'])) ? str_replace("'", "", $data[$i]['NAMA_REKENING']) : '';
			$kodejabatan = (!empty($data[$i]['KODE_JABATAN'])) ? $data[$i]['KODE_JABATAN'] : '';
			$jabatan = (!empty($data[$i]['JABATAN'])) ? $data[$i]['JABATAN'] : '';
			$kodeparent = (!empty($data[$i]['KODE_PARENT_JABATAN'])) ? $data[$i]['KODE_PARENT_JABATAN'] : '';
			$namaparent = (!empty($data[$i]['NAMA_PARENT_JABATAN'])) ? $data[$i]['NAMA_PARENT_JABATAN'] : '';
			$kodepangkat = (!empty($data[$i]['KODE_PANGKAT'])) ? $data[$i]['KODE_PANGKAT'] : '';
			$namapangkat = (!empty($data[$i]['NAMA_PANGKAT'])) ? $data[$i]['NAMA_PANGKAT'] : '';
			$golongan = (!empty($data[$i]['GOLONGAN'])) ? $data[$i]['GOLONGAN'] : '';
			$kodedepart = (!empty($data[$i]['KODE_DEPARTEMEN'])) ? $data[$i]['KODE_DEPARTEMEN'] : '';
			$depart = (!empty($data[$i]['DEPARTEMEN'])) ? $data[$i]['DEPARTEMEN'] : '';
			$kodediv = (!empty($data[$i]['KODE_DIVISI'])) ? $data[$i]['KODE_DIVISI'] : '';
			$div = (!empty($data[$i]['DIVISI'])) ? $data[$i]['DIVISI'] : '';
			$company = (!empty($data[$i]['COMPANY'])) ? $data[$i]['COMPANY'] : '';
			$kodelok = (!empty($data[$i]['KODE_LOKASI'])) ? $data[$i]['KODE_LOKASI'] : '';
			$lokasi = (!empty($data[$i]['LOKASI'])) ? $data[$i]['LOKASI'] : '';
			$email = (!empty($data[$i]['EMAIL_ADDRESS'])) ? str_replace("'", "", $data[$i]['EMAIL_ADDRESS']) : '';
			$atasan = (!empty($data[$i]['ATASAN_LANGSUNG'])) ? str_replace("'", "", $data[$i]['ATASAN_LANGSUNG']) : '';
			$nikatasan = (!empty($data[$i]['NIK_ATASA_LANGSUNG'])) ? $data[$i]['NIK_ATASA_LANGSUNG'] : '';
			$tglmasuk = (!empty($data[$i]['TANGGAL_MASUK'])) ? $data[$i]['TANGGAL_MASUK'] : '';
			$tglkeluar = (!empty($data[$i]['TANGGAL_KELUAR'])) ? $data[$i]['TANGGAL_KELUAR'] : '';
			$nohp = (!empty($data[$i]['NO_HP'])) ? $data[$i]['NO_HP'] : '';

		$emp_insert = "
			INSERT INTO temp_exp_emp(
				NIK, Nama_Karyawan, Gender, Agama, Tanggal_Lahir, Kode_Bank, Nama_Bank, Rekening_Bank, Nama_Rekening, Kode_Jabatan, Jabatan, 
				Kode_Parent_Jabatan, Nama_Parent_Jabatan, Kode_Pangkat, Nama_Pangkat, Golongan, Kode_Departemen, Departemen, Kode_Divisi, Divisi, 
				Company, Kode_Lokasi, Lokasi, Email_Address, Atasan_Langsung, Spv_ID, Tanggal_Masuk, Tanggal_Keluar, No_Hp
			) VALUES (
				'{$nik}', '{$namakaryawan}', '{$gender}', '{$agama}', '{$tgllahir}', '{$kodebank}', '{$namabank}', '{$rekbank}', '{$namarekening}',
				'{$kodejabatan}', '{$jabatan}', '{$kodeparent}', '{$namaparent}', '{$kodepangkat}', '{$namapangkat}', '{$golongan}',
				'{$kodedepart}', '{$depart}', '{$kodediv}', '{$div}', '{$company}', '{$kodelok}', '{$lokasi}', '{$email}', '{$atasan}',
				'{$nikatasan}', '{$tglmasuk}', '{$tglkeluar}', '{$nohp}'
			)
		";
		$db->query($emp_insert);
		$count += 1;
	}
}
echo "=== There is $count data insert into temp table employee ===<br />";

/* SINKRONISASI DATA */
$emp_select = "SELECT * FROM temp_exp_emp";
$db->query($emp_select);

$db_exp = new Database;

$count = 0;
$count_insert = 0;
$count_update = 0;

while ($db->nextRecord()) {
	// set inactive date
	$inactive_date = '';
	$inactive_date = ($db->Record['Tanggal_Keluar']) ? date('Y-m-d', strtotime($db->Record['Tanggal_Keluar'])) : "null";

	$m_emp_select = "SELECT * FROM M_Employee WHERE Employee_NIK = '{$db->Record['NIK']}'";
	$db_exp->query($m_emp_select);
	$db_exp->singleRecord();
	$emp_nik = $db_exp->Record['Employee_NIK'];

	$m_title_select = "SELECT * FROM M_Title WHERE Title_Code = '{$db->Record['Kode_Jabatan']}'";
	$db_exp->query($m_title_select);
	$db_exp->singleRecord();
	$emp_titleID = $db_exp->Record['Title_ID'];

	$m_comp_select = "SELECT * FROM M_Company WHERE Company_Name = '{$db->Record['Company']}'";
	$db_exp->query($m_comp_select);
	$db_exp->singleRecord();
	$emp_compID = $db_exp->Record['Company_ID'];

	$m_div_select = "SELECT * FROM M_Division WHERE Division_Code = '{$db->Record['Kode_Divisi']}'";
	$db_exp->query($m_div_select);
	$db_exp->singleRecord();
	$emp_divisionID = $db_exp->Record['Division_ID'];

	$m_dept_select = "SELECT * FROM M_Departemen WHERE Dep_Code = '{$db->Record['Kode_Departemen']}'";
	$db_exp->query($m_dept_select);
	$db_exp->singleRecord();
	$emp_depID = $db_exp->Record['Dep_ID'];

	// INSERT DATA YANG TIDAK ADA DI M_EMPLOYEE
	if ($emp_nik == '') {
		echo "*** INSERT DATA EMPLOYEE : '{$db->Record['NIK']}' | '{$db->Record['Nama_Karyawan']}' ***<br />";

		$m_emp_query = "INSERT INTO M_Employee (Employee_NIK, Employee_Name, Employee_JobFamilyID, Employee_JobFamily, Employee_Gender, Employee_Religion, Employee_Birthday, Employee_AccountBank, Employee_TitleID, Employee_GradeID, Employee_CompanyID, Employee_DivisionID, Employee_DepID, Employee_SuperviseID, Employee_EmailAddress, Employee_InsertUser, Employee_InsertTime, Employee_UpdateUser, Employee_UpdateTime, Employee_InactiveTime, Employee_ParentCode, Employee_ParentJob, Employee_PhoneNumber, Employee_Location) VALUES (
				'". $db->Record['NIK'] ."',
				'". $db->Record['Nama_Karyawan'] ."', 
				'". $db->Record['Kode_Pangkat'] ."',
				'". $db->Record['Nama_Pangkat'] ."', 
				'". $db->Record['Gender'] ."', 
				'". $db->Record['Agama'] ."',
				'". $db->Record['Tanggal_Lahir'] ."', 
				'". $db->Record['Rekening_Bank'] ."', 
				'". $emp_titleID ."',							
				'". $db->Record['Golongan'] ."',
				'". $emp_compID ."', 
				'". $emp_divisionID ."',
				'". $emp_depID ."',
				'". $db->Record['Spv_ID'] ."', 
				'". $db->Record['Email_Address'] ."',
				'auto_syn_hris',
				sysdate(),
				'auto_syn_hris',
				sysdate(),
				'". $db->Record['Tanggal_Keluar'] ."',
				'". $db->Record['Kode_Parent_Jabatan'] ."',
				'". $db->Record['Nama_Parent_Jabatan'] ."',							
				'". $db->Record['No_Hp'] ."',
				'". $db->Record['Lokasi'] ."'
			)";
			$count_insert += 1;
	}
	// UPDATE DATA YANG TELAH ADA DI M_EMPLOYEE
	else {
		echo "*** UPDATE DATA EMPLOYEE : '{$db->Record['NIK']}' | '{$db->Record['Nama_Karyawan']}' ***<br />";
		$m_emp_query = "
			UPDATE M_Employee 
			SET Employee_Name = '".$db->Record['Nama_Karyawan']."', 
			Employee_JobFamilyID = '". $db->Record['Kode_Pangkat'] ."',
			Employee_JobFamily = '". $db->Record['Nama_Pangkat'] ."',
			Employee_Gender = '". $db->Record['Gender'] ."',
			Employee_Religion = '". $db->Record['Agama'] ."',
			Employee_Birthday = '". $db->Record['Tanggal_Lahir'] ."',
			Employee_AccountBank = '". $db->Record['Rekening_Bank'] ."',
			Employee_TitleID = '". $emp_titleID ."',
			Employee_GradeID = '". $db->Record['Golongan'] ."',
			Employee_CompanyID = '". $emp_compID ."',
			Employee_DivisionID = '". $emp_divisionID ."',
			Employee_DepID = '". $emp_depID ."',
			Employee_SuperviseID = '". $db->Record['Spv_ID'] ."',
			Employee_EmailAddress = '". $db->Record['Email_Address'] ."',
			Employee_ParentCode = '". $db->Record['Kode_Parent_Jabatan'] ."',
			Employee_ParentJob = '". $db->Record['Nama_Parent_Jabatan'] ."',
			Employee_PhoneNumber = '". $db->Record['No_Hp'] ."',
			Employee_Location = '". $db->Record['Lokasi'] ."',
			Employee_InactiveTime = ". $inactive_date .", 
			Employee_UpdateUser = 'auto_syn_hris',
			Employee_UpdateTime = sysdate() 
			WHERE Employee_NIK = '". $db->Record['NIK'] ."'";

		$count_update += 1;
	}
	$db_exp->query($m_emp_query);
	$count += 1;
}
echo "=== Export Data Employee into MySQL Master Table Employee Complete! ===<br />";
echo "=== There is $count total data check, with $count_insert inserted into table and $count_update data updated ===<br />";


/* DROP TEMPORARY TABLE EMPLOYEE */
$m_emp_drop = "DROP TABLE temp_exp_emp";
//$db->query($m_emp_drop);
echo "=== TEMP TABLE EMPLOYEE HAS BEEN DROPPED ===<br />";

$today = date('D M j G:i:s T Y');
echo "=== End of Employee Import .. $today ===<br /><br /><br />";

echo "===========================================================<br />";
echo "===========================================================<br />";
echo "===========================================================<br />";
echo "=== Sinkronisasi Divisi, Department, Jabatan, Karyawan DONE .. $today ===<br />";

unset($data);

?>