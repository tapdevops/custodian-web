<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Sebaran Produksi
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	05/06/2013
Update Terakhir		:	05/06/2013
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
						<input type="text" name="budgetperiod" id="budgetperiod" value="<?=$this->period?>" style="width:200px;"/>
						<input type="button" name="pick_period" id="pick_period" value="...">
					</td>
				</tr>
				<tr>
					<td>REGION :</td>
					<td>
						<?php echo $this->setElement($this->input['src_region_code']);?>
					</td>
				</tr>
				<tr>
					<td>BUSINESS AREA :</td>
					<td>
						<input type="hidden" name="key_find" id="key_find" value="" style="width:200px;" />
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;" />
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
			<legend>MASTER SEBARAN PRODUKSI</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">		
						&nbsp;
					</td>
					<td width="50%" align="right">				
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<!--input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" /-->
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
			<tr>
					<th width="55px">PERIODE<BR>BUDGET</th>
					<th width="55px">BUSINESS<BR>AREA</th>
					<th>JAN</th>
					<th>FEB</th>
					<th>MAR</th>
					<th>APR</th>
					<th>MEI</th>
					<th>JUN</th>
					<th>JUL</th>
					<th>AGS</th>
					<th>SEP</th>
					<th>OKT</th>
					<th>NOV</th>
					<th>DES</th>
					<th>TOTAL</th>
				</tr>
				<tr class='column_number'>
					<th width="55px">1</th>
					<th width="55px">2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<th>6</th>
					<th>7</th>
					<th>8</th>
					<th>9</th>
					<th>10</th>
					<th>11</th>
					<th>12</th>
					<th>13</th>
					<th>14</th>
					<th>15</th>
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr style="display:none">
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:55px;'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:55px;'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text12[]" id="text12_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text13[]" id="text13_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text14[]" id="text14_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text15[]" id="text15_" readonly="readonly" style='width:85px;'/></td>
					<td><input type="text" name="text16[]" id="text16_" readonly="readonly" style='width:85px;'/></td>
				</tr>
			</tbody>
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
	$("#btn_save_temp").hide();
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
				url     : "sebaran-produksi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					page_num = (page_num) ? page_num : 1;
					getData();
					if(data==1){
							$.ajax({
								type    : "post",
								url     : "sebaran-produksi/check-locked-seq", //check apakah status lock sendiri apakah lock
								data    : $("#form_init").serialize(),
								cache   : false,
								dataType: "json",
								success : function(data) {
									if(data.STATUS == 'LOCKED'){
										$("#btn_unlock").show();
										$("#btn_lock").hide();
										$("#btn_save").hide();
									}else{
										//jika periode budget yang dipilih <> periode budget aktif, maka tidak dapat melakukan proses perhitungan
										if (budgetperiod != current_budgetperiod) {
											$("#btn_save").hide();
										}else{
											$("#btn_save").show();
										}
										$("#btn_unlock").hide();
										$("#btn_lock").show();
									}
								}
							})
														
					}else{
						alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
						$("#btn_unlock").hide();
						$("#btn_save").hide();
					}
				}
			})
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
				url      : "sebaran-produksi/save-temp",
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
				url     : "sebaran-produksi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "sebaran-produksi/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
									if ( checkTotal() != false ) {
										$.ajax({
											type     : "post",
											url      : "sebaran-produksi/save",
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
									} else {
										alert ("Total Sebaran Produksi Harus 100.");
									}
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
	$("#btn_cancel").click(function() {
        self.close();
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
		if( checkTotal() != false ){
			$("#btn_save_temp").trigger("click");
			page_num = 1;
		}else{
			alert("Inflasi Harus Lebih Besar Dari 100%.");
		}
		clearDetail();
		getData();
    });
    $("#btn_prev").click(function() {
		
		if( checkTotal() != false ){
			$("#btn_save_temp").trigger("click");
			page_num--;
		}else{
			alert("Inflasi Harus Lebih Besar Dari 100%.");
		}
		clearDetail();
		getData();
    });
    $("#btn_next").click(function() {
		if( checkTotal() != false ){
			$("#btn_save_temp").trigger("click");
			page_num++;
		}else{
			alert("Inflasi Harus Lebih Besar Dari 100%.");
		}
		clearDetail();
		getData();
    });
    $("#btn_last").click(function() {
		
		if( checkTotal() != false ){
			$("#btn_save_temp").trigger("click");
			page_num = page_max;
		}else{
			alert("Inflasi Harus Lebih Besar Dari 100%.");
		}
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
	
	//cek total per baris harus 100
	$("[class^=number]").live("change", function(event) {
        var row = $(this).attr("id").split("_")[1];
		var JAN = parseFloat($("#text04_" + row).val());
		var FEB = parseFloat($("#text05_" + row).val());
		var MAR = parseFloat($("#text06_" + row).val());
		var APR = parseFloat($("#text07_" + row).val());
		var MEI = parseFloat($("#text08_" + row).val());
		var JUN = parseFloat($("#text09_" + row).val());
		var JUL = parseFloat($("#text10_" + row).val());
		var AGS = parseFloat($("#text11_" + row).val());
		var SEP = parseFloat($("#text12_" + row).val());
		var OKT = parseFloat($("#text13_" + row).val());
		var NOV = parseFloat($("#text14_" + row).val());
		var DEC = parseFloat($("#text15_" + row).val());
		
		var total = JAN + FEB + MAR + APR + MEI + JUN + JUL + AGS + SEP	 + OKT + NOV + DEC;
		
		$("#text16_" + row).val(accounting.formatNumber(total, 2));
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "sebaran-produksi/upd-locked-seq-status",
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
				url      : "sebaran-produksi/upd-locked-seq-status",
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

function checkTotal() {
    var cData = parseInt($("input[id^=text16_]").length) - 1;
	for ( var row = 1 ; row <= cData ; row++) {
		var total = $("#text16_" + row).val();
		if (total != 100) {
			$("#data tr:eq(" + row + ") input[id^=text16_]").focus();
			return false;
		}
	}
	return true;
}

function getData(){
    $("#page_num").val(page_num);	
    //
    $.ajax({
        type    : "post",
        url     : "sebaran-produksi/list",
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
					if (row.FLAG_TEMP) {cekTempData(index);} 
					$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(accounting.formatNumber(row.JAN, 4));
					$("#data tr:eq(" + index + ") input[id^=text04_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(accounting.formatNumber(row.FEB, 4));
					$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.MAR, 4));
					$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.APR, 4));
					$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.MAY, 4));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.JUN, 4));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.JUL, 4));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.AUG, 4));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.SEP, 4));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.OCT, 4));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.NOV, 4));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.DEC, 4));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(100, 4));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>
