<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Report RKT CAPEX
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	08/07/2013
Update Terakhir		:	08/07/2013
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
			<legend>REPORT RKT CAPEX</legend>
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
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header' id='data_header'>
			<thead>
				<tr>
					<th rowspan='2'>PERIODE<BR>BUDGET</th>
					<th rowspan='2'>BUSINESS<BR>AREA</th>
					<th rowspan='2'>GROUP CAPEX</th>
					<th rowspan='2'>DESKRIPSI GROUP CAPEX</th>
					<th rowspan='2'>KODE ASET</th>
					<th rowspan='2'>DESKRIPSI ASET</th>
					<th rowspan='2'>DETAIL SPESIFIKASI</th>
					<th colspan='13'>DISTRIBUSI BIAYA INVENTASI <span class='period_before'></span></th>
				</tr>
				<tr>
					<th>YEAR</th>
					<th>JAN</th>
					<th>FEB</th>
					<th>MAR</th>
					<th>APR</th>
					<th>MEI</th>
					<th>JUN</th>
					<th>JUL</th>
					<th>AGS</th>
					<th>SEP</th>
					<th>OKT</th>
					<th>NOV</th>
					<th>DES</th>
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
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr style="display:none" class='rowdata'>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px' value='2'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:50px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:100px' value='4'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:200px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:100px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:300px' value='7'/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:300px' value='8'/></td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:120px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:120px' value='10'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:120px' value='11'/></td>
					<td><input type="text" name="text12[]" id="text12_" readonly="readonly" style='width:120px' value='12'/></td>
					<td><input type="text" name="text13[]" id="text13_" readonly="readonly" style='width:120px' value='13'/></td>
					<td><input type="text" name="text14[]" id="text14_" readonly="readonly" style='width:120px' value='14'/></td>
					<td><input type="text" name="text15[]" id="text15_" readonly="readonly" style='width:120px' value='15'/></td>
					<td><input type="text" name="text16[]" id="text16_" readonly="readonly" style='width:120px' value='16'/></td>
					<td><input type="text" name="text17[]" id="text17_" readonly="readonly" style='width:120px' value='17'/></td>
					<td><input type="text" name="text18[]" id="text18_" readonly="readonly" style='width:120px' value='18'/></td>
					<td><input type="text" name="text19[]" id="text19_" readonly="readonly" style='width:120px' value='19'/></td>
					<td><input type="text" name="text20[]" id="text20_" readonly="readonly" style='width:120px' value='20'/></td>
					<td><input type="text" name="text21[]" id="text21_" readonly="readonly" style='width:120px' value='21'/></td>
				</tr>
				<!-- SUB TOTAL -->
				<tr style='display:none;'>
					<td colspan='7' class='subtotal'>SUB TOTAL</td>
					<td><input type="text" name="text09[]" id="text09_" readonly="readonly" style='width:120px' value='9'/></td>
					<td><input type="text" name="text10[]" id="text10_" readonly="readonly" style='width:120px' value='10'/></td>
					<td><input type="text" name="text11[]" id="text11_" readonly="readonly" style='width:120px' value='11'/></td>
					<td><input type="text" name="text12[]" id="text12_" readonly="readonly" style='width:120px' value='12'/></td>
					<td><input type="text" name="text13[]" id="text13_" readonly="readonly" style='width:120px' value='13'/></td>
					<td><input type="text" name="text14[]" id="text14_" readonly="readonly" style='width:120px' value='14'/></td>
					<td><input type="text" name="text15[]" id="text15_" readonly="readonly" style='width:120px' value='15'/></td>
					<td><input type="text" name="text16[]" id="text16_" readonly="readonly" style='width:120px' value='16'/></td>
					<td><input type="text" name="text17[]" id="text17_" readonly="readonly" style='width:120px' value='17'/></td>
					<td><input type="text" name="text18[]" id="text18_" readonly="readonly" style='width:120px' value='18'/></td>
					<td><input type="text" name="text19[]" id="text19_" readonly="readonly" style='width:120px' value='19'/></td>
					<td><input type="text" name="text20[]" id="text20_" readonly="readonly" style='width:120px' value='20'/></td>
					<td><input type="text" name="text21[]" id="text21_" readonly="readonly" style='width:120px' value='21'/></td>
				</tr>
				<!-- END OF SUB TOTAL -->	
			</tbody>
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='7' class='grandtotal'>GRAND TOTAL</td>
					<td><input type="text" name="total09" id="total09" readonly="readonly" style='width:120px' value='9'/></td>
					<td><input type="text" name="total10" id="total10" readonly="readonly" style='width:120px' value='10'/></td>
					<td><input type="text" name="total11" id="total11" readonly="readonly" style='width:120px' value='11'/></td>
					<td><input type="text" name="total12" id="total12" readonly="readonly" style='width:120px' value='12'/></td>
					<td><input type="text" name="total13" id="total13" readonly="readonly" style='width:120px' value='13'/></td>
					<td><input type="text" name="total14" id="total14" readonly="readonly" style='width:120px' value='14'/></td>
					<td><input type="text" name="total15" id="total15" readonly="readonly" style='width:120px' value='15'/></td>
					<td><input type="text" name="total16" id="total16" readonly="readonly" style='width:120px' value='16'/></td>
					<td><input type="text" name="total17" id="total17" readonly="readonly" style='width:120px' value='17'/></td>
					<td><input type="text" name="total18" id="total18" readonly="readonly" style='width:120px' value='18'/></td>
					<td><input type="text" name="total19" id="total19" readonly="readonly" style='width:120px' value='19'/></td>
					<td><input type="text" name="total20" id="total20" readonly="readonly" style='width:120px' value='20'/></td>
					<td><input type="text" name="total21" id="total21" readonly="readonly" style='width:120px' value='21'/></td>
				</tr>
			</tfoot>
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
	//set nama kolom yang mengandung tahun
	$(".period_budget").html($("#budgetperiod").val());
	$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
	
	//BUTTON ACTION
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		//DEKLARASI VARIABEL
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			
			//set nama kolom yang mengandung tahun
			$(".period_budget").html($("#budgetperiod").val());
			$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
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
		
		/*REMARK BY DONI
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
		*/
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-report-rkt-capex/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
		//}
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
});

