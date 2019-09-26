<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Report Keb Aktivitas Estate Cost & Development Cost
Function 			:	
Disusun Oleh		: 	IT Solution - PT Triputra Agro Persada	
Developer			: 	Nicholas Budihardja
Dibuat Tanggal		: 	18/09/2015
Update Terakhir		:	18/09/2015
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
?>
<form name="form_init" id="form_init">
	<div>   
        <fieldset>
			<legend>REPORT KEBUTUHAN AKTIVITAS</legend>
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
					<td>JENIS REPORT :</td>
					<td>
						<select name="jenis_report" id="jenis_report" style="width:205px;" />
							<option value="report_keb_aktivitas_est_ba" selected=selected>REPORT KEBUTUHAN AKTIVITAS - ESTATE COST (BA)</option>
							<option value="report_keb_aktivitas_est_afd">REPORT KEBUTUHAN AKTIVITAS - ESTATE COST (AFD)</option>
							<option value="report_keb_aktivitas_dev_ba">REPORT KEBUTUHAN AKTIVITAS - DEVELOPMENT COST (BA)</option>
							<option value="report_keb_aktivitas_dev_afd">REPORT KEBUTUHAN AKTIVITAS - DEVELOPMENT COST (AFD)</option>
						</select>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td><i><font color="red">Pilih Jenis Report, Lalu Klik Tombol Generate Report Terlebih Dahulu</font></i></td>
				</tr>	
				<tr>
					<td></td>
					<td>
						<input type="button" name="btn_find" id="btn_find" value="DOWNLOAD EXCEL" class="button"  style="width:130px;"/>
						<input type="button" name="btn_refresh" id="btn_refresh" value="RESET" class="button"  style="width:70px;"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="button" name="btn_generate" id="btn_generate" value="GENERATE REPORT" class="button" style="width:205px; color:red"/>
						Generate Terakhir : <span id='last_generate'></span>
					</td>
				</tr>
			</table>
			<input type="hidden" name="page_num" id="page_num" value="1" />
            <input type="hidden" name="page_rows" id="page_rows" value="20" />
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

$(document).ready(function() {
	//BUTTON ACTION	
    $("#btn_find").click(function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT
		
		if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else if ( ( reference_role == 'REGION_CODE' ) && ( region == '' ) ) {
			alert("Anda Harus Memilih Region Terlebih Dahulu.");
		} else {
			switch(jenis_report){
				case "report_keb_aktivitas_est_ba":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report-keb-aktivitas/report-keb-aktivitas-per-ba/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;  
				case "report_keb_aktivitas_est_afd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report-keb-aktivitas/report-keb-aktivitas-per-afd/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break; 
				case "report_keb_aktivitas_dev_ba":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report-keb-aktivitas/report-keb-aktivitas-dev-per-ba/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;  
				case "report_keb_aktivitas_dev_afd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report-keb-aktivitas/report-keb-aktivitas-dev-per-afd/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;			
			}
			
		}
    });	
	$("#btn_refresh").click(function() {
		location.reload();
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
	
	//untuk generate report
	$("#btn_generate").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "report-keb-aktivitas/generate-report",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					if (data.return == "done") {
						alert("Report berhasil digenerate.");
						$("#last_generate").html(data.last_update_time + " oleh " + data.last_update_user + ".");
					}else{
						alert(data.return);
					}
				}
			});
		}
    });	
	
	//get last generate date
	$("#jenis_report").change( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT
		
		if ( ba_code == '' ) {
			alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "report-keb-aktivitas/get-last-generate",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					$("#last_generate").html(data.last_update_time + " oleh " + data.last_update_user + ".");
				}
			});
		}
    });	
});
</script>
