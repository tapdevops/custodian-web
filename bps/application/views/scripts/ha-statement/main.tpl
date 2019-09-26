<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Ha Statement
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
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
						<?php echo $this->setElement($this->input['src_region_code']);?>
					</td>
				</tr>
				<tr>
					<td>BUSINESS AREA :</td>
					<td>
						<input type="hidden" name="key_find" id="key_find" value="" style="width:200px;" />
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;"  class='filter'/>
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
			<legend>HA STATEMENT</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_upload" id="btn_upload" value="UPLOAD" class="button" />
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />					
					</td>
					<td width="50%" align="right">		
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />				
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th rowspan='2' style='color:#999'>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>AFD</th>
					<th>BLOK - DESC</th>
					<th>HA TANAM</th>
					<th>TOPOGRAFI</th>
					<th>TIPE TANAH</th>
					<th>JENIS BIBIT</th>
					<th>JENIS TANAH</th>
					<th>TAHUN TANAM</th>
					<th>STATUS SMS1</th>
					<th>STATUS SMS2</th>
					<th>JUMLAH POKOK</th>
					<th>SPH</th>
					<th>STATUS</th>
					<th>KONVERSI<BR>TBM3</th>
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
					<th>12</th>
					<th>13</th>
					<th>14</th>
					<th>15</th>
					<th>16</th>
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr style="display:none">
					<td align='center'>
						<!--input type="button" name="btn00[]" id="btn00_" class='button_delete'/-->
					</td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px' value='2'/>
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" style='width:50px' value='5'/>
						<input type="hidden" name="sph_max[]" id="sph_max_" readonly="readonly" />
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:50px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:50px' value='4'/></td>
					<td><input type="text" name="text23[]" id="text23_" readonly="readonly" style='width:75px' /></td>
					<td><input type="text" name="text06[]" id="text06_" style='width:50px' value='6'/></td>
					<td>
						<input type="hidden" name="text07[]" id="text07_" style='width:50%;' value='7'/>
						<input type="text" name="text08[]" id="text08_" style='width:150px' value='8' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td>
						<input type="hidden" name="text09[]" id="text09_" style='width:100%' value='9'/>
						<input type="text" name="text10[]" id="text10_" style='width:100px' value='10' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td>
						<input type="hidden" name="text11[]" id="text11_" style='width:100%' value='11'/>
						<input type="text" name="text12[]" id="text12_" style='width:100px' value='12' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td>
						<input type="hidden" name="text13[]" id="text13_" style='width:100%' value='13'/>
						<input type="text" name="text14[]" id="text14_" style='width:100px' value='14' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td><input type="text" name="text15[]" id="text15_" style='width:50px' value='15'/></td>
					<td><input type="text" name="text20[]" id="text20_" readonly="readonly" style='width:50px' value='20'/></td>
					<td><input type="text" name="text21[]" id="text21_" readonly="readonly" style='width:50px' value='21'/></td>
					<td><input type="text" name="text16[]" id="text16_" style='width:50px' value='16'/></td>
					<td><input type="text" name="text17[]" id="text17_" readonly="readonly" style='width:50px' value='17'/></td>
					<td>
						<input type="hidden" name="text18[]" id="text18_" style='width:100%' value='18'/>
						<input type="text" name="text19[]" id="text19_" style='width:100px;' value='19' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td align='center'><input type="checkbox" name="text22[]" id="text22_"/></td>
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
	<input type="hidden" name="controller" id="controller" value=""/>
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
				url     : "ha-statement/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "ha-statement/get-status-periode", //cek status periode
							data     : $("#form_init").serialize(),
							cache    : false,
							dataType: "json",
							success  : function(data) {
								$("#btn_save_temp").hide();
								if (data == 'CLOSE') {
										$("#btn_upload").hide();
										$("#btn_save").hide();
										$(".button_delete").hide();
									}else{
										$.ajax({
											type    : "post",
											url     : "ha-statement/check-locked-seq", //check apakah status lock sendiri apakah lock
											data    : $("#form_init").serialize(),
											cache   : false,
											dataType: "json",
											success : function(data) {
												if(data.STATUS == 'LOCKED'){
													$("#btn_save").hide();
													$(".button_delete").hide();
													$("#btn_unlock").show();
													$("#btn_lock").hide();
													$("#btn_upload").hide();
												}else{
													$("#btn_upload").show();
													$("#btn_save").show();
													$(".button_delete").show();
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
						$("#btn_upload").hide();
					}
				}
			})
		}
    });	
	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#btn_upload").live("click", function() {
		var controller = "upload/ha-statement";
		$("#controller").val(controller);
		popup("upload/main", "detail", 700, 400);
    });
	$("input[id^=btn00_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "ha-statement/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					$.ajax({
						type    : "post",
						url     : "ha-statement/check-locked-seq",
						data    : $("#form_init").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){ 
								alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
							}else{
								if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
									//cek jika rowid kosong & klik delete, maka kosongkan seluruh data
									if (rowid == '') {
										clearTextField(row);
									}
									else {
										$.ajax({
											type     : "post",
											url      : "ha-statement/delete",
											data     : { 
														 ROW_ID: $("#text00_" + row).val(), 
														 PERIOD_BUDGET: $("#text02_" + row).val(),
														 BA_CODE: $("#text03_" + row).val(),
														 AFD_CODE: $("#text04_" + row).val(),
														 BLOCK_CODE: $("#text05_" + row).val()
													   },
											cache    : false,
											dataType : 'json',
											success  : function(data) {
												if (data.return == "locked") {
													alert("Anda tidak dapat menghapus data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum terhapus. Harap mencoba melakukan proses penghapusan data beberapa saat lagi.");
												}else if (data.return == "done") {
													clearTextField(row);
													alert("Data berhasil dihapus.");
												}else{
													alert(data.return);
												}
											}
										});
									}	
								}
							}
						}
					})
				}else{
					alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				}
			}
		})
    });
	//untuk proses simpan draft
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
				url      : "ha-statement/save-temp",
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
				url     : "ha-statement/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "ha-statement/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
										$.ajax({
											type     : "post",
											url      : "ha-statement/save",
											data     : $("#form_init").serialize(),
											cache    : false,
											dataType : 'json',
											success  : function(data) {
												if (data.return == "locked") {
													alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
												}else if (data.return == "done") {
													alert("Data berhasil dihitung.");
													//alert(data.isi);
													$("#btn_find").trigger("click");
												}else if (data.return == "donewithexception") {
													alert("Data berhasil dihitung. Mohon Lakukan pemeriksaan data pokok di blok " + data.blok + " Pada RKT Tanam Sisip!");
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
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "ha-statement/upd-locked-seq-status",
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
				url      : "ha-statement/upd-locked-seq-status",
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-ha-statement/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	
	//LOV UTK INPUTAN
	$("input[id^=text08_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/topography/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text10_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/land-type/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text12_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/progeny/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text14_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/land-suitability/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text19_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/status-blok-budget/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
});

function getData(){
    $("#page_num").val(page_num);	
	
	var user_role = "<?=$this->userrole?>";
    //
    $.ajax({
        type    : "post",
        url     : "ha-statement/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			if (data.return == 'locked') {
				$("#btn_upload").hide();
				$("#btn_save").hide();
				$("#btn_save_temp").hide();
				$("#btn00_").hide();
				alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
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
						
						$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
						
						//mewarnai jika row nya berasal dari temporary table
						if (row.FLAG_TEMP) {cekTempData(index);}
						
						$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
						$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
						//$("#data tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.HA_PLANTED, 4));
						$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.HA_PLANTED);
						//$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("four_decimal");
						
						//jika KTU tidak bisa edit ha
						if (user_role == 'KTU') $("#data tr:eq(" + index + ") input[id^=text06_]").attr("readonly", "readonly");
						
						$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.TOPOGRAPHY);
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.TOPOGRAPHY_DESC);
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.LAND_TYPE);
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.LAND_TYPE_DESC);
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(row.PROGENY);
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(row.PROGENY_DESC);
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(row.LAND_SUITABILITY);
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(row.LAND_SUITABILITY_DESC);
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(row.TAHUN_TANAM);
						$("#data tr:eq(" + index + ") input[id^=text15_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.POKOK_TANAM, 0));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("integer");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.SPH, 0));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("integer");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(row.STATUS);
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(row.STATUS_DESC);
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(row.MATURITY_STAGE_SMS1);
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(row.MATURITY_STAGE_SMS2);
						
						if (row.KONVERSI_TBM == 'Y'){
							$("#data tr:eq(" + index + ") input[id^=text22_]").attr('checked','checked');
						}else{
							$("#data tr:eq(" + index + ") input[id^=text22_]").removeAttr('checked');
						}
						
						$("#data tr:eq(" + index + ") input[id^=text22_]").attr("name", "text22["+index+"]");
						$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}
			}
        }
    });
}
</script>
