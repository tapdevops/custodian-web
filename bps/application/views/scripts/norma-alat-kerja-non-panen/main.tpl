<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Alat Kerja Non Panen
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	30/04/2013
Update Terakhir		:	30/04/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
?>
<form name="form_init" id="form_init">
	<div>   
        <fieldset>
			<legend>PENCARIAN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
				<tr>
					<td width="15%">PERIODE BUDGET :</td>
					<td width="85%">
						<input type="text" name="budgetperiod" id="budgetperiod" value="<?=$this->period?>" style="width:200px;" class='filter'/>
						<input type="button" name="pick_period" id="pick_period" value="...">
					</td>
				</tr>
				<tr>
					<td>REGION :</td>
					<td>
						<input type="hidden" name="src_region_code" id="src_region_code" value="" style="width:200px;"/>
						<input type="text" name="src_region" id="src_region" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_region" id="pick_region" value="...">
					</td>
				</tr>
				<tr>
					<td>BUSINESS AREA :</td>
					<td>
						<input type="hidden" name="key_find" id="key_find" value="" style="width:200px;" />
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_ba" id="pick_ba" value="...">
					</td>
				</tr>
				<tr>
					<td>AKTIVITAS :</td>
					<td>
						<input type="hidden" name="activity_code" id="activity_code" value="" style="width:200px;" />
						<input type="text" name="activity_name" id="activity_name" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_activity" id="pick_activity" value="...">
					</td>
				</tr>
				<tr>
					<td>PENCARIAN :</td>
					<td>
						<input type="text" name="search" id="search" value="" style="width:200px;"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
						<input type="button" name="btn_refresh" id="btn_refresh" value="RESET" class="button" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="page_num" id="page_num" value="1" />
            <input type="hidden" name="page_rows" id="page_rows" value="20" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>NORMA ALAT KERJA - NON PANEN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th rowspan='2' style='color:#999'>+</th>
					<th rowspan='2' style='color:#999'>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE<BR>AKTIVITAS</th>
					<th>DESKRIPSI</th>
					<th>KODE<BR>MATERIAL</th>
					<th>DESKRIPSI<BR>MATERIAL</th>
					<th>UOM</th>
					<th>HARGA<BR>(RP)</th>
					<th>UNIT</th>
					<th>TOTAL<BR>(RP)</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<th>6</th>
					<th>7</th>
					<th>8</th>
					<th>9</th>
					<th>10</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none">
					<td align='center'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px' value='2'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:50px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:100px' value='4'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:350px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:100px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:350px' value='7'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:50px' value='8'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:120px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" style='width:50px' value='10'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:120px' value='11'/></td>
				</tr>
			</tbody>
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='11' class='grandtotal'>TOTAL <span id='label_summary_data'></span></td>
					<td><input type="text" name="summary_data" id="summary_data" readonly="readonly" style='width:120px'/></td>
				</tr>
			</tfoot>
			</table>
			</div>
			<br />
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<span id="record_counter">DATA: ? / ?</span>
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_first" id="btn_first" value="&lt;&lt;" class="button"/>
						<input type="button" name="btn_prev" id="btn_prev" value="&lt;" class="button"/>
						<input type="button" name="btn_next" id="btn_next" value="&gt;" class="button"/>
						<input type="button" name="btn_last" id="btn_last" value="&gt;&gt;" class="button"/>
						<span id="page_counter" style='margin-left:10px'>HALAMAN: ? / ?</span>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
</form>	
<?php
// you may change these value
echo $this->partial('popup.tpl', array('width'  => 1024,
                                       'height' => 400));

