<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Kastrasi Sanitasi
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	28/06/2013
Update Terakhir		:	28/06/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
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
					<td width="15%">PENCARIAN :</td>
					<td width="85%">
						<input type="text" name="key_find" id="key_find" value="" style="width:200px;" />
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
			<legend>NORMA Kastrasi Sanitasi</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
				</tr>
				<tr>
					<td width="50%" align="right">		
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th>PERIOD</th>
					<th>KODE<BR>AKTIVITAS</th>
					<th>AKTIVITAS</th>
					<th>KELAS TANAH</th>
					<th>UMUR TANAM (BULAN)</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none">
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:100px'/></td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:100px'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:350px'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:100px'/></td>
					<td><input type="text" name="text05[]" id="text05_" style='width:100px'/></td>
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
//untuk width scroll area
var wscrollarea = window.innerWidth - 110;
document.getElementById("scrollarea").style.width = wscrollarea + "px";

var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
	$("#btn_unlock").hide();
	$("#btn_lock").hide();
	$("#btn_save").hide();
	
    $("#btn_find").click(function() {
		cek_squence();
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
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
	
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#key_find").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
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
	
	$("[id^=text05]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		$("#tChange_" + row).val("Y");
    });
	
	//untuk proses simpan draft
	$("#btn_save_temp").click( function() {
		var reference_role = "<?=$this->referencerole?>";
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( src_activity_desc == '' ) {
			alert("Anda Harus Memilih Aktivitas Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "rkt-kastrasi-sanitasi/save-temp",
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
		var budgetperiod = $("#budgetperiod").val();
		var current_budgetperiod = "<?=$this->period?>";
		
		if(confirm("Anda Yakin Untuk Menyimpan Data?")){
			$.ajax({
					type    : "post",
					url     : "rkt-kastrasi-sanitasi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
					data    : $("#form_init").serialize(),
					cache   : false,
					dataType: "json",
					success : function(data) {
						if(data==1){
							$.ajax({
								type     : "post",
								url      : "norma-kastrasi-sanitasi/save",
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
										cek_squence();
									}
								});
						}else{
							alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
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
		
		/*if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		*/
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-kastrasi-sanitasi/key_find/" + key_find,'_blank');
		//}
    });
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
    $("#btn_find").trigger("click");
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-kastrasi-sanitasi/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=LOCKED",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Finalisasi Data Berhasil.");
					$("#btn_find").trigger("click");
					cek_squence();
				}
			});	
		}
    }); 
	
	$("#btn_unlock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Memproses Ulang Data?")){
			$.ajax({
				type     : "post",
				url      : "norma-kastrasi-sanitasi/upd-locked-seq-status",
				data     : $("#form_init").serialize()+"&status=",
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					alert("Anda Dapat Melakukan Proses Ulang Data.");
					$("#btn_find").trigger("click");
					cek_squence();
				}
			});	
		}
    });
			
});

function cek_squence(){
	$.ajax({
		type    : "post",
		url     : "norma-kastrasi-sanitasi/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
		data    : $("#form_init").serialize(),
		cache   : false,
		dataType: "json",
		success : function(data) {
			if(data==1){
					$.ajax({
						type    : "post",
						url     : "norma-kastrasi-sanitasi/check-locked-seq", //check apakah status lock sendiri apakah lock
						data    : $("#form_init").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){
								$("#btn_unlock").show();
								$("#btn_lock").hide();
								$("#btn_save").hide();
								$("#btn_add").hide();
								$(".button_delete").hide();
								$(".button_add").hide();
							}else{
								$("#btn_unlock").hide();
								$("#btn_lock").show();
								$("#btn_save").show();
								$("#btn_add").show();
								$(".button_delete").show();
								$(".button_add").show();
							}
						}
					})
												
			}else{
				alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				$("#btn_unlock").hide();
				$("#btn_save").hide();
				$("#btn_add").hide();
				$(".button_delete").hide();
				$(".button_add").hide();
			}
		}
	})
}

function getData(){
    $("#page_num").val(page_num);
	var total_premi_langsir = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "norma-kastrasi-sanitasi/list",
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
                    var tr = $("#data tr:eq(0)").clone();
                    $("#data").append(tr);
                    var index = ($("#data tr").length - 1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});					
					                    
					$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.PERIOD_BUDGET);					
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.ACTIVITY_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.ACTIVITY_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.LAND_SUITABILITY);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(accounting.formatNumber(row.UMUR,0));
					
					if(data.cek.STATUS == 'LOCKED'){
						$("#data tr:eq(" + index + ") input[id^=text05_]").attr("readonly", "readonly");
					}else{
						$("#data tr:eq(" + index + ") input[id^=text05_]").attr("readonly", "");
					}
					
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=key_find]").focus();
                });
            }
        }
    });
}
</script>
