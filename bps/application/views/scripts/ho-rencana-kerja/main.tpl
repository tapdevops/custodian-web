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
						<input type="text" name="src_div" id="src_div" value="<?php echo $this->divname; ?>" style="width:200px;" class='filter' readonly="readonly" />
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
			<legend>RENCANA KERJA</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="50%">
						<input type="button" name="btn_upload" id="btn_upload" value="UPLOAD" class="button" />
						<input type="button" name="btn_export_csv" id="btn_export_csv" value="EXPORT DATA" class="button" />					
					</td>
					<td width="50%" align="right">		
						<input type="button" name="btn_unlock" id="btn_unlock" value="UNLOCK" class="button" />
						<input type="button" name="btn_lock" id="btn_lock" value="LOCK" class="button" />
						<input type="button" name="btn_add" id="btn_add" value="TAMBAH BARIS" class="button"/>
						<input type="button" name="btn_save_temp" id="btn_save_temp" value="SIMPAN" class="button" />				
						<input type="button" name="btn_save" id="btn_save" value="SIMPAN" class="button" />
						<input type="button" name="btn_hitung" id="btn_hitung" value="HITUNG" class="button" />
					</td>
				</tr>
			</table>
			<table width='100%' id='mainTable' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr name='content' id='content'>
					<th>+</th>
					<th>x</th>
					<th>PERIODE<BR>BUDGET</th>
					<th>COST<BR>CENTER</th>
					<th>RENCANA<BR>KERJA</th>
					<th>PENJELASAN SINGKAT<BR>RENCANA KERJA</th>
				</tr>
				<tr class='column_number'>
					<th></th>
					<th></th>
					<th>1</th>
					<th>2</th>
					<th>3</th>
					<th>4</th>
				</tr>
			</thead>
			<tbody width="100%" name='data' id='data'>
				<tr name='content' id='content' style="display:none;">
					<!--left freeze panes-->
					<td align='center' width='20px'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center' width='20px'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td width='50px'>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="trxrktcode[]" id="trxrktcode_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly"/>
					</td>
					<td width='200px'>
						<input type="hidden" name="text06[]" id="text06_" readonly="readonly" />
						<input type="text" name="text03[]" id="text03_" style="width:200px;" readonly="readonly"/>
					</td>
					<td width='300px'>
						<input type="text" name="text04[]" id="text04_" style="width:300px;"/>
					</td>
					<td width='300px' >
						<input type="text" name="text05[]" id="text05_" style="width:300px;"/>
					</td>
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
	$('#btn_upload').hide();
	$('#btn_export_csv').hide();
	$("#btn_save").hide();
	$('#btn_hitung').hide();
	$("#btn_unlock").hide();
	$("#btn_lock").hide();

	var current_budgetperiod = "<?=$this->period?>";

	//BUTTON ACTION	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var src_cc = $('#src_cc').val();
		var search = $("#search").val();					//SEARCH FREE TEXT

		var key_find_cc = $('#key_find_cc').val();

		if (key_find_cc == '') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu");
		} else {
			page_num = (page_num) ? page_num : 1;
			getData();
			$('#btn_save').show();
		}
    });	

	//klik tombol add
    $("input[id^=btn00_]").live("click", function(event) {
		$("#btn_add").trigger("click");
    });	

	$("#btn_add").live("click", function(event) {		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var src_cc = $('#src_cc').val();
		var region = $("#src_region").val();				//DESKRIPSI REGION
		var ba_code = $("#src_ba").val();					//DESKRIPSI BA
		var search = $("#search").val();					//SEARCH FREE TEXT

		if (src_cc == '') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else {
			var tr = $("#data tr:eq(0)").clone();
			$("#data").append(tr);
			var index = ($("#data tr").length - 1);
			$("#data tr:eq(" + index + ")").find("input, select").each(function() {
				$('#btn01_' + index).show();
				$(this).attr("id", $(this).attr("id") + index);
				$('#tChange_' + index).val("");
			});		

			//set default field
			setDefaultField(index);
		}
    });

	$("input[id^=btn01_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();    //mapping textfield name terhadap field name di DB
		
		if(confirm("Apakah Anda Yakin Untuk Menghapus Data ke - " + row + " ?")) {
			if (rowid == '') {
				clearTextField(row);
			} else {
				$.ajax({
					type: 'POST',
					url: 'ho-rencana-kerja/delete/rowid/' + encode64(rowid),
					cache: false,
					dataType: 'json',
					success: function(data) {
						if (data.return == "done") {
							clearTextField(row);
							alert('Data berhasil dihapus');

							clearDetail();
							page_num = (page_num) ? page_num : 1;
							getData();
						} else {
							alert(data.return);
						}
					}
				});
			}
		}
    });

	$("#btn_refresh").click(function() {
		location.reload();
    });

	$("#btn_upload").live("click", function() {
		var controller = "upload/ha-statement";
		$("#controller").val(controller);
		popup("upload/main", "detail", 700, 400);
    });

	$("input[id^=btn00_]").live("click", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var rowid = $("#text00_" + row).val();
    });

	$("#btn_save").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find = $("#key_find").val();				//KODE BA
		var key_find_cc = $('#key_find_cc').val();
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if (key_find_cc == '') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu");
		} else if ( validateInput() == false )  {
			alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		} else {
			$.ajax({
				type: 'POST',
				url: 'ho-rencana-kerja/save',
				data: $("#form_init").serialize(),
				cache: false,
				dataType: 'json',
				success: function(data) {
					if (data.return == "done") {
						alert("Data berhasil disimpan.");
						$("#btn_find").trigger("click");
					} else {
						alert(data.return);
					}
				}
			});
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
    	popup("pick/ho-division/module/hoRencanaKerja/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
    });
    $('#src_div').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find").val();
    		popup("pick/ho-division/module/hoRencanaKerja/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
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
	
	//LOV UTK INPUTAN
	$("input[id^=text08_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/topography/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text10_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/land-type/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text12_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/progeny/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text14_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/land-suitability/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text19_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/status-blok-budget/module/haStatement/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });

});

