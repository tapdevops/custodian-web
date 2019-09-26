<?PHP
// session_start();
include ("./include/mother-variable.php");
setcookie('Referer', $_SERVER["REQUEST_URI"], time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<title>Custodian System | Detail Registrasi Dokumen Pembebasan Lahan</title>
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

				var jKelengkapan="<?PHP echo "$count"; ?>";
				document.getElementById('jKelengkapanA').value=jKelengkapan;
				document.getElementById('maxValueA').value=i;
				document.getElementById('txtTHRGOLAD_Revision').value=parseInt(document.getElementById('txtTHRGOLAD_Revision').value)+parseInt(1);

				$('#row1').append(content);
			}
		});

}
// MENAMPILKAN BARIS UNTUK UPLOAD DOKUMEN EXCEL
function showUpload () {
	$("#rowUpload").show();
}
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtTHRGOLAD_Phase = document.getElementById('txtTHRGOLAD_Phase').value;
	var txtTHRGOLAD_Period = document.getElementById('txtTHRGOLAD_Period').value;

		if(txtTHRGOLAD_Phase.replace(" ", "") == "") {
			alert("Tahap Pembebasan Lahan Belum Ditentukan!");
			returnValue = false;
		}

		if (txtTHRGOLAD_Period.replace(" ", "") == "") {
			alert("Periode Pembebasan Lahan Belum Ditentukan!");
			returnValue = false;
		}
	if (typeof  document.DLA_detail.optTHRGOLAD_RegStatus != 'undifined') {
		var optTHRGOLAD_RegStatus = document.getElementById('optTHRGOLAD_RegStatus').selectedIndex;
		var txtTHRGOLAD_RegStatusReason = document.getElementById('txtTHRGOLAD_RegStatusReason').value;

		if(optTHRGOLAD_RegStatus == 0) {
			alert("Persetujuan Belum Dipilih!");
			returnValue = false;
		}
		if(optTHRGOLAD_RegStatus == 2) {
			if (txtTHRGOLAD_RegStatusReason.replace(" ", "") == "") {
				alert("Keterangan Persetujuan Harus Diisi Apabila Anda Menolak Dokumen Ini!");
				returnValue = false;
			}
		}
	}
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

function checkdate(dtStr,row){
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
		alert("Format Tanggal Pada Baris ke-" + row + " Salah. Format Tanggal : MM/DD/YYYY")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Bulan Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Hari Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Masukkan 4 Digit Tahun Dari "+minYear+" Dan "+maxYear+" Pada Baris ke-" + row)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Tanggal Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
return true
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var confirmSave=confirm("Anda Yakin Ingin Menyimpan Transaksi Ini?\nTransaksi Yang Tersimpan Akan Langsung Ditambahkan Sebagai Dokumen Baru.");
	if(confirmSave==true){
		var returnValue;
		returnValue = true;
		var maxValue = document.getElementById('maxValueA').value;
		var regDate = document.getElementById('txtDL_RegTime').value;

		var jPT = document.getElementById('count_core_companyid').value;
		for (i = 1; i <= jPT; i++){
			var jrow = document.getElementById('count_row_per_pt'+i).value;
			for(n = 1; n <= jrow; n++){
				var txt_DocDate = document.getElementById('txt_DocDate' + i+"_"+n).value;
				var txt_Block = document.getElementById('txt_Block' + i+"_"+n).value;
				var txt_Village = document.getElementById('txt_Village' + i+"_"+n).value;
				var txt_Owner = document.getElementById('txt_Owner' + i+"_"+n).value;
				var txt_AreaClass = document.getElementById('txt_AreaClass' + i+"_"+n).value;
				var txt_AreaStatement = document.getElementById('txt_AreaStatement' + i+"_"+n).value;
				var txt_AreaPrice = document.getElementById('txt_AreaPrice' + i+"_"+n).value;
				var Date1 = new Date(regDate);
				var Date2 = new Date();
				// var row=i-2;

				if(txt_DocDate.replace(" ", "") == "") {
					alert("Tanggal Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Ditentukan!");
					return false
				}
				else {
					if (checkdate(txt_DocDate,row) == false) {
						return false
					}
				}
				if (txt_Block.replace(" ", "") == "")  {
					alert("Blok pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
					return false
				}
				if (txt_Village.replace(" ", "") == "")  {
					alert("Desa pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
					return false
				}
				if (txt_Owner.replace(" ", "") == "")  {
					alert("Nama Pemilik pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
					return false
				}
				if (txt_AreaStatement.replace(" ", "") == "")  {
					alert("Luas Area pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
					return false
				}
				if (txt_AreaPrice.replace(" ", "") == "")  {
					alert("Rp/Ha pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
					return false
				}
			}
		}
		return true
	}else{
		return false
	}
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

function countTotalA(rowNo){
	document.getElementById('txt_AreaTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_AreaStatement'+rowNo).value) * parseFloat(document.getElementById('txt_AreaPrice'+rowNo).value)).toFixed(2);
	document.getElementById('txt_PlantTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_PlantQuantity'+rowNo).value) * parseFloat(document.getElementById('txt_PlantPrice'+rowNo).value)).toFixed(2);
	countGrandTotalA(rowNo);
}
function countGrandTotalA(rowNo){
	document.getElementById('txt_GrandTotal'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_AreaTotalPrice'+rowNo).value) + parseFloat(document.getElementById('txt_PlantTotalPrice'+rowNo).value)).toFixed(2);
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
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";

$decrp = new custodian_encryp;
$page=new Template();

$act=$decrp->decrypt($_GET["act"]);
$DocID=$decrp->decrypt($_GET["id"]);

if ( !empty($_GET['ati']) && !empty($_GET['rdm']) ){
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);

	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	$sql = mysql_query($query);
	$num = mysql_num_rows($sql);
	if ($num==0)
		echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}


// Cek apakah user berikut memiliki hak untuk approval
$cApp_query="SELECT DISTINCT dra.A_ApproverID
		  	 FROM TH_RegistrationOfLandAcquisitionDocument throld, M_Approval dra
			 WHERE throld.THRGOLAD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=throld.THRGOLAD_RegistrationCode
			 AND throld.THRGOLAD_ID='$DocID'";
$cApp_sql=mysql_query($cApp_query);
$approver=mysql_num_rows($cApp_sql);

$appQuery=(($act=='approve')&&($approver=="1"))?"AND dra.A_ApproverID='$mv_UserID'":"AND dra.A_Status='2'";

$query = "SELECT DISTINCT thrgolad.THRGOLAD_ID,
						  thrgolad.THRGOLAD_RegistrationCode,
						  thrgolad.THRGOLAD_RegistrationDate,
		  				  u.User_ID,
						  u.User_FullName,
						  thrgolad.THRGOLAD_RegStatus,
						  thrgolad.THRGOLAD_Information,
		  				  thrgolad.THRGOLAD_RegStatusReason,
						  c.Company_ID,
						  c.Company_Name,
						  c.Company_Area,
						  thrgolad.THRGOLAD_Phase,
		  	   			  thrgolad.THRGOLAD_Period,
						  thrgolad.THRGOLAD_Revision,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dra.A_ApproverID) waitingApproval
		  FROM TH_RegistrationOfLandAcquisitionDocument thrgolad
		  LEFT JOIN M_User u
			ON thrgolad.THRGOLAD_UserID=u.User_ID
		  LEFT JOIN M_Company c
			ON thrgolad.THRGOLAD_CompanyID=c.Company_ID
		  LEFT JOIN M_Approval dra
			ON dra.A_TransactionCode=thrgolad.THRGOLAD_RegistrationCode
			$appQuery
		  WHERE thrgolad.THRGOLAD_Delete_Time is NULL
		  AND thrgolad.THRGOLAD_ID='$DocID'
		  ORDER BY waitingApproval DESC";
$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$fregdate=date("j M Y", strtotime($arr['THRGOLAD_RegistrationDate']));
$regUser=$arr['User_ID'];
$fperioddate=date("j M Y", strtotime($arr['THRGOLAD_Period']));
$f1perioddate=date("m/d/Y", strtotime($arr['THRGOLAD_Period']));

// Cek apakah Staff Custodian atau bukan.
// Staff Custodian memiliki wewenang untuk print registrasi dokumen.
$cs_query = "SELECT *
			 FROM M_DivisionDepartmentPosition ddp, M_Department d
			 WHERE ddp.DDP_DeptID=d.Department_ID
			 AND ddp.DDP_UserID='$mv_UserID'
			 AND d.Department_Name LIKE '%Custodian%'";
$cs_sql = mysql_query($cs_query);
$custodian = mysql_num_rows($cs_sql);

// Cek apakah Administrator atau bukan.
// Administrator memiliki hak untuk upload softcopy & edit dokumen.
$query = "SELECT *
		  FROM M_UserRole
		  WHERE MUR_RoleID='1'
		  AND MUR_UserID='$mv_UserID'
		  AND MUR_Delete_Time IS NULL";
$sql = mysql_query($query);
$admin = mysql_num_rows($sql);

$MainContent ="
<form name='DLA_detail' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&(($custodian==1)||($admin=="1")))
	$MainContent .="<th colspan=3>Persetujuan Pendaftaran Dokumen Pembebasan Lahan</th>";
else
	$MainContent .="<th colspan=3>Pendaftaran Dokumen Pembebasan Lahan</th>";

if((($arr['THRGOLAD_RegStatus']=="accept")||($arr['THRGOLAD_RegStatus']=="waiting")) && ((($custodian==1)||($admin=="1")) || ($regUser==$mv_UserID))){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='67%'>
			<input name='txtTHRGOLAD_ID' type='hidden' value='$arr[THRGOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRGOLAD_RegistrationCode]'/>
			$arr[THRGOLAD_RegistrationCode]
		</td>
		<td width='3%'>
			<a href='print-registration-of-land-acquisition-document.php?id=$arr[THRGOLAD_RegistrationCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$documentRevision=($arr['THRGOLAD_Revision']<>"0")?"(Revisi $arr[THRGOLAD_Revision])":"";
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='70%'colspan='2'>
			<input name='txtTHRGOLAD_ID' type='hidden' value='$arr[THRGOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRGOLAD_RegistrationCode]'/>
			$arr[THRGOLAD_RegistrationCode] $documentRevision
		</td>
	</tr>";
}

$MainContent .="
<tr>
	<td>Tanggal Pendaftaran</td>
	<td colspan='2'><input name='txtDL_RegTime' id='txtDL_RegTime' type='hidden' value='$arr[THRGOLAD_RegistrationDate]'>$fregdate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'><input name='txtDL_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<!--<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'>-->
		<input type='hidden' name='txtCompany_Area' value='$arr[Company_Area]' readonly='true' class='readonly' />
		<input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/><!--$arr[Company_Name]
	</td>
</tr>
<tr>
	<td>Tahap</td>
	<td colspan='2'>-->";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {
	$MainContent .="
		<input name='txtTHRGOLAD_Phase' id='txtTHRGOLAD_Phase' type='hidden' value='$arr[THRGOLAD_Phase]' size='3'>
		<input name='txtTHRGOLAD_PhaseR' id='txtTHRGOLAD_PhaseR' type='hidden' value='$arr[THRGOLAD_Phase]' size='3' readonly='readonly' class='readonly' style='display:none;'>";
}else {
	$MainContent .="
		<input name='txtTHRGOLAD_Phase' type='hidden' value='$arr[THRGOLAD_Phase]'>";//$arr[THRGOLAD_Phase]";
}

