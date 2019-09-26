<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 25 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
// include_once 'session.php'; //Arief F - 20082018
?>
<title>Custodian System | Registrasi Dokumen Pembebasan Lahan</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdocla.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//untuk upload dokumen xls, temp
function ajaxFileUpload()
{
	$("#loading")
	.ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
		$(this).hide();
	});

	$.ajaxFileUpload
	(
		{
			url:'uploaddokumen/doajaxfileupload.php',
			secureuri:false,
			fileElementId:'fileToUpload',
			dataType: 'json',
			data:{name:'logan', id:'id'},
			success: function (data, status)
			{
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						//alert(data.filename);
						ajaxReadFile(data.filename);
					}
				}
			},
			error: function (data, status, e)
			{
				alert(e);
			}
		}
	)
	return false;
}

function show_tbl_detail(IDtarget){
	if(document.getElementById('flag_detail'+IDtarget).value == '0'){
		document.getElementById('flag_detail'+IDtarget).value = '1';
		document.getElementById('detail'+IDtarget).style.display = 'block';
		document.getElementById('btn-show-detail'+IDtarget).innerHTML = "Hide";
	}else{
		document.getElementById('flag_detail'+IDtarget).value = '0';
		document.getElementById('detail'+IDtarget).style.display = 'none';
		document.getElementById('btn-show-detail'+IDtarget).innerHTML = "Show";
	}
}

