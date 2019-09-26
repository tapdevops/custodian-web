<?php
/*
=========================================================================================================================
Project				: 	Budgeting & Planning System
Versi				: 	2.0.0
Deskripsi			: 	View untuk Menampilkan Mapping Aktivitas di Report
Function 			:	
Disusun Oleh		: 	IT Enterprise Solution - PT Triputra Agro Persada
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/08/2014
Update Terakhir		:	08/08/2014
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
			<legend>MAPPING AKTIVITAS UNTUK REPORT</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">		
						&nbsp;
					</td>
					<td width="50%" align="right">
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
			<th>REPORT</th>
			<th>MAPPING 1</th>
			<th>MAPPING 2</th>
			<th>MAPPING 3</th>
			<th>MAPPING 4</th>
			<th>KODE AKTIVITAS</th>
			<th>DESKRIPSI AKTIVITAS</th>
			<th>COST ELEMENT</th>
		</tr>
		<tr class='column_number'>
			<th>1</th>
			<th>2</th>
			<th>3</th>
			<th>4</th>
			<th>5</th>
			<th>6</th>
			<th>7</th>
			<th>8</th>
		</tr>
		</thead>
		<tbody name='data' id='data'>
				<tr style="display:none">
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:150px;'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:200px;'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:200px;'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:200px;'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:200px;'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:50px;'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:250px;'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:75px;'/></td>
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
	//BUTTON ACTION
    $("#btn_find").click(function() {
        page_num = (page_num) ? page_num : 1;
        clearDetail();
        getData();
    });
	$("#btn_refresh").click(function() {
		location.reload();
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
});

function getData(){
    $("#page_num").val(page_num);	
    //
    $.ajax({
        type    : "post",
        url     : "mapping-activity-report/list",
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
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.TIPE);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.GROUP01_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.GROUP02_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.GROUP03_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.GROUP04_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.ACTIVITY_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text08_]").val(row.ACTIVITY_DESC);
                    $("#data tr:eq(" + index + ") input[id^=text09_]").val(row.COST_ELEMENT);
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>