// $MainContent .="
// 	</td>
// </tr>
// <tr>
// 	<td>Periode</td>
// 	<td colspan='2'>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1))) {
	$MainContent .="
		<input name='txtTHRGOLAD_Period' id='txtTHRGOLAD_Period' type='hidden' value='$f1perioddate' size='7' onclick=\"javascript:NewCssCal('txtTHRGOLAD_Period', 'MMddyyyy');\">
		<input name='txtTHRGOLAD_PeriodR' id='txtTHRGOLAD_PeriodR' type='hidden' value='$f1perioddate' size='7' readonly='readonly' class='readonly' style='display:none;' />";
}else {
	$MainContent .="
		<input name='txtTHRGOLAD_Period' type='hidden' value='$arr[THRGOLAD_Period]'>";//$fperioddate";
}

$MainContent .="
	<!--</td>
</tr>-->
<tr>
	<td>Revisi</td>
	<td colspan='2'><input name='txtTHRGOLAD_Revision' id='txtTHRGOLAD_Revision' type='hidden' value='$arr[THRGOLAD_Revision]'>$arr[THRGOLAD_Revision]</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))){
	$MainContent .="
		<textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'>$arr[THRGOLAD_Information]</textarea>";
}else {
	$MainContent .="
		<input type='hidden' name='txtTHRGOLAD_Information' value='$arr[THRGOLAD_Information]' />$arr[THRGOLAD_Information]";
}
$MainContent .="</td></tr>";

// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHRGOLAD_RegStatus' id='optTHRGOLAD_RegStatus'>
				<option value='0'>--- Menunggu Persetujuan ---</option>";
		$query1="SELECT *
				 FROM M_DocumentRegistrationStatus
				 WHERE (DRS_Name <> '' AND DRS_Name <> 'waiting')
				 AND DRS_Delete_Time is NULL";
		$sql1 = mysql_query($query1);

		while ($field1=mysql_fetch_array($sql1)) {
			if ($field1[DRS_ID]==3)
				$MainContent .="<option value='$field1[DRS_ID]'>Setuju</option>";
			else if ($field1[DRS_ID]==4)
				$MainContent .="<option value='$field1[DRS_ID]'>Tolak</option>";
		}
	$MainContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Keterangan Persetujuan</td>
		<td colspan='2'>
			<textarea name='txtTHRGOLAD_RegStatusReason' id='txtTHRGOLAD_RegStatusReason' cols='50' rows='2'>$arr[THRGOLAD_RegStatusReason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";
	if($arr['THRGOLAD_RegStatus']=="waiting"){
		$MainContent .="
		<td colspan='2'><input type='hidden' name='txtTHRGOLAD_RegStatus' value='$arr[THRGOLAD_RegStatus]'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr['THRGOLAD_RegStatus']=="accept") {
		$MainContent .="
			<td colspan='2'><input type='hidden' name='txtTHRGOLAD_RegStatus' value='$arr[THRGOLAD_RegStatus]'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THRGOLAD_RegStatusReason]</td>
		</tr>";
	}else if($arr['THRGOLAD_RegStatus']=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THRGOLAD_RegStatusReason]</td>
		</tr>
		";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {
	$MainContent .="
	<tr id='rowUpload' style='display:none;'>
		<td>Upload File Excel</td>
		<td colspan=2>
			<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
			<a href='./sample/SampleExcelRegLADoc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
		</td>
	</tr>";

	/*if((($arr['THRGOLAD_RegStatus']=="accept") || ($arr['THRGOLAD_RegStatus']=="waiting"))&&($act<>'approve')) {
		$MainContent .="
		<th colspan=20>
			<input name='add Detail' type='button' value='Tambah Detail' class='button' onclick=\"showUpload();\"/>
		</th>";
	}*/
	if(($act=='approve') && $custodian <> 1) {
		$MainContent .="
		<th colspan=20>
			<input name='add Detail' type='button' value='Tambah Detail' class='button' onclick=\"showUpload();\"/>
		</th>";
	}

	$MainContent .="
	</table>
	<table width='2220' id='row1' class='stripeMe' border=1>
		<input type=hidden name='maxValueA' id='maxValueA'>
		<input type=hidden name='jKelengkapanA' id='jKelengkapanA'>
	</table>
	<table width='2220'>
	<th colspan='50' id='rowButton' style='display:none;'>
		<input name='saveUpload' id='saveUpload' type='submit' value='Tambah Detail' class='button' onclick='return validateInputDetail(this);'/>
		<input name='cancelUpload' id='cancelUpload' type='submit' value='Batal' class='button'/>
	</th>";
}
$MainContent .="</table>";


$query="SELECT *
		FROM M_LandAcquisitionAttribute
		WHERE LAA_Delete_Time is NULL
		ORDER BY LAA_ID ";