//untuk membaca file excel yang diupload
function ajaxReadFile(filename)
{
	$.getJSON("uploaddokumen/readxls.php", { filename: escape(filename) },
		function(data){
			var item = data;
			$("#button").show();
			var content = "";

			if ((data["1"]["A"]!="Perusahaan")||(data["1"]["B"]!="Tahap")||
				(data["1"]["C"]!="No")||(data["1"]["E"]!="Blok")||(data["1"]["G"]!="Nama Pemilik")
				||(data["1"]["I"]!="Lahan")||(data["1"]["L"]!="Kelas")||(data["1"]["P"]!="Total")
			){
				alert ("Format Excel Salah!!");
				$("#button").hide();
			}else{
				var array_company_id = [];
				var array_company_name = [];
				var pt_compare = "";
				var phase_compare = "";

				<?php
				$query_company_id = "SELECT Company_ID, UPPER(Company_Name) AS Company_Name FROM M_Company
						  WHERE Company_Delete_Time is NULL
						  ORDER BY Company_Name ASC";
				$sql_company_id = mysql_query($query_company_id);

				while ($field_ci = mysql_fetch_array($sql_company_id) ){
					?>
					array_company_id.push("<?=$field_ci['Company_ID'];?>");
					array_company_name.push("<?=strtoupper($field_ci['Company_Name']);?>");
					<?php
				}
				?>
				var content = "";
				var header_ke = 0;
				var pt_ke = 0;
				var array_row_ke = [];
				for (var i in data) {
					if (i > 2){
						// data[row][column] references cell from excel document.
						var Nama_Perusahaan = data[i]["A"];
						var Tahap = data[i]["B"];
						// var Periode = data[i]["C"];
						var no = data[i]["C"];
						var TDRGOLAD_DocDate = data[i]["D"];
						var TDRGOLAD_Block = data[i]["E"];
						var TDRGOLAD_Village = data[i]["F"];
						var TDRGOLAD_Owner = data[i]["G"];
						var TDRGOLAD_AreaClass = data[i]["H"];
						var TDRGOLAD_AreaStatement = data[i]["I"];
						var TDRGOLAD_AreaPrice = data[i]["J"];
						var TDRGOLAD_AreaTotalPrice = data[i]["K"];
						var TDRGOLAD_PlantClass = data[i]["L"];
						var TDRGOLAD_PlantQuantity = data[i]["M"];
						var TDRGOLAD_PlantPrice = data[i]["N"];
						var TDRGOLAD_PlantTotalPrice = data[i]["O"];
						var TDRGOLAD_GrandTotal = data[i]["P"];

						if (Nama_Perusahaan.replace(" ", "") == "")  {
						}else{
							var row_ke = 0;
							pt_ke = parseInt(pt_ke)+parseInt(1);
							header_ke = parseInt(header_ke)+parseInt(1);
									
							if((pt_compare == data[i]["A"] && phase_compare != data[i]["B"]) || (pt_compare != data[i]["A"] && phase_compare != data[i]["B"])){
								content += "<table width='100%' id='mytable' class='stripeMe'>";
								content += "<tr>";
								content += "<th>Perusahaan</th>";
								content += "<td><select name='optTHRGOLAD_Core_CompanyID"+pt_ke+"' id='optTHRGOLAD_Core_CompanyID"+pt_ke+"'>\
									<option value='0'>--- Pilih Perusahaan ---</option>";
								for(x = 0; x < array_company_id.length; x++){
									if( array_company_name[x] == Nama_Perusahaan.toUpperCase() ){
										var selected = " selected";
									}else{
										var selected = "";
									}
									content += "<option value='"+array_company_id[x]+"'"+selected+">"+array_company_name[x]+"</option>";
								}
								content += "</select></td>";
								// content += "<td><input type='text' name='optTHRGOLAD_Core_CompanyID' value='"+Nama_Perusahaan+"' ></td>";
								content += "</tr>";
								// content += "<tr>";
								// content += "<th>Keterangan</th>";
								// content += "<td><textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'>"+Keterangan_Dokumen+"</textarea></td>";
								// content += "</tr>";
								content += "<tr>";
								content += "<th>Tahap</th>";
								content += "<td><input type='text' name='txtTHRGOLAD_Core_Phase"+pt_ke+"' id='txtTHRGOLAD_Core_Phase"+pt_ke+"' value='"+Tahap+"' /></td>";
								content += "</td>";
								content += "</tr>";
								// content += "<th>Periode</th>";
								// content += "<td><input type='text' name='txtTHRGOLAD_Core_Period"+pt_ke+"' id='txtTHRGOLAD_Core_Period"+pt_ke+"' value='"+Periode+"' onclick=\"javascript:NewCssCal('txtTHRGOLAD_Core_Period"+pt_ke+"', 'MMddyyyy');\" /></td>";
								// content += "</td>";
								content += "<tr>\
									<td><input type='hidden' id='flag_detail"+header_ke+"' value='0' /></td>\
									<td><a class='btn-show-detail' onclick='show_tbl_detail(\""+header_ke+"\")' id='btn-show-detail"+header_ke+"'>Show</a>\
								</tr>";
								content += "</table>";

								content += "<table width='1000' id='detail"+header_ke+"' class='stripeMe' style='display:none;padding-bottom:10px;'>";
								content += "<tr>";
								content += "<th rowspan='2'>No</th>";
								content += "<th rowspan='2'>Tanggal</th>";
								content += "<th rowspan='2'>Blok</th>";
								content += "<th rowspan='2'>Desa</th>";
								content += "<th rowspan='2'>Nama Pemilik</th>";
								content += "<th rowspan='2'>Kelas</th>";
								content += "<th colspan='3'>Lahan</th>";
								content += "<th rowspan='2'>Kelas</th>";
								content += "<th colspan='3'>Tanam Tumbuh</th>";
								content += "<th rowspan='2'>Total</th>";
								content += "<th rowspan='2'>Keterangan</th>";
								<?PHP
									$query = "SELECT *
												FROM M_LandAcquisitionAttribute
												WHERE LAA_Delete_Time is NULL
												ORDER BY LAA_ID ";
									$sql = mysql_query($query);
									$counts=mysql_num_rows($sql);
								?>
								content += "<th colspan='<?PHP echo "$counts";?>'>Kelengkapan Dokumen</th>";
								content += "</tr>";
								content += "<tr>";
								content += "<th>Ha</th>";
								content += "<th>Rp/Ha</th>";
								content += "<th>Nilai (Rp)</th>";
								content += "<th>Qty</th>";
								content += "<th>Rp/Pkk</th>";
								content += "<th>Nilai (Rp)</th>";
								<?PHP while ($arr = mysql_fetch_array($sql)){ ?>
								content += "<th><?PHP echo "$arr[LAA_ID]"; ?></th>";
								<?PHP } ?>
								content += "</tr>";
							}
							pt_compare = data[i]["A"];
							phase_compare = data[i]["B"];
								
						}

						if(TDRGOLAD_DocDate != "" || TDRGOLAD_Block != "" || TDRGOLAD_Owner != "" || TDRGOLAD_AreaClass != ""
							 || TDRGOLAD_AreaStatement != "" || TDRGOLAD_AreaPrice != "" || TDRGOLAD_AreaTotalPrice != "" || TDRGOLAD_PlantClass != ""
							 || TDRGOLAD_PlantQuantity != "" || TDRGOLAD_PlantPrice != "" || TDRGOLAD_PlantTotalPrice != "" || TDRGOLAD_GrandTotal != ""
						){
							row_ke = parseInt(row_ke)+parseInt(1);
							array_row_ke["count_row_per_pt"+pt_ke] = row_ke;

							if((TDRGOLAD_AreaStatement=="-")||(TDRGOLAD_AreaStatement=="")){
								TDRGOLAD_AreaStatement=0;
							}else{
								TDRGOLAD_AreaStatement=parseFloat(TDRGOLAD_AreaStatement).toFixed(2);
							}
							if((TDRGOLAD_AreaPrice=="-")||(TDRGOLAD_AreaPrice=="")){
								TDRGOLAD_AreaPrice=0;
							}else{
								TDRGOLAD_AreaPrice=parseFloat(TDRGOLAD_AreaPrice).toFixed(2);
							}
							if((TDRGOLAD_AreaTotalPrice=="-")||(TDRGOLAD_AreaTotalPrice=="")){
								TDRGOLAD_AreaTotalPrice=0;
							}else{
								TDRGOLAD_AreaTotalPrice=parseFloat(TDRGOLAD_AreaTotalPrice).toFixed(2);
							}
							if((TDRGOLAD_PlantQuantity=="-")||(TDRGOLAD_PlantQuantity=="")){
								TDRGOLAD_PlantQuantity=0;
							}else{
								TDRGOLAD_PlantQuantity=parseFloat(TDRGOLAD_PlantQuantity).toFixed(2);
							}
							if((TDRGOLAD_PlantPrice=="-")||(TDRGOLAD_PlantPrice=="")){
								TDRGOLAD_PlantPrice=0;
							}else{
								TDRGOLAD_PlantPrice=parseFloat(TDRGOLAD_PlantPrice).toFixed(2);
							}
							if((TDRGOLAD_PlantTotalPrice=="-")||(TDRGOLAD_PlantTotalPrice=="")){
								TDRGOLAD_PlantTotalPrice=0;
							}else{
								TDRGOLAD_PlantTotalPrice=parseFloat(TDRGOLAD_PlantTotalPrice).toFixed(2);
							}
							if((TDRGOLAD_GrandTotal=="-")||(TDRGOLAD_GrandTotal=="")){
								TDRGOLAD_GrandTotal=0;
							}else{
								TDRGOLAD_GrandTotal=parseFloat(TDRGOLAD_GrandTotal).toFixed(2);
							}

							content += "<tr>";
							content += "<td width='50'><input type=text name=txtNumber"+pt_ke+"[] id=txtNumber"+pt_ke+"_"+row_ke+" value='"+no+"' size='1' ></td>";
							content += "<td width='100'><input type=text name=txtTDRGOLAD_DocDate"+pt_ke+"[] id=txtTDRGOLAD_DocDate"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_DocDate+"' size='7' onclick=\"javascript:NewCssCal('txtTDRGOLAD_DocDate"+pt_ke+"_"+row_ke+"', 'MMddyyyy');\"></td>";
							content += "<td width='160'><input type=text name=txtTDRGOLAD_Block"+pt_ke+"[] id=txtTDRGOLAD_Block"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_Block+"' size='15' ></td>";
							content += "<td width='160'><input type=text name=txtTDRGOLAD_Village"+pt_ke+"[] id=txtTDRGOLAD_Village"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_Village+"' size='15' ></td>";
							content += "<td width='160'><input type=text name=txtTDRGOLAD_Owner"+pt_ke+"[] id=txtTDRGOLAD_Owner"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_Owner+"' size='15' ></td>";
							content += "<td width='40'><input type=text name=txtTDRGOLAD_AreaClass"+pt_ke+"[] id=txtTDRGOLAD_AreaClass"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_AreaClass+"' size='3'></td>";
							content += "<td width='40'><input type=number name=txtTDRGOLAD_AreaStatement"+pt_ke+"[] id=txtTDRGOLAD_AreaStatement"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_AreaStatement+"' size='10' style='text-align:right' onchange='countTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_AreaPrice"+pt_ke+"[] id=txtTDRGOLAD_AreaPrice"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_AreaPrice+"' size='10' style='text-align:right' onchange='countTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_AreaTotalPrice"+pt_ke+"[] id=txtTDRGOLAD_AreaTotalPrice"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_AreaTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_PlantClass"+pt_ke+"[] id=txtTDRGOLAD_PlantClass"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_PlantClass+"' size='3' > </td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_PlantQuantity"+pt_ke+"[] id=txtTDRGOLAD_PlantQuantity"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_PlantQuantity+"' size='10' style='text-align:right' onchange='countTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_PlantPrice"+pt_ke+"[] id=txtTDRGOLAD_PlantPrice"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_PlantPrice+"' size='10' style='text-align:right' onchange='countTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_PlantTotalPrice"+pt_ke+"[] id=txtTDRGOLAD_PlantTotalPrice"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_PlantTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotal(\""+pt_ke+"_"+row_ke+"\");'></td>";
							content += "<td width='50'><input type=number name=txtTDRGOLAD_GrandTotal"+pt_ke+"[] id=txtTDRGOLAD_GrandTotal"+pt_ke+"_"+row_ke+" value='"+TDRGOLAD_GrandTotal+"' size='10' style='text-align:right' ></td>";
							content += "<td width='170'><textarea type=text name=txtTDRGOLAD_Information"+pt_ke+"[] id=txtTDRGOLAD_Information"+pt_ke+"_"+row_ke+"></textarea></td>";
							<?PHP
							$query = "SELECT * FROM M_LandAcquisitionAttribute WHERE LAA_Delete_Time is NULL ORDER BY LAA_ID ";
							$sql = mysql_query($query);
							$count=mysql_num_rows($sql);
							while ($arr=mysql_fetch_array($sql)){
								?>
								var Jenis="<?PHP echo "$arr[LAA_ID]"; ?>";
								content += "<td width='50'><select name=optKelengkapan"+Jenis+"_"+pt_ke+"[] id=optKelengkapan"+Jenis+"_"+pt_ke+"_"+row_ke+">";
								<?PHP
								$s_query = "SELECT * FROM M_LandAcquisitionAttributeStatus WHERE LAAS_Delete_Time is NULL ORDER BY LAAS_ID ";
								$s_sql = mysql_query($s_query);
								while ($s_arr=mysql_fetch_array($s_sql)){
									?>
									var optValue="<?PHP echo "$s_arr[LAAS_ID]"; ?>";
									var optText="<?PHP echo "$s_arr[LAAS_Symbol]"; ?>";
									content += "<option value='"+optValue+"' >"+optText+"</option>";
								<?PHP } ?>
								content += "</select></td>";
							<?PHP } ?>

							content += "</tr>";
							// content += "<input type='text' name='count_row_per_pt"+pt_ke+"' id='count_row_per_pt"+pt_ke+"' value='"+row_ke+"'/>";
						}else{
							content += "</table>";
						}
					}
				};
				content += "<input type='hidden' name='count_core_companyid' id='count_core_companyid' value='"+pt_ke+"'/>";
				for (var x = 0; x < pt_ke; x++) {
					var pt = parseInt(x)+parseInt(1);
					var total_row_per_pt = array_row_ke["count_row_per_pt"+pt];
					content += "<input type='hidden' name='count_row_per_pt"+pt+"' id='count_row_per_pt"+pt+"' value='"+total_row_per_pt+"'/>";
				}
				$('#row1').html(content);

				var jKelengkapan="<?PHP echo "$count"; ?>";
				document.getElementById('jKelengkapan').value=jKelengkapan;
				// document.getElementById('maxValue').value=i;
				// $('#row1').html(content);
			}
		});
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;

	// var optTHRGOLAD_CompanyID = document.getElementById('optTHRGOLAD_CompanyID').selectedIndex;
	// var txtTHRGOLAD_Phase = document.getElementById('txtTHRGOLAD_Phase').value;
	// var txtTHRGOLAD_Period = document.getElementById('txtTHRGOLAD_Period').value;
	//
	// 	if(optTHRGOLAD_CompanyID == 0) {
	// 		alert("Perusahaan Belum Dipilih!");
	// 		returnValue = false;
	// 	}
	// 	if (txtTHRGOLAD_Phase.replace(" ", "") == "") {
	// 		alert("Tahap Pembebasan Lahan Belum Terisi!");
	// 		returnValue = false;
	// 	}
	// 	else {
	// 		if(isNaN(txtTHRGOLAD_Phase)){
	// 			alert ("Tahap Harus Berupa Angka [0-9]!");
	// 			returnValue = false;
	// 		}
	// 	}
	// 	if (txtTHRGOLAD_Period.replace(" ", "") == "") {
	// 		alert("Periode Pembebasan Lahan Belum Terisi!");
	// 		returnValue = false;
	// 	}

	return returnValue;
}

