<?php
/*
=========================================================================================================================
Project				: 	Estate Budget Preparation System
Versi				: 	1.0.0
Deskripsi			: 	View untuk Upload Master File
Function 			:	
Disusun Oleh		: 	IT Support Application - PT Triputra Agro Persada	
Developer			: 	Sabrina Ingrid Davita
Dibuat Tanggal		: 	11/04/2012
Update Terakhir		:	11/04/2012
Revisi				:	
=========================================================================================================================
*/
$this->headScript()->appendFile('js/global.js');?>
<form name="upload" id="upload" enctype="multipart/form-data" method='POST' action=''>
<div>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="buttons">
        <tr>
            <td width="50%">&nbsp;</td>
            <td width="50%" align="right">
                <input type="submit" name="btn_save" id="btn_save" value="SIMPAN" class="button" onclick='return validateInput(this);'/>
                <input type="button" name="btn_cancel" id="btn_cancel" value="BATAL" class="button" />
            </td>
        </tr>
    </table>
</div>
<br/>
<div>
    <table border="0" width="100%" cellpadding="0" cellspacing="0" id="main">
        <tr>
            <td>
				<label for="file">PILIH FILE :</label>
				<input type="file" name="file" id="file" size="50" class="required" >
			</td>
        </tr>
    </table>
</div>
<input type="hidden" name="controller" id="controller" value=""/>
<input type="hidden" name="ba_code" id="ba_code" value=""/>
</form>

<script type="text/javascript">
var url = location.href.toString().split("/");
var mod = url[url.length-1];
$(document).ready(function() {
    init();
	$("#btn_save").show();
    $("#btn_cancel").click(function() {
        self.close();
    });
});
function init() {
  $("#controller").val(opener.$("#controller").val());
	$("#ba_code").val(opener.$("#key_find").val());
	$("#upload").attr("action", $("#controller").val());
}

function validateInput(elem) {
	if ($('#file').val() == "" || $('#file').val() == "-1") {
		alert($('#file').attr("id").toUpperCase() + " belum diisi.");
		$('#file').addClass("empty");
		$('#file').focus();
		return false;
	}
	
	var ext = $('#file').val().split('.').pop().toLowerCase();
	if($.inArray(ext, ['csv']) == -1) {
		alert('File harus dalam format CSV.');
		return false;
	}
	return true;
}
</script>
