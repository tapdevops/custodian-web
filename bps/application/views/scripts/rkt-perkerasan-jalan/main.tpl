<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan RKT Perkerasan Jalan
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	29/07/2013
Update Terakhir		:	05/05/2015
Revisi				:	

YULIUS 07/07/2014	: - tambah file hidden TRX_RKT_CODE
					  - fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
					  - penambahan info untuk lock table pada tombol cari, simpan, hapus
					  
YULIUS 10/07/2014 	: 	- perbaikan cektempData dari ROW ID TEMP menjadi FLAG_TEMP 	
SID 15/07/2014		: 	- penambahan info window VRA	
NBU 05/05/2015		: 	- penutupan button lock & unlock di line 114 - 117
					    - penutupan pengecekan lock pada diri sendiri di line 378
					    - penutupan pengecekan lock untuk button save di line 629	
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
							<td>ACTIVITY :</td>
							<td>
								<input type="hidden" name="src_coa_code" id="src_coa_code" value="" style="width:200px;" />
								<input type="text" name="src_coa" id="src_coa" value="" style="width:200px;" class='filter'/>
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
								<input type="hidden" name="src_afd_code" id="src_afd_code" value="" style="width:200px;" />
								<input type="text" name="src_afd" id="src_afd" value="" style="width:200px;" />
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
			<legend>RKT PERKERASAN JALAN</legend>
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
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button"/>
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
					<th>LAND TYPE</th>
					<th>TOPOGRAPHY</th>
					<th>BULAN &<BR>TAHUN TANAM</th>
					<th>STATUS TANAM<BR>SEMESTER 1</th>
					<th>STATUS TANAM<BR>SEMESTER 2</th>
					<th>HA</th>
					<th>PKK</th>
					<th>SPH</th>
					<th>ROTASI SMS1</th>
					<th>ROTASI SMS2</th>
					<th>SUMBER BIAYA</th>
					<th>PERULANGAN / BARU</th>
					<th>JARAK LATERIT</th>
					<th>ACTUAL JALAN DIBUAT</th>
					<th>ACTUAL JALAN DIPERKERAS</th>
					<th>PLAN JAN (METER)</th>
					<th>PLAN FEB (METER)</th>
					<th>PLAN MAR (METER)</th>
					<th>PLAN APR (METER)</th>
					<th>PLAN MAY (METER)</th>
					<th>PLAN JUN (METER)</th>
					<th>PLAN JUL (METER)</th>
					<th>PLAN AUG (METER)</th>
					<th>PLAN SEP (METER)</th>
					<th>PLAN OCT (METER)</th>
					<th>PLAN NOV (METER)</th>
					<th>PLAN DEC (METER)</th>
					<th>TOTAL PLAN</th>
					<th>RP / QTY</th>
					<th>COST JAN</th>
					<th>COST FEB</th>
					<th>COST MAR</th>
					<th>COST APR</th>
					<th>COST MAY</th>
					<th>COST JUN</th>
					<th>COST JUL</th>
					<th>COST AUG</th>
					<th>COST SEP</th>
					<th>COST OCT</th>
					<th>COST NOV</th>
					<th>COST DEC</th>
					<th>TOTAL COST</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;">
					<!--left freeze panes-->
					<td width='100px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" />
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" />
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text04[]" id="text04_" readonly="readonly" /></td>
					<td width='75px'><input type="text" name="text52[]" id="text52_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text06[]" id="text06_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text07[]" id="text07_" readonly="readonly" /></td>
					<td width='100px'>
						<input type="text" name="text08[]" id="text08_" readonly="readonly" />
						<input type="hidden" name="bulan[]" id="bulan_" readonly="readonly" />
						<input type="hidden" name="tahun[]" id="tahun_" readonly="readonly" />
					</td>
					<td width='100px'><input type="text" name="text09[]" id="text09_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text10[]" id="text10_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text12[]" id="text12_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text13[]" id="text13_" readonly="readonly" /></td>
					
					<!--right freeze panes-->
					<td width='100px'><input type="text" name="text50[]" id="text50_" readonly="readonly" class='integer'/></td>
					<td width='100px'><input type="text" name="text51[]" id="text51_" readonly="readonly" class='integer'/></td>
					<td width='100px'><select name="text14[]" id="text14_"></select></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_" value="BARU" title="Tekan F9 Untuk Memilih."/></td>
					<td width='120px'>
						<input type="hidden" name="text16[]" id="text16_" readonly="readonly"/>
						<input type="text" name="jarak[]" id="jarak_" title="Tekan F9 Untuk Memilih."/>
					</td>
					<td width='120px'><input type="text" name="text17[]" id="text17_" value=0 /></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" value=0 /></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text25[]" id="text25_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text26[]" id="text26_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text27[]" id="text27_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text30[]" id="text30_" value=0 class="val_dis"/></td>
					<td width='120px'><input type="text" name="text31[]" id="text31_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text32[]" id="text32_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text37[]" id="text37_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text38[]" id="text38_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text39[]" id="text39_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text40[]" id="text40_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text41[]" id="text41_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text42[]" id="text42_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text43[]" id="text43_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text44[]" id="text44_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text45[]" id="text45_" readonly="readonly" value=0 /></td>
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
						<input type="button" name="btn_first" id="btn_first" value="&lt;&lt;" class="button" style="width:30px;" />
						<input type="button" name="btn_prev" id="btn_prev" value="&lt;" class="button" style="width:30px;" />
						<input type="button" name="btn_next" id="btn_next" value="&gt;" class="button" style="width:30px;" />
						<input type="button" name="btn_last" id="btn_last" value="&gt;&gt;" class="button" style="width:30px;" />
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
function deleteRow(row)
{
    var i=row.parentNode.parentNode.rowIndex;
    document.getElementById('content').deleteRow(i);
}

