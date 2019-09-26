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
			<legend>BUDGET VALIDATION</legend>
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
					<td></td>
					<td>
						<input type="button" name="btn_find" id="btn_find" value="CARI" class="button" />
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
			<legend>BUDGET VALIDATION</legend>
				<table width='100%' border="0" cellpadding="1" cellspacing="1" id="data-load">
				<thead>
					<tr>
						<th>GROUP BUDGET</th>
						<th>OUTLOOK</th>
						<th>2019</th>
						<th>VAR</th>
						<th>VAR %</th>
					</tr>
				</thead>
				<tbody width='100%' name='data' id='data'>
				</tbody>
				</table>
		</fieldset>
	<div>
		<br /><br />
	</div>
		<fieldset>
				<table width='100%' border="0" cellpadding="1" cellspacing="1">
				<thead>
					<tr>
						<th style="width:50%;">Dibuat Oleh:</th>
						<th>Disetujui Oleh:</th>
					</tr>
				</thead>
				<tbody width='100%'>
					<tr>
						<td style="text-align:center;"><span id="division_approval"></span></td>
						<td style="text-align:center;"><span id="cost_center_creator"></span></td>
					</tr>
				</tbody>
				</table>
		</fieldset>
	</div>
	<br /><br />
	<div>
		<div style="text-align:center;"><button type="button" id="btn_simpan">SUBMIT</button> <button type="button" id="btn_print">PRINT</button></div>
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
	$("#btn_unlock").hide();
	$("#btn_lock").hide();
	$('#btn_simpan').hide();
	$('#btn_print').hide();
	//BUTTON ACTION	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		//$('#data').append(tr);
		$('#data tr').remove();
		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var src_div = $('#src_div').val();
		var src_cc = $('#src_cc').val();
		var key_find_cc = $("#key_find_cc").val();				//KODE BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if (key_find_cc == '' || key_find_cc == 'ALL') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else {
			$.ajax({
				type    : "post",
				url     : "ho-valid-submit/list",
				data    : $("#form_init").serialize(),
				cache   : false,
				dataType: "json",
				success : function(data) {
					if (data.return == 'locked') {
						$("#btn_upload").hide();
						$("#btn_save").hide();
						$("#btn_save_temp").hide();
						$("#btn00_").hide();
						alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
					} else {
						var output = data.result.rows;
						count = data.result.count;
						console.log(data.user.HCC_COST_CENTER_HEAD);
						if (count > 0) {
							$.each(output, function(key, row) {
								var tr = '<tr>';
								tr += '<td style="text-align:center;"><input type="hidden" name="output[group_budget][]" value="'+row.GROUP_BUDGET+'">'+row.GROUP_BUDGET+'</td>';
								tr += '<td style="text-align:right;"><input type="hidden" name="output[outlook][]" value="'+row.TOTAL_ACTUAL+'">'+accounting.formatNumber(row.TOTAL_ACTUAL, 2)+'</td>';
								tr += '<td style="text-align:right;"><input type="hidden" name="output[next_budget][]" value="'+row.TOTAL+'">'+accounting.formatNumber(row.TOTAL, 2)+'</td>';
								tr += '<td style="text-align:right;"><input type="hidden" name="output[var_selisih][]" value="'+row.VAR_SELISIH+'">'+accounting.formatNumber(row.VAR_SELISIH, 2)+'</td>';
								tr += '<td style="text-align:center;"><input type="hidden" name="output[var_persen][]" value="'+row.VAR_PERSEN+'">'+row.VAR_PERSEN+'</td>';
								tr += '</tr>';

								$('#data').append(tr);
							});

							$('#data-load tr:last').css('background', '#000066').css('color', 'white').css('font-weight', 'bold');

							$('#division_approval').replaceWith('<span id="division_approval">' + data.user.HCC_COST_CENTER_HEAD + '<br /><br /><br />' + data.user.HCC_COST_CENTER + '</span>');
							$('#cost_center_creator').replaceWith('<span id="cost_center_creator">' + data.user.HCC_DIVISION_HEAD + '<br /><br /><br />' + data.user.DIVISION + '</span>');
							$('#btn_simpan').show();
						}
					}
				}
			});
		}
    });	
	
	$("#btn_simpan").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var src_div = $('#src_div').val();
		var src_cc = $('#src_cc').val();
		var key_find_cc = $("#key_find_cc").val();				//KODE BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if (key_find_cc == '' || key_find_cc == 'ALL') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "ho-valid-submit/save",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					if (data.return == "locked") {
						alert("Anda tidak dapat melakukan perhitungan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".\nData Anda belum tersimpan. Harap mencoba melakukan proses perhitungan beberapa saat lagi.");
					}else if (data.return == "done") {
						alert("Data berhasil divalidasi.");
						//alert(data.isi);
						$("#btn_find").trigger("click");
						$('#btn_print').show();
					}else if (data.return == "donewithexception") {
						alert("Data berhasil dihitung. Mohon Lakukan pemeriksaan data pokok di blok " + data.blok + " Pada RKT Tanam Sisip!");
						$("#btn_find").trigger("click");	
					}else{
						alert(data.return);
					}
				}
			});
		}
    });

    $('#btn_print').click(function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var src_div = $('#src_div').val();
		var src_cc = $('#src_cc').val();
		var key_find_div = $('#key_find_div').val();
		var key_find_cc = $("#key_find_cc").val();				//KODE BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if (key_find_cc == '' || key_find_cc == 'ALL') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else {
			window.open("<?=$_SERVER['PHP_SELF']?>/ho-valid-submit/print/budgetperiod/" + budgetperiod + "/key_find_div/" + key_find_div + "/key_find_cc/" + key_find_cc,'_blank');
			/*$.ajax({
				type     : "post",
				url      : "ho-valid-submit/print",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : 'json',
				success  : function(data) {
					if (data.return == "done") {
						alert("Data berhasil divalidasi.");
						//alert(data.isi);
						$("#btn_find").trigger("click");
						$('#btn_print').show();
					}else if (data.return == "donewithexception") {
						alert("Data berhasil dihitung. Mohon Lakukan pemeriksaan data pokok di blok " + data.blok + " Pada RKT Tanam Sisip!");
						$("#btn_find").trigger("click");	
					}else{
						alert(data.return);
					}
				}
			});		*/
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
		
    $('#pick_div').click(function() {
    	var divcode = $("#key_find").val();
    	popup("pick/ho-division/module/hoValidSubmit/us/<?php echo $this->username; ?>", "pick", 700, 400);
    });
    $('#src_div').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find").val();
    		popup("pick/ho-division/module/hoValidSubmit/us/<?php echo $this->username; ?>", "pick", 700, 400);
    	} else {
    		event.preventDefault();
    	}
    });
		
    $('#pick_cc').click(function() {
    	var divcode = $("#key_find_div").val();
    	popup('pick/cost-center/division/'+divcode+'/us/<?php echo $this->username; ?>', 'pick', 700, 400);
    });
    $('#src_cc').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find_div").val();
    		popup('pick/cost-center/division/'+divcode+'/us/<?php echo $this->username; ?>', 'pick', 700, 400);
    	} else {
    		event.preventDefault();
    	}
    });
		
});

</script>