// VALIDASI TANGGAL
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   }
   return this
}

function checkdate(dtStr,row, pt){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("Format Tanggal Pada Baris ke-" + row + " pada Perusahaan ke-"+pt+" Salah. Format Tanggal : MM/DD/YYYY")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Bulan Pada Baris ke-" + row + " pada Perusahaan ke-"+pt+" Tidak Valid")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Hari Pada Baris ke-" + row + " pada Perusahaan ke-"+pt+" Tidak Valid")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Masukkan 4 Digit Tahun Dari "+minYear+" Dan "+maxYear+" Pada Baris ke-" + row)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Tanggal Pada Baris ke-" + row + " pada Perusahaan ke-"+pt+" Tidak Valid")
		return false
	}
return true
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var returnValue;
	returnValue = true;
	// var maxValue = document.getElementById('maxValue').value;
	var regDate = document.getElementById('regDate').value;

	var jPT = document.getElementById('count_core_companyid').value;
	for (i = 1; i <= jPT; i++){
		var optTHRGOLAD_Core_CompanyID = document.getElementById('optTHRGOLAD_Core_CompanyID' + i).selectedIndex;
		var txtTHRGOLAD_Core_Phase = document.getElementById('txtTHRGOLAD_Core_Phase' + i).value;
		// var txtTHRGOLAD_Core_Period = document.getElementById('txtTHRGOLAD_Core_Period' + i).value;
		if(optTHRGOLAD_Core_CompanyID == 0) {
			alert("Nama Perusahaan pada Perusahaan ke-"+i+" Belum Dipilih!");
			return false;
		}
		if (txtTHRGOLAD_Core_Phase.replace(" ", "") == "")  {
			alert("Tahap pada Perusahaan ke-"+i+" Belum Terisi!");
			return false
		}
		// if (txtTHRGOLAD_Core_Period.replace(" ", "") == "")  {
		// 	alert("Periode pada Perusahaan ke-"+i+" Belum Terisi!");
		// 	return false
		// }

		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
	// for (i = 3; i <= maxValue; i++){
			var txtTDRGOLAD_DocDate = document.getElementById('txtTDRGOLAD_DocDate' + i+"_"+n).value;
			var txtTDRGOLAD_Block = document.getElementById('txtTDRGOLAD_Block' + i+"_"+n).value;
			var txtTDRGOLAD_Village = document.getElementById('txtTDRGOLAD_Village' + i+"_"+n).value;
			var txtTDRGOLAD_Owner = document.getElementById('txtTDRGOLAD_Owner' + i+"_"+n).value;
			var txtTDRGOLAD_AreaClass = document.getElementById('txtTDRGOLAD_AreaClass' + i+"_"+n).value;
			var txtTDRGOLAD_AreaStatement = document.getElementById('txtTDRGOLAD_AreaStatement' + i+"_"+n).value;
			var txtTDRGOLAD_AreaPrice = document.getElementById('txtTDRGOLAD_AreaPrice' + i+"_"+n).value;
			var Date1 = new Date(regDate);
			var Date2 = new Date(txtTDRGOLAD_DocDate);
			// var row=i-2;

			if(txtTDRGOLAD_DocDate.replace(" ", "") == "") {
				alert("Tanggal Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Ditentukan!");
				return false
			}
			else {
				if (checkdate(txtTDRGOLAD_DocDate,n, i) == false) {
					return false
				}
				else {
					if (Date2 > Date1) {
						alert("Tanggal Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Lebih Besar Daripada Tanggal Registrasi!");
						return false
					}
				}
			}
			if (txtTDRGOLAD_Block.replace(" ", "") == "")  {
				alert("Blok pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtTDRGOLAD_Village.replace(" ", "") == "")  {
				alert("Desa pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtTDRGOLAD_Owner.replace(" ", "") == "")  {
				alert("Nama Pemilik pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtTDRGOLAD_AreaClass.replace(" ", "") == "")  {
				alert("Kelas Area pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtTDRGOLAD_AreaStatement.replace(" ", "") == "")  {
				alert("Luas Area pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtTDRGOLAD_AreaPrice.replace(" ", "") == "")  {
				alert("Rp/Ha pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
		}
	}
	return true
}

//PERHITUNGAN TOTAL
function countTotal(rowNo){
	document.getElementById('txtTDRGOLAD_AreaTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_AreaStatement'+rowNo).value) * parseFloat(document.getElementById('txtTDRGOLAD_AreaPrice'+rowNo).value)).toFixed(2);
	document.getElementById('txtTDRGOLAD_PlantTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_PlantQuantity'+rowNo).value) * parseFloat(document.getElementById('txtTDRGOLAD_PlantPrice'+rowNo).value)).toFixed(2);
	countGrandTotal(rowNo);
}
function countGrandTotal(rowNo){
	document.getElementById('txtTDRGOLAD_GrandTotal'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_AreaTotalPrice'+rowNo).value) + parseFloat(document.getElementById('txtTDRGOLAD_PlantTotalPrice'+rowNo).value)).toFixed(2);
}
</script>
<style>
.btn-show-detail{
	border:1px solid cornflowerblue;
	background-color:cornflowerblue;
	font-weight:bold;
	border-radius:3px;
	color:#fff;
	padding:5px 10px;
	float:left;
	margin-bottom:10px;
	cursor:pointer;
}
.btn-show-detail:hover{
	background-color:skyblue;
}
</style>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);

if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$decrp = new custodian_encryp;

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='add-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Pembebasan Lahan</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2,grup.DocumentGroup_Name,grup.DocumentGroup_ID,
						 e.Employee_GradeCode, e.Employee_Grade
				  FROM M_User u
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID='3'
				  LEFT JOIN db_master.M_Employee AS e
                	ON u.User_ID = e.Employee_NIK
                	AND e.Employee_GradeCode IN ('0000000005','06','0000000003','05','04','0000000004')
				  WHERE u.User_ID='$mv_UserID'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHRGOLAD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[PosName]</td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>
				<input name='txtTHRGOLAD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			if( !empty($field['Employee_GradeCode']) && !empty($field['Employee_Grade']) ){
				// $ActionContent .="
				// <tr>
				// 	<td>Perusahaan</td>
				// 	<td>
				// 		<select name='optTHRGOLAD_CompanyID' id='optTHRGOLAD_CompanyID' style='width:350px'>
				// 			<option value='0'>--- Pilih Perusahan ---</option>";
				//
				// 		$query = "SELECT *
				// 				  FROM M_Company
				// 				  WHERE Company_Delete_Time is NULL
				// 				  ORDER BY Company_Name ASC";
				// 		$sql = mysql_query($query);
				//
				// 		while ($field = mysql_fetch_array($sql) ){
				// 			$ActionContent .="
				// 			<option value='$field[Company_ID]'>$field[Company_Name]</option>";
				// 		}
				// $ActionContent .="
				// 		</select>
				// 	</td>
				// </tr>
				// <tr>
				// 	<td>Tahap</td>
				// 	<td>
				// 		<input type='text' name='txtTHRGOLAD_Phase' id='txtTHRGOLAD_Phase' size='7'/>
				// 	</td>
				// </tr>
				// <tr>
				// 	<td>Periode (MM/DD/YYYY)</td>
				// 	<td>
				// 		<input type='text' name='txtTHRGOLAD_Period' id='txtTHRGOLAD_Period'  size='7' onclick=\"javascript:NewCssCal('txtTHRGOLAD_Period', 'MMddyyyy');\"/>
				// 	</td>
				// </tr>
				$ActionContent .="
				<tr>
					<td>Keterangan</td>
					<td><textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'></textarea></td>
				</tr>
				<tr>
					<th colspan=3>
						<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
						<input name='cancel' type='submit' value='Batal' class='button'/>
					</th>
				</tr>";
			}else{ //Else cek jabatan minimal Dept. Head
    			if(!$_POST['cancel']){
    				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.);</script>";
    			}

    			$ActionContent .="
    			<tr>
    				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
    					Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.<br>
    					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
    				</td>
    			</tr>
    			<tr>
    				<th colspan=3>
    					<input name='cancel' type='submit' value='OK' class='button'/>
    				</th>
    			</tr>";
    		}
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}
			$ActionContent .="
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
	$ActionContent .="
			</table>
			</form>";
	}

	//Menambah Detail Dokumen
	elseif($act=='adddetail') {
		$code=$_GET["id"];

		$query = "SELECT header.THRGOLAD_ID,
						 header.THRGOLAD_RegistrationCode,
						 header.THRGOLAD_RegistrationDate,
						 header.THRGOLAD_Period,
						 header.THRGOLAD_Phase,
						 header.THRGOLAD_Information,
						 u.User_FullName as FullName,
						 ddp.DDP_DeptID as DeptID,
						 ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID,
						 dp.Department_Name as DeptName,
						 d.Division_Name as DivName,
						 p.Position_Name as PosName,
						 grup.DocumentGroup_Name,
						 grup.DocumentGroup_ID,
						 comp.Company_Name, comp.Company_Area
				  FROM TH_RegistrationOfLandAcquisitionDocument header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THRGOLAD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_Company comp
					ON comp.Company_ID=header.THRGOLAD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID='3'
				  WHERE header.THRGOLAD_RegistrationCode='$code'
				  AND header.THRGOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$CompanyName=$field[Company_Name];
		$regdate=strtotime($field['THRGOLAD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$fregdate1=date("m/d/Y", $regdate);
		$perdate=strtotime($field['THRGOLAD_Period']);
		$fperdate=date("j M Y", $perdate);

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Pembebasan Lahan</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDRGOLAD_THRGOLAD_ID' type='hidden' value='$field[THRGOLAD_ID]'/>
				<input type='hidden' name='txtTDRGOLAD_THRGOLAD_RegistrationCode' value='$field[THRGOLAD_RegistrationCode]'/>
				$field[THRGOLAD_RegistrationCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Pendaftaran</td>
			<td><input type='hidden' id='regDate' value='$fregdate1'/>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>$field[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[PosName]</td>
		</tr>
		<!--<tr>
			<td>Grup</td>
			<td>$field[DocumentGroup_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>--><input type='hidden' name='txtCompArea' value='$field[Company_Area]' /><!--$CompanyName</td>
		</tr>
		<tr>
			<td>Tahap</td>
			<td>$field[THRGOLAD_Phase]</td>
		</tr>
		<tr>
			<td>Periode</td>
			<td>$fperdate</td>
		</tr>-->
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'>$field[THRGOLAD_Information]</textarea>
			</td>
		</tr>
		<tr>
			<td>Upload File Excel</td>
			<td>
				<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
				<a href='./sample/Sample_of_Excel_Reg_Land_Acquisition_Doc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>
		<input type=hidden name='maxValue' id='maxValue'>
		<input type=hidden name='jKelengkapan' id='jKelengkapan'>";


		// $ActionContent .="
		// <table width='2220' id='row1' class='stripeMe' border=1>";
		// $ActionContent .="
		// <input type=hidden name='maxValue' id='maxValue'>
		// <input type=hidden name='jKelengkapan' id='jKelengkapan'>
		// </table>

		// Bagian Judul Tabel
		$ActionContent .="
		<div id='row1'>
		</div>

		<table width='100%'>
		<tr>
			<td>";

			/* PROSES APPROVAL */
			$user=$mv_UserID;

			$result = array();

			for($sApp=1;$sApp<2;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
				$sql=mysql_query($query);
				$obj=mysql_fetch_object($sql);
				$atasan1=$obj->User_SPV1;
				$atasan2=$obj->User_SPV2;

				if($atasan2){
					$sApp=3;
					$atasan=$atasan2;
				}else{
					$atasan=$atasan1;
				}

				$query="SELECT Employee_NIK
						FROM db_master.M_Employee
						WHERE Employee_NIK='".$atasan."'
						-- AND Employee_Position NOT LIKE '%SECTION%'
						-- AND Employee_Position NOT LIKE '%SUB DEP%'";
				$sql=mysql_query($query);
				$canApprove=mysql_num_rows($sql);

				if($canApprove){
					//$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=3;
				}

				$user=$atasan1;
				$result[] = $user;
			}

			/*$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
						AND a.Approver_Delete_Time is NULL
					WHERE ra.RA_Name LIKE '%custodian%'
					ORDER BY ra.RA_ID";
			$sql=mysql_query($query);

			while($obj=mysql_fetch_object($sql)){
				$ActionContent .="
				<input type='hidden' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}*/
			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID
				FROM M_Role_ApproverDocStepStatus rads
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				LEFT JOIN M_Approver ma
					ON ra.RA_ID = ma.Approver_RoleID
				WHERE rads.RADS_DocID = '10'
					AND rads.RADS_ProsesID = '1'
					AND ma.Approver_Delete_Time IS NULL
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$field['Company_Area']}')
					ORDER BY rads.RADS_StepID
			"; //Arief F - 24082018
			$sql=mysql_query($query);

			$output = array();
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
			}
			// AKHIR PROSES APPROVAL

			$i = 0;
			$newArray = array();
			foreach ($output as $k => $v) {
				if ($v == '0') { $newArray[$k] = $result[$i]; $i++; } else { $newArray[$k] = $v; }
			}

			$key = array_search('', $newArray);
			if (false !== $key) unset($newArray[$key]);

			foreach ($newArray as $key => $value) {
				$ActionContent .= "<input type='hidden' name='txtA_ApproverID[$key]' value='$value' readonly='true' class='readonly' />";
			}
			// AKHIR PROSES APPROVAL

		$ActionContent .="
			</td>
		</tr>
		<tr>
			<th>
				<input name='adddetail' type='submit' value='Daftar' id='button' style='display:none;' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>
		</tr>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_registration_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;
if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throld.THRGOLAD_ID, throld.THRGOLAD_RegistrationCode, throld.THRGOLAD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name, drs.DRS_Description, throld.THRGOLAD_RegStatus
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THRGOLAD_Delete_Time is NULL
		  AND throld.THRGOLAD_CompanyID=c.Company_ID
		  AND throld.THRGOLAD_UserID=u.User_ID
		  AND u.User_ID='$mv_UserID'
		  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
		  AND (c.Company_Delete_Time is NULL OR c.Company_ID='88') /*National*/
		  ORDER BY throld.THRGOLAD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='25%'>Kode Pendaftaran</th>
	<th width='15%'>Tanggal Pendaftaran</th>
	<th width='20%'>Nama Pendaftar</th>
	<th width='20%'>Nama Perusahaan</th>
	<th width='15%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$regdate=strtotime($field['THRGOLAD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THRGOLAD_RegStatus']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";
		if($field[5] == "Draft"){
			$link = "registration-of-land-acquisition-document.php?act=adddetail&id=".$field[1];
		}else{
			$link = "detail-of-registration-land-acquisition-document.php?id=".$decrp->encrypt($field[0]);
		}

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='".$link."' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="
	</table>
";

$query1= "SELECT throld.THRGOLAD_ID, throld.THRGOLAD_RegistrationCode, throld.THRGOLAD_RegistrationDate,
			     u.User_FullName, c.Company_Name, throld.THRGOLAD_RegStatus
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c
		  WHERE throld.THRGOLAD_Delete_Time is NULL
		  AND throld.THRGOLAD_CompanyID=c.Company_ID
		  AND throld.THRGOLAD_UserID=u.User_ID
		  AND c.Company_Delete_Time is NULL
		  AND u.User_ID='$mv_UserID'
		  ORDER BY throld.THRGOLAD_ID DESC";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";
/*echo "<script type='text/javascript'>
		alert('". $_POST[cancel] . "=" . $_POST[canceldetail] . "=" . $_POST[addheader] . "=" . $_POST[adddetail] . "=" ."');
		</script>";
*/
/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
}

elseif(isset($_POST[canceldetail])) {

	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfLandAcquisitionDocument throld
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       throld.THRGOLAD_Delete_UserID='$mv_UserID',throld.THRGOLAD_Delete_Time=sysdate(),
				   throld.THRGOLAD_Update_UserID='$mv_UserID',throld.THRGOLAD_Update_Time=sysdate()
			   WHERE throld.THRGOLAD_ID='$_POST[txtTDRGOLAD_THRGOLAD_ID]'
			   AND throld.THRGOLAD_RegistrationCode=ct.CT_Code
			   AND throld.THRGOLAD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
	}
}

elseif(isset($_POST['addheader'])) {
	$regyear=date("Y");
	$rmonth=date("n");

	// Mengubah Bulan ke Romawi
	switch ($rmonth)	{
		case 1: $regmonth="I"; break;
		case 2: $regmonth="II"; break;
		case 3: $regmonth="III"; break;
		case 4: $regmonth="IV"; break;
		case 5: $regmonth="V"; break;
		case 6: $regmonth="VI"; break;
		case 7: $regmonth="VII"; break;
		case 8: $regmonth="VIII"; break;
		case 9: $regmonth="IX"; break;
		case 10: $regmonth="X"; break;
		case 11: $regmonth="XI"; break;
		case 12: $regmonth="XII"; break;
	}

	// Cari Kode Perusahaan
	$query = "SELECT Company_Code, Company_ID
			  FROM M_Company
			  WHERE Company_Code='ALL'";
  	$field = mysql_fetch_array(mysql_query($query));
  	$Company_Code = $field['Company_Code'];
  	$Company_ID  = $field['Company_ID'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[txtTHRGOLAD_DocumentGroupID]'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='INS'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Registrasi Dokumen
	$CT_Code="$newnum/INS/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','INS','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
			  	   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$Empty_Phase="0";
		$Empty_Period="9999-99-99 23:59:59";
		// $txtTHRGOLAD_Period=date('Y-m-d H:i:s', strtotime($txtTHRGOLAD_Period));
		$info = str_replace("<br>", "\n", $_POST['txtTHRGOLAD_Information']);

		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_RegistrationOfLandAcquisitionDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$Company_ID',
				        '$Empty_Phase','$Empty_Period','0','$info',
						'0',NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	// echo count($_POST["optKelengkapan1_1"])."<br>";
	// echo count($_POST["txtTDRGOLAD_Village1"])."<br>";
	// foreach ($_POST as $key => $value) {
	//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	// }
	// exit();
	$A_TransactionCode = $_POST['txtTDRGOLAD_THRGOLAD_RegistrationCode'];
	$A_ApproverID=$mv_UserID;

	// $count=$_POST['maxValue'];
	$jKelengkapan=$_POST['jKelengkapan'];
	// echo $jKelengkapan."<hr>";
	$txtTHRGOLAD_Information=str_replace("<br>", "\n",$_POST['txtTHRGOLAD_Information']);

	//Phase 2
	$count_company = $_POST['count_core_companyid'];

	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = '';
		$Core_CompanyID = $_POST['optTHRGOLAD_Core_CompanyID'.$c];
		if ($Core_CompanyID == ""){
			for ($d = $c; $d >= 1; $d--){
				if($Core_CompanyID == ""){
					if ($_POST['optTHRGOLAD_Core_CompanyID'.$d] <> "")
						$Core_CompanyID = $_POST['optTHRGOLAD_Core_CompanyID'.$d];
				}
			}
		}
		$Core_Phase = '';
		$Core_Phase = $_POST['txtTHRGOLAD_Core_Phase'.$c];
		if ($Core_Phase == ""){
			for ($d = $c; $d >= 1; $d--){
				if($Core_Phase == ""){
					if ($_POST['txtTHRGOLAD_Core_Phase'.$d] <> "")
						$Core_Phase = $_POST['txtTHRGOLAD_Core_Phase'.$d];
				}
			}
		}
		// $Core_Period = date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Core_Period'.$c]));
		foreach($_POST['txtTDRGOLAD_Village'.$c] as $key => $value){
			$txtNumber=$_POST["txtNumber".$c][$key];
			$txtTDRGOLAD_DocDate=$_POST["txtTDRGOLAD_DocDate".$c][$key];
			$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));
			$txtTDRGOLAD_Block=$_POST["txtTDRGOLAD_Block".$c][$key];
			$txtTDRGOLAD_Village=$_POST["txtTDRGOLAD_Village".$c][$key];
			$txtTDRGOLAD_Owner=$_POST["txtTDRGOLAD_Owner".$c][$key];
			$txtTDRGOLAD_AreaClass=$_POST["txtTDRGOLAD_AreaClass".$c][$key];
			$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaStatement".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaStatement".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaStatement".$c][$key];
			$txtTDRGOLAD_AreaStatement=str_replace(",","",$txtTDRGOLAD_AreaStatement);
			$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaPrice".$c][$key];
			$txtTDRGOLAD_AreaPrice=str_replace(",","",$txtTDRGOLAD_AreaPrice);
			$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key];
			$txtTDRGOLAD_AreaTotalPrice=str_replace(",","",$txtTDRGOLAD_AreaTotalPrice);
			$txtTDRGOLAD_PlantClass=$_POST["txtTDRGOLAD_PlantClass".$c][$key];
			$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantQuantity".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantQuantity".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantQuantity".$c][$key];
			$txtTDRGOLAD_PlantQuantity=str_replace(",","",$txtTDRGOLAD_PlantQuantity);
			$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantPrice".$c][$key];
			$$txtTDRGOLAD_PlantPrice=str_replace(",","",$txtTDRGOLAD_PlantPrice);
			$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key];
			$txtTDRGOLAD_PlantTotalPrice=str_replace(",","",$txtTDRGOLAD_PlantTotalPrice);
			$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txtTDRGOLAD_GrandTotal".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_GrandTotal".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_GrandTotal".$c][$key];
			$txtTDRGOLAD_GrandTotal=str_replace(",","",$txtTDRGOLAD_GrandTotal);
			$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRGOLAD_Information".$c][$key]);

			$sql1= "INSERT INTO TD_RegistrationOfLandAcquisitionDocument
					SET TDRGOLAD_THRGOLAD_ID='$_POST[txtTDRGOLAD_THRGOLAD_ID]',
						TDRGOLAD_Core_CompanyID='$Core_CompanyID', TDRGOLAD_Core_Phase='$Core_Phase',
						TDRGOLAD_DocDate='$txtTDRGOLAD_DocDate', TDRGOLAD_Block='$txtTDRGOLAD_Block', TDRGOLAD_Village='$txtTDRGOLAD_Village',
						TDRGOLAD_Owner='$txtTDRGOLAD_Owner', TDRGOLAD_AreaClass='$txtTDRGOLAD_AreaClass', TDRGOLAD_AreaStatement='$txtTDRGOLAD_AreaStatement',
						TDRGOLAD_AreaPrice='$txtTDRGOLAD_AreaPrice', TDRGOLAD_AreaTotalPrice='$txtTDRGOLAD_AreaTotalPrice',
						TDRGOLAD_PlantClass='$txtTDRGOLAD_PlantClass', TDRGOLAD_PlantQuantity='$txtTDRGOLAD_PlantQuantity',
						TDRGOLAD_PlantPrice='$txtTDRGOLAD_PlantPrice', TDRGOLAD_PlantTotalPrice='$txtTDRGOLAD_PlantTotalPrice',
						TDRGOLAD_GrandTotal='$txtTDRGOLAD_GrandTotal', TDRGOLAD_Revision='0', TDRGOLAD_Information='$txtTDRGOLAD_Information',
						TDRGOLAD_Insert_UserID='$mv_UserID', TDRGOLAD_Insert_Time=sysdate(), TDRGOLAD_Update_UserID='$mv_UserID', TDRGOLAD_Update_Time=sysdate()";

						// echo $sql1."<hr>";
			if($mysqli->query($sql1)){
				$s_sql="SELECT TDRGOLAD_ID
						FROM TD_RegistrationOfLandAcquisitionDocument
						WHERE TDRGOLAD_THRGOLAD_ID='$_POST[txtTDRGOLAD_THRGOLAD_ID]'
						AND TDRGOLAD_DocDate='$txtTDRGOLAD_DocDate'
						AND TDRGOLAD_Block='$txtTDRGOLAD_Block'
						AND TDRGOLAD_Village='$txtTDRGOLAD_Village'
						AND TDRGOLAD_Owner='$txtTDRGOLAD_Owner'
						AND TDRGOLAD_AreaClass='$txtTDRGOLAD_AreaClass'
						AND FORMAT(TDRGOLAD_AreaStatement,2)=FORMAT('$txtTDRGOLAD_AreaStatement',2)
						AND FORMAT(TDRGOLAD_AreaPrice,0)=FORMAT('$txtTDRGOLAD_AreaPrice',0)
						AND FORMAT(TDRGOLAD_AreaTotalPrice,0)=FORMAT('$txtTDRGOLAD_AreaTotalPrice',0)
						AND TDRGOLAD_PlantClass='$txtTDRGOLAD_PlantClass'
						AND FORMAT(TDRGOLAD_PlantQuantity,2)=FORMAT('$txtTDRGOLAD_PlantQuantity',2)
						AND FORMAT(TDRGOLAD_PlantPrice,0)=FORMAT('$txtTDRGOLAD_PlantPrice',0)
						AND FORMAT(TDRGOLAD_PlantTotalPrice,0)=FORMAT('$txtTDRGOLAD_PlantTotalPrice',0)
						AND FORMAT(TDRGOLAD_GrandTotal,0)=FORMAT('$txtTDRGOLAD_GrandTotal',0)
						AND TDRGOLAD_Information='$txtTDRGOLAD_Information'";

				$s_query=mysql_query($s_sql);
				$s_arr=mysql_fetch_array($s_query);
				for ($j=1 ; $j<=$jKelengkapan ; $j++) {
					$optKelengkapan=$_POST["optKelengkapan".$j."_".$c][$key];

					$k_sql="INSERT INTO TD_RegistrationOfLandAcquisitionDocumentDetail
							VALUES (NULL, '$s_arr[TDRGOLAD_ID]', '$j', '$optKelengkapan',
							'$A_ApproverID', sysdate(), '$A_ApproverID', sysdate(), NULL, NULL)";
							// echo $k_sql."<hr>";
					$mysqli->query($k_sql);

				}
				// echo "<h1>stop Kelengkapan</h1>";
			}
		}
		// echo "<h1>stop per perusahaan</h1>";
	}
	//die();
	// exit();

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k] <> NULL) {
			if ($txtA_ApproverID[$k] <> $mv_UserID) {
				$appbefquery="SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_ApproverID='$txtA_ApproverID[$k]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_fetch_row($appbefsql);

				if ($numappbef == '0') {
					$step=$step+1;
					$sql2 = "INSERT INTO M_Approval
							VALUES (NULL, '$A_TransactionCode', '$txtA_ApproverID[$k]', '$k', '1', NULL,
							'$A_ApproverID', sysdate(), '$A_ApproverID', sysdate(), NULL, NULL)";
					$mysqli->query($sql2);
					$sa_query = "SELECT * FROM M_Approval
							WHERE A_TransactionCode = '$A_TransactionCode'
							AND A_ApproverID = '$txtA_ApproverID[$k]' AND A_Delete_Time IS NULL";
					$sa_arr = mysql_fetch_array(mysql_query($sa_query));
					$ARC_AID = $sa_arr['A_ID'];
					$str = rand(1,100);
					$RandomCode = crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}

	$step=0;
	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
		FROM M_Approval
		WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	for ($i=1; $i<=$jStep; $i++) {
		$query ="
			SELECT rads.RADS_StatusID, ma.A_ApproverID
			FROM M_Approval ma
			JOIN M_Role_ApproverDocStepStatus rads
				ON ma.A_Step = rads.RADS_StepID
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			WHERE ma.A_Step = '{$i}'
				AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$_POST['txtCompArea']}')
				AND ma.A_TransactionCode = '{$A_TransactionCode}'
				AND rads.RADS_DocID = '10'
				AND rads.RADS_ProsesID = '1'
		"; //Arief F - 24082018
		$result = mysql_fetch_array(mysql_query($query));

		if ($result['RADS_StatusID'] == '1') {
			$query = "UPDATE M_Approval
					SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_registration_doc($A_TransactionCode);
			}
			break;
		} else if ($result['RADS_StatusID'] == '2') {
			$query = "UPDATE M_Approval
					SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
			}
		}
	}

	$sql4= "UPDATE TH_RegistrationOfLandAcquisitionDocument
			SET THRGOLAD_RegStatus='waiting', THRGOLAD_Information='$txtTHRGOLAD_Information',
			THRGOLAD_Update_UserID='$A_ApproverID',THRGOLAD_Update_Time=sysdate()
			WHERE THRGOLAD_RegistrationCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
			AND THRGOLAD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$mv_UserID){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_fetch_row($appbefsql);

				if ($numappbef==0) {
					$sc_query="SELECT *
							   FROM M_Approver a, M_Role_Approver ra
							   WHERE a.Approver_UserID='$txtA_ApproverID[$i]'
							   AND a.Approver_Delete_Time is NULL
							   AND ra.RA_ID=a.Approver_RoleID
							   AND ra.RA_Name LIKE '%Custodian%'";
					$sc_sql=mysql_query($sc_query);
					$sc_app=mysql_num_rows($sc_sql);
					if ($step==0 || $sc_app==1) {
					$step=$step+1;
					if ($step == '1') {
						$sql2= "INSERT INTO M_Approval
								VALUES (NULL,'$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]', '$txtA_ApproverID[$i]',
										'$step', '3',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
										sysdate(),NULL,NULL)";
					} else {
						$sql2= "INSERT INTO M_Approval
								VALUES (NULL,'$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]', '$txtA_ApproverID[$i]',
										'$step', '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
										sysdate(),NULL,NULL)";
					}
					$mysqli->query($sql2);

					$sa_query="SELECT *
								   FROM M_Approval
								   WHERE A_TransactionCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
								   AND A_ApproverID='$txtA_ApproverID[$i]'
								   AND A_Delete_Time IS NULL";
						$sa_sql=mysql_query($sa_query);
						$sa_arr=mysql_fetch_array($sa_sql);
						$ARC_AID=$sa_arr['A_ID'];
						$str=rand(1,100);
						$RandomCode=crypt('T4pagri'.$str);
						$iSQL="INSERT INTO L_ApprovalRandomCode
							   VALUES ('$ARC_AID','$RandomCode')";
						$mysqli->query($iSQL);
					}
				}
			}
		}
	}*/

	/*$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$mv_UserID',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
			AND A_Step='2'";*/

	/*if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		$id=$_POST['txtTDRGOLAD_THRGOLAD_RegistrationCode'];
		mail_registration_doc($id);
		mail_notif_registration_doc($_POST['txtTDRGOLAD_THRGOLAD_RegistrationCode'], $txtA_ApproverID[0], 3);
		//echo "AAAA";

		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
	}*/
	echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
