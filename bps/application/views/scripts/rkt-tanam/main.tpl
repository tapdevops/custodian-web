<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan RKT Perkerasan Jalan
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	29/07/2013
Update Terakhir		:	05/05/2015
Revisi				:	
- YULIUS 21/07/2014	:	- ubah ke FLAG_TEMP
						- modified TYPE NORMA
- NBU 05/05/2015	:	- penutupan button lock & unlock di line 87 - 90
						- penutupan pengecekan lock pada diri sendiri di line 370
						- penutupan pengecekan lock untuk button save di line 560
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
						<input type="hidden" name="src_coa_code" id="src_coa_code" value="" style="width:200px;" />
						<input type="text" name="src_coa" id="src_coa" value="" style="width:200px;" class='filter'/>
						<input type="hidden" name="activity_uom" id="activity_uom" value="" style="width:200px;" />
						<input type="button" name="pick_activity" id="pick_activity" value="...">
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
		</fieldset>
	</div>
	<br />
	<div>
		<fieldset>
			<legend>RKT TANAM</legend>
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
					<th>HA</th>
					<th>PKK</th>
					<th>SPH</th>
					<th>SUMBER BIAYA</th>
					<th>ACTIVITY CLASS</th>
					<th>NORMA TYPE</th>
					<th>PLAN JAN <span class='activity_uom'></span></th>
					<th>PLAN FEB <span class='activity_uom'></span></th>
					<th>PLAN MAR <span class='activity_uom'></span></th>
					<th>PLAN APR <span class='activity_uom'></span></th>
					<th>PLAN MAY <span class='activity_uom'></span></th>
					<th>PLAN JUN <span class='activity_uom'></span></th>
					<th>PLAN JUL <span class='activity_uom'></span></th>
					<th>PLAN AUG <span class='activity_uom'></span></th>
					<th>PLAN SEP <span class='activity_uom'></span></th>
					<th>PLAN OCT <span class='activity_uom'></span></th>
					<th>PLAN NOV <span class='activity_uom'></span></th>
					<th>PLAN DEC <span class='activity_uom'></span></th>
					
					<th>TOTAL PLAN <span class='activity_uom'></span></th>
					<th>RP / QTY</th>
					
					<th>DIS JAN</th>
					<th>DIS FEB</th>
					<th>DIS MAR</th>
					<th>DIS APR</th>
					<th>DIS MAY</th>
					<th>DIS JUN</th>
					<th>DIS JUL</th>
					<th>DIS AUG</th>
					<th>DIS SEP</th>
					<th>DIS OCT</th>
					<th>DIS NOV</th>
					<th>DIS DEC</th>
					<th>TOTAL COST</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;">
					<!--left freeze panes-->
					<td width='100px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="rowidtemp[]" id="rowidtemp_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" />
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" />
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text04[]" id="text04_" readonly="readonly" /></td>
					<td width='75px'><input type="text" name="text50[]" id="text50_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text06[]" id="text06_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text07[]" id="text07_" readonly="readonly" /></td>
					<td width='100px'>
					<input type="hidden" name="bulan[]" id="bulan_" readonly="readonly"/>
					<input type="hidden" name="tahun[]" id="tahun_" readonly="readonly"/>
					<input type="text" name="text08[]" id="text08_" readonly="readonly" />
					</td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text12[]" id="text12_" readonly="readonly" /></td>
					<td width='100px'><input type="text" name="text13[]" id="text13_" readonly="readonly" /></td>
					
					<!--right freeze panes-->
					<td width='100px'><input type="text" name="text14[]" id="text14_" readonly="readonly" /></td>
					
					<td width='100px'><select name="text15[]" id="text15_"></select></td>
					<td width='100px'>
						<select name="text61[]" id="text61_">
							<!--<option value=0>UMUM</option>
							<option value=1>KHUSUS</option>-->
						</select></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text25[]" id="text25_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text26[]" id="text26_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text27[]" id="text27_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text30[]" id="text30_" readonly="readonly" value=0 /></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" readonly="readonly" value=0 /></td>
					
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
					
					<td width='120px'>
						<input type="text" name="text45[]" id="text45_" readonly="readonly" value=0 />
						<input type="hidden" name="text46[]" id="text46_" readonly="readonly" value=0 />
						<input type="hidden" name="text47[]" id="text47_" readonly="readonly" value=0 />
					</td>
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
	//$("#btn_unlock").hide();
	//$("#btn_lock").hide();
    $("#search").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
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
						$('#src_coa_code').val(activitycode);
						$('#src_coa').val(row.DESCRIPTION);
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
		numFrozen:   10,    // optional
		frozenWidth: 470,   // optional
		clearWidths: false, // optional
	});//freezeTableColumns	
	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		var reference_role = "<?=$this->referencerole?>";
		var region = $("#src_region").val();
		var ba_code = $("#src_ba").val();
		var src_coa_code = $("#src_coa_code").val();
		var budgetperiod = $("#budgetperiod").val();
		var status = "<?=$this->_formula->get_StatusPeriode?>";
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_coa_code == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {	
			//uom distribusi qty
			$(".activity_uom").html( "- " + $("#activity_uom").val() );
			
			//<!-- TIPE NORMA -->
			//ambil jenis norma
			$.ajax({
				type     : "post",
				url      : "rkt-tanam/get-tipe-norma",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text61_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text61_').append(new Option(row.NILAI,row.NILAI));
						});
					}
				}
			});	
			//END TIPE NORMA
			
			$.ajax({
				type     : "post",
				url      : "rkt-tanam/get-activity-class",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					count = data.count;
					if (count > 0) {
						$('#text15_').find('option').remove().end();
						$.each(data.rows, function(key, row) {
							$('#text15_').append(new Option(row.NILAI,row.NILAI));
						});
						$.ajax({
							type    : "post",
							url     : "rkt-tanam/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
										url      : "rkt-tanam/get-status-periode", //cek status periode
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
														url     : "rkt-tanam/check-locked-seq", //check apakah status lock sendiri apakah lock
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
															}else{*/
																$("#btn_save").show();
																$("#btn_add").show();
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
								}
							}
						})
					}else{
						alert("Aktivitas Belum Terdapat Pada Norma Biaya Maupun Norma Harga Borong.");
						getData();
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
		popup("pick/activity-mapp/module/rktTanam", "pick", 700, 400 );
    });
	$("#src_coa").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/activity-mapp/module/rktTanam", "pick", 700, 400 );
        }else{
			event.preventDefault();
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
		var src_matstage_code = $("#src_matstage_code").val();	//MATURITY STAGE
		var src_afd = $("#src_afd").val();					//SEARCH AFD
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("download/data-rkt-tanam/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_coa_code/" + src_coa_code + "/src_afd/" + src_afd + "/search/" + encode64(search) + "/src_matstage_code/" + src_matstage_code,'_blank');
		}
    });		
			
    $("#btn_first").click(function() {
        page_num = 1;
        clearDetail();
        getData();
    });
    $("#btn_prev").click(function() {
        page_num--;
        clearDetail();
        getData();
    });
    $("#btn_next").click(function() {
        page_num++;
        clearDetail();
        getData();
    });
    $("#btn_last").click(function() {
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
		}
		else {
			$.ajax({
				type    : "post",
				url     : "rkt-tanam/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-tanam/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									if(validateInput()){
										$.ajax({
											type     : "post",
											url      : "rkt-tanam/save",
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
									}else{
										alert("Inflasi Harus Lebih Besar Dari 100%.");
									}
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
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "rkt-tanam/upd-locked-seq-status",
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
				url      : "rkt-tanam/upd-locked-seq-status",
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
	var SUM_TEXT11 = parseFloat(0);
	var SUM_TEXT12 = parseFloat(0);
	var SUM_TEXT13 = parseFloat(0);
	var SUM_TEXT17 = parseFloat(0);
	var SUM_TEXT18 = parseFloat(0);
	var SUM_TEXT19 = parseFloat(0);
	var SUM_TEXT20 = parseFloat(0);
	var SUM_TEXT21 = parseFloat(0);
	var SUM_TEXT22 = parseFloat(0);
	var SUM_TEXT23 = parseFloat(0);
	var SUM_TEXT24 = parseFloat(0);
	var SUM_TEXT25 = parseFloat(0);
	var SUM_TEXT26 = parseFloat(0);
	var SUM_TEXT27 = parseFloat(0);
	var SUM_TEXT28 = parseFloat(0);
	var SUM_TEXT29 = parseFloat(0);
	var SUM_TEXT30 = parseFloat(0);
	var SUM_TEXT31 = parseFloat(0);
	var SUM_TEXT33 = parseFloat(0);
	var SUM_TEXT34 = parseFloat(0);
	var SUM_TEXT35 = parseFloat(0);
	var SUM_TEXT36 = parseFloat(0);
	var SUM_TEXT37 = parseFloat(0);
	var SUM_TEXT38 = parseFloat(0);
	var SUM_TEXT39 = parseFloat(0);
	var SUM_TEXT40 = parseFloat(0);
	var SUM_TEXT41 = parseFloat(0);
	var SUM_TEXT42 = parseFloat(0);
	var SUM_TEXT43 = parseFloat(0);
	var SUM_TEXT44 = parseFloat(0);
	var SUM_TEXT45 = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "rkt-tanam/list",
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
					
					//tambah rowid temp
					$("#data_freeze tr:eq(" + index + ") input[id^=rowidtemp_]").val(row.ROW_ID_TEMP);

					//mewarnai jika row nya berasal dari temporary table
					if (row.ROW_ID_TEMP) {cekTempData(index)	;}
					
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text50_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
					$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.LAND_TYPE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.TOPOGRAPHY);
					$("#data_freeze tr:eq(" + index + ") input[id^=bulan_]").val(row.TAHUN_TANAM_M);
					$("#data_freeze tr:eq(" + index + ") input[id^=tahun_]").val(row.TAHUN_TANAM_Y);
					$("#data_freeze tr:eq(" + index + ") input[id^=text08_]").val(row.TAHUN_TANAM);
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
					
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(row.SUMBER_BIAYA);
					$("#text15_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").attr("selected", "selected");
					//$("#text15_" + index + " option[value='"+row.ACTIVITY_CLASS+"']").addClass("required");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.PLAN_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.PLAN_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.PLAN_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.PLAN_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.PLAN_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.PLAN_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.PLAN_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.PLAN_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.PLAN_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.PLAN_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.PLAN_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.PLAN_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number required");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.PLAN_SETAHUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.TOTAL_RP_QTY, 2));
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
					
					$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.TOTAL_RP_SETAHUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text45_]").addClass("number");
					
					$("#data tr:eq(" + index + ") input[id^=text46_]").val(row.MATURITY_STAGE_SMS1);
					$("#data tr:eq(" + index + ") input[id^=text47_]").val(row.MATURITY_STAGE_SMS2);
					
					$("#text61_" + index + " option[value='"+row.TIPE_NORMA+"']").attr("selected", "selected");
					$("#data tr:eq(" + index + ")").removeAttr("style");
                    $("#data tr:eq(1) input[id^=text02_]").focus();
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.ROTASI_SMS1, 0));
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.ROTASI_SMS2, 0));
					/*
					//perhitungan summary
					SUM_TEXT11 = (row.HA_PLANTED) ? SUM_TEXT11 + parseFloat(row.HA_PLANTED) : SUM_TEXT11;
					SUM_TEXT12 = (row.POKOK_TANAM) ? SUM_TEXT12 + parseFloat(row.POKOK_TANAM) : SUM_TEXT12;
					SUM_TEXT13 = (row.SPH) ? SUM_TEXT13 + parseFloat(row.SPH) : SUM_TEXT13;
					SUM_TEXT17 = (row.QTY_ACTUAL) ? SUM_TEXT17 + parseFloat(row.QTY_ACTUAL) : SUM_TEXT17;
					SUM_TEXT18 = (row.DIS_TAHUN_BERJALAN) ? SUM_TEXT18 + parseFloat(row.DIS_TAHUN_BERJALAN) : SUM_TEXT18;
					SUM_TEXT19 = (row.DIS_JAN) ? SUM_TEXT19 + parseFloat(row.DIS_JAN) : SUM_TEXT19;
					SUM_TEXT20 = (row.DIS_FEB) ? SUM_TEXT20 + parseFloat(row.DIS_FEB) : SUM_TEXT20;
					SUM_TEXT21 = (row.DIS_MAR) ? SUM_TEXT21 + parseFloat(row.DIS_MAR) : SUM_TEXT21;
					SUM_TEXT22 = (row.DIS_APR) ? SUM_TEXT22 + parseFloat(row.DIS_APR) : SUM_TEXT22;
					SUM_TEXT23 = (row.DIS_MAY) ? SUM_TEXT23 + parseFloat(row.DIS_MAY) : SUM_TEXT23;
					SUM_TEXT24 = (row.DIS_JUN) ? SUM_TEXT24 + parseFloat(row.DIS_JUN) : SUM_TEXT24;
					SUM_TEXT25 = (row.DIS_JUL) ? SUM_TEXT25 + parseFloat(row.DIS_JUL) : SUM_TEXT25;
					SUM_TEXT26 = (row.DIS_AUG) ? SUM_TEXT26 + parseFloat(row.DIS_AUG) : SUM_TEXT26;
					SUM_TEXT27 = (row.DIS_SEP) ? SUM_TEXT27 + parseFloat(row.DIS_SEP) : SUM_TEXT27;
					SUM_TEXT28 = (row.DIS_OCT) ? SUM_TEXT28 + parseFloat(row.DIS_OCT) : SUM_TEXT28;
					SUM_TEXT29 = (row.DIS_NOV) ? SUM_TEXT29 + parseFloat(row.DIS_NOV) : SUM_TEXT29;
					SUM_TEXT30 = (row.DIS_DEC) ? SUM_TEXT30 + parseFloat(row.DIS_DEC) : SUM_TEXT30;
					SUM_TEXT31 = (row.PLAN_SETAHUN) ? SUM_TEXT31 + parseFloat(row.PLAN_SETAHUN) : SUM_TEXT31;
					SUM_TEXT33 = (row.COST_JAN) ? SUM_TEXT33 + parseFloat(row.COST_JAN) : SUM_TEXT33;
					SUM_TEXT34 = (row.COST_FEB) ? SUM_TEXT34 + parseFloat(row.COST_FEB) : SUM_TEXT34;
					SUM_TEXT35 = (row.COST_MAR) ? SUM_TEXT35 + parseFloat(row.COST_MAR) : SUM_TEXT35;
					SUM_TEXT36 = (row.COST_APR) ? SUM_TEXT36 + parseFloat(row.COST_APR) : SUM_TEXT36;
					SUM_TEXT37 = (row.COST_MAY) ? SUM_TEXT38 + parseFloat(row.COST_MAY) : SUM_TEXT38;
					SUM_TEXT38 = (row.COST_JUN) ? SUM_TEXT38 + parseFloat(row.COST_JUN) : SUM_TEXT38;
					SUM_TEXT39 = (row.COST_JUL) ? SUM_TEXT39 + parseFloat(row.COST_JUL) : SUM_TEXT39;
					SUM_TEXT40 = (row.COST_AUG) ? SUM_TEXT40 + parseFloat(row.COST_AUG) : SUM_TEXT40;
					SUM_TEXT41 = (row.COST_SEP) ? SUM_TEXT41 + parseFloat(row.COST_SEP) : SUM_TEXT41;
					SUM_TEXT42 = (row.COST_OCT) ? SUM_TEXT42 + parseFloat(row.COST_OCT) : SUM_TEXT42;
					SUM_TEXT43 = (row.COST_NOV) ? SUM_TEXT43 + parseFloat(row.COST_NOV) : SUM_TEXT43;
					SUM_TEXT44 = (row.COST_DEC) ? SUM_TEXT44 + parseFloat(row.COST_DEC) : SUM_TEXT44;
					SUM_TEXT45 = (row.COST_SETAHUN) ? SUM_TEXT45 + parseFloat(row.COST_SETAHUN) : SUM_TEXT45;
					*/
                });
				
				/*
				//summary
				$("#sum11").val(accounting.formatNumber(SUM_TEXT11, 0));
				$("#sum11").addClass("integer grandtotal_text");
				$("#sum12").val(accounting.formatNumber(SUM_TEXT12, 0));
				$("#sum12").addClass("integer grandtotal_text");
				$("#sum13").val(accounting.formatNumber(SUM_TEXT13, 0));
				$("#sum13").addClass("integer grandtotal_text");
				$("#sum17").val(accounting.formatNumber(SUM_TEXT17, 0));
				$("#sum17").addClass("integer grandtotal_text");
				$("#sum18").val(accounting.formatNumber(SUM_TEXT18, 0));
				$("#sum18").addClass("integer grandtotal_text");
				$("#sum19").val(accounting.formatNumber(SUM_TEXT19, 0));
				$("#sum19").addClass("integer grandtotal_text");
				$("#sum20").val(accounting.formatNumber(SUM_TEXT20, 0));
				$("#sum20").addClass("integer grandtotal_text");
				$("#sum21").val(accounting.formatNumber(SUM_TEXT21, 0));
				$("#sum21").addClass("integer grandtotal_text");
				$("#sum22").val(accounting.formatNumber(SUM_TEXT22, 0));
				$("#sum22").addClass("integer grandtotal_text");
				$("#sum23").val(accounting.formatNumber(SUM_TEXT23, 0));
				$("#sum23").addClass("integer grandtotal_text");
				$("#sum24").val(accounting.formatNumber(SUM_TEXT24, 0));
				$("#sum24").addClass("integer grandtotal_text");
				$("#sum25").val(accounting.formatNumber(SUM_TEXT25, 0));
				$("#sum25").addClass("integer grandtotal_text");
				$("#sum26").val(accounting.formatNumber(SUM_TEXT26, 2));
				$("#sum26").addClass("number grandtotal_text");
				$("#sum27").val(accounting.formatNumber(SUM_TEXT27, 2));
				$("#sum27").addClass("number grandtotal_text");
				$("#sum28").val(accounting.formatNumber(SUM_TEXT28, 2));
				$("#sum28").addClass("number grandtotal_text");
				$("#sum29").val(accounting.formatNumber(SUM_TEXT29, 2));
				$("#sum29").addClass("number grandtotal_text");
				$("#sum30").val(accounting.formatNumber(SUM_TEXT30, 2));
				$("#sum30").addClass("number grandtotal_text");
				$("#sum31").val(accounting.formatNumber(SUM_TEXT31, 2));
				$("#sum31").addClass("number grandtotal_text");
				$("#sum33").val(accounting.formatNumber(SUM_TEXT33, 2));
				$("#sum33").addClass("number grandtotal_text");
				$("#sum34").val(accounting.formatNumber(SUM_TEXT34, 2));
				$("#sum34").addClass("number grandtotal_text");
				$("#sum35").val(accounting.formatNumber(SUM_TEXT35, 2));
				$("#sum35").addClass("number grandtotal_text");
				$("#sum36").val(accounting.formatNumber(SUM_TEXT36, 2));
				$("#sum36").addClass("number grandtotal_text");
				$("#sum37").val(accounting.formatNumber(SUM_TEXT37, 2));
				$("#sum37").addClass("number grandtotal_text");
				$("#sum38").val(accounting.formatNumber(SUM_TEXT38, 2));
				$("#sum38").addClass("number grandtotal_text");
				$("#sum39").val(accounting.formatNumber(SUM_TEXT39, 2));
				$("#sum39").addClass("number grandtotal_text");
				$("#sum40").val(accounting.formatNumber(SUM_TEXT40, 2));
				$("#sum40").addClass("number grandtotal_text");
				$("#sum41").val(accounting.formatNumber(SUM_TEXT41, 2));
				$("#sum41").addClass("number grandtotal_text");
				$("#sum42").val(accounting.formatNumber(SUM_TEXT42, 2));
				$("#sum42").addClass("number grandtotal_text");
				$("#sum43").val(accounting.formatNumber(SUM_TEXT43, 2));
				$("#sum43").addClass("number grandtotal_text");
				$("#sum44").val(accounting.formatNumber(SUM_TEXT44, 2));
				$("#sum44").addClass("number grandtotal_text");
				$("#sum45").val(accounting.formatNumber(SUM_TEXT45, 2));
				$("#sum45").addClass("number grandtotal_text");
				
				$("#tfoot").removeAttr("style");
				*/
            }else{
				$("#tfoot").hide();
			}
        }
    });
}

</script>