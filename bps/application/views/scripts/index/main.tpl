<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Menampilkan List RKT dan Jumlah Data yang telah diinput
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	09/07/2013
Update Terakhir		:	09/07/2013
Revisi				:	
=========================================================================================================================
*/
?>

<style type="text/css">
	.col1, .col2, .col3, .col4, .col5, .col6, .col7, .col8, .col9, .col10 {
		width: 170px;
		height: 20px;
		padding: 0px 5px;
		border: 1px solid #000;
		border-radius: 5px;
		cursor: pointer;
		font-weight: bolder;
		color: #333;
	}
	.col1{
		background: #C4FC7E;
	}
	.col2{
		background: #FFF89C;
	}
	.col3{
		background: #8bdcf3;
	}
	.col4{
		background: #d3b7ea;
	}
	.col5{
		background: #facfeb;
	}
	.col6{
		background: #ffd2d9;
	}
	.col7{
		background: #ffb2bd;
	}
	.col8{
		background: #6be5d1;
	}
	.col9{
		background: #5dcef0;
	}
	.col10{
		background: #5dcef0;
	}
	.col1:hover, .col2:hover, .col3:hover, .col4:hover, .col5:hover, .col6:hover, .col7:hover, .col8:hover, .col9:hover, .col10:hover {
		background: #f3f3f3;
	}
	a{
		text-decoration: none;
	}
	#accordion{
		background-image: none;
	}
</style>

<script type="text/javascript">
$(document).ready(function() {
	var countLoginFailed = <?php echo $this->countLoginFailed; ?>;
	if (countLoginFailed > 0){
		alert(countLoginFailed + " Kali Percobaan Login Gagal.");
	}
});
</script>
<div style='float:left; width:600px; margin-right:10px;'>
	<!--<fieldset>
		<legend>AKTIVITAS</legend>
		<div id="menu" style='font-family: verdana; font-size: 9px;'>
		
		<h3 style='padding:5px 10px;'>NORMA</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody>
						<tr>
							<td width='170px;' align='center'><a href="norma-checkroll/main"><input type='button' class='col1' value='RKT CHECKROLL MPP'></a></td>
							<td width='170px;' align='center'><a href="norma-alat-kerja-panen/main"><input type='button' class='col2' value='ALAT KERJA PANEN'></a></td>
							<td width='170px;' align='center'>
							<!--<a href="norma-alat-kerja-non-panen/main"><input type='button' class='col1' value='ALAT KERJA NON PANEN'></a>-->
	<!--						<a href="rkt-vra/main"><input type='button' class='col1' value='RKT VRA'></a>
							</td>
						</tr>
						<tr>
							<td width='170px;' align='center'><a href="norma-distribusi-vra-non-infra/main"'><input type='button' class='col1' value='RKT DISTRIBUSI VRA'></a></td>
							<td width='170px;' align='center'><a href="norma-distribusi-vra/main"'><input type='button' class='col2' value='RKT DISTRIBUSI VRA - INFRA'></a></td>
							<td width='170px;' align='center'><a href="norma-wra/main"><input type='button' class='col1' value='RKT WRA'></a></td>
						</tr>
						<tr>
							<td width='170px;' align='center'></td>
							<td width='170px;' align='center'>&nbsp;</td>
							<td width='170px;' align='center'>&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>LAND CLEARING</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_lc' id='data_aktivitas_lc'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>
			
		<h3 style='padding:5px 10px;'>TANAM</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_tanam' id='data_aktivitas_tanam'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>RAWAT</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_rawat' id='data_aktivitas_rawat'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>RAWAT DENGAN OPSI</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_rawat_opsi' id='data_aktivitas_rawat_opsi'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'>&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>HAMA PENYAKIT</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_hama_penyakit' id='data_aktivitas_hama_penyakit'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>	

		<h3 style='padding:5px 10px;'>SENSUS</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_sensus' id='data_aktivitas_sensus'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>INFRASTRUKTUR</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody name='data_aktivitas_infrastruktur' id='data_aktivitas_infrastruktur'>
						<tr style="display:none">
							<td width='170px;' align='center' id='col1'></td>
							<td width='170px;' align='center' id='col2'></td>
							<td width='170px;' align='center' id='col3'></td>
						</tr>
					</tbody>
				</table>
			</div>

		<h3 style='padding:5px 10px;'>OTHER</h3>
			<div style='height:75px'>
				<table border="0" cellpadding="1" cellspacing="5" align='center'>
					<tbody>
						<tr>
							<td width='170px;' align='center'><a href="rkt-capex/main"><input type='button' class='col1' value='CAPEX'></a></td>
							<td width='170px;' align='center'><a href="rkt-opex/main"><input type='button' class='col2' value='OPEX'></a></td>
							<td width='170px;' align='center'><a href="rkt-csr/main"><input type='button' class='col1' value='CSR'></a></td>
						</tr>
						<tr>
							<td width='170px;' align='center'><a href="rkt-she/main"><input type='button' class='col1' value='SUSTAINABILITY'></a></td>
							<td width='170px;' align='center'><a href="rkt-internal-relation/main"><input type='button' class='col2' value='INTERNAL RELATION'></a></td>
							<td width='170px;' align='center'><a href="rkt-panen/main"><input type='button' class='col1' value='PANEN'></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</fieldset>
	-->
	<input type="hidden" name="controller" id="controller" value=""/>
</div>

