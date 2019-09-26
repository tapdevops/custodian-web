<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Panen Supervisi
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	26/06/2013
Update Terakhir		:	26/06/2013
Revisi				:	
YULIUS 07/07/2014	: - fungsi save temp hanya dilakukan ketika ada perubahan data & pindah ke next page
					  - penambahan info untuk lock table pada tombol cari, simpan, list
YULIUS 16/07/2014	:	- Modified Paginasi
						- tambah validasi FLAG_TEMP ke cekTempData
						- tambah html tombol btn_save_temp					  
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
						<!--<input type="hidden" name="src_region_code" id="src_region_code" value="" style="width:200px;"/>
						<input type="text" name="src_region" id="src_region" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_region" id="pick_region" value="...">-->
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
			<legend>NORMA PANEN SUPERVISI</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_save" id="btn_save" value="GENERATE" class="button" />
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button"/>
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th>PERIODE<BR>BUDGET</th>
					<th>BUSINESS<BR>AREA</th>
					<th>MIN<BR>RANGE BJR</th>
					<th>MAX<BR>RANGE BJR</th>
					<th>PREMI MANDOR PANEN</th>
					<th>PREMI MANDOR 1</th>
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
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px' value='2'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:50px' value='3'/></td>
					<td><input type="text" name="text04[]" id="text04_" readonly="readonly" style='width:120px' value='4'/></td>
					<td><input type="text" name="text05[]" id="text05_" readonly="readonly" style='width:120px' value='5'/></td>
					<td><input type="text" name="text06[]" id="text06_" readonly="readonly" style='width:120px' value='6'/></td>
					<td><input type="text" name="text07[]" id="text07_" readonly="readonly" style='width:120px' value='7'/></td>
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
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			
			//cek status periode
			$.ajax({
				type     : "post",
				url      : "norma-panen-supervisi/get-status-periode",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					if (data == 'CLOSE') {
						$("#btn_save_temp").hide();
						$("#btn_save").hide();
					}else{
						$("#btn_save_temp").hide();
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
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		var asumsi_over_basis = $("#asumsi_over_basis").val();
		var cr_avg_mandor_panen = $("#cr_avg_mandor_panen").val();
		var ratio_pemanen = $("#ratio_pemanen").val();
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if (asumsi_over_basis == ''){
			alert('Asumsi Over Budget Harus Diisi Terlebih Dahulu.');
		} else if (cr_avg_mandor_panen == ''){
			alert('Checkroll AVG Mandor Panen Harus Diisi Terlebih Dahulu.');
		} else if (ratio_pemanen == ''){
			alert('Ratio Pemanen Harus Diisi Terlebih Dahulu.');
		} else{
			$.ajax({
				type     : "post",
				url      : "norma-panen-supervisi/save",
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
				}
			});
		}
    });
	
	$("#btn_save_temp").click( function() {
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
			$.ajax({
				type     : "post",
				url      : "norma-panen-supervisi/save-temp",
				data     : $("#form_init").serialize(),
				cache    : false,
				success  : function(data) {
					if (data == "done") {
						alert("Data berhasil disimpan tetapi belum diproses. Silahkan klik tombol Hitung untuk memproses data.");	
					}else if (data == "no_alert") {
					}else{
						alert(data);
					}
				}
			});
		}
    });
	
	$("#btn_cancel").click(function() {
        self.close();
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
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-panen-supervisi/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + encode64(search),'_blank');
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
		$("#btn_save_temp").trigger("click");
		page_num = 1;
		clearDetail();
		getData();
    });
    $("#btn_prev").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num--;
		clearDetail();
		getData();
    });
    $("#btn_next").click(function() {
		$("#btn_save_temp").trigger("click");
		page_num++;
		clearDetail();
		getData();
    });
    $("#btn_last").click(function() {
		$("#btn_save_temp").trigger("click");
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
    //
  $.ajax({
      type    : "post",
      url     : "norma-panen-supervisi/list_2018",
      data    : $("#form_init").serialize(),
      cache   : false,
      dataType: "json",
      success : function(data) {
		if (data.return == 'locked') {
			alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
			$("#btn_save_temp").hide();
			$("#btn_save").hide();
			$("#btn01_").hide();
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
          $("#data").append(tr);
          var index = ($("#data tr").length - 1);
					$("#data tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});
					$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
					if (row.FLAG_TEMP) {cekTempData(index);} 
					$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
					$("#data tr:eq(" + index + ") input[id^=btn01_]").val("");
					$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
					$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
          $("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
          $("#data tr:eq(" + index + ") input[id^=text03_]").val(row.BA_CODE);
          $("#data tr:eq(" + index + ") input[id^=text04_]").val(accounting.toFixed(accounting.formatNumber(row.BJR_MIN, 3, ","), 2));
					$("#data tr:eq(" + index + ") input[id^=text04_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text05_]").val(accounting.toFixed(accounting.formatNumber(row.BJR_MAX, 3, ","), 2));
					$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("integer");
          $("#data tr:eq(" + index + ") input[id^=text06_]").val(accounting.formatNumber(row.PREMI_MANDOR_PANEN, 0));
					$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("integer");
					$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(row.PREMI_MANDOR_1, 0));
					$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("integer");
					$("#data tr:eq(" + index + ")").removeAttr("style");					
          $("#data tr:eq(1) input[id^=text02_]").focus();
					
					//asumsi over basis, avg mandor, ratio/pemanen
					$("#asumsi_over_basis").val(accounting.formatNumber(row.ASUMSI_OVER_BASIS, 2));
					$("#asumsi_over_basis").addClass("number");
					$("#cr_avg_mandor_panen").val(accounting.formatNumber(row.AVG_MANDOR, 2));
					$("#cr_avg_mandor_panen").addClass("number");
					$("#ratio_pemanen").val(accounting.formatNumber(row.RATIO_PEMANEN, 2));
					$("#ratio_pemanen").addClass("number");
      	});
      }
    }
	}
 });
}
</script>
