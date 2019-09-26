<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan RKT Panen
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	05/08/2013
Update Terakhir		:	05/05/2015
Revisi				:	 
	YIR 30/06/2014	: 	- fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
						- penambahan info untuk lock table pada tombol cari, simpan, hapus
	NBU 04/05/2015	: 	- Penambahan readonly pada field Jarak PKS dan %Langsir
	NBU 05/05/2015	: 	- penutupan button lock dan unlock di line 80 & 83
						- penutupan pengecekan lock pada diri sendiri di line 331
						- penutupan pengecekan lock untuk button save di line 434
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
            <input type="hidden" name="page_rows" id="page_rows" value="1000" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>RKT Panen</legend>
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
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="15%">% OER  Per BA  :</td>
					<td>
						<input type="text" name="OERvalue" id="OERvalue" value="" style="width:50px;" readonly="readonly"/>
					</td>
				</tr>
			</table>
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th> <!-- 1 -->
					<th>BUSINESS<BR>AREA</th> <!-- 2 -->
					<th>AFD</th> <!-- 3 -->
					<th>BLOK - DESC</th> <!-- 4 -->
					<th>BULAN TANAM</th> <!-- 5 -->
					<th>TAHUN TANAM</th> <!-- 6 -->
					<th>HA</th> <!-- 7 -->
					<th>POKOK</th> <!-- 8 -->
					<th>SPH</th> <!-- 9 -->
					<th>TOPOGRAFI</th> <!-- 10 -->
					<th>TIPE TANAH</th> <!-- 11 -->

					<th>TON</th> <!-- 12 -->
					<th>JJG</th> <!-- 13 -->
					<th>BJR [AFD]</th> <!-- 14 -->
					<th>JARAK KE<BR>PKS [KM] SEKALI JALAN</th> <!-- 15 -->
					<th>ANGKUT TBS<BR>ASAL UNIT</th> <!-- 16 -->
					<th>%<BR>LANGSIR</th> <!-- 17 -->

					<th>BIAYA PMANEN_RP<br>HK</th> <!-- 18 -->
					<th>BIAYA PMANEN_RP<br>BASIS</th> <!-- 19 -->
					<th>BIAYA PMANEN_RP<br>PREMI JANJANG</th> <!-- 20 -->
					<th>PREMI INSENTIF</th> <!-- 21 -->
					<th>BIAYA PMANEN_RP<br>PREMI BRD</th> <!-- 22 -->
					<th>BIAYA PMANEN_RP<br>TOTAL</th> <!-- 23 -->
					<th>BIAYA PMANEN_RP<br>RP/KG</th>
					<th>BIAYA SPV_RP<br>BASIS</th>
					<th>BIAYA SPV_RP<br>PREMI</th>
					<th>BIAYA SPV_RP<br>TOTAL</th>
					<th>BIAYA SPV_RP<br>RP/KG</th>
					<th>BIAYA ALAT PANEN<br>RP/KG</th> 
					<th>BIAYA ALAT PANEN<br>RP TOTAL</th>
					<th>TKG MUAT<br>BASIS</th>
					<th>TKG MUAT<br>PREMI</th>
					<th>TKG MUAT<br>TOTAL</th>
					<th>TKG MUAT<br>RP/KG</th>
					<th>SUPIR<br>PREMI</th>
					<th>SUPIR<br>RP/KG</th>
					<th>ANGKUT TBS<br>RP/KG/KM</th>
					<th>ANGKUT TBS<br>RP_ANGKUT</th>
					<th>ANGKUT TBS<br>RP/KG</th>
					<th>KR BUAH<br>BASIS</th>
					<th>KR BUAH<br>PREMI</th>
					<th>KR BUAH<br>TOTAL</th>
					<th>KR BUAH<br>RP/KG</th>
					<th>LANGSIR - TRACTOR<br>TON</th>
					<th>LANGSIR - TRACTOR<br>RP_LANGSIR</th>
					<th>LANGSIR - TRACTOR<br>TKG MUAT</th>
					<th>LANGSIR - TRACTOR<br>RP/KG</th>
					<th>DIS PANEN<br>JAN</th>
					<th>DIS PANEN<br>FEB</th>
					<th>DIS PANEN<br>MAR</th>
					<th>DIS PANEN<br>APR</th>
					<th>DIS PANEN<br>MEI</th>
					<th>DIS PANEN<br>JUN</th>
					<th>DIS PANEN<br>JUL</th>
					<th>DIS PANEN<br>AGS</th>
					<th>DIS PANEN<br>SEP</th>
					<th>DIS PANEN<br>OKT</th>
					<th>DIS PANEN<br>NOV</th>
					<th>DIS PANEN<br>DES</th>
					<th>COST SETAHUN</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<!--left freeze panes-->
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" />
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly"/>
					</td>
					<td width='50px' ><input type="text" name="text03[]" id="text03_" readonly="readonly" /></td>
					<td width='50px' ><input type="text" name="text04[]" id="text04_" readonly="readonly" /></td>
					<td width='75px' ><input type="text" name="text55[]" id="text55_" readonly="readonly"/></td>

					<td><input type="text" name="bulan_tanam_[]" id="bulan_tanam_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="tahun_tanam_[]" id="tahun_tanam_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="ha_[]" id="ha_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="pokok_[]" id="pokok_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="sph_[]" id="sph_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="topografi_[]" id="topografi_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->
					<td><input type="text" name="tipe_tanah_[]" id="tipe_tanah_" readonly="readonly"/></td> <!-- yaddi.surahman@tap-agri.co.id-->

					<td width='50px' ><input type="text" name="text06[]" id="text06_" readonly="readonly" /></td>
					<td width='50px' ><input type="text" name="text07[]" id="text07_" readonly="readonly" /></td>
					<td width='50px'><input type="text" name="text08[]" id="text08_" readonly="readonly" /></td>
					<td width='50px'><input type="text" name="text09[]" id="text09_" readonly="readonly" /></td>
					<td width='150px'><select name="text10[]" id="text10_"> </select></td>
					<td width='50px'><input type="text" name="text11[]" id="text11_" readonly="readonly"/></td>
					
					<!--right freeze panes-->
					<td width='120px'><input type="text" name="text12[]" id="text12_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text13[]" id="text13_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text14[]" id="text14_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="incentive[]" id="incentive_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text53[]" id="text53_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text16[]" id="text16_" readonly="readonly" class='number'  /></td>
					<td width='120px'><input type="text" name="text17[]" id="text17_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text25[]" id="text25_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text26[]" id="text26_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text27[]" id="text27_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text30[]" id="text30_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text31[]" id="text31_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text32[]" id="text32_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text37[]" id="text37_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text54[]" id="text54_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text38[]" id="text38_" readonly="readonly" class='number' /></td>
					
					<td width='120px'><input type="text" name="text40[]" id="text40_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text41[]" id="text41_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text42[]" id="text42_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text43[]" id="text43_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text44[]" id="text44_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text45[]" id="text45_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text46[]" id="text46_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text47[]" id="text47_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text48[]" id="text48_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text49[]" id="text49_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text50[]" id="text50_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text51[]" id="text51_" readonly="readonly" class='number' /></td>
					<td width='120px'><input type="text" name="text52[]" id="text52_" readonly="readonly" class='number' /></td>
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
	//$("#btn_lock").hide();
	//$("#btn_unlock").hide();
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       980,   // required
		height:      400,   // required
		numFrozen:   17,     // optional
		frozenWidth: 470//,   // optional
		//clearWidths: true,  // optional
	});//freezeTableColumns
	
	$.ajax({
		type     : "post",
		url      : "rkt-panen/get-sumber-biaya",
		data     : $("#form_init").serialize(),
		cache    : false,
		dataType: "json",
		success  : function(data) {
			count = data.count;
			if (count > 0) {
				$.each(data.rows, function(key, row) {
					$('#text10_').append(new Option(row.PARAMETER_VALUE,row.PARAMETER_VALUE_CODE));
				});
			}else{
				alert("SUMBER BIAYA TIDAK DITEMUKAN");
			}
		}
	});
	 	 
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
			$.ajax({
				type    : "post",
				url     : "rkt-panen/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "rkt-panen/get-status-periode", //cek status periode
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
											url     : "rkt-panen/check-locked-seq", //check apakah status lock sendiri apakah lock
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
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	
	//untuk proses sinpan draft
	$("#btn_save_temp").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		//var search = $("#search").val();					//SEARCH FREE TEXT
		
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "rkt-panen/save-temp",
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
			if (validateInput() != false){
				$.ajax({
				type    : "post",
				url     : "rkt-panen/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-panen/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									$.ajax({
										type     : "post",
										url      : "rkt-panen/save",
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
			} else {
				alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
			}
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
		var src_afd = $("#src_afd").val();					//SEARCH FREE TEXT
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-panen/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search) + "/src_afd/" + src_afd,'_blank');
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
	
	//PICK AFD PANEN
	$("#pick_afd").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/afdeling/bacode/" + bacode, "pick", 700, 400 );
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
	
	$("#data_freeze tr select").live("change", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        if($(this).val() == 'EXTERNAL') {
					$("#data").find("tr:eq("+index+") input#text31_"+index+"").removeAttr('readonly');
        } else {
					$("#data").find("tr:eq("+index+") input#text31_"+index+"").attr('readonly', true);
        }
    });


	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-panen/upd-locked-seq-status",
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
				url      : "rkt-panen/upd-locked-seq-status",
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
	
function setDefaultField(index){
	//DEKLARASI VARIABEL
	var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
	var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
	var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
	var src_region_code = $("#src_region_code").val();	//KODE REGION
	var key_find = $("#key_find").val();				//KODE BA
	var region = $("#src_region").val();				//DESKRIPSI REGION
	var ba_code = $("#src_ba").val();					//DESKRIPSI BA
	var search = $("#search").val();					//SEARCH FREE TEXT
	
	//left freeze panes
	$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
	$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(key_find);
	$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("required");
	$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val($("#data_freeze tr:eq(" + (index-1) + ") input[id^=text06_]").val());
	$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val($("#data_freeze tr:eq(" + (index-1) + ") input[id^=text07_]").val());
	$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("required");
	$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
	
	//right freeze panes
	$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text53_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number required");
	$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text54_]").addClass("number");
	$("#data tr:eq(" + index + ")").removeAttr("style");
	$("#data tr:eq(" + index + ") input[id^=text06_]").focus();
}

