<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Alat Kerja Panen
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
			<legend>ALAT KERJA - PANEN</legend>
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
					<th>KODE<BR>MATERIAL</th>
					<th>DESKRIPSI<BR>MATERIAL</th>
					<th>UOM</th>
					<th>ROTASI /<BR>TAHUN /<BR>ORANG</th>
					<th>HARGA<BR>(RP)</th>
					<th>TOTAL<BR>(RP)</th>
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
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:300px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:100px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" style='width:50px' value='7'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:120px' value='8'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:120px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:120px'/></td>
				</tr>
			</tbody>
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='9' class='grandtotal'>TOTAL</td>
					<td><input type="text" name="summary_data_1" id="summary_data_1" readonly="readonly" style='width:120px'/></td>
					<td><input type="text" name="summary_data_2" id="summary_data_2" readonly="readonly" style='width:120px'/></td>
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
	$("#btn_lock").hide();
	$("#btn_unlock").hide();
	//yg dapat melakukan upload, hanya ADMIN
	var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
	if (reference_role != 'NASIONAL'){
		$("#btn_upload").hide();
	}else{
		$("#btn_upload").show();
	}
	
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
		
		if ( ba_code == '' )  {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			
			$.ajax({
				type    : "post",
				url     : "norma-alat-kerja-panen/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "norma-alat-kerja-panen/get-status-periode", //cek status periode
							data     : $("#form_init").serialize(),
							cache    : false,
							dataType: "json",
							success  : function(data) {
								$("#btn_save_temp").hide();
								if (data == 'CLOSE') {
										$("#btn_save").hide();
										$("#btn_add").hide();
										$("#btn_upload").hide();
										$("#btn01_").hide();
										$(".button_add").hide();
									}else{
										$.ajax({
											type    : "post",
											url     : "norma-alat-kerja-panen/check-locked-seq", //check apakah status lock sendiri apakah lock
											data    : $("#form_init").serialize(),
											cache   : false,
											dataType: "json",
											success : function(data) {
												if(data.STATUS == 'LOCKED'){
													$("#btn_save").hide();
													$("#btn_add").hide();
													$(".button_add").hide();
													$("input[id^=btn01_]").hide();
													$("#btn_upload").hide();
													$("#btn_unlock").show();
													$("#btn_lock").hide();
												}else{
													$("#btn_save").show();
													$("#btn_add").show();
													$(".button_add").show();
													$("#btn_upload").show();
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
						$("#btn_upload").hide();	
						$(".button_add").hide();	
					}
				}
			})
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
	
		if( ba_code == '' || src_region_code == ''){
			alert('Anda Harus Memilih Region dan Business Area Terlebih Dahulu.');
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
			$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("readonly", "");
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("title", "Tekan F9 Untuk Memilih.");
			$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text10_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
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
		$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text04_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text04_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text10_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "norma-alat-kerja-panen/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					$.ajax({
						type    : "post",
						url     : "norma-alat-kerja-panen/check-locked-seq",
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
											url      : "norma-alat-kerja-panen/delete",											
											data     : { 
														 ROW_ID: $("#text00_" + row).val(), 
														 PERIOD_BUDGET: $("#text02_" + row).val(),
														 BA_CODE: $("#text03_" + row).val(),
														 MATERIAL_CODE: $("#text04_" + row).val()
													   },
											cache    : false,
											dataType : 'json',
											success  : function(data) {
												if (data.return == "locked") {
													alert("Anda tidak dapat menghapus data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum terhapus. Harap mencoba melakukan proses penghapusan data beberapa saat lagi.");
												}else if (data.return == "done") {
													clearTextField(row);
													alert("Data berhasil dihapus.");
													$("#btn_find").trigger("click");
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
				url     : "norma-alat-kerja-panen/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "norma-alat-kerja-panen/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
										$.ajax({
											type     : "post",
											url      : "norma-alat-kerja-panen/save",
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-alat-kerja-panen/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	
	//LOV UNTUK INPUTAN
	$("input[id^=text04_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var ba_code = $("#key_find").val();
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text04_'+row).is('[readonly]') == false) { 
				//lov
				popup("pick/norma-harga/module/normaAlatKerjaPanen/row/" + row + "/tipeNormaHarga/alatKerja/bacode/" + ba_code, "pick");
			}
        }else{
			event.preventDefault();
		}
    });
	
	//upload
	$("#btn_upload").live("click", function() {
		var controller = "upload/norma-alat-kerja-panen";
		$("#controller").val(controller);
		popup("upload/main", "detail", 700, 400);
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-alat-kerja-panen/upd-locked-seq-status",
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
				url      : "norma-alat-kerja-panen/upd-locked-seq-status",
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
        url     : "norma-alat-kerja-panen/list",
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
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.MATERIAL_CODE);
					$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.MATERIAL_NAME);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.UOM);
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.ROTASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.HARGA_INFLASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.TOTAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").css("background", "#000");
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
					
					//summary_data
					$("#summary_data_1").val(accounting.formatNumber(row.PRICE_SUM, 2));
					$("#summary_data_1").addClass("number");
					$("#summary_data_2").val(accounting.formatNumber(row.PRICE_KG, 2));
					$("#summary_data_2").addClass("number");
                });
				
				$("#tfoot").removeAttr("style");
            }
			else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
