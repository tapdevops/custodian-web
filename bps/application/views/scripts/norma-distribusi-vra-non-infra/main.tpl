<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Distribusi VRA - Non Infra
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	15/07/2014
Revisi				:	
	SID 15/07/2014	: 	- penambahan info window VRA
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
$this->headScript()->appendFile('js/freezepanes/jquery.freezetablecolumns.1.1.js');
?>
<form name="form_init" id="form_init">
	<div>   
        <fieldset>
			<legend>PENCARIAN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width='50%' valign='top'>
					<table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
						<tr>
							<td width="30%">PERIODE BUDGET :</td>
							<td width="70%">
								<input type="text" name="budgetperiod" id="budgetperiod" value="<?=$this->period?>" style="width:200px;" class='filter'/>
								<input type="button" name="pick_period" id="pick_period" value="...">
							</td>
						</tr>
						<tr>
							<td>REGION :</td>
							<td>
								<?php echo $this->setElement($this->input['src_region_code']);?>
								<!--input type="hidden" name="src_region_code" id="src_region_code" value="" style="width:200px;"/>
								<input type="text" name="src_region" id="src_region" value="" style="width:200px;" class='filter'/>
								<input type="button" name="pick_region" id="pick_region" value="..."-->
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
					<input type="hidden" name="page_rows" id="page_rows" value="30" />
				</td>
				<td width='50%' valign='top'>
					<div id="info_vra">
						<b>INFO VRA</b><br>
						<table border="1" width="100%" cellpadding="0" cellspacing="0" id="info_vra">
						<tbody>
						</tbody>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>RKT DISTRIBUSI VRA - NON INFRA</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
					<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class='button' />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border='0' cellpadding='1' cellspacing='1' class='data_header' id='mainTable'>
			<thead>
				<tr id="theader">
					<th rowspan='3' style='color:#999'>+</th>
					<th rowspan='3' style='color:#999'>x</th>
					<th rowspan='2'>KODE</th>
					<th rowspan='2'>AKTIVITAS</th>
					<th colspan=3>JENIS ALAT</th>
					<th colspan=56 id='thdistribusi'>DISTRIBUSI JAM KERJA BY LOKASI KERJA (HM - KM)</th>
				</tr>
				<tr>
					<th>KODE</th>
					<th>VRATYPE</th>
					<th>UOM</th>
					<?php for ($i=1;$i<=50;$i++){ 
						echo "<th id='thtop".$i."_' style='display:none' class='tabsnyo'>AFD-".$i."</th>";
					}
					?>
					<th>BIBITAN</th>
					<th>B.CAMP</th>
					<th>UMUM</th>
					<th>LAIN</th>
					<th>TOTAL</th>
					<th>TOTAL COST</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<?php for ($i=13;$i<63;$i++){ 
						echo "<th id='thbot".$i."_' style='display:none'>AFD-".($i-12)."</th>";
					}
					?>
					<th>6</th>
					<th>7</th>
					<th>8</th>
					<th>9</th>
					<th>10</th>
					<th>11</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style='display:none'>
					<td align='center'>
						<input type='button' name='btn00[]' id='btn00_' class='button_add'/>
					</td>
					<td align='center'>
						<input type='button' name='btn01[]' id='btn01_' class='button_delete'/>
					</td>
					<td>
						<input type='hidden' name='text00[]' id='text00_' readonly='readonly'/>
						<input type='hidden' name='text01[]' id='text01_' readonly='readonly'/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type='text' name='text02[]' id='text02_' readonly='readonly' style='width:80px' value='2'/>
					</td>
					<td><input type='text' name='text03[]' id='text03_' readonly='readonly' style='width:250px' value='3'/></td>
					<td><input type='text' name='text04[]' id='text04_' readonly='readonly' style='width:80px' value='4'/></td>
					<td><input type='text' name='text05[]' id='text05_' readonly='readonly' style='width:250px' value='5'/></td>
					<td><input type='text' name='text06[]' id='text06_' readonly='readonly' style='width:80px' value='6'/></td>
					
					<?php for ($i=13;$i<63;$i++){ 
						echo "<td id='data".$i."_' style='display:none'>
									<input type='hidden' name='text".$i."_1[]' id='text".$i."1_' style='width:80px' value=''/>
									<input type='text' class='integer' name='text".$i."_2[]'  id='text".$i."2_' style='width:80px' value=''/>
								</td>";
					}
					?>
					<td>
						<input type='hidden' name='text9_1[]' id='text91_' style='width:80px' value='BIBITAN'/>
						<input type='text' name='text9_2[]' id='text92_' style='width:80px' value=''/>
					</td>
					<td>
						<input type='hidden' name='text10_1[]' id='text101_' style='width:80px' value='BASECAMP'/>
						<input type='text' name='text10_2[]' id='text102_' style='width:80px' value=''/>
					</td>
					<td>
						<input type='hidden' name='text11_1[]' id='text111_' style='width:80px' value='UMUM'/>
						<input type='text' name='text11_2[]' id='text112_' style='width:80px' value=''/>
					</td>
					<td>
						<input type='hidden' name='text12_1[]' id='text121_' style='width:80px' value='LAIN'/>
						<input type='text' name='text12_2[]' id='text122_' style='width:80px' value=''/>
					</td>
					<td><input type='text' name='text07[]' id='text07_' readonly='readonly' style='width:80px' value=''/></td>
					<td><input type='text' name='text08[]' id='text08_' readonly='readonly' style='width:120px' value=''/></td>
				</tr>
			</tbody>
			<tfoot name='tfoot' id='tfoot' style='display:none'>
				<tr>
					<td colspan='57' id='tdtotal' class='grandtotal'>TOTAL <span id='label_summary_data'></span></td>
					<td><input type='text' name='summary_data' id='summary_data' readonly='readonly' style='width:120px'/></td>
				</tr>
			</tfoot>
			</table>
			</div>
			<br />
			<table width='100%' border="0" cellpadding="0" cellspacing="0">
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
	$("#btn_unlock").hide();
	$("#btn_lock").hide();
	$("#info_vra").hide();
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
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
		page_num = (page_num) ? page_num : 1;
		getData();
		$("#btn_add").show();
			/* $.ajax({
				type    : "post",
				url     : "norma-distribusi-vra-non-infra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						page_num = (page_num) ? page_num : 1;
						getData();
						getInfoVra(); 						
						$.ajax({
							type     : "post",
							url      : "norma-distribusi-vra-non-infra/get-status-periode", //cek status periode
							data     : $("#form_init").serialize(),
							cache    : false,
							dataType: "json",
							success  : function(data) {
								$("#btn_save_temp").hide();
								if (data == 'CLOSE') {
										$("#btn_save").hide();
										$("#btn_add").hide();
										$("#btn01_").hide();
										$("#info_vra").hide();
									}else{
										$.ajax({
											type    : "post",
											url     : "norma-distribusi-vra-non-infra/check-locked-seq", //check apakah status lock sendiri apakah lock
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
													$("#info_vra").hide();
													
												}else{
													$("#btn_save").show();
													$("#btn_add").show();
													$("input[id^=btn01_]").show();
													$("#btn_unlock").hide();
													$("#btn_lock").show();
													$("#info_vra").show();
													$("#btn01_").show();
													
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
						$("#btn_upload").hide();
					}
				}
			})	*/	
			$("#btn_save").show();
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });	
	$("#btn_add").live("click", function(event) {	
			var tr = $("#data tr:eq(0)").clone();
			$("#data").append(tr);
			var index = ($("#data tr").length - 1);					
			$("#data tr:eq(" + index + ")").find("input, select").each(function() {
				$(this).attr("id", $(this).attr("id") + index);
			});
			
			$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text02_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text02_]").attr("readonly", "");
			$("#data tr:eq(" + index + ") input[id^=text02_]").attr("title", "Tekan F9 Untuk Memilih.");
			$("#data tr:eq(" + index + ") input[id^=text03_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("readonly", "");
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("title", "Tekan F9 Untuk Memilih.");
			$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text91_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text91_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text101_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text101_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text111_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text111_]").addClass("integer");
			$("#data tr:eq(" + index + ") input[id^=text121_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text121_]").addClass("number");
			$("#data tr:eq(" + index + ")").removeAttr("style");
			$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	
	
	$("input[id^=btn00_]").live("click", function(event) {
		$("#btn_add").trigger("click");
	});
	
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var trxcode = $("#text00_" + row).val();
		var ACTIVITY_CODE = $("#text02_" + row).val();
		var VRA_CODE = $("#text04_" + row).val();
		var key_find = $("#key_find").val();				//KODE BA
		var budgetperiod = $("#budgetperiod").val();				//KODE BA
		
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "norma-distribusi-vra-non-infra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					$.ajax({
						type    : "post",
						url     : "norma-distribusi-vra-non-infra/check-locked-seq",
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
											url      : "norma-distribusi-vra-non-infra/delete/trxcode/"+trxcode+"/key_find/"+key_find+"/BA_CODE/"+key_find+"/ACTIVITY_CODE/"+ACTIVITY_CODE+"/VRA_CODE/"+VRA_CODE+"/PERIOD_BUDGET/"+budgetperiod,
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
				url      : "norma-distribusi-vra-non-infra/save-temp",
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
				url     : "norma-distribusi-vra-non-infra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "norma-distribusi-vra-non-infra/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
										$.ajax({
											type     : "post",
											url      : "norma-distribusi-vra-non-infra/save",
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
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-distribusi-vra-non-infra/upd-locked-seq-status",
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
				url      : "norma-distribusi-vra-non-infra/upd-locked-seq-status",
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
	
	//PICK REGION
	$("#pick_region").click(function() {
		popup("pick/region", "pick", 700, 400 );
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
		popup("pick/business-area/regioncode/"+regionCode+"/module/normaDistribusiVraNonInfra", "pick", 700, 400 );
		$("#tabsnyo").hide();
		for(var x=13;x<63;x++){
			$("#thtop"+(x)+"_").hide();
			$("#data"+(x)+"_").hide();
			$("#thbot"+(x)+"_").hide();
		}
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

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + countHeader);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
	
	//LOV UTK INPUTAN
	$("input[id^=text02_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text02_'+row).is('[readonly]') == false) { 
				popup("pick/activity-mapp/module/normaDistribusiVraNonInfra/row/" + row, "pick", 700, 400 );
			}			
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text04_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text04_'+row).is('[readonly]') == false) { 
				var ba_code = $("#key_find").val();
				popup("pick/vra/module/normaDistribusiVraNonInfra/row/" + row + "/bacode/"+ ba_code , "pick", 700, 400 );
			}			
        }else{
			event.preventDefault();
		}
    });
	$("#btn_export_csv").live("click", function() {
		var budgetperiod = $("#budgetperiod").val();
		var src_region_code = $("#src_region_code").val();
		var key_find = $("#key_find").val();
		
		window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-distribusi-vra-non-infra/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find, '_blank');
    });
});

