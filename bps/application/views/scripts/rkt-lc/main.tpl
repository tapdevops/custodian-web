<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan RKT LC
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	05/05/2015
Revisi				:	
	YIR 19/06/2014	: 	- perubahan LoV menjadi combo box untuk pilihan region & maturity status
	SID 24/06/2014	: 	- fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
						- menghilangkan filter maturity status
						- perbaikan LoV afdeling saat pengisian RKT
						- penambahan info untuk lock table pada tombol cari, simpan, hapus
	NBU 05/05/2015	: 	- penutupan button lock dan unlock di line 94 & 95
						- penutupan pengecekan lock pada diri sendiri di line 370
						- penutupan pengecekan lock untuk button save di line 465
						- penutupan pengecekan lock untuk button delete di line 575
	
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
					<td>AKTIVITAS :</td>
					<td>
						<input type="hidden" name="activity_code" id="activity_code" value="" style="width:200px;" />
						<input type="text" name="activity_name" id="activity_name" value="" style="width:200px;" class='filter'/>
						<input type="hidden" name="activity_uom" id="activity_uom" value="" style="width:200px;" />
						<input type="button" name="pick_activity" id="pick_activity" value="...">
					</td>
				</tr>			
				<tr>
					<td>AFDELING :</td>
					<td>
						<input type="text" name="src_afd" id="src_afd" value="" style="width:200px;"/>
						<input type="hidden" name="topo_afd" id="topo_afd" value="" style="width:200px;"/>
						<input type="hidden" name="land_afd" id="land_afd" value="" style="width:200px;"/>
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
			<legend>RKT LC</legend>
			
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
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class='button' />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>+</th>
					<th>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE<BR>AKTIVITAS</th>
					<th>DESKRIPSI</th>
					<th>AFD</th>
					<th>LAND TYPE</th>
					<th>TOPOGRAPHY</th>
					<th>SUMBER BIAYA</th>
					<th>CLASS ACTIVITY</th>
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
					<th>DISTRIBUSI KERJA<BR>TOTAL <span class='activity_uom'></span></th>
					<th>TOTAL RP/QTY</th>
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
					<th>DISTRIBUSI COST<BR>TOTAL</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td align='center' width='20px'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center' width='20px'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="trxrktcode[]" id="trxrktcode_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly"/>
						<input type="hidden" name="text003[]" id="text003_" readonly="readonly"/>
						<input type="hidden" name="text004[]" id="text004_" readonly="readonly"/>
						<input type="hidden" name="text006[]" id="text006_" readonly="readonly"/>
						<input type="hidden" name="text007[]" id="text007_" readonly="readonly"/>
						<input type="hidden" name="text008[]" id="text008_" readonly="readonly"/>
						<input type="hidden" name="text009[]" id="text009_" readonly="readonly"/>
						<input type="hidden" name="text010[]" id="text010_" readonly="readonly"/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text04[]" id="text04_" readonly="readonly"/></td>
					<td width='300px'><input type="text" name="text05[]" id="text05_" readonly="readonly"/></td>
					<td width='50px'><input type="text" name="text06[]" id="text06_" readonly="readonly" style="background-color:white;" /></td>
					<td width='100px'><select name="text07[]" id="text07_"></select></td>
					<td width='100px'><select name="text08[]" id="text08_"></select></td>
					<td width='100px'><input type="text" name="text09[]" id="text09_" readonly="readonly"/></td>
					<td width='100px'><select name="text10[]" id="text10_"></select></td>
					<td width='50px'><input type="text" name="text11[]" id="text11_"/></td>
					<td width='50px'><input type="text" name="text12[]" id="text12_"/></td>
					<td width='50px'><input type="text" name="text13[]" id="text13_"/></td>
					<td width='50px'><input type="text" name="text14[]" id="text14_"/></td>
					<td width='50px'><input type="text" name="text15[]" id="text15_"/></td>
					<td width='50px'><input type="text" name="text16[]" id="text16_"/></td>
					<td width='50px'><input type="text" name="text17[]" id="text17_"/></td>
					<td width='50px'><input type="text" name="text18[]" id="text18_"/></td>
					<td width='50px'><input type="text" name="text19[]" id="text19_"/></td>
					<td width='50px'><input type="text" name="text20[]" id="text20_"/></td>
					<td width='50px'><input type="text" name="text21[]" id="text21_"/></td>
					<td width='50px'><input type="text" name="text22[]" id="text22_"/></td>
					<td width='50px'><input type="text" name="text23[]" id="text23_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text24[]" id="text24_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text25[]" id="text25_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text26[]" id="text26_" readonly="readonly"/></td>					
					<td width='100px'><input type="text" name="text27[]" id="text27_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text28[]" id="text28_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text29[]" id="text29_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text30[]" id="text30_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text31[]" id="text31_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text32[]" id="text32_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text33[]" id="text33_" readonly="readonly"/></td>					
					<td width='100px'><input type="text" name="text34[]" id="text34_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text35[]" id="text35_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text36[]" id="text36_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text37[]" id="text37_" readonly="readonly"/></td>
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
	/*$("#btn_lock").hide();
	$("#btn_unlock").hide();
	*/
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
						$('#activity_code').val(activitycode);
						$('#activity_name').val(row.DESCRIPTION);
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
		numFrozen:   7,     // optional
		frozenWidth: 470,   // optional
		clearWidths: false,  // optional
	});//freezeTableColumns
	
	//DEKLARASI SELECT BOX
	$.ajax({
		type     : "post",
		url      : "rkt-lc/get-land-type",
		data     : $("#form_init").serialize(),
		cache    : false,
		dataType: "json",
		success  : function(data) {
			count = data.count;
			if (count > 0) {
				$.each(data.rows, function(key, row) {
					$('#text07_').append(new Option(row.PARAMETER_VALUE,row.PARAMETER_VALUE_CODE));
				});
			}else{
				alert("No Land Type Found");
			}
		}
	});	
	
	$.ajax({
		type     : "post",
		url      : "rkt-lc/get-topography",
		data     : $("#form_init").serialize(),
		cache    : false,
		dataType: "json",
		success  : function(data) {
			count = data.count;
			if (count > 0) {
				$.each(data.rows, function(key, row) {
					$('#text08_').append(new Option(row.PARAMETER_VALUE,row.PARAMETER_VALUE_CODE));
				});
			}else{
				alert("No Topography Found");
			}
		}
	});	
	
	//BUTTON ACTION	
	$("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		var activity_code = $("#activity_code").val();
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( activity_code == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
		
			//uom distribusi qty
			$(".activity_uom").html( "- " + $("#activity_uom").val() );
		
			$.ajax({
				type     : "post",
				url      : "rkt-lc/get-activity-class",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text10_')
						.find('option')
						.remove()
						.end();
						$.each(data.rows, function(key, row) {
							$('#text10_').append(new Option(row.NILAI,row.NILAI));
						});
						$.ajax({
							type    : "post",
							url     : "rkt-lc/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
										url      : "rkt-lc/get-status-periode", //cek status periode
										data     : $("#form_init").serialize(),
										cache    : false,
										dataType: "json",
										success  : function(data) {
										$("#btn_save_temp").hide();
											if (data == 'CLOSE') {
													$("#btn_save").hide();
													$("#btn_add").hide();
												}else{
													/*$.ajax({
														type    : "post",
														url     : "rkt-lc/check-locked-seq", //check apakah status lock sendiri apakah lock
														data    : $("#form_init").serialize(),
														cache   : false,
														dataType: "json",
														success : function(data) {
															if(data.STATUS == 'LOCKED'){
																$("#btn_save").hide();
																$("#btn_add").hide();
																$(".button_add").hide();
																$("input[id^=btn01_]").hide();
																$("#btn_unlock").show();
																$("#btn_lock").hide();
															}else{*/
																$("#btn_save").show();
																$("#btn_add").show();
																$(".button_add").show();
																$("input[id^=btn01_]").show();
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
									$("#btn_calc_all").hide();
									$("#btn_add").hide();
									$(".button_add").hide();
									$("input[id^=btn01_]").hide();
								}
							}
						}) 
					}else{
						alert("Aktivitas Belum Terdapat Pada Norma Biaya Maupun Norma Harga Borong.");
					}
				}
			});
		}
    });	
	
	$("#btn_refresh").click(function() {
		location.reload();
    });	
	
	$("#btn_add").live("click", function(event) {
		//left freeze panes
		var tr = $("#data_freeze tr:eq(0)").clone();
		$("#data_freeze").append(tr);
		
		var index = ($("#data_freeze tr").length - 1);					
		$("#data_freeze tr:eq(" + index + ")").find("input, select").each(function() {
			$(this).attr("id", $(this).attr("id") + index);
		});	
		
		//right freeze panes
		var tr = $("#data tr:eq(0)").clone();
		$("#data").append(tr);
		
		var index = ($("#data tr").length - 1);					
		$("#data tr:eq(" + index + ")").find("input, select").each(function() {
			$(this).attr("id", $(this).attr("id") + index);
		});		
		
		//set default field
		setDefaultField(index);
    });
	
	
	$("input[id^=btn00_]").live("click", function(event) {
		$("#btn_add").trigger("click");
    });
	
	//klik tombol delete
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "rkt-lc/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					/*$.ajax({
						type    : "post",
						url     : "rkt-lc/check-locked-seq",
						data    : $("#form_init").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){ 
								alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
							}else{*/
								if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
									//cek jika rowid kosong & klik delete, maka kosongkan seluruh data
									if (rowid == '') {
										clearTextField(row);
									}
									else {
										$.ajax({
											type     : "post",
											url      : "rkt-lc/delete",
											data     : { 
														 ROW_ID: $("#text00_" + row).val(), 
														 PERIOD_BUDGET: $("#text02_" + row).val(),
														 OLD_BA_CODE: $("#text003_" + row).val(),
														 OLD_ACTIVITY_CODE: $("#text004_" + row).val(),
														 OLD_ACTIVITY_CLASS: $("#text010_" + row).val(),
														 OLD_AFD_CODE: $("#text006_" + row).val(),
														 OLD_LAND_TYPE: $("#text007_" + row).val(),
														 OLD_TOPOGRAPHY: $("#text008_" + row).val(),
														 OLD_SUMBER_BIAYA: $("#text009_" + row).val(),
														 BA_CODE: $("#text03_" + row).val(),
														 ACTIVITY_CODE: $("#text04_" + row).val(),
														 ACTIVITY_CLASS: $("#text10_" + row).val(),
														 AFD_CODE: $("#text06_" + row).val(),
														 LAND_TYPE: $("#text07_" + row).val(),
														 TOPOGRAPHY: $("#text08_" + row).val(),
														 SUMBER_BIAYA: $("#text09_" + row).val()
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
							//}
						//}
					//})
				}else{
					alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				}
			}
		})
    });
	
	//untuk proses simpan draft
	$("#btn_save_temp").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var budgetperiod = $("#budgetperiod").val();
		var regioncode = $("#src_region").val();
		var bacode = $("#key_find").val();
	
		if( bacode == '' || regioncode == ''){
			alert('Anda Harus Memilih Region dan Business Area Terlebih Dahulu.');
		}
		else{ 
			$.ajax({
				type     : "post",
				url      : "rkt-lc/save-temp",
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
		var reference_role = "<?=$this->referencerole?>";
		var budgetperiod = $("#budgetperiod").val();
		var bacode = $("#key_find").val();
		var regioncode = $("#src_region_code").val();
		var activitycode = $("#activity_code").val();
		var activityname = $("#activity_name").val();
		
		if( bacode == '' || regioncode == '' || activitycode == ''){
			alert('Anda Harus Memilih Afdeling dan Activity Code Terlebih Dahulu.');
		} else if (validateInput() == false){
			alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		} else {
			$.ajax({
				type    : "post",
				url     : "rkt-lc/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-lc/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									$.ajax({
										type     : "post",
										url      : "rkt-lc/save",
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
			})
		}
    });
	
	//LOV UNTUK INPUTAN
	$("input[id^=text06_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var baCode = $("#key_find").val();
		var bacode = $("#text03_" + row).val();
		var activity_code = $("#text04_" + row).val();
		var activity_class = $("#text10_" + row + " option:selected").val();
		//tekan F9
        if (event.keyCode == 120) {
			//if ($('#text06_'+row).is('[readonly]') == false) {// afdeling sesuai HS, request Darmo, remarked by NBU 01.09.2015
				popup("pick/afdeling-lc/module/rktLc/row/" + row + "/bacode/" + bacode, "pick", 700, 400 );
			//}			// afdeling sesuai HS, request Darmo, remarked by NBU 01.09.2015
        }
    });
	
	$("input[id^=text09_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var baCode = $("#key_find").val();
		var bacode = $("#text03_" + row).val();
		var activity_code = $("#text04_" + row).val();
		var activity_class = $("#text10_" + row + " option:selected").val();
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text09_'+row).is('[readonly]') == false) { 
				popup("pick/sumber-biaya/module/rktLc/row/" + row + "/bacode/" + bacode +"/activity/" + activity_code + "/class/" + activity_class, "pick", 700, 400 );
			}			
        }else{
			event.preventDefault();
		}
    });
	
	
	//DEFAULT INPUTAN
	function setDefaultField(index){
		var budgetperiod = $("#budgetperiod").val();
		var bacode = $("#key_find").val();
		var regioncode = $("#src_region_code").val();
		var activitycode = $("#activity_code").val();
		var activityname = $("#activity_name").val();
		var afdcode = $("#src_afd").val();		
		var trxCode = genTransactionCode(budgetperiod, bacode, 'LC');
		
		$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
		$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(bacode);
		$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(activitycode);
		$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(activityname);
		$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(afdcode);
		//$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("readonly", false); // afdeling sesuai HS, request Darmo, remarked by NBU 01.09.2015
		$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("required");
		
		$("#text07_" + index + " option[value='"+$("#data tr:eq(" + (index-1) + ") select[id^=text07_]").val()+"']").attr("selected", "selected");
		$("#text08_" + index + " option[value='"+$("#data tr:eq(" + (index-1) + ") select[id^=text08_]").val()+"']").attr("selected", "selected");
		
		$.ajax({
			type     : "post",
			url      : "rkt-lc/get-sumber-biaya",
			data     : $("#form_init").serialize(),
			cache    : false,
			dataType: "json",
			success  : function(data) {
				$("#data tr:eq(" + index + ") input[id^=text09_]").val(data);
			}
		});
		
		$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text11_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text12_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text13_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text14_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text15_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text16_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text17_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text18_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text19_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
		
		$("#data tr:eq(" + index + ") input[id^=text20_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text21_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text22_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text23_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text24_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text25_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text26_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text27_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text28_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text29_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text30_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text31_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text32_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
		
		$("#data tr:eq(" + index + ") input[id^=text33_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text34_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text35_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text36_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text37_]").val("0.00");
		$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
		
		$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
	}
	
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
	
	$("#pick_ba").click(function() {
		var regionCode = $("#src_region_code").val();
		popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
    });
	
    $("#src_ba").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			var regionCode = $("#src_region_code").val();
			popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });	
	
	$("#pick_afd").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/afdeling-lc/bacode/" + bacode, "pick", 700, 400 );
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
	
	$("#pick_activity").click(function() {
		popup("pick/activity-mapp/module/rktLc", "pick", 700, 400 );
    });
	
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
	
	$("#btn_export_csv").live("click", function() {
		var budgetperiod = $("#budgetperiod").val();
		var src_region_code = $("#src_region_code").val();
		var key_find = $("#key_find").val();
		var src_afd = $("#src_afd").val();
		var activity_code = $("#activity_code").val();
		
		window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-lc/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_afd/" + src_afd + "/activity_code/" + activity_code,'_blank');
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-lc/upd-locked-seq-status",
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
				url      : "rkt-lc/upd-locked-seq-status",
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
    
    $.ajax({
        type    : "post",
        url     : "rkt-lc/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			if (data.return == 'locked') {
				alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
				$("#btn_add").hide();
				$("#btn_save_temp").hide();
				$("#btn_save").hide();
				$("#btn01_").hide();
			} else {
				var activitycode = $("#activity_code").val();
				var activityname = $("#activity_name").val();
				
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
						
						$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=hidden00_]").val(row.LAND_TYPE);
						$("#data_freeze tr:eq(" + index + ") input[id^=hidden01_]").val(row.TOPOGRAPHY);
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data_freeze tr:eq(" + index + ") input[id^=trxrktcode]").val(row.TRX_RKT_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						
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
						
						//alert(row.ROW_ID);
						if((row.ROW_ID!=null)||(row.ROW_ID_TEMP!=null)){
							$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.ACTIVITY_CODE);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text004_]").val(row.ACTIVITY_CODE);
							$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.ACTIVITY_DESC);
							//01-jan-2014
							$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
							$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text003_]").val(row.BA_CODE);
							
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.AFD_CODE);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text006_]").val(row.AFD_CODE);
							//$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("readonly", false); // afdeling sesuai HS, request Darmo, remarked by NBU 01.09.2015
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("required");
			
							$("#text07_" + index + " option[value='"+row.LAND_TYPE+"']").attr("selected", "selected");
							$("#text08_" + index + " option[value='"+row.TOPOGRAPHY+"']").attr("selected", "selected");
							//$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.SUMBER_BIAYA);
							$.ajax({
								type     : "post",
								url      : "rkt-lc/get-sumber-biaya",
								data     : $("#form_init").serialize(),
								cache    : false,
								dataType: "json",
								success  : function(data) {
									$("#data tr:eq(" + index + ") input[id^=text09_]").val(data);
								}
							});
		
							  $("#data_freeze tr:eq(" + index + ") input[id^=text007_]").val(row.LAND_TYPE);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text008_]").val(row.TOPOGRAPHY);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text009_]").val(row.SUMBER_BIAYA);
						}else{
							$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(activitycode);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text004_]").val(activitycode);
							$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(activityname);
							
							$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGETHS);
							$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODEHS);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text003_]").val(row.BA_CODEHS);
							
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.AFD_CODEHS);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text006_]").val(row.AFD_CODEHS);
							//$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("readonly", false); // afdeling sesuai HS, request Darmo, remarked by NBU 01.09.2015
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
							$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("required");
							
							$("#text07_" + index + " option[value='"+row.LAND_TYPEHS+"']").attr("selected", "selected");
							$("#text08_" + index + " option[value='"+row.TOPOGRAPHYHS+"']").attr("selected", "selected");
							$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
							  $("#data_freeze tr:eq(" + index + ") input[id^=text007_]").val(row.LAND_TYPEHS);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text008_]").val(row.TOPOGRAPHYHS);
							  $("#data_freeze tr:eq(" + index + ") input[id^=text009_]").val("");
						}                    
						
						$("#text10_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").attr("selected", "selected");		
						$("#data_freeze tr:eq(" + index + ") input[id^=text010_]").val(row.ACTIVITY_CLASS);
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.PLAN_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.PLAN_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.PLAN_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.PLAN_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.PLAN_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.PLAN_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.PLAN_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.PLAN_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.PLAN_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.PLAN_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.PLAN_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.PLAN_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.TOTALRPQTY, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.COST_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.COST_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.COST_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.COST_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.COST_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.COST_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.COST_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.COST_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.COST_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.COST_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.COST_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.COST_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.COST_SETAHUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}else{
					$("#tfoot").hide();
				}
			}
        }
    });
}
</script>
