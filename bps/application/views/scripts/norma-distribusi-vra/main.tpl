<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Norma Distribusi VRA
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Yopie Irawan
Dibuat Tanggal		: 	03/07/2013
Update Terakhir		:	03/07/2013
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
						<!--<input type="hidden" name="src_region_code" id="src_region_code" value="" style="width:200px;"/>
						<input type="text" name="src_region" id="src_region" value="" style="width:200px;" class='filter'/>
						<input type="button" name="pick_region" id="pick_region" value="...">-->
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
			<legend>DISTRIBUSI VRA - INFRA</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />
					</td>
					<td width="50%" align="right">
						<input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class='button' />
					</td>
				</tr>
			</table>
			<div id='scrollarea'>
			<table width='100%' border='0' cellpadding='1' cellspacing='1' class='data_header' id='main_data'>
			<thead>
				<tr>
					<th rowspan='2'>KODE</th>
					<th rowspan='2'>AKTIVITAS</th>
					<th colspan=3>JENIS ALAT</th>
					<th colspan=52 id='thdistribusi'>DISTRIBUSI JAM KERJA BY LOKASI KERJA (HM - KM)</th>
				</tr>
				<tr>
					<th>KODE</th>
					<th>VRATYPE</th>
					<th>UOM</th>
					<?php for ($i=1;$i<=50;$i++){ 
						echo "<th id='thtop".$i."_' style='display:none'>AFD-".$i."</th>";
					}
					?>
					<th>TOTAL</th>
					<th>TOTAL COST</th>
				</tr>
				<tr class='column_number'>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
					<th>5</th>
					<?php for ($i=13;$i<63;$i++){ 
						echo "<th id='thbot".$i."_' style='display:none'>AFD-".($i-12)."</th>";
					}
					?>
					
					<th>10</th>
					<th>11</th>
				</tr>
			</thead>
			<tbody name='data' id='data'>
				<tr style='display:none'>
					<td>
						<input type='hidden' name='text00[]' id='text00_' readonly='readonly'/>
						<input type='hidden' name='text01[]' id='text01_' readonly='readonly'/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type='text' name='text02[]' id='text02_' readonly='readonly' style='width:80px' value='2'/>
					</td>
					<td><input type='text' name='text03[]' id='text03_' readonly='readonly' style='width:250px' value='3'/></td>
					<td><input type='text' name='text04[]' id='text04_' readonly='readonly' style='width:80px' value='4'/></td>
					<td><input type='text' name='text05[]' id='text05_' readonly='readonly' style='width:250px' value='5'/></td>
					<td><input type='text' name='text06[]' id='text06_' readonly='readonly' style='width:80px' value='6'/></td>
					
					<?php for ($i=13;$i<63;$i++){ 
						echo "<td id='data".$i."_' style='display:none'>
									<input type='hidden' name='text".$i."_1[]' id='text".$i."1_' style='width:80px' value=''/>
									<input type='text' name='text".$i."_2[]'  id='text".$i."2_' style='width:80px' value='' readonly />
								</td>";
					}
					?>
					
					<td><input type='text' name='text07[]' id='text07_' readonly='readonly' style='width:80px' value=''/></td>
					<td><input type='text' name='text08[]' id='text08_' readonly='readonly' style='width:120px' value=''/></td>
				</tr>
			</tbody>
			<tfoot name='tfoot' id='tfoot' style='display:none'>
				<tr>
					<td id='tdtotal' class='grandtotal'>TOTAL <span id='label_summary_data'></span></td>
					<td><input type='text' name='summary_data' id='summary_data' readonly='readonly' style='width:120px'/></td>
				</tr>
			</tfoot>
			</table>
			</div>
			<br />
			<table width='100%' border="0" cellpadding="0" cellspacing="0">
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
var countAfd = 0;
var countHeader = 0;
var countData = 0;
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
			//jika periode budget yang dipilih <> periode budget aktif, maka tidak dapat melakukan proses perhitungan
			if (budgetperiod != current_budgetperiod) {
				$("#btn_save").hide();
				$("#btn_add").hide();
			}else{
				$("#btn_save").show();
				$("#btn_add").show();
			}
			
			$.ajax({
				type     : "post",
				url      : "norma-distribusi-vra/get-status-periode",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType: "json",
				success  : function(data) {
					if (data == 'CLOSE') {
							$("#btn_save").hide();
							$("#btn_add").hide();
						}else{
							$("#btn_save").show();
							$("#btn_add").show();
						}
					}
			});
		}
    });
	$("#btn_refresh").click(function() {
		location.reload();
    });
	$("#btn_add").live("click", function(event) {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT
	
		if( ba_code == '' || region == ''){
			alert('Anda Harus Memilih Region dan Business Area Terlebih Dahulu.');
		}
		else{
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
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("readonly", "");
			$("#data tr:eq(" + index + ") input[id^=text04_]").attr("title", "Tekan F9 Untuk Memilih.");
			$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text9_1_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text9_1_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text10_1_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text10_1_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text11_1_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text11_1_]").addClass("number");
			$("#data tr:eq(" + index + ") input[id^=text12_1_]").val("");
			$("#data tr:eq(" + index + ") input[id^=text12_1_]").addClass("number");
			$("#data tr:eq(" + index + ")").removeAttr("style");
			$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
		}
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
		$("#data tr:eq(" + index + ") input[id^=text02_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text02_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text02_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text03_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text04_]").attr("readonly", "");
		$("#data tr:eq(" + index + ") input[id^=text04_]").attr("title", "Tekan F9 Untuk Memilih.");
		$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text9_1_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text9_1_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text10_1_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text10_1_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text11_1_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text11_1_]").addClass("number");
		$("#data tr:eq(" + index + ") input[id^=text12_1_]").val("");
		$("#data tr:eq(" + index + ") input[id^=text12_1_]").addClass("number");
		$("#data tr:eq(" + index + ")").removeAttr("style");
		$("#data tr:eq(" + index + ") input[id^=text02_]").focus();
    });
	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
		
		if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")){
			//cek jika trxcode kosong & klik delete, maka kosongkan seluruh data
			if (rowid == '') {
				clearTextField(row);
			}
			else {
				$.ajax({
					type     : "post",
					url      : "norma-distribusi-vra/delete/trxcode/"+encode64(rowid),
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
			$.ajax({
				type     : "post",
				url      : "norma-distribusi-vra/save",
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
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {		
			window.open("<?=$_SERVER['PHP_SELF']?>/download/data-norma-distribusi-vra/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find + "/search/" + search,'_blank');
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
		popup("pick/business-area/regioncode/"+regionCode+"/module/normaDistribusiVra", "pick", 700, 400 );
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
		$("#btn_find").trigger("click");
        page_num = 1;
    });
    $("#btn_prev").click(function() {
		$("#btn_find").trigger("click");
        page_num--;
    });
    $("#btn_next").click(function() {
		$("#btn_find").trigger("click");
        page_num++;
    });
    $("#btn_last").click(function() {
		$("#btn_find").trigger("click");
        page_num = page_max;
    });
	$("#data tr input").live("focus", function() {
        var tr = $(this).parent().parent();
        var table = $(tr).parent();
        var index = $(table).children().index(tr);

        $("#record_counter").html("DATA: " + (((page_num - 1) * page_rows) + index) + " / " + countHeader);
        $("#data").find("tr").attr("bgcolor", "#FFFFFF");
        $("#data").find("tr:eq(" + (index) + ")").attr("bgcolor", "#FFFF00");
    });


	//LOV UTK INPUTAN
	$("input[id^=text02_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text02_'+row).is('[readonly]') == false) { 
				popup("pick/activity/module/normaDistribusiVra/row/" + row, "pick", 700, 400 );
			}			
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text04_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			if ($('#text04_'+row).is('[readonly]') == false) { 
				//lov
				popup("pick/vra/module/normaDistribusiVra/row/" + row, "pick", 700, 400 );
			}			
        }else{
			event.preventDefault();
		}
    });
});


