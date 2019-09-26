<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan Norma Infrastruktur
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/06/2013
Update Terakhir		:	01/07/2014
Revisi				:	
	YIR 19/06/2014	: 	- perubahan LoV menjadi combo box untuk pilihan region
	SID 01/07/2014	: 	- fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
						- penambahan info untuk lock table pada tombol cari, simpan, hapus
						- menghilangkan tombol hitung semua
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
$this->headScript()->appendFile('js/freezepanes/jquery.freezetablecolumns.1.1.js');
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
            <input type="hidden" name="page_rows" id="page_rows" value="25" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>NORMA INFRASTRUKTUR</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">				
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th style='color:#999'>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE<BR>AKTIVITAS</th>
					<th>DESKRIPSI</th>
					<th>TIPE<BR>TANAH</th>
					<th>CLASS<BR>AKTIFITAS</th>
					<th>COST ELEMENT</th>
					<th>TOPOGRAFI</th>
					<th>UOM ACTIVITY</th>
					<th>SUB COST ELEMENT</th>
					<th>COST ELEMENT<BR> DESKRIPSI</th>
					<th>UOM<BR>COST ELEMENT</th>
					<th>QTY INFRA</th>
					<th>QTY</th>
					<th>ROTASI</th>
					<th>VOLUME</th>
					<th>QUANTITY/HA</th>
					<th>RP/QTY HARGA EXTERNAL</th>
					<th>RP/HA HARGA EXTERNAL</th>
					<th>HARGA INTERNAL</th>
					<th>RP/QTY HARGA INTERNAL</th>
					<th>RP/HA INTERNAL</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td align='center' width='18px'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly"/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly"/></td>
					<td width='150px'><input type="text" name="text05[]" id="text05_" readonly="readonly"/></td>
					<td width='75px'><input type="text" name="text06[]" id="text06_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text07[]" id="text07_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text08[]" id="text08_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text09[]" id="text09_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text10[]" id="text10_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly"/></td>
					<td width='150px'><input type="text" name="text12[]" id="text12_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text13[]" id="text13_" readonly="readonly"/></td>
					<td width='75px'><input type="text" name="text14[]" id="text14_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text15[]" id="text15_"/></td>
					<td width='50px'><input type="text" name="text16[]" id="text16_"/></td>
					<td width='50px'><input type="text" name="text17[]" id="text17_"/></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" readonly="readonly"/></td>
				</tr>
			</tbody>
			</table>
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
	$("#btn_lock").hide();
	$("#btn_unlock").hide();
	
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       1240,   // required
		height:      400,   // required
		numFrozen:   9,     // optional
		frozenWidth: 445,   // optional
		clearWidths: false,  // optional
	});//freezeTableColumns
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
				url     : "norma-infrastruktur/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "norma-infrastruktur/get-status-periode", //cek status periode
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
											url     : "norma-infrastruktur/check-locked-seq", //check apakah status lock sendiri apakah lock
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
						$("#btn_calc_all").hide();
					}
				}
			})	
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "norma-infrastruktur/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					$.ajax({
						type    : "post",
						url     : "norma-infrastruktur/check-locked-seq",
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
											url      : "norma-infrastruktur/delete",
											data     : { 
														 ROW_ID: $("#text00_" + row).val(), 
														 PERIOD_BUDGET: $("#text02_" + row).val(),
														 BA_CODE: $("#text03_" + row).val(),
														 ACTIVITY_CODE: $("#text04_" + row).val(),
														 ACTIVITY_CLASS: $("#text07_" + row).val(),
														 LAND_TYPE: $("#text06_" + row).val(),
														 COST_ELEMENT: $("#text08_" + row).val(),
														 TOPOGRAPHY: $("#text09_" + row).val()
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
				url     : "norma-infrastruktur/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "norma-infrastruktur/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
									$.ajax({
										type     : "post",
										url      : "norma-infrastruktur/save",
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
				url      : "norma-infrastruktur/save-temp",
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-infrastruktur/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
        $("#data_freeze").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data_freeze").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });	
	$("#data_freeze tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
		$("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
        $("#data_freeze").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data_freeze").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-infrastruktur/upd-locked-seq-status",
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
				url      : "norma-infrastruktur/upd-locked-seq-status",
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
    //
    $.ajax({
        type    : "post",
        url     : "norma-infrastruktur/list",
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
						//left freeze panes
						var tr = $("#data_freeze tr:eq(0)").clone();
						$("#data_freeze").append(tr);
						var index = ($("#data_freeze tr").length - 1);					
						$("#data_freeze tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});		

						//mewarnai jika row nya berasal dari temporary table
						if (row.FLAG_TEMP) {cekTempData(index);}	
						
						//left freeze panes row
						$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.ACTIVITY_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.ACTIVITY_DESC);
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.LAND_TYPE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.ACTIVITY_CLASS);
						$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(row.COST_ELEMENT);
						$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val(row.TOPOGRAPHY);
						$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
						
						//right freeze panes
						var tr = $("#data tr:eq(0)").clone();
						$("#data").append(tr);
						var index = ($("#data tr").length - 1);
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});
						
						//mewarnai jika row nya berasal dari temporary table
						if (row.FLAG_TEMP) {cekTempData(index);}
						
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.UOM_ACTIVITY);
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(row.SUB_COST_ELEMENT);
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(row.SUB_COST_ELEMENT_DESC);
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(row.UOM);
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.QTY_INFRA, 4));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("four_decimal");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.QTY, 4));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("four_decimal");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.ROTASI, 4));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("four_decimal");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.VOLUME, 0));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("integer");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.QTY_HA, 4));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("four_decimal");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.PRICE, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.RP_HA_EXTERNAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.HARGA_INTERNAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.RP_QTY_INTERNAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.RP_HA_INTERNAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}
			}
        }
    });
}
</script>