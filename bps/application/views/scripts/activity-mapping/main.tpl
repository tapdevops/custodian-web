<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Mapping Aktivitas Untuk Penggunaan RKT
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	10/09/2013
Update Terakhir		:	10/09/2013
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
   
	</div>
	<br />
	<div>
		<fieldset>
			<legend>MAPPING AKTIVITAS UNTUK PENGGUNAAN RKT</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">		
						&nbsp;
					</td>
					<td width="50%" align="right">
						<!--
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
						-->
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
		<div id='scrollarea'>
		<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
		<thead>				
		<tr>
			<!--
			<th rowspan='2' style='color:#999'>+</th>
			<th rowspan='2' style='color:#999'>X</th>
			-->
			<th>KODE AKTIVITAS</th>
			<th>DESKRIPSI AKTIVITAS</th>
			<th>KODE TIPE GROUP AKTIVITAS</th>
			<th>DESKRIPSI TIPE GROUP AKTIVITAS</th>
			<th>KODE RKT</th>
			<th>DESKRIPSI RKT</th>
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
					<!--
					<td align='center'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					-->
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:150px;'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:250px;'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:150px;'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:250px;'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:150px;'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:250px;'/></td>
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
	//BUTTON ACTION
    $("#btn_find").click(function() {
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
		$("#btn_save").show();
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
		$("#data tr:eq(" + index + ") input[id^=text02_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text02_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text03_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text06_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text06_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn00_]").live("click", function(event) {
		$("#btn_add").trigger("click");
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
			//cek jika rowid kosong & klik delete, maka kosongkan seluruh data
			if (rowid == '') {
				clearTextField(row);
			}
			else {
				$.ajax({
					type     : "post",
					url      : "activity-mapping/delete/rowid/"+encode64(rowid),
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
    });
	$("#btn_save").click( function() {
        $.ajax({
            type     : "post",
            url      : "activity-mapping/save",
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
		//$("#btn_save").trigger("click");
        page_num = 1;
        clearDetail();
        getData();
    });
    $("#btn_prev").click(function() {
		//$("#btn_save").trigger("click");
        page_num--;
        clearDetail();
        getData();
    });
    $("#btn_next").click(function() {
		//$("#btn_save").trigger("click");
        page_num++;
        clearDetail();
        getData();
    });
    $("#btn_last").click(function() {
		//$("#btn_save").trigger("click");
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
	
	//LOV DI INPUTAN
	$("input[id^=text02_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text02_'+row).is('[readonly]') == false) { 
				//lov
				popup("pick/activity/module/activityMapping/row/" + row, "pick", 700, 400 );
			}
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text06_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text06_'+row).is('[readonly]') == false) { 
				//lov
				popup("pick/rkt/module/activityMapping/row/" + row, "pick", 700, 400 );
			}
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
				url      : "activity-mapping/upd-locked-seq-status",
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
				url      : "activity-mapping/upd-locked-seq-status",
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
		url     : "activity-mapping/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
		data    : $("#form_init").serialize(),
		cache   : false,
		dataType: "json",
		success : function(data) {
			if(data==1){
					$.ajax({
						type    : "post",
						url     : "activity-mapping/check-locked-seq", //check apakah status lock sendiri apakah lock
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
        url     : "activity-mapping/list",
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
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.ACTIVITY_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.ACTIVITY_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.ACTIVITY_GROUP_TYPE_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.ACTIVITY_GROUP_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.UI_RKT_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.UI_RKT);
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>