$sql = mysql_query($query);
$counts=mysql_num_rows($sql);

// DETAIL DOKUMEN GRL
$MainContent .="
<div id='headerdetailtable' class='detail-title'>Daftar Dokumen</div>";
$query_get_company = "SELECT DISTINCT
			CASE WHEN td.TDRGOLAD_Core_CompanyID IS NOT NULL
				THEN td.TDRGOLAD_Core_CompanyID
				ELSE th.THRGOLAD_CompanyID
			END AS company_id,
			CASE WHEN td.TDRGOLAD_Core_CompanyID IS NOT NULL
				THEN (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = td.TDRGOLAD_Core_CompanyID
					)
				ELSE (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = th.THRGOLAD_CompanyID
					)
			END AS company_name,
			CASE WHEN td.TDRGOLAD_Core_Phase IS NOT NULL
				THEN td.TDRGOLAD_Core_Phase
				ELSE th.THRGOLAD_Phase
			END AS tahap,
			td.TDRGOLAD_Core_CompanyID
		FROM TD_RegistrationOfLandAcquisitionDocument td
		LEFT JOIN TH_RegistrationOfLandAcquisitionDocument th
			ON td.TDRGOLAD_THRGOLAD_ID = th.THRGOLAD_ID
		WHERE td.TDRGOLAD_THRGOLAD_ID='$DocID' AND td.TDRGOLAD_Delete_Time IS NULL";
	$sql_gc = mysql_query($query_get_company);
	$header_ke = 0;
	$row_ke = 1;
	$array_row_ke = array();
	while($arr_gc = mysql_fetch_array($sql_gc)){
		$header_ke++;
		$MainContent .= "<table width='100%' id='mytable' class='stripeMe'>
			<tr>
				<th width='20%'>Perusahaan</th>
				<td width='80%'>
					<input type='hidden' name='txtCore_CompanyID' value='".$arr_gc['company_id']."'>
					".$arr_gc['company_name']."
				</td>
			</tr>
			<tr>
				<th width='20%'>Tahap</th>
				<td width='80%'>
					<input type='hidden' name='txtCore_Phase' value='".$arr_gc['tahap']."'>
					".$arr_gc['tahap']."
				</td>
			</tr>
			<tr>
				<td><input type='hidden' id='flag_detail".$header_ke."' value='0' /></td>
				<td><a class='btn-show-detail' onclick='show_tbl_detail(\"".$header_ke."\")' id='btn-show-detail".$header_ke."'>Show</a>
			</tr>
		</table>
		";

		$query_additional = "";
		if($arr_gc['TDRGOLAD_Core_CompanyID'] != null){
			$query_additional = "AND TDRGOLAD_Core_CompanyID='$arr_gc[TDRGOLAD_Core_CompanyID]'";
		}
		if($arr_gc['tahap'] != null){
			$query_additional .= " AND TDRGOLAD_Core_Phase='$arr_gc[tahap]'";
		}
		
		// DETAIL DOKUMEN
		$MainContent .="
		<table width='100%' id='detail$header_ke' class='stripeMe' style='display:none;padding-bottom:10px;'>
		<tr>
		   	<th rowspan='2'>No</th>
		    <th rowspan='2'>Tanggal</th>
		    <th rowspan='2'>Revisi</th>
		    <th rowspan='2'>Blok</th>
		    <th rowspan='2'>Desa</th>
		    <th rowspan='2'>Nama Pemilik</th>
		    <th rowspan='2'>Kelas</th>
		    <th colspan='3'>Lahan</th>
		   	<th rowspan='2'>Kelas</th>
		    <th colspan='3'>Tanam Tumbuh</th>
		   	<th rowspan='2'>Total</th>
			<th rowspan='2'>Keterangan</th>
			<th colspan='$counts'>Kelengkapan Dokumen</th>
		</tr>
		<tr>
			<th>Ha</th>
			<th>Rp/Ha</th>
			<th>Nilai (Rp)</th>
			<th>Qty</th>
			<th>Rp/Pkk</th>
			<th>Nilai (Rp)</th>";
			$query_atribute="SELECT *
					FROM M_LandAcquisitionAttribute
					WHERE LAA_Delete_Time is NULL
					ORDER BY LAA_ID ";
			$sql_atribute = mysql_query($query_atribute);
			while ($arr_atribute = mysql_fetch_array($sql_atribute)){
				$MainContent .="
				<th>$arr_atribute[LAA_ID]</th>";
			}
		$MainContent .="</tr>";

		$query = "SELECT DISTINCT tdrgolad.TDRGOLAD_ID, tdrgolad.TDRGOLAD_DocDate, tdrgolad.TDRGOLAD_Block,
								  tdrgolad.TDRGOLAD_Village, tdrgolad.TDRGOLAD_Owner, tdrgolad.TDRGOLAD_AreaClass,
								  tdrgolad.TDRGOLAD_AreaPrice, tdrgolad.TDRGOLAD_AreaStatement,
								  tdrgolad.TDRGOLAD_AreaTotalPrice, tdrgolad.TDRGOLAD_PlantClass,
								  tdrgolad.TDRGOLAD_PlantQuantity, tdrgolad.TDRGOLAD_PlantPrice,
								  tdrgolad.TDRGOLAD_PlantTotalPrice, tdrgolad.TDRGOLAD_GrandTotal,
								  tdrgolad.TDRGOLAD_Information, tdrgolad.TDRGOLAD_Revision
				  FROM TD_RegistrationOfLandAcquisitionDocument tdrgolad, TH_RegistrationOfLandAcquisitionDocument thrgolad
				  WHERE tdrgolad.TDRGOLAD_THRGOLAD_ID='$DocID'
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL
				  $query_additional
				  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$no=0;
		
		while ($arr = mysql_fetch_array($sql)) {
			$no++;
			$array_row_ke["count_row_per_pt".$header_ke] = $no;
			
			if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {
				$fdocdate=date("m/d/Y", strtotime($arr['TDRGOLAD_DocDate']));

				$MainContent .="
				<tr>
					<td class='center'>
						<input type='hidden' name='corePT".$header_ke."' id='corePT".$header_ke."' value='".$arr_gc['company_id']."' />
						<input type='hidden' name='corePhase".$header_ke."' id='corePhase".$header_ke."' value='".$arr_gc['tahap']."' />
						<input type='hidden' name='txtTDRGOLAD_ID".$header_ke."[]' value='$arr[TDRGOLAD_ID]'/>$no
					</td>
					<td class='center'><input name='txtTDRGOLAD_DocDate".$header_ke."[]' id='txtTDRGOLAD_DocDate$no' type='text' value='$fdocdate' onclick=\"javascript:NewCssCal('txtTDRGOLAD_DocDate$no', 'MMddyyyy');\" size='7'></td>
					<td class='center'>$arr[TDRGOLAD_Revision]</td>
					<td class='center'><input name='txtTDRGOLAD_Block".$header_ke."[]' id='txtTDRGOLAD_Block$no' type='text' value='$arr[TDRGOLAD_Block]'></td>
					<td class='center'><input name='txtTDRGOLAD_Village".$header_ke."[]' id='txtTDRGOLAD_Village$no' type='text' value='$arr[TDRGOLAD_Village]'></td>
					<td class='center'><input name='txtTDRGOLAD_Owner".$header_ke."[]' id='txtTDRGOLAD_Owner$no' type='text' value='$arr[TDRGOLAD_Owner]'></td>
					<td class='center'><input name='txtTDRGOLAD_AreaClass".$header_ke."[]' id='txtTDRGOLAD_AreaClass$no' type='text' value='$arr[TDRGOLAD_AreaClass]' size='3'></td>
					<td class='center'><input name='txtTDRGOLAD_AreaStatement".$header_ke."[]' id='txtTDRGOLAD_AreaStatement$no' type='text' value='$arr[TDRGOLAD_AreaStatement]' size='5' onchange='countTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_AreaPrice".$header_ke."[]' id='txtTDRGOLAD_AreaPrice$no' type='text' value='$arr[TDRGOLAD_AreaPrice]' size='10' onchange='countTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_AreaTotalPrice".$header_ke."[]' id='txtTDRGOLAD_AreaTotalPrice$no' type='text' value='$arr[TDRGOLAD_AreaTotalPrice]' size='10' onchange='countGrandTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_PlantClass".$header_ke."[]' id='txtTDRGOLAD_PlantClass$no' type='text' value='$arr[TDRGOLAD_PlantClass]' size='3'></td>
					<td class='center'><input name='txtTDRGOLAD_PlantQuantity".$header_ke."[]' id='txtTDRGOLAD_PlantQuantity$no' type='text' value='$arr[TDRGOLAD_PlantQuantity]' size='5' onchange='countTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_PlantPrice".$header_ke."[]' id='txtTDRGOLAD_PlantPrice$no' type='text' value='$arr[TDRGOLAD_PlantPrice]' size='10' onchange='countTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_PlantTotalPrice".$header_ke."[]' id='txtTDRGOLAD_PlantTotalPrice$no' type='text' value='$arr[TDRGOLAD_PlantTotalPrice]' size='10' onchange='countGrandTotal($no);'></td>
					<td class='center'><input name='txtTDRGOLAD_GrandTotal".$header_ke."[]' id='txtTDRGOLAD_GrandTotal$no' type='text' value='$arr[TDRGOLAD_GrandTotal]' size='10' ></td>
					<td class='center'><textarea name='txtTDRGOLAD_Information".$header_ke."[]' id='txtTDRGOLAD_Information$no'>$arr[TDRGOLAD_Information]</textarea></td>";

					$at_query = "SELECT laas.LAAS_Symbol,laas.LAAS_ID
							  FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd, M_LandAcquisitionAttributeStatus laas
							  WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$arr[TDRGOLAD_ID]'
							  AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
							  AND tdrgoladd.TDRGOLADD_AttributeStatusID=laas.LAAS_ID
							  ORDER BY tdrgoladd.TDRGOLADD_AttibuteID";
					$at_sql = mysql_query($at_query);
					$idKelengkapan=1;
					while (($at_arr = mysql_fetch_array($at_sql))&&($idKelengkapan<'15')) {
						$row=$arr['TDRGOLAD_ID'];
						$MainContent .="<td class='center'>
											<select name='optKelengkapan$row$idKelengkapan'>";
						$s_query="SELECT *
						 		  FROM M_LandAcquisitionAttributeStatus
						 		  WHERE LAAS_Delete_Time IS NULL";
						$s_sql=mysql_query($s_query);
						while ($s_arr=mysql_fetch_array($s_sql)) {
							$selected=($at_arr['LAAS_ID']==$s_arr['LAAS_ID'])?"selected='selected'":"";
							$MainContent .="
								<option value='$s_arr[LAAS_ID]' $selected>$s_arr[LAAS_Symbol]</option>";
						}
						$MainContent .="</select></td>";
						$idKelengkapan++;
					}
				$MainContent .="</tr>";
				//$no=$no+1;
			}else {
				$fdocdate=date("j M Y", strtotime($arr['TDRGOLAD_DocDate']));

				$MainContent .="
				<tr>
					<td class='center'>
						<input type='hidden' name='corePT".$header_ke."' id='corePT".$header_ke."' value='".$arr_gc['company_id']."' />
						<input type='hidden' name='corePhase".$header_ke."' id='corePhase".$header_ke."' value='".$arr_gc['tahap']."' />
						<input type='hidden' name='txtTDRGOLAD_ID".$header_ke."[]' value='$arr[TDRGOLAD_ID]'/>$no
					</td>
					<td class='center'><input name='txtTDRGOLAD_DocDate".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_DocDate]'>$fdocdate</td>
					<td class='center'><input type='hidden' name='txtTDRGOLAD_Revision".$header_ke."[]' value='$arr[TDRGOLAD_Revision]'/>$arr[TDRGOLAD_Revision]</td>
					<td class='center'><input name='txtTDRGOLAD_Block".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_Block]'>$arr[TDRGOLAD_Block]</td>
					<td class='center'><input name='txtTDRGOLAD_Village".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_Village]'>$arr[TDRGOLAD_Village]</td>
					<td class='center'><input name='txtTDRGOLAD_Owner".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_Owner]'>$arr[TDRGOLAD_Owner]</td>
					<td class='center'><input name='txtTDRGOLAD_AreaClass".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_AreaClass]'>$arr[TDRGOLAD_AreaClass]</td>
					<td class='center'><input name='txtTDRGOLAD_AreaStatement".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_AreaStatement]'>$arr[TDRGOLAD_AreaStatement]</td>
					<td class='center'><input name='txtTDRGOLAD_AreaPrice".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_AreaPrice]'>$arr[TDRGOLAD_AreaPrice]</td>
					<td class='center'><input name='txtTDRGOLAD_AreaTotalPrice".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_AreaTotalPrice]'>$arr[TDRGOLAD_AreaTotalPrice]</td>
					<td class='center'><input name='txtTDRGOLAD_PlantClass".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_PlantClass]'>$arr[TDRGOLAD_PlantClass]</td>
					<td class='center'><input name='txtTDRGOLAD_PlantQuantity".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_PlantQuantity]'>$arr[TDRGOLAD_PlantQuantity]</td>
					<td class='center'><input name='txtTDRGOLAD_PlantPrice".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_PlantPrice]'>$arr[TDRGOLAD_PlantPrice]</td>
					<td class='center'><input name='txtTDRGOLAD_PlantTotalPrice".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_PlantTotalPrice]'>$arr[TDRGOLAD_PlantTotalPrice]</td>
					<td class='center'><input name='txtTDRGOLAD_GrandTotal".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_GrandTotal]'>$arr[TDRGOLAD_GrandTotal]</td>
					<td class='center'><input name='txtTDRGOLAD_Information".$header_ke."[]' type='hidden' value='$arr[TDRGOLAD_Information]'>$arr[TDRGOLAD_Information]</td>";
					$at_query = "SELECT laas.LAAS_Symbol,laas.LAAS_ID
							  FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd, M_LandAcquisitionAttributeStatus laas
							  WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$arr[TDRGOLAD_ID]'
							  AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
							  AND tdrgoladd.TDRGOLADD_AttributeStatusID=laas.LAAS_ID
							  ORDER BY tdrgoladd.TDRGOLADD_AttibuteID";
					$at_sql = mysql_query($at_query);
					$idKelengkapan=1;
					while (($at_arr = mysql_fetch_array($at_sql))&&($idKelengkapan<'15')) {
						$row=$arr['TDRGOLAD_ID'];
						$MainContent .="<td class='center'><input name='optKelengkapan$row$idKelengkapan' type='hidden' value='$at_arr[LAAS_ID]'>$at_arr[LAAS_Symbol]</td>";
						$idKelengkapan++;
					}
				$MainContent .="</tr>";
				// $no=$no+1;
			}
			// $no=$no+1;
			$MainContent .="
				<input type='hidden' name='jRow' id='jRow' value='$no'/>
				<input type='hidden' name='rowTerakhir' id='rowTerakhir' value='$row_ke'/>";
			$row_ke=$row_ke+1;
		}
		$MainContent .="<input type='hidden' name='jPT' id='jPT' value='$header_ke' />
		</table>";
		// $header_ke++;
	}
	$MainContent .="<input type='hidden' name='count_core_companyid' id='count_core_companyid' value='".$header_ke."'/>";
	for($o = 1; $o <= $header_ke; $o++){
		$jumlah_row_per_pt = $array_row_ke["count_row_per_pt".$o];
		$MainContent .="<input type='hidden' name='count_row_per_pt".$o."' id='count_row_per_pt".$o."' value='".$jumlah_row_per_pt."'/>";
	}