function getData(){
    $("#page_num").val(page_num);	
    $.ajax({
        type    : "post",
        url     : "norma-distribusi-vra-non-infra/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data)  {
			$("#tabsnyo").empty();
			if (data.return == 'locked') {
				alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
				$("#btn_add").hide();
				$("#btn_save_temp").hide();
				$("#btn_save").hide();
				$("#btn01_").hide();
			} else {		
				countAfd = data.countAfd;
				countHeader = data.countHeader;
				countData = data.countData;
				
				page_max = Math.ceil(countHeader / page_rows);
				if (page_max == 0) {
					page_max = 1;
				}
				$("#btn_first").attr("disabled", page_num == 1);
				$("#btn_prev").attr("disabled", page_num == 1);
				$("#btn_next").attr("disabled", page_num == page_max);
				$("#btn_last").attr("disabled", page_num == page_max);
				$("#page_counter").html("HALAMAN: " + page_num + " / " + page_max);
				i=0;
				if (countAfd > 0){		
				$("#tabsnyo").show();
					i=0;
					$.each(data.tabs, function(key, tab) {
						$("#thtop"+(13+i)+"_").removeAttr("style");$("#thtop"+(13+i)+"_").html(tab.LOCATION_CODE);
						$("#thbot"+(13+i)+"_").removeAttr("style");$("#thbot"+(13+i)+"_").html(6+i);
						$("#data"+(13+i)+"_").removeAttr("style");					
						$('#main_data tr:nth-child(2) th:nth-child('+(16+i)+')').html("x: "+tab.LOCATION_CODE);
						$('#main_data tr:nth-child(3) th:nth-child('+(6+i)+')').html(6+i);
						$("#text"+(13+i)+"1_").attr("value",tab.LOCATION_CODE);
						$("#text"+(13+i)+"2_").attr("value",'');
						i++;
					});
					$('#main_data tr:nth-child(3) th:nth-child('+(56)+')').html(6+i);
					$('#main_data tr:nth-child(3) th:nth-child('+(57)+')').html(7+i);
					$('#main_data tr:nth-child(3) th:nth-child('+(58)+')').html(8+i);
					$('#main_data tr:nth-child(3) th:nth-child('+(59)+')').html(9+i);
					$('#main_data tr:nth-child(3) th:nth-child('+(60)+')').html(10+i);
					$('#main_data tr:nth-child(3) th:nth-child('+(61)+')').html(11+i);
					$("#thdistribusi").attr("colspan",(6+i));
					$("#tdtotal").attr("colspan",(12+i));
					$("#tfoot").removeAttr("style");
					document.getElementById("label_summary_data").innerHTML = "";
					
					var totalall = parseFloat(0);					
					if (countHeader > 0) {	
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
							$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.TRX_CODE);
							$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
							$("#data tr:eq(" + index + ") input[id^=text02_]").val(row.ACTIVITY_CODE);
							$("#data tr:eq(" + index + ") input[id^=text03_]").val(row.DESCRIPTION);
							$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.VRA_CODE);
							$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.VRA_SUB_CAT_DESCRIPTION+' -- '+row.ACTIVITY_GROUP_TYPE_CODE);
							$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.UOM);
							$("#data tr:eq(" + index + ")").removeAttr("style");
							
							vLoop=13;
							var totalvalue = parseFloat(0);
							var totalprice = parseFloat(0);
							//doni
							$.each(data.rowsAfd, function(key, afdeling) { 
							//hanya untuk 50 hidden afd, dengan syarat nilai 0 didatabase jika tidak ada di afd
							//konfirm by doni 130716, kalo tidak ada nilai record 0 maka nilai setelahnya akan bergeser kesebelumnya
								$.each(afdeling, function(key, dataAfdeling){
									if (row.TRX_CODE == dataAfdeling.TRX_CODE) {
										//mewarnai jika row nya berasal dari temporary table
										if (dataAfdeling.FLAG_TEMP) {cekTempData(index);}
						
										if(dataAfdeling.LOCATION_CODE=='BIBITAN'){
											if(row.ACTIVITY_GROUP_TYPE_CODE == 'DIS_VRA_NON_INFRA'){
												$("#data tr:eq(" + index + ") input[id^=text92_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 0));
												$("#data tr:eq(" + index + ") input[id^=text92_]").addClass("integer");
											}else{
												$("#data tr:eq(" + index + ") input[id^=text92_]").attr('readonly',true);											
												$("#data tr:eq(" + index + ") input[id^=text92_]").val(accounting.formatNumber(0, 0));
												$("#data tr:eq(" + index + ") input[id^=text92_]").addClass("integer");
											}
										}else if(dataAfdeling.LOCATION_CODE=='BASECAMP'){
											if(row.ACTIVITY_GROUP_TYPE_CODE == 'DIS_VRA_NON_INFRA'){
												$("#data tr:eq(" + index + ") input[id^=text102_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 0));
												$("#data tr:eq(" + index + ") input[id^=text102_]").addClass("integer");
											}else{
												$("#data tr:eq(" + index + ") input[id^=text102_]").attr('readonly',true);
												$("#data tr:eq(" + index + ") input[id^=text102_]").val(accounting.formatNumber(0, 0));
												$("#data tr:eq(" + index + ") input[id^=text102_]").addClass("integer");
											}
										}else if(dataAfdeling.LOCATION_CODE=='UMUM'){
											if(row.ACTIVITY_GROUP_TYPE_CODE == 'DIS_VRA_NON_INFRA'){
												$("#data tr:eq(" + index + ") input[id^=text112_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 0));
												$("#data tr:eq(" + index + ") input[id^=text112_]").addClass("integer");
											}else{
												$("#data tr:eq(" + index + ") input[id^=text112_]").attr('readonly',true);
												$("#data tr:eq(" + index + ") input[id^=text112_]").val(accounting.formatNumber(0, 0));
												$("#data tr:eq(" + index + ") input[id^=text112_]").addClass("integer");
											}
										}else if(dataAfdeling.LOCATION_CODE=='LAIN'){
											if(row.ACTIVITY_GROUP_TYPE_CODE == 'DIS_VRA_NON_INFRA'){
												$("#data tr:eq(" + index + ") input[id^=text122_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 0));
												$("#data tr:eq(" + index + ") input[id^=text122_]").addClass("integer");
											}else{
												$("#data tr:eq(" + index + ") input[id^=text122_]").attr('readonly',true);
												$("#data tr:eq(" + index + ") input[id^=text122_]").val(accounting.formatNumber(0, 0));
												$("#data tr:eq(" + index + ") input[id^=text122_]").addClass("integer");
											}
										}else{
											$("#data tr:eq(" + index + ") input[id^=text"+vLoop+"2_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 0));
											$("#data tr:eq(" + index + ") input[id^=text"+vLoop+"2_]").addClass("integer");
											vLoop++;
										}
										totalvalue = parseInt(dataAfdeling.HM_KM) ? (totalvalue + parseInt(dataAfdeling.HM_KM)) : totalvalue;
										totalprice = parseInt(dataAfdeling.PRICE_HM_KM) ? (totalprice+parseFloat(dataAfdeling.PRICE_HM_KM)) : totalprice;
									}
								});
							});
							$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(totalvalue, 0));
							$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("integer");
							$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(totalprice, 2));
							$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
							totalall = parseFloat(totalprice) ? (totalall + parseFloat(totalprice)) : totalall;
						});
					}
					//summary_data
					$("#summary_data").val(accounting.formatNumber(totalall, 2));
					$("#summary_data").addClass("grandtotal_text number");
				} else {
					$("#tfoot").hide();
				}
			}
        }
    });
}


