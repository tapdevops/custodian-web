<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Report Norma Perkerasan Jalan
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Bayu Dwijayanto
Dibuat Tanggal		: 	27/06/2013
Update Terakhir		:	27/06/2013
Revisi				:	
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
			<legend>HARGA PERKERASAN JALAN</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">	
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						&nbsp;
					</td>
				</tr>
			</table>
			<!--div id='scrollarea'-->
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>KODE<BR>AKTIFITAS</th>
					<th>DESKRIPSI</th>
					<th>KM JARAK</th><th>AVG JARAK</th><th>PP JARAK</th>
					<th>MATERIAL<BR>M3</th>
					<th>TRIP/KM</th>
					<th>BIAYA<BR>MATERIAL</th>
					<th>KM DUMP TRUCK</th><th>RP DUMP TRUCK</th>
					<th>HM EXCAVATOR</th><th>RP EXCAVATOR</th>
					<th>HM COMPACTOR</th><th>RP COMPACTOR</th>
					<th>HM GRADER</th><th>RP GRADER</th>
					<th>INTERNAL<BR>RP</th>
					<th>% KEUNTUNGAN</th><th>RP KEUNTUNGAN</th>
					<th>EXTERNAL<BR>RP</th>
				</tr>
				<!--tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<th>6</th>
					<th>7</th>
					<th>8</th>
					<th>9</th>
					<th>10</th>
					<th>11</th>
					<th>12</th>
					<th>13</th>
					<th>14</th>
					<th>15</th>
					<th>16</th>
					<th>17</th>
					<th>18</th>
					<th>19</th>
					<th>20</th>
					<th>21</th>
					<th>22</th>
				</tr-->
			</thead>
			<tbody name='data' id='data'>
				<tr style="display:none">
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='200px'><input type="text" name="text05[]" id="text05_" readonly="readonly" value='5'/></td>
					
					<td width='50px'><input type="text" name="text06[]" id="text06_" readonly="readonly" value='6'/></td>
					<td width='50px'><input type="text" name="text07[]" id="text07_" readonly="readonly" value='7'/></td>
					<td width='50px'><input type="text" name="text08[]" id="text08_" readonly="readonly" value='8'/></td>
					<td width='100px'><input type="text" name="text09[]" id="text09_" readonly="readonly" value='9'/></td>
					<td width='100px'><input type="text" name="text10[]" id="text10_" readonly="readonly" value='10'/></td>
					<td width='150px'><input type="text" name="text11[]" id="text11_" readonly="readonly" value='11'/></td>
					<td width='100px'><input type="text" name="text12[]" id="text12_" readonly="readonly" value='12'/></td>
					<td width='100px'><input type="text" name="text13[]" id="text13_" readonly="readonly" value='13'/></td>
					<td width='100px'><input type="text" name="text14[]" id="text14_" readonly="readonly" value='14'/></td>
					<td width='100px'><input type="text" name="text15[]" id="text15_" readonly="readonly" value='15'/></td>
					<td width='100px'><input type="text" name="text16[]" id="text16_" readonly="readonly" value='16'/></td>
					<td width='100px'><input type="text" name="text17[]" id="text17_" readonly="readonly" value='17'/></td>
					<td width='100px'><input type="text" name="text18[]" id="text18_" readonly="readonly" value='18'/></td>
					<td width='100px'><input type="text" name="text19[]" id="text19_" readonly="readonly" value='19'/></td>
					<td width='100px'><input type="text" name="text20[]" id="text20_" readonly="readonly" value='20'/></td>
					<td width='100px'><input type="text" name="text21[]" id="text21_" readonly="readonly" value='21'/></td>
					<td width='100px'><input type="text" name="text22[]" id="text22_" readonly="readonly" value='22'/></td>
					<td width='100px'><input type="text" name="text23[]" id="text23_" readonly="readonly" value='23'/></td>
				</tr>
			</tbody>
			</table>
			<!--/div-->
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
/*var wscrollarea = window.innerWidth - 110;
document.getElementById("scrollarea").style.width = wscrollarea + "px";*/

var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       1240,   // required
		height:      400,   // required
		numFrozen:   4,     // optional
		frozenWidth: 230,   // optional
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			//jika periode budget yang dipilih <> periode budget aktif, maka tidak dapat melakukan proses perhitungan
			if (budgetperiod != current_budgetperiod) {
				$("#btn_save").hide();
			}else{
				$("#btn_save").show();
			}
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-report-norma-perkerasan-jalan/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	
	//SEARCH FREE TEXT
	$("#search").live("keydown", function(event) {
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
});

function getData(){
    $("#page_num").val(page_num);	
    //
    $.ajax({
        type    : "post",
        url     : "report-norma-perkerasan-jalan/list",
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
					
					//left freeze panes row
					$("#data_freeze tr:eq(" + index + ") input[id^=tChange_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.ACTIVITY_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.DESCRIPTION);
					$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
					
					//right freeze panes
					
                    var tr = $("#data tr:eq(0)").clone();
                    $("#data").append(tr);
                    var index = ($("#data tr").length - 1);
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});
					$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.PARAMETER_VALUE);
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.JARAK_AVG, 2));
					$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.JARAK_PP, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.MATERIAL_QTY, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.TRIP_MATERIAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.BIAYA_MATERIAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.DT_TRIP, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.DT_PRICE, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.EXCAV_HM, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.EXCAV_PRICE, 0));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.COMPACTOR_HM, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.COMPACTOR_PRICE, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
                    $("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.GRADER_HM, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.GRADER_PRICE, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.INTERNAL_PRICE, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.EXTERNAL_PERCENT, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.EXTERNAL_BENEFIT, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.EXTERNAL_PRICE, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");
					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
                });
            }
        }
    });
}
</script>