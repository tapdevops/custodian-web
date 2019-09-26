<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	3.0.0
Deskripsi			: 	View untuk Menampilkan Calculate RKT
Function 			:	
Disusun Oleh		: 	IT Solution - PT Triputra Agro Persada	
Developer			: 	Nicholas Budihardja
Dibuat Tanggal		: 	06/05/2015
Update Terakhir		:	06/05/2015
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/accounting.js');
?>
<form name="form_init" id="form_init">
	<div>   
        <fieldset>
			<legend>Calculate RKT</legend>
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
					<td>JENIS RKT :</td>
					<td>
						<select name="jenis_report" id="jenis_report" style="width:205px;" />
							<option value="rawat" selected=selected>RKT RAWAT</option>
							<option value="rawat_opsi">RKT RAWAT + OPSI</option>
							<option value="tanam_otomatis">RKT TANAM OTOMATIS</option>
							<option value="panen">RKT PANEN</option>
							<option value="kastrasi_sanitasi">RKT KASTRASI SANITASI</option>
							<option value="calc_infra">RKT INFRA</option>
							<!--option value="rawat_sisip">RKT RAWAT SISIP</option-->
						</select>
					</td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td><i><font color="red">Pilih Jenis RKT, Lalu Klik Tombol Calculate RKT Terlebih Dahulu</font></i></td>
				</tr>	
				<tr>
					<td></td>
					<td>
						<!--<input type="button" name="btn_find" id="btn_find" value="DOWNLOAD EXCEL" class="button"  style="width:130px;"/>-->
						<input type="button" name="btn_generate" id="btn_generate" value="Calculate RKT" class="button" style="width:130px; color:red"/>
						<input type="button" name="btn_refresh" id="btn_refresh" value="RESET" class="button"  style="width:70px;"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>	
						Calculate Terakhir : <span id='last_generate'></span>
					</td>
				</tr>
			</table>
			<input type="hidden" name="page_num" id="page_num" value="1" />
            <input type="hidden" name="page_rows" id="page_rows" value="100000" />
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
				case "sebaran_ha":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-sebaran-ha/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "capex":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-capex/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "development_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-development-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "summary_development_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-summary-development-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "estate_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-estate-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "summary_estate_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-summary-estate-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "vra_utilisasi":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-vra-utilisasi/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "vra_utilisasi_region":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-vra-utilisasi-region/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code,'_blank');
				  break;
				case "mod_review_development_cost_ba":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-development-cost-per-ba/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code,'_blank');
				  break;
				case "mod_review_development_cost_afd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-development-cost-per-afd/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "mod_review_estate_cost_ba":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-estate-cost-per-ba/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code,'_blank');
				  break;
				case "mod_review_estate_cost_afd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-estate-cost-per-afd/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "mod_review_produksi_region":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-produksi-per-region/budgetperiod/" + budgetperiod,'_blank');
				  break;
				case "mod_review_produksi_ba":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-produksi-per-ba/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code,'_blank');
				  break;
				case "mod_review_produksi_afd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/mod-review-produksi-per-afd/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "report_hk_development_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-hk-development-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
				  break;
				case "report_hk_estate_cost":
				  window.open("<?=$_SERVER['PHP_SELF']?>/report/report-hk-estate-cost/budgetperiod/" + budgetperiod + "/src_region_code/" + src_region_code + "/key_find/" + key_find,'_blank');
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
				type    : "post",
				url     : "rkt-manual-non-infra/chk-enh-locked-sequence", //check apakah urutan input didepannya ada yang belum lock
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					//calculate rkt
					if(data==1){
						$.ajax({
							type     : "post",
							url      : "calculate-rkt/calc-rkt",
							data     : $("#form_init").serialize(),
							cache    : false,
							dataType : 'json',
							success  : function(data) {
								if (data.return == "done") {
									alert("RKT berhasil dicalculate.");
									$("#last_generate").html(data.last_update_time + " oleh " + data.last_update_user + ".");
								}else{
									alert(data.return);
								}
							}
						});
						
					}else{
						alert('Lakukan finalisasi (LOCK) terlebih dahulu terhadap : '+data+'');
					}
				}
			})
		}
    });		
	
	
	//get last calculate date
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
				url      : "calculate-rkt/get-last-calculate",
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
