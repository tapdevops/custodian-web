<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Tunjangan
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	07/06/2013
Update Terakhir		:	07/06/2013
Revisi				:	
=========================================================================================================================
*/
?>
<div>
    <form name="form_init" id="form_init">
        <fieldset>
			<legend>PENCARIAN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
				<tr>
					<td width="15%">PENCARIAN :</td>
					<td width="65%">
						<input type="text" name="key_find" id="key_find" value="" style="width:200px;" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
						<input type="button" name="btn_refresh" id="btn_refresh" value="RESET" class="button" />
					</td>
					<!-- 
					<td width="80%" align="right">	
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" /> 
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						
					</td>
					-->
				</tr>
			</table>
			<input type="hidden" name="page_num" id="page_num" value="1" />
            <input type="hidden" name="page_rows" id="page_rows" value="20" />
		</fieldset>
    </form>
</div>
<br />
<div>
	<fieldset>
		<legend>MASTER TUNJANGAN</legend>
		<div id='scrollarea'>
		<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
		<thead>
		<tr>
				<th>TIPE TUNJANGAN</th>
				<th>DESKRIPSI</th>
				<th>UOM</th>
				<th>FLAG RP/HK</th>
				<th>KT/KL</th>
				<th>RUMUS</th>
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
				<td>
					<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
					<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
					<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:150px;'/>
				</td>
				<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:450px;'/></td>
				<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:50px;'/></td>
				<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:50px;'/></td>
				<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:50px;'/></td>
				<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:450px;'/></td>
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
    $("#btn_find").click(function() {
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
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
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + count);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });
    $("#btn_find").trigger("click");
	
	cek_squence();
	
	$("#btn_lock").live("click", function(event) {
		if(confirm("Anda Yakin Untuk Melakukan Finalisasi Data?")){
			$.ajax({
				type     : "post",
				url      : "tunjangan/upd-locked-seq-status",
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
				url      : "tunjangan/upd-locked-seq-status",
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
		url     : "tunjangan/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
		data    : $("#form_init").serialize(),
		cache   : false,
		dataType: "json",
		success : function(data) {
			if(data==1){
					$.ajax({
						type    : "post",
						url     : "tunjangan/check-locked-seq", //check apakah status lock sendiri apakah lock
						data    : $("#form_init").serialize(),
						cache   : false,
						dataType: "json",
						success : function(data) {
							if(data.STATUS == 'LOCKED'){
								$("#btn_unlock").show();
								$("#btn_lock").hide();
								$("#btn_save").hide();
								$(".button_delete").hide();
							}else{
								$("#btn_unlock").hide();
								$("#btn_lock").show();
								$("#btn_save").show();
								$(".button_delete").show();
							}
						}
					})
												
			}else{
				alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
				$("#btn_unlock").hide();
				$("#btn_save").hide();
				$(".button_delete").hide();
			}
		}
	})
}

function getData(){
    $("#page_num").val(page_num);	
    //
    $.ajax({
        type    : "post",
        url     : "tunjangan/list",
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
                    $("#data").append(tr);var index = ($("#data tr").length - 1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});
					
                    $("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.TUNJANGAN_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.DESCRIPTION);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.UOM);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.FLAG_RP_HK);
					$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.FLAG_EMPLOYEE_STATUS);
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.RUMUS);
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>
