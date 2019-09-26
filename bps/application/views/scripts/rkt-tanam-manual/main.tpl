<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan RKT Tanam Manual
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	17/07/2013
Update Terakhir		:	05/05/2015
Revisi				:	
NBU		05/05/2015	: - penutupan button lock & unlock di line 94 - 97
					  - penutupan pengecekan lock pada diri sendiri di line 357
					  - penutupan pengecekan lock untuk button save di line 460
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
			<legend>RKT TANAM MANUAL</legend>
			
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
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class='button' />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>AFDELING</th>
					<th>BLOK - DESC</th>
					<th>LAND TYPE</th>
					<th>TOPOGRAFI</th>
					<th>TAHUN TANAM</th>
					<th>MATURITY STAGE<BR>SMS 1</th>
					<th>MATURITY STAGE<BR>SMS 2</th>
					<th>LUAS TANAM<BR>HA</th>
					<th>LUAS TANAM<BR>POKOK</th>
					<th>LUAS TANAM<BR>SPH</th>
					
					<th>SUMBER<BR>BIAYA</th>
					<th>ACTIVITY<BR>CLASS</th>
					<th>NORMA TYPE</th>
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
					<th>TOTAL<BR>RP/QTY</th>
					<th>DISTRIBUSI TOTAL COST<BR>JAN</th>
					<th>DISTRIBUSI TOTAL COST<BR>FEB</th>
					<th>DISTRIBUSI TOTAL COST<BR>MAR</th>
					<th>DISTRIBUSI TOTAL COST<BR>APR</th>
					<th>DISTRIBUSI TOTAL COST<BR>MEI</th>
					<th>DISTRIBUSI TOTAL COST<BR>JUN</th>
					<th>DISTRIBUSI TOTAL COST<BR>JUL</th>
					<th>DISTRIBUSI TOTAL COST<BR>AGS</th>
					<th>DISTRIBUSI TOTAL COST<BR>SEP</th>
					<th>DISTRIBUSI TOTAL COST<BR>OKT</th>
					<th>DISTRIBUSI TOTAL COST<BR>NOV</th>
					<th>DISTRIBUSI TOTAL COST<BR>DES</th>
					<th>DISTRIBUSI TOTAL COST<BR>YEAR</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td width='50px'>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" value='5'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='75px'><input type="text" name="text43[]" id="text43_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text06[]" id="text06_" readonly="readonly" value='6'/></td>
					<td width='100px'><input type="text" name="text07[]" id="text07_" readonly="readonly" value='7'/></td>
					<td width='100px'><input type="text" name="text08[]" id="text08_" readonly="readonly" value='8'/></td>
					<td width='100px'><input type="text" name="text09[]" id="text09_" readonly="readonly" value='9'/></td>
					<td width='100px'><input type="text" name="text10[]" id="text10_" readonly="readonly" value='10'/></td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly" value='11'/></td>
					<td width='100px'><input type="text" name="text12[]" id="text12_" readonly="readonly" value='12'/></td>
					<td width='100px'><input type="text" name="text13[]" id="text13_" readonly="readonly" value='13'/></td>
					<td width='100px'><input type="text" name="text14[]" id="text14_" readonly="readonly"/></td>
					<td width='100px'><select name="text15[]" id="text15_"></select></td>
					<td width='100px'>
						<select name="text55[]" id="text55_">
							<!--<option value=0>UMUM</option>
							<option value=1>KHUSUS</option>-->
						</select></td>
					<td width='100px'><input type="text" name="text16[]" id="text16_" value='0'/></td>
					<td width='100px'><input type="text" name="text17[]" id="text17_" value='0'/></td>
					<td width='100px'><input type="text" name="text18[]" id="text18_" value='0'/></td>
					<td width='100px'><input type="text" name="text19[]" id="text19_" value='0'/></td>
					<td width='100px'><input type="text" name="text20[]" id="text20_" value='0'/></td>
					<td width='100px'><input type="text" name="text21[]" id="text21_" value='0'/></td>
					<td width='100px'><input type="text" name="text22[]" id="text22_" value='0'/></td>
					<td width='100px'><input type="text" name="text23[]" id="text23_" value='0'/></td>
					<td width='100px'><input type="text" name="text24[]" id="text24_" value='0'/></td>
					<td width='100px'><input type="text" name="text25[]" id="text25_" value='0'/></td>
					<td width='100px'><input type="text" name="text26[]" id="text26_" value='0'/></td>				
					<td width='120px'><input type="text" name="text27[]" id="text27_" value='0'/></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" readonly="readonly" value='28'/></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" readonly="readonly" value='29'/></td>
					
					<td width='120px'><input type="text" name="text30[]" id="text30_" readonly="readonly" value='30'/></td>
					<td width='120px'><input type="text" name="text31[]" id="text31_" readonly="readonly" value='31'/></td>
					<td width='120px'><input type="text" name="text32[]" id="text32_" readonly="readonly" value='32'/></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" readonly="readonly" value='33'/></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" readonly="readonly" value='34'/></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" readonly="readonly" value='35'/></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" readonly="readonly" value='36'/></td>
					<td width='120px'><input type="text" name="text37[]" id="text37_" readonly="readonly" value='37'/></td>
					<td width='120px'><input type="text" name="text38[]" id="text38_" readonly="readonly" value='38'/></td>
					<td width='120px'><input type="text" name="text39[]" id="text39_" readonly="readonly" value='39'/></td>
					<td width='120px'><input type="text" name="text40[]" id="text40_" readonly="readonly" value='40'/></td>
					<td width='120px'><input type="text" name="text41[]" id="text41_" readonly="readonly" value='41'/></td>
					
					<td width='120px'><input type="text" name="text42[]" id="text42_" readonly="readonly" value='42'/></td>
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
	$("#btn_unlock").hide();
	$("#btn_lock").hide();
	
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
	
	//BUTTON ACTION
	$("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_activity_desc = $("#src_activity_desc").val();
		var budgetperiod = $("#budgetperiod").val();
		var activity = $("#src_activity_code").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else if ( src_activity_desc == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
			//uom distribusi qty
			$(".activity_uom").html( "- " + $("#activity_uom").val() );
			
			//<!-- TIPE NORMA -->
			//ambil jenis norma
			$.ajax({
				type     : "post",
				url      : "rkt-tanam-manual/get-tipe-norma",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text55_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text55_').append(new Option(row.NILAI,row.NILAI));
						});
					}
				}
			});	
			//END TIPE NORMA
			
			$.ajax({
				type     : "post",
				url      : "rkt-tanam-manual/get-activity-class",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text15_')
						.find('option')
						.remove()
						.end();
						$.each(data.rows, function(key, row) {
							$('#text15_').append(new Option(row.NILAI,row.NILAI));
						});
						$.ajax({
							type    : "post",
							url     : "rkt-tanam-manual/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
										url      : "rkt-tanam-manual/get-status-periode", //cek status periode
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
														url     : "rkt-tanam-manual/check-locked-seq", //check apakah status lock sendiri apakah lock
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
					}else{
						alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
					}
				}
			});
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
				url      : "rkt-tanam-manual/save-temp",
				data     : $("#form_init").serialize(),
				cache    : false,
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan tetapi belum diproses. Silahkan klik tombol Hitung untuk memproses data.");								
						//hapus row id, biar kesimpan di TR_RKT_CAPEX
						//$("input[id^=text00_]").val('');
						
						//rubah warna rowid temp
						$("input[id^=text00_]").each(function(key,value) {
							if ((key > 0) && ($("#rowidtemp_"+key).val())) cekTempData(key);
						});
					}else if (data == "no_alert") {
						//hapus row id, biar kesimpan di TR_RKT_CAPEX
						//$("input[id^=text00_]").val('');
						
						//rubah warna rowid temp
						$("input[id^=text00_]").each(function(key,value) {
							if ((key > 0) && ($("#rowidtemp_"+key).val())) cekTempData(key);
						});
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} /*else if ( validate() == false ) {
			alert("Jumlah Distribusi Kerja Tidak Boleh Melebihi Jumlah HA Tanam.");
		}*/ else {
			$.ajax({
				type    : "post",
				url     : "rkt-tanam-manual/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-tanam-manual/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									
										$.ajax({
											type     : "post",
											url      : "rkt-tanam-manual/save",
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
	$("#btn_export_csv").live("click", function() {	
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var src_activity_code = $("#src_activity_code").val();	//ACTIVITY
		var src_matstage_code = $("#src_matstage_code").val();	//MATURITY STAGE
		var src_afd = $("#src_afd").val();					//SEARCH AFD
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else if ( src_activity_code == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		}else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-tanam-manual/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_activity_code/" + src_activity_code + "/src_afd/" + src_afd + "/src_matstage_code/" + src_matstage_code,'_blank');
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
			var regionCode = $("#src_region_code").val();
			popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });	
	
	//PICK AFD
	$("#pick_afd").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/afd-topo-land/bacode/" + bacode, "pick", 700, 400 );
    });
	$("#src_afd").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			var bacode = $("#key_find").val();
			popup("pick/afd-topo-land/bacode/" + bacode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	

		
	
	
	//PICK ACTIVITY
	$("#pick_activity").click(function() {
		popup("pick/activity-mapp/module/rktTanamManual", "pick", 700, 400 );
    });
	$("#src_activity_desc").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/afdeling-mapp/module/rktTanamManual", "pick", 700, 400 );
        }else{
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
	
	//onclick "i" load data ha
	$("[id^=text2], [id^=text3]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var id = $(this).attr("id").split("text")[1];
		var ha = $("#text11_" + row).val(); //data HA planted
		//tekan F9
		if (($("#text" + id).attr("readonly") == false) && (event.keyCode == 73)){
			$("#text" + id).val(accounting.formatNumber(ha,2));
			$("#tChange_" + row).val("Y");
			$("#data tr:eq(" + row + ") select[id^=text], #data tr:eq(" + row + ") input[id^=text]").addClass("edited");
        }
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-tanam-manual/upd-locked-seq-status",
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
				url      : "rkt-tanam-manual/upd-locked-seq-status",
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
				alert(parseFloat(value)+'>'+parseFloat(ha_planted));
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

function getData(){
    $("#page_num").val(page_num);
	var YEAR = parseFloat(0);
	var DIS_JAN = parseFloat(0);
	var DIS_FEB = parseFloat(0);
	var DIS_MAR = parseFloat(0);
	var DIS_APR = parseFloat(0);
	var DIS_MAY = parseFloat(0);
	var DIS_JUN = parseFloat(0);
	var DIS_JUL = parseFloat(0);
	var DIS_AUG = parseFloat(0);
	var DIS_SEP = parseFloat(0);
	var DIS_OCT = parseFloat(0);
	var DIS_NOV = parseFloat(0);
	var DIS_DEC = parseFloat(0);
	
    //
    $.ajax({
        type    : "post",
        url     : "rkt-tanam-manual/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
            count = data.count;
			//alert('count: '+count);
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
					if (row.ROW_ID_TEMP) {cekTempData(index);}
									
					//left freeze panes row
					$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val(row.TRX_RKT_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text43_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
					$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
					
					//right freeze panes
                    var tr = $("#data tr:eq(0)").clone();
                    $("#data").append(tr);
                    var index = ($("#data tr").length - 1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});
					
					//mewarnai jika row nya berasal dari temporary table
					if (row.ROW_ID_TEMP) {cekTempData(index);}
					
					//right freeze panes row
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.LAND_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.TOPOGRAPHY);
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.TAHUN_TANAM);
					$("#data tr:eq(" + index + ") input[id^=text08_]").css('text-align', 'right');
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.MATURITY_STAGE_SMS1);
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.MATURITY_STAGE_SMS2);
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.HA_PLANTED, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.POKOK_TANAM, 0));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.SPH, 0));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(row.SUMBER_BIAYA);
					
					$("#text15_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").attr("selected", "selected");
					$("#text15_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").addClass("required");
					
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.PLAN_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.PLAN_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.PLAN_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.PLAN_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.PLAN_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.PLAN_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.PLAN_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.PLAN_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.PLAN_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.PLAN_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.PLAN_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass('distribusi_kerja');
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.TOTAL_RP_QTY, 2));//TOTAL_RP_QTY
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
					
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.COST_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.COST_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.COST_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.COST_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.COST_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.COST_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.COST_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.COST_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.COST_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.COST_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text39_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.COST_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text40_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.COST_DEC , 2));
					$("#data tr:eq(" + index + ") input[id^=text41_]").addClass("number");
					
					$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.TOTAL_RP_SETAHUN , 2));
					$("#data tr:eq(" + index + ") input[id^=text42_]").addClass("number");
					
					$("#text55_" + index + " option[value='"+row.TIPE_NORMA+"']").attr("selected", "selected");
					$("#data tr:eq(" + index + ") input[id^=text56_]").val(accounting.formatNumber(row.ROTASI_SMS1, 0));
					$("#data tr:eq(" + index + ") input[id^=text57_]").val(accounting.formatNumber(row.ROTASI_SMS2, 0));
					
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();		
					
					//readonly untuk pupuk kacangan 40300 karena otomatis
					if (row.ACTIVITY_CODE == '40300'){
						$("#data tr:eq(" + index + ") input[id^=text16_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text17_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text18_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text19_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text20_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text21_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text22_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text23_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text24_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text25_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text26_]").attr('readonly', true);
						$("#data tr:eq(" + index + ") input[id^=text27_]").attr('readonly', true);
					}
					//perhitungan total
					YEAR = (row.TOTAL) ? YEAR + parseFloat(row.TOTAL) : YEAR;
					DIS_JAN = (row.JAN) ? DIS_JAN + parseFloat(row.JAN) : DIS_JAN;
					DIS_FEB = (row.FEB) ? DIS_FEB + parseFloat(row.FEB) : DIS_FEB;
					DIS_MAR = (row.MAR) ? DIS_MAR + parseFloat(row.MAR) : DIS_MAR;
					DIS_APR = (row.APR) ? DIS_APR + parseFloat(row.APR) : DIS_APR;
					DIS_MAY = (row.MAY) ? DIS_MAY + parseFloat(row.MAY) : DIS_MAY;
					DIS_JUN = (row.JUN) ? DIS_JUN + parseFloat(row.JUN) : DIS_JUN;
					DIS_JUL = (row.JUL) ? DIS_JUL + parseFloat(row.JUL) : DIS_JUL;
					DIS_AUG = (row.AUG) ? DIS_AUG + parseFloat(row.AUG) : DIS_AUG;
					DIS_SEP = (row.SEP) ? DIS_SEP + parseFloat(row.SEP) : DIS_SEP;
					DIS_OCT = (row.OCT) ? DIS_OCT + parseFloat(row.OCT) : DIS_OCT;
					DIS_NOV = (row.NOV) ? DIS_NOV + parseFloat(row.NOV) : DIS_NOV;
					DIS_DEC = (row.DEC) ? DIS_DEC + parseFloat(row.DEC) : DIS_DEC;
                });
				/*
				$("#total14").val(accounting.formatNumber(DIS_JAN, 2));
				$("#total14").addClass("number grandtotal_text");
				$("#total15").val(accounting.formatNumber(DIS_FEB, 2));
				$("#total15").addClass("number grandtotal_text");
				$("#total16").val(accounting.formatNumber(DIS_MAR, 2));
				$("#total16").addClass("number grandtotal_text");
				$("#total17").val(accounting.formatNumber(DIS_APR, 2));
				$("#total17").addClass("number grandtotal_text");
				$("#total18").val(accounting.formatNumber(DIS_MAY, 2));
				$("#total18").addClass("number grandtotal_text");
				$("#total19").val(accounting.formatNumber(DIS_JUN, 2));
				$("#total19").addClass("number grandtotal_text");
				$("#total20").val(accounting.formatNumber(DIS_JUL, 2));
				$("#total20").addClass("number grandtotal_text");
				$("#total21").val(accounting.formatNumber(DIS_AUG, 2));
				$("#total21").addClass("number grandtotal_text");
				$("#total22").val(accounting.formatNumber(DIS_SEP, 2));
				$("#total22").addClass("number grandtotal_text");
				$("#total23").val(accounting.formatNumber(DIS_OCT, 2));
				$("#total23").addClass("number grandtotal_text");
				$("#total24").val(accounting.formatNumber(DIS_NOV, 2));
				$("#total24").addClass("number grandtotal_text");
				$("#total25").val(accounting.formatNumber(DIS_DEC, 2));
				$("#total25").addClass("number grandtotal_text");
				$("#total26").val(accounting.formatNumber(YEAR, 2));
				$("#total26").addClass("number grandtotal_text");
				
				$("#total27").addClass("grandtotal_text");
				$("#total28").addClass("grandtotal_text");
				$("#total29").addClass("grandtotal_text");
				$("#total30").addClass("grandtotal_text");
				$("#total31").addClass("grandtotal_text");
				$("#total32").addClass("grandtotal_text");
				$("#total33").addClass("grandtotal_text");
				$("#total34").addClass("grandtotal_text");
				$("#total35").addClass("grandtotal_text");
				$("#total36").addClass("grandtotal_text");
				$("#total37").addClass("grandtotal_text");
				$("#total38").addClass("grandtotal_text");
				$("#tfoot").show();
				*/
            }
			else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
