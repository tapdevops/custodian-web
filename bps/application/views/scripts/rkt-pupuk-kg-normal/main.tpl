<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan RKT Pupuk HA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	17/07/2013
Update Terakhir		:	17/07/2013
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
						<input type="text" name="src_ba" id="src_ba" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_ba" id="pick_ba" value="...">
					</td>
				</tr>
				<tr>
					<td>MATURITY STAGE :</td>
					<td>
						
						<?php echo $this->setElement($this->input['src_matstage_code']);?>
					</td>
				</tr>
				<tr>
					<td>JENIS PUPUK :</td>
					<td>
						<input type="hidden" name="src_jenis_pupuk" id="src_jenis_pupuk" value="" style="width:200px;" />
						<input type="text" name="src_pupuk_desc" id="src_pupuk_desc" value="" style="width:200px;" />
						<input type="button" name="pick_pupuk" id="pick_pupuk" value="...">
					</td>
				</tr>
				<tr>
					<td>AFDELING :</td>
					<td>
						<input type="text" name="src_afd" id="src_afd" value="" style="width:200px;"/>
						<input type="button" name="pick_afd" id="pick_afd" value="...">
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
			<legend>PUPUK - KG NORMAL</legend>
			
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />					
					</td>
					<td width="50%" align="right">						
						<input type="button" name="btn_save" id="btn_save" value="GENERATE" class="button" />
					</td>
				</tr>
			</table>
			<table id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>AFDELING</th>
					<th>BLOK - DESC</th>
					<th>LAND TYPE</th>
					<th>TOPOGRAFI</th>
					<th>TAHUN TANAM</th>
					<th>MATURITY STAGE<BR>SMS 1</th>
					<th>MATURITY STAGE<BR>SMS 2</th>
					<th>LUAS TANAM<BR>HA</th>
					<th>LUAS TANAM<BR>POKOK</th>
					<th>LUAS TANAM<BR>SPH</th>
					<th>DISTRIBUSI HA KERJA<BR>JAN</th>
					<th>DISTRIBUSI HA KERJA<BR>FEB</th>
					<th>DISTRIBUSI HA KERJA<BR>MAR</th>
					<th>DISTRIBUSI HA KERJA<BR>APR</th>
					<th>DISTRIBUSI HA KERJA<BR>MEI</th>
					<th>DISTRIBUSI HA KERJA<BR>JUN</th>
					<th>DISTRIBUSI HA KERJA<BR>JUL</th>
					<th>DISTRIBUSI HA KERJA<BR>AGS</th>
					<th>DISTRIBUSI HA KERJA<BR>SEP</th>
					<th>DISTRIBUSI HA KERJA<BR>OKT</th>
					<th>DISTRIBUSI HA KERJA<BR>NOV</th>
					<th>DISTRIBUSI HA KERJA<BR>DES</th>					
					<th>DISTRIBUSI HA KERJA<BR>YEAR</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>JAN</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>FEB</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>MAR</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>APR</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>MEI</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>JUN</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>JUL</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>AGS</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>SEP</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>OKT</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>NOV</th>
					<th>DISTRIBUSI JENIS PUPUK<BR>DES</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr name='content' id='content' style="display:none;" >
					<td width='50px'>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" value='2'/>
						<input type="hidden" name="text05[]" id="text05_" readonly="readonly" value='5'/>
					</td>
					<td width='50px'><input type="text" name="text03[]" id="text03_" readonly="readonly" value='3'/></td>
					<td width='50px'><input type="text" name="text04[]" id="text04_" readonly="readonly" value='4'/></td>
					<td width='75px'><input type="text" name="text50[]" id="text50_" readonly="readonly" /></td>
					<td width='150px'><input type="text" name="text06[]" id="text06_" readonly="readonly" value='6'/></td>
					<td width='150px'><input type="text" name="text07[]" id="text07_" readonly="readonly" value='7'/></td>
					<td width='100px'><input type="text" name="text08[]" id="text08_" readonly="readonly" value='8'/></td>
					<td width='50px'><input type="text" name="text09[]" id="text09_" readonly="readonly" value='9'/></td>
					<td width='50px'><input type="text" name="text10[]" id="text10_" readonly="readonly" value='10'/></td>
					<td width='100px'><input type="text" name="text11[]" id="text11_" readonly="readonly" value='11'/></td>
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
					<td width='100px'><input type="text" name="text24[]" id="text24_" readonly="readonly" value='24'/></td>
					<td width='100px'><input type="text" name="text25[]" id="text25_" readonly="readonly" value='25'/></td>
					<td width='100px'><input type="text" name="text26[]" id="text26_" readonly="readonly" value='26'/></td>
					
					<td width='150px'><input type="text" name="text27[]" id="text27_" readonly="readonly" value='27'/></td>
					<td width='150px'><input type="text" name="text28[]" id="text28_" readonly="readonly" value='28'/></td>
					<td width='150px'><input type="text" name="text29[]" id="text29_" readonly="readonly" value='29'/></td>
					<td width='150px'><input type="text" name="text30[]" id="text30_" readonly="readonly" value='30'/></td>
					<td width='150px'><input type="text" name="text31[]" id="text31_" readonly="readonly" value='31'/></td>
					<td width='150px'><input type="text" name="text32[]" id="text32_" readonly="readonly" value='32'/></td>
					<td width='150px'><input type="text" name="text33[]" id="text33_" readonly="readonly" value='33'/></td>
					<td width='150px'><input type="text" name="text34[]" id="text34_" readonly="readonly" value='34'/></td>
					<td width='150px'><input type="text" name="text35[]" id="text35_" readonly="readonly" value='35'/></td>
					<td width='150px'><input type="text" name="text36[]" id="text36_" readonly="readonly" value='36'/></td>
					<td width='150px'><input type="text" name="text37[]" id="text37_" readonly="readonly" value='37'/></td>
					<td width='150px'><input type="text" name="text38[]" id="text38_" readonly="readonly" value='38'/></td>
				</tr>			
			</tbody>
			<!--
			<tfoot name='tfoot' id='tfoot' style="display:none">
				<tr>
					<td colspan='12' class='grandtotal'>TOTAL</td>
					<td><input type="text" name="total14" id="total14" readonly="readonly" style='width:100px' value='14'/></td>
					<td><input type="text" name="total15" id="total15" readonly="readonly" style='width:100px' value='15'/></td>
					<td><input type="text" name="total16" id="total16" readonly="readonly" style='width:100px' value='16'/></td>
					<td><input type="text" name="total17" id="total17" readonly="readonly" style='width:100px' value='17'/></td>
					<td><input type="text" name="total18" id="total18" readonly="readonly" style='width:100px' value='18'/></td>
					<td><input type="text" name="total19" id="total19" readonly="readonly" style='width:100px' value='19'/></td>
					<td><input type="text" name="total20" id="total20" readonly="readonly" style='width:100px' value='20'/></td>
					<td><input type="text" name="total21" id="total21" readonly="readonly" style='width:100px' value='21'/></td>
					<td><input type="text" name="total22" id="total22" readonly="readonly" style='width:100px' value='22'/></td>
					<td><input type="text" name="total23" id="total23" readonly="readonly" style='width:100px' value='23'/></td>
					<td><input type="text" name="total24" id="total24" readonly="readonly" style='width:100px' value='24'/></td>
					<td><input type="text" name="total25" id="total25" readonly="readonly" style='width:100px' value='25'/></td>
					<td><input type="text" name="total26" id="total26" readonly="readonly" style='width:100px' value='26'/></td>
					<td><input type="text" name="total27" id="total27" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total28" id="total28" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total29" id="total29" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total30" id="total30" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total31" id="total31" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total32" id="total32" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total33" id="total33" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total34" id="total34" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total35" id="total35" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total36" id="total36" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total37" id="total37" readonly="readonly" style='width:150px' value=''/></td>
					<td><input type="text" name="total38" id="total38" readonly="readonly" style='width:150px' value=''/></td>
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
	//FREEZE PANES
	$('#mainTable').freezeTableColumns({ 
		width:       970,   // required
		height:      400,   // required
		numFrozen:   4,     // optional
		frozenWidth: 245,   // optional
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
			
			//cek status periode
			$.ajax({
				type     : "post",
				url      : "rkt-pupuk-kg-normal/get-status-periode",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					if (data == 'CLOSE') {
							$("#btn_save").hide();
						}else{
							$("#btn_save").show();
						}
					}
			});				

		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
				type     : "post",
				url      : "rkt-pupuk-kg-normal/save-all",
				data     : $("#form_init").serialize(),
				cache    : false,
				//dataType : 'json',
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan.");
						$("#btn_find").trigger("click");
					}else{
						alert(data);
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
		var src_matstage_code = $("#src_matstage_code").val();	//MATURITY STAGE
		var search = $("#search").val();					//SEARCH FREE TEXT
		var src_afd = $("#src_afd").val();					//SEARCH AFD
		var src_jenis_pupuk = $("#src_jenis_pupuk").val();	//JENIS PUPUK
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-rkt-pupuk-kg-normal/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/src_matstage_code/" + src_matstage_code + "/src_afd/" + src_afd + "/src_jenis_pupuk/" + src_jenis_pupuk,'_blank');
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
			var regionCode = $("#src_region_code").val();
			popup("pick/business-area/regioncode/"+regionCode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });	
	
	//PICK AFD
	$("#pick_afd").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/afdeling/bacode/" + bacode, "pick", 700, 400 );
    });
	$("#src_afd").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			var bacode = $("#key_find").val();
			popup("pick/afdeling/bacode/" + bacode, "pick", 700, 400 );
        }else{
			event.preventDefault();
		}
    });
	
	//PICK JENIS PUPUK
	$("#pick_pupuk").click(function() {
		var bacode = $("#key_find").val();
		popup("pick/jenis-pupuk/bacode/"+bacode, "pick", 700, 400 );
    });
    $("#src_pupuk_desc").live("keydown", function(event) {
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			var bacode = $("#key_find").val();
			popup("pick/jenis-pupuk/bacode/"+bacode, "pick", 700, 400 );
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
	
    //
    $.ajax({
        type    : "post",
        url     : "rkt-pupuk-kg-normal/list",
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
                    $("#data_freeze tr:eq(" + index + ") input[id^=text04_]").val(row.AFD_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text05_]").val(row.BLOCK_CODE);
					$("#data_freeze tr:eq(" + index + ") input[id^=text50_]").val(row.BLOCK_CODE + " - " + row.BLOCK_DESC);
					$("#data_freeze tr:eq(" + index + ")").removeAttr("style");
					
					
					//right freeze panes
                    var tr = $("#data tr:eq(0)").clone();
                    $("#data").append(tr);
                    var index = ($("#data tr").length - 1);					
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});					
					
					//right freeze panes row
                    $("#data tr:eq(" + index + ") input[id^=text06_]").val(row.LAND_TYPE);
                    $("#data tr:eq(" + index + ") input[id^=text07_]").val(row.TOPOGRAPHY);
					$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.TAHUN_TANAM);
					$("#data tr:eq(" + index + ") input[id^=text08_]").css('text-align', 'right');
					$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.MATURITY_STAGE_SMS1);
					$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.MATURITY_STAGE_SMS2);
					$("#data tr:eq(" + index + ") input[id^=text11_]").val(accounting.formatNumber(row.HA_PLANTED, 2));
					$("#data tr:eq(" + index + ") input[id^=text11_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text12_]").val(accounting.formatNumber(row.POKOK_TANAM, 0));
					$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text13_]").val(accounting.formatNumber(row.SPH, 0));
					$("#data tr:eq(" + index + ") input[id^=text13_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text14_]").val(accounting.formatNumber(row.JAN, 2));
					$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text15_]").val(accounting.formatNumber(row.FEB, 2));
					$("#data tr:eq(" + index + ") input[id^=text15_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text16_]").val(accounting.formatNumber(row.MAR, 2));
					$("#data tr:eq(" + index + ") input[id^=text16_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text17_]").val(accounting.formatNumber(row.APR, 2));
					$("#data tr:eq(" + index + ") input[id^=text17_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text18_]").val(accounting.formatNumber(row.MAY, 2));
					$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text19_]").val(accounting.formatNumber(row.JUN, 2));
					$("#data tr:eq(" + index + ") input[id^=text19_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text20_]").val(accounting.formatNumber(row.JUL, 2));
					$("#data tr:eq(" + index + ") input[id^=text20_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text21_]").val(accounting.formatNumber(row.AUG, 2));
					$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.SEP, 2));
					$("#data tr:eq(" + index + ") input[id^=text22_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.OCT, 2));
					$("#data tr:eq(" + index + ") input[id^=text23_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.NOV, 2));
					$("#data tr:eq(" + index + ") input[id^=text24_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.DEC, 2));
					$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.TOTAL, 2));
					$("#data tr:eq(" + index + ") input[id^=text26_]").addClass("number");
					$("#data tr:eq(" + index + ") input[id^=text27_]").val(row.PUPUK_JAN);
					$("#data tr:eq(" + index + ") input[id^=text28_]").val(row.PUPUK_FEB);
					$("#data tr:eq(" + index + ") input[id^=text29_]").val(row.PUPUK_MAR);
					$("#data tr:eq(" + index + ") input[id^=text30_]").val(row.PUPUK_APR);
					$("#data tr:eq(" + index + ") input[id^=text31_]").val(row.PUPUK_MAY);
					$("#data tr:eq(" + index + ") input[id^=text32_]").val(row.PUPUK_JUN);
					$("#data tr:eq(" + index + ") input[id^=text33_]").val(row.PUPUK_JUL);
					$("#data tr:eq(" + index + ") input[id^=text34_]").val(row.PUPUK_AUG);
					$("#data tr:eq(" + index + ") input[id^=text35_]").val(row.PUPUK_SEP);
					$("#data tr:eq(" + index + ") input[id^=text36_]").val(row.PUPUK_OCT);
					$("#data tr:eq(" + index + ") input[id^=text37_]").val(row.PUPUK_NOV);
					$("#data tr:eq(" + index + ") input[id^=text38_]").val(row.PUPUK_DES);
					$("#data tr:eq(" + index + ")").removeAttr("style");					
                    $("#data tr:eq(1) input[id^=text02_]").focus();		

					//perhitungan total
					YEAR = (row.TOTAL) ? YEAR + parseFloat(row.TOTAL) : YEAR;
					DIS_JAN = (row.JAN) ? DIS_JAN + parseFloat(row.JAN) : DIS_JAN;
					DIS_FEB = (row.FEB) ? DIS_FEB + parseFloat(row.FEB) : DIS_FEB;
					DIS_MAR = (row.MAR) ? DIS_MAR + parseFloat(row.MAR) : DIS_MAR;
					DIS_APR = (row.APR) ? DIS_APR + parseFloat(row.APR) : DIS_APR;
					DIS_MAY = (row.MAY) ? DIS_MAY + parseFloat(row.MAY) : DIS_MAY;
					DIS_JUN = (row.JUN) ? DIS_JUN + parseFloat(row.JUN) : DIS_JUN;
					DIS_JUL = (row.JUL) ? DIS_JUL + parseFloat(row.JUL) : DIS_JUL;
					DIS_AUG = (row.AUG) ? DIS_AUG + parseFloat(row.AUG) : DIS_AUG;
					DIS_SEP = (row.SEP) ? DIS_SEP + parseFloat(row.SEP) : DIS_SEP;
					DIS_OCT = (row.OCT) ? DIS_OCT + parseFloat(row.OCT) : DIS_OCT;
					DIS_NOV = (row.NOV) ? DIS_NOV + parseFloat(row.NOV) : DIS_NOV;
					DIS_DEC = (row.DEC) ? DIS_DEC + parseFloat(row.DEC) : DIS_DEC;
                });
				/*
				$("#total14").val(accounting.formatNumber(DIS_JAN, 2));
				$("#total14").addClass("number grandtotal_text");
				$("#total15").val(accounting.formatNumber(DIS_FEB, 2));
				$("#total15").addClass("number grandtotal_text");
				$("#total16").val(accounting.formatNumber(DIS_MAR, 2));
				$("#total16").addClass("number grandtotal_text");
				$("#total17").val(accounting.formatNumber(DIS_APR, 2));
				$("#total17").addClass("number grandtotal_text");
				$("#total18").val(accounting.formatNumber(DIS_MAY, 2));
				$("#total18").addClass("number grandtotal_text");
				$("#total19").val(accounting.formatNumber(DIS_JUN, 2));
				$("#total19").addClass("number grandtotal_text");
				$("#total20").val(accounting.formatNumber(DIS_JUL, 2));
				$("#total20").addClass("number grandtotal_text");
				$("#total21").val(accounting.formatNumber(DIS_AUG, 2));
				$("#total21").addClass("number grandtotal_text");
				$("#total22").val(accounting.formatNumber(DIS_SEP, 2));
				$("#total22").addClass("number grandtotal_text");
				$("#total23").val(accounting.formatNumber(DIS_OCT, 2));
				$("#total23").addClass("number grandtotal_text");
				$("#total24").val(accounting.formatNumber(DIS_NOV, 2));
				$("#total24").addClass("number grandtotal_text");
				$("#total25").val(accounting.formatNumber(DIS_DEC, 2));
				$("#total25").addClass("number grandtotal_text");
				$("#total26").val(accounting.formatNumber(YEAR, 2));
				$("#total26").addClass("number grandtotal_text");
				
				$("#total27").addClass("grandtotal_text");
				$("#total28").addClass("grandtotal_text");
				$("#total29").addClass("grandtotal_text");
				$("#total30").addClass("grandtotal_text");
				$("#total31").addClass("grandtotal_text");
				$("#total32").addClass("grandtotal_text");
				$("#total33").addClass("grandtotal_text");
				$("#total34").addClass("grandtotal_text");
				$("#total35").addClass("grandtotal_text");
				$("#total36").addClass("grandtotal_text");
				$("#total37").addClass("grandtotal_text");
				$("#total38").addClass("grandtotal_text");
				$("#tfoot").show();
				*/
            }
			else{
				$("#tfoot").hide();
			}
        }
    });
}
</script>
