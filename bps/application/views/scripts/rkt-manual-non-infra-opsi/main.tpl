<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan RKT Manual - Non Infra + Opsi
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	26/07/2013
Update Terakhir		:	05/05/2015
Revisi				:	
	YIR 19/06/2014	: 	- perubahan LoV menjadi combo box untuk pilihan region & maturity status
	SID 23/06/2014	: 	- fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
						- penambahan info untuk lock table pada tombol cari, simpan, hapus
	NBU 05/05/2015	: 	- penutupan button lock & unlock di line 95 - 98
					    - penutupan pengecekan lock pada diri sendiri di line 360
					    - penutupan pengecekan lock untuk button save di line 456	
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
				</tr><tr>
					<td>ACTIVITY :</td>
					<td>
						<input type="hidden" name="src_activity_code" id="src_activity_code" value="" style="width:200px;" />
						<input type="text" name="src_activity_desc" id="src_activity_desc" value="" style="width:200px;" class='filter'/>
						<input type="hidden" name="activity_uom" id="activity_uom" value="" style="width:200px;" />
						<input type="button" name="pick_activity" id="pick_activity" value="...">
					</td>
				</tr>
				<tr>
					<td>MATURITY STAGE :</td>
					<td>
						
						<?php echo $this->setElement($this->input['src_matstage_code']);?>
					</td>
				</tr>
				<tr>
					<td>AFDELING :</td>
					<td>
						<input type="text" name="src_afd" id="src_afd" value="" style="width:200px;"/>
						<input type="button" name="pick_afd" id="pick_afd" value="...">
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
            <input type="hidden" name="page_rows" id="page_rows" value="50" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>RKT RAWAT + OPSI</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />					
					</td>
					<td width="50%" align="right">
						<!--
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						-->
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>AFD</th>
					<th>BLOK - DESC</th>
					<th>TAHUN TANAM</th>
					<th>JENIS TANAH</th>
					<th>TOPOGRAFI</th>
					<th>STATUS<BR>SMS 1</th>
					<th>STATUS<BR>SMS 2</th>
					<th>HEKTAR TANAM<BR>HA</th>
					<th>HEKTAR TANAM<BR>POKOK</th>
					<th>HEKTAR TANAM<BR>SPH</th>
					<th>KODE AKTIVITAS</th>
					<th>DESKRIPSI</th>
					<th>TIPE<br>NORMA</th>
					<th>ACTIVITY CLASS</th>
					<th>ROTASI SMS1</th>
					<th>ROTASI SMS2</th>
					<th>AWAL ROTASI</th>
					<th>AKTIVITAS OPSI</th>
					<!--th>DESKRIPSI AKTIVITAS OPSI</th-->
					<th>SUMBER BIAYA</th>
					<th>DISTRIBUSI KERJA<BR>JAN <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>FEB <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>MAR <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>APR <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>MEI <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>JUN <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>JUL <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>AGS <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>SEP <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>OKT <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>NOV <span class='activity_uom'></span></th>
					<th>DISTRIBUSI KERJA<BR>DES <span class='activity_uom'></span></th>					
					<th>DISTRIBUSI KERJA<BR>YEAR <span class='activity_uom'></span></th>
					<th>RP/ROTASI SMS1</th>
					<th>RP/ROTASI SMS2</th>
					<th>DISTRIBUSI COST<BR>JAN</th>
					<th>DISTRIBUSI COST<BR>FEB</th>
					<th>DISTRIBUSI COST<BR>MAR</th>
					<th>DISTRIBUSI COST<BR>APR</th>
					<th>DISTRIBUSI COST<BR>MEI</th>
					<th>DISTRIBUSI COST<BR>JUN</th>
					<th>DISTRIBUSI COST<BR>JUL</th>
					<th>DISTRIBUSI COST<BR>AGS</th>
					<th>DISTRIBUSI COST<BR>SEP</th>
					<th>DISTRIBUSI COST<BR>OKT</th>
					<th>DISTRIBUSI COST<BR>NOV</th>
					<th>DISTRIBUSI COST<BR>DES</th>					
					<th>DISTRIBUSI COST<BR>TOTAL QTY</th>
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td width='50px'>
						<input type="hidden" name="hidden00[]" id="hidden00_" readonly="readonly"/>
						<input type="hidden" name="hidden01[]" id="hidden01_" readonly="readonly"/>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly"/>
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly"/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly"/></td>
					<td width='75px'><input type="text" name="text51[]" id="text51_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text06[]" id="text06_" readonly="readonly" style='text-align:right;'/></td>
					<td width='100px'><input type="text" name="text07[]" id="text07_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text08[]" id="text08_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text09[]" id="text09_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text10[]" id="text10_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text12[]" id="text12_" readonly="readonly" class='integer'/></td>
					<td width='100px'><input type="text" name="text13[]" id="text13_" readonly="readonly" class='integer'/></td>
					<td width='100px'><input type="text" name="text14[]" id="text14_" readonly="readonly"/></td>
					<td width='250px'><input type="text" name="text15[]" id="text15_" readonly="readonly"/></td>
					<td width='100px'><select name="text60[]" id="text60_"> </select></td> <!-- TIPE NORMA -->
					<td width='100px'><select name="text16[]" id="text16_"> </select></td>
					<td width='100px'><input type="text" name="text17[]" id="text17_" readonly="readonly" class='integer'/></td>
					<td width='100px'><input type="text" name="text18[]" id="text18_" readonly="readonly" class='integer'/></td>
					
					<!-- BULAN AWAL ROTASI -->
					<td width='100px'><select name="text50[]" id="text50_"></select></td>
					
					<td width='100px'><select name="text19[]" id="text19_"> </select></td>
					<!--td width='250px'><input type="text" name="text48[]" id="text48_" readonly="readonly"/></td-->
					<td width='100px'><input type="text" name="text49[]" id="text49_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text20[]" id="text20_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text21[]" id="text21_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text22[]" id="text22_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text23[]" id="text23_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text24[]" id="text24_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text25[]" id="text25_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text26[]" id="text26_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text27[]" id="text27_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text28[]" id="text28_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text29[]" id="text29_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text30[]" id="text30_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text31[]" id="text31_" readonly="readonly" class='number'/></td>
					<td width='100px'><input type="text" name="text32[]" id="text32_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text37[]" id="text37_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text38[]" id="text38_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text39[]" id="text39_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text40[]" id="text40_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text41[]" id="text41_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text42[]" id="text42_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text43[]" id="text43_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text44[]" id="text44_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text45[]" id="text45_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text46[]" id="text46_" readonly="readonly" class='number'/></td>
					<td width='120px'><input type="text" name="text47[]" id="text47_" readonly="readonly" class='number'/></td>
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
	//$("#btn_unlock").hide();
	//$("#btn_lock").hide();
	var activitycode = self.location.toString().split("/")[7];
	if(activitycode){
		$.ajax({
			type     : "post",
			url      : "rkt-lc/get-activity-name",
			data     : 'activitycode='+activitycode,
			cache    : false,
			dataType: "json",
			success  : function(data) {
				count = data.count;
				if (count > 0) {
					$.each(data.rows, function(key, row) {
						$('#src_activity_code').val(activitycode);
						$('#src_activity_desc').val(row.DESCRIPTION);
						$('#activity_uom').val(row.UOM);
					});
				}else{
					alert("No Land Type Found");
				}
			}
		});	
	}
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       970,   // required
		height:      400,   // required
		numFrozen:   4,     // optional
		frozenWidth: 245,   // optional
		clearWidths: false,  // optional
	});//freezeTableColumns
	
	//ACTION BUTTON
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_activity_desc = $("#src_activity_desc").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_activity_desc == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
			//uom distribusi qty
			$(".activity_uom").html( "- " + $("#activity_uom").val() );
			
			//sequence validation
			$.ajax({
							type    : "post",
							url     : "rkt-manual-non-infra-opsi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
										url      : "rkt-manual-non-infra-opsi/get-status-periode", //cek status periode
										data     : $("#form_init").serialize(),
										cache    : false,
										dataType: "json",
										success  : function(data) {
											$("#btn_save_temp").hide();
											if (data == 'CLOSE') {
													$("#btn_save").hide();
													
												}else{
													/*$.ajax({
														type    : "post",
														url     : "rkt-manual-non-infra-opsi/check-locked-seq", //check apakah status lock sendiri apakah lock
														data    : $("#form_init").serialize(),
														cache   : false,
														dataType: "json",
														success : function(data) {
															if(data.STATUS == 'LOCKED'){
																$("#btn_save").hide();
																$("#btn_unlock").show();
																$("#btn_lock").hide();
															}else{*/
																$("#btn_save").show();
																//$("#btn_unlock").hide();
																//$("#btn_lock").show();
															//}
														//}
													//})
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
						
			//<!-- TIPE NORMA -->
			//ambil jenis norma
			$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/get-tipe-norma",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text60_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text60_').append(new Option(row.NILAI,row.NILAI));
						});
					}
				}
			});	
			
			//ambil nilai activity class
			$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/get-activity-class",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text16_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text16_').append(new Option(row.NILAI,row.NILAI));
						});
						//page_num = (page_num) ? page_num : 1;
						//getData();
					}else{
						clearDetail();
						alert("Rotasi di RKT 0 (kosong) maka perhitungan Cost akan = 0 (nol).");
					}
					//getData();
				}
			});

			$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/get-activity-opsi",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text19_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text19_').append(new Option(row.NILAI,row.KODE));
						});
					}
				}
			});	
			
			//cek status periode
			/*$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/get-status-periode",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					if (data == 'CLOSE') {
							$("#btn_save_temp").hide();
							$("#btn_save").hide();
						}else{
							$("#btn_save_temp").hide();
							$("#btn_save").show();
						}
					}
			});*/					
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });	
	//untuk proses simpan draft
	$("#btn_save_temp").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_activity_desc = $("#src_activity_desc").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_activity_desc == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/save-temp",
				data     : $("#form_init").serialize(),
				cache    : false,
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan tetapi belum diproses. Silahkan klik tombol 'Hitung' untuk memproses data.");
					}else if (data == "no_alert") {
					}else{
						alert(data);
					}
				}
			});			
		} 
    });	
	$("#btn_save").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_activity_desc = $("#src_activity_desc").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_activity_desc == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else if ( validateInput() == false ) {
			alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		} else {
			$.ajax({
				type    : "post",
				url     : "rkt-manual-non-infra-opsi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-manual-non-infra-opsi/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									$.ajax({
										type     : "post",
										url      : "rkt-manual-non-infra-opsi/save",
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
								//}
							//}
						//})
					}else{
						alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
					}
				}
			});
		}
    });	
	$("#btn_export_csv").live("click", function() {
		var reference_role = "<?=$this->referencerole?>";
		var budgetperiod = $("#budgetperiod").val();
		var src_region_code = $("#src_region_code").val();
		var key_find = $("#key_find").val();
		var src_matstage_code = $("#src_matstage_code").val();
		var src_afd = $("#src_afd").val();
		var src_activity_code = $("#src_activity_code").val();
		
		if ( ( reference_role == 'BA_CODE' ) && ( src_region_code == '' ) && ( key_find == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( src_region_code == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else if ( src_activity_code == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-manual-non-infra-opsi/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_matstage_code/" + src_matstage_code + "/src_activity_code/" + src_activity_code + "/src_afd/" + src_afd,'_blank');
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
	
	//PICK AFD
	$("#pick_afd").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/afdeling/bacode/" + bacode, "pick", 700, 400 );
    });
	$("#src_afd").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			var bacode = $("#key_find").val();
			popup("pick/afdeling/bacode/" + bacode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//PICK ACTIVITY
	$("#pick_activity").click(function() {
		popup("pick/activity-mapp/module/rktManualNonInfraOpsi", "pick", 700, 400 );
    });
	$("#src_activity_desc").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/afdeling-mapp/module/rktManualNonInfraOpsi", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//LOV UNTUK INPUTAN
	$("[id^=text16_]").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		$.ajax({
			type     : "post",
			url      : "rkt-manual-non-infra-opsi/get-rotation",
			data     : { PERIOD_BUDGET: $("#text02_" + row).val(), 
						 BA_CODE: $("#text03_" + row).val(), 
						 MATURITY_STAGE_SMS1: $("#text09_" + row).val(), 
						 MATURITY_STAGE_SMS2: $("#text10_" + row).val(), 
						 ATRIBUT: $("#text19_" + row).val(),
						 ACTIVITY_CLASS: $("#text16_" + row).val(),
						 LAND_TYPE: $("#hidden00_" + row).val(), 
						 TOPOGRAPHY: $("#hidden01_" + row).val(),
						 TIPE_NORMA: $("#text60_" + row).val() //<!-- TIPE NORMA --> 
					   },
			cache    : false,
			dataType : 'json',
			success  : function(data) {
				$("#text17_" + row).val(accounting.formatNumber(data.ROTASI_SMS1, 0));
				$("#text18_" + row).val(accounting.formatNumber(data.ROTASI_SMS2, 0));
			}
		});
    });
	
	//<!-- TIPE NORMA -->
	//jika ada perubahan pilihan norma umum / khusus
	$("[id^=text60_]").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		$.ajax({
			type     : "post",
			url      : "rkt-manual-non-infra-opsi/get-rotation",
			data     : { PERIOD_BUDGET: $("#text02_" + row).val(), 
						 BA_CODE: $("#text03_" + row).val(), 
						 MATURITY_STAGE_SMS1: $("#text09_" + row).val(), 
						 MATURITY_STAGE_SMS2: $("#text10_" + row).val(), 
						 ATRIBUT: $("#text19_" + row).val(),
						 ACTIVITY_CLASS: $("#text16_" + row).val(),
						 LAND_TYPE: $("#hidden00_" + row).val(), 
						 TOPOGRAPHY: $("#hidden01_" + row).val(), 
						 TIPE_NORMA: $("#text60_" + row).val() //<!-- TIPE NORMA -->
					   },
			cache    : false,
			dataType : 'json',
			success  : function(data) {
				$("#text17_" + row).val(accounting.formatNumber(data.ROTASI_SMS1, 0));
				$("#text18_" + row).val(accounting.formatNumber(data.ROTASI_SMS2, 0));
			}
		});
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
	
	//onclick "i" load data ha
	$("[id^=text2], [id^=text3]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var id = $(this).attr("id").split("text")[1];
		var ha = $("#text11_" + row).val(); //data HA planted
		//tekan F9
		if (($("#text" + id).attr("readonly") == false) && (event.keyCode == 73)){
			$("#text" + id).val(accounting.formatNumber(ha,2));
        }
    });
	
	//DEKLARASI SELECT BOX UNTUK PILIHAN BULAN
	$('#text50_').append(new Option('1 - JAN', '1'));
	$('#text50_').append(new Option('2 - FEB', '2'));
	$('#text50_').append(new Option('3 - MAR', '3'));
	$('#text50_').append(new Option('4 - APR', '4'));
	$('#text50_').append(new Option('5 - MAY', '5'));
	$('#text50_').append(new Option('6 - JUN', '6'));
	$('#text50_').append(new Option('7 - JUL', '7'));
	$('#text50_').append(new Option('8 - AUG', '8'));
	$('#text50_').append(new Option('9 - SEP', '9'));
	$('#text50_').append(new Option('10 - OCT', '10'));
	$('#text50_').append(new Option('11 - NOV', '11'));
	$('#text50_').append(new Option('12 - DEC', '12'));
	$('#text50_').append(new Option('-', '0'));
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-manual-non-infra-opsi/upd-locked-seq-status",
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
				url      : "rkt-manual-non-infra-opsi/upd-locked-seq-status",
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

//validasi text field yang harus diisi
function validate(){	
	var result = true;
	
	$("[class*=distribusi_kerja]").each(function(key, row) {
		if (key > 0){
			var mystring = this.value;
			var value = mystring.split(',').join('');
			
			var row = $(this).attr("id").split("_")[1];
			var mystring1 = $("#text11_" + row).val();
			var ha_planted = mystring1.split(',').join('');
			
			if (parseFloat(value) > parseFloat(ha_planted)) {
				$(this).addClass("error");
				$(this).focus();
				result = false;
			}else{
				$(this).removeClass("error");
			}
		}
	});
	
	return result;
}

//untuk cek jumlah rotasi per semester
function checkRotation(){	
	var result = true;
	
	$('input[id^=text17_]').each(function(key, row) {
		if (key > 0) {
			var rotasi_inputan = 0;		
			var row = $(this).attr("id").split("_")[1];
			
			var rotasi_norma = $("#text17_" + row).val();
			
			if ($("#text20_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text21_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text22_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text23_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text24_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text25_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			
			if (parseInt(rotasi_inputan) > parseInt(rotasi_norma)) { 
				$(this).addClass("error");
				$("#text20_" + row).addClass("error");
				$("#text21_" + row).addClass("error");
				$("#text22_" + row).addClass("error");
				$("#text23_" + row).addClass("error");
				$("#text24_" + row).addClass("error");
				$("#text25_" + row).addClass("error");
				result = false; 
			}else{
				$(this).removeClass("error");
			}
		}
	});
	
	$('input[id^=text18_]').each(function(key, row) {
		if (key > 0) {
			var rotasi_inputan = 0;		
			var row = $(this).attr("id").split("_")[1];
			
			var rotasi_norma = $("#text18_" + row).val();
			
			if ($("#text26_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text27_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text28_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text29_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text30_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			if ($("#text31_" + row).val() != 0.00) { rotasi_inputan += 1 ; }
			
			if (parseInt(rotasi_inputan) > parseInt(rotasi_norma)) { 
				$(this).addClass("error");
				$("#text26_" + row).addClass("error");
				$("#text27_" + row).addClass("error");
				$("#text28_" + row).addClass("error");
				$("#text29_" + row).addClass("error");
				$("#text30_" + row).addClass("error");
				$("#text31_" + row).addClass("error");
				result = false; 
			}else{
				$(this).removeClass("error");
			}
		}
	});
	
	return result;
}

function getData(){
    $("#page_num").val(page_num);
	
    //
    $.ajax({
        type    : "post",
        url     : "rkt-manual-non-infra-opsi/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			if (data.return == 'locked') {
				alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
				$("#btn_save").hide();
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
						$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=hidden00_]").val(row.LAND_TYPE);
						$("#data_freeze tr:eq(" + index + ") input[id^=hidden01_]").val(row.TOPOGRAPHY);
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val(row.TRX_RKT_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text51_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
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
						
						//right freeze panes row
						$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.TAHUN_TANAM);
						$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.LAND_TYPE_DESC);
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.TOPOGRAPHY_DESC);
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.MATURITY_STAGE_SMS1);
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.MATURITY_STAGE_SMS2);
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.HA_PLANTED, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.POKOK_TANAM, 0));
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.SPH, 0));
						$("#data tr:eq(" + index + ") input[id^=text14_]").val($("#src_activity_code").val());
						$("#data tr:eq(" + index + ") input[id^=text15_]").val($("#src_activity_desc").val());
						$("#text60_" + index + " option[value='"+row.TIPE_NORMA+"']").attr("selected", "selected"); //<!-- TIPE NORMA -->
						$("#text16_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").attr("selected", "selected");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.ROTASI_SMS1, 0));
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.ROTASI_SMS2, 0));
						$("#text19_" + index + " option[value='"+row.ATRIBUT+"']").attr("selected", "selected");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.PLAN_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.PLAN_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.PLAN_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.PLAN_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.PLAN_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.PLAN_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text26_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.PLAN_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.PLAN_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text28_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.PLAN_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text29_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.PLAN_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.PLAN_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text31_]").addClass('distribusi_kerja');
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
						
						//jika rotasi 0 maka Rp/Rotasi juga 0
						var rp_rotasi_sms1 = (row.ROTASI_SMS1) ? row.TOTAL_RP_SMS1 : 0;
						var rp_rotasi_sms2 = (row.ROTASI_SMS2) ? row.TOTAL_RP_SMS2 : 0;
						
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(rp_rotasi_sms1, 2));
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(rp_rotasi_sms2, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.COST_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.COST_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.COST_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.COST_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.COST_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.COST_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.COST_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.COST_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.COST_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.COST_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.COST_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(row.COST_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(row.TOTAL_RP_SETAHUN, 2));
						//$("#data tr:eq(" + index + ") input[id^=text48_]").val(row.ATRIBUT_DESC);
						$("#data tr:eq(" + index + ") input[id^=text49_]").val(row.SUMBER_BIAYA);
						
						$("#text50_" + index + " option[value='"+row.AWAL_ROTASI+"']").attr("selected", "selected"); // awal rotasi
						
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}
			}
        }
    });
}
</script>
