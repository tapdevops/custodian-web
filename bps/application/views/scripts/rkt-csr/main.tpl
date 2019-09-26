<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan RKT CSR
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	15/07/2013
Update Terakhir		:	26/06/2014
Revisi				:
	YIR 19/06/2014	: 	- perubahan LoV menjadi combo box untuk pilihan region
						- freeze panes
	SID 26/06/2014	: 	- penambahan fungsi save temp ketika ada perubahan data & pindah ke next page
						- penambahan info untuk lock table pada tombol cari, simpan, hapus
	NBU 05/05/2015	: 	- penutupan button lock dan unlock di line 95 - 98
						- penutupan pengecekan lock pada diri sendiri di line 276
						- penutupan pengecekan lock untuk button save di line 444
						- penutupan pengecekan lock untuk button delete di line 370
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
					<td>COA :</td>
					<td>
						<input type="hidden" name="src_coa_code" id="src_coa_code" value="" style="width:200px;" />
						<input type="hidden" name="src_coa" id="src_coa" value="" style="width:200px;"/>
						<?php echo $this->setElement($this->input['src_coa_code_pre']);?>
					</td>
				</tr>
				<tr>
					<td>GRUP :</td>
					<td>
						<input type="hidden" name="src_group_code" id="src_group_code" value="" style="width:200px;"  readonly="readonly"/>
						<input type="text" name="src_group_desc" id="src_group_desc" value="" style="width:200px;"  readonly="readonly"/>
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
            <input type="hidden" name="page_rows" id="page_rows" value="50" />
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>RKT CSR</legend>
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
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th style='color:#999'>+</th>
					<th style='color:#999'>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE COA</th>
					<th>DESKRIPSI COA</th>
					<th>GRUP</th>
					<th>SUB GRUP</th>
					<th>ACTIVITY DETAIL</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>TOTAL BIAYA</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>JAN</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>FEB</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>MAR</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>APR</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>MAY</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>JUN</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>JUL</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>AUG</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>SEP</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>OCT</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>NOV</th>
					<th>DISTRIBUSI BIAYA <span class='period_budget'></span><BR>DEC</th>
					<th>KETERANGAN</th>
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
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly"/></td>
					<td width='100px'><input type="text" name="text04[]" id="text04_" readonly="readonly"/></td>
					<td width='200px'><input type="text" name="text05[]" id="text05_" readonly="readonly"/></td>
					<td width='200px'>
						<input type="hidden" name="text06[]" id="text06_" readonly="readonly"/>
						<input type="text" name="text07[]" id="text07_" readonly="readonly"/>
					</td>
					<td width='200px'>
						<input type="hidden" name="text08[]" id="text08_" readonly="readonly"/>
						<input type="hidden" name="text008[]" id="text008_" readonly="readonly"/>
						<input type="text" name="text09[]" id="text09_" title="Tekan F9 Untuk Memilih."/>
					</td>
					<td width='300px'>
						<input type="hidden" name="text010[]" id="text010_"/>
						<input type="text" name="text10[]" id="text10_"/>
					</td>
					<td width='120px'><input type="text" name="text11[]" id="text11_" readonly="readonly"/></td>
					<td width='120px'><input type="text" name="text12[]" id="text12_"/></td>
					<td width='120px'><input type="text" name="text13[]" id="text13_"/></td>
					<td width='120px'><input type="text" name="text14[]" id="text14_"/></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_"/></td>
					<td width='120px'><input type="text" name="text16[]" id="text16_"/></td>
					<td width='120px'><input type="text" name="text17[]" id="text17_"/></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_"/></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_"/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_"/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_"/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_"/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_"/></td>
					<td width='300px'><input type="text" name="text24[]" id="text24_"/></td>
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
//	$("#btn_lock").hide();
//	$("#btn_unlock").hide();
	
	//set nama kolom yang mengandung tahun
	$(".period_budget").html($("#budgetperiod").val());
	$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
	
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       970,   // required
		height:      400,   // required
		numFrozen:   9,     // optional
		frozenWidth: 470,   // optional
		clearWidths: false,  // optional
	});//freezeTableColumns
	 	 
    //BUTTON ACTION	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_coa_code = $("#src_coa_code").val();
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_coa_code == '' )  {
			alert("Anda Harus Memilih COA Terlebih Dahulu.");
		} else {
			//page_num = (page_num) ? page_num : 1;
			
			//set nama kolom yang mengandung tahun
			$(".period_budget").html($("#budgetperiod").val());
			$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);

			$.ajax({
				type    : "post",
				url     : "rkt-csr/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "rkt-csr/get-status-periode", //cek status periode
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
											url     : "rkt-csr/check-locked-seq", //check apakah status lock sendiri apakah lock
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
	
	$("#btn_add").live("click", function(event) {
		var budgetperiod = $("#budgetperiod").val();
		var regioncode = $("#src_region").val();
		var bacode = $("#key_find").val();
		var coa_code = $("#src_coa_code").val();
		var coa = $("#src_coa").val();
	
		if( bacode == '' || regioncode == '' || coa == ''){
			alert('Anda Harus Memilih Region, Business Area, dan COA Terlebih Dahulu.');
		}
		else{
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
		}
    });
    $("input[id^=btn00_]").live("click", function(event) {
		$("#btn_add").trigger("click");
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "rkt-csr/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					/*$.ajax({
						type    : "post",
						url     : "rkt-csr/check-locked-seq",
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
											url      : "rkt-csr/delete",
											data     : { 
														 ROW_ID: $("#text00_" + row).val(), 
														 PERIOD_BUDGET: $("#text02_" + row).val(),
														 BA_CODE: $("#text03_" + row).val(),
														 COA_CODE: $("#text04_" + row).val(),
														 GROUP_CODE: $("#text06_" + row).val(),
														 SUB_GROUP_CODE: $("#text08_" + row).val(),
														 ACTIVITY_DETAIL: $("#text10_" + row).val(),
														 OLD_SUB_GROUP_CODE: $("#text008_" + row).val(),
														 OLD_ACTIVITY_DETAIL: $("#text010_" + row).val()
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
		});
    });
	$("#btn_save").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else if (validateInput() == false){
			alert("Field yang berwarna merah harus diisi terlebih dahulu.");
		} else {
			$.ajax({
				type    : "post",
				url     : "rkt-csr/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-csr/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
										$.ajax({
											type     : "post",
											url      : "rkt-csr/save",
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
		var src_coa_code = $("#src_coa_code").val();		//SEARCH KODE COA
		var coa = $("#src_coa").val();						//SEARCH DESKRIPSI COA
	
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "rkt-csr/save-temp",
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
		var src_coa_code = $("#src_coa_code").val();		//SEARCH KODE COA
		var coa = $("#src_coa").val();						//SEARCH DESKRIPSI COA
		var src_group_code = $("#src_group_code").val();	//SEARCH GRUP COA
	
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-csr/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_coa_code/" + src_coa_code + "/src_group_code/" + src_group_code +  "/search/" + encode64(search),'_blank');
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
	
	//PICK COA
	$("#pick_coa").click(function() {
		popup("pick/coa-relation/type/csr", "pick", 700, 400 );
    });
	$("#src_coa").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/coa-relation/type/csr", "pick", 700, 400 );
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
	
	//LOV UNTUK INPUTAN
	$("input[id^=text09_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var bacode = $("#key_find").val();
		var coa = $("#src_coa_code").val();
		var group = $("#text06_" + row).val();
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/sub-group-relation/module/rktCsr/row/" + row + "/type/csr/coa/" + coa + "/group/" + group, "pick");
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
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-csr/upd-locked-seq-status",
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
				url      : "rkt-csr/upd-locked-seq-status",
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
	var src_coa_code = $("#src_coa_code").val();		//SEARCH KODE COA
	var coa = $("#src_coa").val();						//SEARCH DESKRIPSI COA
	var src_group_code = $("#src_group_code").val();	//SEARCH GROUP CODE
	var src_group_desc = $("#src_group_desc").val();	//SEARCH GROUP DESC
	var trxCode = genTransactionCode(budgetperiod, key_find, 'CSR');
	
	//left freeze panes
	$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=rowidtemp_]").val(trxCode);
	$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
	$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(key_find);
	$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(src_coa_code);
	$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(coa);
	$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(src_group_code);
	$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(src_group_desc);
	$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").addClass("required");
	$("#data_freeze tr:eq(" + index + ") input[id^=text10_]").val("");
	$("#data_freeze tr:eq(" + index + ") input[id^=text10_]").addClass("required");
	$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
	
	//right freeze panes
	$("#data tr:eq(" + index + ") input[id^=text11_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text12_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text13_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text14_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text15_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text16_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text17_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text18_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text19_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text20_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text21_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text22_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text23_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text24_]").val("");
	$("#data tr:eq(" + index + ")").removeAttr("style");
	$("#data tr:eq(" + index + ") input[id^=text06_]").focus();
}

function getData(){
    $("#page_num").val(page_num);
    //
    $.ajax({
        type    : "post",
        url     : "rkt-csr/list",
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
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						
						//tambah rowid temp
						$("#data_freeze tr:eq(" + index + ") input[id^=trxrktcode_]").val(row.TRX_RKT_CODE);
						
						//left freeze panes row
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.COA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.COA_DESC);
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.GROUP_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.GROUP_DESC);
						$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(row.SUB_GROUP_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text008_]").val(row.SUB_GROUP_CODE); //old data
						$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").val(row.SUB_GROUP_DESC);
						$("#data_freeze tr:eq(" + index + ") input[id^=text09_]").addClass("required");
						$("#data_freeze tr:eq(" + index + ") input[id^=text10_]").val(row.ACTIVITY_DETAIL);
						$("#data_freeze tr:eq(" + index + ") input[id^=text010_]").val(row.ACTIVITY_DETAIL); //old data
						$("#data_freeze tr:eq(" + index + ") input[id^=text10_]").addClass("required");
						$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
					
						//right freeze panes
						var tr = $("#data tr:eq(0)").clone();
						$("#data").append(tr);
						var index = ($("#data tr").length - 1);					
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});
						
						//mewarnai jika row nya berasal dari temporary table
						if (row.FLAG_TEMP) {cekTempData(index)	;}					
						
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.TOTAL_BIAYA, 2));
						$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.DIS_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.DIS_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.DIS_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.DIS_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.DIS_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.DIS_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.DIS_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.DIS_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.DIS_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.DIS_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.DIS_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.DIS_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(row.KETERANGAN);
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text02_]").focus();
					});
				}
			}
        }
    });
}
</script>