var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;

$(document).ready(function() {
	//$("#btn_lock").hide();
	//$("#btn_unlock").hide();
	
	$("#info_vra").hide();
    $("#search").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       970,   // required
		height:      400,   // required
		numFrozen:   12,    // optional
		frozenWidth: 470,   // optional
		clearWidths: false, // optional
	});//freezeTableColumns	
	
	//validasi plan distribusi
	$(".val_dis").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		if($("#text15_"+row).val() == 'BARU') {
			max_jalan = Number($("#text17_"+row).val().replace(",", "")) - Number($("#text18_"+row).val().replace(",", ""));
			valmonth = Number($("#text19_"+row).val().replace(",", ""))+
						Number($("#text20_"+row).val().replace(",", ""))+
						Number($("#text21_"+row).val().replace(",", ""))+
						Number($("#text22_"+row).val().replace(",", ""))+
						Number($("#text23_"+row).val().replace(",", ""))+
						Number($("#text24_"+row).val().replace(",", ""))+
						Number($("#text25_"+row).val().replace(",", ""))+
						Number($("#text26_"+row).val().replace(",", ""))+
						Number($("#text27_"+row).val().replace(",", ""))+
						Number($("#text28_"+row).val().replace(",", ""))+
						Number($("#text29_"+row).val().replace(",", ""))+
						Number($("#text30_"+row).val().replace(",", ""));
		} else {
			max_jalan = Number($("#text18_"+row).val().replace(",", ""));
			valmonth = Number($("#text19_"+row).val().replace(",", ""))+
						Number($("#text20_"+row).val().replace(",", ""))+
						Number($("#text21_"+row).val().replace(",", ""))+
						Number($("#text22_"+row).val().replace(",", ""))+
						Number($("#text23_"+row).val().replace(",", ""))+
						Number($("#text24_"+row).val().replace(",", ""))+
						Number($("#text25_"+row).val().replace(",", ""))+
						Number($("#text26_"+row).val().replace(",", ""))+
						Number($("#text27_"+row).val().replace(",", ""))+
						Number($("#text28_"+row).val().replace(",", ""))+
						Number($("#text29_"+row).val().replace(",", ""))+
						Number($("#text30_"+row).val().replace(",", ""));
		}


		console.log($("#text15_"+row).val()+':'+max_jalan);
		console.log(valmonth);

		if (Number(max_jalan) == 0 || Number(max_jalan) == ''){
			alert ("Field Aktual Jalan dibuat harus diisi terlebih dahulu untuk menentukan max distribusi plan!");
			$("#text17_"+row).addClass("error");
			$("#text17_"+row).focus();
			$(this).val(0);	
		}

		if (Number(valmonth) > Number(max_jalan)) {
				alert ("Maksimal Jarak yang boleh diinput adalah "+max_jalan+" !");
				$(this).val(0);
				$(this).focus();
		}
	});
	
	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_coa_code = $("#src_coa_code").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		var row = $(this).attr("id").split("_")[1];
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_coa_code == '' )  {
			alert("Anda Harus Memilih Group Perkerasan Jalan Terlebih Dahulu.");
		} else {
		
			//<!-- TIPE NORMA -->
			//ambil jenis norma
			$.ajax({
				type     : "post",
				url      : "rkt-perkerasan-jalan/get-tipe-norma",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text49_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text49_').append(new Option(row.NILAI,row.NILAI));
						});
					}
				}
			});	
			
			//ambil nilai sumber biaya
			$.ajax({
				type     : "post",
				url      : "rkt-perkerasan-jalan/get-sumber-biaya",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text14_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text14_').append(new Option(row.NILAI,row.NILAI));
						});
						page_num = (page_num) ? page_num : 1;
						
						$.ajax({
							type    : "post",
							url     : "rkt-perkerasan-jalan/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data==1){
									//cek status sequence current norma/rkt
									page_num = (page_num) ? page_num : 1;
									getData(); 
									getInfoVra(); //untuk info window VRA
									$.ajax({
										type     : "post",
										url      : "rkt-perkerasan-jalan/get-status-periode", //cek status periode
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
														url     : "rkt-perkerasan-jalan/check-locked-seq", //check apakah status lock sendiri apakah lock
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
																$("#info_vra").hide();
															}else{*/
																$("#info_vra").show();
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
									$("#info_vra").hide();
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
			popup("pick/business-area", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
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
	$("#pick_activity").click(function() {
		popup("pick/activity-mapp/module/rktPerkerasanJalan", "pick", 700, 400 );
    });
	$("#src_coa").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/activity-mapp/module/rktPerkerasanJalan", "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
		
	
	$("input[id^=text14_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var bacode = $("#key_find").val();
		var activity_code = $("#src_coa_code").val();
		var activity_class = $("#text16_" + row + " option:selected").val();
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/sumber-biaya/module/rktManualInfra/row/" + row + "/bacode/" + bacode +"/activity/" + activity_code + "/class/" + activity_class, "pick");
        }else{
			event.preventDefault();
		}
    });
		
	$("input[id^=text15_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var bacode = $("#key_find").val();
		var coa = $("#src_coa_code").val();
		var semester1 = $("#text09_" + row).val();
		var semester2 = $("#text10_" + row).val();
		
		//tekan F9
		if ((semester1 != 'TM') && (semester2 != 'TM')){
			event.preventDefault();
        }else if (event.keyCode == 120){
			//lov
			popup("pick/perulangan-baru/module/rktPerkerasanJalan/row/" + row + "/semester1/" + semester1 + "/semester2/" + semester2 , "pick");
        }else{
			event.preventDefault();
		}
    });
	
	$("input[id^=jarak_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var bacode = $("#key_find").val();
		var coa = $("#src_coa_code").val();
		
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/jarak-perkerasan-jalan/module/rktPerkerasanJalan/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	
	//validasi distribusi
	$("input[id^=jarak_]").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		max_sph = $("#maxNorma_"+row).val();
		isi_jarak = $("#jarak_"+row).val();
		alert(isi_jarak); return false;
		
	});
	
	//<!-- TIPE NORMA -->
	//jika ada perubahan pilihan norma umum / khusus
	$("[id^=text49_]").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		$.ajax({
			type     : "post",
			url      : "rkt-perkerasan-jalan/get-rotation",
			data     : { BA_CODE: $("#text03_" + row).val(), 
						 MATURITY_STAGE_SMS1: $("#text09_" + row).val(), 
						 MATURITY_STAGE_SMS2: $("#text10_" + row).val(), 
						 ACTIVITY_CODE: $("#text14_" + row).val(),
						 ACTIVITY_CLASS: $("#text16_" + row + " option:selected").val(),
						 LAND_TYPE: $("#hidden00_" + row).val(), 
						 TOPOGRAPHY: $("#hidden01_" + row).val(), 
						 TIPE_NORMA: $("#text49_" + row).val() //<!-- TIPE NORMA -->
					   },
			cache    : false,
			dataType : 'json',
			success  : function(data) {
				$("#text50_" + row).val(accounting.formatNumber(data.ROTASI_SMS1, 0));
				$("#text51_" + row).val(accounting.formatNumber(data.ROTASI_SMS2, 0));
			}
		});
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
	
	$("#btn_save").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var regioncode = $("#src_region").val();
		var bacode = $("#key_find").val();
		var coa_code = $("#src_coa_code").val();
		var budgetperiod = $("#budgetperiod").val();
		var coa = $("#src_coa").val();
		var current_budgetperiod = "<?=$this->period?>";		
	
		if( bacode == '' || regioncode == '' || coa == ''){
			alert('Anda Harus Memilih Region, Business Area, dan Group Perkerasan Jalan Terlebih Dahulu.');
		} else if (validateReq() == false){
			alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		//} else if ( validateInput() == false ) {
		//	alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		//} else if ( validate() == false ) {
		//	alert("Jalan diperkeras tidak boleh lebih dari Jalan dibuat.");
		} else {
			$.ajax({
				type    : "post",
				url     : "rkt-perkerasan-jalan/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-perkerasan-jalan/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									$.ajax({
										type     : "post",
										url      : "rkt-perkerasan-jalan/save",
										data     : $("#form_init").serialize(),
										cache    : false,
										dataType : 'json',
										success  : function(data) {
											if (data.return == "locked") {
												alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
											}else if (data.return == "empty") {
												alert("Anda tidak dapat melakukan perhitungan data karena ada VRA Wajib yang tidak ada di norma PK Jalan.");
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
	
	//untuk proses simpan draft
	$("#btn_save_temp").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var regioncode = $("#src_region").val();
		var budgetperiod = $("#budgetperiod").val();
		var bacode = $("#key_find").val();
		var coa_code = $("#src_coa_code").val();
		var coa = $("#src_coa").val();
	
		if( bacode == '' || regioncode == '' || coa == ''){
			alert('Anda Harus Memilih Region, Business Area, dan Group Perkerasan Jalan Terlebih Dahulu.');
		}
		else{
			$.ajax({
				type     : "post",
				url      : "rkt-perkerasan-jalan/save-temp",
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
	
	
	$("#btn_export_csv").live("click", function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var src_coa_code = $("#src_coa_code").val();		//MATURITY STAGE
		var src_afd = $("#src_afd").val();					//SEARCH AFD
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-perkerasan-jalan/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_coa_code/" + src_coa_code + "/src_afd/" + src_afd ,'_blank');
		}
    });
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-perkerasan-jalan/upd-locked-seq-status",
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
				url      : "rkt-perkerasan-jalan/upd-locked-seq-status",
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
function validateReq(){	
	var result = true;
	$("[id^=text18_]").each(function(key, row) {
		var mystring = this.value;
		var value = mystring.split(',').join('');
		if (key > 0){
			var row = $(this).attr("id").split("_")[1];
			
			max_laterit = $("#jarak_"+row).val().replace(",", "");
			max_jalan = $("#text17_"+row).val().replace(",", "");
			max_diperkeras = $("#text18_"+row).val().replace(",", "");
			valmonth = Number($("#text18_"+row).val().replace(",", ""))+
						Number($("#text19_"+row).val().replace(",", ""))+
						Number($("#text20_"+row).val().replace(",", ""))+
						Number($("#text21_"+row).val().replace(",", ""))+
						Number($("#text22_"+row).val().replace(",", ""))+
						Number($("#text23_"+row).val().replace(",", ""))+
						Number($("#text24_"+row).val().replace(",", ""))+
						Number($("#text25_"+row).val().replace(",", ""))+
						Number($("#text26_"+row).val().replace(",", ""))+
						Number($("#text27_"+row).val().replace(",", ""))+
						Number($("#text28_"+row).val().replace(",", ""))+
						Number($("#text29_"+row).val().replace(",", ""))+
						Number($("#text30_"+row).val().replace(",", ""));
			
			if ((Number(max_jalan) == 0 && valmonth > 0) || (Number(max_jalan) == '' && valmonth > 0)){
				$("#text17_"+row).addClass("error");
				result = false;
			}else if ((Number(max_laterit) == 0 && valmonth > 0) || (Number(max_laterit) == '' && valmonth > 0)){
				$("#jarak_"+row).addClass("error");
				result = false;
			}else if (!max_diperkeras && valmonth > 0){
				$("#text18_"+row).addClass("error");
				result = false;
			}else{
				$(this).removeClass("error");
			}
		}
	});
	
	return result;
}

//validasi text field yang harus diisi
function validate(){	
	var result = true;
	$("[id^=text18_]").each(function(key, row) {
		var mystring = this.value;
		var value = mystring.split(',').join('');
		if (key > 0){
			var row = $(this).attr("id").split("_")[1];
			if (parseFloat($("#text17_" + row).val()) < parseFloat($("#text18_" + row).val())){
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

    //
    $.ajax({
        type    : "post",
        url     : "rkt-perkerasan-jalan/list",
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
					 
					$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val(row.TRX_RKT_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");

					//mewarnai jika row nya berasal dari temporary table
					if (row.FLAG_TEMP) {cekTempData(index)	;} 
					
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text52_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
					$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.LAND_TYPE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.TOPOGRAPHY);
					$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(row.TAHUN_TANAM);
					$("#data_freeze tr:eq(" + index + ") input[id^=bulan_]").val(row.TAHUN_TANAM_M);
					$("#data_freeze tr:eq(" + index + ") input[id^=tahun_]").val(row.TAHUN_TANAM_Y);
					$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val(row.SEMESTER1);
					$("#data_freeze tr:eq(" + index + ") input[id^=text10_]").val(row.SEMESTER2);
					$("#data_freeze tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.HA_PLANTED, 2));
					$("#data_freeze tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data_freeze tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.POKOK_TANAM, 0));
					$("#data_freeze tr:eq(" + index + ") input[id^=text12_]").addClass("integer");
					$("#data_freeze tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.SPH, 0));
					$("#data_freeze tr:eq(" + index + ") input[id^=text13_]").addClass("integer");
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
					$("#text49_" + index + " option[value='"+row.TIPE_NORMA+"']").attr("selected", "selected"); //<!-- TIPE NORMA -->
					$("#data tr:eq(" + index + ") input[id^=text50_]").val(accounting.formatNumber(row.ROTASI_SMS1, 0));
					$("#data tr:eq(" + index + ") input[id^=text51_]").val(accounting.formatNumber(row.ROTASI_SMS2, 0));
						
					$("#text14_" + index + " option[value='"+row.SUMBER_BIAYA+"']").attr("selected", "selected");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(row.JENIS_PEKERJAAN);
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(row.JARAK);
					$("#data tr:eq(" + index + ") input[id^=jarak_]").val(row.RANGE_JARAK);
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(row.AKTUAL_JALAN);
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(row.AKTUAL_PERKERASAN_JALAN);
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.PLAN_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.PLAN_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.PLAN_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.PLAN_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.PLAN_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.PLAN_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.PLAN_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.PLAN_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.PLAN_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.PLAN_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.PLAN_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number required distribusi_kerja");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.PRICE_QTY, 2));
					$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.COST_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.COST_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.COST_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.COST_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.COST_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.COST_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.COST_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text39_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.COST_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text40_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.COST_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text41_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.COST_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text42_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.COST_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text43_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.COST_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text44_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.COST_SETAHUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text45_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();

					if ((row.SEMESTER1 != 'TM') && (row.SEMESTER2 != 'TM')) {
						$("#text15_" + index).attr('readonly', 'readonly');
					}
                });
            }else{
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
        url     : "rkt-perkerasan-jalan/list-info-vra",
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
