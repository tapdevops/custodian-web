<!--
=========================================================================================================================
Project				: 	Sinkronisasi Table MASTER dengan table di aplikasi PT/PA
Versi				: 	1.0.0
Deskripsi			: 	Sinkronisasi user table master dengan user di aplikasi PT/PA
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	23/11/2012
Update Terakhir		:	23/11/2012
Revisi				:	
=========================================================================================================================
-->

<!-- REFRESH PAGE TIAP 1 JAM -->
<meta http-equiv="refresh" content="3600">

<?PHP
include("./class.db_connection.php");
$db = new Database;

/* =================== */
/* UPDATE USER PT / PA */
/* =================== */
echo "**********<br>";
$today = date("D M j G:i:s T Y");
echo "Start Sinkronisasi user .. $today <br>";

// sinkronisasi data
$sqlqry = "SELECT * FROM M_Employee";
$db->query($sqlqry);
$db_exp = new Database;
$count = 0;
$count_insert = 0;
$count_update = 0;
while($db->nextRecord()) {
	if ($db->Record['Employee_ResignDate'])
		$status='0';
	else
		$status='1';
	
	$sqlqry = "SELECT * 
			   FROM db_ptpa.M_Role 
			   WHERE Role_UserNIK = '". $db->Record['Employee_NIK'] ."'";
	$db_exp->query($sqlqry);
	$db_exp->singleRecord();
	$emp_nik = $db_exp->Record['Role_UserNIK'];	
	//echo $sqlqry." | ".$emp_nik."<BR>";
	
	//insert data yang tidak terdapat di db_ptpa.M_Role
	if ($emp_nik=="") {
		echo "## Insert Data : ".$db->Record['Employee_NIK']." | ".$db->Record['Employee_FullName']."<br>";
		
		$sqlqry = "INSERT INTO db_ptpa.M_Role (	Role_UserNIK, 
										Role_Code,
										Role_Status,
										Role_InsertUser,
										Role_InsertTime,
										Role_UpdateUser,
										Role_UpdateTime)
					VALUES ('". $db->Record['Employee_NIK'] ."',
							'requester',
							'". $status ."', 
							'auto_syn_hris',
							sysdate(),
							'auto_syn_hris',
							sysdate())";
		$count_insert += 1;
	} 
	//update data yang telah terdapat di M_Employee
	else {
		$sqlqry = "UPDATE db_ptpa.M_Role 
				   SET Role_Status = '". $status ."', 
					   Role_UpdateUser = 'auto_syn_hris',
					   Role_UpdateTime = sysdate() 
				   WHERE Role_UserNIK = '". $db->Record['Employee_NIK'] ."'";	
		$count_update += 1;
	}	
	$db_exp->query($sqlqry); 
	$count += 1;
}
echo "<br>";
echo "... Export Data User into MySQL Master Role Complete! <br>";
echo "There is $count total data check, with $count_insert inserted into table and $count_update data updated<br>";

$today = date("D M j G:i:s T Y");
echo "End of User Import .. $today <BR><BR><BR>";

echo "**********<br>";
echo "Sinkronisasi User DONE.. $today <br>";

// Close MS SQL Connection
odbc_close($connection);
?>