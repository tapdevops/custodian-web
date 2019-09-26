<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan Perencanaan Produksi
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/07/2013
Update Terakhir		:	04/05/2015
Revisi				:	
	YIR 19/06/2014	: 	- perubahan LoV menjadi combo box untuk pilihan region & maturity status
	SID 07/07/2014	: 	- penambahan info untuk lock table pada tombol cari, simpan
	NBU 04/05/2015	: 	- penambahan field Jarak PKS dan %Langsir
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
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;"  class='filter'/>
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
			<legend>PERENCANAAN PRODUKSI</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="80%">
						<input type="button" name="btn_template" id="btn_template" value="DOWNLOAD TEMPLATE" class="button" />
						<input type="button" name="btn_upload" id="btn_upload" value="UPLOAD" class="button" />
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="20%" align="right">
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button"/>
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
					<th>JARAK KE<BR>PKS [KM] SEKALI JALAN</th>
					<th>%<BR>LANGSIR</th>
					
					<th>HEKTAR PANEN<BR><span class='period_before'></span><BR>HA PANEN</th>
					<th>HEKTAR PANEN<BR><span class='period_before'></span><BR>POKOK PRODUKTIF</th>
					<th>HEKTAR PANEN<BR><span class='period_before'></span><BR>SPH PRODUKTIF</th>
					<th>PANEN JAN s/d JUN<BR><span class='period_before'></span><BR>TON AKTUAL</th>
					<th>PANEN JAN s/d JUN<BR><span class='period_before'></span><BR>JANJANG AKTUAL</th>
					<th>PANEN JAN s/d JUN<BR><span class='period_before'></span><BR>BJR AKTUAL</th>
					<th>PANEN JAN s/d JUN<BR><span class='period_before'></span><BR>YPH AKTUAL</th>
					<th>TAKSASI JUL s/d DES<BR><span class='period_before'></span><BR>TON TAKSASI</th>
					<th>TAKSASI JUL s/d DES<BR><span class='period_before'></span><BR>JANJANG TAKSASI</th>
					<th>TAKSASI JUL s/d DES<BR><span class='period_before'></span><BR>BJR TAKSASI</th>
					<th>TAKSASI JUL s/d DES<BR><span class='period_before'></span><BR>YPH TAKSASI</th>
					<th>ANTISIPASI<BR><span class='period_before'></span><BR>TON ANTISIPASI</th>
					<th>ANTISIPASI<BR><span class='period_before'></span><BR>JANJANG ANTISIPASI</th>
					<th>ANTISIPASI<BR><span class='period_before'></span><BR>BJR ANTISIPASI</th>
					<th>ANTISIPASI<BR><span class='period_before'></span><BR>YPH ANTISIPASI</th>
					<th>BUDGET <span class='period_before'></span><BR>TON</th>
					<th>BUDGET <span class='period_before'></span><BR>YPH</th>
					<th>BUDGET <span class='period_before'></span><BR>VAR YPH (%)</th>
					<th>SEMESTER 1<BR><span class='period_budget'></span><BR>HA</th>
					<th>SEMESTER 1<BR><span class='period_budget'></span><BR>POKOK</th>
					<th>SEMESTER 1<BR><span class='period_budget'></span><BR>SPH</th>
					<th>SEMESTER 2<BR><span class='period_budget'></span><BR>HA</th>
					<th>SEMESTER 2<BR><span class='period_budget'></span><BR>POKOK</th>
					<th>SEMESTER 2<BR><span class='period_budget'></span><BR>SPH</th>
					<th>PROFILE YIELD<BR><span class='period_budget'></span><BR>YPH</th>
					<th>PROFILE YIELD<BR><span class='period_budget'></span><BR>TON</th>
					<th>PROFILE YIELD<BR>POTENTION <span class='period_budget'></span><BR>YPH</th>
					<th>PROFILE YIELD<BR>POTENTION <span class='period_budget'></span><BR>TON</th>
					<th>BUDGET YIELD<BR><span class='period_budget'></span><BR>JANJANG</th>
					<th>BUDGET YIELD<BR><span class='period_budget'></span><BR>BJR</th>
					<th>BUDGET YIELD<BR><span class='period_budget'></span><BR>TON</th>
					<th>BUDGET YIELD<BR><span class='period_budget'></span><BR>YPH</th>
					<th>JAN <span class='period_budget'></span></th>
					<th>FEB <span class='period_budget'></span></th>
					<th>MAR <span class='period_budget'></span></th>
					<th>APR <span class='period_budget'></span></th>
					<th>MEI <span class='period_budget'></span></th>
					<th>JUN <span class='period_budget'></span></th>
					<th>JUL <span class='period_budget'></span></th>
					<th>AGS <span class='period_budget'></span></th>
					<th>SEP <span class='period_budget'></span></th>
					<th>OKT <span class='period_budget'></span></th>
					<th>NOV <span class='period_budget'></span></th>
					<th>DES <span class='period_budget'></span></th>
					<th>SMS 1 <span class='period_budget'></span></th>
					<th>SMS 2 <span class='period_budget'></span></th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none" class='rowdata'>
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" value='5'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='75px'><input type="text" name="text55[]" id="text55_" readonly="readonly" /></td>
					<td width='50px'><input type="text" name="text52[]" id="text52_" /></td>
					<td width='50px'><input type="text" name="text53[]" id="text53_"/></td>
					
					<td width='120px'><input type="text" name="text06[]" id="text06_" value='6'/></td>
					<td width='120px'><input type="text" name="text07[]" id="text07_" value='7'/></td>
					<td width='120px'><input type="text" name="text08[]" id="text08_" value='8'/></td>
					<td width='120px'><input type="text" name="text09[]" id="text09_" value='9'/></td>
					<td width='120px'><input type="text" name="text10[]" id="text10_" value='10'/></td>
					<td width='120px'><input type="text" name="text11[]" id="text11_" value='11'/></td>
					<td width='120px'><input type="text" name="text12[]" id="text12_" value='12'/></td>
					<td width='120px'><input type="text" name="text13[]" id="text13_" value='13'/></td>
					<td width='120px'><input type="text" name="text14[]" id="text14_" value='14'/></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_" value='15'/></td>
					<td width='120px'><input type="text" name="text16[]" id="text16_" value='16'/></td>
					<td width='120px'><input type="text" name="text17[]" id="text17_" value='17'/></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" value='18'/></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" value='19'/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" value='20'/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" value='21'/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" value='22'/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" value='23'/></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" value='24'/></td>
					<td width='120px'><input type="text" name="text25[]" id="text25_" value='25'/></td>
					<td width='120px'><input type="text" name="text26[]" id="text26_" value='26'/></td>
					<td width='120px'><input type="text" name="text27[]" id="text27_" value='27'/></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" value='28'/></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" value='29'/></td>
					<td width='120px'><input type="text" name="text30[]" id="text30_" value='30'/></td>
					<td width='120px'><input type="text" name="text31[]" id="text31_" value='31'/></td>
					<td width='120px'><input type="text" name="text32[]" id="text32_" value='32'/></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" value='33'/></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" value='34'/></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" value='35'/></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" value='36'/></td>
					<td width='120px'><input type="text" name="text37[]" id="text37_" value='37'/></td>
					<td width='120px'><input type="text" name="text38[]" id="text38_" value='38'/></td>
					<td width='120px'><input type="text" name="text39[]" id="text39_" value='39'/></td>
					<td width='120px'><input type="text" name="text40[]" id="text40_" value='40'/></td>
					<td width='120px'><input type="text" name="text41[]" id="text41_" value='41'/></td>
					<td width='120px'><input type="text" name="text42[]" id="text42_" value='42'/></td>
					<td width='120px'><input type="text" name="text43[]" id="text43_" value='43'/></td>
					<td width='120px'><input type="text" name="text44[]" id="text44_" value='44'/></td>
					<td width='120px'><input type="text" name="text45[]" id="text45_" value='45'/></td>
					<td width='120px'><input type="text" name="text46[]" id="text46_" value='46'/></td>
					<td width='120px'><input type="text" name="text47[]" id="text47_" value='47'/></td>
					<td width='120px'><input type="text" name="text48[]" id="text48_" value='48'/></td>
					<td width='120px'><input type="text" name="text49[]" id="text49_" value='49'/></td>
					<td width='120px'><input type="text" name="text50[]" id="text50_" value='50'/></td>
					<td width='120px'><input type="text" name="text51[]" id="text51_" value='51'/></td>
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
	$("#btn_lock").hide();
	$("#btn_unlock").hide();
	$("#btn_save_temp").hide();
	//set nama kolom yang mengandung tahun
	$(".period_budget").html($("#budgetperiod").val());
	$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
	
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       970,   // required
		height:      400,   // required
		numFrozen:   6,     // optional
		frozenWidth: 245,   // optional
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
		var src_coa_code = $("#src_coa_code").val();		//SEARCH KODE COA
		var coa = $("#src_coa").val();						//SEARCH DESKRIPSI COA
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			/*page_num = (page_num) ? page_num : 1;
			getData();*/
			
			//set nama kolom yang mengandung tahun
			$(".period_budget").html($("#budgetperiod").val());
			$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
			
			//cek status periode
			//alert('TAP');
			$.ajax({
				type    : "post",
				url     : "perencanaan-produksi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "perencanaan-produksi/get-status-periode", //cek status periode
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
											url     : "perencanaan-produksi/check-locked-seq", //check apakah status lock sendiri apakah lock
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
	$("#btn_upload").live("click", function() {
		var controller = "upload/perencanaan-produksi";
		$("#controller").val(controller);
		popup("upload/main", "detail", 700, 400);
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
				url      : "perencanaan-produksi/save-temp",
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
			if(validate() != false){
				$.ajax({
					type    : "post",
					url     : "perencanaan-produksi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
					data    : $("#form_init").serialize(),
					cache   : false,
					dataType: "json",
					success : function(data) {
						if(data==1){
							//cek status sequence current norma/rkt
							$.ajax({
								type    : "post",
								url     : "perencanaan-produksi/check-locked-seq",
								data    : $("#form_init").serialize(),
								cache   : false,
								dataType: "json",
								success : function(data) {
									if(data.STATUS == 'LOCKED'){ 
										alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
									}else{
										$.ajax({
											type     : "post",
											url      : "perencanaan-produksi/save",
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
			}else{
				alert("Field yang Berwarna Merah, harus lebih dari 0.");
			}
		}
    });
	$("#btn_template").live("click", function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		var src_afd = $("#src_afd").val();					//SEARCH AFD
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			window.open("<?=$_SERVER['PHP_SELF']?>/download/template-perencanaan-produksi/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_afd/" + src_afd + "/search/" + encode64(search),'_blank');
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
		var src_afd = $("#src_afd").val();					//SEARCH AFD
	
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-perencanaan-produksi/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_afd/" + src_afd + "/search/" + encode64(search),'_blank');
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
				url      : "perencanaan-produksi/upd-locked-seq-status",
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
				url      : "perencanaan-produksi/upd-locked-seq-status",
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
	
	$('input[id^=text52_]').each(function(key, row) {
		if (key > 0){
			var mystring = this.value;
			var value = mystring.split(',').join('');
			if (parseFloat(value) <= '0') {
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
        url     : "perencanaan-produksi/list",
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
						
						//left freeze panes row
						$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text55_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
						$("#data_freeze tr:eq(" + index + ") input[id^=text52_]").val(accounting.formatNumber(row.JARAK_PKS, 0));
						$("#data_freeze tr:eq(" + index + ") input[id^=text52_]").addClass("integer");
						$("#data_freeze tr:eq(" + index + ") input[id^=text53_]").val(accounting.formatNumber(row.PERSEN_LANGSIR, 2));
						$("#data_freeze tr:eq(" + index + ") input[id^=text53_]").addClass("number");
						$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
						
						//right freeze panes
						var tr = $("#data tr:eq(0)").clone();
						$("#data").append(tr);
						var index = ($("#data tr").length - 1);					
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});					
						if (row.FLAG_TEMP) {cekTempData(index);} 
						//right freeze panes row
						$("#data tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.HA_PANEN, 2));
						$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.POKOK_PRODUKTIF, 0));
						$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.SPH_PRODUKTIF, 0));
						$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.TON_AKTUAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.JANJANG_AKTUAL, 0));
						$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.BJR_AKTUAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.YPH_AKTUAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.TON_TAKSASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.JANJANG_TAKSASI, 0));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.BJR_TAKSASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.YPH_TAKSASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.TON_ANTISIPASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.JANJANG_ANTISIPASI, 0));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.BJR_ANTISIPASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.YPH_ANTISIPASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.TON_BUDGET_TAHUN_BERJALAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.YPH_BUDGET_TAHUN_BERJALAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.VAR_YPH, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.HA_SMS1, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.POKOK_SMS1, 0));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.SPH_SMS1, 0));
						$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.HA_SMS2, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.POKOK_SMS2, 0));
						$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.SPH_SMS2, 0));
						$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.YPH_PROFILE, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.TON_PROFILE, 2));
						$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.YPH_PROPORTION, 2));
						$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.TON_PROPORTION, 2));
						$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.JANJANG_BUDGET, 0));
						$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("requirednotzero integer");
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.BJR_BUDGET, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.TON_BUDGET, 4));
						$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.YPH_BUDGET, 2));
						$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.JAN, 4));
						$("#data tr:eq(" + index + ") input[id^=text38_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.FEB, 4));
						$("#data tr:eq(" + index + ") input[id^=text39_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.MAR, 4));
						$("#data tr:eq(" + index + ") input[id^=text40_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.APR, 4));
						$("#data tr:eq(" + index + ") input[id^=text41_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.MAY, 4));
						$("#data tr:eq(" + index + ") input[id^=text42_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.JUN, 4));
						$("#data tr:eq(" + index + ") input[id^=text43_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.JUL, 4));
						$("#data tr:eq(" + index + ") input[id^=text44_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.AUG, 4));
						$("#data tr:eq(" + index + ") input[id^=text45_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(row.SEP, 4));
						$("#data tr:eq(" + index + ") input[id^=text46_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(row.OCT, 4));
						$("#data tr:eq(" + index + ") input[id^=text47_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text48_]").val(accounting.formatNumber(row.NOV, 4));
						$("#data tr:eq(" + index + ") input[id^=text48_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text49_]").val(accounting.formatNumber(row.DEC, 4));
						$("#data tr:eq(" + index + ") input[id^=text49_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text50_]").val(accounting.formatNumber(row.SMS1, 4));
						$("#data tr:eq(" + index + ") input[id^=text50_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ") input[id^=text51_]").val(accounting.formatNumber(row.SMS2, 4));
						$("#data tr:eq(" + index + ") input[id^=text51_]").addClass("requirednotzero number");
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}
			}
        }
    });
}
</script>