function getData(){
    $("#page_num").val(page_num);
	var coa = '';
	var bacode = '';
	var showSubTotal = 0;
	var countData = parseInt(0);
	var GRANDTOTAL_countData = parseInt(0);
	
	//sub total
	var SUB_TEXT08 = parseFloat(0);
	var SUB_TEXT09 = parseFloat(0);
	var SUB_TEXT10 = parseFloat(0);
	var SUB_TEXT11 = parseFloat(0);
	var SUB_TEXT12 = parseFloat(0);
	var SUB_TEXT13 = parseFloat(0);
	var SUB_TEXT14 = parseFloat(0);
	var SUB_TEXT15 = parseFloat(0);
	var SUB_TEXT16 = parseFloat(0);
	var SUB_TEXT17 = parseFloat(0);
	var SUB_TEXT18 = parseFloat(0);
	var SUB_TEXT19 = parseFloat(0);
	var SUB_TEXT20 = parseFloat(0);
	var SUB_TEXT21 = parseFloat(0);
	
	//grand total
	var SUM_TEXT08 = parseFloat(0);
	var SUM_TEXT09 = parseFloat(0);
	var SUM_TEXT10 = parseFloat(0);
	var SUM_TEXT11 = parseFloat(0);
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
    //
    $.ajax({
        type    : "post",
        url     : "report-rkt-capex/list",
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
					//tampilkan subtotal
					if (( coa != "" ) && (( coa != row.COA_CODE ) || ( bacode != row.BA_CODE ))) {
						var lastTr = ($("#data tr").length-1);
						var sub_total = $("#data tr:eq(1)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
						var index = ($("#data tr").length -1);	
						
						$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text02_]").val("");;
						$("#data tr:eq(" + index + ") input[id^=text03_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
						
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(SUB_TEXT09, 2));
						$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(SUB_TEXT10, 2));
						$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(SUB_TEXT11, 2));
						$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(SUB_TEXT12, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(SUB_TEXT13, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(SUB_TEXT14, 2));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(SUB_TEXT15, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(SUB_TEXT16, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(SUB_TEXT17, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(SUB_TEXT18, 2));
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(SUB_TEXT19, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(SUB_TEXT20, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(SUB_TEXT21, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ")").removeAttr("style");					
					
						//reset data
						SUB_TEXT08 = parseFloat(0);
						SUB_TEXT09 = parseFloat(0);
						SUB_TEXT10 = parseFloat(0);
						SUB_TEXT11 = parseFloat(0);
						SUB_TEXT12 = parseFloat(0);
						SUB_TEXT13 = parseFloat(0);
						SUB_TEXT14 = parseFloat(0);
						SUB_TEXT15 = parseFloat(0);
						SUB_TEXT16 = parseFloat(0);
						SUB_TEXT17 = parseFloat(0);
						SUB_TEXT18 = parseFloat(0);
						SUB_TEXT19 = parseFloat(0);
						SUB_TEXT20 = parseFloat(0);
						SUB_TEXT21 = parseFloat(0);
						showSubTotal = 1;
					}else{
						showSubTotal = 0;
					}
					
					var lastTr = ($("#data tr").length-1);	
					var tr = $("#data tr:eq(0)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
                    var index = ($("#data tr").length -1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});				
					 
					$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val(row.COA_CODE);
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val(row.COA_DESC);
					$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.ASSET_CODE);
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.ASSET_DESC);
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.DETAIL_SPESIFICATION);
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.DIS_BIAYA_TOTAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.DIS_BIAYA_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.DIS_BIAYA_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.DIS_BIAYA_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.DIS_BIAYA_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.DIS_BIAYA_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.DIS_BIAYA_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.DIS_BIAYA_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.DIS_BIAYA_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.DIS_BIAYA_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.DIS_BIAYA_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.DIS_BIAYA_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.DIS_BIAYA_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();

					//perhitungan SUB TOTAL
					SUB_TEXT09 = (row.DIS_BIAYA_TOTAL) ? SUB_TEXT09 + parseFloat(row.DIS_BIAYA_TOTAL) : SUB_TEXT09;
					SUB_TEXT10 = (row.DIS_BIAYA_JAN) ? SUB_TEXT10 + parseFloat(row.DIS_BIAYA_JAN) : SUB_TEXT10;
					SUB_TEXT11 = (row.DIS_BIAYA_FEB) ? SUB_TEXT11 + parseFloat(row.DIS_BIAYA_FEB) : SUB_TEXT11;
					SUB_TEXT12 = (row.DIS_BIAYA_MAR) ? SUB_TEXT12 + parseFloat(row.DIS_BIAYA_MAR) : SUB_TEXT12;
					SUB_TEXT13 = (row.DIS_BIAYA_APR) ? SUB_TEXT13 + parseFloat(row.DIS_BIAYA_APR) : SUB_TEXT13;
					SUB_TEXT14 = (row.DIS_BIAYA_MAY) ? SUB_TEXT14 + parseFloat(row.DIS_BIAYA_MAY) : SUB_TEXT14;
					SUB_TEXT15 = (row.DIS_BIAYA_JUN) ? SUB_TEXT15 + parseFloat(row.DIS_BIAYA_JUN) : SUB_TEXT15;
					SUB_TEXT16 = (row.DIS_BIAYA_JUL) ? SUB_TEXT16 + parseFloat(row.DIS_BIAYA_JUL) : SUB_TEXT16;
					SUB_TEXT17 = (row.DIS_BIAYA_AUG) ? SUB_TEXT17 + parseFloat(row.DIS_BIAYA_AUG) : SUB_TEXT17;
					SUB_TEXT18 = (row.DIS_BIAYA_SEP) ? SUB_TEXT18 + parseFloat(row.DIS_BIAYA_SEP) : SUB_TEXT18;
					SUB_TEXT19 = (row.DIS_BIAYA_OCT) ? SUB_TEXT19 + parseFloat(row.DIS_BIAYA_OCT) : SUB_TEXT19;
					SUB_TEXT20 = (row.DIS_BIAYA_NOV) ? SUB_TEXT20 + parseFloat(row.DIS_BIAYA_NOV) : SUB_TEXT20;
					SUB_TEXT21 = (row.DIS_BIAYA_DEC) ? SUB_TEXT21 + parseFloat(row.DIS_BIAYA_DEC) : SUB_TEXT21;
					countData += parseInt(1);
					
					//perhitungan summary
					SUM_TEXT09 = (row.DIS_BIAYA_TOTAL) ? SUM_TEXT09 + parseFloat(row.DIS_BIAYA_TOTAL) : SUM_TEXT09;
					SUM_TEXT10 = (row.DIS_BIAYA_JAN) ? SUM_TEXT10 + parseFloat(row.DIS_BIAYA_JAN) : SUM_TEXT10;
					SUM_TEXT11 = (row.DIS_BIAYA_FEB) ? SUM_TEXT11 + parseFloat(row.DIS_BIAYA_FEB) : SUM_TEXT11;
					SUM_TEXT12 = (row.DIS_BIAYA_MAR) ? SUM_TEXT12 + parseFloat(row.DIS_BIAYA_MAR) : SUM_TEXT12;
					SUM_TEXT13 = (row.DIS_BIAYA_APR) ? SUM_TEXT13 + parseFloat(row.DIS_BIAYA_APR) : SUM_TEXT13;
					SUM_TEXT14 = (row.DIS_BIAYA_MAY) ? SUM_TEXT14 + parseFloat(row.DIS_BIAYA_MAY) : SUM_TEXT14;
					SUM_TEXT15 = (row.DIS_BIAYA_JUN) ? SUM_TEXT15 + parseFloat(row.DIS_BIAYA_JUN) : SUM_TEXT15;
					SUM_TEXT16 = (row.DIS_BIAYA_JUL) ? SUM_TEXT16 + parseFloat(row.DIS_BIAYA_JUL) : SUM_TEXT16;
					SUM_TEXT17 = (row.DIS_BIAYA_AUG) ? SUM_TEXT17 + parseFloat(row.DIS_BIAYA_AUG) : SUM_TEXT17;
					SUM_TEXT18 = (row.DIS_BIAYA_SEP) ? SUM_TEXT18 + parseFloat(row.DIS_BIAYA_SEP) : SUM_TEXT18;
					SUM_TEXT19 = (row.DIS_BIAYA_OCT) ? SUM_TEXT19 + parseFloat(row.DIS_BIAYA_OCT) : SUM_TEXT19;
					SUM_TEXT20 = (row.DIS_BIAYA_NOV) ? SUM_TEXT20 + parseFloat(row.DIS_BIAYA_NOV) : SUM_TEXT20;
					SUM_TEXT21 = (row.DIS_BIAYA_DEC) ? SUM_TEXT21 + parseFloat(row.DIS_BIAYA_DEC) : SUM_TEXT21;
					GRANDTOTAL_countData += parseInt(1);
					
					coa = row.COA_CODE;
					bacode = row.BA_CODE;
                });
				if(showSubTotal == 0){
					//tampilkan subtotal terakhir
					var lastTr = ($("#data tr").length-1);
					var sub_total = $("#data tr:eq(1)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
					var index = ($("#data tr").length -1);	
					
					$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
                    $("#data tr:eq(" + index + ") input[id^=text02_]").val("");;
                    $("#data tr:eq(" + index + ") input[id^=text03_]").val("");
                    $("#data tr:eq(" + index + ") input[id^=text04_]").val("");
                    $("#data tr:eq(" + index + ") input[id^=text05_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
					
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(SUB_TEXT09, 2));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(SUB_TEXT10, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(SUB_TEXT11, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(SUB_TEXT12, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(SUB_TEXT13, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(SUB_TEXT14, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(SUB_TEXT15, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(SUB_TEXT16, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(SUB_TEXT17, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(SUB_TEXT18, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(SUB_TEXT19, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(SUB_TEXT20, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(SUB_TEXT21, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ")").removeAttr("style");	
				}
				//summary
				$("#total09").val(accounting.formatNumber(SUM_TEXT09, 2));
				$("#total09").addClass("number grandtotal_text");
				$("#total10").val(accounting.formatNumber(SUM_TEXT10, 2));
				$("#total10").addClass("number grandtotal_text");
				$("#total11").val(accounting.formatNumber(SUM_TEXT11, 2));
				$("#total11").addClass("number grandtotal_text");
				$("#total12").val(accounting.formatNumber(SUM_TEXT12, 2));
				$("#total12").addClass("number grandtotal_text");
				$("#total13").val(accounting.formatNumber(SUM_TEXT13, 2));
				$("#total13").addClass("number grandtotal_text");
				$("#total14").val(accounting.formatNumber(SUM_TEXT14, 2));
				$("#total14").addClass("number grandtotal_text");
				$("#total15").val(accounting.formatNumber(SUM_TEXT15, 2));
				$("#total15").addClass("number grandtotal_text");
				$("#total16").val(accounting.formatNumber(SUM_TEXT16, 2));
				$("#total16").addClass("number grandtotal_text");
				$("#total17").val(accounting.formatNumber(SUM_TEXT17, 2));
				$("#total17").addClass("number grandtotal_text");
				$("#total18").val(accounting.formatNumber(SUM_TEXT18, 2));
				$("#total18").addClass("number grandtotal_text");
				$("#total19").val(accounting.formatNumber(SUM_TEXT19, 2));
				$("#total19").addClass("number grandtotal_text");
				$("#total20").val(accounting.formatNumber(SUM_TEXT20, 2));
				$("#total20").addClass("number grandtotal_text");
				$("#total21").val(accounting.formatNumber(SUM_TEXT21, 2));
				$("#total21").addClass("number grandtotal_text");
				
				$("#tfoot").removeAttr("style");
            }else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
