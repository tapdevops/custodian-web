<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan Master Ha Statement
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	22/04/2013
Update Terakhir		:	22/04/2013
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
					<td>DIVISION :</td>
					<td>
						<?php //echo $this->setElement($this->input['src_region_code']);?>
						<?php $new = explode(',', $this->divcode); $newdiv = $new[0]; ?>
						<input type="hidden" name="key_find_div" id="key_find_div" value="<?php echo $newdiv; ?>" style="width:200px;" />
						<input type="text" name="src_div" id="src_div" value="<?php echo $this->divname; ?>" style="width:200px;"  class='filter' readonly="readonly" />
						<?php //if ($this->divcode == 'ALL') : ?>
						<input type="button" name="pick_div" id="pick_div" value="...">
						<?php //endif; ?>
					</td>
				</tr>
				<tr>
					<td>COST CENTER :</td>
					<td>
						<input type="hidden" name="key_find_cc" id="key_find_cc" value="<?php echo (strpos($this->cccode, ',') !== false) ? '' : $this->cccode; ?>" style="width:200px;" />
						<input type="text" name="src_cc" id="src_cc" value="<?php echo $this->ccname; ?>" style="width:200px;"  class='filter'/>
						<?php //if ($this->cccode == 'ALL') : ?>
						<input type="button" name="pick_cc" id="pick_cc" value="...">
						<?php //endif; ?>
					</td>
				</tr>
				<tr>
					<td>JENIS REPORT :</td>
					<td>
						<select name="jenis_report" id="jenis_report" style="width:205px;" />
							<option value="">Pilih</option>
							<option value="ho_summary_budget">REPORT - SUMMARY BUDGET HO</option>
							<option value="ho_budget">REPORT - BUDGET HO</option>
							<option value="ho_opex">REPORT - OPEX HO</option>
							<option value="ho_capex">REPORT - CAPEX HO</option>
							<option value="ho_spd">REPORT - SPD HO</option>
							<option value="ho_profit_loss">REPORT - PROFIT & LOSS HO</option>
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
						Generate Terakhir : <span id='last_generate'></span-->
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
var count = 0;
var page_num  = parseInt($("#page_num").val(), 10);
var page_rows = parseInt($("#page_rows").val(), 10);
var page_max  = 0;
$(document).ready(function() {
	//BUTTON ACTION	
    $("#btn_find").click(function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var key_find_div = $('#key_find_div').val();
		var key_find_cc = $('#key_find_cc').val();
		var src_cc = $('#src_cc').val();
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT
		
		//if ( ( reference_role == 'BA_CODE' ) && ( region == '' ) && ( ba_code == '' ) ) {
		//	alert("Anda Harus Memilih Region dan Business Area Terlebih Dahulu.");
		if (src_cc == '') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else if (jenis_report == '') {
			alert('Anda Harus Memilih Jenis Report Terlebih Dahulu.');
		} else {
			switch(jenis_report){
				case "ho_summary_budget":
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-summary-budget/jenis_report/ho_summary_budget/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
				  break;
				case "ho_budget":
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-budget/jenis_report/ho_budget/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
				  break;
				case "ho_opex": 
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-opex/jenis_report/ho_opex/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
				  break;
				case "ho_capex": 
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-capex/jenis_report/ho_capex/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
				  break;  
				case "ho_spd":
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-spd/jenis_report/ho_spd/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
				  break;
				case "ho_profit_loss":
				  window.open("<?=$_SERVER['PHP_SELF']?>/ho-report-summary/report-ho-profit-loss/jenis_report/ho_profit_loss/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
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

    $('#pick_div').click(function() {
    	var divcode = $("#key_find").val();
    	popup("pick/ho-division/module/hoReportSummary/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
    });
    $('#src_div').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find").val();
    		popup("pick/ho-division/module/hoReportSummary/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
    	} else {
    		event.preventDefault();
    	}
    });
		
    $('#pick_cc').click(function() {
    	var divcode = $("#key_find_div").val();
    	popup('pick/cost-center/division/'+divcode+'/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>', 'pick', 700, 400);
    });
    $('#src_cc').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find_div").val();
    		popup('pick/cost-center/division/'+divcode+'/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>', 'pick', 700, 400);
    	} else {
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
		var key_find_div = $('#key_find_div').val();
		var key_find_cc = $('#key_find_cc').val();
		var src_cc = $('#src_cc').val();
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT
		
		if ( src_cc == '' ) {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else if (jenis_report == '') {
			alert('Anda Harus Memilih Jenis Report Terlebih Dahulu.');
		} else {
			$.ajax({
				type     : "post",
				url      : "ho-report-summary/generate-report",
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
		var key_find_div = $('#key_find_div').val();
		var key_find_cc = $('#key_find_cc').val();
		var jenis_report = $("#jenis_report").val();		//JENIS REPORT

		var src_cc = $('#src_cc').val();
		
		if ( src_cc == '' ) {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "ho-report-summary/get-last-generate",
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
