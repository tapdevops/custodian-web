<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Mapping Group SHE - COA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	04/06/2013
Update Terakhir		:	04/06/2013
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
			<legend>MAPPING GROUP SHE - COA</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">		
						&nbsp;
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>								
				<tr>
					<th rowspan='2' style='color:#999'>+</th>
					<th rowspan='2' style='color:#999'>x</th>
					<th>KODE GRUP SHE</th>
					<th>DESKRIPSI</th>
					<th>KODE COA</th>
					<th>DESKRIPSI COA</th>
					<th>SUB GRUP SHE</th>
					<th>DESKRIPSI SUB GRUP SHE</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<th>6</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none">
					<td align='center'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" style='width:100px;' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:300px;'/></td>
					<td><input type="text" name="text04[]" id="text04_" style='width:100px;' title="Tekan F9 Untuk Memilih."/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:300px;'/></td>
					<td><input type="text" name="text06[]" id="text06_" style='width:100px;'/></td>
					<td><input type="text" name="text07[]" id="text07_" style='width:300px;'/></td>
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
	$("#btn_save_temp").hide();
	//BUTTON ACTION
	$("#btn_find").click(function() {
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
		$("#btn_save").show();
		$("#btn_add").show();
    });
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#btn_add").live("click", function(event) {
		var tr = $("#data tr:eq(0)").clone();
		$("#data").append(tr);
		var index = ($("#data tr").length - 1);					
		$("#data tr:eq(" + index + ")").find("input, select").each(function() {
			$(this).attr("id", $(this).attr("id") + index);
		});		
		
		var row = $(this).attr("id").split("_")[1];
		
		$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text02_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text03_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn00_]").live("click", function(event) {
		var tr = $("#data tr:eq(0)").clone();
		$("#data").append(tr);
		var index = ($("#data tr").length - 1);					
		$("#data tr:eq(" + index + ")").find("input, select").each(function() {
			$(this).attr("id", $(this).attr("id") + index);
		});		
		
		var row = $(this).attr("id").split("_")[1];
		
		$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text02_]").val($("#text02_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text03_]").val($("#text03_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text04_]").val($("#text04_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text05_]").val($("#text05_" + row).val());
		$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn01_]").live("click", function(event) {
		
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		$.ajax({
			type    : "post",
			url     : "mapping-group-she-coa/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
			data    : $("#form_init").serialize(),
			cache   : false,
			dataType: "json",
			success : function(data) {
				if(data==1){
					//cek status sequence current norma/rkt
					$.ajax({
						type    : "post",
						url     : "mapping-group-she-coa/check-locked-seq",
						data    : $("#form_init").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){ 
								alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
							}else{
								if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
									//cek jika rowid kosong & klik delete, maka kosongkan seluruh data
									if (rowid == '') {
										clearTextField(row);
									}
									else {
										$.ajax({
											type     : "post",
											url      : "mapping-group-she-coa/delete/rowid/"+encode64(rowid),
											cache    : false,
											//dataType : 'json',
											success  : function(data) {
												if (data == "done") {
													clearTextField(row);
													alert("Data berhasil dihapus.");
												}else{
													alert(data);
												}
											}
										});
									}	
								}
							}
						}
					})
				}else{
					alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				}
			}
		})
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
				url      : "mapping-group-she-coa/save-temp",
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
        $.ajax({
				type    : "post",
				url     : "mapping-group-she-coa/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if(data==1){
						//cek status sequence current norma/rkt
						$.ajax({
							type    : "post",
							url     : "mapping-group-she-coa/check-locked-seq",
							data    : $("#form_init").serialize(),
							cache   : false,
							dataType: "json",
							success : function(data) {
								if(data.STATUS == 'LOCKED'){ 
									alert('ANDA TIDAK DAPAT MELAKUKAN PERUBAHAN DATA KARENA DATA DIKUNCI.');
								}else{
									$.ajax({
										type     : "post",
										url      : "mapping-group-she-coa/save",
										data     : $("#form_init").serialize(),
										cache    : false,
										//dataType : 'json',
										success  : function(data) {
											if (data == "done") {
												alert("Data berhasil disimpan.");
												$("#btn_find").trigger("click");
											}else if (data == "no_alert") {
												$("#btn_find").trigger("click");
											}else{
												alert(data);
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
    });
	$("#btn_cancel").click(function() {
        self.close();
    });
	
	//SEARCH FREE TEXT
	$("#key_find").live("keydown", function(event) {
		//tekan enter
        if (event.keyCode == 13) {
			event.preventDefault();
		}
    });
	
	//PAGING
    $("#btn_first").click(function() {
		$("#btn_save").trigger("click");
        page_num = 1;
    });
    $("#btn_prev").click(function() {
		$("#btn_save").trigger("click");
        page_num--;
    });
    $("#btn_next").click(function() {
		$("#btn_save").trigger("click");
        page_num++;
    });
    $("#btn_last").click(function() {
		$("#btn_save").trigger("click");
        page_num = page_max;
    });
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
	
	//LOV UTK INPUTAN
	$("input[id^=text02_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/group-she/module/mappingGroupSheCoa/row/" + row, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text04_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/coa/module/mappingGroupSheCoa/row/" + row, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
    $("#btn_find").trigger("click");
	cek_squence();
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "mapping-group-she-coa/upd-locked-seq-status",
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
				url      : "mapping-group-she-coa/upd-locked-seq-status",
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
		url     : "mapping-group-she-coa/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
		data    : $("#form_init").serialize(),
		cache   : false,
		dataType: "json",
		success : function(data) {
			if(data==1){
					$.ajax({
						type    : "post",
						url     : "mapping-group-she-coa/check-locked-seq", //check apakah status lock sendiri apakah lock
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
    //
    $.ajax({
        type    : "post",
        url     : "mapping-group-she-coa/list",
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
					
					$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
                    $("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.GROUP_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.DESCRIPTION);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.COA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.COA_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.SUB_GROUP_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.SUB_GROUP_DESC);
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>
