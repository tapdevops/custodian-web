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
			<legend>SPD HO</legend>
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
			<div id='scrollarea'>
			<table width='100%' border="0" cellpadding="1" cellspacing="1" class='data_header'>
			<thead>
				<tr>
					<th rowspan='2'>+</th>
					<th rowspan='2'>x</th>
					<th rowspan='2'>PERIODE<BR>BUDGET</th>
					<th rowspan="2">COST<BR>CENTER</th>
					<th rowspan='2'>RENCANA<BR>KERJA</th>
					<th rowspan='2'>KETERANGAN<BR>SPD</th>
					<th rowspan='2'>ACCOUNT</th>
					<th rowspan='2'>ACCOUNT<BR>DESCRIPTION</th>
					<th rowspan='2'>RUTE</th>
					<th rowspan='2'>CORE</th>
					<th rowspan='2'>BEBAN PT<BR>(TUJUAN)</th>
					<th rowspan='2'>BA<BR>TUJUAN</th>
					<th rowspan='2'>TIPE</th>
					<th rowspan='2'>RENCANA</th>
					<th rowspan='2'>GOLONGAN</th>
					<th colspan='2'>JLH</th>
					<th rowspan='2'>JLH<BR>HARI</th>
					<th rowspan='2'>TICKET</th>
					<th rowspan='2'>TRANSPORT<BR>LAIN-LAIN</th>
					<th rowspan='2'>UANG<BR>MAKAN</th>
					<th rowspan='2'>UANG<BR>SAKU</th>
					<th colspan='2'>HOTEL</th>
					<th rowspan='2'>OTHERS</th>
					<th rowspan='2'>TOTAL</th>
					<th rowspan='2'>REMARKS<BR>OTHERS</th>
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
					<th rowspan='2'>TOTAL</th>
				</tr>
				<tr>
					<th>PRIA</th>
					<th>WANITA</th>
					<th>HARI</th>
					<th>TARIF</th>
					<th>JAN</th>
					<th>FEB</th>
					<th>MAR</th>
					<th>APR</th>
					<th>MAY</th>
					<th>JUN</th>
					<th>JUL</th>
					<th>AUG</th>
					<th>SEP</th>
					<th>OCT</th>
					<th>NOV</th>
					<th>DEC</th>
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
					<th>21</th>
					<th>22</th>
					<th>23</th>
					<th>24</th>
					<th>25</th>
					<th>26</th>
					<th>27</th>
					<th>28</th>
					<th>29</th>
					<th>30</th>
					<th>31</th>
					<th>32</th>
					<th>33</th>
					<th>34</th>
					<th>35</th>
					<th>36</th>
					<th>37</th>
					<th>38</th>
					<th>39</th>
					<th>40</th>
				</tr>
			</thead>
			<tbody width='100%' name='data' id='data'>
				<tr style="display:none">
					<td align='center' width='20px'>
						<input type="button" name="btn00[]" id="btn00_" class='button_add'/>
					</td>
					<td align='center' width='20px'>
						<input type="button" name="btn01[]" id="btn01_" class='button_delete'/>
					</td>
					<td>
						<input type="hidden" name="text00[]" id="text00_" readonly="readonly"/>
						<input type="hidden" name="text01[]" id="text01_" readonly="readonly"/>
						<input type="hidden" name="tChange[]" id="tChange_" readonly="readonly"/>
						<input type="text" name="text02[]" id="text02_" readonly="readonly" style='width:50px'/>
					</td>
					<td><input type="text" name="text03[]" id="text03_" readonly="readonly" style='width:200px'/></td>
					<td>
						<input type="hidden" name="text04[]" id="text04_" style='width:50px'/>
						<input type="text" name="text05[]" id="text05_" style='width:200px'/>
					</td>
					<td><input type="text" name="text06[]" id="text06_" style='width:200px'/></td>
					<td><input type="text" name="text07[]" id="text07_" style='width:75px' title="Tekan F9 Untuk Memilih."/></td>
					<td><input type="text" name="text08[]" id="text08_" readonly="readonly" style='width:150px'/></td>
					<td>
						<input type="hidden" name="text09[]" id="text09_" style='width:100px'/>
						<input type="text" name="text10[]" id="text10_" style='width:150px' title="Tekan F9 Untuk Memilih."/>
						<input type="hidden" name="text51[]" id="text51_" style='width:20px'>
						<input type="hidden" name="text52[]" id="text52_" style='width:40px'>
						<input type="hidden" name="text53[]" id="text53_" style='width:20px'>
						<input type="hidden" name="text54[]" id="text54_" style='width:40px'>
						<input type="hidden" name="text55[]" id="text55_" style='width:20px'>
						<input type="hidden" name="text56[]" id="text56_" style='width:40px'>
						<input type="hidden" name="text57[]" id="text57_" style='width:20px'>
						<input type="hidden" name="text58[]" id="text58_" style='width:40px'>
					</td>
					<td>
						<input type="hidden" name="text11[]" id="text11_" style='width:100px'/>
						<input type="text" name="text12[]" id="text12_" style='width:50px' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td>
						<input type="hidden" name="text13[]" id="text13_" style='width:100px'/>
						<input type="text" name="text14[]" id="text14_" style='width:150px' title="Tekan F9 Untuk Memilih."/>
					</td>
					<td>
						<input type="hidden" name="text15[]" id="text15_" readonly="readonly" style='width:50px'/>
						<input type="text" name="text16[]" id="text16_" readonly="readonly" style='width:50px'/>
					</td>
					<td>
						<select name="text50[]" id="text50_">
							<option value="UMUM">UMUM</option>
							<option value="KHUSUS">KHUSUS</option>
						</select>
					</td>
					<td>
						<!--<input type="text" name="text17[]" id="text17_" style='width:50px'/>-->
						<select name="text17[]" id="text17_">
							<option value="1">JAN</option>
							<option value="2">FEB</option>
							<option value="3">MAR</option>
							<option value="4">APR</option>
							<option value="5">MAY</option>
							<option value="6">JUN</option>
							<option value="7">JUL</option>
							<option value="8">AUG</option>
							<option value="9">SEP</option>
							<option value="10">OCT</option>
							<option value="11">NOV</option>
							<option value="12">DEC</option>
						</select>
					</td>
					<td><input type="text" name="text18[]" id="text18_" style='width:50px; text-align: right;'/></td>
					<td><input type="text" name="text19[]" id="text19_" style='width:50px; text-align: right;'/></td>
					<td><input type="text" name="text20[]" id="text20_" style='width:50px; text-align: right;'/></td>
					<td><input type="text" name="text21[]" id="text21_" style='width:50px; text-align: right;'/></td>
					<td>
						<input type="hidden" name="text22[]" id="text22_" readonly="readonly" style='width:100px' class="number"/>
						<input type="text" name="text23[]" id="text23_" readonly="readonly" style='width:100px' class="number"/>
					</td>
					<td>
						<input type="hidden" name="text24[]" id="text24_" readonly="readonly" style='width:100px' class="number"/>
						<input type="text" name="text25[]" id="text25_" readonly="readonly" style='width:100px' class="number"/>
					</td>
					<td>
						<input type="hidden" name="text26[]" id="text26_" readonly="readonly" style='width:100px' class="number"/>
						<input type="text" name="text27[]" id="text27_" readonly="readonly" style='width:100px' class="number"/>
					</td>
					<td>
						<input type="hidden" name="text28[]" id="text28_" readonly="readonly" style='width:100px' class="number"/>
						<input type="text" name="text29[]" id="text29_" readonly="readonly" style='width:100px' class="number"/>
					</td>
					<td><input type="text" name="text30[]" id="text30_" style='width:50px; text-align: right;'/></td>
					<td>
						<input type="hidden" name="text31[]" id="text31_" readonly="readonly" style='width:100px;' class="number"/>
						<input type="text" name="text32[]" id="text32_" readonly="readonly" style='width:100px;' class="number"/>
					</td>
					<td><input type="text" name="text33[]" id="text33_" style='width:100px' class="number"/></td>
					<td><input type="text" name="text34[]" id="text34_" readonly="readonly" style='width:100px' class="number"/></td>
					<td><input type="text" name="text35[]" id="text35_" style='width:150px'/></td>
					<td><input type="text" name="text36[]" id="text36_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text37[]" id="text37_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text38[]" id="text38_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text39[]" id="text39_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text40[]" id="text40_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text41[]" id="text41_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text42[]" id="text42_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text43[]" id="text43_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text44[]" id="text44_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text45[]" id="text45_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text46[]" id="text46_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text47[]" id="text47_" readonly="readonly" style='width:100px' class="number sebaran"/></td>
					<td><input type="text" name="text48[]" id="text48_" readonly="readonly" style='width:100px' class="number"/></td>
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
	$('#btn_hitung').hide();

	$('#btn_upload').hide();
	$('#btn_export_csv').hide();


	//BUTTON ACTION	
    $("#btn_find").click(function() {
		//clear data
		clearDetail();
		
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find_cc = $("#key_find_cc").val();				//KODE BA
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if (key_find_cc == '') {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
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

    $('#btn_add').click(function() {
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
	
	$("#btn_refresh").click(function() {
		location.reload();
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
					url: 'ho-spd/delete/rowid/' + encode64(rowid),
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

	$("#btn_save").click( function() {
		//DEKLARASI VARIABEL
		var reference_role = "<?=$this->referencerole?>";	//REFERENCE_ROLE
		var budgetperiod = $("#budgetperiod").val();		//PERIODE BUDGET
		var current_budgetperiod = "<?=$this->period?>";	//PERIODE BUDGET SEBELUMNYA / TAHUN BERJALAN
		var src_region_code = $("#src_region_code").val();	//KODE REGION
		var key_find_cc = $('#key_find_cc').val();
		var search = $("#search").val();					//SEARCH FREE TEXT
		
		if ( ( key_find_cc == '' ) ) {
			alert("Anda Harus Memilih Cost Center Terlebih Dahulu.");
		} else if ( validateInput() == false )  {
			alert("Field yang Berwarna Merah, Harus Diisi Terlebih Dahulu.");
		} else {
			$.ajax({
				type     : "post",
				url      : "ho-spd/save",
				data     : $("#form_init").serialize(),
				cache    : false,
				dataType : 'json',
				success  : function(data) {
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
    	popup("pick/ho-division/module/hoSpd/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
    });
    $('#src_div').live('keydown', function(event) {
    	if (event.keyCode == 120) {
    		var divcode = $("#key_find").val();
    		popup("pick/ho-division/module/hoSpd/ac/<?php echo $newdiv; ?>/us/<?php echo $this->username; ?>", "pick", 700, 400);
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
	$("input[id^=text05_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var cc = $('#key_find_cc').val();
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-rencana-kerja/module/hoSpd/cc/" + cc + "/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text07_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-coa/module/hoSpd/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text10_]").live("change keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-norma-spd/module/hoSpd/row/" + row, "pick");
        }else{
			event.preventDefault();
		}

		var tipe = $('#text50_' + row).val();

    	var pria = parseInt($('#text19_' + row).val());
    	var wanita = parseInt($('#text20_' + row).val());

    	if (pria == '' || isNaN(pria)) pria = 0;
    	if (wanita == '' || isNaN(wanita)) wanita = 0;

    	var jlhpeserta = pria + wanita;

    	var hari_kerja = parseInt($('#text21_' + row).val());
    	var hari_inap = parseInt($('#text30_' + row).val());

    	if (hari_kerja == '' || isNaN(hari_kerja)) hari_kerja = 0;
    	if (hari_inap == '' || isNaN(hari_inap)) hari_inap = 0;

    	var gol = parseInt($('#text18_'+ row).val());
    	if (gol < 6) {
    		var tiket = parseInt($('#text51_' + row).val());
    		if (hari_kerja > 1) {
    			var kali_uang_makan = 6;
    		} else {
    			var kali_uang_makan = 3;
    		}
    	} else {
    		var tiket = parseInt($('#text52_' + row).val());
    		var kali_uang_makan = 1;
    	}

    	//var tiket = parseInt($('#text22_' + row).val().replace(/,/g, ''));
    	var transport_lain = parseInt($('#text24_' + row).val().replace(/,/g, ''));
    	var uang_makan = parseInt($('#text26_' + row).val().replace(/,/g, ''));
    	var uang_saku = parseInt($('#text28_' + row).val().replace(/,/g, ''));
    	var tarif_hotel = parseInt($('#text31_' + row).val().replace(/,/g, ''));
    	var biaya_other = parseInt($('#text33_' + row).val().replace(/,/g, ''));

    	var qty_taksi = parseInt($('#text53_' + row).val().replace(/,/g, ''));
    	var harga_taksi = parseInt($('#text54_' + row).val().replace(/,/g, ''));
    	if (qty_taksi == '0') {
    		var total_taksi = 0;
    	} else {
    		var round_taksi = Math.ceil(jlhpeserta / qty_taksi);
    		var total_taksi = Math.ceil(round_taksi * harga_taksi);
    	}

    	var qty_charter = parseInt($('#text55_' + row).val().replace(/,/g, ''));
    	var harga_charter = parseInt($('#text56_' + row).val().replace(/,/g, ''));
    	if (qty_charter == '0') {
    		var total_charter = 0;
    	} else {
    		var round_charter = Math.ceil(jlhpeserta / qty_charter);
    		var total_charter = Math.ceil(round_charter * harga_charter);
    	}

    	var qty_air = parseInt($('#text57_' + row).val().replace(/,/g, ''));
    	var harga_air = parseInt($('#text58_' + row).val().replace(/,/g, ''));
    	if (qty_air == '0') {
    		var total_air = 0;
    	} else {
    		var round_air = Math.ceil(jlhpeserta / qty_air);
    		var total_air = Math.ceil(round_air * harga_air);
    	}

    	if (biaya_other == '' || isNaN(biaya_other)) biaya_other = 0;

    	var total_tiket = jlhpeserta * tiket;
    	if (tipe == 'KHUSUS') {
    		//var total_transport_lain = total_taksi + total_charter + total_air;
	    	//var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	//var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		var total_transport_lain = parseInt($('#text25_' + row).val().replace(/,/g, ''));
    		var total_uang_makan = parseInt($('#text27_' + row).val().replace(/,/g, ''));
    		var total_uang_saku = parseInt($('#text29_' + row).val().replace(/,/g, ''));
    	} else {
	    	//var total_transport_lain = jlhpeserta * hari_kerja * transport_lain;
	    	var total_transport_lain = total_taksi + total_charter + total_air;
	    	var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    	}
    	var total_hotel = jlhpeserta * hari_inap * tarif_hotel;

    	$('#text23_' + row).val(accounting.formatNumber(total_tiket, 2));
    	$('#text25_' + row).val(accounting.formatNumber(total_transport_lain, 2));
    	$('#text27_' + row).val(accounting.formatNumber(total_uang_makan, 2));
    	$('#text29_' + row).val(accounting.formatNumber(total_uang_saku, 2));

    	$('#text32_' + row).val(accounting.formatNumber(total_hotel, 2));

    	var total = total_tiket + total_transport_lain + total_uang_makan + total_uang_saku + total_hotel + biaya_other;
    	$('#text34_' + row).val(accounting.formatNumber(total, 2));

    	var monthly = $('#text17_' + row).val();
    	if (monthly == '1') {
    		//$('.sebaran').val(accounting.formatNumber(0, 2));
    		$('#text36_' + row).val(accounting.formatNumber(total, 2));
    		$("#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '2') {
    		$('#text37_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '3') {
    		$('#text38_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '4') {
    		$('#text39_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '5') {
    		$('#text40_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '6') {
    		$('#text41_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '7') {
    		$('#text42_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '8') {
    		$('#text43_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '9') {
    		$('#text44_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '10') {
    		$('#text45_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '11') {
    		$('#text46_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '12') {
    		$('#text47_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row).val(accounting.formatNumber(0, 2));
    	} else {
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	}

    	/*$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).each(function() {
    		if ($(this).val() != '' && $(this).val() != 0.00) {
    			var sebaran_id = $(this).attr('id');
    			$('#' + sebaran_id).val(accounting.formatNumber(total, 2));
    		}
    	});*/

    	$('#text48_' + row).val(accounting.formatNumber(
    		parseInt($('#text36_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text37_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text38_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text39_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text40_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text41_' + row).val().replace(/,/g, '')) +
    		parseInt($('#text42_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text43_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text44_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text45_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text46_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text47_' + row).val().replace(/,/g, '')), 
    	2));
    });
	$("input[id^=text12_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-core/module/hoSpd/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
	$("input[id^=text14_]").live("keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var core = $('#text12_' + row).val();
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-company/module/hoSpd/core/" + core + "/row/" + row, "pick");
        }else{
			event.preventDefault();
		}
    });
    $("select[id^=text17_]").live("change", function(event) {
    	var row = $(this).attr("id").split("_")[1];
    	var total = parseInt($('#text34_' + row).val().replace(/,/g, ''));

    	var monthly = $('#text17_' + row).val();

    	if (monthly == '1') {
    		//$('.sebaran').val(accounting.formatNumber(0, 2));
    		$('#text36_' + row).val(accounting.formatNumber(total, 2));
    		$("#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '2') {
    		$('#text37_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '3') {
    		$('#text38_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '4') {
    		$('#text39_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '5') {
    		$('#text40_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '6') {
    		$('#text41_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '7') {
    		$('#text42_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '8') {
    		$('#text43_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '9') {
    		$('#text44_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '10') {
    		$('#text45_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '11') {
    		$('#text46_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '12') {
    		$('#text47_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row).val(accounting.formatNumber(0, 2));
    	} else {
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	}
    });
	$("input[id^=text18_]").live("change keydown", function(event) {
		var row = $(this).attr("id").split("_")[1];
		//tekan F9
        if (event.keyCode == 120) {
			//lov
			popup("pick/ho-standar-spd/module/hoSpd/row/" + row, "pick");
        }else{
			event.preventDefault();
		}

		var tipe = $('#text50_' + row).val();

    	var pria = parseInt($('#text19_' + row).val());
    	var wanita = parseInt($('#text20_' + row).val());

    	if (pria == '' || isNaN(pria)) pria = 0;
    	if (wanita == '' || isNaN(wanita)) wanita = 0;

    	var jlhpeserta = pria + wanita;

    	var hari_kerja = parseInt($('#text21_' + row).val());
    	var hari_inap = parseInt($('#text30_' + row).val());

    	if (hari_kerja == '' || isNaN(hari_kerja)) hari_kerja = 0;
    	if (hari_inap == '' || isNaN(hari_inap)) hari_inap = 0;

    	var gol = parseInt($('#text18_'+ row).val());
    	//alert(gol);
    	if (gol < 6) {
    		var tiket = parseInt($('#text51_' + row).val());
    		if (hari_kerja > 1) {
    			var kali_uang_makan = 6;
    		} else {
    			var kali_uang_makan = 3;
    		}
    	} else {
    		var tiket = parseInt($('#text52_' + row).val());
    		var kali_uang_makan = 1;
    	}

    	//var tiket = parseInt($('#text22_' + row).val().replace(/,/g, ''));
    	var transport_lain = parseInt($('#text24_' + row).val().replace(/,/g, ''));
    	var uang_makan = parseInt($('#text26_' + row).val().replace(/,/g, ''));
    	var uang_saku = parseInt($('#text28_' + row).val().replace(/,/g, ''));
    	var tarif_hotel = parseInt($('#text31_' + row).val().replace(/,/g, ''));
    	var biaya_other = parseInt($('#text33_' + row).val().replace(/,/g, ''));

    	var qty_taksi = parseInt($('#text53_' + row).val().replace(/,/g, ''));
    	var harga_taksi = parseInt($('#text54_' + row).val().replace(/,/g, ''));
    	if (qty_taksi == '0') {
    		var total_taksi = 0;
    	} else {
    		var round_taksi = Math.ceil(jlhpeserta / qty_taksi);
    		var total_taksi = Math.ceil(round_taksi * harga_taksi);
    	}

    	var qty_charter = parseInt($('#text55_' + row).val().replace(/,/g, ''));
    	var harga_charter = parseInt($('#text56_' + row).val().replace(/,/g, ''));
    	if (qty_charter == '0') {
    		var total_charter = 0;
    	} else {
    		var round_charter = Math.ceil(jlhpeserta / qty_charter);
    		var total_charter = Math.ceil(round_charter * harga_charter);
    	}

    	var qty_air = parseInt($('#text57_' + row).val().replace(/,/g, ''));
    	var harga_air = parseInt($('#text58_' + row).val().replace(/,/g, ''));
    	if (qty_air == '0') {
    		var total_air = 0;
    	} else {
    		var round_air = Math.ceil(jlhpeserta / qty_air);
    		var total_air = Math.ceil(round_air * harga_air);
    	}

    	if (biaya_other == '' || isNaN(biaya_other)) biaya_other = 0;

    	var total_tiket = jlhpeserta * tiket;
    	if (tipe == 'KHUSUS') {
    		var total_transport_lain = total_taksi + total_charter + total_air;
	    	//var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	//var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		//var total_transport_lain = parseInt($('#text24_' + row).val().replace(/,/g, ''));
    		var total_uang_makan = parseInt($('#text26_' + row).val().replace(/,/g, ''));
    		var total_uang_saku = parseInt($('#text28_' + row).val().replace(/,/g, ''));
    	} else {
	    	//var total_transport_lain = jlhpeserta * hari_kerja * transport_lain;
	    	var total_transport_lain = total_taksi + total_charter + total_air;
	    	var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    	}
    	var total_hotel = jlhpeserta * hari_inap * tarif_hotel;

    	$('#text23_' + row).val(accounting.formatNumber(total_tiket, 2));
    	$('#text25_' + row).val(accounting.formatNumber(total_transport_lain, 2));
    	$('#text27_' + row).val(accounting.formatNumber(total_uang_makan, 2));
    	$('#text29_' + row).val(accounting.formatNumber(total_uang_saku, 2));

    	$('#text32_' + row).val(accounting.formatNumber(total_hotel, 2));

    	var total = total_tiket + total_transport_lain + total_uang_makan + total_uang_saku + total_hotel + biaya_other;
    	$('#text34_' + row).val(accounting.formatNumber(total, 2));

    	var monthly = $('#text17_' + row).val();
    	if (monthly == '1') {
    		//$('.sebaran').val(accounting.formatNumber(0, 2));
    		$('#text36_' + row).val(accounting.formatNumber(total, 2));
    		$("#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '2') {
    		$('#text37_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '3') {
    		$('#text38_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '4') {
    		$('#text39_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '5') {
    		$('#text40_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '6') {
    		$('#text41_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '7') {
    		$('#text42_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '8') {
    		$('#text43_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '9') {
    		$('#text44_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '10') {
    		$('#text45_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '11') {
    		$('#text46_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '12') {
    		$('#text47_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row).val(accounting.formatNumber(0, 2));
    	} else {
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	}

    	/*$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).each(function() {
    		if ($(this).val() != '' && $(this).val() != 0.00) {
    			var sebaran_id = $(this).attr('id');
    			$('#' + sebaran_id).val(accounting.formatNumber(total, 2));
    		}
    	});*/

    	$('#text48_' + row).val(accounting.formatNumber(
    		parseInt($('#text36_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text37_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text38_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text39_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text40_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text41_' + row).val().replace(/,/g, '')) +
    		parseInt($('#text42_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text43_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text44_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text45_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text46_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text47_' + row).val().replace(/,/g, '')), 
    	2));

    });

    $("input[id^=text19_], input[id^=text20_], input[id^=text21_], input[id^=text25_], input[id^=text27_], input[id^=text29_], input[id^=text30_], input[id^=text33_]").live("change", function(event) {
    	//input change blur keydown
    	var row = $(this).attr("id").split("_")[1];

    	var tipe = $('#text50_' + row).val();

    	var pria = parseInt($('#text19_' + row).val());
    	var wanita = parseInt($('#text20_' + row).val());

    	if (pria == '' || isNaN(pria)) pria = 0;
    	if (wanita == '' || isNaN(wanita)) wanita = 0;

    	var jlhpeserta = pria + wanita;

    	var hari_kerja = parseInt($('#text21_' + row).val());
    	var hari_inap = parseInt($('#text30_' + row).val());

    	if (hari_kerja == '' || isNaN(hari_kerja)) hari_kerja = 0;
    	if (hari_inap == '' || isNaN(hari_inap)) hari_inap = 0;

    	var gol = parseInt($('#text18_'+ row).val());
    	if (gol < 6) {
    		var tiket = parseInt($('#text51_' + row).val());
    		if (hari_kerja > 1) {
    			var kali_uang_makan = 6;
    		} else {
    			var kali_uang_makan = 3;
    		}
    	} else {
    		var tiket = parseInt($('#text52_' + row).val());
    		var kali_uang_makan = 1;
    	}

    	//var tiket = parseInt($('#text22_' + row).val().replace(/,/g, ''));
    	var transport_lain = parseInt($('#text24_' + row).val().replace(/,/g, ''));
    	var uang_makan = parseInt($('#text26_' + row).val().replace(/,/g, ''));
    	var uang_saku = parseInt($('#text28_' + row).val().replace(/,/g, ''));
    	var tarif_hotel = parseInt($('#text31_' + row).val().replace(/,/g, ''));
    	var biaya_other = parseInt($('#text33_' + row).val().replace(/,/g, ''));

    	var qty_taksi = parseInt($('#text53_' + row).val().replace(/,/g, ''));
    	var harga_taksi = parseInt($('#text54_' + row).val().replace(/,/g, ''));
    	if (qty_taksi == '0') {
    		var total_taksi = 0;
    	} else {
    		var round_taksi = Math.ceil(jlhpeserta / qty_taksi);
    		var total_taksi = Math.ceil(round_taksi * harga_taksi);
    	}

    	var qty_charter = parseInt($('#text55_' + row).val().replace(/,/g, ''));
    	var harga_charter = parseInt($('#text56_' + row).val().replace(/,/g, ''));
    	if (qty_charter == '0') {
    		var total_charter = 0;
    	} else {
    		var round_charter = Math.ceil(jlhpeserta / qty_charter);
    		var total_charter = Math.ceil(round_charter * harga_charter);
    	}

    	var qty_air = parseInt($('#text57_' + row).val().replace(/,/g, ''));
    	var harga_air = parseInt($('#text58_' + row).val().replace(/,/g, ''));
    	if (qty_air == '0') {
    		var total_air = 0;
    	} else {
    		var round_air = Math.ceil(jlhpeserta / qty_air);
    		var total_air = Math.ceil(round_air * harga_air);
    	}

    	if (biaya_other == '' || isNaN(biaya_other)) biaya_other = 0;

    	var total_tiket = jlhpeserta * tiket;
    	if (tipe == 'KHUSUS') {
    		//var total_transport_lain = total_taksi + total_charter + total_air;
	    	//var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	//var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		var total_transport_lain = parseInt($('#text25_' + row).val().replace(/,/g, ''));
	    	var total_uang_makan = parseInt($('#text27_' + row).val().replace(/,/g, ''));
    		var total_uang_saku = parseInt($('#text29_' + row).val().replace(/,/g, ''));
    	} else {
	    	//var total_transport_lain = jlhpeserta * hari_kerja * transport_lain;
	    	var total_transport_lain = total_taksi + total_charter + total_air;
	    	var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    	}
    	var total_hotel = jlhpeserta * hari_inap * tarif_hotel;

    	$('#text23_' + row).val(accounting.formatNumber(total_tiket, 2));
    	$('#text25_' + row).val(accounting.formatNumber(total_transport_lain, 2));
    	$('#text27_' + row).val(accounting.formatNumber(total_uang_makan, 2));
    	$('#text29_' + row).val(accounting.formatNumber(total_uang_saku, 2));

    	$('#text32_' + row).val(accounting.formatNumber(total_hotel, 2));

    	var total = total_tiket + total_transport_lain + total_uang_makan + total_uang_saku + total_hotel + biaya_other;
    	$('#text34_' + row).val(accounting.formatNumber(total, 2));

    	var monthly = $('#text17_' + row).val();
    	if (monthly == '1') {
    		//$('.sebaran').val(accounting.formatNumber(0, 2));
    		$('#text36_' + row).val(accounting.formatNumber(total, 2));
    		$("#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '2') {
    		$('#text37_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '3') {
    		$('#text38_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '4') {
    		$('#text39_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '5') {
    		$('#text40_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '6') {
    		$('#text41_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '7') {
    		$('#text42_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '8') {
    		$('#text43_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '9') {
    		$('#text44_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '10') {
    		$('#text45_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '11') {
    		$('#text46_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '12') {
    		$('#text47_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row).val(accounting.formatNumber(0, 2));
    	} else {
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	}

    	/*$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).each(function() {
    		if ($(this).val() != '' && $(this).val() != 0.00) {
    			var sebaran_id = $(this).attr('id');
    			$('#' + sebaran_id).val(accounting.formatNumber(total, 2));
    		}
    	});*/

    	$('#text48_' + row).val(accounting.formatNumber(
    		parseInt($('#text36_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text37_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text38_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text39_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text40_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text41_' + row).val().replace(/,/g, '')) +
    		parseInt($('#text42_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text43_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text44_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text45_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text46_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text47_' + row).val().replace(/,/g, '')), 
    	2));

    	if (tipe == 'KHUSUS') {
    		//var default_uang_trans = jlhpeserta * hari_kerja * transport_lain;
    		var default_uang_trans = total_taksi + total_charter + total_air;
    		if (total_transport_lain > default_uang_trans) {
    			alert('Uang transport lain-lain tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_trans, 2));
    			$('#text25_' + row).val('0.00');
    			$('#text25_' + row).focus();
    		}

    		var default_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
    		if (total_uang_makan > default_uang_makan) {
    			alert('Uang makan tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_makan, 2));
    			$('#text27_' + row).val('0.00');
    			$('#text27_' + row).focus();
    		}

    		var default_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		if (total_uang_saku > default_uang_saku) {
    			alert('Uang saku tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_saku, 2));
    			$('#text29_' + row).val('0.00');
    			$('#text29_' + row).focus();
    		}
    	}
    });

	$("select[id^=text50_]").live("change", function(event) {
		var row = $(this).attr("id").split("_")[1];
		var tipe = $('#text50_' + row).val();
		
		var row = $(this).attr("id").split("_")[1];
		var tipe = $('#text50_' + row).val();
    	
    	var pria = parseInt($('#text19_' + row).val());
    	var wanita = parseInt($('#text20_' + row).val());

    	if (pria == '' || isNaN(pria)) pria = 0;
    	if (wanita == '' || isNaN(wanita)) wanita = 0;

    	var jlhpeserta = pria + wanita;

    	var hari_kerja = parseInt($('#text21_' + row).val());
    	var hari_inap = parseInt($('#text30_' + row).val());

    	if (hari_kerja == '' || isNaN(hari_kerja)) hari_kerja = 0;
    	if (hari_inap == '' || isNaN(hari_inap)) hari_inap = 0;

    	var gol = parseInt($('#text18_'+ row).val());
    	if (gol < 6) {
    		var tiket = parseInt($('#text51_' + row).val());
    		if (hari_kerja > 1) {
    			var kali_uang_makan = 6;
    		} else {
    			var kali_uang_makan = 3;
    		}
    	} else {
    		var tiket = parseInt($('#text52_' + row).val());
    		var kali_uang_makan = 1;
    	}

    	//var tiket = parseInt($('#text22_' + row).val().replace(/,/g, ''));
    	var transport_lain = parseInt($('#text24_' + row).val().replace(/,/g, ''));
    	var uang_makan = parseInt($('#text26_' + row).val().replace(/,/g, ''));
    	var uang_saku = parseInt($('#text28_' + row).val().replace(/,/g, ''));
    	var tarif_hotel = parseInt($('#text31_' + row).val().replace(/,/g, ''));
    	var biaya_other = parseInt($('#text33_' + row).val().replace(/,/g, ''));

    	var qty_taksi = parseInt($('#text53_' + row).val().replace(/,/g, ''));
    	var harga_taksi = parseInt($('#text54_' + row).val().replace(/,/g, ''));
    	if (qty_taksi == '0') {
    		var total_taksi = 0;
    	} else {
    		var round_taksi = Math.ceil(jlhpeserta / qty_taksi);
    		var total_taksi = Math.ceil(round_taksi * harga_taksi);
    	}

    	var qty_charter = parseInt($('#text55_' + row).val().replace(/,/g, ''));
    	var harga_charter = parseInt($('#text56_' + row).val().replace(/,/g, ''));
    	if (qty_charter == '0') {
    		var total_charter = 0;
    	} else {
    		var round_charter = Math.ceil(jlhpeserta / qty_charter);
    		var total_charter = Math.ceil(round_charter * harga_charter);
    	}

    	var qty_air = parseInt($('#text57_' + row).val().replace(/,/g, ''));
    	var harga_air = parseInt($('#text58_' + row).val().replace(/,/g, ''));
    	if (qty_air == '0') {
    		var total_air = 0;
    	} else {
    		var round_air = Math.ceil(jlhpeserta / qty_air);
    		var total_air = Math.ceil(round_air * harga_air);
    	}

    	if (biaya_other == '' || isNaN(biaya_other)) biaya_other = 0;

    	var total_tiket = jlhpeserta * tiket;
    	if (tipe == 'KHUSUS') {
    		//var total_transport_lain = total_taksi + total_charter + total_air;
	    	//var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	//var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		var total_transport_lain = parseInt($('#text25_' + row).val().replace(/,/g, ''));
    		var total_uang_makan = parseInt($('#text27_' + row).val().replace(/,/g, ''));
    		var total_uang_saku = parseInt($('#text29_' + row).val().replace(/,/g, ''));
    	} else {
	    	//var total_transport_lain = jlhpeserta * hari_kerja * transport_lain;
	    	var total_transport_lain = total_taksi + total_charter + total_air;
	    	var total_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
	    	var total_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    	}
    	var total_hotel = jlhpeserta * hari_inap * tarif_hotel;

    	$('#text23_' + row).val(accounting.formatNumber(total_tiket, 2));
    	$('#text25_' + row).val(accounting.formatNumber(total_transport_lain, 2));
    	$('#text27_' + row).val(accounting.formatNumber(total_uang_makan, 2));
    	$('#text29_' + row).val(accounting.formatNumber(total_uang_saku, 2));

    	$('#text32_' + row).val(accounting.formatNumber(total_hotel, 2));

    	var total = total_tiket + total_transport_lain + total_uang_makan + total_uang_saku + total_hotel + biaya_other;
    	$('#text34_' + row).val(accounting.formatNumber(total, 2));

    	var monthly = $('#text17_' + row).val();
    	if (monthly == '1') {
    		//$('.sebaran').val(accounting.formatNumber(0, 2));
    		$('#text36_' + row).val(accounting.formatNumber(total, 2));
    		$("#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '2') {
    		$('#text37_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '3') {
    		$('#text38_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '4') {
    		$('#text39_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '5') {
    		$('#text40_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '6') {
    		$('#text41_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '7') {
    		$('#text42_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '8') {
    		$('#text43_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '9') {
    		$('#text44_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '10') {
    		$('#text45_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '11') {
    		$('#text46_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	} else if (monthly == '12') {
    		$('#text47_' + row).val(accounting.formatNumber(total, 2));
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row).val(accounting.formatNumber(0, 2));
    	} else {
    		$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).val(accounting.formatNumber(0, 2));
    	}

    	/*$("#text36_" + row + ",#text37_" + row + ",#text38_" + row + ",#text39_" + row + ",#text40_" + row + ",#text41_" + row + ",#text42_" + row + ",#text43_" + row + ",#text44_" + row + ",#text45_" + row + ",#text46_" + row + ",#text47_" + row).each(function() {
    		if ($(this).val() != '' && $(this).val() != 0.00) {
    			var sebaran_id = $(this).attr('id');
    			$('#' + sebaran_id).val(accounting.formatNumber(total, 2));
    		}
    	});*/

    	$('#text48_' + row).val(accounting.formatNumber(
    		parseInt($('#text36_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text37_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text38_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text39_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text40_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text41_' + row).val().replace(/,/g, '')) +
    		parseInt($('#text42_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text43_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text44_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text45_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text46_' + row).val().replace(/,/g, '')) + 
    		parseInt($('#text47_' + row).val().replace(/,/g, '')), 
    	2));

		if (tipe == 'KHUSUS') {
			$('#text25_' + row).attr('readonly', false);
			$('#text27_' + row).attr('readonly', false);
			$('#text29_' + row).attr('readonly', false);

    		//var default_uang_trans = jlhpeserta * hari_kerja * transport_lain;
    		var default_uang_trans = total_taksi + total_charter + total_air;
    		if (total_transport_lain > default_uang_trans) {
    			alert('Uang transport lain-lain tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_trans, 2));
    			$('#text25_' + row).val('0.00');
    			$('#text25_' + row).focus();
    		}

    		var default_uang_makan = jlhpeserta * uang_makan * kali_uang_makan;
    		if (total_uang_makan > default_uang_makan) {
    			alert('Uang makan tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_makan, 2));
    			$('#text27_' + row).val('0.00');
    			$('#text27_' + row).focus();
    		}

    		var default_uang_saku = jlhpeserta * hari_kerja * uang_saku;
    		if (total_uang_saku > default_uang_saku) {
    			alert('Uang saku tidak boleh lebih dari : ' + accounting.formatNumber(default_uang_saku, 2));
    			$('#text29_' + row).val('0.00');
    			$('#text29_' + row).focus();
    		}
		} else {
			$('#text25_' + row).attr('readonly', true);
			$('#text27_' + row).attr('readonly', true);
			$('#text29_' + row).attr('readonly', true);
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
	//var trxCode = genTransactionCode(budgetperiod, key_find, 'CAPEX');
	
	//left freeze panes
	$("#data tr:eq(" + index + ") input[id^=text00_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text01_]").val("");
	$("#data tr:eq(" + index + ") input[id^=trxrktcode_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text02_]").val(budgetperiod);
	$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text03_]").val(val_cc);
	$("#data tr:eq(" + index + ") input[id^=text04_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text05_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text06_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text07_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text08_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text09_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text10_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text11_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text12_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text13_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text14_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text15_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text16_]").val("");
	//$("#text17_" + index + " option[value='"+row.PLAN+"']").attr("selected", "selected");
	$("#data tr:eq(" + index + ") input[id^=text18_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text18_]").css("text-align", 'right');
	$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text19_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text19_]").css("text-align", 'right');
	$("#data tr:eq(" + index + ") input[id^=text20_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text20_]").css("text-align", 'right');
	$("#data tr:eq(" + index + ") input[id^=text21_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text21_]").css("text-align", 'right');
	$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text30_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text30_]").css("text-align", 'right');
	$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("required");
	$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text33_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
	$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text35_]").val("");
	$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ") input[id^=text48_]").val(accounting.formatNumber(0, 2));
	$("#data tr:eq(" + index + ")").removeAttr("style");
	
	$("#data tr:eq(" + index + ") input[id^=text05_]").focus();
}

function getData(){
    $("#page_num").val(page_num);	
	
	var user_role = "<?=$this->userrole?>";
    //
    $.ajax({
        type    : "post",
        url     : "ho-spd/list",
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
						
						$("#data tr:eq(" + index + ") input[id^=btn00_]").val("");
						$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
						
						//mewarnai jika row nya berasal dari temporary table
						if (row.FLAG_TEMP) {cekTempData(index);}
						
						$("#data tr:eq(" + index + ") input[id^=text00_]").val(row.ROW_ID);
						$("#data tr:eq(" + index + ") input[id^=text01_]").val(row.MY_ROWNUM);
						$("#data tr:eq(" + index + ") input[id^=text02_]").val(row.PERIOD_BUDGET);
						$("#data tr:eq(" + index + ") input[id^=text03_]").val(row.HCC_CC + " - " + row.HCC_COST_CENTER);
						$("#data tr:eq(" + index + ") input[id^=text04_]").val(row.RK_ID);
						$("#data tr:eq(" + index + ") input[id^=text05_]").val(row.RK_NAME);
						$("#data tr:eq(" + index + ") input[id^=text05_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text06_]").val(row.SPD_DESCRIPTION);
						$("#data tr:eq(" + index + ") input[id^=text06_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text07_]").val(row.COA_CODE);
						$("#data tr:eq(" + index + ") input[id^=text07_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text08_]").val(row.COA_NAME);
						$("#data tr:eq(" + index + ") input[id^=text08_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text09_]").val(row.NORMA_SPD_ID);
						$("#data tr:eq(" + index + ") input[id^=text10_]").val(row.RUTE);
						$("#data tr:eq(" + index + ") input[id^=text10_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text51_]").val(row.PLANE_N_PRICE);
						$("#data tr:eq(" + index + ") input[id^=text52_]").val(row.PLANE_P_PRICE);
						$("#data tr:eq(" + index + ") input[id^=text53_]").val(row.TAXI_QTY);
						$("#data tr:eq(" + index + ") input[id^=text54_]").val(row.TAXI_N_PRICE);
						$("#data tr:eq(" + index + ") input[id^=text55_]").val(row.CHARTER_QTY);
						$("#data tr:eq(" + index + ") input[id^=text56_]").val(row.CHARTER_N_PRICE);
						$("#data tr:eq(" + index + ") input[id^=text57_]").val(row.WATER_VEH_QTY);
						$("#data tr:eq(" + index + ") input[id^=text58_]").val(row.WATER_VEH_N_PRICE);
						$("#data tr:eq(" + index + ") input[id^=text11_]").val(row.CORE_CODE);
						$("#data tr:eq(" + index + ") input[id^=text12_]").val(row.CORE_CODE);
						$("#data tr:eq(" + index + ") input[id^=text12_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text13_]").val(row.COMP_CODE);
						$("#data tr:eq(" + index + ") input[id^=text14_]").val(row.COMPANY_NAME);
						$("#data tr:eq(" + index + ") input[id^=text14_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text15_]").val(row.BA_CODE);
						$("#data tr:eq(" + index + ") input[id^=text16_]").val(row.BA_NAME);
						//$("#data tr:eq(" + index + ") input[id^=text17_]").val(row.PLAN);
						$("#text17_" + index + " option[value='"+row.PLAN+"']").attr("selected", "selected");
						$("#data tr:eq(" + index + ") input[id^=text18_]").val(row.GOLONGAN);
						$("#data tr:eq(" + index + ") input[id^=text18_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text18_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text19_]").val(row.JLH_PRIA);
						$("#data tr:eq(" + index + ") input[id^=text19_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text20_]").val(row.JLH_WANITA);
						$("#data tr:eq(" + index + ") input[id^=text20_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text21_]").val(row.JLH_HARI);
						$("#data tr:eq(" + index + ") input[id^=text21_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text21_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text22_]").val(accounting.formatNumber(row.TIKET, 2));
						$("#data tr:eq(" + index + ") input[id^=text23_]").val(accounting.formatNumber(row.TIKET, 2));
						$("#data tr:eq(" + index + ") input[id^=text24_]").val(accounting.formatNumber(row.TRANSPORT_LAIN, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").val(accounting.formatNumber(row.TRANSPORT_LAIN, 2));
						$("#data tr:eq(" + index + ") input[id^=text25_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text26_]").val(accounting.formatNumber(row.UANG_MAKAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text27_]").val(accounting.formatNumber(row.UANG_MAKAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text28_]").val(accounting.formatNumber(row.UANG_SAKU, 2));
						$("#data tr:eq(" + index + ") input[id^=text29_]").val(accounting.formatNumber(row.UANG_SAKU, 2));
						$("#data tr:eq(" + index + ") input[id^=text30_]").val(row.HOTEL_JLH_HARI);
						$("#data tr:eq(" + index + ") input[id^=text30_]").css("text-align", 'right');
						$("#data tr:eq(" + index + ") input[id^=text30_]").addClass("required");
						$("#data tr:eq(" + index + ") input[id^=text31_]").val(accounting.formatNumber(row.HOTEL_JLH_TARIF, 2));
						$("#data tr:eq(" + index + ") input[id^=text32_]").val(accounting.formatNumber(row.HOTEL_JLH_TARIF, 2));
						$("#data tr:eq(" + index + ") input[id^=text33_]").val(row.OTHERS);
						$("#data tr:eq(" + index + ") input[id^=text33_]").addClass("number");
						$("#data tr:eq(" + index + ") input[id^=text34_]").val(accounting.formatNumber(row.TOTAL, 2));
						$("#data tr:eq(" + index + ") input[id^=text35_]").val(row.REMARKS_OTHERS);
						$("#data tr:eq(" + index + ") input[id^=text36_]").val(accounting.formatNumber(row.SEBARAN_JAN, 2));
						$("#data tr:eq(" + index + ") input[id^=text37_]").val(accounting.formatNumber(row.SEBARAN_FEB, 2));
						$("#data tr:eq(" + index + ") input[id^=text38_]").val(accounting.formatNumber(row.SEBARAN_MAR, 2));
						$("#data tr:eq(" + index + ") input[id^=text39_]").val(accounting.formatNumber(row.SEBARAN_APR, 2));
						$("#data tr:eq(" + index + ") input[id^=text40_]").val(accounting.formatNumber(row.SEBARAN_MAY, 2));
						$("#data tr:eq(" + index + ") input[id^=text41_]").val(accounting.formatNumber(row.SEBARAN_JUN, 2));
						$("#data tr:eq(" + index + ") input[id^=text42_]").val(accounting.formatNumber(row.SEBARAN_JUL, 2));
						$("#data tr:eq(" + index + ") input[id^=text43_]").val(accounting.formatNumber(row.SEBARAN_AUG, 2));
						$("#data tr:eq(" + index + ") input[id^=text44_]").val(accounting.formatNumber(row.SEBARAN_SEP, 2));
						$("#data tr:eq(" + index + ") input[id^=text45_]").val(accounting.formatNumber(row.SEBARAN_OCT, 2));
						$("#data tr:eq(" + index + ") input[id^=text46_]").val(accounting.formatNumber(row.SEBARAN_NOV, 2));
						$("#data tr:eq(" + index + ") input[id^=text47_]").val(accounting.formatNumber(row.SEBARAN_DEC, 2));
						$("#data tr:eq(" + index + ") input[id^=text48_]").val(accounting.formatNumber(row.SEBARAN_TOTAL, 2));
						$("#text50_" + index + " option[value='"+row.TIPE_NORMA+"']").attr("selected", "selected");

						if (row.TIPE_NORMA == 'KHUSUS') {
							$("#data tr:eq(" + index + ") input[id^=text25_]").attr('readonly', false);
							$("#data tr:eq(" + index + ") input[id^=text27_]").attr('readonly', false);
							$("#data tr:eq(" + index + ") input[id^=text29_]").attr('readonly', false);
						}

						$("#data tr:eq(" + index + ") input[id^=text22_]").attr("name", "text22["+index+"]");
						$("#data tr:eq(" + index + ") input[id^=tChange_]").val("");
						$("#data tr:eq(" + index + ")").removeAttr("style");
						
						$("#data tr:eq(1) input[id^=text05_]").focus();
					});
				} else {
					alert("Belum Ada Data");
					$("#btn_add").trigger("click");
				}
			}
        }
    });
}
</script>