$MainContent .="
<input type=hidden name='maxValue' id='maxValue' value='$no'>
<input type=hidden name='jKelengkapan' id='jKelengkapan' value='$idKelengkapan'>
</table>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1"))) {
	$MainContent .="
	<table width='100%' id='button'>
	<th>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>";
}
$MainContent .="</form>";


/* ACTIONS */
if(isset($_POST['cancel'])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}
elseif(isset($_POST['cancelUpload'])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}
if(isset($_POST['edit'])) {
	//Update Header
	$txtTHRGOLAD_Information=str_replace("<br>", "\n",$_POST[txtTHRGOLAD_Information]);
	$txtPerDate=date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Period']));
	$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
			  SET THRGOLAD_Phase='$_POST[txtTHRGOLAD_Phase]', THRGOLAD_Period='$txtPerDate',
			      THRGOLAD_Information='$txtTHRGOLAD_Information',
			      THRGOLAD_Update_Time=sysdate(), THRGOLAD_Update_UserID='$mv_UserID'
			  WHERE THRGOLAD_RegistrationCode='$_POST[txtA_TransactionCode]'";
	$mysqli->query($query);

	$count=$_POST['rowTerakhir'];

	//Update Detail
	$count=$_POST[maxValue];
	$jKelengkapan=$_POST[jKelengkapan];

	$count_company = $_POST['count_core_companyid'];
	
	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['corePT'.$c];
		foreach($_POST['txtTDRGOLAD_DocDate'.$c] as $key => $value){

			$txtTDRGOLAD_ID=$_POST["txtTDRGOLAD_ID".$c][$key];
			$txtTDRGOLAD_DocDate=$_POST["txtTDRGOLAD_DocDate".$c][$key];
			$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));
			$txtTDRGOLAD_Block=$_POST["txtTDRGOLAD_Block".$c][$key];
			$txtTDRGOLAD_Village=$_POST["txtTDRGOLAD_Village".$c][$key];
			$txtTDRGOLAD_Owner=$_POST["txtTDRGOLAD_Owner".$c][$key];
			$txtTDRGOLAD_AreaClass=$_POST["txtTDRGOLAD_AreaClass".$c][$key];
			$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaStatement".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaStatement".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaStatement".$c][$key];
			$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaPrice".$c][$key];
			$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key];
			$txtTDRGOLAD_PlantClass=$_POST["txtTDRGOLAD_PlantClass".$c][$key];
			$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantQuantity".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantQuantity".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantQuantity".$c][$key];
			$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantPrice".$c][$key];
			$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key];
			$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txtTDRGOLAD_GrandTotal".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_GrandTotal".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_GrandTotal".$c][$key];
			$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRGOLAD_Information".$c][$key]);

			$sql1= "UPDATE TD_RegistrationOfLandAcquisitionDocument
					SET TDRGOLAD_DocDate='$txtTDRGOLAD_DocDate',
						TDRGOLAD_Block='$txtTDRGOLAD_Block',
					    TDRGOLAD_Village='$txtTDRGOLAD_Village',
						TDRGOLAD_Owner='$txtTDRGOLAD_Owner',
						TDRGOLAD_AreaClass='$txtTDRGOLAD_AreaClass',
						TDRGOLAD_AreaStatement=REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
						TDRGOLAD_AreaPrice=REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
						TDRGOLAD_AreaTotalPrice=REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
						TDRGOLAD_PlantClass='$txtTDRGOLAD_PlantClass',
						TDRGOLAD_PlantQuantity=REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
						TDRGOLAD_PlantPrice=REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
						TDRGOLAD_PlantTotalPrice=REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
						TDRGOLAD_GrandTotal=REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
						TDRGOLAD_Information='$txtTDRGOLAD_Information',
						TDRGOLAD_Update_UserID='$mv_UserID',
						TDRGOLAD_Update_Time=sysdate()
					WHERE TDRGOLAD_ID='$txtTDRGOLAD_ID' ";
			if($mysqli->query($sql1)){
				for ($j=1 ; $j<$jKelengkapan ; $j++) {
					$optKelengkapan=$_POST["optKelengkapan".$txtTDRGOLAD_ID.$j];

					$k_sql="UPDATE TD_RegistrationOfLandAcquisitionDocumentDetail
							SET TDRGOLADD_AttributeStatusID='$optKelengkapan',
								TDRGOLADD_Update_UserID='$mv_UserID',
								TDRGOLADD_Update_Time=sysdate()
							WHERE TDRGOLADD_TDRGOLAD_ID='$txtTDRGOLAD_ID'
							AND TDRGOLADD_AttibuteID='$j'";
					$mysqli->query($k_sql);
				}
			}
		}
	}
	if ($_POST['optTHRGOLAD_RegStatus']){
		$A_TransactionCode=$_POST['txtA_TransactionCode'];
		$A_ApproverID=$mv_UserID;
		$A_Status=$_POST['optTHRGOLAD_RegStatus'];
		$THRGOLAD_RegStatusReason=str_replace("<br>", "\n",$_POST['txtTHRGOLAD_RegStatusReason']);

		// MENCARI TAHAP APPROVAL USER TERSEBUT
		$query = "SELECT *
					FROM M_Approval
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_ApproverID='$A_ApproverID' AND A_ApprovalDate IS NULL";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		$step=$arr['A_Step'];
		$AppDate=$arr['A_ApprovalDate'];

		if (!empty($arr) && $AppDate==NULL) {

			// MENCARI JUMLAH APPROVAL
			$query = "SELECT MAX(A_Step) AS jStep
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$jStep=$arr['jStep'];

			// UPDATE APPROVAL
			if ($A_Status == '3') {
				if ($step <> $jStep) {
					$query = "UPDATE M_Approval
						SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
						WHERE A_TransactionCode='$A_TransactionCode' AND A_ApproverID='$A_ApproverID'";
					$sql = mysql_query($query);
				}
			}

			// PROSES BILA "SETUJU"
			if ($A_Status == '3') {
				// CEK APAKAH MERUPAKAN APPROVAL FINAL
				if ($step <> $jStep) {
					$nStep=$step+1;

					$aComp = $_POST['txtCompany_Area'];

					$jenis = "10"; //Dokumen Lainnya (Legal) - Semua Tipe Dokumen

					for ($i=$nStep; $i<=$jStep; $i++) {
						$j = $i + 1;
						$query = "
						SELECT rads.RADS_StatusID, ma.A_ApproverID
						FROM M_Approval ma
						JOIN M_Role_ApproverDocStepStatus rads
							ON ma.A_Step = rads.RADS_StepID
						LEFT JOIN M_Role_Approver ra
							ON rads.RADS_RA_ID = ra.RA_ID
						WHERE ma.A_Step = '{$i}'
							AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$aComp}')
							AND ma.A_TransactionCode = '{$A_TransactionCode}'
							AND rads.RADS_DocID = '{$jenis}'
							AND rads.RADS_ProsesID = '1'
						"; //Arief F - 24082018
						$result = mysql_fetch_array(mysql_query($query));

						if ($result['RADS_StatusID'] == '1') {
							//echo 'Step : ' . $i . ' => Kirim Email Approval<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									if ($xsql = mysql_query($xquery)) {
										mail_registration_doc($A_TransactionCode);
									}
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_registration_doc($A_TransactionCode);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/

							/*if ($i == $jStep) {
								$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
									AND THRGOLAD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3, 1 );
									mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
								}
							}*/
							break;
						} else if ($result['RADS_StatusID'] == '2') {
							//echo 'Step : ' . $i . ' => Kirim Email Notifikasi<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));

							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									$xsql = mysql_query($xquery);
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/

							/*if ($i == $jStep) {
								$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
									AND THRGOLAD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3, 1 );
									mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
								}
							}*/
						} else;
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";

					// UPDATE APPROVAL
					/*$query = "UPDATE M_Approval
								SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
									A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_ApproverID='$A_ApproverID'";
					$sql = mysql_query($query);

					$nStep=$step+1;
					$query = "UPDATE M_Approval
								SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_Step='$nStep'";
					if ($sql = mysql_query($query)) {
						mail_registration_doc($A_TransactionCode);
						if($step=='1'){
							mail_notif_registration_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3 );
							mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
						}
						echo "<meta http-equiv='refresh' content='0; url=home.php'>";
					}*/
				} else {
					$jumlahRow=$_POST['rowTerakhir'];
					// echo $jumlahRow."<br>";
					$jumlahPT=$_POST['jPT'];

					$array_company_id = array();
					for($i = 1; $i <= $jumlahPT; $i++){
						$Core_CompanyID = $_POST['corePT'.$i];
						
						$query_get_company="SELECT Company_ID, Company_Name, Company_Code
							FROM M_Company
							WHERE Company_ID='$Core_CompanyID'";

						$sql_gc = mysql_query($query_get_company);
						$dgc = mysql_fetch_array($sql_gc);
						if( !in_array($dgc['Company_ID'], $array_company_id) ){
							$array_company_id['company_id'][] = $dgc['Company_ID'];
							$array_company_id['company_name'][] = $dgc['Company_Name']." - ".$dgc['Company_Code'];
							$array_company_id['banyak'][] = 1;
						}else{
							$index = array_search($dgc['Company_ID'], $array_company_id['company_id']);
						
							$array_company_id['banyak'][$index] = $array_company_id['banyak'][$index]+1;
						}
					}
					// exit();
					$lokasi_dokumen_kosong = 0;
					
					for($n = 0; $n < count($array_company_id['company_id']); $n++){
						$company_id = $array_company_id['company_id'][$n];
						// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
						$query = "SELECT *
								  FROM L_DocumentLocation
								  WHERE DL_Status='1'
								  AND DL_CompanyID='$company_id'
								  AND DL_DocGroupID='grl'
								  AND DL_Delete_Time IS NULL";
						$avLoc = mysql_num_rows(mysql_query($query));
						$array_company_id['banyak_ruang_tersedia'][$n] = $avLoc;

						
						if(!$avLoc || $avLoc<$array_company_id['banyak'][$n]){
							$array_company_id['ruang_tersedia'][$n] = "0"; //tidak
							$lokasi_dokumen_kosong++;
						}else{
							$array_company_id['ruang_tersedia'][$n] = "1"; //ya
						}
					}	

					

					// $jumlahRow=$_POST[maxValue];
					// // ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
					// $query = "SELECT *
					// 		  FROM L_DocumentLocation
					// 	  	WHERE DL_Status='1'
					// 	  	AND DL_CompanyID='$_POST[txtCompany_ID]'
					// 	  	AND DL_DocGroupID='grl'
					// 	  	AND DL_Delete_Time is NULL";
					// $sql = mysql_query($query);
					// $avLoc = mysql_num_rows($sql);

					// if((!$avLoc)||($avLoc<$jumlahRow)){
					if($lokasi_dokumen_kosong > 0){
						$pesan = "";
						for($z = 0; $z < count($array_company_id['company_id']); $z++){
							if($array_company_id['ruang_tersedia'][$z] == 0){
								$pesan .= "Lokasi untuk Dokumen ".$array_company_id['company_name'][$z]." Tidak Tersedia. Lokasi yang Tersedia : ".$array_company_id['banyak_ruang_tersedia'][$z].". ";
							}
						}
						?>
	                    <script language="JavaScript" type="text/JavaScript">

						alert("<?=$pesan?>\nHubungi Custodian System Administrator untuk Mengatur Lokasi dan Lakukan Persetujuan Ulang.");
						</script>
	                    <?PHP
						echo "<meta http-equiv='refresh' content='0; url=home.php'>";
					} else {
						// UPDATE APPROVAL
						$query = "UPDATE M_Approval
									SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
										A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode'
									AND A_ApproverID='$A_ApproverID'";
						if ($sql = mysql_query($query)){
							//mail_registration_doc($A_TransactionCode);
							//if($step=='1'){
								//if ($step != $jStep) {
								//	mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
								//}
							//}
						}

						//UPDATE STATUS REGISTRASI
						$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'";
						$sql = mysql_query($query);

						// ACTION UNTUK GENERATE NO DOKUMEN
						$regyear=date("y");
						$regmonth=date("m");
							$count_company = $_POST['count_core_companyid'];
						
						for ($c=1; $c<=$count_company; $c++) {
							$Core_CompanyID = $_POST['corePT'.$c];	
							$Core_Phase = $_POST['corePhase'.$c];
						
							// Cari Kode Perusahaan
							$query = "SELECT *
										FROM M_Company
										WHERE Company_ID='$Core_CompanyID'";
							$sql = mysql_query($query);
							$field = mysql_fetch_array($sql);
							$Company_Code=$field['Company_Code'];

							// Cari Kode Dokumen Grup
							$query = "SELECT *
										FROM M_DocumentGroup
										WHERE DocumentGroup_ID='3'";
							$sql = mysql_query($query);
							$field = mysql_fetch_array($sql);
							$DocumentGroup_Code=$field['DocumentGroup_Code'];

							// Cari No Dokumen Terakhir
							$query = "SELECT MAX(CD_SeqNo)
										FROM M_CodeDocument
										WHERE CD_Year='$regyear'
										AND CD_GroupDocCode='$DocumentGroup_Code'
										AND CD_CompanyCode='$Company_Code'
										AND CD_Delete_Time is NULL";
							$sql = mysql_query($query);
							$field = mysql_fetch_array($sql);

							if($field[0]==NULL)
								$maxnum=0;
							else
								$maxnum=$field[0];
							$nnum=$maxnum+1;


							$jKelengkapan=$_POST[jKelengkapan];

							foreach($_POST['txtTDRGOLAD_DocDate'.$c] as $key => $value){
								// Menentukan Lokasi Dokumen
								$query = "SELECT *
										  FROM L_DocumentLocation
									  	WHERE DL_Status='1'
									  	AND DL_CompanyID='$Core_CompanyID'
									  	AND DL_DocGroupID='grl'
									  	AND DL_Delete_Time is NULL
									  	AND DL_ID=(SELECT MIN(DL_ID)
													 FROM L_DocumentLocation
												 	WHERE DL_Status='1'
												 	AND DL_CompanyID='$Core_CompanyID'
												 	AND DL_DocGroupID='grl'
												 	AND DL_Delete_Time is NULL)";
												 	//echo $query . "<br>";
								$sql = mysql_query($query);
								$arr = mysql_fetch_array($sql);
								$DLIU_LocationCode=$arr[DL_Code];
								//echo $DLIU_LocationCode . "<br>";
								$txtDL_RegTime=$_POST['txtDL_RegTime'];
								$txtDL_RegUserID=$_POST['txtDL_RegUserID'];
								$txtCompany_ID=$_POST['txtCompany_ID'];
								$txtTHRGOLAD_Phase=$_POST['txtTHRGOLAD_Phase'];
								//$Core_CompanyID = $_POST['txtCore_CompanyID'.$c];

								//$Core_Phase = $_POST['txtCore_Phase'.$c];
								$txtTHRGOLAD_Period=date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Period']));
								$txtTDRGOLAD_ID=$_POST['txtTDRGOLAD_ID'.$c][$key];
								$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($_POST['txtTDRGOLAD_DocDate'.$c][$key]));
								$txtTDRGOLAD_Revision=$_POST['txtTDRGOLAD_Revision'.$c][$key];
								$txtTDRGOLAD_Block=$_POST['txtTDRGOLAD_Block'.$c][$key];
								$txtTDRGOLAD_Village=$_POST['txtTDRGOLAD_Village'.$c][$key];
								$txtTDRGOLAD_Owner=$_POST['txtTDRGOLAD_Owner'.$c][$key];
								$txtTDRGOLAD_AreaClass=$_POST['txtTDRGOLAD_AreaClass'.$c][$key];
								$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaStatement".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaStatement".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaStatement".$c][$key];
								$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaPrice".$c][$key];
								$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_AreaTotalPrice".$c][$key];
								$txtTDRGOLAD_PlantClass=$_POST["txtTDRGOLAD_PlantClass".$c][$key];
								$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantQuantity".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantQuantity".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantQuantity".$c][$key];
								$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantPrice".$c][$key];
								$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_PlantTotalPrice".$c][$key];
								$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txtTDRGOLAD_GrandTotal".$c][$key])=="")||(is_numeric($_POST["txtTDRGOLAD_GrandTotal".$c][$key])==false))?"0":$_POST["txtTDRGOLAD_GrandTotal".$c][$key];
								$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRGOLAD_Information".$c][$key]);

								$txtDLAA_LAAS_ID=$_POST['txtDLAA_LAAS_ID'.$c][$key];

								$query = "UPDATE L_DocumentLocation
										  SET DL_Status='0', DL_Update_UserID='$mv_UserID', DL_Update_Time=sysdate()
									  	WHERE DL_Code='$DLIU_LocationCode';";
								
								$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);
								$CD_Code_H="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
								$sql2= "INSERT INTO M_CodeDocument
										VALUES ('$CD_Code_H','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth',
												'$regyear','$mv_UserID', sysdate(),'$mv_UserID',
												sysdate(),NULL,NULL)";
								$mysqli->query($sql2); 

								// Memindahkan Pendaftaran Dokumen ke M_DocumentLandAcquisition
								$sql3= "INSERT INTO M_DocumentLandAcquisition
										VALUES (NULL,
												'$CD_Code_H',
												'$txtDL_RegUserID',
												'$txtDL_RegTime',
												'$Core_CompanyID',
												'$Core_Phase',
												'$txtTHRGOLAD_Period',
												'$txtTDRGOLAD_Revision',
												'$txtTDRGOLAD_DocDate',
												'$txtTDRGOLAD_Block',
												'$txtTDRGOLAD_Village',
												'$txtTDRGOLAD_Owner',
												'$txtTDRGOLAD_AreaClass',
												REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
												REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
												REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
												'$txtTDRGOLAD_PlantClass',
												REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
												REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
												REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
												REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
												'$txtTDRGOLAD_Information',
												'$DLIU_LocationCode','1', NULL,
												'$mv_UserID', sysdate(),'$mv_UserID',
												sysdate(),NULL,NULL);";
								
								if(($mysqli->query($sql3)) && ($mysqli->query($query)) ){
									
									$period=
									$s_sql="SELECT *
											FROM M_DocumentLandAcquisition
											WHERE DLA_Code='$CD_Code_H'";
									$s_query=mysql_query($s_sql);
									$s_arr=mysql_fetch_array($s_query);
									$DLA_ID=$s_arr['DLA_ID'];

									for ($j=1 ; $j<$jKelengkapan ; $j++) {
										$optKelengkapan=$_POST["optKelengkapan".$txtTDRGOLAD_ID.$j];
										$dnewnum=str_pad($j,2,"0",STR_PAD_LEFT);
										$CD_Code="$newnum$dnewnum$Company_Code$DocumentGroup_Code$regmonth$regyear";

										$i_sql="INSERT INTO M_DocumentLandAcquisitionAttribute
												VALUES (NULL,'$CD_Code','$DLA_ID','$j','$optKelengkapan',
														'1','$mv_UserID', sysdate(),'$mv_UserID',
														sysdate(),NULL,NULL)";
										$mysqli->query($i_sql);
									}
								}
								$nnum=$nnum+1;
							}
						}
						
						mail_notif_registration_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3, 1 );
						mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );

						echo "<meta http-equiv='refresh' content='0; url=home.php'>";
					}
				}
			}

			// PROSES BILA "TOLAK"
			if ($A_Status=='4') {
				// UPDATE APPROVAL
				$query = "UPDATE M_Approval
							SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
								A_Update_Time=sysdate()
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_ApproverID='$A_ApproverID'";
				$sql = mysql_query($query);

				//UPDATE STATUS REGISTRASI DOKUMEN
				$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
							SET THRGOLAD_RegStatus='reject', THRGOLAD_RegStatusReason='$THRGOLAD_RegStatusReason',
								THRGOLAD_Update_Time=sysdate(), THRGOLAD_Update_UserID='$A_ApproverID'
							WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'";

				$query1 = "UPDATE M_Approval
							SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
								A_Status='$A_Status'
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_Step>'$step'";
				if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
					$h_query="SELECT *
								  FROM TH_RegistrationOfLandAcquisitionDocument
								  WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
								  AND THRGOLAD_Delete_Time IS NULL";
					$h_sql=mysql_query($h_query);
					$h_arr=mysql_fetch_array($h_sql);
					mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 4 );

					$e_query="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$A_TransactionCode'
						  	AND A_Step<'$step' ";
					$e_sql=mysql_query($e_query);
					while ($e_arr=mysql_fetch_array($e_sql)){
						mail_notif_registration_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
				}
			}
		} else {
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}
	else {
		echo "<meta http-equiv='refresh' content='0; url=$PHP_SELF'>";
	}
}