function setDefaultField(index){
	//DEKLARASI VARIABEL
	var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
	var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
	var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
	var key_find_cc = $('#key_find_cc').val();
	var src_cc = $('#src_cc').val();
	var val_cc = src_cc;
	var search = $("#search").val();					//SEARCH FREE TEXT
	
	//left freeze panes
	$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
	$("#data tr:eq(" + index + ") input[id^=trxrktcode_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
	$("#data tr:eq(" + index + ") input[id^=text06_]").val(key_find_cc);
	$("#data tr:eq(" + index + ") input[id^=text03_]").val(val_cc);
	$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text04_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("required");
	$("#data tr:eq(" + index + ")").removeAttr("style");
	
	$("#data tr:eq(" + index + ") input[id^=text04_]").focus();
}

function getData(){
    $("#page_num").val(page_num);	
	
	var user_role = "<?=$this->userrole?>";
    //
    $.ajax({
        type    : "post",
        url     : "ho-rencana-kerja/list",
        data    : $("#form_init").serialize(),
        cache   : false,
        dataType: "json",
        success : function(data) {
			if (data.return == 'locked') {
				$("#btn_upload").hide();
				//$("#btn_save").hide();
				$("#btn_save_temp").hide();
				$("#btn00_").hide();
				alert("Anda tidak dapat melakukan perubahan data karena sedang terjadi proses perhitungan "+ data.module +" oleh "+ data.insert_user +".");
			} else {
				count = data.count;
				if (count < 1) {
					alert("Belum Ada Data");
					$("#btn_add").trigger("click");
				} else {
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
							
							$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
							$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
							
							$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
							$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
							$("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);

							$("#data tr:eq(" + index + ") input[id^=text03_]").val(row.HCC_CC + ' - ' + row.HCC_COST_CENTER);
							$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.RK_NAME);
							$("#data tr:eq(" + index + ") input[id^=text04_]").addClass("required");
							$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.RK_DESCRIPTION);
							$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("required");
							
							$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
							$("#data tr:eq(" + index + ")").removeAttr("style");
							
							$("#data tr:eq(1) input[id^=text02_]").focus();
						});
					}
				}
			}
        }
    });
}
</script>
