<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Panen Premi Langsir
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
YULIUS 08/07/2014	: - fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
					  - penambahan info untuk lock table pada tombol cari, simpan, list
YULIUS 16/07/2014	:	- Modified Paginasi
						- tambah validasi FLAG_TEMP ke cekTempData
						- tambah html tombol btn_save_temp						  
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
						<!--<input type="hidden" name="src_region_code" id="src_region_code" value="" style="width:200px;"/>
						<input type="text" name="src_region" id="src_region" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_region" id="pick_region" value="...">-->
						<?php echo $this->setElement($this->input['src_region_code']);?>
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
			<legend>NORMA PANEN PREMI LANGSIR</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button"/>
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE VRA</th>
					<th>DESKRIPSI</th>
					<th>TON/TRIP</th>
					<th>TRIP/HARI</th>
					<th>TON/HARI</th>
					<th>HM/TRIP</th>
					<th>RP/HM</th>
					<th>RP/TRIP</th>
					<th>RP/KG</th>
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
					<th>11</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none">
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:100px' value='2'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:100px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:100px' value='4'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:350px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" style='width:120px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" style='width:120px' value='7'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:120px' value='8'/></td>
					<td><input type="text" name="text09[]" id="text09_" style='width:120px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:120px' value='10'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:120px' value='11'/></td>
					<td><input type="text" name="text12[]" id="text12_" readonly="readonly" style='width:120px' value='12'/></td>
				</tr>
			</tbody>
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='10' class='grandtotal'>PREMI LANGSIR TOTAL</td>
					<td><input type="text" name="total_premi_langsir" id="total_premi_langsir" readonly="readonly" style='width:120px'/></td>
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
//untuk width scroll area
var wscrollarea = window.innerWidth - 110;
document.getElementById("scrollarea").style.width = wscrollarea + "px";

var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
	$("#btn_unlock").hide();
	$("#btn_lock").hide();
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			$.ajax({
				type    : "post",
				url     : "norma-panen-premi-langsir/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						page_num = (page_num) ? page_num : 1;
						getData(); 
						$.ajax({
							type     : "post",
							url      : "norma-panen-premi-langsir/get-status-periode", //cek status periode
							data     : $("#form_init").serialize(),
							cache    : false,
							dataType: "json",
							success  : function(data) {
								$("#btn_save_temp").hide();
								if (data == 'CLOSE') {
										$("#btn_save").hide();
										$("#btn_add").hide();
									}else{
										$.ajax({
											type    : "post",
											url     : "norma-panen-premi-langsir/check-locked-seq", //check apakah status lock sendiri apakah lock
											data    : $("#form_init").serialize(),
											cache   : false,
											dataType: "json",
											success : function(data) {
												if(data.STATUS == 'LOCKED'){
													$("#btn_save").hide();
													$("#btn_add").hide();
													$("input[id^=btn01_]").hide();
													$("#btn_unlock").show();
													$("#btn_lock").hide();
												}else{
													$("#btn_save").show();
													$("#btn_add").show();
													$("input[id^=btn01_]").show();
													$("#btn_unlock").hide();
													$("#btn_lock").show();
												}
											}
										})
									}
								}
						});
					}else{
						alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
						$("#btn_save").hide();
						$("#btn_unlock").hide();
						$("#btn_lock").hide();
					}
				}
			})	
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
				type    : "post",
				url     : "norma-panen-premi-langsir/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "norma-panen-premi-langsir/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
										$.ajax({
											type     : "post",
											url      : "norma-panen-premi-langsir/save",
											data     : $("#form_init").serialize(),
											cache    : false,
											dataType : 'json',
											success  : function(data) {
												if (data.return == "locked") {
													alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
												}else if (data.return == "done") {
													alert("Data berhasil dihitung.");
													$("#btn_find").trigger("click");
												}else{
													alert(data.return);
												}
											}
										});
								}
							}
						})
					}else{
						alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
					}
				}
			})
		}
    });
	
	$("#btn_save_temp").click( function() {
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
				url      : "norma-panen-premi-langsir/save-temp",
				data     : $("#form_init").serialize(),
				cache    : false,
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan tetapi belum diproses. Silahkan klik tombol Hitung untuk memproses data.");	
					}else if (data == "no_alert") {
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-panen-premi-langsir/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	
	//SEARCH FREE TEXT
	$("#search").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
	//PAGING
    $("#btn_first").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num = 1;
		clearDetail();
		getData();
    });
    $("#btn_prev").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num--;
		clearDetail();
		getData();
    });
    $("#btn_next").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num++;
		clearDetail();
		getData();
    });
    $("#btn_last").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num = page_max;
		clearDetail();
		getData();
    });
	
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-panen-premi-langsir/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=LOCKED",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Finalisasi Data Berhasil.");
					$("#btn_find").trigger("click");
				}
			});	
		}
    });
	
	$("#btn_unlock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Memproses Ulang Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-panen-premi-langsir/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Anda Dapat Melakukan Proses Ulang Data.");
					$("#btn_find").trigger("click");
				}
			});	
		}
    });
});

function getData(){
    $("#page_num").val(page_num);
	var total_premi_langsir = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "norma-panen-premi-langsir/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
		if (data.return == 'locked') {
			alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
			$("#btn_save_temp").hide();
			$("#btn_save").hide();
			$("#btn01_").hide();
		} else {
		
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
					if (row.FLAG_TEMP) {cekTempData(index);} 
					$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=btn01_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.VRA_CODE);
					$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.VRA_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.TON_TRIP, 2));
					$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.TRIP_HARI, 2));
					$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.TON_HARI, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.HM_TRIP, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.RP_HM, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.RP_TRIP, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.RP_KG, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
					
					total_premi_langsir = (row.RP_KG) ? total_premi_langsir + parseFloat(row.RP_KG) : total_premi_langsir;
					
					//asumsi over basis
					$("#total_premi_langsir").val(accounting.formatNumber(total_premi_langsir, 2));
					$("#total_premi_langsir").addClass("number grandtotal_text");
					$("#tfoot").show();
                });
            }else{
				$("#tfoot").hide();
			}
		  }	
        }
    });
}
</script>