if(isset($_POST['saveUpload'])) {
	$count=$_POST['maxValueA'];
	$jKelengkapan=$_POST['jKelengkapanA'];

	if ($_POST['txtTHRGOLAD_RegStatus']=="accept"){
				// ACTION UNTUK GENERATE NO DOKUMEN
				$regyear=date("y");
				$regmonth=date("m");

		$count_company = $_POST['count_core_companyid'];

		for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['optTHRGOLAD_Core_CompanyID'.$c];
		$Core_Phase = $_POST['txtTHRGOLAD_Core_Phase'.$c];

			// Cari Kode Perusahaan
			$query = "SELECT *
						FROM M_Company
						WHERE Company_ID='$Core_CompanyID'";
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);
			$Company_Code=$field['Company_Code'];

			// Cari Kode Dokumen Grup
			$query = "SELECT *
						FROM M_DocumentGroup
						WHERE DocumentGroup_ID='3'";
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);
			$DocumentGroup_Code=$field['DocumentGroup_Code'];

			// Cari No Dokumen Terakhir
			$query = "SELECT MAX(CD_SeqNo)
						FROM M_CodeDocument
						WHERE CD_Year='$regyear'
						AND CD_GroupDocCode='$DocumentGroup_Code'
						AND CD_CompanyCode='$Company_Code'
						AND CD_Delete_Time is NULL";
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);

			if($field[0]==NULL)
				$maxnum=0;
			else
				$maxnum=$field[0];
			$nnum=$maxnum+1;

			// ACTION UNTUK MENENTUKAN LOKASI DOKUMEN
			$query = "SELECT MIN(DL_ID) as minID
						FROM L_DocumentLocation
						WHERE DL_Status='1'
						AND DL_CompanyID='$_POST[txtCompany_ID]'
						AND DL_Delete_Time is NULL";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$nID=$arr[minID];
		
			// $Core_Period = date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Core_Period'.$c]));
			foreach($_POST['txtTDRGOLAD_Village'.$c] as $key => $value){
				// Menentukan Lokasi Dokumen
				$query = "SELECT DL_Code
						  FROM L_DocumentLocation
						  WHERE DL_ID='$nID'
						  AND DL_Delete_Time is NULL";
				$sql = mysql_query($query);
				$arr = mysql_fetch_array($sql);
				$DLIU_LocationCode=$arr[DL_Code];
				$query = "UPDATE L_DocumentLocation
						  SET DL_Status='0', DL_Update_UserID='$mv_UserID', DL_Update_Time=sysdate()
						  WHERE DL_Code='$DLIU_LocationCode';";
				$mysqli->query($query);

				$txtDL_RegUserID=$_COOKIE["User_ID"];
				$txtDL_RegTime=date('Y-m-d H:i:s');
				$txtCompany_ID=$Core_CompanyID;
				$txtTHRGOLAD_PhaseR=$_POST["txtTHRGOLAD_PhaseR"];
				$txtTHRGOLAD_PeriodR=$_POST["txtTHRGOLAD_PeriodR"];
				$txtTHRGOLAD_PeriodR=date('Y-m-d H:i:s', strtotime($txtTHRGOLAD_PeriodR));
				$txtTDRGOLAD_DocDate=$_POST["txt_DocDate".$c][$key];
				$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));
				$txtTDRGOLAD_Block=$_POST["txt_Block".$c][$key];
				$txtTDRGOLAD_Village=$_POST["txt_Village".$c][$key];
				$txtTDRGOLAD_Owner=$_POST["txt_Owner".$c][$key];
				$txtTDRGOLAD_AreaClass=$_POST["txt_AreaClass".$c][$key];
				$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txt_AreaStatement".$c][$key])=="")||(is_numeric($_POST["txt_AreaStatement".$c][$key])==false))?"0":$_POST["txt_AreaStatement".$c][$key];
				$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txt_AreaPrice".$c][$key])=="")||(is_numeric($_POST["txt_AreaPrice".$c][$key])==false))?"0":$_POST["txt_AreaPrice".$c][$key];
				$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txt_AreaTotalPrice".$c][$key])=="")||(is_numeric($_POST["txt_AreaTotalPrice".$c][$key])==false))?"0":$_POST["txt_AreaTotalPrice".$c][$key];
				$txtTDRGOLAD_PlantClass=$_POST["txt_PlantClass".$c][$key];
				$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txt_PlantQuantity".$c][$key])=="")||(is_numeric($_POST["txt_PlantQuantity".$c][$key])==false))?"0":$_POST["txt_PlantQuantity".$c][$key];
				$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txt_PlantPrice".$c][$key])=="")||(is_numeric($_POST["txt_PlantPrice".$c][$key])==false))?"0":$_POST["txt_PlantPrice".$c][$key];
				$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txt_PlantTotalPrice".$c][$key])=="")||(is_numeric($_POST["txt_PlantTotalPrice".$c][$key])==false))?"0":$_POST["txt_PlantTotalPrice".$c][$key];
				$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txt_GrandTotal".$c][$key])=="")||(is_numeric($_POST["txt_GrandTotal".$c][$key])==false))?"0":$_POST["txt_GrandTotal".$c][$key];
				$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txt_Information".$c][$key]);

				$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);
				$CD_Code_H="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
				$sql2= "INSERT INTO M_CodeDocument
						VALUES ('$CD_Code_H','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth',
								'$regyear','$mv_UserID', sysdate(),'$mv_UserID',
								sysdate(),NULL,NULL)";
				$mysqli->query($sql2);

				$sql1= "INSERT INTO M_DocumentLandAcquisition
						VALUES (NULL,'$CD_Code_H','$txtDL_RegUserID','$txtDL_RegTime','$Core_CompanyID',
								'$Core_Phase',
								'$txtTHRGOLAD_PeriodR','$_POST[txtTHRGOLAD_Revision]', '$txtTDRGOLAD_DocDate',
								'$txtTDRGOLAD_Block', '$txtTDRGOLAD_Village', '$txtTDRGOLAD_Owner', '$txtTDRGOLAD_AreaClass',
								REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
								REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
								REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
								'$txtTDRGOLAD_PlantClass',
								REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
								REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
								REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
								REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
								'$txtTDRGOLAD_Information',
								'$DLIU_LocationCode','1',NULL, '$mv_UserID',sysdate(),
								'$mv_UserID',sysdate(),NULL,NULL)";

				if($mysqli->query($sql1)){
					$s_sql="SELECT *
							FROM M_DocumentLandAcquisition
							WHERE DLA_Code='$CD_Code_H'";
					$s_query=mysql_query($s_sql);
					$s_arr=mysql_fetch_array($s_query);
					$DLA_ID=$s_arr['DLA_ID'];

					for ($j=1 ; $j<=$jKelengkapan ; $j++) {
						$dnewnum=str_pad($j,2,"0",STR_PAD_LEFT);
						$CD_Code="$newnum$dnewnum$Company_Code$DocumentGroup_Code$regmonth$regyear";

						$optKelengkapan=$_POST["optKelengkapan".$j."_".$c][$key];
						$i_sql="INSERT INTO M_DocumentLandAcquisitionAttribute
								VALUES (NULL,'$CD_Code','$DLA_ID','$j', '$optKelengkapan',
										'1','$mv_UserID', sysdate(),'$mv_UserID',
										sysdate(),NULL,NULL)";
						$mysqli->query($i_sql);
					}
				}
				$nnum=$nnum+1;
				$nID=$nID+1;
			}
		}
	}

	$count_company = $_POST['count_core_companyid'];

	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['optTHRGOLAD_Core_CompanyID'.$c];
		$Core_Phase = $_POST['txtTHRGOLAD_Core_Phase'.$c];
		// $Core_Period = date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Core_Period'.$c]));
		foreach($_POST['txtTDRGOLAD_Village'.$c] as $key => $value){
			$txtNumber=$_POST["txt_Number".$c][$key];
			$txtTDRGOLAD_DocDate=$_POST["txt_DocDate".$c][$key];
			$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));
			$txtTDRGOLAD_Block=$_POST["txt_Block".$c][$key];
			$txtTDRGOLAD_Village=$_POST["txt_Village".$c][$key];
			$txtTDRGOLAD_Owner=$_POST["txt_Owner".$c][$key];
			$txtTDRGOLAD_AreaClass=$_POST["txt_AreaClass".$c][$key];
			$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txt_AreaStatement".$c][$key])=="")||(is_numeric($_POST["txt_AreaStatement".$c][$key])==false))?"0":$_POST["txt_AreaStatement".$c][$key];
			$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txt_AreaPrice".$c][$key])=="")||(is_numeric($_POST["txt_AreaPrice".$c][$key])==false))?"0":$_POST["txt_AreaPrice".$c][$key];
			$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txt_AreaTotalPrice".$c][$key])=="")||(is_numeric($_POST["txt_AreaTotalPrice".$c][$key])==false))?"0":$_POST["txt_AreaTotalPrice".$c][$key];
			$txtTDRGOLAD_PlantClass=$_POST["txt_PlantClass".$c][$key];
			$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txt_PlantQuantity".$c][$key])=="")||(is_numeric($_POST["txt_PlantQuantity".$c][$key])==false))?"0":$_POST["txt_PlantQuantity".$c][$key];
			$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txt_PlantPrice".$c][$key])=="")||(is_numeric($_POST["txt_PlantPrice".$c][$key])==false))?"0":$_POST["txt_PlantPrice".$c][$key];
			$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txt_PlantTotalPrice".$c][$key])=="")||(is_numeric($_POST["txt_PlantTotalPrice".$c][$key])==false))?"0":$_POST["txt_PlantTotalPrice".$c][$key];
			$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txt_GrandTotal".$c][$key])=="")||(is_numeric($_POST["txt_GrandTotal".$c][$key])==false))?"0":$_POST["txt_GrandTotal".$c][$key];
			$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txt_Information".$c][$key]);

			$sql1= "INSERT INTO TD_RegistrationOfLandAcquisitionDocument
					VALUES (NULL,'$_POST[txtTHRGOLAD_ID]', '$txtTDRGOLAD_DocDate', '$txtTDRGOLAD_Block',
							'$txtTDRGOLAD_Village', '$txtTDRGOLAD_Owner', '$txtTDRGOLAD_AreaClass',
							REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
							'$txtTDRGOLAD_PlantClass',
							REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
							'$_POST[txtTHRGOLAD_Revision]',
							'$txtTDRGOLAD_Information','$mv_UserID', sysdate(),'$mv_UserID',
							sysdate(),NULL,NULL)";
			if($mysqli->query($sql1)){
				$s_sql="SELECT *
						FROM TD_RegistrationOfLandAcquisitionDocument
						WHERE TDRGOLAD_THRGOLAD_ID='$_POST[txtTHRGOLAD_ID]'
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
						AND TDRGOLAD_Revision='$_POST[txtTHRGOLAD_Revision]'
						AND TDRGOLAD_Information='$txtTDRGOLAD_Information'";
				$s_query=mysql_query($s_sql);
				$s_arr=mysql_fetch_array($s_query);
				for ($j=1 ; $j<=$jKelengkapan ; $j++) {
					$optKelengkapan=$_POST["optKelengkapan".$j."_".$c][$key];

					$k_sql="INSERT INTO TD_RegistrationOfLandAcquisitionDocumentDetail
							VALUES (NULL,'$s_arr[TDRGOLAD_ID]', '$j', '$optKelengkapan',
							'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
					$mysqli->query($k_sql);

				}
			}
		}
	}

	$sql3= "UPDATE TH_RegistrationOfLandAcquisitionDocument
			SET THRGOLAD_Revision='$_POST[txtTHRGOLAD_Revision]',
				THRGOLAD_Update_UserID='$mv_UserID',THRGOLAD_Update_Time=sysdate()
			WHERE THRGOLAD_ID='$_POST[txtTHRGOLAD_ID]'
			AND THRGOLAD_Delete_Time IS NULL";
	if ($mysqli->query($sql3)) {
		echo "<meta http-equiv='refresh' content='0; url=$PHP_SELF'>";
	}
}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
</script>