<div style='width:390px; float:left;'>
	<?PHP
	// jika domain : bps.tap-agri.com, jangan muncul tombol upload/download data ke/dari HO
	/*if (($_SERVER['SERVER_NAME'] <> 'bps.tap-agri.com') && ($this->referenceRole == 'BA_CODE')) {
	?>
	<fieldset style="margin-bottom:15px;">
		<legend>SINKRONISASI DATA</legend>
		<input type="button" name="btn_upload" id="btn_upload" value="UPLOAD DATA KE HO" class="button" style="width:368px; height:40px; background:#f58282; color:white; margin-bottom:3px;" /><br>
		<input type="button" name="btn_download" id="btn_download" value="DOWNLOAD DATA DARI HO" class="button" style="width:368px; height:40px; background:#31b573; color:white;" />
	</fieldset>
	<?PHP } */?>
	<fieldset>
		<legend>SEKILAS INFO</legend>
		<div id="accordion" style='font-family: verdana;'>
			<h3 style='padding:5px 10px;'>JADWAL</h3>
			<div>
				<table border="0" cellpadding="4" cellspacing="0" align='center'>
					<tr>
						<td width='30%' align='center' valign='top'>09/2018 – W1</td>
						<td width='70%'>Review Data Tahap I di Kantor Pusat</td>
					</tr>
					<tr>
						<td align='center' valign='top'>09/2018 – W3</td>
						<td>Rekomendasi Pemupukan dari R&D</td>
					</tr>
					<tr>
						<td align='center' valign='top'>09/2018 – W4</td>
						<td>Upload HS, Produksi dan Norma Budget ke BPS</td>
					</tr>
					<tr>
						<td align='center' valign='top'>10/2018 – W1</td>
						<td>Input BPS</td>
					</tr>
					<tr>
						<td align='center' valign='top'>10/2018 – W4</td>
						<td>Review Budget Tahap 2</td>
					</tr>
					<tr>
						<td align='center' valign='top'>11/2018 - W1</td>
						<td>Konsolidasi data PL, Balance Sheet, CF Target dan CAT</td>
					</tr>
					<tr>
						<td align='center' valign='top'>11/2018 - W2</td>
						<td>Finalisasi data CAT</td>
					</tr>
				</table>
			</div>
		
			<h3 style='padding:5px 10px;'>HUBUNGI KAMI</h3>
			<div>
				<p>
					Apabila Ada Kendala Dalam "Budgeting & Planning System", Dapat Menghubungi :
				</p>
				<div style='text-align:center;font-size:13px;font-weight:bolder;'>
					IT HELPDESK<br>
					<a href='http://helpdesk.tap-agri.com' style='color:blue;'>http://helpdesk.tap-agri.com</a>
				</div>
				<p style='text-align:center;font-weight:bolder;'>
					Ticket Category : "Budgeting & Planning System".
				</p>
			</div>		
		</div>
	</fieldset>
</div>

<?php
echo $this->partial('popup.tpl', array('width'  => 650,
                                       'height' => 300)); ?>
<script type="text/javascript">
$(document).ready(function() {
	$( "#accordion" ).accordion({
		heightStyle: "content"
	});
	$( "#menu" ).accordion({
		heightStyle: "content"
	});
	listAktivitas('lc');
	listAktivitas('tanam');
	listAktivitas('rawat');
	listAktivitas('rawat_opsi');
	listAktivitas('hama_penyakit');
	listAktivitas('sensus');
	listAktivitas('infrastruktur');
	
	$("#btn_upload").click( function() {
		$.ajax({
			type     : "post",
			url      : "sync-upload-data/all",
			success  : function(data) {
				alert(data);
			}
		});
    });
	$("#btn_download").click( function() {
		$.ajax({
			type     : "post",
			url      : "sync-download-data/all",
			success  : function(data) {
				alert(data);
			}
		});
    });
});

function listAktivitas(tipe_aktivitas) {
	var kolom = parseInt(1);
    $.ajax({
        type    : "post",
        url     : "index/list-aktivitas",
		data	: { TIPE_AKTIVITAS : tipe_aktivitas },
        cache   : false,
        dataType: "json",
        success : function(data) {
            count = data.count;
			if (count > 0) {
                $.each(data.rows, function(key, row) {
                    if(kolom == 1){
						var tr = $("#data_aktivitas_"+tipe_aktivitas+" tr:eq(0)").clone();
						$("#data_aktivitas_"+tipe_aktivitas).append(tr);
					}
                    var index = ($("#data_aktivitas_"+tipe_aktivitas+" tr").length - 1);					
					$("#data_aktivitas_"+tipe_aktivitas+" tr:eq(" + index + ")").find("input, select").each(function() {
						$(this).attr("id", $(this).attr("id") + index);
					});
					
					if(kolom == 1){
						$("#data_aktivitas_"+tipe_aktivitas+" tr:eq(" + index + ") td[id^=col1]").html("<a href="+row.LINK_RKT+"/activitycode/"+row.ACTIVITY_CODE+"><input type='button' class='col1' value='"+row.ACTIVITY_DESC+"'></a>");
					}else if(kolom == 2){
						$("#data_aktivitas_"+tipe_aktivitas+" tr:eq(" + index + ") td[id^=col2]").html("<a href="+row.LINK_RKT+"/activitycode/"+row.ACTIVITY_CODE+"><input type='button' class='col2' value='"+row.ACTIVITY_DESC+"'></a>");
					}else if(kolom == 3){
						$("#data_aktivitas_"+tipe_aktivitas+" tr:eq(" + index + ") td[id^=col3]").html("<a href="+row.LINK_RKT+"/activitycode/"+row.ACTIVITY_CODE+"><input type='button' class='col1' value='"+row.ACTIVITY_DESC+"'></a>");
						kolom = 0;
					}
					
					kolom++;
					$("#data_aktivitas_"+tipe_aktivitas+" tr:eq(" + index + ")").removeAttr("style");
                });
            }
        }
    });
}
</script>