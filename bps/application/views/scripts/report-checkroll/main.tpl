<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Report Checkroll
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	13/06/2013
Update Terakhir		:	13/06/2013
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
$this->headScript()->appendFile('js/freezepanes/jquery.freezetablecolumns.1.1.js');
$periodbudget = $this->period;
$periodaktual = $periodbudget - 1;
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
			<legend>CHECKROLL DAN PK UMUM</legend>
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
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>JOB CODE</th>
					<th>GROUP CHECKROLL<BR>DESC</th>
					<th>DESKRIPSI</th>
					<th>EMPLOYEE<BR>STATUS</th>
					<th>KENAIKAN GP<BR>(%)</th>
					<th>GP/BULAN<BR><span class='period_budget'></span></th>
					<th>MPP<BR><span class='period_budget'></span></th>
					<th>TOTAL GP<BR>ALL EMP</th>
					<th>ASTEK</th>
					<th>CATU</th>
					<th>JABATAN</th>
					<th>KEHADIRAN</th>
					<th>LAINNYA</th>
					<th>PPH21</th>
					<th>TOTAL GP + TUNJANGAN<BR>/ EMP / BULAN</th>
					<th>RP / HK</th>
					<th>BONUS</th>
					<th>HHR</th>
					<th>OBAT</th>
					<th>THR</th>
					<th>TOTAL PK UMUM</th>
					<th>YEAR <span class='period_budget'></span></th>
					<th>JAN <span class='period_budget'></span></th>
					<th>FEB <span class='period_budget'></span></th>
					<th>MAR <span class='period_budget'></span></th>
					<th>APR <span class='period_budget'></span></th>
					<th>MEI <span class='period_budget'></span></th>
					<th>JUN <span class='period_budget'></span></th>
					<th>JUL <span class='period_budget'></span></th>
					<th>AGS <span class='period_budget'></span></th>
					<th>SEP <span class='period_budget'></span></th>
					<th>OKT <span class='period_budget'></span></th>
					<th>NOV <span class='period_budget'></span></th>
					<th>DES <span class='period_budget'></span></th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='120px'><input type="text" name="text05[]" id="text05_" readonly="readonly" value='5'/></td>
					<td width='300px'><input type="text" name="text06[]" id="text06_" readonly="readonly" value='6'/></td>
					<td width='50px'><input type="text" name="text07[]" id="text07_" readonly="readonly" value='7'/></td>
					<td width='100px'><input type="text" name="text37[]" id="text37_" readonly="readonly" value='37'/></td>
					<td width='120px'><input type="text" name="text08[]" id="text08_" readonly="readonly" value='8'/></td>
					<td width='50px'><input type="text" name="text09[]" id="text09_" readonly="readonly" value='9'/></td>
					<td width='120px'><input type="text" name="text10[]" id="text10_" readonly="readonly" value='10'/></td>
					<td width='120px'><input type="text" name="text11[]" id="text11_" readonly="readonly" value='11'/></td>
					<td width='120px'><input type="text" name="text12[]" id="text12_" readonly="readonly" value='12'/></td>
					<td width='120px'><input type="text" name="text13[]" id="text13_" readonly="readonly" value='13'/></td>
					<td width='120px'><input type="text" name="text14[]" id="text14_" readonly="readonly" value='14'/></td>
					<td width='120px'><input type="text" name="text15[]" id="text15_" readonly="readonly" value='15'/></td>
					<td width='120px'><input type="text" name="text16[]" id="text16_" readonly="readonly" value='16'/></td>
					<td width='120px'><input type="text" name="text17[]" id="text17_" readonly="readonly" value='17'/></td>
					<td width='120px'><input type="text" name="text18[]" id="text18_" readonly="readonly" value='18'/></td><!--RP/HK-->
					<td width='120px'><input type="text" name="text19[]" id="text19_" readonly="readonly" value='19'/></td>
					<td width='120px'><input type="text" name="text20[]" id="text20_" readonly="readonly" value='20'/></td>
					<td width='120px'><input type="text" name="text21[]" id="text21_" readonly="readonly" value='21'/></td>
					<td width='120px'><input type="text" name="text22[]" id="text22_" readonly="readonly" value='22'/></td>
					<td width='120px'><input type="text" name="text23[]" id="text23_" readonly="readonly" value='23'/></td>
					<td width='120px'><input type="text" name="text24[]" id="text24_" readonly="readonly" value='24'/></td>
					<td width='120px'><input type="text" name="text25[]" id="text25_" readonly="readonly" value='25'/></td>
					<td width='120px'><input type="text" name="text26[]" id="text26_" readonly="readonly" value='26'/></td>
					<td width='120px'><input type="text" name="text27[]" id="text27_" readonly="readonly" value='27'/></td>
					<td width='120px'><input type="text" name="text28[]" id="text28_" readonly="readonly" value='28'/></td>
					<td width='120px'><input type="text" name="text29[]" id="text29_" readonly="readonly" value='29'/></td>
					<td width='120px'><input type="text" name="text30[]" id="text30_" readonly="readonly" value='30'/></td>
					<td width='120px'><input type="text" name="text31[]" id="text31_" readonly="readonly" value='31'/></td>
					<td width='120px'><input type="text" name="text32[]" id="text32_" readonly="readonly" value='32'/></td>
					<td width='120px'><input type="text" name="text33[]" id="text33_" readonly="readonly" value='33'/></td>
					<td width='120px'><input type="text" name="text34[]" id="text34_" readonly="readonly" value='34'/></td>
					<td width='120px'><input type="text" name="text35[]" id="text35_" readonly="readonly" value='35'/></td>
					<td width='120px'><input type="text" name="text36[]" id="text36_" readonly="readonly" value='36'/></td>
				</tr>
			</tbody>
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
	
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       980,   // required
		height:      400,   // required
		numFrozen:   6,     // optional
		frozenWidth: 470//,   // optional
		//clearWidths: true,  // optional
	});//freezeTableColumns
	
	//BUTTON ACTION
	$("#btn_find").click(function() {
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
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			
			//set nama kolom yang mengandung tahun
			$(".period_budget").html($("#budgetperiod").val());
			$(".period_before").html(parseInt($("#budgetperiod").val()) - 1);
	
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-report-checkroll/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
	var jobcode = '';
	var bacode = '';
	var showSubTotal = 0;
	var countData = parseInt(0);
	var GRANDTOTAL_countData = parseInt(0);
	
	var total_rp_hk = parseFloat(0);
	var total_mpp = parseFloat(0);
	var grand_total_rp_hk = parseFloat(0);
	var grand_total_mpp = parseFloat(0);
	
	//sub total
	var GP_INFLASI = parseFloat(0);
	var MPP_PERIOD_BUDGET = parseFloat(0);
	var TOTAL_GP_MPP = parseFloat(0);
	var PPH_21 = parseFloat(0);
	var ASTEK = parseFloat(0);
	var JABATAN = parseFloat(0);
	var KEHADIRAN = parseFloat(0);
	var LAINNYA = parseFloat(0);
	var CATU = parseFloat(0);
	var TOTAL_GAJI_TUNJANGAN = parseFloat(0);
	var RP_HK_PERBULAN = parseFloat(0);
	var OBAT = parseFloat(0);
	var THR = parseFloat(0);
	var HHR = parseFloat(0);
	var BONUS = parseFloat(0);
	var TOTAL_TUNJANGAN_PK_UMUM = parseFloat(0);
	var YEAR = parseFloat(0);
	var DIS_JAN = parseFloat(0);
	var DIS_FEB = parseFloat(0);
	var DIS_MAR = parseFloat(0);
	var DIS_APR = parseFloat(0);
	var DIS_MAY = parseFloat(0);
	var DIS_JUN = parseFloat(0);
	var DIS_JUL = parseFloat(0);
	var DIS_AUG = parseFloat(0);
	var DIS_SEP = parseFloat(0);
	var DIS_OCT = parseFloat(0);
	var DIS_NOV = parseFloat(0);
	var DIS_DEC = parseFloat(0);
	
	//grand total
	var GRANDTOTAL_GP_INFLASI = parseFloat(0);
	var GRANDTOTAL_MPP_PERIOD_BUDGET = parseFloat(0);
	var GRANDTOTAL_TOTAL_GP_MPP = parseFloat(0);
	var GRANDTOTAL_PPH_21 = parseFloat(0);
	var GRANDTOTAL_ASTEK = parseFloat(0);
	var GRANDTOTAL_JABATAN = parseFloat(0);
	var GRANDTOTAL_KEHADIRAN = parseFloat(0);
	var GRANDTOTAL_LAINNYA = parseFloat(0);
	var GRANDTOTAL_CATU = parseFloat(0);
	var GRANDTOTAL_TOTAL_GAJI_TUNJANGAN = parseFloat(0);
	var GRANDTOTAL_RP_HK_PERBULAN = parseFloat(0);
	var GRANDTOTAL_OBAT = parseFloat(0);
	var GRANDTOTAL_THR = parseFloat(0);
	var GRANDTOTAL_HHR = parseFloat(0);
	var GRANDTOTAL_BONUS = parseFloat(0);
	var GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM = parseFloat(0);
	var GRANDTOTAL_YEAR = parseFloat(0);
	var GRANDTOTAL_DIS_JAN = parseFloat(0);
	var GRANDTOTAL_DIS_FEB = parseFloat(0);
	var GRANDTOTAL_DIS_MAR = parseFloat(0);
	var GRANDTOTAL_DIS_APR = parseFloat(0);
	var GRANDTOTAL_DIS_MAY = parseFloat(0);
	var GRANDTOTAL_DIS_JUN = parseFloat(0);
	var GRANDTOTAL_DIS_JUL = parseFloat(0);
	var GRANDTOTAL_DIS_AUG = parseFloat(0);
	var GRANDTOTAL_DIS_SEP = parseFloat(0);
	var GRANDTOTAL_DIS_OCT = parseFloat(0);
	var GRANDTOTAL_DIS_NOV = parseFloat(0);
	var GRANDTOTAL_DIS_DEC = parseFloat(0);
    //
    $.ajax({
        type    : "post",
        url     : "report-checkroll/list",
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
					if ((jobcode != "") && (( jobcode != row.JOB_CODE ) || ( bacode != row.BA_CODE ))) {
						var lastTr = ($("#data_freeze tr").length-1);
						var sub_total = $("#data_freeze tr:eq(1)").clone().insertAfter($("#data_freeze tr:eq("+lastTr+")"));
						var index = ($("#data_freeze tr").length -1);	
						$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").addClass("subtotal_text");
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
						$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").addClass("subtotal_text");
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(jobcode);
						$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").addClass("subtotal_text");
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(jobcode_grup);
						$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").addClass("subtotal_text");
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(jobcode_desc);
						$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("subtotal_text");
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val("");
						$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").addClass("number subtotal_text");
						$("#data_freeze tr:eq(" + index + ")").removeAttr("style");					
						
						var lastTr = ($("#data tr").length-1);
						var sub_total = $("#data tr:eq(1)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
						var index = ($("#data tr").length -1);	
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(GP_INFLASI, 2));
						$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(MPP_PERIOD_BUDGET, 0));
						$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(TOTAL_GP_MPP, 2));
						$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(ASTEK, 2));
						$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(CATU, 2));
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(JABATAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(KEHADIRAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(LAINNYA, 2));
						$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(PPH_21, 2));
						$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(TOTAL_GAJI_TUNJANGAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(total_rp_hk/total_mpp, 2)); // sub-total
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(BONUS, 2));
						$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(HHR, 2));
						$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(OBAT, 2));
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(THR, 2));
						$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(TOTAL_TUNJANGAN_PK_UMUM, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(YEAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(DIS_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(DIS_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(DIS_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(DIS_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(DIS_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(DIS_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(DIS_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(DIS_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(DIS_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(DIS_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(DIS_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(DIS_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number subtotal_text");
						$("#data tr:eq(" + index + ") input[id^=text37_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("subtotal_text");
						$("#data tr:eq(" + index + ")").removeAttr("style");					
					
						//reset data
						GP_INFLASI = parseFloat(0);
						MPP_PERIOD_BUDGET = parseFloat(0);
						TOTAL_GP_MPP = parseFloat(0);
						PPH_21 = parseFloat(0);
						ASTEK = parseFloat(0);
						JABATAN = parseFloat(0);
						KEHADIRAN = parseFloat(0);
						LAINNYA = parseFloat(0);
						CATU = parseFloat(0);
						TOTAL_GAJI_TUNJANGAN = parseFloat(0);
						RP_HK_PERBULAN = parseFloat(0);
						OBAT = parseFloat(0);
						THR = parseFloat(0);
						HHR = parseFloat(0);
						BONUS = parseFloat(0);
						TOTAL_TUNJANGAN_PK_UMUM = parseFloat(0);
						YEAR = parseFloat(0);
						DIS_JAN = parseFloat(0);
						DIS_FEB = parseFloat(0);
						DIS_MAR = parseFloat(0);
						DIS_APR = parseFloat(0);
						DIS_MAY = parseFloat(0);
						DIS_JUN = parseFloat(0);
						DIS_JUL = parseFloat(0);
						DIS_AUG = parseFloat(0);
						DIS_SEP = parseFloat(0);
						DIS_OCT = parseFloat(0);
						DIS_NOV = parseFloat(0);
						DIS_DEC = parseFloat(0);
						countData = parseInt(0);
						total_rp_hk = parseFloat(0);
						total_mpp = parseFloat(0);
						showSubTotal = 1;
					}else{
						showSubTotal = 0;
					}
					
					var lastTr = ($("#data_freeze tr").length-1);	
					var tr = $("#data_freeze tr:eq(0)").clone().insertAfter($("#data_freeze tr:eq("+lastTr+")"));
                    var index = ($("#data_freeze tr").length -1);					
					$("#data_freeze tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});					
					
					$("#data_freeze tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=btn01_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.JOB_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.GROUP_CHECKROLL_DESC);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(row.JOB_DESCRIPTION);
                    $("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val(row.EMPLOYEE_STATUS);
					$("#data_freeze tr:eq(" + index + ")").removeAttr("style");					
					
					var lastTr = ($("#data tr").length-1);	
					var tr = $("#data tr:eq(0)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
                    var index = ($("#data tr").length -1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});	
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(row.GP_INFLASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(row.MPP_PERIOD_BUDGET, 0));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(row.TOTAL_GP_MPP, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.ASTEK, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.CATU, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.JABATAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.KEHADIRAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.LAINNYA, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.PPH_21, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.TOTAL_GAJI_TUNJANGAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.RP_HK_PERBULAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.BONUS, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.HHR, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.OBAT, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.THR, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.TOTAL_TUNJANGAN_PK_UMUM, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.DIS_YEAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.DIS_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.DIS_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.DIS_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.DIS_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.DIS_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(row.DIS_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.DIS_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.DIS_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(row.DIS_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.DIS_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(row.DIS_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.DIS_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.PERCENT_INCREASE, 2));
					$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("number");
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();
					
					//subtotal
					GP_INFLASI = (row.GP_INFLASI) ? GP_INFLASI + parseFloat(row.GP_INFLASI) : GP_INFLASI;
					MPP_PERIOD_BUDGET = (row.MPP_PERIOD_BUDGET) ? MPP_PERIOD_BUDGET + parseFloat(row.MPP_PERIOD_BUDGET) : MPP_PERIOD_BUDGET;
					TOTAL_GP_MPP = (row.TOTAL_GP_MPP) ? TOTAL_GP_MPP + parseFloat(row.TOTAL_GP_MPP) : TOTAL_GP_MPP;
					PPH_21 = (row.PPH_21) ? PPH_21 + parseFloat(row.PPH_21) : PPH_21;
					ASTEK = (row.ASTEK) ? ASTEK + parseFloat(row.ASTEK) : ASTEK;
					JABATAN = (row.JABATAN) ? JABATAN + parseFloat(row.JABATAN) : JABATAN;
					KEHADIRAN = (row.KEHADIRAN) ? KEHADIRAN + parseFloat(row.KEHADIRAN) : KEHADIRAN;
					LAINNYA = (row.LAINNYA) ? LAINNYA + parseFloat(row.LAINNYA) : LAINNYA;
					CATU = (row.CATU) ? CATU + parseFloat(row.CATU) : CATU;
					TOTAL_GAJI_TUNJANGAN = (row.TOTAL_GAJI_TUNJANGAN) ? TOTAL_GAJI_TUNJANGAN + parseFloat(row.TOTAL_GAJI_TUNJANGAN) : TOTAL_GAJI_TUNJANGAN;
					RP_HK_PERBULAN = (row.RP_HK_PERBULAN) ? RP_HK_PERBULAN + parseFloat(row.RP_HK_PERBULAN) : RP_HK_PERBULAN;
					OBAT = (row.OBAT) ? OBAT + parseFloat(row.OBAT) : OBAT;
					THR = (row.THR) ? THR + parseFloat(row.THR) : THR;
					HHR = (row.HHR) ? HHR + parseFloat(row.HHR) : HHR;
					BONUS = (row.BONUS) ? BONUS + parseFloat(row.BONUS) : BONUS;
					TOTAL_TUNJANGAN_PK_UMUM = (row.TOTAL_TUNJANGAN_PK_UMUM) ? TOTAL_TUNJANGAN_PK_UMUM + parseFloat(row.TOTAL_TUNJANGAN_PK_UMUM) : TOTAL_TUNJANGAN_PK_UMUM;
					YEAR = (row.DIS_YEAR) ? YEAR + parseFloat(row.DIS_YEAR) : YEAR;
					DIS_JAN = (row.DIS_JAN) ? DIS_JAN + parseFloat(row.DIS_JAN) : DIS_JAN;
					DIS_FEB = (row.DIS_FEB) ? DIS_FEB + parseFloat(row.DIS_FEB) : DIS_FEB;
					DIS_MAR = (row.DIS_MAR) ? DIS_MAR + parseFloat(row.DIS_MAR) : DIS_MAR;
					DIS_APR = (row.DIS_APR) ? DIS_APR + parseFloat(row.DIS_APR) : DIS_APR;
					DIS_MAY = (row.DIS_MAY) ? DIS_MAY + parseFloat(row.DIS_MAY) : DIS_MAY;
					DIS_JUN = (row.DIS_JUN) ? DIS_JUN + parseFloat(row.DIS_JUN) : DIS_JUN;
					DIS_JUL = (row.DIS_JUL) ? DIS_JUL + parseFloat(row.DIS_JUL) : DIS_JUL;
					DIS_AUG = (row.DIS_AUG) ? DIS_AUG + parseFloat(row.DIS_AUG) : DIS_AUG;
					DIS_SEP = (row.DIS_SEP) ? DIS_SEP + parseFloat(row.DIS_SEP) : DIS_SEP;
					DIS_OCT = (row.DIS_OCT) ? DIS_OCT + parseFloat(row.DIS_OCT) : DIS_OCT;
					DIS_NOV = (row.DIS_NOV) ? DIS_NOV + parseFloat(row.DIS_NOV) : DIS_NOV;
					DIS_DEC = (row.DIS_DEC) ? DIS_DEC + parseFloat(row.DIS_DEC) : DIS_DEC;
					countData += parseInt(1);
					
					total_rp_hk = (row.RP_HK_PERBULAN) ? total_rp_hk + ( parseFloat(row.RP_HK_PERBULAN) * parseFloat(row.MPP_PERIOD_BUDGET) ) : total_rp_hk;
					total_mpp = (row.MPP_PERIOD_BUDGET) ? total_mpp + parseFloat(row.MPP_PERIOD_BUDGET) : total_mpp;
					
					//grand total
					GRANDTOTAL_GP_INFLASI = (row.GP_INFLASI) ? GRANDTOTAL_GP_INFLASI + parseFloat(row.GP_INFLASI) : GRANDTOTAL_GP_INFLASI;
					GRANDTOTAL_MPP_PERIOD_BUDGET = (row.MPP_PERIOD_BUDGET) ? GRANDTOTAL_MPP_PERIOD_BUDGET + parseFloat(row.MPP_PERIOD_BUDGET) : GRANDTOTAL_MPP_PERIOD_BUDGET;
					GRANDTOTAL_TOTAL_GP_MPP = (row.TOTAL_GP_MPP) ? GRANDTOTAL_TOTAL_GP_MPP + parseFloat(row.TOTAL_GP_MPP) : GRANDTOTAL_TOTAL_GP_MPP;
					GRANDTOTAL_PPH_21 = (row.PPH_21) ? GRANDTOTAL_PPH_21 + parseFloat(row.PPH_21) : GRANDTOTAL_PPH_21;
					GRANDTOTAL_ASTEK = (row.ASTEK) ? GRANDTOTAL_ASTEK + parseFloat(row.ASTEK) : GRANDTOTAL_ASTEK;
					GRANDTOTAL_JABATAN = (row.JABATAN) ? GRANDTOTAL_JABATAN + parseFloat(row.JABATAN) : GRANDTOTAL_JABATAN;
					GRANDTOTAL_KEHADIRAN = (row.KEHADIRAN) ? GRANDTOTAL_KEHADIRAN + parseFloat(row.KEHADIRAN) : GRANDTOTAL_KEHADIRAN;
					GRANDTOTAL_LAINNYA = (row.LAINNYA) ? GRANDTOTAL_LAINNYA + parseFloat(row.LAINNYA) : GRANDTOTAL_LAINNYA;
					GRANDTOTAL_CATU = (row.CATU) ? GRANDTOTAL_CATU + parseFloat(row.CATU) : GRANDTOTAL_CATU;
					GRANDTOTAL_TOTAL_GAJI_TUNJANGAN = (row.TOTAL_GAJI_TUNJANGAN) ? GRANDTOTAL_TOTAL_GAJI_TUNJANGAN + parseFloat(row.TOTAL_GAJI_TUNJANGAN) : GRANDTOTAL_TOTAL_GAJI_TUNJANGAN;
					GRANDTOTAL_RP_HK_PERBULAN = (row.RP_HK_PERBULAN) ? GRANDTOTAL_RP_HK_PERBULAN + parseFloat(row.RP_HK_PERBULAN) : GRANDTOTAL_RP_HK_PERBULAN;
					GRANDTOTAL_OBAT = (row.OBAT) ? GRANDTOTAL_OBAT + parseFloat(row.OBAT) : GRANDTOTAL_OBAT;
					GRANDTOTAL_THR = (row.THR) ? GRANDTOTAL_THR + parseFloat(row.THR) : GRANDTOTAL_THR;
					GRANDTOTAL_HHR = (row.HHR) ? GRANDTOTAL_HHR + parseFloat(row.HHR) : GRANDTOTAL_HHR;
					GRANDTOTAL_BONUS = (row.BONUS) ? GRANDTOTAL_BONUS + parseFloat(row.BONUS) : GRANDTOTAL_BONUS;
					GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM = (row.TOTAL_TUNJANGAN_PK_UMUM) ? GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM + parseFloat(row.TOTAL_TUNJANGAN_PK_UMUM) : GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM;
					GRANDTOTAL_YEAR = (row.DIS_YEAR) ? GRANDTOTAL_YEAR + parseFloat(row.DIS_YEAR) : GRANDTOTAL_YEAR;
					GRANDTOTAL_DIS_JAN = (row.DIS_JAN) ? GRANDTOTAL_DIS_JAN + parseFloat(row.DIS_JAN) : GRANDTOTAL_DIS_JAN;
					GRANDTOTAL_DIS_FEB = (row.DIS_FEB) ? GRANDTOTAL_DIS_FEB + parseFloat(row.DIS_FEB) : GRANDTOTAL_DIS_FEB;
					GRANDTOTAL_DIS_MAR = (row.DIS_MAR) ? GRANDTOTAL_DIS_MAR + parseFloat(row.DIS_MAR) : GRANDTOTAL_DIS_MAR;
					GRANDTOTAL_DIS_APR = (row.DIS_APR) ? GRANDTOTAL_DIS_APR + parseFloat(row.DIS_APR) : GRANDTOTAL_DIS_APR;
					GRANDTOTAL_DIS_MAY = (row.DIS_MAY) ? GRANDTOTAL_DIS_MAY + parseFloat(row.DIS_MAY) : GRANDTOTAL_DIS_MAY;
					GRANDTOTAL_DIS_JUN = (row.DIS_JUN) ? GRANDTOTAL_DIS_JUN + parseFloat(row.DIS_JUN) : GRANDTOTAL_DIS_JUN;
					GRANDTOTAL_DIS_JUL = (row.DIS_JUL) ? GRANDTOTAL_DIS_JUL + parseFloat(row.DIS_JUL) : GRANDTOTAL_DIS_JUL;
					GRANDTOTAL_DIS_AUG = (row.DIS_AUG) ? GRANDTOTAL_DIS_AUG + parseFloat(row.DIS_AUG) : GRANDTOTAL_DIS_AUG;
					GRANDTOTAL_DIS_SEP = (row.DIS_SEP) ? GRANDTOTAL_DIS_SEP + parseFloat(row.DIS_SEP) : GRANDTOTAL_DIS_SEP;
					GRANDTOTAL_DIS_OCT = (row.DIS_OCT) ? GRANDTOTAL_DIS_OCT + parseFloat(row.DIS_OCT) : GRANDTOTAL_DIS_OCT;
					GRANDTOTAL_DIS_NOV = (row.DIS_NOV) ? GRANDTOTAL_DIS_NOV + parseFloat(row.DIS_NOV) : GRANDTOTAL_DIS_NOV;
					GRANDTOTAL_DIS_DEC = (row.DIS_DEC) ? GRANDTOTAL_DIS_DEC + parseFloat(row.DIS_DEC) : GRANDTOTAL_DIS_DEC;
					GRANDTOTAL_countData += parseInt(1);
					
					grand_total_rp_hk = (row.RP_HK_PERBULAN) ? grand_total_rp_hk + ( parseFloat(row.RP_HK_PERBULAN) * parseFloat(row.MPP_PERIOD_BUDGET) ) : grand_total_rp_hk;
					grand_total_mpp = (row.MPP_PERIOD_BUDGET) ? grand_total_mpp + parseFloat(row.MPP_PERIOD_BUDGET) : grand_total_mpp;
					
					jobcode = row.JOB_CODE;
					jobcode_grup = row.GROUP_CHECKROLL_DESC;
					jobcode_desc = row.JOB_DESCRIPTION;
					bacode = row.BA_CODE;
					periodbudget = row.BA_CODE;
                });
						
				if(showSubTotal == 0){
					var lastTr = ($("#data_freeze tr").length-1);
					var sub_total = $("#data_freeze tr:eq(1)").clone().insertAfter($("#data_freeze tr:eq("+lastTr+")"));
					var index = ($("#data_freeze tr").length -1);	
					$("#data_freeze tr:eq(" + index + ") input[id^=text00_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text01_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").val(periodbudget);
					$("#data_freeze tr:eq(" + index + ") input[id^=text02_]").addClass("subtotal_text");
					$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").val(bacode);
					$("#data_freeze tr:eq(" + index + ") input[id^=text03_]").addClass("subtotal_text");
					$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(jobcode);
					$("#data_freeze tr:eq(" + index + ") input[id^=text04_]").addClass("subtotal_text");
					$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(jobcode_grup);
					$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").addClass("subtotal_text");
					$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").val(jobcode_desc);
					$("#data_freeze tr:eq(" + index + ") input[id^=text06_]").addClass("subtotal_text");
					$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").val("");
					$("#data_freeze tr:eq(" + index + ") input[id^=text07_]").addClass("number subtotal_text");
					$("#data_freeze tr:eq(" + index + ")").removeAttr("style");						
					
					var lastTr = ($("#data tr").length-1);
					var sub_total = $("#data tr:eq(1)").clone().insertAfter($("#data tr:eq("+lastTr+")"));
					var index = ($("#data tr").length -1);	
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(GP_INFLASI, 2));
					$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(accounting.formatNumber(MPP_PERIOD_BUDGET, 0));
					$("#data tr:eq(" + index + ") input[id^=text09_]").addClass("integer subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(accounting.formatNumber(TOTAL_GP_MPP, 2));
					$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(ASTEK, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(CATU, 2));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(JABATAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(KEHADIRAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(LAINNYA, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(PPH_21, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(TOTAL_GAJI_TUNJANGAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(total_rp_hk/total_mpp, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(BONUS, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(HHR, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(OBAT, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(THR, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(TOTAL_TUNJANGAN_PK_UMUM, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(YEAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(DIS_JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(DIS_FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(DIS_MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text27_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(DIS_APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text28_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(DIS_MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text29_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(accounting.formatNumber(DIS_JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(DIS_JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text31_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(DIS_AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text32_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(accounting.formatNumber(DIS_SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(DIS_OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text34_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(accounting.formatNumber(DIS_NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text35_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(DIS_DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text36_]").addClass("number subtotal_text");
					$("#data tr:eq(" + index + ") input[id^=text37_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text37_]").addClass("subtotal_text");
					$("#data tr:eq(" + index + ")").removeAttr("style");
				}
            }
			else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
