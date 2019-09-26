<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan RKT OPEX VRA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/07/2013
Update Terakhir		:	22/07/2013
Revisi				:	
	NBU 05/05/2015	: 	- penutupan button lock & unlock di line 75 - 78
					    - penutupan pengecekan lock pada diri sendiri di line 274
					    - penutupan pengecekan lock untuk button save di line 375	
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
			<legend>RKT OPEX - VRA</legend>
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
					<td width="15%"><b>INFLASI (%) :</b></td>
					<td width="85%">
						<input type="text" name="inflasi_nasional" id="inflasi_nasional" value="" style="width:70px;" readonly="readonly"/>
					</td>
				</tr>
			</table>
			
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE COA</th>
					<th>DESKRIPSI</th>
					<th>GROUP BUM</th>
					<th>BUM AKTUAL<BR><span class='period_before'></span></th>
					<th>BUM TAKSASI<BR><span class='period_before'></span></th>
					<th>ANTISIPASI AKTUAL<BR><span class='period_before'></span></th>
					<th>PERSENTASE<BR>INFLASI (%)</th>
					<th>BIAYA <span class='period_budget'></span><BR>TOTAL QTY</th>
					<th>BIAYA <span class='period_budget'></span><BR>JAN</th>
					<th>BIAYA <span class='period_budget'></span><BR>FEB</th>
					<th>BIAYA <span class='period_budget'></span><BR>MAR</th>
					<th>BIAYA <span class='period_budget'></span><BR>APR</th>
					<th>BIAYA <span class='period_budget'></span><BR>MEI</th>
					<th>BIAYA <span class='period_budget'></span><BR>JUN</th>
					<th>BIAYA <span class='period_budget'></span><BR>JUL</th>
					<th>BIAYA <span class='period_budget'></span><BR>AGS</th>
					<th>BIAYA <span class='period_budget'></span><BR>SEP</th>
					<th>BIAYA <span class='period_budget'></span><BR>OKT</th>
					<th>BIAYA <span class='period_budget'></span><BR>NOV</th>
					<th>BIAYA <span class='period_budget'></span><BR>DES</th>
					<th>KETERANGAN</th>
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td width='50px'>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="rowidtemp[]" id="rowidtemp_" readonly="readonly"/>
						<input type="hidden" name="trxcode[]" id="trxcode_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='100px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='300px'><input type="text" name="text05[]" id="text05_" readonly="readonly" value='5'/></td>
					<td width='300px'>
						<input type="hidden" name="text06[]" id="text06_" readonly="readonly" value='6' />
						<input type="text" name="text07[]" id="text07_" readonly="readonly" value='7'/>
					</td>
					<td width='120px'><input type="text" name="text08[]" id="text08_" value='8'/></td>
					<td width='120px'><input type="text" name="text09[]" id="text09_" value='9'/></td>
					<td width='120px'><input type="text" name="text10[]" id="text10_" readonly="readonly" value='10'/></td>
					<td width='120px'><input type="text" name="text11[]" id="text11_" readonly="readonly" value='11'/></td>
					<td width='120px'><input type="text" name="text12[]" id="text12_" readonly="readonly" value='12'/></td>
					<td width='120px'><input type="text" name="text13[]" id="text13_" readonly="readonly" value='13'/></td>
					<td width='120px'><input type="text" name="text14[]" id="text14_" readonly="readonly" value='14'/></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_" readonly="readonly" value='15'/></td>
					<td width='120px'><input type="text" name="text16[]" id="text16_" readonly="readonly" value='16'/></td>
					<td width='120px'><input type="text" name="text17[]" id="text17_" readonly="readonly" value='17'/></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" readonly="readonly" value='18'/></td>
					<td width='120px'><input type="text" name="text19[]" id="text19_" readonly="readonly" value='19'/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" readonly="readonly" value='20'/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" readonly="readonly" value='21'/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" readonly="readonly" value='22'/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" readonly="readonly" value='23'/></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" readonly="readonly" value='24'/></td>
					<td width='300px'><input type="text" name="text25[]" id="text25_" value='25'/></td>
				</tr>
			</tbody>
			<!--
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='5' class='grandtotal'>TOTAL</td>
					<td><input type="text" name="sum08" id="sum08" readonly="readonly"/></td>
					<td><input type="text" name="sum09" id="sum09" readonly="readonly"/></td>
					<td><input type="text" name="sum10" id="sum10" readonly="readonly"/></td>
					<td><input type="text" name="sum11" id="sum11" readonly="readonly" style='background:#000;'/></td>
					<td><input type="text" name="sum12" id="sum12" readonly="readonly"/></td>
					<td><input type="text" name="sum13" id="sum13" readonly="readonly"/></td>
					<td><input type="text" name="sum14" id="sum14" readonly="readonly"/></td>
					<td><input type="text" name="sum15" id="sum15" readonly="readonly"/></td>
					<td><input type="text" name="sum16" id="sum16" readonly="readonly"/></td>
					<td><input type="text" name="sum17" id="sum17" readonly="readonly"/></td>
					<td><input type="text" name="sum18" id="sum18" readonly="readonly"/></td>
					<td><input type="text" name="sum19" id="sum19" readonly="readonly"/></td>
					<td><input type="text" name="sum20" id="sum20" readonly="readonly"/></td>
					<td><input type="text" name="sum21" id="sum21" readonly="readonly"/></td>
					<td><input type="text" name="sum22" id="sum22" readonly="readonly"/></td>
					<td><input type="text" name="sum23" id="sum23" readonly="readonly"/></td>
					<td><input type="text" name="sum24" id="sum24" readonly="readonly"/></td>
					<td><input type="text" name="sum25" id="sum25" readonly="readonly" style='background:#000;'/></td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
			-->
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
		numFrozen:   5,     // optional
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
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			
			//set nama kolom yang mengandung tahun
			$(".period_budget").html($("#budgetperiod").val());
			$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
			
			$.ajax({
				type    : "post",
				url     : "rkt-opex-vra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
							url      : "rkt-opex-vra/get-status-periode", //cek status periode
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
											url     : "rkt-opex-vra/check-locked-seq", //check apakah status lock sendiri apakah lock
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
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
				url      : "rkt-opex-vra/save-temp",
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
		} else {
			$.ajax({
				type    : "post",
				url     : "rkt-opex-vra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						/*$.ajax({
							type    : "post",
							url     : "rkt-opex-vra/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{*/
									$.ajax({
										type     : "post",
										url      : "rkt-opex-vra/save",
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
	$("#btn_export_csv").live("click", function() {//DEKLARASI VARIABEL
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-opex-vra/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
				url      : "rkt-opex-vra/upd-locked-seq-status",
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
				url      : "rkt-opex-vra/upd-locked-seq-status",
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
	var SUM_TEXT08 = parseFloat(0);
	var SUM_TEXT09 = parseFloat(0);
	var SUM_TEXT10 = parseFloat(0);
	var SUM_TEXT12 = parseFloat(0);
	var SUM_TEXT13 = parseFloat(0);
	var SUM_TEXT14 = parseFloat(0);
	var SUM_TEXT15 = parseFloat(0);
	var SUM_TEXT16 = parseFloat(0);
	var SUM_TEXT17 = parseFloat(0);
	var SUM_TEXT18 = parseFloat(0);
	var SUM_TEXT19 = parseFloat(0);
	var SUM_TEXT20 = parseFloat(0);
	var SUM_TEXT21 = parseFloat(0);
	var SUM_TEXT22 = parseFloat(0);
	var SUM_TEXT23 = parseFloat(0);
	var SUM_TEXT24 = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "rkt-opex-vra/list",
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
					
					//mewarnai jika row nya berasal dari temporary table
					if (row.ROW_ID_TEMP) {cekTempData(index);}
					
					$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					
					//tambah rowid temp
					$("#data_freeze tr:eq(" + index + ") input[id^=rowidtemp_]").val(row.ROW_ID_TEMP);

					$("#data_freeze tr:eq(" + index + ") input[id^=trxcode_]").val(row.TRX_CODE);
					
					//left freeze panes row
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.COA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.COA_DESC);
					$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.GROUP_BUM_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.GROUP_BUM_DESCRIPTION);
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
					
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.ACTUAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.TAKSASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.ANTISIPASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.PERSENTASE_INFLASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.TOTAL_BIAYA, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.DIS_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.DIS_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.DIS_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.DIS_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.DIS_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.DIS_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.DIS_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.DIS_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.DIS_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.DIS_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.DIS_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.DIS_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(row.KETERANGAN);
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
					$("#inflasi_nasional").val(accounting.formatNumber(row.INFLASI_NASIONAL, 2));
					$("#inflasi_nasional").addClass("number");
					
					//perhitungan summary
					SUM_TEXT08 = (row.ACTUAL) ? SUM_TEXT08 + parseFloat(row.ACTUAL) : SUM_TEXT08;
					SUM_TEXT09 = (row.TAKSASI) ? SUM_TEXT09 + parseFloat(row.TAKSASI) : SUM_TEXT09;
					SUM_TEXT10 = (row.ANTISIPASI) ? SUM_TEXT10 + parseFloat(row.ANTISIPASI) : SUM_TEXT10;
					SUM_TEXT12 = (row.TOTAL_BIAYA) ? SUM_TEXT12 + parseFloat(row.TOTAL_BIAYA) : SUM_TEXT12;
					SUM_TEXT13 = (row.DIS_JAN) ? SUM_TEXT13 + parseFloat(row.DIS_JAN) : SUM_TEXT13;
					SUM_TEXT14 = (row.DIS_FEB) ? SUM_TEXT14 + parseFloat(row.DIS_FEB) : SUM_TEXT14;
					SUM_TEXT15 = (row.DIS_MAR) ? SUM_TEXT15 + parseFloat(row.DIS_MAR) : SUM_TEXT15;
					SUM_TEXT16 = (row.DIS_APR) ? SUM_TEXT16 + parseFloat(row.DIS_APR) : SUM_TEXT16;
					SUM_TEXT17 = (row.DIS_MAY) ? SUM_TEXT17 + parseFloat(row.DIS_MAY) : SUM_TEXT17;
					SUM_TEXT18 = (row.DIS_JUN) ? SUM_TEXT18 + parseFloat(row.DIS_JUN) : SUM_TEXT18;
					SUM_TEXT19 = (row.DIS_JUL) ? SUM_TEXT19 + parseFloat(row.DIS_JUL) : SUM_TEXT19;
					SUM_TEXT20 = (row.DIS_AUG) ? SUM_TEXT20 + parseFloat(row.DIS_AUG) : SUM_TEXT20;
					SUM_TEXT21 = (row.DIS_SEP) ? SUM_TEXT21 + parseFloat(row.DIS_SEP) : SUM_TEXT21;
					SUM_TEXT22 = (row.DIS_OCT) ? SUM_TEXT22 + parseFloat(row.DIS_OCT) : SUM_TEXT22;
					SUM_TEXT23 = (row.DIS_NOV) ? SUM_TEXT23 + parseFloat(row.DIS_NOV) : SUM_TEXT23;
					SUM_TEXT24 = (row.DIS_DEC) ? SUM_TEXT24 + parseFloat(row.DIS_DEC) : SUM_TEXT24;
                });
				/*
				//summary
				$("#sum08").val(accounting.formatNumber(SUM_TEXT08, 2));
				$("#sum08").addClass("number");
				$("#sum09").val(accounting.formatNumber(SUM_TEXT09, 2));
				$("#sum09").addClass("number");
				$("#sum10").val(accounting.formatNumber(SUM_TEXT10, 2));
				$("#sum10").addClass("number");
				$("#sum12").val(accounting.formatNumber(SUM_TEXT12, 2));
				$("#sum12").addClass("number");
				$("#sum13").val(accounting.formatNumber(SUM_TEXT13, 2));
				$("#sum13").addClass("number");
				$("#sum14").val(accounting.formatNumber(SUM_TEXT14, 2));
				$("#sum14").addClass("number");
				$("#sum15").val(accounting.formatNumber(SUM_TEXT15, 2));
				$("#sum15").addClass("number");
				$("#sum16").val(accounting.formatNumber(SUM_TEXT16, 2));
				$("#sum16").addClass("number");
				$("#sum17").val(accounting.formatNumber(SUM_TEXT17, 2));
				$("#sum17").addClass("number");
				$("#sum18").val(accounting.formatNumber(SUM_TEXT18, 2));
				$("#sum18").addClass("number");
				$("#sum19").val(accounting.formatNumber(SUM_TEXT19, 2));
				$("#sum19").addClass("number");
				$("#sum20").val(accounting.formatNumber(SUM_TEXT20, 2));
				$("#sum20").addClass("number");
				$("#sum21").val(accounting.formatNumber(SUM_TEXT21, 2));
				$("#sum21").addClass("number");
				$("#sum22").val(accounting.formatNumber(SUM_TEXT22, 2));
				$("#sum22").addClass("number");
				$("#sum23").val(accounting.formatNumber(SUM_TEXT23, 2));
				$("#sum23").addClass("number");
				$("#sum24").val(accounting.formatNumber(SUM_TEXT24, 2));
				$("#sum24").addClass("number");
				
				$("#tfoot").removeAttr("style");
				*/
            }else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