?>
<script type="text/javascript">
var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
	//BUTTON ACTION	
	$("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		var activity_name = $("#activity_name").val();		//DESKRIPSI AKTIVITAS
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( activity_name == '' )  {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			//jika periode budget yang dipilih <> periode budget aktif, maka tidak dapat melakukan proses perhitungan
			if (budgetperiod != current_budgetperiod) {
				$("#btn_save").hide();
				$("#btn_add").hide();
			}else{
				$("#btn_save").show();
				$("#btn_add").show();
			}

			//cek status periode
			$.ajax({
				type     : "post",
				url      : "norma-alat-kerja-non-panen/get-status-periode",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
				getData();
					if (data == 'CLOSE') {
							$("#btn_save").hide();
							$("#btn01_").hide();
						}else{
							$("#btn_save").show();
							$("#btn01_").show();
						}
					}
			});				

		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#btn_add").live("click", function(event) {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		var activity_code = $("#activity_code").val();		//KODE AKTIVITAS
		var activity_name = $("#activity_name").val();		//DESKRIPSI AKTIVITAS
	
		if( ba_code == '' || region == '' || activity_code == ''){
			alert('Anda Harus Memilih Region, Business Area, dan Aktivitas Terlebih Dahulu.');
		}
		else{
			var tr = $("#data tr:eq(0)").clone();
			$("#data").append(tr);
			var index = ($("#data tr").length - 1);					
			$("#data tr:eq(" + index + ")").find("input, select").each(function() {
				$(this).attr("id", $(this).attr("id") + index);
			});		
			
			var row = $(this).attr("id").split("_")[1];
			
			$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
			$("#data tr:eq(" + index + ") input[id^=text03_]").val(key_find);
			$("#data tr:eq(" + index + ") input[id^=text04_]").val(activity_code);
			$("#data tr:eq(" + index + ") input[id^=text05_]").val(activity_name);
			$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text06_]").attr("readonly", "");
			$("#data tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
			$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text10_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text11_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
			$("#data tr:eq(" + index + ")").removeAttr("style");
			$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
		}
    });
    $("input[id^=btn00_]").live("click", function(event) {
		var tr = $("#data tr:eq(0)").clone();
		$("#data").append(tr);
		var index = ($("#data tr").length - 1);					
		$("#data tr:eq(" + index + ")").find("input, select").each(function() {
			$(this).attr("id", $(this).attr("id") + index);
		});		
		
		var row = $(this).attr("id").split("_")[1];
		
		$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text02_]").val($("#text02_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text03_]").val($("#text03_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text04_]").val($("#text04_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text05_]").val($("#text05_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text06_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text10_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("integer");
		$("#data tr:eq(" + index + ") input[id^=text11_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
			//cek jika rowid kosong & klik delete, maka kosongkan seluruh data
			if (rowid == '') {
				clearTextField(row);
			}
			else {
				$.ajax({
					type     : "post",
					url      : "norma-alat-kerja-non-panen/delete/rowid/"+encode64(rowid),
					cache    : false,
					//dataType : 'json',
					success  : function(data) {
						if (data == "done") {
							clearTextField(row);
							alert("Data berhasil dihapus.");
						}else{
							alert(data);
						}
					}
				});
			}		
		}
    });
	$("#btn_save").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "norma-alat-kerja-non-panen/save",
				data     : $("#form_init").serialize(),
				cache    : false,
				//dataType : 'json',
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan.");
						$("#btn_find").trigger("click");
					}else if (data == "no_alert") {
						$("#btn_find").trigger("click");
					}else{
						alert(data);
					}
				}
			});
		}
    });
	$("#btn_cancel").click(function() {
        self.close();
    });
	$("#btn_export_csv").live("click", function() {	
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		var activity_code = $("#activity_code").val();		//KODE AKTIVITAS
		var activity_name = $("#activity_name").val();		//DESKRIPSI AKTIVITAS
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-alat-kerja-non-panen/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/activity_code/" + activity_code + "/search/" + encode64(search),'_blank');
		}
    });
	
	//PICK PERIODE BUDGET
	$("#pick_period").click(function() {
		popup("pick/budget-period", "pick", 700, 400 );
    });	
	$("#budgetperiod").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/budget-period", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	

		
	$("#src_region").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/region", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//PICK BA
	$("#pick_ba").click(function() {
		var regionCode = $("#src_region_code").val();	
		popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
    });
	$("#src_ba").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/business-area", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//PICK ACTIVITY
	$("#pick_activity").click(function() {
		var bacode = $("#key_find").val();
		if ( bacode ) {
			popup("pick/activity-norma-alat-kerja-non-panen/bacode/" + bacode, "pick", 700, 400 );
		} else {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		}
    });
	$("#activity_code").live("keydown", function(event) {
		var bacode = $("#key_find").val();
		//tekan F9
        if (event.keyCode == 120) {
			if ( bacode ) {
				//lov
				popup("pick/activity-norma-alat-kerja-non-panen/bacode/" + bacode, "pick", 700, 400 );
			} else {
				alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
			}
        }else{
			event.preventDefault();
		}
    });
	$("#activity_name").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/activity", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//SEARCH FREE TEXT
	$("#search").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
	//PAGING
    $("#btn_first").click(function() {
		$("#btn_save").trigger("click");
        page_num = 1;
    });
    $("#btn_prev").click(function() {
		$("#btn_save").trigger("click");
        page_num--;
    });
    $("#btn_next").click(function() {
		$("#btn_save").trigger("click");
        page_num++;
    });
    $("#btn_last").click(function() {
		$("#btn_save").trigger("click");
        page_num = page_max;
    });
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
	
	//LOV UTK INPUTAN
	$("input[id^=text06_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var ba_code = $("#key_find").val();
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text06_'+row).is('[readonly]') == false) { 
				//lov
				popup("pick/norma-harga/module/normaAlatKerjaNonPanen/row/" + row + "/tipeNormaHarga/alatKerjaNonPanen/bacode/" + ba_code, "pick");
			}			
        }else{
			event.preventDefault();
		}
    });
});

function getData(){
    $("#page_num").val(page_num);	
	var total = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "norma-alat-kerja-non-panen/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
            count = data.count;
            page_max = Math.ceil(count / page_rows);
            if (page_max == 0) {
                page_max = 1;
            }
            $("#btn_first").attr("disabled", page_num == 1);
            $("#btn_prev").attr("disabled", page_num == 1);
            $("#btn_next").attr("disabled", page_num == page_max);
            $("#btn_last").attr("disabled", page_num == page_max);
            $("#page_counter").html("HALAMAN: " + page_num + " / " + page_max);
            if (count > 0) {
                $.each(data.rows, function(key, row) {
                    var tr = $("#data tr:eq(0)").clone();
                    $("#data").append(tr);
                    var index = ($("#data tr").length - 1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});					
					
					$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
					$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=btn01_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.ACTIVITY_CODE);
					$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.ACTIVITY_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.MATERIAL_CODE);
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.MATERIAL_NAME);
                    $("#data tr:eq(" + index + ") input[id^=text08_]").val(row.UOM);
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.HARGA_INFLASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.UNIT, 0));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.TOTAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
					total = (row.TOTAL) ? (parseFloat(total) + parseFloat(row.TOTAL)) : total;
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();					
                });
				
				//summary_data
				$("#summary_data").val(accounting.formatNumber(total, 2));
				$("#summary_data").addClass("number");
				
				$("#tfoot").removeAttr("style");
				document.getElementById("label_summary_data").innerHTML = $("#activity_name").val();
            }
			else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