function getData(){
    $("#page_num").val(page_num);
    $.ajax({
        type    : "post",
        url     : "norma-distribusi-vra/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			//resetMainTable();
			
            countAfd = data.countAfd;
			countHeader = data.countHeader;
			countData = data.countData;
			
            page_max = Math.ceil(countHeader / page_rows);
            if (page_max == 0) {
                page_max = 1;
            }
            $("#btn_first").attr("disabled", page_num == 1);
            $("#btn_prev").attr("disabled", page_num == 1);
            $("#btn_next").attr("disabled", page_num == page_max);
            $("#btn_last").attr("disabled", page_num == page_max);
            $("#page_counter").html("HALAMAN: " + page_num + " / " + page_max);
			i=0;
            if (countAfd > 0) {		
				i=0;
				$.each(data.tabs, function(key, tab) {
					$("#thtop"+(13+i)+"_").removeAttr("style");
					$("#thbot"+(13+i)+"_").removeAttr("style");
					$("#data"+(13+i)+"_").removeAttr("style");
					$('#main_data tr:nth-child(2) th:nth-child('+(16+i)+')').html(tab.LOCATION_CODE);
					$('#main_data tr:nth-child(3) th:nth-child('+(6+i)+')').html(6+i);
					$("#text"+(13+i)+"1_").attr("value",tab.LOCATION_CODE);
					$("#text"+(13+i)+"2_").attr("value",'');
					i++;
                });
				$('#main_data tr:nth-child(3) th:nth-child('+(56)+')').html(6+i);
				$('#main_data tr:nth-child(3) th:nth-child('+(57)+')').html(7+i);
				$('#main_data tr:nth-child(3) th:nth-child('+(58)+')').html(8+i);
				$('#main_data tr:nth-child(3) th:nth-child('+(59)+')').html(9+i);
				$('#main_data tr:nth-child(3) th:nth-child('+(60)+')').html(10+i);
				$('#main_data tr:nth-child(3) th:nth-child('+(61)+')').html(11+i);
				$("#thdistribusi").attr("colspan",(2+i));
				$("#tdtotal").attr("colspan",(6+i));//bibitan,basecamp dihapus 130823
				$("#tfoot").removeAttr("style");
				document.getElementById("label_summary_data").innerHTML = "";
				
				var totalall = parseFloat(0);
				if (countHeader > 0) {		
					$.each(data.rows, function(key, row) {
						var tr = $("#data tr:eq(0)").clone();
						$("#data").append(tr);
						var index = ($("#data tr").length - 1);					
						$("#data tr:eq(" + index + ")").find("input, select").each(function() {
							$(this).attr("id", $(this).attr("id") + index);
						});
						
						$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data tr:eq(" + index + ") input[id^=btn01_]").val("");
						$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.TRX_CODE);
						$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data tr:eq(" + index + ") input[id^=text02_]").val(row.ACTIVITY_CODE);
						$("#data tr:eq(" + index + ") input[id^=text03_]").val(row.DESCRIPTION);
						$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.VRA_CODE);
						$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.VRA_SUB_CAT_DESCRIPTION);
						$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.UOM);
						$("#data tr:eq(" + index + ")").removeAttr("style");// di resetMainTable() masih menggunakan display:none
						
						vLoop=13;
						var totalvalue = parseFloat(0);
						var totalprice = parseFloat(0);
						
						if (countData > 0) {		
							//doni
							$.each(data.rowsAfd, function(key, afdeling) { 
							//hanya untuk 50 hidden afd, dengan syarat nilai 0 didatabase jika tidak ada di afd
							//konfirm by doni 130716, kalo tidak ada nilai record 0 maka nilai setelahnya akan bergeser kesebelumnya
								$.each(afdeling, function(key, dataAfdeling){
									if (row.TRX_CODE == dataAfdeling.TRX_CODE) {
										if(dataAfdeling.LOCATION_CODE=='BIBITAN'){
											$("#data tr:eq(" + index + ") input[id^=text92_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 2));
											$("#data tr:eq(" + index + ") input[id^=text92_]").addClass("number");
										}else if(dataAfdeling.LOCATION_CODE=='BASECAMP'){
											$("#data tr:eq(" + index + ") input[id^=text102_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 2));
											$("#data tr:eq(" + index + ") input[id^=text102_]").addClass("number");
										}else if(dataAfdeling.LOCATION_CODE=='UMUM'){
											$("#data tr:eq(" + index + ") input[id^=text112_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 2));
											$("#data tr:eq(" + index + ") input[id^=text112_]").addClass("number");
										}else if(dataAfdeling.LOCATION_CODE=='LAIN'){
											$("#data tr:eq(" + index + ") input[id^=text122_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 2));
											$("#data tr:eq(" + index + ") input[id^=text122_]").addClass("number");
										}else{
											$("#data tr:eq(" + index + ") input[id^=text"+vLoop+"2_]").val(accounting.formatNumber(dataAfdeling.HM_KM, 2)); //insert nilai afdeling 
											$("#data tr:eq(" + index + ") input[id^=text"+vLoop+"2_]").addClass("number");
										}
										vLoop++;
										totalvalue=totalvalue+parseFloat(Number(dataAfdeling.HM_KM,10));//PRICE_HM_KM
										totalprice=totalprice+parseFloat(Number(dataAfdeling.PRICE_HM_KM,10));
									}
								});
							});
						}
						
						
						$("#data tr:eq(" + index + ") input[id^=text07_]").val(accounting.formatNumber(totalvalue, 2));
						$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(accounting.formatNumber(totalprice, 2));
						$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("number");
						totalall=totalall+totalprice;
					});
				}
				
				//summary_data
				$("#summary_data").val(accounting.formatNumber(totalall, 2));
				$("#summary_data").addClass("grandtotal_text number");
            } else {
				$("#tfoot").hide();
			}
        }
    });
}
</script>