function getData(){
    $("#page_num").val(page_num);
    $.ajax({
        type    : "post",
        url     : "rkt-panen/list",
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
						if (row.ROW_ID_TEMP) {cekTempData(index);}
						
						$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						
						//tambah rowid temp 
						$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val(row.TRX_RKT_CODE);
						
						//left freeze panes row
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						
						if(row.PERIOD_BUDGET!=null){
							$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
							$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
							$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
							$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
							$("#data_freeze tr:eq(" + index + ") input[id^=text55_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
						}else{
							$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGETHS);
							$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODEHS);
							$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODEHS);
							$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODEHS);
							$("#data_freeze tr:eq(" + index + ") input[id^=text55_]").val(row.BLOCK_CODEHS + " - " + row.BLOCK_DESCHS);
						}
						
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.TON, 2));
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.JANJANG, 2));
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.BJR_AFD, 4));
						$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.JARAK_PKS, 0));
						$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").addClass("integer");
						$("#text10_" + index + " option[value='"+row.SUMBER_BIAYA+"']").attr("selected", "selected");
						$("#data_freeze tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.PERSEN_LANGSIR, 2));
						$("#data_freeze tr:eq(" + index + ") input[id^=text11_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ")").removeAttr("style");

						$("#data_freeze tr:eq(" + index + ") input[id^=bulan_tanam_]").val(row.BULAN_TANAM);
						$("#data_freeze tr:eq(" + index + ") input[id^=tahun_tanam_]").val(row.TAHUN_TANAM);
						$("#data_freeze tr:eq(" + index + ") input[id^=ha_]").val(accounting.formatNumber(row.HA_PLANTED,2));
						$("#data_freeze tr:eq(" + index + ") input[id^=ha_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=pokok_]").val(row.POKOK_TANAM);
						$("#data_freeze tr:eq(" + index + ") input[id^=pokok_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=sph_]").val(row.SPH);
						$("#data_freeze tr:eq(" + index + ") input[id^=sph_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ") input[id^=topografi_]").val(row.TOPOGRAPHY);
						$("#data_freeze tr:eq(" + index + ") input[id^=tipe_tanah_]").val(row.LAND_TYPE);

						//right freeze panes
						var tr = $("#data tr:eq(0)").clone();
						$("#data").append(tr);
						var index = ($("#data tr").length - 1);					
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});
						
						//mewarnai jika row nya berasal dari temporary table
						if (row.ROW_ID_TEMP) {cekTempData(index);}
						
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_HK, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_RP_BASIS, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_RP_PREMI_JANJANG, 2));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=incentive_]").val(accounting.formatNumber(row.INCENTIVE, 2));
						$("#data tr:eq(" + index + ") input[id^=incentive_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text53_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_RP_PREMI_BRD, 2));
						$("#data tr:eq(" + index + ") input[id^=text53_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_RP_TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.BIAYA_PEMANEN_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.BIAYA_SPV_RP_BASIS, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.BIAYA_SPV_RP_PREMI, 2));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.BIAYA_SPV_RP_TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.BIAYA_SPV_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.BIAYA_ALAT_PANEN_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.BIAYA_ALAT_PANEN_RP_TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.TUKANG_MUAT_BASIS, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.TUKANG_MUAT_PREMI, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.TUKANG_MUAT_TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.TUKANG_MUAT_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.SUPIR_PREMI, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.SUPIR_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.ANGKUT_TBS_RP_KG_KM, 2));
						$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.ANGKUT_TBS_RP_ANGKUT, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.ANGKUT_TBS_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.KRANI_BUAH_BASIS, 2));
						$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.KRANI_BUAH_PREMI, 2));
						$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.KRANI_BUAH_TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.KRANI_BUAH_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.LANGSIR_TON, 2));
						$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.LANGSIR_RP, 2));
						$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.LANGSIR_RP_KG, 2));
						$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text54_]").val(accounting.formatNumber(row.LANGSIR_TUKANG_MUAT, 2)); //TUKANG MUAT
						$("#data tr:eq(" + index + ") input[id^=text54_]").addClass("number");
						
						$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.COST_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text40_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.COST_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text41_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.COST_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text42_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.COST_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text43_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.COST_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text44_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.COST_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text45_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(row.COST_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text46_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(row.COST_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text47_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text48_]").val(accounting.formatNumber(row.COST_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text48_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text49_]").val(accounting.formatNumber(row.COST_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text49_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text50_]").val(accounting.formatNumber(row.COST_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text50_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text51_]").val(accounting.formatNumber(row.COST_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text51_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text52_]").val(accounting.formatNumber(row.COST_SETAHUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text52_]").addClass("number");

						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#OERvalue").val(accounting.formatNumber(row.OER_BA, 2));
						$("#OERvalue").addClass("number");
						$("#data tr:eq(1) input[id^=text06_]").focus();
					});
				}else{
					$("#tfoot").hide();
				}
			}
        }
		
    });
}

</script>