//untuk info window VRA
function getInfoVra(){
	var lastRomnum = "";
	var appRow = "";
    $.ajax({
        type    : "post",
        url     : "norma-distribusi-vra-non-infra/list-info-vra",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			$("#info_vra tr").remove(); 
			if (data.count > 0) {
				appRow += '<tr>';
				appRow += '<td width="35%" align="center"><b>JENIS KENDARAAN</b></td>';
				appRow += '<td width="15%" align="center"><b>RP / QTY</b></td>';
				appRow += '<td width="35%" align="center"><b>JENIS KENDARAAN</b></td>';
				appRow += '<td width="15%" align="center"><b>RP / QTY</b></td>';
				appRow += '</tr>';
				$.each(data.rows, function(key, row) {
					if (row.ROWNUM % 2 == 1){
						appRow += '<tr>';
						appRow += '<td width="35%">' + row.VRA_SUB_CAT_DESCRIPTION + '</td>';
						appRow += '<td width="15%" align="right">' + accounting.formatNumber(row.VALUE, 2) + '</td>';
					}else{
						appRow += '<td width="35%">' + row.VRA_SUB_CAT_DESCRIPTION + '</td>';
						appRow += '<td width="15%" align="right">' + accounting.formatNumber(row.VALUE, 2) + '</td>';
						appRow += '</tr>';
					}
					lastRomnum = row.ROWNUM;
				});
				if(lastRomnum % 2 == 1){ appRow += '<td></td><td></td></tr>'; }
				$('#info_vra > tbody:last').append(appRow);
            }
        }
    });
}
</script>
