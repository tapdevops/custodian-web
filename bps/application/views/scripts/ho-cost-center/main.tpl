<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master COA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
Revisi				:	
=========================================================================================================================
*/
?>
<form name="form_init" id="form_init">
	<div>
        <fieldset>
			<legend>PENCARIAN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
				<tr>
					<td width="15%">PENCARIAN :</td>
					<td width="65%">
						<input type="hidden" name="budgetperiod" id="budgetperiod" value="<?=$this->period?>" style="width:200px;" class='filter'/>
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
			<legend>MASTER COST CENTER</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<!--<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>-->
					<td width="50%" align="right">		
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th style='color:#999'>x</th>
					<th>CC</th>
					<th>COST CENTER</th>
					<th>PEMILIK COST CENTER</th>
					<th>DIVISI</th>
					<th>KEPALA DIVISI</th>
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
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:100px;'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:200px;'/></td>
					<td><input type="text" name="text04[]" id="text04_" style='width:400px;'/></td>
					<td>
						<input type="hidden" name="text07[]" id="text07_" readonly="readonly" style="width:250px;"/>
						<input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:250px;'/>
					</td>
					<td><input type="text" name="text06[]" id="text06_" style='width:300px;'/></td>
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
	$("#btn_lock").hide();
	$("#btn_unlock").hide();
	$('#btn_save').show();

	//BUTTON ACTION
    $("#btn_find").click(function() {
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
    });

/*	$("#btn_find").click(function() {
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
		
		$.ajax({
			type    : "post",
			url     : "ho-cost-center/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
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
						url      : "ho-cost-center/get-status-periode", //cek status periode
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
										url     : "ho-cost-center/check-locked-seq", //check apakah status lock sendiri apakah lock
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
		//}
    });	
*/
	$("#btn_refresh").click(function() {
		location.reload();
    });
	
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();    //mapping textfield name terhadap field name di DB
		
		if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
			$.ajax({
				type: 'POST',
				url: 'ho-cost-center/delete/rowid/' + encode64(rowid),
				cache: false,
				dataType: 'json',
				success: function(data) {
					if (data.return == "done") {
						clearTextField(row);
						alert('Data berhasil dihapus');
					} else {
						alert(data.return);
					}
				}
			});
		}
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
    });
	
    $("#btn_find").trigger("click");
	
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
				
		$.ajax({
			type     : "post",
			url      : "ho-cost-center/save",
			data     : $("#form_init").serialize(),
			cache    : false,
			dataType : 'json',
			success  : function(data) {
				if (data.return == "locked") {
					alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
				}else if (data.return == "done") {
					alert("Data berhasil disimpan.");
					$("#btn_find").trigger("click");
				}else{
					alert(data.return);
				}
			}
		});
    });

	$("#btn_cancel").click(function() {
        self.close();
    });

});

function getData(){
    $("#page_num").val(page_num);	
    //
    $.ajax({
        type    : "post",
        url     : "ho-cost-center/list",
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
	                    var tr = $("#data tr:eq(0)").clone();
	                    $("#data").append(tr);var index = ($("#data tr").length - 1);					
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});
						
	                    $("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
	                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.HCC_CC);
	                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.HCC_COST_CENTER);
						$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.HCC_COST_CENTER_HEAD);
	                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.DIV_NAME);
	                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.HCC_DIVISION_HEAD);
	                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.HCC_DIVISI);
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
	                    $("#data tr:eq(1) input[id^=text02_]").focus();
	                });
	            }
	        }
        }
    });
}
</script>
